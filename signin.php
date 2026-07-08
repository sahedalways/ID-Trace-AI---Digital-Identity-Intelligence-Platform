<?php
/**
 * OSINT Universal Intelligence Console — Secure Dynamic Authentication Terminal
 * File: signin.php
 */
require_once 'config.php';       // Pulls defined application constants safely
require_once 'auth_handler.php'; // Triggers AJAX interception if required

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- LOGGED-IN SESSION PROTECTION INTERCEPTOR ---
// If the user is already authenticated, redirect them instantly to the home node
if (isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

// Extract the dynamic return path path explicitly from the URL parameter string
$return_path = isset($_GET['return']) ? trim($_GET['return']) : '/index.php';

// Security sanity check: Ensure it is a relative path starting with '/' to prevent open-redirect vulnerabilities
if (empty($return_path) || strpos($return_path, '/') !== 0) {
    $return_path = '/index.php';
}

// Catch incoming email URL parameters to prepopulate identity inputs
$prefilled_email = isset($_GET['email']) ? trim($_GET['email']) : '';

// Dynamically reference the initialized authentication gateway routing script with the encoded path
$google_auth_url = BASE_URL . "auth_google.php?return=" . urlencode($return_path); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign in or create an account — Identity Trace AI</title>
    <?php include 'head.php'; ?>

    <style>
        .fade-in-up { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .otp-box::-webkit-outer-spin-button, .otp-box::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .otp-box { -moz-appearance: textfield; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white">

    <?php include 'navbar.php'; ?>

    <main class="w-full mx-auto max-w-[440px] px-6 py-12 flex-grow flex flex-col justify-center fade-in-up">
        
        <div class="flex flex-col items-center justify-center mb-8 text-center select-none">
            <span class="text-[#128c7e] w-16 h-16 flex items-center justify-center shrink-0 animate-fingerprint">
                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.10008 21.221C6.71021 19.2375 5.89258 16.8243 5.89258 14.2187C5.89258 10.8443 8.6265 8.10938 11.9989 8.10938C15.3712 8.10938 18.1051 10.8443 18.1051 14.2187" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8.10008 21.221C6.71021 19.2375 5.89258 16.8243 5.89258 14.2187C5.89258 10.8443 8.6265 8.10938 11.9989 8.10938C15.3712 8.10938 18.1051 10.8443 18.1051 14.2187" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.4359 20.3118C18.3259 20.3179 18.218 20.3281 18.107 20.3281C14.7347 20.3281 12.0007 17.5931 12.0007 14.2188" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.4361 20.3118C18.3262 20.3179 18.2182 20.3281 18.1073 20.3281C14.7349 20.3281 12.001 17.5931 12.001 14.2188" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.2694 21.9999C10.675 20.382 8.94705 17.5024 8.94705 14.2187C8.94705 12.5315 10.3145 11.164 12.0007 11.164C13.6869 11.164 15.0543 12.5315 15.0543 14.2187C15.0543 15.9059 16.4218 17.2733 18.108 17.2733C19.7942 17.2733 21.1616 15.9059 21.1616 14.2187C21.1616 9.1571 17.0602 5.05469 12.0017 5.05469C6.94319 5.05469 2.8418 9.1571 2.8418 14.2187C2.8418 15.3469 2.96806 16.4455 3.20021 17.5045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.2694 21.9999C10.675 20.382 8.94705 17.5024 8.94705 14.2187C8.94705 12.5315 10.3145 11.164 12.0007 11.164C13.6869 11.164 15.0543 12.5315 15.0543 14.2187C15.0543 15.9059 16.4218 17.2733 18.108 17.2733C19.7942 17.2733 21.1616 15.9059 21.1616 14.2187C21.1616 9.1571 17.0602 5.05469 12.0017 5.05469C6.94319 5.05469 2.8418 9.1571 2.8418 14.2187C2.8418 15.3469 2.96806 16.4455 3.20021 17.5045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.5257 5.86313C18.4435 3.4978 15.399 2 12.0002 2C8.60136 2 5.55687 3.4978 3.47461 5.86313" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.5257 5.86313C18.4435 3.4978 15.399 2 12.0002 2C8.60136 2 5.55687 3.4978 3.47461 5.86313" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>

        <div id="runtimeFeedbackMsg" class="hidden mb-5 p-3.5 rounded-xl border text-sm font-medium text-center transition-all duration-300"></div>

        <div id="stageIdentityBlock" class="block space-y-6">
            <div class="text-center">
                <h2 class="text-[22px] font-black text-gray-900 tracking-tight leading-tight mb-2">Sign in or create an account</h2>
                <p class="text-[13px] text-gray-500 font-medium leading-relaxed max-w-[320px] mx-auto">Sign in to continue. If you don't have an account, you will be prompted to create one.</p>
            </div>

            <form id="emailSubmissionForm" class="space-y-3">
                <div class="relative">
                    <input type="email" id="emailField" placeholder="Enter your email address" class="w-full bg-white border border-gray-200 focus:border-[#128c7e] focus:ring-4 focus:ring-emerald-50 rounded-xl outline-none text-gray-900 text-base font-semibold px-4 py-3.5 placeholder-gray-400 transition shadow-sm" value="<?php echo htmlspecialchars($prefilled_email, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>
                <button type="submit" id="emailSubmitBtn" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold py-3.5 px-4 rounded-xl transition text-[15px] tracking-wide shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <span>Continue with email</span>
                </button>
            </form>

            <div class="relative flex items-center my-6"><div class="flex-grow border-t border-gray-200"></div><span class="flex-shrink mx-3 text-xs text-gray-400 font-normal lowercase">or</span><div class="flex-grow border-t border-gray-200"></div></div>

            <a href="<?php echo htmlspecialchars($google_auth_url); ?>" class="w-full bg-white hover:bg-gray-50 text-gray-800 font-bold py-3.5 px-4 rounded-xl border border-gray-200 shadow-sm transition text-[15px] flex items-center justify-center gap-2.5 group">
                <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                </svg>
                <span>Continue with Google</span>
            </a>
        </div>

        <div id="stageVerificationBlock" class="hidden space-y-6">
            <div class="text-center">
                <h2 class="text-[22px] font-black text-gray-900 tracking-tight leading-tight mb-2">Enter verification code</h2>
                <p class="text-[13px] text-gray-500 font-medium leading-relaxed max-w-[340px] mx-auto">We've dispatched an email containing a secure 6-digit confirmation key to <span id="targetEmailMirror" class="font-bold text-gray-700"></span>.</p>
            </div>

            <form id="otpVerificationForm" class="space-y-6">
                <div class="flex items-center justify-between gap-2 px-1" id="otpInputsCluster">
                    <input type="number" min="0" max="9" maxlength="1" class="otp-box w-12 h-14 bg-white border border-gray-200 focus:border-[#128c7e] focus:ring-4 focus:ring-emerald-50 rounded-xl outline-none text-center text-xl font-bold text-gray-900 shadow-sm transition" required>
                    <input type="number" min="0" max="9" maxlength="1" class="otp-box w-12 h-14 bg-white border border-gray-200 focus:border-[#128c7e] focus:ring-4 focus:ring-emerald-50 rounded-xl outline-none text-center text-xl font-bold text-gray-900 shadow-sm transition" required disabled>
                    <input type="number" min="0" max="9" maxlength="1" class="otp-box w-12 h-14 bg-white border border-gray-200 focus:border-[#128c7e] focus:ring-4 focus:ring-emerald-50 rounded-xl outline-none text-center text-xl font-bold text-gray-900 shadow-sm transition" required disabled>
                    <input type="number" min="0" max="9" maxlength="1" class="otp-box w-12 h-14 bg-white border border-gray-200 focus:border-[#128c7e] focus:ring-4 focus:ring-emerald-50 rounded-xl outline-none text-center text-xl font-bold text-gray-900 shadow-sm transition" required disabled>
                    <input type="number" min="0" max="9" maxlength="1" class="otp-box w-12 h-14 bg-white border border-gray-200 focus:border-[#128c7e] focus:ring-4 focus:ring-emerald-50 rounded-xl outline-none text-center text-xl font-bold text-gray-900 shadow-sm transition" required disabled>
                    <input type="number" min="0" max="9" maxlength="1" class="otp-box w-12 h-14 bg-white border border-gray-200 focus:border-[#128c7e] focus:ring-4 focus:ring-emerald-50 rounded-xl outline-none text-center text-xl font-bold text-gray-900 shadow-sm transition" required disabled>
                </div>
                <button type="submit" id="otpSubmitBtn" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold py-3.5 px-4 rounded-xl transition text-[15px] tracking-wide shadow-sm flex items-center justify-center cursor-pointer">Verify code</button>
            </form>

            <div class="text-center text-xs text-gray-500 font-semibold">Didn't receive the email? <button type="button" id="resendOtpBtn" class="text-[#128c7e] hover:text-[#0e6f64] underline ml-0.5 font-bold transition cursor-pointer">Resend code</button></div>
        </div>

        <div id="stageNameCollectionBlock" class="hidden space-y-6">
            <div class="text-center">
                <h2 class="text-[22px] font-black text-gray-900 tracking-tight leading-tight mb-2">Tell us your name</h2>
                <p class="text-[13px] text-gray-500 font-medium leading-relaxed max-w-[320px] mx-auto">It looks like you're new here! Let us know what to call you to set up your account.</p>
            </div>

            <form id="nameCollectionForm" class="space-y-3">
                <div class="relative">
                    <input type="text" id="nameField" placeholder="Enter your full name" class="w-full bg-white border border-gray-200 focus:border-[#128c7e] focus:ring-4 focus:ring-emerald-50 rounded-xl outline-none text-gray-900 text-base font-semibold px-4 py-3.5 placeholder-gray-400 transition shadow-sm" required minlength="2">
                </div>
                <button type="submit" id="nameSubmitBtn" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold py-3.5 px-4 rounded-xl transition text-[15px] tracking-wide shadow-sm flex items-center justify-center cursor-pointer">
                    <span>Complete Registration</span>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-500 font-semibold leading-relaxed mt-6">By continuing, you agree with our <a href="terms.php" class="underline text-gray-700 hover:text-gray-900 transition">Terms</a> and <a href="privacy.php" class="underline text-gray-700 hover:text-gray-900 transition">Privacy Policy</a>.</p>
    </main>

    <footer class="w-full border-t border-gray-200/60 bg-white/50 backdrop-blur-sm">
        <div class="max-w-6xl w-full mx-auto px-4 sm:px-6 py-6 text-center text-xs font-semibold text-gray-500">
            <p>&copy; 2026 Identity Trace AI. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const emailInp = document.getElementById("emailField");
            const emailFrm = document.getElementById("emailSubmissionForm");
            
            if (emailInp && emailInp.value.trim() !== "") {
                // Safe buffer timeout ensures global listeners inside auth_ui_flow.js are mounted
                setTimeout(() => {
                    if (emailFrm.checkValidity()) {
                        // Create and dispatch a native submit event to trigger your AJAX listener
                        const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                        emailFrm.dispatchEvent(submitEvent);
                        
                        // Update UI button state dynamically to indicate the automated background process
                        const submitBtn = document.getElementById("emailSubmitBtn");
                        if (submitBtn) {
                            submitBtn.innerHTML = '<span>Sending verification code...</span>';
                            submitBtn.disabled = true;
                        }
                    }
                }, 150);
            }
        });
    </script>
    <script src="auth_ui_flow.js?v=<?php echo time(); ?>"></script>
</body>
</html>