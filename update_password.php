
<?php

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
| Get submitted values
|--------------------------------------------------------------------------
*/

$token =
    $_POST["token"] ?? "";

$password =
    $_POST["password"] ?? "";

$confirmPassword =
    $_POST["confirm_password"] ?? "";


/*
|--------------------------------------------------------------------------
| Basic validation
|--------------------------------------------------------------------------
*/

if (!$token) {

    die("Invalid password reset request.");
}


if ($password !== $confirmPassword) {

    die("Passwords do not match.");
}


if (strlen($password) < 8) {

    die("Password must contain at least 8 characters.");
}


/*
|--------------------------------------------------------------------------
| Password complexity validation
|--------------------------------------------------------------------------
*/

if (!preg_match("/[A-Z]/", $password)) {

    die("Password must contain at least one uppercase letter.");
}


if (!preg_match("/[a-z]/", $password)) {

    die("Password must contain at least one lowercase letter.");
}


if (!preg_match("/[0-9]/", $password)) {

    die("Password must contain at least one number.");
}


/*
|--------------------------------------------------------------------------
| Hash reset token
|--------------------------------------------------------------------------
*/

$tokenHash =
    hash(
        "sha256",
        $token
    );


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


if (!$stmt) {

    die("Unable to process the password reset request.");
}


$stmt->bind_param(
    "s",
    $tokenHash
);


if (!$stmt->execute()) {

    $stmt->close();

    die("Unable to process the password reset request.");
}


$result =
    $stmt->get_result();


$reset =
    $result->fetch_assoc();


$stmt->close();


/*
|--------------------------------------------------------------------------
| Validate reset token
|--------------------------------------------------------------------------
*/

if (!$reset) {

    die("This password reset link is invalid or has expired.");
}


/*
|--------------------------------------------------------------------------
| Hash new password
|--------------------------------------------------------------------------
*/

$passwordHash =
    password_hash(
        $password,
        PASSWORD_DEFAULT
    );


if (!$passwordHash) {

    die("Unable to securely process the new password.");
}


/*
|--------------------------------------------------------------------------
| Update account password
|--------------------------------------------------------------------------
*/

