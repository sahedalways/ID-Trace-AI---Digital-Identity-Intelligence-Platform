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
$out['conv_per_aff'] = q($pdo, "SELECT affid, COUNT(DISTINCT uid) AS uids, COUNT(*) AS cnt FROM `conversions` WHERE affid IS NOT NULL GROUP BY affid ORDER BY affid");
$out['click_per_aff'] = q($pdo, "SELECT affid, COUNT(*) AS cnt FROM `clicks` WHERE affid IS NOT NULL GROUP BY affid ORDER BY affid");

$attribution = "(EXISTS (SELECT 1 FROM `conversions` cv WHERE cv.`uid` = u.`id` AND cv.`affid` = ?)
        OR EXISTS (SELECT 1 FROM `clicks` cl WHERE cl.`affid` = ? AND CONVERT(u.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(cl.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci))";
foreach ([6,10,11,12,14,15,17,19,20,21,22,24,25,26,28] as $aid) {
    $s = $pdo->prepare("SELECT COUNT(DISTINCT u.`id`) AS cnt FROM `users` u WHERE $attribution");
    $s->execute([$aid, $aid]);
    $cnt = (int)$s->fetchColumn();
    $names = [];
    if ($cnt > 0 && $cnt <= 20) {
        $s2 = $pdo->prepare("SELECT u.`id`, u.`email`, u.`plan` FROM `users` u WHERE $attribution ORDER BY u.`id` DESC");
        $s2->execute([$aid, $aid]);
        $names = $s2->fetchAll(PDO::FETCH_ASSOC);
    }
    $out['aff_clients']['id_' . $aid] = ['cnt' => $cnt, 'clients' => $names];
}
$out['target_convs'] = qp($pdo, "SELECT c.uid, c.affid, c.plan, c.tid, u.cid, u.email, u.created_at FROM `conversions` c LEFT JOIN `users` u ON u.id = c.uid WHERE c.uid IN (1336,1337,1338,1341,1348) ORDER BY c.uid", []);
$out['target_clicks'] = q($pdo, "SELECT cid, affid, s1, s2, created_at FROM `clicks` WHERE cid IN (SELECT cid FROM `users` WHERE id IN (1336,1337,1338,1341,1348) AND cid IS NOT NULL) ORDER BY created_at DESC");
$out['target_users'] = qp($pdo, "SELECT id, email, cid, created_at FROM `users` WHERE id IN (1336,1337,1338,1341,1348)", []);

echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
