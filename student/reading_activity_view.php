<?php

/* =========================================================
   READING ACTIVITY VIEW
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
   STUDENT
   ========================================================= */

$studentId =
    trim(
        $_SESSION["user"]["student_id"]
            ?? ""
    );


/* =========================================================
   READING ACTIVITY ID
   ========================================================= */

$readingActivityId =
    isset($_GET["id"])
    ? (int)trim($_GET["id"])
    : 0;

$assignmentId =
    isset($_GET["assignment_id"])
    ? (int)$_GET["assignment_id"]
    : 0;


if (
    $readingActivityId <= 0 ||
    $assignmentId <= 0 ||
    $studentId === ""
) {

    header("Location: reading_activities.php");
    exit;
}


/* =========================================================
   GET STUDENT INFORMATION
   ========================================================= */

$studentName =
    trim(
        ($_SESSION["user"]["first_name"] ?? "")
            . " "
            . ($_SESSION["user"]["last_name"] ?? "")
    );


if ($studentName === "") {

    $studentName =
        $_SESSION["user"]["name"]
        ?? "Student";
}


/* =========================================================
   GET READING ACTIVITY
   ========================================================= */

$activitySQL = "
    SELECT
        r.id,
        r.title,
        r.description,
        r.file_url,
        r.file_type,
        r.department,
        r.year_level,
        r.section,
        a.assigned_at AS start_date,
        a.due_date,
        r.status,
        r.required_reading_minutes,
        r.created_at,
        a.id AS assignment_id,
        cc.course_code
    FROM class_code_reading_assignments a
    INNER JOIN class_codes cc
        ON cc.id = a.class_code_id
    INNER JOIN academic_year_semesters ays
        ON ays.id = cc.academic_year_semester_id
    INNER JOIN class_code_enrollments cce
        ON cce.class_code_id = cc.id
        AND cce.student_id = ?
        AND cce.status = 'Accepted'
    INNER JOIN reading_activity_table r
        ON r.id = a.reading_activity_id
    WHERE
        a.id = ?
        AND a.reading_activity_id = ?
        AND a.status = 'Active'
        AND cc.status = 'Active'
        AND ays.status = 'Active'
        AND (r.status = 'active' OR r.status IS NULL OR r.status = '')
    LIMIT 1
";

$activityStmt = $mysqli->prepare($activitySQL);

if (!$activityStmt) {
    header("Location: reading_activities.php");
    exit;
}

$activityStmt->bind_param(
    "sii",
    $studentId,
    $assignmentId,
    $readingActivityId
);

$activityStmt->execute();
$activityResult = $activityStmt->get_result();
$activity = $activityResult->fetch_assoc();
$activityStmt->close();


/* =========================================================
   ACTIVITY NOT FOUND
   ========================================================= */

if (!$activity) {

    header("Location: reading_activities.php");
    exit;
}


/* =========================================================
   ACTIVITY STATUS
   ========================================================= */

if (
    isset($activity["status"]) &&
    $activity["status"] !== "active" &&
    $activity["status"] !== ""
) {

    header("Location: reading_activities.php");
    exit;
}


/* =========================================================
   REQUIRED READING TIME
   ========================================================= */

$requiredReadingMinutes =
    (int)(
        $activity["required_reading_minutes"]
        ?? 30
    );


if (
    $requiredReadingMinutes <= 0
) {

    $requiredReadingMinutes = 30;
}


$requiredReadingSeconds =
    $requiredReadingMinutes * 60;


/* =========================================================
   GET STUDENT PROGRESS
   ========================================================= */

$progressSQL = "

    SELECT

        id,
        reading_activity_id,
        student_id,
        started_at,
        last_accessed_at,
        access_count,
        completed_at,
        status,
        reading_seconds

    FROM reading_activity_progress

    WHERE

        reading_activity_id = ?

        AND student_id = ?

    LIMIT 1
";


$progressStmt =
    $mysqli->prepare(
        $progressSQL
    );


if (!$progressStmt) {

    header("Location: reading_activities.php");
    exit;
}


$progressStmt->bind_param(
    "is",
    $readingActivityId,
    $studentId
);


$progressStmt->execute();


$progressResult =
    $progressStmt->get_result();


$progress =
    $progressResult->fetch_assoc();


$progressStmt->close();


/* =========================================================
   CREATE PROGRESS RECORD
   ========================================================= */

if (!$progress) {

    $insertSQL = "

        INSERT INTO reading_activity_progress (

            reading_activity_id,
            student_id,
            started_at,
            last_accessed_at,
            access_count,
            status,
            reading_seconds

        )

        VALUES (

            ?,
            ?,
            NOW(),
            NOW(),
            1,
            'in_progress',
            0

        )

    ";


    $insertStmt =
        $mysqli->prepare(
            $insertSQL
        );


    if (!$insertStmt) {

        header("Location: reading_activities.php");
        exit;
    }


    $insertStmt->bind_param(
        "is",
        $readingActivityId,
        $studentId
    );


    $insertStmt->execute();


    $insertStmt->close();


    /* =====================================================
       LOAD NEW RECORD
       ===================================================== */

    $progressStmt =
        $mysqli->prepare(
            $progressSQL
        );


    $progressStmt->bind_param(
        "is",
        $readingActivityId,
        $studentId
    );


    $progressStmt->execute();


    $progressResult =
        $progressStmt->get_result();


    $progress =
        $progressResult->fetch_assoc();


    $progressStmt->close();
}


/* =========================================================
   PROGRESS VALUES
   ========================================================= */

$progressId =
    (int)(
        $progress["id"]
        ?? 0
    );


$status =
    $progress["status"]
    ?? "in_progress";


