<?php
include './globals/checks.php';
/* =========================================================
   ADMIN LECTURES
   ETS-Async Learning Portal
   ========================================================= */


/* =========================================================
   DATABASE CONNECTION
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   DELETE LECTURE
   ========================================================= */

if (
    isset($_GET["delete"]) &&
    is_numeric($_GET["delete"])
) {

    $deleteId =
        (int) $_GET["delete"];


    $deleteSQL = "
        DELETE FROM lectures
        WHERE id = ?
    ";


    $deleteStmt =
        $mysqli->prepare($deleteSQL);


    if ($deleteStmt) {

        $deleteStmt->bind_param(
            "i",
            $deleteId
        );

        $deleteStmt->execute();

        $deleteStmt->close();
    }


    header("Location: lectures.php");
    exit;
}


/* =========================================================
   FILTERS
   ========================================================= */

$department =
    trim($_GET["department"] ?? "");

$yearLevel =
    trim($_GET["year_level"] ?? "");

$status =
    trim($_GET["status"] ?? "");

$search =
    trim($_GET["search"] ?? "");


/* =========================================================
   GET DEPARTMENTS
   ========================================================= */

$departments = [];

$departmentSQL = "
    SELECT DISTINCT department
    FROM accounts
    WHERE access = 'student'
      AND department IS NOT NULL
      AND department != ''
    ORDER BY department ASC
";

$departmentResult =
    $mysqli->query($departmentSQL);

if ($departmentResult) {

    while ($row = $departmentResult->fetch_assoc()) {

        $departments[] =
            $row["department"];
    }
}


/* =========================================================
   GET YEAR LEVELS
   ========================================================= */

$yearLevels = [];

$yearSQL = "
    SELECT DISTINCT year_section
    FROM accounts
    WHERE access = 'student'
      AND year_section IS NOT NULL
      AND year_section != ''
    ORDER BY year_section ASC
";

$yearResult =
    $mysqli->query($yearSQL);

if ($yearResult) {

    while ($row = $yearResult->fetch_assoc()) {

        $value =
            trim($row["year_section"]);

        $parts =
            preg_split(
                '/[-\s]+/',
                $value
            );

        if (
            isset($parts[0]) &&
            $parts[0] !== ""
        ) {

            $yearLevels[] =
                trim($parts[0]);
        }
    }
}

$yearLevels =
    array_unique($yearLevels);

sort($yearLevels);


/* =========================================================
   BUILD WHERE CLAUSE
   ========================================================= */

$where = [];

$params = [];

$types = "";


if ($department !== "") {

    $where[] =
        "l.department = ?";

    $params[] =
        $department;

    $types .= "s";
}


if ($yearLevel !== "") {

    $where[] =
        "l.year_level = ?";

    $params[] =
        $yearLevel;

    $types .= "s";
}


if ($status !== "") {

    $where[] =
        "l.status = ?";

    $params[] =
        $status;

    $types .= "s";
}


if ($search !== "") {

    $where[] = "
        (
            l.title LIKE ?
            OR l.youtube_url LIKE ?
        )
    ";

    $searchValue =
        "%" . $search . "%";

    $params[] =
        $searchValue;

    $params[] =
        $searchValue;

    $types .= "ss";
}


/* =========================================================
   WHERE SQL
   ========================================================= */

$whereSQL = "";

if (count($where) > 0) {

    $whereSQL =
        "WHERE " .
        implode(
            " AND ",
            $where
        );
}


/* =========================================================
   GET LECTURES
   ========================================================= */

/*
   We calculate student counts based on:

   department
   year_level
   section

   If section is NULL or empty,
   the lecture applies to all sections.
*/

