<?php
/**
 * OSINT Universal Intelligence Console — Target Analysis Deep Orchestrator
 * File: process.php
 * Mode: CLI / Background Service Only
 */

// 1. SYSTEM ISOLATION & TIMEOUT IMMUNITY SETTINGS
set_time_limit(0);         // Prevents PHP from killing the background script
ignore_user_abort(true);    // Ensures script continues running if triggered via web and client aborts

require_once 'config.php';

// Explicitly include your email engine configuration file
if (file_exists('mailer.php')) {
    require_once 'mailer.php';
}

/**
 * HELPER LAYER: SECURE DISTRIBUTED MEDIA HARVESTER
 * Saves images flat into the primary root uploads directory.
 */
function downloadScrapedMedia($url) {
    $url = html_entity_decode(trim($url));
    
    if (empty($url) || strpos($url, 'http') !== 0) {
        return $url;
    }

    $dirPath = __DIR__ . '/uploads';
    if (!is_dir($dirPath)) {
        mkdir($dirPath, 0777, true);
    }

    $fileName = md5($url) . '.jpg';
    $absolutePath = $dirPath . '/' . $fileName;
    $relativePath = 'uploads/' . $fileName;

    if (file_exists($absolutePath) && filesize($absolutePath) > 0) {
        return $relativePath;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, httpGecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept: image/avif,image/webp,image/*,*/*;q=0.8',
        'Referer: https://www.instagram.com/'
    ]);

    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && !empty($data)) {
        if (file_put_contents($absolutePath, $data) !== false) {
            return $relativePath;
        }
    }

    return $url; 
}

// Parse command line style runtime query string flags passed from fireBackgroundWorker()
parse_str(implode('&', array_slice($argv, 1)), $_GET);
$vid = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($vid)) {
    error_log("OSINT Core Orchestrator Failure: Script executed without a target view identity context token.");
    exit(1);
}

