<?php
require_once __DIR__ . '/auth.php';
requireAdminAuth();

require __DIR__ . '/../includes/header.php';
?>

<div class="admin-container">
    <h1>Dashboard</h1>
    
    <div class="admin-stats">
        <?php
        $db = getDBConnection();
        $stats = [
            'total_messages' => $db->query("SELECT COUNT(*) FROM messages")->fetchColumn(),
            'unread_messages' => $db->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn(),
            'today_messages' => $db->query("SELECT COUNT(*) FROM messages WHERE DATE(created_at) = CURDATE()")->fetchColumn()
        ];
        ?>
        
        <div class="stat-card">
            <h3>Total Messages</h3>
            <p><?= $stats['total_messages'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Unread Messages</h3>
            <p><?= $stats['unread_messages'] ?></p>
        </div>
        <div class="stat-card">
            <h3>Today's Messages</h3>
            <p><?= $stats['today_messages'] ?></p>
        </div>
    </div>
    
    <div class="admin-actions">
        <a href="messages.php" class="btn btn-primary">View Messages</a>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>