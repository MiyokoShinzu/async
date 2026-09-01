
<?php

/* =========================================================
   STUDENT DASHBOARD
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
   DATABASE
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   USER SESSION
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

$studentId =
    $user["student_id"] ?? "";

$department =
    $user["department"] ?? "";

$yearSection =
    $user["year_section"] ?? "";

$email =
    $user["email"] ?? "";

$username =
    $user["username"] ?? "";

$access =
    $user["access"] ?? "student";


/* =========================================================
   GET PROFILE PHOTO
   ========================================================= */

$profilePhoto = "";

if ($studentId !== "") {

    $photoSQL = "
        SELECT profile_photo
        FROM accounts
        WHERE student_id = ?
        LIMIT 1
    ";

    $photoStmt =
        $mysqli->prepare($photoSQL);

    if ($photoStmt) {

        $photoStmt->bind_param(
            "s",
            $studentId
        );

        $photoStmt->execute();

        $photoResult =
            $photoStmt->get_result();

        if ($photoRow = $photoResult->fetch_assoc()) {

            $profilePhoto =
                trim(
                    $photoRow["profile_photo"] ?? ""
                );
        }

        $photoStmt->close();
    }
}


/* =========================================================
   FULL NAME
   ========================================================= */

$fullName = trim(
    $firstName . " " .
        (
            $middleInitial !== ""
            ? $middleInitial . ". "
            : ""
        ) .
        $lastName .
        (
            $extensionName !== ""
            ? " " . $extensionName
            : ""
        )
);


/* =========================================================
   INITIALS
   ========================================================= */

$initials = "";

if ($firstName !== "") {

    $initials .=
        strtoupper(
            substr(
                $firstName,
                0,
                1
            )
        );
}

if ($lastName !== "") {

    $initials .=
        strtoupper(
            substr(
                $lastName,
                0,
                1
            )
        );
}


/* =========================================================
   PROFILE PHOTO URL
   ========================================================= */

/*
   Your database stores:

   uploads/profile_photos/filename.jpg

   Since index.php is inside:

   /async/student/

   the correct browser path is:

   /async/student/uploads/profile_photos/filename.jpg
*/

$profilePhotoUrl = "";

if ($profilePhoto !== "") {

    $profilePhotoUrl =
        "./" .
        ltrim(
            $profilePhoto,
            "/"
        );
}

?>


<!DOCTYPE html>

<html lang="en">


<?php include "globals/head.php"; ?>


<body>


    <!-- =========================================================
         SIDEBAR
    ========================================================== -->

    <?php include "globals/sidebar.php"; ?>


    <!-- =========================================================
         MOBILE OVERLAY
    ========================================================== -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay">
    </div>


    <!-- =========================================================
         TOPBAR
    ========================================================== -->

    <?php include "globals/topbar.php"; ?>


    <!-- =========================================================
         MAIN CONTENT
    ========================================================== -->

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

                    Welcome to your ETS-Async Learning Portal.
                    Manage your academic information and learning
                    activities from your dashboard.

                </p>

            </div>


            <!-- =================================================
                 QUICK INFORMATION
            ================================================== -->

            <div class="row g-3 mb-4">


                <!-- STUDENT ID -->

                <div class="col-sm-6 col-xl-3">

                    <div class="dashboard-card">

                        <div class="dashboard-card-body">

                            <div class="stat-icon">

                                <i class="bi bi-person-vcard"></i>

                            </div>

                            <div class="stat-label">

                                Student ID

                            </div>

                            <div class="stat-value">

                                <?= htmlspecialchars($studentId) ?>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- YEAR & SECTION -->

                <div class="col-sm-6 col-xl-3">

                    <div class="dashboard-card">

                        <div class="dashboard-card-body">

                            <div class="stat-icon">

                                <i class="bi bi-people"></i>

                            </div>

                            <div class="stat-label">

                                Year & Section

                            </div>

                            <div class="stat-value">

                                <?= htmlspecialchars($yearSection) ?>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- DEPARTMENT -->

                <div class="col-sm-6 col-xl-3">

                    <div class="dashboard-card">

                        <div class="dashboard-card-body">

                            <div class="stat-icon">

                                <i class="bi bi-building"></i>

                            </div>

                            <div class="stat-label">

                                Department

                            </div>

                            <div class="stat-value">

                                <?= htmlspecialchars($department) ?>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ACCOUNT -->

                <div class="col-sm-6 col-xl-3">

                    <div class="dashboard-card">

                        <div class="dashboard-card-body">

                            <div class="stat-icon">

                                <i class="bi bi-person-check"></i>

                            </div>

                            <div class="stat-label">

                                Account

                            </div>

                            <div class="stat-value">

                                <?= htmlspecialchars(
                                    ucfirst($access)
                                ) ?>

                            </div>

                        </div>

                    </div>

                </div>


            </div>


            <!-- =================================================
                 INFORMATION
            ================================================== -->

            <div class="row g-4">


                <!-- STUDENT INFORMATION -->

                <div class="col-lg-8">

                    <div class="dashboard-card">

                        <div class="dashboard-card-body">


                            <div class="section-title">

                                <i class="bi bi-person-lines-fill me-2"></i>

                                Student Information

                            </div>


                            <div class="info-row">

                                <div class="info-label">

                                    Full Name

                                </div>

                                <div class="info-value">

                                    <?= htmlspecialchars($fullName) ?>

                                </div>

                            </div>


                            <div class="info-row">

                                <div class="info-label">

                                    Student ID

                                </div>

                                <div class="info-value">

                                    <?= htmlspecialchars($studentId) ?>

                                </div>

                            </div>


                            <div class="info-row">

                                <div class="info-label">

                                    Department

                                </div>

                                <div class="info-value">

                                    <?= htmlspecialchars($department) ?>

                                </div>

                            </div>


                            <div class="info-row">

                                <div class="info-label">

                                    Year & Section

                                </div>

                                <div class="info-value">

                                    <?= htmlspecialchars($yearSection) ?>

                                </div>

                            </div>


                            <div class="info-row">

                                <div class="info-label">

                                    Email

                                </div>

                                <div class="info-value">

                                    <?= htmlspecialchars($email) ?>

                                </div>

                            </div>


                            <div class="info-row">

                                <div class="info-label">

                                    Username

                                </div>

                                <div class="info-value">

                                    <?= htmlspecialchars($username) ?>

                                </div>

                            </div>


                        </div>

                    </div>

                </div>


                <!-- ACCOUNT INFORMATION -->

                <div class="col-lg-4">

                    <div class="dashboard-card">

                        <div class="dashboard-card-body">


                            <div class="section-title">

                                <i class="bi bi-shield-check me-2"></i>

                                Account

                            </div>


                            <div class="info-row">

                                <div class="info-label">

                                    Account Type

                                </div>

                                <div>

                                    <span
                                        class="badge text-bg-primary">

                                        <?= htmlspecialchars(
                                            ucfirst($access)
                                        ) ?>

                                    </span>

                                </div>

                            </div>


                            <div class="info-row">

                                <div class="info-label">

                                    Username

                                </div>

                                <div class="info-value">

                                    <?= htmlspecialchars($username) ?>

                                </div>

                            </div>


                            <div class="info-row">

                                <div class="info-label">

                                    Status

                                </div>

                                <div>

                                    <span
                                        class="badge text-bg-success">

                                        Active

                                    </span>

                                </div>

                            </div>


                        </div>

                    </div>

                </div>


            </div>


        </div>

    </main>


    <!-- =========================================================
         GLOBAL SCRIPTS
    ========================================================== -->

    <?php require_once "./globals/scripts.php"; ?>


</body>

</html>
