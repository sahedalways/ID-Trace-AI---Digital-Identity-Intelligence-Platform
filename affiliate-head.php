<?php
/**
 * File: affiliate-head.php
 * Global head configuration panel for the affiliate portal interface nodes.
 * Ensure this file is only included, not accessed directly.
 */
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    die('Direct access not permitted.');
}

// Initialize active system session memory if not already started by the parent route script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Production Favicon Shortcut Link -->
<link rel="shortcut icon" href="https://i.ibb.co.com/FpJbXPS/idtrace1.webp" type="image/webp">
<link rel="icon" href="https://i.ibb.co.com/FpJbXPS/idtrace1.webp" type="image/webp">

<!-- Typography Framework Layer — Open Sans Universal Modern Typeface -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<!-- UI Rendering Engine (Tailwind CSS) -->
<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<!-- Font Awesome v6 Free Global Icons CDN Asset Layer -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    body {
        font-family: 'Open Sans', system-ui, -apple-system, sans-serif;
        /* Forced upscale light-cream pastel yellow background tint for extreme readability */
        background-color: #FAFAFA !important;
        color: #111827 !important;
    }
    html { scroll-behavior: smooth; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #128c7e; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #0e6f64; }
</style>
