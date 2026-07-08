<?php
/**
 * OSINT Universal Intelligence Console — Background Task Worker
 * File: scrape_avatar.php
 * Context: Runs sequentially inside process.php context loop mapping
 */

// 1. CONTEXT PROTECTION CHECK
$targetPhoto = isset($currentModulePhoto) ? trim($currentModulePhoto) : '';
$targetVid   = isset($vid) ? trim($vid) : '';

if (empty($targetPhoto) || empty($targetVid)) {
    error_log("Avatar Scraper Failure: Execution stalled due to missing runtime variable scopes.");
    return;
}

$db = $GLOBALS['pdo'] ?? ($pdo ?? null);
if (!($db instanceof PDO)) {
    error_log("Avatar Scraper Failure: Active database connection context broken.");
    return;
}

// 2. CONFIGURATION MATRIX BOUNDS
$apifyToken = APIFY_API_KEY;
$actorId    = 'thodor~google-lens-exact-matches';

// Construct the input payload array matching the required Apify actor schema rules
$payload = [
    "imageUrls" => [$targetPhoto],
    "resolve"   => false
];

// =========================================================================
// EXECUTE NATIVE GOOGLE LENS REVERSE IMAGE ANALYSIS (APIFY RUN-SYNC)
// =========================================================================
$endpoint = "https://api.apify.com/v2/acts/" . $actorId . "/run-sync-get-dataset-items?token=" . $apifyToken;

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 180); // Lens engines can take up to 2-3 mins to scrap thoroughly 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$lensPayload = null;

if ($httpCode === 200 && !empty($response)) {
    $decodedResponse = json_decode($response, true);
    if (isset($decodedResponse['data'])) {
        $lensPayload = $decodedResponse['data'];
    } else if (is_array($decodedResponse)) {
        $lensPayload = $decodedResponse;
    }
} else {
    error_log("Avatar Scraper Alert [VID: {$targetVid}]: Google Lens endpoint returned HTTP status code {$httpCode}");
}

// =========================================================================
// COMMIT EXTRACTED INTELLIGENCE BLOCK TO THE REPORT TABLE
// =========================================================================
if (!empty($lensPayload)) {
    try {
        // Save the raw exact matches array structure natively under the target column: raw_reverse_data
        $updateStmt = $db->prepare("UPDATE `reports` 
            SET `raw_reverse_data` = :matches, 
                `updated_at`       = NOW() 
            WHERE `vid` = :vid");
        
        $updateStmt->execute([
            ':matches' => json_encode($lensPayload),
            ':vid'     => $targetVid
        ]);

    } catch (Exception $dbEx) {
        error_log("Avatar Scraper Database Exception [VID: {$targetVid}]: " . $dbEx->getMessage());
    }
} else {
    error_log("Avatar Scraper Operational Stoppage [VID: {$targetVid}]: Image proxy analysis returned an empty array block.");
}
