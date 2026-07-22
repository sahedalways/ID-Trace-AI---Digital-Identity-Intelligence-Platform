<?php
/**
 * File: admin-clients.php
 * Admin customers — paginated table with search + subscription filters.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
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

$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(u.email LIKE ? OR u.name LIKE ? OR u.id = ? OR a.aid LIKE ? OR a.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = $search;
    $params[] = "%$search%";
    $params[] = "%$search%";
}

switch ($subFilter) {
    case 'active_sub':
        $conditions[] = "u.stripe_subscription_id IS NOT NULL AND u.stripe_subscription_id != ''";
        break;
    case 'no_sub':
        $conditions[] = "(u.plan IS NULL OR u.plan = '')";
        break;
    case 'cancelled':
        $conditions[] = "u.plan IS NOT NULL AND u.plan != '' AND (u.stripe_subscription_id IS NULL OR u.stripe_subscription_id = '')";
        break;
    case 'chargeback':
        $conditions[] = "t.dispute_status = 1";
        break;
}

$whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

try {
    // Count
    $countSql = "SELECT COUNT(DISTINCT u.id) FROM `users` u
        LEFT JOIN (SELECT uid, MAX(affid) as affid FROM `conversions` WHERE affid IS NOT NULL GROUP BY uid) c ON c.uid = u.id
        LEFT JOIN (SELECT id, aid, email FROM `affiliates`) a ON c.affid = a.id
        LEFT JOIN `transactions` t ON t.uid = u.id $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRows = (int)$countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalRows / $perPage));

    $sql = "
        SELECT u.*,
               a.name as aff_name, a.email as aff_email, a.aid,
               t.dispute_status, t.dispute_amount
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
        $whereClause
        ORDER BY u.created_at DESC
        LIMIT $perPage OFFSET $offset
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clients = $stmt->fetchAll();

    // Summary counts
    $totalActive = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE stripe_subscription_id IS NOT NULL AND stripe_subscription_id != ''")->fetchColumn();
    $totalNoSub = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE plan IS NULL OR plan = ''")->fetchColumn();
    $totalCancelled = (int)$pdo->query("SELECT COUNT(*) FROM `users` WHERE plan IS NOT NULL AND plan != '' AND (stripe_subscription_id IS NULL OR stripe_subscription_id = '')")->fetchColumn();
    $totalChargeback = (int)$pdo->query("SELECT COUNT(DISTINCT uid) FROM `transactions` WHERE dispute_status = 1")->fetchColumn();
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM `users`")->fetchColumn();

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
    header("Location: admin-clients.php?sub=" . urlencode($subFilter));
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
                <form method="GET" class="flex items-center gap-2 flex-1 max-w-lg">
                    <input type="hidden" name="sub" value="<?= htmlspecialchars($subFilter) ?>">
                    <div class="flex-1 relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Search by email, name, user ID, affiliate ID or affiliate email..."
                            class="w-full text-sm pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-3 px-5 rounded-xl transition-all cursor-pointer">Search</button>
                    <?php if (!empty($search)): ?>
                        <a href="admin-clients.php?sub=<?= $subFilter ?>" class="text-xs font-bold text-gray-500 hover:text-gray-900 px-2">Clear</a>
                    <?php endif; ?>
                </form>

                <div class="flex flex-wrap items-center gap-2 lg:ml-auto">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-1">Status:</span>
                    <?php foreach (['all'=>'All','active_sub'=>'Active Sub','no_sub'=>'No Sub','cancelled'=>'Cancelled','chargeback'=>'Chargeback'] as $key => $label): ?>
                        <?php $cnt = ($key === 'all') ? $totalUsers : (($key === 'active_sub') ? $totalActive : (($key === 'no_sub') ? $totalNoSub : (($key === 'cancelled') ? $totalCancelled : $totalChargeback))); ?>
                        <a href="?q=<?= urlencode($search) ?>&sub=<?= $key ?>" class="text-[11px] font-bold px-3 py-1.5 rounded-lg transition-all <?= $subFilter === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' ?>"><?= $label ?> (<?= number_format($cnt) ?>)</a>
                    <?php endforeach; ?>
                </div>
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
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Plan</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Subscription</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Joined</th>
                                <th class="text-[10px] font-bold text-gray-400 uppercase tracking-wider px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($clients)): ?>
                                <tr><td colspan="8" class="text-xs text-gray-400 py-8 text-center font-semibold">No customers found.</td></tr>
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
                                    <td class="px-5 py-3 text-xs font-semibold text-gray-700"><?= htmlspecialchars($c['plan'] ?? '—') ?></td>
                                    <td class="px-5 py-3">
                                        <?php if ($c['dispute_status'] == 1): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-red-50 border border-red-100 text-red-700 px-2 py-0.5 rounded-md">Chargeback</span>
                                        <?php elseif (!empty($c['stripe_subscription_id'])): ?>
                                            <span class="inline-flex items-center text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-700 px-2 py-0.5 rounded-md">Active</span>
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
                                            <div class="hidden absolute right-0 z-50 mt-1 w-40 bg-white border border-gray-200 rounded-xl shadow-lg py-1.5 origin-top-right dropdown-menu">
                                                <a href="admin-client-detail.php?id=<?= $c['id'] ?>" class="flex items-center gap-2 px-3.5 py-2 text-[11px] font-semibold text-gray-700 hover:bg-gray-50 transition">
                                                    <i class="fa-solid fa-eye text-[10px] text-blue-500"></i> View
                                                </a>
                                                <a href="admin-client-edit.php?id=<?= $c['id'] ?>" class="flex items-center gap-2 px-3.5 py-2 text-[11px] font-semibold text-gray-700 hover:bg-gray-50 transition">
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
