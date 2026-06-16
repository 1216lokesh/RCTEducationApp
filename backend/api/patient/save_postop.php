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

$procedure_id        = intval($data["procedure_id"]);
$followed            = $data["followed_instructions"];
$had_complications   = $data["had_complications"];
$complication_details = $data["complication_details"];

$stmt_check = $conn->prepare(
    "SELECT id FROM postop_adherence 
     WHERE patient_id=?
     AND procedure_id=?"
);
$stmt_check->bind_param("ii", $patient_id, $procedure_id);
$stmt_check->execute();
$check = $stmt_check->get_result();

if ($check->num_rows > 0) {
    $stmt_write = $conn->prepare(
        "UPDATE postop_adherence 
         SET followed_instructions=?,
             had_complications=?,
             complication_details=?
         WHERE patient_id=?
         AND procedure_id=?"
    );
    $stmt_write->bind_param("sssii", $followed, $had_complications, $complication_details, $patient_id, $procedure_id);
} else {
    $stmt_write = $conn->prepare(
        "INSERT INTO postop_adherence 
         (patient_id, procedure_id, 
          followed_instructions, had_complications,
          complication_details)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt_write->bind_param("iisss", $patient_id, $procedure_id, $followed, $had_complications, $complication_details);
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
