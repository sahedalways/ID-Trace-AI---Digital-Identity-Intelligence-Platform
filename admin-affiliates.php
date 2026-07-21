<?php
/**
 * File: admin-affiliates.php
 * Admin affiliates management — All / Pending / Payments tabs with pagination.
 */
require_once 'config.php';
require_once 'email_ban.php';
require_once 'email_activate.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$action_msg = '';
$action_type = '';

$success_msg = "";
if (isset($_SESSION['flash_success'])) {
    $success_msg = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
if (isset($_SESSION['flash_error'])) {
    $success_msg = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}

// Handle affiliate actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $act = $_POST['action'];
    $affId = (int)($_POST['affiliate_id'] ?? 0);

    if ($act === 'activate' && $affId) {
        $affRow = $pdo->prepare("SELECT `name`, `email` FROM `affiliates` WHERE `id` = ? LIMIT 1");
        $affRow->execute([$affId]);
        $affRow = $affRow->fetch(PDO::FETCH_ASSOC);
        $pdo->prepare("UPDATE `affiliates` SET `status` = 'active' WHERE `id` = ?")->execute([$affId]);
        $_SESSION['flash_success'] = "Affiliate activated successfully.";
        if ($affRow) {
            @sendActivationEmail($affRow['email'], $affRow['name']);
        }
    } elseif ($act === 'ban' && $affId) {
        $affRow = $pdo->prepare("SELECT `name`, `email` FROM `affiliates` WHERE `id` = ? LIMIT 1");
        $affRow->execute([$affId]);
        $affRow = $affRow->fetch(PDO::FETCH_ASSOC);
        $pdo->prepare("UPDATE `affiliates` SET `status` = 'banned' WHERE `id` = ?")->execute([$affId]);
        $_SESSION['flash_success'] = "Affiliate banned.";
        if ($affRow) {
            @sendBanEmail($affRow['email'], $affRow['name']);
        }
    } elseif ($act === 'approve_payment' && $affId) {
        $payId = (int)($_POST['payment_id'] ?? 0);
        $pdo->prepare("UPDATE `withdraw` SET `status` = 'approved' WHERE `id` = ?")->execute([$payId]);
        $_SESSION['flash_success'] = "Payment approved.";
    } elseif ($act === 'reject_payment' && $affId) {
        $payId = (int)($_POST['payment_id'] ?? 0);
        $pdo->prepare("UPDATE `withdraw` SET `status` = 'rejected' WHERE `id` = ?")->execute([$payId]);
        $_SESSION['flash_success'] = "Payment rejected.";
    }
    header("Location: admin-affiliates.php?tab=" . urlencode($tab));
    exit;
}

function buildAffQs($overrides) {
    $q = array_merge($_GET, $overrides);
    return http_build_query($q);
}

