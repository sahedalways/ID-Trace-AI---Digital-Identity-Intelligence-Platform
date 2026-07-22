<?php
/**
 * File: admin-dashboard.php
 * Admin control panel dashboard with sidebar layout, tables, and charts.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login");
    exit;
}

$adminId = $_SESSION['admin_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM `admins` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin || $admin['status'] !== 'active') {
        session_destroy();
        header("Location: admin-login");
        exit;
    }

    $totalAffiliates = (int)$pdo->query("SELECT COUNT(*) FROM `affiliates`")->fetchColumn();
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();

    // Recent 5 affiliates
    $recentAffiliates = $pdo->query("SELECT * FROM `affiliates` ORDER BY `created_at` DESC LIMIT 5")->fetchAll();

    // Recent 5 customers
    $recentCustomers = $pdo->query("SELECT u.*, a.name as aff_name, a.aid FROM `users` u LEFT JOIN (SELECT uid, MAX(affid) as affid FROM `conversions` GROUP BY uid) c ON c.uid = u.id LEFT JOIN `affiliates` a ON c.affid = a.id ORDER BY u.created_at DESC LIMIT 5")->fetchAll();

    // Chart data: last 7 months revenue
    $chartRevenue = $pdo->query("
        SELECT DATE_FORMAT(`created_at`, '%Y-%m') as month, SUM(`price`) as revenue, SUM(`payout`) as payout 
        FROM `conversions` 
        WHERE `created_at` >= DATE_SUB(NOW(), INTERVAL 7 MONTH) 
        GROUP BY month ORDER BY month ASC
    ")->fetchAll();

    // Chart data: last 7 months clicks vs conversions
    $chartClicks = $pdo->query("
        SELECT DATE_FORMAT(`created_at`, '%Y-%m') as month, COUNT(*) as clicks 
        FROM `clicks` 
        WHERE `created_at` >= DATE_SUB(NOW(), INTERVAL 7 MONTH) 
        GROUP BY month ORDER BY month ASC
    ")->fetchAll();

    $chartConversions = $pdo->query("
        SELECT DATE_FORMAT(`created_at`, '%Y-%m') as month, COUNT(*) as conversions 
        FROM `conversions` 
        WHERE `created_at` >= DATE_SUB(NOW(), INTERVAL 7 MONTH) 
        GROUP BY month ORDER BY month ASC
    ")->fetchAll();

    // Chart data: top 5 affiliates by conversions
    $chartTopAffiliates = $pdo->query("
        SELECT MAX(a.name) as name, COUNT(c.id) as conversions, SUM(c.price) as revenue
        FROM `conversions` c
        JOIN `affiliates` a ON c.affid = a.id
        GROUP BY c.affid
        ORDER BY conversions DESC
        LIMIT 5
    ")->fetchAll();

    // Merge chart data
    $months = [];
    foreach ($chartRevenue as $r) $months[$r['month']] = ['revenue' => (float)$r['revenue'], 'payout' => (float)$r['payout'], 'clicks' => 0, 'conversions' => 0];
    foreach ($chartClicks as $c) { if (!isset($months[$c['month']])) $months[$c['month']] = ['revenue'=>0,'payout'=>0,'clicks'=>0,'conversions'=>0]; $months[$c['month']]['clicks'] = (int)$c['clicks']; }
    foreach ($chartConversions as $c) { if (!isset($months[$c['month']])) $months[$c['month']] = ['revenue'=>0,'payout'=>0,'clicks'=>0,'conversions'=>0]; $months[$c['month']]['conversions'] = (int)$c['conversions']; }
    ksort($months);

    $chartLabels = array_keys($months);
    $chartRevenueData = array_map(fn($m) => $months[$m]['revenue'], $chartLabels);
    $chartPayoutData = array_map(fn($m) => $months[$m]['payout'], $chartLabels);
    $chartClicksData = array_map(fn($m) => $months[$m]['clicks'], $chartLabels);
    $chartConvData = array_map(fn($m) => $months[$m]['conversions'], $chartLabels);

} catch (PDOException $e) {
    error_log("Admin Dashboard Error: " . $e->getMessage());
    $errDetail = "Error: " . $e->getMessage();
    if (str_contains($e->getMessage(), 'admins')) $errDetail .= " | Possible fix: Create the admins table via admins.sql";
    if (str_contains($e->getMessage(), 'Table') && str_contains($e->getMessage(), 'doesn')) $errDetail .= " | Table not found on server";
    if (str_contains($e->getMessage(), 'Column')) $errDetail .= " | Column mismatch — check database schema";
    if (str_contains($e->getMessage(), 'group_by') || str_contains($e->getMessage(), 'only_full_group_by')) $errDetail .= " | SQL group_by mode conflict";
    die($errDetail);
}

$success_msg = "";
$error_msg = "";
if (isset($_SESSION['flash_success'])) {
    $success_msg = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $error_msg = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard — ID Trace AI</title>
    <?php include 'admin-head.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Dashboard Overview</h1>
                    <p class="text-xs text-gray-400">Platform-wide affiliate metrics, customers, and revenue stats.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-indigo-50 border border-indigo-100 text-indigo-700 px-2.5 py-1 rounded-md uppercase tracking-wider">
                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div> <?= number_format($totalAffiliates) ?> Affiliates
                    </span>
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold bg-blue-50 border border-blue-100 text-blue-700 px-2.5 py-1 rounded-md uppercase tracking-wider">
                        <?= number_format($totalUsers) ?> Users
                    </span>
                </div>
            </div>

            <!-- Date Filter -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Filter:</span>
                <button onclick="filterStats('all_time')" data-filter="all_time" class="filter-btn active bg-gray-900 text-white text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">All Time</button>
                <button onclick="filterStats('today')" data-filter="today" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">Today</button>
                <button onclick="filterStats('yesterday')" data-filter="yesterday" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">Yesterday</button>
                <button onclick="filterStats('last_7_days')" data-filter="last_7_days" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">Last 7 Days</button>
                <button onclick="filterStats('this_month')" data-filter="this_month" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">This Month</button>
                <button onclick="filterStats('last_month')" data-filter="last_month" class="filter-btn bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">Last Month</button>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left min-h-[110px]">
                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Clicks</span>
                        <div class="w-7 h-7 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center text-sm"><i class="fa-solid fa-mouse-pointer"></i></div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono stat-clicks">0</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left min-h-[110px]">
                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Converted Sales</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                    <div class="text-2xl font-black text-emerald-600 tracking-tight font-mono stat-conversions">0</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left min-h-[110px]">
                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Revenue / Payout</span>
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm"><i class="fa-solid fa-dollar-sign"></i></div>
                    </div>
                    <div class="text-xl font-black text-indigo-600 tracking-tight font-mono stat-revenue">$0.00</div>
                    <div class="text-[11px] font-bold text-gray-500 font-mono">Payout: <span class="stat-payout text-gray-900">$0.00</span></div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left min-h-[110px]">
                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Chargebacks</span>
                        <div class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-sm"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                    <div class="text-2xl font-black text-red-600 tracking-tight font-mono stat-chargebacks">0</div>
                    <div class="text-[11px] font-bold text-gray-500 font-mono">Amount: <span class="stat-chargeback-amount text-red-600">$0.00</span></div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left min-h-[110px]">
                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Withdrawn</span>
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm"><i class="fa-solid fa-money-bill-wave"></i></div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 tracking-tight font-mono stat-withdrawn">$0.00</div>
                </div>
            </div>

            <!-- 3 Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Revenue & Payout Chart -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-chart-area text-indigo-600"></i> Revenue & Payout
                    </h3>
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
                <!-- Clicks vs Conversions Chart -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-chart-bar text-emerald-600"></i> Clicks vs Conversions
                    </h3>
                    <canvas id="clicksConvChart" height="200"></canvas>
                </div>
                <!-- Top Affiliates Chart -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                        <i class="fa-solid fa-trophy text-amber-600"></i> Top Affiliates
                    </h3>
                    <canvas id="topAffChart" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Affiliates + Recent Customers -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Recent Affiliates -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-handshake text-indigo-600"></i> Recent Affiliates
                        </h3>
                        <a href="admin-affiliates" class="text-[11px] font-bold text-indigo-600 hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="border-b border-gray-100">
                                <tr>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-3">Name</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-3">Email</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-3">Status</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if (empty($recentAffiliates)): ?>
                                    <tr><td colspan="4" class="text-xs text-gray-400 py-4 text-center font-semibold">No affiliates yet.</td></tr>
                                <?php else: foreach ($recentAffiliates as $a): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="py-2.5 pr-3 text-xs font-bold text-gray-900"><?= htmlspecialchars($a['name']) ?></td>
                                        <td class="py-2.5 pr-3 text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($a['email']) ?></td>
                                        <td class="py-2.5 pr-3">
                                            <?php if ($a['status'] === 'active'): ?>
                                                <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Active</span>
                                            <?php elseif ($a['status'] === 'pending'): ?>
                                                <span class="inline-flex items-center text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-md">Pending</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md">Banned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-2.5 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Customers -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-users text-blue-600"></i> Recent Customers
                        </h3>
                        <a href="admin-clients" class="text-[11px] font-bold text-indigo-600 hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="border-b border-gray-100">
                                <tr>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-3">Name</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-3">Email</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2 pr-3">Plan</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider py-2">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if (empty($recentCustomers)): ?>
                                    <tr><td colspan="4" class="text-xs text-gray-400 py-4 text-center font-semibold">No customers yet.</td></tr>
                                <?php else: foreach ($recentCustomers as $c): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="py-2.5 pr-3 text-xs font-bold text-gray-900"><?= htmlspecialchars($c['name'] ?? 'N/A') ?></td>
                                        <td class="py-2.5 pr-3 text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($c['email']) ?></td>
                                        <td class="py-2.5 pr-3 text-[10px] font-semibold text-gray-700"><?= htmlspecialchars($c['plan'] ?? '—') ?></td>
                                        <td class="py-2.5 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script>
    // === Filter Stats ===
    function filterStats(filter) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-gray-900', 'text-white');
            btn.classList.add('bg-white', 'border', 'border-gray-200', 'text-gray-600');
        });
        document.querySelector(`[data-filter="${filter}"]`).classList.remove('bg-white', 'border', 'border-gray-200', 'text-gray-600');
        document.querySelector(`[data-filter="${filter}"]`).classList.add('bg-gray-900', 'text-white');

        document.querySelectorAll('.stat-clicks, .stat-conversions, .stat-revenue, .stat-payout, .stat-chargebacks, .stat-chargeback-amount, .stat-withdrawn').forEach(el => el.style.opacity = '0.4');

        fetch(`admin-dashboard-stats.php?filter=${encodeURIComponent(filter)}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.stat-clicks').textContent = data.clicks;
                    document.querySelector('.stat-conversions').textContent = data.conversions;
                    document.querySelector('.stat-revenue').textContent = '$' + data.revenue;
                    document.querySelector('.stat-payout').textContent = '$' + data.payout;
                    document.querySelector('.stat-chargebacks').textContent = data.chargebacks;
                    document.querySelector('.stat-chargeback-amount').textContent = '$' + data.chargeback_amount;
                    document.querySelector('.stat-withdrawn').textContent = '$' + data.withdrawn;
                }
            })
            .catch(err => console.error('Filter error:', err))
            .finally(() => {
                document.querySelectorAll('.stat-clicks, .stat-conversions, .stat-revenue, .stat-payout, .stat-chargebacks, .stat-chargeback-amount, .stat-withdrawn').forEach(el => {
                    el.style.opacity = '1';
                    el.style.transition = 'opacity 0.3s ease';
                });
            });
    }
    filterStats('all_time');

    // === Charts ===
    const labels = <?= json_encode($chartLabels) ?>;
    const revenueData = <?= json_encode($chartRevenueData) ?>;
    const payoutData = <?= json_encode($chartPayoutData) ?>;
    const clicksData = <?= json_encode($chartClicksData) ?>;
    const convData = <?= json_encode($chartConvData) ?>;
    const topAffLabels = <?= json_encode(array_column($chartTopAffiliates, 'name')) ?>;
    const topAffConv = <?= json_encode(array_map('intval', array_column($chartTopAffiliates, 'conversions'))) ?>;
    const topAffRevenue = <?= json_encode(array_map('floatval', array_column($chartTopAffiliates, 'revenue'))) ?>;

    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.font.size = 11;

    // Revenue & Payout Area Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: revenueData,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#6366f1',
                },
                {
                    label: 'Payout',
                    data: payoutData,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245,158,11,0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#f59e0b',
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
        }
    });

    // Clicks vs Conversions Bar Chart
    new Chart(document.getElementById('clicksConvChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Clicks',
                    data: clicksData,
                    backgroundColor: 'rgba(6,182,212,0.7)',
                    borderRadius: 6,
                    barPercentage: 0.6,
                },
                {
                    label: 'Conversions',
                    data: convData,
                    backgroundColor: 'rgba(16,185,129,0.7)',
                    borderRadius: 6,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
        }
    });

    // Top Affiliates Horizontal Bar Chart
    new Chart(document.getElementById('topAffChart'), {
        type: 'bar',
        data: {
            labels: topAffLabels,
            datasets: [
                {
                    label: 'Conversions',
                    data: topAffConv,
                    backgroundColor: 'rgba(99,102,241,0.7)',
                    borderRadius: 6,
                    barPercentage: 0.6,
                },
                {
                    label: 'Revenue ($)',
                    data: topAffRevenue,
                    backgroundColor: 'rgba(245,158,11,0.7)',
                    borderRadius: 6,
                    barPercentage: 0.6,
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 } } },
            scales: { x: { beginAtZero: true, grid: { color: '#f1f5f9' } }, y: { grid: { display: false } } }
        }
    });
    </script>

    <?php
    $alert_type = $success_msg ? 'success' : ($error_msg ? 'error' : '');
    $alert_message = $success_msg ?: $error_msg;
    ?>
    <?php include 'alert-modal.php'; ?>

</body>
</html>