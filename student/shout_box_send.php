<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

function shoutJson(int $status, array $payload): void {
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

$message = trim((string)($_POST["message"] ?? ""));
if ($message === "") {
    shoutJson(422, ["success" => false, "message" => "Please enter a message."]);
}
if (mb_strlen($message, "UTF-8") > 500) {
    shoutJson(422, ["success" => false, "message" => "Message must not exceed 500 characters."]);
}

$user = $_SESSION["user"];
$studentId = trim((string)($user["student_id"] ?? $_SESSION["user_id"] ?? ""));
$firstName = trim((string)($user["first_name"] ?? $user["firstname"] ?? $user["name"] ?? ""));
$lastName = trim((string)($user["last_name"] ?? $user["lastname"] ?? ""));
$middleInitial = trim((string)($user["middle_initial"] ?? ""));
$extensionName = trim((string)($user["extension_name"] ?? ""));

$senderName = trim(
    $firstName . " " .
    ($middleInitial !== "" ? $middleInitial . ". " : "") .
    $lastName .
    ($extensionName !== "" ? " " . $extensionName : "")
);
if ($senderName === "") $senderName = trim((string)($user["username"] ?? "Student"));
if ($senderName === "") $senderName = "Student";

$senderInitials = "";
if ($firstName !== "") $senderInitials .= strtoupper(substr($firstName, 0, 1));
if ($lastName !== "") $senderInitials .= strtoupper(substr($lastName, 0, 1));
if ($senderInitials === "") $senderInitials = "ST";

if ($studentId === "") {
    shoutJson(400, ["success" => false, "message" => "Student account ID is missing."]);
}

require_once "../src/connection.php";
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    shoutJson(500, ["success" => false, "message" => "Database connection is unavailable."]);
}

try {
    $stmt = $mysqli->prepare("
        INSERT INTO shout_box_messages (student_id, sender_name, sender_initials, message)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("ssss", $studentId, $senderName, $senderInitials, $message);
    $stmt->execute();
    $newId = $stmt->insert_id;
    $stmt->close();

    shoutJson(201, ["success" => true, "id" => (int)$newId]);
} catch (Throwable $e) {
    error_log("Shout Box send error: " . $e->getMessage());
    shoutJson(500, ["success" => false, "message" => "Unable to save your message."]);
}
