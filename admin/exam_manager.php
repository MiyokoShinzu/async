<?php
include './globals/checks.php';
require_once '../src/connection.php';
$db = $mysqli ?? null;
require_once __DIR__ . '/exam_common.php';
$msg = '';
$msgType = 'success';
$error = '';
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($examCsrf, (string)($_POST['csrf'] ?? ''))) throw new Exception('Session token expired. Refresh the page and try again.');
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'create_exam') {
            $title = trim((string)($_POST['title'] ?? ''));
            $instructions = trim((string)($_POST['instructions'] ?? ''));
            $minutes = max(1, min(600, (int)($_POST['time_limit_minutes'] ?? 60)));
            $count = max(2, min(500, (int)($_POST['question_count'] ?? 2)));
            $sid = max(0, (int)($_POST['subject_id'] ?? 0));
            $topicIds = array_values(array_unique(array_filter(array_map('intval', (array)($_POST['topic_ids'] ?? [])), fn($v) => $v > 0)));
            $topicIdsJson = json_encode($topicIds, JSON_THROW_ON_ERROR);
            $tid = count($topicIds) === 1 ? $topicIds[0] : 0;
            $instructions = trim((string)($_POST['instructions'] ?? ''));
            if ($title === '') throw new Exception('Enter an exam name.');
            if (trim(strip_tags($instructions)) === '' && !preg_match('/class=[\"\']ql-formula[\"\']/', $instructions)) throw new Exception('Enter exam instructions.');
            foreach ($topicIds as $topicId) {
                $validTopic = ex_rows($db, "SELECT id FROM subject_topics WHERE id=? AND status='active'", 'i', [$topicId]);
                if (!$validTopic) throw new Exception('One of the selected topics is unavailable.');
            }
            $sql = "SELECT q.id FROM question_bank q JOIN subjects s ON s.id=q.subject_id JOIN subject_topics t ON t.id=q.topic_id WHERE q.status='active' AND s.status='active' AND t.status='active'";
            $types = '';
            $args = [];
            if ($sid > 0) {
                $sql .= ' AND q.subject_id=?';
                $types .= 'i';
                $args[] = $sid;
            }
            if ($topicIds) {
                $sql .= ' AND q.topic_id IN (' . implode(',', array_fill(0, count($topicIds), '?')) . ')';
                $types .= str_repeat('i', count($topicIds));
                foreach ($topicIds as $topicId) $args[] = $topicId;
            }
            $pool = ex_rows($db, $sql, $types, $args);
            if (count($pool) < 2) throw new Exception('At least 2 active questions are required for an exam in this selection.');
            if ($count > count($pool)) throw new Exception('You requested ' . $count . ' questions, but only ' . count($pool) . ' active questions match these filters.');
            $code = ex_code($db);
            $db->begin_transaction();
            try {
                ex_exec($db, 'INSERT INTO exams(title,test_code,instructions,time_limit_minutes,question_count,subject_id,topic_id,topic_ids_json,status) VALUES(?,?,?,?,?,?,?,?,\'draft\')', 'sssiiiis', [$title, $code, $instructions, $minutes, $count, $sid, ($tid ?: null), $topicIdsJson]);
                $examId = (int)$db->insert_id;
                shuffle($pool);
                $pool = array_slice($pool, 0, $count);
                $pos = 1;
                foreach ($pool as $item) ex_exec($db, 'INSERT INTO exam_draft_questions(exam_id,position_no,question_id) VALUES(?,?,?)', 'iii', [$examId, $pos++, (int)$item['id']]);
                $db->commit();
                header('Location: exam_manager.php?id=' . $examId . '&created=1');
                exit;
            } catch (Throwable $e) {
                $db->rollback();
                throw $e;
            }
        } elseif ($action === 'replace_question') {
            $examId = (int)($_POST['exam_id'] ?? 0);
            $draftId = (int)($_POST['draft_id'] ?? 0);
            $newQid = (int)($_POST['new_question_id'] ?? 0);
            $er = ex_rows($db, "SELECT * FROM exams WHERE id=? AND status='draft'", 'i', [$examId]);
            if (!$er) throw new Exception('Only draft exams can be edited.');
            $exam = $er[0];
            $dr = ex_rows($db, 'SELECT id FROM exam_draft_questions WHERE id=? AND exam_id=?', 'ii', [$draftId, $examId]);
            if (!$dr) throw new Exception('Question row not found.');
            $sql = "SELECT q.id FROM question_bank q JOIN subjects s ON s.id=q.subject_id JOIN subject_topics t ON t.id=q.topic_id WHERE q.id=? AND q.status='active' AND s.status='active' AND t.status='active'";
            $types = 'i';
            $args = [$newQid];
            if ((int)$exam['subject_id'] > 0) {
                $sql .= ' AND q.subject_id=?';
                $types .= 'i';
                $args[] = (int)$exam['subject_id'];
            }
            $savedTopicIds = json_decode((string)($exam['topic_ids_json'] ?? ''), true);
            if (!is_array($savedTopicIds)) $savedTopicIds = ((int)$exam['topic_id'] > 0 ? [(int)$exam['topic_id']] : []);
            $savedTopicIds = array_values(array_filter(array_map('intval', $savedTopicIds), fn($v) => $v > 0));
            if ($savedTopicIds) {
                $sql .= ' AND q.topic_id IN (' . implode(',', array_fill(0, count($savedTopicIds), '?')) . ')';
                $types .= str_repeat('i', count($savedTopicIds));
                foreach ($savedTopicIds as $topicId) $args[] = $topicId;
            }
            if (!ex_rows($db, $sql, $types, $args)) throw new Exception('The replacement question is not active or does not match the exam filters.');
            if (ex_rows($db, 'SELECT id FROM exam_draft_questions WHERE exam_id=? AND question_id=? AND id<>?', 'iii', [$examId, $newQid, $draftId])) throw new Exception('That question is already in this exam. Choose a different question.');
            ex_exec($db, 'UPDATE exam_draft_questions SET question_id=? WHERE id=? AND exam_id=?', 'iii', [$newQid, $draftId, $examId]);
            header('Location: exam_manager.php?id=' . $examId . '&changed=1');
            exit;
        } elseif ($action === 'finalize_exam') {
            $examId = (int)($_POST['exam_id'] ?? 0);
            $er = ex_rows($db, "SELECT * FROM exams WHERE id=? AND status='draft'", 'i', [$examId]);
            if (!$er) throw new Exception('Exam is not a draft or could not be found.');
            $exam = $er[0];
            $draft = ex_rows($db, 'SELECT d.question_id FROM exam_draft_questions d JOIN question_bank q ON q.id=d.question_id WHERE d.exam_id=? AND q.status=\'active\' ORDER BY d.position_no', 'i', [$examId]);
            if (count($draft) < 2) throw new Exception('An exam must contain at least 2 active questions.');
            if (count($draft) !== (int)$exam['question_count']) throw new Exception('The draft question count does not match the exam settings. Recreate or adjust the exam first.');
            foreach ($draft as $d) {
                $choices = ex_rows($db, 'SELECT choice_label FROM question_choices WHERE question_id=? ORDER BY choice_label', 'i', [(int)$d['question_id']]);
                if (count($choices) < 2) throw new Exception('Question #' . $d['question_id'] . ' has fewer than two answer choices. Fix it in Question Management before finalizing.');
            }
            $db->begin_transaction();
            try {
                ex_exec($db, 'DELETE FROM exam_set_questions WHERE exam_id=?', 'i', [$examId]);
                foreach (['A', 'B', 'C'] as $variant) {
                    $ordered = $draft;
                    shuffle($ordered);
                    $pos = 1;
                    foreach ($ordered as $d) {
                        $qid = (int)$d['question_id'];
                        $choiceRows = ex_rows($db, 'SELECT choice_label FROM question_choices WHERE question_id=? ORDER BY choice_label', 'i', [$qid]);
                        $labels = array_column($choiceRows, 'choice_label');
                        shuffle($labels);
                        $json = json_encode(array_values($labels), JSON_THROW_ON_ERROR);
                        ex_exec($db, 'INSERT INTO exam_set_questions(exam_id,variant,position_no,question_id,choice_order_json) VALUES(?,?,?,?,?)', 'isiis', [$examId, $variant, $pos++, $qid, $json]);
                    }
                }
            } catch (Throwable $e) {
                $db->rollback();
                throw $e;
            }
            $db->commit();
            ex_exec($db, "UPDATE exams SET status='published' WHERE id=?", 'i', [$examId]);
            header('Location: exam_manager.php?id=' . $examId . '&finalized=1');
            exit;
        } elseif ($action === 'delete_exam') {
            $examId = (int)($_POST['exam_id'] ?? 0);
            if ($examId < 1) throw new Exception('Invalid exam selected.');
            $examRows = ex_rows($db, 'SELECT id,title FROM exams WHERE id=?', 'i', [$examId]);
            if (!$examRows) throw new Exception('The exam no longer exists or was already deleted.');
            // Remove dependent student answers and attempts before deleting exam records.
            // Use a transaction so a failure leaves the exam and its data intact.
            $db->begin_transaction();
            try {
                ex_exec($db, 'DELETE FROM exam_attempt_answers WHERE attempt_id IN (SELECT id FROM exam_attempts WHERE exam_id=?)', 'i', [$examId]);
                ex_exec($db, 'DELETE FROM exam_attempts WHERE exam_id=?', 'i', [$examId]);
                ex_exec($db, 'DELETE FROM exam_draft_questions WHERE exam_id=?', 'i', [$examId]);
                ex_exec($db, 'DELETE FROM exam_set_questions WHERE exam_id=?', 'i', [$examId]);
                ex_exec($db, 'DELETE FROM exams WHERE id=?', 'i', [$examId]);
                $db->commit();
            } catch (Throwable $deleteError) {
                $db->rollback();
                throw $deleteError;
            }
            header('Location: exam_manager.php?deleted=1');
            exit;
        } elseif ($action === 'close_exam') {
            $examId = (int)($_POST['exam_id'] ?? 0);
            ex_exec($db, "UPDATE exams SET status='closed' WHERE id=? AND status='published'", 'i', [$examId]);
            header('Location: exam_manager.php?closed=1');
            exit;
        } elseif ($action === 'reopen_exam') {
            $examId = (int)($_POST['exam_id'] ?? 0);
            ex_exec($db, "UPDATE exams SET status='published' WHERE id=? AND status='closed'", 'i', [$examId]);
            header('Location: exam_manager.php?reopened=1');
            exit;
        } else throw new Exception('Unknown action.');
    }
} catch (Throwable $e) {
    if (isset($db) && $db instanceof mysqli && $db->errno === 0) {
    }
    $error = $e->getMessage();
    $msgType = 'danger';
}

