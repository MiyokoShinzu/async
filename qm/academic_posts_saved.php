
<?php

/* =========================================================
   STUDENT SAVED ACADEMIC POSTS
   ETS-Async Learning Portal

   Features:
   - Displays the logged-in student's saved posts
   - Database-driven content
   - Search saved posts
   - Filter by subject
   - Filter by department
   - Open post through academic_post_start.php
   - Remove saved posts without leaving the page
   - Supports light and dark mode
   ========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION
========================================================== */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {

    header("Location: ../login.php");

    exit;
}


/* =========================================================
   CURRENT USER
========================================================== */

$user =
    $_SESSION["user"];


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   USER DATA
========================================================== */

$firstName =
    $user["first_name"] ?? "";

$lastName =
    $user["last_name"] ?? "";

$middleInitial =
    $user["middle_initial"] ?? "";

$extensionName =
    $user["extension_name"] ?? "";

$studentId =
    trim(
        $user["student_id"] ?? ""
    );

$studentDepartment =
    $user["department"] ?? "";

$yearSection =
    $user["year_section"] ?? "";

$email =
    $user["email"] ?? "";

$username =
    $user["username"] ?? "";

$access =
    $user["access"] ?? "student";


/* =========================================================
   FULL NAME
========================================================== */

$fullName = trim(

    $firstName .
        " " .

        (
            $middleInitial !== ""
            ? $middleInitial . ". "
            : ""
        ) .

        $lastName .

        (
            $extensionName !== ""
            ? " " . $extensionName
            : ""
        )
);


/* =========================================================
   INITIALS
========================================================== */

$initials = "";


if ($firstName !== "") {

    $initials .= strtoupper(
        substr($firstName, 0, 1)
    );
}


if ($lastName !== "") {

    $initials .= strtoupper(
        substr($lastName, 0, 1)
    );
}


/* =========================================================
   LOAD SAVED POSTS
========================================================== */

$savedPosts = [];


$savedSql = "

    SELECT

        s.id AS saved_id,

        s.post_id,

        s.saved_at,

        p.title,

        p.description,

        p.url,

        p.subject,

        p.department,

        p.icon,

        p.icon_color

    FROM academic_post_saved AS s

    INNER JOIN academic_posts AS p

        ON p.id = s.post_id

    WHERE s.student_id = ?

      AND p.status = 1

    ORDER BY

        s.saved_at DESC,

        s.id DESC

";


$savedStmt =
    $mysqli->prepare(
        $savedSql
    );


if ($savedStmt) {

    $savedStmt->bind_param(
        "s",
        $studentId
    );


    $savedStmt->execute();


    $savedResult =
        $savedStmt->get_result();


    while (
        $row =
        $savedResult->fetch_assoc()
    ) {

        $savedPosts[] =
            $row;
    }


    $savedStmt->close();
}


/* =========================================================
   LOAD SUBJECTS FROM SAVED POSTS
========================================================== */

$subjects = [];


foreach (
    $savedPosts
    as $savedPost
) {

    $subject =
        trim(
            $savedPost["subject"] ?? ""
        );


    if (
        $subject !== "" &&
        !in_array(
            $subject,
            $subjects,
            true
        )
    ) {

        $subjects[] =
            $subject;
    }
}


/* =========================================================
   SORT SUBJECTS
========================================================== */

natcasesort(
    $subjects
);


$subjects =
    array_values(
        $subjects
    );


/* =========================================================
   LOAD DEPARTMENTS FROM SAVED POSTS
========================================================== */

$departments = [];


foreach (
    $savedPosts
    as $savedPost
) {

    $department =
        trim(
            $savedPost["department"] ?? ""
        );


    if (
        $department !== "" &&
        !in_array(
            $department,
            $departments,
            true
        )
    ) {

        $departments[] =
            $department;
    }
}


/* =========================================================
   SORT DEPARTMENTS
========================================================== */

natcasesort(
    $departments
);


$departments =
    array_values(
        $departments
    );


/* =========================================================
   FILTER VALUE HELPER
========================================================== */

function savedPostFilterValue($value)
{

    $value =
        trim($value);


    $value =
        strtolower($value);


    $value =
        preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $value
        );


    return trim(
        $value,
        "-"
    );
}


/* =========================================================
   ALLOWED ICON COLORS
========================================================== */

