<?php
/* CLASS CODE READING ASSIGNMENTS & PROGRESS MONITOR */
session_start();
include "globals/checks.php";
require_once "../src/connection.php";
$instructorId = (int)($_SESSION["user_id"] ?? $_SESSION["id"] ?? $_SESSION["instructor_id"] ?? 0);
$classCodeId = (int)($_GET["class_code_id"] ?? $_POST["class_code_id"] ?? 0);
if ($instructorId < 1 || $classCodeId < 1) {
    header("Location: class_code.php");
    exit;
}
if (empty($_SESSION["csrf_reading_assignment"])) $_SESSION["csrf_reading_assignment"] = bin2hex(random_bytes(32));
function h($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, "UTF-8");
}
function goBack($type, $message, $id): void
{
    $_SESSION["reading_assignment_flash"] = ["type" => $type, "message" => $message];
    header("Location: class_code_reading_assignments.php?class_code_id=" . (int)$id);
    exit;
}
$flash = $_SESSION["reading_assignment_flash"] ?? null;
unset($_SESSION["reading_assignment_flash"]);
$q = $mysqli->prepare("SELECT cc.id,cc.course_code,cc.description,cc.status AS class_status,ays.academic_year,ays.semester,ays.status AS period_status FROM class_codes cc JOIN academic_year_semesters ays ON ays.id=cc.academic_year_semester_id WHERE cc.id=? AND cc.instructor_id=? LIMIT 1");
$q->bind_param("ii", $classCodeId, $instructorId);
$q->execute();
$class = $q->get_result()->fetch_assoc();
$q->close();
if (!$class) {
    header("Location: class_code.php");
    exit;
}
$canAssign = $class["class_status"] === "Active" && $class["period_status"] === "Active";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_SESSION["csrf_reading_assignment"], $_POST["csrf_token"] ?? "")) goBack("danger", "Invalid session token. Please try again.", $classCodeId);
    $action = $_POST["action"] ?? "";
    if ($action === "assign") {
        if (!$canAssign) goBack("danger", "Only Active class codes in an Active academic period can receive new assignments.", $classCodeId);
        $activityId = (int)($_POST["reading_activity_id"] ?? 0);
        $dueInput = trim($_POST["due_date"] ?? "");
        $dueTs = $dueInput !== "" ? strtotime($dueInput) : false;
        if ($activityId < 1 || $dueTs === false) goBack("danger", "Select a repository material and a valid deadline.", $classCodeId);
        $owner = $mysqli->prepare("SELECT id FROM reading_activity_table WHERE id=? AND created_by=? AND (status='active' OR status IS NULL OR status='') LIMIT 1");
        $owner->bind_param("ii", $activityId, $instructorId);
        $owner->execute();
        $owns = $owner->get_result()->num_rows > 0;
        $owner->close();
        if (!$owns) goBack("danger", "That material is not available in your repository.", $classCodeId);
        $due = date("Y-m-d H:i:s", $dueTs);
        $ins = $mysqli->prepare("INSERT INTO class_code_reading_assignments(class_code_id,reading_activity_id,assigned_by,due_date,status) VALUES(?,?,?,?,'Active') ON DUPLICATE KEY UPDATE due_date=VALUES(due_date),status='Active'");
        if (!$ins) goBack("danger", "Assignment table is missing. Run class_code_reading_assignments.sql first.", $classCodeId);
        $ins->bind_param("iiis", $classCodeId, $activityId, $instructorId, $due);
        $ok = $ins->execute();
        $ins->close();
        goBack($ok ? "success" : "danger", $ok ? "Reading activity assigned." : "Unable to assign activity.", $classCodeId);
    }
    if ($action === "deactivate") {
        $assignmentId = (int)($_POST["assignment_id"] ?? 0);
        $u = $mysqli->prepare("UPDATE class_code_reading_assignments a JOIN class_codes cc ON cc.id=a.class_code_id SET a.status='Inactive' WHERE a.id=? AND a.class_code_id=? AND cc.instructor_id=?");
        $u->bind_param("iii", $assignmentId, $classCodeId, $instructorId);
        $ok = $u->execute();
        $u->close();
        goBack($ok ? "success" : "danger", $ok ? "Assignment moved to inactive history." : "Unable to update assignment.", $classCodeId);
    }
}
$repo = [];
$q = $mysqli->prepare("SELECT id,title,file_type,required_reading_minutes FROM reading_activity_table WHERE created_by=? AND (status='active' OR status IS NULL OR status='') ORDER BY created_at DESC,id DESC");
$q->bind_param("i", $instructorId);
$q->execute();
$rs = $q->get_result();
while ($r = $rs->fetch_assoc()) $repo[] = $r;
$q->close();
$assignments = [];
$q = $mysqli->prepare("SELECT a.id assignment_id,a.due_date,a.status assignment_status,a.assigned_at,r.id activity_id,r.title,r.file_type,r.required_reading_minutes,COUNT(DISTINCT CASE WHEN cce.status='Accepted' THEN cce.student_id END) accepted_students,COUNT(DISTINCT CASE WHEN cce.status='Accepted' AND rp.status='completed' THEN cce.student_id END) completed_students,COUNT(DISTINCT CASE WHEN cce.status='Accepted' AND COALESCE(rp.reading_seconds,0)>0 AND (rp.status IS NULL OR rp.status<>'completed') THEN cce.student_id END) in_progress_students,COALESCE(SUM(CASE WHEN cce.status='Accepted' THEN COALESCE(rp.reading_seconds,0) ELSE 0 END),0) total_reading_seconds FROM class_code_reading_assignments a JOIN class_codes cc ON cc.id=a.class_code_id JOIN reading_activity_table r ON r.id=a.reading_activity_id LEFT JOIN class_code_enrollments cce ON cce.class_code_id=cc.id AND cce.status='Accepted' LEFT JOIN reading_activity_progress rp ON rp.reading_activity_id=r.id AND rp.student_id=cce.student_id WHERE a.class_code_id=? AND cc.instructor_id=? GROUP BY a.id,a.due_date,a.status,a.assigned_at,r.id,r.title,r.file_type,r.required_reading_minutes ORDER BY a.assigned_at DESC,a.id DESC");
if ($q) {
    $q->bind_param("ii", $classCodeId, $instructorId);
    $q->execute();
    $rs = $q->get_result();
    while ($r = $rs->fetch_assoc()) $assignments[] = $r;
    $q->close();
}
$selected = (int)($_GET["assignment"] ?? 0);
$students = [];
if ($selected) {
    $q = $mysqli->prepare("SELECT cce.student_id,ac.first_name,ac.last_name,ac.middle_initial,ac.extension_name,rp.started_at,rp.last_accessed_at,rp.reading_seconds,rp.status progress_status,rp.completed_at,r.required_reading_minutes FROM class_code_reading_assignments a JOIN class_codes cc ON cc.id=a.class_code_id JOIN reading_activity_table r ON r.id=a.reading_activity_id JOIN class_code_enrollments cce ON cce.class_code_id=cc.id AND cce.status='Accepted' LEFT JOIN accounts ac ON ac.student_id=cce.student_id LEFT JOIN reading_activity_progress rp ON rp.reading_activity_id=r.id AND rp.student_id=cce.student_id WHERE a.id=? AND a.class_code_id=? AND cc.instructor_id=? ORDER BY COALESCE(NULLIF(ac.last_name,''),cce.student_id),ac.first_name");
    $q->bind_param("iii", $selected, $classCodeId, $instructorId);
    $q->execute();
    $rs = $q->get_result();
    while ($r = $rs->fetch_assoc()) $students[] = $r;
    $q->close();
}
function timeLabel($n): string
{
    $n = max(0, (int)$n);
    return sprintf("%02d:%02d:%02d", intdiv($n, 3600), intdiv($n % 3600, 60), $n % 60);
}
include "globals/head.php";
?>

