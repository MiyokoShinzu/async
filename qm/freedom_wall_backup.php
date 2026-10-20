<?php
/* =========================================================
   STUDENT FREEDOM WALL
   ETS-Async Learning Portal

   FEATURES
   ---------------------------------------------------------
   - Anonymous student posts
   - Maximum 25 characters
   - Maximum 500 posts per wall
   - Maximum 20 posts per student per wall
   - Random readable fonts
   - Random font size: 30px - 400px
   - Random text colors
   - Random glow
   - Random text stroke
   - Random rotation
   - Random opacity
   - Saved visual properties
   - Live refresh every 1 second
   - Mouse drag / pan
   - One-finger touch pan
   - Two-finger pinch zoom
   - Manual + / - zoom
   - Fit entire wall
   - No mouse-wheel zoom
   - Full canvas PNG export
   - Responsive
   - Dark mode
   - NO BACKGROUND GRID
========================================================= */


/* =========================================================
   SESSION
========================================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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
   DATABASE
========================================================= */

require_once "../src/connection.php";

global $mysqli;


/* =========================================================
   SESSION USER
========================================================= */

$user = $_SESSION["user"];

$studentId =
    $user["student_id"] ?? "";

$department =
    $user["department"] ?? "";

$yearSection =
    $user["year_section"] ?? "";


/* =========================================================
   CONFIGURATION
========================================================= */

$MAX_POSTS_PER_WALL = 500;

$MAX_POSTS_PER_STUDENT = 20;

$MAX_MESSAGE_LENGTH = 25;

$CANVAS_WIDTH = 3000;

$CANVAS_HEIGHT = 2000;


/* =========================================================
   JSON RESPONSE
========================================================= */

