<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php';

if (!$auth->isLoggedIn()) {
    sendJsonResponse(["status" => "error", "message" => "Not authenticated"], 401);
}

$conn = $db->getConnection();

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data["user_id"]) || !isset($data["apt"])) {
    echo json_encode(["status" => "error", 
                      "message" => "No input received"]);
    exit;
}

$currentUser = $auth->getCurrentUser();
$role = $currentUser['role'];
$userId = intval($currentUser['id']);
$user_id = intval($data["user_id"]);

if ($role !== 'admin' && $user_id !== $userId) {
    sendJsonResponse(["status" => "error", "message" => "Access denied"], 403);
}

$apt_column = $data["apt"];
$allowed_apts = ['apt1', 'apt2', 'apt3', 'apt4'];
if (!in_array($apt_column, $allowed_apts, true)) {
    echo json_encode(["status" => "error", "message" => "Invalid appointment field"]);
    exit;
}

$stmt_check = $conn->prepare("SELECT id FROM attendance WHERE user_id=?");
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$check = $stmt_check->get_result();

if ($check->num_rows > 0) {
    // Column name is whitelisted, so this is safe from SQL Injection
    $stmt_write = $conn->prepare("UPDATE attendance SET $apt_column='present' WHERE user_id=?");
    $stmt_write->bind_param("i", $user_id);
} else {
    // Column name is whitelisted, so this is safe from SQL Injection
    $stmt_write = $conn->prepare("INSERT INTO attendance (user_id, $apt_column) VALUES (?, 'present')");
    $stmt_write->bind_param("i", $user_id);
}
$stmt_check->close();

if ($stmt_write->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error",
                      "message" => $stmt_write->error]);
}
$stmt_write->close();
?>