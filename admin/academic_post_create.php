<?php

/* =========================================================
   ACADEMIC POST CREATE
   ETS-Async Learning Portal
   ========================================================= */


/* =========================================================
   ADMIN AUTHENTICATION
========================================================== */

include './globals/checks.php';


/* =========================================================
   SESSION USER
========================================================== */

$user = $_SESSION["user"];

$adminId = $user["id"] ?? null;


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   PAGE VARIABLES
========================================================== */

$error   = "";
$success = "";


/* =========================================================
   FORM DEFAULT VALUES
========================================================== */

$title       = "";
$description = "";
$url         = "";
$subject     = "";
$department  = "";
$icon        = "bi-file-earmark-text";
$iconColor   = "blue";
$sortOrder   = 0;
$status      = 1;


/* =========================================================
   LOAD DEPARTMENTS
   ---------------------------------------------------------
   Departments are taken from student accounts so that the
   department selection remains consistent with the portal.
========================================================== */

$departments = [];

$departmentQuery = "
    SELECT DISTINCT department
    FROM accounts
    WHERE access = 'student'
      AND department IS NOT NULL
      AND TRIM(department) <> ''
    ORDER BY department ASC
";

$departmentResult = $mysqli->query($departmentQuery);

if ($departmentResult) {

    while ($row = $departmentResult->fetch_assoc()) {

        $departments[] = $row["department"];
    }
}


/* =========================================================
   LOAD EXISTING SUBJECTS
   ---------------------------------------------------------
   Existing subjects are displayed through a datalist.
   The administrator may still enter a completely new subject.
========================================================== */

$subjects = [];

$subjectQuery = "
    SELECT DISTINCT subject
    FROM academic_posts
    WHERE subject IS NOT NULL
      AND TRIM(subject) <> ''
    ORDER BY subject ASC
";

$subjectResult = $mysqli->query($subjectQuery);

if ($subjectResult) {

    while ($row = $subjectResult->fetch_assoc()) {

        $subjects[] = $row["subject"];
    }
}


/* =========================================================
   FORM SUBMISSION
========================================================== */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* =====================================================
       GET FORM VALUES
    ====================================================== */

    $title = trim($_POST["title"] ?? "");

    $description = trim($_POST["description"] ?? "");

    $url = trim($_POST["url"] ?? "");

    $subject = trim($_POST["subject"] ?? "");

    $department = trim($_POST["department"] ?? "");

    $icon = trim($_POST["icon"] ?? "bi-file-earmark-text");

    $iconColor = trim($_POST["icon_color"] ?? "blue");

    $sortOrder = (int)($_POST["sort_order"] ?? 0);

    $status = isset($_POST["status"]) ? 1 : 0;


    /* =====================================================
       VALIDATE TITLE
    ====================================================== */

    if ($title === "") {

        $error = "Please enter the academic post title.";
    }


    /* =====================================================
       VALIDATE PHP PAGE FILENAME
       -----------------------------------------------------
       The URL field stores a PHP filename rather than a
       complete web URL.

       Valid examples:
           z_transform.php
           common_z_transform_pairs.php
           difference_equation_solver.php

       Invalid examples:
           https://example.com/page.php
           ../page.php
           /page.php
           page.html
           page.php?id=1
    ====================================================== */ elseif ($url === "") {

        $error = "Please enter the PHP page filename.";
    } elseif (
        !preg_match(
            '/^[a-zA-Z0-9_-]+\.php$/',
            $url
        )
    ) {

        $error =
            "Please enter a valid PHP filename, for example: z_transform.php";
    }


    /* =====================================================
       VALIDATE ICON
    ====================================================== */ elseif ($icon === "") {

        $error = "Please specify a Bootstrap Icon class.";
    }


    /* =====================================================
       VALIDATE ICON COLOR
    ====================================================== */ elseif (
        !in_array(
            $iconColor,
            [
                "blue",
                "green",
                "orange",
                "red",
                "purple",
                "cyan",
                "yellow",
                "gray"
            ],
            true
        )
    ) {

        $error = "Invalid icon color selected.";
    }


    /* =====================================================
       INSERT RECORD
    ====================================================== */ else {

        $sql = "
            INSERT INTO academic_posts (
                title,
                description,
                url,
                subject,
                department,
                icon,
                icon_color,
                sort_order,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $mysqli->prepare($sql);


        /* =================================================
           CHECK PREPARED STATEMENT
        ================================================== */

        if (!$stmt) {

            $error =
                "Unable to prepare the database query. " .
                $mysqli->error;
        } else {

            /* =============================================
               BIND VALUES
            ============================================== */

            $stmt->bind_param(
                "sssssssii",
                $title,
                $description,
                $url,
                $subject,
                $department,
                $icon,
                $iconColor,
                $sortOrder,
                $status
            );


            /* =============================================
               EXECUTE
            ============================================== */

            if ($stmt->execute()) {

                $success =
                    "Academic post created successfully.";


                /* =========================================
                   RESET FORM
                ========================================== */

                $title       = "";
                $description = "";
                $url         = "";
                $subject     = "";
                $department  = "";
                $icon        = "bi-file-earmark-text";
                $iconColor   = "blue";
                $sortOrder   = 0;
                $status      = 1;


                /* =========================================
                   REFRESH SUBJECT LIST
                   ------------------------------------------------
                   This allows a newly created subject to appear
                   in the datalist immediately.
                ========================================== */

                $subjects = [];

                $subjectQuery = "
                    SELECT DISTINCT subject
                    FROM academic_posts
                    WHERE subject IS NOT NULL
                      AND TRIM(subject) <> ''
                    ORDER BY subject ASC
                ";

                $subjectResult = $mysqli->query($subjectQuery);

                if ($subjectResult) {

                    while ($row = $subjectResult->fetch_assoc()) {

                        $subjects[] = $row["subject"];
                    }
                }
            } else {

                $error =
                    "Unable to create the academic post. " .
                    $stmt->error;
            }


            /* =============================================
               CLOSE STATEMENT
            ============================================== */

            $stmt->close();
        }
    }
}


