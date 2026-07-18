<?php
// mailer.php
require_once 'config.php';

/**
 * Dispatches high-priority transactional emails via the Brevo v3 REST API
 * * @param string $toEmail   Target recipient email address
 * @param string $subject   Subject line header string
 * @param string $htmlBody  The structured HTML context layout content
 * @return array            Array containing ['success' => true/false, 'message' => '...']
 */
function sendTransactionalMail($toEmail, $subject, $htmlBody) {
    // Endpoint for the standard Brevo JSON v3 API structure
    $url = 'https://api.brevo.com/v3/smtp/email';
    
    // Build JSON payload array using constants from config.php
    $payload = [
        'sender' => [
            'name'  => MAIL_FROM_NAME,
            'email' => MAIL_FROM_EMAIL
        ],
        'to' => [
            [
                'email' => $toEmail
            ]
        ],
        'subject' => $subject,
        'htmlContent' => $htmlBody
    ];

    // Initialize clean cURL instance
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    // Pass updated API key via standard HTTPS request headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'api-key: ' . BREVO_API_KEY,
        'content-type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Validate request transmission statuses
    if ($curlError) {
        return ['success' => false, 'message' => 'cURL Interruption Error: ' . $curlError];
    }

    if ($httpCode >= 200 && $httpCode < 300) {
        return ['success' => true, 'message' => 'Payload delivered successfully.'];
    } else {
        return ['success' => false, 'message' => 'API Error (HTTP ' . $httpCode . '): ' . $response];
    }
}
