<?php

declare(strict_types=1);
session_start();
date_default_timezone_set('Asia/Manila');

/*
 * ETS-Async Account Manager
 * Place this file at: admin/manage_accounts.php
 * Assumes ../src/connection.php defines a mysqli connection named $mysqli.
 * Uses the existing `accounts` table.
 */

if (
    ($_SESSION['logged_in'] ?? false) !== true ||
    !isset($_SESSION['user']) ||
    strtolower((string)($_SESSION['user']['access'] ?? '')) !== 'admin'
) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../src/connection.php';

if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    http_response_code(500);
    exit('Database connection not found. Check ../src/connection.php and confirm it defines $mysqli.');
}
$mysqli->set_charset('utf8mb4');

$roles = ['admin', 'instructor', 'student', 'quiz_master', 'calendar_master'];

if (empty($_SESSION['account_manager_csrf'])) {
    $_SESSION['account_manager_csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['account_manager_csrf'];

function h(mixed $value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
function postValue(string $key): string
{
    return trim((string)($_POST[$key] ?? ''));
}
function flashRedirect(string $message, string $type = 'success'): never
{
    $_SESSION['account_manager_flash'] = ['message' => $message, 'type' => $type];
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
function csrfValid(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['account_manager_csrf'])
        && hash_equals((string)$_SESSION['account_manager_csrf'], (string)$_POST['csrf_token']);
}
function accountExists(mysqli $db, string $column, string $value): bool
{
    $allowed = ['username', 'email', 'student_id'];
    if (!in_array($column, $allowed, true) || $value === '') return false;
    $stmt = $db->prepare("SELECT id FROM accounts WHERE `$column` = ? LIMIT 1");
    if (!$stmt) return false;
    $stmt->bind_param('s', $value);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result && $result->num_rows > 0;
    $stmt->close();
    return $exists;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfValid()) {
        flashRedirect('Your session token expired. Please refresh and try again.', 'danger');
    }

    $action = postValue('action');

    if ($action === 'create') {
        $firstName = postValue('first_name');
        $lastName = postValue('last_name');
        $middleInitial = strtoupper(postValue('middle_initial'));
        $extensionName = postValue('extension_name');
        $department = postValue('department');
        $yearSection = postValue('year_section');
        $studentId = postValue('student_id');
        $email = strtolower(postValue('email'));
        $username = postValue('username');
        $password = (string)($_POST['password'] ?? '');
        $access = strtolower(postValue('access'));

        if ($firstName === '' || $lastName === '') {
            flashRedirect('First name and last name are required.', 'danger');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flashRedirect('Enter a valid email address.', 'danger');
        }
        if (!preg_match('/^[A-Za-z0-9_.-]{4,50}$/', $username)) {
            flashRedirect('Username must be 4–50 characters and may contain letters, numbers, underscores, periods, and hyphens.', 'danger');
        }
        if (strlen($password) < 8) {
            flashRedirect('Password must contain at least 8 characters.', 'danger');
        }
        if (!in_array($access, $roles, true)) {
            flashRedirect('Select a valid account access type.', 'danger');
        }
        if ($access === 'student' && ($studentId === '' || $department === '' || $yearSection === '')) {
            flashRedirect('Student accounts require Student ID, department, and year/section.', 'danger');
        }
        if (accountExists($mysqli, 'username', $username)) {
            flashRedirect('That username is already registered.', 'danger');
        }
        if (accountExists($mysqli, 'email', $email)) {
            flashRedirect('That email is already registered.', 'danger');
        }
        if ($studentId !== '' && accountExists($mysqli, 'student_id', $studentId)) {
            flashRedirect('That Student ID is already registered.', 'danger');
        }

        // Student-specific fields are NULL for non-student accounts.
        $studentIdValue = $studentId !== '' ? $studentId : null;
        $departmentValue = $department !== '' ? $department : null;
        $yearSectionValue = $yearSection !== '' ? $yearSection : null;
        $middleInitialValue = $middleInitial !== '' ? $middleInitial : null;
        $extensionNameValue = $extensionName !== '' ? $extensionName : null;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare(
            'INSERT INTO accounts
            (last_name, first_name, middle_initial, extension_name, department, year_section,
             student_id, email, username, password, access)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        if (!$stmt) {
            flashRedirect('Could not prepare account creation. Confirm the accounts table columns and database schema.', 'danger');
        }

        $stmt->bind_param(
            'sssssssssss',
            $lastName,
            $firstName,
            $middleInitialValue,
            $extensionNameValue,
            $departmentValue,
            $yearSectionValue,
            $studentIdValue,
            $email,
            $username,
            $passwordHash,
            $access
        );

        if ($stmt->execute()) {
            $stmt->close();
            flashRedirect('Account created successfully.');
        }
        $error = $stmt->errno === 1062
            ? 'A username, email, or Student ID already exists.'
            : 'Account could not be created. Confirm that the SQL changes were applied.';
        $stmt->close();
        flashRedirect($error, 'danger');
    }

    if ($action === 'update_access') {
        $id = filter_var($_POST['account_id'] ?? null, FILTER_VALIDATE_INT);
        $access = strtolower(postValue('access'));

        if (!$id || $id < 1) flashRedirect('Invalid account ID.', 'danger');
        if (!in_array($access, $roles, true)) flashRedirect('Select a valid account access type.', 'danger');

        $currentId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? 0);
        if ($currentId === (int)$id && $access !== 'admin') {
            flashRedirect('You cannot remove your own admin access while signed in.', 'danger');
        }

        $stmt = $mysqli->prepare('UPDATE accounts SET access = ? WHERE id = ?');
        if (!$stmt) flashRedirect('Could not prepare access update.', 'danger');
        $stmt->bind_param('si', $access, $id);
        $ok = $stmt->execute();
        $stmt->close();

        if ($ok) flashRedirect('Account access updated successfully.');
        flashRedirect('Unable to update account access.', 'danger');
    }

    flashRedirect('Unsupported request.', 'danger');
}

$flash = $_SESSION['account_manager_flash'] ?? null;
unset($_SESSION['account_manager_flash']);

$search = trim((string)($_GET['q'] ?? ''));
$filterAccess = strtolower(trim((string)($_GET['access'] ?? '')));
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;

$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = '(first_name LIKE ? OR last_name LIKE ? OR username LIKE ? OR email LIKE ? OR student_id LIKE ?)';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like, $like, $like);
    $types .= 'sssss';
}
if (in_array($filterAccess, $roles, true)) {
    $where[] = 'access = ?';
    $params[] = $filterAccess;
    $types .= 's';
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$countStmt = $mysqli->prepare('SELECT COUNT(*) AS total FROM accounts' . $whereSql);
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = (int)($countStmt->get_result()->fetch_assoc()['total'] ?? 0);
$countStmt->close();

$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$listStmt = $mysqli->prepare(
    'SELECT id, last_name, first_name, middle_initial, extension_name, department, year_section,
            student_id, email, username, access, created_at
     FROM accounts' . $whereSql . ' ORDER BY last_name ASC, first_name ASC LIMIT ? OFFSET ?'
);
$listTypes = $types . 'ii';
$listParams = $params;
$listParams[] = $perPage;
$listParams[] = $offset;
$listStmt->bind_param($listTypes, ...$listParams);
$listStmt->execute();
$result = $listStmt->get_result();
$accounts = [];
while ($row = $result->fetch_assoc()) $accounts[] = $row;
$listStmt->close();

$stats = array_fill_keys(['total', 'admin', 'instructor', 'student', 'quiz_master', 'calendar_master'], 0);
$statsResult = $mysqli->query(
    "SELECT COUNT(*) AS total,
     SUM(access='admin') AS admin,
     SUM(access='instructor') AS instructor,
     SUM(access='student') AS student,
     SUM(access='quiz_master') AS quiz_master,
     SUM(access='calendar_master') AS calendar_master
     FROM accounts"
);
if ($statsResult) {
    $statsRow = $statsResult->fetch_assoc();
    foreach ($stats as $key => $_) $stats[$key] = (int)($statsRow[$key] ?? 0);
}
$currentAdminId = (int)($_SESSION['user']['id'] ?? $_SESSION['user']['user_id'] ?? 0);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Accounts | ETS-Async</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #0d1117;
            --panel: #161b22;
            --border: #30363d;
            --text: #f0f6fc;
            --muted: #8b949e;
            --blue: #2f81f7
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--text);
            font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif
        }

        .topbar {
            min-height: 66px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px clamp(16px, 4vw, 48px);
            background: #0d1117;
            position: sticky;
            top: 0;
            z-index: 5
        }

        .brand {
            font-weight: 800;
            color: var(--text);
            text-decoration: none
        }

        .brand span {
            color: #58a6ff
        }

        .page {
            max-width: 1500px;
            margin: auto;
            padding: 28px clamp(14px, 3vw, 36px) 56px
        }

        .panel,
        .stat {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 14px
        }

        .panel {
            overflow: hidden;
            margin-bottom: 20px
        }

        .panel-head {
            padding: 17px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap
        }

        .panel-head h2 {
            font-size: 16px;
            font-weight: 750;
            margin: 0
        }

        .panel-body {
            padding: 20px
        }

        .stat {
            padding: 15px;
            min-height: 94px
        }

        .stat-label {
            font-size: 12px;
            color: var(--muted)
        }

        .stat-number {
            font-size: 25px;
            font-weight: 800
        }

        .form-label {
            font-size: 12px;
            color: #c9d1d9;
            font-weight: 650
        }

        .form-control,
        .form-select {
            background: #0d1117;
            color: var(--text);
            border-color: #3d444d;
            min-height: 42px
        }

        .form-control:focus,
        .form-select:focus {
            background: #0d1117;
            color: var(--text);
            border-color: var(--blue);
            box-shadow: 0 0 0 .2rem #2f81f733
        }

        .form-select option {
            background: #161b22;
            color: #f0f6fc
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--text);
            --bs-table-border-color: var(--border);
            margin: 0
        }

        .table thead th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: var(--muted);
            white-space: nowrap;
            background: #11161d
        }

        .table td,
        .table th {
            padding: 13px;
            vertical-align: middle
        }

        .subtext {
            display: block;
            font-size: 11px;
            color: var(--muted);
            margin-top: 3px
        }

        .role {
            display: inline-block;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 750;
            overflow-wrap: anywhere
        }

        .role-admin {
            color: #d2a8ff
        }

        .role-instructor {
            color: #79c0ff
        }

        .role-student {
            color: #7ee787
        }

        .role-quiz_master {
            color: #f2cc60
        }

        .role-calendar_master {
            color: #ffa657
        }

        .small-select {
            min-width: 155px;
            min-height: 36px;
            font-size: 12px
        }

        .muted-note {
            font-size: 11px;
            color: var(--muted)
        }

        .pagination .page-link {
            background: var(--panel);
            border-color: var(--border);
            color: var(--text)
        }

        .pagination .active .page-link {
            background: var(--blue);
            border-color: var(--blue)
        }

        @media(max-width:700px) {
            .panel-body {
                padding: 14px
            }

            .table td,
            .table th {
                padding: 10px
            }

            .topbar .btn {
                white-space: nowrap
            }
        }
    </style>
