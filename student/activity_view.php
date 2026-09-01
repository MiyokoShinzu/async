<?php

/* =========================================================
   STUDENT ACTIVITY VIEW
   ETS-Async Learning Portal
   ========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION
   ========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {

    header("Location: ../login.php");
    exit;
}


/* =========================================================
   DATABASE
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   STUDENT DATA
   ========================================================= */

$user = $_SESSION["user"];

$studentId =
    $user["student_id"] ?? "";


/* =========================================================
   GET ACTIVITY ID
   ========================================================= */

$lectureId =
    isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;


if ($lectureId <= 0) {

    header("Location: activities.php");
    exit;
}


/* =========================================================
   GET LECTURE
   ========================================================= */

$lectureSQL = "
    SELECT
        id,
        title,
        description,
        youtube_url,
        department,
        year_level,
        section,
        start_date,
        due_date,
        created_at

    FROM lectures

    WHERE id = ?

    LIMIT 1
";


$lectureStmt =
    $mysqli->prepare(
        $lectureSQL
    );


if (!$lectureStmt) {

    die("Database error: " .
        htmlspecialchars(
            $mysqli->error
        ));
}


$lectureStmt->bind_param(
    "i",
    $lectureId
);


$lectureStmt->execute();


$lectureResult =
    $lectureStmt->get_result();


$lecture =
    $lectureResult->fetch_assoc();


$lectureStmt->close();


/* =========================================================
   ACTIVITY NOT FOUND
   ========================================================= */

if (!$lecture) {

    header("Location: activities.php");
    exit;
}


/* =========================================================
   GET STUDENT PROGRESS
   ========================================================= */

$progressSQL = "
    SELECT
        id,
        status,
        started_at,
        last_accessed_at,
        completed_at,
        access_count,
        watched_seconds,
        video_duration

    FROM lecture_progress

    WHERE
        lecture_id = ?
        AND student_id = ?

    LIMIT 1
";


$progressStmt =
    $mysqli->prepare(
        $progressSQL
    );


if (!$progressStmt) {

    die("Database error: " .
        htmlspecialchars(
            $mysqli->error
        ));
}


$progressStmt->bind_param(
    "is",
    $lectureId,
    $studentId
);


$progressStmt->execute();


$progressResult =
    $progressStmt->get_result();


$progress =
    $progressResult->fetch_assoc();


$progressStmt->close();


/* =========================================================
   CREATE / UPDATE PROGRESS
   ========================================================= */

if ($progress) {


    /* =====================================================
       ALREADY COMPLETED
       ===================================================== */

    if (
        ($progress["status"] ?? "") ===
        "completed"
    ) {

        $updateSQL = "
            UPDATE lecture_progress

            SET
                last_accessed_at = NOW(),

                access_count =
                    COALESCE(
                        access_count,
                        0
                    ) + 1

            WHERE id = ?
        ";


        $updateStmt =
            $mysqli->prepare(
                $updateSQL
            );


        if ($updateStmt) {

            $updateStmt->bind_param(
                "i",
                $progress["id"]
            );

            $updateStmt->execute();

            $updateStmt->close();
        }
    }


    /* =====================================================
       NOT COMPLETED
       ===================================================== */ else {

        $updateSQL = "
            UPDATE lecture_progress

            SET
                status = 'in_progress',

                started_at =
                    COALESCE(
                        started_at,
                        NOW()
                    ),

                last_accessed_at =
                    NOW(),

                access_count =
                    COALESCE(
                        access_count,
                        0
                    ) + 1

            WHERE id = ?
        ";


        $updateStmt =
            $mysqli->prepare(
                $updateSQL
            );


        if ($updateStmt) {

            $updateStmt->bind_param(
                "i",
                $progress["id"]
            );

            $updateStmt->execute();

            $updateStmt->close();
        }
    }
}


/* =========================================================
   FIRST TIME OPENING
   ========================================================= */ else {

    $insertSQL = "
        INSERT INTO lecture_progress
        (
            lecture_id,
            student_id,
            status,
            started_at,
            last_accessed_at,
            access_count,
            watched_seconds,
            video_duration
        )

        VALUES
        (
            ?,
            ?,
            'in_progress',
            NOW(),
            NOW(),
            1,
            0,
            0
        )
    ";


    $insertStmt =
        $mysqli->prepare(
            $insertSQL
        );


    if (!$insertStmt) {

        die("Database error: " .
            htmlspecialchars(
                $mysqli->error
            ));
    }


    $insertStmt->bind_param(
        "is",
        $lectureId,
        $studentId
    );


    $insertStmt->execute();


    $insertStmt->close();
}


