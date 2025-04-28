<?php

// 1. reCAPTCHA Verification (FIRST STEP!)
$secretKey = "YOUR_SECRET_KEY"; // From Google reCAPTCHA admin
$captchaResponse = $_POST['g-recaptcha-response'];
$verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse";
$response = json_decode(file_get_contents($verifyUrl));

if (!$response->success) {
    die("reCAPTCHA verification failed. Please try again.");
}

// 2. Only process the form if reCAPTCHA succeeds
$name = $_POST['name'];
$email = $_POST['email'];
// ... (rest of your form processing code)

require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate required fields
    $required = ['name', 'email', 'message', 'csrf_token'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Please fill all required fields");
        }
    }

    // Verify CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new Exception('Security token validation failed');
    }

    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address');
    }

    // Save to database
    $db = getDBConnection();
    $stmt = $db->prepare("INSERT INTO messages 
        (name, email, message, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $name,
        $email,
        $message,
        $_SERVER['REMOTE_ADDR'],
        substr($_SERVER['HTTP_USER_AGENT'], 0, 255)
    ]);

    $response = [
        'success' => true,
        'message' => 'Thank you! Your message has been sent.'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Contact form error: " . $e->getMessage());
}

echo json_encode($response);
?>