<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php';
Auth::requireRole('admin');
$conn = $db->getConnection();

$result = $conn->query(
    "SELECT id, name, email, phone, status, created_at 
     FROM users 
     WHERE role='patient'"
);

$patients = [];
while ($row = $result->fetch_assoc()) {
    $patients[] = [
        "id"         => (int)$row["id"],
        "name"       => $row["name"],
        "email"      => $row["email"],
        "phone"      => $row["phone"],
        "status"     => $row["status"],
        "created_at" => $row["created_at"]
    ];
}

echo json_encode($patients);
?>