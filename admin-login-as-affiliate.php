<?php
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login");
    exit;
}

$affId = (int)($_GET['id'] ?? 0);
if (!$affId) {
    header("Location: admin-affiliates");
    exit;
}

$stmt = $pdo->prepare("SELECT id, email, name, status FROM `affiliates` WHERE id = ? LIMIT 1");
$stmt->execute([$affId]);
$affiliate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$affiliate || $affiliate['status'] !== 'active') {
    $_SESSION['flash_error'] = "Cannot login as this affiliate. Account may not be active.";
    header("Location: admin-affiliates");
    exit;
}

$_SESSION['affiliate_id'] = $affiliate['id'];
$_SESSION['affiliate_email'] = $affiliate['email'];
$_SESSION['affiliate_name'] = $affiliate['name'];
$_SESSION['admin_impersonating'] = true;

header("Location: affiliate-dashboard");
exit;