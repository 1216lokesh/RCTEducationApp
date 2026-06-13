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
$timepoint    = $conn->real_escape_string($data["timepoint"]);
$score        = $conn->real_escape_string($data["score"]);
$total        = $conn->real_escape_string($data["total"]);

$check = $conn->query(
    "SELECT id FROM knowledge_scores 
     WHERE patient_id='$patient_id' 
     AND procedure_id='$procedure_id'
     AND timepoint='$timepoint'"
);

if ($check->num_rows > 0) {
    $sql = "UPDATE knowledge_scores 
            SET score='$score', total='$total'
            WHERE patient_id='$patient_id'
            AND procedure_id='$procedure_id'
            AND timepoint='$timepoint'";
} else {
    $sql = "INSERT INTO knowledge_scores 
            (patient_id, procedure_id, timepoint, 
             score, total)
            VALUES ('$patient_id','$procedure_id',
                    '$timepoint','$score','$total')";
}

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error",
                      "message" => $conn->error]);
}
?>
