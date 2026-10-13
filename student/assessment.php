<?php
/* ETS-Async Learning Portal — Student Exam Portal and History */
// Keep exam start/expiry calculations consistent with the portal's Philippines timezone.
date_default_timezone_set('Asia/Manila');
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_trans_sid', '0');
    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
header('X-Content-Type-Options: nosniff');
// YouTube embeds require a referrer/origin for playback in many browsers.
// This policy sends only the site origin cross-origin while preserving privacy.
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Resolve shared files from the project root even though this page is in /student.
$projectRoot = dirname(__DIR__);
$checksCandidates = [
    $projectRoot . '/globals/checks.php',
    __DIR__ . '/globals/checks.php',
    dirname($projectRoot) . '/globals/checks.php',
];
foreach ($checksCandidates as $checksPath) {
    if (is_file($checksPath)) {
        require_once $checksPath;
        break;
    }
}

// This page is only for authenticated accounts whose access role is student.
$user = $_SESSION['user'] ?? [];
if (!is_array($user)) $user = [];
$loggedIn = ($_SESSION['logged_in'] ?? false) === true || in_array(strtolower((string)($_SESSION['logged_in'] ?? '')), ['1', 'true', 'yes'], true);
$access = strtolower(trim((string)($user['access'] ?? $user['role'] ?? '')));
if (!$loggedIn || !is_array($user) || $access !== 'student') {
    header('Location: ' . (is_file($projectRoot . '/login.php') ? '../login.php' : '/login.php'));
    exit;
}

$connectionCandidates = [
    $projectRoot . '/src/connection.php',
    __DIR__ . '/../src/connection.php',
    __DIR__ . '/src/connection.php',
];
$connectionPath = null;
foreach ($connectionCandidates as $candidate) {
    if (is_file($candidate)) {
        $connectionPath = $candidate;
        break;
    }
}
if ($connectionPath === null) {
    http_response_code(500);
    exit('Database connection file not found. Expected src/connection.php in the project folder.');
}
require_once $connectionPath;
$db = $mysqli ?? $db ?? $conn ?? $con ?? null;
if (!$db instanceof mysqli) {
    http_response_code(500);
    exit('Database connection is unavailable. Check src/connection.php.');
}

$commonCandidates = [
    $projectRoot . '/exam_common.php',
    __DIR__ . '/exam_common.php',
];
$commonPath = null;
foreach ($commonCandidates as $candidate) {
    if (is_file($candidate)) {
        $commonPath = $candidate;
        break;
    }
}
if ($commonPath === null) {
    http_response_code(500);
    exit('exam_common.php was not found. Place it in the project root beside the globals and src folders.');
}
require_once $commonPath;

// Ensure the solution table exists for post-submission review.
if (!$db->query("CREATE TABLE IF NOT EXISTS question_solutions (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    solution_type ENUM('text','simple_text','rich_text','image','youtube') NOT NULL,
    solution_content MEDIUMTEXT NOT NULL,
    display_order TINYINT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_question_solutions_question (question_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci")) {
    http_response_code(500);
    error_log('Could not prepare question_solutions table: ' . $db->error);
    exit('The exam portal could not prepare its database. Please contact the administrator.');
}

// Add the account key used to securely associate attempts with the signed-in student.
$col = $db->query("SHOW COLUMNS FROM exam_attempts LIKE 'student_account_key'");
if ($col && $col->num_rows === 0) {
    if (!$db->query("ALTER TABLE exam_attempts ADD COLUMN student_account_key VARCHAR(190) NULL AFTER student_number, ADD KEY idx_attempt_student_key (student_account_key)")) {
        http_response_code(500);
        error_log('Could not upgrade exam_attempts: ' . $db->error);
        exit('The exam portal could not prepare its database. Please contact the administrator.');
    }
}
if ($col) $col->free();

// Store lightweight exam-session activity counters for the instructor's records.
foreach (
    [
        'tab_switch_count' => "ALTER TABLE exam_attempts ADD COLUMN tab_switch_count INT UNSIGNED NOT NULL DEFAULT 0",
        'max_open_tabs' => "ALTER TABLE exam_attempts ADD COLUMN max_open_tabs INT UNSIGNED NOT NULL DEFAULT 1",
    ] as $columnName => $alterSql
) {
    $checkColumn = $db->query("SHOW COLUMNS FROM exam_attempts LIKE '" . $db->real_escape_string($columnName) . "'");
    if ($checkColumn && $checkColumn->num_rows === 0 && !$db->query($alterSql)) {
        http_response_code(500);
        error_log('Could not prepare exam activity tracking: ' . $db->error);
        exit('The exam portal could not prepare exam activity tracking. Please contact the administrator.');
    }
    if ($checkColumn) $checkColumn->free();
}

$firstName = trim((string)($user['first_name'] ?? ''));
$lastName = trim((string)($user['last_name'] ?? ''));
$middleInitial = trim((string)($user['middle_initial'] ?? ''));
$extensionName = trim((string)($user['extension_name'] ?? ''));
$fullName = trim($firstName . ' ' . ($middleInitial !== '' ? $middleInitial . '. ' : '') . $lastName . ($extensionName !== '' ? ' ' . $extensionName : ''));
if ($fullName === '') $fullName = trim((string)($user['username'] ?? 'Student'));
$studentNumber = trim((string)($user['student_id'] ?? ''));
if ($studentNumber === '') $studentNumber = null;
if (trim((string)($user['student_id'] ?? '')) !== '') $accountKey = 'sid:' . trim((string)$user['student_id']);
elseif (trim((string)($user['username'] ?? '')) !== '') $accountKey = 'user:' . trim((string)$user['username']);
else $accountKey = 'email:' . trim((string)($user['email'] ?? ''));
$accountKey = substr($accountKey, 0, 190);

// Profile pictures follow the same session-user format used by globals/topbar.php.
// The leaderboard additionally resolves each student's stored image from the account table.
function st_normalize_profile_path(string $image): string
{
    $image = trim(str_replace('\\', '/', $image));
    if ($image === '' || preg_match('~^(?:javascript:|data:)~i', $image)) return '';
    $image = preg_replace('~^(?:\./)+~', '', $image);

    // The shared profile-photo storage belongs to the main vertigation.com domain,
    // not async.vertigation.com. Handle full URLs and both relative path formats.
    if (preg_match('~^https?://~i', $image)) {
        $path = (string)(parse_url($image, PHP_URL_PATH) ?? '');
        if (strpos($path, '/shared/uploads/profile_photos/') === 0) {
            return 'https://vertigation.com' . $path;
        }
        return $image;
    }
    if (strpos($image, '//') === 0) return 'https:' . $image;
    if (strpos($image, '/shared/uploads/profile_photos/') === 0) return 'https://vertigation.com' . $image;
    if (strpos($image, 'shared/uploads/profile_photos/') === 0) return 'https://vertigation.com/' . $image;
    if (strpos($image, 'async/shared/uploads/profile_photos/') === 0) {
        return 'https://vertigation.com/' . substr($image, strlen('async/'));
    }
    if (strpos($image, '/') === 0) return $image;
    if (strpos($image, 'shared/') === 0) return 'https://vertigation.com/' . $image;
    if (strpos($image, 'uploads/profile_photos/') === 0) return 'https://vertigation.com/shared/' . $image;
    return 'https://vertigation.com/shared/' . ltrim($image, '/');
}
function st_profile_image(mysqli $db, array $student): string
{
    static $lookup = null;
    if ($lookup === null) {
        $lookup = ['table' => null, 'match' => null, 'image' => null];
        foreach (['accounts', 'users', 'user_accounts', 'students'] as $table) {
            $tableResult = $db->query("SHOW TABLES LIKE '" . $db->real_escape_string($table) . "'");
            if (!$tableResult || $tableResult->num_rows < 1) {
                if ($tableResult) $tableResult->free();
                continue;
            }
            $tableResult->free();
            $columnsResult = $db->query("SHOW COLUMNS FROM `{$table}`");
            if (!$columnsResult) continue;
            $columns = [];
            while ($column = $columnsResult->fetch_assoc()) $columns[] = (string)$column['Field'];
            $columnsResult->free();
            $imageCol = null;
            foreach (($table === 'accounts' ? ['profile_photo', 'profile_picture', 'profile_image', 'profile_img', 'avatar_url', 'avatar', 'photo_url', 'photo', 'image_path', 'profile_pic', 'picture', 'image', 'user_image'] : ['profile_photo', 'profile_picture', 'profile_image', 'profile_img', 'avatar_url', 'avatar', 'photo_url', 'photo', 'image_path', 'profile_pic', 'picture', 'image', 'user_image']) as $candidate) {
                if (in_array($candidate, $columns, true)) {
                    $imageCol = $candidate;
                    break;
                }
            }
            $matchCol = null;
            foreach (['student_id', 'student_number', 'username', 'email', 'account_id', 'user_id', 'id'] as $candidate) {
                if (in_array($candidate, $columns, true)) {
                    $matchCol = $candidate;
                    break;
                }
            }
            if ($imageCol && $matchCol) {
                $lookup = ['table' => $table, 'match' => $matchCol, 'image' => $imageCol];
                break;
            }
        }
    }
    // If this is the signed-in student, use the same picture fields that may be present in the topbar session.
    $sessionUser = $_SESSION['user'] ?? [];
    $studentNumber = trim((string)($student['student_number'] ?? $student['student_id'] ?? ''));
    $studentKey = trim((string)($student['student_account_key'] ?? ''));
    $sessionStudentId = trim((string)($sessionUser['student_id'] ?? ''));
    $sessionUsername = trim((string)($sessionUser['username'] ?? ''));
    $sameUser = ($studentNumber !== '' && $sessionStudentId !== '' && $studentNumber === $sessionStudentId)
        || ($studentKey !== '' && $sessionUsername !== '' && $studentKey === 'user:' . $sessionUsername)
        || ($studentKey !== '' && !empty($sessionUser['email']) && $studentKey === 'email:' . trim((string)$sessionUser['email']));
    if ($sameUser) {
        foreach (['profile_photo', 'profile_picture', 'profile_image', 'profile_img', 'avatar_url', 'avatar', 'photo_url', 'photo', 'image_path', 'profile_pic', 'picture', 'image', 'user_image'] as $field) {
            $sessionImage = st_normalize_profile_path((string)($sessionUser[$field] ?? ''));
            if ($sessionImage !== '') return $sessionImage;
        }
    }
    if (!$lookup['table'] || !$lookup['match'] || !$lookup['image']) return '';
    $matchValues = [];
    foreach (['student_number', 'student_id', 'username', 'email'] as $field) {
        if (!empty($student[$field])) $matchValues[] = trim((string)$student[$field]);
    }
    if ($studentKey !== '' && strpos($studentKey, ':') !== false) $matchValues[] = substr($studentKey, strpos($studentKey, ':') + 1);
    $matchValues = array_values(array_unique(array_filter($matchValues, static fn($v) => $v !== '')));
    if (!$matchValues) return '';
    $table = '`' . str_replace('`', '', $lookup['table']) . '`';
    $matchCol = '`' . str_replace('`', '', $lookup['match']) . '`';
    $imageCol = '`' . str_replace('`', '', $lookup['image']) . '`';
    foreach ($matchValues as $matchValue) {
        $stmt = $db->prepare("SELECT {$imageCol} AS profile_image FROM {$table} WHERE {$matchCol}=? LIMIT 1");
        if (!$stmt) continue;
        $stmt->bind_param('s', $matchValue);
        if (!$stmt->execute()) {
            $stmt->close();
            continue;
        }
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        if ($result) $result->free();
        $stmt->close();
        $image = st_normalize_profile_path((string)($row['profile_image'] ?? ''));
        if ($image !== '') return $image;
    }
    return '';
}

// Normalize the signed-in user's picture aliases for the shared topbar include.
// Existing session fields are preserved; profile_picture is populated only when a usable image exists.
foreach (['profile_photo', 'profile_picture', 'profile_image', 'profile_img', 'avatar_url', 'avatar', 'photo_url', 'photo', 'image_path', 'profile_pic', 'picture', 'image', 'user_image'] as $profileField) {
    $topbarImage = st_normalize_profile_path((string)($user[$profileField] ?? ''));
    if ($topbarImage !== '') {
        $user['profile_picture'] = $topbarImage;
        $user['profile_photo'] = $topbarImage;
        $_SESSION['user']['profile_picture'] = $topbarImage;
        $_SESSION['user']['profile_photo'] = $topbarImage;
        break;
    }
}
if (empty($user['profile_picture'])) {
    $topbarImage = st_profile_image($db, [
        'student_number' => (string)($user['student_id'] ?? ''),
        'student_account_key' => $accountKey,
        'username' => (string)($user['username'] ?? ''),
        'email' => (string)($user['email'] ?? ''),
    ]);
    if ($topbarImage !== '') {
        $user['profile_picture'] = $topbarImage;
        $user['profile_photo'] = $topbarImage;
        $_SESSION['user']['profile_picture'] = $topbarImage;
        $_SESSION['user']['profile_photo'] = $topbarImage;
    }
}

// Exact variables used by the supplied shared topbar code.
$topbarProfilePhoto = '';
$topbarProfilePhotoURL = '';
$topbarStudentId = trim((string)($user['student_id'] ?? ''));
$initials = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $fullName), 0, 2));
if ($initials === '') $initials = 'ST';
if ($topbarStudentId !== '') {
    $topbarPhotoStmt = $mysqli->prepare('SELECT profile_photo FROM accounts WHERE student_id = ? LIMIT 1');
    if ($topbarPhotoStmt) {
        $topbarPhotoStmt->bind_param('s', $topbarStudentId);
        if ($topbarPhotoStmt->execute()) {
            $topbarPhotoResult = $topbarPhotoStmt->get_result();
            $topbarPhotoRow = $topbarPhotoResult ? $topbarPhotoResult->fetch_assoc() : null;
            if ($topbarPhotoResult) $topbarPhotoResult->free();
            $topbarProfilePhoto = trim((string)($topbarPhotoRow['profile_photo'] ?? ''));
        }
        $topbarPhotoStmt->close();
    }
}
if ($topbarProfilePhoto === '') $topbarProfilePhoto = (string)($user['profile_photo'] ?? $user['profile_picture'] ?? '');
$topbarProfilePhotoURL = st_normalize_profile_path($topbarProfilePhoto);
if ($topbarProfilePhotoURL !== '') {
    $user['profile_photo'] = $topbarProfilePhotoURL;
    $user['profile_picture'] = $topbarProfilePhotoURL;
    $_SESSION['user']['profile_photo'] = $topbarProfilePhotoURL;
    $_SESSION['user']['profile_picture'] = $topbarProfilePhotoURL;
}

