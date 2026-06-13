<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(['success' => true]);
}

if (!$auth->isLoggedIn() || !$auth->hasRole('admin')) {
    sendJsonResponse(['success' => false, 'message' => 'Access denied'], 403);
}

$totalPatients = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'patient'")['count'];
$totalAssigned = $db->fetchOne("SELECT COUNT(*) as count FROM patient_procedure")['count'];
$consentCount = $db->fetchOne("SELECT COUNT(*) as count FROM consent WHERE consent_given = 'yes'")['count'];

$scores = $db->fetchAll("SELECT quiz2, quiz3, followup_1week FROM scores");
$quiz2Count = 0;
$quiz3Count = 0;
$followupCount = 0;
foreach ($scores as $s) {
    if ($s['quiz2'] !== null) $quiz2Count++;
    if ($s['quiz3'] !== null) $quiz3Count++;
    if ($s['followup_1week'] !== null) $followupCount++;
}

$completedSteps = $consentCount + $quiz2Count + $quiz3Count + $followupCount;
$totalExpectedSteps = $totalPatients * 4;

$patients = $db->fetchAll("SELECT id, name, email, phone, created_at FROM users WHERE role = 'patient' ORDER BY created_at DESC LIMIT 10");

sendJsonResponse([
    'success' => true,
    'totalPatients' => (int)$totalPatients,
    'totalAssigned' => (int)$totalAssigned,
    'consentCount' => (int)$consentCount,
    'completedSteps' => (int)$completedSteps,
    'totalExpectedSteps' => (int)$totalExpectedSteps,
    'recentPatients' => $patients
]);
?>
