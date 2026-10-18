<?php
/*
 * Academic Posts Manager - ETS Async Learning Portal
 * Place this file in the same admin directory as academic_post_create.php.
 * PHP resource files created here are saved under ../student/ only.
 */

include './globals/checks.php';
require_once '../src/connection.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$user = $_SESSION['user'] ?? [];
if (($user['access'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied. Administrator access is required.');
}

if (empty($_SESSION['academic_posts_csrf'])) {
    $_SESSION['academic_posts_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['academic_posts_csrf'];
$studentsDir = realpath(__DIR__ . '/../student');
if ($studentsDir === false || !is_dir($studentsDir) || !is_writable($studentsDir)) {
    $studentsDir = false;
}

$error = '';
$success = '';
$allowedColors = ['blue', 'green', 'orange', 'red', 'purple', 'cyan', 'yellow', 'gray'];
$defaultCode = <<<'CODE'
<?php
// Student-facing academic resource page.
// Add your page content below this line.
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Academic Resource</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <main class="container py-5">
        <h1>Academic Resource</h1>
        <p>Replace this sample content with your learning material.</p>
    </main>
</body>
</html>
CODE;

function h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function validPhpFilename(string $filename): bool
{
    return (bool)preg_match('/^[A-Za-z0-9_-]+\.php$/', $filename);
}
function safeStudentFilePath($studentsDir, string $filename): ?string
{
    if (!$studentsDir || !validPhpFilename($filename)) return null;
    return $studentsDir . DIRECTORY_SEPARATOR . $filename;
}
function writeStudentFile($studentsDir, string $filename, string $code): bool
{
    $path = safeStudentFilePath($studentsDir, $filename);
    if ($path === null) return false;
    // Write to a temporary file first so a failed save does not truncate the current page.
    $tmp = tempnam($studentsDir, '.academic_post_');
    if ($tmp === false) return false;
    $written = file_put_contents($tmp, $code, LOCK_EX);
    if ($written === false || !rename($tmp, $path)) {
        if (is_file($tmp)) unlink($tmp);
        return false;
    }
    return true;
}

$editing = null;
$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
if ($editId) {
    $stmt = $mysqli->prepare('SELECT * FROM academic_posts WHERE id = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $editId);
        $stmt->execute();
        $editing = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
    }
    if (!$editing) $error = 'The academic post you selected could not be found.';
}

$title = $editing['title'] ?? '';
$description = $editing['description'] ?? '';
$url = $editing['url'] ?? '';
$subject = $editing['subject'] ?? '';
$department = $editing['department'] ?? '';
$icon = $editing['icon'] ?? 'bi-file-earmark-text';
$iconColor = $editing['icon_color'] ?? 'blue';
$sortOrder = (int)($editing['sort_order'] ?? 0);
$status = (int)($editing['status'] ?? 1);
$code = $defaultCode;
if ($editing && validPhpFilename((string)$url) && $studentsDir) {
    $existingPath = safeStudentFilePath($studentsDir, (string)$url);
    if ($existingPath && is_file($existingPath) && is_readable($existingPath)) {
        $loadedCode = file_get_contents($existingPath);
        if ($loadedCode !== false) $code = $loadedCode;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrf, (string)($_POST['csrf_token'] ?? ''))) {
        $error = 'Your session token expired. Refresh the page and try again.';
    } else {
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'delete') {
            $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
            $deleteFile = isset($_POST['delete_file']) && $_POST['delete_file'] === '1';
            if (!$id) {
                $error = 'Invalid academic post selected.';
            } else {
                $stmt = $mysqli->prepare('SELECT url FROM academic_posts WHERE id = ? LIMIT 1');
                $oldUrl = '';
                if ($stmt) {
                    $stmt->bind_param('i', $id);
                    $stmt->execute();
                    $found = $stmt->get_result()->fetch_assoc();
                    $oldUrl = (string)($found['url'] ?? '');
                    $stmt->close();
                }
                $stmt = $mysqli->prepare('DELETE FROM academic_posts WHERE id = ?');
                if (!$stmt) {
                    $error = 'Could not prepare the delete operation.';
                } else {
                    $stmt->bind_param('i', $id);
                    if ($stmt->execute() && $stmt->affected_rows > 0) {
                        if ($deleteFile && $studentsDir && validPhpFilename($oldUrl)) {
                            $file = safeStudentFilePath($studentsDir, $oldUrl);
                            if ($file && is_file($file)) @unlink($file);
                        }
                        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?message=deleted');
                        exit;
                    }
                    $error = 'The post was not deleted. It may already have been removed.';
                    $stmt->close();
                }
            }
        } elseif ($action === 'save') {
            $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
            $title = trim((string)($_POST['title'] ?? ''));
            $description = trim((string)($_POST['description'] ?? ''));
            $url = trim((string)($_POST['url'] ?? ''));
            $subject = trim((string)($_POST['subject'] ?? ''));
            $department = trim((string)($_POST['department'] ?? ''));
            $icon = trim((string)($_POST['icon'] ?? 'bi-file-earmark-text'));
            $iconColor = trim((string)($_POST['icon_color'] ?? 'blue'));
            $sortOrder = filter_var($_POST['sort_order'] ?? 0, FILTER_VALIDATE_INT);
            if ($sortOrder === false || $sortOrder < 0) $sortOrder = 0;
            $status = isset($_POST['status']) ? 1 : 0;
            $code = (string)($_POST['page_code'] ?? '');

            if (!$studentsDir) {
                $error = 'The ../student/ directory was not found or is not writable. Check the directory and hosting permissions.';
            } elseif ($title === '') {
                $error = 'Please enter a post title.';
            } elseif (!validPhpFilename($url)) {
                $error = 'Filename must look like lesson_page.php and may contain only letters, numbers, underscores, or hyphens.';
            } elseif ($icon === '' || !preg_match('/^bi-[a-zA-Z0-9-]+$/', $icon)) {
                $error = 'Enter a valid Bootstrap Icon class, such as bi-file-earmark-text.';
            } elseif (!in_array($iconColor, $allowedColors, true)) {
                $error = 'Select a valid icon color.';
            } elseif (trim($code) === '') {
                $error = 'Please provide the PHP/HTML code for the student page.';
            } elseif (strlen($title) > 255 || strlen($url) > 500 || strlen($subject) > 150 || strlen($department) > 150) {
                $error = 'One or more fields exceed the maximum length allowed by the database.';
            } else {
                // Prevent overwriting a different file already referenced by another post.
                $duplicate = $mysqli->prepare('SELECT id FROM academic_posts WHERE url = ? AND id <> ? LIMIT 1');
                if ($duplicate) {
                    $duplicate->bind_param('si', $url, $id);
                    $duplicate->execute();
                    $hasDuplicate = (bool)$duplicate->get_result()->fetch_assoc();
                    $duplicate->close();
                } else {
                    $hasDuplicate = true;
                    $error = 'Could not validate the filename against existing posts.';
                }
                if ($hasDuplicate && $error === '') {
                    $error = 'That filename is already used by another academic post. Choose a different filename.';
                }
                $target = safeStudentFilePath($studentsDir, $url);
                if ($error === '' && $target && is_file($target) && !$id) {
                    $error = 'A file with that name already exists in /student. Choose another filename to avoid overwriting it.';
                }
                if ($error === '') {
                    $oldUrl = '';
                    if ($id) {
                        $lookup = $mysqli->prepare('SELECT url FROM academic_posts WHERE id = ? LIMIT 1');
                        if ($lookup) {
                            $lookup->bind_param('i', $id);
                            $lookup->execute();
                            $oldRow = $lookup->get_result()->fetch_assoc();
                            $oldUrl = (string)($oldRow['url'] ?? '');
                            $lookup->close();
                        }
                        if ($oldUrl === '') $error = 'The academic post to edit could not be found.';
                    }
                    if ($error === '' && writeStudentFile($studentsDir, $url, $code)) {
                        if ($id) {
                            $stmt = $mysqli->prepare('UPDATE academic_posts SET title=?, description=?, url=?, subject=?, department=?, icon=?, icon_color=?, sort_order=?, status=? WHERE id=?');
                            if ($stmt) {
                                $stmt->bind_param('sssssssiii', $title, $description, $url, $subject, $department, $icon, $iconColor, $sortOrder, $status, $id);
                                $saved = $stmt->execute();
                                $stmt->close();
                            } else $saved = false;
                        } else {
                            $stmt = $mysqli->prepare('INSERT INTO academic_posts (title, description, url, subject, department, icon, icon_color, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                            if ($stmt) {
                                $stmt->bind_param('sssssssii', $title, $description, $url, $subject, $department, $icon, $iconColor, $sortOrder, $status);
                                $saved = $stmt->execute();
                                $stmt->close();
                            } else $saved = false;
                        }
                        if ($saved) {
                            // If an edit changes the filename, remove the old file only when it is a safe PHP filename.
                            if ($id && $oldUrl !== '' && $oldUrl !== $url && validPhpFilename($oldUrl)) {
                                $oldPath = safeStudentFilePath($studentsDir, $oldUrl);
                                if ($oldPath && is_file($oldPath)) @unlink($oldPath);
                            }
                            header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?message=' . ($id ? 'updated' : 'created'));
                            exit;
                        } else {
                            $error = 'The page file was saved, but the database operation failed. Check the database connection and table structure.';
                        }
                    } elseif ($error === '') {
                        $error = 'Could not write the PHP file. Check permissions for the /student directory.';
                    }
                }
            }
            if ($id) $editing = ['id' => $id];
        }
    }
}

if (isset($_GET['message'])) {
    $messages = [
        'created' => 'Academic post and student page created successfully.',
        'updated' => 'Academic post and student page updated successfully.',
        'deleted' => 'Academic post deleted successfully.'
    ];
    $success = $messages[(string)$_GET['message']] ?? '';
}

$departments = [];
$res = $mysqli->query("SELECT DISTINCT department FROM accounts WHERE access='student' AND department IS NOT NULL AND TRIM(department)<>'' ORDER BY department");
if ($res) while ($r = $res->fetch_assoc()) $departments[] = $r['department'];
$subjects = [];
$res = $mysqli->query("SELECT DISTINCT subject FROM academic_posts WHERE subject IS NOT NULL AND TRIM(subject)<>'' ORDER BY subject");
if ($res) while ($r = $res->fetch_assoc()) $subjects[] = $r['subject'];
$posts = [];
$res = $mysqli->query('SELECT id,title,description,url,subject,department,icon,icon_color,sort_order,status,created_at FROM academic_posts ORDER BY sort_order ASC, id DESC');
if ($res) while ($r = $res->fetch_assoc()) $posts[] = $r;
?>
<!doctype html>
<html lang="en">

<head>
    <?php include 'globals/head.php'; ?>
    <style>
        .page-header {
            margin-bottom: 1.5rem
        }

        .page-header h1 {
            font-weight: 750
        }

        .manager-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .07);
            overflow: hidden;
            margin-bottom: 1.5rem
        }

        .manager-card-header {
            padding: 1rem 1.25rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6
        }

        .manager-card-body {
            padding: 1.25rem
        }

        .form-label {
            font-weight: 600
        }

        .code-editor {
            font-family: Consolas, Monaco, monospace;
            min-height: 360px;
            font-size: .9rem;
            tab-size: 4
        }

        .post-icon {
            font-size: 1.3rem
        }

        .table td,
        .table th {
            vertical-align: middle
        }

        .small-muted {
            font-size: .82rem;
            color: #6c757d
        }

        html[data-theme="dark"] .manager-card {
            background: #1e1e1e
        }

        html[data-theme="dark"] .manager-card-header {
            background: #252525;
            border-color: #3a3a3a
        }

        html[data-theme="dark"] .form-control,
        html[data-theme="dark"] .form-select,
        html[data-theme="dark"] textarea {
            background: #2b2b2b;
            border-color: #444;
            color: #f8f9fa
        }

        html[data-theme="dark"] .table {
            --bs-table-bg: transparent;
            --bs-table-color: #eee
        }

        html[data-theme="dark"] .small-muted {
            color: #adb5bd
        }

        @media(max-width:767px) {
            .manager-card-body {
                padding: 1rem
            }

            .table-responsive {
                font-size: .9rem
            }
        }
    </style>
