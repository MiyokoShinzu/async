<?php
/* =========================================================
   STUDENT SHOUT BOX - ETS-Async Learning Portal
   Front-end demonstration page.
   Messages are temporary and are not saved to MySQL yet.
========================================================= */
session_start();

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

$user = $_SESSION["user"];
$firstName = trim((string)($user["first_name"] ?? $user["firstname"] ?? $user["name"] ?? "Student"));
$lastName = trim((string)($user["last_name"] ?? $user["lastname"] ?? ""));
$displayName = trim($firstName . " " . $lastName);
if ($displayName === "") $displayName = "Student";

$initials = strtoupper(substr($firstName, 0, 1) . ($lastName !== "" ? substr($lastName, 0, 1) : ""));
if ($initials === "") $initials = "ST";

include "globals/head.php";
?>

<body>
    <?php include "globals/sidebar.php"; ?>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php include "globals/topbar.php"; ?>

    <main class="main-content">
        <div class="content-wrapper shout-content">
            <section class="chat-box" aria-label="Student community chat">
                <header class="chat-topbar">
                    <div class="chat-top-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="chat-top-info">
                        <strong>Classroom Shout Box</strong>
                        <small><span></span> A space for your learning community</small>
                    </div>
                    <span class="chat-room-tag">GENERAL CHAT</span>
                </header>

                <div class="chat-messages" id="chatMessages" aria-live="polite">
                    <div class="chat-day"><span>Today</span></div>

                    <div class="message-row">
                        <div class="message-avatar avatar-blue">JM</div>
                        <div class="message-body">
                            <div class="message-meta">Juan Miguel Santos <time>9:15 AM</time></div>
                            <div class="message-bubble">Good morning, everyone! How is your progress with the activities?</div>
                        </div>
                    </div>

                    <div class="message-row">
                        <div class="message-avatar avatar-cyan">AR</div>
                        <div class="message-body">
                            <div class="message-meta">Angela Reyes <time>9:18 AM</time></div>
                            <div class="message-bubble">I'm currently working on the Arduino activity. The LED blinking exercise is fun!</div>
                        </div>
                    </div>

                    <div class="message-row message-own">
                        <div class="message-body">
                            <div class="message-meta"><?= htmlspecialchars($displayName, ENT_QUOTES, "UTF-8") ?> <time>9:20 AM</time></div>
                            <div class="message-bubble">That's great! Keep practicing. We can do this!</div>
                        </div>
                        <div class="message-avatar avatar-own"><?= htmlspecialchars($initials, ENT_QUOTES, "UTF-8") ?></div>
                    </div>

                    <div class="message-row">
                        <div class="message-avatar avatar-purple">KC</div>
                        <div class="message-body">
                            <div class="message-meta">Kyle Cruz <time>9:23 AM</time></div>
                            <div class="message-bubble">Does anyone know where we can find the additional reference materials?</div>
                        </div>
                    </div>
                </div>

                <form class="chat-input-area" id="chatForm" autocomplete="off">
                    <label class="visually-hidden" for="chatInput">Write a message</label>
                    <input type="text" id="chatInput" maxlength="500" placeholder="Type your message here..." required>
                    <span class="input-counter"><span id="charCount">0</span>/500</span>
                    <button type="submit"><i class="bi bi-send-fill"></i><span>Send</span></button>
                </form>
                <div class="chat-note"><i class="bi bi-info-circle"></i> Be respectful and keep the conversation learning-focused.</div>
            </section>
        </div>
    </main>

    <?php require_once "./globals/scripts.php"; ?>

    <style>
        .shout-content {
            min-height: calc(100vh - 130px);
            display: flex;
            flex-direction: column;
        }

        .shout-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .shout-eyebrow {
            margin-bottom: 7px;
            color: #2474c7;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .1em;
        }

        .shout-eyebrow i {
            margin-right: 5px;
        }

        .shout-heading h1 {
            margin: 0 0 5px;
            color: var(--text-color, #172033);
            font-size: clamp(25px, 3vw, 32px);
            font-weight: 800;
        }

        .shout-heading p {
            margin: 0;
            color: var(--text-secondary, #667085);
            font-size: 13px;
        }

        .shout-online {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid var(--activity-border, #e5eaf1);
            border-radius: 30px;
            background: var(--activity-card-bg, #fff);
            color: var(--text-secondary, #667085);
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .shout-online>span,
        .chat-top-info small>span {
            width: 7px;
            height: 7px;
            display: inline-block;
            border-radius: 50%;
            background: #16a36a;
        }

        .chat-box {
            width: 100%;
            max-width: 1100px;
            height: clamp(420px, calc(100vh - 230px), 740px);
            min-height: 420px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--activity-border, #e2e8f0);
            border-radius: 16px;
            background: var(--activity-card-bg, #fff);
            box-shadow: 0 10px 35px rgba(15, 23, 42, .07);
        }

        .chat-topbar {
            min-height: 70px;
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            border-bottom: 1px solid var(--activity-border, #e9edf3);
        }

        .chat-top-icon {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: #e8f3ff;
            color: #246fc0;
            font-size: 19px;
        }

        .chat-top-info {
            min-width: 0;
        }

        .chat-top-info strong {
            display: block;
            color: var(--text-color, #172033);
            font-size: 14px;
            font-weight: 800;
        }

        .chat-top-info small {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-top: 4px;
            color: var(--text-secondary, #7b8494);
            font-size: 10px;
        }

        .chat-room-tag {
            margin-left: auto;
            padding: 6px 9px;
            border-radius: 6px;
            background: #edf5ff;
            color: #286bb0;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .06em;
        }

        /* No scrolling: the message area clips overflow and retains recent messages. */
        .chat-messages {
            min-height: 0;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            gap: 14px;
            padding: 18px 20px;
            overflow: hidden;
            background:
                radial-gradient(circle at 1px 1px, rgba(130, 151, 178, .10) 1px, transparent 0) 0 0 / 20px 20px,
                var(--activity-progress-bg, #f7f9fc);
        }

        .chat-day {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 2px;
            color: var(--text-secondary, #8a94a4);
            font-size: 10px;
        }

        .chat-day::before,
        .chat-day::after {
            content: "";
            width: 65px;
            height: 1px;
            background: var(--activity-border, #dfe5ed);
        }

        .message-row {
            max-width: 82%;
            display: flex;
            align-items: flex-end;
            gap: 9px;
        }

        .message-own {
            align-self: flex-end;
            justify-content: flex-end;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 800;
        }

        .avatar-blue {
            background: #e5efff;
            color: #2458b7;
        }

        .avatar-cyan {
            background: #e1f8fc;
            color: #087d99;
        }

        .avatar-purple {
            background: #f0ebff;
            color: #6952b5;
        }

        .avatar-own {
            background: linear-gradient(135deg, #1769c2, #28a9cf);
            color: #fff;
        }

        .message-body {
            min-width: 0;
        }

        .message-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 5px 3px;
            color: var(--text-color, #344054);
            font-size: 10px;
            font-weight: 800;
        }

        .message-meta time {
            color: var(--text-secondary, #929baa);
            font-size: 9px;
            font-weight: 500;
        }

        .message-bubble {
            width: fit-content;
            max-width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--activity-border, #e5eaf1);
            border-radius: 4px 12px 12px 12px;
            background: var(--activity-card-bg, #fff);
            color: var(--text-color, #344054);
            font-size: 12px;
            line-height: 1.55;
            overflow-wrap: anywhere;
            box-shadow: 0 2px 5px rgba(15, 23, 42, .025);
        }

        .message-own .message-meta {
            justify-content: flex-end;
        }

        .message-own .message-bubble {
            margin-left: auto;
            border-color: #246fc0;
            border-radius: 12px 4px 12px 12px;
            background: #246fc0;
            color: #fff;
        }

        .chat-input-area {
            min-height: 72px;
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 12px 16px;
            border-top: 1px solid var(--activity-border, #e5eaf1);
            background: var(--activity-card-bg, #fff);
        }

        .chat-input-area input {
            width: 100%;
            min-width: 0;
            height: 43px;
            padding: 0 14px;
            border: 1px solid var(--activity-border, #dfe5ed);
            border-radius: 10px;
            outline: none;
            background: var(--activity-progress-bg, #f8fafc);
            color: var(--text-color, #172033);
            font: inherit;
            font-size: 12px;
        }

        .chat-input-area input:focus {
            border-color: #398bd0;
            box-shadow: 0 0 0 3px rgba(57, 139, 208, .12);
        }

        .chat-input-area input::placeholder {
            color: #9aa4b2;
        }

        .input-counter {
            flex: 0 0 auto;
            color: var(--text-secondary, #929baa);
            font-size: 9px;
        }

        .chat-input-area button {
            height: 43px;
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 16px;
            border: 0;
            border-radius: 9px;
            background: #246fc0;
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
        }

        .chat-input-area button:hover {
            background: #185da8;
        }

        .chat-note {
            flex: 0 0 auto;
            padding: 0 17px 10px;
            background: var(--activity-card-bg, #fff);
            color: var(--text-secondary, #929baa);
            font-size: 9px;
        }

        .chat-note i {
            margin-right: 4px;
            color: #398bd0;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @media (max-width: 700px) {
            .shout-content {
                min-height: calc(100vh - 110px);
            }

            .chat-box {
                height: calc(100vh - 205px);
                min-height: 390px;
                border-radius: 12px;
            }

            .chat-topbar {
                min-height: 62px;
                padding: 10px 12px;
            }

            .chat-messages {
                padding: 13px 10px;
                gap: 12px;
            }

            .message-row {
                max-width: 94%;
            }

            .chat-input-area {
                gap: 7px;
                padding: 10px;
            }

            .chat-input-area input {
                height: 41px;
                padding: 0 10px;
            }

            .chat-input-area button {
                width: 42px;
                padding: 0;
            }

            .chat-input-area button span,
            .input-counter {
                display: none;
            }
        }

        @media (max-width: 420px) {
            .shout-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .shout-room-tag {
                display: none;
            }

            .chat-room-tag {
                display: none;
            }

            .message-row {
                max-width: 98%;
            }

            .message-bubble {
                font-size: 11px;
                padding: 8px 10px;
            }
        }
    </style>


    <style>
        /* Full-width, full-height chat layout; no page heading above the chat. */
        .main-content {
            min-height: 100vh;
        }

        .shout-content {
            width: 100%;
            max-width: none;
            height: calc(100vh - 80px);
            min-height: 0;
            padding-top: 0;
            padding-bottom: 0;
            display: flex;
            flex-direction: column;
        }

        .chat-box {
            width: 100%;
            max-width: none;
            height: 100%;
            min-height: 0;
            flex: 1 1 auto;
            margin: 0;
        }

        @media (max-width: 700px) {
            .shout-content {
                height: calc(100vh - 70px);
                min-height: 0;
            }

            .chat-box {
                height: 100%;
                min-height: 0;
            }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("chatForm");
            const input = document.getElementById("chatInput");
            const messages = document.getElementById("chatMessages");
            const charCount = document.getElementById("charCount");

            const studentName = <?= json_encode($displayName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
            const studentInitials = <?= json_encode($initials, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

            function escapeHTML(value) {
                return value.replace(/[&<>"']/g, function(character) {
                    return {
                        "&": "&amp;",
                        "<": "&lt;",
                        ">": "&gt;",
                        '"': "&quot;",
                        "'": "&#039;"
                    } [character];
                });
            }

            input.addEventListener("input", function() {
                charCount.textContent = input.value.length;
            });

            form.addEventListener("submit", function(event) {
                event.preventDefault();

                const message = input.value.trim();
                if (message === "") return;

                const now = new Date();
                const time = now.toLocaleTimeString([], {
                    hour: "numeric",
                    minute: "2-digit"
                });
                const row = document.createElement("div");
                row.className = "message-row message-own";

                row.innerHTML = `
                <div class="message-body">
                    <div class="message-meta">${escapeHTML(studentName)} <time>${escapeHTML(time)}</time></div>
                    <div class="message-bubble">${escapeHTML(message)}</div>
                </div>
                <div class="message-avatar avatar-own">${escapeHTML(studentInitials)}</div>
            `;

                messages.appendChild(row);

                // Keep the area non-scrollable by showing only the latest four chat messages.
                const rows = messages.querySelectorAll(".message-row");
                while (messages.querySelectorAll(".message-row").length > 4) {
                    messages.querySelector(".message-row").remove();
                }

                input.value = "";
                charCount.textContent = "0";
            });
        });
    </script>
</body>

</html>