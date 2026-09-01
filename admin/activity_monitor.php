
<?php

/* =========================================================
   ADMIN ACTIVITY MONITOR
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
    ($_SESSION["user"]["access"] ?? "") !== "admin"
) {

    header("Location: ../login.php");

    exit;
}


/* =========================================================
   DATABASE
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   FILTERS
   ========================================================= */

$lectureId =
    (int) ($_GET["lecture_id"] ?? 0);

$department =
    trim($_GET["department"] ?? "");

$year =
    trim($_GET["year"] ?? "");

$section =
    trim($_GET["section"] ?? "");

$search =
    trim($_GET["search"] ?? "");


/* =========================================================
   GET LECTURES
   ========================================================= */

$lectures = [];

$sql = "
    SELECT
        id,
        title,
        department,
        year_level,
        section,
        start_date,
        due_date
    FROM lectures
    ORDER BY
        created_at DESC,
        id DESC
";

$result =
    $mysqli->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $lectures[] = $row;
    }
}


/* =========================================================
   GET DEPARTMENTS
   ========================================================= */

$departments = [];

$sql = "
    SELECT DISTINCT department
    FROM accounts
    WHERE access = 'student'
      AND department IS NOT NULL
      AND department <> ''
    ORDER BY department ASC
";

$result =
    $mysqli->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $departments[] =
            $row["department"];
    }
}


/* =========================================================
   GET YEAR / SECTION
   ========================================================= */

$yearSections = [];

$sql = "
    SELECT DISTINCT year_section
    FROM accounts
    WHERE access = 'student'
      AND year_section IS NOT NULL
      AND year_section <> ''
    ORDER BY year_section ASC
";

$result =
    $mysqli->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $yearSections[] =
            $row["year_section"];
    }
}


/* =========================================================
   EXTRACT YEARS
   ========================================================= */

$years = [];

foreach ($yearSections as $value) {

    $parts =
        preg_split(
            '/[-\s]+/',
            $value
        );

    if (
        isset($parts[0]) &&
        $parts[0] !== ""
    ) {

        $years[] =
            trim($parts[0]);
    }
}

$years =
    array_unique($years);

sort($years);


/* =========================================================
   EXTRACT SECTIONS
   ========================================================= */

$sections = [];

foreach ($yearSections as $value) {

    $parts =
        preg_split(
            '/[-\s]+/',
            $value,
            2
        );

    if (
        isset($parts[1]) &&
        $parts[1] !== ""
    ) {

        $sections[] =
            trim($parts[1]);
    }
}

$sections =
    array_unique($sections);

sort($sections);


/* =========================================================
   BUILD STUDENT FILTER
   ========================================================= */

$where = [
    "a.access = 'student'"
];

$params = [];

$types = "";


/* =========================================================
   DEPARTMENT
   ========================================================= */

if ($department !== "") {

    $where[] =
        "a.department = ?";

    $params[] =
        $department;

    $types .= "s";
}


/* =========================================================
   YEAR
   ========================================================= */

if ($year !== "") {

    $where[] =
        "a.year_section LIKE ?";

    $params[] =
        $year . "-%";

    $types .= "s";
}


/* =========================================================
   SECTION
   ========================================================= */

if ($section !== "") {

    $where[] =
        "a.year_section LIKE ?";

    $params[] =
        "%-" . $section;

    $types .= "s";
}


/* =========================================================
   SEARCH
   ========================================================= */

if ($search !== "") {

    $where[] = "
        (
            a.student_id LIKE ?
            OR a.first_name LIKE ?
            OR a.last_name LIKE ?
            OR a.email LIKE ?
            OR a.username LIKE ?
        )
    ";

    $searchValue =
        "%" . $search . "%";

    for ($i = 0; $i < 5; $i++) {

        $params[] =
            $searchValue;
    }

    $types .= "sssss";
}


$whereSQL =
    implode(
        " AND ",
        $where
    );


/* =========================================================
   LECTURE CONDITION
   ========================================================= */

$lectureCondition = "";

