<?php
/**
 * Sub-Webhook Module — Job 6: System Enforcement Dispute & Chargeback Mitigation Handler
 * File: webhook_dispute_created.php
 */

$charge_id = $object['charge'] ?? '';
$dispute_reason = $object['reason'] ?? 'unrecognized';
$dispute_amount = (float)($object['amount'] / 100);

if (empty($charge_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing disputed metric tracking signatures.']);
    exit;
}

try {
    // 1. Hit the Stripe API endpoints directly to resolve the parent invoice configuration reference
    $ch_inspect = curl_init("https://api.stripe.com/v1/charges/" . $charge_id);
    curl_setopt($ch_inspect, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_inspect, CURLOPT_USERPWD, $api_key . ":");
    $charge_data = json_decode(curl_exec($ch_inspect), true);
    curl_close($ch_inspect);

    $stripe_invoice_id = $charge_data['invoice'] ?? '';

    if (empty($stripe_invoice_id)) {
        echo json_encode(['status' => 'ignored', 'message' => 'Dispute not linked to a recurring subscription invoice tracker.']);
        exit;
    }

    // 2. Locate the historical matching profile sequence inside transactions context mapping
    $tx_stmt = $pdo->prepare("SELECT `uid`, `plan`, `cid` FROM `transactions` WHERE `stripe_invoice_id` = ? LIMIT 1");
    $tx_stmt->execute([$stripe_invoice_id]);
    $transaction_record = $tx_stmt->fetch(PDO::FETCH_ASSOC);

    if ($transaction_record) {
        $offender_uid = (int)$transaction_record['uid'];
        $plan_name = $transaction_record['plan'];
        $affiliate_cid = !empty($transaction_record['cid']) ? $transaction_record['cid'] : null;

        $pdo->beginTransaction();

        // Mitigation Step 1: Wipe profile tokens, lock out active tiers, and flip account status to inactive
        $pdo->prepare("
            UPDATE `users` 
            SET `status` = 'inactive', 
                `stripe_subscription_id` = NULL, 
                `plan` = NULL, 
                `credit` = 0, 
                `validity` = NULL 
            WHERE `id` = ?
        ")->execute([$offender_uid]);

        // Mitigation Step 2: Push systemic dispute logs, record indicators, exact pricing data, and flag chargeback
        $pdo->prepare("
            UPDATE `transactions` 
            SET `status` = 'chargeback',
                `dispute_status` = 1, 
                `dispute_reason` = ?, 
                `dispute_amount` = ? 
            WHERE `stripe_invoice_id` = ?
        ")->execute([$dispute_reason, $dispute_amount, $stripe_invoice_id]);

        // Mitigation Step 3: Revoke affiliate parameters and perform direct ledger balance clawbacks
        if (!empty($affiliate_cid)) {
            $click_stmt = $pdo->prepare("SELECT `affid` FROM `clicks` WHERE `cid` = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci LIMIT 1");
            $click_stmt->execute([$affiliate_cid]);
            $click_data = $click_stmt->fetch(PDO::FETCH_ASSOC);

            if ($click_data) {
                $aff_id = (int)$click_data['affid'];
                
                // Deducting 50% commission value calculated against the disputed charge threshold
                $rev_payout_deduction = $dispute_amount * 0.50;

                if ($rev_payout_deduction > 0) {
                    // Pull specific commission funds out of affiliate ledger parameters
                    $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` - ? WHERE `id` = ?")
                        ->execute([$rev_payout_deduction, $aff_id]);

                    // Inject an immutable correction layer tracker entry straight into conversions log tracking matrices
                    $pdo->prepare("INSERT INTO `conversions` (`tid`, `cid`, `uid`, `affid`, `plan`, `price`, `payout`, `note`, `fire_postback`, `created_at`) VALUES (?, ?, ?, ?, ?, 0.00, ?, 'Chargeback Revocation — Commission Deducted', 0, NOW())")
                        ->execute(['REV-' . generateWebhookTransactionId(), $affiliate_cid, $offender_uid, $aff_id, $plan_name, -$rev_payout_deduction]);
                }
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Chargeback handled via database tables. User deactivated, commissions reversed.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Matching local transaction metadata ledger could not be resolved.']);
    }
} catch (Exception $disputeEx) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $disputeEx->getMessage()]);
}