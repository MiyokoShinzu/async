<?php
include './globals/checks.php';
require_once '../src/connection.php';
$db = $mysqli ?? null;
require_once __DIR__ . '/exam_common.php';
$id = (int)($_GET['id'] ?? 0);
$rows = ex_rows($db, 'SELECT * FROM exams WHERE id=?', 'i', [$id]);
if (!$rows) {
    http_response_code(404);
    exit('Exam not found.');
}
$exam = $rows[0];
if ($exam['status'] === 'draft') {
    $items = ex_rows($db, 'SELECT d.position_no,q.id,q.question_text,q.explanation FROM exam_draft_questions d JOIN question_bank q ON q.id=d.question_id WHERE d.exam_id=? ORDER BY d.position_no', 'i', [$id]);
} else {
    $items = ex_rows($db, 'SELECT position_no,q.id,q.question_text,q.explanation FROM exam_set_questions d JOIN question_bank q ON q.id=d.question_id WHERE d.exam_id=? AND d.variant=\'A\' ORDER BY d.position_no', 'i', [$id]);
}
ex_page_start('Exam Preview', true); ?>
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
    <div>
        <div class="text-secondary small">EXAM PREVIEW · <?= strtoupper(ex_e($exam['status'])) ?></div>
        <h2 class="fw-bold mb-1"><?= ex_e($exam['title']) ?></h2>
        <div class="text-secondary"><?= count($items) ?> items · <?= (int)$exam['time_limit_minutes'] ?> minutes</div>
    </div><a class="btn btn-outline-secondary" href="exam_manager.php?id=<?= $id ?>">Back to manager</a>
</div>
<section class="exam-card">
    <div class="exam-card-body">
        <h5 class="fw-bold">Instructions</h5>
        <div class="text-rich"><?= ex_rich((string)$exam['instructions']) ?></div>
        <div class="alert alert-warning mt-3 mb-0">Preview mode: answers are not saved and the timer is not running.</div>
    </div>
</section>
<?php foreach ($items as $i => $q): ?><section class="question-card">
        <div class="small text-secondary mb-2">QUESTION <?= $i + 1 ?> OF <?= count($items) ?></div>
        <div class="text-rich fw-semibold"><?= ex_rich((string)$q['question_text']) ?></div><?php ex_render_media($db, (int)$q['id']);
                                                                                            $choices = ex_rows($db, 'SELECT choice_label,choice_text FROM question_choices WHERE question_id=? ORDER BY FIELD(choice_label,\'A\',\'B\',\'C\',\'D\',\'E\',\'F\')', 'i', [(int)$q['id']]);
                                                                                            foreach ($choices as $c): ?><div class="choice-row"><strong><?= ex_e($c['choice_label']) ?>.</strong>
                <div class="text-rich"><?= ex_rich((string)$c['choice_text']) ?></div>
            </div><?php endforeach; ?>
    </section><?php endforeach; ?>
<?php ex_page_end(true); ?>