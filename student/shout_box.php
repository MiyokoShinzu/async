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
                            <div class="post-actions"><button type="button" class="like-btn"><i class="bi bi-heart"></i> Like <span class="like-count">7</span></button><button type="button" class="comment-toggle"><i class="bi bi-chat"></i> Comment <span class="comment-count">1</span></button></div>
                            <div class="comment-area" hidden>
                                <div class="comment"><b>Kevin P.:</b> I think we need to upload both. Let's wait for the instructor's confirmation.</div>
                                <form class="comment-form"><input maxlength="250" placeholder="Write a comment..." required><button aria-label="Send comment"><i class="bi bi-send-fill"></i></button></form>
                            </div>
                        </article>

                        <article class="shout-card post" data-category="all">
                            <div class="post-head">
                                <div class="author-row">
                                    <div class="avatar avatar-violet">KC</div>
                                    <div><strong>Kyle Cruz</strong><small>BS Information Technology · 1 hour ago</small></div>
                                </div><span class="tag tag-general"><i class="bi bi-chat-dots-fill"></i> General</span>
                            </div>
                            <p class="post-text">Finally finished my Arduino LED blinking activity! It was challenging at first, but I learned a lot. Keep practicing, everyone!</p>
                            <div class="post-actions"><button type="button" class="like-btn"><i class="bi bi-heart"></i> Like <span class="like-count">3</span></button><button type="button" class="comment-toggle"><i class="bi bi-chat"></i> Comment <span class="comment-count">0</span></button></div>
                            <div class="comment-area" hidden>
                                <form class="comment-form"><input maxlength="250" placeholder="Write a comment..." required><button aria-label="Send comment"><i class="bi bi-send-fill"></i></button></form>
                            </div>
                        </article>
                    </div>
                    <div id="feedEmpty" class="empty-state" hidden><i class="bi bi-chat-square-text"></i><b>No posts found</b><small>Try another feed filter.</small></div>
                </section>

                <aside class="side-column">
                    <div class="shout-card side-card">
                        <div class="side-icon"><i class="bi bi-people-fill"></i></div>
                        <h3>Your learning community</h3>
                        <p>A space to communicate, collaborate, and learn from one another.</p>
                    </div>
                    <div class="shout-card side-card">
                        <h3><i class="bi bi-shield-check"></i> Community Guidelines</h3>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> Be respectful and supportive.</li>
                            <li><i class="bi bi-check-circle-fill"></i> Keep discussions related to learning.</li>
                            <li><i class="bi bi-check-circle-fill"></i> Do not share private information.</li>
                            <li><i class="bi bi-check-circle-fill"></i> Avoid spam and inappropriate content.</li>
                        </ul>
                    </div>
                    <div class="shout-card tip-card"><i class="bi bi-lightbulb-fill"></i>
                        <div><b>Have a question?</b>
                            <p>Post it in the Shout Box. Your classmates may be able to help!</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <?php require_once './globals/scripts.php'; ?>

    <style>
        .shout-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 18px;
            margin-bottom: 25px
        }

        .eyebrow {
            color: #2474c7;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .1em;
            margin-bottom: 8px
        }

        .eyebrow i {
            margin-right: 6px
        }

        .shout-header h1 {
            margin: 0 0 6px;
            color: var(--text-color, #172033);
            font-size: clamp(27px, 3vw, 34px);
            font-weight: 800
        }

        .shout-header p,
        .feed-heading p {
            margin: 0;
            color: var(--text-secondary, #667085);
            font-size: 13px;
            line-height: 1.6
        }

        .community-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 13px;
            border: 1px solid var(--activity-border, #e5eaf1);
            border-radius: 30px;
            background: var(--activity-card-bg, #fff);
            color: var(--text-secondary, #667085);
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap
        }

        .community-status i {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #16a36a
        }

        .shout-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 280px;
            gap: 20px;
            align-items: start
        }

        .feed-column,
        .side-column {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 16px
        }

        .shout-card {
            background: var(--activity-card-bg, #fff);
            border: 1px solid var(--activity-border, #e5eaf1);
            border-radius: 15px;
            box-shadow: 0 5px 18px rgba(15, 23, 42, .035)
        }

        .composer,
        .post {
            padding: 19px
        }

        .author-row {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0
        }

        .author-row strong {
            display: block;
            color: var(--text-color, #172033);
            font-size: 13px;
            font-weight: 800
        }

        .author-row small {
            display: block;
            margin-top: 3px;
            color: var(--text-secondary, #7b8494);
            font-size: 11px;
            line-height: 1.4
        }

        .avatar {
            width: 41px;
            height: 41px;
            flex: 0 0 41px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 800
        }

        .current-avatar {
            color: #fff;
            background: linear-gradient(135deg, #1769c2, #28a9cf)
        }

        .avatar-blue {
            background: #e8f0ff;
            color: #2458b7
        }

        .avatar-cyan {
            background: #e1f8fc;
            color: #087d99
        }

        .avatar-violet {
            background: #f0ebff;
            color: #6952b5
        }

        #shoutMessage {
            display: block;
            width: 100%;
            min-height: 105px;
            margin-top: 16px;
            padding: 13px 14px;
            resize: vertical;
            border: 1px solid var(--activity-border, #e1e7ef);
            border-radius: 11px;
            outline: none;
            background: var(--activity-progress-bg, #f8fafc);
            color: var(--text-color, #172033);
            font: inherit;
            font-size: 13px;
            line-height: 1.65
        }

        #shoutMessage:focus,
        .comment-form input:focus {
            border-color: #398bd0;
            box-shadow: 0 0 0 3px rgba(57, 139, 208, .12)
        }

        #shoutMessage::placeholder,
        .comment-form input::placeholder {
            color: #9aa4b2
        }

        .composer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 11px
        }

        .composer-bottom small {
            color: var(--text-secondary, #8992a1);
            font-size: 11px
        }

        .primary-btn {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 10px 16px;
            border: 0;
            border-radius: 9px;
            background: #246fc0;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer
        }

        .primary-btn:hover {
            background: #185da8
        }

        .feed-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px
        }

        .feed-heading h2 {
            margin: 0 0 4px;
            color: var(--text-color, #172033);
            font-size: 17px;
            font-weight: 800
        }

        .feed-heading select {
            max-width: 140px;
            padding: 9px 10px;
            border: 1px solid var(--activity-border, #e5eaf1);
            border-radius: 9px;
            background: var(--activity-card-bg, #fff);
            color: var(--text-color, #344054);
            font-size: 11px
        }

        .shout-feed {
            display: flex;
            flex-direction: column;
            gap: 14px
        }

        .post-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px
        }

        .tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 9px;
            border-radius: 30px;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap
        }

        .tag-announcement {
            background: #fff5dc;
            color: #98620a
        }

        .tag-question {
            background: #e8f6fc;
            color: #176b9c
        }

        .tag-general {
            background: #f0edfb;
            color: #6551a3
        }

        .post-text {
            margin: 16px 0;
            color: var(--text-color, #344054);
            font-size: 13px;
            line-height: 1.75;
            overflow-wrap: anywhere
        }

        .post-actions {
            display: flex;
            gap: 20px;
            padding-top: 12px;
            border-top: 1px solid var(--activity-border, #edf0f4)
        }

        .post-actions button {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 2px 0;
            border: 0;
            background: none;
            color: var(--text-secondary, #7b8494);
            font-size: 11px;
            font-weight: 700;
            cursor: pointer
        }

        .post-actions button:hover,
        .post-actions button.liked {
            color: #df4e69
        }

        .comment-area {
            margin-top: 12px;
            padding: 12px;
            border-radius: 10px;
            background: var(--activity-progress-bg, #f7f9fc)
        }

        .comment {
            margin-bottom: 8px;
            color: var(--text-secondary, #667085);
            font-size: 11px;
            line-height: 1.55
        }

        .comment b {
            color: var(--text-color, #344054)
        }

        .comment-form {
            display: flex;
            gap: 8px;
            margin-top: 9px
        }

        .comment-form input {
            width: 100%;
            min-width: 0;
            padding: 9px 10px;
            border: 1px solid var(--activity-border, #e1e7ef);
            border-radius: 8px;
            outline: 0;
            background: var(--activity-card-bg, #fff);
            color: var(--text-color, #172033);
            font-size: 11px
        }

        .comment-form button {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            border: 0;
            border-radius: 8px;
            background: #246fc0;
            color: #fff;
            cursor: pointer
        }

        .side-card {
            padding: 19px
        }

        .side-icon {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 13px;
            border-radius: 12px;
            background: #e8f3ff;
            color: #246fc0;
            font-size: 20px
        }

        .side-card h3 {
            margin: 0 0 8px;
            color: var(--text-color, #172033);
            font-size: 13px;
            font-weight: 800
        }

        .side-card p {
            margin: 0;
            color: var(--text-secondary, #7b8494);
            font-size: 11px;
            line-height: 1.7
        }

        .side-card h3 i {
            color: #2474c7;
            margin-right: 5px
        }

        .side-card ul {
            display: flex;
            flex-direction: column;
            gap: 11px;
            margin: 14px 0 0;
            padding: 0;
            list-style: none
        }

        .side-card li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: var(--text-secondary, #667085);
            font-size: 11px;
            line-height: 1.5
        }

        .side-card li i {
            color: #1a9b72
        }

        .tip-card {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 16px;
            background: #f0f8ff;
            border-color: #dcecfb
        }

        .tip-card>i {
            color: #226fc0;
            font-size: 20px
        }

        .tip-card b {
            display: block;
            margin-bottom: 4px;
            color: #245b91;
            font-size: 12px
        }

        .tip-card p {
            color: #54718e;
            font-size: 11px
        }

        .empty-state {
            padding: 30px;
            text-align: center;
            border: 1px dashed var(--activity-border, #d8e0ea);
            border-radius: 13px;
            color: var(--text-secondary, #7b8494)
        }

        .empty-state i,
        .empty-state b,
        .empty-state small {
            display: block
        }

        .empty-state i {
            font-size: 25px;
            margin-bottom: 8px
        }

        .empty-state b {
            color: var(--text-color, #344054);
            font-size: 13px
        }

        .empty-state small {
            margin-top: 4px;
            font-size: 11px
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0
        }

        @media(max-width:1000px) {
            .shout-layout {
                grid-template-columns: minmax(0, 1fr) 245px;
                gap: 15px
            }
        }

        @media(max-width:800px) {
            .shout-layout {
                grid-template-columns: 1fr
            }

            .side-column {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .tip-card {
                grid-column: 1/-1
            }
        }

        @media(max-width:560px) {
            .shout-header {
                align-items: flex-start;
                flex-direction: column
            }

            .composer,
            .post {
                padding: 15px
            }

            .post-head {
                flex-direction: column
            }

            .tag {
                margin-left: 52px
            }

            .side-column {
                grid-template-columns: 1fr
            }

            .tip-card {
                grid-column: auto
            }

            .feed-heading {
                align-items: flex-start;
                flex-direction: column
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('shoutForm');
            const messageInput = document.getElementById('shoutMessage');
            const charCount = document.getElementById('charCount');
            const feed = document.getElementById('shoutFeed');
            const filter = document.getElementById('feedFilter');
            const empty = document.getElementById('feedEmpty');
            const currentName = <?= json_encode($displayName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
            const currentInitials = <?= json_encode($initials ?: 'ST', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
            const escapeHTML = value => value.replace(/[&<>"']/g, c => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            } [c]));

            messageInput.addEventListener('input', () => charCount.textContent = messageInput.value.length);

            function applyFilter() {
                let visible = 0;
                feed.querySelectorAll('.post').forEach(post => {
                    const category = post.dataset.category;
                    const show = filter.value === 'all' || category === filter.value;
                    post.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                empty.hidden = visible > 0;
            }
            filter.addEventListener('change', applyFilter);

            form.addEventListener('submit', event => {
                event.preventDefault();
                const message = messageInput.value.trim();
                if (!message) return;
                const post = document.createElement('article');
                post.className = 'shout-card post';
                post.dataset.category = 'all';
                post.innerHTML = `<div class="post-head"><div class="author-row"><div class="avatar current-avatar">${escapeHTML(currentInitials)}</div><div><strong>${escapeHTML(currentName)}</strong><small>Just now</small></div></div><span class="tag tag-general"><i class="bi bi-chat-dots-fill"></i> General</span></div><p class="post-text">${escapeHTML(message).replace(/\n/g,'<br>')}</p><div class="post-actions"><button type="button" class="like-btn"><i class="bi bi-heart"></i> Like <span class="like-count">0</span></button><button type="button" class="comment-toggle"><i class="bi bi-chat"></i> Comment <span class="comment-count">0</span></button></div><div class="comment-area" hidden><form class="comment-form"><input maxlength="250" placeholder="Write a comment..." required><button aria-label="Send comment"><i class="bi bi-send-fill"></i></button></form></div>`;
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