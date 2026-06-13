<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(['success' => true]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'message' => 'Only POST requests are allowed.'], 405);
}

$data = getRequestData();
$name = trim($data['name'] ?? '');
if (!empty($name)) {
    $parts = explode(' ', $name, 2);
    $firstName = $parts[0];
    $lastName = $parts[1] ?? '';
} else {
    $firstName = trim($data['first_name'] ?? '');
    $lastName = trim($data['last_name'] ?? '');
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirmPassword = $data['confirm_password'] ?? $password; // If registering via Android, confirm_password is not sent
$phone = trim($data['phone'] ?? '');
$dateOfBirth = trim($data['date_of_birth'] ?? '');
$gender = trim($data['gender'] ?? '');
$language = trim($data['language'] ?? DEFAULT_LANGUAGE);

$errors = [];

if (empty($firstName)) {
    $errors['first_name'] = __('field_required');
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = __('invalid_email');
}
if (empty($password)) {
    $errors['password'] = __('field_required');
}
if ($password !== $confirmPassword) {
    $errors['confirm_password'] = __('password_mismatch');
}

if (!in_array($language, SUPPORTED_LANGUAGES, true)) {
    $language = DEFAULT_LANGUAGE;
}

if (!empty($errors)) {
    sendJsonResponse([
        'success' => false,
        'status' => 'error',
        'message' => 'Validation errors',
        'errors' => $errors
    ], 422);
}

$registerData = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'password' => $password,
    'phone' => $phone ?: null,
    'date_of_birth' => $dateOfBirth ?: null,
    'gender' => $gender ?: null,
    'language' => $language,
    'role' => 'patient'
];

$result = $auth->register($registerData);

if (!$result['success']) {
    sendJsonResponse([
        'success' => false,
        'status' => 'error',
        'message' => $result['message'] ?? __('registration_failed')
    ], 409);
}

if ($auth->login($email, $password)) {
    $user = $auth->getCurrentUser();
    sendJsonResponse([
        'success' => true,
        'status' => 'success',
        'message' => __('register') . ' successful',
        'user' => getUserPayload($user)
    ], 201);
}

sendJsonResponse([
    'success' => false,
    'status' => 'error',
    'message' => __('registration_failed')
], 500);
