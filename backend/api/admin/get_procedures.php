<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$result = $conn->query(
    "SELECT id, name, category, description 
     FROM procedures 
     ORDER BY category, name"
);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>