<?php
/**
 * OSINT Universal Intelligence Console — Background Task Worker
 * File: scrape_tiktok.php
 */

// 1. CONTEXT PROTECTION CHECK
$targetUrl = isset($currentModuleUrl) ? trim($currentModuleUrl) : '';
$targetVid = isset($vid) ? trim($vid) : '';

if (empty($targetUrl) || empty($targetVid)) {
    error_log("TikTok Scraper Failure: Execution stalled due to missing runtime variable scopes.");
    return;
}

$db = $GLOBALS['pdo'] ?? ($pdo ?? null);
if (!($db instanceof PDO)) {
    error_log("TikTok Scraper Failure: Active database connection context broken.");
    return;
}

// =========================================================================
// HELPER LAYER: ABSOLUTE TIKTOK URL TO USERNAME EXTRACTOR
// =========================================================================
$tiktokUsername = '';

$urlPath = parse_url($targetUrl, PHP_URL_PATH);
if (!empty($urlPath)) {
    $segments = array_values(array_filter(explode('/', $urlPath)));
    if (!empty($segments)) {
        $rawUsername = end($segments);
        $tiktokUsername = ltrim(trim(strtok($rawUsername, '?')), '@');
    }
}

if (empty($tiktokUsername)) {
    error_log("TikTok Scraper Stoppage [VID: {$targetVid}]: Could not parse handle from: {$targetUrl}");
    return;
}

// 2. CONFIGURATION MATRIX BOUNDS
$socialFetchKey = SOCIALFETCH_API_KEY;
$apifyToken     = APIFY_API_KEY;
$actorId        = 'clockworks~tiktok-scraper';

$profileResponse   = null;
$videosResponse    = null;
$followingResponse = null;

// ==========================================
// TASK 1: RUN PROFILE ANALYSIS ENDPOINT (SOCIALFETCH NATIVE DRIVER)
// ==========================================
$profileEndpoint = "https://api.socialfetch.dev/v1/tiktok/profiles/" . urlencode($tiktokUsername);

