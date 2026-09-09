<?php

/* =========================================================
   SIGNAL OPERATIONS
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
                        Operations on Signals
                    </h2>

                    <p>
                        Perform and visualize mathematical operations
                        on discrete-time signals.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 MAIN SIGNAL OPERATIONS LAYOUT
            ================================================== -->

            <div class="signal-layout">


                <!-- =================================================
                     LEFT CONTROL PANEL
                ================================================== -->

                <div class="signal-controls">


                    <!-- =================================================
                         OPERATION
                    ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-calculator"></i>

                            <span>
                                Signal Operation
                            </span>

                        </div>


                        <label class="signal-label">
                            Operation
                        </label>


                        <select
                            id="operationType"
                            class="signal-input">

                            <option value="scale">
                                Amplitude Scaling
                            </option>

                            <option value="addition">
                                Addition
                            </option>

                            <option value="subtraction">
                                Subtraction
                            </option>

                            <option value="multiplication">
                                Multiplication
                            </option>

                            <option value="shift">
                                Time Shifting
                            </option>

                            <option value="reversal">
                                Time Reversal
                            </option>

                            <option value="scaling">
                                Time Scaling
                            </option>

                        </select>


                        <div
                            id="operationHelp"
                            class="operation-help">

                            <strong>
                                y[n] = A x[n]
                            </strong>

                            <span>
                                Multiplies every sample of the signal
                                by an amplitude factor.
                            </span>

                        </div>

                    </div>


                    <!-- =================================================
                         SIGNAL X
                    ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-activity"></i>

                            <span>
                                Signal x[n]
                            </span>

                        </div>


                        <label class="signal-label">
                            Signal Type
                        </label>


                        <select
                            id="signalType1"
                            class="signal-input">

                            <option value="step">
                                Unit Step u[n]
                            </option>

                            <option value="impulse">
                                Unit Impulse δ[n]
                            </option>

                            <option value="ramp">
                                Unit Ramp r[n]
                            </option>

                            <option value="constant">
                                Constant
                            </option>

                            <option value="sine">
                                Sinusoidal
                            </option>

                            <option value="cosine">
                                Cosinusoidal
                            </option>

                        </select>


                        <!-- AMPLITUDE -->

                        <div class="input-parameter">

                            <label class="signal-label">

                                Amplitude

                                <span id="amplitude1Value">
                                    1
                                </span>

                            </label>


                            <input
                                type="range"
                                id="amplitude1"
                                min="-10"
                                max="10"
                                step="0.1"
                                value="1"
                                class="signal-range">

                        </div>


                        <!-- FREQUENCY -->

                        <div
                            class="input-parameter"
                            id="frequency1Group">

                            <label class="signal-label">

                                Angular Frequency

                                <span id="frequency1Value">
                                    0.25π
                                </span>

                            </label>


                            <input
                                type="range"
                                id="frequency1"
                                min="0"
                                max="3.14159"
                                step="0.01"
                                value="0.7854"
                                class="signal-range">

                        </div>

                    </div>


                    <!-- =================================================
                         SECOND SIGNAL
                    ================================================== -->

                    <div
                        class="control-section"
                        id="secondSignalSection">

                        <div class="control-section-title">

                            <i class="bi bi-activity"></i>

                            <span>
                                Signal x₂[n]
                            </span>

                        </div>


                        <label class="signal-label">
                            Signal Type
                        </label>


                        <select
                            id="signalType2"
                            class="signal-input">

                            <option value="step">
                                Unit Step u[n]
                            </option>

                            <option value="impulse">
                                Unit Impulse δ[n]
                            </option>

                            <option value="ramp">
                                Unit Ramp r[n]
                            </option>

                            <option value="constant">
                                Constant
                            </option>

                            <option value="sine">
                                Sinusoidal
                            </option>

                            <option value="cosine">
                                Cosinusoidal
                            </option>

                        </select>


                        <!-- AMPLITUDE -->

                        <div class="input-parameter">

                            <label class="signal-label">

                                Amplitude

                                <span id="amplitude2Value">
                                    1
                                </span>

                            </label>


                            <input
                                type="range"
                                id="amplitude2"
                                min="-10"
                                max="10"
                                step="0.1"
                                value="1"
                                class="signal-range">

                        </div>


                        <!-- FREQUENCY -->

                        <div
                            class="input-parameter"
                            id="frequency2Group">

                            <label class="signal-label">

                                Angular Frequency

                                <span id="frequency2Value">
                                    0.25π
                                </span>

                            </label>


                            <input
                                type="range"
                                id="frequency2"
                                min="0"
                                max="3.14159"
                                step="0.01"
                                value="0.7854"
                                class="signal-range">

                        </div>

                    </div>


                    <!-- =================================================
                         OPERATION PARAMETERS
                    ================================================== -->

                    <div
                        class="control-section"
                        id="operationParameters">

                        <div class="control-section-title">

                            <i class="bi bi-sliders"></i>

                            <span>
                                Operation Parameters
                            </span>

                        </div>


                        <!-- AMPLITUDE SCALE -->

                        <div
                            id="amplitudeScaleGroup"
                            class="parameter-group">

                            <label class="signal-label">

                                Scaling Factor

                                <span id="scaleValue">
                                    2
                                </span>

                            </label>


                            <input
                                type="range"
                                id="scaleFactor"
                                min="-10"
                                max="10"
                                step="0.1"
                                value="2"
                                class="signal-range">

                        </div>


                        <!-- TIME SHIFT -->

                        <div
                            id="timeShiftGroup"
                            class="parameter-group">

                            <label class="signal-label">
                                Shift n₀
                            </label>


                            <input
                                type="number"
                                id="timeShift"
                                class="signal-input"
                                value="3"
                                min="-20"
                                max="20">

                        </div>


                        <!-- TIME SCALING -->

                        <div
                            id="timeScalingGroup"
                            class="parameter-group">

                            <label class="signal-label">
                                Scaling Factor k
                            </label>


                            <select
                                id="timeScaleFactor"
                                class="signal-input">

                                <option value="2">
                                    2 — Compression
                                </option>

                                <option value="3">
                                    3 — Compression
                                </option>

                                <option value="0.5">
                                    1/2 — Expansion
                                </option>

                                <option value="0.25">
                                    1/4 — Expansion
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- =================================================
                         COMPUTATION RANGE
                    ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-arrows-expand"></i>

                            <span>
                                Computation Range
                            </span>

                        </div>


                        <div class="range-grid">

                            <div>

                                <label class="signal-label">
                                    Start n
                                </label>

                                <input
                                    type="number"
                                    id="nStart"
                                    class="signal-input"
                                    value="-10"
                                    min="-100"
                                    max="100">

                            </div>


                            <div>

                                <label class="signal-label">
                                    End n
                                </label>

                                <input
                                    type="number"
                                    id="nEnd"
                                    class="signal-input"
                                    value="10"
                                    min="-99"
                                    max="200">

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         ACTION BUTTONS
                    ================================================== -->

                    <div class="signal-actions">

                        <button
                            type="button"
                            id="computeButton"
                            class="signal-button primary">

                            <i class="bi bi-play-fill"></i>

                            Compute

                        </button>


                        <button
                            type="button"
                            id="resetButton"
                            class="signal-button">

                            <i class="bi bi-arrow-counterclockwise"></i>

                            Reset

                        </button>

                    </div>


                </div>


                <!-- =================================================
                     RIGHT OUTPUT PANEL
                ================================================== -->

                <div class="signal-output">


                    <!-- =================================================
                         EQUATION DISPLAY
                    ================================================== -->

                    <div class="equation-display">

                        <div class="equation-display-label">

                            Signal Operation

                        </div>


                        <div
                            id="equationDisplay"
                            class="equation">

                            y[n] = 2x[n]

                        </div>

                    </div>


                    <!-- =================================================
                         GRAPH
                    ================================================== -->

                    <div class="signal-card">

                        <div class="output-header">

                            <div>

                                <h4>
                                    Signal Visualization
                                </h4>

                                <p>
                                    Discrete-time sequence representation
                                </p>

                            </div>


                            <div class="output-badge">

                                <i class="bi bi-bar-chart-line"></i>

                                Discrete-Time

                            </div>

                        </div>


                        <div class="signal-graph">

                            <canvas
                                id="signalCanvas">
                            </canvas>

                        </div>

                    </div>


                    <!-- =================================================
                         RESULTS TABLE
                    ================================================== -->

                    <div class="signal-card">

                        <div class="output-header">

                            <div>

                                <h4>
                                    Computed Sequence
                                </h4>

                                <p>
                                    Numerical values of the input and output
                                </p>

                            </div>

                        </div>


                        <div class="sequence-table-wrapper">

                            <table class="sequence-table">

                                <thead>

                                    <tr>

                                        <th>
                                            n
                                        </th>

                                        <th>
                                            x₁[n]
                                        </th>

                                        <th>
                                            x₂[n]
                                        </th>

                                        <th>
                                            y[n]
                                        </th>

                                    </tr>

                                </thead>


                                <tbody
                                    id="sequenceTable">

                                </tbody>

                            </table>

                        </div>

                    </div>


                    <!-- =================================================
                         SUMMARY
                    ================================================== -->

                    <div class="result-summary">

                        <div class="summary-item">

                            <span>
                                Operation
                            </span>

                            <strong id="summaryOperation">
                                Scaling
                            </strong>

                        </div>


                        <div class="summary-item">

                            <span>
                                Samples
                            </span>

                            <strong id="summarySamples">
                                21
                            </strong>

                        </div>


                        <div class="summary-item">

                            <span>
                                Maximum y[n]
                            </span>

                            <strong id="summaryMax">
                                0
                            </strong>

                        </div>


                        <div class="summary-item">

                            <span>
                                Minimum y[n]
                            </span>

                            <strong id="summaryMin">
                                0
                            </strong>

                        </div>

                    </div>


                </div>

            </div>


            <!-- =================================================
                 THEORY REFERENCE
            ================================================== -->

            <div class="signal-reference">

                <div class="reference-title">

                    <i class="bi bi-info-circle"></i>

                    Signal Operations Reference

                </div>


                <div class="reference-grid">


                    <div class="reference-item">

                        <strong>
                            Amplitude Scaling
                        </strong>

                        <span>
                            y[n] = A x[n]
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Addition
                        </strong>

                        <span>
                            y[n] = x₁[n] + x₂[n]
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Subtraction
                        </strong>

                        <span>
                            y[n] = x₁[n] − x₂[n]
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Multiplication
                        </strong>

                        <span>
                            y[n] = x₁[n]x₂[n]
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Time Shifting
                        </strong>

                        <span>
                            y[n] = x[n − n₀]
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Time Reversal
                        </strong>

                        <span>
                            y[n] = x[−n]
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Time Scaling
                        </strong>

                        <span>
                            y[n] = x[kn]
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Discrete-Time
                        </strong>

                        <span>
                            Signals are evaluated only at integer
                            values of n.
                        </span>

                    </div>

                </div>

            </div>


        </div>

    </main>


    <!-- =========================================================
         SIGNAL OPERATIONS STYLES
    ========================================================== -->

    <style>
        /* =====================================================
           THEME VARIABLES
        ====================================================== */

        :root {

            --signal-bg: #ffffff;

            --signal-border: #e4e7ec;

            --signal-input-border: #d0d5dd;

            --signal-text: #172033;

            --signal-muted: #667085;

            --signal-secondary: #475467;

            --signal-hover: #f8fafc;

            --signal-panel: #ffffff;

            --signal-grid: #e4e7ec;

            --signal-axis: #475467;

            --signal-blue: #2563eb;

            --signal-header: #f8fafc;

        }


        /* =====================================================
           DARK MODE
        ====================================================== */

        html[data-theme="dark"] {

            --signal-bg: #151922;

            --signal-border: #2a3140;

            --signal-input-border: #3a4252;

            --signal-text: #f1f5f9;

            --signal-muted: #a8b0bf;

            --signal-secondary: #c2c9d3;

            --signal-hover: #1c2230;

            --signal-panel: #151922;

            --signal-grid: #303847;

            --signal-axis: #aeb7c5;

            --signal-blue: #60a5fa;

            --signal-header: #1c2230;

        }


        /* =====================================================
           MAIN LAYOUT
        ====================================================== */

        .signal-layout {

            display: grid;

            grid-template-columns:
                310px minmax(0, 1fr);

            gap: 16px;

            margin-bottom: 18px;

        }


        /* =====================================================
           CONTROL PANEL
        ====================================================== */

        .signal-controls {

            background:
                var(--signal-bg);

            border:
                1px solid var(--signal-border);

            border-radius: 10px;

            overflow: hidden;

        }


        .control-section {

            padding: 17px;

            border-bottom:
                1px solid var(--signal-border);

        }


        .control-section-title {

            display: flex;

            align-items: center;

            gap: 8px;

            margin-bottom: 14px;

            color:
                var(--signal-text);

            font-size: .82rem;

            font-weight: 700;

        }


        .control-section-title i {

            color:
                var(--signal-blue);

        }


        /* =====================================================
           OPERATION HELP
        ====================================================== */

        .operation-help {

            margin-top: 12px;

            padding: 10px;

            border-radius: 6px;

            background:
                var(--signal-hover);

            color:
                var(--signal-muted);

            font-size: .68rem;

            line-height: 1.6;

        }


        .operation-help strong {

            display: block;

            margin-bottom: 3px;

            color:
                var(--signal-text);

            font-family:
                "Times New Roman",
                serif;

            font-size: .9rem;

            font-weight: normal;

        }


        /* =====================================================
           LABELS
        ====================================================== */

        .signal-label {

            display: flex;

            justify-content: space-between;

            margin-bottom: 6px;

            color:
                var(--signal-secondary);

            font-size: .71rem;

            font-weight: 600;

        }


        .signal-label span {

            color:
                var(--signal-blue);

            font-weight: 700;

        }


        /* =====================================================
           INPUTS
        ====================================================== */

        .signal-input {

            width: 100%;

            height: 38px;

            padding:
                0 10px;

            border:
                1px solid var(--signal-input-border);

            border-radius: 6px;

            outline: none;

            background:
                var(--signal-panel);

            color:
                var(--signal-text);

            font-family: inherit;

            font-size: .75rem;

        }


        .signal-input:focus {

            border-color:
                var(--signal-blue);

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, .10);

        }


        /* =====================================================
           PARAMETERS
        ====================================================== */

        .input-parameter,
        .parameter-group {

            margin-top: 15px;

        }


        .signal-range {

            width: 100%;

            accent-color:
                var(--signal-blue);

            cursor: pointer;

        }


        /* =====================================================
           RANGE
        ====================================================== */

        .range-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 9px;

        }


        /* =====================================================
           ACTIONS
        ====================================================== */

        .signal-actions {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 8px;

            padding: 14px;

            background:
                var(--signal-hover);

        }


        .signal-button {

            height: 38px;

            border:
                1px solid var(--signal-input-border);

            border-radius: 6px;

            background:
                var(--signal-panel);

            color:
                var(--signal-secondary);

            font-family: inherit;

            font-size: .72rem;

            font-weight: 700;

            cursor: pointer;

            transition:
                all .2s ease;

        }


        .signal-button:hover {

            background:
                var(--signal-hover);

            color:
                var(--signal-text);

        }


        .signal-button.primary {

            background:
                var(--signal-blue);

            border-color:
                var(--signal-blue);

            color:
                #ffffff;

        }


        /* =====================================================
           OUTPUT
        ====================================================== */

        .signal-output {

            min-width: 0;

        }


        /* =====================================================
           EQUATION DISPLAY
        ====================================================== */

        .equation-display {

            margin-bottom: 14px;

            padding:
                17px 20px;

            background:
                var(--signal-bg);

            border:
                1px solid var(--signal-border);

            border-radius: 10px;

        }


        .equation-display-label {

            margin-bottom: 7px;

            color:
                var(--signal-muted);

            font-size: .66rem;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: .03em;

        }


        .equation {

            color:
                var(--signal-text);

            font-family:
                "Times New Roman",
                serif;

            font-size: 1.1rem;

        }


        /* =====================================================
           OUTPUT CARD
        ====================================================== */

        .signal-card {

            margin-bottom: 14px;

            background:
                var(--signal-bg);

            border:
                1px solid var(--signal-border);

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
                1px solid var(--signal-border);

        }


        .output-header h4 {

            margin:
                0 0 3px;

            color:
                var(--signal-text);

            font-size: .9rem;

            font-weight: 700;

        }


        .output-header p {

            margin: 0;

            color:
                var(--signal-muted);

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
                var(--signal-hover);

            color:
                var(--signal-secondary);

            font-size: .63rem;

            font-weight: 700;

            white-space: nowrap;

        }


        /* =====================================================
           GRAPH
        ====================================================== */

        .signal-graph {

            width: 100%;

            height: 420px;

            padding: 10px;

        }


        #signalCanvas {

            width: 100%;

            height: 100%;

            display: block;

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
                10px 14px;

            background:
                var(--signal-header);

            border-bottom:
                1px solid var(--signal-border);

            color:
                var(--signal-secondary);

            font-size: .68rem;

            text-align: right;

        }


        .sequence-table th:first-child {

            text-align: center;

        }


        .sequence-table td {

            padding:
                8px 14px;

            border-bottom:
                1px solid var(--signal-border);

            color:
                var(--signal-text);

            font-family:
                "Courier New",
                monospace;

            font-size: .7rem;

            text-align: right;

        }


        .sequence-table td:first-child {

            text-align: center;

            font-weight: 700;

        }


        .sequence-table tr:hover td {

            background:
                var(--signal-hover);

        }


        /* =====================================================
           SUMMARY
        ====================================================== */

        .result-summary {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            background:
                var(--signal-bg);

            border:
                1px solid var(--signal-border);

            border-radius: 10px;

            overflow: hidden;

        }


        .summary-item {

            padding:
                13px 15px;

            border-right:
                1px solid var(--signal-border);

        }


        .summary-item:last-child {

            border-right: none;

        }


        .summary-item span {

            display: block;

            margin-bottom: 4px;

            color:
                var(--signal-muted);

            font-size: .63rem;

            font-weight: 600;

        }


        .summary-item strong {

            color:
                var(--signal-text);

            font-size: .75rem;

        }


        /* =====================================================
           REFERENCE
        ====================================================== */

        .signal-reference {

            background:
                var(--signal-bg);

            border:
                1px solid var(--signal-border);

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
                1px solid var(--signal-border);

            color:
                var(--signal-text);

            font-size: .8rem;

            font-weight: 700;

        }


        .reference-title i {

            color:
                var(--signal-blue);

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
                1px solid var(--signal-border);

            border-bottom:
                1px solid var(--signal-border);

        }


        .reference-item:nth-child(4n) {

            border-right: none;

        }


        .reference-item strong {

            display: block;

            margin-bottom: 5px;

            color:
                var(--signal-text);

            font-size: .72rem;

        }


        .reference-item span {

            color:
                var(--signal-muted);

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

            .signal-layout {

                grid-template-columns:
                    280px minmax(0, 1fr);

            }


            .reference-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .reference-item:nth-child(4n) {

                border-right:
                    1px solid var(--signal-border);

            }


            .reference-item:nth-child(2n) {

                border-right: none;

            }

        }


        @media (max-width: 850px) {

            .signal-layout {

                grid-template-columns:
                    1fr;

            }

        }


        @media (max-width: 600px) {

            .signal-graph {

                height: 340px;

            }


            .result-summary {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .summary-item:nth-child(2) {

                border-right: none;

            }


            .reference-grid {

                grid-template-columns:
                    1fr;

            }


            .reference-item {

                border-right: none !important;

                border-bottom:
                    1px solid var(--signal-border);

            }


            .output-header {

                align-items: flex-start;

                flex-direction: column;

            }

        }
    </style>


    <!-- =========================================================
         SIGNAL OPERATIONS JAVASCRIPT
    ========================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {


                /* =================================================
                   ELEMENTS
                ================================================== */

                const operationType =
                    document.getElementById(
                        "operationType"
                    );

                const signalType1 =
                    document.getElementById(
                        "signalType1"
                    );

                const signalType2 =
                    document.getElementById(
                        "signalType2"
                    );

                const amplitude1 =
                    document.getElementById(
                        "amplitude1"
                    );

                const amplitude2 =
                    document.getElementById(
                        "amplitude2"
                    );

                const frequency1 =
                    document.getElementById(
                        "frequency1"
                    );

                const frequency2 =
                    document.getElementById(
                        "frequency2"
                    );

                const frequency1Group =
                    document.getElementById(
                        "frequency1Group"
                    );

                const frequency2Group =
                    document.getElementById(
                        "frequency2Group"
                    );

                const secondSignalSection =
                    document.getElementById(
                        "secondSignalSection"
                    );

                const amplitudeScaleGroup =
                    document.getElementById(
                        "amplitudeScaleGroup"
                    );

                const timeShiftGroup =
                    document.getElementById(
                        "timeShiftGroup"
                    );

                const timeScalingGroup =
                    document.getElementById(
                        "timeScalingGroup"
                    );

                const scaleFactor =
                    document.getElementById(
                        "scaleFactor"
                    );

                const timeShift =
                    document.getElementById(
                        "timeShift"
                    );

                const timeScaleFactor =
                    document.getElementById(
                        "timeScaleFactor"
                    );

                const nStart =
                    document.getElementById(
                        "nStart"
                    );

                const nEnd =
                    document.getElementById(
                        "nEnd"
                    );

                const canvas =
                    document.getElementById(
                        "signalCanvas"
                    );

                const ctx =
                    canvas.getContext("2d");


                /* =================================================
                   OPERATION INFORMATION
                ================================================== */

                const operationInfo = {

                    scale: {

                        equation: "y[n] = A x[n]",

                        description: "Multiplies every sample of the signal by an amplitude factor."

                    },

                    addition: {

                        equation: "y[n] = x₁[n] + x₂[n]",

                        description: "Adds two discrete-time signals sample by sample."

                    },

                    subtraction: {

                        equation: "y[n] = x₁[n] − x₂[n]",

                        description: "Subtracts the second signal from the first signal."

                    },

                    multiplication: {

                        equation: "y[n] = x₁[n]x₂[n]",

                        description: "Multiplies two discrete-time signals sample by sample."

                    },

                    shift: {

                        equation: "y[n] = x[n − n₀]",

                        description: "Shifts the signal along the discrete-time axis."

                    },

                    reversal: {

                        equation: "y[n] = x[−n]",

                        description: "Reverses the signal around n = 0."

                    },

                    scaling: {

                        equation: "y[n] = x[kn]",

                        description: "Compresses or expands the signal along the time axis."

                    }

                };


                /* =================================================
                   FORMAT NUMBER
                ================================================== */

                function formatNumber(value) {

                    if (
                        Math.abs(value) <
                        0.000001
                    ) {

                        return "0";

                    }


                    return Number(
                        value.toFixed(6)
                    ).toString();

                }


                /* =================================================
                   SIGNAL VALUE
                ================================================== */

                function getSignalValue(
                    n,
                    type,
                    amplitude,
                    frequency
                ) {

                    switch (type) {


                        case "step":

                            return n >= 0 ?
                                amplitude :
                                0;


                        case "impulse":

                            return n === 0 ?
                                amplitude :
                                0;


                        case "ramp":

                            return n >= 0 ?
                                amplitude * n :
                                0;


                        case "constant":

                            return amplitude;


                        case "sine":

                            return amplitude *
                                Math.sin(
                                    frequency * n
                                );


                        case "cosine":

                            return amplitude *
                                Math.cos(
                                    frequency * n
                                );


                        default:

                            return 0;

                    }

                }


                /* =================================================
                   SIGNAL 1 VALUE
                ================================================== */

                function getX1(n) {

                    return getSignalValue(

                        n,

                        signalType1.value,

                        parseFloat(
                            amplitude1.value
                        ) || 0,

                        parseFloat(
                            frequency1.value
                        ) || 0

                    );

                }


                /* =================================================
                   SIGNAL 2 VALUE
                ================================================== */

                function getX2(n) {

                    return getSignalValue(

                        n,

                        signalType2.value,

                        parseFloat(
                            amplitude2.value
                        ) || 0,

                        parseFloat(
                            frequency2.value
                        ) || 0

                    );

                }


                /* =================================================
                   OUTPUT VALUE
                ================================================== */

                function getOutputValue(n) {

                    const operation =
                        operationType.value;


                    const A =
                        parseFloat(
                            scaleFactor.value
                        ) || 0;


                    const shift =
                        parseInt(
                            timeShift.value
                        ) || 0;


                    const k =
                        parseFloat(
                            timeScaleFactor.value
                        ) || 1;


                    const x1 =
                        getX1(n);


                    const x2 =
                        getX2(n);


                    switch (operation) {


                        case "scale":

                            return A * x1;


                        case "addition":

                            return x1 + x2;


                        case "subtraction":

                            return x1 - x2;


                        case "multiplication":

                            return x1 * x2;


                        case "shift":

                            return getX1(
                                n - shift
                            );


                        case "reversal":

                            return getX1(
                                -n
                            );


                        case "scaling":

                            /*
                             * For integer k:
                             * y[n] = x[kn]
                             *
                             * For fractional k:
                             * the argument must be
                             * an integer sample.
                             */

                            const sourceIndex =
                                n * k;


                            if (
                                !Number.isInteger(
                                    sourceIndex
                                )
                            ) {

                                return 0;

                            }


                            return getX1(
                                sourceIndex
                            );


                        default:

                            return 0;

                    }

                }


                /* =================================================
                   UPDATE OPERATION UI
                ================================================== */

                function updateOperationUI() {

                    const operation =
                        operationType.value;


                    const info =
                        operationInfo[
                            operation
                        ];


                    document
                        .getElementById(
                            "operationHelp"
                        )
                        .innerHTML = `

                            <strong>
                                ${info.equation}
                            </strong>

                            <span>
                                ${info.description}
                            </span>

                        `;


                    /*
                     * Second signal only required
                     * for binary operations.
                     */

                    const isBinary =
                        operation === "addition" ||
                        operation === "subtraction" ||
                        operation === "multiplication";


                    secondSignalSection.style.display =
                        isBinary ?
                        "block" :
                        "none";


                    /*
                     * Amplitude scaling parameter
                     */

                    amplitudeScaleGroup.style.display =
                        operation === "scale" ?
                        "block" :
                        "none";


                    /*
                     * Time shift parameter
                     */

                    timeShiftGroup.style.display =
                        operation === "shift" ?
                        "block" :
                        "none";


                    /*
                     * Time scaling parameter
                     */

                    timeScalingGroup.style.display =
                        operation === "scaling" ?
                        "block" :
                        "none";


                    updateFrequencyVisibility();

                    updateEquation();

                }


                /* =================================================
                   FREQUENCY VISIBILITY
                ================================================== */

                function updateFrequencyVisibility() {

                    const operation =
                        operationType.value;


                    const needsFrequency1 =
                        signalType1.value === "sine" ||
                        signalType1.value === "cosine";


                    const needsFrequency2 =
                        signalType2.value === "sine" ||
                        signalType2.value === "cosine";


                    frequency1Group.style.display =
                        needsFrequency1 ?
                        "block" :
                        "none";


                    const binaryOperation =
                        operation === "addition" ||
                        operation === "subtraction" ||
                        operation === "multiplication";


                    frequency2Group.style.display =
                        (
                            binaryOperation &&
                            needsFrequency2
                        ) ?
                        "block" :
                        "none";

                }


                /* =================================================
                   UPDATE EQUATION
                ================================================== */

                function updateEquation() {

                    const operation =
                        operationType.value;


                    let equation = "";


                    switch (operation) {


                        case "scale":

                            equation =
                                "y[n] = " +
                                formatNumber(
                                    parseFloat(
                                        scaleFactor.value
                                    ) || 0
                                ) +
                                "x[n]";

                            break;


                        case "addition":

                            equation =
                                "y[n] = x₁[n] + x₂[n]";

                            break;


                        case "subtraction":

                            equation =
                                "y[n] = x₁[n] − x₂[n]";

                            break;


                        case "multiplication":

                            equation =
                                "y[n] = x₁[n]x₂[n]";

                            break;


                        case "shift":

                            const shift =
                                parseInt(
                                    timeShift.value
                                ) || 0;


                            if (shift >= 0) {

                                equation =
                                    "y[n] = x[n − " +
                                    shift +
                                    "]";

                            } else {

                                equation =
                                    "y[n] = x[n + " +
                                    Math.abs(shift) +
                                    "]";

                            }

                            break;


                        case "reversal":

                            equation =
                                "y[n] = x[−n]";

                            break;


                        case "scaling":

                            equation =
                                "y[n] = x[" +
                                formatNumber(
                                    parseFloat(
                                        timeScaleFactor.value
                                    ) || 1
                                ) +
                                "n]";

                            break;


                        default:

                            equation =
                                "y[n]";

                    }


                    document
                        .getElementById(
                            "equationDisplay"
                        )
                        .textContent =
                        equation;

                }


                /* =================================================
                   COMPUTE SEQUENCE
                ================================================== */

                function computeSequence() {

                    const start =
                        parseInt(
                            nStart.value
                        );


                    const end =
                        parseInt(
                            nEnd.value
                        );


                    if (
                        isNaN(start) ||
                        isNaN(end) ||
                        end < start
                    ) {

                        return [];

                    }


                    const results = [];


                    for (
                        let n = start; n <= end; n++
                    ) {

                        const x1 =
                            getX1(n);


                        const x2 =
                            getX2(n);


                        const y =
                            getOutputValue(n);


                        results.push({

                            n: n,

                            x1: x1,

                            x2: x2,

                            y: y

                        });

                    }


                    return results;

                }


                /* =================================================
                   UPDATE TABLE
                ================================================== */

                function updateTable(
                    results
                ) {

                    const table =
                        document.getElementById(
                            "sequenceTable"
                        );


                    table.innerHTML =
                        "";


                    const operation =
                        operationType.value;


                    const binaryOperation =
                        operation === "addition" ||
                        operation === "subtraction" ||
                        operation === "multiplication";


                    results.forEach(
                        function(row) {

                            const tr =
                                document.createElement(
                                    "tr"
                                );


                            tr.innerHTML = `

                                <td>
                                    ${row.n}
                                </td>

                                <td>
                                    ${formatNumber(row.x1)}
                                </td>

                                <td>
                                    ${
                                        binaryOperation
                                        ? formatNumber(row.x2)
                                        : "—"
                                    }
                                </td>

                                <td>
                                    ${formatNumber(row.y)}
                                </td>

                            `;


                            table.appendChild(
                                tr
                            );

                        }
                    );

                }


                /* =================================================
                   THEME COLORS
                ================================================== */

                function getThemeColors() {

                    const styles =
                        getComputedStyle(
                            document.documentElement
                        );


                    return {

                        text: styles.getPropertyValue(
                            "--signal-text"
                        ).trim(),

                        muted: styles.getPropertyValue(
                            "--signal-muted"
                        ).trim(),

                        grid: styles.getPropertyValue(
                            "--signal-grid"
                        ).trim(),

                        axis: styles.getPropertyValue(
                            "--signal-axis"
                        ).trim(),

                        blue: styles.getPropertyValue(
                            "--signal-blue"
                        ).trim()

                    };

                }


                /* =================================================
                   DRAW GRAPH
                ================================================== */

                function drawGraph(
                    results
                ) {

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


                    ctx.clearRect(
                        0,
                        0,
                        width,
                        height
                    );


                    if (
                        results.length === 0
                    ) {

                        return;

                    }


                    /* =============================================
                       Y RANGE
                    ============================================== */

                    let maxAbs = 1;


                    results.forEach(
                        function(row) {

                            maxAbs =
                                Math.max(
                                    maxAbs,
                                    Math.abs(row.x1),
                                    Math.abs(row.x2),
                                    Math.abs(row.y)
                                );

                        }
                    );


                    maxAbs *= 1.2;


                    /* =============================================
                       MARGINS
                    ============================================== */

                    const margin = {

                        left: 60,

                        right: 25,

                        top: 30,

                        bottom: 50

                    };


                    const graphWidth =
                        width -
                        margin.left -
                        margin.right;


                    const graphHeight =
                        height -
                        margin.top -
                        margin.bottom;


                    const minN =
                        results[0].n;


                    const maxN =
                        results[
                            results.length - 1
                        ].n;


                    function xPosition(n) {

                        if (
                            maxN === minN
                        ) {

                            return width / 2;

                        }


                        return margin.left +

                            (
                                (n - minN) /
                                (maxN - minN)
                            ) *

                            graphWidth;

                    }


                    const zeroY =
                        margin.top +
                        graphHeight / 2;


                    function yPosition(y) {

                        return zeroY -

                            (
                                y /
                                maxAbs
                            ) *

                            (
                                graphHeight / 2
                            );

                    }


                    /* =============================================
                       GRID
                    ============================================== */

                    ctx.strokeStyle =
                        colors.grid;


                    ctx.lineWidth = 1;


                    results.forEach(
                        function(row) {

                            const x =
                                xPosition(
                                    row.n
                                );


                            ctx.beginPath();


                            ctx.moveTo(
                                x,
                                margin.top
                            );


                            ctx.lineTo(
                                x,
                                height -
                                margin.bottom
                            );


                            ctx.stroke();

                        }
                    );


                    for (
                        let i = -5; i <= 5; i++
                    ) {

                        const yValue =
                            (
                                i / 5
                            ) *
                            maxAbs;


                        const y =
                            yPosition(
                                yValue
                            );


                        ctx.beginPath();


                        ctx.moveTo(
                            margin.left,
                            y
                        );


                        ctx.lineTo(
                            width -
                            margin.right,
                            y
                        );


                        ctx.stroke();

                    }


                    /* =============================================
                       AXIS
                    ============================================== */

                    ctx.strokeStyle =
                        colors.axis;


                    ctx.lineWidth =
                        1.5;


                    ctx.beginPath();


                    ctx.moveTo(
                        margin.left,
                        zeroY
                    );


                    ctx.lineTo(
                        width -
                        margin.right,
                        zeroY
                    );


                    ctx.stroke();


                    /* =============================================
                       DRAW STEM FUNCTION
                    ============================================== */

                    function drawStem(
                        row,
                        value
                    ) {

                        const x =
                            xPosition(
                                row.n
                            );


                        const y =
                            yPosition(
                                value
                            );


                        ctx.strokeStyle =
                            colors.blue;


                        ctx.fillStyle =
                            colors.blue;


                        ctx.lineWidth = 2;


                        ctx.beginPath();


                        ctx.moveTo(
                            x,
                            zeroY
                        );


                        ctx.lineTo(
                            x,
                            y
                        );


                        ctx.stroke();


                        ctx.beginPath();


                        ctx.arc(
                            x,
                            y,
                            4,
                            0,
                            Math.PI * 2
                        );


                        ctx.fill();

                    }


                    /* =============================================
                       DRAW OUTPUT
                    ============================================== */

                    results.forEach(
                        function(row) {

                            drawStem(
                                row,
                                row.y
                            );

                        }
                    );


                    /* =============================================
                       X LABELS
                    ============================================== */

                    ctx.fillStyle =
                        colors.muted;


                    ctx.font =
                        "11px Arial";


                    ctx.textAlign =
                        "center";


                    ctx.textBaseline =
                        "top";


                    results.forEach(
                        function(row) {

                            const x =
                                xPosition(
                                    row.n
                                );


                            ctx.fillText(
                                row.n,
                                x,
                                zeroY + 9
                            );

                        }
                    );


                    /* =============================================
                       Y LABELS
                    ============================================== */

                    ctx.textAlign =
                        "right";


                    ctx.textBaseline =
                        "middle";


                    for (
                        let i = -5; i <= 5; i++
                    ) {

                        const value =
                            (
                                i / 5
                            ) *
                            maxAbs;


                        const y =
                            yPosition(
                                value
                            );


                        ctx.fillText(
                            formatNumber(
                                value
                            ),
                            margin.left - 8,
                            y
                        );

                    }


                    /* =============================================
                       AXIS LABELS
                    ============================================== */

                    ctx.fillStyle =
                        colors.text;


                    ctx.font =
                        "bold 12px Arial";


                    ctx.textAlign =
                        "right";


                    ctx.textBaseline =
                        "bottom";


                    ctx.fillText(
                        "n",
                        width -
                        margin.right,
                        zeroY - 7
                    );


                    ctx.textAlign =
                        "left";


                    ctx.textBaseline =
                        "top";


                    ctx.fillText(
                        "y[n]",
                        margin.left + 5,
                        margin.top
                    );


                    /* =============================================
                       LEGEND
                    ============================================== */

                    ctx.font =
                        "11px Arial";


                    ctx.textAlign =
                        "left";


                    ctx.textBaseline =
                        "middle";


                    const operation =
                        operationType.value;


                    const binaryOperation =
                        operation === "addition" ||
                        operation === "subtraction" ||
                        operation === "multiplication";


                    const legendX =
                        margin.left + 10;


                    const legendY =
                        14;


                    ctx.fillStyle =
                        colors.blue;


                    ctx.beginPath();


                    ctx.arc(
                        legendX,
                        legendY,
                        4,
                        0,
                        Math.PI * 2
                    );


                    ctx.fill();


                    ctx.fillStyle =
                        colors.muted;


                    ctx.fillText(
                        "y[n]",
                        legendX + 10,
                        legendY
                    );


                    if (
                        binaryOperation
                    ) {

                        ctx.fillStyle =
                            colors.muted;

                        ctx.fillText(
                            "Output: y[n]",
                            legendX + 55,
                            legendY
                        );

                    }

                }


                /* =================================================
                   COMPUTE
                ================================================== */

                function compute() {

                    updateOperationUI();


                    const results =
                        computeSequence();


                    updateTable(
                        results
                    );


                    drawGraph(
                        results
                    );


                    /* =============================================
                       SUMMARY
                    ============================================== */

                    const operation =
                        operationType.value;


                    const operationNames = {

                        scale: "Amplitude Scaling",

                        addition: "Addition",

                        subtraction: "Subtraction",

                        multiplication: "Multiplication",

                        shift: "Time Shifting",

                        reversal: "Time Reversal",

                        scaling: "Time Scaling"

                    };


                    document
                        .getElementById(
                            "summaryOperation"
                        )
                        .textContent =
                        operationNames[
                            operation
                        ];


                    document
                        .getElementById(
                            "summarySamples"
                        )
                        .textContent =
                        results.length;


                    if (
                        results.length > 0
                    ) {

                        const values =
                            results.map(
                                row => row.y
                            );


                        const maximum =
                            Math.max(
                                ...values
                            );


                        const minimum =
                            Math.min(
                                ...values
                            );


                        document
                            .getElementById(
                                "summaryMax"
                            )
                            .textContent =
                            formatNumber(
                                maximum
                            );


                        document
                            .getElementById(
                                "summaryMin"
                            )
                            .textContent =
                            formatNumber(
                                minimum
                            );

                    }

                }


                /* =================================================
                   INPUT EVENTS
                ================================================== */

                document
                    .querySelectorAll(
                        ".signal-controls input, .signal-controls select"
                    )
                    .forEach(
                        function(element) {

                            element.addEventListener(
                                "input",
                                function() {

                                    updateDisplayValues();

                                    compute();

                                }
                            );


                            element.addEventListener(
                                "change",
                                function() {

                                    updateDisplayValues();

                                    compute();

                                }
                            );

                        }
                    );


                /* =================================================
                   UPDATE DISPLAY VALUES
                ================================================== */

                function updateDisplayValues() {

                    document
                        .getElementById(
                            "amplitude1Value"
                        )
                        .textContent =
                        parseFloat(
                            amplitude1.value
                        ).toFixed(1);


                    document
                        .getElementById(
                            "amplitude2Value"
                        )
                        .textContent =
                        parseFloat(
                            amplitude2.value
                        ).toFixed(1);


                    document
                        .getElementById(
                            "scaleValue"
                        )
                        .textContent =
                        parseFloat(
                            scaleFactor.value
                        ).toFixed(1);


                    document
                        .getElementById(
                            "frequency1Value"
                        )
                        .textContent =
                        (
                            parseFloat(
                                frequency1.value
                            ) /
                            Math.PI
                        ).toFixed(2) +
                        "π";


                    document
                        .getElementById(
                            "frequency2Value"
                        )
                        .textContent =
                        (
                            parseFloat(
                                frequency2.value
                            ) /
                            Math.PI
                        ).toFixed(2) +
                        "π";

                }


                /* =================================================
                   RESIZE CANVAS
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


                    compute();

                }


                window.addEventListener(
                    "resize",
                    resizeCanvas
                );


                /* =================================================
                   THEME CHANGE
                ================================================== */

                const observer =
                    new MutationObserver(
                        function() {

                            compute();

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
                   RESET
                ================================================== */

                document
                    .getElementById(
                        "resetButton"
                    )
                    .addEventListener(
                        "click",
                        function() {


                            operationType.value =
                                "scale";


                            signalType1.value =
                                "step";


                            signalType2.value =
                                "step";


                            amplitude1.value =
                                "1";


                            amplitude2.value =
                                "1";


                            frequency1.value =
                                "0.7854";


                            frequency2.value =
                                "0.7854";


                            scaleFactor.value =
                                "2";


                            timeShift.value =
                                "3";


                            timeScaleFactor.value =
                                "2";


                            nStart.value =
                                "-10";


                            nEnd.value =
                                "10";


                            updateDisplayValues();

                            updateOperationUI();

                            compute();

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

                            compute();

                        }
                    );


                /* =================================================
                   INITIALIZE
                ================================================== */

                updateDisplayValues();

                updateOperationUI();

                resizeCanvas();

                compute();

            }

        );
    </script>


    <!-- =========================================================
         GLOBAL SCRIPTS
    ========================================================== -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>