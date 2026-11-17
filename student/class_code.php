<?php
session_cache_limiter('nocache');
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

/* Always fetch fresh dashboard statistics from the database. */
if (!headers_sent()) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
}


/* =========================================================
   AUTHENTICATION CHECK
========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user"]) ||
    !isset($_SESSION["user"]["access"]) ||
    $_SESSION["user"]["access"] !== "student"
) {
    header("Location: ../login.php");
    exit;
}


/* =========================================================
   DATABASE
========================================================= */

require_once "../src/connection.php";



/* =========================================================
   STUDENT CLASS CODE ENROLLMENT
========================================================= */
$studentId = trim($_SESSION["user"]["student_id"] ?? "");

if (empty($_SESSION["csrf_class_enrollment"])) {
    $_SESSION["csrf_class_enrollment"] = bin2hex(random_bytes(32));
}

$classEnrollmentFlash = $_SESSION["class_enrollment_flash"] ?? null;
unset($_SESSION["class_enrollment_flash"]);

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "enroll_class_code") {
    $postedToken = (string)($_POST["csrf_token"] ?? "");
    $classCodeId = filter_var($_POST["class_code_id"] ?? null, FILTER_VALIDATE_INT);

    if ($postedToken === "" || !hash_equals($_SESSION["csrf_class_enrollment"], $postedToken)) {
        $_SESSION["class_enrollment_flash"] = [
            "type" => "danger",
            "message" => "Your session token is invalid. Please refresh the page and try again."
        ];
    } elseif ($studentId === "") {
        $_SESSION["class_enrollment_flash"] = [
            "type" => "danger",
            "message" => "Your student ID is missing from your account. Please contact the administrator."
        ];
    } elseif (!$classCodeId || $classCodeId < 1) {
        $_SESSION["class_enrollment_flash"] = [
            "type" => "danger",
            "message" => "Invalid class code selected."
        ];
    } else {
        $validateClassCode = $mysqli->prepare(
            "SELECT cc.id
             FROM class_codes cc
             INNER JOIN academic_year_semesters ays ON ays.id = cc.academic_year_semester_id
             WHERE cc.id = ? AND cc.status = 'Active' AND ays.status = 'Active'
             LIMIT 1"
        );

        if (!$validateClassCode) {
            $_SESSION["class_enrollment_flash"] = [
                "type" => "danger",
                "message" => "Unable to validate the class code. Please check the class code database setup."
            ];
        } else {
            $validateClassCode->bind_param("i", $classCodeId);
            $validateClassCode->execute();
            $validClass = $validateClassCode->get_result()->fetch_assoc();
            $validateClassCode->close();

            if (!$validClass) {
                $_SESSION["class_enrollment_flash"] = [
                    "type" => "warning",
                    "message" => "This class code is no longer active or its academic period is inactive."
                ];
            } else {
                $enrollStmt = $mysqli->prepare(
                    "INSERT INTO class_code_enrollments (class_code_id, student_id, status)
                     VALUES (?, ?, 'Pending')"
                );

                if (!$enrollStmt) {
                    $_SESSION["class_enrollment_flash"] = [
                        "type" => "danger",
                        "message" => "Unable to enroll. Please import the class_code_enrollments SQL table first."
                    ];
                } else {
                    $enrollStmt->bind_param("is", $classCodeId, $studentId);
                    $enrollOk = $enrollStmt->execute();
                    $duplicateEnrollment = ($enrollStmt->errno === 1062);
                    $enrollStmt->close();

                    if ($enrollOk) {
                        $_SESSION["class_enrollment_flash"] = [
                            "type" => "success",
                            "message" => "You have successfully submitted your enrollment request."
                        ];
                    } elseif ($duplicateEnrollment) {
                        $_SESSION["class_enrollment_flash"] = [
                            "type" => "info",
                            "message" => "You are already enrolled in this class."
                        ];
                    } else {
                        $_SESSION["class_enrollment_flash"] = [
                            "type" => "danger",
                            "message" => "Enrollment failed. Please try again or contact the administrator."
                        ];
                    }
                }
            }
        }
    }

    header("Location: " . $_SERVER["PHP_SELF"]);
    exit;
}

/* Load active class codes under active academic year/semester records. */
$availableClassCodes = [];
$classCodeLoadError = "";

$classCodeQuery = "
    SELECT
        cc.id,
        cc.course_code,
        cc.description,
        cc.instructor_id,
        ays.academic_year,
        ays.semester,
        cce.id AS enrollment_id,
        cce.status AS enrollment_status,
        CASE WHEN cce.id IS NULL THEN 0 ELSE 1 END AS is_enrolled
    FROM class_codes cc
    INNER JOIN academic_year_semesters ays
        ON ays.id = cc.academic_year_semester_id
    LEFT JOIN class_code_enrollments cce
        ON cce.class_code_id = cc.id
       AND cce.student_id = ?
    WHERE cc.status = 'Active'
      AND ays.status = 'Active'
    ORDER BY ays.academic_year DESC,
             FIELD(ays.semester, '1st Semester', '2nd Semester', 'Summer'),
             cc.course_code ASC
