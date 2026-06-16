<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php';
Auth::requireRole('admin');
$conn = $db->getConnection();

$result = $conn->query(
    "SELECT 
        u.name,
        u.email,
        pr.name       AS procedure_name,
        pp.group_type,
        anx.timepoint AS timepoint,
        s.quiz1       AS pre_knowledge,
        s.quiz2       AS post_knowledge,
        anx.score     AS anxiety_score,
        sat.score     AS satisfaction_score
     FROM patient_procedure pp
     JOIN users u       ON pp.patient_id   = u.id
     JOIN procedures pr ON pp.procedure_id = pr.id
     LEFT JOIN scores s ON s.user_id = pp.patient_id
     LEFT JOIN (
         SELECT patient_id,
                MAX(score)     AS score,
                MAX(timepoint) AS timepoint
         FROM anxiety_scores
         GROUP BY patient_id
     ) anx ON anx.patient_id = pp.patient_id
     LEFT JOIN (
         SELECT patient_id,
                MAX(score) AS score
         FROM satisfaction_scores
         GROUP BY patient_id
     ) sat ON sat.patient_id = pp.patient_id
     GROUP BY pp.patient_id
     ORDER BY u.name"
);

if (!$result) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "name"               => $row["name"],
        "email"              => $row["email"],
        "procedure_name"     => $row["procedure_name"],
        "group_type"         => $row["group_type"],
        "timepoint"          => $row["timepoint"]      ?? "not started",
        "pre_knowledge"      => $row["pre_knowledge"]  ?? "0",
        "post_knowledge"     => $row["post_knowledge"] ?? "0",
        "anxiety_score"      => $row["anxiety_score"]  ?? "0",
        "satisfaction_score" => $row["satisfaction_score"] ?? "0"
    ];
}

echo json_encode($data);
?>