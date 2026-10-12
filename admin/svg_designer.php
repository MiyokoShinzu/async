<?php
/*
 * SVG Border Designer — ETS-Async Learning Portal
 * Suggested location: admin/svg_border_designer.php
 *
 * Requires:
 *   - ../src/connection.php exposing a mysqli connection as $mysqli
 *   - An admin account in $_SESSION['user']['access'] (or access_level/role)
 *
 * Database table is created automatically if the DB user has CREATE permission.
 * You can also run svg_border_presets.sql manually.
 */

session_start();

if (
    empty($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    empty($_SESSION["user"])
) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION["user"];
$access = strtolower(trim((string)($user["access"] ?? $user["access_level"] ?? $user["role"] ?? "")));
if (!in_array($access, ["admin", "administrator"], true)) {
    http_response_code(403);
    exit("Access denied. Administrator permission is required.");
}

require_once __DIR__ . "/../src/connection.php";

if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    http_response_code(500);
    exit("Database connection not found. Ensure ../src/connection.php defines $mysqli as mysqli.");
}
$mysqli->set_charset("utf8mb4");

$tableSql = "
CREATE TABLE IF NOT EXISTS svg_border_presets (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    preset_name VARCHAR(100) NOT NULL,
    target_area ENUM('topbar','profile','both') NOT NULL DEFAULT 'both',
    border_style ENUM('hud','hexagon','neon','corners','orbit') NOT NULL DEFAULT 'hud',
    primary_color CHAR(7) NOT NULL DEFAULT '#00E5FF',
    secondary_color CHAR(7) NOT NULL DEFAULT '#5B7CFF',
    glow_strength TINYINT UNSIGNED NOT NULL DEFAULT 70,
    animation_speed DECIMAL(4,2) NOT NULL DEFAULT 3.00,
    animation_type ENUM('travel','pulse','rotate','static') NOT NULL DEFAULT 'travel',
    enabled TINYINT(1) NOT NULL DEFAULT 0,
    created_by VARCHAR(100) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_target_enabled (target_area, enabled)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$mysqli->query($tableSql);

if (empty($_SESSION["svg_designer_csrf"])) {
    $_SESSION["svg_designer_csrf"] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION["svg_designer_csrf"];
$message = "";
$error = "";

function esc($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
function validHex($value): bool
{
    return is_string($value) && preg_match('/^#[0-9A-Fa-f]{6}$/', $value);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postedCsrf = (string)($_POST["csrf_token"] ?? "");
    if (!hash_equals($csrf, $postedCsrf)) {
        $error = "Your session token expired. Refresh the page and try again.";
    } else {
        $action = (string)($_POST["action"] ?? "");
        if ($action === "save") {
            $name = trim((string)($_POST["preset_name"] ?? ""));
            $target = (string)($_POST["target_area"] ?? "both");
            $style = (string)($_POST["border_style"] ?? "hud");
            $primary = (string)($_POST["primary_color"] ?? "#00E5FF");
            $secondary = (string)($_POST["secondary_color"] ?? "#5B7CFF");
            $glow = filter_var($_POST["glow_strength"] ?? 70, FILTER_VALIDATE_INT);
            $speed = filter_var($_POST["animation_speed"] ?? 3, FILTER_VALIDATE_FLOAT);
            $animation = (string)($_POST["animation_type"] ?? "travel");
            $enabled = isset($_POST["enabled"]) ? 1 : 0;
            $presetId = filter_var($_POST["preset_id"] ?? 0, FILTER_VALIDATE_INT);
            $creator = trim((string)($user["username"] ?? $user["first_name"] ?? $user["name"] ?? "admin"));

            $allowedTargets = ["topbar", "profile", "both"];
            $allowedStyles = ["hud", "hexagon", "neon", "corners", "orbit"];
            $allowedAnimations = ["travel", "pulse", "rotate", "static"];

            if ($name === "" || mb_strlen($name) > 100) {
                $error = "Enter a preset name of 1–100 characters.";
            } elseif (
                !in_array($target, $allowedTargets, true) ||
                !in_array($style, $allowedStyles, true) ||
                !in_array($animation, $allowedAnimations, true)
            ) {
                $error = "One of the selected design options is invalid.";
            } elseif (!validHex($primary) || !validHex($secondary)) {
                $error = "Choose valid six-digit colors.";
            } elseif ($glow === false || $glow < 0 || $glow > 100) {
                $error = "Glow strength must be between 0 and 100.";
            } elseif ($speed === false || $speed < 0.5 || $speed > 12) {
                $error = "Animation speed must be between 0.5 and 12 seconds.";
            } else {
                if ($enabled) {
                    $stmt = $mysqli->prepare("UPDATE svg_border_presets SET enabled = 0 WHERE target_area IN (?, 'both') OR ? = 'both'");
                    $stmt->bind_param("ss", $target, $target);
                    $stmt->execute();
                    $stmt->close();
                }

                if ($presetId > 0) {
                    $stmt = $mysqli->prepare("UPDATE svg_border_presets SET preset_name=?, target_area=?, border_style=?, primary_color=?, secondary_color=?, glow_strength=?, animation_speed=?, animation_type=?, enabled=?, created_by=? WHERE id=?");
                    $stmt->bind_param("sssssidsisi", $name, $target, $style, $primary, $secondary, $glow, $speed, $animation, $enabled, $creator, $presetId);
                    $ok = $stmt->execute();
                    $stmt->close();
                } else {
                    $stmt = $mysqli->prepare("INSERT INTO svg_border_presets (preset_name,target_area,border_style,primary_color,secondary_color,glow_strength,animation_speed,animation_type,enabled,created_by) VALUES (?,?,?,?,?,?,?,?,?,?)");
                    $stmt->bind_param("sssssidsis", $name, $target, $style, $primary, $secondary, $glow, $speed, $animation, $enabled, $creator);
                    $ok = $stmt->execute();
                    $stmt->close();
                }
                if (!empty($ok)) {
                    header("Location: " . strtok($_SERVER["REQUEST_URI"], "?") . "?saved=1");
                    exit;
                }
                $error = "Unable to save the preset. Please check the database.";
            }
        } elseif ($action === "delete") {
            $presetId = filter_var($_POST["preset_id"] ?? 0, FILTER_VALIDATE_INT);
            if ($presetId && $presetId > 0) {
                $stmt = $mysqli->prepare("DELETE FROM svg_border_presets WHERE id=?");
                $stmt->bind_param("i", $presetId);
                $stmt->execute();
                $stmt->close();
                header("Location: " . strtok($_SERVER["REQUEST_URI"], "?") . "?deleted=1");
                exit;
            }
            $error = "Invalid preset selected.";
        } elseif ($action === "activate") {
            $presetId = filter_var($_POST["preset_id"] ?? 0, FILTER_VALIDATE_INT);
            if ($presetId && $presetId > 0) {
                $stmt = $mysqli->prepare("SELECT target_area FROM svg_border_presets WHERE id=?");
                $stmt->bind_param("i", $presetId);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                if ($row) {
                    $target = $row["target_area"];
                    $stmt = $mysqli->prepare("UPDATE svg_border_presets SET enabled=0 WHERE target_area IN (?, 'both') OR ?='both'");
                    $stmt->bind_param("ss", $target, $target);
                    $stmt->execute();
                    $stmt->close();
                    $stmt = $mysqli->prepare("UPDATE svg_border_presets SET enabled=1 WHERE id=?");
                    $stmt->bind_param("i", $presetId);
                    $stmt->execute();
                    $stmt->close();
                    header("Location: " . strtok($_SERVER["REQUEST_URI"], "?") . "?activated=1");
                    exit;
                }
            }
            $error = "Preset not found.";
        }
    }
}

if (isset($_GET["saved"])) $message = "Preset saved to the database.";
if (isset($_GET["deleted"])) $message = "Preset deleted.";
if (isset($_GET["activated"])) $message = "Preset activated.";

$presets = [];
$result = $mysqli->query("SELECT * FROM svg_border_presets ORDER BY enabled DESC, updated_at DESC, id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) $presets[] = $row;
    $result->free();
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SVG Border Designer | ETS-Async</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg: #08111f;
            --panel: #101d30;
            --panel2: #14243a;
            --line: #233750;
            --text: #e9f2ff;
            --muted: #91a6c2;
            --cyan: #00e5ff;
            --blue: #5b7cff
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            background: radial-gradient(ellipse at top right, #102d4c 0, transparent 40%), var(--bg);
            color: var(--text);
            font-family: Inter, system-ui, -apple-system, Segoe UI, sans-serif;
            min-height: 100vh
        }

        .page-shell {
            max-width: 1500px;
            margin: auto;
            padding: 28px 20px 48px
        }

        .page-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 24px
        }

        .eyebrow {
            color: var(--cyan);
            font-size: .75rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            font-weight: 800
        }

        h1 {
            font-size: clamp(1.7rem, 3vw, 2.5rem);
            font-weight: 800;
            margin: .3rem 0
        }

        .sub {
            color: var(--muted);
            margin: 0
        }

        .glass-panel {
            background: linear-gradient(145deg, rgba(20, 36, 58, .96), rgba(12, 24, 40, .96));
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 18px 55px #0003
        }

        .panel-title {
            font-size: 1rem;
            font-weight: 800;
            margin: 0 0 18px;
            display: flex;
            align-items: center;
            gap: 9px
        }

        .panel-title i {
            color: var(--cyan)
        }

        .form-label {
            color: #c8d7ec;
            font-size: .84rem;
            font-weight: 700
        }

        .form-control,
        .form-select {
            background: #091525;
            color: var(--text);
            border: 1px solid #2b405b;
            border-radius: 10px;
            min-height: 43px
        }

        .form-control:focus,
        .form-select:focus {
            background: #091525;
            color: var(--text);
            border-color: var(--cyan);
            box-shadow: 0 0 0 .2rem #00e5ff1b
        }

        .form-control[type=color] {
            padding: 5px;
            height: 43px
        }

        .form-range {
            accent-color: var(--cyan)
        }

        .btn-cyan {
            background: var(--cyan);
            color: #04111c;
            border: 0;
            font-weight: 800;
            border-radius: 10px;
            padding: 11px 17px
        }

        .btn-cyan:hover {
            background: #83f4ff;
            color: #04111c
        }

        .btn-outline-light {
            border-color: #3a516e;
            border-radius: 10px
        }

        .designer-grid {
            display: grid;
            grid-template-columns: minmax(310px, 390px) minmax(0, 1fr);
            gap: 22px;
            align-items: start
        }

        .pad-panel {
            padding: 24px
        }

        .preview-stage {
            min-height: 340px;
            border: 1px dashed #2c4664;
            border-radius: 16px;
            background: radial-gradient(circle at center, #142943 0, #0b1727 62%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
            overflow: hidden
        }

        .preview-target {
            position: relative;
            isolation: isolate;
            display: flex;
            align-items: center;
            justify-content: center;
            width: min(100%, 680px);
            min-height: 160px;
            padding: 24px;
            background: linear-gradient(135deg, #14263d, #0a1423);
            border-radius: 14px;
            overflow: hidden
        }

        .preview-target.profile-target {
            width: 210px;
            height: 210px;
            min-height: 0;
            border-radius: 50%;
            padding: 15px
        }

        .preview-target svg.border-svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
            overflow: visible
        }

        .preview-target .sample-content {
            position: relative;
            z-index: 1;
            text-align: center
        }

        .sample-brand {
            font-size: 1.2rem;
            font-weight: 900;
            letter-spacing: .03em
        }

        .sample-sub {
            font-size: .78rem;
            color: #91a6c2;
            margin-top: 4px
        }

        .sample-avatar {
            width: 92px;
            height: 92px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            margin: auto;
            background: linear-gradient(135deg, #173e61, #152541);
            border: 1px solid #315879;
            font-size: 2.2rem;
            color: #c5faff
        }

        .preview-caption {
            color: var(--muted);
            font-size: .8rem;
            margin-top: 13px;
            text-align: center
        }

        .preset-card {
            background: #0a1727;
            border: 1px solid #263c56;
            border-radius: 14px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 12px
        }

        .preset-swatch {
            width: 42px;
            height: 42px;
            flex: 0 0 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--p1), var(--p2));
            box-shadow: 0 0 18px color-mix(in srgb, var(--p1), transparent 65%)
        }

        .preset-card strong {
            display: block;
            font-size: .93rem
        }

        .preset-meta {
            font-size: .75rem;
            color: var(--muted)
        }

        .preset-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-left: auto
        }

        .mini-btn {
            border: 1px solid #304965;
            background: #102238;
            color: #d9eaff;
            border-radius: 8px;
            padding: 6px 9px;
            font-size: .78rem
        }

        .mini-btn:hover {
            border-color: var(--cyan);
            color: var(--cyan)
        }

        .status-pill {
            font-size: .68rem;
            padding: 4px 7px;
            border-radius: 999px;
            background: #073b3f;
            color: #79f5ef;
            border: 1px solid #126268
        }

        .code-hint {
            font-size: .78rem;
            color: var(--muted)
        }

        .form-check-input {
            background-color: #0a1727;
            border-color: #3a516e
        }

        .form-check-input:checked {
            background-color: #00bcd4;
            border-color: #00bcd4
        }

        @media(max-width:900px) {
            .designer-grid {
                grid-template-columns: 1fr
            }

            .page-head {
                align-items: flex-start;
                flex-direction: column
            }

            .preview-stage {
                min-height: 290px
            }

            .pad-panel {
                padding: 18px
            }
        }

        @media(prefers-reduced-motion:reduce) {

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important
            }
        }
    </style>
</head>

<body>
    <div class="page-shell">
        <header class="page-head">
            <div>
                <div class="eyebrow"><i class="bi bi-bezier2 me-1"></i> Interface Lab / SVG Studio</div>
                <h1>SVG Border Designer</h1>
                <p class="sub">Design a gaming-style frame, preview it live, and save reusable presets to MySQL.</p>
            </div>
            <a class="btn btn-outline-light" href="javascript:history.back()"><i class="bi bi-arrow-left me-1"></i> Back</a>
        </header>

        <?php if ($message): ?><div class="alert alert-success border-0"><?= esc($message) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="alert alert-danger border-0"><?= esc($error) ?></div><?php endif; ?>

        <div class="designer-grid">
            <section class="glass-panel pad-panel">
                <h2 class="panel-title"><i class="bi bi-sliders2"></i> Design settings</h2>
                <form method="post" id="designerForm">
                    <input type="hidden" name="csrf_token" value="<?= esc($csrf) ?>">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="preset_id" id="presetId" value="0">

                    <div class="mb-3">
                        <label class="form-label" for="presetName">Preset name</label>
                        <input class="form-control" id="presetName" name="preset_name" maxlength="100" value="Neon HUD Cyan" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label" for="targetArea">Apply to</label>
                            <select class="form-select" id="targetArea" name="target_area">
                                <option value="topbar">Top bar</option>
                                <option value="profile">Profile</option>
                                <option value="both" selected>Both</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="borderStyle">Border shape</label>
                            <select class="form-select" id="borderStyle" name="border_style">
                                <option value="hud" selected>Futuristic HUD</option>
                                <option value="hexagon">Hexagon</option>
                                <option value="neon">Neon frame</option>
                                <option value="corners">Corner brackets</option>
                                <option value="orbit">Orbit rings</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="primaryColor">Primary color</label>
                            <input class="form-control form-control-color w-100" type="color" id="primaryColor" name="primary_color" value="#00e5ff">
                        </div>
                        <div class="col-6">
                            <label class="form-label" for="secondaryColor">Secondary color</label>
                            <input class="form-control form-control-color w-100" type="color" id="secondaryColor" name="secondary_color" value="#5b7cff">
                        </div>
                        <div class="col-12">
                            <label class="form-label d-flex justify-content-between" for="glowStrength"><span>Glow strength</span><span id="glowValue">70%</span></label>
                            <input class="form-range" type="range" min="0" max="100" value="70" id="glowStrength" name="glow_strength">
                        </div>
                        <div class="col-12">
                            <label class="form-label d-flex justify-content-between" for="animationSpeed"><span>Animation duration</span><span id="speedValue">3.0s</span></label>
                            <input class="form-range" type="range" min="0.5" max="12" step="0.5" value="3" id="animationSpeed" name="animation_speed">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="animationType">Animation</label>
                            <select class="form-select" id="animationType" name="animation_type">
                                <option value="travel" selected>Light travels around border</option>
                                <option value="pulse">Pulsing glow</option>
                                <option value="rotate">Rotating dash pattern</option>
                                <option value="static">Static border</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check form-switch my-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="enabled" name="enabled" value="1">
                        <label class="form-check-label" for="enabled">Activate this preset after saving</label>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-cyan flex-grow-1" type="submit"><i class="bi bi-database-check me-1"></i> Save preset</button>
                        <button class="btn btn-outline-light" type="button" id="resetBtn">Reset</button>
                    </div>
                    <p class="code-hint mt-3 mb-0"><i class="bi bi-info-circle me-1"></i> Saves the design settings and preset metadata. Applying it to your live top bar/profile requires the shared topbar template to read the active preset.</p>
                </form>
            </section>

            <div class="d-grid gap-4">
                <section class="glass-panel pad-panel">
                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                        <div>
                            <h2 class="panel-title mb-1"><i class="bi bi-eye"></i> Live preview</h2>
                            <div class="code-hint">Change any setting to preview it immediately.</div>
                        </div>
                        <span class="status-pill">LIVE</span>
                    </div>
                    <div class="preview-stage">
                        <div class="preview-target" id="previewTarget">
                            <div class="sample-content">
                                <div class="sample-brand"><i class="bi bi-cpu me-2"></i>ETS-ASYNC</div>
                                <div class="sample-sub">Learning Portal · Interface Preview</div>
                            </div>
                        </div>
                    </div>
                    <div class="preview-caption" id="previewCaption">Futuristic HUD · Both · Light travels around border</div>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button class="mini-btn" type="button" id="topbarDemo"><i class="bi bi-window me-1"></i> Top bar preview</button>
                        <button class="mini-btn" type="button" id="profileDemo"><i class="bi bi-person-circle me-1"></i> Profile preview</button>
                    </div>
                </section>

                <section class="glass-panel pad-panel">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                        <h2 class="panel-title mb-0"><i class="bi bi-collection"></i> Saved presets</h2>
                        <span class="status-pill"><?= count($presets) ?> SAVED</span>
                    </div>
                    <?php if (!$presets): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-layers display-5" style="color:#476481"></i>
                            <p class="mt-3 mb-1 fw-bold">No saved presets yet</p>
                            <p class="code-hint">Save your first design or use one of the starter presets below.</p>
                            <div class="d-flex gap-2 justify-content-center flex-wrap mt-3">
                                <button class="mini-btn" type="button" data-premade="hud">Neon HUD Cyan</button>
                                <button class="mini-btn" type="button" data-premade="hexagon">Hex Tech Blue</button>
                                <button class="mini-btn" type="button" data-premade="corners">Tactical Corners</button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="d-grid gap-2">
                            <?php foreach ($presets as $p): ?>
                                <div class="preset-card" style="--p1:<?= esc($p["primary_color"]) ?>;--p2:<?= esc($p["secondary_color"]) ?>">
                                    <div class="preset-swatch"></div>
                                    <div class="me-2">
                                        <strong><?= esc($p["preset_name"]) ?> <?php if ((int)$p["enabled"]): ?><span class="status-pill ms-1">ACTIVE</span><?php endif; ?></strong>
                                        <div class="preset-meta"><?= esc(ucfirst($p["border_style"])) ?> · <?= esc(ucfirst($p["target_area"])) ?> · <?= esc(ucfirst($p["animation_type"])) ?></div>
                                    </div>
                                    <div class="preset-actions">
                                        <button class="mini-btn load-preset" type="button"
                                            data-id="<?= (int)$p["id"] ?>" data-name="<?= esc($p["preset_name"]) ?>"
                                            data-target="<?= esc($p["target_area"]) ?>" data-style="<?= esc($p["border_style"]) ?>"
                                            data-primary="<?= esc($p["primary_color"]) ?>" data-secondary="<?= esc($p["secondary_color"]) ?>"
                                            data-glow="<?= (int)$p["glow_strength"] ?>" data-speed="<?= esc($p["animation_speed"]) ?>"
                                            data-animation="<?= esc($p["animation_type"]) ?>" data-enabled="<?= (int)$p["enabled"] ?>">Edit</button>
                                        <?php if (!(int)$p["enabled"]): ?>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= esc($csrf) ?>"><input type="hidden" name="action" value="activate"><input type="hidden" name="preset_id" value="<?= (int)$p["id"] ?>">
                                                <button class="mini-btn" type="submit">Activate</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="post" class="d-inline" onsubmit="return confirm('Delete this preset?')">
                                            <input type="hidden" name="csrf_token" value="<?= esc($csrf) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="preset_id" value="<?= (int)$p["id"] ?>">
                                            <button class="mini-btn" type="submit" aria-label="Delete preset"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const form = document.getElementById('designerForm');
            const el = id => document.getElementById(id);
            const defaults = {
                presetName: 'Neon HUD Cyan',
                targetArea: 'both',
                borderStyle: 'hud',
                primaryColor: '#00e5ff',
                secondaryColor: '#5b7cff',
                glowStrength: 70,
                animationSpeed: 3,
                animationType: 'travel'
            };
            let previewMode = 'topbar';

            function hexToRgb(hex) {
                const v = hex.replace('#', '');
                return [parseInt(v.slice(0, 2), 16), parseInt(v.slice(2, 4), 16), parseInt(v.slice(4, 6), 16)].join(',');
            }

            function borderPath(style) {
                if (style === 'hexagon') return 'M 40 2 H 360 L 398 40 V 160 L 360 198 H 40 L 2 160 V 40 Z';
                if (style === 'corners') return 'M 2 48 V 2 H 68 M 332 2 H 398 V 48 M 398 152 V 198 H 332 M 68 198 H 2 V 152';
                if (style === 'orbit') return 'M 18 100 A 182 88 0 1 0 382 100 A 182 88 0 1 0 18 100';
                if (style === 'neon') return 'M 16 2 H 384 Q 398 2 398 16 V 184 Q 398 198 384 198 H 16 Q 2 198 2 184 V 16 Q 2 2 16 2 Z';
                return 'M 2 42 V 2 H 82 M 318 2 H 398 V 42 M 398 158 V 198 H 318 M 82 198 H 2 V 158 M 2 72 H 15 M 385 72 H 398 M 2 128 H 15 M 385 128 H 398';
            }

            function updatePreview() {
                const target = el('previewTarget');
                const p = el('primaryColor').value;
                const s = el('secondaryColor').value;
                const glow = Number(el('glowStrength').value);
                const speed = Number(el('animationSpeed').value);
                const style = el('borderStyle').value;
                const anim = el('animationType').value;
                const targetArea = el('targetArea').value;
                el('glowValue').textContent = glow + '%';
                el('speedValue').textContent = speed.toFixed(1) + 's';
                target.style.setProperty('--p', p);
                target.style.setProperty('--s', s);
                target.style.setProperty('--glow', (glow / 100).toFixed(2));
                target.style.setProperty('--speed', speed + 's');
                target.classList.toggle('profile-target', previewMode === 'profile');
                target.innerHTML = previewMode === 'profile' ?
                    '<div class="sample-content"><div class="sample-avatar"><i class="bi bi-person-fill"></i></div><div class="sample-sub mt-2">STUDENT PROFILE</div></div>' :
                    '<div class="sample-content"><div class="sample-brand"><i class="bi bi-cpu me-2"></i>ETS-ASYNC</div><div class="sample-sub">Learning Portal · Interface Preview</div></div>';

                const svgNS = 'http://www.w3.org/2000/svg';
                const svg = document.createElementNS(svgNS, 'svg');
                svg.setAttribute('class', 'border-svg');
                svg.setAttribute('viewBox', '0 0 400 200');
                svg.setAttribute('preserveAspectRatio', 'none');
                const defs = document.createElementNS(svgNS, 'defs');
                const filter = document.createElementNS(svgNS, 'filter');
                filter.setAttribute('id', 'glowFilter');
                filter.setAttribute('x', '-50%');
                filter.setAttribute('y', '-50%');
                filter.setAttribute('width', '200%');
                filter.setAttribute('height', '200%');
                const blur = document.createElementNS(svgNS, 'feGaussianBlur');
                blur.setAttribute('stdDeviation', String(1 + glow / 18));
                blur.setAttribute('result', 'blur');
                const merge = document.createElementNS(svgNS, 'feMerge');
                const n1 = document.createElementNS(svgNS, 'feMergeNode');
                n1.setAttribute('in', 'blur');
                const n2 = document.createElementNS(svgNS, 'feMergeNode');
                n2.setAttribute('in', 'SourceGraphic');
                merge.append(n1, n2);
                filter.append(blur, merge);
                defs.append(filter);
                svg.append(defs);

                const base = document.createElementNS(svgNS, 'path');
                base.setAttribute('d', borderPath(style));
                base.setAttribute('fill', 'none');
                base.setAttribute('stroke', s);
                base.setAttribute('stroke-width', style === 'orbit' ? '1.5' : '1.2');
                base.setAttribute('opacity', '.7');
                base.setAttribute('vector-effect', 'non-scaling-stroke');
                svg.append(base);

                const light = document.createElementNS(svgNS, 'path');
                light.setAttribute('d', borderPath(style));
                light.setAttribute('fill', 'none');
                light.setAttribute('stroke', p);
                light.setAttribute('stroke-width', style === 'corners' ? '3' : '2.5');
                light.setAttribute('stroke-linecap', 'round');
                light.setAttribute('stroke-dasharray', style === 'corners' ? '34 520' : '42 620');
                light.setAttribute('filter', 'url(#glowFilter)');
                light.setAttribute('opacity', String(.25 + glow / 135));
                light.setAttribute('vector-effect', 'non-scaling-stroke');
                if (anim === 'travel') light.innerHTML = '<animate attributeName="stroke-dashoffset" from="0" to="-1240" dur="' + speed + 's" repeatCount="indefinite"/>';
                else if (anim === 'pulse') light.innerHTML = '<animate attributeName="opacity" values=".25;1;.25" dur="' + speed + 's" repeatCount="indefinite"/>';
                else if (anim === 'rotate') light.innerHTML = '<animate attributeName="stroke-dashoffset" from="0" to="-500" dur="' + speed + 's" repeatCount="indefinite"/>';
                else {
                    light.setAttribute('stroke-dasharray', 'none');
                    light.setAttribute('opacity', String(.3 + glow / 145));
                }
                svg.append(light);
                target.append(svg);
                el('previewCaption').textContent = ({
                    hud: 'Futuristic HUD',
                    hexagon: 'Hexagon frame',
                    neon: 'Neon frame',
                    corners: 'Corner brackets',
                    orbit: 'Orbit ring'
                } [style]) + ' · ' + targetArea + ' · ' + ({
                    travel: 'Light travels around border',
                    pulse: 'Pulsing glow',
                    rotate: 'Rotating dash pattern',
                    static: 'Static border'
                } [anim]);
            }

            function loadData(data) {
                el('presetId').value = data.id || 0;
                el('presetName').value = data.name;
                el('targetArea').value = data.target;
                el('borderStyle').value = data.style;
                el('primaryColor').value = data.primary;
                el('secondaryColor').value = data.secondary;
                el('glowStrength').value = data.glow;
                el('animationSpeed').value = data.speed;
                el('animationType').value = data.animation;
                el('enabled').checked = Boolean(Number(data.enabled || 0));
                updatePreview();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
            ['presetName', 'targetArea', 'borderStyle', 'primaryColor', 'secondaryColor', 'glowStrength', 'animationSpeed', 'animationType'].forEach(id => {
                el(id).addEventListener('input', updatePreview);
                el(id).addEventListener('change', updatePreview);
            });
            el('resetBtn').addEventListener('click', () => loadData({
                id: 0,
                name: defaults.presetName,
                target: defaults.targetArea,
                style: defaults.borderStyle,
                primary: defaults.primaryColor,
                secondary: defaults.secondaryColor,
                glow: defaults.glowStrength,
                speed: defaults.animationSpeed,
                animation: defaults.animationType,
                enabled: 0
            }));
            el('topbarDemo').addEventListener('click', () => {
                previewMode = 'topbar';
                updatePreview();
            });
            el('profileDemo').addEventListener('click', () => {
                previewMode = 'profile';
                updatePreview();
            });
            document.querySelectorAll('.load-preset').forEach(btn => btn.addEventListener('click', () => loadData({
                id: btn.dataset.id,
                name: btn.dataset.name,
                target: btn.dataset.target,
                style: btn.dataset.style,
                primary: btn.dataset.primary,
                secondary: btn.dataset.secondary,
                glow: btn.dataset.glow,
                speed: btn.dataset.speed,
                animation: btn.dataset.animation,
                enabled: btn.dataset.enabled
            })));
            document.querySelectorAll('[data-premade]').forEach(btn => btn.addEventListener('click', () => {
                const choices = {
                    hud: {
                        name: 'Neon HUD Cyan',
                        target: 'both',
                        style: 'hud',
                        primary: '#00e5ff',
                        secondary: '#5b7cff',
                        glow: 75,
                        speed: 3,
                        animation: 'travel'
                    },
                    hexagon: {
                        name: 'Hex Tech Blue',
                        target: 'profile',
                        style: 'hexagon',
                        primary: '#39a9ff',
                        secondary: '#8c5cff',
                        glow: 85,
                        speed: 4,
                        animation: 'rotate'
                    },
                    corners: {
                        name: 'Tactical Corners',
                        target: 'topbar',
                        style: 'corners',
                        primary: '#61ffb4',
                        secondary: '#00bcd4',
                        glow: 50,
                        speed: 2.5,
                        animation: 'pulse'
                    }
                };
                loadData({
                    ...choices[btn.dataset.premade],
                    id: 0,
                    enabled: 0
                });
            }));
            updatePreview();
        })();
    </script>
</body>

</html>