
<?php

session_start();

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

$email = trim(
    $_POST["email"] ?? ""
);


/*
|--------------------------------------------------------------------------
| Generic message
|--------------------------------------------------------------------------
|
| We use the same message whether or not the account exists.
| This prevents account enumeration.
|
*/

$genericMessage =
    "If an account is associated with that email address, "
    . "a password reset link has been sent.";


/*
|--------------------------------------------------------------------------
| Validate email
|--------------------------------------------------------------------------
*/

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION["reset_message"] =
        "Please enter a valid email address.";

    $_SESSION["reset_message_type"] =
        "error";

    header("Location: forgot_password.php");

    exit;
}


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

    $_SESSION["reset_message"] =
        "Unable to process the password reset request.";

    $_SESSION["reset_message_type"] =
        "error";

    header("Location: forgot_password.php");

    exit;
}


$stmt->bind_param(
    "s",
    $email
);


if (!$stmt->execute()) {

    $stmt->close();

    $_SESSION["reset_message"] =
        "Unable to process the password reset request.";

    $_SESSION["reset_message_type"] =
        "error";

    header("Location: forgot_password.php");

    exit;
}


$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Account not found
|--------------------------------------------------------------------------
*/

if (!$user) {

    $_SESSION["reset_message"] =
        $genericMessage;

    $_SESSION["reset_message_type"] =
        "success";

    header("Location: forgot_password.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Generate secure reset token
|--------------------------------------------------------------------------
*/

try {

    $token = bin2hex(
        random_bytes(32)
    );
} catch (Exception $e) {

    $_SESSION["reset_message"] =
        "Unable to process the password reset request.";

    $_SESSION["reset_message_type"] =
        "error";

    header("Location: forgot_password.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Hash token
|--------------------------------------------------------------------------
*/

$tokenHash = hash(
    "sha256",
    $token
);


/*
|--------------------------------------------------------------------------
| Token expiration
|--------------------------------------------------------------------------
|
| 30 minutes from now.
|
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

    $_SESSION["reset_message"] =
        "Unable to process the password reset request.";

    $_SESSION["reset_message_type"] =
        "error";

    header("Location: forgot_password.php");

    exit;
}


$stmt->bind_param(
    "i",
    $user["id"]
);


if (!$stmt->execute()) {

    $stmt->close();

    $_SESSION["reset_message"] =
        "Unable to process the password reset request.";

    $_SESSION["reset_message_type"] =
        "error";

    header("Location: forgot_password.php");

    exit;
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| Insert new reset token
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

    $_SESSION["reset_message"] =
        "Unable to process the password reset request.";

    $_SESSION["reset_message_type"] =
        "error";

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

    $stmt->close();

    $_SESSION["reset_message"] =
        "Unable to process the password reset request.";

    $_SESSION["reset_message_type"] =
        "error";

    header("Location: forgot_password.php");

    exit;
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| Generate reset URL
|--------------------------------------------------------------------------
*/

$resetURL =
    "https://async.vertigation.com/reset_password.php?token="
    . urlencode($token);


/*
|--------------------------------------------------------------------------
| Email configuration
|--------------------------------------------------------------------------
*/

$fromEmail =
    "service-async@vertigation.com";


$subject =
    "ETS-Async Password Reset";


/*
|--------------------------------------------------------------------------
| Email body
|--------------------------------------------------------------------------
*/

$emailBody = <<<EMAIL
Hello,

We received a request to reset your ETS-Async account password.

Click the link below to create a new password:

$resetURL

This link will expire in 30 minutes.

If you did not request this password reset, you may safely ignore this email.

Regards,
ETS-Async
EMAIL;


/*
|--------------------------------------------------------------------------
| Email headers
|--------------------------------------------------------------------------
*/

$headers =
    "From: ETS-Async <$fromEmail>\r\n";


$headers .=
    "Reply-To: $fromEmail\r\n";


$headers .=
    "MIME-Version: 1.0\r\n";


$headers .=
    "Content-Type: text/plain; charset=UTF-8\r\n";


/*
|--------------------------------------------------------------------------
| Send email
|--------------------------------------------------------------------------
*/

$mailSent = mail(
    $user["email"],
    $subject,
    $emailBody,
    $headers
);


/*
|--------------------------------------------------------------------------
| Email failed
|--------------------------------------------------------------------------
*/

if (!$mailSent) {

    /*
    |----------------------------------------------------------------------
    | Remove token because the email was not accepted.
    |----------------------------------------------------------------------
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

    $_SESSION["reset_message_type"] =
        "error";


    header("Location: forgot_password.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| Email successfully sent
|--------------------------------------------------------------------------
*/

$_SESSION["reset_message"] =
    "Password reset link has been sent to your email address.";


$_SESSION["reset_message_type"] =
    "success";


/*
|--------------------------------------------------------------------------
| Return to forgot password page
|--------------------------------------------------------------------------
*/

header("Location: forgot_password.php");

exit;

?>