";

$classCodeStmt = $mysqli->prepare($classCodeQuery);
if ($classCodeStmt) {
    $classCodeStmt->bind_param("s", $studentId);
    if ($classCodeStmt->execute()) {
        $classCodeResult = $classCodeStmt->get_result();
        while ($classCodeRow = $classCodeResult->fetch_assoc()) {
            $availableClassCodes[] = $classCodeRow;
        }
    } else {
        $classCodeLoadError = "Unable to load active class codes.";
    }
    $classCodeStmt->close();
} else {
    $classCodeLoadError = "The class enrollment table may not be installed yet. Please import the provided SQL setup.";
}

$activeClassCodeCount = count($availableClassCodes);

// Set this to the class destination URL when it is provided. Leave empty for now.
$classRoomUrl = "";

function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

/* Resolve instructor details without assuming a specific account-table name. */
function resolveInstructor(mysqli $db, int $instructorId): array
{
    static $schema = null;
    if ($instructorId < 1) return ["name" => "Instructor", "photo" => ""];

    if ($schema === null) {
        $schema = null;
        $tables = ["instructors", "users", "user_accounts", "accounts", "faculty", "employees"];
        $idCandidates = ["id", "user_id", "account_id", "instructor_id"];
        $nameCandidates = ["full_name", "name", "instructor_name", "display_name"];
        $photoCandidates = ["profile_picture", "profile_photo", "profile_image", "profile_img", "profile_pic", "avatar", "avatar_url", "photo", "image", "image_path", "user_image"];

        foreach ($tables as $table) {
            $safeTable = $db->real_escape_string($table);
            $exists = $db->query("SHOW TABLES LIKE '{$safeTable}'");
            if (!$exists || $exists->num_rows < 1) {
                if ($exists) $exists->free();
                continue;
            }
            $exists->free();
            $columnsResult = $db->query("SHOW COLUMNS FROM `{$table}`");
            if (!$columnsResult) continue;
            $columns = [];
            while ($column = $columnsResult->fetch_assoc()) $columns[] = (string)$column["Field"];
            $columnsResult->free();
            $idCol = null;
            foreach ($idCandidates as $candidate) if (in_array($candidate, $columns, true)) {
                $idCol = $candidate;
                break;
            }
            if (!$idCol) continue;
            $nameCol = null;
            foreach ($nameCandidates as $candidate) if (in_array($candidate, $columns, true)) {
                $nameCol = $candidate;
                break;
            }
            $photoCol = null;
            foreach ($photoCandidates as $candidate) if (in_array($candidate, $columns, true)) {
                $photoCol = $candidate;
                break;
            }
            $firstCol = in_array("first_name", $columns, true) ? "first_name" : null;
            $lastCol = in_array("last_name", $columns, true) ? "last_name" : null;
            if ($nameCol || $firstCol || $lastCol || $photoCol) {
                $schema = ["table" => $table, "id" => $idCol, "name" => $nameCol, "first" => $firstCol, "last" => $lastCol, "photo" => $photoCol];
                break;
            }
        }
    }

    if (!$schema) return ["name" => "Instructor", "photo" => ""];
    $table = "`" . $schema["table"] . "`";
    $idCol = "`" . $schema["id"] . "`";
    $select = [];
    if ($schema["name"]) $select[] = "`{$schema['name']}` AS instructor_name";
    if ($schema["first"]) $select[] = "`{$schema['first']}` AS first_name";
    if ($schema["last"]) $select[] = "`{$schema['last']}` AS last_name";
    if ($schema["photo"]) $select[] = "`{$schema['photo']}` AS instructor_photo";
    if (!$select) return ["name" => "Instructor", "photo" => ""];

    $stmt = $db->prepare("SELECT " . implode(", ", $select) . " FROM {$table} WHERE {$idCol} = ? LIMIT 1");
    if (!$stmt) return ["name" => "Instructor", "photo" => ""];
    $stmt->bind_param("i", $instructorId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    if ($result) $result->free();
    $stmt->close();
    if (!$row) return ["name" => "Instructor", "photo" => ""];

    $name = trim((string)($row["instructor_name"] ?? ""));
    if ($name === "") $name = trim((string)($row["first_name"] ?? "") . " " . (string)($row["last_name"] ?? ""));
    if ($name === "") $name = "Instructor";
    $photo = trim(str_replace("\\", "/", (string)($row["instructor_photo"] ?? "")));
    if ($photo !== "" && !preg_match('~^(?:https?:)?//|^/~i', $photo)) {
        $photo = preg_replace('~^(?:\./)+~', "", $photo);
        $photo = "../" . ltrim($photo, "/");
    }
    if (preg_match('~^(?:javascript:|data:)~i', $photo)) $photo = "";
    return ["name" => $name, "photo" => $photo];
}


/* Load only accepted classmates for a specific class code. The student profile
   table/columns are detected so this works with common student account schemas. */
function resolveClassmates(mysqli $db, int $classCodeId): array
{
    static $schema = false;
    if ($classCodeId < 1) return [];

    if ($schema === false) {
        $schema = null;
        $tables = ["students", "student", "student_profiles", "users", "user_accounts", "accounts"];
        $idCandidates = ["student_id", "id", "user_id", "account_id"];
        $nameCandidates = ["full_name", "name", "student_name", "display_name"];
        $photoCandidates = ["profile_picture", "profile_photo", "profile_image", "profile_img", "profile_pic", "avatar", "avatar_url", "photo", "image", "image_path", "user_image"];

        foreach ($tables as $tableName) {
            $safeTable = $db->real_escape_string($tableName);
            $exists = $db->query("SHOW TABLES LIKE '{$safeTable}'");
            if (!$exists || $exists->num_rows < 1) {
                if ($exists) $exists->free();
                continue;
            }
            $exists->free();
            $columnsResult = $db->query("SHOW COLUMNS FROM `{$tableName}`");
            if (!$columnsResult) continue;
            $columns = [];
            while ($column = $columnsResult->fetch_assoc()) $columns[] = (string)$column["Field"];
            $columnsResult->free();

            $idCol = null;
            foreach ($idCandidates as $candidate) {
                if (in_array($candidate, $columns, true)) {
                    $idCol = $candidate;
                    break;
                }
            }
            if (!$idCol) continue;

            $nameCol = null;
            foreach ($nameCandidates as $candidate) {
                if (in_array($candidate, $columns, true)) {
                    $nameCol = $candidate;
                    break;
                }
            }
            $firstCol = in_array("first_name", $columns, true) ? "first_name" : null;
            $lastCol = in_array("last_name", $columns, true) ? "last_name" : null;
            $photoCol = null;
            foreach ($photoCandidates as $candidate) {
                if (in_array($candidate, $columns, true)) {
                    $photoCol = $candidate;
                    break;
                }
            }

            if ($nameCol || $firstCol || $lastCol || $photoCol) {
                $schema = [
                    "table" => $tableName,
                    "id" => $idCol,
                    "name" => $nameCol,
                    "first" => $firstCol,
                    "last" => $lastCol,
                    "photo" => $photoCol
                ];
                break;
            }
        }
    }

    $nameExpr = "CAST(cce.student_id AS CHAR)";
    $photoExpr = "''";
    $join = "";
    if ($schema) {
        $table = "`" . $schema["table"] . "`";
        $idCol = "`" . $schema["id"] . "`";
        $join = " LEFT JOIN {$table} sp ON CAST(sp.{$idCol} AS CHAR) = CAST(cce.student_id AS CHAR) ";
        if ($schema["name"]) {
            $nameCol = "sp.`" . $schema["name"] . "`";
            $nameExpr = "COALESCE(NULLIF(TRIM({$nameCol}), ''), CAST(cce.student_id AS CHAR))";
        } elseif ($schema["first"] || $schema["last"]) {
            $parts = [];
            if ($schema["first"]) $parts[] = "NULLIF(TRIM(sp.`" . $schema["first"] . "`), '')";
            if ($schema["last"]) $parts[] = "NULLIF(TRIM(sp.`" . $schema["last"] . "`), '')";
            $nameExpr = "COALESCE(NULLIF(TRIM(CONCAT_WS(' ', " . implode(", ", $parts) . ")), ''), CAST(cce.student_id AS CHAR))";
        }
        if ($schema["photo"]) $photoExpr = "sp.`" . $schema["photo"] . "`";
    }

    $sql = "SELECT cce.student_id, {$nameExpr} AS classmate_name, {$photoExpr} AS classmate_photo
            FROM class_code_enrollments cce {$join}
            WHERE cce.class_code_id = ? AND cce.status = 'Accepted'
            ORDER BY classmate_name ASC";
    $stmt = $db->prepare($sql);
    if (!$stmt) return [];
    $stmt->bind_param("i", $classCodeId);
    if (!$stmt->execute()) {
        $stmt->close();
        return [];
    }
    $result = $stmt->get_result();
    $classmates = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            /* Use the same central profile-photo URL as the supplied Classmates page. */
            $photoValue = trim(str_replace("\\", "/", (string)($row["classmate_photo"] ?? "")));
            $photoName = basename($photoValue);
            $photo = ($photoName !== "" && $photoName !== "." && $photoName !== "..")
                ? "https://vertigation.com/shared/uploads/profile_photos/" . rawurlencode($photoName)
                : "";
            $classmates[] = [
                "name" => trim((string)($row["classmate_name"] ?? "")) ?: (string)($row["student_id"] ?? "Student"),
                "student_id" => (string)($row["student_id"] ?? ""),
                "photo" => $photo
            ];
        }
        $result->free();
    }
    $stmt->close();
    return $classmates;
}

