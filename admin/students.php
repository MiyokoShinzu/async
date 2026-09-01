
<?php include 'globals/checks.php'; ?>


<!DOCTYPE html>

<html lang="en">


<?php include 'globals/head.php'; ?>


<head>

    <style>
        /* =========================================================
           STUDENT PAGE
        ========================================================= */

        .content-wrapper {
            width: 100%;
            max-width: 100%;
        }


        /* =========================================================
           PAGE HEADER
        ========================================================= */

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h2 {
            font-weight: 700;
            color: #083B66;
            margin-bottom: 5px;
        }

        .page-header p {
            color: #6C757D;
            margin-bottom: 0;
        }


        /* =========================================================
           FILTER CARD
        ========================================================= */

        .filter-card {
            background: #FFFFFF;
            border: 1px solid #DEE2E6;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .04);
        }


        .filter-card .form-label {
            font-weight: 600;
            color: #495057;
            font-size: 14px;
        }


        .filter-card .form-control,
        .filter-card .form-select {
            min-height: 44px;
            border-radius: 8px;
        }


        .filter-card .form-control:focus,
        .filter-card .form-select:focus {
            border-color: #0B4F8A;
            box-shadow: 0 0 0 .2rem rgba(11, 79, 138, .12);
        }


        /* =========================================================
           TABLE CARD
        ========================================================= */

        .table-card {
            background: #FFFFFF;
            border: 1px solid #DEE2E6;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, .04);
        }


        /* =========================================================
           TABLE HEADER
        ========================================================= */

        .table-header {
            min-height: 65px;
            padding: 15px 20px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            border-bottom: 1px solid #DEE2E6;
        }


        .table-header-title {
            font-size: 16px;
            font-weight: 700;
            color: #083B66;

            display: flex;
            align-items: center;
        }


        .table-header .badge {
            font-size: 12px;
            padding: 7px 10px;
        }


        /* =========================================================
           TABLE
        ========================================================= */

        .student-table {
            margin-bottom: 0;
            vertical-align: middle;
            min-width: 1050px;
        }


        .student-table thead th {
            background: #F7F9FB;
            color: #495057;
            font-size: 13px;
            font-weight: 700;

            white-space: nowrap;

            padding: 14px 15px;
            border-bottom: 1px solid #DEE2E6;
        }


        .student-table tbody td {
            padding: 14px 15px;
            font-size: 14px;
            color: #495057;
        }


        .student-table tbody tr {
            transition:
                background .2s ease,
                transform .2s ease;
        }


        .student-table tbody tr:hover {
            background: #F8FBFE;
        }


        /* =========================================================
           STUDENT PROFILE
        ========================================================= */

        .student-profile {
            display: flex;
            align-items: center;
            gap: 12px;

            min-width: 220px;
        }


        .student-photo {
            width: 45px;
            height: 45px;

            min-width: 45px;

            object-fit: cover;

            border-radius: 50%;

            border: 2px solid #EAF3FA;

            background: #EAF3FA;

            transition:
                transform .3s ease,
                box-shadow .3s ease;
        }


        .student-table tbody tr:hover .student-photo {
            transform: scale(1.08);

            box-shadow:
                0 4px 12px rgba(11, 79, 138, .18);
        }


        .student-info {
            min-width: 0;
        }


        .student-name {
            color: #083B66;
            font-weight: 600;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }


        .student-username {
            color: #6C757D;
            font-size: 12px;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }


        /* =========================================================
           STUDENT ID
        ========================================================= */

        .student-id {
            font-weight: 600;
            color: #0B4F8A;
            white-space: nowrap;
        }


        /* =========================================================
           EMAIL
        ========================================================= */

        .student-email {
            max-width: 220px;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }


        /* =========================================================
           DEPARTMENT
        ========================================================= */

        .department-text {
            white-space: nowrap;
        }


        /* =========================================================
           YEAR / SECTION
        ========================================================= */

        .year-section-badge {
            display: inline-flex;

            align-items: center;
            justify-content: center;

            background: #F7F9FB;

            border: 1px solid #DEE2E6;

            color: #495057;

            padding: 5px 9px;

            border-radius: 6px;

            font-size: 12px;

            font-weight: 600;

            white-space: nowrap;
        }


        /* =========================================================
           ACTION BUTTON
        ========================================================= */

        .student-action {
            white-space: nowrap;
        }


        .student-action .btn {
            border-radius: 7px;
            font-weight: 600;

            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }


        .student-action .btn:hover {
            transform: translateY(-2px);

            box-shadow:
                0 4px 10px rgba(11, 79, 138, .12);
        }


        /* =========================================================
           TABLE RESPONSIVE CONTAINER
        ========================================================= */

        .student-table-wrapper {
            width: 100%;
            overflow-x: auto;

            scrollbar-width: thin;
        }


        .student-table-wrapper::-webkit-scrollbar {
            height: 7px;
        }


        .student-table-wrapper::-webkit-scrollbar-track {
            background: #F1F3F5;
        }


        .student-table-wrapper::-webkit-scrollbar-thumb {
            background: #B8C7D3;
            border-radius: 10px;
        }


        /* =========================================================
           EMPTY STATE
        ========================================================= */

        .empty-state {
            text-align: center;
            padding: 70px 20px;
            color: #6C757D;
        }


        .empty-state i {
            display: block;

            font-size: 48px;

            color: #B8C7D3;

            margin-bottom: 15px;
        }


        .empty-state h5 {
            color: #495057;
            font-weight: 700;
        }


        /* =========================================================
           PAGINATION
        ========================================================= */

        .pagination {
            flex-wrap: wrap;
            gap: 3px;
        }


        .pagination .page-link {
            border-radius: 6px !important;
            margin: 1px;

            color: #0B4F8A;
        }


        .pagination .page-item.active .page-link {
            background: #0B4F8A;
            border-color: #0B4F8A;
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 768px) {

            .main-content {
                width: 100%;
            }


            .content-wrapper {
                padding-left: 12px;
                padding-right: 12px;
            }


            .page-header {
                margin-bottom: 18px;
            }


            .page-header h2 {
                font-size: 24px;
            }


            .filter-card {
                padding: 15px;
                border-radius: 10px;
            }


            .table-header {
                padding: 13px 15px;
            }


            .table-header-title {
                font-size: 14px;
            }


            .student-table {
                min-width: 950px;
            }


            .student-table thead th,
            .student-table tbody td {
                padding: 11px 12px;
                font-size: 13px;
            }


            .student-photo {
                width: 40px;
                height: 40px;
                min-width: 40px;
            }


            .student-profile {
                gap: 9px;
            }


            .student-name {
                max-width: 180px;
            }


            .student-email {
                max-width: 180px;
            }


            .pagination {
                justify-content: center !important;
            }

        }


        /* =========================================================
           VERY SMALL SCREENS
        ========================================================= */

        @media (max-width: 480px) {

            .content-wrapper {
                padding-left: 8px;
                padding-right: 8px;
            }


            .filter-card {
                padding: 12px;
            }


            .table-header {
                padding: 12px;
            }


            .table-header-title {
                font-size: 13px;
            }


            .student-table {
                min-width: 900px;
            }

        }


        /* =========================================================
           REDUCED MOTION
        ========================================================= */

        @media (prefers-reduced-motion: reduce) {

            * {
                transition: none !important;
                animation: none !important;
            }

        }
    </style>