function jsonResponse(
    $success,
    $message = "",
    $data = []
) {
    header(
        "Content-Type: application/json; charset=utf-8"
    );

    echo json_encode(
        [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/* =========================================================
   GET WALL ID
========================================================= */

$wallId =
    isset($_GET["wall_id"])
    ? intval($_GET["wall_id"])
    : 0;


if ($wallId <= 0) {

    header(
        "Location: freedom_walls.php"
    );

    exit;
}


/* =========================================================
   GET AUTHORIZED WALL
========================================================= */

function getAuthorizedWall(
    $mysqli,
    $wallId,
    $department,
    $yearSection
) {

    $stmt =
        $mysqli->prepare(
            "
            SELECT
                id,
                title,
                description,
                department,
                year_section,
                status,
                created_at
            FROM freedom_walls
            WHERE
                id = ?
                AND status = 'active'
                AND (
                    department IS NULL
                    OR department = ''
                    OR department = ?
                )
                AND (
                    year_section IS NULL
                    OR year_section = ''
                    OR year_section = ?
                )
            LIMIT 1
            "
        );


    if (!$stmt) {
        return null;
    }


    $stmt->bind_param(
        "iss",
        $wallId,
        $department,
        $yearSection
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    $wall =
        $result->fetch_assoc();


    $stmt->close();


    return $wall ?: null;
}


/* =========================================================
   AUTHORIZE WALL
========================================================= */

$wall =
    getAuthorizedWall(
        $mysqli,
        $wallId,
        $department,
        $yearSection
    );


/* =========================================================
   WALL NOT FOUND
========================================================= */

if (!$wall) {

    if (
        isset($_GET["action"]) &&
        $_GET["action"] !== ""
    ) {

        jsonResponse(
            false,
            "This Freedom Wall is not available."
        );
    }


    header(
        "Location: freedom_walls.php"
    );

    exit;
}


/* =========================================================
   AJAX ACTION
========================================================= */

$action =
    $_GET["action"] ?? "";


/* =========================================================
   LOAD POSTS
========================================================= */

if ($action === "load") {

    $stmt =
        $mysqli->prepare(
            "
            SELECT
                id,
                wall_id,
                message,
                x_position,
                y_position,
                font_family,
                font_size,
                font_color,
                text_stroke,
                text_stroke_width,
                text_glow,
                text_glow_blur,
                rotation,
                opacity
            FROM freedom_wall_posts
            WHERE wall_id = ?
            ORDER BY created_at ASC, id ASC
            "
        );


    if (!$stmt) {

        jsonResponse(
            false,
            "Unable to load Freedom Wall posts."
        );
    }


    $stmt->bind_param(
        "i",
        $wallId
    );


    $stmt->execute();


    $result =
        $stmt->get_result();


    $posts = [];


    while (
        $row =
        $result->fetch_assoc()
    ) {

        $posts[] = [

            "id" =>
            (int)$row["id"],

            "wall_id" =>
            (int)$row["wall_id"],

            "message" =>
            $row["message"],

            "x_position" =>
            (float)$row["x_position"],

            "y_position" =>
            (float)$row["y_position"],

            "font_family" =>
            $row["font_family"],

            "font_size" =>
            (int)$row["font_size"],

            "font_color" =>
            $row["font_color"],

            "text_stroke" =>
            $row["text_stroke"],

            "text_stroke_width" =>
            (float)$row["text_stroke_width"],

            "text_glow" =>
            $row["text_glow"],

            "text_glow_blur" =>
            (int)$row["text_glow_blur"],

            "rotation" =>
            (float)$row["rotation"],

            "opacity" =>
            (float)$row["opacity"]
        ];
    }


    $stmt->close();


    /* =====================================================
       STUDENT POST COUNT
    ====================================================== */

    $countStmt =
        $mysqli->prepare(
            "
            SELECT COUNT(*) AS total
            FROM freedom_wall_posts
            WHERE
                wall_id = ?
                AND student_id = ?
            "
        );


    $studentPostCount = 0;


    if ($countStmt) {

        $countStmt->bind_param(
            "is",
            $wallId,
            $studentId
        );


        $countStmt->execute();


        $countResult =
            $countStmt->get_result();


        $countRow =
            $countResult->fetch_assoc();


        $studentPostCount =
            (int)(
                $countRow["total"] ?? 0
            );


        $countStmt->close();
    }


    jsonResponse(
        true,
        "",
        [
            "posts" => $posts,

            "post_count" =>
            count($posts),

            "student_post_count" =>
            $studentPostCount,

            "max_posts" =>
            $MAX_POSTS_PER_WALL,

            "max_student_posts" =>
            $MAX_POSTS_PER_STUDENT,

            "max_message_length" =>
            $MAX_MESSAGE_LENGTH
        ]
    );
}


/* =========================================================
   CREATE POST
========================================================= */

if ($action === "create") {

    if (
        $_SERVER["REQUEST_METHOD"] !== "POST"
    ) {

        jsonResponse(
            false,
            "Invalid request."
        );
    }


    /* =====================================================
       MESSAGE
    ====================================================== */

    $message =
        trim(
            $_POST["message"] ?? ""
        );


    if ($message === "") {

        jsonResponse(
            false,
            "Please enter a message."
        );
    }


    /* =====================================================
       CHARACTER LIMIT
    ====================================================== */

    if (
        mb_strlen(
            $message,
            "UTF-8"
        ) > $MAX_MESSAGE_LENGTH
    ) {

        jsonResponse(
            false,
            "Your message must not exceed "
                . $MAX_MESSAGE_LENGTH
                . " characters."
        );
    }


    /* =====================================================
       WALL POST COUNT
    ====================================================== */

    $wallCountStmt =
        $mysqli->prepare(
            "
            SELECT COUNT(*) AS total
            FROM freedom_wall_posts
            WHERE wall_id = ?
            "
        );


    if (!$wallCountStmt) {

        jsonResponse(
            false,
            "Unable to check wall capacity."
        );
    }


    $wallCountStmt->bind_param(
        "i",
        $wallId
    );


    $wallCountStmt->execute();


    $wallCountResult =
        $wallCountStmt->get_result();


    $wallCountRow =
        $wallCountResult->fetch_assoc();


    $wallPostCount =
        (int)(
            $wallCountRow["total"] ?? 0
        );


    $wallCountStmt->close();


    if (
        $wallPostCount >=
        $MAX_POSTS_PER_WALL
    ) {

        jsonResponse(
            false,
            "This Freedom Wall has reached its maximum of "
                . $MAX_POSTS_PER_WALL
                . " posts."
        );
    }


    /* =====================================================
       STUDENT POST COUNT
    ====================================================== */

    $studentCountStmt =
        $mysqli->prepare(
            "
            SELECT COUNT(*) AS total
            FROM freedom_wall_posts
            WHERE
                wall_id = ?
                AND student_id = ?
            "
        );


    if (!$studentCountStmt) {

        jsonResponse(
            false,
            "Unable to check your post limit."
        );
    }


    $studentCountStmt->bind_param(
        "is",
        $wallId,
        $studentId
    );


    $studentCountStmt->execute();


    $studentCountResult =
        $studentCountStmt->get_result();


    $studentCountRow =
        $studentCountResult->fetch_assoc();


    $studentPostCount =
        (int)(
            $studentCountRow["total"] ?? 0
        );


    $studentCountStmt->close();


    if (
        $studentPostCount >=
        $MAX_POSTS_PER_STUDENT
    ) {

        jsonResponse(
            false,
            "You have reached your maximum of "
                . $MAX_POSTS_PER_STUDENT
                . " posts on this Freedom Wall."
        );
    }


    /* =====================================================
       RANDOM POSITION
    ====================================================== */

    $xPosition =
        random_int(
            150,
            $CANVAS_WIDTH - 150
        );


    $yPosition =
        random_int(
            150,
            $CANVAS_HEIGHT - 150
        );


    /* =====================================================
       RANDOM READABLE FONT
    ====================================================== */

    $fontFamilies = [

        "Arial",

        "Verdana",

        "Tahoma",

        "Trebuchet MS",

        "Georgia",

        "Courier New"

    ];


    $fontFamily =
        $fontFamilies[array_rand(
                $fontFamilies
            )];


    /* =====================================================
       RANDOM FONT SIZE

       RANGE:
       30px - 400px

       Weighted distribution:
       - Small: 30-59
       - Medium: 60-119
       - Large: 120-219
       - Very large: 220-400

       Medium/large sizes are more common.
    ====================================================== */

    $fontSizePool = [];


    /* Small */
    for (
        $i = 30;
        $i <= 59;
        $i++
    ) {

        $fontSizePool[] =
            $i;
    }


    /* Medium - weighted */
    for (
        $i = 60;
        $i <= 119;
        $i++
    ) {

        $fontSizePool[] =
            $i;

        $fontSizePool[] =
            $i;
    }


    /* Large - weighted */
    for (
        $i = 120;
        $i <= 219;
        $i++
    ) {

        $fontSizePool[] =
            $i;

        $fontSizePool[] =
            $i;
    }


    /* Very large */
    for (
        $i = 220;
        $i <= 400;
        $i++
    ) {

        $fontSizePool[] =
            $i;
    }


    $fontSize =
        $fontSizePool[array_rand(
                $fontSizePool
            )];


    /* =====================================================
       RANDOM TEXT COLOR
    ====================================================== */

    $fontColors = [

        "#111827",

        "#1D4ED8",

        "#4338CA",

        "#7C3AED",

        "#BE123C",

        "#047857",

        "#0F766E",

        "#B45309",

        "#9D174D",

        "#374151",

        "#0369A1",

        "#6D28D9",

        "#C2410C",

        "#15803D"

    ];


    $fontColor =
        $fontColors[array_rand(
                $fontColors
            )];


    /* =====================================================
       RANDOM TEXT STROKE
    ====================================================== */

    $strokeOptions = [

        [
            "type" => "none",
            "width" => 0
        ],

        [
            "type" => "thin",
            "width" => 1
        ],

        [
            "type" => "thin",
            "width" => 1.5
        ],

        [
            "type" => "medium",
            "width" => 2
        ],

        [
            "type" => "medium",
            "width" => 3
        ],

        [
            "type" => "medium",
            "width" => 4
        ]

    ];


    $stroke =
        $strokeOptions[array_rand(
                $strokeOptions
            )];


    $textStroke =
        $stroke["type"];


    $textStrokeWidth =
        $stroke["width"];


    /* =====================================================
       RANDOM GLOW
    ====================================================== */

    $glowOptions = [

        [
            "type" => "none",
            "blur" => 0
        ],

        [
            "type" => "subtle",
            "blur" => 4
        ],

        [
            "type" => "subtle",
            "blur" => 6
        ],

        [
            "type" => "medium",
            "blur" => 9
        ],

        [
            "type" => "medium",
            "blur" => 12
        ],

        [
            "type" => "strong",
            "blur" => 16
        ],

        [
            "type" => "strong",
            "blur" => 22
        ]

    ];


    $glow =
        $glowOptions[array_rand(
                $glowOptions
            )];


    $textGlow =
        $glow["type"];


    $textGlowBlur =
        $glow["blur"];


    /* =====================================================
       RANDOM ROTATION
    ====================================================== */

    $rotation =
        random_int(
            -120,
            120
        ) / 10;


    /* =====================================================
       RANDOM OPACITY
    ====================================================== */

    $opacity =
        random_int(
            94,
            100
        ) / 100;


    /* =====================================================
       INSERT POST
    ====================================================== */

    $insertStmt =
        $mysqli->prepare(
            "
            INSERT INTO freedom_wall_posts
            (
                wall_id,
                student_id,
                message,
                x_position,
                y_position,
                font_family,
                font_size,
                font_color,
                text_stroke,
                text_stroke_width,
                text_glow,
                text_glow_blur,
                rotation,
                opacity
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
            "
        );


    if (!$insertStmt) {

        jsonResponse(
            false,
            "Unable to prepare your post."
        );
    }


    /* =====================================================
       BIND VALUES

       i = integer
       s = string
       d = decimal/double
    ====================================================== */

    $insertStmt->bind_param(
        "issddsisdsisdd",
        $wallId,
        $studentId,
        $message,
        $xPosition,
        $yPosition,
        $fontFamily,
        $fontSize,
        $fontColor,
        $textStroke,
        $textStrokeWidth,
        $textGlow,
        $textGlowBlur,
        $rotation,
        $opacity
    );


    /* =====================================================
       EXECUTE
    ====================================================== */

    if (
        !$insertStmt->execute()
    ) {

        $insertStmt->close();

        jsonResponse(
            false,
            "Unable to save your post."
        );
    }


    $newPostId =
        $insertStmt->insert_id;


    $insertStmt->close();


    /* =====================================================
       SUCCESS
    ====================================================== */

    jsonResponse(
        true,
        "Your message has been posted!",
        [
            "id" =>
            $newPostId
        ]
    );
}


/* =========================================================
   GLOBAL HEAD
========================================================= */

include "globals/head.php";

?>

<style>
    /* =========================================================
   FREEDOM WALL PAGE
========================================================= */

    .freedom-wall-page,
    .freedom-wall-page * {
        box-sizing: border-box;
    }


    .freedom-wall-page {

        width: 100%;

        max-width: 1400px;

        margin-left: auto;

        margin-right: auto;

        padding:
            24px clamp(16px, 3vw, 40px) 40px;
    }


    /* =========================================================
   HEADER
========================================================= */

    .freedom-wall-header {

        display: flex;

        justify-content: space-between;

        align-items: flex-start;

        gap: 20px;

        margin-bottom: 20px;
    }


    .freedom-wall-title {

        margin: 0;

        color:
            var(--academic-blue);

        font-size:
            clamp(22px, 3vw, 32px);

        font-weight: 700;
    }


    .freedom-wall-description {

        margin:
            6px 0 0;

        color:
            var(--text-secondary);

        font-size: 14px;

        line-height: 1.6;

        max-width: 850px;
    }


    /* =========================================================
   WALL INFORMATION
========================================================= */

    .wall-information {

        display: flex;

        flex-wrap: wrap;

        gap: 8px;

        margin-top: 12px;
    }


    .wall-info-badge {

        display: inline-flex;

        align-items: center;

        gap: 6px;

        padding:
            6px 10px;

        border-radius: 20px;

        background:
            var(--surface-secondary);

        border:
            1px solid var(--border-color);

        color:
            var(--text-secondary);

        font-size: 12px;
    }


    .wall-info-badge i {

        color:
            var(--academic-blue);
    }


    /* =========================================================
   TOOLBAR
========================================================= */

    .freedom-wall-toolbar {

        display: flex;

        flex-wrap: wrap;

        align-items: center;

        gap: 8px;

        padding: 10px;

        margin-bottom: 12px;

        border:
            1px solid var(--border-color);

        border-radius: 12px;

        background:
            var(--surface-color);

        box-shadow:
            0 4px 15px var(--shadow-color);
    }


    /* =========================================================
   TOOL BUTTON
========================================================= */

    .wall-tool-button {

        width: 40px;

        height: 40px;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        border:
            1px solid var(--border-color);

        border-radius: 8px;

        background:
            var(--surface-color);

        color:
            var(--text-color);

        cursor: pointer;

        transition:
            background-color .2s ease,
            color .2s ease,
            border-color .2s ease,
            transform .15s ease;
    }


    .wall-tool-button:hover {

        background:
            var(--academic-blue-light);

        color:
            var(--academic-blue);

        border-color:
            var(--academic-blue);
    }


    .wall-tool-button:active {

        transform:
            scale(.94);
    }


    /* =========================================================
   EXPORT BUTTON
========================================================= */

    .wall-export-button {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 7px;

        min-height: 40px;

        padding:
            0 14px;

        border: none;

        border-radius: 8px;

        background:
            var(--academic-blue);

        color: #FFFFFF;

        font-size: 13px;

        font-weight: 600;

        cursor: pointer;

        transition:
            background-color .2s ease,
            transform .15s ease;
    }


    .wall-export-button:hover {

        background:
            var(--academic-blue-dark);
    }


    .wall-export-button:active {

        transform:
            scale(.97);
    }


    /* =========================================================
   COUNT
========================================================= */

    .wall-count {

        margin-left: auto;

        color:
            var(--text-secondary);

        font-size: 13px;

        white-space: nowrap;
    }


    /* =========================================================
   WALL VIEWPORT
========================================================= */

    .freedom-wall-viewport {

        position: relative;

        width: 100%;

        height:
            min(72vh,
                780px);

        min-height: 450px;

        overflow: hidden;

        border:
            1px solid var(--border-color);

        border-radius: 14px;

        background:
            var(--surface-color);

        box-shadow:
            0 6px 25px var(--shadow-color);

        /*
        IMPORTANT:
        This allows touch gestures.
        Pinch zoom is handled manually
        in JavaScript.
    */

        touch-action: none;

        cursor:
            grab;

        user-select: none;

        -webkit-user-select: none;

        -webkit-touch-callout: none;
    }


    /* =========================================================
   CANVAS WRAPPER
========================================================= */

    .freedom-wall-canvas-wrapper {

        position: absolute;

        left: 0;

        top: 0;

        transform-origin:
            0 0;

        will-change:
            transform;
    }


    /* =========================================================
   CANVAS

   NO GRID
========================================================= */

    #freedomWallCanvas {

        display: block;

        width:
            <?= $CANVAS_WIDTH ?>px;

        height:
            <?= $CANVAS_HEIGHT ?>px;

        background:
            var(--surface-color);

        /*
        Deliberately no grid.
    */
    }


    /* =========================================================
   PAN HINT
========================================================= */

    .wall-pan-hint {

        position: absolute;

        left: 50%;

        bottom: 14px;

        transform:
            translateX(-50%);

        padding:
            7px 12px;

        border-radius: 20px;

        background:
            rgba(17,
                24,
                39,
                .78);

        color:
            #FFFFFF;

        font-size: 11px;

        pointer-events: none;

        opacity: .75;

        white-space: nowrap;
    }


    /* =========================================================
   COMPOSER
========================================================= */

    .freedom-wall-composer {

        margin-top: 16px;

        padding: 16px;

        border:
            1px solid var(--border-color);

        border-radius: 12px;

        background:
            var(--surface-color);

        box-shadow:
            0 4px 15px var(--shadow-color);
    }


    .composer-row {

        display: flex;

        gap: 10px;

        align-items: flex-end;
    }


    .composer-input-wrapper {

        flex: 1;

        min-width: 0;
    }


    .composer-label {

        display: block;

        margin-bottom: 6px;

        color:
            var(--text-color);

        font-size: 13px;

        font-weight: 600;
    }


    #messageInput {

        width: 100%;

        min-height: 48px;

        resize: vertical;

        padding:
            12px 14px;

        border:
            1px solid var(--border-color);

        border-radius: 9px;

        outline: none;

        background:
            var(--input-bg);

        color:
            var(--text-color);

        font-family:
            inherit;

        font-size: 14px;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;

        user-select: text;

        -webkit-user-select: text;
    }


    #messageInput:focus {

        border-color:
            var(--academic-blue);

        box-shadow:
            0 0 0 3px rgba(59,
                130,
                246,
                .12);
    }


    #messageInput::placeholder {

        color:
            var(--text-secondary);
    }


    /* =========================================================
   POST BUTTON
========================================================= */

    .post-button {

        height: 48px;

        padding:
            0 18px;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 7px;

        border: none;

        border-radius: 9px;

        background:
            var(--academic-blue);

        color:
            #FFFFFF;

        font-size: 14px;

        font-weight: 600;

        cursor: pointer;

        white-space: nowrap;

        transition:
            background-color .2s ease,
            transform .15s ease;
    }


    .post-button:hover {

        background:
            var(--academic-blue-dark);
    }


    .post-button:active {

        transform:
            scale(.97);
    }


    .post-button:disabled {

        opacity: .6;

        cursor:
            not-allowed;
    }


    /* =========================================================
   CHARACTER COUNTER
========================================================= */

    .composer-footer {

        display: flex;

        justify-content: space-between;

        align-items: center;

        gap: 10px;

        margin-top: 7px;

        color:
            var(--text-secondary);

        font-size: 11px;
    }


    /* =========================================================
   TOAST
========================================================= */

    .freedom-wall-toast {

        position: fixed;

        right: 20px;

        bottom: 20px;

        z-index: 9999;

        max-width: 340px;

        padding:
            12px 16px;

        border-radius: 10px;

        background:
            #111827;

        color:
            #FFFFFF;

        font-size: 13px;

        box-shadow:
            0 8px 30px rgba(0,
                0,
                0,
                .2);

        opacity: 0;

        transform:
            translateY(15px);

        pointer-events: none;

        transition:
            opacity .25s ease,
            transform .25s ease;
    }


    .freedom-wall-toast.show {

        opacity: 1;

        transform:
            translateY(0);
    }


    .freedom-wall-toast.success {

        background:
            #166534;
    }


    .freedom-wall-toast.error {

        background:
            #991B1B;
    }


    /* =========================================================
   EMPTY WALL
========================================================= */

    .wall-empty-message {

        position: absolute;

        left: 50%;

        top: 50%;

        transform:
            translate(-50%,
                -50%);

        text-align: center;

        color:
            var(--text-secondary);

        pointer-events: none;
    }


    .wall-empty-message i {

        display: block;

        margin-bottom: 10px;

        font-size: 38px;

        opacity: .5;
    }


    /* =========================================================
   RESPONSIVE
========================================================= */

    @media (max-width: 768px) {

        .freedom-wall-page {

            padding:
                18px 14px 30px;
        }


        .freedom-wall-header {

            flex-direction: column;
        }


        .freedom-wall-viewport {

            height: 65vh;

            min-height: 400px;

            border-radius: 10px;
        }


        .wall-count {

            margin-left: 0;
        }


        .composer-row {

            flex-direction: column;

            align-items: stretch;
        }


        .post-button {

            width: 100%;
        }


        .wall-pan-hint {

            bottom: 10px;

            font-size: 10px;
        }
    }


    @media (max-width: 480px) {

        .freedom-wall-page {

            padding:
                14px 10px 25px;
        }


        .freedom-wall-title {

            font-size: 22px;
        }


        .freedom-wall-viewport {

            height: 62vh;

            min-height: 360px;
        }


        .freedom-wall-toolbar {

            padding: 8px;
        }


        .wall-tool-button {

            width: 38px;

            height: 38px;
        }


        .wall-export-button {

            flex: 1;
        }
    }


    /* =========================================================
   REDUCED MOTION
========================================================= */

    @media (prefers-reduced-motion: reduce) {

        .wall-tool-button,
        .wall-export-button,
        .post-button,
        #messageInput,
        .freedom-wall-toast {

            transition: none !important;
        }
    }
</style>


<?php
/* =========================================================
   SIDEBAR
========================================================= */

include "globals/sidebar.php";
?>


<div class="main-content">


    <!-- =====================================================
         TOPBAR
    ====================================================== -->

    <?php
    include "globals/topbar.php";
    ?>


    <!-- =====================================================
         FREEDOM WALL PAGE
    ====================================================== -->

    <main class="freedom-wall-page">


        <!-- =================================================
             HEADER
        ================================================== -->

        <section class="freedom-wall-header">

            <div>

                <h1 class="freedom-wall-title">

                    <i class="bi bi-chat-square-heart-fill"></i>

                    <?= htmlspecialchars(
                        $wall["title"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </h1>


                <?php if (
                    !empty($wall["description"])
                ): ?>

                    <p class="freedom-wall-description">

                        <?= nl2br(
                            htmlspecialchars(
                                $wall["description"],
                                ENT_QUOTES,
                                "UTF-8"
                            )
                        ) ?>

                    </p>

                <?php endif; ?>


                <div class="wall-information">

                    <span class="wall-info-badge">

                        <i class="bi bi-people-fill"></i>

                        Anonymous posts

                    </span>


                    <span class="wall-info-badge">

                        <i class="bi bi-chat-left-text-fill"></i>

                        Maximum
                        <?= $MAX_POSTS_PER_WALL ?>
                        posts

                    </span>


                    <span class="wall-info-badge">

                        <i class="bi bi-person-fill"></i>

                        <?= $MAX_POSTS_PER_STUDENT ?>
                        posts per student

                    </span>


                    <span class="wall-info-badge">

                        <i class="bi bi-arrows-move"></i>

                        Drag to explore

                    </span>

                </div>

            </div>

        </section>


        <!-- =================================================
             TOOLBAR
        ================================================== -->

        <div class="freedom-wall-toolbar">


            <button
                type="button"
                class="wall-tool-button"
                id="zoomOutButton"
                title="Zoom out"
                aria-label="Zoom out">

                <i class="bi bi-dash-lg"></i>

            </button>


            <button
                type="button"
                class="wall-tool-button"
                id="zoomInButton"
                title="Zoom in"
                aria-label="Zoom in">

                <i class="bi bi-plus-lg"></i>

            </button>


            <button
                type="button"
                class="wall-tool-button"
                id="fitWallButton"
                title="Fit entire wall"
                aria-label="Fit entire wall">

                <i class="bi bi-arrows-fullscreen"></i>

            </button>


            <button
                type="button"
                class="wall-tool-button"
                id="refreshWallButton"
                title="Refresh posts"
                aria-label="Refresh posts">

                <i class="bi bi-arrow-clockwise"></i>

            </button>


            <button
                type="button"
                class="wall-export-button"
                id="exportWallButton">

                <i class="bi bi-download"></i>

                Export Wall

            </button>


            <span
                class="wall-count"
                id="wallCount">

                Loading...

            </span>

        </div>


        <!-- =================================================
             WALL VIEWPORT
        ================================================== -->

        <section
            class="freedom-wall-viewport"
            id="freedomWallViewport">


            <div
                class="freedom-wall-canvas-wrapper"
                id="freedomWallCanvasWrapper">


                <canvas
                    id="freedomWallCanvas"
                    width="<?= $CANVAS_WIDTH ?>"
                    height="<?= $CANVAS_HEIGHT ?>">
                </canvas>


            </div>


            <div
                class="wall-empty-message"
                id="wallEmptyMessage"
                style="display:none;">

                <i class="bi bi-chat-square-text"></i>

                <div>
                    No messages yet.
                </div>

                <small>
                    Be the first to post!
                </small>

            </div>


            <div class="wall-pan-hint">

                One finger / mouse = move
                &nbsp;•&nbsp;
                Two fingers = zoom

            </div>


        </section>


        <!-- =================================================
             COMPOSER
        ================================================== -->

        <section
            class="freedom-wall-composer">


            <form
                id="freedomWallForm"
                autocomplete="off">


                <div class="composer-row">


                    <div class="composer-input-wrapper">

                        <label
                            for="messageInput"
                            class="composer-label">

                            Share something anonymously

                        </label>


                        <textarea
                            id="messageInput"
                            name="message"
                            maxlength="<?= $MAX_MESSAGE_LENGTH ?>"
                            rows="1"
                            placeholder="Write something..."
                            required></textarea>


                        <div class="composer-footer">

                            <span>

                                Maximum
                                <?= $MAX_MESSAGE_LENGTH ?>
                                characters

                            </span>


                            <span
                                id="characterCounter">

                                0 /
                                <?= $MAX_MESSAGE_LENGTH ?>

                            </span>

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="post-button"
                        id="postButton">

                        <i class="bi bi-send-fill"></i>

                        Post

                    </button>


                </div>

            </form>

        </section>


    </main>

</div>


<!-- =========================================================
     TOAST
========================================================= -->

<div
    class="freedom-wall-toast"
    id="freedomWallToast">
</div>


<script>
    /* =========================================================
   FREEDOM WALL JAVASCRIPT
========================================================= */

    (function() {

        "use strict";


        /* =====================================================
           CONSTANTS
        ====================================================== */

        const WALL_ID =
            <?= (int)$wallId ?>;


        const MAX_WALL_POSTS =
            <?= (int)$MAX_POSTS_PER_WALL ?>;


        const MAX_STUDENT_POSTS =
            <?= (int)$MAX_POSTS_PER_STUDENT ?>;


        const MAX_MESSAGE_LENGTH =
            <?= (int)$MAX_MESSAGE_LENGTH ?>;


        const REFRESH_INTERVAL =
            1000;


        const CANVAS_WIDTH =
            <?= (int)$CANVAS_WIDTH ?>;


        const CANVAS_HEIGHT =
            <?= (int)$CANVAS_HEIGHT ?>;


        /* =====================================================
           ZOOM LIMITS
        ====================================================== */

        const MIN_ZOOM =
            0.10;


        const MAX_ZOOM =
            2;


        /* =====================================================
           ELEMENTS
        ====================================================== */

        const viewport =
            document.getElementById(
                "freedomWallViewport"
            );


        const canvas =
            document.getElementById(
                "freedomWallCanvas"
            );


        const wrapper =
            document.getElementById(
                "freedomWallCanvasWrapper"
            );


        const form =
            document.getElementById(
                "freedomWallForm"
            );


        const messageInput =
            document.getElementById(
                "messageInput"
            );


        const postButton =
            document.getElementById(
                "postButton"
            );


        const characterCounter =
            document.getElementById(
                "characterCounter"
            );


        const wallCount =
            document.getElementById(
                "wallCount"
            );


        const emptyMessage =
            document.getElementById(
                "wallEmptyMessage"
            );


        const toast =
            document.getElementById(
                "freedomWallToast"
            );


        const zoomInButton =
            document.getElementById(
                "zoomInButton"
            );


        const zoomOutButton =
            document.getElementById(
                "zoomOutButton"
            );


        const fitWallButton =
            document.getElementById(
                "fitWallButton"
            );


        const refreshWallButton =
            document.getElementById(
                "refreshWallButton"
            );


        const exportWallButton =
            document.getElementById(
                "exportWallButton"
            );


        /* =====================================================
           CANVAS CONTEXT
        ====================================================== */

        const ctx =
            canvas.getContext(
                "2d"
            );


        /* =====================================================
           STATE
        ====================================================== */

        let posts = [];


        let zoom = 1;


        let offsetX = 0;


        let offsetY = 0;


        let lastSignature = "";


        let toastTimer = null;


        /* =====================================================
           POINTER STATE

           Supports:
           - mouse
           - one-finger touch
           - two-finger pinch
        ====================================================== */

        const activePointers =
            new Map();


        let isDragging = false;


        let dragStartX = 0;


        let dragStartY = 0;


        let dragStartOffsetX = 0;


        let dragStartOffsetY = 0;


        let pinchStartDistance = 0;


        let pinchStartZoom = 1;


        let pinchWorldX = 0;


        let pinchWorldY = 0;


        let pinchCenterX = 0;


        let pinchCenterY = 0;


        /* =====================================================
           TOAST
        ====================================================== */

        function showToast(
            message,
            type = "success"
        ) {

            toast.textContent =
                message;


            toast.className =
                "freedom-wall-toast " +
                type +
                " show";


            clearTimeout(
                toastTimer
            );


            toastTimer =
                setTimeout(
                    function() {

                        toast.classList.remove(
                            "show"
                        );

                    },
                    3000
                );
        }


        /* =====================================================
           CHARACTER COUNTER
        ====================================================== */

        function updateCharacterCounter() {

            const length =
                Array.from(
                    messageInput.value
                ).length;


            characterCounter.textContent =
                length +
                " / " +
                MAX_MESSAGE_LENGTH;


            if (
                length >=
                MAX_MESSAGE_LENGTH
            ) {

                characterCounter.style.color =
                    "#DC2626";

            } else {

                characterCounter.style.color =
                    "";
            }
        }


        messageInput.addEventListener(
            "input",
            updateCharacterCounter
        );


        updateCharacterCounter();


        /* =====================================================
           POST SIGNATURE
        ====================================================== */

        function createPostSignature(
            postList
        ) {

            return postList
                .map(
                    function(post) {

                        return [

                            post.id,

                            post.message,

                            post.x_position,

                            post.y_position,

                            post.font_family,

                            post.font_size,

                            post.font_color,

                            post.text_stroke,

                            post.text_stroke_width,

                            post.text_glow,

                            post.text_glow_blur,

                            post.rotation,

                            post.opacity

                        ].join("|");

                    }
                )
                .join(";");
        }


        /* =====================================================
           APPLY TRANSFORM
        ====================================================== */

        function applyTransform() {

            wrapper.style.transform =
                "translate3d(" +
                offsetX +
                "px, " +
                offsetY +
                "px, 0) scale(" +
                zoom +
                ")";

        }


        /* =====================================================
           FIT ENTIRE WALL
        ====================================================== */

        function fitEntireWall() {

            const viewportWidth =
                viewport.clientWidth;


            const viewportHeight =
                viewport.clientHeight;


            const margin =
                Math.min(
                    40,
                    Math.max(
                        12,
                        viewportWidth * 0.025
                    )
                );


            const availableWidth =
                Math.max(
                    100,
                    viewportWidth -
                    margin * 2
                );


            const availableHeight =
                Math.max(
                    100,
                    viewportHeight -
                    margin * 2
                );


            const widthZoom =
                availableWidth /
                CANVAS_WIDTH;


            const heightZoom =
                availableHeight /
                CANVAS_HEIGHT;


            zoom =
                Math.min(
                    widthZoom,
                    heightZoom
                );


            zoom =
                Math.max(
                    MIN_ZOOM,
                    Math.min(
                        zoom,
                        MAX_ZOOM
                    )
                );


            offsetX =
                (
                    viewportWidth -
                    CANVAS_WIDTH *
                    zoom
                ) / 2;


            offsetY =
                (
                    viewportHeight -
                    CANVAS_HEIGHT *
                    zoom
                ) / 2;


            applyTransform();

        }


        /* =====================================================
           MANUAL ZOOM
        ====================================================== */

        function changeZoom(
            amount
        ) {

            const oldZoom =
                zoom;


            const newZoom =
                Math.min(
                    MAX_ZOOM,
                    Math.max(
                        MIN_ZOOM,
                        zoom + amount
                    )
                );


            if (
                newZoom ===
                oldZoom
            ) {
                return;
            }


            const centerX =
                viewport.clientWidth /
                2;


            const centerY =
                viewport.clientHeight /
                2;


            const worldX =
                (
                    centerX -
                    offsetX
                ) / oldZoom;


            const worldY =
                (
                    centerY -
                    offsetY
                ) / oldZoom;


            zoom =
                newZoom;


            offsetX =
                centerX -
                worldX *
                zoom;


            offsetY =
                centerY -
                worldY *
                zoom;


            applyTransform();

        }


        /* =====================================================
           GET POINTER DISTANCE
        ====================================================== */

        function getPointerDistance(
            pointerA,
            pointerB
        ) {

            const dx =
                pointerA.clientX -
                pointerB.clientX;


            const dy =
                pointerA.clientY -
                pointerB.clientY;


            return Math.sqrt(
                dx * dx +
                dy * dy
            );
        }


        /* =====================================================
           GET POINTER CENTER
        ====================================================== */

        function getPointerCenter(
            pointerA,
            pointerB
        ) {

            return {

                x: (
                    pointerA.clientX +
                    pointerB.clientX
                ) / 2,

                y: (
                    pointerA.clientY +
                    pointerB.clientY
                ) / 2

            };
        }


        /* =====================================================
           START PINCH
        ====================================================== */

        function startPinch() {

            const pointers =
                Array.from(
                    activePointers.values()
                );


            if (
                pointers.length !== 2
            ) {
                return;
            }


            const first =
                pointers[0];


            const second =
                pointers[1];


            pinchStartDistance =
                getPointerDistance(
                    first,
                    second
                );


            pinchStartZoom =
                zoom;


            const center =
                getPointerCenter(
                    first,
                    second
                );


            pinchCenterX =
                center.x;


            pinchCenterY =
                center.y;


            /*
                Convert the pinch center from
                screen coordinates into canvas
                world coordinates.

                This keeps the same point under
                the users' fingers while zooming.
            */

            pinchWorldX =
                (
                    pinchCenterX -
                    offsetX
                ) / zoom;


            pinchWorldY =
                (
                    pinchCenterY -
                    offsetY
                ) / zoom;

        }


        /* =====================================================
           HANDLE PINCH
        ====================================================== */

        function handlePinch() {

            const pointers =
                Array.from(
                    activePointers.values()
                );


            if (
                pointers.length !== 2
            ) {
                return;
            }


            const first =
                pointers[0];


            const second =
                pointers[1];


            const currentDistance =
                getPointerDistance(
                    first,
                    second
                );


            if (
                pinchStartDistance <= 0
            ) {
                return;
            }


            const scale =
                currentDistance /
                pinchStartDistance;


            let newZoom =
                pinchStartZoom *
                scale;


            newZoom =
                Math.min(
                    MAX_ZOOM,
                    Math.max(
                        MIN_ZOOM,
                        newZoom
                    )
                );


            zoom =
                newZoom;


            /*
                Keep the world point under the
                pinch center.
            */

            offsetX =
                pinchCenterX -
                pinchWorldX *
                zoom;


            offsetY =
                pinchCenterY -
                pinchWorldY *
                zoom;


            applyTransform();

        }


        /* =====================================================
           POINTER DOWN
        ====================================================== */

        viewport.addEventListener(
            "pointerdown",
            function(event) {

                activePointers.set(
                    event.pointerId, {
                        clientX: event.clientX,

                        clientY: event.clientY
                    }
                );


                try {

                    viewport.setPointerCapture(
                        event.pointerId
                    );

                } catch (error) {
                    /*
                        Some browsers may not
                        support capture for every
                        pointer type.
                    */
                }


                /* ============================================
                   TWO FINGERS
                ============================================ */

                if (
                    activePointers.size === 2
                ) {

                    isDragging =
                        false;


                    startPinch();

                    return;
                }


                /* ============================================
                   ONE POINTER
                ============================================ */

                if (
                    activePointers.size === 1
                ) {

                    isDragging =
                        true;


                    dragStartX =
                        event.clientX;


                    dragStartY =
                        event.clientY;


                    dragStartOffsetX =
                        offsetX;


                    dragStartOffsetY =
                        offsetY;


                    viewport.style.cursor =
                        "grabbing";
                }

            }
        );


        /* =====================================================
           POINTER MOVE
        ====================================================== */

        viewport.addEventListener(
            "pointermove",
            function(event) {

                if (
                    activePointers.has(
                        event.pointerId
                    )
                ) {

                    activePointers.set(
                        event.pointerId, {
                            clientX: event.clientX,

                            clientY: event.clientY
                        }
                    );
                }


                /* ============================================
                   TWO-FINGER PINCH
                ============================================ */

                if (
                    activePointers.size === 2
                ) {

                    isDragging =
                        false;


                    handlePinch();

                    return;
                }


                /* ============================================
                   ONE-FINGER / MOUSE PAN
                ============================================ */

                if (
                    activePointers.size === 1 &&
                    isDragging
                ) {

                    const deltaX =
                        event.clientX -
                        dragStartX;


                    const deltaY =
                        event.clientY -
                        dragStartY;


                    offsetX =
                        dragStartOffsetX +
                        deltaX;


                    offsetY =
                        dragStartOffsetY +
                        deltaY;


                    applyTransform();

                }

            }
        );


        /* =====================================================
           POINTER UP
        ====================================================== */

        function handlePointerEnd(
            event
        ) {

            activePointers.delete(
                event.pointerId
            );


            /*
                If one finger remains after
                a pinch, restart one-finger
                dragging from its current position.
            */

            if (
                activePointers.size === 1
            ) {

                const remaining =
                    Array.from(
                        activePointers.values()
                    )[0];


                isDragging =
                    true;


                dragStartX =
                    remaining.clientX;


                dragStartY =
                    remaining.clientY;


                dragStartOffsetX =
                    offsetX;


                dragStartOffsetY =
                    offsetY;

            } else {

                isDragging =
                    false;

            }


            if (
                activePointers.size < 2
            ) {

                pinchStartDistance =
                    0;

            }


            if (
                activePointers.size === 0
            ) {

                viewport.style.cursor =
                    "grab";
            }

        }


        viewport.addEventListener(
            "pointerup",
            handlePointerEnd
        );


        viewport.addEventListener(
            "pointercancel",
            handlePointerEnd
        );


        /* =====================================================
           DISABLE CONTEXT MENU
        ====================================================== */

        viewport.addEventListener(
            "contextmenu",
            function(event) {

                event.preventDefault();

            }
        );


        /* =====================================================
           DISABLE MOUSE WHEEL ZOOM
        ====================================================== */

        viewport.addEventListener(
            "wheel",
            function(event) {

                event.preventDefault();

            }, {
                passive: false
            }
        );


        /* =====================================================
           DRAW POST
        ====================================================== */

        function drawPost(
            post
        ) {

            const x =
                Number(
                    post.x_position
                );


            const y =
                Number(
                    post.y_position
                );


            const fontSize =
                Number(
                    post.font_size
                );


            const rotation =
                Number(
                    post.rotation
                );


            ctx.save();


            ctx.translate(
                x,
                y
            );


            ctx.rotate(
                rotation *
                Math.PI /
                180
            );


            /* ================================================
               FONT
            ================================================= */

            ctx.font =
                fontSize +
                "px \"" +
                post.font_family +
                "\"";


            ctx.textAlign =
                "center";


            ctx.textBaseline =
                "middle";


            ctx.globalAlpha =
                Number(
                    post.opacity
                );


            /* ================================================
               GLOW
            ================================================= */

            ctx.shadowOffsetX =
                0;


            ctx.shadowOffsetY =
                0;


            ctx.shadowColor =
                "transparent";


            ctx.shadowBlur =
                0;


            if (
                post.text_glow &&
                post.text_glow !== "none"
            ) {

                ctx.shadowColor =
                    post.font_color;


                ctx.shadowBlur =
                    Number(
                        post.text_glow_blur
                    );

            }


            /* ================================================
               STROKE
            ================================================= */

            if (
                post.text_stroke &&
                post.text_stroke !== "none" &&
                Number(
                    post.text_stroke_width
                ) > 0
            ) {

                ctx.lineWidth =
                    Number(
                        post.text_stroke_width
                    );


                ctx.strokeStyle =
                    "rgba(255,255,255,0.92)";


                ctx.strokeText(
                    post.message,
                    0,
                    0
                );

            }


            /* ================================================
               MAIN TEXT
            ================================================= */

            ctx.fillStyle =
                post.font_color;


            ctx.fillText(
                post.message,
                0,
                0
            );


            ctx.restore();

        }


        /* =====================================================
           DRAW WALL
        ====================================================== */

        function draw() {

            /*
                Clear the entire canvas.
            */

            ctx.clearRect(
                0,
                0,
                CANVAS_WIDTH,
                CANVAS_HEIGHT
            );


            /* ================================================
               THEME BACKGROUND
            ================================================= */

            const isDark =
                document.documentElement
                .getAttribute(
                    "data-theme"
                ) === "dark";


            ctx.fillStyle =
                isDark ?
                "#1F2937" :
                "#FFFFFF";


            ctx.fillRect(
                0,
                0,
                CANVAS_WIDTH,
                CANVAS_HEIGHT
            );


            /* ================================================
               POSTS
            ================================================= */

            posts.forEach(
                function(post) {

                    drawPost(
                        post
                    );

                }
            );


            /* ================================================
               EMPTY STATE
            ================================================= */

            if (
                posts.length === 0
            ) {

                emptyMessage.style.display =
                    "block";

            } else {

                emptyMessage.style.display =
                    "none";
            }

        }


        /* =====================================================
           LOAD POSTS
        ====================================================== */

        async function loadPosts(
            showLoading = false
        ) {

            try {

                if (
                    showLoading
                ) {

                    wallCount.textContent =
                        "Loading...";

                }


                const response =
                    await fetch(
                        "freedom_wall.php" +
                        "?wall_id=" +
                        WALL_ID +
                        "&action=load" +
                        "&_=" +
                        Date.now(), {
                            method: "GET",
                            cache: "no-store"
                        }
                    );


                const result =
                    await response.json();


                if (
                    !result.success
                ) {

                    if (
                        showLoading
                    ) {

                        showToast(
                            result.message ||
                            "Unable to load posts.",
                            "error"
                        );

                    }

                    return;
                }


                const newPosts =
                    result.data.posts || [];


                const newSignature =
                    createPostSignature(
                        newPosts
                    );


                /*
                    Only redraw when posts
                    actually changed.

                    This prevents flickering and
                    preserves zoom/pan.
                */

                if (
                    newSignature !==
                    lastSignature
                ) {

                    posts =
                        newPosts;


                    lastSignature =
                        newSignature;


                    draw();

                }


                wallCount.textContent =
                    result.data.post_count +
                    " / " +
                    MAX_WALL_POSTS +
                    " posts";


            } catch (error) {

                console.error(
                    "Load posts error:",
                    error
                );


                if (
                    showLoading
                ) {

                    showToast(
                        "Unable to connect to the Freedom Wall.",
                        "error"
                    );

                }

            }

        }


        /* =====================================================
           CREATE POST
        ====================================================== */

        form.addEventListener(
            "submit",
            async function(event) {

                event.preventDefault();


                const message =
                    messageInput.value.trim();


                if (!message) {

                    showToast(
                        "Please enter a message.",
                        "error"
                    );


                    messageInput.focus();


                    return;
                }


                const messageLength =
                    Array.from(
                        message
                    ).length;


                if (
                    messageLength >
                    MAX_MESSAGE_LENGTH
                ) {

                    showToast(
                        "Your message must not exceed " +
                        MAX_MESSAGE_LENGTH +
                        " characters.",
                        "error"
                    );


                    return;
                }


                postButton.disabled =
                    true;


                postButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Posting...';


                try {

                    const formData =
                        new FormData();


                    formData.append(
                        "message",
                        message
                    );


                    const response =
                        await fetch(
                            "freedom_wall.php" +
                            "?wall_id=" +
                            WALL_ID +
                            "&action=create", {
                                method: "POST",
                                body: formData
                            }
                        );


                    const result =
                        await response.json();


                    if (
                        !result.success
                    ) {

                        showToast(
                            result.message ||
                            "Unable to post your message.",
                            "error"
                        );


                        return;
                    }


                    messageInput.value =
                        "";


                    updateCharacterCounter();


                    showToast(
                        "Your message has been posted!",
                        "success"
                    );


                    await loadPosts(
                        false
                    );


                } catch (error) {

                    console.error(
                        "Create post error:",
                        error
                    );


                    showToast(
                        "Unable to post your message.",
                        "error"
                    );


                } finally {

                    postButton.disabled =
                        false;


                    postButton.innerHTML =
                        '<i class="bi bi-send-fill"></i> Post';

                }

            }
        );


        /* =====================================================
           ZOOM BUTTONS
        ====================================================== */

        zoomInButton.addEventListener(
            "click",
            function() {

                changeZoom(
                    0.10
                );

            }
        );


        zoomOutButton.addEventListener(
            "click",
            function() {

                changeZoom(
                    -0.10
                );

            }
        );


        /* =====================================================
           FIT BUTTON
        ====================================================== */

        fitWallButton.addEventListener(
            "click",
            function() {

                fitEntireWall();

            }
        );


        /* =====================================================
           REFRESH BUTTON
        ====================================================== */

        refreshWallButton.addEventListener(
            "click",
            function() {

                loadPosts(
                    true
                );

            }
        );


        /* =====================================================
           EXPORT WALL
        ====================================================== */

        function exportWallAsImage() {

            if (
                posts.length === 0
            ) {

                showToast(
                    "There are no posts to export yet.",
                    "error"
                );


                return;
            }


            try {

                /*
                    Draw the complete native
                    3000 x 2000 canvas.

                    The current zoom/pan does
                    NOT affect the exported image.
                */

                draw();


                const image =
                    canvas.toDataURL(
                        "image/png"
                    );


                const link =
                    document.createElement(
                        "a"
                    );


                const safeTitle =
                    <?= json_encode(
                        $wall["title"],
                        JSON_UNESCAPED_UNICODE
                    ) ?>;


                const cleanTitle =
                    String(
                        safeTitle
                    )
                    .replace(
                        /[^a-zA-Z0-9\u00C0-\uFFFF]+/g,
                        "_"
                    )
                    .replace(
                        /^_+|_+$/g,
                        ""
                    );


                const filename =
                    (
                        cleanTitle ||
                        "freedom_wall"
                    ) +
                    "_Freedom_Wall.png";


                link.href =
                    image;


                link.download =
                    filename;


                document.body.appendChild(
                    link
                );


                link.click();


                document.body.removeChild(
                    link
                );


                showToast(
                    "Freedom Wall exported successfully.",
                    "success"
                );


            } catch (error) {

                console.error(
                    "Export error:",
                    error
                );


                showToast(
                    "Unable to export the Freedom Wall.",
                    "error"
                );

            }

        }


        exportWallButton.addEventListener(
            "click",
            exportWallAsImage
        );


        /* =====================================================
           THEME CHANGE OBSERVER
        ====================================================== */

        const themeObserver =
            new MutationObserver(
                function() {

                    draw();

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


        /* =====================================================
           WINDOW RESIZE
        ====================================================== */

        let resizeTimer =
            null;


        window.addEventListener(
            "resize",
            function() {

                clearTimeout(
                    resizeTimer
                );


                resizeTimer =
                    setTimeout(
                        function() {

                            fitEntireWall();

                        },
                        150
                    );

            }
        );


        /* =====================================================
           AUTO REFRESH
        ====================================================== */

        setInterval(
            function() {

                /*
                    Don't reload while the
                    user is manipulating the wall.
                */

                if (
                    !isDragging &&
                    activePointers.size === 0
                ) {

                    loadPosts(
                        false
                    );

                }

            },
            REFRESH_INTERVAL
        );


        /* =====================================================
           INITIALIZATION
        ====================================================== */

        fitEntireWall();


        draw();


        loadPosts(
            true
        );


    })();
</script>


<?php

/* =========================================================
   GLOBAL SCRIPTS
========================================================= */

require_once "./globals/scripts.php";

?>