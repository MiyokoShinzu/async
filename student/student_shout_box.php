<?php
/* =========================================================
   STUDENT SHOUT BOX - ETS-Async Learning Portal
   Database-connected shared student chat.
========================================================= */
session_start();

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION["user"];

$firstName     = trim((string)($user["first_name"] ?? $user["firstname"] ?? $user["name"] ?? ""));
$lastName      = trim((string)($user["last_name"] ?? $user["lastname"] ?? ""));
$middleInitial = trim((string)($user["middle_initial"] ?? ""));
$extensionName = trim((string)($user["extension_name"] ?? ""));
$studentId     = trim((string)($user["student_id"] ?? $_SESSION["user_id"]));

$displayName = trim(
    $firstName . " " .
        ($middleInitial !== "" ? $middleInitial . ". " : "") .
        $lastName .
        ($extensionName !== "" ? " " . $extensionName : "")
);
if ($displayName === "") {
    $displayName = trim((string)($user["username"] ?? "Student"));
}
if ($displayName === "") {
    $displayName = "Student";
}

$initials = "";
if ($firstName !== "") {
    $initials .= strtoupper(substr($firstName, 0, 1));
}
if ($lastName !== "") {
    $initials .= strtoupper(substr($lastName, 0, 1));
}
if ($initials === "") {
    $initials = "ST";
}

/* Match the database connection convention in your existing Async pages. */
require_once "../src/connection.php";

if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    http_response_code(500);
    die("Database connection is unavailable. Please check ../src/connection.php.");
}

