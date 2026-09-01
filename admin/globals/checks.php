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

$firstName =
    $user["first_name"] ?? "";

$lastName =
    $user["last_name"] ?? "";

$middleInitial =
    $user["middle_initial"] ?? "";

$extensionName =
    $user["extension_name"] ?? "";

$email =
    $user["email"] ?? "";

$username =
    $user["username"] ?? "";

$access =
    $user["access"] ?? "admin";


/* =========================================================
   BUILD FULL NAME
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
   DATABASE CONNECTION
   ========================================================= */

/*
   CHANGE THIS PATH IF YOUR DATABASE CONNECTION
   FILE IS LOCATED SOMEWHERE ELSE.

   Example:
   ../config/db.php
   ../includes/db.php
   ../db.php
*/

require_once "../src/connection.php";


/* =========================================================
   FILTER VALUES
   ========================================================= */

$department =
    trim($_GET["department"] ?? "");

$year =
    trim($_GET["year"] ?? "");

$section =
    trim($_GET["section"] ?? "");

$search =
    trim($_GET["search"] ?? "");


/* =========================================================
   PAGINATION
   ========================================================= */

$recordsPerPage = 10;

$page =
    isset($_GET["page"])
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
    ORDER BY department ASC
";

$departmentResult =
    $mysqli->query($departmentSQL);

if ($departmentResult) {

    while ($row = $departmentResult->fetch_assoc()) {

        if (
            isset($row["department"]) &&
            $row["department"] !== ""
        ) {

            $departments[] =
                $row["department"];
        }
    }
}


/* =========================================================
   GET YEAR / SECTION VALUES
   ========================================================= */

$yearSections = [];

$yearSectionSQL = "
    SELECT DISTINCT year_section
    FROM accounts
    WHERE access = 'student'
    ORDER BY year_section ASC
";

$yearSectionResult =
    $mysqli->query($yearSectionSQL);

if ($yearSectionResult) {

    while ($row = $yearSectionResult->fetch_assoc()) {

        if (
            isset($row["year_section"]) &&
            $row["year_section"] !== ""
        ) {

            $yearSections[] =
                $row["year_section"];
        }
    }
}


/* =========================================================
   EXTRACT YEARS
   ========================================================= */

$years = [];

foreach ($yearSections as $value) {

    /*
       Expected examples:

       1-A
       1-B
       2-A
       3-B
       4-A
    */

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

    $where[] =
        "department = ?";

    $params[] =
        $department;

    $types .= "s";
}


/* =========================================================
   YEAR FILTER
   ========================================================= */

if ($year !== "") {

    /*
       Example:

       year_section = 1-A

       year_section LIKE 1-%
    */

    $where[] =
        "year_section LIKE ?";

    $params[] =
        $year . "-%";

    $types .= "s";
}


/* =========================================================
   SECTION FILTER
   ========================================================= */

if ($section !== "") {

    /*
       Example:

       1-A
       2-A
       3-A

       % - A
    */

    $where[] =
        "year_section LIKE ?";

    $params[] =
        "%-" . $section;

    $types .= "s";
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

    $searchValue =
        "%" . $search . "%";

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

$whereSQL =
    implode(
        " AND ",
        $where
    );


/* =========================================================
   COUNT TOTAL STUDENTS
   ========================================================= */

$countSQL = "
    SELECT COUNT(*) AS total
    FROM accounts
    WHERE $whereSQL
";

$countStmt =
    $mysqli->prepare($countSQL);

if ($types !== "") {

    $countStmt->bind_param(
        $types,
        ...$params
    );
}

$countStmt->execute();

$countResult =
    $countStmt->get_result();

$totalStudents =
    (int) (
        $countResult->fetch_assoc()["total"]
        ?? 0
    );

$countStmt->close();


/* =========================================================
   PAGINATION CALCULATION
   ========================================================= */

$totalPages =
    max(
        1,
        ceil(
            $totalStudents /
                $recordsPerPage
        )
    );

if ($page > $totalPages) {

    $page =
        $totalPages;
}

$offset =
    ($page - 1) *
    $recordsPerPage;


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
        first_name ASC
    LIMIT ? OFFSET ?
";


$studentStmt =
    $mysqli->prepare($studentSQL);


/* =========================================================
   BIND PARAMETERS
   ========================================================= */

$studentParams =
    $params;

$studentParams[] =
    $recordsPerPage;

$studentParams[] =
    $offset;

$studentTypes =
    $types . "ii";


$studentStmt->bind_param(
    $studentTypes,
    ...$studentParams
);


$studentStmt->execute();


$students =
    $studentStmt->get_result();


/* =========================================================
   FUNCTION FOR FILTER URL
   ========================================================= */

function buildPageUrl($page)
{

    $params =
        $_GET;

    $params["page"] =
        $page;

    return "?" .
        http_build_query($params);
}