if ($lectureId > 0) {

    $lectureCondition =
        "AND l.id = ?";
}


/* =========================================================
   MAIN MONITOR QUERY
   ========================================================= */

$sql = "
    SELECT

        a.id,
        a.student_id,
        a.first_name,
        a.middle_initial,
        a.last_name,
        a.extension_name,
        a.department,
        a.year_section,
        a.email,

        l.id AS lecture_id,
        l.title AS lecture_title,
        l.department AS lecture_department,
        l.year_level,
        l.section,
        l.start_date,
        l.due_date,

        lp.started_at,
        lp.last_accessed_at,
        lp.access_count,
        lp.completed_at,
        lp.status AS progress_status,

        ls.submission_text,
        ls.submitted_at,
        ls.score,
        ls.status AS submission_status

    FROM accounts a

    CROSS JOIN lectures l

    LEFT JOIN lecture_progress lp
        ON lp.lecture_id = l.id
        AND lp.student_id = a.student_id

    LEFT JOIN lecture_submissions ls
        ON ls.lecture_id = l.id
        AND ls.student_id = a.student_id

    WHERE
        $whereSQL

        $lectureCondition

        AND (
            l.department = a.department
            OR l.department = ''
            OR l.department IS NULL
        )

        AND (
            CAST(l.year_level AS CHAR) =
            SUBSTRING_INDEX(a.year_section, '-', 1)

            OR l.year_level = ''
            OR l.year_level IS NULL
        )

    ORDER BY
        a.last_name ASC,
        a.first_name ASC
";


$allParams =
    $params;

$allTypes =
    $types;


if ($lectureId > 0) {

    $allParams[] =
        $lectureId;

    $allTypes .= "i";
}


$stmt =
    $mysqli->prepare($sql);


if (!$stmt) {

    die("Database error: " .
        htmlspecialchars(
            $mysqli->error
        ));
}


if ($allTypes !== "") {

    $stmt->bind_param(
        $allTypes,
        ...$allParams
    );
}


$stmt->execute();


$result =
    $stmt->get_result();


/* =========================================================
   STORE RECORDS
   ========================================================= */

$records = [];

while ($row = $result->fetch_assoc()) {

    $records[] =
        $row;
}


$stmt->close();


/* =========================================================
   STATISTICS
   ========================================================= */

$totalStudents =
    count($records);

$started =
    0;

$completed =
    0;

$submitted =
    0;


foreach ($records as $record) {

    if (
        !empty($record["started_at"])
    ) {

        $started++;
    }


    if (
        ($record["progress_status"] ?? "")
        === "completed"
    ) {

        $completed++;
    }


    if (
        !empty($record["submitted_at"])
    ) {

        $submitted++;
    }
}


$notStarted =
    $totalStudents - $started;


$completionRate =
    $totalStudents > 0
    ? round(
        ($completed / $totalStudents) * 100
    )
    : 0;


$submissionRate =
    $totalStudents > 0
    ? round(
        ($submitted / $totalStudents) * 100
    )
    : 0;


/* =========================================================
   URL HELPER
   ========================================================= */

function buildMonitorUrl(
    $lectureId = null
) {

    $params =
        $_GET;


    if ($lectureId !== null) {

        if ($lectureId > 0) {

            $params["lecture_id"] =
                $lectureId;
        } else {

            unset(
                $params["lecture_id"]
            );
        }
    }


    return "?" .
        http_build_query(
            $params
        );
}

?>


<!DOCTYPE html>

<html lang="en">


<?php include 'globals/head.php'; ?>


