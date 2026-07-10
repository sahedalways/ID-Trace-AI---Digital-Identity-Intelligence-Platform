<?php
/**
 * OSINT Universal Intelligence Console — Corporate Billing Statement Terminal
 * File: generate_invoice.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Gateway: Deny access if user context drops mid-flight
if (!isset($_SESSION['user_logged_in']) || !isset($_SESSION['user_id'])) {
    die("Access Denied.");
}

$user_id = (int)$_SESSION['user_id'];
$tid = $_GET['tid'] ?? '';

if (empty($tid)) {
    die("Transaction identifier missing.");
}

// Securely join users table row to pull checkout_email directly and prevent runtime null notice
$stmt = $pdo->prepare("SELECT t.*, p.price as plan_cost, p.credit, u.email as checkout_email
                       FROM `transactions` t
                       LEFT JOIN `plans` p ON t.plan = p.name
                       LEFT JOIN `users` u ON t.uid = u.id
                       WHERE t.tid = ? AND t.uid = ? LIMIT 1");
$stmt->execute([$tid, $user_id]);
$tx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tx) {
    die("Invoice record could not be found or verified.");
}

$checkout_email = $tx['checkout_email'] ?? '';
$invoice_date = date('F d, Y', strtotime($tx['created_at']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice — <?php echo htmlspecialchars($tx['tid']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f9fafb !important; color: #111827 !important; min-height: auto !important; }
        @media print {
            @page { margin: 1cm; size: A4 portrait; }
            body { background: #ffffff !important; color: #000000 !important; min-height: auto !important; }
            .no-print { display: none !important; }
            .print-border-none { border: none !important; padding: 0 !important; margin: 0 !important; }
            /* FIXED: Force background colors to render exactly as shown during printing layout cycles */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased selection:bg-[#128c7e] selection:text-white">

    <!-- TOP FLOATING INTERACTION WIDGET LAYER -->
    <div class="no-print max-w-3xl w-full mx-auto px-4 mt-6">
        <div class="bg-slate-900 rounded-2xl p-4 flex flex-row items-center justify-between gap-4 shadow-md border border-slate-800">
            <div class="flex items-center gap-3 text-left">
                <span class="text-lg">🕵️‍♂️</span>
                <div>
                    <h4 class="text-xs font-bold text-white tracking-wide">Document Statement Preview</h4>
                    <p class="text-[11px] text-slate-400 font-medium">Verified invoice ready. Click the utility controller to prompt system preservation hooks.</p>
                </div>
            </div>
            <button onclick="window.print();" class="bg-[#128c7e] hover:bg-[#0e6f64] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition whitespace-nowrap tracking-wide flex items-center gap-2">
                Save / Print
            </button>
        </div>
    </div>

    <!-- CORE INVOICE STRIPE-MINIMAL CANVAS -->
    <main class="max-w-3xl w-full mx-auto bg-white p-8 sm:p-14 my-6 print-border-none border border-gray-200/80 rounded-3xl shadow-sm text-left">

        <!-- SECTION 1: TOP LEFT BRANDING LOGO & METADATA ROWS WITH LARGE PAID LABEL -->
        <div class="flex justify-between items-start gap-4">
            <div class="space-y-4">
                <div class="flex items-center gap-2 select-none">
                    <span class="text-2xl">🕵️‍♂️</span>
                    <h1 class="text-xl font-medium tracking-tight text-gray-700">Identity Search <span class="bg-[#128c7e] text-white text-sm font-black px-2 py-0.5 rounded inline-flex items-center justify-center tracking-widest mt-0.5">AI</span></h1>
                </div>

                <div class="text-xs space-y-1 pt-2 font-medium text-gray-700 leading-normal">
                    <p><span class="text-gray-400 font-bold uppercase text-[10px] inline-block w-28 tracking-wide">Invoice number</span> : <span class="font-mono font-bold ml-1"><?php echo htmlspecialchars($tx['tid']); ?></span></p>
                    <p><span class="text-gray-400 font-bold uppercase text-[10px] inline-block w-28 tracking-wide">Date paid</span> : <span class="font-semibold ml-1"><?php echo $invoice_date; ?></span></p>
                </div>
            </div>

            <!-- FIXED: Large, pure-text paid label badge explicitly mapped for print sheets -->
            <div class="pt-1 select-none">
                <span class="inline-block bg-[#128c7e] text-white text-sm font-black px-7 py-2.5 rounded-xl shadow-sm uppercase tracking-widest border border-transparent">
                    Paid
                </span>
            </div>
        </div>

        <!-- SECTION 2: SIDE-BY-SIDE COMPANY ADDRESS AND CUSTOMER PROFILE -->
        <div class="grid grid-cols-2 gap-8 pt-8 mt-2 text-xs font-medium text-gray-500 leading-relaxed">
            <!-- Left Side: Identity Search Corporate Contact Profiles -->
            <div>
                <p class="font-bold text-gray-900 text-sm mb-1">Identity Search AI</p>
                <p>30 North Gould Street</p>
                <p>Sheridan, Wyoming 82801</p>
                <p>United States</p>
                <p class="text-gray-500 font-medium mt-0.5">support@identitysearch.ai</p>
            </div>

            <!-- Right Side: Clean "Bill To" Address Block Mapping from Transactions Table Columns -->
            <div class="text-left">
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Bill to</h3>
                <strong class="text-gray-900 text-sm font-black block mb-0.5"><?php echo htmlspecialchars($tx['cardholder_name'] ?? 'N/A'); ?></strong>
                <p><?php echo htmlspecialchars($tx['street'] ?? '—'); ?></p>
                <p><?php echo htmlspecialchars($tx['zip'] ?? ''); ?><?php echo !empty($tx['zip']) ? ', ' : ''; ?><span class="uppercase font-normal text-gray-500"><?php echo htmlspecialchars($tx['country'] ?? '—'); ?></span></p>
                <p class="font-medium text-gray-500 mt-0.5"><?php echo htmlspecialchars($checkout_email); ?></p>
            </div>
        </div>

        <!-- SECTION 3: LINE ITEM BREAKDOWN MATRIX -->
        <div class="mt-8">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 text-[11px] font-bold uppercase text-gray-400 tracking-wider">
                        <th class="py-3 pr-4 font-bold">Description</th>
                        <th class="py-3 px-4 text-center w-16 font-bold">Qty</th>
                        <th class="py-3 px-4 text-right w-28 font-bold">Unit price</th>
                        <th class="py-3 pl-4 text-right w-24 font-bold">Amount</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-medium text-gray-700 divide-y divide-gray-100">
                    <tr>
                        <td class="py-5 pr-4 leading-relaxed">
                            <span class="font-bold text-gray-900 text-sm block"><?php echo strtoupper(htmlspecialchars($tx['plan'])); ?> Subscription Pack</span>
                        </td>
                        <td class="py-5 px-4 text-center text-gray-900 font-bold">1</td>
                        <td class="py-5 px-4 text-right text-gray-600 font-semibold">$<?php echo number_format($tx['plan_cost'], 2); ?></td>
                        <td class="py-5 pl-4 text-right font-bold text-gray-900">$<?php echo number_format($tx['plan_cost'], 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- SECTION 4: CLOSELY ATTACHED SUMMARY LEDGER BLOCKS -->
        <div class="mt-4 border-t border-gray-100 pt-5 flex justify-end">
            <div class="w-64 space-y-3 text-xs">
                <div class="flex justify-between font-medium text-gray-500">
                    <span>Subtotal</span>
                    <span class="text-gray-900 font-semibold">$<?php echo number_format($tx['plan_cost'], 2); ?></span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 pt-3 border-t border-gray-200 text-sm">
                    <span>Total Paid</span>
                    <span class="text-[#128c7e] font-black">$<?php echo number_format($tx['plan_cost'], 2); ?> USD</span>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