if (isset($_GET['created'])) $msg = 'Exam created as a draft. Review or replace questions, then preview and finalize it.';
if (isset($_GET['changed'])) $msg = 'Question replaced successfully.';
if (isset($_GET['finalized'])) $msg = 'Exam finalized. Sets A, B, and C are generated and the test code is active.';
if (isset($_GET['closed'])) $msg = 'Exam closed.';
if (isset($_GET['reopened'])) $msg = 'Exam reopened.';
if (isset($_GET['deleted'])) $msg = 'Exam and all associated attempts, answers, and generated question sets were permanently deleted.';
$selectedId = (int)($_GET['id'] ?? 0);
$selected = null;
$draftQuestions = [];
$replacementPool = [];
try {
    $subjects = ex_rows($db, "SELECT id,subject_code,subject_name FROM subjects WHERE status='active' ORDER BY subject_name");
    $topics = ex_rows($db, "SELECT id,subject_id,topic_name FROM subject_topics WHERE status='active' ORDER BY topic_name");
    $createTopicIds = array_values(array_filter(array_map('intval', (array)($_POST['topic_ids'] ?? [])), fn($v) => $v > 0));
    if ($selectedId > 0) {
        $rr = ex_rows($db, 'SELECT e.*,s.subject_name,t.topic_name FROM exams e LEFT JOIN subjects s ON s.id=e.subject_id LEFT JOIN subject_topics t ON t.id=e.topic_id WHERE e.id=?', 'i', [$selectedId]);
        $selected = $rr[0] ?? null;
        if ($selected) {
            $draftQuestions = ex_rows($db, 'SELECT d.id AS draft_id,d.position_no,q.id AS question_id,q.question_text,q.difficulty FROM exam_draft_questions d JOIN question_bank q ON q.id=d.question_id WHERE d.exam_id=? ORDER BY d.position_no', 'i', [$selectedId]);
            if ($selected['status'] === 'draft') {
                $sql = "SELECT q.id,q.question_text,t.topic_name FROM question_bank q LEFT JOIN subject_topics t ON t.id=q.topic_id JOIN subjects s ON s.id=q.subject_id WHERE q.status='active' AND s.status='active'";
                $types = '';
                $args = [];
                if ((int)$selected['subject_id'] > 0) {
                    $sql .= ' AND q.subject_id=?';
                    $types .= 'i';
                    $args[] = (int)$selected['subject_id'];
                }
                $savedTopicIds = json_decode((string)($selected['topic_ids_json'] ?? ''), true);
                if (!is_array($savedTopicIds)) $savedTopicIds = ((int)$selected['topic_id'] > 0 ? [(int)$selected['topic_id']] : []);
                $savedTopicIds = array_values(array_filter(array_map('intval', $savedTopicIds), fn($v) => $v > 0));
                if ($savedTopicIds) {
                    $sql .= ' AND q.topic_id IN (' . implode(',', array_fill(0, count($savedTopicIds), '?')) . ')';
                    $types .= str_repeat('i', count($savedTopicIds));
                    foreach ($savedTopicIds as $topicId) $args[] = $topicId;
                }
                $sql .= ' ORDER BY q.id DESC';
                $replacementPool = ex_rows($db, $sql, $types, $args);
            }
        }
    }
    $allExams = ex_rows($db, 'SELECT e.*, (SELECT COUNT(*) FROM exam_attempts a WHERE a.exam_id=e.id) AS attempts FROM exams e ORDER BY e.created_at DESC LIMIT 100');
} catch (Throwable $e) {
    $error = $error ?: $e->getMessage();
}
ex_page_start('Exam Manager', true);
?>
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1"><i class="bi bi-journal-check me-2"></i>Exam Manager</h2>
        <p class="text-secondary mb-0">Build, review, preview, and publish code-accessible exams.</p>
    </div><a href="question_management.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Question Bank</a>
