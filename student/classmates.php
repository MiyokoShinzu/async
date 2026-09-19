<?php
/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   STUDENT - CLASSMATES PAGE
   =========================================================

   PURPOSE:
   ---------------------------------------------------------
   Displays the students belonging to the same class based
   on the current student's:

       department
       year_section

   DATABASE TABLE:
   ---------------------------------------------------------
   accounts

   FEATURES:
   ---------------------------------------------------------
   1. Student-only authentication
   2. Classmate retrieval
   3. Circular student tiles
   4. Student profile photo
   5. Initials fallback
   6. Hover animation
   7. Student information popup
   8. Fixed hover overlap
   9. Responsive classroom layout
   10. Light / dark theme support

   DATABASE CONNECTION:
   ---------------------------------------------------------
   ../src/connection.php

   IMPORTANT:
   ---------------------------------------------------------
   This project uses:

       $mysqli

   and NOT:

       $conn

   ACCOUNTS TABLE FIELDS USED:
   ---------------------------------------------------------
   id
   last_name
   first_name
   middle_initial
   extension_name
   department
   year_section
   student_id
   email
   access
   profile_photo

   TIMEZONE:
   ---------------------------------------------------------
   Asia/Manila
   ========================================================= */


/* =========================================================
   TIMEZONE
   ========================================================= */

date_default_timezone_set('Asia/Manila');


/* =========================================================
   SESSION
   ========================================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =========================================================
   STUDENT AUTHENTICATION
   ========================================================= */

if (
    !isset($_SESSION['access']) ||
    $_SESSION['access'] !== 'student'
) {
    header("Location: login.php");
    exit;
}


/* =========================================================
   DATABASE CONNECTION
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   CURRENT STUDENT ID
   ========================================================= */

$currentStudentId = trim(
    (string)($_SESSION['student_id'] ?? '')
);


/* =========================================================
   HELPER:
   ESCAPE HTML
   ========================================================= */

function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/* =========================================================
   HELPER:
   CREATE INITIALS
   ========================================================= */

function getInitials($firstName, $lastName)
{
    $firstName = trim((string)$firstName);
    $lastName  = trim((string)$lastName);

    $initials = '';

    if ($firstName !== '') {
        $initials .= mb_substr(
            $firstName,
            0,
            1
        );
    }

    if ($lastName !== '') {
        $initials .= mb_substr(
            $lastName,
            0,
            1
        );
    }

    if ($initials === '') {
        return '?';
    }

    return strtoupper($initials);
}


/* =========================================================
   HELPER:
   BUILD FULL NAME
   ========================================================= */

function buildFullName($firstName, $middleInitial, $lastName, $extensionName)
{
    $parts = [];

    $firstName = trim((string)$firstName);
    $middleInitial = trim((string)$middleInitial);
    $lastName = trim((string)$lastName);
    $extensionName = trim((string)$extensionName);


    if ($firstName !== '') {
        $parts[] = $firstName;
    }


    if ($middleInitial !== '') {

        /*
         * Prevent duplicate period if the database already
         * stores something like "S."
         */

        if (substr($middleInitial, -1) !== '.') {
            $middleInitial .= '.';
        }

        $parts[] = $middleInitial;
    }


    if ($lastName !== '') {
        $parts[] = $lastName;
    }


    $fullName = implode(
        ' ',
        $parts
    );


    if ($extensionName !== '') {

        $fullName .= ', ' . $extensionName;
    }


    return trim($fullName);
}


/* =========================================================
   HELPER:
   PROFILE PHOTO URL
   ========================================================= */

