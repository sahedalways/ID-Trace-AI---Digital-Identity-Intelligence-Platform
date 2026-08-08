<?php
/**
 * File: go.php
 * Traffic distribution processing node.
 * Captures, processes, logs inbound marketing parameters, and drops cookie layers.
 */
require_once 'config.php';

// 1. Collect and clean query parameters (Accepting alphanumeric tracking string aid values)
$raw_aid = isset($_GET['id']) ? strtoupper(trim($_GET['id'])) : '';
$s1      = isset($_GET['s1']) ? trim($_GET['s1']) : null;
$s2      = isset($_GET['s2']) ? trim($_GET['s2']) : null;

// Fallback protection: If no parameter text is passed, push out to main root index directly
if (empty($raw_aid)) {
    header("Location: index");
    exit;
}

$affiliateId = 0;

try {
    // 2. Look up real system affiliate configuration matching structural alphanumeric values
    $stmt = $pdo->prepare("SELECT `id`, `referral_enabled` FROM `affiliates` WHERE `aid` = ? AND `status` = 'active' LIMIT 1");
    $stmt->execute([$raw_aid]);
    $affRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($affRow) {
        $affiliateId = (int)$affRow['id'];
        $referralEnabled = (int)($affRow['referral_enabled'] ?? 1) === 1;
    }
} catch (PDOException $e) {
    error_log("Affiliate Verification Node Intercept Error: " . $e->getMessage());
}

// Fallback protection: Reject traffic routing loops if token entry is unauthorized or missing
if ($affiliateId <= 0) {
    header("Location: index");
    exit;
}

// Referral deactivation guard: If admin disabled referrals for this affiliate,
// skip click tracking entirely and send visitors straight to the index page.
if (!$referralEnabled) {
    header("Location: index");
    exit;
}

// 3. Extract environmental data parameters from raw PHP server headers
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

// Check for common reverse proxy header if not utilizing direct Cloudflare routing matrices
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $ip = trim($ipList[0]);
}

// Parse geography securely using cloudflare headers, default to null if missing
$country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY'])) : null;
if ($country === 'XX' || empty($country)) {
    $country = null; // Clean out edge case placeholders
}

$referrer = $_SERVER['HTTP_REFERER'] ?? null;
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// 4. Programmatic User Agent Parser Matrix (OS & Browser Detection Logic)
// NOTE: parseUserAgentPlatform() lives in config.php
list($detectedOs, $detectedBrowser) = parseUserAgentPlatform($userAgent);

// 5. Generate Unique 16-Digit Alphanumeric Click Transaction ID Matrix Token (cid)
function generateUniqueClickToken($length = 16) {
    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $token = '';
    $max = strlen($pool) - 1;
    for ($i = 0; $i < $length; $i++) {
        $token .= $pool[random_int(0, $max)];
    }
    return $token;
}
$cid = generateUniqueClickToken(16);

// 6. Store parsed data layers directly to the persistent 'clicks' tracking matrix table
try {
    $insertStmt = $pdo->prepare("
        INSERT INTO `clicks` 
        (`cid`, `affid`, `s1`, `s2`, `ip`, `country`, `os`, `browser`, `referrer`, `ua`, `created_at`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $insertStmt->execute([
        $cid,
        $affiliateId, // Storing primary database user ID for unified relational queries mappings
        $s1,
        $s2,
        $ip,
        $country,
        $detectedOs,
        $detectedBrowser,
        $referrer,
        $userAgent
    ]);

} catch (PDOException $e) {
    // Gracefully handle database crash error logs silently so traffic loops do not display blank SQL layouts
    error_log("Click Tracker Pipeline Drop Exception: " . $e->getMessage());
}

// 7. Synchronize persistent browser engine tracking cookies (Valid for 30 Days)
setcookie('affiliate_click_id', $cid, time() + (86400 * 30), "/");
setcookie('affiliate_ref_id', $affiliateId, time() + (86400 * 30), "/");

// Strict Last-Click Attribution Sync: Unconditionally update active server session metrics instantly
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['active_cid'] = $cid;

// 8. Extract structural base deployment target matching your international site address layer
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$baseUrl = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . "/index";

// Clean out string directory formatting anomalies 
$baseUrl = str_replace('//index', '/index', $baseUrl);

// Append structured tracking transaction matrices parameters cleanly down to the redirect header routing channel
$redirectDestination = $baseUrl . "?cid=" . urlencode($cid);

// Dispatch redirection headers instantly to exit the script scope execution engine
header("Location: " . $redirectDestination);
exit;