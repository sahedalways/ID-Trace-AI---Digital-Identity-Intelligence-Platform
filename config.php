<?php
// File: config.php
// Prevent direct access to this file if hit directly in the browser
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    die('Direct access not permitted.');
}

// FORCE PHP RUNTIME TO UTC
date_default_timezone_set('UTC');

// Load environment variables from .env file
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }
        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));
        // Remove surrounding quotes if present
        if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
            (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
            $value = substr($value, 1, -1);
        }
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

/**
 * Get environment variable with fallback
 */
function env($key, $default = null) {
    $value = $_ENV[$key] ?? getenv($key);
    return $value !== false && $value !== null ? $value : $default;
}

// 1. Application Configuration
define('APP_ENV', env('APP_ENV', 'production'));
define('BASE_URL', env('BASE_URL', 'https://idtrace.ai/'));

// 2. Database Configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'id_db'));
define('DB_USER', env('DB_USER', 'sahed'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

// 3. Stripe Configuration
define('STRIPE_TEST_PUBLISHABLE_KEY', env('STRIPE_PUBLISHABLE_KEY'));
define('STRIPE_TEST_SECRET_KEY', env('STRIPE_SECRET_KEY'));

// 4. Brevo Transactional API Configuration
define('BREVO_API_KEY', env('BREVO_API_KEY'));

// Custom domain identities for authentication mailers
define('MAIL_FROM_EMAIL', env('MAIL_FROM_EMAIL', 'support@identitysearch.ai'));
define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'Identity Search AI'));

// 5. Google OAuth2 Configuration
define('GOOGLE_CLIENT_ID', env('GOOGLE_CLIENT_ID'));
define('GOOGLE_CLIENT_SECRET', env('GOOGLE_CLIENT_SECRET'));
define('GOOGLE_REDIRECT_URI', env('GOOGLE_REDIRECT_URI', 'https://idtrace.ai/auth_google'));

// 6. Google Gemini AI Configuration
define('GEMINI_API_KEY', env('GEMINI_API_KEY'));

// 7. SocialFetch API Configuration
define('SOCIALFETCH_API_KEY', env('SOCIALFETCH_API_KEY'));
define('SOCIALFETCH_API_KEY_2', env('SOCIALFETCH_API_KEY_2'));

// 8. Apify API Configuration
define('APIFY_API_KEY', env('APIFY_API_KEY'));
define('APIFY_API_KEY_2', env('APIFY_API_KEY_2'));

// 9. RapidAPI (Eyecon3) Configuration
define('RAPIDAPI_API_KEY', env('RAPIDAPI_API_KEY'));
define('RAPIDAPI_HOST', env('RAPIDAPI_HOST', 'eyecon3.p.rapidapi.com'));

// 10. SearchBug API Configuration
define('SEARCHBUG_API_KEY', env('SEARCHBUG_API_KEY'));
define('SEARCHBUG_CO_CODE', env('SEARCHBUG_CO_CODE'));
define('SEARCHBUG_BASE_URL', env('SEARCHBUG_BASE_URL', 'https://data.searchbug.com/api/search.aspx'));

// 11. Stripe Webhook Secret
define('STRIPE_WEBHOOK_SECRET', env('STRIPE_WEBHOOK_SECRET'));

// 6. PDO Connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    // FORCE MYSQL SESSION TO UTC
    // This guarantees that any instance of NOW() or CURRENT_TIMESTAMP returns UTC time.
    $pdo->exec("SET time_zone = '+00:00'");
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// 7. Affiliate Bonus Helper Functions
function getAffiliateBonusAmount($pdo, $affiliateId) {
    $stmt = $pdo->prepare("SELECT `referral_bonus`, `use_global_settings` FROM `affiliates` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$affiliateId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data || $data['use_global_settings'] || $data['referral_bonus'] === null) {
        $gStmt = $pdo->prepare("SELECT `setting_value` FROM `affiliate_settings` WHERE `setting_key` = 'global_bonus_amount' LIMIT 1");
        $gStmt->execute();
        return (float)$gStmt->fetchColumn();
    }
    return (float)$data['referral_bonus'];
}

function getAffiliateBonusType($pdo, $affiliateId) {
    $stmt = $pdo->prepare("SELECT `bonus_type`, `use_global_settings` FROM `affiliates` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$affiliateId]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data || $data['use_global_settings'] || $data['bonus_type'] === null) {
        $gStmt = $pdo->prepare("SELECT `setting_value` FROM `affiliate_settings` WHERE `setting_key` = 'global_bonus_type' LIMIT 1");
        $gStmt->execute();
        return $gStmt->fetchColumn();
    }
    return $data['bonus_type'];
}

