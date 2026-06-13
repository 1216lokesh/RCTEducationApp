<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();
include __DIR__ . "/../auth/PHPMailer.php";
include __DIR__ . "/../auth/SMTP.php";
include __DIR__ . "/../auth/Exception.php";


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data["user_id"])) {
    echo json_encode(["status" => "error", "message" => "No input received"]);
    exit;
}

$user_id = $conn->real_escape_string($data["user_id"]);

$result = $conn->query("SELECT name, email FROM users WHERE id='$user_id'");

if (!$result || $result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Patient not found", "user_id" => $user_id]);
    exit;
}

$user   = $result->fetch_assoc();
$email  = $user["email"];
$name   = $user["name"];

$otp    = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

$conn->query("UPDATE users SET otp='$otp', otp_expiry='$expiry' WHERE id='$user_id'");

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'lokeshlokeshlokey2@gmail.com';
    $mail->Password   = 'ohys bpvw munx mays';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('lokeshlokeshlokey2@gmail.com', 'RCT Hospital');
    $mail->addAddress($email, $name);
    $mail->isHTML(true);
    $mail->Subject = 'RCT Hospital - Password Reset OTP';
    $mail->Body    = "
        <h2 style='color:#1565C0;'>🏥 RCT Hospital</h2>
        <h3>Password Reset Request</h3>
        <p>Dear <b>$name</b>,</p>
        <p>Your admin has requested a password reset for your account.</p>
        <h1 style='color:#1565C0; letter-spacing:8px;'>$otp</h1>
        <p>This OTP is valid for <b>10 minutes</b> only.</p>
        <p style='color:#888; font-size:12px;'>
            If you did not request this, please contact the hospital immediately.
        </p>
    ";

    $mail->send();
    echo json_encode(["status" => "success", "message" => "OTP sent to patient email"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Email failed: " . $mail->ErrorInfo]);
}
?>