</head>

<body>
    <?php include 'globals/sidebar.php'; ?>
    <?php include 'globals/topbar.php'; ?>
    <main class="main-content">
        <div class="container-fluid py-4">
            <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3"><i class="bi bi-journal-richtext me-2"></i>Academic Posts Manager</h1>
                    <p class="text-secondary mb-0">Create, edit, activate, or delete academic posts and their student-facing PHP pages.</p>
                </div>
                <a class="btn btn-outline-secondary" href="academic_posts.php"><i class="bi bi-arrow-left me-1"></i>Back to Academic Posts</a>
            </div>
            <?php if ($success): ?><div class="alert alert-success alert-dismissible fade show"><?= h($success) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= h($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
            <?php if (!$studentsDir): ?><div class="alert alert-warning">The <code>../student/</code> directory is missing or not writable. Create it and grant the PHP process permission to write files before creating or editing pages.</div><?php endif; ?>

            <section class="card manager-card" id="editor">
                <div class="manager-card-header d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <h2 class="h5 mb-1"><?= $editing ? 'Edit Academic Post' : 'Create Academic Post' ?></h2>
                        <div class="small-muted">The page code is saved as a PHP file inside <code>/student/</code>.</div>
                    </div><?php if ($editing): ?><a href="<?= h(strtok($_SERVER['REQUEST_URI'], '?')) ?>#editor" class="btn btn-sm btn-outline-secondary">New Post</a><?php endif; ?>
                </div>
                <div class="manager-card-body">
                    <form method="post" action="<?= h(strtok($_SERVER['REQUEST_URI'], '?')) ?><?= $editing ? '?edit=' . (int)($editing['id'] ?? 0) : '' ?>#editor">
                        <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">
                        <div class="row g-3">
                            <div class="col-md-8"><label class="form-label" for="title">Post title *</label><input class="form-control" id="title" name="title" maxlength="255" required value="<?= h($title) ?>" placeholder="e.g. Common Z-Transform Pairs"></div>
                            <div class="col-md-4"><label class="form-label" for="url">Filename in /student/ *</label>
                                <div class="input-group"><span class="input-group-text">/student/</span><input class="form-control" id="url" name="url" required pattern="[A-Za-z0-9_-]+\.php" value="<?= h($url) ?>" placeholder="z_transform.php"></div>
                                <div class="form-text">Use only letters, numbers, underscores, hyphens, and <code>.php</code>.</div>
                            </div>
                            <div class="col-12"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description" rows="2" placeholder="Describe this resource for students."><?= h($description) ?></textarea></div>
                            <div class="col-md-4"><label class="form-label" for="subject">Subject</label><input class="form-control" id="subject" name="subject" list="subjects" maxlength="150" value="<?= h($subject) ?>"><datalist id="subjects"><?php foreach ($subjects as $s): ?><option value="<?= h($s) ?>"><?php endforeach; ?></datalist></div>
                            <div class="col-md-4"><label class="form-label" for="department">Department</label><select class="form-select" id="department" name="department">
                                    <option value="">All Departments</option><?php foreach ($departments as $d): ?><option value="<?= h($d) ?>" <?= $department === $d ? 'selected' : '' ?>><?= h($d) ?></option><?php endforeach; ?>
                                </select></div>
                            <div class="col-md-2"><label class="form-label" for="sort_order">Sort order</label><input type="number" class="form-control" id="sort_order" name="sort_order" min="0" step="1" value="<?= h($sortOrder) ?>"></div>
                            <div class="col-md-2"><label class="form-label" for="status">Visibility</label>
                                <div class="form-check form-switch mt-2"><input class="form-check-input" type="checkbox" role="switch" id="status" name="status" <?= $status ? 'checked' : '' ?>><label class="form-check-label" for="status">Active</label></div>
                            </div>
                            <div class="col-md-6"><label class="form-label" for="icon">Bootstrap icon class</label><input class="form-control" id="icon" name="icon" value="<?= h($icon) ?>" required pattern="bi-[A-Za-z0-9-]+" placeholder="bi-file-earmark-text"></div>
                            <div class="col-md-6"><label class="form-label" for="icon_color">Icon color</label><select class="form-select" id="icon_color" name="icon_color"><?php foreach ($allowedColors as $c): ?><option value="<?= h($c) ?>" <?= $iconColor === $c ? 'selected' : '' ?>><?= ucfirst($c) ?></option><?php endforeach; ?></select></div>
                            <div class="col-12"><label class="form-label" for="page_code">PHP / HTML page code *</label><textarea class="form-control code-editor" id="page_code" name="page_code" spellcheck="false" required><?= h($code) ?></textarea>
                                <div class="form-text">This code will execute when students open the corresponding PHP page. Only enter code you trust.</div>
                            </div>
                            <div class="col-12 d-flex flex-wrap justify-content-end gap-2"><a class="btn btn-outline-secondary" href="<?= h(strtok($_SERVER['REQUEST_URI'], '?')) ?>#editor">Clear form</a><button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i><?= $editing ? 'Save Changes' : 'Create Post and PHP File' ?></button></div>
                        </div>
                    </form>
                </div>
            </section>

            <section class="card manager-card">
                <div class="manager-card-header">
                    <h2 class="h5 mb-0">Manage Existing Posts <span class="badge text-bg-secondary"><?= count($posts) ?></span></h2>
                </div>
                <div class="manager-card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6"><input id="postSearch" class="form-control" placeholder="Search title, subject, department, or filename..."></div>
                        <div class="col-md-3"><select id="statusFilter" class="form-select">
                                <option value="all">All statuses</option>
                                <option value="active">Active only</option>
                                <option value="inactive">Inactive only</option>
                            </select></div>
                        <div class="col-md-3"><select id="departmentFilter" class="form-select">
                                <option value="all">All departments</option><?php foreach ($departments as $d): ?><option value="<?= h(mb_strtolower($d)) ?>"><?= h($d) ?></option><?php endforeach; ?>
                            </select></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="postsTable">
                            <thead>
                                <tr>
                                    <th>Post</th>
                                    <th>Subject / Department</th>
                                    <th>Student page</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$posts): ?><tr>
                                        <td colspan="6" class="text-center text-secondary py-4">No academic posts found. Use the form above to create one.</td>
                                    </tr><?php endif; ?>
                                <?php foreach ($posts as $p): $filePath = $studentsDir && validPhpFilename((string)$p['url']) ? safeStudentFilePath($studentsDir, (string)$p['url']) : null;
                                    $fileExists = $filePath && is_file($filePath); ?>
                                    <tr data-search="<?= h(mb_strtolower($p['title'] . ' ' . $p['subject'] . ' ' . $p['department'] . ' ' . $p['url'])) ?>" data-status="<?= $p['status'] ? 'active' : 'inactive' ?>" data-department="<?= h(mb_strtolower($p['department'] ?? '')) ?>">
                                        <td>
                                            <div class="d-flex align-items-center gap-2"><i class="bi <?= h($p['icon'] ?: 'bi-file-earmark-text') ?> post-icon"></i>
                                                <div><strong><?= h($p['title']) ?></strong>
                                                    <div class="small-muted">ID #<?= (int)$p['id'] ?></div>
                                                </div>
                                            </div><?php if ($p['description']): ?><div class="small-muted mt-1"><?= h(mb_strimwidth((string)$p['description'], 0, 100, '…')) ?></div><?php endif; ?>
                                        </td>
                                        <td><?= h($p['subject'] ?: '—') ?><div class="small-muted"><?= h($p['department'] ?: 'All departments') ?></div>
                                        </td>
                                        <td><code><?= h($p['url']) ?></code>
                                            <div class="small-muted"><?= $fileExists ? 'File exists' : 'File missing' ?></div><?php if (validPhpFilename((string)$p['url'])): ?><a href="../student/<?= rawurlencode((string)$p['url']) ?>" target="_blank" rel="noopener">Open page <i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?>
                                        </td>
                                        <td><?= $p['status'] ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-secondary">Inactive</span>' ?></td>
                                        <td><?= (int)$p['sort_order'] ?></td>
                                        <td class="text-end">
                                            <div class="d-inline-flex flex-wrap justify-content-end gap-1"><a class="btn btn-sm btn-outline-primary" href="<?= h(strtok($_SERVER['REQUEST_URI'], '?')) ?>?edit=<?= (int)$p['id'] ?>#editor"><i class="bi bi-pencil-square"></i> Edit</a>
                                                <form method="post" action="<?= h(strtok($_SERVER['REQUEST_URI'], '?')) ?>" onsubmit="return confirm('Delete this academic post record? This does not delete its PHP file unless you check the option in the next prompt.');"><input type="hidden" name="csrf_token" value="<?= h($csrf) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><input type="hidden" name="delete_file" id="delete_file_<?= (int)$p['id'] ?>" value="0"><button class="btn btn-sm btn-outline-danger" type="submit" onclick="document.getElementById('delete_file_<?= (int)$p['id'] ?>').value = confirm('Also permanently delete /student/<?= h(addslashes((string)$p['url'])) ?>? This cannot be undone.') ? '1' : '0';"><i class="bi bi-trash"></i> Delete</button></form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <?php include 'globals/scripts.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const search = document.getElementById('postSearch');
            const status = document.getElementById('statusFilter');
            const dept = document.getElementById('departmentFilter');
            const rows = [...document.querySelectorAll('#postsTable tbody tr[data-search]')];

            function filterRows() {
                const q = search.value.trim().toLowerCase();
                rows.forEach(row => {
                    const match = row.dataset.search.includes(q) &&
                        (status.value === 'all' || row.dataset.status === status.value) &&
                        (dept.value === 'all' || row.dataset.department === dept.value);
                    row.hidden = !match;
                });
            }
            search.addEventListener('input', filterRows);
            status.addEventListener('change', filterRows);
            dept.addEventListener('change', filterRows);
        });
    </script>
</body>

</html>