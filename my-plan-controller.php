<?php
/**
 * OSINT Universal Intelligence Console — Subscription Engine Controller
 * File: my-plan-controller.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Gateway: Enforce authentication constraints before granting dashboard access
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "signin");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
// Stripe Test Secret Key
$api_key = STRIPE_TEST_SECRET_KEY;

/**
 * Pure PHP cURL engine to communicate securely with Stripe REST Endpoints
 */
function stripeGetRequest($endpoint, $apiKey) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/" . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $apiKey
    ]);
    
    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    if ($httpStatus < 200 || $httpStatus >= 300) {
        return null;
    }
    return $result;
}

try {
    // 1. Fetch current profile state metrics
    $user_stmt = $pdo->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1");
    $user_stmt->execute([$user_id]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        session_destroy();
        header("Location: " . BASE_URL . "signin");
        exit;
    }

    $last_charge_date = 'N/A';
    $next_charge_date = 'N/A';
    $subscription_status = 'inactive';
    $plan_amount = '0.00';
    $plan_frequency = 'N/A';
    $cancel_at_period_end = false;
    $raw_stripe_end_date = null;
    $payment_method_display = 'N/A';

    // 2. LIVE STRIPE RESOLUTION: Pull robust status values directly from Stripe API
    if (!empty($user['stripe_subscription_id'])) {
        // FIXED: Replaced string syntax with clean indexed expansion parameter layout array maps
        $subscription = stripeGetRequest('subscriptions/' . $user['stripe_subscription_id'] . '?expand[0]=default_payment_method', $api_key);
        
        if ($subscription) {
            $cancel_at_period_end = $subscription['cancel_at_period_end'] ?? false;
            
            if ($cancel_at_period_end) {
                $subscription_status = 'cancels soon';
            } else {
                $subscription_status = $subscription['status'];
            }
            
            if (isset($subscription['current_period_start'])) {
                $last_charge_date = date('M d, Y', $subscription['current_period_start']);
            }
            if (isset($subscription['current_period_end'])) {
                $raw_stripe_end_date = $subscription['current_period_end'];
                $next_charge_date = date('M d, Y', $subscription['current_period_end']);
            }
            
            // Resolve expanded card details safely from the payment method array keys
            if (!empty($subscription['default_payment_method']['card'])) {
                $card_meta = $subscription['default_payment_method']['card'];
                $brand_name = ucfirst($card_meta['brand'] ?? 'Card');
                $last4_digits = $card_meta['last4'] ?? '****';
                $payment_method_display = $brand_name . ' ' . $last4_digits;
            } else {
                if (!empty($user['stripe_customer_id'])) {
                    // FIXED: Rebuilt expansion target vectors tracking to use invoice_settings parameters natively
                    $customer_obj = stripeGetRequest('customers/' . $user['stripe_customer_id'] . '?expand[0]=invoice_settings.default_payment_method', $api_key);
                    if (!empty($customer_obj['invoice_settings']['default_payment_method']['card'])) {
                        $card_meta = $customer_obj['invoice_settings']['default_payment_method']['card'];
                        $payment_method_display = ucfirst($card_meta['brand'] ?? 'Card') . ' ' . ($card_meta['last4'] ?? '****');
                    }
                }
            }
            
            // Extract core cost metric details and frequency periods from lines payload
            if (!empty($subscription['items']['data'][0]['price'])) {
                $price_obj = $subscription['items']['data'][0]['price'];
                $plan_amount = number_format($price_obj['unit_amount'] / 100, 2);
                
                $interval = $price_obj['recurring']['interval'] ?? 'month';
                $interval_count = $price_obj['recurring']['interval_count'] ?? 1;
                
                if ($interval === 'month') {
                    if ($interval_count == 3) $plan_frequency = 'Quarterly';
                    elseif ($interval_count == 6) $plan_frequency = 'Biannual';
                    elseif ($interval_count == 1) $plan_frequency = 'Monthly';
                    else $plan_frequency = 'Every ' . $interval_count . ' Months';
                } elseif ($interval === 'year') {
                    $plan_frequency = 'Yearly';
                } else {
                    $plan_frequency = ucfirst($interval);
                }
            }
        }
    }

    if ($last_charge_date === 'N/A' && !empty($user['created_at'])) {
        $last_charge_date = date('M d, Y', strtotime($user['created_at']));
    }

    // 3. Resolve historical transaction array log for the lower table list view
    $tx_stmt = $pdo->prepare("
        SELECT t.*, p.`price` as plan_cost
        FROM `transactions` t
        LEFT JOIN `plans` p ON t.`plan` = p.`name`
        WHERE t.`uid` = ? 
        ORDER BY t.`created_at` DESC
    ");
    $tx_stmt->execute([$user_id]);
    $transactions = $tx_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (\PDOException $e) {
    die("Data Pipeline Exception: " . htmlspecialchars($e->getMessage()));
}