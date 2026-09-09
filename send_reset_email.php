
<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "src/connection.php";

echo "<h2>ETS-Async Password Reset Debug</h2>";

/*
|--------------------------------------------------------------------------
| Check request
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo "<p style='color:red;'>ERROR: Request is not POST.</p>";
    exit;
}

echo "<p style='color:green;'>✓ POST request received.</p>";


/*
|--------------------------------------------------------------------------
| Get email
|--------------------------------------------------------------------------
*/

$email = trim($_POST["email"] ?? "");

echo "<p>Email submitted: <strong>"
    . htmlspecialchars($email)
    . "</strong></p>";


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo "<p style='color:red;'>ERROR: Invalid email address.</p>";
    exit;
}

echo "<p style='color:green;'>✓ Email is valid.</p>";


/*
|--------------------------------------------------------------------------
| Check database
|--------------------------------------------------------------------------
*/

if (!isset($mysqli)) {

    echo "<p style='color:red;'>ERROR: \$mysqli is not defined.</p>";
    exit;
}


if ($mysqli->connect_error) {

    echo "<p style='color:red;'>DATABASE ERROR:</p>";

    echo "<pre>";
    echo htmlspecialchars($mysqli->connect_error);
    echo "</pre>";

    exit;
}

echo "<p style='color:green;'>✓ Database connection successful.</p>";


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

    echo "<p style='color:red;'>SQL PREPARE ERROR:</p>";

    echo "<pre>";
    echo htmlspecialchars($mysqli->error);
    echo "</pre>";

    exit;
}


$stmt->bind_param("s", $email);


if (!$stmt->execute()) {

    echo "<p style='color:red;'>SQL EXECUTE ERROR:</p>";

    echo "<pre>";
    echo htmlspecialchars($stmt->error);
    echo "</pre>";

    exit;
}


$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


if (!$user) {

    echo "<p style='color:red;'>
        No account found with this email address.
    </p>";

    exit;
}


echo "<p style='color:green;'>✓ Account found.</p>";

echo "<pre>";
print_r($user);
echo "</pre>";


/*
|--------------------------------------------------------------------------
| Generate token
|--------------------------------------------------------------------------
*/

try {

    $token = bin2hex(random_bytes(32));
} catch (Exception $e) {

    echo "<p style='color:red;'>TOKEN ERROR:</p>";

    echo "<pre>";
    echo htmlspecialchars($e->getMessage());
    echo "</pre>";

    exit;
}


echo "<p style='color:green;'>✓ Secure token generated.</p>";


/*
|--------------------------------------------------------------------------
| Hash token
|--------------------------------------------------------------------------
*/

$tokenHash = hash(
    "sha256",
    $token
);


$expiresAt = date(
    "Y-m-d H:i:s",
    time() + (30 * 60)
);


echo "<p style='color:green;'>✓ Token hashed.</p>";

echo "<p>Token expires at: "
    . htmlspecialchars($expiresAt)
    . "</p>";


/*
|--------------------------------------------------------------------------
| Delete old tokens
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    DELETE FROM password_resets
    WHERE user_id = ?
");


if (!$stmt) {

    echo "<p style='color:red;'>
        ERROR: Could not access password_resets table.
    </p>";

    echo "<pre>";
    echo htmlspecialchars($mysqli->error);
    echo "</pre>";

    exit;
}


$stmt->bind_param(
    "i",
    $user["id"]
);


if (!$stmt->execute()) {

    echo "<p style='color:red;'>DELETE ERROR:</p>";

    echo "<pre>";
    echo htmlspecialchars($stmt->error);
    echo "</pre>";

    exit;
}


$stmt->close();


echo "<p style='color:green;'>✓ Previous tokens deleted.</p>";


/*
|--------------------------------------------------------------------------
| Insert token
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

    echo "<p style='color:red;'>INSERT PREPARE ERROR:</p>";

    echo "<pre>";
    echo htmlspecialchars($mysqli->error);
    echo "</pre>";

    exit;
}


$stmt->bind_param(
    "iss",
    $user["id"],
    $tokenHash,
    $expiresAt
);


if (!$stmt->execute()) {

    echo "<p style='color:red;'>INSERT ERROR:</p>";

    echo "<pre>";
    echo htmlspecialchars($stmt->error);
    echo "</pre>";

    exit;
}


$stmt->close();


echo "<p style='color:green;'>
    ✓ Reset token successfully stored.
</p>";


/*
|--------------------------------------------------------------------------
| Reset URL
|--------------------------------------------------------------------------
|
| IMPORTANT:
| Replace this with your actual domain/path.
|--------------------------------------------------------------------------
*/

$resetURL =
    "https://async.vertigation.com/reset_password.php?token="
    . urlencode($token);


echo "<hr>";

echo "<h3>Generated Reset Link</h3>";

echo "<p>";

echo "<a href='" .
    htmlspecialchars($resetURL) .
    "'>";

echo htmlspecialchars($resetURL);

echo "</a>";

echo "</p>";


/*
|--------------------------------------------------------------------------
| Email
|--------------------------------------------------------------------------
*/

$fromEmail =
    "no-reply@YOUR-DOMAIN.com";

$subject =
    "ETS-Async Password Reset";


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

echo "<p>Attempting to send email...</p>";


$mailSent = mail(
    $user["email"],
    $subject,
    $emailBody,
    $headers
);


if ($mailSent) {

    echo "<p style='color:green;font-weight:bold;'>
        ✓ PHP mail() reported SUCCESS.
    </p>";

    echo "<p>
        Check the recipient email inbox and spam folder.
    </p>";
} else {

    echo "<p style='color:red;font-weight:bold;'>
        ✗ PHP mail() FAILED.
    </p>";

    echo "<p>
        The database portion is working, but the server did not
        accept the email for sending.
    </p>";
}


echo "<hr>";

echo "<p>
    Debugging finished. This page intentionally does not redirect.
</p>";

?>
