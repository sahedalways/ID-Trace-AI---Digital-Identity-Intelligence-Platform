<?php
/**
 * OSINT Universal Intelligence Console — Background Task Worker
 * File: scrape_instagram.php
 * Context: Runs sequentially inside process.php context loop mapping or report.php inline scope
 */

// 1. CONTEXT PROTECTION CHECK
$targetUrl = isset($currentModuleUrl) ? trim($currentModuleUrl) : '';
$targetVid = isset($vid) ? trim($vid) : '';

if (empty($targetUrl) || empty($targetVid)) {
    error_log("Instagram Scraper Failure: Execution stalled due to missing runtime variable scopes.");
    return;
}

$db = $GLOBALS['pdo'] ?? ($pdo ?? null);
if (!($db instanceof PDO)) {
    error_log("Instagram Scraper Failure: Active database connection context broken.");
    return;
}

// =========================================================================
// HELPER LAYER: ABSOLUTE INSTAGRAM URL TO USERNAME EXTRACTOR
// =========================================================================
$instagramUsername = '';

$urlPath = parse_url($targetUrl, PHP_URL_PATH);
if (!empty($urlPath)) {
    $segments = array_values(array_filter(explode('/', $urlPath)));
    if (!empty($segments)) {
        $rawUsername = end($segments);
        $instagramUsername = ltrim(trim(strtok($rawUsername, '?')), '@');
    }
}

if (empty($instagramUsername) || (strpos($instagramUsername, '.') !== false && strlen($instagramUsername) < 2)) {
    error_log("Instagram Scraper Stoppage [VID: {$targetVid}]: Could not extract a valid username handle from: {$targetUrl}");
    return;
}

// 2. CONFIGURATION MATRIX BOUNDS
$socialFetchKey = SOCIALFETCH_API_KEY;

$profileResponse   = null;
$allPagesCollected = []; // Stores exact post responses chronologically

// ==========================================
// TASK 1: RUN PROFILE ANALYSIS ENDPOINT (SOCIALFETCH NATIVE DRIVER)
// ==========================================
$profileEndpoint = "https://api.socialfetch.dev/v1/instagram/profiles/" . urlencode($instagramUsername);

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
    $decodedProfile = json_decode($fetchedProfile, true);
    if (json_last_error() === JSON_ERROR_NONE && !empty($decodedProfile)) {
        
        // Deep-cleaning routine targeting nested structural bloat fields
        array_walk_recursive($decodedProfile, function(&$item, $key) {
            if (is_array($item) && ($key === 'recentPosts' || $key === 'relatedProfiles')) {
                $item = []; // Empty data fields to shrink database payloads safely
            }
        });

        // Drop keys at root container framework level if mapped explicitly
        if (isset($decodedProfile['recentPosts'])) unset($decodedProfile['recentPosts']);
        if (isset($decodedProfile['relatedProfiles'])) unset($decodedProfile['relatedProfiles']);
        if (isset($decodedProfile['data']['recentPosts'])) unset($decodedProfile['data']['recentPosts']);
        if (isset($decodedProfile['data']['relatedProfiles'])) unset($decodedProfile['data']['relatedProfiles']);
        
        $profileResponse = $decodedProfile;
    }
} else {
    error_log("Instagram Scraper Alert [VID: {$targetVid}]: Profile endpoint returned HTTP status code {$profileHttpCode}");
}

// ==========================================
// TASK 2: PAGINATED CURSOR LOOP FOR INSTAGRAM POSTS (UP TO 3 PAGES)
// ==========================================
$nextCursor = '';
$maxPages = 3;

for ($pageCount = 1; $pageCount <= $maxPages; $pageCount++) {
    
    $postsEndpoint = "https://api.socialfetch.dev/v1/instagram/profiles/" . urlencode($instagramUsername) . "/posts";
    if (!empty($nextCursor)) {
        $postsEndpoint .= "?cursor=" . urlencode($nextCursor);
    }

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
        $decodedPage = json_decode($fetchedPosts, true);
        if (json_last_error() === JSON_ERROR_NONE && !empty($decodedPage)) {
            
            $allPagesCollected[] = $decodedPage; // Stashes whole raw page frame response
            
            // Look for nextCursor anywhere in this page dataset to chain the iteration
            $foundCursor = null;
            array_walk_recursive($decodedPage, function($val, $key) use (&$foundCursor) {
                if ($key === 'nextCursor' && !empty($val)) {
                    $foundCursor = $val;
                }
            });

            // Fallback checking at standard wrapper root layer variables directly
            if (empty($foundCursor)) {
                $foundCursor = $decodedPage['nextCursor'] ?? ($decodedPage['data']['nextCursor'] ?? '');
            }

            $nextCursor = trim($foundCursor);
            
            // Break loop immediately if no cursor trail exists for more posts
            if (empty($nextCursor)) {
                break;
            }
        } else {
            break; 
        }
    } else {
        error_log("Instagram Scraper Alert [VID: {$targetVid}]: Paginated post loop gap at page layer {$pageCount}");
        break;
    }
}

// ==========================================
// CHANGED: EXCLUSIVE POST IMAGES PRE-HARVESTING HOOK
// Recursively updates 'displayUrl' properties ONLY within post feed contexts
// ==========================================
if (!empty($allPagesCollected) && function_exists('downloadScrapedMedia')) {
    array_walk_recursive($allPagesCollected, function(&$value, $key) {
        if ($key === 'displayUrl' && is_string($value) && strpos($value, 'http') === 0) {
            $localServerPath = downloadScrapedMedia($value);
            if (!empty($localServerPath)) {
                $value = $localServerPath;
            }
        }
    });
}

// ==========================================
// TASK 3: MUTUALLY ASSURED MATRIX COMBINATION WORKSPACE
// ==========================================
if ($profileResponse || !empty($allPagesCollected)) {
    try {
        $db->beginTransaction();

        // Fetch current database record allocations using row isolation locking
        $checkStmt = $db->prepare("SELECT `raw_profile`, `raw_post` FROM `reports` WHERE `vid` = ? FOR UPDATE");
        $checkStmt->execute([$targetVid]);
        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Decode existing arrays or initiate fallbacks
        $currentProfiles = !empty($existingRecord['raw_profile']) ? json_decode($existingRecord['raw_profile'], true) : [];
        $currentPosts    = !empty($existingRecord['raw_post']) ? json_decode($existingRecord['raw_post'], true) : [];

        if (!is_array($currentProfiles)) { $currentProfiles = []; }
        if (!is_array($currentPosts)) { $currentPosts = []; }

        // Core array merge sequence under platform namespace definitions
        if ($profileResponse) {
            $currentProfiles['instagram'] = $profileResponse;
        }
        if (!empty($allPagesCollected)) {
            $currentPosts['instagram'] = $allPagesCollected;
        }

        // Encodes objects preserving raw names, emoticons, and clean local unescaped URL strings
        $profilePayloadData = json_encode($currentProfiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $postsPayloadData   = json_encode($currentPosts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $updateStmt = $db->prepare("UPDATE `reports` SET `raw_profile` = ?, `raw_post` = ?, `updated_at` = NOW() WHERE `vid` = ?");
        $updateStmt->execute([$profilePayloadData, $postsPayloadData, $targetVid]);

        $db->commit();
    } catch (Exception $dbEx) {
        $db->rollBack();
        error_log("Instagram Scraper Matrix Synchronization Crash [VID: {$targetVid}]: " . $dbEx->getMessage());
    }
} else {
    error_log("Instagram Scraper Operational Stoppage [VID: {$targetVid}]: No fresh network payload data intercepted.");
}