$error = '';
$notice = '';
$exam = null;
$attempt = null;
$questions = [];
$view = 'home';

function st_solution_embed(string $type, string $content): string
{
    $content = trim($content);
    if ($type === 'image') {
        $src = ex_media_url('image', $content);
        if ($src !== '') return '<div class="solution-embed ratio ratio-16x9"><iframe src="' . ex_e($src) . '" title="Solution image" loading="lazy" allow="autoplay" referrerpolicy="no-referrer"></iframe></div><a class="small" target="_blank" rel="noopener noreferrer" href="' . ex_e($content) . '">Open image</a>';
        return '<a target="_blank" rel="noopener noreferrer" href="' . ex_e($content) . '">Open solution image</a>';
    }
    if ($type === 'youtube') {
        $src = ex_media_url('youtube', $content);
        if ($src !== '') return '<div class="solution-embed ratio ratio-16x9"><iframe src="' . ex_e($src) . '" title="Solution video" loading="lazy" allow="autoplay; encrypted-media; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div><a class="small" target="_blank" rel="noopener noreferrer" href="' . ex_e($content) . '">Open video on YouTube</a>';
        return '<a target="_blank" rel="noopener noreferrer" href="' . ex_e($content) . '">Open solution video</a>';
    }
    if ($type === 'rich_text') return '<div class="text-rich">' . ex_rich($content) . '</div>';
    return '<div class="solution-plain">' . nl2br(ex_e($content)) . '</div>';
}

function st_grade(mysqli $db, int $attemptId, array $answers, bool $timedOut = false): array
{
    $rows = ex_rows($db, 'SELECT * FROM exam_attempts WHERE id=?', 'i', [$attemptId]);
    if (!$rows) throw new Exception('Exam attempt not found.');
    $a = $rows[0];
    if ($a['status'] !== 'in_progress') return ['score' => (int)$a['score'], 'total' => (int)$a['total_items'], 'status' => $a['status']];
    if (strtotime((string)$a['expires_at']) <= time()) $timedOut = true;
    $set = ex_rows($db, 'SELECT question_id FROM exam_set_questions WHERE exam_id=? AND variant=? ORDER BY position_no', 'is', [(int)$a['exam_id'], $a['variant']]);
    $score = 0;
    $db->begin_transaction();
    try {
        foreach ($set as $item) {
            $qid = (int)$item['question_id'];
            $picked = isset($answers[$qid]) ? trim((string)$answers[$qid]) : '';
            if ($picked !== '' && !ex_rows($db, 'SELECT choice_label FROM question_choices WHERE question_id=? AND choice_label=?', 'is', [$qid, $picked])) $picked = '';
            $cr = ex_rows($db, 'SELECT choice_label FROM question_choices WHERE question_id=? AND is_correct=1 LIMIT 1', 'i', [$qid]);
            $correct = (string)($cr[0]['choice_label'] ?? '');
            $isCorrect = ($picked !== '' && $picked === $correct);
            if ($isCorrect) $score++;
            ex_exec($db, 'INSERT INTO exam_attempt_answers(attempt_id,question_id,selected_choice,correct_choice,is_correct) VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE selected_choice=VALUES(selected_choice),correct_choice=VALUES(correct_choice),is_correct=VALUES(is_correct)', 'iissi', [$attemptId, $qid, ($picked !== '' ? $picked : null), ($correct !== '' ? $correct : null), ($isCorrect ? 1 : 0)]);
        }
        $status = $timedOut ? 'timed_out' : 'submitted';
        ex_exec($db, "UPDATE exam_attempts SET status=?,score=?,submitted_at=NOW(),last_seen_at=NOW() WHERE id=? AND status='in_progress'", 'sii', [$status, $score, $attemptId]);
        $db->commit();
        return ['score' => $score, 'total' => count($set), 'status' => $status];
    } catch (Throwable $e) {
        $db->rollback();
        throw $e;
    }
}

function st_owned_attempt(mysqli $db, int $attemptId, string $key, ?string $studentNumber): array
{
    if ($studentNumber !== null && $studentNumber !== '') {
        $rows = ex_rows($db, 'SELECT a.*,e.title,e.instructions,e.time_limit_minutes,e.test_code,e.status AS exam_status FROM exam_attempts a JOIN exams e ON e.id=a.exam_id WHERE a.id=? AND (a.student_account_key=? OR (a.student_account_key IS NULL AND a.student_number=?))', 'iss', [$attemptId, $key, $studentNumber]);
    } else {
        $rows = ex_rows($db, 'SELECT a.*,e.title,e.instructions,e.time_limit_minutes,e.test_code,e.status AS exam_status FROM exam_attempts a JOIN exams e ON e.id=a.exam_id WHERE a.id=? AND a.student_account_key=?', 'is', [$attemptId, $key]);
    }
    if (!$rows) throw new Exception('That attempt was not found in your student account.');
    return $rows[0];
}

/** Return whether this student has ever started this exam, including legacy attempts. */
function st_has_exam_attempt(mysqli $db, int $examId, string $key, ?string $studentNumber): bool
{
    if ($studentNumber !== null && $studentNumber !== '') {
        $rows = ex_rows($db, 'SELECT id FROM exam_attempts WHERE exam_id=? AND (student_account_key=? OR (student_account_key IS NULL AND student_number=?)) LIMIT 1', 'iss', [$examId, $key, $studentNumber]);
    } else {
        $rows = ex_rows($db, 'SELECT id FROM exam_attempts WHERE exam_id=? AND student_account_key=? LIMIT 1', 'is', [$examId, $key]);
    }
    return !empty($rows);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!hash_equals($examCsrf, (string)($_POST['csrf'] ?? ''))) throw new Exception('Session expired. Refresh the page and try again.');
        $action = (string)($_POST['action'] ?? '');
        if ($action === 'record_proctoring') {
            $attemptId = (int)($_POST['attempt_id'] ?? 0);
            $a = st_owned_attempt($db, $attemptId, $accountKey, $studentNumber);
            if ($a['status'] !== 'in_progress') {
                http_response_code(409);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false]);
                exit;
            }
            $switches = max(0, min(100000, (int)($_POST['tab_switch_count'] ?? 0)));
            $openTabs = max(1, min(100, (int)($_POST['max_open_tabs'] ?? 1)));
            // Update the existing attempt row; keep the highest counters reported by any tab.
            $stmt = $db->prepare("UPDATE exam_attempts SET tab_switch_count=GREATEST(COALESCE(tab_switch_count,0),?), max_open_tabs=GREATEST(COALESCE(max_open_tabs,1),?), last_seen_at=NOW() WHERE id=? AND status='in_progress'");
            if (!$stmt) {
                error_log('Exam activity update prepare failed: ' . $db->error);
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'database_prepare_failed']);
                exit;
            }
            $stmt->bind_param('iii', $switches, $openTabs, $attemptId);
            $saved = $stmt->execute();
            if (!$saved) error_log('Exam activity update failed: ' . $stmt->error);
            $stmt->close();
            if (!$saved) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode(['ok' => false, 'error' => 'database_update_failed']);
                exit;
            }
            header('Content-Type: application/json');
            echo json_encode(['ok' => true]);
            exit;
        } elseif ($action === 'enter_code') {
            $code = strtoupper(trim((string)($_POST['test_code'] ?? '')));
            $rows = ex_rows($db, "SELECT * FROM exams WHERE test_code=? AND status='published'", 's', [$code]);
            if (!$rows) throw new Exception('That test code is invalid or the exam is not currently available.');
            $requestedExamId = (int)$rows[0]['id'];
            if (st_has_exam_attempt($db, $requestedExamId, $accountKey, $studentNumber)) {
                throw new Exception('You have already accessed this exam. Each student account may take this exam only once.');
            }
            $_SESSION['student_exam_code_id'] = $requestedExamId;
            header('Location: assessment.php?instructions=1');
            exit;
        } elseif ($action === 'start_exam') {
            $examId = (int)($_POST['exam_id'] ?? 0);
            if ((int)($_SESSION['student_exam_code_id'] ?? 0) !== $examId) throw new Exception('Enter the test code again before starting.');
            if (st_has_exam_attempt($db, $examId, $accountKey, $studentNumber)) {
                unset($_SESSION['student_exam_code_id']);
                throw new Exception('You have already accessed this exam. Each student account may take this exam only once.');
            }
            $rows = ex_rows($db, "SELECT * FROM exams WHERE id=? AND status='published'", 'i', [$examId]);
            if (!$rows) throw new Exception('This exam is no longer available.');
            $exam = $rows[0];
            $variantsRows = ex_rows($db, 'SELECT variant,COUNT(*) AS total FROM exam_set_questions WHERE exam_id=? GROUP BY variant', 'i', [$examId]);
            $variants = [];
            foreach ($variantsRows as $vr) if ((int)$vr['total'] === (int)$exam['question_count']) $variants[] = $vr['variant'];
            if (!$variants) throw new Exception('This exam has not been finalized correctly. Contact your instructor.');
            $variant = $variants[random_int(0, count($variants) - 1)];
            $durationMinutes = (int)($exam['time_limit_minutes'] ?? 0);
            if ($durationMinutes < 1) {
                throw new Exception('This exam has an invalid time limit. Please contact your instructor before starting.');
            }
            $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Manila'));
            $expires = $now->modify('+' . $durationMinutes . ' minutes');
            ex_exec($db, "INSERT INTO exam_attempts(exam_id,student_name,student_number,student_account_key,variant,started_at,expires_at,last_seen_at,status,total_items) VALUES(?,?,?,?,?,?,?,NOW(),'in_progress',?)", 'issssssi', [$examId, $fullName, $studentNumber, $accountKey, $variant, $now->format('Y-m-d H:i:s'), $expires->format('Y-m-d H:i:s'), (int)$exam['question_count']]);
            $attemptId = (int)$db->insert_id;
            $_SESSION['student_exam_attempt_ids'][$attemptId] = true;
            unset($_SESSION['student_exam_code_id']);
            header('Location: assessment.php?attempt=' . $attemptId);
            exit;
        } elseif ($action === 'submit_exam') {
            $attemptId = (int)($_POST['attempt_id'] ?? 0);
            $a = st_owned_attempt($db, $attemptId, $accountKey, $studentNumber);
            if ($a['status'] === 'in_progress') st_grade($db, $attemptId, is_array($_POST['answers'] ?? null) ? $_POST['answers'] : [], strtotime((string)$a['expires_at']) <= time());
            header('Location: assessment.php?attempt=' . $attemptId . '&submitted=1');
            exit;
        } else throw new Exception('Unknown action.');
    }

    if (isset($_GET['attempt'])) {
        $attemptId = (int)$_GET['attempt'];
        $attempt = st_owned_attempt($db, $attemptId, $accountKey, $studentNumber);
        $view = 'attempt';
        if ($attempt['status'] === 'in_progress' && strtotime((string)$attempt['expires_at']) <= time()) {
            st_grade($db, $attemptId, [], true);
            $attempt = st_owned_attempt($db, $attemptId, $accountKey, $studentNumber);
        }
        if ($attempt['status'] === 'in_progress') {
            ex_exec($db, 'UPDATE exam_attempts SET last_seen_at=NOW() WHERE id=?', 'i', [$attemptId]);
            $questions = ex_rows($db, 'SELECT s.position_no,s.question_id,s.choice_order_json,q.question_text FROM exam_set_questions s JOIN question_bank q ON q.id=s.question_id WHERE s.exam_id=? AND s.variant=? ORDER BY s.position_no', 'is', [(int)$attempt['exam_id'], $attempt['variant']]);
        }
    } elseif (isset($_GET['instructions']) && (int)($_SESSION['student_exam_code_id'] ?? 0) > 0) {
        $rows = ex_rows($db, "SELECT * FROM exams WHERE id=? AND status='published'", 'i', [(int)$_SESSION['student_exam_code_id']]);
        if ($rows) {
            $exam = $rows[0];
            $view = 'instructions';
        }
    } elseif (isset($_GET['review'])) {
        $attemptId = (int)$_GET['review'];
        $attempt = st_owned_attempt($db, $attemptId, $accountKey, $studentNumber);
        if ($attempt['status'] === 'in_progress') throw new Exception('Solutions are available after the exam has been submitted.');
        $view = 'review';
        $questions = ex_rows($db, 'SELECT s.position_no,s.question_id,q.question_text,q.explanation,a.selected_choice,a.correct_choice,a.is_correct FROM exam_set_questions s JOIN question_bank q ON q.id=s.question_id LEFT JOIN exam_attempt_answers a ON a.question_id=s.question_id AND a.attempt_id=? WHERE s.exam_id=? AND s.variant=? ORDER BY s.position_no', 'iis', [$attemptId, (int)$attempt['exam_id'], $attempt['variant']]);
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}
if (isset($_GET['submitted'])) $notice = 'Your exam has been submitted and your result has been saved.';

