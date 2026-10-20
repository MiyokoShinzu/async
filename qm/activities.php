<?php

/* =========================================================  
   STUDENT LECTURES  
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

    $initials .= strtoupper(
        substr(
            $firstName,
            0,
            1
        )
    );
}

if ($lastName !== "") {

    $initials .= strtoupper(
        substr(
            $lastName,
            0,
            1
        )
    );
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
   GET LECTURES  
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
   STORE LECTURES  
   ========================================================= */

$lectureRecords = [];


while (
    $lecture =
    $result->fetch_assoc()
) {

    $lectureRecords[] =
        $lecture;
}


$stmt->close();


/* =========================================================  
   STATISTICS  
   ========================================================= */

$totalLectures =
    count($lectureRecords);


$completedLectures =
    0;


$inProgressLectures =
    0;


$notStartedLectures =
    0;


/* =========================================================  
   CALCULATE STATUS  
   ========================================================= */

foreach (
    $lectureRecords
    as $lecture
) {

    $status =
        $lecture["progress_status"] ?? "";


    $watchedSeconds =
        (float)(
            $lecture["watched_seconds"]
            ?? 0
        );


    if (
        $status === "completed"
    ) {

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


/* =========================================================  
   OVERALL COMPLETION  
   ========================================================= */

$completionPercentage =

    $totalLectures > 0

    ? round(
        (
            $completedLectures /
            $totalLectures
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
```

========================================================= -->

     <?php include 'globals/sidebar.php'; ?>


    <!-- =========================================================  
 TOPBAR  
```

========================================================= -->

     <?php include 'globals/topbar.php'; ?>


    <!-- =========================================================  
 MAIN CONTENT  
```

========================================================= -->

     <main class="main-content">


        <div class="content-wrapper">


            <!-- =================================================  
         PAGE HEADER  
    ================================================== -->

            <div class="page-header">

                <div>

                    <h2>
                        Video Lectures/Demos
                    </h2>

                    <p>
                        Watch your assigned lectures
                        and complete your asynchronous learning.
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
                                Total Lectures
                            </span>

                            <strong>
                                <?= $totalLectures ?>
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
                                <?= $notStartedLectures ?>
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
                                <?= $inProgressLectures ?>
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

                                <?= $completedLectures ?>

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

                        <?= $completedLectures ?>

                        of

                        <?= $totalLectures ?>

                        lectures completed

                    </span>


                    <?php if (
                        $completionPercentage >= 100 &&
                        $totalLectures > 0
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
         LECTURE CARD  
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
                            Your assigned learning lectures
                        </p>

                    </div>


                    <span
                        class="activity-count">

                        <?= $totalLectures ?>

                        lectures

                    </span>


                </div>


                <!-- =================================================  
             LECTURES  
        ================================================== -->

                <?php if (
                    $totalLectures > 0
                ): ?>


                    <div
                        class="activity-list">


                        <?php

                        $lectureNumber = 1;

                        ?>


                        <?php foreach (
                            $lectureRecords
                            as $lecture
                        ): ?>


                            <?php

                            /* =====================================  
                       STATUS  
                    ===================================== */

                            $status =
                                $lecture["progress_status"] ?? "";


                            $watchedSeconds =
                                (float)(
                                    $lecture["watched_seconds"]
                                    ?? 0
                                );


                            $videoDuration =
                                (float)(
                                    $lecture["video_duration"]
                                    ?? 0
                                );


                            /* =====================================  
                       LECTURE PROGRESS  
                    ===================================== */

                            if (
                                $status === "completed"
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
                                $videoDuration > 0 &&
                                $watchedSeconds > 0
                            ) {

                                $statusClass =
                                    "in-progress";

                                $statusText =
                                    "In Progress";

                                $statusIcon =
                                    "bi-play-circle-fill";


                                $lectureProgress =
                                    (
                                        $watchedSeconds /
                                        $videoDuration
                                    ) * 100;


                                /* 
                             * Never display 100% unless 
                             * the lecture is actually 
                             * marked completed. 
                             */

                                $lectureProgress =
                                    min(
                                        99.99,
                                        $lectureProgress
                                    );


                                $lectureProgress =
                                    round(
                                        $lectureProgress,
                                        2
                                    );
                            } elseif (
                                !empty($lecture["started_at"])
                            ) {

                                $statusClass =
                                    "in-progress";

                                $statusText =
                                    "In Progress";

                                $statusIcon =
                                    "bi-play-circle-fill";

                                $lectureProgress =
                                    0;
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
                                !empty($lecture["due_date"])
                            ) {

                                $timestamp =
                                    strtotime(
                                        $lecture["due_date"]
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
                                !empty($lecture["start_date"])
                            ) {

                                $timestamp =
                                    strtotime(
                                        $lecture["start_date"]
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
                         LECTURE ITEM  
                    ================================== -->

                            <div
                                class="activity-item">


                                <!-- NUMBER -->

                                <div
                                    class="activity-number">

                                    <?= $lectureNumber++ ?>

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
                                            $lecture["title"]
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
                                        <?= $lectureProgress >= 100
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

                                            <?= number_format(
                                                $lectureProgress,
                                                2
                                            ) ?>%

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
                                        !empty($lecture["access_count"])
                                    ): ?>


                                        <small>

                                            <?= (int)(
                                                $lecture["access_count"]
                                            ) ?>

                                            access(es)

                                        </small>


                                    <?php endif; ?>


                                </div>


                                <!-- ACTION -->

                                <div
                                    class="activity-action">


                                    <a href="activity_view.php?id=<?= (int)$lecture["id"] ?>" class="btn btn-primary">


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
                                            !empty($lecture["started_at"]) ||
                                            $watchedSeconds > 0
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

                            <i class="bi bi-journal-x">
                            </i>

                        </div>


                        <h5>

                            No lectures yet

                        </h5>


                        <p>

                            There are currently no
                            asynchronous lectures assigned
                            to your class.

                        </p>


                    </div>


                <?php endif; ?>


            </div>


        </div>


    </main>


    <!-- =========================================================  
 JAVASCRIPT  
```

========================================================= -->

     <?php include 'globals/scripts.php'; ?>
 
</body>

</html>