<?php
/**
 * File: affiliate-forgot.php
 * Unified Affiliate Password Recovery Terminal.
 * Multi-stage secure wizard processing system utilizing Brevo transactional API dispatches.
 */
require_once 'config.php';
require_once 'mailer.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$status_msg = "";
$status_success = false;

// 1. STATE MACHINE RESOLVER: Track wizard milestones cleanly in active session bounds
if (!isset($_SESSION['reset_step'])) {
    $_SESSION['reset_step'] = 'request'; // Steps: request -> verify -> password
}

// 2. STAGE 1: HANDLE EMAIL REQUEST & OTP GENERATION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_request_otp'])) {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status_msg = "Error: Please provide a valid email format pattern.";
        $status_success = false;
    } else {
        try {
            // Verify affiliate record presence in db core tables
            $stmt = $pdo->prepare("SELECT `id`, `email` FROM `affiliates` WHERE `email` = ? LIMIT 1");
            $stmt->execute([$email]);
            $affiliate = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$affiliate) {
                // Security mitigation: fake success response to protect against enumeration scanning
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_otp']   = random_int(100000, 999999); 
                $_SESSION['reset_step']  = 'verify';
                $status_msg = "If the account exists, a 6-digit security code was dispatched onto your inbox layer.";
                $status_success = true;
            } else {
                // Core Execution: Account matched successfully. Generate structural numeric token string
                $generated_otp = random_int(100000, 999999);
                
                $_SESSION['reset_email'] = $affiliate['email'];
                $_SESSION['reset_aff_id']= (int)$affiliate['id'];
                $_SESSION['reset_otp']   = $generated_otp;
                $_SESSION['reset_step']  = 'verify';

                // Draft clear, high-priority dynamic HTML notification payload template card
                $subject = "Security Verification Code — Password Recovery Verification";
                $htmlBody = "
                    <div style='max-width:550px; margin:20px auto; font-family:sans-serif; border:1px solid #e5e7eb; padding:30px; border-radius:16px; background:#fff; text-align:left;'>
                        <h2 style='color:#111827; font-size:18px; font-weight:800; margin-bottom:12px;'>Password Recovery Request</h2>
                        <p style='color:#4b5563; font-size:13px; line-height:1.6;'>A verification event was initiated against this partner console profile. Use the 6-digit numeric sequence block below to authenticate your identity signature constraints:</p>
                        <div style='margin:24px 0; background:#eff6ff; text-align:center; padding:16px; border-radius:12px; font-family:monospace; font-size:26px; font-weight:800; tracking-wide:4px; color:#0072bc; border:1px dashed #bfdbfe;'>
                            {$generated_otp}
                        </div>
                        <p style='color:#9ca3af; font-size:11px; line-height:1.4; border-top:1px solid #f3f4f6; padding-top:12px; margin-top:20px;'>If you did not perform this security mapping route command parameter string change, please ignore this notice safely.</p>
                    </div>
                ";

                // Fire transactional mail engine
                $mail_result = sendTransactionalMail($affiliate['email'], $subject, $htmlBody);

                if ($mail_result['success']) {
                    $status_msg = "A 6-digit security validation token was dispatched onto your registered mailbox.";
                    $status_success = true;
                } else {
                    $status_msg = "Operational Fault: Token generation succeeded but mailer engine rejected processing: " . $mail_result['message'];
                    $status_success = false;
                }
            }
        } catch (Exception $e) {
            error_log("Affiliate Reset Matrix Fault: " . $e->getMessage());
            $status_msg = "Operational Break: Database connection failure running recovery verification loops.";
            $status_success = false;
        }
    }
}

// 3. STAGE 2: HANDLE CODE VERIFICATION LOOP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_verify_otp'])) {
    $user_otp_input = trim($_POST['otp'] ?? '');

    if (empty($user_otp_input) || !isset($_SESSION['reset_otp'])) {
        $status_msg = "Error: System processing variables dropped or invalid entry code provided.";
        $status_success = false;
    } elseif ((int)$user_otp_input === (int)$_SESSION['reset_otp']) {
        // Validation match resolved. Promote state matrix parameters up to password modifications access window
        $_SESSION['reset_step'] = 'password';
        $status_msg = "Identity successfully authorized. Provide your updated password specifications configuration profiles.";
        $status_success = true;
    } else {
        $status_msg = "Error: Validation code mismatch verified. Provide a valid 6-digit sequence parameter string.";
        $status_success = false;
    }
}

// 4. STAGE 3: COMMIT PASSWORD MODIFICATION METRIC PROFILES
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_reset_password'])) {
    $new_password  = $_POST['password'] ?? '';
    $conf_password = $_POST['conf_password'] ?? '';

    if (empty($new_password) || strlen($new_password) < 8) {
        $status_msg = "Error: System criteria demands that your password structure match a minimum metric of 8 character counts.";
        $status_success = false;
    } elseif ($new_password !== $conf_password) {
        $status_msg = "Error: Password confirmation arrays alignment error. Fields must match exactly.";
        $status_success = false;
    } elseif (!isset($_SESSION['reset_email'])) {
        $status_msg = "Error: Security context token missing. Please return to stage one loops.";
        $status_success = false;
    } else {
        try {
            // Apply password hashing calculations
            $password_hash_string = password_hash($new_password, PASSWORD_BCRYPT);
            
            // Execute mutation record update
            $update_stmt = $pdo->prepare("UPDATE `affiliates` SET `password` = ? WHERE `email` = ? LIMIT 1");
            $update_stmt->execute([$password_hash_string, $_SESSION['reset_email']]);

            // Flush recovery session configurations block parameters safely
            unset($_SESSION['reset_step'], $_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_aff_id']);
            
            $_SESSION['login_redirect_flash_msg'] = "Password configuration reestablished smoothly. Access console via updated signatures.";
            header("Location: affiliate-login");
            exit;
        } catch (PDOException $e) {
            error_log("Affiliate Password Finalize Reset Matrix Error: " . $e->getMessage());
            $status_msg = "Operational Break: Critical infrastructure save loop failure. Password reset aborted.";
            $status_success = false;
        }
    }
}