$readingSeconds =
    (float)(
        $progress["reading_seconds"]
        ?? 0
    );


/* =========================================================
   COMPLETED CHECK
   ========================================================= */

$isCompleted =
    $status === "completed";


/* =========================================================
   INITIAL PROGRESS PERCENTAGE
   ========================================================= */

if (
    $requiredReadingSeconds > 0
) {

    $progressPercent =
        (
            $readingSeconds /
            $requiredReadingSeconds
        ) * 100;
} else {

    $progressPercent = 0;
}


/* =========================================================
   COMPLETED = 100%
   ========================================================= */

if ($isCompleted) {

    $progressPercent = 100;
}


/* =========================================================
   LIMIT PROGRESS
   ========================================================= */

$progressPercent =
    max(
        0,
        min(
            100,
            $progressPercent
        )
    );


/* =========================================================
   DISPLAY PROGRESS
   ========================================================= */

$displayProgress =
    $isCompleted
    ? 100
    : min(
        99.99,
        $progressPercent
    );


/* =========================================================
   FORMAT READING TIME
   ========================================================= */

$initialHours =
    floor(
        $readingSeconds / 3600
    );


$initialMinutes =
    floor(
        ($readingSeconds % 3600) / 60
    );


$initialSeconds =
    floor(
        $readingSeconds % 60
    );


$initialReadingTime =
    sprintf(
        "%02d:%02d:%02d",
        $initialHours,
        $initialMinutes,
        $initialSeconds
    );


/* =========================================================
   FORMAT REQUIRED TIME
   ========================================================= */

$requiredHours =
    floor(
        $requiredReadingSeconds / 3600
    );


$requiredMinutesDisplay =
    floor(
        ($requiredReadingSeconds % 3600) / 60
    );


$requiredSecondsDisplay =
    floor(
        $requiredReadingSeconds % 60
    );


$requiredReadingTime =
    sprintf(
        "%02d:%02d:%02d",
        $requiredHours,
        $requiredMinutesDisplay,
        $requiredSecondsDisplay
    );


/* =========================================================
   DATE FORMATTING
   ========================================================= */

$startDate =
    !empty($activity["start_date"])
    ? date(
        "M d, Y h:i A",
        strtotime(
            $activity["start_date"]
        )
    )
    : "Not specified";


$dueDate =
    !empty($activity["due_date"])
    ? date(
        "M d, Y h:i A",
        strtotime(
            $activity["due_date"]
        )
    )
    : "Not specified";


/* =========================================================
   FILE TYPE
   ========================================================= */

$fileType =
    strtolower(
        trim(
            $activity["file_type"]
                ?? ""
        )
    );


$fileUrl =
    trim(
        $activity["file_url"]
            ?? ""
    );


/* =========================================================
   GOOGLE FILE ID
   ========================================================= */

$driveFileId = "";


/* =========================================================
   GOOGLE SLIDES
   ========================================================= */

if (
    preg_match(
        '/docs\.google\.com\/presentation\/d\/([a-zA-Z0-9_-]+)/',
        $fileUrl,
        $matches
    )
) {

    $driveFileId =
        $matches[1];
}


/* =========================================================
   GOOGLE DRIVE FILE
   ========================================================= */ elseif (
    preg_match(
        '/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/',
        $fileUrl,
        $matches
    )
) {

    $driveFileId =
        $matches[1];
}


/* =========================================================
   GOOGLE DRIVE ID PARAMETER
   ========================================================= */ elseif (
    preg_match(
        '/[?&]id=([a-zA-Z0-9_-]+)/',
        $fileUrl,
        $matches
    )
) {

    $driveFileId =
        $matches[1];
}


/* =========================================================
   RAW GOOGLE FILE ID
   ========================================================= */ elseif (
    preg_match(
        '/^[a-zA-Z0-9_-]{20,}$/',
        $fileUrl
    )
) {

    $driveFileId =
        $fileUrl;
}


/* =========================================================
   VIEWER URL
   ========================================================= */

$viewerUrl = "";


/* =========================================================
   GOOGLE SLIDES EMBED
   ========================================================= */

if (
    $fileType === "ppt" ||
    $fileType === "pptx"
) {

    if ($driveFileId !== "") {

        $viewerUrl =
            "https://docs.google.com/presentation/d/"
            . rawurlencode($driveFileId)
            . "/embed";
    }
}


/* =========================================================
   GOOGLE DOCUMENT
   ========================================================= */ elseif (
    $fileType === "doc" ||
    $fileType === "docx"
) {

    if ($driveFileId !== "") {

        $viewerUrl =
            "https://docs.google.com/document/d/"
            . rawurlencode($driveFileId)
            . "/preview";
    }
}


/* =========================================================
   GOOGLE PDF
   ========================================================= */ elseif (
    $fileType === "pdf"
) {

    if ($driveFileId !== "") {

        $viewerUrl =
            "https://drive.google.com/file/d/"
            . rawurlencode($driveFileId)
            . "/preview";
    } else {

        $viewerUrl =
            $fileUrl;
    }
}


/* =========================================================
   FALLBACK
   ========================================================= */

if (
    $viewerUrl === ""
) {

    $viewerUrl =
        $fileUrl;
}


/* =========================================================
   DESCRIPTION
   ========================================================= */

$description =
    trim(
        $activity["description"]
            ?? ""
    );


/* =========================================================
   PAGE GLOBALS
   ========================================================= */

include 'globals/head.php';

?>

