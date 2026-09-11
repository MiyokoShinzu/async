<?php

/* =========================================================
   ACADEMIC POST ACCESS START
   ETS-Async Learning Portal

   ---------------------------------------------------------
   PURPOSE:
   - Records every click/open of an Academic Post
   - Creates a unique access token
   - Records the opening time
   - Redirects the student to the actual PHP page
========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION
========================================================== */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {

    http_response_code(403);

    exit("Unauthorized.");

}


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   GET STUDENT INFORMATION
========================================================== */

$user = $_SESSION["user"];

$studentId = $user["student_id"] ?? null;


/* =========================================================
   GET POST ID
========================================================== */

$postId = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$postId) {

    http_response_code(400);

    exit("Invalid academic post.");

}


/* =========================================================
   GET ACADEMIC POST
========================================================== */

$sql = "
    SELECT
        id,
        url,
        status
    FROM academic_posts
    WHERE id = ?
      AND status = 1
    LIMIT 1
";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    exit("Database error.");

}

$stmt->bind_param(
    "i",
    $postId
);

$stmt->execute();

$result = $stmt->get_result();

$post = $result->fetch_assoc();

$stmt->close();


/* =========================================================
   CHECK POST
========================================================== */

if (!$post) {

    http_response_code(404);

    exit("Academic post not found.");

}


/* =========================================================
   GENERATE ACCESS TOKEN
   ---------------------------------------------------------
   Each click gets its own unique token.
========================================================== */

$accessToken = bin2hex(
    random_bytes(32)
);


/* =========================================================
   CURRENT TIME
========================================================== */

$openedAt = date("Y-m-d H:i:s");


/* =========================================================
   IP ADDRESS
========================================================== */

$ipAddress = $_SERVER["REMOTE_ADDR"] ?? null;


/* =========================================================
   USER AGENT
========================================================== */

$userAgent = $_SERVER["HTTP_USER_AGENT"] ?? null;


/* =========================================================
   INSERT ACCESS RECORD
========================================================== */

$sql = "
    INSERT INTO academic_post_access_logs (
        post_id,
        student_id,
        opened_at,
        status,
        access_token,
        ip_address,
        user_agent
    )
    VALUES (?, ?, ?, 'open', ?, ?, ?)
";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    exit("Unable to create access record.");

}

$stmt->bind_param(
    "isssss",
    $postId,
    $studentId,
    $openedAt,
    $accessToken,
    $ipAddress,
    $userAgent
);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    exit("Unable to record academic post access.");

}

$stmt->close();


/* =========================================================
   GET PHP PAGE
========================================================== */

$page = $post["url"];


/* =========================================================
   REDIRECT
   ---------------------------------------------------------
   The access token is passed to the academic PHP page.

   Example:

   z_transform.php?academic_access=ABC123...
========================================================== */

$separator = (
    strpos($page, "?") !== false
)
    ? "&"
    : "?";

$redirectUrl =
    $page .
    $separator .
    "academic_access=" .
    urlencode($accessToken);


/* =========================================================
   REDIRECT STUDENT
========================================================== */

header(
    "Location: " . $redirectUrl
);

exit;