<!DOCTYPE html>

<html lang="en">

<head>

    
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Student Registration | CPE Asynchronous Class</title>

    <!-- Bootstrap 5 -->
     <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        :root {
            --academic-blue: #0B4F8A;
            --academic-blue-dark: #083B66;
            --academic-blue-light: #EAF3FA;
            --text-dark: #212529;
            --text-muted: #6C757D;
            --border-color: #DEE2E6;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            font-family:
                'Segoe UI',
                Tahoma,
                Geneva,
                Verdana,
                sans-serif;

            background:
                linear-gradient(135deg,
                    #F7F9FB 0%,
                    #FFFFFF 50%,
                    #EAF3FA 100%);

            color: var(--text-dark);
            min-height: 100vh;
            margin: 0;
        }

        .navbar {
            background: #FFFFFF;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .navbar-brand {
            color: var(--academic-blue) !important;
            font-weight: 700;
            font-size: 1.3rem;
        }

        .back-link {
            color: var(--academic-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            color: var(--academic-blue-dark);
        }

        .register-wrapper {
            min-height: calc(100vh - 73px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 15px;
        }

        .register-card {
            width: 100%;
            max-width: 700px;
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .register-header {
            background: var(--academic-blue);
            color: #FFFFFF;
            padding: 35px 30px;
            text-align: center;
        }

        .register-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        .register-header h1 {
            font-size: 27px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .register-header p {
            margin: 0;
            color: #EAF3FA;
            font-size: 15px;
        }

        .register-body {
            padding: 35px;
        }

        .form-section-title {
            color: var(--academic-blue-dark);
            font-weight: 700;
            border-bottom: 2px solid var(--academic-blue-light);
            padding-bottom: 10px;
            margin-bottom: 22px;
        }

        .form-label {
            font-weight: 600;
            color: var(--academic-blue-dark);
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            height: 50px;
            border: 1px solid #CED4DA;
            border-radius: 6px;
            padding: 10px 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--academic-blue);
            box-shadow: 0 0 0 0.2rem rgba(11, 79, 138, 0.15);
        }

        .form-text {
            color: var(--text-muted);
            font-size: 12px;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 14px;
        }

        .password-toggle:hover {
            color: var(--academic-blue);
        }

        .register-button {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 6px;

            background:
                linear-gradient(135deg,
                    #0B4F8A 0%,
                    #083B66 100%);

            color: #FFFFFF;
            font-weight: 600;
            font-size: 16px;

            box-shadow:
                0 4px 12px rgba(11, 79, 138, 0.25);

            transition: all 0.2s ease;
        }

        .register-button:hover {
            background: var(--academic-blue-dark);
            transform: translateY(-1px);
        }

        .register-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .login-link {
            color: var(--academic-blue);
            font-weight: 600;
            text-decoration: none;
        }

        .login-link:hover {
            color: var(--academic-blue-dark);
            text-decoration: underline;
        }

        #registerMessage {
            display: none;
            border-radius: 6px;
            font-size: 14px;
        }

        .register-footer {
            text-align: center;
            border-top: 1px solid var(--border-color);
            padding: 20px 30px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .register-footer strong {
            color: var(--academic-blue-dark);
        }

        @media (max-width: 576px) {

            .register-wrapper {
                padding: 25px 15px;
            }

            .register-body {
                padding: 30px 22px 25px;
            }

            .register-header {
                padding: 30px 20px;
            }

            .register-header h1 {
                font-size: 24px;
            }

        }
    </style>
    

</head>

<body>

    <nav class="navbar">

        
        <div class="container">

            <div
                class="d-flex
               justify-content-between
               align-items-center
               w-100">

                <a
                    class="navbar-brand"
                    href="../index.php">

                    CPE Department

                </a>

                <a
                    href="index.php"
                    class="back-link">

                    ← Back to Home

                </a>

            </div>

        </div>
        

    </nav>

    <main class="register-wrapper">

        
        <div class="register-card">


            <div class="register-header">

                <div class="register-icon">
                    📝
                </div>

                <h1>
                    Student Registration
                </h1>

                <p>
                    CPE Asynchronous Class
                </p>

            </div>


            <div class="register-body">


                <div
                    id="registerMessage"
                    class="alert mb-4"
                    role="alert">
                </div>


                <form
                    id="registerForm"
                    autocomplete="on">


                    <h5 class="form-section-title">
                        Student Information
                    </h5>


                    <div class="row g-3">


                        <div class="col-md-4">

                            <label
                                for="last_name"
                                class="form-label">

                                Last Name

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="last_name"
                                name="last_name"
                                placeholder="Last name"
                                autocomplete="family-name"
                                maxlength="100"
                                required>

                        </div>


                        <div class="col-md-4">

                            <label
                                for="first_name"
                                class="form-label">

                                First Name

                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="first_name"
                                name="first_name"
                                placeholder="First name"
                                autocomplete="given-name"
                                maxlength="100"
                                required>

                        </div>


                        <div class="col-md-2">

                            <label
                                for="middle_initial"
                                class="form-label">

                                M.I.

                            </label>

                            <input
                                type="text"
                                class="form-control text-uppercase"
                                id="middle_initial"
                                name="middle_initial"
                                placeholder="M.I."
                                maxlength="2">

                        </div>


                        <div class="col-md-2">

                            <label
                                for="extension_name"
                                class="form-label">

                                Extension

                            </label>

                            <select
                                class="form-select"
                                id="extension_name"
                                name="extension_name">

                                <option value="">
                                    None
                                </option>

                                <option value="Jr">
                                    Jr
                                </option>


                                <option value="I">
                                    I
                                </option>

                                <option value="II">
                                    II
                                </option>

                                <option value="III">
                                    III
                                </option>

                                <option value="IV">
                                    IV
                                </option>

                            </select>

                        </div>

                    </div>


                    <div class="row g-3 mt-2">


                        <div class="col-md-6">

                            <label
                                for="department"
                                class="form-label">

                                Department

                            </label>

                            <select
                                class="form-select"
                                id="department"
                                name="department"
                                required>

                                <option
                                    value=""
                                    selected
                                    disabled>

                                    Select Department

                                </option>

                                <option value="Computer Engineering">
                                    Computer Engineering
                                </option>

                                <option value="Electrical Engineering">
                                    Electrical Engineering
                                </option>

                                <option value="Agricultural and Biosystems Engineering">
                                    Agricultural and Biosystems Engineering
                                </option>

                                <option value="Chemical Engineering">
                                    Chemical Engineering
                                </option>

                                <option value="Geodetic Engineering">
                                    Geodetic Engineering
                                </option>

                                <option value="Electronics and Communications Engineering">
                                    Electronics and Communications Engineering
                                </option>

                                <option value="Architecture">
                                    Architecture
                                </option>

                            </select>

                        </div>


                        <div class="col-md-6">

                            <label
                                for="year_section"
                                class="form-label">

                                Year & Section

                            </label>

                            <select
                                class="form-select"
                                id="year_section"
                                name="year_section"
                                required>

                                <option
                                    value=""
                                    selected
                                    disabled>

                                    Select Year & Section

                                </option>

                                <option value="1-A">
                                    1st Year - A
                                </option>

                                <option value="1-B">
                                    1st Year - B
                                </option>

                                <option value="1-C">
                                    1st Year - C
                                </option>

                                <option value="2-A">
                                    2nd Year - A
                                </option>

                                <option value="2-B">
                                    2nd Year - B
                                </option>

                                <option value="2-C">
                                    2nd Year - C
                                </option>

                                <option value="3-A">
                                    3rd Year - A
                                </option>

                                <option value="3-B">
                                    3rd Year - B
                                </option>

                                <option value="3-C">
                                    3rd Year - C
                                </option>

                                <option value="4-A">
                                    4th Year - A
                                </option>

                                <option value="4-B">
                                    4th Year - B
                                </option>

                                <option value="4-C">
                                    4th Year - C
                                </option>

                            </select>

                        </div>

                    </div>


                    <div class="mt-3 mb-4">

                        <label
                            for="student_id"
                            class="form-label">

                            Student ID

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="student_id"
                            name="student_id"
                            placeholder="Enter your student ID"
                            maxlength="50"
                            required>

                    </div>


                    <h5 class="form-section-title mt-4">
                        Account Information
                    </h5>


                    <div class="mb-4">

                        <label
                            for="email"
                            class="form-label">

                            Email Address

                        </label>

                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="Enter your email address"
                            autocomplete="email"
                            maxlength="150"
                            required>

                    </div>


                    <div class="mb-4">

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
                            placeholder="Create a username"
                            autocomplete="username"
                            minlength="4"
                            maxlength="50"
                            required>

                        <div class="form-text mt-1">

                            Username must contain at least
                            4 characters.

                        </div>

                    </div>


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
                                placeholder="Create a password"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="255"
                                required>

                            <button
                                type="button"
                                class="password-toggle"
                                id="togglePassword">

                                Show

                            </button>

                        </div>

                        <div class="form-text mt-1">

                            Password must contain at least
                            8 characters.

                        </div>

                    </div>


                    <div class="mb-4">

                        <label
                            for="confirm_password"
                            class="form-label">

                            Confirm Password

                        </label>

                        <div class="password-wrapper">

                            <input
                                type="password"
                                class="form-control"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Re-enter your password"
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="255"
                                required>

                            <button
                                type="button"
                                class="password-toggle"
                                id="toggleConfirmPassword">

                                Show

                            </button>

                        </div>

                    </div>


                    <div class="form-check mb-4">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="agree"
                            name="agree"
                            required>

                        <label
                            class="form-check-label text-muted"
                            for="agree">

                            I confirm that the information
                            I provided is accurate.

                        </label>

                    </div>


                    <button
                        type="submit"
                        class="register-button"
                        id="registerButton">

                        Create Student Account

                    </button>


                </form>


                <div class="text-center mt-4">

                    <span class="text-muted">
                        Already have an account?
                    </span>

                    <a
                        href="login.php"
                        class="login-link ms-1">

                        Login here

                    </a>

                </div>


            </div>


            <div class="register-footer">

                <div>
                    <strong>
                        Engr. Karl Stephen Evallo
                    </strong>
                </div>

                <div>
                    Computer Engineering Department
                </div>

                <div class="mt-1">
                    Asynchronous Learning Portal
                </div>

            </div>


        </div>
        

    </main>

    <script>
        /* =========================================================
   API CONFIGURATION
   ========================================================= */

        const REGISTER_API = "./api/register.php";


        /* =========================================================
           PASSWORD TOGGLE
           ========================================================= */

        function setupPasswordToggle(inputId, buttonId) {

            const input =
                document.getElementById(inputId);

            const button =
                document.getElementById(buttonId);

            button.addEventListener(
                "click",
                function() {

                    if (input.type === "password") {

                        input.type = "text";
                        button.textContent = "Hide";

                    } else {

                        input.type = "password";
                        button.textContent = "Show";

                    }

                }
            );

        }


        setupPasswordToggle(
            "password",
            "togglePassword"
        );


        setupPasswordToggle(
            "confirm_password",
            "toggleConfirmPassword"
        );


        /* =========================================================
           MESSAGE FUNCTION
           ========================================================= */

        function showMessage(message, type) {

            const messageBox =
                document.getElementById(
                    "registerMessage"
                );

            messageBox.className =
                "alert mb-4 alert-" + type;

            messageBox.textContent =
                message;

            messageBox.style.display =
                "block";

            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });

        }


        /* =========================================================
           FORM
           ========================================================= */

        const registerForm =
            document.getElementById(
                "registerForm"
            );


        const registerButton =
            document.getElementById(
                "registerButton"
            );


        registerForm.addEventListener(
            "submit",
            async function(event) {

                event.preventDefault();


                /* =================================================
                   GET VALUES
                   ================================================= */

                const lastName =
                    document
                    .getElementById("last_name")
                    .value
                    .trim();


                const firstName =
                    document
                    .getElementById("first_name")
                    .value
                    .trim();


                const middleInitial =
                    document
                    .getElementById("middle_initial")
                    .value
                    .trim()
                    .toUpperCase();


                const extensionName =
                    document
                    .getElementById("extension_name")
                    .value;


                const department =
                    document
                    .getElementById("department")
                    .value;


                const yearSection =
                    document
                    .getElementById("year_section")
                    .value;


                const studentId =
                    document
                    .getElementById("student_id")
                    .value
                    .trim();


                const email =
                    document
                    .getElementById("email")
                    .value
                    .trim()
                    .toLowerCase();


                const username =
                    document
                    .getElementById("username")
                    .value
                    .trim();


                const password =
                    document
                    .getElementById("password")
                    .value;


                const confirmPassword =
                    document
                    .getElementById("confirm_password")
                    .value;


                const agree =
                    document
                    .getElementById("agree")
                    .checked;


                /* =================================================
                   CLIENT VALIDATION
                   ================================================= */

                if (!agree) {

                    showMessage(
                        "Please confirm that the information you provided is accurate.",
                        "warning"
                    );

                    return;

                }


                if (lastName.length < 2) {

                    showMessage(
                        "Please enter a valid last name.",
                        "warning"
                    );

                    return;

                }


                if (firstName.length < 2) {

                    showMessage(
                        "Please enter a valid first name.",
                        "warning"
                    );

                    return;

                }


                if (
                    middleInitial !== "" &&
                    !/^[A-Z]{1,2}$/.test(middleInitial)
                ) {

                    showMessage(
                        "Invalid middle initial.",
                        "warning"
                    );

                    return;

                }


                if (!department) {

                    showMessage(
                        "Please select a department.",
                        "warning"
                    );

                    return;

                }


                if (!yearSection) {

                    showMessage(
                        "Please select your year and section.",
                        "warning"
                    );

                    return;

                }


                if (studentId.length < 3) {

                    showMessage(
                        "Please enter a valid student ID.",
                        "warning"
                    );

                    return;

                }


                if (!email) {

                    showMessage(
                        "Please enter your email address.",
                        "warning"
                    );

                    return;

                }


                if (username.length < 4) {

                    showMessage(
                        "Username must contain at least 4 characters.",
                        "warning"
                    );

                    return;

                }


                if (
                    !/^[A-Za-z0-9_.-]+$/.test(username)
                ) {

                    showMessage(
                        "Username may only contain letters, numbers, underscores, periods, and hyphens.",
                        "warning"
                    );

                    return;

                }


                if (password.length < 8) {

                    showMessage(
                        "Password must contain at least 8 characters.",
                        "warning"
                    );

                    return;

                }


                if (password !== confirmPassword) {

                    showMessage(
                        "Passwords do not match.",
                        "danger"
                    );

                    return;

                }


                /* =================================================
                   DISABLE BUTTON
                   ================================================= */

                registerButton.disabled = true;

                registerButton.textContent =
                    "Creating Account...";


                /* =================================================
                   CREATE GET PARAMETERS
                   ================================================= */

                const params =
                    new URLSearchParams({

                        last_name: lastName,

                        first_name: firstName,

                        middle_initial: middleInitial,

                        extension_name: extensionName,

                        department: department,

                        year_section: yearSection,

                        student_id: studentId,

                        email: email,

                        username: username,

                        password: password

                    });


                /* =================================================
                   SEND GET REQUEST
                   ================================================= */

                try {

                    const response =
                        await fetch(
                            REGISTER_API +
                            "?" +
                            params.toString(), {

                                method: "GET",

                                headers: {
                                    "Accept": "application/json"
                                }

                            }
                        );


                    /* =================================================
                       CHECK HTTP RESPONSE
                       ================================================= */

                    const contentType =
                        response.headers.get(
                            "content-type"
                        ) || "";


                    if (
                        !contentType.includes(
                            "application/json"
                        )
                    ) {

                        const text =
                            await response.text();

                        console.error(
                            "Invalid API response:",
                            text
                        );

                        throw new Error(
                            "API did not return JSON."
                        );

                    }


                    const data =
                        await response.json();


                    /* =================================================
                       SUCCESS
                       ================================================= */

                    if (
                        response.ok &&
                        data.success
                    ) {

                        showMessage(
                            data.message ||
                            "Registration successful.",
                            "success"
                        );


                        registerForm.reset();


                        setTimeout(
                            function() {

                                window.location.href =
                                    "login.php";

                            },
                            1200
                        );

                        return;

                    }


                    /* =================================================
                       API ERROR
                       ================================================= */

                    showMessage(
                        data.message ||
                        "Registration failed. Please try again.",
                        "danger"
                    );


                    registerButton.disabled =
                        false;

                    registerButton.textContent =
                        "Create Student Account";


                } catch (error) {

                    console.error(
                        "Registration error:",
                        error
                    );


                    showMessage(
                        "Unable to connect to the registration server. Please check your API URL.",
                        "danger"
                    );


                    registerButton.disabled =
                        false;

                    registerButton.textContent =
                        "Create Student Account";

                }

            }
        );
    </script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>

</html>