<?php
session_start();
include "globals/checks.php";
require_once "../src/connection.php";

$currentInstructorId = (int)($_SESSION["user_id"] ?? $_SESSION["id"] ?? $_SESSION["instructor_id"] ?? 0);

if (empty($_SESSION["csrf_enrollment"])) {
    $_SESSION["csrf_enrollment"] = bin2hex(random_bytes(32));
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function studentPhotoUrl(?string $photo): string
{
    $photo = trim((string)$photo);
    if ($photo === "") return "";

    if (preg_match('/^https?:\/\//i', $photo)) {
        if (stripos($photo, "async.vertigation.com") !== false) {
            $path = parse_url($photo, PHP_URL_PATH);
            return (is_string($path) && $path !== "") ? "https://vertigation.com" . $path : "";
        }
        return $photo;
    }
    if (strpos($photo, "/shared/uploads/profile_photos/") === 0) {
        return "https://vertigation.com" . $photo;
    }
    if (strpos($photo, "shared/uploads/profile_photos/") === 0) {
        return "https://vertigation.com/" . $photo;
    }
    if (strpos($photo, "async/shared/uploads/profile_photos/") === 0) {
        return "https://vertigation.com/" . substr($photo, strlen("async/"));
    }
    return "https://vertigation.com/" . ltrim($photo, "/");
}

function enrollmentRedirect(string $type, string $message): void
{
    $_SESSION["enrollment_flash"] = ["type" => $type, "message" => $message];
    header("Location: enrollment.php");
    exit;
}

$flash = $_SESSION["enrollment_flash"] ?? null;
unset($_SESSION["enrollment_flash"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";
    if (!hash_equals($_SESSION["csrf_enrollment"], $token)) {
        enrollmentRedirect("danger", "Your session token is invalid. Please try again.");
    }

    $action = $_POST["action"] ?? "";
    $newStatus = trim((string)($_POST["enrollment_status"] ?? ""));
    $allowedStatuses = ["Accepted", "Denied", "On Hold"];

    if ($action !== "bulk_update" || !in_array($newStatus, $allowedStatuses, true)) {
        enrollmentRedirect("danger", "Please select a valid bulk enrollment action.");
    }

    $ids = $_POST["enrollment_ids"] ?? [];
    if (!is_array($ids)) $ids = [];
    $ids = array_values(array_unique(array_filter(array_map(
        static fn($id) => filter_var($id, FILTER_VALIDATE_INT),
        $ids
    ), static fn($id) => $id !== false && $id > 0)));

    if (!$ids) {
        enrollmentRedirect("warning", "Select at least one student before applying a bulk action.");
    }

    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $types = "sii" . str_repeat("i", count($ids));
    $sql = "UPDATE class_code_enrollments cce
            INNER JOIN class_codes cc ON cc.id = cce.class_code_id
            SET cce.status = ?, cce.reviewed_at = NOW(), cce.reviewed_by = ?
            WHERE cc.instructor_id = ? AND cce.id IN ($placeholders)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        enrollmentRedirect("danger", "Unable to prepare the bulk update. Check your database setup.");
    }

    $params = array_merge([$newStatus, $currentInstructorId, $currentInstructorId], $ids);
    $stmt->bind_param($types, ...$params);
    $ok = $stmt->execute();
    $changed = $stmt->affected_rows;
    $stmt->close();

    if ($ok) {
        enrollmentRedirect("success", $changed . " enrollment(s) updated to " . $newStatus . ".");
    }
    enrollmentRedirect("danger", "The bulk status update failed.");
}

/* Load all student enrollments under the logged-in instructor's class codes. */
$studentEnrollments = [];
$enrollmentLoadError = "";

if ($currentInstructorId > 0) {
    $stmt = $mysqli->prepare(
        "SELECT cce.id AS enrollment_id, cce.student_id,
                cce.status AS enrollment_status, cce.enrolled_at, cce.reviewed_at,
                cc.id AS class_code_id, cc.course_code, cc.description,
                ays.academic_year, ays.semester,
                a.first_name, a.last_name, a.middle_initial, a.extension_name, a.profile_photo
         FROM class_code_enrollments cce
         INNER JOIN class_codes cc ON cc.id = cce.class_code_id
         INNER JOIN academic_year_semesters ays ON ays.id = cc.academic_year_semester_id
         LEFT JOIN accounts a ON a.student_id = cce.student_id
         WHERE cc.instructor_id = ?
         ORDER BY CASE cce.status
             WHEN 'Pending' THEN 0 WHEN 'On Hold' THEN 1
             WHEN 'Accepted' THEN 2 WHEN 'Denied' THEN 3 ELSE 4
         END, cce.enrolled_at DESC"
    );

    if ($stmt) {
        $stmt->bind_param("i", $currentInstructorId);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) $studentEnrollments[] = $row;
            $result->free();
        } else {
            $enrollmentLoadError = "Unable to load student enrollments.";
        }
        $stmt->close();
    } else {
        $enrollmentLoadError = "Unable to prepare the enrollment list. Confirm that the enrollment status migration has been applied.";
    }
} else {
    $enrollmentLoadError = "Your instructor account ID could not be detected.";
}

