
<?php

/* =========================================================
   ADMIN DASHBOARD
   ETS-Async Learning Portal
   ========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION CHECK
   ========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "admin"
) {

    header("Location: ../login.php");

    exit;
}


/* =========================================================
   GET USER SESSION
   ========================================================= */

$user = $_SESSION["user"];


/* =========================================================
   USER DATA
   ========================================================= */

$firstName =
    $user["first_name"] ?? "";

$lastName =
    $user["last_name"] ?? "";

$middleInitial =
    $user["middle_initial"] ?? "";

$extensionName =
    $user["extension_name"] ?? "";

$email =
    $user["email"] ?? "";

$username =
    $user["username"] ?? "";

$access =
    $user["access"] ?? "admin";


/* =========================================================
   BUILD FULL NAME
   ========================================================= */

$fullName = trim(

    $firstName . " " .

        ($middleInitial !== ""
            ? $middleInitial . ". "
            : "") .

        $lastName .

        ($extensionName !== ""
            ? " " . $extensionName
            : "")

);


/* =========================================================
   INITIALS
   ========================================================= */

$initials = "";

if ($firstName !== "") {

    $initials .= strtoupper(
        substr($firstName, 0, 1)
    );
}

if ($lastName !== "") {

    $initials .= strtoupper(
        substr($lastName, 0, 1)
    );
}

?>


<!DOCTYPE html>

<html lang="en">
    <?php include 'globals/head.php'; ?>
<body>


    <!-- =========================================================
     SIDEBAR
========================================================= -->

    <?php include 'globals/sidebar.php'; ?>


    <!-- =========================================================
     TOPBAR
========================================================= -->

   <?php include 'globals/topbar.php'; ?>


    <!-- =========================================================
     MAIN CONTENT
========================================================= -->

    <main class="main-content">


        <div class="content-wrapper">


            <!-- =================================================
             WELCOME
        ================================================== -->

            <div class="welcome-card">


                <h2>

                    Welcome,
                    <?= htmlspecialchars($firstName) ?>!

                </h2>


                <p>

                    Welcome to the ETS-Async
                    Administration Portal.
                    Manage students, accounts,
                    activities, grades, schedules,
                    and academic resources.

                </p>


            </div>


            <!-- =================================================
             STATISTICS
        ================================================== -->

            <div class="row g-3 mb-4">


                <!-- STUDENTS -->

                <div class="col-sm-6 col-xl-3">

                    <div class="stat-card">

                        <div class="stat-icon">

                            <i class="bi bi-people-fill"></i>

                        </div>


                        <div class="stat-label">

                            Total Students

                        </div>


                        <div class="stat-value">

                            0

                        </div>

                    </div>

                </div>


                <!-- ACCOUNTS -->

                <div class="col-sm-6 col-xl-3">

                    <div class="stat-card">

                        <div class="stat-icon">

                            <i class="bi bi-person-vcard-fill"></i>

                        </div>


                        <div class="stat-label">

                            Total Accounts

                        </div>


                        <div class="stat-value">

                            0

                        </div>

                    </div>

                </div>


                <!-- ACTIVITIES -->

                <div class="col-sm-6 col-xl-3">

                    <div class="stat-card">

                        <div class="stat-icon">

                            <i class="bi bi-journal-text"></i>

                        </div>


                        <div class="stat-label">

                            Activities

                        </div>


                        <div class="stat-value">

                            0

                        </div>

                    </div>

                </div>


                <!-- REPORTS -->

                <div class="col-sm-6 col-xl-3">

                    <div class="stat-card">

                        <div class="stat-icon">

                            <i class="bi bi-file-earmark-bar-graph"></i>

                        </div>


                        <div class="stat-label">

                            Reports

                        </div>


                        <div class="stat-value">

                            0

                        </div>

                    </div>

                </div>


            </div>


            <!-- =================================================
             LOWER SECTION
        ================================================== -->

            <div class="row g-4">


                <!-- QUICK ACTIONS -->

                <div class="col-lg-7">


                    <div class="dashboard-card">


                        <div class="dashboard-card-header">

                            <i class="bi bi-lightning-charge me-2"></i>

                            Quick Actions

                        </div>


                        <div class="dashboard-card-body">


                            <!-- MANAGE STUDENTS -->

                            <a
                                href="#"
                                class="quick-action">


                                <div class="quick-action-icon">

                                    <i class="bi bi-person-plus"></i>

                                </div>


                                <div>

                                    <div class="quick-action-title">

                                        Manage Students

                                    </div>


                                    <div class="quick-action-text">

                                        View and manage student records.

                                    </div>

                                </div>


                            </a>


                            <!-- MANAGE ACCOUNTS -->

                            <a
                                href="#"
                                class="quick-action">


                                <div class="quick-action-icon">

                                    <i class="bi bi-person-vcard"></i>

                                </div>


                                <div>

                                    <div class="quick-action-title">

                                        Manage Accounts

                                    </div>


                                    <div class="quick-action-text">

                                        Manage student and administrator accounts.

                                    </div>

                                </div>


                            </a>


                            <!-- MANAGE ACTIVITIES -->

                            <a
                                href="#"
                                class="quick-action">


                                <div class="quick-action-icon">

                                    <i class="bi bi-journal-plus"></i>

                                </div>


                                <div>

                                    <div class="quick-action-title">

                                        Manage Activities

                                    </div>


                                    <div class="quick-action-text">

                                        Create and manage learning activities.

                                    </div>

                                </div>


                            </a>


                            <!-- REPORTS -->

                            <a
                                href="#"
                                class="quick-action">


                                <div class="quick-action-icon">

                                    <i class="bi bi-file-earmark-bar-graph"></i>

                                </div>


                                <div>

                                    <div class="quick-action-title">

                                        Generate Reports

                                    </div>


                                    <div class="quick-action-text">

                                        View academic and system reports.

                                    </div>

                                </div>


                            </a>


                        </div>

                    </div>

                </div>


                <!-- ADMIN INFORMATION -->

                <div class="col-lg-5">


                    <div class="dashboard-card">


                        <div class="dashboard-card-header">

                            <i class="bi bi-shield-check me-2"></i>

                            Administrator Information

                        </div>


                        <div class="dashboard-card-body">


                            <!-- FULL NAME -->

                            <div class="information-item">

                                <div class="information-label">

                                    Full Name

                                </div>


                                <div class="information-value">

                                    <?= htmlspecialchars($fullName) ?>

                                </div>

                            </div>


                            <!-- USERNAME -->

                            <div class="information-item">

                                <div class="information-label">

                                    Username

                                </div>


                                <div class="information-value">

                                    <?= htmlspecialchars($username) ?>

                                </div>

                            </div>


                            <!-- EMAIL -->

                            <div class="information-item">

                                <div class="information-label">

                                    Email Address

                                </div>


                                <div class="information-value">

                                    <?= htmlspecialchars($email) ?>

                                </div>

                            </div>


                            <!-- ACCOUNT TYPE -->

                            <div class="d-flex justify-content-between align-items-center">

                                <span class="text-secondary">

                                    Account Type

                                </span>


                                <span class="badge text-bg-primary">

                                    <?= htmlspecialchars(
                                        ucfirst($access)
                                    ) ?>

                                </span>

                            </div>


                        </div>

                    </div>

                </div>


            </div>


        </div>


    </main>


    <!-- =========================================================
     JAVASCRIPT
========================================================= -->

    <?php include 'globals/scripts.php'; ?>
</body>

</html>