/* =========================================================
   PAGE
========================================================== */

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php include 'globals/head.php'; ?>

    <style>
        /* =================================================
           PAGE HEADER
        ================================================== */

        .page-header {

            margin-bottom: 1.5rem;

        }

        .page-header h1 {

            font-weight: 700;

            margin-bottom: .35rem;

        }

        .page-header p {

            margin-bottom: 0;

            color: #6c757d;

        }


        /* =================================================
           FORM CARD
        ================================================== */

        .form-card {

            border: 0;

            border-radius: 14px;

            box-shadow:
                0 4px 18px rgba(0, 0, 0, .07);

            overflow: hidden;

        }


        /* =================================================
           FORM CARD HEADER
        ================================================== */

        .form-card-header {

            padding: 1.25rem 1.5rem;

            background: #f8f9fa;

            border-bottom: 1px solid #dee2e6;

        }

        .form-card-header h5 {

            margin: 0;

            font-weight: 700;

        }

        .form-card-header p {

            margin: .25rem 0 0;

            color: #6c757d;

            font-size: .9rem;

        }


        /* =================================================
           FORM BODY
        ================================================== */

        .form-card-body {

            padding: 1.5rem;

        }


        /* =================================================
           FORM LABELS
        ================================================== */

        .form-label {

            font-weight: 600;

            margin-bottom: .45rem;

        }

        .required-mark {

            color: #dc3545;

        }


        /* =================================================
           HELP TEXT
        ================================================== */

        .form-text {

            font-size: .82rem;

        }


        /* =================================================
           ICON PREVIEW
        ================================================== */

        .icon-preview {

            width: 52px;

            height: 52px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 1.5rem;

            margin-top: .5rem;

        }


        /* =================================================
           ICON COLORS
        ================================================== */

        .icon-blue {

            color: #0d6efd;

            background: rgba(13, 110, 253, .12);

        }

        .icon-green {

            color: #198754;

            background: rgba(25, 135, 84, .12);

        }

        .icon-orange {

            color: #fd7e14;

            background: rgba(253, 126, 20, .12);

        }

        .icon-red {

            color: #dc3545;

            background: rgba(220, 53, 69, .12);

        }

        .icon-purple {

            color: #6f42c1;

            background: rgba(111, 66, 193, .12);

        }

        .icon-cyan {

            color: #0dcaf0;

            background: rgba(13, 202, 240, .12);

        }

        .icon-yellow {

            color: #ffc107;

            background: rgba(255, 193, 7, .15);

        }

        .icon-gray {

            color: #6c757d;

            background: rgba(108, 117, 125, .12);

        }


        /* =================================================
           FORM FOOTER
        ================================================== */

        .form-card-footer {

            padding: 1rem 1.5rem;

            background: #f8f9fa;

            border-top: 1px solid #dee2e6;

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 1rem;

        }


        /* =================================================
           DARK MODE
        ================================================== */

        html[data-theme="dark"] .page-header p {

            color: #adb5bd;

        }

        html[data-theme="dark"] .form-card {

            background: #1e1e1e;

            box-shadow:
                0 4px 18px rgba(0, 0, 0, .25);

        }

        html[data-theme="dark"] .form-card-header,

        html[data-theme="dark"] .form-card-footer {

            background: #252525;

            border-color: #3a3a3a;

        }

        html[data-theme="dark"] .form-card-header p {

            color: #adb5bd;

        }

        html[data-theme="dark"] .form-control,

        html[data-theme="dark"] .form-select {

            background-color: #2b2b2b;

            border-color: #444;

            color: #f8f9fa;

        }

        html[data-theme="dark"] .form-control::placeholder {

            color: #888;

        }

        html[data-theme="dark"] .form-control:focus,

        html[data-theme="dark"] .form-select:focus {

            background-color: #2b2b2b;

            color: #fff;

            border-color: #0d6efd;

        }

        html[data-theme="dark"] .form-text {

            color: #adb5bd !important;

        }


        /* =================================================
           RESPONSIVE
        ================================================== */

        @media (max-width: 767.98px) {

            .form-card-body {

                padding: 1rem;

            }

            .form-card-header {

                padding: 1rem;

            }

            .form-card-footer {

                padding: 1rem;

                flex-direction: column-reverse;

                align-items: stretch;

            }

            .form-card-footer .btn {

                width: 100%;

            }

        }
    </style>

