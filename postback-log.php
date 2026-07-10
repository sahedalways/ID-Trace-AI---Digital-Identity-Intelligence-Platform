<?php
/**
 * File: postback-log.php
 * Real-Time S2S Postback Transmission Log Registry Panel.
 * Audits server-to-server outbound webhook pings and remote HTTP callback execution logs.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Enforce strict authentication limits
if (!isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-login.php");
    exit;
}

$affiliateId = (int)$_SESSION['affiliate_id'];

// 2. Capture and sanitize incoming transmission filter status metrics
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : 'all';

// 3. Pre-fetch Dynamic Row Counters for Dropdown Select Options (First Bracket)
try {
    // Total Postback Hooks Counter
    $c_all = $pdo->prepare("SELECT COUNT(*) FROM `conversions` conv INNER JOIN `clicks` c ON conv.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci WHERE c.`affid` = ?");
    $c_all->execute([$affiliateId]);
    $count_all = (int)$c_all->fetchColumn();

    // Successfully Fired Postbacks Counter
    $c_fired = $pdo->prepare("SELECT COUNT(*) FROM `conversions` conv INNER JOIN `clicks` c ON conv.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci WHERE c.`affid` = ? AND conv.`fire_postback` = 1");
    $c_fired->execute([$affiliateId]);
    $count_fired = (int)$c_fired->fetchColumn();

    // Skipped / Not Fired Postbacks Counter
    $c_skipped = $pdo->prepare("SELECT COUNT(*) FROM `conversions` conv INNER JOIN `clicks` c ON conv.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci WHERE c.`affid` = ? AND conv.`fire_postback` = 0");
    $c_skipped->execute([$affiliateId]);
    $count_skipped = (int)$c_skipped->fetchColumn();
} catch (PDOException $e) {
    error_log("Affiliate Postback Logs Counter Fault: " . $e->getMessage());
    $count_all = $count_fired = $count_skipped = 0;
}

// 4. Construct conditional SQL constraints based on dropdown state selection parameters
$status_condition = "";
switch ($filter_status) {
    case 'fired':
        $status_condition = "AND conv.`fire_postback` = 1";
        break;
    case 'not_fired':
        $status_condition = "AND conv.`fire_postback` = 0";
        break;
    case 'all':
    default:
        $status_condition = "";
        break;
}

$log_data = [];

try {
    // 5. UNIFIED DATA PIPELINE ENGINE — Join conversions with clicks to pull structural parameters safely
    $query = "SELECT 
                conv.`id` AS conv_id,
                conv.`tid` AS tx_id,
                conv.`created_at` AS log_date,
                conv.`cid` AS click_id,
                conv.`plan` AS plan_name,
                conv.`price` AS plan_price,
                conv.`payout` AS payout_amount,
                conv.`fire_postback`,
                conv.`postback_url`,
                conv.`response_code`,
                conv.`postback_log`,
                c.`s1` AS sub1,
                c.`s2` AS sub2
              FROM `conversions` conv
              INNER JOIN `clicks` c ON conv.`cid` = CONVERT(c.`cid` USING utf8mb4) COLLATE utf8mb4_unicode_ci
              WHERE c.`affid` = ? $status_condition
              ORDER BY conv.`created_at` DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$affiliateId]);
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $log_data[] = [
            'id'            => $row['conv_id'],
            'tx_id'         => !empty($row['tx_id']) ? $row['tx_id'] : '—',
            'date'          => $row['log_date'],
            'click_id'      => $row['click_id'],
            'plan_name'     => strtoupper($row['plan_name']),
            'price'         => (float)$row['plan_price'],
            'payout'        => (float)$row['payout_amount'],
            'fired'         => (int)$row['fire_postback'] === 1,
            'url'           => $row['postback_url'] ?? '—',
            'response_code' => $row['response_code'] !== null ? (int)$row['response_code'] : '—',
            'log'           => !empty($row['postback_log']) ? $row['postback_log'] : '—',
            'sub1'          => !empty($row['sub1']) ? $row['sub1'] : '—',
            'sub2'          => !empty($row['sub2']) ? $row['sub2'] : '—'
        ];
    }

} catch (PDOException $e) {
    error_log("Affiliate Postback Registry Evaluation Fault: " . $e->getMessage());
    die("An error occurred extracting structural webhook dispatch metrics loops.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Postback Audit Registry Matrix — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="max-w-7xl w-full mx-auto px-4 sm:px-6 pt-8 pb-16 grow space-y-6">

        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
            <form method="GET" action="postback-log.php" class="flex flex-col sm:flex-row gap-4 items-end justify-between w-full">
                <div class="space-y-1.5 w-full sm:max-w-sm text-left">
                    <label class="text-xs font-extrabold text-gray-400 uppercase tracking-wider">Transmission Delivery Hook State</label>
                    <select name="status" class="w-full bg-slate-50 border border-gray-200 text-sm rounded-xl px-3 py-2.5 font-semibold text-slate-700 outline-none focus:border-indigo-500 transition-all">
                        <option value="all" <?= $filter_status === 'all' ? 'selected' : '' ?>>All Webhooks (<?= $count_all ?>)</option>
                        <option value="fired" <?= $filter_status === 'fired' ? 'selected' : '' ?>>Fired Webhooks (<?= $count_fired ?>)</option>
                        <option value="not_fired" <?= $filter_status === 'not_fired' ? 'selected' : '' ?>>Skipped / Failed (<?= $count_skipped ?>)</option>
                    </select>
                </div>

                <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition-all cursor-pointer shadow-sm flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-filter text-sm"></i> Filter Matrix Logs
                </button>
            </form>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-white flex justify-between items-center">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Outbound Postback Transmission Ledger</h3>
                <span class="text-xs font-mono text-slate-400">Dispatched records: <?= count($log_data) ?> hooks</span>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-white">
                            <th class="px-4 py-3.5 whitespace-nowrap">Timestamp</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">TXID</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Click ID Token</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">SubID 1</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">SubID 2</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Plan Details</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">CPA Value / Payout</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Postback Fired</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">Dispatched Destination URL String</th>
                            <th class="px-4 py-3.5 whitespace-nowrap">HTTP Code</th>
                            <th class="px-4 py-3.5 whitespace-nowrap text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs font-medium text-slate-700 bg-white">
                        <?php if (empty($log_data)): ?>
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center text-gray-400 font-semibold font-mono">
                                <i class="fa-solid fa-folder-open text-2xl block mb-2 text-slate-300"></i>
                                No active conversion postback logs recorded on this partner vector node.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($log_data as $log): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-4 font-mono text-slate-500 whitespace-nowrap">
                                    <?= date('Y-m-d H:i:s', strtotime($log['date'])) ?>
                                </td>
                                <td class="px-4 py-4 font-mono text-slate-800 font-bold whitespace-nowrap">
                                    <?= htmlspecialchars($log['tx_id']) ?>
                                </td>
                                <td class="px-4 py-4 font-mono text-gray-400 select-all whitespace-nowrap">
                                    <?= htmlspecialchars($log['click_id']) ?>
                                </td>
                                <td class="px-4 py-4 font-mono text-slate-600 whitespace-nowrap">
                                    <?= htmlspecialchars($log['sub1']) ?>
                                </td>
                                <td class="px-4 py-4 font-mono text-slate-600 whitespace-nowrap">
                                    <?= htmlspecialchars($log['sub2']) ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-50 border border-indigo-100 text-indigo-700">
                                        <?= htmlspecialchars($log['plan_name']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-4 font-mono text-slate-600 whitespace-nowrap">
                                    $<?= number_format($log['price'], 2) ?> <span class="text-gray-400 text-[10px] font-bold">/</span> <span class="text-emerald-600 font-bold">+$<?= number_format($log['payout'], 2) ?></span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php if ($log['fired']): ?>
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 font-bold px-2.5 py-0.5 rounded text-[10px] border border-emerald-100">
                                            Fired
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-500 font-bold px-2.5 py-0.5 rounded text-[10px] border border-slate-200">
                                            Skipped
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-slate-500 max-w-xs truncate font-mono text-[11px]" title="<?= htmlspecialchars($log['url']) ?>">
                                    <?= htmlspecialchars($log['url']) ?>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <?php if ($log['response_code'] === '—'): ?>
                                        <span class="font-mono font-bold text-slate-400">—</span>
                                    <?php else: ?>
                                        <span class="font-mono font-black rounded px-1.5 py-0.5 <?= (int)$log['response_code'] >= 200 && (int)$log['response_code'] < 300 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' ?>">
                                            <?= $log['response_code'] ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-4 text-right whitespace-nowrap">
                                    <button onclick="openLogModal(<?= htmlspecialchars(json_encode($log)) ?>)" class="text-xs font-bold text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg transition-all cursor-pointer">
                                        <i class="fa-solid fa-magnifying-glass-chart text-xs mr-0.5"></i> View Details
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <div id="logDetailsModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4" onclick="closeLogModal(event)">
        <div class="bg-white rounded-2xl max-w-2xl w-full border border-gray-200 shadow-xl overflow-hidden transform transition-all text-left flex flex-col" onclick="event.stopPropagation()">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-server text-indigo-600"></i> Postback Telemetry Matrix</h3>
                    <p class="text-[10px] font-mono text-gray-400 mt-0.5" id="modalTimestamp"></p>
                </div>
                <button onclick="toggleModalVisibility(true)" class="text-gray-400 hover:text-gray-600 font-bold text-sm bg-gray-100 hover:bg-gray-200 w-7 h-7 rounded-full flex items-center justify-center cursor-pointer">✕</button>
            </div>

            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto text-xs">
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Unique Transaction ID (TXID)</p>
                        <div class="bg-slate-50 border border-gray-200 font-mono p-3 rounded-xl text-gray-900 font-bold select-all" id="modalTxId"></div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Click Tracking ID</p>
                        <div class="bg-slate-50 border border-gray-200 font-mono p-3 rounded-xl text-slate-700 font-semibold truncate select-all" id="modalClickId"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">SubID 1 Parameter</p>
                        <div class="bg-slate-50 border border-gray-200 font-mono p-3 rounded-xl text-slate-800 font-medium select-all" id="modalSub1"></div>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">SubID 2 Parameter</p>
                        <div class="bg-slate-50 border border-gray-200 font-mono p-3 rounded-xl text-slate-800 font-medium select-all" id="modalSub2"></div>
                    </div>
                </div>

                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Dispatched Target URL</p>
                    <div class="bg-slate-50 border border-gray-200 font-mono p-3 rounded-xl text-slate-700 break-all select-all font-semibold" id="modalUrl"></div>
                </div>

                <div class="space-y-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Remote Server Execution Response Log</p>
                    <pre class="bg-slate-50 border border-gray-200 font-mono text-[11px] p-4 rounded-xl overflow-x-auto text-slate-700 leading-relaxed font-semibold whitespace-pre-wrap text-left min-h-24 max-h-64" id="modalBody"></pre>
                </div>
            </div>

            <div class="px-6 py-3 border-t border-gray-100 bg-white flex justify-end">
                <button onclick="toggleModalVisibility(true)" class="bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs px-4 py-2 rounded-xl transition-all cursor-pointer shadow-xs">
                    Dismiss Modal
                </button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('logDetailsModal');

        function toggleModalVisibility(shouldHide) {
            if (shouldHide) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            } else {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function openLogModal(data) {
            document.getElementById('modalTimestamp').textContent = `Logged Event: ${data.date} (UTC)`;
            document.getElementById('modalTxId').textContent = data.tx_id;
            document.getElementById('modalClickId').textContent = data.click_id;
            document.getElementById('modalSub1').textContent = data.sub1;
            document.getElementById('modalSub2').textContent = data.sub2;
            document.getElementById('modalUrl').textContent = data.url;
            document.getElementById('modalBody').textContent = data.log;

            toggleModalVisibility(false);
        }

        function closeLogModal(e) {
            if(e.target === modal) {
                toggleModalVisibility(true);
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') toggleModalVisibility(true);
        });
    </script>

    <footer class="w-full bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 font-semibold">
        &copy; <?= date('Y'); ?> Identity Search AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>
