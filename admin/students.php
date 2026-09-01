<?php include 'globals/checks.php'; ?>


<!DOCTYPE html>

<html lang="en">

<?php include 'globals/head.php'; ?>


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

                        <div class="col-md-12 col-lg-2 d-flex align-items-end gap-2">

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


                <div class="table-header">


                    <div class="table-header-title">

                        <i class="bi bi-people me-2 text-primary"></i>

                        Student Records

                    </div>


                    <span class="badge text-bg-primary">

                        <?= number_format($totalStudents) ?>

                    </span>


                </div>


                <?php if ($students->num_rows > 0): ?>


                    <div class="table-responsive">


                        <table
                            class="table table-hover student-table">


                            <thead
                                class="table-light">


                                <tr>

                                    <th>
                                        #
                                    </th>

                                    <th>
                                        Student ID
                                    </th>

                                    <th>
                                        Name
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

                                    <th
                                        class="text-center">

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

                                    $studentName =
                                        trim(

                                            $student["first_name"] .
                                                " " .

                                                (
                                                    !empty($student["middle_initial"])
                                                    ? $student["middle_initial"] .
                                                    ". "
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

                                    ?>


                                    <tr>


                                        <!-- NUMBER -->

                                        <td>

                                            <?= $number++ ?>

                                        </td>


                                        <!-- STUDENT ID -->

                                        <td>

                                            <span
                                                class="fw-semibold">

                                                <?= htmlspecialchars(
                                                    $student["student_id"]
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- NAME -->

                                        <td>

                                            <div
                                                class="student-name">

                                                <?= htmlspecialchars(
                                                    $studentName
                                                ) ?>

                                            </div>

                                        </td>


                                        <!-- DEPARTMENT -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $student["department"]
                                            ) ?>

                                        </td>


                                        <!-- YEAR SECTION -->

                                        <td>

                                            <span
                                                class="badge text-bg-light border">

                                                <?= htmlspecialchars(
                                                    $student["year_section"]
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- EMAIL -->

                                        <td>

                                            <?= htmlspecialchars(
                                                $student["email"]
                                            ) ?>

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
                                            class="text-center">

                                            <a
                                                href="student_view.php?id=<?= (int)$student["id"] ?>"
                                                class="btn btn-sm btn-outline-primary">

                                                <i class="bi bi-eye"></i>

                                                View

                                            </a>

                                        </td>


                                    </tr>


                                <?php endwhile; ?>


                            </tbody>


                        </table>


                    </div>


                <?php else: ?>


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

                                        Previous

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

                                        Next

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