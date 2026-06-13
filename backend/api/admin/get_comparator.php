<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$result = $conn->query(
    "SELECT 
        u.name,
        u.email,
        u.phone,
        pr.name AS procedure_name,
        pp.group_type,
        pp.assigned_date
     FROM patient_procedure pp
     JOIN users u ON pp.patient_id = u.id
     JOIN procedures pr ON pp.procedure_id = pr.id
     WHERE pp.group_type = 'comparator'
     ORDER BY pp.assigned_date DESC"
);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>