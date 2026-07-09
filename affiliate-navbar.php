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
$active_script = basename($_SERVER['SCRIPT_FILENAME']);

/**
 * Helper function to output the correct Tailwind classes for active tabs.
 * Synchronized with the global theme palette styles natively.
 */
function getActiveNavClass($current_page, $target_pages) {
    if (in_array($current_page, (array)$target_pages)) {
        return 'bg-emerald-50 text-[#128c7e] border border-emerald-100/60 rounded-xl font-bold';
    }
    return 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/50 rounded-xl font-semibold';
}
?>

<style>
    /* Submenu animation configurations */
    .dropdown-hover-zone:hover .dropdown-panel {
        display: block !important;
    }
</style>

<nav id="affiliateNavbar" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm transition-all duration-300">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            
            <div class="flex-shrink-0">
                <a href="<?= $isLoggedIn ? 'affiliate-dashboard.php' : 'affiliate-portal.php' ?>" class="text-lg sm:text-xl font-bold tracking-tight text-gray-900 flex items-center gap-2">
                    <span class="text-gray-900 flex items-center shrink-0 w-7 h-7 sm:w-8 sm:h-8">
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
                    <span class="bg-gradient-to-r from-gray-900 to-gray-800 bg-clip-text text-transparent font-semibold">
                        Affiliate<span class="text-gray-900 font-semibold ml-1">Portal</span><span class="text-[10px] font-mono font-bold bg-[#128c7e] text-white px-1.5 py-0.5 rounded ml-1.5 align-middle tracking-wider">v1</span>
                    </span>
                </a>
            </div>

            <div class="hidden lg:flex items-center gap-2 text-[15px]">
                <?php if ($isLoggedIn): ?>
                    <a href="affiliate-dashboard.php" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, 'affiliate-dashboard.php') ?>">
                        Dashboard
                    </a>
                    <a href="affiliate-reports.php" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, 'affiliate-reports.php') ?>">
                        Reports
                    </a>
                    <a href="affiliate-clients.php" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, 'affiliate-clients.php') ?>">
                        Clients
                    </a>
                    <a href="affiliate-payout.php" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, 'affiliate-payout.php') ?>">
                        Withdraw
                    </a>
                    
                    <div class="relative dropdown-hover-zone py-2">
                        <button type="button" class="px-3.5 py-2 flex items-center gap-1.5 transition-all outline-none cursor-pointer <?= getActiveNavClass($active_script, ['affiliate-postback.php', 'test-postback.php', 'postback-log.php']) ?>">
                            <span>Postback</span>
                            <i class="fa-solid fa-chevron-down text-[11px] opacity-70"></i>
                        </button>
                        
                        <div class="hidden dropdown-panel absolute left-0 mt-1 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl py-2 z-50 text-left">
                            <a href="affiliate-postback.php" class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-700 hover:bg-slate-50 font-semibold transition <?= $active_script === 'affiliate-postback.php' ? 'text-[#128c7e] font-bold bg-emerald-50/40' : '' ?>">
                                <i class="fa-solid fa-gear text-base text-gray-400"></i> Postback Setup
                            </a>
                            <a href="test-postback.php" class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-700 hover:bg-slate-50 font-semibold transition <?= $active_script === 'test-postback.php' ? 'text-[#128c7e] font-bold bg-emerald-50/40' : '' ?>">
                                <i class="fa-solid fa-vial text-base text-gray-400"></i> Test Postback
                            </a>
                            <a href="postback-log.php" class="flex items-center gap-2.5 px-4 py-2.5 text-[13px] text-gray-700 hover:bg-slate-50 font-semibold transition <?= $active_script === 'postback-log.php' ? 'text-[#128c7e] font-bold bg-emerald-50/40' : '' ?>">
                                <i class="fa-solid fa-clock-rotate-left text-base text-gray-400"></i> Postback Logs
                            </a>
                        </div>
                    </div>

                    <a href="affiliate-logout.php" class="text-[13px] font-bold text-rose-600 hover:text-rose-700 transition bg-rose-50 hover:bg-rose-100/70 px-3.5 py-2 rounded-xl flex items-center gap-1.5 ml-2">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i> Logout
                    </a>
                <?php endif; ?>
            </div>

            <?php if (!$isLoggedIn): ?>
            <div class="hidden lg:flex items-center gap-2 text-[15px] ml-auto">
                <a href="affiliate-login.php" class="px-3.5 py-2 transition-all <?= getActiveNavClass($active_script, ['affiliate-login.php', 'affiliate-forgot.php']) ?>">
                    Affiliate Login
                </a>
                <a href="affiliate-register.php" class="bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold px-4.5 py-2 rounded-xl transition-all shadow-xs text-center tracking-wide ml-1">
                    Affiliate Register
                </a>
                
                <!-- Desktop Separator & User Portal Link (Only for Logged Out users) -->
                <div class="w-px h-5 bg-gray-200 mx-2"></div>
                <a href="index.php" class="text-slate-600 hover:text-slate-900 px-3.5 py-2 font-semibold flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-house text-[11px] text-gray-400"></i> User Portal
                </a>
            </div>
            <?php endif; ?>

            <div class="flex items-center gap-4">
                <?php if ($isLoggedIn): ?>
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-gray-900"><?= htmlspecialchars($affiliateName) ?></div>
                        <div class="text-[10px] font-mono font-bold text-[#128c7e]">Authorized Node</div>
                    </div>
                <?php endif; ?>
                
                <div class="relative lg:hidden">
                    <button type="button" onclick="toggleAffiliateMenu(event)" class="flex items-center justify-center p-2 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-700 outline-none transition cursor-pointer">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    
                    <div id="mobileAffiliateMenu" class="hidden absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl py-2 z-50 text-left font-semibold text-[14px]">
                        <?php if ($isLoggedIn): ?>
                            <a href="affiliate-dashboard.php" class="flex items-center gap-2.5 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-chart-pie text-base text-gray-400"></i> Dashboard
                            </a>
                            <a href="affiliate-reports.php" class="flex items-center gap-2.5 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-chart-line text-base text-gray-400"></i> Reports
                            </a>
                            <a href="affiliate-clients.php" class="flex items-center gap-2.5 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-users text-base text-gray-400"></i> Clients
                            </a>
                            <a href="affiliate-payout.php" class="flex items-center gap-2.5 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-wallet text-base text-gray-400"></i> Withdraw
                            </a>
                            
                            <hr class="border-gray-100 my-1.5">
                            <span class="px-4 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider block">S2S Tools</span>
                            
                            <a href="affiliate-postback.php" class="flex items-center gap-2.5 pl-6 pr-4 py-2 text-gray-600 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-gear text-base text-gray-400"></i> Setup
                            </a>
                            <a href="test-postback.php" class="flex items-center gap-2.5 pl-6 pr-4 py-2 text-gray-600 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-vial text-base text-gray-400"></i> Test Tool
                            </a>
                            <a href="postback-log.php" class="flex items-center gap-2.5 pl-6 pr-4 py-2 text-gray-600 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-clock-rotate-left text-base text-gray-400"></i> Logs
                            </a>

                            <hr class="border-gray-100 my-1.5">
                            <a href="affiliate-logout.php" class="flex items-center gap-2.5 px-4 py-2.5 text-rose-600 hover:bg-rose-50 transition font-bold">
                                <i class="fa-solid fa-right-from-bracket text-base"></i> Logout
                            </a>
                        <?php else: ?>
                            <a href="affiliate-login.php" class="flex items-center gap-2.5 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-right-to-bracket text-base text-gray-400"></i> Affiliate Login
                            </a>
                            <a href="affiliate-register.php" class="flex items-center gap-2.5 px-4 py-2.5 text-[#128c7e] hover:bg-emerald-50 transition font-bold">
                                <i class="fa-solid fa-user-plus text-base text-emerald-600"></i> Affiliate Register
                            </a>
                            
                            <!-- Mobile Separator & User Portal (Only for Logged Out users) -->
                            <hr class="border-gray-100 my-1.5">
                            <a href="index.php" class="flex items-center gap-2.5 px-4 py-2.5 text-slate-700 hover:bg-gray-50 transition">
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

window.addEventListener('scroll', function() {
    const nav = document.getElementById('affiliateNavbar');
    if (window.scrollY > 20) {
        nav.classList.add('bg-emerald-50/60', 'backdrop-blur-3xl', 'shadow-[0_8px_32px_rgba(18,140,126,0.12)]', 'border-b-emerald-200/50');
        nav.classList.remove('bg-white', 'shadow-sm', 'border-b-gray-200');
    } else {
        nav.classList.remove('bg-emerald-50/60', 'backdrop-blur-3xl', 'shadow-[0_8px_32px_rgba(18,140,126,0.12)]', 'border-b-emerald-200/50');
        nav.classList.add('bg-white', 'shadow-sm', 'border-b-gray-200');
    }
});
</script>
