<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php'; $conn = $db->getConnection();

$exportDir = "../exports/";

if (!is_dir($exportDir)) {
    mkdir($exportDir, 0777, true);
}

$filename = "export_" . date("Y-m-d") . ".csv";
$filepath = $exportDir . $filename;
$file     = fopen($filepath, "w");

fputcsv($file, [
    "Name", "Email", "Phone",
    "Quiz1", "Quiz2", "Quiz3",
    "Consent", "Apt1", "Apt2", "Apt3", "Apt4"
]);

$result = $conn->query(
    "SELECT u.name, u.email, u.phone,
            COALESCE(s.quiz1, 0) as quiz1,
            COALESCE(s.quiz2, 0) as quiz2,
            COALESCE(s.quiz3, 0) as quiz3,
            COALESCE(c.consent_given, 'no') as consent_given,
            COALESCE(a.apt1, 'absent') as apt1,
            COALESCE(a.apt2, 'absent') as apt2,
            COALESCE(a.apt3, 'absent') as apt3,
            COALESCE(a.apt4, 'absent') as apt4
     FROM users u
     LEFT JOIN scores s     ON s.user_id = u.id
     LEFT JOIN consent c    ON c.user_id = u.id
     LEFT JOIN attendance a ON a.user_id = u.id
     WHERE u.role = 'patient'"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($file, [
            $row["name"],
            $row["email"],
            $row["phone"],
            $row["quiz1"],
            $row["quiz2"],
            $row["quiz3"],
            $row["consent_given"],
            $row["apt1"],
            $row["apt2"],
            $row["apt3"],
            $row["apt4"]
        ]);
    }
}

fclose($file);

echo json_encode([
    "status" => "success",
    "file"   => $filename,
    "path"   => $filepath
]);
?>