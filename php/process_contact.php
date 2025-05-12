<?php
require_once 'config.php';

// Start session for CSRF token
session_start();

// Verify reCAPTCHA first
if (isset($_POST['g-recaptcha-response'])) {
    $recaptcha_secret = '6LdI3iYrAAAAAFHG_1rkR6yRdivd3RWvhD_njuUP';
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
// Example: Lock out after 3 failed attempts
if ($failed_attempts >= 3) {
    die("Too many attempts. Try again later.");
}
$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
$message = htmlspecialchars($_POST['message']);
?>
<?php
// Database configuration
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email from form
$email = $_POST['email'];
$ip_address = $_SERVER['REMOTE_ADDR'];

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email, ip_address) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $ip_address);

// Execute and respond
if ($stmt->execute()) {
    echo "Thank you for subscribing!";
} else {
    if ($conn->errno == 1062) {
        echo "This email is already subscribed.";
    } else {
        echo "Error: " . $conn->error;
    }
}

$stmt->close();
$conn->close();
?>