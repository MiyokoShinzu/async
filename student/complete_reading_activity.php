<?php

/* =========================================================
   COMPLETE READING ACTIVITY
   ETS-Async Learning Portal
   ========================================================= */

session_start();

header("Content-Type: application/json");


/* =========================================================
   AUTHENTICATION
   ========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized"
    ]);

    exit;
}


/* =========================================================
   DATABASE
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   STUDENT
   ========================================================= */

$studentId =
    $_SESSION["user"]["student_id"] ?? "";


/* =========================================================
   READING ACTIVITY ID
   ========================================================= */

$readingActivityId =
    isset($_POST["reading_activity_id"])
    ? (int)$_POST["reading_activity_id"]
    : 0;


if (
    $readingActivityId <= 0 ||
    $studentId === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid data"
    ]);

    exit;
}


/* =========================================================
   GET REQUIRED READING TIME
   ========================================================= */

$activitySQL = "
    SELECT
        required_reading_minutes

    FROM reading_activity_table

    WHERE
        id = ?

    LIMIT 1
";


$activityStmt =
    $mysqli->prepare(
        $activitySQL
    );


if (!$activityStmt) {

    echo json_encode([
        "success" => false,
        "message" => $mysqli->error
    ]);

    exit;
}


$activityStmt->bind_param(
    "i",
    $readingActivityId
);


$activityStmt->execute();


$activityResult =
    $activityStmt->get_result();


$activity =
    $activityResult->fetch_assoc();


$activityStmt->close();


/* =========================================================
   ACTIVITY NOT FOUND
   ========================================================= */

if (!$activity) {

    echo json_encode([
        "success" => false,
        "message" => "Reading activity not found"
    ]);

    exit;
}


/* =========================================================
   REQUIRED READING SECONDS
   ========================================================= */

$requiredReadingMinutes =
    (int)(
        $activity["required_reading_minutes"]
        ?? 30
    );


$requiredReadingSeconds =
    $requiredReadingMinutes * 60;


/* =========================================================
   GET EXISTING PROGRESS
   ========================================================= */

$checkSQL = "
    SELECT

        id,
        status,
        reading_seconds

    FROM reading_activity_progress

    WHERE

        reading_activity_id = ?

        AND student_id = ?

    LIMIT 1
";


$checkStmt =
    $mysqli->prepare(
        $checkSQL
    );


if (!$checkStmt) {

    echo json_encode([
        "success" => false,
        "message" => $mysqli->error
    ]);

    exit;
}


$checkStmt->bind_param(
    "is",
    $readingActivityId,
    $studentId
);


$checkStmt->execute();


$result =
    $checkStmt->get_result();


$progress =
    $result->fetch_assoc();


$checkStmt->close();


/* =========================================================
   NO RECORD
   ========================================================= */

if (!$progress) {

    echo json_encode([
        "success" => false,
        "message" => "Progress record not found"
    ]);

    exit;
}


/* =========================================================
   ALREADY COMPLETED
   ========================================================= */

if (
    ($progress["status"] ?? "") ===
    "completed"
) {

    echo json_encode([
        "success" => true,
        "message" => "Already completed"
    ]);

    exit;
}


/* =========================================================
   ACTUAL READING TIME
   ========================================================= */

$readingSeconds =
    (float)(
        $progress["reading_seconds"]
        ?? 0
    );


/* =========================================================
   CHECK REQUIRED TIME
   ========================================================= */

if (
    $readingSeconds <
    $requiredReadingSeconds
) {

    echo json_encode([
        "success" => false,
        "message" => "Required reading time has not been reached",
        "reading_seconds" => $readingSeconds,
        "required_seconds" => $requiredReadingSeconds
    ]);

    exit;
}


/* =========================================================
   COMPLETE
   ========================================================= */

$updateSQL = "
    UPDATE reading_activity_progress

    SET

        status = 'completed',

        reading_seconds =
            GREATEST(
                reading_seconds,
                ?
            ),

        completed_at =
            COALESCE(
                completed_at,
                NOW()
            ),

        last_accessed_at =
            NOW()

    WHERE

        id = ?

        AND status <> 'completed'
";


$updateStmt =
    $mysqli->prepare(
        $updateSQL
    );


if (!$updateStmt) {

    echo json_encode([
        "success" => false,
        "message" => $mysqli->error
    ]);

    exit;
}


$updateStmt->bind_param(
    "di",
    $readingSeconds,
    $progress["id"]
);


$success =
    $updateStmt->execute();


$updateStmt->close();


/* =========================================================
   RESPONSE
   ========================================================= */

if ($success) {

    echo json_encode([
        "success" => true,
        "message" => "Reading activity completed",
        "reading_seconds" => $readingSeconds,
        "required_seconds" => $requiredReadingSeconds
    ]);
} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to complete reading activity"
    ]);
}

exit;
