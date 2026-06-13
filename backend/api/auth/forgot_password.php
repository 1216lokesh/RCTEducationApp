<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();
include "PHPMailer.php";
include "SMTP.php";
include "Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$data  = json_decode(file_get_contents("php://input"), true);
$email = $conn->real_escape_string($data["email"]);

$check = $conn->query("SELECT id FROM users WHERE email='$email'");
if ($check->num_rows == 0) {
    echo json_encode(["status" => "error",
                      "message" => "Email not found"]);
    exit;
}

$token = bin2hex(random_bytes(32));
$conn->query("DELETE FROM password_resets WHERE email='$email'");
$conn->query(
    "INSERT INTO password_resets (email, token, created_at)
     VALUES ('$email', '$token', NOW())"
);

$your_ip   = $_SERVER['HTTP_HOST'];
$reset_link = "http://$your_ip/rct_api/auth/reset_password_form.php?token=$token&email=$email";

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'YOUR_GMAIL@gmail.com';
    $mail->Password   = 'YOUR_APP_PASSWORD';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('YOUR_GMAIL@gmail.com', 'ToothTalk App');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'ToothTalk - Password Reset';
    $mail->Body    = "
        <h2 style='color:#1565C0;'>🦷 ToothTalk</h2>
        <h3>Password Reset Request</h3>
        <p>Click the link below to reset your password:</p>
        <p><a href='$reset_link' 
              style='background:#1565C0;color:white;padding:12px 24px;
                     border-radius:6px;text-decoration:none;'>
           Reset My Password
        </a></p>
        <p style='color:#888;font-size:12px;'>
           This link expires in 1 hour. If you did not request this, ignore this email.
        </p>
    ";

    $mail->send();
    echo json_encode(["status"  => "success",
                      "message" => "Reset link sent to your email"]);
} catch (Exception $e) {
    echo json_encode(["status"  => "error",
                      "message" => "Email failed: " . $mail->ErrorInfo]);
}
?>