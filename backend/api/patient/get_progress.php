<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    sendJsonResponse(['success' => true]);
}

if (!$auth->isLoggedIn()) {
    sendJsonResponse(['success' => false, 'message' => 'Not authenticated'], 401);
}

$user = $auth->getCurrentUser();
$userId = intval($user['id']);

// Appointment 1 is completed if consent is given
$apt1_completed = $db->recordExists('consent', 'user_id', $userId);

// Fetch quiz/follow-up scores for other appointments
$scores = $db->fetchOne("SELECT quiz1, quiz2, quiz3, followup_1week FROM scores WHERE user_id = {$userId}");

$apt2_completed = $scores && $scores['quiz2'] !== null;
$apt3_completed = $scores && $scores['quiz3'] !== null;
$followup_completed = $scores && $scores['followup_1week'] !== null;

$progress = 0;
if ($apt1_completed) $progress++;
if ($apt2_completed) $progress++;
if ($apt3_completed) $progress++;
if ($followup_completed) $progress++;

sendJsonResponse([
    'success' => true,
    'progress' => $progress,
    'apt1_completed' => (bool)$apt1_completed,
    'apt2_completed' => (bool)$apt2_completed,
    'apt3_completed' => (bool)$apt3_completed,
    'followup_completed' => (bool)$followup_completed,
    'scores' => $scores
]);
?>
