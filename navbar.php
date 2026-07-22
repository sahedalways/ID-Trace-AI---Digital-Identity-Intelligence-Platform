<?php

/**
 * Identity Search AI — Global Layout Navigation Component
 * File: navbar.php
 */

// Start session if not already started to check auth status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Dynamically handle display identity: Use name if available, fallback to email
$userDisplayName = !empty($_SESSION['user_name']) ? $_SESSION['user_name'] : ($_SESSION['user_email'] ?? 'Account');

// Capture raw execution query context mapping path
$current_uri = $_SERVER['REQUEST_URI'];

/**
 * PATH SANITIZATION ENGINE
 * Strips out the project subdirectory wrapper from BASE_URL (e.g., "/idtrace/")
 * to ensure parameters hold pure relative paths, avoiding path doubling bugs.
 */
$base_path = parse_url(BASE_URL, PHP_URL_PATH);
if ($base_path !== '/' && strpos($current_uri, $base_path) === 0) {
    $relative_return = '/' . ltrim(substr($current_uri, strlen($base_path)), '/');
} else {
    $relative_return = $current_uri;
}

// Build clean authorization endpoint link routing destinations
$signin_url = BASE_URL . "signin?return=" . urlencode($relative_return);
$logout_url = BASE_URL . "logout?return=" . urlencode($relative_return);

// Detect active page for menu highlighting
$active_page = pathinfo(basename($_SERVER['SCRIPT_FILENAME']), PATHINFO_FILENAME);
?>

