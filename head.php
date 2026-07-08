<?php
/**
 * File: head.php
 * Global system layout configuration & streamlined click ID affiliate tracking loop.
 * Ensure this file is only included, not accessed directly.
 */
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    die('Direct access not permitted.');
}

// 1. Initialize active system session memory if not already started by the routing script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =========================================================================
// PERSISTENT AFFILIATE TRACKING REDUNDANCY MATRIX
// =========================================================================
// If the short-term PHP Session has expired but the user has a valid affiliate 
// entry cookie from go.php, restore the active tracking state token instantly.
if (empty($_SESSION['active_cid']) && isset($_COOKIE['affiliate_click_id']) && !empty(trim($_COOKIE['affiliate_click_id']))) {
    $_SESSION['active_cid'] = trim($_COOKIE['affiliate_click_id']);
}
// =========================================================================
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Production Favicon Shortcut Link -->
<link rel="shortcut icon" href="https://i.ibb.co.com/FpJbXPS/idtrace1.webp" type="image/webp">

<link rel="icon" href="https://i.ibb.co.com/FpJbXPS/idtrace1.webp" type="image/webp">

<!-- Open Sans Typography Engine -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<!-- Tailwind v4 Browser Engine Runtime -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<!-- Font Awesome 6 Solid, Regular, and Brands Kit CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    body {
        font-family: 'Open Sans', sans-serif;
        background-color: #FAFAFA !important; /* Premium Light White Web Canvas */
        color: #111827 !important;
    }
</style>