$sql = "

    SELECT

        l.id,

        l.title,

        l.youtube_url,

        l.department,

        l.year_level,

        l.section,

        l.description,

        l.start_date,

        l.due_date,

        l.status,

        l.created_at,


        /* =====================================================
           TOTAL STUDENTS
        ===================================================== */

        (

            SELECT COUNT(*)

            FROM accounts a

            WHERE a.access = 'student'

              AND a.department = l.department

              AND (

                    a.year_section LIKE CONCAT(
                        l.year_level,
                        '-%'
                    )

              )

              AND (

                    l.section IS NULL

                    OR l.section = ''

                    OR a.year_section = CONCAT(
                        l.year_level,
                        '-',
                        l.section
                    )

              )

        ) AS total_students,


        /* =====================================================
           STARTED
        ===================================================== */

        (

            SELECT COUNT(*)

            FROM lecture_progress lp

            WHERE lp.lecture_id = l.id

              AND lp.status IN (
                  'in_progress',
                  'completed'
              )

        ) AS started_students,


        /* =====================================================
           COMPLETED
        ===================================================== */

        (

            SELECT COUNT(*)

            FROM lecture_progress lp

            WHERE lp.lecture_id = l.id

              AND lp.status = 'completed'

        ) AS completed_students,


        /* =====================================================
           SUBMISSIONS
        ===================================================== */

        (

            SELECT COUNT(*)

            FROM lecture_submissions ls

            WHERE ls.lecture_id = l.id

              AND ls.status = 'submitted'

        ) AS submitted_students


    FROM lectures l

    $whereSQL

    ORDER BY
        l.created_at DESC
";


$stmt =
    $mysqli->prepare($sql);


if ($stmt && $types !== "") {

    $stmt->bind_param(
        $types,
        ...$params
    );
}


if ($stmt) {

    $stmt->execute();

    $lectures =
        $stmt->get_result();
} else {

    $lectures = false;
}


/* =========================================================
   HELPER FUNCTIONS
   ========================================================= */

function lectureStatusClass($status)
{

    if ($status === "active") {

        return "bg-success-subtle text-success";
    }

    return "bg-secondary-subtle text-secondary";
}


function lectureTimeStatus(
    $startDate,
    $dueDate,
    $status
) {

    if ($status !== "active") {

        return [
            "label" => "Inactive",
            "class" => "text-secondary"
        ];
    }


    $now =
        time();

    $start =
        strtotime($startDate);

    $due =
        strtotime($dueDate);


    if ($now < $start) {

        return [
            "label" => "Upcoming",
            "class" => "text-primary"
        ];
    }


    if ($now > $due) {

        return [
            "label" => "Expired",
            "class" => "text-danger"
        ];
    }


    return [
        "label" => "Open",
        "class" => "text-success"
    ];
}


function buildLectureUrl($page = null)
{

    $params =
        $_GET;


    if ($page !== null) {

        $params["page"] =
            $page;
    }


    return "?" .
        http_build_query($params);
}

?>


<!DOCTYPE html>

<html lang="en">


<?php include 'globals/head.php'; ?>

