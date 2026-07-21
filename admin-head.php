<?php
/**
 * File: admin-head.php
 * Global head configuration panel for the admin panel interface.
 */
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    die('Direct access not permitted.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="shortcut icon" href="https://i.ibb.co.com/FpJbXPS/idtrace1.webp" type="image/webp">
<link rel="icon" href="https://i.ibb.co.com/FpJbXPS/idtrace1.webp" type="image/webp">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300..800&display=swap" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: #f8fafc !important;
        color: #0f172a !important;
    }
    html { scroll-behavior: smooth; }
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #6366f1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #4f46e5; }
</style>
