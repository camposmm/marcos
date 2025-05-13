<?php
session_start();
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>