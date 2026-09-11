
<?php

/* =========================================================
   ACADEMIC POST SAVE / UNSAVE
   ETS-Async Learning Portal

   Purpose:
   - Save an academic post for the logged-in student
   - Remove an academic post from Saved Posts
   - Uses the academic_post_saved table
   - Returns JSON response for AJAX requests
   ========================================================= */


/* =========================================================
   START SESSION
========================================================== */

session_start();


/* =========================================================
   JSON RESPONSE HEADER
========================================================== */

header(
    "Content-Type: application/json; charset=UTF-8"
);


/* =========================================================
   AUTHENTICATION
========================================================== */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);

    exit;
}


/* =========================================================
   CURRENT USER
========================================================== */

$user =
    $_SESSION["user"];


/* =========================================================
   STUDENT ID
========================================================== */

$studentId =
    trim(
        $user["student_id"] ?? ""
    );


/* =========================================================
   VERIFY STUDENT ID
========================================================== */

if ($studentId === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Student ID is missing."
    ]);

    exit;
}


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   VERIFY REQUEST METHOD
========================================================== */

if (
    $_SERVER["REQUEST_METHOD"] !== "POST"
) {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


/* =========================================================
   GET POST ID
========================================================== */

$postId =
    filter_input(
        INPUT_POST,
        "post_id",
        FILTER_VALIDATE_INT
    );


/* =========================================================
   VALIDATE POST ID
========================================================== */

if (
    !$postId ||
    $postId < 1
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid academic post."
    ]);

    exit;
}


/* =========================================================
   VERIFY THAT THE ACADEMIC POST EXISTS
   AND IS ACTIVE
========================================================== */

$postStmt =
    $mysqli->prepare("

        SELECT
            id

        FROM academic_posts

        WHERE id = ?

          AND status = 1

        LIMIT 1

    ");


/* =========================================================
   CHECK PREPARED STATEMENT
========================================================== */

if (!$postStmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to verify academic post."
    ]);

    exit;
}


/* =========================================================
   BIND POST ID
========================================================== */

$postStmt->bind_param(
    "i",
    $postId
);


/* =========================================================
   EXECUTE
========================================================== */

$postStmt->execute();


/* =========================================================
   GET RESULT
========================================================== */

$postResult =
    $postStmt->get_result();


/* =========================================================
   CHECK IF POST EXISTS
========================================================== */

if (
    !$postResult->fetch_assoc()
) {

    $postStmt->close();

    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Academic post not found."
    ]);

    exit;
}


/* =========================================================
   CLOSE POST STATEMENT
========================================================== */

$postStmt->close();


/* =========================================================
   CHECK WHETHER POST IS ALREADY SAVED
========================================================== */

$checkStmt =
    $mysqli->prepare("

        SELECT
            id

        FROM academic_post_saved

        WHERE post_id = ?

          AND student_id = ?

        LIMIT 1

    ");


/* =========================================================
   CHECK PREPARED STATEMENT
========================================================== */

if (!$checkStmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to check saved post."
    ]);

    exit;
}


/* =========================================================
   BIND VALUES
========================================================== */

$checkStmt->bind_param(
    "is",
    $postId,
    $studentId
);


/* =========================================================
   EXECUTE
========================================================== */

$checkStmt->execute();


/* =========================================================
   GET RESULT
========================================================== */

$checkResult =
    $checkStmt->get_result();


/* =========================================================
   GET EXISTING SAVED RECORD
========================================================== */

$existing =
    $checkResult->fetch_assoc();


/* =========================================================
   CLOSE CHECK STATEMENT
========================================================== */

$checkStmt->close();


/* =========================================================
   IF ALREADY SAVED
   REMOVE FROM SAVED POSTS
========================================================== */

if ($existing) {


    /* =====================================================
       GET SAVED RECORD ID
    ====================================================== */

    $savedId =
        (int)$existing["id"];


    /* =====================================================
       DELETE SAVED RECORD
    ====================================================== */

    $deleteStmt =
        $mysqli->prepare("

            DELETE FROM academic_post_saved

            WHERE id = ?

            LIMIT 1

        ");


    /* =====================================================
       CHECK DELETE STATEMENT
    ====================================================== */

    if (!$deleteStmt) {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Unable to remove saved post."
        ]);

        exit;
    }


    /* =====================================================
       BIND SAVED ID
    ====================================================== */

    $deleteStmt->bind_param(
        "i",
        $savedId
    );


    /* =====================================================
       EXECUTE DELETE
    ====================================================== */

    if (
        !$deleteStmt->execute()
    ) {

        $deleteStmt->close();

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Unable to remove saved post."
        ]);

        exit;
    }


    /* =====================================================
       CLOSE DELETE STATEMENT
    ====================================================== */

    $deleteStmt->close();


    /* =====================================================
       SUCCESS RESPONSE
    ====================================================== */

    echo json_encode([

        "success" => true,

        "saved" => false,

        "message" =>
        "Post removed from Saved Posts."

    ]);

    exit;
}


/* =========================================================
   POST IS NOT SAVED
   INSERT INTO SAVED POSTS
========================================================== */

$insertStmt =
    $mysqli->prepare("

        INSERT INTO academic_post_saved (

            post_id,

            student_id

        )

        VALUES (

            ?,

            ?

        )

    ");


/* =========================================================
   CHECK INSERT STATEMENT
========================================================== */

if (!$insertStmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to save the post."
    ]);

    exit;
}


/* =========================================================
   BIND VALUES
========================================================== */

$insertStmt->bind_param(
    "is",
    $postId,
    $studentId
);


/* =========================================================
   EXECUTE INSERT
========================================================== */

if (
    !$insertStmt->execute()
) {

    $error =
        $insertStmt->error;


    $insertStmt->close();


    /* =====================================================
       HANDLE DUPLICATE RECORD
       UNIQUE KEY:
       unique_student_post
    ====================================================== */

    if (
        $mysqli->errno === 1062
    ) {

        echo json_encode([

            "success" => true,

            "saved" => true,

            "message" =>
            "Post is already saved."

        ]);

        exit;
    }


    /* =====================================================
       GENERAL DATABASE ERROR
    ====================================================== */

    http_response_code(500);

    echo json_encode([

        "success" => false,

        "message" =>
        "Unable to save the post."

    ]);

    exit;
}


/* =========================================================
   CLOSE INSERT STATEMENT
========================================================== */

$insertStmt->close();


/* =========================================================
   SUCCESS RESPONSE
========================================================== */

echo json_encode([

    "success" => true,

    "saved" => true,

    "message" =>
    "Post saved successfully."

]);

exit;

?>