<body>


    <?php include 'globals/sidebar.php'; ?>

    <?php include 'globals/topbar.php'; ?>


    <main class="main-content">


        <div class="content-wrapper">


            <!-- =====================================================
             HEADER
        ====================================================== -->

            <div class="page-header">

                <div>

                    <h2>
                        Activity Monitor
                    </h2>

                    <p>
                        Monitor student participation
                        in asynchronous lectures.
                    </p>

                </div>

            </div>


            <!-- =====================================================
             STATISTICS
        ====================================================== -->

            <div class="row g-3 mb-4">


                <!-- TOTAL -->

                <div class="col-xl-3 col-md-6">

                    <div class="monitor-stat-card">

                        <div
                            class="monitor-stat-icon blue">

                            <i
                                class="bi bi-people">
                            </i>

                        </div>

                        <div>

                            <span>
                                Total Students
                            </span>

                            <strong>
                                <?= number_format(
                                    $totalStudents
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- NOT STARTED -->

                <div class="col-xl-3 col-md-6">

                    <div class="monitor-stat-card">

                        <div
                            class="monitor-stat-icon gray">

                            <i
                                class="bi bi-circle">
                            </i>

                        </div>

                        <div>

                            <span>
                                Not Started
                            </span>

                            <strong>
                                <?= number_format(
                                    $notStarted
                                ) ?>
                            </strong>

                        </div>

                    </div>

                </div>


                <!-- COMPLETED -->

                <div class="col-xl-3 col-md-6">

                    <div class="monitor-stat-card">

                        <div
                            class="monitor-stat-icon green">

                            <i
                                class="bi bi-check-circle">
                            </i>

                        </div>

                        <div>

                            <span>
                                Completed
                            </span>

                            <strong>
                                <?= number_format(
                                    $completed
                                ) ?>

                                <small>
                                    (<?= $completionRate ?>%)
                                </small>

                            </strong>

                        </div>

                    </div>

                </div>


                <!-- SUBMITTED -->

                <div class="col-xl-3 col-md-6">

                    <div class="monitor-stat-card">

                        <div
                            class="monitor-stat-icon purple">

                            <i
                                class="bi bi-file-earmark-check">
                            </i>

                        </div>

                        <div>

                            <span>
                                Submitted
                            </span>

                            <strong>
                                <?= number_format(
                                    $submitted
                                ) ?>

                                <small>
                                    (<?= $submissionRate ?>%)
                                </small>

                            </strong>

                        </div>

                    </div>

                </div>


            </div>


            <!-- =====================================================
             FILTERS
        ====================================================== -->

            <div class="filter-card mb-4">


                <form
                    method="GET"
                    action="activity_monitor.php">


                    <div class="row g-3">


                        <!-- ACTIVITY -->

                        <div class="col-lg-4">

                            <label
                                class="form-label">

                                Activity / Lecture

                            </label>


                            <select
                                name="lecture_id"
                                class="form-select">


                                <option value="0">

                                    All Activities

                                </option>


                                <?php foreach (
                                    $lectures
                                    as $item
                                ): ?>

                                    <option
                                        value="<?= (int)$item["id"] ?>"
                                        <?= $lectureId ===
                                            (int)$item["id"]
                                            ? "selected"
                                            : ""
                                        ?>>

                                        <?= htmlspecialchars(
                                            $item["title"]
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>


                        <!-- SEARCH -->

                        <div class="col-lg-4">

                            <label
                                class="form-label">

                                Search Student

                            </label>


                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Student ID, name, email..."
                                value="<?= htmlspecialchars(
                                            $search
                                        ) ?>">

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

                                    All

                                </option>


                                <?php foreach (
                                    $departments
                                    as $dept
                                ): ?>

                                    <option
                                        value="<?= htmlspecialchars(
                                                    $dept
                                                ) ?>"
                                        <?= $department ===
                                            $dept
                                            ? "selected"
                                            : ""
                                        ?>>

                                        <?= htmlspecialchars(
                                            $dept
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>


                        <!-- YEAR -->

                        <div class="col-md-4 col-lg-2">

                            <label
                                class="form-label">

                                Year

                            </label>


                            <select
                                name="year"
                                class="form-select">


                                <option value="">

                                    All

                                </option>


                                <?php foreach (
                                    $years
                                    as $itemYear
                                ): ?>

                                    <option
                                        value="<?= htmlspecialchars(
                                                    $itemYear
                                                ) ?>"
                                        <?= $year ===
                                            $itemYear
                                            ? "selected"
                                            : ""
                                        ?>>

                                        <?= htmlspecialchars(
                                            $itemYear
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>


                        <!-- SECTION -->

                        <div class="col-md-4 col-lg-2">

                            <label
                                class="form-label">

                                Section

                            </label>


                            <select
                                name="section"
                                class="form-select">


                                <option value="">

                                    All

                                </option>


                                <?php foreach (
                                    $sections
                                    as $itemSection
                                ): ?>

                                    <option
                                        value="<?= htmlspecialchars(
                                                    $itemSection
                                                ) ?>"
                                        <?= $section ===
                                            $itemSection
                                            ? "selected"
                                            : ""
                                        ?>>

                                        <?= htmlspecialchars(
                                            $itemSection
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>


                        <!-- BUTTONS -->

                        <div
                            class="col-md-12 col-lg-4 d-flex align-items-end gap-2">


                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i
                                    class="bi bi-funnel me-1">
                                </i>

                                Filter

                            </button>


                            <a
                                href="activity_monitor.php"
                                class="btn btn-outline-secondary">

                                <i
                                    class="bi bi-arrow-counterclockwise">
                                </i>

                            </a>


                        </div>


                    </div>


                </form>


            </div>


            <!-- =====================================================
             MONITOR TABLE
        ====================================================== -->

            <div class="table-card">


                <div class="table-header">


                    <div
                        class="table-header-title">

                        <i
                            class="bi bi-activity me-2 text-primary">
                        </i>

                        Student Activity Records

                    </div>


                    <span
                        class="badge text-bg-primary">

                        <?= number_format(
                            $totalStudents
                        ) ?>

                    </span>


                </div>


                <?php if (
                    count($records) > 0
                ): ?>


                    <div class="table-responsive">


                        <table
                            class="table table-hover monitor-table">


                            <thead
                                class="table-light">


                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Student
                                    </th>

                                    <th>
                                        Activity
                                    </th>

                                    <th>
                                        Access
                                    </th>

                                    <th>
                                        Last Access
                                    </th>

                                    <th>
                                        Lecture
                                    </th>

                                    <th>
                                        Submission
                                    </th>

                                </tr>


                            </thead>


                            <tbody>


                                <?php

                                $number = 1;

                                ?>


                                <?php foreach (
                                    $records
                                    as $record
                                ): ?>


                                    <?php

                                    $studentName =
                                        trim(

                                            $record["first_name"] .
                                                " " .

                                                (
                                                    !empty($record["middle_initial"])
                                                    ?
                                                    $record["middle_initial"] . ". "
                                                    :
                                                    ""
                                                ) .

                                                $record["last_name"] .

                                                (
                                                    !empty($record["extension_name"])
                                                    ?
                                                    " " .
                                                    $record["extension_name"]
                                                    :
                                                    ""
                                                )

                                        );


                                    $progressStatus =
                                        $record["progress_status"]
                                        ?? "";


                                    $submissionStatus =
                                        $record["submission_status"]
                                        ?? "";

                                    ?>


                                    <tr>


                                        <!-- NUMBER -->

                                        <td>

                                            <?= $number++ ?>

                                        </td>


                                        <!-- STUDENT -->

                                        <td>

                                            <div
                                                class="monitor-student-name">

                                                <?= htmlspecialchars(
                                                    $studentName
                                                ) ?>

                                            </div>


                                            <div
                                                class="monitor-student-id">

                                                <?= htmlspecialchars(
                                                    $record["student_id"]
                                                ) ?>

                                                ·

                                                <?= htmlspecialchars(
                                                    $record["year_section"]
                                                ) ?>

                                            </div>

                                        </td>


                                        <!-- ACTIVITY -->

                                        <td>

                                            <div
                                                class="monitor-activity-name">

                                                <?= htmlspecialchars(
                                                    $record["lecture_title"]
                                                ) ?>

                                            </div>


                                            <small
                                                class="text-muted">

                                                <?= htmlspecialchars(
                                                    $record["lecture_department"]
                                                ) ?>

                                            </small>

                                        </td>


                                        <!-- ACCESS -->

                                        <td>

                                            <?php if (
                                                empty($record["started_at"])
                                            ): ?>

                                                <span
                                                    class="status-badge not-started">

                                                    Not Started

                                                </span>

                                            <?php elseif (
                                                $progressStatus ===
                                                "completed"
                                            ): ?>

                                                <span
                                                    class="status-badge completed">

                                                    <i
                                                        class="bi bi-check-circle me-1">
                                                    </i>

                                                    Completed

                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="status-badge in-progress">

                                                    <i
                                                        class="bi bi-play-circle me-1">
                                                    </i>

                                                    In Progress

                                                </span>

                                            <?php endif; ?>


                                            <div
                                                class="monitor-access-count">

                                                <?php if (
                                                    !empty($record["access_count"])
                                                ): ?>

                                                    <?= (int)(
                                                        $record["access_count"]
                                                    ) ?>

                                                    access

                                                <?php endif; ?>

                                            </div>

                                        </td>


                                        <!-- LAST ACCESS -->

                                        <td>

                                            <?php if (
                                                !empty($record["last_accessed_at"])
                                            ): ?>

                                                <div
                                                    class="monitor-date">

                                                    <?= htmlspecialchars(
                                                        date(
                                                            "M d, Y",
                                                            strtotime(
                                                                $record["last_accessed_at"]
                                                            )
                                                        )
                                                    ) ?>

                                                </div>


                                                <div
                                                    class="monitor-time">

                                                    <?= htmlspecialchars(
                                                        date(
                                                            "h:i A",
                                                            strtotime(
                                                                $record["last_accessed_at"]
                                                            )
                                                        )
                                                    ) ?>

                                                </div>

                                            <?php else: ?>

                                                <span
                                                    class="text-muted">

                                                    —

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- LECTURE -->

                                        <td>

                                            <?php if (
                                                $progressStatus ===
                                                "completed"
                                            ): ?>

                                                <span
                                                    class="status-badge completed">

                                                    <i
                                                        class="bi bi-check-lg me-1">
                                                    </i>

                                                    Completed

                                                </span>

                                            <?php elseif (
                                                !empty($record["started_at"])
                                            ): ?>

                                                <span
                                                    class="status-badge in-progress">

                                                    In Progress

                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="status-badge not-started">

                                                    Not Started

                                                </span>

                                            <?php endif; ?>


                                        </td>


                                        <!-- SUBMISSION -->

                                        <td>

                                            <?php if (
                                                !empty($record["submitted_at"])
                                            ): ?>


                                                <span
                                                    class="status-badge submitted">

                                                    <i
                                                        class="bi bi-check-circle me-1">
                                                    </i>

                                                    Submitted

                                                </span>


                                                <div
                                                    class="monitor-date">

                                                    <?= htmlspecialchars(
                                                        date(
                                                            "M d, Y h:i A",
                                                            strtotime(
                                                                $record["submitted_at"]
                                                            )
                                                        )
                                                    ) ?>

                                                </div>


                                                <?php if (
                                                    $record["score"] !== null
                                                ): ?>

                                                    <div
                                                        class="monitor-score">

                                                        Score:

                                                        <strong>

                                                            <?= htmlspecialchars(
                                                                $record["score"]
                                                            ) ?>

                                                        </strong>

                                                    </div>

                                                <?php endif; ?>


                                            <?php else: ?>


                                                <span
                                                    class="status-badge not-submitted">

                                                    Not Submitted

                                                </span>


                                            <?php endif; ?>

                                        </td>


                                    </tr>


                                <?php endforeach; ?>


                            </tbody>


                        </table>


                    </div>


                <?php else: ?>


                    <div
                        class="empty-state">


                        <i
                            class="bi bi-activity">
                        </i>


                        <h5>

                            No activity records found

                        </h5>


                        <p
                            class="mb-0">

                            Try changing your filters
                            or select another activity.

                        </p>


                    </div>


                <?php endif; ?>


            </div>


        </div>


    </main>


    <?php include 'globals/scripts.php'; ?>


</body>

</html>
