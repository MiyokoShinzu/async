<?php
/* =========================================================
   LECTURE MONITOR - ETS Async Learning Portal
   Uses the existing lectures, accounts, lecture_progress,
   and lecture_submissions tables referenced by lectures.php.
   ========================================================= */

include './globals/checks.php';
require_once '../src/connection.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function lm_h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$lectureId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$lectureId || $lectureId < 1) {
    http_response_code(400);
    $pageError = 'A valid lecture ID is required. Open this page from the Monitor button on the Lectures page.';
    $lecture = null;
} else {
    $pageError = '';
    $lectureStmt = $mysqli->prepare(
        'SELECT id, title, youtube_url, department, year_level, section, description,
                start_date, due_date, status, created_at
         FROM lectures WHERE id = ? LIMIT 1'
    );
    if (!$lectureStmt) {
        $pageError = 'Unable to prepare the lecture query. Please check the database connection and lectures table.';
        $lecture = null;
    } else {
        $lectureStmt->bind_param('i', $lectureId);
        $lectureStmt->execute();
        $lecture = $lectureStmt->get_result()->fetch_assoc();
        $lectureStmt->close();
        if (!$lecture) {
            http_response_code(404);
            $pageError = 'The requested lecture was not found.';
        }
    }
}

$search = trim((string)($_GET['search'] ?? ''));
$progressFilter = trim((string)($_GET['progress'] ?? ''));
$submissionFilter = trim((string)($_GET['submission'] ?? ''));
$sectionFilter = trim((string)($_GET['year_section'] ?? ''));
$allowedProgress = ['', 'not_started', 'in_progress', 'completed'];
$allowedSubmission = ['', 'submitted', 'not_submitted'];
if (!in_array($progressFilter, $allowedProgress, true)) $progressFilter = '';
if (!in_array($submissionFilter, $allowedSubmission, true)) $submissionFilter = '';

$students = [];
$loadError = '';
$allRows = [];
$summary = ['assigned' => 0, 'started' => 0, 'completed' => 0, 'submitted' => 0];
$yearSections = [];

