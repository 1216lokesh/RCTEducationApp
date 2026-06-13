<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$sql = "SELECT u.name, s.quiz1, s.quiz2, s.quiz3 
        FROM scores s 
        INNER JOIN users u ON s.user_id = u.id";

$result = $conn->query($sql);

$scores = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $scores[] = [
            "name"  => $row["name"],
            "quiz1" => $row["quiz1"],
            "quiz2" => $row["quiz2"],
            "quiz3" => $row["quiz3"]
        ];
    }
}

echo json_encode($scores);
?>