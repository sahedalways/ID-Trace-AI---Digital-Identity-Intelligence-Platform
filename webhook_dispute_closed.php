<?php
/**
 * Sub-Webhook Module — charge.dispute.closed: Final Dispute Resolution Handler
 * File: webhook_dispute_closed.php
 */

$charge_id = $object['charge'] ?? '';
$dispute_reason = $object['reason'] ?? 'unrecognized';
$dispute_amount = (float)($object['amount'] / 100);
$dispute_status = $object['status'] ?? 'closed';
$is_charge_won = ($object['result'] ?? '') === 'won';

if (empty($charge_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing dispute charge reference for close tracking.']);
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

        $stripe_invoice_id = $charge_data['invoice'] ?? '';
        if (!empty($stripe_invoice_id)) {
            $tx_stmt2 = $pdo->prepare("SELECT `uid`, `plan`, `cid`, `tid` FROM `transactions` WHERE `stripe_invoice_id` = ? AND `status` != 'chargeback' LIMIT 1");
            $tx_stmt2->execute([$stripe_invoice_id]);
            $transaction_record = $tx_stmt2->fetch(PDO::FETCH_ASSOC);
        }
    }

    if ($transaction_record) {
        $offender_uid = (int)$transaction_record['uid'];
        $plan_name = $transaction_record['plan'];
        $affiliate_cid = !empty($transaction_record['cid']) ? $transaction_record['cid'] : null;

        $pdo->beginTransaction();

        if ($is_charge_won) {
            // Dispute won: restore user account and reverse the earlier chargeback clawback
            $pdo->prepare("
                UPDATE `transactions` 
                SET `status` = 'succeeded',
                    `dispute_reason` = ? 
                WHERE (`stripe_charge_id` = ? OR (`stripe_invoice_id` = ? AND `stripe_charge_id` IS NULL)) AND `status` = 'chargeback'
            ")->execute([$dispute_reason, $charge_id, $stripe_invoice_id ?? '']);

            // Re-activate user account
            $pdo->prepare("UPDATE `users` SET `status` = 'active' WHERE `id` = ?")->execute([$offender_uid]);

            // Restore affiliate commission if previously clawed back
            if (!empty($affiliate_cid)) {
                $click_stmt = $pdo->prepare("SELECT `affid` FROM `clicks` WHERE `cid` = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci LIMIT 1");
                $click_stmt->execute([$affiliate_cid]);
                $click_data = $click_stmt->fetch(PDO::FETCH_ASSOC);

                if ($click_data) {
                    $aff_id = (int)$click_data['affid'];
                    $restore_amount = $dispute_amount * 0.50;

                    if ($restore_amount > 0) {
                        $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` + ? WHERE `id` = ?")
                            ->execute([$restore_amount, $aff_id]);

                        $pdo->prepare("INSERT INTO `conversions` (`tid`, `cid`, `uid`, `affid`, `plan`, `price`, `payout`, `note`, `fire_postback`, `created_at`) VALUES (?, ?, ?, ?, ?, 0.00, ?, 'Dispute Won — Commission Restored', 0, NOW())")
                            ->execute(['DWN-' . generateWebhookTransactionId(), $affiliate_cid, $offender_uid, $aff_id, $plan_name, $restore_amount]);
                    }
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'Dispute won. Account restored, commissions reversed back.']);
        } else {
            // Dispute lost (closed against merchant): enforce permanent chargeback
            $pdo->prepare("
                UPDATE `transactions` 
                SET `status` = 'chargeback',
                    `dispute_status` = 1, 
                    `dispute_reason` = ?, 
                    `dispute_amount` = ? 
                WHERE (`stripe_charge_id` = ? OR (`stripe_invoice_id` = ? AND `stripe_charge_id` IS NULL)) AND `status` != 'chargeback'
            ")->execute([$dispute_reason, $dispute_amount, $charge_id, $stripe_invoice_id ?? '']);

            $pdo->prepare("
                UPDATE `users` 
                SET `status` = 'inactive', 
                    `stripe_subscription_id` = NULL, 
                    `plan` = NULL, 
                    `credit` = 0, 
                    `validity` = NULL 
                WHERE `id` = ?
            ")->execute([$offender_uid]);

            if (!empty($affiliate_cid)) {
                $click_stmt = $pdo->prepare("SELECT `affid` FROM `clicks` WHERE `cid` = CONVERT(? USING utf8mb4) COLLATE utf8mb4_unicode_ci LIMIT 1");
                $click_stmt->execute([$affiliate_cid]);
                $click_data = $click_stmt->fetch(PDO::FETCH_ASSOC);

                if ($click_data) {
                    $aff_id = (int)$click_data['affid'];
                    $rev_payout_deduction = $dispute_amount * 0.50;

                    if ($rev_payout_deduction > 0) {
                        $pdo->prepare("UPDATE `affiliates` SET `balance` = `balance` - ? WHERE `id` = ?")
                            ->execute([$rev_payout_deduction, $aff_id]);

                        $pdo->prepare("INSERT INTO `conversions` (`tid`, `cid`, `uid`, `affid`, `plan`, `price`, `payout`, `note`, `fire_postback`, `created_at`) VALUES (?, ?, ?, ?, ?, 0.00, ?, 'Dispute Lost — Commission Deducted', 0, NOW())")
                            ->execute(['DLP-' . generateWebhookTransactionId(), $affiliate_cid, $offender_uid, $aff_id, $plan_name, -$rev_payout_deduction]);
                    }
                }
            }

            echo json_encode(['status' => 'success', 'message' => 'Dispute lost. Permanent chargeback enforced.']);
        }

        $pdo->commit();
    } else {
        echo json_encode(['status' => 'ignored', 'message' => 'No matching transaction found for dispute charge: ' . $charge_id]);
    }
} catch (Exception $closedEx) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $closedEx->getMessage()]);
}