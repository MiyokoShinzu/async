<?php
include './globals/checks.php';
require_once "../src/connection.php";

/* =========================================================
   ADMIN QUIZ MASTER SUBJECT ASSIGNMENTS
   ETS-Async Learning Portal

   Features:
   - Assign multiple subjects to a quiz master
   - View existing assignments
   - Search and filter assignments
   - Prevent duplicate assignments
   - Remove assignment records only
   - CSRF protection for assignment changes
========================================================== */

$user = $_SESSION["user"] ?? [];
$adminId = $user["id"] ?? null;

if (empty($_SESSION["quiz_master_subjects_csrf"])) {
    $_SESSION["quiz_master_subjects_csrf"] = bin2hex(random_bytes(32));
}

$message = "";
$messageType = "success";
$mysqli->set_charset("utf8mb4");

/* =========================================================
   DETECT AVAILABLE ACCOUNT DISPLAY COLUMNS
   This avoids assuming a specific name-column structure.
========================================================== */

$accountColumns = [];
$columnsResult = $mysqli->query("SHOW COLUMNS FROM accounts");
if ($columnsResult) {
    while ($column = $columnsResult->fetch_assoc()) {
        $accountColumns[] = $column["Field"];
    }
    $columnsResult->free();
}

$hasColumn = static function (string $column) use ($accountColumns): bool {
    return in_array($column, $accountColumns, true);
};

$displayParts = [];
foreach (["first_name", "middle_name", "last_name"] as $column) {
    if ($hasColumn($column)) {
        $displayParts[] = "NULLIF(TRIM(`" . $column . "`), '')";
    }
}
if (!$displayParts) {
    foreach (["full_name", "name", "username", "email"] as $column) {
        if ($hasColumn($column)) {
            $displayParts[] = "NULLIF(TRIM(`" . $column . "`), '')";
            break;
        }
    }
}
$nameExpression = $displayParts
    ? "COALESCE(" . implode(", ", $displayParts) . ", CONCAT('Quiz Master #', id))"
    : "CONCAT('Quiz Master #', id)";