</div>
<?php if ($msg || $error): ?><div class="alert alert-<?= ex_e($error ? 'danger' : $msgType) ?> alert-dismissible fade show"><?= ex_e($error ?: $msg) ?><button class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<section class="exam-card">
    <div class="exam-card-head">
        <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Create a new exam</h5>
    </div>
    <div class="exam-card-body">
        <form method="post"><input type="hidden" name="csrf" value="<?= ex_e($examCsrf) ?>"><input type="hidden" name="action" value="create_exam">
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Exam name</label><input class="form-control" name="title" required maxlength="200" placeholder="e.g. Signals and Systems Midterm"></div>
                <div class="col-md-4"><label class="form-label">Time limit (minutes)</label><input class="form-control" type="number" name="time_limit_minutes" min="1" max="600" value="60" required></div>
                <div class="col-12"><label class="form-label">Instructions shown to students</label>
                    <div class="editor-toolbar"><button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="bold"><strong>B</strong></button><button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="italic"><em>I</em></button><button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="underline"><u>U</u></button><button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="insertUnorderedList"><i class="bi bi-list-ul"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" data-cmd="insertOrderedList"><i class="bi bi-list-ol"></i></button><button type="button" class="btn btn-sm btn-outline-secondary" id="insertInstructionMath">ƒx Insert equation</button></div>
                    <div id="instructionsEditor" class="exam-instructions-editor" contenteditable="true" role="textbox" aria-multiline="true">
                        <p>Read all questions carefully. Select one answer per question.</p>
                    </div><textarea class="d-none" name="instructions" id="instructionsInput"></textarea>
                    <div class="form-text">Supports bold, italics, lists, and LaTeX formulas. Question formatting and formulas from the question bank are rendered in preview and the student exam.</div>
                </div>
                <div class="col-md-4"><label class="form-label">Subject filter</label><select class="form-select" name="subject_id" id="subjectFilter">
                        <option value="0">All active subjects</option><?php foreach ($subjects as $s): ?><option value="<?= (int)$s['id'] ?>"><?= ex_e(($s['subject_code'] ? $s['subject_code'] . ' — ' : '') . $s['subject_name']) ?></option><?php endforeach; ?>
                    </select></div>
                <div class="col-md-8"><label class="form-label">Select topics <span class="text-secondary fw-normal">(leave all unchecked to include all topics for the selected subject)</span></label>
                    <div class="d-flex gap-2 mb-2"><button type="button" class="btn btn-sm btn-outline-secondary" id="selectVisibleTopics">Select visible</button><button type="button" class="btn btn-sm btn-outline-secondary" id="clearTopics">Clear selection</button><span class="small text-secondary align-self-center" id="topicCount"></span></div>
                    <div class="topic-checklist" id="topicChecklist"><?php foreach ($topics as $t): ?><label class="topic-check" data-subject="<?= (int)$t['subject_id'] ?>"><input class="form-check-input topic-checkbox" type="checkbox" name="topic_ids[]" value="<?= (int)$t['id'] ?>" <?= in_array((int)$t['id'], $createTopicIds, true) ? 'checked' : '' ?>><span><?= ex_e($t['topic_name']) ?></span></label><?php endforeach; ?></div>
                </div>
                <div class="col-md-4"><label class="form-label">Number of questions</label><input class="form-control" type="number" name="question_count" min="2" max="500" value="20" required>
                    <div class="form-text">Minimum is 2. The selected subject and topics must contain enough active questions.</div>
                </div>
                <div class="col-12"><button class="btn btn-primary"><i class="bi bi-magic me-1"></i>Create draft and choose questions</button></div>
            </div>
        </form>
    </div>