// 5. MANUAL RESET FLUSH FUNCTION: Let the partner drop out of active loops back into baseline states manually if desired
if (isset($_GET['abort_wizard'])) {
    unset($_SESSION['reset_step'], $_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_aff_id']);
    header("Location: affiliate-forgot");
    exit;
}

$current_wizard_step = $_SESSION['reset_step'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Account Recovery Module — PartnerTerminal</title>
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

    <main class="grow flex items-center justify-center p-4 w-full max-w-md mx-auto">
        <div class="w-full space-y-5 onload-anim">
            
            <?php if (!empty($status_msg)): ?>
                <div class="p-4 rounded-xl text-xs font-semibold border <?= $status_success ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-600' ?> text-left onload-anim onload-delay-100">
                    <i class="fa-solid <?= $status_success ? 'fa-circle-check text-[#0072bc]' : 'fa-circle-exclamation text-red-500' ?> mr-1.5 text-sm align-middle"></i>
                    <?= htmlspecialchars($status_msg) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-sm text-left space-y-5 onload-anim onload-delay-200">
                
                <?php if ($current_wizard_step === 'request'): ?>
                    <div class="space-y-1">
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Recover Account Access</h2>
                        <p class="text-xs text-gray-700 font-semibold leading-relaxed">Provide your locked registration email address token parameters. Our backend dispatcher loop will route a performance token to process verification metrics.</p>
                    </div>

                    <form method="POST" action="affiliate-forgot" class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Registered Partner Email</label>
                            <input type="email" name="email" id="email" required placeholder="partner@identity-network.com" 
                                   class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0072bc] focus:bg-white focus:ring-1 focus:ring-[#0072bc] transition-all font-semibold text-gray-900 placeholder-gray-400">
                        </div>
                        <button type="submit" name="action_request_otp" class="w-full bg-[#0072bc] hover:bg-[#005ea3] text-white font-bold text-sm py-4 px-4 rounded-xl transition-all cursor-pointer border border-transparent shadow-sm flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-xs"></i> Send Security Code
                        </button>
                    </form>

                <?php elseif ($current_wizard_step === 'verify'): ?>
                    <div class="space-y-1">
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Authorize Security Code</h2>
                        <p class="text-xs text-gray-700 font-semibold leading-relaxed">Input the 6-digit verification code token parsed out straight onto your external dynamic network address inbox folder row layout structures.</p>
                    </div>

                    <form method="POST" action="affiliate-forgot" class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">6-Digit Verification Code</label>
                            <input type="text" name="otp" required maxlength="6" pattern="\d{6}" placeholder="000000" 
                                   class="w-full text-center bg-gray-50 border border-gray-200 text-sm font-mono font-bold rounded-xl px-4 py-3.5 text-[#0072bc] outline-none focus:border-[#0072bc] focus:bg-white focus:ring-1 focus:ring-[#0072bc] transition-all tracking-widest">
                        </div>
                        <div class="space-y-2">
                            <button type="submit" name="action_verify_otp" class="w-full bg-[#0072bc] hover:bg-[#005ea3] text-white font-bold text-sm py-4 px-4 rounded-xl transition-all cursor-pointer border border-transparent shadow-sm flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-user-shield text-xs"></i> Validate Identity Match
                            </button>
                            <a href="affiliate-forgot?abort_wizard=1" class="block w-full text-center bg-gray-50 hover:bg-gray-100 text-gray-500 font-bold text-xs py-3.5 rounded-xl transition-all flex items-center justify-center gap-1">
                                <i class="fa-solid fa-rotate text-[10px]"></i> Request Alternative Token
                            </a>
                        </div>
                    </form>

                <?php elseif ($current_wizard_step === 'password'): ?>
                    <div class="space-y-1">
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Reconfigure Access Password</h2>
                        <p class="text-xs text-gray-700 font-semibold leading-relaxed">Update your signature credential arrays. Use structural encryption frameworks metrics guidelines to maintain configuration records safety.</p>
                    </div>

                    <form method="POST" action="affiliate-forgot" class="space-y-4">
                        <div class="space-y-3 text-left">
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">New Secure Password</label>
                                <input type="password" name="password" id="password" required placeholder="••••••••••••" 
                                       class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0072bc] focus:bg-white focus:ring-1 focus:ring-[#0072bc] transition-all font-semibold text-gray-900 placeholder-gray-400">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Confirm Password Fields</label>
                                <input type="password" name="conf_password" id="conf_password" required placeholder="••••••••••••" 
                                       class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#0072bc] focus:bg-white focus:ring-1 focus:ring-[#0072bc] transition-all font-semibold text-gray-900 placeholder-gray-400">
                            </div>
                        </div>
                        <button type="submit" name="action_reset_password" class="w-full bg-[#0072bc] hover:bg-[#005ea3] text-white font-bold text-sm py-4 px-4 rounded-xl transition-all cursor-pointer border border-transparent shadow-sm flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk text-xs"></i> Save Security Changes
                        </button>
                    </form>
                <?php endif; ?>

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
            <img src="<?php echo LOGO_URL; ?>" alt="Identity Search AI Logo" class="h-12 w-auto">
        </div>
        &copy; 2026 Identity Search AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>
