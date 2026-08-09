<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

if (!hash_equals('603153b5487ee9bd33d2e8d882d1c1ab', trim($_GET['key'] ?? ''))) {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

$out = [];

$out['db_now'] = $pdo->query("SELECT NOW()")->fetchColumn();
$out['users_total'] = (int)$pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();
$out['users_today'] = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE DATE(created_at) = CURRENT_DATE()")->fetchColumn();
$out['users_yesterday'] = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE DATE(created_at) = DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)")->fetchColumn();
$out['users_last7'] = $pdo->query("SELECT DATE(created_at) d, COUNT(*) c FROM `users` WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY d")->fetchAll(PDO::FETCH_KEY_PAIR);

$out['affiliates'] = $pdo->query("SELECT id, aid, name, email, status FROM `affiliates` ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

$out['conv_per_aff'] = $pdo->query("SELECT affid, COUNT(DISTINCT uid) uids, COUNT(*) rows FROM `conversions` WHERE affid IS NOT NULL GROUP BY affid ORDER BY affid")->fetchAll(PDO::FETCH_ASSOC);
$out['click_per_aff'] = $pdo->query("SELECT affid, COUNT(*) rows FROM `clicks` WHERE affid IS NOT NULL GROUP BY affid ORDER BY affid")->fetchAll(PDO::FETCH_ASSOC);

$out['zqsm'] = $pdo->query("SELECT c.uid, c.affid, c.plan, c.tid, u.cid, u.email, u.created_at FROM `conversions` c LEFT JOIN `users` u ON u.id = c.uid WHERE c.uid IN (1336,1337,1338,1341,1348) ORDER BY c.uid")->fetchAll(PDO::FETCH_ASSOC);

$out['clicks_for_cids'] = $pdo->query("SELECT cid, affid, s1, s2, created_at FROM `clicks` WHERE cid IN (SELECT cid FROM `users` WHERE id IN (1336,1337,1338,1341,1348) AND cid IS NOT NULL) ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

$out['users_1336_1348'] = $pdo->query("SELECT id, email, cid, created_at FROM `users` WHERE id IN (1336,1337,1338,1341,1348)")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
