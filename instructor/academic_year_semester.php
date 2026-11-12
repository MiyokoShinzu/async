<?php
/* =========================================================
   ACADEMIC YEAR & SEMESTER MANAGEMENT
   ETS-Async Learning Portal

   Expected database table:
   academic_year_semesters
   ---------------------------------------------------------
   id            INT PRIMARY KEY AUTO_INCREMENT
   academic_year VARCHAR(20) NOT NULL
   semester      VARCHAR(30) NOT NULL
   status        ENUM('Active','Inactive') DEFAULT 'Active'
   created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                 ON UPDATE CURRENT_TIMESTAMP

   Recommended unique key: (academic_year, semester)
   This page does not create or alter database tables.
   ========================================================= */

session_start();

include "globals/checks.php";

require_once "../src/connection.php";

if (empty($_SESSION["csrf_academic_year_semester"])) {
    $_SESSION["csrf_academic_year_semester"] = bin2hex(random_bytes(32));
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function redirectWithMessage(string $type, string $message): void
{
    $_SESSION["academic_year_semester_flash"] = [
        "type" => $type,
        "message" => $message
    ];
    header("Location: academic_year_semester.php");
    exit;
}

$flash = $_SESSION["academic_year_semester_flash"] ?? null;
unset($_SESSION["academic_year_semester_flash"]);

$errors = [];
$editRecord = null;

/* ---------------------------------------------------------
   CREATE / UPDATE / DELETE
--------------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_academic_year_semester"], $token)) {
        redirectWithMessage("danger", "Your session token is invalid. Please try again.");
    }

    $action = $_POST["action"] ?? "";

    if ($action === "delete") {
        $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            redirectWithMessage("danger", "Invalid academic year record.");
        }

        $stmt = $mysqli->prepare("DELETE FROM academic_year_semesters WHERE id = ?");
        if (!$stmt) {
            redirectWithMessage("danger", "Unable to prepare the delete request. Check the database table.");
        }

        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $deleted = $stmt->affected_rows;
        $stmt->close();

        if ($ok && $deleted > 0) {
            redirectWithMessage("success", "Academic year and semester deleted successfully.");
        }

        redirectWithMessage("warning", "The record was not found or could not be deleted.");
    }

    if ($action === "save") {
        $id = (int)($_POST["id"] ?? 0);
        $academicYear = trim($_POST["academic_year"] ?? "");
        $semester = trim($_POST["semester"] ?? "");
        $status = trim($_POST["status"] ?? "Active");

        if (!preg_match('/^\d{4}\s*-\s*\d{4}$/', $academicYear)) {
            $errors[] = "Enter a valid academic year, for example 2026-2027.";
        } else {
            $years = preg_split('/\s*-\s*/', $academicYear);
            if ((int)$years[1] !== (int)$years[0] + 1) {
                $errors[] = "The ending year must be exactly one year after the starting year.";
            } else {
                $academicYear = $years[0] . "-" . $years[1];
            }
        }

        $allowedSemesters = ["1st Semester", "2nd Semester", "Summer"];
        if (!in_array($semester, $allowedSemesters, true)) {
            $errors[] = "Please select a valid semester.";
        }

        if (!in_array($status, ["Active", "Inactive"], true)) {
            $errors[] = "Please select a valid status.";
        }

        if (!$errors) {
            $check = $mysqli->prepare(
                "SELECT id FROM academic_year_semesters
                 WHERE academic_year = ? AND semester = ? AND id <> ? LIMIT 1"
            );

            if (!$check) {
                $errors[] = "Unable to validate the record. Check that the academic_year_semesters table exists.";
            } else {
                $check->bind_param("ssi", $academicYear, $semester, $id);
                $check->execute();
                $duplicate = $check->get_result()->num_rows > 0;
                $check->close();

                if ($duplicate) {
                    $errors[] = "This academic year and semester already exist.";
                }
            }
        }

        if (!$errors) {
            if ($id > 0) {
                $stmt = $mysqli->prepare(
                    "UPDATE academic_year_semesters
                     SET academic_year = ?, semester = ?, status = ?
                     WHERE id = ?"
                );

                if (!$stmt) {
                    $errors[] = "Unable to prepare the update. Check the database table.";
                } else {
                    $stmt->bind_param("sssi", $academicYear, $semester, $status, $id);
                    if ($stmt->execute()) {
                        $stmt->close();
                        redirectWithMessage("success", "Academic year and semester updated successfully.");
                    }
                    $errors[] = "Unable to update the record: " . $stmt->error;
                    $stmt->close();
                }
            } else {
                $stmt = $mysqli->prepare(
                    "INSERT INTO academic_year_semesters (academic_year, semester, status)
                     VALUES (?, ?, ?)"
                );

                if (!$stmt) {
                    $errors[] = "Unable to prepare the insert. Check the database table.";
                } else {
                    $stmt->bind_param("sss", $academicYear, $semester, $status);
                    if ($stmt->execute()) {
                        $stmt->close();
                        redirectWithMessage("success", "Academic year and semester added successfully.");
                    }
                    $errors[] = "Unable to add the record: " . $stmt->error;
                    $stmt->close();
                }
            }
        }

        $editRecord = [
            "id" => $id,
            "academic_year" => $academicYear,
            "semester" => $semester,
            "status" => $status
        ];
    }
}