<style>
    /* =========================================================
   READING VIEWER CARD
   ========================================================= */

    .reading-viewer-card {

        background: var(--bs-body-bg);

        border: 1px solid var(--bs-border-color);

        border-radius: 12px;

        overflow: hidden;

        transition:
            background-color 0.25s ease,
            border-color 0.25s ease,
            box-shadow 0.25s ease;
    }


    /* =========================================================
   READING HEADER
   ========================================================= */

    .reading-viewer-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 15px;

        padding: 16px 20px;

        background: var(--bs-body-bg);

        border-bottom: 1px solid var(--bs-border-color);

        color: var(--bs-body-color);

        transition:
            background-color 0.25s ease,
            border-color 0.25s ease,
            color 0.25s ease;
    }


    .reading-viewer-title {

        display: flex;

        align-items: center;

        gap: 10px;

        font-weight: 600;

        color: var(--bs-body-color);

    }


    .reading-viewer-title i {

        font-size: 20px;

        color: var(--bs-primary);

    }


    /* =========================================================
   DOCUMENT VIEWER
   ========================================================= */

    .reading-viewer-container {

        width: 100%;

        height: 700px;

        background: var(--bs-tertiary-bg);

        position: relative;

        transition:
            background-color 0.25s ease;
    }


    .reading-viewer-frame-wrapper {

        width: 100%;

        height: 100%;

        overflow: auto;

        background: var(--bs-body-bg);

        transition:
            background-color 0.25s ease;
    }


    .reading-viewer-container iframe {

        width: 100%;

        height: 100%;

        border: 0;

        display: block;

        margin-inline: auto;

        background: var(--bs-body-bg);
        transform: scale(1);
        transform-origin: top center;
        transition: transform 0.15s ease;

    }

    /* Compact toolbar indicators and controls. These remain outside
       the transformed iframe, so portal controls never scale with the page. */
    .reader-zoom-controls {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        flex: 0 0 auto;
    }

    .reader-zoom-controls .btn {
        min-width: 29px;
        min-height: 29px;
        padding: 3px 7px;
        font-size: 0.72rem;
        line-height: 1.15;
    }

    .reader-zoom-level {
        min-width: 39px;
        text-align: center;
        font-size: 0.70rem;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
    }

    .reading-viewer-controls>.badge {
        padding: 4px 6px;
        font-size: 0.62rem;
        font-weight: 600;
    }

    .reader-live-time {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 7px;
        border: 1px solid var(--bs-border-color);
        border-radius: 7px;
        font-size: 0.72rem;
        font-weight: 650;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .reader-live-time small {
        font-size: 0.62rem;
    }

    .reading-viewer-controls>.btn {
        padding: 4px 8px;
        font-size: 0.72rem;
        line-height: 1.2;
        white-space: nowrap;
    }

    /* Keep the toolbar fixed at its normal size; only the iframe is scaled. */
    .reading-viewer-header,
    .reading-viewer-controls,
    .reader-zoom-controls,
    .reader-live-time {
        transform: none !important;
        zoom: 1 !important;
    }


    /* Full-screen reader keeps the portal page and timer running. */
    .reading-viewer-card:fullscreen,
    .reading-viewer-card.reader-fullscreen-mode {
        position: fixed;
        inset: 0;
        z-index: 9999;
        width: 100%;
        height: 100%;
        margin: 0 !important;
        border: 0;
        border-radius: 0;
        background: var(--bs-body-bg);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .reading-viewer-card:fullscreen .reading-viewer-container,
    .reading-viewer-card.reader-fullscreen-mode .reading-viewer-container {
        height: auto;
        min-height: 0;
        flex: 1 1 auto;
    }

    .reading-viewer-card:fullscreen .reading-viewer-frame-wrapper,
    .reading-viewer-card.reader-fullscreen-mode .reading-viewer-frame-wrapper {
        height: 100%;
    }

    .reading-viewer-controls {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
    }

    @media (max-width: 575.98px) {
        .reading-viewer-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .reading-viewer-controls {
            width: 100%;
            justify-content: space-between;
        }

        .reading-viewer-container {
            height: 65vh;
        }
    }


    /* =========================================================
   DARK MODE
   ========================================================= */

    [data-theme="dark"] .reading-viewer-card {

        background: #1b1b1b;

        border-color: #343434;

    }


    [data-theme="dark"] .reading-viewer-header {

        background: #1b1b1b;

        border-color: #343434;

        color: #f1f1f1;

    }


    [data-theme="dark"] .reading-viewer-title {

        color: #f1f1f1;

    }


    [data-theme="dark"] .reading-viewer-container {

        background: #121212;

    }


    [data-theme="dark"] .reading-viewer-frame-wrapper {

        background: #121212;

    }


    /* =========================================================
   READING TIMER
   ========================================================= */

    .reading-time-value {

        font-size: 20px;

        font-weight: 700;

        letter-spacing: 1px;

    }


    .reading-time-label {

        font-size: 13px;

        color: var(--bs-secondary-color);

    }


    /* =========================================================
   COMPLETION MESSAGE
   ========================================================= */

    .reading-completed-message {

        display: flex;

        align-items: center;

        gap: 8px;

        font-weight: 600;

    }


    .reading-progress-message {

        font-size: 14px;

        color: var(--bs-secondary-color);

    }


    /* =========================================================
   MOBILE
   ========================================================= */

    @media (max-width: 768px) {

        .reading-viewer-container {

            height: 550px;

        }


        .reading-viewer-header {

            padding: 14px 16px;

        }

    }

    /* =========================================================
   YOUR ACTIVITY
========================================================== */

    .activity-details {
        width: 100%;
    }


    /* =========================================================
   ACTIVITY INFORMATION GRID
========================================================== */

    .activity-details .information-grid {
        display: grid;

        grid-template-columns: repeat(2, minmax(0, 1fr));

        gap: 18px 20px;

        width: 100%;
    }


    /* =========================================================
   ACTIVITY INFORMATION ITEM
========================================================== */

    .activity-details .information-grid>div {
        min-width: 0;

        width: 100%;
    }


    /* =========================================================
   LABEL
========================================================== */

    .activity-details .information-grid span {
        display: block;

        margin-bottom: 5px;

        font-size: 13px;

        color: var(--bs-text-color);
    }


    /* =========================================================
   VALUE
========================================================== */

    .activity-details .information-grid strong {
        display: block;

        width: 100%;

        font-size: 14px;

        font-weight: 600;

        color: var(--bs-text-color);

        overflow-wrap: anywhere;

        word-break: break-word;
    }


    /* =========================================================
   TABLET
========================================================== */

    @media (max-width: 991.98px) {

        .activity-details .information-grid {
            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 16px;
        }

    }


    /* =========================================================
   MOBILE
========================================================== */

    @media (max-width: 575.98px) {

        .activity-details .information-grid {
            grid-template-columns: 1fr;

            gap: 14px;
        }


        .activity-details .information-grid>div {
            padding-bottom: 10px;

            border-bottom: 1px solid var(--bs-border-color);
        }


        .activity-details .information-grid>div:last-child {
            padding-bottom: 0;

            border-bottom: none;
        }

    }


    /* =========================================================
   VERY SMALL DEVICES
========================================================== */

    @media (max-width: 375px) {

        .activity-details .information-grid {
            gap: 12px;
        }


        .activity-details .information-grid span {
            font-size: 12px;
        }


        .activity-details .information-grid strong {
            font-size: 13px;
        }

    }
</style>

<?php

/* =========================================================
   SIDEBAR
   ========================================================= */

include 'globals/sidebar.php';


/* =========================================================
   TOPBAR
   ========================================================= */

include 'globals/topbar.php';

?>

<!-- =========================================================
     MAIN CONTENT
========================================================== -->

<main class="main-content">


    <div class="content-wrapper">


        <!-- =================================================
         BACK BUTTON
    ================================================== -->

        <a
            href="reading_activities.php"
            class="back-link mb-3">

            <i class="bi bi-arrow-left me-2"></i>

            Back to Reading Activities

        </a>


        <!-- =================================================
         ACTIVITY HEADER
    ================================================== -->

        <div class="activity-view-header">


            <div>

                <div class="activity-view-label">

                    <i class="bi bi-book"></i>

                    Reading Activity

                </div>


                <h1 class="mb-2">

                    <?= htmlspecialchars(
                        $activity["title"]
                    ) ?>

                </h1>


                <?php if ($description !== ""): ?>

                    <p class="text-secondary mb-0">

                        <?= nl2br(
                            htmlspecialchars(
                                $description
                            )
                        ) ?>

                    </p>

                <?php endif; ?>

            </div>


            <!-- =============================================
             STATUS
        ============================================== -->

            <div>

                <span
                    id="activityStatusBadge"
                    class="activity-status-badge
                <?= $isCompleted
                    ? 'completed'
                    : 'in-progress' ?>">

                    <?php if ($isCompleted): ?>

                        <i class="bi bi-check-circle-fill"></i>

                        Completed

                    <?php else: ?>

                        <i class="bi bi-hourglass-split"></i>

                        In Progress

                    <?php endif; ?>

                </span>

            </div>


        </div>


        <!-- =================================================
         PROGRESS CARD
    ================================================== -->

        <div class="activity-progress-card mt-4">


            <div class="activity-progress-header">


                <div>

                    <strong>

                        Reading Progress

                    </strong>


                    <div
                        class="reading-progress-message"
                        id="readingProgressMessage">

                        <?php if ($isCompleted): ?>

                            Required reading time completed.

                        <?php else: ?>

                            Read the material for at least

                            <?= number_format(
                                $requiredReadingMinutes
                            ) ?>

                            minute<?= $requiredReadingMinutes != 1 ? 's' : '' ?>.

                        <?php endif; ?>

                    </div>

                </div>


                <div
                    class="fw-bold"
                    id="progressPercentage">

                    <?= $isCompleted
                        ? "100%"
                        : number_format(
                            $displayProgress,
                            2
                        ) . "%" ?>

                </div>


            </div>


            <!-- =============================================
             PROGRESS BAR
        ============================================== -->

            <div class="progress activity-progress">


                <div
                    id="readingProgressBar"
                    class="progress-bar
                <?= $isCompleted
                    ? 'completed'
                    : 'in-progress' ?>"
                    role="progressbar"
                    style="width: <?= $displayProgress ?>%"
                    aria-valuenow="<?= $displayProgress ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"></div>


            </div>


            <!-- =============================================
             READING TIME
        ============================================== -->

            <div
                class="d-flex
                   justify-content-between
                   align-items-center
                   mt-3
                   flex-wrap
                   gap-3">


                <div>

                    <div class="reading-time-label">

                        Actual Reading Time

                    </div>


                    <div
                        class="reading-time-value"
                        id="readingTime">

                        <?= $initialReadingTime ?>

                    </div>

                </div>


                <div>

                    <div class="reading-time-label">

                        Required Reading Time

                    </div>


                    <div
                        class="reading-time-value">

                        <?= $requiredReadingTime ?>

                    </div>

                </div>


            </div>


            <!-- =============================================
             COMPLETED MESSAGE
        ============================================== -->

            <div
                id="completedMessage"
                class="completed-message mt-3
            <?= $isCompleted
                ? ''
                : 'd-none' ?>">

                <i class="bi bi-check-circle-fill"></i>

                Reading activity completed successfully.

            </div>


        </div>


        <!-- =================================================
         DOCUMENT VIEWER
    ================================================== -->

        <div class="reading-viewer-card mt-4">


            <div class="reading-viewer-header">


                <div class="reading-viewer-title">

                    <?php if ($fileType === "pdf"): ?>

                        <i class="bi bi-file-earmark-pdf"></i>

                    <?php elseif (
                        $fileType === "ppt" ||
                        $fileType === "pptx"
                    ): ?>

                        <i class="bi bi-file-earmark-slides"></i>

                    <?php elseif (
                        $fileType === "doc" ||
                        $fileType === "docx"
                    ): ?>

                        <i class="bi bi-file-earmark-word"></i>

                    <?php else: ?>

                        <i class="bi bi-file-earmark-text"></i>

                    <?php endif; ?>


                    <span>

                        <?= htmlspecialchars(
                            $activity["title"]
                        ) ?>

                    </span>

                </div>


                <div class="reading-viewer-controls">

                    <span class="reader-live-time" title="Total time spent reading">
                        <i class="bi bi-stopwatch"></i>
                        <span id="readerLiveTime"><?= htmlspecialchars($initialReadingTime) ?></span>
                        <small class="text-secondary">total</small>
                    </span>

                    <span class="badge text-bg-secondary">
                        <?= strtoupper(htmlspecialchars($fileType)) ?>
                    </span>

                    <div class="reader-zoom-controls" role="group" aria-label="Document zoom controls">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="readerZoomOut" aria-label="Zoom out" title="Zoom out">
                            <i class="bi bi-zoom-out"></i>
                        </button>
                        <span class="reader-zoom-level" id="readerZoomLevel">100%</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="readerZoomIn" aria-label="Zoom in" title="Zoom in">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="readerZoomReset" title="Reset zoom">
                            Reset
                        </button>
                    </div>

                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        id="readerFullscreenButton"
                        aria-label="Open full-screen reader">
                        <i class="bi bi-arrows-fullscreen me-1"></i>
                        Read Full Screen
                    </button>

                </div>

            </div>


            <!-- =============================================
             VIEWER
        ============================================== -->

            <div class="reading-viewer-container">


                <?php if ($viewerUrl !== ""): ?>

                    <div
                        class="reading-viewer-frame-wrapper"
                        id="readingViewerFrameWrapper">

                        <iframe
                            id="readingViewer"
                            src="<?= htmlspecialchars(
                                        $viewerUrl
                                    ) ?>"
                            allowfullscreen>
                        </iframe>

                    </div>

                <?php else: ?>

                    <div
                        class="video-error
                           d-flex
                           align-items-center
                           justify-content-center
                           h-100">

                        <div class="text-center p-4">

                            <i
                                class="bi
                                   bi-exclamation-triangle
                                   fs-1"></i>


                            <h5 class="mt-3">

                                Unable to load reading material

                            </h5>


                            <p class="text-secondary mb-0">

                                The document URL is invalid
                                or unsupported.

                            </p>

                        </div>

                    </div>

                <?php endif; ?>


            </div>


        </div>


        <!-- =================================================
         INFORMATION
    ================================================== -->

        <div class="row g-3 mt-1">


            <!-- =============================================
             ACTIVITY INFORMATION
        ============================================== -->

            <div class="col-lg-8">


                <div class="information-card">


                    <div class="information-card-header mb-3">

                        <i class="bi bi-info-circle"></i>

                        Activity Information

                    </div>


                    <div class="information-grid">


                        <div>

                            <span>

                                Department

                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $activity["department"]
                                        ?: "All Departments"
                                ) ?>

                            </strong>

                        </div>


                        <div>

                            <span>

                                Year Level

                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $activity["year_level"]
                                        ?: "All Year Levels"
                                ) ?>

                            </strong>

                        </div>


                        <div>

                            <span>

                                Section

                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $activity["section"]
                                        ?: "All Sections"
                                ) ?>

                            </strong>

                        </div>


                        <div>

                            <span>

                                Required Reading

                            </span>

                            <strong>

                                <?= number_format(
                                    $requiredReadingMinutes
                                ) ?>

                                minute<?= $requiredReadingMinutes != 1 ? 's' : '' ?>

                            </strong>

                        </div>


                        <div>

                            <span>

                                Start Date

                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $startDate
                                ) ?>

                            </strong>

                        </div>


                        <div>

                            <span>

                                Due Date

                            </span>

                            <strong>

                                <?= htmlspecialchars(
                                    $dueDate
                                ) ?>

                            </strong>

                        </div>


                    </div>


                </div>


            </div>


            <!-- =============================================
             ACTIVITY DETAILS
        ============================================== -->

            <div class="col-lg-4">


                <div class="information-card activity-details">


                    <div class="information-card-header">

                        <i class="bi bi-person-check"></i>

                        Your Activity

                    </div>


                    <div class="information-grid">


                        <div>

                            <span>

                                Student

                            </span>

                            <strong class="text-center">

                                <?= htmlspecialchars(
                                    $studentName
                                ) ?>

                            </strong>

                        </div>


                        <div>

                            <span>

                                Status

                            </span>

                            <strong class="text-center"
                                id="activityDetailsStatus">

                                <?= $isCompleted
                                    ? "Completed"
                                    : "In Progress" ?>

                            </strong>

                        </div>


                        <div>

                            <span>

                                Reading Time

                            </span>

                            <strong class="text-center"
                                id="activityDetailsTime">

                                <?= $initialReadingTime ?>

                            </strong>

                        </div>


                        <div>

                            <span>

                                Access Count

                            </span>

                            <strong class="text-center">

                                <?= number_format(
                                    (int)(
                                        $progress["access_count"]
                                        ?? 0
                                    )
                                ) ?>

                            </strong>

                        </div>


                    </div>


                </div>


            </div>


        </div>


    </div>


