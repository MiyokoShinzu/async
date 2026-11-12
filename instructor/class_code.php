<?php
/* =========================================================
   CLASS CODE MANAGEMENT
   ETS-Async Learning Portal

   Expected database table: class_codes
   Fields:
   id, course_code, description, academic_year_semester_id,
   instructor_id, status, created_at, updated_at

   This page does not create or alter database tables.
   ========================================================= */

session_start();
include "globals/checks.php";
require_once "../src/connection.php";

// Logged-in instructor/account ID used to scope all class-code operations.
$currentInstructorId = (int)($_SESSION["user_id"] ?? $_SESSION["id"] ?? $_SESSION["instructor_id"] ?? 0);

if (empty($_SESSION["csrf_class_code"])) {
    $_SESSION["csrf_class_code"] = bin2hex(random_bytes(32));
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function redirectWithMessage(string $type, string $message): void
{
    $_SESSION["class_code_flash"] = ["type" => $type, "message" => $message];
    header("Location: class_code.php");
    exit;
}

$flash = $_SESSION["class_code_flash"] ?? null;
unset($_SESSION["class_code_flash"]);

$errors = [];
$editRecord = null;

/* CREATE / UPDATE / DELETE */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_class_code"], $token)) {
        redirectWithMessage("danger", "Your session token is invalid. Please try again.");
    }

    $action = $_POST["action"] ?? "";

    if ($action === "delete") {
        $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
        if (!$id || $id < 1) {
            redirectWithMessage("danger", "Invalid class code record.");
        }

        $stmt = $mysqli->prepare("DELETE FROM class_codes WHERE id = ? AND instructor_id = ?");
        if (!$stmt) {
            redirectWithMessage("danger", "Unable to prepare the delete request. Check the database table.");
        }

        $stmt->bind_param("ii", $id, $currentInstructorId);
        $ok = $stmt->execute();
        $deleted = $stmt->affected_rows;
        $stmt->close();

        if ($ok && $deleted > 0) {
            redirectWithMessage("success", "Class code deleted successfully.");
        }
        redirectWithMessage("warning", "The record was not found or could not be deleted.");
    }

    if ($action === "save") {
        $id = (int)($_POST["id"] ?? 0);
        $courseCode = strtoupper(trim($_POST["course_code"] ?? ""));
        $description = trim($_POST["description"] ?? "");
        $academicPeriodId = filter_var($_POST["academic_year_semester_id"] ?? null, FILTER_VALIDATE_INT);
        $status = trim($_POST["status"] ?? "Active");

        if ($courseCode === "" || !preg_match('/^[A-Z0-9][A-Z0-9 ._-]{1,29}$/', $courseCode)) {
            $errors[] = "Enter a valid course code (2–30 characters; letters, numbers, spaces, dots, hyphens, and underscores).";
        }
        if ($description === "" || mb_strlen($description) > 255) {
            $errors[] = "Enter a class description (maximum 255 characters).";
        }
        if (!$academicPeriodId || $academicPeriodId < 1) {
            $errors[] = "Please select an academic year and semester.";
        }

        // Resolve the currently logged-in instructor/account ID.
        $instructorId = $currentInstructorId;
        if ($instructorId < 1) {
            $errors[] = "Your instructor account ID could not be detected. Please check the session variable used by your login page.";
        } else {
            $instructorId = (int)$instructorId;
        }
        if (!in_array($status, ["Active", "Inactive"], true)) {
            $errors[] = "Please select a valid status.";
        }

        if (!$errors) {
            $periodCheck = $mysqli->prepare("SELECT id FROM academic_year_semesters WHERE id = ? LIMIT 1");
            if (!$periodCheck) {
                $errors[] = "Unable to validate the academic period. Check the academic_year_semesters table.";
            } else {
                $periodCheck->bind_param("i", $academicPeriodId);
                $periodCheck->execute();
                $periodExists = $periodCheck->get_result()->num_rows > 0;
                $periodCheck->close();
                if (!$periodExists) {
                    $errors[] = "The selected academic year and semester does not exist.";
                }
            }
        }

        if (!$errors) {
            $check = $mysqli->prepare(
                "SELECT id FROM class_codes
                 WHERE course_code = ? AND description = ? AND academic_year_semester_id = ?
                 AND instructor_id = ? AND id <> ? LIMIT 1"
            );
            if (!$check) {
                $errors[] = "Unable to validate the class code. Check that the class_codes table exists.";
            } else {
                $check->bind_param("ssiii", $courseCode, $description, $academicPeriodId, $instructorId, $id);
                $check->execute();
                $duplicate = $check->get_result()->num_rows > 0;
                $check->close();
                if ($duplicate) {
                    $errors[] = "This course code, description, and academic period combination already exists.";
                }
            }
        }

        if (!$errors) {
            if ($id > 0) {
                $stmt = $mysqli->prepare(
                    "UPDATE class_codes
                     SET course_code = ?, description = ?, academic_year_semester_id = ?, status = ?
                     WHERE id = ? AND instructor_id = ?"
                );
                if (!$stmt) {
                    $errors[] = "Unable to prepare the update. Check the database table.";
                } else {
                    $stmt->bind_param("ssisii", $courseCode, $description, $academicPeriodId, $status, $id, $instructorId);
                    if ($stmt->execute()) {
                        $stmt->close();
                        redirectWithMessage("success", "Class code updated successfully.");
                    }
                    $errors[] = "Unable to update the record: " . $stmt->error;
                    $stmt->close();
                }
            } else {
                $stmt = $mysqli->prepare(
                    "INSERT INTO class_codes (course_code, description, academic_year_semester_id, instructor_id, status)
                     VALUES (?, ?, ?, ?, ?)"
                );
                if (!$stmt) {
                    $errors[] = "Unable to prepare the insert. Check the database table.";
                } else {
                    $stmt->bind_param("ssiis", $courseCode, $description, $academicPeriodId, $instructorId, $status);
                    if ($stmt->execute()) {
                        $stmt->close();
                        redirectWithMessage("success", "Class code added successfully.");
                    }
                    $errors[] = "Unable to add the record: " . $stmt->error;
                    $stmt->close();
                }
            }
        }

        $editRecord = [
            "id" => $id,
            "course_code" => $courseCode,
            "description" => $description,
            "academic_year_semester_id" => $academicPeriodId,
            "status" => $status
        ];
    }
}

