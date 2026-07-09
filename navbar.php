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
$signin_url = BASE_URL . "signin.php?return=" . urlencode($relative_return);
$logout_url = BASE_URL . "logout.php?return=" . urlencode($relative_return);
?>

<style>
    @keyframes fingerPulse {
        0% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(0,0,0,0)); }
        50% { transform: scale(1.05); filter: drop-shadow(0 0 6px rgba(18,140,126,0.12)); }
        100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(0,0,0,0)); }
    }
    
    .animate-fingerprint { display: inline-block; animation: fingerPulse 3s ease-in-out infinite; }
    .truncate-nav-text { max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
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
                <a href="<?php echo BASE_URL; ?>" class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900 flex items-center gap-2 group">
                    <span class="animate-fingerprint text-gray-900 flex items-center shrink-0 w-7 h-7 sm:w-8 sm:h-8">
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
                    <span class="bg-gradient-to-r from-gray-900 to-gray-800 bg-clip-text text-transparent font-bold tracking-tight">
                        Identity Search<span class="text-[14px] font-bold bg-black text-white px-2.5 py-0.5 rounded-md ml-1.5 align-middle tracking-wider">AI</span>
                    </span>
                </a>
            </div>

            <div class="flex items-center gap-2 sm:gap-4">
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo BASE_URL; ?>my-plan.php" class="hidden sm:flex text-base text-black font-semibold hover:text-[#128c7e] items-center gap-2 transition">
                        <i class="fa-regular fa-credit-card text-slate-400"></i> Subscription
                    </a>
                    <a href="<?php echo BASE_URL; ?>my-report.php" class="hidden sm:flex text-base text-black font-semibold hover:text-[#128c7e] items-center gap-2 transition">
                        <i class="fa-solid fa-folder-open text-slate-400 text-sm"></i> Reports
                    </a>
                    <a href="<?php echo BASE_URL; ?>my-promo.php" class="hidden sm:flex text-base text-black font-semibold hover:text-[#128c7e] items-center gap-2 transition">
                        <i class="fa-solid fa-ticket text-slate-400 text-sm"></i> Promo Codes
                    </a>
                    
                    <div class="relative">
                        <button type="button" onclick="toggleUserDropdown(event)" class="flex items-center gap-2 text-base text-black font-semibold hover:text-[#128c7e] focus:outline-none py-1.5 px-3 hover:bg-gray-50 rounded-xl transition border border-transparent cursor-pointer">
                            <span class="truncate-nav-text"><?php echo htmlspecialchars($userDisplayName); ?></span>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400 pointer-events-none"></i>
                        </button>
                        
                        <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl py-2 z-50 text-base text-black font-semibold">
                            <a href="<?php echo BASE_URL; ?>my-plan.php" class="flex sm:hidden items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-regular fa-credit-card text-slate-400 w-5"></i> My Subscription
                            </a>
                            <a href="<?php echo BASE_URL; ?>my-report.php" class="flex sm:hidden items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-folder-open text-slate-400 text-sm w-5"></i> My Reports
                            </a>
                            <a href="<?php echo BASE_URL; ?>my-promo.php" class="flex sm:hidden items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-ticket text-slate-400 text-sm w-5"></i> Promo Codes
                            </a>
                            <a href="<?php echo BASE_URL; ?>account.php" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-user-gear text-slate-400 w-5"></i> My Profile
                            </a>
                            <hr class="border-gray-100 my-1.5">
                            <a href="<?php echo htmlspecialchars($logout_url); ?>" class="flex items-center gap-2.5 px-4 py-2.5 text-red-600 hover:bg-red-50/50 font-bold transition">
                                <i class="fa-solid fa-right-from-bracket w-5"></i> Sign Out
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="relative">
                        <button type="button" onclick="toggleUserDropdown(event)" class="w-10 h-10 flex items-center justify-center text-black hover:text-[#128c7e] hover:bg-gray-50 rounded-xl transition border border-transparent focus:outline-none cursor-pointer text-lg" title="Open Navigation Menu">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        
                        <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-52 bg-white border border-gray-200 rounded-2xl shadow-xl py-2 z-50 text-base text-black font-semibold">
                            <a href="<?php echo htmlspecialchars($signin_url); ?>" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-user-plus text-slate-400 text-xs w-5"></i> Register
                            </a>
                            <a href="<?php echo htmlspecialchars($signin_url); ?>" class="flex items-center gap-2.5 px-4 py-2.5 hover:bg-gray-50 transition">
                                <i class="fa-solid fa-right-to-bracket text-slate-400 text-xs w-5"></i> Login
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
