<?php

include './globals/checks.php';

require_once '../src/connection.php';

$db = $mysqli;

if (!$db instanceof mysqli) exit('Database connection is unavailable.');

$db->set_charset('utf8mb4');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (empty($_SESSION['practice_csrf'])) $_SESSION['practice_csrf'] = bin2hex(random_bytes(32));

$csrf = $_SESSION['practice_csrf'];

function e($v)

{

    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function rows($db, $sql, $types = '', $args = [])

{

    $s = $db->prepare($sql);

    if (!$s) throw new Exception($db->error);

    if ($types) $s->bind_param($types, ...$args);

    $s->execute();

    $r = $s->get_result()->fetch_all(MYSQLI_ASSOC);

    $s->close();

    return $r;
}

function safe_rich($html)

{

    // Allow common editor formatting while removing scripts, embedded content and event handlers.

    $html = strip_tags((string)$html, '<p><br><strong><b><em><i><u><s><sub><sup><ol><ul><li><blockquote><pre><code><span><div><h1><h2><h3><h4><a>');

    $html = preg_replace("~\\s+on[a-z]+\\s*=\\s*(\"[^\"]*\"|'[^']*'|[^\\s>]+)~i", '', $html);

    $html = preg_replace("~(href|src)\\s*=\\s*([\"\'])\\s*javascript:[^\"\']*\\2~i", '$1="#"', $html);

    return $html;
}

$msg = '';

$error = '';

$graded = false;

$score = 0;

$total = 0;

$results = [];

$questions = [];

try {

    $subjects = rows($db, "SELECT id,subject_code,subject_name FROM subjects WHERE status='active' ORDER BY subject_name");

    $topics = rows($db, "SELECT id,subject_id,topic_name FROM subject_topics WHERE status='active' ORDER BY topic_name");

    $subjectId = max(0, (int)($_POST['subject_id'] ?? $_GET['subject'] ?? 0));

    $topicId = max(0, (int)($_POST['topic_id'] ?? $_GET['topic'] ?? 0));

    $limit = (int)($_POST['question_count'] ?? $_GET['count'] ?? 10);

    if (!in_array($limit, [5, 10, 15, 20, 30, 50], true)) $limit = 10;



    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'grade') {

        if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) throw new Exception('Your session expired. Refresh the page and try again.');

        $ids = array_values(array_unique(array_filter(array_map('intval', $_POST['question_ids'] ?? []), fn($v) => $v > 0)));

        if (!$ids) throw new Exception('No questions were submitted. Please start the practice test again.');

        $ids = array_slice($ids, 0, 50);

        $marks = $_POST['answers'] ?? [];

        foreach ($ids as $qid) {

            $qrows = rows($db, "SELECT q.id,q.subject_id,q.topic_id,q.difficulty,q.question_text,q.explanation,s.subject_code,s.subject_name,t.topic_name FROM question_bank q JOIN subjects s ON s.id=q.subject_id JOIN subject_topics t ON t.id=q.topic_id WHERE q.id=? AND q.status='active' AND s.status='active' AND t.status='active'", 'i', [$qid]);

            if (!$qrows) continue;

            $q = $qrows[0];

            if ($subjectId && (int)$q['subject_id'] !== $subjectId) continue;

            if ($topicId && (int)$q['topic_id'] !== $topicId) continue;

            $choices = rows($db, 'SELECT choice_label,choice_text,is_correct FROM question_choices WHERE question_id=? ORDER BY FIELD(choice_label,\'A\',\'B\',\'C\',\'D\',\'E\',\'F\')', 'i', [$qid]);

            $correct = '';

            foreach ($choices as $c) if ((int)$c['is_correct'] === 1) $correct = $c['choice_label'];

            $picked = strtoupper((string)($marks[$qid] ?? ''));

            $isCorrect = ($picked !== '' && $picked === $correct);

            if ($isCorrect) $score++;

            $q['choices'] = $choices;

            $q['picked'] = $picked;

            $q['correct'] = $correct;

            $q['is_correct'] = $isCorrect;

            $results[] = $q;
        }

        $total = count($results);

        $graded = true;

        if (!$total) throw new Exception('The submitted questions are no longer available. Please start again.');
    } else {

        $sql = "SELECT q.id,q.subject_id,q.topic_id,q.difficulty,q.question_text,q.explanation,s.subject_code,s.subject_name,t.topic_name FROM question_bank q JOIN subjects s ON s.id=q.subject_id JOIN subject_topics t ON t.id=q.topic_id WHERE q.status='active' AND s.status='active' AND t.status='active'";

        $types = '';

        $args = [];

        if ($subjectId) {

            $sql .= ' AND q.subject_id=?';

            $types .= 'i';

            $args[] = $subjectId;
        }

        if ($topicId) {

            $sql .= ' AND q.topic_id=?';

            $types .= 'i';

            $args[] = $topicId;
        }

        $sql .= ' ORDER BY RAND() LIMIT ' . (int)$limit;

        $questions = rows($db, $sql, $types, $args);

        foreach ($questions as &$q) $q['choices'] = rows($db, 'SELECT choice_label,choice_text FROM question_choices WHERE question_id=? ORDER BY FIELD(choice_label,\'A\',\'B\',\'C\',\'D\',\'E\',\'F\')', 'i', [$q['id']]);

        unset($q);
    }
} catch (Throwable $x) {

    $error = $x->getMessage();
}

