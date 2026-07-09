<?php
/**
 * Sub-Webhook Module — Job 1: Successful Recurring Subscription Renewal Handler
 * File: webhook_renewal_success.php
 */

$stripe_subscription_id = $object['subscription'] ?? '';
$stripe_customer_id = $object['customer'] ?? '';
$stripe_invoice_id = $object['id'] ?? null;
$amount_paid = (float)($object['amount_paid'] / 100);

if (empty($stripe_subscription_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required subscription reference metadata.']);
    exit;
}

try {
    $u_stmt = $pdo->prepare("SELECT `id`, `email`, `name`, `cardholder_name`, `country`, `street`, `zip`, `cid`, `plan` FROM `users` WHERE `stripe_subscription_id` = ? LIMIT 1");
    $u_stmt->execute([$stripe_subscription_id]);
    $user = $u_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $u_stmt = $pdo->prepare("SELECT `id`, `email`, `name`, `cardholder_name`, `country`, `street`, `zip`, `cid`, `plan` FROM `users` WHERE `stripe_customer_id` = ? LIMIT 1");
        $u_stmt->execute([$stripe_customer_id]);
        $user = $u_stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($user) {
        $user_id = (int)$user['id'];
        $checkout_email = $user['email'];
        $affiliate_cid = !empty($user['cid']) ? $user['cid'] : null;
        $cardholder_name = !empty($user['cardholder_name']) ? $user['cardholder_name'] : $user['name'];

        $lines = $object['lines']['data'] ?? [];
        $plan_name = 'm1'; 
        if (!empty($lines)) {
            $price_id = $lines[0]['price']['id'] ?? '';
            $p_stmt = $pdo->prepare("SELECT `name`, `credit`, `free_credit` FROM `plans` WHERE `stripe_price_id` = ? LIMIT 1");
            $p_stmt->execute([$price_id]);
            $plan_specs = $p_stmt->fetch(PDO::FETCH_ASSOC);
            if ($plan_specs) {
                $plan_name = $plan_specs['name'];
                $credits_allocated = (int)$plan_specs['credit'] + (int)$plan_specs['free_credit'];
            }
        }

        if (!isset($credits_allocated)) {
            $credits_allocated = 100; 
        }

        $unique_tid = generateWebhookTransactionId();
        $next_renewal_timestamp = null;
        
        $sub_ch = curl_init("https://api.stripe.com/v1/subscriptions/" . $stripe_subscription_id);
        curl_setopt($sub_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($sub_ch, CURLOPT_USERPWD, $api_key . ":");
        $sub_obj = json_decode(curl_exec($sub_ch), true);
        curl_close($sub_ch);

        if (isset($sub_obj['current_period_end'])) {
            $next_renewal_timestamp = (int)$sub_obj['current_period_end'];
        }

        if ($next_renewal_timestamp) {
            $db_validity_date = date('Y-m-d', $next_renewal_timestamp);
        } else {
            $plan_intervals = [
                'm1'   => '+30 days',
                'q3'   => '+90 days',
                'b6'   => '+180 days',
                'y12'  => '+365 days'
            ];
            $extension_period = $plan_intervals[$plan_name] ?? '+30 days';
            $db_validity_date = date('Y-m-d', strtotime($extension_period));
        }

        $pdo->beginTransaction();

        try {
            $tx_query = "INSERT INTO `transactions` (`tid`, `cid`, `stripe_invoice_id`, `uid`, `plan`, `cardholder_name`, `country`, `street`, `zip`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'succeeded', NOW())";
            $pdo->prepare($tx_query)->execute([
                $unique_tid, $affiliate_cid, $stripe_invoice_id, $user_id, $plan_name, $cardholder_name, 
                $user['country'] ?? 'XX', $user['street'] ?? '', $user['zip'] ?? ''
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000 || strpos($e->getMessage(), '1062') !== false) {
                $pdo->rollBack(); 
                echo json_encode(['status' => 'ignored', 'message' => 'Duplicate Invoice Blocked. Handled elsewhere.']);
                exit; 
            }
            throw $e; 
        }

        $pdo->prepare("UPDATE `users` SET `plan` = ?, `stripe_subscription_id` = ?, `validity` = ?, `credit` = `credit` + ? WHERE `id` = ?")
            ->execute([$plan_name, $stripe_subscription_id, $db_validity_date, $credits_allocated, $user_id]);

        $generated_promo_code = generateWebhookPromoCode();
        $promo_query = "INSERT INTO `promo` (`uid`, `email`, `promo_code`, `created_at`) VALUES (?, ?, ?, NOW())";
        $pdo->prepare($promo_query)->execute([$user_id, $checkout_email, $generated_promo_code]);

        if (!empty($affiliate_cid)) {
            $click_stmt = $pdo->prepare("SELECT `affid` FROM `clicks` WHERE `cid` = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci LIMIT 1");
            $click_stmt->execute([$affiliate_cid]);
            $click_data = $click_stmt->fetch(PDO::FETCH_ASSOC);

            if ($click_data) {
                $aff_id = (int)$click_data['affid'];
                $payout_amount = $amount_paid * 0.50;

                $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` + ? WHERE `id` = ?")->execute([$payout_amount, $aff_id]);

                $pdo->prepare("INSERT INTO `recurring` (`tid`, `cid`, `uid`, `affid`, `plan`, `price`, `payout`, `note`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, 'Recurring Subscription Webhook Verified', NOW())")
                    ->execute([$unique_tid, $affiliate_cid, $user_id, $aff_id, $plan_name, $amount_paid, $payout_amount]);
            }
        }

        $pdo->commit();

        $invoice_date = date('M d, Y');
        $formatted_price = '$' . number_format($amount_paid, 2);

        $invoiceHtmlBody = "
            <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
                <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>
                    <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                        <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                            <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='Identity Search AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                            <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; display: inline-block; vertical-align: middle;'>Identity Search <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                        </div>
                    </div>
                    <div style='padding: 24px 20px; text-align: left;'>
                        <h2 style='font-size: 16px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 10px;'>Payment Invoice</h2>
                        <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 18px;'>Your subscription Renewal at Identity Search AI is successfully and your features are fully active.</p>
                        <div style='background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px; margin-bottom: 18px;'>
                            <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Payer Name:</b> " . htmlspecialchars($cardholder_name) . "</div>
                            <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Plan Name:</b> <span style='text-transform: uppercase;'>" . htmlspecialchars($plan_name) . "</span></div>
                            <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Credits:</b> {$credits_allocated} Reports</div>
                            <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Amount:</b> <span style='color: #128c7e; font-weight: 700;'>{$formatted_price}</span></div>
                            <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>TXID:</b> <span style='font-family: monospace; color: #6B7280;'>{$unique_tid}</span></div>
                            <div style='font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Date:</b> {$invoice_date}</div>
                        </div>
                        <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-top: 0; margin-bottom: 0;'>To download payment invoice, please visit your <a href='https://idtrace.ai/my-plan.php' style='color: #128c7e; text-decoration: none; font-weight: 500;'>account dashboard</a>.</p>
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

        sendTransactionalMail($checkout_email, "Your Payment Invoice — {$unique_tid}", $invoiceHtmlBody);
        echo json_encode(['status' => 'success', 'message' => 'Renewal metrics provisioned successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User matching the invoice context parameters was not found.']);
    }
} catch (Exception $dbEx) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $dbEx->getMessage()]);
}