// Student history: attempts created by this portal, plus older attempts matched by the student's number.
if ($studentNumber !== null && $studentNumber !== '') {
    $history = ex_rows($db, 'SELECT a.id,a.exam_id,a.student_name,a.variant,a.started_at,a.submitted_at,a.status,a.score,a.total_items,a.tab_switch_count,e.title,e.test_code FROM exam_attempts a JOIN exams e ON e.id=a.exam_id WHERE a.student_account_key=? OR (a.student_account_key IS NULL AND a.student_number=?) ORDER BY a.started_at DESC LIMIT 100', 'ss', [$accountKey, $studentNumber]);
} else {
    $history = ex_rows($db, 'SELECT a.id,a.exam_id,a.student_name,a.variant,a.started_at,a.submitted_at,a.status,a.score,a.total_items,a.tab_switch_count,e.title,e.test_code FROM exam_attempts a JOIN exams e ON e.id=a.exam_id WHERE a.student_account_key=? ORDER BY a.started_at DESC LIMIT 100', 's', [$accountKey]);
}

// Build a separate leaderboard for each exam represented in this student's history/current view.
// Show every completed taker; one best result per student per exam protects against legacy duplicate attempts.
$leaderboards = [];
$examIdsForBoards = [];
foreach ($history as $historyRow) {
    $historyExamId = (int)($historyRow['exam_id'] ?? 0);
    if ($historyExamId > 0) $examIdsForBoards[$historyExamId] = ['id' => $historyExamId, 'title' => (string)$historyRow['title']];
}
if ($attempt && !empty($attempt['exam_id'])) {
    $currentExamId = (int)$attempt['exam_id'];
    $examIdsForBoards[$currentExamId] = ['id' => $currentExamId, 'title' => (string)($attempt['title'] ?? 'Exam')];
}
if ($exam && !empty($exam['id'])) {
    $currentExamId = (int)$exam['id'];
    $examIdsForBoards[$currentExamId] = ['id' => $currentExamId, 'title' => (string)($exam['title'] ?? 'Exam')];
}
foreach ($examIdsForBoards as $boardExam) {
    $examIdForBoard = (int)$boardExam['id'];
    try {
        $leaderRows = ex_rows($db, "SELECT a.student_name,a.student_number,a.student_account_key,a.score,a.total_items,a.started_at,a.submitted_at
            FROM exam_attempts a
            WHERE a.exam_id=? AND a.status IN ('submitted','timed_out') AND a.score IS NOT NULL AND a.total_items > 0
            ORDER BY (a.score / a.total_items) DESC, a.score DESC, a.submitted_at ASC", 'i', [$examIdForBoard]);
        $seenStudents = [];
        $rankedTakers = [];
        foreach ($leaderRows as $lr) {
            $identity = trim((string)($lr['student_account_key'] ?? ''));
            if ($identity === '') $identity = 'sid:' . trim((string)($lr['student_number'] ?? ''));
            if ($identity === 'sid:' || $identity === '') $identity = 'name:' . strtolower(trim((string)($lr['student_name'] ?? '')));
            if ($identity === 'name:' || isset($seenStudents[$identity])) continue;
            $seenStudents[$identity] = true;
            $lr['_percentage'] = (int)$lr['total_items'] > 0 ? ((int)$lr['score'] / (int)$lr['total_items']) : 0;
            $startedTs = !empty($lr['started_at']) ? strtotime((string)$lr['started_at']) : false;
            $submittedTs = !empty($lr['submitted_at']) ? strtotime((string)$lr['submitted_at']) : false;
            $lr['_time_taken_seconds'] = ($startedTs !== false && $submittedTs !== false && $submittedTs >= $startedTs) ? ($submittedTs - $startedTs) : null;
            $lr['_profile_image'] = st_profile_image($db, $lr);
            $rankedTakers[] = $lr;
        }
        // Rank by score percentage, then raw score; for equal scores, the fastest valid completion wins.
        usort($rankedTakers, static function (array $a, array $b): int {
            $pctCompare = ((float)$b['_percentage']) <=> ((float)$a['_percentage']);
            if ($pctCompare !== 0) return $pctCompare;
            $scoreCompare = ((int)$b['score']) <=> ((int)$a['score']);
            if ($scoreCompare !== 0) return $scoreCompare;
            $timeA = $a['_time_taken_seconds'];
            $timeB = $b['_time_taken_seconds'];
            if ($timeA === null && $timeB !== null) return 1;
            if ($timeB === null && $timeA !== null) return -1;
            if ($timeA !== null && $timeB !== null && $timeA !== $timeB) return $timeA <=> $timeB;
            $submittedA = !empty($a['submitted_at']) ? strtotime((string)$a['submitted_at']) : PHP_INT_MAX;
            $submittedB = !empty($b['submitted_at']) ? strtotime((string)$b['submitted_at']) : PHP_INT_MAX;
            return $submittedA <=> $submittedB;
        });
        $leaderboards[$examIdForBoard] = ['title' => $boardExam['title'], 'takers' => $rankedTakers];
    } catch (Throwable $leaderboardError) {
        error_log('Exam leaderboard query failed for exam ' . $examIdForBoard . ': ' . $leaderboardError->getMessage());
        $leaderboards[$examIdForBoard] = ['title' => $boardExam['title'], 'takers' => []];
    }
}

// Leaderboard filter is limited to exams this student has taken (or is currently viewing).
$selectedLeaderboardExamId = (int)($_GET['leaderboard_exam'] ?? 0);
if ($selectedLeaderboardExamId > 0 && !isset($leaderboards[$selectedLeaderboardExamId])) {
    $selectedLeaderboardExamId = 0;
}
if ($selectedLeaderboardExamId === 0 && $leaderboards) {
    // Avoid array_key_first() so this also works on older supported PHP builds.
    reset($leaderboards);
    $firstLeaderboardKey = key($leaderboards);
    $selectedLeaderboardExamId = ($firstLeaderboardKey !== null) ? (int)$firstLeaderboardKey : 0;
}
$visibleLeaderboards = $selectedLeaderboardExamId > 0 && isset($leaderboards[$selectedLeaderboardExamId])
    ? [$selectedLeaderboardExamId => $leaderboards[$selectedLeaderboardExamId]]
    : [];
$activeExamInProgress = ($view === 'attempt' && $attempt && $attempt['status'] === 'in_progress');

ex_page_start('Student Examinations', true);
?>
<div class="student-exams-page<?= $activeExamInProgress ? ' exam-active' : '' ?>">
    <?php if (!$activeExamInProgress): ?>
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <div class="text-secondary small fw-semibold mb-1">ETS-ASYNC LEARNING PORTAL</div>
                <h2 class="fw-bold mb-1">Student Examinations</h2>
                <p class="text-secondary mb-0">Enter a test code, continue an attempt, or review your previous results.</p>
            </div>
            <a class="btn btn-outline-primary" href="#examHistory"><i class="bi bi-clock-history me-1"></i> Exam history <span class="badge text-bg-primary ms-1"><?= count($history) ?></span></a>
        </div>
    <?php endif; ?>
    <?php if ($error !== ''): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?= ex_e($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>
    <?php if ($notice !== ''): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?= ex_e($notice) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div><?php endif; ?>

    <?php if ($view === 'home'): ?>
        <section class="exam-card access-card">
            <div class="exam-card-head">
                <div class="d-flex align-items-center gap-3"><span class="student-icon"><i class="bi bi-shield-lock"></i></span>
                    <div>
                        <h5 class="fw-bold mb-1">Enter test code</h5>
                        <div class="text-secondary small">Use the code provided by your instructor.</div>
                    </div>
                </div>
            </div>
            <div class="exam-card-body">
                <form method="post" class="row g-3 align-items-end"><input type="hidden" name="csrf" value="<?= ex_e($examCsrf) ?>"><input type="hidden" name="action" value="enter_code">
                    <div class="col-12 col-md-8 col-lg-7 col-xl-6"><label for="testCode" class="form-label">Test code</label><input class="form-control form-control-lg text-uppercase test-code-input" id="testCode" name="test_code" maxlength="20" placeholder="e.g. ABCD2345" autocomplete="off" required></div>
                    <div class="col-12 col-md-4 col-lg-3 col-xl-2"><button class="btn btn-primary btn-lg w-100"><i class="bi bi-arrow-right-circle me-1"></i> Continue</button></div>
                </form>
            </div>
        </section>
    <?php elseif ($view === 'instructions' && $exam): ?>
        <section class="exam-card">
            <div class="exam-card-head"><span class="badge text-bg-success mb-2">EXAM AVAILABLE</span>
                <h3 class="fw-bold mb-1"><?= ex_e($exam['title']) ?></h3>
                <div class="text-secondary"><?= (int)$exam['question_count'] ?> questions <span class="mx-1">·</span> <?= (int)$exam['time_limit_minutes'] ?> minutes</div>
            </div>
            <div class="exam-card-body">
                <h5 class="fw-bold">Instructions</h5>
                <div class="text-rich mb-4"><?= ex_rich((string)$exam['instructions']) ?></div>
                <div class="alert alert-warning"><strong>Before starting:</strong> the timer begins immediately, and your answers will be submitted when time expires.</div>
                <div class="d-flex flex-wrap gap-2">
                    <form method="post"><input type="hidden" name="csrf" value="<?= ex_e($examCsrf) ?>"><input type="hidden" name="action" value="start_exam"><input type="hidden" name="exam_id" value="<?= (int)$exam['id'] ?>"><button class="btn btn-primary btn-lg" onclick="return confirm('Start the exam now? The timer will begin immediately.');"><i class="bi bi-play-circle me-1"></i> Start examination</button></form><a class="btn btn-outline-secondary btn-lg" href="assessment.php">Cancel</a>
                </div>
            </div>
        </section>
    <?php elseif ($view === 'attempt' && $attempt): ?>
        <?php if ($attempt['status'] === 'in_progress'): ?>
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
                <div>
                    <h3 class="fw-bold mb-1"><?= ex_e($attempt['title']) ?></h3>
                    <div class="text-secondary small">Set <?= ex_e($attempt['variant']) ?> · <?= ex_e($fullName) ?></div>
                </div><button type="button" class="btn btn-outline-primary" id="fullscreenButton"><i class="bi bi-arrows-fullscreen me-1"></i> Enter full screen</button>
            </div>
            <div class="exam-floating-tools" id="examFloatingTools" aria-label="Exam status">
                <div class="exam-tools-heading"><span><i class="bi bi-activity me-1" aria-hidden="true"></i> Exam status</span><button type="button" class="exam-tools-toggle" id="examToolsToggle" aria-expanded="true" aria-controls="examMonitorDetails" title="Minimize exam indicators"><i class="bi bi-chevron-down" aria-hidden="true"></i><span class="visually-hidden">Minimize exam indicators</span></button></div>
                <div class="timer-pill" title="Time remaining"><i class="bi bi-clock-history" aria-hidden="true"></i><span id="examTimer">--:--</span></div>
                <div class="exam-monitor-pill" id="examMonitorDetails">
                    <div class="monitor-line monitor-answered"><i class="bi bi-check2-square" aria-hidden="true"></i><span>Answered <strong id="answeredCount">0</strong>/<strong id="totalQuestionCount"><?= count($questions) ?></strong></span><strong class="answered-percent" id="answeredPercent">0%</strong></div>
                    <div class="answered-progress" role="progressbar" aria-label="Questions answered" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="answeredProgress"><span id="answeredProgressBar"></span></div>
                    <div class="monitor-line monitor-switches"><i class="bi bi-arrow-left-right" aria-hidden="true"></i><span>Tab switches</span><strong id="tabSwitchCount">0</strong></div>
                </div>
            </div>
            <div id="multipleTabsWarning" class="multiple-tabs-warning" role="alert" aria-live="assertive" hidden><strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Multiple exam tabs detected</strong>
                <div>Only one tab may be used for this exam. Please close all extra tabs and return to a single exam tab.</div>
            </div>
            <form method="post" id="studentExamForm"><input type="hidden" name="csrf" value="<?= ex_e($examCsrf) ?>"><input type="hidden" name="action" value="submit_exam"><input type="hidden" name="attempt_id" value="<?= (int)$attempt['id'] ?>">
                <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i> Choose one answer for each question. Unanswered questions will be marked incorrect.</div>
                <?php foreach ($questions as $index => $q): ?><section class="question-card">
                        <div class="small text-secondary fw-semibold mb-2">QUESTION <?= $index + 1 ?> OF <?= count($questions) ?></div>
                        <div class="text-rich fw-semibold mb-2 question-prompt-scroll"><?= ex_rich((string)$q['question_text']) ?></div><?php
                                                                                                                                        // Media is optional: a hosting schema mismatch must not stop the exam from rendering.
                                                                                                                                        try {
                                                                                                                                            ex_render_media($db, (int)$q['question_id']);
                                                                                                                                        } catch (Throwable $mediaError) {
                                                                                                                                            error_log('Student exam media rendering failed for question ' . (int)$q['question_id'] . ': ' . $mediaError->getMessage());
                                                                                                                                        }
                                                                                                                                        $choices = [];
                                                                                                                                        try {
                                                                                                                                            $choices = ex_rows($db, 'SELECT choice_label,choice_text FROM question_choices WHERE question_id=?', 'i', [(int)$q['question_id']]);
                                                                                                                                        } catch (Throwable $choiceError) {
                                                                                                                                            error_log('Student exam choices query failed for question ' . (int)$q['question_id'] . ': ' . $choiceError->getMessage());
                                                                                                                                        }
                                                                                                                                        $map = [];
                                                                                                                                        foreach ($choices as $c) $map[trim((string)$c['choice_label'])] = (string)$c['choice_text'];
                                                                                                                                        $order = json_decode((string)($q['choice_order_json'] ?? ''), true);
                                                                                                                                        if (!is_array($order)) $order = [];
                                                                                                                                        // Preserve the randomized order where valid; append choices absent from older order data.
                                                                                                                                        $validOrder = [];
                                                                                                                                        foreach ($order as $choiceKey) {
                                                                                                                                            $choiceKey = trim((string)$choiceKey);
                                                                                                                                            if (array_key_exists($choiceKey, $map) && !in_array($choiceKey, $validOrder, true)) $validOrder[] = $choiceKey;
                                                                                                                                        }
                                                                                                                                        foreach (array_keys($map) as $choiceKey) {
                                                                                                                                            if (!in_array($choiceKey, $validOrder, true)) $validOrder[] = $choiceKey;
                                                                                                                                        }
                                                                                                                                        $display = 'A';
                                                                                                                                        foreach ($validOrder as $original): ?><label class="choice-row"><input class="form-check-input mt-1" type="radio" name="answers[<?= (int)$q['question_id'] ?>]" value="<?= ex_e($original) ?>"><strong><?= ex_e($display) ?>.</strong><span class="text-rich"><?= ex_rich($map[$original]) ?></span></label><?php $display++;
                                                                                                                                                                                                                                                                                                                                                                                                                            endforeach;
                                                                                                                                                                                                                                                                                                                                                                                                                            if (!$validOrder): ?><div class="alert alert-danger mt-3">Answer choices could not be loaded for this question. Please contact your instructor.</div><?php endif; ?>
                    </section><?php endforeach; ?>
                <button type="submit" class="btn btn-success btn-lg w-100 mb-5" onclick="return confirm('Submit your exam now? You cannot change answers after submitting.');"><i class="bi bi-send me-1"></i> Submit examination</button>
            </form>
            <?php $remainingSeconds = max(0, strtotime((string)$attempt['expires_at']) - time()); ?>
            <script>
                (function() {
                    const attemptId = <?= (int)$attempt['id'] ?>;
                    const csrf = <?= json_encode($examCsrf) ?>;
                    let seconds = <?= (int)$remainingSeconds ?>;
                    const el = document.getElementById('examTimer');
                    const form = document.getElementById('studentExamForm');
                    const switchEl = document.getElementById('tabSwitchCount');
                    const answeredEl = document.getElementById('answeredCount');
                    const answeredPercentEl = document.getElementById('answeredPercent');
                    const answeredProgress = document.getElementById('answeredProgress');
                    const answeredProgressBar = document.getElementById('answeredProgressBar');
                    const totalQuestions = <?= count($questions) ?>;
                    const fullscreenButton = document.getElementById('fullscreenButton');
                    const floatingTools = document.getElementById('examFloatingTools');
                    const toolsToggle = document.getElementById('examToolsToggle');
                    const monitorDetails = document.getElementById('examMonitorDetails');
                    if (!el || !form) return;
                    let clock = null,
                        submitting = false,
                        tabSwitches = <?= (int)($attempt['tab_switch_count'] ?? 0) ?>,
                        maxOpenTabs = <?= max(1, (int)($attempt['max_open_tabs'] ?? 1)) ?>;
                    if (switchEl) switchEl.textContent = String(tabSwitches);
                    if (toolsToggle && floatingTools && monitorDetails) {
                        toolsToggle.addEventListener('click', () => {
                            const minimized = floatingTools.classList.toggle('is-minimized');
                            toolsToggle.setAttribute('aria-expanded', minimized ? 'false' : 'true');
                            toolsToggle.title = minimized ? 'Maximize exam indicators' : 'Minimize exam indicators';
                            toolsToggle.innerHTML = minimized ? '<i class="bi bi-chevron-up" aria-hidden="true"></i><span class="visually-hidden">Maximize exam indicators</span>' : '<i class="bi bi-chevron-down" aria-hidden="true"></i><span class="visually-hidden">Minimize exam indicators</span>';
                        });
                    }
                    const storageKey = 'assessment_answers_' + attemptId;
                    const peers = new Map();
                    const myTabId = (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : String(Date.now()) + Math.random().toString(16).slice(2);
                    const warningEl = document.getElementById('multipleTabsWarning');
                    let channel = null,
                        warningShown = false,
                        currentOpenTabs = 1;
                    try {
                        if ('BroadcastChannel' in window) channel = new BroadcastChannel('assessment_' + attemptId);
                    } catch (e) {}

                    // Restore answers after an accidental refresh, and persist each selection locally.
                    try {
                        const saved = JSON.parse(sessionStorage.getItem(storageKey) || '{}');
                        Object.entries(saved).forEach(([name, value]) => {
                            const input = form.querySelector('input[type="radio"][name="' + CSS.escape(name) + '"][value="' + CSS.escape(value) + '"]');
                            if (input) input.checked = true;
                        });
                    } catch (e) {}

                    function saveAnswers() {
                        const saved = {};
                        form.querySelectorAll('input[type="radio"]:checked').forEach(input => saved[input.name] = input.value);
                        try {
                            sessionStorage.setItem(storageKey, JSON.stringify(saved));
                        } catch (e) {}
                    }

                    function updateAnsweredProgress() {
                        const answered = new Set(Array.from(form.querySelectorAll('input[type="radio"]:checked')).map(input => input.name)).size;
                        const percent = totalQuestions > 0 ? Math.round((answered / totalQuestions) * 100) : 0;
                        if (answeredEl) answeredEl.textContent = String(answered);
                        if (answeredPercentEl) answeredPercentEl.textContent = percent + '%';
                        if (answeredProgressBar) answeredProgressBar.style.width = percent + '%';
                        if (answeredProgress) answeredProgress.setAttribute('aria-valuenow', String(percent));
                    }
                    form.addEventListener('change', () => {
                        saveAnswers();
                        updateAnsweredProgress();
                    });
                    updateAnsweredProgress();

                    function postStats() {
                        const body = new URLSearchParams({
                            csrf,
                            action: 'record_proctoring',
                            attempt_id: String(attemptId),
                            tab_switch_count: String(tabSwitches),
                            max_open_tabs: String(maxOpenTabs)
                        });
                        fetch(location.pathname, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                                },
                                body,
                                credentials: 'same-origin',
                                keepalive: true
                            })
                            .then(response => {
                                if (!response.ok) console.warn('Exam activity counters were not saved to the database. HTTP', response.status);
                            })
                            .catch(error => console.warn('Could not save exam activity counters:', error));
                    }

                    function updateTabs() {
                        const now = Date.now();
                        peers.set(myTabId, now);
                        for (const [id, seen] of peers)
                            if (now - seen > 7000) peers.delete(id);
                        currentOpenTabs = Math.max(1, peers.size);
                        maxOpenTabs = Math.max(maxOpenTabs, currentOpenTabs);
                        if (warningEl) warningEl.hidden = currentOpenTabs < 2;
                        if (currentOpenTabs >= 2 && !warningShown) {
                            warningShown = true;
                            alert('Multiple exam tabs detected (' + currentOpenTabs + '). Please close all extra exam tabs and continue in only one tab.');
                        }
                        if (currentOpenTabs < 2) warningShown = false;
                        postStats();
                    }

                    function announceTab() {
                        if (channel) channel.postMessage({
                            type: 'hello',
                            id: myTabId,
                            at: Date.now()
                        });
                        updateTabs();
                    }
                    if (channel) channel.onmessage = event => {
                        const m = event.data || {};
                        if (!m.id || m.id === myTabId) return;
                        if (m.type === 'bye') peers.delete(m.id);
                        else peers.set(m.id, Date.now());
                        if (m.type === 'hello') channel.postMessage({
                            type: 'hello',
                            id: myTabId,
                            at: Date.now()
                        });
                        updateTabs();
                    };
                    announceTab();
                    const tabHeartbeat = setInterval(announceTab, 2000);

                    function announceLeaving() {
                        if (channel) channel.postMessage({
                            type: 'bye',
                            id: myTabId
                        });
                        clearInterval(tabHeartbeat);
                        postStats();
                    }
                    window.addEventListener('pagehide', announceLeaving, {
                        once: true
                    });

                    function countSwitch() {
                        if (submitting) return;
                        tabSwitches++;
                        if (switchEl) switchEl.textContent = String(tabSwitches);
                        postStats();
                    }
                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'hidden') countSwitch();
                    });
                    window.addEventListener('blur', () => {
                        if (document.visibilityState === 'visible') countSwitch();
                    });

                    async function enterFullscreen() {
                        try {
                            if (!document.fullscreenElement) await document.documentElement.requestFullscreen();
                            if (fullscreenButton) fullscreenButton.innerHTML = '<i class="bi bi-fullscreen-exit me-1"></i> Exit full screen';
                        } catch (e) {
                            alert('Full screen was blocked by the browser. Click the full-screen button again to allow it.');
                        }
                    }
                    if (fullscreenButton) fullscreenButton.addEventListener('click', async () => {
                        try {
                            if (document.fullscreenElement) await document.exitFullscreen();
                            else await document.documentElement.requestFullscreen();
                        } catch (e) {
                            alert('Your browser did not allow full screen. Please use the full-screen button to try again.');
                        }
                    });
                    // Attempt automatic full screen on exam load. Browsers may block this without a fresh user gesture.
                    if (document.documentElement.requestFullscreen && !document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(() => {
                            if (fullscreenButton) fullscreenButton.classList.add('fullscreen-needed');
                        });
                    }
                    document.addEventListener('fullscreenchange', () => {
                        if (fullscreenButton) fullscreenButton.innerHTML = document.fullscreenElement ? '<i class="bi bi-fullscreen-exit me-1"></i> Exit full screen' : '<i class="bi bi-arrows-fullscreen me-1"></i> Enter full screen';
                    });

                    // Warn before navigating away/reloading; the browser controls the wording of this dialog.
                    window.addEventListener('beforeunload', event => {
                        if (!submitting) {
                            event.preventDefault();
                            event.returnValue = '';
                        }
                    });
                    form.addEventListener('submit', () => {
                        submitting = true;
                        saveAnswers();
                        if (clock) clearInterval(clock);
                    });
                    // Use an absolute deadline rather than decrementing a counter. Mobile browsers
                    // throttle intervals while scrolling/backgrounded; this keeps the display accurate.
                    const deadlineMs = (window.performance && typeof performance.now === 'function' ? performance.now() : Date.now()) + (Math.max(0, seconds) * 1000);

                    function tick() {
                        const clockNow = (window.performance && typeof performance.now === 'function' ? performance.now() : Date.now());
                        const safeSeconds = Math.max(0, Math.ceil((deadlineMs - clockNow) / 1000));
                        el.textContent = String(Math.floor(safeSeconds / 60)).padStart(2, '0') + ':' + String(safeSeconds % 60).padStart(2, '0');
                        if (safeSeconds <= 60) el.closest('.timer-pill')?.classList.add('timer-warning');
                        if (safeSeconds <= 0 && !submitting) {
                            submitting = true;
                            if (clock) clearInterval(clock);
                            // Auto-submit only when the server-provided remaining time has truly elapsed.
                            const action = form.querySelector('input[name="action"]');
                            if (action) action.value = 'submit_exam';
                            HTMLFormElement.prototype.submit.call(form);
                            return;
                        }
                    }
                    tick();
                    clock = setInterval(tick, 250);
                    window.addEventListener('pagehide', postStats);
                })();
            </script>
        <?php else: ?><section class="exam-card result-summary-card">
                <div class="exam-card-body text-center py-5 px-3 px-md-5"><i class="bi bi-check-circle-fill text-success" style="font-size:3rem"></i>
                    <h2 class="fw-bold mt-3">Exam submitted</h2>
                    <p class="text-secondary">Your result has been saved.</p>
                    <div class="display-5 fw-bold"><?= (int)$attempt['score'] ?> / <?= (int)$attempt['total_items'] ?></div>
                    <p class="text-secondary">Status: <?= ex_e(str_replace('_', ' ', $attempt['status'])) ?></p>
                    <div class="result-activity-badge"><i class="bi bi-arrow-left-right me-1"></i> Tab switches recorded: <strong><?= (int)($attempt['tab_switch_count'] ?? 0) ?></strong></div>
                    <div class="d-flex justify-content-center flex-wrap gap-2"><a href="assessment.php?review=<?= (int)$attempt['id'] ?>" class="btn btn-primary">View solutions</a><a href="assessment.php#examHistory" class="btn btn-outline-secondary">Exam history</a></div>
                </div>
            </section><?php endif; ?>
    <?php elseif ($view === 'review' && $attempt): ?>
        <div class="result-review-header d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div class="result-review-title">
                <div class="text-secondary small">RESULT REVIEW · SET <?= ex_e($attempt['variant']) ?></div>
                <h3 class="fw-bold mb-1"><?= ex_e($attempt['title']) ?></h3>
                <div class="text-secondary">Score: <strong><?= (int)$attempt['score'] ?> / <?= (int)$attempt['total_items'] ?></strong> · <?= ex_e(date('M j, Y g:i A', strtotime((string)($attempt['submitted_at'] ?? $attempt['started_at'])))) ?></div>
                <div class="result-activity-badge mt-2"><i class="bi bi-arrow-left-right me-1"></i> Tab switches recorded: <strong><?= (int)($attempt['tab_switch_count'] ?? 0) ?></strong></div>
            </div>
            <div class="d-flex flex-wrap gap-2"><a class="btn btn-outline-secondary" href="assessment.php#examHistory">Back to history</a></div>
        </div>
        <?php foreach ($questions as $index => $q): ?><section class="question-card">
                <div class="d-flex justify-content-between gap-2 flex-wrap mb-2">
                    <div class="small text-secondary fw-semibold">QUESTION <?= $index + 1 ?> OF <?= count($questions) ?></div><?php if ((int)($q['is_correct'] ?? 0) === 1): ?><span class="badge text-bg-success">Correct</span><?php else: ?><span class="badge text-bg-danger">Incorrect / unanswered</span><?php endif; ?>
                </div>
                <div class="text-rich fw-semibold mb-2 question-prompt-scroll"><?= ex_rich((string)$q['question_text']) ?></div>
                <?php
                // Keep review rendering resilient if optional media or solution data differs on hosting.
                try {
                    ex_render_media($db, (int)$q['question_id']);
                } catch (Throwable $mediaError) {
                    error_log('Review media rendering failed for question ' . (int)$q['question_id'] . ': ' . $mediaError->getMessage());
                }
                $reviewChoices = [];
                try {
                    $reviewChoices = ex_rows($db, 'SELECT choice_label,choice_text,is_correct FROM question_choices WHERE question_id=? ORDER BY FIELD(choice_label,\'A\',\'B\',\'C\',\'D\',\'E\',\'F\')', 'i', [(int)$q['question_id']]);
                } catch (Throwable $choiceError) {
                    error_log('Review choices query failed for question ' . (int)$q['question_id'] . ': ' . $choiceError->getMessage());
                }
                foreach ($reviewChoices as $c):
                    $picked = ((string)$c['choice_label'] === (string)($q['selected_choice'] ?? ''));
                    $correct = ((string)$c['choice_label'] === (string)($q['correct_choice'] ?? '') && (int)$c['is_correct'] === 1);
                ?><div class="choice-row <?= ($correct ? 'choice-correct' : ($picked ? 'choice-wrong' : '')) ?>">
                        <strong><?= ex_e($c['choice_label']) ?>.</strong>
                        <span class="text-rich"><?= ex_rich((string)$c['choice_text']) ?></span>
                        <?php if ($correct): ?><span class="ms-auto small fw-bold text-success">Correct answer</span><?php elseif ($picked): ?><span class="ms-auto small fw-bold text-danger">Your answer</span><?php endif; ?>
                    </div><?php endforeach; ?>
                <?php if (!$reviewChoices): ?><div class="alert alert-warning mt-3">Answer choices are unavailable for this question. Please contact your instructor.</div><?php endif; ?>
                <?php if (trim((string)($q['explanation'] ?? '')) !== ''): ?><div class="solution-block mt-3">
                        <h6 class="fw-bold"><i class="bi bi-lightbulb me-1"></i>Explanation</h6>
                        <div class="text-rich"><?= ex_rich((string)$q['explanation']) ?></div>
                    </div><?php endif; ?>
                <?php
                $solutions = [];
                try {
                    $solutions = ex_rows($db, 'SELECT solution_type,solution_content FROM question_solutions WHERE question_id=? ORDER BY display_order,id', 'i', [(int)$q['question_id']]);
                } catch (Throwable $solutionError) {
                    error_log('Review solutions query failed for question ' . (int)$q['question_id'] . ': ' . $solutionError->getMessage());
                }
                if ($solutions): ?><div class="solution-block mt-3">
                        <h6 class="fw-bold"><i class="bi bi-journal-check me-1"></i>Solutions</h6>
                        <?php foreach ($solutions as $sol): ?><div class="solution-item">
                                <div class="small text-secondary text-uppercase fw-semibold mb-2"><?= ex_e(str_replace('_', ' ', (string)$sol['solution_type'])) ?></div>
                                <?= st_solution_embed((string)$sol['solution_type'], (string)$sol['solution_content']) ?>
                            </div><?php endforeach; ?>
                    </div><?php endif; ?>
            </section><?php endforeach; ?>
        <a href="assessment.php#examHistory" class="btn btn-outline-primary mb-4"><i class="bi bi-clock-history me-1"></i> Return to exam history</a>
    <?php endif; ?>

    <?php if (!$activeExamInProgress): ?>
        <section id="examHistory" class="exam-card history-card">
            <div class="exam-card-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-clock-history me-2"></i>Exam history</h5>
                    <div class="text-secondary small">Your attempts, results, and available solution reviews.</div>
                </div><span class="badge text-bg-secondary"><?= count($history) ?> attempts</span>
            </div>
            <div class="exam-card-body p-0">
                <?php if (!$history): ?><div class="text-center py-5 px-3"><i class="bi bi-journal-x text-secondary" style="font-size:2rem"></i>
                        <h6 class="fw-bold mt-2">No exam attempts yet</h6>
                        <p class="text-secondary mb-0">When you take an exam using a test code, it will appear here.</p>
                    </div><?php else: ?><div class="table-responsive">
                        <table class="table align-middle mb-0 history-table">
                            <thead>
                                <tr>
                                    <th class="history-exam-col">Exam</th>
                                    <th>Started</th>
                                    <th>Set</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Tab switches</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody><?php foreach ($history as $h): ?><tr>
                                        <td>
                                            <div class="fw-semibold history-exam-name" title="<?= ex_e($h['title']) ?>"><?= ex_e($h['title']) ?></div>
                                            <div class="small text-secondary history-test-code"><span>Code</span> <code><?= ex_e($h['test_code']) ?></code></div>
                                        </td>
                                        <td class="text-nowrap"><?= ex_e(date('M j, Y g:i A', strtotime((string)$h['started_at']))) ?></td>
                                        <td><?= ex_e($h['variant']) ?></td>
                                        <td><span class="badge <?= ($h['status'] === 'in_progress' ? 'text-bg-warning' : ($h['status'] === 'submitted' ? 'text-bg-success' : 'text-bg-secondary')) ?>"><?= ex_e(str_replace('_', ' ', $h['status'])) ?></span></td>
                                        <td><?= ($h['score'] === null ? '—' : (int)$h['score'] . ' / ' . (int)$h['total_items']) ?></td>
                                        <td><span class="switch-count-badge"><?= (int)($h['tab_switch_count'] ?? 0) ?></span></td>
                                        <td class="text-end text-nowrap"><?php if ($h['status'] === 'in_progress'): ?><a class="btn btn-sm btn-primary" href="assessment.php?attempt=<?= (int)$h['id'] ?>">Continue</a><?php else: ?><a class="btn btn-sm btn-outline-primary" href="assessment.php?review=<?= (int)$h['id'] ?>">Results &amp; solutions</a><?php endif; ?></td>
                                    </tr><?php endforeach; ?></tbody>
                        </table>
                    </div><?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!$activeExamInProgress): ?>
        <?php if ($leaderboards): ?>
            <section class="exam-card leaderboard-filter-card mb-3" aria-label="Filter leaderboard by exam">
                <div class="exam-card-body">
                    <form method="get" class="leaderboard-filter-form">
                        <label for="leaderboardExamFilter" class="form-label fw-semibold"><i class="bi bi-funnel me-1" aria-hidden="true"></i>Choose an exam</label>
                        <select class="form-select" id="leaderboardExamFilter" name="leaderboard_exam" onchange="if (this.form.requestSubmit) { this.form.requestSubmit(); } else { this.form.submit(); }" aria-controls="examLeaderboard">
                            <?php foreach ($examIdsForBoards as $filterExam): ?>
                                <option value="<?= (int)$filterExam['id'] ?>" <?= $selectedLeaderboardExamId === (int)$filterExam['id'] ? 'selected' : '' ?>><?= $selectedLeaderboardExamId === (int)$filterExam['id'] ? '✓  ' : '' ?><?= ex_e($filterExam['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">The leaderboard updates automatically when you choose an exam.</div>
                    </form>
                </div>
            </section>
        <?php endif; ?>
        <?php foreach ($visibleLeaderboards as $boardExamId => $board): $boardTakers = $board['takers']; ?>
            <section id="examLeaderboard" class="exam-card leaderboard-card mb-4" aria-label="Leaderboard for <?= ex_e($board['title']) ?>">
                <div class="exam-card-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="bi bi-trophy-fill me-2" aria-hidden="true"></i><?= ex_e($board['title']) ?> leaderboard</h5>
                        <div class="text-secondary small">All completed takers for this exam, ranked by percentage.</div>
                    </div>
                    <div class="d-flex align-items-center flex-wrap gap-2"><span class="badge leaderboard-count-badge"><i class="bi bi-people-fill me-1" aria-hidden="true"></i><?= count($boardTakers) ?> takers</span></div>
                </div>
                <div class="exam-card-body p-0">
                    <?php if (!$boardTakers): ?>
                        <div class="text-center py-4 px-3 text-secondary"><i class="bi bi-hourglass-split me-1" aria-hidden="true"></i>No completed results for this exam yet.</div>
                    <?php else: ?>
                        <div class="leaderboard-podium" aria-label="Top three students">
                            <?php foreach (array_slice($boardTakers, 0, 3) as $podiumRank => $podiumTaker): ?>
                                <div class="podium-place podium-<?= $podiumRank + 1 ?>">
                                    <div class="podium-avatar-wrap podium-avatar-rank-<?= $podiumRank + 1 ?>">
                                        <?php $podiumName = trim((string)($podiumTaker['student_name'] ?? 'Student'));
                                        $podiumInitials = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $podiumName), 0, 2));
                                        if ($podiumInitials === '') $podiumInitials = 'S';
                                        $podiumPhoto = (string)($podiumTaker['_profile_image'] ?? ''); ?>
                                        <?php if ($podiumPhoto !== ''): ?><img class="podium-avatar" src="<?= ex_e($podiumPhoto) ?>" alt="<?= ex_e($podiumName) ?> profile picture" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><?php endif; ?>
                                        <span class="podium-avatar-fallback" <?= $podiumPhoto !== '' ? 'hidden' : '' ?>><?= ex_e($podiumInitials) ?></span>
                                        <span class="podium-icon" aria-hidden="true"><i class="bi <?= $podiumRank === 0 ? 'bi-trophy-fill' : ($podiumRank === 1 ? 'bi-award-fill' : 'bi-medal-fill') ?>"></i></span>
                                    </div>
                                    <div class="podium-rank"><?= $podiumRank === 0 ? '1st Place' : ($podiumRank === 1 ? '2nd Place' : '3rd Place') ?></div>
                                    <div class="podium-award">
                                        <?php if ($podiumRank === 0): ?><span class="leader-achievement gold">Champion</span>
                                        <?php elseif ($podiumRank === 1): ?><span class="leader-achievement silver">Runner-up</span>
                                        <?php else: ?><span class="leader-achievement bronze">3rd Place</span><?php endif; ?>
                                    </div>
                                    <div class="podium-name"><?= ex_e(trim((string)$podiumTaker['student_name']) !== '' ? (string)$podiumTaker['student_name'] : 'Student') ?></div>
                                    <div class="podium-score"><?= number_format(((float)$podiumTaker['_percentage']) * 100, 1) ?>%</div>
                                    <div class="podium-points"><?= (int)$podiumTaker['score'] ?> / <?= (int)$podiumTaker['total_items'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 leaderboard-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student</th>
                                        <th>Score</th>
                                        <th>Time Taken</th>
                                        <th>Percentage</th>
                                        <th>Achievement</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($boardTakers as $rank => $leader): ?>
                                        <tr class="<?= $rank < 3 ? 'leaderboard-top-row' : '' ?>">
                                            <td><span class="leader-rank rank-<?= min(3, $rank + 1) ?>"><?php if ($rank === 0): ?><i class="bi bi-trophy-fill" aria-label="First place"></i><span class="visually-hidden">1st place</span><?php elseif ($rank === 1): ?><i class="bi bi-award-fill" aria-label="Second place medal" role="img"></i><span class="visually-hidden">2nd place</span><?php elseif ($rank === 2): ?><i class="bi bi-pen-fill" aria-label="Third place"></i><span class="visually-hidden">3rd place</span><?php else: ?><?= $rank + 1 ?><?php endif; ?></span></td>
                                            <td>
                                                <div class="leader-student-cell"><?php $leaderName = trim((string)($leader['student_name'] ?? 'Student'));
                                                                                    $leaderInitials = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $leaderName), 0, 2));
                                                                                    if ($leaderInitials === '') $leaderInitials = 'S';
                                                                                    $leaderPhoto = (string)($leader['_profile_image'] ?? ''); ?><?php if ($leaderPhoto !== ''): ?><img class="leader-avatar" src="<?= ex_e($leaderPhoto) ?>" alt="" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false"><?php endif; ?><span class="leader-avatar-fallback" <?= $leaderPhoto !== '' ? 'hidden' : '' ?>><?= ex_e($leaderInitials) ?></span><span class="fw-semibold"><?= ex_e($leaderName !== '' ? $leaderName : 'Student') ?></span></div>
                                            </td>
                                            <td><?= (int)$leader['score'] ?> / <?= (int)$leader['total_items'] ?></td>
                                            <td><?php if ($leader['_time_taken_seconds'] !== null): ?><?php $elapsed = (int)$leader['_time_taken_seconds'];
                                                                                                        $hours = intdiv($elapsed, 3600);
                                                                                                        $minutes = intdiv($elapsed % 3600, 60);
                                                                                                        $seconds = $elapsed % 60; ?><span class="leader-time-taken"><i class="bi bi-stopwatch me-1" aria-hidden="true"></i><?php if ($hours > 0): ?><?= $hours ?>h <?php endif; ?><?= $minutes ?>m <?= $seconds ?>s</span><?php else: ?><span class="text-secondary small">Not recorded</span><?php endif; ?></td>
                                            <td><strong><?= number_format(((float)$leader['_percentage']) * 100, 1) ?>%</strong></td>
                                            <td><?php if ($rank === 0): ?><span class="leader-achievement gold">Champion</span><?php elseif ($rank === 1): ?><span class="leader-achievement silver">Runner-up</span><?php elseif ($rank === 2): ?><span class="leader-achievement bronze">3rd Place</span><?php else: ?><span class="leader-achievement completed">Completed</span><?php endif; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
