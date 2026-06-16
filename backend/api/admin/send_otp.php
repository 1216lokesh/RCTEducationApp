<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';
Auth::requireRole('admin');
$conn = $db->getConnection();
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

$user_id = intval($data["user_id"]);

$stmt_check = $conn->prepare("SELECT name, email FROM users WHERE id=?");
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if (!$result || $result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Patient not found", "user_id" => $user_id]);
    $stmt_check->close();
    exit;
}

$user   = $result->fetch_assoc();
$email  = $user["email"];
$name   = $user["name"];
$stmt_check->close();

$otp    = rand(100000, 999999);
$expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

$stmt_write = $conn->prepare("UPDATE users SET otp=?, otp_expiry=? WHERE id=?");
$stmt_write->bind_param("ssi", $otp, $expiry, $user_id);
$stmt_write->execute();
$stmt_write->close();

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