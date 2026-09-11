<?php

include './globals/checks.php';


/* =========================================================
   ADMIN ACADEMIC POSTS
   ETS-Async Learning Portal

   Features:
   - View academic posts
   - Search academic posts
   - Filter by department
   - Filter by subject
   - Display post status
   - Display post URL
   - Create new academic post
   ========================================================= */


/* =========================================================
   GET ADMIN USER
========================================================== */

$user = $_SESSION["user"];

$adminId =
    $user["id"] ?? null;


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   LOAD DEPARTMENTS
========================================================== */

$departments = [];


$departmentSQL = "

    SELECT DISTINCT
        department

    FROM accounts

    WHERE access = 'student'

      AND department IS NOT NULL

      AND department != ''

    ORDER BY
        department ASC
";


$departmentResult =
    $mysqli->query(
        $departmentSQL
    );


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
   LOAD ACADEMIC POST SUBJECTS
========================================================== */

$subjects = [];


$subjectSQL = "

    SELECT DISTINCT
        subject

    FROM academic_posts

    WHERE subject IS NOT NULL

      AND subject != ''

    ORDER BY
        subject ASC
";


$subjectResult =
    $mysqli->query(
        $subjectSQL
    );


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
   LOAD ACADEMIC POSTS
========================================================== */

$posts = [];


$postSQL = "

    SELECT

        id,

        title,

        description,

        url,

        subject,

        department,

        icon,

        icon_color,

        sort_order,

        status,

        created_at,

        updated_at

    FROM academic_posts

    ORDER BY

        sort_order ASC,

        created_at DESC,

        title ASC
";


$postResult =
    $mysqli->query(
        $postSQL
    );


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
   TOTAL COUNTS
========================================================== */

$totalPosts =
    count($posts);


$activePosts =
    0;


$inactivePosts =
    0;


foreach (
    $posts
    as $post
) {

    if (
        (int)$post["status"] === 1
    ) {

        $activePosts++;
    } else {

        $inactivePosts++;
    }
}

?>


<!DOCTYPE html>

<html lang="en">


<?php include 'globals/head.php'; ?>


