<?php

/* =========================================================
   ACADEMIC POST ACCESS LOG
   ETS-Async Learning Portal

   ---------------------------------------------------------
   PURPOSE:
   - Monitor student access to Academic Posts
   - Record every access session
   - Display student name
   - Filter by:
       • Student
       • Department
       • Year & Section
       • Academic Post
       • Status
       • Date range

   ---------------------------------------------------------
   IMPORTANT:
   This version DOES NOT modify the database.

   Collation mismatches are handled directly inside the
   SQL queries using explicit COLLATE clauses.
========================================================= */


/* =========================================================
   ADMIN AUTHENTICATION
========================================================== */

include './globals/checks.php';


/* =========================================================
   CURRENT ADMIN USER
========================================================== */

$user = $_SESSION["user"];

$adminId = $user["id"] ?? null;


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   FILTER VALUES
========================================================== */

$search = trim(
    $_GET["search"] ?? ""
);

$department = trim(
    $_GET["department"] ?? ""
);

$yearSection = trim(
    $_GET["year_section"] ?? ""
);

$postId = trim(
    $_GET["post_id"] ?? ""
);

$status = trim(
    $_GET["status"] ?? ""
);

$dateFrom = trim(
    $_GET["date_from"] ?? ""
);

$dateTo = trim(
    $_GET["date_to"] ?? ""
);


/* =========================================================
   PAGINATION
========================================================== */

$recordsPerPage = 25;

$page = isset($_GET["page"])
    ? max(
        1,
        (int)$_GET["page"]
    )
    : 1;

$offset =
    ($page - 1) *
    $recordsPerPage;


/* =========================================================
   DATABASE COLLATION
   ---------------------------------------------------------
   We DO NOT change the database.

   utf8mb4_general_ci is explicitly used when comparing
   text values.

   This prevents:

   utf8mb4_general_ci
   vs
   utf8mb4_unicode_ci

   conflicts.
========================================================= */

$comparisonCollation =
    "utf8mb4_general_ci";


/* =========================================================
   LOAD DEPARTMENTS
========================================================== */

$departments = [];

$sql = "
    SELECT DISTINCT
        department
    FROM accounts
    WHERE access = 'student'
      AND department IS NOT NULL
      AND TRIM(department) <> ''
    ORDER BY department ASC
";

$result =
    $mysqli->query($sql);

if ($result) {

    while (
        $row =
        $result->fetch_assoc()
    ) {

        $departments[] =
            $row["department"];
    }
}


/* =========================================================
   LOAD YEAR / SECTION
========================================================== */

$yearSections = [];

$sql = "
    SELECT DISTINCT
        year_section
    FROM accounts
    WHERE access = 'student'
      AND year_section IS NOT NULL
      AND TRIM(year_section) <> ''
    ORDER BY year_section ASC
";

$result =
    $mysqli->query($sql);

if ($result) {

    while (
        $row =
        $result->fetch_assoc()
    ) {

        $yearSections[] =
            $row["year_section"];
    }
}


/* =========================================================
   LOAD ACADEMIC POSTS
========================================================== */

$academicPosts = [];

$sql = "
    SELECT
        id,
        title,
        subject
    FROM academic_posts
    ORDER BY title ASC
";

$result =
    $mysqli->query($sql);

if ($result) {

    while (
        $row =
        $result->fetch_assoc()
    ) {

        $academicPosts[] =
            $row;
    }
}


/* =========================================================
   BUILD FILTER CONDITIONS
========================================================== */

$where = [];

$params = [];

$types = "";


/* =========================================================
   STUDENT SEARCH
   ---------------------------------------------------------
   Searches:
   - First name
   - Last name
   - Student ID
   - Combined name
========================================================== */

if ($search !== "") {

    $where[] = "
        (
            a.first_name COLLATE $comparisonCollation
                LIKE ?

            OR

            a.last_name COLLATE $comparisonCollation
                LIKE ?

            OR

            a.student_id COLLATE $comparisonCollation
                LIKE ?

            OR

            CONCAT(
                a.last_name,
                ', ',
                a.first_name
            ) COLLATE $comparisonCollation
                LIKE ?
        )
    ";

    $searchValue =
        "%" .
        $search .
        "%";

    $params[] =
        $searchValue;

    $params[] =
        $searchValue;

    $params[] =
        $searchValue;

    $params[] =
        $searchValue;

    $types .= "ssss";
}


/* =========================================================
   DEPARTMENT FILTER
========================================================== */

if ($department !== "") {

    $where[] = "
        a.department COLLATE $comparisonCollation
            = ?
    ";

    $params[] =
        $department;

    $types .= "s";
}


/* =========================================================
   YEAR / SECTION FILTER
========================================================== */

if ($yearSection !== "") {

    $where[] = "
        a.year_section COLLATE $comparisonCollation
            = ?
    ";

    $params[] =
        $yearSection;

    $types .= "s";
}


