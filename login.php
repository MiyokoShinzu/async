<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Login | ETS-Async</title>


    <!-- Bootstrap 5 -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        /* =========================================================
   ETS-ASYNC PROFESSIONAL LOGIN
   Academic / University Portal Design
   ========================================================= */


        /* =========================================================
   ROOT VARIABLES
========================================================= */

        :root {

            --academic-blue: #0B4F8A;
            --academic-blue-dark: #073A66;
            --academic-blue-light: #EAF3FA;
            --academic-blue-soft: #F4F8FC;

            --text-dark: #1F2937;
            --text-muted: #6B7280;

            --border-color: #D9E2EC;

            --white: #FFFFFF;

            --success: #198754;
            --danger: #DC3545;
            --warning: #D99A00;

            --shadow-sm:
                0 4px 12px rgba(0, 0, 0, 0.05);

            --shadow-md:
                0 15px 40px rgba(11, 79, 138, 0.12);

            --shadow-lg:
                0 25px 70px rgba(11, 79, 138, 0.18);

            --gradient-primary:
                linear-gradient(135deg,
                    #0B4F8A 0%,
                    #073A66 100%);

        }


        /* =========================================================
   RESET
========================================================= */

        * {

            box-sizing: border-box;

        }


        html {

            scroll-behavior: smooth;

        }


        body {

            margin: 0;

            min-height: 100vh;

            font-family:
                "Segoe UI",
                Tahoma,
                Geneva,
                Verdana,
                sans-serif;

            color: var(--text-dark);

            background:
                linear-gradient(135deg,
                    #F7FAFD 0%,
                    #EAF3FA 45%,
                    #F8FAFC 100%);

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 30px;

            overflow-x: hidden;

            position: relative;

        }


        /* =========================================================
   ANIMATED BACKGROUND
========================================================= */

        body::before {

            content: "";

            position: fixed;

            width: 500px;

            height: 500px;

            border-radius: 50%;

            background:
                rgba(11, 79, 138, 0.06);

            top: -180px;

            left: -180px;

            filter: blur(5px);

            animation:
                backgroundFloat 12s ease-in-out infinite;

            pointer-events: none;

        }


        body::after {

            content: "";

            position: fixed;

            width: 450px;

            height: 450px;

            border-radius: 50%;

            background:
                rgba(8, 59, 102, 0.05);

            bottom: -180px;

            right: -150px;

            filter: blur(5px);

            animation:
                backgroundFloatReverse 15s ease-in-out infinite;

            pointer-events: none;

        }


        /* =========================================================
   BACKGROUND ANIMATIONS
========================================================= */

        @keyframes backgroundFloat {

            0%,
            100% {

                transform:
                    translate(0, 0) scale(1);

            }

            50% {

                transform:
                    translate(60px, 40px) scale(1.08);

            }

        }


        @keyframes backgroundFloatReverse {

            0%,
            100% {

                transform:
                    translate(0, 0) scale(1);

            }

            50% {

                transform:
                    translate(-50px, -35px) scale(1.06);

            }

        }


        /* =========================================================
   LOGIN CARD
========================================================= */

        .login-card {

            width: 100%;

            max-width: 430px;

            background:
                rgba(255, 255, 255, 0.97);

            border:
                1px solid rgba(217, 226, 236, 0.9);

            border-radius: 18px;

            padding:
                42px 40px;

            box-shadow:
                var(--shadow-lg);

            position: relative;

            z-index: 2;

            animation:
                loginCardEntrance 0.8s cubic-bezier(0.16,
                    1,
                    0.3,
                    1);

        }


        /* =========================================================
   CARD TOP ACCENT
========================================================= */

        .login-card::before {

            content: "";

            position: absolute;

            top: 0;

            left: 0;

            right: 0;

            height: 5px;

            background:
                var(--gradient-primary);

            border-radius:
                18px 18px 0 0;

        }


        /* =========================================================
   CARD ENTRANCE
========================================================= */

        @keyframes loginCardEntrance {

            from {

                opacity: 0;

                transform:
                    translateY(35px) scale(0.97);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0) scale(1);

            }

        }


        /* =========================================================
   LOGO
========================================================= */

        .logo {

            width: 78px;

            height: 78px;

            margin:
                0 auto 22px;

            border-radius: 50%;

            background:
                linear-gradient(135deg,
                    #EAF3FA,
                    #DDECF8);

            border:
                1px solid #D2E3F0;

            color:
                var(--academic-blue);

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 30px;

            box-shadow:
                0 8px 25px rgba(11, 79, 138, 0.12);

            animation:
                logoEntrance 1s ease-out 0.2s both;

        }


        /* =========================================================
   LOGO HOVER
========================================================= */

        .logo:hover {

            transform:
                translateY(-3px) rotate(-3deg);

            box-shadow:
                0 12px 30px rgba(11, 79, 138, 0.18);

        }


        /* =========================================================
   LOGO ANIMATION
========================================================= */

        @keyframes logoEntrance {

            from {

                opacity: 0;

                transform:
                    translateY(-15px) scale(0.8);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0) scale(1);

            }

        }


        /* =========================================================
   LOGIN TITLE
========================================================= */

        h1 {

            color:
                var(--academic-blue-dark);

            font-size:
                30px;

            font-weight:
                700;

            letter-spacing:
                -0.5px;

            text-align:
                center;

            margin:
                0 0 6px;

        }


        /* =========================================================
   SUBTITLE
========================================================= */

        .subtitle {

            color:
                var(--text-muted);

            text-align:
                center;

            font-size:
                14px;

            margin:
                0 0 32px;

        }


        /* =========================================================
   FORM LABEL
========================================================= */

        .form-label {

            color:
                var(--academic-blue-dark);

            font-size:
                14px;

            font-weight:
                600;

            margin-bottom:
                8px;

        }


        /* =========================================================
   INPUT GROUP
========================================================= */

        .input-group {

            position: relative;

        }


        /* =========================================================
   FORM CONTROL
========================================================= */

        .form-control {

            height:
                50px;

            border:
                1px solid var(--border-color);

            border-radius:
                8px;

            background:
                #FFFFFF;

            color:
                var(--text-dark);

            font-size:
                15px;

            padding:
                12px 15px;

            transition:
                border-color 0.25s ease,
                box-shadow 0.25s ease,
                transform 0.25s ease;

        }


        .form-control::placeholder {

            color:
                #9CA3AF;

        }


        /* =========================================================
   INPUT HOVER
========================================================= */

        .form-control:hover {

            border-color:
                #B8CBDC;

        }


        /* =========================================================
   INPUT FOCUS
========================================================= */

        .form-control:focus {

            border-color:
                var(--academic-blue);

            background:
                #FFFFFF;

            box-shadow:
                0 0 0 4px rgba(11, 79, 138, 0.10);

            outline:
                none;

            transform:
                translateY(-1px);

        }


        /* =========================================================
   PASSWORD WRAPPER
========================================================= */

        .password-wrapper {

            position:
                relative;

        }


        .password-wrapper .form-control {

            padding-right:
                65px;

        }


        /* =========================================================
   SHOW PASSWORD
========================================================= */

        .show-password {

            position:
                absolute;

            right:
                12px;

            top:
                50%;

            transform:
                translateY(-50%);

            border:
                none;

            background:
                transparent;

            color:
                var(--text-muted);

            font-size:
                13px;

            font-weight:
                600;

            padding:
                5px 7px;

            border-radius:
                5px;

            cursor:
                pointer;

        }


        .show-password:hover {

            color:
                var(--academic-blue);

            background:
                var(--academic-blue-soft);

        }


        /* =========================================================
   LOGIN BUTTON
========================================================= */

        .login-button {

            width:
                100%;

            height:
                50px;

            border:
                none;

            border-radius:
                8px;

            background:
                var(--gradient-primary);

            color:
                #FFFFFF;

            font-size:
                15px;

            font-weight:
                600;

            letter-spacing:
                0.1px;

            cursor:
                pointer;

            box-shadow:
                0 6px 18px rgba(11, 79, 138, 0.22);

            position:
                relative;

            overflow:
                hidden;

            transition:
                transform 0.25s ease,
                box-shadow 0.25s ease,
                background 0.25s ease;

        }


        /* =========================================================
   BUTTON SHINE
========================================================= */

        .login-button::before {

            content: "";

            position:
                absolute;

            top:
                0;

            left:
                -100%;

            width:
                70%;

            height:
                100%;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.18),
                    transparent);

            transform:
                skewX(-20deg);

        }


        /* =========================================================
   BUTTON HOVER
========================================================= */

        .login-button:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 10px 25px rgba(11, 79, 138, 0.28);

            background:
                linear-gradient(135deg,
                    #0D5A9D,
                    #073A66);

        }


        .login-button:hover::before {

            animation:
                buttonShine 0.8s ease;

        }


        /* =========================================================
   BUTTON ACTIVE
========================================================= */

        .login-button:active {

            transform:
                translateY(0);

            box-shadow:
                0 4px 12px rgba(11, 79, 138, 0.20);

        }


        /* =========================================================
   BUTTON DISABLED
========================================================= */

        .login-button:disabled {

            opacity:
                0.7;

            cursor:
                not-allowed;

            transform:
                none;

            box-shadow:
                none;

        }


        /* =========================================================
   BUTTON SHINE ANIMATION
========================================================= */

        @keyframes buttonShine {

            from {

                left:
                    -100%;

            }

            to {

                left:
                    130%;

            }

        }


        /* =========================================================
   LOGIN MESSAGE
========================================================= */

        #loginMessage {

            display:
                none;

            font-size:
                14px;

            border-radius:
                8px;

            border:
                none;

            animation:
                messageEntrance 0.35s ease;

        }


        /* =========================================================
   MESSAGE ANIMATION
========================================================= */

        @keyframes messageEntrance {

            from {

                opacity:
                    0;

                transform:
                    translateY(-5px);

            }

            to {

                opacity:
                    1;

                transform:
                    translateY(0);

            }

        }


        /* =========================================================
   REGISTER AREA
========================================================= */

        .register {

            text-align:
                center;

            margin-top:
                26px;

            padding-top:
                20px;

            border-top:
                1px solid #EEF2F6;

            color:
                var(--text-muted);

            font-size:
                14px;

        }


        .register a {

            color:
                var(--academic-blue);

            font-weight:
                600;

            text-decoration:
                none;

            margin-left:
                3px;

            transition:
                color 0.2s ease;

        }


        .register a:hover {

            color:
                var(--academic-blue-dark);

            text-decoration:
                underline;

        }


        /* =========================================================
   FORM SPACING
========================================================= */

        .mb-3 {

            margin-bottom:
                20px !important;

        }


        .mb-4 {

            margin-bottom:
                24px !important;

        }


        /* =========================================================
   RESPONSIVE
========================================================= */

        @media (max-width: 576px) {

            body {

                padding:
                    20px;

                align-items:
                    center;

            }


            .login-card {

                max-width:
                    100%;

                padding:
                    35px 25px;

                border-radius:
                    15px;

            }


            .login-card::before {

                border-radius:
                    15px 15px 0 0;

            }


            .logo {

                width:
                    70px;

                height:
                    70px;

                font-size:
                    27px;

            }


            h1 {

                font-size:
                    27px;

            }

        }


        /* =========================================================
   SMALL MOBILE DEVICES
========================================================= */

        @media (max-width: 380px) {

            body {

                padding:
                    15px;

            }


            .login-card {

                padding:
                    30px 20px;

            }


            h1 {

                font-size:
                    25px;

            }


            .form-control,
            .login-button {

                height:
                    48px;

            }

        }


        /* =========================================================
   REDUCED MOTION
========================================================= */

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {

                animation-duration:
                    0.01ms !important;

                animation-iteration-count:
                    1 !important;

                transition-duration:
                    0.01ms !important;

                scroll-behavior:
                    auto !important;

            }

        }
    </style>