<body><?php include "globals/sidebar.php"; ?><div class="sidebar-overlay" id="sidebarOverlay"></div><?php include "globals/topbar.php"; ?>
    <main class="main-content">
        <div class="content-wrapper reading-assignment-page">
            <header class="page-heading">
                <div>
                    <div class="eyebrow"><i class="bi bi-book-half"></i> READING ACTIVITIES</div>
                    <h2>Class Reading Assignments</h2>
                    <p><?= h($class["course_code"]) ?> — <?= h($class["description"]) ?></p><small><?= h($class["academic_year"] . " • " . $class["semester"]) ?> · <b><?= h($class["period_status"]) ?> academic period</b></small>
                </div><a class="back" href="class_code.php"><i class="bi bi-arrow-left"></i> Back to Class Codes</a>
            </header>
            <?php if ($flash): ?><div class="notice <?= h($flash["type"]) ?>"><?= h($flash["message"]) ?></div><?php endif; ?>
            <?php if (!$canAssign): ?><div class="notice warning">This class code or academic period is inactive. You can review historical assignments, but cannot assign new activities. Students will not see assignments from inactive academic periods.</div><?php endif; ?>
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h3><i class="bi bi-plus-circle"></i> Assign from Repository</h3>
                        <p>Select a saved reading material and set its deadline for this class.</p>
                    </div><a class="back" href="reading_activity_repository.php">Open Repository</a>
                </div>
                <?php if (!$repo): ?><p class="empty">No active repository materials found. Save a reading material first.</p><?php else: ?><form method="post" class="assign-form"><input type="hidden" name="csrf_token" value="<?= h($_SESSION["csrf_reading_assignment"]) ?>"><input type="hidden" name="class_code_id" value="<?= $classCodeId ?>"><input type="hidden" name="action" value="assign"><label>Reading Material<select name="reading_activity_id" required>
                                <option value="">Select material</option><?php foreach ($repo as $r): ?><option value="<?= (int)$r["id"] ?>"><?= h($r["title"]) ?> · <?= h(strtoupper($r["file_type"])) ?> · <?= (int)$r["required_reading_minutes"] ?> min</option><?php endforeach; ?>
                            </select></label><label>Assignment Deadline<input type="datetime-local" name="due_date" required></label><button type="submit" <?= !$canAssign ? "disabled" : "" ?>><i class="bi bi-send"></i> Assign Activity</button></form><?php endif; ?>
            </section>
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h3><i class="bi bi-list-check"></i> Assigned Activities</h3>
                        <p>Completion and accumulated reading time for Accepted students.</p>
                    </div><span class="count"><?= count($assignments) ?> total</span>
                </div>
                <?php if (!$assignments): ?><p class="empty">No reading activities assigned to this class code yet.</p><?php else: ?><div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Material</th>
                                    <th>Deadline</th>
                                    <th>Accepted</th>
                                    <th>Completed</th>
                                    <th>In Progress</th>
                                    <th>Total Reading Time</th>
                                    <th>Status</th>
                                    <th>Monitor</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody><?php foreach ($assignments as $a): ?><tr>
                                        <td><b><?= h($a["title"]) ?></b><small><?= h(strtoupper($a["file_type"])) ?> · <?= (int)$a["required_reading_minutes"] ?> min required</small></td>
                                        <td><?= h(date("M d, Y h:i A", strtotime($a["due_date"]))) ?></td>
                                        <td><?= (int)$a["accepted_students"] ?></td>
                                        <td class="green"><?= (int)$a["completed_students"] ?></td>
                                        <td><?= (int)$a["in_progress_students"] ?></td>
                                        <td><?= h(timeLabel($a["total_reading_seconds"])) ?></td>
                                        <td><span class="pill <?= strtolower($a["assignment_status"]) ?>"><?= h($a["assignment_status"]) ?></span></td>
                                        <td><a class="monitor" href="?class_code_id=<?= $classCodeId ?>&assignment=<?= (int)$a["assignment_id"] ?>">View Students</a></td>
                                        <td><?php if ($a["assignment_status"] === "Active"): ?><form method="post" onsubmit="return confirm('Move this assignment to inactive history?')"><input type="hidden" name="csrf_token" value="<?= h($_SESSION["csrf_reading_assignment"]) ?>"><input type="hidden" name="class_code_id" value="<?= $classCodeId ?>"><input type="hidden" name="assignment_id" value="<?= (int)$a["assignment_id"] ?>"><input type="hidden" name="action" value="deactivate"><button class="deactivate">Deactivate</button></form><?php endif; ?></td>
                                    </tr><?php endforeach; ?></tbody>
                        </table>
                    </div><?php endif; ?>
            </section>
            <?php if ($selected): ?><section class="panel">
                    <div class="panel-head">
                        <div>
                            <h3><i class="bi bi-people"></i> Student Reading Progress</h3>
                            <p>Only Accepted students enrolled in this class code are included.</p>
                        </div><span class="count"><?= count($students) ?> students</span>
                    </div><?php if (!$students): ?><p class="empty">No accepted students found.</p><?php else: ?><div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student</th>
                                        <th>Status</th>
                                        <th>Reading Time</th>
                                        <th>Required</th>
                                        <th>Progress</th>
                                        <th>Started</th>
                                        <th>Last Access</th>
                                        <th>Completed</th>
                                    </tr>
                                </thead>
                                <tbody><?php foreach ($students as $i => $p): $sec = (int)($p["reading_seconds"] ?? 0);
                                                                                                        $req = max(1, (int)$p["required_reading_minutes"] * 60);
                                                                                                        $st = strtolower((string)($p["progress_status"] ?? ""));
                                                                                                        $state = $st === "completed" ? "Completed" : (($sec > 0 || !empty($p["started_at"])) ? "In Progress" : "Not Started");
                                                                                                        $pct = $state === "Completed" ? 100 : min(99.9, $sec / $req * 100);
                                                                                                        $name = trim(($p["last_name"] ?? "") . ", " . ($p["first_name"] ?? "") . " " . ($p["middle_initial"] ?? "") . " " . ($p["extension_name"] ?? ""), " ,");
                                                                                                        if ($name === "") $name = "Name unavailable"; ?><tr>
                                            <td><?= $i + 1 ?></td>
                                            <td><b><?= h($name) ?></b><small><?= h($p["student_id"]) ?></small></td>
                                            <td><?= h($state) ?></td>
                                            <td><?= h(timeLabel($sec)) ?></td>
                                            <td><?= (int)$p["required_reading_minutes"] ?> min</td>
                                            <td>
                                                <div class="bar"><span style="width:<?= number_format($pct, 1, '.', '') ?>%"></span></div><small><?= number_format($pct, 1) ?>%</small>
                                            </td>
                                            <td><?= !empty($p["started_at"]) ? h(date("M d, Y h:i A", strtotime($p["started_at"]))) : "—" ?></td>
                                            <td><?= !empty($p["last_accessed_at"]) ? h(date("M d, Y h:i A", strtotime($p["last_accessed_at"]))) : "—" ?></td>
                                            <td><?= !empty($p["completed_at"]) ? h(date("M d, Y h:i A", strtotime($p["completed_at"]))) : "—" ?></td>
                                        </tr><?php endforeach; ?></tbody>
                            </table>
                        </div><?php endif; ?>
                </section><?php endif; ?>
        </div>
    </main><?php require_once "./globals/scripts.php"; ?>
    <style>
        .reading-assignment-page {
            padding-bottom: 35px
        }

        .page-heading {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 22px
        }

        .eyebrow {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            color: var(--academic-blue-dark, #3154b8);
            margin-bottom: 7px
        }

        .page-heading h2 {
            margin: 0;
            color: var(--text-color);
            font-size: 25px;
            font-weight: 750
        }

        .page-heading p {
            margin: 6px 0;
            color: var(--text-secondary);
            font-size: 13px
        }

        .page-heading small {
            color: var(--text-secondary)
        }

        .back,
        .monitor {
            color: var(--academic-blue-dark, #3154b8);
            text-decoration: none;
            font-size: 12px;
            font-weight: 700
        }

        .panel {
            background: var(--activity-card-bg, var(--card-bg, #fff));
            border: 1px solid var(--activity-border, #e7eaf0);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 18px;
            box-shadow: 0 4px 14px var(--shadow-color, rgba(20, 35, 70, .05))
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 17px
        }

        .panel h3 {
            margin: 0;
            color: var(--text-color);
            font-size: 15px
        }

        .panel h3 i {
            color: var(--academic-blue-dark, #3154b8);
            margin-right: 7px
        }

        .panel-head p {
            margin: 4px 0 0;
            color: var(--text-secondary);
            font-size: 12px
        }

        .assign-form {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(220px, 1fr) auto;
            gap: 12px;
            align-items: end
        }

        .assign-form label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-color)
        }

        .assign-form select,
        .assign-form input {
            display: block;
            width: 100%;
            height: 42px;
            margin-top: 6px;
            padding: 8px 11px;
            border: 1px solid var(--activity-border, #dfe3eb);
            border-radius: 9px;
            background: var(--input-bg, var(--activity-card-bg, #fff));
            color: var(--text-color);
            font-size: 12px
        }

        .assign-form button {
            height: 42px;
            padding: 0 15px;
            border: 0;
            border-radius: 9px;
            background: var(--academic-blue-dark, #3456b8);
            color: white;
            font-size: 12px;
            font-weight: 700
        }

        .assign-form button:disabled {
            opacity: .45;
            cursor: not-allowed
        }

        .notice {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 14px;
            font-size: 12px
        }

        .notice.success {
            background: #eaf8ef;
            color: #17683a
        }

        .notice.danger {
            background: #fff0f0;
            color: #a52b2b
        }

        .notice.warning {
            background: #fff8e8;
            color: #8a5a08
        }

        .empty {
            padding: 24px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 12px
        }

        .count {
            padding: 6px 10px;
            border-radius: 20px;
            background: var(--activity-progress-bg, #f1f3f8);
            font-size: 11px;
            color: var(--text-secondary)
        }

        .table-wrap {
            overflow-x: auto
        }

        table {
            width: 100%;
            min-width: 950px;
            border-collapse: collapse;
            color: var(--text-color)
        }

        th {
            padding: 11px 12px;
            text-align: left;
            background: var(--activity-progress-bg, #f6f7fa);
            color: var(--text-secondary);
            font-size: 10px;
            text-transform: uppercase;
            white-space: nowrap;
            border-block: 1px solid var(--activity-border, #e7eaf0)
        }

        td {
            padding: 12px;
            border-bottom: 1px solid var(--activity-border, #edf0f4);
            font-size: 11px;
            vertical-align: middle
        }

        td b {
            font-size: 12px
        }

        td small {
            display: block;
            color: var(--text-secondary);
            font-size: 10px;
            margin-top: 3px
        }

        .pill {
            padding: 4px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 750
        }

        .pill.active {
            background: #e8f7ee;
            color: #20834b
        }

        .pill.inactive {
            background: #eff1f4;
            color: #697382
        }

        .green {
            color: #20834b;
            font-weight: 800
        }

        .deactivate {
            border: 1px solid var(--activity-border, #dfe3eb);
            background: transparent;
            color: var(--text-secondary);
            padding: 5px 8px;
            border-radius: 7px;
            font-size: 10px
        }

        .bar {
            height: 6px;
            min-width: 75px;
            background: var(--activity-progress-bg, #edf0f5);
            border-radius: 8px;
            overflow: hidden
        }

        .bar span {
            display: block;
            height: 100%;
            background: #2698bd
        }

        .@media(max-width:767px) {
            .page-heading {
                flex-direction: column
            }

            .assign-form {
                grid-template-columns: 1fr
            }

            .assign-form button {
                width: 100%
            }

            .panel {
                padding: 15px
            }
        }
    </style>
</body>