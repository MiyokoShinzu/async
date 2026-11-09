<?php

/* =========================================================
   ACADEMIC POST ACCESS START
   ETS-Async Learning Portal

   ---------------------------------------------------------
   PURPOSE:
   - Records every click/open of an Academic Post
   - Creates a unique access token
   - Records the opening time
   - Displays a 10-second preparation screen
   - Shows a Continue button when the page is ready
   - Redirects the student to the actual PHP page on click
========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION
========================================================== */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {
    http_response_code(403);
    exit("Unauthorized.");
}


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   GET STUDENT INFORMATION
========================================================== */

$user = $_SESSION["user"];
$studentId = $user["student_id"] ?? null;


/* =========================================================
   GET POST ID
========================================================== */

$postId = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$postId) {
    http_response_code(400);
    exit("Invalid academic post.");
}


/* =========================================================
   GET ACADEMIC POST
========================================================== */

$sql = "
    SELECT
        id,
        url,
        status
    FROM academic_posts
    WHERE id = ?
      AND status = 1
    LIMIT 1
";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    exit("Database error.");
}

$stmt->bind_param("i", $postId);
$stmt->execute();

$result = $stmt->get_result();
$post = $result->fetch_assoc();

$stmt->close();


/* =========================================================
   CHECK POST
========================================================== */

if (!$post) {
    http_response_code(404);
    exit("Academic post not found.");
}


/* =========================================================
   GENERATE ACCESS TOKEN
========================================================== */

$accessToken = bin2hex(random_bytes(32));


/* =========================================================
   CURRENT TIME
========================================================== */

$openedAt = date("Y-m-d H:i:s");


/* =========================================================
   IP ADDRESS
========================================================== */

$ipAddress = $_SERVER["REMOTE_ADDR"] ?? null;


/* =========================================================
   USER AGENT
========================================================== */

$userAgent = $_SERVER["HTTP_USER_AGENT"] ?? null;


/* =========================================================
   INSERT ACCESS RECORD
========================================================== */

$sql = "
    INSERT INTO academic_post_access_logs (
        post_id,
        student_id,
        opened_at,
        status,
        access_token,
        ip_address,
        user_agent
    )
    VALUES (?, ?, ?, 'open', ?, ?, ?)
";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    exit("Unable to create access record.");
}

$stmt->bind_param(
    "isssss",
    $postId,
    $studentId,
    $openedAt,
    $accessToken,
    $ipAddress,
    $userAgent
);

if (!$stmt->execute()) {
    $stmt->close();
    http_response_code(500);
    exit("Unable to record academic post access.");
}

$stmt->close();


/* =========================================================
   BUILD DESTINATION URL
========================================================== */

$page = $post["url"];

$separator = (strpos($page, "?") !== false) ? "&" : "?";

$redirectUrl =
    $page .
    $separator .
    "academic_access=" .
    urlencode($accessToken);

/*
 * Encode the destination safely for JavaScript.
 * The database URL and generated access token are retained.
 */
$redirectUrlJson = json_encode(
    $redirectUrl,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
);