</head>


<body>


    <!-- =========================================================
         SIDEBAR
    ========================================================= -->

    <?php include 'globals/sidebar.php'; ?>


    <!-- =========================================================
         TOPBAR
    ========================================================= -->

    <?php include 'globals/topbar.php'; ?>


    <!-- =========================================================
         MAIN
    ========================================================= -->

    <main class="main-content">


        <div class="content-wrapper">


            <!-- =====================================================
                 PAGE HEADER
            ====================================================== -->

            <div class="page-header">

                <h2>
                    Students
                </h2>

                <p>
                    View and manage registered students.
                </p>

            </div>


            <!-- =====================================================
                 FILTERS
            ====================================================== -->

            <div class="filter-card">

                <form
                    method="GET"
                    action="students.php">


                    <div class="row g-3">


                        <!-- SEARCH -->

                        <div class="col-lg-4">

                            <label
                                class="form-label">

                                Search

                            </label>


                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Name, student ID, email..."
                                value="<?= htmlspecialchars($search) ?>">

                        </div>


                        <!-- DEPARTMENT -->

                        <div class="col-md-4 col-lg-2">

                            <label
                                class="form-label">

                                Department

                            </label>


                            <select
                                name="department"
                                class="form-select">


                                <option value="">

                                    All Departments

                                </option>


                                <?php foreach ($departments as $dept): ?>

                                    <option
                                        value="<?= htmlspecialchars($dept) ?>"
                                        <?= $department === $dept ? "selected" : "" ?>>

                                        <?= htmlspecialchars($dept) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>


                        <!-- YEAR -->

                        <div class="col-md-4 col-lg-2">

                            <label
                                class="form-label">

                                Year

                            </label>


                            <select
                                name="year"
                                class="form-select">


                                <option value="">

                                    All Years

                                </option>


                                <?php foreach ($years as $itemYear): ?>

                                    <option
                                        value="<?= htmlspecialchars($itemYear) ?>"
                                        <?= $year === $itemYear ? "selected" : "" ?>>

                                        <?= htmlspecialchars($itemYear) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>


                        <!-- SECTION -->

                        <div class="col-md-4 col-lg-2">

                            <label
                                class="form-label">

                                Section

                            </label>


                            <select
                                name="section"
                                class="form-select">


                                <option value="">

                                    All Sections

                                </option>


                                <?php foreach ($sections as $itemSection): ?>

                                    <option
                                        value="<?= htmlspecialchars($itemSection) ?>"
                                        <?= $section === $itemSection ? "selected" : "" ?>>

                                        <?= htmlspecialchars($itemSection) ?>

                                    </option>

                                <?php endforeach; ?>


                            </select>

                        </div>


                        <!-- BUTTONS -->

                        <div
                            class="col-md-12 col-lg-2 d-flex align-items-end gap-2">


                            <button
                                type="submit"
                                class="btn btn-primary">

                                <i class="bi bi-funnel me-1"></i>

                                Filter

                            </button>


                            <a
                                href="students.php"
                                class="btn btn-outline-secondary">

                                <i class="bi bi-arrow-counterclockwise"></i>

                            </a>


                        </div>


                    </div>


                </form>

            </div>


            <!-- =====================================================
                 STUDENT TABLE
            ====================================================== -->

            <div class="table-card">


                <!-- TABLE HEADER -->

                <div class="table-header">


                    <div class="table-header-title">

                        <i
                            class="bi bi-people me-2 text-primary">
                        </i>

                        Student Records

                    </div>


                    <span
                        class="badge text-bg-primary">

                        <?= number_format($totalStudents) ?>

                    </span>


                </div>


                <?php if ($students->num_rows > 0): ?>


                    <!-- =================================================
                         RESPONSIVE TABLE WRAPPER
                    ================================================== -->

                    <div class="student-table-wrapper">


                        <table
                            class="table table-hover student-table">


                            <thead>

                                <tr>


                                    <th>
                                        #
                                    </th>


                                    <th>
                                        Student ID
                                    </th>


                                    <th>
                                        Student
                                    </th>


                                    <th>
                                        Department
                                    </th>


                                    <th>
                                        Year / Section
                                    </th>


                                    <th>
                                        Email
                                    </th>


                                    <th>
                                        Created
                                    </th>


                                    <th class="text-center">
                                        Action
                                    </th>


                                </tr>


                            </thead>


                            <tbody>


                                <?php

                                $number =
                                    $offset + 1;

                                ?>


                                <?php while ($student = $students->fetch_assoc()): ?>


                                    <?php

                                    /*
                                     * BUILD FULL NAME
                                     */

                                    $studentName =
                                        trim(

                                            $student["first_name"] .
                                                " " .

                                                (
                                                    !empty($student["middle_initial"])
                                                    ? $student["middle_initial"] . ". "
                                                    : ""
                                                ) .

                                                $student["last_name"] .

                                                (
                                                    !empty($student["extension_name"])
                                                    ? " " .
                                                    $student["extension_name"]
                                                    : ""
                                                )

                                        );


                                    /*
                                     * PROFILE PHOTO
                                     *
                                     * Change "profile_photo"
                                     * if your database uses
                                     * another column name.
                                     */

                                    $profilePhoto =
                                        !empty($student["profile_photo"])
                                        ? $student["profile_photo"]
                                        : "./assets/img/default-avatar.png";

                                    ?>


                                    <tr>


                                        <!-- NUMBER -->

                                        <td>

                                            <?= $number++ ?>

                                        </td>


                                        <!-- STUDENT ID -->

                                        <td>

                                            <span
                                                class="student-id">

                                                <?= htmlspecialchars(
                                                    $student["student_id"]
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- STUDENT PROFILE -->

                                        <td>


                                            <div
                                                class="student-profile">


                                                <img
                                                    src="<?= htmlspecialchars($profilePhoto) ?>"
                                                    alt="<?= htmlspecialchars($studentName) ?>"
                                                    class="student-photo"
                                                    loading="lazy"
                                                    onerror="this.onerror=null;this.src='./assets/img/default-avatar.png';">


                                                <div
                                                    class="student-info">


                                                    <div
                                                        class="student-name"
                                                        title="<?= htmlspecialchars($studentName) ?>">

                                                        <?= htmlspecialchars(
                                                            $studentName
                                                        ) ?>

                                                    </div>


                                                    <?php if (!empty($student["username"])): ?>

                                                        <div
                                                            class="student-username">

                                                            @<?= htmlspecialchars(
                                                                    $student["username"]
                                                                ) ?>

                                                        </div>

                                                    <?php endif; ?>


                                                </div>


                                            </div>


                                        </td>


                                        <!-- DEPARTMENT -->

                                        <td>

                                            <span
                                                class="department-text">

                                                <?= htmlspecialchars(
                                                    $student["department"]
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- YEAR SECTION -->

                                        <td>

                                            <span
                                                class="year-section-badge">

                                                <?= htmlspecialchars(
                                                    $student["year_section"]
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- EMAIL -->

                                        <td>

                                            <div
                                                class="student-email"
                                                title="<?= htmlspecialchars($student["email"]) ?>">

                                                <?= htmlspecialchars(
                                                    $student["email"]
                                                ) ?>

                                            </div>

                                        </td>


                                        <!-- CREATED -->

                                        <td>

                                            <?php

                                            if (
                                                !empty($student["created_at"])
                                            ) {

                                                echo htmlspecialchars(

                                                    date(
                                                        "M d, Y",
                                                        strtotime(
                                                            $student["created_at"]
                                                        )
                                                    )

                                                );
                                            }

                                            ?>

                                        </td>


                                        <!-- ACTION -->

                                        <td
                                            class="text-center student-action">


                                            <a
                                                href="student_view.php?id=<?= (int)$student["id"] ?>"
                                                class="btn btn-sm btn-outline-primary">

                                                <i
                                                    class="bi bi-eye me-1">
                                                </i>

                                                View

                                            </a>


                                        </td>


                                    </tr>


                                <?php endwhile; ?>


                            </tbody>


                        </table>


                    </div>


                <?php else: ?>


                    <!-- EMPTY STATE -->

                    <div class="empty-state">


                        <i
                            class="bi bi-people">
                        </i>


                        <h5>

                            No students found

                        </h5>


                        <p class="mb-0">

                            No student records match
                            your current filters.

                        </p>


                    </div>


                <?php endif; ?>


                <!-- =================================================
                     PAGINATION
                ================================================== -->

                <?php if ($totalPages > 1): ?>


                    <div
                        class="border-top p-3">


                        <nav
                            aria-label="Student pagination">


                            <ul
                                class="pagination justify-content-end mb-0">


                                <!-- PREVIOUS -->

                                <li
                                    class="page-item
                                    <?= $page <= 1 ? "disabled" : "" ?>">


                                    <a
                                        class="page-link"
                                        href="<?= $page > 1
                                                    ? htmlspecialchars(
                                                        buildPageUrl($page - 1)
                                                    )
                                                    : "#"
                                                ?>">

                                        <i
                                            class="bi bi-chevron-left">
                                        </i>

                                        <span class="d-none d-sm-inline">
                                            Previous
                                        </span>

                                    </a>


                                </li>


                                <!-- PAGE NUMBERS -->

                                <?php

                                $startPage =
                                    max(
                                        1,
                                        $page - 2
                                    );


                                $endPage =
                                    min(
                                        $totalPages,
                                        $page + 2
                                    );

                                ?>


                                <?php for (
                                    $i = $startPage;
                                    $i <= $endPage;
                                    $i++
                                ): ?>


                                    <li
                                        class="page-item
                                        <?= $i === $page
                                            ? "active"
                                            : ""
                                        ?>">


                                        <a
                                            class="page-link"
                                            href="<?= htmlspecialchars(
                                                        buildPageUrl($i)
                                                    ) ?>">

                                            <?= $i ?>

                                        </a>


                                    </li>


                                <?php endfor; ?>


                                <!-- NEXT -->

                                <li
                                    class="page-item
                                    <?= $page >= $totalPages
                                        ? "disabled"
                                        : ""
                                    ?>">


                                    <a
                                        class="page-link"
                                        href="<?= $page < $totalPages
                                                    ? htmlspecialchars(
                                                        buildPageUrl($page + 1)
                                                    )
                                                    : "#"
                                                ?>">

                                        <span class="d-none d-sm-inline">
                                            Next
                                        </span>

                                        <i
                                            class="bi bi-chevron-right">
                                        </i>

                                    </a>


                                </li>


                            </ul>


                        </nav>


                    </div>


                <?php endif; ?>


            </div>


        </div>


    </main>


    <!-- =========================================================
         JAVASCRIPT
    ========================================================= -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>