try {
    if ($tab === 'payments') {
        $where = "";
        $params = [];
        if (!empty($search)) {
            $where = "WHERE (a.name LIKE ? OR a.email LIKE ? OR a.aid LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        $totalRows = (int)$pdo->prepare("SELECT COUNT(*) FROM `withdraw` w LEFT JOIN `affiliates` a ON w.affid = a.id $where")->execute($params) ? 0 : $pdo->prepare("SELECT COUNT(*) FROM `withdraw` w LEFT JOIN `affiliates` a ON w.affid = a.id $where")->execute($params);
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `withdraw` w LEFT JOIN `affiliates` a ON w.affid = a.id $where");
        $countStmt->execute($params);
        $totalRows = (int)$countStmt->fetchColumn();
        $totalPages = max(1, ceil($totalRows / $perPage));
        $payments = $pdo->prepare("
            SELECT w.*, a.name as aff_name, a.email as aff_email, a.aid 
            FROM `withdraw` w 
            LEFT JOIN `affiliates` a ON w.affid = a.id 
            $where
            ORDER BY w.created_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        $payments->execute($params);
        $payments = $payments->fetchAll();
    } else {
        $where = $tab === 'pending' ? "WHERE a.status = 'pending'" : "";
        $params = [];
        if (!empty($search)) {
            $sep = $where ? " AND" : " WHERE";
            $where .= "$sep (a.id = ? OR a.email LIKE ? OR a.aid LIKE ? OR a.name LIKE ?)";
            $params = array_merge($params, [$search, "%$search%", "%$search%", "%$search%"]);
        }
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM `affiliates` a $where");
        $countStmt->execute($params);
        $totalRows = (int)$countStmt->fetchColumn();
        $totalPages = max(1, ceil($totalRows / $perPage));
        $affiliates = $pdo->prepare("SELECT a.* FROM `affiliates` a $where ORDER BY a.created_at DESC LIMIT $perPage OFFSET $offset");
        $affiliates->execute($params);
        $affiliates = $affiliates->fetchAll();
    }
} catch (PDOException $e) {
    error_log("Admin Affiliates Error: " . $e->getMessage());
    die("An error occurred.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Affiliates — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6">

            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Affiliate Management</h1>
                <p class="text-xs text-gray-400">Manage all affiliate accounts, approvals, and payments.</p>
            </div>

            <!-- Tabs + Search Row -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div class="flex items-center gap-2 border-b border-gray-200 pb-0">
                    <a href="admin-affiliates.php?tab=all" class="px-4 py-2.5 text-[13px] font-bold transition-all border-b-2 <?= $tab === 'all' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-900' ?>">All</a>
                    <a href="admin-affiliates.php?tab=pending" class="px-4 py-2.5 text-[13px] font-bold transition-all border-b-2 <?= $tab === 'pending' ? 'border-amber-500 text-amber-700' : 'border-transparent text-gray-500 hover:text-gray-900' ?>">Pending</a>
                    <a href="admin-affiliates.php?tab=payments" class="px-4 py-2.5 text-[13px] font-bold transition-all border-b-2 <?= $tab === 'payments' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-900' ?>">Payments</a>
                </div>

                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search..."
                            class="text-sm pl-8 pr-3 py-2 bg-white border border-gray-200 rounded-lg focus:outline-none focus:border-indigo-500 transition-all font-semibold text-gray-900 placeholder-gray-400 w-56">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2 px-3 rounded-lg transition-all cursor-pointer">Go</button>
                    <?php if (!empty($search)): ?>
                        <a href="admin-affiliates.php?tab=<?= $tab ?>" class="text-[10px] font-bold text-gray-500 hover:text-gray-900">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="text-[11px] font-bold text-gray-400">Showing <?= number_format($totalRows) ?> results</div>

            <?php if ($tab === 'payments'): ?>
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">ID</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Affiliate</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Amount</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Status</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Date</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if (empty($payments)): ?>
                                    <tr><td colspan="6" class="text-xs text-gray-400 py-8 text-center font-semibold">No payment records found.</td></tr>
                                <?php else: foreach ($payments as $p): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-5 py-3 text-xs font-mono text-gray-500">#<?= $p['id'] ?></td>
                                        <td class="px-5 py-3">
                                            <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($p['aff_name'] ?? 'N/A') ?></div>
                                            <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($p['aff_email'] ?? '') ?></div>
                                        </td>
                                        <td class="px-5 py-3 text-xs font-bold text-gray-900 font-mono">$<?= number_format($p['amount'], 2) ?></td>
                                        <td class="px-5 py-3">
                                            <?php if ($p['status'] === 'approved'): ?>
                                                <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Approved</span>
                                            <?php elseif ($p['status'] === 'rejected'): ?>
                                                <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md">Rejected</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-md">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-3 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y', strtotime($p['created_at'])) ?></td>
                                        <td class="px-5 py-3">
                                            <?php if ($p['status'] === 'pending'): ?>
                                                <div class="flex items-center gap-1.5">
                                                    <form method="POST" class="inline"><input type="hidden" name="action" value="approve_payment"><input type="hidden" name="affiliate_id" value="<?= $p['affid'] ?>"><input type="hidden" name="payment_id" value="<?= $p['id'] ?>"><button type="submit" class="text-[10px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-2 py-1 rounded-md transition cursor-pointer">Approve</button></form>
                                                    <form method="POST" class="inline"><input type="hidden" name="action" value="reject_payment"><input type="hidden" name="affiliate_id" value="<?= $p['affid'] ?>"><input type="hidden" name="payment_id" value="<?= $p['id'] ?>"><button type="submit" class="text-[10px] font-bold bg-red-50 text-red-700 hover:bg-red-100 px-2 py-1 rounded-md transition cursor-pointer">Reject</button></form>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-[10px] text-gray-400 font-semibold">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">ID</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Name / Email</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Status</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Balance</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Withdrawn</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Joined</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if (empty($affiliates)): ?>
                                    <tr><td colspan="7" class="text-xs text-gray-400 py-8 text-center font-semibold">No affiliates found.</td></tr>
                                <?php else: foreach ($affiliates as $a): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-5 py-3 text-xs font-mono text-gray-500">#<?= $a['id'] ?></td>
                                        <td class="px-5 py-3">
                                            <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($a['name']) ?></div>
                                            <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($a['email']) ?></div>
                                            <div class="text-[10px] text-indigo-600 font-mono"><?= htmlspecialchars($a['aid']) ?></div>
                                        </td>
                                        <td class="px-5 py-3">
                                            <?php if ($a['status'] === 'active'): ?>
                                                <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Active</span>
                                            <?php elseif ($a['status'] === 'pending'): ?>
                                                <span class="inline-flex items-center text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-md">Pending</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md">Banned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-3 text-xs font-bold text-gray-900 font-mono">$<?= number_format($a['balance'], 2) ?></td>
                                        <td class="px-5 py-3 text-xs font-bold text-gray-500 font-mono">$<?= number_format($a['withdraw'], 2) ?></td>
                                        <td class="px-5 py-3 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-1.5">
                                                <?php if ($a['status'] !== 'active'): ?>
                                                    <form method="POST" class="inline"><input type="hidden" name="action" value="activate"><input type="hidden" name="affiliate_id" value="<?= $a['id'] ?>"><button type="submit" class="text-[10px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 px-2 py-1 rounded-md transition cursor-pointer">Activate</button></form>
                                                <?php endif; ?>
                                                <?php if ($a['status'] !== 'banned'): ?>
                                                    <form method="POST" class="inline" onsubmit="return confirm('Ban this affiliate?')"><input type="hidden" name="action" value="ban"><input type="hidden" name="affiliate_id" value="<?= $a['id'] ?>"><button type="submit" class="text-[10px] font-bold bg-red-50 text-red-700 hover:bg-red-100 px-2 py-1 rounded-md transition cursor-pointer">Ban</button></form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                    <div class="text-[11px] font-semibold text-gray-400">Page <?= $page ?> of <?= number_format($totalPages) ?></div>
                    <div class="flex items-center gap-1.5">
                        <?php if ($page > 1): ?>
                            <a href="?<?= buildAffQs(['page' => 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">First</a>
                            <a href="?<?= buildAffQs(['page' => $page - 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Prev</a>
                        <?php endif; ?>
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <a href="?<?= buildAffQs(['page' => $i]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg transition <?= $i === $page ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= buildAffQs(['page' => $page + 1]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Next</a>
                            <a href="?<?= buildAffQs(['page' => $totalPages]) ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Last</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

        </main>
    </div>

    <?php
    $alert_type = $success_msg ? 'success' : '';
    $alert_message = $success_msg;
    ?>
    <?php include 'alert-modal.php'; ?>

</body>
</html>