try {
    // 2. CORRELATE PARENT VIEW COORDINATES
    $stmt = $pdo->prepare("SELECT `name`, `input_email`, `avatar`, `social_urls` FROM `view` WHERE `vid` = ? LIMIT 1");
    $stmt->execute([$vid]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$target) {
        throw new Exception("Master tracking view entity could not be traced inside the directory layout table.");
    }

    // Set row task layout parameters to 'processing' to establish a lock state
    $pdo->prepare("UPDATE `reports` SET `status` = 'processing', `updated_at` = NOW() WHERE `vid` = ?")->execute([$vid]);

    // Gather existing operational properties from resource row
    $targetName  = trim($target['name'] ?? '');
    $targetEmail = trim($target['input_email'] ?? '');
    $targetPhoto = trim($target['avatar'] ?? '');
    $socialsRaw  = trim($target['social_urls'] ?? '');

    // -------------------------------------------------------------------------
    // STEP 1: CONSOLIDATE, SANITIZE, AND MERGE SOCIAL TARGET MATRIX (Strictly social_urls)
    // -------------------------------------------------------------------------
    $socialLinks = [];
    
    if (!empty($socialsRaw)) {
        $explodedUrls = explode(',', $socialsRaw);
        foreach ($explodedUrls as $rawUrl) {
            $cleanedUrl = rtrim(trim($rawUrl), '/'); 
            if (!empty($cleanedUrl) && !in_array($cleanedUrl, $socialLinks)) {
                $socialLinks[] = $cleanedUrl;
            }
        }
    }

    // -------------------------------------------------------------------------
    // STEP 2: SEQUENTIAL SOCIAL PROFILE SCRAPING PIPELINE
    // -------------------------------------------------------------------------
    foreach ($socialLinks as $activeUrl) {
        $lowerUrl = strtolower($activeUrl);
        $currentModuleUrl = $activeUrl;

        if (strpos($lowerUrl, 'facebook.com') !== false || strpos($lowerUrl, 'fb.com') !== false) {
            if (file_exists('scrape_facebook.php')) { include 'scrape_facebook.php'; }
        } 
        elseif (strpos($lowerUrl, 'instagram.com') !== false) {
            if (file_exists('scrape_instagram.php')) { include 'scrape_instagram.php'; }
        } 
        elseif (strpos($lowerUrl, 'twitter.com') !== false || strpos($lowerUrl, 'x.com') !== false) {
            if (file_exists('scrape_twitter.php')) { include 'scrape_twitter.php'; }
        } 
        elseif (strpos($lowerUrl, 'linkedin.com') !== false) {
            if (file_exists('scrape_linkedin.php')) { include 'scrape_linkedin.php'; }
        } 
        elseif (strpos($lowerUrl, 'tiktok.com') !== false) {
            if (file_exists('scrape_tiktok.php')) { include 'scrape_tiktok.php'; }
        }
    }

    // -------------------------------------------------------------------------
    // STEP 3: BREACH SEARCH AND EMAIL TRACKING ENGINES
    // -------------------------------------------------------------------------
    if (!empty($targetEmail)) {
        $currentModuleEmail = $targetEmail;
        if (file_exists('scrape_email.php')) { include 'scrape_email.php'; }
    }

    // -------------------------------------------------------------------------
    // STEP 4: COGNITIVE INTELLIGENCE MATRIX SYNTHESIS (GEMINI)
    // -------------------------------------------------------------------------
    // Initializing scope fallback data elements
    $cleanJsonArray = null; 

    if (file_exists('gemini.php')) {
        include 'gemini.php';
    } else {
        throw new Exception("Critical dependency broken: Root intelligence layout 'gemini.php' not found in website root.");
    }

    // -------------------------------------------------------------------------
    // STEP 5: EVALUATE MATRIX SYNTHESIS TARGET RESPONSE CODES
    // -------------------------------------------------------------------------
    // Evaluate payload responses directly parsed down out of gemini.php context arrays
    $isSuccessState = (is_array($cleanJsonArray) && isset($cleanJsonArray['status']) && strtolower($cleanJsonArray['status']) === 'success');

    if (!$isSuccessState) {
        // Enforce fallback failed state validation criteria rules
        $pdo->prepare("UPDATE `reports` SET `status` = 'failed', `updated_at` = NOW() WHERE `vid` = ?")->execute([$vid]);
        
        // Fetch User Identity row parameters to execute credit allocation safety protocols
        $checkReportStmt = $pdo->prepare("SELECT `uid` FROM `reports` WHERE `vid` = ? LIMIT 1");
        $checkReportStmt->execute([$vid]);
        $reportDataState = $checkReportStmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($reportDataState['uid'])) {
            try {
                $pdo->beginTransaction();
                $refundQuery = $pdo->prepare("UPDATE `users` SET `credit` = `credit` + 1 WHERE `id` = ?");
                $refundQuery->execute([(int)$reportDataState['uid']]);
                $pdo->commit();
                error_log("Credit Refund Tracker [VID: {$vid}]: Re-credited 1 token to User ID {$reportDataState['uid']} due to thin tracking matrices.");
            } catch (Exception $dbEx) {
                $pdo->rollBack();
                error_log("Credit Refund Critical Failure [VID: {$vid}]: " . $dbEx->getMessage());
            }
        }

        // Dispatch unified transactional exception/failed notification email parameters 
        if (file_exists('email_report.php')) { include 'email_report.php'; }
        exit(0);
    }

    // -------------------------------------------------------------------------
    // STEP 5.5: SECURELY WRITE HIGH-FIDELITY INTELLIGENCE DATA ONLY ON VALID SUCCESS STATE
    // -------------------------------------------------------------------------
    $cleanJsonString = json_encode($cleanJsonArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $finalStatement = $pdo->prepare("UPDATE `reports` SET `ai_analysis` = ? WHERE `vid` = ?");
    $finalStatement->execute([$cleanJsonString, $vid]);

    // -------------------------------------------------------------------------
    // STEP 6: DISPATCH COMPLETION DOSSIER EMAIL REPORT (SUCCESS STATE MAP)
    // -------------------------------------------------------------------------
    if (file_exists('email_report.php')) {
        include 'email_report.php';
    }

    // -------------------------------------------------------------------------
    // STEP 7: CLOSE COMPILATION TRANSACTION CYCLE (SUCCESS CONFIRMATION ONLY)
    // -------------------------------------------------------------------------
    $pdo->prepare("UPDATE `reports` SET `status` = 'completed', `updated_at` = NOW() WHERE `vid` = ?")->execute([$vid]);
    exit(0);

} catch (Exception $e) {
    // General global runtime boundary safety catches
    $failStmt = $pdo->prepare("UPDATE `reports` SET `status` = 'failed', `updated_at` = NOW() WHERE `vid` = ?");
    $failStmt->execute([$vid]);
    
    error_log("OSINT Core Orchestrator Crash Event [VID: $vid]: " . $e->getMessage());
    exit(1);
}
