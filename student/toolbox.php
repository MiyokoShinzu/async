<?php

/* =========================================================
   STUDENT ACADEMIC TOOLBOX
   ETS-Async Learning Portal
   ========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION
   ========================================================= */

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
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   USER DATA
   ========================================================= */

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

$department =
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
   ========================================================= */

$fullName = trim(
    $firstName . " " .
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
   ========================================================= */

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
   LOAD TOOLBOX SUBJECTS
   ========================================================= */

$subjects = [];

$subjectSql = "
    SELECT DISTINCT subject
    FROM toolbox
    WHERE status = 1
      AND subject IS NOT NULL
      AND subject <> ''
    ORDER BY subject ASC
";

$subjectResult = $mysqli->query($subjectSql);

if ($subjectResult) {

    while ($row = $subjectResult->fetch_assoc()) {

        $subjects[] = $row["subject"];
    }

    $subjectResult->free();
}


/* =========================================================
   LOAD TOOLBOX TOOLS
   ========================================================= */

$tools = [];

$toolSql = "
    SELECT
        id,
        title,
        description,
        url,
        subject,
        icon,
        icon_color
    FROM toolbox
    WHERE status = 1
    ORDER BY
        sort_order ASC,
        subject ASC,
        title ASC
";

$toolResult = $mysqli->query($toolSql);

if ($toolResult) {

    while ($row = $toolResult->fetch_assoc()) {

        $tools[] = $row;
    }

    $toolResult->free();
}


/* =========================================================
   SUBJECT FILTER VALUE
   ========================================================= */

function subjectFilterValue($subject)
{
    $subject = trim($subject);

    $subject = strtolower($subject);

    $subject = preg_replace(
        '/[^a-z0-9]+/',
        '-',
        $subject
    );

    return trim($subject, "-");
}


/* =========================================================
   ALLOWED ICON COLORS
   ========================================================= */

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
                        Academic Toolbox
                    </h2>

                    <p>
                        Search and access useful tools for your
                        academic and technical coursework.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 SEARCH AND FILTER
            ================================================== -->

            <div class="toolbox-controls mb-4">


                <!-- SEARCH -->

                <div class="toolbox-search">

                    <i class="bi bi-search"></i>

                    <input
                        type="text"
                        id="toolSearch"
                        placeholder="Search tools..."
                        autocomplete="off">

                </div>


                <!-- SUBJECT FILTER -->

                <div
                    class="toolbox-filters"
                    id="subjectFilters">


                    <!-- ALL SUBJECTS -->

                    <button
                        type="button"
                        class="tool-filter active"
                        data-subject="all">
                        All Subjects
                    </button>


                    <?php foreach ($subjects as $subject): ?>

                        <?php
                        $subjectValue =
                            subjectFilterValue($subject);
                        ?>

                        <button
                            type="button"
                            class="tool-filter"
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
                 TOOL COUNT
            ================================================== -->

            <div class="toolbox-results-header">

                <span id="toolCount">
                    0 tools found
                </span>

            </div>


            <!-- =================================================
                 TOOL GRID
            ================================================== -->

            <div
                class="toolbox-grid"
                id="toolboxGrid">


                <?php if (count($tools) > 0): ?>


                    <?php foreach ($tools as $tool): ?>


                        <?php

                        /* -----------------------------------------
                           DATABASE VALUES
                        ----------------------------------------- */

                        $toolId =
                            (int) ($tool["id"] ?? 0);


                        $title =
                            trim(
                                $tool["title"] ?? ""
                            );


                        $description =
                            trim(
                                $tool["description"] ?? ""
                            );


                        $url =
                            trim(
                                $tool["url"] ?? "#"
                            );


                        $subject =
                            trim(
                                $tool["subject"] ?? "General"
                            );


                        $icon =
                            trim(
                                $tool["icon"] ?? "bi-tools"
                            );


                        $iconColor =
                            trim(
                                $tool["icon_color"] ?? "blue"
                            );


                        /* -----------------------------------------
                           SECURITY / VALIDATION
                        ----------------------------------------- */

                        if (
                            !in_array(
                                $iconColor,
                                $allowedIconColors,
                                true
                            )
                        ) {

                            $iconColor = "blue";
                        }


                        if ($url === "") {

                            $url = "#";
                        }


                        /* -----------------------------------------
                           FILTER VALUES
                        ----------------------------------------- */

                        $subjectValue =
                            subjectFilterValue($subject);


                        $searchData =
                            strtolower(
                                $title .
                                    " " .
                                    $description .
                                    " " .
                                    $subject
                            );


                        ?>


                        <a
                            href="<?= htmlspecialchars(
                                        $url,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>"
                            class="tool-card"

                            data-id="<?= $toolId ?>"

                            data-subject="<?= htmlspecialchars(
                                                $subjectValue,
                                                ENT_QUOTES,
                                                "UTF-8"
                                            ) ?>"

                            data-name="<?= htmlspecialchars(
                                            $searchData,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>"

                            target="_blank"
                            rel="noopener noreferrer">


                            <!-- TOOL ICON -->

                            <div
                                class="tool-icon <?= htmlspecialchars(
                                                        $iconColor,
                                                        ENT_QUOTES,
                                                        "UTF-8"
                                                    ) ?>">

                                <i
                                    class="bi <?= htmlspecialchars(
                                                    $icon,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>"></i>

                            </div>


                            <!-- TOOL CONTENT -->

                            <div class="tool-content">


                                <h5>

                                    <?= htmlspecialchars(
                                        $title,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>

                                </h5>


                                <p>

                                    <?= htmlspecialchars(
                                        $description,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>

                                </p>


                                <span class="tool-subject">

                                    <?= htmlspecialchars(
                                        $subject,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>

                                </span>


                            </div>


                        </a>


                    <?php endforeach; ?>


                <?php endif; ?>


            </div>


            <!-- =================================================
                 EMPTY STATE
            ================================================== -->

            <div
                id="toolEmpty"
                class="toolbox-empty"
                style="display:none;">

                <div class="toolbox-empty-icon">

                    <i class="bi bi-search"></i>

                </div>


                <h5>
                    No tools found
                </h5>


                <p>
                    Try a different search term or
                    select another subject.
                </p>

            </div>


        </div>


    </main>


    <!-- =========================================================
     TOOLBOX STYLES
========================================================== -->

    <style>
        /* =========================================================
   TOOLBOX THEME VARIABLES
   These follow the global ETS light/dark theme.
========================================================== */

        :root {

            --toolbox-bg: #ffffff;
            --toolbox-border: #e4e7ec;
            --toolbox-input-border: #d0d5dd;

            --toolbox-text: #172033;
            --toolbox-muted: #667085;
            --toolbox-secondary: #475467;

            --toolbox-control-bg: #ffffff;
            --toolbox-hover-bg: #f8fafc;

            --toolbox-filter-active: #1e3a8a;
            --toolbox-filter-active-border: #1e3a8a;

            --toolbox-tag-bg: #f2f4f7;

            --toolbox-search-focus: #2563eb;
        }


        /* =========================================================
   DARK MODE
   Supports the ETS theme using data-theme="dark"
========================================================== */

        html[data-theme="dark"] {

            --toolbox-bg: #151922;
            --toolbox-border: #2a3140;
            --toolbox-input-border: #3a4252;

            --toolbox-text: #f1f5f9;
            --toolbox-muted: #a8b0bf;
            --toolbox-secondary: #c2c9d3;

            --toolbox-control-bg: #151922;
            --toolbox-hover-bg: #1c2230;

            --toolbox-filter-active: #2563eb;
            --toolbox-filter-active-border: #2563eb;

            --toolbox-tag-bg: #252c38;

            --toolbox-search-focus: #3b82f6;
        }


        /* =========================================================
   CONTROLS
========================================================== */

        .toolbox-controls {

            padding: 20px;

            background: var(--toolbox-bg);

            border: 1px solid var(--toolbox-border);

            border-radius: 10px;

            transition:
                background .2s ease,
                border-color .2s ease;
        }


        /* =========================================================
   SEARCH
========================================================== */

        .toolbox-search {

            position: relative;

            margin-bottom: 17px;
        }


        .toolbox-search i {

            position: absolute;

            left: 15px;

            top: 50%;

            transform: translateY(-50%);

            color: var(--toolbox-muted);

            transition:
                color .2s ease;
        }


        .toolbox-search input {

            width: 100%;

            height: 45px;

            padding:
                0 15px 0 42px;

            border:
                1px solid var(--toolbox-input-border);

            border-radius: 7px;

            outline: none;

            font-family: inherit;

            font-size: .85rem;

            color: var(--toolbox-text);

            background: var(--toolbox-control-bg);

            transition:
                background .2s ease,
                border-color .2s ease,
                color .2s ease,
                box-shadow .2s ease;
        }


        .toolbox-search input::placeholder {

            color: var(--toolbox-muted);

            opacity: .8;
        }


        .toolbox-search input:focus {

            border-color:
                var(--toolbox-search-focus);

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, .10);
        }


        /* =========================================================
   FILTERS
========================================================== */

        .toolbox-filters {

            display: flex;

            flex-wrap: wrap;

            gap: 8px;
        }


        .tool-filter {

            border:
                1px solid var(--toolbox-input-border);

            background:
                var(--toolbox-control-bg);

            color:
                var(--toolbox-muted);

            padding:
                7px 12px;

            border-radius: 6px;

            font-family: inherit;

            font-size: .76rem;

            font-weight: 600;

            cursor: pointer;

            transition:
                all .2s ease;
        }


        .tool-filter:hover {

            border-color:
                var(--toolbox-secondary);

            color:
                var(--toolbox-text);

            background:
                var(--toolbox-hover-bg);
        }


        .tool-filter.active {

            color: #ffffff;

            background:
                var(--toolbox-filter-active);

            border-color:
                var(--toolbox-filter-active-border);
        }


        /* =========================================================
   RESULT HEADER
========================================================== */

        .toolbox-results-header {

            margin-bottom: 12px;

            color:
                var(--toolbox-muted);

            font-size: .78rem;

            font-weight: 600;
        }


        /* =========================================================
   TOOL GRID
========================================================== */

        .toolbox-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 14px;
        }


        /* =========================================================
   TOOL CARD
========================================================== */

        .tool-card {

            display: flex;

            align-items: flex-start;

            gap: 14px;

            padding: 20px;

            min-height: 145px;

            border:
                1px solid var(--toolbox-border);

            border-radius: 9px;

            background:
                var(--toolbox-bg);

            color:
                var(--toolbox-text);

            text-decoration: none;

            transition:
                border-color .2s ease,
                background .2s ease,
                color .2s ease,
                transform .2s ease,
                box-shadow .2s ease;
        }


        .tool-card:hover {

            color:
                var(--toolbox-text);

            border-color:
                var(--toolbox-input-border);

            background:
                var(--toolbox-hover-bg);

            transform:
                translateY(-2px);
        }


        /* =========================================================
   TOOL ICON
========================================================== */

        .tool-icon {

            flex:
                0 0 auto;

            width: 42px;

            height: 42px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 8px;

            font-size: 1rem;

            transition:
                background .2s ease,
                color .2s ease;
        }


        /* =========================================================
   ICON COLORS
========================================================== */

        .tool-icon.blue {

            color: #2563eb;

            background: #eff6ff;
        }


        .tool-icon.purple {

            color: #7c3aed;

            background: #f5f3ff;
        }


        .tool-icon.orange {

            color: #ea580c;

            background: #fff7ed;
        }


        .tool-icon.green {

            color: #16a34a;

            background: #ecfdf3;
        }


        .tool-icon.red {

            color: #dc2626;

            background: #fef2f2;
        }


        .tool-icon.yellow {

            color: #ca8a04;

            background: #fefce8;
        }


        /* =========================================================
   DARK MODE ICON BACKGROUNDS
========================================================== */

        html[data-theme="dark"] .tool-icon.blue {

            color: #60a5fa;

            background: rgba(37, 99, 235, .16);
        }


        html[data-theme="dark"] .tool-icon.purple {

            color: #a78bfa;

            background: rgba(124, 58, 237, .16);
        }


        html[data-theme="dark"] .tool-icon.orange {

            color: #fb923c;

            background: rgba(234, 88, 12, .16);
        }


        html[data-theme="dark"] .tool-icon.green {

            color: #4ade80;

            background: rgba(22, 163, 74, .16);
        }


        html[data-theme="dark"] .tool-icon.red {

            color: #f87171;

            background: rgba(220, 38, 38, .16);
        }


        html[data-theme="dark"] .tool-icon.yellow {

            color: #facc15;

            background: rgba(202, 138, 4, .16);
        }


        /* =========================================================
   TOOL CONTENT
========================================================== */

        .tool-content {

            min-width: 0;

            flex: 1;
        }


        .tool-content h5 {

            margin:
                0 0 5px;

            font-size: .88rem;

            font-weight: 700;

            line-height: 1.35;

            color:
                var(--toolbox-text);
        }


        .tool-content p {

            margin:
                0 0 11px;

            color:
                var(--toolbox-muted);

            font-size: .76rem;

            line-height: 1.55;
        }


        .tool-subject {

            display: inline-block;

            color:
                var(--toolbox-secondary);

            background:
                var(--toolbox-tag-bg);

            border-radius: 5px;

            padding:
                3px 7px;

            font-size: .66rem;

            font-weight: 600;

            transition:
                background .2s ease,
                color .2s ease;
        }


        /* =========================================================
   EMPTY STATE
========================================================== */

        .toolbox-empty {

            padding:
                60px 20px;

            text-align: center;

            border:
                1px solid var(--toolbox-border);

            border-radius: 10px;

            background:
                var(--toolbox-bg);

            color:
                var(--toolbox-text);

            transition:
                background .2s ease,
                border-color .2s ease,
                color .2s ease;
        }


        .toolbox-empty-icon {

            width: 45px;

            height: 45px;

            margin:
                0 auto 15px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background:
                var(--toolbox-tag-bg);

            color:
                var(--toolbox-muted);
        }


        .toolbox-empty h5 {

            margin:
                0 0 5px;

            font-size: .95rem;

            color:
                var(--toolbox-text);
        }


        .toolbox-empty p {

            margin: 0;

            color:
                var(--toolbox-muted);

            font-size: .8rem;
        }


        /* =========================================================
   RESPONSIVE
========================================================== */

        @media (max-width: 1100px) {

            .toolbox-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }

        }


        @media (max-width: 700px) {

            .toolbox-grid {

                grid-template-columns:
                    1fr;
            }

        }


        @media (max-width: 576px) {

            .toolbox-controls {

                padding: 15px;
            }


            .tool-filter {

                font-size: .72rem;

                padding:
                    6px 9px;
            }


            .tool-card {

                padding: 17px;
            }

        }
    </style>


    <!-- =========================================================
         FILTER / SEARCH JAVASCRIPT
    ========================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {


                const searchInput =
                    document.getElementById(
                        "toolSearch"
                    );


                const filterButtons =
                    document.querySelectorAll(
                        ".tool-filter"
                    );


                const toolCards =
                    document.querySelectorAll(
                        ".tool-card"
                    );


                const toolCount =
                    document.getElementById(
                        "toolCount"
                    );


                const emptyState =
                    document.getElementById(
                        "toolEmpty"
                    );


                let selectedSubject =
                    "all";


                /* =================================================
                   FILTER FUNCTION
                ================================================== */

                function filterTools() {


                    const searchTerm =
                        searchInput.value
                        .toLowerCase()
                        .trim();


                    let visibleCount = 0;


                    toolCards.forEach(
                        function(card) {


                            const subject =
                                (
                                    card.dataset.subject ||
                                    ""
                                )
                                .toLowerCase();


                            const name =
                                (
                                    card.dataset.name ||
                                    ""
                                )
                                .toLowerCase();


                            const content =
                                card.innerText
                                .toLowerCase();


                            const subjectMatch =
                                selectedSubject === "all" ||
                                subject === selectedSubject;


                            const searchMatch =
                                searchTerm === "" ||
                                name.includes(searchTerm) ||
                                content.includes(searchTerm);


                            if (
                                subjectMatch &&
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

                    toolCount.textContent =
                        visibleCount +
                        (
                            visibleCount === 1 ?
                            " tool found" :
                            " tools found"
                        );


                    /* =================================================
                       EMPTY STATE
                    ================================================== */

                    if (visibleCount === 0) {

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

                filterButtons.forEach(
                    function(button) {


                        button.addEventListener(
                            "click",
                            function() {


                                filterButtons.forEach(
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


                                filterTools();

                            }
                        );

                    }
                );


                /* =================================================
                   SEARCH
                ================================================== */

                searchInput.addEventListener(
                    "input",
                    filterTools
                );


                /* =================================================
                   INITIALIZE
                ================================================== */

                filterTools();

            }
        );
    </script>


    <!-- =========================================================
         GLOBAL SCRIPTS
    ========================================================== -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>