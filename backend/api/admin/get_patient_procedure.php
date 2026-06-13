<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$data    = json_decode(file_get_contents("php://input"), true);
$user_id = $conn->real_escape_string($data["user_id"]);

$result = $conn->query(
    "SELECT pp.procedure_id, pp.group_type,
            p.name AS procedure_name,
            p.category, p.description
     FROM patient_procedure pp
     JOIN procedures p ON pp.procedure_id = p.id
     WHERE pp.patient_id = '$user_id'
     LIMIT 1"
);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        "status"         => "success",
        "procedure_id"   => $row["procedure_id"],
        "procedure_name" => $row["procedure_name"],
        "category"       => $row["category"],
        "description"    => $row["description"],
        "group_type"     => $row["group_type"]
    ]);
} else {
    echo json_encode([
        "status"  => "not_assigned",
        "message" => "No procedure assigned yet"
    ]);
}
?>