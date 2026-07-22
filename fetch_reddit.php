<?php
// fetch_reddit.php
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

$apifyToken = APIFY_API_KEY_2;
$actorId    = 'red_crawler~reddit-search-v2';

// Build payload parameters mapping to your exact test input requirements
$payload = [
    "endpoint"     => "search",
    "search_limit" => 10,
    "search_query" => $search_query,
    "search_type"  => "people"
];

// Unified runtime synchronous wrapper dataset endpoint
$endpoint = "https://api.apify.com/v2/acts/" . $actorId . "/run-sync-get-dataset-items?token=" . $apifyToken;

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 180); // Allocated for headful browser execution cycles
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 && $httpCode !== 201) {
    echo json_encode(['status' => 'error', 'message' => 'Cloud computation engine fault code: ' . $httpCode]);
    exit;
}

$rawDataset = json_decode($response, true);
$normalizedResults = [];

// Parse data rows layout variations safely
$records = [];
if (is_array($rawDataset)) {
    if (isset($rawDataset['data']['items'])) {
        $records = $rawDataset['data']['items'];
    } else {
        $records = $rawDataset;
    }
}

// Standardize output map keys to match frontend UI client expectations seamlessly
foreach ($records as $item) {
    if (is_array($item)) {
        // Validate that this row is a Redditor identity node 
        if (($item['__typename'] ?? '') !== 'Redditor' && empty($item['name'])) {
            continue;
        }

        // Determine profile avatar asset paths cleanly across available fallback configurations
        $avatarUrl = null;
        if (!empty($item['profileInfo']['styles']['legacyIcon']['url'])) {
            $avatarUrl = $item['profileInfo']['styles']['legacyIcon']['url'];
        } elseif (!empty($item['icon']['url'])) {
            $avatarUrl = $item['icon']['url'];
        }

        $username = $item['name'] ?? '';
        
        $normalizedResults[] = [
            'profile_id'    => $item['id'] ?? '',
            'name'          => $item['prefixedName'] ?? ('u/' . $username),
            'handler'       => $username,
            'link'          => 'https://www.reddit.com/user/' . $username,
            'avatar'        => $avatarUrl,
            'karma_total'   => $item['karma']['total'] ?? 0,
            'is_nsfw'       => (bool)($item['profileInfo']['isNsfw'] ?? false),
            'allow_follows' => (bool)($item['isAcceptingFollowers'] ?? true)
        ];
    }
}

// Send clean consolidated data structures back to frontend console components stream
echo json_encode([
    'status'  => 'success',
    'results' => $normalizedResults
]);
exit;