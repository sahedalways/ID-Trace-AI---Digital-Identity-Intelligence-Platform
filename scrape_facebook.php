<?php
/**
 * OSINT Universal Intelligence Console — Background Task Worker
 * File: scrape_facebook.php
 * Context: Runs sequentially inside process.php context loop mapping or report.php inline scope
 */

// 1. CONTEXT PROTECTION CHECK
$targetUrl = isset($currentModuleUrl) ? trim($currentModuleUrl) : '';
$targetVid = isset($vid) ? trim($vid) : '';

if (empty($targetUrl) || empty($targetVid)) {
    error_log("Facebook Scraper Failure: Execution stalled due to missing runtime variable scopes.");
    return;
}

$db = $GLOBALS['pdo'] ?? ($pdo ?? null);
if (!($db instanceof PDO)) {
    error_log("Facebook Scraper Failure: Active database connection context broken.");
    return;
}

// 2. CONFIGURATION MATRIX BOUNDS
$apiKey = SOCIALFETCH_API_KEY;

$profileResponse = null;
$allPagesCollected = []; // Stores exact responses chronologically

// ==========================================
// TASK 1: RUN PROFILE ANALYSIS ENDPOINT (TRUE RAW RECORDING)
// ==========================================
$profileEndpoint = "https://api.socialfetch.dev/v1/facebook/profiles?url=" . urlencode($targetUrl);

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
}

// ==========================================
// TASK 2: PAGINATED CURSOR LOOP (UP TO 3 PAGES - SAVING EACH RAW CHUNK)
// ==========================================
$nextCursor = '';
$maxPages = 3;

for ($pageCount = 1; $pageCount <= $maxPages; $pageCount++) {
    
    $postsEndpoint = "https://api.socialfetch.dev/v1/facebook/profiles/posts?url=" . urlencode($targetUrl);
    if (!empty($nextCursor)) {
        $postsEndpoint .= "&cursor=" . urlencode($nextCursor);
    }

    $chPosts = curl_init($postsEndpoint);
    curl_setopt($chPosts, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chPosts, CURLOPT_TIMEOUT, 35);
    curl_setopt($chPosts, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($chPosts, CURLOPT_HTTPHEADER, [
        'x-api-key: ' . $apiKey,
        'Accept: application/json'
    ]);

    $fetchedPosts = curl_exec($chPosts);
    $postsHttpCode = curl_getinfo($chPosts, CURLINFO_HTTP_CODE);
    curl_close($chPosts);

    if ($postsHttpCode === 200 && !empty($fetchedPosts)) {
        $decodedPage = json_decode($fetchedPosts, true);
        if (json_last_error() === JSON_ERROR_NONE && !empty($decodedPage)) {
            
            $allPagesCollected[] = $decodedPage; // Stashes whole raw page frame response
            
            // Extract look-ahead cursor metrics
            $nextCursor = $decodedPage['data']['page']['nextCursor'] ?? '';
            if (empty($nextCursor)) {
                break;
            }
        } else {
            break; 
        }
    } else {
        error_log("Facebook Scraper Alert [VID: {$targetVid}]: Paginated endpoint connection gap at page layer {$pageCount}");
        break;
    }
}

// ==========================================
// CHANGED: TARGETED IN-MEMORY MEDIA PRE-HARVESTING COUPLING
// Recursively updates 'imageUrl' elements across BOTH profile and post structures
// ==========================================
if (function_exists('downloadScrapedMedia')) {
    // 1. Process profile payload parameters if present
    if (!empty($profileResponse)) {
        array_walk_recursive($profileResponse, function(&$value, $key) {
            if ($key === 'imageUrl' && is_string($value) && strpos($value, 'http') === 0) {
                $localServerPath = downloadScrapedMedia($value);
                if (!empty($localServerPath)) {
                    $value = $localServerPath;
                }
            }
        });
    }

    // 2. Process feed post updates timeline chunks if present
    if (!empty($allPagesCollected)) {
        array_walk_recursive($allPagesCollected, function(&$value, $key) {
            if ($key === 'imageUrl' && is_string($value) && strpos($value, 'http') === 0) {
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
if ($profileResponse || !empty($allPagesCollected)) {
    try {
        $db->beginTransaction();

        // 1. Fetch current database record allocations to prevent wiping other scrapers
        $checkStmt = $db->prepare("SELECT `raw_profile`, `raw_post` FROM `reports` WHERE `vid` = ? FOR UPDATE");
        $checkStmt->execute([$targetVid]);
        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

        $currentProfiles = !empty($existingRecord['raw_profile']) ? json_decode($existingRecord['raw_profile'], true) : [];
        $currentPosts    = !empty($existingRecord['raw_post']) ? json_decode($existingRecord['raw_post'], true) : [];

        if (!is_array($currentProfiles)) { $currentProfiles = []; }
        if (!is_array($currentPosts)) { $currentPosts = []; }

        // 2. Key-isolate Facebook allocations cleanly inside multidimensional structures
        if ($profileResponse) {
            $currentProfiles['facebook'] = $profileResponse;
        }
        if (!empty($allPagesCollected)) {
            $currentPosts['facebook'] = $allPagesCollected;
        }

        // Encodes objects preserving unescaped slashes and unicode formats cleanly
        $profilePayloadData = json_encode($currentProfiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $postsPayloadData   = json_encode($currentPosts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // 3. Atomically write back matrix values safely
        $updateStmt = $db->prepare("UPDATE `reports` SET `raw_profile` = ?, `raw_post` = ?, `updated_at` = NOW() WHERE `vid` = ?");
        $updateStmt->execute([$profilePayloadData, $postsPayloadData, $targetVid]);

        $db->commit();
    } catch (Exception $dbEx) {
        $db->rollBack();
        error_log("Facebook Scraper Matrix Synchronization Crash [VID: {$targetVid}]: " . $dbEx->getMessage());
    }
}