/* =========================================================
   ACADEMIC POST FILTER
========================================================== */

if ($postId !== "") {

    $where[] = "
        l.post_id = ?
    ";

    $params[] =
        (int)$postId;

    $types .= "i";
}


/* =========================================================
   STATUS FILTER
========================================================== */

if (
    $status === "open" ||
    $status === "closed"
) {

    $where[] = "
        l.status COLLATE $comparisonCollation
            = ?
    ";

    $params[] =
        $status;

    $types .= "s";
}


/* =========================================================
   DATE FROM
========================================================== */

if ($dateFrom !== "") {

    $where[] = "
        DATE(l.opened_at) >= ?
    ";

    $params[] =
        $dateFrom;

    $types .= "s";
}


/* =========================================================
   DATE TO
========================================================== */

if ($dateTo !== "") {

    $where[] = "
        DATE(l.opened_at) <= ?
    ";

    $params[] =
        $dateTo;

    $types .= "s";
}


/* =========================================================
   WHERE CLAUSE
========================================================== */

$whereSql = "";

if (!empty($where)) {

    $whereSql =
        " WHERE " .
        implode(
            " AND ",
            $where
        );
}


/* =========================================================
   COUNT FILTERED RECORDS
========================================================== */

$countSql = "

    SELECT
        COUNT(*) AS total

    FROM academic_post_access_logs l

    INNER JOIN accounts a

        ON
            a.student_id COLLATE $comparisonCollation
            =
            l.student_id COLLATE $comparisonCollation

    INNER JOIN academic_posts p

        ON p.id = l.post_id

    $whereSql

";


$countStmt =
    $mysqli->prepare(
        $countSql
    );


$totalRecords = 0;


if ($countStmt) {

    if (!empty($params)) {

        $countStmt->bind_param(
            $types,
            ...$params
        );
    }

    $countStmt->execute();

    $countResult =
        $countStmt->get_result();

    if (
        $countRow =
        $countResult->fetch_assoc()
    ) {

        $totalRecords =
            (int)$countRow["total"];
    }

    $countStmt->close();
}


/* =========================================================
   TOTAL PAGES
========================================================== */

$totalPages =
    max(
        1,
        (int)ceil(
            $totalRecords /
                $recordsPerPage
        )
    );


/* =========================================================
   CORRECT INVALID PAGE
========================================================== */

if (
    $page >
    $totalPages
) {

    $page =
        $totalPages;

    $offset =
        ($page - 1) *
        $recordsPerPage;
}


/* =========================================================
   LOAD ACCESS RECORDS
========================================================== */

$records = [];


$dataSql = "

    SELECT

        l.id,

        l.post_id,

        l.student_id,

        l.opened_at,

        l.closed_at,

        l.duration_seconds,

        l.status,

        p.title AS post_title,

        p.subject AS post_subject,

        a.first_name,

        a.last_name,

        a.middle_initial,

        a.extension_name,

        a.department,

        a.year_section,


        /* =============================================
           ACCESS NUMBER
           ============================================= */

        (

            SELECT COUNT(*)

            FROM academic_post_access_logs l2

            WHERE

                l2.post_id =
                l.post_id

                AND

                l2.student_id COLLATE $comparisonCollation
                =
                l.student_id COLLATE $comparisonCollation

                AND

                l2.opened_at <=
                l.opened_at

        ) AS access_number,


        /* =============================================
           TOTAL ACCESS COUNT
           ============================================= */

        (

            SELECT COUNT(*)

            FROM academic_post_access_logs l3

            WHERE

                l3.post_id =
                l.post_id

                AND

                l3.student_id COLLATE $comparisonCollation
                =
                l.student_id COLLATE $comparisonCollation

        ) AS total_accesses,


        /* =============================================
           TOTAL DURATION FOR THIS STUDENT + POST
           ============================================= */

        (

            SELECT

                COALESCE(
                    SUM(
                        COALESCE(
                            l4.duration_seconds,
                            0
                        )
                    ),
                    0
                )

            FROM academic_post_access_logs l4

            WHERE

                l4.post_id =
                l.post_id

                AND

                l4.student_id COLLATE $comparisonCollation
                =
                l.student_id COLLATE $comparisonCollation

        ) AS total_duration


    FROM academic_post_access_logs l


    /* =============================================
       STUDENT JOIN

       Explicit COLLATE prevents:

       utf8mb4_general_ci
       vs
       utf8mb4_unicode_ci
       ============================================= */

    INNER JOIN accounts a

        ON

            a.student_id COLLATE $comparisonCollation

            =

            l.student_id COLLATE $comparisonCollation


    /* =============================================
       ACADEMIC POST
       ============================================= */

    INNER JOIN academic_posts p

        ON p.id = l.post_id


    $whereSql


    ORDER BY

        l.opened_at DESC


    LIMIT ?, ?

";


/* =========================================================
   PAGINATION PARAMETERS
========================================================== */

