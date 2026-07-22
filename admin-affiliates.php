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

    // AJAX handlers — return JSON and exit immediately
    if ($act === 'change_invoice_status') {
        header('Content-Type: application/json');
        $invId     = (int)($_POST['invoice_id'] ?? 0);
        $newStatus = trim($_POST['new_status'] ?? '');
        if ($invId && in_array($newStatus, ['pending','processing','completed','cancelled'])) {
            $pdo->prepare("UPDATE `payment_invoices` SET `status` = ? WHERE `id` = ?")->execute([$newStatus, $invId]);
            $_SESSION['flash_success'] = "Invoice status changed to " . ucfirst($newStatus) . ".";
            echo json_encode(['ok' => true, 'status' => $newStatus]);
        } else {
            echo json_encode(['ok' => false]);
        }
        exit;
    }

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
    } elseif ($act === 'change_status' && $affId) {
        $newStatus = $_POST['new_status'] ?? '';
        if (in_array($newStatus, ['active', 'pending', 'banned'])) {
            $affRow = $pdo->prepare("SELECT `name`, `email`, `status` FROM `affiliates` WHERE `id` = ? LIMIT 1");
            $affRow->execute([$affId]);
            $affRow = $affRow->fetch(PDO::FETCH_ASSOC);
            $pdo->prepare("UPDATE `affiliates` SET `status` = ? WHERE `id` = ?")->execute([$newStatus, $affId]);
            $_SESSION['flash_success'] = "Affiliate status changed to " . ucfirst($newStatus) . ".";
            if ($affRow && $newStatus === 'active' && $affRow['status'] !== 'active') {
                @sendActivationEmail($affRow['email'], $affRow['name']);
            } elseif ($affRow && $newStatus === 'banned' && $affRow['status'] !== 'banned') {
                @sendBanEmail($affRow['email'], $affRow['name']);
            }
        }
    } elseif ($act === 'create_invoice' && $affId) {
        $payId    = (int)($_POST['payment_id'] ?? 0);
        $invAmt   = (float)($_POST['invoice_amount'] ?? 0);
        $invNote  = trim($_POST['invoice_note'] ?? '');
        $invStatus   = trim($_POST['inv_status'] ?? 'pending');
        $invType     = trim($_POST['invoice_type'] ?? 'regular');
        $txnId       = trim($_POST['transaction_id'] ?? '');
        $frequency   = trim($_POST['frequency'] ?? 'one_time');
        $remarks     = trim($_POST['remarks'] ?? '');

        if (!in_array($invStatus, ['pending','processing','completed','cancelled'])) $invStatus = 'pending';
        if (!in_array($invType, ['payout','regular','bonus','correction','adjustment'])) $invType = 'payout';
        if (!in_array($frequency, ['net15'])) $frequency = 'net15';

        $wd = $pdo->prepare("SELECT `amount`, `status` FROM `withdraw` WHERE `id` = ? LIMIT 1");
        $wd->execute([$payId]);
        $wd = $wd->fetch(PDO::FETCH_ASSOC);

        if (!$wd) {
            $_SESSION['flash_error'] = "Payment request not found.";
        } elseif ($wd['status'] !== 'pending' && $wd['status'] !== 'approved') {
            $_SESSION['flash_error'] = "Cannot create invoice for this request.";
        } else {
            $paidStmt = $pdo->prepare("SELECT COALESCE(SUM(`amount`),0) FROM `payment_invoices` WHERE `withdraw_id` = ? AND `status` IN ('approved','completed')");
            $paidStmt->execute([$payId]);
            $alreadyPaid = (float)$paidStmt->fetchColumn();
            $remaining   = (float)$wd['amount'] - $alreadyPaid;

            if ($invAmt <= 0) {
                $_SESSION['flash_error'] = "Invoice amount must be greater than 0.";
            } elseif ($invAmt > $remaining) {
                $_SESSION['flash_error'] = "Invoice amount ($" . number_format($invAmt,2) . ") exceeds pending balance ($" . number_format($remaining,2) . ").";
            } else {
                $invoiceCode = 'INV' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 13));
                $pdo->prepare("INSERT INTO `payment_invoices` (`withdraw_id`, `invoice_id`, `amount`, `status`, `transaction_id`, `remarks`, `frequency`, `invoice_type`, `note`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())")
                    ->execute([$payId, $invoiceCode, $invAmt, $invStatus, $txnId ?: null, $remarks ?: null, $frequency, $invType, $invNote ?: null]);
                $_SESSION['flash_success'] = "Invoice $invoiceCode created for $" . number_format($invAmt, 2) . ".";
            }
        }
    } elseif ($act === 'approve_invoice') {
        $invId = (int)($_POST['invoice_db_id'] ?? 0);
        $pdo->prepare("UPDATE `payment_invoices` SET `status` = 'approved' WHERE `id` = ?")->execute([$invId]);
        $_SESSION['flash_success'] = "Invoice approved.";
    } elseif ($act === 'reject_payment' && $affId) {
        $payId = (int)($_POST['payment_id'] ?? 0);
        $wdRow = $pdo->prepare("SELECT `amount` FROM `withdraw` WHERE `id` = ? AND `status` = 'pending' LIMIT 1");
        $wdRow->execute([$payId]);
        $wdRow = $wdRow->fetch(PDO::FETCH_ASSOC);
        if ($wdRow) {
            $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` + ? WHERE `id` = ?")->execute([(float)$wdRow['amount'], $affId]);
        }
        $pdo->prepare("UPDATE `withdraw` SET `status` = 'rejected' WHERE `id` = ?")->execute([$payId]);
        $_SESSION['flash_success'] = "Payment rejected. $" . number_format($wdRow['amount'] ?? 0, 2) . " returned to affiliate balance.";
    } elseif ($act === 'add_affiliate') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $country = trim($_POST['country'] ?? '');
        $contact = trim($_POST['contact'] ?? '');
        $experience_level = trim($_POST['experience_level'] ?? 'New Affiliate');

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['flash_error'] = "Name, email and password are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = "Invalid email address.";
        } elseif (strlen($password) < 6) {
            $_SESSION['flash_error'] = "Password must be at least 6 characters.";
        } else {
            $checkStmt = $pdo->prepare("SELECT id FROM `affiliates` WHERE `email` = ? LIMIT 1");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                $_SESSION['flash_error'] = "Email is already registered.";
            } else {
                function generateAdminAffId() {
                    global $pdo;
                    do {
                        $aid = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
                        $stmt = $pdo->prepare("SELECT id FROM `affiliates` WHERE `aid` = ? LIMIT 1");
                        $stmt->execute([$aid]);
                    } while ($stmt->fetch());
                    return $aid;
                }
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $uniqueAid = generateAdminAffId();
                $pdo->prepare("INSERT INTO `affiliates` (`aid`, `name`, `email`, `password`, `country`, `contact`, `experience_level`, `status`, `balance`, `withdraw`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', 0.00, 0.00, NOW())")
                    ->execute([$uniqueAid, $name, $email, $hashed, $country, $contact, $experience_level]);
                $_SESSION['flash_success'] = "Affiliate '$name' created successfully. ID: $uniqueAid";
            }
        }
        header("Location: admin-affiliates.php?tab=" . urlencode($tab));
        exit;
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
            SELECT w.*,
                   a.name as aff_name, a.email as aff_email, a.aid,
                   COALESCE(paid.total_paid, 0) as total_paid,
                   (w.amount - COALESCE(paid.total_paid, 0)) as pending_amount
            FROM `withdraw` w 
            LEFT JOIN `affiliates` a ON w.affid = a.id 
            LEFT JOIN (
                SELECT withdraw_id, SUM(amount) as total_paid
                FROM `payment_invoices` WHERE status IN ('approved','completed')
                GROUP BY withdraw_id
            ) paid ON paid.withdraw_id = w.id
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
            $where .= "$sep (a.id = ? OR a.email LIKE ? OR a.aid LIKE ? OR a.name LIKE ? OR a.contact LIKE ?)";
            $params = array_merge($params, [$search, "%$search%", "%$search%", "%$search%", "%$search%"]);
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
    die("Error: " . $e->getMessage());
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

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Affiliate Management</h1>
                    <p class="text-xs text-gray-400">Manage all affiliate accounts, approvals, and payments.</p>
                </div>
                <button onclick="document.getElementById('addAffModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 text-[11px] font-bold bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-xl transition cursor-pointer">
                    <i class="fa-solid fa-plus text-[10px]"></i> New Affiliate
                </button>
            </div>

            <!-- Tabs + Search Row -->
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div class="flex items-center gap-2 border-b border-gray-200 pb-0">
                    <a href="admin-affiliates.php?tab=all" class="px-4 py-2.5 text-[13px] font-bold transition-all border-b-2 <?= $tab === 'all' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-900' ?>">All</a>
                    <a href="admin-affiliates.php?tab=pending" class="px-4 py-2.5 text-[13px] font-bold transition-all border-b-2 <?= $tab === 'pending' ? 'border-amber-500 text-amber-700' : 'border-transparent text-gray-500 hover:text-gray-900' ?>">Pending</a>
                    <a href="admin-affiliates.php?tab=payments" class="px-4 py-2.5 text-[13px] font-bold transition-all border-b-2 <?= $tab === 'payments' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-gray-500 hover:text-gray-900' ?>">Payments</a>
                </div>

                <form method="GET" class="flex items-center gap-2 flex-1 justify-end">
                    <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                    <div class="relative w-full max-w-2xl">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by ID, name, email, affiliate ID, telegram..."
                            class="w-full text-sm pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-5 rounded-xl transition-all cursor-pointer">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="admin-affiliates.php?tab=<?= $tab ?>" class="text-[10px] font-bold text-gray-500 hover:text-gray-900 px-2">Clear</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="text-[11px] font-bold text-gray-400">Showing <?= number_format($totalRows) ?> results</div>

            <?php if ($tab === 'payments'):
                $sumStmt = $pdo->query("SELECT (SELECT COALESCE(SUM(w.amount - COALESCE(p.total_paid,0)),0) FROM `withdraw` w LEFT JOIN (SELECT withdraw_id, SUM(amount) as total_paid FROM `payment_invoices` WHERE status IN ('approved','completed') GROUP BY withdraw_id) p ON p.withdraw_id = w.id WHERE w.status != 'rejected') as total_pending, (SELECT COALESCE(SUM(amount),0) FROM `payment_invoices` WHERE status IN ('approved','completed')) as total_paid, (SELECT COUNT(*) FROM `withdraw` w LEFT JOIN (SELECT withdraw_id, SUM(amount) as total_paid FROM `payment_invoices` WHERE status IN ('approved','completed') GROUP BY withdraw_id) p ON p.withdraw_id = w.id WHERE w.status != 'rejected' AND (w.amount - COALESCE(p.total_paid,0)) > 0) as pending_invoices, (SELECT COUNT(*) FROM `withdraw` w LEFT JOIN (SELECT withdraw_id, SUM(amount) as total_paid FROM `payment_invoices` WHERE status IN ('approved','completed') GROUP BY withdraw_id) p ON p.withdraw_id = w.id WHERE w.status != 'rejected' AND (w.amount - COALESCE(p.total_paid,0)) <= 0) as paid_invoices");
                $sumRow = $sumStmt->fetch(PDO::FETCH_ASSOC);
                $totalPending = (float)($sumRow['total_pending'] ?? 0);
                $totalPaid    = (float)($sumRow['total_paid'] ?? 0);
                $pendingInvCount = (int)($sumRow['pending_invoices'] ?? 0);
                $paidInvCount    = (int)($sumRow['paid_invoices'] ?? 0);
            ?>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white border border-amber-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center"><i class="fa-solid fa-hourglass-half text-amber-500 text-lg"></i></div>
                        <div><div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pending Amount</div><div class="text-2xl font-black text-gray-900 tracking-tight font-mono">$<?= number_format($totalPending, 2) ?></div></div>
                    </div>
                    <div class="bg-white border border-emerald-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center"><i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i></div>
                        <div><div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Paid Amount</div><div class="text-2xl font-black text-gray-900 tracking-tight font-mono">$<?= number_format($totalPaid, 2) ?></div></div>
                    </div>
                    <div class="bg-white border border-orange-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center"><i class="fa-solid fa-file-circle-exclamation text-orange-500 text-lg"></i></div>
                        <div><div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pending Invoices</div><div class="text-2xl font-black text-gray-900 tracking-tight font-mono"><?= $pendingInvCount ?></div></div>
                    </div>
                    <div class="bg-white border border-blue-200 rounded-2xl p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center"><i class="fa-solid fa-file-circle-check text-blue-500 text-lg"></i></div>
                        <div><div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Paid Invoices</div><div class="text-2xl font-black text-gray-900 tracking-tight font-mono"><?= $paidInvCount ?></div></div>
                    </div>
                </div>
                <!-- Status Filter -->
                <div class="flex items-center gap-2">
                    <button onclick="filterWd('all', this)" class="wd-filter text-[11px] font-bold px-3 py-1.5 rounded-lg transition cursor-pointer bg-indigo-600 text-white">All</button>
                    <button onclick="filterWd('pending', this)" class="wd-filter text-[11px] font-bold px-3 py-1.5 rounded-lg transition cursor-pointer bg-amber-50 text-amber-700 hover:bg-amber-100">Pending</button>
                    <button onclick="filterWd('approved', this)" class="wd-filter text-[11px] font-bold px-3 py-1.5 rounded-lg transition cursor-pointer bg-emerald-50 text-emerald-700 hover:bg-emerald-100">Approved</button>
                    <button onclick="filterWd('rejected', this)" class="wd-filter text-[11px] font-bold px-3 py-1.5 rounded-lg transition cursor-pointer bg-red-50 text-red-700 hover:bg-red-100">Rejected</button>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">ID</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Affiliate</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Amount</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Paid</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Pending</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Payment Term</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Status</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Date</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if (empty($payments)): ?>
                                    <tr><td colspan="9" class="text-xs text-gray-400 py-8 text-center font-semibold">No payment records found.</td></tr>
                                <?php else: foreach ($payments as $p):
                                    $rowPaid   = (float)($p['total_paid'] ?? 0);
                                    $rowPending = (float)($p['pending_amount'] ?? 0);
                                ?>
                                    <tr class="hover:bg-gray-50/50 wd-row" data-status="<?= htmlspecialchars($p['status']) ?>">
                                        <td class="px-5 py-3 text-xs font-mono text-gray-500">#<?= $p['id'] ?></td>
                                        <td class="px-5 py-3">
                                            <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($p['aff_name'] ?? 'N/A') ?></div>
                                            <div class="text-[10px] text-gray-400 font-mono"><?= htmlspecialchars($p['aff_email'] ?? '') ?></div>
                                        </td>
                                        <td class="px-5 py-3 text-xs font-bold text-gray-900 font-mono">$<?= number_format($p['amount'], 2) ?></td>
                                        <td class="px-5 py-3 text-xs font-bold text-emerald-600 font-mono">$<?= number_format($rowPaid, 2) ?></td>
                                        <td class="px-5 py-3 text-xs font-bold text-amber-600 font-mono">$<?= number_format($rowPending, 2) ?></td>
                                        <td class="px-5 py-3"><span class="inline-flex items-center text-[10px] font-bold bg-purple-50 border border-purple-100 text-purple-700 px-2 py-0.5 rounded-md">Net15</span></td>
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
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <?php if ($p['status'] !== 'rejected' && $rowPending > 0): ?>
                                                    <button onclick="openCreateInvoice(<?= $p['id'] ?>, <?= $p['affid'] ?>, <?= $rowPending ?>, <?= $rowPaid ?>, '<?= addslashes(htmlspecialchars($p['aff_name'] ?? 'N/A')) ?>', '<?= addslashes(htmlspecialchars($p['aid'] ?? '')) ?>')" class="text-[10px] font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 px-2 py-1 rounded-md transition cursor-pointer">+ Invoice</button>
                                                <?php endif; ?>
                                                <button onclick="openViewInvoices(<?= $p['id'] ?>)" class="text-[10px] font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 px-2 py-1 rounded-md transition cursor-pointer">View Invoices</button>
                                                <?php if ($p['status'] === 'pending'): ?>
                                                    <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to reject this payment?')"><input type="hidden" name="action" value="reject_payment"><input type="hidden" name="affiliate_id" value="<?= $p['affid'] ?>"><input type="hidden" name="payment_id" value="<?= $p['id'] ?>"><button type="submit" class="text-[10px] font-bold bg-red-50 text-red-700 hover:bg-red-100 px-2 py-1 rounded-md transition cursor-pointer">Reject</button></form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">ID</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Name</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Email</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Telegram</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Status</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Balance</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Withdrawn</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Joined</th>
                                    <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php if (empty($affiliates)): ?>
                                    <tr><td colspan="9" class="text-xs text-gray-400 py-8 text-center font-semibold">No affiliates found.</td></tr>
                                <?php else: foreach ($affiliates as $a): ?>
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-5 py-3 text-xs font-mono text-gray-500">#<?= $a['id'] ?></td>
                                        <td class="px-5 py-3">
                                            <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($a['name']) ?></div>
                                            <div class="text-[10px] text-indigo-600 font-mono"><?= htmlspecialchars($a['aid']) ?></div>
                                        </td>
                                        <td class="px-5 py-3 text-[11px] font-semibold text-gray-600 font-mono"><?= htmlspecialchars($a['email']) ?></td>
                                        <td class="px-5 py-3 text-[11px] font-semibold text-blue-600 font-mono flex items-center gap-1.5">
                                            <span id="tg-<?= $a['id'] ?>"><?= htmlspecialchars($a['contact'] ?? '—') ?></span>
                                            <?php if (!empty($a['contact'])): ?>
                                            <button onclick="copyTg(this, '<?= htmlspecialchars(addslashes($a['contact'])) ?>')" class="text-gray-400 hover:text-blue-600 transition cursor-pointer" title="Copy Telegram ID">
                                                <i class="fa-regular fa-copy text-[8px]"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-3">
                                            <form method="POST" onsubmit="return confirm('Change affiliate status?')">
                                                <input type="hidden" name="action" value="change_status">
                                                <input type="hidden" name="affiliate_id" value="<?= $a['id'] ?>">
                                                <select name="new_status" onchange="this.form.submit()" class="text-[10px] font-bold px-2 py-1 rounded-md border cursor-pointer focus:outline-none
                                                    <?= $a['status'] === 'active' ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : ($a['status'] === 'pending' ? 'bg-amber-50 border-amber-100 text-amber-700' : 'bg-red-50 border-red-100 text-red-700') ?>">
                                                    <option value="active" <?= $a['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                    <option value="pending" <?= $a['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="banned" <?= $a['status'] === 'banned' ? 'selected' : '' ?>>Banned</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-5 py-3 text-xs font-bold text-gray-900 font-mono">$<?= number_format($a['balance'], 2) ?></td>
                                        <td class="px-5 py-3 text-xs font-bold text-gray-500 font-mono">$<?= number_format($a['withdraw'], 2) ?></td>
                                        <td class="px-5 py-3 text-[10px] text-gray-400 font-semibold"><?= date('M d, Y', strtotime($a['created_at'])) ?></td>
                                        <td class="px-5 py-3">
                                            <div class="relative">
                                                <button onclick="toggleDropdown(this)" class="inline-flex items-center gap-1 text-[10px] font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 px-2.5 py-1 rounded-md transition cursor-pointer">
                                                    <i class="fa-solid fa-ellipsis"></i> More
                                                </button>
                                                <div class="hidden absolute right-0 z-50 mt-1 w-44 bg-white border border-gray-200 rounded-xl shadow-lg py-1.5 origin-top-right dropdown-menu">
                                                    <a href="admin-login-as-affiliate.php?id=<?= $a['id'] ?>" class="flex items-center gap-2 px-3.5 py-2 text-[11px] font-semibold text-gray-700 hover:bg-gray-50 transition">
                                                        <i class="fa-solid fa-right-to-bracket text-[10px] text-indigo-500"></i> Login as Affiliate
                                                    </a>
                                                    <a href="admin-affiliate-view.php?id=<?= $a['id'] ?>" class="flex items-center gap-2 px-3.5 py-2 text-[11px] font-semibold text-gray-700 hover:bg-gray-50 transition">
                                                        <i class="fa-solid fa-eye text-[10px] text-blue-500"></i> View
                                                    </a>
                                                    <a href="admin-affiliate-edit.php?id=<?= $a['id'] ?>" class="flex items-center gap-2 px-3.5 py-2 text-[11px] font-semibold text-gray-700 hover:bg-gray-50 transition">
                                                        <i class="fa-solid fa-pen text-[10px] text-amber-500"></i> Edit
                                                    </a>
                                                </div>
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

    <!-- Add New Affiliate Modal -->
    <div id="addAffModal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-indigo-600"></i> Add New Affiliate
                </h3>
                <button onclick="document.getElementById('addAffModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-700 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add_affiliate">
                <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Full Name *</label>
                        <input type="text" name="name" required placeholder="John Doe"
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Email Address *</label>
                        <input type="email" name="email" required placeholder="partner@domain.com"
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
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Telegram Username</label>
                        <input type="text" name="contact" placeholder="@username"
                            class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Experience Level</label>
                        <select name="experience_level" class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 cursor-pointer">
                            <option value="New Affiliate">New Affiliate</option>
                            <option value="Have some experience">Have some experience</option>
                            <option value="Expert">Expert</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Create Affiliate
                    </button>
                    <button type="button" onclick="document.getElementById('addAffModal').classList.add('hidden')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Invoice Modal -->
    <div id="createInvoiceModal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-[90vw] max-w-5xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5 border-b border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center"><i class="fa-solid fa-file-invoice text-indigo-600"></i></div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">New Invoice</h3>
                        <div class="text-[11px] text-gray-400 font-mono flex items-center gap-2">
                            <span id="ci_invoice_code">INV----------</span>
                            <span class="text-gray-300">|</span>
                            <span>Affiliate: <strong id="ci_aff_display" class="text-gray-600">—</strong></span>
                        </div>
                    </div>
                </div>
                <button onclick="document.getElementById('createInvoiceModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-700 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <!-- Left: Form -->
                <div class="lg:col-span-3">
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="create_invoice">
                        <input type="hidden" name="payment_id" id="ci_payment_id">
                        <input type="hidden" name="affiliate_id" id="ci_affiliate_id">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Invoice Amount (USD) *</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">$</span>
                                    <input type="number" name="invoice_amount" id="ci_amount" required step="0.01" min="0.01" placeholder="0.00"
                                        class="w-full text-sm pl-7 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Currency</label>
                                <div class="w-full text-sm px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl font-bold text-gray-500 flex items-center gap-2">
                                    <span class="text-lg">🇺🇸</span> USD
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Status *</label>
                                <select name="inv_status" class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 cursor-pointer">
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Invoice Type *</label>
                                <select name="invoice_type" class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 cursor-pointer">
                                    <option value="payout">Payout</option>
                                    <option value="regular">Regular</option>
                                    <option value="bonus">Bonus</option>
                                    <option value="correction">Correction</option>
                                    <option value="adjustment">Adjustment</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Transaction ID</label>
                                <input type="text" name="transaction_id" placeholder="e.g. TXN-123456"
                                    class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Frequency</label>
                                <select name="frequency" class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 cursor-pointer">
                                    <option value="net15">Net15</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Remarks</label>
                            <textarea name="remarks" rows="2" placeholder="Additional notes about this invoice..."
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 resize-none"></textarea>
                        </div>

                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Note (optional)</label>
                            <input type="text" name="invoice_note" placeholder="e.g. Partial payment for March"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                                <i class="fa-solid fa-check mr-1"></i> Create Invoice
                            </button>
                            <button type="button" onclick="document.getElementById('createInvoiceModal').classList.add('hidden')" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right: Affiliate Info -->
                <div class="lg:col-span-2 bg-gray-50 border border-gray-200 rounded-xl p-5 space-y-4" id="ci_aff_info">
                    <div class="text-xs text-gray-400 text-center py-4"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Invoices Modal -->
    <div id="viewInvoicesModal" class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-[90vw] max-w-5xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-indigo-600"></i> Invoices
                </h3>
                <button onclick="document.getElementById('viewInvoicesModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-700 transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div id="viewInvoicesBody" class="text-xs text-gray-400 text-center py-6">Loading...</div>
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
    function copyTg(btn, text) {
        navigator.clipboard.writeText(text).then(function() {
            var icon = btn.querySelector('i');
            icon.className = 'fa-solid fa-check text-[8px] text-emerald-500';
            var toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-[9999] bg-gray-900 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg flex items-center gap-2';
            toast.innerHTML = '<i class="fa-solid fa-check-circle text-emerald-400"></i> Telegram ID copied!';
            document.body.appendChild(toast);
            setTimeout(function() { toast.remove(); }, 2000);
            setTimeout(function() { icon.className = 'fa-regular fa-copy text-[8px]'; }, 1500);
        });
    }
    function copyInvoice(btn, text) {
        navigator.clipboard.writeText(text).then(function() {
            var icon = btn.querySelector('i');
            icon.className = 'fa-solid fa-check text-[8px] text-emerald-500';
            var toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-[9999] bg-gray-900 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg flex items-center gap-2';
            toast.innerHTML = '<i class="fa-solid fa-check-circle text-emerald-400"></i> Invoice ID copied!';
            document.body.appendChild(toast);
            setTimeout(function() { toast.remove(); }, 2000);
            setTimeout(function() { icon.className = 'fa-regular fa-copy text-[8px]'; }, 1500);
        });
    }
    function openCreateInvoice(paymentId, affId, pending, paid, affName, affAid) {
        document.getElementById('ci_payment_id').value = paymentId;
        document.getElementById('ci_affiliate_id').value = affId;
        document.getElementById('ci_amount').max = pending;
        document.getElementById('ci_amount').value = '';
        document.getElementById('ci_aff_display').textContent = affAid + ' — ' + affName;

        var code = 'INV' + Math.random().toString(36).substring(2, 10).toUpperCase();
        document.getElementById('ci_invoice_code').textContent = code;

        var infoBox = document.getElementById('ci_aff_info');
        infoBox.innerHTML = '<div class="text-xs text-gray-400 text-center py-4"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>';
        document.getElementById('createInvoiceModal').classList.remove('hidden');

        fetch('admin-fetch-affiliate-payments.php?affiliate_id=' + affId + '&amount=' + encodeURIComponent(pending) + '&paid=' + encodeURIComponent(paid))
            .then(function(r) { return r.text(); })
            .then(function(html) { infoBox.innerHTML = html; })
            .catch(function() { infoBox.innerHTML = '<span class="text-red-500 text-xs">Failed to load affiliate info.</span>'; });
    }
    function changeInvoiceStatus(invId, newStatus) {
        var fd = new FormData();
        fd.append('action', 'change_invoice_status');
        fd.append('invoice_id', invId);
        fd.append('new_status', newStatus);
        fetch('admin-affiliates.php', { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function() { location.reload(); })
            .catch(function() { alert('Error changing status.'); });
    }
    function approveInvoice(invId, btn) {
        if (!confirm('Approve this invoice?')) return;
        btn.disabled = true;
        btn.textContent = '...';
        var fd = new FormData();
        fd.append('action', 'approve_invoice');
        fd.append('invoice_db_id', invId);
        fetch('admin-affiliates.php', { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function() { location.reload(); })
            .catch(function() { btn.disabled = false; btn.textContent = 'Approve'; alert('Error approving invoice.'); });
    }
    var activeInvFilter = 'all';
    function filterInvoices(status, btn) {
        activeInvFilter = status;
        document.querySelectorAll('.inv-filter').forEach(function(b) {
            b.classList.remove('bg-indigo-600', 'text-white');
            b.classList.add('bg-gray-100', 'text-gray-600');
        });
        btn.classList.remove('bg-gray-100', 'text-gray-600');
        btn.classList.add('bg-indigo-600', 'text-white');
        filterInvoicesSearch();
    }
    function filterInvoicesSearch() {
        var q = (document.getElementById('invSearchInput')?.value || '').toLowerCase();
        document.querySelectorAll('.inv-card').forEach(function(card) {
            var matchStatus = activeInvFilter === 'all' || card.dataset.status === activeInvFilter;
            var matchSearch = !q || (card.dataset.invId || '').toLowerCase().indexOf(q) !== -1;
            card.style.display = (matchStatus && matchSearch) ? '' : 'none';
        });
    }
    function filterWd(status, btn) {
        document.querySelectorAll('.wd-filter').forEach(function(b) {
            b.classList.remove('bg-indigo-600', 'text-white');
            b.classList.add('bg-gray-100', 'text-gray-600');
        });
        btn.classList.remove('bg-gray-100', 'text-gray-600');
        btn.classList.add('bg-indigo-600', 'text-white');
        document.querySelectorAll('.wd-row').forEach(function(row) {
            row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
        });
    }
    function openViewInvoices(paymentId) {
        var body = document.getElementById('viewInvoicesBody');
        body.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-indigo-500"></i>';
        document.getElementById('viewInvoicesModal').classList.remove('hidden');
        activeInvFilter = 'all';
        fetch('admin-fetch-invoices.php?withdraw_id=' + paymentId)
            .then(function(r) { return r.text(); })
            .then(function(html) { body.innerHTML = html; })
            .catch(function() { body.innerHTML = '<span class="text-red-500">Failed to load invoices.</span>'; });
    }
    </script>

</body>
</html>
