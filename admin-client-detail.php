<?php
/**
 * File: admin-client-detail.php
 * Admin individual customer detail page — mirrors the affiliate client-info UI.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login");
    exit;
}

$clientId = (int)($_GET['id'] ?? 0);
if (!$clientId) {
    header("Location: admin-clients");
    exit;
}

try {
    // 1. FETCH GENERAL CORE INFO (Users + clicks mapping for SubIDs)
    $u_query = "SELECT 
                    u.`id` AS usr_id, u.`name` AS usr_name, u.`email` AS usr_email, 
                    u.`country` AS usr_country, u.`street`, u.`zip`, u.`credit`, 
                    u.`plan` AS plan_name, u.`validity` AS plan_validity, u.`status` AS usr_status,
                    u.`stripe_subscription_id` AS sub_id, u.`created_at` AS joined_date,
                    u.`cid` AS click_id, c.`s1` AS sub1, c.`s2` AS sub2
                FROM `users` u
                LEFT JOIN `clicks` c ON CONVERT(u.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci
                WHERE u.`id` = ? LIMIT 1";
    $u_stmt = $pdo->prepare($u_query);
    $u_stmt->execute([$clientId]);
    $client = $u_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        header("Location: admin-clients");
        exit;
    }

    // 2. FETCH PAYOUT LOGS (conversions + recurring) WITH DISPUTE VERIFICATION
    $payouts = [];
    $total_earned = 0.00;

    $conv_query = "SELECT c.`tid`, c.`created_at`, c.`plan`, c.`payout`,
                          COALESCE((
                              SELECT t.`dispute_status` FROM `transactions` t 
                              WHERE CONVERT(t.`stripe_invoice_id` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(conv.`stripe_invoice_id` USING utf8mb4) COLLATE utf8mb4_unicode_ci 
                              OR CONVERT(t.`tid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(c.`tid` USING utf8mb4) COLLATE utf8mb4_unicode_ci 
                              LIMIT 1
                          ), 0) AS is_disputed
                   FROM `conversions` c
                   LEFT JOIN `transactions` conv ON CONVERT(c.`tid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(conv.`tid` USING utf8mb4) COLLATE utf8mb4_unicode_ci
                   WHERE c.`uid` = ?
                   ORDER BY c.`created_at` DESC";
    $conv_stmt = $pdo->prepare($conv_query);
    $conv_stmt->execute([$clientId]);
    while ($row = $conv_stmt->fetch(PDO::FETCH_ASSOC)) {
        $payout_amt = (float)$row['payout'];
        $is_disputed = (int)$row['is_disputed'] === 1;

        if (!$is_disputed) {
            $total_earned += $payout_amt;
        }

        $payouts[] = [
            'tx_id'       => !empty($row['tid']) ? $row['tid'] : '—',
            'date'        => $row['created_at'],
            'plan'        => strtoupper($row['plan']),
            'type'        => 'Conversion',
            'payout'      => $payout_amt,
            'is_disputed' => $is_disputed
        ];
    }

    $rec_query = "SELECT r.`tid`, r.`created_at`, r.`plan`, r.`payout`,
                         COALESCE((
                             SELECT t.`dispute_status` FROM `transactions` t 
                             WHERE CONVERT(t.`tid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(r.`tid` USING utf8mb4) COLLATE utf8mb4_unicode_ci 
                             LIMIT 1
                         ), 0) AS is_disputed
                  FROM `recurring` r
                  WHERE r.`uid` = ?
                  ORDER BY r.`created_at` DESC";
    $rec_stmt = $pdo->prepare($rec_query);
    $rec_stmt->execute([$clientId]);
    while ($row = $rec_stmt->fetch(PDO::FETCH_ASSOC)) {
        $payout_amt = (float)$row['payout'];
        $is_disputed = (int)$row['is_disputed'] === 1;

        if (!$is_disputed) {
            $total_earned += $payout_amt;
        }

        $payouts[] = [
            'tx_id'       => !empty($row['tid']) ? $row['tid'] : '—',
            'date'        => $row['created_at'],
            'plan'        => strtoupper($row['plan']),
            'type'        => 'Renewal',
            'payout'      => $payout_amt,
            'is_disputed' => $is_disputed
        ];
    }

    usort($payouts, function($a, $b) {
        return strcmp($b['date'], $a['date']);
    });

    // 3. FETCH GENERATED REPORT LOGS
    $reports = [];
    $rep_query = "SELECT 
                    r.`vid`, 
                    COALESCE(v.`name`, 'Unknown Target Identity') AS rep_name, 
                    v.`avatar`, 
                    v.`source` 
                  FROM `reports` r
                  LEFT JOIN `view` v ON CONVERT(r.`vid` USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(v.`vid` USING utf8mb4) COLLATE utf8mb4_unicode_ci
                  WHERE r.`uid` = ? 
                  ORDER BY r.`created_at` DESC";
    $rep_stmt = $pdo->prepare($rep_query);
    $rep_stmt->execute([$clientId]);
    $reports = $rep_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Admin Client Detail Error: " . $e->getMessage());
    die("Error loading customer details.");
}

// Clean fallback indicators for missing parameters
$display_name   = !empty($client['usr_name']) ? htmlspecialchars($client['usr_name']) : 'Registered Customer';
$display_email  = !empty($client['usr_email']) ? htmlspecialchars($client['usr_email']) : '—';
$display_country= !empty($client['usr_country']) ? strtoupper(htmlspecialchars($client['usr_country'])) : '—';
$display_plan   = (!empty($client['plan_name']) && strtoupper($client['plan_name']) !== 'FREE TIER') ? strtoupper(htmlspecialchars($client['plan_name'])) : 'FREE TIER';

$sub1_display = !empty($client['sub1']) ? htmlspecialchars($client['sub1']) : '—';
$sub2_display = !empty($client['sub2']) ? htmlspecialchars($client['sub2']) : '—';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Customer #<?= (int)$client['usr_id'] ?> — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6 max-w-6xl">

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="admin-clients" class="text-gray-400 hover:text-gray-900 transition">
                        <i class="fa-solid fa-arrow-left text-sm"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Customer #<?= (int)$client['usr_id'] ?></h1>
                        <p class="text-xs text-gray-400">Full account details, commission ledger and report history.</p>
                    </div>
                </div>
                <a href="admin-client-edit?id=<?= (int)$client['usr_id'] ?>" class="inline-flex items-center gap-1.5 text-[11px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-pen text-[10px]"></i> Edit Profile
                </a>
            </div>

            <!-- Profile Card -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-6">

                <div class="flex flex-col items-center text-center pb-2 border-b border-gray-100">
                    <div class="w-20 h-20 rounded-full border border-gray-200 bg-gray-50 p-1 overflow-hidden shadow-2xs">
                        <img src="https://api.dicebear.com/7.x/identicon/svg?seed=<?= urlencode($display_email) ?>" class="w-full h-full rounded-full object-cover" alt="User Profile Avatar">
                    </div>
                    <h1 class="text-lg font-extrabold text-gray-900 tracking-tight mt-3"><?= $display_name ?></h1>
                    <p class="text-xs font-semibold text-slate-400 font-mono tracking-wide mt-0.5"><?= $display_email ?></p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Name</p>
                        <p class="font-semibold text-slate-800 truncate"><?= $display_name ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Country</p>
                        <p class="font-semibold text-slate-800 uppercase"><?= $display_country ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Address</p>
                        <p class="font-semibold text-slate-700 truncate" title="<?= !empty($client['street']) ? htmlspecialchars($client['street']) : '—' ?>"><?= !empty($client['street']) ? htmlspecialchars($client['street']) : '—' ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">ZIP Code</p>
                        <p class="font-semibold font-mono text-slate-700"><?= !empty($client['zip']) ? htmlspecialchars($client['zip']) : '—' ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Credits Available</p>
                        <p class="font-bold font-mono text-slate-800"><?= number_format($client['credit']) ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Plan Name</p>
                        <p class="font-bold font-mono text-indigo-600 truncate"><?= $display_plan ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Plan Validity</p>
                        <p class="font-semibold font-mono text-slate-700 truncate"><?= !empty($client['plan_validity']) ? htmlspecialchars($client['plan_validity']) : '—' ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Status</p>
                        <span class="inline-block font-mono text-[10px] font-bold px-2 py-0.5 rounded <?= ($client['usr_status'] ?? '') === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-red-50 text-red-700 border border-red-100' ?>">
                            <?= ucfirst($client['usr_status'] ?? '—') ?>
                        </span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Subscription</p>
                        <?php if (!empty($client['sub_id'])): ?>
                            <span class="inline-block font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                        <?php else: ?>
                            <span class="inline-block font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">Inactive</span>
                        <?php endif; ?>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Click ID Token</p>
                        <p class="font-semibold font-mono text-slate-500 truncate select-all" title="<?= htmlspecialchars($client['click_id']) ?>"><?= htmlspecialchars($client['click_id']) ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">SubID 1</p>
                        <p class="font-semibold font-mono text-slate-700 truncate"><?= $sub1_display ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">SubID 2</p>
                        <p class="font-semibold font-mono text-slate-700 truncate"><?= $sub2_display ?></p>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50/40 border border-gray-100 space-y-1">
                        <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Joined Date</p>
                        <p class="font-semibold font-mono text-slate-600"><?= date('Y-m-d', strtotime($client['joined_date'])) ?></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-4">
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-3">
                        Commission Payout Streams Ledger
                    </h2>

                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-slate-50/20">
                                    <th class="px-4 py-3">Date Invoiced</th>
                                    <th class="px-4 py-3">TXID</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Plan</th>
                                    <th class="px-4 py-3 text-right">Commission</th>
                                    <th class="px-4 py-3 text-center">Chargedback</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs font-medium text-slate-700">
                                <?php if (empty($payouts)): ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-gray-400 font-mono font-bold">
                                        No transaction records tracked down to this customer node.
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($payouts as $p): ?>
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-4 py-3.5 font-mono text-slate-500 whitespace-nowrap"><?= date('Y-m-d H:i', strtotime($p['date'])) ?></td>
                                        <td class="px-4 py-3.5 font-mono text-slate-800 font-bold whitespace-nowrap"><?= htmlspecialchars($p['tx_id']) ?></td>

                                        <td class="px-4 py-3.5 whitespace-nowrap">
                                            <span class="inline-block font-mono text-[10px] font-bold px-2 py-0.5 rounded <?= $p['type'] === 'Conversion' ? 'bg-indigo-50 border border-indigo-100 text-indigo-700' : 'bg-emerald-50 border border-emerald-100 text-emerald-700' ?>">
                                                <?= $p['type'] ?>
                                            </span>
                                        </td>

                                        <td class="px-4 py-3.5 font-bold text-gray-600 whitespace-nowrap"><?= htmlspecialchars($p['plan']) ?></td>

                                        <td class="px-4 py-3.5 text-right font-mono font-black text-sm whitespace-nowrap <?= $p['is_disputed'] ? 'text-red-600' : 'text-emerald-600' ?>">
                                            <?= $p['is_disputed'] ? '-$' . number_format($p['payout'], 2) : '+$' . number_format($p['payout'], 2) ?>
                                        </td>

                                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                            <span class="font-mono font-bold text-[10px] px-2 py-0.5 rounded <?= $p['is_disputed'] ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-slate-100 text-slate-400 border border-slate-200' ?>">
                                                <?= $p['is_disputed'] ? 'Yes' : 'No' ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm text-left relative overflow-hidden flex flex-col justify-between min-h-[140px]">
                    <div class="flex justify-between items-start">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Earned Commission</span>
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm"><i class="fa-solid fa-sack-dollar"></i></div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-gray-900 tracking-tight font-mono">$<?= number_format($total_earned, 2) ?></div>
                        <p class="text-[10px] text-gray-400 mt-1">Net dynamic splits generated from upfront conversion values and downstream continuity payouts (minus disputes).</p>
                    </div>
                </div>

            </div>

            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm text-left space-y-5">
                <h2 class="text-sm font-black text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-3">
                    Target Resolution Logs Engine Generated
                </h2>

                <?php if (empty($reports)): ?>
                    <div class="text-center py-12 text-gray-400 font-semibold font-mono text-xs">
                        No target intelligence reports generated by this account profile container yet.
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($reports as $rep):
                            $cleanRepName = htmlspecialchars($rep['rep_name']);
                        ?>
                        <div class="border border-gray-200 rounded-xl p-4 flex items-center justify-between bg-slate-50/20 hover:border-indigo-400 transition-all group">
                            <div class="flex items-center gap-3 min-w-0">

                                <div class="w-9 h-9 rounded-lg overflow-hidden border bg-white flex items-center justify-center shrink-0 relative">
                                    <?php if (!empty($rep['avatar'])): ?>
                                        <img src="<?= htmlspecialchars($rep['avatar']) ?>"
                                             class="w-full h-full object-cover"
                                             alt="<?= $cleanRepName ?>"
                                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                    <?php endif; ?>
                                    <div class="absolute inset-0 w-full h-full bg-indigo-50 text-indigo-700 border border-indigo-100 flex items-center justify-center font-black uppercase text-xs <?= !empty($rep['avatar']) ? 'hidden' : '' ?>">
                                        <?= substr($cleanRepName, 0, 2) ?>
                                    </div>
                                </div>

                                <div class="min-w-0 text-left">
                                    <p class="text-xs font-extrabold text-gray-900 truncate group-hover:text-indigo-600 transition-colors" title="<?= $cleanRepName ?>"><?= $cleanRepName ?></p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase mt-0.5 tracking-wider flex items-center gap-1 font-mono">
                                        Node: <?= htmlspecialchars($rep['source'] ?? 'Unknown Source') ?>
                                    </p>
                                </div>
                            </div>

                            <a href="report?id=<?= urlencode($rep['vid']) ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-indigo-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-2xs shrink-0 cursor-pointer flex items-center justify-center" title="Open Target Profile">
                                Open
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

</body>
</html>
