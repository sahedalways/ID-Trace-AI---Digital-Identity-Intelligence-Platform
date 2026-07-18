<?php
/**
 * Sub-Webhook Module — charge.refunded: Full Refund Chargeback Enforcement Handler
 * File: webhook_charge_refunded.php
 */

$charge_id = $object['id'] ?? '';
$invoice_id = $object['invoice'] ?? '';
$refund_amount = (float)($object['amount_refunded'] / 100);

if (empty($charge_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing charge reference for refund tracking.']);
    exit;
}

try {
    // 1. Resolve transaction by stored stripe_charge_id first, fallback to invoice lookup via Stripe API
    $tx_stmt = $pdo->prepare("SELECT `uid`, `plan`, `cid`, `tid` FROM `transactions` WHERE `stripe_charge_id` = ? AND `status` != 'chargeback' LIMIT 1");
    $tx_stmt->execute([$charge_id]);
    $transaction_record = $tx_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction_record && !empty($invoice_id)) {
        $tx_stmt2 = $pdo->prepare("SELECT `uid`, `plan`, `cid`, `tid` FROM `transactions` WHERE `stripe_invoice_id` = ? AND `status` != 'chargeback' LIMIT 1");
        $tx_stmt2->execute([$invoice_id]);
        $transaction_record = $tx_stmt2->fetch(PDO::FETCH_ASSOC);
    }

    if (!$transaction_record) {
        // Fallback: hit Stripe API to resolve invoice from charge, then re-query
        $ch_inspect = curl_init("https://api.stripe.com/v1/charges/" . $charge_id);
        curl_setopt($ch_inspect, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_inspect, CURLOPT_USERPWD, $api_key . ":");
        $charge_data = json_decode(curl_exec($ch_inspect), true);
        curl_close($ch_inspect);

        $fallback_invoice = $charge_data['invoice'] ?? '';
        if (!empty($fallback_invoice)) {
            $tx_stmt3 = $pdo->prepare("SELECT `uid`, `plan`, `cid`, `tid` FROM `transactions` WHERE `stripe_invoice_id` = ? AND `status` != 'chargeback' LIMIT 1");
            $tx_stmt3->execute([$fallback_invoice]);
            $transaction_record = $tx_stmt3->fetch(PDO::FETCH_ASSOC);
        }
    }

    if ($transaction_record) {
        $offender_uid = (int)$transaction_record['uid'];
        $plan_name = $transaction_record['plan'];
        $affiliate_cid = !empty($transaction_record['cid']) ? $transaction_record['cid'] : null;

        $pdo->beginTransaction();

        // Mark transaction as chargeback
        $pdo->prepare("
            UPDATE `transactions` 
            SET `status` = 'chargeback',
                `dispute_status` = 1, 
                `dispute_reason` = 'charge_refunded', 
                `dispute_amount` = ? 
            WHERE (`stripe_charge_id` = ? OR (`stripe_invoice_id` = ? AND `stripe_charge_id` IS NULL)) AND `status` != 'chargeback'
        ")->execute([$refund_amount, $charge_id, $invoice_id]);

        // Deactivate user account and wipe credits
        $pdo->prepare("
            UPDATE `users` 
            SET `status` = 'inactive', 
                `stripe_subscription_id` = NULL, 
                `plan` = NULL, 
                `credit` = 0, 
                `validity` = NULL 
            WHERE `id` = ?
        ")->execute([$offender_uid]);

        // Revoke affiliate commissions on refund
        if (!empty($affiliate_cid)) {
            $click_stmt = $pdo->prepare("SELECT `affid` FROM `clicks` WHERE `cid` = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci LIMIT 1");
            $click_stmt->execute([$affiliate_cid]);
            $click_data = $click_stmt->fetch(PDO::FETCH_ASSOC);

            if ($click_data) {
                $aff_id = (int)$click_data['affid'];
                $rev_payout_deduction = $refund_amount * 0.50;

                if ($rev_payout_deduction > 0) {
                    $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` - ? WHERE `id` = ?")
                        ->execute([$rev_payout_deduction, $aff_id]);

                    $pdo->prepare("INSERT INTO `conversions` (`tid`, `cid`, `uid`, `affid`, `plan`, `price`, `payout`, `note`, `fire_postback`, `created_at`) VALUES (?, ?, ?, ?, ?, 0.00, ?, 'Refund Revocation — Commission Deducted', 0, NOW())")
                        ->execute(['REF-' . generateWebhookTransactionId(), $affiliate_cid, $offender_uid, $aff_id, $plan_name, -$rev_payout_deduction]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Refund chargeback enforced. User deactivated, commissions reversed.']);
    } else {
        echo json_encode(['status' => 'ignored', 'message' => 'No matching transaction found for charge: ' . $charge_id]);
    }
} catch (Exception $refundEx) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $refundEx->getMessage()]);
}
