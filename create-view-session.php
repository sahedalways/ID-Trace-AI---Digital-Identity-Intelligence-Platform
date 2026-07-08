<?php
/**
 * OSINT Universal Intelligence Console — Target View Session Initialization
 * File: create-view-session.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enforce explicit JSON responses with standard charset layout definitions
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
    exit;
}

// 1. Capture incoming form parameters
$name   = isset($_POST['name']) ? trim($_POST['name']) : '';
$avatar = isset($_POST['avatar']) ? trim($_POST['avatar']) : '';
$source = isset($_POST['source']) ? trim($_POST['source']) : '';
$url    = isset($_POST['url']) ? trim($_POST['url']) : '';
$uid    = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// Validate basic mandatory tracking data strings
if (empty($name) || empty($source)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Validation Fault: Incomplete identity footprint metrics.']);
    exit;
}

try {
    // 2. Generate a unique 16-digit cryptographic numeric string for the 'vid'
    $is_unique = false;
    $vid = '';
    $attempts = 0;

    while (!$is_unique && $attempts < 10) {
        $vid = '';
        for ($i = 0; $i < 16; $i++) {
            $vid .= mt_rand(0, 9);
        }
        
        // Double check database collision parameters
        $chk = $pdo->prepare("SELECT 1 FROM `view` WHERE `vid` = ? LIMIT 1");
        $chk->execute([$vid]);
        if (!$chk->fetch()) {
            $is_unique = true;
        }
        $attempts++;
    }

    if (!$is_unique) {
        throw new Exception('Unique indexing signature window timed out. Please try again.');
    }

    // 3. DB OPERATION: Write record tracking trace values securely
    $stmt = $pdo->prepare("
        INSERT INTO `view` (`vid`, `uid`, `name`, `avatar`, `source`, `url`, `created_at`) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    
    // Explicit parameter bindings protect against loose types failing on older server engines
    $stmt->bindValue(1, $vid, PDO::PARAM_STR);
    $stmt->bindValue(2, $uid, $uid === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindValue(3, $name, PDO::PARAM_STR);
    $stmt->bindValue(4, !empty($avatar) ? $avatar : null, $avatar === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(5, $source, PDO::PARAM_STR);
    $stmt->bindValue(6, !empty($url) ? $url : null, $url === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
    
    $stmt->execute();

    // 4. Return success footprint wrapper to the JS framework
    echo json_encode([
        'success' => true, 
        'vid'     => $vid
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error'   => 'Database Persistence Failure: ' . $e->getMessage()
    ]);
}
