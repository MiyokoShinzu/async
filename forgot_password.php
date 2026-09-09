
<?php

session_start();

/*
|--------------------------------------------------------------------------
| Get password reset message
|--------------------------------------------------------------------------
*/

$resetMessage = $_SESSION["reset_message"] ?? "";
$resetMessageType = $_SESSION["reset_message_type"] ?? "success";

/*
|--------------------------------------------------------------------------
| Clear message after reading
|--------------------------------------------------------------------------
*/

unset($_SESSION["reset_message"]);
unset($_SESSION["reset_message_type"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

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

            box-sizing: border-box;

        }


        h2 {

            margin-top: 0;

            margin-bottom: 10px;

        }


        p {

            color: #555;

            line-height: 1.5;

        }


        .message {

            padding: 13px 15px;

            margin: 20px 0;

            border-radius: 8px;

            font-size: 14px;

            line-height: 1.5;

        }


        .message.success {

            background: #ecfdf5;

            color: #065f46;

            border: 1px solid #a7f3d0;

        }


        .message.error {

            background: #fef2f2;

            color: #991b1b;

            border: 1px solid #fecaca;

        }


        label {

            display: block;

            margin-bottom: 5px;

            font-weight: 600;

            color: #333;

        }


        input {

            width: 100%;

            box-sizing: border-box;

            padding: 12px;

            margin: 10px 0 15px;

            border: 1px solid #ddd;

            border-radius: 7px;

            font-size: 15px;

        }


        input:focus {

            outline: none;

            border-color: #2563eb;

            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);

        }


        button {

            width: 100%;

            padding: 12px;

            border: 0;

            border-radius: 7px;

            background: #2563eb;

            color: white;

            cursor: pointer;

            font-size: 15px;

            font-weight: 600;

        }


        button:hover {

            background: #1d4ed8;

        }
    </style>

</head>


<body>


    <div class="card">


        <h2>
            Forgot Password?
        </h2>


        <p>

            Enter your registered email address and we will send you
            a password reset link.

        </p>


        <?php if ($resetMessage): ?>

            <div
                class="message <?= $resetMessageType === "error"
                                    ? "error"
                                    : "success" ?>">

                <?= htmlspecialchars($resetMessage) ?>

            </div>

        <?php endif; ?>


        <form
            action="send_reset_email.php"
            method="POST">


            <label for="email">

                Email Address

            </label>


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