if ($lecture) {
    // Load students by department first, then match year/section in PHP.
    // This avoids SQL CONCAT/LIKE filtering differences between database setups.
    $targetSection = trim((string)($lecture['section'] ?? ''));
    $targetYear = trim((string)($lecture['year_level'] ?? ''));
    $studentSql = "SELECT id, first_name, middle_initial, last_name, extension_name,
                          student_id, email, department, year_section
                   FROM accounts
                   WHERE access = 'student' AND department = ?
                   ORDER BY last_name, first_name";

    $allStudentStmt = $mysqli->prepare($studentSql);
    if (!$allStudentStmt) {
        $loadError = 'Unable to prepare the student query: ' . $mysqli->error;
    } else {
        $department = (string)($lecture['department'] ?? '');
        $allStudentStmt->bind_param('s', $department);
        if (!$allStudentStmt->execute()) {
            $loadError = 'Unable to load assigned students: ' . $allStudentStmt->error;
        } else {
            $res = $allStudentStmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $storedYearSection = trim((string)($row['year_section'] ?? ''));
                $normalizedStored = strtolower(preg_replace('/\s+/', '', $storedYearSection));
                $expectedPrefix = strtolower(preg_replace('/\s+/', '', $targetYear . '-'));

                // Example account values: "1-A", "1 - A". If the lecture has
                // a section, require an exact year-section match.
                if ($targetSection !== '') {
                    $expectedSection = strtolower(preg_replace('/\s+/', '', $targetYear . '-' . $targetSection));
                    if ($normalizedStored !== $expectedSection) {
                        continue;
                    }
                } else {
                    if (strpos($normalizedStored, $expectedPrefix) !== 0) {
                        continue;
                    }
                }

                $allRows[(int)$row['id']] = $row;
                $yearSections[$storedYearSection] = true;
            }
        }
        $allStudentStmt->close();
    }

    // Connect progress to accounts using accounts.student_id.
    // lecture_progress.student_id is VARCHAR(50), matching the public student ID.
    // The fallback to accounts.id supports any older records that stored numeric account IDs.
    if ($loadError === '') {
        $progressByStudent = [];
        $pStmt = $mysqli->prepare(
            "SELECT a.id AS account_id,
                    MAX(CASE WHEN lp.status = 'completed' THEN 3
                             WHEN lp.status = 'in_progress' THEN 2
                             ELSE 1 END) AS progress_rank
             FROM lecture_progress lp
             INNER JOIN accounts a
               ON a.student_id = lp.student_id
               OR CAST(a.id AS CHAR) = lp.student_id
             WHERE lp.lecture_id = ?
               AND a.access = 'student'
             GROUP BY a.id"
        );
        if ($pStmt) {
            $pStmt->bind_param('i', $lectureId);
            if ($pStmt->execute()) {
                $pRes = $pStmt->get_result();
                while ($p = $pRes->fetch_assoc()) {
                    $progressByStudent[(int)$p['account_id']] = (int)$p['progress_rank'];
                }
            } else {
                $loadError = 'Unable to read lecture_progress: ' . $pStmt->error;
            }
            $pStmt->close();
        } else {
            $loadError = 'Unable to prepare lecture progress query: ' . $mysqli->error;
        }

        $submissionByStudent = [];
        if ($loadError === '') {
            $sStmt = $mysqli->prepare(
                "SELECT a.id AS account_id,
                        MAX(CASE WHEN ls.status = 'submitted' THEN 1 ELSE 0 END) AS is_submitted
                 FROM lecture_submissions ls
                 INNER JOIN accounts a
                   ON a.student_id = ls.student_id
                   OR CAST(a.id AS CHAR) = ls.student_id
                 WHERE ls.lecture_id = ?
                   AND a.access = 'student'
                 GROUP BY a.id"
            );
            if ($sStmt) {
                $sStmt->bind_param('i', $lectureId);
                if ($sStmt->execute()) {
                    $sRes = $sStmt->get_result();
                    while ($s = $sRes->fetch_assoc()) {
                        $submissionByStudent[(int)$s['account_id']] = (int)$s['is_submitted'];
                    }
                } else {
                    $loadError = 'Unable to read lecture_submissions: ' . $sStmt->error;
                }
                $sStmt->close();
            } else {
                $loadError = 'Unable to prepare submission query: ' . $mysqli->error;
            }
        }

        if ($loadError === '') {
            foreach ($allRows as $id => $student) {
                $rank = $progressByStudent[(int)$id] ?? 0;
                $student['progress_status'] = $rank === 3 ? 'completed' : ($rank === 2 ? 'in_progress' : 'not_started');
                $student['is_submitted'] = $submissionByStudent[(int)$id] ?? 0;
                $name = trim(($student['first_name'] ?? '') . ' ' . (!empty($student['middle_initial']) ? $student['middle_initial'] . '. ' : '') . ($student['last_name'] ?? '') . (!empty($student['extension_name']) ? ' ' . $student['extension_name'] : ''));
                $student['full_name'] = $name !== '' ? $name : 'Unnamed student';
                $summary['assigned']++;
                if ($student['progress_status'] !== 'not_started') $summary['started']++;
                if ($student['progress_status'] === 'completed') $summary['completed']++;
                if ($student['is_submitted']) $summary['submitted']++;

                $haystack = strtolower(implode(' ', [
                    $student['full_name'],
                    $student['student_id'] ?? '',
                    $student['email'] ?? '',
                    $student['department'] ?? '',
                    $student['year_section'] ?? ''
                ]));
                if ($search !== '' && strpos($haystack, strtolower($search)) === false) continue;
                if ($progressFilter !== '' && $student['progress_status'] !== $progressFilter) continue;
                if ($submissionFilter === 'submitted' && !$student['is_submitted']) continue;
                if ($submissionFilter === 'not_submitted' && $student['is_submitted']) continue;
                if ($sectionFilter !== '' && ($student['year_section'] ?? '') !== $sectionFilter) continue;
                $students[] = $student;
            }
        }
    }
}

