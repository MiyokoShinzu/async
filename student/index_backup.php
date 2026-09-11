
<?php
/* =========================================================
   STUDENT DASHBOARD
   ETS-Async Learning Portal
   ========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION CHECK
========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user"]) ||
    !isset($_SESSION["user"]["access"]) ||
    $_SESSION["user"]["access"] !== "student"
) {
    header("Location: ../login.php");
    exit;
}


/* =========================================================
   DATABASE
========================================================= */

require_once "../src/connection.php";


/* =========================================================
   USER DATA
========================================================= */

$user = $_SESSION["user"];

$firstName =
    trim($user["first_name"] ?? "");

$studentId =
    trim($user["student_id"] ?? "");

$department =
    trim($user["department"] ?? "");

$yearSection =
    trim($user["year_section"] ?? "");


/* =========================================================
   PARSE YEAR AND SECTION
========================================================= */

$yearLevel = "";
$section = "";

$parts =
    preg_split(
        '/[-\s]+/',
        trim($yearSection),
        2
    );

if (!empty($parts[0])) {

    $yearLevel =
        trim($parts[0]);
}

if (!empty($parts[1])) {

    $section =
        trim($parts[1]);
}


/* =========================================================
   INITIALIZE STATISTICS
========================================================= */

$totalLectures = 0;
$completedLectures = 0;
$inProgressLectures = 0;
$notStartedLectures = 0;

$totalReadingActivities = 0;
$completedReadingActivities = 0;
$inProgressReadingActivities = 0;
$notStartedReadingActivities = 0;


/* =========================================================
   LECTURE PROGRESS
========================================================= */

$lectureProgressQuery = "

    SELECT
        l.id,
        lp.started_at,
        lp.watched_seconds,
        lp.video_duration,
        lp.status AS progress_status

    FROM lectures l

    LEFT JOIN lecture_progress lp
        ON lp.lecture_id = l.id
        AND lp.student_id = ?

    WHERE

        (
            l.department = ?
            OR l.department = ''
            OR l.department IS NULL
        )

        AND
        (
            CAST(l.year_level AS CHAR) = ?
            OR l.year_level = ''
            OR l.year_level IS NULL
        )

        AND
        (
            l.section = ?
            OR l.section = ''
            OR l.section IS NULL
        )

        AND
        (
            l.status = 'active'
            OR l.status IS NULL
        )

        AND
        (
            l.start_date IS NULL
            OR l.start_date <= NOW()
        )

        AND
        (
            l.due_date IS NULL
            OR l.due_date >= NOW()
        )

";


$stmt =
    $mysqli->prepare(
        $lectureProgressQuery
    );

