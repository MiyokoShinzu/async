<?php
include './globals/auth.php';
require_once '../src/connection.php';
$db = $mysqli ?? null;
if (!$db instanceof mysqli) exit('Database connection is unavailable.');
$db->set_charset('utf8mb4');
$user = $_SESSION['user'] ?? [];
$adminId = (int)($user['id'] ?? 0);
if (!$adminId) exit('Please sign in again.');
if (empty($_SESSION['exam_settings_csrf'])) $_SESSION['exam_settings_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['exam_settings_csrf'];
function es_e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function es_rows($db, $sql, $types = '', $args = [])
{
    $stmt = $db->prepare($sql);
    if (!$stmt) throw new Exception($db->error);
    if ($types) $stmt->bind_param($types, ...$args);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $result;
}
function es_exec($db, $sql, $types = '', $args = [])
{
    $stmt = $db->prepare($sql);
    if (!$stmt) throw new Exception($db->error);
    if ($types) $stmt->bind_param($types, ...$args);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}
$message = '';
$messageType = 'success';
try {
    // Creates this page's settings table if the database user has CREATE permission.
    $db->query("CREATE TABLE IF NOT EXISTS exam_settings (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        quiz_master_id INT NOT NULL,
        subject_id INT NOT NULL,
        exam_title VARCHAR(180) NOT NULL,
        question_count INT UNSIGNED NOT NULL DEFAULT 20,
        duration_minutes INT UNSIGNED NOT NULL DEFAULT 60,
        difficulty ENUM('any','easy','medium','hard') NOT NULL DEFAULT 'any',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_exam_owner_subject_title (quiz_master_id, subject_id, exam_title),
        KEY idx_exam_owner (quiz_master_id), KEY idx_exam_subject (subject_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch (Throwable $e) {
    exit('Could not prepare exam settings storage. Create the exam_settings table using the SQL provided with this page.');
}
$subjects = es_rows($db, 'SELECT s.id,s.subject_code,s.subject_name FROM subjects s JOIN quiz_master_subjects qms ON qms.subject_id=s.id WHERE qms.quiz_master_id=? ORDER BY s.subject_name', 'i', [$adminId]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) throw new Exception('Session token expired. Refresh the page and try again.');
        $action = $_POST['action'] ?? '';
        if ($action === 'save') {
            $id = (int)($_POST['id'] ?? 0);
            $subjectId = (int)($_POST['subject_id'] ?? 0);
            $title = trim((string)($_POST['exam_title'] ?? ''));
            $count = (int)($_POST['question_count'] ?? 20);
            $duration = (int)($_POST['duration_minutes'] ?? 60);
            $difficulty = in_array($_POST['difficulty'] ?? 'any', ['any', 'easy', 'medium', 'hard'], true) ? $_POST['difficulty'] : 'any';
            if (!$subjectId || !in_array($subjectId, array_map(fn($s) => (int)$s['id'], $subjects), true)) throw new Exception('Select a subject assigned to your account.');
            if ($title === '' || mb_strlen($title) > 180) throw new Exception('Enter an exam title (maximum 180 characters).');
            if ($count < 1 || $count > 300) throw new Exception('Question count must be between 1 and 300.');
            if ($duration < 1 || $duration > 600) throw new Exception('Duration must be between 1 and 600 minutes.');
            if ($id) {
                $stmt = $db->prepare('UPDATE exam_settings SET subject_id=?,exam_title=?,question_count=?,duration_minutes=?,difficulty=? WHERE id=? AND quiz_master_id=?');
                $stmt->bind_param('isiisii', $subjectId, $title, $count, $duration, $difficulty, $id, $adminId);
                if (!$stmt->execute()) throw new Exception($stmt->error);
                if (!$stmt->affected_rows && !es_rows($db, 'SELECT id FROM exam_settings WHERE id=? AND quiz_master_id=?', 'ii', [$id, $adminId])) throw new Exception('Exam setting not found.');
                $stmt->close();
            }
            if (!$id) {
                $stmt = $db->prepare('INSERT INTO exam_settings (quiz_master_id,subject_id,exam_title,question_count,duration_minutes,difficulty) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE question_count=VALUES(question_count),duration_minutes=VALUES(duration_minutes),difficulty=VALUES(difficulty),id=LAST_INSERT_ID(id)');
                $stmt->bind_param('iisiis', $adminId, $subjectId, $title, $count, $duration, $difficulty);
                if (!$stmt->execute()) throw new Exception($stmt->error);
                $id = (int)$stmt->insert_id;
                $stmt->close();
            }
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?edit=' . $id . '&saved=1');
            exit;
        } elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            es_exec($db, 'DELETE FROM exam_settings WHERE id=? AND quiz_master_id=?', 'ii', [$id, $adminId]);
            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?deleted=1');
            exit;
        } else throw new Exception('Unknown action.');
    } catch (Throwable $e) {
        $message = $e->getMessage();
        $messageType = 'danger';
    }
}
if (isset($_GET['saved'])) {
    $message = 'Exam settings saved.';
    $messageType = 'success';
}
if (isset($_GET['deleted'])) {
    $message = 'Exam setting deleted.';
    $messageType = 'success';
}
$editId = (int)($_GET['edit'] ?? 0);
$setting = null;
if ($editId) $setting = es_rows($db, 'SELECT * FROM exam_settings WHERE id=? AND quiz_master_id=?', 'ii', [$editId, $adminId])[0] ?? null;
$settings = es_rows($db, 'SELECT es.*,s.subject_code,s.subject_name FROM exam_settings es JOIN subjects s ON s.id=es.subject_id WHERE es.quiz_master_id=? ORDER BY es.updated_at DESC', 'i', [$adminId]);
$subjectId = (int)($setting['subject_id'] ?? $_GET['subject_id'] ?? ($subjects[0]['id'] ?? 0));
$count = max(1, min(300, (int)($setting['question_count'] ?? $_GET['question_count'] ?? 20)));
$difficulty = $setting['difficulty'] ?? ($_GET['difficulty'] ?? 'any');
$duration = (int)($setting['duration_minutes'] ?? 60);
$title = $setting['exam_title'] ?? 'Examination';
$previewQuestions = [];
$availableCount = 0;
if ($subjectId && in_array($subjectId, array_map(fn($s) => (int)$s['id'], $subjects), true)) {
    $base = " FROM question_bank q JOIN quiz_master_subjects qms ON qms.subject_id=q.subject_id WHERE qms.quiz_master_id=? AND q.subject_id=? AND q.status='active'";
    $types = 'ii';
    $args = [$adminId, $subjectId];
    if (in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
        $base .= ' AND q.difficulty=?';
        $types .= 's';
        $args[] = $difficulty;
    }
    $availableCount = (int)(es_rows($db, 'SELECT COUNT(*) AS n' . $base, $types, $args)[0]['n'] ?? 0);
    $previewQuestions = es_rows($db, 'SELECT q.id,q.question_text,q.difficulty,q.explanation' . $base . ' ORDER BY RAND() LIMIT ' . (int)$count, $types, $args);
    if ($previewQuestions) {
        $ids = array_map(fn($q) => (int)$q['id'], $previewQuestions);
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $choiceTypes = str_repeat('i', count($ids));
        $choices = es_rows($db, "SELECT question_id,choice_label,choice_text,is_correct FROM question_choices WHERE question_id IN ($marks) ORDER BY FIELD(choice_label,'A','B','C','D','E','F')", $choiceTypes, $ids);
        $choiceMap = [];
        foreach ($choices as $choice) $choiceMap[$choice['question_id']][] = $choice;
        foreach ($previewQuestions as &$q) $q['choices'] = $choiceMap[$q['id']] ?? [];
        unset($q);
    }
}
function exam_safe_html($html)
{
    $html = (string)$html;
    if (!class_exists('DOMDocument')) return nl2br(es_e(strip_tags($html)));
    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8"><div id="root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $allowed = ['div', 'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'sub', 'sup', 'span', 'ol', 'ul', 'li', 'blockquote', 'pre', 'code', 'h1', 'h2', 'h3'];
    $root = $doc->getElementById('root');
    if (!$root) return es_e(strip_tags($html));
    $walk = function ($node) use (&$walk, $allowed) {
        for ($child = $node->firstChild; $child;) {
            $next = $child->nextSibling;
            if ($child instanceof DOMElement) {
                if (!in_array(strtolower($child->tagName), $allowed, true)) {
                    while ($child->firstChild) $child->parentNode->insertBefore($child->firstChild, $child);
                    $child->parentNode->removeChild($child);
                } else {
                    foreach (iterator_to_array($child->attributes) as $attr) {
                        if ($attr->name === 'class') {
                            $classes = preg_split('/\s+/', $attr->value);
                            $classes = array_filter($classes, fn($c) => preg_match('/^(ql-align-(center|right|justify)|ql-indent-[1-8]|ql-size-(small|large|huge)|ql-font-[\w-]+|ql-formula)$/', $c));
                            if ($classes) $child->setAttribute('class', implode(' ', $classes));
                            else $child->removeAttribute('class');
                        } elseif ($attr->name === 'data-value' && $child->tagName === 'span' && str_contains((string)$child->getAttribute('class'), 'ql-formula')) { /* retain formula latex */
                        } else $child->removeAttribute($attr->name);
                    }
                    $walk($child);
                }
            }
            $child = $next;
        }
    };
    $walk($root);
    $out = '';
    foreach ($root->childNodes as $child) $out .= $doc->saveHTML($child);
    return $out;
}
?>
<!doctype html>
<html lang="en">
<?php include 'globals/head.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/katex.min.css">
<style>
    :root {
        --academic-blue: #0B4F8A;
        --academic-blue-dark: #083B66;
        --academic-blue-light: #EAF3FA;
        --bg-color: #F7F9FB;
        --surface-color: #fff;
        --text-color: #212529;
        --text-secondary: #6C757D;
        --border-color: #DEE2E6;
        --surface: var(--surface-color);
        --text: var(--text-color);
        --text-muted: var(--text-secondary);
        --border: var(--border-color)
    }

    * {
        box-sizing: border-box
    }

    body {
        background: var(--bg-color);
        color: var(--text-color);
        overflow-x: hidden
    }

    .content-wrapper {
        max-width: 100%;
        min-width: 0
    }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 24px
    }

    .page-header h2 {
        font-size: 1.6rem;
        font-weight: 750;
        margin: 0
    }

    .page-header p {
        margin: 6px 0 0;
        color: var(--text-muted)
    }

    .exam-grid {
        display: grid;
        grid-template-columns: minmax(280px, .85fr) minmax(0, 1.35fr);
        gap: 22px;
        align-items: start
    }

    .panel {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 2px 8px #0f172a08;
        overflow: hidden;
        margin-bottom: 22px
    }

    .panel-head {
        display: flex;
        gap: 13px;
        align-items: center;
        padding: 18px 21px;
        background: linear-gradient(135deg, #f7f9fb, var(--academic-blue-light));
        border-bottom: 1px solid var(--border)
    }

    .head-icon {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        background: var(--academic-blue);
        color: #fff;
        border-radius: 12px;
        font-size: 1.25rem
    }

    .panel-head h5 {
        font-size: 1rem;
        font-weight: 700;
        margin: 0
    }

    .panel-head p {
        font-size: .83rem;
        color: var(--text-muted);
        margin: 4px 0 0
    }

    .panel-body {
        padding: 21px
    }

    .form-label {
        font-size: .88rem;
        font-weight: 650;
        margin-bottom: 7px
    }

    .form-control,
    .form-select {
        min-height: 43px;
        border-radius: 8px
    }

    .form-text {
        font-size: .78rem
    }

    .btn {
        border-radius: 8px
    }

    .btn-primary {
        background: var(--academic-blue);
        border-color: var(--academic-blue)
    }

    .btn-primary:hover {
        background: var(--academic-blue-dark);
        border-color: var(--academic-blue-dark)
    }

    .statline {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid #edf0f2;
        font-size: .9rem
    }

    .statline:last-child {
        border: 0
    }

    .badge-soft {
        display: inline-block;
        background: var(--academic-blue-light);
        color: var(--academic-blue);
        padding: 5px 9px;
        border-radius: 20px;
        font-size: .78rem;
        font-weight: 700
    }

    .table-wrap {
        overflow-x: auto
    }

    .table {
        margin: 0
    }

    .table th {
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: var(--text-muted);
        white-space: nowrap
    }

    .table td {
        vertical-align: middle;
        font-size: .88rem
    }

    .preview-sheet {
        background: #fff;
        color: #202124;
        border: 1px solid #d9dee5;
        border-radius: 5px;
        box-shadow: 0 8px 25px #15223812;
        padding: clamp(20px, 3vw, 42px);
        min-height: 400px
    }

    .paper-head {
        text-align: center;
        border-bottom: 1px solid #bbb;
        padding-bottom: 15px;
        margin-bottom: 20px
    }

    .paper-head h3 {
        font-size: 1.15rem;
        font-weight: 800;
        margin: 0 0 5px
    }

    .paper-head p {
        font-size: .84rem;
        margin: 3px 0;
        color: #444
    }

    .student-lines {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin: 20px 0;
        font-size: .84rem
    }

    .student-lines span {
        border-bottom: 1px solid #777;
        min-height: 22px
    }

    .paper-questions-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 24px;
        row-gap: 8px;
        align-items: start
    }

    .paper-question {
        min-width: 0;
        margin: 14px 0;
        break-inside: avoid;
        page-break-inside: avoid
    }

    .paper-question.is-wide {
        grid-column: 1/-1
    }

    .paper-question-title {
        font-weight: 650;
        margin-bottom: 8px;
        line-height: 1.5;
        overflow-wrap: anywhere
    }

    .paper-choice {
        display: flex;
        gap: 7px;
        margin: 5px 0 5px 12px;
        line-height: 1.45;
        min-width: 0;
        overflow-wrap: anywhere
    }

    .paper-choice-label {
        font-weight: 700;
        min-width: 18px
    }

    .paper-choices.is-four-short {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        margin: 8px 0 0
    }

    .paper-choices.is-four-short .paper-choice {
        margin: 0;
        gap: 5px;
        align-items: flex-start
    }

    .paper-choices.is-four-short .paper-choice-label {
        min-width: auto
    }

    .paper-question p {
        margin-bottom: 6px
    }

    .paper-question img {
        max-width: 100%
    }

    .paper-note {
        font-size: .8rem;
        color: #667085;
        margin-top: 12px
    }

    .preview-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap
    }

    .preview-toolbar h5 {
        margin: 0;
        font-weight: 750
    }

    .empty-state {
        text-align: center;
        padding: 35px 15px;
        color: var(--text-muted)
    }

    .actions {
        display: flex;
        gap: 7px;
        flex-wrap: wrap
    }

    .alert {
        border-radius: 10px
    }

    .qm-form-footer {
        display: flex;
        gap: 9px;
        justify-content: flex-end;
        flex-wrap: wrap;
        margin-top: 20px
    }

    .math-field-preview {
        font-size: 1em;
        pointer-events: none;
        border: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        max-width: 100%
    }

    .preview-sheet .ql-align-center {
        text-align: center
    }

    .preview-sheet .ql-align-right {
        text-align: right
    }

    .preview-sheet .ql-align-justify {
        text-align: justify
    }

    .preview-sheet .ql-indent-1 {
        padding-left: 3em
    }

    .preview-sheet .ql-indent-2 {
        padding-left: 6em
    }

    @media(max-width:1050px) {
        .exam-grid {
            grid-template-columns: 1fr
        }

        .preview-sheet {
            min-height: 300px
        }
    }

    @media(max-width:700px) {
        .paper-questions-grid {
            grid-template-columns: 1fr
        }

        .paper-question.is-wide {
            grid-column: auto
        }

        .paper-choices.is-four-short {
            grid-template-columns: repeat(2, minmax(0, 1fr))
        }
    }

    @media(max-width:600px) {
        .page-header {
            align-items: flex-start;
            flex-direction: column
        }

        .panel-body {
            padding: 16px
        }

        .panel-head {
            padding: 16px
        }

        .student-lines {
            grid-template-columns: 1fr
        }

        .qm-form-footer>* {
            width: 100%
        }
    }

    @media print {
        @page {
            size: A4;
            margin: 16mm
        }

        .no-print,
        body>header,
        aside,
        .sidebar,
        .topbar,
        nav {
            display: none !important
        }

        body {
            background: #fff !important;
            color: #000 !important
        }

        .main-content,
        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important
        }

        .exam-grid {
            display: block
        }

        .exam-grid>.settings-column,
        .settings-list,
        .page-header,
        .preview-toolbar,
        .panel-head,
        .paper-note {
            display: none !important
        }

        .panel,
        .preview-sheet {
            border: 0 !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: 0 !important
        }

        .panel-body {
            padding: 0 !important
        }

        .preview-sheet {
            display: block !important
        }

        .paper-questions-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            column-gap: 18px;
            row-gap: 4px
        }

        .paper-question.is-wide {
            grid-column: 1/-1
        }

        .paper-question {
            break-inside: avoid;
            page-break-inside: avoid
        }

        .paper-head {
            margin-bottom: 18px
        }
    }