$completionRate = $summary['assigned'] > 0 ? round(($summary['completed'] / $summary['assigned']) * 100) : 0;
$baseParams = ['id' => (int)($lecture['id'] ?? 0)];
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'globals/head.php'; ?>
<style>
    .monitor-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 22px
    }

    .monitor-header h2 {
        font-weight: 750;
        color: #1f2937;
        margin: 0
    }

    .monitor-header p {
        color: #6b7280;
        margin: 6px 0 0
    }

    .monitor-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 13px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        overflow: hidden
    }

    .monitor-stat {
        padding: 18px
    }

    .monitor-stat-label {
        font-size: .8rem;
        color: #6b7280
    }

    .monitor-stat-value {
        font-size: 1.65rem;
        font-weight: 750;
        color: #111827;
        margin-top: 5px
    }

    .monitor-stat-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #4f46e5;
        font-size: 1.1rem
    }

    .monitor-filter {
        padding: 18px;
        margin-bottom: 20px
    }

    .monitor-filter label {
        font-size: .8rem;
        font-weight: 650;
        color: #374151;
        margin-bottom: 6px
    }

    .monitor-filter .form-control,
    .monitor-filter .form-select {
        min-height: 42px;
        border-radius: 8px
    }

    .monitor-table {
        min-width: 950px;
        margin: 0
    }

    .monitor-table th {
        background: #f8fafc;
        color: #6b7280;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        white-space: nowrap
    }

    .monitor-table td,
    .monitor-table th {
        padding: 13px 14px;
        vertical-align: middle
    }

    .monitor-table td {
        font-size: .84rem
    }

    .student-name {
        font-weight: 700;
        color: #1f2937
    }

    .muted-small {
        font-size: .74rem;
        color: #9ca3af;
        margin-top: 3px
    }

    .status-pill {
        display: inline-block;
        border-radius: 20px;
        padding: 5px 9px;
        font-size: .72rem;
        font-weight: 700;
        white-space: nowrap
    }

    .pill-completed {
        background: #dcfce7;
        color: #166534
    }

    .pill-progress {
        background: #dbeafe;
        color: #1d4ed8
    }

    .pill-notstarted {
        background: #f3f4f6;
        color: #4b5563
    }

    .pill-submitted {
        background: #dcfce7;
        color: #166534
    }

    .pill-pending {
        background: #fef3c7;
        color: #92400e
    }

    .monitor-empty {
        padding: 48px 18px;
        text-align: center;
        color: #6b7280
    }

    .monitor-empty i {
        font-size: 2rem;
        color: #818cf8
    }

    .lecture-info {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb
    }

    .lecture-info h4 {
        font-size: 1rem;
        font-weight: 750;
        margin: 0 0 8px;
        color: #111827
    }

    .lecture-tags {
        display: flex;
        gap: 7px;
        flex-wrap: wrap
    }

    .lecture-tag {
        background: #f5f7ff;
        color: #4f46e5;
        border: 1px solid #e0e7ff;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: .72rem;
        font-weight: 650
    }

    @media(max-width:768px) {
        .monitor-header {
            flex-direction: column
        }

        .monitor-header .btn {
            width: 100%
        }
    }
</style>