/* ---------------------------------------------------------
   LOAD RECORD FOR EDITING
--------------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] !== "POST" && isset($_GET["edit"])) {
    $editId = filter_var($_GET["edit"], FILTER_VALIDATE_INT);

    if ($editId && $editId > 0) {
        $stmt = $mysqli->prepare(
            "SELECT id, academic_year, semester, status
             FROM academic_year_semesters WHERE id = ? LIMIT 1"
        );

        if ($stmt) {
            $stmt->bind_param("i", $editId);
            $stmt->execute();
            $editRecord = $stmt->get_result()->fetch_assoc() ?: null;
            $stmt->close();
        }
    }
}

/* ---------------------------------------------------------
   FETCH RECORDS AND SUMMARY
--------------------------------------------------------- */
$records = [];
$totalRecords = 0;
$activeRecords = 0;
$inactiveRecords = 0;
$dbError = "";

$result = $mysqli->query(
    "SELECT id, academic_year, semester, status, created_at
     FROM academic_year_semesters
     ORDER BY academic_year DESC,
       FIELD(semester, '1st Semester', '2nd Semester', 'Summer'),
       id DESC"
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
        $totalRecords++;
        if ($row["status"] === "Active") {
            $activeRecords++;
        } else {
            $inactiveRecords++;
        }
    }
    $result->free();
} else {
    $dbError = "Unable to load records. Please make sure the academic_year_semesters table has been created.";
}

include "globals/head.php";
?>

