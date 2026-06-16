<?php
require_once '../../backend/includes/init.php';

if ($auth->isLoggedIn()) {
    header('Location: ' . APP_URL . '/patient/dashboard.php');
    exit;
}

$errors = [];
$formData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'date_of_birth' => '',
    'gender' => '',
    'language' => DEFAULT_LANGUAGE
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData = array_merge($formData, $_POST);

    // Validation
    if (empty(trim($_POST['first_name']))) {
        $errors['first_name'] = __('field_required');
    }
    if (empty(trim($_POST['last_name']))) {
        $errors['last_name'] = __('field_required');
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = __('invalid_email');
    }
    if (empty($_POST['password'])) {
        $errors['password'] = __('field_required');
    }
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors['confirm_password'] = __('password_mismatch');
    }

    if (empty($errors)) {
        $registerData = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'gender' => $_POST['gender'] ?? null,
            'language' => $_POST['language'] ?? DEFAULT_LANGUAGE,
            'password' => $_POST['password'],
            'role' => 'patient'
        ];

        $result = $auth->register($registerData);
        
        if ($result['success']) {
            $auth->login($_POST['email'], $_POST['password']);
            header('Location: ' . APP_URL . '/patient/dashboard.php');
            exit;
        } else {
            $errors['general'] = $result['message'] ?? __('registration_failed');
        }
    }
}
?>

<?php include __DIR__ . '/../views/register.php'; ?>
