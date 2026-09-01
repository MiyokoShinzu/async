
<?php

/* =========================================================
   COMPLETE ACTIVITY
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
   LECTURE ID
   ========================================================= */

$lectureId =
    isset($_POST["lecture_id"])
    ? (int)$_POST["lecture_id"]
    : 0;


if (
    $lectureId <= 0 ||
    $studentId === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid data"
    ]);

    exit;
}


/* =========================================================
   CHECK EXISTING RECORD
   ========================================================= */

$checkSQL = "
    SELECT
        id,
        status,
        video_duration

    FROM lecture_progress

    WHERE
        lecture_id = ?
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
    $lectureId,
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
   COMPLETE
   ========================================================= */

$duration =
    (float)(
        $progress["video_duration"] ?? 0
    );


$updateSQL = "
    UPDATE lecture_progress

    SET
        status = 'completed',

        watched_seconds =
            CASE

                WHEN video_duration > 0
                THEN video_duration

                ELSE watched_seconds

            END,

        completed_at = NOW(),

        last_accessed_at = NOW()

    WHERE
        id = ?
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
    "i",
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
        "message" => "Activity completed"
    ]);

}
else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to complete activity"
    ]);
}

exit;

