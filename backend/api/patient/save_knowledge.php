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
$total        = intval($data["total"]);

$stmt_check = $conn->prepare(
    "SELECT id FROM knowledge_scores 
     WHERE patient_id=? 
     AND procedure_id=?
     AND timepoint=?"
);
$stmt_check->bind_param("iis", $patient_id, $procedure_id, $timepoint);
$stmt_check->execute();
$check = $stmt_check->get_result();

if ($check->num_rows > 0) {
    $stmt_write = $conn->prepare(
        "UPDATE knowledge_scores 
         SET score=?, total=?
         WHERE patient_id=?
         AND procedure_id=?
         AND timepoint=?"
    );
    $stmt_write->bind_param("iiiis", $score, $total, $patient_id, $procedure_id, $timepoint);
} else {
    $stmt_write = $conn->prepare(
        "INSERT INTO knowledge_scores 
         (patient_id, procedure_id, timepoint, 
          score, total)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt_write->bind_param("iiisi", $patient_id, $procedure_id, $timepoint, $score, $total);
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
