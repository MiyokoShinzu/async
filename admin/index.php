<?php
/* =========================================================
   ADMIN DASHBOARD - ETS-Async Learning Portal
   Improvements: live database statistics, working navigation,
   recent activities, resilient optional-table checks.
   ========================================================= */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) || ($_SESSION["user"]["access"] ?? "") !== "admin"
) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../src/connection.php';

$user = $_SESSION["user"];
$firstName = trim((string)($user["first_name"] ?? ""));
$lastName = trim((string)($user["last_name"] ?? ""));
$middleInitial = trim((string)($user["middle_initial"] ?? ""));
$extensionName = trim((string)($user["extension_name"] ?? ""));
$email = trim((string)($user["email"] ?? ""));
$username = trim((string)($user["username"] ?? ""));
$access = (string)($user["access"] ?? "admin");
$fullName = trim($firstName . ' ' . ($middleInitial !== '' ? $middleInitial . '. ' : '') . $lastName . ($extensionName !== '' ? ' ' . $extensionName : ''));
$initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

// The existing connection file in this project uses mysqli.
$dbConnection = isset($mysqli) && $mysqli instanceof mysqli ? $mysqli : (isset($db) && $db instanceof mysqli ? $db : null);
$dbAvailable = $dbConnection instanceof mysqli;

function dash_table_exists(?mysqli $db, string $table): bool
{
    if (!$db || !preg_match('/^[A-Za-z0-9_]+$/', $table)) return false;
    $safe = $db->real_escape_string($table);
    $result = $db->query("SHOW TABLES LIKE '{$safe}'");
    if (!$result) return false;
    $exists = $result->num_rows > 0;
    $result->free();
    return $exists;
}
function dash_count(?mysqli $db, string $table, string $where = ''): int
{
    if (!dash_table_exists($db, $table)) return 0;
    $result = $db->query("SELECT COUNT(*) AS total FROM `{$table}`" . ($where !== '' ? " WHERE {$where}" : ''));
    if (!$result) return 0;
    $row = $result->fetch_assoc();
    $result->free();
    return (int)($row['total'] ?? 0);
}
function dash_e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$students = $dbAvailable ? dash_count($dbConnection, 'accounts', "access='student'") : 0;
$accounts = $dbAvailable ? dash_count($dbConnection, 'accounts') : 0;
$readingActivities = $dbAvailable ? dash_count($dbConnection, 'reading_activity_table') : 0;
$lectures = $dbAvailable ? dash_count($dbConnection, 'lectures') : 0;
$exams = $dbAvailable ? (dash_count($dbConnection, 'exams') ?: dash_count($dbConnection, 'exam_table')) : 0;
$reports = $dbAvailable ? (dash_count($dbConnection, 'exam_attempts') + dash_count($dbConnection, 'exam_results') + dash_count($dbConnection, 'reading_activity_progress')) : 0;

