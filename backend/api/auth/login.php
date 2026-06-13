<?php
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(['success' => true]);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(['success' => false, 'message' => 'Only POST requests are allowed.'], 405);
}

$data = getRequestData();
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$rememberMe = filter_var($data['remember_me'] ?? false, FILTER_VALIDATE_BOOLEAN);

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJsonResponse(['success' => false, 'message' => __('invalid_email')], 400);
}

if (empty($password)) {
    sendJsonResponse(['success' => false, 'message' => __('field_required')], 400);
}

if ($auth->login($email, $password, $rememberMe)) {
    $user = $auth->getCurrentUser();
    sendJsonResponse([
        'success' => true,
        'status' => 'success',
        'message' => __('login') . ' successful',
        'id' => $user['id'],
        'name' => $user['name'] ?? 'User',
        'role' => $user['role'],
        'user' => getUserPayload($user)
    ]);
}

sendJsonResponse(['success' => false, 'message' => __('login_failed')], 401);
