
<?php

require_once "src/connection.php";


/*
|--------------------------------------------------------------------------
| Get reset token
|--------------------------------------------------------------------------
*/

$token = $_GET["token"] ?? "";

if (!$token) {

    die("Invalid password reset link.");
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


$result = $stmt->get_result();

$reset = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Validate token
|--------------------------------------------------------------------------
*/

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

    <meta
        name="description"
        content="Reset your ETS-Async account password.">

    <title>
        Reset Password | ETS-Async
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
        | Main Container
        |--------------------------------------------------------------------------
        */

        .reset-wrapper {

            width: 100%;

            max-width: 460px;

        }


        /*
        |--------------------------------------------------------------------------
        | Card
        |--------------------------------------------------------------------------
        */

        .reset-card {

            background: rgba(255, 255, 255, 0.97);

            border: 1px solid rgba(226, 232, 240, 0.9);

            border-radius: 20px;

            padding: 34px;

            box-shadow:
                0 20px 50px rgba(15, 23, 42, 0.10),
                0 4px 12px rgba(15, 23, 42, 0.04);

        }


        /*
        |--------------------------------------------------------------------------
        | Logo / Icon
        |--------------------------------------------------------------------------
        */

        .brand {

            display: flex;

            justify-content: center;

            margin-bottom: 22px;

        }


        .brand-icon {

            width: 64px;

            height: 64px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 18px;

            background:
                linear-gradient(135deg,
                    #2563eb,
                    #4f46e5);

            color: white;

            box-shadow:
                0 10px 25px rgba(37, 99, 235, 0.25);

        }


        .brand-icon svg {

            width: 31px;

            height: 31px;

        }


        /*
        |--------------------------------------------------------------------------
        | Heading
        |--------------------------------------------------------------------------
        */

        .header {

            text-align: center;

            margin-bottom: 28px;

        }


        .header h1 {

            margin: 0 0 9px;

            font-size: 25px;

            font-weight: 700;

            letter-spacing: -0.4px;

            color: #0f172a;

        }


        .header p {

            margin: 0;

            font-size: 14px;

            line-height: 1.6;

            color: #64748b;

        }


        /*
        |--------------------------------------------------------------------------
        | Form Group
        |--------------------------------------------------------------------------
        */

        .form-group {

            margin-bottom: 20px;

        }


        .form-label {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: 600;

            color: #334155;

        }


        /*
        |--------------------------------------------------------------------------
        | Password Input
        |--------------------------------------------------------------------------
        */

        .password-wrapper {

            position: relative;

        }


        .password-input {

            width: 100%;

            height: 48px;

            padding:
                0 48px 0 15px;

            border: 1px solid #cbd5e1;

            border-radius: 10px;

            outline: none;

            background: #ffffff;

            color: #0f172a;

            font-size: 14px;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;

        }


        .password-input:hover {

            border-color: #94a3b8;

        }


        .password-input:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 4px rgba(37, 99, 235, 0.10);

        }


        /*
        |--------------------------------------------------------------------------
        | Show / Hide Button
        |--------------------------------------------------------------------------
        */

        .toggle-password {

            position: absolute;

            right: 6px;

            top: 50%;

            transform: translateY(-50%);

            width: 38px;

            height: 38px;

            display: flex;

            align-items: center;

            justify-content: center;

            border: 0;

            border-radius: 8px;

            background: transparent;

            color: #64748b;

            cursor: pointer;

            transition:
                background 0.2s ease,
                color 0.2s ease;

        }


        .toggle-password:hover {

            background: #f1f5f9;

            color: #2563eb;

        }


        .toggle-password:focus {

            outline: 2px solid rgba(37, 99, 235, 0.25);

            outline-offset: 1px;

        }


        .toggle-password svg {

            width: 19px;

            height: 19px;

        }


        /*
        |--------------------------------------------------------------------------
        | Password Requirements
        |--------------------------------------------------------------------------
        */

        .requirements {

            margin-top: 10px;

            padding: 12px 13px;

            border-radius: 9px;

            background: #f8fafc;

            border: 1px solid #e2e8f0;

        }


        .requirements-title {

            margin-bottom: 8px;

            font-size: 12px;

            font-weight: 700;

            color: #475569;

        }


        .requirement {

            display: flex;

            align-items: center;

            gap: 7px;

            margin: 5px 0;

            font-size: 12px;

            color: #64748b;

        }


        .requirement-icon {

            width: 15px;

            height: 15px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            border: 1px solid #cbd5e1;

            color: transparent;

            font-size: 9px;

            flex-shrink: 0;

        }


        .requirement.valid {

            color: #15803d;

        }


        .requirement.valid .requirement-icon {

            border-color: #22c55e;

            background: #22c55e;

            color: white;

        }


        /*
        |--------------------------------------------------------------------------
        | Password Match
        |--------------------------------------------------------------------------
        */

        .match-message {

            display: none;

            margin-top: 8px;

            font-size: 12px;

        }


        .match-message.match {

            display: block;

            color: #15803d;

        }


        .match-message.no-match {

            display: block;

            color: #dc2626;

        }


        /*
        |--------------------------------------------------------------------------
        | Submit Button
        |--------------------------------------------------------------------------
        */

        .submit-button {

            width: 100%;

            height: 50px;

            margin-top: 5px;

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

            color: white;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;

            box-shadow:
                0 8px 18px rgba(37, 99, 235, 0.20);

            transition:
                transform 0.15s ease,
                box-shadow 0.2s ease,
                opacity 0.2s ease;

        }


        .submit-button:hover {

            transform: translateY(-1px);

            box-shadow:
                0 11px 22px rgba(37, 99, 235, 0.25);

        }


        .submit-button:active {

            transform: translateY(0);

        }


        .submit-button:disabled {

            opacity: 0.6;

            cursor: not-allowed;

            transform: none;

            box-shadow: none;

        }


        .submit-button svg {

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


            .reset-card {

                padding: 25px 20px;

                border-radius: 16px;

            }


            .header h1 {

                font-size: 23px;

            }

        }
    </style>

</head>


<body>


    <main class="reset-wrapper">


        <div class="reset-card">


            <!-- =========================================================
                 BRAND ICON
            ========================================================== -->

            <div class="brand">

                <div class="brand-icon">

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

                </div>

            </div>


            <!-- =========================================================
                 HEADER
            ========================================================== -->

            <div class="header">

                <h1>
                    Create New Password
                </h1>

                <p>
                    Choose a strong password to secure your
                    ETS-Async account.
                </p>

            </div>


            <!-- =========================================================
                 FORM
            ========================================================== -->

            <form
                action="update_password.php"
                method="POST"
                id="resetPasswordForm">


                <!-- =====================================================
                     TOKEN
                ====================================================== -->

                <input
                    type="hidden"
                    name="token"
                    value="<?= htmlspecialchars(
                                $token,
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>">


                <!-- =====================================================
                     NEW PASSWORD
                ====================================================== -->

                <div class="form-group">

                    <label
                        class="form-label"
                        for="password">

                        <span>
                            New Password
                        </span>

                    </label>


                    <div class="password-wrapper">

                        <input
                            class="password-input"
                            type="password"
                            id="password"
                            name="password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Enter your new password">


                        <button
                            type="button"
                            class="toggle-password"
                            data-target="password"
                            aria-label="Show password"
                            title="Show password">

                            <!-- Eye -->

                            <svg
                                class="eye-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <path
                                    d="M1 12s4-8 11-8
                                       11 8 11 8-4 8-11 8
                                       -11-8-11-8z" />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="3" />

                            </svg>

                        </button>

                    </div>


                    <!-- =================================================
                         PASSWORD REQUIREMENTS
                    ================================================== -->

                    <div class="requirements">

                        <div class="requirements-title">

                            Password requirements

                        </div>


                        <div
                            class="requirement"
                            id="reqLength">

                            <span class="requirement-icon">
                                ✓
                            </span>

                            At least 8 characters

                        </div>


                        <div
                            class="requirement"
                            id="reqUpper">

                            <span class="requirement-icon">
                                ✓
                            </span>

                            At least one uppercase letter

                        </div>


                        <div
                            class="requirement"
                            id="reqLower">

                            <span class="requirement-icon">
                                ✓
                            </span>

                            At least one lowercase letter

                        </div>


                        <div
                            class="requirement"
                            id="reqNumber">

                            <span class="requirement-icon">
                                ✓
                            </span>

                            At least one number

                        </div>

                    </div>

                </div>


                <!-- =====================================================
                     CONFIRM PASSWORD
                ====================================================== -->

                <div class="form-group">

                    <label
                        class="form-label"
                        for="confirm_password">

                        <span>
                            Confirm Password
                        </span>

                    </label>


                    <div class="password-wrapper">

                        <input
                            class="password-input"
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            required
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Re-enter your new password">


                        <button
                            type="button"
                            class="toggle-password"
                            data-target="confirm_password"
                            aria-label="Show password"
                            title="Show password">

                            <!-- Eye -->

                            <svg
                                class="eye-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <path
                                    d="M1 12s4-8 11-8
                                       11 8 11 8-4 8-11 8
                                       -11-8-11-8z" />

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="3" />

                            </svg>

                        </button>

                    </div>


                    <div
                        id="matchMessage"
                        class="match-message"></div>

                </div>


                <!-- =====================================================
                     SUBMIT
                ====================================================== -->

                <button
                    type="submit"
                    class="submit-button"
                    id="submitButton">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <path
                            d="M5 12h14" />

                        <path
                            d="m12 5 7 7-7 7" />

                    </svg>

                    Reset Password

                </button>


            </form>


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

                    <path
                        d="M12 22s8-4 8-10V5l-8-3-8 3v7
                           c0 6 8 10 8 10z" />

                    <path
                        d="m9 12 2 2 4-4" />

                </svg>


                <span>

                    Your password will be securely encrypted
                    before being stored.

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


    <script>
        /*
        |--------------------------------------------------------------------------
        | Password Show / Hide
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(".toggle-password")
            .forEach(function(button) {

                button.addEventListener(
                    "click",
                    function() {

                        const targetId =
                            this.getAttribute("data-target");

                        const input =
                            document.getElementById(targetId);

                        const isPassword =
                            input.type === "password";


                        /*
                        |------------------------------------------------------
                        | Change input type
                        |------------------------------------------------------
                        */

                        input.type =
                            isPassword ?
                            "text" :
                            "password";


                        /*
                        |------------------------------------------------------
                        | Change accessibility text
                        |------------------------------------------------------
                        */

                        this.setAttribute(
                            "aria-label",
                            isPassword ?
                            "Hide password" :
                            "Show password"
                        );


                        this.setAttribute(
                            "title",
                            isPassword ?
                            "Hide password" :
                            "Show password"
                        );


                        /*
                        |------------------------------------------------------
                        | Change icon
                        |------------------------------------------------------
                        */

                        const icon =
                            this.querySelector("svg");


                        if (isPassword) {

                            icon.innerHTML = `
                                <path
                                    d="M3 3l18 18"
                                />
                                <path
                                    d="M10.58 10.58
                                       a2 2 0 0 0
                                       2.83 2.83"
                                />
                                <path
                                    d="M9.88 4.24
                                       A10.94 10.94 0 0 1
                                       12 4
                                       c7 0 11 8 11 8
                                       a18.45 18.45 0 0 1
                                       -3.08 4.27"
                                />
                                <path
                                    d="M6.61 6.61
                                       A18.56 18.56 0 0 0
                                       1 12
                                       s4 8 11 8
                                       a10.94 10.94 0 0 0
                                       5.39-1.39"
                                />
                            `;

                        } else {

                            icon.innerHTML = `
                                <path
                                    d="M1 12s4-8 11-8
                                       11 8 11 8-4 8-11 8
                                       -11-8-11-8z"
                                />
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="3"
                                />
                            `;

                        }

                    }

                );

            });


        /*
        |--------------------------------------------------------------------------
        | Password Validation
        |--------------------------------------------------------------------------
        */

        const password =
            document.getElementById("password");

        const confirmPassword =
            document.getElementById("confirm_password");

        const matchMessage =
            document.getElementById("matchMessage");

        const submitButton =
            document.getElementById("submitButton");


        const reqLength =
            document.getElementById("reqLength");

        const reqUpper =
            document.getElementById("reqUpper");

        const reqLower =
            document.getElementById("reqLower");

        const reqNumber =
            document.getElementById("reqNumber");


        /*
        |--------------------------------------------------------------------------
        | Requirement helper
        |--------------------------------------------------------------------------
        */

        function updateRequirement(
            element,
            valid
        ) {

            if (valid) {

                element.classList.add("valid");

            } else {

                element.classList.remove("valid");

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Validate password
        |--------------------------------------------------------------------------
        */

        function validatePassword() {

            const value =
                password.value;


            updateRequirement(
                reqLength,
                value.length >= 8
            );


            updateRequirement(
                reqUpper,
                /[A-Z]/.test(value)
            );


            updateRequirement(
                reqLower,
                /[a-z]/.test(value)
            );


            updateRequirement(
                reqNumber,
                /[0-9]/.test(value)
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Validate password match
        |--------------------------------------------------------------------------
        */

        function validateMatch() {

            const first =
                password.value;

            const second =
                confirmPassword.value;


            if (!second) {

                matchMessage.className =
                    "match-message";

                matchMessage.textContent =
                    "";

                return false;

            }


            if (first === second) {

                matchMessage.className =
                    "match-message match";

                matchMessage.textContent =
                    "✓ Passwords match.";

                return true;

            }


            matchMessage.className =
                "match-message no-match";

            matchMessage.textContent =
                "Passwords do not match.";

            return false;

        }


        /*
        |--------------------------------------------------------------------------
        | Password input event
        |--------------------------------------------------------------------------
        */

        password.addEventListener(
            "input",
            function() {

                validatePassword();

                validateMatch();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Confirm password event
        |--------------------------------------------------------------------------
        */

        confirmPassword.addEventListener(
            "input",
            function() {

                validateMatch();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Form submission
        |--------------------------------------------------------------------------
        */

        document
            .getElementById("resetPasswordForm")
            .addEventListener(
                "submit",
                function(event) {

                    const value =
                        password.value;

                    const matching =
                        validateMatch();


                    /*
                    |----------------------------------------------------------
                    | Require minimum password rules
                    |----------------------------------------------------------
                    */

                    const validPassword =
                        value.length >= 8 &&
                        /[A-Z]/.test(value) &&
                        /[a-z]/.test(value) &&
                        /[0-9]/.test(value);


                    if (!validPassword) {

                        event.preventDefault();

                        alert(
                            "Please make sure your password has at least 8 characters, " +
                            "one uppercase letter, one lowercase letter, " +
                            "and one number."
                        );

                        return;

                    }


                    /*
                    |----------------------------------------------------------
                    | Require matching passwords
                    |----------------------------------------------------------
                    */

                    if (!matching) {

                        event.preventDefault();

                        alert(
                            "The passwords do not match."
                        );

                        return;

                    }


                    /*
                    |----------------------------------------------------------
                    | Prevent double submission
                    |----------------------------------------------------------
                    */

                    submitButton.disabled = true;

                    submitButton.innerHTML = `
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M12 2v4"
                            />
                            <path
                                d="M12 18v4"
                            />
                            <path
                                d="m4.93 4.93 2.83 2.83"
                            />
                            <path
                                d="m16.24 16.24 2.83 2.83"
                            />
                            <path
                                d="M2 12h4"
                            />
                            <path
                                d="M18 12h4"
                            />
                        </svg>

                        Updating Password...
                    `;

                }
            );
    </script>


</body>

</html>
