<?php
/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   LOGIN API — USERNAME OR EMAIL
   Supports: admin, instructor, student, quiz_master,
             calendar_master
   ========================================================= */

date_default_timezone_set('Asia/Manila');
header('Content-Type: application/json; charset=UTF-8');

/* =========================================================
   START SESSION
   ========================================================= */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/* =========================================================
   HELPER: JSON RESPONSE
   ========================================================= */
function respond(int $status, array $payload): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/* =========================================================
   ONLY ALLOW POST REQUEST
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(405, [
        'success' => false,
        'message' => 'Only POST requests are allowed.'
    ]);
}

/* =========================================================
   DATABASE CONNECTION
   ========================================================= */
require_once __DIR__ . '/../src/connection.php';

if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    respond(500, [
        'success' => false,
        'message' => 'Database connection is unavailable.'
    ]);
}

/* =========================================================
   READ JSON REQUEST
   ========================================================= */
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!is_array($input)) {
    respond(400, [
        'success' => false,
        'message' => 'Invalid JSON request.'
    ]);
}

$login = trim((string)($input['login'] ?? ''));
$password = (string)($input['password'] ?? '');

if ($login === '' || $password === '') {
    respond(400, [
        'success' => false,
        'message' => 'Please enter your username or email and password.'
    ]);
}

if (strlen($login) > 100) {
    respond(400, [
        'success' => false,
        'message' => 'Invalid username or email.'
    ]);
}

/* =========================================================
   FIND ACCOUNT BY EMAIL OR USERNAME
   ========================================================= */
$isEmail = filter_var($login, FILTER_VALIDATE_EMAIL) !== false;
$column = $isEmail ? 'email' : 'username';

$sql = "SELECT
            id,
            last_name,
            first_name,
            middle_initial,
            extension_name,
            department,
            year_section,
            student_id,
            email,
            username,
            password,
            access,
            created_at
        FROM accounts
        WHERE {$column} = ?
        LIMIT 1";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    error_log('Login prepare failed: ' . $mysqli->error);
    respond(500, [
        'success' => false,
        'message' => 'Database error.'
    ]);
}

$stmt->bind_param('s', $login);
if (!$stmt->execute()) {
    error_log('Login execute failed: ' . $stmt->error);
    $stmt->close();
    respond(500, [
        'success' => false,
        'message' => 'Unable to process login.'
    ]);
}

$result = $stmt->get_result();
$account = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$account) {
    $mysqli->close();
    respond(401, [
        'success' => false,
        'message' => 'Invalid username/email or password.'
    ]);
}

/* =========================================================
   VERIFY PASSWORD
   Passwords must be stored using password_hash().
   ========================================================= */
if (!password_verify($password, (string)$account['password'])) {
    $mysqli->close();
    respond(401, [
        'success' => false,
        'message' => 'Invalid username/email or password.'
    ]);
}

/* =========================================================
   VERIFY ACCESS ROLE
   ========================================================= */
$allowedAccess = [
    'admin',
    'instructor',
    'student',
    'quiz_master',
    'calendar_master'
];

$access = strtolower(trim((string)($account['access'] ?? '')));
if (!in_array($access, $allowedAccess, true)) {
    $mysqli->close();
    respond(403, [
        'success' => false,
        'message' => 'Your account does not have valid access.'
    ]);
}
$account['access'] = $access;

/* =========================================================
   ROLE-BASED DASHBOARD REDIRECT
   Change folder/file names here if your structure differs.
   ========================================================= */
$redirects = [
    'admin'           => './admin/index.php',
    'instructor'      => './instructor/index.php',
    'student'         => './student/index.php',
    'quiz_master'     => './qm/index.php',
    'calendar_master' => './cm/index.php'
];
$redirect = $redirects[$access];

/* =========================================================
   REGENERATE SESSION ID AND STORE AUTHENTICATED USER
   ========================================================= */
session_regenerate_id(true);

$_SESSION['logged_in'] = true;
$_SESSION['user'] = [
    'id' => (int)$account['id'],
    'username' => $account['username'],
    'first_name' => $account['first_name'],
    'last_name' => $account['last_name'],
    'middle_initial' => $account['middle_initial'],
    'extension_name' => $account['extension_name'],
    'department' => $account['department'],
    'year_section' => $account['year_section'],
    'student_id' => $account['student_id'],
    'email' => $account['email'],
    'access' => $access
];

// Compatibility values for pages that use these session keys.
$_SESSION['user_id'] = (int)$account['id'];
$_SESSION['username'] = $account['username'];
$_SESSION['access'] = $access;
$_SESSION['student_id'] = $account['student_id'];

$mysqli->close();

/* =========================================================
   SUCCESS RESPONSE
   ========================================================= */
respond(200, [
    'success' => true,
    'message' => 'Login successful.',
    'access' => $access,
    'user' => $_SESSION['user'],
    'redirect' => $redirect
]);
