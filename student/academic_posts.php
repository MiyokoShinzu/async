<?php

/* =========================================================
   STUDENT ACADEMIC POSTS
   ETS-Async Learning Portal

   Features:
   - Database-driven academic posts
   - Search posts
   - Filter by subject
   - Filter by department
   - Combined subject + department filtering
   - Database-driven title
   - Database-driven description
   - Database-driven URL
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


$user = $_SESSION["user"];


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
    $user["student_id"] ?? "";

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
   LOAD POST SUBJECTS
========================================================== */

$subjects = [];


$subjectSql = "

    SELECT DISTINCT
        subject

    FROM academic_posts

    WHERE status = 1

      AND subject IS NOT NULL

      AND subject <> ''

    ORDER BY
        subject ASC
";


$subjectResult =
    $mysqli->query($subjectSql);


if ($subjectResult) {

    while (
        $row =
        $subjectResult->fetch_assoc()
    ) {

        $subjects[] =
            $row["subject"];
    }

    $subjectResult->free();
}


/* =========================================================
   LOAD POST DEPARTMENTS
========================================================== */

$departments = [];


$departmentSql = "

    SELECT DISTINCT
        department

    FROM academic_posts

    WHERE status = 1

      AND department IS NOT NULL

      AND department <> ''

    ORDER BY
        department ASC
";


$departmentResult =
    $mysqli->query($departmentSql);


if ($departmentResult) {

    while (
        $row =
        $departmentResult->fetch_assoc()
    ) {

        $departments[] =
            $row["department"];
    }

    $departmentResult->free();
}


/* =========================================================
   LOAD ACADEMIC POSTS
========================================================== */

$posts = [];


$postSql = "

    SELECT

        id,

        title,

        description,

        url,

        subject,

        department,

        icon,

        icon_color

    FROM academic_posts

    WHERE status = 1

    ORDER BY

        sort_order ASC,

        department ASC,

        subject ASC,

        title ASC
";


$postResult =
    $mysqli->query($postSql);


if ($postResult) {

    while (
        $row =
        $postResult->fetch_assoc()
    ) {

        $posts[] =
            $row;
    }

    $postResult->free();
}


/* =========================================================
   SUBJECT FILTER VALUE
========================================================== */

function subjectFilterValue($subject)
{

    $subject =
        trim($subject);


    $subject =
        strtolower($subject);


    $subject =
        preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $subject
        );


    return trim(
        $subject,
        "-"
    );
}


/* =========================================================
   DEPARTMENT FILTER VALUE
========================================================== */

