<?php
/**
 * Identity Search AI — Affiliate Layout Navigation Component
 * File: affiliate-navbar.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['affiliate_id']);

// Enforce strict name fallback metrics
$affiliateName = !empty($_SESSION['affiliate_name']) ? $_SESSION['affiliate_name'] : 'Partner';

// Capture the active filename context to compute precise active menu states
$active_script = pathinfo(basename($_SERVER['SCRIPT_FILENAME']), PATHINFO_FILENAME);

// Detect the affiliate portal landing page so the navbar renders transparent by default
// (matching the home page pattern) and only gains a tinted background on scroll.
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$is_portal_page = ($active_script === 'affiliate-portal') || (rtrim($request_path, '/') === '/affiliate-portal');

/**
 * Helper function to output the correct Tailwind classes for active tabs.
 * Synchronized with the deep navy tech theme palette.
 */
function getActiveNavClass($current_page, $target_pages) {
    if (in_array($current_page, (array)$target_pages)) {
        return 'bg-white/60 text-slate-900 border border-blue-200/60 rounded-xl font-bold';
    }
    return 'text-slate-800 hover:text-slate-900 hover:bg-white/40 rounded-xl font-semibold';
}
?>

<style>
    /* Submenu animation configurations */
    .dropdown-hover-zone:hover .dropdown-panel {
        display: block !important;
    }
</style>

