<?php
require_once __DIR__ . '/../bootstrap.php';

if (!$auth->isLoggedIn()) {
    sendJsonResponse(["status" => "error", "message" => "Not authenticated"], 401);
}

$currentUser = $auth->getCurrentUser();
$patient_id = intval($currentUser['id']);

header('Content-Type: application/json');

$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    echo json_encode(["status" => "error", "message" => "No input"]);
    exit;
}

$appointment  = $data['appointment'];
$q1           = $data['q1'];
$q2           = $data['q2'];
$q3           = $data['q3'];

$stmt = $conn->prepare(
    "INSERT INTO baseline_responses (patient_id, appointment, q1, q2, q3, created_at)
     VALUES (?, ?, ?, ?, ?, NOW())"
);
$stmt->bind_param("issss", $patient_id, $appointment, $q1, $q2, $q3);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
$stmt->close();
?>