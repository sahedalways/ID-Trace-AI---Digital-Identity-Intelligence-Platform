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
    header("Location: signin");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$plan_name = isset($_GET['plan']) ? trim($_GET['plan']) : 'm12';
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

// 4. Normalize billing country so Stripe never receives an invalid/empty ISO code.
//    Invalid placeholders (e.g. 'XX') or missing values fall back to the visitor's
//    Cloudflare country, then to a safe default, so the country dropdown always
//    has a valid pre-selection and the Stripe customer record stays clean.
if (empty($saved_country) || !isset($country_matrix[$saved_country])) {
    $saved_country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY'])) : '';
    if (empty($saved_country) || !isset($country_matrix[$saved_country])) {
        $saved_country = 'US';
    }
}

// 5. Handle Stripe Cloud Handshake with Advanced Redundancy Cleanup and Profile Generation
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

$client_ip = getClientIp();

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
            ],
            'metadata' => [
                'user_id'     => (string)$user_id,
                'ip_address'  => $client_ip,
                'source'      => 'checkout_page',
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
            ],
            'metadata' => [
                'user_id'     => (string)$user_id,
                'ip_address'  => $client_ip,
                'source'      => 'checkout_page',
            ]
        ], $api_key);
    }

    // CLEANUP: Only cancel stale incomplete subscriptions that are truly abandoned
    // (requires_payment_method = card failed/no card attached). Do NOT cancel in-flight
    // requires_action subscriptions (e.g. 3DS pending) as that would break a legitimate
    // checkout attempt in progress.
    $sub_res = null;
    $existing_subs = stripeCoreCall("subscriptions?customer=" . $stripe_customer_id . "&status=incomplete", [], $api_key, 'GET');
    if (isset($existing_subs['data']) && is_array($existing_subs['data'])) {
        foreach ($existing_subs['data'] as $candidate_sub) {
            $candidate_price = $candidate_sub['items']['data'][0]['price']['id'] ?? '';
            if ($candidate_price !== $plan['stripe_price_id']) {
                // Wrong plan — safe to cancel
                stripeCoreCall("subscriptions/" . $candidate_sub['id'], [], $api_key, 'DELETE');
                continue;
            }
            $expand_sub = stripeCoreCall("subscriptions/" . $candidate_sub['id'] . "?expand[0]=latest_invoice.payment_intent", [], $api_key, 'GET');
            $pi_status = $expand_sub['latest_invoice']['payment_intent']['status'] ?? '';
            if ($pi_status === 'requires_action') {
                // In-flight 3DS or authentication — reuse this one, don't cancel
                $sub_res = $expand_sub;
                break;
            }
            if ($pi_status === 'requires_payment_method' || $pi_status === 'requires_confirmation') {
                // Truly failed / no card — safe to cancel
                stripeCoreCall("subscriptions/" . $candidate_sub['id'], [], $api_key, 'DELETE');
                continue;
            }
            // For any other status, don't cancel — leave it alone
        }
    }

    if (!$sub_res) {
        $sub_res = stripeCoreCall('subscriptions', [
            'customer' => $stripe_customer_id,
            'items' => [['price' => $plan['stripe_price_id']]],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => [
                'save_default_payment_method' => 'on_subscription',
                'statement_descriptor_suffix' => 'IDENTITYSEARCH.AI',
            ],
            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'automatic',
                ],
            ],
            'metadata' => [
                'cardholder_name' => $saved_name,
                'street'          => $saved_street,
                'zip'             => $saved_zip,
                'country'         => $saved_country,
                'user_id'         => (string)$user_id,
                'ip_address'      => $client_ip,
                'plan_name'       => $plan_name,
            ],
            'expand' => ['latest_invoice.payment_intent']
        ], $api_key);
    }

    // Safely extract client_secret from subscription response
    if (isset($sub_res['latest_invoice']) && isset($sub_res['latest_invoice']['payment_intent'])) {
        $client_secret = $sub_res['latest_invoice']['payment_intent']['client_secret'] ?? '';
    } else {
        // Fallback: try to find client_secret from the raw response
        $client_secret = '';
        if (isset($sub_res['id'])) {
            $pi_ch = curl_init("https://api.stripe.com/v1/payment_intents?subscription=" . $sub_res['id']);
            curl_setopt($pi_ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($pi_ch, CURLOPT_USERPWD, $api_key . ":");
            $pi_res = json_decode(curl_exec($pi_ch), true);
            curl_close($pi_ch);
            if (isset($pi_res['data']) && is_array($pi_res['data']) && count($pi_res['data']) > 0) {
                $client_secret = $pi_res['data'][0]['client_secret'] ?? '';
            }
        }
    }
} catch (Exception $e) {
    die("Stripe Engine Exception: " . $e->getMessage());
}

