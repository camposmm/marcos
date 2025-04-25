<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (adminLogin($_POST['username'], $_POST['password'])) {
        $redirect = $_SESSION['redirect_url'] ?? 'dashboard.php';
        unset($_SESSION['redirect_url']);
        header("Location: $redirect");
        exit;
    }
    $error = 'Invalid credentials';
}

require __DIR__ . '/../includes/header.php';
?>

<div class="login-container">
    <h1>Admin Login</h1>
    
    <?php if (!empty($error)): ?>
        <div class="alert error"><?= sanitizeInput($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>