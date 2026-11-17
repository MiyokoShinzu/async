<?php
/* =========================================================
   LF - LOOKING FOR
   Temporary humorous placeholder for ETS-Async
   Student-only page; no database query required.
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
        <div class="content-wrapper lf-page">
            <div class="page-heading lf-page-heading">
                <div>
                    <div class="page-eyebrow"><i class="bi bi-search-heart"></i> LF • LOOKING FOR</div>
                    <h2>Looking for something?</h2>
                    <p>We looked everywhere. Under the keyboard, inside the coffee mug, even behind the deadlines. Still looking!</p>
                </div>
                <div class="lf-heading-icon" aria-hidden="true"><i class="bi bi-binoculars-fill"></i></div>
            </div>

            <section class="lf-hero">
                <div class="lf-hero-copy">
                    <span class="lf-hero-label"><i class="bi bi-cone-striped"></i> TEMPORARILY UNDER CONSTRUCTION</span>
                    <h1>Our real LF page is playing hide and seek.</h1>
                    <p>Our developers are currently negotiating with the code. The code has requested snacks, fewer bugs, and a long weekend.</p>
                    <div class="lf-status"><span class="lf-pulse"></span> Official page is getting its brain cells together.</div>
                </div>
                <div class="lf-hero-art" aria-hidden="true">
                    <div class="lf-spark lf-spark-one">✦</div>
                    <div class="lf-spark lf-spark-two">✧</div>
                    <div class="lf-magnifier"><i class="bi bi-search"></i><span>?</span></div>
                    <div class="lf-floating-note">Have you checked your downloads?</div>
                </div>
            </section>

            <section class="lf-grid">
                <article class="management-card lf-card">
                    <div class="lf-card-heading">
                        <span class="lf-card-icon"><i class="bi bi-code-slash"></i></span>
                        <div><span class="lf-kicker">DEVELOPMENT STATUS</span>
                            <h3>This page is on a little break.</h3>
                        </div>
                    </div>
                    <p>We asked the code to behave. It replied with 17 new errors and a “works on my machine” message.</p>
                    <div class="lf-progress-label"><span>Page preparation</span><strong>99% vibes, 1% actual code</strong></div>
                    <div class="lf-progress"><span></span></div>
                    <small>Please do not feed the bugs. They already know where the database is.</small>
                </article>

                <article class="management-card lf-card">
                    <div class="lf-card-heading">
                        <span class="lf-card-icon purple"><i class="bi bi-emoji-laughing"></i></span>
                        <div><span class="lf-kicker">DAILY DOSE OF HUMOR</span>
                            <h3 id="jokeTitle">A little joke while you wait</h3>
                        </div>
                    </div>
                    <p id="jokeText" class="lf-joke-text">Why do programmers prefer dark mode? Because light attracts bugs.</p>
                    <button type="button" class="btn-primary-custom lf-button" id="newJoke"><i class="bi bi-shuffle"></i> Give me another</button>
                </article>

                <article class="management-card lf-card">
                    <div class="lf-card-heading">
                        <span class="lf-card-icon orange"><i class="bi bi-chat-square-quote"></i></span>
                        <div><span class="lf-kicker">STUDENT SURVIVAL TOOL</span>
                            <h3>Generate a study excuse</h3>
                        </div>
                    </div>
                    <p>Need a totally questionable explanation for why your task is not finished? We got you. (Just kidding. Please do your work.)</p>
                    <div class="lf-excuse-output" id="excuseOutput">Click the button to generate your very creative excuse.</div>
                    <button type="button" class="btn-primary-custom lf-button lf-button-orange" id="newExcuse"><i class="bi bi-magic"></i> Generate excuse</button>
                </article>

                <article class="management-card lf-card">
                    <div class="lf-card-heading">
                        <span class="lf-card-icon green"><i class="bi bi-lightning-charge"></i></span>
                        <div><span class="lf-kicker">SYSTEM CHECK</span>
                            <h3>Current productivity level</h3>
                        </div>
                    </div>
                    <div class="lf-mood-number" id="moodNumber">42<span>%</span></div>
                    <p id="moodMessage">Running on coffee, hope, and unfinished tasks.</p>
                    <button type="button" class="lf-text-button" id="boostMood"><i class="bi bi-arrow-repeat"></i> Recalculate my energy</button>
                </article>
            </section>

            <section class="management-card lf-bottom">
                <span class="lf-bottom-icon"><i class="bi bi-info-circle"></i></span>
                <div>
                    <strong>Just a temporary page!</strong>
                    <p>The official Looking For (LF) feature is still being prepared. This playful placeholder will be replaced once the real page is ready.</p>
                </div>
                <span class="lf-bottom-stamp">COMING SOON <i class="bi bi-stars"></i></span>
            </section>

            <footer class="lf-footer">Made with <span>♥</span> and a suspicious amount of coffee · ETS-Async</footer>
        </div>
    </main>

    <?php require_once "./globals/scripts.php"; ?>

    <style>
        /* Uses the existing ETS-Async theme variables so light/dark mode
           follows the website's current data-theme setting automatically. */
        .lf-page {
            padding-bottom: 35px;
        }

        .lf-page-heading {
            margin-bottom: 20px;
        }

        .lf-page-heading h2 {
            margin: 0;
            color: var(--text-color);
            font-size: 25px;
            font-weight: 750;
        }

        .lf-page-heading p {
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

        .lf-heading-icon {
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

        .lf-hero {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 28px;
            min-height: 235px;
            padding: 30px 34px;
            margin-bottom: 18px;
            border: 1px solid var(--activity-border);
            border-radius: 15px;
            background: var(--activity-card-bg, var(--card-bg));
            box-shadow: 0 4px 14px var(--shadow-color, rgba(20, 35, 70, .05));
        }

        .lf-hero-copy {
            position: relative;
            z-index: 1;
            max-width: 650px;
        }

        .lf-hero-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--academic-indigo, var(--academic-blue-dark));
            font-size: 10px;
            font-weight: 850;
            letter-spacing: 1px;
        }

        .lf-hero h1 {
            margin: 12px 0 9px;
            color: var(--text-color);
            font-size: clamp(25px, 3.5vw, 36px);
            line-height: 1.15;
            font-weight: 850;
            letter-spacing: -.8px;
        }

        .lf-hero p {
            max-width: 570px;
            margin: 0;
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.75;
        }

        .lf-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 17px;
            padding: 8px 12px;
            border-radius: 30px;
            background: var(--activity-progress-bg);
            border: 1px solid var(--activity-border);
            color: var(--text-secondary);
            font-size: 10px;
            font-weight: 650;
        }

        .lf-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d99a39;
            box-shadow: 0 0 0 4px rgba(217, 154, 57, .15);
        }

        .lf-hero-art {
            position: relative;
            flex: 0 0 205px;
            height: 165px;
            display: grid;
            place-items: center;
        }

        .lf-magnifier {
            position: relative;
            display: grid;
            place-items: center;
            width: 105px;
            height: 105px;
            border: 11px solid var(--academic-blue-dark);
            border-radius: 50%;
            color: var(--academic-blue-dark);
            background: var(--activity-progress-bg);
            font-size: 35px;
            transform: rotate(-15deg);
            box-shadow: 0 12px 28px var(--shadow-color);
        }

        .lf-magnifier:after {
            content: "";
            position: absolute;
            width: 17px;
            height: 53px;
            background: var(--academic-blue-dark);
            border-radius: 10px;
            right: -18px;
            bottom: -39px;
            transform: rotate(-12deg);
        }

        .lf-magnifier span {
            position: absolute;
            font-size: 23px;
            font-weight: 900;
            transform: translate(20px, -18px);
            color: #d99a39;
        }

        .lf-spark {
            position: absolute;
            color: #d99a39;
            font-size: 27px;
        }

        .lf-spark-one {
            top: 3px;
            right: 31px;
        }

        .lf-spark-two {
            bottom: 17px;
            left: 8px;
            font-size: 34px;
        }

        .lf-floating-note {
            position: absolute;
            right: -4px;
            bottom: 0;
            padding: 8px 10px;
            border-radius: 9px;
            background: var(--activity-progress-bg);
            border: 1px solid var(--activity-border);
            color: var(--text-secondary);
            box-shadow: 0 5px 14px var(--shadow-color);
            font-size: 9px;
            font-weight: 750;
            transform: rotate(3deg);
        }

        .lf-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .lf-card {
            min-width: 0;
            padding: 21px;
            margin: 0;
        }

        .lf-card-heading {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 13px;
        }

        .lf-card-icon {
            width: 41px;
            height: 41px;
            flex: 0 0 41px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            background: var(--activity-icon-blue-bg);
            color: var(--academic-blue-dark);
            font-size: 18px;
        }

        .lf-card-icon.purple {
            background: rgba(132, 91, 196, .13);
            color: #9871d0;
        }

        .lf-card-icon.orange {
            background: rgba(220, 145, 49, .14);
            color: #d58b2d;
        }

        .lf-card-icon.green {
            background: rgba(43, 157, 105, .13);
            color: #36a875;
        }

        .lf-kicker {
            display: block;
            color: var(--text-secondary);
            font-size: 9px;
            font-weight: 850;
            letter-spacing: 1px;
        }

        .lf-card h3 {
            margin: 4px 0 0;
            color: var(--text-color);
            font-size: 16px;
            font-weight: 750;
        }

        .lf-card>p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 12px;
            line-height: 1.7;
        }

        .lf-progress-label {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 18px;
            color: var(--text-secondary);
            font-size: 10px;
        }

        .lf-progress-label strong {
            color: var(--text-color);
        }

        .lf-progress {
            height: 8px;
            margin-top: 8px;
            border-radius: 20px;
            overflow: hidden;
            background: var(--activity-progress-bg);
        }

        .lf-progress span {
            display: block;
            width: 99%;
            height: 100%;
            border-radius: 20px;
            background: var(--academic-blue-dark);
        }

        .lf-card>small {
            display: block;
            margin-top: 11px;
            color: var(--text-secondary);
            font-size: 10px;
        }

        .lf-joke-text {
            min-height: 43px;
            margin-bottom: 13px !important;
        }

        .lf-button {
            min-height: 37px;
            padding: 8px 13px;
            font-size: 11px;
        }

        .lf-button-orange {
            background: #c9842d;
            border-color: #c9842d;
        }

        .lf-excuse-output {
            min-height: 57px;
            display: flex;
            align-items: center;
            padding: 10px 12px;
            margin: 12px 0;
            border: 1px dashed var(--activity-border);
            border-radius: 9px;
            background: var(--activity-progress-bg);
            color: var(--text-secondary);
            font-size: 11px;
            line-height: 1.6;
        }

        .lf-mood-number {
            margin: 13px 0 4px;
            color: #35a575;
            font-size: 42px;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -2px;
        }

        .lf-mood-number span {
            font-size: 19px;
        }

        .lf-text-button {
            margin-top: 13px;
            padding: 0;
            border: 0;
            background: none;
            color: var(--academic-blue-dark);
            font-size: 11px;
            font-weight: 750;
            cursor: pointer;
        }

        .lf-bottom {
            display: flex;
            align-items: center;
            gap: 13px;
            margin: 17px 0 0;
            padding: 15px 17px;
        }

        .lf-bottom-icon {
            flex: 0 0 34px;
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: var(--activity-icon-blue-bg);
            color: var(--academic-blue-dark);
        }

        .lf-bottom strong {
            display: block;
            margin-bottom: 3px;
            color: var(--text-color);
            font-size: 12px;
        }

        .lf-bottom p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 10px;
            line-height: 1.55;
        }

        .lf-bottom-stamp {
            margin-left: auto;
            white-space: nowrap;
            color: var(--academic-blue-dark);
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 1px;
        }

        .lf-footer {
            padding: 17px 5px 0;
            text-align: center;
            color: var(--text-secondary);
            font-size: 10px;
        }

        .lf-footer span {
            color: #df7180;
        }

        @media(max-width:850px) {
            .lf-hero {
                padding: 25px;
            }

            .lf-hero-art {
                flex-basis: 165px;
                transform: scale(.85);
            }

            .lf-floating-note {
                right: -15px;
            }
        }

        @media(max-width:650px) {
            .lf-page-heading {
                align-items: flex-start;
            }

            .lf-heading-icon {
                width: 45px;
                height: 45px;
                flex-basis: 45px;
            }

            .lf-hero {
                min-height: 0;
                padding: 22px 19px;
            }

            .lf-hero-art {
                display: none;
            }

            .lf-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .lf-card {
                padding: 18px;
            }

            .lf-bottom {
                align-items: flex-start;
            }

            .lf-bottom-stamp {
                display: none;
            }
        }
    </style>

    <script>
        (() => {
            const jokes = [
                ["A programmer's diet", "Why do programmers prefer dark mode? Because light attracts bugs."],
                ["The missing semicolon", "I told my computer I needed a break. It said: “No problem, I'll go to sleep.”"],
                ["Array of feelings", "Why did the programmer quit their job? Because they didn't get arrays."],
                ["Deadline season", "My favorite exercise is running... out of time before a deadline."],
                ["Wi-Fi connection", "I have a strong relationship with Wi-Fi. We connect, disconnect, then reconnect."],
                ["Debugging life", "There are 10 kinds of people: those who understand binary and those who don't."]
            ];
            const excuses = [
                "My laptop needed emotional support, so I had to listen to its fan for 45 minutes.",
                "I was about to submit, but my cursor and I had a disagreement.",
                "The task was completed in my dreams. I am currently working on the reality version.",
                "I opened the file, blinked, and somehow it was already tomorrow.",
                "My internet connection decided to practice social distancing.",
                "I was debugging one error and accidentally discovered three new personalities in my code.",
                "The document is ready. It is just shy and does not want to be uploaded yet."
            ];
            let previousJoke = -1,
                previousExcuse = -1;

            function pickDifferent(list, previous) {
                let index;
                do {
                    index = Math.floor(Math.random() * list.length);
                } while (list.length > 1 && index === previous);
                return index;
            }
            document.getElementById("newJoke").addEventListener("click", () => {
                const index = pickDifferent(jokes, previousJoke);
                previousJoke = index;
                document.getElementById("jokeTitle").textContent = jokes[index][0];
                document.getElementById("jokeText").textContent = jokes[index][1];
            });
            document.getElementById("newExcuse").addEventListener("click", () => {
                const index = pickDifferent(excuses, previousExcuse);
                previousExcuse = index;
                document.getElementById("excuseOutput").textContent = excuses[index];
            });
            document.getElementById("boostMood").addEventListener("click", () => {
                const score = Math.floor(Math.random() * 101);
                document.getElementById("moodNumber").innerHTML = score + "<span>%</span>";
                document.getElementById("moodMessage").textContent = score >= 80 ?
                    "Legendary focus! Go conquer that task." :
                    score >= 55 ? "Not bad! One small step at a time." :
                    score >= 30 ? "A little stretch and a glass of water might help." :
                    "Low battery detected. Take a short break, then try again.";
            });
        })();
    </script>
</body>