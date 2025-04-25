<?php
require_once __DIR__ . '/../config.php';

function requireAdminAuth() {
    if (!isAdminLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

function isAdminLoggedIn() {
    return !empty($_SESSION['admin_logged_in']) 
        && $_SESSION['admin_ip'] === $_SERVER['REMOTE_ADDR']
        && $_SESSION['admin_ua'] === $_SERVER['HTTP_USER_AGENT'];
}

function adminLogin($username, $password) {
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['admin_ua'] = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['admin_last_activity'] = time();
        return true;
    }
    return false;
}

function adminLogout() {
    $_SESSION = [];
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
}