<?php

/* =========================================================
   DIFFERENCE EQUATION SOLVER
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
                        Difference Equation Solver
                    </h2>

                    <p>
                        Compute and visualize the output of a
                        discrete-time system from its difference equation.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 MAIN SOLVER LAYOUT
            ================================================== -->

            <div class="difference-layout">


                <!-- =================================================
                     LEFT CONTROL PANEL
                ================================================== -->

                <div class="difference-controls">


                    <!-- =================================================
                         EQUATION COEFFICIENTS
                    ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-calculator"></i>

                            <span>
                                Difference Equation
                            </span>

                        </div>


                        <div class="equation-help">

                            General form:

                            <strong>
                                y[n] + a₁y[n−1] + a₂y[n−2] =
                                b₀x[n] + b₁x[n−1] + b₂x[n−2]
                            </strong>

                        </div>


                        <!-- ORDER -->

                        <label class="difference-label">
                            System Order
                        </label>


                        <select
                            id="systemOrder"
                            class="difference-input">

                            <option value="1">
                                First Order
                            </option>

                            <option value="2" selected>
                                Second Order
                            </option>

                            <option value="3">
                                Third Order
                            </option>

                            <option value="4">
                                Fourth Order
                            </option>

                        </select>


                        <!-- =================================================
                             OUTPUT COEFFICIENTS
                        ================================================== -->

                        <div class="coefficient-title">

                            Output Coefficients

                        </div>


                        <div
                            id="outputCoefficients"
                            class="coefficient-grid">

                        </div>


                        <!-- =================================================
                             INPUT COEFFICIENTS
                        ================================================== -->

                        <div class="coefficient-title">

                            Input Coefficients

                        </div>


                        <div
                            id="inputCoefficients"
                            class="coefficient-grid">

                        </div>

                    </div>


                    <!-- =================================================
                         INPUT SIGNAL
                    ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-activity"></i>

                            <span>
                                Input Signal x[n]
                            </span>

                        </div>


                        <label class="difference-label">
                            Signal Type
                        </label>


                        <select
                            id="inputType"
                            class="difference-input">

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

                        </select>


                        <!-- INPUT AMPLITUDE -->

                        <div
                            class="input-parameter">

                            <label class="difference-label">

                                Amplitude

                                <span id="inputAmplitudeValue">
                                    1
                                </span>

                            </label>


                            <input
                                type="range"
                                id="inputAmplitude"
                                min="-10"
                                max="10"
                                step="0.1"
                                value="1"
                                class="difference-range">

                        </div>


                        <!-- INPUT FREQUENCY -->

                        <div
                            class="input-parameter"
                            id="inputFrequencyGroup">

                            <label class="difference-label">

                                Angular Frequency

                                <span id="inputFrequencyValue">
                                    π/4
                                </span>

                            </label>


                            <input
                                type="range"
                                id="inputFrequency"
                                min="0"
                                max="3.14159"
                                step="0.01"
                                value="0.7854"
                                class="difference-range">

                        </div>

                    </div>


                    <!-- =================================================
                         INITIAL CONDITIONS
                    ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-arrow-return-right"></i>

                            <span>
                                Initial Conditions
                            </span>

                        </div>


                        <div
                            id="initialConditions"
                            class="initial-grid">

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

                                <label class="difference-label">
                                    Start n
                                </label>

                                <input
                                    type="number"
                                    id="nStart"
                                    class="difference-input"
                                    value="0"
                                    min="0"
                                    max="100">

                            </div>


                            <div>

                                <label class="difference-label">
                                    End n
                                </label>

                                <input
                                    type="number"
                                    id="nEnd"
                                    class="difference-input"
                                    value="20"
                                    min="1"
                                    max="200">

                            </div>

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

                            Difference Equation

                        </div>


                        <div
                            id="equationDisplay"
                            class="equation">

                            y[n] + 0.5y[n−1] =
                            x[n]

                        </div>

                    </div>


                    <!-- =================================================
                         GRAPH
                    ================================================== -->

                    <div class="difference-card">

                        <div class="output-header">

                            <div>

                                <h4>
                                    Output Sequence
                                </h4>

                                <p>
                                    Computed values of y[n]
                                </p>

                            </div>


                            <div class="output-badge">

                                <i class="bi bi-bar-chart-line"></i>

                                Discrete-Time

                            </div>

                        </div>


                        <div class="difference-graph">

                            <canvas
                                id="differenceCanvas">
                            </canvas>

                        </div>

                    </div>


                    <!-- =================================================
                         RESULTS TABLE
                    ================================================== -->

                    <div class="difference-card">

                        <div class="output-header">

                            <div>

                                <h4>
                                    Computed Sequence
                                </h4>

                                <p>
                                    Numerical solution for each n
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
                                            x[n]
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
                         SYSTEM INFORMATION
                    ================================================== -->

                    <div class="result-summary">

                        <div class="summary-item">

                            <span>
                                System Order
                            </span>

                            <strong id="summaryOrder">
                                2
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

            <div class="difference-reference">

                <div class="reference-title">

                    <i class="bi bi-info-circle"></i>

                    Difference Equation Reference

                </div>


                <div class="reference-grid">


                    <div class="reference-item">

                        <strong>
                            First Order
                        </strong>

                        <span>
                            y[n] + a₁y[n−1] =
                            b₀x[n] + b₁x[n−1]
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Second Order
                        </strong>

                        <span>
                            y[n] + a₁y[n−1] + a₂y[n−2] =
                            b₀x[n] + b₁x[n−1] + b₂x[n−2]
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Recursive System
                        </strong>

                        <span>
                            Output depends on previous
                            output samples.
                        </span>

                    </div>


                    <div class="reference-item">

                        <strong>
                            Initial Rest
                        </strong>

                        <span>
                            y[n] = 0 for n &lt; 0
                        </span>

                    </div>

                </div>

            </div>


        </div>

    </main>


    <!-- =========================================================
         DIFFERENCE EQUATION STYLES
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
           EQUATION HELP
        ====================================================== */

        .equation-help {

            margin-bottom: 14px;

            padding: 10px;

            border-radius: 6px;

            background:
                var(--difference-hover);

            color:
                var(--difference-muted);

            font-size: .68rem;

            line-height: 1.6;

        }


        .equation-help strong {

            display: block;

            margin-top: 4px;

            color:
                var(--difference-text);

            font-family:
                "Times New Roman",
                serif;

            font-size: .82rem;

            font-weight: normal;

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
           COEFFICIENT TITLES
        ====================================================== */

        .coefficient-title {

            margin-top: 17px;

            margin-bottom: 8px;

            color:
                var(--difference-secondary);

            font-size: .68rem;

            font-weight: 700;

        }


        /* =====================================================
           COEFFICIENT GRID
        ====================================================== */

        .coefficient-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 8px;

        }


        .coefficient-item {

            display: flex;

            align-items: center;

            gap: 6px;

        }


        .coefficient-item span {

            min-width: 32px;

            color:
                var(--difference-muted);

            font-family:
                "Times New Roman",
                serif;

            font-size: .82rem;

            font-style: italic;

        }


        .coefficient-item input {

            width: 100%;

            height: 34px;

            padding:
                0 7px;

            border:
                1px solid var(--difference-input-border);

            border-radius: 5px;

            outline: none;

            background:
                var(--difference-panel);

            color:
                var(--difference-text);

            font-size: .72rem;

        }


        .coefficient-item input:focus {

            border-color:
                var(--difference-blue);

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


        .range-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 9px;

        }


        /* =====================================================
           INITIAL CONDITIONS
        ====================================================== */

        .initial-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 8px;

        }


        .initial-item label {

            display: block;

            margin-bottom: 5px;

            color:
                var(--difference-muted);

            font-family:
                "Times New Roman",
                serif;

            font-size: .75rem;

            font-style: italic;

        }


        .initial-item input {

            width: 100%;

            height: 34px;

            padding:
                0 7px;

            border:
                1px solid var(--difference-input-border);

            border-radius: 5px;

            outline: none;

            background:
                var(--difference-panel);

            color:
                var(--difference-text);

            font-size: .72rem;

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

            width: 100%;

            height: 420px;

            padding: 10px;

        }


        #differenceCanvas {

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
                var(--difference-header);

            border-bottom:
                1px solid var(--difference-border);

            color:
                var(--difference-secondary);

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
                1px solid var(--difference-border);

            color:
                var(--difference-text);

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
                var(--difference-hover);

        }


        /* =====================================================
           SUMMARY
        ====================================================== */

        .result-summary {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

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

        }


        @media (max-width: 850px) {

            .difference-layout {

                grid-template-columns:
                    1fr;

            }

        }


        @media (max-width: 600px) {

            .difference-graph {

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

                border-right: none;

                border-bottom:
                    1px solid var(--difference-border);

            }


            .output-header {

                align-items: flex-start;

                flex-direction: column;

            }

        }
    </style>


    <!-- =========================================================
         DIFFERENCE EQUATION JAVASCRIPT
    ========================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {


                /* =================================================
                   ELEMENTS
                ================================================== */

                const systemOrder =
                    document.getElementById(
                        "systemOrder"
                    );

                const outputCoefficients =
                    document.getElementById(
                        "outputCoefficients"
                    );

                const inputCoefficients =
                    document.getElementById(
                        "inputCoefficients"
                    );

                const initialConditions =
                    document.getElementById(
                        "initialConditions"
                    );

                const inputType =
                    document.getElementById(
                        "inputType"
                    );

                const inputAmplitude =
                    document.getElementById(
                        "inputAmplitude"
                    );

                const inputFrequency =
                    document.getElementById(
                        "inputFrequency"
                    );

                const inputFrequencyGroup =
                    document.getElementById(
                        "inputFrequencyGroup"
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
                        "differenceCanvas"
                    );

                const ctx =
                    canvas.getContext("2d");


                /* =================================================
                   BUILD COEFFICIENT INPUTS
                ================================================== */

                function buildInputs() {

                    const order =
                        parseInt(
                            systemOrder.value
                        );


                    outputCoefficients.innerHTML =
                        "";


                    inputCoefficients.innerHTML =
                        "";


                    initialConditions.innerHTML =
                        "";


                    /* =============================================
                       OUTPUT COEFFICIENTS
                    ============================================== */

                    for (
                        let i = 1; i <= order; i++
                    ) {

                        const wrapper =
                            document.createElement(
                                "div"
                            );


                        wrapper.className =
                            "coefficient-item";


                        wrapper.innerHTML = `

                            <span>
                                a${i}
                            </span>

                            <input
                                type="number"
                                step="any"
                                value="0"
                                data-output-order="${i}">
                        `;


                        outputCoefficients.appendChild(
                            wrapper
                        );

                    }


                    /* =============================================
                       INPUT COEFFICIENTS
                    ============================================== */

                    for (
                        let i = 0; i <= order; i++
                    ) {

                        const wrapper =
                            document.createElement(
                                "div"
                            );


                        wrapper.className =
                            "coefficient-item";


                        wrapper.innerHTML = `

                            <span>
                                b${i}
                            </span>

                            <input
                                type="number"
                                step="any"
                                value="${i === 0 ? 1 : 0}"
                                data-input-order="${i}">
                        `;


                        inputCoefficients.appendChild(
                            wrapper
                        );

                    }


                    /* =============================================
                       INITIAL CONDITIONS
                    ============================================== */

                    for (
                        let i = 1; i <= order; i++
                    ) {

                        const wrapper =
                            document.createElement(
                                "div"
                            );


                        wrapper.className =
                            "initial-item";


                        wrapper.innerHTML = `

                            <label>
                                y[−${i}]
                            </label>

                            <input
                                type="number"
                                step="any"
                                value="0"
                                data-initial-order="${i}">
                        `;


                        initialConditions.appendChild(
                            wrapper
                        );

                    }


                    attachInputEvents();

                    updateEquation();

                }


                /* =================================================
                   READ COEFFICIENTS
                ================================================== */

                function getOutputCoefficients() {

                    const inputs =
                        document.querySelectorAll(
                            "[data-output-order]"
                        );


                    const coefficients = {};

                    inputs.forEach(
                        function(input) {

                            const index =
                                parseInt(
                                    input.dataset.outputOrder
                                );


                            coefficients[index] =
                                parseFloat(
                                    input.value
                                ) || 0;

                        }
                    );


                    return coefficients;

                }


                function getInputCoefficients() {

                    const inputs =
                        document.querySelectorAll(
                            "[data-input-order]"
                        );


                    const coefficients = {};

                    inputs.forEach(
                        function(input) {

                            const index =
                                parseInt(
                                    input.dataset.inputOrder
                                );


                            coefficients[index] =
                                parseFloat(
                                    input.value
                                ) || 0;

                        }
                    );


                    return coefficients;

                }


                function getInitialConditions() {

                    const inputs =
                        document.querySelectorAll(
                            "[data-initial-order]"
                        );


                    const conditions = {};

                    inputs.forEach(
                        function(input) {

                            const index =
                                parseInt(
                                    input.dataset.initialOrder
                                );


                            conditions[index] =
                                parseFloat(
                                    input.value
                                ) || 0;

                        }
                    );


                    return conditions;

                }


                /* =================================================
                   INPUT SIGNAL
                ================================================== */

                function getInputValue(n) {

                    const A =
                        parseFloat(
                            inputAmplitude.value
                        ) || 0;


                    const w =
                        parseFloat(
                            inputFrequency.value
                        ) || 0;


                    switch (
                        inputType.value
                    ) {


                        case "step":

                            return n >= 0 ?
                                A :
                                0;


                        case "impulse":

                            return n === 0 ?
                                A :
                                0;


                        case "ramp":

                            return n >= 0 ?
                                A * n :
                                0;


                        case "constant":

                            return A;


                        case "sine":

                            return A *
                                Math.sin(
                                    w * n
                                );


                        default:

                            return 0;

                    }

                }


                /* =================================================
                   COMPUTE DIFFERENCE EQUATION
                ================================================== */

                function computeSequence() {

                    const order =
                        parseInt(
                            systemOrder.value
                        );


                    const a =
                        getOutputCoefficients();


                    const b =
                        getInputCoefficients();


                    const initial =
                        getInitialConditions();


                    const start =
                        parseInt(
                            nStart.value
                        );


                    const end =
                        parseInt(
                            nEnd.value
                        );


                    if (end <= start) {

                        return [];

                    }


                    const x = {};

                    const y = {};


                    /* =============================================
                       INITIAL CONDITIONS
                    ============================================== */

                    for (
                        let i = 1; i <= order; i++
                    ) {

                        y[-i] =
                            initial[i] || 0;

                    }


                    /* =============================================
                       COMPUTE x[n]
                    ============================================== */

                    for (
                        let n = start - order; n <= end; n++
                    ) {

                        x[n] =
                            getInputValue(n);

                    }


                    /* =============================================
                       RECURSIVE COMPUTATION
                    ============================================== */

                    const results = [];


                    for (
                        let n = start; n <= end; n++
                    ) {

                        let value = 0;


                        /* -----------------------------------------
                           INPUT TERMS
                        ------------------------------------------ */

                        for (
                            let k = 0; k <= order; k++
                        ) {

                            value +=
                                (
                                    b[k] || 0
                                ) *
                                (
                                    x[n - k] || 0
                                );

                        }


                        /* -----------------------------------------
                           PREVIOUS OUTPUT TERMS
                           Move all a coefficients to RHS.
                        ------------------------------------------ */

                        for (
                            let k = 1; k <= order; k++
                        ) {

                            value -=
                                (
                                    a[k] || 0
                                ) *
                                (
                                    y[n - k] || 0
                                );

                        }


                        /* -----------------------------------------
                           Current output coefficient is assumed 1.
                        ------------------------------------------ */

                        y[n] =
                            value;


                        results.push({

                            n: n,

                            x: x[n] || 0,

                            y: y[n]

                        });

                    }


                    return results;

                }


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
                   UPDATE EQUATION
                ================================================== */

                function updateEquation() {

                    const order =
                        parseInt(
                            systemOrder.value
                        );


                    const a =
                        getOutputCoefficients();


                    const b =
                        getInputCoefficients();


                    let left = "y[n]";


                    for (
                        let k = 1; k <= order; k++
                    ) {

                        const coefficient =
                            a[k] || 0;


                        if (
                            coefficient === 0
                        ) {

                            continue;

                        }


                        const sign =
                            coefficient >= 0 ?
                            " + " :
                            " − ";


                        const magnitude =
                            Math.abs(
                                coefficient
                            );


                        left +=
                            sign +
                            (
                                magnitude === 1 ?
                                "" :
                                formatNumber(
                                    magnitude
                                )
                            ) +
                            `y[n−${k}]`;

                    }


                    let right = "";


                    for (
                        let k = 0; k <= order; k++
                    ) {

                        const coefficient =
                            b[k] || 0;


                        if (
                            coefficient === 0
                        ) {

                            continue;

                        }


                        const sign =
                            coefficient >= 0 ?
                            (
                                right === "" ?
                                "" :
                                " + "
                            ) :
                            " − ";


                        const magnitude =
                            Math.abs(
                                coefficient
                            );


                        right +=
                            sign +
                            (
                                magnitude === 1 ?
                                "" :
                                formatNumber(
                                    magnitude
                                )
                            ) +
                            `x[n${k === 0 ? "" : "−" + k}]`;

                    }


                    if (right === "") {

                        right = "0";

                    }


                    document
                        .getElementById(
                            "equationDisplay"
                        )
                        .textContent =
                        left +
                        " = " +
                        right;

                }


                /* =================================================
                   UPDATE TABLE
                ================================================== */

                function updateTable(results) {

                    const table =
                        document.getElementById(
                            "sequenceTable"
                        );


                    table.innerHTML =
                        "";


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
                                    ${formatNumber(row.x)}
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

                        text: styles
                            .getPropertyValue(
                                "--difference-text"
                            )
                            .trim(),

                        muted: styles
                            .getPropertyValue(
                                "--difference-muted"
                            )
                            .trim(),

                        grid: styles
                            .getPropertyValue(
                                "--difference-grid"
                            )
                            .trim(),

                        axis: styles
                            .getPropertyValue(
                                "--difference-axis"
                            )
                            .trim(),

                        blue: styles
                            .getPropertyValue(
                                "--difference-blue"
                            )
                            .trim()

                    };

                }


                /* =================================================
                   DRAW GRAPH
                ================================================== */

                function drawGraph(results) {

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
                                    Math.abs(
                                        row.y
                                    )
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

                        top: 25,

                        bottom: 45

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
                                y / maxAbs
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
                       AXES
                    ============================================== */

                    ctx.strokeStyle =
                        colors.axis;

                    ctx.lineWidth = 1.5;


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
                       STEMS
                    ============================================== */

                    ctx.strokeStyle =
                        colors.blue;

                    ctx.fillStyle =
                        colors.blue;

                    ctx.lineWidth = 2;


                    results.forEach(
                        function(row) {

                            const x =
                                xPosition(
                                    row.n
                                );

                            const y =
                                yPosition(
                                    row.y
                                );


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
                            formatNumber(value),
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

                }


                /* =================================================
                   COMPUTE AND DISPLAY
                ================================================== */

                function compute() {

                    updateEquation();


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

                    const order =
                        parseInt(
                            systemOrder.value
                        );


                    document
                        .getElementById(
                            "summaryOrder"
                        )
                        .textContent =
                        order;


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

                function attachInputEvents() {

                    document
                        .querySelectorAll(
                            ".difference-controls input, .difference-controls select"
                        )
                        .forEach(
                            function(element) {

                                element.addEventListener(
                                    "input",
                                    function() {

                                        updateEquation();

                                        compute();

                                    }
                                );


                                element.addEventListener(
                                    "change",
                                    function() {

                                        updateEquation();

                                        compute();

                                    }
                                );

                            }
                        );

                }


                /* =================================================
                   SYSTEM ORDER CHANGE
                ================================================== */

                systemOrder.addEventListener(
                    "change",
                    function() {

                        buildInputs();

                        compute();

                    }
                );


                /* =================================================
                   INPUT TYPE CHANGE
                ================================================== */

                inputType.addEventListener(
                    "change",
                    function() {

                        inputFrequencyGroup.style.display =
                            inputType.value === "sine" ?
                            "block" :
                            "none";

                        compute();

                    }
                );


                /* =================================================
                   AMPLITUDE DISPLAY
                ================================================== */

                inputAmplitude.addEventListener(
                    "input",
                    function() {

                        document
                            .getElementById(
                                "inputAmplitudeValue"
                            )
                            .textContent =
                            parseFloat(
                                inputAmplitude.value
                            ).toFixed(1);

                    }
                );


                /* =================================================
                   FREQUENCY DISPLAY
                ================================================== */

                inputFrequency.addEventListener(
                    "input",
                    function() {

                        document
                            .getElementById(
                                "inputFrequencyValue"
                            )
                            .textContent =
                            (
                                parseFloat(
                                    inputFrequency.value
                                ) /
                                Math.PI
                            ).toFixed(2) +
                            "π";

                    }
                );


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

                            systemOrder.value =
                                "2";

                            inputType.value =
                                "step";

                            inputAmplitude.value =
                                "1";

                            inputFrequency.value =
                                "0.7854";

                            nStart.value =
                                "0";

                            nEnd.value =
                                "20";


                            buildInputs();


                            const aInputs =
                                document.querySelectorAll(
                                    "[data-output-order]"
                                );


                            aInputs.forEach(
                                function(input) {

                                    input.value =
                                        "0";

                                }
                            );


                            const bInputs =
                                document.querySelectorAll(
                                    "[data-input-order]"
                                );


                            bInputs.forEach(
                                function(input) {

                                    input.value =
                                        input.dataset.inputOrder === "0" ?
                                        "1" :
                                        "0";

                                }
                            );


                            const initialInputs =
                                document.querySelectorAll(
                                    "[data-initial-order]"
                                );


                            initialInputs.forEach(
                                function(input) {

                                    input.value =
                                        "0";

                                }
                            );


                            inputFrequencyGroup.style.display =
                                "none";


                            compute();

                        }
                    );


                /* =================================================
                   INITIALIZE
                ================================================== */

                buildInputs();

                inputFrequencyGroup.style.display =
                    "none";

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