/* =========================================================
   GET PROGRESS AGAIN
   ========================================================= */

$progressSQL = "
    SELECT
        id,
        status,
        started_at,
        last_accessed_at,
        completed_at,
        access_count,
        watched_seconds,
        video_duration

    FROM lecture_progress

    WHERE
        lecture_id = ?
        AND student_id = ?

    LIMIT 1
";


$progressStmt =
    $mysqli->prepare(
        $progressSQL
    );


if (!$progressStmt) {

    die("Database error: " .
        htmlspecialchars(
            $mysqli->error
        ));
}


$progressStmt->bind_param(
    "is",
    $lectureId,
    $studentId
);


$progressStmt->execute();


$progressResult =
    $progressStmt->get_result();


$progress =
    $progressResult->fetch_assoc();


$progressStmt->close();


/* =========================================================
   CURRENT STATUS
   ========================================================= */

$status =
    $progress["status"] ?? "in_progress";


/* =========================================================
   SAVED VIDEO POSITION
   ========================================================= */

$watchedSeconds =
    (float)(
        $progress["watched_seconds"] ?? 0
    );


$videoDuration =
    (float)(
        $progress["video_duration"] ?? 0
    );


/* =========================================================
   CALCULATE PROGRESS
   ========================================================= */

if (
    $status === "completed"
) {

    $statusText =
        "Completed";

    $statusClass =
        "completed";

    $statusIcon =
        "bi-check-circle-fill";

    $progressPercentage =
        100;
} else {

    $statusText =
        "In Progress";

    $statusClass =
        "in-progress";

    $statusIcon =
        "bi-play-circle-fill";


    if (
        $videoDuration > 0
    ) {

        $progressPercentage =
            (
                $watchedSeconds /
                $videoDuration
            ) * 100;


        $progressPercentage =
            min(
                99.99,
                max(
                    0,
                    $progressPercentage
                )
            );
    } else {

        $progressPercentage =
            0;
    }
}


/* =========================================================
   YOUTUBE URL
   ========================================================= */

$youtubeUrl =
    trim(
        $lecture["youtube_url"] ?? ""
    );


/* =========================================================
   EXTRACT YOUTUBE VIDEO ID
   ========================================================= */

$youtubeId = "";


if (
    $youtubeUrl !== ""
) {

    $parsedUrl =
        parse_url(
            $youtubeUrl
        );


    /* =====================================================
       youtube.com/watch?v=
       ===================================================== */

    if (
        isset(
            $parsedUrl["query"]
        )
    ) {

        parse_str(
            $parsedUrl["query"],
            $query
        );


        if (
            !empty($query["v"])
        ) {

            $youtubeId =
                $query["v"];
        }
    }


    /* =====================================================
       youtu.be/VIDEO_ID
       ===================================================== */

    if (
        $youtubeId === "" &&
        isset(
            $parsedUrl["host"]
        )
    ) {

        if (
            strpos(
                $parsedUrl["host"],
                "youtu.be"
            ) !== false
        ) {

            $youtubeId =
                trim(
                    $parsedUrl["path"] ?? "",
                    "/"
                );
        }
    }


    /* =====================================================
       youtube.com/embed/VIDEO_ID
       ===================================================== */

    if (
        $youtubeId === "" &&
        isset(
            $parsedUrl["path"]
        )
    ) {

        if (
            preg_match(
                '#/embed/([^/?]+)#',
                $parsedUrl["path"],
                $matches
            )
        ) {

            $youtubeId =
                $matches[1];
        }
    }
}


/* =========================================================
   SECURITY
   ========================================================= */

$youtubeId =
    preg_replace(
        '/[^a-zA-Z0-9_-]/',
        '',
        $youtubeId
    );

?>

<!DOCTYPE html>

<html lang="en">


