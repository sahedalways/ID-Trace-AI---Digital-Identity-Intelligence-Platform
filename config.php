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
define('GOOGLE_REDIRECT_URI', env('GOOGLE_REDIRECT_URI', 'https://idtrace.ai/auth_google.php'));

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

// 10. Stripe Webhook Secret
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

// 7. Fire-and-Forget Asynchronous Background Spawner
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
