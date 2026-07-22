<?php
/**
 * File: admin-reports-stats.php
 * AJAX endpoint for affiliate-specific filtered report stats.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$affid = (int)($_GET['affid'] ?? 0);
$filter = $_GET['filter'] ?? 'all_time';

if (!$affid) {
    echo json_encode(['success' => false]);
    exit;
}

$dateCondition = '';
switch ($filter) {
    case 'today':        $dateCondition = "AND DATE(created_at) = CURDATE()"; break;
    case 'yesterday':    $dateCondition = "AND DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)"; break;
    case 'last_7_days':  $dateCondition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"; break;
    case 'this_month':   $dateCondition = "AND YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())"; break;
    case 'last_month':   $dateCondition = "AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))"; break;
}

try {
    $clicks = (int)$pdo->query("SELECT COUNT(*) FROM `clicks` WHERE `affid` = $affid $dateCondition")->fetchColumn();

    $convRow = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(`price`),0) as revenue, COALESCE(SUM(`payout`),0) as payout FROM `conversions` WHERE `affid` = $affid $dateCondition")->fetch(PDO::FETCH_ASSOC);

    $chargeRow = $pdo->query("SELECT COUNT(*) as total FROM `transactions` WHERE `dispute_status` = 1 AND `uid` IN (SELECT `uid` FROM `conversions` WHERE `affid` = $affid) $dateCondition")->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'clicks' => number_format($clicks),
        'conversions' => number_format($convRow['total']),
        'revenue' => number_format($convRow['revenue'], 2),
        'payout' => number_format($convRow['payout'], 2),
        'chargebacks' => number_format($chargeRow['total']),
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false]);
}