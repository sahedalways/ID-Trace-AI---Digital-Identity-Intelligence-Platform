<?php
/**
 * Identity Search AI — Secure Test Client Login Terminal
 * File: test-login.php
 * Purpose: Dedicated email + password login gateway for the pre-seeded test client account.
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- ALREADY AUTHENTICATED INTERCEPTOR ---
// If the tester is already signed in, route them straight into the user panel
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true && isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "index");
    exit;
}

// Capture PRG flash message for a single render pass
$login_error = $_SESSION['test_login_error'] ?? '';
unset($_SESSION['test_login_error']);

// Pre-fill email on failed attempts
$prefill_email = $_SESSION['test_login_email'] ?? '';
unset($_SESSION['test_login_email']);

// -------------------------------------------------------------------------
// HANDLE EMAIL + PASSWORD AUTHENTICATION SUBMISSION
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_email    = trim($_POST['email'] ?? '');
    $input_password = trim($_POST['password'] ?? '');

    if (empty($input_email) || empty($input_password)) {
        $_SESSION['test_login_error'] = "Please enter both your email address and password.";
        header("Location: " . BASE_URL . "test-login");
        exit;
    }

    if (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['test_login_error'] = "The email address format is invalid. Please check and try again.";
        header("Location: " . BASE_URL . "test-login");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT `id`, `name`, `email`, `avatar`, `country`, `password`, `status`, `credit` FROM `users` WHERE `email` = :email LIMIT 1");
        $stmt->execute([':email' => $input_email]);
        $user = $stmt->fetch();

        if (!$user || empty($user['password']) || !password_verify($input_password, $user['password'])) {
            $_SESSION['test_login_error'] = "Invalid credentials. Please verify your email and password and try again.";
            $_SESSION['test_login_email'] = $input_email;
            header("Location: " . BASE_URL . "test-login");
            exit;
        }

        if ($user['status'] !== 'active') {
            $_SESSION['test_login_error'] = "This account is currently inactive. Please contact support.";
            $_SESSION['test_login_email'] = $input_email;
            header("Location: " . BASE_URL . "test-login");
            exit;
        }

        // Establish the standard authenticated application session state
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id']        = (int)$user['id'];
        $_SESSION['user_email']     = $user['email'];
        $_SESSION['user_name']      = $user['name'];
        $_SESSION['user_avatar']    = $user['avatar'];
        $_SESSION['user_country']   = $user['country'];

        // Record login fingerprint (IP, device, browser, user agent)
        recordLoginSession($pdo, $user['id']);

        header("Location: " . BASE_URL . "index");
        exit;
    } catch (Exception $e) {
        $_SESSION['test_login_error'] = "System error during authentication: " . $e->getMessage();
        header("Location: " . BASE_URL . "test-login");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Test Login — Identity Search AI</title>
    <?php include 'head.php'; ?>

    <style>
        .fade-in-up { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

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
        .blob-1 { animation: blobMove1 18s ease-in-out infinite; }
        .blob-2 { animation: blobMove2 14s ease-in-out infinite; }
        .blob-3 { animation: blobMove3 16s ease-in-out infinite; }

        .navbar-scrolled {
            background: linear-gradient(to right, #eef5ff 20%, #0072bc 100%);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        #mainNavbar #siteNavbar {
            background: transparent !important;
            border-bottom: none !important;
            box-shadow: none !important;
        }
        #siteNavbar a,
        #siteNavbar button,
        #siteNavbar .text-black,
        #siteNavbar .text-slate-400,
        #siteNavbar .text-\[\#0072bc\] {
            transition: color 0.35s ease, background-color 0.35s ease, border-color 0.35s ease;
        }
        #mainNavbar.navbar-scrolled #siteNavbar a:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:not(#userDropdownMenu button, #mobileDrawer button),
        #mainNavbar.navbar-scrolled #siteNavbar .text-black:not(#userDropdownMenu, #mobileDrawer),
        #mainNavbar.navbar-scrolled #siteNavbar .text-slate-400:not(#userDropdownMenu .text-slate-400, #mobileDrawer .text-slate-400),
        #mainNavbar.navbar-scrolled #siteNavbar .text-\[\#0072bc\]:not(#userDropdownMenu .text-\[\#0072bc\], #mobileDrawer .text-\[\#0072bc\]) {
            color: #ffffff !important;
        }
        #mainNavbar.navbar-scrolled #siteNavbar a:hover:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:hover:not(#userDropdownMenu button, #mobileDrawer button, #mobileMenuButton) {
            color: #ffffff !important;
        }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton {
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.25);
        }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton:hover,
        #mainNavbar.navbar-scrolled #siteNavbar #userMenuButton:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#0072bc] selection:text-white bg-slate-50 relative">

    <!-- Sticky Glassmorphic Navbar Container -->
    <header id="mainNavbar" class="sticky top-0 z-50 bg-transparent transition-all duration-300">
        <?php include 'navbar.php'; ?>
    </header>

    <!-- Full-width Background Decorations -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden" style="min-height: 100vh; background: linear-gradient(180deg, #BFE4FD 0%, #FFFFFF 100%);">
        <div class="blob-1 absolute top-0 left-1/2 w-[900px] h-[900px] bg-[#0072bc]/10 rounded-full blur-3xl opacity-60 -translate-x-1/2 will-change-transform"></div>
        <div class="blob-2 absolute top-24 -left-20 w-96 h-96 bg-[#0072bc]/15 rounded-full blur-3xl will-change-transform"></div>
        <div class="blob-3 absolute bottom-0 right-0 w-96 h-96 bg-[#BFE4FD]/50 rounded-full blur-3xl opacity-70 will-change-transform"></div>
    </div>

    <main class="w-full mx-auto max-w-[440px] px-6 py-12 flex-grow flex flex-col justify-center fade-in-up">

        <div class="flex flex-col items-center justify-center mb-8 text-center select-none">
            <span class="text-[#0072bc] w-16 h-16 flex items-center justify-center shrink-0">
                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.10008 21.221C6.71021 19.2375 5.89258 16.8243 5.89258 14.2187C5.89258 10.8443 8.6265 8.10938 11.9989 8.10938C15.3712 8.10938 18.1051 10.8443 18.1051 14.2187" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M18.4359 20.3118C18.3259 20.3179 18.218 20.3281 18.107 20.3281C14.7347 20.3281 12.0007 17.5931 12.0007 14.2188" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.2694 21.9999C10.675 20.382 8.94705 17.5024 8.94705 14.2187C8.94705 12.5315 10.3145 11.164 12.0007 11.164C13.6869 11.164 15.0543 12.5315 15.0543 14.2187C15.0543 15.9059 16.4218 17.2733 18.108 17.2733C19.7942 17.2733 21.1616 15.9059 21.1616 14.2187C21.1616 9.1571 17.0602 5.05469 12.0017 5.05469C6.94319 5.05469 2.8418 9.1571 2.8418 14.2187C2.8418 15.3469 2.96806 16.4455 3.20021 17.5045" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M20.5257 5.86313C18.4435 3.4978 15.399 2 12.0002 2C8.60136 2 5.55687 3.4978 3.47461 5.86313" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        </div>

        <div class="relative bg-white/80 backdrop-blur rounded-3xl border border-gray-200 p-7 sm:p-8 shadow-xl ring-1 ring-black/5 overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-[#0072bc] via-blue-400 to-[#0072bc]"></div>

            <div class="text-center mb-6">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#0072bc]/10 border border-[#0072bc]/20 text-[11px] font-bold text-[#0072bc] uppercase tracking-wide">
                    <i class="fa-solid fa-vial"></i> Test Environment
                </span>
                <h2 class="mt-3 text-[22px] font-black text-gray-900 tracking-tight leading-tight">Test Client Login</h2>
                <p class="mt-1.5 text-[13px] text-gray-500 font-medium leading-relaxed">Sign in with your email and password to explore the user panel.</p>
            </div>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="space-y-5">
                <div class="group">
                    <label for="login_email" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Email Address</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#0072bc] transition-colors duration-300 text-sm pointer-events-none"></i>
                        <input
                            type="email"
                            name="email"
                            id="login_email"
                            value="<?php echo htmlspecialchars($prefill_email); ?>"
                            placeholder="name@example.com"
                            autocomplete="email"
                            required
                            class="w-full bg-white border border-gray-200 rounded-xl pl-11 pr-4 py-3.5 text-sm text-gray-900 font-semibold outline-none focus:border-[#0072bc] focus:ring-4 focus:ring-blue-50 transition-all duration-300 shadow-sm placeholder-gray-400"
                        >
                    </div>
                </div>

                <div class="group">
                    <label for="login_password" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#0072bc] transition-colors duration-300 text-sm pointer-events-none"></i>
                        <input
                            type="password"
                            name="password"
                            id="login_password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                            class="w-full bg-white border border-gray-200 rounded-xl pl-11 pr-11 py-3.5 text-sm text-gray-900 font-semibold outline-none focus:border-[#0072bc] focus:ring-4 focus:ring-blue-50 transition-all duration-300 shadow-sm placeholder-gray-400"
                        >
                        <button type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()" class="absolute right-3 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-[#0072bc] rounded-lg transition cursor-pointer" title="Show password">
                            <i id="togglePasswordIcon" class="fa-solid fa-eye text-sm pointer-events-none"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="submitLoginBtn" class="relative w-full bg-gradient-to-r from-[#0072bc] to-blue-600 hover:from-blue-600 hover:to-blue-700 active:scale-[0.99] text-white py-4 rounded-xl text-base font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300 overflow-hidden cursor-pointer group mt-2">
                    <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                    <i id="btnIconNode" class="fa-solid fa-right-to-bracket text-sm shrink-0"></i>
                    <span id="btnTextNode">Sign In to User Panel</span>
                </button>
            </form>

            <p class="text-center text-[11px] font-semibold text-gray-400 flex items-center justify-center gap-1.5 mt-5">
                <i class="fa-solid fa-lock text-[#0072bc]"></i>
                Encrypted transmission. No spam, ever.
            </p>
        </div>
    </main>

    <?php if (file_exists('index_footer.php')) {
        include 'index_footer.php';
    } ?>

    <?php
    $alert_type    = !empty($login_error) ? 'error' : '';
    $alert_message = $login_error;
    include 'alert-modal.php';
    ?>

    <script>
        // NAVBAR SCROLL GLASS EFFECT
        document.addEventListener("DOMContentLoaded", () => {
            const navbar = document.getElementById("mainNavbar");
            if (!navbar) return;
            const handleScroll = () => {
                if (window.scrollY > 20) {
                    navbar.classList.add("navbar-scrolled");
                } else {
                    navbar.classList.remove("navbar-scrolled");
                }
            };
            window.addEventListener("scroll", handleScroll, { passive: true });
            handleScroll();
        });

        // PASSWORD VISIBILITY TOGGLE
        function togglePasswordVisibility() {
            const input = document.getElementById('login_password');
            const icon = document.getElementById('togglePasswordIcon');
            if (!input || !icon) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // SUBMIT INTERCEPTOR — disable double processing
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.querySelector('form[method="POST"]');
            form.addEventListener('submit', function () {
                const btn = document.getElementById('submitLoginBtn');
                const iconNode = document.getElementById('btnIconNode');
                const textNode = document.getElementById('btnTextNode');
                if (btn) btn.style.pointerEvents = 'none';
                if (iconNode) iconNode.className = "fa-solid fa-spinner animate-spin text-sm shrink-0";
                if (textNode) textNode.textContent = "Signing in...";
            });
        });
    </script>
</body>
</html>
