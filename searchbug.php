<?php

// Custom logger: writes every SearchBug event to searchbugLog.txt
function searchbugLog($message) {
    $logFile = __DIR__ . '/searchbugLog.txt';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[{$timestamp}] {$message}" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

$targetVid   = isset($vid) ? trim($vid) : '';
$fullNameRaw = isset($targetName) ? trim($targetName) : '';

if (empty($targetVid) || empty($fullNameRaw)) {
    searchbugLog("SKIP [VID: {$targetVid}] — missing vid or target name.");
    return;
}

$db = $GLOBALS['pdo'] ?? ($pdo ?? null);
if (!($db instanceof PDO)) {
    searchbugLog("SKIP [VID: {$targetVid}] — database connection unavailable.");
    return;
}

if (empty(SEARCHBUG_API_KEY) || empty(SEARCHBUG_CO_CODE)) {
    searchbugLog("SKIP [VID: {$targetVid}] — API key or CO_CODE not configured.");
    return;
}

$nameTokens = preg_split('/\s+/', $fullNameRaw);
$nameTokens = array_filter($nameTokens);

if (count($nameTokens) < 2) {
    searchbugLog("SKIP [VID: {$targetVid}] — single-word name: '{$fullNameRaw}'");
    return;
}

$lastName  = array_pop($nameTokens);
$firstName = implode(' ', $nameTokens);

$firstName = preg_replace("/[^A-Za-z'\\-\\s]/", '', trim($firstName));
$lastName  = preg_replace("/[^A-Za-z'\\-\\s]/", '', trim($lastName));

if (empty($firstName) || empty($lastName)) {
    searchbugLog("SKIP [VID: {$targetVid}] — sanitized name empty after filtering.");
    return;
}

searchbugLog("START [VID: {$targetVid}] — searching for: {$firstName} {$lastName}");

$peopleSearchParams = [
    'CO_CODE' => SEARCHBUG_CO_CODE,
    'TYPE'    => 'api_ppl',
    'FNAME'   => $firstName,
    'LNAME'   => $lastName,
    'FORMAT'  => 'JSON',
    'REF'     => 'OSINT-' . $targetVid
];

$step1Ch = curl_init(SEARCHBUG_BASE_URL);
curl_setopt($step1Ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($step1Ch, CURLOPT_POST, true);
curl_setopt($step1Ch, CURLOPT_POSTFIELDS, http_build_query($peopleSearchParams));
curl_setopt($step1Ch, CURLOPT_TIMEOUT, 30);
curl_setopt($step1Ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($step1Ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . SEARCHBUG_API_KEY,
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
]);

$step1Response  = curl_exec($step1Ch);
$step1HttpCode  = curl_getinfo($step1Ch, CURLINFO_HTTP_CODE);
$step1CurlError = curl_error($step1Ch);
$step1CurlErrno = curl_errno($step1Ch);
curl_close($step1Ch);

if ($step1CurlErrno !== 0) {
    searchbugLog("ERROR Step1 [VID: {$targetVid}] — cURL error ({$step1CurlErrno}): {$step1CurlError}");
    return;
}

if ($step1HttpCode !== 200 || empty($step1Response)) {
    searchbugLog("ERROR Step1 [VID: {$targetVid}] — HTTP {$step1HttpCode}, empty response.");
    return;
}

$step1Data = json_decode($step1Response, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($step1Data)) {
    searchbugLog("ERROR Step1 [VID: {$targetVid}] — invalid JSON: " . json_last_error_msg());
    return;
}

$apiError = $step1Data['Error'] ?? $step1Data['error'] ?? null;
if (!empty($apiError)) {
    searchbugLog("ERROR Step1 [VID: {$targetVid}] — API error: " . $apiError);
    return;
}

searchbugLog("STEP1 Response [VID: {$targetVid}] — " . substr($step1Response, 0, 500));

$reportToken = null;

if (isset($step1Data['result']['people']['person']['reportToken'])) {
    $reportToken = $step1Data['result']['people']['person']['reportToken'];
} elseif (isset($step1Data['result']['people']['person']) && is_array($step1Data['result']['people']['person'])) {
    $persons = $step1Data['result']['people']['person'];
    if (isset($persons[0]['reportToken'])) {
        $reportToken = $persons[0]['reportToken'];
    } elseif (isset($persons['reportToken'])) {
        $reportToken = $persons['reportToken'];
    }
} elseif (isset($step1Data['reportToken'])) {
    $reportToken = $step1Data['reportToken'];
} elseif (isset($step1Data['RESULTS']['reportToken'])) {
    $reportToken = $step1Data['RESULTS']['reportToken'];
}

if (empty($reportToken)) {
    searchbugLog("NO MATCH [VID: {$targetVid}] — no reportToken found for: {$firstName} {$lastName}");
    return;
}

searchbugLog("STEP1 OK [VID: {$targetVid}] — reportToken: {$reportToken}");

$backgroundParams = [
    'CO_CODE'     => SEARCHBUG_CO_CODE,
    'TYPE'        => 'api_back',
    'reportToken' => $reportToken,
    'FORMAT'      => 'JSON',
    'REF'         => 'OSINT-' . $targetVid
];

$step2Ch = curl_init(SEARCHBUG_BASE_URL);
curl_setopt($step2Ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($step2Ch, CURLOPT_POST, true);
curl_setopt($step2Ch, CURLOPT_POSTFIELDS, http_build_query($backgroundParams));
curl_setopt($step2Ch, CURLOPT_TIMEOUT, 60);
curl_setopt($step2Ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($step2Ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . SEARCHBUG_API_KEY,
    'Content-Type: application/x-www-form-urlencoded',
    'Accept: application/json'
]);

$step2Response  = curl_exec($step2Ch);
$step2HttpCode  = curl_getinfo($step2Ch, CURLINFO_HTTP_CODE);
$step2CurlError = curl_error($step2Ch);
$step2CurlErrno = curl_errno($step2Ch);
curl_close($step2Ch);

if ($step2CurlErrno !== 0) {
    searchbugLog("ERROR Step2 [VID: {$targetVid}] — cURL error ({$step2CurlErrno}): {$step2CurlError}");
    return;
}

if ($step2HttpCode !== 200 || empty($step2Response)) {
    searchbugLog("ERROR Step2 [VID: {$targetVid}] — HTTP {$step2HttpCode}, empty response.");
    return;
}

$step2Data = json_decode($step2Response, true);
if (json_last_error() !== JSON_ERROR_NONE || !is_array($step2Data)) {
    searchbugLog("ERROR Step2 [VID: {$targetVid}] — invalid JSON: " . json_last_error_msg());
    return;
}

$step2Error = $step2Data['Error'] ?? $step2Data['error'] ?? null;
if (!empty($step2Error)) {
    searchbugLog("ERROR Step2 [VID: {$targetVid}] — API error: " . $step2Error);
    return;
}

$backgroundReport = $step2Data['result'] ?? $step2Data;

searchbugLog("STEP2 OK [VID: {$targetVid}] — report received, size: " . strlen($step2Response) . " bytes");

$enrichedData = [
    'search_metadata' => [
        'search_name'      => $firstName . ' ' . $lastName,
        'report_token'     => $reportToken,
        'retrieved_at'     => gmdate('Y-m-d\TH:i:s\Z'),
        'source'           => 'searchbug_background_report'
    ],
    'names'                  => $backgroundReport['names'] ?? null,
    'addresses'              => $backgroundReport['addresses'] ?? null,
    'phones'                 => $backgroundReport['phones'] ?? null,
    'emails'                 => $backgroundReport['emails'] ?? null,
    'dob'                    => $backgroundReport['DOBs'] ?? null,
    'relatives'              => $backgroundReport['relatives'] ?? null,
    'criminal_records'       => $backgroundReport['criminalRecords'] ?? null,
    'corporate_filings'      => $backgroundReport['corporateFilings'] ?? null,
    'personal_bankruptcy'    => $backgroundReport['personalBankruptcyFilings'] ?? null,
    'personal_lien_filings'  => $backgroundReport['personalLienFilings'] ?? null,
    'personal_judgment'      => $backgroundReport['personalJudgmentFilings'] ?? null,
    'evictions'              => $backgroundReport['evictions'] ?? null,
    'ucc_filings'            => $backgroundReport['UCCFilings'] ?? null,
    'professional_licenses'  => $backgroundReport['professionalLicenses'] ?? null,
    'professional_associations' => $backgroundReport['professionalAssociations'] ?? null,
    'trade_marks'            => $backgroundReport['tradeMarks'] ?? null,
    'watchlist_records'      => $backgroundReport['watchListRecords'] ?? null,
    'concealed_weapon_permits' => $backgroundReport['concealedWeaponPermits'] ?? null,
    'hunting_permits'        => $backgroundReport['huntingPermits'] ?? null,
    'pilot_licenses'         => $backgroundReport['pilotLicenses'] ?? null,
    'dod'                    => $backgroundReport['DODs'] ?? null,
    'business_associations'  => $backgroundReport['businessAssociations'] ?? null,
    'voter_registrations'    => $backgroundReport['voterRegistrations'] ?? null,
    'employers'              => $backgroundReport['employers'] ?? null,
    'employment_history'     => $backgroundReport['employmentHistory'] ?? null,
    'drivers_licenses'       => $backgroundReport['driversLicenses'] ?? null
];

$storagePayload = [
    'raw_response'  => $backgroundReport,
    'parsed_report' => $enrichedData
];

$encodedPayload = json_encode($storagePayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

try {
    $updateStmt = $db->prepare(
        "UPDATE `reports`
         SET `raw_searchbug` = :searchbugData,
             `updated_at` = NOW()
         WHERE `vid` = :vid"
    );
    $updateStmt->execute([
        ':searchbugData' => $encodedPayload,
        ':vid'           => $targetVid
    ]);

    searchbugLog("SUCCESS [VID: {$targetVid}] — background report stored for: {$firstName} {$lastName}, payload: " . strlen($encodedPayload) . " bytes");
} catch (Exception $dbEx) {
    searchbugLog("ERROR DB [VID: {$targetVid}] — persistence failed: " . $dbEx->getMessage());
    return;
}