<body>
    <?php include "globals/sidebar.php"; ?>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <?php include "globals/topbar.php"; ?>

    <main class="main-content">
        <div class="content-wrapper academic-year-page">

            <div class="page-heading">
                <div>
                    <div class="page-eyebrow"><i class="bi bi-calendar2-week"></i> ACADEMIC SETTINGS</div>
                    <h2>Academic Year &amp; Semester</h2>
                    <p>Manage the academic years and semesters available in the learning portal.</p>
                </div>
                <div class="heading-icon"><i class="bi bi-calendar-range"></i></div>
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
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span><?= e($dbError) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($errors): ?>
                <div class="page-alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>Please check the following:</strong>
                        <ul class="mb-0 mt-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <!-- SUMMARY CARDS -->
            <section class="summary-grid" aria-label="Academic year summary">
                <div class="summary-card">
                    <div class="summary-icon blue"><i class="bi bi-calendar3"></i></div>
                    <div><span>Total Records</span><strong><?= $totalRecords ?></strong></div>
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

            <!-- ADD / EDIT FORM -->
            <section class="management-card form-card">
                <div class="section-header">
                    <div class="section-heading-icon"><i class="bi bi-<?= $editRecord ? "pencil-square" : "plus-circle" ?>"></i></div>
                    <div>
                        <h5><?= $editRecord ? "Edit Academic Year & Semester" : "Add Academic Year & Semester" ?></h5>
                        <p><?= $editRecord ? "Update the selected academic period." : "Create a new academic period for the learning portal." ?></p>
                    </div>
                </div>

                <form method="POST" action="academic_year_semester.php" class="academic-form">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_academic_year_semester"]) ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" value="<?= e($editRecord["id"] ?? 0) ?>">

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="academic_year">Academic Year <span>*</span></label>
                            <div class="input-wrap">
                                <i class="bi bi-calendar-event"></i>
                                <input
                                    type="text"
                                    id="academic_year"
                                    name="academic_year"
                                    placeholder="e.g. 2026-2027"
                                    pattern="[0-9]{4}\s*-\s*[0-9]{4}"
                                    maxlength="11"
                                    value="<?= e($editRecord["academic_year"] ?? "") ?>"
                                    required>
                            </div>
                            <small>Use the format YYYY-YYYY.</small>
                        </div>

                        <div class="form-field">
                            <label for="semester">Semester <span>*</span></label>
                            <div class="input-wrap">
                                <i class="bi bi-journal-bookmark"></i>
                                <select id="semester" name="semester" required>
                                    <option value="">Select semester</option>
                                    <?php foreach (["1st Semester", "2nd Semester", "Summer"] as $option): ?>
                                        <option value="<?= e($option) ?>" <?= (($editRecord["semester"] ?? "") === $option) ? "selected" : "" ?>>
                                            <?= e($option) ?>
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
                            <a href="academic_year_semester.php" class="btn-secondary-custom">
                                <i class="bi bi-x-lg"></i> Cancel
                            </a>
                        <?php endif; ?>
                        <button type="submit" class="btn-primary-custom">
                            <i class="bi bi-<?= $editRecord ? "save" : "plus-lg" ?>"></i>
                            <?= $editRecord ? "Save Changes" : "Add Academic Period" ?>
                        </button>
                    </div>
                </form>
            </section>

            <!-- RECORDS TABLE -->
            <section class="management-card records-card">
                <div class="records-heading">
                    <div class="section-header">
                        <div class="section-heading-icon"><i class="bi bi-table"></i></div>
                        <div>
                            <h5>Academic Year Records</h5>
                            <p>View, edit, and manage the academic periods in the system.</p>
                        </div>
                    </div>
                    <div class="record-count"><i class="bi bi-collection"></i> <?= $totalRecords ?> Records</div>
                </div>

                <div class="table-tools">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="search" id="recordSearch" placeholder="Search academic year or semester..." aria-label="Search records">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table academic-table" id="academicTable">
                        <thead>
                            <tr>
                                <th>#</th>
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
                                            <div class="year-cell">
                                                <span class="year-icon"><i class="bi bi-calendar2-week"></i></span>
                                                <strong><?= e($record["academic_year"]) ?></strong>
                                            </div>
                                        </td>
                                        <td><span class="semester-label"><?= e($record["semester"]) ?></span></td>
                                        <td>
                                            <span class="status-pill <?= $record["status"] === "Active" ? "status-active" : "status-inactive" ?>">
                                                <span></span><?= e($record["status"]) ?>
                                            </span>
                                        </td>
                                        <td class="date-cell">
                                            <?= !empty($record["created_at"]) ? e(date("M d, Y", strtotime($record["created_at"]))) : "—" ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a class="action-btn edit-btn" href="academic_year_semester.php?edit=<?= (int)$record["id"] ?>" title="Edit record" aria-label="Edit record">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form method="POST" action="academic_year_semester.php" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this academic year and semester?');">
                                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_academic_year_semester"]) ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= (int)$record["id"] ?>">
                                                    <button type="submit" class="action-btn delete-btn" title="Delete record" aria-label="Delete record">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="emptyRow">
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <div><i class="bi bi-calendar2-x"></i></div>
                                            <strong>No Academic Periods Yet</strong>
                                            <p>Add an academic year and semester using the form above.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <div class="no-search-results" id="noSearchResults" hidden>
                        <i class="bi bi-search"></i>
                        <span>No matching academic periods found.</span>
                    </div>
                </div>
            </section>

        </div>
    </main>

    <?php require_once "./globals/scripts.php"; ?>

    <style>
        .academic-year-page {
            padding-bottom: 35px;
        }

        .page-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 24px;
        }

        .page-eyebrow {
            color: var(--academic-indigo, var(--academic-blue-dark, #2446a8));
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.1px;
            margin-bottom: 7px;
        }

        .page-eyebrow i {
            margin-right: 5px;
        }

        .page-heading h2 {
            margin: 0;
            color: var(--text-color);
            font-size: 25px;
            font-weight: 750;
        }

        .page-heading p {
            margin: 6px 0 0;
            color: var(--text-secondary);
            font-size: 14px;
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
            background: var(--activity-icon-blue-bg, #eaf0ff);
        }

        .page-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 13px 15px;
            border-radius: 11px;
            margin-bottom: 17px;
            font-size: 13px;
            border: 1px solid transparent;
        }

        .page-alert>i {
            margin-top: 2px;
        }

        .alert-success {
            background: #eaf8ef;
            color: #17683a;
            border-color: #ccebd7;
        }

        .alert-danger {
            background: #fff0f0;
            color: #a52b2b;
            border-color: #f2d0d0;
        }

        .alert-warning {
            background: #fff8e8;
            color: #8a5a08;
            border-color: #f1dfb6;
        }

        .alert-close {
            margin-left: auto;
            border: 0;
            background: transparent;
            color: inherit;
            font-size: 21px;
            line-height: 1;
            cursor: pointer;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
            margin-bottom: 20px;
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
            box-shadow: 0 4px 12px var(--shadow-color, rgba(20, 35, 70, .05));
        }

        .summary-icon {
            width: 45px;
            height: 45px;
            flex: 0 0 45px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 19px;
        }

        .summary-icon.blue {
            background: #eaf0ff;
            color: #4165c5;
        }

        .summary-icon.green {
            background: #e8f7ee;
            color: #249653;
        }

        .summary-icon.gray {
            background: #eff1f4;
            color: #727b89;
        }

        .summary-card span {
            display: block;
            color: var(--text-secondary);
            font-size: 12px;
            margin-bottom: 3px;
        }

        .summary-card strong {
            display: block;
            color: var(--text-color);
            font-size: 23px;
            line-height: 1.2;
        }

        .management-card {
            background: var(--activity-card-bg, var(--card-bg, #fff));
            border: 1px solid var(--activity-border, #e7eaf0);
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 14px var(--shadow-color, rgba(20, 35, 70, .05));
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
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
            font-size: 17px;
        }

        .section-header h5 {
            margin: 0;
            color: var(--text-color);
            font-size: 15px;
            font-weight: 750;
        }

        .section-header p {
            margin: 4px 0 0;
            color: var(--text-secondary);
            font-size: 12px;
        }

        .form-card {
            padding: 21px 22px 20px;
        }

        .academic-form {
            margin-top: 21px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 17px;
        }

        .form-field label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-size: 12px;
            font-weight: 700;
        }

        .form-field label span {
            color: #dc5555;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap>i {
            position: absolute;
            z-index: 1;
            top: 50%;
            left: 13px;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 14px;
            pointer-events: none;
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
            transition: border-color .2s, box-shadow .2s;
        }

        .input-wrap input:focus,
        .input-wrap select:focus {
            border-color: var(--academic-blue-dark, #4968c4);
            box-shadow: 0 0 0 3px rgba(73, 104, 196, .12);
        }

        .form-field small {
            display: block;
            margin-top: 6px;
            color: var(--text-secondary);
            font-size: 11px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 9px;
            margin-top: 20px;
            padding-top: 17px;
            border-top: 1px solid var(--activity-border, #e7eaf0);
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
            transition: all .2s ease;
        }

        .btn-primary-custom {
            border: 1px solid var(--academic-blue-dark, #3456b8);
            color: #fff;
            background: var(--academic-blue-dark, #3456b8);
        }

        .btn-primary-custom:hover {
            filter: brightness(.92);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-secondary-custom {
            border: 1px solid var(--activity-border, #dfe3eb);
            color: var(--text-color);
            background: var(--activity-card-bg, #fff);
        }

        .btn-secondary-custom:hover {
            background: var(--activity-progress-bg, #f3f5f9);
            color: var(--text-color);
        }

        .records-card {
            padding: 20px 0 0;
        }

        .records-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 0 21px 17px;
        }

        .record-count {
            white-space: nowrap;
            padding: 7px 11px;
            border-radius: 20px;
            background: var(--activity-progress-bg, #f1f3f8);
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 700;
        }

        .record-count i {
            margin-right: 4px;
        }

        .table-tools {
            display: flex;
            justify-content: flex-end;
            padding: 0 21px 14px;
        }

        .search-box {
            width: min(100%, 340px);
            position: relative;
        }

        .search-box i {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 13px;
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
            outline: none;
        }

        .search-box input:focus {
            border-color: var(--academic-blue-dark, #4968c4);
        }

        .academic-table {
            margin: 0;
            min-width: 720px;
            color: var(--text-color);
        }

        .academic-table thead th {
            padding: 12px 16px;
            white-space: nowrap;
            border-top: 1px solid var(--activity-border, #e7eaf0);
            border-bottom: 1px solid var(--activity-border, #e7eaf0);
            background: var(--activity-progress-bg, #f6f7fa);
            color: var(--text-secondary);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .55px;
            text-transform: uppercase;
        }

        .academic-table tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--activity-border, #edf0f4);
            background: transparent;
            font-size: 12px;
        }

        .academic-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .academic-table tbody tr:hover td {
            background: var(--activity-progress-bg, #fafbfe);
        }

        .row-number {
            color: var(--text-secondary);
            width: 55px;
        }

        .year-cell {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .year-cell strong {
            color: var(--text-color);
            font-size: 13px;
        }

        .year-icon {
            width: 31px;
            height: 31px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: var(--activity-icon-blue-bg, #edf1ff);
            color: var(--academic-blue-dark, #3456b8);
        }

        .semester-label {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 7px;
            background: var(--activity-progress-bg, #f1f3f8);
            color: var(--text-color);
            font-size: 11px;
            font-weight: 650;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 750;
        }

        .status-pill>span {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .status-active {
            color: #20834b;
            background: #e8f7ee;
        }

        .status-active>span {
            background: #2aaf63;
        }

        .status-inactive {
            color: #737b87;
            background: #eff1f4;
        }

        .status-inactive>span {
            background: #89919c;
        }

        .date-cell {
            color: var(--text-secondary) !important;
            white-space: nowrap;
        }

        .action-buttons {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 7px;
        }

        .delete-form {
            display: inline-flex;
            margin: 0;
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
            transition: all .18s ease;
        }

        .edit-btn {
            color: #4165c5;
            background: #edf1ff;
        }

        .edit-btn:hover {
            color: #fff;
            background: #4165c5;
        }

        .delete-btn {
            color: #d14c4c;
            background: #fff0f0;
        }

        .delete-btn:hover {
            color: #fff;
            background: #d14c4c;
        }

        .empty-state {
            min-height: 190px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 25px;
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
            margin-bottom: 11px;
        }

        .empty-state strong {
            color: var(--text-color);
            font-size: 13px;
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 12px;
            margin: 5px 0 0;
        }

        .no-search-results {
            padding: 25px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 12px;
        }

        .no-search-results i {
            margin-right: 6px;
        }

        @media (max-width:767px) {
            .page-heading h2 {
                font-size: 21px;
            }

            .summary-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .summary-card {
                min-height: 76px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 13px;
            }

            .form-card {
                padding: 17px 15px;
            }

            .records-heading {
                align-items: flex-start;
                flex-direction: column;
                padding: 0 15px 15px;
            }

            .table-tools {
                padding: 0 15px 13px;
            }

            .search-box {
                width: 100%;
            }

            .form-actions {
                flex-wrap: wrap;
            }

            .form-actions>* {
                flex: 1;
            }

            .heading-icon {
                width: 45px;
                height: 45px;
                flex-basis: 45px;
            }
        }
    </style>

    <script>
        (() => {
            const searchInput = document.getElementById("recordSearch");
            const table = document.getElementById("academicTable");
            const noResults = document.getElementById("noSearchResults");

            if (!searchInput || !table || !noResults) return;

            searchInput.addEventListener("input", () => {
                const query = searchInput.value.trim().toLowerCase();
                const rows = Array.from(table.querySelectorAll("tbody tr"))
                    .filter(row => row.id !== "emptyRow");
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