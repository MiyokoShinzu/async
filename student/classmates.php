
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

        $name .= rtrim($student["middle_initial"], ".") . ".";
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
   GET STUDENT INITIALS
   ========================================================= */

function getStudentInitials($student)
{
    $firstName = trim($student["first_name"] ?? "");
    $lastName  = trim($student["last_name"] ?? "");

    $initials = "";

    if ($firstName !== "") {
        $initials .= strtoupper(substr($firstName, 0, 1));
    }

    if ($lastName !== "") {
        $initials .= strtoupper(substr($lastName, 0, 1));
    }

    return $initials !== "" ? $initials : "?";
}

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
     * Get only the filename
     */

    $photoName = basename($photo);


    /*
     * Profile photos are stored on the main
     * vertigation.com domain.
     */

    return "https://vertigation.com/shared/uploads/profile_photos/" .
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

            <div class="page-title-row">

                <div class="page-title-icon">

                    <i class="bi bi-people-fill"></i>

                </div>

                <div>

                    <h2>
                        Classmates
                    </h2>

                    <p>
                        Your class seating view
                    </p>

                </div>

            </div>


            <!-- =================================================
                 CLASS COUNT
            ================================================== -->

            <div class="class-count">

                <i class="bi bi-people-fill"></i>

                <span>

                    <?= $classmateCount ?>

                    <?= $classmateCount === 1
                        ? "Classmate"
                        : "Classmates"
                    ?>

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
             CLASSROOM AREA
        ====================================================== -->

        <div class="classroom-container">


            <!-- =================================================
                 CLASSROOM HEADER
            ================================================== -->

            <div class="classroom-header">

                <div class="classroom-title">

                    <i class="bi bi-easel2-fill"></i>

                    <span>
                        Classroom
                    </span>

                </div>

                <div class="classroom-hint">

                    <i class="bi bi-cursor-fill"></i>

                    Hover over a classmate to view details

                </div>

            </div>


            <!-- =================================================
                 TEACHER AREA
            ================================================== -->

            <div class="teacher-area">

                <div class="teacher-desk">

                    <i class="bi bi-person-workspace"></i>

                    <span>
                        Instructor
                    </span>

                </div>

            </div>


            <!-- =================================================
                 CLASSMATE SEATS
            ================================================== -->

            <?php if ($classmateCount > 0): ?>

                <div class="seating-area">

                    <?php foreach ($classmates as $index => $classmate): ?>

                        <?php

                        $fullName =
                            formatStudentName($classmate);

                        $photo =
                            getProfilePhoto(
                                $classmate["profile_photo"] ?? ""
                            );

                        ?>


                        <!-- =====================================
                             STUDENT SEAT
                        ====================================== -->

                        <div class="student-seat">


                            <!-- =================================
                                 SEAT
                            ================================== -->

                            <div class="seat">


                                <!-- =============================
                                     PROFILE PHOTO
                                ============================== -->

                                <div class="seat-photo-container">

                                    <?php if (!empty($photo)): ?>

                                        <img
                                            src="<?= htmlspecialchars($photo) ?>"
                                            alt="<?= htmlspecialchars($fullName) ?>"
                                            class="seat-photo"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                        <div
                                            class="seat-photo-placeholder"
                                            style="display:none;">

                                            <i class="bi bi-person-fill"></i>

                                        </div>

                                    <?php else: ?>

                                        <div class="seat-photo-placeholder">

                                            <i class="bi bi-person-fill"></i>

                                        </div>

                                    <?php endif; ?>

                                </div>


                                <!-- =============================
                                     HOVER DETAILS
                                ============================== -->

                                <div class="seat-details">

                                    <div class="seat-details-name">

                                        <?= htmlspecialchars($fullName) ?>

                                    </div>


                                    <div class="seat-details-id">

                                        <i class="bi bi-person-badge"></i>

                                        <?= htmlspecialchars(
                                            $classmate["student_id"]
                                        ) ?>

                                    </div>

                                </div>

                            </div>


                            <!-- =================================
                                 SEAT BASE
                            ================================== -->

                            <div class="seat-base">

                                <span></span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>


            <?php else: ?>


                <!-- =================================================
                     EMPTY CLASSROOM
                ================================================== -->

                <div class="empty-classroom">

                    <div class="empty-classroom-icon">

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
   CLASSROOM CONTAINER
   ========================================================= */

    .classroom-container {

        position: relative;

        background:
            var(--activity-card-bg);

        border:
            1px solid var(--activity-border);

        border-radius: 16px;

        padding: 24px;

        box-shadow:
            0 4px 16px var(--shadow-color);

        overflow: visible;

    }


    /* =========================================================
   CLASSROOM HEADER
   ========================================================= */

    .classroom-header {

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 15px;

        padding-bottom: 18px;

        border-bottom:
            1px solid var(--activity-border-light);

    }


    .classroom-title {

        display: flex;

        align-items: center;

        gap: 9px;

        color:
            var(--text-color);

        font-size: 0.95rem;

        font-weight: 700;

    }


    .classroom-title i {

        color:
            var(--activity-icon-blue);

    }


    .classroom-hint {

        display: flex;

        align-items: center;

        gap: 6px;

        color:
            var(--text-secondary);

        font-size: 0.75rem;

    }


    .classroom-hint i {

        color:
            var(--activity-icon-blue);

        font-size: 0.7rem;

    }


    /* =========================================================
   TEACHER AREA
   ========================================================= */

    .teacher-area {

        display: flex;

        justify-content: center;

        padding: 22px 0 35px;

    }


    .teacher-desk {

        display: flex;

        align-items: center;

        justify-content: center;

        gap: 8px;

        min-width: 160px;

        padding: 10px 22px;

        border-radius: 8px;

        background:
            var(--activity-icon-blue-bg);

        border:
            1px solid var(--activity-border);

        color:
            var(--activity-icon-blue);

        font-size: 0.78rem;

        font-weight: 600;

    }


    .teacher-desk i {

        font-size: 1rem;

    }


    /* =========================================================
   SEATING AREA
   ========================================================= */

    .seating-area {

        display: flex;

        flex-wrap: wrap;

        justify-content: center;

        align-items: flex-start;

        column-gap: 32px;

        row-gap: 55px;

        padding: 20px 15px 30px;

    }


    /* =========================================================
   STUDENT SEAT
   ========================================================= */

    .student-seat {

        position: relative;

        width: 125px;

        height: 145px;

        display: flex;

        flex-direction: column;

        align-items: center;

    }


    /* =========================================================
   SEAT
   ========================================================= */

    .seat {

        position: relative;

        width: 100px;

        height: 100px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        background:
            var(--surface-secondary);

        border:
            5px solid var(--activity-border);

        box-shadow:
            0 4px 10px var(--shadow-color);

        cursor: pointer;

        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            border-color 0.25s ease;

        z-index: 2;

    }


    /* =========================================================
   PROFILE PHOTO CONTAINER
   ========================================================= */

    .seat-photo-container {

        width: 90px;

        height: 90px;

        border-radius: 50%;

        overflow: hidden;

        display: flex;

        align-items: center;

        justify-content: center;

        background:
            var(--activity-icon-gray-bg);

    }


    /* =========================================================
   PROFILE PHOTO
   ========================================================= */

    .seat-photo {

        width: 100%;

        height: 100%;

        object-fit: cover;

        display: block;

    }


    /* =========================================================
   PHOTO PLACEHOLDER
   ========================================================= */

    .seat-photo-placeholder {

        width: 100%;

        height: 100%;

        display: flex;

        align-items: center;

        justify-content: center;

        color:
            var(--activity-icon-gray);

        background:
            var(--activity-icon-gray-bg);

        font-size: 2.4rem;

    }


    /* =========================================================
   HOVER EFFECT
   ========================================================= */

    .seat:hover {

        transform:
            scale(1.55);

        border-color:
            var(--academic-blue);

        box-shadow:
            0 12px 30px var(--shadow-color);

        z-index: 100;

    }


    /* =========================================================
   HOVER DETAILS
   ========================================================= */

    .seat-details {

        position: absolute;

        left: 50%;

        top: calc(100% + 10px);

        transform:
            translateX(-50%) translateY(-5px);

        min-width: 180px;

        max-width: 240px;

        padding: 11px 13px;

        background:
            var(--surface-color);

        border:
            1px solid var(--border-color);

        border-radius: 9px;

        box-shadow:
            0 8px 25px var(--shadow-color);

        opacity: 0;

        visibility: hidden;

        pointer-events: none;

        transition:
            opacity 0.2s ease,
            transform 0.2s ease;

        text-align: center;

    }


    /* =========================================================
   SHOW DETAILS
   ========================================================= */

    .seat:hover .seat-details {

        opacity: 1;

        visibility: visible;

        transform:
            translateX(-50%) translateY(0);

    }


    /* =========================================================
   STUDENT NAME
   ========================================================= */

    .seat-details-name {

        color:
            var(--text-color);

        font-size: 0.8rem;

        font-weight: 700;

        line-height: 1.35;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;

    }


    /* =========================================================
   STUDENT ID
   ========================================================= */

    .seat-details-id {

        display: flex;

        justify-content: center;

        align-items: center;

        gap: 5px;

        margin-top: 5px;

        color:
            var(--text-secondary);

        font-size: 0.7rem;

    }


    .seat-details-id i {

        color:
            var(--activity-icon-blue);

    }


    /* =========================================================
   SEAT BASE
   ========================================================= */

    .seat-base {

        width: 72px;

        height: 12px;

        margin-top: 8px;

        border-radius: 0 0 7px 7px;

        background:
            var(--activity-border);

        position: relative;

    }


    .seat-base::before {

        content: "";

        position: absolute;

        left: 50%;

        top: 100%;

        transform:
            translateX(-50%);

        width: 8px;

        height: 12px;

        background:
            var(--activity-border);

    }


    .seat-base span {

        display: block;

        width: 82px;

        height: 4px;

        position: absolute;

        bottom: -12px;

        left: 50%;

        transform:
            translateX(-50%);

        border-radius: 50%;

        background:
            var(--activity-border);

    }


    /* =========================================================
   EMPTY CLASSROOM
   ========================================================= */

    .empty-classroom {

        padding: 60px 25px;

        text-align: center;

    }


    .empty-classroom-icon {

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


    .empty-classroom h4 {

        margin: 0 0 8px;

        color:
            var(--text-color);

        font-weight: 700;

    }


    .empty-classroom p {

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


        .classroom-container {

            padding: 16px 10px;

        }


        .classroom-header {

            align-items: flex-start;

            flex-direction: column;

        }


        .classroom-hint {

            font-size: 0.7rem;

        }


        .seating-area {

            column-gap: 18px;

            row-gap: 50px;

            padding-left: 5px;

            padding-right: 5px;

        }


        .student-seat {

            width: 105px;

        }


        .seat {

            width: 88px;

            height: 88px;

        }


        .seat-photo-container {

            width: 78px;

            height: 78px;

        }


        .seat-photo-placeholder {

            font-size: 2rem;

        }


        .seat:hover {

            transform:
                scale(1.35);

        }


        .seat-details {

            min-width: 155px;

        }

    }


    @media (max-width: 450px) {

        .seating-area {

            column-gap: 8px;

            row-gap: 45px;

        }


        .student-seat {

            width: 92px;

        }


        .seat {

            width: 78px;

            height: 78px;

            border-width: 4px;

        }


        .seat-photo-container {

            width: 70px;

            height: 70px;

        }


        .seat:hover {

            transform:
                scale(1.25);

        }


        .seat-details {

            min-width: 145px;

            padding: 9px 10px;

        }


        .seat-details-name {

            font-size: 0.72rem;

        }

    }
</style>


<?php

/* =========================================================
   GLOBAL SCRIPTS
   ========================================================= */

require_once "./globals/scripts.php";

?>