/* LOAD RECORD FOR EDITING */
if ($_SERVER["REQUEST_METHOD"] !== "POST" && isset($_GET["edit"])) {
    $editId = filter_var($_GET["edit"], FILTER_VALIDATE_INT);
    if ($editId && $editId > 0) {
        $stmt = $mysqli->prepare(
            "SELECT id, course_code, description, academic_year_semester_id, status
             FROM class_codes WHERE id = ? AND instructor_id = ? LIMIT 1"
        );
        if ($stmt) {
            $stmt->bind_param("ii", $editId, $currentInstructorId);
            $stmt->execute();
            $editRecord = $stmt->get_result()->fetch_assoc() ?: null;
            $stmt->close();
        }
    }
}

/* LOAD ACADEMIC PERIOD OPTIONS */
$academicPeriods = [];
$periodResult = $mysqli->query(
    "SELECT id, academic_year, semester, status
     FROM academic_year_semesters
     ORDER BY academic_year DESC,
       FIELD(semester, '1st Semester', '2nd Semester', 'Summer')"
);
if ($periodResult) {
    while ($period = $periodResult->fetch_assoc()) {
        $academicPeriods[] = $period;
    }
    $periodResult->free();
}

/* FETCH CLASS CODE RECORDS */
$records = [];
$totalRecords = 0;
$activeRecords = 0;
$inactiveRecords = 0;
$dbError = "";

