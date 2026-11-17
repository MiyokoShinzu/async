<?php
/* =========================================================
   COMPLETE READING ACTIVITY — ETS-Async Learning Portal
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
$clientReadingSeconds = max(0, (float)($_POST["reading_seconds"] ?? 0));

if ($studentId === "" || $readingActivityId < 1 || $assignmentId < 1) {
    http_response_code(400);
    jsonResponse(["success" => false, "message" => "Invalid data"]);
}
if (!studentHasActiveReadingAssignment($mysqli, $studentId, $assignmentId, $readingActivityId)) {
    http_response_code(403);
    jsonResponse(["success" => false, "message" => "This reading assignment is not available to your account."]);
}

$activity = $mysqli->prepare("SELECT r.required_reading_minutes FROM class_code_reading_assignments a JOIN reading_activity_table r ON r.id=a.reading_activity_id WHERE a.id=? AND a.reading_activity_id=? LIMIT 1");
if (!$activity) jsonResponse(["success" => false, "message" => "Unable to load reading requirement"]);
$activity->bind_param("ii", $assignmentId, $readingActivityId);
$activity->execute();
$activityRow = $activity->get_result()->fetch_assoc();
$activity->close();
if (!$activityRow) jsonResponse(["success" => false, "message" => "Reading activity not found"]);
$requiredSeconds = max(1, (int)($activityRow["required_reading_minutes"] ?? 30)) * 60;

$check = $mysqli->prepare("SELECT id, status, reading_seconds FROM reading_activity_progress WHERE reading_activity_id=? AND student_id=? LIMIT 1");
if (!$check) jsonResponse(["success" => false, "message" => "Unable to check progress"]);
$check->bind_param("is", $readingActivityId, $studentId);
$check->execute();
$progress = $check->get_result()->fetch_assoc();
$check->close();
if (!$progress) jsonResponse(["success" => false, "message" => "Progress record not found"]);
if (($progress["status"] ?? "") === "completed") jsonResponse(["success" => true, "message" => "Already completed"]);

$readingSeconds = max((float)$progress["reading_seconds"], $clientReadingSeconds);
if ($readingSeconds < $requiredSeconds) {
    jsonResponse(["success" => false, "message" => "Required reading time has not been reached", "reading_seconds" => $readingSeconds, "required_seconds" => $requiredSeconds]);
}
$update = $mysqli->prepare("UPDATE reading_activity_progress SET status='completed', reading_seconds=GREATEST(reading_seconds, ?), completed_at=COALESCE(completed_at,NOW()), last_accessed_at=NOW() WHERE id=? AND status <> 'completed'");
if (!$update) jsonResponse(["success" => false, "message" => "Unable to prepare completion update"]);
$update->bind_param("di", $readingSeconds, $progress["id"]);
$ok = $update->execute();
$update->close();
if (!$ok) jsonResponse(["success" => false, "message" => "Unable to complete reading activity"]);
jsonResponse(["success" => true, "message" => "Reading activity completed", "reading_seconds" => $readingSeconds, "required_seconds" => $requiredSeconds]);
