<?php
// Start session for CSRF token
session_start();

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    die('Invalid CSRF token');
}

// Honeypot validation
if (!empty($_POST['website'])) {
    http_response_code(400);
    die('Spam detected');
}

// Validate required fields
$required_fields = ['name', 'email', 'message', 'g-recaptcha-response'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        die("Missing required field: $field");
    }
}

// Validate email
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    http_response_code(400);
    die('Invalid email address');
}

// Verify reCAPTCHA
$recaptcha_secret = '6LdI3iYrAAAAAFHG_1rkR6yRdivd3RWvhD_njuUP'; // Replace with your actual secret key
$recaptcha_response = $_POST['g-recaptcha-response'];

$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
$recaptcha_data = [
    'secret' => $recaptcha_secret,
    'response' => $recaptcha_response,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

$recaptcha_options = [
    'http' => [
        'method' => 'POST',
        'content' => http_build_query($recaptcha_data),
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
    ]
];

$recaptcha_context = stream_context_create($recaptcha_options);
$recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
$recaptcha_json = json_decode($recaptcha_result);

if (!$recaptcha_json->success) {
    http_response_code(400);
    die('reCAPTCHA verification failed');
}

// Sanitize inputs
$name = htmlspecialchars(trim($_POST['name']));
$message = htmlspecialchars(trim($_POST['message']));
$subject = "New Contact Form Submission from $name";

// Prepare email headers
$to = 'contact@marcosmurilocampos.com'; // Replace with your email
$headers = [
    'From' => $email,
    'Reply-To' => $email,
    'X-Mailer' => 'PHP/' . phpversion(),
    'Content-Type' => 'text/plain; charset=utf-8'
];

// Build email message
$email_message = "You have received a new message from your website contact form.\n\n";
$email_message .= "Name: $name\n";
$email_message .= "Email: $email\n\n";
$email_message .= "Message:\n$message\n";

// Send email
$mail_sent = mail($to, $subject, $email_message, $headers);

if ($mail_sent) {
    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent.']);
} else {
    http_response_code(500);
    die('Failed to send message. Please try again later.');
}
?>