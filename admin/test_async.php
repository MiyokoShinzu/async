
<?php

/* =========================================================
   TEMPORARY ASYNC STUDENT VIEWER
   ETS-Async Learning Portal
   ========================================================= */

session_start();


/* =========================================================
   ADMIN AUTHENTICATION
   ========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "admin"
) {
    header("Location: ../login.php");
    exit;
}


/* =========================================================
   DATABASE
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   VARIABLES
   ========================================================= */

$studentId =
    trim($_GET["student_id"] ?? "");

$lectureId =
    (int) ($_GET["lecture_id"] ?? 0);

$message = "";

$messageType = "success";


/* =========================================================
   PROCESS ACTIONS
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action =
        $_POST["action"] ?? "";

    $studentId =
        trim($_POST["student_id"] ?? "");

    $lectureId =
        (int) ($_POST["lecture_id"] ?? 0);


    /* =====================================================
       START / ACCESS LECTURE
    ===================================================== */

    if ($action === "start") {

        $sql = "
            INSERT INTO lecture_progress (
                lecture_id,
                student_id,
                started_at,
                last_accessed_at,
                access_count,
                status
            )
            VALUES (
                ?,
                ?,
                NOW(),
                NOW(),
                1,
                'in_progress'
            )

            ON DUPLICATE KEY UPDATE

                started_at =
                    COALESCE(
                        started_at,
                        NOW()
                    ),

                last_accessed_at =
                    NOW(),

                access_count =
                    access_count + 1,

                status =
                    CASE
                        WHEN status = 'completed'
                        THEN 'completed'
                        ELSE 'in_progress'
                    END
        ";


        $stmt =
            $mysqli->prepare($sql);


        $stmt->bind_param(
            "is",
            $lectureId,
            $studentId
        );


        if ($stmt->execute()) {

            $message =
                "Lecture opened successfully.";
        } else {

            $message =
                "Unable to record lecture access.";

            $messageType =
                "danger";
        }


        $stmt->close();
    }


    /* =====================================================
       UPDATE LAST ACCESS
    ===================================================== */ elseif ($action === "access") {

        $sql = "
            UPDATE lecture_progress

            SET
                last_accessed_at = NOW(),
                access_count = access_count + 1

            WHERE lecture_id = ?
              AND student_id = ?
        ";


        $stmt =
            $mysqli->prepare($sql);


        $stmt->bind_param(
            "is",
            $lectureId,
            $studentId
        );


        $stmt->execute();

        $stmt->close();
    }


    /* =====================================================
       COMPLETE
    ===================================================== */ elseif ($action === "complete") {

        $sql = "
            INSERT INTO lecture_progress (
                lecture_id,
                student_id,
                started_at,
                last_accessed_at,
                access_count,
                completed_at,
                status
            )
            VALUES (
                ?,
                ?,
                NOW(),
                NOW(),
                1,
                NOW(),
                'completed'
            )

            ON DUPLICATE KEY UPDATE

                started_at =
                    COALESCE(
                        started_at,
                        NOW()
                    ),

                last_accessed_at =
                    NOW(),

                completed_at =
                    NOW(),

                status =
                    'completed'
        ";


        $stmt =
            $mysqli->prepare($sql);


        $stmt->bind_param(
            "is",
            $lectureId,
            $studentId
        );


        if ($stmt->execute()) {

            $message =
                "Lecture marked as completed.";
        } else {

            $message =
                "Unable to mark lecture as completed.";

            $messageType =
                "danger";
        }


        $stmt->close();
    }


    /* =====================================================
       SUBMIT ACTIVITY
    ===================================================== */ elseif ($action === "submit") {

        $submissionText =
            trim(
                $_POST["submission_text"] ?? ""
            );


        if ($submissionText === "") {

            $submissionText =
                "Completed asynchronous lecture activity.";
        }


        $sql = "
            INSERT INTO lecture_submissions (
                lecture_id,
                student_id,
                submission_text,
                submitted_at,
                status
            )
            VALUES (
                ?,
                ?,
                ?,
                NOW(),
                'submitted'
            )

            ON DUPLICATE KEY UPDATE

                submission_text =
                    VALUES(submission_text),

                submitted_at =
                    NOW(),

                status =
                    'submitted'
        ";


        $stmt =
            $mysqli->prepare($sql);


        $stmt->bind_param(
            "iss",
            $lectureId,
            $studentId,
            $submissionText
        );


        if ($stmt->execute()) {

            $message =
                "Activity submitted successfully.";
        } else {

            $message =
                "Unable to submit activity.";

            $messageType =
                "danger";
        }


        $stmt->close();
    }
}


