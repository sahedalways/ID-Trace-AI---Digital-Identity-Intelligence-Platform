<?php
/**
 * File: opt-out.php
 * User data opt-out / removal request page.
 */
require_once 'config.php';
require_once 'mailer.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$session_email = !empty($_SESSION['user_email']) ? trim($_SESSION['user_email']) : '';
$success_message = $_SESSION['optout_flash_success'] ?? '';
$error_message   = $_SESSION['optout_flash_error'] ?? '';
unset($_SESSION['optout_flash_success'], $_SESSION['optout_flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_name    = trim($_POST['optout_name'] ?? '');
    $input_email   = trim($_POST['optout_email'] ?? '');
    $input_detail  = trim($_POST['optout_detail'] ?? '');

    if (empty($input_name) || empty($input_email)) {
        $_SESSION['optout_flash_error'] = "Name and email are required.";
        header("Location: opt-out");
        exit;
    } elseif (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['optout_flash_error'] = "Invalid email address.";
        header("Location: opt-out");
        exit;
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO `opt_out_requests` (`name`, `email`, `detail`, `status`, `created_at`) VALUES (?, ?, ?, 'pending', NOW())");
            $stmt->execute([$input_name, $input_email, $input_detail ?: null]);

            $escapedName  = htmlspecialchars($input_name, ENT_QUOTES, 'UTF-8');
            $escapedEmail = htmlspecialchars($input_email, ENT_QUOTES, 'UTF-8');
            $escapedDetail = htmlspecialchars($input_detail ?: 'No additional details provided.', ENT_QUOTES, 'UTF-8');
            $htmlBody = "
                <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:30px;'>
                    <h2 style='color:#0072bc;'>Opt-Out Request Received</h2>
                    <p>We have received your data removal request. Our team will review and process it shortly.</p>
                    <div style='background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:20px;margin:20px 0;'>
                        <p><strong>Name:</strong> {$escapedName}</p>
                        <p><strong>Email:</strong> {$escapedEmail}</p>
                        <p><strong>Details:</strong> {$escapedDetail}</p>
                    </div>
                    <p style='color:#64748b;font-size:13px;'>If you have questions, contact us at support@identitysearch.ai</p>
                </div>";
            @sendTransactionalMail($input_email, "Opt-Out Request Confirmation", $htmlBody);

            $_SESSION['optout_flash_success'] = "Your opt-out request has been submitted successfully. We will process it within 30 days.";
            header("Location: opt-out");
            exit;
        } catch (Exception $e) {
            $_SESSION['optout_flash_error'] = "Something went wrong. Please try again later.";
            header("Location: opt-out");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Opt-Out — Identity Search AI</title>
    <?php include 'head.php'; ?>
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

    <style>
        @keyframes blobMove1 { 0%, 100% { transform: translateX(-50%) translateY(0); } 25% { transform: translateX(-40%) translateY(-20px); } 50% { transform: translateX(-50%) translateY(-10px); } 75% { transform: translateX(-60%) translateY(10px); } }
        @keyframes blobMove2 { 0%, 100% { transform: translate(0, 0); } 33% { transform: translate(30px, -15px); } 66% { transform: translate(-20px, 20px); } }
        @keyframes blobMove3 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(-40px, -20px); } }
        .blob-1 { animation: blobMove1 18s ease-in-out infinite; }
        .blob-2 { animation: blobMove2 14s ease-in-out infinite; }
        .blob-3 { animation: blobMove3 16s ease-in-out infinite; }
        .navbar-scrolled { background: linear-gradient(to right, #eef5ff 20%, #0072bc 100%); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); }
        #mainNavbar #siteNavbar { background: transparent !important; border-bottom: none !important; box-shadow: none !important; }
        #siteNavbar a, #siteNavbar button, #siteNavbar .text-black, #siteNavbar .text-slate-400, #siteNavbar .text-\[\#0072bc\] { transition: color 0.35s ease, background-color 0.35s ease, border-color 0.35s ease; }
        #mainNavbar.navbar-scrolled #siteNavbar a:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:not(#userDropdownMenu button, #mobileDrawer button),
        #mainNavbar.navbar-scrolled #siteNavbar .text-black:not(#userDropdownMenu, #mobileDrawer),
        #mainNavbar.navbar-scrolled #siteNavbar .text-slate-400:not(#userDropdownMenu .text-slate-400, #mobileDrawer .text-slate-400),
        #mainNavbar.navbar-scrolled #siteNavbar .text-\[\#0072bc\]:not(#userDropdownMenu .text-\[\#0072bc\], #mobileDrawer .text-\[\#0072bc\]) { color: #ffffff !important; }
        #mainNavbar.navbar-scrolled #siteNavbar a:hover:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:hover:not(#userDropdownMenu button, #mobileDrawer button, #mobileMenuButton) { color: #ffffff !important; }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton { color: #ffffff !important; border-color: rgba(255, 255, 255, 0.25); }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton:hover,
        #mainNavbar.navbar-scrolled #siteNavbar #userMenuButton:hover { background-color: rgba(255, 255, 255, 0.15); color: #ffffff !important; }
    </style>

    <main class="flex-grow max-w-xl w-full mx-auto px-4 sm:px-6 pt-12 pb-16">
        <div class="space-y-6">

            <div class="text-center space-y-2">
                <h1 class="text-3xl font-black tracking-tight text-gray-900">Opt-Out Request</h1>
                <p class="text-base text-black font-semibold max-w-sm mx-auto leading-relaxed">
                    Want your data removed from our platform? Submit a request below and we will process it within 30 days.
                </p>
            </div>

            <?php
            $alert_type = !empty($success_message) ? 'success' : (!empty($error_message) ? 'error' : '');
            $alert_message = $success_message ?: $error_message;
            ?>
            <?php include 'alert-modal.php'; ?>

            <form action="opt-out" method="POST" class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-xl space-y-5 text-left">

                <div class="space-y-1.5">
                    <label for="optout_name" class="text-xs font-black uppercase text-gray-400 tracking-wider">Full Name</label>
                    <input type="text" name="optout_name" id="optout_name" placeholder="Enter your full name"
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#0072bc] focus:bg-white transition" required>
                </div>

                <div class="space-y-1.5">
                    <label for="optout_email" class="text-xs font-black uppercase text-gray-400 tracking-wider">Email Address</label>
                    <input type="email" name="optout_email" id="optout_email" value="<?= htmlspecialchars($session_email) ?>" placeholder="name@example.com"
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#0072bc] focus:bg-white transition" required>
                </div>

                <div class="space-y-1.5">
                    <label for="optout_detail" class="text-xs font-black uppercase text-gray-400 tracking-wider">Additional Details (Optional)</label>
                    <textarea name="optout_detail" id="optout_detail" rows="4" placeholder="Any specific information you'd like us to know..."
                        class="w-full bg-slate-50 border border-gray-200 rounded-xl px-4 py-3 text-base text-black font-semibold outline-none focus:border-[#0072bc] focus:bg-white transition resize-none"></textarea>
                </div>

                <button type="submit" class="w-full bg-[#0072bc] hover:bg-[#005ea3] active:scale-[0.99] text-white py-4 rounded-xl text-base font-bold transition-all flex items-center justify-center gap-2 shadow-md shadow-emerald-100 cursor-pointer mt-2">
                    <i class="fa-solid fa-user-slash text-sm shrink-0"></i>
                    Submit Opt-Out Request
                </button>

            </form>

            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-3">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-[#0072bc]"></i> What happens next?
                </h3>
                <ul class="space-y-2 text-sm text-gray-600 font-medium">
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-[#0072bc] mt-1 text-xs"></i> We will review your request within 5 business days.</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-[#0072bc] mt-1 text-xs"></i> Your data will be removed within 30 days of verification.</li>
                    <li class="flex items-start gap-2"><i class="fa-solid fa-check text-[#0072bc] mt-1 text-xs"></i> You will receive an email confirmation once the process is complete.</li>
                </ul>
            </div>

        </div>
    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

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
    </script></body>
</html>
