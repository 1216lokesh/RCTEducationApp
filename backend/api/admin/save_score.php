<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../bootstrap.php';

if (!$auth->isLoggedIn()) {
    sendJsonResponse(["status" => "error", "message" => "Not authenticated"], 401);
}

$conn = $db->getConnection();

$data    = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data["user_id"])) {
    echo json_encode(["status" => "error", "message" => "No input"]);
    exit;
}

$currentUser = $auth->getCurrentUser();
$role = $currentUser['role'];
$userId = intval($currentUser['id']);
$user_id = intval($data["user_id"]);

if ($role !== 'admin' && $user_id !== $userId) {
    sendJsonResponse(["status" => "error", "message" => "Access denied"], 403);
}

$quiz    = $data["quiz"];
$score   = intval($data["score"]);

// Whitelist allowed quiz columns to prevent SQL injection
$allowed_quizzes = ['quiz1', 'quiz2', 'quiz3', 'followup_1week'];
if (!in_array($quiz, $allowed_quizzes, true)) {
    echo json_encode(["status" => "error", "message" => "Invalid quiz parameter"]);
    exit;
}

$stmt_check = $conn->prepare("SELECT id FROM scores WHERE user_id=?");
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$check = $stmt_check->get_result();

if ($check->num_rows > 0) {
    // Column name is whitelisted, so this is safe from SQL Injection
    $stmt_write = $conn->prepare("UPDATE scores SET $quiz=? WHERE user_id=?");
    $stmt_write->bind_param("ii", $score, $user_id);
} else {
    // Column name is whitelisted, so this is safe from SQL Injection
    $stmt_write = $conn->prepare("INSERT INTO scores (user_id, $quiz) VALUES (?, ?)");
    $stmt_write->bind_param("ii", $user_id, $score);
}
$stmt_check->close();

if ($stmt_write->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error",
                      "message" => $stmt_write->error]);
}
$stmt_write->close();
?>