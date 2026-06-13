<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$raw  = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data || !isset($data["user_id"])) {
    echo json_encode(["status" => "error", 
                      "message" => "No input received"]);
    exit;
}

$user_id    = $conn->real_escape_string($data["user_id"]);
$apt_column = $conn->real_escape_string($data["apt"]);

$check = $conn->query(
    "SELECT id FROM attendance WHERE user_id='$user_id'"
);

if ($check->num_rows > 0) {
    $sql = "UPDATE attendance 
            SET $apt_column='present' 
            WHERE user_id='$user_id'";
} else {
    $sql = "INSERT INTO attendance (user_id, $apt_column) 
            VALUES ('$user_id', 'present')";
}

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error",
                      "message" => $conn->error]);
}
?>