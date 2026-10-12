<?php
include './globals/checks.php';
require_once '../src/connection.php';
$db = $mysqli ?? null;
require_once __DIR__ . '/exam_common.php';

$examId = (int)($_GET['exam_id'] ?? 0);
$statusFilter = (string)($_GET['status'] ?? '');
$error = '';

/** Convert a stored "First Middle Last Suffix" name to "Last, First Middle Suffix". */
function monitor_last_first(string $name): string
{
    $name = trim(preg_replace('/\s+/', ' ', $name));
    if ($name === '') return 'Unknown student';
    $parts = preg_split('/\s+/', $name) ?: [];
    if (count($parts) < 2) return $name;

    $suffixes = ['jr', 'jr.', 'sr', 'sr.', 'ii', 'iii', 'iv', 'v'];
    $suffix = '';
    $lastIndex = count($parts) - 1;
    if (in_array(strtolower($parts[$lastIndex]), $suffixes, true) && $lastIndex >= 2) {
        $suffix = array_pop($parts);
        $lastIndex--;
    }
    $last = $parts[$lastIndex];
    $given = array_slice($parts, 0, $lastIndex);
    $display = $last . ', ' . implode(' ', $given);
    if ($suffix !== '') $display .= ' ' . $suffix;
    return $display;
}

/** Sort attempt rows by the last name inferred from the stored student name. */
function monitor_last_name_key(string $name): string
{
    $display = monitor_last_first($name);
    $comma = strpos($display, ',');
    return mb_strtolower(trim($comma === false ? $display : substr($display, 0, $comma)), 'UTF-8');
}

