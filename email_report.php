<?php
/**
 * Identity Trace AI — Automated Transactional Reporting Matrix
 * File: email_report.php
 * Context: Included within process.php context sequence maps or ran as daemon hooks
 */

// Context boundary lock protection checks inherited from parent orchestrator script scope
$targetVid = isset($vid) ? trim($vid) : '';
if (empty($targetVid)) {
    error_log("Email Report System Failure: Scope parameter execution halted due to missing target identity context verification.");
    return;
}

$dbConnection = $GLOBALS['pdo'] ?? ($pdo ?? null);
if (!($dbConnection instanceof PDO)) {
    error_log("Email Report System Failure: Data tier connection layer dropped parameters mid-flight.");
    return;
}

try {
    // 1. Gather target configuration matrices from base tracking tables
    $viewQuery = $dbConnection->prepare("SELECT `name`, `source`, `input_email` FROM `view` WHERE `vid` = ? LIMIT 1");
    $viewQuery->execute([$targetVid]);
    $viewRow = $viewQuery->fetch(PDO::FETCH_ASSOC);

    if (!$viewRow) {
        error_log("Email Report Interception Alert [VID: {$targetVid}]: View directory item metadata could not be fetched.");
        return;
    }

    // Query report record to establish status context
    $reportQuery = $dbConnection->prepare("SELECT `status`, `uid` FROM `reports` WHERE `vid` = ? LIMIT 1");
    $reportQuery->execute([$targetVid]);
    $reportRow = $reportQuery->fetch(PDO::FETCH_ASSOC);

    $isFailedState = ($reportRow && $reportRow['status'] === 'failed');

    // Determine target delivery address fallback routes safely
    $recipientEmail = !empty($viewRow['input_email']) ? trim($viewRow['input_email']) : '';
    
    if (empty($recipientEmail) && !empty($reportRow['uid'])) {
        $userQuery = $dbConnection->prepare("SELECT `email` FROM `users` WHERE `id` = ? LIMIT 1");
        $userQuery->execute([(int)$reportRow['uid']]);
        $userRow = $userQuery->fetch(PDO::FETCH_ASSOC);
        $recipientEmail = !empty($userRow['email']) ? trim($userRow['email']) : '';
    }

    if (empty($recipientEmail)) {
        error_log("Email Report Interception Warning [VID: {$targetVid}]: Terminated outbound message pass due to missing client delivery targets.");
        return;
    }

    // 2. Prepare dynamic context strings for HTML string rendering wrappers
    $displayTargetName   = !empty($viewRow['name']) ? htmlspecialchars($viewRow['name'], ENT_QUOTES, 'UTF-8') : 'Unknown Target Identity';
    $displayTargetSource = !empty($viewRow['source']) ? htmlspecialchars(ucfirst($viewRow['source']), ENT_QUOTES, 'UTF-8') : 'OSINT Scan System Core';
    $currentDateTimeUtc  = gmdate('Y-m-d H:i') . ' UTC';
    
    // Always route the user to the report parameters directly since the target file renders the failed screen too
    $secureReportUrl  = rtrim(BASE_URL, '/') . '/report.php?id=' . urlencode($targetVid);

    // =========================================================================
    // RENAME INTERFACES: CONDITIONAL COPY ROUTING MATRIX
    // =========================================================================
    if ($isFailedState) {
        $emailSubject     = "Identity Search AI - Intelligence Report Failed [{$displayTargetName}]";
        $headerMessage    = "Analysis Interrupted (Credit Refunded)";
        $bodyNarrative    = "Our deep cognitive trace analysis core has Failed to processing the requested targets. Cause may include insufficient data source, server error, ai response error. Try some time later with more social media data source.";
        $statusBadgeHtml  = "<span style='background-color: #FEE2E2; color: #991B1B; padding: 2px 6px; font-size: 9px; font-weight: 700; border-radius: 5px; text-transform: uppercase; display: inline-block; letter-spacing: 0.3px;'>Failed</span>";
        $buttonColorStyle = "background-color: #DC2626; box-shadow: 0 2px 4px rgba(220, 38, 38, 0.15);";
    } else {
        $emailSubject     = "Identity Search AI - Intelligence Report Completed [{$displayTargetName}]";
        $headerMessage    = "Your Intelligence Report is Ready";
        $bodyNarrative    = "Our deep cognitive trace analysis core has successfully completed processing the requested targets.";
        $statusBadgeHtml  = "<span style='background-color: #D1FAE5; color: #065F46; padding: 2px 6px; font-size: 9px; font-weight: 700; border-radius: 5px; text-transform: uppercase; display: inline-block; letter-spacing: 0.3px;'>Complete</span>";
        $buttonColorStyle = "background-color: #128c7e; box-shadow: 0 2px 4px rgba(18, 140, 126, 0.15);";
    }

    // =========================================================================
    // CONSTRUCT TRANSACTIONAL EMAIL HIGHLIGHT TEMPLATE
    // =========================================================================
    $htmlBody = "
        <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
            <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>
                
                <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                    <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                        <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='Identity Search AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                        <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; display: inline-block; vertical-align: middle;'>Identity Search <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                    </div>
                </div>
                
                <div style='padding: 24px 20px; text-align: left;'>
                    <h2 style='font-size: 16px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 10px;'>{$headerMessage}</h2>
                    <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 18px;'>{$bodyNarrative}</p>
                    
                    <!-- RENAME: Dossier Metrics -> Report Summary -->
                    <div style='background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px; margin-bottom: 20px;'>
                        <div style='margin-bottom: 6px; font-size: 11px; font-weight: 700; color: #111827; border-bottom: 1px dashed #E5E7EB; padding-bottom: 4px; text-transform: uppercase; letter-spacing: 0.2px;'>Report Summary</div>
                        <div style='margin-bottom: 6px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Name:</b> {$displayTargetName}</div>
                        <div style='margin-bottom: 6px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Source:</b> {$displayTargetSource}</div>
                        <div style='margin-bottom: 6px; font-size: 11px; color: #4B5563;'>
                            <b style='color: #111827;'>Status:</b> 
                            {$statusBadgeHtml}
                        </div>
                        <div style='font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Date:</b> {$currentDateTimeUtc}</div>
                    </div>

                    <!-- RENAME: Body Footer Text -> Unified Check Statement -->
                    <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 20px;'>Please log into your account and check the report details.</p>

                    <!-- RENAME: Unified Action Button -> Open Report -->
                    <div style='text-align: center; margin-bottom: 8px;'>
                        <a href='{$secureReportUrl}' target='_blank' style='color: #FFFFFF; font-size: 12px; font-weight: 700; text-decoration: none; padding: 12px 24px; border-radius: 10px; display: inline-block; {$buttonColorStyle}'>Open Report</a>
                    </div>
                </div>
                
                <div style='padding: 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: center;'>
                    <div style='display: block; margin-bottom: 8px;'>
                        <span style='font-size: 16px; display: inline-block; vertical-align: middle;'>🕵️‍♂️</span>
                    </div>
                    <p style='font-size: 9px; color: #4B5563; font-weight: 500; margin: 0 0 4px 0;'>&copy; 2026 - Identity Trace AI</p>
                    <p style='font-size: 9px; color: #4B5563; font-weight: 400; margin: 0;'>
                        <a href='mailto:support@idtrace.ai' style='color: #128c7e; text-decoration: none;'>support@idtrace.ai</a>
                    </p>
                </div>

            </div>
        </div>
    ";

    sendTransactionalMail($recipientEmail, $emailSubject, $htmlBody);

} catch (Exception $ex) {
    error_log("Email Report Execution Exception [VID: {$targetVid}]: " . $ex->getMessage());
}
