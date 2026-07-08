<?php
/**
 * OSINT Universal Intelligence Console — Secure Session Termination Terminal
 * File: logout.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Extract the redirect fallback route safely from the URL query parameter string
$return_path = isset($_GET['return']) ? trim($_GET['return']) : '/index.php';

// 2. Security sanity check: Prevent open-redirect vulnerabilities
if (empty($return_path) || preg_match('#^https?://#i', $return_path)) {
    $return_path = '/index.php';
}

// 3. Clear and destroy the session variables completely
$_SESSION = [];

// 4. Wipe the tracking cookie from the client browser cache layer
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

// 5. Destroy the file cluster data layer on the local filesystem
session_destroy();

/**
 * 6. DYNAMIC REDIRECTION ROUTER
 * If the return path starts with a single leading slash but does not contain 
 * your base directory name, we strip it out and bind it safely to BASE_URL.
 */
$clean_target = ltrim($return_path, '/');

// Build the final fully-qualified URL location target string safely
$final_redirect_url = BASE_URL . $clean_target;

header("Location: " . $final_redirect_url);
exit;
