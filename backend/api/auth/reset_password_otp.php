<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$data     = json_decode(file_get_contents("php://input"), true);
$user_id  = $conn->real_escape_string($data["user_id"]);
$otp      = $conn->real_escape_string($data["otp"]);
$new_pass = password_hash($data["new_password"], PASSWORD_DEFAULT);

$result = $conn->query("SELECT otp, otp_expiry FROM users WHERE id='$user_id'");
$user   = $result->fetch_assoc();

if ($user["otp"] !== $otp) {
    echo json_encode(["status" => "error", "message" => "Invalid OTP"]);
    exit;
}

if (strtotime($user["otp_expiry"]) < time()) {
    echo json_encode(["status" => "error", "message" => "OTP expired"]);
    exit;
}

$conn->query("UPDATE users SET password='$new_pass', otp=NULL, otp_expiry=NULL WHERE id='$user_id'");
echo json_encode(["status" => "success", "message" => "Password reset successfully"]);
?>