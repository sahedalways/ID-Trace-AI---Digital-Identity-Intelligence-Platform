<?php
/**
 * Identity Trace AI — Independent Daily Subscription Renewal Alert Engine
 * File: cron_renewal_alerts.php
 */
require_once 'config.php';
require_once 'mailer.php';

// Security Gateway: Prevent unauthorized web browsers from hitting this file.
if (php_sapi_name() !== 'cli') {
    $secure_token = 'SecURetRaCeAI_99Xq_Key'; 
    if (!isset($_GET['secret']) || $_GET['secret'] !== $secure_token) {
        http_response_code(403);
        exit(json_encode(['status' => 'error', 'message' => 'Unauthorized automated access intercept.']));
    }
}

try {
    // 1. Calculate the target lookahead date threshold (Exactly 3 days from right now)
    $target_expiry_date = date('Y-m-d', strtotime('+3 days'));
    
    // 2. Fetch users whose plan validity matches that exact target date matrix
    $stmt = $pdo->prepare("
        SELECT `u`.`id`, `u`.`email`, `u`.`name`, `u`.`plan`, `u`.`validity`, `p`.`credit`, `p`.`free_credit`, `p`.`price`
        FROM `users` `u`
        LEFT JOIN `plans` `p` ON `u`.`plan` = `p`.`name`
        WHERE `u`.`validity` = ? 
          AND `u`.`stripe_subscription_id` IS NOT NULL 
          AND `u`.`status` = 'active'
    ");
    $stmt->execute([$target_expiry_date]);
    $upcoming_renewals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $notifications_dispatched = 0;

    // 3. Loop through matching entries and process email transmissions
    foreach ($upcoming_renewals as $user) {
        $user_email = $user['email'];
        $plan_raw = $user['plan'] ?? 'm1';
        $plan_name = strtoupper($plan_raw);
        $expiry_formatted = date('M d, Y', strtotime($user['validity']));
        
        // Calculate credits allocation metrics dynamically from plans table backup specifications
        $credits_allocated = isset($user['credit']) ? ((int)$user['credit'] + (int)$user['free_credit']) : 100;
        $amount_raw = isset($user['price']) ? (float)$user['price'] : 0.00;
        $formatted_price = '$' . number_format($amount_raw, 2);

        // Build your precise universal HTML branding template wrapper matching your identity guidelines
        $alertHtmlBody = "
            <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
                <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>
                    
                    <!-- Universal Branding Header -->
                    <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                        <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                            <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='ID Trace AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                            <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; display: inline-block; vertical-align: middle;'>ID Trace <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                        </div>
                    </div>
                    
                    <!-- Main Body Content -->
                    <div style='padding: 24px 20px; text-align: left;'>
                        <h2 style='font-size: 16px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 10px;'>Upcoming Renewal Reminder</h2>
                        <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 18px;'>It is to info you that your subscription at ID Trace AI will renew within next few days.</p>
                        
                        <!-- Highlighted Summary Matrix -->
                        <div style='background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px; margin-bottom: 18px;'>
                            <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Plan Name:</b> <span style='text-transform: uppercase;'>{$plan_name}</span></div>
                            <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Credits:</b> {$credits_allocated} Reports</div>
                            <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Amount:</b> <span style='color: #128c7e; font-weight: 700;'>{$formatted_price}</span></div>
                            <div style='font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Renewal Date:</b> {$expiry_formatted}</div>
                        </div>

                        <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 18px;'>If you donot want to continue subscription, Please cancel it from you dashboard.</p>
                        
                        <!-- High-Contrast Call To Action Button -->
                        <div style='text-align: center; margin-top: 20px; margin-bottom: 5px;'>
                            <a href='https://idtrace.ai/my-plan.php' style='display: inline-block; background-color: #000000; color: #FFFFFF; font-size: 11px; font-weight: 700; text-decoration: none; padding: 10px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.08);'>Cancel Subscription</a>
                        </div>
                    </div>
                    
                    <!-- Universal Footer -->
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

        $alertSubject = "Notice: Upcoming Subscription Renewal Plan — Identity Trace AI";
        sendTransactionalMail($user_email, $alertSubject, $alertHtmlBody);
        
        $notifications_dispatched++;
    }

    echo json_encode([
        'status' => 'success',
        'target_date' => $target_expiry_date,
        'notifications_sent' => $notifications_dispatched
    ]);

} catch (Exception $e) {
    error_log("Renewal Notification Cron Process Intercept Failure: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
