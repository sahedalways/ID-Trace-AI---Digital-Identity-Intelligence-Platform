<?php
/**
 * OSINT Universal Intelligence Console — Verified Success Provisioning Engine
 * File: success.php
 */
require_once 'config.php';
require_once 'mailer.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "signin.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$payment_intent_id = $_GET['payment_intent'] ?? '';
$plan_name         = $_GET['plan'] ?? '';
$vid               = $_GET['id'] ?? '';

// Read billing parameters routed securely from checkout.php
$cardholder_name = $_GET['c_name'] ?? '';
$country         = $_GET['c_country'] ?? '';
$street_address  = $_GET['c_street'] ?? '';
$zip_code        = $_GET['c_zip'] ?? '';

$api_key = STRIPE_TEST_SECRET_KEY;
$error_message = null;

$should_fire_postback = false;
$postback_args = [];

/**
 * Generates a structured 14-character alphanumeric Transaction ID.
 */
function generateUniqueTransactionId() {
    $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < 12; $i++) {
        $random_string .= $pool[random_int(0, strlen($pool) - 1)];
    }
    return 'TX' . $random_string;
}

/**
 * Generates a solid 20-character promo code mixed with numbers and uppercase letters.
 */
function generatePromoCode() {
    $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 20; $i++) {
        $code .= $pool[random_int(0, strlen($pool) - 1)];
    }
    return $code;
}

$unique_tid = generateUniqueTransactionId();

