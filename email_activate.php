<?php
/**
 * File: email_activate.php
 * Sends account approval notification email to affiliate.
 */
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    die('Direct access not permitted.');
}

require_once __DIR__ . '/mailer.php';

function sendActivationEmail($toEmail, $name) {
    $subject = "Confirmation: Your Identity Search AI account is now approved";
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
                    <h2 style='font-size: 19px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 12px; font-family: \"Roboto\", sans-serif; text-align: left;'>Account Approved</h2>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>Hi " . htmlspecialchars($name) . ",</p>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>Congratulations, your account just got approved!</p>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>You can now start promoting our offers links!</p>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>Please make sure that your payment details are filled out correctly. If you wish to change them, you can do that from your account.</p>
                    <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.6; margin-bottom: 0; font-family: \"Roboto\", sans-serif; text-align: left;'>In case you have any questions, contact your account manager.</p>
                </div>

                <div style='padding: 24px 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: left; font-family: \"Roboto\", sans-serif;'>
                    <p style='font-size: 12px; color: #111827; font-weight: 700; margin: 0 0 2px 0;'>Best Regards,</p>
                    <p style='font-size: 12px; color: #111827; font-weight: 700; margin: 0 0 10px 0;'>James Smith</p>
                    <p style='font-size: 11px; color: #6B7280; font-weight: 500; margin: 0 0 2px 0;'>Affiliate Manager</p>
                    <p style='font-size: 11px; color: #6B7280; font-weight: 400; margin: 0 0 2px 0;'>E: <a href='mailto:smith@identitysearch.ai' style='color: #128c7e; text-decoration: none;'>smith@identitysearch.ai</a></p>
                    <p style='font-size: 11px; color: #6B7280; font-weight: 400; margin: 0 0 2px 0;'>Telegram: <a href='https://t.me/identitysearchai' style='color: #128c7e; text-decoration: none;'>https://t.me/identitysearchai</a></p>
                </div>

                <div style='padding: 16px 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: center; font-family: \"Roboto\", sans-serif;'>
                    <p style='font-size: 10px; color: #9CA3AF; font-weight: 500; margin: 0;'>&copy; 2026 - Identity Search AI</p>
                </div>

            </div>
        </div>
    ";

    return sendTransactionalMail($toEmail, $subject, $htmlBody);
}