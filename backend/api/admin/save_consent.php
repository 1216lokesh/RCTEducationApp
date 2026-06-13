<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "No input"]);
    exit;
}

$user_id       = $conn->real_escape_string($data["user_id"]);
$consent_given = $conn->real_escape_string($data["consent_given"]);
$consent_date  = date("Y-m-d");

$check = $conn->query(
    "SELECT id FROM consent WHERE user_id='$user_id'"
);

if ($check->num_rows > 0) {
    $sql = "UPDATE consent 
            SET consent_given='$consent_given',
                consent_date='$consent_date',
                created_at=NOW()
            WHERE user_id='$user_id'";
} else {
    $sql = "INSERT INTO consent 
            (user_id, consent_given, consent_date, created_at) 
            VALUES ('$user_id', '$consent_given', 
                    '$consent_date', NOW())";
}

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error",
                      "message" => $conn->error]);
}
?>