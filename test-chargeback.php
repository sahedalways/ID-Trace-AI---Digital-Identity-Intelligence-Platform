<?php
/**
 * File: test-chargeback.php
 * Dev-only tool: Simulate chargeback on a transaction for local testing.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['affiliate_id']) && !isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$message = '';
$transactions = [];

try {
    // Fetch recent transactions with their status
    $tx_stmt = $pdo->prepare("SELECT `id`, `tid`, `uid`, `plan`, `status`, `dispute_status`, `dispute_reason`, `created_at` FROM `transactions` ORDER BY `created_at` DESC LIMIT 50");
    $tx_stmt->execute();
    $transactions = $tx_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle chargeback trigger
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tx_id'])) {
        $tx_id = (int)$_POST['tx_id'];

        $pdo->beginTransaction();

        // 1. Mark transaction as chargeback
        $pdo->prepare("UPDATE `transactions` SET `status` = 'chargeback', `dispute_status` = 1, `dispute_reason` = 'test_chargeback' WHERE `id` = ?")->execute([$tx_id]);

        // 2. Get transaction details for user deactivation
        $tx_detail = $pdo->prepare("SELECT `uid` FROM `transactions` WHERE `id` = ? LIMIT 1");
        $tx_detail->execute([$tx_id]);
        $tx_row = $tx_detail->fetch(PDO::FETCH_ASSOC);

        if ($tx_row) {
            $pdo->prepare("UPDATE `users` SET `status` = 'inactive', `stripe_subscription_id` = NULL, `plan` = NULL, `credit` = 0, `validity` = NULL WHERE `id` = ?")->execute([$tx_row['uid']]);
        }

        $pdo->commit();
        $message = "Chargeback triggered successfully for transaction ID: " . $tx_id;

        // Refresh list
        $tx_stmt->execute();
        $transactions = $tx_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $message = "Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Test Chargeback — Dev Tool</title>
    <?php include 'head.php'; ?>
</head>
<body class="bg-slate-50 min-h-screen">

    <div class="max-w-4xl mx-auto px-4 py-10 space-y-6">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h1 class="text-lg font-black text-gray-900 mb-1">Test Chargeback Trigger</h1>
            <p class="text-xs text-gray-500 font-semibold">Dev-only tool. Select a transaction to simulate chargeback.</p>

            <?php if ($message): ?>
                <div class="mt-4 p-3 rounded-xl text-sm font-semibold <?= strpos($message, 'Error') !== false ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-slate-50/40">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">TXID</th>
                            <th class="px-4 py-3">User ID</th>
                            <th class="px-4 py-3">Plan</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Dispute</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium text-slate-700">
                        <?php foreach ($transactions as $tx): ?>
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-mono"><?= $tx['id'] ?></td>
                            <td class="px-4 py-3 font-mono font-bold"><?= htmlspecialchars($tx['tid']) ?></td>
                            <td class="px-4 py-3 font-mono"><?= $tx['uid'] ?></td>
                            <td class="px-4 py-3 font-mono font-bold text-indigo-600"><?= strtoupper($tx['plan']) ?></td>
                            <td class="px-4 py-3">
                                <?php if ($tx['status'] === 'chargeback'): ?>
                                    <span class="inline-flex items-center bg-red-50 text-red-700 font-bold px-2 py-0.5 rounded text-[10px] border border-red-100">Chargeback</span>
                                <?php elseif ($tx['status'] === 'succeeded'): ?>
                                    <span class="inline-flex items-center bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded text-[10px] border border-emerald-100">Succeeded</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center bg-slate-100 text-slate-500 font-bold px-2 py-0.5 rounded text-[10px] border border-slate-200"><?= htmlspecialchars($tx['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?= $tx['dispute_status'] ? '<span class="text-red-600 font-bold">Yes</span>' : '<span class="text-slate-400">No</span>' ?>
                            </td>
                            <td class="px-4 py-3 font-mono text-slate-500"><?= date('Y-m-d H:i', strtotime($tx['created_at'])) ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if ($tx['status'] === 'chargeback'): ?>
                                    <span class="text-slate-400 font-bold text-[10px]">Already Done</span>
                                <?php else: ?>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to simulate chargeback on this transaction?');" class="inline">
                                        <input type="hidden" name="tx_id" value="<?= $tx['id'] ?>">
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-700 font-bold text-[10px] px-3 py-1.5 rounded-lg border border-red-100 transition-all cursor-pointer">
                                            <i class="fa-solid fa-ban mr-1"></i> Chargeback
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
