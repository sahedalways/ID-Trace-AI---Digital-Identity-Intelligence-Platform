<?php
/**
 * Standalone Stripe Integration Diagnostic Tool
 * File: test.php
 */
require_once 'config.php';

header('Content-Type: text/plain; charset=utf-8');
echo "==================================================\n";
echo "       STRIPE LIVE API DIAGNOSTIC TERMINAL        \n";
echo "==================================================\n\n";

// 1. Verify Configuration Environment Constants
if (!defined('STRIPE_TEST_SECRET_KEY')) {
    die("CRITICAL ERROR: 'STRIPE_TEST_SECRET_KEY' constant is not defined in config.php.\n");
}

$api_key = STRIPE_TEST_SECRET_KEY;
echo "[✓] Environment Loaded.\n";
echo "[i] Using Key: " . substr($api_key, 0, 7) . "..." . substr($api_key, -4) . "\n\n";

// 2. Define Diagnostic Target Parameters
$target_customer     = 'cus_UpWZIMY7cuBJEw';
$target_subscription = 'sub_1TprWJP5kfHpYd1vFxZO91lP';

/**
 * Executes a debug cURL request down to Stripe API infrastructure
 */
function runStripeDiagnosticCall($endpoint, $apiKey) {
    echo "--------------------------------------------------\n";
    echo "GET Request: https://api.stripe.com/v1/{$endpoint}\n";
    echo "--------------------------------------------------\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/" . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $apiKey
    ]);
    
    // Safety fallback settings to catch routing drops
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_error($ch); // Read implicit error states if networking fails
    curl_close($ch);

    echo "HTTP Network Status: {$httpStatus}\n";

    $decoded = json_decode($response, true);
    
    if ($httpStatus === 200) {
        echo "[✓] Request successful.\n";
    } else {
        echo "[X] Stripe API Error Response Generated.\n";
    }
    
    return $decoded;
}

// 3. Execution Step A: Attempt to pull Subscription Data with explicit array index expansions
echo "Executing Step A: Subscription Profile Retrieval...\n";
$sub_response = runStripeDiagnosticCall("subscriptions/" . $target_subscription . "?expand[0]=default_payment_method", $api_key);

echo "\nSubscription Response Payload Dump:\n";
print_r($sub_response);
echo "\n\n";

// 4. Execution Step B: Attempt to pull Customer Profile Data as fallback validation
echo "Executing Step B: Customer Profile Backup Retrieval...\n";
$cust_response = runStripeDiagnosticCall("customers/" . $target_customer . "?expand[0]=default_payment_method", $api_key);

echo "\nCustomer Response Payload Dump:\n";
print_r($cust_response);
echo "\n==================================================\n";
echo "               End of Diagnostic Run              \n";
echo "==================================================\n";