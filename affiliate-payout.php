<?php
/**
 * File: affiliate-payout.php
 * Simplified Affiliate Payout Hub
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login.php");
    exit;
}

$affiliateId = (int)$_SESSION['affiliate_id'];

// 1. Handle Withdrawal Submission Pipeline
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_withdrawal'])) {
    $withdraw_amount = (float)$_POST['withdraw_amount'];
    $payoneer_email  = trim($_POST['payoneer_email']);

    try {
        $aff_stmt = $pdo->prepare("SELECT `balance` FROM `affiliates` WHERE `id` = ? LIMIT 1");
        $aff_stmt->execute([$affiliateId]);
        $current_balance = (float)$aff_stmt->fetchColumn();

        if ($withdraw_amount < 100.00) {
            $_SESSION['payout_msg'] = "Minimum withdrawal amount is $100.00";
        } elseif ($withdraw_amount > $current_balance) {
            $_SESSION['payout_msg'] = "Insufficient balance.";
        } elseif (!filter_var($payoneer_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['payout_msg'] = "Invalid Payoneer email address.";
        } else {
            $pdo->beginTransaction();

            // Deduct balance, update tracking total, and save the latest email to profile
            $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` - ?, `withdraw` = `withdraw` + ?, `payoneer_email` = ? WHERE `id` = ?")
                ->execute([$withdraw_amount, $withdraw_amount, $payoneer_email, $affiliateId]);

            // Updated Log Insertion: Explicitly binds NULL to note for administrative action allocation
            $pdo->prepare("INSERT INTO `withdraw` (`affid`, `status`, `amount`, `payoneer_email`, `note`, `created_at`, `updated_at`) VALUES (?, 'pending', ?, ?, NULL, NOW(), NULL)")
                ->execute([$affiliateId, $withdraw_amount, $payoneer_email]);

            $pdo->commit();
            $_SESSION['payout_msg'] = "Withdrawal request submitted successfully.";
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log("Withdrawal Core Failure: " . $e->getMessage());
        $_SESSION['payout_msg'] = "An error occurred. Please try again.";
    }
    header("Location: affiliate-payout.php");
    exit;
}

$status_msg = $_SESSION['payout_msg'] ?? "";
unset($_SESSION['payout_msg']);

// 2. Fetch current balance metrics and saved default email profiles
$aff_stmt = $pdo->prepare("SELECT `balance`, `payoneer_email` FROM `affiliates` WHERE `id` = ? LIMIT 1");
$aff_stmt->execute([$affiliateId]);
$account = $aff_stmt->fetch(PDO::FETCH_ASSOC);

$balance = (float)($account['balance'] ?? 0.00);
$saved_email = $account['payoneer_email'] ?? '';

// 3. Fetch log history records rows
$history = [];
$hist_stmt = $pdo->prepare("SELECT `created_at`, `status`, `amount`, `payoneer_email`, `note`, `updated_at` FROM `withdraw` WHERE `affid` = ? ORDER BY `created_at` DESC");
$hist_stmt->execute([$affiliateId]);
while ($row = $hist_stmt->fetch(PDO::FETCH_ASSOC)) {
    $history[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Withdraw Dashboard — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="max-w-4xl w-full mx-auto px-4 sm:px-6 pt-8 pb-16 grow space-y-6">
        
        <?php if (!empty($status_msg)): ?>
            <div class="p-4 rounded-xl text-xs font-bold border bg-white shadow-xs text-slate-700 text-left flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-indigo-600 text-sm"></i>
                <?= htmlspecialchars($status_msg) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 text-left">
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Available Balance</span>
                <div class="text-4xl font-black font-mono text-gray-900 mt-1">$<?= number_format($balance, 2) ?></div>
            </div>
            <button onclick="toggleModal(false)" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-3.5 px-6 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-money-bill-transfer text-xs"></i> Withdraw Funds
            </button>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-white text-left flex items-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-gray-400 text-sm"></i>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Withdrawal History</h3>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-white">
                            <th class="px-6 py-3.5">Date</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Amount</th>
                            <th class="px-6 py-3.5">Payoneer Email</th>
                            <th class="px-6 py-3.5">Admin Note</th>
                            <th class="px-6 py-3.5 text-right">Updated At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs text-slate-700 font-mono bg-white">
                        <?php if (empty($history)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 font-normal font-sans">
                                <i class="fa-solid fa-receipt text-2xl block mb-2 text-slate-300"></i> No withdrawals recorded yet.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($history as $h): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 text-slate-500 whitespace-nowrap"><?= $h['created_at'] ?></td>
                                <td class="px-6 py-4 uppercase font-bold whitespace-nowrap">
                                    <?php if ($h['status'] === 'pending'): ?>
                                        <span class="inline-flex items-center gap-1 bg-amber-50 border border-amber-200 text-amber-700 font-bold px-2 py-0.5 rounded text-[10px] font-sans">Pending</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold px-2 py-0.5 rounded text-[10px] font-sans">Paid</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">$<?= number_format($h['amount'], 2) ?></td>
                                <td class="px-6 py-4 text-slate-600 whitespace-nowrap select-all"><?= htmlspecialchars($h['payoneer_email']) ?></td>
                                <td class="px-6 py-4 text-slate-400 font-sans leading-relaxed text-left">
                                    <?= !empty($h['note']) ? htmlspecialchars($h['note']) : '<span class="italic text-gray-300">No message logs logged</span>' ?>
                                </td>
                                <td class="px-6 py-4 text-slate-400 text-right whitespace-nowrap">
                                    <?= !empty($h['updated_at']) ? $h['updated_at'] : '—' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="withdrawModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4" onclick="toggleModal(true)">
        <div class="bg-white rounded-2xl max-w-sm w-full border border-gray-200 shadow-xl p-6 space-y-4 text-left transform transition-all" onclick="event.stopPropagation()">
            <div>
                <h3 class="text-base font-black text-gray-900 tracking-tight flex items-center gap-1.5"><i class="fa-solid fa-wallet text-indigo-600 text-sm"></i> Request Withdrawal</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Available: <span class="font-bold font-mono">$<?= number_format($balance, 2) ?></span> (Min: $100.00)</p>
            </div>

            <form method="POST" action="affiliate-payout.php" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Amount (USD)</label>
                    <input type="number" name="withdraw_amount" required step="0.01" min="100.00" max="<?= $balance ?>" value="<?= number_format(max(100.00, $balance), 2, '.', '') ?>"
                           class="w-full bg-slate-50 border border-gray-200 font-mono text-xs rounded-xl px-4 py-3 text-slate-800 outline-none focus:border-indigo-500 focus:bg-white transition-all shadow-inner">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Payoneer Email Address</label>
                    <input type="email" name="payoneer_email" required value="<?= htmlspecialchars($saved_email) ?>" placeholder="name@domain.com"
                           class="w-full bg-slate-50 border border-gray-200 font-mono text-xs rounded-xl px-4 py-3 text-slate-800 outline-none focus:border-indigo-500 focus:bg-white transition-all shadow-inner">
                </div>

                <div class="pt-2 flex gap-3 text-xs">
                    <button type="button" onclick="toggleModal(true)" class="w-1/3 bg-gray-100 hover:bg-gray-200 text-slate-600 font-bold py-3 rounded-xl transition-all cursor-pointer text-center">Cancel</button>
                    <button type="submit" name="submit_withdrawal" class="w-2/3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition-all shadow-md cursor-pointer text-center flex items-center justify-center gap-1"><i class="fa-solid fa-paper-plane text-[10px]"></i> Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('withdrawModal');
        function toggleModal(shouldHide) {
            modal.classList.toggle('hidden', shouldHide);
        }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') toggleModal(true); });
    </script>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        &copy; <?= date('Y'); ?> Identity Search AI Affiliate Portal. All rights reserved. Developed and Designed by <a href="https://sahedahmed.netlify.app/" target="_blank" class="text-[#128c7e] font-bold">Enostation IT</a>.
    </footer>

</body>
</html>
