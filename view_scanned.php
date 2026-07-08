<?php
/**
 * OSINT Universal Intelligence Console — Silent Cache State Committer
 * File: view_scanned.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid transaction routing boundary.']);
    exit;
}

$vid = isset($_POST['id']) ? trim($_POST['id']) : '';

if (!empty($vid)) {
    try {
        // Toggle the scanned state column framework natively for the targeting row element
        $stmt = $pdo->prepare("UPDATE `view` SET `scanned` = 1 WHERE `vid` = ?");
        $success = $stmt->execute([$vid]);
        
        echo json_encode(['success' => $success]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Target identification context empty.']);
exit;
