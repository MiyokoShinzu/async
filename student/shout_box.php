<?php
/* =========================================================
   STUDENT SHOUT BOX - ETS-Async Learning Portal
   Initial front-end example. Posts/comments are not yet saved
   to MySQL; connect the forms to backend endpoints for that.
========================================================= */
session_start();
if (
    !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_id'], $_SESSION['user']['access']) ||
    $_SESSION['user']['access'] !== 'student'
) {
    header('Location: ../login.php');
    exit;
}
$user = $_SESSION['user'];
$firstName = trim((string)($user['first_name'] ?? $user['firstname'] ?? $user['name'] ?? 'Student'));
$lastName = trim((string)($user['last_name'] ?? $user['lastname'] ?? ''));
$displayName = trim($firstName . ' ' . $lastName) ?: 'Student';
$initials = strtoupper(substr($firstName, 0, 1) . ($lastName ? substr($lastName, 0, 1) : ''));
include 'globals/head.php';
?>

<body>
    <?php include 'globals/sidebar.php'; ?>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php include 'globals/topbar.php'; ?>

    <main class="main-content">
        <div class="content-wrapper">
            <section class="shout-header">
                <div>
                    <div class="eyebrow"><i class="bi bi-chat-square-dots-fill"></i> STUDENT COMMUNITY</div>
                    <h1>Shout Box</h1>
                    <p>Share ideas, ask questions, and stay connected with your classmates.</p>
                </div>
                <span class="community-status"><i></i> Community space</span>
            </section>

            <div class="shout-layout">
                <section class="feed-column">
                    <div class="shout-card composer">
                        <div class="author-row">
                            <div class="avatar current-avatar"><?= htmlspecialchars($initials ?: 'ST', ENT_QUOTES, 'UTF-8') ?></div>
                            <div><strong><?= htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8') ?></strong><small>What would you like to share?</small></div>
                        </div>
                        <form id="shoutForm">
                            <label class="sr-only" for="shoutMessage">Write your shout</label>
                            <textarea id="shoutMessage" maxlength="500" placeholder="Write a message, ask a question, or share something with your classmates..." required></textarea>
                            <div class="composer-bottom"><small><span id="charCount">0</span>/500</small><button class="primary-btn" type="submit"><i class="bi bi-send-fill"></i> Post Shout</button></div>
                        </form>
                    </div>

                    <div class="feed-heading">
                        <div>
                            <h2>Community Feed</h2>
                            <p>Recent messages from the learning community</p>
                        </div>
                        <select id="feedFilter" aria-label="Filter posts">
                            <option value="all">All posts</option>
                            <option value="questions">Questions</option>
                            <option value="announcements">Announcements</option>
                        </select>
                    </div>

                    <div id="shoutFeed" class="shout-feed">
                        <article class="shout-card post" data-category="announcements">
                            <div class="post-head">
                                <div class="author-row">
                                    <div class="avatar avatar-blue">JM</div>
                                    <div><strong>Juan Miguel Santos</strong><small>BS Information Technology · 12 minutes ago</small></div>
                                </div><span class="tag tag-announcement"><i class="bi bi-megaphone-fill"></i> Announcement</span>
                            </div>
                            <p class="post-text">Good day, everyone! Just a reminder to check our learning materials for this week. Let's stay updated and complete our activities before the deadline. Good luck!</p>
                            <div class="post-actions"><button type="button" class="like-btn"><i class="bi bi-heart"></i> Like <span class="like-count">4</span></button><button type="button" class="comment-toggle"><i class="bi bi-chat"></i> Comment <span class="comment-count">2</span></button></div>
                            <div class="comment-area" hidden>
                                <div class="comment"><b>Maria L.:</b> Thank you for the reminder!</div>
                                <div class="comment"><b>Mark D.:</b> Noted. Good luck, everyone!</div>
                                <form class="comment-form"><input maxlength="250" placeholder="Write a comment..." required><button aria-label="Send comment"><i class="bi bi-send-fill"></i></button></form>
                            </div>
                        </article>

                        <article class="shout-card post" data-category="questions">
                            <div class="post-head">
                                <div class="author-row">
                                    <div class="avatar avatar-cyan">AR</div>
                                    <div><strong>Angela Reyes</strong><small>BS Information Technology · 35 minutes ago</small></div>
                                </div><span class="tag tag-question"><i class="bi bi-question-circle-fill"></i> Question</span>
                            </div>
                            <p class="post-text">Hello, classmates! Does anyone know if we need to submit the programming activity as a PDF or should we upload the source code? Thank you!</p>
                            <div class="post-actions"><button type="button" class="like-btn"><i class="bi bi-heart"></i> Like <span clas <style>
                                        .shout-main-content { min-height: 0; }
                                        .shout-content-wrapper {
                                        min-height: calc(100vh - 125px);
                                        display: flex;
                                        flex-direction: column;
                                        }

                                        .shout-page-heading {
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: flex-end;
                                        gap: 16px;
                                        margin-bottom: 18px;
                                        flex: 0 0 auto;
                                        }

                                        .eyebrow {
                                        color: #2474c7;
                                        font-size: 11px;
                                        font-weight: 800;
                                        letter-spacing: .1em;
                                        margin-bottom: 7px;
                                        }

                                        .eyebrow i { margin-right: 6px; }
                                        .shout-page-heading h1 {
                                        margin: 0 0 5px;
                                        color: var(--text-color, #172033);
                                        font-size: clamp(25px, 3vw, 32px);
                                        font-weight: 800;
                                        }

                                        .shout-page-heading p {
                                        margin: 0;
                                        color: var(--text-secondary, #667085);
                                        font-size: 13px;
                                        }

                                        .community-status {
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

                                        .community-status i, .chat-header-details span i {
                                        width: 8px;
                                        height: 8px;
                                        display: inline-block;
                                        border-radius: 50%;
                                        background: #16a36a;
                                        }

                                        .chat-panel {
                                        width: 100%;
                                        max-width: 1100px;
                                        height: clamp(430px, calc(100vh - 225px), 760px);
                                        min-height: 430px;
                                        margin: 0 auto;
                                        display: flex;
                                        flex-direction: column;
                                        overflow: hidden;
                                        border: 1px solid var(--activity-border, #e2e8f0);
                                        border-radius: 17px;
                                        background: var(--activity-card-bg, #fff);
                                        box-shadow: 0 10px 35px rgba(15, 23, 42, .07);
                                        }

                                        .chat-header {
                                        min-height: 72px;
                                        flex: 0 0 auto;
                                        display: flex;
                                        align-items: center;
                                        gap: 12px;
                                        padding: 13px 20px;
                                        border-bottom: 1px solid var(--activity-border, #e9edf3);
                                        background: var(--activity-card-bg, #fff);
                                        }

                                        .chat-header-icon {
                                        width: 43px;
                                        height: 43px;
                                        flex: 0 0 43px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        border-radius: 13px;
                                        background: #e8f3ff;
                                        color: #246fc0;
                                        font-size: 19px;
                                        }

                                        .chat-header-details { min-width: 0; }
                                        .chat-header-details strong {
                                        display: block;
                                        color: var(--text-color, #172033);
                                        font-size: 14px;
                                        font-weight: 800;
                                        }

                                        .chat-header-details span {
                                        display: flex;
                                        align-items: center;
                                        gap: 7px;
                                        margin-top: 4px;
                                        color: var(--text-secondary, #7b8494);
                                        font-size: 11px;
                                        }

                                        .chat-header-details span i { width: 7px; height: 7px; }
                                        .chat-header-label {
                                        margin-left: auto;
                                        padding: 6px 9px;
                                        border-radius: 6px;
                                        background: #edf5ff;
                                        color: #286bb0;
                                        font-size: 9px;
                                        font-weight: 800;
                                        letter-spacing: .07em;
                                        }

                                        /* The message area intentionally has no scrollbar. */
                                        .chat-messages {
                                        min-height: 0;
                                        flex: 1 1 auto;
                                        display: flex;
                                        flex-direction: column;
                                        justify-content: flex-end;
                                        gap: 15px;
                                        padding: 20px 22px;
                                        overflow: hidden;
                                        background:
                                        radial-gradient(circle at 1px 1px, rgba(130, 151, 178, .10) 1px, transparent 0) 0 0 / 20px 20px,
                                        var(--activity-progress-bg, #f7f9fc);
                                        }

                                        .chat-date-divider {
                                        display: flex;
                                        align-items: center;
                                        gap: 12px;
                                        justify-content: center;
                                        margin: 0 0 2px;
                                        color: var(--text-secondary, #8a94a4);
                                        font-size: 10px;
                                        }

                                        .chat-date-divider::before, .chat-date-divider::after {
                                        content: "";
                                        width: min(18%, 100px);
                                        height: 1px;
                                        background: var(--activity-border, #dfe5ed);
                                        }

                                        .chat-message-row {
                                        max-width: 82%;
                                        display: flex;
                                        align-items: flex-end;
                                        gap: 9px;
                                        }

                                        .chat-message-row.own-message {
                                        align-self: flex-end;
                                        justify-content: flex-end;
                                        }

                                        .chat-avatar {
                                        width: 32px;
                                        height: 32px;
                                        flex: 0 0 32px;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        border-radius: 50%;
                                        font-size: 10px;
                                        font-weight: 800;
                                        }

                                        .avatar-blue { background: #e5efff; color: #2458b7; }
                                        .avatar-cyan { background: #e1f8fc; color: #087d99; }
                                        .avatar-violet { background: #f0ebff; color: #6952b5; }
                                        .current-avatar { background: linear-gradient(135deg, #1769c2, #28a9cf); color: #fff; }

                                        .chat-message-content { min-width: 0; }
                                        .chat-sender {
                                        display: flex;
                                        align-items: center;
                                        gap: 8px;
                                        margin: 0 0 5px 3px;
                                        color: var(--text-color, #344054);
                                        font-size: 10px;
                                        font-weight: 800;
                                        }

                                        .chat-sender time {
                                        color: var(--text-secondary, #929baa);
                                        font-size: 9px;
                                        font-weight: 500;
                                        }

                                        .chat-bubble {
                                        width: fit-content;
                                        max-width: 100%;
                                        padding: 10px 13px;
                                        border: 1px solid var(--activity-border, #e5eaf1);
                                        border-radius: 4px 13px 13px 13px;
                                        background: var(--activity-card-bg, #fff);
                                        color: var(--text-color, #344054);
                                        font-size: 12px;
                                        line-height: 1.55;
                                        overflow-wrap: anywhere;
                                        box-shadow: 0 2px 5px rgba(15, 23, 42, .025);
                                        }

                                        .own-message .chat-bubble {
                                        margin-left: auto;
                                        border-color: #246fc0;
                                        border-radius: 13px 4px 13px 13px;
                                        background: #246fc0;
                                        color: #fff;
                                        }

                                        .own-message .chat-sender { justify-content: flex-end; }

                                        .chat-composer {
                                        min-height: 76px;
                                        flex: 0 0 auto;
                                        display: flex;
                                        align-items: center;
                                        gap: 10px;
                                        padding: 13px 18px;
                                        border-top: 1px solid var(--activity-border, #e5eaf1);
                                        background: var(--activity-card-bg, #fff);
                                        }

                                        .chat-composer input {
                                        width: 100%;
                                        min-width: 0;
                                        height: 44px;
                                        padding: 0 15px;
                                        border: 1px solid var(--activity-border, #dfe5ed);
                                        border-radius: 11px;
                                        outline: none;
                                        background: var(--activity-progress-bg, #f8fafc);
                                        color: var(--text-color, #172033);
                                        font: inherit;
                                        font-size: 12px;
                                        transition: border-color .2s, box-shadow .2s;
                                        }

                                        .chat-composer input:focus {
                                        border-color: #398bd0;
                                        box-shadow: 0 0 0 3px rgba(57, 139, 208, .12);
                                        }

                                        .chat-composer input::placeholder { color: #9aa4b2; }
                                        .chat-char-count {
                                        flex: 0 0 auto;
                                        color: var(--text-secondary, #929baa);
                                        font-size: 9px;
                                        }

                                        .chat-composer button {
                                        height: 43px;
                                        flex: 0 0 auto;
                                        display: inline-flex;
                                        align-items: center;
                                        justify-content: center;
                                        gap: 8px;
                                        padding: 0 17px;
                                        border: 0;
                                        border-radius: 10px;
                                        background: #246fc0;
                                        color: #fff;
                                        font-size: 11px;
                                        font-weight: 800;
                                        cursor: pointer;
                                        transition: background .2s;
                                        }

                                        .chat-composer button:hover { background: #185da8; }
                                        .chat-footer-note {
                                        flex: 0 0 auto;
                                        padding: 0 18px 11px;
                                        background: var(--activity-card-bg, #fff);
                                        color: var(--text-secondary, #929baa);
                                        font-size: 9px;
                                        }

                                        .chat-footer-note i { margin-right: 4px; color: #398bd0; }
                                        .visually-hidden {
                                        position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px;
                                        overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;
                                        }

                                        @media (max-width: 700px) {
                                        .shout-content-wrapper { min-height: calc(100vh - 110px); }
                                        .chat-panel { height: calc(100vh - 205px); min-height: 390px; border-radius: 13px; }
                                        .chat-header { min-height: 65px; padding: 11px 14px; }
                                        .chat-messages { padding: 15px 12px; gap: 13px; }
                                        .chat-message-row { max-width: 92%; }
                                        .chat-composer { gap: 7px; padding: 10px; }
                                        .chat-composer input { height: 42px; padding: 0 11px; }
                                        .chat-composer button { width: 43px; padding: 0; }
                                        .chat-composer button span { display: none; }
                                        .chat-char-count { display: none; }
                                        }

                                        @media (max-width: 420px) {
                                        .shout-page-heading { align-items: flex-start; flex-direction: column; }
                                        .community-status { align-self: flex-start; }
                                        .chat-header-label { display: none; }
                                        .chat-bubble { font-size: 11px; padding: 9px 11px; }
                                        .chat-message-row { max-width: 96%; }
                                        }
                                        </style>

                                        <script>
                                            document.addEventListener('DOMContentLoaded', () => {
                                                const form = document.getElementById('chatForm');
                                                const input = document.getElementById('chatInput');
                                                const messages = document.getElementById('chatMessages');
                                                const count = document.getElementById('chatCharCount');

                                                const currentName = <?= json_encode($displayName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
                                                const currentInitials = <?= json_encode($initials ?: 'ST', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

                                                const escapeHTML = value => value.replace(/[&<>"']/g, char => ({
                                                    '&': '&amp;',
                                                    '<': '&lt;',
                                                    '>': '&gt;',
                                                    '"': '&quot;',
                                                    "'": '&#039;'
                                                })[char]);

                                                input.addEventListener('input', () => {
                                                    count.textContent = input.value.length;
                                                });

                                                form.addEventListener('submit', event => {
                                                    event.preventDefault();
                                                    const message = input.value.trim();
                                                    if (!message) return;

                                                    const row = document.createElement('div');
                                                    row.className = 'chat-message-row own-message';
                                                    const now = new Date();
                                                    const time = now.toLocaleTimeString([], {
                                                        hour: 'numeric',
                                                        minute: '2-digit'
                                                    });

                                                    row.innerHTML = `
                <div class="chat-message-content">
                    <div class="chat-sender">${escapeHTML(currentName)} <time>${escapeHTML(time)}</time></div>
                    <div class="chat-bubble">${escapeHTML(message).replace(/\n/g, '<br>')}</div>
                </div>
                <div class="chat-avatar current-avatar">${escapeHTML(currentInitials)}</div>
            `;

                                                    messages.appendChild(row);

                                                    // Keep the chat area non-scrollable and show only the newest messages.
                                                    const chatRows = messages.querySelectorAll('.chat-message-row');
                                                    while (chatRows.length > 5) {
                                                        chatRows[0].remove();
                                                        break;
                                                    }

                                                    input.value = '';
                                                    count.textContent = '0';
                                                });
                                            });
                                        </script>
                                        lue.trim();
                                        if (!message) return;

                                        const row = document.createElement('div');
                                        row.className = 'chat-message-row own-message';
                                        const now = new Date();
                                        const time = now.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });

                                        row.innerHTML = `
                                        <div class="chat-message-content">
                                            <div class="chat-sender">${escapeHTML(currentName)} <time>${escapeHTML(time)}</time></div>
                                            <div class="chat-bubble">${escapeHTML(message).replace(/\n/g, '<br>')}</div>
                                        </div>
                                        <div class="chat-avatar current-avatar">${escapeHTML(currentInitials)}</div>
                                        `;

                                        messages.appendChild(row);

                                        // Keep the chat area non-scrollable and show only the newest messages.
                                        const chatRows = messages.querySelectorAll('.chat-message-row');
                                        while (chatRows.length > 5) {
                                        chatRows[0].remove();
                                        break;
                                        }

                                        input.value = '';
                                        count.textContent = '0';
                                        });
                                        });
                                        </script>
                                        form.addEventListener('submit', event => {
                                        event.preventDefault();
                                        const message = messageInput.value.trim();
                                        if (!message) return;
                                        const post = document.createElement('article');
                                        post.className = 'shout-card post';
                                        post.dataset.category = 'all';
                                        post.innerHTML = `<div class="post-head">
                                            <div class="author-row">
                                                <div class="avatar current-avatar">${escapeHTML(currentInitials)}</div>
                                                <div><strong>${escapeHTML(currentName)}</strong><small>Just now</small></div>
                                            </div><span class="tag tag-general"><i class="bi bi-chat-dots-fill"></i> General</span>
                                        </div>
                                        <p class="post-text">${escapeHTML(message).replace(/\n/g,'<br>')}</p>
                                        <div class="post-actions"><button type="button" class="like-btn"><i class="bi bi-heart"></i> Like <span class="like-count">0</span></button><button type="button" class="comment-toggle"><i class="bi bi-chat"></i> Comment <span class="comment-count">0</span></button></div>
                                        <div class="comment-area" hidden>
                                            <form class="comment-form"><input maxlength="250" placeholder="Write a comment..." required><button aria-label="Send comment"><i class="bi bi-send-fill"></i></button></form>
                                        </div>`;
                                        feed.prepend(post);
                                        messageInput.value = '';
                                        charCount.textContent = '0';
                                        filter.value = 'all';
                                        applyFilter();
                                        });

                                        feed.addEventListener('click', event => {
                                        const like = event.target.closest('.like-btn');
                                        if (like) {
                                        const count = like.querySelector('.like-count');
                                        const liked = like.classList.toggle('liked');
                                        count.textContent = Math.max(0, (parseInt(count.textContent, 10) || 0) + (liked ? 1 : -1));
                                        like.querySelector('i').className = liked ? 'bi bi-heart-fill' : 'bi bi-heart';
                                        return;
                                        }
                                        const toggle = event.target.closest('.comment-toggle');
                                        if (toggle) {
                                        const area = toggle.closest('.post').querySelector('.comment-area');
                                        area.hidden = !area.hidden;
                                        }
                                        });

                                        feed.addEventListener('submit', event => {
                                        if (!event.target.matches('.comment-form')) return;
                                        event.preventDefault();
                                        const commentForm = event.target;
                                        const input = commentForm.querySelector('input');
                                        const value = input.value.trim();
                                        if (!value) return;
                                        const comment = document.createElement('div');
                                        comment.className = 'comment';
                                        comment.innerHTML = `<b>${escapeHTML(currentName)}:</b> ${escapeHTML(value)}`;
                                        commentForm.parentElement.insertBefore(comment, commentForm);
                                        const count = commentForm.closest('.post').querySelector('.comment-count');
                                        count.textContent = (parseInt(count.textContent, 10) || 0) + 1;
                                        input.value = '';
                                        });
                                        applyFilter();
                                        });
                                        </script>
</body>

</html>