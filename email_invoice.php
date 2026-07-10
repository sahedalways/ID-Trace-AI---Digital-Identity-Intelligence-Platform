<?php
/**
 * Identity Search AI — Secure Transactional Invoice Email Dispatcher Component
 * File: email_invoice.php
 * Usage: include 'email_invoice.php'; inside success.php right after commit()
 */

// Defensive validation: Ensure the file can only execute if nested within an active payment context framework
if (!isset($checkout_email) || empty($checkout_email)) {
    return; // Quietly exit if called outside an authorized payment sequence
}

try {
    $invoice_date = date('M d, Y');
    $formatted_price = '$' . number_format($plan_price, 2);

    // Construct Transactional Email Confirmation Layout cloning contact.php structure completely
    $escapedPayerName   = htmlspecialchars($cardholder_name, ENT_QUOTES, 'UTF-8');
    $escapedPlanName    = htmlspecialchars($plan_name, ENT_QUOTES, 'UTF-8');
    $escapedStreet      = htmlspecialchars($street_address, ENT_QUOTES, 'UTF-8');
    $escapedZip         = htmlspecialchars($zip_code, ENT_QUOTES, 'UTF-8');
    $escapedCountry     = htmlspecialchars($country, ENT_QUOTES, 'UTF-8');

    $invoiceHtmlBody = "
        <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
            <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>

                <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                    <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                        <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='Identity Search AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                        <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; display: inline-block; vertical-align: middle;'>Identity Search <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                    </div>
                </div>

                <div style='padding: 24px 20px; text-align: left;'>
                    <h2 style='font-size: 16px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 10px;'>Payment Invoice</h2>
                    <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 18px;'>Thank you very much for your payment at Identity Search AI. Your transaction completed successfully and your features are fully active.</p>

                    <div style='background-color: #FAFAFA; border: 1px solid #E5E7EB; border-radius: 10px; padding: 12px; margin-bottom: 18px;'>
                        <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Payer Name:</b> {$escapedPayerName}</div>
                        <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Billing Address:</b> {$escapedStreet}, {$escapedZip}, {$escapedCountry}</div>
                        <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Plan Name:</b> <span style='text-transform: uppercase;'>{$escapedPlanName}</span></div>
                        <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Credits:</b> {$credits_allocated} Reports</div>
                        <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Amount:</b> <span style='color: #128c7e; font-weight: 700;'>{$formatted_price}</span></div>
                        <div style='margin-bottom: 8px; font-size: 11px; color: #4B5563;'><b style='color: #111827;'>TXID:</b> <span style='font-family: monospace; color: #6B7280;'>{$unique_tid}</span></div>
                        <div style='font-size: 11px; color: #4B5563;'><b style='color: #111827;'>Date:</b> {$invoice_date}</div>
                    </div>

                    <p style='font-size: 11px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-top: 0; margin-bottom: 0;'>To download payment invoice, please visit your <a href='https://idtrace.ai/my-plan.php' style='color: #128c7e; text-decoration: none; font-weight: 500;'>account dashboard</a>.</p>
                </div>

                <div style='padding: 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: center;'>
                    <div style='display: block; margin-bottom: 8px;'>
                        <span style='font-size: 16px; display: inline-block; vertical-align: middle;'>🕵️‍♂️</span>
                    </div>
                    <p style='font-size: 9px; color: #4B5563; font-weight: 500; margin: 0 0 4px 0;'>&copy; 2026 - Identity Search AI</p>
                    <p style='font-size: 9px; color: #4B5563; font-weight: 400; margin: 0;'>
                        <a href='mailto:support@identitysearch.ai' style='color: #128c7e; text-decoration: none;'>support@identitysearch.ai</a>
                    </p>
                </div>

            </div>
        </div>
    ";

    $invoiceSubject = "Your Payment Invoice — {$unique_tid}";
    sendTransactionalMail($checkout_email, $invoiceSubject, $invoiceHtmlBody);

} catch (Exception $invoiceEx) {
    error_log("Invoice notification failed processing cleanly: " . $invoiceEx->getMessage());
}
