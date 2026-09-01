<?php

/* =========================================================
   STUDENT REGISTRATION API - GET VERSION
   ========================================================= */

header("Content-Type: application/json; charset=UTF-8");


/* =========================================================
   DATABASE CONNECTION
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   ONLY ALLOW GET REQUEST
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only GET requests are allowed."
    ]);

    exit;
}


/* =========================================================
   GET INPUT VALUES
   ========================================================= */

$last_name =
    trim($_GET["last_name"] ?? "");

$first_name =
    trim($_GET["first_name"] ?? "");

$middle_initial =
    strtoupper(
        trim($_GET["middle_initial"] ?? "")
    );

$extension_name =
    trim($_GET["extension_name"] ?? "");

$department =
    trim($_GET["department"] ?? "");

$year_section =
    trim($_GET["year_section"] ?? "");

$student_id =
    trim($_GET["student_id"] ?? "");

$email =
    strtolower(
        trim($_GET["email"] ?? "")
    );

$username =
    trim($_GET["username"] ?? "");

$password =
    $_GET["password"] ?? "";


/* =========================================================
   ALLOWED EXTENSION NAMES
   ========================================================= */

$allowed_extensions = [
    "",
    "Jr",
    "Sr",
    "II",
    "III",
    "IV"
];

$extension_valid = false;

foreach ($allowed_extensions as $allowed) {

    if (
        strcasecmp(
            $extension_name,
            $allowed
        ) === 0
    ) {

        $extension_name = $allowed;

        $extension_valid = true;

        break;
    }
}


if (!$extension_valid) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid extension name."
    ]);

    exit;
}


/* =========================================================
   REQUIRED FIELD CHECK
   ========================================================= */

if (
    $last_name === "" ||
    $first_name === "" ||
    $department === "" ||
    $year_section === "" ||
    $student_id === "" ||
    $email === "" ||
    $username === "" ||
    $password === ""
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please complete all required fields."
    ]);

    exit;
}


/* =========================================================
   NAME VALIDATION
   ========================================================= */

if (
    strlen($last_name) < 2 ||
    strlen($last_name) > 100
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid last name."
    ]);

    exit;
}


if (
    strlen($first_name) < 2 ||
    strlen($first_name) > 100
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid first name."
    ]);

    exit;
}


/* =========================================================
   MIDDLE INITIAL VALIDATION
   ========================================================= */

if (
    $middle_initial !== "" &&
    !preg_match(
        "/^[A-Z]{1,2}$/",
        $middle_initial
    )
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid middle initial."
    ]);

    exit;
}


/* =========================================================
   EMAIL VALIDATION
   ========================================================= */

if (
    strlen($email) > 150 ||
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);

    exit;
}


/* =========================================================
   USERNAME VALIDATION
   ========================================================= */

if (
    strlen($username) < 4 ||
    strlen($username) > 50
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" =>
        "Username must contain 4 to 50 characters."
    ]);

    exit;
}


if (
    !preg_match(
        "/^[A-Za-z0-9_.-]+$/",
        $username
    )
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" =>
        "Username may only contain letters, numbers, underscores, periods, and hyphens."
    ]);

    exit;
}


/* =========================================================
   PASSWORD VALIDATION
   ========================================================= */

if (
    strlen($password) < 8 ||
    strlen($password) > 255
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" =>
        "Password must contain 8 to 255 characters."
    ]);

    exit;
}


/* =========================================================
   STUDENT ID VALIDATION
   ========================================================= */

if (
    strlen($student_id) < 3 ||
    strlen($student_id) > 50
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" =>
        "Please enter a valid student ID."
    ]);

    exit;
}


/* =========================================================
   DEPARTMENT VALIDATION
   ========================================================= */

$allowed_departments = [
    "Computer Engineering", "Civil Engineering", "Electrical Engineering",
    "Electronics and Communications Engineering", "Geodetic Engineering", "Architecture", "Chemical Engineering"
];


if (
    !in_array(
        $department,
        $allowed_departments,
        true
    )
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" =>
        "Invalid department selected."
    ]);

    exit;
}


/* =========================================================
   YEAR & SECTION VALIDATION
   ========================================================= */

$allowed_sections = [

    "1-A",
    "1-B",
    "1-C",

    "2-A",
    "2-B",
    "2-C",

    "3-A",
    "3-B",
    "3-C",

    "4-A",
    "4-B",
    "4-C"

];


if (
    !in_array(
        $year_section,
        $allowed_sections,
        true
    )
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" =>
        "Invalid year and section."
    ]);

    exit;
}


/* =========================================================
   CHECK EXISTING ACCOUNT
   ========================================================= */

$stmt = $mysqli->prepare("
    SELECT
        username,
        email,
        student_id
    FROM accounts
    WHERE username = ?
       OR email = ?
       OR student_id = ?
    LIMIT 1
");


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" =>
        "Database error."
    ]);

    exit;
}


$stmt->bind_param(
    "sss",
    $username,
    $email,
    $student_id
);


if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" =>
        "Unable to check account information."
    ]);

    exit;
}


$result =
    $stmt->get_result();


if ($result->num_rows > 0) {

    $existing =
        $result->fetch_assoc();


    if (
        strtolower(
            $existing["username"]
        ) ===
        strtolower($username)
    ) {

        $message =
            "Username is already registered.";
    } elseif (
        strtolower(
            $existing["email"]
        ) ===
        strtolower($email)
    ) {

        $message =
            "Email address is already registered.";
    } else {

        $message =
            "Student ID is already registered.";
    }


    $stmt->close();

    http_response_code(409);

    echo json_encode([
        "success" => false,
        "message" => $message
    ]);

    exit;
}


$stmt->close();


/* =========================================================
   HASH PASSWORD
   ========================================================= */

$password_hash =
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );


if ($password_hash === false) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" =>
        "Unable to secure password."
    ]);

    exit;
}


/* =========================================================
   DEFAULT ACCESS
   ========================================================= */

$access = "student";


/* =========================================================
   INSERT ACCOUNT
   ========================================================= */

$stmt = $mysqli->prepare("
    INSERT INTO accounts
    (
        last_name,
        first_name,
        middle_initial,
        extension_name,
        department,
        year_section,
        student_id,
        email,
        username,
        password,
        access
    )
    VALUES
    (
        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
    )
");


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" =>
        "Unable to prepare account registration."
    ]);

    exit;
}


$stmt->bind_param(
    "sssssssssss",
    $last_name,
    $first_name,
    $middle_initial,
    $extension_name,
    $department,
    $year_section,
    $student_id,
    $email,
    $username,
    $password_hash,
    $access
);


/* =========================================================
   EXECUTE INSERT
   ========================================================= */

if ($stmt->execute()) {

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" =>
        "Student account created successfully.",
        "data" => [
            "username" => $username,
            "access" => $access
        ]
    ]);
}


/* =========================================================
   INSERT ERROR
   ========================================================= */ else {

    if ($stmt->errno === 1062) {

        http_response_code(409);

        echo json_encode([
            "success" => false,
            "message" =>
            "Username, email, or student ID is already registered."
        ]);
    } else {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" =>
            "Unable to create the student account."
        ]);
    }
}


/* =========================================================
   CLOSE CONNECTION
   ========================================================= */

$stmt->close();

$mysqli->close();
