<?php
/* ETS-Async | Calendar-first Academic Calendar Management */
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
date_default_timezone_set('Asia/Manila');
if (empty($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || empty($_SESSION['user']) || !in_array(strtolower((string)($_SESSION['user']['access'] ?? '')), ['admin', 'administrator'], true)) {
    if (isset($_POST['ajax'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'message' => 'Your session has expired. Please sign in again.']);
        exit;
    }
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../src/connection.php';
if (!isset($mysqli) || !$mysqli instanceof mysqli) {
    http_response_code(500);
    exit('Database connection is unavailable.');
}
$mysqli->set_charset('utf8mb4');
if (empty($_SESSION['academic_calendar_csrf'])) $_SESSION['academic_calendar_csrf'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['academic_calendar_csrf'];
$defs = [
    'event' => ['table' => 'academic_calendar_events', 'date' => 'event_date', 'kind' => 'Event', 'category' => 'Academic Event'],
    'activity' => ['table' => 'academic_calendar_activities', 'date' => 'activity_date', 'kind' => 'Activity', 'category' => 'Academic Activity']
];
function cal_e($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function cal_null($v): ?string
{
    $v = trim((string)$v);
    return $v === '' ? null : $v;
}
function cal_valid_date($v): bool
{
    if (!is_string($v) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return false;
    $d = DateTime::createFromFormat('!Y-m-d', $v);
    return $d && $d->format('Y-m-d') === $v;
}
function cal_json($payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// All database mutations happen through AJAX requests from the calendar modal.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    if (!hash_equals($csrf, (string)($_POST['csrf'] ?? ''))) cal_json(['ok' => false, 'message' => 'Session token expired. Refresh the page and try again.'], 419);
    $kind = (string)($_POST['kind'] ?? 'activity');
    if (!isset($defs[$kind])) cal_json(['ok' => false, 'message' => 'Invalid calendar entry type.'], 400);
    $def = $defs[$kind];
    $table = $def['table'];
    $dateCol = $def['date'];
    $action = (string)($_POST['action'] ?? 'save');
    $id = max(0, (int)($_POST['id'] ?? 0));
    if ($action === 'delete') {
        if ($id < 1) cal_json(['ok' => false, 'message' => 'Invalid entry selected.'], 400);
        $stmt = $mysqli->prepare("DELETE FROM `{$table}` WHERE id=?");
        if (!$stmt) cal_json(['ok' => false, 'message' => 'Could not prepare deletion. Check that the calendar SQL was imported.'], 500);
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        if (!$ok || !$affected) cal_json(['ok' => false, 'message' => 'Entry could not be deleted or no longer exists.'], 404);
        cal_json(['ok' => true, 'message' => $def['kind'] . ' deleted.', 'id' => $kind . '-' . $id]);
    }
    if ($action !== 'save') cal_json(['ok' => false, 'message' => 'Unsupported action.'], 400);
    $title = trim((string)($_POST['title'] ?? ''));
    $description = trim((string)($_POST['description'] ?? ''));
    $category = trim((string)($_POST['category'] ?? $def['category']));
    $start = trim((string)($_POST['calendar_date'] ?? ''));
    $due = $kind === 'activity' ? cal_null($_POST['due_date'] ?? '') : null;
    $startTime = cal_null($_POST['start_time'] ?? '');
    $endTime = cal_null($_POST['end_time'] ?? '');
    $location = cal_null($_POST['location'] ?? '');
    $department = trim((string)($_POST['department'] ?? ''));
    $year = trim((string)($_POST['year_level'] ?? ''));
    $section = trim((string)($_POST['section'] ?? ''));
    $status = (($_POST['status'] ?? 'active') === 'inactive') ? 'inactive' : 'active';
    $creator = trim((string)($_SESSION['user']['username'] ?? $_SESSION['user']['email'] ?? $_SESSION['user']['first_name'] ?? 'Admin'));
    if ($title === '') cal_json(['ok' => false, 'message' => 'Please enter an activity or event name.'], 422);
    if (!cal_valid_date($start)) cal_json(['ok' => false, 'message' => 'Choose a valid start date.'], 422);
    if ($due !== null && (!cal_valid_date($due) || $due < $start)) cal_json(['ok' => false, 'message' => 'The due date must be on or after the start date.'], 422);
    if ($startTime !== null && !preg_match('/^\d{2}:\d{2}$/', $startTime)) cal_json(['ok' => false, 'message' => 'Invalid start time.'], 422);
    if ($endTime !== null && !preg_match('/^\d{2}:\d{2}$/', $endTime)) cal_json(['ok' => false, 'message' => 'Invalid end time.'], 422);
    if ($kind === 'event') {
        if ($id > 0) {
            $sql = 'UPDATE academic_calendar_events SET title=?,description=?,category=?,event_date=?,start_time=?,end_time=?,location=?,department=?,year_level=?,section=?,status=? WHERE id=?';
            $stmt = $mysqli->prepare($sql);
            if ($stmt) $stmt->bind_param('sssssssssssi', $title, $description, $category, $start, $startTime, $endTime, $location, $department, $year, $section, $status, $id);
        } else {
            $sql = 'INSERT INTO academic_calendar_events (title,description,category,event_date,start_time,end_time,location,department,year_level,section,status,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
            $stmt = $mysqli->prepare($sql);
            if ($stmt) $stmt->bind_param('ssssssssssss', $title, $description, $category, $start, $startTime, $endTime, $location, $department, $year, $section, $status, $creator);
        }
    } else {
        if ($id > 0) {
            $sql = 'UPDATE academic_calendar_activities SET title=?,description=?,category=?,activity_date=?,due_date=?,start_time=?,end_time=?,location=?,department=?,year_level=?,section=?,status=? WHERE id=?';
            $stmt = $mysqli->prepare($sql);
            if ($stmt) $stmt->bind_param('ssssssssssssi', $title, $description, $category, $start, $due, $startTime, $endTime, $location, $department, $year, $section, $status, $id);
        } else {
            $sql = 'INSERT INTO academic_calendar_activities (title,description,category,activity_date,due_date,start_time,end_time,location,department,year_level,section,status,created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)';
            $stmt = $mysqli->prepare($sql);
            if ($stmt) $stmt->bind_param('sssssssssssss', $title, $description, $category, $start, $due, $startTime, $endTime, $location, $department, $year, $section, $status, $creator);
        }
    }
    if (!$stmt) cal_json(['ok' => false, 'message' => 'Could not prepare save. Check that the academic calendar SQL tables exist.'], 500);
    $ok = $stmt->execute();
    if (!$ok) {
        $stmt->close();
        cal_json(['ok' => false, 'message' => 'Database save failed. Please check the database schema and try again.'], 500);
    }
    if ($id === 0) $id = (int)$mysqli->insert_id;
    $stmt->close();
    $stmt = $mysqli->prepare("SELECT * FROM `{$table}` WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) cal_json(['ok' => false, 'message' => 'Saved, but could not reload the calendar entry.'], 500);
    $startDate = $row[$dateCol];
    $endDate = $kind === 'activity' && !empty($row['due_date']) ? $row['due_date'] : $startDate;
    cal_json(['ok' => true, 'message' => $def['kind'] . ($action === 'save' && isset($_POST['id']) && (int)$_POST['id'] > 0 ? ' updated.' : ' saved.'), 'event' => [
        'id' => $kind . '-' . $id,
        'title' => $row['title'],
        'start' => $startDate,
        'end' => (new DateTime($endDate))->modify('+1 day')->format('Y-m-d'),
        'allDay' => true,
        'backgroundColor' => $row['status'] === 'active' ? 'hsl(' . ((($id * 137) + ($kind === 'event' ? 25 : 185)) % 360) . ', 68%, 43%)' : '#98a2b3',
        'borderColor' => 'transparent',
        'extendedProps' => ['kind' => $kind, 'recordId' => $id, 'description' => $row['description'] ?? '', 'category' => $row['category'] ?? '', 'dueDate' => $kind === 'activity' ? ($row['due_date'] ?? '') : '', 'startTime' => $row['start_time'] ?? '', 'endTime' => $row['end_time'] ?? '', 'location' => $row['location'] ?? '', 'department' => $row['department'] ?? '', 'yearLevel' => $row['year_level'] ?? '', 'section' => $row['section'] ?? '', 'status' => $row['status'] ?? 'active']
    ]]);
}

$calendarEvents = [];
foreach (['event' => 'events', 'activity' => 'activities'] as $kind => $var) {
    $def = $defs[$kind];
    $table = $def['table'];
    $dateCol = $def['date'];
    $result = $mysqli->query("SELECT * FROM `{$table}` ORDER BY `{$dateCol}` ASC, id ASC");
    if ($result) {
        while ($r = $result->fetch_assoc()) {
            $start = $r[$dateCol];
            $last = $kind === 'activity' && !empty($r['due_date']) ? $r['due_date'] : $start;
            $id = (int)$r['id'];
            $calendarEvents[] = ['id' => $kind . '-' . $id, 'title' => $r['title'] ?: 'Activity (details to be added)', 'start' => $start, 'end' => (new DateTime($last))->modify('+1 day')->format('Y-m-d'), 'allDay' => true, 'backgroundColor' => $r['status'] === 'active' ? 'hsl(' . ((($id * 137) + ($kind === 'event' ? 25 : 185)) % 360) . ', 68%, 43%)' : '#98a2b3', 'borderColor' => 'transparent', 'extendedProps' => ['kind' => $kind, 'recordId' => $id, 'description' => $r['description'] ?? '', 'category' => $r['category'] ?? '', 'dueDate' => $kind === 'activity' ? ($r['due_date'] ?? '') : '', 'startTime' => $r['start_time'] ?? '', 'endTime' => $r['end_time'] ?? '', 'location' => $r['location'] ?? '', 'department' => $r['department'] ?? '', 'yearLevel' => $r['year_level'] ?? '', 'section' => $r['section'] ?? '', 'status' => $r['status'] ?? 'active']];
        }
        $result->free();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'globals/head.php'; ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
<style>
    :root {
        --ink: #172033;
        --muted: #667085;
        --line: #e5eaf2;
        --blue: #315eea;
        --surface: #f5f7fb
    }

    * {
        box-sizing: border-box
    }

    body {
        background: var(--surface);
        color: var(--ink);
        font-family: Inter, system-ui, -apple-system, sans-serif
    }

    .page {
        max-width: 1500px;
        margin: auto;
        padding: 26px 18px
    }

    .hero,
    .calendar-shell {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(22, 34, 55, .04)
    }

    .hero {
        padding: 22px 24px;
        margin-bottom: 18px;
        display: flex;
        justify-content: space-between;
        gap: 18px;
        align-items: center
    }

    .hero h1 {
        font-size: clamp(1.3rem, 2.5vw, 1.9rem);
        font-weight: 800;
        margin: 0
    }

    .hero p {
        color: var(--muted);
        margin: 7px 0 0
    }

    .calendar-shell {
        padding: 20px
    }

    .calendar-shell .fc .fc-toolbar-title {
        font-size: clamp(1rem, 2vw, 1.35rem);
        font-weight: 850;
        color: #182b59
    }

    .calendar-shell .fc .fc-col-header-cell-cushion,
    .calendar-shell .fc .fc-daygrid-day-number {
        text-decoration: none !important
    }

    .calendar-shell .fc .fc-col-header-cell-cushion {
        color: #334155;
        font-weight: 800;
        padding: 10px 4px
    }

    .calendar-shell .fc .fc-col-header-cell {
        background: #eef4ff
    }

    .calendar-shell .fc .fc-day-today {
        background: #fff5d6 !important
    }

    .calendar-shell .fc .fc-button-primary {
        background: #315eea;
        border-color: #315eea
    }

    .calendar-shell .fc .fc-button-primary:hover {
        background: #2048c9;
        border-color: #2048c9
    }

    .calendar-shell .fc .fc-button-primary:focus {
        box-shadow: 0 0 0 .2rem #315eea40
    }

    .calendar-shell .fc .fc-button {
        border-radius: 8px;
        background: var(--blue);
        border-color: var(--blue);
        font-weight: 700
    }

    .calendar-shell .fc .fc-daygrid-day-number {
        color: var(--ink);
        text-decoration: none;
        padding: 8px
    }

    .calendar-shell .fc .fc-daygrid-day-frame {
        min-height: 100px
    }

    .fc-event {
        cursor: pointer;
        border-radius: 5px;
        padding: 2px 4px
    }

    .calendar-help {
        color: var(--muted);
        font-size: .9rem
    }

    .form-label {
        font-size: .82rem;
        font-weight: 750
    }

    .form-control,
    .form-select {
        border-color: #dce3ee;
        border-radius: 10px;
        min-height: 42px
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #8aa3ff;
        box-shadow: 0 0 0 .2rem #315eea1a
    }

    .btn {
        border-radius: 10px;
        font-weight: 700
    }

    .modal-content {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 20px 70px #10182833;
        max-height: calc(100dvh - 2rem);
        display: flex;
        flex-direction: column
    }

    .modal-dialog {
        max-width: min(900px, calc(100vw - 1.5rem));
        margin: 1rem auto
    }

    .modal-body {
        overflow-y: auto;
        overscroll-behavior: contain;
        min-height: 0
    }

    .modal-header {
        border-bottom: 1px solid var(--line);
        padding: 20px 22px
    }

    .modal-body {
        padding: 22px
    }

    .modal-footer {
        border-top: 1px solid var(--line);
        padding: 16px 22px
    }

    .entry-meta {
        font-size: .84rem;
        color: var(--muted)
    }

    .legend-dot {
        display: inline-block;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: var(--blue);
        margin-right: 6px
    }

    @media(max-width:767px) {
        .page {
            padding: 12px 9px
        }

        .hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 17px
        }

        .calendar-shell {
            padding: 10px
        }

        .calendar-shell .fc .fc-daygrid-day-frame {
            min-height: 65px
        }

        .calendar-shell .fc .fc-toolbar {
            gap: 8px;
            flex-wrap: wrap
        }

        .calendar-shell .fc .fc-toolbar-title {
            font-size: 1rem
        }

        .calendar-shell .fc .fc-button {
            padding: .35rem .5rem;
            font-size: .8rem
        }

        .modal-body {
            padding: 16px
        }
    }

    .page {
        max-width: none;
        margin: 0;
        padding: 22px 24px
    }

    .calendar-layout {
        display: grid;
        grid-template-columns: minmax(0, 3fr) minmax(285px, 1fr);
        gap: 20px;
        align-items: start
    }

    .calendar-shell {
        min-width: 0;
        padding: 18px
    }

    .side-overview {
        display: grid;
        gap: 16px;
        min-width: 0
    }

    .overview-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 18px;
        box-shadow: 0 8px 25px rgba(22, 34, 55, .04);
        overflow: hidden
    }

    .overview-head {
        padding: 17px 18px 13px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border-bottom: 1px solid #eef1f6
    }

    .overview-title {
        font-size: .98rem;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 9px
    }

    .overview-subtitle {
        font-size: .78rem;
        color: var(--muted);
        margin-top: 4px
    }

    .overview-count {
        font-size: .75rem;
        font-weight: 850;
        padding: 5px 10px;
        border-radius: 999px;
        background: #e0eaff;
        color: #173fc4;
        border: 1px solid #c7d7ff
    }

    .overview-card:nth-child(1) .overview-title i {
        color: #e8790c !important
    }

    .overview-card:nth-child(2) .overview-title i {
        color: #2563eb !important
    }

    .overview-card:nth-child(3) .overview-title i {
        color: #0f9f6e !important
    }

    .overview-card:nth-child(1) .overview-count {
        background: #fff0d9;
        color: #a34c00;
        border-color: #ffd9a0
    }

    .overview-card:nth-child(3) .overview-count {
        background: #dff8eb;
        color: #087443;
        border-color: #b3efd0
    }

    .overview-list {
        padding: 7px 12px 12px;
        max-height: 245px;
        overflow: auto
    }

    .overview-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 8px;
        border-bottom: 1px solid #f0f2f6;
        cursor: pointer;
        border-radius: 10px;
        transition: background .15s
    }

    .overview-item:last-child {
        border-bottom: 0
    }

    .overview-item:hover {
        background: #eef4ff
    }

    .overview-item:focus-visible {
        outline: 2px solid #315eea;
        outline-offset: 2px
    }

    .overview-color {
        width: 4px;
        min-height: 42px;
        align-self: stretch;
        border-radius: 8px;
        flex-shrink: 0
    }

    .overview-item-main {
        min-width: 0;
        flex: 1
    }

    .overview-item-title {
        font-size: .88rem;
        font-weight: 750;
        line-height: 1.35;
        overflow-wrap: anywhere
    }

    .overview-item-meta {
        font-size: .76rem;
        color: var(--muted);
        margin-top: 5px;
        display: flex;
        gap: 7px;
        flex-wrap: wrap
    }

    .overview-empty {
        padding: 24px 16px;
        text-align: center;
        color: var(--muted);
        font-size: .84rem
    }

    .overview-empty i {
        display: block;
        font-size: 1.5rem;
        color: #a5b4fc;
        margin-bottom: 7px
    }

    .calendar-shell .fc .fc-daygrid-day-frame {
        min-height: 88px
    }

    .fc .fc-col-header-cell-cushion {
        text-decoration: none !important
    }

    .fc .fc-scrollgrid a {
        text-decoration: none !important
    }

    @media(max-width:1100px) {
        .calendar-layout {
            grid-template-columns: minmax(0, 1fr)
        }

        .side-overview {
            grid-template-columns: repeat(2, minmax(0, 1fr))
        }

        .overview-card:last-child {
            grid-column: 1/-1
        }
    }

    @media(max-width:767px) {
        .page {
            padding: 12px 9px
        }

        .calendar-layout {
            gap: 14px
        }

        .side-overview {
            grid-template-columns: minmax(0, 1fr)
        }

        .overview-card:last-child {
            grid-column: auto
        }

        .calendar-shell {
            padding: 10px
        }

        .calendar-shell .fc .fc-daygrid-day-frame {
            min-height: 65px
        }

        .calendar-shell .fc .fc-toolbar {
            gap: 8px;
            flex-wrap: wrap
        }

        .calendar-shell .fc .fc-toolbar-title {
            font-size: 1rem
        }

        .calendar-shell .fc .fc-button {
            padding: .35rem .5rem;
            font-size: .8rem
        }

        .overview-list {
            max-height: 220px
        }

        .modal-dialog {
            margin: .5rem auto;
            max-width: calc(100vw - 1rem)
        }

        .modal-content {
            max-height: calc(100dvh - 1rem);
            border-radius: 14px
        }

        .modal-header {
            padding: 15px
        }

        .modal-body {
            padding: 14px
        }

        .modal-footer {
            padding: 12px;
            gap: 8px;
            flex-wrap: wrap
        }

        .modal-footer .ms-auto {
            width: 100%;
            justify-content: flex-end
        }

        .modal-footer #deleteEntryBtn {
            margin-right: auto
        }
    }

    /* Reliable modal scrolling on desktop, tablet, mobile and short landscape screens */
    #entryModal {
        overflow-y: auto !important;
        overscroll-behavior: contain;
        padding: 0 !important
    }

    #entryModal .modal-dialog {
        width: min(900px, calc(100vw - 2rem));
        max-width: none;
        margin: 1rem auto;
        height: calc(100% - 2rem);
        min-height: 0;
        display: flex;
        align-items: stretch
    }

    #entryModal .modal-content {
        width: 100%;
        height: 100%;
        max-height: calc(100dvh - 2rem);
        min-height: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column
    }

    #entryModal #entryForm {
        display: flex;
        flex-direction: column;
        flex: 1 1 auto;
        min-height: 0;
        overflow: hidden
    }

    #entryModal .modal-header,
    #entryModal .modal-footer {
        flex: 0 0 auto;
        background: #fff;
        position: relative;
        z-index: 2
    }

    #entryModal .modal-body {
        display: block;
        flex: 1 1 auto;
        min-height: 0;
        height: auto;
        max-height: none;
        overflow-y: auto !important;
        overflow-x: hidden;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y
    }

    #entryModal .modal-body .row {
        min-width: 0
    }

    #entryModal .modal-body input,
    #entryModal .modal-body select,
    #entryModal .modal-body textarea {
        max-width: 100%
    }

    #entryModal .modal-footer {
        padding-bottom: max(16px, env(safe-area-inset-bottom))
    }

    @media(max-width:767px) {
        #entryModal .modal-dialog {
            width: calc(100vw - 1rem);
            margin: .5rem auto;
            height: calc(100% - 1rem)
        }

        #entryModal .modal-content {
            max-height: calc(100dvh - 1rem);
            border-radius: 14px
        }

        #entryModal .modal-header {
            padding: 13px 15px
        }

        #entryModal .modal-body {
            padding: 14px
        }

        #entryModal .modal-footer {
            padding: 11px 14px max(11px, env(safe-area-inset-bottom));
            gap: 8px
        }

        #entryModal .modal-footer .ms-auto {
            width: 100%;
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap
        }

        #entryModal .modal-footer #deleteEntryBtn {
            margin-right: auto
        }
    }

    @media(max-height:560px) {
        #entryModal .modal-dialog {
            margin: .25rem auto;
            height: calc(100% - .5rem);
            width: min(900px, calc(100vw - .75rem))
        }

        #entryModal .modal-content {
            max-height: calc(100dvh - .5rem);
            border-radius: 12px
        }

        #entryModal .modal-header {
            padding: 9px 14px
        }

        #entryModal .modal-header .modal-title {
            font-size: 1rem !important
        }

        #entryModal .modal-body {
            padding: 12px 14px
        }

        #entryModal .modal-footer {
            padding: 8px 12px
        }
    }