$paid_credit = (int)$plan['credit'];
$free_credit = (int)$plan['free_credit'];
$display_name = ($free_credit > 0) ? "{$paid_credit} Reports + {$free_credit} Free Pack" : "{$paid_credit} Reports Pack";

$billing_intervals = [
    'm3' => 'monthly',
    'm5' => 'monthly',
    'm12' => 'monthly',
    'm1' => 'monthly',
    'q3' => 'every 3 months',
    'b6' => 'every 6 months',
    'y12' => 'annually',
];
$billing_cycle_text = $billing_intervals[$plan['name']] ?? 'every ' . $plan['validity'] . ' days';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout — Identity Search AI</title>
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
            border-color: #0072bc;
            box-shadow: 0 0 0 1px #0072bc;
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
            border-color: #0072bc;
            box-shadow: 0 0 0 1px #0072bc;
        }
        .method-tab-btn {
            background: #ffffff;
            border: 2px solid #e5e7eb;
            transition: all 0.2s ease;
        }
        .method-tab-btn.active {
            border-color: #0072bc;
            background: #f0fdfa;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-[#0072bc] selection:text-white bg-slate-50 relative">

    <!-- Sticky Glassmorphic Navbar Container -->
    <header id="mainNavbar" class="sticky top-0 z-50 bg-transparent transition-all duration-300">
        <?php include 'navbar.php'; ?>
    </header>

    <!-- Full-width Background Decorations -->
    <div class="absolute inset-x-0 top-0 -z-10 overflow-hidden" style="min-height: 100vh; background: linear-gradient(180deg, #BFE4FD 0%, #FFFFFF 100%);">
        <div class="blob-1 absolute top-0 left-1/2 w-[900px] h-[900px] bg-[#0072bc]/10 rounded-full blur-3xl opacity-60 -translate-x-1/2 will-change-transform"></div>
        <div class="blob-2 absolute top-24 -left-20 w-96 h-96 bg-[#0072bc]/15 rounded-full blur-3xl will-change-transform"></div>
        <div class="blob-3 absolute bottom-0 right-0 w-96 h-96 bg-[#BFE4FD]/50 rounded-full blur-3xl opacity-70 will-change-transform"></div>
    </div>

    <style>
        @keyframes blobMove1 { 0%, 100% { transform: translateX(-50%) translateY(0); } 25% { transform: translateX(-40%) translateY(-20px); } 50% { transform: translateX(-50%) translateY(-10px); } 75% { transform: translateX(-60%) translateY(10px); } }
        @keyframes blobMove2 { 0%, 100% { transform: translate(0, 0); } 33% { transform: translate(30px, -15px); } 66% { transform: translate(-20px, 20px); } }
        @keyframes blobMove3 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(-40px, -20px); } }
        .blob-1 { animation: blobMove1 18s ease-in-out infinite; }
        .blob-2 { animation: blobMove2 14s ease-in-out infinite; }
        .blob-3 { animation: blobMove3 16s ease-in-out infinite; }
        .navbar-scrolled { background: linear-gradient(to right, #eef5ff 20%, #0072bc 100%); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); }
        #mainNavbar #siteNavbar { background: transparent !important; border-bottom: none !important; box-shadow: none !important; }
        #siteNavbar a, #siteNavbar button, #siteNavbar .text-black, #siteNavbar .text-slate-400, #siteNavbar .text-\[\#0072bc\] { transition: color 0.35s ease, background-color 0.35s ease, border-color 0.35s ease; }
        #mainNavbar.navbar-scrolled #siteNavbar a:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:not(#userDropdownMenu button, #mobileDrawer button),
        #mainNavbar.navbar-scrolled #siteNavbar .text-black:not(#userDropdownMenu, #mobileDrawer),
        #mainNavbar.navbar-scrolled #siteNavbar .text-slate-400:not(#userDropdownMenu .text-slate-400, #mobileDrawer .text-slate-400),
        #mainNavbar.navbar-scrolled #siteNavbar .text-\[\#0072bc\]:not(#userDropdownMenu .text-\[\#0072bc\], #mobileDrawer .text-\[\#0072bc\]) { color: #ffffff !important; }
        #mainNavbar.navbar-scrolled #siteNavbar a:hover:not(#userDropdownMenu a, #mobileDrawer a),
        #mainNavbar.navbar-scrolled #siteNavbar button:hover:not(#userDropdownMenu button, #mobileDrawer button, #mobileMenuButton) { color: #ffffff !important; }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton { color: #ffffff !important; border-color: rgba(255, 255, 255, 0.25); }
        #mainNavbar.navbar-scrolled #siteNavbar #mobileMenuButton:hover,
        #mainNavbar.navbar-scrolled #siteNavbar #userMenuButton:hover { background-color: rgba(255, 255, 255, 0.15); color: #ffffff !important; }
    </style>

    <main class="flex-grow max-w-md w-full mx-auto px-4 py-8 space-y-6">

        <div class="border border-gray-200 rounded-2xl p-5 bg-white shadow-sm space-y-4 text-left">
            <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">Order Summary</h3>
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
                    <span class="bg-[#0072bc] text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">1</span>
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
                    <span class="bg-[#0072bc] text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">2</span>
                    <h3 class="text-base font-bold text-gray-900 tracking-tight">Billing Details</h3>
                </div>
                <div class="border border-gray-200 rounded-2xl p-4 bg-white shadow-sm space-y-4">
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Cardholder Name *</label>
                        <input type="text" id="cardholder_name" required class="form-input" placeholder="Name on card" value="<?php echo htmlspecialchars($saved_name); ?>" autocomplete="off">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">Country Jurisdiction *</label>
                        <select id="billing_country" required class="form-input cursor-pointer">
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

            <div class="border border-blue-100 bg-blue-50/40 rounded-2xl p-4 text-xs text-gray-600 font-medium leading-relaxed border-dashed">
                By Subscribing, you authorize Identity Search AI to charge you according to the terms until you cancel. You will be charged <span class="font-bold text-gray-900">$<?php echo number_format($plan['price'], 2); ?></span> immediately to unlock full account access logs.
            </div>

            <div class="space-y-3">
                <label for="accept_terms" class="flex items-start justify-center gap-2.5 text-[11px] text-gray-500 font-medium leading-relaxed cursor-pointer">
                    <input type="checkbox" id="accept_terms" name="accept_terms" required class="mt-0.5 h-4 w-4 accent-[#0072bc] cursor-pointer">
                    <span class="text-left">By purchasing, you agree to our
                        <a href="terms" class="underline text-[#0072bc] hover:text-[#005ea3] transition">Terms and Conditions</a>
                        and
                        <a href="privacy" class="underline text-[#0072bc] hover:text-[#005ea3] transition">Privacy Policy</a>.
                    </span>
                </label>
                <button type="submit" id="submitPaymentBtn" class="w-full bg-[#0072bc] hover:bg-[#005ea3] text-white py-4 px-4 rounded-xl text-[15px] font-bold transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <span id="btnText">Complete Checkout</span>
                </button>
                <div class="flex items-center justify-center gap-1.5 text-xs font-semibold text-gray-500">
                    <i class="fa-solid fa-shield-halved text-xs text-[#0072bc]"></i> 30-day money-back guarantee
                </div>
            </div>
        </form>
    </main>

    <?php if (file_exists('index_footer.php')) { include 'index_footer.php'; } ?>


    <script>
        // NAVBAR SCROLL GLASS EFFECT
        document.addEventListener("DOMContentLoaded", () => {
            const navbar = document.getElementById("mainNavbar");
            if (!navbar) return;
            const handleScroll = () => {
                if (window.scrollY > 20) {
                    navbar.classList.add("navbar-scrolled");
                } else {
                    navbar.classList.remove("navbar-scrolled");
                }
            };
            window.addEventListener("scroll", handleScroll, { passive: true });
            handleScroll();
        });
    </script></body>
</html>
