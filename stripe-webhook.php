<?php
/**
 * Identity Search AI — Secure Asynchronous Stripe Webhook Operations Core Engine
 * File: stripe-webhook.php
 */
require_once 'config.php';
require_once 'mailer.php';

header('Content-Type: application/json');

// Retrieve the raw postback signature payload stream from the network buffer
$payload = file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$event = null;

// Your live verified Stripe webhook signing secret key
$endpoint_secret = STRIPE_WEBHOOK_SECRET;

/**
 * Logs Stripe webhook payment failure diagnostic info (no sensitive card data).
 */
function logStripeWebhookFailure($eventType, $details) {
    $logEntry = [
        'timestamp'     => date('c'),
        'event_type'    => $eventType,
        'pi_id'         => $details['pi_id'] ?? '',
        'invoice_id'    => $details['invoice_id'] ?? '',
        'customer_id'   => $details['customer_id'] ?? '',
        'subscription_id'=> $details['subscription_id'] ?? '',
        'amount'        => $details['amount'] ?? '',
        'currency'      => $details['currency'] ?? '',
        'status'        => $details['status'] ?? '',
        'error_code'    => $details['error_code'] ?? '',
        'decline_code'  => $details['decline_code'] ?? '',
        'failure_message'=> $details['failure_message'] ?? '',
        'event_id'      => $details['event_id'] ?? '',
    ];
    error_log("[StripeWebhookFailure] " . json_encode($logEntry));
}

try {
    if (empty($endpoint_secret)) {
        error_log("[StripeWebhook] CRITICAL: STRIPE_WEBHOOK_SECRET is empty — webhook signature verification is disabled. Fix this immediately.");
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Webhook secret not configured.']);
        exit;
    }

    if (empty($sig_header)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing Stripe signature header.']);
        exit;
    }

    $sig_parts = explode(',', $sig_header);
    $timestamp = -1;
    $signatures = [];
    foreach ($sig_parts as $part) {
        $kv = explode('=', $part);
        if (count($kv) === 2) {
            if (trim($kv[0]) === 't') $timestamp = (int)$kv[1];
            if (trim($kv[0]) === 'v1') $signatures[] = trim($kv[1]);
        }
    }
    
    $signed_payload = $timestamp . '.' . $payload;
    $computed_mac = hash_hmac('sha256', $signed_payload, $endpoint_secret);
    
    $valid_signature = false;
    foreach ($signatures as $sig) {
        if (hash_equals($sig, $computed_mac)) {
            $valid_signature = true;
            break;
        }
    }
    
    if (!$valid_signature || (time() - $timestamp > 300)) {
        error_log("[StripeWebhook] Signature validation failed or timestamp expired. IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Signature Validation Intercept Error.']);
        exit;
    }
    
    $event = json_decode($payload, true);
} catch (Exception $sigEx) {
    error_log("[StripeWebhook] Signature verification exception: " . $sigEx->getMessage());
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $sigEx->getMessage()]);
    exit;
}

if (!isset($event['type'])) {
    http_response_code(400);
    exit;
}

/**
 * Global Webhook Helper Utilities (Available within included scope handlers)
 */
function generateWebhookTransactionId() {
    $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < 12; $i++) {
        $random_string .= $pool[random_int(0, strlen($pool) - 1)];
    }
    return 'TX' . $random_string;
}

function generateWebhookPromoCode() {
    $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';
    for ($i = 0; $i < 20; $i++) {
        $code .= $pool[random_int(0, strlen($pool) - 1)];
    }
    return $code;
}

// Route Configuration Context Variables
$eventType = $event['type'];
$object = $event['data']['object'] ?? [];
$billing_reason = $object['billing_reason'] ?? '';
$api_key = STRIPE_TEST_SECRET_KEY;
$webhook_event_id = $event['id'] ?? '';

// Log all incoming webhook events for audit trail
error_log("[StripeWebhook] Received event: type={$eventType}, id={$webhook_event_id}, object_id=" . ($object['id'] ?? 'unknown'));

switch ($eventType) {
    
    // -------------------------------------------------------------------------
    // JOB 1: RENEWAL PAYMENT SUCCESSFUL
    // -------------------------------------------------------------------------
    case 'invoice.payment_succeeded':
        // Only run for automatic subscription cycles. Day-one purchases are ignored here and handled natively by success.php.
        if ($billing_reason === 'subscription_cycle') {
            include 'webhook_renewal_success.php';
        } else {
            echo json_encode(['status' => 'ignored', 'message' => 'Not a recurring cycle renewal. Handled entirely by success.php execution chain.']);
        }
        break;

    // -------------------------------------------------------------------------
    // NEW JOB: ATTEMPT SOFT DECLINED / ACTION REQUIRED (UNCONDITIONAL)
    // -------------------------------------------------------------------------
    case 'invoice.payment_action_required':
        // Processed globally for all checkouts, upgrades, and automated renewals
        include 'webhook_payment_action_required.php';
        break;

    // -------------------------------------------------------------------------
    // JOBS 3 & 4: RENEWAL FAILURE VS CHECKOUT/UPGRADE FAILURE
    // -------------------------------------------------------------------------
    case 'invoice.payment_failed':
        if ($billing_reason === 'subscription_cycle') {
            // JOB 3: True organic recurring cycle decline alert (e.g. insufficient funds)
            include 'webhook_renewal_failed.php';
        } else {
            // JOB 4: Checkout payment failure or initial setup failure triggered from success.php/upgrades
            include 'webhook_payment_failed.php';
        }
        break;

    // -------------------------------------------------------------------------
    // JOB 2: PERMANENT SUBSCRIPTION LIFECYCLE DELETION (CHURN/EXPIRED RETRIES)
    // -------------------------------------------------------------------------
    case 'customer.subscription.deleted':
        // Includes the Option 1 database guard internally to prevent tier-upgrade wiping interference
        include 'webhook_subscription_deleted.php';
        break;

    // -------------------------------------------------------------------------
    // JOB 5: NEW SUBSCRIPTION CREATED (CONFIRMED)
    // -------------------------------------------------------------------------
    case 'customer.subscription.created':
        // Stripe fires this when a subscription transitions to active status.
        // Useful for tracking initial subscription setup from Checkout Sessions.
        echo json_encode(['status' => 'success', 'message' => 'Subscription created event received and acknowledged.']);
        break;

    // -------------------------------------------------------------------------
    // JOB 6: CRITICAL SYSTEM ENFORCEMENT — DISPUTE / CHARGEBACK LANDED
    // -------------------------------------------------------------------------
    case 'charge.dispute.created':
        include 'webhook_dispute_created.php';
        break;

    // -------------------------------------------------------------------------
    // JOB 7: CHARGE REFUNDED — FULL REFUND CHARGEBACK ENFORCEMENT
    // -------------------------------------------------------------------------
    case 'charge.refunded':
        include 'webhook_charge_refunded.php';
        break;

    // -------------------------------------------------------------------------
    // JOB 8: DISPUTE CLOSED — FINAL RESOLUTION (WON OR LOST)
    // -------------------------------------------------------------------------
    case 'charge.dispute.closed':
        include 'webhook_dispute_closed.php';
        break;

    // -------------------------------------------------------------------------
    // JOB 9: INDIVIDUAL REFUND CREATED — REFUND CHARGEBACK ENFORCEMENT
    // -------------------------------------------------------------------------
    case 'refund.created':
        include 'webhook_refund_created.php';
        break;

    default:
        echo json_encode(['status' => 'unhandled', 'message' => 'Event lifecycle bypassed. Code operations skipped.']);
        break;
}