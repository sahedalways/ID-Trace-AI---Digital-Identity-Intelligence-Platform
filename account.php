<?php
/**
 * OSINT Universal Intelligence Console — Account Profiler Management Node
 * File: account.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Enforcement
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "signin");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Automated Open-Source Country List Retriever Data Block Matrix
$country_matrix = [];
$country_cache_file = __DIR__ . '/cache_countries.json';

// Check cache first (Valid for 7 days)
if (file_exists($country_cache_file) && (time() - filemtime($country_cache_file) < 86400 * 7)) {
    $country_matrix = json_decode(file_get_contents($country_cache_file), true);
}

// Fetch live from reliable standard flat open-source dictionary if cache empty
if (empty($country_matrix) || !is_array($country_matrix)) {
    $remote_cdn_url = 'https://cdn.jsdelivr.net/gh/umpirsky/country-list@master/data/en/country.json';
    $ch = curl_init($remote_cdn_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) OSINT-Console-Agent/1.0');
    $response = curl_exec($ch);
    curl_close($ch);

    if (!empty($response)) {
        $raw_data = json_decode($response, true);
        if (is_array($raw_data) && !empty($raw_data)) {
            $country_matrix = [];
            foreach ($raw_data as $iso => $name) {
                $country_matrix[strtoupper(trim($iso))] = trim($name);
            }
            asort($country_matrix); // Alphabetize naturally
            file_put_contents($country_cache_file, json_encode($country_matrix));
        }
    }
}

// Solid baseline emergency fallback array if server sandbox outbound curls are completely blocked
if (empty($country_matrix) || !is_array($country_matrix)) {
    $country_matrix = [
        'BD' => 'Bangladesh', 'US' => 'United States', 'GB' => 'United Kingdom', 
        'CA' => 'Canada', 'AU' => 'Australia', 'AE' => 'United Arab Emirates', 
        'SA' => 'Saudi Arabia', 'DE' => 'Germany', 'FR' => 'France', 'IN' => 'India'
    ];
}

// Handle Flashing Operational System Flags
$success_msg = isset($_SESSION['flash_msg']) ? $_SESSION['flash_msg'] : '';
unset($_SESSION['flash_msg']);

// Process Profile Mutation Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $new_name   = trim($_POST['name'] ?? '');
    $new_iso    = strtoupper(trim($_POST['country'] ?? ''));
    $new_street = trim($_POST['street'] ?? '');
    $new_zip    = trim($_POST['zip'] ?? '');

    if (!empty($new_name)) {
        if (!array_key_exists($new_iso, $country_matrix)) {
            $new_iso = null;
        }

        $stmt = $pdo->prepare("UPDATE `users` SET `name` = ?, `country` = ?, `street` = ?, `zip` = ? WHERE `id` = ?");
        $stmt->execute([$new_name, $new_iso, $new_street, $new_zip, $user_id]);
        
        $_SESSION['user_name'] = $new_name;
        $_SESSION['user_country'] = $new_iso;
        
        $_SESSION['flash_msg'] = "Profile intelligence credentials optimized successfully.";
    }

    header("Location: " . BASE_URL . "account");
    exit;
}

// Extract current target context data points
$stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Error: Target console user sequence broken.");
}

$current_iso = strtoupper(trim($user['country'] ?? ''));
$display_country = $country_matrix[$current_iso] ?? 'Not Specified';
$firstLetter = !empty($user['name']) ? strtoupper(substr(trim($user['name']), 0, 1)) : 'U';
$cleanName = !empty($user['name']) ? htmlspecialchars($user['name']) : 'User Profile';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $cleanName; ?> — Identity Search AI</title>
    <?php include 'head.php'; ?>
    <style>
        body { background-color: #f9fafb !important; color: #111827 !important; }
        .row-grid { display: grid; grid-template-columns: 160px 1fr; gap: 1rem; align-items: center; }
        @media (max-width: 640px) {
            .row-grid { grid-template-columns: 1fr; gap: 0.25rem; align-items: start; }
        }
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

    <main class="flex-grow max-w-2xl w-full mx-auto px-4 py-8 space-y-6">
        
        <?php if (!empty($success_msg)): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-xs font-semibold shadow-sm animate-fade-in flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> <?php echo htmlspecialchars($success_msg); ?>
            </div>
        <?php endif; ?>

        <div class="flex flex-col items-center justify-center space-y-3 py-4">
            <div class="relative w-20 h-20 rounded-full overflow-hidden border border-gray-200 bg-white flex items-center justify-center shadow-sm">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo (strpos($user['avatar'], 'avatars/') === 0) ? $user['avatar'] : 'proxy.php?url=' . urlencode(base64_encode($user['avatar'])); ?>" 
                         alt="<?php echo $cleanName; ?>" 
                         class="w-full h-full object-cover relative z-10"
                         onerror="this.style.display='none'; document.getElementById('letterFallbackAvatar').classList.remove('hidden');">
                <?php endif; ?>
                <div id="letterFallbackAvatar" class="absolute inset-0 w-full h-full bg-emerald-50 text-[#0072bc] font-bold text-3xl flex items-center justify-center uppercase <?php echo !empty($user['avatar']) ? 'hidden' : ''; ?>">
                    <?php echo $firstLetter; ?>
                </div>
            </div>
            <h1 class="text-xl font-bold tracking-tight text-gray-900"><?php echo $cleanName; ?></h1>
        </div>

        <div id="readOnlyDossierCard" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
            <div class="divide-y divide-gray-100 text-xs font-semibold text-left">
                <div class="row-grid px-6 py-4.5">
                    <span class="text-gray-400 uppercase tracking-wider text-[10px] flex items-center gap-2"><i class="fa-solid fa-user text-gray-400 w-3.5 text-center"></i> Full Name</span>
                    <span class="text-gray-900 font-semibold text-sm"><?php echo htmlspecialchars($user['name'] ?? 'Not Configured'); ?></span>
                </div>
                <div class="row-grid px-6 py-4.5">
                    <span class="text-gray-400 uppercase tracking-wider text-[10px] flex items-center gap-2"><i class="fa-solid fa-envelope text-gray-400 w-3.5 text-center"></i> Email Address</span>
                    <span class="text-gray-900 font-semibold text-sm"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="row-grid px-6 py-4.5">
                    <span class="text-gray-400 uppercase tracking-wider text-[10px] flex items-center gap-2"><i class="fa-solid fa-earth-americas text-gray-400 w-3.5 text-center"></i> Country Anchor</span>
                    <span class="text-gray-900 font-semibold text-sm"><?php echo htmlspecialchars($display_country); ?></span>
                </div>
                <div class="row-grid px-6 py-4.5">
                    <span class="text-gray-400 uppercase tracking-wider text-[10px] flex items-center gap-2"><i class="fa-solid fa-location-dot text-gray-400 w-3.5 text-center"></i> Street Address</span>
                    <span class="text-gray-900 font-semibold text-sm"><?php echo htmlspecialchars($user['street'] ?? 'Not Configured'); ?></span>
                </div>
                <div class="row-grid px-6 py-4.5">
                    <span class="text-gray-400 uppercase tracking-wider text-[10px] flex items-center gap-2"><i class="fa-solid fa-envelope-open-text text-gray-400 w-3.5 text-center"></i> Zip Code</span>
                    <span class="text-gray-900 font-semibold text-sm"><?php echo htmlspecialchars($user['zip'] ?? 'Not Configured'); ?></span>
                </div>
                <div class="row-grid px-6 py-4.5">
                    <span class="text-gray-400 uppercase tracking-wider text-[10px] flex items-center gap-2"><i class="fa-solid fa-calendar-day text-gray-400 w-3.5 text-center"></i> Joined Date</span>
                    <span class="text-gray-900 font-semibold text-sm"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></span>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center">
                <button type="button" onclick="switchToEditMode(true)" class="w-full bg-[#0072bc] hover:bg-[#005ea3] text-white py-3 px-6 rounded-xl text-xs font-bold shadow-sm transition-colors cursor-pointer flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-gear text-sm"></i> Modify Profile Variables
                </button>
            </div>
        </div>

        <div id="editableWorkspaceCard" class="hidden bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
            <div class="px-6 py-5 border-b border-gray-100 text-left">
                <h3 class="text-sm font-bold text-gray-900">Update Profile Fields</h3>
            </div>
            
            <form action="account" method="POST" class="p-6 space-y-4 text-left">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-user text-gray-400 text-[10px]"></i> Full Name</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs font-semibold text-black focus:outline-none focus:ring-2 focus:ring-[#0072bc] focus:border-[#0072bc] focus:bg-white transition-all" autocomplete="off">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-earth-americas text-gray-400 text-[10px]"></i> Country Jurisdiction</label>
                    <select name="country" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs font-semibold text-black focus:outline-none focus:ring-2 focus:ring-[#0072bc] focus:border-[#0072bc] focus:bg-white transition-all cursor-pointer">
                        <option value="">Select Target Country</option>
                        <?php foreach ($country_matrix as $iso_key => $country_name): ?>
                            <option value="<?php echo $iso_key; ?>" <?php echo ($current_iso === $iso_key) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($country_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-location-dot text-gray-400 text-[10px]"></i> Street Address</label>
                    <input type="text" name="street" value="<?php echo htmlspecialchars($user['street'] ?? ''); ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs font-semibold text-black focus:outline-none focus:ring-2 focus:ring-[#0072bc] focus:border-[#0072bc] focus:bg-white transition-all" placeholder="1621 Central Ave" autocomplete="off">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide flex items-center gap-1.5"><i class="fa-solid fa-envelope-open-text text-gray-400 text-[10px]"></i> Zip Code</label>
                    <input type="text" name="zip" value="<?php echo htmlspecialchars($user['zip'] ?? ''); ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-xs font-semibold text-black focus:outline-none focus:ring-2 focus:ring-[#0072bc] focus:border-[#0072bc] focus:bg-white transition-all" placeholder="82001" autocomplete="off">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                    <button type="button" onclick="switchToEditMode(false)" class="px-5 py-2.5 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" class="bg-[#0072bc] hover:bg-[#005ea3] text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-colors cursor-pointer flex items-center gap-1.5">
                        <i class="fa-solid fa-floppy-disk"></i> Save Variables
                    </button>
                </div>
            </form>
        </div>

    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>

    <script>
        function switchToEditMode(enableEdit) {
            const readCard = document.getElementById('readOnlyDossierCard');
            const editCard = document.getElementById('editableWorkspaceCard');
            
            if (enableEdit) {
                readCard.classList.add('hidden');
                editCard.classList.remove('hidden');
            } else {
                editCard.classList.add('hidden');
                readCard.classList.remove('hidden');
            }
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