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
                        <div style='margin:24px 0; background:#f0fdf4; text-align:center; padding:16px; border-radius:12px; font-family:monospace; font-size:26px; font-weight:800; tracking-wide:4px; color:#128c7e; border:1px dashed #bbf7d0;'>
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
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-[#f9fafb]">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="grow flex items-center justify-center p-4 w-full max-w-md mx-auto">
        <div class="w-full space-y-5">
            
            <?php if (!empty($status_msg)): ?>
                <div class="p-4 rounded-xl text-xs font-semibold border <?= $status_success ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-600' ?> text-left">
                    <i class="fa-solid <?= $status_success ? 'fa-circle-check text-[#128c7e]' : 'fa-circle-exclamation text-red-500' ?> mr-1.5 text-sm align-middle"></i>
                    <?= htmlspecialchars($status_msg) ?>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-gray-200 rounded-3xl p-6 sm:p-8 shadow-sm text-left space-y-5">
                
                <?php if ($current_wizard_step === 'request'): ?>
                    <div class="space-y-1">
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Recover Account Access</h2>
                        <p class="text-xs text-gray-400 font-semibold leading-relaxed">Provide your locked registration email address token parameters. Our backend dispatcher loop will route a performance token to process verification metrics.</p>
                    </div>

                    <form method="POST" action="affiliate-forgot" class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Registered Partner Email</label>
                            <input type="email" name="email" id="email" required placeholder="partner@identity-network.com" 
                                   class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400">
                        </div>
                        <button type="submit" name="action_request_otp" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold text-sm py-4 px-4 rounded-xl transition-all cursor-pointer border border-transparent shadow-sm flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-xs"></i> Send Security Code
                        </button>
                    </form>

                <?php elseif ($current_wizard_step === 'verify'): ?>
                    <div class="space-y-1">
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Authorize Security Code</h2>
                        <p class="text-xs text-gray-400 font-semibold leading-relaxed">Input the 6-digit verification code token parsed out straight onto your external dynamic network address inbox folder row layout structures.</p>
                    </div>

                    <form method="POST" action="affiliate-forgot" class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">6-Digit Verification Code</label>
                            <input type="text" name="otp" required maxlength="6" pattern="\d{6}" placeholder="000000" 
                                   class="w-full text-center bg-gray-50 border border-gray-200 text-xl font-mono font-bold rounded-xl px-4 py-3.5 text-[#128c7e] outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all tracking-widest">
                        </div>
                        <div class="space-y-2">
                            <button type="submit" name="action_verify_otp" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold text-sm py-4 px-4 rounded-xl transition-all cursor-pointer border border-transparent shadow-sm flex items-center justify-center gap-1.5">
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
                        <p class="text-xs text-gray-400 font-semibold leading-relaxed">Update your signature credential arrays. Use structural encryption frameworks metrics guidelines to maintain configuration records safety.</p>
                    </div>

                    <form method="POST" action="affiliate-forgot" class="space-y-4">
                        <div class="space-y-3 text-left">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">New Secure Password</label>
                                <input type="password" name="password" id="password" required placeholder="••••••••••••" 
                                       class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Confirm Password Fields</label>
                                <input type="password" name="conf_password" id="conf_password" required placeholder="••••••••••••" 
                                       class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400">
                            </div>
                        </div>
                        <button type="submit" name="action_reset_password" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold text-sm py-4 px-4 rounded-xl transition-all cursor-pointer border border-transparent shadow-sm flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk text-xs"></i> Save Security Changes
                        </button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <footer class="w-full text-center py-6 border-t border-gray-200 bg-white/50 backdrop-blur-sm text-xs font-semibold text-gray-400">
        <div class="flex items-center justify-center gap-2 mb-2">
            <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
        </div>
        &copy; 2026 Identity Search AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>