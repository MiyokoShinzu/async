
<?php

/* =========================================================
   LOGIN API
   ========================================================= */

header("Content-Type: application/json; charset=UTF-8");


/* =========================================================
   START SESSION
   ========================================================= */

session_start();


/* =========================================================
   DATABASE CONNECTION
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   ONLY ALLOW POST REQUEST
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only POST requests are allowed."
    ]);

    exit;
}


/* =========================================================
   GET JSON REQUEST
   ========================================================= */

$rawInput = file_get_contents("php://input");

$input = json_decode($rawInput, true);


if (!is_array($input)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON request."
    ]);

    exit;
}


/* =========================================================
   GET LOGIN DATA
   ========================================================= */

$username =
    trim($input["username"] ?? "");

$password =
    $input["password"] ?? "";


/* =========================================================
   REQUIRED FIELD CHECK
   ========================================================= */

if (
    $username === "" ||
    $password === ""
) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter your username and password."
    ]);

    exit;
}


/* =========================================================
   USERNAME VALIDATION
   ========================================================= */

if (strlen($username) < 4) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password."
    ]);

    exit;
}


if (strlen($username) > 50) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password."
    ]);

    exit;
}


/* =========================================================
   PASSWORD VALIDATION
   ========================================================= */

if (strlen($password) < 8) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password."
    ]);

    exit;
}


/* =========================================================
   FIND ACCOUNT
   ========================================================= */

$stmt = $mysqli->prepare("
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
        password,
        access,
        created_at
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


/* =========================================================
   BIND USERNAME
   ========================================================= */

$stmt->bind_param(
    "s",
    $username
);


/* =========================================================
   EXECUTE QUERY
   ========================================================= */

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to process login."
    ]);

    exit;
}


/* =========================================================
   GET RESULT
   ========================================================= */

$result =
    $stmt->get_result();


/* =========================================================
   ACCOUNT NOT FOUND
   ========================================================= */

if ($result->num_rows !== 1) {

    $stmt->close();

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password."
    ]);

    exit;
}


/* =========================================================
   GET ACCOUNT
   ========================================================= */

$account =
    $result->fetch_assoc();


$stmt->close();


/* =========================================================
   VERIFY PASSWORD
   ========================================================= */

if (
    !password_verify(
        $password,
        $account["password"]
    )
) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password."
    ]);

    $mysqli->close();

    exit;
}


/* =========================================================
   VERIFY ACCESS
   ========================================================= */

$allowed_access = [
    "student",
    "admin"
];


if (
    !in_array(
        $account["access"],
        $allowed_access,
        true
    )
) {

    http_response_code(403);

    echo json_encode([
        "success" => false,
        "message" => "Your account does not have valid access."
    ]);

    $mysqli->close();

    exit;
}



/* =========================================================
   REGENERATE SESSION ID
   ========================================================= */

session_regenerate_id(true);


/* =========================================================
   STORE LOGIN SESSION
   ========================================================= */

$_SESSION["logged_in"] = true;


/* =========================================================
   STORE USER DATA
   PASSWORD IS NOT STORED
   ========================================================= */

$_SESSION["user"] = [

    "id" =>
    (int) $account["id"],

    "username" =>
    $account["username"],

    "first_name" =>
    $account["first_name"],

    "last_name" =>
    $account["last_name"],

    "middle_initial" =>
    $account["middle_initial"],

    "extension_name" =>
    $account["extension_name"],

    "department" =>
    $account["department"],

    "year_section" =>
    $account["year_section"],

    "student_id" =>
    $account["student_id"],

    "email" =>
    $account["email"],

    "access" =>
    $account["access"]

];


/* =========================================================
   OPTIONAL QUICK-ACCESS SESSION VALUES
   ========================================================= */

$_SESSION["user_id"] =
    (int) $account["id"];

$_SESSION["username"] =
    $account["username"];

$_SESSION["access"] =
    $account["access"];

$_SESSION["student_id"] =
    $account["student_id"];


/* =========================================================
   DETERMINE DASHBOARD
   ========================================================= */

if ($account["access"] === "admin") {

    $redirect =
        "./admin/index.php";

} else {

    $redirect =
        "./student/index.php";

}


/* =========================================================
   SUCCESS RESPONSE
   ========================================================= */

http_response_code(200);

echo json_encode([

    "success" => true,

    "message" =>
    "Login successful.",

    "access" =>
    $account["access"],

    "user" => [

        "id" =>
        (int) $account["id"],

        "username" =>
        $account["username"],

        "first_name" =>
        $account["first_name"],

        "last_name" =>
        $account["last_name"],

        "middle_initial" =>
        $account["middle_initial"],

        "extension_name" =>
        $account["extension_name"],

        "department" =>
        $account["department"],

        "year_section" =>
        $account["year_section"],

        "student_id" =>
        $account["student_id"],

        "email" =>
        $account["email"],

        "access" =>
        $account["access"]

    ],

    "redirect" =>
    $redirect

]);



/* =========================================================
   CLOSE DATABASE
   ========================================================= */

$mysqli->close();

?>