function getProfilePhoto($photo)
{
    $photo = trim((string)$photo);

    if ($photo === '') {
        return '';
    }


    /* -----------------------------------------------
       Complete URL
       ----------------------------------------------- */

    if (
        str_starts_with($photo, 'http://') ||
        str_starts_with($photo, 'https://')
    ) {
        return $photo;
    }


    /* -----------------------------------------------
       Root-relative path
       ----------------------------------------------- */

    if (str_starts_with($photo, '/')) {
        return $photo;
    }


    /* -----------------------------------------------
       Existing relative paths
       ----------------------------------------------- */

    if (
        str_starts_with($photo, './') ||
        str_starts_with($photo, '../')
    ) {
        return $photo;
    }


    /* -----------------------------------------------
       Upload / asset paths
       ----------------------------------------------- */

    if (
        str_starts_with($photo, 'uploads/') ||
        str_starts_with($photo, 'assets/')
    ) {
        return '/' . ltrim($photo, '/');
    }


    /*
     * Default profile photo location.
     *
     * If your profile photo database value already
     * contains its complete path, the conditions above
     * will preserve it.
     */

    return '../uploads/profile/' . ltrim(
        $photo,
        '/'
    );
}


/* =========================================================
   CURRENT STUDENT INFORMATION
   ========================================================= */

$currentStudent = null;


if ($currentStudentId !== '') {

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
            access,
            profile_photo
        FROM accounts
        WHERE student_id = ?
        LIMIT 1
    ");


    if ($stmt) {

        $stmt->bind_param(
            "s",
            $currentStudentId
        );

        $stmt->execute();

        $result = $stmt->get_result();


        if (
            $result &&
            $result->num_rows > 0
        ) {

            $currentStudent = $result->fetch_assoc();
        }


        $stmt->close();
    }
}


/* =========================================================
   CURRENT STUDENT CLASS INFORMATION
   ========================================================= */

$currentDepartment = '';
$currentYearSection = '';


if ($currentStudent) {

    $currentDepartment = trim(
        (string)($currentStudent['department'] ?? '')
    );

    $currentYearSection = trim(
        (string)($currentStudent['year_section'] ?? '')
    );
}


/* =========================================================
   CLASSMATES ARRAY
   ========================================================= */

$classmates = [];


/* =========================================================
   RETRIEVE CLASSMATES
   =========================================================

   IMPORTANT:
   ---------------------------------------------------------
   Since your accounts table contains:

       department
       year_section

   the classmates are matched using BOTH fields.

   The current student is also included.

   If you want to exclude the currently logged-in student,
   add:

       AND student_id <> ?

   ========================================================= */

if (
    $currentDepartment !== '' &&
    $currentYearSection !== ''
) {

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
            access,
            profile_photo
        FROM accounts
        WHERE department = ?
          AND year_section = ?
          AND access = 'student'
        ORDER BY
            last_name ASC,
            first_name ASC,
            student_id ASC
    ");


    if ($stmt) {

        $stmt->bind_param(
            "ss",
            $currentDepartment,
            $currentYearSection
        );

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result) {

            while (
                $row = $result->fetch_assoc()
            ) {

                $classmates[] = $row;
            }
        }


        $stmt->close();
    }
}


/* =========================================================
   CLASS DISPLAY
   ========================================================= */

$classDisplay = '';


if (
    $currentDepartment !== '' &&
    $currentYearSection !== ''
) {

    $classDisplay =
        $currentDepartment .
        ' • ' .
        $currentYearSection;
}


/* =========================================================
   INCLUDE GLOBAL HEAD
   ========================================================= */

require_once "./globals/head.php";


/* =========================================================
   INCLUDE SIDEBAR
   ========================================================= */

require_once "./globals/sidebar.php";


/* =========================================================
   INCLUDE TOPBAR
   ========================================================= */

require_once "./globals/topbar.php";

?>


<!-- =======================================================
     PAGE CONTENT
     ======================================================= -->

