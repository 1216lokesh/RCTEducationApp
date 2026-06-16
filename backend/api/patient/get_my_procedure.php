<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';

if (!$auth->isLoggedIn()) {
    sendJsonResponse(["status" => "error", "message" => "Not authenticated"], 401);
}

$currentUser = $auth->getCurrentUser();
$patient_id = intval($currentUser['id']);

$conn = $db->getConnection();

$stmt = $conn->prepare(
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
     WHERE pp.patient_id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

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
$stmt->close();
?>