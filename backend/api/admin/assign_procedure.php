<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';
Auth::requireRole('admin');
$conn = $db->getConnection();

$data         = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", 
                      "message" => "No input"]);
    exit;
}

$patient_id   = intval($data["patient_id"]);
$procedure_id = intval($data["procedure_id"]);
$assigned_by  = intval($data["assigned_by"]);
$group_type   = $data["group_type"];
$date         = date("Y-m-d");

// Check if already assigned
$stmt_check = $conn->prepare("SELECT id FROM patient_procedure WHERE patient_id=?");
$stmt_check->bind_param("i", $patient_id);
$stmt_check->execute();
$check = $stmt_check->get_result();

if ($check->num_rows > 0) {
    $stmt_write = $conn->prepare(
        "UPDATE patient_procedure 
         SET procedure_id=?,
             assigned_by=?,
             group_type=?,
             assigned_date=?
         WHERE patient_id=?"
    );
    $stmt_write->bind_param("iissi", $procedure_id, $assigned_by, $group_type, $date, $patient_id);
} else {
    $stmt_write = $conn->prepare(
        "INSERT INTO patient_procedure 
         (patient_id, procedure_id, assigned_by, 
          group_type, assigned_date)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt_write->bind_param("iiiss", $patient_id, $procedure_id, $assigned_by, $group_type, $date);
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