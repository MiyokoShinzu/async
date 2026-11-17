<?php
/* =========================================================
   FS - FULL SURPRISE
   ETS-Async student page | Rickroll edition
   Based on the existing LF page layout and shared includes.
   No database query required.
   ========================================================= */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* Match the authentication structure used by the student portal. */
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

include "globals/head.php";
?>

<body>
    <?php include "globals/sidebar.php"; ?>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php include "globals/topbar.php"; ?>

    <main class="main-content">
        <div class="content-wrapper fs-page">
            <div class="page-heading fs-page-heading">
                <div>
                    <div class="page-eyebrow"><i class="bi bi-stars"></i> FS • FULL SURPRISE</div>
                    <h2>You've found the secret page!</h2>
                    <p>You were expecting something useful. Instead, the internet has made a decision for you.</p>
                </div>
                <div class="fs-heading-icon" aria-hidden="true"><i class="bi bi-music-note-beamed"></i></div>
            </div>

            <section class="fs-hero management-card">
                <div class="fs-intro">
                    <span class="fs-label"><i class="bi bi-exclamation-circle-fill"></i> IMPORTANT ACADEMIC ANNOUNCEMENT</span>
                    <h1>Never gonna give you up.</h1>
                    <p>Never gonna let you down. Never gonna run around and desert you.</p>
                    <div class="fs-status"><span class="fs-dot"></span> Your surprise is ready. You're welcome. 😎</div>
                </div>
                <div class="fs-emoji" aria-hidden="true">🕺</div>
            </section>

            <section class="fs-player-card management-card">
                <div class="fs-player-heading">
                    <div>
                        <span class="fs-kicker">NOW PLAYING</span>
                        <h3>Rick Astley — Never Gonna Give You Up</h3>
                        <p>The legendary surprise. Enjoy the moment.</p>
                    </div>
                    <span class="fs-live"><span></span> PLAYING</span>
                </div>

                <div class="fs-video-wrap">
                    <div id="rickrollPlayer" title="Never Gonna Give You Up by Rick Astley"></div>
                </div>

                <div class="fs-controls">
                    <div class="fs-audio-note" id="audioNote"><i class="bi bi-volume-mute-fill"></i> Autoplay starts muted because browsers may block automatic sound.</div>
                    <button type="button" class="btn-primary-custom fs-sound-button" id="enableSound">
                        <i class="bi bi-volume-up-fill"></i> Turn on sound
                    </button>
                </div>
            </section>

            <section class="fs-message management-card">
                <div class="fs-message-icon"><i class="bi bi-heart-fill"></i></div>
                <div>
                    <strong>You've just been Rickrolled!</strong>
                    <p>Don't worry. Your grades are safe. Your playlist, however, may never be the same again.</p>
                </div>
                <span class="fs-stamp">GOTCHA! <i class="bi bi-emoji-sunglasses"></i></span>
            </section>

            <footer class="fs-footer">A little surprise from ETS-Async · Never gonna give you up. ♥</footer>
        </div>
    </main>

    <?php require_once "./globals/scripts.php"; ?>

    <style>
        /* Follows the existing ETS-Async light/dark theme variables. */
        .fs-page {
            padding-bottom: 35px;
        }

        .fs-page-heading {
            margin-bottom: 20px;
        }

        .fs-page-heading h2 {
            margin: 0;
            color: var(--text-color);
            font-size: 25px;
            font-weight: 750;
        }

        .fs-page-heading p {
            max-width: 700px;
            margin: 6px 0 0;
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.65;
        }

        .page-eyebrow {
            color: var(--academic-indigo, var(--academic-blue-dark));
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.1px;
            margin-bottom: 7px;
        }

        .page-eyebrow i {
            margin-right: 5px;
        }

        .fs-heading-icon {
            width: 54px;
            height: 54px;
            flex: 0 0 54px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            color: var(--academic-blue-dark);
            background: var(--activity-icon-blue-bg);
            font-size: 23px;
        }

        .fs-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 27px 32px;
            margin-bottom: 17px;
            border: 1px solid var(--activity-border);
            border-radius: 15px;
            background: var(--activity-card-bg, var(--card-bg));
            box-shadow: 0 4px 14px var(--shadow-color, rgba(20, 35, 70, .05));
        }

        .fs-intro {
            min-width: 0;
        }

        .fs-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--academic-indigo, var(--academic-blue-dark));
            font-size: 10px;
            font-weight: 850;
            letter-spacing: 1px;
        }

        .fs-hero h1 {
            margin: 12px 0 8px;
            color: var(--text-color);
            font-size: clamp(26px, 4vw, 42px);
            font-weight: 900;
            letter-spacing: -1px;
        }

        .fs-hero p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.7;
        }

        .fs-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 15px;
            padding: 8px 12px;
            border: 1px solid var(--activity-border);
            border-radius: 30px;
            background: var(--activity-progress-bg);
            color: var(--text-secondary);
            font-size: 10px;
            font-weight: 650;
        }

        .fs-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #35a575;
            box-shadow: 0 0 0 4px rgba(53, 165, 117, .14);
        }

        .fs-emoji {
            flex: 0 0 110px;
            text-align: center;
            font-size: 75px;
            animation: fsDance 1.2s ease-in-out infinite alternate;
        }

        @keyframes fsDance {
            from {
                transform: rotate(-12deg) translateY(0);
            }

            to {
                transform: rotate(12deg) translateY(-8px);
            }
        }

        .fs-player-card {
            padding: 22px;
            margin: 0;
            border: 1px solid var(--activity-border);
            border-radius: 15px;
            background: var(--activity-card-bg, var(--card-bg));
            box-shadow: 0 4px 14px var(--shadow-color, rgba(20, 35, 70, .05));
        }

        .fs-player-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 15px;
        }

        .fs-kicker {
            color: var(--text-secondary);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: 1px;
        }

        .fs-player-heading h3 {
            margin: 5px 0 3px;
            color: var(--text-color);
            font-size: 17px;
            font-weight: 800;
        }

        .fs-player-heading p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 11px;
        }

        .fs-live {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border-radius: 20px;
            background: rgba(53, 165, 117, .12);
            color: #35a575;
            font-size: 9px;
            font-weight: 850;
            letter-spacing: .8px;
            white-space: nowrap;
        }

        .fs-live span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #35a575;
            animation: fsBlink 1s infinite;
        }

        @keyframes fsBlink {
            50% {
                opacity: .3;
            }
        }

        .fs-video-wrap {
            position: relative;
            width: 100%;
            aspect-ratio: 16/9;
            overflow: hidden;
            border-radius: 12px;
            background: #080b12;
        }

        .fs-video-wrap iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .fs-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            padding-top: 14px;
        }

        .fs-audio-note {
            color: var(--text-secondary);
            font-size: 10px;
            line-height: 1.5;
        }

        .fs-audio-note i {
            margin-right: 5px;
        }

        .fs-sound-button {
            min-height: 37px;
            padding: 8px 13px;
            font-size: 11px;
            white-space: nowrap;
        }

        .fs-message {
            display: flex;
            align-items: center;
            gap: 13px;
            margin: 17px 0 0;
            padding: 15px 17px;
            border: 1px solid var(--activity-border);
            border-radius: 13px;
            background: var(--activity-card-bg, var(--card-bg));
        }

        .fs-message-icon {
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: rgba(223, 113, 128, .13);
            color: #df7180;
        }

        .fs-message strong {
            display: block;
            margin-bottom: 3px;
            color: var(--text-color);
            font-size: 12px;
        }

        .fs-message p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 10px;
            line-height: 1.55;
        }

        .fs-stamp {
            margin-left: auto;
            color: var(--academic-blue-dark);
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .fs-footer {
            padding: 17px 5px 0;
            text-align: center;
            color: var(--text-secondary);
            font-size: 10px;
        }

        @media(max-width:650px) {
            .fs-hero {
                padding: 22px 19px;
            }

            .fs-emoji {
                flex-basis: 65px;
                font-size: 52px;
            }

            .fs-player-card {
                padding: 16px;
            }

            .fs-player-heading {
                align-items: flex-start;
            }

            .fs-message {
                align-items: flex-start;
            }

            .fs-stamp {
                display: none;
            }
        }

        @media(prefers-reduced-motion:reduce) {

            .fs-emoji,
            .fs-live span {
                animation: none;
            }
        }
    </style>

    <script>
        let rickrollPlayer;
        let soundEnabled = false;

        // YouTube's player API starts the video muted to comply with autoplay policies.
        function onYouTubeIframeAPIReady() {
            rickrollPlayer = new YT.Player('rickrollPlayer', {
                videoId: 'dQw4w9WgXcQ',
                playerVars: {
                    autoplay: 1,
                    controls: 1,
                    loop: 1,
                    playlist: 'dQw4w9WgXcQ',
                    mute: 1,
                    playsinline: 1,
                    rel: 0
                },
                events: {
                    onReady: function(event) {
                        event.target.mute();
                        event.target.playVideo();
                    },
                    onStateChange: function(event) {
                        if (event.data === YT.PlayerState.ENDED) {
                            event.target.seekTo(0);
                            event.target.playVideo();
                        }
                    }
                }
            });
        }

        document.getElementById('enableSound').addEventListener('click', function() {
            if (rickrollPlayer && typeof rickrollPlayer.unMute === 'function') {
                rickrollPlayer.unMute();
                rickrollPlayer.setVolume(100);
                rickrollPlayer.playVideo();
                soundEnabled = true;
                this.innerHTML = '<i class="bi bi-volume-up-fill"></i> Sound enabled';
                this.disabled = true;
                document.getElementById('audioNote').innerHTML = '<i class="bi bi-volume-up-fill"></i> Sound is on. You have officially been Rickrolled.';
            }
        });

        (function loadYouTubeAPI() {
            const tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            const firstScript = document.getElementsByTagName('script')[0];
            firstScript.parentNode.insertBefore(tag, firstScript);
        })();
    </script>
</body>