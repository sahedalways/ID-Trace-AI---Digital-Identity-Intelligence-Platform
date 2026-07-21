<?php
/**
 * File: admin-clients.php
 * Admin customers — paginated table with search + subscription filters.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$subFilter = isset($_GET['sub']) ? $_GET['sub'] : 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(u.email LIKE ? OR u.name LIKE ? OR u.id = ? OR a.aid LIKE ? OR a.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = $search;
    $params[] = "%$search%";
    $params[] = "%$search%";
}

switch ($subFilter) {
    case 'active_sub':
        $conditions[] = "u.stripe_subscription_id IS NOT NULL AND u.stripe_subscription_id != ''";
        break;
    case 'no_sub':
        $conditions[] = "(u.plan IS NULL OR u.plan = '')";
        break;
    case 'cancelled':
        $conditions[] = "u.plan IS NOT NULL AND u.plan != '' AND (u.stripe_subscription_id IS NULL OR u.stripe_subscription_id = '')";
        break;
    case 'chargeback':
        $conditions[] = "t.dispute_status = 1";
        break;
}

$whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

try {
    // Count
    $countSql = "SELECT COUNT(DISTINCT u.id) FROM `users` u
        LEFT JOIN (SELECT uid, MAX(affid) as affid FROM `conversions` WHERE affid IS NOT NULL GROUP BY uid) c ON c.uid = u.id
        LEFT JOIN (SELECT id, aid, email FROM `affiliates`) a ON c.affid = a.id
        LEFT JOIN `transactions` t ON t.uid = u.id $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRows = (int)$countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalRows / $perPage));

    $sql = "
        SELECT u.*,
               a.name as aff_name, a.email as aff_email, a.aid,
               t.dispute_status, t.dispute_amount
        FROM `users` u
        LEFT JOIN (
            SELECT uid, MAX(affid) as affid
            FROM `conversions` WHERE affid IS NOT NULL
            GROUP BY uid
        ) c ON c.uid = u.id
        LEFT JOIN (
            SELECT id, name, email, aid FROM `affiliates`
        ) a ON c.affid = a.id
        LEFT JOIN (
            SELECT uid, MAX(CASE WHEN dispute_status = 1 THEN 1 ELSE 0 END) as dispute_status,
                   MAX(COALESCE(dispute_amount, 0)) as dispute_amount
            FROM `transactions`
            GROUP BY uid
        ) t ON t.uid = u.id
        $whereClause
        ORDER BY u.created_at DESC
        LIMIT $perPage OFFSET $offset
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clients = $stmt->fetchAll();

    // Summary counts
    $totalActive = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE stripe_subscription_id IS NOT NULL AND stripe_subscription_id != ''")->fetchColumn();
    $totalNoSub = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE plan IS NULL OR plan = ''")->fetchColumn();
    $totalCancelled = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE plan IS NOT NULL AND plan != '' AND (stripe_subscription_id IS NULL OR stripe_subscription_id = '')")->fetchColumn();
    $totalChargeback = (int)$pdo->query("SELECT COUNT(DISTINCT uid) FROM `transactions` WHERE dispute_status = 1")->fetchColumn();
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();

} catch (PDOException $e) {
    error_log("Admin Clients Error: " . $e->getMessage());
    die("Error: " . $e->getMessage());
}

function buildClientQs($overrides) {
    $q = array_merge($_GET, $overrides);
    return http_build_query($q);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customers — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6">

            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Customer Management</h1>
                <p class="text-xs text-gray-400">View all customers acquired through affiliates.</p>
            </div>

            <!-- Search + Status Filter Row -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <form method="GET" class="flex items-center gap-2 flex-1 max-w-lg">
                    <input type="hidden" name="sub" value="<?= htmlspecialchars($subFilter) ?>">
                    <div class="flex-1 relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by email, name, user ID, affiliate ID or affiliate email..."
                            class="w-full text-sm pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 px-5 rounded-xl transition-all cursor-pointer">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="admin-clients.php?sub=<?= $subFilter ?>" class="text-xs font-bold text-gray-500 hover:text-gray-900 px-2">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="flex flex-wrap items-center gap-2 lg:ml-auto">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Status:</span>
                    <?php foreach (['all'=>'All','active_sub'=>'Active Sub','no_sub'=>'No Sub','cancelled'=>'Cancelled','chargeback'=>'Chargeback'] as $key => $label): ?>
                        <?php $cnt = ($key === 'all') ? $totalUsers : (($key === 'active_sub') ? $totalActive : (($key === 'no_sub') ? $totalNoSub : (($key === 'cancelled') ? $totalCancelled : $totalChargeback))); ?>
                        <a href="?q=<?= urlencode($search) ?>&sub=<?= $key ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg transition-all <?= $subFilter === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"><?= $label ?> (<?= number_format($cnt) ?>)</a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="text-[11px] font-bold text-gray-400">Showing <?= number_format($totalRows) ?> customers</div>

            <!-- Clients Table -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">ID</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Customer</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Affiliate</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Plan</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Subscription</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Joined</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($clients)): ?>
                                <tr><td colspan="7" class="text-xs text-gray-400 py-8 text-center font-semibold">No customers found.</td></tr>
                            <?php else: foreach ($clients as $c): ?>
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-3 text-xs font-mono text-gray-500">#<?= $c['id'] ?></td>
                                    <td class="px-5 py-3">
                                        <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($c['name'] ?? 'N/A') ?></div>
                                        <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($c['email']) ?></div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <?php if ($c['aff_name']): ?>
                                            <div class="text-xs font-semibold text-gray-700"><?= htmlspecialchars($c['aff_name']) ?></div>
                                            <div class="text-[10px] text-indigo-600 font-mono"><?= htmlspecialchars($c['aid'] ?? '') ?></div>
                                        <?php else: ?>
                                            <span class="text-[10px] text-gray-400 font-semibold">Direct</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-xs font-semibold text-gray-700"><?= htmlspecialchars($c['plan'] ?? '—') ?></td>
                                    <td class="px-5 py-3">
                                        <?php if ($c['dispute_status'] == 1): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md">Chargeback</span>
                                        <?php elseif (!empty($c['stripe_subscription_id'])): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Active</span>
                                        <?php elseif (!empty($c['plan'])): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-md">No Sub</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-gray-50 border border-gray-100 text-gray-600 px-2 py-0.5 rounded-md">Free</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                                    <td class="px-5 py-3">
                                        <a href="admin-client-detail.php?id=<?= $c['id'] ?>" class="text-[10px] font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-2.5 py-1 rounded-md transition inline-flex items-center gap-1">
                                            <i class="fa-solid fa-eye text-[9px]"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                    <div class="text-[11px] font-semibold text-gray-400">Page <?= $page ?> of <?= number_format($totalPages) ?></div>
                    <div class="flex items-center gap-1.5">
                        <?php if ($page > 1): ?>
                            <a href="?<?= buildClientQs(['page' => 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">First</a>
                            <a href="?<?= buildClientQs(['page' => $page - 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Prev</a>
                        <?php endif; ?>
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?<?= buildClientQs(['page' => $i]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg transition <?= $i === $page ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= buildClientQs(['page' => $page + 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Next</a>
                            <a href="?<?= buildClientQs(['page' => $totalPages]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Last</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

        </main>
    </div>

</body>
</html>
