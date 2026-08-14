<?php
/**
 * Identity Search AI — Secure Digital Communications Core Gateway
 * File: contact.php
 */
require_once 'config.php';
require_once 'mailer.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Session check to safely pre-fill data handles
$session_uid   = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$preload_name  = !empty($_SESSION['user_name']) ? trim($_SESSION['user_name']) : '';
$preload_email = !empty($_SESSION['user_email']) ? trim($_SESSION['user_email']) : '';

// 2. Fetch flashed PRG status messages from session storage frames
$success_message = $_SESSION['contact_flash_success'] ?? '';
$error_message   = $_SESSION['contact_flash_error'] ?? '';

// Unset immediately so they only display for a single execution pass
unset($_SESSION['contact_flash_success'], $_SESSION['contact_flash_error']);

// 3. Handle outbound message dispatch form metrics securely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_name    = trim($_POST['contact_name'] ?? '');
    $input_email   = trim($_POST['contact_email'] ?? '');
    $input_subject = trim($_POST['contact_subject'] ?? '');
    $input_body    = trim($_POST['contact_body'] ?? '');

    if (empty($input_name) || empty($input_email) || empty($input_subject) || empty($input_body)) {
        $_SESSION['contact_flash_error'] = "All form matrix entries are required to route your communication.";
        header("Location: " . BASE_URL . "contact");
        exit;
    } elseif (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact_flash_error'] = "The provided electronic communication address layout remains invalid.";
        header("Location: " . BASE_URL . "contact");
        exit;
    } else {
        try {
            // Commit tracking entry payload dynamically to data layer table
            $stmt = $pdo->prepare("
                INSERT INTO `contact` (`uid`, `name`, `email`, `subject`, `body`, `created_at`)
                VALUES (:uid, :name, :email, :subject, :body, NOW())
            ");
            $stmt->execute([
                ':uid'     => $session_uid,
                ':name'    => $input_name,
                ':email'   => $input_email,
                ':subject' => $input_subject,
                ':body'    => $input_body
            ]);

            // Construct Transactional Email Confirmation Layout with clean thinned font metrics
            $escapedName    = htmlspecialchars($input_name, ENT_QUOTES, 'UTF-8');
            $escapedEmail   = htmlspecialchars($input_email, ENT_QUOTES, 'UTF-8');
            $escapedSubject = htmlspecialchars($input_subject, ENT_QUOTES, 'UTF-8');
            $escapedBody    = nl2br(htmlspecialchars($input_body, ENT_QUOTES, 'UTF-8'));

            $htmlBody = "
                <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
                    <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>

                        <!-- HEADER LOGO STACK -->
                        <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                            <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                                <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='Identity Search AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                                <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; display: inline-block; vertical-align: middle;'>Identity Search <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                            </div>
                        </div>

                        <!-- BODY MAIN SECTOR WITH SMALL TEXT LAYOUT -->
                        <div style='padding: 24px 20px; text-align: left;'>
                            <h2 style='font-size: 16px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 10px;'>We received your support request</h2>
                            <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 18px;'>Thank you for reaching out to Identity Search AI. We have successfully logged your technical data request, and our engineering team will review your inquiry and respond shortly.</p>

                            <div style='background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px; margin-bottom: 18px;'>
                                <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Name:</b> {$escapedName}</div>
                                <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Email:</b> {$escapedEmail}</div>
                                <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Subject:</b> {$escapedSubject}</div>
                                <div style='font-size: 11px; color: #4B5563; line-height: 1.4;'><b style='color: #111827; display: block; margin-bottom: 4px;'>Details:</b><div style='color: #6B7280;'>{$escapedBody}</div></div>
                            </div>
                        </div>

                        <!-- FOOTER BRAND SECTOR -->
                        <div style='padding: 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: center;'>
                            <div style='display: block; margin-bottom: 8px;'>
                                <span style='font-size: 16px; display: inline-block; vertical-align: middle;'>🕵️‍♂️</span>
                            </div>
                            <p style='font-size: 9px; color: #4B5563; font-weight: 500; margin: 0 0 4px 0;'>&copy; 2026 - Identity Search AI</p>
                            <p style='font-size: 9px; color: #4B5563; font-weight: 400; margin: 0;'>
                                <a href='mailto:support@identitysearch.ai' style='color: #0072bc; text-decoration: none;'>support@identitysearch.ai</a>
                            </p>
                        </div>

                    </div>
                </div>
            ";

            $emailSubject = "We received your support request";
            sendTransactionalMail($input_email, $emailSubject, $htmlBody);

            $_SESSION['contact_flash_success'] = "Message dispatched successfully. A confirmation receipt copy has been sent to your inbox.";

            // Post/Redirect/Get Execution Termination Loop
            header("Location: " . BASE_URL . "contact");
            exit;
        } catch (Exception $dbEx) {
            $_SESSION['contact_flash_error'] = "Operational tracking error: " . $dbEx->getMessage();
            header("Location: " . BASE_URL . "contact");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Support Center — Identity Search AI</title>
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

    <main class="relative flex-grow w-full mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20" style="max-width: 1600px;">
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
            .blob-1 { animation: blobMove1 18s ease-in-out infinite; }
            .blob-2 { animation: blobMove2 14s ease-in-out infinite; }
            .blob-3 { animation: blobMove3 16s ease-in-out infinite; }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .onload-anim { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; }
            .onload-delay-100 { animation-delay: 100ms; }
            .onload-delay-200 { animation-delay: 200ms; }
            .onload-delay-300 { animation-delay: 300ms; }

            .animate-on-scroll {
                opacity: 0;
                transform: translateY(30px);
                transition: opacity 0.8s ease-out, transform 0.8s ease-out;
                will-change: opacity, transform;
            }
            .animate-on-scroll.is-visible { opacity: 1; transform: translateY(0); }

            @keyframes spark-sweep {
                0% { background-position: 0% 50%; }
                100% { background-position: 200% 50%; }
            }
            .spark-text {
                background: linear-gradient(
                    135deg,
                    #020617 35%,
                    #2563eb 50%,
                    #020617 65%
                );
                background-size: 200% auto;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                color: transparent;
                animation: spark-sweep 5s linear infinite;
            }

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

        <!-- HERO -->
        <section class="max-w-3xl mx-auto text-center pt-6 sm:pt-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#0072bc]/10 backdrop-blur-md border border-[#0072bc]/20 text-xs font-semibold text-[#0072bc] tracking-wide shadow-lg shadow-[#0072bc]/10 hover:-translate-y-0.5 hover:shadow-[#0072bc]/25 transition-all duration-300 onload-anim">
                <span class="w-2 h-2 rounded-full bg-[#0072bc] shadow-[0_0_8px_rgba(0,114,188,0.9)] animate-pulse"></span>
                Support Center
            </div>

            <h1 class="mt-7 text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-[1.08] onload-anim onload-delay-100">
                <span class="spark-text">How can we help?</span>
            </h1>

            <p class="mt-6 text-sm sm:text-base lg:text-lg text-black font-semibold max-w-2xl leading-relaxed mx-auto onload-anim onload-delay-200">
                Have questions or need technical support? Drop us a message below and we will respond shortly.
            </p>
        </section>

        <!-- BILLING INQUIRIES + CONTACT GRID -->
        <section class="max-w-6xl mx-auto mt-12 mb-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

                <!-- LEFT: Billing inquiries (sticky on scroll) -->
                <div class="space-y-6 lg:sticky lg:top-20 lg:self-start onload-anim onload-delay-300">

                    <!-- Billing header -->
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-50 border border-indigo-100 text-xs font-semibold text-indigo-700 tracking-wide shadow-sm">
                            <i class="fa-solid fa-credit-card"></i>
                            Billing Inquiries
                        </div>
                        <h2 class="mt-4 text-2xl sm:text-3xl font-black tracking-tight text-black">Questions about your membership plan?</h2>
                        <p class="mt-3 text-sm sm:text-base text-gray-600 font-semibold leading-relaxed">
                            Contact a member services representative who can explain your membership plan and assist you with any billing questions.
                        </p>
                    </div>

                    <!-- Charge descriptor card -->
                    <div class="relative bg-white/80 backdrop-blur rounded-3xl border border-gray-200 p-6 sm:p-7 shadow-xl ring-1 ring-black/5 overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-[#0072bc] via-blue-400 to-[#0072bc]"></div>
                        <div class="flex items-start sm:items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-black tracking-tight text-black">Charges on your bank card statement</h3>
                                <p class="text-xs font-semibold text-gray-500 mt-1">Charges may appear as any of the following:</p>
                            </div>
                        </div>
                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="flex items-center gap-3 rounded-2xl border border-gray-100 bg-slate-50 px-5 py-4">
                                <div class="w-9 h-9 rounded-xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center text-sm shrink-0">
                                    <i class="fa-solid fa-building-columns"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Descriptor 1</p>
                                    <p class="text-sm font-extrabold text-black mt-0.5 tracking-wide">IDENTITY SEARCH AI</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 rounded-2xl border border-gray-100 bg-slate-50 px-5 py-4">
                                <div class="w-9 h-9 rounded-xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center text-sm shrink-0">
                                    <i class="fa-solid fa-globe"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Descriptor 2</p>
                                    <p class="text-sm font-extrabold text-black mt-0.5 tracking-wide">IDENTITYSEARCH.AI</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mailing address -->
                    <div class="relative bg-white/80 backdrop-blur rounded-3xl border border-gray-200 p-6 sm:p-7 shadow-xl ring-1 ring-black/5 overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#0072bc] via-blue-400 to-[#0072bc]"></div>
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <h3 class="text-base font-black tracking-tight text-black">Mailing Address</h3>
                        </div>
                        <div class="mt-5 rounded-2xl bg-slate-50 border border-gray-100 p-5">
                            <p class="text-sm font-extrabold text-black">Cpabossaffiliate LLC</p>
                            <p class="text-sm font-semibold text-gray-600 mt-1">30 N Gould St Ste R</p>
                            <p class="text-sm font-semibold text-gray-600">Sheridan, WY 82801</p>
                        </div>
                        <p class="mt-4 text-xs font-semibold text-amber-600 flex items-start gap-2 leading-relaxed">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            Please note: we do not accept opt-out requests via mail or email.
                        </p>
                    </div>

                    <!-- Customer care -->
                    <div class="relative bg-white/80 backdrop-blur rounded-3xl border border-gray-200 p-6 sm:p-7 shadow-xl ring-1 ring-black/5 overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#0072bc] via-blue-400 to-[#0072bc]"></div>
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center text-lg shrink-0">
                                <i class="fa-solid fa-headset"></i>
                            </div>
                            <h3 class="text-base font-black tracking-tight text-black">Customer Care</h3>
                        </div>
                        <div class="mt-5 space-y-3">
                            <a href="tel:+13074001496" class="flex items-center gap-3 rounded-2xl border border-gray-100 bg-slate-50 px-5 py-4 hover:border-[#0072bc]/40 hover:bg-white hover:shadow-lg transition-all duration-300 group">
                                <div class="w-9 h-9 rounded-xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center text-sm shrink-0 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Contract No.</p>
                                    <p class="text-sm font-extrabold text-black mt-0.5">(307) 400-1496</p>
                                </div>
                            </a>
                            <a href="mailto:support@identitysearch.ai" class="flex items-center gap-3 rounded-2xl border border-gray-100 bg-slate-50 px-5 py-4 hover:border-[#0072bc]/40 hover:bg-white hover:shadow-lg transition-all duration-300 group">
                                <div class="w-9 h-9 rounded-xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center text-sm shrink-0 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Email</p>
                                    <p class="text-sm font-extrabold text-[#0072bc] mt-0.5 break-all">support@identitysearch.ai</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Business hours -->
                    <div class="relative bg-white/80 backdrop-blur rounded-3xl border border-gray-200 p-6 sm:p-7 shadow-xl ring-1 ring-black/5 overflow-hidden">
                        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#0072bc] via-blue-400 to-[#0072bc]"></div>
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center text-lg shrink-0">
                                <i class="fa-regular fa-clock"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-black tracking-tight text-black">Business Hours</h3>
                                <p class="text-xs font-semibold text-gray-500 mt-0.5">All times shown in Pacific Standard Time (PST)</p>
                            </div>
                        </div>
                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-gray-100 bg-slate-50 px-5 py-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Mon – Fri</p>
                                <p class="text-sm font-extrabold text-black mt-0.5">6:00am – 6:00pm PST</p>
                            </div>
                            <div class="rounded-2xl border border-gray-100 bg-slate-50 px-5 py-4">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wide">Sat – Sun</p>
                                <p class="text-sm font-extrabold text-black mt-0.5">7:00am – 3:30pm PST</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Contact submission -->
                <div class="space-y-6 onload-anim onload-delay-300">

                <!-- STATUS RESPONSE INTERFACES -->
                        <?php if (!empty($success_message)): ?>
                            <div class="p-4 bg-emerald-50 border border-emerald-200 border-l-4 border-l-emerald-500 rounded-2xl flex items-start gap-3 text-left">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-lg mt-0.5"></i>
                                <p class="text-sm text-emerald-900 font-bold leading-normal"><?php echo htmlspecialchars($success_message); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_message)): ?>
                            <div class="p-4 bg-red-50 border border-red-200 border-l-4 border-l-red-500 rounded-2xl flex items-start gap-3 text-left">
                                <i class="fa-solid fa-circle-exclamation text-red-600 text-lg mt-0.5"></i>
                                <p class="text-sm text-red-900 font-bold leading-normal"><?php echo htmlspecialchars($error_message); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- CONTACT SUBMISSION GATEWAY -->
                        <form id="contactSubmissionForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="relative bg-white/80 backdrop-blur rounded-3xl border border-gray-200 p-7 sm:p-9 shadow-xl ring-1 ring-black/5 space-y-5 text-left onload-anim onload-delay-300 overflow-hidden">

                            <!-- Top gradient accent strip -->
                            <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-[#0072bc] via-blue-400 to-[#0072bc]"></div>

                            <!-- Card header -->
                            <div class="flex items-center gap-4 pb-5 border-b border-gray-100">
                                <div class="w-11 h-11 rounded-2xl bg-[#0072bc]/10 text-[#0072bc] flex items-center justify-center text-lg shrink-0">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-black tracking-tight text-black leading-tight">Send us a message</h3>
                                    <p class="text-xs font-semibold text-gray-800 mt-0.5">Fill in the fields below — we typically respond within 24 hours.</p>
                                </div>
                            </div>

                            <div class="group">
                                <label for="contact_name" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Your Full Name</label>
                                <div class="relative">
                                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#0072bc] transition-colors duration-300 text-sm pointer-events-none"></i>
                                    <input
                                        type="text"
                                        name="contact_name"
                                        id="contact_name"
                                        value="<?php echo htmlspecialchars($preload_name); ?>"
                                        placeholder="Enter your name"
                                        class="w-full bg-slate-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3.5 text-sm text-black font-semibold outline-none focus:bg-white focus:border-[#0072bc] focus:ring-4 focus:ring-[#0072bc]/10 transition-all duration-300"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="group">
                                <label for="contact_email" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Your Email Address</label>
                                <div class="relative">
                                    <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#0072bc] transition-colors duration-300 text-sm pointer-events-none"></i>
                                    <input
                                        type="email"
                                        name="contact_email"
                                        id="contact_email"
                                        value="<?php echo htmlspecialchars($preload_email); ?>"
                                        placeholder="name@example.com"
                                        class="w-full bg-slate-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3.5 text-sm text-black font-semibold outline-none focus:bg-white focus:border-[#0072bc] focus:ring-4 focus:ring-[#0072bc]/10 transition-all duration-300"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="group">
                                <label for="contact_subject" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Inquiry Subject</label>
                                <div class="relative">
                                    <i class="fa-solid fa-tag absolute left-4 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-[#0072bc] transition-colors duration-300 text-sm pointer-events-none"></i>
                                    <input
                                        type="text"
                                        name="contact_subject"
                                        id="contact_subject"
                                        placeholder="What are you reaching out about?"
                                        class="w-full bg-slate-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3.5 text-sm text-black font-semibold outline-none focus:bg-white focus:border-[#0072bc] focus:ring-4 focus:ring-[#0072bc]/10 transition-all duration-300"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="group">
                                <label for="contact_body" class="block text-[11px] font-bold text-gray-400 uppercase tracking-wide mb-2">Message Details</label>
                                <div class="relative">
                                    <i class="fa-solid fa-comment-dots absolute left-4 top-4 text-gray-300 group-focus-within:text-[#0072bc] transition-colors duration-300 text-sm pointer-events-none"></i>
                                    <textarea
                                        name="contact_body"
                                        id="contact_body"
                                        rows="5"
                                        placeholder="Type the full details of your message here..."
                                        class="w-full bg-slate-50 border border-gray-200 rounded-xl pl-11 pr-4 py-3.5 text-sm text-black font-semibold outline-none focus:bg-white focus:border-[#0072bc] focus:ring-4 focus:ring-[#0072bc]/10 transition-all duration-300 resize-none"
                                        required
                                    ></textarea>
                                </div>
                            </div>

                            <button type="submit" id="submitContactBtn" class="relative w-full bg-gradient-to-r from-[#0072bc] to-blue-600 hover:from-blue-600 hover:to-blue-700 active:scale-[0.99] text-white py-4 rounded-xl text-base font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300 overflow-hidden cursor-pointer mt-2 group">
                                <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700"></span>
                                <i id="btnIconNode" class="fa-solid fa-paper-plane text-sm shrink-0"></i>
                                <span id="btnTextNode">Send Message</span>
                            </button>

                            <p class="text-center text-[11px] font-semibold text-gray-400 flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-lock text-[#0072bc]"></i>
                                Encrypted transmission. No spam, ever.
                            </p>
                        </form>
                    </div>
                </div>

            <!-- Opt-out banner -->
            <div class="mt-8 flex flex-col sm:flex-row items-start sm:items-center gap-5 justify-between rounded-3xl bg-gradient-to-r from-[#0072bc] to-blue-600 text-white p-6 sm:p-7 shadow-xl shadow-blue-200">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-white/15 text-white flex items-center justify-center text-lg shrink-0">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-black tracking-tight text-white">Manage or remove your public records</h3>
                        <p class="text-xs font-medium text-white/80 mt-1">Opt-out requests are processed securely through our dedicated portal.</p>
                    </div>
                </div>
                <a href="https://identitysearch.ai/opt-out" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-xl bg-white text-[#0072bc] px-6 py-3 text-sm font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 shrink-0">
                    Go to Opt-Out Portal
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>
        </section>
    </main>

    <?php if (file_exists('index_footer.php')) {
        include 'index_footer.php';
    } ?>

    <!-- ANIMATED LOCKOUT FORM BLOCK INTERCEPTOR SCRIPT -->
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

        document.getElementById('contactSubmissionForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitContactBtn');
            const iconNode = document.getElementById('btnIconNode');
            const textNode = document.getElementById('btnTextNode');

            if (btn && iconNode && textNode) {
                // Terminate double processing flows instantly
                btn.style.pointerEvents = 'none';
                btn.classList.add('opacity-80');

                // Trigger animation processing layouts
                iconNode.className = "fa-solid fa-spinner animate-spin text-sm shrink-0";
                textNode.textContent = "Processing...";
            }
        });
    </script>
</body>
</html>