if ($redirectUrlJson === false) {
    http_response_code(500);
    exit("Unable to prepare academic post destination.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Preparing Academic Post | ASYNC</title>
    <script>
        (function() {
            try {
                var savedTheme = localStorage.getItem("ets-theme");
                if (savedTheme === "dark" || savedTheme === "light") {
                    document.documentElement.setAttribute("data-theme", savedTheme);
                } else {
                    document.documentElement.setAttribute("data-theme", "light");
                }
            } catch (e) {
                document.documentElement.setAttribute("data-theme", "light");
            }
        })();
    </script>

    <style>
        :root {
            color-scheme: light;
            --async-bg: #f3f7ff;
            --async-card: rgba(255, 255, 255, .92);
            --async-text: #172b4d;
            --async-muted: #687b98;
            --async-line: rgba(39, 105, 190, .13);
            --async-blue: #1769d2;
            --async-cyan: #36b9ed;
            --async-shadow: 0 24px 70px rgba(31, 75, 132, .14);
            --async-ad-bg: rgba(255, 255, 255, .88);
            --async-ad-border: rgba(39, 105, 190, .16);
            --async-ad-shadow: 0 8px 24px rgba(31, 75, 132, .08);
            --async-ad-label: #71819a;
            --async-ad-divider: rgba(39, 105, 190, .12);
        }

        html[data-theme="dark"] {
            color-scheme: dark;
            --async-bg: #071426;
            --async-card: rgba(13, 31, 55, .82);
            --async-text: #edf5ff;
            --async-muted: #a7bad3;
            --async-line: rgba(110, 174, 245, .22);
            --async-blue: #62a9ff;
            --async-cyan: #45d3f2;
            --async-shadow: 0 24px 70px rgba(0, 0, 0, .42);
            --async-ad-bg: rgba(13, 31, 55, .82);
            --async-ad-border: rgba(110, 174, 245, .24);
            --async-ad-shadow: 0 12px 32px rgba(0, 0, 0, .30), inset 0 1px 0 rgba(255, 255, 255, .05);
            --async-ad-label: #b8cce5;
            --async-ad-divider: rgba(125, 177, 232, .20);
        }

        html[data-theme="dark"] body {
            background:
                radial-gradient(circle at 12% 16%, rgba(42, 128, 255, .18), transparent 32%),
                radial-gradient(circle at 88% 82%, rgba(49, 194, 230, .13), transparent 30%),
                var(--async-bg);
        }

        html[data-theme="dark"] .async-loader-card {
            background: rgba(11, 28, 51, .76);
            border-color: rgba(117, 180, 248, .24);
            box-shadow: 0 24px 70px rgba(0, 0, 0, .42), inset 0 1px 0 rgba(255, 255, 255, .07);
        }

        html[data-theme="dark"] .async-step-indicator {
            background: rgba(35, 68, 105, .72);
            border-color: rgba(125, 177, 232, .35);
        }

        html[data-theme="dark"] .async-loader-progress-track {
            background: rgba(137, 170, 207, .2);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            min-height: 100svh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 24px;
            overflow-x: hidden;
            overflow-y: auto;
            color: var(--async-text);
            background:
                radial-gradient(circle at 12% 16%, rgba(42, 128, 255, .12), transparent 32%),
                radial-gradient(circle at 88% 82%, rgba(49, 194, 230, .12), transparent 30%),
                var(--async-bg);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .async-page-shell {
            width: min(100%, 1500px);
            min-width: 0;
            min-height: calc(100svh - 48px);
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 300px) minmax(320px, 440px) minmax(0, 468px);
            grid-template-areas:
                "ad-left dialog ad-right"
                "ad-bottom ad-bottom ad-bottom";
            align-items: center;
            justify-content: center;
            gap: clamp(12px, 1.6vw, 24px);
            padding: 0;
        }

        /* Every ad gets a fluid white frame. The frame grows to fit its content
           while preventing fixed-width creatives from breaking the page. */
        .async-ad-box {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            gap: 10px;
            width: 100%;
            min-width: 0;
            min-height: 120px;
            padding: 12px;
            overflow: hidden;
            background: var(--async-ad-bg);
            border: 1px solid var(--async-ad-border);
            border-radius: 16px;
            box-shadow: var(--async-ad-shadow);
            backdrop-filter: blur(18px) saturate(135%);
            -webkit-backdrop-filter: blur(18px) saturate(135%);
        }

        .async-ad-box--left {
            grid-area: ad-left;
        }

        .async-ad-slot--small,
        .async-ad-slot--large {
            width: 100%;
        }

        .async-ad-label {
            align-self: flex-start;
            color: var(--async-ad-label);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            line-height: 1;
        }

        .async-ad-box script,
        .async-ad-box img,
        .async-ad-box iframe,
        .async-ad-box video {
            max-width: 100% !important;
        }

        .async-ad-label {
            display: block;
            width: 100%;
            padding-bottom: 7px;
            border-bottom: 1px solid var(--async-ad-divider);
        }

        .async-ad-frame-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-width: 0;
            min-height: 60px;
            overflow-x: auto;
            overflow-y: hidden;
            overscroll-behavior-x: contain;
            -webkit-overflow-scrolling: touch;
        }

        .async-ad-frame-wrap>*,
        .async-ad-box>*:not(.async-ad-label) {
            max-width: 100%;
        }

        .async-ad-frame-wrap iframe {
            display: block;
            max-width: 100% !important;
            border: 0;
        }

        /* A centered dialog card, not a full-screen modal/backdrop. */
        .async-loader-card {
            min-width: 0;
            position: relative;
            width: min(440px, 100%);
            max-height: calc(100svh - 32px);
            overflow-y: auto;
            padding: 34px 30px 29px;
            overflow: hidden;
            text-align: center;
            border: 1px solid rgba(23, 105, 210, .22);
            border-radius: 25px;
            background: linear-gradient(145deg, rgba(255, 255, 255, .78), rgba(225, 242, 255, .66));
            box-shadow: 0 24px 70px rgba(23, 105, 210, .16), inset 0 1px 0 rgba(255, 255, 255, .9);
            backdrop-filter: blur(24px) saturate(145%);
            -webkit-backdrop-filter: blur(24px) saturate(145%);
        }

        .async-loader-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: -45%;
            width: 42%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #39baf0, #246fe0, transparent);
            animation: async-top-sweep 3.8s ease-in-out infinite;
        }

        .async-loader-logo {
            width: 68px;
            height: 68px;
            display: grid;
            place-items: center;
            margin: 0 auto 21px;
            border-radius: 21px;
            color: #fff;
            font-size: 27px;
            font-weight: 900;
            letter-spacing: -1.5px;
            background: linear-gradient(145deg, #3289f4, #1455b5);
            box-shadow: 0 10px 28px rgba(31, 111, 215, .27), inset 0 1px 0 rgba(255, 255, 255, .35);
            animation: async-logo-float 3s ease-in-out infinite;
            position: relative;
        }

        .async-loader-logo::after {
            content: "";
            position: absolute;
            inset: -7px;
            border: 2px solid rgba(45, 143, 238, .24);
            border-top-color: #2b83e8;
            border-right-color: #38b8e9;
            border-radius: 25px;
            animation: async-spin 1.5s linear infinite;
            pointer-events: none;
        }

        .async-loader-title {
            margin: 0;
            color: var(--async-text);
            font-size: clamp(22px, 5vw, 27px);
            line-height: 1.2;
            font-weight: 850;
            letter-spacing: -.6px;
        }

        .async-loader-subtitle {
            margin: 10px 0 27px;
            color: var(--async-muted);
            font-size: 13.5px;
            line-height: 1.65;
        }

        .async-loader-steps {
            display: grid;
            gap: 13px;
            margin: 0 0 24px;
            text-align: left;
        }

        .async-loader-step {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #8291a8;
            font-size: 13px;
            transition: color .3s ease, transform .3s ease;
        }

        .async-loader-step.is-active {
            color: var(--async-blue);
            transform: translateX(3px);
            font-weight: 700;
        }

        .async-loader-step.is-complete {
            color: #16865d;
        }

        /* Pending steps show an empty marker; the active step becomes a spinner;
           only completed steps reveal the check mark. */
        .async-step-indicator {
            width: 21px;
            height: 21px;
            flex: 0 0 21px;
            display: grid;
            place-items: center;
            border: 1px solid #d7e1ef;
            border-radius: 50%;
            color: transparent;
            font-size: 11px;
            font-weight: 900;
            background: rgba(248, 250, 255, .82);
            transition: background .25s ease, border-color .25s ease, color .25s ease;
        }

        .is-active .async-step-indicator {
            border: 2px solid rgba(23, 105, 210, .24);
            border-top-color: var(--async-blue);
            border-right-color: var(--async-cyan);
            color: transparent;
            background: rgba(54, 185, 237, .10);
            animation: async-spin .8s linear infinite;
        }

        .is-complete .async-step-indicator {
            border: 1px solid #b8ead6;
            color: #16865d;
            background: #e9fbf3;
            animation: async-check-pop .35s ease both;
        }

        .async-loader-progress-track {
            height: 7px;
            overflow: hidden;
            border-radius: 99px;
            background: #e7eef8;
        }

        .async-loader-progress {
            width: 0;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, var(--async-blue), var(--async-cyan));
            box-shadow: 0 0 13px rgba(44, 153, 231, .4);
            transition: width .15s linear;
        }

        .async-loader-status {
            min-height: 20px;
            margin: 11px 0 0;
            color: var(--async-muted);
            font-size: 11.5px;
        }

        .async-loader-button {
            display: none;
            width: 100%;
            min-height: 49px;
            margin-top: 23px;
            padding: 12px 20px;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border: 0;
            border-radius: 13px;
            color: #fff;
            background: linear-gradient(110deg, #267ce5, #1255b9);
            box-shadow: 0 9px 23px rgba(26, 100, 197, .24);
            font: inherit;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .async-loader-button.is-visible {
            display: inline-flex;
            animation: async-button-appear .45s ease both;
        }

        .async-loader-button:hover {
            transform: translateY(-2px);
            filter: brightness(1.07);
            box-shadow: 0 13px 28px rgba(26, 100, 197, .32);
        }

        .async-loader-button:focus-visible {
            outline: 3px solid rgba(45, 143, 238, .35);
            outline-offset: 4px;
        }

        @keyframes async-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes async-logo-float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes async-top-sweep {
            0% {
                left: -45%;
                opacity: 0;
            }

            15%,
            75% {
                opacity: 1;
            }

            100% {
                left: 110%;
                opacity: 0;
            }
        }

        @keyframes async-button-appear {
            from {
                opacity: 0;
                transform: translateY(9px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes async-check-pop {
            from {
                transform: scale(.7);
            }

            to {
                transform: scale(1);
            }
        }

        @media (max-width: 1100px) {
            .async-page-shell {
                width: min(100%, 760px);
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                grid-template-areas:
                    "dialog dialog"
                    "ad-left ad-right"
                    "ad-bottom ad-bottom";
                align-content: center;
                gap: 16px;
            }

            .async-loader-card {
                grid-area: dialog;
                justify-self: center;
            }

            .async-ad-box {
                min-height: 110px;
            }
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }

            .async-page-shell {
                width: 100%;
                min-height: calc(100svh - 24px);
                grid-template-columns: minmax(0, 1fr);
                grid-template-areas:
                    "ad-small"
                    "dialog"
                    "ad-left"
                    "ad-large"
                    "ad-bottom";
                justify-items: center;
                align-content: center;
                gap: 12px;
            }

            .async-ad-slot--large {
                grid-area: ad-large;
                width: min(100%, 520px);
                justify-self: center;
            }

            .async-ad-box {
                min-height: 100px;
                padding: 10px;
                border-radius: 13px;
            }

            .async-ad-frame-wrap {
                min-height: 50px;
            }

            .async-loader-card {
                grid-area: dialog;
                justify-self: center;
                align-self: center;
                width: min(100%, 440px);
                margin-inline: auto;
                padding: 28px 20px 23px;
                border-radius: 21px;
            }

            .async-loader-subtitle {
                margin-bottom: 22px;
                font-size: 13px;
            }

            .async-loader-steps {
                gap: 12px;
            }

            .async-loader-step {
                font-size: 12px;
            }
        }

        @media (max-width: 420px) {
            .async-loader-card {
                width: min(100%, 440px);
                margin-inline: auto;
                padding: 25px 16px 21px;
            }

            .async-loader-logo {
                width: 60px;
                height: 60px;
                margin-bottom: 18px;
            }

            .async-loader-step {
                gap: 8px;
                line-height: 1.4;
            }

            .async-loader-button {
                min-height: 48px;
            }
        }

        @media (max-height: 560px) and (orientation: landscape) {
            body {
                place-items: center;
                padding-top: 16px;
                padding-bottom: 16px;
            }

            .async-page-shell {
                min-height: calc(100svh - 32px);
            }

            .async-loader-card {
                padding-top: 22px;
                padding-bottom: 20px;
            }

            .async-loader-logo {
                width: 48px;
                height: 48px;
                margin-bottom: 13px;
                border-radius: 15px;
            }

            .async-loader-subtitle {
                margin-bottom: 16px;
            }

            .async-loader-steps {
                gap: 8px;
                margin-bottom: 16px;
            }
        }

        @media (max-width: 350px) {
            body {
                padding: 8px;
            }

            .async-loader-card {
                padding: 24px 15px 20px;
            }

            .async-loader-title {
                font-size: 21px;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>

<body>
    <div class="async-page-shell">
        <aside class="async-ad-box async-ad-box--left" aria-label="Advertisement">
            <span class="async-ad-label">ADVERTISEMENT</span>
            <script>
                (function(bgj) {
                    var d = document,
                        s = d.createElement('script'),
                        l = d.currentScript || d.scripts[d.scripts.length - 1];
                    s.settings = bgj || {};
                    s.src = "\/\/juvenilechoice.com\/b\/XSVIsnd.G\/lu0\/YlWIcY\/fermF9EuGZTUplJk\/P\/TGcG2tMpD\/cj0yMNzcc\/t-N\/zWYbwZNYzHQi0nMBQj";
                    s.async = true;
                    s.referrerPolicy = 'no-referrer-when-downgrade';
                    l.parentNode.insertBefore(s, l);
                })({})
            </script>
        </aside>

        <main class="async-loader-card" role="dialog" aria-modal="false" aria-labelledby="asyncLoaderTitle">
            <div class="async-loader-logo" aria-hidden="true">A</div>

            <h1 class="async-loader-title" id="asyncLoaderTitle">Preparing your post</h1>
            <p class="async-loader-subtitle">
                ASYNC is preparing your academic content and verifying secure access.
            </p>

            <div class="async-loader-steps" aria-live="polite">
                <div class="async-loader-step is-active" data-step="0">
                    <span class="async-step-indicator" aria-hidden="true">✓</span>
                    <span>Fetching data in database</span>
                </div>
                <div class="async-loader-step" data-step="1">
                    <span class="async-step-indicator" aria-hidden="true">✓</span>
                    <span>Checking Access</span>
                </div>
                <div class="async-loader-step" data-step="2">
                    <span class="async-step-indicator" aria-hidden="true">✓</span>
                    <span>Verifying Access</span>
                </div>
                <div class="async-loader-step" data-step="3">
                    <span class="async-step-indicator" aria-hidden="true">✓</span>
                    <span>Preparing interface</span>
                </div>
                <div class="async-loader-step" data-step="4">
                    <span class="async-step-indicator" aria-hidden="true">✓</span>
                    <span>Integrating tools</span>
                </div>
                <div class="async-loader-step" data-step="5">
                    <span class="async-step-indicator" aria-hidden="true">✓</span>
                    <span>Security verification</span>
                </div>
            </div>

            <div class="async-loader-progress-track" role="progressbar"
                aria-label="Preparing academic post" aria-valuemin="0"
                aria-valuemax="100" aria-valuenow="0">
                <div class="async-loader-progress"></div>
            </div>

            <p class="async-loader-status" id="asyncLoaderStatus">Initializing secure access…</p>

            <button type="button" class="async-loader-button" id="asyncContinueButton">
                View Post <span aria-hidden="true">→</span>
            </button>
        </main>
        <aside class="async-ad-box async-ad-slot--large" aria-label="Right-side advertisement">
            <span class="async-ad-label">ADVERTISEMENT</span>
            <div class="async-ad-frame-wrap">
                <script>
                    (function(rchdn) {
                        var d = document,
                            s = d.createElement('script'),
                            l = d.currentScript || d.scripts[d.scripts.length - 1];
                        s.settings = rchdn || {};
                        s.src = "\/\/juvenilechoice.com\/b.XAVqsmdSGllC0-YDWKcP\/Deimn9lu\/ZeUPlMkXPnTjcN2yMWDEcp3\/NfDrU-trNqzxYJwJNczmc\/0DORQm";
                        s.async = true;
                        s.referrerPolicy = 'no-referrer-when-downgrade';
                        l.parentNode.insertBefore(s, l);
                    })({})
                </script>
            </div>
        </aside>
    </div>

    <!-- In-page push ad script -->
    <script>
        (function(bgj) {
            var d = document,
                s = d.createElement('script'),
                l = d.currentScript || d.scripts[d.scripts.length - 1];
            s.settings = bgj || {};
            s.src = "\/\/juvenilechoice.com\/b\/XgVfs.dcGflu0\/YjWocA\/eeFms9tuWZjUAllksPoTwcr2RMdDXc-zlOrTEcutLNpzBYMwdNSzbQjwUMXQT";
            s.async = true;
            s.referrerPolicy = 'no-referrer-when-downgrade';
            l.parentNode.insertBefore(s, l);
        })({})
    </script>

    <script>
        (function() {
            "use strict";

            // Random preparation duration: 10 to 20 seconds per visit.
            const minimumWait = Math.floor(Math.random() * 10001) + 10000;
            const startedAt = Date.now();
            const destination = <?= $redirectUrlJson ?>;

            const progressBar = document.querySelector(".async-loader-progress");
            const progressTrack = document.querySelector(".async-loader-progress-track");
            const statusText = document.getElementById("asyncLoaderStatus");
            const continueButton = document.getElementById("asyncContinueButton");
            const steps = Array.from(document.querySelectorAll(".async-loader-step"));

            const stages = [{
                    at: 0.00,
                    text: "Fetching data in database…"
                },
                {
                    at: 0.16,
                    text: "Checking Access…"
                },
                {
                    at: 0.32,
                    text: "Verifying Access…"
                },
                {
                    at: 0.48,
                    text: "Preparing interface…"
                },
                {
                    at: 0.64,
                    text: "Integrating tools…"
                },
                {
                    at: 0.80,
                    text: "Security verification…"
                }
            ];

            let ready = false;

            function updateLoader() {
                const elapsed = Date.now() - startedAt;
                const progress = Math.min(elapsed / minimumWait, 1);
                const percent = Math.round(progress * 100);

                progressBar.style.width = percent + "%";
                progressTrack.setAttribute("aria-valuenow", String(percent));

                let activeStage = 0;
                for (let i = 0; i < stages.length; i++) {
                    if (progress >= stages[i].at) activeStage = i;
                }

                steps.forEach(function(step, index) {
                    step.classList.toggle("is-complete", index < activeStage);
                    step.classList.toggle("is-active", index === activeStage && !ready);
                });

                if (elapsed < minimumWait) {
                    statusText.textContent = stages[activeStage].text + " " + percent + "%";
                    window.setTimeout(updateLoader, 100);
                    return;
                }

                ready = true;
                steps.forEach(function(step) {
                    step.classList.remove("is-active");
                    step.classList.add("is-complete");
                });

                statusText.textContent = "Page ready. 100% complete.";
                continueButton.classList.add("is-visible");
                continueButton.focus({
                    preventScroll: true
                });
            }

            continueButton.addEventListener("click", function() {
                if (!ready) return;
                continueButton.disabled = true;
                continueButton.textContent = "Opening post…";
                window.location.assign(destination);
            });

            updateLoader();
        })();
    </script>





</body>

</html>