<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(['success' => true]);
}

if ($auth->isLoggedIn()) {
    $user = $auth->getCurrentUser();
    sendJsonResponse([
        'success' => true,
        'loggedIn' => true,
        'user' => getUserPayload($user)
    ]);
} else {
    sendJsonResponse([
        'success' => false,
        'loggedIn' => false,
        'message' => 'Not authenticated'
    ]);
}
?>
