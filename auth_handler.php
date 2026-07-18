<?php
/**
 * Identity Search AI — Secure Dynamic Authentication Operations Handler
 * File: auth_handler.php
 */
require_once 'config.php';
require_once 'mailer.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['action'])) {
    header('Content-Type: application/json');

    // Extract the dynamic relative path parameter directly from the current active request context
    $return_path = isset($_GET['return']) ? trim($_GET['return']) : '/index.php';

    // Security Guard: Restrict path formatting strictly to relative locations to eliminate open-redirect flaws
    if (empty($return_path) || strpos($return_path, '/') !== 0) {
        $return_path = '/index.php';
    }

    // Construct absolute local redirection engine string natively using BASE_URL
    $final_redirect_target = BASE_URL . ltrim($return_path, '/');

    // -------------------------------------------------------------------------
    // ACTION 1: DISPATCHING OTP PAYLOAD
    // -------------------------------------------------------------------------
    if ($_GET['action'] === 'send_otp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw_input = json_decode(file_get_contents('php://input'), true);
        $email = trim($raw_input['email'] ?? '');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address structure.']);
            exit;
        }

        try {
            $_SESSION['auth_pending_email'] = $email;

            $otp_code = (APP_ENV === 'development') ? '123456' : (string)random_int(100000, 999999);

            $_SESSION['auth_otp_token']    = $otp_code;
            $_SESSION['auth_otp_expires']  = time() + 600; // 10-Minute window

            // HTML Body Refactored: Left-aligned thin prose text, scaled headers, compact centered dynamic code
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
                            <h2 style='font-size: 19px; font-weight: 700; color: #111827; margin-top: 0; margin-bottom: 12px; font-family: \"Roboto\", sans-serif; text-align: left;'>Verification Code</h2>
                            <p style='font-size: 13px; color: #4B5563; font-weight: 400; line-height: 1.5; margin-bottom: 24px; font-family: \"Roboto\", sans-serif; text-align: left;'>Use the single-use verification code below to complete your login sequence.</p>

                            <div style='text-align: center; margin-bottom: 24px;'>
                                <div style='background-color: #FFFFFF; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px; text-align: center; font-size: 22px; font-weight: 800; letter-spacing: 4px; color: #128c7e; font-family: \"Roboto\", sans-serif; display: inline-block; min-width: 150px;'>
                                    {$otp_code}
                                </div>
                            </div>

                            <p style='font-size: 12px; color: #9CA3AF; font-weight: 400; line-height: 1.4; margin-bottom: 0; font-family: \"Roboto\", sans-serif; text-align: left;'>If you did not initiate this request, you can safely disregard this message.</p>
                        </div>

                        <div style='padding: 24px 20px; border-top: 1px solid #F3F4F6; background-color: #FAFAFA; text-align: center; font-family: \"Roboto\", sans-serif;'>
                            <div style='display: block; margin-bottom: 10px;'>
                                <span style='font-size: 18px; display: inline-block; vertical-align: middle;'>🕵️‍♂️</span>
                            </div>

                            <p style='font-size: 10px; color: #4B5563; font-weight: 500; margin: 0 0 6px 0;'>&copy; 2026 - Identity Search AI</p>

                            <p style='font-size: 10px; color: #4B5563; font-weight: 400; margin: 0;'>
                                <a href='mailto:support@identitysearch.ai' style='color: #128c7e; text-decoration: none;'>support@identitysearch.ai</a>
                            </p>
                        </div>

                    </div>
                </div>
            ";

            $subject = "Your OTP code : {$otp_code}";

            $mailer = sendTransactionalMail($email, $subject, $htmlBody);

            if ($mailer['success']) {
                echo json_encode(['status' => 'success', 'message' => 'Verification token dispatched successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gateway Rejected Payload: ' . $mailer['message']]);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'System error generating token: ' . $e->getMessage()]);
        }
        exit;
    }

    // -------------------------------------------------------------------------
    // ACTION 2: MATCHING INPUT OTP & VERIFYING USER EXISTENCE
    // -------------------------------------------------------------------------
    if ($_GET['action'] === 'verify_otp' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw_input = json_decode(file_get_contents('php://input'), true);
        $user_otp  = trim($raw_input['otp'] ?? '');

        $saved_otp = $_SESSION['auth_otp_token'] ?? '';
        $expires   = $_SESSION['auth_otp_expires'] ?? 0;
        $email     = $_SESSION['auth_pending_email'] ?? '';

        if (empty($saved_otp) || time() > $expires) {
            echo json_encode(['status' => 'error', 'message' => 'Verification window has expired. Please request a new code.']);
            exit;
        }

        if ($user_otp === $saved_otp) {
            try {
                // Check if the user email signature already sits inside the table matrix
                $stmt = $pdo->prepare("SELECT `id`, `name`, `email`, `avatar`, `country` FROM `users` WHERE `email` = :email LIMIT 1");
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();

                // Clear OTP verification tokens from memory immediately now that it's verified
                unset($_SESSION['auth_otp_token'], $_SESSION['auth_otp_expires']);

                if (!$user) {
                    // NEW USER DETECTED: Put database insertion on hold. Inform frontend to request name parameters.
                    $_SESSION['auth_signup_verified_email'] = $email;
                    echo json_encode(['status' => 'require_name']);
                } else {
                    // EXISTING USER: Log them in straight away
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_id']        = $user['id'];
                    $_SESSION['user_email']     = $user['email'];
                    $_SESSION['user_name']      = $user['name'];
                    $_SESSION['user_avatar']    = $user['avatar'];
                    $_SESSION['user_country']   = $user['country'];

                    unset($_SESSION['auth_pending_email']);
                    echo json_encode(['status' => 'success', 'redirect' => $final_redirect_target]);
                }
            } catch (\PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Database Sync Failure: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'The security code entered is invalid. Please verify and try again.']);
        }
        exit;
    }

    // -------------------------------------------------------------------------
    // ACTION 3: FINALIZE REGISTRATION WITH COLLECTED NAME
    // -------------------------------------------------------------------------
    if ($_GET['action'] === 'complete_signup' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $raw_input = json_decode(file_get_contents('php://input'), true);
        $name      = trim($raw_input['name'] ?? '');
        $email     = $_SESSION['auth_signup_verified_email'] ?? '';

        if (empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Session expired or invalid authorization sequence. Please start over.']);
            exit;
        }
        if (empty($name) || strlen($name) < 2) {
            echo json_encode(['status' => 'error', 'message' => 'Please enter a valid full name.']);
            exit;
        }

        // Capture client location via Cloudflare headers. Default to 'XX' for local servers.
        $country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY'])) : 'XX';

        try {
            // TRACKING INTERCEPT: Pull tracking code from background session (populated by head.php)
            $cid = isset($_SESSION['active_cid']) ? trim($_SESSION['active_cid']) : null;

            // New Registration: Write explicit row containing your precise schema matrix columns
            $insert_stmt = $pdo->prepare("
                INSERT INTO `users` (`email`, `password`, `name`, `avatar`, `country`, `cid`, `created_at`)
                VALUES (:email, NULL, :name, NULL, :country, :cid, NOW())
            ");
            $insert_stmt->execute([
                ':email'   => $email,
                ':name'    => $name,
                ':country' => $country,
                ':cid'     => $cid
            ]);

            $user_id = $insert_stmt ? $pdo->lastInsertId() : false;

            if (!$user_id) {
                throw new Exception("Unable to extract valid record primary index mapping context.");
            }

            // Establish standard secure global application user login tracking state parameters
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id']        = $user_id;
            $_SESSION['user_email']     = $email;
            $_SESSION['user_name']      = $name;
            $_SESSION['user_avatar']    = null;
            $_SESSION['user_country']   = $country;

            // Cleanup all auth registration and affiliate remnants out of server-side state memory
            unset($_SESSION['auth_signup_verified_email'], $_SESSION['auth_pending_email'], $_SESSION['active_cid']);

            echo json_encode(['status' => 'success', 'redirect' => $final_redirect_target]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Registration Failure: ' . $e->getMessage()]);
        }
        exit;
    }
}
