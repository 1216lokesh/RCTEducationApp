<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data["user_id"]) || !isset($data["new_password"])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$user_id  = $conn->real_escape_string($data["user_id"]);
$new_pass = password_hash($data["new_password"], PASSWORD_DEFAULT);

$result = $conn->query("SELECT id FROM users WHERE id='$user_id'");

if (!$result || $result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Patient not found"]);
    exit;
}

$conn->query("UPDATE users SET password='$new_pass' WHERE id='$user_id'");
echo json_encode(["status" => "success", "message" => "Password reset successfully"]);
?>