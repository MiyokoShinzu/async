<?php
include './globals/checks.php';
require_once '../src/connection.php';
$db = $mysqli ?? null;
if (!$db instanceof mysqli) exit('Database connection is unavailable.');
$db->set_charset('utf8mb4');

if (empty($_SESSION['qsolution_csrf'])) $_SESSION['qsolution_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['qsolution_csrf'];
$msg = '';
$msgType = 'success';
$selectedQuestion = (int)($_GET['question_id'] ?? $_POST['question_id'] ?? 0);
$subjectFilter = (int)($_GET['subject_filter'] ?? $_POST['subject_filter'] ?? 0);
$topicFilter = (int)($_GET['topic_filter'] ?? $_POST['topic_filter'] ?? 0);

function qs_e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function qs_rows(mysqli $db, string $sql, string $types = '', array $args = []): array
{
    $stmt = $db->prepare($sql);
    if (!$stmt) throw new Exception($db->error);
    if ($types !== '') $stmt->bind_param($types, ...$args);
    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        throw new Exception($error);
    }
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $result;
}
function qs_exec(mysqli $db, string $sql, string $types = '', array $args = []): void
{
    $stmt = $db->prepare($sql);
    if (!$stmt) throw new Exception($db->error);
    if ($types !== '') $stmt->bind_param($types, ...$args);
    if (!$stmt->execute()) {
        $error = $stmt->error;
        $stmt->close();
        throw new Exception($error);
    }
    $stmt->close();
}
function qs_safe_rich(string $html): string
{
    $html = strip_tags($html, '<p><br><strong><b><em><i><u><s><sub><sup><ol><ul><li><blockquote><pre><code><span><div><h1><h2><h3><h4><a>');
    $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);
    return $html;
}
function qs_normalize(string $type, string $value): ?string
{
    $value = trim($value);
    if (in_array($type, ['text', 'simple_text', 'rich_text'], true)) return $value !== '' ? $value : null;
    if (!filter_var($value, FILTER_VALIDATE_URL)) return null;
    $p = parse_url($value);
    if (!$p || !in_array(strtolower($p['scheme'] ?? ''), ['http', 'https'], true)) return null;
    $host = strtolower($p['host'] ?? '');
    $path = $p['path'] ?? '';
    if ($type === 'image') {
        if (!in_array($host, ['drive.google.com', 'www.drive.google.com'], true)) return null;
        if (preg_match('~/file/d/([A-Za-z0-9_-]+)~', $path, $m)) return 'https://drive.google.com/file/d/' . $m[1] . '/view';
        parse_str($p['query'] ?? '', $query);
        if (!empty($query['id']) && preg_match('/^[A-Za-z0-9_-]+$/', (string)$query['id'])) return 'https://drive.google.com/file/d/' . $query['id'] . '/view';
        return null;
    }
    if ($type === 'youtube') {
        $id = '';
        if ($host === 'youtu.be') $id = explode('/', trim($path, '/'))[0] ?? '';
        elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            parse_str($p['query'] ?? '', $query);
            if (str_starts_with($path, '/watch')) $id = (string)($query['v'] ?? '');
            elseif (preg_match('~/(?:embed|shorts)/([A-Za-z0-9_-]+)~', $path, $m)) $id = $m[1];
        }
        return preg_match('/^[A-Za-z0-9_-]{11}$/', $id) ? 'https://www.youtube.com/watch?v=' . $id : null;
    }
    return null;
}
function qs_media_preview(string $type, string $url): string
{
    $p = parse_url($url);
    $host = strtolower($p['host'] ?? '');
    $path = $p['path'] ?? '';
    $embed = '';
    $label = '';
    if ($type === 'image' && in_array($host, ['drive.google.com', 'www.drive.google.com'], true)) {
        $id = '';
        if (preg_match('~/file/d/([A-Za-z0-9_-]+)~', $path, $m)) $id = $m[1];
        else {
            parse_str($p['query'] ?? '', $query);
            $id = (string)($query['id'] ?? '');
        }
        if (preg_match('/^[A-Za-z0-9_-]+$/', $id)) {
            $embed = 'https://drive.google.com/file/d/' . rawurlencode($id) . '/preview';
            $label = 'Solution image';
        }
    } elseif ($type === 'youtube') {
        $id = '';
        if ($host === 'youtu.be') $id = explode('/', trim($path, '/'))[0] ?? '';
        else {
            parse_str($p['query'] ?? '', $query);
            if (str_starts_with($path, '/watch')) $id = (string)($query['v'] ?? '');
            elseif (preg_match('~/(?:embed|shorts)/([A-Za-z0-9_-]+)~', $path, $m)) $id = $m[1];
        }
        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $id)) {
            $embed = 'https://www.youtube-nocookie.com/embed/' . rawurlencode($id);
            $label = 'Solution video';
        }
    }
    if (!$embed) return '<a href="' . qs_e($url) . '" target="_blank" rel="noopener noreferrer">Open media link</a>';
    return '<div class="media-preview"><iframe src="' . qs_e($embed) . '" title="' . qs_e($label) . '" loading="lazy" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe></div><a class="small" href="' . qs_e($url) . '" target="_blank" rel="noopener noreferrer">Open original <i class="bi bi-box-arrow-up-right"></i></a>';
}