</head>

<body>
    <nav class="topbar">
        <a class="brand" href="dashboard.php">ETS-<span>Async</span> <small class="text-secondary fw-normal ms-2">Admin</small></a>
        <a href="dashboard.php" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
    </nav>
    <main class="page">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold mb-1"><i class="bi bi-people text-info me-2"></i>Account Management</h1>
                <p class="text-secondary mb-0">Create accounts, list registered users, and change access types.</p>
            </div>
            <a href="#create-account" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Create account</a>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= h($flash['type'] ?? 'info') ?> alert-dismissible fade show" role="alert">
                <?= h($flash['message'] ?? '') ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <?php
            $statLabels = [
                'total' => 'Total accounts',
                'admin' => 'Admins',
                'instructor' => 'Instructors',
                'student' => 'Students',
                'quiz_master' => 'Quiz masters',
                'calendar_master' => 'Calendar masters'
            ];
            foreach ($statLabels as $key => $label):
            ?>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="stat">
                        <div class="stat-label"><?= h($label) ?></div>
                        <div class="stat-number"><?= $stats[$key] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <section class="panel" id="create-account">
            <div class="panel-head">
                <h2><i class="bi bi-person-plus text-info me-2"></i>Create an account</h2><span class="muted-note">Passwords are stored as secure hashes.</span>
            </div>
            <div class="panel-body">
                <form method="post" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                    <input type="hidden" name="action" value="create">
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label" for="first_name">First name *</label><input class="form-control" id="first_name" name="first_name" maxlength="100" required></div>
                        <div class="col-md-3"><label class="form-label" for="last_name">Last name *</label><input class="form-control" id="last_name" name="last_name" maxlength="100" required></div>
                        <div class="col-md-2"><label class="form-label" for="middle_initial">Middle initial</label><input class="form-control" id="middle_initial" name="middle_initial" maxlength="2"></div>
                        <div class="col-md-2"><label class="form-label" for="extension_name">Extension</label><select class="form-select" id="extension_name" name="extension_name">
                                <option value="">None</option>
                                <option value="Jr">Jr</option>
                                <option value="I">I</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                            </select></div>
                        <div class="col-md-2"><label class="form-label" for="access">Account access *</label><select class="form-select" id="access" name="access" required>
                                <?php foreach ($roles as $role): ?><option value="<?= h($role) ?>" <?= $role === 'student' ? 'selected' : '' ?>><?= h(ucwords(str_replace('_', ' ', $role))) ?></option><?php endforeach; ?>
                            </select></div>
                        <div class="col-md-4 student-fields"><label class="form-label" for="department">Department <span class="student-required">*</span></label><input class="form-control" id="department" name="department" maxlength="100" placeholder="e.g. Computer Engineering"></div>
                        <div class="col-md-4 student-fields"><label class="form-label" for="year_section">Year &amp; section <span class="student-required">*</span></label><input class="form-control" id="year_section" name="year_section" maxlength="10" placeholder="e.g. 3-A"></div>
                        <div class="col-md-4 student-fields"><label class="form-label" for="student_id">Student ID <span class="student-required">*</span></label><input class="form-control" id="student_id" name="student_id" maxlength="50"></div>
                        <div class="col-md-4"><label class="form-label" for="email">Email address *</label><input type="email" class="form-control" id="email" name="email" maxlength="150" required></div>
                        <div class="col-md-4"><label class="form-label" for="username">Username *</label><input class="form-control" id="username" name="username" minlength="4" maxlength="50" pattern="[A-Za-z0-9_.-]+" required>
                            <div class="muted-note mt-1">4–50 characters: letters, numbers, underscore, period, hyphen.</div>
                        </div>
                        <div class="col-md-4"><label class="form-label" for="password">Initial password *</label><input type="password" class="form-control" id="password" name="password" minlength="8" maxlength="255" required>
                            <div class="muted-note mt-1">At least 8 characters.</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4"><button type="reset" class="btn btn-outline-light">Clear form</button><button type="submit" class="btn btn-primary"><i class="bi bi-person-check me-1"></i>Create account</button></div>
                </form>
            </div>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2><i class="bi bi-list-ul text-info me-2"></i>Registered accounts <span class="text-secondary fw-normal">(<?= $total ?>)</span></h2>
            </div>
            <div class="panel-body border-bottom" style="border-color:var(--border)!important">
                <form method="get" class="row g-2 align-items-end">
                    <div class="col-md-7"><label class="form-label" for="q">Search accounts</label><input class="form-control" id="q" name="q" value="<?= h($search) ?>" placeholder="Name, username, email, or student ID"></div>
                    <div class="col-md-3"><label class="form-label" for="filter-access">Access type</label><select class="form-select" id="filter-access" name="access">
                            <option value="">All access types</option>
                            <?php foreach ($roles as $role): ?><option value="<?= h($role) ?>" <?= $filterAccess === $role ? 'selected' : '' ?>><?= h(ucwords(str_replace('_', ' ', $role))) ?></option><?php endforeach; ?>
                        </select></div>
                    <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary flex-grow-1" type="submit"><i class="bi bi-search me-1"></i>Filter</button><a class="btn btn-outline-light" href="<?= h(strtok($_SERVER['REQUEST_URI'], '?')) ?>" title="Reset filters"><i class="bi bi-x-lg"></i></a></div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Username / Email</th>
                            <th>Student details</th>
                            <th>Current access</th>
                            <th>Change access</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$accounts): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-5"><i class="bi bi-person-x fs-3 d-block mb-2"></i>No accounts found.</td>
                            </tr>
                            <?php else: foreach ($accounts as $account):
                                $name = trim(
                                    ($account['first_name'] ?? '') . ' ' .
                                        (($account['middle_initial'] ?? '') !== '' ? $account['middle_initial'] . '. ' : '') .
                                        ($account['last_name'] ?? '') .
                                        (($account['extension_name'] ?? '') !== '' ? ' ' . $account['extension_name'] : '')
                                );
                                $role = strtolower((string)($account['access'] ?? 'student'));
                                $roleClass = in_array($role, $roles, true) ? $role : 'student';
                            ?>
                                <tr>
                                    <td><strong><?= h($name) ?></strong><span class="subtext">Account #<?= (int)$account['id'] ?> · <?= h($account['created_at'] ?? '') ?></span></td>
                                    <td><?= h($account['username']) ?><span class="subtext"><?= h($account['email']) ?></span></td>
                                    <td><?= h($account['student_id'] ?: '—') ?><span class="subtext"><?= h($account['department'] ?: '—') ?><?= !empty($account['year_section']) ? ' · ' . h($account['year_section']) : '' ?></span></td>
                                    <td><span class="role role-<?= h($roleClass) ?>"><?= h(ucwords(str_replace('_', ' ', $role))) ?></span></td>
                                    <td>
                                        <form method="post" class="d-flex gap-2 align-items-center">
                                            <input type="hidden" name="csrf_token" value="<?= h($csrf) ?>">
                                            <input type="hidden" name="action" value="update_access">
                                            <input type="hidden" name="account_id" value="<?= (int)$account['id'] ?>">
                                            <select class="form-select small-select" name="access" aria-label="Access for <?= h($name) ?>">
                                                <?php foreach ($roles as $option): ?><option value="<?= h($option) ?>" <?= $role === $option ? 'selected' : '' ?>><?= h(ucwords(str_replace('_', ' ', $option))) ?></option><?php endforeach; ?>
                                            </select>
                                            <button class="btn btn-sm btn-outline-info" type="submit" title="Save access" onclick="return confirm('Update access for this account?')"><i class="bi bi-check2"></i></button>
                                        </form>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1): ?>
                <div class="p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="muted-note">Page <?= $page ?> of <?= $totalPages ?></span>
                    <nav aria-label="Account pages">
                        <ul class="pagination pagination-sm mb-0">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?><li class="page-item <?= $p === $page ? 'active' : '' ?>"><a class="page-link" href="?<?= h(http_build_query(array_merge($_GET, ['page' => $p]))) ?>"><?= $p ?></a></li><?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </section>
        <p class="muted-note text-center">ETS-Async · Account role changes take effect according to your application's login and permission checks.</p>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const access = document.getElementById('access');
            const fields = ['department', 'year_section', 'student_id'].map(id => document.getElementById(id));

            function syncStudentFields() {
                const isStudent = access.value === 'student';
                fields.forEach(input => {
                    input.required = isStudent;
                    input.closest('.student-fields').style.opacity = isStudent ? '1' : '.58';
                });
                document.querySelectorAll('.student-required').forEach(label => {
                    label.style.display = isStudent ? 'inline' : 'none';
                });
            }
            access.addEventListener('change', syncStudentFields);
            document.querySelector('#create-account form')?.addEventListener('reset', () => setTimeout(syncStudentFields, 0));
            syncStudentFields();
        })();
    </script>
</body>

</html>