$stmt = $mysqli->prepare("
    UPDATE accounts
    SET password = ?
    WHERE id = ?
");


if (!$stmt) {

    die("Unable to update the password.");
}


$stmt->bind_param(
    "si",
    $passwordHash,
    $reset["user_id"]
);


if (!$stmt->execute()) {

    $stmt->close();

    die("Unable to update the password.");
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| Invalidate reset token
|--------------------------------------------------------------------------
|
| This makes the password reset link one-time use.
|
*/

$stmt = $mysqli->prepare("
    UPDATE password_resets
    SET used_at = NOW()
    WHERE id = ?
");


if (!$stmt) {

    die("Password was changed, but the reset token could not be "
        . "invalidated. Please contact the administrator.");
}


$stmt->bind_param(
    "i",
    $reset["id"]
);


if (!$stmt->execute()) {

    $stmt->close();

    die("Password was changed, but the reset token could not be "
        . "invalidated. Please contact the administrator.");
}


$stmt->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="Your ETS-Async password has been successfully changed.">

    <title>
        Password Reset Successful | ETS-Async
    </title>


    <style>
        /*
        |--------------------------------------------------------------------------
        | Reset
        |--------------------------------------------------------------------------
        */

        * {
            box-sizing: border-box;
        }


        html,
        body {

            margin: 0;

            padding: 0;

        }


        /*
        |--------------------------------------------------------------------------
        | Body
        |--------------------------------------------------------------------------
        */

        body {

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 24px;

            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Roboto,
                Arial,
                sans-serif;

            background:
                radial-gradient(circle at top left,
                    rgba(37, 99, 235, 0.10),
                    transparent 35%),
                radial-gradient(circle at bottom right,
                    rgba(99, 102, 241, 0.10),
                    transparent 35%),
                #f5f7fb;

            color: #1e293b;

        }


        /*
        |--------------------------------------------------------------------------
        | Wrapper
        |--------------------------------------------------------------------------
        */

        .success-wrapper {

            width: 100%;

            max-width: 460px;

        }


        /*
        |--------------------------------------------------------------------------
        | Card
        |--------------------------------------------------------------------------
        */

        .success-card {

            background: rgba(255, 255, 255, 0.97);

            border: 1px solid rgba(226, 232, 240, 0.9);

            border-radius: 20px;

            padding: 36px 34px;

            text-align: center;

            box-shadow:
                0 20px 50px rgba(15, 23, 42, 0.10),
                0 4px 12px rgba(15, 23, 42, 0.04);

        }


        /*
        |--------------------------------------------------------------------------
        | Success Icon
        |--------------------------------------------------------------------------
        */

        .success-icon {

            width: 76px;

            height: 76px;

            margin: 0 auto 24px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background: #ecfdf5;

            border: 8px solid #f0fdf4;

            color: #16a34a;

        }


        .success-icon svg {

            width: 36px;

            height: 36px;

        }


        /*
        |--------------------------------------------------------------------------
        | Heading
        |--------------------------------------------------------------------------
        */

        h1 {

            margin: 0 0 10px;

            font-size: 25px;

            font-weight: 700;

            letter-spacing: -0.4px;

            color: #0f172a;

        }


        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        .description {

            margin: 0 auto;

            max-width: 350px;

            font-size: 14px;

            line-height: 1.65;

            color: #64748b;

        }


        /*
        |--------------------------------------------------------------------------
        | Status Box
        |--------------------------------------------------------------------------
        */

        .status-box {

            display: flex;

            align-items: flex-start;

            gap: 11px;

            margin-top: 24px;

            padding: 14px;

            text-align: left;

            border-radius: 10px;

            background: #f8fafc;

            border: 1px solid #e2e8f0;

        }


        .status-icon {

            flex-shrink: 0;

            width: 20px;

            height: 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #2563eb;

        }


        .status-icon svg {

            width: 18px;

            height: 18px;

        }


        .status-text {

            font-size: 13px;

            line-height: 1.55;

            color: #475569;

        }


        .status-text strong {

            display: block;

            margin-bottom: 2px;

            color: #334155;

        }


        /*
        |--------------------------------------------------------------------------
        | Login Button
        |--------------------------------------------------------------------------
        */

        .login-button {

            width: 100%;

            height: 50px;

            margin-top: 25px;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 9px;

            border: 0;

            border-radius: 10px;

            background:
                linear-gradient(135deg,
                    #2563eb,
                    #4f46e5);

            color: #ffffff;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;

            box-shadow:
                0 8px 18px rgba(37, 99, 235, 0.20);

            transition:
                transform 0.15s ease,
                box-shadow 0.2s ease;

        }


        .login-button:hover {

            transform: translateY(-1px);

            box-shadow:
                0 11px 22px rgba(37, 99, 235, 0.25);

        }


        .login-button:active {

            transform: translateY(0);

        }


        .login-button svg {

            width: 18px;

            height: 18px;

        }


        /*
        |--------------------------------------------------------------------------
        | Security Notice
        |--------------------------------------------------------------------------
        */

        .security-note {

            display: flex;

            align-items: flex-start;

            gap: 9px;

            margin-top: 20px;

            padding: 12px 13px;

            border-radius: 9px;

            background: #f8fafc;

            border: 1px solid #e2e8f0;

            text-align: left;

            font-size: 12px;

            line-height: 1.5;

            color: #64748b;

        }


        .security-note svg {

            width: 17px;

            height: 17px;

            flex-shrink: 0;

            color: #2563eb;

            margin-top: 1px;

        }


        /*
        |--------------------------------------------------------------------------
        | Footer
        |--------------------------------------------------------------------------
        */

        .footer {

            text-align: center;

            margin-top: 22px;

            font-size: 12px;

            color: #94a3b8;

        }


        /*
        |--------------------------------------------------------------------------
        | Mobile
        |--------------------------------------------------------------------------
        */

        @media (max-width: 480px) {

            body {

                padding: 15px;

            }


            .success-card {

                padding: 30px 20px;

                border-radius: 16px;

            }


            h1 {

                font-size: 23px;

            }

        }
    </style>

</head>


<body>


    <main class="success-wrapper">


        <div class="success-card">


            <!-- =========================================================
                 SUCCESS ICON
            ========================================================== -->

            <div class="success-icon">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path
                        d="M20 6L9 17l-5-5" />

                </svg>

            </div>


            <!-- =========================================================
                 HEADING
            ========================================================== -->

            <h1>

                Password Successfully Changed

            </h1>


            <!-- =========================================================
                 DESCRIPTION
            ========================================================== -->

            <p class="description">

                Your ETS-Async account password has been
                successfully updated.

                You can now sign in using your new password.

            </p>


            <!-- =========================================================
                 STATUS
            ========================================================== -->

            <div class="status-box">


                <div class="status-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <path
                            d="M12 22s8-4 8-10V5l-8-3-8 3v7
                               c0 6 8 10 8 10z" />

                        <path
                            d="m9 12 2 2 4-4" />

                    </svg>

                </div>


                <div class="status-text">

                    <strong>
                        Your account is secure
                    </strong>

                    Your new password has been securely
                    encrypted and stored.

                </div>


            </div>


            <!-- =========================================================
                 LOGIN BUTTON
            ========================================================== -->

            <a
                href="login.php"
                class="login-button">

                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <path
                        d="M15 3h4a2 2 0 0 1 2 2v14
                           a2 2 0 0 1-2 2h-4" />

                    <polyline
                        points="10 17 15 12 10 7" />

                    <line
                        x1="15"
                        y1="12"
                        x2="3"
                        y2="12" />

                </svg>

                Go to Login

            </a>


            <!-- =========================================================
                 SECURITY NOTICE
            ========================================================== -->

            <div class="security-note">


                <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round">

                    <rect
                        x="3"
                        y="11"
                        width="18"
                        height="10"
                        rx="2" />

                    <path
                        d="M7 11V7a5 5 0 0 1 10 0v4" />

                </svg>


                <span>

                    For your security, the password reset link
                    can no longer be used after this password
                    change.

                </span>


            </div>


        </div>


        <!-- =============================================================
             FOOTER
        ============================================================== -->

        <div class="footer">

            ETS-Async Learning Portal

        </div>


    </main>


</body>

</html>