try {
    $db->query("CREATE TABLE IF NOT EXISTS question_solutions (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        solution_type ENUM('text','simple_text','rich_text','image','youtube') NOT NULL,
        solution_content MEDIUMTEXT NOT NULL,
        display_order TINYINT UNSIGNED NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        KEY idx_question_solutions_question (question_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) throw new Exception('Session token expired. Refresh this page and try again.');
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'add_solution') {
            $selectedQuestion = (int)($_POST['question_id'] ?? 0);
            $type = (string)($_POST['solution_type'] ?? '');
            $content = qs_normalize($type, (string)($_POST['solution_content'] ?? ''));
            if ($selectedQuestion < 1 || !qs_rows($db, 'SELECT id FROM question_bank WHERE id = ?', 'i', [$selectedQuestion])) throw new Exception('Please select a valid question first.');
            if (!in_array($type, ['text', 'simple_text', 'rich_text', 'image', 'youtube'], true)) throw new Exception('Choose a valid solution type.');
            if ($content === null) throw new Exception(in_array($type, ['text', 'simple_text', 'rich_text'], true) ? 'Enter the solution text.' : ($type === 'image' ? 'Enter a valid Google Drive file link.' : 'Enter a valid YouTube video link.'));
            $count = qs_rows($db, 'SELECT COUNT(*) AS total FROM question_solutions WHERE question_id = ?', 'i', [$selectedQuestion]);
            if ((int)$count[0]['total'] >= 10) throw new Exception('This question already has 10 solution items. Remove one before adding another.');
            $order = qs_rows($db, 'SELECT COALESCE(MAX(display_order), 0) + 1 AS next_order FROM question_solutions WHERE question_id = ?', 'i', [$selectedQuestion]);
            qs_exec($db, 'INSERT INTO question_solutions (question_id, solution_type, solution_content, display_order) VALUES (?, ?, ?, ?)', 'issi', [$selectedQuestion, $type, $content, (int)$order[0]['next_order']]);
            $msg = 'Solution added. It will appear in the answer review after the student submits the practice test.';
        } elseif ($action === 'delete_solution') {
            $solutionId = (int)($_POST['solution_id'] ?? 0);
            if ($solutionId < 1 || $selectedQuestion < 1) throw new Exception('Invalid solution selection.');
            qs_exec($db, 'DELETE FROM question_solutions WHERE id = ? AND question_id = ?', 'ii', [$solutionId, $selectedQuestion]);
            $msg = 'Solution removed.';
        } else throw new Exception('Unknown action.');
    }

    $subjects = qs_rows($db, 'SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_name');
    $topicSql = 'SELECT id, subject_id, topic_name FROM subject_topics';
    $topicTypes = '';
    $topicArgs = [];
    if ($subjectFilter > 0) {
        $topicSql .= ' WHERE subject_id = ?';
        $topicTypes = 'i';
        $topicArgs[] = $subjectFilter;
    }
    $topics = qs_rows($db, $topicSql . ' ORDER BY topic_name', $topicTypes, $topicArgs);

    $questionSql = "SELECT q.id, q.question_text, q.subject_id, q.topic_id, s.subject_code, s.subject_name, t.topic_name FROM question_bank q LEFT JOIN subjects s ON s.id = q.subject_id LEFT JOIN subject_topics t ON t.id = q.topic_id WHERE 1=1";
    $questionTypes = '';
    $questionArgs = [];
    if ($subjectFilter > 0) {
        $questionSql .= ' AND q.subject_id = ?';
        $questionTypes .= 'i';
        $questionArgs[] = $subjectFilter;
    }
    if ($topicFilter > 0) {
        $questionSql .= ' AND q.topic_id = ?';
        $questionTypes .= 'i';
        $questionArgs[] = $topicFilter;
    }
    $questions = qs_rows($db, $questionSql . ' ORDER BY s.subject_name, t.topic_name, q.id DESC LIMIT 500', $questionTypes, $questionArgs);
    $selectedRows = $selectedQuestion > 0 ? qs_rows($db, 'SELECT q.id, q.question_text, s.subject_code, s.subject_name, t.topic_name FROM question_bank q LEFT JOIN subjects s ON s.id=q.subject_id LEFT JOIN subject_topics t ON t.id=q.topic_id WHERE q.id=?', 'i', [$selectedQuestion]) : [];
    $selected = $selectedRows[0] ?? null;
    if (!$selected) $selectedQuestion = 0;
    $solutions = $selectedQuestion > 0 ? qs_rows($db, 'SELECT id, solution_type, solution_content, display_order FROM question_solutions WHERE question_id = ? ORDER BY display_order, id', 'i', [$selectedQuestion]) : [];
} catch (Throwable $ex) {
    $msg = $ex->getMessage();
    $msgType = 'danger';
    $subjects = $subjects ?? [];
    $topics = $topics ?? [];
    $questions = $questions ?? [];
    $selected = $selected ?? null;
    $solutions = $solutions ?? [];
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'globals/head.php'; ?>
<style>
    .media-page {
        max-width: 1200px;
        margin: 0 auto
    }

    .media-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 24px
    }

    .media-header h2 {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 750;
        color: #1f2937
    }

    .media-header p {
        margin: 6px 0 0;
        color: #6b7280
    }

    .media-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        margin-bottom: 22px;
        overflow: hidden
    }

    .media-card-head {
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb
    }

    .media-card-head h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937
    }

    .media-card-body {
        padding: 22px
    }

    .media-page .form-label {
        font-size: .88rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 7px
    }

    .media-page .form-control,
    .media-page .form-select {
        min-height: 43px;
        border-radius: 8px;
        border-color: #d1d5db
    }

    .media-page .form-control:focus,
    .media-page .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .12)
    }

    .media-alert {
        border: 0;
        border-radius: 10px;
        padding: 13px 16px
    }

    .solution-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 16px
    }

    .solution-item {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        min-width: 0
    }

    .solution-item-body {
        padding: 15px;
        overflow-wrap: anywhere
    }

    .solution-preview {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        padding: 14px
    }

    .solution-preview .media-preview {
        aspect-ratio: 16/9;
        background: #f3f4f6;
        overflow: hidden;
        margin-bottom: 10px
    }

    .solution-preview iframe {
        width: 100%;
        height: 100%;
        border: 0
    }

    .solution-text {
        white-space: pre-wrap;
        overflow-wrap: anywhere
    }

    .solution-text pre {
        white-space: pre-wrap
    }

    .solution-empty {
        padding: 32px 15px;
        text-align: center;
        color: #6b7280
    }

    .solution-empty i {
        font-size: 2rem;
        display: block;
        margin-bottom: 8px;
        color: #9ca3af
    }

    .solution-toolbar {
        display: flex;
        gap: 6px;
        flex-wrap: wrap
    }

    .solution-toolbar .btn {
        min-width: 36px
    }

    .question-option {
        max-width: 100%
    }

    @media(max-width:700px) {
        .media-header {
            flex-direction: column
        }

        .media-header h2 {
            font-size: 1.4rem
        }

        .media-card-body {
            padding: 16px
        }

        .media-card-head {
            padding: 16px
        }

        .solution-list {
            grid-template-columns: 1fr
        }
    }
