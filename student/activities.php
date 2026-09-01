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