<style>
    .truncate-nav-text {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<script>
    window.addEventListener('scroll', function() {
        const nav = document.getElementById('mainNavbar');
        if (window.scrollY > 20) {
            nav.classList.add('bg-emerald-50/60', 'backdrop-blur-3xl', 'shadow-[0_8px_32px_rgba(18,140,126,0.12)]', 'border-b-emerald-200/50');
            nav.classList.remove('bg-white', 'shadow-sm', 'border-b-gray-200');
        } else {
            nav.classList.remove('bg-emerald-50/60', 'backdrop-blur-3xl', 'shadow-[0_8px_32px_rgba(18,140,126,0.12)]', 'border-b-emerald-200/50');
            nav.classList.add('bg-white', 'shadow-sm', 'border-b-gray-200');
        }
    });
</script>



<nav id="mainNavbar" class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm transition-all duration-300">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <div class="flex-shrink-0">
                <a href="<?php echo BASE_URL; ?>" class="flex items-center group">
                    <img src="<?php echo BASE_URL; ?>public/logo.png" alt="Identity Search AI Logo" class="h-12 w-auto">
                </a>
            </div>

            <div class="flex items-center gap-3 sm:gap-5">
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo BASE_URL; ?>my-plan" class="hidden sm:flex text-base font-semibold items-center gap-2 transition <?= $active_page === 'my-plan' ? 'text-[#128c7e]' : 'text-black hover:text-[#128c7e]' ?>">
                        <i class="fa-regular fa-credit-card <?= $active_page === 'my-plan' ? 'text-[#128c7e]' : 'text-slate-400' ?>"></i> Subscription
                    </a>
                    <a href="<?php echo BASE_URL; ?>my-report" class="hidden sm:flex text-base font-semibold items-center gap-2 transition <?= $active_page === 'my-report' ? 'text-[#128c7e]' : 'text-black hover:text-[#128c7e]' ?>">
                        <i class="fa-solid fa-folder-open text-sm <?= $active_page === 'my-report' ? 'text-[#128c7e]' : 'text-slate-400' ?>"></i> Reports
                    </a>
                    <a href="<?php echo BASE_URL; ?>my-promo" class="hidden sm:flex text-base font-semibold items-center gap-2 transition <?= $active_page === 'my-promo' ? 'text-[#128c7e]' : 'text-black hover:text-[#128c7e]' ?>">
                        <i class="fa-solid fa-ticket text-sm <?= $active_page === 'my-promo' ? 'text-[#128c7e]' : 'text-slate-400' ?>"></i> Promo Codes
                    </a>

                    <div class="relative">
                        <button type="button" onclick="toggleUserDropdown(event)" class="flex items-center gap-2 text-base text-black font-semibold hover:text-[#128c7e] focus:outline-none py-1.5 px-3 hover:bg-gray-50 rounded-xl transition border border-transparent cursor-pointer">
                            <span class="truncate-nav-text"><?php echo htmlspecialchars($userDisplayName); ?></span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400 pointer-events-none"></i>
                        </button>

                        <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl py-2 z-50 text-base text-black font-semibold">
                            <a href="<?php echo BASE_URL; ?>my-plan" class="flex sm:hidden items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-regular fa-credit-card text-slate-400 w-5"></i> My Subscription
                            </a>
                            <a href="<?php echo BASE_URL; ?>my-report" class="flex sm:hidden items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-folder-open text-slate-400 text-sm w-5"></i> My Reports
                            </a>
                            <a href="<?php echo BASE_URL; ?>my-promo" class="flex sm:hidden items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-ticket text-slate-400 text-sm w-5"></i> Promo Codes
                            </a>
                            <a href="<?php echo BASE_URL; ?>account" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-user-gear text-slate-400 w-5"></i> My Profile
                            </a>
                            <hr class="border-gray-100 my-1.5">
                            <a href="<?php echo htmlspecialchars($logout_url); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-red-600 hover:bg-red-50/50 font-bold transition">
                                <i class="fa-solid fa-right-from-bracket w-5"></i> Sign Out
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Desktop View: Inline Row Menu -->
                    <div class="hidden md:flex items-center gap-6">
                        <a href="<?php echo BASE_URL; ?>buy-credit" class="text-base font-semibold transition <?= $active_page === 'buy-credit' ? 'text-[#128c7e]' : 'text-black hover:text-[#128c7e]' ?>">
                            Pricing
                        </a>
                        <a href="<?php echo htmlspecialchars($signin_url); ?>" class="text-base font-semibold transition <?= $active_page === 'signin' ? 'text-[#128c7e]' : 'text-black hover:text-[#128c7e]' ?>">
                            Login
                        </a>
                        <a href="<?php echo htmlspecialchars($signin_url); ?>" class="text-base font-semibold transition <?= $active_page === 'signin' ? 'text-[#128c7e]' : 'text-black hover:text-[#128c7e]' ?>">
                            Register
                        </a>
                        <a href="<?php echo BASE_URL; ?>buy-credit" class="text-base font-semibold <?= $active_page === 'buy-credit' ? 'text-[#128c7e] bg-emerald-50' : 'text-black hover:text-[#128c7e] bg-gray-100 hover:bg-gray-200' ?> px-5 py-2.5 rounded-xl transition">
                            Get Report
                        </a>
                    </div>

                    <!-- Mobile View: Hamburger Dropdown -->
                    <div class="relative md:hidden">
                        <button type="button" onclick="toggleUserDropdown(event)" class="w-10 h-10 flex items-center justify-center text-black hover:text-[#128c7e] hover:bg-gray-50 rounded-xl transition border border-transparent focus:outline-none cursor-pointer text-lg" title="Open Navigation Menu">
                            <i class="fa-solid fa-bars"></i>
                        </button>

                        <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl py-2 z-50 text-base text-black font-semibold">
                            <a href="<?php echo BASE_URL; ?>buy-credit" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-tag text-slate-400 text-xs w-5"></i> Pricing
                            </a>
                            <a href="<?php echo htmlspecialchars($signin_url); ?>" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-right-to-bracket text-slate-400 text-xs w-5"></i> Login
                            </a>
                            <a href="<?php echo htmlspecialchars($signin_url); ?>" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-user-plus text-slate-400 text-xs w-5"></i> Register
                            </a>
                            <a href="<?php echo BASE_URL; ?>buy-credit" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-file-lines text-slate-400 text-xs w-5"></i> Get Report
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>

<script>
    function toggleUserDropdown(event) {
        event.stopPropagation();
        const targetMenu = document.getElementById('userDropdownMenu');
        if (targetMenu) targetMenu.classList.toggle('hidden');
    }

    window.addEventListener('click', function() {
        const dropdown = document.getElementById('userDropdownMenu');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    });
</script>