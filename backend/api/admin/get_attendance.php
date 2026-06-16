<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php';
Auth::requireRole('admin');
$conn = $db->getConnection();

$sql = "SELECT u.name, a.apt1, a.apt2, a.apt3, a.apt4 
        FROM attendance a 
        INNER JOIN users u ON a.user_id = u.id";

$result = $conn->query($sql);

$attendance = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $attendance[] = [
            "name" => $row["name"],
            "apt1" => $row["apt1"],
            "apt2" => $row["apt2"],
            "apt3" => $row["apt3"],
            "apt4" => $row["apt4"]
        ];
    }
}

echo json_encode($attendance);
?>