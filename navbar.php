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

// Home page uses the glassmorphic header wrapper (id="mainNavbar") from index.php.
// Other pages include navbar.php directly, so they need the solid white sticky nav.
// Detect home page via both the active script name and the request URI for router.php compatibility.
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$is_home_page = ($active_page === 'index') || ($request_path === '/' || $request_path === '/index.php' || rtrim($request_path, '/') === rtrim(parse_url(BASE_URL, PHP_URL_PATH), '/'));
?>

<style>
    .truncate-nav-text {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

<nav id="siteNavbar" class="transition-all duration-300 <?= $is_home_page ? '' : 'bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm' ?>">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <div class="flex-shrink-0">
                <a href="<?php echo BASE_URL; ?>" class="flex items-center group">
                    <img src="<?php echo LOGO_URL; ?>" alt="Identity Search AI Logo" class="h-12 w-auto">
                </a>
            </div>

            <div class="flex items-center gap-3 sm:gap-5">
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo BASE_URL; ?>my-plan" class="hidden sm:flex text-base font-semibold items-center gap-2 transition <?= $active_page === 'my-plan' ? 'text-[#0072bc]' : 'text-black hover:text-[#0072bc]' ?>">
                        <i class="fa-regular fa-credit-card <?= $active_page === 'my-plan' ? 'text-[#0072bc]' : 'text-slate-400' ?>"></i> Subscription
                    </a>
                    <a href="<?php echo BASE_URL; ?>my-report" class="hidden sm:flex text-base font-semibold items-center gap-2 transition <?= $active_page === 'my-report' ? 'text-[#0072bc]' : 'text-black hover:text-[#0072bc]' ?>">
                        <i class="fa-solid fa-folder-open text-sm <?= $active_page === 'my-report' ? 'text-[#0072bc]' : 'text-slate-400' ?>"></i> Reports
                    </a>
                    <a href="<?php echo BASE_URL; ?>my-promo" class="hidden sm:flex text-base font-semibold items-center gap-2 transition <?= $active_page === 'my-promo' ? 'text-[#0072bc]' : 'text-black hover:text-[#0072bc]' ?>">
                        <i class="fa-solid fa-ticket text-sm <?= $active_page === 'my-promo' ? 'text-[#0072bc]' : 'text-slate-400' ?>"></i> Promo Codes
                    </a>

                    <a href="<?php echo BASE_URL; ?>support" class="hidden sm:flex items-center gap-2 text-base font-semibold transition <?= $active_page === 'support' ? 'text-[#0072bc]' : 'text-black hover:text-[#0072bc]' ?>">
                        <i class="fa-solid fa-headset text-sm <?= $active_page === 'support' ? 'text-[#0072bc]' : 'text-slate-400' ?>"></i> Customer Support
                    </a>

                    <div class="relative">
                        <button id="userMenuButton" type="button" onclick="toggleUserDropdown(event)" class="flex items-center gap-2 text-base text-black font-semibold hover:text-[#0072bc] focus:outline-none py-1.5 px-3 hover:bg-gray-50 rounded-xl transition border border-transparent cursor-pointer">
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
                            <a href="<?php echo BASE_URL; ?>support" class="flex sm:hidden items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-headset text-slate-400 w-5"></i> Customer Support
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
                        <a href="<?php echo BASE_URL; ?>buy-credit" class="text-base font-semibold transition <?= $active_page === 'buy-credit' ? 'text-[#0072bc]' : 'text-black hover:text-[#0072bc]' ?>">
                            Pricing
                        </a>
                        <a href="<?php echo htmlspecialchars($signin_url); ?>" class="text-base font-semibold transition <?= $active_page === 'signin' ? 'text-[#0072bc]' : 'text-black hover:text-[#0072bc]' ?>">
                            Login
                        </a>
                        <a href="<?php echo htmlspecialchars($signin_url); ?>" class="text-base font-semibold transition <?= $active_page === 'signin' ? 'text-[#0072bc]' : 'text-black hover:text-[#0072bc]' ?>">
                            Register
                        </a>
                        <a href="<?php echo BASE_URL; ?>buy-credit" class="group flex items-center gap-2 text-base font-bold text-white bg-gradient-to-r from-[#0072bc] to-blue-600 hover:from-blue-600 hover:to-blue-700 px-5 py-2.5 rounded-xl hover:-translate-y-0.5 transition-all duration-300">
                            <span>Get Report</span>
                            <span class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/30 transition-colors">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </span>
                        </a>
                    </div>

                    <!-- Mobile View: Hamburger Button -->
                    <div class="md:hidden">
                        <button id="mobileMenuButton" type="button" onclick="toggleMobileMenu(event)" class="w-10 h-10 flex items-center justify-center text-black hover:text-[#0072bc] hover:bg-gray-50 rounded-xl transition border border-transparent focus:outline-none cursor-pointer text-lg" title="Open Navigation Menu">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 md:hidden opacity-0 pointer-events-none transition-opacity duration-300" onclick="closeMobileMenu()"></div>

    <!-- Mobile Menu Drawer -->
    <div id="mobileDrawer" class="fixed top-0 right-0 z-50 h-full w-[85%] max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 md:hidden overflow-y-auto">
        <div class="flex items-center justify-between px-5 h-16 border-b border-gray-100">
            <a href="<?php echo BASE_URL; ?>" class="flex items-center">
                <img src="<?php echo LOGO_URL; ?>" alt="Identity Search AI Logo" class="h-10 w-auto">
            </a>
            <button type="button" onclick="closeMobileMenu()" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        <div class="px-3 py-4 space-y-1 text-[15px] font-semibold">
            <?php if ($isLoggedIn): ?>
                <a href="<?php echo BASE_URL; ?>my-plan" class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:text-[#0072bc] hover:bg-gray-50 rounded-xl transition">
                    <i class="fa-regular fa-credit-card w-5 text-gray-400"></i> My Subscription
                </a>
                <a href="<?php echo BASE_URL; ?>my-report" class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:text-[#0072bc] hover:bg-gray-50 rounded-xl transition">
                    <i class="fa-solid fa-folder-open text-sm w-5 text-gray-400"></i> My Reports
                </a>
                <a href="<?php echo BASE_URL; ?>my-promo" class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:text-[#0072bc] hover:bg-gray-50 rounded-xl transition">
                    <i class="fa-solid fa-ticket text-sm w-5 text-gray-400"></i> Promo Codes
                </a>
                <a href="<?php echo BASE_URL; ?>account" class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:text-[#0072bc] hover:bg-gray-50 rounded-xl transition">
                    <i class="fa-solid fa-user-gear w-5 text-gray-400"></i> My Profile
                </a>
                <a href="<?php echo BASE_URL; ?>support" class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:text-[#0072bc] hover:bg-gray-50 rounded-xl transition">
                    <i class="fa-solid fa-headset text-sm w-5 text-gray-400"></i> Customer Support
                </a>
                <hr class="border-gray-100 my-2">
                <a href="<?php echo htmlspecialchars($logout_url); ?>" class="flex items-center gap-3 px-4 py-3 text-red-600 hover:bg-red-50/50 rounded-xl transition font-bold">
                    <i class="fa-solid fa-right-from-bracket w-5"></i> Sign Out
                </a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>buy-credit" class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:text-[#0072bc] hover:bg-gray-50 rounded-xl transition">
                    <i class="fa-solid fa-tag text-xs w-5 text-gray-400"></i> Pricing
                </a>
                <a href="<?php echo htmlspecialchars($signin_url); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:text-[#0072bc] hover:bg-gray-50 rounded-xl transition">
                    <i class="fa-solid fa-right-to-bracket text-sm w-5 text-gray-400"></i> Login
                </a>
                <a href="<?php echo htmlspecialchars($signin_url); ?>" class="flex items-center gap-3 px-4 py-3 text-gray-800 hover:text-[#0072bc] hover:bg-gray-50 rounded-xl transition">
                    <i class="fa-solid fa-user-plus text-sm w-5 text-gray-400"></i> Register
                </a>
                <a href="<?php echo BASE_URL; ?>buy-credit" class="flex items-center gap-3 px-4 py-3 text-white bg-gradient-to-r from-[#0072bc] to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-xl transition font-bold">
                    <i class="fa-solid fa-file-lines text-sm"></i> Get Report
                </a>
            <?php endif; ?>
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

    function openMobileMenu() {
        const drawer = document.getElementById('mobileDrawer');
        const overlay = document.getElementById('mobileMenuOverlay');
        if (!drawer || !overlay) return;
        drawer.classList.remove('translate-x-full');
        drawer.classList.add('translate-x-0');
        overlay.classList.remove('opacity-0', 'pointer-events-none');
        overlay.classList.add('opacity-100', 'pointer-events-auto');
        document.body.classList.add('overflow-hidden');
    }

    function closeMobileMenu() {
        const drawer = document.getElementById('mobileDrawer');
        const overlay = document.getElementById('mobileMenuOverlay');
        if (!drawer || !overlay) return;
        drawer.classList.remove('translate-x-0');
        drawer.classList.add('translate-x-full');
        overlay.classList.remove('opacity-100', 'pointer-events-auto');
        overlay.classList.add('opacity-0', 'pointer-events-none');
        document.body.classList.remove('overflow-hidden');
    }

    function toggleMobileMenu(event) {
        event.stopPropagation();
        const drawer = document.getElementById('mobileDrawer');
        if (!drawer) return;
        if (drawer.classList.contains('translate-x-0')) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    document.getElementById('mobileDrawer')?.addEventListener('click', function(e) {
        if (e.target.closest('a')) {
            closeMobileMenu();
        }
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            closeMobileMenu();
        }
    });
</script>