
<?php

/* =========================================================
   PROJECTILE MOTION INTERACTIVE TOOL
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


/* =========================================================
   USER DATA
========================================================== */

$user = $_SESSION["user"];


/* =========================================================
   DATABASE CONNECTION
   Included to maintain the standard ETS-Async structure.
========================================================== */

require_once "../src/connection.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- =====================================================
         STANDARD ETS-ASYNC HEAD
    ====================================================== -->

    <?php include "../globals/head.php"; ?>


    <!-- =====================================================
         MATHJAX
    ====================================================== -->

    <script>
        window.MathJax = {
            tex: {
                inlineMath: [
                    ['\\(', '\\)']
                ],
                displayMath: [
                    ['\\[', '\\]']
                ]
            },
            svg: {
                fontCache: 'global'
            }
        };
    </script>

    <script
        async
        src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js">
    </script>


    <!-- =====================================================
         CHART.JS
    ====================================================== -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <style>
        /* =====================================================
           ROOT VARIABLES
        ====================================================== */

        :root {

            --pm-primary: #0B4F8A;
            --pm-primary-dark: #083B68;
            --pm-secondary: #6C63FF;

            --pm-success: #198754;
            --pm-warning: #f59e0b;
            --pm-danger: #dc3545;

            --pm-bg: #f5f7fb;
            --pm-card: #ffffff;

            --pm-text: #212529;
            --pm-muted: #6c757d;

            --pm-border: #dee2e6;

            --pm-grid: rgba(0, 0, 0, 0.08);

            --pm-radius: 14px;
        }


        /* =====================================================
           DARK MODE
        ====================================================== */

        html[data-theme="dark"] {

            --pm-bg: #111827;
            --pm-card: #1f2937;

            --pm-text: #f3f4f6;
            --pm-muted: #9ca3af;

            --pm-border: #374151;

            --pm-grid: rgba(255, 255, 255, 0.10);
        }


        /* =====================================================
           PAGE BACKGROUND
        ====================================================== */

        body {
            background: var(--pm-bg);
            color: var(--pm-text);
        }


        /* =====================================================
           MAIN CONTENT
        ====================================================== */

        .content-wrapper {
            max-width: 1600px;
            margin: 0 auto;
            padding: 25px;
        }


        /* =====================================================
           PAGE HEADER
        ====================================================== */

        .tool-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 25px;
        }

        .tool-header-icon {

            width: 60px;
            height: 60px;

            border-radius: 15px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: linear-gradient(135deg,
                    var(--pm-primary),
                    var(--pm-secondary));

            color: white;

            font-size: 30px;

            flex-shrink: 0;
        }

        .tool-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .tool-header p {
            margin: 3px 0 0;
            color: var(--pm-muted);
        }


        /* =====================================================
           GENERAL CARD
        ====================================================== */

        .pm-card {

            background: var(--pm-card);

            border: 1px solid var(--pm-border);

            border-radius: var(--pm-radius);

            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);

            margin-bottom: 22px;

            overflow: hidden;
        }

        .pm-card-header {

            padding: 16px 20px;

            border-bottom: 1px solid var(--pm-border);

            display: flex;
            align-items: center;
            gap: 10px;

            font-weight: 700;
        }

        .pm-card-header i {
            color: var(--pm-primary);
            font-size: 20px;
        }

        .pm-card-body {
            padding: 20px;
        }


        /* =====================================================
           INFORMATION CARD
        ====================================================== */

        .info-box {

            border-left: 5px solid var(--pm-primary);

            background: rgba(11, 79, 138, 0.06);

            padding: 18px 20px;

            border-radius: 10px;

            margin-bottom: 22px;
        }

        html[data-theme="dark"] .info-box {
            background: rgba(59, 130, 246, 0.10);
        }

        .info-box strong {
            color: var(--pm-primary);
        }


        /* =====================================================
           INPUT CONTROLS
        ====================================================== */

        .form-label {
            font-weight: 600;
            font-size: 14px;
        }

        .form-control {

            background: var(--pm-card);

            color: var(--pm-text);

            border-color: var(--pm-border);
        }

        .form-control:focus {

            background: var(--pm-card);

            color: var(--pm-text);

            border-color: var(--pm-primary);

            box-shadow: 0 0 0 0.2rem rgba(11, 79, 138, 0.15);
        }


        /* =====================================================
           BUTTONS
        ====================================================== */

        .btn-primary {
            background: var(--pm-primary);
            border-color: var(--pm-primary);
        }

        .btn-primary:hover {
            background: var(--pm-primary-dark);
            border-color: var(--pm-primary-dark);
        }


        /* =====================================================
           RESULT CARDS
        ====================================================== */

        .result-card {

            border: 1px solid var(--pm-border);

            border-radius: 12px;

            padding: 17px;

            height: 100%;

            background: var(--pm-card);
        }

        .result-label {

            font-size: 13px;

            color: var(--pm-muted);

            margin-bottom: 5px;
        }

        .result-value {

            font-size: 24px;

            font-weight: 700;

            color: var(--pm-primary);
        }

        .result-unit {

            font-size: 12px;

            color: var(--pm-muted);
        }


        /* =====================================================
           GRAPH CONTAINER
        ====================================================== */

        .graph-container {

            position: relative;

            width: 100%;

            height: 600px;

            padding: 10px;
        }

        .graph-container canvas {
            width: 100% !important;
            height: 100% !important;
        }


        /* =====================================================
           GRAPH INSTRUCTION
        ====================================================== */

        .graph-instruction {

            padding: 12px 15px;

            background: rgba(11, 79, 138, 0.07);

            border-radius: 9px;

            color: var(--pm-muted);

            font-size: 13px;

            margin-bottom: 15px;
        }

        .graph-instruction i {
            color: var(--pm-primary);
        }


        /* =====================================================
           SELECTED POINT PANEL
        ====================================================== */

        .selected-point {

            border: 1px solid var(--pm-border);

            border-radius: 12px;

            padding: 18px;

            background: var(--pm-card);

            height: 100%;
        }

        .selected-point-title {

            font-weight: 700;

            margin-bottom: 15px;

            color: var(--pm-primary);
        }

        .selected-value-row {

            display: flex;

            justify-content: space-between;

            padding: 7px 0;

            border-bottom: 1px dashed var(--pm-border);

            font-size: 14px;
        }

        .selected-value-row:last-child {
            border-bottom: none;
        }

        .selected-value-label {
            color: var(--pm-muted);
        }

        .selected-value-number {
            font-weight: 600;
        }


        /* =====================================================
           TIME SLIDER
        ====================================================== */

        .time-slider {

            width: 100%;

            accent-color: var(--pm-primary);

            cursor: pointer;
        }

        .time-display {

            text-align: center;

            font-size: 20px;

            font-weight: 700;

            color: var(--pm-primary);

            margin-bottom: 8px;
        }


        /* =====================================================
           TABLE
        ====================================================== */

        .table-responsive {
            border-radius: 10px;
            overflow-x: auto;
        }

        .table {

            color: var(--pm-text);

            margin-bottom: 0;
        }

        .table thead th {

            background: var(--pm-primary);

            color: white;

            border-color: var(--pm-primary);

            white-space: nowrap;
        }

        .table tbody td {

            border-color: var(--pm-border);

            vertical-align: middle;
        }


        /* =====================================================
           EQUATION CARDS
        ====================================================== */

        .equation-box {

            background: rgba(11, 79, 138, 0.05);

            border: 1px solid var(--pm-border);

            border-radius: 10px;

            padding: 18px;

            margin-bottom: 15px;

            overflow-x: auto;
        }

        html[data-theme="dark"] .equation-box {
            background: rgba(255, 255, 255, 0.03);
        }

        .equation-title {

            font-weight: 700;

            color: var(--pm-primary);

            margin-bottom: 10px;
        }


        /* =====================================================
           CONCEPT CARDS
        ====================================================== */

        .concept-card {

            border: 1px solid var(--pm-border);

            border-radius: 12px;

            padding: 18px;

            height: 100%;
        }

        .concept-icon {

            width: 42px;
            height: 42px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 10px;

            background: rgba(11, 79, 138, 0.10);

            color: var(--pm-primary);

            font-size: 20px;

            margin-bottom: 12px;
        }

        .concept-title {
            font-weight: 700;
            margin-bottom: 7px;
        }

        .concept-text {
            color: var(--pm-muted);
            font-size: 14px;
            line-height: 1.6;
        }


        /* =====================================================
           ERROR MESSAGE
        ====================================================== */

        .tool-error {

            background: rgba(220, 53, 69, 0.10);

            border: 1px solid rgba(220, 53, 69, 0.25);

            color: var(--pm-danger);

            padding: 12px 15px;

            border-radius: 9px;

            margin-top: 15px;
        }


        /* =====================================================
           RESPONSIVE DESIGN
        ====================================================== */

        @media (max-width: 991px) {

            .content-wrapper {
                padding: 18px;
            }

            .graph-container {
                height: 450px;
            }

        }


        @media (max-width: 576px) {

            .content-wrapper {
                padding: 12px;
            }

            .tool-header h1 {
                font-size: 22px;
            }

            .tool-header-icon {

                width: 50px;
                height: 50px;

                font-size: 24px;
            }

            .graph-container {
                height: 380px;
            }

            .result-value {
                font-size: 20px;
            }

        }
    </style>