if (empty($_SESSION["shout_box_csrf"])) {
    $_SESSION["shout_box_csrf"] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION["shout_box_csrf"];

include "globals/head.php";
?>

<body>
    <?php include "globals/sidebar.php"; ?>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php include "globals/topbar.php"; ?>

    <main class="main-content mt-2 ">
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
                    <div class="chat-day"><span>Recent messages</span></div>
                    <div class="chat-empty" id="chatEmpty">Loading messages...</div>
                </div>

                <form class="chat-input-area" id="chatForm" autocomplete="off">
                    <label class="visually-hidden" for="chatInput">Write a message</label>
                    <input type="text" id="chatInput" maxlength="500" placeholder="Type your message here..." required>
                    <span class="input-counter"><span id="charCount">0</span>/500</span>
                    <button type="submit"><i class="bi bi-send-fill"></i><span>Send</span></button>
                </form>
                <div class="chat-note"><i class="bi bi-info-circle"></i> Be respectful and keep the conversation learning-focused.</div>
                <!-- Edit Message Modal -->
                <div class="message-modal-backdrop" id="editMessageModal" hidden>
                    <section class="message-modal" role="dialog" aria-modal="true" aria-labelledby="editMessageTitle">
                        <div class="message-modal-header">
                            <div>
                                <span class="message-modal-eyebrow">MESSAGE OPTIONS</span>
                                <h3 id="editMessageTitle">Edit message</h3>
                            </div>
                            <button type="button" class="message-modal-close" id="closeEditModal" aria-label="Close">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <form id="editMessageForm">
                            <label for="editMessageText">Your message</label>
                            <textarea id="editMessageText" maxlength="500" rows="4" required></textarea>
                            <div class="message-modal-footer">
                                <span class="edit-char-count"><span id="editCharCount">0</span>/500</span>
                                <div class="message-modal-buttons">
                                    <button type="button" class="modal-cancel-btn" id="cancelEditModal">Cancel</button>
                                    <button type="submit" class="modal-save-btn" id="saveEditMessage">
                                        <i class="bi bi-check2"></i> Save changes
                                    </button>
                                </div>
                            </div>
                            <p class="message-modal-error" id="editMessageError" role="alert"></p>
                        </form>
                    </section>
                </div>

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

        .chat-empty {
            margin: auto;
            padding: 18px;
            color: var(--text-secondary, #7b8494);
            font-size: 12px;
            text-align: center;
        }

        .message-menu-wrap {
            position: relative;
            display: inline-flex;
            margin-left: auto;
        }

        .message-menu-trigger {
            width: 25px;
            height: 25px;
            display: inline-grid;
            place-items: center;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: transparent;
            color: var(--text-secondary, #7b8494);
            cursor: pointer;
            font-size: 16px;
        }

        .message-menu-trigger:hover,
        .message-menu-trigger[aria-expanded="true"] {
            background: rgba(36, 111, 192, .10);
            color: #246fc0;
        }

        .message-menu {
            position: absolute;
            z-index: 30;
            top: 27px;
            right: 0;
            min-width: 155px;
            padding: 5px;
            border: 1px solid var(--activity-border, #e2e8f0);
            border-radius: 10px;
            background: var(--activity-card-bg, #fff);
            box-shadow: 0 10px 28px rgba(15, 23, 42, .16);
        }

        .message-menu[hidden] {
            display: none !important;
        }

        .message-menu-item {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            border: 0;
            border-radius: 7px;
            background: transparent;
            color: var(--text-color, #344054);
            text-align: left;
            font-size: 11px;
            cursor: pointer;
        }

        .message-menu-item:hover {
            background: var(--activity-progress-bg, #f3f7fc);
        }

        .message-menu-delete {
            color: #d04444;
        }

        .message-modal-backdrop[hidden] {
            display: none !important;
        }

        .message-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: grid;
            place-items: center;
            padding: 18px;
            background: rgba(15, 23, 42, .48);
            backdrop-filter: blur(3px);
        }

        .message-modal {
            width: min(100%, 460px);
            padding: 22px;
            border: 1px solid var(--activity-border, #e2e8f0);
            border-radius: 16px;
            background: var(--activity-card-bg, #fff);
            box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
        }

        .message-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
        }

        .message-modal-eyebrow {
            color: #246fc0;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .1em;
        }

        .message-modal-header h3 {
            margin: 4px 0 0;
            color: var(--text-color, #172033);
            font-size: 19px;
            font-weight: 800;
        }

        .message-modal-close {
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 8px;
            background: var(--activity-progress-bg, #f3f6fa);
            color: var(--text-secondary, #667085);
        }

        .message-modal label {
            display: block;
            margin-bottom: 7px;
            color: var(--text-color, #344054);
            font-size: 11px;
            font-weight: 700;
        }

        .message-modal textarea {
            width: 100%;
            min-height: 115px;
            resize: vertical;
            padding: 12px;
            border: 1px solid var(--activity-border, #dfe5ed);
            border-radius: 10px;
            outline: none;
            background: var(--activity-progress-bg, #f8fafc);
            color: var(--text-color, #172033);
            font: inherit;
            font-size: 13px;
            line-height: 1.5;
        }

        .message-modal textarea:focus {
            border-color: #398bd0;
            box-shadow: 0 0 0 3px rgba(57, 139, 208, .12);
        }

        .message-modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 12px;
        }

        .edit-char-count {
            color: var(--text-secondary, #929baa);
            font-size: 10px;
        }

        .message-modal-buttons {
            display: flex;
            gap: 8px;
        }

        .modal-cancel-btn,
        .modal-save-btn {
            min-height: 36px;
            padding: 0 13px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
        }

        .modal-cancel-btn {
            border: 1px solid var(--activity-border, #dfe5ed);
            background: transparent;
            color: var(--text-color, #344054);
        }

        .modal-save-btn {
            border: 0;
            background: #246fc0;
            color: #fff;
        }

        .modal-save-btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .message-modal-error {
            min-height: 15px;
            margin: 8px 0 0;
            color: #d04444;
            font-size: 10px;
        }

        @media (max-width: 480px) {
            .message-modal {
                padding: 17px;
            }

            .message-modal-footer {
                align-items: flex-start;
                flex-direction: column;
            }

            .message-modal-buttons {
                width: 100%;
                justify-content: flex-end;
            }
        }

        .message-edited-label {
            color: var(--text-secondary, #929baa);
            font-size: 9px;
            font-weight: 500;
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
            const sendButton = form.querySelector("button");
            const emptyMessage = document.getElementById("chatEmpty");

            const currentStudentId = <?= json_encode($studentId, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
            const csrfToken = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

            let lastRenderedSignature = "";

            function makeElement(tag, className, text) {
                const element = document.createElement(tag);
                if (className) element.className = className;
                if (text !== undefined) element.textContent = text;
                return element;
            }

            function formatTime(value) {
                const date = new Date(value.replace(" ", "T"));
                if (Number.isNaN(date.getTime())) return "";
                return date.toLocaleTimeString([], {
                    hour: "numeric",
                    minute: "2-digit"
                });
            }

            function renderMessages(items) {
                const signature = JSON.stringify(items.map(item => [
                    item.id, item.message, item.created_at, item.edited_at, item.sender_student_id
                ]));

                if (signature === lastRenderedSignature) return;
                lastRenderedSignature = signature;

                messages.replaceChildren();
                const day = makeElement("div", "chat-day");
                day.appendChild(makeElement("span", "", "Recent messages"));
                messages.appendChild(day);

                if (!items.length) {
                    messages.appendChild(makeElement("div", "chat-empty", "No messages yet. Start the conversation!"));
                    return;
                }

                items.forEach(item => {
                    const ownMessage = String(item.sender_student_id) === String(currentStudentId);
                    const row = makeElement("div", "message-row" + (ownMessage ? " message-own" : ""));

                    if (!ownMessage) {
                        row.appendChild(makeElement("div", "message-avatar avatar-blue", item.sender_initials || "ST"));
                    }

                    const body = makeElement("div", "message-body");
                    const meta = makeElement("div", "message-meta");
                    meta.appendChild(document.createTextNode(item.sender_name || "Student"));
                    meta.appendChild(makeElement("time", "", formatTime(item.created_at || "")));

                    if (item.edited) {
                        meta.appendChild(makeElement("span", "message-edited-label", "(edited)"));
                    }

                    body.appendChild(meta);
                    body.appendChild(makeElement("div", "message-bubble", item.message || ""));

                    if (ownMessage) {
                        const actionWrap = makeElement("div", "message-menu-wrap");
                        const menuButton = makeElement("button", "message-menu-trigger");
                        menuButton.type = "button";
                        menuButton.setAttribute("aria-label", "Message options");
                        menuButton.setAttribute("aria-haspopup", "true");
                        menuButton.setAttribute("aria-expanded", "false");
                        menuButton.innerHTML = '<i class="bi bi-three-dots-vertical"></i>';

                        const menu = makeElement("div", "message-menu");
                        menu.hidden = true;

                        const editButton = makeElement("button", "message-menu-item", "Edit message");
                        editButton.type = "button";
                        editButton.innerHTML = '<i class="bi bi-pencil"></i><span>Edit message</span>';
                        editButton.addEventListener("click", () => {
                            closeAllMessageMenus();
                            openEditModal(item);
                        });

                        const unsendButton = makeElement("button", "message-menu-item message-menu-delete", "Unsend");
                        unsendButton.type = "button";
                        unsendButton.innerHTML = '<i class="bi bi-trash3"></i><span>Unsend</span>';
                        unsendButton.addEventListener("click", () => {
                            closeAllMessageMenus();
                            unsendMessage(item.id);
                        });

                        menu.append(editButton, unsendButton);
                        menuButton.addEventListener("click", function(event) {
                            event.stopPropagation();
                            const willOpen = menu.hidden;
                            closeAllMessageMenus();
                            menu.hidden = !willOpen;
                            menuButton.setAttribute("aria-expanded", willOpen ? "true" : "false");
                        });

                        actionWrap.append(menuButton, menu);
                        meta.appendChild(actionWrap);

                        body.appendChild(meta);
                        body.appendChild(makeElement("div", "message-bubble", item.message || ""));
                        row.appendChild(body);
                        row.appendChild(makeElement("div", "message-avatar avatar-own", item.sender_initials || "ST"));
                    } else {
                        body.appendChild(meta);
                        body.appendChild(makeElement("div", "message-bubble", item.message || ""));
                        row.appendChild(body);
                    }

                    messages.appendChild(row);
                });
            }

            async function manageMessage(action, id, message = "") {
                const body = new URLSearchParams();
                body.set("action", action);
                body.set("message_id", String(id));
                body.set("csrf_token", csrfToken);
                if (action === "edit") body.set("message", message);

                const response = await fetch("shout_box_manage.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
                        "Accept": "application/json"
                    },
                    body: body.toString()
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || "Unable to update this message.");
                }
                return data;
            }

            const editModal = document.getElementById("editMessageModal");
            const editForm = document.getElementById("editMessageForm");
            const editText = document.getElementById("editMessageText");
            const editCharCount = document.getElementById("editCharCount");
            const editError = document.getElementById("editMessageError");
            const saveEditButton = document.getElementById("saveEditMessage");
            let editingMessageId = null;
            let originalMessageText = "";

            function closeAllMessageMenus() {
                document.querySelectorAll(".message-menu").forEach(menu => {
                    menu.hidden = true;
                    const trigger = menu.parentElement.querySelector(".message-menu-trigger");
                    if (trigger) trigger.setAttribute("aria-expanded", "false");
                });
            }

            function openEditModal(item) {
                editingMessageId = item.id;
                originalMessageText = item.message || "";
                editText.value = originalMessageText;
                editCharCount.textContent = editText.value.length;
                editError.textContent = "";
                editModal.hidden = false;
                document.body.style.overflow = "hidden";
                window.setTimeout(() => {
                    editText.focus();
                    editText.setSelectionRange(editText.value.length, editText.value.length);
                }, 30);
            }

            function closeEditModal() {
                editModal.hidden = true;
                document.body.style.overflow = "";
                editingMessageId = null;
                editForm.reset();
                editError.textContent = "";
            }

            editText.addEventListener("input", function() {
                editCharCount.textContent = editText.value.length;
            });

            document.getElementById("closeEditModal").addEventListener("click", closeEditModal);
            document.getElementById("cancelEditModal").addEventListener("click", closeEditModal);

            editModal.addEventListener("click", function(event) {
                if (event.target === editModal) closeEditModal();
            });

            document.addEventListener("click", function() {
                closeAllMessageMenus();
            });

            document.addEventListener("keydown", function(event) {
                if (event.key === "Escape") {
                    closeAllMessageMenus();
                    if (!editModal.hidden) closeEditModal();
                }
            });

            editForm.addEventListener("submit", async function(event) {
                event.preventDefault();
                const cleaned = editText.value.trim();

                if (!cleaned) {
                    editError.textContent = "A message cannot be empty.";
                    editText.focus();
                    return;
                }
                if (cleaned.length > 500) {
                    editError.textContent = "Your message must not exceed 500 characters.";
                    return;
                }
                if (cleaned === originalMessageText) {
                    closeEditModal();
                    return;
                }
                if (!editingMessageId) return;

                saveEditButton.disabled = true;
                saveEditButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

                try {
                    await manageMessage("edit", editingMessageId, cleaned);
                    closeEditModal();
                    lastRenderedSignature = "";
                    await loadMessages();
                } catch (error) {
                    editError.textContent = error.message || "Unable to edit your message.";
                } finally {
                    saveEditButton.disabled = false;
                    saveEditButton.innerHTML = '<i class="bi bi-check2"></i> Save changes';
                }
            });

            async function unsendMessage(id) {
                if (!window.confirm("Unsend this message? It will be removed from the chat for everyone.")) return;

                try {
                    await manageMessage("unsend", id);
                    lastRenderedSignature = "";
                    await loadMessages();
                } catch (error) {
                    alert(error.message || "Unable to unsend your message.");
                }
            }

            async function loadMessages() {
                try {
                    const response = await fetch("shout_box_fetch.php", {
                        method: "GET",
                        cache: "no-store",
                        headers: {
                            "Accept": "application/json"
                        }
                    });

                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || "Unable to load messages.");
                    }

                    renderMessages(data.messages || []);
                } catch (error) {
                    if (emptyMessage && messages.children.length <= 1) {
                        emptyMessage.textContent = "Unable to load messages. Please refresh the page.";
                    }
                    console.error("Shout Box fetch error:", error);
                }
            }

            input.addEventListener("input", function() {
                charCount.textContent = input.value.length;
            });

            form.addEventListener("submit", async function(event) {
                event.preventDefault();

                const message = input.value.trim();
                if (!message) return;

                sendButton.disabled = true;

                try {
                    const body = new URLSearchParams();
                    body.set("message", message);
                    body.set("csrf_token", csrfToken);

                    const response = await fetch("shout_box_send.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
                            "Accept": "application/json"
                        },
                        body: body.toString()
                    });

                    const data = await response.json();
                    if (!response.ok || !data.success) {
                        throw new Error(data.message || "Message could not be sent.");
                    }

                    input.value = "";
                    charCount.textContent = "0";
                    await loadMessages();
                    input.focus();
                } catch (error) {
                    alert(error.message || "Unable to send your message. Please try again.");
                    console.error("Shout Box send error:", error);
                } finally {
                    sendButton.disabled = false;
                }
            });

            loadMessages();
            window.setInterval(loadMessages, 3000);
        });
    </script>
</body>

</html>