<main class="main-content">

    <div class="container-fluid classmates-page">


        <!-- =================================================
             PAGE HEADER
             ================================================= -->

        <div class="classmates-header">


            <div>

                <div class="classmates-eyebrow">
                    CLASSROOM
                </div>


                <h1 class="classmates-title">
                    My Classmates
                </h1>


                <p class="classmates-subtitle">

                    <?php if ($classDisplay !== ''): ?>

                        <?= e($classDisplay) ?>

                    <?php else: ?>

                        Your classmates

                    <?php endif; ?>

                </p>

            </div>


            <!-- =============================================
                 CLASSMATE COUNT
                 ============================================= -->

            <div class="classmates-count-card">

                <div class="count-number">
                    <?= count($classmates) ?>
                </div>


                <div class="count-label">
                    CLASSMATES
                </div>

            </div>


        </div>



        <!-- =================================================
             CLASSROOM CARD
             ================================================= -->

        <section class="classroom-card">


            <!-- =============================================
                 CLASSROOM HEADER
                 ============================================= -->

            <div class="classroom-header">

                <div>

                    <h2 class="classroom-title">
                        Classroom
                    </h2>


                    <p class="classroom-description">
                        Hover over a classmate to view their details.
                    </p>

                </div>

            </div>



            <!-- =============================================
                 CLASSROOM AREA
                 ============================================= -->

            <div class="classroom-container">


                <!-- =========================================
                     CLASSROOM BOARD
                     ========================================= -->

                <div class="classroom-board">

                    <span>
                        CLASSROOM
                    </span>

                </div>



                <!-- =========================================
                     STUDENT SEATING
                     ========================================= -->

                <div class="student-seating">


                    <?php if (!empty($classmates)): ?>


                        <?php foreach (
                            $classmates as $index => $classmate
                        ): ?>


                            <?php
                            /* =================================
                               STUDENT DATA
                               ================================= */

                            $firstName = trim(
                                (string)(
                                    $classmate['first_name']
                                    ?? ''
                                )
                            );


                            $middleInitial = trim(
                                (string)(
                                    $classmate['middle_initial']
                                    ?? ''
                                )
                            );


                            $lastName = trim(
                                (string)(
                                    $classmate['last_name']
                                    ?? ''
                                )
                            );


                            $extensionName = trim(
                                (string)(
                                    $classmate['extension_name']
                                    ?? ''
                                )
                            );


                            $studentId = trim(
                                (string)(
                                    $classmate['student_id']
                                    ?? ''
                                )
                            );


                            $department = trim(
                                (string)(
                                    $classmate['department']
                                    ?? ''
                                )
                            );


                            $yearSection = trim(
                                (string)(
                                    $classmate['year_section']
                                    ?? ''
                                )
                            );


                            $profilePhoto = getProfilePhoto(
                                $classmate['profile_photo']
                                    ?? ''
                            );


                            /* =================================
                               FULL NAME
                               ================================= */

                            $fullName = buildFullName(
                                $firstName,
                                $middleInitial,
                                $lastName,
                                $extensionName
                            );


                            if ($fullName === '') {

                                $fullName = 'Student';
                            }


                            /* =================================
                               INITIALS
                               ================================= */

                            $initials = getInitials(
                                $firstName,
                                $lastName
                            );


                            /* =================================
                               CURRENT STUDENT
                               ================================= */

                            $isCurrentStudent =
                                $studentId ===
                                $currentStudentId;


                            /* =================================
                               ANIMATION DELAY
                               ================================= */

                            $animationDelay =
                                ($index % 12) * 0.08;

                            ?>


                            <!-- =================================
                                 STUDENT SEAT WRAPPER
                                 ================================= -->

                            <div
                                class="student-seat <?= $isCurrentStudent ? 'current-student' : '' ?>"
                                style="--animation-delay: <?= e($animationDelay) ?>s;">


                                <!-- =============================
                                     STUDENT CIRCLE
                                     ============================= -->

                                <div class="seat">


                                    <?php if ($profilePhoto !== ''): ?>


                                        <img
                                            src="<?= e($profilePhoto) ?>"
                                            alt="<?= e($fullName) ?>"
                                            class="seat-photo"
                                            loading="lazy"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">


                                        <div
                                            class="seat-initials"
                                            style="display:none;">
                                            <?= e($initials) ?>
                                        </div>


                                    <?php else: ?>


                                        <div class="seat-initials">
                                            <?= e($initials) ?>
                                        </div>


                                    <?php endif; ?>


                                    <!-- =========================
                                         CURRENT USER BADGE
                                         ========================= -->

                                    <?php if ($isCurrentStudent): ?>

                                        <span class="you-badge">
                                            YOU
                                        </span>

                                    <?php endif; ?>


                                </div>



                                <!-- =================================
                                     STUDENT NAME
                                     ================================= -->

                                <div class="seat-name">

                                    <?= e($fullName) ?>

                                </div>



                                <!-- =================================
                                     STUDENT DETAILS
                                     ================================= -->

                                <div class="seat-details">


                                    <div class="detail-name">

                                        <?= e($fullName) ?>

                                    </div>


                                    <div class="detail-role">

                                        Student

                                    </div>


                                    <!-- =============================
                                         STUDENT ID
                                         ============================= -->

                                    <?php if ($studentId !== ''): ?>

                                        <div class="detail-row">

                                            <i class="bi bi-person-badge"></i>

                                            <span>
                                                <?= e($studentId) ?>
                                            </span>

                                        </div>

                                    <?php endif; ?>


                                    <!-- =============================
                                         YEAR / SECTION
                                         ============================= -->

                                    <?php if ($yearSection !== ''): ?>

                                        <div class="detail-row">

                                            <i class="bi bi-mortarboard"></i>

                                            <span>
                                                <?= e($yearSection) ?>
                                            </span>

                                        </div>

                                    <?php endif; ?>


                                    <!-- =============================
                                         DEPARTMENT
                                         ============================= -->

                                    <?php if ($department !== ''): ?>

                                        <div class="detail-row">

                                            <i class="bi bi-building"></i>

                                            <span>
                                                <?= e($department) ?>
                                            </span>

                                        </div>

                                    <?php endif; ?>


                                </div>


                            </div>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <!-- =========================================
                             EMPTY CLASS
                             ========================================= -->

                        <div class="empty-class">


                            <div class="empty-icon">

                                <i class="bi bi-people"></i>

                            </div>


                            <h3>
                                No Classmates Found
                            </h3>


                            <p>
                                There are currently no students
                                available in your class.
                            </p>


                        </div>


                    <?php endif; ?>


                </div>


            </div>


        </section>


    </div>

