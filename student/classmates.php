<?php
/* =========================================================
   STUDENT CLASSMATES
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
$department = $user["department"] ?? "";
$yearSection = $user["year_section"] ?? "";


/* =========================================================
   VALIDATE STUDENT INFORMATION
   ========================================================= */

if (
    empty($studentId) ||
    empty($department) ||
    empty($yearSection)
) {
    die("Student class information could not be determined.");
}


/* =========================================================
   LOAD CLASSMATES
   ========================================================= */

$classmates = [];

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
        profile_photo
    FROM accounts
    WHERE access = 'student'
      AND department = ?
      AND year_section = ?
      AND student_id <> ?
    ORDER BY
        last_name ASC,
        first_name ASC
");

if (!$stmt) {
    die("Database error: " . $mysqli->error);
}


$stmt->bind_param(
    "sss",
    $department,
    $yearSection,
    $studentId
);


$stmt->execute();

$result = $stmt->get_result();


while ($row = $result->fetch_assoc()) {

    $classmates[] = $row;
}


$stmt->close();


/* =========================================================
   CLASSMATE COUNT
   ========================================================= */

$classmateCount = count($classmates);


/* =========================================================
   FORMAT FULL NAME
   ========================================================= */

function formatStudentName($student)
{
    $name = "";

    if (!empty($student["first_name"])) {
        $name .= $student["first_name"];
    }

    if (!empty($student["middle_initial"])) {

        if ($name !== "") {
            $name .= " ";
        }

        $name .= $student["middle_initial"] . ".";
    }

    if (!empty($student["last_name"])) {

        if ($name !== "") {
            $name .= " ";
        }

        $name .= $student["last_name"];
    }

    if (!empty($student["extension_name"])) {

        if ($name !== "") {
            $name .= " ";
        }

        $name .= $student["extension_name"];
    }

    return $name;
}


/* =========================================================
   PROFILE PHOTO PATH
   ========================================================= */

/* =========================================================
   PROFILE PHOTO PATH
   ========================================================= */

