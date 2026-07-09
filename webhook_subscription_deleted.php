<?php
/**
 * Sub-Webhook Module — Job 2: Permanent Subscription Deactivation Handler
 * File: webhook_subscription_deleted.php
 */

$stripe_subscription_id = $object['id'] ?? '';
$stripe_customer_id = $object['customer'] ?? '';

if (empty($stripe_subscription_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete webhook operational parameters.']);
    exit;
}

try {
    // SECURITY FILTER (OPTION 1): Verify if this subscription matches the user's active row.
    // If no row is found, it means success.php already updated the column with a new subscription ID for an upgrade.
    $check_stmt = $pdo->prepare("SELECT `id`, `email`, `stripe_subscription_id` FROM `users` WHERE `stripe_subscription_id` = ? LIMIT 1");
    $check_stmt->execute([$stripe_subscription_id]);
    $user_record = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user_record) {
        echo json_encode(['status' => 'ignored', 'message' => 'Subscription mismatch. Likely an upgraded swap lifecycle event. Local records preserved.']);
        exit;
    }

    $user_id = (int)$user_record['id'];
    $user_email = $user_record['email'];

    // Legitimate Churn / Overdue Drop detected -> Wiping profile tokens and active balance allocations
    $clear_stmt = $pdo->prepare("
        UPDATE `users` 
        SET `stripe_subscription_id` = NULL, 
            `plan` = NULL, 
            `credit` = 0, 
            `validity` = NULL 
        WHERE `id` = ?
    ");
    $clear_stmt->execute([$user_id]);
    
    // Centralized Churn Email dispatch using your universal brand layout framework
    if (!empty($user_email)) {
        $cancelHtmlBody = "
            <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
                <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>
                    <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                        <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                            <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='Identity Search AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                            <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; display: inline-block; vertical-align: middle;'>Identity Search <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                        </div>
                    </div>
                    <div style='padding: 24px 20px; text-align: left;'>
                        <h2 style='font-size: 16px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 10px;'>Subscription Canceled</h2>
                        <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 12px;'>This message confirms that your subscription has been successfully canceled and closed out.</p>
                        <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 0;'>Your credits have been reset to zero. To subscribe again, please visit your account dashboard.</p>
                    </div>
                    <div style='padding: 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: center;'>
                        <div style='display: block; margin-bottom: 8px;'>
                            <span style='font-size: 16px; display: inline-block; vertical-align: middle;'>🕵️‍♂️</span>
                        </div>
                        <p style='font-size: 9px; color: #4B5563; font-weight: 500; margin: 0 0 4px 0;'>&copy; 2026 - Identity Search AI</p>
                        <p style='font-size: 9px; color: #4B5563; font-weight: 400; margin: 0;'>
                            <a href='mailto:support@idtrace.ai' style='color: #128c7e; text-decoration: none;'>support@idtrace.ai</a>
                        </p>
                    </div>
                </div>
            </div>
        ";

        $cancelSubject = "Subscription Canceled Successfully — Identity Search AI";
        sendTransactionalMail($user_email, $cancelSubject, $cancelHtmlBody);
    }

    echo json_encode(['status' => 'success', 'message' => 'Subscription hard-deleted. Profile structures wiped. Notice email dispatched.']);
} catch (Exception $clearEx) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $clearEx->getMessage()]);
}
