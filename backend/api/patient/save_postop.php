<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$data         = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status" => "error",
                      "message" => "No input"]);
    exit;
}

$patient_id          = $conn->real_escape_string($data["patient_id"]);
$procedure_id        = $conn->real_escape_string($data["procedure_id"]);
$followed            = $conn->real_escape_string($data["followed_instructions"]);
$had_complications   = $conn->real_escape_string($data["had_complications"]);
$complication_details = $conn->real_escape_string($data["complication_details"]);

$check = $conn->query(
    "SELECT id FROM postop_adherence 
     WHERE patient_id='$patient_id'
     AND procedure_id='$procedure_id'"
);

if ($check->num_rows > 0) {
    $sql = "UPDATE postop_adherence 
            SET followed_instructions='$followed',
                had_complications='$had_complications',
                complication_details='$complication_details'
            WHERE patient_id='$patient_id'
            AND procedure_id='$procedure_id'";
} else {
    $sql = "INSERT INTO postop_adherence 
            (patient_id, procedure_id, 
             followed_instructions, had_complications,
             complication_details)
            VALUES ('$patient_id','$procedure_id',
                    '$followed','$had_complications',
                    '$complication_details')";
}

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error",
                      "message" => $conn->error]);
}
?>
