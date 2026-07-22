<?php
/**
 * File: affiliate-logout.php
 * Automated Session Destroy Pipeline for Affiliate Partners.
 * Safely flushes specific auth vectors and maps terminal headers to the landing portal.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Flush specific authentication parameters from active session blocks
if (isset($_SESSION['affiliate_id'])) {
    unset($_SESSION['affiliate_id']);
}

// 2. Clear out residual session cookies matching browser storage settings
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// 3. Clear overall runtime memory scopes and completely tear down container state
$_SESSION = [];
session_destroy();

// 4. Fire absolute header routing map redirect back onto portal access interface
header("Location: affiliate-portal");
exit;