/* =========================================================
   CSRF-PROTECTED FORM HANDLING
========================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $submittedToken = (string)($_POST["csrf_token"] ?? "");

    if (
        $submittedToken === "" ||
        !hash_equals($_SESSION["quiz_master_subjects_csrf"], $submittedToken)
    ) {
        $message = "Security check failed. Refresh the page and try again.";
        $messageType = "danger";
    } elseif (isset($_POST["assign_subjects"])) {
        $quizMasterId = filter_input(INPUT_POST, "quiz_master_id", FILTER_VALIDATE_INT);
        $subjectIds = $_POST["subject_ids"] ?? [];

        if (!$quizMasterId || $quizMasterId < 1) {
            $message = "Please select a valid quiz master.";
            $messageType = "danger";
        } elseif (!is_array($subjectIds) || count($subjectIds) === 0) {
            $message = "Select at least one subject to assign.";
            $messageType = "warning";
        } else {
            $cleanSubjectIds = [];
            foreach ($subjectIds as $subjectId) {
                $validated = filter_var($subjectId, FILTER_VALIDATE_INT);
                if ($validated && $validated > 0) {
                    $cleanSubjectIds[] = (int)$validated;
                }
            }
            $cleanSubjectIds = array_values(array_unique($cleanSubjectIds));

            $masterCheck = $mysqli->prepare(
                "SELECT id FROM accounts WHERE id = ? AND LOWER(access) = 'quiz_master' LIMIT 1"
            );
            if (!$masterCheck) {
                $message = "Unable to validate the selected quiz master.";
                $messageType = "danger";
            } else {
                $masterCheck->bind_param("i", $quizMasterId);
                $masterCheck->execute();
                $masterResult = $masterCheck->get_result();
                $validMaster = $masterResult && $masterResult->num_rows === 1;
                $masterCheck->close();

                if (!$validMaster) {
                    $message = "The selected account is not a valid quiz master.";
                    $messageType = "danger";
                } elseif (!$cleanSubjectIds) {
                    $message = "No valid subjects were selected.";
                    $messageType = "warning";
                } else {
                    $subjectCheck = $mysqli->prepare(
                        "SELECT id FROM subjects WHERE id = ? LIMIT 1"
                    );
                    $insertStmt = $mysqli->prepare(
                        "INSERT IGNORE INTO quiz_master_subjects (quiz_master_id, subject_id) VALUES (?, ?)"
                    );

                    if (!$subjectCheck || !$insertStmt) {
                        $message = "Unable to prepare the assignment operation. Check that the quiz_master_subjects table exists.";
                        $messageType = "danger";
                        if ($subjectCheck) $subjectCheck->close();
                        if ($insertStmt) $insertStmt->close();
                    } else {
                        $added = 0;
                        $skipped = 0;
                        $mysqli->begin_transaction();

                        try {
                            foreach ($cleanSubjectIds as $subjectId) {
                                $subjectCheck->bind_param("i", $subjectId);
                                $subjectCheck->execute();
                                $subjectResult = $subjectCheck->get_result();

                                if (!$subjectResult || $subjectResult->num_rows !== 1) {
                                    $skipped++;
                                    continue;
                                }

                                $insertStmt->bind_param("ii", $quizMasterId, $subjectId);
                                if (!$insertStmt->execute()) {
                                    throw new RuntimeException("An assignment could not be saved.");
                                }

                                if ($insertStmt->affected_rows === 1) {
                                    $added++;
                                } else {
                                    $skipped++;
                                }
                            }

                            $mysqli->commit();
                            $message = $added . " subject(s) assigned successfully.";
                            if ($skipped > 0) {
                                $message .= " " . $skipped . " invalid or already assigned subject(s) were skipped.";
                            }
                            $messageType = $added > 0 ? "success" : "info";
                            $_SESSION["quiz_master_subjects_csrf"] = bin2hex(random_bytes(32));
                        } catch (Throwable $e) {
                            $mysqli->rollback();
                            $message = "The assignments could not be saved. Please verify the database table and try again.";
                            $messageType = "danger";
                        }

                        $subjectCheck->close();
                        $insertStmt->close();
                    }
                }
            }
        }
    } elseif (isset($_POST["remove_assignment"])) {
        $assignmentId = filter_input(INPUT_POST, "assignment_id", FILTER_VALIDATE_INT);

        if (!$assignmentId || $assignmentId < 1) {
            $message = "Invalid assignment selected.";
            $messageType = "danger";
        } else {
            $deleteStmt = $mysqli->prepare(
                "DELETE FROM quiz_master_subjects WHERE id = ? LIMIT 1"
            );

            if (!$deleteStmt) {
                $message = "Unable to prepare the removal operation.";
                $messageType = "danger";
            } else {
                $deleteStmt->bind_param("i", $assignmentId);
                if ($deleteStmt->execute() && $deleteStmt->affected_rows === 1) {
                    $message = "Subject assignment removed successfully. The subject itself was not deleted.";
                    $_SESSION["quiz_master_subjects_csrf"] = bin2hex(random_bytes(32));
                } else {
                    $message = "Assignment not found or it has already been removed.";
                    $messageType = "warning";
                }
                $deleteStmt->close();
            }
        }
    }
}

/* =========================================================
   LOAD QUIZ MASTERS
========================================================== */

$quizMasters = [];
$masterSQL = "SELECT id, " . $nameExpression . " AS display_name
              FROM accounts
              WHERE LOWER(access) = 'quiz_master'
              ORDER BY display_name ASC, id ASC";
$masterResult = $mysqli->query($masterSQL);

if ($masterResult) {
    while ($row = $masterResult->fetch_assoc()) {
        $quizMasters[] = $row;
    }
    $masterResult->free();
}

/* =========================================================
   LOAD SUBJECTS
========================================================== */