$chProfile = curl_init($profileEndpoint);
curl_setopt($chProfile, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chProfile, CURLOPT_TIMEOUT, 30);
curl_setopt($chProfile, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($chProfile, CURLOPT_HTTPHEADER, [
    'x-api-key: ' . $socialFetchKey,
    'Accept: application/json'
]);

$fetchedProfile = curl_exec($chProfile);
$profileHttpCode = curl_getinfo($chProfile, CURLINFO_HTTP_CODE);
curl_close($chProfile);

if ($profileHttpCode === 200 && !empty($fetchedProfile)) {
    $testJson = json_decode($fetchedProfile, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $profileResponse = $testJson;
    }
}

// ==========================================
// TASK 2: RUN VIDEOS FEED ENDPOINT (APIFY ACTOR INTEGRATION CORE)
// ==========================================
$apifyPayload = [
    "commentsPerPost"               => 0,
    "excludePinnedPosts"            => false,
    "maxFollowersPerProfile"        => 0,
    "maxFollowingPerProfile"        => 0,
    "maxRepliesPerComment"          => 0,
    "profileScrapeSections"         => ["videos"],
    "profileSorting"                => "latest",
    "profiles"                      => [$tiktokUsername],
    "proxyCountryCode"              => "None",
    "resultsPerPage"                => 20,
    "scrapeAdditionalAuthorMeta"    => false,
    "scrapeRelatedSearchWords"      => false,
    "scrapeRelatedVideos"           => false,
    "shouldDownloadAvatars"         => false,
    "shouldDownloadCovers"          => false,
    "shouldDownloadMusicCovers"     => false,
    "shouldDownloadSlideshowImages" => false,
    "shouldDownloadVideos"          => false,
    "topLevelCommentsPerPost"       => 0
];

$apifyEndpoint = "https://api.apify.com/v2/acts/" . $actorId . "/run-sync-get-dataset-items?token=" . $apifyToken;

$chVideos = curl_init($apifyEndpoint);
curl_setopt($chVideos, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chVideos, CURLOPT_POST, true);
curl_setopt($chVideos, CURLOPT_POSTFIELDS, json_encode($apifyPayload));
curl_setopt($chVideos, CURLOPT_TIMEOUT, 180);
curl_setopt($chVideos, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($chVideos, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$fetchedVideos = curl_exec($chVideos);
$videosHttpCode = curl_getinfo($chVideos, CURLINFO_HTTP_CODE);
curl_close($chVideos);

if (($videosHttpCode === 200 || $videosHttpCode === 201) && !empty($fetchedVideos)) {
    $testJson = json_decode($fetchedVideos, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $videosResponse = $testJson;
    }
}

// ==========================================
// TASK 3: RUN FOLLOWING NETWORK ENDPOINT (SOCIALFETCH NATIVE DRIVER)
// ==========================================
$followingEndpoint = "https://api.socialfetch.dev/v1/tiktok/profiles/" . urlencode($tiktokUsername) . "/following";

$chFollowing = curl_init($followingEndpoint);
curl_setopt($chFollowing, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chFollowing, CURLOPT_TIMEOUT, 35);
curl_setopt($chFollowing, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($chFollowing, CURLOPT_HTTPHEADER, [
    'x-api-key: ' . $socialFetchKey,
    'Accept: application/json'
]);

$fetchedFollowing = curl_exec($chFollowing);
$followingHttpCode = curl_getinfo($chFollowing, CURLINFO_HTTP_CODE);
curl_close($chFollowing);

if ($followingHttpCode === 200 && !empty($fetchedFollowing)) {
    $testJson = json_decode($fetchedFollowing, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $followingResponse = $testJson;
    }
}

// ==========================================
// CHANGED: DATA BLOAT CLEANER
// Recursively removes heavy CDN paths before persistence
// ==========================================
function trimPayloadKeys(&$array, $keysToRemove) {
    foreach ($array as $key => &$value) {
        if (in_array($key, $keysToRemove)) {
            unset($array[$key]);
        } elseif (is_array($value)) {
            trimPayloadKeys($value, $keysToRemove);
        }
    }
}

// UPDATED: Added 'originalCoverUrl' to the heavy parameters purge matrix
$heavyKeys = [
    'avatar', 
    'playUrl', 
    'originalAvatarUrl', 
    'coverMediumUrl', 
    'originalCoverMediumUrl', 
    'originalCoverUrl', 
    'downloadLink', 
    'tiktokLink'
];

if (!empty($profileResponse)) trimPayloadKeys($profileResponse, $heavyKeys);
if (!empty($videosResponse)) trimPayloadKeys($videosResponse, $heavyKeys);
if (!empty($followingResponse)) trimPayloadKeys($followingResponse, $heavyKeys);

// ==========================================
// CHANGED: TARGETED IN-MEMORY MEDIA PRE-HARVESTING COUPLING
// Recursively extracts and updates 'coverUrl' elements across the Apify output feed array
// ==========================================
if (!empty($videosResponse) && function_exists('downloadScrapedMedia')) {
    array_walk_recursive($videosResponse, function(&$value, $key) {
        if ($key === 'coverUrl' && is_string($value) && strpos($value, 'http') === 0) {
            $localServerPath = downloadScrapedMedia($value);
            if (!empty($localServerPath)) {
                $value = $localServerPath;
            }
        }
    });
}

// ==========================================
// TASK 4: MUTUALLY ASSURED MATRIX COMBINATION WORKSPACE
// ==========================================
if ($profileResponse || $videosResponse || $followingResponse) {
    try {
        $db->beginTransaction();

        $checkStmt = $db->prepare("SELECT `raw_profile`, `raw_post`, `raw_following` FROM `reports` WHERE `vid` = ? FOR UPDATE");
        $checkStmt->execute([$targetVid]);
        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

        $currentProfiles  = !empty($existingRecord['raw_profile']) ? json_decode($existingRecord['raw_profile'], true) : [];
        $currentPosts     = !empty($existingRecord['raw_post']) ? json_decode($existingRecord['raw_post'], true) : [];
        $currentFollowing = !empty($existingRecord['raw_following']) ? json_decode($existingRecord['raw_following'], true) : [];

        if (!is_array($currentProfiles)) { $currentProfiles = []; }
        if (!is_array($currentPosts)) { $currentPosts = []; }
        if (!is_array($currentFollowing)) { $currentFollowing = []; }

        if ($profileResponse) $currentProfiles['tiktok'] = $profileResponse;
        if ($videosResponse) $currentPosts['tiktok'] = $videosResponse;
        if ($followingResponse) $currentFollowing['tiktok'] = $followingResponse;

        $updateStmt = $db->prepare("UPDATE `reports` 
            SET `raw_profile`   = ?, 
                `raw_post`      = ?, 
                `raw_following` = ?, 
                `updated_at`    = NOW() 
            WHERE `vid` = ?");
        
        $updateStmt->execute([
            json_encode($currentProfiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            json_encode($currentPosts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            json_encode($currentFollowing, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $targetVid
        ]);

        $db->commit();
    } catch (Exception $dbEx) {
        $db->rollBack();
        error_log("TikTok Scraper Matrix Synchronization Crash [VID: {$targetVid}]: " . $dbEx->getMessage());
    }
}