<?php
include './globals/checks.php';
require_once '../src/connection.php';
$db = $mysqli;
if (!$db instanceof mysqli) exit('Database connection is unavailable.');
$db->set_charset('utf8mb4');
$db->query("CREATE TABLE IF NOT EXISTS question_solutions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    solution_type ENUM('text','simple_text','rich_text','image','youtube') NOT NULL,
    solution_content MEDIUMTEXT NOT NULL,
    display_order TINYINT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_question_solutions_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
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
    $html = preg_replace('/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    $html = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '$1="#"', $html);
    return $html;
}

function question_media(mysqli $db, int $questionId): array
{
    return rows(
        $db,
        'SELECT id, media_type, media_url, display_order FROM question_media WHERE question_id = ? ORDER BY display_order, id',
        'i',
        [$questionId]
    );
}


function question_solutions(mysqli $db, int $questionId): array
{
    return rows($db, 'SELECT id, solution_type, solution_content, display_order FROM question_solutions WHERE question_id = ? ORDER BY display_order, id', 'i', [$questionId]);
}

function render_question_solutions(array $items): void
{
    if (!$items) return;
    echo '<div class="qp-solutions"><h6><i class="bi bi-lightbulb me-2"></i>Solution</h6>';
    foreach ($items as $item) {
        $type = (string)($item['solution_type'] ?? '');
        $content = trim((string)($item['solution_content'] ?? ''));
        if ($content === '') continue;
        if (in_array($type, ['text', 'simple_text'], true)) {
            echo '<div class="qp-solution-text rich-content">' . nl2br(e($content)) . '</div>';
            continue;
        }
        if ($type === 'rich_text') {
            echo '<div class="qp-solution-text rich-content">' . safe_rich($content) . '</div>';
            continue;
        }
        $parts = parse_url($content);
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $embedUrl = '';
        $label = '';
        if ($type === 'image' && in_array($host, ['drive.google.com', 'www.drive.google.com'], true)) {
            $fileId = '';
            if (preg_match('~/file/d/([A-Za-z0-9_-]+)~', $path, $m)) $fileId = $m[1];
            else {
                parse_str($parts['query'] ?? '', $query);
                if (!empty($query['id']) && preg_match('/^[A-Za-z0-9_-]+$/', (string)$query['id'])) $fileId = (string)$query['id'];
            }
            if ($fileId !== '') {
                $embedUrl = 'https://drive.google.com/file/d/' . rawurlencode($fileId) . '/preview';
                $label = 'Solution image';
            }
        } elseif ($type === 'youtube') {
            $videoId = '';
            if ($host === 'youtu.be') $videoId = explode('/', trim($path, '/'))[0] ?? '';
            elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
                parse_str($parts['query'] ?? '', $query);
                if (str_starts_with($path, '/watch')) $videoId = (string)($query['v'] ?? '');
                elseif (preg_match('~/(?:embed|shorts)/([A-Za-z0-9_-]+)~', $path, $m)) $videoId = $m[1];
            }
            if (preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) {
                $embedUrl = 'https://www.youtube-nocookie.com/embed/' . rawurlencode($videoId);
                $label = 'Solution video';
            }
        }
        if ($embedUrl !== '') echo '<div class="qp-solution-media"><div class="qp-media-label">' . e($label) . '</div><div class="qp-media-frame"><iframe src="' . e($embedUrl) . '" title="' . e($label) . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div><div class="qp-media-open"><a href="' . e($content) . '" target="_blank" rel="noopener noreferrer">Open original <i class="bi bi-box-arrow-up-right"></i></a></div></div>';
    }
    echo '</div>';
}