$dataParams =
    $params;

$dataTypes =
    $types . "ii";

$dataParams[] =
    $offset;

$dataParams[] =
    $recordsPerPage;


/* =========================================================
   PREPARE DATA QUERY
========================================================== */

$dataStmt =
    $mysqli->prepare(
        $dataSql
    );


if ($dataStmt) {

    $dataStmt->bind_param(
        $dataTypes,
        ...$dataParams
    );

    $dataStmt->execute();

    $dataResult =
        $dataStmt->get_result();


    while (
        $row =
        $dataResult->fetch_assoc()
    ) {

        $records[] =
            $row;
    }

    $dataStmt->close();
}


/* =========================================================
   TOTAL ACCESS SESSIONS
========================================================== */

$totalSessions = 0;

$sql = "

    SELECT
        COUNT(*) AS total

    FROM academic_post_access_logs

";

$result =
    $mysqli->query($sql);

if ($result) {

    $row =
        $result->fetch_assoc();

    $totalSessions =
        (int)$row["total"];
}


/* =========================================================
   UNIQUE STUDENTS
========================================================== */

$totalStudents = 0;

$sql = "

    SELECT
        COUNT(
            DISTINCT
            student_id COLLATE $comparisonCollation
        ) AS total

    FROM academic_post_access_logs

    WHERE student_id IS NOT NULL

      AND TRIM(student_id) <> ''

";

$result =
    $mysqli->query($sql);

if ($result) {

    $row =
        $result->fetch_assoc();

    $totalStudents =
        (int)$row["total"];
}


/* =========================================================
   CURRENTLY OPEN
========================================================== */

$totalOpen = 0;

$sql = "

    SELECT
        COUNT(*) AS total

    FROM academic_post_access_logs

    WHERE status = 'open'

";

$result =
    $mysqli->query($sql);

if ($result) {

    $row =
        $result->fetch_assoc();

    $totalOpen =
        (int)$row["total"];
}


/* =========================================================
   COMPLETED
========================================================== */

$totalClosed = 0;

$sql = "

    SELECT
        COUNT(*) AS total

    FROM academic_post_access_logs

    WHERE status = 'closed'

";

$result =
    $mysqli->query($sql);

if ($result) {

    $row =
        $result->fetch_assoc();

    $totalClosed =
        (int)$row["total"];
}


/* =========================================================
   FORMAT DURATION
========================================================== */

function formatDuration(
    $seconds
) {

    $seconds =
        max(
            0,
            (int)$seconds
        );


    $hours =
        floor(
            $seconds / 3600
        );


    $minutes =
        floor(
            ($seconds % 3600) / 60
        );


    $remainingSeconds =
        $seconds % 60;


    if ($hours > 0) {

        return sprintf(
            "%dh %02dm %02ds",
            $hours,
            $minutes,
            $remainingSeconds
        );
    }


    if ($minutes > 0) {

        return sprintf(
            "%dm %02ds",
            $minutes,
            $remainingSeconds
        );
    }


    return sprintf(
        "%ds",
        $remainingSeconds
    );
}


/* =========================================================
   FORMAT STUDENT NAME
   ---------------------------------------------------------
   OUTPUT:

   LAST NAME, First Name M.I. Extension
========================================================== */

function formatStudentName(
    $row
) {

    $lastName =
        trim(
            $row["last_name"] ?? ""
        );


    $firstName =
        trim(
            $row["first_name"] ?? ""
        );


    $middleInitial =
        trim(
            $row["middle_initial"] ?? ""
        );


    $extension =
        trim(
            $row["extension_name"] ?? ""
        );


    /* =====================================================
       MIDDLE INITIAL
    ====================================================== */

    $middle = "";


    if (
        $middleInitial !== ""
    ) {

        $middle =
            " " .
            rtrim(
                $middleInitial,
                "."
            ) .
            ".";
    }


    /* =====================================================
       EXTENSION
    ====================================================== */

    $ext = "";


    if (
        $extension !== ""
    ) {

        $ext =
            " " .
            $extension;
    }


    /* =====================================================
       FINAL NAME
    ====================================================== */

    return

        strtoupper(
            $lastName
        )

        .

        ", "

        .

        $firstName

        .

        $middle

        .

        $ext;
}


/* =========================================================
   PAGINATION QUERY
========================================================== */

$queryParameters =
    $_GET;


unset(
    $queryParameters["page"]
);


