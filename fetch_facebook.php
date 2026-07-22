<?php
/**
 * OSINT Universal Intelligence Console — High-Speed Core Discovery Engine
 * File: fetch_facebook.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the endpoint strictly serves programmatic JSON responses
header('Content-Type: application/json; charset=UTF-8');

$action = $_GET['action'] ?? '';
$search_query = trim($_GET['q'] ?? '');

if ($action !== 'ajax_scan') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid gateway routing exception.']);
    exit;
}

if (empty($search_query)) {
    echo json_encode(['status' => 'success', 'results' => []]);
    exit;
}

// ==========================================
// GLOBALLY SCOPED CACHE PREPARATION ENGINE (3 Days)
// ==========================================
$currentEngine = 'facebook';

// Normalize input data string: force lowercase, trim space, strip trailing slashes cleanly
$normalizedQuery = rtrim(strtolower(trim($search_query)), '/');

$cacheKey = md5($currentEngine . '_' . $normalizedQuery);
$cacheDuration = 3 * 24 * 60 * 60; // 3 Days window measured in seconds
$currentTime = time();

// TARGET NATIVE PDO OBJECT MAPPED IN CONFIG.PHP
$databaseConnection = $GLOBALS['pdo'] ?? ($pdo ?? null);

if ($databaseConnection instanceof PDO) {
    try {
        $stmt = $databaseConnection->prepare("SELECT response_json, created_at FROM search_cache WHERE cache_key = :cache_key LIMIT 1");
        $stmt->execute([':cache_key' => $cacheKey]);
        $cachedRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cachedRecord) {
            $age = $currentTime - (int)$cachedRecord['created_at'];
            if ($age <= $cacheDuration) {
                // FRESH CACHE HIT: Serve immediately and exit cleanly
                header('X-Cache-Status: HIT');
                echo $cachedRecord['response_json'];
                exit;
            } else {
                // Clear old expired cache row
                $deleteStmt = $databaseConnection->prepare("DELETE FROM search_cache WHERE cache_key = :cache_key");
                $deleteStmt->execute([':cache_key' => $cacheKey]);
            }
        }
    } catch (Exception $e) {
        // Fallback silently into live scanning arrays if database errors throw
    }
}

$normalizedResults = [];
$socialFetchKey = SOCIALFETCH_API_KEY;

/**
 * Helper function to extract a semantic username handle from absolute URLs.
 * Drops hashing strings like "people", "profile.php", or "pfbid..." entirely.
 */
function extractCleanFacebookHandle($url) {
    if (empty($url) || $url === '#') return null;
    
    $path = parse_url($url, PHP_URL_PATH);
    if (empty($path)) return null;

    $segments = array_values(array_filter(explode('/', $path)));
    
    if (!empty($segments) && $segments[0] === 'people') {
        return null; 
    }

    $query = parse_url($url, PHP_URL_QUERY);
    if (!empty($query)) {
        parse_str($query, $params);
        if (isset($params['id']) && strpos($params['id'], 'pfbid') !== 0) {
            return '@' . $params['id'];
        }
    }

    if (!empty($segments) && $segments[0] !== 'profile.php') {
        $cleanHandle = end($segments);
        if (strpos($cleanHandle, 'pfbid') !== 0) {
            return '@' . $cleanHandle;
        }
    }

    return null;
}

// DETECT INPUT TYPE: Direct Facebook profile URL lookup vs Standard raw text search name
$isUrlSearch = filter_var($search_query, FILTER_VALIDATE_URL) && (strpos($search_query, 'facebook.com') !== false);