<?php include 'globals/head.php'; ?>
<style>
    /* =========================================================
   STUDENT ACTIVITY VIEW
   ETS-Async Learning Portal
   ========================================================= */


    /* =========================================================
   BACK LINK
   ========================================================= */

    .back-link {
        display: inline-flex;
        align-items: center;

        text-decoration: none;

        color: #4f46e5;

        font-size: 14px;
        font-weight: 500;

        transition: all 0.2s ease;
    }

    .back-link:hover {
        color: #3730a3;

        transform: translateX(-2px);
    }


    /* =========================================================
   ACTIVITY HEADER
   ========================================================= */

    .activity-view-header {
        display: flex;

        justify-content: space-between;
        align-items: flex-start;

        gap: 25px;

        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 16px;

        padding: 25px 28px;

        margin-bottom: 18px;

        box-shadow:
            0 2px 8px rgba(15, 23, 42, 0.04);
    }


    /* =========================================================
   ACTIVITY LABEL
   ========================================================= */

    .activity-view-label {
        display: inline-block;

        font-size: 11px;

        font-weight: 700;

        letter-spacing: 0.8px;

        color: #4f46e5;

        background: #eef2ff;

        border-radius: 6px;

        padding: 5px 9px;

        margin-bottom: 10px;
    }


    /* =========================================================
   ACTIVITY TITLE
   ========================================================= */

    .activity-view-header h2 {
        margin: 0;

        color: #111827;

        font-size: 26px;

        font-weight: 700;

        line-height: 1.35;
    }


    /* =========================================================
   ACTIVITY DESCRIPTION
   ========================================================= */

    .activity-view-header p {
        margin: 10px 0 0;

        color: #6b7280;

        font-size: 14px;

        line-height: 1.7;

        max-width: 850px;
    }


    /* =========================================================
   STATUS BADGE
   ========================================================= */

    .activity-status-badge {
        display: inline-flex;

        align-items: center;

        gap: 6px;

        white-space: nowrap;

        padding: 8px 13px;

        border-radius: 999px;

        font-size: 13px;

        font-weight: 600;
    }


    /* =========================================================
   IN PROGRESS
   ========================================================= */

    .activity-status-badge.in-progress {
        color: #92400e;

        background: #fef3c7;

        border: 1px solid #fde68a;
    }


    /* =========================================================
   COMPLETED
   ========================================================= */

    .activity-status-badge.completed {
        color: #166534;

        background: #dcfce7;

        border: 1px solid #bbf7d0;
    }


    /* =========================================================
   PROGRESS CARD
   ========================================================= */

    .activity-progress-card {
        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 16px;

        padding: 20px 24px;

        margin-bottom: 18px;

        box-shadow:
            0 2px 8px rgba(15, 23, 42, 0.04);
    }


    /* =========================================================
   PROGRESS HEADER
   ========================================================= */

    .activity-progress-header {
        display: flex;

        align-items: center;

        justify-content: space-between;

        margin-bottom: 10px;

        font-size: 14px;

        color: #374151;

        font-weight: 500;
    }


    .activity-progress-header strong {
        color: #4f46e5;

        font-size: 15px;
    }


    /* =========================================================
   PROGRESS BAR
   ========================================================= */

    .activity-progress {
        height: 9px;

        overflow: hidden;

        background: #e5e7eb;

        border-radius: 999px;
    }


    .activity-progress .progress-bar {
        border-radius: 999px;

        transition:
            width 0.4s ease,
            background-color 0.3s ease;
    }


    /* =========================================================
   IN PROGRESS BAR
   ========================================================= */

    .activity-progress .progress-bar.in-progress {
        background: #6366f1;
    }


    /* =========================================================
   COMPLETED BAR
   ========================================================= */

    .activity-progress .progress-bar.completed {
        background: #22c55e;
    }


    /* =========================================================
   PROGRESS MESSAGE
   ========================================================= */

    .progress-message,
    .completed-message {
        display: flex;

        align-items: center;

        gap: 7px;

        margin-top: 12px;

        font-size: 12px;

        line-height: 1.5;
    }


    .progress-message {
        color: #6b7280;
    }


    .completed-message {
        color: #15803d;

        font-weight: 500;
    }


    /* =========================================================
   VIDEO CARD
   ========================================================= */

    .video-card {
        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 16px;

        overflow: hidden;

        margin-bottom: 18px;

        box-shadow:
            0 2px 8px rgba(15, 23, 42, 0.04);
    }


    /* =========================================================
   VIDEO HEADER
   ========================================================= */

    .video-header {
        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 15px;

        padding: 15px 20px;

        background: #fafafa;

        border-bottom: 1px solid #e5e7eb;

        color: #1f2937;

        font-size: 14px;

        font-weight: 600;
    }


    .video-header i {
        color: #ef4444;

        font-size: 17px;
    }


    .video-header span {
        color: #6b7280;

        font-size: 12px;

        font-weight: 500;
    }


    /* =========================================================
   VIDEO CONTAINER
   ========================================================= */

    .video-container {
        position: relative;

        width: 100%;

        aspect-ratio: 16 / 9;

        background: #111827;

        overflow: hidden;
    }


    /* =========================================================
   YOUTUBE IFRAME
   ========================================================= */

    .video-container iframe {
        position: absolute;

        inset: 0;

        width: 100%;

        height: 100%;

        border: 0;
    }


    /* =========================================================
   VIDEO ERROR
   ========================================================= */

    .video-error {
        position: absolute;

        inset: 0;

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        text-align: center;

        padding: 30px;

        color: #d1d5db;
    }


    .video-error i {
        font-size: 40px;

        color: #f59e0b;

        margin-bottom: 10px;
    }


    .video-error h5 {
        margin: 0 0 6px;

        color: #ffffff;

        font-size: 16px;

        font-weight: 600;
    }


    .video-error p {
        margin: 0;

        color: #9ca3af;

        font-size: 13px;
    }


    /* =========================================================
   INFORMATION CARD
   ========================================================= */

    .information-card {
        height: 100%;

        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 16px;

        padding: 20px 22px;

        box-shadow:
            0 2px 8px rgba(15, 23, 42, 0.04);
    }


    /* =========================================================
   INFORMATION CARD TITLE
   ========================================================= */

    .information-card h5 {
        display: flex;

        align-items: center;

        margin: 0 0 18px;

        padding-bottom: 13px;

        border-bottom: 1px solid #f0f0f0;

        color: #1f2937;

        font-size: 15px;

        font-weight: 600;
    }


    .information-card h5 i {
        color: #4f46e5;

        font-size: 17px;
    }


    /* =========================================================
   INFORMATION GRID
   ========================================================= */

    .information-grid {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

        gap: 18px 25px;
    }


    .information-grid>div {
        display: flex;

        flex-direction: column;

        gap: 4px;
    }


    .information-grid span,
    .activity-details span {
        color: #9ca3af;

        font-size: 11px;

        font-weight: 500;

        text-transform: uppercase;

        letter-spacing: 0.5px;
    }


    .information-grid strong {
        color: #374151;

        font-size: 13px;

        font-weight: 600;
    }


    /* =========================================================
   ACTIVITY DETAILS
   ========================================================= */

    .activity-details {
        display: flex;

        flex-direction: column;

        gap: 0;
    }


    .activity-details>div {
        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 15px;

        padding: 12px 0;

        border-bottom: 1px solid #f3f4f6;
    }


    .activity-details>div:first-child {
        padding-top: 0;
    }


    .activity-details>div:last-child {
        padding-bottom: 0;

        border-bottom: 0;
    }


    .activity-details strong {
        color: #374151;

        font-size: 12px;

        font-weight: 600;

        text-align: right;
    }


    /* =========================================================
   ACTIVITY DETAIL STATUS
   ========================================================= */

    .activity-details strong.in-progress {
        color: #d97706;
    }


    .activity-details strong.completed {
        color: #16a34a;
    }


    /* =========================================================
   RESPONSIVE
   ========================================================= */

    @media (max-width: 992px) {

        .activity-view-header {
            padding: 22px;
        }

        .activity-view-header h2 {
            font-size: 23px;
        }

    }


    @media (max-width: 768px) {

        .activity-view-header {
            flex-direction: column;

            gap: 15px;
        }


        .activity-status-badge {
            align-self: flex-start;
        }


        .information-grid {
            grid-template-columns: 1fr;
        }


        .activity-progress-card {
            padding: 18px;
        }


        .video-header {
            padding: 13px 15px;
        }

    }


    @media (max-width: 576px) {

        .activity-view-header {
            padding: 18px;
        }


        .activity-view-header h2 {
            font-size: 20px;
        }


        .activity-view-header p {
            font-size: 13px;
        }


        .activity-progress-header {
            font-size: 13px;
        }


        .information-card {
            padding: 18px;
        }


        .video-header span {
            font-size: 11px;
        }

    }


    /* =========================================================
   SMOOTH CARD HOVER
   ========================================================= */

    .activity-progress-card,
    .video-card,
    .information-card,
    .activity-view-header {
        transition:
            box-shadow 0.2s ease,
            transform 0.2s ease;
    }


    .activity-progress-card:hover,
    .video-card:hover,
    .information-card:hover,
    .activity-view-header:hover {
        box-shadow:
            0 5px 18px rgba(15, 23, 42, 0.07);
    }


    /* =========================================================
   MAIN CONTENT SPACING
   ========================================================= */

    .main-content .content-wrapper {
        padding-bottom: 35px;
    }


    /* =========================================================
   BOOTSTRAP ROW ADJUSTMENT
   ========================================================= */

    .row.g-3 {
        margin-top: 0 !important;
    }
