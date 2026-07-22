<?php
/**
 * OSINT Universal Intelligence Console — Background Task Worker
 * File: scrape_email.php
 * Context: Runs sequentially inside process.php context loop mapping or report.php inline scope
 */

// 1. CONTEXT PROTECTION CHECK
$targetEmail = isset($currentModuleEmail) ? trim($currentModuleEmail) : '';
$targetVid   = isset($vid) ? trim($vid) : '';

if (empty($targetEmail) || empty($targetVid)) {
    error_log("Email Scraper Failure: Execution stalled due to missing runtime variable scopes.");
    return;
}

$db = $GLOBALS['pdo'] ?? ($pdo ?? null);
if (!($db instanceof PDO)) {
    error_log("Email Scraper Failure: Active database connection context broken.");
    return;
}

// 2. CONFIGURATION MATRIX BOUNDS
$apifyToken = APIFY_API_KEY;
$actorId    = 'one-api~skip-trace';

// Simplified input payload map to only match email fields and isolate target data output accuracy
$payload = [
    "email" => [$targetEmail]
];

// =========================================================================
// EXECUTE REVERSE EMAIL OSINT SCAN (APIFY RUN-SYNC)
// =========================================================================
$endpoint = "https://api.apify.com/v2/acts/" . $actorId . "/run-sync-get-dataset-items?token=" . $apifyToken;

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 120); 
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$emailResponsePayload = null;

if ($httpCode === 200 && !empty($response)) {
    // Validates JSON structure, tracking full un-modified raw array data block directly from endpoint returns
    $decodedJson = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE && !empty($decodedJson)) {
        $emailResponsePayload = $decodedJson;
    }
} else {
    error_log("Email Scraper Alert [VID: {$targetVid}]: Skip-trace endpoint returned HTTP status code {$httpCode}");
}

// =========================================================================
// COMMIT EXTRACTED INTELLIGENCE BLOCK TO THE REPORT TABLE (UNALTERED STORAGE)
// =========================================================================
if (!empty($emailResponsePayload)) {
    try {
        // Save the raw leak/profile results map structure natively under raw_email_data column
        $updateStmt = $db->prepare("UPDATE `reports` 
            SET `raw_email_data` = :emailData, 
                `updated_at`     = NOW() 
            WHERE `vid` = :vid");
        
        // CHANGED: Encodes the Apify response payload preserving unescaped slashes and unicode formats cleanly
        $emailPayloadData = json_encode($emailResponsePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $updateStmt->execute([
            ':emailData' => $emailPayloadData,
            ':vid'       => $targetVid
        ]);

    } catch (Exception $dbEx) {
        error_log("Email Scraper Database Exception [VID: {$targetVid}]: " . $dbEx->getMessage());
    }
} else {
    error_log("Email Scraper Operational Stoppage [VID: {$targetVid}]: Skip-trace analysis returned empty data blocks.");
}