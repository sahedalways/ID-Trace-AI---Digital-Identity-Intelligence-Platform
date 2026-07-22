<?php
/**
 * Sub-Webhook Module — Job 4: Checkout Phase / Initial Setup Payment Failure Handler
 * File: webhook_payment_failed.php
 */

$stripe_customer_id = $object['customer'] ?? '';

try {
    $u_stmt = $pdo->prepare("SELECT `email` FROM `users` WHERE `stripe_customer_id` = ? LIMIT 1");
    $u_stmt->execute([$stripe_customer_id]);
    $failed_user = $u_stmt->fetch(PDO::FETCH_ASSOC);

    if ($failed_user && !empty($failed_user['email'])) {
        $failed_email = $failed_user['email'];

        $failHtmlBody = "
            <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
                <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>
                    <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                        <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                            <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='Identity Search AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                            <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; display: inline-block; vertical-align: middle;'>Identity Search <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                        </div>
                    </div>
                    <div style='padding: 24px 20px; text-align: left;'>
                        <h2 style='font-size: 16px; font-weight: 700; color: #DC2626; margin-top: 0; margin-bottom: 10px;'>Subscription Unsuccessful</h2>
                        <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 18px;'>Your Subscription checkout attempt or plan modification request was unsuccessful due to payment failure. To subscribe again, please use valid payment method.</p>
                    </div>
                    <div style='padding: 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: center;'>
                        <div style='display: block; margin-bottom: 8px;'>
                            <span style='font-size: 16px; display: inline-block; vertical-align: middle;'>🕵️‍♂️</span>
                        </div>
                        <p style='font-size: 9px; color: #4B5563; font-weight: 500; margin: 0 0 4px 0;'>&copy; 2026 - Identity Search AI</p>
                        <p style='font-size: 9px; color: #4B5563; font-weight: 400; margin: 0;'>
                            <a href='mailto:support@identitysearch.ai' style='color: #128c7e; text-decoration: none;'>support@identitysearch.ai</a>
                        </p>
                    </div>
                </div>
            </div>
        ";

        $failSubject = "Payment Failed — Identity Search AI";
        sendTransactionalMail($failed_email, $failSubject, $failHtmlBody);

        echo json_encode(['status' => 'success', 'message' => 'Checkout checkout failure email alert dispatched.']);
    } else {
        echo json_encode(['status' => 'ignored', 'message' => 'No local profile mapping resolved for system failure alert.']);
    }
} catch (Exception $clearEx) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $clearEx->getMessage()]);
}