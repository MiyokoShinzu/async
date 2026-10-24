<?php
/* =========================================================
   QUIZ MASTER WELCOME PAGE — ETS-Async Learning Portal
   A simple landing page for the logged-in Quiz Master.
========================================================= */


include './globals/auth.php';

$user = $_SESSION['user'] ?? [];
if (strtolower(trim((string)($user['access'] ?? ''))) !== 'quiz_master') {
    http_response_code(403);
    exit('Quiz Master access required.');
}

$firstName = trim((string)($user['first_name'] ?? ''));
$displayName = $firstName !== '' ? $firstName : 'Quiz Master';
function qmd_e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

include 'globals/head.php';
?>
<style>
    .qm-welcome-page {
        min-height: calc(100vh - 140px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 28px 0 48px
    }

    .qm-welcome-card {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        width: 100%;
        max-width: 920px;
        padding: clamp(30px, 6vw, 72px) clamp(22px, 5vw, 64px);
        text-align: center;
        border: 1px solid var(--activity-border, #e5e7eb);
        border-radius: 24px;
        background: var(--activity-card-bg, #fff);
        box-shadow: 0 14px 40px var(--shadow-color, rgba(15, 23, 42, .07))
    }

    .qm-welcome-card:before,
    .qm-welcome-card:after {
        content: "";
        position: absolute;
        z-index: -1;
        border-radius: 50%;
        pointer-events: none
    }

    .qm-welcome-card:before {
        width: 260px;
        height: 260px;
        top: -150px;
        right: -90px;
        background: var(--activity-icon-blue-bg, #eaf2ff);
        opacity: .9
    }

    .qm-welcome-card:after {
        width: 220px;
        height: 220px;
        bottom: -145px;
        left: -80px;
        background: rgba(99, 102, 241, .08)
    }

    .qm-welcome-icon {
        display: grid;
        place-items: center;
        width: 82px;
        height: 82px;
        margin: 0 auto 25px;
        border-radius: 24px;
        background: var(--activity-icon-blue-bg, #eaf2ff);
        color: var(--activity-indigo, #4f46e5);
        font-size: 36px;
        box-shadow: 0 8px 22px rgba(79, 70, 229, .10)
    }

    .qm-welcome-eyebrow {
        margin: 0 0 12px;
        color: var(--activity-indigo, #4f46e5);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .16em;
        text-transform: uppercase
    }

    .qm-welcome-title {
        margin: 0;
        color: var(--academic-blue-dark, #1f2937);
        font-size: clamp(28px, 4.5vw, 44px);
        font-weight: 800;
        letter-spacing: -.035em;
        line-height: 1.15
    }

    .qm-welcome-name {
        color: var(--activity-indigo, #4f46e5)
    }

    .qm-welcome-message {
        max-width: 590px;
        margin: 18px auto 0;
        color: var(--text-secondary, #6b7280);
        font-size: clamp(14px, 2vw, 16px);
        line-height: 1.8
    }

    .qm-welcome-divider {
        width: 64px;
        height: 4px;
        margin: 28px auto 0;
        border-radius: 99px;
        background: var(--activity-indigo, #4f46e5);
        opacity: .8
    }

    .qm-welcome-note {
        margin: 20px 0 0;
        color: var(--text-secondary, #6b7280);
        font-size: 13px
    }

    html[data-theme="dark"] .qm-welcome-card {
        background: #111827;
        border-color: #374151
    }

    html[data-theme="dark"] .qm-welcome-title {
        color: #f3f4f6
    }

    html[data-theme="dark"] .qm-welcome-message,
    html[data-theme="dark"] .qm-welcome-note {
        color: #9ca3af
    }

    @media(max-width:575px) {
        .qm-welcome-page {
            min-height: calc(100vh - 120px);
            padding: 18px 0 30px
        }

        .qm-welcome-card {
            border-radius: 18px
        }

        .qm-welcome-icon {
            width: 68px;
            height: 68px;
            border-radius: 20px;
            font-size: 30px;
            margin-bottom: 21px
        }
    }

    @media(prefers-reduced-motion:no-preference) {
        .qm-welcome-card {
            animation: qmWelcomeIn .55s ease-out both
        }

        .qm-welcome-icon {
            animation: qmIconIn .65s .1s ease-out both
        }

        @keyframes qmWelcomeIn {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes qmIconIn {
            from {
                opacity: 0;
                transform: scale(.9)
            }

            to {
                opacity: 1;
                transform: scale(1)
            }
        }
    }
</style>

<body>
    <?php include 'globals/sidebar.php'; ?>

    <?php include 'globals/topbar.php'; ?>

    <main class="main-content">
        <div class="content-wrapper qm-welcome-page">
            <section class="qm-welcome-card" aria-labelledby="qmWelcomeTitle">
                <div class="qm-welcome-icon" aria-hidden="true"><i class="bi bi-mortarboard-fill"></i></div>
                <p class="qm-welcome-eyebrow">ETS-Async Learning Portal</p>
                <h1 class="qm-welcome-title" id="qmWelcomeTitle">Welcome back, <span class="qm-welcome-name"><?= qmd_e($displayName) ?></span>!</h1>
                <p class="qm-welcome-message">Welcome to your Quiz Master workspace. Thank you for helping learners build their knowledge, practice their skills, and achieve their academic goals.</p>
                <div class="qm-welcome-divider" aria-hidden="true"></div>
                <p class="qm-welcome-note">Ready when you are. Choose a page from the sidebar to get started.</p>
            </section>
        </div>
    </main>
    <?php include 'globals/scripts.php'; ?>
</body>