<?php

/* =========================================================
   PROJECTILE MOTION TOOL
   ETS-Async Learning Portal
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
========================================================== */

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
                        Projectile Motion
                    </h2>

                    <p>
                        Compute, visualize, and interact with
                        the trajectory of a projectile.
                    </p>

                </div>

            </div>


            <!-- =================================================
             MAIN PROJECTILE LAYOUT
        ================================================== -->

            <div class="difference-layout">


                <!-- =================================================
                 LEFT CONTROL PANEL
            ================================================== -->

                <div class="difference-controls">


                    <!-- =================================================
                     INITIAL CONDITIONS
                ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-rocket-takeoff"></i>

                            <span>
                                Initial Conditions
                            </span>

                        </div>


                        <!-- INITIAL VELOCITY -->

                        <label class="difference-label">

                            Initial Velocity

                            <span>
                                v₀
                            </span>

                        </label>


                        <input
                            type="number"
                            id="initialVelocity"
                            class="difference-input"
                            value="20"
                            min="0"
                            step="any">


                        <!-- LAUNCH ANGLE -->

                        <div class="input-parameter">

                            <label class="difference-label">

                                Launch Angle

                                <span id="angleValue">
                                    45°
                                </span>

                            </label>


                            <input
                                type="range"
                                id="launchAngle"
                                min="0"
                                max="90"
                                step="0.1"
                                value="45"
                                class="difference-range">

                        </div>


                        <!-- INITIAL HEIGHT -->

                        <div class="input-parameter">

                            <label class="difference-label">

                                Initial Height

                                <span>
                                    y₀
                                </span>

                            </label>


                            <input
                                type="number"
                                id="initialHeight"
                                class="difference-input"
                                value="0"
                                min="0"
                                step="any">

                        </div>


                        <!-- GRAVITY -->

                        <div class="input-parameter">

                            <label class="difference-label">

                                Gravitational Acceleration

                                <span>
                                    g
                                </span>

                            </label>


                            <input
                                type="number"
                                id="gravity"
                                class="difference-input"
                                value="9.81"
                                min="0.01"
                                step="any">

                        </div>

                    </div>


                    <!-- =================================================
                     GRAPH OPTIONS
                ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-graph-up"></i>

                            <span>
                                Graph Options
                            </span>

                        </div>


                        <!-- SHOW POINTS -->

                        <div class="graph-option">

                            <label>

                                <span>
                                    Show Trajectory Points
                                </span>

                                <input
                                    type="checkbox"
                                    id="showPoints"
                                    checked>

                            </label>

                        </div>


                        <!-- SHOW GRID -->

                        <div class="graph-option">

                            <label>

                                <span>
                                    Show Grid
                                </span>

                                <input
                                    type="checkbox"
                                    id="showGrid"
                                    checked>

                            </label>

                        </div>


                        <!-- SHOW ELEVATION -->

                        <div class="graph-option">

                            <label>

                                <span>
                                    Show Elevation Guides
                                </span>

                                <input
                                    type="checkbox"
                                    id="showElevation"
                                    checked>

                            </label>

                        </div>


                        <!-- POINT DENSITY -->

                        <div class="input-parameter">

                            <label class="difference-label">

                                Trajectory Points

                                <span id="pointCountValue">
                                    21
                                </span>

                            </label>


                            <input
                                type="range"
                                id="pointCount"
                                min="11"
                                max="101"
                                step="5"
                                value="21"
                                class="difference-range">

                        </div>

                    </div>


                    <!-- =================================================
                     ACTION BUTTONS
                ================================================== -->

                    <div class="difference-actions">

                        <button
                            type="button"
                            id="computeButton"
                            class="difference-button primary">

                            <i class="bi bi-play-fill"></i>

                            Compute

                        </button>


                        <button
                            type="button"
                            id="resetButton"
                            class="difference-button">

                            <i class="bi bi-arrow-counterclockwise"></i>

                            Reset

                        </button>

                    </div>


                </div>


                <!-- =================================================
                 RIGHT OUTPUT PANEL
            ================================================== -->

                <div class="difference-output">


                    <!-- =================================================
                     EQUATION DISPLAY
                ================================================== -->

                    <div class="equation-display">

                        <div class="equation-display-label">

                            Projectile Equations

                        </div>


                        <div
                            id="equationDisplay"
                            class="equation">

                            x(t) = v₀ cos(θ)t
                            &nbsp;&nbsp;&nbsp;
                            y(t) = y₀ + v₀ sin(θ)t − ½gt²

                        </div>

                    </div>


                    <!-- =================================================
                     GRAPH
                ================================================== -->

                    <div class="difference-card">

                        <div class="output-header">

                            <div>

                                <h4>
                                    Projectile Trajectory
                                </h4>

                                <p>
                                    Hover, click, or drag across the
                                    trajectory to inspect individual points.
                                </p>

                            </div>


                            <div class="output-badge">

                                <i class="bi bi-cursor"></i>

                                Interactive

                            </div>

                        </div>


                        <div class="difference-graph">

                            <canvas
                                id="projectileCanvas">
                            </canvas>


                            <!-- =================================================
                             GRAPH TOOLTIP
                        ================================================== -->

                            <div
                                id="graphTooltip"
                                class="graph-tooltip">

                                <div class="tooltip-title">
                                    Projectile Point
                                </div>

                                <div>
                                    t =
                                    <strong id="tooltipTime">
                                        0
                                    </strong>
                                    s
                                </div>

                                <div>
                                    x =
                                    <strong id="tooltipX">
                                        0
                                    </strong>
                                    m
                                </div>

                                <div>
                                    y =
                                    <strong id="tooltipY">
                                        0
                                    </strong>
                                    m
                                </div>

                                <div>
                                    vₓ =
                                    <strong id="tooltipVx">
                                        0
                                    </strong>
                                    m/s
                                </div>

                                <div>
                                    vᵧ =
                                    <strong id="tooltipVy">
                                        0
                                    </strong>
                                    m/s
                                </div>

                                <div>
                                    speed =
                                    <strong id="tooltipSpeed">
                                        0
                                    </strong>
                                    m/s
                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                     SELECTED POINT
                ================================================== -->

                    <div class="difference-card">

                        <div class="output-header">

                            <div>

                                <h4>
                                    Selected Point
                                </h4>

                                <p>
                                    Exact projectile values at the
                                    selected position.
                                </p>

                            </div>


                            <div class="output-badge">

                                <i class="bi bi-crosshair"></i>

                                Point Analysis

                            </div>

                        </div>


                        <div class="selected-point-grid">


                            <div class="selected-point-item">

                                <span>
                                    Time
                                </span>

                                <strong id="selectedTime">
                                    0.000 s
                                </strong>

                            </div>


                            <div class="selected-point-item">

                                <span>
                                    Horizontal Distance
                                </span>

                                <strong id="selectedX">
                                    0.000 m
                                </strong>

                            </div>


                            <div class="selected-point-item">

                                <span>
                                    Elevation
                                </span>

                                <strong id="selectedY">
                                    0.000 m
                                </strong>

                            </div>


                            <div class="selected-point-item">

                                <span>
                                    Vertical Velocity
                                </span>

                                <strong id="selectedVy">
                                    0.000 m/s
                                </strong>

                            </div>


                            <div class="selected-point-item">

                                <span>
                                    Horizontal Velocity
                                </span>

                                <strong id="selectedVx">
                                    0.000 m/s
                                </strong>

                            </div>


                            <div class="selected-point-item">

                                <span>
                                    Speed
                                </span>

                                <strong id="selectedSpeed">
                                    0.000 m/s
                                </strong>

                            </div>


                        </div>

                    </div>


                    <!-- =================================================
                     RESULTS TABLE
                ================================================== -->

                    <div class="difference-card">

                        <div class="output-header">

                            <div>

                                <h4>
                                    Trajectory Points
                                </h4>

                                <p>
                                    Numerical values along the projectile path.
                                    Click a row to highlight its point.
                                </p>

                            </div>

                        </div>


                        <div class="sequence-table-wrapper">

                            <table class="sequence-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Point
                                        </th>

                                        <th>
                                            t (s)
                                        </th>

                                        <th>
                                            x (m)
                                        </th>

                                        <th>
                                            y (m)
                                        </th>

                                        <th>
                                            vₓ (m/s)
                                        </th>

                                        <th>
                                            vᵧ (m/s)
                                        </th>

                                        <th>
                                            Speed (m/s)
                                        </th>

                                    </tr>

                                </thead>


                                <tbody
                                    id="trajectoryTable">

                                </tbody>

                            </table>

                        </div>

                    </div>


                    <!-- =================================================
                     RESULTS SUMMARY
                ================================================== -->

                    <div class="result-summary">


                        <div class="summary-item">

                            <span>
                                Time of Flight
                            </span>

                            <strong id="summaryFlightTime">
                                0 s
                            </strong>

                        </div>


                        <div class="summary-item">

                            <span>
                                Maximum Height
                            </span>

                            <strong id="summaryMaxHeight">
                                0 m
                            </strong>

                        </div>


                        <div class="summary-item">

                            <span>
                                Horizontal Range
                            </span>

                            <strong id="summaryRange">
                                0 m
                            </strong>

                        </div>


                        <div class="summary-item">

                            <span>
                                Time to Maximum Height
                            </span>

                            <strong id="summaryMaxTime">
                                0 s
                            </strong>

                        </div>


                    </div>


                </div>

            </div>


            <!-- =================================================
             THEORY REFERENCE
        ================================================== -->

            <div class="difference-reference">

                <div class="reference-title">

                    <i class="bi bi-info-circle"></i>

                    Projectile Motion Reference

                </div>


                <div class="reference-grid">


                    <div class="reference-item">

                        <strong>
                            Horizontal Motion
                        </strong>

                        <span>
                            x(t) = v₀ cos(θ)t
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Vertical Motion
                        </strong>

                        <span>
                            y(t) = y₀ + v₀sin(θ)t − ½gt²
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Maximum Height
                        </strong>

                        <span>
                            H = y₀ + v₀²sin²(θ)/(2g)
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Trajectory
                        </strong>

                        <span>
                            y(x) = y₀ + x tan(θ)
                            − gx²/(2v₀²cos²(θ))
                        </span>

                    </div>


                </div>

            </div>


        </div>

    </main>


    <!-- =========================================================
     PROJECTILE MOTION STYLES
