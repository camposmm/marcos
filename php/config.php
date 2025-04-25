<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'marcos_db');
define('DB_USER', 'secure_dbuser');
define('DB_PASS', 'your_strong_password');
define('DB_DSN', 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4');

// reCAPTCHA Keys
define('RECAPTCHA_SITE_KEY', 'your_site_key');
define('RECAPTCHA_SECRET', 'your_secret_key');

// Admin Credentials (store hashed password)
define('ADMIN_USERNAME', 'marcos_admin');
define('ADMIN_PASSWORD_HASH', password_hash('your_admin_password', PASSWORD_BCRYPT));

// Email Settings
define('ADMIN_EMAIL', 'your@email.com');
define('EMAIL_FROM', 'noreply@marcosmurilocampos.com');

// Session Security
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database Connection
function getDBConnection() {
    static $db;
    if ($db === null) {
        try {
            $db = new PDO(DB_DSN, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            die('A database error occurred');
        }
    }
    return $db;
}

// reCAPTCHA Verification
function verifyRecaptcha($response) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => RECAPTCHA_SECRET,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

// Email Sender
function sendEmail($to, $subject, $body) {
    $headers = [
        'From' => EMAIL_FROM,
        'Reply-To' => EMAIL_FROM,
        'X-Mailer' => 'PHP/' . phpversion(),
        'Content-Type' => 'text/plain; charset=UTF-8'
    ];

    return mail($to, $subject, $body, $headers);
}

// Input Sanitization
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}