</style>

<body>


    <!-- =========================================================
     SIDEBAR
========================================================= -->

    <?php include 'globals/sidebar.php'; ?>


    <!-- =========================================================
     TOPBAR
========================================================= -->

    <?php include 'globals/topbar.php'; ?>


    <!-- =========================================================
     MAIN CONTENT
========================================================= -->

    <main class="main-content">


        <div class="content-wrapper">


            <!-- =================================================
             BACK BUTTON
        ================================================== -->

            <div class="mb-3">

                <a
                    href="activities.php"
                    class="back-link">

                    <i
                        class="bi bi-arrow-left me-1">
                    </i>

                    Back to Activities

                </a>

            </div>


            <!-- =================================================
             ACTIVITY HEADER
        ================================================== -->

            <div class="activity-view-header">


                <div>

                    <div
                        class="activity-view-label">

                        ASYNCHRONOUS LECTURE

                    </div>


                    <h2>

                        <?= htmlspecialchars(
                            $lecture["title"]
                        ) ?>

                    </h2>


                    <?php if (
                        !empty($lecture["description"])
                    ): ?>

                        <p>

                            <?= nl2br(
                                htmlspecialchars(
                                    $lecture["description"]
                                )
                            ) ?>

                        </p>

                    <?php endif; ?>


                </div>


                <!-- STATUS -->

                <div>

                    <span
                        class="
                    activity-status-badge
                    <?= $statusClass ?>">

                        <i
                            class="
                        bi
                        <?= $statusIcon ?>">
                        </i>

                        <?= $statusText ?>

                    </span>

                </div>


            </div>


            <!-- =================================================
             PROGRESS
        ================================================== -->

            <div
                class="activity-progress-card">


                <div
                    class="activity-progress-header">


                    <span>
                        Activity Progress
                    </span>


                    <strong
                        id="progressPercentage">

                        <?= number_format(
                            $progressPercentage,
                            1
                        ) ?>%

                    </strong>


                </div>


                <div
                    class="progress activity-progress">


                    <div
                        id="activityProgressBar"
                        class="
                    progress-bar
                    <?= $statusClass ?>"
                        role="progressbar"
                        style="
                        width:
                        <?= $progressPercentage ?>%;
                    "
                        aria-valuenow="<?= $progressPercentage ?>"
                        aria-valuemin="0"
                        aria-valuemax="100">

                    </div>


                </div>


                <?php if (
                    $status === "completed"
                ): ?>

                    <div
                        class="completed-message">

                        <i
                            class="
                        bi
                        bi-check-circle-fill">
                        </i>

                        You have completed this lecture.
                        You may replay the video anytime.

                    </div>

                <?php else: ?>

                    <div
                        class="progress-message">

                        <i
                            class="bi bi-info-circle">
                        </i>

                        Your progress is automatically saved
                        while watching the lecture.

                    </div>

                <?php endif; ?>


            </div>


            <!-- =================================================
             VIDEO
        ================================================== -->

            <div
                class="video-card">


                <div
                    class="video-header">


                    <div>

                        <i
                            class="
                        bi
                        bi-youtube
                        me-2">
                        </i>

                        Lecture Video

                    </div>


                    <span>

                        <?= (int)(
                            $progress["access_count"] ?? 1
                        ) ?>

                        view(s)

                    </span>


                </div>


                <div
                    class="video-container">


                    <?php if (
                        $youtubeId !== ""
                    ): ?>


                        <iframe
                            id="youtubePlayer"
                            src="https://www.youtube.com/embed/<?= htmlspecialchars($youtubeId) ?>?enablejsapi=1&rel=0&modestbranding=1"
                            title="<?= htmlspecialchars($lecture["title"]) ?>"
                            frameborder="0"
                            allow="
                            accelerometer;
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            gyroscope;
                            picture-in-picture;
                            web-share
                        "
                            allowfullscreen>
                        </iframe>


                    <?php else: ?>


                        <div
                            class="video-error">


                            <i
                                class="
                            bi
                            bi-exclamation-triangle">
                            </i>


                            <h5>
                                Video unavailable
                            </h5>


                            <p>
                                The YouTube video URL
                                is missing or invalid.
                            </p>


                        </div>


                    <?php endif; ?>


                </div>


            </div>


            <!-- =================================================
             INFORMATION
        ================================================== -->

            <div
                class="row g-3 mt-1">


                <!-- COURSE INFORMATION -->

                <div
                    class="col-lg-8">


                    <div
                        class="information-card">


                        <h5>

                            <i
                                class="
                            bi
                            bi-info-circle
                            me-2">
                            </i>

                            Activity Information

                        </h5>


                        <div
                            class="information-grid">


                            <div>

                                <span>
                                    Department
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $lecture["department"] ?? "All"
                                    ) ?>

                                </strong>

                            </div>


                            <div>

                                <span>
                                    Year Level
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $lecture["year_level"] ?? "All"
                                    ) ?>

                                </strong>

                            </div>


                            <div>

                                <span>
                                    Section
                                </span>

                                <strong>

                                    <?= htmlspecialchars(
                                        $lecture["section"] ?? "All"
                                    ) ?>

                                </strong>

                            </div>


                            <?php if (
                                !empty($lecture["due_date"])
                            ): ?>


                                <div>

                                    <span>
                                        Due Date
                                    </span>

                                    <strong>

                                        <?= htmlspecialchars(
                                            date(
                                                "M d, Y",
                                                strtotime(
                                                    $lecture["due_date"]
                                                )
                                            )
                                        ) ?>

                                    </strong>

                                </div>


                            <?php endif; ?>


                        </div>


                    </div>


                </div>


                <!-- PROGRESS INFORMATION -->

                <div
                    class="col-lg-4">


                    <div
                        class="information-card">


                        <h5>

                            <i
                                class="
                            bi
                            bi-activity
                            me-2">
                            </i>

                            Your Activity

                        </h5>


                        <div
                            class="activity-details">


                            <div>

                                <span>
                                    Status
                                </span>


                                <strong
                                    class="<?= $statusClass ?>">

                                    <?= $statusText ?>

                                </strong>

                            </div>


                            <div>

                                <span>
                                    Started
                                </span>


                                <strong>

                                    <?php if (
                                        !empty($progress["started_at"])
                                    ): ?>

                                        <?= htmlspecialchars(
                                            date(
                                                "M d, Y h:i A",
                                                strtotime(
                                                    $progress["started_at"]
                                                )
                                            )
                                        ) ?>

                                    <?php else: ?>

                                        —

                                    <?php endif; ?>

                                </strong>

                            </div>


                            <div>

                                <span>
                                    Completed
                                </span>


                                <strong
                                    id="completedDate">

                                    <?php if (
                                        !empty($progress["completed_at"])
                                    ): ?>

                                        <?= htmlspecialchars(
                                            date(
                                                "M d, Y h:i A",
                                                strtotime(
                                                    $progress["completed_at"]
                                                )
                                            )
                                        ) ?>

                                    <?php else: ?>

                                        —

                                    <?php endif; ?>

                                </strong>

                            </div>


                        </div>


                    </div>


                </div>


            </div>


        </div>


    </main>


    <!-- =========================================================
     YOUTUBE API