if ($isUrlSearch) {
    // ==========================================
    // ROUTE A: SOCIALFETCH PROFILE URL LOOKUP
    // ==========================================
    $endpoint = "https://api.socialfetch.dev/v1/facebook/profiles?url=" . urlencode($search_query);
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'x-api-key: ' . $socialFetchKey,
        'Accept: application/json'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $sfDataset = json_decode($response, true);
    $profile = $sfDataset['data']['profile'] ?? null;
    
    if ($profile && ($sfDataset['data']['lookupStatus'] ?? '') === 'found') {
        $metrics  = $sfDataset['data']['metrics'] ?? [];
        $business = $sfDataset['data']['business'] ?? [];
        
        $cleanHandle = extractCleanFacebookHandle($profile['profileUrl'] ?? '');
        
        $metaDetails = [];
        if (!empty($cleanHandle)) $metaDetails[] = $cleanHandle;
        if (!empty($profile['profileType'])) $metaDetails[] = ucfirst($profile['profileType']) . " Account";
        if (!empty($profile['gender'])) $metaDetails[] = ucfirst($profile['gender']);
        if (!empty($business['category'])) $metaDetails[] = $business['category'];
        if (!empty($metrics['followers'])) $metaDetails[] = number_format($metrics['followers']) . " Followers";
        if (!empty($metrics['likes'])) $metaDetails[] = number_format($metrics['likes']) . " Likes";
        if (!empty($business['intro'])) $metaDetails[] = $business['intro'];
        if (!empty($sfDataset['data']['contact']['website'])) $metaDetails[] = $sfDataset['data']['contact']['website'];
        
        $rawAvatarUrl = $profile['avatarUrl'] ?? '';
        $isSfVerified = isset($profile['isVerified']) ? (bool)$profile['isVerified'] : (isset($profile['is_verified']) ? (bool)$profile['is_verified'] : false);

        $normalizedResults[] = [
            'profile_id'  => $profile['platformUserId'] ?? 'N/A',
            'name'        => $profile['displayName'] ?? 'Hidden Identity',
            'link'        => $profile['profileUrl'] ?? '#',
            'avatar'      => $rawAvatarUrl, // Direct high-speed string references
            'raw_avatar'  => $rawAvatarUrl,
            'handle'      => implode(" • ", $metaDetails),
            'is_verified' => $isSfVerified
        ];
    }
} else {
    // ==========================================
    // ROUTE B: HIGH-SPEED APIFY NAME SEARCH ONLY
    // ==========================================
    $apifyToken = APIFY_API_KEY;
    $actorId    = 'patient_discovery~facebook-search-people';

    $ch = curl_init("https://api.apify.com/v2/acts/" . $actorId . "/run-sync-get-dataset-items?token=" . $apifyToken);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["query" => $search_query, "maxPages" => 1]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 180);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    
    $response = curl_exec($ch);
    curl_close($ch);

    $rawDataset = json_decode($response, true);
    $records = is_array($rawDataset) ? ($rawDataset['data']['items'] ?? $rawDataset) : [];

    foreach ($records as $item) {
        if (is_array($item) && (($item['type'] ?? '') === 'search_profile' || isset($item['profile_id']))) {
            $targetUrl = $item['url'] ?? '';
            if (empty($targetUrl) || $targetUrl === '#') continue;

            $metaDetails = [];
            $cleanHandle = extractCleanFacebookHandle($targetUrl);
            if (!empty($cleanHandle)) {
                $metaDetails[] = $cleanHandle;
            }

            $pUserId = $item['profile_id'] ?? '';
            $rawAvatarUrl = $item['profile_picture']['uri'] ?? '';
            $isProfileVerified = isset($item['is_verified']) ? (bool)$item['is_verified'] : false;

            $normalizedResults[] = [
                'profile_id'  => $pUserId ?: 'N/A',
                'name'        => $item['name'] ?? 'Hidden Identity',
                'link'        => $targetUrl,
                'avatar'      => $rawAvatarUrl, // Handed down cleanly to avoid connection stalls
                'raw_avatar'  => $rawAvatarUrl,
                'handle'      => !empty($metaDetails) ? implode(" • ", $metaDetails) : 'Facebook Profile',
                'is_verified' => $isProfileVerified
            ];
        }
    }
}

// ==========================================
// VISUAL DESIGN TWEAK: INLINE BULLET REMAPPING
// ==========================================
foreach ($normalizedResults as &$result) {
    if (!empty($result['handle'])) {
        $styledBullet = '<span class="text-[10px] font-light opacity-35 mx-1.5">•</span>';
        $result['handle'] = str_replace(' • ', $styledBullet, $result['handle']);
    }
}
unset($result); 

// Render dynamic payload mapping down to Javascript layer
$outputPayload = json_encode(['status' => 'success', 'results' => $normalizedResults]);

// ==========================================
// CACHE SAVING BLOCK: MYSQL 8+ COMPLIANT SAFE REFERENCE FIXED
// ==========================================
if (!empty($normalizedResults) && $databaseConnection instanceof PDO) {
    try {
        $saveStmt = $databaseConnection->prepare("INSERT INTO search_cache (cache_key, engine, search_query, response_json, created_at) 
            VALUES (:cache_key, :engine, :search_query, :response_json, :created_at)
            ON DUPLICATE KEY UPDATE response_json = :up_json, created_at = :up_time");
        
        $saveStmt->execute([
            ':cache_key'     => (string)$cacheKey,
            ':engine'        => (string)$currentEngine,
            ':search_query'  => (string)$search_query,
            ':response_json' => (string)$outputPayload,
            ':created_at'    => (int)$currentTime,
            ':up_json'       => (string)$outputPayload,
            ':up_time'       => (int)$currentTime
        ]);
    } catch (Exception $e) {
        // Fallback silently
    }
}

header('X-Cache-Status: MISS');
echo $outputPayload;
exit;