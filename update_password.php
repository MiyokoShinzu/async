<?php

require_once "src/connection.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: forgot_password.php");
    exit;
}

$token = $_POST["token"] ?? "";

$password = $_POST["password"] ?? "";

$confirmPassword = $_POST["confirm_password"] ?? "";


/*
|--------------------------------------------------------------------------
| Basic validation
|--------------------------------------------------------------------------
*/

if (!$token) {
    die("Invalid reset request.");
}

if ($password !== $confirmPassword) {
    die("Passwords do not match.");
}

if (strlen($password) < 8) {
    die("Password must contain at least 8 characters.");
}


/*
|--------------------------------------------------------------------------
| Hash token
|--------------------------------------------------------------------------
*/

$tokenHash = hash("sha256", $token);


/*
|--------------------------------------------------------------------------
| Find valid reset request
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    SELECT
        id,
        user_id
    FROM password_resets
    WHERE token_hash = ?
      AND used_at IS NULL
      AND expires_at > NOW()
    LIMIT 1
");

$stmt->bind_param("s", $tokenHash);

$stmt->execute();

$result = $stmt->get_result();

$reset = $result->fetch_assoc();

$stmt->close();


if (!$reset) {
    die("This password reset link is invalid or has expired.");
}


/*
|--------------------------------------------------------------------------
| Hash new password
|--------------------------------------------------------------------------
*/

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);


/*
|--------------------------------------------------------------------------
| Update password
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    UPDATE accounts
    SET password = ?
    WHERE id = ?
");

$stmt->bind_param(
    "si",
    $passwordHash,
    $reset["user_id"]
);

$stmt->execute();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Invalidate token
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    UPDATE password_resets
    SET used_at = NOW()
    WHERE id = ?
");

$stmt->bind_param(
    "i",
    $reset["id"]
);

$stmt->execute();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Success
|--------------------------------------------------------------------------
*/

echo "
<!DOCTYPE html>

<html>

<head>

    <title>Password Reset</title>

</head>

<body>

    <h2>Password Successfully Changed</h2>

    <p>
        Your password has been updated successfully.
    </p>

    <p>
        You may now log in using your new password.
    </p>

    <a href='login.php'>
        Go to Login
    </a>

</body>

</html>
";
