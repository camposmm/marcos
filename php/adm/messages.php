<?php
// Add password protection here
$pdo = new PDO("mysql:host=localhost;dbname=poetry_contacts", 'poetry_user', 'StrongPass123!');

$messages = $pdo->query("SELECT * FROM visitor_messages ORDER BY created_at DESC")->fetchAll();
?>

<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Message</th>
        <th>Date</th>
    </tr>
    <?php foreach ($messages as $msg): ?>
    <tr>
        <td><?= htmlspecialchars($msg['name']) ?></td>
        <td><?= htmlspecialchars($msg['email']) ?></td>
        <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
        <td><?= $msg['created_at'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>