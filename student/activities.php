<?php

/* =========================================================
   STUDENT ACTIVITIES
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
$user = $_SESSION["user"];


/* =========================================================
   USER DATA
   ========================================================= */

$firstName      = $user["first_name"] ?? "";
$lastName       = $user["last_name"] ?? "";
$middleInitial  = $user["middle_initial"] ?? "";
$extensionName  = $user["extension_name"] ?? "";
$studentId      = $user["student_id"] ?? "";
$department     = $user["department"] ?? "";
$yearSection    = $user["year_section"] ?? "";
$email          = $user["email"] ?? "";
$username       = $user["username"] ?? "";
$access         = $user["access"] ?? "student";


/* =========================================================
   FULL NAME
   ========================================================= */

$fullName = trim(
    $firstName . " " .
        ($middleInitial !== ""
            ? $middleInitial . ". "
            : "") .
        $lastName .
        ($extensionName !== ""
            ? " " . $extensionName
            : "")
);


/* =========================================================
   INITIALS
   ========================================================= */

$initials = "";

if ($firstName !== "") {
    $initials .= strtoupper(substr($firstName, 0, 1));
}

if ($lastName !== "") {
    $initials .= strtoupper(substr($lastName, 0, 1));
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

$department =
    $user["department"] ?? "";

$yearSection =
    $user["year_section"] ?? "";


/* =========================================================
   EXTRACT YEAR AND SECTION
   ========================================================= */

$yearLevel = "";

$section = "";


$parts =
    preg_split(
        '/[-\s]+/',
        $yearSection,
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
   GET ACTIVITIES
   ========================================================= */

$sql = "
    SELECT

        l.id,

        l.title,

        l.description,

        l.youtube_url,

        l.department,

        l.year_level,

        l.section,

        l.start_date,

        l.due_date,

        lp.started_at,

        lp.last_accessed_at,

        lp.access_count,

        lp.completed_at,

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

    ORDER BY

        l.start_date DESC,

        l.id DESC
";


$stmt =
    $mysqli->prepare($sql);


if (!$stmt) {

    die("Database error: " .
        htmlspecialchars(
            $mysqli->error
        ));
}


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


/* =========================================================
   STORE ACTIVITIES
   ========================================================= */

$activityRecords = [];


while (
    $activity =
    $result->fetch_assoc()
) {

    $activityRecords[] =
        $activity;
}


$stmt->close();


/* =========================================================
   STATISTICS
   ========================================================= */

$totalActivities =
    count($activityRecords);


$completedActivities =
    0;


$inProgressActivities =
    0;


$notStartedActivities =
    0;


/* =========================================================
   CALCULATE PROGRESS
   ========================================================= */

foreach (
    $activityRecords
    as $activity
) {

    $status =
        $activity["progress_status"] ?? "";


    if (
        $status === "completed"
    ) {

        $completedActivities++;
    } elseif (
        !empty($activity["started_at"])
    ) {

        $inProgressActivities++;
    } else {

        $notStartedActivities++;
    }
}


/* =========================================================
   OVERALL COMPLETION
   ========================================================= */

$completionPercentage =

    $totalActivities > 0

    ? round(
        (
            $completedActivities /
            $totalActivities
        ) * 100
    )

    : 0;


?>


<!DOCTYPE html>

<html lang="en">


<?php include 'globals/head.php'; ?>

<style>
    /* =========================================================
   STUDENT ACTIVITIES PAGE
   ETS-Async Learning Portal
   ========================================================= */


    /* =========================================================
   PAGE HEADER
   ========================================================= */

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-header h2 {
        margin: 0;
        font-weight: 700;
        color: #1f2937;
    }

    .page-header p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 14px;
    }


    /* =========================================================
   STATISTICS CARDS
   ========================================================= */

    .student-stat-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        height: 100%;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        transition: all 0.2s ease;
    }

    .student-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
    }

    .student-stat-icon {
        width: 50px;
        height: 50px;
        min-width: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
    }

    .student-stat-icon.blue {
        background: #eaf2ff;
        color: #2563eb;
    }

    .student-stat-icon.gray {
        background: #f3f4f6;
        color: #6b7280;
    }

    .student-stat-icon.orange {
        background: #fff4e5;
        color: #f59e0b;
    }

    .student-stat-icon.green {
        background: #eafaf1;
        color: #16a34a;
    }

    .student-stat-card span {
        display: block;
        color: #6b7280;
        font-size: 13px;
        margin-bottom: 3px;
    }

    .student-stat-card strong {
        display: block;
        color: #111827;
        font-size: 24px;
        line-height: 1.2;
    }

    .student-stat-card strong small {
        font-size: 13px;
        color: #16a34a;
        font-weight: 600;
    }


    /* =========================================================
   OVERALL LEARNING PROGRESS
   ========================================================= */

    .learning-progress-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 22px 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .learning-progress-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .learning-progress-header h5 {
        margin: 0;
        font-weight: 700;
        color: #1f2937;
    }

    .learning-progress-header p {
        margin: 5px 0 0;
        color: #6b7280;
        font-size: 13px;
    }

    .learning-progress-header strong {
        font-size: 24px;
        color: #2563eb;
    }

    .learning-progress {
        height: 10px;
        border-radius: 20px;
        background: #e5e7eb;
        overflow: hidden;
    }

    .learning-progress .progress-bar {
        border-radius: 20px;
        background: linear-gradient(90deg,
                #2563eb,
                #4f46e5);
        transition: width 0.5s ease;
    }

    .learning-progress-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        color: #6b7280;
        font-size: 13px;
    }


    /* =========================================================
   ACTIVITY CARD
   ========================================================= */

    .activity-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .activity-card-header {
        padding: 22px 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .activity-card-header h5 {
        margin: 0;
        font-weight: 700;
        color: #1f2937;
    }

    .activity-card-header p {
        margin: 5px 0 0;
        color: #6b7280;
        font-size: 13px;
    }

    .activity-count {
        background: #eef2ff;
        color: #4f46e5;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }


    /* =========================================================
   ACTIVITY LIST
   ========================================================= */

    .activity-list {
        display: flex;
        flex-direction: column;
    }

    .activity-item {
        display: grid;
        grid-template-columns:
            36px 48px minmax(250px, 1fr) 130px 130px;

        align-items: center;
        gap: 15px;

        padding: 20px 24px;

        border-bottom: 1px solid #f0f0f0;

        transition: background 0.2s ease;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background: #fafbff;
    }


    /* =========================================================
   ACTIVITY NUMBER
   ========================================================= */

    .activity-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #f3f4f6;
        color: #6b7280;

        font-size: 13px;
        font-weight: 700;
    }


    /* =========================================================
   ACTIVITY ICON
   ========================================================= */

    .activity-icon {
        width: 44px;
        height: 44px;

        border-radius: 12px;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 19px;
    }

    .activity-icon.completed {
        background: #eafaf1;
        color: #16a34a;
    }

    .activity-icon.in-progress {
        background: #fff4e5;
        color: #f59e0b;
    }

    .activity-icon.not-started {
        background: #f3f4f6;
        color: #6b7280;
    }


    /* =========================================================
   ACTIVITY INFORMATION
   ========================================================= */

    .activity-information {
        min-width: 0;
    }

    .activity-information h5 {
        margin: 0 0 7px;
        color: #1f2937;
        font-size: 15px;
        font-weight: 700;

        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }


    /* =========================================================
   ACTIVITY META
   ========================================================= */

    .activity-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .activity-meta span {
        color: #6b7280;
        font-size: 12px;
    }

    .activity-meta i {
        margin-right: 4px;
        color: #9ca3af;
    }


    /* =========================================================
   INDIVIDUAL PROGRESS
   ========================================================= */

    .lecture-progress-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 10px;
    }

    .lecture-progress-bar {
        width: 150px;
        max-width: 100%;
        height: 6px;

        background: #e5e7eb;

        border-radius: 20px;
        overflow: hidden;
    }

    .lecture-progress-fill {
        height: 100%;
        background: #f59e0b;
        border-radius: 20px;
        transition: width 0.4s ease;
    }

    .lecture-progress-fill.completed {
        background: #16a34a;
    }

    .lecture-progress-wrapper span {
        min-width: 35px;
        color: #6b7280;
        font-size: 12px;
        font-weight: 600;
    }


    /* =========================================================
   STATUS
   ========================================================= */

    .activity-status {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }

    .activity-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;

        padding: 6px 10px;

        border-radius: 20px;

        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .activity-status-badge.completed {
        background: #eafaf1;
        color: #16a34a;
    }

    .activity-status-badge.in-progress {
        background: #fff4e5;
        color: #d97706;
    }

    .activity-status-badge.not-started {
        background: #f3f4f6;
        color: #6b7280;
    }

    .activity-status small {
        color: #9ca3af;
        font-size: 11px;
    }


    /* =========================================================
   ACTION BUTTON
   ========================================================= */

    .activity-action {
        display: flex;
        justify-content: flex-end;
    }

    .activity-action .btn {
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
    }

    .activity-action .btn-primary {
        background: #2563eb;
        border-color: #2563eb;
    }

    .activity-action .btn-primary:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }


    /* =========================================================
   EMPTY STATE
   ========================================================= */

    .activity-empty {
        padding: 60px 20px;
        text-align: center;
    }

    .activity-empty-icon {
        width: 70px;
        height: 70px;

        margin: 0 auto 15px;

        border-radius: 50%;

        background: #f3f4f6;
        color: #9ca3af;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 28px;
    }

    .activity-empty h5 {
        margin-bottom: 7px;
        color: #374151;
        font-weight: 700;
    }

    .activity-empty p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
    }


    /* =========================================================
   RESPONSIVE
   ========================================================= */

    @media (max-width: 1100px) {

        .activity-item {
            grid-template-columns:
                36px 48px minmax(200px, 1fr) 120px 110px;
        }

        .lecture-progress-bar {
            width: 100px;
        }

    }


    @media (max-width: 900px) {

        .activity-item {
            grid-template-columns:
                36px 48px 1fr auto;
        }

        .activity-status {
            display: none;
        }

        .activity-action {
            justify-content: flex-end;
        }

    }


    @media (max-width: 650px) {

        .page-header {
            margin-bottom: 18px;
        }

        .page-header h2 {
            font-size: 22px;
        }

        .learning-progress-card {
            padding: 18px;
        }

        .learning-progress-header {
            align-items: center;
        }

        .learning-progress-header h5 {
            font-size: 16px;
        }

        .learning-progress-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }

        .activity-card-header {
            padding: 18px;
        }

        .activity-card-header p {
            display: none;
        }

        .activity-item {
            grid-template-columns:
                30px 42px 1fr;

            gap: 10px;

            padding: 17px;
        }

        .activity-number {
            width: 28px;
            height: 28px;
            font-size: 11px;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            font-size: 17px;
        }

        .activity-information h5 {
            font-size: 14px;
            white-space: normal;
        }

        .activity-meta {
            gap: 8px;
        }

        .lecture-progress-wrapper {
            margin-top: 8px;
        }

        .lecture-progress-bar {
            width: 100%;
        }

        .activity-action {
            grid-column: 3;
            justify-content: flex-start;
            margin-top: 5px;
        }

        .activity-action .btn {
            width: 100%;
        }

    }


    /* =========================================================
   VERY SMALL SCREENS
   ========================================================= */

    @media (max-width: 400px) {

        .student-stat-card {
            padding: 15px;
        }

        .student-stat-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
        }

        .student-stat-card strong {
            font-size: 20px;
        }

        .activity-count {
            display: none;
        }

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
             PAGE HEADER
        ================================================== -->

            <div class="page-header">

                <div>

                    <h2>
                        My Activities
                    </h2>

                    <p>
                        Watch your assigned lectures
                        and complete your asynchronous activities.
                    </p>

                </div>

            </div>


            <!-- =================================================
             STATISTICS
        ================================================== -->

            <div class="row g-3 mb-4">


                <!-- TOTAL -->

                <div class="col-xl-3 col-md-6">

                    <div class="student-stat-card">

                        <div
                            class="student-stat-icon blue">

                            <i
                                class="bi bi-journal-text">
                            </i>

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


                <!-- NOT STARTED -->

                <div class="col-xl-3 col-md-6">

                    <div class="student-stat-card">

                        <div
                            class="student-stat-icon gray">

                            <i
                                class="bi bi-circle">
                            </i>

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


                <!-- IN PROGRESS -->

                <div class="col-xl-3 col-md-6">

                    <div class="student-stat-card">

                        <div
                            class="student-stat-icon orange">

                            <i
                                class="bi bi-play-circle">
                            </i>

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


                <!-- COMPLETED -->

                <div class="col-xl-3 col-md-6">

                    <div class="student-stat-card">

                        <div
                            class="student-stat-icon green">

                            <i
                                class="bi bi-check-circle">
                            </i>

                        </div>


                        <div>

                            <span>
                                Completed
                            </span>

                            <strong>

                                <?= $completedActivities ?>

                                <small>
                                    (<?= $completionPercentage ?>%)
                                </small>

                            </strong>

                        </div>

                    </div>

                </div>


            </div>


            <!-- =================================================
             OVERALL LEARNING PROGRESS
        ================================================== -->

            <div
                class="learning-progress-card mb-4">


                <div
                    class="learning-progress-header">


                    <div>

                        <h5>
                            Overall Learning Progress
                        </h5>

                        <p>
                            Your progress through the assigned
                            asynchronous lectures.
                        </p>

                    </div>


                    <strong>

                        <?= $completionPercentage ?>%

                    </strong>


                </div>


                <!-- PROGRESS BAR -->

                <div
                    class="progress learning-progress"
                    role="progressbar"
                    aria-valuenow="<?= $completionPercentage ?>"
                    aria-valuemin="0"
                    aria-valuemax="100">


                    <div
                        class="progress-bar"
                        style="
                        width:
                        <?= $completionPercentage ?>%;
                    ">
                    </div>


                </div>


                <!-- PROGRESS FOOTER -->

                <div
                    class="learning-progress-footer">


                    <span>

                        <?= $completedActivities ?>

                        of

                        <?= $totalActivities ?>

                        lectures completed

                    </span>


                    <?php if (
                        $completionPercentage >= 100 &&
                        $totalActivities > 0
                    ): ?>


                        <span class="text-success">

                            <i
                                class="bi bi-check-circle-fill me-1">
                            </i>

                            All lectures completed

                        </span>


                    <?php elseif (
                        $completionPercentage > 0
                    ): ?>


                        <span>

                            Keep going!

                        </span>


                    <?php else: ?>


                        <span>

                            Start your first lecture

                        </span>


                    <?php endif; ?>


                </div>


            </div>


            <!-- =================================================
             ACTIVITY CARD
        ================================================== -->

            <div class="activity-card">


                <!-- =================================================
                 HEADER
            ================================================== -->

                <div
                    class="activity-card-header">


                    <div>

                        <h5>
                            Asynchronous Lectures
                        </h5>

                        <p>
                            Your assigned learning activities
                        </p>

                    </div>


                    <span
                        class="activity-count">

                        <?= $totalActivities ?>

                        activities

                    </span>


                </div>


                <!-- =================================================
                 ACTIVITIES
            ================================================== -->

                <?php if (
                    $totalActivities > 0
                ): ?>


                    <div
                        class="activity-list">


                        <?php

                        $activityNumber = 1;

                        ?>


                        <?php foreach (
                            $activityRecords
                            as $activity
                        ): ?>


                            <?php

                            /* =====================================
                           STATUS
                        ===================================== */

                            $status =
                                $activity["progress_status"] ?? "";


                            if (
                                $status ===
                                "completed"
                            ) {

                                $statusClass =
                                    "completed";

                                $statusText =
                                    "Completed";

                                $statusIcon =
                                    "bi-check-circle-fill";


                                $lectureProgress =
                                    100;
                            } elseif (
                                !empty($activity["started_at"])
                            ) {

                                $statusClass =
                                    "in-progress";

                                $statusText =
                                    "In Progress";

                                $statusIcon =
                                    "bi-play-circle-fill";


                                /*
                             * Temporary progress.
                             *
                             * Later we will replace this
                             * with actual YouTube percentage.
                             */

                                $lectureProgress =
                                    50;
                            } else {

                                $statusClass =
                                    "not-started";

                                $statusText =
                                    "Not Started";

                                $statusIcon =
                                    "bi-circle";


                                $lectureProgress =
                                    0;
                            }


                            /* =====================================
                           DUE DATE
                        ===================================== */

                            $dueText = "";


                            if (
                                !empty($activity["due_date"])
                            ) {

                                $timestamp =
                                    strtotime(
                                        $activity["due_date"]
                                    );


                                if (
                                    $timestamp !== false
                                ) {

                                    $dueText =
                                        date(
                                            "M d, Y",
                                            $timestamp
                                        );
                                }
                            }


                            /* =====================================
                           START DATE
                        ===================================== */

                            $startText = "";


                            if (
                                !empty($activity["start_date"])
                            ) {

                                $timestamp =
                                    strtotime(
                                        $activity["start_date"]
                                    );


                                if (
                                    $timestamp !== false
                                ) {

                                    $startText =
                                        date(
                                            "M d, Y",
                                            $timestamp
                                        );
                                }
                            }


                            ?>


                            <!-- =================================
                             ACTIVITY ITEM
                        ================================== -->

                            <div
                                class="activity-item">


                                <!-- NUMBER -->

                                <div
                                    class="activity-number">

                                    <?= $activityNumber++ ?>

                                </div>


                                <!-- ICON -->

                                <div
                                    class="
                                activity-icon
                                <?= $statusClass ?>">

                                    <i
                                        class="
                                    bi
                                    <?= $statusIcon ?>">
                                    </i>

                                </div>


                                <!-- INFORMATION -->

                                <div
                                    class="activity-information">


                                    <h5>

                                        <?= htmlspecialchars(
                                            $activity["title"]
                                        ) ?>

                                    </h5>


                                    <!-- META -->

                                    <div
                                        class="activity-meta">


                                        <?php if (
                                            $startText !== ""
                                        ): ?>

                                            <span>

                                                <i
                                                    class="bi bi-calendar3">
                                                </i>

                                                <?= htmlspecialchars(
                                                    $startText
                                                ) ?>

                                            </span>

                                        <?php endif; ?>


                                        <?php if (
                                            $dueText !== ""
                                        ): ?>

                                            <span>

                                                <i
                                                    class="bi bi-clock">
                                                </i>

                                                Due
                                                <?= htmlspecialchars(
                                                    $dueText
                                                ) ?>

                                            </span>

                                        <?php endif; ?>


                                    </div>


                                    <!-- =================================
                                     INDIVIDUAL PROGRESS
                                ================================== -->

                                    <div
                                        class="lecture-progress-wrapper">


                                        <div
                                            class="lecture-progress-bar">


                                            <div
                                                class="
                                            lecture-progress-fill
                                            <?= $lectureProgress === 100
                                                ? "completed"
                                                : ""
                                            ?>"
                                                style="
                                                width:
                                                <?= $lectureProgress ?>%;
                                            ">
                                            </div>


                                        </div>


                                        <span>

                                            <?= $lectureProgress ?>%

                                        </span>


                                    </div>


                                </div>


                                <!-- STATUS -->

                                <div
                                    class="activity-status">


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


                                    <?php if (
                                        !empty($activity["access_count"])
                                    ): ?>


                                        <small>

                                            <?= (int)(
                                                $activity["access_count"]
                                            ) ?>

                                            access(es)

                                        </small>


                                    <?php endif; ?>


                                </div>


                                <!-- ACTION -->

                                <div
                                    class="activity-action">


                                    <a
                                        href="
                                    activity_view.php?id=
                                    <?= (int)$activity["id"] ?>
                                    "
                                        class="btn btn-primary">


                                        <?php if (
                                            $status ===
                                            "completed"
                                        ): ?>


                                            <i
                                                class="
                                            bi
                                            bi-arrow-repeat
                                            me-1">
                                            </i>

                                            Review


                                        <?php elseif (
                                            !empty($activity["started_at"])
                                        ): ?>


                                            <i
                                                class="
                                            bi
                                            bi-play-fill
                                            me-1">
                                            </i>

                                            Continue


                                        <?php else: ?>


                                            <i
                                                class="
                                            bi
                                            bi-play-fill
                                            me-1">
                                            </i>

                                            Watch


                                        <?php endif; ?>


                                    </a>


                                </div>


                            </div>


                        <?php endforeach; ?>


                    </div>


                <?php else: ?>


                    <!-- =========================================
                     EMPTY STATE
                ========================================== -->

                    <div
                        class="activity-empty">


                        <div
                            class="activity-empty-icon">

                            <i
                                class="bi bi-journal-x">
                            </i>

                        </div>


                        <h5>

                            No activities yet

                        </h5>


                        <p>

                            There are currently no
                            asynchronous activities assigned
                            to your class.

                        </p>


                    </div>


                <?php endif; ?>


            </div>


        </div>


    </main>


    <!-- =========================================================
     JAVASCRIPT
========================================================= -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>