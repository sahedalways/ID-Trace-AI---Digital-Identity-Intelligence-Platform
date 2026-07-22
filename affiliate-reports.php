<?php
/**
 * File: affiliate-reports.php (Debug Enabled, Local Time Offset & Pagination Structured)
 * Performance Audit Ledger & Dimensional Reporting Engine for Affiliate Partners.
 * Displays structural tracking arrays cleanly filtered by timeline matrices.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// Display error output streams on-screen for tracking execution anomalies
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Enforce strict authentication limits
if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login");
    exit;
}

$affiliateId = (int)$_SESSION['affiliate_id'];

// 2. Capture and sanitize incoming filter parameters (Default changed to clicks)
$filter_metric = isset($_GET['metric']) ? trim($_GET['metric']) : 'clicks';
$filter_date   = isset($_GET['date_range']) ? trim($_GET['date_range']) : 'all_time';
$custom_start  = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$custom_end    = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// PAGINATION MATRIX RULES SETUP
$limit = 25; // Target records per viewport configuration layer
$page  = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 3. Resolve dynamic SQL date interval boundaries
$date_condition = ""; 
$custom_date_params = [];
switch ($filter_date) {
    case 'today':
        $date_condition = "AND DATE(created_at) = CURRENT_DATE()";
        break;
    case 'yesterday':
        $date_condition = "AND DATE(created_at) = DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)";
        break;
    case 'this_week':
        $date_condition = "AND YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)";
        break;
    case 'this_month':
        $date_condition = "AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        break;
    case 'last_month':
        $date_condition = "AND MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))";
        break;
    case 'custom':
        if (!empty($custom_start) && !empty($custom_end)) {
            $date_condition = "AND DATE(created_at) BETWEEN ? AND ?";
            $custom_date_params = [$custom_start, $custom_end];
        }
        break;
    case 'all_time':
    default:
        $date_condition = "";
        break;
}

// Map the dynamic switch date condition to table-qualified field names for JOINed queries
$tx_date_condition = str_replace('created_at', 't.`created_at`', $date_condition);
$conv_date_condition = str_replace('created_at', 'conv.`created_at`', $date_condition);
$rec_date_condition = str_replace('created_at', 'rec.`created_at`', $date_condition);

$report_data = [];
$total_records = 0;

// 4. METRIC SELECTION DISPATCH ROUTERS WITH PAGINATION ENGINES
if ($filter_metric === 'clicks') {
    // Count exact total pool parameters
    $countQuery = "SELECT COUNT(*) FROM `clicks` WHERE `affid` = ? $date_condition";
    $cStmt = $pdo->prepare($countQuery);
    $cBind = 1;
    $cStmt->bindValue($cBind++, $affiliateId, PDO::PARAM_INT);
    if (!empty($custom_date_params)) {
        $cStmt->bindValue($cBind++, $custom_date_params[0]);
        $cStmt->bindValue($cBind++, $custom_date_params[1]);
    }
    $cStmt->execute();
    $total_records = (int)$cStmt->fetchColumn();

    $query = "SELECT `created_at`, `cid`, `s1`, `s2`, `ip`, `country`, `os`, `browser`, `referrer`, `conversion` 
              FROM `clicks` 
              WHERE `affid` = ? $date_condition 
              ORDER BY `created_at` DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($query);
    $bindIndex = 1;
    $stmt->bindValue($bindIndex++, $affiliateId, PDO::PARAM_INT);
    if (!empty($custom_date_params)) {
        $stmt->bindValue($bindIndex++, $custom_date_params[0]);
        $stmt->bindValue($bindIndex++, $custom_date_params[1]);
    }
    $stmt->bindValue($bindIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $report_data[] = [
            'created_at' => $row['created_at'], // Raw UTC out of database
            'type'       => 'Click',
            'tx_id'      => '—',
            'cid'        => $row['cid'],
            's1'         => $row['s1'] ?? '—',
            's2'         => $row['s2'] ?? '—',
            'converted'  => (int)$row['conversion'] === 1 ? 'Yes' : 'No',
            'col_5'      => $row['ip'],
            'col_6'      => $row['country'] ?? '—',
            'col_7'      => $row['os'] ?? '—',
            'col_8'      => $row['browser'] ?? '—',
            'col_9'      => !empty($row['referrer']) ? $row['referrer'] : '—',
            'is_financial' => false
        ];
    }
} 
elseif ($filter_metric === 'conversions') {
    $countQuery = "SELECT COUNT(*) FROM `conversions` conv WHERE conv.`affid` = ? $conv_date_condition";
    $cStmt = $pdo->prepare($countQuery);
    $cBind = 1;
    $cStmt->bindValue($cBind++, $affiliateId, PDO::PARAM_INT);
    if (!empty($custom_date_params)) {
        $cStmt->bindValue($cBind++, $custom_date_params[0]);
        $cStmt->bindValue($cBind++, $custom_date_params[1]);
    }
    $cStmt->execute();
    $total_records = (int)$cStmt->fetchColumn();

    $query = "SELECT conv.`created_at`, conv.`tid` AS tx_id, conv.`cid`, clk.`s1`, clk.`s2`, clk.`country`, u.`name` AS client_name, conv.`plan`, conv.`payout`, conv.`fire_postback`
              FROM `conversions` conv
              LEFT JOIN `clicks` clk ON conv.`cid` = clk.`cid`
              LEFT JOIN `users` u ON conv.`uid` = u.`id`
              WHERE conv.`affid` = ? $conv_date_condition
              ORDER BY conv.`created_at` DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($query);
    $bindIndex = 1;
    $stmt->bindValue($bindIndex++, $affiliateId, PDO::PARAM_INT);
    if (!empty($custom_date_params)) {
        $stmt->bindValue($bindIndex++, $custom_date_params[0]);
        $stmt->bindValue($bindIndex++, $custom_date_params[1]);
    }
    $stmt->bindValue($bindIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $report_data[] = [
            'created_at'   => $row['created_at'],
            'type'         => 'Conversion',
            'tx_id'        => !empty($row['tx_id']) ? $row['tx_id'] : '—',
            'cid'          => $row['cid'],
            's1'           => $row['s1'] ?? '—',
            's2'           => $row['s2'] ?? '—',
            'col_5'        => $row['country'] ?? '—',
            'col_6'        => !empty($row['client_name']) ? $row['client_name'] : 'Authenticated Customer',
            'col_7'        => strtoupper($row['plan']),
            'payout'       => (float)$row['payout'],
            'pb_fired'     => (int)$row['fire_postback'] === 1 ? 'Yes' : 'No',
            'is_financial' => true,
            'is_negative'  => false
        ];
    }
} 
elseif ($filter_metric === 'recurring') {
    $countQuery = "SELECT COUNT(*) FROM `recurring` rec WHERE rec.`affid` = ? $rec_date_condition";
    $cStmt = $pdo->prepare($countQuery);
    $cBind = 1;
    $cStmt->bindValue($cBind++, $affiliateId, PDO::PARAM_INT);
    if (!empty($custom_date_params)) {
        $cStmt->bindValue($cBind++, $custom_date_params[0]);
        $cStmt->bindValue($cBind++, $custom_date_params[1]);
    }
    $cStmt->execute();
    $total_records = (int)$cStmt->fetchColumn();

    $query = "SELECT rec.`created_at`, rec.`tid` AS tx_id, rec.`cid`, clk.`s1`, clk.`s2`, clk.`country`, u.`name` AS client_name, rec.`plan`, rec.`payout`
              FROM `recurring` rec
              LEFT JOIN `clicks` clk ON rec.`cid` = clk.`cid`
              LEFT JOIN `users` u ON rec.`uid` = u.`id`
              WHERE rec.`affid` = ? $rec_date_condition
              ORDER BY rec.`created_at` DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($query);
    $bindIndex = 1;
    $stmt->bindValue($bindIndex++, $affiliateId, PDO::PARAM_INT);
    if (!empty($custom_date_params)) {
        $stmt->bindValue($bindIndex++, $custom_date_params[0]);
        $stmt->bindValue($bindIndex++, $custom_date_params[1]);
    }
    $stmt->bindValue($bindIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $report_data[] = [
            'created_at'   => $row['created_at'],
            'type'         => 'Recurring Payout',
            'tx_id'        => !empty($row['tx_id']) ? $row['tx_id'] : '—',
            'cid'          => $row['cid'],
            's1'           => $row['s1'] ?? '—',
            's2'           => $row['s2'] ?? '—',
            'col_5'        => $row['country'] ?? '—',
            'col_6'        => !empty($row['client_name']) ? $row['client_name'] : 'Authenticated Customer',
            'col_7'        => strtoupper($row['plan']),
            'payout'       => (float)$row['payout'],
            'is_financial' => true,
            'is_negative'  => false
        ];
    }
}
elseif ($filter_metric === 'chargebacks') {
    $countQuery = "SELECT COUNT(*) 
                   FROM `transactions` t
                   JOIN `clicks` clk ON CONVERT(t.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(clk.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci
                   WHERE clk.`affid` = ? AND t.`dispute_status` = 1 $tx_date_condition";
    $cStmt = $pdo->prepare($countQuery);
    $cBind = 1;
    $cStmt->bindValue($cBind++, $affiliateId, PDO::PARAM_INT);
    if (!empty($custom_date_params)) {
        $cStmt->bindValue($cBind++, $custom_date_params[0]);
        $cStmt->bindValue($cBind++, $custom_date_params[1]);
    }
    $cStmt->execute();
    $total_records = (int)$cStmt->fetchColumn();

    $query = "SELECT t.`created_at`, t.`tid` AS tx_id, t.`cid`, clk.`s1`, clk.`s2`, t.`country`, u.`name` AS client_name, t.`plan`, p.`price`, t.`dispute_reason`
              FROM `transactions` t
              JOIN `clicks` clk ON CONVERT(t.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(clk.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci
              LEFT JOIN `users` u ON t.`uid` = u.`id`
              LEFT JOIN `plans` p ON t.`plan` = p.`name`
              WHERE clk.`affid` = ? AND t.`dispute_status` = 1 $tx_date_condition
              ORDER BY t.`created_at` DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($query);
    $bindIndex = 1;
    $stmt->bindValue($bindIndex++, $affiliateId, PDO::PARAM_INT);
    if (!empty($custom_date_params)) {
        $stmt->bindValue($bindIndex++, $custom_date_params[0]);
        $stmt->bindValue($bindIndex++, $custom_date_params[1]);
    }
    $stmt->bindValue($bindIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $reversed_commission = (float)($row['price'] * 0.50);
        $report_data[] = [
            'created_at'     => $row['created_at'],
            'type'           => 'Chargeback',
            'tx_id'          => !empty($row['tx_id']) ? $row['tx_id'] : '—',
            'cid'            => $row['cid'],
            's1'             => $row['s1'] ?? '—',
            's2'             => $row['s2'] ?? '—',
            'col_5'          => $row['country'] ?? '—',
            'col_6'          => !empty($row['client_name']) ? $row['client_name'] : 'Disputed Account User',
            'col_7'          => strtoupper($row['plan']),
            'payout'         => $reversed_commission,
            'dispute_reason' => !empty($row['dispute_reason']) ? $row['dispute_reason'] : '—',
            'is_financial'   => true,
            'is_negative'    => true
        ];
    }
}

$total_pages = ceil($total_records / $limit);
if ($total_pages < 1) $total_pages = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Performance Reports Ledger Matrix — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between bg-slate-50/50">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="max-w-[1650px] w-full mx-auto px-4 sm:px-6 pt-8 pb-16 grow space-y-6">
        
        <!-- REPORT QUERY FORM INPUT SECTION -->
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <form method="GET" action="affiliate-reports" class="flex flex-col sm:flex-row gap-3 items-end w-full">
                
                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Metric Type</label>
                    <select name="metric" class="w-full bg-slate-50 border border-gray-200 text-sm rounded-xl px-3 py-2.5 font-semibold text-slate-700 outline-none focus:border-indigo-500 transition-all">
                        <option value="clicks" <?= $filter_metric === 'clicks' ? 'selected' : '' ?>>Clicks</option>
                        <option value="conversions" <?= $filter_metric === 'conversions' ? 'selected' : '' ?>>Conversions</option>
                        <option value="recurring" <?= $filter_metric === 'recurring' ? 'selected' : '' ?>>Recurring Payouts</option>
                        <option value="chargebacks" <?= $filter_metric === 'chargebacks' ? 'selected' : '' ?>>Chargebacks</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Date Timeline Range</label>
                    <select name="date_range" id="dateRangeSelect" onchange="toggleCustomDate()" class="w-full bg-slate-50 border border-gray-200 text-sm rounded-xl px-3 py-2.5 font-semibold text-slate-700 outline-none focus:border-indigo-500 transition-all">
                        <option value="all_time" <?= $filter_date === 'all_time' ? 'selected' : '' ?>>All Times History</option>
                        <option value="today" <?= $filter_date === 'today' ? 'selected' : '' ?>>Today</option>
                        <option value="yesterday" <?= $filter_date === 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
                        <option value="this_week" <?= $filter_date === 'this_week' ? 'selected' : '' ?>>This Week</option>
                        <option value="this_month" <?= $filter_date === 'this_month' ? 'selected' : '' ?>>This Month</option>
                        <option value="last_month" <?= $filter_date === 'last_month' ? 'selected' : '' ?>>Last Month</option>
                        <option value="custom" <?= $filter_date === 'custom' ? 'selected' : '' ?>>Custom Date</option>
                    </select>
                </div>

                <div id="customDateFields" class="flex gap-3 items-end <?= $filter_date === 'custom' ? '' : 'hidden' ?>">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">Start Date</label>
                        <input type="date" name="start_date" value="<?= htmlspecialchars($custom_start) ?>" class="w-full bg-slate-50 border border-gray-200 text-sm rounded-xl px-3 py-2.5 font-semibold text-slate-700 outline-none focus:border-indigo-500 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-extrabold text-gray-400 uppercase tracking-wider">End Date</label>
                        <input type="date" name="end_date" value="<?= htmlspecialchars($custom_end) ?>" class="w-full bg-slate-50 border border-gray-200 text-sm rounded-xl px-3 py-2.5 font-semibold text-slate-700 outline-none focus:border-indigo-500 transition-all">
                    </div>
                </div>

                <div class="ml-auto">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-filter text-sm"></i> Run Reports
                    </button>
                </div>
                </button>
            </form>
        </div>

        <!-- REVENUE AND REFERRAL LEDGER RESULT MATRIX VIEW -->
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-slate-50/40 flex justify-between items-center">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Performance Audit Ledger</h3>
                <span class="text-xs font-mono text-slate-400">Showing <?= count($report_data) ?> of <?= $total_records ?> logs</span>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-slate-50/20">
                            <th class="px-4 py-3.5 whitespace-nowrap">Date and Time</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Type</th>
                            
                            <?php if ($filter_metric !== 'clicks'): ?>
                                <th class="px-4 py-3.5 whitespace-nowrap">TXID</th>
                            <?php endif; ?>

                            <th class="px-4 py-3.5 whitespace-nowrap">Click ID</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Subid 1</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Subid 2</th>
                            
                            <?php if ($filter_metric === 'clicks'): ?>
                                <th class="px-4 py-3.5 whitespace-nowrap">Converted</th>
                                <th class="px-4 py-3.5 whitespace-nowrap">IP</th>
                                <th class="px-4 py-3.5 whitespace-nowrap">Country</th>
                                <th class="px-4 py-3.5 whitespace-nowrap">OS</th>
                                <th class="px-4 py-3.5 whitespace-nowrap">Browser</th>
                                <th class="px-4 py-3.5">Referrer</th>
                            <?php else: ?>
                                <th class="px-4 py-3.5 whitespace-nowrap">Country</th>
                                <th class="px-4 py-3.5 whitespace-nowrap">Client Name</th>
                                <th class="px-4 py-3.5 whitespace-nowrap">Plan</th>
                                <th class="px-4 py-3.5 whitespace-nowrap text-right">Payout</th>
                                <?php if ($filter_metric === 'chargebacks'): ?>
                                    <th class="px-4 py-3.5 whitespace-nowrap text-left">Dispute Reason</th>
                                <?php endif; ?>
                                <?php if ($filter_metric === 'conversions'): ?>
                                    <th class="px-4 py-3.5 whitespace-nowrap text-center">Postback Fired</th>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium text-slate-700">
                        <?php if (empty($report_data)): ?>
                        <tr>
                            <td colspan="12" class="px-6 py-12 text-center text-gray-400 font-semibold font-mono">
                                <i class="fa-solid fa-database text-2xl block mb-2 text-slate-300"></i>
                                No tracking elements found within selected configurations.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($report_data as $log): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <!-- Intercept raw UTC date attributes dynamically via JS conversion classes -->
                                <td class="px-4 py-4 font-mono text-slate-500 whitespace-nowrap utc-time-transform" data-utc="<?= htmlspecialchars($log['created_at']) ?>">
                                    <?= htmlspecialchars($log['created_at']) ?> UTC
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php if ($log['type'] === 'Click'): ?>
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 font-bold px-2 py-0.5 rounded text-[10px] border border-amber-100">Click</span>
                                    <?php elseif ($log['type'] === 'Conversion'): ?>
                                        <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 font-bold px-2 py-0.5 rounded text-[10px] border border-indigo-100">Conversion</span>
                                    <?php elseif ($log['type'] === 'Recurring Payout'): ?>
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded text-[10px] border border-emerald-100">Recurring</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 font-bold px-2 py-0.5 rounded text-[10px] border border-red-100">Chargeback</span>
                                    <?php endif; ?>
                                </td>

                                <?php if ($filter_metric !== 'clicks'): ?>
                                    <td class="px-4 py-4 font-mono text-gray-900 font-bold whitespace-nowrap"><?= htmlspecialchars($log['tx_id']) ?></td>
                                <?php endif; ?>

                                <td class="px-4 py-4 font-mono text-gray-400 select-all whitespace-nowrap"><?= htmlspecialchars($log['cid']) ?></td>
                                <td class="px-4 py-4 font-mono text-gray-500 whitespace-nowrap"><?= htmlspecialchars($log['s1']) ?></td>
                                <td class="px-4 py-4 font-mono text-gray-500 whitespace-nowrap"><?= htmlspecialchars($log['s2']) ?></td>
                                
                                <?php if ($filter_metric === 'clicks'): ?>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="font-mono font-bold text-[10px] px-2 py-0.5 rounded <?= $log['converted'] === 'Yes' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-400 border border-slate-200' ?>">
                                            <?= $log['converted'] ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 font-mono whitespace-nowrap"><?= htmlspecialchars($log['col_5']) ?></td>
                                    <td class="px-4 py-4 uppercase font-bold text-gray-600 whitespace-nowrap"><?= htmlspecialchars($log['col_6']) ?></td>
                                    <td class="px-4 py-4 text-gray-500 whitespace-nowrap"><?= htmlspecialchars($log['col_7']) ?></td>
                                    <td class="px-4 py-4 text-gray-500 whitespace-nowrap"><?= htmlspecialchars($log['col_8']) ?></td>
                                    <td class="px-4 py-4 font-mono text-[11px] text-slate-400 break-all select-all min-w-[200px]">
                                        <?= htmlspecialchars($log['col_9']) ?>
                                    </td>
                                <?php else: ?>
                                    <td class="px-4 py-4 uppercase font-bold text-gray-600 whitespace-nowrap"><?= htmlspecialchars($log['col_5']) ?></td>
                                    <td class="px-4 py-4 font-bold text-gray-900 whitespace-nowrap"><?= htmlspecialchars($log['col_6']) ?></td>
                                    <td class="px-4 py-4 font-mono text-indigo-600 font-bold whitespace-nowrap"><?= htmlspecialchars($log['col_7']) ?></td>
                                    
                                    <td class="px-4 py-4 text-right font-mono font-black text-sm whitespace-nowrap <?= $log['is_negative'] ? 'text-red-600' : 'text-emerald-600' ?>">
                                        <?= $log['is_negative'] ? '-$' . number_format($log['payout'], 2) : '+$' . number_format($log['payout'], 2) ?>
                                    </td>
                                    
                                    <?php if ($filter_metric === 'chargebacks'): ?>
                                        <td class="px-4 py-4 text-left font-mono font-bold text-gray-500 whitespace-nowrap">
                                            <?= htmlspecialchars($log['dispute_reason']) ?>
                                        </td>
                                    <?php endif; ?>

                                    <?php if ($filter_metric === 'conversions'): ?>
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <span class="font-mono font-bold text-[10px] px-2 py-0.5 rounded <?= $log['pb_fired'] === 'Yes' ? 'bg-indigo-50 text-indigo-700 border border-indigo-100' : 'bg-slate-100 text-slate-400 border border-slate-200' ?>">
                                                <?= $log['pb_fired'] ?>
                                            </span>
                                        </td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINATION INTERFACE LAYER PANEL -->
            <?php if ($total_pages > 1): ?>
                <div class="px-6 py-4 border-t border-gray-100 bg-slate-50/30 flex items-center justify-between">
                    <div class="text-xs text-gray-500 font-medium">
                        Page <span class="font-bold text-slate-700"><?= $page ?></span> of <span class="font-bold text-slate-700"><?= $total_pages ?></span>
                    </div>
                    <div class="inline-flex gap-1">
                        <!-- Previous Page Nav Anchor -->
                        <a href="?metric=<?= urlencode($filter_metric) ?>&date_range=<?= urlencode($filter_date) ?><?= $filter_date === 'custom' ? '&start_date=' . urlencode($custom_start) . '&end_date=' . urlencode($custom_end) : '' ?>&page=<?= max(1, $page - 1) ?>" 
                           class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 shadow-sm transition-all flex items-center gap-1 <?= $page <= 1 ? 'pointer-events-none opacity-40' : '' ?>">
                            <i class="fa-solid fa-angle-left"></i> Prev
                        </a>
                        
                        <!-- Dynamic Page Number Anchors -->
                        <?php 
                        $start = max(1, $page - 2);
                        $end = min($total_pages, $page + 2);
                        for ($i = $start; $i <= $end; $i++): 
                        ?>
                            <a href="?metric=<?= urlencode($filter_metric) ?>&date_range=<?= urlencode($filter_date) ?><?= $filter_date === 'custom' ? '&start_date=' . urlencode($custom_start) . '&end_date=' . urlencode($custom_end) : '' ?>&page=<?= $i ?>" 
                               class="px-3 py-1.5 rounded-lg border text-xs font-bold shadow-sm transition-all <?= $i === $page ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-gray-200 text-slate-600 hover:bg-slate-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Next Page Nav Anchor -->
                        <a href="?metric=<?= urlencode($filter_metric) ?>&date_range=<?= urlencode($filter_date) ?><?= $filter_date === 'custom' ? '&start_date=' . urlencode($custom_start) . '&end_date=' . urlencode($custom_end) : '' ?>&page=<?= min($total_pages, $page + 1) ?>" 
                           class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs font-bold text-slate-600 hover:bg-slate-50 shadow-sm transition-all flex items-center gap-1 <?= $page >= $total_pages ? 'pointer-events-none opacity-40' : '' ?>">
                            Next <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        <div class="flex items-center justify-center gap-2 mb-2">
            <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
        </div>
        &copy; <?= date('Y'); ?> Identity Search AI Affiliate Portal. All rights reserved.
    </footer>

    <!-- DYNAMIC CLIENT TIMEZONE EVALUATOR SCRIPT -->
    <script>
        function toggleCustomDate() {
            const sel = document.getElementById('dateRangeSelect');
            const fields = document.getElementById('customDateFields');
            if (sel.value === 'custom') {
                fields.classList.remove('hidden');
            } else {
                fields.classList.add('hidden');
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Intercept all nodes tracking string elements injected by database layers
            const timelineCells = document.querySelectorAll('.utc-time-transform');
            
            timelineCells.forEach(cell => {
                const rawUtcString = cell.getAttribute('data-utc');
                if (!rawUtcString || rawUtcString === '—') return;

                // Append direct ISO normalization spacer elements so JS parses the string correctly as explicit UTC
                const normalizedIsoString = rawUtcString.trim().replace(' ', 'T') + 'Z';
                const dateInstance = new Date(normalizedIsoString);

                // Safe fallback evaluation checking if string conversions succeeded cleanly
                if (!isNaN(dateInstance.getTime())) {
                    // Extrapolate structural layout blocks using local machine location metrics
                    const year = dateInstance.getFullYear();
                    const month = String(dateInstance.getMonth() + 1).padStart(2, '0');
                    const day = String(dateInstance.getDate()).padStart(2, '0');
                    
                    let hours = dateInstance.getHours();
                    const minutes = String(dateInstance.getMinutes()).padStart(2, '0');
                    const seconds = String(dateInstance.getSeconds()).padStart(2, '0');
                    
                    // Format into a highly clean, structured view: YYYY-MM-DD HH:MM:SS
                    const formattedLocalTime = `${year}-${month}-${day} ${String(hours).padStart(2, '0')}:${minutes}:${seconds}`;
                    cell.textContent = formattedLocalTime;
                }
            });
        });
    </script>
</body>
</html>