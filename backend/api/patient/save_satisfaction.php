<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';

if (!$auth->isLoggedIn()) {
    sendJsonResponse(["status" => "error", "message" => "Not authenticated"], 401);
}

$currentUser = $auth->getCurrentUser();
$patient_id = intval($currentUser['id']);

$conn = $db->getConnection();

$data         = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error",
                      "message" => "No input"]);
    exit;
}

$procedure_id = intval($data["procedure_id"]);
$timepoint    = $data["timepoint"];
$score        = intval($data["score"]);

$stmt_check = $conn->prepare(
    "SELECT id FROM satisfaction_scores 
     WHERE patient_id=?
     AND procedure_id=?
     AND timepoint=?"
);
$stmt_check->bind_param("iis", $patient_id, $procedure_id, $timepoint);
$stmt_check->execute();
$check = $stmt_check->get_result();

if ($check->num_rows > 0) {
    $stmt_write = $conn->prepare(
        "UPDATE satisfaction_scores 
         SET score=?
         WHERE patient_id=?
         AND procedure_id=?
         AND timepoint=?"
    );
    $stmt_write->bind_param("iiis", $score, $patient_id, $procedure_id, $timepoint);
} else {
    $stmt_write = $conn->prepare(
        "INSERT INTO satisfaction_scores 
         (patient_id, procedure_id, timepoint, score)
         VALUES (?, ?, ?, ?)"
    );
    $stmt_write->bind_param("iisi", $patient_id, $procedure_id, $timepoint, $score);
}
$stmt_check->close();

if ($stmt_write->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error",
                      "message" => $stmt_write->error]);
}
$stmt_write->close();
?>