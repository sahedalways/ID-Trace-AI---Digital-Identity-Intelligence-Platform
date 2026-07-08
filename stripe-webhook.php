<?php
/**
 * Identity Trace AI — Secure Asynchronous Stripe Webhook Operations Core Engine
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

try {
    if (!empty($endpoint_secret) && !empty($sig_header)) {
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
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Signature Validation Intercept Error.']);
            exit;
        }
    }
    
    $event = json_decode($payload, true);
} catch (Exception $sigEx) {
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
    // JOB 6: CRITICAL SYSTEM ENFORCEMENT — DISPUTE / CHARGEBACK LANDED
    // -------------------------------------------------------------------------
    case 'charge.dispute.created':
        include 'webhook_dispute_created.php';
        break;

    default:
        echo json_encode(['status' => 'unhandled', 'message' => 'Event lifecycle bypassed. Code operations skipped.']);
        break;
}
