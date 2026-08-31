
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
   ONLY ALLOW POST REQUEST
   ============================================ */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
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
   GET FORM DATA
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

$student_id =
    trim($input["student_id"] ?? "");

$email =
    trim($input["email"] ?? "");

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



/* ============================================
   EMAIL VALIDATION
   ============================================ */

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
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
   CHECK EXISTING ACCOUNT
   ============================================ */

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
        "message" => "Database error."
    ]);

    exit;
}


$stmt->bind_param(
    "sss",
    $username,
    $email,
    $student_id
);


$stmt->execute();


$result = $stmt->get_result();


if ($result->num_rows > 0) {

    $existing =
        $result->fetch_assoc();


    if (
        $existing["username"] ===
        $username
    ) {

        $message =
            "Username is already registered.";

    }

    elseif (
        $existing["email"] ===
        $email
    ) {

        $message =
            "Email address is already registered.";

    }

    else {

        $message =
            "Student ID is already registered.";

    }


    http_response_code(409);

    echo json_encode([
        "success" => false,
        "message" => $message
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
   INSERT ACCOUNT
   ============================================ */

$stmt = $mysqli->prepare("
    INSERT INTO accounts
    (
        last_name,
        first_name,
        middle_initial,
        department,
        year_section,
        student_id,
        email,
        username,
        password,
        access
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");


if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);

    exit;
}



/*
 * New registrations are automatically
 * assigned student access.
 */

$access = "student";


$stmt->bind_param(
    "ssssssssss",
    $last_name,
    $first_name,
    $middle_initial,
    $department,
    $year_section,
    $student_id,
    $email,
    $username,
    $password_hash,
    $access
);



/* ============================================
   EXECUTE INSERT
   ============================================ */

if ($stmt->execute()) {

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Student account created successfully."
    ]);

}

else {

    /*
     * Handle duplicate entries that may
     * occur because of database UNIQUE keys.
     */

    if ($stmt->errno === 1062) {

        http_response_code(409);

        echo json_encode([
            "success" => false,
            "message" => "Username, email, or student ID is already registered."
        ]);

    }

    else {

        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Unable to create the student account."
        ]);

    }

}


$stmt->close();

$mysqli->close();

?>

