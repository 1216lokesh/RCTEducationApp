<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Language.php';
require_once __DIR__ . '/../classes/Auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();
Auth::startSession();
$auth = new Auth($db);
Language::getUserLanguage();

// Helper function for getting translated strings
if (!function_exists('__')) {
    function __($key, $default = '') {
        return Language::get($key, $default);
    }
}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Content-Type: application/json; charset=utf-8');

function sendJsonResponse($payload, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function getJsonInput() {
    $input = file_get_contents('php://input');
    if (empty($input)) {
        return [];
    }
    $data = json_decode($input, true);
    return (is_array($data) ? $data : []);
}

function getRequestData() {
    $data = getJsonInput();
    if (empty($data)) {
        $data = $_POST;
    }
    return $data;
}

function getUserPayload(array $user) {
    $name = $user['name'] ?? '';
    $parts = explode(' ', $name, 2);
    $firstName = $parts[0] ?? '';
    $lastName = $parts[1] ?? '';
    
    return [
        'id' => $user['id'],
        'name' => $name,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $user['email'] ?? '',
        'role' => $user['role'] ?? '',
        'phone' => $user['phone'] ?? null,
        'date_of_birth' => $user['date_of_birth'] ?? null,
        'gender' => $user['gender'] ?? null,
        'language' => $user['language'] ?? DEFAULT_LANGUAGE,
        'status' => $user['status'] ?? null,
        'last_login' => $user['last_login'] ?? null,
    ];
}