</head>


<body>


    <div class="login-card">


        <!-- LOGO -->

        <div class="logo">

            🔐

        </div>


        <!-- TITLE -->

        <h1>

            Login

        </h1>


        <p class="subtitle">

            ETS-Async Learning Portal

        </p>


        <!-- MESSAGE -->

        <div
            id="loginMessage"
            class="alert"
            role="alert">
        </div>


        <!-- FORM -->

        <form id="loginForm">


            <!-- USERNAME -->

            <div class="mb-3">

                <label
                    for="username"
                    class="form-label">

                    Username

                </label>


                <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    placeholder="Enter username"
                    autocomplete="username"
                    required>

            </div>


            <!-- PASSWORD -->

            <div class="mb-4">

                <label
                    for="password"
                    class="form-label">

                    Password

                </label>


                <div class="password-wrapper">

                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        placeholder="Enter password"
                        autocomplete="current-password"
                        required>


                    <button
                        type="button"
                        class="show-password"
                        id="togglePassword">

                        Show

                    </button>

                </div>

            </div>


            <!-- LOGIN BUTTON -->

            <button
                type="submit"
                class="login-button"
                id="loginButton">

                Login

            </button>


        </form>


        <!-- REGISTER -->

        <div class="register">

            Don't have an account?

            <a href="register.php">

                Register

            </a>

        </div>


    </div>


    <script>
        /* =========================================
           API
        ========================================= */

        const LOGIN_API =
            "./api/login.php";



        /* =========================================
           PASSWORD SHOW / HIDE
        ========================================= */

        const passwordInput =
            document.getElementById(
                "password"
            );


        const togglePassword =
            document.getElementById(
                "togglePassword"
            );


        togglePassword.addEventListener(
            "click",
            function() {

                if (
                    passwordInput.type ===
                    "password"
                ) {

                    passwordInput.type =
                        "text";

                    togglePassword.textContent =
                        "Hide";

                } else {

                    passwordInput.type =
                        "password";

                    togglePassword.textContent =
                        "Show";

                }

            }
        );



        /* =========================================
           SHOW MESSAGE
        ========================================= */

        function showMessage(
            message,
            type
        ) {

            const messageBox =
                document.getElementById(
                    "loginMessage"
                );


            messageBox.className =
                "alert alert-" + type;


            messageBox.textContent =
                message;


            messageBox.style.display =
                "block";

        }



        /* =========================================
           HIDE MESSAGE
        ========================================= */

        function hideMessage() {

            const messageBox =
                document.getElementById(
                    "loginMessage"
                );


            messageBox.style.display =
                "none";


            messageBox.textContent =
                "";

        }



        /* =========================================
           LOGIN FORM
        ========================================= */

        document
            .getElementById("loginForm")
            .addEventListener(
                "submit",
                async function(event) {

                    event.preventDefault();


                    /* =================================
                       GET ELEMENTS
                    ================================= */

                    const username =
                        document
                        .getElementById(
                            "username"
                        )
                        .value
                        .trim();


                    const passwordValue =
                        document
                        .getElementById(
                            "password"
                        )
                        .value;


                    const button =
                        document
                        .getElementById(
                            "loginButton"
                        );


                    /* =================================
                       CLEAR OLD MESSAGE
                    ================================= */

                    hideMessage();


                    /* =================================
                       VALIDATION
                    ================================= */

                    if (
                        username === "" ||
                        passwordValue === ""
                    ) {

                        showMessage(
                            "Please enter your username and password.",
                            "warning"
                        );

                        return;

                    }


                    /* =================================
                       DISABLE BUTTON
                    ================================= */

                    button.disabled =
                        true;

                    button.textContent =
                        "Logging in...";


                    try {


                        /* =============================
                           SEND REQUEST
                        ============================= */

                        const response =
                            await fetch(
                                LOGIN_API, {

                                    method: "POST",

                                    headers: {

                                        "Content-Type": "application/json"

                                    },

                                    body: JSON.stringify({

                                        username: username,

                                        password: passwordValue

                                    })

                                }
                            );


                        /* =============================
                           READ RESPONSE
                        ============================= */

                        const data =
                            await response.json();


                        console.log(
                            "Login response:",
                            data
                        );


                        /* =============================
                           SUCCESS
                        ============================= */

                        if (
                            response.ok &&
                            data.success === true
                        ) {


                            showMessage(
                                data.message ||
                                "Login successful.",
                                "success"
                            );


                            /*
                             * Use the redirect
                             * supplied by the API.
                             */

                            setTimeout(
                                function() {

                                    window.location.href =
                                        data.redirect;

                                },
                                700
                            );


                            return;

                        }


                        /* =============================
                           LOGIN FAILED
                        ============================= */

                        showMessage(
                            data.message ||
                            "Invalid username or password.",
                            "danger"
                        );


                        /*
                         * IMPORTANT:
                         * Re-enable button when
                         * login fails.
                         */

                        button.disabled =
                            false;

                        button.textContent =
                            "Login";


                    } catch (error) {


                        /* =============================
                           NETWORK / SERVER ERROR
                        ============================= */

                        console.error(
                            "Login error:",
                            error
                        );


                        showMessage(
                            "Unable to connect to the server. Please try again.",
                            "danger"
                        );


                        button.disabled =
                            false;

                        button.textContent =
                            "Login";

                    }

                }
            );
    </script>


</body>

</html>