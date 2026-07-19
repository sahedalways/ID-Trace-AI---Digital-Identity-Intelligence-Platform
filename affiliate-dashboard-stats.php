<?php
/**
 * File: affiliate-dashboard-stats.php
 * AJAX API endpoint for filtered dashboard statistics.
 * Returns JSON metrics based on time period filter.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['affiliate_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$affiliateId = (int)$_SESSION['affiliate_id'];
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'lifetime';

// Build date conditions per table
$dateCondition = "";
switch ($filter) {
    case 'today':
        $dateCondition = "AND DATE(created_at) = CURRENT_DATE()";
        break;
    case 'yesterday':
        $dateCondition = "AND DATE(created_at) = DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)";
        break;
    case 'this_week':
        $dateCondition = "AND YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)";
        break;
    case 'this_month':
        $dateCondition = "AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        break;
    case 'last_month':
        $dateCondition = "AND MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))";
        break;
    case 'this_year':
        $dateCondition = "AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        break;
    case 'lifetime':
    default:
        $dateCondition = "";
        break;
}

try {
    // Balance always shows lifetime total (not filterable)
    $affStmt = $pdo->prepare("SELECT `balance` FROM `affiliates` WHERE `id` = ? LIMIT 1");
    $affStmt->execute([$affiliateId]);
    $balance = (float)$affStmt->fetchColumn();

    if ($filter === 'lifetime') {
        // Withdrawn lifetime: pull from affiliates table
        $affStmt2 = $pdo->prepare("SELECT `withdraw` FROM `affiliates` WHERE `id` = ? LIMIT 1");
        $affStmt2->execute([$affiliateId]);
        $withdrawn = (float)$affStmt2->fetchColumn();
    } else {
        // Filtered Withdrawn: sum from withdraw table
        $wdDate = str_replace('created_at', 'w.`created_at`', $dateCondition);
        $wdStmt = $pdo->prepare("
            SELECT COALESCE(SUM(w.`amount`), 0) FROM `withdraw` w WHERE w.`affid` = ? $wdDate
        ");
        $wdStmt->execute([$affiliateId]);
        $withdrawn = (float)$wdStmt->fetchColumn();
    }

    // Total Clicks
    $clicksStmt = $pdo->prepare("SELECT COUNT(*) FROM `clicks` WHERE `affid` = ? $dateCondition");
    $clicksStmt->execute([$affiliateId]);
    $totalClicks = (int)$clicksStmt->fetchColumn();

    // Converted Sales
    $convDate = str_replace('created_at', 'conv.`created_at`', $dateCondition);
    $convStmt = $pdo->prepare("SELECT COUNT(*) FROM `conversions` conv WHERE conv.`affid` = ? $convDate");
    $convStmt->execute([$affiliateId]);
    $totalConversions = (int)$convStmt->fetchColumn();

    // Recurring Billings
    $recDate = str_replace('created_at', 'rec.`created_at`', $dateCondition);
    $recStmt = $pdo->prepare("SELECT COUNT(*) FROM `recurring` rec WHERE rec.`affid` = ? $recDate");
    $recStmt->execute([$affiliateId]);
    $totalRecurring = (int)$recStmt->fetchColumn();

    // Chargebacks
    $txDate = str_replace('created_at', 't.`created_at`', $dateCondition);
    $cbStmt = $pdo->prepare("
        SELECT COUNT(*) FROM `transactions` t
        WHERE t.`dispute_status` = 1
        AND t.`uid` IN (SELECT `uid` FROM `conversions` WHERE `affid` = ?)
        $txDate
    ");
    $cbStmt->execute([$affiliateId]);
    $totalChargebacks = (int)$cbStmt->fetchColumn();

    echo json_encode([
        'success'       => true,
        'filter'        => $filter,
        'balance'       => number_format($balance, 2),
        'withdrawn'     => number_format($withdrawn, 2),
        'clicks'        => number_format($totalClicks),
        'conversions'   => number_format($totalConversions),
        'recurring'     => number_format($totalRecurring),
        'chargebacks'   => number_format($totalChargebacks),
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    exit;
}
