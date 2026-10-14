<?php

/* =========================================================
   STUDENT DASHBOARD
   ETS-Async Learning Portal

   FEATURES:
   ---------------------------------------------------------
   1. Lecture activity monitoring
   2. Reading activity monitoring
   3. Academic Post activity monitoring
   4. Overall learning progress
   5. Recent learning activities
   6. Academic Post access history
   7. Academic Post access statistics

   IMPORTANT:
   ---------------------------------------------------------
   This file DOES NOT modify the database structure,
   collations, tables, or columns.

   Academic Post monitoring reads from:

       academic_post_access_logs

   and joins:

       academic_posts

   using the integer post_id field.

   The student_id comparison is performed only against
   academic_post_access_logs.student_id, avoiding the
   collation conflict that can occur when joining student
   account fields with different collations.
   ========================================================= */


/* =========================================================
   SESSION
========================================================= */

session_cache_limiter('nocache');
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

/* Always fetch fresh dashboard statistics from the database. */
if (!headers_sent()) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
}


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


/*
   Use first name instead of undefined $username.
*/

$displayName =
    $firstName !== ""
    ? $firstName
    : "Student";


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
   STUDENT ACADEMIC CALENDAR
   Includes existing learning activities and admin-managed
   calendar events/activities from dedicated tables.
========================================================= */

$studentCalendarEvents = [];

// Existing lecture and reading activity dates.
$calendarSources = [
    ["table" => "lectures", "type" => "Lecture", "url" => "activity_view.php?id=", "dateFields" => ["start_date" => "Available", "due_date" => "Due"]],
    ["table" => "reading_activity_table", "type" => "Reading Activity", "url" => "reading_activity_view.php?id=", "dateFields" => ["start_date" => "Available", "due_date" => "Due"]],
];

foreach ($calendarSources as $calendarSource) {
    $table = $calendarSource["table"];
    $calendarQuery = "SELECT id, title, description, start_date, due_date
        FROM `{$table}`
        WHERE (department = ? OR department = '' OR department IS NULL)
          AND (CAST(year_level AS CHAR) = ? OR year_level = '' OR year_level IS NULL)
          AND (section = ? OR section = '' OR section IS NULL)
          AND (status = 'active' OR status IS NULL)
          AND (start_date IS NOT NULL OR due_date IS NOT NULL)
        ORDER BY COALESCE(start_date, due_date) ASC, id DESC LIMIT 300";
    $calendarStmt = $mysqli->prepare($calendarQuery);
    if ($calendarStmt) {
        $calendarStmt->bind_param("sss", $department, $yearLevel, $section);
        $calendarStmt->execute();
        $calendarResult = $calendarStmt->get_result();
        while ($calendarRow = $calendarResult->fetch_assoc()) {
            $activityId = (int)$calendarRow["id"];
            $activityTitle = trim((string)($calendarRow["title"] ?? ""));
            $activityDescription = trim((string)($calendarRow["description"] ?? ""));
            foreach ($calendarSource["dateFields"] as $dateField => $dateLabel) {
                if (empty($calendarRow[$dateField])) continue;
                $timestamp = strtotime((string)$calendarRow[$dateField]);
                if ($timestamp === false) continue;
                $eventDate = date("Y-m-d", $timestamp);
                $studentCalendarEvents[$eventDate][] = [
                    "id" => $activityId,
                    "title" => $activityTitle !== "" ? $activityTitle : $calendarSource["type"],
                    "description" => $activityDescription,
                    "type" => $dateLabel === "Due" ? "Deadline" : $calendarSource["type"],
                    "dateLabel" => $dateLabel,
                    "date" => $eventDate,
                    "dateTime" => $calendarRow[$dateField],
                    "url" => $calendarSource["url"] . $activityId
                ];
            }
        }
        $calendarStmt->close();
    }
}

// Admin-created general events and activities live in separate tables.
$managedCalendarSources = [
    ["table" => "academic_calendar_events", "type" => "Event", "dateField" => "event_date", "label" => "Event"],
    ["table" => "academic_calendar_activities", "type" => "Calendar Activity", "dateField" => "activity_date", "label" => "Scheduled"],
];
foreach ($managedCalendarSources as $source) {
    $table = $source["table"];
    $dateField = $source["dateField"];
    $query = "SELECT id, title, description, {$dateField} AS primary_date, " .
        ($table === "academic_calendar_activities" ? "due_date" : "NULL AS due_date") . ", start_time, location, category
        FROM `{$table}`
        WHERE status = 'active'
          AND (department = ? OR department = '' OR department IS NULL)
          AND (CAST(year_level AS CHAR) = ? OR year_level = '' OR year_level IS NULL)
          AND (section = ? OR section = '' OR section IS NULL)
          AND ({$dateField} IS NOT NULL OR " . ($table === "academic_calendar_activities" ? "due_date IS NOT NULL" : "1=0") . ")
        ORDER BY {$dateField} ASC, id DESC LIMIT 300";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) continue; // Keep the dashboard working until the SQL setup is imported.
    $stmt->bind_param("sss", $department, $yearLevel, $section);
    if (!$stmt->execute()) {
        $stmt->close();
        continue;
    }
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $id = (int)$row["id"];
        $title = trim((string)($row["title"] ?? ""));
        $description = trim((string)($row["description"] ?? ""));
        $location = trim((string)($row["location"] ?? ""));
        $category = trim((string)($row["category"] ?? ""));
        if (!empty($row["primary_date"])) {
            $ts = strtotime((string)$row["primary_date"]);
            if ($ts !== false) {
                $key = date("Y-m-d", $ts);
                $studentCalendarEvents[$key][] = [
                    "id" => $id,
                    "title" => $title !== "" ? $title : $source["type"],
                    "description" => $description,
                    "type" => $source["type"],
                    "category" => $category,
                    "dateLabel" => $source["label"],
                    "date" => $key,
                    "dateTime" => $row["start_time"] ?? "",
                    "location" => $location,
                    "url" => ""
                ];
            }
        }
        if (!empty($row["due_date"])) {
            $ts = strtotime((string)$row["due_date"]);
            if ($ts !== false) {
                $key = date("Y-m-d", $ts);
                $studentCalendarEvents[$key][] = [
                    "id" => $id,
                    "title" => $title !== "" ? $title : $source["type"],
                    "description" => $description,
                    "type" => "Deadline",
                    "dateLabel" => "Due",
                    "date" => $key,
                    "dateTime" => "",
                    "location" => $location,
                    "url" => ""
                ];
            }
        }
    }
    $stmt->close();
}

$studentCalendarEventsJson = json_encode(
    $studentCalendarEvents,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
);
if ($studentCalendarEventsJson === false) $studentCalendarEventsJson = "{}";


/* =========================================================
   INITIALIZE STATISTICS
========================================================= */

$totalLectures = 0;
$completedLectures = 0;
$inProgressLectures = 0;
$notStartedLectures = 0;
$lectureProgressTotalPercent = 0.0;


$totalReadingActivities = 0;
$completedReadingActivities = 0;
$inProgressReadingActivities = 0;
$notStartedReadingActivities = 0;


/* =========================================================
   ACADEMIC POST MONITORING STATISTICS
========================================================= */

$totalAcademicPostAccesses = 0;

$totalAcademicPostsAccessed = 0;

$totalAcademicPostReadingSeconds = 0;