</section>
<?php if ($selected): ?>
    <section class="exam-card">
        <div class="exam-card-head d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h5 class="mb-1 fw-bold"><?= ex_e($selected['title']) ?></h5>
                <div class="small text-secondary">Code: <strong class="text-dark"><?= ex_e($selected['test_code']) ?></strong> · <?= (int)$selected['time_limit_minutes'] ?> minutes · <?= count($draftQuestions) ?> draft questions</div>
            </div><span class="badge text-bg-<?= $selected['status'] === 'published' ? 'success' : ($selected['status'] === 'closed' ? 'secondary' : 'warning') ?>"><?= strtoupper(ex_e($selected['status'])) ?></span>
        </div>
        <div class="exam-card-body">
            <div class="d-flex gap-2 flex-wrap mb-3"><?php if ($selected['status'] === 'draft'): ?><a class="btn btn-outline-primary" target="_blank" href="exam_preview.php?id=<?= (int)$selected['id'] ?>"><i class="bi bi-eye me-1"></i>Preview exam</a>
                    <form method="post" onsubmit="return confirm('Finalize this exam and generate shuffled Sets A, B, and C? You will no longer be able to edit its questions.');"><input type="hidden" name="csrf" value="<?= ex_e($examCsrf) ?>"><input type="hidden" name="action" value="finalize_exam"><input type="hidden" name="exam_id" value="<?= (int)$selected['id'] ?>"><button class="btn btn-success" <?= count($draftQuestions) < 2 ? 'disabled' : '' ?>><i class="bi bi-check2-circle me-1"></i>Finalize & Generate A/B/C</button></form><?php else: ?><a class="btn btn-outline-primary" target="_blank" href="exam_preview.php?id=<?= (int)$selected['id'] ?>"><i class="bi bi-eye me-1"></i>View exam</a><a class="btn btn-outline-secondary" href="exam_monitor.php?exam_id=<?= (int)$selected['id'] ?>"><i class="bi bi-activity me-1"></i>Monitor students</a><?php if ($selected['status'] === 'published'): ?><form method="post"><input type="hidden" name="csrf" value="<?= ex_e($examCsrf) ?>"><input type="hidden" name="action" value="close_exam"><input type="hidden" name="exam_id" value="<?= (int)$selected['id'] ?>"><button class="btn btn-outline-danger">Close exam</button></form><?php else: ?><form method="post"><input type="hidden" name="csrf" value="<?= ex_e($examCsrf) ?>"><input type="hidden" name="action" value="reopen_exam"><input type="hidden" name="exam_id" value="<?= (int)$selected['id'] ?>"><button class="btn btn-outline-success">Reopen exam</button></form><?php endif; ?><button class="btn btn-outline-dark" type="button" onclick="navigator.clipboard.writeText('<?= ex_e($selected['test_code']) ?>')"><i class="bi bi-copy me-1"></i>Copy test code</button><?php endif; ?>
            </div>
            <?php if ($selected['status'] === 'draft'): ?><div class="alert alert-info small">Review each selected question below. Use the dropdown to replace an unwanted question before finalizing. Finalized exams lock the question set and generate three shuffled variants using the same questions and shuffled answer choices.</div><?php endif; ?>
            <div class="d-grid gap-3"><?php foreach ($draftQuestions as $dq): ?><article class="border rounded-3 p-3">
                        <div class="d-flex justify-content-between gap-2"><strong>Question <?= $dq['position_no'] ?></strong><span class="badge text-bg-light">ID #<?= $dq['question_id'] ?></span></div>
                        <div class="mt-2 text-rich"><?= ex_rich((string)$dq['question_text']) ?></div><?php if ($selected['status'] === 'draft'): ?><form method="post" class="row g-2 align-items-end mt-2"><input type="hidden" name="csrf" value="<?= ex_e($examCsrf) ?>"><input type="hidden" name="action" value="replace_question"><input type="hidden" name="exam_id" value="<?= (int)$selected['id'] ?>"><input type="hidden" name="draft_id" value="<?= (int)$dq['draft_id'] ?>">
                                <div class="col-md-10"><label class="form-label small">Replace with another question</label><select class="form-select" name="new_question_id" required>
                                        <option value="">Choose replacement…</option><?php foreach ($replacementPool as $rp): if ((int)$rp['id'] === (int)$dq['question_id']) continue;
                                                                                                            $already = false;
                                                                                                            foreach ($draftQuestions as $x) if ((int)$x['question_id'] === (int)$rp['id']) $already = true;
                                                                                                            if ($already) continue; ?><option value="<?= (int)$rp['id'] ?>">#<?= (int)$rp['id'] ?> · <?= ex_e(mb_strimwidth(strip_tags($rp['question_text']), 0, 130, '…')) ?></option><?php endforeach; ?>
                                    </select></div>
                                <div class="col-md-2"><button class="btn btn-outline-primary w-100">Replace</button></div>
                            </form><?php endif; ?>
                    </article><?php endforeach; ?></div>
            <?php if ($selected['status'] !== 'draft'): ?><div class="mt-4"><strong>Student access</strong>
                    <div class="mt-2">Share the test URL and code with students: <code><?= ex_e((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/exam_take.php') ?></code><br>Test code: <span class="fs-5 fw-bold"><?= ex_e($selected['test_code']) ?></span></div>
                </div><?php endif; ?>
        </div>
    </section>
<?php endif; ?>
<section class="exam-card">
    <div class="exam-card-head">
        <h5 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>Existing exams</h5>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Exam</th>
                    <th>Test code</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Attempts</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php if (!$allExams): ?><tr>
                        <td colspan="6" class="text-center text-secondary py-4">No exams created yet.</td>
                    </tr><?php endif; ?><?php foreach ($allExams as $ex): ?><tr>
                        <td><strong><?= ex_e($ex['title']) ?></strong>
                            <div class="small text-secondary"><?= (int)$ex['question_count'] ?> items</div>
                        </td>
                        <td><code><?= ex_e($ex['test_code']) ?></code></td>
                        <td><?= (int)$ex['time_limit_minutes'] ?> min</td>
                        <td><span class="badge text-bg-<?= $ex['status'] === 'published' ? 'success' : ($ex['status'] === 'closed' ? 'secondary' : 'warning') ?>"><?= ex_e($ex['status']) ?></span></td>
                        <td><?= (int)$ex['attempts'] ?></td>
                        <td class="text-end">
                            <div class="d-inline-flex flex-wrap justify-content-end gap-1"><a class="btn btn-sm btn-outline-primary" href="exam_manager.php?id=<?= (int)$ex['id'] ?>">Manage</a> <a class="btn btn-sm btn-outline-secondary" href="exam_monitor.php?exam_id=<?= (int)$ex['id'] ?>">Monitor</a>
                                <form method="post" class="d-inline" onsubmit="return confirm('PERMANENTLY DELETE this exam? This also deletes its generated Sets A/B/C, all student attempts, saved answers, and results. This cannot be undone.');"><input type="hidden" name="csrf" value="<?= ex_e($examCsrf) ?>"><input type="hidden" name="action" value="delete_exam"><input type="hidden" name="exam_id" value="<?= (int)$ex['id'] ?>"><button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button></form>
                            </div>
                        </td>
                    </tr><?php endforeach; ?></tbody>
        </table>
    </div>
</section>
<script>
    (function() {
        const sf = document.getElementById('subjectFilter'),
            checklist = document.getElementById('topicChecklist'),
            count = document.getElementById('topicCount');

        function updateTopics() {
            if (!checklist) return;
            let visible = 0,
                selected = 0;
            checklist.querySelectorAll('.topic-check').forEach(label => {
                const match = !sf || sf.value === '0' || label.dataset.subject === sf.value;
                label.hidden = !match;
                const cb = label.querySelector('input');
                if (!match) cb.checked = false;
                if (match) visible++;
                if (match && cb.checked) selected++;
            });
            if (count) count.textContent = selected + ' selected · ' + visible + ' visible';
        }
        if (sf) sf.addEventListener('change', updateTopics);
        checklist?.addEventListener('change', updateTopics);
        document.getElementById('selectVisibleTopics')?.addEventListener('click', () => {
            checklist.querySelectorAll('.topic-check:not([hidden]) input').forEach(cb => cb.checked = true);
            updateTopics();
        });
        document.getElementById('clearTopics')?.addEventListener('click', () => {
            checklist.querySelectorAll('input').forEach(cb => cb.checked = false);
            updateTopics();
        });
        updateTopics();
        const editor = document.getElementById('instructionsEditor'),
            hidden = document.getElementById('instructionsInput');
        document.querySelectorAll('[data-cmd]').forEach(btn => btn.addEventListener('click', () => {
            editor.focus();
            document.execCommand(btn.dataset.cmd, false, null);
        }));
        document.getElementById('insertInstructionMath')?.addEventListener('click', () => {
            const latex = prompt('Enter LaTeX (example: \\frac{a}{b} or \\int_0^1 x^2 dx):');
            if (latex === null || !latex.trim()) return;
            const span = document.createElement('span');
            span.className = 'ql-formula';
            span.setAttribute('data-value', latex.trim());
            span.textContent = latex.trim();
            const sel = window.getSelection();
            if (sel && sel.rangeCount && editor.contains(sel.anchorNode)) {
                const range = sel.getRangeAt(0);
                range.deleteContents();
                range.insertNode(span);
                range.setStartAfter(span);
                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
            } else editor.appendChild(span);
            editor.focus();
        });
        const form = editor?.closest('form');
        if (form) form.addEventListener('submit', function(e) {
            hidden.value = editor.innerHTML.trim();
            if (editor.innerText.trim() === '' && !editor.querySelector('.ql-formula')) {
                e.preventDefault();
                alert('Enter the exam instructions before creating the exam.');
                editor.focus();
            }
        });
    })();
</script>
<?php ex_page_end(true); ?>