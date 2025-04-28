<?php
require_once 'config.php';

// Start session for CSRF token
session_start();

// Verify reCAPTCHA first
if (isset($_POST['g-recaptcha-response'])) {
    $recaptcha_secret = 'YOUR_RECAPTCHA_SECRET_KEY';
    $recaptcha_response = $_POST['g-recaptcha-response'];
    
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];
    
    $context = stream_context_create($options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $context);
    $recaptcha_json = json_decode($recaptcha_result);
    
    if (!$recaptcha_json->success) {
        die("reCAPTCHA verification failed. Please try again.");
    }
}

// Check CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed.");
}

// Check honeypot field
if (!empty($_POST['website'])) {
    die("Spam detected.");
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    // Validate inputs
    if (empty($name) || strlen($name) < 2) {
        die("Name must be at least 2 characters long.");
    }
    
    if (!$email) {
        die("Invalid email address.");
    }
    
    if (empty($message) || strlen($message) < 10) {
        die("Message must be at least 10 characters long.");
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $message, $ip_address, $user_agent]);
        
        // Return JSON response for AJAX handling
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
        exit();
    } catch(PDOException $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit();
    }
}
?>