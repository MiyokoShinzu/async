<?php

/* ========================================================= 
   STUDENT READING ACTIVITIES 
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
        substr($firstName, 0, 1)
    );
}

if ($lastName !== "") {

    $initials .= strtoupper(
        substr($lastName, 0, 1)
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
   GET READING ACTIVITIES 
   ========================================================= */

$sql = " 
    SELECT 
 
        r.id, 
 
        r.title, 
 
        r.description, 
 
        r.file_url, 
 
        r.file_type, 
 
        r.department, 
 
        r.year_level, 
 
        r.section, 
 
        r.start_date, 
 
        r.due_date, 
 
        r.required_reading_minutes, 
 
        rp.started_at, 
 
        rp.last_accessed_at, 
 
        rp.access_count, 
 
        rp.completed_at, 
 
        rp.status AS progress_status, 
 
        rp.reading_seconds 
 
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
 
    ORDER BY 
 
        r.start_date DESC, 
 
        r.id DESC 
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
   STORE READING ACTIVITIES 
   ========================================================= */

$readingRecords = [];


while (
    $reading =
    $result->fetch_assoc()
) {

    $readingRecords[] =
        $reading;
}


$stmt->close();


/* ========================================================= 
   STATISTICS 
   ========================================================= */

$totalReadings =
    count($readingRecords);


$completedReadings =
    0;


$inProgressReadings =
    0;


$notStartedReadings =
    0;


/* ========================================================= 
   CALCULATE PROGRESS 
   ========================================================= */

foreach (
    $readingRecords
    as $reading
) {

    $status =
        $reading["progress_status"] ?? "";


    if (
        $status ===
        "completed"
    ) {

        $completedReadings++;
    } elseif (
        !empty($reading["started_at"])
    ) {

        $inProgressReadings++;
    } else {

        $notStartedReadings++;
    }
}


/* ========================================================= 
   OVERALL COMPLETION 
   ========================================================= */

