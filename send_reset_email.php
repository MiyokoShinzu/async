
<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Forgot Password Debug</h2>";

/*
|--------------------------------------------------------------------------
| Check request
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo "<p style='color:red;'>ERROR: Request is not POST.</p>";
    exit;
}

echo "<p>✓ POST request received.</p>";


/*
|--------------------------------------------------------------------------
| Check email
|--------------------------------------------------------------------------
*/

$email = trim($_POST["email"] ?? "");

echo "<p>Email received: <strong>"
    . htmlspecialchars($email)
    . "</strong></p>";

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    echo "<p style='color:red;'>ERROR: Invalid email address.</p>";
    exit;
}

echo "<p>✓ Email format is valid.</p>";


/*
|--------------------------------------------------------------------------
| Database connection
|--------------------------------------------------------------------------
*/

require_once "src/connection.php";

echo "<p>✓ connection.php loaded.</p>";


/*
|--------------------------------------------------------------------------
| Check mysqli connection
|--------------------------------------------------------------------------
*/

if (!isset($mysqli)) {

    echo "<p style='color:red;'>
        ERROR: \$mysqli does not exist.
    </p>";

    exit;
}

if ($mysqli->connect_error) {

    echo "<p style='color:red;'>
        DATABASE ERROR:
        " . htmlspecialchars($mysqli->connect_error) . "
    </p>";

    exit;
}

echo "<p>✓ Database connection successful.</p>";


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

    echo "<p style='color:red;'>
        SQL PREPARE ERROR:
        " . htmlspecialchars($mysqli->error) . "
    </p>";

    exit;
}

echo "<p>✓ SQL statement prepared.</p>";


$stmt->bind_param("s", $email);

if (!$stmt->execute()) {

    echo "<p style='color:red;'>
        SQL EXECUTE ERROR:
        " . htmlspecialchars($stmt->error) . "
    </p>";

    exit;
}

echo "<p>✓ SQL query executed.</p>";


$result = $stmt->get_result();

if (!$result) {

    echo "<p style='color:red;'>
        ERROR: Could not retrieve query result.
    </p>";

    exit;
}


$user = $result->fetch_assoc();

$stmt->close();


if (!$user) {

    echo "<p style='color:red;'>
        NO ACCOUNT FOUND for this email address.
    </p>";

    exit;
}


echo "<p>✓ Account found.</p>";

echo "<pre>";
print_r($user);
echo "</pre>";


/*
|--------------------------------------------------------------------------
| Generate token
|--------------------------------------------------------------------------
*/

$token = bin2hex(random_bytes(32));

echo "<p>✓ Secure token generated.</p>";


$tokenHash = hash("sha256", $token);

$expiresAt = date(
    "Y-m-d H:i:s",
    time() + (30 * 60)
);

echo "<p>✓ Token hash generated.</p>";
echo "<p>Expires at: <strong>"
    . htmlspecialchars($expiresAt)
    . "</strong></p>";


/*
|--------------------------------------------------------------------------
| Check password_resets table
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    DELETE FROM password_resets
    WHERE user_id = ?
");

if (!$stmt) {

    echo "<p style='color:red;'>
        ERROR: password_resets table may not exist.
    </p>";

    echo "<p>MySQL says:</p>";
    echo "<pre>" . htmlspecialchars($mysqli->error) . "</pre>";

    exit;
}

$stmt->bind_param("i", $user["id"]);

if (!$stmt->execute()) {

    echo "<p style='color:red;'>
        ERROR deleting old reset token:
    </p>";

    echo "<pre>" . htmlspecialchars($stmt->error) . "</pre>";

    exit;
}

$stmt->close();

echo "<p>✓ Old reset tokens removed.</p>";


/*
|--------------------------------------------------------------------------
| Insert new token
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

    echo "<p style='color:red;'>
        ERROR preparing token INSERT:
    </p>";

    echo "<pre>" . htmlspecialchars($mysqli->error) . "</pre>";

    exit;
}

$stmt->bind_param(
    "iss",
    $user["id"],
    $tokenHash,
    $expiresAt
);


if (!$stmt->execute()) {

    echo "<p style='color:red;'>
        ERROR inserting reset token:
    </p>";

    echo "<pre>" . htmlspecialchars($stmt->error) . "</pre>";

    exit;
}

$stmt->close();

echo "<p>✓ Reset token stored in database.</p>";


/*
|--------------------------------------------------------------------------
| Generate reset URL
|--------------------------------------------------------------------------
*/

$resetURL =
    "https://YOUR-DOMAIN.com/reset_password.php?token="
    . urlencode($token);


echo "<hr>";

echo "<h3>Reset Link</h3>";

echo "<p>";
echo "<a href='" . htmlspecialchars($resetURL) . "'>";
echo htmlspecialchars($resetURL);
echo "</a>";
echo "</p>";

echo "<p style='color:green;'>
    ✓ Everything up to token generation is working.
</p>";

echo "<p>
    We have intentionally stopped before sending email.
</p>";

