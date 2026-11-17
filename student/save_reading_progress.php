<?php
/* =========================================================
   SAVE READING PROGRESS — ETS-Async Learning Portal
   ========================================================= */
session_start();
header("Content-Type: application/json; charset=utf-8");

function jsonResponse(array $data): void
{
    echo json_encode($data);
    exit;
}

if (
    !isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) || ($_SESSION["user"]["access"] ?? "") !== "student"
) {
    http_response_code(401);
    jsonResponse(["success" => false, "message" => "Unauthorized"]);
}

require_once "../src/connection.php";
require_once __DIR__ . "/reading_assignment_access.php";

$studentId = trim((string)($_SESSION["user"]["student_id"] ?? ""));
$readingActivityId = (int)($_POST["reading_activity_id"] ?? 0);
$assignmentId = (int)($_POST["assignment_id"] ?? 0);
$readingSeconds = max(0, (float)($_POST["reading_seconds"] ?? 0));

if ($studentId === "" || $readingActivityId < 1 || $assignmentId < 1) {
    http_response_code(400);
    jsonResponse(["success" => false, "message" => "Invalid data"]);
}

if (!studentHasActiveReadingAssignment($mysqli, $studentId, $assignmentId, $readingActivityId)) {
    http_response_code(403);
    jsonResponse(["success" => false, "message" => "This reading assignment is not available to your account."]);
}

$check = $mysqli->prepare("SELECT id, status, reading_seconds FROM reading_activity_progress WHERE reading_activity_id=? AND student_id=? LIMIT 1");
if (!$check) jsonResponse(["success" => false, "message" => "Unable to check progress"]);
$check->bind_param("is", $readingActivityId, $studentId);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
$check->close();
if (!$existing) jsonResponse(["success" => false, "message" => "Progress record not found"]);
if (($existing["status"] ?? "") === "completed") {
    jsonResponse(["success" => true, "message" => "Already completed", "reading_seconds" => (float)$existing["reading_seconds"]]);
}

$update = $mysqli->prepare("UPDATE reading_activity_progress SET reading_seconds=GREATEST(reading_seconds, ?), last_accessed_at=NOW() WHERE id=? AND status <> 'completed'");
if (!$update) jsonResponse(["success" => false, "message" => "Unable to prepare progress update"]);
$update->bind_param("di", $readingSeconds, $existing["id"]);
$ok = $update->execute();
$update->close();
if (!$ok) jsonResponse(["success" => false, "message" => "Unable to save progress"]);
jsonResponse(["success" => true, "reading_seconds" => max($readingSeconds, (float)$existing["reading_seconds"])]);