function render_question_media(array $items): void
{
    if (!$items) return;

    echo '<div class="qp-media-list" aria-label="Question media">';
    foreach ($items as $item) {
        $type = (string)($item['media_type'] ?? '');
        $url = (string)($item['media_url'] ?? '');
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $embedUrl = '';
        $label = '';

        if ($type === 'image' && in_array($host, ['drive.google.com', 'www.drive.google.com'], true)) {
            $fileId = '';
            if (preg_match('~/file/d/([A-Za-z0-9_-]+)~', $path, $match)) {
                $fileId = $match[1];
            } else {
                parse_str($parts['query'] ?? '', $query);
                if (!empty($query['id']) && preg_match('/^[A-Za-z0-9_-]+$/', (string)$query['id'])) {
                    $fileId = (string)$query['id'];
                }
            }
            if ($fileId !== '') {
                $embedUrl = 'https://drive.google.com/file/d/' . rawurlencode($fileId) . '/preview';
                $label = 'Google Drive image';
            }
        } elseif ($type === 'youtube') {
            $videoId = '';
            if ($host === 'youtu.be') {
                $videoId = explode('/', trim($path, '/'))[0] ?? '';
            } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
                parse_str($parts['query'] ?? '', $query);
                if (str_starts_with($path, '/watch')) {
                    $videoId = (string)($query['v'] ?? '');
                } elseif (preg_match('~/(?:embed|shorts)/([A-Za-z0-9_-]+)~', $path, $match)) {
                    $videoId = $match[1];
                }
            }
            if (preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) {
                $embedUrl = 'https://www.youtube-nocookie.com/embed/' . rawurlencode($videoId);
                $label = 'YouTube video';
            }
        }

        if ($embedUrl === '') continue;
        echo '<div class="qp-media-item">';
        echo '<div class="qp-media-label">' . e($label) . '</div>';
        echo '<div class="qp-media-frame"><iframe src="' . e($embedUrl) . '" title="' . e($label) . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>';
        echo '<div class="qp-media-open"><a href="' . e($url) . '" target="_blank" rel="noopener noreferrer">Open original <i class="bi bi-box-arrow-up-right"></i></a></div>';
        echo '</div>';
    }
    echo '</div>';
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
    $rawTopicIds = $_POST['topic_ids'] ?? $_GET['topic_ids'] ?? [];
    if (!is_array($rawTopicIds)) $rawTopicIds = [];
    $topicIds = array_values(array_unique(array_filter(array_map('intval', $rawTopicIds), fn($v) => $v > 0)));
    // Keep compatibility with links that still pass one topic through ?topic=ID.
    if (!$topicIds && $topicId > 0) $topicIds = [$topicId];
    $topicId = count($topicIds) === 1 ? $topicIds[0] : 0;
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
            if ($topicIds && !in_array((int)$q['topic_id'], $topicIds, true)) continue;
            $choices = rows($db, 'SELECT choice_label,choice_text,is_correct FROM question_choices WHERE question_id=? ORDER BY FIELD(choice_label,\'A\',\'B\',\'C\',\'D\',\'E\',\'F\')', 'i', [$qid]);
            $correct = '';
            foreach ($choices as $c) if ((int)$c['is_correct'] === 1) $correct = $c['choice_label'];
            $picked = strtoupper((string)($marks[$qid] ?? ''));
            $isCorrect = ($picked !== '' && $picked === $correct);
            if ($isCorrect) $score++;
            $q['choices'] = $choices;
            $q['media'] = question_media($db, (int)$q['id']);
            $q['solutions'] = question_solutions($db, (int)$q['id']);
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
        if ($topicIds) {
            $sql .= ' AND q.topic_id IN (' . implode(',', array_fill(0, count($topicIds), '?')) . ')';
            $types .= str_repeat('i', count($topicIds));
            foreach ($topicIds as $selectedTopicId) $args[] = $selectedTopicId;
        }
        $sql .= ' ORDER BY RAND() LIMIT ' . (int)$limit;
        $questions = rows($db, $sql, $types, $args);
        foreach ($questions as &$q) {
            $q['choices'] = rows($db, 'SELECT choice_label,choice_text FROM question_choices WHERE question_id=? ORDER BY FIELD(choice_label,\'A\',\'B\',\'C\',\'D\',\'E\',\'F\')', 'i', [$q['id']]);
            $q['media'] = question_media($db, (int)$q['id']);
        }
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

    .qp-filter-grid {
        display: grid;
        grid-template-columns: minmax(0, 5fr) minmax(0, 7fr);
        gap: 20px 24px;
        align-items: start
    }

    .qp-filter-field {
        min-width: 0
    }

    .qp-filter-count {
        grid-column: 1 / 2
    }

    .qp-filter-submit {
        grid-column: 1 / -1
    }

    .qp-topic-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        max-height: 220px;
        overflow-y: auto;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px;
        background: #fff;
        min-width: 0
    }

    .qp-topic-item {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 8px 10px;
        border: 1px solid #eef0f4;
        border-radius: 7px;
        cursor: pointer;
        min-width: 0
    }

    .qp-topic-item:hover {
        background: #fafaff;
        border-color: #c7d2fe
    }

    .qp-topic-item input {
        margin-top: 4px;
        accent-color: var(--qp-primary);
        flex-shrink: 0
    }

    .qp-topic-item span {
        overflow-wrap: anywhere
    }

    .qp-topic-item[hidden] {
        display: none
    }

    .qp-question-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        column-gap: 24px;
        row-gap: 22px;
        align-items: stretch;
        min-width: 0
    }

    .qp-question {
        padding: 22px;
        border: 2px solid #cbd5e1;
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 3px 10px rgba(15, 23, 42, .06);
        min-width: 0;
        overflow-wrap: anywhere
    }

    .qp-question+.qp-question {
        margin-top: 0
    }

    .qp-question .rich-content p {
        margin: 0 0 .75rem
    }

    .qp-question .rich-content p:last-child {
        margin-bottom: 0
    }

    .rich-content {
        overflow-wrap: anywhere;
        word-break: normal
    }

    .qp-score-card {
        margin-bottom: 30px
    }

    .qp-review-card {
        margin-top: 0
    }

    .qp-question:first-child {
        padding-top: 22px
    }

    .qp-question:last-child {
        padding-bottom: 22px
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
        margin-top: 16px;
        padding: 12px 14px;
        border-radius: 8px;
        border-left: 3px solid #64748b;
        background: #f8fafc;
        color: #374151;
        font-size: .9rem;
        line-height: 1.6
    }

    .qp-solutions {
        margin-top: 16px;
        padding: 15px;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        background: #eff6ff;
    }

    .qp-solutions h6 {
        font-weight: 750;
        color: #1d4ed8;
        margin-bottom: 10px;
    }

    .qp-solution-text {
        color: #1f2937;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .qp-solution-media {
        margin-top: 12px;
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

    /* Render code blocks created in the question editor clearly inside the practice exam. */
    .rich-content pre,
    .rich-content pre.ql-syntax,
    .rich-content .ql-code-block-container,
    .rich-content .ql-code-block {
        display: block;
        box-sizing: border-box;
        max-width: 100%;
        margin: 12px 0;
        padding: 14px 16px;
        overflow-x: auto;
        border: 1px solid #334155;
        border-radius: 8px;
        background: #0f172a;
        color: #e2e8f0;
        font-family: Consolas, Monaco, "Courier New", monospace;
        font-size: .88rem;
        font-weight: 400;
        line-height: 1.6;
        text-align: left;
        white-space: pre;
        overflow-wrap: normal;
        word-break: normal;
        tab-size: 4;
    }

    .rich-content .ql-code-block-container {
        white-space: normal;
    }

    .rich-content .ql-code-block {
        margin: 0;
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
    }

    .rich-content code,
    .rich-content p code,
    .rich-content li code {
        padding: 2px 5px;
        border-radius: 4px;
        background: #eef2f7;
        color: #be123c;
        font-family: Consolas, Monaco, "Courier New", monospace;
        font-size: .92em;
        font-weight: 400;
        overflow-wrap: anywhere;
    }

    .qp-question-text .rich-content,
    .qp-question-text.rich-content,
    .qp-option-text {
        min-width: 0;
    }

    .qp-media-list {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 14px;
        margin: 0 0 16px 46px;
        min-width: 0;
    }

    .qp-media-item {
        min-width: 0;
        overflow: hidden;
        border: 1px solid #dbe1ea;
        border-radius: 10px;
        background: #f8fafc;
    }

    .qp-media-label {
        padding: 8px 12px;
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
        border-bottom: 1px solid #e5e7eb;
        background: #f1f5f9;
    }

    .qp-media-frame {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9;
        background: #e5e7eb;
    }

    .qp-media-frame iframe {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    .qp-media-open {
        padding: 8px 12px;
        font-size: .8rem;
        background: #fff;
    }

    .qp-media-open a {
        text-decoration: none;
    }

    .qp-media-open a:hover {
        text-decoration: underline;
    }

    .qp-option-text {
        flex: 1 1 auto;
    }

    @media(max-width:900px) {
        .qp-filter-grid {
            grid-template-columns: minmax(0, 1fr)
        }

        .qp-filter-count,
        .qp-filter-submit {
            grid-column: 1 / -1
        }

        .qp-question-grid {
            grid-template-columns: minmax(0, 1fr);
            column-gap: 0
        }
    }

    @media(max-width:600px) {
        .qp-topic-list {
            grid-template-columns: minmax(0, 1fr)
        }
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

        .qp-question {
            padding: 18px;
            border-radius: 11px
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
                            <p>Choose a subject, select one or more topics, and set the number of questions.</p>
                        </div>
                    </div>
                    <div class="qp-body">
                        <form method="get" class="qp-filter-grid">
                            <div class="qp-filter-field"><label class="qp-label" for="subject">Subject</label><select class="qp-select" id="subject" name="subject">
                                    <option value="0">All subjects</option><?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>" <?= (int)$subjectId === (int)$s['id'] ? 'selected' : '' ?>><?= e($s['subject_code'] . ' — ' . $s['subject_name']) ?></option><?php endforeach; ?>
                                </select></div>
                            <div class="qp-filter-field"><label class="qp-label">Topics <span class="text-secondary fw-normal">(check any topics you want)</span></label>
                                <div class="qp-topic-list" id="topicList"><?php foreach ($topics as $t): ?><label class="qp-topic-item" data-subject="<?= (int)$t['subject_id'] ?>"><input type="checkbox" name="topic_ids[]" value="<?= (int)$t['id'] ?>" <?php if (in_array((int)$t['id'], $topicIds, true)) echo 'checked'; ?>><span><?= e($t['topic_name']) ?></span></label><?php endforeach; ?></div>
                                <div class="form-text">Leave all topics unchecked to include every topic for the selected subject.</div>
                            </div>
                            <div class="qp-filter-field qp-filter-count"><label class="qp-label" for="count">Questions</label><select class="qp-select" id="count" name="count"><?php foreach ([5, 10, 15, 20, 30, 50] as $n): ?><option value="<?= $n ?>" <?= $limit === $n ? 'selected' : '' ?>><?= $n ?> questions</option><?php endforeach; ?></select></div>
                            <div class="qp-filter-submit"><button class="btn qp-btn qp-btn-primary"><i class="bi bi-arrow-repeat me-1"></i>Load Practice Questions</button></div>
                        </form>
                    </div>
                </div>
                <?php if (!$questions): ?><div class="qp-card">
                        <div class="qp-empty"><i class="bi bi-journal-x fs-2 d-block mb-2"></i><strong>No active questions found.</strong>
                            <div class="mt-1">Try a different subject or topic, or add active questions to the question bank.</div>
                        </div>
                    </div>
                <?php else: ?>
                    <form method="post" id="practiceForm"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="action" value="grade"><input type="hidden" name="subject_id" value="<?= (int)$subjectId ?>"><input type="hidden" name="topic_id" value="<?= (int)$topicId ?>"><?php foreach ($topicIds as $selectedTopicId): ?><input type="hidden" name="topic_ids[]" value="<?= (int)$selectedTopicId ?>"><?php endforeach; ?><input type="hidden" name="question_count" value="<?= $limit ?>">
                        <div class="qp-card">
                            <div class="qp-head">
                                <div class="qp-icon"><i class="bi bi-patch-question"></i></div>
                                <div>
                                    <h5>Your Questions</h5>
                                    <p><?= count($questions) ?> question<?= count($questions) === 1 ? '' : 's' ?> · Select one answer for each question.</p>
                                </div>
                            </div>
                            <div class="qp-body">
                                <div class="qp-question-grid"><?php foreach ($questions as $i => $q): ?><section class="qp-question" id="question-<?= (int)$q['id'] ?>"><input type="hidden" name="question_ids[]" value="<?= (int)$q['id'] ?>">
                                            <div class="qp-meta"><span><?= e($q['subject_code']) ?></span><span><?= e($q['topic_name']) ?></span><span><?= e(ucfirst($q['difficulty'])) ?></span></div>
                                            <div class="qp-question-title">
                                                <div class="qp-number"><?= $i + 1 ?></div>
                                                <div class="qp-question-text rich-content"><?= safe_rich($q['question_text']) ?></div>
                                            </div><?php render_question_media($q['media'] ?? []); ?><?php foreach ($q['choices'] as $c): ?><label class="qp-option"><input type="radio" name="answers[<?= (int)$q['id'] ?>]" value="<?= e($c['choice_label']) ?>"><span class="qp-option-label"><?= e($c['choice_label']) ?>.</span><span class="qp-option-text rich-content"><?= safe_rich($c['choice_text']) ?></span></label><?php endforeach; ?>
                                        </section><?php endforeach; ?></div>
                                <div class="qp-actions"><span class="text-secondary small"><i class="bi bi-info-circle me-1"></i>Your score will appear after submission.</span><button type="submit" class="btn qp-btn qp-btn-primary"><i class="bi bi-check2-circle me-1"></i>Submit Answers</button></div>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <div class="qp-card qp-score-card">
                    <div class="qp-score">
                        <div class="qp-score-circle"><?= number_format(($score / max(1, $total)) * 100, 0) ?>%</div>
                        <div>
                            <h4 class="mb-1 fw-bold">Practice Results</h4>
                            <div class="text-secondary">You answered <strong><?= $score ?> of <?= $total ?></strong> questions correctly.</div>
                            <div class="text-secondary small mt-1"><?= ($total - $score) ?> incorrect or unanswered</div>
                        </div>
                    </div>
                </div>
                <div class="qp-card qp-review-card">
                    <div class="qp-head">
                        <div class="qp-icon"><i class="bi bi-clipboard-check"></i></div>
                        <div>
                            <h5>Review Answers</h5>
                            <p>Check the correct answers and explanations where available.</p>
                        </div>
                    </div>
                    <div class="qp-body">
                        <div class="qp-question-grid"><?php foreach ($results as $i => $q): ?><section class="qp-question">
                                    <div class="qp-question-title">
                                        <div class="qp-number"><?= $i + 1 ?></div>
                                        <div class="qp-question-text rich-content"><?= safe_rich($q['question_text']) ?></div>
                                    </div><?php render_question_media($q['media'] ?? []); ?><?php foreach ($q['choices'] as $c): $correctChoice = $c['choice_label'] === $q['correct'];
                                                                                                $pickedChoice = $c['choice_label'] === $q['picked']; ?><div class="qp-option" style="cursor:default;<?= $correctChoice ? 'border-color:#86efac;background:#f0fdf4;' : ($pickedChoice ? 'border-color:#fdba74;background:#fff7ed;' : '') ?>"><span class="qp-option-label"><?= e($c['choice_label']) ?>.</span><span class="qp-option-text rich-content"><?= safe_rich($c['choice_text']) ?><?php if ($correctChoice): ?> <strong class="text-success ms-1">Correct answer</strong><?php elseif ($pickedChoice): ?> <strong class="text-warning-emphasis ms-1">Your answer</strong><?php endif; ?></span></div><?php endforeach; ?><?php if (trim(strip_tags((string)$q['explanation'])) !== ''): ?><div class="qp-explanation"><span class="rich-content"><?= safe_rich($q['explanation']) ?></span></div><?php endif; ?><?php render_question_solutions($q['solutions'] ?? []); ?>
                                </section><?php endforeach; ?></div>
                        <div class="qp-actions"><a class="btn btn-outline-secondary qp-btn" href="question_practice.php"><i class="bi bi-arrow-left me-1"></i>Back to Setup</a><a class="btn qp-btn qp-btn-primary" href="question_practice.php?<?= e(http_build_query(['subject' => $subjectId, 'topic_ids' => $topicIds, 'count' => $limit])) ?>"><i class="bi bi-arrow-repeat me-1"></i>Try Another Set</a></div>
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
                topicList = document.getElementById('topicList');
            if (s && topicList) {
                function filterTopics() {
                    topicList.querySelectorAll('.qp-topic-item').forEach(item => {
                        const matches = s.value === '0' || item.dataset.subject === s.value;
                        if (!matches) {
                            const checkbox = item.querySelector('input[type="checkbox"]');
                            if (checkbox) checkbox.checked = false;
                        }
                        item.hidden = !matches;
                    });
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
            document.querySelectorAll('.rich-content').forEach(el => {
                if (window.renderMathInElement) {
                    try {
                        renderMathInElement(el, {
                            delimiters: [{
                                left: '$$',
                                right: '$$',
                                display: true
                            }, {
                                left: '$',
                                right: '$',
                                display: false
                            }],
                            throwOnError: false
                        });
                    } catch (e) {}
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