if ($stmt) {

    $stmt->bind_param(
        "ssss",
        $studentId,
        $department,
        $yearLevel,
        $section
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    while (
        $lecture =
        $result->fetch_assoc()
    ) {

        $totalLectures++;


        $status =
            $lecture["progress_status"] ?? "";

        $watchedSeconds =
            (float)(
                $lecture["watched_seconds"] ?? 0
            );

        $videoDuration =
            (float)(
                $lecture["video_duration"] ?? 0
            );


        if ($status === "completed") {

            $completedLectures++;
        } elseif (
            !empty($lecture["started_at"]) ||
            $watchedSeconds > 0
        ) {

            $inProgressLectures++;
        } else {

            $notStartedLectures++;
        }
    }

    $stmt->close();
}


/* =========================================================
   READING ACTIVITY PROGRESS
========================================================= */

$readingProgressQuery = "

    SELECT
        r.id,
        rp.started_at,
        rp.reading_seconds,
        rp.status AS progress_status

    FROM reading_activity_table r

    LEFT JOIN reading_activity_progress rp
        ON rp.reading_activity_id = r.id
        AND rp.student_id = ?

    WHERE

        (
            r.department = ?
            OR r.department = ''
            OR r.department IS NULL
        )

        AND
        (
            CAST(r.year_level AS CHAR) = ?
            OR r.year_level = ''
            OR r.year_level IS NULL
        )

        AND
        (
            r.section = ?
            OR r.section = ''
            OR r.section IS NULL
        )

        AND
        (
            r.status = 'active'
            OR r.status IS NULL
        )

        AND
        (
            r.start_date IS NULL
            OR r.start_date <= NOW()
        )

        AND
        (
            r.due_date IS NULL
            OR r.due_date >= NOW()
        )

";


$stmt =
    $mysqli->prepare(
        $readingProgressQuery
    );

if ($stmt) {

    $stmt->bind_param(
        "ssss",
        $studentId,
        $department,
        $yearLevel,
        $section
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    while (
        $reading =
        $result->fetch_assoc()
    ) {

        $totalReadingActivities++;


        $status =
            $reading["progress_status"] ?? "";

        $readingSeconds =
            (float)(
                $reading["reading_seconds"] ?? 0
            );


        if ($status === "completed") {

            $completedReadingActivities++;
        } elseif (
            !empty($reading["started_at"]) ||
            $readingSeconds > 0
        ) {

            $inProgressReadingActivities++;
        } else {

            $notStartedReadingActivities++;
        }
    }

    $stmt->close();
}


/* =========================================================
   OVERALL STATISTICS
========================================================= */

$totalActivities =
    $totalLectures +
    $totalReadingActivities;

$completedActivities =
    $completedLectures +
    $completedReadingActivities;

$inProgressActivities =
    $inProgressLectures +
    $inProgressReadingActivities;

$notStartedActivities =
    $notStartedLectures +
    $notStartedReadingActivities;


if ($totalActivities > 0) {

    $overallProgress =
        ($completedActivities / $totalActivities) * 100;
} else {

    $overallProgress = 0;
}

$overallProgress =
    round(
        min(100, $overallProgress),
        1
    );


/* =========================================================
   LECTURE PROGRESS PERCENTAGE
========================================================= */

$lectureCompletion =
    $totalLectures > 0
    ? round(
        ($completedLectures / $totalLectures) * 100,
        1
    )
    : 0;


/* =========================================================
   READING PROGRESS PERCENTAGE
========================================================= */

$readingCompletion =
    $totalReadingActivities > 0
    ? round(
        ($completedReadingActivities / $totalReadingActivities) * 100,
        1
    )
    : 0;


/* =========================================================
   RECENT / NEW ACTIVITIES
========================================================= */

$recentActivities = [];


/* =========================================================
   RECENT LECTURES
========================================================= */

$recentLectureQuery = "

    SELECT
        l.id,
        l.title,
        l.description,
        l.created_at,
        lp.status AS progress_status

    FROM lectures l

    LEFT JOIN lecture_progress lp
        ON lp.lecture_id = l.id
        AND lp.student_id = ?

    WHERE

        (
            l.department = ?
            OR l.department = ''
            OR l.department IS NULL
        )

        AND
        (
            CAST(l.year_level AS CHAR) = ?
            OR l.year_level = ''
            OR l.year_level IS NULL
        )

        AND
        (
            l.section = ?
            OR l.section = ''
            OR l.section IS NULL
        )

        AND
        (
            l.status = 'active'
            OR l.status IS NULL
        )

        AND
        (
            l.start_date IS NULL
            OR l.start_date <= NOW()
        )

    ORDER BY
        l.created_at DESC,
        l.id DESC

    LIMIT 5

";


$stmt =
    $mysqli->prepare(
        $recentLectureQuery
    );

if ($stmt) {

    $stmt->bind_param(
        "ssss",
        $studentId,
        $department,
        $yearLevel,
        $section
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    while (
        $row =
        $result->fetch_assoc()
    ) {

        $recentActivities[] = [

            "id" =>
            (int)$row["id"],

            "title" =>
            $row["title"],

            "description" =>
            $row["description"],

            "created_at" =>
            $row["created_at"],

            "type" =>
            "lecture",

            "type_label" =>
            "Lecture",

            "icon" =>
            "bi-play-circle-fill",

            "progress_status" =>
            $row["progress_status"] ?? ""

        ];
    }

    $stmt->close();
}


/* =========================================================
   RECENT READING ACTIVITIES
========================================================= */

$recentReadingQuery = "

    SELECT
        r.id,
        r.title,
        r.description,
        r.created_at,
        rp.status AS progress_status

    FROM reading_activity_table r

    LEFT JOIN reading_activity_progress rp
        ON rp.reading_activity_id = r.id
        AND rp.student_id = ?

    WHERE

        (
            r.department = ?
            OR r.department = ''
            OR r.department IS NULL
        )

        AND
        (
            CAST(r.year_level AS CHAR) = ?
            OR r.year_level = ''
            OR r.year_level IS NULL
        )

        AND
        (
            r.section = ?
            OR r.section = ''
            OR r.section IS NULL
        )

        AND
        (
            r.status = 'active'
            OR r.status IS NULL
        )

        AND
        (
            r.start_date IS NULL
            OR r.start_date <= NOW()
        )

    ORDER BY
        r.created_at DESC,
        r.id DESC

    LIMIT 5

";


$stmt =
    $mysqli->prepare(
        $recentReadingQuery
    );

if ($stmt) {

    $stmt->bind_param(
        "ssss",
        $studentId,
        $department,
        $yearLevel,
        $section
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    while (
        $row =
        $result->fetch_assoc()
    ) {

        $recentActivities[] = [

            "id" =>
            (int)$row["id"],

            "title" =>
            $row["title"],

            "description" =>
            $row["description"],

            "created_at" =>
            $row["created_at"],

            "type" =>
            "reading",

            "type_label" =>
            "Reading Activity",

            "icon" =>
            "bi-book-fill",

            "progress_status" =>
            $row["progress_status"] ?? ""

        ];
    }

    $stmt->close();
}


/* =========================================================
   SORT RECENT ACTIVITIES
========================================================= */

usort(
    $recentActivities,
    function ($a, $b) {

        return
            strtotime($b["created_at"])
            <=>
            strtotime($a["created_at"]);
    }
);


$recentActivities =
    array_slice(
        $recentActivities,
        0,
        6
    );


/* =========================================================
   DATE FORMATTER
========================================================= */

function formatActivityDate($date)
{
    if (empty($date)) {

        return "";
    }

    $timestamp =
        strtotime($date);

    if (!$timestamp) {

        return "";
    }

    $difference =
        time() - $timestamp;


    if ($difference < 60) {

        return "Just now";
    }


    if ($difference < 3600) {

        return
            floor($difference / 60)
            . " min ago";
    }


    if ($difference < 86400) {

        return
            floor($difference / 3600)
            . " hr ago";
    }


    if ($difference < 604800) {

        $days =
            floor(
                $difference / 86400
            );

        return
            $days .
            " day" .
            ($days == 1 ? "" : "s") .
            " ago";
    }


    return date(
        "M d, Y",
        $timestamp
    );
}


/* =========================================================
   NEW ACTIVITY CHECK
========================================================= */

function isNewActivity($date)
{
    if (empty($date)) {

        return false;
    }

    $timestamp =
        strtotime($date);

    if (!$timestamp) {

        return false;
    }

    return (time() - $timestamp)
        <= 604800;
}


/* =========================================================
   HTML ESCAPE HELPER
========================================================= */

function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}


/* =========================================================
   GLOBAL HEAD
========================================================= */

include "globals/head.php";

?>

<body>


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <?php include "globals/sidebar.php"; ?>


    <!-- =====================================================
         SIDEBAR OVERLAY
    ====================================================== -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay">
    </div>


    <!-- =====================================================
         TOPBAR
    ====================================================== -->

    <?php include "globals/topbar.php"; ?>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">

        <div class="content-wrapper">


            <!-- =================================================
                 DASHBOARD HEADER
            ================================================== -->

            <div class="dashboard-heading">

                <div>

                    <h2 class="dashboard-heading-title">

                        Welcome,
                        <?= e($username ?: "Student") ?>

                    </h2>

                    <p class="dashboard-heading-subtitle">

                        Monitor your learning activities,
                        progress, and newly assigned materials.

                    </p>

                </div>

            </div>


            <!-- =================================================
                 SUMMARY STATISTICS
            ================================================== -->

            <div class="row g-3 mb-4">


                <!-- TOTAL -->

                <div class="col-xl-3 col-md-6">

                    <div class="student-stat-card">

                        <div class="student-stat-icon blue">

                            <i class="bi bi-journal-text"></i>

                        </div>

                        <div>

                            <span>
                                Total Activities
                            </span>

                            <strong>
                                <?= $totalActivities ?>
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- COMPLETED -->

                <div class="col-xl-3 col-md-6">

                    <div class="student-stat-card">

                        <div class="student-stat-icon green">

                            <i class="bi bi-check-circle-fill"></i>

                        </div>

                        <div>

                            <span>
                                Completed
                            </span>

                            <strong>
                                <?= $completedActivities ?>
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- IN PROGRESS -->

                <div class="col-xl-3 col-md-6">

                    <div class="student-stat-card">

                        <div class="student-stat-icon orange">

                            <i class="bi bi-arrow-repeat"></i>

                        </div>

                        <div>

                            <span>
                                In Progress
                            </span>

                            <strong>
                                <?= $inProgressActivities ?>
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- NOT STARTED -->

                <div class="col-xl-3 col-md-6">

                    <div class="student-stat-card">

                        <div class="student-stat-icon gray">

                            <i class="bi bi-hourglass-split"></i>

                        </div>

                        <div>

                            <span>
                                Not Started
                            </span>

                            <strong>
                                <?= $notStartedActivities ?>
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 OVERALL PROGRESS
            ================================================== -->

            <div class="learning-progress-card mb-4">


                <div class="learning-progress-header">

                    <div>

                        <h5>

                            <i class="bi bi-bar-chart-fill me-2"></i>

                            Overall Learning Progress

                        </h5>

                        <p>

                            Your completion progress across
                            lectures and reading activities.

                        </p>

                    </div>


                    <strong>

                        <?= number_format(
                            $overallProgress,
                            1
                        ) ?>%

                    </strong>

                </div>


                <div class="learning-progress">

                    <div
                        class="progress-bar"
                        role="progressbar"
                        style="width: <?= $overallProgress ?>%;"
                        aria-valuenow="<?= $overallProgress ?>"
                        aria-valuemin="0"
                        aria-valuemax="100">
                    </div>

                </div>


                <div class="learning-progress-footer">

                    <span>

                        <i class="bi bi-check-circle-fill me-1"></i>

                        <?= $completedActivities ?>
                        of
                        <?= $totalActivities ?>
                        activities completed

                    </span>


                    <span>

                        <i class="bi bi-arrow-up-right-circle me-1"></i>

                        Keep progressing

                    </span>

                </div>

            </div>


            <!-- =================================================
                 NEW LEARNING ACTIVITIES
            ================================================== -->

            <div class="activity-card mb-4">


                <div class="activity-card-header">

                    <div>

                        <h5>

                            <i class="bi bi-bell-fill me-2"></i>

                            New Learning Activities

                        </h5>

                        <p>

                            Recently created lectures and
                            reading activities assigned to you.

                        </p>

                    </div>


                    <div class="activity-count">

                        <?= count($recentActivities) ?>

                        <span class="ms-1">
                            Recent
                        </span>

                    </div>

                </div>


                <?php if (!empty($recentActivities)): ?>

                    <div class="activity-list">


                        <?php foreach (
                            $recentActivities
                            as $index => $activity
                        ): ?>


                            <?php

                            $activityId =
                                (int)$activity["id"];

                            $activityType =
                                $activity["type"];

                            $activityTitle =
                                $activity["title"];

                            $activityDescription =
                                trim(
                                    $activity["description"] ?? ""
                                );

                            $activityDate =
                                $activity["created_at"];

                            $activityStatus =
                                $activity["progress_status"] ?? "";


                            if (
                                $activityStatus ===
                                "completed"
                            ) {

                                $statusClass =
                                    "completed";

                                $statusText =
                                    "Completed";

                                $statusIcon =
                                    "bi-check-circle-fill";
                            } elseif (
                                $activityStatus ===
                                "in_progress"
                            ) {

                                $statusClass =
                                    "in-progress";

                                $statusText =
                                    "In Progress";

                                $statusIcon =
                                    "bi-arrow-repeat";
                            } else {

                                $statusClass =
                                    "not-started";

                                $statusText =
                                    "Not Started";

                                $statusIcon =
                                    "bi-circle";
                            }


                            $activityLink =
                                $activityType === "lecture"
                                ? "activity_view.php?id=" .
                                $activityId
                                : "reading_activity_view.php?id=" .
                                $activityId;

                            ?>


                            <div class="activity-item">


                                <!-- NUMBER -->

                                <div class="activity-number">

                                    <?= $index + 1 ?>

                                </div>


                                <!-- ICON -->

                                <div
                                    class="activity-icon <?= e($statusClass) ?>">

                                    <i
                                        class="bi <?= e($activity["icon"]) ?>">
                                    </i>

                                </div>


                                <!-- INFORMATION -->

                                <div class="activity-information">

                                    <div class="activity-title-row">

                                        <h5>

                                            <?= e(
                                                $activityTitle
                                            ) ?>

                                        </h5>


                                        <?php if (
                                            isNewActivity(
                                                $activityDate
                                            )
                                        ): ?>

                                            <span class="activity-new">

                                                <i class="bi bi-stars"></i>

                                                NEW

                                            </span>

                                        <?php endif; ?>

                                    </div>


                                    <div class="activity-type">

                                        <i
                                            class="bi
                                            <?= $activityType === "lecture"
                                                ? "bi-play-circle"
                                                : "bi-book"
                                            ?>">
                                        </i>

                                        <?= e(
                                            $activity["type_label"]
                                        ) ?>

                                    </div>


                                    <?php if (
                                        $activityDescription !== ""
                                    ): ?>

                                        <div class="activity-description">

                                            <?= e(
                                                $activityDescription
                                            ) ?>

                                        </div>

                                    <?php endif; ?>


                                    <div class="activity-meta">

                                        <span>

                                            <i class="bi bi-clock"></i>

                                            <?= e(
                                                formatActivityDate(
                                                    $activityDate
                                                )
                                            ) ?>

                                        </span>

                                    </div>

                                </div>


                                <!-- STATUS -->

                                <div class="activity-status">

                                    <span
                                        class="activity-status-badge
                                        <?= e($statusClass) ?>">

                                        <i
                                            class="bi
                                            <?= e($statusIcon) ?>">
                                        </i>

                                        <?= e($statusText) ?>

                                    </span>

                                </div>


                                <!-- ACTION -->

                                <div class="activity-action">

                                    <a
                                        href="<?= e($activityLink) ?>"
                                        class="btn btn-primary">

                                        <i class="bi bi-arrow-right me-1"></i>

                                        Open

                                    </a>

                                </div>


                            </div>


                        <?php endforeach; ?>


                    </div>

                <?php else: ?>


                    <div class="activity-empty">

                        <div class="activity-empty-icon">

                            <i class="bi bi-bell-slash"></i>

                        </div>

                        <h5>
                            No New Activities
                        </h5>

                        <p>
                            There are currently no learning
                            activities assigned to you.
                        </p>

                    </div>


                <?php endif; ?>


            </div>


            <!-- =================================================
                 ACTIVITY PROGRESS
            ================================================== -->

            <div class="row g-3">


                <!-- =================================================
                     LECTURE PROGRESS
                ================================================== -->

                <div class="col-lg-6">

                    <div class="category-progress-card">


                        <div class="category-progress-header">

                            <div class="category-progress-title">

                                <div class="category-progress-icon lecture">

                                    <i class="bi bi-play-circle-fill"></i>

                                </div>

                                <div>

                                    <h5>
                                        Lecture Activities
                                    </h5>

                                    <p>
                                        Video-based learning
                                    </p>

                                </div>

                            </div>


                            <strong>

                                <?= number_format(
                                    $lectureCompletion,
                                    1
                                ) ?>%

                            </strong>

                        </div>


                        <div class="category-progress-bar">

                            <div
                                class="category-progress-fill lecture"
                                style="width: <?= $lectureCompletion ?>%;">
                            </div>

                        </div>


                        <div class="category-progress-footer">

                            <span>

                                <i class="bi bi-check-circle-fill"></i>

                                <?= $completedLectures ?>
                                completed

                            </span>


                            <span>

                                <?= $totalLectures ?>
                                total

                            </span>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     READING PROGRESS
                ================================================== -->

                <div class="col-lg-6">

                    <div class="category-progress-card">


                        <div class="category-progress-header">

                            <div class="category-progress-title">

                                <div class="category-progress-icon reading">

                                    <i class="bi bi-book-fill"></i>

                                </div>

                                <div>

                                    <h5>
                                        Reading Activities
                                    </h5>

                                    <p>
                                        Required learning materials
                                    </p>

                                </div>

                            </div>


                            <strong>

                                <?= number_format(
                                    $readingCompletion,
                                    1
                                ) ?>%

                            </strong>

                        </div>


                        <div class="category-progress-bar">

                            <div
                                class="category-progress-fill reading"
                                style="width: <?= $readingCompletion ?>%;">
                            </div>

                        </div>


                        <div class="category-progress-footer">

                            <span>

                                <i class="bi bi-check-circle-fill"></i>

                                <?= $completedReadingActivities ?>
                                completed

                            </span>


                            <span>

                                <?= $totalReadingActivities ?>
                                total

                            </span>

                        </div>

                    </div>

                </div>

            </div>


        </div>

    </main>


    <!-- =====================================================
         GLOBAL SCRIPTS
    ====================================================== -->

    <?php require_once "./globals/scripts.php"; ?>


    <!-- =====================================================
         DASHBOARD STYLE
    ====================================================== -->

    <style>
        /* =====================================================
           DASHBOARD HEADER
        ====================================================== */

        .dashboard-heading {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 24px;

        }


        .dashboard-heading-title {

            margin: 0;

            font-size: 24px;

            font-weight: 700;

            color:
                var(--academic-blue-dark);

        }


        .dashboard-heading-subtitle {

            margin: 6px 0 0;

            color:
                var(--text-secondary);

            font-size: 14px;

        }


        /* =====================================================
           STATISTICS CARDS
        ====================================================== */

        .student-stat-card {

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                14px;

            padding:
                20px;

            display:
                flex;

            align-items:
                center;

            gap:
                15px;

            height:
                100%;

            box-shadow:
                0 4px 12px var(--shadow-color);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.25s ease,
                border-color 0.25s ease;

        }


        .student-stat-card:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 8px 20px var(--shadow-color);

        }


        .student-stat-icon {

            width:
                50px;

            height:
                50px;

            min-width:
                50px;

            border-radius:
                12px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                21px;

        }


        /* BLUE */

        .student-stat-icon.blue {

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);

        }


        /* GREEN */

        .student-stat-icon.green {

            background:
                var(--activity-icon-green-bg);

            color:
                var(--activity-icon-green);

        }


        /* ORANGE */

        .student-stat-icon.orange {

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-icon-orange);

        }


        /* GRAY */

        .student-stat-icon.gray {

            background:
                var(--activity-icon-gray-bg);

            color:
                var(--activity-icon-gray);

        }


        .student-stat-card span {

            display:
                block;

            color:
                var(--activity-muted);

            font-size:
                13px;

            margin-bottom:
                3px;

        }


        .student-stat-card strong {

            display:
                block;

            color:
                var(--text-color);

            font-size:
                24px;

            line-height:
                1.2;

        }


        /* =====================================================
           LEARNING PROGRESS
        ====================================================== */

        .learning-progress-card {

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                14px;

            padding:
                22px 24px;

            box-shadow:
                0 4px 12px var(--shadow-color);

        }


        .learning-progress-header {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                flex-start;

            margin-bottom:
                16px;

        }


        .learning-progress-header h5 {

            margin:
                0;

            font-weight:
                700;

            color:
                var(--text-color);

            font-size:
                16px;

        }


        .learning-progress-header h5 i {

            color:
                var(--activity-indigo);

        }


        .learning-progress-header p {

            margin:
                5px 0 0;

            color:
                var(--text-secondary);

            font-size:
                13px;

        }


        .learning-progress-header strong {

            font-size:
                24px;

            color:
                var(--activity-indigo);

        }


        .learning-progress {

            height:
                10px;

            border-radius:
                20px;

            background:
                var(--activity-progress-bg);

            overflow:
                hidden;

        }


        .learning-progress .progress-bar {

            height:
                100%;

            border-radius:
                20px;

            background:
                linear-gradient(90deg,
                    var(--activity-icon-blue),
                    var(--activity-indigo));

            transition:
                width 0.5s ease;

        }


        .learning-progress-footer {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

            margin-top:
                10px;

            color:
                var(--text-secondary);

            font-size:
                13px;

        }


        .learning-progress-footer i {

            color:
                var(--activity-completed);

        }


        /* =====================================================
           ACTIVITY CARD
        ====================================================== */

        .activity-card {

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                14px;

            overflow:
                hidden;

            box-shadow:
                0 4px 12px var(--shadow-color);

            transition:
                background-color 0.25s ease,
                border-color 0.25s ease,
                box-shadow 0.25s ease;

        }


        .activity-card-header {

            padding:
                22px 24px;

            border-bottom:
                1px solid var(--activity-border);

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

        }


        .activity-card-header h5 {

            margin:
                0;

            font-weight:
                700;

            color:
                var(--text-color);

        }


        .activity-card-header h5 i {

            color:
                var(--activity-indigo);

        }


        .activity-card-header p {

            margin:
                5px 0 0;

            color:
                var(--text-secondary);

            font-size:
                13px;

        }


        .activity-count {

            background:
                var(--activity-indigo-bg);

            color:
                var(--activity-indigo);

            padding:
                6px 12px;

            border-radius:
                20px;

            font-size:
                12px;

            font-weight:
                600;

        }


        /* =====================================================
           ACTIVITY LIST
        ====================================================== */

        .activity-list {

            display:
                flex;

            flex-direction:
                column;

        }


        .activity-item {

            display:
                grid;

            grid-template-columns:
                36px 48px minmax(250px, 1fr) 130px 100px;

            align-items:
                center;

            gap:
                15px;

            padding:
                20px 24px;

            border-bottom:
                1px solid var(--activity-border-light);

            transition:
                background-color 0.2s ease;

        }


        .activity-item:last-child {

            border-bottom:
                none;

        }


        .activity-item:hover {

            background:
                var(--activity-hover-bg);

        }


        /* =====================================================
           ACTIVITY NUMBER
        ====================================================== */

        .activity-number {

            width:
                32px;

            height:
                32px;

            border-radius:
                50%;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                var(--activity-number-bg);

            color:
                var(--activity-number-text);

            font-size:
                13px;

            font-weight:
                700;

        }


        /* =====================================================
           ACTIVITY ICON
        ====================================================== */

        .activity-icon {

            width:
                44px;

            height:
                44px;

            border-radius:
                12px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                19px;

        }


        .activity-icon.completed {

            background:
                var(--activity-icon-green-bg);

            color:
                var(--activity-icon-green);

        }


        .activity-icon.in-progress {

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-icon-orange);

        }


        .activity-icon.not-started {

            background:
                var(--activity-icon-gray-bg);

            color:
                var(--activity-icon-gray);

        }


        /* =====================================================
           ACTIVITY INFORMATION
        ====================================================== */

        .activity-information {

            min-width:
                0;

        }


        .activity-title-row {

            display:
                flex;

            align-items:
                center;

            gap:
                8px;

            flex-wrap:
                wrap;

        }


        .activity-information h5 {

            margin:
                0;

            color:
                var(--text-color);

            font-size:
                15px;

            font-weight:
                700;

            overflow:
                hidden;

            text-overflow:
                ellipsis;

            white-space:
                nowrap;

        }


        .activity-type {

            margin-top:
                5px;

            color:
                var(--activity-indigo);

            font-size:
                11px;

            font-weight:
                600;

        }


        .activity-type i {

            margin-right:
                4px;

        }


        .activity-description {

            margin-top:
                6px;

            color:
                var(--text-secondary);

            font-size:
                12px;

            line-height:
                1.5;

            display:
                -webkit-box;

            -webkit-line-clamp:
                2;

            -webkit-box-orient:
                vertical;

            overflow:
                hidden;

        }


        /* =====================================================
           ACTIVITY META
        ====================================================== */

        .activity-meta {

            display:
                flex;

            flex-wrap:
                wrap;

            gap:
                15px;

            margin-top:
                8px;

        }


        .activity-meta span {

            color:
                var(--text-secondary);

            font-size:
                11px;

        }


        .activity-meta i {

            margin-right:
                4px;

            color:
                var(--activity-muted);

        }


        /* =====================================================
           NEW BADGE
        ====================================================== */

        .activity-new {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                4px;

            padding:
                4px 7px;

            border-radius:
                6px;

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);

            font-size:
                9px;

            font-weight:
                700;

            letter-spacing:
                0.3px;

        }


        /* =====================================================
           STATUS
        ====================================================== */

        .activity-status {

            display:
                flex;

            flex-direction:
                column;

            align-items:
                flex-start;

        }


        .activity-status-badge {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                5px;

            padding:
                6px 10px;

            border-radius:
                20px;

            font-size:
                11px;

            font-weight:
                600;

            white-space:
                nowrap;

        }


        .activity-status-badge.completed {

            background:
                var(--activity-icon-green-bg);

            color:
                var(--activity-completed);

        }


        .activity-status-badge.in-progress {

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-inprogress);

        }


        .activity-status-badge.not-started {

            background:
                var(--activity-icon-gray-bg);

            color:
                var(--activity-notstarted);

        }


        /* =====================================================
           ACTION BUTTON
        ====================================================== */

        .activity-action {

            display:
                flex;

            justify-content:
                flex-end;

        }


        .activity-action .btn-primary {

            background:
                var(--activity-icon-blue);

            border-color:
                var(--activity-icon-blue);

            border-radius:
                8px;

            padding:
                8px 13px;

            font-size:
                12px;

            font-weight:
                600;

            white-space:
                nowrap;

        }


        .activity-action .btn-primary:hover {

            filter:
                brightness(0.92);

        }


        /* =====================================================
           CATEGORY PROGRESS
        ====================================================== */

        .category-progress-card {

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                14px;

            padding:
                20px;

            box-shadow:
                0 4px 12px var(--shadow-color);

            height:
                100%;

            transition:
                background-color 0.25s ease,
                border-color 0.25s ease,
                box-shadow 0.25s ease;

        }


        .category-progress-header {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

            gap:
                15px;

            margin-bottom:
                15px;

        }


        .category-progress-title {

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

        }


        .category-progress-icon {

            width:
                42px;

            height:
                42px;

            min-width:
                42px;

            border-radius:
                10px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                18px;

        }


        .category-progress-icon.lecture {

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);

        }


        .category-progress-icon.reading {

            background:
                var(--activity-indigo-bg);

            color:
                var(--activity-indigo);

        }


        .category-progress-title h5 {

            margin:
                0;

            color:
                var(--text-color);

            font-size:
                14px;

            font-weight:
                700;

        }


        .category-progress-title p {

            margin:
                3px 0 0;

            color:
                var(--text-secondary);

            font-size:
                11px;

        }


        .category-progress-header>strong {

            font-size:
                18px;

            color:
                var(--text-color);

        }


        .category-progress-bar {

            height:
                8px;

            background:
                var(--activity-progress-bg);

            border-radius:
                20px;

            overflow:
                hidden;

        }


        .category-progress-fill {

            height:
                100%;

            border-radius:
                20px;

            transition:
                width 0.5s ease;

        }


        .category-progress-fill.lecture {

            background:
                var(--activity-icon-blue);

        }


        .category-progress-fill.reading {

            background:
                var(--activity-indigo);

        }


        .category-progress-footer {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

            margin-top:
                10px;

            color:
                var(--text-secondary);

            font-size:
                11px;

        }


        .category-progress-footer i {

            color:
                var(--activity-completed);

            margin-right:
                3px;

        }


        /* =====================================================
           EMPTY STATE
        ====================================================== */

        .activity-empty {

            padding:
                60px 20px;

            text-align:
                center;

        }


        .activity-empty-icon {

            width:
                65px;

            height:
                65px;

            margin:
                0 auto 15px;

            border-radius:
                50%;

            background:
                var(--activity-icon-gray-bg);

            color:
                var(--activity-muted);

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                25px;

        }


        .activity-empty h5 {

            margin-bottom:
                7px;

            color:
                var(--text-color);

            font-weight:
                700;

        }


        .activity-empty p {

            margin:
                0;

            color:
                var(--text-secondary);

            font-size:
                13px;

        }


        /* =====================================================
           DARK THEME BOOTSTRAP OVERRIDES
        ====================================================== */

        [data-theme="dark"] .text-dark {

            color:
                var(--text-color) !important;

        }


        [data-theme="dark"] .text-muted {

            color:
                var(--text-secondary) !important;

        }


        [data-theme="dark"] .bg-white {

            background-color:
                var(--surface-color) !important;

        }


        [data-theme="dark"] .bg-light {

            background-color:
                var(--surface-secondary) !important;

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1100px) {

            .activity-item {

                grid-template-columns:
                    36px 48px minmax(200px, 1fr) 120px 90px;

            }

        }


        @media (max-width: 900px) {

            .activity-item {

                grid-template-columns:
                    36px 48px 1fr auto;

            }


            .activity-status {

                display:
                    none;

            }

        }


        @media (max-width: 768px) {

            .dashboard-heading-title {

                font-size:
                    21px;

            }


            .learning-progress-card {

                padding:
                    18px;

            }


            .learning-progress-header {

                align-items:
                    center;

            }


            .learning-progress-header strong {

                font-size:
                    21px;

            }


            .activity-card-header {

                padding:
                    18px;

            }


            .activity-card-header p {

                display:
                    none;

            }


            .activity-item {

                grid-template-columns:
                    30px 42px 1fr;

                gap:
                    10px;

                padding:
                    17px;

            }


            .activity-number {

                width:
                    28px;

                height:
                    28px;

                font-size:
                    11px;

            }


            .activity-icon {

                width:
                    40px;

                height:
                    40px;

                font-size:
                    17px;

            }


            .activity-information h5 {

                white-space:
                    normal;

                font-size:
                    14px;

            }


            .activity-action {

                grid-column:
                    3;

                justify-content:
                    flex-start;

                margin-top:
                    5px;

            }


            .activity-action .btn {

                width:
                    100%;

            }


            .category-progress-card {

                padding:
                    18px;

            }

        }


        @media (max-width: 576px) {

            .dashboard-heading {

                margin-bottom:
                    18px;

            }


            .dashboard-heading-title {

                font-size:
                    20px;

            }


            .dashboard-heading-subtitle {

                font-size:
                    13px;

            }


            .student-stat-card {

                padding:
                    16px;

            }


            .student-stat-icon {

                width:
                    44px;

                height:
                    44px;

                min-width:
                    44px;

                font-size:
                    18px;

            }


            .student-stat-card strong {

                font-size:
                    21px;

            }


            .learning-progress-footer {

                flex-direction:
                    column;

                align-items:
                    flex-start;

                gap:
                    5px;

            }


            .activity-count {

                display:
                    none;

            }

        }


        @media (max-width: 400px) {

            .student-stat-card {

                padding:
                    14px;

            }


            .student-stat-icon {

                width:
                    40px;

                height:
                    40px;

                min-width:
                    40px;

            }


            .student-stat-card strong {

                font-size:
                    19px;

            }

        }


        /* =====================================================
           REDUCE MOTION
        ====================================================== */

        @media (prefers-reduced-motion: reduce) {

            * {

                transition:
                    none !important;

            }

        }
    </style>

</body>