$recentActivities = [];
if ($dbAvailable && dash_table_exists($dbConnection, 'reading_activity_table')) {
    $columns = [];
    $colResult = $dbConnection->query("SHOW COLUMNS FROM `reading_activity_table`");
    if ($colResult) {
        while ($col = $colResult->fetch_assoc()) $columns[] = $col['Field'];
        $colResult->free();
    }
    $idColumn = in_array('id', $columns, true) ? 'id' : (in_array('activity_id', $columns, true) ? 'activity_id' : null);
    if (in_array('title', $columns, true)) {
        $orderColumn = in_array('created_at', $columns, true) ? 'created_at' : ($idColumn ?: 'title');
        $selectId = $idColumn ? "`{$idColumn}` AS activity_id" : 'NULL AS activity_id';
        $selectStatus = in_array('status', $columns, true) ? '`status`' : "'unknown' AS status";
        $selectDate = in_array('created_at', $columns, true) ? '`created_at` AS created_at' : (in_array('start_date', $columns, true) ? '`start_date` AS created_at' : 'NULL AS created_at');
        $query = "SELECT `title`, {$selectId}, {$selectStatus}, {$selectDate} FROM `reading_activity_table` ORDER BY `{$orderColumn}` DESC LIMIT 5";
        $res = $dbConnection->query($query);
        if ($res) {
            while ($row = $res->fetch_assoc()) $recentActivities[] = $row;
            $res->free();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'globals/head.php'; ?>

<body>
    <?php include 'globals/sidebar.php'; ?>
    <?php include 'globals/topbar.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <div class="welcome-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h2>Welcome, <?= dash_e($firstName !== '' ? $firstName : 'Administrator') ?>!</h2>
                        <p class="mb-0">Your ETS-Async administration overview. Manage learners, learning materials, examinations, schedules, and reports from one place.</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-primary" href="students.php"><i class="bi bi-people me-1"></i> Manage Students</a>
                        <a class="btn btn-outline-primary" href="reading_activity_create.php"><i class="bi bi-plus-circle me-1"></i> Create Reading Activity</a>
                    </div>
                </div>
            </div>

            <?php if (!$dbAvailable): ?>
                <div class="alert alert-warning" role="alert"><strong>Database connection not detected.</strong> Check that <code>../src/connection.php</code> creates a mysqli connection named <code>$mysqli</code> or <code>$db</code>.</div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="stat-label">Total Students</div>
                        <div class="stat-value"><?= number_format($students) ?></div><a class="small" href="students.php">View students <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-person-vcard-fill"></i></div>
                        <div class="stat-label">Total Accounts</div>
                        <div class="stat-value"><?= number_format($accounts) ?></div><span class="small text-secondary">All account types</span>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
                        <div class="stat-label">Reading Activities</div>
                        <div class="stat-value"><?= number_format($readingActivities) ?></div><a class="small" href="reading_activities.php">Manage reading activities <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
                        <div class="stat-label">Recorded Learning Results</div>
                        <div class="stat-value"><?= number_format($reports) ?></div><a class="small" href="exam_monitor.php">Open exam monitor <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <section class="dashboard-card h-100">
                        <div class="dashboard-card-header"><i class="bi bi-lightning-charge me-2"></i>Quick Actions</div>
                        <div class="dashboard-card-body">
                            <a href="students.php" class="quick-action">
                                <div class="quick-action-icon"><i class="bi bi-people"></i></div>
                                <div>
                                    <div class="quick-action-title">Manage Students</div>
                                    <div class="quick-action-text">View, search, update, and maintain student records.</div>
                                </div><i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                            <a href="reading_activities.php" class="quick-action">
                                <div class="quick-action-icon"><i class="bi bi-journal-check"></i></div>
                                <div>
                                    <div class="quick-action-title">Reading Activities & Progress</div>
                                    <div class="quick-action-text">Manage reading tasks and review available student progress.</div>
                                </div><i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                            <a href="lectures.php" class="quick-action">
                                <div class="quick-action-icon"><i class="bi bi-play-btn"></i></div>
                                <div>
                                    <div class="quick-action-title">Manage Lectures</div>
                                    <div class="quick-action-text">Organize lecture materials and view lecture activity.</div>
                                </div><i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                            <a href="exam_monitor.php" class="quick-action">
                                <div class="quick-action-icon"><i class="bi bi-clipboard-data"></i></div>
                                <div>
                                    <div class="quick-action-title">Exam Monitor & Reports</div>
                                    <div class="quick-action-text">Review exam activity and available results.</div>
                                </div><i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                            <a href="exam_manager.php" class="quick-action">
                                <div class="quick-action-icon"><i class="bi bi-file-earmark-text"></i></div>
                                <div>
                                    <div class="quick-action-title">Manage Exams</div>
                                    <div class="quick-action-text">Create and maintain examination content.</div>
                                </div><i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                            <a href="question_management.php" class="quick-action">
                                <div class="quick-action-icon"><i class="bi bi-patch-question"></i></div>
                                <div>
                                    <div class="quick-action-title">Question Bank</div>
                                    <div class="quick-action-text">Manage questions used by practice and assessment activities.</div>
                                </div><i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                            <a href="academic_calendar_management.php" class="quick-action">
                                <div class="quick-action-icon"><i class="bi bi-calendar3"></i></div>
                                <div>
                                    <div class="quick-action-title">Academic Calendar</div>
                                    <div class="quick-action-text">Manage important dates and academic events.</div>
                                </div><i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                            <a href="activity_monitor.php" class="quick-action">
                                <div class="quick-action-icon"><i class="bi bi-activity"></i></div>
                                <div>
                                    <div class="quick-action-title">Activity Monitor</div>
                                    <div class="quick-action-text">Inspect learning activity and engagement records.</div>
                                </div><i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                            <a href="support_rankings.php" class="quick-action">
                                <div class="quick-action-icon"><i class="bi bi-life-preserver"></i></div>
                                <div>
                                    <div class="quick-action-title">Support Center</div>
                                    <div class="quick-action-text">Review support-related rankings and activity.</div>
                                </div><i class="bi bi-chevron-right ms-auto"></i>
                            </a>
                        </div>
                    </section>
                </div>
                <div class="col-lg-5">
                    <section class="dashboard-card mb-4">
                        <div class="dashboard-card-header"><i class="bi bi-person-badge me-2"></i>Administrator Information</div>
                        <div class="dashboard-card-body">
                            <div class="information-item">
                                <div class="information-label">Full Name</div>
                                <div class="information-value"><?= dash_e($fullName !== '' ? $fullName : 'Not provided') ?></div>
                            </div>
                            <div class="information-item">
                                <div class="information-label">Username</div>
                                <div class="information-value"><?= dash_e($username !== '' ? $username : 'Not provided') ?></div>
                            </div>
                            <div class="information-item">
                                <div class="information-label">Email Address</div>
                                <div class="information-value"><?= dash_e($email !== '' ? $email : 'Not provided') ?></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3"><span class="text-secondary">Account Type</span><span class="badge text-bg-primary"><?= dash_e(ucfirst($access)) ?></span></div>
                        </div>
                    </section>
                    <section class="dashboard-card">
                        <div class="dashboard-card-header"><i class="bi bi-clock-history me-2"></i>Recent Reading Activities</div>
                        <div class="dashboard-card-body">
                            <?php if (!$recentActivities): ?>
                                <p class="text-secondary mb-3">No reading activities were found, or the reading activity table is not available.</p>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recentActivities as $activity): ?>
                                        <div class="list-group-item px-0">
                                            <div class="d-flex justify-content-between gap-2 align-items-start">
                                                <div>
                                                    <div class="fw-semibold"><?= dash_e($activity['title'] ?? 'Untitled activity') ?></div>
                                                    <?php if (!empty($activity['created_at'])): ?><small class="text-secondary"><?= dash_e($activity['created_at']) ?></small><?php endif; ?>
                                                </div>
                                                <span class="badge <?= strtolower((string)($activity['status'] ?? '')) === 'active' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= dash_e(ucfirst((string)($activity['status'] ?? 'Unknown'))) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <a href="reading_activities.php" class="btn btn-sm btn-outline-primary mt-3">View all reading activities</a>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </main>
    <?php include 'globals/scripts.php'; ?>
</body>

</html>