/* =========================================================
   GET STUDENT
   ========================================================= */

$student = null;


if ($studentId !== "") {

    $sql = "
        SELECT
            student_id,
            first_name,
            middle_initial,
            last_name,
            department,
            year_section,
            email
        FROM accounts
        WHERE student_id = ?
          AND access = 'student'
        LIMIT 1
    ";


    $stmt =
        $mysqli->prepare($sql);


    $stmt->bind_param(
        "s",
        $studentId
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    $student =
        $result->fetch_assoc();


    $stmt->close();
}


/* =========================================================
   GET LECTURE
   ========================================================= */

$lecture = null;


if ($lectureId > 0) {

    $sql = "
        SELECT
            id,
            title,
            youtube_url,
            department,
            year_level,
            section,
            description,
            start_date,
            due_date,
            status
        FROM lectures
        WHERE id = ?
        LIMIT 1
    ";


    $stmt =
        $mysqli->prepare($sql);


    $stmt->bind_param(
        "i",
        $lectureId
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    $lecture =
        $result->fetch_assoc();


    $stmt->close();
}


/* =========================================================
   AUTOMATICALLY RECORD FIRST ACCESS
   ========================================================= */

if (
    $student &&
    $lecture
) {

    $sql = "
        INSERT INTO lecture_progress (
            lecture_id,
            student_id,
            started_at,
            last_accessed_at,
            access_count,
            status
        )
        VALUES (
            ?,
            ?,
            NOW(),
            NOW(),
            1,
            'in_progress'
        )

        ON DUPLICATE KEY UPDATE

            started_at =
                COALESCE(
                    started_at,
                    NOW()
                ),

            last_accessed_at =
                NOW(),

            access_count =
                access_count + 1,

            status =
                CASE
                    WHEN status = 'completed'
                    THEN 'completed'
                    ELSE 'in_progress'
                END
    ";


    $stmt =
        $mysqli->prepare($sql);


    $stmt->bind_param(
        "is",
        $lectureId,
        $studentId
    );


    $stmt->execute();

    $stmt->close();
}


/* =========================================================
   GET PROGRESS
   ========================================================= */

$progress = null;


if (
    $student &&
    $lecture
) {

    $sql = "
        SELECT
            started_at,
            last_accessed_at,
            access_count,
            completed_at,
            status
        FROM lecture_progress
        WHERE lecture_id = ?
          AND student_id = ?
        LIMIT 1
    ";


    $stmt =
        $mysqli->prepare($sql);


    $stmt->bind_param(
        "is",
        $lectureId,
        $studentId
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    $progress =
        $result->fetch_assoc();


    $stmt->close();
}


/* =========================================================
   GET SUBMISSION
   ========================================================= */

$submission = null;


if (
    $student &&
    $lecture
) {

    $sql = "
        SELECT
            submission_text,
            submitted_at,
            score,
            status
        FROM lecture_submissions
        WHERE lecture_id = ?
          AND student_id = ?
        LIMIT 1
    ";


    $stmt =
        $mysqli->prepare($sql);


    $stmt->bind_param(
        "is",
        $lectureId,
        $studentId
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    $submission =
        $result->fetch_assoc();


    $stmt->close();
}


/* =========================================================
   YOUTUBE EMBED URL
   ========================================================= */

$youtubeEmbedUrl = "";


if ($lecture) {

    $youtubeUrl =
        trim(
            $lecture["youtube_url"]
        );


    /*
       YouTube watch URL

       https://www.youtube.com/watch?v=XXXXXXXXXXX
    */

    if (
        preg_match(
            '/[?&]v=([^&]+)/',
            $youtubeUrl,
            $matches
        )
    ) {

        $videoId =
            $matches[1];
    }

    /*
       YouTube short URL

       https://youtu.be/XXXXXXXXXXX
    */ elseif (
        preg_match(
            '/youtu\.be\/([^?&]+)/',
            $youtubeUrl,
            $matches
        )
    ) {

        $videoId =
            $matches[1];
    } else {

        $videoId = "";
    }


    if ($videoId !== "") {

        $youtubeEmbedUrl =
            "https://www.youtube.com/embed/" .
            rawurlencode($videoId) .
            "?rel=0";
    }
}

?>


<!DOCTYPE html>

<html lang="en">


<?php include 'globals/head.php'; ?>


<body>


    <?php include 'globals/sidebar.php'; ?>

    <?php include 'globals/topbar.php'; ?>


    <main class="main-content">


        <div class="content-wrapper">


            <!-- =====================================================
             HEADER
        ====================================================== -->

            <div class="page-header">

                <div>

                    <h2>
                        Async Lecture Test
                    </h2>

                    <p>
                        Temporary student lecture viewer.
                    </p>

                </div>


                <a
                    href="lectures.php"
                    class="btn btn-outline-secondary">

                    <i
                        class="bi bi-arrow-left me-1">
                    </i>

                    Back to Lectures

                </a>

            </div>


            <!-- =====================================================
             TEST SELECTOR
        ====================================================== -->

            <?php if (!$student || !$lecture): ?>


                <div class="filter-card">


                    <div class="alert alert-warning mb-0">

                        <i
                            class="bi bi-info-circle me-2">
                        </i>

                        Please open this page using a student and
                        lecture selection.

                    </div>


                </div>


            <?php else: ?>


                <!-- =================================================
                 MESSAGE
            ================================================== -->

                <?php if ($message !== ""): ?>

                    <div
                        class="alert alert-<?= htmlspecialchars($messageType) ?>">

                        <?= htmlspecialchars($message) ?>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                 STUDENT INFO
            ================================================== -->

                <div class="student-test-banner">


                    <div>

                        <div class="student-test-label">

                            TESTING AS STUDENT

                        </div>


                        <div class="student-test-name">

                            <?= htmlspecialchars(
                                trim(
                                    $student["first_name"] .
                                        " " .
                                        $student["last_name"]
                                )
                            ) ?>

                        </div>


                        <div class="student-test-meta">

                            ID:
                            <strong>
                                <?= htmlspecialchars(
                                    $student["student_id"]
                                ) ?>
                            </strong>

                            ·

                            <?= htmlspecialchars(
                                $student["department"]
                            ) ?>

                            ·

                            <?= htmlspecialchars(
                                $student["year_section"]
                            ) ?>

                        </div>

                    </div>


                    <div>

                        <span
                            class="badge bg-warning-subtle text-warning-emphasis">

                            TEST MODE

                        </span>

                    </div>


                </div>


                <!-- =================================================
                 LECTURE
            ================================================== -->

                <div class="lecture-view-card">


                    <!-- =================================================
                     VIDEO
                ================================================== -->

                    <div class="lecture-video-wrapper">


                        <?php if (
                            $youtubeEmbedUrl !== ""
                        ): ?>


                            <iframe
                                src="<?= htmlspecialchars(
                                            $youtubeEmbedUrl
                                        ) ?>"
                                title="<?= htmlspecialchars(
                                            $lecture["title"]
                                        ) ?>"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                            </iframe>


                        <?php else: ?>


                            <div
                                class="lecture-video-error">

                                <i
                                    class="bi bi-youtube">
                                </i>

                                <h5>
                                    Invalid YouTube URL
                                </h5>

                                <p>
                                    Please check the lecture's
                                    YouTube URL.
                                </p>

                            </div>


                        <?php endif; ?>


                    </div>


                    <!-- =================================================
                     LECTURE INFORMATION
                ================================================== -->

                    <div class="lecture-view-content">


                        <div
                            class="lecture-view-heading">


                            <div>

                                <div class="lecture-view-category">

                                    ASYNCHRONOUS LECTURE

                                </div>


                                <h3>

                                    <?= htmlspecialchars(
                                        $lecture["title"]
                                    ) ?>

                                </h3>


                                <div
                                    class="lecture-view-assignment">


                                    <span>

                                        <i
                                            class="bi bi-building me-1">
                                        </i>

                                        <?= htmlspecialchars(
                                            $lecture["department"]
                                        ) ?>

                                    </span>


                                    <span>

                                        <i
                                            class="bi bi-mortarboard me-1">
                                        </i>

                                        Year
                                        <?= htmlspecialchars(
                                            $lecture["year_level"]
                                        ) ?>

                                    </span>


                                    <?php if (
                                        !empty($lecture["section"])
                                    ): ?>

                                        <span>

                                            <i
                                                class="bi bi-people me-1">
                                            </i>

                                            Section
                                            <?= htmlspecialchars(
                                                $lecture["section"]
                                            ) ?>

                                        </span>

                                    <?php endif; ?>


                                </div>

                            </div>


                            <div>

                                <?php if (
                                    $progress &&
                                    $progress["status"] === "completed"
                                ): ?>

                                    <span
                                        class="badge bg-success-subtle text-success">

                                        <i
                                            class="bi bi-check-circle me-1">
                                        </i>

                                        Completed

                                    </span>

                                <?php else: ?>

                                    <span
                                        class="badge bg-primary-subtle text-primary">

                                        <i
                                            class="bi bi-play-circle me-1">
                                        </i>

                                        In Progress

                                    </span>

                                <?php endif; ?>


                            </div>


                        </div>


                        <!-- =================================================
                         DESCRIPTION
                    ================================================== -->

                        <?php if (
                            !empty($lecture["description"])
                        ): ?>


                            <div
                                class="lecture-instructions">


                                <div
                                    class="lecture-instructions-title">

                                    <i
                                        class="bi bi-info-circle me-1">
                                    </i>

                                    Instructions

                                </div>


                                <div>

                                    <?= nl2br(
                                        htmlspecialchars(
                                            $lecture["description"]
                                        )
                                    ) ?>

                                </div>


                            </div>


                        <?php endif; ?>


                        <!-- =================================================
                         SCHEDULE
                    ================================================== -->

                        <div
                            class="lecture-schedule">


                            <div>

                                <span>
                                    Available From
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        date(
                                            "M d, Y h:i A",
                                            strtotime(
                                                $lecture["start_date"]
                                            )
                                        )
                                    ) ?>

                                </strong>

                            </div>


                            <div>

                                <span>
                                    Due Date
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        date(
                                            "M d, Y h:i A",
                                            strtotime(
                                                $lecture["due_date"]
                                            )
                                        )
                                    ) ?>

                                </strong>

                            </div>


                        </div>


                        <!-- =================================================
                         PROGRESS
                    ================================================== -->

                        <div
                            class="lecture-progress-panel">


                            <div
                                class="lecture-progress-heading">

                                <strong>
                                    Your Progress
                                </strong>


                                <span>

                                    <?=
                                    (
                                        $progress &&
                                        $progress["status"] === "completed"
                                    )
                                        ? "100%"
                                        : "In Progress"
                                    ?>

                                </span>

                            </div>


                            <div
                                class="progress">

                                <div
                                    class="progress-bar"
                                    style="width: <?= (
                                                        $progress &&
                                                        $progress["status"] === "completed"
                                                    )
                                                        ? "100"
                                                        : "50"
                                                    ?>%;">

                                </div>

                            </div>


                            <div
                                class="lecture-progress-details">


                                <span>

                                    <i
                                        class="bi bi-play-circle me-1">
                                    </i>

                                    Started

                                </span>


                                <span>

                                    <i
                                        class="bi bi-eye me-1">
                                    </i>

                                    <?= (int)(
                                        $progress["access_count"]
                                        ?? 0
                                    ) ?>

                                    access

                                </span>


                                <?php if (
                                    $progress &&
                                    $progress["completed_at"]
                                ): ?>

                                    <span>

                                        <i
                                            class="bi bi-check-circle me-1">
                                        </i>

                                        Completed

                                    </span>

                                <?php endif; ?>


                            </div>


                        </div>


                        <!-- =================================================
                         ACTIONS
                    ================================================== -->

                        <div
                            class="lecture-view-actions">


                            <?php if (
                                !$progress ||
                                $progress["status"] !== "completed"
                            ): ?>


                                <form
                                    method="POST">


                                    <input
                                        type="hidden"
                                        name="student_id"
                                        value="<?= htmlspecialchars(
                                                    $studentId
                                                ) ?>">


                                    <input
                                        type="hidden"
                                        name="lecture_id"
                                        value="<?= (int)$lectureId ?>">


                                    <input
                                        type="hidden"
                                        name="action"
                                        value="complete">


                                    <button
                                        type="submit"
                                        class="btn btn-success btn-lg">

                                        <i
                                            class="bi bi-check-circle me-1">
                                        </i>

                                        Mark Lecture as Completed

                                    </button>


                                </form>


                            <?php else: ?>


                                <button
                                    type="button"
                                    class="btn btn-success btn-lg"
                                    disabled>

                                    <i
                                        class="bi bi-check-circle me-1">
                                    </i>

                                    Lecture Completed

                                </button>


                            <?php endif; ?>


                        </div>


                        <!-- =================================================
                         SUBMISSION
                    ================================================== -->

                        <div
                            class="lecture-submission-panel">


                            <div
                                class="lecture-submission-title">

                                <i
                                    class="bi bi-file-earmark-text me-2">
                                </i>

                                Activity Submission

                            </div>


                            <p>

                                Submit a response after watching
                                the lecture.

                            </p>


                            <form
                                method="POST">


                                <input
                                    type="hidden"
                                    name="student_id"
                                    value="<?= htmlspecialchars(
                                                $studentId
                                            ) ?>">


                                <input
                                    type="hidden"
                                    name="lecture_id"
                                    value="<?= (int)$lectureId ?>">


                                <input
                                    type="hidden"
                                    name="action"
                                    value="submit">


                                <textarea
                                    name="submission_text"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Enter your activity response..."><?= htmlspecialchars(
                                                                                        $submission["submission_text"]
                                                                                            ?? ""
                                                                                    ) ?></textarea>


                                <div
                                    class="d-flex justify-content-between align-items-center mt-3">


                                    <?php if (
                                        $submission
                                    ): ?>

                                        <span
                                            class="text-success small">

                                            <i
                                                class="bi bi-check-circle me-1">
                                            </i>

                                            Submitted

                                        </span>

                                    <?php else: ?>

                                        <span
                                            class="text-muted small">

                                            No submission yet.

                                        </span>

                                    <?php endif; ?>


                                    <button
                                        type="submit"
                                        class="btn btn-primary">

                                        <i
                                            class="bi bi-send me-1">
                                        </i>

                                        Submit Activity

                                    </button>


                                </div>


                            </form>


                        </div>


                    </div>


                </div>


            <?php endif; ?>


        </div>


    </main>


    <?php include 'globals/scripts.php'; ?>


</body>

</html>
