<?php
/**
 * Identity Trace AI — Google Auth Router Terminal
 * File: auth_google.php
 */
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------------------
// STATE 2: HANDLE GOOGLE AUTHENTICATION CALLBACK RESPONSE 
// -------------------------------------------------------------------------
if (isset($_GET['code'])) {
    
    // Parse out our bundled state parameter payload cleanly
    $incoming_state = isset($_GET['state']) ? $_GET['state'] : '';
    $state_parts = explode('|', $incoming_state);
    
    $csrf_token  = $state_parts[0] ?? '';
    $return_path = isset($state_parts[1]) ? urldecode($state_parts[1]) : '/index.php';

    // Security Guard: Restrict formatting strictly to relative locations to eliminate open-redirect flaws
    if (empty($return_path) || strpos($return_path, '/') !== 0) {
        $return_path = '/index.php';
    }

    // CSRF Protection: Validate that the cross-site state tokens match exactly
    if (empty($csrf_token) || ($csrf_token !== ($_SESSION['oauth_state'] ?? ''))) {
        unset($_SESSION['oauth_state']);
        die('Security Check Failed: Invalid OAuth state parameter signature.');
    }
    unset($_SESSION['oauth_state']); // Instantly flush tracking hash token after validation

    // Exchange the temporary code for secure API tokens via backend cURL POST
    $token_url = 'https://oauth2.googleapis.com/token';
    $payload = [
        'code'          => $_GET['code'],
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $token_data = json_decode($response, true);

    if ($http_code !== 200 || empty($token_data['access_token'])) {
        die('Authentication Error: Failed to fetch token from Google identity provider.');
    }

    $access_token = $token_data['access_token'];

    // Use access token to securely request verified user profile data details
    $userinfo_url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . urlencode($access_token);
    
    $ch = curl_init($userinfo_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $user_response = curl_exec($ch);
    $user_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $user_data = json_decode($user_response, true);

    if ($user_http_code !== 200 || empty($user_data['email'])) {
        die('Profile Error: Unable to fetch verified user data endpoints from Google API.');
    }

    // Capture verified identity variables from payload metadata
    $email  = trim($user_data['email']);
    $name   = trim($user_data['name'] ?? '');
    $avatar = trim($user_data['picture'] ?? ''); 
    
    // Capture physical location via Cloudflare headers (Matches your OTP flow)
    $country = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? strtoupper(trim($_SERVER['HTTP_CF_IPCOUNTRY'])) : 'XX';

    try {
        // 1. Check if the user email signature already sits inside the table matrix
        $stmt = $pdo->prepare("SELECT `id`, `name`, `email`, `avatar`, `country` FROM `users` WHERE `email` = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            // TRACKING INTERCEPT: Pull tracking code from background session (populated by head.php)
            $cid = isset($_SESSION['active_cid']) ? trim($_SESSION['active_cid']) : null;

            // 2. New Registration: Write an entry with Name, Avatar, Email, Cloudflare Country, and tracking CID
            // Note: 'plan' column is omitted here so it naturally defaults to NULL in your database
            $insert_stmt = $pdo->prepare("
                INSERT INTO `users` (`email`, `password`, `name`, `avatar`, `country`, `cid`, `created_at`) 
                VALUES (:email, NULL, :name, :avatar, :country, :cid, NOW())
            ");
            $insert_stmt->execute([
                ':email'   => $email,
                ':name'    => $name,
                ':avatar'  => $avatar,
                ':country' => $country,
                ':cid'     => $cid
            ]);

            $user_id = $pdo->lastInsertId();

            // Clear affiliate tracking token memory only after a verified conversion
            if ($cid) {
                unset($_SESSION['active_cid']);
            }
        } else {
            // 3. Existing User: Bypasses database writes entirely, pull current data rows
            $user_id = $user['id'];
            $name    = $user['name'];
            $avatar  = $user['avatar'];
            $country = $user['country'];
        }

        // Initialize secure global user login tracking state parameters
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id']        = $user_id;
        $_SESSION['user_email']     = $email;
        $_SESSION['user_name']      = $name;
        $_SESSION['user_avatar']    = $avatar;
        $_SESSION['user_country']   = $country;

        // Redirect the user straight into the dynamic absolute landing location safely
        header('Location: ' . BASE_URL . ltrim($return_path, '/'));
        exit;

    } catch (\PDOException $e) {
        die('Database Sync Failure: ' . $e->getMessage());
    }
}

// -------------------------------------------------------------------------
// STATE 1: INITIAL REDIRECT ROUTING TRIGGER (USER CLICKED LOG IN)
// -------------------------------------------------------------------------
// Generate a secure cross-site reference checking string
$_SESSION['oauth_state'] = bin2hex(random_bytes(16));

// Retrieve our URL param return target route context safely
$return_target = isset($_GET['return']) ? trim($_GET['return']) : '/index.php';

if (empty($return_target) || strpos($return_target, '/') !== 0) {
    $return_target = '/index.php';
}

// Bundle the CSRF validation token and our destination page together inside the state wrapper
$bundled_state = $_SESSION['oauth_state'] . '|' . urlencode($return_target);

$google_auth_endpoint = 'https://accounts.google.com/o/oauth2/v2/auth';

$params = [
    'response_type' => 'code',
    'client_id'     => GOOGLE_CLIENT_ID,
    'redirect_uri'  => GOOGLE_REDIRECT_URI,
    'scope'         => 'openid email profile',
    'state'         => $bundled_state,   // Passed securely as a single property parameter payload to Google
    'prompt'        => 'select_account'  // Forces the Google account chooser screen cleanly every time
];

// Combine tracking params onto the primary endpoint path location
$target_redirect_url = $google_auth_endpoint . '?' . http_build_query($params);

// Send user out to Google Identity Picker matrix nodes
header('Location: ' . $target_redirect_url);
exit;
