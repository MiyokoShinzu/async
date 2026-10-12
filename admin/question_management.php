<?php
include './globals/checks.php';
require_once '../src/connection.php';
$db = $mysqli;
if (!$db instanceof mysqli) {
    exit('Database connection is unavailable.');
}
$db->set_charset('utf8mb4');
$user = $_SESSION['user'] ?? [];
$adminId = $user['id'] ?? null;
if (empty($_SESSION['qm_csrf'])) $_SESSION['qm_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['qm_csrf'];
$msg = '';
$msgType = 'success';
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
function execsql($db, $sql, $types = '', $args = [])
{
    $s = $db->prepare($sql);
    if (!$s) throw new Exception($db->error);
    if ($types) $s->bind_param($types, ...$args);
    if (!$s->execute()) throw new Exception($s->error);
    $s->close();
}
function post_id($key)
{
    return (int)($_POST[$key] ?? 0);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) throw new Exception('Session token expired. Refresh and try again.');
        $a = $_POST['action'] ?? '';
        if ($a === 'subject') {
            $id = post_id('id');
            $code = trim($_POST['subject_code'] ?? '');
            $name = trim($_POST['subject_name'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
            if (!$code || !$name) throw new Exception('Subject code and name are required.');
            if ($id) execsql($db, 'UPDATE subjects SET subject_code=?,subject_name=?,description=?,status=? WHERE id=?', 'ssssi', [$code, $name, $desc, $status, $id]);
            else execsql($db, 'INSERT INTO subjects(subject_code,subject_name,description,status) VALUES(?,?,?,?)', 'ssss', [$code, $name, $desc, $status]);
            $msg = 'Subject saved.';
        } elseif ($a === 'topic') {
            $id = post_id('id');
            $sid = post_id('subject_id');
            $name = trim($_POST['topic_name'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
            if (!$sid || !$name) throw new Exception('Select a subject and enter a topic name.');
            if ($id) execsql($db, 'UPDATE subject_topics SET subject_id=?,topic_name=?,description=?,status=? WHERE id=?', 'isssi', [$sid, $name, $desc, $status, $id]);
            else execsql($db, 'INSERT INTO subject_topics(subject_id,topic_name,description,status) VALUES(?,?,?,?)', 'isss', [$sid, $name, $desc, $status]);
            $msg = 'Topic saved.';
        } elseif ($a === 'question') {
            $id = post_id('id');
            $sid = post_id('subject_id');
            $tid = post_id('topic_id');
            $difficulty = in_array($_POST['difficulty'] ?? '', ['easy', 'medium', 'hard'], true) ? $_POST['difficulty'] : 'medium';
            $q = trim($_POST['question_text'] ?? '');
            $ex = trim($_POST['explanation'] ?? '');
            $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
            $correct = $_POST['correct_choice'] ?? '';
            $choices = [];
            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $label) {
                $v = trim($_POST['choice_text'][$label] ?? '');
                if ($v !== '') $choices[$label] = $v;
            }
            if (!$sid || !$tid || !$q) throw new Exception('Subject, topic, and question are required.');
            if (count($choices) < 2 || !isset($choices[$correct])) throw new Exception('Enter at least two choices and select the correct answer.');
            if (!rows($db, 'SELECT id FROM subject_topics WHERE id=? AND subject_id=?', 'ii', [$tid, $sid])) throw new Exception('The topic does not belong to that subject.');
            $db->begin_transaction();
            try {
                if ($id) {
                    execsql($db, 'UPDATE question_bank SET subject_id=?,topic_id=?,difficulty=?,question_text=?,explanation=?,status=? WHERE id=?', 'iissssi', [$sid, $tid, $difficulty, $q, $ex, $status, $id]);
                    execsql($db, 'DELETE FROM question_choices WHERE question_id=?', 'i', [$id]);
                    $qid = $id;
                } else {
                    $by = trim($_SESSION['user']['name'] ?? $_SESSION['username'] ?? 'Admin');
                    execsql($db, 'INSERT INTO question_bank(subject_id,topic_id,difficulty,question_text,explanation,status,created_by) VALUES(?,?,?,?,?,?,?)', 'iisssss', [$sid, $tid, $difficulty, $q, $ex, $status, $by]);
                    $qid = $db->insert_id;
                }
                foreach ($choices as $label => $text) execsql($db, 'INSERT INTO question_choices(question_id,choice_label,choice_text,is_correct) VALUES(?,?,?,?)', 'issi', [$qid, $label, $text, $label === $correct ? 1 : 0]);
                $db->commit();
            } catch (Throwable $x) {
                $db->rollback();
                throw $x;
            }
            $msg = 'Question and answer choices saved.';
        } elseif ($a === 'delete_question') {
            $id = post_id('id');
            $db->begin_transaction();
            try {
                execsql($db, 'DELETE FROM question_choices WHERE question_id=?', 'i', [$id]);
                execsql($db, 'DELETE FROM question_bank WHERE id=?', 'i', [$id]);
                $db->commit();
            } catch (Throwable $x) {
                $db->rollback();
                throw $x;
            }
            $msg = 'Question deleted.';
        } else throw new Exception('Unknown action.');
    } catch (Throwable $x) {
        $msg = $x->getMessage();
        $msgType = 'danger';
    }
}
$requestedTab = $_GET['tab'] ?? 'questions';
$tab = in_array($requestedTab, ['questions', 'subjects', 'topics'], true) ? $requestedTab : 'questions';
$editQ = null;
$editC = [];
$editS = null;
$editT = null;
try {
    if (isset($_GET['edit'])) {
        $r = rows($db, 'SELECT * FROM question_bank WHERE id=?', 'i', [(int)$_GET['edit']]);
        $editQ = $r[0] ?? null;
        if ($editQ) $editC = rows($db, 'SELECT * FROM question_choices WHERE question_id=?', 'i', [$editQ['id']]);
    }
    if (isset($_GET['edit_subject'])) {
        $r = rows($db, 'SELECT * FROM subjects WHERE id=?', 'i', [(int)$_GET['edit_subject']]);
        $editS = $r[0] ?? null;
    }
    if (isset($_GET['edit_topic'])) {
        $r = rows($db, 'SELECT * FROM subject_topics WHERE id=?', 'i', [(int)$_GET['edit_topic']]);
        $editT = $r[0] ?? null;
    }
    $subjects = rows($db, 'SELECT * FROM subjects ORDER BY subject_name');
    $topics = rows($db, 'SELECT t.*,s.subject_code,s.subject_name FROM subject_topics t JOIN subjects s ON s.id=t.subject_id ORDER BY s.subject_name,t.topic_name');
    $filters = [];
    $types = '';
    $args = [];
    $sql = 'SELECT q.*,s.subject_code,s.subject_name,t.topic_name FROM question_bank q JOIN subjects s ON s.id=q.subject_id JOIN subject_topics t ON t.id=q.topic_id WHERE 1=1';
    if (!empty($_GET['subject'])) {
        $sql .= ' AND q.subject_id=?';
        $types .= 'i';
        $args[] = (int)$_GET['subject'];
    }
    if (!empty($_GET['topic'])) {
        $sql .= ' AND q.topic_id=?';
        $types .= 'i';
        $args[] = (int)$_GET['topic'];
    }
    if (in_array($_GET['difficulty'] ?? '', ['easy', 'medium', 'hard'], true)) {
        $sql .= ' AND q.difficulty=?';
        $types .= 's';
        $args[] = $_GET['difficulty'];
    }
    if (trim($_GET['q'] ?? '') !== '') {
        $sql .= ' AND q.question_text LIKE ?';
        $types .= 's';
        $args[] = '%' . trim($_GET['q']) . '%';
    }
    $sql .= ' ORDER BY q.created_at DESC LIMIT 200';
    $questions = rows($db, $sql, $types, $args);
    $stats = rows($db, "SELECT (SELECT COUNT(*) FROM subjects) subjects_count,(SELECT COUNT(*) FROM subject_topics) topics_count,(SELECT COUNT(*) FROM question_bank) questions_count,(SELECT COUNT(*) FROM question_bank WHERE status='active') active_count")[0];
} catch (Throwable $x) {
    http_response_code(500);
    exit('Unable to load management data: ' . e($x->getMessage()));
}
$cmap = [];
foreach ($editC as $c) $cmap[$c['choice_label']] = $c;
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'globals/head.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/katex.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">

<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 25px
    }

    .page-header h2 {
        margin: 0;
        font-size: 1.65rem;
        font-weight: 700;
        color: #1f2937
    }

    .page-header p {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: .92rem
    }

    .page-header .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        border-radius: 8px;
        font-size: .88rem;
        padding: 9px 15px;
        white-space: nowrap
    }

    .qm-stats {
        margin-bottom: 22px
    }

    .qm-stat {
        height: 100%;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        padding: 18px 20px
    }

    .qm-stat-label {
        color: #6b7280;
        font-size: .85rem
    }

    .qm-stat-value {
        font-size: 1.8rem;
        font-weight: 750;
        color: #1f2937;
        margin-top: 5px
    }

    .qm-stat-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 11px;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 1.2rem
    }

    .qm-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 20px
    }

    .qm-tabs a {
        padding: 9px 15px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #4b5563;
        text-decoration: none;
        font-size: .9rem;
        font-weight: 600;
        background: #fff
    }

    .qm-tabs a.active {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #fff
    }

    .activity-form-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        overflow: hidden;
        margin-bottom: 22px
    }

    .activity-form-header {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 22px 25px;
        background: linear-gradient(135deg, #f8faff, #f5f3ff);
        border-bottom: 1px solid #e5e7eb
    }

    .activity-form-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: #fff;
        font-size: 1.35rem;
        box-shadow: 0 5px 12px rgba(79, 70, 229, .2)
    }

    .activity-form-header h5 {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #1f2937
    }

    .activity-form-header p {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: .85rem
    }

    .activity-form-card .qm-card-body {
        padding: 25px
    }

    .activity-form-card .form-label {
        display: block;
        margin-bottom: 7px;
        color: #374151;
        font-size: .88rem;
        font-weight: 600
    }

    .activity-form-card .form-control,
    .activity-form-card .form-select {
        min-height: 44px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #1f2937;
        font-size: .9rem;
        padding: 9px 12px;
        transition: border-color .2s, box-shadow .2s
    }

    .activity-form-card textarea.form-control {
        min-height: 90px;
        resize: vertical;
        line-height: 1.55
    }

    .activity-form-card .form-control:focus,
    .activity-form-card .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
        outline: none
    }

    .qm-alert {
        border: 0;
        border-radius: 10px;
        padding: 13px 16px;
        margin-bottom: 20px;
        font-size: .9rem
    }

    .qm-alert.alert-success {
        background: #ecfdf3;
        color: #15803d
    }

    .qm-alert.alert-danger {
        background: #fef2f2;
        color: #dc2626
    }

    .qm-table-wrap {
        padding: 0 22px 20px
    }

    .qm-table {
        margin: 0
    }

    .qm-table>:not(caption)>*>* {
        padding: 13px 12px;
        vertical-align: middle
    }

    .qm-table thead th {
        font-size: .76rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .03em;
        background: #f9fafb;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb
    }

    .qm-table tbody td {
        font-size: .9rem
    }

    .qm-table .question {
        min-width: 240px;
        max-width: 480px
    }

    .small-muted {
        color: #6b7280;
        font-size: .82rem
    }

    .qm-choice {
        display: grid;
        grid-template-columns: 24px minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px
    }

    .qm-form-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb
    }

    .qm-form-footer .btn {
        min-height: 42px;
        padding: 9px 17px;
        border-radius: 8px;
        font-size: .88rem;
        font-weight: 600
    }

    .qm-form-footer .btn-primary {
        background: #4f46e5;
        border-color: #4f46e5
    }

    .qm-form-footer .btn-primary:hover {
        background: #4338ca;
        border-color: #4338ca;
        box-shadow: 0 4px 10px rgba(79, 70, 229, .2);
        transform: translateY(-1px)
    }

    .qm-form-footer .btn-light {
        background: #f3f4f6;
        border-color: #e5e7eb;
        color: #4b5563
    }

    @media(max-width:768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px
        }

        .page-header>div:last-child,
        .page-header .btn {
            width: 100%
        }

        .activity-form-header {
            padding: 18px
        }

        .activity-form-card .qm-card-body {
            padding: 18px
        }

        .qm-table-wrap {
            padding: 0 12px 14px
        }

        .qm-form-footer {
            flex-direction: column-reverse;
            align-items: stretch
        }

        .qm-form-footer .btn {
            width: 100%
        }
    }

    @media(max-width:480px) {
        .page-header h2 {
            font-size: 1.4rem
        }

        .page-header p {
            font-size: .84rem
        }

        .activity-form-header {
            align-items: flex-start
        }

        .activity-form-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            font-size: 1.15rem
        }

        .activity-form-card .qm-card-body {
            padding: 16px
        }
    }

    /* =========================================================
   RESPONSIVE LAYOUT — QUESTION MANAGEMENT
   Optimized for desktop, tablet, and mobile screens.
   ========================================================= */
    html {
        width: 100%;
        max-width: 100%;
    }

    body {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    img,
    svg,
    video,
    canvas {
        max-width: 100%;
    }

    .main-content,
    .content-wrapper {
        min-width: 0;
        max-width: 100%;
    }

    .content-wrapper {
        width: 100%;
    }

    .activity-form-card,
    .qm-stat,
    .qm-tabs,
    .page-header,
    .qm-card-body,
    .activity-form-header {
        min-width: 0;
    }

    .activity-form-header>div:last-child,
    .page-header>div:first-child {
        min-width: 0;
    }

    .activity-form-header h5,
    .activity-form-header p,
    .page-header h2,
    .page-header p {
        overflow-wrap: anywhere;
    }

    .qm-card-body {
        max-width: 100%;
    }

    .activity-form-card .form-control,
    .activity-form-card .form-select,
    .qm-card-body .form-control,
    .qm-card-body .form-select,
    .qm-card-body textarea {
        width: 100%;
        min-width: 0;
    }

    .qm-table-wrap,
    .table-responsive {
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .qm-table {
        width: 100%;
    }

    .qm-table td,
    .qm-table th {
        overflow-wrap: anywhere;
    }

    .qm-table .question {
        min-width: 220px;
    }

    .qm-tabs {
        max-width: 100%;
    }

    .qm-choice {
        min-width: 0;
    }

    .qm-choice>* {
        min-width: 0;
    }

    .qm-choice .form-control {
        width: 100%;
    }

    .qm-form-footer {
        flex-wrap: wrap;
    }

    .qm-form-footer .btn {
        min-width: 110px;
    }

    .qm-card-body .btn {
        max-width: 100%;
    }

    .qm-card-body form>.row {
        min-width: 0;
    }

    @media (max-width: 1199.98px) {
        .qm-stat {
            padding: 16px;
        }

        .qm-stat-value {
            font-size: 1.6rem;
        }

        .activity-form-card .qm-card-body {
            padding: 22px;
        }

        .qm-table-wrap {
            padding-left: 16px;
            padding-right: 16px;
        }
    }

    @media (max-width: 991.98px) {
        .page-header {
            align-items: flex-start;
        }

        .page-header h2 {
            font-size: 1.5rem;
        }

        .qm-stats>[class*="col-"] {
            min-width: 0;
        }

        .qm-choice {
            grid-template-columns: 20px minmax(0, 1fr);
            gap: 8px;
        }

        .qm-choice>label {
            grid-column: 2;
            justify-self: start;
        }
    }

    @media (max-width: 767.98px) {
        .main-content {
            margin-left: 0;
            width: 100%;
            max-width: 100%;
        }

        .content-wrapper {
            padding-left: 14px;
            padding-right: 14px;
        }

        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
            margin-bottom: 20px;
        }

        .page-header h2 {
            font-size: 1.4rem;
            line-height: 1.3;
        }

        .page-header p {
            font-size: .88rem;
        }

        .page-header>div:last-child,
        .page-header .btn {
            width: 100%;
        }

        .page-header .btn {
            white-space: normal;
        }

        .qm-stat {
            padding: 14px;
            border-radius: 12px;
        }

        .qm-stat-label {
            font-size: .78rem;
        }

        .qm-stat-value {
            font-size: 1.45rem;
        }

        .qm-stat-icon {
            width: 36px;
            height: 36px;
            font-size: 1rem;
        }

        .qm-tabs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 7px;
        }

        .qm-tabs a {
            padding: 10px 6px;
            text-align: center;
            font-size: .82rem;
        }

        .activity-form-header {
            padding: 17px;
            gap: 12px;
        }

        .activity-form-header h5 {
            font-size: 1rem;
        }

        .activity-form-header p {
            font-size: .82rem;
        }

        .activity-form-icon {
            width: 42px;
            height: 42px;
            min-width: 42px;
            font-size: 1.1rem;
        }

        .activity-form-card .qm-card-body,
        .qm-card-body {
            padding: 17px;
        }

        .qm-table-wrap {
            padding: 0 10px 12px;
        }

        .qm-table {
            min-width: 620px;
        }

        .qm-table .question {
            min-width: 220px;
        }

        .qm-form-footer {
            flex-direction: column-reverse;
            align-items: stretch;
        }

        .qm-form-footer .btn {
            width: 100%;
            min-width: 0;
        }

        .qm-card-body form .btn {
            min-height: 42px;
        }

        .qm-card-body form .row {
            --bs-gutter-x: .85rem;
        }
    }

    @media (max-width: 480px) {
        .content-wrapper {
            padding-left: 10px;
            padding-right: 10px;
        }

        .page-header h2 {
            font-size: 1.25rem;
        }

        .page-header p {
            font-size: .82rem;
        }

        .qm-stats {
            --bs-gutter-x: .6rem;
            --bs-gutter-y: .6rem;
        }

        .qm-stat {
            padding: 12px;
        }

        .qm-stat-label {
            font-size: .72rem;
        }

        .qm-stat-value {
            font-size: 1.3rem;
        }

        .qm-stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
        }

        .qm-tabs {
            gap: 5px;
        }

        .qm-tabs a {
            font-size: .76rem;
            padding: 9px 4px;
        }

        .activity-form-header {
            padding: 14px;
            align-items: flex-start;
        }

        .activity-form-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
        }

        .activity-form-card .qm-card-body,
        .qm-card-body {
            padding: 14px;
        }

        .qm-table-wrap {
            padding-left: 8px;
            padding-right: 8px;
        }

        .qm-choice {
            gap: 7px;
        }

        .qm-choice .form-control {
            font-size: .84rem;
            padding: 8px 9px;
        }

        .qm-alert {
            padding: 11px 12px;
            font-size: .84rem;
        }
    }

    @media (max-width: 360px) {
        .qm-stat-icon {
            display: none;
        }

        .qm-tabs a {
            font-size: .7rem;
        }

        .activity-form-header {
            gap: 9px;
        }

        .activity-form-header p {
            font-size: .78rem;
        }
    }


    /* Subjects and Topics: full-width form row followed by full-width table row */
    .qm-tabs+.row.g-4>.col-12 {
        width: 100%;
        max-width: 100%;
        flex: 0 0 100%;
    }

    .qm-tabs+.row.g-4>.col-12>.activity-form-card {
        width: 100%;
    }

    .qm-tabs+.row.g-4 .qm-table-wrap {
        width: 100%;
    }

    @media (max-width: 767.98px) {
        .qm-tabs+.row.g-4 {
            --bs-gutter-x: .75rem;
            --bs-gutter-y: 1rem;
        }

        .qm-tabs+.row.g-4>.col-12 {
            padding-left: calc(var(--bs-gutter-x) * .5);
            padding-right: calc(var(--bs-gutter-x) * .5);
        }
    }


    /* Quill document-style editors and formula support */
    .qm-editor-help {
        font-size: .82rem;
        color: #64748b;
        margin: -1px 0 10px
    }

    .qm-quill-wrap {
        width: 100%;
        min-width: 0;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        overflow: visible;
        background: #fff
    }

    .qm-quill-wrap:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .12)
    }

    .qm-quill-wrap .ql-toolbar.ql-snow {
        border: 0;
        border-bottom: 1px solid #e5e7eb;
        border-radius: 10px 10px 0 0;
        background: #f8fafc;
        padding: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 2px
    }

    .qm-quill-wrap .ql-container.ql-snow {
        border: 0;
        border-radius: 0 0 10px 10px;
        font: 400 15px/1.65 Inter, system-ui, sans-serif
    }

    .qm-quill-wrap .ql-editor {
        min-height: 180px;
        padding: 18px 20px;
        overflow-wrap: anywhere
    }

    .qm-quill-wrap .ql-editor.ql-blank::before {
        font-style: normal;
        color: #9ca3af;
        left: 20px;
        right: 20px
    }

    .qm-quill-wrap .ql-formula {
        font-size: 1.05em
    }

    .qm-quill-wrap .ql-toolbar .ql-picker {
        max-width: 100%
    }

    .qm-quill-wrap .ql-toolbar .ql-picker-label {
        border-radius: 5px
    }

    .qm-quill-wrap .ql-toolbar button {
        border-radius: 5px
    }

    .qm-quill-wrap .ql-toolbar button:hover,
    .qm-quill-wrap .ql-toolbar button.ql-active {
        background: #eef2ff
    }

    .qm-choice-editor-wrap {
        grid-column: 2 / -1;
        min-width: 0;
        width: 100%
    }

    .qm-choice-editor-wrap .qm-quill-wrap .ql-editor {
        min-height: 62px;
        padding: 10px 12px
    }

    .qm-choice-editor-wrap .qm-quill-wrap .ql-toolbar.ql-snow {
        padding: 4px 6px
    }

    .qm-choice-editor-wrap .qm-quill-wrap .ql-container.ql-snow {
        font-size: .9rem
    }

    .qm-choice-editor-wrap .qm-quill-wrap .ql-toolbar button {
        width: 26px;
        height: 26px;
        padding: 3px
    }

    .qm-choice-editor-wrap .qm-quill-wrap .ql-toolbar .ql-picker-label {
        height: 26px;
        padding-top: 3px
    }

    .qm-choice-editor-wrap .qm-quill-wrap .ql-toolbar .ql-picker-options {
        z-index: 20
    }

    .qm-quill-wrap .ql-tooltip {
        z-index: 30
    }

    .qm-table .qm-question-preview {
        max-height: 7em;
        overflow: hidden
    }

    .qm-card-body select.form-select,
    .activity-form-card select.form-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 2.5rem !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.7' d='m3 6 5 5 5-5'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right .8rem center !important;
        background-size: 14px 14px !important;
    }

    @media(max-width:767.98px) {
        .qm-quill-wrap .ql-toolbar.ql-snow {
            padding: 6px
        }

        .qm-quill-wrap .ql-editor {
            min-height: 155px;
            padding: 14px
        }

        .qm-choice {
            grid-template-columns: 20px minmax(0, 1fr)
        }

        .qm-choice-editor-wrap {
            grid-column: 2
        }

        .qm-quill-wrap .ql-toolbar .ql-formats {
            margin-right: 5px
        }

        .qm-quill-wrap .ql-toolbar .ql-picker.ql-header {
            width: 88px
        }

        .qm-quill-wrap .ql-toolbar .ql-picker.ql-size {
            width: 76px
        }
    }

    @media(max-width:420px) {
        .qm-quill-wrap .ql-toolbar .ql-picker.ql-font {
            width: 80px
        }

        .qm-quill-wrap .ql-toolbar .ql-picker.ql-header {
            width: 72px
        }

        .qm-quill-wrap .ql-toolbar .ql-picker.ql-size {
            width: 66px
        }

        .qm-quill-wrap .ql-editor {
            font-size: 14px
        }
    }


    /* MathLive equation dialog */
    .qm-math-modal {
        position: fixed;
        inset: 0;
        z-index: 100000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(15, 23, 42, .58)
    }

    .qm-math-modal.is-open {
        display: flex
    }

    .qm-math-dialog {
        width: min(760px, 100%);
        max-height: 92vh;
        overflow: auto;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, .25)
    }

    .qm-math-dialog-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb
    }

    .qm-math-dialog-head h5 {
        margin: 0;
        font-weight: 700;
        color: #111827
    }

    .qm-math-dialog-body {
        padding: 20px
    }

    .qm-math-dialog math-field {
        display: block;
        width: 100%;
        min-height: 100px;
        padding: 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #fff;
        font-size: 1.35rem
    }

    .qm-math-dialog .qm-math-presets {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 12px 0
    }

    .qm-math-dialog .qm-math-presets button {
        border: 1px solid #dbe2ea;
        background: #f8fafc;
        border-radius: 8px;
        padding: 7px 10px;
        color: #334155
    }

    .qm-math-dialog .qm-math-presets button:hover {
        background: #eef2ff;
        border-color: #a5b4fc
    }

    .qm-math-dialog .qm-math-latex {
        font-size: .82rem;
        color: #64748b;
        overflow-wrap: anywhere;
        margin-top: 12px
    }

    .qm-math-dialog-foot {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 14px 20px;
        border-top: 1px solid #e5e7eb
    }

    .qm-quill-wrap .ql-editor .ql-formula {
        font-size: 1.1em
    }

    @media(max-width:575px) {

        .qm-math-dialog-head,
        .qm-math-dialog-body {
            padding: 14px
        }

        .qm-math-dialog-foot {
            padding: 12px 14px
        }

        .qm-math-dialog math-field {
            font-size: 1.1rem;
            min-height: 85px
        }
    }

    /* Floating question preview - added without changing existing form behavior */
    .qm-preview-fab {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 9990;
        width: 56px;
        height: 56px;
        border: 0;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        background: #4f46e5;
        box-shadow: 0 8px 24px rgba(79, 70, 229, .35);
        font-size: 1.35rem;
        cursor: pointer;
        transition: transform .2s, background .2s, box-shadow .2s;
    }

    .qm-preview-fab:hover {
        color: #fff;
        background: #4338ca;
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(79, 70, 229, .4);
    }

    .qm-preview-modal {
        position: fixed;
        inset: 0;
        z-index: 100001;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
        background: rgba(15, 23, 42, .6);
    }

    .qm-preview-modal.is-open {
        display: flex;
    }

    .qm-preview-dialog {
        width: min(780px, 100%);
        max-height: 90vh;
        overflow: auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 24px 80px rgba(0, 0, 0, .28);
    }

    .qm-preview-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid #e5e7eb;
    }

    .qm-preview-head h5 {
        margin: 0;
        font-weight: 700;
        color: #111827;
    }

    .qm-preview-body {
        padding: 22px;
    }

    .qm-preview-question {
        font-size: 1.05rem;
        line-height: 1.7;
        margin-bottom: 18px;
        overflow-wrap: anywhere;
    }

    .qm-preview-choice {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 11px 13px;
        margin: 8px 0;
        border: 1px solid #e5e7eb;
        border-radius: 9px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .qm-preview-choice-label {
        flex: 0 0 24px;
        font-weight: 700;
        color: #4f46e5;
    }

    .qm-preview-explanation {
        margin-top: 18px;
        padding: 13px 15px;
        background: #f8fafc;
        border-radius: 9px;
        color: #475569;
        line-height: 1.6;
    }

    .qm-preview-empty {
        color: #94a3b8;
        font-style: italic;
    }

    @media (max-width: 575px) {
        .qm-preview-fab {
            right: 16px;
            bottom: 16px;
            width: 50px;
            height: 50px;
        }

        .qm-preview-head,
        .qm-preview-body {
            padding: 16px;
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
                    <h2>Question Management</h2>
                    <p>Manage subjects, topics, questions, and multiple-choice answers.</p>
                </div>
                <div><a class="btn btn-primary" href="?tab=questions#editor"><i class="bi bi-plus-lg me-1"></i>Add Question</a></div>
            </div>
            <?php if ($msg): ?><div class="alert alert-<?= e($msgType) ?> qm-alert alert-dismissible fade show"><?= e($msg) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <div class="row g-3 qm-stats"><?php foreach ([['Total subjects', $stats['subjects_count'], 'bi-journal-bookmark'], ['Total topics', $stats['topics_count'], 'bi-diagram-3'], ['Question bank', $stats['questions_count'], 'bi-patch-question'], ['Active questions', $stats['active_count'], 'bi-check-circle']] as $st): ?><div class="col-6 col-xl-3">
                        <div class="qm-stat">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="qm-stat-label"><?= e($st[0]) ?></div>
                                    <div class="qm-stat-value"><?= (int)$st[1] ?></div>
                                </div>
                                <div class="qm-stat-icon"><i class="bi <?= e($st[2]) ?>"></i></div>
                            </div>
                        </div>
                    </div><?php endforeach; ?></div>
            <div class="qm-tabs"><a class="<?= $tab === 'questions' ? 'active' : '' ?>" href="?tab=questions"><i class="bi bi-patch-question me-1"></i>Questions</a><a class="<?= $tab === 'subjects' ? 'active' : '' ?>" href="?tab=subjects"><i class="bi bi-journal-bookmark me-1"></i>Subjects</a><a class="<?= $tab === 'topics' ? 'active' : '' ?>" href="?tab=topics"><i class="bi bi-diagram-3 me-1"></i>Topics</a></div>
            <?php if ($tab === 'questions'): ?>
                <div class="activity-form-card" id="editor">
                    <div class="activity-form-header">
                        <div class="activity-form-icon"><i class="bi bi-patch-question"></i></div>
                        <div>
                            <h5><?= $editQ ? 'Edit Question' : 'Create Question' ?></h5>
                            <p>Enter question details and configure the correct answer.</p>
                        </div>
                    </div>
                    <div class="qm-card-body">
                        <form method="post"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="question"><input type="hidden" name="id" value="<?= (int)($editQ['id'] ?? 0) ?>">
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label">Subject *</label><select class="form-select" name="subject_id" id="subjectSelect" required>
                                        <option value="">Select subject</option><?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>" <?= ((int)($editQ['subject_id'] ?? 0) === (int)$s['id']) ? 'selected' : '' ?>><?= e($s['subject_code'] . ' — ' . $s['subject_name']) ?></option><?php endforeach; ?>
                                    </select></div>
                                <div class="col-md-4"><label class="form-label">Topic *</label><select class="form-select" name="topic_id" id="topicSelect" required>
                                        <option value="">Select topic</option><?php foreach ($topics as $t): ?><option data-subject="<?= (int)$t['subject_id'] ?>" value="<?= (int)$t['id'] ?>" <?= ((int)($editQ['topic_id'] ?? 0) === (int)$t['id']) ? 'selected' : '' ?>><?= e($t['topic_name'] . ' — ' . $t['subject_code']) ?></option><?php endforeach; ?>
                                    </select></div>
                                <div class="col-md-4"><label class="form-label">Difficulty</label><select class="form-select" name="difficulty"><?php foreach (['easy', 'medium', 'hard'] as $d): ?><option value="<?= $d ?>" <?= ($editQ['difficulty'] ?? 'medium') === $d ? 'selected' : '' ?>><?= ucfirst($d) ?></option><?php endforeach; ?></select></div>
                                <div class="col-12"><label class="form-label">Question text *</label>
                                    <div class="qm-editor-help">Format the question like a document. Use <strong>ƒx</strong> to insert mathematical equations and formulas.</div><textarea class="form-control qm-rich-question" name="question_text" rows="3"><?= e($editQ['question_text'] ?? '') ?></textarea>
                                </div>
                                <div class="col-12"><label class="form-label">Choices (select the correct answer) *</label><?php foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $l): $c = $cmap[$l] ?? []; ?><div class="qm-choice"><span class="fw-bold"><?= $l ?></span><input class="form-control qm-rich-choice" name="choice_text[<?= $l ?>]" placeholder="Choice <?= $l ?>" value="<?= e($c['choice_text'] ?? '') ?>"><label class="d-flex align-items-center gap-1 small text-secondary mb-0"><input class="form-check-input mt-0" type="radio" name="correct_choice" value="<?= $l ?>" title="Mark this choice as correct" required <?= !empty($c['is_correct']) ? 'checked' : '' ?>> Correct</label></div><?php endforeach; ?></div>
                                <div class="col-12"><label class="form-label">Explanation (optional)</label><textarea class="form-control qm-rich-explanation" name="explanation" rows="2"><?= e($editQ['explanation'] ?? '') ?></textarea></div>
                                <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status">
                                        <option value="active" <?= ($editQ['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= ($editQ['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select></div>
                                <div class="col-12">
                                    <div class="qm-form-footer"><a class="btn btn-light" href="?tab=questions#editor">Cancel</a><button class="btn btn-primary"><i class="bi bi-check2 me-1"></i><?= $editQ ? 'Update Question' : 'Save Question' ?></button></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="activity-form-card">
                    <div class="activity-form-header">
                        <div class="activity-form-icon"><i class="bi bi-list-check"></i></div>
                        <div>
                            <h5>Question Bank</h5>
                            <p>Search, filter, edit, and review your saved questions.</p>
                        </div>
                    </div>
                    <div class="qm-card-body">
                        <form class="row g-2 mb-3" method="get"><input type="hidden" name="tab" value="questions">
                            <div class="col-lg-4"><input class="form-control" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search question text"></div>
                            <div class="col-md-4 col-lg-3"><select class="form-select" name="subject">
                                    <option value="">All subjects</option><?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>" <?= ((string)($_GET['subject'] ?? '') === (string)$s['id']) ? 'selected' : '' ?>><?= e($s['subject_code'] . ' — ' . $s['subject_name']) ?></option><?php endforeach; ?>
                                </select></div>
                            <div class="col-md-4 col-lg-2"><select class="form-select" name="difficulty">
                                    <option value="">All difficulty</option><?php foreach (['easy', 'medium', 'hard'] as $d): ?><option value="<?= $d ?>" <?= ($_GET['difficulty'] ?? '') === $d ? 'selected' : '' ?>><?= ucfirst($d) ?></option><?php endforeach; ?>
                                </select></div>
                            <div class="col-lg-3"><button class="btn btn-outline-primary">Filter</button> <a class="btn btn-light border" href="?tab=questions">Reset</a></div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-hover qm-table">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>Subject / Topic</th>
                                        <th>Difficulty</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody><?php if (!$questions): ?><tr>
                                            <td colspan="5" class="text-center text-secondary py-4">No questions found.</td>
                                        </tr><?php endif;
                                            foreach ($questions as $q): ?><tr>
                                            <td class="question">
                                                <div class="fw-semibold"><?= e(strip_tags($q['question_text'])) ?></div>
                                                <div class="small-muted">#<?= (int)$q['id'] ?> · <?= e($q['created_at']) ?></div>
                                            </td>
                                            <td><strong><?= e($q['subject_code']) ?></strong>
                                                <div class="small-muted"><?= e($q['topic_name']) ?></div>
                                            </td>
                                            <td><span class="badge text-bg-<?= ['easy' => 'success', 'medium' => 'warning', 'hard' => 'danger'][$q['difficulty']] ?>"><?= e(ucfirst($q['difficulty'])) ?></span></td>
                                            <td><span class="badge text-bg-<?= $q['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e(ucfirst($q['status'])) ?></span></td>
                                            <td>
                                                <div class="d-flex gap-1"><a class="btn btn-sm btn-outline-primary" href="?tab=questions&edit=<?= (int)$q['id'] ?>#editor"><i class="bi bi-pencil"></i></a>
                                                    <form method="post" onsubmit="return confirm('Delete this question and its answer choices?')"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="delete_question"><input type="hidden" name="id" value="<?= (int)$q['id'] ?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                                                </div>
                                            </td>
                                        </tr><?php endforeach; ?></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php elseif ($tab === 'subjects'): ?>
                <div class="row g-4">
                    <div class="col-12">
                        <div class="activity-form-card">
                            <div class="activity-form-header">
                                <div class="activity-form-icon"><i class="bi bi-journal-bookmark"></i></div>
                                <div>
                                    <h5><?= $editS ? 'Edit' : 'Add' ?> Subject</h5>
                                    <p>Maintain the subject details used by the question bank.</p>
                                </div>
                            </div>
                            <div class="qm-card-body">
                                <form method="post"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="subject"><input type="hidden" name="id" value="<?= (int)($editS['id'] ?? 0) ?>"><label class="form-label">Subject code *</label><input class="form-control mb-3" name="subject_code" maxlength="50" required value="<?= e($editS['subject_code'] ?? '') ?>"><label class="form-label">Subject name *</label><input class="form-control mb-3" name="subject_name" maxlength="150" required value="<?= e($editS['subject_name'] ?? '') ?>"><label class="form-label">Description</label><textarea class="form-control mb-3" name="description" rows="3"><?= e($editS['description'] ?? '') ?></textarea><label class="form-label">Status</label><select class="form-select mb-3" name="status">
                                        <option value="active" <?= ($editS['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= ($editS['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                    </select><button class="btn btn-primary w-100">Save Subject</button></form>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="activity-form-card">
                                <div class="activity-form-header">
                                    <div class="activity-form-icon"><i class="bi bi-journal-text"></i></div>
                                    <div>
                                        <h5>Subjects</h5>
                                        <p>Existing subjects available in the portal.</p>
                                    </div>
                                </div>
                                <div class="qm-table-wrap">
                                    <div class="table-responsive">
                                        <table class="table qm-table">
                                            <thead>
                                                <tr>
                                                    <th>Code</th>
                                                    <th>Subject</th>
                                                    <th>Status</th>
                                                    <th>Edit</th>
                                                </tr>
                                            </thead>
                                            <tbody><?php foreach ($subjects as $s): ?><tr>
                                                        <td><?= e($s['subject_code']) ?></td>
                                                        <td><?= e($s['subject_name']) ?><div class="small-muted"><?= e($s['description'] ?? '') ?></div>
                                                        </td>
                                                        <td><?= e(ucfirst($s['status'])) ?></td>
                                                        <td><a class="btn btn-sm btn-outline-primary" href="?tab=subjects&edit_subject=<?= (int)$s['id'] ?>"><i class="bi bi-pencil"></i></a></td>
                                                    </tr><?php endforeach; ?></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="activity-form-card">
                                    <div class="activity-form-header">
                                        <div class="activity-form-icon"><i class="bi bi-diagram-3"></i></div>
                                        <div>
                                            <h5><?= $editT ? 'Edit' : 'Add' ?> Topic</h5>
                                            <p>Assign topics to their related subjects.</p>
                                        </div>
                                    </div>
                                    <div class="qm-card-body">
                                        <form method="post"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="topic"><input type="hidden" name="id" value="<?= (int)($editT['id'] ?? 0) ?>"><label class="form-label">Subject *</label><select class="form-select mb-3" name="subject_id" required>
                                                <option value="">Select subject</option><?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (int)($editT['subject_id'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['subject_code'] . ' — ' . $s['subject_name']) ?></option><?php endforeach; ?>
                                            </select><label class="form-label">Topic name *</label><input class="form-control mb-3" name="topic_name" maxlength="150" required value="<?= e($editT['topic_name'] ?? '') ?>"><label class="form-label">Description</label><textarea class="form-control mb-3" name="description" rows="3"><?= e($editT['description'] ?? '') ?></textarea><label class="form-label">Status</label><select class="form-select mb-3" name="status">
                                                <option value="active" <?= ($editT['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= ($editT['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                            </select><button class="btn btn-primary w-100">Save Topic</button></form>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="activity-form-card">
                                        <div class="activity-form-header">
                                            <div class="activity-form-icon"><i class="bi bi-list-ul"></i></div>
                                            <div>
                                                <h5>Topics</h5>
                                                <p>Existing topics organized by subject.</p>
                                            </div>
                                        </div>
                                        <div class="qm-table-wrap">
                                            <div class="table-responsive">
                                                <table class="table qm-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Topic</th>
                                                            <th>Subject</th>
                                                            <th>Status</th>
                                                            <th>Edit</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody><?php foreach ($topics as $t): ?><tr>
                                                                <td><?= e($t['topic_name']) ?><div class="small-muted"><?= e($t['description'] ?? '') ?></div>
                                                                </td>
                                                                <td><?= e($t['subject_code'] . ' — ' . $t['subject_name']) ?></td>
                                                                <td><?= e(ucfirst($t['status'])) ?></td>
                                                                <td><a class="btn btn-sm btn-outline-primary" href="?tab=topics&edit_topic=<?= (int)$t['id'] ?>"><i class="bi bi-pencil"></i></a></td>
                                                            </tr><?php endforeach; ?></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="text-center small-muted mt-4">ETS-Async Learning Portal · Question Management</div>
                            </div>
    </main>

    <?php if ($tab === 'questions'): ?>
        <button type="button" class="qm-preview-fab" id="qmPreviewOpen" aria-label="Preview question" title="Preview question">
            <i class="bi bi-eye"></i>
        </button>
        <div class="qm-preview-modal" id="qmPreviewModal" role="dialog" aria-modal="true" aria-labelledby="qmPreviewTitle">
            <div class="qm-preview-dialog">
                <div class="qm-preview-head">
                    <h5 id="qmPreviewTitle"><i class="bi bi-eye me-2"></i>Question Preview</h5>
                    <button type="button" class="btn-close" id="qmPreviewClose" aria-label="Close"></button>
                </div>
                <div class="qm-preview-body">
                    <div class="qm-preview-question" id="qmPreviewQuestion"><span class="qm-preview-empty">Enter a question to see its preview.</span></div>
                    <div id="qmPreviewChoices"></div>
                    <div class="qm-preview-explanation" id="qmPreviewExplanation" style="display:none;"></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="qm-math-modal" id="qmMathModal" role="dialog" aria-modal="true" aria-labelledby="qmMathTitle">
        <div class="qm-math-dialog">
            <div class="qm-math-dialog-head">
                <h5 id="qmMathTitle"><i class="bi bi-sigma me-2"></i>Equation editor</h5>
                <button type="button" class="btn-close" id="qmMathClose" aria-label="Close"></button>
            </div>
            <div class="qm-math-dialog-body">
                <p class="small text-secondary mb-2">Build equations visually using the math keyboard. Supports fractions, roots, powers, integrals, matrices, limits, summations, and Greek symbols.</p>
                <div class="qm-math-presets">
                    <button type="button" data-latex="\frac{a}{b}">Fraction</button>
                    <button type="button" data-latex="\sqrt{x}">Square root</button>
                    <button type="button" data-latex="\int_a^b f(x)\,dx">Integral</button>
                    <button type="button" data-latex="\sum_{i=1}^{n} i">Summation</button>
                    <button type="button" data-latex="\begin{bmatrix}a&b\\c&d\end{bmatrix}">Matrix</button>
                    <button type="button" data-latex="\lim_{x\to 0}\frac{\sin x}{x}">Limit</button>
                </div>
                <math-field id="qmMathField" virtual-keyboard-mode="auto" smart-fence aria-label="Enter mathematical equation"></math-field>
                <div class="qm-math-latex">Tip: tap the equation field to open the math keyboard on mobile.</div>
            </div>
            <div class="qm-math-dialog-foot">
                <button type="button" class="btn btn-light" id="qmMathCancel">Cancel</button>
                <button type="button" class="btn btn-primary" id="qmMathInsert"><i class="bi bi-check2 me-1"></i>Insert equation</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/katex@0.19.0/dist/katex.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/mathlive@0.110.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toolbarOptions = [
                [{
                    header: [1, 2, 3, false]
                }],
                [{
                    font: []
                }, {
                    size: ['small', false, 'large', 'huge']
                }],
                ['bold', 'italic', 'underline', 'strike'],
                [{
                    color: []
                }, {
                    background: []
                }],
                [{
                    script: 'sub'
                }, {
                    script: 'super'
                }],
                [{
                    list: 'ordered'
                }, {
                    list: 'bullet'
                }],
                [{
                    align: []
                }],
                ['formula', 'blockquote', 'code-block'],
                ['clean']
            ];
            const allowedFormats = [
                'background', 'bold', 'color', 'font', 'code', 'italic', 'link', 'size', 'strike',
                'script', 'underline', 'blockquote', 'header', 'indent', 'list', 'align',
                'direction', 'code-block', 'formula'
            ];
            const editors = [];
            let activeMathQuill = null;
            let activeMathRange = null;

            const modal = document.getElementById('qmMathModal');
            const mathField = document.getElementById('qmMathField');

            function openMathEditor(quill) {
                activeMathQuill = quill;
                activeMathRange = quill.getSelection(true) || {
                    index: quill.getLength(),
                    length: 0
                };
                modal.classList.add('is-open');
                document.body.style.overflow = 'hidden';
                window.setTimeout(function() {
                    if (mathField) mathField.focus();
                }, 80);
            }

            function closeMathEditor() {
                modal.classList.remove('is-open');
                document.body.style.overflow = '';
                activeMathQuill = null;
                activeMathRange = null;
            }
            document.getElementById('qmMathClose').addEventListener('click', closeMathEditor);
            document.getElementById('qmMathCancel').addEventListener('click', closeMathEditor);
            modal.addEventListener('click', function(event) {
                if (event.target === modal) closeMathEditor();
            });
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) closeMathEditor();
            });
            document.querySelectorAll('.qm-math-presets [data-latex]').forEach(function(button) {
                button.addEventListener('click', function() {
                    if (mathField) {
                        mathField.value = button.dataset.latex || '';
                        mathField.focus();
                    }
                });
            });
            document.getElementById('qmMathInsert').addEventListener('click', function() {
                if (!activeMathQuill || !mathField) return;
                const latex = (mathField.value || '').trim();
                if (!latex) {
                    mathField.focus();
                    return;
                }
                const quill = activeMathQuill;
                const range = activeMathRange || {
                    index: quill.getLength(),
                    length: 0
                };
                if (range.length) quill.deleteText(range.index, range.length, 'user');
                quill.insertEmbed(range.index, 'formula', latex, 'user');
                quill.insertText(range.index + 1, ' ', 'user');
                quill.setSelection(range.index + 2, 0, 'silent');
                closeMathEditor();
                quill.focus();
            });

            function mountEditor(source, placeholder, compact) {
                if (!source) return null;
                const initial = source.value || '';
                const wrap = document.createElement('div');
                wrap.className = 'qm-quill-wrap' + (compact ? ' qm-quill-compact' : '');
                const host = document.createElement('div');
                host.className = 'qm-quill-host';
                source.insertAdjacentElement('afterend', wrap);
                wrap.appendChild(host);
                source.style.display = 'none';
                source.removeAttribute('required');

                const quill = new Quill(host, {
                    theme: 'snow',
                    placeholder: placeholder,
                    formats: allowedFormats,
                    modules: {
                        formula: true,
                        toolbar: toolbarOptions,
                        clipboard: {
                            matchVisual: false
                        }
                    }
                });
                if (initial.trim()) quill.clipboard.dangerouslyPasteHTML(initial, 'silent');
                const toolbar = quill.getModule('toolbar');
                toolbar.addHandler('formula', function() {
                    openMathEditor(quill);
                });
                editors.push({
                    source,
                    quill,
                    compact
                });
                return quill;
            }

            const questionEditor = mountEditor(
                document.querySelector('.qm-rich-question'),
                'Write your question here. Use the formula button for equations.',
                false
            );
            mountEditor(
                document.querySelector('.qm-rich-explanation'),
                'Explain the answer or show the calculation steps…',
                false
            );

            document.querySelectorAll('.qm-rich-choice').forEach(function(source) {
                const wrap = document.createElement('div');
                wrap.className = 'qm-choice-editor-wrap';
                source.insertAdjacentElement('afterend', wrap);
                source.style.display = 'none';
                source.removeAttribute('required');
                const richWrap = document.createElement('div');
                richWrap.className = 'qm-quill-wrap';
                wrap.appendChild(richWrap);
                const editorHost = document.createElement('div');
                editorHost.className = 'qm-quill-host';
                richWrap.appendChild(editorHost);
                const quill = new Quill(editorHost, {
                    theme: 'snow',
                    placeholder: source.getAttribute('placeholder') || 'Enter answer choice…',
                    formats: allowedFormats,
                    modules: {
                        formula: true,
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{
                                script: 'sub'
                            }, {
                                script: 'super'
                            }],
                            ['formula', 'clean']
                        ],
                        clipboard: {
                            matchVisual: false
                        }
                    }
                });
                const toolbar = quill.getModule('toolbar');
                toolbar.addHandler('formula', function() {
                    openMathEditor(quill);
                });
                if ((source.value || '').trim()) quill.clipboard.dangerouslyPasteHTML(source.value, 'silent');
                editors.push({
                    source,
                    quill,
                    compact: true
                });
            });

            // Floating live preview for the current question and its answer choices.
            const previewOpen = document.getElementById('qmPreviewOpen');
            const previewModal = document.getElementById('qmPreviewModal');
            const previewClose = document.getElementById('qmPreviewClose');
            const previewQuestion = document.getElementById('qmPreviewQuestion');
            const previewChoices = document.getElementById('qmPreviewChoices');
            const previewExplanation = document.getElementById('qmPreviewExplanation');

            function openQuestionPreview() {
                if (!previewModal) return;

                const questionItem = editors.find(item => item.source.classList.contains('qm-rich-question'));
                const explanationItem = editors.find(item => item.source.classList.contains('qm-rich-explanation'));

                if (questionItem && questionItem.quill) {
                    const questionHtml = questionItem.quill.root.innerHTML.trim();
                    previewQuestion.innerHTML = questionItem.quill.getText().trim() ||
                        questionItem.quill.root.querySelector('.ql-formula') ?
                        questionHtml : '<span class="qm-preview-empty">Enter a question to see its preview.</span>';
                }

                previewChoices.innerHTML = '';
                const choiceItems = editors.filter(item => item.source.classList.contains('qm-rich-choice'));
                choiceItems.forEach(function(item) {
                    const name = item.source.getAttribute('name') || '';
                    const match = name.match(/choice_text\[([A-F])\]/);
                    const label = match ? match[1] : '';
                    const hasText = item.quill.getText().trim() || item.quill.root.querySelector('.ql-formula');
                    if (!hasText) return;

                    const row = document.createElement('div');
                    row.className = 'qm-preview-choice';
                    const letter = document.createElement('span');
                    letter.className = 'qm-preview-choice-label';
                    letter.textContent = label ? label + '.' : '';
                    const content = document.createElement('div');
                    content.innerHTML = item.quill.root.innerHTML;
                    row.appendChild(letter);
                    row.appendChild(content);
                    previewChoices.appendChild(row);
                });

                if (explanationItem && explanationItem.quill &&
                    (explanationItem.quill.getText().trim() || explanationItem.quill.root.querySelector('.ql-formula'))) {
                    previewExplanation.style.display = '';
                    previewExplanation.innerHTML = '<strong>Explanation:</strong><div>' + explanationItem.quill.root.innerHTML + '</div>';
                } else {
                    previewExplanation.style.display = 'none';
                    previewExplanation.innerHTML = '';
                }

                previewModal.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            function closeQuestionPreview() {
                if (!previewModal) return;
                previewModal.classList.remove('is-open');
                document.body.style.overflow = '';
            }

            if (previewOpen && previewModal) {
                previewOpen.addEventListener('click', openQuestionPreview);
                previewClose.addEventListener('click', closeQuestionPreview);
                previewModal.addEventListener('click', function(event) {
                    if (event.target === previewModal) closeQuestionPreview();
                });
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape' && previewModal.classList.contains('is-open')) {
                        closeQuestionPreview();
                    }
                });
            }

            const form = questionEditor && questionEditor.root.closest('form');
            if (form) {
                form.addEventListener('submit', function(event) {
                    editors.forEach(function(item) {
                        const hasFormula = !!item.quill.root.querySelector('.ql-formula');
                        const plainText = item.quill.getText().replace(/\u00a0/g, ' ').trim();
                        item.source.value = (plainText || hasFormula) ?
                            item.quill.root.innerHTML :
                            '';
                    });
                    const question = editors.find(item => item.source.classList.contains('qm-rich-question'));
                    if (question) {
                        const hasFormula = !!question.quill.root.querySelector('.ql-formula');
                        if (!question.source.value.trim() || (!question.quill.getText().trim() && !hasFormula)) {
                            event.preventDefault();
                            alert('Please enter the question text or insert a formula.');
                            question.quill.focus();
                        }
                    }
                });
            }

            const subjectSelect = document.getElementById('subjectSelect');
            const topicSelect = document.getElementById('topicSelect');
            if (subjectSelect && topicSelect) {
                function filterTopics() {
                    Array.from(topicSelect.options).forEach(function(option, index) {
                        if (index > 0) option.hidden = option.dataset.subject !== subjectSelect.value;
                    });
                    if (topicSelect.selectedOptions[0] && topicSelect.selectedOptions[0].hidden) topicSelect.value = '';
                }
                subjectSelect.addEventListener('change', filterTopics);
                filterTopics();
            }
        });
    </script><?php include 'globals/scripts.php'; ?>
</body>

</html>