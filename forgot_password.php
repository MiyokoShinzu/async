<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password | ETS-Async</title>

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
            box-shadow: 0 5px 25px rgba(0, 0, 0, .08);
        }

        h2 {
            margin-top: 0;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin: 10px 0 15px;
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

        button:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>

    <div class="card">

        <h2>Forgot Password?</h2>

        <p>
            Enter your registered email address and we will send you
            a password reset link.
        </p>

        <form action="send_reset_email.php" method="POST">

            <label for="email">Email Address</label>

            <input
                type="email"
                id="email"
                name="email"
                required
                autocomplete="email">

            <button type="submit">
                Send Reset Link
            </button>

        </form>

    </div>

</body>

</html>