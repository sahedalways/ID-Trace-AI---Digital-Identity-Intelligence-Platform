<?php
/**
 * OSINT Universal Intelligence Console — High-Speed Core Discovery Engine
 * File: fetch_truecaller.php
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
// STRICT PURE NUMBER STRIPPING BEFORE DB/AI
// ==========================================
$pureStrippedNumber = preg_replace('/[^0-9]/', '', $search_query);

if (empty($pureStrippedNumber)) {
    echo json_encode(['status' => 'success', 'results' => []]);
    exit;
}

// ==========================================
// GLOBALLY SCOPED CACHE PREPARATION ENGINE (3 Days)
// ==========================================
$currentEngine = 'truecaller';
$cacheKey = md5($currentEngine . '_' . $pureStrippedNumber);
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
        // Fallback silently into live scanning if database errors throw
    }
}

/**
 * Human-readable short metric formatting helper (K, M, B)
 */
function formatKMB($number) {
    if (!is_numeric($number)) return '0';
    if ($number >= 1000000000) return round($number / 1000000000, 1) . 'B';
    if ($number >= 1000000) return round($number / 1000000, 1) . 'M';
    if ($number >= 1000) return round($number / 1000, 1) . 'K';
    return $number;
}

/**
 * Helper function to extract a username handle from absolute URLs.
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

// ==========================================
// AI PHONE PARSING PIPELINE (GEMINI DISPATCH WITH CLEAN INPUT)
// ==========================================
$countryCode = ''; 
$pureNumber = '';

$geminiApiKey = GEMINI_API_KEY;
$geminiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $geminiApiKey;

$promptText = "Analyze this pure numeric phone string: \"{$pureStrippedNumber}\". Extract its parameters and return strictly a valid, minified JSON object containing keys: 'country_code' (numerical only, excluding any plus prefix symbols) and 'main_number' (the actual dialable local registry number with leading zeros stripped). Do not append any Markdown tags, backticks, or prose explanations. Only raw JSON string output.";

$geminiPayload = [
    "contents" => [
        ["parts" => [["text" => $promptText]]]
    ]
];

$aiCh = curl_init($geminiUrl);
curl_setopt($aiCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($aiCh, CURLOPT_POST, true);
curl_setopt($aiCh, CURLOPT_POSTFIELDS, json_encode($geminiPayload));
curl_setopt($aiCh, CURLOPT_TIMEOUT, 8);
curl_setopt($aiCh, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($aiCh, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$aiResponse = curl_exec($aiCh);
curl_close($aiCh);

if (!empty($aiResponse)) {
    $aiDataset = json_decode($aiResponse, true);
    $rawAiText = $aiDataset['candidates'][0]['content']['parts'][0]['text'] ?? '';
    
    $cleanJsonString = trim(str_replace(['```json', '```'], '', $rawAiText));
    $parsedSpecs = json_decode($cleanJsonString, true);
    
    if (!empty($parsedSpecs['country_code']) && !empty($parsedSpecs['main_number'])) {
        $countryCode = preg_replace('/[^0-9]/', '', $parsedSpecs['country_code']);
        $pureNumber  = preg_replace('/[^0-9]/', '', $parsedSpecs['main_number']);
    }
}

if (empty($countryCode) || empty($pureNumber)) {
    echo json_encode(['status' => 'success', 'results' => []]);
    exit;
}

$normalizedResults = [];
$rapidApiKey    = RAPIDAPI_API_KEY;
$socialFetchKey = SOCIALFETCH_API_KEY;

// ==========================================
// HOP 1: RAPIDAPI EYECON3 PHONE LOOKUP
// ==========================================
$eyeconEndpoint = "https://eyecon3.p.rapidapi.com/api/v1/search?code=" . urlencode($countryCode) . "&number=" . urlencode($pureNumber);

$ch = curl_init($eyeconEndpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-rapidapi-host: ' . RAPIDAPI_HOST,
    'x-rapidapi-key: ' . $rapidApiKey
]);

$eyeconResponse = curl_exec($ch);
curl_close($ch);

$eyeconDataset = json_decode($eyeconResponse, true);
$eyeconData = $eyeconDataset['data'] ?? null;

$discoveredName = $eyeconData['fullName'] ?? '';
$fbProfileUrl   = $eyeconData['facebookID']['url'] ?? '';

if (!empty($fbProfileUrl)) {
    // ==========================================
    // HOP 2: SOCIALFETCH FACEBOOK PIPELINE DEEP SYNC
    // ==========================================
    $sfEndpoint = "https://api.socialfetch.dev/v1/facebook/profiles?url=" . urlencode($fbProfileUrl);
    
    $sfCh = curl_init($sfEndpoint);
    curl_setopt($sfCh, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($sfCh, CURLOPT_TIMEOUT, 20);
    curl_setopt($sfCh, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($sfCh, CURLOPT_HTTPHEADER, [
        'x-api-key: ' . $socialFetchKey,
        'Accept: application/json'
    ]);
    
    $sfResponse = curl_exec($sfCh);
    curl_close($sfCh);
    
    $sfDataset = json_decode($sfResponse, true);
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
        if (!empty($metrics['followers'])) $metaDetails[] = formatKMB($metrics['followers']) . " Followers";
        if (!empty($metrics['likes'])) $metaDetails[] = formatKMB($metrics['likes']) . " Likes";
        if (!empty($business['intro'])) $metaDetails[] = $business['intro'];
        if (!empty($sfDataset['data']['contact']['website'])) $metaDetails[] = $sfDataset['data']['contact']['website'];

        $isSfVerified = isset($profile['isVerified']) ? (bool)$profile['isVerified'] : (isset($profile['is_verified']) ? (bool)$profile['is_verified'] : false);

        // Bypassed legacy synchronous server-side picture downloads entirely
        $rawAvatarUrl = $profile['avatarUrl'] ?? '';

        $normalizedResults[] = [
            'profile_id'  => $profile['platformUserId'] ?? 'N/A',
            'name'        => $profile['displayName'] ?? (!empty($discoveredName) ? $discoveredName : 'Hidden Identity'),
            'link'        => $profile['profileUrl'] ?? $fbProfileUrl,
            'avatar'      => $rawAvatarUrl, // Handed down raw for high-speed non-blocking parallel loading
            'raw_avatar'  => $rawAvatarUrl,
            'handle'      => !empty($metaDetails) ? implode(" • ", $metaDetails) : 'Facebook Profile Discovery Match',
            'is_verified' => $isSfVerified
        ];
    }
}

// FALLBACK: Store only name from RapidAPI if SocialFetch has no profile mapping
if (empty($normalizedResults) && !empty($discoveredName)) {
    $metaDetails = ["TrueCaller Identity Sync Match"];
    
    $normalizedResults[] = [
        'profile_id'  => $eyeconData['facebookID']['id'] ?? 'N/A',
        'name'        => $discoveredName,
        'link'        => $fbProfileUrl ? $fbProfileUrl : '#',
        'avatar'      => '',
        'raw_avatar'  => '',
        'handle'      => implode(" • ", $metaDetails),
        'is_verified' => false
    ];
}

foreach ($normalizedResults as &$result) {
    if (!empty($result['handle'])) {
        $styledBullet = '<span class="text-[10px] font-light opacity-35 mx-1.5">•</span>';
        $result['handle'] = str_replace(' • ', $styledBullet, $result['handle']);
    }
}
unset($result); 

// Storing ONLY clean fields & the SocialFetch payload dataset
$finalDataPayload = [
    'status' => 'success',
    'results' => $normalizedResults,
    'raw_socialfetch' => isset($sfDataset['data']) ? $sfDataset['data'] : null
];

$outputPayload = json_encode($finalDataPayload);

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
            ':search_query'  => (string)$pureStrippedNumber,
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
