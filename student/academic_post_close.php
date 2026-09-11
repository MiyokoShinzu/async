<?php

/* =========================================================
   ACADEMIC POST ACCESS CLOSE
   ETS-Async Learning Portal

   ---------------------------------------------------------
   PURPOSE:
   - Receives the access token
   - Records the closing time
   - Calculates total reading/access duration
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

    exit;
}


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   GET TOKEN
========================================================== */

$accessToken = trim(
    $_POST["access_token"] ?? ""
);

if ($accessToken === "") {

    http_response_code(400);

    exit;
}


/* =========================================================
   CURRENT TIME
========================================================== */

$closedAt = date("Y-m-d H:i:s");


/* =========================================================
   UPDATE ACCESS RECORD
   ---------------------------------------------------------
   TIMESTAMPDIFF calculates the number of seconds between
   opening and closing.
========================================================== */

$sql = "
    UPDATE academic_post_access_logs
    SET
        closed_at = ?,
        duration_seconds = TIMESTAMPDIFF(
            SECOND,
            opened_at,
            ?
        ),
        status = 'closed'
    WHERE access_token = ?
      AND status = 'open'
    LIMIT 1
";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    exit;
}

$stmt->bind_param(
    "sss",
    $closedAt,
    $closedAt,
    $accessToken
);

$stmt->execute();

$stmt->close();


/* =========================================================
   RESPONSE
========================================================== */

http_response_code(204);

exit;
