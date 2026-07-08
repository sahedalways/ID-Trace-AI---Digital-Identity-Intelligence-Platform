<?php
/**
 * OSINT Universal Intelligence Console — Background Task Worker
 * File: scrape_twitter.php
 * Context: Runs sequentially inside process.php context loop mapping or report.php inline scope
 */

// 1. CONTEXT PROTECTION CHECK
$targetUrl = isset($currentModuleUrl) ? trim($currentModuleUrl) : '';
$targetVid = isset($vid) ? trim($vid) : '';

if (empty($targetUrl) || empty($targetVid)) {
    error_log("Twitter Scraper Failure: Execution stalled due to missing runtime variable scopes.");
    return;
}

$db = $GLOBALS['pdo'] ?? ($pdo ?? null);
if (!($db instanceof PDO)) {
    error_log("Twitter Scraper Failure: Active database connection context broken.");
    return;
}

// =========================================================================
// HELPER LAYER: ABSOLUTE TWITTER/X URL TO USERNAME EXTRACTOR
// =========================================================================
$twitterUsername = '';

$urlPath = parse_url($targetUrl, PHP_URL_PATH);
if (!empty($urlPath)) {
    $segments = array_values(array_filter(explode('/', $urlPath)));
    if (!empty($segments)) {
        $rawUsername = end($segments);
        $twitterUsername = ltrim(trim(strtok($rawUsername, '?')), '@');
    }
}

if (empty($twitterUsername) || $twitterUsername === 'home' || $twitterUsername === 'search') {
    error_log("Twitter Scraper Stoppage [VID: {$targetVid}]: Could not parse a valid user handle from: {$targetUrl}");
    return;
}

// 2. CONFIGURATION MATRIX BOUNDS
$apiKey = SOCIALFETCH_API_KEY;

$profileResponse = null;
$tweetsResponse  = null;

// ==========================================
// TASK 1: RUN PROFILE ANALYSIS ENDPOINT (TRUE RAW RECORDING)
// ==========================================
$profileEndpoint = "https://api.socialfetch.dev/v1/twitter/profiles/" . urlencode($twitterUsername);

$chProfile = curl_init($profileEndpoint);
curl_setopt($chProfile, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chProfile, CURLOPT_TIMEOUT, 30);
curl_setopt($chProfile, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($chProfile, CURLOPT_HTTPHEADER, [
    'x-api-key: ' . $apiKey,
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
} else {
    error_log("Twitter Scraper Alert [VID: {$targetVid}]: Profile endpoint returned HTTP status code {$profileHttpCode}");
}

// ==========================================
// TASK 2: RUN TWEETS ANALYSIS ENDPOINT (TRUE RAW RECORDING)
// ==========================================
$tweetsEndpoint = "https://api.socialfetch.dev/v1/twitter/profiles/" . urlencode($twitterUsername) . "/tweets";

$chTweets = curl_init($tweetsEndpoint);
curl_setopt($chTweets, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chTweets, CURLOPT_TIMEOUT, 35);
curl_setopt($chTweets, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($chTweets, CURLOPT_HTTPHEADER, [
    'x-api-key: ' . $apiKey,
    'Accept: application/json'
]);

$fetchedTweets = curl_exec($chTweets);
$tweetsHttpCode = curl_getinfo($chTweets, CURLINFO_HTTP_CODE);
curl_close($chTweets);

if ($tweetsHttpCode === 200 && !empty($fetchedTweets)) {
    $testJson = json_decode($fetchedTweets, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $tweetsResponse = $testJson;
    }
} else {
    error_log("Twitter Scraper Alert [VID: {$targetVid}]: Tweets endpoint returned HTTP status code {$tweetsHttpCode}");
}

// ==========================================
// CHANGED: TARGETED IN-MEMORY MEDIA PRE-HARVESTING COUPLING
// Recursively updates 'thumbnailUrl' elements across BOTH profile and tweet structures
// ==========================================
if (function_exists('downloadScrapedMedia')) {
    // 1. Process profile parameters if present
    if (!empty($profileResponse)) {
        array_walk_recursive($profileResponse, function(&$value, $key) {
            if ($key === 'thumbnailUrl' && is_string($value) && strpos($value, 'http') === 0) {
                $localServerPath = downloadScrapedMedia($value);
                if (!empty($localServerPath)) {
                    $value = $localServerPath;
                }
            }
        });
    }

    // 2. Process feed post structures if present
    if (!empty($tweetsResponse)) {
        array_walk_recursive($tweetsResponse, function(&$value, $key) {
            if ($key === 'thumbnailUrl' && is_string($value) && strpos($value, 'http') === 0) {
                $localServerPath = downloadScrapedMedia($value);
                if (!empty($localServerPath)) {
                    $value = $localServerPath;
                }
            }
        });
    }
}

// ==========================================
// TASK 3: MUTUALLY ASSURED MATRIX COMBINATION WORKSPACE (PREVENTS OVERWRITING)
// ==========================================
if ($profileResponse || $tweetsResponse) {
    try {
        $db->beginTransaction();

        // Fetch current records with a transactional row-lock
        $checkStmt = $db->prepare("SELECT `raw_profile`, `raw_post` FROM `reports` WHERE `vid` = ? FOR UPDATE");
        $checkStmt->execute([$targetVid]);
        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

        $currentProfiles = !empty($existingRecord['raw_profile']) ? json_decode($existingRecord['raw_profile'], true) : [];
        $currentPosts    = !empty($existingRecord['raw_post']) ? json_decode($existingRecord['raw_post'], true) : [];

        if (!is_array($currentProfiles)) { $currentProfiles = []; }
        if (!is_array($currentPosts)) { $currentPosts = []; }

        // Key-isolate configuration entries safely under 'twitter' allocations maps
        if ($profileResponse) {
            $currentProfiles['twitter'] = $profileResponse;
        }
        if ($tweetsResponse) {
            $currentPosts['twitter'] = $tweetsResponse;
        }

        // Formats output data structures tracking clean text string sets and clean web URL links
        $profilePayloadData = json_encode($currentProfiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $postsPayloadData   = json_encode($currentPosts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Update aggregated matrices safely back into reports table space cleanly
        $updateStmt = $db->prepare("UPDATE `reports` SET `raw_profile` = ?, `raw_post` = ?, `updated_at` = NOW() WHERE `vid` = ?");
        $updateStmt->execute([$profilePayloadData, $postsPayloadData, $targetVid]);

        $db->commit();
    } catch (Exception $dbEx) {
        $db->rollBack();
        error_log("Twitter Scraper Database Exception [VID: {$targetVid}]: " . $dbEx->getMessage());
    }
} else {
    error_log("Twitter Scraper Operational Stoppage [VID: {$targetVid}]: Both endpoint lookups returned structural anomalies or empty sets.");
}
