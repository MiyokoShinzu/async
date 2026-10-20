<?php
/* =========================================================
   STUDENT ACCOUNT SETTINGS
   ETS-Async Learning Portal
   ========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION CHECK
   ========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user"]) ||
    !isset($_SESSION["user"]["access"]) ||
    $_SESSION["user"]["access"] !== "student"
) {
    header("Location: ../login.php");
    exit;
}


/* =========================================================
   DATABASE CONNECTION
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   SESSION DATA
   ========================================================= */

$user = $_SESSION["user"];

$studentId = $user["student_id"] ?? "";

if (empty($studentId)) {
    die("Student account information could not be found.");
}


/* =========================================================
   VARIABLES
   ========================================================= */

$successMessage = "";
$errorMessage = "";

$lastName = "";
$firstName = "";
$middleInitial = "";
$extensionName = "";

$department = "";
$yearSection = "";
$email = "";
$username = "";


/* =========================================================
   LOAD CURRENT STUDENT INFORMATION
   ========================================================= */

$stmt = $mysqli->prepare("
    SELECT
        id,
        last_name,
        first_name,
        middle_initial,
        extension_name,
        department,
        year_section,
        student_id,
        email,
        username,
        access,
        created_at
    FROM accounts
    WHERE student_id = ?
      AND access = 'student'
    LIMIT 1
");

if (!$stmt) {
    die("Database error: " . $mysqli->error);
}

$stmt->bind_param(
    "s",
    $studentId
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {

    $stmt->close();

    die("Student account could not be found.");
}

$account = $result->fetch_assoc();

$stmt->close();


/* =========================================================
   LOAD ACCOUNT VALUES
   ========================================================= */

$lastName = $account["last_name"] ?? "";
$firstName = $account["first_name"] ?? "";
$middleInitial = $account["middle_initial"] ?? "";
$extensionName = $account["extension_name"] ?? "";

$department = $account["department"] ?? "";
$yearSection = $account["year_section"] ?? "";
$email = $account["email"] ?? "";
$username = $account["username"] ?? "";


/* =========================================================
   PROCESS FORM SUBMISSION
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /* =====================================================
       GET ONLY EDITABLE FORM DATA
       ===================================================== */

    $lastName = trim(
        $_POST["last_name"] ?? ""
    );

    $firstName = trim(
        $_POST["first_name"] ?? ""
    );

    $middleInitial = trim(
        $_POST["middle_initial"] ?? ""
    );

    $extensionName = trim(
        $_POST["extension_name"] ?? ""
    );

    $username = trim(
        $_POST["username"] ?? ""
    );


    /* =====================================================
       PASSWORD FIELDS
       ===================================================== */

    $currentPassword =
        $_POST["current_password"] ?? "";

    $newPassword =
        $_POST["new_password"] ?? "";

    $confirmPassword =
        $_POST["confirm_password"] ?? "";


    /* =====================================================
       BASIC VALIDATION
       ===================================================== */

    if (
        $lastName === "" ||
        $firstName === "" ||
        $username === ""
    ) {

        $errorMessage =
            "Please complete all required fields.";
    }


    /* =====================================================
       MIDDLE INITIAL VALIDATION
       ===================================================== */

    if (
        empty($errorMessage) &&
        $middleInitial !== "" &&
        !preg_match(
            '/^[A-Za-z]{1,2}$/',
            $middleInitial
        )
    ) {

        $errorMessage =
            "Middle initial must contain only letters.";
    }


    /* =====================================================
       USERNAME VALIDATION
       ===================================================== */

    if (
        empty($errorMessage) &&
        !preg_match(
            '/^[A-Za-z0-9._-]{3,50}$/',
            $username
        )
    ) {

        $errorMessage =
            "Username must be 3 to 50 characters and may contain letters, numbers, dots, underscores, and hyphens.";
    }


    /* =====================================================
       CHECK USERNAME DUPLICATE
       ===================================================== */

    if (empty($errorMessage)) {

        $stmt = $mysqli->prepare("
            SELECT id
            FROM accounts
            WHERE username = ?
              AND id <> ?
            LIMIT 1
        ");

        if (!$stmt) {

            $errorMessage =
                "Database error occurred.";
        } else {

            $stmt->bind_param(
                "si",
                $username,
                $account["id"]
            );

            $stmt->execute();

            $duplicateUsername =
                $stmt->get_result();

            if (
                $duplicateUsername->num_rows > 0
            ) {

                $errorMessage =
                    "That username is already being used by another account.";
            }

            $stmt->close();
        }
    }


    /* =====================================================
       CHECK PASSWORD CHANGE
       ===================================================== */

    $changingPassword =
        $currentPassword !== "" ||
        $newPassword !== "" ||
        $confirmPassword !== "";


    /* =====================================================
       PASSWORD FIELD VALIDATION
       ===================================================== */

    if (
        empty($errorMessage) &&
        $changingPassword
    ) {

        if (
            $currentPassword === "" ||
            $newPassword === "" ||
            $confirmPassword === ""
        ) {

            $errorMessage =
                "To change your password, complete all password fields.";
        }
    }


    /* =====================================================
       VERIFY CURRENT PASSWORD
       ===================================================== */

    if (
        empty($errorMessage) &&
        $changingPassword
    ) {

        $stmt = $mysqli->prepare("
            SELECT password
            FROM accounts
            WHERE id = ?
              AND student_id = ?
              AND access = 'student'
            LIMIT 1
        ");

        if (!$stmt) {

            $errorMessage =
                "Database error occurred.";
        } else {

            $stmt->bind_param(
                "is",
                $account["id"],
                $studentId
            );

            $stmt->execute();

            $passwordResult =
                $stmt->get_result();

            $passwordAccount =
                $passwordResult->fetch_assoc();

            $stmt->close();


            if (
                !$passwordAccount ||
                !password_verify(
                    $currentPassword,
                    $passwordAccount["password"]
                )
            ) {

                $errorMessage =
                    "Your current password is incorrect.";
            }
        }
    }


    /* =====================================================
       NEW PASSWORD VALIDATION
       ===================================================== */

    if (
        empty($errorMessage) &&
        $changingPassword
    ) {

        if (
            strlen($newPassword) < 8
        ) {

            $errorMessage =
                "Your new password must contain at least 8 characters.";
        }
    }


    /* =====================================================
       CONFIRM NEW PASSWORD
       ===================================================== */

    if (
        empty($errorMessage) &&
        $changingPassword
    ) {

        if (
            $newPassword !== $confirmPassword
        ) {

            $errorMessage =
                "The new password and confirmation password do not match.";
        }
    }


    /* =====================================================
       UPDATE ACCOUNT
       ===================================================== */

    if (empty($errorMessage)) {

        $mysqli->begin_transaction();

        try {


            /* =================================================
               UPDATE WITH PASSWORD
               ================================================= */

            if ($changingPassword) {

                $hashedPassword =
                    password_hash(
                        $newPassword,
                        PASSWORD_DEFAULT
                    );


                $stmt = $mysqli->prepare("
                    UPDATE accounts
                    SET
                        last_name = ?,
                        first_name = ?,
                        middle_initial = ?,
                        extension_name = ?,
                        username = ?,
                        password = ?
                    WHERE id = ?
                      AND student_id = ?
                      AND access = 'student'
                ");


                if (!$stmt) {

                    throw new Exception(
                        "Unable to prepare account update."
                    );
                }


                $stmt->bind_param(
                    "ssssssis",
                    $lastName,
                    $firstName,
                    $middleInitial,
                    $extensionName,
                    $username,
                    $hashedPassword,
                    $account["id"],
                    $studentId
                );
            }


            /* =================================================
               UPDATE WITHOUT PASSWORD
               ================================================= */ else {

                $stmt = $mysqli->prepare("
                    UPDATE accounts
                    SET
                        last_name = ?,
                        first_name = ?,
                        middle_initial = ?,
                        extension_name = ?,
                        username = ?
                    WHERE id = ?
                      AND student_id = ?
                      AND access = 'student'
                ");


                if (!$stmt) {

                    throw new Exception(
                        "Unable to prepare account update."
                    );
                }


                $stmt->bind_param(
                    "sssssis",
                    $lastName,
                    $firstName,
                    $middleInitial,
                    $extensionName,
                    $username,
                    $account["id"],
                    $studentId
                );
            }


            /* =================================================
               EXECUTE UPDATE
               ================================================= */

            if (!$stmt->execute()) {

                throw new Exception(
                    "Unable to update your account."
                );
            }


            $stmt->close();


            /* =================================================
               COMMIT
               ================================================= */

            $mysqli->commit();


            /* =================================================
               UPDATE SESSION
               ================================================= */

            $_SESSION["user"]["last_name"] =
                $lastName;

            $_SESSION["user"]["first_name"] =
                $firstName;

            $_SESSION["user"]["middle_initial"] =
                $middleInitial;

            $_SESSION["user"]["extension_name"] =
                $extensionName;

            $_SESSION["user"]["username"] =
                $username;


            /* =================================================
               UPDATE LOCAL ACCOUNT DATA
               ================================================= */

            $account["last_name"] =
                $lastName;

            $account["first_name"] =
                $firstName;

            $account["middle_initial"] =
                $middleInitial;

            $account["extension_name"] =
                $extensionName;

            $account["username"] =
                $username;


            /* =================================================
               SUCCESS MESSAGE
               ================================================= */

            if ($changingPassword) {

                $successMessage =
                    "Your account details and password have been updated successfully.";
            } else {

                $successMessage =
                    "Your account details have been updated successfully.";
            }
        } catch (Exception $e) {

            $mysqli->rollback();

            $errorMessage =
                $e->getMessage();
        }
    }
}


/* =========================================================
   GLOBALS
   ========================================================= */

include "globals/head.php";
include "globals/sidebar.php";
include "globals/topbar.php";

?>


<!-- =========================================================
     MAIN CONTENT
     ========================================================= -->

<main class="main-content">

    <div class="content-wrapper">


        <!-- =====================================================
             PAGE HEADER
        ====================================================== -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h2
                    class="mb-1"
                    style="font-weight: 700;">

                    <i class="bi bi-person-gear me-2"></i>

                    Account Settings

                </h2>

                <p
                    class="mb-0"
                    style="color: var(--text-secondary);">

                    Update your personal information and account password.

                </p>

            </div>

        </div>


        <!-- =====================================================
             SUCCESS MESSAGE
        ====================================================== -->

        <?php if (!empty($successMessage)): ?>

            <div
                class="alert alert-success d-flex align-items-start mb-4"
                role="alert">

                <i
                    class="bi bi-check-circle-fill me-2 mt-1"></i>

                <div>

                    <?= htmlspecialchars($successMessage) ?>

                </div>

            </div>

        <?php endif; ?>


        <!-- =====================================================
             ERROR MESSAGE
        ====================================================== -->

        <?php if (!empty($errorMessage)): ?>

            <div
                class="alert alert-danger d-flex align-items-start mb-4"
                role="alert">

                <i
                    class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>

                <div>

                    <?= htmlspecialchars($errorMessage) ?>

                </div>

            </div>

        <?php endif; ?>


        <!-- =====================================================
             ACCOUNT FORM
        ====================================================== -->

        <form
            method="POST"
            action=""
            autocomplete="off">

            <div class="row g-4">


                <!-- =================================================
                     PERSONAL INFORMATION
                ================================================== -->

                <div class="col-12 col-xl-7">

                    <div class="profile-card">


                        <!-- =========================================
                             HEADER
                        ========================================== -->

                        <div class="profile-card-header">

                            <div class="profile-card-icon">

                                <i class="bi bi-person"></i>

                            </div>

                            <div>

                                <h5>
                                    Personal Information
                                </h5>

                                <p>
                                    Information you can update
                                </p>

                            </div>

                        </div>


                        <!-- =========================================
                             BODY
                        ========================================== -->

                        <div class="profile-card-body">

                            <div class="row g-3">


                                <!-- =================================
                                     LAST NAME
                                ================================== -->

                                <div class="col-md-6">

                                    <label
                                        for="last_name"
                                        class="form-label">

                                        Last Name

                                        <span class="required">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="last_name"
                                        name="last_name"
                                        value="<?= htmlspecialchars($lastName) ?>"
                                        maxlength="100"
                                        required>

                                </div>


                                <!-- =================================
                                     FIRST NAME
                                ================================== -->

                                <div class="col-md-6">

                                    <label
                                        for="first_name"
                                        class="form-label">

                                        First Name

                                        <span class="required">
                                            *
                                        </span>

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="first_name"
                                        name="first_name"
                                        value="<?= htmlspecialchars($firstName) ?>"
                                        maxlength="100"
                                        required>

                                </div>


                                <!-- =================================
                                     MIDDLE INITIAL
                                ================================== -->

                                <div class="col-md-6">

                                    <label
                                        for="middle_initial"
                                        class="form-label">

                                        Middle Initial

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="middle_initial"
                                        name="middle_initial"
                                        value="<?= htmlspecialchars($middleInitial) ?>"
                                        maxlength="2">

                                </div>


                                <!-- =================================
                                     EXTENSION NAME
                                ================================== -->

                                <div class="col-md-6">

                                    <label
                                        for="extension_name"
                                        class="form-label">

                                        Extension

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="extension_name"
                                        name="extension_name"
                                        value="<?= htmlspecialchars($extensionName) ?>"
                                        maxlength="10"
                                        placeholder="Jr, Sr, III">

                                </div>


                                <!-- =================================
                                     STUDENT ID
                                ================================== -->

                                <div class="col-md-4">

                                    <label
                                        for="student_id"
                                        class="form-label">

                                        Student ID

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="student_id"
                                        value="<?= htmlspecialchars($studentId) ?>"
                                        readonly>

                                    <small
                                        class="form-text"
                                        style="color: var(--text-secondary);">

                                        Cannot be changed.

                                    </small>

                                </div>


                                <!-- =================================
                                     DEPARTMENT
                                ================================== -->

                                <div class="col-md-4">

                                    <label
                                        for="department"
                                        class="form-label">

                                        Department

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="department"
                                        value="<?= htmlspecialchars($department) ?>"
                                        readonly>

                                    <small
                                        class="form-text"
                                        style="color: var(--text-secondary);">

                                        Admin only.

                                    </small>

                                </div>


                                <!-- =================================
                                     YEAR & SECTION
                                ================================== -->

                                <div class="col-md-4">

                                    <label
                                        for="year_section"
                                        class="form-label">

                                        Year & Section

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="year_section"
                                        value="<?= htmlspecialchars($yearSection) ?>"
                                        readonly>

                                    <small
                                        class="form-text"
                                        style="color: var(--text-secondary);">

                                        Admin only.

                                    </small>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     ACCOUNT INFORMATION
                ================================================== -->

                <div class="col-12 col-xl-5">

                    <div class="profile-card">


                        <!-- =========================================
                             HEADER
                        ========================================== -->

                        <div class="profile-card-header">

                            <div class="profile-card-icon">

                                <i class="bi bi-person-vcard"></i>

                            </div>

                            <div>

                                <h5>
                                    Account Information
                                </h5>

                                <p>
                                    Login account information
                                </p>

                            </div>

                        </div>


                        <!-- =========================================
                             BODY
                        ========================================== -->

                        <div class="profile-card-body">


                            <!-- =====================================
                                 EMAIL
                            ====================================== -->

                            <div class="mb-3">

                                <label
                                    for="email"
                                    class="form-label">

                                    Email Address

                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="bi bi-envelope"></i>

                                    </span>

                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        value="<?= htmlspecialchars($email) ?>"
                                        readonly>

                                </div>

                                <small
                                    class="form-text"
                                    style="color: var(--text-secondary);">

                                    Email can only be changed by an administrator.

                                </small>

                            </div>


                            <!-- =====================================
                                 USERNAME
                            ====================================== -->

                            <div class="mb-3">

                                <label
                                    for="username"
                                    class="form-label">

                                    Username

                                    <span class="required">
                                        *
                                    </span>

                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="bi bi-person-circle"></i>

                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="username"
                                        name="username"
                                        value="<?= htmlspecialchars($username) ?>"
                                        maxlength="50"
                                        required>

                                </div>

                            </div>


                            <!-- =====================================
                                 ACCOUNT TYPE
                            ====================================== -->

                            <div class="mb-3">

                                <label class="form-label">

                                    Account Type

                                </label>

                                <div class="account-type-box">

                                    <i class="bi bi-mortarboard-fill"></i>

                                    <div>

                                        <strong>
                                            Student
                                        </strong>

                                        <small>
                                            Student account access
                                        </small>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     PASSWORD
                ================================================== -->

                <div class="col-12">

                    <div class="profile-card">


                        <!-- =========================================
                             HEADER
                        ========================================== -->

                        <div class="profile-card-header">

                            <div class="profile-card-icon password-icon">

                                <i class="bi bi-shield-lock"></i>

                            </div>

                            <div>

                                <h5>
                                    Change Password
                                </h5>

                                <p>
                                    Leave these fields blank if you do not want to change your password.
                                </p>

                            </div>

                        </div>


                        <!-- =========================================
                             BODY
                        ========================================== -->

                        <div class="profile-card-body">

                            <div class="row g-3">


                                <!-- =================================
                                     CURRENT PASSWORD
                                ================================== -->

                                <div class="col-md-4">

                                    <label
                                        for="current_password"
                                        class="form-label">

                                        Current Password

                                    </label>

                                    <div class="password-input">

                                        <input
                                            type="password"
                                            class="form-control"
                                            id="current_password"
                                            name="current_password"
                                            autocomplete="current-password">

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            onclick="togglePassword('current_password', this)"
                                            aria-label="Show password">

                                            <i class="bi bi-eye"></i>

                                        </button>

                                    </div>

                                </div>


                                <!-- =================================
                                     NEW PASSWORD
                                ================================== -->

                                <div class="col-md-4">

                                    <label
                                        for="new_password"
                                        class="form-label">

                                        New Password

                                    </label>

                                    <div class="password-input">

                                        <input
                                            type="password"
                                            class="form-control"
                                            id="new_password"
                                            name="new_password"
                                            minlength="8"
                                            autocomplete="new-password">

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            onclick="togglePassword('new_password', this)"
                                            aria-label="Show password">

                                            <i class="bi bi-eye"></i>

                                        </button>

                                    </div>

                                    <small
                                        style="color: var(--text-secondary);">

                                        Minimum 8 characters.

                                    </small>

                                </div>


                                <!-- =================================
                                     CONFIRM PASSWORD
                                ================================== -->

                                <div class="col-md-4">

                                    <label
                                        for="confirm_password"
                                        class="form-label">

                                        Confirm New Password

                                    </label>

                                    <div class="password-input">

                                        <input
                                            type="password"
                                            class="form-control"
                                            id="confirm_password"
                                            name="confirm_password"
                                            minlength="8"
                                            autocomplete="new-password">

                                        <button
                                            type="button"
                                            class="password-toggle"
                                            onclick="togglePassword('confirm_password', this)"
                                            aria-label="Show password">

                                            <i class="bi bi-eye"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>


                            <!-- =====================================
                                 PASSWORD NOTICE
                            ====================================== -->

                            <div class="password-notice mt-3">

                                <i class="bi bi-info-circle-fill"></i>

                                <span>

                                    To change your password, you must provide your current password.
                                    Your new password will be securely encrypted before being stored.

                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     ACTION BUTTONS
                ================================================== -->

                <div class="col-12">

                    <div class="form-actions">

                        <a
                            href="index.php"
                            class="btn btn-secondary">

                            <i class="bi bi-arrow-left me-1"></i>

                            Back to Dashboard

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-check-lg me-1"></i>

                            Save Changes

                        </button>

                    </div>

                </div>

            </div>

        </form>

    </div>

</main>


<!-- =========================================================
     PAGE STYLES
     ========================================================= -->

<style>
    /* =========================================================
   PROFILE CARD
   ========================================================= */

    .profile-card {

        background: var(--surface-color);

        border: 1px solid var(--border-color);

        border-radius: 14px;

        box-shadow:
            0 4px 16px var(--shadow-color);

        overflow: hidden;

    }


    /* =========================================================
   CARD HEADER
   ========================================================= */

    .profile-card-header {

        display: flex;

        align-items: center;

        gap: 14px;

        padding: 20px 22px;

        background: var(--activity-header-bg);

        border-bottom:
            1px solid var(--activity-border-light);

    }

    .profile-card-header h5 {

        margin: 0;

        color: var(--text-color);

        font-weight: 700;

    }

    .profile-card-header p {

        margin: 3px 0 0;

        color: var(--text-secondary);

        font-size: 0.85rem;

    }


    /* =========================================================
   CARD ICON
   ========================================================= */

    .profile-card-icon {

        width: 44px;

        height: 44px;

        min-width: 44px;

        border-radius: 12px;

        display: flex;

        align-items: center;

        justify-content: center;

        background:
            var(--activity-icon-blue-bg);

        color:
            var(--activity-icon-blue);

        font-size: 1.25rem;

    }

    .password-icon {

        background:
            var(--activity-indigo-bg);

        color:
            var(--activity-indigo);

    }


    /* =========================================================
   CARD BODY
   ========================================================= */

    .profile-card-body {

        padding: 22px;

    }


    /* =========================================================
   FORM LABEL
   ========================================================= */

    .form-label {

        color: var(--text-color);

        font-weight: 600;

        margin-bottom: 7px;

    }

    .required {

        color:
            var(--activity-error-warning);

    }


    /* =========================================================
   FORM CONTROL
   ========================================================= */

    .form-control,
    .input-group-text {

        background-color:
            var(--input-bg);

        color:
            var(--text-color);

        border-color:
            var(--border-color);

    }

    .form-control {

        min-height: 44px;

    }

    .form-control:focus {

        background-color:
            var(--input-bg);

        color:
            var(--text-color);

        border-color:
            var(--academic-blue);

        box-shadow:
            0 0 0 0.2rem var(--academic-blue-light);

    }

    .form-control::placeholder {

        color:
            var(--text-secondary);

        opacity: 0.75;

    }

    .input-group-text {

        color:
            var(--text-secondary);

    }


    /* =========================================================
   READONLY FIELDS
   ========================================================= */

    .form-control[readonly] {

        background-color:
            var(--surface-secondary);

        color:
            var(--text-secondary);

        cursor:
            not-allowed;

    }

    .form-control[readonly]:focus {

        border-color:
            var(--border-color);

        box-shadow:
            none;

    }


    /* =========================================================
   ACCOUNT TYPE
   ========================================================= */

    .account-type-box {

        display: flex;

        align-items: center;

        gap: 12px;

        min-height: 50px;

        padding: 9px 12px;

        background:
            var(--activity-icon-blue-bg);

        border:
            1px solid var(--activity-border);

        border-radius: 8px;

    }

    .account-type-box>i {

        font-size: 1.25rem;

        color:
            var(--activity-icon-blue);

    }

    .account-type-box strong {

        display: block;

        color:
            var(--text-color);

        font-size: 0.9rem;

    }

    .account-type-box small {

        display: block;

        color:
            var(--text-secondary);

        font-size: 0.75rem;

    }


    /* =========================================================
   PASSWORD INPUT
   ========================================================= */

    .password-input {

        position: relative;

    }

    .password-input .form-control {

        padding-right: 46px;

    }

    .password-toggle {

        position: absolute;

        top: 50%;

        right: 10px;

        transform:
            translateY(-50%);

        border: 0;

        background: transparent;

        color:
            var(--text-secondary);

        width: 34px;

        height: 34px;

        border-radius: 7px;

        display: flex;

        align-items: center;

        justify-content: center;

        cursor: pointer;

    }

    .password-toggle:hover {

        background:
            var(--activity-hover-bg);

        color:
            var(--academic-blue);

    }


    /* =========================================================
   PASSWORD NOTICE
   ========================================================= */

    .password-notice {

        display: flex;

        align-items: flex-start;

        gap: 9px;

        padding: 12px 14px;

        background:
            var(--activity-icon-blue-bg);

        border:
            1px solid var(--activity-border);

        border-radius: 8px;

        color:
            var(--text-secondary);

        font-size: 0.85rem;

    }

    .password-notice i {

        color:
            var(--activity-icon-blue);

        margin-top: 2px;

    }


    /* =========================================================
   FORM ACTIONS
   ========================================================= */

    .form-actions {

        display: flex;

        justify-content: flex-end;

        align-items: center;

        gap: 10px;

        padding-top: 4px;

    }

    .form-actions .btn {

        min-height: 44px;

        padding: 8px 18px;

    }


    /* =========================================================
   PRIMARY BUTTON
   ========================================================= */

    .form-actions .btn-primary {

        background:
            var(--academic-blue);

        border-color:
            var(--academic-blue);

        color: #fff;

    }

    .form-actions .btn-primary:hover {

        background:
            var(--academic-blue-dark);

        border-color:
            var(--academic-blue-dark);

    }


    /* =========================================================
   SECONDARY BUTTON
   ========================================================= */

    .form-actions .btn-secondary {

        background:
            var(--surface-secondary);

        border-color:
            var(--border-color);

        color:
            var(--text-color);

    }

    .form-actions .btn-secondary:hover {

        background:
            var(--activity-hover-bg);

        border-color:
            var(--border-color);

        color:
            var(--text-color);

    }


    /* =========================================================
   DARK MODE ALERTS
   ========================================================= */

    [data-theme="dark"] .alert-success {

        background:
            var(--activity-icon-green-bg);

        border-color:
            var(--activity-border);

        color:
            var(--activity-completed);

    }

    [data-theme="dark"] .alert-danger {

        background:
            var(--activity-icon-orange-bg);

        border-color:
            var(--activity-border);

        color:
            var(--activity-error-warning);

    }


    /* =========================================================
   MOBILE
   ========================================================= */

    @media (max-width: 767.98px) {

        .profile-card-body {

            padding: 17px;

        }

        .profile-card-header {

            padding: 17px;

        }

        .form-actions {

            flex-direction:
                column-reverse;

            align-items:
                stretch;

        }

        .form-actions .btn {

            width: 100%;

        }

    }
</style>


<!-- =========================================================
     PASSWORD TOGGLE SCRIPT
     ========================================================= -->

<script>
    function togglePassword(inputId, button) {

        const input =
            document.getElementById(inputId);

        const icon =
            button.querySelector("i");


        if (!input || !icon) {

            return;

        }


        if (input.type === "password") {

            input.type = "text";

            icon.classList.remove(
                "bi-eye"
            );

            icon.classList.add(
                "bi-eye-slash"
            );

            button.setAttribute(
                "aria-label",
                "Hide password"
            );

        } else {

            input.type = "password";

            icon.classList.remove(
                "bi-eye-slash"
            );

            icon.classList.add(
                "bi-eye"
            );

            button.setAttribute(
                "aria-label",
                "Show password"
            );

        }

    }
</script>


<?php

/* =========================================================
   GLOBAL SCRIPTS
   ========================================================= */

require_once "./globals/scripts.php";

?>