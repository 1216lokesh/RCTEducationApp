<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$result = $conn->query(
    "SELECT u.name, c.consent_given, c.consent_date 
     FROM consent c 
     JOIN users u ON c.user_id = u.id"
);

if (!$result) {
    echo json_encode([["name" => "Error", 
                       "consent_given" => $conn->error, 
                       "consent_date" => ""]]);
    exit;
}

$consent = [];
while ($row = $result->fetch_assoc()) {
    $consent[] = $row;
}

if (empty($consent)) {
    echo json_encode([]);
} else {
    echo json_encode($consent);
}
?>