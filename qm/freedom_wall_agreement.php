
<?php
/* =========================================================
   STUDENT FREEDOM WALL AGREEMENT
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
   STUDENT INFORMATION
========================================================= */

$user = $_SESSION["user"];

$studentId = $user["student_id"] ?? "";
$department = trim($user["department"] ?? "");
$yearSection = trim($user["year_section"] ?? "");


/* =========================================================
   ESCAPE HELPER
========================================================= */

function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}


/* =========================================================
   GET WALL ID
========================================================= */

$wallId = isset($_GET["wall_id"])
    ? (int)$_GET["wall_id"]
    : 0;


/* =========================================================
   INVALID WALL ID
========================================================= */

if ($wallId <= 0) {
    header("Location: freedom_walls.php");
    exit;
}


/* =========================================================
   LOAD AUTHORIZED WALL
========================================================= */

$sql = "
    SELECT
        id,
        title,
        description,
        department,
        year_section,
        status

    FROM freedom_walls

    WHERE id = ?

    AND status = 'active'

    AND (
        department IS NULL
        OR department = ''
        OR department = ?
    )

    AND (
        year_section IS NULL
        OR year_section = ''
        OR year_section = ?
    )

    LIMIT 1
";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Database query failed: " . $mysqli->error);
}

$stmt->bind_param(
    "iss",
    $wallId,
    $department,
    $yearSection
);

$stmt->execute();

$result = $stmt->get_result();

$wall = $result->fetch_assoc();

$stmt->close();


/* =========================================================
   WALL NOT AVAILABLE
========================================================= */

if (!$wall) {
    header("Location: freedom_walls.php");
    exit;
}


/* =========================================================
   WALL INFORMATION
========================================================= */

$wallTitle = $wall["title"] ?? "Freedom Wall";

$wallDescription = trim(
    $wall["description"] ?? ""
);

if ($wallDescription === "") {
    $wallDescription =
        "Share your thoughts and messages on this Freedom Wall.";
}


/* =========================================================
   CHECK EXISTING AGREEMENT
========================================================= */

$disclaimerVersion = "1.0";

$agreementExists = false;

$agreementSql = "
    SELECT id

    FROM freedom_wall_agreements

    WHERE wall_id = ?
    AND student_id = ?

    LIMIT 1
";

$agreementStmt = $mysqli->prepare($agreementSql);

if ($agreementStmt) {

    $agreementStmt->bind_param(
        "is",
        $wallId,
        $studentId
    );

    $agreementStmt->execute();

    $agreementResult =
        $agreementStmt->get_result();

    if ($agreementResult->num_rows > 0) {
        $agreementExists = true;
    }

    $agreementStmt->close();
}


/* =========================================================
   IF ALREADY AGREED
   DIRECTLY OPEN THE WALL
========================================================= */

if ($agreementExists) {

    header(
        "Location: freedom_wall.php?wall_id=" .
            $wallId
    );

    exit;
}


/* =========================================================
   PROCESS AGREEMENT
========================================================= */

