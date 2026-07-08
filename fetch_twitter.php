<?php
/**
 * OSINT Universal Intelligence Console — High-Speed Core Discovery Engine
 * File: fetch_twitter.php
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
$currentEngine = 'twitter';

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
 * Human-readable short metric formatting helper (K, M, B)
 */
function formatKMB($number) {
    if (!is_numeric($number)) return '0';
    if ($number >= 1000000000) {
        return round($number / 1000000000, 1) . 'B';
    } elseif ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    }
    return $number;
}

// DETECT INPUT TYPE: Direct Twitter/X profile URL lookup vs Standard raw text search name
$isUrlSearch = filter_var($search_query, FILTER_VALIDATE_URL) && (strpos($search_query, 'twitter.com') !== false || strpos($search_query, 'x.com') !== false);

if ($isUrlSearch) {
    // ==========================================
    // ROUTE A: SOCIALFETCH PROFILE URL LOOKUP
    // ==========================================
    $urlPath = parse_url($search_query, PHP_URL_PATH);
    $segments = array_values(array_filter(explode('/', $urlPath)));
    $rawUsername = !empty($segments) ? end($segments) : '';
    $cleanHandleForEndpoint = ltrim($rawUsername, '@');

    $endpoint = "https://api.socialfetch.dev/v1/twitter/profiles/" . urlencode($cleanHandleForEndpoint);
    
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
        $metrics = $sfDataset['data']['metrics'] ?? [];
        $handleName = isset($profile['handle']) ? '@' . ltrim($profile['handle'], '@') : '';
        
        // Construct Unified Meta Line
        $metaDetails = [];
        if (!empty($handleName)) $metaDetails[] = $handleName;
        if (!empty($metrics['followers'])) $metaDetails[] = formatKMB($metrics['followers']) . " Followers";
        if (!empty($metrics['tweets'])) $metaDetails[] = formatKMB($metrics['tweets']) . " Tweets";
        if (!empty($profile['location'])) $metaDetails[] = trim($profile['location']);
        
        // Append unified bio string straight into the same metadata block
        $cleanBio = isset($profile['bio']) ? trim(preg_replace('/\s+/', ' ', $profile['bio'])) : '';
        if (!empty($cleanBio)) {
            $metaDetails[] = $cleanBio;
        }
        
        $rawAvatarUrl = $profile['avatarUrl'] ?? '';
        $isTwitterVerified = isset($profile['blueVerified']) ? (bool)$profile['blueVerified'] : false;

        $normalizedResults[] = [
            'profile_id'  => $profile['platformUserId'] ?? 'N/A',
            'name'        => $profile['displayName'] ?? 'Hidden Identity',
            'link'        => $profile['profileUrl'] ?? 'https://x.com/' . ltrim($handleName, '@'),
            'avatar'      => $rawAvatarUrl, // Handed off directly to the high-speed rendering array
            'raw_avatar'  => $rawAvatarUrl,
            'handle'      => implode(" • ", $metaDetails),
            'is_verified' => $isTwitterVerified
        ];
    }
} else {
    // ==========================================
    // ROUTE B: SOCIALFETCH TWITTER PEOPLE SEARCH
    // ==========================================
    $endpoint = "https://api.socialfetch.dev/v1/twitter/search?query=" . urlencode($search_query) . "&section=people";
    
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
    $usersList = $sfDataset['data']['people'] ?? ($sfDataset['data']['users'] ?? []);

    foreach ($usersList as $userItem) {
        if (is_array($userItem)) {
            $handleName = isset($userItem['handle']) ? '@' . ltrim($userItem['handle'], '@') : '';
            $userMetrics = $userItem['metrics'] ?? [];
            
            // Construct Unified Meta Line
            $metaDetails = [];
            if (!empty($handleName)) $metaDetails[] = $handleName;
            if (!empty($userMetrics['followers'])) $metaDetails[] = formatKMB($userMetrics['followers']) . " Followers";
            if (!empty($userMetrics['tweets'])) $metaDetails[] = formatKMB($userMetrics['tweets']) . " Tweets";
            if (!empty($userItem['location'])) $metaDetails[] = trim($userItem['location']);
            
            // Append unified bio string straight into the same metadata block
            $cleanBio = isset($userItem['bio']) ? trim(preg_replace('/\s+/', ' ', $userItem['bio'])) : '';
            if (!empty($cleanBio)) {
                $metaDetails[] = $cleanBio;
            }

            $rawAvatarUrl = $userItem['avatarUrl'] ?? '';
            $isTwitterVerified = isset($userItem['blueVerified']) ? (bool)$userItem['blueVerified'] : false;

            $normalizedResults[] = [
                'profile_id'  => $userItem['platformUserId'] ?? 'N/A',
                'name'        => $userItem['displayName'] ?? 'Hidden Identity',
                'link'        => $userItem['profileUrl'] ?? 'https://x.com/' . ltrim($handleName, '@'),
                'avatar'      => $rawAvatarUrl, // Direct streaming optimization endpoint parameter target
                'raw_avatar'  => $rawAvatarUrl,
                'handle'      => implode(" • ", $metaDetails),
                'is_verified' => $isTwitterVerified
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
        // Fallback silently if unexpected database runtime exceptions occur
    }
}

header('X-Cache-Status: MISS');
echo $outputPayload;
exit;
