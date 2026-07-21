<?php
/**
 * File: admin-client-detail.php
 * Admin individual customer detail page with login-as-user capability.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

$clientId = (int)($_GET['id'] ?? 0);
if (!$clientId) {
    header("Location: admin-clients.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$clientId]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        header("Location: admin-clients.php");
        exit;
    }

    // Get affiliate info
    $affStmt = $pdo->prepare("
        SELECT a.name as aff_name, a.email as aff_email, a.aid 
        FROM `conversions` c 
        LEFT JOIN `affiliates` a ON c.affid = a.id 
        WHERE c.uid = ? 
        ORDER BY c.id DESC LIMIT 1
    ");
    $affStmt->execute([$clientId]);
    $affiliate = $affStmt->fetch(PDO::FETCH_ASSOC);

    // Get all conversions
    $convStmt = $pdo->prepare("SELECT * FROM `conversions` WHERE `uid` = ? ORDER BY `created_at` DESC");
    $convStmt->execute([$clientId]);
    $conversions = $convStmt->fetchAll();

    // Get all transactions
    $txnStmt = $pdo->prepare("SELECT * FROM `transactions` WHERE `uid` = ? ORDER BY `created_at` DESC");
    $txnStmt->execute([$clientId]);
    $transactions = $txnStmt->fetchAll();

    // Get reports
    $reportStmt = $pdo->prepare("SELECT * FROM `reports` WHERE `uid` = ? ORDER BY `created_at` DESC");
    $reportStmt->execute([$clientId]);
    $reports = $reportStmt->fetchAll();

} catch (PDOException $e) {
    error_log("Admin Client Detail Error: " . $e->getMessage());
    die("An error occurred.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customer Details — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6">

            <div class="flex items-center gap-3">
                <a href="admin-clients.php" class="text-gray-400 hover:text-gray-900 transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Customer #<?= $client['id'] ?></h1>
                    <p class="text-xs text-gray-400">Full account details and transaction history.</p>
                </div>
            </div>

            <!-- Customer Info Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start gap-5">
                    <?php if ($client['avatar']): ?>
                        <img src="<?= htmlspecialchars($client['avatar']) ?>" alt="Avatar" class="w-16 h-16 rounded-full border-2 border-gray-200 shadow-sm object-cover">
                    <?php else: ?>
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-xl font-black shadow-md">
                            <?= strtoupper(substr($client['name'] ?? 'U', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Name</div>
                            <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($client['name'] ?? 'N/A') ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Email</div>
                            <div class="text-xs font-mono text-gray-700"><?= htmlspecialchars($client['email']) ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Status</div>
                            <div class="text-xs font-bold <?= $client['status'] === 'active' ? 'text-emerald-600' : 'text-red-600' ?>"><?= ucfirst($client['status']) ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Joined</div>
                            <div class="text-xs font-semibold text-gray-700"><?= date('M d, Y', strtotime($client['created_at'])) ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Plan</div>
                            <div class="text-xs font-bold text-indigo-600"><?= htmlspecialchars($client['plan'] ?? 'None') ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Country</div>
                            <div class="text-xs font-semibold text-gray-700"><?= htmlspecialchars($client['country'] ?? 'N/A') ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Credits</div>
                            <div class="text-xs font-bold text-gray-900"><?= (int)$client['credit'] ?></div>
                        </div>
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase">Subscription</div>
                            <?php if (!empty($client['stripe_subscription_id'])): ?>
                                <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Active</span>
                            <?php else: ?>
                                <span class="inline-flex items-center text-[10px] font-bold bg-gray-50 border border-gray-100 text-gray-600 px-2 py-0.5 rounded-md">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($affiliate): ?>
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">Referred by:</span>
                    <span class="text-xs font-bold text-indigo-600"><?= htmlspecialchars($affiliate['aff_name']) ?></span>
                    <span class="text-[10px] text-gray-400 font-mono">(<?= htmlspecialchars($affiliate['aid']) ?>)</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Transactions -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-1.5">
                    <i class="fa-solid fa-credit-card text-indigo-600"></i> Transactions (<?= count($transactions) ?>)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b border-gray-100">
                            <tr>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">ID</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">Plan</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">Status</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($transactions)): ?>
                                <tr><td colspan="4" class="text-xs text-gray-400 py-4 text-center">No transactions.</td></tr>
                            <?php else: foreach ($transactions as $t): ?>
                                <tr>
                                    <td class="py-2.5 pr-4 text-xs font-mono text-gray-500">#<?= $t['id'] ?></td>
                                    <td class="py-2.5 pr-4 text-xs font-semibold text-gray-700"><?= htmlspecialchars($t['plan']) ?></td>
                                    <td class="py-2.5 pr-4">
                                        <?php if ($t['status'] === 'succeeded'): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Succeeded</span>
                                        <?php elseif ($t['dispute_status'] == 1): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md">Chargeback</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-gray-50 border border-gray-100 text-gray-600 px-2 py-0.5 rounded-md"><?= htmlspecialchars($t['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2.5 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y H:i', strtotime($t['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Conversions -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Conversions (<?= count($conversions) ?>)
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
                                    <td class="py-2.5 pr-4 text-xs font-semibold text-gray-700 font-mono">$<?= number_format($c['payout'], 2) ?></td>
                                    <td class="py-2.5 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y H:i', strtotime($c['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Reports -->
            <?php if (!empty($reports)): ?>
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-1.5">
                    <i class="fa-solid fa-file-lines text-amber-600"></i> Reports (<?= count($reports) ?>)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="border-b border-gray-100">
                            <tr>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">VID</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-4">Status</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($reports as $r): ?>
                                <tr>
                                    <td class="py-2.5 pr-4 text-xs font-mono text-gray-500"><?= htmlspecialchars($r['vid']) ?></td>
                                    <td class="py-2.5 pr-4">
                                        <span class="inline-flex items-center text-[10px] font-bold bg-<?= $r['status'] === 'completed' ? 'emerald' : 'amber' ?>-50 border border-<?= $r['status'] === 'completed' ? 'emerald' : 'amber' ?>-100 text-<?= $r['status'] === 'completed' ? 'emerald' : 'amber' ?>-700 px-2 py-0.5 rounded-md"><?= ucfirst($r['status']) ?></span>
                                    </td>
                                    <td class="py-2.5 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y H:i', strtotime($r['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        </main>
    </div>

</body>
</html>
