<?php

/**
 * File: admin-login.php
 * Admin panel authentication gateway.
 * Verifies admin credentials, checks account status, and initializes admin session.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['admin_id'])) {
    header("Location: admin-dashboard.php");
    exit;
}

$message = '';
$status_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
        $status_type = "error";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM `admins` WHERE `email` = ? LIMIT 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {

                if ($admin['status'] === 'inactive') {
                    $message = "Your admin account is currently inactive. Contact the superadmin.";
                    $status_type = "error";
                } elseif ($admin['status'] === 'banned') {
                    $message = "This admin account has been banned.";
                    $status_type = "error";
                } elseif ($admin['status'] === 'active') {
                    $updateStmt = $pdo->prepare("UPDATE `admins` SET `last_login` = NOW() WHERE `id` = ?");
                    $updateStmt->execute([$admin['id']]);

                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_role'] = $admin['role'];

                    header("Location: admin-dashboard.php");
                    exit;
                }
            } else {
                $message = "Invalid email or password.";
                $status_type = "error";
            }
        } catch (PDOException $e) {
            error_log("Admin Login Error: " . $e->getMessage());
            $message = "An internal authentication error occurred. Please try again later.";
            $status_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Login — ID Trace AI</title>
    <?php include 'admin-head.php'; ?>
</head>

<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between selection:bg-indigo-500 selection:text-white bg-[#f8fafc]">

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0">
                    <a href="admin-login.php" class="flex items-center gap-2.5">
                        <img src="public/logo.png" alt="Identity Search AI Logo" class="h-10 w-auto">
                    </a>
                </div>
                <a href="index.php" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Back to Site
                </a>
            </div>
        </div>
    </nav>

    <main class="grow flex items-center justify-center px-4 py-12 w-full">
        <div class="max-w-md w-full space-y-6">

            <div class="text-center space-y-2">
                <div class="inline-flex p-3.5 bg-indigo-50 text-indigo-600 rounded-2xl border border-indigo-100 text-2xl">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Admin Access</h2>
                <p class="text-xs text-gray-400 font-semibold">Authenticate with your admin credentials to access the control panel.</p>
            </div>

            <div class="bg-white border border-gray-200 shadow-sm rounded-3xl p-6 sm:p-8 space-y-6">

                <?php if (!empty($message)): ?>
                    <?php if ($status_type === 'success'): ?>
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex gap-3 text-left">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0 mt-0.5"></i>
                            <p class="text-xs text-emerald-800 font-semibold leading-relaxed"><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="bg-red-50 border border-red-100 rounded-xl p-4 flex gap-3 text-left">
                            <i class="fa-solid fa-circle-exclamation text-red-600 text-base shrink-0 mt-0.5"></i>
                            <p class="text-xs text-red-800 font-semibold leading-relaxed"><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="admin-login.php" method="POST" class="space-y-4 text-left">

                    <div class="space-y-1.5">
                        <label for="email" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Admin Email</label>
                        <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" placeholder="Enter email address"
                            class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>

                    <div class="space-y-1.5">
                        <label for="password" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter password"
                            class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 focus:bg-white focus:ring-1 focus:ring-indigo-500 transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm py-4 px-4 rounded-xl transition-all shadow-sm mt-2 cursor-pointer border border-transparent">
                        Sign In to Admin Panel
                    </button>
                </form>
            </div>
        </div>
    </main>

    <footer class="w-full text-center py-6 border-t border-gray-200 bg-white/50 backdrop-blur-sm text-xs font-semibold text-gray-400">
        <div class="flex items-center justify-center gap-2 mb-2">
            <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
        </div>
        &copy; <?= date('Y'); ?> Identity Search AI Admin Panel. Restricted Access.
    </footer>

</body>

</html>
