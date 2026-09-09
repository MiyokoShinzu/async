<?php

/* =========================================================
   SPECIAL SIGNALS VISUALIZER
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
                        Special Signals Visualizer
                    </h2>

                    <p>
                        Visualize and manipulate common discrete-time
                        signals by changing their parameters.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 VISUALIZER LAYOUT
            ================================================== -->

            <div class="signal-layout">


                <!-- =================================================
                     CONTROL PANEL
                ================================================== -->

                <div class="signal-controls">


                    <!-- SIGNAL TYPE -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-sliders"></i>

                            <span>
                                Signal Configuration
                            </span>

                        </div>


                        <label class="signal-label">
                            Signal Type
                        </label>


                        <select
                            id="signalType"
                            class="signal-input">

                            <option value="step">
                                Unit Step
                            </option>

                            <option value="impulse">
                                Unit Impulse
                            </option>

                            <option value="ramp">
                                Unit Ramp
                            </option>

                            <option value="exponential">
                                Exponential
                            </option>

                            <option value="sine">
                                Sinusoidal — Sine
                            </option>

                            <option value="cosine">
                                Sinusoidal — Cosine
                            </option>

                            <option value="rectangular">
                                Rectangular Pulse
                            </option>

                        </select>

                    </div>


                    <!-- =================================================
                         PARAMETERS
                    ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-tune"></i>

                            <span>
                                Parameters
                            </span>

                        </div>


                        <!-- AMPLITUDE -->

                        <div
                            class="parameter-group"
                            id="amplitudeGroup">

                            <label class="signal-label">

                                Amplitude
                                <span id="amplitudeValue">
                                    1
                                </span>

                            </label>

                            <input
                                type="range"
                                id="amplitude"
                                min="-10"
                                max="10"
                                step="0.1"
                                value="1"
                                class="signal-range">

                        </div>


                        <!-- SHIFT -->

                        <div
                            class="parameter-group">

                            <label class="signal-label">

                                Time Shift
                                <span id="shiftValue">
                                    0
                                </span>

                            </label>

                            <input
                                type="range"
                                id="shift"
                                min="-10"
                                max="10"
                                step="1"
                                value="0"
                                class="signal-range">

                        </div>


                        <!-- EXPONENTIAL FACTOR -->

                        <div
                            class="parameter-group"
                            id="exponentialGroup">

                            <label class="signal-label">

                                Exponential Factor
                                <span id="factorValue">
                                    0.8
                                </span>

                            </label>

                            <input
                                type="range"
                                id="factor"
                                min="-2"
                                max="2"
                                step="0.05"
                                value="0.8"
                                class="signal-range">

                        </div>


                        <!-- FREQUENCY -->

                        <div
                            class="parameter-group"
                            id="frequencyGroup">

                            <label class="signal-label">

                                Angular Frequency
                                <span id="frequencyValue">
                                    π/4
                                </span>

                            </label>

                            <input
                                type="range"
                                id="frequency"
                                min="0"
                                max="3.14159"
                                step="0.01"
                                value="0.7854"
                                class="signal-range">

                        </div>


                        <!-- PHASE -->

                        <div
                            class="parameter-group"
                            id="phaseGroup">

                            <label class="signal-label">

                                Phase
                                <span id="phaseValue">
                                    0
                                </span>

                            </label>

                            <input
                                type="range"
                                id="phase"
                                min="-3.14159"
                                max="3.14159"
                                step="0.01"
                                value="0"
                                class="signal-range">

                        </div>


                        <!-- PULSE WIDTH -->

                        <div
                            class="parameter-group"
                            id="widthGroup">

                            <label class="signal-label">

                                Pulse Width
                                <span id="widthValue">
                                    5
                                </span>

                            </label>

                            <input
                                type="range"
                                id="pulseWidth"
                                min="1"
                                max="15"
                                step="1"
                                value="5"
                                class="signal-range">

                        </div>

                    </div>


                    <!-- =================================================
                         RANGE
                    ================================================== -->

                    <div class="control-section">

                        <div class="control-section-title">

                            <i class="bi bi-arrows-expand"></i>

                            <span>
                                Time Range
                            </span>

                        </div>


                        <div class="range-row">

                            <div>

                                <label class="signal-label">
                                    n min
                                </label>

                                <input
                                    type="number"
                                    id="nMin"
                                    class="signal-input"
                                    value="-10"
                                    min="-100"
                                    max="0">

                            </div>


                            <div>

                                <label class="signal-label">
                                    n max
                                </label>

                                <input
                                    type="number"
                                    id="nMax"
                                    class="signal-input"
                                    value="10"
                                    min="0"
                                    max="100">

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         ACTIONS
                    ================================================== -->

                    <div class="control-actions">

                        <button
                            type="button"
                            class="signal-button primary"
                            id="resetButton">

                            <i class="bi bi-arrow-counterclockwise"></i>

                            Reset

                        </button>


                        <button
                            type="button"
                            class="signal-button"
                            id="pngButton">

                            <i class="bi bi-image"></i>

                            PNG

                        </button>


                        <button
                            type="button"
                            class="signal-button"
                            id="csvButton">

                            <i class="bi bi-filetype-csv"></i>

                            CSV

                        </button>

                    </div>

                </div>


                <!-- =================================================
                     GRAPH PANEL
                ================================================== -->

                <div class="signal-graph-panel">


                    <!-- GRAPH HEADER -->

                    <div class="graph-header">

                        <div>

                            <h4 id="signalTitle">
                                Unit Step
                            </h4>

                            <p id="signalEquation">
                                x[n] = u[n]
                            </p>

                        </div>


                        <div class="graph-badge">

                            <i class="bi bi-bar-chart-line"></i>

                            Discrete-Time

                        </div>

                    </div>


                    <!-- GRAPH -->

                    <div class="graph-container">

                        <canvas
                            id="signalCanvas">
                        </canvas>

                    </div>


                    <!-- SIGNAL INFORMATION -->

                    <div class="signal-info">

                        <div class="info-item">

                            <span>
                                Signal
                            </span>

                            <strong id="infoSignal">
                                Unit Step
                            </strong>

                        </div>


                        <div class="info-item">

                            <span>
                                Samples
                            </span>

                            <strong id="infoSamples">
                                21
                            </strong>

                        </div>


                        <div class="info-item">

                            <span>
                                n range
                            </span>

                            <strong id="infoRange">
                                -10 to 10
                            </strong>

                        </div>


                        <div class="info-item">

                            <span>
                                Maximum
                            </span>

                            <strong id="infoMax">
                                1
                            </strong>

                        </div>


                    </div>


                </div>

            </div>


            <!-- =================================================
                 EXPLANATION
            ================================================== -->

            <div class="signal-reference">

                <div class="reference-header">

                    <i class="bi bi-info-circle"></i>

                    Special Signal Reference

                </div>


                <div class="reference-grid">


                    <div class="reference-card">

                        <strong>
                            Unit Step
                        </strong>

                        <span>
                            u[n] = 1 for n ≥ 0
                        </span>

                    </div>


                    <div class="reference-card">

                        <strong>
                            Unit Impulse
                        </strong>

                        <span>
                            δ[n] = 1 at n = 0
                        </span>

                    </div>


                    <div class="reference-card">

                        <strong>
                            Unit Ramp
                        </strong>

                        <span>
                            r[n] = n u[n]
                        </span>

                    </div>


                    <div class="reference-card">

                        <strong>
                            Exponential
                        </strong>

                        <span>
                            x[n] = A aⁿ u[n]
                        </span>

                    </div>


                    <div class="reference-card">

                        <strong>
                            Sine
                        </strong>

                        <span>
                            x[n] = A sin(ωn + φ)
                        </span>

                    </div>


                    <div class="reference-card">

                        <strong>
                            Cosine
                        </strong>

                        <span>
                            x[n] = A cos(ωn + φ)
                        </span>

                    </div>


                </div>

            </div>


        </div>

    </main>


    <!-- =========================================================
         SPECIAL SIGNALS STYLES
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

        }


        /* =====================================================
           MAIN LAYOUT
        ====================================================== */

        .signal-layout {

            display: grid;

            grid-template-columns:
                290px minmax(0, 1fr);

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


        .control-section:last-of-type {

            border-bottom: none;

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
           LABELS
        ====================================================== */

        .signal-label {

            display: flex;

            justify-content: space-between;

            margin-bottom: 6px;

            color:
                var(--signal-secondary);

            font-size: .72rem;

            font-weight: 600;

        }


        .signal-label span {

            color:
                var(--signal-blue);

            font-weight: 700;

        }


        /* =====================================================
           INPUT
        ====================================================== */

        .signal-input {

            width: 100%;

            height: 39px;

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

            font-size: .76rem;

        }


        .signal-input:focus {

            border-color:
                var(--signal-blue);

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, .10);

        }


        /* =====================================================
           RANGE SLIDER
        ====================================================== */

        .parameter-group {

            margin-bottom: 16px;

        }


        .parameter-group:last-child {

            margin-bottom: 0;

        }


        .signal-range {

            width: 100%;

            accent-color:
                var(--signal-blue);

            cursor: pointer;

        }


        /* =====================================================
           RANGE INPUTS
        ====================================================== */

        .range-row {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 10px;

        }


        /* =====================================================
           ACTION BUTTONS
        ====================================================== */

        .control-actions {

            display: grid;

            grid-template-columns:
                1fr 1fr 1fr;

            gap: 7px;

            padding: 14px;

            background:
                var(--signal-hover);

        }


        .signal-button {

            height: 36px;

            border:
                1px solid var(--signal-input-border);

            border-radius: 6px;

            background:
                var(--signal-panel);

            color:
                var(--signal-secondary);

            font-family: inherit;

            font-size: .7rem;

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

            color: #ffffff;

            background:
                var(--signal-blue);

            border-color:
                var(--signal-blue);

        }


        .signal-button.primary:hover {

            opacity: .9;

        }


        /* =====================================================
           GRAPH PANEL
        ====================================================== */

        .signal-graph-panel {

            min-width: 0;

            background:
                var(--signal-bg);

            border:
                1px solid var(--signal-border);

            border-radius: 10px;

            overflow: hidden;

        }


        /* =====================================================
           GRAPH HEADER
        ====================================================== */

        .graph-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 18px 20px;

            border-bottom:
                1px solid var(--signal-border);

        }


        .graph-header h4 {

            margin: 0 0 4px;

            color:
                var(--signal-text);

            font-size: .95rem;

            font-weight: 700;

        }


        .graph-header p {

            margin: 0;

            color:
                var(--signal-muted);

            font-family:
                "Times New Roman",
                serif;

            font-size: .95rem;

            font-style: italic;

        }


        .graph-badge {

            display: flex;

            align-items: center;

            gap: 6px;

            padding:
                5px 8px;

            border-radius: 5px;

            background:
                var(--signal-hover);

            color:
                var(--signal-secondary);

            font-size: .65rem;

            font-weight: 700;

            white-space: nowrap;

        }


        /* =====================================================
           GRAPH CONTAINER
        ====================================================== */

        .graph-container {

            position: relative;

            width: 100%;

            height: 470px;

            padding: 10px;

        }


        #signalCanvas {

            width: 100%;

            height: 100%;

            display: block;

        }


        /* =====================================================
           SIGNAL INFORMATION
        ====================================================== */

        .signal-info {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            border-top:
                1px solid var(--signal-border);

        }


        .info-item {

            padding:
                13px 16px;

            border-right:
                1px solid var(--signal-border);

        }


        .info-item:last-child {

            border-right: none;

        }


        .info-item span {

            display: block;

            margin-bottom: 4px;

            color:
                var(--signal-muted);

            font-size: .65rem;

            font-weight: 600;

        }


        .info-item strong {

            color:
                var(--signal-text);

            font-size: .76rem;

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


        .reference-header {

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


        .reference-header i {

            color:
                var(--signal-blue);

        }


        .reference-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

        }


        .reference-card {

            padding:
                15px 17px;

            border-right:
                1px solid var(--signal-border);

            border-bottom:
                1px solid var(--signal-border);

        }


        .reference-card strong {

            display: block;

            margin-bottom: 5px;

            color:
                var(--signal-text);

            font-size: .76rem;

        }


        .reference-card span {

            color:
                var(--signal-muted);

            font-family:
                "Times New Roman",
                serif;

            font-size: .82rem;

            font-style: italic;

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1100px) {

            .signal-layout {

                grid-template-columns:
                    250px minmax(0, 1fr);

            }

            .reference-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }


        @media (max-width: 850px) {

            .signal-layout {

                grid-template-columns:
                    1fr;

            }

            .signal-controls {

                order: 2;

            }

            .signal-graph-panel {

                order: 1;

            }

        }


        @media (max-width: 600px) {

            .graph-container {

                height: 360px;

            }


            .signal-info {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .info-item:nth-child(2) {

                border-right: none;

            }


            .reference-grid {

                grid-template-columns:
                    1fr;

            }


            .reference-card {

                border-right: none;

            }


            .graph-header {

                align-items: flex-start;

                flex-direction: column;

            }

        }
    </style>


    <!-- =========================================================
         SIGNAL VISUALIZER JAVASCRIPT
    ========================================================== -->

    <script>
        document.addEventListener(
            "DOMContentLoaded",
            function() {


                /* =================================================
                   ELEMENTS
                ================================================== */

                const canvas =
                    document.getElementById(
                        "signalCanvas"
                    );

                const ctx =
                    canvas.getContext("2d");


                const signalType =
                    document.getElementById(
                        "signalType"
                    );

                const amplitude =
                    document.getElementById(
                        "amplitude"
                    );

                const shift =
                    document.getElementById(
                        "shift"
                    );

                const factor =
                    document.getElementById(
                        "factor"
                    );

                const frequency =
                    document.getElementById(
                        "frequency"
                    );

                const phase =
                    document.getElementById(
                        "phase"
                    );

                const pulseWidth =
                    document.getElementById(
                        "pulseWidth"
                    );

                const nMin =
                    document.getElementById(
                        "nMin"
                    );

                const nMax =
                    document.getElementById(
                        "nMax"
                    );


                /* =================================================
                   VALUE LABELS
                ================================================== */

                const amplitudeValue =
                    document.getElementById(
                        "amplitudeValue"
                    );

                const shiftValue =
                    document.getElementById(
                        "shiftValue"
                    );

                const factorValue =
                    document.getElementById(
                        "factorValue"
                    );

                const frequencyValue =
                    document.getElementById(
                        "frequencyValue"
                    );

                const phaseValue =
                    document.getElementById(
                        "phaseValue"
                    );

                const widthValue =
                    document.getElementById(
                        "widthValue"
                    );


                /* =================================================
                   PAGE ELEMENTS
                ================================================== */

                const signalTitle =
                    document.getElementById(
                        "signalTitle"
                    );

                const signalEquation =
                    document.getElementById(
                        "signalEquation"
                    );

                const infoSignal =
                    document.getElementById(
                        "infoSignal"
                    );

                const infoSamples =
                    document.getElementById(
                        "infoSamples"
                    );

                const infoRange =
                    document.getElementById(
                        "infoRange"
                    );

                const infoMax =
                    document.getElementById(
                        "infoMax"
                    );


                /* =================================================
                   PARAMETER GROUPS
                ================================================== */

                const amplitudeGroup =
                    document.getElementById(
                        "amplitudeGroup"
                    );

                const exponentialGroup =
                    document.getElementById(
                        "exponentialGroup"
                    );

                const frequencyGroup =
                    document.getElementById(
                        "frequencyGroup"
                    );

                const phaseGroup =
                    document.getElementById(
                        "phaseGroup"
                    );

                const widthGroup =
                    document.getElementById(
                        "widthGroup"
                    );


                /* =================================================
                   DEFAULT VALUES
                ================================================== */

                const defaults = {

                    type: "step",

                    amplitude: 1,

                    shift: 0,

                    factor: 0.8,

                    frequency: 0.7854,

                    phase: 0,

                    width: 5,

                    nMin: -10,

                    nMax: 10

                };


                /* =================================================
                   FORMAT PI
                ================================================== */

                function formatPi(value) {

                    const pi =
                        Math.PI;

                    const ratio =
                        value / pi;

                    if (
                        Math.abs(ratio) <
                        0.001
                    ) {

                        return "0";

                    }


                    const common = [

                        [1, "π"],

                        [2, "π/2"],

                        [3, "3π/2"],

                        [4, "π/4"],

                        [6, "π/6"],

                        [8, "π/8"],

                        [12, "π/12"]

                    ];


                    let best =
                        null;

                    let error =
                        Infinity;


                    common.forEach(
                        function(item) {

                            const denominator =
                                parseFloat(
                                    item[1]
                                    .replace("π/", "")
                                );

                            let candidate;

                            if (
                                item[1] === "π"
                            ) {

                                candidate = 1;

                            } else {

                                candidate =
                                    item[1]
                                    .split("π/")[0];

                                candidate =
                                    candidate === "" ?
                                    1 :
                                    parseFloat(
                                        candidate
                                    );

                                candidate =
                                    1 / denominator *
                                    candidate;

                            }

                            const e =
                                Math.abs(
                                    ratio -
                                    candidate
                                );

                            if (e < error) {

                                error = e;

                                best =
                                    item[1];

                            }

                        }
                    );


                    if (
                        best !== null &&
                        error < 0.025
                    ) {

                        if (ratio < 0) {

                            return "-" + best;

                        }

                        return best;

                    }


                    return value.toFixed(2);

                }


                /* =================================================
                   GET SIGNAL VALUE
                ================================================== */

                function getSignalValue(n) {

                    const A =
                        parseFloat(
                            amplitude.value
                        );

                    const k =
                        parseInt(
                            shift.value
                        );

                    const a =
                        parseFloat(
                            factor.value
                        );

                    const w =
                        parseFloat(
                            frequency.value
                        );

                    const p =
                        parseFloat(
                            phase.value
                        );

                    const width =
                        parseInt(
                            pulseWidth.value
                        );


                    const m =
                        n - k;


                    switch (
                        signalType.value
                    ) {


                        /* =========================================
                           UNIT STEP
                        ========================================== */

                        case "step":

                            return m >= 0 ?
                                A :
                                0;


                            /* =========================================
                               UNIT IMPULSE
                            ========================================== */

                        case "impulse":

                            return m === 0 ?
                                A :
                                0;


                            /* =========================================
                               UNIT RAMP
                            ========================================== */

                        case "ramp":

                            return m >= 0 ?
                                A * m :
                                0;


                            /* =========================================
                               EXPONENTIAL
                            ========================================== */

                        case "exponential":

                            return m >= 0 ?
                                A * Math.pow(a, m) :
                                0;


                            /* =========================================
                               SINE
                            ========================================== */

                        case "sine":

                            return A *
                                Math.sin(
                                    w * m + p
                                );


                            /* =========================================
                               COSINE
                            ========================================== */

                        case "cosine":

                            return A *
                                Math.cos(
                                    w * m + p
                                );


                            /* =========================================
                               RECTANGULAR PULSE
                            ========================================== */

                        case "rectangular":

                            return (
                                    m >= 0 &&
                                    m < width
                                ) ?
                                A :
                                0;


                        default:

                            return 0;

                    }

                }


                /* =================================================
                   SIGNAL INFORMATION
                ================================================== */

                function updateSignalInformation() {

                    const type =
                        signalType.value;


                    const names = {

                        step: "Unit Step",

                        impulse: "Unit Impulse",

                        ramp: "Unit Ramp",

                        exponential: "Exponential",

                        sine: "Sinusoidal — Sine",

                        cosine: "Sinusoidal — Cosine",

                        rectangular: "Rectangular Pulse"

                    };


                    const A =
                        parseFloat(
                            amplitude.value
                        );

                    const k =
                        parseInt(
                            shift.value
                        );

                    const a =
                        parseFloat(
                            factor.value
                        );

                    const w =
                        parseFloat(
                            frequency.value
                        );

                    const p =
                        parseFloat(
                            phase.value
                        );

                    const width =
                        parseInt(
                            pulseWidth.value
                        );


                    signalTitle.textContent =
                        names[type];


                    /* =============================================
                       EQUATIONS
                    ============================================== */

                    let equation = "";


                    if (type === "step") {

                        equation =
                            `x[n] = ${A}u[n${k >= 0 ? "-" + k : "+" + Math.abs(k)}]`;

                    } else if (type === "impulse") {

                        equation =
                            `x[n] = ${A}δ[n${k >= 0 ? "-" + k : "+" + Math.abs(k)}]`;

                    } else if (type === "ramp") {

                        equation =
                            `x[n] = ${A}(n${k >= 0 ? "-" + k : "+" + Math.abs(k)})u[n${k >= 0 ? "-" + k : "+" + Math.abs(k)}]`;

                    } else if (
                        type === "exponential"
                    ) {

                        equation =
                            `x[n] = ${A}(${a})ⁿu[n]`;

                    } else if (
                        type === "sine"
                    ) {

                        equation =
                            `x[n] = ${A}sin(${formatPi(w)}n + ${p.toFixed(2)})`;

                    } else if (
                        type === "cosine"
                    ) {

                        equation =
                            `x[n] = ${A}cos(${formatPi(w)}n + ${p.toFixed(2)})`;

                    } else if (
                        type === "rectangular"
                    ) {

                        equation =
                            `x[n] = ${A}, 0 ≤ n-${k} < ${width}`;

                    }


                    signalEquation.textContent =
                        equation;


                    infoSignal.textContent =
                        names[type];

                }


                /* =================================================
                   UPDATE PARAMETER VISIBILITY
                ================================================== */

                function updateParameterVisibility() {

                    const type =
                        signalType.value;


                    amplitudeGroup.style.display =
                        "block";


                    exponentialGroup.style.display =
                        type === "exponential" ?
                        "block" :
                        "none";


                    frequencyGroup.style.display =
                        (
                            type === "sine" ||
                            type === "cosine"
                        ) ?
                        "block" :
                        "none";


                    phaseGroup.style.display =
                        (
                            type === "sine" ||
                            type === "cosine"
                        ) ?
                        "block" :
                        "none";


                    widthGroup.style.display =
                        type === "rectangular" ?
                        "block" :
                        "none";

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
                        rect.width * dpr;

                    canvas.height =
                        rect.height * dpr;


                    ctx.setTransform(
                        dpr,
                        0,
                        0,
                        dpr,
                        0,
                        0
                    );


                    drawSignal();

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
                   DRAW SIGNAL
                ================================================== */

                function drawSignal() {

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


                    const minN =
                        parseInt(
                            nMin.value
                        );

                    const maxN =
                        parseInt(
                            nMax.value
                        );


                    if (
                        maxN <= minN
                    ) {

                        return;

                    }


                    const values = [];


                    for (
                        let n = minN; n <= maxN; n++
                    ) {

                        values.push({

                            n: n,

                            y: getSignalValue(n)

                        });

                    }


                    /* =============================================
                       DETERMINE Y RANGE
                    ============================================== */

                    let maxAbs =
                        1;


                    values.forEach(
                        function(point) {

                            if (
                                Number.isFinite(
                                    point.y
                                )
                            ) {

                                maxAbs =
                                    Math.max(
                                        maxAbs,
                                        Math.abs(
                                            point.y
                                        )
                                    );

                            }

                        }
                    );


                    maxAbs *= 1.2;


                    /* =============================================
                       GRAPH MARGINS
                    ============================================== */

                    const margin = {

                        left: 55,

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


                    const zeroY =
                        margin.top +
                        graphHeight / 2;


                    function xPosition(n) {

                        return margin.left +
                            (
                                (n - minN) /
                                (maxN - minN)
                            ) *
                            graphWidth;

                    }


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

                    ctx.lineWidth = 1;

                    ctx.strokeStyle =
                        colors.grid;


                    for (
                        let n = minN; n <= maxN; n++
                    ) {

                        const x =
                            xPosition(n);


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


                    /* =============================================
                       HORIZONTAL GRID
                    ============================================== */

                    const ySteps = 5;


                    for (
                        let i = -ySteps; i <= ySteps; i++
                    ) {

                        const yValue =
                            (
                                i /
                                ySteps
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


                    /* X AXIS */

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


                    /* Y AXIS */

                    const zeroX =
                        xPosition(0);


                    ctx.beginPath();

                    ctx.moveTo(
                        zeroX,
                        margin.top
                    );

                    ctx.lineTo(
                        zeroX,
                        height -
                        margin.bottom
                    );

                    ctx.stroke();


                    /* =============================================
                       STEM SIGNAL
                    ============================================== */

                    ctx.strokeStyle =
                        colors.blue;

                    ctx.fillStyle =
                        colors.blue;

                    ctx.lineWidth = 2;


                    values.forEach(
                        function(point) {

                            const x =
                                xPosition(
                                    point.n
                                );

                            const y =
                                yPosition(
                                    point.y
                                );


                            /* STEM */

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


                            /* SAMPLE MARKER */

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
                       X AXIS LABELS
                    ============================================== */

                    ctx.fillStyle =
                        colors.muted;

                    ctx.font =
                        "11px Arial";

                    ctx.textAlign =
                        "center";

                    ctx.textBaseline =
                        "top";


                    values.forEach(
                        function(point) {

                            const x =
                                xPosition(
                                    point.n
                                );


                            ctx.fillText(
                                point.n,
                                x,
                                zeroY + 9
                            );

                        }
                    );


                    /* =============================================
                       Y AXIS LABELS
                    ============================================== */

                    ctx.textAlign =
                        "right";

                    ctx.textBaseline =
                        "middle";


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


                        ctx.fillText(
                            yValue.toFixed(1),
                            margin.left - 8,
                            y
                        );

                    }


                    /* =============================================
                       AXIS LABEL
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
                        "x[n]",
                        zeroX + 8,
                        margin.top
                    );


                    /* =============================================
                       UPDATE INFORMATION
                    ============================================== */

                    infoSamples.textContent =
                        values.length;


                    infoRange.textContent =
                        `${minN} to ${maxN}`;


                    const maximum =
                        Math.max(
                            ...values.map(
                                p => p.y
                            )
                        );


                    infoMax.textContent =
                        Number.isFinite(
                            maximum
                        ) ?
                        maximum.toFixed(3) :
                        "—";

                }


                /* =================================================
                   UPDATE LABELS
                ================================================== */

                function updateLabels() {

                    amplitudeValue.textContent =
                        parseFloat(
                            amplitude.value
                        ).toFixed(1);


                    shiftValue.textContent =
                        shift.value;


                    factorValue.textContent =
                        parseFloat(
                            factor.value
                        ).toFixed(2);


                    frequencyValue.textContent =
                        formatPi(
                            parseFloat(
                                frequency.value
                            )
                        );


                    phaseValue.textContent =
                        formatPi(
                            parseFloat(
                                phase.value
                            )
                        );


                    widthValue.textContent =
                        pulseWidth.value;

                }


                /* =================================================
                   UPDATE EVERYTHING
                ================================================== */

                function updateVisualizer() {

                    updateLabels();

                    updateParameterVisibility();

                    updateSignalInformation();

                    drawSignal();

                }


                /* =================================================
                   INPUT EVENTS
                ================================================== */

                [

                    signalType,

                    amplitude,

                    shift,

                    factor,

                    frequency,

                    phase,

                    pulseWidth,

                    nMin,

                    nMax

                ].forEach(
                    function(element) {

                        element.addEventListener(
                            "input",
                            updateVisualizer
                        );

                        element.addEventListener(
                            "change",
                            updateVisualizer
                        );

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

                            signalType.value =
                                defaults.type;

                            amplitude.value =
                                defaults.amplitude;

                            shift.value =
                                defaults.shift;

                            factor.value =
                                defaults.factor;

                            frequency.value =
                                defaults.frequency;

                            phase.value =
                                defaults.phase;

                            pulseWidth.value =
                                defaults.width;

                            nMin.value =
                                defaults.nMin;

                            nMax.value =
                                defaults.nMax;


                            updateVisualizer();

                        }
                    );


                /* =================================================
                   PNG EXPORT
                ================================================== */

                document
                    .getElementById(
                        "pngButton"
                    )
                    .addEventListener(
                        "click",
                        function() {

                            const link =
                                document.createElement(
                                    "a"
                                );


                            link.download =
                                "special-signal.png";


                            link.href =
                                canvas.toDataURL(
                                    "image/png"
                                );


                            link.click();

                        }
                    );


                /* =================================================
                   CSV EXPORT
                ================================================== */

                document
                    .getElementById(
                        "csvButton"
                    )
                    .addEventListener(
                        "click",
                        function() {

                            const minN =
                                parseInt(
                                    nMin.value
                                );

                            const maxN =
                                parseInt(
                                    nMax.value
                                );


                            let csv =
                                "n,x[n]\n";


                            for (
                                let n = minN; n <= maxN; n++
                            ) {

                                csv +=
                                    `${n},${getSignalValue(n)}\n`;

                            }


                            const blob =
                                new Blob(
                                    [csv], {
                                        type: "text/csv"
                                    }
                                );


                            const url =
                                URL.createObjectURL(
                                    blob
                                );


                            const link =
                                document.createElement(
                                    "a"
                                );


                            link.href =
                                url;


                            link.download =
                                "special-signal.csv";


                            link.click();


                            URL.revokeObjectURL(
                                url
                            );

                        }
                    );


                /* =================================================
                   REDRAW WHEN THEME CHANGES
                ================================================== */

                const observer =
                    new MutationObserver(
                        function() {

                            drawSignal();

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
                    resizeCanvas
                );


                /* =================================================
                   INITIALIZE
                ================================================== */

                updateVisualizer();

                resizeCanvas();

            }
        );
    </script>


    <!-- =========================================================
         GLOBAL SCRIPTS
    ========================================================== -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>