function getProfilePhoto($photo)
{
    if (empty($photo)) {
        return null;
    }

    /*
     * Normalize path
     */

    $photo = str_replace("\\", "/", trim($photo));


    /*
     * External/shared profile photo directory
     */

    $photoName = basename($photo);


    /*
     * Return path from classmates.php
     * to the shared/uploads/profile_photos folder
     */

    return "../shared/uploads/profile_photos/" .
        rawurlencode($photoName);
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

        <div class="classmates-header">

            <div>

                <div class="page-title-row">

                    <div class="page-title-icon">

                        <i class="bi bi-people-fill"></i>

                    </div>

                    <div>

                        <h2>
                            Classmates
                        </h2>

                        <p>
                            Students in your class
                        </p>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 CLASSMATE COUNT
            ================================================== -->

            <div class="class-count">

                <i class="bi bi-people"></i>

                <span>

                    <?= $classmateCount ?>

                    <?= $classmateCount === 1 ? "Classmate" : "Classmates" ?>

                </span>

            </div>

        </div>


        <!-- =====================================================
             CLASS INFORMATION
        ====================================================== -->

        <div class="class-information">

            <div class="class-information-item">

                <div class="class-information-icon">

                    <i class="bi bi-building"></i>

                </div>

                <div>

                    <span class="class-information-label">
                        Department
                    </span>

                    <strong>
                        <?= htmlspecialchars($department) ?>
                    </strong>

                </div>

            </div>


            <div class="class-information-divider"></div>


            <div class="class-information-item">

                <div class="class-information-icon">

                    <i class="bi bi-mortarboard-fill"></i>

                </div>

                <div>

                    <span class="class-information-label">
                        Year & Section
                    </span>

                    <strong>
                        <?= htmlspecialchars($yearSection) ?>
                    </strong>

                </div>

            </div>

        </div>


        <!-- =====================================================
             CLASSMATES
        ====================================================== -->

        <?php if ($classmateCount > 0): ?>

            <div class="classmates-grid">

                <?php foreach ($classmates as $classmate): ?>

                    <?php

                    $fullName =
                        formatStudentName($classmate);

                    $photo =
                        getProfilePhoto(
                            $classmate["profile_photo"] ?? ""
                        );

                    ?>

                    <!-- =========================================
                         CLASSMATE CARD
                    ========================================== -->

                    <div class="classmate-card">


                        <!-- =====================================
                             PHOTO
                        ====================================== -->

                        <div class="classmate-photo-wrapper">

                            <?php if (!empty($photo)): ?>

                                <img
                                    src="<?= htmlspecialchars($photo) ?>"
                                    alt="<?= htmlspecialchars($fullName) ?>"
                                    class="classmate-photo"
                                    loading="lazy"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                <div
                                    class="classmate-photo-placeholder"
                                    style="display: none;">

                                    <i class="bi bi-person-fill"></i>

                                </div>

                            <?php else: ?>

                                <div class="classmate-photo-placeholder">

                                    <i class="bi bi-person-fill"></i>

                                </div>

                            <?php endif; ?>

                        </div>


                        <!-- =====================================
                             STUDENT INFORMATION
                        ====================================== -->

                        <div class="classmate-information">

                            <h5 class="classmate-name">

                                <?= htmlspecialchars($fullName) ?>

                            </h5>


                            <div class="classmate-student-id">

                                <i class="bi bi-person-badge"></i>

                                <span>

                                    <?= htmlspecialchars(
                                        $classmate["student_id"]
                                    ) ?>

                                </span>

                            </div>


                            <div class="classmate-status">

                                <span class="status-dot"></span>

                                <span>
                                    Classmate
                                </span>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


        <?php else: ?>


            <!-- =================================================
                 NO CLASSMATES
            ================================================== -->

            <div class="empty-classmates">

                <div class="empty-classmates-icon">

                    <i class="bi bi-people"></i>

                </div>

                <h4>
                    No Classmates Found
                </h4>

                <p>

                    There are currently no other students registered
                    in your department and year/section.

                </p>

            </div>


        <?php endif; ?>

    </div>

</main>


<!-- =========================================================
     PAGE STYLES
     ========================================================= -->

<style>
    /* =========================================================
   PAGE HEADER
   ========================================================= */

    .classmates-header {

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 20px;

        margin-bottom: 20px;

    }


    .page-title-row {

        display: flex;

        align-items: center;

        gap: 14px;

    }


    .page-title-icon {

        width: 48px;

        height: 48px;

        min-width: 48px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 12px;

        background:
            var(--activity-icon-blue-bg);

        color:
            var(--activity-icon-blue);

        font-size: 1.35rem;

    }


    .page-title-row h2 {

        margin: 0;

        color:
            var(--text-color);

        font-size: 1.55rem;

        font-weight: 700;

    }


    .page-title-row p {

        margin: 3px 0 0;

        color:
            var(--text-secondary);

        font-size: 0.88rem;

    }


    /* =========================================================
   CLASS COUNT
   ========================================================= */

    .class-count {

        display: flex;

        align-items: center;

        gap: 8px;

        padding: 9px 14px;

        background:
            var(--surface-color);

        border:
            1px solid var(--border-color);

        border-radius: 9px;

        color:
            var(--text-secondary);

        font-size: 0.85rem;

        white-space: nowrap;

    }


    .class-count i {

        color:
            var(--activity-icon-blue);

        font-size: 1rem;

    }


    /* =========================================================
   CLASS INFORMATION
   ========================================================= */

    .class-information {

        display: flex;

        align-items: center;

        gap: 24px;

        padding: 18px 20px;

        margin-bottom: 24px;

        background:
            var(--activity-card-bg);

        border:
            1px solid var(--activity-border);

        border-radius: 12px;

        box-shadow:
            0 3px 12px var(--shadow-color);

    }


    .class-information-item {

        display: flex;

        align-items: center;

        gap: 12px;

        min-width: 0;

    }


    .class-information-icon {

        width: 40px;

        height: 40px;

        min-width: 40px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 10px;

        background:
            var(--activity-icon-blue-bg);

        color:
            var(--activity-icon-blue);

    }


    .class-information-label {

        display: block;

        margin-bottom: 2px;

        color:
            var(--text-secondary);

        font-size: 0.72rem;

        text-transform: uppercase;

        letter-spacing: 0.04em;

        font-weight: 600;

    }


    .class-information-item strong {

        display: block;

        color:
            var(--text-color);

        font-size: 0.9rem;

        font-weight: 600;

    }


    .class-information-divider {

        width: 1px;

        height: 38px;

        background:
            var(--border-color);

    }


    /* =========================================================
   CLASSMATES GRID
   ========================================================= */

    .classmates-grid {

        display: grid;

        grid-template-columns:
            repeat(auto-fill, minmax(220px, 1fr));

        gap: 18px;

    }


    /* =========================================================
   CLASSMATE CARD
   ========================================================= */

    .classmate-card {

        background:
            var(--activity-card-bg);

        border:
            1px solid var(--activity-border);

        border-radius: 14px;

        overflow: hidden;

        box-shadow:
            0 3px 12px var(--shadow-color);

        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease,
            border-color 0.2s ease;

    }


    .classmate-card:hover {

        transform:
            translateY(-3px);

        box-shadow:
            0 8px 20px var(--shadow-color);

        border-color:
            var(--academic-blue);

    }


    /* =========================================================
   PHOTO WRAPPER
   ========================================================= */

    .classmate-photo-wrapper {

        width: 100%;

        height: 235px;

        background:
            var(--surface-secondary);

        display: flex;

        align-items: center;

        justify-content: center;

        overflow: hidden;

        border-bottom:
            1px solid var(--activity-border-light);

    }


    /* =========================================================
   CLASSMATE PHOTO
   ========================================================= */

    .classmate-photo {

        width: 100%;

        height: 100%;

        object-fit: cover;

        display: block;

    }


    /* =========================================================
   PHOTO PLACEHOLDER
   ========================================================= */

    .classmate-photo-placeholder {

        width: 100%;

        height: 100%;

        display: flex;

        align-items: center;

        justify-content: center;

        background:
            var(--activity-icon-gray-bg);

        color:
            var(--activity-icon-gray);

        font-size: 4rem;

    }


    /* =========================================================
   CLASSMATE INFORMATION
   ========================================================= */

    .classmate-information {

        padding: 16px;

    }


    .classmate-name {

        margin: 0 0 9px;

        color:
            var(--text-color);

        font-size: 0.98rem;

        font-weight: 700;

        line-height: 1.35;

        word-break: break-word;

    }


    /* =========================================================
   STUDENT ID
   ========================================================= */

    .classmate-student-id {

        display: flex;

        align-items: center;

        gap: 7px;

        color:
            var(--text-secondary);

        font-size: 0.78rem;

    }


    .classmate-student-id i {

        color:
            var(--activity-icon-blue);

        font-size: 0.85rem;

    }


    /* =========================================================
   STATUS
   ========================================================= */

    .classmate-status {

        display: flex;

        align-items: center;

        gap: 6px;

        margin-top: 11px;

        padding-top: 10px;

        border-top:
            1px solid var(--activity-border-light);

        color:
            var(--text-secondary);

        font-size: 0.72rem;

    }


    .status-dot {

        width: 7px;

        height: 7px;

        border-radius: 50%;

        background:
            var(--activity-completed);

    }


    /* =========================================================
   EMPTY STATE
   ========================================================= */

    .empty-classmates {

        padding: 60px 25px;

        text-align: center;

        background:
            var(--activity-card-bg);

        border:
            1px solid var(--activity-border);

        border-radius: 14px;

        box-shadow:
            0 3px 12px var(--shadow-color);

    }


    .empty-classmates-icon {

        width: 70px;

        height: 70px;

        margin: 0 auto 18px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        background:
            var(--activity-icon-gray-bg);

        color:
            var(--activity-icon-gray);

        font-size: 2rem;

    }


    .empty-classmates h4 {

        margin: 0 0 8px;

        color:
            var(--text-color);

        font-weight: 700;

    }


    .empty-classmates p {

        max-width: 500px;

        margin: 0 auto;

        color:
            var(--text-secondary);

        font-size: 0.88rem;

        line-height: 1.6;

    }


    /* =========================================================
   MOBILE
   ========================================================= */

    @media (max-width: 767.98px) {

        .classmates-header {

            align-items: flex-start;

            flex-direction: column;

        }


        .class-count {

            width: 100%;

            justify-content: center;

        }


        .class-information {

            flex-direction: column;

            align-items: stretch;

            gap: 15px;

        }


        .class-information-divider {

            width: 100%;

            height: 1px;

        }


        .classmates-grid {

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap: 12px;

        }


        .classmate-photo-wrapper {

            height: 190px;

        }


        .classmate-information {

            padding: 13px;

        }


        .classmate-name {

            font-size: 0.88rem;

        }

    }


    @media (max-width: 450px) {

        .classmates-grid {

            grid-template-columns:
                1fr;

        }


        .classmate-photo-wrapper {

            height: 260px;

        }

    }
</style>


<?php

/* =========================================================
   GLOBAL SCRIPTS
   ========================================================= */

require_once "./globals/scripts.php";

?>