<?php
session_start();
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

function shoutJson(int $status, array $payload): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if (
    !isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) || ($_SESSION["user"]["access"] ?? "") !== "student"
) {
    shoutJson(401, ["success" => false, "message" => "Please log in again."]);
}

require_once "../src/connection.php";
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    shoutJson(500, ["success" => false, "message" => "Database connection is unavailable."]);
}

try {
    $result = $mysqli->query("
        SELECT id, student_id AS sender_student_id, sender_name, sender_initials,
               message, DATE_FORMAT(created_at, '%Y-%m-%d %H:%i:%s') AS created_at
        FROM shout_box_messages
        WHERE is_deleted = 0
        ORDER BY id DESC
        LIMIT 4
    ");

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            "id" => (int)$row["id"],
            "sender_student_id" => (string)$row["sender_student_id"],
            "sender_name" => (string)$row["sender_name"],
            "sender_initials" => (string)$row["sender_initials"],
            "message" => (string)$row["message"],
            "created_at" => (string)$row["created_at"]
        ];
    }

    shoutJson(200, ["success" => true, "messages" => array_reverse($messages)]);
} catch (Throwable $e) {
    error_log("Shout Box fetch error: " . $e->getMessage());
    shoutJson(500, ["success" => false, "message" => "Unable to retrieve messages."]);
}