</head>


<body>


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <?php include "../globals/sidebar.php"; ?>


    <!-- =====================================================
         TOPBAR
    ====================================================== -->

    <?php include "../globals/topbar.php"; ?>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">

        <div class="content-wrapper">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="tool-header">

                <div class="tool-header-icon">

                    <i class="bi bi-rocket-takeoff"></i>

                </div>

                <div>

                    <h1>Projectile Motion</h1>

                    <p>
                        Interactive two-dimensional kinematics and trajectory analysis
                    </p>

                </div>

            </div>


            <!-- =================================================
                 INFORMATION
            ================================================== -->

            <div class="info-box">

                <strong>
                    <i class="bi bi-info-circle"></i>
                    Projectile Motion Reference
                </strong>

                <p class="mb-0 mt-2">

                    Projectile motion describes the motion of an object launched
                    into the air under the influence of gravity. This tool assumes
                    negligible air resistance and constant gravitational acceleration.

                    The horizontal and vertical components of motion are treated
                    independently.

                </p>

            </div>


            <!-- =================================================
                 PROJECTILE PARAMETERS
            ================================================== -->

            <div class="pm-card">

                <div class="pm-card-header">

                    <i class="bi bi-sliders"></i>

                    Projectile Parameters

                </div>

                <div class="pm-card-body">

                    <div class="row g-3">


                        <!-- INITIAL VELOCITY -->

                        <div class="col-md-3">

                            <label
                                for="velocityInput"
                                class="form-label">

                                Initial Velocity \(v_0\)

                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    class="form-control"
                                    id="velocityInput"
                                    value="20"
                                    min="0"
                                    step="0.1">

                                <span class="input-group-text">
                                    m/s
                                </span>

                            </div>

                        </div>


                        <!-- LAUNCH ANGLE -->

                        <div class="col-md-3">

                            <label
                                for="angleInput"
                                class="form-label">

                                Launch Angle \(\theta\)

                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    class="form-control"
                                    id="angleInput"
                                    value="45"
                                    min="0"
                                    max="90"
                                    step="0.1">

                                <span class="input-group-text">
                                    °
                                </span>

                            </div>

                        </div>


                        <!-- INITIAL ELEVATION -->

                        <div class="col-md-3">

                            <label
                                for="heightInput"
                                class="form-label">

                                Initial Elevation \(y_0\)

                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    class="form-control"
                                    id="heightInput"
                                    value="0"
                                    min="0"
                                    step="0.1">

                                <span class="input-group-text">
                                    m
                                </span>

                            </div>

                        </div>


                        <!-- GRAVITY -->

                        <div class="col-md-3">

                            <label
                                for="gravityInput"
                                class="form-label">

                                Gravity \(g\)

                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    class="form-control"
                                    id="gravityInput"
                                    value="9.81"
                                    min="0.01"
                                    step="0.01">

                                <span class="input-group-text">
                                    m/s²
                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- ERROR -->

                    <div
                        id="errorMessage"
                        class="tool-error d-none">

                        <i class="bi bi-exclamation-triangle"></i>

                        <span id="errorText"></span>

                    </div>


                    <div class="mt-4 d-flex flex-wrap gap-2">

                        <button
                            type="button"
                            class="btn btn-primary"
                            id="calculateButton">

                            <i class="bi bi-calculator"></i>

                            Calculate

                        </button>


                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            id="resetButton">

                            <i class="bi bi-arrow-counterclockwise"></i>

                            Reset

                        </button>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 PRIMARY RESULTS
            ================================================== -->

            <div class="row g-3 mb-4">


                <div class="col-md-3">

                    <div class="result-card">

                        <div class="result-label">
                            Total Flight Time
                        </div>

                        <div
                            class="result-value"
                            id="flightTimeResult">
                            0.000
                        </div>

                        <div class="result-unit">
                            seconds
                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="result-card">

                        <div class="result-label">
                            Maximum Elevation
                        </div>

                        <div
                            class="result-value"
                            id="maxHeightResult">
                            0.000
                        </div>

                        <div class="result-unit">
                            meters
                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="result-card">

                        <div class="result-label">
                            Horizontal Range
                        </div>

                        <div
                            class="result-value"
                            id="rangeResult">
                            0.000
                        </div>

                        <div class="result-unit">
                            meters
                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="result-card">

                        <div class="result-label">
                            Time to Maximum Height
                        </div>

                        <div
                            class="result-value"
                            id="maxTimeResult">
                            0.000
                        </div>

                        <div class="result-unit">
                            seconds
                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 VELOCITY COMPONENTS
            ================================================== -->

            <div class="row g-3 mb-4">


                <div class="col-md-3">

                    <div class="result-card">

                        <div class="result-label">
                            Horizontal Velocity \(v_x\)
                        </div>

                        <div
                            class="result-value"
                            id="vxResult">
                            0.000
                        </div>

                        <div class="result-unit">
                            m/s
                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="result-card">

                        <div class="result-label">
                            Initial Vertical Velocity \(v_y\)
                        </div>

                        <div
                            class="result-value"
                            id="vyResult">
                            0.000
                        </div>

                        <div class="result-unit">
                            m/s
                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="result-card">

                        <div class="result-label">
                            Impact Speed
                        </div>

                        <div
                            class="result-value"
                            id="impactSpeedResult">
                            0.000
                        </div>

                        <div class="result-unit">
                            m/s
                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="result-card">

                        <div class="result-label">
                            Impact Angle
                        </div>

                        <div
                            class="result-value"
                            id="impactAngleResult">
                            0.000
                        </div>

                        <div class="result-unit">
                            degrees
                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 INTERACTIVE GRAPH
            ================================================== -->

            <div class="pm-card">

                <div class="pm-card-header">

                    <i class="bi bi-graph-up-arrow"></i>

                    Interactive Trajectory

                </div>

                <div class="pm-card-body">


                    <div class="graph-instruction">

                        <i class="bi bi-hand-index-thumb"></i>

                        <strong>Interaction:</strong>

                        Use the time slider to move the projectile.
                        Click directly on the trajectory to inspect a point.
                        The selected point displays its elevation, horizontal
                        distance, velocity components, and instantaneous speed.

                    </div>


                    <!-- GRAPH -->

                    <div class="graph-container">

                        <canvas id="trajectoryChart"></canvas>

                    </div>


                    <!-- TIME CONTROL -->

                    <div class="mt-3">

                        <div class="time-display">

                            \(t=\)

                            <span id="currentTimeDisplay">
                                0.000
                            </span>

                            s

                        </div>


                        <input
                            type="range"
                            class="time-slider"
                            id="timeSlider"
                            min="0"
                            max="1"
                            step="0.001"
                            value="0">

                    </div>


                    <!-- ANIMATION CONTROLS -->

                    <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">

                        <button
                            type="button"
                            class="btn btn-primary"
                            id="playButton">

                            <i class="bi bi-play-fill"></i>

                            Play

                        </button>


                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            id="pauseButton">

                            <i class="bi bi-pause-fill"></i>

                            Pause

                        </button>


                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            id="animationResetButton">

                            <i class="bi bi-arrow-counterclockwise"></i>

                            Reset Position

                        </button>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 SELECTED POINT INFORMATION
            ================================================== -->

            <div class="pm-card">

                <div class="pm-card-header">

                    <i class="bi bi-crosshair"></i>

                    Selected Point Analysis

                </div>

                <div class="pm-card-body">

                    <div class="row g-4">


                        <!-- POINT DATA -->

                        <div class="col-lg-6">

                            <div class="selected-point">

                                <div class="selected-point-title">

                                    <i class="bi bi-geo-alt"></i>

                                    Projectile Position

                                </div>


                                <div class="selected-value-row">

                                    <span class="selected-value-label">
                                        Time \(t\)
                                    </span>

                                    <span
                                        class="selected-value-number"
                                        id="selectedTime">
                                        0.000 s
                                    </span>

                                </div>


                                <div class="selected-value-row">

                                    <span class="selected-value-label">
                                        Horizontal Position \(x\)
                                    </span>

                                    <span
                                        class="selected-value-number"
                                        id="selectedX">
                                        0.000 m
                                    </span>

                                </div>


                                <div class="selected-value-row">

                                    <span class="selected-value-label">
                                        Elevation \(y\)
                                    </span>

                                    <span
                                        class="selected-value-number"
                                        id="selectedY">
                                        0.000 m
                                    </span>

                                </div>


                                <div class="selected-value-row">

                                    <span class="selected-value-label">
                                        Horizontal Velocity \(v_x\)
                                    </span>

                                    <span
                                        class="selected-value-number"
                                        id="selectedVx">
                                        0.000 m/s
                                    </span>

                                </div>


                                <div class="selected-value-row">

                                    <span class="selected-value-label">
                                        Vertical Velocity \(v_y\)
                                    </span>

                                    <span
                                        class="selected-value-number"
                                        id="selectedVy">
                                        0.000 m/s
                                    </span>

                                </div>


                                <div class="selected-value-row">

                                    <span class="selected-value-label">
                                        Instantaneous Speed
                                    </span>

                                    <span
                                        class="selected-value-number"
                                        id="selectedSpeed">
                                        0.000 m/s
                                    </span>

                                </div>


                                <div class="selected-value-row">

                                    <span class="selected-value-label">
                                        Direction of Velocity
                                    </span>

                                    <span
                                        class="selected-value-number"
                                        id="selectedAngle">
                                        0.000°
                                    </span>

                                </div>

                            </div>

                        </div>


                        <!-- COMPONENT DESCRIPTION -->

                        <div class="col-lg-6">

                            <div class="selected-point">

                                <div class="selected-point-title">

                                    <i class="bi bi-arrows"></i>

                                    Motion Components

                                </div>


                                <p class="mb-3">

                                    At the selected point, the velocity vector
                                    is divided into independent horizontal and
                                    vertical components.

                                </p>


                                <div class="equation-box">

                                    \[
                                    v_x=v_0\cos(\theta)
                                    \]

                                </div>


                                <div class="equation-box">

                                    \[
                                    v_y=v_0\sin(\theta)-gt
                                    \]

                                </div>


                                <div class="equation-box mb-0">

                                    \[
                                    v=\sqrt{v_x^2+v_y^2}
                                    \]

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 TRAJECTORY DATA TABLE
            ================================================== -->

            <div class="pm-card">

                <div class="pm-card-header">

                    <i class="bi bi-table"></i>

                    Trajectory Data

                </div>

                <div class="pm-card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered">

                            <thead>

                                <tr>

                                    <th>Point</th>

                                    <th>Time \(t\) (s)</th>

                                    <th>Position \(x\) (m)</th>

                                    <th>Elevation \(y\) (m)</th>

                                    <th>\(v_x\) (m/s)</th>

                                    <th>\(v_y\) (m/s)</th>

                                    <th>Speed (m/s)</th>

                                </tr>

                            </thead>

                            <tbody id="trajectoryTableBody"></tbody>

                        </table>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 EQUATIONS
            ================================================== -->

            <div class="pm-card">

                <div class="pm-card-header">

                    <i class="bi bi-function"></i>

                    Equations Used

                </div>

                <div class="pm-card-body">


                    <div class="row g-3">


                        <!-- COMPONENTS -->

                        <div class="col-lg-6">

                            <div class="equation-box">

                                <div class="equation-title">
                                    Initial Velocity Components
                                </div>

                                \[
                                v_{x0}=v_0\cos(\theta)
                                \]

                                \[
                                v_{y0}=v_0\sin(\theta)
                                \]

                            </div>

                        </div>


                        <!-- POSITION -->

                        <div class="col-lg-6">

                            <div class="equation-box">

                                <div class="equation-title">
                                    Position Equations
                                </div>

                                \[
                                x(t)=v_0\cos(\theta)t
                                \]

                                \[
                                y(t)=y_0+
                                v_0\sin(\theta)t-
                                \frac{1}{2}gt^2
                                \]

                            </div>

                        </div>


                        <!-- VELOCITY -->

                        <div class="col-lg-6">

                            <div class="equation-box">

                                <div class="equation-title">
                                    Velocity Equations
                                </div>

                                \[
                                v_x=v_0\cos(\theta)
                                \]

                                \[
                                v_y=v_0\sin(\theta)-gt
                                \]

                            </div>

                        </div>


                        <!-- FLIGHT TIME -->

                        <div class="col-lg-6">

                            <div class="equation-box">

                                <div class="equation-title">
                                    Flight Time
                                </div>

                                \[
                                t_f=
                                \frac{
                                v_{y0}+
                                \sqrt{
                                v_{y0}^{2}+2gy_0
                                }
                                }{g}
                                \]

                            </div>

                        </div>


                        <!-- MAX HEIGHT -->

                        <div class="col-lg-6">

                            <div class="equation-box">

                                <div class="equation-title">
                                    Maximum Elevation
                                </div>

                                \[
                                y_{\max}
                                =
                                y_0+
                                \frac{v_{y0}^{2}}{2g}
                                \]

                            </div>

                        </div>


                        <!-- RANGE -->

                        <div class="col-lg-6">

                            <div class="equation-box">

                                <div class="equation-title">
                                    Horizontal Range
                                </div>

                                \[
                                R=v_x t_f
                                \]

                            </div>

                        </div>


                        <!-- TRAJECTORY -->

                        <div class="col-12">

                            <div class="equation-box">

                                <div class="equation-title">
                                    Trajectory Equation
                                </div>

                                \[
                                y(x)
                                =
                                y_0+
                                x\tan(\theta)
                                -
                                \frac{
                                gx^2
                                }{
                                2v_0^2\cos^2(\theta)
                                }
                                \]

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 GROUND-LEVEL SPECIAL CASE
            ================================================== -->

            <div class="pm-card">

                <div class="pm-card-header">

                    <i class="bi bi-book"></i>

                    Ground-Level Launch \((y_0=0)\)

                </div>

                <div class="pm-card-body">

                    <p>

                        When the projectile is launched and lands at the same
                        elevation, the equations simplify to:

                    </p>


                    <div class="row g-3">


                        <div class="col-md-6">

                            <div class="equation-box">

                                \[
                                T=
                                \frac{
                                2v_0\sin(\theta)
                                }{g}
                                \]

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="equation-box">

                                \[
                                H_{\max}
                                =
                                \frac{
                                v_0^2\sin^2(\theta)
                                }{
                                2g
                                }
                                \]

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="equation-box">

                                \[
                                R=
                                \frac{
                                v_0^2\sin(2\theta)
                                }{g}
                                \]

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="equation-box">

                                \[
                                R_{\max}
                                =
                                \frac{v_0^2}{g}
                                \]

                                <br>

                                at

                                \[
                                \theta=45^\circ
                                \]

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 KEY CONCEPTS
            ================================================== -->

            <div class="pm-card">

                <div class="pm-card-header">

                    <i class="bi bi-lightbulb"></i>

                    Key Concepts

                </div>

                <div class="pm-card-body">

                    <div class="row g-3">


                        <div class="col-md-3">

                            <div class="concept-card">

                                <div class="concept-icon">

                                    <i class="bi bi-arrow-right"></i>

                                </div>

                                <div class="concept-title">

                                    Horizontal Motion

                                </div>

                                <div class="concept-text">

                                    Horizontal acceleration is zero when
                                    air resistance is neglected.

                                    \[
                                    a_x=0
                                    \]

                                </div>

                            </div>

                        </div>


                        <div class="col-md-3">

                            <div class="concept-card">

                                <div class="concept-icon">

                                    <i class="bi bi-arrow-down"></i>

                                </div>

                                <div class="concept-title">

                                    Vertical Motion

                                </div>

                                <div class="concept-text">

                                    The projectile experiences a constant
                                    downward acceleration.

                                    \[
                                    a_y=-g
                                    \]

                                </div>

                            </div>

                        </div>


                        <div class="col-md-3">

                            <div class="concept-card">

                                <div class="concept-icon">

                                    <i class="bi bi-diagram-3"></i>

                                </div>

                                <div class="concept-title">

                                    Independent Components

                                </div>

                                <div class="concept-text">

                                    Horizontal and vertical motions can be
                                    analyzed independently and combined
                                    vectorially.

                                </div>

                            </div>

                        </div>


                        <div class="col-md-3">

                            <div class="concept-card">

                                <div class="concept-icon">

                                    <i class="bi bi-symmetry-horizontal"></i>

                                </div>

                                <div class="concept-title">

                                    Symmetry

                                </div>

                                <div class="concept-text">

                                    The trajectory is symmetric about the
                                    maximum-height point only when launch
                                    and landing elevations are equal.

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


        </div>

    </main>


    <!-- =====================================================
         PROJECTILE MOTION JAVASCRIPT
    ====================================================== -->

    <script>
        /* =====================================================
           GLOBAL VARIABLES
        ====================================================== */

        let trajectoryChart = null;

        let animationFrame = null;

        let isAnimating = false;

        let projectileData = {

            v0: 20,

            angle: 45,

            y0: 0,

            g: 9.81,

            vx0: 0,

            vy0: 0,

            flightTime: 0,

            maxHeight: 0,

            maxTime: 0,

            range: 0

        };


        /* =====================================================
           DOM ELEMENTS
        ====================================================== */

        const velocityInput =
            document.getElementById("velocityInput");

        const angleInput =
            document.getElementById("angleInput");

        const heightInput =
            document.getElementById("heightInput");

        const gravityInput =
            document.getElementById("gravityInput");

        const timeSlider =
            document.getElementById("timeSlider");

        const currentTimeDisplay =
            document.getElementById("currentTimeDisplay");

        const errorMessage =
            document.getElementById("errorMessage");

        const errorText =
            document.getElementById("errorText");

        const trajectoryTableBody =
            document.getElementById("trajectoryTableBody");


        /* =====================================================
           FORMAT NUMBER
        ====================================================== */

        function formatNumber(value, decimals = 3) {

            if (!Number.isFinite(value)) {
                return "0.000";
            }

            return Number(value).toFixed(decimals);

        }


        /* =====================================================
           SHOW ERROR
        ====================================================== */

        function showError(message) {

            errorText.textContent = message;

            errorMessage.classList.remove("d-none");

        }


        /* =====================================================
           HIDE ERROR
        ====================================================== */

        function hideError() {

            errorMessage.classList.add("d-none");

        }


        /* =====================================================
           CALCULATE PROJECTILE
        ====================================================== */

        function calculateProjectile() {

            hideError();

            stopAnimation();


            const v0 =
                parseFloat(velocityInput.value);

            const angle =
                parseFloat(angleInput.value);

            const y0 =
                parseFloat(heightInput.value);

            const g =
                parseFloat(gravityInput.value);


            /* -----------------------------------------------
               VALIDATION
            ------------------------------------------------ */

            if (
                !Number.isFinite(v0) ||
                !Number.isFinite(angle) ||
                !Number.isFinite(y0) ||
                !Number.isFinite(g)
            ) {

                showError(
                    "Please enter valid numerical values."
                );

                return;

            }


            if (v0 < 0) {

                showError(
                    "Initial velocity cannot be negative."
                );

                return;

            }


            if (angle < 0 || angle > 90) {

                showError(
                    "Launch angle must be between 0° and 90°."
                );

                return;

            }


            if (y0 < 0) {

                showError(
                    "Initial elevation cannot be negative."
                );

                return;

            }


            if (g <= 0) {

                showError(
                    "Gravitational acceleration must be greater than zero."
                );

                return;

            }


            /* -----------------------------------------------
               CONVERT ANGLE TO RADIANS
            ------------------------------------------------ */

            const theta =
                angle * Math.PI / 180;


            /* -----------------------------------------------
               VELOCITY COMPONENTS
            ------------------------------------------------ */

            const vx0 =
                v0 * Math.cos(theta);

            const vy0 =
                v0 * Math.sin(theta);


            /* -----------------------------------------------
               FLIGHT TIME
               
               y = y0 + vy0*t - 1/2*g*t²
               
               Positive root:
               
               t = [vy0 + sqrt(vy0² + 2gy0)] / g
            ------------------------------------------------ */

            const discriminant =
                vy0 * vy0 + 2 * g * y0;


            if (discriminant < 0) {

                showError(
                    "The projectile does not intersect the ground."
                );

                return;

            }


            let flightTime =
                (
                    vy0 +
                    Math.sqrt(discriminant)
                ) / g;


            if (!Number.isFinite(flightTime)) {
                flightTime = 0;
            }


            /* -----------------------------------------------
               TIME TO MAXIMUM HEIGHT
            ------------------------------------------------ */

            let maxTime =
                vy0 / g;


            if (maxTime < 0) {
                maxTime = 0;
            }


            /* -----------------------------------------------
               MAXIMUM HEIGHT
            ------------------------------------------------ */

            let maxHeight =
                y0 +
                (vy0 * vy0) / (2 * g);


            if (!Number.isFinite(maxHeight)) {
                maxHeight = y0;
            }


            /* -----------------------------------------------
               HORIZONTAL RANGE
            ------------------------------------------------ */

            const range =
                vx0 * flightTime;


            /* -----------------------------------------------
               SAVE DATA
            ------------------------------------------------ */

            projectileData = {

                v0: v0,

                angle: angle,

                y0: y0,

                g: g,

                vx0: vx0,

                vy0: vy0,

                flightTime: flightTime,

                maxHeight: maxHeight,

                maxTime: maxTime,

                range: range

            };


            /* -----------------------------------------------
               UPDATE RESULTS
            ------------------------------------------------ */

            document.getElementById(
                    "flightTimeResult"
                ).textContent =
                formatNumber(flightTime);


            document.getElementById(
                    "maxHeightResult"
                ).textContent =
                formatNumber(maxHeight);


            document.getElementById(
                    "rangeResult"
                ).textContent =
                formatNumber(range);


            document.getElementById(
                    "maxTimeResult"
                ).textContent =
                formatNumber(maxTime);


            document.getElementById(
                    "vxResult"
                ).textContent =
                formatNumber(vx0);


            document.getElementById(
                    "vyResult"
                ).textContent =
                formatNumber(vy0);


            /* -----------------------------------------------
               IMPACT VELOCITY
            ------------------------------------------------ */

            const impactVy =
                vy0 - g * flightTime;


            const impactSpeed =
                Math.sqrt(
                    vx0 * vx0 +
                    impactVy * impactVy
                );


            const impactAngle =
                Math.atan2(
                    impactVy,
                    vx0
                ) * 180 / Math.PI;


            document.getElementById(
                    "impactSpeedResult"
                ).textContent =
                formatNumber(impactSpeed);


            document.getElementById(
                    "impactAngleResult"
                ).textContent =
                formatNumber(impactAngle);


            /* -----------------------------------------------
               TIME SLIDER
            ------------------------------------------------ */

            timeSlider.max =
                flightTime;

            timeSlider.value = 0;


            /* -----------------------------------------------
               DRAW GRAPH
            ------------------------------------------------ */

            buildTrajectoryChart();

            buildTrajectoryTable();

            updateSelectedPoint(0);

        }


        /* =====================================================
           CALCULATE POSITION
        ====================================================== */

        function getPositionAtTime(t) {

            const data = projectileData;


            const x =
                data.vx0 * t;


            let y =
                data.y0 +
                data.vy0 * t -
                0.5 * data.g * t * t;


            /* Prevent tiny floating-point negative values */

            if (
                Math.abs(y) < 0.000001
            ) {
                y = 0;
            }


            return {

                x: x,

                y: y

            };

        }


        /* =====================================================
           CALCULATE VELOCITY
        ====================================================== */

        function getVelocityAtTime(t) {

            const data = projectileData;


            const vx =
                data.vx0;


            const vy =
                data.vy0 -
                data.g * t;


            const speed =
                Math.sqrt(
                    vx * vx +
                    vy * vy
                );


            const angle =
                Math.atan2(
                    vy,
                    vx
                ) * 180 / Math.PI;


            return {

                vx: vx,

                vy: vy,

                speed: speed,

                angle: angle

            };

        }


        /* =====================================================
           GENERATE TRAJECTORY POINTS
        ====================================================== */

        function generateTrajectoryPoints() {

            const points = [];

            const numberOfPoints = 101;


            for (
                let i = 0; i < numberOfPoints; i++
            ) {

                const t =
                    projectileData.flightTime *
                    i /
                    (numberOfPoints - 1);


                const position =
                    getPositionAtTime(t);


                points.push({

                    x: position.x,

                    y: position.y,

                    t: t

                });

            }


            return points;

        }


        /* =====================================================
           BUILD CHART
        ====================================================== */

        function buildTrajectoryChart() {

            const canvas =
                document.getElementById(
                    "trajectoryChart"
                );


            if (trajectoryChart) {

                trajectoryChart.destroy();

                trajectoryChart = null;

            }


            const points =
                generateTrajectoryPoints();


            const trajectoryCoordinates =
                points.map(point => ({

                    x: point.x,

                    y: point.y

                }));


            const launchPoint = {

                x: 0,

                y: projectileData.y0

            };


            const apexPoint = {

                x: projectileData.vx0 *
                    projectileData.maxTime,

                y: projectileData.maxHeight

            };


            const landingPoint = {

                x: projectileData.range,

                y: 0

            };


            const selectedPosition =
                getPositionAtTime(0);


            const ctx =
                canvas.getContext("2d");


            trajectoryChart =
                new Chart(
                    ctx, {

                        type: "scatter",

                        data: {

                            datasets: [

                                /* --------------------------------
                                   TRAJECTORY
                                --------------------------------- */

                                {

                                    label: "Projectile Trajectory",

                                    data: trajectoryCoordinates,

                                    showLine: true,

                                    borderWidth: 3,

                                    pointRadius: 0,

                                    tension: 0.1

                                },


                                /* --------------------------------
                                   LAUNCH POINT
                                --------------------------------- */

                                {

                                    label: "Launch Point",

                                    data: [launchPoint],

                                    pointRadius: 7,

                                    pointHoverRadius: 10,

                                    showLine: false

                                },


                                /* --------------------------------
                                   APEX
                                --------------------------------- */

                                {

                                    label: "Maximum Height",

                                    data: [apexPoint],

                                    pointRadius: 7,

                                    pointHoverRadius: 10,

                                    showLine: false

                                },


                                /* --------------------------------
                                   LANDING
                                --------------------------------- */

                                {

                                    label: "Landing Point",

                                    data: [landingPoint],

                                    pointRadius: 7,

                                    pointHoverRadius: 10,

                                    showLine: false

                                },


                                /* --------------------------------
                                   CURRENT PROJECTILE
                                --------------------------------- */

                                {

                                    label: "Current Position",

                                    data: [selectedPosition],

                                    pointRadius: 9,

                                    pointHoverRadius: 12,

                                    showLine: false

                                }

                            ]

                        },

                        options: {

                            responsive: true,

                            maintainAspectRatio: false,

                            animation: false,

                            interaction: {

                                mode: "nearest",

                                intersect: false

                            },

                            plugins: {

                                legend: {

                                    position: "bottom"

                                },

                                tooltip: {

                                    callbacks: {

                                        label: function(context) {

                                            const x =
                                                context.parsed.x;

                                            const y =
                                                context.parsed.y;

                                            return [
                                                "x = " +
                                                formatNumber(x) +
                                                " m",

                                                "y = " +
                                                formatNumber(y) +
                                                " m"
                                            ];

                                        }

                                    }

                                }

                            },

                            scales: {

                                x: {

                                    type: "linear",

                                    title: {

                                        display: true,

                                        text: "Horizontal Distance x (m)"

                                    },

                                    grid: {

                                        color: getGridColor()

                                    },

                                    beginAtZero: true

                                },

                                y: {

                                    title: {

                                        display: true,

                                        text: "Elevation y (m)"

                                    },

                                    grid: {

                                        color: getGridColor()

                                    },

                                    beginAtZero: true

                                }

                            }

                        },

                        plugins: [

                            {

                                id: "projectileGuides",

                                afterDraw: function(chart) {

                                    drawProjectileGuides(
                                        chart
                                    );

                                }

                            }

                        ]

                    }

                );


            /* -----------------------------------------------
               CLICK ON GRAPH
            ------------------------------------------------ */

            canvas.onclick =
                function(event) {

                    if (!trajectoryChart) {
                        return;
                    }


                    const elements =
                        trajectoryChart.getElementsAtEventForMode(
                            event,
                            "nearest", {
                                intersect: false
                            },
                            false
                        );


                    if (
                        elements.length === 0
                    ) {
                        return;
                    }


                    const element =
                        elements[0];


                    /* Only trajectory dataset */

                    if (
                        element.datasetIndex !== 0
                    ) {
                        return;
                    }


                    const index =
                        element.index;


                    const point =
                        points[index];


                    timeSlider.value =
                        point.t;


                    updateSelectedPoint(
                        point.t
                    );

                };

        }


        /* =====================================================
           DRAW GRAPH GUIDES
        ====================================================== */

        function drawProjectileGuides(chart) {

            const ctx =
                chart.ctx;


            const t =
                parseFloat(
                    timeSlider.value
                );


            const position =
                getPositionAtTime(t);


            const velocity =
                getVelocityAtTime(t);


            const xScale =
                chart.scales.x;


            const yScale =
                chart.scales.y;


            const px =
                xScale.getPixelForValue(
                    position.x
                );


            const py =
                yScale.getPixelForValue(
                    position.y
                );


            const groundY =
                yScale.getPixelForValue(0);


            const axisX =
                xScale.getPixelForValue(0);


            ctx.save();


            /* -----------------------------------------------
               ELEVATION GUIDE
            ------------------------------------------------ */

            ctx.setLineDash([6, 6]);

            ctx.lineWidth = 1.5;

            ctx.strokeStyle =
                getGuideColor();


            ctx.beginPath();

            ctx.moveTo(
                px,
                py
            );

            ctx.lineTo(
                px,
                groundY
            );

            ctx.stroke();


            /* -----------------------------------------------
               HORIZONTAL GUIDE
            ------------------------------------------------ */

            ctx.beginPath();

            ctx.moveTo(
                axisX,
                py
            );

            ctx.lineTo(
                px,
                py
            );

            ctx.stroke();


            ctx.setLineDash([]);


            /* -----------------------------------------------
               VELOCITY VECTOR
            ------------------------------------------------ */

            const vectorScale = 10;


            const vxEnd =
                px +
                velocity.vx *
                vectorScale;


            const vyEnd =
                py -
                velocity.vy *
                vectorScale;


            ctx.strokeStyle =
                getVectorColor();

            ctx.lineWidth =
                2.5;


            ctx.beginPath();

            ctx.moveTo(
                px,
                py
            );

            ctx.lineTo(
                vxEnd,
                vyEnd
            );

            ctx.stroke();


            /* -----------------------------------------------
               VECTOR ARROWHEAD
            ------------------------------------------------ */

            const arrowLength = 8;

            const vectorAngle =
                Math.atan2(
                    vyEnd - py,
                    vxEnd - px
                );


            ctx.beginPath();

            ctx.moveTo(
                vxEnd,
                vyEnd
            );

            ctx.lineTo(
                vxEnd -
                arrowLength *
                Math.cos(vectorAngle - Math.PI / 6),

                vyEnd -
                arrowLength *
                Math.sin(vectorAngle - Math.PI / 6)
            );

            ctx.lineTo(
                vxEnd -
                arrowLength *
                Math.cos(vectorAngle + Math.PI / 6),

                vyEnd -
                arrowLength *
                Math.sin(vectorAngle + Math.PI / 6)
            );

            ctx.closePath();

            ctx.fillStyle =
                getVectorColor();

            ctx.fill();


            ctx.restore();

        }


        /* =====================================================
           UPDATE SELECTED POINT
        ====================================================== */

        function updateSelectedPoint(t) {

            const position =
                getPositionAtTime(t);


            const velocity =
                getVelocityAtTime(t);


            currentTimeDisplay.textContent =
                formatNumber(t);


            document.getElementById(
                    "selectedTime"
                ).textContent =
                formatNumber(t) +
                " s";


            document.getElementById(
                    "selectedX"
                ).textContent =
                formatNumber(position.x) +
                " m";


            document.getElementById(
                    "selectedY"
                ).textContent =
                formatNumber(position.y) +
                " m";


            document.getElementById(
                    "selectedVx"
                ).textContent =
                formatNumber(velocity.vx) +
                " m/s";


            document.getElementById(
                    "selectedVy"
                ).textContent =
                formatNumber(velocity.vy) +
                " m/s";


            document.getElementById(
                    "selectedSpeed"
                ).textContent =
                formatNumber(velocity.speed) +
                " m/s";


            document.getElementById(
                    "selectedAngle"
                ).textContent =
                formatNumber(velocity.angle) +
                "°";


            /* -----------------------------------------------
               UPDATE CURRENT POINT DATASET
            ------------------------------------------------ */

            if (trajectoryChart) {

                trajectoryChart.data.datasets[4].data = [{
                    x: position.x,
                    y: position.y
                }];


                trajectoryChart.update(
                    "none"
                );

            }

        }


        /* =====================================================
           BUILD TRAJECTORY TABLE
        ====================================================== */

        function buildTrajectoryTable() {

            trajectoryTableBody.innerHTML = "";


            const numberOfRows = 21;


            for (
                let i = 0; i < numberOfRows; i++
            ) {

                const t =
                    projectileData.flightTime *
                    i /
                    (numberOfRows - 1);


                const position =
                    getPositionAtTime(t);


                const velocity =
                    getVelocityAtTime(t);


                const row =
                    document.createElement("tr");


                row.innerHTML = `

                    <td>
                        ${i + 1}
                    </td>

                    <td>
                        ${formatNumber(t)}
                    </td>

                    <td>
                        ${formatNumber(position.x)}
                    </td>

                    <td>
                        ${formatNumber(position.y)}
                    </td>

                    <td>
                        ${formatNumber(velocity.vx)}
                    </td>

                    <td>
                        ${formatNumber(velocity.vy)}
                    </td>

                    <td>
                        ${formatNumber(velocity.speed)}
                    </td>

                `;


                trajectoryTableBody.appendChild(row);

            }

        }


        /* =====================================================
           PLAY ANIMATION
        ====================================================== */

        function playAnimation() {

            if (isAnimating) {
                return;
            }


            isAnimating = true;


            let startTimestamp = null;


            const startingTime =
                parseFloat(
                    timeSlider.value
                );


            const remainingTime =
                projectileData.flightTime -
                startingTime;


            const animationDuration =
                Math.max(
                    remainingTime * 1000,
                    1000
                );


            function animate(timestamp) {

                if (!isAnimating) {
                    return;
                }


                if (!startTimestamp) {
                    startTimestamp = timestamp;
                }


                const elapsed =
                    timestamp -
                    startTimestamp;


                const progress =
                    Math.min(
                        elapsed /
                        animationDuration,
                        1
                    );


                const currentTime =
                    startingTime +
                    (
                        projectileData.flightTime -
                        startingTime
                    ) *
                    progress;


                timeSlider.value =
                    currentTime;


                updateSelectedPoint(
                    currentTime
                );


                if (progress < 1) {

                    animationFrame =
                        requestAnimationFrame(
                            animate
                        );

                } else {

                    isAnimating = false;

                    animationFrame = null;

                }

            }


            animationFrame =
                requestAnimationFrame(
                    animate
                );

        }


        /* =====================================================
           STOP ANIMATION
        ====================================================== */

        function stopAnimation() {

            isAnimating = false;


            if (animationFrame) {

                cancelAnimationFrame(
                    animationFrame
                );

                animationFrame = null;

            }

        }


        /* =====================================================
           RESET POSITION
        ====================================================== */

        function resetAnimationPosition() {

            stopAnimation();


            timeSlider.value = 0;


            updateSelectedPoint(0);

        }


        /* =====================================================
           THEME-AWARE GRAPH COLORS
        ====================================================== */

        function getGridColor() {

            const dark =
                document.documentElement
                .getAttribute("data-theme") ===
                "dark";


            return dark ?
                "rgba(255,255,255,0.10)" :
                "rgba(0,0,0,0.08)";

        }


        function getGuideColor() {

            const dark =
                document.documentElement
                .getAttribute("data-theme") ===
                "dark";


            return dark ?
                "rgba(255,255,255,0.55)" :
                "rgba(11,79,138,0.55)";

        }


        function getVectorColor() {

            const dark =
                document.documentElement
                .getAttribute("data-theme") ===
                "dark";


            return dark ?
                "#fbbf24" :
                "#dc3545";

        }


        /* =====================================================
           TIME SLIDER EVENT
        ====================================================== */

        timeSlider.addEventListener(
            "input",
            function() {

                stopAnimation();


                updateSelectedPoint(
                    parseFloat(
                        this.value
                    )
                );

            }
        );


        /* =====================================================
           CALCULATE BUTTON
        ====================================================== */

        document.getElementById(
            "calculateButton"
        ).addEventListener(
            "click",
            calculateProjectile
        );


        /* =====================================================
           RESET BUTTON
        ====================================================== */

        document.getElementById(
            "resetButton"
        ).addEventListener(
            "click",
            function() {

                velocityInput.value = 20;

                angleInput.value = 45;

                heightInput.value = 0;

                gravityInput.value = 9.81;

                calculateProjectile();

            }
        );


        /* =====================================================
           PLAY BUTTON
        ====================================================== */

        document.getElementById(
            "playButton"
        ).addEventListener(
            "click",
            playAnimation
        );


        /* =====================================================
           PAUSE BUTTON
        ====================================================== */

        document.getElementById(
            "pauseButton"
        ).addEventListener(
            "click",
            stopAnimation
        );


        /* =====================================================
           RESET POSITION BUTTON
        ====================================================== */

        document.getElementById(
            "animationResetButton"
        ).addEventListener(
            "click",
            resetAnimationPosition
        );


        /* =====================================================
           RECALCULATE WHEN ENTER IS PRESSED
        ====================================================== */

        [
            velocityInput,
            angleInput,
            heightInput,
            gravityInput

        ].forEach(
            function(input) {

                input.addEventListener(
                    "keydown",
                    function(event) {

                        if (
                            event.key === "Enter"
                        ) {

                            calculateProjectile();

                        }

                    }
                );

            }
        );


        /* =====================================================
           INITIAL CALCULATION
        ====================================================== */

        document.addEventListener(
            "DOMContentLoaded",
            function() {

                calculateProjectile();

            }
        );


        /* =====================================================
           UPDATE GRAPH WHEN THEME CHANGES
        ====================================================== */

        const themeObserver =
            new MutationObserver(
                function() {

                    if (
                        trajectoryChart
                    ) {

                        buildTrajectoryChart();

                        updateSelectedPoint(
                            parseFloat(
                                timeSlider.value
                            )
                        );

                    }

                }
            );


        themeObserver.observe(
            document.documentElement, {
                attributes: true,
                attributeFilter: [
                    "data-theme"
                ]
            }
        );
    </script>


    <!-- =====================================================
         STANDARD ETS-ASYNC SCRIPTS
    ====================================================== -->

    <?php include "../globals/scripts.php"; ?>


</body>

</html>
