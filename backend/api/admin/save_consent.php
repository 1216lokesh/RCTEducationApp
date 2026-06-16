<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/../bootstrap.php';

if (!$auth->isLoggedIn()) {
    sendJsonResponse(["status" => "error", "message" => "Not authenticated"], 401);
}

$conn = $db->getConnection();

$data = json_decode(file_get_contents("php://input"), true);

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

$consent_given = $data["consent_given"];
$consent_date  = date("Y-m-d");

$stmt_check = $conn->prepare("SELECT id FROM consent WHERE user_id=?");
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$check = $stmt_check->get_result();

if ($check->num_rows > 0) {
    $stmt_write = $conn->prepare(
        "UPDATE consent 
         SET consent_given=?,
             consent_date=?,
             created_at=NOW()
         WHERE user_id=?"
    );
    $stmt_write->bind_param("ssi", $consent_given, $consent_date, $user_id);
} else {
    $stmt_write = $conn->prepare(
        "INSERT INTO consent 
         (user_id, consent_given, consent_date, created_at) 
         VALUES (?, ?, ?, NOW())"
    );
    $stmt_write->bind_param("iss", $user_id, $consent_given, $consent_date);
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