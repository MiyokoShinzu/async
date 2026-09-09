<?php

session_start();

require_once "src/connection.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: forgot_password.php");
    exit;
}

$email = trim($_POST["email"] ?? "");

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: forgot_password.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Find user
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    SELECT id, email
    FROM accounts
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Always show generic response
|--------------------------------------------------------------------------
*/

$message = "
    If an account is associated with that email address,
    a password reset link has been sent.
";


/*
|--------------------------------------------------------------------------
| Stop if account does not exist
|--------------------------------------------------------------------------
*/

if (!$user) {

    $_SESSION["reset_message"] = $message;

    header("Location: forgot_password.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Generate secure token
|--------------------------------------------------------------------------
*/

$token = bin2hex(random_bytes(32));

$tokenHash = hash("sha256", $token);


/*
|--------------------------------------------------------------------------
| Token expiration
| 30 minutes
|--------------------------------------------------------------------------
*/

$expiresAt = date(
    "Y-m-d H:i:s",
    time() + (30 * 60)
);


/*
|--------------------------------------------------------------------------
| Delete previous unused reset tokens
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    DELETE FROM password_resets
    WHERE user_id = ?
");

$stmt->bind_param("i", $user["id"]);

$stmt->execute();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Store token hash
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    INSERT INTO password_resets
    (
        user_id,
        token_hash,
        expires_at
    )
    VALUES (?, ?, ?)
");

$stmt->bind_param(
    "iss",
    $user["id"],
    $tokenHash,
    $expiresAt
);

$stmt->execute();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Create reset URL
|--------------------------------------------------------------------------
*/

$resetURL =
    "http://localhost/reset_password.php?token="
    . urlencode($token);


/*
|--------------------------------------------------------------------------
| Email
|--------------------------------------------------------------------------
*/

$subject = "ETS-Async Password Reset";

$message = "
Hello,

A password reset request was made for your ETS-Async account.

Click the link below to create a new password:

$resetURL

This link will expire in 30 minutes.

If you did not request a password reset, you can safely ignore this email.

Regards,
ETS-Async
";


$headers = "From: ETS-Async <no-reply@your-domain.com>\r\n";
$headers .= "Reply-To: no-reply@your-domain.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";


mail(
    $user["email"],
    $subject,
    $message,
    $headers
);


/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

$_SESSION["reset_message"] = "
    If an account is associated with that email address,
    a password reset link has been sent.
";

header("Location: forgot_password.php");

exit;
