
<?php

/* =========================================================
   SAVE VIDEO PROGRESS
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

$lectureId =
    isset($_POST["lecture_id"])
    ? (int)$_POST["lecture_id"]
    : 0;


$watchedSeconds =
    isset($_POST["watched_seconds"])
    ? (float)$_POST["watched_seconds"]
    : 0;


$videoDuration =
    isset($_POST["video_duration"])
    ? (float)$_POST["video_duration"]
    : 0;


/* =========================================================
   VALIDATION
   ========================================================= */

if (
    $lectureId <= 0 ||
    $studentId === "" ||
    $videoDuration <= 0
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

$watchedSeconds =
    max(
        0,
        min(
            $watchedSeconds,
            $videoDuration
        )
    );


/* =========================================================
   CHECK EXISTING PROGRESS
   ========================================================= */

$checkSQL = "
    SELECT
        id,
        status

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


$existing =
    $result->fetch_assoc();


$checkStmt->close();


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

           Only update the video information.
        */

        $sql = "
            UPDATE lecture_progress

            SET
                watched_seconds = ?,
                video_duration = ?,
                last_accessed_at = NOW()

            WHERE
                id = ?
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
            "ddi",
            $watchedSeconds,
            $videoDuration,
            $existing["id"]
        );


        $stmt->execute();

        $stmt->close();

    }

    else {


        $sql = "
            UPDATE lecture_progress

            SET
                watched_seconds = ?,
                video_duration = ?,
                last_accessed_at = NOW()

            WHERE
                id = ?
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
            "ddi",
            $watchedSeconds,
            $videoDuration,
            $existing["id"]
        );


        $stmt->execute();

        $stmt->close();
    }

}


/* =========================================================
   RESPONSE
   ========================================================= */

echo json_encode([
    "success" => true,
    "watched_seconds" => $watchedSeconds,
    "video_duration" => $videoDuration
]);

exit;