</main>

<?php

/* =========================================================
   SCRIPTS
   ========================================================= */

include 'globals/scripts.php';

?>

<script>
    /* =========================================================
   READING ACTIVITY TRACKER
   ETS-Async Learning Portal
   ========================================================= */


    /* =========================================================
       VARIABLES
       ========================================================= */

    const readingActivityId =
        <?= (int)$readingActivityId ?>;

    const assignmentId =
        <?= (int)$assignmentId ?>;


    const requiredReadingSeconds =
        <?= (int)$requiredReadingSeconds ?>;


    let readingSeconds =
        <?= json_encode(
            (float)$readingSeconds
        ) ?>;


    let isCompleted =
        <?= $isCompleted ? "true" : "false" ?>;


    let lastTickTime =
        Date.now();


    let lastSaveTime =
        Date.now();


    let completionRequested =
        false;


    /* =========================================================
       DOM ELEMENTS
       ========================================================= */

    const readingTimeElement =
        document.getElementById(
            "readingTime"
        );


    const readerLiveTime =
        document.getElementById("readerLiveTime");

    const readerFullscreenButton =
        document.getElementById("readerFullscreenButton");

    const readingViewer = document.getElementById("readingViewer");
    const readerZoomIn = document.getElementById("readerZoomIn");
    const readerZoomOut = document.getElementById("readerZoomOut");
    const readerZoomReset = document.getElementById("readerZoomReset");
    const readerZoomLevel = document.getElementById("readerZoomLevel");
    const readingViewerFrameWrapper = document.getElementById("readingViewerFrameWrapper");
    let readerZoom = 1;

    const readingViewerCard =
        document.querySelector(".reading-viewer-card");


    const activityDetailsTime =
        document.getElementById(
            "activityDetailsTime"
        );


    const progressPercentage =
        document.getElementById(
            "progressPercentage"
        );


    const readingProgressBar =
        document.getElementById(
            "readingProgressBar"
        );


    const readingProgressMessage =
        document.getElementById(
            "readingProgressMessage"
        );


    const completedMessage =
        document.getElementById(
            "completedMessage"
        );


    const activityStatusBadge =
        document.getElementById(
            "activityStatusBadge"
        );


    const activityDetailsStatus =
        document.getElementById(
            "activityDetailsStatus"
        );


    /* =========================================================
       FORMAT TIME
       ========================================================= */

    function formatReadingTime(
        totalSeconds
    ) {

        totalSeconds =
            Math.max(
                0,
                Math.floor(
                    totalSeconds
                )
            );


        const hours =
            Math.floor(
                totalSeconds / 3600
            );


        const minutes =
            Math.floor(
                (totalSeconds % 3600) / 60
            );


        const seconds =
            totalSeconds % 60;


        return (

            String(hours).padStart(2, "0")

            +

            ":"

            +

            String(minutes).padStart(2, "0")

            +

            ":"

            +

            String(seconds).padStart(2, "0")

        );
    }


    /* =========================================================
       UPDATE PROGRESS DISPLAY
       ========================================================= */

    function updateProgressDisplay() {


        /* =====================================================
           TIME
        ====================================================== */

        const formattedTime =
            formatReadingTime(
                readingSeconds
            );


        if (
            readingTimeElement
        ) {

            readingTimeElement.textContent =
                formattedTime;

        }


        if (readerLiveTime) {
            readerLiveTime.textContent = formattedTime;
        }


        if (
            activityDetailsTime
        ) {

            activityDetailsTime.textContent =
                formattedTime;

        }


        /* =====================================================
           PERCENTAGE
        ====================================================== */

        let percentage = 0;


        if (
            requiredReadingSeconds > 0
        ) {

            percentage =
                (
                    readingSeconds /
                    requiredReadingSeconds
                ) * 100;

        }


        /* =====================================================
           COMPLETION
        ====================================================== */

        if (
            isCompleted
        ) {

            percentage = 100;

        } else {

            percentage =
                Math.min(
                    99.99,
                    percentage
                );

        }


        percentage =
            Math.max(
                0,
                percentage
            );


        /* =====================================================
           DISPLAY PERCENTAGE
        ====================================================== */

        if (
            progressPercentage
        ) {

            progressPercentage.textContent =
                isCompleted ?
                "100%" :
                percentage.toFixed(2) + "%";

        }


        /* =====================================================
           PROGRESS BAR
        ====================================================== */

        if (
            readingProgressBar
        ) {

            readingProgressBar.style.width =
                percentage + "%";


            readingProgressBar.setAttribute(
                "aria-valuenow",
                percentage
            );

        }


        /* =====================================================
           STATUS
        ====================================================== */

        if (
            isCompleted
        ) {

            if (
                activityStatusBadge
            ) {

                activityStatusBadge.classList.remove(
                    "in-progress"
                );


                activityStatusBadge.classList.add(
                    "completed"
                );


                activityStatusBadge.innerHTML =
                    '<i class="bi bi-check-circle-fill"></i> Completed';

            }


            if (
                readingProgressBar
            ) {

                readingProgressBar.classList.remove(
                    "in-progress"
                );


                readingProgressBar.classList.add(
                    "completed"
                );

            }


            if (
                activityDetailsStatus
            ) {

                activityDetailsStatus.textContent =
                    "Completed";

            }


            if (
                readingProgressMessage
            ) {

                readingProgressMessage.textContent =
                    "Required reading time completed.";

            }


            if (
                completedMessage
            ) {

                completedMessage.classList.remove(
                    "d-none"
                );

            }

        } else {

            if (
                activityDetailsStatus
            ) {

                activityDetailsStatus.textContent =
                    "In Progress";

            }

        }

    }


    /* =========================================================
       SAVE READING PROGRESS
       ========================================================= */

    async function saveReadingProgress() {


        if (
            isCompleted
        ) {

            return;

        }


        try {

            const formData =
                new FormData();


            formData.append(
                "reading_activity_id",
                readingActivityId
            );

            formData.append(
                "assignment_id",
                assignmentId
            );


            formData.append(
                "reading_seconds",
                readingSeconds
            );


            const response =
                await fetch(
                    "save_reading_progress.php", {

                        method: "POST",

                        body: formData,

                        credentials: "same-origin",

                        keepalive: true

                    }
                );


            if (
                !response.ok
            ) {

                return;

            }


            const data =
                await response.json();


            if (
                data.success
            ) {

                lastSaveTime =
                    Date.now();

            }

        } catch (
            error
        ) {

            console.error(
                "Unable to save reading progress:",
                error
            );

        }

    }


    /* =========================================================
       COMPLETE READING ACTIVITY
       ========================================================= */

    async function completeReadingActivity() {


        if (
            isCompleted ||
            completionRequested
        ) {

            return;

        }


        if (
            readingSeconds <
            requiredReadingSeconds
        ) {

            return;

        }


        completionRequested =
            true;


        /* =====================================================
           SAVE FINAL TIME
        ====================================================== */

        await saveReadingProgress();


        try {

            const formData =
                new FormData();


            formData.append(
                "reading_activity_id",
                readingActivityId
            );

            formData.append(
                "assignment_id",
                assignmentId
            );


            /*
             * Send the current timer value too.
             *
             * This prevents a race condition where the
             * save request has not finished before the
             * completion request reaches the server.
             */

            formData.append(
                "reading_seconds",
                readingSeconds
            );


            const response =
                await fetch(
                    "complete_reading_activity.php", {

                        method: "POST",

                        body: formData,

                        credentials: "same-origin"

                    }
                );


            if (
                !response.ok
            ) {

                completionRequested =
                    false;

                return;

            }


            const data =
                await response.json();


            if (
                data.success
            ) {

                isCompleted =
                    true;


                readingSeconds =
                    Math.max(
                        readingSeconds,
                        requiredReadingSeconds
                    );


                updateProgressDisplay();

            } else {

                completionRequested =
                    false;

            }

        } catch (
            error
        ) {

            console.error(
                "Unable to complete reading activity:",
                error
            );


            completionRequested =
                false;

        }

    }


    /* =========================================================
       DOCUMENT ZOOM CONTROLS
       Only the embedded iframe is scaled. The portal toolbar and its
       indicators are separate siblings and retain their normal size.
       The portal remains active, so the reading timer keeps running.
       ========================================================= */

    function applyReaderZoom() {
        if (!readingViewer) return;

        /*
         * Keep the visible iframe viewport centered as it scales.
         * Reducing its layout width/height before scaling preserves the
         * viewer's outer dimensions while enlarging the document content.
         */
        readingViewer.style.transform = `scale(${readerZoom})`;
        readingViewer.style.transformOrigin = "top center";
        readingViewer.style.width = `${100 / readerZoom}%`;
        readingViewer.style.height = `${100 / readerZoom}%`;
        readingViewer.style.marginLeft = "auto";
        readingViewer.style.marginRight = "auto";

        if (readerZoomLevel) {
            readerZoomLevel.textContent = `${Math.round(readerZoom * 100)}%`;
        }
    }

    if (readerZoomIn) {
        readerZoomIn.addEventListener("click", function() {
            readerZoom = Math.min(3, Math.round((readerZoom + 0.1) * 100) / 100);
            applyReaderZoom();
        });
    }

    if (readerZoomOut) {
        readerZoomOut.addEventListener("click", function() {
            readerZoom = Math.max(0.5, Math.round((readerZoom - 0.1) * 100) / 100);
            applyReaderZoom();
        });
    }

    if (readerZoomReset) {
        readerZoomReset.addEventListener("click", function() {
            readerZoom = 1;
            applyReaderZoom();
            if (readingViewerFrameWrapper) {
                readingViewerFrameWrapper.scrollTo({
                    top: 0,
                    left: 0,
                    behavior: "smooth"
                });
            }
        });
    }

    /* =========================================================
       FULL-SCREEN READING MODE
       ========================================================= */

    async function enterReaderFullscreen() {
        if (!readingViewerCard) return;

        try {
            if (readingViewerCard.requestFullscreen) {
                await readingViewerCard.requestFullscreen();
            } else {
                readingViewerCard.classList.add("reader-fullscreen-mode");
            }
        } catch (error) {
            // Fallback keeps the document inside this page so the timer continues.
            readingViewerCard.classList.add("reader-fullscreen-mode");
        }

        if (readerFullscreenButton) {
            readerFullscreenButton.innerHTML =
                '<i class="bi bi-fullscreen-exit me-1"></i> Exit Full Screen';
            readerFullscreenButton.setAttribute("aria-label", "Exit full-screen reader");
        }
    }

    async function exitReaderFullscreen() {
        if (document.fullscreenElement && document.exitFullscreen) {
            try {
                await document.exitFullscreen();
            } catch (error) {
                /* no-op */ }
        }

        if (readingViewerCard) {
            readingViewerCard.classList.remove("reader-fullscreen-mode");
        }

        if (readerFullscreenButton) {
            readerFullscreenButton.innerHTML =
                '<i class="bi bi-arrows-fullscreen me-1"></i> Read Full Screen';
            readerFullscreenButton.setAttribute("aria-label", "Open full-screen reader");
        }
    }

    if (readerFullscreenButton) {
        readerFullscreenButton.addEventListener("click", function() {
            const isFullscreen =
                document.fullscreenElement === readingViewerCard ||
                readingViewerCard?.classList.contains("reader-fullscreen-mode");

            if (isFullscreen) {
                exitReaderFullscreen();
            } else {
                enterReaderFullscreen();
            }
        });
    }

    document.addEventListener("fullscreenchange", function() {
        const isFullscreen = document.fullscreenElement === readingViewerCard;
        if (readerFullscreenButton && !isFullscreen && readingViewerCard) {
            readingViewerCard.classList.remove("reader-fullscreen-mode");
            readerFullscreenButton.innerHTML =
                '<i class="bi bi-arrows-fullscreen me-1"></i> Read Full Screen';
            readerFullscreenButton.setAttribute("aria-label", "Open full-screen reader");
        }
    });


    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape" && readingViewerCard?.classList.contains("reader-fullscreen-mode")) {
            exitReaderFullscreen();
        }
    });


    /* =========================================================
       TIMER TICK
       ========================================================= */

    function readingTimerTick() {


        /* =====================================================
           ALREADY COMPLETED
        ====================================================== */

        if (
            isCompleted
        ) {

            updateProgressDisplay();

            return;

        }


        /* =====================================================
           PAGE HIDDEN
        ====================================================== */

        if (
            document.visibilityState !==
            "visible"
        ) {

            lastTickTime =
                Date.now();

            return;

        }


        /* =====================================================
           CURRENT TIME
        ====================================================== */

        const now =
            Date.now();


        const elapsedMilliseconds =
            now -
            lastTickTime;


        lastTickTime =
            now;


        /* =====================================================
           ADD READING TIME
        ====================================================== */

        if (
            elapsedMilliseconds > 0 &&
            elapsedMilliseconds < 10000
        ) {

            readingSeconds +=
                elapsedMilliseconds /
                1000;

        }


        /* =====================================================
           UPDATE DISPLAY
        ====================================================== */

        updateProgressDisplay();


        /* =====================================================
           SAVE EVERY 5 SECONDS
        ====================================================== */

        if (
            now -
            lastSaveTime >= 5000
        ) {

            saveReadingProgress();

        }


        /* =====================================================
           COMPLETE
        ====================================================== */

        if (
            readingSeconds >=
            requiredReadingSeconds
        ) {

            readingSeconds =
                Math.max(
                    readingSeconds,
                    requiredReadingSeconds
                );


            updateProgressDisplay();


            completeReadingActivity();

        }

    }


    /* =========================================================
       START TIMER
       ========================================================== */

    setInterval(
        readingTimerTick,
        1000
    );


    /* =========================================================
       INITIAL DISPLAY
       ========================================================== */

    updateProgressDisplay();


    /* =========================================================
       VISIBILITY CHANGE
       ========================================================== */

    document.addEventListener(
        "visibilitychange",
        function() {


            if (
                document.visibilityState ===
                "hidden"
            ) {

                saveReadingProgress();


                lastTickTime =
                    Date.now();

            } else {

                lastTickTime =
                    Date.now();

            }

        }
    );


    /* =========================================================
       BEFORE PAGE UNLOAD
       ========================================================== */

    window.addEventListener(
        "beforeunload",
        function() {


            if (
                isCompleted
            ) {

                return;

            }


            const formData =
                new FormData();


            formData.append(
                "reading_activity_id",
                readingActivityId
            );

            formData.append(
                "assignment_id",
                assignmentId
            );


            formData.append(
                "reading_seconds",
                readingSeconds
            );


            fetch(
                "save_reading_progress.php", {

                    method: "POST",

                    body: formData,

                    credentials: "same-origin",

                    keepalive: true

                }
            );

        }
    );


    /* =========================================================
       PAGE LOAD
    ========================================================== */

    lastTickTime =
        Date.now();


    lastSaveTime =
        Date.now();