try {
    $exams = ex_rows($db, 'SELECT id,title,test_code,status FROM exams ORDER BY created_at DESC');
    $selected = null;
    if ($examId > 0) {
        $r = ex_rows($db, 'SELECT * FROM exams WHERE id=?', 'i', [$examId]);
        $selected = $r[0] ?? null;
    }

    $sql = 'SELECT a.*, e.title, e.test_code,
                   TIMESTAMPDIFF(SECOND,NOW(),a.expires_at) AS seconds_left,
                   TIMESTAMPDIFF(SECOND,a.started_at,COALESCE(a.submitted_at,NOW())) AS elapsed_seconds
            FROM exam_attempts a
            JOIN exams e ON e.id=a.exam_id
            WHERE 1=1';
    $types = '';
    $args = [];
    if ($examId > 0) {
        $sql .= ' AND a.exam_id=?';
        $types .= 'i';
        $args[] = $examId;
    }
    if (in_array($statusFilter, ['in_progress', 'submitted', 'timed_out'], true)) {
        $sql .= ' AND a.status=?';
        $types .= 's';
        $args[] = $statusFilter;
    }
    // Fetch the most recent 500 matching attempts, then sort these alphabetically by last name below.
    $sql .= " ORDER BY CASE WHEN a.status='in_progress' THEN 0 ELSE 1 END, a.started_at DESC LIMIT 500";
    $attempts = ex_rows($db, $sql, $types, $args);

    usort($attempts, static function (array $a, array $b): int {
        $lastCompare = strcmp(monitor_last_name_key((string)($a['student_name'] ?? '')), monitor_last_name_key((string)($b['student_name'] ?? '')));
        if ($lastCompare !== 0) return $lastCompare;
        return strcasecmp(monitor_last_first((string)($a['student_name'] ?? '')), monitor_last_first((string)($b['student_name'] ?? '')));
    });


    // Leaderboard uses completed attempts and is independent of the status filter above.
    $leaderSql = 'SELECT a.id,a.exam_id,a.student_name,a.student_number,a.student_account_key,a.score,a.total_items,a.started_at,a.submitted_at,e.title AS exam_title,e.test_code FROM exam_attempts a JOIN exams e ON e.id=a.exam_id WHERE a.status IN (\'submitted\',\'timed_out\') AND a.score IS NOT NULL AND a.total_items > 0';
    $leaderTypes = '';
    $leaderArgs = [];
    if ($examId > 0) {
        $leaderSql .= ' AND a.exam_id=?';
        $leaderTypes .= 'i';
        $leaderArgs[] = $examId;
    }
    $leaderRows = ex_rows($db, $leaderSql, $leaderTypes, $leaderArgs);
    usort($leaderRows, static function (array $a, array $b): int {
        $pa = (int)$a['score'] / max(1, (int)$a['total_items']);
        $pb = (int)$b['score'] / max(1, (int)$b['total_items']);
        if ($pa !== $pb) return $pb <=> $pa;
        if ((int)$a['score'] !== (int)$b['score']) return (int)$b['score'] <=> (int)$a['score'];
        $ta = strtotime((string)($a['submitted_at'] ?: $a['started_at'])) ?: PHP_INT_MAX;
        $tb = strtotime((string)($b['submitted_at'] ?: $b['started_at'])) ?: PHP_INT_MAX;
        if ($ta !== $tb) return $ta <=> $tb;
        return strcasecmp(monitor_last_first((string)$a['student_name']), monitor_last_first((string)$b['student_name']));
    });
    // Keep each student only once, using their best result in the selected scope.
    $leaderboard = [];
    $seenStudents = [];
    foreach ($leaderRows as $lr) {
        $identity = trim((string)($lr['student_account_key'] ?? ''));
        if ($identity === '') $identity = trim((string)($lr['student_number'] ?? ''));
        if ($identity === '') $identity = mb_strtolower(trim((string)$lr['student_name']), 'UTF-8');
        if (isset($seenStudents[$identity])) continue;
        $seenStudents[$identity] = true;
        $leaderboard[] = $lr;
        if (count($leaderboard) >= 10) break;
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
    $exams = [];
    $attempts = [];
    $leaderboard = [];
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="exam_attempts.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Exam', 'Test Code', 'Student Name (Last, First)', 'Student Number', 'Set', 'Status', 'Score', 'Total', 'Tab Switch Count', 'Max Open Tabs', 'Started At', 'Submitted At', 'Last Seen']);
    foreach ($attempts as $a) {
        fputcsv($out, [
            $a['title'],
            $a['test_code'],
            monitor_last_first((string)$a['student_name']),
            $a['student_number'],
            $a['variant'],
            $a['status'],
            $a['score'],
            $a['total_items'],
            (int)($a['tab_switch_count'] ?? 0),
            (int)($a['max_open_tabs'] ?? 1),
            $a['started_at'],
            $a['submitted_at'],
            $a['last_seen_at']
        ]);
    }
    fclose($out);
    exit;
}

ex_page_start('Exam Monitoring', true);
?>
<div class="exam-monitor-page">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-activity me-2"></i>Exam Monitoring</h2>
            <p class="text-secondary mb-0">Monitor active attempts, assigned sets, submission status, scores, and tab activity.</p>
        </div>
        <a class="btn btn-outline-secondary" href="exam_manager.php<?= $examId ? '?id=' . $examId : '' ?>"><i class="bi bi-arrow-left me-1"></i>Back to Exam Manager</a>
    </div>
    <?php if ($error): ?><div class="alert alert-danger"><?= ex_e($error) ?></div><?php endif; ?>

    <section class="exam-card">
        <div class="exam-card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Exam</label>
                    <select class="form-select" name="exam_id">
                        <option value="0">All exams</option>
                        <?php foreach ($exams as $e): ?>
                            <option value="<?= (int)$e['id'] ?>" <?= $examId === (int)$e['id'] ? 'selected' : '' ?>><?= ex_e($e['title'] . ' · ' . $e['test_code'] . ' · ' . $e['status']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Attempt status</label>
                    <select class="form-select" name="status">
                        <option value="">All statuses</option>
                        <?php foreach (['in_progress' => 'In progress', 'submitted' => 'Submitted', 'timed_out' => 'Timed out'] as $v => $label): ?>
                            <option value="<?= ex_e($v) ?>" <?= $statusFilter === $v ? 'selected' : '' ?>><?= ex_e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1"><button class="btn btn-primary w-100">Go</button></div>
            </form>
            <div class="d-flex gap-2 mt-3 flex-wrap">
                <a class="btn btn-sm btn-outline-secondary" href="exam_monitor.php?<?= http_build_query(array_filter(['exam_id' => $examId ?: null, 'status' => $statusFilter ?: null, 'export' => 'csv'])) ?>"><i class="bi bi-download me-1"></i>Export CSV</a>
                <span class="small text-secondary align-self-center">Auto-refreshes every 15 seconds. Active status is based on the last request from the student's browser.</span>
            </div>
        </div>
    </section>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="exam-card p-3">
                <div class="small text-secondary">In progress</div>
                <div class="fs-2 fw-bold"><?= count(array_filter($attempts, fn($a) => $a['status'] === 'in_progress')) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="exam-card p-3">
                <div class="small text-secondary">Submitted</div>
                <div class="fs-2 fw-bold"><?= count(array_filter($attempts, fn($a) => $a['status'] === 'submitted')) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="exam-card p-3">
                <div class="small text-secondary">Timed out</div>
                <div class="fs-2 fw-bold"><?= count(array_filter($attempts, fn($a) => $a['status'] === 'timed_out')) ?></div>
            </div>
        </div>
    </div>

    <section class="exam-card exam-leaderboard mb-4">
        <div class="exam-card-head d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1"><i class="bi bi-trophy-fill text-warning me-2"></i>Leaderboard</h5>
                <div class="small text-secondary"><?= $examId > 0 ? 'Top completed results for the selected exam.' : 'Top students by their best completed percentage across all exams.' ?></div>
            </div>
            <span class="badge text-bg-primary">Top <?= count($leaderboard) ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped align-middle mb-0 leaderboard-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Student (Last, First)</th>
                        <th>Exam</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$leaderboard): ?><tr>
                            <td colspan="6" class="text-center text-secondary py-4">No completed results available for the selected scope yet.</td>
                        </tr><?php endif; ?>
                    <?php foreach ($leaderboard as $rank => $leader): $percent = ((int)$leader['score'] / max(1, (int)$leader['total_items'])) * 100; ?>
                        <tr>
                            <td><span class="leader-rank <?= $rank < 3 ? 'leader-rank-top' : '' ?>"><?= $rank + 1 ?></span></td>
                            <td><strong><?= ex_e(monitor_last_first((string)$leader['student_name'])) ?></strong>
                                <div class="small text-secondary"><?= ex_e($leader['student_number'] ?: 'No student number') ?></div>
                            </td>
                            <td><?= ex_e($leader['exam_title']) ?><div class="small text-secondary"><code><?= ex_e($leader['test_code']) ?></code></div>
                            </td>
                            <td class="text-nowrap"><?= (int)$leader['score'] ?> / <?= (int)$leader['total_items'] ?></td>
                            <td><strong><?= number_format($percent, 1) ?>%</strong></td>
                            <td class="small text-nowrap"><?= ex_e((string)($leader['submitted_at'] ?: $leader['started_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="exam-card exam-monitor-results">
        <div class="exam-card-head">
            <h5 class="fw-bold mb-0"><?= count($attempts) ?> attempt(s) <span class="small text-secondary fw-normal">· sorted by student last name</span></h5>
        </div>
        <div class="exam-card-body pb-2">
            <div class="exam-table-controls">
                <div class="d-flex align-items-center gap-2">
                    <label for="examTableLength" class="small text-secondary mb-0">Show</label>
                    <select id="examTableLength" class="form-select form-select-sm" aria-label="Rows per page" style="width:auto">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="small text-secondary">entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label for="examTableSearch" class="small text-secondary mb-0">Search:</label>
                    <input type="search" id="examTableSearch" class="form-control form-control-sm" placeholder="Search attempts…" autocomplete="off">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="examAttemptsTable" class="table table-sm table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col" data-sort="text">Student (Last, First)</th>
                        <th scope="col" data-sort="text">Exam / Code</th>
                        <th scope="col" data-sort="text">Set</th>
                        <th scope="col" data-sort="text">Status</th>
                        <th scope="col" data-sort="time">Time</th>
                        <th scope="col" data-sort="number">Score</th>
                        <th scope="col" data-sort="number">Tab switches</th>
                        <th scope="col" data-sort="number">Max open tabs</th>
                        <th scope="col" data-sort="date">Started</th>
                        <th scope="col" data-sort="date">Last seen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$attempts): ?><tr>
                            <td colspan="10" class="text-center text-secondary py-5">No attempts match this filter yet.</td>
                        </tr><?php endif; ?>
                    <?php foreach ($attempts as $a): $active = (int)$a['seconds_left'];
                        $elapsed = (int)$a['elapsed_seconds'];
                        $switchCount = (int)($a['tab_switch_count'] ?? 0);
                        $maxTabs = max(1, (int)($a['max_open_tabs'] ?? 1)); ?>
                        <tr>
                            <td><strong><?= ex_e(monitor_last_first((string)$a['student_name'])) ?></strong>
                                <div class="small text-secondary"><?= ex_e($a['student_number'] ?: 'No student number') ?></div>
                            </td>
                            <td><?= ex_e($a['title']) ?><div class="small text-secondary"><code><?= ex_e($a['test_code']) ?></code></div>
                            </td>
                            <td><span class="badge text-bg-primary"><?= ex_e($a['variant']) ?></span></td>
                            <td><span class="badge text-bg-<?= $a['status'] === 'in_progress' ? 'warning' : ($a['status'] === 'submitted' ? 'success' : 'secondary') ?>"><?= ex_e(str_replace('_', ' ', $a['status'])) ?></span>
                                <?php if ($a['status'] === 'in_progress' && time() - strtotime((string)$a['last_seen_at']) > 60): ?><div class="small text-danger">No activity recently</div><?php endif; ?>
                            </td>
                            <td><?php if ($a['status'] === 'in_progress'): ?><?= gmdate('H:i:s', max(0, $active)) ?> left<?php else: ?><?= gmdate('H:i:s', max(0, $elapsed)) ?> used<?php endif; ?></td>
                            <td><?= $a['score'] === null ? '—' : ((int)$a['score'] . ' / ' . (int)$a['total_items']) ?></td>
                            <td><span class="badge <?= $switchCount > 0 ? 'text-bg-warning' : 'text-bg-secondary' ?>"><?= $switchCount ?></span></td>
                            <td><span class="badge <?= $maxTabs >= 2 ? 'text-bg-warning' : 'text-bg-secondary' ?>"><?= $maxTabs ?></span></td>
                            <td class="small text-nowrap"><?= ex_e($a['started_at']) ?></td>
                            <td class="small text-nowrap"><?= ex_e($a['last_seen_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <style>
        /* Hide the global topbar only on this monitoring page; keep the sidebar/layout. */
        body:has(.exam-monitor-page) .topbar,
        body:has(.exam-monitor-page) #topbar,
        body:has(.exam-monitor-page) .top-bar,
        body:has(.exam-monitor-page) .main-topbar,
        body:has(.exam-monitor-page) header.topbar {
            display: none !important;
        }

        body:has(.exam-monitor-page) .main-content {
            padding-top: 0 !important;
        }

        body:has(.exam-monitor-page) .content-wrapper {
            padding-top: 18px;
        }

        .exam-leaderboard .leader-rank {
            display: inline-flex;
            min-width: 28px;
            height: 28px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: var(--bs-tertiary-bg, #f1f5f9);
            font-weight: 700;
        }

        .exam-leaderboard .leader-rank-top {
            background: #fef3c7;
            color: #92400e;
        }

        .exam-leaderboard .table {
            font-size: .82rem;
        }

        .exam-leaderboard .table th,
        .exam-leaderboard .table td {
            padding: .5rem .65rem;
        }

        .exam-monitor-results {
            --monitor-table-font: .82rem;
        }

        .exam-monitor-results .exam-table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .exam-monitor-results #examTableSearch {
            width: min(260px, 58vw);
        }

        .exam-monitor-results .table {
            font-size: var(--monitor-table-font);
        }

        .exam-monitor-results .table th,
        .exam-monitor-results .table td {
            padding: .48rem .6rem;
            vertical-align: middle;
        }

        .exam-monitor-results .table thead th {
            white-space: nowrap;
            font-size: .76rem;
            letter-spacing: .01em;
        }

        .exam-monitor-results .table thead th[data-sort] {
            cursor: pointer;
            user-select: none;
        }

        .exam-monitor-results .table thead th[data-sort]::after {
            content: ' ↕';
            color: var(--bs-secondary-color, #6c757d);
            opacity: .65;
            font-size: .8em;
        }

        .exam-monitor-results .table thead th.sort-asc::after {
            content: ' ↑';
            opacity: 1;
        }

        .exam-monitor-results .table thead th.sort-desc::after {
            content: ' ↓';
            opacity: 1;
        }

        .exam-monitor-results .table-striped>tbody>tr:nth-of-type(odd)>* {
            --bs-table-bg-type: var(--bs-tertiary-bg, rgba(0, 0, 0, .035));
        }

        .exam-monitor-results .exam-table-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            padding: 12px 16px;
            border-top: 1px solid var(--bs-border-color, #dee2e6);
            font-size: .8rem;
        }

        .exam-monitor-results .exam-table-pagination {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }

        .exam-monitor-results .exam-table-pagination button {
            min-width: 32px;
            padding: .25rem .5rem;
            border: 1px solid var(--bs-border-color, #dee2e6);
            border-radius: 6px;
            background: var(--bs-body-bg, #fff);
            color: var(--bs-body-color, #212529);
            font-size: .78rem;
        }

        .exam-monitor-results .exam-table-pagination button[aria-current="page"] {
            background: var(--bs-primary, #0d6efd);
            color: #fff;
            border-color: var(--bs-primary, #0d6efd);
        }

        .exam-monitor-results .exam-table-pagination button:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        @media(max-width:600px) {
            .exam-monitor-results .exam-table-controls {
                align-items: stretch;
            }

            .exam-monitor-results .exam-table-controls>div:last-child {
                width: 100%;
            }

            .exam-monitor-results #examTableSearch {
                flex: 1;
                width: auto;
            }
        }
    </style>
    <script>
        (function() {
            const table = document.getElementById('examAttemptsTable');
            if (!table) return;
            const tbody = table.tBodies[0];
            const allRows = Array.from(tbody.rows).filter(row => !row.querySelector('td[colspan]'));
            const search = document.getElementById('examTableSearch');
            const length = document.getElementById('examTableLength');
            const responsive = table.closest('.exam-monitor-results');
            if (!tbody || !search || !length || !responsive) return;

            let filteredRows = allRows.slice();
            let currentPage = 1;
            let sortColumn = -1;
            let sortDirection = 'asc';
            const footer = document.createElement('div');
            footer.className = 'exam-table-footer';
            footer.innerHTML = '<div class="exam-table-info" aria-live="polite"></div><div class="exam-table-pagination" aria-label="Table pagination"></div>';
            responsive.appendChild(footer);
            const info = footer.querySelector('.exam-table-info');
            const pagination = footer.querySelector('.exam-table-pagination');

            function cellText(row, index) {
                return (row.cells[index]?.innerText || '').trim();
            }

            function sortValue(row, index, type) {
                const value = cellText(row, index);
                if (type === 'number') {
                    const match = value.match(/-?\d+(?:\.\d+)?/);
                    return match ? Number(match[0]) : -1;
                }
                if (type === 'time') {
                    const match = value.match(/(\d+):(\d+):(\d+)/);
                    return match ? Number(match[1]) * 3600 + Number(match[2]) * 60 + Number(match[3]) : 0;
                }
                if (type === 'date') {
                    const parsed = Date.parse(value);
                    return Number.isNaN(parsed) ? 0 : parsed;
                }
                return value.toLocaleLowerCase();
            }

            function applySearch() {
                const query = search.value.trim().toLocaleLowerCase();
                filteredRows = allRows.filter(row => row.innerText.toLocaleLowerCase().includes(query));
                if (sortColumn >= 0) sortRows();
                currentPage = 1;
                render();
            }

            function sortRows() {
                const header = table.tHead.rows[0].cells[sortColumn];
                const type = header.dataset.sort || 'text';
                filteredRows.sort((a, b) => {
                    const av = sortValue(a, sortColumn, type);
                    const bv = sortValue(b, sortColumn, type);
                    const result = typeof av === 'number' && typeof bv === 'number' ? av - bv : String(av).localeCompare(String(bv), undefined, {
                        numeric: true,
                        sensitivity: 'base'
                    });
                    return (sortDirection === 'asc' ? result : -result);
                });
            }

            function render() {
                const pageSize = Number(length.value) || 10;
                const total = filteredRows.length;
                const pages = Math.max(1, Math.ceil(total / pageSize));
                currentPage = Math.min(currentPage, pages);
                const start = total ? (currentPage - 1) * pageSize : 0;
                const visible = filteredRows.slice(start, start + pageSize);
                tbody.replaceChildren(...visible);
                if (!total) {
                    const tr = document.createElement('tr');
                    const td = document.createElement('td');
                    td.colSpan = table.tHead.rows[0].cells.length;
                    td.className = 'text-center text-secondary py-4';
                    td.textContent = 'No matching attempts found.';
                    tr.appendChild(td);
                    tbody.appendChild(tr);
                }
                info.textContent = total ? `Showing ${start + 1} to ${Math.min(start + pageSize, total)} of ${total} entries` : 'Showing 0 entries';
                pagination.replaceChildren();
                const addButton = (label, page, disabled, current) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    button.disabled = disabled;
                    if (current) button.setAttribute('aria-current', 'page');
                    button.addEventListener('click', () => {
                        currentPage = page;
                        render();
                    });
                    pagination.appendChild(button);
                };
                addButton('‹', Math.max(1, currentPage - 1), currentPage <= 1, false);
                let first = Math.max(1, currentPage - 2),
                    last = Math.min(pages, first + 4);
                first = Math.max(1, last - 4);
                for (let page = first; page <= last; page++) addButton(String(page), page, false, page === currentPage);
                addButton('›', Math.min(pages, currentPage + 1), currentPage >= pages, false);
            }
            search.addEventListener('input', applySearch);
            length.addEventListener('change', () => {
                currentPage = 1;
                render();
            });
            Array.from(table.tHead.rows[0].cells).forEach((th, index) => {
                th.addEventListener('click', () => {
                    if (!th.dataset.sort) return;
                    if (sortColumn === index) sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    else {
                        sortColumn = index;
                        sortDirection = 'asc';
                    }
                    Array.from(table.tHead.rows[0].cells).forEach(cell => cell.classList.remove('sort-asc', 'sort-desc'));
                    th.classList.add(sortDirection === 'asc' ? 'sort-asc' : 'sort-desc');
                    sortRows();
                    currentPage = 1;
                    render();
                });
            });
            render();
        })();
        setTimeout(() => location.reload(), 15000);
    </script>
</div>
<?php ex_page_end(true); ?>