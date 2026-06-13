<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';

if (!$auth->isLoggedIn() || !$auth->hasRole('admin')) {
    sendJsonResponse(['success' => false, 'message' => 'Access denied'], 403);
}

$patientId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($patientId <= 0) {
    sendJsonResponse(['success' => false, 'message' => 'Invalid patient ID'], 400);
}

// 1. Patient profile
$patient = $db->fetchOne("SELECT id, name, email, phone, status, created_at FROM users WHERE id = {$patientId} AND role = 'patient'");
if (!$patient) {
    sendJsonResponse(['success' => false, 'message' => 'Patient not found'], 404);
}

// 2. Assigned procedure
$assignedProc = $db->fetchOne("
    SELECT pp.procedure_id, pp.group_type, pp.assigned_date, pr.name, pr.category, pr.description
    FROM patient_procedure pp
    JOIN procedures pr ON pp.procedure_id = pr.id
    WHERE pp.patient_id = {$patientId}
    LIMIT 1
");

// 3. Consent logs
$consentLog = $db->fetchOne("SELECT consent_given, consent_date, created_at FROM consent WHERE user_id = {$patientId}");

// 4. Scores logs
$scoresLog = $db->fetchOne("SELECT quiz1, quiz2, quiz3, followup_1week FROM scores WHERE user_id = {$patientId}");

// 5. Attendance logs
$attendanceLog = $db->fetchOne("SELECT apt1, apt2, apt3, apt4 FROM attendance WHERE user_id = {$patientId}");

// 6. Baseline responses
$baselineResponses = $db->fetchAll("SELECT appointment, q1, q2, q3, created_at FROM baseline_responses WHERE patient_id = {$patientId} ORDER BY created_at ASC");

// 7. Anxiety scores
$anxietyScores = $db->fetchAll("SELECT timepoint, score, created_at FROM anxiety_scores WHERE patient_id = {$patientId} ORDER BY timepoint ASC");

// 8. Satisfaction scores
$satisfactionScores = $db->fetchAll("SELECT timepoint, score, created_at FROM satisfaction_scores WHERE patient_id = {$patientId} ORDER BY timepoint ASC");

sendJsonResponse([
    'success' => true,
    'patient' => $patient,
    'assignedProcedure' => $assignedProc ? $assignedProc : null,
    'consent' => $consentLog ? $consentLog : null,
    'scores' => $scoresLog ? $scoresLog : null,
    'attendance' => $attendanceLog ? $attendanceLog : null,
    'baseline' => $baselineResponses,
    'anxiety' => $anxietyScores,
    'satisfaction' => $satisfactionScores
]);
?>
