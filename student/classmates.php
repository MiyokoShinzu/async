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
   CURRENT STUDENT INFORMATION
========================================================= */

$user = $_SESSION["user"];

$studentId = $user["student_id"] ?? "";
$department = $user["department"] ?? "";
$yearSection = $user["year_section"] ?? "";


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


$classmateCount = count($classmates);


/* =========================================================
   FORMAT STUDENT NAME
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

        $name .= rtrim(
            $student["middle_initial"],
            "."
        ) . ".";
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
    $firstName = trim(
        $student["first_name"] ?? ""
    );

    $lastName = trim(
        $student["last_name"] ?? ""
    );

    $initials = "";


    /* -----------------------------------------------------
       FIRST NAME INITIAL
    ----------------------------------------------------- */

    if ($firstName !== "") {

        $firstCharacters = preg_split(
            '/\s+/u',
            $firstName
        );

        if (!empty($firstCharacters[0])) {

            $initials .= strtoupper(
                mb_substr(
                    $firstCharacters[0],
                    0,
                    1,
                    "UTF-8"
                )
            );
        }
    }


    /* -----------------------------------------------------
       LAST NAME INITIAL
    ----------------------------------------------------- */

    if ($lastName !== "") {

        $initials .= strtoupper(
            mb_substr(
                $lastName,
                0,
                1,
                "UTF-8"
            )
        );
    }


    /* -----------------------------------------------------
       FALLBACK
    ----------------------------------------------------- */

    if ($initials === "") {
        $initials = "?";
    }


    return $initials;
}


/* =========================================================
   GET PROFILE PHOTO
========================================================= */

function getProfilePhoto($photo)
{
    if (empty($photo)) {
        return null;
    }


    /*
     * Normalize slashes.
     */

    $photo = str_replace(
        "\\",
        "/",
        trim($photo)
    );


    /*
     * Extract filename only.
     *
     * This allows database values such as:
     *
     * uploads/profile_photos/example.png
     *
     * or
     *
     * shared/uploads/profile_photos/example.png
     *
     * to work using the main domain.
     */

    $photoName = basename($photo);


    if ($photoName === "" || $photoName === ".") {
        return null;
    }


    /*
     * Profile photos are stored on the
     * main vertigation.com domain.
     */

    return "https://vertigation.com/shared/uploads/profile_photos/" .
        rawurlencode($photoName);
}


