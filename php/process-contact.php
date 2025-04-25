<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Honeypot check
    if (!empty($_POST['website'])) {
        $response['success'] = true; // Silent fail for bots
        echo json_encode($response);
        exit;
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

    // Validate email
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception('Please enter a valid email address');
    }

    // Verify reCAPTCHA
    $recaptcha = verifyRecaptcha($_POST['g-recaptcha-response']);
    if (!$recaptcha['success']) {
        throw new Exception('reCAPTCHA verification failed');
    }

    // Prepare data
    $data = [
        'name' => sanitizeInput($_POST['name']),
        'email' => $email,
        'message' => sanitizeInput($_POST['message']),
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255)
    ];

    // Save to database
    $db = getDBConnection();
    $stmt = $db->prepare("INSERT INTO messages 
        (name, email, message, ip_address, user_agent) 
        VALUES (:name, :email, :message, :ip_address, :user_agent)");
    
    $stmt->execute($data);

    // Send notification email
    $emailSubject = "New Contact Form Submission: " . $data['name'];
    $emailBody = "You have received a new message:\n\n"
        . "Name: {$data['name']}\n"
        . "Email: {$data['email']}\n\n"
        . "Message:\n{$data['message']}\n\n"
        . "IP: {$data['ip_address']}\n"
        . "User Agent: {$data['user_agent']}";
    
    sendEmail(ADMIN_EMAIL, $emailSubject, $emailBody);

    $response = [
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully.'
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log('Contact form error: ' . $e->getMessage());
} finally {
    echo json_encode($response);
}