$openAcademicPostSessions = 0;


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


        $status = strtolower(trim(
            (string)($lecture["progress_status"] ?? "")
        ));

        $watchedSeconds = max(0, (float)(
            $lecture["watched_seconds"] ?? 0
        ));

        $videoDuration = max(0, (float)(
            $lecture["video_duration"] ?? 0
        ));

        /*
         * Use actual watch percentage for the lecture progress bar.
         * A completed status always counts as 100%, while a partial
         * watch contributes proportionally instead of remaining at 0%.
         */
        if ($status === "completed") {
            $lectureProgressTotalPercent += 100;
            $completedLectures++;
        } else {
            $watchPercent = $videoDuration > 0
                ? min(100, ($watchedSeconds / $videoDuration) * 100)
                : 0;

            $lectureProgressTotalPercent += $watchPercent;

            if (!empty($lecture["started_at"]) || $watchedSeconds > 0) {
                $inProgressLectures++;
            } else {
                $notStartedLectures++;
            }
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


        $status = strtolower(trim(
            (string)($reading["progress_status"] ?? "")
        ));


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
   ACADEMIC POST STATISTICS

   IMPORTANT:
   ---------------------------------------------------------
   We intentionally DO NOT join the accounts/users table
   here.

   This avoids the collation problem:

       utf8mb4_general_ci
       vs
       utf8mb4_unicode_ci

   We only compare:

       academic_post_access_logs.student_id

   against the logged-in student's student ID.
========================================================= */


/* ---------------------------------------------------------
   TOTAL ACCESS SESSIONS
--------------------------------------------------------- */

$academicPostStatsQuery = "

    SELECT

        COUNT(*) AS total_accesses,

        COUNT(
            DISTINCT post_id
        ) AS total_posts,

        COALESCE(
            SUM(
                duration_seconds
            ),
            0
        ) AS total_seconds,

        SUM(
            CASE
                WHEN status = 'open'
                THEN 1
                ELSE 0
            END
        ) AS open_sessions

    FROM academic_post_access_logs

    WHERE student_id = ?

";


$stmt =
    $mysqli->prepare(
        $academicPostStatsQuery
    );


if ($stmt) {

    $stmt->bind_param(
        "s",
        $studentId
    );

    $stmt->execute();

    $result =
        $stmt->get_result();


    $stats =
        $result->fetch_assoc();


    if ($stats) {

        $totalAcademicPostAccesses =
            (int)(
                $stats["total_accesses"] ?? 0
            );


        $totalAcademicPostsAccessed =
            (int)(
                $stats["total_posts"] ?? 0
            );


        $totalAcademicPostReadingSeconds =
            (int)(
                $stats["total_seconds"] ?? 0
            );


        $openAcademicPostSessions =
            (int)(
                $stats["open_sessions"] ?? 0
            );
    }


    $stmt->close();
}


/* =========================================================
   ACADEMIC POST RECENT ACCESS HISTORY
========================================================= */

$academicPostActivities = [];


$academicPostActivityQuery = "

    SELECT

        l.id,

        l.post_id,

        l.opened_at,

        l.closed_at,

        l.duration_seconds,

        l.status,

        p.title AS post_title,

        p.description AS post_description,

        p.subject

    FROM academic_post_access_logs l

    LEFT JOIN academic_posts p
        ON p.id = l.post_id

    WHERE

        l.student_id = ?

    ORDER BY

        l.opened_at DESC,

        l.id DESC

    LIMIT 10

";


$stmt =
    $mysqli->prepare(
        $academicPostActivityQuery
    );


if ($stmt) {

    $stmt->bind_param(
        "s",
        $studentId
    );

    $stmt->execute();

    $result =
        $stmt->get_result();


    while (
        $row =
        $result->fetch_assoc()
    ) {

        $academicPostActivities[] =
            $row;
    }


    $stmt->close();
}


/* =========================================================
   ACADEMIC POST ACCESS COUNT PER POST

   This allows the student to see how many times they have
   opened each Academic Post.
========================================================= */

$academicPostAccessCounts = [];


$academicPostCountQuery = "

    SELECT

        post_id,

        COUNT(*) AS access_count,

        MAX(opened_at) AS last_accessed

    FROM academic_post_access_logs

    WHERE student_id = ?

    GROUP BY post_id

";


$stmt =
    $mysqli->prepare(
        $academicPostCountQuery
    );


if ($stmt) {

    $stmt->bind_param(
        "s",
        $studentId
    );

    $stmt->execute();

    $result =
        $stmt->get_result();


    while (
        $row =
        $result->fetch_assoc()
    ) {

        $academicPostAccessCounts[(int)$row["post_id"]] = [

            "access_count" =>
            (int)$row["access_count"],

            "last_accessed" =>
            $row["last_accessed"]

        ];
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

/*
 * Overall progress includes partial lecture watch percentages.
 * Reading activities are counted as 100% only when marked completed,
 * because this schema does not provide a total reading-time target.
 */
$overallProgressPoints =
    $lectureProgressTotalPercent +
    ($completedReadingActivities * 100);

$overallProgress =
    $totalActivities > 0
    ? round(min(100, $overallProgressPoints / $totalActivities), 1)
    : 0;


/* =========================================================
   LECTURE PROGRESS PERCENTAGE
========================================================= */

$lectureCompletion =
    $totalLectures > 0
    ? round(min(100, $lectureProgressTotalPercent / $totalLectures), 1)
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
   FORMAT DURATION
========================================================= */

function formatDuration($seconds)
{
    $seconds =
        max(
            0,
            (int)$seconds
        );


    if ($seconds < 60) {

        return
            $seconds . " sec";
    }


    $minutes =
        floor(
            $seconds / 60
        );


    $remainingSeconds =
        $seconds % 60;


    if ($minutes < 60) {

        if ($remainingSeconds > 0) {

            return
                $minutes .
                " min " .
                $remainingSeconds .
                " sec";
        }


        return
            $minutes .
            " min";
    }


    $hours =
        floor(
            $minutes / 60
        );


    $remainingMinutes =
        $minutes % 60;


    if ($remainingMinutes > 0) {

        return
            $hours .
            " hr " .
            $remainingMinutes .
            " min";
    }


    return
        $hours .
        " hr";
}


/* =========================================================
   FORMAT DATE AND TIME
========================================================= */

function formatDateTime($date)
{
    if (empty($date)) {

        return "—";
    }


    $timestamp =
        strtotime($date);


    if (!$timestamp) {

        return "—";
    }


    return date(
        "M d, Y h:i A",
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


    <!-- =========================================================
     SIDEBAR
========================================================== -->

    <?php include "globals/sidebar.php"; ?>


    <!-- =========================================================
     SIDEBAR OVERLAY
========================================================== -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay">
    </div>


    <!-- =========================================================
     TOPBAR
========================================================== -->

    <?php include "globals/topbar.php"; ?>


    <!-- =========================================================
     MAIN CONTENT
========================================================== -->

    <main class="main-content">

        <div class="content-wrapper">


            <!-- =================================================
             DASHBOARD HEADER
        ================================================== -->

            <div class="dashboard-heading">

                <div>

                    <h2 class="">

                        Welcome,
                        <?= e($displayName) ?>

                    </h2>

                    <p class="dashboard-heading-subtitle">

                        Monitor your learning activities,
                        progress, and academic materials.

                    </p>

                </div>

            </div>

            <!-- New learning activities first, with a compact agenda sidebar. -->
            <section class="student-learning-overview mb-3" aria-label="New learning activities">
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
                                        $activity["description"]
                                            ?? ""
                                    );


                                $activityDate =
                                    $activity["created_at"];


                                $activityStatus = strtolower(trim(
                                    (string)($activity["progress_status"] ?? "")
                                ));


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

                                    ? "activity_view.php?id="
                                    . $activityId

                                    : "reading_activity_view.php?id="
                                    . $activityId;

                                ?>


                                <div
                                    class="
                                activity-item
                            ">


                                    <!-- NUMBER -->

                                    <div
                                        class="
                                    activity-number
                                ">

                                        <?= $index + 1 ?>

                                    </div>


                                    <!-- ICON -->

                                    <div
                                        class="
                                    activity-icon
                                    <?= e(
                                        $statusClass
                                    ) ?>
                                ">

                                        <i
                                            class="
                                        bi
                                        <?= e(
                                            $activity["icon"]
                                        ) ?>
                                    ">
                                        </i>

                                    </div>


                                    <!-- INFORMATION -->

                                    <div
                                        class="
                                    activity-information
                                ">


                                        <div
                                            class="
                                        activity-title-row
                                    ">

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

                                                <span
                                                    class="
                                                activity-new
                                            ">

                                                    <i
                                                        class="
                                                    bi
                                                    bi-stars
                                                ">
                                                    </i>

                                                    NEW

                                                </span>

                                            <?php endif; ?>

                                        </div>


                                        <div
                                            class="
                                        activity-type
                                    ">

                                            <i
                                                class="
                                            bi
                                            <?= $activityType === "lecture"
                                                ? "bi-play-circle"
                                                : "bi-book"
                                            ?>
                                        ">
                                            </i>

                                            <?= e(
                                                $activity["type_label"]
                                            ) ?>

                                        </div>


                                        <?php if (
                                            $activityDescription !== ""
                                        ): ?>

                                            <div
                                                class="
                                            activity-description
                                        ">

                                                <?= e(
                                                    $activityDescription
                                                ) ?>

                                            </div>

                                        <?php endif; ?>


                                        <div
                                            class="
                                        activity-meta
                                    ">

                                            <span>

                                                <i
                                                    class="
                                                bi
                                                bi-clock
                                            ">
                                                </i>

                                                <?= e(
                                                    formatActivityDate(
                                                        $activityDate
                                                    )
                                                ) ?>

                                            </span>

                                        </div>


                                    </div>


                                    <!-- STATUS -->

                                    <div
                                        class="
                                    activity-status
                                ">

                                        <span
                                            class="
                                        activity-status-badge
                                        <?= e(
                                            $statusClass
                                        ) ?>
                                    ">

                                            <i
                                                class="
                                            bi
                                            <?= e(
                                                $statusIcon
                                            ) ?>
                                        ">
                                            </i>

                                            <?= e(
                                                $statusText
                                            ) ?>

                                        </span>

                                    </div>


                                    <!-- ACTION -->

                                    <div
                                        class="
                                    activity-action
                                ">

                                        <a
                                            href="<?= e(
                                                        $activityLink
                                                    ) ?>"
                                            class="
                                        btn
                                        btn-primary
                                    ">

                                            <i
                                                class="
                                            bi
                                            bi-arrow-right
                                            me-1
                                        ">
                                            </i>

                                            Open

                                        </a>

                                    </div>


                                </div>


                            <?php endforeach; ?>


                        </div>


                    <?php else: ?>


                        <div
                            class="
                        activity-empty
                    ">

                            <div
                                class="
                            activity-empty-icon
                        ">

                                <i
                                    class="
                                bi
                                bi-bell-slash
                            ">
                                </i>

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


            </section>

            <aside class="student-agenda-sidebar mb-4" aria-label="Events and activities overview">
                <section class="student-agenda-panel" aria-labelledby="agendaTodayTitle">
                    <header><span class="agenda-panel-icon today"><i class="bi bi-sun"></i></span>
                        <div>
                            <h5 id="agendaTodayTitle">Events/Activities Today</h5>
                            <p>Scheduled for today</p>
                        </div><strong id="agendaTodayTotal">0</strong>
                    </header>
                    <div class="student-agenda-list" id="agendaTodayList">
                        <p class="agenda-empty">Checking today's schedule…</p>
                    </div>
                </section>
                <section class="student-agenda-panel" aria-labelledby="agendaUpcomingTitle">
                    <header><span class="agenda-panel-icon upcoming"><i class="bi bi-calendar2-week"></i></span>
                        <div>
                            <h5 id="agendaUpcomingTitle">Upcoming Events/Activities</h5>
                            <p>Next scheduled items</p>
                        </div><strong id="agendaUpcomingTotal">0</strong>
                    </header>
                    <div class="student-agenda-list" id="agendaUpcomingList">
                        <p class="agenda-empty">Loading upcoming activities…</p>
                    </div>
                </section>
                <section class="student-agenda-panel" aria-labelledby="agendaEndedTitle">
                    <header><span class="agenda-panel-icon ended"><i class="bi bi-check2-circle"></i></span>
                        <div>
                            <h5 id="agendaEndedTitle">Recently Ended Events/Activities</h5>
                            <p>Previous scheduled items</p>
                        </div><strong id="agendaEndedTotal">0</strong>
                    </header>
                    <div class="student-agenda-list" id="agendaEndedList">
                        <p class="agenda-empty">Loading recent history…</p>
                    </div>
                </section>
            </aside>

            <!-- Floating calendar launcher keeps the dashboard uncluttered. -->
            <button type="button" class="student-calendar-fab" id="studentCalendarOpen" aria-label="Open academic calendar" aria-haspopup="dialog" aria-controls="studentCalendarModal">
                <i class="bi bi-calendar3" aria-hidden="true"></i>
                <span class="student-calendar-fab-label">Calendar</span>
                <span class="student-calendar-fab-count" id="studentCalendarFabCount" aria-label="Scheduled activities">0</span>
            </button>

            <div class="student-calendar-modal" id="studentCalendarModal" role="dialog" aria-modal="true" aria-labelledby="studentCalendarTitle" aria-hidden="true">
                <button type="button" class="student-calendar-modal-backdrop" id="studentCalendarBackdrop" aria-label="Close calendar"></button>
                <div class="student-calendar-modal-dialog" role="document">
                    <button type="button" class="student-calendar-modal-close" id="studentCalendarClose" aria-label="Close academic calendar"><i class="bi bi-x-lg"></i></button>
                    <section class="student-calendar-card" aria-labelledby="studentCalendarTitle">
                        <div class="student-calendar-header">
                            <div class="student-calendar-heading">
                                <div class="student-calendar-heading-icon"><i class="bi bi-calendar3"></i></div>
                                <div>
                                    <h5 id="studentCalendarTitle">Academic Calendar</h5>
                                    <p>Admin-managed events and activities, plus your assigned lectures and reading deadlines.</p>
                                </div>
                            </div>
                            <div class="student-calendar-controls">
                                <button type="button" class="student-calendar-today" id="studentCalendarToday">Today</button>
                                <div class="student-calendar-nav">
                                    <button type="button" id="studentCalendarPrev" aria-label="Previous month"><i class="bi bi-chevron-left"></i></button>
                                    <button type="button" id="studentCalendarNext" aria-label="Next month"><i class="bi bi-chevron-right"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="student-calendar-body">
                            <div class="student-calendar-month-row">
                                <h3 id="studentCalendarMonth" aria-live="polite"></h3>
                                <div class="student-calendar-legend">
                                    <span><i class="calendar-legend-dot lecture"></i> Lecture</span>
                                    <span><i class="calendar-legend-dot reading"></i> Reading</span>
                                    <span><i class="calendar-legend-dot deadline"></i> Due date</span>
                                    <span><i class="calendar-legend-dot event"></i> Admin event</span>
                                </div>
                            </div>

                            <div class="student-calendar-weekdays" aria-hidden="true">
                                <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                            </div>
                            <div class="student-calendar-grid" id="studentCalendarGrid" role="grid" aria-label="Academic calendar dates"></div>

                            <div class="student-calendar-detail" id="studentCalendarDetail" aria-live="polite">
                                <div class="student-calendar-detail-empty">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Select a date to view its activities.</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- =================================================
             ACADEMIC POST MONITORING
        ================================================== -->

            <div class="academic-monitor-card mb-4">


                <!-- HEADER -->

                <div class="academic-monitor-header">

                    <div>

                        <h5>

                            <i class="bi bi-file-earmark-text-fill me-2"></i>

                            My Academic Post Activity

                        </h5>

                        <p>

                            Monitor the academic posts you have opened,
                            including access time and reading duration.

                        </p>

                    </div>


                    <div class="academic-monitor-icon">

                        <i class="bi bi-activity"></i>

                    </div>

                </div>


                <!-- STATISTICS -->

                <div class="row g-3 p-3">


                    <!-- TOTAL ACCESSES -->

                    <div class="col-xl-3 col-md-6">

                        <div class="academic-mini-stat">

                            <div class="academic-mini-icon blue">

                                <i class="bi bi-box-arrow-up-right"></i>

                            </div>

                            <div>

                                <span>
                                    Total Accesses
                                </span>

                                <strong>
                                    <?= $totalAcademicPostAccesses ?>
                                </strong>

                            </div>

                        </div>

                    </div>


                    <!-- UNIQUE POSTS -->

                    <div class="col-xl-3 col-md-6">

                        <div class="academic-mini-stat">

                            <div class="academic-mini-icon purple">

                                <i class="bi bi-file-earmark-text"></i>

                            </div>

                            <div>

                                <span>
                                    Posts Accessed
                                </span>

                                <strong>
                                    <?= $totalAcademicPostsAccessed ?>
                                </strong>

                            </div>

                        </div>

                    </div>


                    <!-- READING TIME -->

                    <div class="col-xl-3 col-md-6">

                        <div class="academic-mini-stat">

                            <div class="academic-mini-icon green">

                                <i class="bi bi-clock-history"></i>

                            </div>

                            <div>

                                <span>
                                    Reading Time
                                </span>

                                <strong>
                                    <?= e(
                                        formatDuration(
                                            $totalAcademicPostReadingSeconds
                                        )
                                    ) ?>
                                </strong>

                            </div>

                        </div>

                    </div>


                    <!-- CURRENTLY OPEN -->

                    <div class="col-xl-3 col-md-6">

                        <div class="academic-mini-stat">

                            <div class="academic-mini-icon orange">

                                <i class="bi bi-eye-fill"></i>

                            </div>

                            <div>

                                <span>
                                    Currently Open
                                </span>

                                <strong>
                                    <?= $openAcademicPostSessions ?>
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                 ACCESS HISTORY
            ================================================== -->

                <div class="academic-history">


                    <div class="academic-history-header">

                        <div>

                            <h6>

                                <i class="bi bi-clock-history me-2"></i>

                                Recent Academic Post Activity

                            </h6>

                        </div>

                    </div>


                    <?php if (
                        !empty($academicPostActivities)
                    ): ?>


                        <div class="table-responsive">

                            <table
                                class="
                                table
                                academic-history-table
                                mb-0
                            ">

                                <thead>

                                    <tr>

                                        <th>
                                            Academic Post
                                        </th>

                                        <th>
                                            Subject
                                        </th>

                                        <th>
                                            Opened
                                        </th>

                                        <th>
                                            Closed
                                        </th>

                                        <th>
                                            Duration
                                        </th>

                                        <th>
                                            Accesses
                                        </th>

                                        <th>
                                            Status
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>


                                    <?php foreach (
                                        $academicPostActivities
                                        as $activity
                                    ): ?>


                                        <?php

                                        $postId =
                                            (int)(
                                                $activity["post_id"]
                                            );


                                        $accessCount =
                                            $academicPostAccessCounts[$postId]["access_count"]
                                            ?? 1;


                                        $status =
                                            $activity["status"]
                                            ?? "closed";


                                        $duration =
                                            (int)(
                                                $activity["duration_seconds"]
                                                ?? 0
                                            );


                                        ?>


                                        <tr>


                                            <!-- POST -->

                                            <td>

                                                <div
                                                    class="
                                                academic-post-name
                                            ">

                                                    <i
                                                        class="
                                                    bi
                                                    bi-file-earmark-text
                                                ">
                                                    </i>

                                                    <span>

                                                        <?= e(
                                                            $activity["post_title"]
                                                                ??
                                                                "Academic Post"
                                                        ) ?>

                                                    </span>

                                                </div>

                                            </td>


                                            <!-- SUBJECT -->

                                            <td>

                                                <?php if (
                                                    !empty($activity["subject"])
                                                ): ?>

                                                    <span
                                                        class="
                                                    academic-subject
                                                ">

                                                        <?= e(
                                                            $activity["subject"]
                                                        ) ?>

                                                    </span>

                                                <?php else: ?>

                                                    <span
                                                        class="
                                                    text-muted
                                                ">

                                                        —

                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                            <!-- OPENED -->

                                            <td>

                                                <div
                                                    class="
                                                academic-time
                                            ">

                                                    <i
                                                        class="
                                                    bi
                                                    bi-box-arrow-up-right
                                                ">
                                                    </i>

                                                    <?= e(
                                                        formatDateTime(
                                                            $activity["opened_at"]
                                                        )
                                                    ) ?>

                                                </div>

                                            </td>


                                            <!-- CLOSED -->

                                            <td>

                                                <?php if (
                                                    $status === "open"
                                                ): ?>

                                                    <span
                                                        class="
                                                    academic-open-label
                                                ">

                                                        <i
                                                            class="
                                                        bi
                                                        bi-circle-fill
                                                    ">
                                                        </i>

                                                        Currently Open

                                                    </span>

                                                <?php else: ?>

                                                    <?= e(
                                                        formatDateTime(
                                                            $activity["closed_at"]
                                                        )
                                                    ) ?>

                                                <?php endif; ?>

                                            </td>


                                            <!-- DURATION -->

                                            <td>

                                                <span
                                                    class="
                                                academic-duration
                                            ">

                                                    <i
                                                        class="
                                                    bi
                                                    bi-stopwatch
                                                ">
                                                    </i>

                                                    <?= e(
                                                        formatDuration(
                                                            $duration
                                                        )
                                                    ) ?>

                                                </span>

                                            </td>


                                            <!-- ACCESS COUNT -->

                                            <td>

                                                <span
                                                    class="
                                                academic-access-count
                                            ">

                                                    <?= $accessCount ?>

                                                </span>

                                            </td>


                                            <!-- STATUS -->

                                            <td>

                                                <?php if (
                                                    $status === "open"
                                                ): ?>

                                                    <span
                                                        class="
                                                    academic-status
                                                    open
                                                ">

                                                        <i
                                                            class="
                                                        bi
                                                        bi-eye-fill
                                                    ">
                                                        </i>

                                                        Open

                                                    </span>

                                                <?php else: ?>

                                                    <span
                                                        class="
                                                    academic-status
                                                    closed
                                                ">

                                                        <i
                                                            class="
                                                        bi
                                                        bi-check-circle-fill
                                                    ">
                                                        </i>

                                                        Closed

                                                    </span>

                                                <?php endif; ?>

                                            </td>


                                        </tr>


                                    <?php endforeach; ?>


                                </tbody>

                            </table>

                        </div>


                    <?php else: ?>


                        <!-- EMPTY -->

                        <div
                            class="
                            academic-history-empty
                        ">

                            <div
                                class="
                                academic-history-empty-icon
                            ">

                                <i
                                    class="
                                    bi
                                    bi-file-earmark-text
                                ">
                                </i>

                            </div>


                            <h6>

                                No Academic Post Activity Yet

                            </h6>


                            <p>

                                When you open an Academic Post,
                                your access time and reading duration
                                will appear here.

                            </p>

                        </div>


                    <?php endif; ?>


                </div>


            </div>





        </div>

    </main>


    <!-- =========================================================
     GLOBAL SCRIPTS
========================================================== -->

    <?php require_once "./globals/scripts.php"; ?>


    <!-- =========================================================
     DASHBOARD STYLE
========================================================== -->

    <style>
        /* =========================================================
   DASHBOARD HEADER
========================================================= */

        .dashboard-heading {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

            margin-bottom:
                24px;
        }


        .dashboard-heading-title {

            margin:
                0;

            font-size:
                24px;

            font-weight:
                700;

            color:
                var(--academic-blue-dark);
        }


        .dashboard-heading-subtitle {

            margin:
                6px 0 0;

            color:
                var(--text-secondary);

            font-size:
                14px;
        }


        /* =========================================================
   STATISTICS
========================================================= */

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
                transform .2s ease,
                box-shadow .2s ease;
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


        .student-stat-icon.blue {

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);
        }


        .student-stat-icon.green {

            background:
                var(--activity-icon-green-bg);

            color:
                var(--activity-icon-green);
        }


        .student-stat-icon.orange {

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-icon-orange);
        }


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


        /* =========================================================
   LEARNING PROGRESS
========================================================= */

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


        /* =========================================================
   ACADEMIC POST MONITOR
========================================================= */

        .academic-monitor-card {

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
        }


        .academic-monitor-header {

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


        .academic-monitor-header h5 {

            margin:
                0;

            color:
                var(--text-color);

            font-size:
                16px;

            font-weight:
                700;
        }


        .academic-monitor-header h5 i {

            color:
                var(--activity-indigo);
        }


        .academic-monitor-header p {

            margin:
                5px 0 0;

            color:
                var(--text-secondary);

            font-size:
                13px;
        }


        .academic-monitor-icon {

            width:
                45px;

            height:
                45px;

            border-radius:
                12px;

            background:
                var(--activity-indigo-bg);

            color:
                var(--activity-indigo);

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                20px;
        }


        /* =========================================================
   ACADEMIC MINI STAT
========================================================= */

        .academic-mini-stat {

            background:
                var(--surface-secondary);

            border:
                1px solid var(--activity-border-light);

            border-radius:
                12px;

            padding:
                15px;

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

            height:
                100%;
        }


        .academic-mini-icon {

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
        }


        .academic-mini-icon.blue {

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);
        }


        .academic-mini-icon.purple {

            background:
                var(--activity-indigo-bg);

            color:
                var(--activity-indigo);
        }


        .academic-mini-icon.green {

            background:
                var(--activity-icon-green-bg);

            color:
                var(--activity-icon-green);
        }


        .academic-mini-icon.orange {

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-icon-orange);
        }


        .academic-mini-stat span {

            display:
                block;

            font-size:
                11px;

            color:
                var(--text-secondary);

            margin-bottom:
                3px;
        }


        .academic-mini-stat strong {

            display:
                block;

            font-size:
                18px;

            color:
                var(--text-color);
        }


        /* =========================================================
   ACADEMIC HISTORY
========================================================= */

        .academic-history {

            border-top:
                1px solid var(--activity-border);
        }


        .academic-history-header {

            padding:
                18px 24px;

            border-bottom:
                1px solid var(--activity-border);
        }


        .academic-history-header h6 {

            margin:
                0;

            color:
                var(--text-color);

            font-weight:
                700;

            font-size:
                14px;
        }


        .academic-history-header i {

            color:
                var(--activity-indigo);
        }


        /* =========================================================
   ACADEMIC HISTORY TABLE
========================================================= */

        .academic-history-table {

            color:
                var(--text-color);

            vertical-align:
                middle;
        }


        .academic-history-table thead th {

            background:
                var(--surface-secondary);

            color:
                var(--text-secondary);

            font-size:
                10px;

            font-weight:
                700;

            text-transform:
                uppercase;

            letter-spacing:
                .4px;

            white-space:
                nowrap;

            border-bottom:
                1px solid var(--activity-border);
        }


        .academic-history-table tbody td {

            color:
                var(--text-color);

            font-size:
                12px;

            border-color:
                var(--activity-border-light);

            padding:
                13px 12px;
        }


        .academic-history-table tbody tr:hover {

            background:
                var(--activity-hover-bg);
        }


        /* =========================================================
   POST NAME
========================================================= */

        .academic-post-name {

            display:
                flex;

            align-items:
                center;

            gap:
                8px;

            font-weight:
                600;

            min-width:
                180px;
        }


        .academic-post-name i {

            color:
                var(--activity-icon-blue);

            font-size:
                16px;
        }


        .academic-subject {

            display:
                inline-block;

            padding:
                4px 8px;

            border-radius:
                6px;

            background:
                var(--activity-indigo-bg);

            color:
                var(--activity-indigo);

            font-size:
                10px;

            font-weight:
                600;
        }


        /* =========================================================
   TIME
========================================================= */

        .academic-time {

            white-space:
                nowrap;

            color:
                var(--text-secondary);

            font-size:
                11px;
        }


        .academic-time i {

            margin-right:
                4px;

            color:
                var(--activity-icon-blue);
        }


        .academic-duration {

            white-space:
                nowrap;

            color:
                var(--text-secondary);

            font-size:
                11px;
        }


        .academic-duration i {

            color:
                var(--activity-icon-orange);

            margin-right:
                4px;
        }


        /* =========================================================
   ACCESS COUNT
========================================================= */

        .academic-access-count {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            min-width:
                28px;

            height:
                28px;

            border-radius:
                50%;

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);

            font-size:
                11px;

            font-weight:
                700;
        }


        /* =========================================================
   STATUS
========================================================= */

        .academic-status {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                5px;

            padding:
                5px 9px;

            border-radius:
                20px;

            font-size:
                10px;

            font-weight:
                600;

            white-space:
                nowrap;
        }


        .academic-status.open {

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-inprogress);
        }


        .academic-status.closed {

            background:
                var(--activity-icon-green-bg);

            color:
                var(--activity-completed);
        }


        .academic-open-label {

            color:
                var(--activity-inprogress);

            font-size:
                11px;

            white-space:
                nowrap;
        }


        .academic-open-label i {

            font-size:
                7px;

            margin-right:
                4px;
        }


        /* =========================================================
   EMPTY STATE
========================================================= */

        .academic-history-empty {

            text-align:
                center;

            padding:
                45px 20px;
        }


        .academic-history-empty-icon {

            width:
                55px;

            height:
                55px;

            border-radius:
                50%;

            margin:
                0 auto 12px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                var(--activity-icon-gray-bg);

            color:
                var(--activity-muted);

            font-size:
                22px;
        }


        .academic-history-empty h6 {

            margin:
                0 0 6px;

            color:
                var(--text-color);

            font-weight:
                700;
        }


        .academic-history-empty p {

            margin:
                0;

            color:
                var(--text-secondary);

            font-size:
                12px;
        }


        /* =========================================================
   ACTIVITY CARD
========================================================= */

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


        /* =========================================================
   ACTIVITY LIST
========================================================= */

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
                background-color .2s ease;
        }


        .activity-item:last-child {

            border-bottom:
                none;
        }


        .activity-item:hover {

            background:
                var(--activity-hover-bg);
        }


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
        }


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


        /* =========================================================
   CATEGORY PROGRESS
========================================================= */

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


        /* =========================================================
   DARK MODE
========================================================= */

        [data-theme="dark"] .academic-history-table thead th {

            background:
                var(--surface-secondary);
        }


        [data-theme="dark"] .academic-history-table tbody td {

            color:
                var(--text-color);
        }


        [data-theme="dark"] .text-muted {

            color:
                var(--text-secondary) !important;
        }


        /* =========================================================
   RESPONSIVE
========================================================= */

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


            .academic-monitor-header {

                padding:
                    18px;
            }


            .academic-history-header {

                padding:
                    16px 18px;
            }


            .academic-history-table {

                min-width:
                    900px;
            }


            .activity-item {

                grid-template-columns:
                    30px 42px 1fr;

                gap:
                    10px;

                padding:
                    17px;
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

        }


        @media (max-width: 576px) {

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


            .academic-monitor-icon {

                display:
                    none;
            }

        }


        /* =========================================================
   REDUCE MOTION
========================================================= */

        @media (prefers-reduced-motion: reduce) {

            * {

                transition:
                    none !important;
            }

        }

        /* Floating calendar launcher + responsive modal */
        .student-calendar-fab {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 1040;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 56px;
            padding: 0 17px;
            border: 0;
            border-radius: 999px;
            color: #fff;
            background: linear-gradient(135deg, #4f46e5, #2563eb);
            box-shadow: 0 12px 30px rgba(37, 99, 235, .32);
            cursor: pointer;
            font: inherit;
            font-size: 13px;
            font-weight: 800;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .student-calendar-fab:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 34px rgba(37, 99, 235, .4);
        }

        .student-calendar-fab>i {
            font-size: 20px;
        }

        .student-calendar-fab-count {
            display: grid;
            place-items: center;
            min-width: 23px;
            height: 23px;
            padding: 0 5px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .2);
            font-size: 11px;
        }

        .student-calendar-modal {
            position: fixed;
            inset: 0;
            z-index: 1055;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .student-calendar-modal.is-open {
            display: flex;
        }

        .student-calendar-modal-backdrop {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
            background: rgba(8, 15, 32, .66);
            backdrop-filter: blur(5px);
            cursor: default;
        }

        .student-calendar-modal-dialog {
            position: relative;
            z-index: 1;
            width: min(1120px, 100%);
            max-height: min(92vh, 1000px);
            overflow: auto;
            border-radius: 18px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, .3);
            animation: studentCalendarPop .18s ease-out;
        }

        .student-calendar-modal-dialog .student-calendar-card {
            width: 100%;
            max-width: none;
        }

        .student-calendar-modal-close {
            position: sticky;
            top: 10px;
            float: right;
            z-index: 3;
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            margin: 10px 10px -46px 0;
            border: 1px solid var(--activity-border, #e5e7eb);
            border-radius: 50%;
            color: var(--text-color, #172033);
            background: var(--activity-card-bg, #fff);
            cursor: pointer;
        }

        .student-calendar-modal .student-calendar-header {
            padding-right: 58px;
        }

        body.student-calendar-modal-open {
            overflow: hidden;
        }

        [data-theme="dark"] .student-calendar-modal-close {
            border-color: var(--activity-border, #30394b);
        }

        @keyframes studentCalendarPop {
            from {
                opacity: 0;
                transform: translateY(10px) scale(.99);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 600px) {
            .student-calendar-fab {
                right: 15px;
                bottom: 15px;
                min-height: 52px;
                padding: 0 14px;
            }

            .student-calendar-modal {
                padding: 8px;
                align-items: flex-end;
            }

            .student-calendar-modal-dialog {
                max-height: 94vh;
                border-radius: 16px 16px 10px 10px;
            }

            .student-calendar-modal .student-calendar-header {
                flex-direction: column;
                align-items: stretch;
                padding: 16px 52px 14px 14px;
            }

            .student-calendar-modal .student-calendar-controls {
                justify-content: space-between;
            }

            .student-calendar-modal .student-calendar-body {
                padding: 12px;
            }
        }

        /* =========================================================
           RESPONSIVE STUDENT ACADEMIC CALENDAR
        ========================================================= */
        .student-calendar-card {
            --sc-border: var(--activity-border, #e5e7eb);
            --sc-muted: var(--text-secondary, #64748b);
            --sc-text: var(--text-color, #172033);
            box-sizing: border-box;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            background: var(--activity-card-bg, #fff);
            color: var(--sc-text);
            border: 1px solid var(--sc-border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 18px var(--shadow-color, rgba(15, 23, 42, .06));
        }

        .student-calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--sc-border);
        }

        .student-calendar-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .student-calendar-heading-icon {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: var(--activity-indigo-bg, #eef2ff);
            color: var(--activity-indigo, #4f46e5);
            font-size: 20px;
        }

        .student-calendar-heading h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 750;
            color: var(--sc-text);
        }

        .student-calendar-heading p {
            margin: 5px 0 0;
            font-size: 12px;
            color: var(--sc-muted);
        }

        .student-calendar-controls,
        .student-calendar-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .student-calendar-controls button {
            min-height: 36px;
            border: 1px solid var(--sc-border);
            border-radius: 9px;
            background: var(--surface-secondary, #f8fafc);
            color: var(--sc-text);
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 650;
            cursor: pointer;
        }

        .student-calendar-controls button:hover {
            border-color: var(--activity-indigo, #4f46e5);
            color: var(--activity-indigo, #4f46e5);
        }

        .student-calendar-nav button {
            width: 36px;
            padding: 7px;
        }

        .student-calendar-body {
            padding: 20px 22px 22px;
        }

        .student-calendar-month-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .student-calendar-month-row h3 {
            margin: 0;
            font-size: 21px;
            font-weight: 750;
            letter-spacing: -.4px;
            color: var(--sc-text);
        }

        .student-calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            color: var(--sc-muted);
            font-size: 11px;
        }

        .student-calendar-legend span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .calendar-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .calendar-legend-dot.lecture {
            background: #3b82f6;
        }

        .calendar-legend-dot.reading {
            background: #8b5cf6;
        }

        .calendar-legend-dot.deadline {
            background: #f97316;
        }

        .calendar-legend-dot.event {
            background: #0d9488;
        }

        .calendar-legend-dot.event {
            background: #0d9488;
        }

        .student-calendar-weekdays,
        .student-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .student-calendar-weekdays {
            margin-bottom: 7px;
        }

        .student-calendar-weekdays span {
            text-align: center;
            padding: 8px 0;
            color: var(--sc-muted);
            font-size: 10px;
            font-weight: 750;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .student-calendar-day {
            position: relative;
            min-width: 0;
            min-height: 83px;
            padding: 8px 8px 7px;
            border: 1px solid var(--sc-border);
            border-right: 0;
            border-bottom: 0;
            background: transparent;
            color: var(--sc-text);
            text-align: left;
            cursor: pointer;
            transition: background .15s ease, box-shadow .15s ease;
        }

        .student-calendar-day:nth-child(7n) {
            border-right: 1px solid var(--sc-border);
        }

        .student-calendar-day:nth-last-child(-n+7) {
            border-bottom: 1px solid var(--sc-border);
        }

        .student-calendar-day:hover {
            background: var(--activity-hover-bg, rgba(99, 102, 241, .06));
        }

        .student-calendar-day.outside-month {
            color: var(--sc-muted);
            opacity: .55;
        }

        .student-calendar-day.is-today .day-number {
            background: var(--activity-indigo, #4f46e5);
            color: #fff;
        }

        .student-calendar-day.is-selected {
            z-index: 1;
            box-shadow: inset 0 0 0 2px var(--activity-indigo, #4f46e5);
            background: var(--activity-indigo-bg, #eef2ff);
        }

        .day-number {
            display: grid;
            place-items: center;
            width: 27px;
            height: 27px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }

        .day-event-list {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: 5px;
        }

        .day-event-chip {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            border-radius: 4px;
            padding: 3px 5px;
            font-size: 9px;
            font-weight: 650;
            line-height: 1.3;
        }

        .day-event-chip.lecture {
            background: rgba(59, 130, 246, .13);
            color: #2563eb;
        }

        .day-event-chip.reading {
            background: rgba(139, 92, 246, .13);
            color: #7c3aed;
        }

        .day-event-chip.deadline {
            background: rgba(249, 115, 22, .14);
            color: #c2410c;
        }

        .day-event-chip.event {
            background: rgba(13, 148, 136, .14);
            color: #0f766e;
        }

        .day-event-chip.event {
            background: rgba(13, 148, 136, .14);
            color: #0f766e;
        }

        .day-event-more {
            font-size: 9px;
            color: var(--sc-muted);
            padding-left: 3px;
        }

        .student-calendar-detail {
            margin-top: 18px;
            border: 1px solid var(--sc-border);
            border-radius: 12px;
            padding: 15px 16px;
        }

        .student-calendar-detail-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
        }

        .student-calendar-detail-head h6 {
            margin: 0;
            font-size: 13px;
            font-weight: 750;
            color: var(--sc-text);
        }

        .student-calendar-detail-count {
            color: var(--sc-muted);
            font-size: 11px;
        }

        .student-calendar-detail-empty {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--sc-muted);
            font-size: 12px;
        }

        .student-calendar-detail-empty i {
            font-size: 20px;
            color: var(--activity-indigo, #4f46e5);
        }

        .student-calendar-event {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 11px 0;
            border-top: 1px solid var(--sc-border);
        }

        .student-calendar-event:first-of-type {
            border-top: 0;
        }

        .student-calendar-event-marker {
            width: 4px;
            min-height: 42px;
            border-radius: 5px;
            flex: 0 0 4px;
            background: #3b82f6;
        }

        .student-calendar-event-marker.reading {
            background: #8b5cf6;
        }

        .student-calendar-event-marker.deadline {
            background: #f97316;
        }

        .student-calendar-event-marker.event {
            background: #0d9488;
        }

        .student-calendar-event-marker.event {
            background: #0d9488;
        }

        .student-calendar-event-main {
            min-width: 0;
            flex: 1;
        }

        .student-calendar-event-title {
            margin: 0 0 4px;
            font-size: 12px;
            font-weight: 700;
            color: var(--sc-text);
            overflow-wrap: anywhere;
        }

        .student-calendar-event-meta {
            font-size: 10px;
            color: var(--sc-muted);
        }

        .student-calendar-event-description {
            margin-top: 5px;
            font-size: 11px;
            color: var(--sc-muted);
            overflow-wrap: anywhere;
        }

        .student-calendar-event-open {
            font-size: 11px;
            font-weight: 700;
            color: var(--activity-indigo, #4f46e5);
            text-decoration: none;
            white-space: nowrap;
        }

        .student-calendar-event-open:hover {
            text-decoration: underline;
        }

        .student-learning-overview {
            display: block;
            width: 100%;
            min-width: 0;
        }

        .student-learning-overview>.activity-card {
            min-width: 0;
            margin-bottom: 0 !important;
        }

        .student-agenda-sidebar {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            min-width: 0;
            width: 100%;
        }

        .student-agenda-sidebar>.student-agenda-panel {
            height: 100%;
        }

        .student-agenda-panel {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--activity-border, #e5e7eb);
            border-radius: 15px;
            background: var(--activity-card-bg, #fff);
            color: var(--text-color, #172033);
            box-shadow: 0 5px 18px var(--shadow-color, rgba(15, 23, 42, .05));
        }

        .student-agenda-panel>header {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 12px 13px;
            border-bottom: 1px solid var(--activity-border, #e5e7eb);
        }

        .agenda-panel-icon {
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: var(--surface-secondary, #f1f5f9);
            color: var(--activity-indigo, #4f46e5);
        }

        .agenda-panel-icon.today {
            color: #d97706;
            background: rgba(245, 158, 11, .12);
        }

        .agenda-panel-icon.upcoming {
            color: #2563eb;
            background: rgba(59, 130, 246, .12);
        }

        .agenda-panel-icon.ended {
            color: #059669;
            background: rgba(16, 185, 129, .12);
        }

        .student-agenda-panel header div {
            min-width: 0;
            flex: 1;
        }

        .student-agenda-panel header h5 {
            margin: 0;
            font-size: 13px;
            font-weight: 800;
            color: var(--text-color, #172033);
        }

        .student-agenda-panel header p {
            margin: 3px 0 0;
            font-size: 10px;
            color: var(--text-secondary, #64748b);
        }

        .student-agenda-panel header>strong {
            display: grid;
            place-items: center;
            min-width: 25px;
            height: 25px;
            padding: 0 6px;
            border-radius: 8px;
            background: var(--surface-secondary, #f1f5f9);
            font-size: 11px;
            color: var(--text-color, #172033);
        }

        .student-agenda-list {
            max-height: 190px;
            overflow-y: auto;
            padding: 4px 12px 8px;
        }

        .student-agenda-item {
            display: block;
            width: 100%;
            padding: 10px 1px;
            border: 0;
            border-bottom: 1px solid var(--activity-border, #e5e7eb);
            background: transparent;
            color: inherit;
            text-align: left;
            cursor: pointer;
        }

        .student-agenda-item:last-child {
            border-bottom: 0;
        }

        .student-agenda-item:hover .agenda-item-title {
            color: var(--activity-indigo, #4f46e5);
        }

        .agenda-item-title {
            display: block;
            font-size: 12px;
            line-height: 1.4;
            font-weight: 750;
            color: var(--text-color, #172033);
            overflow-wrap: anywhere;
        }

        .agenda-item-meta {
            display: block;
            margin-top: 4px;
            font-size: 10px;
            line-height: 1.4;
            color: var(--text-secondary, #64748b);
        }

        .agenda-empty {
            margin: 0;
            padding: 14px 2px;
            font-size: 11px;
            color: var(--text-secondary, #64748b);
        }

        [data-theme="dark"] .student-agenda-panel {
            border-color: var(--activity-border, #30394b);
        }

        [data-theme="dark"] .student-agenda-item {
            border-color: var(--activity-border, #30394b);
        }

        @media (max-width:1199.98px) {
            .student-agenda-sidebar {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .student-agenda-list {
                max-height: 220px;
            }
        }

        @media (max-width:767.98px) {
            .student-agenda-sidebar {
                grid-template-columns: minmax(0, 1fr);
            }

            .student-agenda-list {
                max-height: 180px;
            }
        }

        /* Calendar and agenda share the dashboard grid. */
        .student-planner-layout {
            display: grid;
            grid-template-columns: minmax(0, 3fr) minmax(280px, 1fr);
            align-items: start;
            gap: 18px;
            width: 100%;
            min-width: 0;
        }

        .student-calendar-card {
            width: 100%;
            min-width: 0;
        }

        .student-upcoming-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid var(--activity-border, #e5e7eb);
            border-radius: 16px;
            background: var(--activity-card-bg, #fff);
            color: var(--text-color, #172033);
            box-shadow: 0 5px 18px var(--shadow-color, rgba(15, 23, 42, .06));
        }

        .student-upcoming-header {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 18px;
            border-bottom: 1px solid var(--activity-border, #e5e7eb);
        }

        .student-upcoming-icon {
            display: grid;
            place-items: center;
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--activity-indigo-bg, #eef2ff);
            color: var(--activity-indigo, #4f46e5);
            font-size: 19px;
        }

        .student-upcoming-heading {
            min-width: 0;
            flex: 1;
        }

        .student-upcoming-heading h5 {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            color: var(--text-color, #172033);
        }

        .student-upcoming-heading p {
            margin: 4px 0 0;
            font-size: 11px;
            line-height: 1.45;
            color: var(--text-secondary, #64748b);
        }

        .student-upcoming-total {
            display: grid;
            place-items: center;
            min-width: 30px;
            height: 30px;
            padding: 0 8px;
            border-radius: 10px;
            background: #315eea;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
        }

        .student-upcoming-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
            padding: 13px 15px;
            border-bottom: 1px solid var(--activity-border, #e5e7eb);
        }

        .student-upcoming-stat {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 10px;
            border-radius: 11px;
            background: var(--surface-secondary, #f8fafc);
            min-width: 0;
        }

        .student-upcoming-stat strong {
            display: block;
            color: var(--text-color, #172033);
            font-size: 17px;
            line-height: 1.1;
        }

        .student-upcoming-stat small {
            display: block;
            margin-top: 3px;
            color: var(--text-secondary, #64748b);
            font-size: 10px;
        }

        .upcoming-stat-dot {
            width: 9px;
            height: 9px;
            flex: 0 0 9px;
            border-radius: 50%;
            background: #2563eb;
        }

        .upcoming-stat-dot.today {
            background: #f97316;
        }

        .upcoming-stat-dot.soon {
            background: #10b981;
        }

        .student-upcoming-list {
            max-height: 620px;
            overflow: auto;
            padding: 5px 14px 12px;
        }

        .student-upcoming-item {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 13px 2px;
            border-bottom: 1px solid var(--activity-border, #e5e7eb);
            cursor: pointer;
        }

        .student-upcoming-item:last-child {
            border-bottom: 0;
        }

        .student-upcoming-item:hover {
            background: var(--activity-hover-bg, rgba(99, 102, 241, .06));
        }

        .student-upcoming-date {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 0 0 44px;
            min-height: 48px;
            border-radius: 10px;
            background: var(--surface-secondary, #f8fafc);
            color: var(--text-color, #172033);
        }

        .student-upcoming-date strong {
            font-size: 17px;
            line-height: 1.1;
        }

        .student-upcoming-date span {
            margin-top: 3px;
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-secondary, #64748b);
        }

        .student-upcoming-item-main {
            min-width: 0;
            flex: 1;
        }

        .student-upcoming-item-title {
            margin: 0;
            color: var(--text-color, #172033);
            font-size: 12px;
            font-weight: 800;
            line-height: 1.4;
            overflow-wrap: anywhere;
        }

        .student-upcoming-item-meta {
            margin-top: 4px;
            color: var(--text-secondary, #64748b);
            font-size: 10px;
            line-height: 1.5;
        }

        .student-upcoming-tag {
            display: inline-flex;
            align-items: center;
            margin-top: 6px;
            padding: 3px 7px;
            border-radius: 6px;
            background: rgba(59, 130, 246, .12);
            color: #2563eb;
            font-size: 9px;
            font-weight: 800;
        }

        .student-upcoming-tag.reading {
            background: rgba(139, 92, 246, .14);
            color: #7c3aed;
        }

        .student-upcoming-tag.deadline {
            background: rgba(249, 115, 22, .15);
            color: #c2410c;
        }

        .student-upcoming-tag.event {
            background: rgba(13, 148, 136, .14);
            color: #0f766e;
        }

        .student-upcoming-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 35px 18px;
            text-align: center;
            color: var(--text-secondary, #64748b);
            font-size: 12px;
        }

        .student-upcoming-empty i {
            font-size: 27px;
            color: var(--activity-indigo, #4f46e5);
        }

        /* Follow the global page theme (the portal uses data-theme="dark"). */
        [data-theme="dark"] .student-calendar-card,
        [data-theme="dark"] .student-upcoming-card {
            --sc-border: var(--activity-border, #30394b);
            --sc-muted: var(--text-secondary, #a6b0c2);
            --sc-text: var(--text-color, #edf2f7);
        }

        [data-theme="dark"] .student-calendar-day {
            color: var(--text-color, #edf2f7);
        }

        [data-theme="dark"] .student-calendar-day.is-selected {
            background: var(--activity-indigo-bg, rgba(99, 102, 241, .22));
        }

        [data-theme="dark"] .student-calendar-weekdays span {
            color: var(--text-secondary, #a6b0c2);
        }

        [data-theme="dark"] .student-upcoming-stat,
        [data-theme="dark"] .student-upcoming-date {
            background: var(--surface-secondary, #202a3a);
        }

        [data-theme="dark"] .student-upcoming-tag {
            color: #93c5fd;
            background: rgba(59, 130, 246, .2);
        }

        [data-theme="dark"] .student-upcoming-tag.reading {
            color: #c4b5fd;
            background: rgba(139, 92, 246, .2);
        }

        [data-theme="dark"] .student-upcoming-tag.deadline {
            color: #fdba74;
            background: rgba(249, 115, 22, .2);
        }

        [data-theme="dark"] .student-upcoming-tag.event {
            color: #5eead4;
            background: rgba(13, 148, 136, .2);
        }

        @media (max-width:1199.98px) {
            .student-planner-layout {
                grid-template-columns: minmax(0, 1fr);
            }

            .student-upcoming-list {
                max-height: 420px;
            }
        }

        @media (max-width:767.98px) {
            .student-calendar-header {
                align-items: flex-start;
                flex-direction: column;
                padding: 17px;
            }

            .student-calendar-controls {
                width: 100%;
                justify-content: space-between;
            }

            .student-calendar-body {
                padding: 14px;
            }

            .student-calendar-month-row {
                align-items: flex-start;
                flex-direction: column;
            }

            .student-calendar-month-row h3 {
                font-size: 19px;
            }

            .student-calendar-weekdays span {
                font-size: 9px;
            }

            .student-calendar-day {
                min-height: 65px;
                padding: 5px 3px;
            }

            .day-number {
                width: 23px;
                height: 23px;
                font-size: 11px;
            }

            .day-event-chip {
                padding: 3px 2px;
                font-size: 8px;
            }

            .day-event-chip .chip-label {
                display: none;
            }

            .day-event-more {
                font-size: 8px;
            }

            .student-calendar-detail {
                padding: 12px;
            }
        }

        @media (max-width:420px) {
            .student-calendar-legend {
                gap: 8px;
                font-size: 10px;
            }

            .student-calendar-day {
                min-height: 55px;
            }

            .day-event-chip {
                height: 5px;
                padding: 0;
                border-radius: 4px;
                font-size: 0;
            }

            .day-event-more {
                display: none;
            }

            .student-calendar-event {
                flex-wrap: wrap;
            }

            .student-calendar-event-open {
                margin-left: 15px;
            }
        }
    </style>



    <script>
        (function() {
            "use strict";
            const eventsByDate = <?= $studentCalendarEventsJson ?>;
            const monthLabel = document.getElementById("studentCalendarMonth");
            const grid = document.getElementById("studentCalendarGrid");
            const detail = document.getElementById("studentCalendarDetail");
            if (!monthLabel || !grid || !detail) return;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            let visibleMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            let selectedDate = toDateKey(today);

            function toDateKey(date) {
                return date.getFullYear() + "-" +
                    String(date.getMonth() + 1).padStart(2, "0") + "-" +
                    String(date.getDate()).padStart(2, "0");
            }

            function escapeHtml(value) {
                return String(value ?? "").replace(/[&<>"']/g, ch => ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;"
                })[ch]);
            }

            function eventClass(event) {
                if (event.dateLabel === "Due" || event.type === "Deadline") return "deadline";
                if (event.type === "Event") return "event";
                if (event.type === "Reading Activity" || event.type === "Calendar Activity") return "reading";
                return "lecture";
            }

            function renderDetail(dateKey) {
                const date = new Date(dateKey + "T12:00:00");
                const events = eventsByDate[dateKey] || [];
                const readableDate = date.toLocaleDateString(undefined, {
                    weekday: "long",
                    month: "long",
                    day: "numeric",
                    year: "numeric"
                });
                if (!events.length) {
                    detail.innerHTML = '<div class="student-calendar-detail-head"><h6>' +
                        escapeHtml(readableDate) + '</h6><span class="student-calendar-detail-count">No activities</span></div>' +
                        '<div class="student-calendar-detail-empty"><i class="bi bi-calendar-check"></i><span>No scheduled activities for this date.</span></div>';
                    return;
                }
                let html = '<div class="student-calendar-detail-head"><h6>' + escapeHtml(readableDate) +
                    '</h6><span class="student-calendar-detail-count">' + events.length +
                    (events.length === 1 ? ' activity' : ' activities') + '</span></div>';
                events.forEach(event => {
                    const cls = eventClass(event);
                    const description = event.description ?
                        '<div class="student-calendar-event-description">' + escapeHtml(event.description) + '</div>' : '';
                    const dateTime = event.dateTime ?
                        (/^\d{2}:\d{2}/.test(String(event.dateTime)) ?
                            new Date("2000-01-01T" + String(event.dateTime)).toLocaleTimeString([], {
                                hour: "numeric",
                                minute: "2-digit"
                            }) :
                            new Date(String(event.dateTime).replace(" ", "T")).toLocaleTimeString([], {
                                hour: "numeric",
                                minute: "2-digit"
                            })) :
                        "All day";
                    const location = event.location ? ' · ' + escapeHtml(event.location) : '';
                    const openLink = event.url ? '<a class="student-calendar-event-open" href="' + escapeHtml(event.url) + '">Open <i class="bi bi-arrow-up-right"></i></a>' : '';
                    html += '<article class="student-calendar-event">' +
                        '<span class="student-calendar-event-marker ' + cls + '"></span>' +
                        '<div class="student-calendar-event-main"><h6 class="student-calendar-event-title">' +
                        escapeHtml(event.dateLabel + ': ' + event.title) + '</h6>' +
                        '<div class="student-calendar-event-meta">' + escapeHtml(event.category || event.type) + ' · ' + escapeHtml(dateTime) + location + '</div>' +
                        description + '</div>' + openLink + '</article>';
                });
                detail.innerHTML = html;
            }

            function renderUpcoming() {
                const list = document.getElementById("studentUpcomingList");
                const totalEl = document.getElementById("studentUpcomingTotal");
                const todayCountEl = document.getElementById("studentTodayCount");
                const soonCountEl = document.getElementById("studentSoonCount");
                if (!list) return;
                const todayKey = toDateKey(today);
                const soonLimit = new Date(today);
                soonLimit.setDate(soonLimit.getDate() + 7);
                const soonKey = toDateKey(soonLimit);
                const items = [];
                Object.keys(eventsByDate).forEach(dateKey => {
                    if (dateKey < todayKey) return;
                    (eventsByDate[dateKey] || []).forEach(event => {
                        items.push({
                            ...event,
                            scheduleDate: dateKey
                        });
                    });
                });
                items.sort((a, b) => {
                    const dateCompare = a.scheduleDate.localeCompare(b.scheduleDate);
                    if (dateCompare !== 0) return dateCompare;
                    const timeA = String(a.dateTime || "");
                    const timeB = String(b.dateTime || "");
                    return timeA.localeCompare(timeB) || String(a.title).localeCompare(String(b.title));
                });
                // Remove duplicate instances of the same source entry/date while preserving separate due dates.
                const seen = new Set();
                const uniqueItems = items.filter(item => {
                    const key = [item.type, item.id, item.dateLabel, item.scheduleDate, item.title].join("|");
                    if (seen.has(key)) return false;
                    seen.add(key);
                    return true;
                });
                const todayCount = uniqueItems.filter(item => item.scheduleDate === todayKey).length;
                const soonCount = uniqueItems.filter(item => item.scheduleDate >= todayKey && item.scheduleDate <= soonKey).length;
                if (totalEl) totalEl.textContent = String(uniqueItems.length);
                if (todayCountEl) todayCountEl.textContent = String(todayCount);
                if (soonCountEl) soonCountEl.textContent = String(soonCount);
                if (!uniqueItems.length) {
                    list.innerHTML = '<div class="student-upcoming-empty"><i class="bi bi-calendar-check"></i><span>You are all caught up. No upcoming activities are scheduled.</span></div>';
                    return;
                }
                list.innerHTML = uniqueItems.slice(0, 40).map(item => {
                    const date = new Date(item.scheduleDate + "T12:00:00");
                    const cls = eventClass(item);
                    const time = item.dateTime && /^\d{2}:\d{2}/.test(String(item.dateTime)) ?
                        new Date("2000-01-01T" + String(item.dateTime)).toLocaleTimeString([], {
                            hour: "numeric",
                            minute: "2-digit"
                        }) :
                        "All day";
                    const dayLabel = item.scheduleDate === todayKey ? "Today" : date.toLocaleDateString(undefined, {
                        weekday: "short"
                    });
                    const meta = date.toLocaleDateString(undefined, {
                        month: "short",
                        day: "numeric"
                    }) + " · " + time + (item.location ? " · " + item.location : "");
                    const url = item.url || "";
                    return '<article class="student-upcoming-item" data-date="' + escapeHtml(item.scheduleDate) + '" tabindex="0" role="button" aria-label="Show ' + escapeHtml(item.title) + ' on calendar">' +
                        '<div class="student-upcoming-date"><strong>' + date.getDate() + '</strong><span>' + escapeHtml(dayLabel) + '</span></div>' +
                        '<div class="student-upcoming-item-main"><h6 class="student-upcoming-item-title">' + escapeHtml(item.title) + '</h6>' +
                        '<div class="student-upcoming-item-meta">' + escapeHtml(meta) + '</div>' +
                        '<span class="student-upcoming-tag ' + cls + '">' + escapeHtml(item.dateLabel || item.type) + '</span></div></article>';
                }).join("");
                list.querySelectorAll(".student-upcoming-item").forEach(itemEl => {
                    const selectUpcomingDate = () => {
                        selectedDate = itemEl.dataset.date;
                        const chosen = new Date(selectedDate + "T12:00:00");
                        visibleMonth = new Date(chosen.getFullYear(), chosen.getMonth(), 1);
                        renderCalendar();
                        renderDetail(selectedDate);
                    };
                    itemEl.addEventListener("click", selectUpcomingDate);
                    itemEl.addEventListener("keydown", e => {
                        if (e.key === "Enter" || e.key === " ") {
                            e.preventDefault();
                            selectUpcomingDate();
                        }
                    });
                });
            }

            function renderActivitySidebar() {
                const todayKey = toDateKey(today);
                const todayItems = [];
                const futureItems = [];
                const pastItems = [];
                const recentLimit = new Date(today);
                recentLimit.setDate(recentLimit.getDate() - 30);
                const recentLimitKey = toDateKey(recentLimit);
                Object.keys(eventsByDate).sort().forEach(dateKey => {
                    (eventsByDate[dateKey] || []).forEach(event => {
                        const item = {
                            ...event,
                            scheduleDate: dateKey
                        };
                        if (dateKey === todayKey) todayItems.push(item);
                        else if (dateKey > todayKey) futureItems.push(item);
                        else if (dateKey >= recentLimitKey) pastItems.push(item);
                    });
                });
                const sorter = (a, b) => a.scheduleDate.localeCompare(b.scheduleDate) || String(a.dateTime || '').localeCompare(String(b.dateTime || '')) || String(a.title || '').localeCompare(String(b.title || ''));
                todayItems.sort(sorter);
                futureItems.sort(sorter);
                pastItems.sort((a, b) => sorter(b, a));
                const configs = [{
                        items: todayItems,
                        list: 'agendaTodayList',
                        count: 'agendaTodayTotal',
                        empty: 'No activities scheduled for today.'
                    },
                    {
                        items: futureItems,
                        list: 'agendaUpcomingList',
                        count: 'agendaUpcomingTotal',
                        empty: 'Nothing upcoming is scheduled.'
                    },
                    {
                        items: pastItems,
                        list: 'agendaEndedList',
                        count: 'agendaEndedTotal',
                        empty: 'No previous activities or events found.'
                    }
                ];
                configs.forEach(config => {
                    const list = document.getElementById(config.list);
                    const count = document.getElementById(config.count);
                    if (count) count.textContent = String(config.items.length);
                    if (!list) return;
                    if (!config.items.length) {
                        list.innerHTML = '<p class="agenda-empty">' + escapeHtml(config.empty) + '</p>';
                        return;
                    }
                    list.innerHTML = config.items.slice(0, 8).map(item => {
                        const date = new Date(item.scheduleDate + 'T12:00:00');
                        const dateText = item.scheduleDate === todayKey ? 'Today' : date.toLocaleDateString(undefined, {
                            month: 'short',
                            day: 'numeric',
                            year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined
                        });
                        const time = item.dateTime && /^\d{2}:\d{2}/.test(String(item.dateTime)) ? new Date('2000-01-01T' + String(item.dateTime)).toLocaleTimeString([], {
                            hour: 'numeric',
                            minute: '2-digit'
                        }) : 'All day';
                        return '<button type="button" class="student-agenda-item" data-agenda-date="' + escapeHtml(item.scheduleDate) + '"><span class="agenda-item-title">' + escapeHtml(item.title || 'Untitled activity') + '</span><span class="agenda-item-meta">' + escapeHtml(dateText + ' · ' + time + ' · ' + (item.dateLabel || item.type || 'Activity')) + '</span></button>';
                    }).join('');
                    list.querySelectorAll('[data-agenda-date]').forEach(button => button.addEventListener('click', () => {
                        selectedDate = button.dataset.agendaDate;
                        const chosen = new Date(selectedDate + 'T12:00:00');
                        visibleMonth = new Date(chosen.getFullYear(), chosen.getMonth(), 1);
                        renderCalendar();
                        renderDetail(selectedDate);
                        document.querySelector('.student-calendar-card')?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }));
                });
            }

            function renderCalendar() {
                const year = visibleMonth.getFullYear();
                const month = visibleMonth.getMonth();
                monthLabel.textContent = visibleMonth.toLocaleDateString(undefined, {
                    month: "long",
                    year: "numeric"
                });
                grid.innerHTML = "";
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const previousMonthDays = new Date(year, month, 0).getDate();
                const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;

                for (let i = 0; i < totalCells; i++) {
                    let date;
                    let outside = false;
                    if (i < firstDay) {
                        date = new Date(year, month - 1, previousMonthDays - firstDay + i + 1);
                        outside = true;
                    } else if (i >= firstDay + daysInMonth) {
                        date = new Date(year, month + 1, i - firstDay - daysInMonth + 1);
                        outside = true;
                    } else {
                        date = new Date(year, month, i - firstDay + 1);
                    }
                    const key = toDateKey(date);
                    const dayEvents = eventsByDate[key] || [];
                    const button = document.createElement("button");
                    button.type = "button";
                    button.className = "student-calendar-day" +
                        (outside ? " outside-month" : "") +
                        (key === toDateKey(today) ? " is-today" : "") +
                        (key === selectedDate ? " is-selected" : "");
                    button.setAttribute("role", "gridcell");
                    button.setAttribute("aria-label", date.toLocaleDateString(undefined, {
                        weekday: "long",
                        month: "long",
                        day: "numeric",
                        year: "numeric"
                    }) + (dayEvents.length ? ", " + dayEvents.length + " activities" : ""));
                    let chips = "";
                    dayEvents.slice(0, 2).forEach(event => {
                        chips += '<span class="day-event-chip ' + eventClass(event) + '">' +
                            escapeHtml(event.dateLabel + ": " + event.title) + '</span>';
                    });
                    if (dayEvents.length > 2) chips += '<span class="day-event-more">+' + (dayEvents.length - 2) + ' more</span>';
                    button.innerHTML = '<span class="day-number">' + date.getDate() + '</span>' +
                        '<span class="day-event-list">' + chips + '</span>';
                    button.addEventListener("click", function() {
                        selectedDate = key;
                        if (outside) visibleMonth = new Date(date.getFullYear(), date.getMonth(), 1);
                        renderCalendar();
                        renderDetail(selectedDate);
                    });
                    grid.appendChild(button);
                }
            }
            document.getElementById("studentCalendarPrev")?.addEventListener("click", () => {
                visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() - 1, 1);
                renderCalendar();
            });
            document.getElementById("studentCalendarNext")?.addEventListener("click", () => {
                visibleMonth = new Date(visibleMonth.getFullYear(), visibleMonth.getMonth() + 1, 1);
                renderCalendar();
            });
            document.getElementById("studentCalendarToday")?.addEventListener("click", () => {
                visibleMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                selectedDate = toDateKey(today);
                renderCalendar();
                renderDetail(selectedDate);
            });
            const calendarModal = document.getElementById("studentCalendarModal");
            const calendarOpenButton = document.getElementById("studentCalendarOpen");
            const calendarCloseButton = document.getElementById("studentCalendarClose");
            const calendarBackdrop = document.getElementById("studentCalendarBackdrop");
            let calendarPreviousFocus = null;
            const openCalendarModal = () => {
                calendarPreviousFocus = document.activeElement;
                calendarModal?.classList.add("is-open");
                calendarModal?.setAttribute("aria-hidden", "false");
                document.body.classList.add("student-calendar-modal-open");
                calendarCloseButton?.focus();
            };
            const closeCalendarModal = () => {
                calendarModal?.classList.remove("is-open");
                calendarModal?.setAttribute("aria-hidden", "true");
                document.body.classList.remove("student-calendar-modal-open");
                calendarPreviousFocus?.focus?.();
            };
            calendarOpenButton?.addEventListener("click", openCalendarModal);
            calendarCloseButton?.addEventListener("click", closeCalendarModal);
            calendarBackdrop?.addEventListener("click", closeCalendarModal);
            document.addEventListener("keydown", event => {
                if (event.key === "Escape" && calendarModal?.classList.contains("is-open")) closeCalendarModal();
            });
            const calendarFabCount = document.getElementById("studentCalendarFabCount");
            if (calendarFabCount) {
                const totalScheduled = Object.values(eventsByDate).reduce((sum, items) => sum + (Array.isArray(items) ? items.length : 0), 0);
                calendarFabCount.textContent = String(totalScheduled);
            }

            renderCalendar();
            renderDetail(selectedDate);
            renderUpcoming();
            renderActivitySidebar();
        })();
    </script>

</body>