function departmentFilterValue($department)
{

    $department =
        trim($department);


    $department =
        strtolower($department);


    $department =
        preg_replace(
            '/[^a-z0-9]+/',
            '-',
            $department
        );


    return trim(
        $department,
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
                        Academic Posts
                    </h2>

                    <p>
                        Browse academic references,
                        learning materials, and technical
                        resources for your coursework.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 SEARCH AND FILTER
            ================================================== -->

            <div class="posts-controls mb-4">


                <!-- =================================================
                     SEARCH
                ================================================== -->

                <div class="posts-search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        id="postSearch"
                        placeholder="Search posts..."
                        autocomplete="off">

                </div>


                <!-- =================================================
                     SUBJECT FILTER
                ================================================== -->

                <div class="filter-group">

                    <span class="filter-label">

                        Subject

                    </span>


                    <div
                        class="posts-filters"
                        id="subjectFilters">


                        <!-- ALL SUBJECTS -->

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
                                subjectFilterValue(
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


                <!-- =================================================
                     DEPARTMENT FILTER
                ================================================== -->

                <div class="filter-group">

                    <span class="filter-label">

                        Department

                    </span>


                    <div
                        class="posts-filters"
                        id="departmentFilters">


                        <!-- ALL DEPARTMENTS -->

                        <button
                            type="button"
                            class="post-filter active"
                            data-department="all">

                            All Departments

                        </button>


                        <?php foreach (
                            $departments
                            as $departmentItem
                        ): ?>


                            <?php

                            $departmentValue =
                                departmentFilterValue(
                                    $departmentItem
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
                                    $departmentItem,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </button>


                        <?php endforeach; ?>


                    </div>

                </div>


            </div>


            <!-- =================================================
                 POST COUNT
            ================================================== -->

            <div class="posts-results-header">

                <span id="postCount">

                    0 posts found

                </span>

            </div>


            <!-- =================================================
                 POST GRID
            ================================================== -->

            <div
                class="posts-grid"
                id="postsGrid">


                <?php if (
                    count($posts) > 0
                ): ?>


                    <?php foreach (
                        $posts
                        as $post
                    ): ?>


                        <?php


                        /* -----------------------------------------
                           DATABASE VALUES
                        ----------------------------------------- */

                        $postId =
                            (int) (
                                $post["id"]
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


                        $url =
                            trim(
                                $post["url"]
                                    ?? "#"
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
                           ICON COLOR VALIDATION
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
                           URL VALIDATION
                        ----------------------------------------- */

                        if (
                            $url === ""
                        ) {

                            $url =
                                "#";
                        }


                        /* -----------------------------------------
                           FILTER VALUES
                        ----------------------------------------- */

                        $subjectValue =
                            subjectFilterValue(
                                $subject
                            );


                        $departmentValue =
                            departmentFilterValue(
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

                        ?>


                        <!-- =================================================
                             POST CARD
                        ================================================== -->

                        <a
                            href="academic_post_start.php?id=<?= (int)$postId ?>"
                            class="academic-post-card"

                            data-id="<?= $postId ?>"

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
                                            ) ?>"

                            target="_blank"

                            rel="noopener noreferrer">


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


                                    <!-- SUBJECT -->

                                    <span class="post-subject">

                                        <?= htmlspecialchars(
                                            $subject,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>

                                    </span>


                                    <!-- DEPARTMENT -->

                                    <span class="post-department">

                                        <?= htmlspecialchars(
                                            $department,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>

                                    </span>


                                </div>


                            </div>


                        </a>


                    <?php endforeach; ?>


                <?php endif; ?>


            </div>


            <!-- =================================================
                 EMPTY STATE
            ================================================== -->

            <div
                id="postEmpty"
                class="posts-empty"
                style="display:none;">


                <div class="posts-empty-icon">

                    <i class="bi bi-search"></i>

                </div>


                <h5>

                    No posts found

                </h5>


                <p>

                    Try a different search term or
                    select another subject or department.

                </p>


            </div>


        </div>


    </main>


    <!-- =========================================================
         ACADEMIC POSTS STYLES
    ========================================================== -->

    <style>
        /* =========================================================
           POSTS THEME VARIABLES
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

            transition:
                background .2s ease,
                border-color .2s ease;

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

            transition:
                background .2s ease,
                border-color .2s ease,
                color .2s ease,
                box-shadow .2s ease;

        }


        .posts-search input::placeholder {

            color:
                var(--posts-muted);

            opacity:
                .8;

        }


        .posts-search input:focus {

            border-color:
                var(--posts-search-focus);

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, .10);

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
           FILTERS
        ========================================================== */

        .posts-filters {

            display:
                flex;

            flex-wrap:
                wrap;

            gap:
                8px;

        }


        /* =========================================================
           FILTER BUTTON
        ========================================================== */

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

            text-decoration:
                none;

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
           DARK MODE ICONS
        ========================================================== */

        html[data-theme="dark"] .post-icon.blue {

            color:
                #60a5fa;

            background:
                rgba(37, 99, 235, .16);

        }


        html[data-theme="dark"] .post-icon.purple {

            color:
                #a78bfa;

            background:
                rgba(124, 58, 237, .16);

        }


        html[data-theme="dark"] .post-icon.orange {

            color:
                #fb923c;

            background:
                rgba(234, 88, 12, .16);

        }


        html[data-theme="dark"] .post-icon.green {

            color:
                #4ade80;

            background:
                rgba(22, 163, 74, .16);

        }


        html[data-theme="dark"] .post-icon.red {

            color:
                #f87171;

            background:
                rgba(220, 38, 38, .16);

        }


        html[data-theme="dark"] .post-icon.yellow {

            color:
                #facc15;

            background:
                rgba(202, 138, 4, .16);

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


        /* =========================================================
           SUBJECT TAG
        ========================================================== */

        .post-subject {

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
           DEPARTMENT TAG
        ========================================================== */

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
                45px;

            height:
                45px;

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

        }


        .posts-empty h5 {

            margin:
                0 0 5px;

            font-size:
                .95rem;

            color:
                var(--posts-text);

        }


        .posts-empty p {

            margin:
                0;

            color:
                var(--posts-muted);

            font-size:
                .8rem;

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
         SEARCH / FILTER JAVASCRIPT
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
                        "postSearch"
                    );


                const subjectButtons =
                    document.querySelectorAll(
                        "#subjectFilters .post-filter"
                    );


                const departmentButtons =
                    document.querySelectorAll(
                        "#departmentFilters .post-filter"
                    );


                const postCards =
                    document.querySelectorAll(
                        ".academic-post-card"
                    );


                const postCount =
                    document.getElementById(
                        "postCount"
                    );


                const emptyState =
                    document.getElementById(
                        "postEmpty"
                    );


                /* =================================================
                   CURRENT FILTER VALUES
                ================================================== */

                let selectedSubject =
                    "all";


                let selectedDepartment =
                    "all";


                /* =================================================
                   FILTER POSTS
                ================================================== */

                function filterPosts() {


                    const searchTerm =
                        searchInput.value
                        .toLowerCase()
                        .trim();


                    let visibleCount =
                        0;


                    /* =================================================
                       LOOP THROUGH POSTS
                    ================================================== */

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


                            /* =========================================
                               SUBJECT MATCH
                            ========================================== */

                            const subjectMatch =
                                selectedSubject === "all" ||
                                subject === selectedSubject;


                            /* =========================================
                               DEPARTMENT MATCH
                            ========================================== */

                            const departmentMatch =
                                selectedDepartment === "all" ||
                                department === selectedDepartment;


                            /* =========================================
                               SEARCH MATCH
                            ========================================== */

                            const searchMatch =
                                searchTerm === "" ||

                                searchData.includes(
                                    searchTerm
                                ) ||

                                cardText.includes(
                                    searchTerm
                                );


                            /* =========================================
                               FINAL MATCH
                            ========================================== */

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
                       RESULT COUNT
                    ================================================== */

                    postCount.textContent =

                        visibleCount +

                        (
                            visibleCount === 1 ?
                            " post found" :
                            " posts found"
                        );


                    /* =================================================
                       EMPTY STATE
                    ================================================== */

                    if (
                        visibleCount === 0
                    ) {

                        emptyState.style.display =
                            "block";

                    } else {

                        emptyState.style.display =
                            "none";

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


                                filterPosts();

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


                                filterPosts();

                            }
                        );

                    }
                );


                /* =================================================
                   SEARCH
                ================================================== */

                searchInput.addEventListener(
                    "input",
                    filterPosts
                );


                /* =================================================
                   INITIALIZE
                ================================================== */

                filterPosts();

            }
        );
    </script>


    <!-- =========================================================
         GLOBAL SCRIPTS
    ========================================================== -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>