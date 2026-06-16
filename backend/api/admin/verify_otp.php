<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';

if (!$auth->isLoggedIn()) {
    sendJsonResponse(["status" => "error", "message" => "Not authenticated"], 401);
}

$conn = $db->getConnection();

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data["user_id"]) || !isset($data["otp"])) {
    echo json_encode(["status" => "error", "message" => "No input received"]);
    exit;
}

$user_id = intval($data["user_id"]);
$otp     = $data["otp"];

$stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Patient not found"]);
    $stmt->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

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