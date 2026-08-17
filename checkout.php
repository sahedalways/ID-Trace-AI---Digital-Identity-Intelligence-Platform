<?php

/**
 * OSINT Universal Intelligence Console
 * File: checkout.php
 */

require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['user_logged_in']) ||
    $_SESSION['user_logged_in'] !== true ||
    !isset($_SESSION['user_id'])
) {
    header("Location: signin");
    exit;
}

$user_id   = (int) $_SESSION['user_id'];
$plan_name = isset($_GET['plan']) ? trim($_GET['plan']) : 'm12';
$vid       = isset($_GET['id']) ? trim($_GET['id']) : '';

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function stripeApiCall(
    string $endpoint,
    array $data,
    string $apiKey,
    string $method = 'POST'
): array {
    $url = 'https://api.stripe.com/v1/' . ltrim($endpoint, '/');

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD        => $apiKey . ':',
        CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'User-Agent: IdentitySearchAI/1.0',
        ],
    ]);

    $method = strtoupper($method);

    if ($method === 'GET') {
        // GET query is already included in endpoint.
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
    } else {
        curl_setopt($ch, CURLOPT_POST, true);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
    }

    $raw = curl_exec($ch);

    if ($raw === false) {
        $error = curl_error($ch);
        curl_close($ch);

        throw new Exception('Stripe connection error: ' . $error);
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    $response = json_decode($raw, true);

    if (!is_array($response)) {
        throw new Exception('Invalid response received from Stripe.');
    }

    if ($httpCode >= 400 || isset($response['error'])) {
        $message = $response['error']['message'] ?? 'Stripe API request failed.';
        throw new Exception($message);
    }

    return $response;
}

