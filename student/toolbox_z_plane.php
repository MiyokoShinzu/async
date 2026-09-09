<?php

/* =========================================================
   Z-PLANE PLOTTER
   ETS-Async Learning Portal
   ========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION
   ========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["user"]) ||
    !isset($_SESSION["user"]["access"]) ||
    $_SESSION["user"]["access"] !== "student"
) {

    header("Location: ../login.php");
    exit;

}


/* =========================================================
   DATABASE CONNECTION
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   USER INFORMATION
   ========================================================= */

$user = $_SESSION["user"];

$firstName =
    trim(
        $user["first_name"] ??
        $user["firstname"] ??
        ""
    );

if ($firstName === "") {
    $firstName = "Student";
}


/* =========================================================
   PAGE TITLE
   ========================================================= */

$pageTitle = "Z-Plane Plotter";


/* =========================================================
   GLOBAL HEAD
   ========================================================= */

include "globals/head.php";

?>

<style>

/* =========================================================
   Z-PLANE PAGE
   ========================================================= */

.zplane-page {
    padding-bottom: 40px;
}


/* =========================================================
   PAGE HEADER
   ========================================================= */

.zplane-header {
    margin-bottom: 22px;
}

.zplane-header h2 {
    font-weight: 700;
    color: var(--text-color, #1f2937);
    margin-bottom: 6px;
}

.zplane-header p {
    color: var(--text-secondary, #6b7280);
    margin-bottom: 0;
}


/* =========================================================
   CARD
   ========================================================= */

.zplane-card {
    background: var(--activity-card-bg, #ffffff);
    border: 1px solid var(--activity-border, #e5e7eb);
    border-radius: 14px;
    box-shadow:
        0 4px 16px
        var(--shadow-color, rgba(0, 0, 0, 0.08));
    overflow: hidden;
}

.zplane-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--activity-border, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.zplane-card-header h5 {
    margin: 0;
    font-weight: 700;
    color: var(--text-color, #1f2937);
}

.zplane-card-header h5 i {
    margin-right: 8px;
}

.zplane-card-body {
    padding: 20px;
}


/* =========================================================
   INPUT LABEL
   ========================================================= */

.zplane-label {
    font-weight: 600;
    color: var(--text-color, #1f2937);
    margin-bottom: 7px;
    display: block;
}

.zplane-help {
    font-size: 12px;
    color: var(--text-secondary, #6b7280);
    margin-top: 6px;
    display: block;
}


/* =========================================================
   TEXTAREA
   ========================================================= */

.zplane-input {
    width: 100%;
    min-height: 145px;
    resize: vertical;

    border: 1px solid var(--activity-border, #d1d5db);
    border-radius: 10px;

    padding: 12px 14px;

    background: var(--activity-card-bg, #ffffff);
    color: var(--text-color, #1f2937);

    font-family:
        "Roboto Mono",
        Consolas,
        monospace;

    font-size: 14px;

    outline: none;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.zplane-input:focus {
    border-color: #4f46e5;

    box-shadow:
        0 0 0 3px
        rgba(79, 70, 229, 0.12);
}


/* =========================================================
   EXAMPLE BOX
   ========================================================= */

.example-box {
    background: rgba(79, 70, 229, 0.06);
    border: 1px solid rgba(79, 70, 229, 0.15);

    border-radius: 10px;

    padding: 12px 14px;

    font-size: 13px;

    color: var(--text-color, #374151);

    margin-top: 15px;
}

.example-box strong {
    display: block;
    margin-bottom: 5px;
}

.example-box code {
    font-family:
        "Roboto Mono",
        Consolas,
        monospace;

    color: var(--activity-indigo, #4f46e5);
}


/* =========================================================
   BUTTONS
   ========================================================= */

.zplane-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 18px;
}

.zplane-buttons .btn {
    border-radius: 9px;
}


/* =========================================================
   CANVAS CONTAINER
   ========================================================= */

.canvas-wrapper {
    position: relative;

    width: 100%;
    height: 610px;

    border:
        1px solid
        var(--activity-border, #d1d5db);

    border-radius: 12px;

    overflow: hidden;

    background: #ffffff;

    cursor: crosshair;
}

[data-theme="dark"] .canvas-wrapper {
    background: #111827;
}

#zplaneCanvas {
    display: block;

    width: 100%;
    height: 100%;
}


/* =========================================================
   CANVAS CONTROLS
   ========================================================= */

.canvas-controls {
    position: absolute;

    right: 12px;
    top: 12px;

    display: flex;
    gap: 6px;

    z-index: 5;
}

.canvas-control-btn {
    width: 38px;
    height: 38px;

    border-radius: 8px;

    border: 1px solid #d1d5db;

    background: rgba(255, 255, 255, 0.94);

    color: #374151;

    display: flex;
    align-items: center;
    justify-content: center;

    cursor: pointer;

    box-shadow:
        0 2px 7px
        rgba(0, 0, 0, 0.12);
}

.canvas-control-btn:hover {
    background: #f3f4f6;
}

[data-theme="dark"] .canvas-control-btn {
    background: rgba(31, 41, 55, 0.94);
    color: #f9fafb;
    border-color: #4b5563;
}


/* =========================================================
   LEGEND
   ========================================================= */

.zplane-legend {
    display: flex;
    flex-wrap: wrap;

    gap: 18px;

    margin-top: 12px;

    color: var(--text-secondary, #6b7280);

    font-size: 13px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 7px;
}

.legend-zero {
    width: 14px;
    height: 14px;

    border: 2px solid #2563eb;

    border-radius: 50%;
}

.legend-pole {
    position: relative;

    width: 14px;
    height: 14px;
}

.legend-pole::before,
.legend-pole::after {
    content: "";

    position: absolute;

    width: 16px;
    height: 2px;

    background: #dc2626;

    top: 6px;
    left: -1px;
}

.legend-pole::before {
    transform: rotate(45deg);
}

.legend-pole::after {
    transform: rotate(-45deg);
}

.legend-circle {
    width: 15px;
    height: 15px;

    border: 1.5px solid #6b7280;

    border-radius: 50%;
}


/* =========================================================
   STATUS
   ========================================================= */

#plotStatus {
    margin-top: 15px;
}


/* =========================================================
   DATA TABLE
   ========================================================= */

.zplane-table-wrapper {
    overflow-x: auto;
}

.zplane-table {
    width: 100%;
    border-collapse: collapse;

    color: var(--text-color, #1f2937);
}

.zplane-table th,
.zplane-table td {
    padding: 10px 12px;

    border-bottom:
        1px solid
        var(--activity-border, #e5e7eb);

    text-align: left;
}

.zplane-table th {
    font-size: 12px;

    text-transform: uppercase;

    letter-spacing: 0.04em;

    color: var(--text-secondary, #6b7280);
}

.zplane-table td {
    font-family:
        "Roboto Mono",
        Consolas,
        monospace;

    font-size: 13px;
}


/* =========================================================
   TYPE BADGES
   ========================================================= */

.zero-badge {
    display: inline-block;

    padding: 4px 8px;

    border-radius: 6px;

    background: rgba(37, 99, 235, 0.10);

    color: #2563eb;

    font-size: 11px;

    font-weight: 700;
}

.pole-badge {
    display: inline-block;

    padding: 4px 8px;

    border-radius: 6px;

    background: rgba(220, 38, 38, 0.10);

    color: #dc2626;

    font-size: 11px;

    font-weight: 700;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 991px) {

    .canvas-wrapper {
        height: 500px;
    }

}

@media (max-width: 576px) {

    .zplane-card-body {
        padding: 14px;
    }

    .canvas-wrapper {
        height: 420px;
    }

    .zplane-buttons .btn {
        flex: 1 1 auto;
    }

}


/* =========================================================
   DARK MODE
   ========================================================= */

[data-theme="dark"] .zplane-input {
    background: #111827;
    color: #f9fafb;
    border-color: #374151;
}

[data-theme="dark"] .example-box {
    background: rgba(99, 102, 241, 0.10);
    border-color: rgba(99, 102, 241, 0.20);
}

</style>


<body>


<!-- =========================================================
     SIDEBAR
     ========================================================= -->

<?php include "globals/sidebar.php"; ?>


<!-- =========================================================
     SIDEBAR OVERLAY
     ========================================================= -->

<div
    class="sidebar-overlay"
    id="sidebarOverlay">
</div>


<!-- =========================================================
     TOPBAR
     ========================================================= -->

<?php include "globals/topbar.php"; ?>


<!-- =========================================================
     MAIN CONTENT
     ========================================================= -->

<main class="main-content">

    <div class="content-wrapper zplane-page">


        <!-- =================================================
             PAGE HEADER
             ================================================= -->

        <div class="zplane-header">

            <h2>
                <i class="bi bi-graph-up"></i>
                Z-Plane Plotter
            </h2>

            <p>
                Plot zeros and poles of a discrete-time system
                on the complex z-plane.
            </p>

        </div>


        <!-- =================================================
             MAIN PLOTTER CARD
             ================================================= -->

        <div class="zplane-card">

            <div class="zplane-card-header">

                <h5>
                    <i class="bi bi-bullseye"></i>
                    Z Plane
                </h5>

                <span
                    class="badge text-bg-secondary"
                    id="pointCountBadge">
                    0 points
                </span>

            </div>


            <div class="zplane-card-body">

                <div class="row g-4">


                    <!-- =====================================
                         INPUT PANEL
                         ===================================== -->

                    <div class="col-lg-4">

                        <!-- ZEROS -->

                        <label
                            for="zerosInput"
                            class="zplane-label">

                            <i class="bi bi-circle"></i>
                            Zeros

                        </label>

                        <textarea
                            id="zerosInput"
                            class="zplane-input"
                            spellcheck="false"
                            placeholder="Enter zeros...

Examples:
0
1
-1
0.5+0.5j
0.5-0.5j
j
-j"></textarea>

                        <span class="zplane-help">

                            Enter one value per line,
                            or separate values with commas.

                        </span>


                        <!-- POLES -->

                        <div class="mt-4">

                            <label
                                for="polesInput"
                                class="zplane-label">

                                <i class="bi bi-x-lg"></i>
                                Poles

                            </label>

                            <textarea
                                id="polesInput"
                                class="zplane-input"
                                spellcheck="false"
                                placeholder="Enter poles...

Examples:
0.8
0.8+0.3j
0.8-0.3j
1
-1
2j
-2j"></textarea>

                            <span class="zplane-help">

                                Complex conjugate pairs can be
                                entered separately.

                            </span>

                        </div>


                        <!-- EXAMPLES -->

                        <div class="example-box">

                            <strong>
                                <i class="bi bi-lightbulb"></i>
                                Accepted formats
                            </strong>

                            <code>3</code>,
                            <code>-2</code>,
                            <code>3j</code>,
                            <code>-2j</code>,
                            <code>j</code>,
                            <code>-j</code>,
                            <code>2+3j</code>,
                            <code>2-3j</code>,
                            <code>-2+4j</code>,
                            <code>2+3i</code>

                        </div>


                        <!-- BUTTONS -->

                        <div class="zplane-buttons">

                            <button
                                type="button"
                                class="btn btn-primary"
                                id="plotBtn">

                                <i class="bi bi-play-fill"></i>
                                Plot

                            </button>


                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                id="fitBtn">

                                <i class="bi bi-arrows-fullscreen"></i>
                                Fit

                            </button>


                            <button
                                type="button"
                                class="btn btn-outline-danger"
                                id="clearBtn">

                                <i class="bi bi-trash"></i>
                                Clear

                            </button>

                        </div>


                        <!-- EXPORT BUTTONS -->

                        <div class="zplane-buttons">

                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                id="pngBtn">

                                <i class="bi bi-image"></i>
                                PNG

                            </button>


                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                id="svgBtn">

                                <i class="bi bi-filetype-svg"></i>
                                SVG

                            </button>


                            <button
                                type="button"
                                class="btn btn-outline-success"
                                id="csvBtn">

                                <i class="bi bi-filetype-csv"></i>
                                CSV

                            </button>

                        </div>


                        <!-- STATUS -->

                        <div id="plotStatus"></div>

                    </div>


                    <!-- =====================================
                         CANVAS
                         ===================================== -->

                    <div class="col-lg-8">

                        <div class="canvas-wrapper">

                            <canvas
                                id="zplaneCanvas">
                            </canvas>


                            <!-- CANVAS CONTROLS -->

                            <div class="canvas-controls">

                                <button
                                    type="button"
                                    class="canvas-control-btn"
                                    id="zoomInBtn"
                                    title="Zoom in">

                                    <i class="bi bi-plus-lg"></i>

                                </button>


                                <button
                                    type="button"
                                    class="canvas-control-btn"
                                    id="zoomOutBtn"
                                    title="Zoom out">

                                    <i class="bi bi-dash-lg"></i>

                                </button>


                                <button
                                    type="button"
                                    class="canvas-control-btn"
                                    id="resetViewBtn"
                                    title="Reset view">

                                    <i class="bi bi-arrow-counterclockwise"></i>

                                </button>

                            </div>

                        </div>


                        <!-- LEGEND -->

                        <div class="zplane-legend">

                            <div class="legend-item">

                                <span class="legend-zero"></span>

                                <span>
                                    Zero
                                </span>

                            </div>


                            <div class="legend-item">

                                <span class="legend-pole"></span>

                                <span>
                                    Pole
                                </span>

                            </div>


                            <div class="legend-item">

                                <span class="legend-circle"></span>

                                <span>
                                    Unit circle
                                </span>

                            </div>


                            <div class="legend-item">

                                <span>
                                    <i class="bi bi-mouse"></i>
                                    Drag to pan
                                </span>

                            </div>


                            <div class="legend-item">

                                <span>
                                    <i class="bi bi-mouse2"></i>
                                    Scroll to zoom
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =================================================
             DATA TABLE
             ================================================= -->

        <div class="zplane-card mt-4">

            <div class="zplane-card-header">

                <h5>
                    <i class="bi bi-table"></i>
                    Parsed Points
                </h5>

            </div>


            <div class="zplane-card-body">

                <div class="zplane-table-wrapper">

                    <table class="zplane-table">

                        <thead>

                            <tr>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Input
                                </th>

                                <th>
                                    Real
                                </th>

                                <th>
                                    Imaginary
                                </th>

                                <th>
                                    Magnitude
                                </th>

                                <th>
                                    Angle
                                </th>

                            </tr>

                        </thead>

                        <tbody id="pointsTableBody">

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center text-secondary">

                                    No points plotted yet.

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</main>


<!-- =========================================================
     JAVASCRIPT
     ========================================================= -->

<script>

(function () {

    "use strict";


    /* =====================================================
       DOM ELEMENTS
       ===================================================== */

    const canvas =
        document.getElementById("zplaneCanvas");

    const ctx =
        canvas.getContext("2d");

    const zerosInput =
        document.getElementById("zerosInput");

    const polesInput =
        document.getElementById("polesInput");

    const plotBtn =
        document.getElementById("plotBtn");

    const fitBtn =
        document.getElementById("fitBtn");

    const clearBtn =
        document.getElementById("clearBtn");

    const pngBtn =
        document.getElementById("pngBtn");

    const svgBtn =
        document.getElementById("svgBtn");

    const csvBtn =
        document.getElementById("csvBtn");

    const zoomInBtn =
        document.getElementById("zoomInBtn");

    const zoomOutBtn =
        document.getElementById("zoomOutBtn");

    const resetViewBtn =
        document.getElementById("resetViewBtn");

    const plotStatus =
        document.getElementById("plotStatus");

    const pointCountBadge =
        document.getElementById("pointCountBadge");

    const pointsTableBody =
        document.getElementById("pointsTableBody");


    /* =====================================================
       DATA
       ===================================================== */

    let zeros = [];

    let poles = [];

    let errors = [];


    /* =====================================================
       VIEW STATE
       ===================================================== */

    let view = {

        centerX: 0,

        centerY: 0,

        scale: 100

    };


    let canvasWidth = 0;

    let canvasHeight = 0;


    /* =====================================================
       DRAG STATE
       ===================================================== */

    let isDragging = false;

    let dragStartX = 0;

    let dragStartY = 0;

    let dragStartCenterX = 0;

    let dragStartCenterY = 0;


    /* =====================================================
       COLORS
       ===================================================== */

    const COLORS = {

        axis:
            "#6b7280",

        grid:
            "#e5e7eb",

        gridDark:
            "#374151",

        unitCircle:
            "#6b7280",

        zero:
            "#2563eb",

        pole:
            "#dc2626",

        text:
            "#374151",

        textDark:
            "#e5e7eb"

    };


    /* =====================================================
       DEVICE PIXEL RATIO / RESIZE
       ===================================================== */

    function resizeCanvas() {

        const rect =
            canvas.getBoundingClientRect();

        const dpr =
            window.devicePixelRatio || 1;

        canvasWidth =
            rect.width;

        canvasHeight =
            rect.height;

        canvas.width =
            Math.round(
                rect.width * dpr
            );

        canvas.height =
            Math.round(
                rect.height * dpr
            );

        ctx.setTransform(
            dpr,
            0,
            0,
            dpr,
            0,
            0
        );

        draw();

    }


    /* =====================================================
       DARK MODE DETECTION
       ===================================================== */

    function isDarkMode() {

        return (
            document.documentElement
                .getAttribute("data-theme") ===
            "dark"
        );

    }


    /* =====================================================
       COMPLEX NUMBER PARSER
       ===================================================== */

    function parseComplex(value) {

        let original =
            value.trim();

        if (original === "") {

            return null;

        }


        let s =
            original
                .toLowerCase()
                .replace(/\s+/g, "")
                .replace(/i/g, "j");


        /* -----------------------------------------------
           PURE IMAGINARY
           ----------------------------------------------- */

        if (s === "j") {

            return {

                re: 0,

                im: 1,

                notation: original

            };

        }


        if (s === "+j") {

            return {

                re: 0,

                im: 1,

                notation: original

            };

        }


        if (s === "-j") {

            return {

                re: 0,

                im: -1,

                notation: original

            };

        }


        /* -----------------------------------------------
           PURE REAL
           ----------------------------------------------- */

        if (!s.includes("j")) {

            const real =
                Number(s);

            if (!Number.isFinite(real)) {

                throw new Error(
                    "Invalid real number"
                );

            }

            return {

                re: real,

                im: 0,

                notation: original

            };

        }


        /* -----------------------------------------------
           MUST END WITH j
           ----------------------------------------------- */

        if (!s.endsWith("j")) {

            throw new Error(
                "Complex number must end with j or i"
            );

        }


        /* Remove final j */

        const body =
            s.slice(
                0,
                -1
            );


        /* -----------------------------------------------
           PURE IMAGINARY
           ----------------------------------------------- */

        if (
            body === "" ||
            body === "+" ||
            body === "-"
        ) {

            let imaginary = 1;

            if (body === "-") {

                imaginary = -1;

            }

            return {

                re: 0,

                im: imaginary,

                notation: original

            };

        }


        /* -----------------------------------------------
           FIND REAL/IMAGINARY SEPARATOR
           ----------------------------------------------- */

        let splitIndex = -1;

        for (
            let i = 1;
            i < body.length;
            i++
        ) {

            if (
                body[i] === "+" ||
                body[i] === "-"
            ) {

                splitIndex = i;

            }

        }


        /* -----------------------------------------------
           ONLY IMAGINARY PART
           ----------------------------------------------- */

        if (splitIndex === -1) {

            let imaginary;

            if (
                body === "+" ||
                body === ""
            ) {

                imaginary = 1;

            }
            else if (body === "-") {

                imaginary = -1;

            }
            else {

                imaginary =
                    Number(body);

            }

            if (
                !Number.isFinite(imaginary)
            ) {

                throw new Error(
                    "Invalid imaginary number"
                );

            }

            return {

                re: 0,

                im: imaginary,

                notation: original

            };

        }


        /* -----------------------------------------------
           REAL + IMAGINARY
           ----------------------------------------------- */

        const realPart =
            body.slice(
                0,
                splitIndex
            );

        const imaginaryPart =
            body.slice(
                splitIndex
            );


        const real =
            Number(realPart);


        let imaginary;


        if (
            imaginaryPart === "+" ||
            imaginaryPart === ""
        ) {

            imaginary = 1;

        }
        else if (
            imaginaryPart === "-"
        ) {

            imaginary = -1;

        }
        else {

            imaginary =
                Number(imaginaryPart);

        }


        if (
            !Number.isFinite(real) ||
            !Number.isFinite(imaginary)
        ) {

            throw new Error(
                "Invalid complex number"
            );

        }


        return {

            re: real,

            im: imaginary,

            notation: original

        };

    }


    /* =====================================================
       PARSE INPUT LIST
       ===================================================== */

    function parseInput(text, type) {

        const values =
            text
                .split(/[\n,;]+/)
                .map(value => value.trim())
                .filter(value => value !== "");


        const parsed = [];


        values.forEach(
            function (value, index) {

                try {

                    const point =
                        parseComplex(value);

                    if (point) {

                        point.type =
                            type;

                        parsed.push(point);

                    }

                }
                catch (error) {

                    errors.push(
                        type +
                        " #" +
                        (index + 1) +
                        ": " +
                        value +
                        " — " +
                        error.message
                    );

                }

            }
        );


        return parsed;

    }


    /* =====================================================
       READ INPUTS
       ===================================================== */

    function readInputs() {

        errors = [];


        zeros =
            parseInput(
                zerosInput.value,
                "Zero"
            );


        poles =
            parseInput(
                polesInput.value,
                "Pole"
            );


        updateStatus();

        updateTable();

        updatePointCount();

    }


    /* =====================================================
       FORMAT NUMBER
       ===================================================== */

    function formatNumber(number) {

        if (
            Math.abs(number) < 1e-10
        ) {

            number = 0;

        }


        return Number(
            number.toFixed(6)
        ).toString();

    }


    /* =====================================================
       FORMAT COMPLEX NUMBER
       ===================================================== */

    function formatComplex(point) {

        const re =
            point.re;

        const im =
            point.im;


        if (
            Math.abs(im) < 1e-10
        ) {

            return formatNumber(re);

        }


        if (
            Math.abs(re) < 1e-10
        ) {

            if (im === 1) {

                return "j";

            }

            if (im === -1) {

                return "-j";

            }

            return (
                formatNumber(im) +
                "j"
            );

        }


        let imaginaryText;


        if (Math.abs(im) === 1) {

            imaginaryText = "j";

        }
        else {

            imaginaryText =
                formatNumber(
                    Math.abs(im)
                ) +
                "j";

        }


        return (
            formatNumber(re) +
            (
                im >= 0
                    ? "+"
                    : "-"
            ) +
            imaginaryText
        );

    }


    /* =====================================================
       UPDATE STATUS
       ===================================================== */

    function updateStatus() {

        if (errors.length === 0) {

            plotStatus.innerHTML =

                '<div class="alert alert-success py-2 mb-0">' +
                '<i class="bi bi-check-circle"></i> ' +
                'All complex numbers were parsed successfully.' +
                '</div>';

            return;

        }


        let html =

            '<div class="alert alert-danger py-2 mb-0">' +
            '<strong><i class="bi bi-exclamation-triangle"></i> ' +
            'Input errors:</strong>' +
            '<ul class="mb-0 mt-1">';


        errors.forEach(
            function (error) {

                html +=
                    "<li>" +
                    escapeHtml(error) +
                    "</li>";

            }
        );


        html +=
            "</ul></div>";


        plotStatus.innerHTML =
            html;

    }


    /* =====================================================
       UPDATE POINT COUNT
       ===================================================== */

    function updatePointCount() {

        const total =
            zeros.length +
            poles.length;


        pointCountBadge.textContent =
            total +
            (
                total === 1
                    ? " point"
                    : " points"
            );

    }


    /* =====================================================
       ESCAPE HTML
       ===================================================== */

    function escapeHtml(value) {

        return value
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");

    }


    /* =====================================================
       UPDATE TABLE
       ===================================================== */

    function updateTable() {

        const allPoints =
            zeros.concat(poles);


        if (allPoints.length === 0) {

            pointsTableBody.innerHTML =

                '<tr>' +
                '<td colspan="6" class="text-center text-secondary">' +
                'No points plotted yet.' +
                '</td>' +
                '</tr>';

            return;

        }


        let html = "";


        allPoints.forEach(
            function (point) {

                const magnitude =
                    Math.sqrt(
                        point.re * point.re +
                        point.im * point.im
                    );


                const angle =
                    Math.atan2(
                        point.im,
                        point.re
                    ) *
                    180 /
                    Math.PI;


                const badge =
                    point.type === "Zero"
                        ? '<span class="zero-badge">ZERO</span>'
                        : '<span class="pole-badge">POLE</span>';


                html +=

                    "<tr>" +

                    "<td>" +
                    badge +
                    "</td>" +

                    "<td>" +
                    escapeHtml(
                        point.notation
                    ) +
                    "</td>" +

                    "<td>" +
                    formatNumber(point.re) +
                    "</td>" +

                    "<td>" +
                    formatNumber(point.im) +
                    "</td>" +

                    "<td>" +
                    formatNumber(magnitude) +
                    "</td>" +

                    "<td>" +
                    formatNumber(angle) +
                    "°</td>" +

                    "</tr>";

            }
        );


        pointsTableBody.innerHTML =
            html;

    }


    /* =====================================================
       WORLD TO SCREEN
       ===================================================== */

    function worldToScreen(
        x,
        y
    ) {

        return {

            x:
                canvasWidth / 2 +
                (
                    x -
                    view.centerX
                ) *
                view.scale,

            y:
                canvasHeight / 2 -
                (
                    y -
                    view.centerY
                ) *
                view.scale

        };

    }


    /* =====================================================
       SCREEN TO WORLD
       ===================================================== */

    function screenToWorld(
        x,
        y
    ) {

        return {

            x:
                (
                    x -
                    canvasWidth / 2
                ) /
                view.scale +
                view.centerX,

            y:
                (
                    canvasHeight / 2 -
                    y
                ) /
                view.scale +
                view.centerY

        };

    }


    /* =====================================================
       NICE GRID STEP
       ===================================================== */

    function niceGridStep() {

        const targetPixels =
            70;


        const raw =
            targetPixels /
            view.scale;


        const power =
            Math.pow(
                10,
                Math.floor(
                    Math.log10(raw)
                )
            );


        const normalized =
            raw /
            power;


        let step;


        if (normalized < 1.5) {

            step = 1;

        }
        else if (normalized < 3) {

            step = 2;

        }
        else if (normalized < 7) {

            step = 5;

        }
        else {

            step = 10;

        }


        return step * power;

    }


    /* =====================================================
       DRAW GRID
       ===================================================== */

    function drawGrid() {

        const dark =
            isDarkMode();


        const step =
            niceGridStep();


        const topLeft =
            screenToWorld(
                0,
                0
            );


        const bottomRight =
            screenToWorld(
                canvasWidth,
                canvasHeight
            );


        const startX =
            Math.floor(
                topLeft.x / step
            ) *
            step;


        const endX =
            Math.ceil(
                bottomRight.x / step
            ) *
            step;


        const startY =
            Math.floor(
                bottomRight.y / step
            ) *
            step;


        const endY =
            Math.ceil(
                topLeft.y / step
            ) *
            step;


        ctx.lineWidth = 1;


        ctx.strokeStyle =
            dark
                ? COLORS.gridDark
                : COLORS.grid;


        ctx.beginPath();


        for (
            let x = startX;
            x <= endX;
            x += step
        ) {

            const p =
                worldToScreen(
                    x,
                    0
                );


            ctx.moveTo(
                p.x,
                0
            );

            ctx.lineTo(
                p.x,
                canvasHeight
            );

        }


        for (
            let y = startY;
            y <= endY;
            y += step
        ) {

            const p =
                worldToScreen(
                    0,
                    y
                );


            ctx.moveTo(
                0,
                p.y
            );

            ctx.lineTo(
                canvasWidth,
                p.y
            );

        }


        ctx.stroke();

    }


    /* =====================================================
       DRAW AXES
       ===================================================== */

    function drawAxes() {

        const dark =
            isDarkMode();


        const origin =
            worldToScreen(
                0,
                0
            );


        ctx.lineWidth = 1.5;

        ctx.strokeStyle =
            dark
                ? "#9ca3af"
                : COLORS.axis;


        ctx.beginPath();


        /* X AXIS */

        ctx.moveTo(
            0,
            origin.y
        );

        ctx.lineTo(
            canvasWidth,
            origin.y
        );


        /* Y AXIS */

        ctx.moveTo(
            origin.x,
            0
        );

        ctx.lineTo(
            origin.x,
            canvasHeight
        );


        ctx.stroke();


        /* =================================================
           AXIS ARROWS
           ================================================= */

        drawArrow(
            canvasWidth - 8,
            origin.y,
            canvasWidth - 1,
            origin.y
        );


        drawArrow(
            origin.x,
            8,
            origin.x,
            1
        );


        /* =================================================
           AXIS LABELS
           ================================================= */

        ctx.font =
            "bold 14px Arial";

        ctx.fillStyle =
            dark
                ? COLORS.textDark
                : COLORS.text;


        ctx.fillText(
            "Re{z}",
            canvasWidth - 45,
            origin.y - 10
        );


        ctx.fillText(
            "Im{z}",
            origin.x + 10,
            18
        );

    }


    /* =====================================================
       DRAW ARROW
       ===================================================== */

    function drawArrow(
        x1,
        y1,
        x2,
        y2
    ) {

        const angle =
            Math.atan2(
                y2 - y1,
                x2 - x1
            );


        const size = 6;


        ctx.beginPath();

        ctx.moveTo(
            x2,
            y2
        );

        ctx.lineTo(
            x2 -
            size *
            Math.cos(angle - Math.PI / 6),

            y2 -
            size *
            Math.sin(angle - Math.PI / 6)
        );

        ctx.lineTo(
            x2 -
            size *
            Math.cos(angle + Math.PI / 6),

            y2 -
            size *
            Math.sin(angle + Math.PI / 6)
        );

        ctx.closePath();

        ctx.fillStyle =
            ctx.strokeStyle;

        ctx.fill();

    }


    /* =====================================================
       DRAW GRID LABELS
       ===================================================== */

    function drawLabels() {

        const dark =
            isDarkMode();


        const step =
            niceGridStep();


        const topLeft =
            screenToWorld(
                0,
                0
            );


        const bottomRight =
            screenToWorld(
                canvasWidth,
                canvasHeight
            );


        const startX =
            Math.floor(
                topLeft.x / step
            ) *
            step;


        const endX =
            Math.ceil(
                bottomRight.x / step
            ) *
            step;


        const startY =
            Math.floor(
                bottomRight.y / step
            ) *
            step;


        const endY =
            Math.ceil(
                topLeft.y / step
            ) *
            step;


        const origin =
            worldToScreen(
                0,
                0
            );


        ctx.font =
            "11px Arial";


        ctx.fillStyle =
            dark
                ? "#9ca3af"
                : "#6b7280";


        /* X LABELS */

        for (
            let x = startX;
            x <= endX;
            x += step
        ) {

            if (
                Math.abs(x) < 1e-10
            ) {

                continue;

            }


            const p =
                worldToScreen(
                    x,
                    0
                );


            if (
                p.x < 20 ||
                p.x > canvasWidth - 20
            ) {

                continue;

            }


            ctx.textAlign =
                "center";

            ctx.fillText(
                formatNumber(x),
                p.x,
                origin.y + 16
            );

        }


        /* Y LABELS */

        for (
            let y = startY;
            y <= endY;
            y += step
        ) {

            if (
                Math.abs(y) < 1e-10
            ) {

                continue;

            }


            const p =
                worldToScreen(
                    0,
                    y
                );


            if (
                p.y < 15 ||
                p.y > canvasHeight - 10
            ) {

                continue;

            }


            ctx.textAlign =
                "right";

            ctx.fillText(
                formatNumber(y),
                origin.x - 8,
                p.y + 4
            );

        }


        /* ORIGIN */

        if (
            origin.x >= 0 &&
            origin.x <= canvasWidth &&
            origin.y >= 0 &&
            origin.y <= canvasHeight
        ) {

            ctx.textAlign =
                "right";

            ctx.fillText(
                "0",
                origin.x - 8,
                origin.y + 16
            );

        }

    }


    /* =====================================================
       DRAW UNIT CIRCLE
       ===================================================== */

    function drawUnitCircle() {

        const center =
            worldToScreen(
                0,
                0
            );


        ctx.beginPath();

        ctx.arc(
            center.x,
            center.y,
            view.scale,
            0,
            Math.PI * 2
        );


        ctx.strokeStyle =
            COLORS.unitCircle;

        ctx.lineWidth = 2;

        ctx.setLineDash([
            6,
            5
        ]);

        ctx.stroke();

        ctx.setLineDash([]);

    }


    /* =====================================================
       DRAW ZERO
       ===================================================== */

    function drawZero(point) {

        const p =
            worldToScreen(
                point.re,
                point.im
            );


        const radius = 7;


        ctx.beginPath();

        ctx.arc(
            p.x,
            p.y,
            radius,
            0,
            Math.PI * 2
        );


        ctx.fillStyle =
            isDarkMode()
                ? "#111827"
                : "#ffffff";

        ctx.fill();


        ctx.strokeStyle =
            COLORS.zero;

        ctx.lineWidth = 2.5;

        ctx.stroke();


        drawPointLabel(
            point,
            p,
            COLORS.zero
        );

    }


    /* =====================================================
       DRAW POLE
       ===================================================== */

    function drawPole(point) {

        const p =
            worldToScreen(
                point.re,
                point.im
            );


        const size = 7;


        ctx.strokeStyle =
            COLORS.pole;

        ctx.lineWidth = 2.5;


        ctx.beginPath();


        ctx.moveTo(
            p.x - size,
            p.y - size
        );

        ctx.lineTo(
            p.x + size,
            p.y + size
        );


        ctx.moveTo(
            p.x + size,
            p.y - size
        );

        ctx.lineTo(
            p.x - size,
            p.y + size
        );


        ctx.stroke();


        drawPointLabel(
            point,
            p,
            COLORS.pole
        );

    }


    /* =====================================================
       DRAW POINT LABEL
       ===================================================== */

    function drawPointLabel(
        point,
        p,
        color
    ) {

        const text =
            formatComplex(point);


        ctx.font =
            "12px Arial";

        ctx.fillStyle =
            color;

        ctx.textAlign =
            "left";


        let offsetX = 11;

        let offsetY = -10;


        /* Avoid going outside top */

        if (
            p.y < 25
        ) {

            offsetY = 20;

        }


        /* Avoid right edge */

        if (
            p.x >
            canvasWidth - 100
        ) {

            ctx.textAlign =
                "right";

            offsetX = -11;

        }


        ctx.fillText(
            text,
            p.x + offsetX,
            p.y + offsetY
        );

    }


    /* =====================================================
       DRAW ALL POINTS
       ===================================================== */

    function drawPoints() {

        zeros.forEach(
            drawZero
        );


        poles.forEach(
            drawPole
        );

    }


    /* =====================================================
       DRAW
       ===================================================== */

    function draw() {

        if (
            canvasWidth <= 0 ||
            canvasHeight <= 0
        ) {

            return;

        }


        /* -----------------------------------------------
           BACKGROUND
           ----------------------------------------------- */

        ctx.clearRect(
            0,
            0,
            canvasWidth,
            canvasHeight
        );


        ctx.fillStyle =
            isDarkMode()
                ? "#111827"
                : "#ffffff";


        ctx.fillRect(
            0,
            0,
            canvasWidth,
            canvasHeight
        );


        /* -----------------------------------------------
           GRID
           ----------------------------------------------- */

        drawGrid();


        /* -----------------------------------------------
           UNIT CIRCLE
           ----------------------------------------------- */

        drawUnitCircle();


        /* -----------------------------------------------
           AXES
           ----------------------------------------------- */

        drawAxes();


        /* -----------------------------------------------
           LABELS
           ----------------------------------------------- */

        drawLabels();


        /* -----------------------------------------------
           POINTS
           ----------------------------------------------- */

        drawPoints();

    }


    /* =====================================================
       FIT VIEW
       ===================================================== */

    function fitView() {

        const allPoints =
            zeros.concat(poles);


        if (allPoints.length === 0) {

            view.centerX = 0;

            view.centerY = 0;

            view.scale = 100;

            draw();

            return;

        }


        let minX = Infinity;

        let maxX = -Infinity;

        let minY = Infinity;

        let maxY = -Infinity;


        allPoints.forEach(
            function (point) {

                minX =
                    Math.min(
                        minX,
                        point.re
                    );

                maxX =
                    Math.max(
                        maxX,
                        point.re
                    );

                minY =
                    Math.min(
                        minY,
                        point.im
                    );

                maxY =
                    Math.max(
                        maxY,
                        point.im
                    );

            }
        );


        /* Include unit circle */

        minX =
            Math.min(
                minX,
                -1
            );

        maxX =
            Math.max(
                maxX,
                1
            );

        minY =
            Math.min(
                minY,
                -1
            );

        maxY =
            Math.max(
                maxY,
                1
            );


        const centerX =
            (minX + maxX) / 2;

        const centerY =
            (minY + maxY) / 2;


        let rangeX =
            maxX - minX;

        let rangeY =
            maxY - minY;


        rangeX =
            Math.max(
                rangeX,
                3
            );

        rangeY =
            Math.max(
                rangeY,
                3
            );


        /* Padding */

        rangeX *= 1.25;

        rangeY *= 1.25;


        view.centerX =
            centerX;

        view.centerY =
            centerY;


        view.scale =
            Math.min(
                canvasWidth / rangeX,
                canvasHeight / rangeY
            );


        view.scale =
            Math.max(
                20,
                Math.min(
                    view.scale,
                    500
                )
            );


        draw();

    }


    /* =====================================================
       ZOOM AT POINT
       ===================================================== */

    function zoomAt(
        factor,
        screenX,
        screenY
    ) {

        const before =
            screenToWorld(
                screenX,
                screenY
            );


        view.scale *= factor;


        view.scale =
            Math.max(
                15,
                Math.min(
                    view.scale,
                    1000
                )
            );


        const after =
            screenToWorld(
                screenX,
                screenY
            );


        view.centerX +=
            before.x -
            after.x;


        view.centerY +=
            before.y -
            after.y;


        draw();

    }


    /* =====================================================
       MOUSE WHEEL ZOOM
       ===================================================== */

    canvas.addEventListener(
        "wheel",
        function (event) {

            event.preventDefault();


            const rect =
                canvas.getBoundingClientRect();


            const x =
                event.clientX -
                rect.left;


            const y =
                event.clientY -
                rect.top;


            const factor =
                event.deltaY < 0
                    ? 1.15
                    : 1 / 1.15;


            zoomAt(
                factor,
                x,
                y
            );

        },
        {
            passive: false
        }
    );


    /* =====================================================
       MOUSE DOWN
       ===================================================== */

    canvas.addEventListener(
        "mousedown",
        function (event) {

            isDragging = true;


            dragStartX =
                event.clientX;

            dragStartY =
                event.clientY;


            dragStartCenterX =
                view.centerX;

            dragStartCenterY =
                view.centerY;


            canvas.style.cursor =
                "grabbing";

        }
    );


    /* =====================================================
       MOUSE MOVE
       ===================================================== */

    window.addEventListener(
        "mousemove",
        function (event) {

            if (!isDragging) {

                return;

            }


            const dx =
                event.clientX -
                dragStartX;


            const dy =
                event.clientY -
                dragStartY;


            view.centerX =
                dragStartCenterX -
                dx /
                view.scale;


            view.centerY =
                dragStartCenterY +
                dy /
                view.scale;


            draw();

        }
    );


    /* =====================================================
       MOUSE UP
       ===================================================== */

    window.addEventListener(
        "mouseup",
        function () {

            isDragging = false;

            canvas.style.cursor =
                "crosshair";

        }
    );


    /* =====================================================
       TOUCH SUPPORT
       ===================================================== */

    let lastTouch = null;


    canvas.addEventListener(
        "touchstart",
        function (event) {

            if (
                event.touches.length !== 1
            ) {

                return;

            }


            const touch =
                event.touches[0];


            lastTouch = {

                x: touch.clientX,

                y: touch.clientY

            };

        },
        {
            passive: true
        }
    );


    canvas.addEventListener(
        "touchmove",
        function (event) {

            if (
                event.touches.length !== 1 ||
                !lastTouch
            ) {

                return;

            }


            event.preventDefault();


            const touch =
                event.touches[0];


            const dx =
                touch.clientX -
                lastTouch.x;


            const dy =
                touch.clientY -
                lastTouch.y;


            view.centerX -=
                dx /
                view.scale;


            view.centerY +=
                dy /
                view.scale;


            lastTouch = {

                x: touch.clientX,

                y: touch.clientY

            };


            draw();

        },
        {
            passive: false
        }
    );


    canvas.addEventListener(
        "touchend",
        function () {

            lastTouch = null;

        }
    );


    /* =====================================================
       PLOT BUTTON
       ===================================================== */

    plotBtn.addEventListener(
        "click",
        function () {

            readInputs();

            fitView();

        }
    );


    /* =====================================================
       FIT BUTTON
       ===================================================== */

    fitBtn.addEventListener(
        "click",
        function () {

            fitView();

        }
    );


    /* =====================================================
       RESET VIEW
       ===================================================== */

    resetViewBtn.addEventListener(
        "click",
        function () {

            view.centerX = 0;

            view.centerY = 0;

            view.scale = 100;

            draw();

        }
    );


    /* =====================================================
       ZOOM IN
       ===================================================== */

    zoomInBtn.addEventListener(
        "click",
        function () {

            zoomAt(
                1.25,
                canvasWidth / 2,
                canvasHeight / 2
            );

        }
    );


    /* =====================================================
       ZOOM OUT
       ===================================================== */

    zoomOutBtn.addEventListener(
        "click",
        function () {

            zoomAt(
                1 / 1.25,
                canvasWidth / 2,
                canvasHeight / 2
            );

        }
    );


    /* =====================================================
       CLEAR
       ===================================================== */

    clearBtn.addEventListener(
        "click",
        function () {

            zerosInput.value = "";

            polesInput.value = "";

            zeros = [];

            poles = [];

            errors = [];


            view.centerX = 0;

            view.centerY = 0;

            view.scale = 100;


            updateStatus();

            updateTable();

            updatePointCount();

            draw();

        }
    );


    /* =====================================================
       PNG EXPORT
       ===================================================== */

    pngBtn.addEventListener(
        "click",
        function () {

            const exportCanvas =
                document.createElement(
                    "canvas"
                );


            const scale = 2;


            exportCanvas.width =
                canvasWidth * scale;

            exportCanvas.height =
                canvasHeight * scale;


            const exportCtx =
                exportCanvas.getContext(
                    "2d"
                );


            exportCtx.drawImage(
                canvas,
                0,
                0,
                exportCanvas.width,
                exportCanvas.height
            );


            const link =
                document.createElement(
                    "a"
                );


            link.download =
                "z-plane-plot.png";


            link.href =
                exportCanvas.toDataURL(
                    "image/png"
                );


            link.click();

        }
    );


    /* =====================================================
       SVG EXPORT
       ===================================================== */

    svgBtn.addEventListener(
        "click",
        function () {

            const svg =
                generateSVG();


            const blob =
                new Blob(
                    [svg],
                    {
                        type:
                            "image/svg+xml"
                    }
                );


            downloadBlob(
                blob,
                "z-plane-plot.svg"
            );

        }
    );


    /* =====================================================
       CSV EXPORT
       ===================================================== */

    csvBtn.addEventListener(
        "click",
        function () {

            const allPoints =
                zeros.concat(poles);


            let csv =
                "Type,Input,Real,Imaginary,Magnitude,Angle (degrees)\n";


            allPoints.forEach(
                function (point) {

                    const magnitude =
                        Math.sqrt(
                            point.re *
                            point.re +
                            point.im *
                            point.im
                        );


                    const angle =
                        Math.atan2(
                            point.im,
                            point.re
                        ) *
                        180 /
                        Math.PI;


                    csv +=

                        '"' +
                        point.type +
                        '",' +

                        '"' +
                        point.notation
                            .replace(
                                /"/g,
                                '""'
                            ) +
                        '",' +

                        formatNumber(
                            point.re
                        ) +
                        "," +

                        formatNumber(
                            point.im
                        ) +
                        "," +

                        formatNumber(
                            magnitude
                        ) +
                        "," +

                        formatNumber(
                            angle
                        ) +

                        "\n";

                }
            );


            const blob =
                new Blob(
                    [csv],
                    {
                        type:
                            "text/csv;charset=utf-8;"
                    }
                );


            downloadBlob(
                blob,
                "z-plane-data.csv"
            );

        }
    );


    /* =====================================================
       DOWNLOAD BLOB
       ===================================================== */

    function downloadBlob(
        blob,
        filename
    ) {

        const url =
            URL.createObjectURL(
                blob
            );


        const link =
            document.createElement(
                "a"
            );


        link.href = url;

        link.download =
            filename;


        document.body.appendChild(
            link
        );


        link.click();


        link.remove();


        setTimeout(
            function () {

                URL.revokeObjectURL(
                    url
                );

            },
            1000
        );

    }


    /* =====================================================
       SVG ESCAPE
       ===================================================== */

    function escapeSvg(text) {

        return String(text)
            .replace(
                /&/g,
                "&amp;"
            )
            .replace(
                /</g,
                "&lt;"
            )
            .replace(
                />/g,
                "&gt;"
            )
            .replace(
                /"/g,
                "&quot;"
            )
            .replace(
                /'/g,
                "&apos;"
            );

    }


    /* =====================================================
       GENERATE SVG
       ===================================================== */

    function generateSVG() {

        const width =
            Math.round(
                canvasWidth
            );


        const height =
            Math.round(
                canvasHeight
            );


        const dark =
            isDarkMode();


        const background =
            dark
                ? "#111827"
                : "#ffffff";


        const grid =
            dark
                ? "#374151"
                : "#e5e7eb";


        const text =
            dark
                ? "#e5e7eb"
                : "#374151";


        let svg =

            '<?xml version="1.0" encoding="UTF-8"?>' +

            '<svg xmlns="http://www.w3.org/2000/svg" ' +

            'width="' +
            width +
            '" ' +

            'height="' +
            height +
            '" ' +

            'viewBox="0 0 ' +
            width +
            " " +
            height +
            '">' +

            '<rect width="100%" height="100%" fill="' +
            background +
            '"/>' ;


        /* -----------------------------------------------
           GRID
           ----------------------------------------------- */

        const step =
            niceGridStep();


        const topLeft =
            screenToWorld(
                0,
                0
            );


        const bottomRight =
            screenToWorld(
                width,
                height
            );


        const startX =
            Math.floor(
                topLeft.x / step
            ) *
            step;


        const endX =
            Math.ceil(
                bottomRight.x / step
            ) *
            step;


        const startY =
            Math.floor(
                bottomRight.y / step
            ) *
            step;


        const endY =
            Math.ceil(
                topLeft.y / step
            ) *
            step;


        for (
            let x = startX;
            x <= endX;
            x += step
        ) {

            const p =
                worldToScreen(
                    x,
                    0
                );


            svg +=

                '<line x1="' +
                p.x +
                '" y1="0" x2="' +
                p.x +
                '" y2="' +
                height +
                '" stroke="' +
                grid +
                '" stroke-width="1"/>';

        }


        for (
            let y = startY;
            y <= endY;
            y += step
        ) {

            const p =
                worldToScreen(
                    0,
                    y
                );


            svg +=

                '<line x1="0" y1="' +
                p.y +
                '" x2="' +
                width +
                '" y2="' +
                p.y +
                '" stroke="' +
                grid +
                '" stroke-width="1"/>';

        }


        /* -----------------------------------------------
           AXES
           ----------------------------------------------- */

        const origin =
            worldToScreen(
                0,
                0
            );


        svg +=

            '<line x1="0" y1="' +
            origin.y +
            '" x2="' +
            width +
            '" y2="' +
            origin.y +
            '" stroke="#6b7280" stroke-width="1.5"/>' +

            '<line x1="' +
            origin.x +
            '" y1="0" x2="' +
            origin.x +
            '" y2="' +
            height +
            '" stroke="#6b7280" stroke-width="1.5"/>';


        /* -----------------------------------------------
           UNIT CIRCLE
           ----------------------------------------------- */

        svg +=

            '<circle cx="' +
            origin.x +
            '" cy="' +
            origin.y +
            '" r="' +
            view.scale +
            '" fill="none" stroke="#6b7280" stroke-width="2" stroke-dasharray="6 5"/>';


        /* -----------------------------------------------
           LABELS
           ----------------------------------------------- */

        svg +=

            '<text x="' +
            (width - 45) +
            '" y="' +
            (origin.y - 10) +
            '" font-family="Arial" font-size="14" font-weight="bold" fill="' +
            text +
            '">Re{z}</text>' +

            '<text x="' +
            (origin.x + 10) +
            '" y="18" font-family="Arial" font-size="14" font-weight="bold" fill="' +
            text +
            '">Im{z}</text>';


        /* -----------------------------------------------
           POINTS
           ----------------------------------------------- */

        zeros.forEach(
            function (point) {

                const p =
                    worldToScreen(
                        point.re,
                        point.im
                    );


                svg +=

                    '<circle cx="' +
                    p.x +
                    '" cy="' +
                    p.y +
                    '" r="7" fill="' +
                    background +
                    '" stroke="#2563eb" stroke-width="2.5"/>' +

                    '<text x="' +
                    (p.x + 11) +
                    '" y="' +
                    (p.y - 10) +
                    '" font-family="Arial" font-size="12" fill="#2563eb">' +
                    escapeSvg(
                        formatComplex(point)
                    ) +
                    '</text>';

            }
        );


        poles.forEach(
            function (point) {

                const p =
                    worldToScreen(
                        point.re,
                        point.im
                    );


                const size = 7;


                svg +=

                    '<line x1="' +
                    (p.x - size) +
                    '" y1="' +
                    (p.y - size) +
                    '" x2="' +
                    (p.x + size) +
                    '" y2="' +
                    (p.y + size) +
                    '" stroke="#dc2626" stroke-width="2.5"/>' +

                    '<line x1="' +
                    (p.x + size) +
                    '" y1="' +
                    (p.y - size) +
                    '" x2="' +
                    (p.x - size) +
                    '" y2="' +
                    (p.y + size) +
                    '" stroke="#dc2626" stroke-width="2.5"/>' +

                    '<text x="' +
                    (p.x + 11) +
                    '" y="' +
                    (p.y - 10) +
                    '" font-family="Arial" font-size="12" fill="#dc2626">' +
                    escapeSvg(
                        formatComplex(point)
                    ) +
                    '</text>';

            }
        );


        svg +=
            "</svg>";


        return svg;

    }


    /* =====================================================
       KEYBOARD SHORTCUTS
       ===================================================== */

    document.addEventListener(
        "keydown",
        function (event) {

            /* Ctrl + Enter = Plot */

            if (
                event.ctrlKey &&
                event.key === "Enter"
            ) {

                event.preventDefault();

                readInputs();

                fitView();

            }


            /* + = Zoom in */

            if (
                event.key === "+"
            ) {

                zoomAt(
                    1.15,
                    canvasWidth / 2,
                    canvasHeight / 2
                );

            }


            /* - = Zoom out */

            if (
                event.key === "-"
            ) {

                zoomAt(
                    1 / 1.15,
                    canvasWidth / 2,
                    canvasHeight / 2
                );

            }

        }
    );


    /* =====================================================
       OBSERVE THEME CHANGES
       ===================================================== */

    const themeObserver =
        new MutationObserver(
            function () {

                draw();

            }
        );


    themeObserver.observe(
        document.documentElement,
        {
            attributes: true,
            attributeFilter: [
                "data-theme"
            ]
        }
    );


    /* =====================================================
       WINDOW RESIZE
       ===================================================== */

    window.addEventListener(
        "resize",
        function () {

            resizeCanvas();

        }
    );


    /* =====================================================
       INITIALIZATION
       ===================================================== */

    zerosInput.value =
        "0\n0.5+0.5j\n0.5-0.5j";


    polesInput.value =
        "0.8+0.3j\n0.8-0.3j";


    resizeCanvas();

    readInputs();

    fitView();


})();

</script>


<!-- =========================================================
     GLOBAL SCRIPTS
     ========================================================= -->

<?php include "globals/scripts.php"; ?>


</body>

</html>