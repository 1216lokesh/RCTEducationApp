<?php
require_once __DIR__ . '/../../backend/includes/init.php';

Auth::requireLogin();
Auth::requireRole('patient');

$user = $auth->getCurrentUser();
$userId = intval($user['id']);

// Load assigned procedure from database
$procedure = $db->fetchOne("
    SELECT pp.id, pp.procedure_id, pp.group_type, pr.name AS procedure_name, pr.category, pr.description
    FROM patient_procedure pp
    JOIN procedures pr ON pp.procedure_id = pr.id
    WHERE pp.patient_id = {$userId}
    LIMIT 1
");

// Check appointment completion states in database
$apt1_completed = $db->recordExists('consent', 'user_id', $userId);
$scores = $db->fetchOne("SELECT quiz2, quiz3, followup_1week FROM scores WHERE user_id = {$userId}");

$apt2_completed = $scores && $scores['quiz2'] !== null;
$apt3_completed = $scores && $scores['quiz3'] !== null;
$followup_completed = $scores && $scores['followup_1week'] !== null;

$progress = 0;
if ($apt1_completed) $progress++;
if ($apt2_completed) $progress++;
if ($apt3_completed) $progress++;
if ($followup_completed) $progress++;

include __DIR__ . '/../views/patient_dashboard.php';
?>
