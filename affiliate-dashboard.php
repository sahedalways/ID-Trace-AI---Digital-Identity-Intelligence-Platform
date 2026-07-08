<?php
/**
 * File: affiliate-dashboard.php
 * Main Analytics Summary Panel Matrix for Activated Affiliate Partners.
 * Displays financial metrics balances, referral status flags, and updated clean go.php tracking links.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Enforce strict authentication wall intercept limits
if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login.php");
    exit;
}

$affiliateId = $_SESSION['affiliate_id'];

try {
    // 2. Fetch fresh, live financial metadata numbers from database
    $stmt = $pdo->prepare("SELECT * FROM `affiliates` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$affiliateId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Safe fallback logout sequence if user entry vanished from core tables
        session_destroy();
        header("Location: affiliate-login.php");
        exit;
    }

    // 3. Dynamically craft affiliate tracking link targeting go.php?id=[aid]
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "https://";
    $referralLink = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "go.php?id=" . $user['aid'];

    // 4. AGGREGATE LIVE NETWORK PERFORMANCE METRICS FROM NEW MATRIX LAYOUT
    // Total Raw Clicks routing through this partner node
    $clicks_stmt = $pdo->prepare("SELECT COUNT(*) FROM `clicks` WHERE `affid` = ?");
    $clicks_stmt->execute([$affiliateId]);
    $totalClicks = (int)$clicks_stmt->fetchColumn();

    // Total Conversions where user has checked out successfully (directly tracking via affid)
    $conv_stmt = $pdo->prepare("SELECT COUNT(*) FROM `conversions` WHERE `affid` = ?");
    $conv_stmt->execute([$affiliateId]);
    $totalConversions = (int)$conv_stmt->fetchColumn();

    // Total Recurring Subscriptions tracked across subsequent billing cycle events
    $rec_stmt = $pdo->prepare("SELECT COUNT(*) FROM `recurring` WHERE `affid` = ?");
    $rec_stmt->execute([$affiliateId]);
    $totalRecurring = (int)$rec_stmt->fetchColumn();

    // Total Chargebacks matching this affiliate ID where dispute_status equals 1 inside transactions table
    $dispute_stmt = $pdo->prepare("
        SELECT COUNT(*) FROM `transactions` 
        WHERE `dispute_status` = 1 
        AND `uid` IN (SELECT `uid` FROM `conversions` WHERE `affid` = ?)
    ");
    $dispute_stmt->execute([$affiliateId]);
    $totalChargebacks = (int)$dispute_stmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Affiliate Dashboard Loading Error: " . $e->getMessage());
    die("An error occurred loading the account panel matrix layers.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Partner Metrics Panel — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="max-w-6xl w-full mx-auto px-4 sm:px-6 pt-8 pb-16 grow space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white border border-gray-200 p-6 rounded-2xl shadow-sm gap-4">
            <div class="text-left space-y-0.5">
                <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Partner Intelligence Console</h1>
                <p class="text-xs text-gray-400">Track conversions, traffic links, and residual revenue metrics live.</p>
            </div>
            
            <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2.5 py-1 rounded-md uppercase tracking-wider">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div> Network Node: <?= htmlspecialchars($user['status']) ?>
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-5">
            
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Available Balance</span>
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm"><i class="fa-solid fa-wallet"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono">$<?= number_format($user['balance'], 2) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Cleared splits ready for payout deployment.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Withdrawn</span>
                    <div class="w-7 h-7 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center text-sm"><i class="fa-solid fa-money-bill-wave"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono">$<?= number_format($user['withdraw'], 2) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Accumulated funds dispatched securely.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Traffic Clicks</span>
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm"><i class="fa-solid fa-mouse-pointer"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono"><?= number_format($totalClicks) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Raw unique inbound visitor referrals.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Converted Sales</span>
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm"><i class="fa-solid fa-circle-check"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-emerald-600 tracking-tight font-mono"><?= number_format($totalConversions) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Successful customer purchases logged.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Recurring Billings</span>
                    <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm"><i class="fa-solid fa-arrows-rotate"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono"><?= number_format($totalRecurring) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Active continuous 50% split plan loops.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[120px]">
                <div class="flex justify-between items-start">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Chargebacks</span>
                    <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-sm"><i class="fa-solid fa-triangle-exclamation"></i></div>
                </div>
                <div>
                    <div class="text-2xl font-black text-red-600 tracking-tight font-mono"><?= number_format($totalChargebacks) ?></div>
                    <p class="text-[10px] text-gray-400 mt-0.5">Disputed sales revoked from balance.</p>
                </div>
            </div>

        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-4">
            <div class="space-y-1">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5"><i class="fa-solid fa-link text-indigo-600 text-base"></i> Your Unique Promotional Link Anchor</h3>
                <p class="text-xs text-gray-400">Deploy this URL pattern into tracking content grids to lock cookie matrices to referred visitors.</p>
            </div>
            
            <div class="flex items-center gap-2 bg-slate-50 border border-gray-150 rounded-xl p-2 pl-3">
                <i class="fa-solid fa-globe text-slate-400 shrink-0 text-sm"></i>
                <input type="text" id="refLinkInput" readonly value="<?= htmlspecialchars($referralLink) ?>" 
                    class="w-full bg-transparent font-mono text-xs font-semibold text-slate-700 outline-none select-all">
                <button onclick="copyTrackingLink()" id="copyBtn" class="bg-gray-900 hover:bg-gray-800 text-white text-[11px] font-bold px-4 py-2 rounded-lg transition-all shrink-0 cursor-pointer flex items-center gap-1">
                    <i class="fa-solid fa-copy text-xs"></i> Copy Link
                </button>
            </div>
        </div>

    </main>

    <script>
        function copyTrackingLink() {
            const copyText = document.getElementById("refLinkInput");
            const copyBtn = document.getElementById("copyBtn");
            
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value);
            
            copyBtn.innerHTML = `<i class="fa-solid fa-check text-xs"></i> Copied!`;
            copyBtn.style.background = "#059669";
            
            setTimeout(() => {
                copyBtn.innerHTML = `<i class="fa-solid fa-copy text-xs"></i> Copy Link`;
                copyBtn.style.background = "#111827";
            }, 2500);
        }
    </script>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        &copy; <?= date('Y'); ?> Identity Trace AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>