$completionPercentage =

    $totalReadings > 0

    ? round(
        (
            $completedReadings /
            $totalReadings
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
                        Readings Activities
                    </h2>

                    <p>
                        Read your assigned learning materials
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
                                class="bi bi-book">
                            </i>

                        </div>


                        <div>

                            <span>
                                Total Readings
                            </span>

                            <strong>
                                <?= $totalReadings ?>
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
                                <?= $notStartedReadings ?>
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
                                class="bi bi-book-half">
                            </i>

                        </div>


                        <div>

                            <span>
                                In Progress
                            </span>

                            <strong>
                                <?= $inProgressReadings ?>
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

                                <?= $completedReadings ?>

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
                            Overall Reading Progress
                        </h5>

                        <p>
                            Your progress through the assigned
                            asynchronous reading materials.
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

                        <?= $completedReadings ?>

                        of

                        <?= $totalReadings ?>

                        readings completed

                    </span>


                    <?php if (
                        $completionPercentage >= 100 &&
                        $totalReadings > 0
                    ): ?>


                        <span class="text-success">

                            <i
                                class="bi bi-check-circle-fill me-1">
                            </i>

                            All readings completed

                        </span>


                    <?php elseif (
                        $completionPercentage > 0
                    ): ?>


                        <span>

                            Keep going!

                        </span>


                    <?php else: ?>


                        <span>

                            Start your first reading

                        </span>


                    <?php endif; ?>


                </div>


            </div>


            <!-- ================================================= 
         READING CARD 
    ================================================== -->

            <div class="activity-card">


                <!-- ================================================= 
             HEADER 
        ================================================== -->

                <div
                    class="activity-card-header">


                    <div>

                        <h5>
                            Asynchronous Readings
                        </h5>

                        <p>
                            Your assigned learning materials
                        </p>

                    </div>


                    <span
                        class="activity-count">

                        <?= $totalReadings ?>

                        readings

                    </span>


                </div>


                <!-- ================================================= 
             READINGS 
        ================================================== -->

                <?php if (
                    $totalReadings > 0
                ): ?>


                    <div
                        class="activity-list">


                        <?php

                        $readingNumber = 1;

                        ?>


                        <?php foreach (
                            $readingRecords
                            as $reading
                        ): ?>


                            <?php

                            /* ===================================== 
                       STATUS 
                    ===================================== */

                            $status =
                                $reading["progress_status"] ?? "";


                            /* ===================================== 
                       REQUIRED READING TIME 
                    ===================================== */

                            $requiredReadingMinutes =
                                (int)(
                                    $reading["required_reading_minutes"] ?? 0
                                );


                            $requiredReadingSeconds =
                                $requiredReadingMinutes * 60;


                            /* ===================================== 
                       ACTUAL READING TIME 
                    ===================================== */

                            $readingSeconds =
                                (float)(
                                    $reading["reading_seconds"] ?? 0
                                );


                            /* ===================================== 
                       STATUS AND REAL PROGRESS 
                    ===================================== */

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


                                $readingProgress =
                                    100;
                            } elseif (
                                $requiredReadingSeconds > 0 &&
                                $readingSeconds > 0
                            ) {

                                $statusClass =
                                    "in-progress";

                                $statusText =
                                    "In Progress";

                                $statusIcon =
                                    "bi-book-half";


                                /* 
                                 * REAL READING PROGRESS
                                 *
                                 * Actual reading seconds
                                 * divided by the required
                                 * reading seconds.
                                 */

                                $readingProgress =
                                    (
                                        $readingSeconds /
                                        $requiredReadingSeconds
                                    ) * 100;


                                /* 
                                 * Never visually reach 100%
                                 * until the activity is
                                 * actually completed.
                                 */

                                $readingProgress =
                                    min(
                                        99.99,
                                        $readingProgress
                                    );


                                $readingProgress =
                                    round(
                                        $readingProgress,
                                        2
                                    );
                            } else {

                                $statusClass =
                                    "not-started";

                                $statusText =
                                    "Not Started";

                                $statusIcon =
                                    "bi-circle";


                                $readingProgress =
                                    0;
                            }


                            /* ===================================== 
                       DUE DATE 
                    ===================================== */

                            $dueText = "";


                            if (
                                !empty($reading["due_date"])
                            ) {

                                $timestamp =
                                    strtotime(
                                        $reading["due_date"]
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
                                !empty($reading["start_date"])
                            ) {

                                $timestamp =
                                    strtotime(
                                        $reading["start_date"]
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


                            /* ===================================== 
                       FILE TYPE 
                    ===================================== */

                            $fileType =
                                strtolower(
                                    trim(
                                        $reading["file_type"] ?? ""
                                    )
                                );


                            /* ===================================== 
                       FILE ICON 
                    ===================================== */

                            switch ($fileType) {

                                case "pdf":

                                    $fileIcon =
                                        "bi-file-earmark-pdf";

                                    break;


                                case "doc":

                                case "docx":

                                    $fileIcon =
                                        "bi-file-earmark-word";

                                    break;


                                case "ppt":

                                case "pptx":

                                    $fileIcon =
                                        "bi-file-earmark-ppt";

                                    break;

                                default:

                                    $fileIcon =
                                        "bi-file-earmark-text";

                                    break;
                            }


                            /* ===================================== 
                       CLEAN READING ID 
                    ===================================== */

                            $readingId =
                                (int)$reading["id"];


                            ?>


                            <!-- ================================= 
                         READING ITEM 
                    ================================== -->

                            <div
                                class="activity-item">


                                <!-- NUMBER -->

                                <div
                                    class="activity-number">

                                    <?= $readingNumber++ ?>

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
                                            $reading["title"]
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


                                        <span>

                                            <i
                                                class="bi bi-file-earmark">
                                            </i>

                                            <?= htmlspecialchars(
                                                strtoupper(
                                                    $fileType
                                                )
                                            ) ?>

                                        </span>


                                        <?php if (
                                            $requiredReadingMinutes > 0
                                        ): ?>

                                            <span>

                                                <i
                                                    class="bi bi-hourglass-split">
                                                </i>

                                                <?= number_format(
                                                    $requiredReadingMinutes
                                                ) ?>

                                                min reading

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
                                        <?= $readingProgress >= 100
                                            ? "completed"
                                            : ""
                                        ?>"
                                                style=" 
                                            width: 
                                            <?= $readingProgress ?>%; 
                                        ">
                                            </div>


                                        </div>


                                        <span>

                                            <?= number_format(
                                                $readingProgress,
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
                                        !empty($reading["access_count"])
                                    ): ?>


                                        <small>

                                            <?= (int)(
                                                $reading["access_count"]
                                            ) ?>

                                            access(es)

                                        </small>


                                    <?php endif; ?>


                                </div>


                                <!-- ACTION -->

                                <div
                                    class="activity-action">


                                    <!-- 
                                    IMPORTANT:
                                    Keep this URL on ONE LINE.
                                    Do NOT add whitespace around
                                    the ID.
                                -->

                                    <a
                                        href="reading_activity_view.php?id=<?= $readingId ?>"
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
                                            !empty($reading["started_at"])
                                        ): ?>

                                            <i
                                                class=" 
                                        bi 
                                        bi-book-half 
                                        me-1">
                                            </i>

                                            Continue


                                        <?php else: ?>

                                            <i
                                                class=" 
                                        bi 
                                        bi-book-half 
                                        me-1">
                                            </i>

                                            Read

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

                            <i class="bi bi-book-x">
                            </i>

                        </div>


                        <h5>

                            No readings yet

                        </h5>


                        <p>

                            There are currently no
                            asynchronous reading materials
                            assigned to your class.

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