$subjects = [];
$subjectResult = $mysqli->query(
    "SELECT id, subject_code, subject_name, description, status
     FROM subjects
     ORDER BY subject_code ASC, subject_name ASC"
);

if ($subjectResult) {
    while ($row = $subjectResult->fetch_assoc()) {
        $subjects[] = $row;
    }
    $subjectResult->free();
}

/* =========================================================
   LOAD ASSIGNMENTS
========================================================== */

$assignments = [];
$assignmentSQL = "
    SELECT
        qms.id AS assignment_id,
        qms.quiz_master_id,
        qms.subject_id,
        qms.assigned_at,
        " . $nameExpression . " AS quiz_master_name,
        s.subject_code,
        s.subject_name,
        s.status AS subject_status
    FROM quiz_master_subjects AS qms
    INNER JOIN accounts AS a ON a.id = qms.quiz_master_id
    INNER JOIN subjects AS s ON s.id = qms.subject_id
    ORDER BY quiz_master_name ASC, s.subject_code ASC, s.subject_name ASC
";

/* Qualify detected account columns in the display expression for the join query. */
$assignmentNameParts = [];
foreach (["first_name", "middle_name", "last_name"] as $column) {
    if ($hasColumn($column)) {
        $assignmentNameParts[] = "NULLIF(TRIM(a.`" . $column . "`), '')";
    }
}
if (!$assignmentNameParts) {
    foreach (["full_name", "name", "username", "email"] as $column) {
        if ($hasColumn($column)) {
            $assignmentNameParts[] = "NULLIF(TRIM(a.`" . $column . "`), '')";
            break;
        }
    }
}
$assignmentNameExpression = $assignmentNameParts
    ? "COALESCE(" . implode(", ", $assignmentNameParts) . ", CONCAT('Quiz Master #', a.id))"
    : "CONCAT('Quiz Master #', a.id)";
$assignmentSQL = str_replace($nameExpression . " AS quiz_master_name", $assignmentNameExpression . " AS quiz_master_name", $assignmentSQL);

$assignmentResult = $mysqli->query($assignmentSQL);
if ($assignmentResult) {
    while ($row = $assignmentResult->fetch_assoc()) {
        $assignments[] = $row;
    }
    $assignmentResult->free();
}

$totalMasters = count($quizMasters);
$totalSubjects = count($subjects);
$totalAssignments = count($assignments);
$assignedMasterIds = [];
$assignedSubjectIds = [];
foreach ($assignments as $assignment) {
    $assignedMasterIds[(int)$assignment["quiz_master_id"]] = true;
    $assignedSubjectIds[(int)$assignment["subject_id"]] = true;
}
$mastersWithAssignments = count($assignedMasterIds);
$unassignedSubjects = max(0, $totalSubjects - count($assignedSubjectIds));