<body>
    <?php include 'globals/sidebar.php'; ?>
    <?php include 'globals/topbar.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <div class="monitor-header">
                <div>
                    <h2><i class="bi bi-bar-chart-line me-2 text-primary"></i>Lecture Monitor</h2>
                    <p>Monitor assigned students, lecture progress, and submissions.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap"><a class="btn btn-outline-secondary" href="lectures.php"><i class="bi bi-arrow-left me-1"></i> Back to Lectures</a><?php if ($lecture): ?><a class="btn btn-primary" href="<?= lm_h($lecture['youtube_url'] ?? '#') ?>" target="_blank" rel="noopener"><i class="bi bi-play-circle me-1"></i> Open Lecture</a><?php endif; ?></div>
            </div>

            <?php if ($pageError !== ''): ?>
                <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= lm_h($pageError) ?></div>
            <?php elseif ($lecture): ?>
                <div class="monitor-card mb-4">
                    <div class="lecture-info">
                        <h4><?= lm_h($lecture['title']) ?></h4>
                        <div class="lecture-tags"><span class="lecture-tag"><?= lm_h($lecture['department']) ?></span><span class="lecture-tag">Year <?= lm_h($lecture['year_level']) ?></span><span class="lecture-tag"><?= !empty($lecture['section']) ? 'Section ' . lm_h($lecture['section']) : 'All Sections' ?></span><span class="lecture-tag">Status: <?= lm_h(ucfirst($lecture['status'] ?? 'unknown')) ?></span></div><?php if (!empty($lecture['description'])): ?><p class="text-secondary small mt-3 mb-0"><?= nl2br(lm_h($lecture['description'])) ?></p><?php endif; ?>
                    </div>
                </div>

                <?php if ($loadError !== ''): ?>
                    <div class="alert alert-warning mt-3"><strong>Progress data could not be loaded.</strong><br><?= lm_h($loadError) ?></div>
                <?php else: ?>
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-xl-3">
                            <div class="monitor-card monitor-stat">
                                <div class="d-flex justify-content-between align-items-center"><span class="monitor-stat-label">Assigned students</span><span class="monitor-stat-icon"><i class="bi bi-people"></i></span></div>
                                <div class="monitor-stat-value"><?= number_format($summary['assigned']) ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="monitor-card monitor-stat">
                                <div class="d-flex justify-content-between align-items-center"><span class="monitor-stat-label">Started</span><span class="monitor-stat-icon"><i class="bi bi-play-circle"></i></span></div>
                                <div class="monitor-stat-value"><?= number_format($summary['started']) ?></div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="monitor-card monitor-stat">
                                <div class="d-flex justify-content-between align-items-center"><span class="monitor-stat-label">Completed</span><span class="monitor-stat-icon"><i class="bi bi-check-circle"></i></span></div>
                                <div class="monitor-stat-value"><?= number_format($summary['completed']) ?><span class="fs-6 text-secondary"> (<?= $completionRate ?>%)</span></div>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3">
                            <div class="monitor-card monitor-stat">
                                <div class="d-flex justify-content-between align-items-center"><span class="monitor-stat-label">Submitted</span><span class="monitor-stat-icon"><i class="bi bi-file-earmark-check"></i></span></div>
                                <div class="monitor-stat-value"><?= number_format($summary['submitted']) ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="monitor-card monitor-filter">
                        <form method="get" action="lecture_monitor.php"><input type="hidden" name="id" value="<?= (int)$lecture['id'] ?>">
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-4 col-md-6"><label for="search" class="form-label">Search students</label><input class="form-control" type="search" id="search" name="search" placeholder="Name, student ID, email..." value="<?= lm_h($search) ?>"></div>
                                <div class="col-lg-2 col-md-6"><label for="year_section" class="form-label">Year / Section</label><select class="form-select" id="year_section" name="year_section">
                                        <option value="">All assigned sections</option><?php foreach (array_keys($yearSections) as $ys): ?><option value="<?= lm_h($ys) ?>" <?= $sectionFilter === $ys ? 'selected' : '' ?>><?= lm_h($ys) ?></option><?php endforeach; ?>
                                    </select></div>
                                <div class="col-lg-2 col-md-6"><label for="progress" class="form-label">Lecture progress</label><select class="form-select" id="progress" name="progress">
                                        <option value="">All progress</option>
                                        <option value="not_started" <?= $progressFilter === 'not_started' ? 'selected' : '' ?>>Not started</option>
                                        <option value="in_progress" <?= $progressFilter === 'in_progress' ? 'selected' : '' ?>>In progress</option>
                                        <option value="completed" <?= $progressFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    </select></div>
                                <div class="col-lg-2 col-md-6"><label for="submission" class="form-label">Submission</label><select class="form-select" id="submission" name="submission">
                                        <option value="">All submissions</option>
                                        <option value="submitted" <?= $submissionFilter === 'submitted' ? 'selected' : '' ?>>Submitted</option>
                                        <option value="not_submitted" <?= $submissionFilter === 'not_submitted' ? 'selected' : '' ?>>Not submitted</option>
                                    </select></div>
                                <div class="col-lg-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-funnel me-1"></i> Filter</button><a class="btn btn-outline-secondary" href="lecture_monitor.php?id=<?= (int)$lecture['id'] ?>" title="Reset filters"><i class="bi bi-arrow-counterclockwise"></i></a></div>
                            </div>
                        </form>
                    </div>

                    <div class="monitor-card">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 border-bottom">
                            <div class="fw-bold"><i class="bi bi-list-check me-2 text-primary"></i>Student Progress</div><span class="badge text-bg-primary"><?= number_format(count($students)) ?> shown / <?= number_format($summary['assigned']) ?> assigned</span>
                        </div>
                        <?php if ($students): ?><div class="table-responsive">
                                <table class="table table-hover monitor-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student</th>
                                            <th>Student ID</th>
                                            <th>Department</th>
                                            <th>Year / Section</th>
                                            <th>Lecture Progress</th>
                                            <th>Submission</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $index => $student): ?><tr>
                                                <td><?= $index + 1 ?></td>
                                                <td>
                                                    <div class="student-name"><?= lm_h($student['full_name']) ?></div>
                                                    <div class="muted-small"><?= lm_h($student['email'] ?? '') ?></div>
                                                </td>
                                                <td><?= lm_h($student['student_id'] ?? '—') ?></td>
                                                <td><?= lm_h($student['department'] ?? '—') ?></td>
                                                <td><?= lm_h($student['year_section'] ?? '—') ?></td>
                                                <td><?php if ($student['progress_status'] === 'completed'): ?><span class="status-pill pill-completed"><i class="bi bi-check-circle me-1"></i>Completed</span><?php elseif ($student['progress_status'] === 'in_progress'): ?><span class="status-pill pill-progress"><i class="bi bi-play-circle me-1"></i>In progress</span><?php else: ?><span class="status-pill pill-notstarted"><i class="bi bi-circle me-1"></i>Not started</span><?php endif; ?></td>
                                                <td><?php if ($student['is_submitted']): ?><span class="status-pill pill-submitted">Submitted</span><?php else: ?><span class="status-pill pill-pending">Not submitted</span><?php endif; ?></td>
                                            </tr><?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div><?php else: ?><div class="monitor-empty"><i class="bi bi-people d-block mb-3"></i>
                                <h5 class="fw-bold text-dark">No students match these filters</h5>
                                <p class="mb-0">Try changing the filters or reset them to see all assigned students.</p>
                            </div><?php endif; ?>
                    </div>
                    <p class="small text-secondary mt-3">Progress and submission states are read from <code>lecture_progress</code> and <code>lecture_submissions</code>. “Not started” means no matching progress record was found for the student.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php include 'globals/scripts.php'; ?>
</body>

</html>