========================================================= -->

    <script
        src="https://www.youtube.com/iframe_api">
    </script>


    <script>
        /* =========================================================
   YOUTUBE PLAYER
========================================================= */

        let player = null;


        /* =========================================================
           PROGRESS TIMER
        ========================================================= */

        let progressTimer = null;


        /* =========================================================
           RESUME POSITION
        ========================================================= */

        const savedWatchedSeconds =
            <?= json_encode(
                $watchedSeconds
            ) ?>;


        /* =========================================================
           COMPLETED STATE
        ========================================================= */

        const activityCompleted =
            <?= $status === "completed"
                ? "true"
                : "false" ?>;


        /* =========================================================
           RESUME ATTEMPT
        ========================================================= */

        let resumeAttempted = false;


        /* =========================================================
           YOUTUBE API READY
        ========================================================= */

        function onYouTubeIframeAPIReady() {


            <?php if ($youtubeId !== ""): ?>


                player =
                    new YT.Player(
                        "youtubePlayer", {

                            events: {

                                "onReady": onPlayerReady,

                                "onStateChange": onPlayerStateChange

                            }

                        }
                    );


            <?php endif; ?>

        }


        /* =========================================================
           PLAYER READY
        ========================================================= */

        function onPlayerReady(event) {


            /*
               Resume only if:

               1. Lecture is not completed
               2. There is a saved position
            */

            if (
                !activityCompleted &&
                savedWatchedSeconds > 0
            ) {

                resumeVideo(
                    event.target
                );

            }


            /*
               Start progress tracking.
            */

            startProgressTracking();
        }


        /* =========================================================
           RESUME VIDEO
        ========================================================= */

        function resumeVideo(
            youtubePlayer
        ) {


            if (
                resumeAttempted
            ) {

                return;
            }


            try {

                const duration =
                    youtubePlayer.getDuration();


                /*
                   Make sure YouTube knows
                   the video duration.
                */

                if (
                    duration > 0
                ) {

                    let resumePosition =
                        savedWatchedSeconds;


                    /*
                       Never seek outside the video.
                    */

                    if (
                        resumePosition >=
                        duration
                    ) {

                        resumePosition =
                            Math.max(
                                0,
                                duration - 1
                            );
                    }


                    /*
                       Only resume if there is
                       a meaningful saved position.
                    */

                    if (
                        resumePosition > 1
                    ) {

                        youtubePlayer.seekTo(
                            resumePosition,
                            true
                        );

                    }


                    resumeAttempted =
                        true;

                } else {

                    /*
                       YouTube duration may not
                       be available immediately.

                       Try again shortly.
                    */

                    setTimeout(
                        function() {

                            resumeVideo(
                                youtubePlayer
                            );

                        },
                        500
                    );
                }

            } catch (error) {

                console.error(
                    "Resume error:",
                    error
                );

            }
        }


        /* =========================================================
           PLAYER STATE
        ========================================================= */

        function onPlayerStateChange(
            event
        ) {


            /*
               PLAYING
            */

            if (
                event.data ===
                YT.PlayerState.PLAYING
            ) {

                startProgressTracking();
            }


            /*
               PAUSED
            */

            if (
                event.data ===
                YT.PlayerState.PAUSED
            ) {

                saveCurrentProgress();
            }


            /*
               BUFFERING
            */

            if (
                event.data ===
                YT.PlayerState.BUFFERING
            ) {

                /*
                   Do nothing.

                   Progress is saved by the
                   timer when appropriate.
                */

            }


            /*
               ENDED
            */

            if (
                event.data ===
                YT.PlayerState.ENDED
            ) {

                stopProgressTracking();

                completeActivity();
            }

        }


        /* =========================================================
           START PROGRESS TRACKING
        ========================================================= */

        function startProgressTracking() {


            if (
                progressTimer !== null
            ) {

                return;
            }


            /*
               Save immediately.
            */

            saveCurrentProgress();


            /*
               Continue saving every 5 seconds.
            */

            progressTimer =
                setInterval(
                    function() {

                        saveCurrentProgress();

                    },
                    5000
                );
        }


        /* =========================================================
           STOP PROGRESS TRACKING
        ========================================================= */

        function stopProgressTracking() {


            if (
                progressTimer !== null
            ) {

                clearInterval(
                    progressTimer
                );

                progressTimer = null;
            }
        }


        /* =========================================================
           SAVE CURRENT PROGRESS
        ========================================================= */

        function saveCurrentProgress() {


            if (
                !player ||
                typeof player.getCurrentTime !==
                "function"
            ) {

                return;
            }


            let currentTime = 0;

            let duration = 0;


            try {

                currentTime =
                    player.getCurrentTime();

                duration =
                    player.getDuration();

            } catch (error) {

                return;
            }


            if (
                duration <= 0
            ) {

                return;
            }


            /*
               Don't display 100% until
               the video actually ends.
            */

            let percentage =
                (
                    currentTime /
                    duration
                ) * 100;


            percentage =
                Math.min(
                    99.99,
                    Math.max(
                        0,
                        percentage
                    )
                );


            /*
               Update progress bar.
            */

            updateProgressBar(
                percentage
            );


            /*
               Save position to database.
            */

            fetch(
                    "save_progress.php", {

                        method: "POST",

                        headers: {

                            "Content-Type": "application/x-www-form-urlencoded"

                        },

                        body:

                            "lecture_id=" +
                            encodeURIComponent(
                                <?= $lectureId ?>
                            )

                            +

                            "&watched_seconds=" +
                            encodeURIComponent(
                                currentTime
                            )

                            +

                            "&video_duration=" +
                            encodeURIComponent(
                                duration
                            )

                    }
                )

                .then(
                    response =>
                    response.json()
                )

                .then(
                    data => {

                        if (
                            !data.success
                        ) {

                            console.error(
                                "Progress save failed:",
                                data.message
                            );

                        }

                    }
                )

                .catch(
                    error => {

                        console.error(
                            "Progress save error:",
                            error
                        );

                    }
                );
        }


        /* =========================================================
           UPDATE PROGRESS BAR
        ========================================================= */

        function updateProgressBar(
            percentage
        ) {


            const progressBar =
                document.getElementById(
                    "activityProgressBar"
                );


            const percentageText =
                document.getElementById(
                    "progressPercentage"
                );


            if (
                progressBar
            ) {

                progressBar.style.width =
                    percentage + "%";


                progressBar.setAttribute(
                    "aria-valuenow",
                    percentage
                );
            }


            if (
                percentageText
            ) {

                percentageText.textContent =
                    percentage.toFixed(1) + "%";
            }
        }


        /* =========================================================
           COMPLETE ACTIVITY
        ========================================================= */

        function completeActivity() {


            /*
               Don't complete twice.
            */

            if (
                activityCompleted
            ) {

                return;
            }


            fetch(
                    "complete_activity.php", {

                        method: "POST",

                        headers: {

                            "Content-Type": "application/x-www-form-urlencoded"

                        },

                        body: "lecture_id=" +
                            encodeURIComponent(
                                <?= $lectureId ?>
                            )

                    }
                )

                .then(
                    response =>
                    response.json()
                )

                .then(
                    data => {


                        if (
                            !data.success
                        ) {

                            console.error(
                                "Completion failed:",
                                data.message
                            );

                            return;
                        }


                        /*
                           Set progress to 100%.
                        */

                        updateProgressBar(
                            100
                        );


                        /*
                           Update progress bar class.
                        */

                        const progressBar =
                            document.getElementById(
                                "activityProgressBar"
                            );


                        if (
                            progressBar
                        ) {

                            progressBar.classList.remove(
                                "in-progress"
                            );

                            progressBar.classList.add(
                                "completed"
                            );

                            progressBar.style.width =
                                "100%";

                            progressBar.setAttribute(
                                "aria-valuenow",
                                "100"
                            );
                        }


                        /*
                           Update status badge.
                        */

                        const badge =
                            document.querySelector(
                                ".activity-status-badge"
                            );


                        if (
                            badge
                        ) {

                            badge.classList.remove(
                                "in-progress"
                            );

                            badge.classList.add(
                                "completed"
                            );


                            badge.innerHTML =
                                '<i class="bi bi-check-circle-fill"></i> Completed';
                        }


                        /*
                           Reload so completed_at
                           appears.
                        */

                        setTimeout(
                            function() {

                                window.location.reload();

                            },
                            800
                        );

                    }
                )

                .catch(
                    error => {

                        console.error(
                            "Completion error:",
                            error
                        );

                    }
                );
        }


        /* =========================================================
           SAVE BEFORE LEAVING
        ========================================================= */

        window.addEventListener(
            "beforeunload",
            function() {

                saveCurrentProgress();

            }
        );


        /* =========================================================
           PAGE VISIBILITY
        ========================================================= */

        document.addEventListener(
            "visibilitychange",
            function() {

                if (
                    document.visibilityState ===
                    "hidden"
                ) {

                    saveCurrentProgress();

                }

            }
        );
    </script>


    <!-- =========================================================
     GLOBAL SCRIPTS
========================================================= -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>