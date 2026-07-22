<?php
/**
 * File: admin-logout.php
 * Session destroy pipeline for admin panel.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin_id'])) {
    unset($_SESSION['admin_id']);
}

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

$_SESSION = [];
session_destroy();

header("Location: admin-login");
exit;