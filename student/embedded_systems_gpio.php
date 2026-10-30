<?php

/* =========================================================
   C CONDITIONAL STATEMENTS REFERENCE
   ELECTRICAL ENGINEERING EXAMPLES
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
    <?php include 'globals/sidebar.php'; ?>
    <?php include 'globals/topbar.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h2>General Purpose Input / Output (GPIO)</h2>
                    <p>Understand GPIO operation, digital logic levels, and voltage behavior through embedded-system examples.</p>
                </div>
            </div>

            <div class="theory-card">
                <div class="theory-header">
                    <div>
                        <h4>Introduction to GPIO</h4>
                        <p>Configurable microcontroller pins for communicating with external circuits.</p>
                    </div>
                    <div class="theory-badge"><i class="bi bi-cpu"></i> Embedded Systems</div>
                </div>
                <div class="theory-body">
                    <p>General Purpose Input/Output (GPIO) refers to configurable pins on a microcontroller. A pin can be configured as an <strong>input</strong> to read an external logic signal or as an <strong>output</strong> to drive a logic signal to an external circuit.</p>
                    <p>GPIO pins are commonly used with LEDs, switches, push buttons, sensors, relays (through a suitable driver), and other digital devices.</p>
                    <div class="concept-box"><strong>Basic idea</strong><span>INPUT: the microcontroller reads a logic level. OUTPUT: the microcontroller drives a pin LOW or HIGH.</span></div>
                </div>
            </div>

            <div class="theory-card">
                <div class="theory-header">
                    <div>
                        <h4>1. GPIO Operating Modes</h4>
                        <p>A GPIO pin must be configured for its intended function.</p>
                    </div>
                    <div class="statement-number">MODES</div>
                </div>
                <div class="theory-body">
                    <div class="definition-box"><strong>Explanation</strong><span>Common modes include input, output, alternate function, and analog. Exact modes and configuration registers depend on the microcontroller family.</span></div>
                    <div class="application-grid">
                        <div class="application-item"><i class="bi bi-box-arrow-in-down"></i><strong>Input</strong><span>Reads an external digital signal, such as a push button.</span></div>
                        <div class="application-item"><i class="bi bi-box-arrow-up"></i><strong>Output</strong><span>Drives a digital LOW or HIGH logic signal.</span></div>
                        <div class="application-item"><i class="bi bi-arrow-left-right"></i><strong>Alternate Function</strong><span>Connects a pin to UART, SPI, I²C, timers, or other peripherals.</span></div>
                        <div class="application-item"><i class="bi bi-activity"></i><strong>Analog</strong><span>Connects a pin to an ADC or DAC peripheral, when supported.</span></div>
                        <div class="application-item"><i class="bi bi-diagram-2"></i><strong>Pull-up</strong><span>Holds an input HIGH through an internal or external resistor when undriven.</span></div>
                        <div class="application-item"><i class="bi bi-diagram-2-fill"></i><strong>Pull-down</strong><span>Holds an input LOW through an internal or external resistor when undriven.</span></div>
                    </div>
                </div>
            </div>

            <div class="theory-card">
                <div class="theory-header">
                    <div>
                        <h4>2. Digital Logic Levels</h4>
                        <p>Standard GPIO outputs normally provide two logic states.</p>
                    </div>
                    <div class="statement-number">LOW / HIGH</div>
                </div>
                <div class="theory-body">
                    <div class="example-grid">
                        <div class="example-description"><strong>Logic LOW</strong>
                            <p>A LOW output is near 0 V and represents binary <code>0</code>.</p><strong>Logic HIGH</strong>
                            <p>A HIGH output is near the pin's logic supply voltage, commonly 3.3 V or 5 V, and represents binary <code>1</code>.</p>
                        </div>
                        <div class="code-box">
                            <pre><code>// Generic C-style GPIO example
// Replace with your MCU's actual GPIO API.
gpio_configure_output(GPIO_PIN_5);
gpio_write(GPIO_PIN_5, GPIO_LOW);  // Logic 0
gpio_write(GPIO_PIN_5, GPIO_HIGH); // Logic 1</code></pre>
                        </div>
                    </div>
                    <div class="result-box"><strong>Important</strong><span>GPIO HIGH does not automatically mean 12 V. The HIGH voltage is determined by the microcontroller's design and supply. Always check the datasheet for allowed pin voltage, output current, and absolute maximum ratings.</span></div>
                </div>
            </div>

            <div class="theory-card">
                <div class="theory-header">
                    <div>
                        <h4>3. GPIO Voltage Visualizer</h4>
                        <p>Adjust the simulated voltage from 0 V to 12 V and observe the chart.</p>
                    </div>
                    <div class="statement-number">INTERACTIVE</div>
                </div>
                <div class="theory-body">
                    <div class="visualizer-layout">
                        <div class="visualizer-controls">
                            <label for="gpioPin">Select GPIO pin</label><select id="gpioPin">
                                <option>GPIO 0</option>
                                <option>GPIO 1</option>
                                <option>GPIO 2</option>
                                <option>GPIO 3</option>
                                <option>GPIO 4</option>
                                <option selected>GPIO 5</option>
                                <option>GPIO 6</option>
                                <option>GPIO 7</option>
                            </select>
                            <label for="gpioVoltage">Set simulated voltage</label>
                            <div class="voltage-readout"><span id="voltageValue">3.3</span><small>V</small></div><input type="range" id="gpioVoltage" min="0" max="12" step="0.1" value="3.3">
                            <div class="range-labels"><span>0 V</span><span>12 V</span></div>
                            <div class="preset-buttons"><button type="button" class="preset-btn" data-voltage="0">0 V</button><button type="button" class="preset-btn" data-voltage="3.3">3.3 V</button><button type="button" class="preset-btn" data-voltage="5">5 V</button><button type="button" class="preset-btn" data-voltage="12">12 V</button></div>
                            <div class="visualizer-status"><span>Simulated pin state</span><strong id="gpioState">HIGH (SIMULATED)</strong></div>
                            <p class="small-note">Educational simulation only. This does not measure or drive a physical GPIO pin.</p>
                        </div>
                        <div class="chart-panel">
                            <div class="chart-heading">
                                <div><strong>GPIO voltage level</strong><span id="chartPinName">GPIO 5</span></div><strong class="chart-current"><span id="chartVoltage">3.3</span> V</strong>
                            </div>
                            <div class="chart-wrap"><canvas id="gpioChart" role="img" aria-label="Chart showing simulated voltage from zero to twelve volts"></canvas></div>
                            <div class="chart-scale"><span>0 V</span><span>3 V</span><span>6 V</span><span>9 V</span><span>12 V</span></div>
                            <div class="voltage-bar">
                                <div id="voltageBar"></div>
                            </div>
                            <p class="chart-caption">The chart scale represents the selected simulated voltage from 0 V to 12 V.</p>
                        </div>
                    </div>
                    <div class="result-box warning-box"><strong>Safety warning: never apply 12 V directly to a GPIO pin.</strong><span>The 0–12 V slider is only a visualization. Most microcontroller GPIO pins are designed for logic-level voltages such as 3.3 V or 5 V. Applying 12 V directly can permanently damage the device. To switch a 12 V load, use a suitable transistor or MOSFET driver and a separate 12 V supply. For a variable analog output, use a DAC or filtered PWM with an appropriate external driver/converter.</span></div>
                </div>
            </div>

            <div class="theory-card">
                <div class="theory-header">
                    <div>
                        <h4>4. Digital GPIO vs. Variable Voltage</h4>
                        <p>Understand the difference between digital outputs and analog control.</p>
                    </div>
                </div>
                <div class="theory-body">
                    <div class="comparison-wrapper">
                        <table class="comparison-table">
                            <thead>
                                <tr>
                                    <th>Method</th>
                                    <th>Output behavior</th>
                                    <th>Typical application</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Digital GPIO</td>
                                    <td>LOW or HIGH logic level</td>
                                    <td>LED control and digital enable signals</td>
                                </tr>
                                <tr>
                                    <td>PWM</td>
                                    <td>Rapid HIGH/LOW pulses; duty cycle controls average energy</td>
                                    <td>LED dimming and motor speed control with a driver</td>
                                </tr>
                                <tr>
                                    <td>DAC</td>
                                    <td>Variable analog voltage within its specified range</td>
                                    <td>Analog reference or signal generation</td>
                                </tr>
                                <tr>
                                    <td>External driver</td>
                                    <td>Interfaces a low-voltage controller to a higher-voltage load</td>
                                    <td>Switching or controlling a 12 V load</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="section-label">Example: controlling a 12 V load safely</div>
                    <div class="code-box">
                        <pre><code>// Generic embedded C-style example.
// The GPIO controls a transistor/MOSFET driver.
// The GPIO pin itself remains at logic voltage.
gpio_configure_output(LOAD_CONTROL_PIN);

void load_on(void) {
    gpio_write(LOAD_CONTROL_PIN, GPIO_HIGH);
}

void load_off(void) {
    gpio_write(LOAD_CONTROL_PIN, GPIO_LOW);
}

// The external driver switches the separate
// 12 V supply to the load. Never connect 12 V
// directly to the microcontroller GPIO pin.</code></pre>
                    </div>
                </div>
            </div>

            <div class="theory-card">
                <div class="theory-header">
                    <div>
                        <h4>5. Common GPIO Applications</h4>
                        <p>Examples in embedded and electrical systems.</p>
                    </div>
                </div>
                <div class="theory-body">
                    <div class="application-grid">
                        <div class="application-item"><i class="bi bi-lightbulb"></i><strong>LED Control</strong><span>Turn indicator LEDs on and off through a current-limiting resistor.</span></div>
                        <div class="application-item"><i class="bi bi-toggle-on"></i><strong>Switch Reading</strong><span>Detect whether a push button or switch is open or closed.</span></div>
                        <div class="application-item"><i class="bi bi-fan"></i><strong>Motor Interface</strong><span>Send control signals to a motor driver circuit.</span></div>
                        <div class="application-item"><i class="bi bi-bell"></i><strong>Alarm Systems</strong><span>Activate buzzers or alarm-driver circuits.</span></div>
                        <div class="application-item"><i class="bi bi-broadcast"></i><strong>Communication</strong><span>Support UART, SPI, and I²C peripheral functions.</span></div>
                        <div class="application-item"><i class="bi bi-speedometer2"></i><strong>Sensor Interface</strong><span>Read digital status outputs from sensors and modules.</span></div>
                    </div>
                </div>
            </div>

            <div class="reference-note">
                <div class="reference-note-title"><i class="bi bi-lightbulb"></i> Key Points to Remember</div>
                <div class="key-point-grid">
                    <div><strong>01</strong><span>GPIO means General Purpose Input/Output.</span></div>
                    <div><strong>02</strong><span>Input mode reads an external logic signal.</span></div>
                    <div><strong>03</strong><span>Output mode drives a LOW or HIGH logic level.</span></div>
                    <div><strong>04</strong><span>The allowed HIGH voltage depends on the microcontroller.</span></div>
                    <div><strong>05</strong><span>PWM and DAC are different methods for producing controlled signals.</span></div>
                    <div><strong>06</strong><span>Never apply 12 V directly to a GPIO pin unless the datasheet explicitly rates it for that voltage.</span></div>
                </div>
            </div>
        </div>
    </main>

    <style>
        :root {
            --theory-bg: #fff;
            --theory-border: #e4e7ec;
            --theory-text: #172033;
            --theory-muted: #667085;
            --theory-secondary: #475467;
            --theory-hover: #f8fafc;
            --theory-blue: #2563eb;
            --theory-green: #16a34a;
            --theory-red: #dc2626;
            --theory-header: #f8fafc
        }

        html[data-theme="dark"] {
            --theory-bg: #151922;
            --theory-border: #2a3140;
            --theory-text: #f1f5f9;
            --theory-muted: #a8b0bf;
            --theory-secondary: #c2c9d3;
            --theory-hover: #1c2230;
            --theory-blue: #60a5fa;
            --theory-green: #4ade80;
            --theory-red: #f87171;
            --theory-header: #1c2230
        }

        .theory-card,
        .reference-note {
            margin-bottom: 16px;
            background: var(--theory-bg);
            border: 1px solid var(--theory-border);
            border-radius: 10px;
            overflow: hidden
        }

        .theory-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 17px 19px;
            border-bottom: 1px solid var(--theory-border)
        }

        .theory-header h4 {
            margin: 0 0 4px;
            color: var(--theory-text);
            font-size: .92rem;
            font-weight: 700
        }

        .theory-header p {
            margin: 0;
            color: var(--theory-muted);
            font-size: .68rem
        }

        .theory-badge,
        .statement-number {
            padding: 6px 9px;
            border-radius: 5px;
            background: var(--theory-hover);
            color: var(--theory-blue);
            font-size: .62rem;
            font-weight: 800;
            white-space: nowrap
        }

        .theory-body {
            padding: 19px
        }

        .theory-body p {
            margin: 0 0 13px;
            color: var(--theory-secondary);
            font-family: "Times New Roman", serif;
            font-size: .82rem;
            line-height: 1.7
        }

        .theory-body strong {
            color: var(--theory-text)
        }

        .concept-box,
        .definition-box {
            padding: 14px 16px;
            border-radius: 7px;
            background: var(--theory-hover)
        }

        .concept-box {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 16px;
            border-left: 4px solid var(--theory-blue)
        }

        .concept-box strong,
        .definition-box strong {
            font-size: .73rem
        }

        .concept-box span,
        .definition-box span {
            color: var(--theory-muted);
            font-family: "Times New Roman", serif;
            font-size: .79rem;
            line-height: 1.7
        }

        .definition-box {
            margin-bottom: 18px;
            border: 1px solid var(--theory-border)
        }

        .definition-box strong {
            display: block;
            margin-bottom: 6px;
            color: var(--theory-blue)
        }

        .code-box {
            padding: 16px 18px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-hover);
            overflow-x: auto
        }

        .code-box pre {
            margin: 0;
            color: var(--theory-text);
            font-family: "Courier New", monospace;
            font-size: .72rem;
            line-height: 1.7
        }

        .example-grid {
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            gap: 14px;
            margin-top: 10px
        }

        .example-description {
            padding: 15px 16px;
            border: 1px solid var(--theory-border);
            border-radius: 7px;
            background: var(--theory-hover)
        }

        .example-description strong {
            display: block;
            margin-bottom: 6px;
            color: var(--theory-blue);
            font-size: .72rem
        }

        .example-description p {
            margin-bottom: 12px;
            font-size: .77rem
        }

        .result-box {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 14px;
            padding: 13px 16px;
            border-left: 4px solid var(--theory-green);
            border-radius: 5px;
            background: rgba(22, 163, 74, .07)
        }

        .result-box strong {
            color: var(--theory-green);
            font-size: .72rem
        }

        .result-box span {
            color: var(--theory-secondary);
            font-family: "Times New Roman", serif;
            font-size: .77rem;
            line-height: 1.6
        }

        .warning-box {
            border-left-color: var(--theory-red);
            background: rgba(220, 38, 38, .06)
        }

        .warning-box strong {
            color: var(--theory-red)
        }

        .application-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr)
        }

        .application-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 18px;
            border-right: 1px solid var(--theory-border);
            border-bottom: 1px solid var(--theory-border)
        }

        .application-item:nth-child(3n) {
            border-right: 0
        }

        .application-item:nth-last-child(-n+3) {
            border-bottom: 0
        }

        .application-item i {
            color: var(--theory-blue);
            font-size: 1.1rem
        }

        .application-item strong {
            font-size: .72rem
        }

        .application-item span {
            color: var(--theory-muted);
            font-family: "Times New Roman", serif;
            font-size: .74rem;
            line-height: 1.5
        }

        .comparison-wrapper {
            overflow-x: auto
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse
        }

        .comparison-table th {
            padding: 11px 15px;
            background: var(--theory-header);
            border-bottom: 1px solid var(--theory-border);
            color: var(--theory-secondary);
            font-size: .67rem;
            text-align: left
        }

        .comparison-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--theory-border);
            color: var(--theory-text);
            font-family: "Times New Roman", serif;
            font-size: .76rem;
            line-height: 1.5
        }

        .comparison-table td:first-child {
            font-family: "Courier New", monospace;
            font-weight: 700
        }

        .reference-note-title {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 17px;
            border-bottom: 1px solid var(--theory-border);
            color: var(--theory-text);
            font-size: .8rem;
            font-weight: 700
        }

        .reference-note-title i {
            color: var(--theory-blue)
        }

        .key-point-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr)
        }

        .key-point-grid>div {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 14px 16px;
            border-right: 1px solid var(--theory-border);
            border-bottom: 1px solid var(--theory-border)
        }

        .key-point-grid>div:nth-child(3n) {
            border-right: 0
        }

        .key-point-grid>div:nth-last-child(-n+3) {
            border-bottom: 0
        }

        .key-point-grid strong {
            color: var(--theory-blue);
            font-size: .67rem
        }

        .key-point-grid span {
            color: var(--theory-muted);
            font-family: "Times New Roman", serif;
            font-size: .74rem;
            line-height: 1.5
        }

        .visualizer-layout {
            display: grid;
            grid-template-columns: minmax(230px, .75fr) minmax(0, 1.5fr);
            gap: 18px
        }

        .visualizer-controls,
        .chart-panel {
            padding: 17px;
            border: 1px solid var(--theory-border);
            border-radius: 9px;
            background: var(--theory-hover)
        }

        .visualizer-controls label {
            display: block;
            margin: 0 0 7px;
            color: var(--theory-text);
            font-size: .72rem;
            font-weight: 700
        }

        .visualizer-controls select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--theory-border);
            border-radius: 6px;
            background: var(--theory-bg);
            color: var(--theory-text);
            margin-bottom: 17px
        }

        .voltage-readout {
            display: flex;
            align-items: baseline;
            gap: 5px;
            margin: 5px 0
        }

        .voltage-readout span {
            font-size: 2rem;
            font-weight: 800;
            color: var(--theory-blue);
            font-variant-numeric: tabular-nums
        }

        .voltage-readout small {
            color: var(--theory-muted);
            font-weight: 700
        }

        #gpioVoltage {
            width: 100%;
            accent-color: var(--theory-blue);
            cursor: pointer
        }

        .range-labels,
        .chart-scale {
            display: flex;
            justify-content: space-between;
            color: var(--theory-muted);
            font-size: .64rem
        }

        .preset-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 15px 0
        }

        .preset-btn {
            padding: 7px 10px;
            border: 1px solid var(--theory-border);
            border-radius: 5px;
            background: var(--theory-bg);
            color: var(--theory-text);
            font-size: .68rem;
            cursor: pointer
        }

        .preset-btn:hover {
            border-color: var(--theory-blue);
            color: var(--theory-blue)
        }

        .visualizer-status {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 15px;
            padding: 12px;
            border-radius: 6px;
            background: var(--theory-bg);
            border: 1px solid var(--theory-border)
        }

        .visualizer-status span {
            color: var(--theory-muted);
            font-size: .65rem
        }

        .visualizer-status strong {
            color: var(--theory-green);
            font-size: .78rem
        }

        .small-note {
            margin: 10px 0 0 !important;
            font-size: .68rem !important;
            color: var(--theory-muted) !important
        }

        .chart-heading {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px
        }

        .chart-heading div {
            display: flex;
            flex-direction: column;
            gap: 3px
        }

        .chart-heading strong {
            color: var(--theory-text);
            font-size: .8rem
        }

        .chart-heading span {
            color: var(--theory-muted);
            font-size: .66rem
        }

        .chart-heading .chart-current {
            color: var(--theory-blue);
            font-size: 1rem;
            white-space: nowrap
        }

        .chart-wrap {
            height: 230px;
            position: relative
        }

        #gpioChart {
            width: 100%;
            height: 100%;
            display: block
        }

        .chart-scale {
            margin-top: 4px
        }

        .voltage-bar {
            height: 12px;
            margin-top: 12px;
            border-radius: 20px;
            background: var(--theory-border);
            overflow: hidden
        }

        .voltage-bar div {
            height: 100%;
            width: 27.5%;
            background: var(--theory-blue);
            border-radius: 20px;
            transition: width .15s ease
        }

        .chart-caption {
            margin: 9px 0 0 !important;
            font-size: .68rem !important;
            color: var(--theory-muted) !important
        }

        @media(max-width:950px) {

            .example-grid,
            .visualizer-layout {
                grid-template-columns: 1fr
            }

            .application-grid {
                grid-template-columns: repeat(2, 1fr)
            }

            .application-item:nth-child(3n) {
                border-right: 1px solid var(--theory-border)
            }

            .application-item:nth-child(2n) {
                border-right: 0
            }

            .application-item:nth-last-child(-n+3) {
                border-bottom: 1px solid var(--theory-border)
            }

            .application-item:nth-last-child(-n+2) {
                border-bottom: 0
            }

            .key-point-grid {
                grid-template-columns: repeat(2, 1fr)
            }

            .key-point-grid>div:nth-child(3n) {
                border-right: 1px solid var(--theory-border)
            }

            .key-point-grid>div:nth-child(2n) {
                border-right: 0
            }

            .key-point-grid>div:nth-last-child(-n+3) {
                border-bottom: 1px solid var(--theory-border)
            }

            .key-point-grid>div:nth-last-child(-n+2) {
                border-bottom: 0
            }
        }

        @media(max-width:650px) {
            .theory-header {
                align-items: flex-start;
                flex-direction: column
            }

            .application-grid,
            .key-point-grid {
                grid-template-columns: 1fr
            }

            .application-item,
            .application-item:nth-child(2n),
            .application-item:nth-child(3n),
            .key-point-grid>div,
            .key-point-grid>div:nth-child(2n),
            .key-point-grid>div:nth-child(3n) {
                border-right: 0;
                border-bottom: 1px solid var(--theory-border)
            }

            .application-item:last-child,
            .key-point-grid>div:last-child {
                border-bottom: 0
            }
        }
    </style>
    <script>
        (() => {
            const slider = document.getElementById('gpioVoltage'),
                pin = document.getElementById('gpioPin'),
                value = document.getElementById('voltageValue'),
                chartValue = document.getElementById('chartVoltage'),
                pinName = document.getElementById('chartPinName'),
                state = document.getElementById('gpioState'),
                bar = document.getElementById('voltageBar'),
                canvas = document.getElementById('gpioChart'),
                ctx = canvas.getContext('2d');

            function draw(v) {
                const r = canvas.getBoundingClientRect(),
                    d = window.devicePixelRatio || 1;
                canvas.width = Math.max(1, r.width * d);
                canvas.height = Math.max(1, r.height * d);
                ctx.setTransform(d, 0, 0, d, 0, 0);
                const w = r.width,
                    h = r.height,
                    L = 43,
                    R = 14,
                    T = 14,
                    B = 27,
                    pw = w - L - R,
                    ph = h - T - B,
                    css = getComputedStyle(document.documentElement),
                    muted = css.getPropertyValue('--theory-muted').trim() || '#667085',
                    grid = css.getPropertyValue('--theory-border').trim() || '#ddd',
                    blue = css.getPropertyValue('--theory-blue').trim() || '#2563eb',
                    txt = css.getPropertyValue('--theory-text').trim() || '#172033';
                ctx.clearRect(0, 0, w, h);
                ctx.font = '11px Arial';
                ctx.textAlign = 'right';
                for (let n = 0; n <= 12; n += 3) {
                    let y = T + ph - (n / 12) * ph;
                    ctx.strokeStyle = grid;
                    ctx.beginPath();
                    ctx.moveTo(L, y);
                    ctx.lineTo(w - R, y);
                    ctx.stroke();
                    ctx.fillStyle = muted;
                    ctx.fillText(n + ' V', L - 8, y + 4)
                }
                let y = T + ph - (v / 12) * ph;
                ctx.strokeStyle = blue;
                ctx.lineWidth = 3;
                ctx.beginPath();
                ctx.moveTo(L, y);
                ctx.lineTo(w - R, y);
                ctx.stroke();
                ctx.fillStyle = blue;
                ctx.beginPath();
                ctx.arc(w - R, y, 5, 0, Math.PI * 2);
                ctx.fill();
                ctx.fillStyle = txt;
                ctx.textAlign = 'center';
                ctx.fillText('Simulated voltage level', L + pw / 2, h - 5)
            }

            function update() {
                let v = Number(slider.value),
                    s = v.toFixed(1);
                value.textContent = s;
                chartValue.textContent = s;
                pinName.textContent = pin.value;
                state.textContent = v < 0.05 ? 'LOW (SIMULATED)' : 'HIGH / LEVEL (SIMULATED)';
                bar.style.width = (v / 12 * 100) + '%';
                draw(v)
            }
            slider.addEventListener('input', update);
            pin.addEventListener('change', update);
            document.querySelectorAll('.preset-btn').forEach(b => b.addEventListener('click', () => {
                slider.value = b.dataset.voltage;
                update()
            }));
            window.addEventListener('resize', update);
            new MutationObserver(update).observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['data-theme']
            });
            update();
        })();
    </script>
    <?php include 'globals/scripts.php'; ?>
</body>

</html>