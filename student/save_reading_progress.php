<?php

/* =========================================================
   SAVE READING PROGRESS
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
   INPUT
   ========================================================= */

$readingActivityId =
    isset($_POST["reading_activity_id"])
    ? (int)$_POST["reading_activity_id"]
    : 0;


$readingSeconds =
    isset($_POST["reading_seconds"])
    ? (float)$_POST["reading_seconds"]
    : 0;


/* =========================================================
   VALIDATION
   ========================================================= */

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
   SANITIZE VALUES
   ========================================================= */

$readingSeconds =
    max(
        0,
        $readingSeconds
    );


/* =========================================================
   CHECK EXISTING PROGRESS
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


$existing =
    $result->fetch_assoc();


$checkStmt->close();


/* =========================================================
   NO EXISTING RECORD
   ========================================================= */

if (!$existing) {

    echo json_encode([
        "success" => false,
        "message" => "Progress record not found"
    ]);

    exit;
}


/* =========================================================
   UPDATE EXISTING RECORD
   ========================================================= */

if ($existing) {


    /*
       NEVER change completed back
       to in_progress.
    */

    if (
        ($existing["status"] ?? "") ===
        "completed"
    ) {

        /*
           Keep completed state.

           Do not modify the reading time
           or status anymore.
        */

        echo json_encode([
            "success" => true,
            "message" => "Already completed",
            "reading_seconds" =>
            (float)(
                $existing["reading_seconds"]
                ?? 0
            )
        ]);

        exit;
    }


    /* =====================================================
       KEEP THE HIGHEST READING TIME
    ====================================================== */

    $sql = "
        UPDATE reading_activity_progress

        SET

            reading_seconds =
                GREATEST(
                    reading_seconds,
                    ?
                ),

            last_accessed_at =
                NOW()

        WHERE
            id = ?

            AND status <> 'completed'
    ";


    $stmt =
        $mysqli->prepare(
            $sql
        );


    if (!$stmt) {

        echo json_encode([
            "success" => false,
            "message" => $mysqli->error
        ]);

        exit;
    }


    $stmt->bind_param(
        "di",
        $readingSeconds,
        $existing["id"]
    );


    $success =
        $stmt->execute();


    $stmt->close();


    if (!$success) {

        echo json_encode([
            "success" => false,
            "message" => "Unable to save progress"
        ]);

        exit;
    }
}


/* =========================================================
   RESPONSE
   ========================================================= */

echo json_encode([
    "success" => true,
    "reading_seconds" => $readingSeconds
]);

exit;
