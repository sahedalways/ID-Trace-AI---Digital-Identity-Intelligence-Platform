<?php
/**
 * Sub-Webhook Module — refund.created: Individual Refund Chargeback Handler
 * File: webhook_refund_created.php
 */

$charge_id = $object['charge'] ?? '';
$refund_id = $object['id'] ?? '';
$refund_amount = (float)($object['amount'] / 100);
$refund_reason = $object['reason'] ?? 'requested_by_customer';

if (empty($charge_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing charge reference for refund.created tracking.']);
    exit;
}

try {
    // 1. Resolve transaction by stored stripe_charge_id first, fallback to invoice lookup via Stripe API
    $tx_stmt = $pdo->prepare("SELECT `uid`, `plan`, `cid`, `tid` FROM `transactions` WHERE `stripe_charge_id` = ? AND `status` != 'chargeback' LIMIT 1");
    $tx_stmt->execute([$charge_id]);
    $transaction_record = $tx_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction_record) {
        $ch_inspect = curl_init("https://api.stripe.com/v1/charges/" . $charge_id);
        curl_setopt($ch_inspect, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_inspect, CURLOPT_USERPWD, $api_key . ":");
        $charge_data = json_decode(curl_exec($ch_inspect), true);
        curl_close($ch_inspect);

        $invoice_id = $charge_data['invoice'] ?? '';
        if (!empty($invoice_id)) {
            $tx_stmt2 = $pdo->prepare("SELECT `uid`, `plan`, `cid`, `tid` FROM `transactions` WHERE `stripe_invoice_id` = ? AND `status` != 'chargeback' LIMIT 1");
            $tx_stmt2->execute([$invoice_id]);
            $transaction_record = $tx_stmt2->fetch(PDO::FETCH_ASSOC);
        }
    }

    if ($transaction_record) {
        $offender_uid = (int)$transaction_record['uid'];
        $plan_name = $transaction_record['plan'];
        $affiliate_cid = !empty($transaction_record['cid']) ? $transaction_record['cid'] : null;

        $pdo->beginTransaction();

        // Mark transaction as chargeback with refund details
        $pdo->prepare("
            UPDATE `transactions` 
            SET `status` = 'chargeback',
                `dispute_status` = 1, 
                `dispute_reason` = ?, 
                `dispute_amount` = ? 
            WHERE `stripe_charge_id` = ? AND `status` != 'chargeback'
        ")->execute(['refund_' . $refund_reason, $refund_amount, $charge_id]);

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

                    $pdo->prepare("INSERT INTO `conversions` (`tid`, `cid`, `uid`, `affid`, `plan`, `price`, `payout`, `note`, `fire_postback`, `created_at`) VALUES (?, ?, ?, ?, ?, 0.00, ?, 'Refund Created — Commission Deducted', 0, NOW())")
                        ->execute(['RFC-' . generateWebhookTransactionId(), $affiliate_cid, $offender_uid, $aff_id, $plan_name, -$rev_payout_deduction]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Refund created chargeback enforced. User deactivated, commissions reversed.']);
    } else {
        echo json_encode(['status' => 'ignored', 'message' => 'No matching transaction found for refund charge: ' . $charge_id]);
    }
} catch (Exception $refundEx) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $refundEx->getMessage()]);
}
