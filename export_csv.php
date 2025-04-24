<?php
require 'db_config.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="subscriptions.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Email', 'Subscribed At']);

$result = $conn->query("SELECT id, email, subscribed_at FROM subscriptions");

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$conn->close();
?>