</head>


<body>

    <?php include 'globals/sidebar.php'; ?>

    <?php include 'globals/topbar.php'; ?>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">

        <div class="container-fluid py-4">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="page-header">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                    <div>

                        <h1 class="h3">

                            <i class="bi bi-plus-circle me-2"></i>

                            Create Academic Post

                        </h1>

                        <p>

                            Add an academic resource or learning tool
                            to the student Academic Posts section.

                        </p>

                    </div>


                    <div>

                        <a
                            href="academic_posts.php"
                            class="btn btn-outline-primary">

                            <i class="bi bi-arrow-left me-1"></i>

                            Manage Academic Posts

                        </a>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 SUCCESS MESSAGE
            ================================================== -->

            <?php if ($success !== ""): ?>

                <div
                    class="alert alert-success alert-dismissible fade show"
                    role="alert">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    <?= htmlspecialchars($success, ENT_QUOTES, "UTF-8") ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 ERROR MESSAGE
            ================================================== -->

            <?php if ($error !== ""): ?>

                <div
                    class="alert alert-danger alert-dismissible fade show"
                    role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                    </button>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 FORM CARD
            ================================================== -->

            <div class="card form-card">


                <!-- =================================================
                     CARD HEADER
                ================================================== -->

                <div class="form-card-header">

                    <h5>

                        <i class="bi bi-file-earmark-plus me-2"></i>

                        Academic Post Information

                    </h5>

                    <p>

                        Configure the information that will be displayed
                        to students.

                    </p>

                </div>


                <!-- =================================================
                     FORM
                ================================================== -->

                <form
                    method="POST"
                    action="">


                    <!-- =================================================
                         FORM BODY
                    ================================================== -->

                    <div class="form-card-body">

                        <div class="row g-4">


                            <!-- =================================================
                                 TITLE
                            ================================================== -->

                            <div class="col-12">

                                <label
                                    for="title"
                                    class="form-label">

                                    Post Title

                                    <span class="required-mark">*</span>

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="title"
                                    name="title"
                                    value="<?= htmlspecialchars($title, ENT_QUOTES, "UTF-8") ?>"
                                    placeholder="e.g. Common Z-Transform Pairs"
                                    required>

                                <div class="form-text">

                                    Enter the title that students will see
                                    on the Academic Posts page.

                                </div>

                            </div>


                            <!-- =================================================
                                 DESCRIPTION
                            ================================================== -->

                            <div class="col-12">

                                <label
                                    for="description"
                                    class="form-label">

                                    Description

                                </label>

                                <textarea
                                    class="form-control"
                                    id="description"
                                    name="description"
                                    rows="4"
                                    placeholder="Briefly describe this academic resource..."><?= htmlspecialchars($description, ENT_QUOTES, "UTF-8") ?></textarea>

                                <div class="form-text">

                                    Give students a short explanation of
                                    what this resource contains.

                                </div>

                            </div>


                            <!-- =================================================
                                 PHP PAGE FILENAME
                            ================================================== -->

                            <div class="col-md-8">

                                <label
                                    for="url"
                                    class="form-label">

                                    PHP Page Filename

                                    <span class="required-mark">*</span>

                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="bi bi-filetype-php"></i>

                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="url"
                                        name="url"
                                        value="<?= htmlspecialchars($url, ENT_QUOTES, "UTF-8") ?>"
                                        placeholder="z_transform.php"
                                        required>

                                </div>

                                <div class="form-text">

                                    Enter the PHP filename of the academic
                                    page. Example:

                                    <code>z_transform.php</code>

                                </div>

                            </div>


                            <!-- =================================================
                                 SUBJECT
                            ================================================== -->

                            <div class="col-md-4">

                                <label
                                    for="subject"
                                    class="form-label">

                                    Subject

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="subject"
                                    name="subject"
                                    list="subjectList"
                                    value="<?= htmlspecialchars($subject, ENT_QUOTES, "UTF-8") ?>"
                                    placeholder="e.g. DSP">

                                <datalist id="subjectList">

                                    <?php foreach ($subjects as $existingSubject): ?>

                                        <option
                                            value="<?= htmlspecialchars($existingSubject, ENT_QUOTES, "UTF-8") ?>">

                                        <?php endforeach; ?>

                                </datalist>

                                <div class="form-text">

                                    Select an existing subject or type
                                    a new one.

                                </div>

                            </div>


                            <!-- =================================================
                                 DEPARTMENT
                            ================================================== -->

                            <div class="col-md-6">

                                <label
                                    for="department"
                                    class="form-label">

                                    Department

                                </label>

                                <select
                                    class="form-select"
                                    id="department"
                                    name="department">

                                    <option value="">

                                        All Departments

                                    </option>

                                    <?php foreach ($departments as $existingDepartment): ?>

                                        <option
                                            value="<?= htmlspecialchars($existingDepartment, ENT_QUOTES, "UTF-8") ?>"
                                            <?= $department === $existingDepartment ? "selected" : "" ?>>

                                            <?= htmlspecialchars($existingDepartment, ENT_QUOTES, "UTF-8") ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                                <div class="form-text">

                                    Leave as "All Departments" if the
                                    resource applies to all students.

                                </div>

                            </div>


                            <!-- =================================================
                                 SORT ORDER
                            ================================================== -->

                            <div class="col-md-3">

                                <label
                                    for="sort_order"
                                    class="form-label">

                                    Sort Order

                                </label>

                                <input
                                    type="number"
                                    class="form-control"
                                    id="sort_order"
                                    name="sort_order"
                                    value="<?= htmlspecialchars((string)$sortOrder, ENT_QUOTES, "UTF-8") ?>"
                                    min="0"
                                    step="1">

                                <div class="form-text">

                                    Lower numbers appear first.

                                </div>

                            </div>


                            <!-- =================================================
                                 STATUS
                            ================================================== -->

                            <div class="col-md-3">

                                <label
                                    class="form-label d-block">

                                    Status

                                </label>

                                <div class="form-check form-switch mt-2">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        role="switch"
                                        id="status"
                                        name="status"
                                        <?= $status == 1 ? "checked" : "" ?>>

                                    <label
                                        class="form-check-label"
                                        for="status">

                                        Active

                                    </label>

                                </div>

                                <div class="form-text">

                                    Active posts are visible to students.

                                </div>

                            </div>


                            <!-- =================================================
                                 ICON
                            ================================================== -->

                            <div class="col-md-8">

                                <label
                                    for="icon"
                                    class="form-label">

                                    Bootstrap Icon

                                    <span class="required-mark">*</span>

                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="bi bi-bootstrap"></i>

                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="icon"
                                        name="icon"
                                        value="<?= htmlspecialchars($icon, ENT_QUOTES, "UTF-8") ?>"
                                        placeholder="bi-file-earmark-text"
                                        required>

                                </div>

                                <div class="form-text">

                                    Example:

                                    <code>bi-file-earmark-text</code>

                                    or

                                    <code>bi-calculator</code>

                                </div>

                                <div
                                    id="iconPreview"
                                    class="icon-preview icon-<?= htmlspecialchars($iconColor, ENT_QUOTES, "UTF-8") ?>">

                                    <i
                                        id="previewIcon"
                                        class="bi <?= htmlspecialchars($icon, ENT_QUOTES, "UTF-8") ?>">
                                    </i>

                                </div>

                            </div>


                            <!-- =================================================
                                 ICON COLOR
                            ================================================== -->

                            <div class="col-md-4">

                                <label
                                    for="icon_color"
                                    class="form-label">

                                    Icon Color

                                </label>

                                <select
                                    class="form-select"
                                    id="icon_color"
                                    name="icon_color">

                                    <option
                                        value="blue"
                                        <?= $iconColor === "blue" ? "selected" : "" ?>>

                                        Blue

                                    </option>

                                    <option
                                        value="green"
                                        <?= $iconColor === "green" ? "selected" : "" ?>>

                                        Green

                                    </option>

                                    <option
                                        value="orange"
                                        <?= $iconColor === "orange" ? "selected" : "" ?>>

                                        Orange

                                    </option>

                                    <option
                                        value="red"
                                        <?= $iconColor === "red" ? "selected" : "" ?>>

                                        Red

                                    </option>

                                    <option
                                        value="purple"
                                        <?= $iconColor === "purple" ? "selected" : "" ?>>

                                        Purple

                                    </option>

                                    <option
                                        value="cyan"
                                        <?= $iconColor === "cyan" ? "selected" : "" ?>>

                                        Cyan

                                    </option>

                                    <option
                                        value="yellow"
                                        <?= $iconColor === "yellow" ? "selected" : "" ?>>

                                        Yellow

                                    </option>

                                    <option
                                        value="gray"
                                        <?= $iconColor === "gray" ? "selected" : "" ?>>

                                        Gray

                                    </option>

                                </select>

                                <div class="form-text">

                                    Select the color used for the
                                    academic post icon.

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         FORM FOOTER
                    ================================================== -->

                    <div class="form-card-footer">

                        <a
                            href="academic_posts.php"
                            class="btn btn-outline-secondary">

                            <i class="bi bi-x-circle me-1"></i>

                            Cancel

                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-check-circle me-1"></i>

                            Create Academic Post

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>


    <!-- =====================================================
         GLOBAL SCRIPTS
    ====================================================== -->

    <?php include 'globals/scripts.php'; ?>


    <!-- =====================================================
         ICON PREVIEW SCRIPT
    ====================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {

                const iconInput =
                    document.getElementById("icon");

                const colorInput =
                    document.getElementById("icon_color");

                const previewIcon =
                    document.getElementById("previewIcon");

                const iconPreview =
                    document.getElementById("iconPreview");


                /* =============================================
                   UPDATE ICON PREVIEW
                ============================================== */

                function updateIconPreview() {

                    const icon =
                        iconInput.value.trim() ||
                        "bi-file-earmark-text";

                    const color =
                        colorInput.value;


                    /* =========================================
                       UPDATE ICON
                    ========================================== */

                    previewIcon.className =
                        "bi " + icon;


                    /* =========================================
                       REMOVE OLD COLOR CLASSES
                    ========================================== */

                    iconPreview.classList.remove(

                        "icon-blue",
                        "icon-green",
                        "icon-orange",
                        "icon-red",
                        "icon-purple",
                        "icon-cyan",
                        "icon-yellow",
                        "icon-gray"

                    );


                    /* =========================================
                       ADD CURRENT COLOR CLASS
                    ========================================== */

                    iconPreview.classList.add(
                        "icon-" + color
                    );

                }


                /* =============================================
                   ICON INPUT EVENT
                ============================================== */

                iconInput.addEventListener(
                    "input",
                    updateIconPreview
                );


                /* =============================================
                   COLOR INPUT EVENT
                ============================================== */

                colorInput.addEventListener(
                    "change",
                    updateIconPreview
                );


                /* =============================================
                   INITIAL PREVIEW
                ============================================== */

                updateIconPreview();

            }

        );
    </script>

</body>

</html>