</style>
</head>

<body>
    <?php include 'globals/sidebar.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <div class="page">
                <header class="hero">
                    <div>
                        <h1><i class="bi bi-calendar2-week text-primary me-2"></i>Academic Calendar</h1>
                        <p>Manage events and activities from the calendar. Click a day or drag across dates to add an entry; click an entry to edit or delete it.</p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap"><button class="btn btn-primary" id="newEntryBtn"><i class="bi bi-plus-lg me-1"></i>New entry</button></div>
                </header>
                <div id="toastArea" aria-live="polite"></div>
                <div class="calendar-layout">
                    <section class="calendar-shell">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div class="calendar-help"><span class="legend-dot"></span>Each entry has its own color. Overlapping activities remain separate.</div>
                            <div class="calendar-help"><i class="bi bi-mouse me-1"></i>Click to add one day · Drag to select a date range</div>
                        </div>
                        <div id="academicCalendar"></div>
                    </section>
                    <aside class="side-overview" aria-label="Calendar event overview">
                        <section class="overview-card">
                            <div class="overview-head">
                                <div>
                                    <h2 class="overview-title"><i class="bi bi-calendar2-check text-primary"></i>Events Today</h2>
                                    <div class="overview-subtitle">Entries scheduled for today</div>
                                </div><span class="overview-count" id="todayCount">0</span>
                            </div>
                            <div class="overview-list" id="todayList"></div>
                        </section>
                        <section class="overview-card">
                            <div class="overview-head">
                                <div>
                                    <h2 class="overview-title"><i class="bi bi-calendar2-week text-primary"></i>Upcoming</h2>
                                    <div class="overview-subtitle">Next scheduled events and activities</div>
                                </div><span class="overview-count" id="upcomingCount">0</span>
                            </div>
                            <div class="overview-list" id="upcomingList"></div>
                        </section>
                        <section class="overview-card">
                            <div class="overview-head">
                                <div>
                                    <h2 class="overview-title"><i class="bi bi-calendar2-minus text-secondary"></i>Recently Ended</h2>
                                    <div class="overview-subtitle">Most recently completed entries</div>
                                </div><span class="overview-count" id="endedCount">0</span>
                            </div>
                            <div class="overview-list" id="endedList"></div>
                        </section>
                    </aside>
                </div>
            </div>
        </div>
    </main>
    <div class="modal fade" id="entryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="entryForm">
                    <div class="modal-header">
                        <div>
                            <h2 class="modal-title fs-5 fw-bold" id="modalTitle">New calendar entry</h2>
                            <div class="entry-meta mt-1" id="selectedDateLabel">Select a date on the calendar</div>
                        </div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"><input type="hidden" name="ajax" value="1"><input type="hidden" name="csrf" value="<?= cal_e($csrf) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="0">
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label" for="entryKind">Entry type</label><select class="form-select" id="entryKind" name="kind">
                                    <option value="activity">Activity</option>
                                    <option value="event">General event</option>
                                </select></div>
                            <div class="col-md-8"><label class="form-label" for="entryTitle">Name / title *</label><input class="form-control" id="entryTitle" name="title" required maxlength="190" placeholder="e.g. Midterm Examination"></div>
                            <div class="col-md-4"><label class="form-label" for="entryStart">Start date *</label><input class="form-control" type="date" id="entryStart" name="calendar_date" required></div>
                            <div class="col-md-4" id="dueWrap"><label class="form-label" for="entryDue">Due date (optional)</label><input class="form-control" type="date" id="entryDue" name="due_date"></div>
                            <div class="col-md-4"><label class="form-label" for="entryCategory">Category</label><input class="form-control" id="entryCategory" name="category" value="Academic Activity" placeholder="Exam, deadline, seminar..."></div>
                            <div class="col-md-6"><label class="form-label" for="entryStartTime">Start time (optional)</label><input class="form-control" type="time" id="entryStartTime" name="start_time"></div>
                            <div class="col-md-6"><label class="form-label" for="entryEndTime">End time (optional)</label><input class="form-control" type="time" id="entryEndTime" name="end_time"></div>
                            <div class="col-md-6"><label class="form-label" for="entryLocation">Location / venue</label><input class="form-control" id="entryLocation" name="location" maxlength="190" placeholder="Optional"></div>
                            <div class="col-md-6"><label class="form-label" for="entryDepartment">Department / program</label><input class="form-control" id="entryDepartment" name="department" placeholder="Blank means all departments"></div>
                            <div class="col-md-4"><label class="form-label" for="entryYear">Year level</label><input class="form-control" id="entryYear" name="year_level" placeholder="All years"></div>
                            <div class="col-md-4"><label class="form-label" for="entrySection">Section</label><input class="form-control" id="entrySection" name="section" placeholder="All sections"></div>
                            <div class="col-md-4"><label class="form-label" for="entryStatus">Visibility</label><select class="form-select" id="entryStatus" name="status">
                                    <option value="active">Active / visible</option>
                                    <option value="inactive">Inactive / hidden</option>
                                </select></div>
                            <div class="col-12"><label class="form-label" for="entryDescription">Details / description</label><textarea class="form-control" id="entryDescription" name="description" rows="3" placeholder="You can add or update the details later."></textarea></div>
                        </div>
                        <div class="small text-secondary mt-3">Leave department, year, and section blank to show this entry to all students. The calendar saves changes directly to the database.</div>
                    </div>
                    <div class="modal-footer justify-content-between"><button type="button" class="btn btn-outline-danger d-none" id="deleteEntryBtn"><i class="bi bi-trash me-1"></i>Delete entry</button>
                        <div class="ms-auto d-flex gap-2"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary" id="saveEntryBtn"><i class="bi bi-check2 me-1"></i>Save entry</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        const csrfToken = <?= json_encode($csrf) ?>;
        const initialEvents = <?= json_encode($calendarEvents, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
        const modalEl = document.getElementById('entryModal');
        const entryModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        const form = document.getElementById('entryForm');
        let calendar;

        function toast(message, type = 'success') {
            const area = document.getElementById('toastArea');
            area.innerHTML = '';
            const el = document.createElement('div');
            el.className = 'alert alert-' + (type === 'success' ? 'success' : 'danger') + ' alert-dismissible fade show';
            el.setAttribute('role', 'alert');
            el.textContent = message;
            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'btn-close';
            close.setAttribute('data-bs-dismiss', 'alert');
            close.setAttribute('aria-label', 'Close');
            el.appendChild(close);
            area.appendChild(el);
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function setVal(name, value) {
            const el = form.elements.namedItem(name);
            if (el) el.value = value ?? '';
        }

        function updateTypeFields() {
            const isActivity = document.getElementById('entryKind').value === 'activity';
            document.getElementById('dueWrap').style.display = isActivity ? 'block' : 'none';
            if (!isActivity) setVal('due_date', '');
            if (!document.getElementById('entryCategory').value) setVal('category', isActivity ? 'Academic Activity' : 'Academic Event');
        }

        function resetForm(start = '', end = '') {
            form.reset();
            setVal('csrf', csrfToken);
            setVal('ajax', '1');
            setVal('action', 'save');
            setVal('id', '0');
            setVal('kind', 'activity');
            setVal('category', 'Academic Activity');
            setVal('calendar_date', start);
            setVal('due_date', end && end !== start ? end : '');
            document.getElementById('modalTitle').textContent = 'New calendar entry';
            document.getElementById('deleteEntryBtn').classList.add('d-none');
            document.getElementById('saveEntryBtn').innerHTML = '<i class="bi bi-check2 me-1"></i>Save entry';
            document.getElementById('selectedDateLabel').textContent = start ? (end && end !== start ? start + ' — ' + end : start) : 'Choose a date or date range';
            updateTypeFields();
        }

        function dateOnly(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }

        function openNew(start = '', end = '') {
            resetForm(start, end);
            entryModal.show();
            setTimeout(() => document.getElementById('entryTitle').focus(), 250);
        }

        function openEdit(ev) {
            const p = ev.extendedProps;
            resetForm(ev.startStr, p.dueDate || '');
            setVal('id', p.recordId);
            setVal('kind', p.kind);
            setVal('title', ev.title);
            setVal('description', p.description);
            setVal('category', p.category);
            setVal('calendar_date', ev.startStr);
            setVal('due_date', p.dueDate || '');
            setVal('start_time', p.startTime);
            setVal('end_time', p.endTime);
            setVal('location', p.location);
            setVal('department', p.department);
            setVal('year_level', p.yearLevel);
            setVal('section', p.section);
            setVal('status', p.status);
            document.getElementById('modalTitle').textContent = 'Edit calendar entry';
            document.getElementById('selectedDateLabel').textContent = p.dueDate && p.dueDate !== ev.startStr ? ev.startStr + ' — ' + p.dueDate : ev.startStr;
            document.getElementById('deleteEntryBtn').classList.remove('d-none');
            document.getElementById('saveEntryBtn').innerHTML = '<i class="bi bi-check2 me-1"></i>Save changes';
            updateTypeFields();
            entryModal.show();
        }

        function calendarEventFromApi(e) {
            return e;
        }

        function calToday() {
            const d = new Date();
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }

        function calEnd(ev) {
            let end = ev.endStr || ev.end || ev.startStr;
            if (end instanceof Date) end = calDate(end);
            if (!end) return ev.startStr;
            const d = new Date(String(end).slice(0, 10) + 'T00:00:00');
            d.setDate(d.getDate() - 1);
            return calDate(d);
        }

        function calDate(d) {
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }

        function calPretty(v) {
            return v ? new Date(v + 'T00:00:00').toLocaleDateString(undefined, {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }) : '';
        }

        function renderCalList(listId, countId, events, icon, emptyText) {
            const list = document.getElementById(listId);
            document.getElementById(countId).textContent = events.length;
            list.replaceChildren();
            if (!events.length) {
                const empty = document.createElement('div');
                empty.className = 'overview-empty';
                const i = document.createElement('i');
                i.className = 'bi ' + icon;
                const t = document.createElement('div');
                t.textContent = emptyText;
                empty.append(i, t);
                list.append(empty);
                return;
            }
            events.forEach(ev => {
                const p = ev.extendedProps || {};
                const row = document.createElement('div');
                row.className = 'overview-item';
                row.tabIndex = 0;
                row.setAttribute('role', 'button');
                const stripe = document.createElement('span');
                stripe.className = 'overview-color';
                stripe.style.backgroundColor = ev.backgroundColor || '#315eea';
                const main = document.createElement('div');
                main.className = 'overview-item-main';
                const title = document.createElement('div');
                title.className = 'overview-item-title';
                title.textContent = ev.title || 'Untitled entry';
                const meta = document.createElement('div');
                meta.className = 'overview-item-meta';
                const date = document.createElement('span');
                const last = calEnd(ev);
                date.textContent = ev.startStr === last ? calPretty(ev.startStr) : calPretty(ev.startStr) + ' – ' + calPretty(last);
                const kind = document.createElement('span');
                kind.textContent = p.kind === 'event' ? 'General event' : 'Activity';
                meta.append(date, kind);
                if (p.location) {
                    const loc = document.createElement('span');
                    loc.textContent = '· ' + p.location;
                    meta.append(loc);
                }
                main.append(title, meta);
                row.append(stripe, main);
                const open = () => {
                    const ce = calendar.getEventById(ev.id);
                    if (ce) openEdit(ce);
                };
                row.addEventListener('click', open);
                row.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        open();
                    }
                });
                list.append(row);
            });
        }

        function refreshCalOverview() {
            if (!calendar) return;
            const today = calToday();
            const all = calendar.getEvents().map(ev => ({
                id: ev.id,
                title: ev.title,
                startStr: ev.startStr,
                endStr: ev.endStr || ev.startStr,
                backgroundColor: ev.backgroundColor,
                extendedProps: ev.extendedProps || {}
            }));
            const active = all.filter(ev => (ev.extendedProps.status || 'active') === 'active');
            const todayList = active.filter(ev => ev.startStr <= today && calEnd(ev) >= today).sort((a, b) => a.startStr.localeCompare(b.startStr));
            const upcoming = active.filter(ev => ev.startStr > today).sort((a, b) => a.startStr.localeCompare(b.startStr)).slice(0, 8);
            const ended = active.filter(ev => calEnd(ev) < today).sort((a, b) => calEnd(b).localeCompare(calEnd(a))).slice(0, 8);
            renderCalList('todayList', 'todayCount', todayList, 'bi-sun', 'No events scheduled for today.');
            renderCalList('upcomingList', 'upcomingCount', upcoming, 'bi-calendar-plus', 'Nothing upcoming yet.');
            renderCalList('endedList', 'endedCount', ended, 'bi-check2-circle', 'No recently ended entries.');
        }
        document.addEventListener('DOMContentLoaded', () => {
            calendar = new FullCalendar.Calendar(document.getElementById('academicCalendar'), {
                initialView: 'dayGridMonth',
                height: 'auto',
                expandRows: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: 3,
                editable: false,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,listMonth'
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    list: 'List'
                },
                events: initialEvents,
                selectAllow(info) {
                    return info.start < info.end;
                },
                dateClick(info) {
                    openNew(info.dateStr, '');
                },
                select(info) {
                    const end = new Date(info.end);
                    end.setDate(end.getDate() - 1);
                    openNew(info.startStr, dateOnly(end));
                    calendar.unselect();
                },
                eventClick(info) {
                    openEdit(info.event);
                }
            });
            calendar.render();
            refreshCalOverview();
            document.getElementById('newEntryBtn').addEventListener('click', () => openNew(dateOnly(new Date()), ''));
            document.getElementById('entryKind').addEventListener('change', updateTypeFields);
            form.addEventListener('submit', async e => {
                e.preventDefault();
                const save = document.getElementById('saveEntryBtn');
                save.disabled = true;
                save.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving';
                try {
                    const fd = new FormData(form);
                    fd.set('ajax', '1');
                    fd.set('csrf', csrfToken);
                    const res = await fetch(window.location.pathname, {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (!res.ok || !data.ok) throw new Error(data.message || 'Could not save entry.');
                    const existing = calendar.getEventById(data.event.id);
                    if (existing) existing.remove();
                    calendar.addEvent(calendarEventFromApi(data.event));
                    refreshCalOverview();
                    entryModal.hide();
                    toast(data.message || 'Entry saved.');
                } catch (err) {
                    toast(err.message || 'An unexpected error occurred.', 'error');
                } finally {
                    save.disabled = false;
                    save.innerHTML = (Number(form.elements.namedItem('id').value) > 0 ? '<i class="bi bi-check2 me-1"></i>Save changes' : '<i class="bi bi-check2 me-1"></i>Save entry');
                }
            });
            document.getElementById('deleteEntryBtn').addEventListener('click', async () => {
                const id = Number(form.elements.namedItem('id').value);
                const kind = form.elements.namedItem('kind').value;
                if (!id || !confirm('Delete this calendar entry permanently?')) return;
                const btn = document.getElementById('deleteEntryBtn');
                btn.disabled = true;
                try {
                    const fd = new FormData();
                    fd.set('ajax', '1');
                    fd.set('csrf', csrfToken);
                    fd.set('action', 'delete');
                    fd.set('kind', kind);
                    fd.set('id', String(id));
                    const res = await fetch(window.location.pathname, {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (!res.ok || !data.ok) throw new Error(data.message || 'Could not delete entry.');
                    const ev = calendar.getEventById(kind + '-' + id);
                    if (ev) ev.remove();
                    refreshCalOverview();
                    entryModal.hide();
                    toast(data.message || 'Entry deleted.');
                } catch (err) {
                    toast(err.message || 'An unexpected error occurred.', 'error');
                } finally {
                    btn.disabled = false;
                }
            });
        });
    </script>
</body>

</html>