$classCodeOptions = [];
$academicPeriodOptions = [];
foreach ($studentEnrollments as $row) {
    $classCodeOptions[(string)$row["class_code_id"]] = (string)$row["course_code"];
    $periodKey = (string)$row["academic_year"] . "|" . (string)$row["semester"];
    $academicPeriodOptions[$periodKey] = (string)$row["academic_year"] . " • " . (string)$row["semester"];
}
asort($classCodeOptions, SORT_NATURAL | SORT_FLAG_CASE);
asort($academicPeriodOptions, SORT_NATURAL | SORT_FLAG_CASE);

$statusCounts = ["Pending" => 0, "Accepted" => 0, "On Hold" => 0, "Denied" => 0];
foreach ($studentEnrollments as $row) {
    if (isset($statusCounts[$row["enrollment_status"]])) $statusCounts[$row["enrollment_status"]]++;
}

include "globals/head.php";
?>

<body>
    <?php include "globals/sidebar.php"; ?>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php include "globals/topbar.php"; ?>

    <main class="main-content">
        <div class="content-wrapper enrollment-page">
            <div class="page-heading">
                <div>
                    <div class="page-eyebrow"><i class="bi bi-people-fill"></i> CLASS MANAGEMENT</div>
                    <h2>Student Enrollment</h2>
                    <p>View student profiles and manage enrollment statuses using bulk actions and filters.</p>
                </div>
                <div class="heading-icon"><i class="bi bi-person-check"></i></div>
            </div>

            <?php if ($flash): ?>
                <div class="page-alert alert-<?= e($flash["type"]) ?>" role="alert">
                    <i class="bi <?= $flash["type"] === "success" ? "bi-check-circle-fill" : "bi-exclamation-circle-fill" ?>"></i>
                    <span><?= e($flash["message"]) ?></span>
                    <button type="button" class="alert-close" aria-label="Close" onclick="this.parentElement.remove()">&times;</button>
                </div>
            <?php endif; ?>

            <?php if ($enrollmentLoadError): ?>
                <div class="page-alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i><span><?= e($enrollmentLoadError) ?></span></div>
            <?php endif; ?>

            <section class="summary-grid">
                <div class="summary-card">
                    <div class="summary-icon blue"><i class="bi bi-people"></i></div>
                    <div><span>Total Enrolled</span><strong><?= count($studentEnrollments) ?></strong></div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon amber"><i class="bi bi-hourglass-split"></i></div>
                    <div><span>Pending</span><strong><?= $statusCounts["Pending"] ?></strong></div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon green"><i class="bi bi-check-circle"></i></div>
                    <div><span>Accepted</span><strong><?= $statusCounts["Accepted"] ?></strong></div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon purple"><i class="bi bi-pause-circle"></i></div>
                    <div><span>On Hold</span><strong><?= $statusCounts["On Hold"] ?></strong></div>
                </div>
                <div class="summary-card">
                    <div class="summary-icon red"><i class="bi bi-x-circle"></i></div>
                    <div><span>Denied</span><strong><?= $statusCounts["Denied"] ?></strong></div>
                </div>
            </section>

            <section class="management-card records-card">
                <div class="records-heading">
                    <div class="section-header">
                        <div class="section-heading-icon"><i class="bi bi-person-lines-fill"></i></div>
                        <div>
                            <h5>Enrolled Students</h5>
                            <p>Select multiple students and apply one enrollment status to all selected records.</p>
                        </div>
                    </div>
                    <div class="record-count"><i class="bi bi-people"></i> <?= count($studentEnrollments) ?> Students</div>
                </div>

                <div class="table-tools enrollment-tools">
                    <div class="search-box"><i class="bi bi-search"></i><input type="search" id="enrollmentSearch" placeholder="Search student name or ID..." aria-label="Search enrollments"></div>
                    <select id="classCodeFilter" class="status-filter" aria-label="Filter by class code">
                        <option value="">All class codes</option>
                        <?php foreach ($classCodeOptions as $classId => $courseCode): ?>
                            <option value="<?= e($classId) ?>"><?= e($courseCode) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="periodFilter" class="status-filter" aria-label="Filter by academic period">
                        <option value="">All academic periods</option>
                        <?php foreach ($academicPeriodOptions as $periodKey => $periodLabel): ?>
                            <option value="<?= e($periodKey) ?>"><?= e($periodLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="statusFilter" class="status-filter" aria-label="Filter by status">
                        <option value="">All statuses</option>
                        <option>Pending</option>
                        <option>Accepted</option>
                        <option>On Hold</option>
                        <option>Denied</option>
                    </select>
                </div>

                <?php if ($studentEnrollments): ?>
                    <form method="POST" id="bulkEnrollmentForm" onsubmit="return confirmBulkUpdate();">
                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_enrollment"]) ?>">
                        <input type="hidden" name="action" value="bulk_update">
                        <div class="bulk-toolbar">
                            <label class="select-all-label"><input type="checkbox" id="selectAll"> Select visible</label>
                            <span id="selectedCount">0 selected</span>
                            <div class="bulk-controls">
                                <select name="enrollment_status" id="bulkStatus" required>
                                    <option value="">Choose status</option>
                                    <option value="Accepted">Accept</option>
                                    <option value="Denied">Deny</option>
                                    <option value="On Hold">Place on hold</option>
                                </select>
                                <button type="submit" class="bulk-apply-btn"><i class="bi bi-check2-square"></i> Apply to Selected</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table enrollment-table" id="enrollmentTable">
                                <thead>
                                    <tr>
                                        <th class="check-column">Select</th>
                                        <th>#</th>
                                        <th>Student</th>
                                        <th>Student ID</th>
                                        <th>Class Code</th>
                                        <th>Date Enrolled</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($studentEnrollments as $index => $enrollment):
                                        $status = (string)$enrollment["enrollment_status"];
                                        $statusClass = strtolower(str_replace(" ", "-", $status));
                                        $lastName = trim((string)($enrollment["last_name"] ?? ""));
                                        $firstName = trim((string)($enrollment["first_name"] ?? ""));
                                        $middleInitial = trim((string)($enrollment["middle_initial"] ?? ""));
                                        $extensionName = trim((string)($enrollment["extension_name"] ?? ""));
                                        $studentDisplayName = $lastName !== "" ? $lastName . ", " . $firstName : ($firstName !== "" ? $firstName : "Name unavailable");
                                        if ($middleInitial !== "") $studentDisplayName .= " " . $middleInitial;
                                        if ($extensionName !== "") $studentDisplayName .= " " . $extensionName;
                                        $photoUrl = studentPhotoUrl($enrollment["profile_photo"] ?? "");
                                        $periodKey = (string)$enrollment["academic_year"] . "|" . (string)$enrollment["semester"];
                                        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                                    ?>
                                        <tr data-status="<?= e($status) ?>" data-class-code="<?= (int)$enrollment["class_code_id"] ?>" data-period="<?= e($periodKey) ?>">
                                            <td class="check-column"><input class="row-checkbox" type="checkbox" name="enrollment_ids[]" value="<?= (int)$enrollment["enrollment_id"] ?>" aria-label="Select student <?= e($enrollment["student_id"]) ?>"></td>
                                            <td class="row-number"><?= $index + 1 ?></td>
                                            <td>
                                                <div class="student-identity">
                                                    <?php if ($photoUrl !== ""): ?>
                                                        <img class="student-avatar" src="<?= e($photoUrl) ?>" alt="<?= e($studentDisplayName) ?>" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false;">
                                                    <?php endif; ?>
                                                    <span class="student-avatar avatar-fallback" <?= $photoUrl !== "" ? 'hidden' : '' ?>><?= e($initials !== "" ? $initials : "ST") ?></span>
                                                    <strong><?= e($studentDisplayName) ?></strong>
                                                </div>
                                            </td>
                                            <td><strong><?= e($enrollment["student_id"]) ?></strong></td>
                                            <td><strong><?= e($enrollment["course_code"]) ?></strong></td>
                                            <td class="date-cell"><?= !empty($enrollment["enrolled_at"]) ? e(date("M d, Y h:i A", strtotime($enrollment["enrolled_at"]))) : "—" ?></td>
                                            <td><span class="enrollment-status-pill status-<?= e($statusClass) ?>"><?= e($status) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="no-search-results" id="noSearchResults" hidden><i class="bi bi-search"></i> No matching students found.</div>
                        </div>
                    </form>
                <?php elseif (!$enrollmentLoadError): ?>
                    <div class="enrollment-empty"><i class="bi bi-person-plus"></i><strong>No students enrolled yet</strong><span>Student enrollment records for your class codes will appear here.</span></div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php require_once "./globals/scripts.php"; ?>

    <style>
        .enrollment-page {
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
            cursor: pointer
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px
        }

        .summary-card {
            display: flex;
            align-items: center;
            gap: 11px;
            min-height: 83px;
            padding: 14px;
            border-radius: 13px;
            background: var(--activity-card-bg, var(--card-bg, #fff));
            border: 1px solid var(--activity-border, #e7eaf0);
            box-shadow: 0 4px 12px var(--shadow-color, rgba(20, 35, 70, .05))
        }

        .summary-icon {
            width: 39px;
            height: 39px;
            flex: 0 0 39px;
            border-radius: 11px;
            display: grid;
            place-items: center;
            font-size: 17px
        }

        .summary-icon.blue {
            background: #eaf0ff;
            color: #4165c5
        }

        .summary-icon.amber {
            background: #fff4d9;
            color: #946200
        }

        .summary-icon.green {
            background: #e8f7ee;
            color: #249653
        }

        .summary-icon.purple {
            background: #f1eafe;
            color: #7753a6
        }

        .summary-icon.red {
            background: #fdecee;
            color: #a52b36
        }

        .summary-card span {
            display: block;
            color: var(--text-secondary);
            font-size: 11px
        }

        .summary-card strong {
            display: block;
            color: var(--text-color);
            font-size: 21px;
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

        .record-count {
            white-space: nowrap;
            padding: 7px 11px;
            border-radius: 20px;
            background: var(--activity-progress-bg, #f1f3f8);
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 700
        }

        .table-tools {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 0 21px 14px;
            flex-wrap: wrap
        }

        .search-box {
            width: min(100%, 320px);
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

        .search-box input,
        .status-filter {
            width: 100%;
            height: 39px;
            padding: 8px 12px;
            border: 1px solid var(--activity-border, #dfe3eb);
            border-radius: 9px;
            background: var(--input-bg, var(--activity-card-bg, #fff));
            color: var(--text-color);
            font-size: 12px;
            outline: none
        }

        .search-box input {
            padding-left: 35px
        }

        .status-filter {
            width: 190px
        }

        .bulk-toolbar {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            padding: 12px 21px;
            background: var(--activity-progress-bg, #f6f7fa);
            border-top: 1px solid var(--activity-border, #e7eaf0);
            border-bottom: 1px solid var(--activity-border, #e7eaf0);
            font-size: 12px;
            color: var(--text-secondary)
        }

        .select-all-label {
            display: flex;
            align-items: center;
            gap: 7px;
            color: var(--text-color);
            font-weight: 700
        }

        .bulk-controls {
            display: flex;
            gap: 8px;
            margin-left: auto
        }

        .bulk-controls select {
            min-height: 34px;
            padding: 6px 9px;
            border: 1px solid var(--activity-border, #dfe3eb);
            border-radius: 7px;
            background: var(--input-bg, var(--activity-card-bg, #fff));
            color: var(--text-color);
            font-size: 11px
        }

        .bulk-apply-btn {
            border: 0;
            border-radius: 7px;
            background: var(--academic-blue-dark, #3456b8);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 8px 12px;
            cursor: pointer
        }

        .bulk-apply-btn:hover {
            filter: brightness(.92)
        }

        .enrollment-table {
            margin: 0;
            min-width: 900px;
            color: var(--text-color)
        }

        .enrollment-table thead th {
            padding: 12px 14px;
            white-space: nowrap;
            border-top: 0;
            border-bottom: 1px solid var(--activity-border, #e7eaf0);
            background: var(--activity-progress-bg, #f6f7fa);
            color: var(--text-secondary);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .45px;
            text-transform: uppercase
        }

        .enrollment-table tbody td {
            padding: 12px 14px;
            vertical-align: middle;
            border-bottom: 1px solid var(--activity-border, #edf0f4);
            background: transparent;
            font-size: 12px
        }

        .enrollment-table tbody tr:hover td {
            background: var(--activity-progress-bg, #fafbfe)
        }

        .check-column {
            width: 55px;
            text-align: center
        }

        .check-column input {
            width: 15px;
            height: 15px;
            accent-color: var(--academic-blue-dark, #3456b8);
            cursor: pointer
        }

        .row-number {
            color: var(--text-secondary)
        }

        .date-cell {
            color: var(--text-secondary) !important;
            white-space: nowrap
        }

        .enrollment-status-pill {
            display: inline-flex;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 750;
            white-space: nowrap
        }

        .status-pending {
            color: #946200;
            background: #fff4d9
        }

        .status-accepted {
            color: #187443;
            background: #e7f7ed
        }

        .status-denied {
            color: #a52b36;
            background: #fdecee
        }

        .status-on-hold {
            color: #7753a6;
            background: #f1eafe
        }

        .student-identity {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 210px
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--activity-border, #e7eaf0);
            background: var(--activity-progress-bg, #eef1f7)
        }

        .avatar-fallback {
            display: inline-grid;
            place-items: center;
            color: var(--academic-blue-dark, #3456b8);
            font-size: 12px;
            font-weight: 800
        }

        .avatar-fallback[hidden] {
            display: none
        }

        .student-identity strong {
            white-space: nowrap;
        }

        .student-identity .student-avatar {
            order: 0;
        }

        .enrollment-empty {
            min-height: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 22px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 12px
        }

        .enrollment-empty>i {
            font-size: 27px;
            color: var(--academic-blue-dark, #3456b8)
        }

        .enrollment-empty strong {
            color: var(--text-color)
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

        @media(max-width:900px) {
            .summary-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr))
            }
        }

        @media(max-width:767px) {
            .page-heading h2 {
                font-size: 21px
            }

            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 9px
            }

            .summary-card {
                min-height: 72px;
                padding: 11px
            }

            .records-heading {
                align-items: flex-start;
                flex-direction: column;
                padding: 0 15px 15px
            }

            .table-tools {
                padding: 0 15px 13px;
                flex-direction: column
            }

            .search-box,
            .status-filter {
                width: 100%
            }

            .bulk-toolbar {
                padding: 12px 15px
            }

            .bulk-controls {
                width: 100%;
                margin-left: 0
            }

            .bulk-controls select {
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
            const table = document.getElementById("enrollmentTable");
            if (!table) return;
            const search = document.getElementById("enrollmentSearch");
            const statusFilter = document.getElementById("statusFilter");
            const classCodeFilter = document.getElementById("classCodeFilter");
            const periodFilter = document.getElementById("periodFilter");
            const selectAll = document.getElementById("selectAll");
            const selectedCount = document.getElementById("selectedCount");
            const noResults = document.getElementById("noSearchResults");

            function updateSelected() {
                const boxes = [...table.querySelectorAll(".row-checkbox")];
                const checked = boxes.filter(box => box.checked);
                selectedCount.textContent = checked.length + " selected";
                const visible = boxes.filter(box => !box.closest("tr").hidden);
                selectAll.checked = visible.length > 0 && visible.every(box => box.checked);
                selectAll.indeterminate = visible.some(box => box.checked) && !selectAll.checked;
            }

            function updateRows() {
                const query = search.value.trim().toLowerCase();
                const selectedStatus = statusFilter.value;
                const selectedClassCode = classCodeFilter.value;
                const selectedPeriod = periodFilter.value;
                let visible = 0;
                table.querySelectorAll("tbody tr").forEach(row => {
                    const matches = row.textContent.toLowerCase().includes(query) &&
                        (!selectedStatus || row.dataset.status === selectedStatus) &&
                        (!selectedClassCode || row.dataset.classCode === selectedClassCode) &&
                        (!selectedPeriod || row.dataset.period === selectedPeriod);
                    row.hidden = !matches;
                    if (matches) visible++;
                    if (!matches) row.querySelector(".row-checkbox").checked = false;
                });
                noResults.hidden = visible > 0;
                updateSelected();
            }

            search.addEventListener("input", updateRows);
            statusFilter.addEventListener("change", updateRows);
            classCodeFilter.addEventListener("change", updateRows);
            periodFilter.addEventListener("change", updateRows);
            selectAll.addEventListener("change", () => {
                table.querySelectorAll("tbody tr").forEach(row => {
                    if (!row.hidden) row.querySelector(".row-checkbox").checked = selectAll.checked;
                });
                updateSelected();
            });
            table.addEventListener("change", event => {
                if (event.target.classList.contains("row-checkbox")) updateSelected();
            });

            window.confirmBulkUpdate = () => {
                const count = table.querySelectorAll(".row-checkbox:checked").length;
                const status = document.getElementById("bulkStatus").value;
                if (!count || !status) {
                    alert("Select at least one student and choose a status.");
                    return false;
                }
                return confirm("Update " + count + " selected enrollment(s) to " + status + "?");
            };
        })();
    </script>
</body>