</style>

<body>
    <?php include 'globals/sidebar.php'; ?>
    <?php include 'globals/topbar.php'; ?>
    <main class="main-content">
        <div class="content-wrapper media-page">
            <div class="media-header">
                <div>
                    <h2><i class="bi bi-lightbulb me-2"></i>Question Solutions Management</h2>
                    <p>Add text explanations, rich-text walkthroughs, images, and YouTube videos to each question.</p>
                </div><a class="btn btn-outline-secondary" href="question_management.php"><i class="bi bi-arrow-left me-1"></i>Back to Questions</a>
            </div>
            <?php if ($msg !== ''): ?><div class="alert alert-<?= qs_e($msgType) ?> media-alert alert-dismissible fade show"><?= qs_e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
            <section class="media-card">
                <div class="media-card-head">
                    <h5><i class="bi bi-journal-text me-2"></i>Select a question</h5>
                </div>
                <div class="media-card-body">
                    <form method="get" class="mb-4 p-3 border rounded-3 bg-light">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5"><label class="form-label" for="subjectFilter">Filter by subject</label><select class="form-select" id="subjectFilter" name="subject_filter">
                                    <option value="0">All subjects</option><?php foreach ($subjects as $subject): ?><option value="<?= (int)$subject['id'] ?>" <?= ($subjectFilter === (int)$subject['id'] ? 'selected' : '') ?>><?= qs_e(($subject['subject_code'] ? $subject['subject_code'] . ' — ' : '') . $subject['subject_name']) ?></option><?php endforeach; ?>
                                </select></div>
                            <div class="col-md-5"><label class="form-label" for="topicFilter">Filter by topic</label><select class="form-select" id="topicFilter" name="topic_filter">
                                    <option value="0">All topics</option><?php foreach ($topics as $topic): ?><option value="<?= (int)$topic['id'] ?>" <?= ($topicFilter === (int)$topic['id'] ? 'selected' : '') ?>><?= qs_e($topic['topic_name']) ?></option><?php endforeach; ?>
                                </select></div>
                            <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button><a class="btn btn-outline-secondary" href="question_solutions_management.php" title="Clear filters"><i class="bi bi-x-lg"></i></a></div>
                        </div>
                        <div class="form-text mt-2"><?= count($questions) ?> question(s) match the selected filters.</div>
                    </form>
                    <form method="get" class="mb-4"><input type="hidden" name="subject_filter" value="<?= $subjectFilter ?>"><input type="hidden" name="topic_filter" value="<?= $topicFilter ?>"><label class="form-label" for="questionPicker">Choose question</label>
                        <div class="d-flex gap-2"><select class="form-select question-option" id="questionPicker" name="question_id" required>
                                <option value="">Choose a question…</option><?php foreach ($questions as $q): $label = trim(($q['subject_code'] ? $q['subject_code'] . ' — ' : '') . ($q['topic_name'] ? $q['topic_name'] . ' — ' : '') . strip_tags($q['question_text'])); ?><option value="<?= (int)$q['id'] ?>" <?= ((int)$q['id'] === $selectedQuestion ? 'selected' : '') ?>><?= qs_e('#' . $q['id'] . ' · ' . mb_strimwidth($label, 0, 115, '…')) ?></option><?php endforeach; ?>
                            </select><button class="btn btn-primary" type="submit">Select</button></div>
                    </form>
                    <?php if ($selected): ?><div class="alert alert-light border mb-4">
                            <div class="small text-muted mb-1"><?= qs_e(($selected['subject_code'] ?? '') . ' · ' . ($selected['topic_name'] ?? '')) ?></div><strong>Question #<?= (int)$selected['id'] ?></strong>
                            <div class="mt-1"><?= nl2br(qs_e($selected['question_text'])) ?></div>
                        </div>
                        <form method="post" id="solutionForm"><input type="hidden" name="csrf" value="<?= qs_e($csrf) ?>"><input type="hidden" name="action" value="add_solution"><input type="hidden" name="question_id" value="<?= (int)$selectedQuestion ?>"><input type="hidden" name="subject_filter" value="<?= $subjectFilter ?>"><input type="hidden" name="topic_filter" value="<?= $topicFilter ?>">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4"><label class="form-label" for="solutionType">Solution type</label><select class="form-select" name="solution_type" id="solutionType" required>
                                        <option value="simple_text">Simple text</option>
                                        <option value="rich_text">Rich text / formatted explanation</option>
                                        <option value="image">Image (Google Drive)</option>
                                        <option value="youtube">Video (YouTube)</option>
                                    </select></div>
                                <div class="col-md-8">
                                    <div class="small text-muted">Add up to 10 solution items per question. They appear in answer review after submission.</div>
                                </div>
                                <div class="col-12" id="simpleTextField"><label class="form-label" for="simpleSolutionContent">Solution text</label><textarea class="form-control" id="simpleSolutionContent" rows="5" placeholder="Explain the solution step by step…"></textarea>
                                    <div class="form-text">Plain text with line breaks preserved.</div>
                                </div>
                                <div class="col-12 d-none" id="richTextField"><label class="form-label">Rich solution text</label>
                                    <div class="solution-toolbar mb-2" role="toolbar" aria-label="Text formatting"><button type="button" class="btn btn-sm btn-outline-secondary" data-command="bold"><b>B</b></button><button type="button" class="btn btn-sm btn-outline-secondary" data-command="italic"><i>I</i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-command="underline"><u>U</u></button><button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertUnorderedList">• List</button><button type="button" class="btn btn-sm btn-outline-secondary" data-command="insertOrderedList">1. List</button><button type="button" class="btn btn-sm btn-outline-secondary" data-command="formatBlock" data-value="h3">Heading</button></div>
                                    <div id="richSolutionEditor" class="form-control" contenteditable="true" style="min-height:180px;overflow:auto" aria-label="Rich solution editor"></div>
                                    <div class="form-text">Use the toolbar to format headings, emphasis, and lists.</div>
                                </div>
                                <div class="col-12 d-none" id="urlField"><label class="form-label" id="urlLabel" for="solutionUrl">Media URL</label><input class="form-control" type="url" id="solutionUrl" placeholder="Paste a Google Drive or YouTube link" disabled>
                                    <div class="form-text">For images, share the Google Drive file as Anyone with the link — Viewer. YouTube videos must allow embedding.</div>
                                </div>
                                <input type="hidden" name="solution_content" id="solutionContent">
                                <div class="col-12"><button class="btn btn-primary" type="submit" <?= count($solutions) >= 10 ? 'disabled' : '' ?>><i class="bi bi-plus-lg me-1"></i>Add solution</button></div>
                        </form><?php else: ?><div class="text-muted small">Choose a question above to add or manage its solutions.</div><?php endif; ?>
                </div>
            </section>
            <?php if ($selected): ?><section class="media-card">
                    <div class="media-card-head d-flex justify-content-between align-items-center gap-2">
                        <h5><i class="bi bi-lightbulb me-2"></i>Saved solutions</h5><span class="badge text-bg-secondary"><?= count($solutions) ?> / 10</span>
                    </div>
                    <div class="media-card-body">
                        <?php if (!$solutions): ?><div class="solution-empty"><i class="bi bi-journal-x"></i>No solutions saved for this question yet.</div><?php else: ?><div class="solution-list">
                                <?php foreach ($solutions as $solution): ?><article class="solution-item">
                                        <div class="solution-preview">
                                            <div class="d-flex justify-content-between align-items-center gap-2"><span class="badge text-bg-primary"><?= qs_e(str_replace('_', ' ', strtoupper($solution['solution_type']))) ?></span><span class="small text-muted">Order <?= (int)$solution['display_order'] ?></span></div>
                                            <?php if ($solution['solution_type'] === 'image' || $solution['solution_type'] === 'youtube'): ?><?= qs_media_preview($solution['solution_type'], (string)$solution['solution_content']) ?><?php endif; ?>
                                        </div>
                                        <div class="solution-item-body">
                                            <?php if (in_array($solution['solution_type'], ['text', 'simple_text'], true)): ?><div class="solution-text"><?= qs_e($solution['solution_content']) ?></div><?php elseif ($solution['solution_type'] === 'rich_text'): ?><div class="solution-text"><?= qs_safe_rich((string)$solution['solution_content']) ?></div><?php endif; ?>
                                            <?php if (in_array($solution['solution_type'], ['image', 'youtube'], true)): ?><div class="small text-muted mt-2" style="overflow-wrap:anywhere"><?= qs_e($solution['solution_content']) ?></div><?php endif; ?>
                                            <div class="d-flex justify-content-end mt-3">
                                                <form method="post" onsubmit="return confirm('Remove this solution?');"><input type="hidden" name="csrf" value="<?= qs_e($csrf) ?>"><input type="hidden" name="action" value="delete_solution"><input type="hidden" name="question_id" value="<?= (int)$selectedQuestion ?>"><input type="hidden" name="subject_filter" value="<?= $subjectFilter ?>"><input type="hidden" name="topic_filter" value="<?= $topicFilter ?>"><input type="hidden" name="solution_id" value="<?= (int)$solution['id'] ?>"><button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i>Remove</button></form>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?></div><?php endif; ?>
                    </div>
                </section><?php endif; ?>
            <div class="small text-muted mb-4"><i class="bi bi-info-circle me-1"></i>Solutions are stored separately from question media. Google Drive images require “Anyone with the link — Viewer” access. If a preview does not load, check sharing permissions or open the original link.</div>
        </div>
    </main>
    <script>
        const typeSelect = document.getElementById('solutionType');
        const simpleField = document.getElementById('simpleTextField');
        const richField = document.getElementById('richTextField');
        const urlField = document.getElementById('urlField');
        const simpleText = document.getElementById('simpleSolutionContent');
        const richEditor = document.getElementById('richSolutionEditor');
        const hiddenContent = document.getElementById('solutionContent');
        const urlInput = document.getElementById('solutionUrl');
        const urlLabel = document.getElementById('urlLabel');
        if (richEditor) document.querySelectorAll('[data-command]').forEach(button => button.addEventListener('click', () => {
            richEditor.focus();
            document.execCommand(button.dataset.command, false, button.dataset.value || null);
        }));
        if (typeSelect) {
            function updateSolutionFields() {
                const t = typeSelect.value,
                    simple = t === 'simple_text' || t === 'text',
                    rich = t === 'rich_text',
                    text = simple || rich;
                simpleField.classList.toggle('d-none', !simple);
                richField.classList.toggle('d-none', !rich);
                urlField.classList.toggle('d-none', text);
                simpleText.disabled = !simple;
                richEditor.contentEditable = rich ? 'true' : 'false';
                urlInput.disabled = text;
                urlInput.required = !text;
                urlLabel.textContent = t === 'image' ? 'Google Drive image link' : 'YouTube video link';
                urlInput.placeholder = t === 'image' ? 'https://drive.google.com/file/d/…' : 'https://www.youtube.com/watch?v=…';
            }
            typeSelect.addEventListener('change', updateSolutionFields);
            updateSolutionFields();
            document.getElementById('solutionForm').addEventListener('submit', function(event) {
                if (typeSelect.value === 'simple_text' || typeSelect.value === 'text') hiddenContent.value = simpleText.value;
                else if (typeSelect.value === 'rich_text') hiddenContent.value = richEditor.innerHTML;
                else hiddenContent.value = urlInput.value;
                if (typeSelect.value === 'rich_text' && richEditor.innerText.trim() === '') {
                    event.preventDefault();
                    alert('Enter the solution text.');
                }
            });
        }
        const subjectFilter = document.getElementById('subjectFilter'),
            topicFilter = document.getElementById('topicFilter');
        if (subjectFilter && topicFilter) subjectFilter.addEventListener('change', () => {
            topicFilter.value = '0';
        });
    </script>
    <?php include 'globals/scripts.php'; ?>
</body>

</html>