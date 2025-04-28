<?php
// Database config
$db_host = 'localhost';
$db_name = 'poetry_contacts';
$db_user = 'poetry_user'; // Create this limited user
$db_pass = 'StrongPass123!';

header('Content-Type: application/json');

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Get form data
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['message'] ?? '');

    // Validate
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please fill all fields correctly');
    }

    // Save to database
    $stmt = $pdo->prepare("INSERT INTO visitor_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);

    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}