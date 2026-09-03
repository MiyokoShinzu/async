<?php
/* =========================================================
   STUDENT FREEDOM WALLS
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
   LOAD AVAILABLE FREEDOM WALLS
========================================================= */

$sql = "
    SELECT
        fw.id,
        fw.title,
        fw.description,
        fw.department,
        fw.year_section,
        fw.status,
        fw.created_at,

        (
            SELECT COUNT(*)
            FROM freedom_wall_posts fwp
            WHERE fwp.wall_id = fw.id
        ) AS post_count

    FROM freedom_walls fw

    WHERE fw.status = 'active'

    AND (
        fw.department IS NULL
        OR fw.department = ''
        OR fw.department = ?
    )

    AND (
        fw.year_section IS NULL
        OR fw.year_section = ''
        OR fw.year_section = ?
    )

    ORDER BY fw.created_at DESC
";


$stmt = $mysqli->prepare($sql);


if (!$stmt) {

    die("Database query failed: " .
        $mysqli->error);
}


$stmt->bind_param(
    "ss",
    $department,
    $yearSection
);


$stmt->execute();


$result = $stmt->get_result();


$walls = [];


while ($row = $result->fetch_assoc()) {

    $walls[] = $row;
}


$stmt->close();

?>


<!DOCTYPE html>

<html lang="en">