try {
    if (empty($payment_intent_id) || empty($plan_name)) {
        throw new Exception("Missing intent validation signatures. Please contact support if you were charged.");
    }

    // 1. Verify status against Stripe directly
    $ch = curl_init("https://api.stripe.com/v1/payment_intents/" . $payment_intent_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":");
    $stripe_intent = json_decode(curl_exec($ch), true);
    curl_close($ch);

    if (!isset($stripe_intent['status']) || $stripe_intent['status'] !== 'succeeded') {
        throw new Exception("Payment status failed verification checks.");
    }

    // 2. Resolve internal configuration records
    $stmt = $pdo->prepare("SELECT * FROM `plans` WHERE `name` = ? LIMIT 1");
    $stmt->execute([$plan_name]);
    $plan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$plan) {
        throw new Exception("Target plan configuration records could not be resolved.");
    }

    $credits_allocated = (int)$plan['credit'] + (int)$plan['free_credit'];
    $plan_price        = (float)$plan['price'];

    $u_stmt = $pdo->prepare("SELECT `id`, `email`, `stripe_customer_id`, `stripe_subscription_id`, `cid` FROM `users` WHERE `id` = ? LIMIT 1");
    $u_stmt->execute([$user_id]);
    $user_data = $u_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user_data) {
        throw new Exception("User account record context dropped mid-flight.");
    }

    $checkout_email = $user_data['email'] ?? '';
    $affiliate_cid = $user_data['cid'] ?? null;

    $stripe_subscription_id = '';
    $stripe_invoice_id = null;
    $next_renewal_timestamp = null;

    if (isset($stripe_intent['invoice'])) {
        $stripe_invoice_id = $stripe_intent['invoice'];
        
        $inv_ch = curl_init("https://api.stripe.com/v1/invoices/" . $stripe_invoice_id);
        curl_setopt($inv_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($inv_ch, CURLOPT_USERPWD, $api_key . ":");
        $invoice_obj = json_decode(curl_exec($inv_ch), true);
        curl_close($inv_ch);
        $stripe_subscription_id = $invoice_obj['subscription'] ?? '';

        // Fetch current_period_end directly from Stripe Subscription object for chron cycle accuracy
        if (!empty($stripe_subscription_id)) {
            $sub_ch = curl_init("https://api.stripe.com/v1/subscriptions/" . $stripe_subscription_id);
            curl_setopt($sub_ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($sub_ch, CURLOPT_USERPWD, $api_key . ":");
            $sub_obj = json_decode(curl_exec($sub_ch), true);
            curl_close($sub_ch);

            if (isset($sub_obj['current_period_end'])) {
                $next_renewal_timestamp = (int)$sub_obj['current_period_end'];
            }
        }
    }

    // 3. SECURE DATABASE WRITE HOOKS & STRIPE CLEANUP SUBSCRIPTION OPERATIONS
    $pdo->beginTransaction();

    // TARGETED STRIPE OPERATIONS: Instantly terminate any old active subscription found locally
    if (!empty($user_data['stripe_subscription_id']) && $user_data['stripe_subscription_id'] !== $stripe_subscription_id) {
        $ch_del = curl_init("https://api.stripe.com/v1/subscriptions/" . $user_data['stripe_subscription_id']);
        curl_setopt($ch_del, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_del, CURLOPT_USERPWD, $api_key . ":");
        curl_setopt($ch_del, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_exec($ch_del);
        curl_close($ch_del);
    }

    // ADVANCED REDUNDANCY CLEANUP: Scan Stripe Profile for any other lurking active layers to eliminate duplication
    if (!empty($user_data['stripe_customer_id'])) {
        $cust_id = $user_data['stripe_customer_id'];
        $ch_list = curl_init("https://api.stripe.com/v1/subscriptions?customer=" . $cust_id . "&status=active");
        curl_setopt($ch_list, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_list, CURLOPT_USERPWD, $api_key . ":");
        $sub_list = json_decode(curl_exec($ch_list), true);
        curl_close($ch_list);

        if (isset($sub_list['data']) && is_array($sub_list['data'])) {
            foreach ($sub_list['data'] as $active_sub) {
                $found_sub_id = $active_sub['id'];
                if ($found_sub_id !== $stripe_subscription_id) {
                    $ch_cleanup = curl_init("https://api.stripe.com/v1/subscriptions/" . $found_sub_id);
                    curl_setopt($ch_cleanup, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch_cleanup, CURLOPT_USERPWD, $api_key . ":");
                    curl_setopt($ch_cleanup, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    curl_exec($ch_cleanup);
                    curl_close($ch_cleanup);
                }
            }
        }
    }

    // Dynamically calculate final validity string from Stripe API payload data
    if ($next_renewal_timestamp) {
        // Precise date calculated directly from the verified live Stripe webhook context
        $db_validity_date = date('Y-m-d', $next_renewal_timestamp);
    } else {
        // Fallback calculations sequence if network connection issues drop tracking parameters
        $plan_intervals = [
            'm1'   => '+30 days',
            'q3'   => '+90 days',
            'b6'   => '+180 days',
            'y12'  => '+365 days'
        ];
        $extension_period = $plan_intervals[$plan_name] ?? '+30 days';
        $db_validity_date = date('Y-m-d', strtotime($extension_period));
    }

    // Update main users row to persist the billing details natively with Stripe-sourced validity
    $pdo->prepare("UPDATE `users` SET `cardholder_name` = ?, `country` = ?, `street` = ?, `zip` = ?, `plan` = ?, `stripe_subscription_id` = ?, `validity` = ?, `credit` = `credit` + ? WHERE `id` = ?")
        ->execute([$cardholder_name, $country, $street_address, $zip_code, $plan_name, $stripe_subscription_id, $db_validity_date, $credits_allocated, $user_id]);

    // Insert transaction row parameters allocation matrix
    $tx_query = "INSERT INTO `transactions` (`tid`, `cid`, `stripe_invoice_id`, `uid`, `plan`, `cardholder_name`, `country`, `street`, `zip`, `status`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'succeeded', NOW())";
    $pdo->prepare($tx_query)->execute([
        $unique_tid, 
        $affiliate_cid, 
        $stripe_invoice_id, 
        $user_id, 
        $plan_name, 
        $cardholder_name, 
        $country, 
        $street_address, 
        $zip_code
    ]);
    $transaction_id = $pdo->lastInsertId();

    // PROMO MATRIX WRITER GENERATION: Generate 20-digit string and log to promo table
    $generated_promo_code = generatePromoCode();
    $promo_query = "INSERT INTO `promo` (`uid`, `email`, `promo_code`, `created_at`) VALUES (?, ?, ?, NOW())";
    $pdo->prepare($promo_query)->execute([$user_id, $checkout_email, $generated_promo_code]);

    // 4. PROCESS AFFILIATE COMMISSIONS WITH INTEGRATED RELATIONAL AFFID STRUCTS
    if (!empty($affiliate_cid)) {
        $click_stmt = $pdo->prepare("SELECT `affid`, `conversion`, `s1`, `s2` FROM `clicks` WHERE `cid` = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci LIMIT 1");
        $click_stmt->execute([$affiliate_cid]);
        $click_data = $click_stmt->fetch(PDO::FETCH_ASSOC);

        if ($click_data) {
            $aff_id = (int)$click_data['affid'];
            $conversion_status = (int)$click_data['conversion'];
            $payout_amount = $plan_price * 0.50;

            if ($conversion_status === 0) {
                $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` + ? WHERE `id` = ?")->execute([$payout_amount, $aff_id]);
                $pdo->prepare("UPDATE `clicks` SET `conversion` = 1 WHERE `cid` = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci")->execute([$affiliate_cid]);

                $aff_stmt = $pdo->prepare("SELECT `postback_url` FROM `affiliates` WHERE `id` = ? LIMIT 1");
                $aff_stmt->execute([$aff_id]);
                $raw_postback_url = $aff_stmt->fetchColumn();

                if (!empty($raw_postback_url)) {
                    $should_fire_postback = true;
                    $postback_args = [
                        'raw_url' => $raw_postback_url,
                        's1'      => $click_data['s1'] ?? '',
                        's2'      => $click_data['s2'] ?? '',
                        'price'   => $plan_price,
                        'payout'  => $payout_amount,
                        'cid'     => $affiliate_cid,
                        'tid'     => $unique_tid
                    ];
                }

                $ins_conv_stmt = $pdo->prepare("INSERT INTO `conversions` (`tid`, `cid`, `uid`, `affid`, `plan`, `price`, `payout`, `note`, `fire_postback`, `postback_url`, `response_code`, `postback_log`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, 'Conversion Verified', 0, NULL, NULL, 'Pending Dispatch Process', NOW())");
                $ins_conv_stmt->execute([$unique_tid, $affiliate_cid, $user_id, $aff_id, $plan_name, $plan_price, $payout_amount]);
                $conversion_row_id = $pdo->lastInsertId();
            } else {
                $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` + ? WHERE `id` = ?")->execute([$payout_amount, $aff_id]);
                
                $pdo->prepare("INSERT INTO `recurring` (`tid`, `cid`, `uid`, `affid`, `plan`, `price`, `payout`, `note`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, 'Recurring Verified', NOW())")
                    ->execute([$unique_tid, $affiliate_cid, $user_id, $aff_id, $plan_name, $plan_price, $payout_amount]);
            }
        }
    }

    $pdo->commit();

    // 5. TRANSACTIONAL INVOICE DISPATCH HOOK 
    include 'email_invoice.php';

    // 6. SECURE OUT-OF-LOCK POSTBACK ROUTING TRANSMISSION
    if ($should_fire_postback && isset($conversion_row_id)) {
        $search_macros = ['[s1]', '[s2]', '[price]', '[payout]', '[cid]', '[tid]'];
        $replace_data  = [
            $postback_args['s1'],
            $postback_args['s2'],
            number_format($postback_args['price'], 2, '.', ''),
            number_format($postback_args['payout'], 2, '.', ''),
            $postback_args['cid'],
            $postback_args['tid']
        ];
        $final_postback_url = str_replace($search_macros, $replace_data, $postback_args['raw_url']);

        $pb_ch = curl_init();
        curl_setopt($pb_ch, CURLOPT_URL, $final_postback_url);
        curl_setopt($pb_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($pb_ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($pb_ch, CURLOPT_TIMEOUT, 6); 
        curl_setopt($pb_ch, CURLOPT_USERAGENT, 'IdentityTrace-PostbackEngine/1.2 (Live Hook Layer)');
        
        $postback_log = curl_exec($pb_ch);
        $response_code = (int)curl_getinfo($pb_ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($pb_ch)) {
            $postback_log = "cURL Failure Error String: " . curl_error($pb_ch);
        }
        curl_close($pb_ch);

        $up_pb_stmt = $pdo->prepare("UPDATE `conversions` SET `fire_postback` = 1, `postback_url` = ?, `response_code` = ?, `postback_log` = ? WHERE `id` = ?");
        $up_pb_stmt->execute([$final_postback_url, $response_code, $postback_log, $conversion_row_id]);
    }

    $_SESSION['last_purchase'] = ['tx_id' => $transaction_id, 'tid' => $unique_tid, 'plan' => $plan_name, 'credits' => $credits_allocated, 'price' => $plan_price, 'promo_code' => $generated_promo_code];

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) { 
        $pdo->rollBack(); 
    }
    $error_message = $e->getMessage();
}

$hasTargetVid = !empty($vid);
$redirect_url = $hasTargetVid ? BASE_URL . "view.php?id=" . urlencode($vid) : BASE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $error_message ? 'Payment Failed' : 'Payment Successful'; ?> — Identity Trace AI</title>
    <?php include 'head.php'; ?>
    <style>
        body { background-color: #f9fafb !important; color: #111827 !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white">

    <?php include 'navbar.php'; ?>

    <main class="max-w-md w-full mx-auto px-4 py-12 flex-grow flex items-center justify-center">
        
        <?php if ($error_message): ?>
            <div class="w-full bg-white border border-gray-200 rounded-3xl p-8 text-center space-y-6 shadow-sm">
                <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center mx-auto border border-red-100 text-2xl">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="space-y-1.5">
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Verification Failed</h2>
                    <p class="text-xs font-semibold text-red-600 leading-relaxed px-2"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
                <div class="pt-2">
                    <a href="<?php echo BASE_URL; ?>buy-credit.php" class="block w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white py-4 rounded-xl font-bold text-sm transition shadow-sm cursor-pointer">
                        Try Again
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="w-full bg-white border border-gray-200 rounded-3xl p-8 text-center space-y-6 shadow-sm">
                <div class="w-16 h-16 bg-emerald-50 text-[#128c7e] rounded-2xl flex items-center justify-center mx-auto border border-emerald-100 text-2xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="space-y-1.5">
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Payment Completed</h2>
                    <p class="text-xs font-semibold text-gray-500 leading-relaxed px-4">Your subscription is active and credits have been successfully assigned to your workspace profile.</p>
                </div>
                
                <div class="pt-2">
                    <?php if ($hasTargetVid): ?>
                        <button disabled class="w-full bg-[#128c7e] text-white py-4 rounded-xl font-bold text-sm transition shadow-sm opacity-70 cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fa-solid fa-spinner animate-spin"></i> Redirecting...
                        </button>
                    <?php else: ?>
                        <a href="<?php echo $redirect_url; ?>" class="block w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white py-4 rounded-xl font-bold text-sm transition shadow-sm cursor-pointer">
                            Go to Homepage
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <footer class="w-full text-center py-6 border-t border-gray-200 bg-white/50 backdrop-blur-sm text-xs font-semibold text-gray-400 mt-12">
        <p>&copy; 2026 Identity Trace AI. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.history && window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            <?php if (!$error_message && $hasTargetVid): ?>
                setTimeout(function() {
                    window.location.href = "<?php echo $redirect_url; ?>";
                }, 3000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
