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
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
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
            width: min(100%, 1120px);
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            grid-template-areas:
                "banner"
                "main"
                "vertical"
                "inpage"
                "referral";
            align-items: center;
            justify-items: center;
            gap: 18px;
        }

        .async-ad {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 100%;
            overflow: hidden;
            margin: 0 auto;
            padding: 25px 12px 12px;
            isolation: isolate;
            background: #fff;
            border: 1px solid rgba(35, 75, 125, .12);
            border-radius: 13px;
            box-shadow: 0 5px 18px rgba(31, 75, 132, .07);
        }

        .async-ad::before {
            content: "ADVERTISEMENT";
            position: absolute;
            top: 7px;
            left: 11px;
            color: #8794a7;
            font-size: 9px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: 1.1px;
        }

        .async-ad-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-width: 0;
            overflow: hidden;
        }

        .async-ad-inpage {
            grid-area: inpage;
            width: min(100%, 760px);
            min-height: 110px;
            justify-self: center;
        }

        .async-ad-vertical {
            grid-area: vertical;
            width: 184px;
            min-height: 340px;
            justify-self: center;
        }

        .async-ad-referral {
            grid-area: referral;
            width: min(100%, 752px);
            min-height: 120px;
            justify-self: center;
        }

        .async-ad-referral img {
            display: block;
            width: min(720px, 100%);
            height: auto;
            max-height: 90px;
            object-fit: contain;
        }

        .async-ad-vertical iframe {
            display: block;
            max-width: 100%;
            border: 0;
        }

        .async-loader-card {
            grid-area: main;
            justify-self: center;
            position: relative;
            width: min(460px, 100%);
            padding: 38px 34px 32px;
            overflow: hidden;
            text-align: center;
            border: 1px solid var(--async-line);
            border-radius: 25px;
            background: var(--async-card);
            box-shadow: var(--async-shadow);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
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
            background: #f8faff;
        }

        .is-active .async-step-indicator {
            border: 2px solid #4a9af0;
            border-top-color: transparent;
            color: transparent;
            animation: async-spin .8s linear infinite;
        }

        .is-complete .async-step-indicator {
            border-color: #b8ead6;
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

        @media (max-width: 900px) {
            .async-page-shell {
                width: min(100%, 760px);
                grid-template-columns: minmax(0, 1fr);
                grid-template-areas:
                    "banner"
                    "main"
                    "vertical"
                    "inpage"
                    "referral";
                gap: 14px;
            }

            .async-ad-banner {
                display: none;
            }

            .async-ad-mobile {
                display: flex;
                grid-area: banner;
                width: 344px;
                min-height: 90px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 12px;
            }

            .async-page-shell {
                gap: 12px;
            }

            .async-loader-card {
                width: 100%;
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

            .async-ad-mobile {
                width: min(344px, 100%);
                min-height: 90px;
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
        <div class="async-ad async-ad-banner" aria-label="Advertisement">
            <div class="async-ad-inner">
                <script>
                    var atOptions = {
                        'key': '57102c61d8db26fe4dbc2b79d6b703d6',
                        'format': 'iframe',
                        'height': 90,
                        'width': 728,
                        'params': {}
                    };
                </script>
                <script src="https://bauval.org/22/57102c61d8db26fe4dbc2b79d6b703d6"></script>
            </div>
        </div>

        <div class="async-ad async-ad-mobile" aria-label="Advertisement">
            <div class="async-ad-inner">
                <script>
                    var atOptions = {
                        'key': '67ec8671ad590ebf8365e5fb6fca1199',
                        'format': 'iframe',
                        'height': 50,
                        'width': 320,
                        'params': {}
                    };
                </script>
                <script src="https://bauval.org/22/67ec8671ad590ebf8365e5fb6fca1199"></script>
            </div>
        </div>

        <main class="async-loader-card" aria-labelledby="asyncLoaderTitle">
            <div class="async-loader-logo" aria-hidden="true">A</div>

            <h1 class="async-loader-title" id="asyncLoaderTitle">Preparing your post</h1>
            <p class="async-loader-subtitle">
                ASYNC is preparing your academic content and verifying secure access. Just a few moments.
            </p>

            <div class="async-loader-steps" aria-live="polite">
                <div class="async-loader-step is-active" data-step="0">
                    <span class="async-step-indicator">✓</span>
                    <span>Fetching data in database</span>
                </div>
                <div class="async-loader-step" data-step="1">
                    <span class="async-step-indicator">✓</span>
                    <span>Preparing interface</span>
                </div>
                <div class="async-loader-step" data-step="2">
                    <span class="async-step-indicator">✓</span>
                    <span>Integrating tools</span>
                </div>
                <div class="async-loader-step" data-step="3">
                    <span class="async-step-indicator">✓</span>
                    <span>Just a few moments</span>
                </div>
                <div class="async-loader-step" data-step="4">
                    <span class="async-step-indicator">✓</span>
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
                Page Ready <span aria-hidden="true">→</span>
            </button>
        </main>

        <section class="async-ad async-ad-inpage" aria-label="In-page advertisement">
            <div class="async-ad-inner">
                <script>
                    (function(gptwkeg) {
                        var d = document,
                            s = d.createElement('script'),
                            l = d.currentScript || d.scripts[d.scripts.length - 1];
                        s.settings = gptwkeg || {};
                        s.src = "//juvenilechoice.com/bsXvV/s.dhGdlX0qYIWlcC/qeEm/9duXZeUmlGkFP/Tzch2/MHDOQMw/OcD-EgtTNNzfY/wANLDRAK4eNIQO";
                        s.async = true;
                        s.referrerPolicy = 'no-referrer-when-downgrade';
                        l.parentNode.insertBefore(s, l);
                    })({});
                </script>
            </div>
        </section>

        <section class="async-ad async-ad-vertical" aria-label="Advertisement">
            <div class="async-ad-inner">
                <script>
                    var atOptions = {
                        'key': '981d46f3eb1343ec22581b3edf14be30',
                        'format': 'iframe',
                        'height': 300,
                        'width': 160,
                        'params': {}
                    };
                </script>
                <script src="https://bauval.org/22/981d46f3eb1343ec22581b3edf14be30"></script>
            </div>
        </section>

        <section class="async-ad async-ad-referral" aria-label="Adsterra banner">
            <div class="async-ad-inner">
                <img
                    src="https://landings-cdn.adsterratech.com/referralBanners/gif/720x90_adsterra_reff.gif"
                    alt="Adsterra referral banner"
                    loading="lazy">
            </div>
        </section>
    </div>

    <script>
        (function() {
            "use strict";

            const minimumWait = 10000;
            const startedAt = Date.now();
            const destination = <?= $redirectUrlJson ?>;

            const progressBar = document.querySelector(".async-loader-progress");
            const progressTrack = document.querySelector(".async-loader-progress-track");
            const statusText = document.getElementById("asyncLoaderStatus");
            const continueButton = document.getElementById("asyncContinueButton");
            const steps = Array.from(document.querySelectorAll(".async-loader-step"));

            const stages = [{
                    at: 0,
                    text: "Fetching data in database…"
                },
                {
                    at: 2000,
                    text: "Preparing interface…"
                },
                {
                    at: 4000,
                    text: "Integrating tools…"
                },
                {
                    at: 6000,
                    text: "Just a few moments…"
                },
                {
                    at: 8000,
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
                    if (elapsed >= stages[i].at) activeStage = i;
                }

                steps.forEach(function(step, index) {
                    step.classList.toggle("is-complete", index < activeStage);
                    step.classList.toggle("is-active", index === activeStage && !ready);
                });

                if (elapsed < minimumWait) {
                    const remaining = Math.ceil((minimumWait - elapsed) / 1000);
                    statusText.textContent = stages[activeStage].text + " " + remaining + "s";
                    window.setTimeout(updateLoader, 100);
                    return;
                }

                ready = true;
                steps.forEach(function(step) {
                    step.classList.remove("is-active");
                    step.classList.add("is-complete");
                });

                statusText.textContent = "Page ready. You can continue.";
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