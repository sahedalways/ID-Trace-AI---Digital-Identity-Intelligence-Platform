<?php
/**
 * File: email_ban.php
 * Sends account ban notification email to affiliate.
 */
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    die('Direct access not permitted.');
}

require_once __DIR__ . '/mailer.php';

function sendBanEmail($toEmail, $name) {
    $subject = "Your account has been banned from Identity Search AI";
    $htmlBody = "
        <div style='background-color: #FAFAFA; padding: 24px 12px; font-family: \"Roboto\", -apple-system, BlinkMacSystemFont, sans-serif;'>
            <div style='max-width: 380px; margin: 0 auto; background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04);'>

                <div style='padding: 14px 20px; border-bottom: 1px solid #E5E7EB; text-align: center; background-color: #F9FAFB;'>
                    <div style='display: inline-block; vertical-align: middle; text-align: center;'>
                        <img src='https://i.postimg.cc/SQnMm8sh/2313362.png' alt='Identity Search AI Logo' style='width: 28px; height: 28px; display: inline-block; vertical-align: middle; margin-right: 6px; border: 0;'>
                        <span style='font-size: 14px; font-weight: 800; color: #111827; letter-spacing: -0.3px; font-family: \"Roboto\", sans-serif; display: inline-block; vertical-align: middle;'>Identity Search <span style='font-size: 10px; font-weight: 900; background-color: #000000; color: #FFFFFF; padding: 1.5px 5px; border-radius: 3.5px; margin-left: 3px; vertical-align: middle; letter-spacing: 0.5px;'>AI</span></span>
                    </div>
                </div>

                <div style='padding: 28px 24px; text-align: left;'>
                    <h2 style='font-size: 19px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 12px; font-family: \"Roboto\", sans-serif; text-align: left;'>Your Account Has Been Banned</h2>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>Hi " . htmlspecialchars($name) . ",</p>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>After reviewing your account, we found unusual results and activity.</p>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>This email is to inform you that your account has been blocked and all payments have been suspended.</p>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>We were analyzing your activities and we noticed that a very high number of conversions were flagged as high risk of fraudulent activity by our fraud detection system and we could not find sufficient information about your sources.</p>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>Accepting such traffic puts our deal with the providers in jeopardy.</p>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 0; font-family: \"Roboto\", sans-serif; text-align: left;'>This results in us choosing to part ways. We wish you the best of luck with another company.</p>
                </div>

                <div style='padding: 24px 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: left; font-family: \"Roboto\", sans-serif;'>
                    <p style='font-size: 12px; color: #111827; font-weight: 700; margin: 0 0 2px 0;'>Best Regards,</p>
                    <p style='font-size: 12px; color: #111827; font-weight: 700; margin: 0 0 10px 0;'>The Identity Search AI Team</p>
                </div>

                <div style='padding: 16px 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: center; font-family: \"Roboto\", sans-serif;'>
                    <p style='font-size: 10px; color: #9CA3AF; font-weight: 500; margin: 0;'>&copy; 2026 - Identity Search AI</p>
                </div>

            </div>
        </div>
    ";

    return sendTransactionalMail($toEmail, $subject, $htmlBody);
}
