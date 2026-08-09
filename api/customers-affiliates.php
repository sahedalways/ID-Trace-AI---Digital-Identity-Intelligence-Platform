<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

function apiRespond($code, $payload) {
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

if (empty(INTEGRATION_API_KEY)) {
    apiRespond(500, ['success' => false, 'error' => 'API not configured. Set INTEGRATION_API_KEY in .env']);
}

$apiKey = $_GET['api_key'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? '');
if (!is_string($apiKey) || !hash_equals(INTEGRATION_API_KEY, trim($apiKey))) {
    apiRespond(401, ['success' => false, 'error' => 'Invalid API key.']);
}

$source   = trim($_GET['source'] ?? '');
$userType = strtolower(trim($_GET['user_type'] ?? ''));

if ($source === '') {
    apiRespond(422, ['success' => false, 'error' => 'source parameter is required.']);
}
if (!in_array($userType, ['customer', 'affiliate'], true)) {
    apiRespond(422, ['success' => false, 'error' => 'user_type must be either "customer" or "affiliate".']);
}

$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$email  = trim($_GET['email'] ?? '');
$q      = trim($_GET['q'] ?? '');
$limit  = min(max((int)($_GET['limit'] ?? 500), 1), 1000);
$offset = max((int)($_GET['offset'] ?? 0), 0);

$where  = "WHERE 1=1";
$params = [];
if ($id > 0) {
    $where .= " AND `id` = ?";
    $params[] = $id;
}
if ($email !== '') {
    $where .= " AND `email` = ?";
    $params[] = $email;
}
if ($q !== '') {
    $where .= " AND (`name` LIKE ? OR `email` LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

try {
    if ($userType === 'affiliate') {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `affiliates` $where");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT `id`, `name`, `email`, `mobile` AS phone FROM `affiliates` $where ORDER BY `id` DESC LIMIT $limit OFFSET $offset");
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `users` $where");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT `id`, `name`, `email`, NULL AS phone FROM `users` $where ORDER BY `id` DESC LIMIT $limit OFFSET $offset");
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Integration API Error: " . $e->getMessage());
    apiRespond(500, ['success' => false, 'error' => 'Database error.']);
}

foreach ($rows as &$row) {
    $row['name']  = $row['name'] ?? '';
    $row['email'] = $row['email'] ?? '';
    $row['phone'] = trim((string)($row['phone'] ?? ''));
}
unset($row);

apiRespond(200, [
    'success'   => true,
    'source'    => $source,
    'user_type' => $userType,
    'total'     => $total,
    'limit'     => $limit,
    'offset'    => $offset,
    'data'      => $rows,
]);
