
<?php

/* ============================================
   STUDENT REGISTRATION API
   ============================================ */

header("Content-Type: application/json; charset=UTF-8");


/* ============================================
   DATABASE CONNECTION
   ============================================ */

require_once "../src/connection.php";


/* ============================================
   ONLY ALLOW POST
   ============================================ */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only POST requests are allowed."
    ]);

    exit;
}


/* ============================================
   GET JSON DATA
   ============================================ */

$input = json_decode(
    file_get_contents("php://input"),
    true
);


if (!is_array($input)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request data."
    ]);

    exit;
}


/* ============================================
   GET DATA
   ============================================ */

$last_name =
    trim($input["last_name"] ?? "");

$first_name =
    trim($input["first_name"] ?? "");

$middle_initial =
    strtoupper(
        trim($input["middle_initial"] ?? "")
    );

$department =
    trim($input["department"] ?? "");

$year_section =
    trim($input["year_section"] ?? "");

$username =
    trim($input["username"] ?? "");

$password =
    $input["password"] ?? "";


/* ============================================
   REQUIRED FIELDS
   ============================================ */

if (
    $last_name === "" ||
    $first_name === "" ||
    $department === "" ||
    $year_section === "" ||
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


/* ============================================
   SPLIT YEAR AND SECTION
   ============================================ */

$parts = explode("-", $year_section);


if (count($parts) !== 2) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid year and section."
    ]);

    exit;
}


$year_level =
    trim($parts[0]);

$section =
    strtoupper(
        trim($parts[1])
    );


/* ============================================
   VALIDATE YEAR LEVEL
   ============================================ */

if (!in_array($year_level, ["1", "2", "3", "4"])) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid year level."
    ]);

    exit;
}


/* ============================================
   VALIDATE SECTION
   ============================================ */

if ($section === "") {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid section."
    ]);

    exit;
}


/* ============================================
   USERNAME VALIDATION
   ============================================ */

if (strlen($username) < 4) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Username must contain at least 4 characters."
    ]);

    exit;
}


/* ============================================
   PASSWORD VALIDATION
   ============================================ */

if (strlen($password) < 8) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Password must contain at least 8 characters."
    ]);

    exit;
}


/* ============================================
   CHECK USERNAME
   ============================================ */

$stmt = $mysqli->prepare("
    SELECT id
    FROM accounts
    WHERE username = ?
    LIMIT 1
");


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);

    exit;
}


$stmt->bind_param(
    "s",
    $username
);


$stmt->execute();


$result =
    $stmt->get_result();


if ($result->num_rows > 0) {

    http_response_code(409);

    echo json_encode([
        "success" => false,
        "message" => "Username is already registered."
    ]);

    $stmt->close();

    exit;
}


$stmt->close();


/* ============================================
   HASH PASSWORD
   ============================================ */

$password_hash =
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );


if ($password_hash === false) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to secure password."
    ]);

    exit;
}


/* ============================================
   DEFAULT ACCESS
   ============================================ */

$access = "student";


/* ============================================
   INSERT ACCOUNT
   ============================================ */

$stmt = $mysqli->prepare("
    INSERT INTO accounts
    (
        last_name,
        first_name,
        middle_initial,
        department,
        year_level,
        section,
        username,
        password,
        access
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?)
");


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);

    exit;
}


/* ============================================
   BIND PARAMETERS
   ============================================ */

$stmt->bind_param(
    "sssssssss",
    $last_name,
    $first_name,
    $middle_initial,
    $department,
    $year_level,
    $section,
    $username,
    $password_hash,
    $access
);


/* ============================================
   INSERT
   ============================================ */

if ($stmt->execute()) {

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Student account created successfully."
    ]);

}

else {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to create the student account."
    ]);

}


$stmt->close();

$mysqli->close();

?>

