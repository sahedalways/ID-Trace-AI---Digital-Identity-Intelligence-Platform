<?php
/**
 * File: affiliate-register.php
 * Secure registration gateway for new affiliate partners.
 * Validates inputs, captures details, hashes passwords, generates unique AID tokens, and handles database storage.
 */
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$message = '';
$status_type = ''; // 'success' or 'error'

/**
 * Generates an 8-character unique alphanumeric Affiliate ID (capital letters and numbers).
 * Example: 4M7X9K2P
 */
function generateUniqueAffiliateId() {
    $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < 8; $i++) {
        $random_string .= $pool[random_int(0, strlen($pool) - 1)];
    }
    return $random_string;
}

// 1. Detect Country Code via Cloudflare Header Matrix with no hardcoded fallback defaults
$cf_country_code = isset($_SERVER["HTTP_CF_IPCOUNTRY"]) ? strtoupper(trim($_SERVER["HTTP_CF_IPCOUNTRY"])) : '';

// 2. Fetch Countries Matrix from Shared json Cache
$country_matrix = [];
$country_cache_file = __DIR__ . '/cache_countries.json';

if (file_exists($country_cache_file)) {
    $country_matrix = json_decode(file_get_contents($country_cache_file), true);
}

if (empty($country_matrix) || !is_array($country_matrix)) {
    $country_matrix = ['BD' => 'Bangladesh', 'US' => 'United States', 'GB' => 'United Kingdom', 'CA' => 'Canada', 'AU' => 'Australia'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and collect input data layers
    $name             = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email            = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $password         = isset($_POST['password']) ? $_POST['password'] : '';
    $country          = isset($_POST['country']) ? strtoupper(trim($_POST['country'])) : '';
    $experience_level = isset($_POST['experience_level']) ? trim($_POST['experience_level']) : '';
    $past_experience  = isset($_POST['past_experience']) ? trim($_POST['past_experience']) : '';
    $contact          = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $traffic_source   = isset($_POST['traffic_source']) ? trim($_POST['traffic_source']) : '';

    // Strict validation verification block across target variables
    if (empty($name) || empty($email) || empty($password) || empty($country) || empty($experience_level) || empty($contact) || empty($traffic_source)) {
        $message = "Please fill in all required operational profile fields.";
        $status_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please provide a valid email address routing node.";
        $status_type = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters long for optimal security.";
        $status_type = "error";
    } else {
        try {
            $checkStmt = $pdo->prepare("SELECT `id` FROM `affiliates` WHERE `email` = ? LIMIT 1");
            $checkStmt->execute([$email]);
            
            if ($checkStmt->fetch()) {
                $message = "This email is already registered inside our network parameters.";
                $status_type = "error";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $unique_aid = generateUniqueAffiliateId();

                // Store affiliate including experience metrics vectors
                $insertStmt = $pdo->prepare("
                    INSERT INTO `affiliates` 
                    (`aid`, `name`, `email`, `password`, `country`, `experience_level`, `past_experience`, `status`, `balance`, `withdraw`, `traffic_source`, `contact`, `created_at`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', 0.00, 0.00, ?, ?, NOW())
                ");
                
                $insertStmt->execute([
                    $unique_aid, $name, $email, $hashedPassword, $country, 
                    $experience_level, $past_experience, $traffic_source, $contact
                ]);

                $status_type = "success";
            }
        } catch (PDOException $e) {
            error_log("Affiliate Registration Database Crash: " . $e->getMessage());
            $message = "An internal schema query error occurred. Please try again later.";
            $status_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Become an Affiliate Partner - ID Trace AI</title>
    <?php include 'affiliate-head.php'; ?>
</head>
<body class="min-h-screen text-slate-900 font-sans antialiased flex flex-col justify-between selection:bg-[#128c7e] selection:text-white bg-[#f9fafb]">

    <?php include 'affiliate-navbar.php'; ?>

    <main class="flex-grow flex items-center justify-center px-4 py-12 w-full">
        <div class="max-w-md w-full space-y-6">
            
            <div class="text-center space-y-2">
                <div class="inline-flex p-3.5 bg-emerald-50 text-[#128c7e] rounded-2xl border border-emerald-100 text-2xl">
                    <i class="fa-solid fa-users-gear"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Affiliate Partner Network</h2>
                <p class="text-xs font-semibold text-gray-400">Apply to promote our advanced tracking solutions and earn commissions.</p>
            </div>

            <?php if ($status_type === 'success'): ?>
                <div class="bg-white border border-gray-200 shadow-sm rounded-3xl p-8 text-center space-y-6">
                    <div class="relative w-16 h-16 mx-auto flex items-center justify-center bg-amber-50 text-amber-600 rounded-2xl border border-amber-100 text-2xl">
                        <i class="fa-solid fa-hourglass-half animate-pulse"></i>
                    </div>
                    
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-gray-900 tracking-tight">Application Under Review</h3>
                        <p class="text-xs font-semibold text-gray-500 leading-relaxed">
                            Thank you for applying, <span class="text-gray-900 font-bold"><?= htmlspecialchars($name) ?></span>! Your account details have been securely stored in our network parameters.
                        </p>
                        <p class="text-xs text-gray-400 bg-slate-50 border border-gray-200/60 rounded-xl p-4 mt-4 text-left leading-relaxed font-semibold">
                            <i class="fa-solid fa-circle-info text-[#128c7e] mr-1"></i> Our vetting team will review your traffic channels manually. You will receive an automated notification email instantly once your status updates from pending to active.
                        </p>
                    </div>

                    <div class="pt-2">
                        <a href="affiliate-login.php" class="block w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white text-xs font-bold py-4 rounded-xl transition-all shadow-sm cursor-pointer border border-transparent">
                            Return to Authentication Gate
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <div class="bg-white border border-gray-200 shadow-sm rounded-3xl p-6 sm:p-8 space-y-6">
                    
                    <?php if (!empty($message) && $status_type === 'error'): ?>
                        <div class="bg-red-50 border border-red-100 rounded-xl p-4 flex gap-3 text-left">
                            <i class="fa-solid fa-circle-exclamation text-red-500 text-base shrink-0 pt-0.5"></i>
                            <p class="text-xs text-red-700 font-semibold leading-relaxed"><?= htmlspecialchars($message) ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="affiliate-register.php" method="POST" class="space-y-4 text-left">
                        
                        <div class="space-y-1.5">
                            <label for="name" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Full Name *</label>
                            <input type="text" id="name" name="name" required value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" placeholder="John Doe" 
                                class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400">
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Email Address *</label>
                            <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" placeholder="partner@domain.com" 
                                class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400">
                        </div>

                        <div class="space-y-1.5">
                            <label for="password" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Account Password *</label>
                            <input type="password" id="password" name="password" required minlength="6" placeholder="••••••••" 
                                class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400">
                        </div>

                        <div class="space-y-1.5">
                            <label for="country" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Country Base *</label>
                            <select id="country" name="country" required class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 cursor-pointer">
                                <option value="">Select Country</option>
                                <?php foreach ($country_matrix as $iso => $country_name): ?>
                                    <option value="<?= $iso; ?>" <?= ((isset($_POST['country']) && $_POST['country'] === $iso) || (!isset($_POST['country']) && $cf_country_code === $iso)) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($country_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="experience_level" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Experience Level *</label>
                            <select id="experience_level" name="experience_level" required class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 cursor-pointer">
                                <option value="">Select Skill Tier</option>
                                <option value="New Affiliate" <?= (isset($_POST['experience_level']) && $_POST['experience_level'] === 'New Affiliate') ? 'selected' : ''; ?>>New Affiliate</option>
                                <option value="Have some experience" <?= (isset($_POST['experience_level']) && $_POST['experience_level'] === 'Have some experience') ? 'selected' : ''; ?>>Have some experience</option>
                                <option value="Expert" <?= (isset($_POST['experience_level']) && $_POST['experience_level'] === 'Expert') ? 'selected' : ''; ?>>Expert</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="contact" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Telegram Username *</label>
                            <input type="text" id="contact" name="contact" required value="<?= isset($_POST['contact']) ? htmlspecialchars($_POST['contact']) : '' ?>" placeholder="@username" 
                                class="w-full text-sm px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400">
                        </div>

                        <div class="space-y-1.5">
                            <label for="past_experience" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Past Affiliate Experience</label>
                            <textarea id="past_experience" name="past_experience" rows="3" placeholder="Tell us about previous networks or campaigns you have managed (e.g. MaxBounty CPA networks, media buying)..." 
                                class="w-full text-sm p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400 resize-none"><?= isset($_POST['past_experience']) ? htmlspecialchars($_POST['past_experience']) : '' ?></textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label for="traffic_source" class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Traffic Source & Promotional Strategy *</label>
                            <textarea id="traffic_source" name="traffic_source" required rows="3" placeholder="Tell us about your traffic channels: Websites URLs, Paid Ad Networks, Social Media profiles, or Lead Generation strategies..." 
                                class="w-full text-sm p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#128c7e] focus:bg-white focus:ring-1 focus:ring-[#128c7e] transition-all font-semibold text-gray-900 placeholder-gray-400 resize-none"><?= isset($_POST['traffic_source']) ? htmlspecialchars($_POST['traffic_source']) : '' ?></textarea>
                        </div>

                        <button type="submit" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white font-bold text-sm py-4 px-4 rounded-xl transition-all shadow-sm mt-2 cursor-pointer border border-transparent">
                            Submit Partnership Application
                        </button>
                    </form>

                    <div class="border-t border-gray-100 pt-4 text-center">
                        <p class="text-xs text-gray-400 font-semibold">Already a registered partner? <a href="affiliate-login.php" class="text-[#128c7e] font-bold hover:underline">Log in here</a></p>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <footer class="w-full text-center py-6 border-t border-gray-200 bg-white/50 backdrop-blur-sm text-xs font-semibold text-gray-400">
        &copy; 2026 Identity Trace AI Affiliate Portal. All rights reserved.
    </footer>

</body>
</html>