<nav id="affiliateNavbar" class="transition-all duration-300 <?= $is_portal_page ? '' : 'bg-[#BFE4FD] border-b border-blue-200/50' ?>">
    <div class="max-w-[1650px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            <div class="flex-shrink-0">
                <a href="<?= $isLoggedIn ? 'affiliate-dashboard' : 'affiliate-portal' ?>" class="flex items-center">
                    <img src="public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
                </a>
            </div>

            <div class="hidden lg:flex items-center gap-2 text-[15px]">
                <?php if ($isLoggedIn): ?>
                    <a href="affiliate-dashboard" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, 'affiliate-dashboard') ?>">
                        Dashboard
                    </a>
                    <a href="affiliate-reports" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, 'affiliate-reports') ?>">
                        Reports
                    </a>
                    <a href="affiliate-clients" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, 'affiliate-clients') ?>">
                        Clients
                    </a>
                    <a href="affiliate-payout" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, 'affiliate-payout') ?>">
                        Withdraw
                    </a>
                    
                    <div class="relative dropdown-hover-zone py-2">
                        <button type="button" class="px-3.5 py-2 flex items-center gap-1.5 transition-all outline-none cursor-pointer <?= getActiveNavClass($active_script, ['affiliate-postback', 'test-postback', 'postback-log']) ?>">
                            <span>Postback</span>
                            <i class="fa-solid fa-chevron-down text-[11px] opacity-70"></i>
                        </button>
                        
                        <div class="hidden dropdown-panel absolute left-0 mt-1 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl py-2 z-50 text-left">
                            <a href="affiliate-postback" class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-white/50 font-semibold transition <?= $active_script === 'affiliate-postback' ? 'text-slate-900 font-bold bg-white/60' : '' ?>">
                                <i class="fa-solid fa-gear text-base text-slate-500"></i> Postback Setup
                            </a>
                            <a href="test-postback" class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-white/50 font-semibold transition <?= $active_script === 'test-postback' ? 'text-slate-900 font-bold bg-white/60' : '' ?>">
                                <i class="fa-solid fa-vial text-base text-slate-500"></i> Test Postback
                            </a>
                            <a href="postback-log" class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-slate-700 hover:bg-white/50 font-semibold transition <?= $active_script === 'postback-log' ? 'text-slate-900 font-bold bg-white/60' : '' ?>">
                                <i class="fa-solid fa-clock-rotate-left text-base text-slate-500"></i> Postback Logs
                            </a>
                        </div>
                    </div>

                    <a href="affiliate-am" class="px-3 py-2 rounded-xl flex items-center gap-1.5 transition-all text-[13px] font-bold <?= getActiveNavClass($active_script, 'affiliate-am') ?>" title="Account Manager">
                        <i class="fa-solid fa-user-tie text-sm"></i> AM
                    </a>

                    <a href="affiliate-logout" class="text-[13px] font-bold text-rose-600 hover:text-rose-700 transition bg-rose-50 hover:bg-rose-100/70 px-3.5 py-2 rounded-xl flex items-center gap-1.5 ml-1">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i> Logout
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!$isLoggedIn): ?>
            <div class="hidden lg:flex items-center gap-2 text-[15px] ml-auto">
                <a href="affiliate-login" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, ['affiliate-login', 'affiliate-forgot']) ?>">
                    Affiliate Login
                </a>
                <a href="affiliate-register" class="bg-gradient-to-r from-[#0072bc] to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold px-4.5 py-2 rounded-xl transition-all shadow-sm shadow-blue-200 text-center tracking-wide ml-1">
                    Affiliate Register
                </a>
                
                <!-- Desktop Separator & User Portal Link (Only for Logged Out users) -->
                <div class="w-px h-5 bg-blue-300 mx-2"></div>
                <a href="index" class="text-slate-800 hover:text-slate-900 px-3.5 py-2 font-semibold flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-house text-[11px] text-slate-500"></i> User Portal
                </a>
            </div>
            <?php endif; ?>

            <div class="flex items-center gap-4">
                <?php if ($isLoggedIn): ?>
                    <a href="affiliate-profile" class="hidden sm:flex items-center gap-2 hover:opacity-80 transition">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-white shadow-md">
                            <i class="fa-solid fa-user text-sm"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-slate-900"><?= htmlspecialchars($affiliateName) ?></div>
                            <div class="text-[10px] font-mono font-bold text-slate-700">Authorized Node</div>
                        </div>
                    </a>
                <?php endif; ?>
                
                <div class="relative lg:hidden">
                    <button type="button" onclick="toggleAffiliateMenu(event)" class="flex items-center justify-center p-2 rounded-xl border border-blue-200/60 hover:bg-white/40 text-slate-700 outline-none transition cursor-pointer">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    
                    <div id="mobileAffiliateMenu" class="hidden absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl py-2 z-50 text-left font-semibold text-[14px]">
                        <?php if ($isLoggedIn): ?>
                            <a href="affiliate-dashboard" class="flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-white/50 transition">
                                <i class="fa-solid fa-chart-pie text-base text-slate-500"></i> Dashboard
                            </a>
                            <a href="affiliate-reports" class="flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-white/50 transition">
                                <i class="fa-solid fa-chart-line text-base text-slate-500"></i> Reports
                            </a>
                            <a href="affiliate-clients" class="flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-white/50 transition">
                                <i class="fa-solid fa-users text-base text-slate-500"></i> Clients
                            </a>
                            <a href="affiliate-payout" class="flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-white/50 transition">
                                <i class="fa-solid fa-wallet text-base text-slate-500"></i> Withdraw
                            </a>
                            
                            <hr class="border-gray-100 my-1.5">
                            <span class="px-4 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider block">S2S Tools</span>
                            
                            <a href="affiliate-postback" class="flex items-center gap-2.5 pl-6 pr-4 py-2 text-slate-600 hover:bg-white/50 transition">
                                <i class="fa-solid fa-gear text-base text-slate-500"></i> Setup
                            </a>
                            <a href="test-postback" class="flex items-center gap-2.5 pl-6 pr-4 py-2 text-slate-600 hover:bg-white/50 transition">
                                <i class="fa-solid fa-vial text-base text-slate-500"></i> Test Tool
                            </a>
                            <a href="postback-log" class="flex items-center gap-2.5 pl-6 pr-4 py-2 text-slate-600 hover:bg-white/50 transition">
                                <i class="fa-solid fa-clock-rotate-left text-base text-slate-500"></i> Logs
                            </a>

                            <hr class="border-gray-100 my-1.5">
                            <a href="affiliate-logout" class="flex items-center gap-2.5 px-4 py-2.5 text-rose-600 hover:bg-rose-50 transition font-bold">
                                <i class="fa-solid fa-right-from-bracket text-base"></i> Logout
                            </a>
                        <?php else: ?>
                            <a href="affiliate-login" class="flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-white/50 transition">
                                <i class="fa-solid fa-right-to-bracket text-base text-slate-500"></i> Affiliate Login
                            </a>
                            <a href="affiliate-register" class="flex items-center gap-2.5 px-4 py-2.5 text-[#0072bc] hover:bg-blue-50 transition font-bold">
                                <i class="fa-solid fa-user-plus text-base text-[#0072bc]"></i> Affiliate Register
                            </a>
                            
                            <!-- Mobile Separator & User Portal (Only for Logged Out users) -->
                            <hr class="border-gray-100 my-1.5">
                            <a href="index" class="flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-house text-base text-gray-400"></i> User Portal
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</nav>

<script>
function toggleAffiliateMenu(event) {
    event.stopPropagation();
    const menu = document.getElementById('mobileAffiliateMenu');
    if (menu) menu.classList.toggle('hidden');
}

window.addEventListener('click', function() {
    const mobileMenu = document.getElementById('mobileAffiliateMenu');
    if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
    }
});

<?php if (!$is_portal_page): ?>
window.addEventListener('scroll', function() {
    const nav = document.getElementById('affiliateNavbar');
    if (window.scrollY > 20) {
        nav.classList.add('bg-[#BFE4FD]/90', 'backdrop-blur-md', 'shadow-[0_8px_32px_rgba(0,114,188,0.12)]');
        nav.classList.remove('bg-[#BFE4FD]', 'border-b-blue-200/50');
    } else {
        nav.classList.remove('bg-[#BFE4FD]/90', 'backdrop-blur-md', 'shadow-[0_8px_32px_rgba(0,114,188,0.12)]');
        nav.classList.add('bg-[#BFE4FD]', 'border-b-blue-200/50');
    }
});
<?php endif; ?>
</script>