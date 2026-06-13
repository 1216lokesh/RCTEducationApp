<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data["user_id"]) || !isset($data["otp"])) {
    echo json_encode(["status" => "error", "message" => "No input received"]);
    exit;
}

$user_id = $conn->real_escape_string($data["user_id"]);
$otp     = $conn->real_escape_string($data["otp"]);

$result = $conn->query("SELECT otp, otp_expiry FROM users WHERE id='$user_id'");

if (!$result || $result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Patient not found"]);
    exit;
}

$user = $result->fetch_assoc();

if ((string)$user["otp"] !== (string)$otp) {
    echo json_encode(["status" => "error", "message" => "Invalid OTP"]);
    exit;
}

if (strtotime($user["otp_expiry"]) < time()) {
    echo json_encode(["status" => "error", "message" => "OTP expired. Please request again."]);
    exit;
}

echo json_encode(["status" => "success", "message" => "OTP verified"]);
?>