include "globals/head.php";
?>

<body>
    <?php include "globals/sidebar.php"; ?>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <?php include "globals/topbar.php"; ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="dashboard-heading">
                <div>
                    <h2 class="dashboard-heading-title">My Class Codes</h2>
                    <p class="dashboard-heading-subtitle">Browse active classes and submit your enrollment request.</p>
                </div>
            </div>

            <section class="student-class-codes-card mb-4" aria-labelledby="studentClassCodesTitle">
                <div class="student-class-codes-header">
                    <div class="student-class-codes-title-row">
                        <span class="student-class-codes-icon"><i class="bi bi-journal-bookmark-fill"></i></span>
                        <div>
                            <h5 id="studentClassCodesTitle">Available Class Codes</h5>
                            <p>Join a class using a code assigned to the active academic year and semester.</p>
                        </div>
                    </div>
                    <div class="student-class-codes-count">
                        <strong><?= $activeClassCodeCount ?></strong>
                        <span>Available</span>
                    </div>
                </div>

                <?php if ($classEnrollmentFlash): ?>
                    <div class="student-class-alert alert-<?= e($classEnrollmentFlash["type"]) ?>" role="alert">
                        <?= e($classEnrollmentFlash["message"]) ?>
                    </div>
                <?php endif; ?>

                <?php if ($classCodeLoadError !== ""): ?>
                    <div class="student-class-empty">
                        <i class="bi bi-exclamation-circle"></i>
                        <strong>Class codes are temporarily unavailable</strong>
                        <span><?= e($classCodeLoadError) ?></span>
                    </div>
                <?php elseif (!empty($availableClassCodes)): ?>
                    <div class="student-class-codes-grid">
                        <?php foreach ($availableClassCodes as $classCode): ?>
                            <?php $instructorInfo = resolveInstructor($mysqli, (int)($classCode["instructor_id"] ?? 0)); ?>
                            <article class="student-class-code-item">
                                <div class="student-class-code-top">
                                    <span class="student-class-code-label">COURSE CODE</span>
                                    <?php if ((int)$classCode["is_enrolled"] === 1): ?>
                                        <?php
                                        $studentEnrollmentStatus = (string)($classCode["enrollment_status"] ?? "Pending");
                                        $studentEnrollmentStatusClass = strtolower(str_replace(" ", "-", $studentEnrollmentStatus));
                                        ?>
                                        <span class="student-enrollment-status status-<?= e($studentEnrollmentStatusClass) ?>">
                                            <i class="bi bi-circle-fill"></i> <?= e($studentEnrollmentStatus) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="student-class-available"><i class="bi bi-circle-fill"></i> Available</span>
                                    <?php endif; ?>
                                </div>

                                <h6><?= e($classCode["course_code"]) ?></h6>
                                <p class="student-class-description"><?= e($classCode["description"]) ?></p>

                                <div class="student-class-period">
                                    <i class="bi bi-calendar3"></i>
                                    <span><?= e($classCode["academic_year"]) ?> · <?= e($classCode["semester"]) ?></span>
                                </div>

                                <div class="student-class-instructor">
                                    <?php if ($instructorInfo["photo"] !== ""): ?>
                                        <img src="<?= e($instructorInfo["photo"]) ?>" alt="<?= e($instructorInfo["name"]) ?>" class="student-instructor-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span class="student-instructor-avatar student-instructor-fallback" style="display:none"><i class="bi bi-person-fill"></i></span>
                                    <?php else: ?>
                                        <span class="student-instructor-avatar student-instructor-fallback"><i class="bi bi-person-fill"></i></span>
                                    <?php endif; ?>
                                    <div class="student-instructor-details">
                                        <small>INSTRUCTOR</small>
                                        <strong><?= e($instructorInfo["name"]) ?></strong>
                                    </div>
                                    <button type="button" class="student-instructor-notification" title="Instructor notifications (coming soon)" aria-label="Instructor notifications coming soon" disabled>
                                        <i class="bi bi-bell"></i>
                                        <span class="notification-dot"></span>
                                    </button>
                                </div>

                                <?php if ((int)$classCode["is_enrolled"] === 1): ?>
                                    <?php
                                    $studentEnrollmentStatus = (string)($classCode["enrollment_status"] ?? "Pending");
                                    $studentStatusMessages = [
                                        "Pending" => "Your enrollment is waiting for instructor review.",
                                        "Accepted" => "Your enrollment has been accepted.",
                                        "Denied" => "Your enrollment was denied. Contact your instructor for assistance.",
                                        "On Hold" => "Your enrollment is on hold. Please wait for an update from your instructor."
                                    ];
                                    $studentStatusIcons = [
                                        "Pending" => "bi-hourglass-split",
                                        "Accepted" => "bi-check-circle-fill",
                                        "Denied" => "bi-x-circle-fill",
                                        "On Hold" => "bi-pause-circle-fill"
                                    ];
                                    ?>
                                    <div class="student-enrollment-result status-<?= e(strtolower(str_replace(" ", "-", $studentEnrollmentStatus))) ?>">
                                        <strong><i class="bi <?= e($studentStatusIcons[$studentEnrollmentStatus] ?? "bi-info-circle") ?>"></i> <?= e($studentEnrollmentStatus) ?></strong>
                                        <span><?= e($studentStatusMessages[$studentEnrollmentStatus] ?? "Your enrollment status is being processed.") ?></span>
                                    </div>
                                    <?php if ($studentEnrollmentStatus === "Accepted"): ?>
                                        <?php $classmates = resolveClassmates($mysqli, (int)$classCode["id"]); ?>
                                        <button type="button" class="student-classmates-btn" data-classmates-open="classmatesModal<?= (int)$classCode["id"] ?>">
                                            <i class="bi bi-people-fill"></i> Classmates
                                            <span class="student-classmates-count"><?= count($classmates) ?></span>
                                        </button>

                                        <div class="classmates-modal" id="classmatesModal<?= (int)$classCode["id"] ?>" aria-hidden="true">
                                            <div class="classmates-modal-backdrop" data-classmates-close></div>
                                            <section class="classmates-modal-panel" role="dialog" aria-modal="true" aria-labelledby="classmatesTitle<?= (int)$classCode["id"] ?>">
                                                <header class="classmates-modal-header">
                                                    <div>
                                                        <span class="classmates-modal-eyebrow">YOUR CLASS</span>
                                                        <h3 id="classmatesTitle<?= (int)$classCode["id"] ?>">Classmates</h3>
                                                        <p><?= e($classCode["course_code"]) ?> · <?= count($classmates) ?> accepted student<?= count($classmates) === 1 ? "" : "s" ?></p>
                                                    </div>
                                                    <button type="button" class="classmates-modal-close" data-classmates-close aria-label="Close classmates"><i class="bi bi-x-lg"></i></button>
                                                </header>
                                                <div class="classmates-modal-body">
                                                    <?php if (!empty($classmates)): ?>
                                                        <div class="classmates-grid">
                                                            <?php foreach ($classmates as $classmate): ?>
                                                                <div class="classmate-card">
                                                                    <?php if ($classmate["photo"] !== ""): ?>
                                                                        <img class="classmate-avatar" src="<?= e($classmate["photo"]) ?>" alt="<?= e($classmate["name"]) ?>" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                        <span class="classmate-avatar classmate-avatar-fallback" style="display:none"><i class="bi bi-person-fill"></i></span>
                                                                    <?php else: ?>
                                                                        <span class="classmate-avatar classmate-avatar-fallback"><i class="bi bi-person-fill"></i></span>
                                                                    <?php endif; ?>
                                                                    <strong><?= e($classmate["name"]) ?></strong>
                                                                    <small><?= e($classmate["student_id"]) ?></small>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="classmates-empty">
                                                            <i class="bi bi-people"></i>
                                                            <strong>No accepted classmates yet</strong>
                                                            <span>Accepted students will appear here when their enrollment is approved.</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </section>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <form method="POST" class="student-class-enroll-form">
                                        <input type="hidden" name="action" value="enroll_class_code">
                                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_class_enrollment"]) ?>">
                                        <input type="hidden" name="class_code_id" value="<?= (int)$classCode["id"] ?>">
                                        <button type="submit" class="student-class-enroll-btn">
                                            <i class="bi bi-plus-circle"></i> Enroll in Class
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="student-class-empty">
                        <i class="bi bi-journal-x"></i>
                        <strong>No active class codes available</strong>
                        <span>There are currently no active class codes for an active academic year and semester.</span>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php require_once "./globals/scripts.php"; ?>

    <style>
        .dashboard-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .dashboard-heading-title {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--academic-blue-dark);
        }

        .dashboard-heading-subtitle {
            margin: 6px 0 0;
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* =========================================================
           STUDENT CLASS CODE ENROLLMENT
        ========================================================= */
        .student-class-codes-card {
            background: var(--activity-card-bg);
            border: 1px solid var(--activity-border);
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 4px 12px var(--shadow-color);
        }

        .student-class-codes-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .student-class-codes-title-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .student-class-codes-icon {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            color: var(--academic-blue-dark);
            background: var(--activity-progress-bg, #edf3ff);
            font-size: 19px;
        }

        .student-class-codes-header h5 {
            margin: 0;
            color: var(--academic-blue-dark);
            font-size: 16px;
            font-weight: 750;
        }

        .student-class-codes-header p {
            margin: 4px 0 0;
            color: var(--text-secondary);
            font-size: 12px;
        }

        .student-class-codes-count {
            min-width: 78px;
            text-align: center;
            padding: 8px 12px;
            border: 1px solid var(--activity-border);
            border-radius: 12px;
        }

        .student-class-codes-count strong {
            display: block;
            color: var(--academic-blue-dark);
            font-size: 20px;
            line-height: 1.1;
        }

        .student-class-codes-count span {
            color: var(--text-secondary);
            font-size: 10px;
            font-weight: 650;
        }

        .student-class-codes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 14px;
        }

        .student-class-code-item {
            min-width: 0;
            padding: 16px;
            border: 1px solid var(--activity-border);
            border-radius: 13px;
            background: var(--activity-card-bg);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .student-class-code-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 18px var(--shadow-color);
        }

        .student-class-code-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .student-class-code-label {
            color: var(--text-secondary);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .7px;
        }

        .student-class-enrolled,
        .student-class-available {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            font-size: 10px;
            font-weight: 750;
        }

        .student-class-enrolled {
            color: #16834b;
        }

        .student-class-available {
            color: #b7791f;
        }

        .student-class-code-item h6 {
            margin: 0 0 5px;
            color: var(--academic-blue-dark);
            font-size: 19px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .student-class-description {
            min-height: 34px;
            margin: 0 0 13px;
            color: var(--text-secondary);
            font-size: 12px;
            line-height: 1.45;
        }

        .student-class-period {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 14px;
            color: var(--text-secondary);
            font-size: 11px;
        }

        .student-class-period i {
            color: var(--academic-blue-dark);
        }

        .student-class-enroll-form {
            margin: 0;
        }

        .student-class-enroll-btn {
            width: 100%;
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            border: 0;
            border-radius: 9px;
            background: var(--academic-blue-dark);
            color: #fff;
            font-size: 12px;
            font-weight: 750;
            cursor: pointer;
            transition: opacity .18s ease, transform .18s ease;
        }

        .student-class-enroll-btn:hover:not(:disabled) {
            opacity: .9;
            transform: translateY(-1px);
        }

        .student-class-enroll-btn.is-enrolled,
        .student-class-enroll-btn:disabled {
            background: var(--activity-progress-bg, #edf3ff);
            color: var(--text-secondary);
            cursor: default;
            opacity: 1;
        }

        .student-class-alert {
            padding: 10px 13px;
            margin: 0 0 15px;
            border-radius: 9px;
            font-size: 12px;
        }

        .student-class-alert.alert-success {
            color: #176b3a;
            background: #e8f7ee;
        }

        .student-class-alert.alert-danger {
            color: #a52834;
            background: #fdecee;
        }

        .student-class-alert.alert-warning {
            color: #8a5b12;
            background: #fff5df;
        }

        .student-class-alert.alert-info {
            color: #155b83;
            background: #e8f4fb;
        }

        .student-class-empty {
            min-height: 145px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 22px;
            text-align: center;
            border: 1px dashed var(--activity-border);
            border-radius: 12px;
            color: var(--text-secondary);
        }

        .student-class-empty>i {
            color: var(--academic-blue-dark);
            font-size: 25px;
            margin-bottom: 2px;
        }

        .student-class-empty strong {
            color: var(--academic-blue-dark);
            font-size: 13px;
        }

        .student-class-empty span {
            font-size: 11px;
        }


        .student-enrollment-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            font-size: 10px;
            font-weight: 750
        }

        .student-enrollment-status.status-pending {
            color: #946200
        }

        .student-enrollment-status.status-accepted {
            color: #187443
        }

        .student-enrollment-status.status-denied {
            color: #a52b36
        }

        .student-enrollment-status.status-on-hold {
            color: #7753a6
        }

        .student-enrollment-result {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 9px 10px;
            border-radius: 9px;
            font-size: 11px
        }

        .student-enrollment-result strong {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px
        }

        .student-enrollment-result span {
            line-height: 1.4
        }

        .student-enrollment-result.status-pending {
            color: #805b0a;
            background: #fff5dc
        }

        .student-enrollment-result.status-accepted {
            color: #176b3a;
            background: #e8f7ee
        }

        .student-enrollment-result.status-denied {
            color: #a52834;
            background: #fdecee
        }

        .student-enrollment-result.status-on-hold {
            color: #684397;
            background: #f1eafe
        }

        @media (max-width: 575px) {
            .student-class-codes-card {
                padding: 15px;
            }

            .student-class-codes-header {
                align-items: flex-start;
            }

            .student-class-codes-header p {
                max-width: 230px;
            }

            .student-class-codes-grid {
                grid-template-columns: 1fr;
            }
        }



        .student-class-instructor {
            display: flex;
            align-items: center;
            gap: 11px;
            margin: 17px 0 15px;
            padding: 12px;
            border: 1px solid var(--activity-border);
            border-radius: 13px;
            background: var(--activity-progress-bg, var(--activity-card-bg));
        }

        .student-instructor-avatar {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--activity-card-bg);
            box-shadow: 0 1px 5px var(--shadow-color);
        }

        .student-instructor-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--activity-progress-bg, var(--activity-card-bg));
            color: var(--academic-blue-dark);
            font-size: 19px;
        }

        .student-instructor-details {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .student-instructor-details small {
            color: var(--text-secondary);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .student-instructor-details strong {
            color: var(--academic-blue-dark);
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .student-instructor-notification {
            position: relative;
            margin-left: auto;
            width: 35px;
            height: 35px;
            flex: 0 0 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--activity-border);
            border-radius: 10px;
            background: var(--activity-card-bg);
            color: var(--academic-blue-dark);
            font-size: 16px;
            opacity: .85;
            cursor: not-allowed;
        }

        .notification-dot {
            position: absolute;
            width: 7px;
            height: 7px;
            top: 6px;
            right: 6px;
            border-radius: 50%;
            background: #94a3b8;
        }

        .student-classmates-btn {
            width: 100%;
            min-height: 42px;
            margin-top: 12px;
            padding: 10px 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid var(--academic-blue-dark);
            border-radius: 10px;
            background: var(--academic-blue-dark);
            color: #fff;
            font-size: 12px;
            font-weight: 750;
            cursor: pointer;
            transition: transform .18s ease, opacity .18s ease;
        }

        .student-classmates-btn:hover {
            transform: translateY(-1px);
            opacity: .92;
        }

        .student-classmates-count {
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            background: rgba(255, 255, 255, .2);
            font-size: 10px;
        }

        /* Responsive classmates dialog: fits phones, tablets, laptops and desktops. */
        .classmates-modal {
            position: fixed;
            inset: 0;
            z-index: 12000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: max(12px, env(safe-area-inset-top)) max(12px, env(safe-area-inset-right)) max(12px, env(safe-area-inset-bottom)) max(12px, env(safe-area-inset-left));
            overflow: hidden;
        }

        .classmates-modal.is-open {
            display: flex;
        }

        .classmates-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(10, 18, 35, .66);
            -webkit-backdrop-filter: blur(5px);
            backdrop-filter: blur(5px);
        }

        .classmates-modal-panel {
            position: relative;
            z-index: 1;
            width: min(760px, calc(100vw - 24px));
            max-width: 760px;
            max-height: min(820px, calc(100vh - 24px));
            max-height: min(820px, calc(100dvh - 24px));
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
            border: 1px solid var(--activity-border);
            border-radius: clamp(12px, 2vw, 20px);
            background: var(--activity-card-bg);
            box-shadow: 0 24px 70px rgba(0, 0, 0, .28);
            animation: classmatesModalIn .2s ease-out;
        }

        @keyframes classmatesModalIn {
            from {
                opacity: 0;
                transform: translateY(10px) scale(.985)
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }

        .classmates-modal-header {
            flex: 0 0 auto;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            padding: clamp(15px, 2.5vw, 22px);
            border-bottom: 1px solid var(--activity-border);
        }

        .classmates-modal-header>div {
            min-width: 0;
        }

        .classmates-modal-eyebrow {
            color: var(--text-secondary);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .12em;
        }

        .classmates-modal-header h3 {
            margin: 4px 0 3px;
            color: var(--academic-blue-dark);
            font-size: clamp(18px, 2.5vw, 22px);
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .classmates-modal-header p {
            margin: 0;
            color: var(--text-secondary);
            font-size: clamp(11px, 1.7vw, 12px);
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .classmates-modal-close {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            display: grid;
            place-items: center;
            border: 1px solid var(--activity-border);
            border-radius: 10px;
            background: var(--activity-progress-bg, var(--activity-card-bg));
            color: var(--academic-blue-dark);
            cursor: pointer;
            touch-action: manipulation;
        }

        .classmates-modal-close:focus-visible,
        .student-classmates-btn:focus-visible {
            outline: 3px solid var(--academic-blue);
            outline-offset: 3px;
        }

        .classmates-modal-body {
            min-height: 0;
            overflow: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            padding: clamp(14px, 2.5vw, 22px);
        }

        .classmates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(145px, 100%), 1fr));
            gap: clamp(9px, 1.8vw, 14px);
        }

        .classmate-card {
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 7px;
            padding: clamp(11px, 1.8vw, 16px) 9px 13px;
            border: 1px solid var(--activity-border);
            border-radius: 13px;
            background: var(--activity-progress-bg, var(--activity-card-bg));
            text-align: center;
        }

        .classmate-avatar {
            width: clamp(54px, 8vw, 72px);
            height: clamp(54px, 8vw, 72px);
            flex: 0 0 auto;
            border: 3px solid var(--activity-card-bg);
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 2px 8px var(--shadow-color);
        }

        .classmate-avatar-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--activity-card-bg);
            color: var(--academic-blue-dark);
            font-size: 28px;
        }

        .classmate-card strong {
            max-width: 100%;
            color: var(--academic-blue-dark);
            font-size: 12px;
            line-height: 1.4;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .classmate-card small {
            max-width: 100%;
            color: var(--text-secondary);
            font-size: 10px;
            overflow-wrap: anywhere;
            word-break: break-word;
        }

        .classmates-empty {
            min-height: clamp(150px, 25vh, 210px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 20px;
            color: var(--text-secondary);
            text-align: center;
        }

        .classmates-empty i {
            color: var(--academic-blue-dark);
            font-size: 32px;
        }

        .classmates-empty strong {
            color: var(--academic-blue-dark);
            font-size: 14px;
        }

        .classmates-empty span {
            max-width: 320px;
            font-size: 12px;
            line-height: 1.5;
        }

        body.classmates-modal-open {
            overflow: hidden;
        }

        @media (max-width: 480px) {
            .classmates-modal {
                padding: max(8px, env(safe-area-inset-top)) max(8px, env(safe-area-inset-right)) max(8px, env(safe-area-inset-bottom)) max(8px, env(safe-area-inset-left));
            }

            .classmates-modal-panel {
                width: 100%;
                max-height: calc(100vh - 16px);
                max-height: calc(100dvh - 16px);
                border-radius: 14px;
            }

            .classmates-modal-header {
                padding: 14px;
            }

            .classmates-modal-body {
                padding: 12px;
            }

            .classmates-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 8px;
            }

            .classmate-card {
                padding: 11px 6px;
            }
        }

        @media (max-width: 340px) {
            .classmates-modal-header {
                gap: 8px;
            }

            .classmates-modal-close {
                width: 34px;
                height: 34px;
                flex-basis: 34px;
            }

            .classmates-modal-body {
                padding: 9px;
            }

            .classmates-grid {
                gap: 7px;
            }

            .classmate-card strong {
                font-size: 11px;
            }

            .classmate-card small {
                font-size: 9px;
            }
        }

        @media (max-height: 520px) and (orientation: landscape) {
            .classmates-modal {
                align-items: flex-start;
            }

            .classmates-modal-panel {
                margin-block: 4px;
                max-height: calc(100vh - 8px);
                max-height: calc(100dvh - 8px);
            }

            .classmates-modal-header {
                padding: 10px 14px;
            }

            .classmates-modal-body {
                padding: 10px 14px;
            }

            .classmates-grid {
                grid-template-columns: repeat(auto-fill, minmax(125px, 1fr));
            }

            .classmate-avatar {
                width: 48px;
                height: 48px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .classmates-modal-panel {
                animation: none;
            }
        }
    </style>

    <script>
        (() => {
            let activeModal = null;
            let previousFocus = null;

            function closeClassmatesModal(modal) {
                if (!modal) return;
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('classmates-modal-open');
                if (activeModal === modal) activeModal = null;
                if (previousFocus && typeof previousFocus.focus === 'function') previousFocus.focus();
            }

            document.addEventListener('click', (event) => {
                const openButton = event.target.closest('[data-classmates-open]');
                if (openButton) {
                    const modal = document.getElementById(openButton.getAttribute('data-classmates-open'));
                    if (!modal) return;
                    previousFocus = openButton;
                    activeModal = modal;
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('classmates-modal-open');
                    const closeButton = modal.querySelector('[data-classmates-close]');
                    if (closeButton) closeButton.focus();
                    return;
                }

                const closeTarget = event.target.closest('[data-classmates-close]');
                if (closeTarget) {
                    const modal = closeTarget.closest('.classmates-modal');
                    closeClassmatesModal(modal);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (!activeModal) return;
                if (event.key === 'Escape') {
                    closeClassmatesModal(activeModal);
                    return;
                }
                if (event.key === 'Tab') {
                    const focusable = activeModal.querySelectorAll('button:not([disabled]), a[href], input:not([disabled]), [tabindex]:not([tabindex="-1"])');
                    if (!focusable.length) return;
                    const first = focusable[0],
                        last = focusable[focusable.length - 1];
                    if (event.shiftKey && document.activeElement === first) {
                        event.preventDefault();
                        last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        event.preventDefault();
                        first.focus();
                    }
                }
            });
        })();
    </script>

</body>

</html>