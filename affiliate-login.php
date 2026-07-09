<?php
/**
 * File: affiliate-login.php
 * Secure authentication gateway for affiliate partners.
 * Verifies credentials, evaluates account status limits, and initializes tracking sessions.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

// If an active session matrix is already verified, route straight to the dashboard
if (isset($_SESSION['affiliate_id'])) {
    header("Location: affiliate-dashboard.php");
    exit;
}

$message = '';
$status_type = ''; // 'error' or 'success'

// Capture external flash redirect parameters dropped by security reset modules
if (isset($_SESSION['login_redirect_flash_msg'])) {
    $message = $_SESSION['login_redirect_flash_msg'];
    $status_type = 'success';
    unset($_SESSION['login_redirect_flash_msg']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $message = "Please enter both your email address and password.";
        $status_type = "error";
    } else {
        try {
            // 1. Locate the affiliate account record by email
            $stmt = $pdo->prepare("SELECT * FROM `affiliates` WHERE `email` = ? LIMIT 1");
            $stmt->execute([$email]);
            $affiliate = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Validate password hashes match secure system metrics
            if ($affiliate && password_verify($password, $affiliate['password'])) {
                
                // 3. Evaluate conditional state machine constraints ('pending', 'active', 'banned')
                if ($affiliate['status'] === 'pending') {
                    $message = "Your application is currently under review. You will receive an automated validation email once your dashboard access status updates to active.";
                    $status_type = "error";
                } elseif ($affiliate['status'] === 'banned') {
                    $message = "This affiliate account has been suspended or banned for violating network traffic rules policy metrics.";
                    $status_type = "error";
                } elseif ($affiliate['status'] === 'active') {
                    // 4. Securely establish validation session state tokens
                    $_SESSION['affiliate_id'] = $affiliate['id'];
                    $_SESSION['affiliate_email'] = $affiliate['email'];
                    $_SESSION['affiliate_name'] = $affiliate['name'];

                    // 5. Execute secure directional redirect path to dashboard matrix index
                    header("Location: affiliate-dashboard.php");
                    exit;
                }
            } else {
                // Generic error block to prevent bad actors from checking if email exists
                $message = "Invalid email address or account access password configuration mapping.";
                $status_type = "error";
            }
        } catch (PDOException $e) {
            error_log("Affiliate Login Authentication Exception dropped: " . $e->getMessage());
            $message = "An internal authentication server fault occurred. Please try again later.";
            $status_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Partner Sign In — PartnerTerminal</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-[#f9fafb]">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="grow flex items-center justify-center px-4 py-12 w-full">
        <div class="max-w-md w-full space-y-6">
            
            <div class="text-center space-y-2">
                <div class="inline-flex p-3.5 bg-emerald-50 text-[#128c7e] rounded-2xl border border-emerald-100 text-2xl">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Welcome Back Partner</h2>
                <p class="text-xs text-gray-400 font-semibold">Sign in to monitor tracking links, metrics, and residual revenue balances.</p>
            </div>

            <div class="bg-white border border-gray-200 shadow-sm rounded-3xl p-6 sm:p-8 space-y-6">
                
                <?php if (!empty($message)): ?>
                    <?php if ($status_type === 'success'): ?>
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex gap-3 text-left">
                            <i class="fa-solid fa-circle-check text-[#128c7e] text-base shrink-0 mt-0.5"></i>
                            <p class="text-xs text-emerald-800 font-semibold leading-relaxed"><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex gap-3 text-left">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0 mt-0.5"></i>
                            <p class="text-xs text-amber-800 font-semibold leading-relaxed"><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="affiliate-login.php" method="POST" class="space-y-4 text-left">
                    
                    <div class="space-y-1.5">
                        <label for="email" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Account Email</label>
                        <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" placeholder="partner@domain.com" 
                            class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <label for="password" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Secure Password</label>
                            <a href="affiliate-forgot.php" class="text-xs text-[#128c7e] font-bold hover:underline">Forgot Password?</a>
                        </div>
                        <input type="password" id="password" name="password" required placeholder="••••••••" 
                            class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>

                    <button type="submit" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold text-sm py-4 px-4 rounded-xl transition-all shadow-sm mt-2 cursor-pointer border border-transparent">
                        Login as Affiliate
                    </button>
                </form>

                <div class="border-t border-gray-100 pt-4 text-center text-xs font-semibold">
                    <p class="text-gray-400">Not a network partner yet? <a href="affiliate-register.php" class="text-[#128c7e] font-bold hover:underline">Register as Affiliate</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer class="w-full text-center py-6 border-t border-gray-200 bg-white/50 backdrop-blur-sm text-xs font-semibold text-gray-400">
        &copy; 2026 Identity Search AI Affiliate Portal. All rights reserved. Developed and Designed by <a href="https://sahedahmed.netlify.app/" target="_blank" class="text-[#128c7e] font-bold">Enostation IT</a>.
    </footer>

</body>
</html>
