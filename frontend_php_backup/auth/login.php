<?php
require_once __DIR__ . '/../../backend/includes/init.php';

if ($auth->isLoggedIn()) {
    if ($auth->hasRole('patient')) {
        header('Location: ' . APP_URL . '/patient/dashboard.php');
    } elseif ($auth->hasRole('admin') || $auth->hasRole('dentist')) {
        header('Location: ' . APP_URL . '/admin/dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = __('invalid_email');
    } elseif (empty($password)) {
        $error = __('field_required');
    } else {
        if ($auth->login($email, $password, $rememberMe)) {
            $user = $auth->getCurrentUser();
            if ($user['role'] === 'patient') {
                header('Location: ' . APP_URL . '/patient/dashboard.php');
            } else {
                header('Location: ' . APP_URL . '/admin/dashboard.php');
            }
            exit;
        } else {
            $error = __('login_failed');
        }
    }
}
?>

<?php include __DIR__ . '/../views/login.php'; ?>