<style>
    /* =========================================================
   ADMIN LECTURES
   ETS-Async Learning Portal
   ========================================================= */


    /* =========================================================
   PAGE HEADER
   ========================================================= */

    .lecture-page-header {

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 20px;

        margin-bottom: 24px;
    }


    .lecture-page-header h2 {

        margin: 0;

        color: #1f2937;

        font-size: 1.65rem;

        font-weight: 700;
    }


    .lecture-page-header p {

        margin: 6px 0 0;

        color: #6b7280;

        font-size: 0.9rem;
    }


    .lecture-page-header .btn {

        display: inline-flex;

        align-items: center;
        justify-content: center;

        min-height: 42px;

        padding: 9px 16px;

        border-radius: 8px;

        font-size: 0.88rem;

        font-weight: 600;
    }


    /* =========================================================
   FILTER CARD
   ========================================================= */

    .filter-card {

        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 12px;

        padding: 20px;

        margin-bottom: 20px;

        box-shadow:
            0 2px 8px rgba(15,
                23,
                42,
                0.04);
    }


    /* =========================================================
   FILTER LABELS
   ========================================================= */

    .filter-card .form-label {

        display: block;

        margin-bottom: 6px;

        color: #374151;

        font-size: 0.82rem;

        font-weight: 600;
    }


    /* =========================================================
   FILTER INPUTS
   ========================================================= */

    .filter-card .form-control,
    .filter-card .form-select {

        min-height: 42px;

        border: 1px solid #d1d5db;

        border-radius: 8px;

        color: #1f2937;

        background-color: #ffffff;

        font-size: 0.86rem;

        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }


    .filter-card .form-control {

        padding-left: 12px;

        padding-right: 12px;
    }


    .filter-card .form-select {

        cursor: pointer;
    }


    .filter-card .form-control::placeholder {

        color: #9ca3af;
    }


    .filter-card .form-control:focus,
    .filter-card .form-select:focus {

        border-color: #6366f1;

        box-shadow:
            0 0 0 3px rgba(99,
                102,
                241,
                0.12);

        outline: none;
    }


    /* =========================================================
   FILTER BUTTONS
   ========================================================= */

    .filter-actions {

        gap: 8px !important;
    }


    .filter-actions .btn {

        min-height: 42px;

        border-radius: 8px;

        font-size: 0.84rem;

        font-weight: 600;
    }


    .filter-actions .btn-primary {

        flex: 1;
    }


    .filter-actions .btn-outline-secondary {

        width: 42px;

        display: flex;

        align-items: center;
        justify-content: center;
    }


    /* =========================================================
   TABLE CARD
   ========================================================= */

    .table-card {

        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 12px;

        overflow: hidden;

        box-shadow:
            0 2px 8px rgba(15,
                23,
                42,
                0.04);
    }


    /* =========================================================
   TABLE HEADER
   ========================================================= */

    .table-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        padding: 17px 20px;

        border-bottom: 1px solid #e5e7eb;

        background: #ffffff;
    }


    .table-header-title {

        display: flex;

        align-items: center;

        color: #1f2937;

        font-size: 0.95rem;

        font-weight: 700;
    }


    .table-header-title i {

        font-size: 1.05rem;
    }


    .table-header .badge {

        min-width: 30px;

        padding: 5px 9px;

        border-radius: 20px;

        font-size: 0.72rem;
    }


    /* =========================================================
   LECTURE TABLE
   ========================================================= */

    .lecture-table {

        margin: 0;

        vertical-align: middle;

        min-width: 1050px;
    }


    .lecture-table thead th {

        padding: 12px 14px;

        border-bottom: 1px solid #e5e7eb;

        background: #f8fafc;

        color: #6b7280;

        font-size: 0.72rem;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: 0.035em;

        white-space: nowrap;
    }


    .lecture-table tbody td {

        padding: 16px 14px;

        border-color: #eef0f3;

        color: #374151;

        font-size: 0.84rem;
    }


    .lecture-table tbody tr {

        transition:
            background-color 0.15s ease;
    }


    .lecture-table tbody tr:hover {

        background-color: #f8faff;
    }


    /* =========================================================
   LECTURE TITLE
   ========================================================= */

    .lecture-title {

        max-width: 250px;

        color: #1f2937;

        font-size: 0.88rem;

        font-weight: 700;

        line-height: 1.4;
    }


    .lecture-meta {

        margin-top: 4px;

        color: #9ca3af;

        font-size: 0.72rem;
    }


    /* =========================================================
   ASSIGNMENT BADGES
   ========================================================= */

    .assignment-badges {

        display: flex;

        flex-wrap: wrap;

        gap: 5px;

        max-width: 220px;
    }


    .assignment-badge {

        padding: 5px 8px;

        border: 1px solid #e0e7ff;

        border-radius: 6px;

        background: #f5f7ff;

        color: #4f46e5;

        font-size: 0.68rem;

        font-weight: 600;
    }


    /* =========================================================
   SCHEDULE
   ========================================================= */

    .lecture-date {

        color: #374151;

        font-size: 0.8rem;

        font-weight: 600;

        white-space: nowrap;
    }


    .lecture-date i {

        color: #6366f1;
    }


    .lecture-due {

        margin-top: 4px;

        color: #9ca3af;

        font-size: 0.72rem;

        white-space: nowrap;
    }


    .lecture-time-status {

        margin-top: 5px;

        font-size: 0.72rem;

        font-weight: 600;
    }


    /* =========================================================
   STUDENT COUNT
   ========================================================= */

    .student-count {

        color: #1f2937;

        font-size: 1rem;

        font-weight: 700;
    }


    .student-count-label {

        margin-top: 2px;

        color: #9ca3af;

        font-size: 0.7rem;
    }


    /* =========================================================
   PROGRESS
   ========================================================= */

    .lecture-progress-cell {

        min-width: 180px;
    }


    .progress-summary {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 10px;

        margin-bottom: 6px;

        color: #6b7280;

        font-size: 0.7rem;
    }


    .progress-summary strong {

        color: #374151;

        font-size: 0.75rem;
    }


    .lecture-progress-cell .progress {

        height: 7px;

        overflow: hidden;

        border-radius: 20px;

        background-color: #e5e7eb;
    }


    .lecture-progress-cell .progress-bar {

        border-radius: 20px;

        transition:
            width 0.4s ease;
    }


    .progress-details {

        margin-top: 5px;

        color: #9ca3af;

        font-size: 0.68rem;

        white-space: nowrap;
    }


    /* =========================================================
   STATUS
   ========================================================= */

    .lecture-status {

        padding: 6px 9px;

        border-radius: 20px;

        font-size: 0.7rem;

        font-weight: 600;

        white-space: nowrap;
    }


    .bg-success-subtle {

        background-color: #ecfdf3 !important;
    }


    .text-success {

        color: #15803d !important;
    }


    .bg-secondary-subtle {

        background-color: #f3f4f6 !important;
    }


    .text-secondary {

        color: #6b7280 !important;
    }


    .text-primary {

        color: #4f46e5 !important;
    }


    .text-danger {

        color: #dc2626 !important;
    }


    /* =========================================================
   ACTIONS
   ========================================================= */

    .lecture-actions {

        display: flex;

        align-items: center;

        justify-content: center;

        gap: 5px;

        white-space: nowrap;
    }


    .lecture-actions .btn {

        min-height: 32px;

        padding: 5px 8px;

        border-radius: 7px;

        font-size: 0.73rem;

        font-weight: 600;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 4px;

        transition:
            background-color 0.15s ease,
            color 0.15s ease,
            border-color 0.15s ease,
            transform 0.15s ease;
    }


    .lecture-actions .btn:hover {

        transform: translateY(-1px);
    }


    .lecture-actions .btn-outline-primary:hover {

        background: #4f46e5;

        border-color: #4f46e5;

        color: #ffffff;
    }


    .lecture-actions .btn-outline-secondary:hover {

        background: #6b7280;

        border-color: #6b7280;

        color: #ffffff;
    }


    .lecture-actions .btn-outline-danger:hover {

        background: #dc2626;

        border-color: #dc2626;

        color: #ffffff;
    }


    /* =========================================================
   EMPTY STATE
   ========================================================= */

    .empty-state {

        padding: 65px 25px;

        text-align: center;
    }


    .empty-state>i {

        display: inline-flex;

        align-items: center;
        justify-content: center;

        width: 70px;
        height: 70px;

        margin-bottom: 18px;

        border-radius: 50%;

        background: #f5f7ff;

        color: #6366f1;

        font-size: 2rem;
    }


    .empty-state h5 {

        margin-bottom: 7px;

        color: #1f2937;

        font-size: 1rem;

        font-weight: 700;
    }


    .empty-state p {

        color: #9ca3af;

        font-size: 0.84rem;
    }


    .empty-state .btn {

        min-height: 40px;

        padding: 8px 15px;

        border-radius: 8px;

        font-size: 0.82rem;

        font-weight: 600;
    }


    /* =========================================================
   TABLE SCROLLBAR
   ========================================================= */

    .table-responsive {

        scrollbar-width: thin;

        scrollbar-color: #cbd5e1 transparent;
    }


    .table-responsive::-webkit-scrollbar {

        height: 7px;
    }


    .table-responsive::-webkit-scrollbar-track {

        background: #f8fafc;
    }


    .table-responsive::-webkit-scrollbar-thumb {

        background: #cbd5e1;

        border-radius: 10px;
    }


    /* =========================================================
   RESPONSIVE
   ========================================================= */

    @media (max-width: 992px) {

        .lecture-page-header {

            flex-direction: column;

            align-items: flex-start;
        }


        .lecture-page-header>div:last-child {

            width: 100%;
        }


        .lecture-page-header .btn {

            width: 100%;
        }


        .filter-actions {

            align-items: stretch !important;
        }


        .filter-actions .btn-primary {

            flex: 1;
        }

    }


    /* =========================================================
   TABLET
   ========================================================= */

    @media (max-width: 768px) {

        .filter-card {

            padding: 16px;
        }


        .table-header {

            padding: 15px 16px;
        }


        .lecture-table {

            min-width: 1000px;
        }

    }


    /* =========================================================
   MOBILE
   ========================================================= */

    @media (max-width: 576px) {

        .lecture-page-header h2 {

            font-size: 1.4rem;
        }


        .lecture-page-header p {

            font-size: 0.82rem;

            line-height: 1.5;
        }


        .filter-card {

            padding: 14px;
        }


        .filter-actions {

            width: 100%;
        }


        .filter-actions .btn-outline-secondary {

            width: 42px;

            min-width: 42px;
        }


        .table-header-title {

            font-size: 0.88rem;
        }


        .empty-state {

            padding: 50px 18px;
        }

    }
