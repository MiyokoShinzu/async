<?php
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
    $_SESSION["user"]["access"] !== "quiz_master"
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

?>