<style>
    /* ETS-Async theme tokens: follows the site's html[data-theme] switch. */
    .student-exams-page {
        --exam-surface: #ffffff;
        --exam-surface-soft: #f9fafb;
        --exam-text: #172033;
        --exam-muted: #667085;
        --exam-border: #e4e7ec;
        --exam-input-border: #d0d5dd;
        --exam-hover: #f8fafc;
        --exam-code-bg: #eef2f7;
        --exam-code-text: #be123c;
        --exam-solution-bg: #f8fbff;
        --exam-solution-border: #dbeafe;
        --exam-correct-bg: #f0fdf4;
        --exam-correct-border: #86efac;
        --exam-wrong-bg: #fef2f2;
        --exam-wrong-border: #fca5a5;
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 clamp(0px, 1.25vw, 16px);
        box-sizing: border-box;
        color: var(--exam-text);
    }

    html[data-theme="dark"] .student-exams-page,
    html[data-bs-theme="dark"] .student-exams-page,
    body.dark-mode .student-exams-page,
    body.dark-theme .student-exams-page {
        --exam-surface: #151922;
        --exam-surface-soft: #1b2230;
        --exam-text: #f0f4fa;
        --exam-muted: #98a2b3;
        --exam-border: #2a3140;
        --exam-input-border: #3a4252;
        --exam-hover: #202938;
        --exam-code-bg: #252d3b;
        --exam-code-text: #fda4af;
        --exam-solution-bg: #192332;
        --exam-solution-border: #34445a;
        --exam-correct-bg: #153126;
        --exam-correct-border: #246746;
        --exam-wrong-bg: #3a2028;
        --exam-wrong-border: #7f3542;
    }

    .student-exams-page .text-secondary {
        color: var(--exam-muted) !important
    }

    .student-exams-page .exam-card,
    .student-exams-page .question-card {
        color: var(--exam-text);
        background: var(--exam-surface);
        border: 1px solid var(--exam-border);
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
    }

    html[data-theme="dark"] .student-exams-page .exam-card,
    html[data-theme="dark"] .student-exams-page .question-card,
    html[data-bs-theme="dark"] .student-exams-page .exam-card,
    html[data-bs-theme="dark"] .student-exams-page .question-card,
    body.dark-mode .student-exams-page .exam-card,
    body.dark-mode .student-exams-page .question-card,
    body.dark-theme .student-exams-page .exam-card,
    body.dark-theme .student-exams-page .question-card {
        box-shadow: 0 8px 24px rgba(0, 0, 0, .14);
    }

    .student-exams-page .exam-card-head {
        background: var(--exam-surface-soft);
        border-bottom: 1px solid var(--exam-border);
    }

    .student-exams-page .exam-card-body {
        background: var(--exam-surface)
    }

    .student-exams-page .student-icon {
        width: 48px;
        height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(37, 99, 235, .12);
        color: #3b82f6;
        font-size: 1.25rem;
        flex-shrink: 0
    }

    .student-exams-page .access-card {
        max-width: 900px
    }

    .student-exams-page .history-card {
        scroll-margin-top: 90px
    }

    .student-exams-page .form-label {
        color: var(--exam-text);
        font-weight: 650
    }

    .student-exams-page .form-control,
    .student-exams-page .form-select {
        color: var(--exam-text);
        background: var(--exam-surface);
        border-color: var(--exam-input-border)
    }

    .student-exams-page .form-control::placeholder {
        color: var(--exam-muted);
        opacity: .85
    }

    .student-exams-page .form-control:focus,
    .student-exams-page .form-select:focus {
        color: var(--exam-text);
        background: var(--exam-surface);
        border-color: #3b82f6;
        box-shadow: 0 0 0 .2rem rgba(59, 130, 246, .18)
    }

    .student-exams-page .choice-row {
        color: var(--exam-text);
        background: var(--exam-surface);
        border: 1px solid var(--exam-border);
        border-radius: 9px
    }

    .student-exams-page .choice-row:hover {
        background: var(--exam-hover);
        border-color: #60a5fa
    }

    .student-exams-page .choice-correct {
        border-color: var(--exam-correct-border) !important;
        background: var(--exam-correct-bg) !important
    }

    .student-exams-page .choice-wrong {
        border-color: var(--exam-wrong-border) !important;
        background: var(--exam-wrong-bg) !important
    }

    .student-exams-page .solution-block {
        padding: 16px;
        border: 1px solid var(--exam-solution-border);
        background: var(--exam-solution-bg);
        border-radius: 10px;
        color: var(--exam-text)
    }

    .student-exams-page .solution-item {
        padding: 12px 0;
        border-top: 1px solid var(--exam-border)
    }

    .student-exams-page .solution-item:first-of-type {
        border-top: 0
    }

    .student-exams-page .solution-embed {
        max-width: 780px;
        border: 1px solid var(--exam-border);
        border-radius: 10px;
        overflow: hidden
    }

    .student-exams-page .solution-plain {
        white-space: normal;
        line-height: 1.7
    }

    .student-exams-page .table {
        --bs-table-color: var(--exam-text);
        --bs-table-bg: var(--exam-surface);
        --bs-table-border-color: var(--exam-border);
        --bs-table-striped-bg: var(--exam-surface-soft);
        --bs-table-hover-bg: var(--exam-hover);
        color: var(--exam-text);
        background: var(--exam-surface);
        margin-bottom: 0
    }

    .student-exams-page .table thead th {
        font-size: .78rem;
        text-transform: uppercase;
        letter-spacing: .03em;
        color: var(--exam-muted);
        background: var(--exam-surface-soft);
        border-bottom-color: var(--exam-border)
    }

    .student-exams-page .table td,
    .student-exams-page .table th {
        padding: 14px 16px
    }

    .student-exams-page .alert {
        border-radius: 10px
    }

    .student-exams-page .text-rich img {
        max-width: 100%;
        height: auto
    }

    .student-exams-page .text-rich table {
        max-width: 100%;
        border-collapse: collapse
    }

    .student-exams-page .text-rich td,
    .student-exams-page .text-rich th {
        border: 1px solid var(--exam-border);
        padding: 5px
    }

    .student-exams-page .text-rich pre,
    .student-exams-page .text-rich pre.ql-syntax {
        display: block;
        max-width: 100%;
        overflow: auto;
        white-space: pre;
        tab-size: 4;
        background: #111827;
        color: #e5e7eb;
        border: 1px solid #374151;
        border-radius: 10px;
        padding: 16px;
        margin: 14px 0;
        font: 13px/1.65 ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        font-weight: 400
    }

    .student-exams-page .text-rich pre code,
    .student-exams-page .text-rich code {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        font-weight: 400
    }

    .student-exams-page .text-rich :not(pre)>code {
        background: var(--exam-code-bg);
        color: var(--exam-code-text);
        padding: .15em .35em;
        border-radius: 4px
    }

    .student-exams-page .text-rich pre code {
        background: transparent !important;
        color: inherit !important;
        padding: 0 !important;
        white-space: pre;
        font-size: inherit
    }

    .student-exams-page .text-rich pre * {
        font-weight: inherit
    }

    @media(max-width:700px) {

        .student-exams-page .table td,
        .student-exams-page .table th {
            padding: 10px
        }

        .student-exams-page .btn {
            max-width: 100%
        }

        .student-exams-page .exam-card-body {
            padding: 16px
        }

        .student-exams-page .question-card {
            padding: 14px
        }
    }

    .student-exams-page .fullscreen-needed {
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .18);
        border-color: #2563eb;
    }

    /* Distinct, low-saturation indicator colors for quick scanning. */
    .student-exams-page .exam-floating-tools .timer-pill {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .student-exams-page .exam-floating-tools .monitor-answered {
        background: #ecfdf5;
        border-radius: 8px;
        padding: 7px;
        color: #065f46;
    }

    .student-exams-page .exam-floating-tools .monitor-answered i {
        color: #059669;
    }

    .student-exams-page .exam-floating-tools .monitor-switches {
        background: #fff7ed;
        border-radius: 8px;
        padding: 7px;
        color: #9a3412;
    }

    .student-exams-page .exam-floating-tools .monitor-switches i {
        color: #ea580c;
    }

    .student-exams-page .exam-floating-tools .answered-progress span {
        background: #0d9488;
    }

    html[data-theme="dark"] .student-exams-page .exam-floating-tools .timer-pill,
    html[data-bs-theme="dark"] .student-exams-page .exam-floating-tools .timer-pill,
    body.dark-mode .student-exams-page .exam-floating-tools .timer-pill,
    body.dark-theme .student-exams-page .exam-floating-tools .timer-pill {
        background: #172554;
        border-color: #1d4ed8;
        color: #bfdbfe;
    }

    html[data-theme="dark"] .student-exams-page .exam-floating-tools .monitor-answered,
    html[data-bs-theme="dark"] .student-exams-page .exam-floating-tools .monitor-answered,
    body.dark-mode .student-exams-page .exam-floating-tools .monitor-answered,
    body.dark-theme .student-exams-page .exam-floating-tools .monitor-answered {
        background: #052e2b;
        color: #a7f3d0;
    }

    html[data-theme="dark"] .student-exams-page .exam-floating-tools .monitor-switches,
    html[data-bs-theme="dark"] .student-exams-page .exam-floating-tools .monitor-switches,
    body.dark-mode .student-exams-page .exam-floating-tools .monitor-switches,
    body.dark-theme .student-exams-page .exam-floating-tools .monitor-switches {
        background: #431407;
        color: #fed7aa;
    }

    /* During an active exam, remove navigation chrome and use the full viewport. */
    body:has(.student-exams-page.exam-active) .sidebar,
    body:has(.student-exams-page.exam-active) #sidebar,
    body:has(.student-exams-page.exam-active) .sidebar-wrapper,
    body:has(.student-exams-page.exam-active) .main-sidebar,
    body:has(.student-exams-page.exam-active) .topbar,
    body:has(.student-exams-page.exam-active) #topbar,
    body:has(.student-exams-page.exam-active) .top-bar,
    body:has(.student-exams-page.exam-active) .main-topbar,
    body:has(.student-exams-page.exam-active) header.topbar {
        display: none !important;
    }

    body:has(.student-exams-page.exam-active) .main-content,
    body:has(.student-exams-page.exam-active) main.main-content {
        margin-left: 0 !important;
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
    }

    body:has(.student-exams-page.exam-active) .content-wrapper {
        margin: 0 auto !important;
        padding: 14px !important;
        max-width: none !important;
        width: 100% !important;
    }

    body:has(.student-exams-page.exam-active) .student-exams-page {
        max-width: none;
        width: 100%;
    }

    body:has(.student-exams-page.exam-active) .student-exams-page>.d-flex:first-child {
        margin-top: 0;
    }

    /* Floating exam controls: anchored to the bottom-right on phones, tablets and desktops. */
    .student-exams-page {
        --exam-tool-bg: var(--exam-surface, #fff);
        --exam-tool-text: var(--exam-text, #172033);
        --exam-tool-border: var(--exam-border, #e4e7ec);
        --exam-progress-track: var(--exam-surface-soft, #f1f5f9);
        --exam-progress-fill: #2563eb;
    }

    html[data-theme="dark"] .student-exams-page,
    html[data-bs-theme="dark"] .student-exams-page,
    body.dark-mode .student-exams-page,
    body.dark-theme .student-exams-page {
        --exam-tool-bg: var(--exam-surface, #151922);
        --exam-tool-text: var(--exam-text, #f0f4fa);
        --exam-tool-border: var(--exam-border, #2a3140);
        --exam-progress-track: var(--exam-surface-soft, #1b2230);
    }

    .student-exams-page #studentExamForm {
        padding-bottom: 220px;
    }

    .student-exams-page .exam-floating-tools {
        position: fixed;
        z-index: 1050;
        right: max(12px, env(safe-area-inset-right));
        bottom: max(14px, env(safe-area-inset-bottom));
        top: auto;
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-items: stretch;
        width: min(260px, calc(100vw - 24px));
        pointer-events: none;
    }

    .student-exams-page .exam-floating-tools>* {
        pointer-events: auto;
    }

    .student-exams-page .exam-tools-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 10px;
        border: 1px solid var(--exam-tool-border);
        border-radius: 12px;
        background: var(--exam-tool-bg);
        color: var(--exam-tool-text);
        font-size: .78rem;
        font-weight: 750;
        box-shadow: 0 8px 28px rgba(0, 0, 0, .12);
        pointer-events: auto;
    }

    .student-exams-page .exam-tools-toggle {
        width: 32px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--exam-tool-border);
        border-radius: 8px;
        background: var(--exam-surface-soft);
        color: var(--exam-tool-text);
        cursor: pointer;
        flex: 0 0 auto;
    }

    .student-exams-page .exam-tools-toggle:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    .student-exams-page .exam-floating-tools.is-minimized .exam-monitor-pill {
        display: none;
    }

    .student-exams-page .exam-floating-tools.is-minimized {
        width: min(190px, calc(100vw - 24px));
    }

    .student-exams-page .exam-floating-tools.is-minimized .exam-tools-heading {
        padding: 5px 7px;
    }

    .student-exams-page .exam-floating-tools .timer-pill {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-width: 0;
        width: 100%;
        padding: 10px 14px;
        border-radius: 14px;
        background: var(--exam-tool-bg);
        border: 1px solid var(--exam-tool-border);
        color: var(--exam-tool-text);
        font-size: 1.1rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }

    .student-exams-page .exam-floating-tools .timer-pill i {
        font-size: 1.05rem;
    }

    .student-exams-page .exam-floating-tools .timer-pill.timer-warning {
        background: #b91c1c !important;
        color: #fff !important;
        border-color: #b91c1c !important;
    }

    .student-exams-page .exam-monitor-pill {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 11px 12px;
        border-radius: 13px;
        background: var(--exam-tool-bg);
        border: 1px solid var(--exam-tool-border);
        color: var(--exam-tool-text);
        font-size: .8rem;
        min-width: 0;
    }

    .student-exams-page .exam-monitor-pill .monitor-line {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .student-exams-page .exam-monitor-pill .monitor-line i {
        width: 16px;
        flex: 0 0 16px;
        text-align: center;
        color: #3b82f6;
    }

    .student-exams-page .exam-monitor-pill .monitor-line span {
        flex: 1;
        min-width: 0;
    }

    .student-exams-page .exam-monitor-pill strong {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .student-exams-page .exam-monitor-pill .answered-percent {
        color: var(--exam-tool-text);
    }

    .student-exams-page .answered-progress {
        height: 6px;
        overflow: hidden;
        border-radius: 999px;
        background: var(--exam-progress-track);
        border: 1px solid var(--exam-tool-border);
    }

    .student-exams-page .answered-progress span {
        display: block;
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: var(--exam-progress-fill);
        transition: width .2s ease;
    }

    .student-exams-page .tab-count-scope {
        color: var(--exam-muted, #667085);
        font-size: .68rem;
        line-height: 1.3;
    }

    .student-exams-page .multiple-tabs-warning {
        position: sticky;
        top: 76px;
        z-index: 1045;
        margin: 0 0 18px;
        padding: 14px 16px;
        border: 2px solid #dc2626;
        border-radius: 12px;
        background: #fef2f2;
        color: #991b1b;
        box-shadow: 0 8px 24px rgba(127, 29, 29, .16);
    }

    .student-exams-page .multiple-tabs-warning strong {
        display: block;
        margin-bottom: 4px;
    }

    html[data-theme="dark"] .student-exams-page .multiple-tabs-warning,
    html[data-bs-theme="dark"] .student-exams-page .multiple-tabs-warning,
    body.dark-mode .student-exams-page .multiple-tabs-warning,
    body.dark-theme .student-exams-page .multiple-tabs-warning {
        background: #3a2028;
        color: #fecaca;
        border-color: #ef4444;
    }

    /* Ensure Bootstrap controls and cards remain readable under either site theme. */
    .student-exams-page .exam-floating-tools .timer-pill,
    .student-exams-page .exam-floating-tools .exam-monitor-pill {
        color: var(--exam-text);
        background: var(--exam-surface);
        border-color: var(--exam-border);
    }

    .student-exams-page .exam-floating-tools .timer-pill.timer-warning {
        color: #fff !important;
        background: #b91c1c !important;
        border-color: #b91c1c !important;
    }

    @media (max-width: 700px) {
        .student-exams-page #studentExamForm {
            padding-bottom: 205px;
        }

        .student-exams-page .exam-floating-tools {
            right: max(8px, env(safe-area-inset-right));
            bottom: max(8px, env(safe-area-inset-bottom));
            width: min(230px, calc(100vw - 16px));
            gap: 6px;
        }

        .student-exams-page .exam-floating-tools .timer-pill {
            padding: 8px 11px;
            font-size: 1rem;
        }

        .student-exams-page .exam-monitor-pill {
            padding: 9px 10px;
            gap: 6px;
            font-size: .73rem;
        }

        .student-exams-page .tab-count-scope {
            font-size: .62rem;
        }
    }

    @media (min-width: 701px) and (max-width: 1100px) {
        .student-exams-page .exam-floating-tools {
            width: 245px;
            right: 14px;
            bottom: 14px;
        }
    }

    @media (min-width: 1101px) {
        .student-exams-page .exam-floating-tools {
            width: 260px;
            right: 20px;
            bottom: 20px;
        }
    }

    .student-exams-page .result-activity-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 7px 11px;
        border: 1px solid #fed7aa;
        border-radius: 9px;
        background: #fff7ed;
        color: #9a3412;
        font-size: .9rem;
    }

    .student-exams-page .switch-count-badge {
        display: inline-block;
        min-width: 30px;
        text-align: center;
        padding: 4px 8px;
        border-radius: 7px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-weight: 700;
    }

    .student-exams-page .leader-rank {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--exam-surface-soft);
        border: 1px solid var(--exam-border);
        font-weight: 800;
    }

    .student-exams-page .leader-rank.rank-1 {
        background: #dbeafe;
        border-color: #60a5fa;
        color: #1d4ed8;
    }

    .student-exams-page .leader-rank.rank-2 {
        background: #e5e7eb;
        border-color: #9ca3af;
        color: #374151;
    }

    .student-exams-page .leader-rank.rank-3 {
        background: #cffafe;
        border-color: #67e8f9;
        color: #155e75;
    }

    html[data-theme="dark"] .student-exams-page .result-activity-badge,
    html[data-bs-theme="dark"] .student-exams-page .result-activity-badge,
    body.dark-mode .student-exams-page .result-activity-badge,
    body.dark-theme .student-exams-page .result-activity-badge,
    html[data-theme="dark"] .student-exams-page .switch-count-badge,
    html[data-bs-theme="dark"] .student-exams-page .switch-count-badge,
    body.dark-mode .student-exams-page .switch-count-badge,
    body.dark-theme .student-exams-page .switch-count-badge {
        background: #431407;
        border-color: #9a3412;
        color: #fed7aa;
    }

    /* Result/review spacing: keep content aligned and avoid edge collisions on narrow screens. */
    .student-exams-page .result-summary-card {
        width: 100%;
        max-width: 980px;
        margin: 0 auto 1.5rem;
        overflow: hidden;
    }

    .student-exams-page .result-summary-card .exam-card-body {
        box-sizing: border-box;
        padding-top: clamp(1.5rem, 4vw, 2.75rem) !important;
        padding-bottom: clamp(1.5rem, 4vw, 2.75rem) !important;
    }

    .student-exams-page .result-summary-card .result-activity-badge {
        margin: 0 auto 1.25rem;
        max-width: 100%;
        flex-wrap: wrap;
        justify-content: center;
    }

    .student-exams-page .result-summary-card .btn {
        white-space: normal;
    }

    .student-exams-page .result-review-header {
        min-width: 0;
        margin-top: .25rem;
    }

    .student-exams-page .result-review-title {
        min-width: 0;
        flex: 1 1 320px;
        overflow-wrap: anywhere;
    }

    .student-exams-page .question-card {
        margin: 0 0 1rem;
        padding: clamp(1rem, 2.5vw, 1.5rem);
        min-width: 0;
    }

    .student-exams-page .question-card .text-rich {
        overflow-wrap: anywhere;
        min-width: 0;
        max-width: 100%;
    }

    /* Wide question content scrolls horizontally instead of being clipped on phones. */
    .student-exams-page .question-card .question-prompt-scroll {
        max-width: 100%;
        min-width: 0;
        overflow-x: auto;
        overflow-y: visible;
        overscroll-behavior-x: contain;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        padding: .15rem .4rem .55rem 0;
        line-height: 1.65;
    }

    .student-exams-page .question-card .question-prompt-scroll>*:last-child {
        margin-bottom: 0;
    }

    .student-exams-page .question-card .question-prompt-scroll img,
    .student-exams-page .question-card .question-prompt-scroll video,
    .student-exams-page .question-card .question-prompt-scroll iframe {
        max-width: 100%;
    }

    /* Preserve wide equations/code/tables so they can be swiped sideways. */
    .student-exams-page .question-card .question-prompt-scroll pre,
    .student-exams-page .question-card .question-prompt-scroll pre code,
    .student-exams-page .question-card .question-prompt-scroll .ql-syntax {
        max-width: none;
        white-space: pre;
        overflow-x: visible;
    }

    .student-exams-page .question-card .question-prompt-scroll table {
        width: max-content;
        max-width: none;
    }

    @media (max-width: 767.98px) {
        .student-exams-page .question-card .question-prompt-scroll {
            padding-right: .55rem;
            scrollbar-width: thin;
        }
    }

    .student-exams-page .leaderboard-podium {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        padding: 18px;
        border-bottom: 1px solid var(--exam-border);
        background: var(--exam-surface);
    }

    .student-exams-page .podium-place {
        min-width: 0;
        text-align: center;
        padding: 16px 10px;
        border: 1px solid var(--exam-border);
        border-radius: 14px;
        background: var(--exam-surface-soft);
        overflow-wrap: anywhere;
    }

    .student-exams-page .podium-place.podium-1 {
        background: #eff6ff;
        border-color: #60a5fa;
        color: #1e40af;
    }

    .student-exams-page .podium-place.podium-2 {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #334155;
    }

    .student-exams-page .podium-place.podium-3 {
        background: #ecfeff;
        border-color: #67e8f9;
        color: #155e75;
    }

    .student-exams-page .podium-icon {
        font-size: 1.7rem;
        line-height: 1.2;
        margin-bottom: 7px;
    }

    .student-exams-page .podium-rank {
        font-size: .76rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .student-exams-page .podium-award {
        margin-top: 8px;
        min-height: 25px;
    }

    .student-exams-page .podium-award .leader-achievement {
        justify-content: center;
        max-width: 100%;
    }

    .student-exams-page .leader-achievement.bronze {
        background: #ffedd5;
        color: #9a3412;
        border: 1px solid #fdba74;
        box-shadow: 0 2px 5px rgba(154, 52, 18, .12);
    }

    .student-exams-page .podium-name {
        font-weight: 750;
        margin-top: 6px;
    }

    .student-exams-page .podium-score {
        font-size: 1.35rem;
        font-weight: 850;
        margin-top: 5px;
    }

    .student-exams-page .podium-points {
        font-size: .82rem;
        opacity: .8;
    }

    .student-exams-page .leader-achievement {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        border-radius: 999px;
        padding: 4px 9px;
        font-size: .78rem;
        font-weight: 750;
    }

    .student-exams-page .leader-achievement.gold {
        background: #fef3c7;
        color: #92400e;
    }

    .student-exams-page .leader-achievement.silver {
        background: #e2e8f0;
        color: #334155;
    }

    .student-exams-page .leader-achievement.bronze {
        background: #ffedd5;
        color: #9a3412;
    }

    .student-exams-page .leaderboard-top-row td {
        background: color-mix(in srgb, var(--exam-surface-soft) 78%, #fbbf24 22%);
    }

    @media (max-width: 575.98px) {
        .student-exams-page .result-summary-card {
            border-radius: 12px;
        }

        .student-exams-page .result-review-header {
            align-items: stretch !important;
        }

        .student-exams-page .result-review-header>a {
            align-self: flex-start;
        }

        .student-exams-page .leaderboard-podium {
            gap: 7px;
            padding: 10px;
        }

        .student-exams-page .podium-place {
            padding: 12px 5px;
            border-radius: 10px;
        }

        .student-exams-page .podium-icon {
            font-size: 1.35rem;
        }

        .student-exams-page .podium-rank {
            font-size: .64rem;
        }

        .student-exams-page .podium-name {
            font-size: .78rem;
        }

        .student-exams-page .podium-score {
            font-size: 1rem;
        }

        .student-exams-page .podium-points {
            font-size: .7rem;
        }

        .student-exams-page .leaderboard-table {
            font-size: .84rem;
        }
    }

    /* Full-width access and leaderboard filter containers. */
    .student-exams-page {
        max-width: none !important;
        width: 100% !important;
    }

    .student-exams-page .access-card,
    .student-exams-page .leaderboard-filter-card {
        display: block;
        width: 100% !important;
        max-width: none !important;
        margin-inline: 0 !important;
    }

    .student-exams-page .access-card .exam-card-body,
    .student-exams-page .leaderboard-filter-card .exam-card-body,
    .student-exams-page .leaderboard-filter-form {
        width: 100%;
        max-width: none;
    }

    .student-exams-page .access-card form.row {
        width: 100%;
    }

    /* Consistent page and result layout across desktop and mobile. */
    .student-exams-page *,
    .student-exams-page *::before,
    .student-exams-page *::after {
        box-sizing: border-box;
    }

    .student-exams-page>* {
        min-width: 0;
    }

    .student-exams-page .exam-card {
        width: 100%;
        min-width: 0;
        margin-inline: auto;
        overflow: hidden;
    }

    .student-exams-page .exam-card-head {
        padding: 1rem clamp(1rem, 2.5vw, 1.5rem);
    }

    .student-exams-page .exam-card-body {
        padding: clamp(1rem, 2.5vw, 1.5rem);
        min-width: 0;
    }

    .student-exams-page .result-summary-card {
        margin: 0 auto 1.5rem;
    }

    .student-exams-page .result-review-header {
        width: 100%;
        gap: 1rem;
    }

    .student-exams-page .result-review-title {
        overflow-wrap: anywhere;
    }

    .student-exams-page .question-card {
        width: 100%;
    }

    .student-exams-page .leaderboard-filter-form {
        width: 100%;
        max-width: 720px;
    }

    .student-exams-page .leaderboard-filter-form .form-select {
        width: 100%;
        min-width: 0;
    }

    .student-exams-page .leaderboard-card .table-responsive {
        width: 100%;
        margin: 0;
    }

    .student-exams-page .leaderboard-table {
        min-width: 560px;
    }

    .student-exams-page .history-card .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .student-exams-page .history-table {
        width: 100%;
        table-layout: fixed;
    }

    .student-exams-page .history-table th,
    .student-exams-page .history-table td {
        vertical-align: middle;
        overflow-wrap: anywhere;
    }

    .student-exams-page .history-table th:nth-child(1) {
        width: 25%;
    }

    .student-exams-page .history-table th:nth-child(2) {
        width: 17%;
    }

    .student-exams-page .history-table th:nth-child(3) {
        width: 6%;
    }

    .student-exams-page .history-table th:nth-child(4) {
        width: 11%;
    }

    .student-exams-page .history-table th:nth-child(5) {
        width: 9%;
    }

    .student-exams-page .history-table th:nth-child(6) {
        width: 10%;
    }

    .student-exams-page .history-table th:nth-child(7) {
        width: 22%;
    }

    .student-exams-page .history-exam-name {
        white-space: normal;
        overflow-wrap: anywhere;
        line-height: 1.35;
    }

    .student-exams-page .history-test-code {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: .3rem;
        margin-top: .2rem;
    }

    .student-exams-page .history-test-code code {
        display: inline-block;
        max-width: 100%;
        white-space: normal;
        overflow-wrap: anywhere;
        color: var(--exam-muted);
        background: var(--exam-surface-soft);
        border: 1px solid var(--exam-border);
        border-radius: 5px;
        padding: .08rem .3rem;
    }

    .student-exams-page .test-code-input {
        width: 100%;
        max-width: 520px;
        letter-spacing: .06em;
    }

    .student-exams-page .leaderboard-filter-form .form-select {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    @media (max-width: 767.98px) {
        .student-exams-page .history-table {
            min-width: 780px;
        }

        .student-exams-page .test-code-input {
            max-width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .student-exams-page {
            padding-inline: 0;
        }

        .student-exams-page .result-summary-card .exam-card-body {
            padding-inline: 1rem !important;
        }

        .student-exams-page .result-summary-card .display-5 {
            font-size: clamp(2rem, 10vw, 3rem);
        }

        .student-exams-page .result-summary-card .d-flex {
            gap: .5rem !important;
        }

        .student-exams-page .result-summary-card .btn {
            flex: 1 1 150px;
        }

        .student-exams-page .result-review-header>a {
            max-width: 100%;
            white-space: normal;
        }

        .student-exams-page .question-card {
            padding: 1rem;
        }

        .student-exams-page .leaderboard-filter-card .exam-card-body {
            padding: 1rem;
        }
    }


    .student-exams-page .leaderboard-count-badge {
        background: #e0f2fe;
        color: #075985;
        border: 1px solid #7dd3fc;
    }

    .student-exams-page .podium-avatar-wrap {
        position: relative;
        width: 76px;
        height: 76px;
        margin: 0 auto 10px;
    }

    .student-exams-page .podium-avatar,
    .student-exams-page .podium-avatar-fallback {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        object-fit: cover;
        border: 3px solid currentColor;
        box-shadow: 0 4px 12px rgba(15, 23, 42, .15);
    }

    .student-exams-page .podium-avatar-fallback,
    .student-exams-page .leader-avatar-fallback {
        background: #e0f2fe;
        color: #075985;
        font-weight: 850;
    }

    .student-exams-page .podium-icon {
        position: absolute;
        right: -5px;
        bottom: -5px;
        width: 31px;
        height: 31px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 2px solid currentColor;
        font-size: 1rem;
        margin: 0;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .18);
    }

    .student-exams-page .podium-place.podium-1 .podium-icon {
        color: #1d4ed8;
    }

    .student-exams-page .podium-place.podium-2 .podium-icon {
        color: #475569;
    }

    .student-exams-page .podium-place.podium-3 .podium-icon {
        color: #0e7490;
    }

    .student-exams-page .leader-student-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .student-exams-page .leader-avatar,
    .student-exams-page .leader-avatar-fallback {
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #bae6fd;
    }

    .student-exams-page .leaderboard-table .leader-rank.rank-2 {
        background: #e2e8f0;
        border-color: #94a3b8;
        color: #334155;
    }

    .student-exams-page .leaderboard-table .leader-rank.rank-2 i {
        display: inline-block;
        font-size: 1rem;
    }

    .student-exams-page .runner-up-medal {
        display: inline-block;
        font-size: 1.15rem;
        line-height: 1;
    }

    .student-exams-page .leaderboard-table .leader-rank.rank-3 {
        background: #cffafe;
        border-color: #67e8f9;
        color: #155e75;
    }

    @media (max-width: 575.98px) {

        .student-exams-page .podium-avatar-wrap,
        .student-exams-page .podium-avatar,
        .student-exams-page .podium-avatar-fallback {
            width: 58px;
            height: 58px;
        }

        .student-exams-page .podium-icon {
            width: 27px;
            height: 27px;
            font-size: .85rem;
        }
    }

    html[data-theme="dark"] .student-exams-page .podium-place,
    html[data-bs-theme="dark"] .student-exams-page .podium-place,
    body.dark-mode .student-exams-page .podium-place,
    body.dark-theme .student-exams-page .podium-place {
        background: var(--exam-surface-soft);
        color: var(--exam-text);
    }

    .student-exams-page .rank-label-bronze {
        display: inline-block;
        margin-left: 4px;
        font-size: .72rem;
        font-weight: 900;
        color: #9a3412;
    }

    .student-exams-page .table-rank-badge {
        display: inline-flex;
        margin-top: 5px;
        white-space: nowrap;
    }

    .student-exams-page .leaderboard-table td {
        vertical-align: middle;
    }

    .student-exams-page .podium-place.podium-3 .podium-icon {
        color: #b45309;
    }

    .student-exams-page .podium-place.podium-3 .podium-award .leader-achievement.bronze {
        display: inline-flex;
        padding: 6px 11px;
        font-size: .82rem;
        font-weight: 850;
        background: #fed7aa;
        color: #7c2d12;
        border: 2px solid #c2410c;
        box-shadow: 0 3px 0 rgba(124, 45, 18, .16);
    }

    /* Highly visible animated rings for the three podium avatars. */
    .student-exams-page .podium-avatar-wrap {
        isolation: isolate;
    }

    .student-exams-page .podium-avatar-wrap::before {
        content: "";
        position: absolute;
        inset: -7px;
        border-radius: 50%;
        z-index: -1;
        background: conic-gradient(from 0deg, #38bdf8, #2563eb, #a78bfa, #22d3ee, #38bdf8);
        animation: podiumRingSpin 2.2s linear infinite, podiumRingPulse 1.35s ease-in-out infinite;
        filter: drop-shadow(0 0 7px rgba(56, 189, 248, .75));
    }

    .student-exams-page .podium-avatar-wrap::after {
        content: "";
        position: absolute;
        inset: -3px;
        border-radius: 50%;
        z-index: -1;
        background: var(--exam-surface, #fff);
    }

    .student-exams-page .podium-avatar-wrap.podium-avatar-rank-1::before {
        background: conic-gradient(from 0deg, #60a5fa, #1d4ed8, #c4b5fd, #06b6d4, #60a5fa);
        animation-duration: 1.5s, .95s;
        filter: drop-shadow(0 0 10px rgba(37, 99, 235, .95));
    }

    .student-exams-page .podium-avatar-wrap.podium-avatar-rank-2::before {
        background: conic-gradient(from 0deg, #e2e8f0, #64748b, #f8fafc, #94a3b8, #e2e8f0);
        animation-duration: 2s, 1.2s;
        filter: drop-shadow(0 0 8px rgba(148, 163, 184, .9));
    }

    .student-exams-page .podium-avatar-wrap.podium-avatar-rank-3::before {
        background: conic-gradient(from 0deg, #22d3ee, #0e7490, #67e8f9, #3b82f6, #22d3ee);
        animation-duration: 1.8s, 1.05s;
        filter: drop-shadow(0 0 9px rgba(34, 211, 238, .9));
    }

    .student-exams-page .podium-avatar {
        position: relative;
        z-index: 0;
        border: 3px solid #fff;
    }

    @keyframes podiumRingSpin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes podiumRingPulse {

        0%,
        100% {
            opacity: .8;
            scale: 1;
        }

        50% {
            opacity: 1;
            scale: 1.07;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .student-exams-page .podium-avatar-wrap::before {
            animation: none;
        }
    }
</style>
<script>
    (function() {

    })();
</script>
<?php ex_page_end(true); ?>
<style>
    .student-exams-page .leader-achievement.gold {
        background: #dbeafe !important;
        color: #1d4ed8 !important;
        border: 1px solid #60a5fa !important;
        box-shadow: 0 2px 5px rgba(29, 78, 216, .12) !important;
    }

    .student-exams-page .leader-achievement.silver {
        background: #e2e8f0 !important;
        color: #334155 !important;
        border: 1px solid #94a3b8 !important;
    }

    .student-exams-page .leader-achievement.bronze {
        background: #cffafe !important;
        color: #155e75 !important;
        border: 1px solid #67e8f9 !important;
    }
</style>

<style>
    .student-exams-page .leaderboard-table .leader-rank.rank-2 .bi-award-fill {
        display: inline-block;
        font-size: 1rem;
        line-height: 1;
        color: #475569;
    }

    .student-exams-page .leader-time-taken {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        padding: 5px 8px;
        border-radius: 7px;
        background: rgba(37, 99, 235, .08);
        border: 1px solid rgba(37, 99, 235, .22);
        color: var(--exam-text);
        font-size: .78rem;
        font-weight: 650;
        font-variant-numeric: tabular-nums;
    }

    html[data-theme="dark"] .student-exams-page .leader-time-taken {
        background: rgba(96, 165, 250, .12);
        border-color: rgba(96, 165, 250, .28);
    }
</style>

<style>
    /* Text-only achievement badges (no icon glyphs in the last column). */
    .student-exams-page .leaderboard-table td:last-child .leader-achievement,
    .student-exams-page .podium-award .leader-achievement {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0;
    }

    .student-exams-page .leader-achievement.completed {
        background: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: .75rem;
        font-weight: 750;
    }

    /* Bright neon rings rotate and glow differently for each podium rank. */
    .student-exams-page .podium-avatar-wrap::before {
        inset: -9px !important;
        background: conic-gradient(from 0deg, var(--neon-a), var(--neon-b), var(--neon-c), var(--neon-a)) !important;
        animation: leaderNeonSpin 1.35s linear infinite, leaderNeonGlow 1s ease-in-out infinite alternate !important;
        filter: drop-shadow(0 0 5px var(--neon-a)) drop-shadow(0 0 12px var(--neon-b)) drop-shadow(0 0 20px var(--neon-c)) !important;
    }

    .student-exams-page .podium-avatar-wrap.podium-avatar-rank-1 {
        --neon-a: #38bdf8;
        --neon-b: #2563eb;
        --neon-c: #a78bfa;
    }

    .student-exams-page .podium-avatar-wrap.podium-avatar-rank-2 {
        --neon-a: #f8fafc;
        --neon-b: #94a3b8;
        --neon-c: #64748b;
    }

    .student-exams-page .podium-avatar-wrap.podium-avatar-rank-3 {
        --neon-a: #22d3ee;
        --neon-b: #06b6d4;
        --neon-c: #0e7490;
    }

    .student-exams-page .podium-avatar-wrap::after {
        inset: -4px !important;
    }

    .student-exams-page .podium-avatar {
        border: 3px solid #fff;
        box-shadow: 0 0 10px var(--neon-a, #38bdf8), 0 0 20px var(--neon-b, #2563eb);
    }

    @keyframes leaderNeonSpin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes leaderNeonGlow {
        from {
            opacity: .78;
        }

        to {
            opacity: 1;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .student-exams-page .podium-avatar-wrap::before {
            animation: none !important;
        }
    }
</style>

<style>
    /* Prominent rotating neon borders around each podium card, with extra breathing room. */
    .student-exams-page .leaderboard-podium {
        gap: 18px !important;
        padding: 24px !important;
    }

    .student-exams-page .podium-place {
        position: relative;
        isolation: isolate;
        overflow: visible !important;
        padding: 24px 16px 22px !important;
        border: 2px solid transparent !important;
        border-radius: 20px !important;
        box-shadow: 0 8px 24px rgba(15, 23, 42, .10);
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .student-exams-page .podium-place::before {
        content: "";
        position: absolute;
        inset: -3px;
        border-radius: 22px;
        z-index: -1;
        background: conic-gradient(from 0deg, var(--podium-a), var(--podium-b), var(--podium-c), var(--podium-a));
        animation: podiumCardBorderSpin 2.4s linear infinite, podiumCardBorderGlow 1.35s ease-in-out infinite alternate;
        box-shadow: 0 0 12px var(--podium-glow), 0 0 25px var(--podium-glow);
    }

    .student-exams-page .podium-place::after {
        content: "";
        position: absolute;
        inset: 2px;
        border-radius: 15px;
        z-index: -1;
        pointer-events: none;
        opacity: .35;
        background: linear-gradient(145deg, rgba(255, 255, 255, .28), transparent 55%);
    }

    .student-exams-page .podium-place>* {
        position: relative;
    }

    .student-exams-page .podium-place.podium-1 {
        --podium-a: #38bdf8;
        --podium-b: #2563eb;
        --podium-c: #c4b5fd;
        --podium-glow: rgba(37, 99, 235, .65);
        padding-top: 30px !important;
        box-shadow: 0 10px 30px rgba(37, 99, 235, .20);
    }

    .student-exams-page .podium-place.podium-2 {
        --podium-a: #f8fafc;
        --podium-b: #64748b;
        --podium-c: #cbd5e1;
        --podium-glow: rgba(148, 163, 184, .65);
    }

    .student-exams-page .podium-place.podium-3 {
        --podium-a: #22d3ee;
        --podium-b: #0e7490;
        --podium-c: #67e8f9;
        --podium-glow: rgba(6, 182, 212, .65);
    }

    .student-exams-page .podium-place:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 32px var(--podium-glow, rgba(37, 99, 235, .18));
    }

    .student-exams-page .podium-avatar-wrap {
        margin-bottom: 16px !important;
    }

    .student-exams-page .podium-rank {
        margin-top: 6px;
    }

    .student-exams-page .podium-name {
        margin-top: 12px !important;
        line-height: 1.4;
    }

    .student-exams-page .podium-score {
        margin-top: 9px !important;
    }

    .student-exams-page .podium-points {
        margin-top: 4px;
    }

    @keyframes podiumCardBorderSpin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes podiumCardBorderGlow {
        from {
            opacity: .78;
        }

        to {
            opacity: 1;
        }
    }

    @media (max-width: 767.98px) {
        .student-exams-page .leaderboard-podium {
            gap: 12px !important;
            padding: 18px 12px !important;
        }

        .student-exams-page .podium-place {
            padding: 18px 9px 16px !important;
            border-radius: 16px !important;
        }

        .student-exams-page .podium-place.podium-1 {
            padding-top: 22px !important;
        }

        .student-exams-page .podium-place::before {
            border-radius: 18px;
        }
    }

    @media (max-width: 575.98px) {
        .student-exams-page .leaderboard-podium {
            gap: 10px !important;
            padding: 14px 8px !important;
        }

        .student-exams-page .podium-place {
            padding: 15px 6px 14px !important;
        }

        .student-exams-page .podium-place.podium-1 {
            padding-top: 18px !important;
        }

        .student-exams-page .podium-name {
            font-size: .84rem;
            overflow-wrap: anywhere;
        }

        .student-exams-page .podium-score {
            font-size: 1.1rem !important;
        }

        .student-exams-page .podium-points {
            font-size: .74rem !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .student-exams-page .podium-place::before {
            animation: none !important;
        }

        .student-exams-page .podium-place {
            transition: none !important;
        }
    }
</style>