$result = false;
if ($currentInstructorId > 0) {
    $listStmt = $mysqli->prepare(
        "SELECT cc.id, cc.course_code, cc.description, cc.academic_year_semester_id,
                cc.status, cc.created_at, ays.academic_year, ays.semester
         FROM class_codes cc
         INNER JOIN academic_year_semesters ays
            ON ays.id = cc.academic_year_semester_id
         WHERE cc.instructor_id = ?
         ORDER BY ays.academic_year DESC,
            FIELD(ays.semester, '1st Semester', '2nd Semester', 'Summer'),
            cc.course_code ASC, cc.description ASC"
    );
    if ($listStmt) {
        $listStmt->bind_param("i", $currentInstructorId);
        $listStmt->execute();
        $result = $listStmt->get_result();
    }
}

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
        $totalRecords++;
        if ($row["status"] === "Active") $activeRecords++;
        else $inactiveRecords++;
    }
    $result->free();
    if (isset($listStmt) && $listStmt) $listStmt->close();
} else {
    if (isset($listStmt) && $listStmt) $listStmt->close();
    $dbError = "Unable to load class codes. Please make sure the class_codes table has been created and linked to academic_year_semesters.";
}

include "globals/head.php";
?>

<body>
    <?php include "globals/sidebar.php"; ?>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php include "globals/topbar.php"; ?>

    <main class="main-content">
        <div class="content-wrapper class-code-page">
            <div class="page-heading">
                <div>
                    <div class="page-eyebrow"><i class="bi bi-grid-3x3-gap"></i> ACADEMIC SETTINGS</div>
                    <h2>Class Code Management</h2>
                    <p>Manage course codes, descriptions, and their assigned academic periods.</p>
                </div>
                <div class="heading-icon"><i class="bi bi-journal-code"></i></div>
            </div>

            <?php if ($flash): ?>
                <div class="page-alert alert-<?= e($flash["type"]) ?>" role="alert">
                    <i class="bi <?= $flash["type"] === "success" ? "bi-check-circle-fill" : "bi-exclamation-circle-fill" ?>"></i>
                    <span><?= e($flash["message"]) ?></span>
                    <button type="button" class="alert-close" aria-label="Close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            <?php endif; ?>

            <?php if ($dbError): ?>
                <div class="page-alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i><span><?= e($dbError) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!$academicPeriods): ?>
                <div class="page-alert alert-warning" role="alert">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Add an academic year and semester first. Only saved academic periods can be assigned to class codes.</span>
                </div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="page-alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div><strong>Please check the following:</strong>
                        <ul class="mb-0 mt-1">
                            <?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <section class="summary-grid" aria-label="Class code summary">
                <div class="summary-card">
                    <div class="summary-icon blue"><i class="bi bi-collection"></i></div>
                    <div><span>Total Class Codes</span><strong><?= $totalRecords ?></strong></div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon green"><i class="bi bi-check-circle"></i></div>
                    <div><span>Active</span><strong><?= $activeRecords ?></strong></div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon gray"><i class="bi bi-pause-circle"></i></div>
                    <div><span>Inactive</span><strong><?= $inactiveRecords ?></strong></div>
                </div>
            </section>

            <section class="management-card form-card">
                <div class="section-header">
                    <div class="section-heading-icon"><i class="bi bi-<?= $editRecord ? "pencil-square" : "plus-circle" ?>"></i></div>
                    <div>
                        <h5><?= $editRecord ? "Edit Class Code" : "Add Class Code" ?></h5>
                        <p><?= $editRecord ? "Update the selected class code and its details." : "Register a course code, describe its class, and assign an academic period." ?></p>
                    </div>
                </div>

                <form method="POST" action="class_code.php" class="class-code-form">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_class_code"]) ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?= e($editRecord["id"] ?? 0) ?>">

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="course_code">Course Code <span>*</span></label>
                            <div class="input-wrap">
                                <i class="bi bi-code-square"></i>
                                <input type="text" id="course_code" name="course_code" placeholder="e.g. CPE 101"
                                    maxlength="30" value="<?= e($editRecord["course_code"] ?? "") ?>" required>
                            </div>
                            <small>Enter the official course code.</small>
                        </div>

                        <div class="form-field">
                            <label for="description">Class Description <span>*</span></label>
                            <div class="input-wrap">
                                <i class="bi bi-card-text"></i>
                                <input type="text" id="description" name="description" placeholder="e.g. Computer Engineering - First Year"
                                    maxlength="255" value="<?= e($editRecord["description"] ?? "") ?>" required>
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="academic_year_semester_id">Academic Year &amp; Semester <span>*</span></label>
                            <div class="input-wrap">
                                <i class="bi bi-calendar2-week"></i>
                                <select id="academic_year_semester_id" name="academic_year_semester_id" required>
                                    <option value="">Select academic period</option>
                                    <?php foreach ($academicPeriods as $period): ?>
                                        <option value="<?= (int)$period["id"] ?>"
                                            <?= ((string)($editRecord["academic_year_semester_id"] ?? "") === (string)$period["id"]) ? "selected" : "" ?>>
                                            <?= e($period["academic_year"] . " • " . $period["semester"]) ?><?= $period["status"] !== "Active" ? " (Inactive)" : "" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-field">
                            <label for="status">Status <span>*</span></label>
                            <div class="input-wrap">
                                <i class="bi bi-toggle-on"></i>
                                <select id="status" name="status" required>
                                    <option value="Active" <?= (($editRecord["status"] ?? "Active") === "Active") ? "selected" : "" ?>>Active</option>
                                    <option value="Inactive" <?= (($editRecord["status"] ?? "") === "Inactive") ? "selected" : "" ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <?php if ($editRecord): ?>
                            <a href="class_code.php" class="btn-secondary-custom"><i class="bi bi-x-lg"></i> Cancel</a>
                        <?php endif; ?>
                        <button type="submit" class="btn-primary-custom" <?= !$academicPeriods ? "disabled" : "" ?>>
                            <i class="bi bi-<?= $editRecord ? "save" : "plus-lg" ?>"></i>
                            <?= $editRecord ? "Save Changes" : "Add Class Code" ?>
                        </button>
                    </div>
                </form>
            </section>

            <section class="management-card records-card">
                <div class="records-heading">
                    <div class="section-header">
                        <div class="section-heading-icon"><i class="bi bi-table"></i></div>
                        <div>
                            <h5>Class Code Records</h5>
                            <p>View, edit, and manage registered course codes and class descriptions.</p>
                        </div>
                    </div>
                    <div class="record-count"><i class="bi bi-collection"></i> <?= $totalRecords ?> Records</div>
                </div>

                <div class="table-tools">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="search" id="recordSearch" placeholder="Search course code, description, or academic period..." aria-label="Search class codes">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table class-code-table" id="classCodeTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course Code</th>
                                <th>Class Description</th>
                                <th>Academic Year</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Date Added</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($records): ?>
                                <?php foreach ($records as $index => $record): ?>
                                    <tr>
                                        <td class="row-number"><?= $index + 1 ?></td>
                                        <td>
                                            <div class="code-cell"><span class="code-icon"><i class="bi bi-code-square"></i></span><strong><?= e($record["course_code"]) ?></strong></div>
                                        </td>
                                        <td><span class="description-label"><?= e($record["description"]) ?></span></td>
                                        <td><?= e($record["academic_year"]) ?></td>
                                        <td><span class="semester-label"><?= e($record["semester"]) ?></span></td>
                                        <td><span class="status-pill <?= $record["status"] === "Active" ? "status-active" : "status-inactive" ?>"><span></span><?= e($record["status"]) ?></span></td>
                                        <td class="date-cell"><?= !empty($record["created_at"]) ? e(date("M d, Y", strtotime($record["created_at"]))) : "—" ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a class="action-btn edit-btn" href="class_code.php?edit=<?= (int)$record["id"] ?>" title="Edit record" aria-label="Edit record"><i class="bi bi-pencil-square"></i></a>
                                                <form method="POST" action="class_code.php" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this class code?');">
                                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_class_code"]) ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= (int)$record["id"] ?>">
                                                    <button type="submit" class="action-btn delete-btn" title="Delete record" aria-label="Delete record"><i class="bi bi-trash3"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="emptyRow">
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <div><i class="bi bi-journal-x"></i></div>
                                            <strong>No Class Codes Assigned to You</strong>
                                            <p>Class codes created under your instructor account will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="no-search-results" id="noSearchResults" hidden><i class="bi bi-search"></i><span>No matching class codes found.</span></div>
                </div>
            </section>
        </div>
    </main>

    <?php require_once "./globals/scripts.php"; ?>

    <style>
        .class-code-page {
            padding-bottom: 35px
        }

        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 24px
        }

        .page-eyebrow {
            color: var(--academic-indigo, var(--academic-blue-dark, #2446a8));
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.1px;
            margin-bottom: 7px
        }

        .page-eyebrow i {
            margin-right: 5px
        }

        .page-heading h2 {
            margin: 0;
            color: var(--text-color);
            font-size: 25px;
            font-weight: 750
        }

        .page-heading p {
            margin: 6px 0 0;
            color: var(--text-secondary);
            font-size: 14px
        }

        .heading-icon {
            width: 54px;
            height: 54px;
            flex: 0 0 54px;
            border-radius: 15px;
            display: grid;
            place-items: center;
            font-size: 23px;
            color: var(--academic-blue-dark, #2446a8);
            background: var(--activity-icon-blue-bg, #eaf0ff)
        }

        .page-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px 15px;
            border-radius: 11px;
            margin-bottom: 17px;
            font-size: 13px;
            border: 1px solid transparent
        }

        .page-alert>i {
            margin-top: 2px
        }

        .alert-success {
            background: #eaf8ef;
            color: #17683a;
            border-color: #ccebd7
        }

        .alert-danger {
            background: #fff0f0;
            color: #a52b2b;
            border-color: #f2d0d0
        }

        .alert-warning {
            background: #fff8e8;
            color: #8a5a08;
            border-color: #f1dfb6
        }

        .alert-close {
            margin-left: auto;
            border: 0;
            background: transparent;
            color: inherit;
            font-size: 21px;
            line-height: 1;
            cursor: pointer
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 20px
        }

        .summary-card {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 91px;
            padding: 17px 19px;
            border-radius: 14px;
            background: var(--activity-card-bg, var(--card-bg, #fff));
            border: 1px solid var(--activity-border, #e7eaf0);
            box-shadow: 0 4px 12px var(--shadow-color, rgba(20, 35, 70, .05))
        }

        .summary-icon {
            width: 45px;
            height: 45px;
            flex: 0 0 45px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 19px
        }

        .summary-icon.blue {
            background: #eaf0ff;
            color: #4165c5
        }

        .summary-icon.green {
            background: #e8f7ee;
            color: #249653
        }

        .summary-icon.gray {
            background: #eff1f4;
            color: #727b89
        }

        .summary-card span {
            display: block;
            color: var(--text-secondary);
            font-size: 12px;
            margin-bottom: 3px
        }

        .summary-card strong {
            display: block;
            color: var(--text-color);
            font-size: 23px;
            line-height: 1.2
        }

        .management-card {
            background: var(--activity-card-bg, var(--card-bg, #fff));
            border: 1px solid var(--activity-border, #e7eaf0);
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 14px var(--shadow-color, rgba(20, 35, 70, .05))
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .section-heading-icon {
            width: 41px;
            height: 41px;
            flex: 0 0 41px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            color: var(--academic-blue-dark, #3154b8);
            background: var(--activity-icon-blue-bg, #edf1ff);
            font-size: 17px
        }

        .section-header h5 {
            margin: 0;
            color: var(--text-color);
            font-size: 15px;
            font-weight: 750
        }

        .section-header p {
            margin: 4px 0 0;
            color: var(--text-secondary);
            font-size: 12px
        }

        .form-card {
            padding: 21px 22px 20px
        }

        .class-code-form {
            margin-top: 21px
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 17px
        }

        .form-field label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-size: 12px;
            font-weight: 700
        }

        .form-field label span {
            color: #dc5555
        }

        .input-wrap {
            position: relative
        }

        .input-wrap>i {
            position: absolute;
            z-index: 1;
            top: 50%;
            left: 13px;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 14px;
            pointer-events: none
        }

        .input-wrap input,
        .input-wrap select {
            width: 100%;
            min-height: 44px;
            padding: 10px 13px 10px 39px;
            border: 1px solid var(--activity-border, #dfe3eb);
            border-radius: 9px;
            background: var(--input-bg, var(--activity-card-bg, #fff));
            color: var(--text-color);
            font-size: 13px;
            outline: none;
            transition: border-color .2s, box-shadow .2s
        }

        .input-wrap input:focus,
        .input-wrap select:focus {
            border-color: var(--academic-blue-dark, #4968c4);
            box-shadow: 0 0 0 3px rgba(73, 104, 196, .12)
        }

        .form-field small {
            display: block;
            margin-top: 6px;
            color: var(--text-secondary);
            font-size: 11px
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 9px;
            margin-top: 20px;
            padding-top: 17px;
            border-top: 1px solid var(--activity-border, #e7eaf0)
        }

        .btn-primary-custom,
        .btn-secondary-custom {
            min-height: 40px;
            padding: 9px 16px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all .2s ease
        }

        .btn-primary-custom {
            border: 1px solid var(--academic-blue-dark, #3456b8);
            color: #fff;
            background: var(--academic-blue-dark, #3456b8)
        }

        .btn-primary-custom:hover {
            filter: brightness(.92);
            color: #fff;
            transform: translateY(-1px)
        }

        .btn-primary-custom:disabled {
            opacity: .55;
            cursor: not-allowed;
            transform: none
        }

        .btn-secondary-custom {
            border: 1px solid var(--activity-border, #dfe3eb);
            color: var(--text-color);
            background: var(--activity-card-bg, #fff)
        }

        .btn-secondary-custom:hover {
            background: var(--activity-progress-bg, #f3f5f9);
            color: var(--text-color)
        }

        .records-card {
            padding: 20px 0 0
        }

        .records-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 0 21px 17px
        }

        .record-count {
            white-space: nowrap;
            padding: 7px 11px;
            border-radius: 20px;
            background: var(--activity-progress-bg, #f1f3f8);
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 700
        }

        .record-count i {
            margin-right: 4px
        }

        .table-tools {
            display: flex;
            justify-content: flex-end;
            padding: 0 21px 14px
        }

        .search-box {
            width: min(100%, 380px);
            position: relative
        }

        .search-box i {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 13px
        }

        .search-box input {
            width: 100%;
            height: 39px;
            padding: 8px 12px 8px 35px;
            border: 1px solid var(--activity-border, #dfe3eb);
            border-radius: 9px;
            background: var(--input-bg, var(--activity-card-bg, #fff));
            color: var(--text-color);
            font-size: 12px;
            outline: none
        }

        .search-box input:focus {
            border-color: var(--academic-blue-dark, #4968c4)
        }

        .class-code-table {
            margin: 0;
            min-width: 900px;
            color: var(--text-color)
        }

        .class-code-table thead th {
            padding: 12px 16px;
            white-space: nowrap;
            border-top: 1px solid var(--activity-border, #e7eaf0);
            border-bottom: 1px solid var(--activity-border, #e7eaf0);
            background: var(--activity-progress-bg, #f6f7fa);
            color: var(--text-secondary);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .55px;
            text-transform: uppercase
        }

        .class-code-table tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--activity-border, #edf0f4);
            background: transparent;
            font-size: 12px
        }

        .class-code-table tbody tr:last-child td {
            border-bottom: 0
        }

        .class-code-table tbody tr:hover td {
            background: var(--activity-progress-bg, #fafbfe)
        }

        .row-number {
            color: var(--text-secondary);
            width: 55px
        }

        .code-cell {
            display: flex;
            align-items: center;
            gap: 9px
        }

        .code-cell strong {
            color: var(--text-color);
            font-size: 13px
        }

        .code-icon {
            width: 31px;
            height: 31px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: var(--activity-icon-blue-bg, #edf1ff);
            color: var(--academic-blue-dark, #3456b8)
        }

        .description-label,
        .semester-label {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 7px;
            background: var(--activity-progress-bg, #f1f3f8);
            color: var(--text-color);
            font-size: 11px;
            font-weight: 650
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 750
        }

        .status-pill>span {
            width: 6px;
            height: 6px;
            border-radius: 50%
        }

        .status-active {
            color: #20834b;
            background: #e8f7ee
        }

        .status-active>span {
            background: #2aaf63
        }

        .status-inactive {
            color: #737b87;
            background: #eff1f4
        }

        .status-inactive>span {
            background: #89919c
        }

        .date-cell {
            color: var(--text-secondary) !important;
            white-space: nowrap
        }

        .action-buttons {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 7px
        }

        .delete-form {
            display: inline-flex;
            margin: 0
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer;
            transition: all .18s ease
        }

        .edit-btn {
            color: #4165c5;
            background: #edf1ff
        }

        .edit-btn:hover {
            color: #fff;
            background: #4165c5
        }

        .delete-btn {
            color: #d14c4c;
            background: #fff0f0
        }

        .delete-btn:hover {
            color: #fff;
            background: #d14c4c
        }

        .empty-state {
            min-height: 190px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 25px
        }

        .empty-state>div {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: var(--activity-progress-bg, #f1f3f8);
            color: var(--text-secondary);
            font-size: 21px;
            margin-bottom: 11px
        }

        .empty-state strong {
            color: var(--text-color);
            font-size: 13px
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 12px;
            margin: 5px 0 0
        }

        .no-search-results {
            padding: 25px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 12px
        }

        .no-search-results i {
            margin-right: 6px
        }

        @media(max-width:767px) {
            .page-heading h2 {
                font-size: 21px
            }

            .summary-grid {
                grid-template-columns: 1fr;
                gap: 10px
            }

            .summary-card {
                min-height: 76px
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 13px
            }

            .form-card {
                padding: 17px 15px
            }

            .records-heading {
                align-items: flex-start;
                flex-direction: column;
                padding: 0 15px 15px
            }

            .table-tools {
                padding: 0 15px 13px
            }

            .search-box {
                width: 100%
            }

            .form-actions {
                flex-wrap: wrap
            }

            .form-actions>* {
                flex: 1
            }

            .heading-icon {
                width: 45px;
                height: 45px;
                flex-basis: 45px
            }
        }
    </style>

    <script>
        (() => {
            const searchInput = document.getElementById("recordSearch");
            const table = document.getElementById("classCodeTable");
            const noResults = document.getElementById("noSearchResults");
            if (!searchInput || !table || !noResults) return;

            searchInput.addEventListener("input", () => {
                const query = searchInput.value.trim().toLowerCase();
                const rows = Array.from(table.querySelectorAll("tbody tr")).filter(row => row.id !== "emptyRow");
                let visibleCount = 0;
                rows.forEach(row => {
                    const matches = row.textContent.toLowerCase().includes(query);
                    row.hidden = !matches;
                    if (matches) visibleCount++;
                });
                noResults.hidden = visibleCount > 0 || rows.length === 0 || query === "";
            });
        })();
    </script>
</body>