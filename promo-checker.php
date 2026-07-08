<?php
/**
 * Identity Trace AI — External Promotional Verification API Core
 * File: promo-checker.php
 * Endpoint Layout: promo-checker.php?key=xxx&promo=xxx
 */
require_once 'config.php';

// Enforce strict JSON transmission content-type header rules
header('Content-Type: application/json; charset=utf-8');

$expected_secret_key = '01765645695';

// 1. Collect inbound parameters safely from query context maps
$provided_key   = isset($_GET['key']) ? trim($_GET['key']) : '';
$provided_promo = isset($_GET['promo']) ? trim($_GET['promo']) : '';

// 2. Execution Validation Matrix Checks
if (empty($provided_key) || empty($provided_promo)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'failed',
        'note'   => 'Inbound transaction dropped: Missing required key or promo validation signatures.'
    ]);
    exit;
}

if ($provided_key !== $expected_secret_key) {
    http_response_code(401);
    echo json_encode([
        'status' => 'failed',
        'note'   => 'Security access refused: Fixed authorization key mismatch.'
    ]);
    exit;
}

try {
    // 3. Search relational db matrices to match the unique promo record row context
    $stmt = $pdo->prepare("SELECT `id`, `email`, `created_at` FROM `promo` WHERE `promo_code` = ? LIMIT 1");
    $stmt->execute([$provided_promo]);
    $promo_record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$promo_record) {
        http_response_code(444);
        echo json_encode([
            'status' => 'failed',
            'note'   => 'Verification sequence failed: Requested promo code not found.'
        ]);
        exit;
    }

    // 4. Success Pipeline Vector Output Execution Loop
    http_response_code(200);
    echo json_encode([
        'status'     => 'success',
        'note'       => 'Promo code validated successfully.',
        'data'       => [
            'email'      => $promo_record['email'],
            'created_at' => $promo_record['created_at']
        ]
    ]);
    exit;

} catch (\PDOException $e) {
    // Graceful schema exception layer protection tracking parameter drops
    http_response_code(500);
    echo json_encode([
        'status' => 'failed',
        'note'   => 'Internal systems error layer triggered: ' . $e->getMessage()
    ]);
    exit;
}