function getGlobalBonusAmount($pdo) {
    $stmt = $pdo->prepare("SELECT `setting_value` FROM `affiliate_settings` WHERE `setting_key` = 'global_bonus_amount' LIMIT 1");
    $stmt->execute();
    return (float)$stmt->fetchColumn();
}

function getGlobalBonusType($pdo) {
    $stmt = $pdo->prepare("SELECT `setting_value` FROM `affiliate_settings` WHERE `setting_key` = 'global_bonus_type' LIMIT 1");
    $stmt->execute();
    return $stmt->fetchColumn();
}

// 8. Fire-and-Forget Asynchronous Background Spawner
/**
 * Spawns an independent background CLI worker process to handle OSINT operations
 * without blocking or locking the Nginx/php-fpm web request channel.
 * * @param string $vid The target view identifier code.
 */
function fireBackgroundWorker($vid)
{
    // Generate secure terminal instruction string pointing directly to the root directory
    $cmd = "php " . __DIR__ . "/process.php id=" . escapeshellarg($vid);

    // Check operating system matrix to route shell spawning methods properly
    if (substr(php_uname(), 0, 7) == "Windows") {
        pclose(popen("start /B " . $cmd, "r"));
    } else {
        // Run as a detached system daemon process in Linux, dumping output streams to prevent timeout locks
        exec($cmd . " > /dev/null 2>&1 &");
    }
}

// 9. Client Device / Session Fingerprint Helpers
/**
 * Resolve the real client IP address, trusting Cloudflare and reverse proxy headers.
 */
function getClientIp() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return trim($_SERVER['HTTP_CF_CONNECTING_IP']);
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ipList[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Parse a User-Agent string into [Operating System, Browser].
 */
function parseUserAgentPlatform($ua) {
    $os = 'Unknown OS';
    $browser = 'Unknown Browser';

    // A. Detect Operating System Profile
    $osMatrix = [
        '/windows nt 10/i'      => 'Windows 10/11',
        '/windows nt 6.3/i'     => 'Windows 8.1',
        '/windows nt 6.2/i'     => 'Windows 8',
        '/windows nt 6.1/i'     => 'Windows 7',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/iphone|ipad|ipod/i'   => 'iOS',
        '/android/i'            => 'Android',
        '/linux/i'              => 'Linux',
        '/ubuntu/i'             => 'Ubuntu'
    ];
    foreach ($osMatrix as $regex => $title) {
        if (preg_match($regex, $ua)) {
            $os = $title;
            break;
        }
    }

    // B. Detect Browser Application Identity
    // Reordered execution order since Chrome includes Safari signatures inside its signature format
    if (preg_match('/edge|edg/i', $ua)) {
        $browser = 'Edge';
    } elseif (preg_match('/opr/i', $ua)) {
        $browser = 'Opera';
    } elseif (preg_match('/chrome/i', $ua)) {
        $browser = 'Chrome';
    } elseif (preg_match('/firefox/i', $ua)) {
        $browser = 'Firefox';
    } elseif (preg_match('/safari/i', $ua)) {
        $browser = 'Safari';
    } elseif (preg_match('/msie|trident/i', $ua)) {
        $browser = 'Internet Explorer';
    }

    return [$os, $browser];
}

/**
 * Classify the device form factor from a User-Agent string.
 */
function detectDeviceType($ua) {
    if (preg_match('/ipad|tablet|playbook|silk/i', $ua)) return 'Tablet';
    if (preg_match('/iphone|ipod|android.*mobile|mobile|opera mini|windows phone/i', $ua)) return 'Mobile';
    return 'Desktop';
}

/**
 * Persist a login fingerprint row (IP, device, browser, user agent) for a client.
 * Failures are logged silently so the authentication flow is never blocked.
 */
function recordLoginSession($pdo, $userId) {
    try {
        $ua      = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip      = getClientIp();
        list($os, $browser) = parseUserAgentPlatform($ua);
        $device  = detectDeviceType($ua);

        $stmt = $pdo->prepare("INSERT INTO `login_sessions` (`uid`, `ip_address`, `device`, `browser`, `user_agent`, `created_at`) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([(int)$userId, $ip, $device, $browser, $ua]);
    } catch (\PDOException $e) {
        error_log("Login Session Recording Error: " . $e->getMessage());
    }
}