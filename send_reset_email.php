
<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "src/connection.php";


/*
|--------------------------------------------------------------------------
| Only allow POST requests
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: forgot_password.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get email
|--------------------------------------------------------------------------
*/

$email = trim($_POST["email"] ?? "");


/*
|--------------------------------------------------------------------------
| Validate email
|--------------------------------------------------------------------------
*/

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION["reset_message"] =
        "Please enter a valid email address.";

    header("Location: forgot_password.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Generic response
|
| We use the same message whether the account exists or not.
| This prevents account enumeration.
|--------------------------------------------------------------------------
*/

$genericMessage =
    "If an account is associated with that email address, "
    . "a password reset link has been sent.";


/*
|--------------------------------------------------------------------------
| Find account
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    SELECT id, email
    FROM accounts
    WHERE email = ?
    LIMIT 1
");


if (!$stmt) {

    error_log(
        "Forgot Password SQL Error: "
            . $mysqli->error
    );

    $_SESSION["reset_message"] =
        "Something went wrong. Please try again later.";

    header("Location: forgot_password.php");
    exit;
}


$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Account does not exist
|--------------------------------------------------------------------------
*/

if (!$user) {

    $_SESSION["reset_message"] =
        $genericMessage;

    header("Location: forgot_password.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Generate secure random token
|--------------------------------------------------------------------------
*/

try {

    $token = bin2hex(
        random_bytes(32)
    );
} catch (Exception $e) {

    error_log(
        "Password Reset Token Error: "
            . $e->getMessage()
    );

    $_SESSION["reset_message"] =
        "Unable to process your request. Please try again later.";

    header("Location: forgot_password.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Hash token before storing it
|--------------------------------------------------------------------------
*/

$tokenHash = hash(
    "sha256",
    $token
);


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
| Delete previous reset tokens
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    DELETE FROM password_resets
    WHERE user_id = ?
");


if (!$stmt) {

    error_log(
        "Password Reset Delete Error: "
            . $mysqli->error
    );

    $_SESSION["reset_message"] =
        "Something went wrong. Please try again later.";

    header("Location: forgot_password.php");
    exit;
}


$stmt->bind_param(
    "i",
    $user["id"]
);


if (!$stmt->execute()) {

    error_log(
        "Password Reset Delete Execute Error: "
            . $stmt->error
    );

    $stmt->close();

    $_SESSION["reset_message"] =
        "Something went wrong. Please try again later.";

    header("Location: forgot_password.php");
    exit;
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| Store new reset token
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


if (!$stmt) {

    error_log(
        "Password Reset Insert Error: "
            . $mysqli->error
    );

    $_SESSION["reset_message"] =
        "Something went wrong. Please try again later.";

    header("Location: forgot_password.php");
    exit;
}


$stmt->bind_param(
    "iss",
    $user["id"],
    $tokenHash,
    $expiresAt
);


if (!$stmt->execute()) {

    error_log(
        "Password Reset Insert Execute Error: "
            . $stmt->error
    );

    $stmt->close();

    $_SESSION["reset_message"] =
        "Something went wrong. Please try again later.";

    header("Location: forgot_password.php");
    exit;
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| CREATE RESET URL
|--------------------------------------------------------------------------
|
| IMPORTANT:
| Replace YOUR-DOMAIN.com with your actual domain.
|
|--------------------------------------------------------------------------
*/

$resetURL =
    "https://async.vertigation.com/reset_password.php?token="
    . urlencode($token);


/*
|--------------------------------------------------------------------------
| Email configuration
|--------------------------------------------------------------------------
|
| IMPORTANT:
| Use an actual email address created in Hostinger.
|
|--------------------------------------------------------------------------
*/

$fromEmail = "no-reply@YOUR-DOMAIN.com";

$fromName = "ETS-Async";


/*
|--------------------------------------------------------------------------
| Email subject
|--------------------------------------------------------------------------
*/

$subject =
    "ETS-Async Password Reset";


/*
|--------------------------------------------------------------------------
| Email body
|--------------------------------------------------------------------------
*/

$emailBody = <<<EMAIL
Hello,

We received a request to reset the password for your ETS-Async account.

To create a new password, click the link below:

$resetURL

This password reset link will expire in 30 minutes.

If you did not request a password reset, you can safely ignore this email.

Regards,

ETS-Async
EMAIL;


/*
|--------------------------------------------------------------------------
| Email headers
|--------------------------------------------------------------------------
*/

$headers = [];

$headers[] =
    "From: " . $fromName . " <" . $fromEmail . ">";

$headers[] =
    "Reply-To: " . $fromEmail;

$headers[] =
    "MIME-Version: 1.0";

$headers[] =
    "Content-Type: text/plain; charset=UTF-8";


$headersString =
    implode("\r\n", $headers);


/*
|--------------------------------------------------------------------------
| Send email
|--------------------------------------------------------------------------
*/

$mailSent = mail(
    $user["email"],
    $subject,
    $emailBody,
    $headersString
);


/*
|--------------------------------------------------------------------------
| Log mail failure
|--------------------------------------------------------------------------
*/

if (!$mailSent) {

    error_log(
        "Password reset email failed for: "
            . $user["email"]
    );

    /*
    |--------------------------------------------------------------------------
    | Remove the token because the user didn't receive the email.
    |--------------------------------------------------------------------------
    */

    $stmt = $mysqli->prepare("
        DELETE FROM password_resets
        WHERE user_id = ?
    ");

    if ($stmt) {

        $stmt->bind_param(
            "i",
            $user["id"]
        );

        $stmt->execute();

        $stmt->close();
    }

    $_SESSION["reset_message"] =
        "We could not send the password reset email. "
        . "Please try again later.";

    header("Location: forgot_password.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Success
|--------------------------------------------------------------------------
*/

$_SESSION["reset_message"] =
    $genericMessage;


/*
|--------------------------------------------------------------------------
| Return to forgot password page
|--------------------------------------------------------------------------
*/

header("Location: forgot_password.php");

exit;
?>
