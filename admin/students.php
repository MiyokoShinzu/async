
<?php

/* =========================================================
   ADMIN STUDENTS
   ETS-Async Learning Portal
   ========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION CHECK
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
   GET ADMIN USER
   ========================================================= */

$user = $_SESSION["user"];


/* =========================================================
   USER DATA
   ========================================================= */

$firstName = $user["first_name"] ?? "";
$lastName = $user["last_name"] ?? "";
$middleInitial = $user["middle_initial"] ?? "";
$extensionName = $user["extension_name"] ?? "";

$email = $user["email"] ?? "";
$username = $user["username"] ?? "";
$access = $user["access"] ?? "admin";


/* =========================================================
   BUILD FULL NAME
   ========================================================= */

$fullName = trim(
    $firstName . " " .
        (
            $middleInitial !== ""
            ? $middleInitial . ". "
            : ""
        ) .
        $lastName .
        (
            $extensionName !== ""
            ? " " . $extensionName
            : ""
        )
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
   DATABASE CONNECTION
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   FILTER VALUES
   ========================================================= */

$department = trim($_GET["department"] ?? "");
$year       = trim($_GET["year"] ?? "");
$section    = trim($_GET["section"] ?? "");
$search     = trim($_GET["search"] ?? "");


/* =========================================================
   PAGINATION
   ========================================================= */

$recordsPerPage = 10;

$page = isset($_GET["page"])
    ? (int) $_GET["page"]
    : 1;

if ($page < 1) {
    $page = 1;
}


/* =========================================================
   GET DEPARTMENTS
   ========================================================= */

$departments = [];

$departmentSQL = "
    SELECT DISTINCT department
    FROM accounts
    WHERE access = 'student'
      AND department IS NOT NULL
      AND department <> ''
    ORDER BY department ASC
";

$departmentResult = $mysqli->query($departmentSQL);

if ($departmentResult) {

    while ($row = $departmentResult->fetch_assoc()) {

        $dept = trim($row["department"] ?? "");

        if ($dept !== "") {
            $departments[] = $dept;
        }
    }

    $departmentResult->free();
}


/* =========================================================
   GET YEAR / SECTION VALUES
   ========================================================= */

$yearSections = [];

$yearSectionSQL = "
    SELECT DISTINCT year_section
    FROM accounts
    WHERE access = 'student'
      AND year_section IS NOT NULL
      AND year_section <> ''
    ORDER BY year_section ASC
";

$yearSectionResult = $mysqli->query($yearSectionSQL);

if ($yearSectionResult) {

    while ($row = $yearSectionResult->fetch_assoc()) {

        $value = trim($row["year_section"] ?? "");

        if ($value !== "") {
            $yearSections[] = $value;
        }
    }

    $yearSectionResult->free();
}


/* =========================================================
   EXTRACT YEARS AND SECTIONS
   ========================================================= */

$years = [];
$sections = [];

foreach ($yearSections as $value) {

    /*
     * Expected format examples:
     *
     * 1-A
     * 2-B
     * 3-C
     * 4-D
     *
     * Also supports:
     *
     * 1 - A
     * 2 - B
     */

    $normalized = preg_replace('/\s*-\s*/', '-', $value);

    if (strpos($normalized, "-") !== false) {

        $parts = explode("-", $normalized, 2);

        $itemYear = trim($parts[0]);
        $itemSection = trim($parts[1]);

        if ($itemYear !== "") {
            $years[] = $itemYear;
        }

        if ($itemSection !== "") {
            $sections[] = $itemSection;
        }
    } else {

        /*
         * Fallback if the database contains
         * values such as "1A", "2B", etc.
         */

        if (preg_match('/^([0-9]+)\s*(.+)$/', $normalized, $matches)) {

            $itemYear = trim($matches[1]);
            $itemSection = trim($matches[2]);

            if ($itemYear !== "") {
                $years[] = $itemYear;
            }

            if ($itemSection !== "") {
                $sections[] = $itemSection;
            }
        }
    }
}


/* =========================================================
   CLEAN YEAR / SECTION ARRAYS
   ========================================================= */

$years = array_values(array_unique($years));
$sections = array_values(array_unique($sections));

usort($years, function ($a, $b) {

    if (is_numeric($a) && is_numeric($b)) {
        return (int)$a <=> (int)$b;
    }

    return strcasecmp($a, $b);
});

usort($sections, function ($a, $b) {
    return strcasecmp($a, $b);
});


/* =========================================================
   BUILD STUDENT QUERY
   ========================================================= */

$where = [
    "access = 'student'"
];

$params = [];
$types = "";


/* =========================================================
   DEPARTMENT FILTER
   ========================================================= */

if ($department !== "") {

    $where[] = "department = ?";

    $params[] = $department;

    $types .= "s";
}


/* =========================================================
   YEAR FILTER
   ========================================================= */

if ($year !== "") {

    /*
     * Matches:
     *
     * 1-A
     * 1-B
     * 1-C
     *
     * but not:
     *
     * 10-A
     */

    $where[] = "
        (
            year_section = ?
            OR year_section LIKE ?
        )
    ";

    $params[] = $year;
    $params[] = $year . "-%";

    $types .= "ss";
}


/* =========================================================
   SECTION FILTER
   ========================================================= */

if ($section !== "") {

    /*
     * Matches:
     *
     * 1-A
     * 2-A
     * 3-A
     *
     * but avoids matching random text
     * containing the section.
     */

    $where[] = "
        (
            year_section = ?
            OR year_section LIKE ?
        )
    ";

    $params[] = $section;
    $params[] = "%-" . $section;

    $types .= "ss";
}


/* =========================================================
   SEARCH FILTER
   ========================================================= */

if ($search !== "") {

    $where[] = "
        (
            student_id LIKE ?
            OR first_name LIKE ?
            OR last_name LIKE ?
            OR email LIKE ?
            OR username LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "sssss";
}


/* =========================================================
   WHERE CLAUSE
   ========================================================= */

$whereSQL = implode(" AND ", $where);


/* =========================================================
   COUNT TOTAL STUDENTS
   ========================================================= */

$countSQL = "
    SELECT COUNT(*) AS total
    FROM accounts
    WHERE $whereSQL
";

$countStmt = $mysqli->prepare($countSQL);

if (!$countStmt) {

    die("Count query error: " .
        htmlspecialchars($mysqli->error));
}


if ($types !== "") {

    $countStmt->bind_param(
        $types,
        ...$params
    );
}


$countStmt->execute();

$countResult = $countStmt->get_result();

$countRow = $countResult->fetch_assoc();

$totalStudents = (int)($countRow["total"] ?? 0);

$countStmt->close();


/* =========================================================
   PAGINATION CALCULATION
   ========================================================= */

$totalPages = max(
    1,
    (int)ceil(
        $totalStudents / $recordsPerPage
    )
);


if ($page > $totalPages) {
    $page = $totalPages;
}


$offset = ($page - 1) * $recordsPerPage;


/* =========================================================
   GET STUDENTS
   ========================================================= */

$studentSQL = "
    SELECT
        id,
        last_name,
        first_name,
        middle_initial,
        extension_name,
        department,
        year_section,
        student_id,
        email,
        username,
        created_at,
        profile_photo
    FROM accounts
    WHERE $whereSQL
    ORDER BY
        last_name ASC,
        first_name ASC,
        id ASC
    LIMIT ? OFFSET ?
";


$studentStmt = $mysqli->prepare($studentSQL);

if (!$studentStmt) {

    die("Student query error: " .
        htmlspecialchars($mysqli->error));
}


/* =========================================================
   BIND STUDENT PARAMETERS
   ========================================================= */

$studentParams = $params;

$studentParams[] = $recordsPerPage;
$studentParams[] = $offset;

$studentTypes = $types . "ii";


$studentStmt->bind_param(
    $studentTypes,
    ...$studentParams
);


$studentStmt->execute();

$students = $studentStmt->get_result();


/* =========================================================
   BUILD PAGINATION URL
   ========================================================= */

function buildPageUrl($page)
{
    $params = $_GET;

    $params["page"] = $page;

    return "?" . http_build_query($params);
}

?>


<!DOCTYPE html>

<html lang="en">

<?php include 'globals/head.php'; ?>


<style>
    /* =========================================================
   STUDENTS PAGE
   ========================================================= */

    :root {

        --student-primary: #0B4F8A;
        --student-primary-dark: #083B66;
        --student-primary-light: #EAF3FA;

        --student-text: #212529;
        --student-muted: #6C757D;

        --student-border: #E2E8F0;
        --student-bg: #F6F8FB;
        --student-white: #FFFFFF;

        --student-success: #198754;

        --student-shadow-sm:
            0 3px 12px rgba(0, 0, 0, 0.05);

        --student-shadow-md:
            0 10px 30px rgba(0, 0, 0, 0.08);

        --student-radius: 12px;
    }


    /* =========================================================
   MAIN PAGE
   ========================================================= */

    .main-content {

        background:
            linear-gradient(180deg,
                #F8FAFC 0%,
                #FFFFFF 100%);

        min-height: 100vh;
    }

    .content-wrapper {
        width: 100%;
    }


    /* =========================================================
   PAGE HEADER
   ========================================================= */

    .page-header {

        margin-bottom: 24px;

        animation:
            studentFadeDown .6s ease both;
    }

    .page-header h2 {

        color:
            var(--student-primary-dark);

        font-size: 28px;

        font-weight: 700;

        margin-bottom: 5px;

        letter-spacing: -.3px;
    }

    .page-header h2::before {

        content: "";

        display: inline-block;

        width: 5px;

        height: 27px;

        margin-right: 10px;

        vertical-align: -4px;

        background:
            linear-gradient(180deg,
                var(--student-primary),
                var(--student-primary-dark));

        border-radius: 5px;
    }

    .page-header p {

        margin:
            0 0 0 15px;

        color:
            var(--student-muted);

        font-size: 14px;
    }


    /* =========================================================
   FILTER CARD
   ========================================================= */

    .filter-card {

        background:
            var(--student-white);

        border:
            1px solid var(--student-border);

        border-radius:
            var(--student-radius);

        padding:
            22px;

        margin-bottom:
            24px;

        box-shadow:
            var(--student-shadow-sm);

        animation:
            studentFadeUp .65s ease .1s both;

        transition:
            box-shadow .3s ease,
            border-color .3s ease;
    }

    .filter-card:hover {

        border-color:
            rgba(11, 79, 138, .18);

        box-shadow:
            var(--student-shadow-md);
    }


    /* =========================================================
   FORM LABELS
   ========================================================= */

    .filter-card .form-label {

        color:
            #344054;

        font-size:
            13px;

        font-weight:
            600;

        margin-bottom:
            7px;
    }


    /* =========================================================
   FORM CONTROLS
   ========================================================= */

    .filter-card .form-control,
    .filter-card .form-select {

        min-height:
            44px;

        border:
            1px solid #D9E0E7;

        border-radius:
            8px;

        color:
            var(--student-text);

        font-size:
            14px;

        background-color:
            #FFFFFF;

        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            transform .2s ease;
    }

    .filter-card .form-control::placeholder {
        color: #98A2B3;
    }

    .filter-card .form-control:hover,
    .filter-card .form-select:hover {
        border-color: #B8C5D1;
    }

    .filter-card .form-control:focus,
    .filter-card .form-select:focus {

        border-color:
            var(--student-primary);

        box-shadow:
            0 0 0 3px rgba(11, 79, 138, .10);

        outline:
            none;
    }


    /* =========================================================
   FILTER BUTTON
   ========================================================= */

    .filter-card .btn-primary {

        min-height:
            44px;

        background:
            linear-gradient(135deg,
                var(--student-primary),
                var(--student-primary-dark));

        border:
            none;

        border-radius:
            8px;

        padding:
            0 18px;

        font-size:
            14px;

        font-weight:
            600;

        box-shadow:
            0 4px 12px rgba(11, 79, 138, .18);

        transition:
            transform .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .filter-card .btn-primary:hover {

        transform:
            translateY(-2px);

        box-shadow:
            0 7px 18px rgba(11, 79, 138, .25);
    }

    .filter-card .btn-primary:active {

        transform:
            translateY(0);
    }


    /* =========================================================
   RESET BUTTON
   ========================================================= */

    .filter-card .btn-outline-secondary {

        min-height:
            44px;

        min-width:
            44px;

        border-radius:
            8px;

        transition:
            all .2s ease;
    }

    .filter-card .btn-outline-secondary:hover {

        color:
            var(--student-primary);

        border-color:
            var(--student-primary);

        background:
            var(--student-primary-light);

        transform:
            translateY(-2px);
    }


    /* =========================================================
   TABLE CARD
   ========================================================= */

    .table-card {

        background:
            var(--student-white);

        border:
            1px solid var(--student-border);

        border-radius:
            var(--student-radius);

        overflow:
            hidden;

        box-shadow:
            var(--student-shadow-sm);

        animation:
            studentFadeUp .7s ease .2s both;
    }


    /* =========================================================
   TABLE HEADER
   ========================================================= */

    .table-header {

        display:
            flex;

        align-items:
            center;

        justify-content:
            space-between;

        padding:
            18px 22px;

        background:
            linear-gradient(180deg,
                #FFFFFF,
                #FBFCFE);

        border-bottom:
            1px solid var(--student-border);
    }

    .table-header-title {

        display:
            flex;

        align-items:
            center;

        color:
            #344054;

        font-size:
            15px;

        font-weight:
            700;
    }

    .table-header-title i {

        font-size:
            18px;

        color:
            var(--student-primary);
    }

    .table-header .badge {

        min-width:
            34px;

        padding:
            6px 10px;

        border-radius:
            20px;

        font-size:
            12px;

        font-weight:
            600;
    }


    /* =========================================================
   TABLE
   ========================================================= */

    .student-table {

        width:
            100%;

        margin:
            0;

        vertical-align:
            middle;

        border-collapse:
            separate;

        border-spacing:
            0;
    }

    .student-table thead th {

        background:
            #F8FAFC;

        color:
            #667085;

        border-bottom:
            1px solid var(--student-border);

        padding:
            14px;

        font-size:
            11px;

        font-weight:
            700;

        letter-spacing:
            .5px;

        text-transform:
            uppercase;

        white-space:
            nowrap;
    }

    .student-table tbody td {

        padding:
            14px;

        border-bottom:
            1px solid #EEF1F4;

        color:
            #475467;

        font-size:
            14px;

        background:
            #FFFFFF;
    }

    .student-table tbody tr:last-child td {
        border-bottom: none;
    }


    /* =========================================================
   TABLE ROW
   ========================================================= */

    .student-table tbody tr {

        transition:
            background-color .25s ease,
            box-shadow .25s ease;
    }

    .student-table tbody tr:hover td {
        background: #F8FBFE;
    }


    /* =========================================================
   NUMBER COLUMN
   ========================================================= */

    .student-table tbody td:first-child {

        color:
            #98A2B3;

        font-size:
            13px;

        font-weight:
            600;
    }


    /* =========================================================
   STUDENT PROFILE
   ========================================================= */

    .student-profile {

        display:
            flex;

        align-items:
            center;

        gap:
            12px;

        min-width:
            230px;
    }


    /* =========================================================
   AVATAR
   ========================================================= */

    .student-avatar {

        position:
            relative;

        width:
            48px;

        height:
            48px;

        min-width:
            48px;

        border-radius:
            50%;

        overflow:
            hidden;

        background:
            linear-gradient(135deg,
                #EAF3FA,
                #DDECF7);

        border:
            2px solid #D7E6F1;

        display:
            flex;

        align-items:
            center;

        justify-content:
            center;

        box-shadow:
            0 3px 8px rgba(11, 79, 138, .08);

        transition:
            transform .3s ease,
            box-shadow .3s ease,
            border-color .3s ease;
    }

    .student-table tbody tr:hover .student-avatar {

        transform:
            scale(1.07);

        border-color:
            var(--student-primary);

        box-shadow:
            0 5px 14px rgba(11, 79, 138, .16);
    }

    .student-avatar img {

        width:
            100%;

        height:
            100%;

        object-fit:
            cover;

        display:
            block;
    }


    /* =========================================================
   AVATAR PLACEHOLDER
   ========================================================= */

    .avatar-placeholder {

        width:
            100%;

        height:
            100%;

        display:
            flex;

        align-items:
            center;

        justify-content:
            center;

        background:
            linear-gradient(135deg,
                #EAF3FA,
                #DCECF7);

        color:
            var(--student-primary);

        font-size:
            21px;
    }

    .avatar-placeholder i {
        opacity: .85;
    }


    /* =========================================================
   STUDENT INFORMATION
   ========================================================= */

    .student-info {
        min-width: 0;
    }

    .student-name {

        max-width:
            250px;

        color:
            #1D2939;

        font-size:
            14px;

        font-weight:
            650;

        white-space:
            nowrap;

        overflow:
            hidden;

        text-overflow:
            ellipsis;

        transition:
            color .2s ease;
    }

    .student-table tbody tr:hover .student-name {
        color:
            var(--student-primary);
    }

    .student-info small {

        display:
            block;

        margin-top:
            2px;

        font-size:
            12px;
    }


    /* =========================================================
   STUDENT ID
   ========================================================= */

    .student-id-badge {

        display:
            inline-flex;

        align-items:
            center;

        padding:
            6px 10px;

        background:
            #F1F7FB;

        color:
            var(--student-primary);

        border:
            1px solid #D9E9F5;

        border-radius:
            7px;

        font-size:
            12px;

        font-weight:
            700;

        white-space:
            nowrap;

        transition:
            all .2s ease;
    }

    .student-table tbody tr:hover .student-id-badge {

        background:
            var(--student-primary-light);

        border-color:
            #BBD7EA;
    }


    /* =========================================================
   YEAR / SECTION BADGE
   ========================================================= */

    .student-table .badge.text-bg-light {

        background:
            #F8FAFC !important;

        color:
            #475467 !important;

        border:
            1px solid #E4E7EC !important;

        border-radius:
            6px;

        padding:
            6px 9px;

        font-size:
            12px;

        font-weight:
            600;
    }


    /* =========================================================
   EMAIL
   ========================================================= */

    .student-email {

        display:
            inline-block;

        max-width:
            220px;

        color:
            #667085;

        font-size:
            13px;

        white-space:
            nowrap;

        overflow:
            hidden;

        text-overflow:
            ellipsis;
    }


    /* =========================================================
   VIEW BUTTON
   ========================================================= */

    .student-view-btn {

        display:
            inline-flex;

        align-items:
            center;

        justify-content:
            center;

        min-width:
            72px;

        border-radius:
            7px;

        font-size:
            13px;

        font-weight:
            600;

        transition:
            all .25s ease;
    }

    .student-view-btn:hover {

        color:
            #FFFFFF;

        background:
            var(--student-primary);

        border-color:
            var(--student-primary);

        transform:
            translateY(-2px);

        box-shadow:
            0 4px 10px rgba(11, 79, 138, .18);
    }

    .student-view-btn i {
        transition:
            transform .25s ease;
    }

    .student-view-btn:hover i {
        transform:
            scale(1.12);
    }


    /* =========================================================
   EMPTY STATE
   ========================================================= */

    .empty-state {

        padding:
            75px 20px;

        text-align:
            center;

        color:
            var(--student-muted);

        animation:
            studentFadeUp .5s ease both;
    }

    .empty-icon {

        width:
            76px;

        height:
            76px;

        margin:
            0 auto 20px;

        border-radius:
            50%;

        display:
            flex;

        align-items:
            center;

        justify-content:
            center;

        background:
            var(--student-primary-light);

        color:
            var(--student-primary);

        font-size:
            31px;

        animation:
            studentFloat 3s ease-in-out infinite;
    }

    .empty-state h5 {

        margin-bottom:
            7px;

        color:
            #344054;

        font-size:
            16px;

        font-weight:
            700;
    }

    .empty-state p {
        font-size: 14px;
    }


    /* =========================================================
   PAGINATION
   ========================================================= */

    .pagination-wrapper {

        padding:
            16px 20px;

        border-top:
            1px solid var(--student-border);

        background:
            #FCFDFE;
    }

    .pagination {
        gap: 4px;
    }

    .pagination .page-link {

        min-width:
            36px;

        height:
            36px;

        display:
            inline-flex;

        align-items:
            center;

        justify-content:
            center;

        border:
            1px solid #E1E6EB;

        border-radius:
            7px !important;

        color:
            var(--student-primary);

        background:
            #FFFFFF;

        font-size:
            13px;

        font-weight:
            600;

        transition:
            all .2s ease;
    }

    .pagination .page-link:hover {

        background:
            var(--student-primary-light);

        border-color:
            var(--student-primary);

        color:
            var(--student-primary);

        transform:
            translateY(-1px);
    }

    .pagination .active .page-link {

        background:
            linear-gradient(135deg,
                var(--student-primary),
                var(--student-primary-dark));

        border-color:
            var(--student-primary);

        color:
            #FFFFFF;

        box-shadow:
            0 3px 8px rgba(11, 79, 138, .18);
    }

    .pagination .disabled .page-link {

        color:
            #98A2B3;

        background:
            #F8FAFC;

        border-color:
            #E7EAEE;

        cursor:
            not-allowed;
    }


    /* =========================================================
   TABLE SCROLLBAR
   ========================================================= */

    .table-responsive {

        scrollbar-width:
            thin;

        scrollbar-color:
            #B8C9D8 #F3F6F8;
    }

    .table-responsive::-webkit-scrollbar {
        height: 7px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #F3F6F8;
    }

    .table-responsive::-webkit-scrollbar-thumb {

        background:
            #B8C9D8;

        border-radius:
            10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background:
            var(--student-primary);
    }


    /* =========================================================
   ANIMATIONS
   ========================================================= */

    @keyframes studentFadeUp {

        from {

            opacity: 0;

            transform:
                translateY(18px);
        }

        to {

            opacity: 1;

            transform:
                translateY(0);
        }
    }


    @keyframes studentFadeDown {

        from {

            opacity: 0;

            transform:
                translateY(-15px);
        }

        to {

            opacity: 1;

            transform:
                translateY(0);
        }
    }


    @keyframes studentFloat {

        0%,
        100% {

            transform:
                translateY(0);
        }

        50% {

            transform:
                translateY(-5px);
        }
    }


    /* =========================================================
   RESPONSIVE — TABLET
   ========================================================= */

    @media (max-width: 991.98px) {

        .filter-card {
            padding: 18px;
        }

        .student-table {
            min-width: 1050px;
        }

        .table-responsive {

            overflow-x:
                auto;

            -webkit-overflow-scrolling:
                touch;
        }

        .table-header {
            padding: 16px 18px;
        }
    }


    /* =========================================================
   RESPONSIVE — MOBILE
   ========================================================= */

    @media (max-width: 767.98px) {

        .page-header {
            margin-bottom: 18px;
        }

        .page-header h2 {
            font-size: 23px;
        }

        .page-header h2::before {

            height: 22px;

            width: 4px;

            vertical-align: -3px;
        }

        .page-header p {

            margin-left: 14px;

            font-size: 13px;
        }

        .filter-card {

            padding: 16px;

            border-radius: 10px;
        }

        .filter-card .row {
            --bs-gutter-y: 12px;
        }

        .filter-card .btn-primary {
            flex: 1;
        }

        .table-card {
            border-radius: 10px;
        }

        .table-header {

            padding: 15px;

            min-height: 58px;
        }

        .table-header-title {
            font-size: 14px;
        }

        .student-table {
            min-width: 1000px;
        }

        .student-profile {
            min-width: 210px;
        }

        .student-avatar {

            width: 44px;

            height: 44px;

            min-width: 44px;
        }

        .student-name {
            max-width: 190px;
        }

        .student-email {
            max-width: 180px;
        }

        .pagination-wrapper {
            padding: 12px;
        }
    }


    /* =========================================================
   RESPONSIVE — SMALL MOBILE
   ========================================================= */

    @media (max-width: 575.98px) {

        .content-wrapper {

            padding-left: 10px;

            padding-right: 10px;
        }

        .page-header h2 {
            font-size: 21px;
        }

        .filter-card {
            padding: 14px;
        }

        .filter-card .form-control,
        .filter-card .form-select {

            min-height: 42px;
        }

        .filter-card .btn-primary,
        .filter-card .btn-outline-secondary {

            min-height: 42px;
        }

        .table-header {
            padding: 13px;
        }

        .table-header .badge {
            min-width: 30px;
        }

        .student-table {
            min-width: 950px;
        }

        .student-avatar {

            width: 42px;

            height: 42px;

            min-width: 42px;
        }

        .student-name {
            max-width: 165px;
        }

        .student-profile {
            min-width: 190px;
        }

        .empty-state {
            padding: 55px 15px;
        }

        .pagination {

            justify-content:
                center !important;

            flex-wrap:
                wrap;
        }
    }


    /* =========================================================
   REDUCED MOTION
   ========================================================= */

    @media (prefers-reduced-motion: reduce) {

        *,
        *::before,
        *::after {

            animation:
                none !important;

            transition:
                none !important;
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


            <!-- =====================================================
                 PAGE HEADER
            ===================================================== -->

            <div class="page-header">

                <h2>
                    Students
                </h2>

                <p>
                    View and manage registered students.
                </p>

            </div>


            <!-- =====================================================
                 FILTERS
            ===================================================== -->

            <div class="filter-card">

                <form
                    method="GET"
                    action="students.php">

                    <div class="row g-3">


                        <!-- SEARCH -->

                        <div class="col-lg-4">

                            <label class="form-label">
                                Search
                            </label>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Name, student ID, email..."
                                value="<?= htmlspecialchars(
                                            $search,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>">

                        </div>


                        <!-- DEPARTMENT -->

                        <div class="col-md-4 col-lg-2">

                            <label class="form-label">
                                Department
                            </label>

                            <select
                                name="department"
                                class="form-select">

                                <option value="">
                                    All Departments
                                </option>

                                <?php foreach ($departments as $dept): ?>

                                    <option
                                        value="<?= htmlspecialchars(
                                                    $dept,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                        <?= ($department === $dept)
                                            ? 'selected'
                                            : '' ?>>

                                        <?= htmlspecialchars(
                                            $dept,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- YEAR -->

                        <div class="col-md-4 col-lg-2">

                            <label class="form-label">
                                Year
                            </label>

                            <select
                                name="year"
                                class="form-select">

                                <option value="">
                                    All Years
                                </option>

                                <?php foreach ($years as $itemYear): ?>

                                    <option
                                        value="<?= htmlspecialchars(
                                                    $itemYear,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                        <?= ($year === $itemYear)
                                            ? 'selected'
                                            : '' ?>>

                                        <?= htmlspecialchars(
                                            $itemYear,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- SECTION -->

                        <div class="col-md-4 col-lg-2">

                            <label class="form-label">
                                Section
                            </label>

                            <select
                                name="section"
                                class="form-select">

                                <option value="">
                                    All Sections
                                </option>

                                <?php foreach ($sections as $itemSection): ?>

                                    <option
                                        value="<?= htmlspecialchars(
                                                    $itemSection,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                        <?= ($section === $itemSection)
                                            ? 'selected'
                                            : '' ?>>

                                        <?= htmlspecialchars(
                                            $itemSection,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- BUTTONS -->

                        <div class="col-md-12 col-lg-2 d-flex align-items-end gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-funnel me-1"></i>

                                Filter

                            </button>


                            <a
                                href="students.php"
                                class="btn btn-outline-secondary"
                                title="Reset filters">

                                <i class="bi bi-arrow-counterclockwise"></i>

                            </a>

                        </div>

                    </div>

                </form>

            </div>


            <!-- =====================================================
                 STUDENT TABLE
            ===================================================== -->

            <div class="table-card">


                <!-- TABLE HEADER -->

                <div class="table-header">

                    <div class="table-header-title">

                        <i class="bi bi-people me-2 text-primary"></i>

                        Student Records

                    </div>


                    <span class="badge text-bg-primary">

                        <?= number_format($totalStudents) ?>

                    </span>

                </div>


                <?php if ($students && $students->num_rows > 0): ?>


                    <!-- =================================================
                         RESPONSIVE TABLE
                    ================================================= -->

                    <div class="table-responsive">

                        <table class="table table-hover student-table">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Student
                                    </th>

                                    <th>
                                        Student ID
                                    </th>

                                    <th>
                                        Department
                                    </th>

                                    <th>
                                        Year / Section
                                    </th>

                                    <th>
                                        Email
                                    </th>

                                    <th>
                                        Created
                                    </th>

                                    <th class="text-center">
                                        Action
                                    </th>

                                </tr>

                            </thead>


                            <tbody>


                                <?php

                                $number = $offset;

                                ?>


                                <?php while ($student = $students->fetch_assoc()): ?>


                                    <?php

                                    /* =================================================
                                       STUDENT NAME
                                    ================================================= */

                                    $studentFirstName =
                                        $student["first_name"] ?? "";

                                    $studentMiddleInitial =
                                        $student["middle_initial"] ?? "";

                                    $studentLastName =
                                        $student["last_name"] ?? "";

                                    $studentExtensionName =
                                        $student["extension_name"] ?? "";


                                    $studentName = trim(

                                        $studentFirstName .
                                            " " .

                                            (
                                                $studentMiddleInitial !== ""
                                                ? $studentMiddleInitial . ". "
                                                : ""
                                            ) .

                                            $studentLastName .

                                            (
                                                $studentExtensionName !== ""
                                                ? " " . $studentExtensionName
                                                : ""
                                            )
                                    );


                                    /* =================================================
                                       PROFILE PHOTO
                                    ================================================= */

                                    $profilePhoto = trim(
                                        $student["profile_photo"] ?? ""
                                    );

                                    $photoUrl = "";


                                    if ($profilePhoto !== "") {

                                        /*
                                         * Normalize slashes.
                                         */

                                        $profilePhoto = str_replace(
                                            "\\",
                                            "/",
                                            $profilePhoto
                                        );

                                        $profilePhoto = ltrim(
                                            $profilePhoto,
                                            "/"
                                        );


                                        /*
                                         * Prevent directory traversal.
                                         */

                                        if (
                                            strpos($profilePhoto, "..") === false
                                        ) {

                                            $extension = strtolower(
                                                pathinfo(
                                                    $profilePhoto,
                                                    PATHINFO_EXTENSION
                                                )
                                            );


                                            $allowedExtensions = [
                                                "jpg",
                                                "jpeg",
                                                "png",
                                                "gif",
                                                "webp"
                                            ];


                                            if (
                                                in_array(
                                                    $extension,
                                                    $allowedExtensions,
                                                    true
                                                )
                                            ) {

                                                /*
                                                 * Physical file path.
                                                 */

                                                $photoPath =
                                                    __DIR__ .
                                                    "/../student/" .
                                                    $profilePhoto;


                                                /*
                                                 * Verify file exists.
                                                 */

                                                if (
                                                    is_file($photoPath) &&
                                                    is_readable($photoPath)
                                                ) {

                                                    /*
                                                     * Browser URL.
                                                     */

                                                    $photoParts = explode(
                                                        "/",
                                                        $profilePhoto
                                                    );

                                                    $encodedParts = [];

                                                    foreach (
                                                        $photoParts
                                                        as $part
                                                    ) {

                                                        $encodedParts[] =
                                                            rawurlencode($part);
                                                    }


                                                    $photoUrl =
                                                        "../student/" .
                                                        implode(
                                                            "/",
                                                            $encodedParts
                                                        );
                                                }
                                            }
                                        }
                                    }


                                    /* =================================================
                                       CREATED DATE
                                    ================================================= */

                                    $createdDate = "";

                                    $createdAt =
                                        $student["created_at"] ?? "";


                                    if ($createdAt !== "") {

                                        $timestamp =
                                            strtotime($createdAt);


                                        if ($timestamp !== false) {

                                            $createdDate =
                                                date(
                                                    "M d, Y",
                                                    $timestamp
                                                );
                                        }
                                    }


                                    /* =================================================
                                       STUDENT ID
                                    ================================================= */

                                    $studentID =
                                        $student["student_id"] ?? "";


                                    /* =================================================
                                       DEPARTMENT
                                    ================================================= */

                                    $studentDepartment =
                                        $student["department"] ?? "";


                                    /* =================================================
                                       YEAR / SECTION
                                    ================================================= */

                                    $studentYearSection =
                                        $student["year_section"] ?? "";


                                    /* =================================================
                                       EMAIL
                                    ================================================= */

                                    $studentEmail =
                                        $student["email"] ?? "";


                                    /* =================================================
                                       ACCOUNT ID
                                    ================================================= */

                                    $studentAccountID =
                                        (int)($student["id"] ?? 0);

                                    ?>


                                    <!-- =================================================
                                         STUDENT ROW
                                    ================================================= -->

                                    <tr>


                                        <!-- NUMBER -->

                                        <td class="align-middle">

                                            <?= ++$number ?>

                                        </td>


                                        <!-- STUDENT PROFILE -->

                                        <td class="align-middle">

                                            <div class="student-profile">


                                                <!-- PROFILE IMAGE -->

                                                <div class="student-avatar">

                                                    <?php if ($photoUrl !== ""): ?>

                                                        <img
                                                            src="<?= htmlspecialchars(
                                                                        $photoUrl,
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>"
                                                            alt="<?= htmlspecialchars(
                                                                        $studentName,
                                                                        ENT_QUOTES,
                                                                        'UTF-8'
                                                                    ) ?>"
                                                            loading="lazy"
                                                            onerror="
                                                                this.style.display='none';
                                                                this.nextElementSibling.style.display='flex';
                                                            ">


                                                        <div
                                                            class="avatar-placeholder"
                                                            style="display:none;">

                                                            <i class="bi bi-person-fill"></i>

                                                        </div>


                                                    <?php else: ?>

                                                        <div class="avatar-placeholder">

                                                            <i class="bi bi-person-fill"></i>

                                                        </div>

                                                    <?php endif; ?>

                                                </div>


                                                <!-- NAME -->

                                                <div class="student-info">

                                                    <div class="student-name">

                                                        <?= htmlspecialchars(
                                                            $studentName,
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        ) ?>

                                                    </div>


                                                    <small class="text-muted">

                                                        Student

                                                    </small>

                                                </div>

                                            </div>

                                        </td>


                                        <!-- STUDENT ID -->

                                        <td class="align-middle">

                                            <span class="student-id-badge">

                                                <?= htmlspecialchars(
                                                    $studentID,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- DEPARTMENT -->

                                        <td class="align-middle">

                                            <?= htmlspecialchars(
                                                $studentDepartment,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>


                                        <!-- YEAR / SECTION -->

                                        <td class="align-middle">

                                            <span class="badge text-bg-light border">

                                                <?= htmlspecialchars(
                                                    $studentYearSection,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- EMAIL -->

                                        <td class="align-middle">

                                            <span class="student-email">

                                                <?= htmlspecialchars(
                                                    $studentEmail,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- CREATED -->

                                        <td class="align-middle">

                                            <?= htmlspecialchars(
                                                $createdDate,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </td>


                                        <!-- ACTION -->

                                        <td class="text-center align-middle">

                                            <?php if ($studentAccountID > 0): ?>

                                                <a
                                                    href="student_view.php?id=<?= $studentAccountID ?>"
                                                    class="btn btn-sm btn-outline-primary student-view-btn">

                                                    <i class="bi bi-eye me-1"></i>

                                                    View

                                                </a>

                                            <?php else: ?>

                                                <span
                                                    class="text-muted small">

                                                    N/A

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                    </tr>


                                <?php endwhile; ?>


                            </tbody>

                        </table>

                    </div>


                <?php else: ?>


                    <!-- =================================================
                         EMPTY STATE
                    ================================================= -->

                    <div class="empty-state">

                        <div class="empty-icon">

                            <i class="bi bi-people"></i>

                        </div>


                        <h5>

                            No students found

                        </h5>


                        <p class="mb-0">

                            No student records match
                            your current filters.

                        </p>

                    </div>


                <?php endif; ?>


                <!-- =================================================
                     PAGINATION
                ================================================= -->

                <?php if ($totalPages > 1): ?>


                    <div class="pagination-wrapper">

                        <nav
                            aria-label="Student pagination">

                            <ul class="pagination justify-content-end mb-0">


                                <!-- PREVIOUS -->

                                <li
                                    class="page-item
                                    <?= $page <= 1
                                        ? "disabled"
                                        : "" ?>">

                                    <?php if ($page > 1): ?>

                                        <a
                                            class="page-link"
                                            href="<?= htmlspecialchars(
                                                        buildPageUrl($page - 1),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                            <i class="bi bi-chevron-left"></i>

                                            <span class="d-none d-sm-inline">
                                                Previous
                                            </span>

                                        </a>

                                    <?php else: ?>

                                        <span class="page-link">

                                            <i class="bi bi-chevron-left"></i>

                                            <span class="d-none d-sm-inline">
                                                Previous
                                            </span>

                                        </span>

                                    <?php endif; ?>

                                </li>


                                <!-- PAGE NUMBERS -->

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


                                <?php if ($startPage > 1): ?>

                                    <li class="page-item">

                                        <a
                                            class="page-link"
                                            href="<?= htmlspecialchars(
                                                        buildPageUrl(1),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                            1

                                        </a>

                                    </li>


                                    <?php if ($startPage > 2): ?>

                                        <li class="page-item disabled">

                                            <span class="page-link">
                                                ...
                                            </span>

                                        </li>

                                    <?php endif; ?>

                                <?php endif; ?>


                                <?php for (
                                    $i = $startPage;
                                    $i <= $endPage;
                                    $i++
                                ): ?>

                                    <li
                                        class="page-item
                                        <?= $i === $page
                                            ? "active"
                                            : "" ?>">

                                        <a
                                            class="page-link"
                                            href="<?= htmlspecialchars(
                                                        buildPageUrl($i),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                            <?= $i ?>

                                        </a>

                                    </li>

                                <?php endfor; ?>


                                <?php if ($endPage < $totalPages): ?>

                                    <?php if ($endPage < $totalPages - 1): ?>

                                        <li class="page-item disabled">

                                            <span class="page-link">
                                                ...
                                            </span>

                                        </li>

                                    <?php endif; ?>


                                    <li class="page-item">

                                        <a
                                            class="page-link"
                                            href="<?= htmlspecialchars(
                                                        buildPageUrl($totalPages),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                            <?= $totalPages ?>

                                        </a>

                                    </li>

                                <?php endif; ?>


                                <!-- NEXT -->

                                <li
                                    class="page-item
                                    <?= $page >= $totalPages
                                        ? "disabled"
                                        : "" ?>">

                                    <?php if ($page < $totalPages): ?>

                                        <a
                                            class="page-link"
                                            href="<?= htmlspecialchars(
                                                        buildPageUrl($page + 1),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>">

                                            <span class="d-none d-sm-inline">
                                                Next
                                            </span>

                                            <i class="bi bi-chevron-right"></i>

                                        </a>

                                    <?php else: ?>

                                        <span class="page-link">

                                            <span class="d-none d-sm-inline">
                                                Next
                                            </span>

                                            <i class="bi bi-chevron-right"></i>

                                        </span>

                                    <?php endif; ?>

                                </li>


                            </ul>

                        </nav>

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


<?php

/* =========================================================
   CLOSE DATABASE RESOURCES
   ========================================================= */

$studentStmt->close();

$mysqli->close();

?>
