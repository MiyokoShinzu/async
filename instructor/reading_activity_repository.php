<?php
/* =========================================================
   INSTRUCTOR READING ACTIVITY REPOSITORY
   ETS-Async Learning Portal
   Activities are stored independently of class assignments.
   ========================================================= */

session_cache_limiter('nocache');
session_start();

if (
    !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user']['access']) || $_SESSION['user']['access'] !== 'instructor'
) {
    header('Location: ../login.php');
    exit;
}

require_once '../src/connection.php';

$user = $_SESSION['user'];
$instructorId = (int)($user['id'] ?? $_SESSION['user_id']);
$displayName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));

$title = '';
$fileUrl = '';
$fileType = 'pdf';
$description = '';
$requiredReadingMinutes = 30;
$status = 'active';
$startDate = date('Y-m-d\TH:i');
$dueDate = date('Y-m-d\TH:i', strtotime('+14 days'));
$error = '';
$success = '';
$editingId = 0;
$isEditing = false;

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/* Load an instructor-owned activity into the form for editing. */
if (isset($_GET['edit']) && ctype_digit((string)$_GET['edit'])) {
    $requestedEditId = (int)$_GET['edit'];
    $editStmt = $mysqli->prepare("SELECT id, title, file_url, file_type, description, required_reading_minutes, start_date, due_date, status FROM reading_activity_table WHERE id = ? AND created_by = ? LIMIT 1");
    if ($editStmt) {
        $editStmt->bind_param('ii', $requestedEditId, $instructorId);
        if ($editStmt->execute()) {
            $editResult = $editStmt->get_result();
            if ($editRow = $editResult->fetch_assoc()) {
                $editingId = (int)$editRow['id'];
                $isEditing = true;
                $title = $editRow['title'];
                $fileUrl = $editRow['file_url'];
                $fileType = $editRow['file_type'];
                $description = $editRow['description'] ?? '';
                $requiredReadingMinutes = (int)$editRow['required_reading_minutes'];
                $startDate = $editRow['start_date'] ? date('Y-m-d\TH:i', strtotime($editRow['start_date'])) : '';
                $dueDate = $editRow['due_date'] ? date('Y-m-d\TH:i', strtotime($editRow['due_date'])) : '';
                $status = $editRow['status'];
            } else {
                $error = 'Reading activity not found or you do not have permission to edit it.';
            }
        }
        $editStmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array(($_POST['action'] ?? ''), ['create', 'update'], true)) {
    $action = $_POST['action'];
    $editingId = $action === 'update' ? (int)($_POST['activity_id'] ?? 0) : 0;
    $isEditing = $action === 'update';

    $title = trim($_POST['title'] ?? '');
    $fileUrl = trim($_POST['file_url'] ?? '');
    $fileType = strtolower(trim($_POST['file_type'] ?? 'pdf'));
    $description = trim($_POST['description'] ?? '');
    $requiredReadingMinutes = (int)($_POST['required_reading_minutes'] ?? 0);
    $startDate = trim($_POST['start_date'] ?? '');
    $dueDate = trim($_POST['due_date'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($isEditing && $editingId < 1) {
        $error = 'Invalid reading activity selected.';
    } elseif ($title === '') {
        $error = 'Reading activity title is required.';
    } elseif (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
        $error = 'Please enter a valid reading material URL.';
    } elseif (!in_array($fileType, ['pdf', 'doc', 'docx', 'ppt', 'pptx'], true)) {
        $error = 'Please select a valid file type.';
    } elseif ($requiredReadingMinutes < 1 || $requiredReadingMinutes > 1440) {
        $error = 'Required reading time must be between 1 and 1440 minutes.';
    } elseif ($startDate === '' || $dueDate === '') {
        $error = 'Both the available date and due date are required.';
    } elseif (strtotime($dueDate) <= strtotime($startDate)) {
        $error = 'Due date must be later than the available date.';
    } elseif (!in_array($status, ['active', 'inactive'], true)) {
        $error = 'Invalid activity status.';
    } else {
        $startDateDB = date('Y-m-d H:i:s', strtotime($startDate));
        $dueDateDB = date('Y-m-d H:i:s', strtotime($dueDate));

        if ($isEditing) {
            /* The ownership condition prevents editing another instructor's item. */
            $sql = "UPDATE reading_activity_table
                    SET title = ?, file_url = ?, file_type = ?, description = ?,
                        required_reading_minutes = ?, start_date = ?, due_date = ?, status = ?
                    WHERE id = ? AND created_by = ?";
            $stmt = $mysqli->prepare($sql);
            if ($stmt) {
                $stmt->bind_param(
                    'ssssisssii',
                    $title,
                    $fileUrl,
                    $fileType,
                    $description,
                    $requiredReadingMinutes,
                    $startDateDB,
                    $dueDateDB,
                    $status,
                    $editingId,
                    $instructorId
                );
                if ($stmt->execute() && $stmt->affected_rows >= 0) {
                    $success = 'Reading activity updated successfully.';
                    $isEditing = false;
                    $editingId = 0;
                    $title = $fileUrl = $description = '';
                    $fileType = 'pdf';
                    $requiredReadingMinutes = 30;
                    $status = 'active';
                    $startDate = date('Y-m-d\TH:i');
                    $dueDate = date('Y-m-d\TH:i', strtotime('+14 days'));
                } else {
                    $error = 'Unable to update the reading activity. Please try again.';
                }
                $stmt->close();
            } else {
                $error = 'Database error. Unable to prepare the update request.';
            }
        } else {
            /* Keep legacy assignment columns empty; assignment will use class codes later. */
            $unassignedDepartment = '';
            $unassignedYearLevel = '';
            $unassignedSection = '';
            $sql = "INSERT INTO reading_activity_table
                (title, file_url, file_type, department, year_level, section,
                 description, required_reading_minutes, start_date, due_date, status, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql);
            if ($stmt) {
                $stmt->bind_param(
                    'sssssssisssi',
                    $title,
                    $fileUrl,
                    $fileType,
                    $unassignedDepartment,
                    $unassignedYearLevel,
                    $unassignedSection,
                    $description,
                    $requiredReadingMinutes,
                    $startDateDB,
                    $dueDateDB,
                    $status,
                    $instructorId
                );
                if ($stmt->execute()) {
                    $success = 'Reading activity saved to your repository.';
                    $title = $fileUrl = $description = '';
                    $fileType = 'pdf';
                    $requiredReadingMinutes = 30;
                    $status = 'active';
                    $startDate = date('Y-m-d\TH:i');
                    $dueDate = date('Y-m-d\TH:i', strtotime('+14 days'));
                } else {
                    $error = 'Unable to save the reading activity. Please try again.';
                }
                $stmt->close();
            } else {
                $error = 'Database error. Unable to prepare the save request.';
            }
        }
    }
}

/* Fetch only activities created by the currently logged-in instructor. */
$repositoryItems = [];
$listSql = "SELECT id, title, file_url, file_type, required_reading_minutes, start_date, due_date, status, created_at
            FROM reading_activity_table
            WHERE created_by = ?
            ORDER BY created_at DESC, id DESC";
$listStmt = $mysqli->prepare($listSql);
if ($listStmt) {
    $listStmt->bind_param('i', $instructorId);
    if ($listStmt->execute()) {
        $result = $listStmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $repositoryItems[] = $row;
        }
    }
    $listStmt->close();
}

include 'globals/head.php';
?>

<style>
    .repository-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .repository-heading {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        margin-bottom: 24px;
    }

    .repository-heading h2 {
        margin: 0;
        color: var(--text-color, #1f2937);
        font-size: 1.55rem;
        font-weight: 750;
    }

    .repository-heading p {
        margin: 7px 0 0;
        color: var(--text-secondary, #6b7280);
        font-size: .92rem;
    }

    .repository-card {
        overflow: hidden;
        background: var(--activity-card-bg, #fff);
        border: 1px solid var(--activity-border, #e5e7eb);
        border-radius: 14px;
        box-shadow: 0 4px 14px var(--shadow-color, rgba(15, 23, 42, .05));
        margin-bottom: 24px;
    }

    .repository-card-header {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 20px 23px;
        border-bottom: 1px solid var(--activity-border, #e5e7eb);
        background: var(--activity-card-header-bg, rgba(37, 99, 235, .045));
    }

    .repository-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        color: #2563eb;
        background: rgba(37, 99, 235, .11);
        font-size: 1.25rem;
    }

    .repository-card-header h5 {
        margin: 0;
        color: var(--text-color, #1f2937);
        font-size: 1rem;
        font-weight: 700;
    }

    .repository-card-header p {
        margin: 4px 0 0;
        color: var(--text-secondary, #6b7280);
        font-size: .83rem;
    }

    .repository-form {
        padding: 24px;
    }

    .repository-form .form-label {
        color: var(--text-color, #374151);
        font-size: .87rem;
        font-weight: 650;
        margin-bottom: 7px;
    }

    .repository-form .form-control,
    .repository-form .form-select {
        min-height: 44px;
        color: var(--text-color, #1f2937);
        background: var(--input-bg, var(--activity-card-bg, #fff));
        border: 1px solid var(--activity-border, #d1d5db);
        border-radius: 8px;
        font-size: .9rem;
    }

    .repository-form textarea.form-control {
        min-height: 112px;
        resize: vertical;
    }

    .repository-form .form-control:focus,
    .repository-form .form-select:focus {
        border-color: #2f81f7;
        box-shadow: 0 0 0 3px rgba(47, 129, 247, .14);
    }

    .repository-help {
        color: var(--text-secondary, #6b7280);
        font-size: .78rem;
        margin-top: 6px;
    }

    .repository-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid var(--activity-border, #e5e7eb);
        margin-top: 23px;
        padding-top: 19px;
    }

    .repository-footer .btn {
        min-height: 42px;
        border-radius: 8px;
        padding: 9px 17px;
        font-weight: 650;
    }

    .repository-footer .btn-primary {
        background: #2f81f7;
        border-color: #2f81f7;
    }

    .repository-footer .btn-primary:hover {
        background: #1f6feb;
        border-color: #1f6feb;
    }

    .repository-table {
        color: var(--text-color, #1f2937);
        margin: 0;
    }

    .repository-table th {
        color: var(--text-secondary, #6b7280);
        font-size: .76rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        white-space: nowrap;
    }

    .repository-table td {
        vertical-align: middle;
        font-size: .86rem;
    }

    .repository-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 30px;
        padding: 5px 9px;
        font-size: .74rem;
        font-weight: 700;
    }

    .repository-status.active {
        color: #15803d;
        background: rgba(34, 197, 94, .12);
    }

    .repository-status.inactive {
        color: #6b7280;
        background: rgba(107, 114, 128, .12);
    }

    .repository-empty {
        padding: 42px 20px;
        text-align: center;
        color: var(--text-secondary, #6b7280);
    }

    .repository-empty i {
        display: block;
        color: #2f81f7;
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .repository-alert {
        border: 0;
        border-radius: 10px;
        font-size: .9rem;
    }

    @media(max-width:768px) {
        .repository-heading {
            flex-direction: column;
        }

        .repository-heading .btn {
            width: 100%;
        }

        .repository-form {
            padding: 17px;
        }

        .repository-card-header {
            padding: 17px;
        }

        .repository-footer {
            flex-direction: column-reverse;
        }

        .repository-footer .btn {
            width: 100%;
        }
    }
</style>

<body>
    <?php include 'globals/sidebar.php'; ?>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php include 'globals/topbar.php'; ?>

    <main class="main-content">
        <div class="content-wrapper repository-page">
            <div class="repository-heading">
                <div>
                    <h2><i class="bi bi-collection me-2" style="color:#2f81f7"></i>Reading Activity Repository</h2>
                    <p>Save and organize reading materials now, then assign them to classes later using class codes.</p>
                </div>
                <a href="#repository-list" class="btn btn-outline-secondary">
                    <i class="bi bi-list-ul me-1"></i> Manage Activities
                </a>
            </div>

            <?php if ($success !== ''): ?>
                <div class="alert alert-success repository-alert"><i class="bi bi-check-circle me-2"></i><?= e($success) ?></div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div class="alert alert-danger repository-alert"><i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?></div>
            <?php endif; ?>

            <section class="repository-card" id="repository-form">
                <div class="repository-card-header">
                    <div class="repository-icon"><i class="bi bi-journal-plus"></i></div>
                    <div>
                        <h5><?= $isEditing ? 'Edit Reading Activity' : 'Add Reading Material' ?></h5>
                        <p><?= $isEditing ? 'Update the saved material details below.' : 'Create a reusable activity without assigning it to a department, year level, section, or class.' ?></p>
                    </div>
                </div>
                <form class="repository-form" method="POST" action="reading_activity_repository.php">
                    <input type="hidden" name="action" value="<?= $isEditing ? 'update' : 'create' ?>">
                    <?php if ($isEditing): ?><input type="hidden" name="activity_id" value="<?= (int)$editingId ?>"><?php endif; ?>
                    <div class="row g-4">
                        <div class="col-12">
                            <label for="title" class="form-label">Reading Activity Title <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control" maxlength="255" placeholder="Example: Introduction to Digital Signal Processing" value="<?= e($title) ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="file_url" class="form-label">Reading Material URL <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                                <input type="url" id="file_url" name="file_url" class="form-control" maxlength="1000" placeholder="https://drive.google.com/..." value="<?= e($fileUrl) ?>" required>
                            </div>
                            <div class="repository-help">Paste a Google Drive, Google Docs, Google Slides, or other supported reading material URL.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="file_type" class="form-label">File Type <span class="text-danger">*</span></label>
                            <select id="file_type" name="file_type" class="form-select" required>
                                <?php foreach (['pdf' => 'PDF', 'doc' => 'Word Document (.doc)', 'docx' => 'Word Document (.docx)', 'ppt' => 'PowerPoint (.ppt)', 'pptx' => 'PowerPoint (.pptx)'] as $value => $label): ?>
                                    <option value="<?= e($value) ?>" <?= $fileType === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="required_reading_minutes" class="form-label">Required Reading Time <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input type="number" id="required_reading_minutes" name="required_reading_minutes" class="form-control" min="1" max="1440" value="<?= e($requiredReadingMinutes) ?>" required>
                            </div>
                            <div class="repository-help">Minimum active reading time before completion.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Available From <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="start_date" name="start_date" class="form-control" value="<?= e($startDate) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" id="due_date" name="due_date" class="form-control" value="<?= e($dueDate) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Repository Status</label>
                            <select id="status" name="status" class="form-select">
                                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                            <div class="repository-help">This status is stored with the item. It does not assign the activity to students.</div>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Instructions <span class="text-muted fw-normal">(Optional)</span></label>
                            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Provide reading instructions or notes for students."><?= e($description) ?></textarea>
                        </div>
                    </div>
                    <div class="repository-footer">
                        <a href="reading_activity_repository.php" class="btn btn-light"><i class="bi bi-x-lg me-1"></i> <?= $isEditing ? 'Cancel' : 'Clear' ?></a>
                        <button type="submit" class="btn btn-primary"><i class="bi <?= $isEditing ? 'bi-save' : 'bi-plus-lg' ?> me-1"></i> <?= $isEditing ? 'Update Activity' : 'Save to Repository' ?></button>
                    </div>
                </form>
            </section>

            <section class="repository-card" id="repository-list">
                <div class="repository-card-header">
                    <div class="repository-icon"><i class="bi bi-archive"></i></div>
                    <div>
                        <h5>My Saved Reading Activities</h5>
                        <p><?= count($repositoryItems) ?> item(s) in your repository. These are not assigned to a class yet.</p>
                    </div>
                </div>
                <?php if ($repositoryItems): ?>
                    <div class="table-responsive">
                        <table class="table repository-table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>File</th>
                                    <th>Reading Time</th>
                                    <th>Availability</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($repositoryItems as $item): ?>
                                    <tr>
                                        <td><strong><?= e($item['title']) ?></strong>
                                            <div class="small text-muted">Created <?= e($item['created_at'] ? date('M d, Y', strtotime($item['created_at'])) : '—') ?></div>
                                        </td>
                                        <td><span class="text-uppercase fw-semibold"><?= e($item['file_type']) ?></span></td>
                                        <td><?= (int)$item['required_reading_minutes'] ?> min</td>
                                        <td>
                                            <div class="small">From: <?= e($item['start_date'] ? date('M d, Y g:i A', strtotime($item['start_date'])) : '—') ?></div>
                                            <div class="small text-muted">Due: <?= e($item['due_date'] ? date('M d, Y g:i A', strtotime($item['due_date'])) : '—') ?></div>
                                        </td>
                                        <td><span class="repository-status <?= e($item['status']) ?>"><i class="bi bi-circle-fill" style="font-size:.42rem"></i><?= e(ucfirst($item['status'])) ?></span></td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="reading_activity_repository.php?edit=<?= (int)$item['id'] ?>#repository-form" title="Edit activity"><i class="bi bi-pencil-square me-1"></i>Edit</a><a class="btn btn-sm btn-outline-secondary" href="<?= e($item['file_url']) ?>" target="_blank" rel="noopener noreferrer" title="Open reading material"><i class="bi bi-box-arrow-up-right"></i></a></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="repository-empty"><i class="bi bi-journal-bookmark"></i><strong class="d-block mb-1">Your repository is empty</strong><span>Create your first reading activity above. You can assign it to a class later.</span></div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php require_once 'globals/scripts.php'; ?>
</body>

</html>