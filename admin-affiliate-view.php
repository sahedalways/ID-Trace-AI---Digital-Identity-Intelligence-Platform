<?php
/**
 * File: admin-affiliate-view.php
 * Admin read-only affiliate detail view with stats, conversions, withdrawals, and payment info.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

$affId = (int)($_GET['id'] ?? 0);
if (!$affId) {
    header("Location: admin-affiliates.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM `affiliates` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$affId]);
    $affiliate = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$affiliate) {
        header("Location: admin-affiliates.php");
        exit;
    }

    $payStmt = $pdo->prepare("SELECT * FROM `affiliate_payments` WHERE `affiliate_id` = ? LIMIT 1");
    $payStmt->execute([$affId]);
    $payment = $payStmt->fetch(PDO::FETCH_ASSOC);

    $convStmt = $pdo->prepare("SELECT * FROM `conversions` WHERE `affid` = ? ORDER BY `created_at` DESC LIMIT 50");
    $convStmt->execute([$affId]);
    $conversions = $convStmt->fetchAll();

    $convCountStmt = $pdo->prepare("SELECT COUNT(*) FROM `conversions` WHERE `affid` = ?");
    $convCountStmt->execute([$affId]);
    $totalConversions = (int)$convCountStmt->fetchColumn();

    $revStmt = $pdo->prepare("SELECT COUNT(*) as cnt, COALESCE(SUM(`price`),0) as revenue, COALESCE(SUM(`payout`),0) as payout FROM `conversions` WHERE `affid` = ?");
    $revStmt->execute([$affId]);
    $revenue = $revStmt->fetch(PDO::FETCH_ASSOC);

    $withdrawStmt = $pdo->prepare("SELECT * FROM `withdraw` WHERE `affid` = ? ORDER BY `created_at` DESC LIMIT 20");
    $withdrawStmt->execute([$affId]);
    $withdrawals = $withdrawStmt->fetchAll();

    $clickStmt = $pdo->prepare("SELECT COUNT(*) FROM `clicks` WHERE `affid` = ?");
    $clickStmt->execute([$affId]);
    $totalClicks = (int)$clickStmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Admin Affiliate View Error: " . $e->getMessage());
    die("Error loading affiliate details.");
}

$methodLabels = [
    'payoneer' => 'Payoneer',
    'usdt_bep20' => 'USDT (BEP-20)',
    'bank_transfer' => 'Bank Transfer'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Affiliate #<?= str_pad($affiliate['id'], 3, '0', STR_PAD_LEFT) ?> — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6">

            <div class="flex items-center gap-3">
                <a href="admin-affiliates.php?tab=<?= $tab ?? 'all' ?>" class="text-gray-400 hover:text-gray-900 transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div class="flex-1">
                    <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Affiliate #<?= str_pad($affiliate['id'], 3, '0', STR_PAD_LEFT) ?></h1>
                    <p class="text-xs text-gray-400">Full profile, payment info, and activity history.</p>
                </div>
                <a href="admin-login-as-affiliate.php?id=<?= $affiliate['id'] ?>" class="inline-flex items-center gap-1.5 text-[11px] font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition">
                    <i class="fa-solid fa-right-to-bracket text-[10px]"></i> Login as Affiliate
                </a>
                <a href="admin-affiliate-edit.php?id=<?= $affiliate['id'] ?>" class="inline-flex items-center gap-1.5 text-[11px] font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 px-3 py-1.5 rounded-lg transition">
                    <i class="fa-solid fa-pen text-[10px]"></i> Edit
                </a>
            </div>

            <!-- Profile Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start gap-5">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-xl font-black shadow-md">
                        <?= strtoupper(substr($affiliate['name'], 0, 1)) ?>
                    </div>
                    <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Name</div>
                            <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($affiliate['name']) ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Email</div>
                            <div class="text-xs font-mono text-gray-700"><?= htmlspecialchars($affiliate['email']) ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Affiliate ID</div>
                            <div class="text-xs font-mono font-bold text-indigo-600"><?= htmlspecialchars($affiliate['aid']) ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Status</div>
                            <?php if ($affiliate['status'] === 'active'): ?>
                                <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md"><i class="fa-solid fa-circle-check text-[8px] mr-0.5"></i> Active</span>
                            <?php elseif ($affiliate['status'] === 'pending'): ?>
                                <span class="inline-flex items-center text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-md"><i class="fa-solid fa-clock text-[8px] mr-0.5"></i> Pending</span>
                            <?php else: ?>
                                <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md"><i class="fa-solid fa-ban text-[8px] mr-0.5"></i> Banned</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Mobile</div>
                            <div class="text-xs font-semibold text-gray-700"><?= htmlspecialchars($affiliate['mobile'] ?? 'N/A') ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Country</div>
                            <div class="text-xs font-semibold text-gray-700"><?= htmlspecialchars($affiliate['country'] ?? 'N/A') ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Joined</div>
                            <div class="text-xs font-semibold text-gray-700"><?= date('M d, Y', strtotime($affiliate['created_at'])) ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Balance</div>
                            <div class="text-xs font-bold text-gray-900 font-mono">$<?= number_format($affiliate['balance'], 2) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Row -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm text-center">
                    <div class="text-lg font-extrabold text-gray-900"><?= number_format($totalClicks) ?></div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase mt-1">Clicks</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm text-center">
                    <div class="text-lg font-extrabold text-gray-900"><?= number_format($totalConversions) ?></div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase mt-1">Conversions</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm text-center">
                    <div class="text-lg font-extrabold text-indigo-600">$<?= number_format($revenue['revenue'], 2) ?></div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase mt-1">Revenue</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm text-center">
                    <div class="text-lg font-extrabold text-emerald-600">$<?= number_format($revenue['payout'], 2) ?></div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase mt-1">Total Payout</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm text-center">
                    <div class="text-lg font-extrabold text-gray-900">$<?= number_format($affiliate['withdraw'], 2) ?></div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase mt-1">Withdrawn</div>
                </div>
            </div>

            <!-- Payment Info -->
            <?php if ($payment): ?>
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-1.5">
                    <i class="fa-solid fa-wallet text-emerald-600"></i> Payment Profile
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase">Method</div>
                        <div class="text-xs font-bold text-gray-900"><?= $methodLabels[$payment['payment_method']] ?? htmlspecialchars($payment['payment_method']) ?></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase">Details</div>
                        <div class="text-xs font-mono text-gray-700 break-all"><?= htmlspecialchars($payment['payment_info']) ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Withdrawals -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-1.5">
                    <i class="fa-solid fa-money-bill-transfer text-amber-600"></i> Withdrawals (<?= count($withdrawals) ?>)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b border-gray-100">
                            <tr>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">ID</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">Amount</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">Method</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">Status</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($withdrawals)): ?>
                                <tr><td colspan="5" class="text-xs text-gray-400 py-4 text-center">No withdrawals.</td></tr>
                            <?php else: foreach ($withdrawals as $w): ?>
                                <tr>
                                    <td class="py-2.5 pr-4 text-xs font-mono text-gray-500">#<?= $w['id'] ?></td>
                                    <td class="py-2.5 pr-4 text-xs font-bold text-gray-900 font-mono">$<?= number_format($w['amount'], 2) ?></td>
                                    <td class="py-2.5 pr-4 text-xs font-semibold text-gray-700"><?= $methodLabels[$w['payment_method']] ?? htmlspecialchars($w['payment_method']) ?></td>
                                    <td class="py-2.5 pr-4">
                                        <?php if ($w['status'] === 'approved'): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Approved</span>
                                        <?php elseif ($w['status'] === 'rejected'): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md">Rejected</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-md">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2.5 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y', strtotime($w['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Conversions -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-indigo-600"></i> Conversions (<?= number_format($totalConversions) ?>)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b border-gray-100">
                            <tr>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">ID</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">Plan</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">Price</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">Payout</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($conversions)): ?>
                                <tr><td colspan="5" class="text-xs text-gray-400 py-4 text-center">No conversions.</td></tr>
                            <?php else: foreach ($conversions as $c): ?>
                                <tr>
                                    <td class="py-2.5 pr-4 text-xs font-mono text-gray-500">#<?= $c['id'] ?></td>
                                    <td class="py-2.5 pr-4 text-xs font-semibold text-gray-700"><?= htmlspecialchars($c['plan']) ?></td>
                                    <td class="py-2.5 pr-4 text-xs font-bold text-indigo-600 font-mono">$<?= number_format($c['price'], 2) ?></td>
                                    <td class="py-2.5 pr-4 text-xs font-bold text-emerald-600 font-mono">$<?= number_format($c['payout'], 2) ?></td>
                                    <td class="py-2.5 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>