</main>



<!-- =======================================================
     PAGE-SPECIFIC STYLES
     ======================================================= -->

<style>
    /* =========================================================
   PAGE VARIABLES
   ========================================================= */

    .classmates-page {

        --class-primary: #0b4f8a;
        --class-primary-light: #1976c9;

        --class-card: #ffffff;

        --class-border:
            rgba(11, 79, 138, 0.12);

        --class-text: #172033;

        --class-muted: #687386;

        padding-bottom: 40px;

    }


    /* =========================================================
   DARK THEME
   ========================================================= */

    [data-theme="dark"] .classmates-page {

        --class-card: #182231;

        --class-border:
            rgba(255, 255, 255, 0.08);

        --class-text: #f1f5f9;

        --class-muted: #9ca8b8;

    }


    /* =========================================================
   PAGE HEADER
   ========================================================= */

    .classmates-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 25px;

        margin-bottom: 25px;

    }


    /* =========================================================
   EYEBROW
   ========================================================= */

    .classmates-eyebrow {

        margin-bottom: 5px;

        color: var(--class-primary);

        font-size: 0.75rem;

        font-weight: 800;

        letter-spacing: 0.14em;

    }


    /* =========================================================
   TITLE
   ========================================================= */

    .classmates-title {

        margin: 0;

        color: var(--class-text);

        font-size: 2rem;

        font-weight: 800;

        line-height: 1.2;

    }


    /* =========================================================
   SUBTITLE
   ========================================================= */

    .classmates-subtitle {

        margin: 7px 0 0;

        color: var(--class-muted);

        font-size: 0.92rem;

    }


    /* =========================================================
   COUNT CARD
   ========================================================= */

    .classmates-count-card {

        flex-shrink: 0;

        min-width: 110px;

        padding: 15px 20px;

        text-align: center;

        border-radius: 16px;

        background: var(--class-card);

        border:
            1px solid var(--class-border);

        box-shadow:
            0 8px 25px rgba(15, 23, 42, 0.06);

    }


    .count-number {

        color: var(--class-primary);

        font-size: 1.8rem;

        font-weight: 800;

        line-height: 1;

    }


    .count-label {

        margin-top: 5px;

        color: var(--class-muted);

        font-size: 0.65rem;

        font-weight: 800;

        letter-spacing: 0.1em;

    }


    /* =========================================================
   CLASSROOM CARD
   ========================================================= */

    .classroom-card {

        position: relative;

        overflow: visible;

        background: var(--class-card);

        border:
            1px solid var(--class-border);

        border-radius: 22px;

        box-shadow:
            0 12px 35px rgba(15, 23, 42, 0.06);

    }


    /* =========================================================
   CLASSROOM HEADER
   ========================================================= */

    .classroom-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        padding: 22px 25px;

        border-bottom:
            1px solid var(--class-border);

    }


    .classroom-title {

        margin: 0;

        color: var(--class-text);

        font-size: 1.15rem;

        font-weight: 750;

    }


    .classroom-description {

        margin: 4px 0 0;

        color: var(--class-muted);

        font-size: 0.82rem;

    }


    /* =========================================================
   CLASSROOM CONTAINER
   ========================================================= */

    .classroom-container {

        position: relative;

        min-height: 580px;

        padding:
            45px 35px 70px;

        overflow: visible;

    }


    /* =========================================================
   CLASSROOM BOARD
   ========================================================= */

    .classroom-board {

        width: min(560px, 80%);

        min-height: 55px;

        margin:
            0 auto 70px;

        display: flex;

        align-items: center;

        justify-content: center;

        text-align: center;

        border-radius: 14px;

        background:
            linear-gradient(135deg,
                #0b4f8a,
                #1976c9);

        box-shadow:
            0 10px 25px rgba(11, 79, 138, 0.20);

        color: #ffffff;

        font-size: 0.78rem;

        font-weight: 800;

        letter-spacing: 0.15em;

    }


    /* =========================================================
   STUDENT SEATING
   ========================================================= */

    .student-seating {

        display: grid;

        grid-template-columns:
            repeat(auto-fit,
                minmax(110px, 1fr));

        column-gap: 18px;

        row-gap: 70px;

        align-items: start;

        justify-items: center;

        width: 100%;

        overflow: visible;

    }


    /* =========================================================
   STUDENT SEAT WRAPPER
   ========================================================= */

    .student-seat {

        position: relative;

        width: 110px;

        min-height: 145px;

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: flex-start;

        z-index: 1;

    }


    /* =========================================================
   IMPORTANT HOVER STACKING FIX
   ========================================================= */

    .student-seat:hover {

        z-index: 1000;

    }


    /* =========================================================
   STUDENT CIRCLE
   ========================================================= */

    .seat {

        position: relative;

        width: 100px;

        height: 100px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        overflow: visible;

        background:
            linear-gradient(135deg,
                #0b4f8a,
                #1976c9);

        border:
            4px solid rgba(255, 255, 255, 0.92);

        box-shadow:
            0 8px 20px rgba(11, 79, 138, 0.20);

        transform: scale(1);

        transition:
            transform 0.22s ease,
            box-shadow 0.22s ease;

        animation:
            studentFloat 4s ease-in-out infinite;

        animation-delay:
            var(--animation-delay);

    }


    /* =========================================================
   CIRCLE HOVER
   ========================================================= */

    .student-seat:hover .seat {

        transform: scale(1.38);

        box-shadow:
            0 16px 32px rgba(11, 79, 138, 0.30);

    }


    /* =========================================================
   STUDENT PHOTO
   ========================================================= */

    .seat-photo {

        width: 100%;

        height: 100%;

        display: block;

        object-fit: cover;

        border-radius: 50%;

    }


    /* =========================================================
   INITIALS
   ========================================================= */

    .seat-initials {

        width: 100%;

        height: 100%;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        color: #ffffff;

        font-size: 1.7rem;

        font-weight: 800;

    }


    /* =========================================================
   CURRENT STUDENT
   ========================================================= */

    .current-student .seat {

        border-color: #ffffff;

        box-shadow:
            0 0 0 4px rgba(25, 118, 201, 0.22),

            0 10px 25px rgba(11, 79, 138, 0.25);

    }


    /* =========================================================
   YOU BADGE
   ========================================================= */

    .you-badge {

        position: absolute;

        right: -6px;

        bottom: 4px;

        z-index: 20;

        padding:
            4px 7px;

        border-radius: 999px;

        background: #ffffff;

        color: var(--class-primary);

        border:
            2px solid var(--class-primary);

        font-size: 0.52rem;

        font-weight: 900;

        letter-spacing: 0.05em;

        box-shadow:
            0 4px 10px rgba(0, 0, 0, 0.12);

    }


    /* =========================================================
   STUDENT NAME
   ========================================================= */

    .seat-name {

        width: 115px;

        margin-top: 13px;

        text-align: center;

        color: var(--class-text);

        font-size: 0.72rem;

        font-weight: 700;

        line-height: 1.3;

        overflow: hidden;

        display: -webkit-box;

        -webkit-line-clamp: 2;

        -webkit-box-orient: vertical;

    }


    /* =========================================================
   STUDENT DETAILS
   =========================================================

   IMPORTANT:
   ---------------------------------------------------------
   This is positioned relative to .student-seat.

   It is NOT positioned relative to .seat.

   Therefore the scale transformation of the circle does
   not affect the detail panel position.
   ========================================================= */

    .seat-details {

        position: absolute;

        top: 112px;

        left: 50%;

        width: max-content;

        min-width: 175px;

        max-width: 240px;

        padding:
            13px 14px;

        border-radius: 14px;

        background:
            var(--class-card);

        border:
            1px solid var(--class-border);

        box-shadow:
            0 18px 40px rgba(15, 23, 42, 0.18);

        transform:
            translateX(-50%) translateY(-8px);

        opacity: 0;

        visibility: hidden;

        pointer-events: none;

        z-index: 2000;

        transition:
            opacity 0.18s ease,
            transform 0.18s ease,
            visibility 0.18s ease;

    }


    /* =========================================================
   SHOW DETAILS
   ========================================================= */

    .student-seat:hover .seat-details {

        opacity: 1;

        visibility: visible;

        transform:
            translateX(-50%) translateY(0);

    }


    /* =========================================================
   DETAILS POINTER
   ========================================================= */

    .seat-details::before {

        content: "";

        position: absolute;

        top: -7px;

        left: 50%;

        width: 13px;

        height: 13px;

        background:
            var(--class-card);

        border-left:
            1px solid var(--class-border);

        border-top:
            1px solid var(--class-border);

        transform:
            translateX(-50%) rotate(45deg);

    }


    /* =========================================================
   DETAIL NAME
   ========================================================= */

    .detail-name {

        position: relative;

        color: var(--class-text);

        font-size: 0.8rem;

        font-weight: 800;

        line-height: 1.3;

        margin-bottom: 2px;

    }


    /* =========================================================
   DETAIL ROLE
   ========================================================= */

    .detail-role {

        position: relative;

        margin-bottom: 9px;

        color: var(--class-primary);

        font-size: 0.63rem;

        font-weight: 800;

        text-transform: uppercase;

        letter-spacing: 0.08em;

    }


    /* =========================================================
   DETAIL ROW
   ========================================================= */

    .detail-row {

        position: relative;

        display: flex;

        align-items: center;

        gap: 7px;

        padding: 3px 0;

        color: var(--class-muted);

        font-size: 0.68rem;

        line-height: 1.25;

    }


    .detail-row i {

        width: 15px;

        flex-shrink: 0;

        color: var(--class-primary);

        font-size: 0.72rem;

    }


    /* =========================================================
   FLOAT ANIMATION
   ========================================================= */

    @keyframes studentFloat {

        0%,
        100% {

            transform:
                translateY(0);

        }

        50% {

            transform:
                translateY(-5px);

        }

    }


    /* =========================================================
   EMPTY CLASS
   ========================================================= */

    .empty-class {

        grid-column: 1 / -1;

        width: 100%;

        min-height: 300px;

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        text-align: center;

    }


    .empty-icon {

        width: 75px;

        height: 75px;

        display: flex;

        align-items: center;

        justify-content: center;

        margin-bottom: 15px;

        border-radius: 50%;

        background:
            rgba(11, 79, 138, 0.08);

        color: var(--class-primary);

        font-size: 2rem;

    }


    .empty-class h3 {

        margin: 0;

        color: var(--class-text);

        font-size: 1rem;

        font-weight: 750;

    }


    .empty-class p {

        max-width: 360px;

        margin: 7px 0 0;

        color: var(--class-muted);

        font-size: 0.82rem;

    }


    /* =========================================================
   TABLET
   ========================================================= */

    @media (max-width: 991px) {

        .classmates-header {

            align-items: flex-start;

        }


        .classroom-container {

            padding:
                35px 25px 60px;

        }


        .student-seating {

            grid-template-columns:
                repeat(auto-fit,
                    minmax(100px, 1fr));

            column-gap: 12px;

            row-gap: 65px;

        }


        .student-seat {

            width: 100px;

        }


        .seat {

            width: 90px;

            height: 90px;

        }


        .student-seat:hover .seat {

            transform:
                scale(1.28);

        }

    }


    /* =========================================================
   MOBILE
   ========================================================= */

    @media (max-width: 767px) {

        .classmates-header {

            flex-direction: column;

            gap: 15px;

        }


        .classmates-title {

            font-size: 1.65rem;

        }


        .classmates-count-card {

            width: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 10px;

        }


        .count-number {

            font-size: 1.35rem;

        }


        .count-label {

            margin-top: 0;

        }


        .classroom-header {

            padding:
                18px 20px;

        }


        .classroom-container {

            min-height: 500px;

            padding:
                30px 15px 50px;

        }


        .classroom-board {

            width: 90%;

            min-height: 48px;

            margin-bottom: 60px;

        }


        .student-seating {

            grid-template-columns:
                repeat(3,
                    1fr);

            column-gap: 5px;

            row-gap: 65px;

        }


        .student-seat {

            width: 82px;

            min-height: 130px;

        }


        .seat {

            width: 82px;

            height: 82px;

        }


        .seat-initials {

            font-size: 1.35rem;

        }


        .student-seat:hover .seat {

            transform:
                scale(1.25);

        }


        .seat-name {

            width: 92px;

            margin-top: 10px;

            font-size: 0.65rem;

        }


        .seat-details {

            top: 96px;

            min-width: 155px;

            max-width: 210px;

            padding:
                11px 12px;

        }

    }


    /* =========================================================
   SMALL MOBILE
   ========================================================= */

    @media (max-width: 420px) {

        .student-seating {

            grid-template-columns:
                repeat(3,
                    1fr);

            row-gap: 62px;

        }


        .student-seat {

            width: 75px;

        }


        .seat {

            width: 75px;

            height: 75px;

        }


        .student-seat:hover .seat {

            transform:
                scale(1.18);

        }


        .seat-name {

            width: 82px;

            font-size: 0.61rem;

        }


        .seat-details {

            min-width: 145px;

            max-width: 190px;

        }

    }


    /* =========================================================
   REDUCE MOTION
   ========================================================= */

    @media (prefers-reduced-motion: reduce) {

        .seat {

            animation: none;

            transition: none;

        }


        .seat-details {

            transition: none;

        }

    }
</style>


<?php
/* =========================================================
   GLOBAL SCRIPTS
   ========================================================= */

require_once "./globals/scripts.php";
?>