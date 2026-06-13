<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$data         = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error", 
                      "message" => "No input"]);
    exit;
}

$patient_id   = $conn->real_escape_string($data["patient_id"]);
$procedure_id = $conn->real_escape_string($data["procedure_id"]);
$assigned_by  = $conn->real_escape_string($data["assigned_by"]);
$group_type   = $conn->real_escape_string($data["group_type"]);
$date         = date("Y-m-d");

// Check if already assigned
$check = $conn->query(
    "SELECT id FROM patient_procedure 
     WHERE patient_id='$patient_id'"
);

if ($check->num_rows > 0) {
    $sql = "UPDATE patient_procedure 
            SET procedure_id='$procedure_id',
                assigned_by='$assigned_by',
                group_type='$group_type',
                assigned_date='$date'
            WHERE patient_id='$patient_id'";
} else {
    $sql = "INSERT INTO patient_procedure 
            (patient_id, procedure_id, assigned_by, 
             group_type, assigned_date)
            VALUES ('$patient_id','$procedure_id',
                    '$assigned_by','$group_type','$date')";
}

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error",
                      "message" => $conn->error]);
}
?>