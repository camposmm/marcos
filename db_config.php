<?php
$host = 'localhost';
$db   = 'newsletter';
$user = 'root'@'localhost';
$pass = 'loucuras600';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
