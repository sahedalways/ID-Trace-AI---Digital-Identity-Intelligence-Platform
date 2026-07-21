<?php
/**
 * File: admin-dashboard-stats.php
 * AJAX endpoint for filtered dashboard stats.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all_time';

// Build separate date conditions per table
$clickDate = '';
$convDate = '';
$txnDate = '';
$withdrawDate = '';

switch ($filter) {
    case 'today':
        $clickDate = "AND DATE(`created_at`) = CURDATE()";
        $convDate = "AND DATE(`created_at`) = CURDATE()";
        $txnDate = "AND DATE(`created_at`) = CURDATE()";
        $withdrawDate = "AND DATE(`created_at`) = CURDATE()";
        break;
    case 'yesterday':
        $clickDate = "AND DATE(`created_at`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        $convDate = "AND DATE(`created_at`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        $txnDate = "AND DATE(`created_at`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        $withdrawDate = "AND DATE(`created_at`) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        break;
    case 'last_7_days':
        $clickDate = "AND `created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $convDate = "AND `created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $txnDate = "AND `created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $withdrawDate = "AND `created_at` >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        break;
    case 'this_month':
        $clickDate = "AND YEAR(`created_at`) = YEAR(NOW()) AND MONTH(`created_at`) = MONTH(NOW())";
        $convDate = "AND YEAR(`created_at`) = YEAR(NOW()) AND MONTH(`created_at`) = MONTH(NOW())";
        $txnDate = "AND YEAR(`created_at`) = YEAR(NOW()) AND MONTH(`created_at`) = MONTH(NOW())";
        $withdrawDate = "AND YEAR(`created_at`) = YEAR(NOW()) AND MONTH(`created_at`) = MONTH(NOW())";
        break;
    case 'last_month':
        $clickDate = "AND YEAR(`created_at`) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND MONTH(`created_at`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
        $convDate = "AND YEAR(`created_at`) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND MONTH(`created_at`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
        $txnDate = "AND YEAR(`created_at`) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND MONTH(`created_at`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
        $withdrawDate = "AND YEAR(`created_at`) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND MONTH(`created_at`) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))";
        break;
}

try {
    // Total clicks
    $totalClicks = (int)$pdo->query("SELECT COUNT(*) FROM `clicks` WHERE 1=1 $clickDate")->fetchColumn();

    // Total conversions + revenue + payout
    $convRow = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(`price`), 0) as revenue, COALESCE(SUM(`payout`), 0) as payout FROM `conversions` WHERE 1=1 $convDate")->fetch(PDO::FETCH_ASSOC);
    $totalConversions = (int)$convRow['total'];
    $totalRevenue = (float)$convRow['revenue'];
    $totalPayout = (float)$convRow['payout'];

    // Total chargebacks
    $chargeRow = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(`dispute_amount`), 0) as amount FROM `transactions` WHERE `dispute_status` = 1 $txnDate")->fetch(PDO::FETCH_ASSOC);
    $totalChargebacks = (int)$chargeRow['total'];
    $chargebackAmount = (float)$chargeRow['amount'];

    // Total withdrawn
    $totalWithdrawn = (float)$pdo->query("SELECT COALESCE(SUM(`amount`), 0) FROM `withdraw` WHERE `status` = 'approved' $withdrawDate")->fetchColumn();

    // Total affiliates
    $totalAffiliates = (int)$pdo->query("SELECT COUNT(*) FROM `affiliates`")->fetchColumn();

    echo json_encode([
        'success' => true,
        'clicks' => number_format($totalClicks),
        'conversions' => number_format($totalConversions),
        'revenue' => number_format($totalRevenue, 2),
        'payout' => number_format($totalPayout, 2),
        'chargebacks' => number_format($totalChargebacks),
        'chargeback_amount' => number_format($chargebackAmount, 2),
        'withdrawn' => number_format($totalWithdrawn, 2),
        'affiliates' => number_format($totalAffiliates),
    ]);

} catch (PDOException $e) {
    error_log("Admin Dashboard Stats Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Query error']);
}
