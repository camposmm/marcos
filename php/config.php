<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'marcos_poetry_db');
define('DB_USER', 'poetry_user'); // Use restricted user, not root!
define('DB_PASS', 'your_strong_password_here');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1); // Set to 0 in production

// Establish database connection
function getDBConnection() {
    static $db = null;
    
    if ($db === null) {
        try {
            $db = new PDO(
                "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("We're experiencing technical difficulties. Please try again later.");
        }
    }
    return $db;
}
?>
