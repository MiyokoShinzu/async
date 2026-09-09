<?php

require_once "src/connection.php";

$token = $_GET["token"] ?? "";

if (!$token) {
    die("Invalid password reset link.");
}

$tokenHash = hash("sha256", $token);


/*
|--------------------------------------------------------------------------
| Find valid token
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    SELECT
        password_resets.id AS reset_id,
        password_resets.user_id,
        accounts.email
    FROM password_resets
    INNER JOIN accounts
        ON accounts.id = password_resets.user_id
    WHERE password_resets.token_hash = ?
      AND password_resets.used_at IS NULL
      AND password_resets.expires_at > NOW()
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

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Reset Password | ETS-Async</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;

            display: flex;
            justify-content: center;
            align-items: center;

            min-height: 100vh;

            margin: 0;
        }

        .card {
            width: 100%;
            max-width: 420px;

            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 5px 25px rgba(0, 0, 0, .08);
        }

        input {
            width: 100%;
            box-sizing: border-box;

            padding: 12px;

            margin: 8px 0 15px;

            border: 1px solid #ddd;

            border-radius: 7px;
        }

        button {
            width: 100%;

            padding: 12px;

            border: 0;

            border-radius: 7px;

            background: #2563eb;

            color: white;

            cursor: pointer;
        }
    </style>

</head>

<body>

    <div class="card">

        <h2>Create New Password</h2>

        <form action="update_password.php" method="POST">

            <input
                type="hidden"
                name="token"
                value="<?= htmlspecialchars($token) ?>">

            <label>New Password</label>

            <input
                type="password"
                name="password"
                required
                minlength="8"
                autocomplete="new-password">

            <label>Confirm Password</label>

            <input
                type="password"
                name="confirm_password"
                required
                minlength="8"
                autocomplete="new-password">

            <button type="submit">
                Reset Password
            </button>

        </form>

    </div>

</body>

</html>