?>

<!DOCTYPE html>

<html lang="en">

<?php include 'globals/head.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css">

<style>
    :root {

        --qp-primary: #4f46e5;

        --qp-text: #1f2937;

        --qp-muted: #6b7280;

        --qp-border: #e5e7eb
    }



    .page-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

        margin-bottom: 24px
    }



    .page-header h2 {

        margin: 0;

        font-size: 1.65rem;

        font-weight: 750;

        color: var(--qp-text)
    }



    .page-header p {

        margin: 6px 0 0;

        color: var(--qp-muted);

        font-size: .92rem
    }



    .qp-card {

        background: #fff;

        border: 1px solid var(--qp-border);

        border-radius: 14px;

        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);

        overflow: hidden;

        margin-bottom: 20px
    }



    .qp-head {

        display: flex;

        align-items: center;

        gap: 14px;

        padding: 20px 24px;

        background: linear-gradient(135deg, #f8faff, #f5f3ff);

        border-bottom: 1px solid var(--qp-border)
    }



    .qp-icon {

        width: 46px;

        height: 46px;

        display: flex;

        align-items: center;

        justify-content: center;

        flex-shrink: 0;

        border-radius: 12px;

        background: linear-gradient(135deg, #4f46e5, #6366f1);

        color: white;

        font-size: 1.25rem
    }



    .qp-head h5 {

        margin: 0;

        font-weight: 750;

        color: var(--qp-text);

        font-size: 1.05rem
    }



    .qp-head p {

        margin: 4px 0 0;

        color: var(--qp-muted);

        font-size: .85rem
    }



    .qp-body {

        padding: 24px
    }



    .qp-label {

        display: block;

        font-size: .87rem;

        font-weight: 650;

        color: #374151;

        margin-bottom: 7px
    }



    .qp-select {

        min-height: 44px;

        border: 1px solid #d1d5db;

        border-radius: 8px;

        padding: 9px 12px;

        width: 100%;

        background: #fff;

        color: var(--qp-text)
    }



    .qp-question {

        padding: 22px 0;

        border-bottom: 1px solid #eef0f4
    }



    .qp-question:first-child {

        padding-top: 0
    }



    .qp-question:last-child {

        border-bottom: 0;

        padding-bottom: 0
    }



    .qp-question-title {

        display: flex;

        gap: 12px;

        align-items: flex-start;

        margin-bottom: 16px
    }



    .qp-number {

        display: flex;

        align-items: center;

        justify-content: center;

        width: 34px;

        height: 34px;

        flex: 0 0 34px;

        border-radius: 10px;

        background: #eef2ff;

        color: var(--qp-primary);

        font-weight: 750
    }



    .qp-question-text {

        font-weight: 600;

        line-height: 1.65;

        color: var(--qp-text);

        min-width: 0;

        padding-top: 4px
    }



    .qp-meta {

        display: flex;

        gap: 8px;

        flex-wrap: wrap;

        margin-bottom: 10px
    }



    .qp-meta span {

        font-size: .75rem;

        color: #6b7280;

        background: #f3f4f6;

        padding: 4px 8px;

        border-radius: 6px
    }



    .qp-option {

        display: flex;

        gap: 11px;

        align-items: flex-start;

        padding: 12px 14px;

        border: 1px solid var(--qp-border);

        border-radius: 10px;

        margin: 8px 0;

        cursor: pointer;

        transition: .15s
    }



    .qp-option:hover {

        border-color: #a5b4fc;

        background: #fafaff
    }



    .qp-option:has(input:checked) {

        border-color: var(--qp-primary);

        background: #eef2ff
    }



    .qp-option input {

        margin-top: 5px;

        accent-color: var(--qp-primary);

        flex-shrink: 0
    }



    .qp-option-label {

        font-weight: 750;

        color: #4b5563;

        min-width: 22px
    }



    .qp-option-text {

        line-height: 1.55;

        overflow-wrap: anywhere
    }



    .qp-actions {

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 12px;

        padding-top: 20px;

        margin-top: 20px;

        border-top: 1px solid var(--qp-border)
    }



    .qp-btn {

        border-radius: 8px;

        padding: 10px 17px;

        font-weight: 650
    }



    .qp-btn-primary {

        background: var(--qp-primary);

        border-color: var(--qp-primary);

        color: #fff
    }



    .qp-btn-primary:hover {

        background: #4338ca;

        color: #fff
    }



    .qp-score {

        display: flex;

        align-items: center;

        gap: 18px;

        padding: 22px 24px
    }



    .qp-score-circle {

        width: 78px;

        height: 78px;

        flex: 0 0 78px;

        border-radius: 50%;

        display: grid;

        place-items: center;

        background: #eef2ff;

        color: var(--qp-primary);

        font-size: 1.2rem;

        font-weight: 800
    }



    .qp-result {

        padding: 14px;

        border-radius: 10px;

        margin-top: 12px;

        font-size: .9rem
    }



    .qp-result.good {

        background: #ecfdf3;

        border: 1px solid #bbf7d0
    }



    .qp-result.bad {

        background: #fff7ed;

        border: 1px solid #fed7aa
    }



    .qp-explanation {

        margin-top: 10px;

        color: #4b5563;

        font-size: .9rem
    }



    .qp-empty {

        padding: 34px;

        text-align: center;

        color: var(--qp-muted)
    }



    .qp-alert {

        border-radius: 10px
    }



    .ql-formula {

        font-family: inherit
    }



    @media(max-width:768px) {

        .page-header {

            align-items: flex-start;

            flex-direction: column
        }



        .qp-body {

            padding: 17px
        }



        .qp-head {

            padding: 17px
        }



        .qp-actions {

            align-items: stretch;

            flex-direction: column-reverse
        }



        .qp-actions>* {

            width: 100%
        }



        .qp-score {

            padding: 18px;

            gap: 13px
        }



        .qp-score-circle {

            width: 65px;

            height: 65px;

            flex-basis: 65px
        }

    }
</style>



<body>

    <?php include 'globals/sidebar.php'; ?>

    <?php include 'globals/topbar.php'; ?>

    <main class="main-content">

        <div class="content-wrapper">

            <div class="page-header">

                <div>

                    <h2>Practice Test</h2>

                    <p>Test your knowledge using questions saved in the question bank.</p>

                </div><a href="question_management.php" class="btn btn-outline-primary"><i class="bi bi-gear me-1"></i>Question Management</a>

            </div>

            <?php if ($error): ?><div class="alert alert-danger qp-alert"><?= e($error) ?></div><?php endif; ?>

            <?php if (!$graded): ?>

                <div class="qp-card">

                    <div class="qp-head">

                        <div class="qp-icon"><i class="bi bi-sliders"></i></div>

                        <div>

                            <h5>Customize Your Practice</h5>

                            <p>Choose a subject, topic, and number of questions.</p>

                        </div>

                    </div>

                    <div class="qp-body">

                        <form method="get" class="row g-3 align-items-end">

                            <div class="col-md-5"><label class="qp-label" for="subject">Subject</label><select class="qp-select" id="subject" name="subject">

                                    <option value="0">All subjects</option><?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (int)$subjectId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['subject_code'] . ' — ' . $s['subject_name']) ?></option><?php endforeach; ?>

                                </select></div>

                            <div class="col-md-4"><label class="qp-label" for="topic">Topic</label><select class="qp-select" id="topic" name="topic">

                                    <option value="0">All topics</option><?php foreach ($topics as $t): ?><option data-subject="<?= (int)$t['subject_id'] ?>" value="<?= (int)$t['id'] ?>" <?= (int)$topicId === (int)$t['id'] ? 'selected' : '' ?>><?= e($t['topic_name']) ?></option><?php endforeach; ?>

                                </select></div>

                            <div class="col-md-3"><label class="qp-label" for="count">Questions</label><select class="qp-select" id="count" name="count"><?php foreach ([5, 10, 15, 20, 30, 50] as $n): ?><option value="<?= $n ?>" <?= $limit === $n ? 'selected' : '' ?>><?= $n ?> questions</option><?php endforeach; ?></select></div>

                            <div class="col-12"><button class="btn qp-btn qp-btn-primary"><i class="bi bi-arrow-repeat me-1"></i>Load Practice Questions</button></div>

                        </form>

                    </div>

                </div>

                <?php if (!$questions): ?><div class="qp-card">

                        <div class="qp-empty"><i class="bi bi-journal-x fs-2 d-block mb-2"></i><strong>No active questions found.</strong>

                            <div class="mt-1">Try a different subject or topic, or add active questions to the question bank.</div>

                        </div>

                    </div>

                <?php else: ?>

                    <form method="post" id="practiceForm"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="grade"><input type="hidden" name="subject_id" value="<?= (int)$subjectId ?>"><input type="hidden" name="topic_id" value="<?= (int)$topicId ?>"><input type="hidden" name="question_count" value="<?= $limit ?>">

                        <div class="qp-card">

                            <div class="qp-head">

                                <div class="qp-icon"><i class="bi bi-patch-question"></i></div>

                                <div>

                                    <h5>Your Questions</h5>

                                    <p><?= count($questions) ?> question<?= count($questions) === 1 ? '' : 's' ?> · Select one answer for each question.</p>

                                </div>

                            </div>

                            <div class="qp-body">

                                <?php foreach ($questions as $i => $q): ?><section class="qp-question" id="question-<?= (int)$q['id'] ?>"><input type="hidden" name="question_ids[]" value="<?= (int)$q['id'] ?>">

                                        <div class="qp-meta"><span><?= e($q['subject_code']) ?></span><span><?= e($q['topic_name']) ?></span><span><?= e(ucfirst($q['difficulty'])) ?></span></div>

                                        <div class="qp-question-title">

                                            <div class="qp-number"><?= $i + 1 ?></div>

                                            <div class="qp-question-text rich-content"><?= safe_rich($q['question_text']) ?></div>

                                        </div><?php foreach ($q['choices'] as $c): ?><label class="qp-option"><input type="radio" name="answers[<?= (int)$q['id'] ?>]" value="<?= e($c['choice_label']) ?>"><span class="qp-option-label"><?= e($c['choice_label']) ?>.</span><span class="qp-option-text rich-content"><?= safe_rich($c['choice_text']) ?></span></label><?php endforeach; ?>

                                    </section><?php endforeach; ?>

                                <div class="qp-actions"><span class="text-secondary small"><i class="bi bi-info-circle me-1"></i>Your score will appear after submission.</span><button type="submit" class="btn qp-btn qp-btn-primary"><i class="bi bi-check2-circle me-1"></i>Submit Answers</button></div>

                            </div>

                        </div>

                    </form>

                <?php endif; ?>

            <?php else: ?>

                <div class="qp-card">

                    <div class="qp-score">

                        <div class="qp-score-circle"><?= number_format(($score / max(1, $total)) * 100, 0) ?>%</div>

                        <div>

                            <h4 class="mb-1 fw-bold">Practice Results</h4>

                            <div class="text-secondary">You answered <strong><?= $score ?> of <?= $total ?></strong> questions correctly.</div>

                            <div class="text-secondary small mt-1"><?= ($total - $score) ?> incorrect or unanswered</div>

                        </div>

                    </div>

                </div>

                <div class="qp-card">

                    <div class="qp-head">

                        <div class="qp-icon"><i class="bi bi-clipboard-check"></i></div>

                        <div>

                            <h5>Review Answers</h5>

                            <p>Check the correct answers and explanations where available.</p>

                        </div>

                    </div>

                    <div class="qp-body">

                        <?php foreach ($results as $i => $q): ?><section class="qp-question">

                                <div class="qp-question-title">

                                    <div class="qp-number"><?= $i + 1 ?></div>

                                    <div class="qp-question-text rich-content"><?= safe_rich($q['question_text']) ?></div>

                                </div><?php foreach ($q['choices'] as $c): $correctChoice = $c['choice_label'] === $q['correct'];

                                            $pickedChoice = $c['choice_label'] === $q['picked']; ?><div class="qp-option" style="cursor:default;<?= $correctChoice ? 'border-color:#86efac;background:#f0fdf4;' : ($pickedChoice ? 'border-color:#fdba74;background:#fff7ed;' : '') ?>"><span class="qp-option-label"><?= e($c['choice_label']) ?>.</span><span class="qp-option-text rich-content"><?= safe_rich($c['choice_text']) ?><?php if ($correctChoice): ?> <strong class="text-success ms-1">Correct answer</strong><?php elseif ($pickedChoice): ?> <strong class="text-warning-emphasis ms-1">Your answer</strong><?php endif; ?></span></div><?php endforeach; ?><div class="qp-result <?= $q['is_correct'] ? 'good' : 'bad' ?>"><strong><?= $q['is_correct'] ? 'Correct!' : 'Not quite.' ?></strong> <?php if (!$q['picked']): ?>You did not select an answer. <?php else: ?>Your answer: <?= e($q['picked']) ?>. <?php endif; ?>Correct answer: <?= e($q['correct'] ?: 'Not configured') ?>.</div><?php if (trim(strip_tags((string)$q['explanation'])) !== ''): ?><div class="qp-explanation"><strong>Explanation:</strong> <span class="rich-content"><?= safe_rich($q['explanation']) ?></span></div><?php endif; ?>

                            </section><?php endforeach; ?>

                        <div class="qp-actions"><a class="btn btn-outline-secondary qp-btn" href="question_practice.php"><i class="bi bi-arrow-left me-1"></i>Back to Setup</a><a class="btn qp-btn qp-btn-primary" href="question_practice.php?subject=<?= (int)$subjectId ?>&topic=<?= (int)$topicId ?>&count=<?= $limit ?>"><i class="bi bi-arrow-repeat me-1"></i>Try Another Set</a></div>

                    </div>

                </div>

            <?php endif; ?>

            <div class="text-center small text-secondary mt-4">ETS-Async Learning Portal · Practice Test</div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js"></script>

    <script>
        (() => {

            const s = document.getElementById('subject'),

                t = document.getElementById('topic');

            if (s && t) {

                function filterTopics() {

                    [...t.options].forEach((o, i) => {

                        if (i === 0) return;

                        o.hidden = s.value !== '0' && o.dataset.subject !== s.value;

                    });

                    if (t.selectedOptions[0]?.hidden) t.value = '0';

                }

                s.addEventListener('change', filterTopics);

                filterTopics();

            }

            document.querySelectorAll('.rich-content .ql-formula').forEach(el => {

                const latex = el.getAttribute('data-value');

                if (latex && window.katex) {

                    try {

                        window.katex.render(latex, el, {

                            throwOnError: false,

                            displayMode: false

                        });

                    } catch (e) {}

                }

            });

            // Render plain-text LaTeX delimiters in question text, choices, and explanations.
            // Quill formula embeds (.ql-formula) are rendered separately above.
            document.querySelectorAll('.rich-content').forEach(el => {
                if (window.renderMathInElement) {
                    try {
                        window.renderMathInElement(el, {
                            delimiters: [{
                                    left: '$$',
                                    right: '$$',
                                    display: true
                                },
                                {
                                    left: '\\[',
                                    right: '\\]',
                                    display: true
                                },
                                {
                                    left: '\\(',
                                    right: '\\)',
                                    display: false
                                },
                                {
                                    left: '$',
                                    right: '$',
                                    display: false
                                }
                            ],
                            ignoredTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code', 'option'],
                            throwOnError: false,
                            strict: 'ignore'
                        });
                    } catch (e) {
                        console.warn('Could not render LaTeX in this content block.', e);
                    }
                }
            });
            const f = document.getElementById('practiceForm');

            if (f) f.addEventListener('submit', e => {

                if (!confirm('Submit your answers and view your score?')) e.preventDefault();

            });

        })();
    </script>

    <?php include 'globals/scripts.php'; ?>

</body>



</html>