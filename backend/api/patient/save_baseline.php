<?php
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$patient_id   = $conn->real_escape_string($data['patient_id']);
$appointment  = $conn->real_escape_string($data['appointment']);
$q1           = $conn->real_escape_string($data['q1']);
$q2           = $conn->real_escape_string($data['q2']);
$q3           = $conn->real_escape_string($data['q3']);

$sql = "INSERT INTO baseline_responses (patient_id, appointment, q1, q2, q3, created_at)
        VALUES ('$patient_id', '$appointment', '$q1', '$q2', '$q3', NOW())";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}
?>