</style>

<body>
    <?php
    // Keep the same shared navigation shell as question_management.php when these files exist.
    if (file_exists('globals/sidebar.php')) include 'globals/sidebar.php';
    ?>
    <div class="main-content">
        <div class="content-wrapper container-fluid py-4 px-lg-4">
            <header class="page-header">
                <div>
                    <h2><i class="bi bi-sliders2-vertical me-2" style="color:var(--academic-blue)"></i>Exam Settings</h2>
                    <p>Configure the question count, duration, and preview a printable exam paper.</p>
                </div>
                <div class="no-print"><button class="btn btn-outline-primary" type="button" onclick="window.print()"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</button></div>
            </header>
            <?php if ($message): ?><div class="alert alert-<?= es_e($messageType) ?> no-print"><?= es_e($message) ?></div><?php endif; ?>
            <div class="exam-grid">
                <section class="settings-column">
                    <div class="panel">
                        <div class="panel-head">
                            <div class="head-icon"><i class="bi bi-gear"></i></div>
                            <div>
                                <h5>Configure examination</h5>
                                <p>Choose which active questions to include.</p>
                            </div>
                        </div>
                        <div class="panel-body">
                            <form method="post" id="examSettingsForm"><input type="hidden" name="csrf" value="<?= es_e($csrf) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($setting['id'] ?? 0) ?>">
                                <div class="mb-3"><label class="form-label" for="exam_title">Exam title</label><input class="form-control" id="exam_title" name="exam_title" maxlength="180" required value="<?= es_e($title) ?>" placeholder="e.g. Midterm Examination"></div>
                                <div class="mb-3"><label class="form-label" for="subject_id">Subject</label><select class="form-select" id="subject_id" name="subject_id" required><?php if (!$subjects): ?><option value="">No assigned subjects</option><?php endif; ?><?php foreach ($subjects as $s): ?><option value="<?= $s['id'] ?>" <?= ((int)$s['id'] === $subjectId ? 'selected' : '') ?>><?= es_e($s['subject_code'] . ' — ' . $s['subject_name']) ?></option><?php endforeach; ?></select></div>
                                <div class="row g-3">
                                    <div class="col-sm-6"><label class="form-label" for="question_count">Number of questions</label><input class="form-control" type="number" id="question_count" name="question_count" min="1" max="300" value="<?= $count ?>" required>
                                        <div class="form-text">Maximum 300 questions.</div>
                                    </div>
                                    <div class="col-sm-6"><label class="form-label" for="duration_minutes">Duration (minutes)</label><input class="form-control" type="number" id="duration_minutes" name="duration_minutes" min="1" max="600" value="<?= $duration ?>" required></div>
                                </div>
                                <div class="mt-3 mb-3"><label class="form-label" for="difficulty">Question difficulty</label><select class="form-select" id="difficulty" name="difficulty">
                                        <option value="any" <?= $difficulty === 'any' ? 'selected' : '' ?>>Any difficulty</option>
                                        <option value="easy" <?= $difficulty === 'easy' ? 'selected' : '' ?>>Easy only</option>
                                        <option value="medium" <?= $difficulty === 'medium' ? 'selected' : '' ?>>Medium only</option>
                                        <option value="hard" <?= $difficulty === 'hard' ? 'selected' : '' ?>>Hard only</option>
                                    </select></div>
                                <div class="statline"><span>Available active questions</span><strong id="availableCount"><?= $availableCount ?></strong></div>
                                <div class="statline"><span>Requested questions</span><strong id="requestedCount"><?= $count ?></strong></div>
                                <div class="statline"><span>Estimated duration</span><strong id="durationSummary"><?= $duration ?> minutes</strong></div>
                                <div class="qm-form-footer"><a href="<?= es_e(strtok($_SERVER['REQUEST_URI'], '?')) ?>" class="btn btn-light border">New settings</a><button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i>Save settings</button></div>
                            </form>
                            <div class="d-grid mt-3"><button type="button" class="btn btn-outline-primary" id="refreshPreview"><i class="bi bi-arrow-clockwise me-1"></i>Refresh preview</button></div>
                            <div class="paper-note"><i class="bi bi-info-circle me-1"></i>PDF export opens your browser's print dialog. Select <strong>Save as PDF</strong>. The preview uses a random selection of active questions; refresh to generate another sample.</div>
                        </div>
                    </div>
                    <div class="panel settings-list">
                        <div class="panel-head">
                            <div class="head-icon"><i class="bi bi-collection"></i></div>
                            <div>
                                <h5>Saved exam settings</h5>
                                <p>Load or remove an existing configuration.</p>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Questions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody><?php if (!$settings): ?><tr>
                                            <td colspan="3" class="text-center text-secondary py-4">No saved exam settings yet.</td>
                                        </tr><?php endif; ?><?php foreach ($settings as $es): ?><tr>
                                            <td><strong><?= es_e($es['exam_title']) ?></strong>
                                                <div class="small text-secondary"><?= es_e($es['subject_code'] . ' · ' . $es['subject_name']) ?></div>
                                            </td>
                                            <td><?= $es['question_count'] ?> <div class="small text-secondary"><?= $es['duration_minutes'] ?> min</div>
                                            </td>
                                            <td>
                                                <div class="actions"><a class="btn btn-sm btn-outline-primary" href="?edit=<?= $es['id'] ?>"><i class="bi bi-pencil"></i></a>
                                                    <form method="post" onsubmit="return confirm('Delete this saved exam setting?')"><input type="hidden" name="csrf" value="<?= es_e($csrf) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $es['id'] ?>"><button class="btn btn-sm btn-outline-danger" type="submit" title="Delete"><i class="bi bi-trash"></i></button></form>
                                                </div>
                                            </td>
                                        </tr><?php endforeach; ?></tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <section class="preview-column">
                    <div class="preview-toolbar no-print">
                        <h5><i class="bi bi-file-earmark-text me-2"></i>Exam paper preview</h5><span class="badge-soft">A4 · Print-ready</span>
                    </div>
                    <div class="panel">
                        <div class="panel-body">
                            <article class="preview-sheet" id="examPaper">
                                <header class="paper-head">
                                    <h3 id="paperTitle"><?= es_e($title) ?></h3>
                                    <p id="paperSubject"><?php foreach ($subjects as $s) if ((int)$s['id'] === $subjectId) echo es_e($s['subject_code'] . ' — ' . $s['subject_name']); ?></p>
                                    <p id="paperMeta"><?= $count ?> Questions &nbsp; | &nbsp; <?= $duration ?> Minutes</p>
                                </header>
                                <div class="student-lines">
                                    <div>Name: <span></span></div>
                                    <div>Section/Year: <span></span></div>
                                    <div>Date: <span></span></div>
                                    <div>Score: <span></span> / <?= $count ?></div>
                                </div>
                                <p><strong>Instructions:</strong> Read each question carefully. Select or write the best answer for each item.</p>
                                <?php if (!$previewQuestions): ?><div class="empty-state no-print"><i class="bi bi-inbox fs-2 d-block mb-2"></i><strong>No questions available for this selection.</strong>
                                        <div class="small mt-1">Choose another subject or difficulty, or add active questions in Question Management.</div>
                                    </div><?php else: ?><div class="paper-questions-grid"><?php foreach ($previewQuestions as $i => $q): ?><?php
                                                                                                                                        $questionPlain = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string)$q['question_text']), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                                                                                                                                        $choicesForLayout = $q['choices'] ?? [];
                                                                                                                                        $longQuestion = mb_strlen($questionPlain) > 180 || count(preg_split('/\s+/u', $questionPlain)) > 30;
                                                                                                                                        $longChoice = false;
                                                                                                                                        foreach ($choicesForLayout as $choiceForLayout) {
                                                                                                                                            $choicePlain = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string)$choiceForLayout['choice_text']), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                                                                                                                                            if (mb_strlen($choicePlain) > 48 || count(preg_split('/\s+/u', $choicePlain)) > 8) {
                                                                                                                                                $longChoice = true;
                                                                                                                                                break;
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                        $wideQuestion = $longQuestion || $longChoice;
                                                                                                                                        $fourShortChoices = count($choicesForLayout) === 4 && !$longChoice;
                                                                                                                                        ?><section class="paper-question<?= $wideQuestion ? ' is-wide' : '' ?>">
                                            <div class="paper-question-title"><span><?= ($i + 1) ?>.</span> <?= exam_safe_html($q['question_text']) ?></div>
                                            <div class="paper-choices<?= $fourShortChoices ? ' is-four-short' : '' ?>"><?php foreach ($choicesForLayout as $choice): ?><div class="paper-choice"><span class="paper-choice-label"><?= es_e($choice['choice_label']) ?>.</span><span><?= exam_safe_html($choice['choice_text']) ?></span></div><?php endforeach; ?></div>
                                        </section><?php endforeach; ?></div><?php endif; ?>
                            </article>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/contrib/auto-render.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/mathlive@0.110.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const title = document.getElementById('exam_title'),
                subject = document.getElementById('subject_id'),
                count = document.getElementById('question_count'),
                duration = document.getElementById('duration_minutes'),
                difficulty = document.getElementById('difficulty');

            function updatePaper() {
                document.getElementById('paperTitle').textContent = title.value || 'Examination';
                document.getElementById('paperMeta').textContent = (count.value || 0) + ' Questions | ' + (duration.value || 0) + ' Minutes';
                document.getElementById('requestedCount').textContent = count.value || 0;
                document.getElementById('durationSummary').textContent = (duration.value || 0) + ' minutes';
            }
            [title, count, duration].forEach(el => el.addEventListener('input', updatePaper));
            subject.addEventListener('change', () => {
                const url = new URL(window.location.href);
                url.searchParams.set('subject_id', subject.value);
                url.searchParams.set('question_count', count.value || 20);
                url.searchParams.set('difficulty', difficulty.value);
                url.searchParams.delete('edit');
                window.location.href = url.toString();
            });
            [count, difficulty].forEach(el => el.addEventListener('change', () => {
                const url = new URL(window.location.href);
                url.searchParams.set('subject_id', subject.value);
                url.searchParams.set('question_count', count.value || 20);
                url.searchParams.set('difficulty', difficulty.value);
                url.searchParams.delete('edit');
                window.location.href = url.toString();
            }));
            document.getElementById('refreshPreview').addEventListener('click', () => {
                const url = new URL(window.location.href);
                url.searchParams.set('subject_id', subject.value);
                url.searchParams.set('question_count', count.value || 20);
                url.searchParams.set('difficulty', difficulty.value);
                url.searchParams.set('_refresh', Date.now());
                url.searchParams.delete('edit');
                window.location.href = url.toString();
            });
            document.querySelectorAll('.paper-question .ql-formula').forEach(formula => {
                const latex = formula.getAttribute('data-value') || formula.textContent || '';
                if (!latex.trim()) return;
                const mf = document.createElement('math-field');
                mf.setAttribute('read-only', '');
                mf.setAttribute('virtual-keyboard-mode', 'off');
                mf.className = 'math-field-preview';
                mf.value = latex;
                formula.replaceWith(mf);
            });
            if (typeof window.renderMathInElement === 'function') window.renderMathInElement(document.getElementById('examPaper'), {
                delimiters: [{
                    left: '$$',
                    right: '$$',
                    display: true
                }, {
                    left: '\\[',
                    right: '\\]',
                    display: true
                }, {
                    left: '\\(',
                    right: '\\)',
                    display: false
                }],
                ignoredTags: ['script', 'noscript', 'style', 'textarea', 'pre', 'code', 'math-field'],
                throwOnError: false,
                strict: 'ignore'
            });
            updatePaper();
        });
    </script>
</body>

</html>