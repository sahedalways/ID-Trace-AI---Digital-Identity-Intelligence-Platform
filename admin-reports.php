<?php
/**
 * File: admin-reports.php
 * Admin affiliate reports — all affiliates table with pagination + search + date filters.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login");
    exit;
}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all_time';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build WHERE
$where = "";
$params = [];
if (!empty($search)) {
    $where = "WHERE (a.id = ? OR a.email LIKE ? OR a.aid LIKE ? OR a.name LIKE ?)";
    $params = [$search, "%$search%", "%$search%", "%$search%"];
}

// Date filter — separate conditions per table
$clkDate = '';
$convDate = '';
$chargeDate = '';

switch ($filter) {
    case 'today':
        $clkDate = "AND DATE(`created_at`) = CURDATE()";
        $convDate = "AND DATE(`created_at`) = CURDATE()";
        $chargeDate = "AND DATE(t.`created_at`) = CURDATE()";
        break;
    case 'yesterday':
        $clkDate = "AND DATE(`created_at`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        $convDate = "AND DATE(`created_at`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        $chargeDate = "AND DATE(t.`created_at`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        break;
    case 'last_7_days':
        $clkDate = "AND `created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $convDate = "AND `created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $chargeDate = "AND t.`created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        break;
    case 'this_month':
        $clkDate = "AND YEAR(`created_at`) = YEAR(NOW()) AND MONTH(`created_at`) = MONTH(NOW())";
        $convDate = "AND YEAR(`created_at`) = YEAR(NOW()) AND MONTH(`created_at`) = MONTH(NOW())";
        $chargeDate = "AND YEAR(t.`created_at`) = YEAR(NOW()) AND MONTH(t.`created_at`) = MONTH(NOW())";
        break;
    case 'last_month':
        $clkDate = "AND YEAR(`created_at`) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND MONTH(`created_at`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
        $convDate = "AND YEAR(`created_at`) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND MONTH(`created_at`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
        $chargeDate = "AND YEAR(t.`created_at`) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND MONTH(t.`created_at`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
        break;
}

try {
    // Count total
    $countSql = "SELECT COUNT(DISTINCT a.id) FROM `affiliates` a $where";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRows = (int)$countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalRows / $perPage));

    // Fetch affiliates with aggregated stats
    $sql = "
        SELECT a.*,
               COALESCE(clk.total_clicks, 0) as total_clicks,
               COALESCE(conv.total_conversions, 0) as total_conversions,
               COALESCE(conv.total_revenue, 0) as total_revenue,
               COALESCE(conv.total_payout, 0) as total_payout,
               COALESCE(chg.total_chargebacks, 0) as total_chargebacks
        FROM `affiliates` a
        LEFT JOIN (
            SELECT affid, COUNT(*) as total_clicks FROM `clicks` WHERE 1=1 $clkDate GROUP BY affid
        ) clk ON clk.affid = a.id
        LEFT JOIN (
            SELECT affid, COUNT(*) as total_conversions, SUM(`price`) as total_revenue, SUM(`payout`) as total_payout
            FROM `conversions` WHERE 1=1 $convDate GROUP BY affid
        ) conv ON conv.affid = a.id
        LEFT JOIN (
            SELECT c.affid, COUNT(*) as total_chargebacks
            FROM `transactions` t
            JOIN `conversions` c ON c.uid = t.uid
            WHERE t.dispute_status = 1 $chargeDate
            GROUP BY c.affid
        ) chg ON chg.affid = a.id
        $where
        ORDER BY a.created_at DESC
        LIMIT $perPage OFFSET $offset
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $affiliates = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Admin Reports Error: " . $e->getMessage());
    die("Error: " . $e->getMessage());
}

function buildQueryString($overrides) {
    $q = array_merge($_GET, $overrides);
    return http_build_query($q);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Affiliate Reports — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6">

            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Affiliate Reports</h1>
                <p class="text-xs text-gray-400">All affiliate performance metrics with search and filters.</p>
            </div>

            <!-- Search + Date Filter Row -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <form method="GET" class="flex items-center gap-2 flex-1 max-w-lg">
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                    <div class="flex-1 relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by ID, Email, AID, or Name..."
                            class="w-full text-sm pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 px-5 rounded-xl transition-all cursor-pointer">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="admin-reports?filter=<?= $filter ?>" class="text-xs font-bold text-gray-500 hover:text-gray-900 px-2">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="flex flex-wrap items-center gap-2 lg:ml-auto">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Date:</span>
                    <?php foreach (['all_time'=>'All Time','today'=>'Today','yesterday'=>'Yesterday','last_7_days'=>'Last 7 Days','this_month'=>'This Month','last_month'=>'Last Month'] as $key => $label): ?>
                        <a href="?q=<?= urlencode($search) ?>&filter=<?= $key ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg transition-all <?= $filter === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="text-[11px] font-bold text-gray-400">Showing <?= number_format($totalRows) ?> affiliates</div>

            <!-- Reports Table -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">ID</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Affiliate</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3 text-right">Clicks</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3 text-right">Sales</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3 text-right">Revenue</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3 text-right">Payout</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3 text-right">Chargebacks</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($affiliates)): ?>
                                <tr><td colspan="8" class="text-xs text-gray-400 py-8 text-center font-semibold">No affiliates found.</td></tr>
                            <?php else: foreach ($affiliates as $a): ?>
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-3 text-xs font-mono text-gray-500">#<?= $a['id'] ?></td>
                                    <td class="px-5 py-3">
                                        <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($a['name']) ?></div>
                                        <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($a['email']) ?></div>
                                        <div class="text-[10px] text-indigo-600 font-mono"><?= htmlspecialchars($a['aid']) ?></div>
                                    </td>
                                    <td class="px-5 py-3 text-xs font-bold text-gray-900 font-mono text-right"><?= number_format($a['total_clicks']) ?></td>
                                    <td class="px-5 py-3 text-xs font-bold text-emerald-600 font-mono text-right"><?= number_format($a['total_conversions']) ?></td>
                                    <td class="px-5 py-3 text-xs font-bold text-indigo-600 font-mono text-right">$<?= number_format($a['total_revenue'], 2) ?></td>
                                    <td class="px-5 py-3 text-xs font-bold text-gray-900 font-mono text-right">$<?= number_format($a['total_payout'], 2) ?></td>
                                    <td class="px-5 py-3 text-xs font-bold text-red-600 font-mono text-right"><?= number_format($a['total_chargebacks']) ?></td>
                                    <td class="px-5 py-3">
                                        <?php if ($a['status'] === 'active'): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Active</span>
                                        <?php elseif ($a['status'] === 'pending'): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-md">Pending</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md">Banned</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                    <div class="text-[11px] font-semibold text-gray-400">
                        Page <?= $page ?> of <?= number_format($totalPages) ?>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <?php if ($page > 1): ?>
                            <a href="?<?= buildQueryString(['page' => 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">First</a>
                            <a href="?<?= buildQueryString(['page' => $page - 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Prev</a>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <a href="?<?= buildQueryString(['page' => $i]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg transition <?= $i === $page ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= buildQueryString(['page' => $page + 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Next</a>
                            <a href="?<?= buildQueryString(['page' => $totalPages]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Last</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

        </main>
    </div>

</body>
</html>