</style>

<body>


    <!-- =========================================================
         SIDEBAR
    ========================================================== -->

    <?php include 'globals/sidebar.php'; ?>


    <!-- =========================================================
         TOPBAR
    ========================================================== -->

    <?php include 'globals/topbar.php'; ?>


    <!-- =========================================================
         MAIN
    ========================================================== -->

    <main class="main-content">


        <div class="content-wrapper">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="page-header lecture-page-header">


                <div>

                    <h2>
                        Lectures
                    </h2>

                    <p>
                        Manage asynchronous lectures and monitor
                        student participation.
                    </p>

                </div>


                <div>

                    <a
                        href="lecture_create.php"
                        class="btn btn-primary">

                        <i class="bi bi-plus-lg me-1"></i>

                        Create Lecture

                    </a>

                </div>


            </div>


            <!-- =================================================
                 FILTER CARD
            ================================================== -->

            <div class="filter-card">


                <form
                    method="GET"
                    action="lectures.php">


                    <div class="row g-3">


                        <!-- SEARCH -->

                        <div class="col-lg-4">

                            <label
                                class="form-label">

                                Search

                            </label>


                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search lecture..."
                                value="<?= htmlspecialchars($search) ?>">

                        </div>


                        <!-- DEPARTMENT -->

                        <div class="col-md-4 col-lg-2">

                            <label
                                class="form-label">

                                Department

                            </label>


                            <select
                                name="department"
                                class="form-select">


                                <option value="">
                                    All Departments
                                </option>


                                <?php foreach (
                                    $departments
                                    as $itemDepartment
                                ): ?>

                                    <option
                                        value="<?= htmlspecialchars($itemDepartment) ?>"
                                        <?= $department === $itemDepartment
                                            ? "selected"
                                            : ""
                                        ?>>

                                        <?= htmlspecialchars($itemDepartment) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>


                        <!-- YEAR -->

                        <div class="col-md-4 col-lg-2">

                            <label
                                class="form-label">

                                Year Level

                            </label>


                            <select
                                name="year_level"
                                class="form-select">


                                <option value="">
                                    All Years
                                </option>


                                <?php foreach (
                                    $yearLevels
                                    as $itemYear
                                ): ?>

                                    <option
                                        value="<?= htmlspecialchars($itemYear) ?>"
                                        <?= $yearLevel === $itemYear
                                            ? "selected"
                                            : ""
                                        ?>>

                                        <?= htmlspecialchars($itemYear) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>


                        <!-- STATUS -->

                        <div class="col-md-4 col-lg-2">

                            <label
                                class="form-label">

                                Status

                            </label>


                            <select
                                name="status"
                                class="form-select">


                                <option value="">
                                    All Status
                                </option>


                                <option
                                    value="active"
                                    <?= $status === "active"
                                        ? "selected"
                                        : ""
                                    ?>>

                                    Active

                                </option>


                                <option
                                    value="inactive"
                                    <?= $status === "inactive"
                                        ? "selected"
                                        : ""
                                    ?>>

                                    Inactive

                                </option>


                            </select>

                        </div>


                        <!-- BUTTONS -->

                        <div
                            class="col-md-12 col-lg-2 d-flex align-items-end gap-2 filter-actions">


                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-funnel me-1"></i>

                                Filter

                            </button>


                            <a
                                href="lectures.php"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-arrow-counterclockwise"></i>

                            </a>


                        </div>


                    </div>


                </form>


            </div>


            <!-- =================================================
                 LECTURE TABLE
            ================================================== -->

            <div class="table-card">


                <div class="table-header">


                    <div class="table-header-title">

                        <i
                            class="bi bi-play-circle me-2 text-primary">
                        </i>

                        Asynchronous Lectures

                    </div>


                    <?php

                    $lectureCount =
                        $lectures
                        ? $lectures->num_rows
                        : 0;

                    ?>


                    <span
                        class="badge text-bg-primary">

                        <?= number_format($lectureCount) ?>

                    </span>


                </div>


                <?php if (
                    $lectures &&
                    $lectures->num_rows > 0
                ): ?>


                    <div class="table-responsive">


                        <table
                            class="table table-hover lecture-table">


                            <thead
                                class="table-light">


                                <tr>

                                    <th>
                                        Lecture
                                    </th>

                                    <th>
                                        Assignment
                                    </th>

                                    <th>
                                        Schedule
                                    </th>

                                    <th>
                                        Students
                                    </th>

                                    <th>
                                        Progress
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th
                                        class="text-center">

                                        Actions

                                    </th>

                                </tr>


                            </thead>


                            <tbody>


                                <?php while (
                                    $lecture =
                                    $lectures->fetch_assoc()
                                ): ?>


                                    <?php

                                    $timeStatus =
                                        lectureTimeStatus(
                                            $lecture["start_date"],
                                            $lecture["due_date"],
                                            $lecture["status"]
                                        );


                                    $totalStudents =
                                        (int)
                                        $lecture["total_students"];


                                    $startedStudents =
                                        (int)
                                        $lecture["started_students"];


                                    $completedStudents =
                                        (int)
                                        $lecture["completed_students"];


                                    $submittedStudents =
                                        (int)
                                        $lecture["submitted_students"];


                                    $completionPercentage = 0;


                                    if (
                                        $totalStudents > 0
                                    ) {

                                        $completionPercentage =
                                            round(
                                                (
                                                    $completedStudents /
                                                    $totalStudents
                                                ) * 100
                                            );
                                    }


                                    ?>


                                    <tr>


                                        <!-- =================================
                                             LECTURE
                                        ================================== -->

                                        <td>

                                            <div
                                                class="lecture-title">

                                                <?= htmlspecialchars(
                                                    $lecture["title"]
                                                ) ?>

                                            </div>


                                            <div
                                                class="lecture-meta">

                                                Created

                                                <?= htmlspecialchars(
                                                    date(
                                                        "M d, Y",
                                                        strtotime(
                                                            $lecture["created_at"]
                                                        )
                                                    )
                                                ) ?>

                                            </div>

                                        </td>


                                        <!-- =================================
                                             ASSIGNMENT
                                        ================================== -->

                                        <td>


                                            <div
                                                class="assignment-badges">


                                                <span
                                                    class="badge assignment-badge">

                                                    <?= htmlspecialchars(
                                                        $lecture["department"]
                                                    ) ?>

                                                </span>


                                                <span
                                                    class="badge assignment-badge">

                                                    Year
                                                    <?= htmlspecialchars(
                                                        $lecture["year_level"]
                                                    ) ?>

                                                </span>


                                                <?php if (
                                                    !empty($lecture["section"])
                                                ): ?>

                                                    <span
                                                        class="badge assignment-badge">

                                                        Sec.
                                                        <?= htmlspecialchars(
                                                            $lecture["section"]
                                                        ) ?>

                                                    </span>

                                                <?php else: ?>

                                                    <span
                                                        class="badge assignment-badge">

                                                        All Sections

                                                    </span>

                                                <?php endif; ?>


                                            </div>


                                        </td>


                                        <!-- =================================
                                             SCHEDULE
                                        ================================== -->

                                        <td>


                                            <div
                                                class="lecture-date">

                                                <i
                                                    class="bi bi-calendar-event me-1">
                                                </i>

                                                <?= htmlspecialchars(
                                                    date(
                                                        "M d, Y",
                                                        strtotime(
                                                            $lecture["start_date"]
                                                        )
                                                    )
                                                ) ?>

                                            </div>


                                            <div
                                                class="lecture-due">

                                                Due

                                                <?= htmlspecialchars(
                                                    date(
                                                        "M d, Y",
                                                        strtotime(
                                                            $lecture["due_date"]
                                                        )
                                                    )
                                                ) ?>

                                            </div>


                                            <div
                                                class="<?= htmlspecialchars(
                                                            $timeStatus["class"]
                                                        ) ?> lecture-time-status">

                                                <?= htmlspecialchars(
                                                    $timeStatus["label"]
                                                ) ?>

                                            </div>


                                        </td>


                                        <!-- =================================
                                             STUDENTS
                                        ================================== -->

                                        <td>


                                            <div
                                                class="student-count">

                                                <?= number_format(
                                                    $totalStudents
                                                ) ?>

                                            </div>


                                            <div
                                                class="student-count-label">

                                                assigned

                                            </div>


                                        </td>


                                        <!-- =================================
                                             PROGRESS
                                        ================================== -->

                                        <td
                                            class="lecture-progress-cell">


                                            <div
                                                class="progress-summary">


                                                <span>

                                                    <?= $completedStudents ?>

                                                    /
                                                    <?= $totalStudents ?>

                                                    completed

                                                </span>


                                                <strong>

                                                    <?= $completionPercentage ?>%

                                                </strong>


                                            </div>


                                            <div
                                                class="progress"
                                                role="progressbar"
                                                aria-valuenow="<?= $completionPercentage ?>"
                                                aria-valuemin="0"
                                                aria-valuemax="100">


                                                <div
                                                    class="progress-bar bg-primary"
                                                    style="width: <?= $completionPercentage ?>%">

                                                </div>


                                            </div>


                                            <div
                                                class="progress-details">

                                                <?= $startedStudents ?>
                                                started

                                                ·

                                                <?= $submittedStudents ?>
                                                submitted

                                            </div>


                                        </td>


                                        <!-- =================================
                                             STATUS
                                        ================================== -->

                                        <td>


                                            <span
                                                class="badge lecture-status <?= lectureStatusClass(
                                                                                $lecture["status"]
                                                                            ) ?>">

                                                <?= ucfirst(
                                                    htmlspecialchars(
                                                        $lecture["status"]
                                                    )
                                                ) ?>

                                            </span>


                                        </td>


                                        <!-- =================================
                                             ACTIONS
                                        ================================== -->

                                        <td
                                            class="text-center">


                                            <div
                                                class="lecture-actions">


                                                <a
                                                    href="lecture_monitor.php?id=<?= (int)$lecture["id"] ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Monitor students">

                                                    <i
                                                        class="bi bi-bar-chart">
                                                    </i>

                                                    Monitor

                                                </a>


                                                <a
                                                    href="lecture_edit.php?id=<?= (int)$lecture["id"] ?>"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    title="Edit lecture">

                                                    <i
                                                        class="bi bi-pencil">
                                                    </i>

                                                </a>


                                                <a
                                                    href="lectures.php?delete=<?= (int)$lecture["id"] ?>"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Delete lecture"
                                                    onclick="return confirm('Are you sure you want to delete this lecture? All student progress and submissions associated with this lecture will also be deleted.')">

                                                    <i
                                                        class="bi bi-trash">
                                                    </i>

                                                </a>


                                            </div>


                                        </td>


                                    </tr>


                                <?php endwhile; ?>


                            </tbody>


                        </table>


                    </div>


                <?php else: ?>


                    <div class="empty-state">


                        <i
                            class="bi bi-play-circle">
                        </i>


                        <h5>

                            No lectures found

                        </h5>


                        <p class="mb-3">

                            You haven't created any lectures
                            matching the current filters.

                        </p>


                        <a
                            href="lecture_create.php"
                            class="btn btn-primary">

                            <i
                                class="bi bi-plus-lg me-1">
                            </i>

                            Create Your First Lecture

                        </a>


                    </div>


                <?php endif; ?>


            </div>


        </div>


    </main>


    <!-- =========================================================
         JAVASCRIPT
    ========================================================== -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>