$allowedIconColors = [

    "blue",
    "purple",
    "orange",
    "green",
    "red",
    "yellow"

];

?>


<!DOCTYPE html>

<html lang="en">


<?php include 'globals/head.php'; ?>


<body>


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <?php include 'globals/sidebar.php'; ?>


    <!-- =====================================================
         TOPBAR
    ====================================================== -->

    <?php include 'globals/topbar.php'; ?>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">


        <div class="content-wrapper">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="page-header">

                <div>

                    <h2>

                        Saved Posts

                    </h2>


                    <p>

                        View and manage your saved
                        academic references and
                        learning resources.

                    </p>

                </div>

            </div>


            <!-- =================================================
                 SEARCH AND FILTERS
            ================================================== -->

            <?php if (
                count($savedPosts) > 0
            ): ?>

                <div class="posts-controls mb-4">


                    <!-- =============================================
                         SEARCH
                    ============================================== -->

                    <div class="posts-search">

                        <i class="bi bi-search"></i>


                        <input
                            type="text"
                            id="savedPostSearch"
                            placeholder="Search saved posts..."
                            autocomplete="off">

                    </div>


                    <!-- =============================================
                         SUBJECT FILTER
                    ============================================== -->

                    <?php if (
                        count($subjects) > 0
                    ): ?>

                        <div class="filter-group">

                            <span class="filter-label">

                                Subject

                            </span>


                            <div
                                class="posts-filters"
                                id="savedSubjectFilters">


                                <button
                                    type="button"
                                    class="post-filter active"
                                    data-subject="all">

                                    All Subjects

                                </button>


                                <?php foreach (
                                    $subjects
                                    as $subject
                                ): ?>


                                    <?php

                                    $subjectValue =
                                        savedPostFilterValue(
                                            $subject
                                        );

                                    ?>


                                    <button
                                        type="button"
                                        class="post-filter"
                                        data-subject="<?= htmlspecialchars(
                                                            $subjectValue,
                                                            ENT_QUOTES,
                                                            "UTF-8"
                                                        ) ?>">

                                        <?= htmlspecialchars(
                                            $subject,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>

                                    </button>


                                <?php endforeach; ?>


                            </div>

                        </div>

                    <?php endif; ?>


                    <!-- =============================================
                         DEPARTMENT FILTER
                    ============================================== -->

                    <?php if (
                        count($departments) > 0
                    ): ?>

                        <div class="filter-group">

                            <span class="filter-label">

                                Department

                            </span>


                            <div
                                class="posts-filters"
                                id="savedDepartmentFilters">


                                <button
                                    type="button"
                                    class="post-filter active"
                                    data-department="all">

                                    All Departments

                                </button>


                                <?php foreach (
                                    $departments
                                    as $department
                                ): ?>


                                    <?php

                                    $departmentValue =
                                        savedPostFilterValue(
                                            $department
                                        );

                                    ?>


                                    <button
                                        type="button"
                                        class="post-filter"
                                        data-department="<?= htmlspecialchars(
                                                                $departmentValue,
                                                                ENT_QUOTES,
                                                                "UTF-8"
                                                            ) ?>">

                                        <?= htmlspecialchars(
                                            $department,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>

                                    </button>


                                <?php endforeach; ?>


                            </div>

                        </div>

                    <?php endif; ?>


                </div>

            <?php endif; ?>


            <!-- =================================================
                 RESULT COUNT
            ================================================== -->

            <div
                class="posts-results-header"
                id="savedResultsHeader"
                <?php if (count($savedPosts) === 0): ?>
                style="display:none;"
                <?php endif; ?>>

                <span id="savedPostCount">

                    <?= count($savedPosts) ?>

                    <?= count($savedPosts) === 1
                        ? "saved post"
                        : "saved posts"
                    ?>

                </span>

            </div>


            <!-- =================================================
                 SAVED POSTS GRID
            ================================================== -->

            <div
                class="posts-grid"
                id="savedPostsGrid">


                <?php foreach (
                    $savedPosts
                    as $post
                ): ?>


                    <?php

                    /* -----------------------------------------
                       POST VALUES
                    ----------------------------------------- */

                    $savedId =
                        (int)(
                            $post["saved_id"]
                            ?? 0
                        );


                    $postId =
                        (int)(
                            $post["post_id"]
                            ?? 0
                        );


                    $title =
                        trim(
                            $post["title"]
                                ?? ""
                        );


                    $description =
                        trim(
                            $post["description"]
                                ?? ""
                        );


                    $subject =
                        trim(
                            $post["subject"]
                                ?? "General"
                        );


                    $department =
                        trim(
                            $post["department"]
                                ?? "General"
                        );


                    $icon =
                        trim(
                            $post["icon"]
                                ?? "bi-file-earmark-text"
                        );


                    $iconColor =
                        trim(
                            $post["icon_color"]
                                ?? "blue"
                        );


                    /* -----------------------------------------
                       VALIDATE ICON COLOR
                    ----------------------------------------- */

                    if (
                        !in_array(
                            $iconColor,
                            $allowedIconColors,
                            true
                        )
                    ) {

                        $iconColor =
                            "blue";
                    }


                    /* -----------------------------------------
                       FILTER VALUES
                    ----------------------------------------- */

                    $subjectValue =
                        savedPostFilterValue(
                            $subject
                        );


                    $departmentValue =
                        savedPostFilterValue(
                            $department
                        );


                    /* -----------------------------------------
                       SEARCH DATA
                    ----------------------------------------- */

                    $searchData =
                        strtolower(

                            $title .
                                " " .
                                $description .
                                " " .
                                $subject .
                                " " .
                                $department

                        );


                    /* -----------------------------------------
                       SAVED DATE
                    ----------------------------------------- */

                    $savedAt =
                        $post["saved_at"]
                        ?? "";

                    ?>


                    <!-- =================================================
                         SAVED POST CARD
                    ================================================== -->

                    <div
                        class="academic-post-card saved-post-card"

                        data-id="<?= $postId ?>"

                        data-saved-id="<?= $savedId ?>"

                        data-subject="<?= htmlspecialchars(
                                            $subjectValue,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>"

                        data-department="<?= htmlspecialchars(
                                                $departmentValue,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>"

                        data-search="<?= htmlspecialchars(
                                            $searchData,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>">


                        <!-- =================================================
                             POST ICON
                        ================================================== -->

                        <div
                            class="post-icon <?= htmlspecialchars(
                                                    $iconColor,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>">

                            <i
                                class="bi <?= htmlspecialchars(
                                                $icon,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>">
                            </i>

                        </div>


                        <!-- =================================================
                             POST CONTENT
                        ================================================== -->

                        <div class="post-content">


                            <!-- POST TITLE -->

                            <h5>

                                <?= htmlspecialchars(
                                    $title,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </h5>


                            <!-- POST DESCRIPTION -->

                            <p>

                                <?= htmlspecialchars(
                                    $description,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </p>


                            <!-- POST META -->

                            <div class="post-meta">


                                <span class="post-subject">

                                    <?= htmlspecialchars(
                                        $subject,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>

                                </span>


                                <span class="post-department">

                                    <?= htmlspecialchars(
                                        $department,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>

                                </span>


                            </div>


                            <!-- =================================================
                                 SAVED DATE
                            ================================================== -->

                            <?php if (
                                $savedAt !== ""
                            ): ?>

                                <div class="saved-date">

                                    <i class="bi bi-bookmark-fill"></i>

                                    Saved

                                    <?= htmlspecialchars(
                                        date(
                                            "M d, Y",
                                            strtotime($savedAt)
                                        ),
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>

                                </div>

                            <?php endif; ?>


                            <!-- =================================================
                                 POST ACTIONS
                            ================================================== -->

                            <div class="post-actions">


                                <!-- =============================================
                                     OPEN POST
                                ============================================== -->

                                <a
                                    href="academic_post_start.php?id=<?= $postId ?>"
                                    class="post-open-btn"
                                    target="_blank"
                                    rel="noopener noreferrer">

                                    <i class="bi bi-box-arrow-up-right"></i>

                                    <span>

                                        Open Post

                                    </span>

                                </a>


                                <!-- =============================================
                                     REMOVE SAVED POST
                                ============================================== -->

                                <button
                                    type="button"

                                    class="post-bookmark-btn saved"

                                    data-post-id="<?= $postId ?>"

                                    data-saved="1"

                                    aria-label="Remove from saved posts"

                                    title="Remove from Saved Posts">

                                    <i class="bi bi-bookmark-fill"></i>

                                </button>


                            </div>


                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


            <!-- =================================================
                 EMPTY SEARCH RESULT
            ================================================== -->

            <div
                id="savedPostSearchEmpty"
                class="posts-empty"
                style="display:none;">

                <div class="posts-empty-icon">

                    <i class="bi bi-search"></i>

                </div>


                <h5>

                    No saved posts found

                </h5>


                <p>

                    Try a different search term or
                    select another filter.

                </p>

            </div>


            <!-- =================================================
                 NO SAVED POSTS
            ================================================== -->

            <?php if (
                count($savedPosts) === 0
            ): ?>


                <div
                    class="posts-empty"
                    id="noSavedPosts">

                    <div class="posts-empty-icon">

                        <i class="bi bi-bookmark"></i>

                    </div>


                    <h5>

                        No Saved Posts

                    </h5>


                    <p>

                        You have not saved any
                        academic posts yet.

                    </p>


                    <a
                        href="academic_posts.php"
                        class="browse-posts-btn">

                        <i class="bi bi-journal-text"></i>

                        Browse Academic Posts

                    </a>

                </div>


            <?php endif; ?>


        </div>


    </main>


    <!-- =========================================================
         STYLES
    ========================================================== -->

    <style>
        /* =========================================================
           THEME VARIABLES
        ========================================================== */

        :root {

            --posts-bg:
                #ffffff;

            --posts-border:
                #e4e7ec;

            --posts-input-border:
                #d0d5dd;

            --posts-text:
                #172033;

            --posts-muted:
                #667085;

            --posts-secondary:
                #475467;

            --posts-control-bg:
                #ffffff;

            --posts-hover-bg:
                #f8fafc;

            --posts-filter-active:
                #1e3a8a;

            --posts-filter-active-border:
                #1e3a8a;

            --posts-tag-bg:
                #f2f4f7;

            --posts-search-focus:
                #2563eb;

        }


        /* =========================================================
           DARK MODE
        ========================================================== */

        html[data-theme="dark"] {

            --posts-bg:
                #151922;

            --posts-border:
                #2a3140;

            --posts-input-border:
                #3a4252;

            --posts-text:
                #f1f5f9;

            --posts-muted:
                #a8b0bf;

            --posts-secondary:
                #c2c9d3;

            --posts-control-bg:
                #151922;

            --posts-hover-bg:
                #1c2230;

            --posts-filter-active:
                #2563eb;

            --posts-filter-active-border:
                #2563eb;

            --posts-tag-bg:
                #252c38;

            --posts-search-focus:
                #3b82f6;

        }


        /* =========================================================
           CONTROLS
        ========================================================== */

        .posts-controls {

            padding:
                20px;

            background:
                var(--posts-bg);

            border:
                1px solid var(--posts-border);

            border-radius:
                10px;

        }


        /* =========================================================
           SEARCH
        ========================================================== */

        .posts-search {

            position:
                relative;

            margin-bottom:
                17px;

        }


        .posts-search i {

            position:
                absolute;

            left:
                15px;

            top:
                50%;

            transform:
                translateY(-50%);

            color:
                var(--posts-muted);

        }


        .posts-search input {

            width:
                100%;

            height:
                45px;

            padding:
                0 15px 0 42px;

            border:
                1px solid var(--posts-input-border);

            border-radius:
                7px;

            outline:
                none;

            font-family:
                inherit;

            font-size:
                .85rem;

            color:
                var(--posts-text);

            background:
                var(--posts-control-bg);

        }


        .posts-search input:focus {

            border-color:
                var(--posts-search-focus);

            box-shadow:
                0 0 0 3px rgba(37,
                    99,
                    235,
                    .10);

        }


        /* =========================================================
           FILTER GROUP
        ========================================================== */

        .filter-group {

            margin-bottom:
                14px;

        }


        .filter-group:last-child {

            margin-bottom:
                0;

        }


        /* =========================================================
           FILTER LABEL
        ========================================================== */

        .filter-label {

            display:
                block;

            margin-bottom:
                7px;

            color:
                var(--posts-secondary);

            font-size:
                .72rem;

            font-weight:
                700;

            text-transform:
                uppercase;

            letter-spacing:
                .04em;

        }


        /* =========================================================
           FILTER BUTTONS
        ========================================================== */

        .posts-filters {

            display:
                flex;

            flex-wrap:
                wrap;

            gap:
                8px;

        }


        .post-filter {

            border:
                1px solid var(--posts-input-border);

            background:
                var(--posts-control-bg);

            color:
                var(--posts-muted);

            padding:
                7px 12px;

            border-radius:
                6px;

            font-family:
                inherit;

            font-size:
                .76rem;

            font-weight:
                600;

            cursor:
                pointer;

            transition:
                all .2s ease;

        }


        .post-filter:hover {

            border-color:
                var(--posts-secondary);

            color:
                var(--posts-text);

            background:
                var(--posts-hover-bg);

        }


        .post-filter.active {

            color:
                #ffffff;

            background:
                var(--posts-filter-active);

            border-color:
                var(--posts-filter-active-border);

        }


        /* =========================================================
           RESULT HEADER
        ========================================================== */

        .posts-results-header {

            margin-bottom:
                12px;

            color:
                var(--posts-muted);

            font-size:
                .78rem;

            font-weight:
                600;

        }


        /* =========================================================
           POST GRID
        ========================================================== */

        .posts-grid {

            display:
                grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap:
                14px;

        }


        /* =========================================================
           POST CARD
        ========================================================== */

        .academic-post-card {

            display:
                flex;

            align-items:
                flex-start;

            gap:
                14px;

            padding:
                20px;

            min-height:
                145px;

            border:
                1px solid var(--posts-border);

            border-radius:
                9px;

            background:
                var(--posts-bg);

            color:
                var(--posts-text);

            transition:
                border-color .2s ease,
                background .2s ease,
                color .2s ease,
                transform .2s ease,
                box-shadow .2s ease;

        }


        .academic-post-card:hover {

            color:
                var(--posts-text);

            border-color:
                var(--posts-input-border);

            background:
                var(--posts-hover-bg);

            transform:
                translateY(-2px);

        }


        /* =========================================================
           POST ICON
        ========================================================== */

        .post-icon {

            flex:
                0 0 auto;

            width:
                42px;

            height:
                42px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                8px;

            font-size:
                1rem;

        }


        /* =========================================================
           ICON COLORS
        ========================================================== */

        .post-icon.blue {

            color:
                #2563eb;

            background:
                #eff6ff;

        }


        .post-icon.purple {

            color:
                #7c3aed;

            background:
                #f5f3ff;

        }


        .post-icon.orange {

            color:
                #ea580c;

            background:
                #fff7ed;

        }


        .post-icon.green {

            color:
                #16a34a;

            background:
                #ecfdf3;

        }


        .post-icon.red {

            color:
                #dc2626;

            background:
                #fef2f2;

        }


        .post-icon.yellow {

            color:
                #ca8a04;

            background:
                #fefce8;

        }


        /* =========================================================
           DARK MODE ICON COLORS
        ========================================================== */

        html[data-theme="dark"] .post-icon.blue {

            color:
                #60a5fa;

            background:
                rgba(37,
                    99,
                    235,
                    .16);

        }


        html[data-theme="dark"] .post-icon.purple {

            color:
                #a78bfa;

            background:
                rgba(124,
                    58,
                    237,
                    .16);

        }


        html[data-theme="dark"] .post-icon.orange {

            color:
                #fb923c;

            background:
                rgba(234,
                    88,
                    12,
                    .16);

        }


        html[data-theme="dark"] .post-icon.green {

            color:
                #4ade80;

            background:
                rgba(22,
                    163,
                    74,
                    .16);

        }


        html[data-theme="dark"] .post-icon.red {

            color:
                #f87171;

            background:
                rgba(220,
                    38,
                    38,
                    .16);

        }


        html[data-theme="dark"] .post-icon.yellow {

            color:
                #facc15;

            background:
                rgba(202,
                    138,
                    4,
                    .16);

        }


        /* =========================================================
           POST CONTENT
        ========================================================== */

        .post-content {

            min-width:
                0;

            flex:
                1;

        }


        .post-content h5 {

            margin:
                0 0 5px;

            font-size:
                .88rem;

            font-weight:
                700;

            line-height:
                1.35;

            color:
                var(--posts-text);

        }


        .post-content p {

            margin:
                0 0 11px;

            color:
                var(--posts-muted);

            font-size:
                .76rem;

            line-height:
                1.55;

        }


        /* =========================================================
           POST META
        ========================================================== */

        .post-meta {

            display:
                flex;

            flex-wrap:
                wrap;

            gap:
                6px;

        }


        .post-subject,
        .post-department {

            display:
                inline-block;

            color:
                var(--posts-secondary);

            background:
                var(--posts-tag-bg);

            border-radius:
                5px;

            padding:
                3px 7px;

            font-size:
                .66rem;

            font-weight:
                600;

        }


        /* =========================================================
           SAVED DATE
        ========================================================== */

        .saved-date {

            display:
                flex;

            align-items:
                center;

            gap:
                5px;

            margin-top:
                9px;

            color:
                var(--posts-muted);

            font-size:
                .66rem;

            font-weight:
                500;

        }


        .saved-date i {

            color:
                #f59e0b;

        }


        /* =========================================================
           POST ACTIONS
        ========================================================== */

        .post-actions {

            display:
                flex;

            align-items:
                center;

            gap:
                8px;

            margin-top:
                13px;

        }


        /* =========================================================
           OPEN POST BUTTON
        ========================================================== */

        .post-open-btn {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                6px;

            padding:
                6px 10px;

            border-radius:
                6px;

            background:
                #1e3a8a;

            color:
                #ffffff;

            text-decoration:
                none;

            font-size:
                .68rem;

            font-weight:
                600;

            transition:
                background .2s ease,
                transform .2s ease;

        }


        .post-open-btn:hover {

            background:
                #2563eb;

            color:
                #ffffff;

            transform:
                translateY(-1px);

        }


        /* =========================================================
           BOOKMARK BUTTON
        ========================================================== */

        .post-bookmark-btn {

            width:
                32px;

            height:
                32px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                1px solid #f59e0b;

            border-radius:
                6px;

            background:
                rgba(245,
                    158,
                    11,
                    .10);

            color:
                #f59e0b;

            cursor:
                pointer;

            font-size:
                .9rem;

            transition:
                background .2s ease,
                border-color .2s ease,
                color .2s ease,
                transform .2s ease;

        }


        .post-bookmark-btn:hover {

            color:
                #d97706;

            border-color:
                #d97706;

            background:
                rgba(245,
                    158,
                    11,
                    .16);

            transform:
                translateY(-1px);

        }


        /* =========================================================
           EMPTY STATE
        ========================================================== */

        .posts-empty {

            padding:
                60px 20px;

            text-align:
                center;

            border:
                1px solid var(--posts-border);

            border-radius:
                10px;

            background:
                var(--posts-bg);

            color:
                var(--posts-text);

        }


        .posts-empty-icon {

            width:
                48px;

            height:
                48px;

            margin:
                0 auto 15px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                50%;

            background:
                var(--posts-tag-bg);

            color:
                var(--posts-muted);

            font-size:
                1.1rem;

        }


        .posts-empty h5 {

            margin:
                0 0 6px;

            font-size:
                .95rem;

            color:
                var(--posts-text);

        }


        .posts-empty p {

            margin:
                0 0 18px;

            color:
                var(--posts-muted);

            font-size:
                .8rem;

        }


        /* =========================================================
           BROWSE POSTS BUTTON
        ========================================================== */

        .browse-posts-btn {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                7px;

            padding:
                8px 13px;

            border-radius:
                6px;

            background:
                #1e3a8a;

            color:
                #ffffff;

            text-decoration:
                none;

            font-size:
                .72rem;

            font-weight:
                600;

            transition:
                background .2s ease,
                transform .2s ease;

        }


        .browse-posts-btn:hover {

            background:
                #2563eb;

            color:
                #ffffff;

            transform:
                translateY(-1px);

        }


        /* =========================================================
           RESPONSIVE
        ========================================================== */

        @media (max-width: 1100px) {

            .posts-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }


        @media (max-width: 700px) {

            .posts-grid {

                grid-template-columns:
                    1fr;

            }

        }


        @media (max-width: 576px) {

            .posts-controls {

                padding:
                    15px;

            }


            .post-filter {

                font-size:
                    .72rem;

                padding:
                    6px 9px;

            }


            .academic-post-card {

                padding:
                    17px;

            }

        }
    </style>


    <!-- =========================================================
         JAVASCRIPT
    ========================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {


                /* =================================================
                   ELEMENTS
                ================================================== */

                const searchInput =
                    document.getElementById(
                        "savedPostSearch"
                    );


                const subjectButtons =
                    document.querySelectorAll(
                        "#savedSubjectFilters .post-filter"
                    );


                const departmentButtons =
                    document.querySelectorAll(
                        "#savedDepartmentFilters .post-filter"
                    );


                const postCards =
                    document.querySelectorAll(
                        ".saved-post-card"
                    );


                const postCount =
                    document.getElementById(
                        "savedPostCount"
                    );


                const resultsHeader =
                    document.getElementById(
                        "savedResultsHeader"
                    );


                const searchEmpty =
                    document.getElementById(
                        "savedPostSearchEmpty"
                    );


                /* =================================================
                   FILTER VALUES
                ================================================== */

                let selectedSubject =
                    "all";


                let selectedDepartment =
                    "all";


                /* =================================================
                   FILTER SAVED POSTS
                ================================================== */

                function filterSavedPosts() {

                    const searchTerm =
                        searchInput ?
                        searchInput.value
                        .toLowerCase()
                        .trim() :
                        "";


                    let visibleCount =
                        0;


                    postCards.forEach(
                        function(card) {


                            const subject =
                                (
                                    card.dataset.subject ||
                                    ""
                                )
                                .toLowerCase();


                            const department =
                                (
                                    card.dataset.department ||
                                    ""
                                )
                                .toLowerCase();


                            const searchData =
                                (
                                    card.dataset.search ||
                                    ""
                                )
                                .toLowerCase();


                            const cardText =
                                card.innerText
                                .toLowerCase();


                            /* =====================================
                               SUBJECT MATCH
                            ====================================== */

                            const subjectMatch =

                                selectedSubject === "all" ||

                                subject === selectedSubject;


                            /* =====================================
                               DEPARTMENT MATCH
                            ====================================== */

                            const departmentMatch =

                                selectedDepartment === "all" ||

                                department === selectedDepartment;


                            /* =====================================
                               SEARCH MATCH
                            ====================================== */

                            const searchMatch =

                                searchTerm === "" ||

                                searchData.includes(
                                    searchTerm
                                ) ||

                                cardText.includes(
                                    searchTerm
                                );


                            /* =====================================
                               FINAL MATCH
                            ====================================== */

                            if (

                                subjectMatch &&

                                departmentMatch &&

                                searchMatch

                            ) {

                                card.style.display =
                                    "flex";

                                visibleCount++;

                            } else {

                                card.style.display =
                                    "none";

                            }

                        }
                    );


                    /* =================================================
                       UPDATE COUNT
                    ================================================== */

                    if (postCount) {

                        postCount.textContent =

                            visibleCount +

                            (
                                visibleCount === 1 ?
                                " saved post" :
                                " saved posts"
                            );

                    }


                    /* =================================================
                       EMPTY SEARCH RESULT
                    ================================================== */

                    if (searchEmpty) {

                        if (
                            visibleCount === 0 &&
                            postCards.length > 0
                        ) {

                            searchEmpty.style.display =
                                "block";

                        } else {

                            searchEmpty.style.display =
                                "none";

                        }

                    }

                }


                /* =================================================
                   SUBJECT FILTER
                ================================================== */

                subjectButtons.forEach(
                    function(button) {

                        button.addEventListener(
                            "click",
                            function() {


                                subjectButtons.forEach(
                                    function(item) {

                                        item.classList.remove(
                                            "active"
                                        );

                                    }
                                );


                                this.classList.add(
                                    "active"
                                );


                                selectedSubject =
                                    this.dataset.subject;


                                filterSavedPosts();

                            }
                        );

                    }
                );


                /* =================================================
                   DEPARTMENT FILTER
                ================================================== */

                departmentButtons.forEach(
                    function(button) {

                        button.addEventListener(
                            "click",
                            function() {


                                departmentButtons.forEach(
                                    function(item) {

                                        item.classList.remove(
                                            "active"
                                        );

                                    }
                                );


                                this.classList.add(
                                    "active"
                                );


                                selectedDepartment =
                                    this.dataset.department;


                                filterSavedPosts();

                            }
                        );

                    }
                );


                /* =================================================
                   SEARCH
                ================================================== */

                if (searchInput) {

                    searchInput.addEventListener(
                        "input",
                        filterSavedPosts
                    );

                }


                /* =================================================
                   REMOVE SAVED POST
                ================================================== */

                const bookmarkButtons =
                    document.querySelectorAll(
                        ".post-bookmark-btn"
                    );


                bookmarkButtons.forEach(
                    function(button) {


                        button.addEventListener(
                            "click",
                            async function(event) {


                                event.preventDefault();

                                event.stopPropagation();


                                const postId =
                                    this.dataset.postId;


                                if (!postId) {

                                    return;

                                }


                                /* =====================================
                                   SAVE CURRENT STATE
                                ====================================== */

                                const previousHTML =
                                    this.innerHTML;


                                const previousTitle =
                                    this.title;


                                const previousAriaLabel =
                                    this.getAttribute(
                                        "aria-label"
                                    );


                                this.disabled =
                                    true;


                                /* =====================================
                                   SHOW LOADING
                                ====================================== */

                                this.innerHTML = `

                                    <span
                                        class="spinner-border spinner-border-sm"
                                        style="
                                            width: .8rem;
                                            height: .8rem;
                                            border-width: .12rem;
                                        "
                                        aria-hidden="true">
                                    </span>

                                `;


                                try {


                                    /* =================================
                                       FORM DATA
                                    ================================== */

                                    const formData =
                                        new FormData();


                                    formData.append(
                                        "post_id",
                                        postId
                                    );


                                    /* =================================
                                       SEND REQUEST
                                    ================================== */

                                    const response =
                                        await fetch(
                                            "academic_post_toggle_save.php", {
                                                method: "POST",

                                                body: formData
                                            }
                                        );


                                    /* =================================
                                       VERIFY RESPONSE
                                    ================================== */

                                    if (
                                        !response.ok
                                    ) {

                                        throw new Error(
                                            "Server returned HTTP " +
                                            response.status
                                        );

                                    }


                                    /* =================================
                                       READ JSON
                                    ================================== */

                                    const data =
                                        await response.json();


                                    /* =================================
                                       CHECK RESPONSE
                                    ================================== */

                                    if (
                                        !data.success
                                    ) {

                                        throw new Error(
                                            data.message ||
                                            "Unable to update Saved Posts."
                                        );

                                    }


                                    /* =================================
                                       POST WAS REMOVED
                                    ================================== */

                                    if (
                                        data.saved === false
                                    ) {


                                        const card =
                                            this.closest(
                                                ".saved-post-card"
                                            );


                                        if (card) {

                                            card.style.opacity =
                                                "0";


                                            card.style.transform =
                                                "scale(.97)";


                                            card.style.transition =
                                                "opacity .2s ease, transform .2s ease";


                                            setTimeout(
                                                function() {

                                                    card.remove();


                                                    /*
                                                     * Re-read the
                                                     * remaining cards.
                                                     */

                                                    updateSavedPage();

                                                },
                                                220
                                            );

                                        }

                                    }


                                } catch (error) {


                                    console.error(
                                        "Remove saved post error:",
                                        error
                                    );


                                    this.innerHTML =
                                        previousHTML;


                                    this.title =
                                        previousTitle;


                                    this.setAttribute(
                                        "aria-label",
                                        previousAriaLabel
                                    );


                                    this.disabled =
                                        false;


                                    alert(
                                        error.message ||
                                        "Unable to remove saved post."
                                    );


                                    return;

                                }


                                this.disabled =
                                    false;

                            }
                        );

                    }
                );


                /* =================================================
                   UPDATE PAGE AFTER REMOVAL
                ================================================== */

                function updateSavedPage() {


                    const remainingCards =
                        document.querySelectorAll(
                            ".saved-post-card"
                        );


                    /* =============================================
                       NO POSTS REMAIN
                    ============================================== */

                    if (
                        remainingCards.length === 0
                    ) {


                        window.location.reload();


                        return;

                    }


                    /* =============================================
                       UPDATE COUNT
                    ============================================== */

                    filterSavedPosts();

                }


                /* =================================================
                   INITIALIZE
                ================================================== */

                filterSavedPosts();

            }
        );
    </script>


    <!-- =========================================================
         GLOBAL SCRIPTS
    ========================================================== -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>
