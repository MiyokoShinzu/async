<?php
include './globals/checks.php';
require_once '../src/connection.php';
$db = $mysqli ?? null;
if (!$db instanceof mysqli) {
    exit('Database connection is unavailable.');
}
$db->set_charset('utf8mb4');

if (empty($_SESSION['qmedia_csrf'])) $_SESSION['qmedia_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['qmedia_csrf'];
$msg = '';
$msgType = 'success';
$selectedQuestion = (int)($_GET['question_id'] ?? $_POST['question_id'] ?? 0);
$subjectFilter = (int)($_GET['subject_filter'] ?? $_POST['subject_filter'] ?? 0);
$topicFilter = (int)($_GET['topic_filter'] ?? $_POST['topic_filter'] ?? 0);

function qm_e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function qm_rows(mysqli $db, string $sql, string $types = '', array $args = []): array
{
    $stmt = $db->prepare($sql);
    if (!$stmt) throw new Exception($db->error);
    if ($types !== '') $stmt->bind_param($types, ...$args);
    if (!$stmt->execute()) throw new Exception($stmt->error);
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $result;
}
function qm_exec(mysqli $db, string $sql, string $types = '', array $args = []): void
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
function qm_normalize_url(string $type, string $url): ?string
{
    $url = trim($url);
    if (!filter_var($url, FILTER_VALIDATE_URL)) return null;
    $p = parse_url($url);
    if (!$p || !in_array(strtolower($p['scheme'] ?? ''), ['http', 'https'], true)) return null;
    $host = strtolower($p['host'] ?? '');

    if ($type === 'image') {
        if (!in_array($host, ['drive.google.com', 'www.drive.google.com'], true)) return null;
        if (preg_match('~/file/d/([A-Za-z0-9_-]+)~', $p['path'] ?? '', $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/view';
        }
        parse_str($p['query'] ?? '', $query);
        if (!empty($query['id']) && preg_match('/^[A-Za-z0-9_-]+$/', (string)$query['id'])) {
            return 'https://drive.google.com/file/d/' . $query['id'] . '/view';
        }
        return null;
    }

    if ($type === 'youtube') {
        $videoId = null;
        if ($host === 'youtu.be') {
            $videoId = trim(explode('/', trim($p['path'] ?? '', '/'))[0] ?? '');
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com'], true)) {
            parse_str($p['query'] ?? '', $query);
            if (str_starts_with($p['path'] ?? '', '/watch')) $videoId = $query['v'] ?? null;
            elseif (preg_match('~/(?:embed|shorts)/([A-Za-z0-9_-]+)~', $p['path'] ?? '', $m)) $videoId = $m[1];
        }
        if ($videoId && preg_match('/^[A-Za-z0-9_-]{11}$/', $videoId)) return 'https://www.youtube.com/watch?v=' . $videoId;
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) throw new Exception('Session token expired. Refresh this page and try again.');
        $action = $_POST['action'] ?? '';
        if ($action === 'add_media') {
            $selectedQuestion = (int)($_POST['question_id'] ?? 0);
            $type = (string)($_POST['media_type'] ?? '');
            $url = (string)($_POST['media_url'] ?? '');
            if ($selectedQuestion < 1) throw new Exception('Please select a question first.');
            if (!in_array($type, ['image', 'youtube'], true)) throw new Exception('Choose a valid media type.');
            $question = qm_rows($db, 'SELECT id FROM question_bank WHERE id = ?', 'i', [$selectedQuestion]);
            if (!$question) throw new Exception('The selected question was not found.');
            $normalized = qm_normalize_url($type, $url);
            if ($normalized === null) throw new Exception($type === 'image' ? 'Enter a valid Google Drive file link.' : 'Enter a valid YouTube video, Shorts, or youtu.be link.');
            $countRows = qm_rows($db, 'SELECT COUNT(*) AS total FROM question_media WHERE question_id = ?', 'i', [$selectedQuestion]);
            if ((int)$countRows[0]['total'] >= 10) throw new Exception('This question already has 10 media items. Remove one before adding another.');
            $orderRows = qm_rows($db, 'SELECT COALESCE(MAX(display_order), 0) + 1 AS next_order FROM question_media WHERE question_id = ?', 'i', [$selectedQuestion]);
            $order = (int)$orderRows[0]['next_order'];
            qm_exec($db, 'INSERT INTO question_media (question_id, media_type, media_url, display_order) VALUES (?, ?, ?, ?)', 'issi', [$selectedQuestion, $type, $normalized, $order]);
            $msg = 'Media added to the selected question.';
        } elseif ($action === 'delete_media') {
            $mediaId = (int)($_POST['media_id'] ?? 0);
            $selectedQuestion = (int)($_POST['question_id'] ?? 0);
            if ($mediaId < 1 || $selectedQuestion < 1) throw new Exception('Invalid media selection.');
            qm_exec($db, 'DELETE FROM question_media WHERE id = ? AND question_id = ?', 'ii', [$mediaId, $selectedQuestion]);
            $msg = 'Media item removed.';
        } else {
            throw new Exception('Unknown action.');
        }
    } catch (Throwable $ex) {
        $msg = $ex->getMessage();
        $msgType = 'danger';
    }
}

try {
    $subjects = qm_rows($db, 'SELECT id, subject_code, subject_name FROM subjects ORDER BY subject_name');
    $topicSql = 'SELECT id, subject_id, topic_name FROM subject_topics';
    $topicTypes = '';
    $topicArgs = [];
    if ($subjectFilter > 0) {
        $topicSql .= ' WHERE subject_id = ?';
        $topicTypes = 'i';
        $topicArgs[] = $subjectFilter;
    }
    $topicSql .= ' ORDER BY topic_name';
    $topics = qm_rows($db, $topicSql, $topicTypes, $topicArgs);

    $questionSql = "SELECT q.id, q.question_text, q.status, q.subject_id, q.topic_id, s.subject_code, s.subject_name, t.topic_name
        FROM question_bank q
        LEFT JOIN subjects s ON s.id = q.subject_id
        LEFT JOIN subject_topics t ON t.id = q.topic_id WHERE 1=1";
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
    $questionSql .= ' ORDER BY s.subject_name, t.topic_name, q.id DESC';
    $questions = qm_rows($db, $questionSql, $questionTypes, $questionArgs);
    $selected = [];
    foreach ($questions as $question) if ((int)$question['id'] === $selectedQuestion) {
        $selected = $question;
        break;
    }
    if (!$selected) $selectedQuestion = 0;
    $mediaItems = $selectedQuestion > 0
        ? qm_rows($db, 'SELECT id, media_type, media_url, display_order FROM question_media WHERE question_id = ? ORDER BY display_order, id', 'i', [$selectedQuestion])
        : [];
    $totalMedia = count($mediaItems);
} catch (Throwable $ex) {
    http_response_code(500);
    exit('Unable to load media management data: ' . qm_e($ex->getMessage()));
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

    .media-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 16px
    }

    .media-item {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        min-width: 0
    }

    .media-preview {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        aspect-ratio: 16/9;
        overflow: hidden
    }

    .media-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain
    }

    .media-preview iframe {
        width: 100%;
        height: 100%;
        border: 0
    }

    .media-item-info {
        padding: 13px
    }

    .media-item-title {
        font-size: .82rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 7px
    }

    .media-item-url {
        font-size: .78rem;
        color: #6b7280;
        word-break: break-all
    }

    .media-item-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-top: 12px
    }

    .media-badge {
        font-size: .72rem;
        border-radius: 999px;
        padding: 4px 8px;
        background: #eef2ff;
        color: #4338ca;
        font-weight: 700
    }

    .media-empty {
        padding: 32px 15px;
        text-align: center;
        color: #6b7280
    }

    .media-empty i {
        font-size: 2rem;
        display: block;
        margin-bottom: 8px;
        color: #9ca3af
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

        .media-list {
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
                    <h2><i class="bi bi-camera-reels me-2"></i>Question Media Management</h2>
                    <p>Attach Google Drive images and YouTube videos to questions without editing the question bank.</p>
                </div><a class="btn btn-outline-secondary" href="question_management.php"><i class="bi bi-arrow-left me-1"></i>Back to Questions</a>
            </div>
            <?php if ($msg !== ''): ?><div class="alert alert-<?= qm_e($msgType) ?> media-alert alert-dismissible fade show"><?= qm_e($msg) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
            <section class="media-card">
                <div class="media-card-head">
                    <h5><i class="bi bi-link-45deg me-2"></i>Add media to a question</h5>
                </div>
                <div class="media-card-body">
                    <form method="get" class="mb-4 p-3 border rounded-3 bg-light">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5"><label class="form-label" for="subjectFilter">Filter by subject</label><select class="form-select" id="subjectFilter" name="subject_filter">
                                    <option value="0">All subjects</option><?php foreach ($subjects as $subject): ?><option value="<?= (int)$subject['id'] ?>" <?= ($subjectFilter === (int)$subject['id'] ? 'selected' : '') ?>><?= qm_e(($subject['subject_code'] ? $subject['subject_code'] . ' — ' : '') . $subject['subject_name']) ?></option><?php endforeach; ?>
                                </select></div>
                            <div class="col-md-5"><label class="form-label" for="topicFilter">Filter by topic</label><select class="form-select" id="topicFilter" name="topic_filter">
                                    <option value="0">All topics</option><?php foreach ($topics as $topic): ?><option value="<?= (int)$topic['id'] ?>" <?= ($topicFilter === (int)$topic['id'] ? 'selected' : '') ?>><?= qm_e($topic['topic_name']) ?></option><?php endforeach; ?>
                                </select></div>
                            <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button><a class="btn btn-outline-secondary" href="question_media_management.php" title="Clear filters"><i class="bi bi-x-lg"></i></a></div>
                        </div>
                        <div class="form-text mt-2"><?= count($questions) ?> question(s) match the selected filters.</div>
                    </form>
                    <form method="get" class="mb-4"><input type="hidden" name="subject_filter" value="<?= $subjectFilter ?>"><input type="hidden" name="topic_filter" value="<?= $topicFilter ?>"><label class="form-label" for="questionPicker">Select question</label>
                        <div class="d-flex gap-2"><select class="form-select question-option" id="questionPicker" name="question_id" required>
                                <option value="">Choose a question…</option><?php foreach ($questions as $q): $label = trim(($q['subject_code'] ? $q['subject_code'] . ' — ' : '') . ($q['topic_name'] ? $q['topic_name'] . ' — ' : '') . strip_tags($q['question_text'])); ?><option value="<?= (int)$q['id'] ?>" <?= ((int)$q['id'] === $selectedQuestion ? 'selected' : '') ?>><?= qm_e('#' . $q['id'] . ' · ' . mb_strimwidth($label, 0, 115, '…')) ?></option><?php endforeach; ?>
                            </select><button class="btn btn-primary" type="submit">Select</button></div>
                    </form>
                    <?php if ($selected): ?>
                        <div class="alert alert-light border mb-4">
                            <div class="small text-muted mb-1"><?= qm_e(($selected['subject_code'] ?? '') . ' · ' . ($selected['topic_name'] ?? '')) ?></div><strong>Question #<?= (int)$selected['id'] ?></strong>
                            <div class="mt-1"><?= nl2br(qm_e($selected['question_text'])) ?></div>
                        </div>
                        <form method="post"><input type="hidden" name="csrf" value="<?= qm_e($csrf) ?>"><input type="hidden" name="action" value="add_media"><input type="hidden" name="question_id" value="<?= (int)$selectedQuestion ?>"><input type="hidden" name="subject_filter" value="<?= $subjectFilter ?>"><input type="hidden" name="topic_filter" value="<?= $topicFilter ?>">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3"><label class="form-label" for="mediaType">Media type</label><select class="form-select" name="media_type" id="mediaType" required>
                                        <option value="image">Google Drive image</option>
                                        <option value="youtube">YouTube video</option>
                                    </select></div>
                                <div class="col-md-7"><label class="form-label" for="mediaUrl">Share link</label><input class="form-control" type="url" name="media_url" id="mediaUrl" placeholder="Paste a Google Drive or YouTube URL" required>
                                    <div class="form-text" id="mediaHelp">Google Drive file must be shared as Anyone with the link — Viewer.</div>
                                </div>
                                <div class="col-md-2"><button class="btn btn-primary w-100" type="submit" <?= ($totalMedia >= 10 ? 'disabled' : '') ?>><i class="bi bi-plus-lg me-1"></i>Add</button></div>
                            </div>
                            <div class="small text-muted mt-3">Media items: <strong><?= $totalMedia ?> / 10</strong>. Images and videos can be mixed.</div>
                        </form>
                    <?php else: ?><div class="text-muted small">Choose a question above to manage its images and videos.</div><?php endif; ?>
                </div>
            </section>
            <?php if ($selected): ?><section class="media-card">
                    <div class="media-card-head d-flex justify-content-between align-items-center gap-2">
                        <h5><i class="bi bi-collection-play me-2"></i>Attached media</h5><span class="badge text-bg-secondary"><?= $totalMedia ?> / 10</span>
                    </div>
                    <div class="media-card-body">
                        <?php if (!$mediaItems): ?><div class="media-empty"><i class="bi bi-images"></i>No media attached to this question yet.</div><?php else: ?><div class="media-list">
                                <?php foreach ($mediaItems as $item): $previewId = '';
                                                                                                                                                            if ($item['media_type'] === 'youtube' && preg_match('/[?&]v=([A-Za-z0-9_-]{11})/', $item['media_url'], $mm)) $previewId = $mm[1];
                                                                                                                                                            $driveId = '';
                                                                                                                                                            if ($item['media_type'] === 'image' && preg_match('~/file/d/([A-Za-z0-9_-]+)~', $item['media_url'], $mm)) $driveId = $mm[1]; ?>
                                    <article class="media-item">
                                        <div class="media-preview"><?php if ($item['media_type'] === 'image' && $driveId): ?><iframe src="<?= qm_e('https://drive.google.com/file/d/' . rawurlencode($driveId) . '/preview') ?>" title="Google Drive image preview" loading="lazy" allow="autoplay" referrerpolicy="no-referrer"></iframe><?php elseif ($item['media_type'] === 'youtube' && $previewId): ?><iframe src="https://www.youtube-nocookie.com/embed/<?= qm_e($previewId) ?>" title="YouTube preview" loading="lazy" allowfullscreen></iframe><?php else: ?><span class="text-muted small">Preview unavailable</span><?php endif; ?></div>
                                        <div class="media-item-info">
                                            <div class="d-flex justify-content-between align-items-center gap-2 mb-2"><span class="media-badge"><?= ($item['media_type'] === 'image' ? 'Google Drive image' : 'YouTube video') ?></span><span class="small text-muted">Order <?= (int)$item['display_order'] ?></span></div>
                                            <div class="media-item-url"><?= qm_e($item['media_url']) ?></div>
                                            <div class="media-item-actions"><a class="small" href="<?= qm_e($item['media_url']) ?>" target="_blank" rel="noopener noreferrer">Open original <i class="bi bi-box-arrow-up-right"></i></a>
                                                <form method="post" onsubmit="return confirm('Remove this media item from the question?');"><input type="hidden" name="csrf" value="<?= qm_e($csrf) ?>"><input type="hidden" name="action" value="delete_media"><input type="hidden" name="question_id" value="<?= (int)$selectedQuestion ?>"><input type="hidden" name="subject_filter" value="<?= $subjectFilter ?>"><input type="hidden" name="topic_filter" value="<?= $topicFilter ?>"><input type="hidden" name="media_id" value="<?= (int)$item['id'] ?>"><button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i>Remove</button></form>
                                            </div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div><?php endif; ?>
                    </div>
                </section><?php endif; ?>
            <div class="small text-muted mb-4"><i class="bi bi-info-circle me-1"></i>Google Drive previews use the Drive preview player and require “Anyone with the link — Viewer” access. If the preview still does not load, check the sharing permission or open the original link. Some YouTube videos may block embedding; use “Open original” to view them on YouTube.</div>
        </div>
    </main>
    <script>
        const subjectFilter = document.getElementById('subjectFilter');
        const topicFilter = document.getElementById('topicFilter');
        if (subjectFilter && topicFilter) {
            subjectFilter.addEventListener('change', () => {
                topicFilter.value = '0';
            });
        }
        const typeSelect = document.getElementById('mediaType');
        const urlInput = document.getElementById('mediaUrl');
        const help = document.getElementById('mediaHelp');
        if (typeSelect && urlInput && help) {
            typeSelect.addEventListener('change', () => {
                if (typeSelect.value === 'youtube') {
                    urlInput.placeholder = 'https://www.youtube.com/watch?v=XXXXXXXXXXX';
                    help.textContent = 'Paste a YouTube video, Shorts, or youtu.be link.';
                } else {
                    urlInput.placeholder = 'https://drive.google.com/file/d/FILE_ID/view';
                    help.textContent = 'Google Drive file must be shared as Anyone with the link — Viewer.';
                }
            });
        }
    </script>
    <?php include 'globals/scripts.php'; ?>
</body>

</html>