$paginationQuery =
    http_build_query(
        $queryParameters
    );

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php include 'globals/head.php'; ?>


    <style>
        /* =================================================
           PAGE HEADER
        ================================================== */

        .page-header {

            margin-bottom: 1.5rem;

        }


        .page-header h1 {

            font-weight: 700;

            margin-bottom: .35rem;

        }


        .page-header p {

            margin-bottom: 0;

            color: #6c757d;

        }


        /* =================================================
           STAT CARDS
        ================================================== */

        .stat-card {

            border: 0;

            border-radius: 14px;

            box-shadow:
                0 4px 18px rgba(0,
                    0,
                    0,
                    .06);

        }


        .stat-icon {

            width: 46px;

            height: 46px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 1.25rem;

        }


        .stat-number {

            font-size: 1.5rem;

            font-weight: 700;

            line-height: 1.1;

        }


        .stat-label {

            color: #6c757d;

            font-size: .85rem;

        }


        /* =================================================
           FILTER CARD
        ================================================== */

        .filter-card {

            border: 0;

            border-radius: 14px;

            box-shadow:
                0 4px 18px rgba(0,
                    0,
                    0,
                    .06);

        }


        .filter-card-header {

            background: #f8f9fa;

            border-bottom:
                1px solid #dee2e6;

            padding: 1rem 1.25rem;

        }


        .filter-card-header h5 {

            margin: 0;

            font-weight: 700;

        }


        /* =================================================
           MONITOR CARD
        ================================================== */

        .monitor-card {

            border: 0;

            border-radius: 14px;

            box-shadow:
                0 4px 18px rgba(0,
                    0,
                    0,
                    .06);

            overflow: hidden;

        }


        /* =================================================
           TABLE
        ================================================== */

        .monitor-table {

            margin-bottom: 0;

        }


        .monitor-table thead th {

            background: #f8f9fa;

            font-size: .78rem;

            text-transform: uppercase;

            letter-spacing: .03em;

            white-space: nowrap;

            vertical-align: middle;

        }


        .monitor-table tbody td {

            vertical-align: middle;

            font-size: .9rem;

        }


        /* =================================================
           STUDENT
        ================================================== */

        .student-name {

            font-weight: 600;

        }


        .student-id {

            font-size: .78rem;

            color: #6c757d;

        }


        /* =================================================
           POST
        ================================================== */

        .post-title {

            font-weight: 600;

        }


        .post-subject {

            font-size: .78rem;

            color: #6c757d;

        }


        /* =================================================
           ACCESS NUMBER
        ================================================== */

        .access-number {

            display: inline-flex;

            width: 30px;

            height: 30px;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background: rgba(13,
                    110,
                    253,
                    .1);

            color: #0d6efd;

            font-weight: 700;

        }


        /* =================================================
           STATUS
        ================================================== */

        .status-badge {

            font-size: .75rem;

            font-weight: 600;

            padding:
                .4rem .65rem;

            border-radius:
                50rem;

        }


        .status-open {

            background:
                rgba(25,
                    135,
                    84,
                    .12);

            color:
                #198754;

        }


        .status-closed {

            background:
                rgba(108,
                    117,
                    125,
                    .12);

            color:
                #6c757d;

        }


        /* =================================================
           TIME
        ================================================== */

        .time-value {

            white-space: nowrap;

            font-size: .84rem;

        }


        /* =================================================
           EMPTY STATE
        ================================================== */

        .empty-state {

            padding: 4rem 1rem;

            text-align: center;

        }


        .empty-state i {

            font-size: 3rem;

            color: #adb5bd;

        }


        .empty-state h5 {

            margin-top: 1rem;

            font-weight: 600;

        }


        /* =================================================
           DARK MODE
        ================================================== */

        html[data-theme="dark"] .page-header p {

            color: #adb5bd;

        }


        html[data-theme="dark"] .stat-card,

        html[data-theme="dark"] .filter-card,

        html[data-theme="dark"] .monitor-card {

            background: #1e1e1e;

            box-shadow:
                0 4px 18px rgba(0,
                    0,
                    0,
                    .25);

        }


        html[data-theme="dark"] .stat-label {

            color: #adb5bd;

        }


        html[data-theme="dark"] .filter-card-header,

        html[data-theme="dark"] .monitor-table thead th {

            background: #252525;

            border-color: #3a3a3a;

        }


        html[data-theme="dark"] .table {

            --bs-table-bg: #1e1e1e;

            --bs-table-color: #f8f9fa;

        }


        html[data-theme="dark"] .table> :not(caption)>*>* {

            border-color: #3a3a3a;

        }


        html[data-theme="dark"] .form-control,

        html[data-theme="dark"] .form-select {

            background-color: #2b2b2b;

            border-color: #444;

            color: #f8f9fa;

        }


        html[data-theme="dark"] .form-control::placeholder {

            color: #888;

        }


        html[data-theme="dark"] .form-control:focus,

        html[data-theme="dark"] .form-select:focus {

            background-color: #2b2b2b;

            color: #fff;

            border-color: #0d6efd;

        }


        html[data-theme="dark"] .student-id,

        html[data-theme="dark"] .post-subject {

            color: #adb5bd;

        }


        /* =================================================
           MOBILE
        ================================================== */

        @media (max-width: 767.98px) {

            .page-header {

                margin-bottom: 1rem;

            }


            .stat-number {

                font-size: 1.25rem;

            }

        }
    </style>

