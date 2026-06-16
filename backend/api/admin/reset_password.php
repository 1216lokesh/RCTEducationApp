<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';
Auth::requireRole('admin');
$conn = $db->getConnection();

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data["user_id"]) || !isset($data["new_password"])) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$user_id  = intval($data["user_id"]);
$new_pass = password_hash($data["new_password"], PASSWORD_DEFAULT);

$stmt_check = $conn->prepare("SELECT id FROM users WHERE id=?");
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if (!$result || $result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Patient not found"]);
    $stmt_check->close();
    exit;
}
$stmt_check->close();

$stmt_write = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$stmt_write->bind_param("si", $new_pass, $user_id);

if ($stmt_write->execute()) {
    echo json_encode(["status" => "success", "message" => "Password reset successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt_write->error]);
}
$stmt_write->close();
?>