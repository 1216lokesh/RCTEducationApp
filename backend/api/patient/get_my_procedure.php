<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$data       = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error",
                      "message" => "No input"]);
    exit;
}

$patient_id = $conn->real_escape_string($data["patient_id"]);

$result = $conn->query(
    "SELECT 
        pp.id,
        pp.procedure_id,
        pp.group_type,
        pp.assigned_date,
        pr.name AS procedure_name,
        pr.category,
        pr.description
     FROM patient_procedure pp
     JOIN procedures pr ON pp.procedure_id = pr.id
     WHERE pp.patient_id = '$patient_id'
     LIMIT 1"
);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "status" => "success",
        "data"   => $row
    ]);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "No procedure assigned"
    ]);
}
?>