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

        $name .=
            rtrim(
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
     */

    $photoName = basename($photo);


    if (
        $photoName === "" ||
        $photoName === "."
    ) {
        return null;
    }


    /*
     * Profile photos are stored on the
     * main vertigation.com domain.
     */

    return
        "https://vertigation.com/shared/uploads/profile_photos/" .
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

            </div>


            <!-- =================================================
                 CLASS INFORMATION
            ================================================== -->

            <div class="class-info">

                <div class="class-info-item">

                    <i class="bi bi-mortarboard-fill"></i>

                    <span>
                        <?= htmlspecialchars(
                            $department,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </span>

                </div>


                <div class="class-info-divider"></div>


                <div class="class-info-item">

                    <i class="bi bi-people-fill"></i>

                    <span>
                        <?= htmlspecialchars(
                            $yearSection,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
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
                        Hover over your classmate to view details
                    </span>

                </div>

            </div>


            <!-- =================================================
                 CLASSMATE SEATING AREA
            ================================================== -->

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
                             STUDENT SEAT WRAPPER
                        ================================== -->

                        <div
                            class="student-seat"
                            style="
                                --seat-delay: <?= ($index * 0.04) ?>s;
                            ">


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
                                            src="<?= htmlspecialchars(
                                                        $photo,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>"
                                            alt="<?= htmlspecialchars(
                                                        $fullName,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>"
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

                                            <?= htmlspecialchars(
                                                $initials,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </div>


                                    <?php else: ?>


                                        <!-- =========================
                                             INITIALS
                                        ========================== -->

                                        <div class="seat-photo-placeholder">

                                            <?= htmlspecialchars(
                                                $initials,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </div>

                                    <?php endif; ?>

                                </div>


                                <!-- =================================
                                     STUDENT DETAILS
                                ================================== -->

                                <div class="seat-details">

                                    <div class="seat-details-name">

                                        <?= htmlspecialchars(
                                            $fullName,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>

                                    </div>


                                    <div class="seat-details-id">

                                        <i class="bi bi-person-badge"></i>

                                        <?= htmlspecialchars(
                                            $classmate["student_id"],
                                            ENT_QUOTES,
                                            "UTF-8"
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

        display: flex;

        align-items: center;

        gap: 10px;

        color: var(--text-color);

        font-size: 28px;

        font-weight: 700;

    }


    .page-title i {

        color: var(--academic-blue);

    }


    /* =========================================================
   CLASS INFORMATION
========================================================= */

    .class-info {

        display: flex;

        align-items: center;

        gap: 15px;

        padding: 10px 16px;

        border:
            1px solid var(--border-color);

        border-radius: 10px;

        background:
            var(--surface-color);

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

        /*
     * IMPORTANT:
     * Details extend outside individual seats.
     * Therefore the classroom must remain visible.
     */

        overflow: visible;

        background:
            var(--surface-color);

        border:
            1px solid var(--border-color);

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

        border-bottom:
            1px solid var(--border-color);

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

        background:
            var(--academic-blue-light);

        color:
            var(--academic-blue);

        font-size: 19px;

    }


    .classroom-title h2 {

        margin: 0;

        color: var(--text-color);

        font-size: 17px;

        font-weight: 700;

    }


    .classroom-title p {

        margin: 2px 0 0;

        color: var(--text-secondary);

        font-size: 12px;

    }


    .classroom-hint {

        display: flex;

        align-items: center;

        gap: 7px;

        color: var(--text-secondary);

        font-size: 12px;

    }


    .classroom-hint i {

        color: var(--academic-blue);

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

        row-gap: 70px;

        padding:
            35px 45px 45px;

    }


    /* =========================================================
   STUDENT SEAT WRAPPER
========================================================= */

    .student-seat {

        /*
     * The wrapper owns the stacking level.
     *
     * This is important because transforming the child
     * .seat creates a new stacking context.
     */

        position: relative;

        width: 125px;

        height: 155px;

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: flex-start;

        animation:
            seatAppear 0.5s ease both;

        animation-delay:
            var(--seat-delay);

        /*
     * Allow details to escape the wrapper.
     */

        overflow: visible;

    }


    /* =========================================================
   HOVERED STUDENT GETS HIGHEST LAYER
========================================================= */

    .student-seat:hover {

        z-index: 1000;

    }


    /* =========================================================
   SEAT
========================================================= */

    .seat {

        position: relative;

        width: 100px;

        height: 100px;

        flex-shrink: 0;

        display: flex;

        align-items: center;

        justify-content: center;

        border:
            5px solid var(--activity-border);

        border-radius: 50%;

        background:
            var(--surface-secondary);

        box-shadow:
            0 4px 12px var(--shadow-color);

        cursor: pointer;

        z-index: 1;

        /*
     * Only animate the visual seat.
     */

        transition:
            transform 0.30s cubic-bezier(.2, .8, .2, 1),
            border-color 0.25s ease,
            box-shadow 0.30s ease,
            background-color 0.25s ease;

    }


    /* =========================================================
   SEAT HOVER
========================================================= */

    .student-seat:hover .seat {

        /*
     * Reduced from 1.55.
     *
     * 1.55 was causing excessive overlap with nearby
     * classmates.
     */

        transform:
            scale(1.38);

        border-color:
            var(--academic-blue);

        background:
            var(--surface-color);

        box-shadow:
            0 14px 32px var(--shadow-color);

        z-index: 100;

    }


    /* =========================================================
   PHOTO CONTAINER
========================================================= */

    .seat-photo-container {

        width: 90px;

        height: 90px;

        flex-shrink: 0;

        display: flex;

        align-items: center;

        justify-content: center;

        overflow: hidden;

        border-radius: 50%;

        background:
            var(--activity-number-bg);

    }


    /* =========================================================
   PROFILE PHOTO
========================================================= */

    .seat-photo {

        width: 100%;

        height: 100%;

        display: block;

        object-fit: cover;

        transition:
            transform 0.35s cubic-bezier(.2, .8, .2, 1);

    }


    .student-seat:hover .seat-photo {

        transform:
            scale(1.04);

    }


    /* =========================================================
   INITIALS PLACEHOLDER
========================================================= */

    .seat-photo-placeholder {

        width: 100%;

        height: 100%;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        background:
            var(--academic-blue-light);

        color:
            var(--academic-blue);

        font-size: 28px;

        font-weight: 700;

        letter-spacing: 1px;

        user-select: none;

        transition:
            transform 0.35s cubic-bezier(.2, .8, .2, 1),
            background-color 0.25s ease,
            color 0.25s ease;

    }


    .student-seat:hover .seat-photo-placeholder {

        transform:
            scale(1.04);

    }


    /* =========================================================
   STUDENT DETAILS
========================================================= */

    .seat-details {

        /*
     * Position relative to the complete student seat.
     * This avoids the detail panel being affected by
     * the enlarged .seat.
     */

        position: absolute;

        top: 112px;

        left: 50%;

        width: max-content;

        min-width: 155px;

        max-width: 230px;

        padding:
            8px 12px;

        border:
            1px solid var(--academic-blue);

        border-radius: 9px;

        background:
            var(--surface-color);

        box-shadow:
            0 10px 26px var(--shadow-color);

        text-align: center;

        /*
     * Keep it centered.
     */

        transform:
            translateX(-50%) translateY(-6px);

        /*
     * Hidden until hover.
     */

        opacity: 0;

        visibility: hidden;

        pointer-events: none;

        z-index: 2000;

        /*
     * IMPORTANT:
     * Do not use scale here.
     * Scaling the details while the seat itself scales
     * makes the overlap appear exaggerated.
     */

        transition:
            opacity 0.20s ease,
            visibility 0.20s ease,
            transform 0.20s ease;

    }


    /* =========================================================
   DETAILS SHOW
========================================================= */

    .student-seat:hover .seat-details {

        opacity: 1;

        visibility: visible;

        transform:
            translateX(-50%) translateY(0);

    }


    /* =========================================================
   SMALL POINTER / ARROW
========================================================= */

    .seat-details::before {

        content: "";

        position: absolute;

        left: 50%;

        top: -6px;

        width: 10px;

        height: 10px;

        background:
            var(--surface-color);

        border-left:
            1px solid var(--academic-blue);

        border-top:
            1px solid var(--academic-blue);

        transform:
            translateX(-50%) rotate(45deg);

    }


    /* =========================================================
   STUDENT NAME
========================================================= */

    .seat-details-name {

        max-width: 205px;

        overflow: hidden;

        color:
            var(--text-color);

        font-size: 12px;

        font-weight: 700;

        line-height: 1.35;

        white-space: nowrap;

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

        color:
            var(--text-secondary);

        font-size: 10px;

        line-height: 1.3;

    }


    .seat-details-id i {

        color:
            var(--academic-blue);

    }


    /* =========================================================
   SEAT BASE
========================================================= */

    .seat-base {

        width: 70px;

        height: 28px;

        margin-top: 8px;

        display: flex;

        align-items: center;

        justify-content: center;

        border:
            1px solid var(--border-color);

        border-radius: 6px;

        background:
            var(--surface-secondary);

        transition:
            transform 0.30s ease,
            box-shadow 0.30s ease,
            background-color 0.25s ease;

    }


    .seat-base span {

        width: 42px;

        height: 3px;

        border-radius: 10px;

        background:
            var(--border-color);

        transition:
            background-color 0.25s ease;

    }


    .student-seat:hover .seat-base {

        transform:
            translateY(3px);

        box-shadow:
            0 4px 10px var(--shadow-color);

    }


    .student-seat:hover .seat-base span {

        background:
            var(--academic-blue);

    }


    /* =========================================================
   EMPTY CLASSROOM
========================================================= */

    .empty-classroom {

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        padding:
            80px 20px;

        text-align: center;

    }


    .empty-classroom-icon {

        width: 70px;

        height: 70px;

        display: flex;

        align-items: center;

        justify-content: center;

        margin-bottom: 15px;

        border-radius: 50%;

        background:
            var(--academic-blue-light);

        color:
            var(--academic-blue);

        font-size: 30px;

    }


    .empty-classroom h3 {

        margin:
            0 0 5px;

        color:
            var(--text-color);

        font-size: 18px;

    }


    .empty-classroom p {

        max-width: 400px;

        margin: 0;

        color:
            var(--text-secondary);

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

    [data-theme="dark"] .classroom-container {

        box-shadow:
            0 4px 18px rgba(0, 0, 0, 0.30);

    }


    [data-theme="dark"] .student-seat:hover .seat {

        box-shadow:
            0 14px 35px rgba(0, 0, 0, 0.45);

    }


    [data-theme="dark"] .seat-details {

        box-shadow:
            0 10px 30px rgba(0, 0, 0, 0.45);

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

            row-gap: 70px;

            padding:
                30px 25px 40px;

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

            padding:
                9px 12px;

        }


        .class-info-item {

            font-size: 11px;

        }


        .classroom-header {

            padding:
                16px;

        }


        .classroom-hint {

            display: none;

        }


        .seating-area {

            column-gap: 12px;

            row-gap: 60px;

            padding:
                25px 10px 35px;

        }


        .student-seat {

            width: 95px;

            height: 130px;

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


        .student-seat:hover .seat {

            transform:
                scale(1.25);

        }


        .seat-details {

            top: 92px;

            min-width: 135px;

            max-width: 180px;

            padding:
                7px 9px;

        }


        .seat-details-name {

            max-width: 158px;

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
   VERY SMALL MOBILE
========================================================= */

    @media (max-width: 400px) {

        .class-info {

            width: 100%;

            justify-content: space-between;

        }


        .class-info-divider {

            display: none;

        }


        .seating-area {

            column-gap: 5px;

        }


        .student-seat {

            width: 88px;

        }


        .student-seat:hover .seat {

            transform:
                scale(1.18);

        }


        .seat-details {

            min-width: 125px;

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

        .seat-base {

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