<?php
/**
 * OSINT Universal Intelligence Console — Unified Subscription Terminal
 * File: checkout.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$plan_name = isset($_GET['plan']) ? trim($_GET['plan']) : 'm1';
$vid = isset($_GET['id']) ? trim($_GET['id']) : ''; // Dynamically intercepts context loop flow from view page if active

// 1. Fetch User Profile
$checkout_email = ''; $saved_country = ''; $saved_street = ''; $saved_zip = ''; $saved_name = '';
$u_stmt = $pdo->prepare("SELECT `email`, `name`, `cardholder_name`, `country`, `street`, `zip`, `stripe_customer_id` FROM `users` WHERE `id` = ? LIMIT 1");
$u_stmt->execute([$user_id]);
$ud = $u_stmt->fetch(PDO::FETCH_ASSOC);
if ($ud) {
    $checkout_email = $ud['email'];
    $saved_name     = !empty($ud['cardholder_name']) ? $ud['cardholder_name'] : ($ud['name'] ?? '');
    $saved_country  = strtoupper(trim($ud['country'] ?? ''));
    $saved_street   = $ud['street'] ?? '';
    $saved_zip      = $ud['zip'] ?? '';
}

// 2. Fetch Plan Specifications
$stmt = $pdo->prepare("SELECT * FROM `plans` WHERE `name` = ? LIMIT 1");
$stmt->execute([$plan_name]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$plan || empty($plan['stripe_price_id'])) {
    die("Error: Target tier price mapping missing.");
}

// 3. Open-Source Country List Engine
$country_matrix = [];
$country_cache_file = __DIR__ . '/cache_countries.json';

if (file_exists($country_cache_file) && (time() - filemtime($country_cache_file) < 86400 * 7)) {
    $country_matrix = json_decode(file_get_contents($country_cache_file), true);
}

if (empty($country_matrix) || !is_array($country_matrix)) {
    $remote_cdn_url = 'https://cdn.jsdelivr.net/gh/umpirsky/country-list@master/data/en/country.json';
    $ch = curl_init($remote_cdn_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) OSINT-Console-Agent/1.0');
    $response = curl_exec($ch);
    curl_close($ch);

    if (!empty($response)) {
        $raw_data = json_decode($response, true);
        if (is_array($raw_data) && !empty($raw_data)) {
            $country_matrix = [];
            foreach ($raw_data as $iso => $name) {
                $country_matrix[strtoupper(trim($iso))] = trim($name);
            }
            asort($country_matrix);
            file_put_contents($country_cache_file, json_encode($country_matrix));
        }
    }
}

if (empty($country_matrix) || !is_array($country_matrix)) {
    $country_matrix = ['BD' => 'Bangladesh', 'US' => 'United States', 'GB' => 'United Kingdom', 'CA' => 'Canada', 'AU' => 'Australia'];
}

// 4. Handle Stripe Cloud Handshake with Advanced Redundancy Cleanup and Profile Generation
$api_key = STRIPE_TEST_SECRET_KEY;
$pub_key = STRIPE_TEST_PUBLISHABLE_KEY;
$stripe_customer_id = $ud['stripe_customer_id'] ?? '';

function stripeCoreCall($endpoint, $postData, $apiKey, $customMethod = null) {
    $ch = curl_init("https://api.stripe.com/v1/" . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ":");
    
    if ($customMethod) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $customMethod);
    } else {
        curl_setopt($ch, CURLOPT_POST, true);
    }
    
    if (!empty($postData)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $res;
}

try {
    if (empty($stripe_customer_id)) {
        $customerPayload = [
            'email' => $checkout_email,
            'name' => $saved_name,
            'description' => 'Identity Search Profile',
            'address' => [
                'line1' => $saved_street,
                'postal_code' => $saved_zip,
                'country' => $saved_country
            ]
        ];
        $cust_res = stripeCoreCall('customers', $customerPayload, $api_key);
        $stripe_customer_id = $cust_res['id'] ?? die("Stripe customer creation engine crashed.");
        $pdo->prepare("UPDATE `users` SET `stripe_customer_id` = ? WHERE `id` = ?")->execute([$stripe_customer_id, $user_id]);
    } else {
        stripeCoreCall('customers/' . $stripe_customer_id, [
            'name' => $saved_name,
            'address' => [
                'line1' => $saved_street,
                'postal_code' => $saved_zip,
                'country' => $saved_country
            ]
        ], $api_key);
    }

    $existing_subs = stripeCoreCall("subscriptions?customer=" . $stripe_customer_id . "&status=incomplete", [], $api_key, 'GET');
    if (isset($existing_subs['data']) && is_array($existing_subs['data'])) {
        foreach ($existing_subs['data'] as $old_incomplete_sub) {
            stripeCoreCall("subscriptions/" . $old_incomplete_sub['id'], [], $api_key, 'DELETE');
        }
    }

    $sub_res = stripeCoreCall('subscriptions', [
        'customer' => $stripe_customer_id,
        'items' => [['price' => $plan['stripe_price_id']]],
        'payment_behavior' => 'default_incomplete',
        'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
        'metadata' => [
            'cardholder_name' => $saved_name,
            'street'          => $saved_street,
            'zip'             => $saved_zip,
            'country'         => $saved_country
        ],
        'expand' => ['latest_invoice.payment_intent']
    ], $api_key);

    $client_secret = $sub_res['latest_invoice']['payment_intent']['client_secret'] ?? die("Unable duly to generate verification tokens.");
} catch (Exception $e) {
    die("Stripe Engine Exception: " . $e->getMessage());
}

$paid_credit = (int)$plan['credit'];
$free_credit = (int)$plan['free_credit'];
$display_name = ($free_credit > 0) ? "{$paid_credit} Reports + {$free_credit} Free Pack" : "{$paid_credit} Reports Pack";

$billing_intervals = ['m1' => 'monthly', 'q3' => 'every 3 months', 'b6' => 'every 6 months', 'y12' => 'annually'];
$billing_cycle_text = $billing_intervals[$plan['name']] ?? 'every ' . $plan['validity'] . ' days';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout — Identity Trace AI</title>
    <?php include 'head.php'; ?>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        body { background-color: #f9fafb !important; color: #111827 !important; }
        .form-input {
            width: 100%;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #111827;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: #128c7e;
            box-shadow: 0 0 0 1px #128c7e;
            background-color: #ffffff;
        }
        .stripe-container-input {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            transition: all 0.2s;
        }
        .stripe-container-input--focus {
            border-color: #128c7e;
            box-shadow: 0 0 0 1px #128c7e;
        }
        .method-tab-btn {
            background: #ffffff;
            border: 2px solid #e5e7eb;
            transition: all 0.2s ease;
        }
        .method-tab-btn.active {
            border-color: #128c7e;
            background: #f0fdfa;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#128c7e] selection:text-white">

    <?php include 'navbar.php'; ?>

    <main class="flex-grow max-w-md w-full mx-auto px-4 py-8 space-y-6">

        <div class="border border-gray-200 rounded-2xl p-5 bg-white shadow-sm space-y-4 text-left">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Order Summary</h3>
            <div class="flex justify-between items-start pt-1">
                <div>
                    <h4 class="text-base font-bold text-gray-900 tracking-tight"><?php echo $display_name; ?></h4>
                    <p class="text-xs font-medium text-gray-400 mt-0.5">Billed <?php echo $billing_cycle_text; ?></p>
                </div>
                <span class="text-base font-bold text-gray-900">$<?php echo number_format($plan['price'], 2); ?></span>
            </div>
            <div class="border-t border-gray-100 pt-3 flex justify-between text-sm font-bold text-gray-900">
                <span>Total Due:</span>
                <span>$<?php echo number_format($plan['price'], 2); ?></span>
            </div>
        </div>

        <form id="paymentExecutionForm" class="space-y-6 text-left">
            
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="bg-[#128c7e] text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">1</span>
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">Account Identity</h3>
                </div>
                <div class="border border-gray-200 rounded-2xl p-4 bg-white shadow-sm">
                    <input type="email" id="customer_email" readonly required 
                           value="<?php echo htmlspecialchars($checkout_email); ?>" 
                           class="form-input bg-gray-50 text-gray-400 cursor-not-allowed select-none border-gray-200">
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="bg-[#128c7e] text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">2</span>
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">Billing Details</h3>
                </div>
                <div class="border border-gray-200 rounded-2xl p-4 bg-white shadow-sm space-y-4">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Cardholder Name *</label>
                        <input type="text" id="cardholder_name" required class="form-input" placeholder="Name on card" value="<?php echo htmlspecialchars($saved_name); ?>" autocomplete="off">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Country Jurisdiction *</label>
                        <select id="billing_country" class="form-input cursor-pointer">
                            <option value="">Select Target Country</option>
                            <?php foreach ($country_matrix as $iso_key => $country_name): ?>
                                <option value="<?php echo $iso_key; ?>" <?php echo ($saved_country === $iso_key || (empty($saved_country) && $iso_key === 'BD')) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Street Address *</label>
                        <input type="text" id="billing_street" required class="form-input" placeholder="1621 Central Ave" value="<?php echo htmlspecialchars($saved_street); ?>" autocomplete="off">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Postal / ZIP Code *</label>
                        <input type="text" id="billing_zip" required class="form-input" placeholder="82001" value="<?php echo htmlspecialchars($saved_zip); ?>" autocomplete="off">
                    </div>
                </div>
            </div>

            <?php include 'checkout_payment.php'; ?>

            <div class="border border-emerald-100 bg-emerald-50/40 rounded-2xl p-4 text-xs text-gray-600 font-medium leading-relaxed border-dashed">
                By Subscribing, you authorize Identity Search AI to charge you according to the terms until you cancel. You will be charged <span class="font-bold text-gray-900">$<?php echo number_format($plan['price'], 2); ?></span> immediately to unlock full account access logs.
            </div>

            <div class="space-y-3">
                <button type="submit" id="submitPaymentBtn" class="w-full bg-[#128c7e] hover:bg-[#0e6f64] text-white py-4 px-4 rounded-xl text-[15px] font-bold transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <span id="btnText">Complete Checkout</span>
                </button>
            </div>
        </form>
    </main>

    <footer class="w-full text-center py-6 border-t border-gray-200 bg-white/50 backdrop-blur-sm text-xs font-semibold text-gray-400 mt-12">
        <p>&copy; 2026 Identity Trace AI. All rights reserved.</p>
    </footer>

    <!-- INTERCEPT PAYLOAD EXECUTION TO INJECT VERIFIED BILLING_DETAILS INTO STRIPE FRONTEND OBJECT -->
    <script>
        const stripePublishableKey = "<?php echo $pub_key; ?>";
        const stripeClientSecret   = "<?php echo $client_secret; ?>";
        const planParameterName    = "<?php echo urlencode($plan_name); ?>";
        const targetViewId         = "<?php echo urlencode($vid); ?>";
        const globalBaseUrl        = "<?php echo BASE_URL; ?>";

        document.getElementById('paymentExecutionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitPaymentBtn');
            const btnText = document.getElementById('btnText');
            
            if (submitBtn) {
                submitBtn.disabled = true;
                btnText.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1"></i> Processing...';
            }

            // Gather the latest updated form fields inside the DOM workspace layer
            const nameField    = document.getElementById('cardholder_name').value.trim();
            const emailField   = document.getElementById('customer_email').value.trim();
            const streetField  = document.getElementById('billing_street').value.trim();
            const zipField     = document.getElementById('billing_zip').value.trim();
            const countryField = document.getElementById('billing_country').value;

            const stripeInstance = Stripe(stripePublishableKey);

            // Use the globally exposed elements collection from checkout_payment.php
            // Typically cardElement is declared window.cardElement inside checkout_payment.php
            const activeCardElement = window.cardElement || null; 

            if (!activeCardElement) {
                alert("Payment Form Initialization Error: Card framework context dropped.");
                if (submitBtn) { submitBtn.disabled = false; btnText.textContent = 'Complete Checkout'; }
                return;
            }

            // Execute the secure tokenized handshake mapping structured variables for Stripe Radar AVS verification
            stripeInstance.confirmCardPayment(stripeClientSecret, {
                payment_method: {
                    card: activeCardElement,
                    billing_details: {
                        name: nameField,
                        email: emailField,
                        address: {
                            line1: streetField,
                            postal_code: zipField,
                            country: countryField
                        }
                    }
                }
            }).then(function(result) {
                if (result.error) {
                    // Route back tracking error states gently
                    alert(result.error.message);
                    if (submitBtn) { submitBtn.disabled = false; btnText.textContent = 'Complete Checkout'; }
                } else {
                    if (result.paymentIntent.status === 'succeeded') {
                        // Pass validation values down to success.php to securely update users/transactions tables
                        const landingUrl = globalBaseUrl + "success.php?" + 
                            "payment_intent=" + encodeURIComponent(result.paymentIntent.id) +
                            "&plan=" + encodeURIComponent(planParameterName) +
                            "&id=" + encodeURIComponent(targetViewId) +
                            "&c_name=" + encodeURIComponent(nameField) +
                            "&c_country=" + encodeURIComponent(countryField) +
                            "&c_street=" + encodeURIComponent(streetField) +
                            "&c_zip=" + encodeURIComponent(zipField);
                        
                        window.location.href = landingUrl;
                    }
                }
            });
        });
    </script>
</body>
</html>
