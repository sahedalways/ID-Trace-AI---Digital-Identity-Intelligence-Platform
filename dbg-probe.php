<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

if (!hash_equals('603153b5487ee9bd33d2e8d882d1c1ab', trim($_GET['key'] ?? ''))) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

function q($pdo, $sql) {
    try {
        return ['ok' => true, 'data' => $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC)];
    } catch (\Throwable $e) {
        return ['ok' => false, 'error' => $e->getMessage(), 'sql' => $sql];
    }
}

function qp($pdo, $sql, $params) {
    try {
        $s = $pdo->prepare($sql);
        $s->execute($params);
        return ['ok' => true, 'data' => $s->fetchAll(PDO::FETCH_ASSOC)];
    } catch (\Throwable $e) {
        return ['ok' => false, 'error' => $e->getMessage(), 'sql' => $sql];
    }
}

$out = [];
$out['db_now'] = q($pdo, "SELECT NOW() AS n, CURRENT_DATE() AS d");
$out['users_total'] = q($pdo, "SELECT COUNT(*) AS c FROM `users`");
$out['users_today'] = q($pdo, "SELECT COUNT(*) AS c FROM `users` WHERE DATE(created_at) = CURRENT_DATE()");
$out['users_yesterday'] = q($pdo, "SELECT COUNT(*) AS c FROM `users` WHERE DATE(created_at) = DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)");
$out['users_last7'] = q($pdo, "SELECT DATE(created_at) AS d, COUNT(*) AS c FROM `users` WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY d");
$out['affiliates'] = q($pdo, "SELECT id, aid, name, email, status FROM `affiliates` ORDER BY id");
$out['conv_per_aff'] = q($pdo, "SELECT affid, COUNT(DISTINCT uid) AS uids, COUNT(*) AS rows FROM `conversions` WHERE affid IS NOT NULL GROUP BY affid ORDER BY affid");
$out['click_per_aff'] = q($pdo, "SELECT affid, COUNT(*) AS rows FROM `clicks` WHERE affid IS NOT NULL GROUP BY affid ORDER BY affid");
$out['target_convs'] = qp($pdo, "SELECT c.uid, c.affid, c.plan, c.tid, u.cid, u.email, u.created_at FROM `conversions` c LEFT JOIN `users` u ON u.id = c.uid WHERE c.uid IN (1336,1337,1338,1341,1348) ORDER BY c.uid", []);
$out['target_clicks'] = q($pdo, "SELECT cid, affid, s1, s2, created_at FROM `clicks` WHERE cid IN (SELECT cid FROM `users` WHERE id IN (1336,1337,1338,1341,1348) AND cid IS NOT NULL) ORDER BY created_at DESC");
$out['target_users'] = qp($pdo, "SELECT id, email, cid, created_at FROM `users` WHERE id IN (1336,1337,1338,1341,1348)", []);

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