$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $submittedWallId =
        isset($_POST["wall_id"])
        ? (int)$_POST["wall_id"]
        : 0;

    $agree =
        isset($_POST["agree"])
        ? $_POST["agree"]
        : "";


    /* =====================================================
       VERIFY WALL ID
    ====================================================== */

    if ($submittedWallId !== $wallId) {

        $errorMessage =
            "Invalid Freedom Wall.";
    } elseif ($agree !== "yes") {

        $errorMessage =
            "You must agree to the disclaimer before continuing.";
    } else {


        /* =================================================
           SAVE AGREEMENT
        ================================================== */

        $insertSql = "
            INSERT INTO freedom_wall_agreements
            (
                wall_id,
                student_id,
                disclaimer_version
            )

            VALUES
            (
                ?,
                ?,
                ?
            )

            ON DUPLICATE KEY UPDATE
                disclaimer_version = VALUES(disclaimer_version),
                agreed_at = CURRENT_TIMESTAMP
        ";

        $insertStmt =
            $mysqli->prepare($insertSql);


        if (!$insertStmt) {

            $errorMessage =
                "Unable to save your agreement. Please try again.";
        } else {

            $insertStmt->bind_param(
                "iss",
                $wallId,
                $studentId,
                $disclaimerVersion
            );


            if ($insertStmt->execute()) {

                $insertStmt->close();


                /* =========================================
                   AGREEMENT SUCCESSFUL
                ========================================== */

                header(
                    "Location: freedom_wall.php?wall_id=" .
                        $wallId
                );

                exit;
            } else {

                $errorMessage =
                    "Unable to save your agreement. Please try again.";

                $insertStmt->close();
            }
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">


<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        Freedom Wall Guidelines | ETS-Async
    </title>


    <!-- =====================================================
         APPLY SAVED THEME BEFORE PAGE LOAD
    ====================================================== -->

    <script>
        (function() {

            const savedTheme =
                localStorage.getItem("ets-theme");

            document.documentElement.setAttribute(
                "data-theme",
                savedTheme === "dark" ?
                "dark" :
                "light"
            );

        })();
    </script>


    <!-- =====================================================
         GLOBAL HEAD
    ====================================================== -->

    <?php include "globals/head.php"; ?>


</head>


<body>


    <!-- =====================================================
         GLOBAL SIDEBAR
    ====================================================== -->

    <?php include "globals/sidebar.php"; ?>


    <!-- =====================================================
         GLOBAL TOPBAR
    ====================================================== -->

    <?php include "globals/topbar.php"; ?>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">


        <div class="agreement-container">


            <!-- =================================================
                 AGREEMENT CARD
            ================================================== -->

            <section class="agreement-card">


                <!-- =============================================
                     HEADER
                ============================================== -->

                <div class="agreement-header">


                    <div class="agreement-icon">

                        <i class="bi bi-shield-check"></i>

                    </div>


                    <div class="agreement-header-text">

                        <div class="agreement-label">

                            FREEDOM WALL

                        </div>


                        <h1>

                            Before You Enter

                        </h1>


                        <p>

                            Please read and agree to the guidelines
                            before accessing this Freedom Wall.

                        </p>

                    </div>


                </div>


                <!-- =================================================
                     WALL INFORMATION
                ================================================== -->

                <div class="wall-information">


                    <div class="wall-information-icon">

                        <i class="bi bi-chat-square-heart-fill"></i>

                    </div>


                    <div>

                        <h2>

                            <?= e($wallTitle) ?>

                        </h2>


                        <p>

                            <?= e($wallDescription) ?>

                        </p>

                    </div>


                </div>


                <!-- =================================================
                     ERROR MESSAGE
                ================================================== -->

                <?php if ($errorMessage !== ""): ?>

                    <div class="agreement-error">

                        <i class="bi bi-exclamation-circle-fill"></i>

                        <span>

                            <?= e($errorMessage) ?>

                        </span>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     DISCLAIMER
                ================================================== -->

                <div class="disclaimer-content">


                    <h2>

                        Freedom Wall Disclaimer and Guidelines

                    </h2>


                    <p>

                        The Freedom Wall is a feature of the
                        <strong>asynchronous learning environment</strong>
                        intended primarily for
                        <strong>entertainment, lighthearted interaction,
                            creativity, and casual student expression</strong>.
                        It provides an informal space where students may
                        share jokes, reactions, observations, motivational
                        messages, and other appropriate content related
                        to their learning experience.

                    </p>


                    <h3>

                        1. Respectful and Responsible Communication

                    </h3>


                    <p>

                        Students are expected to practice respectful
                        communication and responsible digital citizenship
                        when using the Freedom Wall. All contributions
                        should be appropriate for an academic environment
                        and should respect the dignity, privacy, and rights
                        of others.

                    </p>


                    <p>

                        Do not use the Freedom Wall to:

                    </p>


                    <ul>

                        <li>
                            Target, insult, ridicule, embarrass, threaten,
                            or personally attack anyone.
                        </li>

                        <li>
                            Specifically target or mock an instructor,
                            professor, faculty member, staff member,
                            or administrator.
                        </li>

                        <li>
                            Harass, bully, discriminate against, or
                            intimidate another person.
                        </li>

                        <li>
                            Spread rumors, false information, accusations,
                            or misleading statements about another person.
                        </li>

                        <li>
                            Create content intended to provoke conflict
                            or disrupt the learning environment.
                        </li>

                        <li>
                            Share private, confidential, or sensitive
                            information about yourself or another person.
                        </li>

                        <li>
                            Post threatening, sexually explicit,
                            excessively offensive, defamatory, abusive,
                            or harassing content.
                        </li>

                    </ul>


                    <h3>

                        2. Entertainment and Lighthearted Expression

                    </h3>


                    <p>

                        The Freedom Wall is intended to be
                        <strong>fun and recreational</strong>.
                        Posts should generally be understood as casual
                        and lighthearted expressions and should not be
                        interpreted as official statements, academic
                        instructions, or formal institutional positions.

                    </p>


                    <p>

                        Humor is welcome, but humor should not come at the
                        expense of another person's dignity, privacy, or
                        well-being. If a joke or message could reasonably
                        be understood as targeting or harming another
                        individual, it should not be posted.

                    </p>


                    <h3>

                        3. Respect for Instructors and the Learning Environment

                    </h3>


                    <p>

                        The Freedom Wall must not be used as a means of
                        publicly criticizing, attacking, insulting, or
                        targeting an instructor or other academic personnel.

                    </p>


                    <p>

                        Students may have different opinions, experiences,
                        and perspectives regarding their coursework.
                        Concerns involving instructors, course requirements,
                        grades, teaching practices, deadlines, or academic
                        policies should be addressed through appropriate
                        and respectful academic communication channels.

                    </p>


                    <p>

                        The purpose of the Freedom Wall is to complement
                        the asynchronous learning experience—not to create
                        conflict or interfere with the student-instructor
                        relationship.

                    </p>


                    <h3>

                        4. Academic Integrity

                    </h3>


                    <p>

                        The Freedom Wall is not intended to facilitate
                        academic dishonesty. Students should not use it to:

                    </p>


                    <ul>

                        <li>
                            Share answers to active examinations, quizzes,
                            or graded assessments.
                        </li>

                        <li>
                            Distribute unauthorized examination materials.
                        </li>

                        <li>
                            Request or provide assistance intended to
                            circumvent academic requirements.
                        </li>

                        <li>
                            Impersonate another student, instructor,
                            or school personnel.
                        </li>

                        <li>
                            Misrepresent another person's work or ideas
                            as their own.
                        </li>

                    </ul>


                    <h3>

                        5. Privacy and Personal Information

                    </h3>


                    <p>

                        Students should exercise caution when posting
                        information online. Do not post personal,
                        confidential, or sensitive information belonging
                        to yourself or another person.

                    </p>


                    <h3>

                        6. Individual Responsibility

                    </h3>


                    <p>

                        Every post represents the responsibility of the
                        individual who submitted it. Students are expected
                        to consider the possible impact of their words
                        before posting.

                    </p>


                    <p>

                        Although posts may appear anonymous to other
                        students, anonymous participation does not remove
                        the responsibility of the person who created the
                        post.

                    </p>


                    <h3>

                        7. Moderation

                    </h3>


                    <p>

                        The Freedom Wall is subject to reasonable
                        moderation. Authorized administrators may review,
                        remove, or restrict content that violates these
                        guidelines or applicable institutional policies.

                    </p>


                    <p>

                        Inappropriate content may be removed, and posting
                        privileges may be restricted when necessary to
                        maintain a safe, respectful, and productive
                        learning environment.

                    </p>


                    <h3>

                        8. No Official Representation

                    </h3>


                    <p>

                        Posts are the opinions or expressions of the
                        individual users who submit them. They do not
                        necessarily represent the views, opinions,
                        policies, or official position of the instructor,
                        ETS-Async Learning Portal, academic department,
                        or institution.

                    </p>


                    <h3>

                        9. Purpose of the Freedom Wall

                    </h3>


                    <p>

                        The Freedom Wall exists to add a
                        <strong>social, recreational, and human element
                            to asynchronous learning</strong>.
                        Students are encouraged to use it to share
                        positive experiences, humor, encouragement,
                        relatable academic moments, and creative
                        expressions that contribute to a friendly
                        learning community.

                    </p>


                    <!-- =================================================
                         FINAL REMINDER
                    ================================================== -->

                    <div class="final-reminder">

                        <i class="bi bi-heart-fill"></i>

                        <div>

                            <strong>

                                Keep it fun. Keep it respectful.
                                Keep it appropriate.

                            </strong>

                            <p>

                                The Freedom Wall is for entertainment
                                and positive student interaction—not
                                for targeting, attacking, or humiliating
                                anyone, including instructors.

                            </p>

                        </div>

                    </div>


                </div>


                <!-- =================================================
                     AGREEMENT FORM
                ================================================== -->

                <form
                    method="POST"
                    action=""
                    id="agreementForm">


                    <input
                        type="hidden"
                        name="wall_id"
                        value="<?= $wallId ?>">


                    <!-- =============================================
                         CHECKBOX
                    ============================================== -->

                    <label class="agreement-checkbox">


                        <input
                            type="checkbox"
                            id="agreeCheckbox"
                            name="agree"
                            value="yes">


                        <span class="custom-checkbox">

                            <i class="bi bi-check"></i>

                        </span>


                        <span class="agreement-checkbox-text">

                            I have read and understood the Freedom Wall
                            Disclaimer and Guidelines. I agree to use
                            this feature responsibly, respectfully, and
                            appropriately within the asynchronous
                            learning environment.

                        </span>


                    </label>


                    <!-- =================================================
                         BUTTONS
                    ================================================== -->

                    <div class="agreement-actions">


                        <a
                            href="freedom_walls.php"
                            class="back-button">

                            <i class="bi bi-arrow-left"></i>

                            <span>
                                Go Back
                            </span>

                        </a>


                        <button
                            type="submit"
                            class="agree-button"
                            id="agreeButton"
                            disabled>

                            <span>
                                I Agree & Continue
                            </span>

                            <i class="bi bi-arrow-right"></i>

                        </button>


                    </div>


                </form>


            </section>


        </div>


    </main>


    <!-- =====================================================
         PAGE CSS
    ====================================================== -->

    <style>
        /* =====================================================
           BOX SIZING
        ====================================================== */

        .agreement-container,
        .agreement-container *,
        .agreement-container *::before,
        .agreement-container *::after {

            box-sizing: border-box;

        }


        /* =====================================================
           PAGE CONTAINER
        ====================================================== */

        .agreement-container {

            width: 100%;

            max-width: 1000px;

            margin-left: auto;
            margin-right: auto;

            padding-left:
                clamp(16px, 2.5vw, 32px);

            padding-right:
                clamp(16px, 2.5vw, 32px);

            padding-top:
                clamp(20px, 3vw, 36px);

            padding-bottom:
                clamp(30px, 5vw, 60px);

        }


        /* =====================================================
           AGREEMENT CARD
        ====================================================== */

        .agreement-card {

            width: 100%;

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                clamp(14px, 2vw, 20px);

            box-shadow:
                0 6px 25px var(--shadow-color);

            overflow: hidden;

        }


        /* =====================================================
           HEADER
        ====================================================== */

        .agreement-header {

            display: flex;

            align-items: center;

            gap: 16px;

            padding:
                clamp(22px, 3vw, 32px);

            border-bottom:
                1px solid var(--activity-border);

        }


        .agreement-icon {

            width: 58px;

            height: 58px;

            flex:
                0 0 58px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 15px;

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);

            font-size: 27px;

        }


        .agreement-header-text {

            min-width: 0;

            flex: 1;

        }


        .agreement-label {

            margin-bottom: 3px;

            color:
                var(--academic-blue);

            font-size: 11px;

            font-weight: 700;

            letter-spacing: .08em;

        }


        .agreement-header h1 {

            margin: 0;

            color:
                var(--text-color);

            font-size:
                clamp(22px, 3vw, 30px);

            font-weight: 700;

            line-height: 1.25;

        }


        .agreement-header p {

            margin:
                6px 0 0;

            color:
                var(--text-secondary);

            font-size:
                clamp(12px, 1.5vw, 14px);

            line-height: 1.6;

        }


        /* =====================================================
           WALL INFORMATION
        ====================================================== */

        .wall-information {

            display: flex;

            align-items: flex-start;

            gap: 14px;

            margin:
                clamp(18px, 3vw, 28px);

            padding:
                clamp(16px, 2.5vw, 22px);

            background:
                var(--activity-icon-blue-bg);

            border:
                1px solid var(--activity-border);

            border-radius: 13px;

        }


        .wall-information-icon {

            width: 42px;

            height: 42px;

            flex:
                0 0 42px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 11px;

            background:
                var(--surface-color);

            color:
                var(--activity-icon-blue);

        }


        .wall-information h2 {

            margin: 0 0 5px;

            color:
                var(--text-color);

            font-size:
                clamp(16px, 2vw, 19px);

            font-weight: 700;

            overflow-wrap: anywhere;

        }


        .wall-information p {

            margin: 0;

            color:
                var(--text-secondary);

            font-size:
                13px;

            line-height: 1.6;

        }


        /* =====================================================
           ERROR
        ====================================================== */

        .agreement-error {

            display: flex;

            align-items: flex-start;

            gap: 9px;

            margin:
                0 clamp(18px, 3vw, 28px) 20px;

            padding: 12px 14px;

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-inprogress);

            border:
                1px solid var(--activity-border);

            border-radius: 10px;

            font-size: 13px;

            line-height: 1.5;

        }


        /* =====================================================
           DISCLAIMER CONTENT
        ====================================================== */

        .disclaimer-content {

            padding:
                0 clamp(18px, 3vw, 32px);

            color:
                var(--text-color);

        }


        .disclaimer-content h2 {

            margin:
                0 0 18px;

            padding-bottom: 12px;

            color:
                var(--text-color);

            font-size:
                clamp(18px, 2.5vw, 22px);

            font-weight: 700;

            border-bottom:
                1px solid var(--activity-border);

        }


        .disclaimer-content h3 {

            margin:
                25px 0 8px;

            color:
                var(--text-color);

            font-size:
                clamp(14px, 1.8vw, 16px);

            font-weight: 700;

        }


        .disclaimer-content p {

            margin:
                0 0 12px;

            color:
                var(--text-secondary);

            font-size:
                clamp(12px, 1.4vw, 14px);

            line-height: 1.75;

        }


        .disclaimer-content strong {

            color:
                var(--text-color);

        }


        .disclaimer-content ul {

            margin:
                8px 0 15px;

            padding-left: 22px;

            color:
                var(--text-secondary);

        }


        .disclaimer-content li {

            margin-bottom: 8px;

            padding-left: 4px;

            font-size:
                clamp(12px, 1.4vw, 14px);

            line-height: 1.65;

        }


        /* =====================================================
           FINAL REMINDER
        ====================================================== */

        .final-reminder {

            display: flex;

            align-items: flex-start;

            gap: 12px;

            margin-top: 25px;

            padding:
                clamp(15px, 2.5vw, 20px);

            background:
                var(--activity-icon-blue-bg);

            border:
                1px solid var(--activity-border);

            border-radius: 12px;

        }


        .final-reminder>i {

            flex:
                0 0 auto;

            margin-top: 2px;

            color:
                var(--activity-icon-blue);

            font-size: 18px;

        }


        .final-reminder strong {

            display: block;

            margin-bottom: 4px;

            color:
                var(--text-color);

            font-size:
                clamp(13px, 1.6vw, 15px);

        }


        .final-reminder p {

            margin: 0;

            font-size:
                clamp(12px, 1.4vw, 13px);

        }


        /* =====================================================
           AGREEMENT FORM
        ====================================================== */

        #agreementForm {

            margin-top: 28px;

            padding:
                clamp(18px, 3vw, 32px);

            border-top:
                1px solid var(--activity-border);

        }


        /* =====================================================
           AGREEMENT CHECKBOX
        ====================================================== */

        .agreement-checkbox {

            display: flex;

            align-items: flex-start;

            gap: 12px;

            cursor: pointer;

            user-select: none;

        }


        .agreement-checkbox input {

            position: absolute;

            opacity: 0;

            pointer-events: none;

        }


        .custom-checkbox {

            width: 22px;

            height: 22px;

            flex:
                0 0 22px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-top: 1px;

            border:
                2px solid var(--border-color);

            border-radius: 6px;

            background:
                var(--input-bg);

            color: #FFFFFF;

            transition:
                background-color .2s ease,
                border-color .2s ease;

        }


        .custom-checkbox i {

            display: none;

            font-size: 14px;

        }


        .agreement-checkbox input:checked+.custom-checkbox {

            background:
                var(--academic-blue);

            border-color:
                var(--academic-blue);

        }


        .agreement-checkbox input:checked+.custom-checkbox i {

            display: block;

        }


        .agreement-checkbox-text {

            color:
                var(--text-secondary);

            font-size:
                clamp(12px, 1.5vw, 14px);

            line-height: 1.65;

        }


        /* =====================================================
           ACTIONS
        ====================================================== */

        .agreement-actions {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 12px;

            margin-top: 22px;

        }


        .back-button,
        .agree-button {

            min-height: 44px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            padding:
                10px 17px;

            border-radius: 9px;

            font-size: 13px;

            font-weight: 600;

            text-decoration: none;

            transition:
                background-color .2s ease,
                color .2s ease,
                opacity .2s ease,
                transform .2s ease;

        }


        .back-button {

            background:
                var(--surface-secondary);

            border:
                1px solid var(--border-color);

            color:
                var(--text-secondary);

        }


        .back-button:hover {

            color:
                var(--text-color);

            background:
                var(--activity-hover-bg);

        }


        .agree-button {

            border: 0;

            background:
                var(--academic-blue);

            color: #FFFFFF;

            cursor: pointer;

        }


        .agree-button:hover:not(:disabled) {

            background:
                var(--academic-blue-dark);

            transform:
                translateX(2px);

        }


        .agree-button:disabled {

            opacity: .45;

            cursor: not-allowed;

            transform: none;

        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media (max-width: 600px) {

            .agreement-header {

                align-items: flex-start;

                padding: 20px;

            }


            .agreement-icon {

                width: 48px;

                height: 48px;

                flex-basis: 48px;

                border-radius: 12px;

                font-size: 22px;

            }


            .wall-information {

                margin:
                    18px 16px;

            }


            .disclaimer-content {

                padding:
                    0 16px;

            }


            #agreementForm {

                padding:
                    20px 16px;

            }


            .agreement-actions {

                flex-direction: column-reverse;

                align-items: stretch;

            }


            .back-button,
            .agree-button {

                width: 100%;

            }

        }


        /* =====================================================
           REDUCED MOTION
        ====================================================== */

        @media (prefers-reduced-motion: reduce) {

            .back-button,
            .agree-button,
            .custom-checkbox {

                transition: none;

            }

        }
    </style>


    <!-- =====================================================
         AGREEMENT JAVASCRIPT
    ====================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {

                const checkbox =
                    document.getElementById(
                        "agreeCheckbox"
                    );

                const button =
                    document.getElementById(
                        "agreeButton"
                    );


                if (!checkbox || !button) {
                    return;
                }


                checkbox.addEventListener(
                    "change",
                    function() {

                        button.disabled = !checkbox.checked;

                    }
                );

            }
        );
    </script>


    <!-- =====================================================
         GLOBAL SCRIPTS
    ====================================================== -->

    <?php require_once "./globals/scripts.php"; ?>


</body>

</html>