<style>
    /* =========================================================
   ADMIN ACADEMIC POSTS
   ETS-Async Learning Portal
   ========================================================= */


    /* =========================================================
   PAGE HEADER
========================================================== */

    .page-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

        margin-bottom: 25px;
    }


    .page-header h2 {

        margin: 0;

        font-size: 1.65rem;

        font-weight: 700;

        color: #1f2937;
    }


    .page-header p {

        margin: 6px 0 0;

        color: #6b7280;

        font-size: 0.92rem;
    }


    /* =========================================================
   CREATE BUTTON
========================================================== */

    .page-header .btn {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 5px;

        border-radius: 8px;

        font-size: 0.88rem;

        padding: 9px 15px;

        white-space: nowrap;
    }


    /* =========================================================
   STAT CARDS
========================================================== */

    .posts-stat-grid {

        display: grid;

        grid-template-columns:
            repeat(3, 1fr);

        gap: 15px;

        margin-bottom: 20px;
    }


    .posts-stat-card {

        display: flex;

        align-items: center;

        gap: 13px;

        padding: 18px;

        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 12px;

        box-shadow:
            0 2px 8px rgba(15, 23, 42, 0.03);
    }


    .posts-stat-icon {

        width: 42px;

        height: 42px;

        min-width: 42px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 10px;

        font-size: 1.1rem;
    }


    .posts-stat-icon.blue {

        color: #2563eb;

        background: #eff6ff;
    }


    .posts-stat-icon.green {

        color: #16a34a;

        background: #ecfdf3;
    }


    .posts-stat-icon.red {

        color: #dc2626;

        background: #fef2f2;
    }


    .posts-stat-content strong {

        display: block;

        color: #1f2937;

        font-size: 1.25rem;

        line-height: 1.2;
    }


    .posts-stat-content span {

        display: block;

        margin-top: 3px;

        color: #6b7280;

        font-size: 0.76rem;
    }


    /* =========================================================
   MAIN CARD
========================================================== */

    .posts-admin-card {

        background: #ffffff;

        border: 1px solid #e5e7eb;

        border-radius: 14px;

        box-shadow:
            0 2px 8px rgba(15, 23, 42, 0.04);

        overflow: hidden;
    }


    /* =========================================================
   CARD HEADER
========================================================== */

    .posts-admin-header {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

        padding: 20px 22px;

        border-bottom: 1px solid #e5e7eb;
    }


    .posts-admin-header h5 {

        margin: 0;

        font-size: 1rem;

        font-weight: 700;

        color: #1f2937;
    }


    .posts-admin-header p {

        margin: 4px 0 0;

        color: #6b7280;

        font-size: 0.8rem;
    }


    /* =========================================================
   FILTER AREA
========================================================== */

    .posts-admin-filters {

        display: grid;

        grid-template-columns:
            1fr 220px 220px;

        gap: 10px;

        padding: 18px 22px;

        background: #f9fafb;

        border-bottom: 1px solid #e5e7eb;
    }


    /* =========================================================
   SEARCH
========================================================== */

    .posts-search {

        position: relative;
    }


    .posts-search i {

        position: absolute;

        left: 13px;

        top: 50%;

        transform:
            translateY(-50%);

        color: #9ca3af;
    }


    .posts-search input {

        width: 100%;

        height: 42px;

        padding:
            0 12px 0 38px;

        border:
            1px solid #d1d5db;

        border-radius: 8px;

        background: #ffffff;

        color: #1f2937;

        font-size: 0.85rem;

        outline: none;
    }


    .posts-search input:focus {

        border-color: #6366f1;

        box-shadow:
            0 0 0 3px rgba(99, 102, 241, 0.10);
    }


    /* =========================================================
   FILTER SELECT
========================================================== */

    .posts-admin-filters .form-select {

        height: 42px;

        border:
            1px solid #d1d5db;

        border-radius: 8px;

        color: #374151;

        background-color: #ffffff;

        font-size: 0.82rem;
    }


    .posts-admin-filters .form-select:focus {

        border-color: #6366f1;

        box-shadow:
            0 0 0 3px rgba(99, 102, 241, 0.10);
    }


    /* =========================================================
   TABLE WRAPPER
========================================================== */

    .posts-table-wrapper {

        width: 100%;

        overflow-x: auto;
    }


    /* =========================================================
   TABLE
========================================================== */

    .posts-table {

        width: 100%;

        border-collapse: collapse;

        min-width: 950px;
    }


    .posts-table thead th {

        padding:
            13px 16px;

        background: #f9fafb;

        border-bottom:
            1px solid #e5e7eb;

        color: #6b7280;

        font-size: 0.7rem;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: .04em;

        white-space: nowrap;
    }


    .posts-table tbody td {

        padding:
            15px 16px;

        border-bottom:
            1px solid #f0f1f3;

        vertical-align: middle;

        color: #374151;

        font-size: 0.82rem;
    }


    .posts-table tbody tr:last-child td {

        border-bottom: none;
    }


    .posts-table tbody tr:hover {

        background: #fafbfc;
    }


    /* =========================================================
   POST TITLE
========================================================== */

    .post-title-cell {

        min-width: 250px;
    }


    .post-title {

        display: block;

        color: #1f2937;

        font-weight: 700;

        font-size: 0.84rem;

        line-height: 1.35;
    }


    .post-description {

        display: block;

        max-width: 400px;

        margin-top: 4px;

        color: #9ca3af;

        font-size: 0.73rem;

        line-height: 1.45;

        display: -webkit-box;

        -webkit-line-clamp: 2;

        -webkit-box-orient: vertical;

        overflow: hidden;
    }


    /* =========================================================
   URL
========================================================== */

    .post-url {

        display: inline-flex;

        align-items: center;

        gap: 5px;

        max-width: 220px;

        color: #4f46e5;

        text-decoration: none;

        font-size: 0.75rem;

        overflow: hidden;

        text-overflow: ellipsis;

        white-space: nowrap;
    }


    .post-url:hover {

        color: #4338ca;

        text-decoration: underline;
    }


    /* =========================================================
   TAGS
========================================================== */

    .post-tag {

        display: inline-block;

        padding:
            4px 8px;

        border-radius: 5px;

        background: #f3f4f6;

        color: #4b5563;

        font-size: 0.68rem;

        font-weight: 600;

        white-space: nowrap;
    }


    /* =========================================================
   STATUS
========================================================== */

    .post-status {

        display: inline-flex;

        align-items: center;

        gap: 5px;

        padding:
            4px 8px;

        border-radius: 20px;

        font-size: 0.68rem;

        font-weight: 700;
    }


    .post-status.active {

        color: #15803d;

        background: #ecfdf3;
    }


    .post-status.inactive {

        color: #b91c1c;

        background: #fef2f2;
    }


    .post-status-dot {

        width: 6px;

        height: 6px;

        border-radius: 50%;

        background: currentColor;
    }


    /* =========================================================
   ICON PREVIEW
========================================================== */

    .post-icon-preview {

        width: 36px;

        height: 36px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 8px;

        font-size: 0.95rem;
    }


    .post-icon-preview.blue {

        color: #2563eb;

        background: #eff6ff;
    }


    .post-icon-preview.purple {

        color: #7c3aed;

        background: #f5f3ff;
    }


    .post-icon-preview.orange {

        color: #ea580c;

        background: #fff7ed;
    }


    .post-icon-preview.green {

        color: #16a34a;

        background: #ecfdf3;
    }


    .post-icon-preview.red {

        color: #dc2626;

        background: #fef2f2;
    }


    .post-icon-preview.yellow {

        color: #ca8a04;

        background: #fefce8;
    }


    /* =========================================================
   EMPTY STATE
========================================================== */

    .posts-empty {

        padding: 60px 20px;

        text-align: center;

        display: none;
    }


    .posts-empty-icon {

        width: 50px;

        height: 50px;

        margin: 0 auto 14px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        background: #f3f4f6;

        color: #9ca3af;

        font-size: 1.1rem;
    }


    .posts-empty h5 {

        margin: 0 0 5px;

        color: #1f2937;

        font-size: 0.95rem;
    }


    .posts-empty p {

        margin: 0;

        color: #9ca3af;

        font-size: 0.78rem;
    }


    /* =========================================================
   DARK MODE
========================================================== */

    html[data-theme="dark"] .page-header h2,
    html[data-theme="dark"] .posts-stat-content strong,
    html[data-theme="dark"] .posts-admin-header h5,
    html[data-theme="dark"] .post-title,
    html[data-theme="dark"] .posts-empty h5 {

        color: #f1f5f9;
    }


    html[data-theme="dark"] .page-header p,
    html[data-theme="dark"] .posts-stat-content span,
    html[data-theme="dark"] .posts-admin-header p,
    html[data-theme="dark"] .post-description {

        color: #a8b0bf;
    }


    html[data-theme="dark"] .posts-stat-card,
    html[data-theme="dark"] .posts-admin-card {

        background: #151922;

        border-color: #2a3140;
    }


    html[data-theme="dark"] .posts-admin-filters,
    html[data-theme="dark"] .posts-table thead th {

        background: #1c2230;

        border-color: #2a3140;
    }


    html[data-theme="dark"] .posts-search input,
    html[data-theme="dark"] .posts-admin-filters .form-select {

        background: #151922;

        border-color: #3a4252;

        color: #f1f5f9;
    }


    html[data-theme="dark"] .posts-table tbody td {

        border-color: #2a3140;

        color: #c2c9d3;
    }


    html[data-theme="dark"] .posts-table tbody tr:hover {

        background: #1c2230;
    }


    html[data-theme="dark"] .post-tag {

        background: #252c38;

        color: #c2c9d3;
    }


    html[data-theme="dark"] .posts-empty-icon {

        background: #252c38;

        color: #a8b0bf;
    }


    /* =========================================================
   RESPONSIVE
========================================================== */

    @media (max-width: 900px) {

        .posts-stat-grid {

            grid-template-columns:
                repeat(3, 1fr);
        }


        .posts-admin-filters {

            grid-template-columns:
                1fr 1fr;
        }


        .posts-search {

            grid-column:
                1 / -1;
        }

    }


    @media (max-width: 650px) {

        .page-header {

            flex-direction: column;

            align-items: flex-start;
        }


        .page-header>div:last-child {

            width: 100%;
        }


        .page-header .btn {

            width: 100%;
        }


        .posts-stat-grid {

            grid-template-columns:
                1fr;
        }


        .posts-admin-filters {

            grid-template-columns:
                1fr;
        }

    }
