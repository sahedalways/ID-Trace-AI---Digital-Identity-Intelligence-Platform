<?php
/**
 * File: admin-client-edit.php
 * Admin page to edit customer profile details.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

$clientId = (int)($_GET['id'] ?? 0);
if (!$clientId) {
    header("Location: admin-clients.php");
    exit;
}

$success_msg = "";
$error_msg = "";

try {
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$clientId]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        header("Location: admin-clients.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Admin Client Edit Error: " . $e->getMessage());
    die("Error loading customer.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['form_action'] ?? '';

    if ($act === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $status = $_POST['status'] ?? $client['status'];
        $credit = (int)($_POST['credit'] ?? $client['credit']);

        if (empty($name) || empty($email)) {
            $error_msg = "Name and email are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "Invalid email address.";
        } else {
            $checkStmt = $pdo->prepare("SELECT id FROM `users` WHERE `email` = ? AND `id` != ? LIMIT 1");
            $checkStmt->execute([$email, $clientId]);
            if ($checkStmt->fetch()) {
                $error_msg = "Email is already in use by another customer.";
            } else {
                $upStmt = $pdo->prepare("UPDATE `users` SET `name` = ?, `email` = ?, `country` = ?, `status` = ?, `credit` = ? WHERE `id` = ?");
                $upStmt->execute([$name, $email, $country, $status, $credit, $clientId]);
                $_SESSION['flash_success'] = "Customer profile updated successfully.";
                header("Location: admin-client-edit.php?id=$clientId");
                exit;
            }
        }
    }

    if ($act === 'reset_password') {
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($newPass) < 6) {
            $error_msg = "Password must be at least 6 characters.";
        } elseif ($newPass !== $confirm) {
            $error_msg = "Passwords do not match.";
        } else {
            $hashed = password_hash($newPass, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?")->execute([$hashed, $clientId]);
            $_SESSION['flash_success'] = "Password has been reset successfully.";
            header("Location: admin-client-edit.php?id=$clientId");
            exit;
        }
    }
}

if (isset($_SESSION['flash_success'])) {
    $success_msg = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Customer #<?= $clientId ?> — Admin Panel</title>
    <?php include 'admin-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased bg-[#f8fafc]">

    <?php include 'admin-sidebar.php'; ?>
    <?php include 'admin-navbar.php'; ?>

    <div id="sidebarContent" class="lg:ml-64 pt-16 min-h-screen">
        <main class="p-4 sm:p-6 space-y-6 max-w-4xl">

            <div class="flex items-center gap-3">
                <a href="admin-client-detail.php?id=<?= $clientId ?>" class="text-gray-400 hover:text-gray-900 transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-gray-900">Edit Customer #<?= $clientId ?></h1>
                    <p class="text-xs text-gray-400">Update profile information and manage security credentials.</p>
                </div>
            </div>

            <!-- Profile Edit -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-5 flex items-center gap-1.5">
                    <i class="fa-solid fa-user-pen text-indigo-600"></i> Profile Information
                </h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="form_action" value="update_profile">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Full Name</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($client['name'] ?? '') ?>" required
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Country</label>
                            <input type="text" name="country" value="<?= htmlspecialchars($client['country'] ?? '') ?>"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Status</label>
                            <select name="status" class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900 cursor-pointer">
                                <option value="active" <?= ($client['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= ($client['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Credits</label>
                            <input type="number" name="credit" value="<?= (int)($client['credit'] ?? 0) ?>" min="0"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Plan</label>
                            <input type="text" value="<?= htmlspecialchars($client['plan'] ?? 'None') ?>" readonly
                                class="w-full text-sm px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-xl font-semibold text-gray-500 cursor-not-allowed">
                        </div>
                    </div>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Save Changes
                    </button>
                </form>
            </div>

            <!-- Reset Password -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 mb-5 flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-amber-600"></i> Reset Password
                </h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="form_action" value="reset_password">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">New Password</label>
                            <input type="password" name="new_password" required minlength="6" placeholder="Minimum 6 characters"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-amber-500 transition font-semibold text-gray-900">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1 block">Confirm Password</label>
                            <input type="password" name="confirm_password" required minlength="6" placeholder="Repeat password"
                                class="w-full text-sm px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-amber-500 transition font-semibold text-gray-900">
                        </div>
                    </div>

                    <button type="submit" onclick="return confirm('Are you sure you want to reset this customer\'s password?')" class="bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs py-2.5 px-6 rounded-xl transition cursor-pointer">
                        Reset Password
                    </button>
                </form>
            </div>

        </main>
    </div>

    <?php
    $alert_type = !empty($error_msg) ? 'error' : (!empty($success_msg) ? 'success' : '');
    $alert_message = !empty($error_msg) ? $error_msg : $success_msg;
    ?>
    <?php include 'alert-modal.php'; ?>

</body>
</html>
