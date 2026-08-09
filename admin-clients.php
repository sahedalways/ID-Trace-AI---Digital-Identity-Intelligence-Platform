<?php
/**
 * File: admin-clients.php
 * Admin customers — paginated table with search + subscription filters.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    $_SESSION['admin_intended'] = $_SERVER['REQUEST_URI'];
    header("Location: admin-login");
    exit;
}

$success_msg = "";
if (isset($_SESSION['flash_success'])) {
    $success_msg = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $success_msg = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$subFilter = isset($_GET['sub']) ? $_GET['sub'] : 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$filter_date = isset($_GET['date_range']) ? trim($_GET['date_range']) : 'lifetime';

// Resolve dynamic date interval boundaries (applied to u.created_at)
$date_condition = "";
switch ($filter_date) {
    case 'today':
        $date_condition = "DATE(created_at) = CURRENT_DATE()";
        break;
    case 'yesterday':
        $date_condition = "DATE(created_at) = DATE_SUB(CURRENT_DATE(), INTERVAL 1 DAY)";
        break;
    case 'this_week':
        $date_condition = "YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)";
        break;
    case 'this_month':
        $date_condition = "MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        break;
    case 'last_month':
        $date_condition = "MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))";
        break;
    case 'this_year':
        $date_condition = "YEAR(created_at) = YEAR(CURRENT_DATE())";
        break;
    case 'lifetime':
    default:
        $date_condition = "";
        break;
}

$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(u.email LIKE ? OR u.name LIKE ? OR u.id = ? OR a.aid LIKE ? OR a.email LIKE ? OR cl.s1 LIKE ? OR cl.s2 LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = $search;
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

switch ($subFilter) {
    case 'active_sub':
        $conditions[] = "u.stripe_subscription_id IS NOT NULL AND u.stripe_subscription_id != ''";
        break;
    case 'no_sub':
        $conditions[] = "(u.plan IS NULL OR u.plan = '' OR u.plan = 'FREE TIER') AND u.status != 'inactive'";
        break;
    case 'cancelled':
        $conditions[] = "u.status = 'inactive' AND NOT EXISTS (SELECT 1 FROM `transactions` tx WHERE tx.uid = u.id AND tx.dispute_status = 1)";
        break;
    case 'chargeback':
        $conditions[] = "EXISTS (SELECT 1 FROM `transactions` tx WHERE tx.uid = u.id AND tx.dispute_status = 1)";
        break;
}

if ($date_condition !== '') {
    $conditions[] = str_replace('created_at', 'u.`created_at`', $date_condition);
}

$whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

try {
    // Count
    $countSql = "SELECT COUNT(DISTINCT u.id) FROM `users` u
        LEFT JOIN (SELECT uid, MAX(affid) as affid FROM `conversions` WHERE affid IS NOT NULL GROUP BY uid) c ON c.uid = u.id
        LEFT JOIN (SELECT id, aid, email FROM `affiliates`) a ON c.affid = a.id
        LEFT JOIN (SELECT cid, MAX(s1) as s1, MAX(s2) as s2 FROM `clicks` GROUP BY cid) cl ON CONVERT(u.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(cl.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci
        $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRows = (int)$countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalRows / $perPage));

    $sql = "
        SELECT u.*,
               a.name as aff_name, a.email as aff_email, a.aid,
               t.dispute_status, t.dispute_amount,
               cl.s1 as sub1, cl.s2 as sub2,
               pt.ip_address, pt.device, pt.browser, pt.pay_country, pt.user_agent
        FROM `users` u
        LEFT JOIN (
            SELECT uid, MAX(affid) as affid
            FROM `conversions` WHERE affid IS NOT NULL
            GROUP BY uid
        ) c ON c.uid = u.id
        LEFT JOIN (
            SELECT id, name, email, aid FROM `affiliates`
        ) a ON c.affid = a.id
        LEFT JOIN (
            SELECT uid, MAX(CASE WHEN dispute_status = 1 THEN 1 ELSE 0 END) as dispute_status,
                   MAX(COALESCE(dispute_amount, 0)) as dispute_amount
            FROM `transactions`
            GROUP BY uid
        ) t ON t.uid = u.id
        LEFT JOIN (
            SELECT t1.uid, t1.ip_address, t1.device, t1.browser, t1.country AS pay_country, t1.user_agent
            FROM `transactions` t1
            INNER JOIN (
                SELECT uid, MAX(id) AS max_id FROM `transactions` GROUP BY uid
            ) t2 ON t1.id = t2.max_id
        ) pt ON pt.uid = u.id
        LEFT JOIN (
            SELECT cid, MAX(s1) as s1, MAX(s2) as s2 FROM `clicks` GROUP BY cid
        ) cl ON CONVERT(u.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(cl.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci
        $whereClause
        ORDER BY u.created_at DESC
        LIMIT $perPage OFFSET $offset
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clients = $stmt->fetchAll();

    // Summary counts
    $dateCondAgg = $date_condition !== '' ? ' AND ' . str_replace('created_at', 'u.`created_at`', $date_condition) : '';
    $totalActive = (int)$pdo->query("SELECT COUNT(*) FROM `users` u WHERE u.stripe_subscription_id IS NOT NULL AND u.stripe_subscription_id != ''" . $dateCondAgg)->fetchColumn();
    $totalNoSub = (int)$pdo->query("SELECT COUNT(*) FROM `users` u WHERE (u.plan IS NULL OR u.plan = '' OR u.plan = 'FREE TIER') AND u.status != 'inactive'" . $dateCondAgg)->fetchColumn();
    $totalCancelled = (int)$pdo->query("SELECT COUNT(*) FROM `users` u WHERE u.status = 'inactive' AND NOT EXISTS (SELECT 1 FROM `transactions` tx WHERE tx.uid = u.id AND tx.dispute_status = 1)" . $dateCondAgg)->fetchColumn();
    $totalChargeback = (int)$pdo->query("SELECT COUNT(DISTINCT u.id) FROM `users` u INNER JOIN `transactions` tx ON tx.uid = u.id WHERE tx.dispute_status = 1" . $dateCondAgg)->fetchColumn();
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM `users` u WHERE 1=1" . $dateCondAgg)->fetchColumn();

} catch (PDOException $e) {
    error_log("Admin Clients Error: " . $e->getMessage());
    die("Error: " . $e->getMessage());
}

// Handle new customer creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_action'] ?? '') === 'add_customer') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $country = trim($_POST['country'] ?? '');
    $affId = (int)($_POST['affiliate_id'] ?? 0);

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['flash_error'] = "Name, email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_error'] = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $_SESSION['flash_error'] = "Password must be at least 6 characters.";
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM `users` WHERE `email` = ? LIMIT 1");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $_SESSION['flash_error'] = "Email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO `users` (`name`, `email`, `password`, `country`, `status`, `credit`, `created_at`) VALUES (?, ?, ?, ?, 'active', 0, NOW())")
                ->execute([$name, $email, $hashed, $country]);
            $newUserId = $pdo->lastInsertId();

            if ($affId) {
                $affCheck = $pdo->prepare("SELECT id FROM `affiliates` WHERE `id` = ? LIMIT 1");
                $affCheck->execute([$affId]);
                if ($affCheck->fetch()) {
                    $pdo->prepare("INSERT INTO `conversions` (`uid`, `affid`, `plan`, `price`, `payout`, `note`, `fire_postback`, `created_at`) VALUES (?, ?, 'admin_added', 0.00, 0.00, 'Added by admin', 0, NOW())")
                        ->execute([$newUserId, $affId]);
                }
            }

            $_SESSION['flash_success'] = "Customer '$name' created successfully. ID: #$newUserId";
        }
    }
    header("Location: admin-clients?sub=" . urlencode($subFilter) . "&date_range=" . urlencode($filter_date));
    exit;
}

// Handle force chargeback (Cancelled tab -> Chargeback tab, local DB only — no Stripe API call)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form_action'] ?? '') === 'force_chargeback') {
    $fuserId = (int)($_POST['user_id'] ?? 0);

    if ($fuserId <= 0) {
        $_SESSION['flash_error'] = "Invalid user ID for force chargeback.";
        header("Location: admin-clients?sub=cancelled&date_range=" . urlencode($filter_date));
        exit;
    }

    try {
        $fuStmt = $pdo->prepare("SELECT `id`, `cid`, `status` FROM `users` WHERE `id` = ? LIMIT 1");
        $fuStmt->execute([$fuserId]);
        $fuser = $fuStmt->fetch(PDO::FETCH_ASSOC);

        if (!$fuser) {
            $_SESSION['flash_error'] = "User not found.";
            header("Location: admin-clients?sub=cancelled&date_range=" . urlencode($filter_date));
            exit;
        }

        $dupCheck = $pdo->prepare("SELECT COUNT(*) FROM `transactions` WHERE `uid` = ? AND `dispute_status` = 1");
        $dupCheck->execute([$fuserId]);
        if ((int)$dupCheck->fetchColumn() > 0) {
            $_SESSION['flash_error'] = "Customer #$fuserId is already marked as chargeback. No action taken.";
            header("Location: admin-clients?sub=cancelled&date_range=" . urlencode($filter_date));
            exit;
        }

        $pdo->beginTransaction();

        // 1. Resolve affiliate attribution + the actual registration bonus recorded at signup
        $affId = null;
        $affCid = null;
        $bonusDeduction = 0.00;
        $planName = null;
        $convTid = null;
        $txRow = null;

        $convStmt = $pdo->prepare("SELECT `affid`, `payout`, `tid`, `plan`, `cid` FROM `conversions` WHERE `uid` = ? AND `affid` IS NOT NULL ORDER BY `created_at` ASC, `id` ASC LIMIT 1");
        $convStmt->execute([$fuserId]);
        $conv = $convStmt->fetch(PDO::FETCH_ASSOC);

        if ($conv) {
            $affId = (int)$conv['affid'];
            $affCid = !empty($conv['cid']) ? $conv['cid'] : (!empty($fuser['cid']) ? $fuser['cid'] : null);
            $bonusDeduction = (float)$conv['payout'];
            $planName = $conv['plan'];
            $convTid = $conv['tid'];
        } elseif (!empty($fuser['cid'])) {
            $clickStmt = $pdo->prepare("SELECT `affid` FROM `clicks` WHERE `cid` = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci LIMIT 1");
            $clickStmt->execute([$fuser['cid']]);
            $clickRow = $clickStmt->fetch(PDO::FETCH_ASSOC);
            if ($clickRow) {
                $affId = (int)$clickRow['affid'];
                $affCid = $fuser['cid'];
            }
        }

        if ($affId && $bonusDeduction <= 0) {
            $bonusDeduction = getAffiliateBonusAmount($pdo, $affId);
        }

        // 2. Resolve the initial purchase transaction (prefer the one tied to the registration conversion)
        if (!empty($convTid)) {
            $txStmt = $pdo->prepare("SELECT `id`, `tid`, `plan`, `cid`, `status` FROM `transactions` WHERE `uid` = ? AND `tid` = ? AND `dispute_status` != 1 LIMIT 1");
            $txStmt->execute([$fuserId, $convTid]);
            $txRow = $txStmt->fetch(PDO::FETCH_ASSOC);
        }
        if (!$txRow) {
            $txStmt2 = $pdo->prepare("SELECT `id`, `tid`, `plan`, `cid`, `status` FROM `transactions` WHERE `uid` = ? AND `dispute_status` != 1 AND `status` = 'succeeded' ORDER BY `created_at` ASC, `id` ASC LIMIT 1");
            $txStmt2->execute([$fuserId]);
            $txRow = $txStmt2->fetch(PDO::FETCH_ASSOC);
        }

        if ($txRow) {
            if (empty($planName)) $planName = $txRow['plan'];
            if (empty($affCid)) $affCid = $txRow['cid'];

            $disputeAmount = 0.00;
            if (!empty($txRow['plan'])) {
                $pStmt = $pdo->prepare("SELECT `price` FROM `plans` WHERE `name` = ? LIMIT 1");
                $pStmt->execute([$txRow['plan']]);
                $planPrice = $pStmt->fetchColumn();
                if ($planPrice !== false && $planPrice !== null) {
                    $disputeAmount = (float)$planPrice;
                }
            }

            // Flag as chargeback while keeping status 'succeeded' so the client still sees a normal (cancelled) view
            $pdo->prepare("UPDATE `transactions` SET `dispute_status` = 1, `dispute_reason` = 'force_chargeback', `dispute_amount` = ? WHERE `id` = ?")
                ->execute([$disputeAmount, (int)$txRow['id']]);
        }

        // 3. Keep the account inactive (already cancelled)
        $pdo->prepare("UPDATE `users` SET `status` = 'inactive' WHERE `id` = ?")->execute([$fuserId]);

        // 4. Claw back the affiliate registration bonus locally (never touches Stripe)
        if ($affId && $bonusDeduction > 0) {
            $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` - ? WHERE `id` = ?")->execute([$bonusDeduction, $affId]);

            $fcTid = 'FCB-' . strtoupper(bin2hex(random_bytes(6)));
            $pdo->prepare("INSERT INTO `conversions` (`tid`, `cid`, `uid`, `affid`, `plan`, `price`, `payout`, `note`, `fire_postback`, `created_at`) VALUES (?, ?, ?, ?, ?, 0.00, ?, 'Force Chargeback — Commission Deducted', 0, NOW())")
                ->execute([$fcTid, $affCid, $fuserId, $affId, $planName, -$bonusDeduction]);
        }

        $pdo->commit();

        $fmsg = "Customer #$fuserId successfully moved to Chargeback.";
        if ($affId && $bonusDeduction > 0) {
            $fmsg .= " Affiliate bonus of $" . number_format($bonusDeduction, 2) . " deducted from their balance.";
        } elseif (!$affId) {
            $fmsg .= " No affiliate linked — no commission deducted.";
        }
        $_SESSION['flash_success'] = $fmsg;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log("Force Chargeback Error: " . $e->getMessage());
        $_SESSION['flash_error'] = "Force chargeback failed: " . $e->getMessage();
    }

    header("Location: admin-clients?sub=cancelled&date_range=" . urlencode($filter_date));
    exit;
}

function buildClientQs($overrides) {
    $q = array_merge($_GET, $overrides);
    return http_build_query($q);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customers — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Customer Management</h1>
                    <p class="text-xs text-gray-400">View all customers acquired through affiliates.</p>
                </div>
                <button onclick="document.getElementById('addCustModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 text-[11px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-plus text-[10px]"></i> New Customer
                </button>
            </div>

            <!-- Search + Status Filter Row -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <form method="GET" class="flex items-center gap-2 flex-1 max-w-2xl">
                    <input type="hidden" name="sub" value="<?= htmlspecialchars($subFilter) ?>">
                    <input type="hidden" name="date_range" value="<?= htmlspecialchars($filter_date) ?>">
                    <div class="flex-1 relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by email, name, user ID, affiliate ID, affiliate email or SubID..."
                            class="w-full text-sm pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 px-5 rounded-xl transition-all cursor-pointer">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="admin-clients?sub=<?= $subFilter ?>&date_range=<?= urlencode($filter_date) ?>" class="text-xs font-bold text-gray-500 hover:text-gray-900 px-2">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="flex flex-wrap items-center gap-2 lg:ml-auto">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Status:</span>
                    <?php foreach (['all'=>'All','active_sub'=>'Active Sub','no_sub'=>'No Sub','cancelled'=>'Cancelled','chargeback'=>'Chargeback'] as $key => $label): ?>
                        <?php $cnt = ($key === 'all') ? $totalUsers : (($key === 'active_sub') ? $totalActive : (($key === 'no_sub') ? $totalNoSub : (($key === 'cancelled') ? $totalCancelled : $totalChargeback))); ?>
                        <a href="?<?= buildClientQs(['sub' => $key, 'page' => 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg transition-all <?= $subFilter === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"><?= $label ?> (<?= number_format($cnt) ?>)</a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Date:</span>
                <?php $dateLabels = ['lifetime' => 'Lifetime', 'today' => 'Today', 'yesterday' => 'Yesterday', 'this_week' => 'This Week', 'this_month' => 'This Month', 'last_month' => 'Last Month', 'this_year' => 'This Year']; ?>
                <?php foreach ($dateLabels as $dk => $dl): ?>
                    <a href="?<?= buildClientQs(['date_range' => $dk, 'page' => 1]) ?>" class="text-[11px] font-bold px-3.5 py-1.5 rounded-lg transition-all cursor-pointer <?= $filter_date === $dk ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"><?= $dl ?></a>
                <?php endforeach; ?>
            </div>

            <div class="text-[11px] font-bold text-gray-400">Showing <?= number_format($totalRows) ?> customers</div>

            <!-- Clients Table -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">ID</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Name</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Email</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Affiliate</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">SubID 1</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">SubID 2</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Plan</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Pay Browser</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Pay IP</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Pay Country</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Pay Device</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Pay UA</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Subscription</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Joined</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($clients)): ?>
                                <tr><td colspan="15" class="text-xs text-gray-400 py-8 text-center font-semibold">No customers found.</td></tr>
                            <?php else: foreach ($clients as $c): ?>
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-3 text-xs font-mono text-gray-500">#<?= str_pad($c['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                    <td class="px-5 py-3 text-xs font-bold text-gray-900"><?= htmlspecialchars($c['name'] ?? 'N/A') ?></td>
                                    <td class="px-5 py-3 text-[11px] font-semibold text-gray-600 font-mono"><?= htmlspecialchars($c['email']) ?></td>
                                    <td class="px-5 py-3">
                                        <?php if ($c['aff_name']): ?>
                                            <div class="text-xs font-semibold text-gray-700"><?= htmlspecialchars($c['aff_name']) ?></div>
                                            <div class="text-[10px] text-indigo-600 font-mono"><?= htmlspecialchars($c['aid'] ?? '') ?></div>
                                        <?php else: ?>
                                            <span class="text-[10px] text-gray-400 font-semibold">Direct</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-[10px] font-mono text-gray-500 truncate max-w-[120px]" title="<?= htmlspecialchars($c['sub1'] ?? '') ?>"><?= !empty($c['sub1']) ? htmlspecialchars($c['sub1']) : '—' ?></td>
                                    <td class="px-5 py-3 text-[10px] font-mono text-gray-500 truncate max-w-[120px]" title="<?= htmlspecialchars($c['sub2'] ?? '') ?>"><?= !empty($c['sub2']) ? htmlspecialchars($c['sub2']) : '—' ?></td>
                                    <td class="px-5 py-3 text-xs font-semibold text-gray-700"><?= htmlspecialchars($c['plan'] ?? '—') ?></td>
                                    <td class="px-5 py-3 text-[11px] font-semibold text-gray-600"><?= !empty($c['browser']) ? htmlspecialchars($c['browser']) : '—' ?></td>
                                    <td class="px-5 py-3 text-[11px] font-mono text-gray-500 select-all whitespace-nowrap"><?= !empty($c['ip_address']) ? htmlspecialchars($c['ip_address']) : '—' ?></td>
                                    <td class="px-5 py-3 text-[11px] font-bold text-gray-600 uppercase"><?= !empty($c['pay_country']) ? htmlspecialchars($c['pay_country']) : '—' ?></td>
                                    <td class="px-5 py-3">
                                        <?php if (!empty($c['device'])): ?>
                                            <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded <?= $c['device'] === 'Mobile' ? 'bg-amber-50 text-amber-700 border border-amber-100' : ($c['device'] === 'Tablet' ? 'bg-sky-50 text-sky-700 border border-sky-100' : 'bg-slate-100 text-slate-600 border border-slate-200') ?>">
                                                <?= htmlspecialchars($c['device']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-[11px] text-gray-400">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-[11px] font-mono text-gray-500 max-w-[220px] truncate select-all" title="<?= htmlspecialchars($c['user_agent'] ?? '') ?>"><?= !empty($c['user_agent']) ? htmlspecialchars($c['user_agent']) : '—' ?></td>
                                    <td class="px-5 py-3">
                                        <?php if ($c['dispute_status'] == 1): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md">Chargeback</span>
                                        <?php elseif (!empty($c['stripe_subscription_id'])): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Active</span>
                                        <?php elseif (($c['status'] ?? '') === 'inactive'): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-md">Cancelled</span>
                                        <?php elseif (!empty($c['plan'])): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-md">No Sub</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-gray-50 border border-gray-100 text-gray-600 px-2 py-0.5 rounded-md">Free</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                                    <td class="px-5 py-3">
                                        <div class="relative">
                                            <button onclick="toggleDropdown(this)" class="inline-flex items-center gap-1 text-[10px] font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 px-2.5 py-1 rounded-md transition cursor-pointer">
                                                <i class="fa-solid fa-ellipsis"></i> More
                                            </button>
                                            <div class="hidden absolute right-0 z-50 mt-1 w-48 bg-white border border-gray-200 rounded-xl shadow-lg py-1.5 origin-top-right dropdown-menu">
                                                <a href="admin-client-detail?id=<?= $c['id'] ?>" class="flex items-center gap-2 px-3.5 py-2 text-[11px] font-semibold text-gray-700 hover:bg-gray-50 transition">
                                                    <i class="fa-solid fa-eye text-[10px] text-blue-500"></i> View
                                                </a>
                                                <a href="admin-client-edit?id=<?= $c['id'] ?>" class="flex items-center gap-2 px-3.5 py-2 text-[11px] font-semibold text-gray-700 hover:bg-gray-50 transition">
                                                    <i class="fa-solid fa-pen text-[10px] text-amber-500"></i> Edit
                                                </a>
                                                <?php if ($subFilter === 'cancelled'): ?>
                                                <form method="POST" onsubmit="return confirm('Force chargeback for customer #<?= (int)$c['id'] ?>? The affiliate bonus will be deducted from the affiliate\'s balance. This cannot be undone.');">
                                                    <input type="hidden" name="form_action" value="force_chargeback">
                                                    <input type="hidden" name="user_id" value="<?= (int)$c['id'] ?>">
                                                    <button type="submit" class="w-full flex items-center gap-2 px-3.5 py-2 text-[11px] font-semibold text-red-600 hover:bg-red-50 transition cursor-pointer">
                                                        <i class="fa-solid fa-ban text-[10px] text-red-500"></i> Force to Chargeback
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                    <div class="text-[11px] font-semibold text-gray-400">Page <?= $page ?> of <?= number_format($totalPages) ?></div>
                    <div class="flex items-center gap-1.5">
                        <?php if ($page > 1): ?>
                            <a href="?<?= buildClientQs(['page' => 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">First</a>
                            <a href="?<?= buildClientQs(['page' => $page - 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Prev</a>
                        <?php endif; ?>
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?<?= buildClientQs(['page' => $i]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg transition <?= $i === $page ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= buildClientQs(['page' => $page + 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Next</a>
                            <a href="?<?= buildClientQs(['page' => $totalPages]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Last</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

        </main>
    </div>

    <?php
    $alert_type = !empty($success_msg) ? (strpos($success_msg, 'success') !== false || strpos($success_msg, 'created') !== false ? 'success' : 'error') : '';
    $alert_message = $success_msg;
    ?>
    <?php include 'alert-modal.php'; ?>

    <!-- Add New Customer Modal -->
    <div id="addCustModal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-indigo-600"></i> Add New Customer
                </h3>
                <button onclick="document.getElementById('addCustModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-700 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="form_action" value="add_customer">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Full Name *</label>
                        <input type="text" name="name" required placeholder="John Doe"
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Email Address *</label>
                        <input type="email" name="email" required placeholder="user@domain.com"
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Password *</label>
                        <input type="password" name="password" required minlength="6" placeholder="Minimum 6 characters"
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Country</label>
                        <input type="text" name="country" placeholder="US, BD, IN..."
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Assign to Affiliate (optional)</label>
                    <select name="affiliate_id" class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 cursor-pointer">
                        <option value="0">— Direct (No Affiliate) —</option>
                        <?php
                        $affList = $pdo->query("SELECT id, name, aid, email FROM `affiliates` WHERE `status` = 'active' ORDER BY `name` ASC")->fetchAll();
                        foreach ($affList as $af):
                        ?>
                        <option value="<?= $af['id'] ?>"><?= htmlspecialchars($af['name']) ?> (<?= htmlspecialchars($af['aid']) ?>) — <?= htmlspecialchars($af['email']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Create Customer
                    </button>
                    <button type="button" onclick="document.getElementById('addCustModal').classList.add('hidden')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function toggleDropdown(btn) {
        var menu = btn.nextElementSibling;
        var wasOpen = !menu.classList.contains('hidden');
        document.querySelectorAll('.dropdown-menu').forEach(function(m) { m.classList.add('hidden'); m.style.position = ''; m.style.left = ''; m.style.top = ''; });
        if (!wasOpen) {
            var rect = btn.getBoundingClientRect();
            menu.classList.remove('hidden');
            menu.style.position = 'fixed';
            menu.style.left = (rect.right - menu.offsetWidth) + 'px';
            menu.style.top = (rect.bottom + 4) + 'px';
        }
    }
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('.dropdown-menu').forEach(function(m) { m.classList.add('hidden'); m.style.position = ''; m.style.left = ''; m.style.top = ''; });
        }
    });
    </script>

</body>
</html>