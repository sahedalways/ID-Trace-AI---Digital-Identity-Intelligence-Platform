<?php
/**
 * Identity Search AI — User Promotional Ledger View
 * File: my-promo.php
 * Style Profile: Premium Whitebridge Multi-Sector Corporate Intel Layout
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enforce login access authorization locks
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "signin");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Fetch promotional codes allocated to this specific user row profile context
$stmt = $pdo->prepare("SELECT `promo_code`, `created_at` FROM `promo` WHERE `uid` = ? ORDER BY `id` DESC");
$stmt->execute([$user_id]);
$promos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function escapeHtml($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Promo Codes — Identity Search AI</title>
    <?php include 'head.php'; ?>
    <style>
        body { background-color: #f9fafb !important; color: #1e293b !important; }
        .copy-success-pop { transform: scale(1.05); border-color: #10b981 !important; background-color: #f0fdf4 !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#0072bc] selection:text-white bg-slate-50 relative">

    <!-- Sticky Glassmorphic Navbar Container -->
    <header id="mainNavbar" class="sticky top-0 z-50 bg-transparent transition-all duration-300">
        <?php include 'navbar.php'; ?>
    </header>

    <!-- Full-width Background Decorations -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden" style="height: 900px; background: linear-gradient(180deg, #BFE4FD 0%, #FFFFFF 100%);">
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

    <main class="max-w-xl w-full mx-auto px-4 py-12 flex-grow space-y-6">
        
        <div class="space-y-1 text-center sm:text-left">
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">My Promo Codes</h1>
            <p class="text-xs font-semibold text-gray-500">Access and deploy your generated reward subscription codes below.</p>
        </div>

        <!-- =========================================================================
             PROMO CODES LIST DISPLAY CONTAINER
             ========================================================================= -->
        <div class="space-y-3">
            <?php if (empty($promos)): ?>
                <div class="p-12 border border-dashed border-gray-200 rounded-2xl text-center space-y-3 bg-white">
                    <div class="w-12 h-12 bg-gray-50 border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 mx-auto text-lg">
                        <i class="fa-solid fa-ticket-simple"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-sm font-bold text-gray-900">No Codes Issued Yet</h3>
                        <p class="text-xs font-semibold text-gray-400 max-w-xs mx-auto">Purchase a credit subscription layer framework to automatically unlock promotional platform credentials.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($promos as $promo): 
                    $rawCode = $promo['promo_code'];
                    $formattedDate = date('M d, Y', strtotime($promo['created_at']));
                ?>
                    <div class="bg-white border border-gray-200 rounded-2xl p-4 flex items-center justify-between gap-4 shadow-2sm transition-all duration-200" id="promo-row-<?= escapeHtml($rawCode) ?>">
                        <div class="space-y-1 min-w-0">
                            <span class="block text-sm font-bold text-gray-900 font-mono tracking-wider truncate select-all select-none" id="code-text-<?= escapeHtml($rawCode) ?>"><?= escapeHtml($rawCode) ?></span>
                            <span class="block text-[11px] text-gray-400 font-semibold"><i class="fa-regular fa-calendar mr-1"></i>Generated: <?= $formattedDate ?></span>
                        </div>
                        <button onclick="copyPromoCodeToClipboard('<?= escapeHtml($rawCode) ?>')" class="w-9 h-9 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl flex items-center justify-center text-slate-700 transition cursor-pointer shrink-0 shadow-3sm" title="Copy code to clipboard" id="btn-<?= escapeHtml($rawCode) ?>">
                            <i class="fa-regular fa-copy text-sm"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- =========================================================================
             STEP-BY-STEP USER ONBOARDING HINTS
             ========================================================================= -->
        <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-5 space-y-4 shadow-3sm">
            <h3 class="text-xs font-bold uppercase tracking-widest text-blue-900 flex items-center gap-1.5"><i class="fa-solid fa-circle-info text-blue-700"></i> How to Activate Your Code</h3>
            
            <div class="space-y-3 text-xs font-semibold text-blue-950">
                <div class="flex items-start gap-3">
                    <span class="w-5 h-5 bg-blue-700/10 text-blue-800 rounded-md flex items-center justify-center font-bold text-[11px] shrink-0">1</span>
                    <p class="leading-relaxed pt-0.5">Go to <a href="https://chatzara.com" target="_blank" class="text-[#0072bc] hover:underline font-bold">https://chatzara.com</a> and sign into your account dashboard profile.</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-5 h-5 bg-blue-700/10 text-blue-800 rounded-md flex items-center justify-center font-bold text-[11px] shrink-0">2</span>
                    <p class="leading-relaxed pt-0.5">Navigate natively out to the <b class="text-slate-900">Profile > Upgrade Plan</b> configuration modules tab link.</p>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-5 h-5 bg-blue-700/10 text-blue-800 rounded-md flex items-center justify-center font-bold text-[11px] shrink-0">3</span>
                    <p class="leading-relaxed pt-0.5">Enter your copied unique promo token string sequence directly into the input container interface and click <b class="text-slate-900">Activate Premium Subscription</b>.</p>
                </div>
            </div>
        </div>

    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <!-- =========================================================================
         CLIPBOARD COPY ENGINE LOGIC SEQUENCE
         ========================================================================= -->
    <script>
    function copyPromoCodeToClipboard(code) {
        if (!code) return;
        
        navigator.clipboard.writeText(code).then(() => {
            const row = document.getElementById(`promo-row-${code}`);
            const btn = document.getElementById(`btn-${code}`);
            
            if (row && btn) {
                // Flash layout visual state attributes
                row.classList.add('copy-success-pop');
                btn.innerHTML = `<i class="fa-solid fa-check text-emerald-600"></i>`;
                btn.className = "w-9 h-9 bg-emerald-50 border border-emerald-300 rounded-xl flex items-center justify-center text-emerald-700 transition shrink-0";

                // Revert state transitions automatically
                setTimeout(() => {
                    row.classList.remove('copy-success-pop');
                    btn.innerHTML = `<i class="fa-regular fa-copy text-sm"></i>`;
                    btn.className = "w-9 h-9 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl flex items-center justify-center text-slate-700 transition cursor-pointer shrink-0 shadow-3sm";
                }, 1500);
            }
        }).catch(err => {
            console.error('Core clipboard write permissions initialization tracking failure: ', err);
        });
    }
    </script>
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