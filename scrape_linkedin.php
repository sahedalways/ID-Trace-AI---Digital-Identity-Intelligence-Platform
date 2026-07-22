<?php
/**
 * OSINT Universal Intelligence Console — Background Task Worker
 * File: scrape_linkedin.php
 * Context: Runs sequentially inside process.php context loop mapping or report.php inline scope
 */

// 1. CONTEXT PROTECTION CHECK
$targetUrl = isset($currentModuleUrl) ? trim($currentModuleUrl) : '';
$targetVid = isset($vid) ? trim($vid) : '';

if (empty($targetUrl) || empty($targetVid)) {
    error_log("LinkedIn Scraper Failure: Execution stalled due to missing runtime variable scopes.");
    return;
}

$db = $GLOBALS['pdo'] ?? ($pdo ?? null);
if (!($db instanceof PDO)) {
    error_log("LinkedIn Scraper Failure: Active database connection context broken.");
    return;
}

// 2. CONFIGURATION MATRIX BOUNDS
$socialFetchKey = SOCIALFETCH_API_KEY;
$apifyToken     = APIFY_API_KEY;
$actorId        = 'dev_fusion~linkedin-profile-scraper';

$profileResponse = null;
$postsResponse   = null;

// ==========================================
// TASK 1: RUN PROFILE ANALYSIS ENDPOINT (APIFY ACTOR INTEGRATION CORE)
// ==========================================
$apifyPayload = [
    "profileUrls" => [$targetUrl]
];

$apifyEndpoint = "https://api.apify.com/v2/acts/" . $actorId . "/run-sync-get-dataset-items?token=" . $apifyToken;

$chProfile = curl_init($apifyEndpoint);
curl_setopt($chProfile, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chProfile, CURLOPT_POST, true);
curl_setopt($chProfile, CURLOPT_POSTFIELDS, json_encode($apifyPayload));
curl_setopt($chProfile, CURLOPT_TIMEOUT, 180);
curl_setopt($chProfile, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($chProfile, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$fetchedProfile = curl_exec($chProfile);
$profileHttpCode = curl_getinfo($chProfile, CURLINFO_HTTP_CODE);
curl_close($chProfile);

// FIXED: Accepts both 200 (OK) and 201 (Created) responses to handle synchronous run allocations smoothly
if (($profileHttpCode === 200 || $profileHttpCode === 201) && !empty($fetchedProfile)) {
    $testJson = json_decode($fetchedProfile, true);
    if (json_last_error() === JSON_ERROR_NONE && !empty($testJson)) {
        // Extract the actual profile dataset object entry safely out of Apify's collection index wrapper
        $profileResponse = isset($testJson[0]) ? $testJson[0] : $testJson;
    } else {
        error_log("LinkedIn Scraper Error [VID: {$targetVid}]: Apify profile json_decode failed. Raw response snippet: " . substr($fetchedProfile, 0, 100));
    }
} else {
    error_log("LinkedIn Scraper Alert [VID: {$targetVid}]: Apify profile endpoint returned HTTP status code {$profileHttpCode}");
}

// ==========================================
// TASK 2: RUN POSTS ANALYSIS ENDPOINT (SOCIALFETCH UPGRADED AUTHORED ENGINE)
// ==========================================
$postsEndpoint = "https://api.socialfetch.dev/v1/linkedin/profiles/posts?url=" . urlencode($targetUrl) . "&onlyAuthoredPosts=true&limit=10";

$chPosts = curl_init($postsEndpoint);
curl_setopt($chPosts, CURLOPT_RETURNTRANSFER, true);
curl_setopt($chPosts, CURLOPT_TIMEOUT, 35);
curl_setopt($chPosts, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($chPosts, CURLOPT_HTTPHEADER, [
    'x-api-key: ' . $socialFetchKey,
    'Accept: application/json'
]);

$fetchedPosts = curl_exec($chPosts);
$postsHttpCode = curl_getinfo($chPosts, CURLINFO_HTTP_CODE);
curl_close($chPosts);

if ($postsHttpCode === 200 && !empty($fetchedPosts)) {
    $testJson = json_decode($fetchedPosts, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        $postsResponse = $testJson; // Captures true raw response object array
    }
} else {
    error_log("LinkedIn Scraper Alert [VID: {$targetVid}]: Posts endpoint returned HTTP status code {$postsHttpCode}");
}

// ==========================================
// TASK 3: MUTUALLY ASSURED MATRIX COMBINATION WORKSPACE (PREVENTS OVERWRITING)
// ==========================================
if (!empty($profileResponse) || !empty($postsResponse)) {
    try {
        $db->beginTransaction();

        // Fetch current database allocations using a row safety-lock
        $checkStmt = $db->prepare("SELECT `raw_profile`, `raw_post` FROM `reports` WHERE `vid` = ? FOR UPDATE");
        $checkStmt->execute([$targetVid]);
        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

        $currentProfiles = !empty($existingRecord['raw_profile']) ? json_decode($existingRecord['raw_profile'], true) : [];
        $currentPosts    = !empty($existingRecord['raw_post']) ? json_decode($existingRecord['raw_post'], true) : [];

        if (!is_array($currentProfiles)) { $currentProfiles = []; }
        if (!is_array($currentPosts)) { $currentPosts = []; }

        // Key-isolate LinkedIn metrics inside the multi-platform columns cleanly
        if (!empty($profileResponse)) {
            $currentProfiles['linkedin'] = $profileResponse;
        }
        if (!empty($postsResponse)) {
            $currentPosts['linkedin'] = $postsResponse;
        }

        // Encodes array structures while keeping slashes and global unicode characters entirely readable
        $profilePayloadData = json_encode($currentProfiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $postsPayloadData   = json_encode($currentPosts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // Atomically commit datasets cleanly back into the reports row
        $updateStmt = $db->prepare("UPDATE `reports` SET `raw_profile` = ?, `raw_post` = ?, `updated_at` = NOW() WHERE `vid` = ?");
        $updateStmt->execute([$profilePayloadData, $postsPayloadData, $targetVid]);

        $db->commit();
    } catch (Exception $dbEx) {
        $db->rollBack();
        error_log("LinkedIn Scraper Matrix Synchronization Crash [VID: {$targetVid}]: " . $dbEx->getMessage());
    }
} else {
    error_log("LinkedIn Scraper Operational Stoppage [VID: {$targetVid}]: Both target profile and activity tracking endpoints returned empty sets.");
}