</style>


<body>


    <!-- =========================================================
     SIDEBAR
========================================================== -->

    <?php include 'globals/sidebar.php'; ?>


    <!-- =========================================================
     TOPBAR
========================================================== -->

    <?php include 'globals/topbar.php'; ?>


    <!-- =========================================================
     MAIN CONTENT
========================================================== -->

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
                        Manage academic references, learning materials,
                        and technical resources available to students.
                    </p>

                </div>


                <div>

                    <a
                        href="academic_post_create.php"
                        class="btn btn-primary">

                        <i class="bi bi-plus-lg me-1"></i>

                        Create Academic Post

                    </a>

                </div>


            </div>


            <!-- =================================================
             STATISTICS
        ================================================== -->

            <div class="posts-stat-grid">


                <!-- TOTAL -->

                <div class="posts-stat-card">

                    <div class="posts-stat-icon blue">

                        <i class="bi bi-file-earmark-text"></i>

                    </div>


                    <div class="posts-stat-content">

                        <strong>
                            <?= $totalPosts ?>
                        </strong>

                        <span>
                            Total Posts
                        </span>

                    </div>

                </div>


                <!-- ACTIVE -->

                <div class="posts-stat-card">

                    <div class="posts-stat-icon green">

                        <i class="bi bi-check-circle"></i>

                    </div>


                    <div class="posts-stat-content">

                        <strong>
                            <?= $activePosts ?>
                        </strong>

                        <span>
                            Active Posts
                        </span>

                    </div>

                </div>


                <!-- INACTIVE -->

                <div class="posts-stat-card">

                    <div class="posts-stat-icon red">

                        <i class="bi bi-pause-circle"></i>

                    </div>


                    <div class="posts-stat-content">

                        <strong>
                            <?= $inactivePosts ?>
                        </strong>

                        <span>
                            Inactive Posts
                        </span>

                    </div>

                </div>


            </div>


            <!-- =================================================
             MAIN CARD
        ================================================== -->

            <div class="posts-admin-card">


                <!-- =================================================
                 HEADER
            ================================================== -->

                <div class="posts-admin-header">


                    <div>

                        <h5>
                            Academic Post Library
                        </h5>

                        <p>
                            View and search all academic posts.
                        </p>

                    </div>


                </div>


                <!-- =================================================
                 FILTERS
            ================================================== -->

                <div class="posts-admin-filters">


                    <!-- SEARCH -->

                    <div class="posts-search">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            id="postSearch"
                            placeholder="Search posts..."
                            autocomplete="off">

                    </div>


                    <!-- SUBJECT -->

                    <select
                        id="subjectFilter"
                        class="form-select">

                        <option value="all">
                            All Subjects
                        </option>


                        <?php foreach (
                            $subjects
                            as $itemSubject
                        ): ?>

                            <option
                                value="<?= htmlspecialchars(
                                            $itemSubject,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>">

                                <?= htmlspecialchars(
                                    $itemSubject,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>


                    <!-- DEPARTMENT -->

                    <select
                        id="departmentFilter"
                        class="form-select">

                        <option value="all">
                            All Departments
                        </option>


                        <?php foreach (
                            $departments
                            as $itemDepartment
                        ): ?>

                            <option
                                value="<?= htmlspecialchars(
                                            $itemDepartment,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>">

                                <?= htmlspecialchars(
                                    $itemDepartment,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>


                </div>


                <!-- =================================================
                 TABLE
            ================================================== -->

                <div class="posts-table-wrapper">


                    <table class="posts-table">


                        <thead>

                            <tr>

                                <th>
                                    Post
                                </th>

                                <th>
                                    Subject
                                </th>

                                <th>
                                    Department
                                </th>

                                <th>
                                    URL
                                </th>

                                <th>
                                    Icon
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Order
                                </th>

                            </tr>

                        </thead>


                        <tbody id="postsTableBody">


                            <?php foreach (
                                $posts
                                as $post
                            ): ?>


                                <?php

                                $postId =
                                    (int)(
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
                                            ?? ""
                                    );


                                $subject =
                                    trim(
                                        $post["subject"]
                                            ?? ""
                                    );


                                $department =
                                    trim(
                                        $post["department"]
                                            ?? ""
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


                                $sortOrder =
                                    (int)(
                                        $post["sort_order"]
                                        ?? 0
                                    );


                                $status =
                                    (int)(
                                        $post["status"]
                                        ?? 0
                                    );


                                $searchData =
                                    strtolower(

                                        $title .
                                            " " .

                                            $description .
                                            " " .

                                            $url .
                                            " " .

                                            $subject .
                                            " " .

                                            $department

                                    );

                                ?>


                                <tr
                                    class="post-row"

                                    data-search="<?= htmlspecialchars(
                                                        $searchData,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>"

                                    data-subject="<?= htmlspecialchars(
                                                        $subject,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>"

                                    data-department="<?= htmlspecialchars(
                                                            $department,
                                                            ENT_QUOTES,
                                                            "UTF-8"
                                                        ) ?>">


                                    <!-- =================================
                                     POST
                                ================================== -->

                                    <td class="post-title-cell">


                                        <span class="post-title">

                                            <?= htmlspecialchars(
                                                $title,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>

                                        </span>


                                        <?php if (
                                            $description !== ""
                                        ): ?>

                                            <span
                                                class="post-description">

                                                <?= htmlspecialchars(
                                                    $description,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>

                                            </span>

                                        <?php endif; ?>


                                    </td>


                                    <!-- =================================
                                     SUBJECT
                                ================================== -->

                                    <td>

                                        <?php if (
                                            $subject !== ""
                                        ): ?>

                                            <span class="post-tag">

                                                <?= htmlspecialchars(
                                                    $subject,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- =================================
                                     DEPARTMENT
                                ================================== -->

                                    <td>

                                        <?php if (
                                            $department !== ""
                                        ): ?>

                                            <span class="post-tag">

                                                <?= htmlspecialchars(
                                                    $department,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>

                                            </span>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- =================================
                                     URL
                                ================================== -->

                                    <td>


                                        <?php if (
                                            $url !== ""
                                        ): ?>

                                            <a
                                                href="<?= htmlspecialchars(
                                                            $url,
                                                            ENT_QUOTES,
                                                            "UTF-8"
                                                        ) ?>"

                                                class="post-url"

                                                target="_blank"

                                                rel="noopener noreferrer">

                                                <i class="bi bi-box-arrow-up-right"></i>

                                                Open

                                            </a>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>


                                    </td>


                                    <!-- =================================
                                     ICON
                                ================================== -->

                                    <td>


                                        <?php

                                        $allowedColors = [

                                            "blue",
                                            "purple",
                                            "orange",
                                            "green",
                                            "red",
                                            "yellow"

                                        ];


                                        if (
                                            !in_array(
                                                $iconColor,
                                                $allowedColors,
                                                true
                                            )
                                        ) {

                                            $iconColor =
                                                "blue";
                                        }

                                        ?>


                                        <div
                                            class="post-icon-preview <?= htmlspecialchars(
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


                                    </td>


                                    <!-- =================================
                                     STATUS
                                ================================== -->

                                    <td>


                                        <?php if (
                                            $status === 1
                                        ): ?>

                                            <span
                                                class="post-status active">

                                                <span
                                                    class="post-status-dot">
                                                </span>

                                                Active

                                            </span>

                                        <?php else: ?>

                                            <span
                                                class="post-status inactive">

                                                <span
                                                    class="post-status-dot">
                                                </span>

                                                Inactive

                                            </span>

                                        <?php endif; ?>


                                    </td>


                                    <!-- =================================
                                     SORT ORDER
                                ================================== -->

                                    <td>

                                        <?= $sortOrder ?>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>


                    </table>


                </div>


                <!-- =================================================
                 EMPTY STATE
            ================================================== -->

                <div
                    id="postsEmpty"
                    class="posts-empty">


                    <div class="posts-empty-icon">

                        <i class="bi bi-search"></i>

                    </div>


                    <h5>
                        No academic posts found
                    </h5>


                    <p>
                        Try changing your search term,
                        subject, or department filter.
                    </p>


                </div>


            </div>


        </div>


    </main>


    <!-- =========================================================
     JAVASCRIPT
========================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {


                /* =====================================================
                   ELEMENTS
                ====================================================== */

                const searchInput =
                    document.getElementById(
                        "postSearch"
                    );


                const subjectFilter =
                    document.getElementById(
                        "subjectFilter"
                    );


                const departmentFilter =
                    document.getElementById(
                        "departmentFilter"
                    );


                const rows =
                    document.querySelectorAll(
                        ".post-row"
                    );


                const emptyState =
                    document.getElementById(
                        "postsEmpty"
                    );


                /* =====================================================
                   FILTER FUNCTION
                ====================================================== */

                function filterPosts() {


                    const search =
                        searchInput.value
                        .toLowerCase()
                        .trim();


                    const subject =
                        subjectFilter.value
                        .toLowerCase();


                    const department =
                        departmentFilter.value
                        .toLowerCase();


                    let visible =
                        0;


                    rows.forEach(
                        function(row) {


                            const rowSearch =
                                (
                                    row.dataset.search ||
                                    ""
                                )
                                .toLowerCase();


                            const rowSubject =
                                (
                                    row.dataset.subject ||
                                    ""
                                )
                                .toLowerCase();


                            const rowDepartment =
                                (
                                    row.dataset.department ||
                                    ""
                                )
                                .toLowerCase();


                            const searchMatch =
                                search === "" ||
                                rowSearch.includes(
                                    search
                                );


                            const subjectMatch =
                                subject === "all" ||
                                rowSubject === subject;


                            const departmentMatch =
                                department === "all" ||
                                rowDepartment === department;


                            if (
                                searchMatch &&
                                subjectMatch &&
                                departmentMatch
                            ) {

                                row.style.display =
                                    "";

                                visible++;

                            } else {

                                row.style.display =
                                    "none";
                            }

                        }
                    );


                    if (
                        visible === 0
                    ) {

                        emptyState.style.display =
                            "block";

                    } else {

                        emptyState.style.display =
                            "none";
                    }

                }


                /* =====================================================
                   EVENTS
                ====================================================== */

                searchInput.addEventListener(
                    "input",
                    filterPosts
                );


                subjectFilter.addEventListener(
                    "change",
                    filterPosts
                );


                departmentFilter.addEventListener(
                    "change",
                    filterPosts
                );


                /* =====================================================
                   INITIALIZE
                ====================================================== */

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