/* =========================================================
   GLOBAL PAGE COMPONENTS
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


        <!-- =================================================
             PAGE HEADER
        ================================================== -->

        <div class="page-header">

            <div>

                <h1 class="page-title">
                    <i class="bi bi-people-fill"></i>
                    Classmates
                </h1>

                <p class="page-subtitle">
                    View the students in your class.
                </p>

            </div>


            <div class="class-info">

                <div class="class-info-item">

                    <i class="bi bi-mortarboard-fill"></i>

                    <span>
                        <?= htmlspecialchars($department) ?>
                    </span>

                </div>


                <div class="class-info-divider"></div>


                <div class="class-info-item">

                    <i class="bi bi-people-fill"></i>

                    <span>
                        <?= htmlspecialchars($yearSection) ?>
                    </span>

                </div>

            </div>

        </div>


        <!-- =================================================
             CLASSROOM CONTAINER
        ================================================== -->

        <div class="classroom-container">


            <!-- =============================================
                 CLASSROOM HEADER
            ============================================== -->

            <div class="classroom-header">

                <div class="classroom-title">

                    <div class="classroom-title-icon">
                        <i class="bi bi-building"></i>
                    </div>

                    <div>

                        <h2>
                            Classroom
                        </h2>

                        <p>
                            <?= $classmateCount ?>
                            <?= $classmateCount === 1
                                ? "classmate"
                                : "classmates"
                            ?>
                        </p>

                    </div>

                </div>


                <div class="classroom-hint">

                    <i class="bi bi-mouse"></i>

                    <span>
                        Hover over a student to view details
                    </span>

                </div>

            </div>


            <!-- =============================================
                 TEACHER AREA
            ============================================== -->

            <div class="teacher-area">

                <div class="teacher-desk">

                    <div class="teacher-icon">
                        <i class="bi bi-person-workspace"></i>
                    </div>

                    <div class="teacher-label">
                        Instructor
                    </div>

                </div>

            </div>


            <!-- =============================================
                 CLASSMATE SEATING AREA
            ============================================== -->

            <?php if (!empty($classmates)): ?>

                <div class="seating-area">

                    <?php foreach (
                        $classmates
                        as $index => $classmate
                    ): ?>

                        <?php

                        $fullName =
                            formatStudentName(
                                $classmate
                            );

                        $initials =
                            getStudentInitials(
                                $classmate
                            );

                        $photo =
                            getProfilePhoto(
                                $classmate["profile_photo"] ?? ""
                            );

                        ?>

                        <!-- =================================
                             STUDENT SEAT
                        ================================== -->

                        <div
                            class="student-seat"
                            style="--seat-delay: <?= ($index * 0.04) ?>s;">


                            <!-- =================================
                                 SEAT
                            ================================== -->

                            <div class="seat">


                                <!-- =============================
                                     PROFILE PHOTO / INITIALS
                                ============================== -->

                                <div class="seat-photo-container">


                                    <?php if (!empty($photo)): ?>

                                        <img
                                            src="<?= htmlspecialchars($photo) ?>"
                                            alt="<?= htmlspecialchars($fullName) ?>"
                                            class="seat-photo"
                                            loading="lazy"
                                            onerror="
                                                this.style.display='none';
                                                this.nextElementSibling.style.display='flex';
                                            ">


                                        <!-- =========================
                                             INITIALS FALLBACK
                                        ========================== -->

                                        <div
                                            class="seat-photo-placeholder"
                                            style="display:none;">
                                            <?= htmlspecialchars($initials) ?>
                                        </div>


                                    <?php else: ?>


                                        <!-- =========================
                                             INITIALS
                                        ========================== -->

                                        <div class="seat-photo-placeholder">

                                            <?= htmlspecialchars($initials) ?>

                                        </div>


                                    <?php endif; ?>


                                </div>


                                <!-- =================================
                                     STUDENT DETAILS
                                ================================== -->

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
                                 CHAIR BASE
                            ================================== -->

                            <div class="seat-base">

                                <span></span>

                            </div>


                        </div>

                    <?php endforeach; ?>

                </div>


            <?php else: ?>


                <!-- =============================================
                     EMPTY STATE
                ============================================== -->

                <div class="empty-classroom">

                    <div class="empty-classroom-icon">

                        <i class="bi bi-people"></i>

                    </div>

                    <h3>
                        No Classmates Found
                    </h3>

                    <p>
                        There are currently no other students
                        assigned to your class.
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

    .page-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

        margin-bottom: 25px;

    }


    .page-title {

        margin: 0;

        font-size: 28px;

        font-weight: 700;

        color: var(--text-color);

        display: flex;

        align-items: center;

        gap: 10px;

    }


    .page-title i {

        color: var(--academic-blue);

    }


    .page-subtitle {

        margin: 5px 0 0;

        color: var(--text-secondary);

        font-size: 14px;

    }


    /* =========================================================
   CLASS INFORMATION
========================================================= */

    .class-info {

        display: flex;

        align-items: center;

        gap: 15px;

        padding: 10px 16px;

        border-radius: 10px;

        background: var(--surface-color);

        border: 1px solid var(--border-color);

        box-shadow:
            0 2px 8px var(--shadow-color);

    }


    .class-info-item {

        display: flex;

        align-items: center;

        gap: 7px;

        color: var(--text-secondary);

        font-size: 13px;

        font-weight: 600;

    }


    .class-info-item i {

        color: var(--academic-blue);

        font-size: 15px;

    }


    .class-info-divider {

        width: 1px;

        height: 20px;

        background: var(--border-color);

    }


    /* =========================================================
   CLASSROOM CONTAINER
========================================================= */

    .classroom-container {

        position: relative;

        overflow: visible;

        background: var(--surface-color);

        border: 1px solid var(--border-color);

        border-radius: 16px;

        box-shadow:
            0 4px 18px var(--shadow-color);

        padding-bottom: 50px;

    }


    /* =========================================================
   CLASSROOM HEADER
========================================================= */

    .classroom-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

        padding: 20px 24px;

        border-bottom: 1px solid var(--border-color);

    }


    .classroom-title {

        display: flex;

        align-items: center;

        gap: 12px;

    }


    .classroom-title-icon {

        width: 42px;

        height: 42px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 10px;

        background: var(--academic-blue-light);

        color: var(--academic-blue);

        font-size: 19px;

    }


    .classroom-title h2 {

        margin: 0;

        font-size: 17px;

        font-weight: 700;

        color: var(--text-color);

    }


    .classroom-title p {

        margin: 2px 0 0;

        font-size: 12px;

        color: var(--text-secondary);

    }


    .classroom-hint {

        display: flex;

        align-items: center;

        gap: 7px;

        font-size: 12px;

        color: var(--text-secondary);

    }


    .classroom-hint i {

        color: var(--academic-blue);

    }


    /* =========================================================
   TEACHER AREA
========================================================= */

    .teacher-area {

        display: flex;

        justify-content: center;

        padding-top: 35px;

        padding-bottom: 30px;

    }


    .teacher-desk {

        position: relative;

        min-width: 150px;

        padding: 12px 25px;

        border-radius: 10px;

        background: var(--surface-secondary);

        border: 1px solid var(--border-color);

        text-align: center;

        box-shadow:
            0 3px 10px var(--shadow-color);

        transition:
            transform 0.3s ease,
            box-shadow 0.3s ease;

    }


    .teacher-desk:hover {

        transform: translateY(-3px);

        box-shadow:
            0 7px 18px var(--shadow-color);

    }


    .teacher-icon {

        color: var(--academic-blue);

        font-size: 25px;

        margin-bottom: 3px;

    }


    .teacher-label {

        color: var(--text-secondary);

        font-size: 12px;

        font-weight: 600;

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

        padding: 20px 45px 30px;

    }


    /* =========================================================
   STUDENT SEAT WRAPPER
========================================================= */

    .student-seat {

        position: relative;

        width: 125px;

        height: 145px;

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: flex-start;

        animation:
            seatAppear 0.5s ease both;

        animation-delay: var(--seat-delay);

    }


    /* =========================================================
   SEAT
========================================================= */

    .seat {

        position: relative;

        width: 100px;

        height: 100px;

        flex-shrink: 0;

        border-radius: 50%;

        border: 5px solid var(--activity-border);

        background: var(--surface-secondary);

        display: flex;

        align-items: center;

        justify-content: center;

        box-shadow:
            0 4px 12px var(--shadow-color);

        cursor: pointer;

        z-index: 1;

        transition:
            transform 0.35s cubic-bezier(.2, .8, .2, 1),
            border-color 0.3s ease,
            box-shadow 0.35s ease,
            background-color 0.3s ease;

    }


    /* =========================================================
   SEAT HOVER
========================================================= */

    .seat:hover {

        transform: scale(1.55);

        border-color: var(--academic-blue);

        background: var(--surface-color);

        box-shadow:
            0 12px 30px var(--shadow-color);

        z-index: 100;

    }


    /* =========================================================
   PHOTO CONTAINER
========================================================= */

    .seat-photo-container {

        width: 90px;

        height: 90px;

        border-radius: 50%;

        overflow: hidden;

        display: flex;

        align-items: center;

        justify-content: center;

        background: var(--activity-number-bg);

    }


    /* =========================================================
   PROFILE PHOTO
========================================================= */

    .seat-photo {

        width: 100%;

        height: 100%;

        object-fit: cover;

        display: block;

        transition:
            transform 0.45s cubic-bezier(.2, .8, .2, 1),
            filter 0.3s ease;

    }


    /* =========================================================
   PHOTO HOVER
========================================================= */

    .seat:hover .seat-photo {

        transform: scale(1.04);

    }


    /* =========================================================
   INITIALS PLACEHOLDER
========================================================= */

    .seat-photo-placeholder {

        width: 100%;

        height: 100%;

        border-radius: 50%;

        display: flex;

        align-items: center;

        justify-content: center;

        background: var(--academic-blue-light);

        color: var(--academic-blue);

        font-size: 28px;

        font-weight: 700;

        letter-spacing: 1px;

        user-select: none;

        transition:
            transform 0.4s cubic-bezier(.2, .8, .2, 1),
            background-color 0.3s ease,
            color 0.3s ease;

    }


    /* =========================================================
   INITIALS HOVER
========================================================= */

    .seat:hover .seat-photo-placeholder {

        transform: scale(1.04);

    }


    /* =========================================================
   STUDENT DETAILS
========================================================= */

    .seat-details {

        position: absolute;

        top: 108px;

        left: 50%;

        min-width: 155px;

        max-width: 220px;

        padding: 8px 11px;

        transform:
            translateX(-50%) translateY(-5px) scale(0.96);

        opacity: 0;

        visibility: hidden;

        pointer-events: none;

        border-radius: 8px;

        background: var(--surface-color);

        border: 1px solid var(--academic-blue);

        box-shadow:
            0 8px 24px var(--shadow-color);

        text-align: center;

        z-index: 200;

        transition:
            opacity 0.25s ease,
            visibility 0.25s ease,
            transform 0.3s cubic-bezier(.2, .8, .2, 1);

    }


    /* =========================================================
   SHOW DETAILS ON HOVER
========================================================= */

    .seat:hover .seat-details {

        opacity: 1;

        visibility: visible;

        transform:
            translateX(-50%) translateY(0) scale(1);

    }


    /* =========================================================
   STUDENT NAME
========================================================= */

    .seat-details-name {

        color: var(--text-color);

        font-size: 12px;

        font-weight: 700;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;

    }


    /* =========================================================
   STUDENT ID
========================================================= */

    .seat-details-id {

        display: flex;

        align-items: center;

        justify-content: center;

        gap: 4px;

        margin-top: 3px;

        color: var(--text-secondary);

        font-size: 10px;

    }


    .seat-details-id i {

        color: var(--academic-blue);

    }


    /* =========================================================
   SEAT BASE
========================================================= */

    .seat-base {

        width: 70px;

        height: 28px;

        margin-top: 8px;

        border-radius: 6px;

        background: var(--surface-secondary);

        border: 1px solid var(--border-color);

        display: flex;

        align-items: center;

        justify-content: center;

        transition:
            transform 0.35s ease,
            box-shadow 0.35s ease,
            background-color 0.3s ease;

    }


    .seat-base span {

        width: 42px;

        height: 3px;

        border-radius: 10px;

        background: var(--border-color);

        transition:
            background-color 0.3s ease;

    }


    /* =========================================================
   BASE HOVER
========================================================= */

    .student-seat:hover .seat-base {

        transform: translateY(3px);

        box-shadow:
            0 4px 10px var(--shadow-color);

    }


    .student-seat:hover .seat-base span {

        background: var(--academic-blue);

    }


    /* =========================================================
   EMPTY CLASSROOM
========================================================= */

    .empty-classroom {

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        padding: 80px 20px;

        text-align: center;

    }


    .empty-classroom-icon {

        width: 70px;

        height: 70px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        background: var(--academic-blue-light);

        color: var(--academic-blue);

        font-size: 30px;

        margin-bottom: 15px;

    }


    .empty-classroom h3 {

        margin: 0 0 5px;

        color: var(--text-color);

        font-size: 18px;

    }


    .empty-classroom p {

        margin: 0;

        max-width: 400px;

        color: var(--text-secondary);

        font-size: 13px;

    }


    /* =========================================================
   SEAT APPEAR ANIMATION
========================================================= */

    @keyframes seatAppear {

        from {

            opacity: 0;

            transform:
                translateY(12px) scale(0.94);

        }

        to {

            opacity: 1;

            transform:
                translateY(0) scale(1);

        }

    }


    /* =========================================================
   DARK MODE
========================================================= */

    [data-theme="dark"] .seat {

        box-shadow:
            0 4px 15px rgba(0, 0, 0, 0.30);

    }


    [data-theme="dark"] .seat:hover {

        box-shadow:
            0 14px 35px rgba(0, 0, 0, 0.45);

    }


    [data-theme="dark"] .seat-details {

        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.40);

    }


    /* =========================================================
   RESPONSIVE - TABLET
========================================================= */

    @media (max-width: 992px) {

        .page-header {

            align-items: flex-start;

            flex-direction: column;

        }


        .class-info {

            width: 100%;

            justify-content: center;

        }


        .seating-area {

            column-gap: 25px;

            row-gap: 50px;

            padding-left: 25px;

            padding-right: 25px;

        }

    }


    /* =========================================================
   RESPONSIVE - MOBILE
========================================================= */

    @media (max-width: 576px) {

        .page-title {

            font-size: 23px;

        }


        .class-info {

            gap: 10px;

            padding: 9px 12px;

        }


        .class-info-item {

            font-size: 11px;

        }


        .classroom-header {

            padding: 16px;

        }


        .classroom-hint {

            display: none;

        }


        .seating-area {

            column-gap: 12px;

            row-gap: 40px;

            padding: 15px 10px 25px;

        }


        .student-seat {

            width: 95px;

            height: 125px;

        }


        .seat {

            width: 82px;

            height: 82px;

            border-width: 4px;

        }


        .seat-photo-container {

            width: 74px;

            height: 74px;

        }


        .seat-photo-placeholder {

            font-size: 22px;

        }


        .seat:hover {

            transform: scale(1.30);

        }


        .seat-details {

            top: 90px;

            min-width: 135px;

            max-width: 175px;

            padding: 7px 9px;

        }


        .seat-details-name {

            font-size: 10px;

        }


        .seat-details-id {

            font-size: 9px;

        }


        .seat-base {

            width: 58px;

            height: 24px;

            margin-top: 7px;

        }

    }


    /* =========================================================
   REDUCED MOTION ACCESSIBILITY
========================================================= */

    @media (prefers-reduced-motion: reduce) {

        .student-seat {

            animation: none;

        }


        .seat,
        .seat-photo,
        .seat-photo-placeholder,
        .seat-details,
        .seat-base,
        .teacher-desk {

            transition: none !important;

        }

    }
</style>


<?php

/* =========================================================
   GLOBAL SCRIPTS
========================================================= */

require_once "./globals/scripts.php";

?>