<?php
/* =========================================================
   SHOUT BOX - EDIT OR UNSEND OWN MESSAGE
========================================================= */
session_start();
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

function shoutJson(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    shoutJson(405, ["success" => false, "message" => "Method not allowed."]);
}

if (
    !isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) || ($_SESSION["user"]["access"] ?? "") !== "student"
) {
    shoutJson(401, ["success" => false, "message" => "Please log in again."]);
}

$providedToken = (string)($_POST["csrf_token"] ?? "");
$sessionToken = (string)($_SESSION["shout_box_csrf"] ?? "");
if ($sessionToken === "" || !hash_equals($sessionToken, $providedToken)) {
    shoutJson(403, ["success" => false, "message" => "Security check failed. Refresh the page and try again."]);
}

$studentId = trim((string)($_SESSION["user"]["student_id"] ?? $_SESSION["user_id"] ?? ""));
$messageId = filter_input(INPUT_POST, "message_id", FILTER_VALIDATE_INT);
$action = (string)($_POST["action"] ?? "");

if ($studentId === "" || !$messageId || $messageId < 1) {
    shoutJson(422, ["success" => false, "message" => "Invalid message request."]);
}
if (!in_array($action, ["edit", "unsend"], true)) {
    shoutJson(422, ["success" => false, "message" => "Unsupported message action."]);
}

$message = trim((string)($_POST["message"] ?? ""));
if ($action === "edit") {
    if ($message === "") {
        shoutJson(422, ["success" => false, "message" => "A message cannot be empty."]);
    }
    if (mb_strlen($message, "UTF-8") > 500) {
        shoutJson(422, ["success" => false, "message" => "Message must not exceed 500 characters."]);
    }
}

require_once "../src/connection.php";
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    shoutJson(500, ["success" => false, "message" => "Database connection is unavailable."]);
}

try {
    if ($action === "edit") {
        $stmt = $mysqli->prepare("
            UPDATE shout_box_messages
            SET message = ?, edited_at = CURRENT_TIMESTAMP
            WHERE id = ? AND student_id = ? AND is_deleted = 0
        ");
        $stmt->bind_param("sis", $message, $messageId, $studentId);
    } else {
        $stmt = $mysqli->prepare("
            UPDATE shout_box_messages
            SET is_deleted = 1
            WHERE id = ? AND student_id = ? AND is_deleted = 0
        ");
        $stmt->bind_param("is", $messageId, $studentId);
    }

    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected < 1) {
        shoutJson(404, ["success" => false, "message" => "Message not found, already removed, or you do not have permission to change it."]);
    }

    shoutJson(200, ["success" => true, "message" => $action === "edit" ? "Message updated." : "Message unsent."]);
} catch (Throwable $e) {
    error_log("Shout Box manage error: " . $e->getMessage());
    shoutJson(500, ["success" => false, "message" => "Unable to update this message right now."]);
}