<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        Freedom Walls | ETS-Async
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


        <!-- =================================================
             RESPONSIVE PAGE CONTAINER
        ================================================== -->

        <div class="freedom-wall-container">


            <!-- =============================================
                 PAGE HEADER
            ============================================== -->

            <div class="page-header">


                <div class="page-header-content">


                    <div class="page-header-icon">

                        <i class="bi bi-chat-square-heart-fill"></i>

                    </div>


                    <div class="page-header-text">

                        <h1 class="page-title">
                            Freedom Walls
                        </h1>


                        <p class="page-subtitle">
                            Share your thoughts and see what your
                            classmates have to say.
                        </p>

                    </div>


                </div>


            </div>


            <!-- =============================================
                 WALL SECTION
            ============================================== -->

            <section class="walls-section">


                <?php if (empty($walls)): ?>


                    <!-- =====================================
                         EMPTY STATE
                    ====================================== -->

                    <div class="empty-state">


                        <div class="empty-icon">

                            <i class="bi bi-chat-square-heart"></i>

                        </div>


                        <h3>
                            No Freedom Walls Available
                        </h3>


                        <p>
                            There are currently no active Freedom
                            Walls available for your class.
                        </p>


                    </div>


                <?php else: ?>


                    <!-- =====================================
                         WALL GRID
                    ====================================== -->

                    <div class="walls-grid">


                        <?php foreach ($walls as $wall): ?>


                            <?php

                            /* =================================
                               WALL INFORMATION
                            ================================== */

                            $wallId =
                                (int)$wall["id"];


                            $title =
                                $wall["title"] ?? "";


                            $description =
                                trim(
                                    $wall["description"] ?? ""
                                );


                            $postCount =
                                (int)$wall["post_count"];


                            $wallDepartment =
                                trim(
                                    $wall["department"] ?? ""
                                );


                            $wallYearSection =
                                trim(
                                    $wall["year_section"] ?? ""
                                );


                            /* =================================
                               AUDIENCE
                            ================================== */

                            if (
                                $wallDepartment === "" &&
                                $wallYearSection === ""
                            ) {

                                $audienceLabel =
                                    "All Students";
                            } elseif (
                                $wallDepartment !== "" &&
                                $wallYearSection === ""
                            ) {

                                $audienceLabel =
                                    $wallDepartment;
                            } elseif (
                                $wallDepartment === "" &&
                                $wallYearSection !== ""
                            ) {

                                $audienceLabel =
                                    $wallYearSection;
                            } else {

                                $audienceLabel =
                                    $wallDepartment .
                                    " • " .
                                    $wallYearSection;
                            }


                            /* =================================
                               DESCRIPTION
                            ================================== */

                            if ($description === "") {

                                $description =
                                    "Share your thoughts and messages on this Freedom Wall.";
                            }


                            /* =================================
                               DATE
                            ================================== */

                            $createdTimestamp =
                                strtotime(
                                    $wall["created_at"]
                                );


                            $createdDate =
                                date(
                                    "M d, Y",
                                    $createdTimestamp
                                );

                            ?>


                            <!-- =================================
                                 WALL CARD
                            ================================== -->

                            <article class="wall-card">


                                <!-- =============================
                                     TOP ICON
                                ============================== -->

                                <div class="wall-card-icon">

                                    <i class="bi bi-chat-square-heart-fill"></i>

                                </div>


                                <!-- =============================
                                     CONTENT
                                ============================== -->

                                <div class="wall-card-content">


                                    <h2 class="wall-title">

                                        <?= e($title) ?>

                                    </h2>


                                    <p class="wall-description">

                                        <?= e($description) ?>

                                    </p>


                                    <!-- =========================
                                         META INFORMATION
                                    ========================== -->

                                    <div class="wall-meta">


                                        <div class="wall-meta-item">

                                            <i class="bi bi-people-fill"></i>

                                            <span>
                                                <?= e($audienceLabel) ?>
                                            </span>

                                        </div>


                                        <div class="wall-meta-item">

                                            <i class="bi bi-chat-left-text-fill"></i>

                                            <span>

                                                <?= $postCount ?>

                                                <?= $postCount === 1
                                                    ? "post"
                                                    : "posts"
                                                ?>

                                            </span>

                                        </div>


                                        <div class="wall-meta-item">

                                            <i class="bi bi-calendar3"></i>

                                            <span>
                                                <?= e($createdDate) ?>
                                            </span>

                                        </div>


                                    </div>


                                </div>


                                <!-- =============================
                                     FOOTER
                                ============================== -->

                                <div class="wall-card-footer">


                                    <div class="active-badge">

                                        <span class="status-dot"></span>

                                        <span>
                                            Active
                                        </span>

                                    </div>


                                    <a
                                        href="freedom_wall_agreement.php?wall_id=<?= $wallId ?>"
                                        class="view-wall-button">

                                        <span>
                                            View Wall
                                        </span>

                                        <i class="bi bi-arrow-right"></i>

                                    </a>


                                </div>


                            </article>


                        <?php endforeach; ?>


                    </div>


                <?php endif; ?>


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

        .freedom-wall-container,
        .freedom-wall-container *,
        .freedom-wall-container *::before,
        .freedom-wall-container *::after {

            box-sizing: border-box;

        }


        /* =====================================================
       RESPONSIVE PAGE CONTAINER

       IMPORTANT:
       We do NOT modify .main-content here.

       The spacing is controlled by this container so it
       does not conflict with your canonical global layout.
    ====================================================== */

        .freedom-wall-container {

            width: 100%;

            max-width: 1250px;

            margin-left: auto;
            margin-right: auto;

            /*
         * Responsive horizontal spacing.
         *
         * Desktop:
         * approximately 32px
         *
         * Mobile:
         * approximately 16px
         */

            padding-left:
                clamp(16px, 2.5vw, 32px);

            padding-right:
                clamp(16px, 2.5vw, 32px);

            /*
         * Responsive vertical spacing.
         */

            padding-top:
                clamp(20px, 3vw, 36px);

            padding-bottom:
                clamp(30px, 5vw, 60px);

        }


        /* =====================================================
       PAGE HEADER
    ====================================================== */

        .page-header {

            width: 100%;

            margin-bottom:
                clamp(20px, 3vw, 30px);

        }


        .page-header-content {

            display: flex;

            align-items: center;

            gap:
                clamp(10px, 1.5vw, 15px);

            width: 100%;

        }


        .page-header-icon {

            width:
                clamp(42px, 5vw, 50px);

            height:
                clamp(42px, 5vw, 50px);

            flex:
                0 0 clamp(42px, 5vw, 50px);

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius:
                clamp(11px, 1.5vw, 13px);

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);

            font-size:
                clamp(18px, 2.5vw, 22px);

        }


        .page-header-text {

            min-width: 0;

            flex: 1;

        }


        .page-title {

            margin: 0;

            color:
                var(--text-color);

            font-size:
                clamp(21px, 3vw, 28px);

            font-weight: 700;

            line-height: 1.25;

            overflow-wrap: anywhere;

        }


        .page-subtitle {

            margin:
                clamp(4px, 0.7vw, 7px) 0 0;

            color:
                var(--text-secondary);

            font-size:
                clamp(12px, 1.5vw, 14px);

            line-height: 1.6;

            overflow-wrap: anywhere;

        }


        /* =====================================================
       WALL SECTION
    ====================================================== */

        .walls-section {

            width: 100%;

        }


        /* =====================================================
       WALL GRID
    ====================================================== */

        .walls-grid {

            display: grid;

            width: 100%;

            /*
         * Automatically adapts based on available width.
         */

            grid-template-columns:
                repeat(auto-fit,
                    minmax(min(100%, 300px),
                        1fr));

            gap:
                clamp(14px, 2vw, 22px);

        }


        /* =====================================================
       WALL CARD
    ====================================================== */

        .wall-card {

            position: relative;

            display: flex;

            flex-direction: column;

            width: 100%;

            min-width: 0;

            min-height:
                clamp(280px, 25vw, 320px);

            padding:
                clamp(18px, 2.2vw, 25px);

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                clamp(13px, 1.5vw, 17px);

            box-shadow:
                0 4px 16px var(--shadow-color);

            overflow: hidden;

            transition:
                transform .2s ease,
                box-shadow .2s ease,
                border-color .2s ease;

        }


        /* =====================================================
       CARD TOP LINE
    ====================================================== */

        .wall-card::before {

            content: "";

            position: absolute;

            top: 0;
            left: 0;
            right: 0;

            height: 4px;

            background:
                var(--academic-blue);

        }


        /* =====================================================
       CARD HOVER
    ====================================================== */

        @media (hover: hover) {

            .wall-card:hover {

                transform:
                    translateY(-4px);

                border-color:
                    var(--academic-blue);

                box-shadow:
                    0 10px 28px var(--shadow-color);

            }

        }


        /* =====================================================
       WALL ICON
    ====================================================== */

        .wall-card-icon {

            width:
                clamp(46px, 5vw, 54px);

            height:
                clamp(46px, 5vw, 54px);

            flex:
                0 0 clamp(46px, 5vw, 54px);

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom:
                clamp(13px, 1.7vw, 18px);

            border-radius:
                clamp(11px, 1.5vw, 14px);

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);

            font-size:
                clamp(21px, 2.5vw, 25px);

        }


        /* =====================================================
       WALL CONTENT
    ====================================================== */

        .wall-card-content {

            flex: 1;

            min-width: 0;

        }


        /* =====================================================
       WALL TITLE
    ====================================================== */

        .wall-title {

            margin:
                0 0 clamp(7px, 1vw, 10px);

            color:
                var(--text-color);

            font-size:
                clamp(17px, 2vw, 20px);

            font-weight: 700;

            line-height: 1.35;

            overflow-wrap: anywhere;

            word-break: break-word;

        }


        /* =====================================================
       DESCRIPTION
    ====================================================== */

        .wall-description {

            margin: 0;

            color:
                var(--text-secondary);

            font-size:
                clamp(12px, 1.4vw, 14px);

            line-height: 1.65;

            display: -webkit-box;

            -webkit-line-clamp: 3;

            -webkit-box-orient: vertical;

            overflow: hidden;

            overflow-wrap: anywhere;

            word-break: break-word;

        }


        /* =====================================================
       META INFORMATION
    ====================================================== */

        .wall-meta {

            display: flex;

            flex-direction: column;

            gap:
                clamp(7px, 1vw, 9px);

            margin-top:
                clamp(15px, 2vw, 20px);

            width: 100%;

        }


        .wall-meta-item {

            display: flex;

            align-items: flex-start;

            gap: 8px;

            width: 100%;

            min-width: 0;

            color:
                var(--activity-muted);

            font-size:
                clamp(11px, 1.3vw, 12px);

            line-height: 1.45;

        }


        .wall-meta-item i {

            width: 16px;

            min-width: 16px;

            margin-top: 1px;

            color:
                var(--academic-blue);

            font-size: 14px;

        }


        .wall-meta-item span {

            min-width: 0;

            overflow-wrap: anywhere;

            word-break: break-word;

        }


        /* =====================================================
       CARD FOOTER
    ====================================================== */

        .wall-card-footer {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 12px;

            width: 100%;

            margin-top:
                clamp(18px, 2.5vw, 25px);

            padding-top:
                clamp(14px, 2vw, 18px);

            border-top:
                1px solid var(--activity-border-light);

        }


        /* =====================================================
       ACTIVE BADGE
    ====================================================== */

        .active-badge {

            display: inline-flex;

            align-items: center;

            justify-content: flex-start;

            gap: 6px;

            min-width: 0;

            color:
                var(--activity-completed);

            font-size:
                clamp(11px, 1.3vw, 12px);

            font-weight: 600;

            white-space: nowrap;

        }


        .status-dot {

            width: 7px;

            height: 7px;

            flex: 0 0 7px;

            border-radius: 50%;

            background:
                var(--activity-completed);

        }


        /* =====================================================
       VIEW WALL BUTTON
    ====================================================== */

        .view-wall-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            flex-shrink: 0;

            min-height: 38px;

            padding:
                9px clamp(11px, 1.5vw, 15px);

            border-radius: 9px;

            background:
                var(--academic-blue);

            color: #FFFFFF;

            text-decoration: none;

            font-size:
                clamp(12px, 1.3vw, 13px);

            font-weight: 600;

            white-space: nowrap;

            transition:
                background-color .2s ease,
                transform .2s ease;

        }


        .view-wall-button:hover {

            color: #FFFFFF;

            background:
                var(--academic-blue-dark);

        }


        @media (hover: hover) {

            .view-wall-button:hover {

                transform:
                    translateX(2px);

            }

        }


        .view-wall-button i {

            font-size: 14px;

        }


        /* =====================================================
       EMPTY STATE
    ====================================================== */

        .empty-state {

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            width: 100%;

            min-height:
                clamp(280px, 40vw, 380px);

            padding:
                clamp(25px, 5vw, 50px) clamp(18px, 5vw, 40px);

            text-align: center;

            background:
                var(--surface-color);

            border:
                1px solid var(--border-color);

            border-radius:
                clamp(13px, 1.5vw, 17px);

        }


        .empty-icon {

            width:
                clamp(60px, 8vw, 74px);

            height:
                clamp(60px, 8vw, 74px);

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom:
                clamp(14px, 2vw, 20px);

            border-radius: 50%;

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);

            font-size:
                clamp(25px, 4vw, 31px);

        }


        .empty-state h3 {

            margin:
                0 0 8px;

            color:
                var(--text-color);

            font-size:
                clamp(17px, 2.5vw, 20px);

            line-height: 1.4;

        }


        .empty-state p {

            width: 100%;

            max-width: 420px;

            margin: 0;

            color:
                var(--text-secondary);

            font-size:
                clamp(12px, 1.5vw, 14px);

            line-height: 1.6;

        }


        /* =====================================================
       TABLET
    ====================================================== */

        @media (max-width: 900px) {

            .walls-grid {

                grid-template-columns:
                    repeat(2,
                        minmax(0, 1fr));

            }

        }


        /* =====================================================
       MOBILE
    ====================================================== */

        @media (max-width: 600px) {

            .freedom-wall-container {

                /*
             * Explicit mobile spacing.
             */

                padding-left: 16px;

                padding-right: 16px;

                padding-top: 22px;

                padding-bottom: 40px;

            }


            .walls-grid {

                grid-template-columns: 1fr;

                gap: 16px;

            }


            .wall-card {

                min-height: 0;

                padding: 20px;

            }


            .wall-card-footer {

                flex-direction: column;

                align-items: stretch;

            }


            .active-badge {

                justify-content: center;

            }


            .view-wall-button {

                width: 100%;

                min-height: 42px;

            }

        }


        /* =====================================================
       SMALL MOBILE
    ====================================================== */

        @media (max-width: 400px) {

            .freedom-wall-container {

                padding-left: 14px;

                padding-right: 14px;

                padding-top: 18px;

                padding-bottom: 35px;

            }


            .page-header-content {

                align-items: flex-start;

            }


            .wall-card {

                padding: 17px;

            }


            .wall-card-footer {

                margin-top: 18px;

            }

        }


        /* =====================================================
       VERY SMALL MOBILE
    ====================================================== */

        @media (max-width: 340px) {

            .freedom-wall-container {

                padding-left: 12px;

                padding-right: 12px;

            }


            .page-header-content {

                gap: 8px;

            }


            .page-title {

                font-size: 19px;

            }


            .page-subtitle {

                font-size: 11px;

            }


            .wall-card {

                padding: 15px;

            }

        }


        /* =====================================================
       LANDSCAPE MOBILE
    ====================================================== */

        @media (max-width: 900px) and (orientation: landscape) {

            .freedom-wall-container {

                padding-top: 18px;

                padding-bottom: 30px;

            }

        }


        /* =====================================================
       REDUCED MOTION
    ====================================================== */

        @media (prefers-reduced-motion: reduce) {

            .wall-card,
            .view-wall-button {

                transition: none;

            }


            .wall-card:hover {

                transform: none;

            }


            .view-wall-button:hover {

                transform: none;

            }

        }
    </style>


    <!-- =====================================================
         GLOBAL SCRIPTS
    ====================================================== -->

    <?php require_once "./globals/scripts.php"; ?>


</body>

</html>