function getClientIpSafe(): string
{
    if (function_exists('getClientIp')) {
        $ip = getClientIp();

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    $possibleIps = [
        $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '',
        $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '',
        $_SERVER['REMOTE_ADDR'] ?? '',
    ];

    foreach ($possibleIps as $value) {
        if (!$value) {
            continue;
        }

        $ip = trim(explode(',', $value)[0]);

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return '';
}

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

$checkout_email = '';
$saved_country  = '';
$saved_street   = '';
$saved_zip      = '';
$saved_name     = '';
$stripe_customer_id = '';

$userStmt = $pdo->prepare("
    SELECT
        email,
        name,
        cardholder_name,
        country,
        street,
        zip,
        stripe_customer_id
    FROM users
    WHERE id = ?
    LIMIT 1
");

$userStmt->execute([$user_id]);

$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Unable to load user account.');
}

$checkout_email     = trim($user['email'] ?? '');
$saved_name         = trim($user['cardholder_name'] ?? '') ?: trim($user['name'] ?? '');
$saved_country      = strtoupper(trim($user['country'] ?? ''));
$saved_street       = trim($user['street'] ?? '');
$saved_zip          = trim($user['zip'] ?? '');
$stripe_customer_id = trim($user['stripe_customer_id'] ?? '');

/*
|--------------------------------------------------------------------------
| Plan
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM plans
    WHERE name = ?
    LIMIT 1
");

$stmt->execute([$plan_name]);

$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan || empty($plan['stripe_price_id'])) {
    die('Error: Target tier price mapping missing.');
}

$planPrice = (float) $plan['price'];

if ($planPrice <= 0) {
    die('Error: Invalid plan price.');
}

/*
|--------------------------------------------------------------------------
| Country list
|--------------------------------------------------------------------------
*/

$country_matrix = [];

$country_cache_file = __DIR__ . '/cache_countries.json';

if (
    file_exists($country_cache_file) &&
    (time() - filemtime($country_cache_file) < 86400 * 7)
) {
    $country_matrix = json_decode(
        file_get_contents($country_cache_file),
        true
    );
}

if (empty($country_matrix) || !is_array($country_matrix)) {

    $remote_cdn_url =
        'https://cdn.jsdelivr.net/gh/umpirsky/country-list@master/data/en/country.json';

    $ch = curl_init($remote_cdn_url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT      => 'IdentitySearchAI/1.0',
    ]);

    $response = curl_exec($ch);

    curl_close($ch);

    if (!empty($response)) {

        $raw_data = json_decode($response, true);

        if (is_array($raw_data)) {

            foreach ($raw_data as $iso => $name) {
                $iso = strtoupper(trim($iso));

                if (preg_match('/^[A-Z]{2}$/', $iso)) {
                    $country_matrix[$iso] = trim($name);
                }
            }

            asort($country_matrix);

            @file_put_contents(
                $country_cache_file,
                json_encode($country_matrix, JSON_PRETTY_PRINT)
            );
        }
    }
}

if (empty($country_matrix)) {
    $country_matrix = [
        'BD' => 'Bangladesh',
        'US' => 'United States',
        'GB' => 'United Kingdom',
        'CA' => 'Canada',
        'AU' => 'Australia',
    ];
}

/*
|--------------------------------------------------------------------------
| Normalize country
|--------------------------------------------------------------------------
*/

if (
    empty($saved_country) ||
    !isset($country_matrix[$saved_country])
) {
    $cfCountry = strtoupper(
        trim($_SERVER['HTTP_CF_IPCOUNTRY'] ?? '')
    );

    if (
        !empty($cfCountry) &&
        isset($country_matrix[$cfCountry])
    ) {
        $saved_country = $cfCountry;
    } else {
        $saved_country = 'US';
    }
}

/*
|--------------------------------------------------------------------------
| Stripe configuration
|--------------------------------------------------------------------------
|
| IMPORTANT:
| STRIPE_TEST_SECRET_KEY + STRIPE_TEST_PUBLISHABLE_KEY
| must belong to the SAME Stripe account/mode.
|
*/

$api_key = STRIPE_TEST_SECRET_KEY;
$pub_key = STRIPE_TEST_PUBLISHABLE_KEY;

if (empty($api_key) || empty($pub_key)) {
    die('Stripe configuration is missing.');
}

$client_ip = getClientIpSafe();

try {

    /*
    |--------------------------------------------------------------------------
    | Create / Update Stripe Customer
    |--------------------------------------------------------------------------
    */

    if (empty($stripe_customer_id)) {

        $customerPayload = [
            'email' => $checkout_email,
            'name'  => $saved_name,

            'description' => 'Identity Search AI Customer',

            'address' => [
                'line1'       => $saved_street,
                'postal_code' => $saved_zip,
                'country'     => $saved_country,
            ],

            'metadata' => [
                'user_id'    => (string) $user_id,
                'ip_address' => $client_ip,
                'source'     => 'checkout_page',
            ],
        ];

        $customer = stripeApiCall(
            'customers',
            $customerPayload,
            $api_key
        );

        if (empty($customer['id'])) {
            throw new Exception(
                'Stripe customer creation failed.'
            );
        }

        $stripe_customer_id = $customer['id'];

        $updateUser = $pdo->prepare("
            UPDATE users
            SET stripe_customer_id = ?
            WHERE id = ?
        ");

        $updateUser->execute([
            $stripe_customer_id,
            $user_id
        ]);
    } else {

        stripeApiCall(
            'customers/' . rawurlencode($stripe_customer_id),
            [
                'name' => $saved_name,

                'address' => [
                    'line1'       => $saved_street,
                    'postal_code' => $saved_zip,
                    'country'     => $saved_country,
                ],

                'metadata' => [
                    'user_id'    => (string) $user_id,
                    'ip_address' => $client_ip,
                    'source'     => 'checkout_page',
                ],
            ],
            $api_key,
            'POST'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Clean abandoned incomplete subscriptions
    |--------------------------------------------------------------------------
    |
    | We do NOT reuse old incomplete subscriptions.
    | This prevents old PaymentIntent / 3DS state from interfering
    | with the current checkout.
    |
    */

    $subscriptions = stripeApiCall(
        'subscriptions?customer=' .
            rawurlencode($stripe_customer_id) .
            '&status=incomplete&limit=20',
        [],
        $api_key,
        'GET'
    );

    if (!empty($subscriptions['data'])) {

        foreach ($subscriptions['data'] as $oldSubscription) {

            if (empty($oldSubscription['id'])) {
                continue;
            }

            /*
             * Only remove incomplete subscriptions.
             * Active subscriptions are NEVER touched here.
             */
            stripeApiCall(
                'subscriptions/' .
                    rawurlencode($oldSubscription['id']),
                [],
                $api_key,
                'DELETE'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create fresh subscription
    |--------------------------------------------------------------------------
    */

    $subscriptionPayload = [
        'customer' => $stripe_customer_id,

        'items' => [
            [
                'price' => $plan['stripe_price_id'],
            ],
        ],

        /*
         * Subscription is created incomplete.
         * The first invoice PaymentIntent is then confirmed
         * from the frontend.
         */
        'payment_behavior' => 'default_incomplete',

        'payment_settings' => [
            'save_default_payment_method' => 'on_subscription',

            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'automatic',
                ],
            ],
        ],

        'metadata' => [
            'user_id'        => (string) $user_id,
            'plan_name'      => $plan_name,
            'cardholder_name' => $saved_name,
            'street'         => $saved_street,
            'zip'            => $saved_zip,
            'country'        => $saved_country,
            'ip_address'     => $client_ip,
        ],

        'expand' => [
            'latest_invoice.payment_intent',
        ],
    ];

    $subscription = stripeApiCall(
        'subscriptions',
        $subscriptionPayload,
        $api_key
    );

    if (empty($subscription['id'])) {
        throw new Exception(
            'Stripe subscription creation failed.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Get first invoice PaymentIntent
    |--------------------------------------------------------------------------
    */

    $paymentIntent =
        $subscription['latest_invoice']['payment_intent']
        ?? null;

    if (
        !is_array($paymentIntent) ||
        empty($paymentIntent['client_secret'])
    ) {

        /*
         * Re-fetch subscription if expansion did not return PI.
         */

        $subscription = stripeApiCall(
            'subscriptions/' .
                rawurlencode($subscription['id']) .
                '?expand[]=latest_invoice.payment_intent',
            [],
            $api_key,
            'GET'
        );

        $paymentIntent =
            $subscription['latest_invoice']['payment_intent']
            ?? null;
    }

    if (
        !is_array($paymentIntent) ||
        empty($paymentIntent['client_secret'])
    ) {
        throw new Exception(
            'Unable to initialize the subscription payment.'
        );
    }

    $client_secret = $paymentIntent['client_secret'];

    $payment_intent_id = $paymentIntent['id'] ?? '';
} catch (Throwable $e) {

    error_log(
        'Stripe Checkout Error [' . $user_id . ']: ' .
            $e->getMessage()
    );

    die('Unable to initialize payment. Please refresh the page and try again.');
}

/*
|--------------------------------------------------------------------------
| Display
|--------------------------------------------------------------------------
*/

$paid_credit = (int) $plan['credit'];
$free_credit = (int) $plan['free_credit'];

$display_name =
    ($free_credit > 0)
    ? "{$paid_credit} Reports + {$free_credit} Free Pack"
    : "{$paid_credit} Reports Pack";

$billing_intervals = [
    'm3'  => 'monthly',
    'm5'  => 'monthly',
    'm12' => 'monthly',
    'm1'  => 'monthly',
    'q3'  => 'every 3 months',
    'b6'  => 'every 6 months',
    'y12' => 'annually',
];

$billing_cycle_text =
    $billing_intervals[$plan['name']]
    ?? 'every ' . $plan['validity'] . ' days';

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <title>Checkout — Identity Search AI</title>

    <?php include 'head.php'; ?>

    <script src="https://js.stripe.com/v3/"></script>

    <style>
        body {
            background-color: #f9fafb !important;
            color: #111827 !important;
        }

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

        #stripeCardNumberTarget iframe,
        #stripeCardExpiryTarget iframe,
        #stripeCardCvcTarget iframe {
            pointer-events: auto !important;
            opacity: 1 !important;
            min-height: 20px;
        }

        .stripe-error {
            color: #ef4444;
        }
    </style>

</head>

<body class="min-h-screen flex flex-col bg-slate-50">

    <header id="mainNavbar"
        class="sticky top-0 z-50 bg-transparent transition-all duration-300">

        <?php include 'navbar.php'; ?>

    </header>

    <main class="flex-grow max-w-md w-full mx-auto px-4 py-8 space-y-6">

        <!-- Order Summary -->

        <div class="border border-gray-200 rounded-2xl p-5 bg-white shadow-sm space-y-4">

            <h3 class="text-xs font-semibold text-gray-800 uppercase tracking-wider">
                Order Summary
            </h3>

            <div class="flex justify-between items-start">

                <div>

                    <h4 class="text-base font-bold text-gray-900">
                        <?php echo htmlspecialchars($display_name); ?>
                    </h4>

                    <p class="text-xs font-medium text-gray-400 mt-0.5">
                        Billed <?php echo htmlspecialchars($billing_cycle_text); ?>
                    </p>

                </div>

                <span class="text-base font-bold text-gray-900">
                    $<?php echo number_format($planPrice, 2); ?>
                </span>

            </div>

            <div class="border-t border-gray-100 pt-3 flex justify-between text-sm font-bold text-gray-900">

                <span>Total Due:</span>

                <span>
                    $<?php echo number_format($planPrice, 2); ?>
                </span>

            </div>

        </div>


        <form id="paymentExecutionForm" class="space-y-6">

            <!-- Account -->

            <div class="space-y-3">

                <div class="flex items-center gap-2">

                    <span class="bg-[#0072bc] text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                        1
                    </span>

                    <h3 class="text-base font-bold text-gray-900">
                        Account Identity
                    </h3>

                </div>

                <div class="border border-gray-200 rounded-2xl p-4 bg-white shadow-sm">

                    <input
                        type="email"
                        id="customer_email"
                        readonly
                        required
                        value="<?php echo htmlspecialchars($checkout_email); ?>"
                        class="form-input bg-gray-50 text-gray-400 cursor-not-allowed">

                </div>

            </div>


            <!-- Billing -->

            <div class="space-y-3">

                <div class="flex items-center gap-2">

                    <span class="bg-[#0072bc] text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                        2
                    </span>

                    <h3 class="text-base font-bold text-gray-900">
                        Billing Details
                    </h3>

                </div>

                <div class="border border-gray-200 rounded-2xl p-4 bg-white shadow-sm space-y-4">

                    <div>

                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">
                            Country Jurisdiction *
                        </label>

                        <select
                            id="billing_country"
                            required
                            class="form-input cursor-pointer">

                            <?php foreach ($country_matrix as $iso => $countryName): ?>

                                <option
                                    value="<?php echo htmlspecialchars($iso); ?>"
                                    <?php echo $saved_country === $iso ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($countryName); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <div>

                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">
                            Street Address *
                        </label>

                        <input
                            type="text"
                            id="billing_street"
                            required
                            class="form-input"
                            placeholder="1621 Central Ave"
                            value="<?php echo htmlspecialchars($saved_street); ?>"
                            autocomplete="street-address">

                    </div>


                    <div>

                        <label class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">
                            Postal / ZIP Code *
                        </label>

                        <input
                            type="text"
                            id="billing_zip"
                            required
                            class="form-input"
                            placeholder="82001"
                            value="<?php echo htmlspecialchars($saved_zip); ?>"
                            autocomplete="postal-code">

                    </div>

                </div>

            </div>


            <?php include 'checkout_payment.php'; ?>


            <!-- Terms -->

            <div class="border border-blue-100 bg-blue-50/40 rounded-2xl p-4 text-xs text-gray-600 font-medium leading-relaxed">

                By Subscribing, you authorize Identity Search AI to charge you according to the terms until you cancel.

                You will be charged

                <span class="font-bold text-gray-900">
                    $<?php echo number_format($planPrice, 2); ?>
                </span>

                immediately.

            </div>


            <div class="space-y-3">

                <label
                    for="accept_terms"
                    class="flex items-start justify-center gap-2.5 text-[11px] text-gray-500 font-medium leading-relaxed cursor-pointer">

                    <input
                        type="checkbox"
                        id="accept_terms"
                        required
                        class="mt-0.5 h-4 w-4 accent-[#0072bc]">

                    <span>

                        By purchasing, you agree to our

                        <a href="terms"
                            class="underline text-[#0072bc]">
                            Terms and Conditions
                        </a>

                        and

                        <a href="privacy"
                            class="underline text-[#0072bc]">
                            Privacy Policy
                        </a>.

                    </span>

                </label>


                <button
                    type="submit"
                    id="submitPaymentBtn"
                    class="w-full bg-[#0072bc] hover:bg-[#005ea3] text-white py-4 px-4 rounded-xl text-[15px] font-bold transition flex items-center justify-center gap-2">

                    <span id="btnText">
                        Complete Checkout
                    </span>

                </button>

                <div class="flex items-center justify-center gap-1.5 text-xs font-semibold text-gray-500">

                    <i class="fa-solid fa-shield-halved text-[#0072bc]"></i>

                    30-day money-back guarantee

                </div>

            </div>

        </form>

    </main>

    <?php
    if (file_exists('index_footer.php')) {
        include 'index_footer.php';
    }
    ?>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const navbar = document.getElementById('mainNavbar');

            if (navbar) {

                const handleScroll = () => {

                    if (window.scrollY > 20) {
                        navbar.classList.add('navbar-scrolled');
                    } else {
                        navbar.classList.remove('navbar-scrolled');
                    }

                };

                window.addEventListener(
                    'scroll',
                    handleScroll, {
                        passive: true
                    }
                );

                handleScroll();
            }

        });
    </script>

</body>

</html>