</head>


<body>


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <?php include 'globals/sidebar.php'; ?>


    <!-- =====================================================
         TOPBAR
    ====================================================== -->

    <?php include 'globals/topbar.php'; ?>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">

        <div class="container-fluid py-4">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="page-header">

                <div
                    class="
                        d-flex
                        flex-wrap
                        justify-content-between
                        align-items-center
                        gap-3
                    ">


                    <div>

                        <h1 class="h3">

                            <i
                                class="bi bi-activity me-2">
                            </i>

                            Academic Post Monitor

                        </h1>


                        <p>

                            Monitor student access,
                            reading sessions,
                            and time spent on
                            Academic Posts.

                        </p>

                    </div>


                    <div>

                        <a
                            href="academic_posts.php"
                            class="
                                btn
                                btn-outline-primary
                            ">

                            <i
                                class="
                                    bi
                                    bi-file-earmark-text
                                    me-1
                                ">
                            </i>

                            Academic Posts

                        </a>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 STATISTICS
            ================================================== -->

            <div class="row g-3 mb-4">


                <!-- TOTAL ACCESS -->

                <div class="col-6 col-xl-3">

                    <div
                        class="
                            card
                            stat-card
                            h-100
                        ">

                        <div class="card-body">

                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    gap-3
                                ">

                                <div
                                    class="stat-icon"
                                    style="
                                        background:
                                        rgba(
                                            13,
                                            110,
                                            253,
                                            .12
                                        );

                                        color:
                                        #0d6efd;
                                    ">

                                    <i
                                        class="
                                            bi
                                            bi-bar-chart-line
                                        ">
                                    </i>

                                </div>


                                <div>

                                    <div
                                        class="stat-number">

                                        <?= number_format(
                                            $totalSessions
                                        ) ?>

                                    </div>


                                    <div
                                        class="stat-label">

                                        Total Accesses

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- UNIQUE STUDENTS -->

                <div class="col-6 col-xl-3">

                    <div
                        class="
                            card
                            stat-card
                            h-100
                        ">

                        <div class="card-body">

                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    gap-3
                                ">

                                <div
                                    class="stat-icon"
                                    style="
                                        background:
                                        rgba(
                                            111,
                                            66,
                                            193,
                                            .12
                                        );

                                        color:
                                        #6f42c1;
                                    ">

                                    <i
                                        class="
                                            bi
                                            bi-people
                                        ">
                                    </i>

                                </div>


                                <div>

                                    <div
                                        class="stat-number">

                                        <?= number_format(
                                            $totalStudents
                                        ) ?>

                                    </div>


                                    <div
                                        class="stat-label">

                                        Students

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- OPEN -->

                <div class="col-6 col-xl-3">

                    <div
                        class="
                            card
                            stat-card
                            h-100
                        ">

                        <div class="card-body">

                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    gap-3
                                ">

                                <div
                                    class="stat-icon"
                                    style="
                                        background:
                                        rgba(
                                            25,
                                            135,
                                            84,
                                            .12
                                        );

                                        color:
                                        #198754;
                                    ">

                                    <i
                                        class="
                                            bi
                                            bi-eye
                                        ">
                                    </i>

                                </div>


                                <div>

                                    <div
                                        class="stat-number">

                                        <?= number_format(
                                            $totalOpen
                                        ) ?>

                                    </div>


                                    <div
                                        class="stat-label">

                                        Currently Open

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- CLOSED -->

                <div class="col-6 col-xl-3">

                    <div
                        class="
                            card
                            stat-card
                            h-100
                        ">

                        <div class="card-body">

                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    gap-3
                                ">

                                <div
                                    class="stat-icon"
                                    style="
                                        background:
                                        rgba(
                                            108,
                                            117,
                                            125,
                                            .12
                                        );

                                        color:
                                        #6c757d;
                                    ">

                                    <i
                                        class="
                                            bi
                                            bi-check2-circle
                                        ">
                                    </i>

                                </div>


                                <div>

                                    <div
                                        class="stat-number">

                                        <?= number_format(
                                            $totalClosed
                                        ) ?>

                                    </div>


                                    <div
                                        class="stat-label">

                                        Completed Sessions

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


            </div>


            <!-- =================================================
                 FILTER CARD
            ================================================== -->

            <div
                class="
                    card
                    filter-card
                    mb-4
                ">


                <div
                    class="
                        filter-card-header
                    ">

                    <h5>

                        <i
                            class="
                                bi
                                bi-funnel
                                me-2
                            ">
                        </i>

                        Filter Student Activity

                    </h5>

                </div>


                <div class="card-body">


                    <form
                        method="GET"
                        action="">


                        <div class="row g-3">


                            <!-- STUDENT SEARCH -->

                            <div
                                class="
                                    col-12
                                    col-lg-4
                                ">

                                <label
                                    for="search"
                                    class="form-label">

                                    Student

                                </label>


                                <div
                                    class="input-group">

                                    <span
                                        class="
                                            input-group-text
                                        ">

                                        <i
                                            class="
                                                bi
                                                bi-search
                                            ">
                                        </i>

                                    </span>


                                    <input
                                        type="text"
                                        class="form-control"
                                        id="search"
                                        name="search"
                                        value="<?= htmlspecialchars(
                                                    $search,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>"
                                        placeholder="
                                            Name or Student ID
                                        ">

                                </div>

                            </div>


                            <!-- DEPARTMENT -->

                            <div
                                class="
                                    col-12
                                    col-md-6
                                    col-lg-2
                                ">

                                <label
                                    for="department"
                                    class="form-label">

                                    Department

                                </label>


                                <select
                                    class="form-select"
                                    id="department"
                                    name="department">


                                    <option value="">

                                        All Departments

                                    </option>


                                    <?php foreach (
                                        $departments
                                        as $item
                                    ): ?>

                                        <option
                                            value="<?= htmlspecialchars(
                                                        $item,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>"
                                            <?= $department === $item
                                                ? "selected"
                                                : "" ?>>

                                            <?= htmlspecialchars(
                                                $item,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </option>

                                    <?php endforeach; ?>


                                </select>

                            </div>


                            <!-- YEAR SECTION -->

                            <div
                                class="
                                    col-12
                                    col-md-6
                                    col-lg-2
                                ">

                                <label
                                    for="year_section"
                                    class="form-label">

                                    Year & Section

                                </label>


                                <select
                                    class="form-select"
                                    id="year_section"
                                    name="year_section">


                                    <option value="">

                                        All Year & Sections

                                    </option>


                                    <?php foreach (
                                        $yearSections
                                        as $item
                                    ): ?>

                                        <option
                                            value="<?= htmlspecialchars(
                                                        $item,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>"
                                            <?= $yearSection === $item
                                                ? "selected"
                                                : "" ?>>

                                            <?= htmlspecialchars(
                                                $item,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </option>

                                    <?php endforeach; ?>


                                </select>

                            </div>


                            <!-- ACADEMIC POST -->

                            <div
                                class="
                                    col-12
                                    col-md-6
                                    col-lg-2
                                ">

                                <label
                                    for="post_id"
                                    class="form-label">

                                    Academic Post

                                </label>


                                <select
                                    class="form-select"
                                    id="post_id"
                                    name="post_id">


                                    <option value="">

                                        All Posts

                                    </option>


                                    <?php foreach (
                                        $academicPosts
                                        as $post
                                    ): ?>

                                        <option
                                            value="<?= (int)$post["id"] ?>"
                                            <?= $postId == $post["id"]
                                                ? "selected"
                                                : "" ?>>

                                            <?= htmlspecialchars(
                                                $post["title"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </option>

                                    <?php endforeach; ?>


                                </select>

                            </div>


                            <!-- STATUS -->

                            <div
                                class="
                                    col-12
                                    col-md-6
                                    col-lg-2
                                ">

                                <label
                                    for="status"
                                    class="form-label">

                                    Status

                                </label>


                                <select
                                    class="form-select"
                                    id="status"
                                    name="status">


                                    <option value="">

                                        All Status

                                    </option>


                                    <option
                                        value="open"
                                        <?= $status === "open"
                                            ? "selected"
                                            : "" ?>>

                                        Open

                                    </option>


                                    <option
                                        value="closed"
                                        <?= $status === "closed"
                                            ? "selected"
                                            : "" ?>>

                                        Closed

                                    </option>


                                </select>

                            </div>


                            <!-- DATE FROM -->

                            <div
                                class="
                                    col-12
                                    col-md-6
                                    col-lg-3
                                ">

                                <label
                                    for="date_from"
                                    class="form-label">

                                    From Date

                                </label>


                                <input
                                    type="date"
                                    class="form-control"
                                    id="date_from"
                                    name="date_from"
                                    value="<?= htmlspecialchars(
                                                $dateFrom,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>">

                            </div>


                            <!-- DATE TO -->

                            <div
                                class="
                                    col-12
                                    col-md-6
                                    col-lg-3
                                ">

                                <label
                                    for="date_to"
                                    class="form-label">

                                    To Date

                                </label>


                                <input
                                    type="date"
                                    class="form-control"
                                    id="date_to"
                                    name="date_to"
                                    value="<?= htmlspecialchars(
                                                $dateTo,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>">

                            </div>


                            <!-- BUTTONS -->

                            <div
                                class="
                                    col-12
                                    col-lg-6
                                    d-flex
                                    align-items-end
                                    gap-2
                                ">


                                <button
                                    type="submit"
                                    class="
                                        btn
                                        btn-primary
                                    ">

                                    <i
                                        class="
                                            bi
                                            bi-funnel
                                            me-1
                                        ">
                                    </i>

                                    Apply Filters

                                </button>


                                <a
                                    href="academic_post_access_log.php"
                                    class="
                                        btn
                                        btn-outline-secondary
                                    ">

                                    <i
                                        class="
                                            bi
                                            bi-arrow-counterclockwise
                                            me-1
                                        ">
                                    </i>

                                    Reset

                                </a>


                            </div>


                        </div>

                    </form>

                </div>

            </div>


            <!-- =================================================
                 ACCESS RECORDS
            ================================================== -->

            <div
                class="
                    card
                    monitor-card
                ">


                <!-- HEADER -->

                <div
                    class="
                        card-header
                        bg-transparent
                        border-0
                        p-3
                    ">


                    <div
                        class="
                            d-flex
                            flex-wrap
                            justify-content-between
                            align-items-center
                            gap-2
                        ">


                        <div>

                            <h5
                                class="
                                    mb-1
                                    fw-bold
                                ">

                                Student Access Records

                            </h5>


                            <div
                                class="
                                    text-muted
                                    small
                                ">

                                Showing

                                <strong>

                                    <?= number_format(
                                        count($records)
                                    ) ?>

                                </strong>

                                of

                                <strong>

                                    <?= number_format(
                                        $totalRecords
                                    ) ?>

                                </strong>

                                access sessions

                            </div>

                        </div>


                    </div>

                </div>


                <?php if (
                    !empty($records)
                ): ?>


                    <!-- =================================================
                         TABLE
                    ================================================== -->

                    <div
                        class="table-responsive">


                        <table
                            class="
                                table
                                monitor-table
                                align-middle
                            ">


                            <thead>

                                <tr>

                                    <th>
                                        Student
                                    </th>

                                    <th>
                                        Department
                                    </th>

                                    <th>
                                        Year & Section
                                    </th>

                                    <th>
                                        Academic Post
                                    </th>

                                    <th
                                        class="text-center">

                                        Access

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
                                        Status
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php foreach (
                                    $records
                                    as $row
                                ): ?>


                                    <?php

                                    $studentName =
                                        formatStudentName(
                                            $row
                                        );


                                    $duration =
                                        (int)(
                                            $row["duration_seconds"] ?? 0
                                        );


                                    /* =====================================
                                       LIVE DURATION FOR OPEN SESSION
                                    ====================================== */

                                    if (

                                        $row["status"] ===
                                        "open"

                                        &&

                                        !empty($row["opened_at"])

                                    ) {

                                        $openedTimestamp =
                                            strtotime(
                                                $row["opened_at"]
                                            );


                                        $currentDuration =
                                            max(
                                                0,
                                                time()
                                                    -
                                                    $openedTimestamp
                                            );
                                    } else {

                                        $currentDuration =
                                            $duration;
                                    }

                                    ?>


                                    <tr>


                                        <!-- STUDENT -->

                                        <td>

                                            <div
                                                class="
                                                    student-name
                                                ">

                                                <?= htmlspecialchars(
                                                    $studentName,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>

                                            </div>


                                            <div
                                                class="
                                                    student-id
                                                ">

                                                <?= htmlspecialchars(
                                                    $row["student_id"] ?? "",
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>

                                            </div>

                                        </td>


                                        <!-- DEPARTMENT -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $row["department"] ?? "",
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </td>


                                        <!-- YEAR SECTION -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $row["year_section"] ?? "",
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </td>


                                        <!-- POST -->

                                        <td>

                                            <div
                                                class="
                                                    post-title
                                                ">

                                                <?= htmlspecialchars(
                                                    $row["post_title"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>

                                            </div>


                                            <?php if (
                                                !empty($row["post_subject"])
                                            ): ?>

                                                <div
                                                    class="
                                                        post-subject
                                                    ">

                                                    <?= htmlspecialchars(
                                                        $row["post_subject"],
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>

                                                </div>

                                            <?php endif; ?>


                                        </td>


                                        <!-- ACCESS NUMBER -->

                                        <td
                                            class="text-center">


                                            <span
                                                class="
                                                    access-number
                                                "
                                                title="
                                                    Total accesses:
                                                    <?= (int)$row["total_accesses"] ?>
                                                ">

                                                <?= (int)$row["access_number"] ?>

                                            </span>


                                        </td>


                                        <!-- OPENED -->

                                        <td>

                                            <div
                                                class="
                                                    time-value
                                                ">


                                                <?= htmlspecialchars(
                                                    date(
                                                        "M d, Y",
                                                        strtotime(
                                                            $row["opened_at"]
                                                        )
                                                    ),
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>


                                                <br>


                                                <span
                                                    class="
                                                        text-muted
                                                    ">

                                                    <?= htmlspecialchars(
                                                        date(
                                                            "h:i:s A",
                                                            strtotime(
                                                                $row["opened_at"]
                                                            )
                                                        ),
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>

                                                </span>


                                            </div>

                                        </td>


                                        <!-- CLOSED -->

                                        <td>


                                            <?php if (
                                                !empty($row["closed_at"])
                                            ): ?>


                                                <div
                                                    class="
                                                        time-value
                                                    ">


                                                    <?= htmlspecialchars(
                                                        date(
                                                            "M d, Y",
                                                            strtotime(
                                                                $row["closed_at"]
                                                            )
                                                        ),
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>


                                                    <br>


                                                    <span
                                                        class="
                                                            text-muted
                                                        ">

                                                        <?= htmlspecialchars(
                                                            date(
                                                                "h:i:s A",
                                                                strtotime(
                                                                    $row["closed_at"]
                                                                )
                                                            ),
                                                            ENT_QUOTES,
                                                            "UTF-8"
                                                        ) ?>

                                                    </span>


                                                </div>


                                            <?php else: ?>


                                                <span
                                                    class="
                                                        text-muted
                                                    ">

                                                    —

                                                </span>


                                            <?php endif; ?>


                                        </td>


                                        <!-- DURATION -->

                                        <td>

                                            <strong>

                                                <?= htmlspecialchars(
                                                    formatDuration(
                                                        $currentDuration
                                                    ),
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>

                                            </strong>

                                        </td>


                                        <!-- STATUS -->

                                        <td>


                                            <?php if (
                                                $row["status"] === "open"
                                            ): ?>


                                                <span
                                                    class="
                                                        status-badge
                                                        status-open
                                                    ">

                                                    <i
                                                        class="
                                                            bi
                                                            bi-circle-fill
                                                            me-1
                                                        ">
                                                    </i>

                                                    Open

                                                </span>


                                            <?php else: ?>


                                                <span
                                                    class="
                                                        status-badge
                                                        status-closed
                                                    ">

                                                    <i
                                                        class="
                                                            bi
                                                            bi-check-circle
                                                            me-1
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


                    <!-- =================================================
                         PAGINATION
                    ================================================== -->

                    <?php if (
                        $totalPages > 1
                    ): ?>


                        <div
                            class="
                                card-footer
                                bg-transparent
                                border-0
                            ">


                            <nav
                                aria-label="
                                    Academic post monitoring pagination
                                ">


                                <ul
                                    class="
                                        pagination
                                        justify-content-center
                                        mb-0
                                    ">


                                    <!-- PREVIOUS -->

                                    <li
                                        class="
                                            page-item
                                            <?= $page <= 1
                                                ? "disabled"
                                                : "" ?>
                                        ">


                                        <a
                                            class="page-link"
                                            href="?<?= htmlspecialchars(
                                                        $paginationQuery,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>&page=<?= max(
                                                            1,
                                                            $page - 1
                                                        ) ?>">

                                            <i
                                                class="
                                                    bi
                                                    bi-chevron-left
                                                ">
                                            </i>

                                        </a>


                                    </li>


                                    <?php

                                    $startPage =
                                        max(
                                            1,
                                            $page - 2
                                        );


                                    $endPage =
                                        min(
                                            $totalPages,
                                            $page + 2
                                        );

                                    ?>


                                    <?php for (
                                        $i = $startPage;
                                        $i <= $endPage;
                                        $i++
                                    ): ?>


                                        <li
                                            class="
                                                page-item
                                                <?= $i === $page
                                                    ? "active"
                                                    : "" ?>
                                            ">


                                            <a
                                                class="page-link"
                                                href="?<?= htmlspecialchars(
                                                            $paginationQuery,
                                                            ENT_QUOTES,
                                                            "UTF-8"
                                                        ) ?>&page=<?= $i ?>">

                                                <?= $i ?>

                                            </a>


                                        </li>


                                    <?php endfor; ?>


                                    <!-- NEXT -->

                                    <li
                                        class="
                                            page-item
                                            <?= $page >= $totalPages
                                                ? "disabled"
                                                : "" ?>
                                        ">


                                        <a
                                            class="page-link"
                                            href="?<?= htmlspecialchars(
                                                        $paginationQuery,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>&page=<?= min(
                                                            $totalPages,
                                                            $page + 1
                                                        ) ?>">

                                            <i
                                                class="
                                                    bi
                                                    bi-chevron-right
                                                ">
                                            </i>

                                        </a>


                                    </li>


                                </ul>


                            </nav>


                        </div>


                    <?php endif; ?>


                <?php else: ?>


                    <!-- =================================================
                         EMPTY STATE
                    ================================================== -->

                    <div
                        class="empty-state">


                        <i
                            class="
                                bi
                                bi-inbox
                            ">
                        </i>


                        <h5>

                            No access records found

                        </h5>


                        <p
                            class="
                                text-muted
                                mb-0
                            ">

                            No Academic Post activity
                            matches your selected filters.

                        </p>


                    </div>


                <?php endif; ?>


            </div>


        </div>

    </main>


    <!-- =====================================================
         GLOBAL SCRIPTS
    ====================================================== -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>