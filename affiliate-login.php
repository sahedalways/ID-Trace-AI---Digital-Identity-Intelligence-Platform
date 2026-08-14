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
    header("Location: affiliate-dashboard");
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
                } elseif ($affiliate['status'] === 'rejected') {
                    $message = "Your application has been rejected. This means you won't be able to use our platform.";
                    $status_type = "error";
                } elseif ($affiliate['status'] === 'active') {
                    // 4. Securely establish validation session state tokens
                    $_SESSION['affiliate_id'] = $affiliate['id'];
                    $_SESSION['affiliate_email'] = $affiliate['email'];
                    $_SESSION['affiliate_name'] = $affiliate['name'];

                    // 5. Execute secure directional redirect path to dashboard matrix index
                    header("Location: affiliate-dashboard");
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
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col selection:bg-[#0072bc] selection:text-white bg-slate-50 relative">

    <header id="mainNavbar" class="sticky top-0 z-50 bg-transparent transition-all duration-300">
        <?php include 'affiliate-navbar.php'; ?>
    </header>

    <!-- Full-width Background Decorations -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden" style="min-height: 100vh; background: linear-gradient(180deg, #BFE4FD 0%, #FFFFFF 100%);">
        <div class="blob-1 absolute top-0 left-1/2 w-[900px] h-[900px] bg-[#0072bc]/10 rounded-full blur-3xl opacity-60 -translate-x-1/2 will-change-transform"></div>
        <div class="blob-2 absolute top-24 -left-20 w-96 h-96 bg-[#0072bc]/15 rounded-full blur-3xl will-change-transform"></div>
        <div class="blob-3 absolute bottom-0 right-0 w-96 h-96 bg-[#BFE4FD]/50 rounded-full blur-3xl opacity-70 will-change-transform"></div>
    </div>

    <style>
        @keyframes blobMove1 {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            25% { transform: translateX(-40%) translateY(-20px); }
            50% { transform: translateX(-50%) translateY(-10px); }
            75% { transform: translateX(-60%) translateY(10px); }
        }
        @keyframes blobMove2 {
            0%, 100% { transform: translate(0, 0); }
            33% { transform: translate(30px, -15px); }
            66% { transform: translate(-20px, 20px); }
        }
        @keyframes blobMove3 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-40px, -20px); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .onload-anim {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        .onload-delay-100 { animation-delay: 100ms; }
        .onload-delay-200 { animation-delay: 200ms; }
        .onload-delay-300 { animation-delay: 300ms; }
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
            will-change: opacity, transform;
        }
        .animate-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .scroll-delay-100 { transition-delay: 100ms; }
        .scroll-delay-200 { transition-delay: 200ms; }
        .blob-1 { animation: blobMove1 18s ease-in-out infinite; }
        .blob-2 { animation: blobMove2 14s ease-in-out infinite; }
        .blob-3 { animation: blobMove3 16s ease-in-out infinite; }

        .navbar-scrolled {
            background: linear-gradient(to right, #eef5ff 20%, #0072bc 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        /* Smooth color transitions for all navbar links, buttons, and icons */
        #affiliateNavbar a,
        #affiliateNavbar button,
        #affiliateNavbar .text-slate-800,
        #affiliateNavbar .text-slate-900,
        #affiliateNavbar .text-slate-700,
        #affiliateNavbar .text-slate-600,
        #affiliateNavbar .text-slate-500,
        #affiliateNavbar .text-rose-600 {
            transition: color 0.35s ease, background-color 0.35s ease, border-color 0.35s ease;
        }

        /* When scrolled, switch navbar text and icons to white */
        #mainNavbar.navbar-scrolled #affiliateNavbar a:not(#mobileAffiliateDrawer a),
        #mainNavbar.navbar-scrolled #affiliateNavbar button:not(#mobileAffiliateDrawer button),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-800:not(#mobileAffiliateDrawer .text-slate-800),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-900:not(#mobileAffiliateDrawer .text-slate-900),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-700:not(#mobileAffiliateDrawer .text-slate-700),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-600:not(#mobileAffiliateDrawer .text-slate-600),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-slate-500:not(#mobileAffiliateDrawer .text-slate-500),
        #mainNavbar.navbar-scrolled #affiliateNavbar .text-rose-600:not(#mobileAffiliateDrawer .text-rose-600) {
            color: #ffffff !important;
        }

        /* Adjust hover states when scrolled for better contrast on dark gradient */
        #mainNavbar.navbar-scrolled #affiliateNavbar a:hover:not(#mobileAffiliateDrawer a),
        #mainNavbar.navbar-scrolled #affiliateNavbar button:hover:not(#mobileAffiliateDrawer button, #affiliateMobileMenuButton) {
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff !important;
        }

        /* Mobile hamburger: keep icon white and subtle glass highlight when scrolled */
        #mainNavbar.navbar-scrolled #affiliateNavbar #affiliateMobileMenuButton {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.25);
        }

        #mainNavbar.navbar-scrolled #affiliateNavbar #affiliateMobileMenuButton:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
        }

        /* Adjust mobile hamburger border when scrolled */
        #mainNavbar.navbar-scrolled #affiliateNavbar .border-blue-200\/60 {
            border-color: rgba(255, 255, 255, 0.25);
        }

        /* Adjust separator color when scrolled */
        #mainNavbar.navbar-scrolled #affiliateNavbar .bg-blue-300 {
            background-color: rgba(255, 255, 255, 0.3);
        }
    </style>

    <main class="grow flex items-center justify-center px-4 py-12 w-full">
        <div class="max-w-md w-full space-y-6 onload-anim">
            
            <div class="text-center space-y-2 onload-anim onload-delay-100">
                <div class="inline-flex p-3.5 bg-blue-50 text-[#0072bc] rounded-2xl border border-blue-100 text-2xl">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Welcome Back Partner</h2>
                <p class="text-xs text-gray-700 font-semibold">Sign in to monitor tracking links, metrics, and residual revenue balances.</p>
            </div>

            <div class="bg-white border border-gray-200 shadow-sm rounded-3xl p-6 sm:p-8 space-y-6 onload-anim onload-delay-200">
                
                <?php if (!empty($message)): ?>
                    <?php if ($status_type === 'success'): ?>
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex gap-3 text-left">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0 mt-0.5"></i>
                            <p class="text-xs text-emerald-800 font-semibold leading-relaxed"><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex gap-3 text-left">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base shrink-0 mt-0.5"></i>
                            <p class="text-xs text-amber-800 font-semibold leading-relaxed"><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="affiliate-login" method="POST" class="space-y-4 text-left">
                    
                    <div class="space-y-1.5">
                        <label for="email" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Account Email</label>
                        <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" placeholder="partner@domain.com" 
                            class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0072bc] focus:bg-white focus:ring-1 focus:ring-[#0072bc] transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <label for="password" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Secure Password</label>
                            <a href="affiliate-forgot" class="text-xs text-[#0072bc] font-bold hover:underline">Forgot Password?</a>
                        </div>
                        <input type="password" id="password" name="password" required placeholder="••••••••" 
                            class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0072bc] focus:bg-white focus:ring-1 focus:ring-[#0072bc] transition-all font-semibold text-gray-900 placeholder-gray-400">
                    </div>

                    <button type="submit" class="w-full bg-[#0072bc] hover:bg-[#005ea3] text-white font-bold text-sm py-4 px-4 rounded-xl transition-all shadow-sm mt-2 cursor-pointer border border-transparent">
                        Login as Affiliate
                    </button>
                </form>

                <div class="border-t border-gray-100 pt-4 text-center text-xs font-semibold">
                    <p class="text-gray-400">Not a network partner yet? <a href="affiliate-register" class="text-[#0072bc] font-bold hover:underline">Register as Affiliate</a></p>
                </div>
            </div>
        </div>
    </main>

    <script>
        // INTERSECTION OBSERVER FOR SCROLL ANIMATIONS
        document.addEventListener("DOMContentLoaded", () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };
            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            document.querySelectorAll('.animate-on-scroll').forEach((el) => {
                observer.observe(el);
            });
        });
    </script>

    <footer class="relative overflow-hidden w-full border-t border-gray-200 py-6 text-center text-xs text-gray-700 font-semibold">
        <div class="absolute inset-0 -z-10" style="background: linear-gradient(180deg, #ffffff 0%, #F0F8FF 40%, #EAF5FF 60%, #FFFFFF 100%);"></div>
        <div class="flex items-center justify-center gap-2 mb-2">
            <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
        </div>
        &copy; 2026 Identity Search AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>