========================================================== -->

    <style>
        /* =====================================================
       THEME VARIABLES
    ====================================================== */

        :root {

            --difference-bg: #ffffff;

            --difference-border: #e4e7ec;

            --difference-input-border: #d0d5dd;

            --difference-text: #172033;

            --difference-muted: #667085;

            --difference-secondary: #475467;

            --difference-hover: #f8fafc;

            --difference-panel: #ffffff;

            --difference-grid: #e4e7ec;

            --difference-axis: #475467;

            --difference-blue: #2563eb;

            --difference-header: #f8fafc;

            --difference-point: #ef4444;

            --difference-guide: #94a3b8;

        }


        /* =====================================================
       DARK MODE
    ====================================================== */

        html[data-theme="dark"] {

            --difference-bg: #151922;

            --difference-border: #2a3140;

            --difference-input-border: #3a4252;

            --difference-text: #f1f5f9;

            --difference-muted: #a8b0bf;

            --difference-secondary: #c2c9d3;

            --difference-hover: #1c2230;

            --difference-panel: #151922;

            --difference-grid: #303847;

            --difference-axis: #aeb7c5;

            --difference-blue: #60a5fa;

            --difference-header: #1c2230;

            --difference-point: #f87171;

            --difference-guide: #64748b;

        }


        /* =====================================================
       MAIN LAYOUT
    ====================================================== */

        .difference-layout {

            display: grid;

            grid-template-columns:
                310px minmax(0, 1fr);

            gap: 16px;

            margin-bottom: 18px;

        }


        /* =====================================================
       CONTROL PANEL
    ====================================================== */

        .difference-controls {

            background:
                var(--difference-bg);

            border:
                1px solid var(--difference-border);

            border-radius: 10px;

            overflow: hidden;

        }


        .control-section {

            padding: 17px;

            border-bottom:
                1px solid var(--difference-border);

        }


        .control-section-title {

            display: flex;

            align-items: center;

            gap: 8px;

            margin-bottom: 14px;

            color:
                var(--difference-text);

            font-size: .82rem;

            font-weight: 700;

        }


        .control-section-title i {

            color:
                var(--difference-blue);

        }


        /* =====================================================
       LABELS
    ====================================================== */

        .difference-label {

            display: flex;

            justify-content: space-between;

            margin-bottom: 6px;

            color:
                var(--difference-secondary);

            font-size: .71rem;

            font-weight: 600;

        }


        .difference-label span {

            color:
                var(--difference-blue);

            font-weight: 700;

        }


        /* =====================================================
       INPUTS
    ====================================================== */

        .difference-input {

            width: 100%;

            height: 38px;

            padding:
                0 10px;

            border:
                1px solid var(--difference-input-border);

            border-radius: 6px;

            outline: none;

            background:
                var(--difference-panel);

            color:
                var(--difference-text);

            font-family: inherit;

            font-size: .75rem;

        }


        .difference-input:focus {

            border-color:
                var(--difference-blue);

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, .10);

        }


        /* =====================================================
       RANGE
    ====================================================== */

        .input-parameter {

            margin-top: 15px;

        }


        .difference-range {

            width: 100%;

            accent-color:
                var(--difference-blue);

            cursor: pointer;

        }


        /* =====================================================
       GRAPH OPTIONS
    ====================================================== */

        .graph-option {

            margin-bottom: 10px;

        }


        .graph-option label {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 10px;

            color:
                var(--difference-secondary);

            font-size: .71rem;

            font-weight: 600;

            cursor: pointer;

        }


        .graph-option input {

            width: 16px;

            height: 16px;

            accent-color:
                var(--difference-blue);

            cursor: pointer;

        }


        /* =====================================================
       ACTIONS
    ====================================================== */

        .difference-actions {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 8px;

            padding: 14px;

            background:
                var(--difference-hover);

        }


        .difference-button {

            height: 38px;

            border:
                1px solid var(--difference-input-border);

            border-radius: 6px;

            background:
                var(--difference-panel);

            color:
                var(--difference-secondary);

            font-family: inherit;

            font-size: .72rem;

            font-weight: 700;

            cursor: pointer;

            transition:
                all .2s ease;

        }


        .difference-button:hover {

            background:
                var(--difference-hover);

            color:
                var(--difference-text);

        }


        .difference-button.primary {

            background:
                var(--difference-blue);

            border-color:
                var(--difference-blue);

            color:
                #ffffff;

        }


        /* =====================================================
       OUTPUT
    ====================================================== */

        .difference-output {

            min-width: 0;

        }


        /* =====================================================
       EQUATION DISPLAY
    ====================================================== */

        .equation-display {

            margin-bottom: 14px;

            padding: 17px 20px;

            background:
                var(--difference-bg);

            border:
                1px solid var(--difference-border);

            border-radius: 10px;

        }


        .equation-display-label {

            margin-bottom: 7px;

            color:
                var(--difference-muted);

            font-size: .66rem;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .03em;

        }


        .equation {

            color:
                var(--difference-text);

            font-family:
                "Times New Roman",
                serif;

            font-size: 1.1rem;

        }


        /* =====================================================
       OUTPUT CARD
    ====================================================== */

        .difference-card {

            margin-bottom: 14px;

            background:
                var(--difference-bg);

            border:
                1px solid var(--difference-border);

            border-radius: 10px;

            overflow: hidden;

        }


        .output-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding:
                16px 18px;

            border-bottom:
                1px solid var(--difference-border);

        }


        .output-header h4 {

            margin:
                0 0 3px;

            color:
                var(--difference-text);

            font-size: .9rem;

            font-weight: 700;

        }


        .output-header p {

            margin: 0;

            color:
                var(--difference-muted);

            font-size: .68rem;

        }


        .output-badge {

            display: flex;

            align-items: center;

            gap: 5px;

            padding:
                5px 8px;

            border-radius: 5px;

            background:
                var(--difference-hover);

            color:
                var(--difference-secondary);

            font-size: .63rem;

            font-weight: 700;

            white-space: nowrap;

        }


        /* =====================================================
       GRAPH
    ====================================================== */

        .difference-graph {

            position: relative;

            width: 100%;

            height: 500px;

            padding: 10px;

            overflow: hidden;

        }


        #projectileCanvas {

            width: 100%;

            height: 100%;

            display: block;

            cursor:
                crosshair;

        }


        /* =====================================================
       GRAPH TOOLTIP
    ====================================================== */

        .graph-tooltip {

            position: absolute;

            top: 15px;

            right: 15px;

            min-width: 175px;

            padding: 10px 12px;

            background:
                var(--difference-panel);

            border:
                1px solid var(--difference-border);

            border-radius: 7px;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, .10);

            color:
                var(--difference-secondary);

            font-size: .66rem;

            line-height: 1.7;

            pointer-events: none;

            opacity: 0;

            transform:
                translateY(-4px);

            transition:
                opacity .12s ease,
                transform .12s ease;

            z-index: 10;

        }


        .graph-tooltip.visible {

            opacity: 1;

            transform:
                translateY(0);

        }


        .tooltip-title {

            margin-bottom: 3px;

            color:
                var(--difference-text);

            font-size: .7rem;

            font-weight: 700;

        }


        .graph-tooltip strong {

            color:
                var(--difference-blue);

        }


        /* =====================================================
       SELECTED POINT
    ====================================================== */

        .selected-point-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

        }


        .selected-point-item {

            padding:
                14px 16px;

            border-right:
                1px solid var(--difference-border);

            border-bottom:
                1px solid var(--difference-border);

        }


        .selected-point-item:nth-child(3n) {

            border-right: none;

        }


        .selected-point-item:nth-child(n + 4) {

            border-bottom: none;

        }


        .selected-point-item span {

            display: block;

            margin-bottom: 4px;

            color:
                var(--difference-muted);

            font-size: .63rem;

            font-weight: 600;

        }


        .selected-point-item strong {

            color:
                var(--difference-text);

            font-size: .76rem;

        }


        /* =====================================================
       TABLE
    ====================================================== */

        .sequence-table-wrapper {

            max-height: 330px;

            overflow-y: auto;

        }


        .sequence-table {

            width: 100%;

            border-collapse: collapse;

        }


        .sequence-table th {

            position: sticky;

            top: 0;

            padding:
                10px 10px;

            background:
                var(--difference-header);

            border-bottom:
                1px solid var(--difference-border);

            color:
                var(--difference-secondary);

            font-size: .64rem;

            text-align: right;

            white-space: nowrap;

        }


        .sequence-table th:first-child {

            text-align: center;

        }


        .sequence-table td {

            padding:
                8px 10px;

            border-bottom:
                1px solid var(--difference-border);

            color:
                var(--difference-text);

            font-family:
                "Courier New",
                monospace;

            font-size: .66rem;

            text-align: right;

            white-space: nowrap;

        }


        .sequence-table td:first-child {

            text-align: center;

            font-weight: 700;

        }


        .sequence-table tr {

            cursor: pointer;

            transition:
                background .15s ease;

        }


        .sequence-table tr:hover td {

            background:
                var(--difference-hover);

        }


        .sequence-table tr.selected td {

            background:
                rgba(37, 99, 235, .10);

        }


        /* =====================================================
       SUMMARY
    ====================================================== */

        .result-summary {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            margin-bottom: 14px;

            background:
                var(--difference-bg);

            border:
                1px solid var(--difference-border);

            border-radius: 10px;

            overflow: hidden;

        }


        .summary-item {

            padding:
                13px 15px;

            border-right:
                1px solid var(--difference-border);

        }


        .summary-item:last-child {

            border-right: none;

        }


        .summary-item span {

            display: block;

            margin-bottom: 4px;

            color:
                var(--difference-muted);

            font-size: .63rem;

            font-weight: 600;

        }


        .summary-item strong {

            color:
                var(--difference-text);

            font-size: .75rem;

        }


        /* =====================================================
       REFERENCE
    ====================================================== */

        .difference-reference {

            background:
                var(--difference-bg);

            border:
                1px solid var(--difference-border);

            border-radius: 10px;

            overflow: hidden;

        }


        .reference-title {

            display: flex;

            align-items: center;

            gap: 8px;

            padding:
                14px 17px;

            border-bottom:
                1px solid var(--difference-border);

            color:
                var(--difference-text);

            font-size: .8rem;

            font-weight: 700;

        }


        .reference-title i {

            color:
                var(--difference-blue);

        }


        .reference-grid {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

        }


        .reference-item {

            padding:
                15px 17px;

            border-right:
                1px solid var(--difference-border);

        }


        .reference-item:last-child {

            border-right: none;

        }


        .reference-item strong {

            display: block;

            margin-bottom: 5px;

            color:
                var(--difference-text);

            font-size: .72rem;

        }


        .reference-item span {

            color:
                var(--difference-muted);

            font-family:
                "Times New Roman",
                serif;

            font-size: .76rem;

            line-height: 1.5;

        }


        /* =====================================================
       RESPONSIVE
    ====================================================== */

        @media (max-width: 1100px) {

            .difference-layout {

                grid-template-columns:
                    280px minmax(0, 1fr);

            }


            .reference-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .selected-point-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .selected-point-item:nth-child(3n) {

                border-right:
                    1px solid var(--difference-border);

            }


            .selected-point-item:nth-child(2n) {

                border-right:
                    none;

            }


            .selected-point-item:nth-child(n + 4) {

                border-bottom:
                    1px solid var(--difference-border);

            }


            .selected-point-item:nth-child(n + 5) {

                border-bottom:
                    none;

            }

        }


        @media (max-width: 850px) {

            .difference-layout {

                grid-template-columns:
                    1fr;

            }

        }


        @media (max-width: 600px) {

            .difference-graph {

                height: 390px;

            }


            .result-summary {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .summary-item:nth-child(2) {

                border-right:
                    none;

            }


            .summary-item:nth-child(1),
            .summary-item:nth-child(2) {

                border-bottom:
                    1px solid var(--difference-border);

            }


            .reference-grid {

                grid-template-columns:
                    1fr;

            }


            .reference-item {

                border-right: none;

                border-bottom:
                    1px solid var(--difference-border);

            }


            .reference-item:last-child {

                border-bottom: none;

            }


            .output-header {

                align-items:
                    flex-start;

                flex-direction:
                    column;

            }


            .selected-point-grid {

                grid-template-columns:
                    1fr 1fr;

            }


            .graph-tooltip {

                min-width:
                    155px;

                font-size:
                    .61rem;

            }

        }
    </style>


    <!-- =========================================================
     PROJECTILE MOTION JAVASCRIPT
========================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {


                /* =================================================
                   ELEMENTS
                ================================================== */

                const initialVelocity =
                    document.getElementById(
                        "initialVelocity"
                    );

                const launchAngle =
                    document.getElementById(
                        "launchAngle"
                    );

                const initialHeight =
                    document.getElementById(
                        "initialHeight"
                    );

                const gravity =
                    document.getElementById(
                        "gravity"
                    );

                const showPoints =
                    document.getElementById(
                        "showPoints"
                    );

                const showGrid =
                    document.getElementById(
                        "showGrid"
                    );

                const showElevation =
                    document.getElementById(
                        "showElevation"
                    );

                const pointCount =
                    document.getElementById(
                        "pointCount"
                    );

                const canvas =
                    document.getElementById(
                        "projectileCanvas"
                    );

                const ctx =
                    canvas.getContext(
                        "2d"
                    );


                const tooltip =
                    document.getElementById(
                        "graphTooltip"
                    );


                /* =================================================
                   DATA
                ================================================== */

                let trajectory = [];

                let selectedIndex = 0;

                let hoverIndex = -1;

                let isDragging = false;


                /* =================================================
                   GRAPH SCALE
                ================================================== */

                let graphScale = {

                    minX: 0,

                    maxX: 1,

                    minY: 0,

                    maxY: 1

                };


                /* =================================================
                   FORMAT NUMBER
                ================================================== */

                function formatNumber(
                    value,
                    decimals = 3
                ) {

                    if (
                        Math.abs(value) <
                        0.0000001
                    ) {

                        return "0";

                    }


                    return Number(
                        value.toFixed(
                            decimals
                        )
                    ).toString();

                }


                /* =================================================
                   GET THEME COLORS
                ================================================== */

                function getThemeColors() {

                    const styles =
                        getComputedStyle(
                            document.documentElement
                        );


                    return {

                        text: styles.getPropertyValue(
                            "--difference-text"
                        ).trim(),

                        muted: styles.getPropertyValue(
                            "--difference-muted"
                        ).trim(),

                        grid: styles.getPropertyValue(
                            "--difference-grid"
                        ).trim(),

                        axis: styles.getPropertyValue(
                            "--difference-axis"
                        ).trim(),

                        blue: styles.getPropertyValue(
                            "--difference-blue"
                        ).trim(),

                        point: styles.getPropertyValue(
                            "--difference-point"
                        ).trim(),

                        guide: styles.getPropertyValue(
                            "--difference-guide"
                        ).trim()

                    };

                }


                /* =================================================
                   COMPUTE PROJECTILE
                ================================================== */

                function computeProjectile() {

                    const v0 =
                        parseFloat(
                            initialVelocity.value
                        ) || 0;


                    const angle =
                        parseFloat(
                            launchAngle.value
                        ) || 0;


                    const y0 =
                        parseFloat(
                            initialHeight.value
                        ) || 0;


                    const g =
                        parseFloat(
                            gravity.value
                        ) || 9.81;


                    const numberOfPoints =
                        parseInt(
                            pointCount.value
                        ) || 21;


                    const theta =
                        angle *
                        Math.PI /
                        180;


                    const vx0 =
                        v0 *
                        Math.cos(theta);


                    const vy0 =
                        v0 *
                        Math.sin(theta);


                    /* =============================================
                       TIME OF FLIGHT
                    ============================================== */

                    const discriminant =
                        (
                            vy0 * vy0
                        ) +
                        (
                            2 *
                            g *
                            y0
                        );


                    let flightTime = 0;


                    if (
                        g > 0 &&
                        discriminant >= 0
                    ) {

                        flightTime =
                            (
                                vy0 +
                                Math.sqrt(
                                    discriminant
                                )
                            ) /
                            g;

                    }


                    if (
                        !isFinite(
                            flightTime
                        ) ||
                        flightTime < 0
                    ) {

                        flightTime = 0;

                    }


                    /* =============================================
                       MAXIMUM HEIGHT
                    ============================================== */

                    const timeToMaximum =
                        g > 0 ?
                        vy0 / g :
                        0;


                    const maximumHeight =
                        y0 +
                        (
                            vy0 * vy0
                        ) /
                        (
                            2 * g
                        );


                    /* =============================================
                       HORIZONTAL RANGE
                    ============================================== */

                    const horizontalRange =
                        vx0 *
                        flightTime;


                    /* =============================================
                       BUILD TRAJECTORY
                    ============================================== */

                    trajectory = [];


                    for (
                        let i = 0; i < numberOfPoints; i++
                    ) {

                        const ratio =
                            numberOfPoints === 1 ?
                            0 :
                            i /
                            (
                                numberOfPoints -
                                1
                            );


                        const t =
                            flightTime *
                            ratio;


                        const x =
                            vx0 *
                            t;


                        let y =
                            y0 +
                            vy0 *
                            t -
                            (
                                0.5 *
                                g *
                                t *
                                t
                            );


                        /* -----------------------------------------
                           Remove floating-point negative zero
                        ------------------------------------------ */

                        if (
                            Math.abs(y) <
                            0.000001
                        ) {

                            y = 0;

                        }


                        const vy =
                            vy0 -
                            g *
                            t;


                        const speed =
                            Math.sqrt(
                                (
                                    vx0 *
                                    vx0
                                ) +
                                (
                                    vy *
                                    vy
                                )
                            );


                        trajectory.push({

                            index: i,

                            t: t,

                            x: x,

                            y: y,

                            vx: vx0,

                            vy: vy,

                            speed: speed

                        });

                    }


                    /* =============================================
                       GRAPH RANGE
                    ============================================== */

                    graphScale.minX = 0;

                    graphScale.maxX =
                        Math.max(
                            horizontalRange * 1.08,
                            1
                        );


                    graphScale.minY =
                        Math.min(
                            0,
                            y0
                        );


                    graphScale.maxY =
                        Math.max(
                            maximumHeight * 1.12,
                            y0 + 1,
                            1
                        );


                    /* =============================================
                       SUMMARY
                    ============================================== */

                    document
                        .getElementById(
                            "summaryFlightTime"
                        )
                        .textContent =
                        formatNumber(
                            flightTime
                        ) +
                        " s";


                    document
                        .getElementById(
                            "summaryMaxHeight"
                        )
                        .textContent =
                        formatNumber(
                            maximumHeight
                        ) +
                        " m";


                    document
                        .getElementById(
                            "summaryRange"
                        )
                        .textContent =
                        formatNumber(
                            horizontalRange
                        ) +
                        " m";


                    document
                        .getElementById(
                            "summaryMaxTime"
                        )
                        .textContent =
                        formatNumber(
                            Math.max(
                                0,
                                timeToMaximum
                            )
                        ) +
                        " s";


                    /* =============================================
                       EQUATION
                    ============================================== */

                    document
                        .getElementById(
                            "equationDisplay"
                        )
                        .textContent =
                        "x(t) = " +
                        formatNumber(
                            vx0
                        ) +
                        "t     |     y(t) = " +
                        formatNumber(
                            y0
                        ) +
                        " + " +
                        formatNumber(
                            vy0
                        ) +
                        "t − ½(" +
                        formatNumber(
                            g
                        ) +
                        ")t²";


                    updateTable();


                    selectedIndex =
                        Math.min(
                            selectedIndex,
                            trajectory.length - 1
                        );


                    if (
                        selectedIndex < 0
                    ) {

                        selectedIndex = 0;

                    }


                    updateSelectedPoint(
                        selectedIndex
                    );


                    drawGraph();

                }


                /* =================================================
                   UPDATE TABLE
                ================================================== */

                function updateTable() {

                    const table =
                        document.getElementById(
                            "trajectoryTable"
                        );


                    table.innerHTML =
                        "";


                    trajectory.forEach(
                        function(point) {

                            const row =
                                document.createElement(
                                    "tr"
                                );


                            if (
                                point.index ===
                                selectedIndex
                            ) {

                                row.classList.add(
                                    "selected"
                                );

                            }


                            row.innerHTML = `

                            <td>
                                ${point.index + 1}
                            </td>

                            <td>
                                ${formatNumber(point.t)}
                            </td>

                            <td>
                                ${formatNumber(point.x)}
                            </td>

                            <td>
                                ${formatNumber(point.y)}
                            </td>

                            <td>
                                ${formatNumber(point.vx)}
                            </td>

                            <td>
                                ${formatNumber(point.vy)}
                            </td>

                            <td>
                                ${formatNumber(point.speed)}
                            </td>

                        `;


                            row.addEventListener(
                                "click",
                                function() {

                                    selectedIndex =
                                        point.index;

                                    updateSelectedPoint(
                                        selectedIndex
                                    );

                                    updateTable();

                                    drawGraph();

                                }
                            );


                            table.appendChild(
                                row
                            );

                        }
                    );

                }


                /* =================================================
                   UPDATE SELECTED POINT
                ================================================== */

                function updateSelectedPoint(
                    index
                ) {

                    if (
                        !trajectory[index]
                    ) {

                        return;

                    }


                    const point =
                        trajectory[index];


                    document
                        .getElementById(
                            "selectedTime"
                        )
                        .textContent =
                        formatNumber(
                            point.t
                        ) +
                        " s";


                    document
                        .getElementById(
                            "selectedX"
                        )
                        .textContent =
                        formatNumber(
                            point.x
                        ) +
                        " m";


                    document
                        .getElementById(
                            "selectedY"
                        )
                        .textContent =
                        formatNumber(
                            point.y
                        ) +
                        " m";


                    document
                        .getElementById(
                            "selectedVy"
                        )
                        .textContent =
                        formatNumber(
                            point.vy
                        ) +
                        " m/s";


                    document
                        .getElementById(
                            "selectedVx"
                        )
                        .textContent =
                        formatNumber(
                            point.vx
                        ) +
                        " m/s";


                    document
                        .getElementById(
                            "selectedSpeed"
                        )
                        .textContent =
                        formatNumber(
                            point.speed
                        ) +
                        " m/s";

                }


                /* =================================================
                   CANVAS RESIZE
                ================================================== */

                function resizeCanvas() {

                    const rect =
                        canvas.getBoundingClientRect();


                    const dpr =
                        window.devicePixelRatio ||
                        1;


                    canvas.width =
                        rect.width *
                        dpr;


                    canvas.height =
                        rect.height *
                        dpr;


                    ctx.setTransform(
                        dpr,
                        0,
                        0,
                        dpr,
                        0,
                        0
                    );


                    drawGraph();

                }


                /* =================================================
                   GRAPH DIMENSIONS
                ================================================== */

                function getGraphDimensions() {

                    const width =
                        canvas.clientWidth;

                    const height =
                        canvas.clientHeight;


                    const margin = {

                        left: 65,

                        right: 25,

                        top: 25,

                        bottom: 55

                    };


                    return {

                        width: width,

                        height: height,

                        margin: margin,

                        graphWidth: width -
                            margin.left -
                            margin.right,

                        graphHeight: height -
                            margin.top -
                            margin.bottom

                    };

                }


                /* =================================================
                   CONVERT DATA TO SCREEN POSITION
                ================================================== */

                function pointToScreen(
                    point
                ) {

                    const d =
                        getGraphDimensions();


                    const xRange =
                        graphScale.maxX -
                        graphScale.minX;


                    const yRange =
                        graphScale.maxY -
                        graphScale.minY;


                    const x =
                        d.margin.left +
                        (
                            (
                                point.x -
                                graphScale.minX
                            ) /
                            xRange
                        ) *
                        d.graphWidth;


                    const y =
                        d.margin.top +
                        d.graphHeight -
                        (
                            (
                                point.y -
                                graphScale.minY
                            ) /
                            yRange
                        ) *
                        d.graphHeight;


                    return {

                        x: x,

                        y: y

                    };

                }


                /* =================================================
                   SCREEN TO DATA
                ================================================== */

                function screenToData(
                    screenX,
                    screenY
                ) {

                    const d =
                        getGraphDimensions();


                    const x =
                        graphScale.minX +
                        (
                            (
                                screenX -
                                d.margin.left
                            ) /
                            d.graphWidth
                        ) *
                        (
                            graphScale.maxX -
                            graphScale.minX
                        );


                    const y =
                        graphScale.minY +
                        (
                            (
                                d.graphHeight -
                                (
                                    screenY -
                                    d.margin.top
                                )
                            ) /
                            d.graphHeight
                        ) *
                        (
                            graphScale.maxY -
                            graphScale.minY
                        );


                    return {

                        x: x,

                        y: y

                    };

                }


                /* =================================================
                   FIND NEAREST TRAJECTORY POINT
                ================================================== */

                function findNearestPoint(
                    screenX,
                    screenY
                ) {

                    if (
                        trajectory.length === 0
                    ) {

                        return -1;

                    }


                    let nearest = -1;

                    let nearestDistance =
                        Infinity;


                    trajectory.forEach(
                        function(point) {

                            const screen =
                                pointToScreen(
                                    point
                                );


                            const dx =
                                screen.x -
                                screenX;


                            const dy =
                                screen.y -
                                screenY;


                            const distance =
                                Math.sqrt(
                                    dx * dx +
                                    dy * dy
                                );


                            if (
                                distance <
                                nearestDistance
                            ) {

                                nearestDistance =
                                    distance;

                                nearest =
                                    point.index;

                            }

                        }
                    );


                    return nearest;

                }


                /* =================================================
                   DRAW GRAPH
                ================================================== */

                function drawGraph() {

                    const width =
                        canvas.clientWidth;

                    const height =
                        canvas.clientHeight;


                    if (
                        width <= 0 ||
                        height <= 0
                    ) {

                        return;

                    }


                    const colors =
                        getThemeColors();


                    const d =
                        getGraphDimensions();


                    ctx.clearRect(
                        0,
                        0,
                        width,
                        height
                    );


                    /* =============================================
                       GRAPH BACKGROUND
                    ============================================== */

                    ctx.fillStyle =
                        colors.grid;


                    ctx.fillStyle =
                        "transparent";


                    ctx.fillRect(
                        0,
                        0,
                        width,
                        height
                    );


                    /* =============================================
                       GRID
                    ============================================== */

                    if (
                        showGrid.checked
                    ) {

                        ctx.strokeStyle =
                            colors.grid;

                        ctx.lineWidth =
                            1;


                        /* -----------------------------------------
                           VERTICAL GRID
                        ------------------------------------------ */

                        const verticalLines =
                            10;


                        for (
                            let i = 0; i <= verticalLines; i++
                        ) {

                            const x =
                                d.margin.left +
                                (
                                    i /
                                    verticalLines
                                ) *
                                d.graphWidth;


                            ctx.beginPath();

                            ctx.moveTo(
                                x,
                                d.margin.top
                            );

                            ctx.lineTo(
                                x,
                                d.margin.top +
                                d.graphHeight
                            );

                            ctx.stroke();

                        }


                        /* -----------------------------------------
                           HORIZONTAL GRID
                        ------------------------------------------ */

                        const horizontalLines =
                            8;


                        for (
                            let i = 0; i <= horizontalLines; i++
                        ) {

                            const y =
                                d.margin.top +
                                (
                                    i /
                                    horizontalLines
                                ) *
                                d.graphHeight;


                            ctx.beginPath();

                            ctx.moveTo(
                                d.margin.left,
                                y
                            );

                            ctx.lineTo(
                                d.margin.left +
                                d.graphWidth,
                                y
                            );

                            ctx.stroke();

                        }

                    }


                    /* =============================================
                       AXES
                    ============================================== */

                    ctx.strokeStyle =
                        colors.axis;

                    ctx.lineWidth =
                        1.5;


                    /* X AXIS */

                    const zeroPoint =
                        pointToScreen({

                            x: 0,

                            y: 0

                        });


                    ctx.beginPath();

                    ctx.moveTo(
                        d.margin.left,
                        zeroPoint.y
                    );

                    ctx.lineTo(
                        d.margin.left +
                        d.graphWidth,
                        zeroPoint.y
                    );

                    ctx.stroke();


                    /* Y AXIS */

                    ctx.beginPath();

                    ctx.moveTo(
                        d.margin.left,
                        d.margin.top
                    );

                    ctx.lineTo(
                        d.margin.left,
                        d.margin.top +
                        d.graphHeight
                    );

                    ctx.stroke();


                    /* =============================================
                       AXIS LABELS
                    ============================================== */

                    ctx.fillStyle =
                        colors.muted;

                    ctx.font =
                        "11px Arial";

                    ctx.textAlign =
                        "center";

                    ctx.textBaseline =
                        "top";


                    const xTicks =
                        10;


                    for (
                        let i = 0; i <= xTicks; i++
                    ) {

                        const value =
                            graphScale.minX +
                            (
                                i /
                                xTicks
                            ) *
                            (
                                graphScale.maxX -
                                graphScale.minX
                            );


                        const x =
                            d.margin.left +
                            (
                                i /
                                xTicks
                            ) *
                            d.graphWidth;


                        ctx.fillText(
                            formatNumber(
                                value,
                                1
                            ),
                            x,
                            d.margin.top +
                            d.graphHeight +
                            9
                        );

                    }


                    ctx.textAlign =
                        "right";

                    ctx.textBaseline =
                        "middle";


                    const yTicks =
                        8;


                    for (
                        let i = 0; i <= yTicks; i++
                    ) {

                        const value =
                            graphScale.minY +
                            (
                                i /
                                yTicks
                            ) *
                            (
                                graphScale.maxY -
                                graphScale.minY
                            );


                        const y =
                            d.margin.top +
                            d.graphHeight -
                            (
                                i /
                                yTicks
                            ) *
                            d.graphHeight;


                        ctx.fillText(
                            formatNumber(
                                value,
                                1
                            ),
                            d.margin.left -
                            8,
                            y
                        );

                    }


                    /* =============================================
                       AXIS TITLES
                    ============================================== */

                    ctx.fillStyle =
                        colors.text;

                    ctx.font =
                        "bold 12px Arial";


                    ctx.textAlign =
                        "center";

                    ctx.textBaseline =
                        "bottom";


                    ctx.fillText(
                        "Horizontal Distance x (m)",
                        d.margin.left +
                        d.graphWidth / 2,
                        height - 7
                    );


                    ctx.save();


                    ctx.translate(
                        15,
                        d.margin.top +
                        d.graphHeight / 2
                    );


                    ctx.rotate(
                        -Math.PI / 2
                    );


                    ctx.textAlign =
                        "center";


                    ctx.fillText(
                        "Elevation y (m)",
                        0,
                        0
                    );


                    ctx.restore();


                    /* =============================================
                       TRAJECTORY CURVE
                    ============================================== */

                    if (
                        trajectory.length > 0
                    ) {

                        ctx.strokeStyle =
                            colors.blue;

                        ctx.lineWidth =
                            3;

                        ctx.lineJoin =
                            "round";

                        ctx.lineCap =
                            "round";


                        ctx.beginPath();


                        trajectory.forEach(
                            function(point, index) {

                                const screen =
                                    pointToScreen(
                                        point
                                    );


                                if (
                                    index === 0
                                ) {

                                    ctx.moveTo(
                                        screen.x,
                                        screen.y
                                    );

                                } else {

                                    ctx.lineTo(
                                        screen.x,
                                        screen.y
                                    );

                                }

                            }
                        );


                        ctx.stroke();

                    }


                    /* =============================================
                       SELECTED POINT GUIDES
                    ============================================== */

                    if (
                        showElevation.checked &&
                        trajectory[selectedIndex]
                    ) {

                        const point =
                            trajectory[
                                selectedIndex
                            ];


                        const screen =
                            pointToScreen(
                                point
                            );


                        ctx.strokeStyle =
                            colors.guide;

                        ctx.lineWidth =
                            1;

                        ctx.setLineDash([
                            5,
                            5
                        ]);


                        /* -----------------------------------------
                           VERTICAL ELEVATION GUIDE
                        ------------------------------------------ */

                        ctx.beginPath();

                        ctx.moveTo(
                            screen.x,
                            screen.y
                        );

                        ctx.lineTo(
                            screen.x,
                            zeroPoint.y
                        );

                        ctx.stroke();


                        /* -----------------------------------------
                           HORIZONTAL GUIDE
                        ------------------------------------------ */

                        ctx.beginPath();

                        ctx.moveTo(
                            d.margin.left,
                            screen.y
                        );

                        ctx.lineTo(
                            screen.x,
                            screen.y
                        );

                        ctx.stroke();


                        ctx.setLineDash([]);


                        /* -----------------------------------------
                           ELEVATION LABEL
                        ------------------------------------------ */

                        ctx.fillStyle =
                            colors.text;

                        ctx.font =
                            "bold 11px Arial";

                        ctx.textAlign =
                            "left";

                        ctx.textBaseline =
                            "bottom";


                        ctx.fillText(
                            "y = " +
                            formatNumber(
                                point.y
                            ) +
                            " m",
                            screen.x + 7,
                            screen.y - 7
                        );

                    }


                    /* =============================================
                       TRAJECTORY POINTS
                    ============================================== */

                    if (
                        showPoints.checked
                    ) {

                        trajectory.forEach(
                            function(point) {

                                const screen =
                                    pointToScreen(
                                        point
                                    );


                                let radius = 3;


                                if (
                                    point.index ===
                                    selectedIndex
                                ) {

                                    radius = 7;

                                }


                                if (
                                    point.index ===
                                    hoverIndex
                                ) {

                                    radius = 6;

                                }


                                ctx.beginPath();


                                ctx.arc(
                                    screen.x,
                                    screen.y,
                                    radius,
                                    0,
                                    Math.PI * 2
                                );


                                ctx.fillStyle =
                                    point.index ===
                                    selectedIndex ?
                                    colors.point :
                                    colors.blue;


                                ctx.fill();

                            }
                        );

                    }


                    /* =============================================
                       SELECTED POINT LABEL
                    ============================================== */

                    if (
                        trajectory[selectedIndex]
                    ) {

                        const point =
                            trajectory[
                                selectedIndex
                            ];


                        const screen =
                            pointToScreen(
                                point
                            );


                        ctx.fillStyle =
                            colors.point;

                        ctx.beginPath();

                        ctx.arc(
                            screen.x,
                            screen.y,
                            8,
                            0,
                            Math.PI * 2
                        );

                        ctx.strokeStyle =
                            colors.point;

                        ctx.lineWidth =
                            2;

                        ctx.stroke();

                    }

                }


                /* =================================================
                   UPDATE TOOLTIP
                ================================================== */

                function updateTooltip(
                    point
                ) {

                    if (!point) {

                        tooltip.classList.remove(
                            "visible"
                        );

                        return;

                    }


                    document
                        .getElementById(
                            "tooltipTime"
                        )
                        .textContent =
                        formatNumber(
                            point.t
                        );


                    document
                        .getElementById(
                            "tooltipX"
                        )
                        .textContent =
                        formatNumber(
                            point.x
                        );


                    document
                        .getElementById(
                            "tooltipY"
                        )
                        .textContent =
                        formatNumber(
                            point.y
                        );


                    document
                        .getElementById(
                            "tooltipVx"
                        )
                        .textContent =
                        formatNumber(
                            point.vx
                        );


                    document
                        .getElementById(
                            "tooltipVy"
                        )
                        .textContent =
                        formatNumber(
                            point.vy
                        );


                    document
                        .getElementById(
                            "tooltipSpeed"
                        )
                        .textContent =
                        formatNumber(
                            point.speed
                        );


                    tooltip.classList.add(
                        "visible"
                    );

                }


                /* =================================================
                   GET MOUSE POSITION
                ================================================== */

                function getMousePosition(
                    event
                ) {

                    const rect =
                        canvas.getBoundingClientRect();


                    return {

                        x: event.clientX -
                            rect.left,

                        y: event.clientY -
                            rect.top

                    };

                }


                /* =================================================
                   MOUSE MOVE
                ================================================== */

                canvas.addEventListener(
                    "mousemove",
                    function(event) {

                        const mouse =
                            getMousePosition(
                                event
                            );


                        const nearest =
                            findNearestPoint(
                                mouse.x,
                                mouse.y
                            );


                        hoverIndex =
                            nearest;


                        if (
                            nearest >= 0
                        ) {

                            updateTooltip(
                                trajectory[
                                    nearest
                                ]
                            );

                            canvas.style.cursor =
                                "pointer";

                        } else {

                            tooltip.classList.remove(
                                "visible"
                            );

                            canvas.style.cursor =
                                "crosshair";

                        }


                        if (
                            isDragging &&
                            nearest >= 0
                        ) {

                            selectedIndex =
                                nearest;


                            updateSelectedPoint(
                                selectedIndex
                            );

                            updateTable();

                        }


                        drawGraph();

                    }
                );


                /* =================================================
                   MOUSE LEAVE
                ================================================== */

                canvas.addEventListener(
                    "mouseleave",
                    function() {

                        hoverIndex = -1;

                        tooltip.classList.remove(
                            "visible"
                        );

                        if (
                            !isDragging
                        ) {

                            drawGraph();

                        }

                    }
                );


                /* =================================================
                   MOUSE DOWN
                ================================================== */

                canvas.addEventListener(
                    "mousedown",
                    function(event) {

                        const mouse =
                            getMousePosition(
                                event
                            );


                        const nearest =
                            findNearestPoint(
                                mouse.x,
                                mouse.y
                            );


                        if (
                            nearest >= 0
                        ) {

                            selectedIndex =
                                nearest;


                            isDragging =
                                true;


                            updateSelectedPoint(
                                selectedIndex
                            );


                            updateTable();

                            drawGraph();

                        }

                    }
                );


                /* =================================================
                   MOUSE UP
                ================================================== */

                window.addEventListener(
                    "mouseup",
                    function() {

                        isDragging =
                            false;

                    }
                );


                /* =================================================
                   TOUCH INTERACTION
                ================================================== */

                canvas.addEventListener(
                    "touchstart",
                    function(event) {

                        if (
                            event.touches.length === 0
                        ) {

                            return;

                        }


                        const rect =
                            canvas.getBoundingClientRect();


                        const touch =
                            event.touches[0];


                        const mouse = {

                            x: touch.clientX -
                                rect.left,

                            y: touch.clientY -
                                rect.top

                        };


                        const nearest =
                            findNearestPoint(
                                mouse.x,
                                mouse.y
                            );


                        if (
                            nearest >= 0
                        ) {

                            selectedIndex =
                                nearest;


                            updateSelectedPoint(
                                selectedIndex
                            );


                            updateTable();

                            drawGraph();

                        }

                    }, {
                        passive: true
                    }
                );


                /* =================================================
                   ANGLE DISPLAY
                ================================================== */

                launchAngle.addEventListener(
                    "input",
                    function() {

                        document
                            .getElementById(
                                "angleValue"
                            )
                            .textContent =
                            parseFloat(
                                launchAngle.value
                            ).toFixed(1) +
                            "°";

                    }
                );


                /* =================================================
                   POINT COUNT DISPLAY
                ================================================== */

                pointCount.addEventListener(
                    "input",
                    function() {

                        document
                            .getElementById(
                                "pointCountValue"
                            )
                            .textContent =
                            pointCount.value;

                    }
                );


                /* =================================================
                   AUTO COMPUTE INPUTS
                ================================================== */

                [
                    initialVelocity,
                    launchAngle,
                    initialHeight,
                    gravity,
                    showPoints,
                    showGrid,
                    showElevation,
                    pointCount
                ].forEach(
                    function(element) {

                        element.addEventListener(
                            "input",
                            function() {

                                if (
                                    element ===
                                    launchAngle
                                ) {

                                    document
                                        .getElementById(
                                            "angleValue"
                                        )
                                        .textContent =
                                        parseFloat(
                                            launchAngle.value
                                        ).toFixed(1) +
                                        "°";

                                }


                                if (
                                    element ===
                                    pointCount
                                ) {

                                    document
                                        .getElementById(
                                            "pointCountValue"
                                        )
                                        .textContent =
                                        pointCount.value;

                                }


                                computeProjectile();

                            }
                        );


                        element.addEventListener(
                            "change",
                            function() {

                                computeProjectile();

                            }
                        );

                    }
                );


                /* =================================================
                   COMPUTE BUTTON
                ================================================== */

                document
                    .getElementById(
                        "computeButton"
                    )
                    .addEventListener(
                        "click",
                        function() {

                            computeProjectile();

                        }
                    );


                /* =================================================
                   RESET
                ================================================== */

                document
                    .getElementById(
                        "resetButton"
                    )
                    .addEventListener(
                        "click",
                        function() {

                            initialVelocity.value =
                                "20";


                            launchAngle.value =
                                "45";


                            initialHeight.value =
                                "0";


                            gravity.value =
                                "9.81";


                            showPoints.checked =
                                true;


                            showGrid.checked =
                                true;


                            showElevation.checked =
                                true;


                            pointCount.value =
                                "21";


                            selectedIndex =
                                0;


                            document
                                .getElementById(
                                    "angleValue"
                                )
                                .textContent =
                                "45.0°";


                            document
                                .getElementById(
                                    "pointCountValue"
                                )
                                .textContent =
                                "21";


                            computeProjectile();

                        }
                    );


                /* =================================================
                   THEME CHANGE
                ================================================== */

                const observer =
                    new MutationObserver(
                        function() {

                            drawGraph();

                        }
                    );


                observer.observe(
                    document.documentElement, {

                        attributes: true,

                        attributeFilter: [
                            "data-theme"
                        ]

                    }
                );


                /* =================================================
                   WINDOW RESIZE
                ================================================== */

                window.addEventListener(
                    "resize",
                    function() {

                        resizeCanvas();

                    }
                );


                /* =================================================
                   INITIALIZE
                ================================================== */

                document
                    .getElementById(
                        "angleValue"
                    )
                    .textContent =
                    "45.0°";


                document
                    .getElementById(
                        "pointCountValue"
                    )
                    .textContent =
                    "21";


                resizeCanvas();

                computeProjectile();

            }

        );
    </script>


    <!-- =========================================================
     GLOBAL SCRIPTS
========================================================== -->

    <?php include 'globals/scripts.php'; ?>
  
</body>

</html>