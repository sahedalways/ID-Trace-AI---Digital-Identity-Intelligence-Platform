<?php
/**
 * File: affiliate-clients.php
 * Managed Client Registry Matrix for Affiliate Partners.
 * Filter referred client logs strictly by explicit column configurations.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Enforce strict authentication limits
if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login");
    exit;
}

$affiliateId = (int)$_SESSION['affiliate_id'];

// Fetch affiliate bonus type
$bonusType = getAffiliateBonusType($pdo, $affiliateId);

// 2. Capture and sanitize incoming status filters
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : 'all';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$current_date = date('Y-m-d');

// 3. Pre-fetch Dynamic Row Counters for Dropdown Select Options (First Bracket)
try {
    // Total referred accounts count
    $c_all = $pdo->prepare("SELECT COUNT(*) FROM `users` u INNER JOIN `clicks` c ON u.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci WHERE c.`affid` = ?");
    $c_all->execute([$affiliateId]);
    $count_all = (int)$c_all->fetchColumn();

    // Active subscription count
    $c_act = $pdo->prepare("SELECT COUNT(*) FROM `users` u INNER JOIN `clicks` c ON u.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci WHERE c.`affid` = ? AND u.`plan` IS NOT NULL AND u.`plan` != '' AND u.`plan` != 'FREE TIER'");
    $c_act->execute([$affiliateId]);
    $count_active = (int)$c_act->fetchColumn();

    // No subscription count
    $c_nos = $pdo->prepare("SELECT COUNT(*) FROM `users` u INNER JOIN `clicks` c ON u.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci WHERE c.`affid` = ? AND (u.`plan` IS NULL OR u.`plan` = '' OR u.`plan` = 'FREE TIER') AND u.`status` != 'inactive'");
    $c_nos->execute([$affiliateId]);
    $count_no_sub = (int)$c_nos->fetchColumn();

    // Cancelled subscription count (inactive status, no chargeback)
    $c_cancel = $pdo->prepare("SELECT COUNT(*) FROM `users` u INNER JOIN `clicks` c ON u.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci WHERE c.`affid` = ? AND u.`status` = 'inactive' AND NOT EXISTS (SELECT 1 FROM `transactions` t WHERE t.`uid` = u.`id` AND t.`dispute_status` = 1)");
    $c_cancel->execute([$affiliateId]);
    $count_cancelled = (int)$c_cancel->fetchColumn();

    // Chargeback subscription count (users with at least one chargeback transaction)
    $c_cb = $pdo->prepare("SELECT COUNT(DISTINCT u.`id`) FROM `users` u INNER JOIN `clicks` c ON u.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci INNER JOIN `transactions` t ON t.`uid` = u.`id` WHERE c.`affid` = ? AND t.`dispute_status` = 1");
    $c_cb->execute([$affiliateId]);
    $count_chargeback = (int)$c_cb->fetchColumn();
} catch (PDOException $e) {
    error_log("Affiliate Counter Fault: " . $e->getMessage());
    $count_all = $count_active = $count_no_sub = $count_cancelled = $count_chargeback = 0;
}

// 4. Construct clean SQL queries checking target status columns directly
$status_condition = "";
switch ($filter_status) {
    case 'active':
        $status_condition = "AND u.`plan` IS NOT NULL AND u.`plan` != '' AND u.`plan` != 'FREE TIER'";
        break;
    case 'no_sub':
        $status_condition = "AND (u.`plan` IS NULL OR u.`plan` = '' OR u.`plan` = 'FREE TIER') AND u.`status` != 'inactive'";
        break;
    case 'cancelled':
        $status_condition = "AND u.`status` = 'inactive' AND NOT EXISTS (SELECT 1 FROM `transactions` t WHERE t.`uid` = u.`id` AND t.`dispute_status` = 1)";
        break;
    case 'chargeback':
        $status_condition = "AND EXISTS (SELECT 1 FROM `transactions` t WHERE t.`uid` = u.`id` AND t.`dispute_status` = 1)";
        break;
    case 'all':
    default:
        $status_condition = "";
        break;
}

$client_data = [];

// 4b. Build dynamic search clause (email, name, user ID, Click ID or SubID 1/2)
$search_condition = "";
if (!empty($search)) {
    $search_condition = " AND (u.`email` LIKE ? OR u.`name` LIKE ? OR u.`id` = ? OR u.`cid` LIKE ? OR c.`s1` LIKE ? OR c.`s2` LIKE ?)";
}

try {
    // 5. DATA CORE PIPELINE — Fetch users matching conversion matrices
    $query = "SELECT 
                u.`id` AS usr_id,
                u.`name` AS usr_name, 
                u.`email` AS usr_email, 
                u.`country` AS usr_country, 
                u.`cid` AS usr_click_id, 
                c.`s1` AS usr_sub1,
                c.`s2` AS usr_sub2,
                u.`plan` AS usr_plan_name, 
                u.`credit` AS usr_credit, 
                u.`status` AS usr_status,
                u.`created_at` AS usr_joined_date,
                EXISTS(SELECT 1 FROM `transactions` t WHERE t.`uid` = u.`id` AND t.`dispute_status` = 1) AS has_chargeback,
                pt.`browser` AS pay_browser,
                pt.`ip_address` AS pay_ip,
                pt.`country` AS pay_country,
                pt.`device` AS pay_device,
                pt.`user_agent` AS pay_ua
              FROM `users` u
              INNER JOIN `clicks` c ON u.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci
              LEFT JOIN (
                  SELECT t1.uid, t1.browser, t1.ip_address, t1.country, t1.device, t1.user_agent
                  FROM `transactions` t1
                  INNER JOIN (
                      SELECT uid, MAX(id) AS max_id FROM `transactions` GROUP BY uid
                  ) t2 ON t1.id = t2.max_id
              ) pt ON pt.uid = u.`id`
              WHERE c.`affid` = ? $status_condition $search_condition
              ORDER BY u.`created_at` DESC";

    $params = [$affiliateId];
    if (!empty($search)) {
        array_push($params, "%$search%", "%$search%", $search, "%$search%", "%$search%", "%$search%");
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $has_active_plan = (!empty($row['usr_plan_name']) && strtoupper($row['usr_plan_name']) !== 'FREE TIER');

        // Clean plan identity logic (Hyphen instead of Free Plan text labels)
        $display_plan = '—';
        if (!empty($row['usr_plan_name']) && strtoupper($row['usr_plan_name']) !== 'FREE TIER') {
            $display_plan = strtoupper($row['usr_plan_name']);
        }

        $client_data[] = [
            'id'          => (int)$row['usr_id'],
            'name'        => !empty($row['usr_name']) ? $row['usr_name'] : 'Registered Customer',
            'email'       => $row['usr_email'],
            'country'     => !empty($row['usr_country']) ? strtoupper($row['usr_country']) : '—',
            'click_id'    => !empty($row['usr_click_id']) ? $row['usr_click_id'] : '—',
            'sub1'        => !empty($row['usr_sub1']) ? $row['usr_sub1'] : '—',
            'sub2'        => !empty($row['usr_sub2']) ? $row['usr_sub2'] : '—',
            'plan_name'   => $display_plan,
            'credit'      => (int)$row['usr_credit'],
            'joined_date' => !empty($row['usr_joined_date']) ? date('Y-m-d', strtotime($row['usr_joined_date'])) : '—',
            'is_active'   => $has_active_plan,
            'is_chargeback' => (int)$row['has_chargeback'] === 1,
            'is_cancelled'  => (!$has_active_plan && (int)$row['has_chargeback'] === 0 && ($row['usr_status'] ?? '') === 'inactive'),
            'pay_browser' => !empty($row['pay_browser']) ? $row['pay_browser'] : '—',
            'pay_ip'      => !empty($row['pay_ip']) ? $row['pay_ip'] : '—',
            'pay_country' => !empty($row['pay_country']) ? strtoupper($row['pay_country']) : '—',
            'pay_device'  => !empty($row['pay_device']) ? $row['pay_device'] : '—',
            'pay_ua'      => !empty($row['pay_ua']) ? $row['pay_ua'] : '—'
        ];
    }

} catch (PDOException $e) {
    error_log("Affiliate Clients Management Matrix Error: " . $e->getMessage());
    die("An error occurred loading the client list matrix panel.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Referred Client Registry — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="max-w-[1650px] w-full mx-auto px-4 sm:px-6 pt-8 pb-16 grow space-y-6">
        
        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <form method="GET" action="affiliate-clients" class="flex flex-col lg:flex-row gap-4 items-end justify-between w-full">
                <div class="flex flex-col sm:flex-row gap-4 items-end w-full lg:max-w-3xl">
                    <div class="space-y-1.5 w-full sm:max-w-[220px] text-left">
                        <label class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Subscription Status</label>
                        <select name="status" class="w-full bg-slate-50 border border-gray-200 text-sm rounded-xl px-3 py-2.5 font-semibold text-slate-700 outline-none focus:border-indigo-500 transition-all">
                            <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>All Clients (<?= $count_all ?>)</option>
                            <option value="active" <?= $filter_status === 'active' ? 'selected' : '' ?>>Active Subscription (<?= $count_active ?>)</option>
                            <option value="no_sub" <?= $filter_status === 'no_sub' ? 'selected' : '' ?>>No Subscription (<?= $count_no_sub ?>)</option>
                            <option value="cancelled" <?= $filter_status === 'cancelled' ? 'selected' : '' ?>>Cancelled Subscription (<?= $count_cancelled ?>)</option>
                            <option value="chargeback" <?= $filter_status === 'chargeback' ? 'selected' : '' ?>>Chargeback Subscription (<?= $count_chargeback ?>)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5 w-full sm:max-w-sm text-left">
                        <label class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Search Clients</label>
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, email, ID, Click ID or SubID..."
                                class="w-full text-sm pl-10 pr-4 py-2.5 bg-slate-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition-all font-semibold text-slate-700 placeholder-gray-400">
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i> Search Clients
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="affiliate-clients?status=<?= urlencode($filter_status) ?>" class="text-xs font-bold text-gray-500 hover:text-gray-900 px-2 whitespace-nowrap">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-slate-50/40 flex justify-between items-center">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Client Registry</h3>
                <span class="text-xs font-mono text-slate-400">Total Records: <?= count($client_data) ?> profiles</span>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-slate-50/20">
                            <th class="px-6 py-3.5 whitespace-nowrap">Name</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Status</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Email</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Country</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Click ID</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">SubID 1</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">SubID 2</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Plan Name</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Pay Browser</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Pay IP</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Pay Country</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Pay Device</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Pay UA</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Credit</th>
                            <th class="px-6 py-3.5 whitespace-nowrap">Joined Date</th>
                            <th class="px-6 py-3.5 text-right whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium text-slate-700">
                        <?php if (empty($client_data)): ?>
                        <tr>
                            <td colspan="16" class="px-6 py-12 text-center text-gray-400 font-semibold font-mono">
                                <i class="fa-solid fa-users-slash text-2xl block mb-2 text-slate-300"></i>
                                No customer accounts found matching selected criteria.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($client_data as $client): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">
                                    <?= htmlspecialchars($client['name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($client['is_chargeback']): ?>
                                        <span class="inline-flex items-center gap-1 bg-red-50 text-red-700 font-bold px-2.5 py-0.5 rounded text-[10px] border border-red-100">
                                            Chargeback
                                        </span>
                                    <?php elseif ($client['is_cancelled']): ?>
                                        <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 font-bold px-2.5 py-0.5 rounded text-[10px] border border-amber-100">
                                            Cancelled
                                        </span>
                                    <?php elseif ($client['is_active']): ?>
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 font-bold px-2.5 py-0.5 rounded text-[10px] border border-emerald-100">
                                            Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-500 font-bold px-2.5 py-0.5 rounded text-[10px] border border-slate-200">
                                            No Subscription
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-slate-600 select-all whitespace-nowrap">
                                    <?= htmlspecialchars($client['email']) ?>
                                </td>
                                <td class="px-6 py-4 font-bold uppercase text-gray-500 whitespace-nowrap">
                                    <?= htmlspecialchars($client['country']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-gray-400 text-xs whitespace-nowrap select-all">
                                    <?= htmlspecialchars($client['click_id']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-gray-500 text-xs whitespace-nowrap select-all">
                                    <?= htmlspecialchars($client['sub1']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-gray-500 text-xs whitespace-nowrap select-all">
                                    <?= htmlspecialchars($client['sub2']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-bold text-gray-700 whitespace-nowrap">
                                    <?= htmlspecialchars($client['plan_name']) ?>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-600 whitespace-nowrap">
                                    <?= htmlspecialchars($client['pay_browser']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-500 whitespace-nowrap select-all">
                                    <?= htmlspecialchars($client['pay_ip']) ?>
                                </td>
                                <td class="px-6 py-4 font-bold uppercase text-gray-500 whitespace-nowrap">
                                    <?= htmlspecialchars($client['pay_country']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($client['pay_device'] !== '—'): ?>
                                        <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded <?= $client['pay_device'] === 'Mobile' ? 'bg-amber-50 text-amber-700 border border-amber-100' : ($client['pay_device'] === 'Tablet' ? 'bg-sky-50 text-sky-700 border border-sky-100' : 'bg-slate-100 text-slate-600 border border-slate-200') ?>">
                                            <?= htmlspecialchars($client['pay_device']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-500 max-w-[220px] truncate select-all" title="<?= htmlspecialchars($client['pay_ua']) ?>">
                                    <?= htmlspecialchars($client['pay_ua']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                    <?= number_format($client['credit']) ?>
                                </td>
                                <td class="px-6 py-4 font-mono text-slate-500 whitespace-nowrap">
                                    <?= htmlspecialchars($client['joined_date']) ?>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <?php if ($bonusType === 'fixed'): ?>
                                        <span class="text-xs font-bold text-gray-400 bg-gray-100 px-3 py-1.5 rounded-xl inline-flex items-center gap-1 cursor-not-allowed">
                                            Restricted
                                        </span>
                                    <?php else: ?>
                                        <a href="client-info?id=<?= $client['id'] ?>" class="text-xs font-bold text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-xl transition-all inline-flex items-center gap-1">
                                            View Details
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        <div class="flex items-center justify-center gap-2 mb-2">
            <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
        </div>
        &copy; <?= date('Y'); ?> Identity Search AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>