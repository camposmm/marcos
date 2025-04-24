<?php
require 'db_config.php';

$email = trim($_POST['email']);
$recaptchaSecret = 'YOUR_SECRET_KEY_HERE';
$recaptchaResponse = $_POST['g-recaptcha-response'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

// Validate reCAPTCHA
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
$responseData = json_decode($response);
if (!$responseData->success) {
    die("reCAPTCHA verification failed. Try again.");
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO subscriptions (email) VALUES (?)");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    echo "Thanks for subscribing!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