</script>

<script>
    /* =========================================================
   READING VIEWER THEME
   ETS-Async Learning Portal
   ========================================================= */

    (function() {


        /* =====================================================
           HTML ELEMENT
        ====================================================== */

        const htmlElement =
            document.documentElement;


        /* =====================================================
           UPDATE READING VIEWER THEME
        ====================================================== */

        function updateReadingViewerTheme() {


            const currentTheme =
                htmlElement.getAttribute(
                    "data-theme"
                );


            const viewerElements =
                document.querySelectorAll(
                    ".reading-viewer-card, " +
                    ".reading-viewer-header, " +
                    ".reading-viewer-container, " +
                    ".reading-viewer-frame-wrapper"
                );


            /* =================================================
               DARK MODE
            ================================================== */

            if (
                currentTheme === "dark"
            ) {

                viewerElements.forEach(
                    function(element) {

                        element.classList.add(
                            "reading-dark-mode"
                        );

                    }
                );

            }


            /* =================================================
               LIGHT MODE
            ================================================== */
            else {

                viewerElements.forEach(
                    function(element) {

                        element.classList.remove(
                            "reading-dark-mode"
                        );

                    }
                );

            }

        }


        /* =====================================================
           INITIAL THEME
        ====================================================== */

        updateReadingViewerTheme();


        /* =====================================================
           WATCH FOR ETS THEME CHANGES
        ====================================================== */

        const themeObserver =
            new MutationObserver(
                function(mutations) {

                    mutations.forEach(
                        function(mutation) {

                            if (
                                mutation.type ===
                                "attributes" &&

                                mutation.attributeName ===
                                "data-theme"
                            ) {

                                updateReadingViewerTheme();

                            }

                        }
                    );

                }
            );


        themeObserver.observe(
            htmlElement, {
                attributes: true
            }
        );


    })();
</script>