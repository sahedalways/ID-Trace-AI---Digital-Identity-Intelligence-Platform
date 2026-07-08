<?php
/**
 * OSINT Universal Intelligence Console — High-Speed Core Discovery Engine
 * File: fetch_linkedin.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Extend execution parameters for heavy headless engine wait cycles
ini_set('max_execution_time', 180);
set_time_limit(180);

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
$currentEngine = 'linkedin';

// Normalize input: force lowercase, trim space, strip trailing slashes cleanly
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
$apifyToken     = APIFY_API_KEY;
$actorId        = 'harvestapi~linkedin-profile-search-by-name';

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

// DETECT INPUT TYPE: Direct LinkedIn profile URL lookup vs Standard raw text search name
$isUrlSearch = filter_var($search_query, FILTER_VALIDATE_URL) && (strpos($search_query, 'linkedin.com') !== false);

if ($isUrlSearch) {
    // ==========================================
    // ROUTE A: SOCIALFETCH PROFILE URL LOOKUP
    // ==========================================
    $endpoint = "https://api.socialfetch.dev/v1/linkedin/profiles?url=" . urlencode($search_query);
    
    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'x-api-key: ' . $socialFetchKey,
        'Accept: application/json'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    $sfDataset = json_decode($response, true);
    $resultNode = $sfDataset['data']['results'][0] ?? null;
    
    if ($resultNode && ($resultNode['lookupStatus'] ?? '') === 'found' && !empty($resultNode['profile'])) {
        $profile = $resultNode['profile'];
        $metrics = $resultNode['metrics'] ?? [];
        
        $usernameHandle = !empty($profile['handle']) ? $profile['handle'] : '';
        $handleName = !empty($usernameHandle) ? '@' . ltrim($usernameHandle, '@') : 'linkedin.profile';

        // Construct Unified Meta line
        $metaDetails = [];
        if (!empty($handleName)) $metaDetails[] = $handleName;
        if (!empty($profile['headline'])) $metaDetails[] = trim($profile['headline']);
        if (!empty($metrics['followers'])) $metaDetails[] = formatKMB($metrics['followers']) . " Followers";
        if (!empty($profile['city'])) $metaDetails[] = trim($profile['city']);
        
        // Append current workplace parameters natively if visible
        if (!empty($profile['currentCompany']['name'])) {
            $metaDetails[] = trim($profile['currentCompany']['name']);
        }

        // Append unified bio text cleanly into the same metadata line
        $cleanBio = isset($profile['bio']) ? trim(preg_replace('/\s+/', ' ', $profile['bio'])) : '';
        if (!empty($cleanBio)) {
            $metaDetails[] = $cleanBio;
        }

        $rawAvatarUrl = $profile['avatarUrl'] ?? '';

        $normalizedResults[] = [
            'profile_id'  => $profile['numericId'] ?? 'N/A',
            'name'        => $profile['displayName'] ?? 'Hidden Identity',
            'link'        => $profile['profileUrl'] ?? $search_query,
            'avatar'      => $rawAvatarUrl, // Direct reference mapping to avoid script stalls
            'raw_avatar'  => $rawAvatarUrl,
            'handle'      => implode(" • ", $metaDetails),
            'is_verified' => false
        ];
    }
} else {
    // ==========================================
    // ROUTE B: APIFY NAME SEARCH SCOPE
    // ==========================================
    $nameParts = explode(' ', $search_query);
    $firstName = $nameParts[0];
    $lastName = (count($nameParts) > 1) ? implode(' ', array_slice($nameParts, 1)) : '';

    $payload = [
        "firstName"          => $firstName,
        "lastName"           => $lastName,
        "maxItems"           => 8,
        "maxPages"           => 1,
        "profileScraperMode" => "Short",
        "strictSearch"       => false
    ];

    $endpoint = "https://api.apify.com/v2/acts/" . $actorId . "/run-sync-get-dataset-items?token=" . $apifyToken;

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_TIMEOUT, 180);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
    
    $response = curl_exec($ch);
    curl_close($ch);

    $rawDataset = json_decode($response, true);
    $records = is_array($rawDataset) ? ($rawDataset['data']['items'] ?? $rawDataset) : [];

    foreach ($records as $item) {
        if (is_array($item)) {
            $usernameHandle = !empty($item['publicIdentifier']) ? $item['publicIdentifier'] : '';
            if (empty($usernameHandle) && !empty($item['linkedinUrl']) && $item['linkedinUrl'] !== '#') {
                $urlSegments = array_values(array_filter(explode('/', parse_url($item['linkedinUrl'], PHP_URL_PATH))));
                $usernameHandle = !empty($urlSegments) ? end($urlSegments) : '';
            }
            $handleName = !empty($usernameHandle) ? '@' . ltrim($usernameHandle, '@') : 'linkedin.profile';

            // Construct Unified Meta line
            $metaDetails = [];
            if (!empty($handleName)) $metaDetails[] = $handleName;
            if (!empty($item['position'])) $metaDetails[] = trim($item['position']);
            if (!empty($item['location']['linkedinText'])) $metaDetails[] = trim($item['location']['linkedinText']);

            $rawAvatarUrl = $item['photo'] ?? '';

            $normalizedResults[] = [
                'profile_id'  => $item['id'] ?? 'N/A',
                'name'        => $item['name'] ?? 'Hidden Identity',
                'link'        => $item['linkedinUrl'] ?? '#',
                'avatar'      => $rawAvatarUrl, // Asynchronous presentation layer delivery
                'raw_avatar'  => $rawAvatarUrl,
                'handle'      => !empty($metaDetails) ? implode(" • ", $metaDetails) : 'LinkedIn Professional',
                'is_verified' => false
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
