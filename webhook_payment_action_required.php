<?php
/**
 * Sub-Webhook Module — Job: Intermediate Soft Decline / Action Required Notification (Global)
 * File: webhook_payment_action_required.php
 */

$stripe_subscription_id = $object['subscription'] ?? '';
$stripe_customer_id = $object['customer'] ?? '';

try {
    $u_stmt = $pdo->prepare("SELECT `email` FROM `users` WHERE `stripe_subscription_id` = ? OR `stripe_customer_id` = ? LIMIT 1");
    $u_stmt->execute([$stripe_subscription_id, $stripe_customer_id]);
    $warn_user = $u_stmt->fetch(PDO::FETCH_ASSOC);

    if ($warn_user && !empty($warn_user['email'])) {
        $user_email = $warn_user['email'];
        
        $warnHtmlBody = "
            <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
                <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>
                    <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                        <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                            <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='Identity Search AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                            <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; display: inline-block; vertical-align: middle;'>Identity Search <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                        </div>
                    </div>
                    <div style='padding: 24px 20px; text-align: left;'>
                        <h2 style='font-size: 16px; font-weight: 700; color: #D97706; margin-top: 0; margin-bottom: 10px;'>Payment Action Required</h2>
                        <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 12px;'>We attempted to process your subscription payment, but your card was declined or requires additional authorization by the card issuer.</p>
                        <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 0;'>Please verify your card details and ensure sufficient funds are available. If this attempt was for an ongoing plan you no longer wish to maintain, you can manage and cancel your subscription directly from your account dashboard.</p>
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

        $warnSubject = "Notice: Subscription Payment Action Required — Identity Search AI";
        sendTransactionalMail($user_email, $warnSubject, $warnHtmlBody);
        
        echo json_encode(['status' => 'success', 'message' => 'Global payment action verification notice dispatched.']);
    } else {
        echo json_encode(['status' => 'ignored', 'message' => 'No local profile mapping resolved for dynamic alert dispatch.']);
    }
} catch (Exception $warnEx) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $warnEx->getMessage()]);
}
