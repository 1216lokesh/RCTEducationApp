<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireLogin();

$auth->logout();

header('Location: ' . APP_URL . '/auth/login.php');
exit;
?>
