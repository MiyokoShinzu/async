<?php
/* =========================================================
   STUDENT DASHBOARD — UNDER MAINTENANCE
   ETS-Async Learning Portal

   FILE STRUCTURE:
   1. Session and student authentication
   2. Shared dashboard header
   3. Sidebar and topbar
   4. Under-maintenance notice
   5. Shared dashboard scripts and styling

   This page intentionally displays only the maintenance notice.
   It does not query or modify the database.
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
   USER DISPLAY NAME
========================================================= */
$user = $_SESSION["user"];
$firstName = trim((string)(
    $user["first_name"] ??
    $user["firstname"] ??
    $user["name"] ??
    ""
));
$displayName = $firstName !== "" ? $firstName : "Student";

/* =========================================================
   GLOBAL HEAD
========================================================= */
include "globals/head.php";
?>

<body>
    <!-- SIDEBAR -->
    <?php include "globals/sidebar.php"; ?>

    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- TOPBAR -->
    <?php include "globals/topbar.php"; ?>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="content-wrapper">
            <section class="maintenance-page" aria-labelledby="maintenance-title">
                <div class="maintenance-card">
                    <div class="maintenance-icon" aria-hidden="true">
                        <i class="bi bi-tools"></i>
                    </div>

                    <span class="maintenance-label">
                        <span class="maintenance-status-dot"></span>
                        System Maintenance
                    </span>

                    <h1 id="maintenance-title">We're Under Maintenance</h1>

                    <p class="maintenance-message">
                        Hello, <?= htmlspecialchars($displayName, ENT_QUOTES, "UTF-8") ?>.
                        The ETS-Async Learning Portal is temporarily unavailable
                        while we perform maintenance and improvements.
                    </p>

                    <div class="maintenance-divider"></div>

                    <div class="maintenance-info">
                        <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                        <p>
                            Please check back later. Thank you for your patience
                            and understanding.
                        </p>
                    </div>

                    <div class="maintenance-footer">
                        <i class="bi bi-shield-check" aria-hidden="true"></i>
                        ETS-Async Learning Portal
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- GLOBAL SCRIPTS -->
    <?php require_once "./globals/scripts.php"; ?>

    <style>
        .maintenance-page {
            min-height: calc(100vh - 150px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .maintenance-card {
            width: 100%;
            max-width: 720px;
            padding: clamp(28px, 5vw, 56px);
            text-align: center;
            background: var(--activity-card-bg, #ffffff);
            border: 1px solid var(--activity-border, #e5e7eb);
            border-radius: 24px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
            animation: maintenance-appear .55s ease both;
        }

        .maintenance-icon {
            width: 88px;
            height: 88px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            border-radius: 24px;
            color: #2458c6;
            background: #eaf1ff;
            font-size: 38px;
            animation: maintenance-float 3s ease-in-out infinite;
        }

        .maintenance-label {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 8px 13px;
            margin-bottom: 18px;
            border-radius: 999px;
            color: #8a5a00;
            background: #fff4d6;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .maintenance-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e3a008;
            box-shadow: 0 0 0 4px rgba(227, 160, 8, .15);
        }

        .maintenance-card h1 {
            margin: 0 0 16px;
            color: var(--text-color, #172033);
            font-size: clamp(28px, 4vw, 42px);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -.035em;
        }

        .maintenance-message {
            max-width: 540px;
            margin: 0 auto;
            color: var(--text-secondary, #667085);
            font-size: 16px;
            line-height: 1.8;
        }

        .maintenance-divider {
            width: 72px;
            height: 4px;
            margin: 28px auto;
            border-radius: 99px;
            background: linear-gradient(90deg, #2458c6, #7c5ce7);
        }

        .maintenance-info {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            max-width: 510px;
            margin: 0 auto;
            padding: 16px 18px;
            border: 1px solid var(--activity-border, #e5e7eb);
            border-radius: 14px;
            background: var(--activity-progress-bg, #f8fafc);
            text-align: left;
        }

        .maintenance-info>i {
            margin-top: 3px;
            color: #2458c6;
            font-size: 19px;
        }

        .maintenance-info p {
            margin: 0;
            color: var(--text-secondary, #667085);
            font-size: 14px;
            line-height: 1.65;
        }

        .maintenance-footer {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 28px;
            color: var(--text-secondary, #667085);
            font-size: 12px;
            font-weight: 600;
        }

        .maintenance-footer i {
            color: #16a36a;
            font-size: 16px;
        }

        @keyframes maintenance-appear {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes maintenance-float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }

        @media (max-width: 576px) {
            .maintenance-page {
                min-height: calc(100vh - 120px);
                padding: 20px 10px;
            }

            .maintenance-card {
                border-radius: 18px;
            }

            .maintenance-info {
                padding: 14px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .maintenance-card,
            .maintenance-icon {
                animation: none;
            }
        }
    </style>
</body>

</html>