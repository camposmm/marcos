<?php
require_once __DIR__ . '/auth.php';
requireAdminAuth();

$db = getDBConnection();
$action = $_GET['action'] ?? '';

try {
    // Mark as read
    if ($action === 'mark_read' && !empty($_GET['id'])) {
        $stmt = $db->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        header('Location: messages.php');
        exit;
    }

    // Delete message
    if ($action === 'delete' && !empty($_GET['id'])) {
        $stmt = $db->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        header('Location: messages.php');
        exit;
    }

    // Get all messages
    $stmt = $db->query("SELECT * FROM messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log('Admin messages error: ' . $e->getMessage());
    die('A database error occurred');
}

// Include view template
require __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <h1>Messages</h1>
    
    <?php if (empty($messages)): ?>
        <p>No messages found.</p>
    <?php else: ?>
        <div class="message-list">
            <?php foreach ($messages as $message): ?>
            <div class="message <?= $message['is_read'] ? '' : 'unread' ?>">
                <div class="message-header">
                    <h3><?= sanitizeInput($message['name']) ?></h3>
                    <div class="message-meta">
                        <a href="mailto:<?= sanitizeInput($message['email']) ?>">
                            <?= sanitizeInput($message['email']) ?>
                        </a>
                        <span>•</span>
                        <?= date('M j, Y g:i a', strtotime($message['created_at'])) ?>
                    </div>
                </div>
                <div class="message-content">
                    <p><?= nl2br(sanitizeInput($message['message'])) ?></p>
                </div>
                <div class="message-footer">
                    <div class="message-actions">
                        <?php if (!$message['is_read']): ?>
                            <a href="?action=mark_read&id=<?= $message['id'] ?>">Mark Read</a>
                        <?php endif; ?>
                        <a href="?action=delete&id=<?= $message['id'] ?>" 
                           onclick="return confirm('Delete this message?')">Delete</a>
                        <span class="ip-address">IP: <?= sanitizeInput($message['ip_address']) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>