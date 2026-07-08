<?php
/**
 * OSINT Console — Secure Image Proxy Gateway
 * File: proxy.php
 */
require_once 'config.php';

$rawParam = isset($_GET['url']) ? trim($_GET['url']) : '';

if (empty($rawParam)) {
    header("HTTP/1.1 400 Bad Request");
    exit("Invalid URL parameter context.");
}

// =========================================================================
// URL-SAFE BASE64 DECODING NORMALIZATION LAYER
// =========================================================================
$standardBase64 = str_replace(['-', '_'], ['+', '/'], $rawParam);
$remainder = strlen($standardBase64) % 4;
if ($remainder) {
    $standardBase64 .= str_repeat('=', 4 - $remainder);
}

$url = base64_decode($standardBase64);

if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
    header("HTTP/1.1 400 Bad Request");
    exit("Invalid URL parameter context.");
}

// Parse image file extension cleanly to send correct content headers
$extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
$mime_type = 'image/jpeg'; // Default fallback

if ($extension === 'png')  $mime_type = 'image/png';
if ($extension === 'webp') $mime_type = 'image/webp';
if ($extension === 'gif')  $mime_type = 'image/gif';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// CRITICAL: Mimic an authentic desktop browser request to Instagram CDNs
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Accept: image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.9',
    'Referer: https://www.instagram.com/' // Bypasses Instagram's hotlink blockade protection
]);

$raw_image_data = curl_exec($ch);
$http_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_status_code === 200 && !empty($raw_image_data)) {
    header("Content-Type: " . $mime_type);
    header("Cache-Control: public, max-age=86400"); // Cache image locally for 24 hours to optimize speeds
    echo $raw_image_data;
    exit;
} else {
    // If the image fails to pull down, stream a local anonymous profile silhouette icon 
    header("Content-Type: image/png");
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
    exit;
}
