<?php
/**
 * OSINT Universal Intelligence Console — Instant Subscription Termination Controller
 * File: cancel-subscription.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Gateway: Enforce authentication constraints before processing data mutations
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || !isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "signin");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$api_key = STRIPE_TEST_SECRET_KEY;

try {
    // Fetch the user's active stripe subscription tracking marker
    $stmt = $pdo->prepare("SELECT `stripe_subscription_id` FROM `users` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || empty($user['stripe_subscription_id'])) {
        throw new Exception("No active recurring subscription was found linked to your profile container.");
    }

    $sub_id = $user['stripe_subscription_id'];

    // 1. STRIPE OPERATION: Kill the subscription instantly via API DELETE request
    // This action triggers Stripe to asynchronously fire 'customer.subscription.deleted' straight to your webhook
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/subscriptions/" . $sub_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_USERPWD, $api_key . ":");
    
    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($httpStatus < 200 || $httpStatus >= 300) {
        $errorMsg = isset($result['error']['message']) ? $result['error']['message'] : 'Stripe API Error';
        throw new Exception($errorMsg);
    }

    // Redirect back to the dashboard. The webhook handler module will update the local DB and send the confirmation email instantly.
    header("Location: " . BASE_URL . "my-plan?msg=" . urlencode("Subscription cancellation request processed. Access parameters are updating asynchronously."));
    exit;

} catch (Exception $e) {
    // Gracefully route back to dashboard on exception and show error feedback
    header("Location: " . BASE_URL . "my-plan?error=" . urlencode($e->getMessage()));
    exit;
}