function h($value): string
{
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'globals/head.php'; ?>

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

    .assignment-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px
    }

    .assignment-stat-card {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 18px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .03)
    }

    .assignment-stat-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 1.1rem
    }

    .assignment-stat-icon.blue {
        color: #2563eb;
        background: #eff6ff
    }

    .assignment-stat-icon.green {
        color: #16a34a;
        background: #ecfdf3
    }

    .assignment-stat-icon.orange {
        color: #ea580c;
        background: #fff7ed
    }

    .assignment-stat-content strong {
        display: block;
        color: #1f2937;
        font-size: 1.25rem;
        line-height: 1.2
    }

    .assignment-stat-content span {
        display: block;
        margin-top: 3px;
        color: #6b7280;
        font-size: .76rem
    }

    .assignment-admin-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
        overflow: hidden;
        margin-bottom: 22px
    }

    .assignment-admin-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        padding: 20px 22px;
        border-bottom: 1px solid #e5e7eb
    }

    .assignment-admin-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937
    }

    .assignment-admin-header p {
        margin: 4px 0 0;
        color: #6b7280;
        font-size: .8rem
    }

    .assignment-form-body {
        padding: 22px
    }

    .assignment-form-grid {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) minmax(280px, 1.5fr);
        gap: 20px
    }

    .assignment-field label {
        display: block;
        margin-bottom: 7px;
        color: #374151;
        font-size: .82rem;
        font-weight: 700
    }

    .assignment-field .form-select,
    .assignment-field .form-control {
        min-height: 42px;
        border-radius: 8px;
        font-size: .85rem
    }

    .subject-picker {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        overflow: hidden
    }

    .subject-picker-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb
    }

    .subject-picker-toolbar input {
        min-width: 0;
        flex: 1;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        padding: 7px 10px;
        font-size: .8rem
    }

    .subject-picker-list {
        max-height: 245px;
        overflow: auto;
        padding: 5px 12px
    }

    .subject-option {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 2px;
        border-bottom: 1px solid #f0f1f3;
        cursor: pointer
    }

    .subject-option:last-child {
        border-bottom: 0
    }

    .subject-option input {
        margin-top: 4px
    }

    .subject-option strong {
        display: block;
        font-size: .82rem;
        color: #1f2937
    }

    .subject-option small {
        display: block;
        color: #6b7280;
        font-size: .73rem;
        margin-top: 2px
    }

    .assignment-form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 18px
    }

    .assignment-hint {
        font-size: .76rem;
        color: #6b7280
    }

    .assignment-admin-filters {
        display: grid;
        grid-template-columns: 1fr 260px;
        gap: 10px;
        padding: 18px 22px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb
    }

    .assignment-search {
        position: relative
    }

    .assignment-search i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af
    }

    .assignment-search input {
        width: 100%;
        height: 42px;
        padding: 0 12px 0 38px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #fff;
        color: #1f2937;
        font-size: .85rem;
        outline: none
    }

    .assignment-admin-filters .form-select {
        height: 42px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: .82rem
    }

    .assignment-table-wrapper {
        width: 100%;
        overflow-x: auto
    }

    .assignment-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px
    }

    .assignment-table thead th {
        padding: 13px 16px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: .7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap
    }

    .assignment-table tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #f0f1f3;
        vertical-align: middle;
        color: #374151;
        font-size: .82rem
    }

    .assignment-table tbody tr:last-child td {
        border-bottom: 0
    }

    .assignment-table tbody tr:hover {
        background: #fafbfc
    }

    .assignment-main-text {
        display: block;
        color: #1f2937;
        font-weight: 700;
        font-size: .84rem
    }

    .assignment-sub-text {
        display: block;
        margin-top: 4px;
        color: #9ca3af;
        font-size: .73rem
    }

    .assignment-tag {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 5px;
        background: #f3f4f6;
        color: #4b5563;
        font-size: .68rem;
        font-weight: 600;
        white-space: nowrap
    }

    .assignment-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: .68rem;
        font-weight: 700
    }

    .assignment-status.active {
        color: #15803d;
        background: #ecfdf3
    }

    .assignment-status.inactive {
        color: #b91c1c;
        background: #fef2f2
    }

    .assignment-status span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor
    }

    .assignment-empty {
        padding: 50px 20px;
        text-align: center;
        display: none
    }

    .assignment-empty i {
        display: block;
        font-size: 1.7rem;
        color: #9ca3af;
        margin-bottom: 10px
    }

    .assignment-empty h5 {
        font-size: .95rem;
        color: #1f2937
    }

    .assignment-empty p {
        font-size: .78rem;
        color: #9ca3af;
        margin: 0
    }

    html[data-theme="dark"] .page-header h2,
    html[data-theme="dark"] .assignment-stat-content strong,
    html[data-theme="dark"] .assignment-admin-header h5,
    html[data-theme="dark"] .assignment-main-text,
    html[data-theme="dark"] .assignment-empty h5,
    html[data-theme="dark"] .subject-option strong {
        color: #f1f5f9
    }

    html[data-theme="dark"] .page-header p,
    html[data-theme="dark"] .assignment-stat-content span,
    html[data-theme="dark"] .assignment-admin-header p,
    html[data-theme="dark"] .assignment-sub-text,
    html[data-theme="dark"] .assignment-hint,
    html[data-theme="dark"] .subject-option small {
        color: #a8b0bf
    }

    html[data-theme="dark"] .assignment-stat-card,
    html[data-theme="dark"] .assignment-admin-card {
        background: #151922;
        border-color: #2a3140
    }

    html[data-theme="dark"] .assignment-admin-header,
    html[data-theme="dark"] .assignment-table thead th,
    html[data-theme="dark"] .assignment-admin-filters,
    html[data-theme="dark"] .subject-picker-toolbar {
        background: #1c2230;
        border-color: #2a3140
    }

    html[data-theme="dark"] .assignment-table tbody td,
    html[data-theme="dark"] .subject-option {
        border-color: #2a3140;
        color: #c2c9d3
    }

    html[data-theme="dark"] .assignment-table tbody tr:hover {
        background: #1c2230
    }

    html[data-theme="dark"] .assignment-tag {
        background: #252c38;
        color: #c2c9d3
    }

    html[data-theme="dark"] .assignment-search input,
    html[data-theme="dark"] .assignment-admin-filters .form-select,
    html[data-theme="dark"] .subject-picker,
    html[data-theme="dark"] .subject-picker-toolbar input,
    html[data-theme="dark"] .assignment-field .form-select {
        background: #151922;
        border-color: #3a4252;
        color: #f1f5f9
    }

    @media(max-width:900px) {
        .assignment-form-grid {
            grid-template-columns: 1fr
        }

        .assignment-admin-filters {
            grid-template-columns: 1fr 220px
        }
    }

    @media(max-width:650px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start
        }

        .assignment-stat-grid {
            grid-template-columns: 1fr
        }

        .assignment-admin-filters {
            grid-template-columns: 1fr
        }

        .assignment-form-body {
            padding: 16px
        }

        .assignment-admin-header {
            padding: 16px
        }

        .assignment-form-footer {
            align-items: stretch;
            flex-direction: column
        }

        .assignment-form-footer .btn {
            width: 100%
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
                    <h2>Quiz Master Subject Assignments</h2>
                    <p>Assign subjects to quiz masters and manage existing subject access.</p>
                </div>
            </div>

            <?php if ($message !== ""): ?>
                <div class="alert alert-<?= h($messageType) ?> alert-dismissible fade show" role="alert">
                    <?= h($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="assignment-stat-grid">
                <div class="assignment-stat-card">
                    <div class="assignment-stat-icon blue"><i class="bi bi-person-badge"></i></div>
                    <div class="assignment-stat-content"><strong><?= $totalMasters ?></strong><span>Total Quiz Masters</span></div>
                </div>
                <div class="assignment-stat-card">
                    <div class="assignment-stat-icon green"><i class="bi bi-journal-check"></i></div>
                    <div class="assignment-stat-content"><strong><?= $totalAssignments ?></strong><span>Subject Assignments</span></div>
                </div>
                <div class="assignment-stat-card">
                    <div class="assignment-stat-icon orange"><i class="bi bi-journal-minus"></i></div>
                    <div class="assignment-stat-content"><strong><?= $unassignedSubjects ?></strong><span>Subjects Not Yet Assigned</span></div>
                </div>
            </div>

            <section class="assignment-admin-card">
                <div class="assignment-admin-header">
                    <div>
                        <h5><i class="bi bi-plus-circle me-2"></i>Assign Subjects</h5>
                        <p>Choose a quiz master and select one or more subjects.</p>
                    </div>
                </div>
                <div class="assignment-form-body">
                    <?php if (!$quizMasters): ?>
                        <div class="alert alert-warning mb-0">No accounts with the <strong>quiz_master</strong> role were found.</div>
                    <?php elseif (!$subjects): ?>
                        <div class="alert alert-warning mb-0">No subjects were found. Add subjects before creating assignments.</div>
                    <?php else: ?>
                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= h($_SESSION["quiz_master_subjects_csrf"]) ?>">
                            <div class="assignment-form-grid">
                                <div class="assignment-field">
                                    <label for="quizMasterSelect">Quiz Master</label>
                                    <select class="form-select" id="quizMasterSelect" name="quiz_master_id" required>
                                        <option value="">Select quiz master</option>
                                        <?php foreach ($quizMasters as $master): ?>
                                            <option value="<?= (int)$master["id"] ?>"><?= h($master["display_name"]) ?> (ID: <?= (int)$master["id"] ?>)</option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="assignment-hint mt-2">Assignments are linked to the selected account ID.</div>
                                </div>
                                <div class="assignment-field">
                                    <label>Subjects</label>
                                    <div class="subject-picker">
                                        <div class="subject-picker-toolbar">
                                            <input type="search" id="subjectPickerSearch" placeholder="Search subject code or name..." autocomplete="off" aria-label="Search subjects">
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="selectVisibleSubjects">Select visible</button>
                                        </div>
                                        <div class="subject-picker-list" id="subjectPickerList">
                                            <?php foreach ($subjects as $subject): ?>
                                                <?php
                                                $subjectId = (int)$subject["id"];
                                                $subjectLabel = trim(($subject["subject_code"] ?? "") . " " . ($subject["subject_name"] ?? ""));
                                                $subjectDescription = trim((string)($subject["description"] ?? ""));
                                                ?>
                                                <label class="subject-option" data-subject-search="<?= h(strtolower($subjectLabel . " " . $subjectDescription)) ?>">
                                                    <input type="checkbox" name="subject_ids[]" value="<?= $subjectId ?>">
                                                    <span>
                                                        <strong><?= h($subjectLabel !== "" ? $subjectLabel : "Subject #" . $subjectId) ?></strong>
                                                        <?php if ($subjectDescription !== ""): ?><small><?= h($subjectDescription) ?></small><?php endif; ?>
                                                        <?php if (isset($subject["status"]) && (string)$subject["status"] !== "1"): ?><small>Currently marked inactive</small><?php endif; ?>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="assignment-hint mt-2"><span id="selectedSubjectCount">0</span> subject(s) selected. Existing assignments are skipped automatically.</div>
                                </div>
                            </div>
                            <div class="assignment-form-footer">
                                <span class="assignment-hint">This creates assignment records only; it does not modify the subjects themselves.</span>
                                <button type="submit" class="btn btn-primary" name="assign_subjects" value="1" onclick="return confirm('Assign the selected subjects to this quiz master?');">
                                    <i class="bi bi-check2-circle me-1"></i> Assign Selected Subjects
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </section>

            <section class="assignment-admin-card">
                <div class="assignment-admin-header">
                    <div>
                        <h5><i class="bi bi-list-check me-2"></i>Current Assignments</h5>
                        <p>Search, filter, and remove individual quiz master subject assignments.</p>
                    </div>
                </div>
                <div class="assignment-admin-filters">
                    <div class="assignment-search">
                        <i class="bi bi-search"></i>
                        <input type="search" id="assignmentSearch" placeholder="Search quiz master or subject..." autocomplete="off">
                    </div>
                    <select class="form-select" id="assignmentMasterFilter" aria-label="Filter by quiz master">
                        <option value="all">All Quiz Masters</option>
                        <?php foreach ($quizMasters as $master): ?>
                            <option value="<?= (int)$master["id"] ?>"><?= h($master["display_name"]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="assignment-table-wrapper">
                    <table class="assignment-table">
                        <thead>
                            <tr>
                                <th>Quiz Master</th>
                                <th>Subject</th>
                                <th>Assigned Date</th>
                                <th>Subject Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="assignmentTableBody">
                            <?php foreach ($assignments as $assignment): ?>
                                <?php
                                $masterId = (int)$assignment["quiz_master_id"];
                                $assignmentId = (int)$assignment["assignment_id"];
                                $subjectStatus = (int)($assignment["subject_status"] ?? 0);
                                $searchText = strtolower(($assignment["quiz_master_name"] ?? "") . " " . ($assignment["subject_code"] ?? "") . " " . ($assignment["subject_name"] ?? ""));
                                ?>
                                <tr class="assignment-row" data-search="<?= h($searchText) ?>" data-master="<?= $masterId ?>">
                                    <td><span class="assignment-main-text"><?= h($assignment["quiz_master_name"]) ?></span><span class="assignment-sub-text">Account ID: <?= $masterId ?></span></td>
                                    <td><span class="assignment-main-text"><?= h(trim(($assignment["subject_code"] ?? "") . " — " . ($assignment["subject_name"] ?? ""))) ?></span><span class="assignment-sub-text">Subject ID: <?= (int)$assignment["subject_id"] ?></span></td>
                                    <td><?= !empty($assignment["assigned_at"]) ? h(date("M d, Y", strtotime($assignment["assigned_at"]))) : "—" ?></td>
                                    <td>
                                        <?php if ($subjectStatus === 1): ?>
                                            <span class="assignment-status active"><span></span>Active</span>
                                        <?php else: ?>
                                            <span class="assignment-status inactive"><span></span>Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="" class="d-inline" onsubmit="return confirm('Remove this subject assignment? The subject itself will not be deleted.');">
                                            <input type="hidden" name="csrf_token" value="<?= h($_SESSION["quiz_master_subjects_csrf"]) ?>">
                                            <input type="hidden" name="assignment_id" value="<?= $assignmentId ?>">
                                            <button type="submit" name="remove_assignment" value="1" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-circle me-1"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="assignment-empty" id="assignmentEmpty">
                    <i class="bi bi-search"></i>
                    <h5>No assignments found</h5>
                    <p>Try another search term or quiz master filter.</p>
                </div>
            </section>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const assignmentSearch = document.getElementById('assignmentSearch');
            const masterFilter = document.getElementById('assignmentMasterFilter');
            const assignmentRows = Array.from(document.querySelectorAll('.assignment-row'));
            const assignmentEmpty = document.getElementById('assignmentEmpty');

            function filterAssignments() {
                const query = (assignmentSearch?.value || '').toLowerCase().trim();
                const masterId = masterFilter?.value || 'all';
                let visible = 0;

                assignmentRows.forEach(function(row) {
                    const matchesSearch = (row.dataset.search || '').includes(query);
                    const matchesMaster = masterId === 'all' || row.dataset.master === masterId;
                    const show = matchesSearch && matchesMaster;
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                if (assignmentEmpty) {
                    assignmentEmpty.style.display = visible === 0 ? 'block' : 'none';
                }
            }

            assignmentSearch?.addEventListener('input', filterAssignments);
            masterFilter?.addEventListener('change', filterAssignments);
            filterAssignments();

            const pickerSearch = document.getElementById('subjectPickerSearch');
            const subjectOptions = Array.from(document.querySelectorAll('.subject-option'));
            const selectedCount = document.getElementById('selectedSubjectCount');
            const selectVisibleButton = document.getElementById('selectVisibleSubjects');

            function updateSelectedCount() {
                if (selectedCount) {
                    selectedCount.textContent = document.querySelectorAll('input[name="subject_ids[]"]:checked').length;
                }
            }

            pickerSearch?.addEventListener('input', function() {
                const query = pickerSearch.value.toLowerCase().trim();
                subjectOptions.forEach(function(option) {
                    option.style.display = (option.dataset.subjectSearch || '').includes(query) ? '' : 'none';
                });
            });

            document.querySelectorAll('input[name="subject_ids[]"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            selectVisibleButton?.addEventListener('click', function() {
                subjectOptions.forEach(function(option) {
                    if (option.style.display !== 'none') {
                        const checkbox = option.querySelector('input[type="checkbox"]');
                        if (checkbox) checkbox.checked = true;
                    }
                });
                updateSelectedCount();
            });

            updateSelectedCount();
        });
    </script>

    <?php include 'globals/scripts.php'; ?>
</body>

</html>