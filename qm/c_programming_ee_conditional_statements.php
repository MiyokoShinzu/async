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
                        C Conditional Statements
                    </h2>

                    <p>
                        Understanding if, if-else, else-if, and
                        nested if statements through electrical
                        engineering applications.
                    </p>

                </div>

            </div>


            <!-- =================================================
             INTRODUCTION
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            Conditional Statements in C
                        </h4>

                        <p>
                            Decision-making structures used to
                            control program execution.
                        </p>

                    </div>

                    <div class="theory-badge">

                        <i class="bi bi-diagram-3"></i>

                        Program Control

                    </div>

                </div>


                <div class="theory-body">

                    <p>

                        In C programming, conditional statements
                        allow a program to make decisions based
                        on whether a condition is <strong>true</strong>
                        or <strong>false</strong>.

                    </p>


                    <p>

                        In electrical engineering applications,
                        conditional statements are useful for
                        monitoring voltage, current, temperature,
                        motor status, battery condition, sensor
                        measurements, and protection systems.

                    </p>


                    <div class="concept-box">

                        <strong>
                            Basic idea
                        </strong>

                        <span>

                            IF a specified electrical condition
                            occurs, THEN execute the corresponding
                            instructions.

                        </span>

                    </div>

                </div>

            </div>


            <!-- =================================================
             IF STATEMENT
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            1. if Statement
                        </h4>

                        <p>
                            Executes a block of code only when
                            a condition is true.
                        </p>

                    </div>

                    <div class="statement-number">
                        IF
                    </div>

                </div>


                <div class="theory-body">


                    <div class="definition-box">

                        <strong>
                            Explanation
                        </strong>

                        <span>

                            The <code>if</code> statement evaluates
                            a condition. If the condition evaluates
                            to a nonzero value, the statements inside
                            the <code>if</code> block are executed.

                            If the condition is false, the block is
                            skipped.

                        </span>

                    </div>


                    <!-- SYNTAX -->

                    <div class="section-label">

                        Syntax

                    </div>


                    <div class="code-box">


                        <pre><code>if (condition) {

    // statements

}</code></pre>


                    </div>


                    <!-- ELECTRICAL EXAMPLE -->

                    <div class="section-label">

                        Electrical Engineering Example:
                        Overvoltage Detection

                    </div>


                    <div class="example-grid">


                        <div class="example-description">

                            <strong>
                                Problem
                            </strong>

                            <p>

                                A power supply should be monitored
                                to determine whether its output
                                voltage exceeds the safe operating
                                limit of 12 V.

                            </p>


                            <strong>
                                Decision
                            </strong>

                            <p>

                                If the measured voltage is greater
                                than 12 V, the program displays an
                                overvoltage warning.

                            </p>

                        </div>


                        <div class="code-box">


                            <pre><code>#include &lt;stdio.h&gt;

int main() {

    float voltage = 13.2;

    if (voltage &gt; 12.0) {

        printf("WARNING: Overvoltage detected.\n");

    }

    return 0;
}</code></pre>


                        </div>

                    </div>


                    <div class="result-box">

                        <strong>
                            What happens?
                        </strong>

                        <span>

                            Since 13.2 V is greater than 12 V,
                            the condition is true and the warning
                            message is displayed.

                        </span>

                    </div>

                </div>

            </div>


            <!-- =================================================
             IF ELSE
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            2. if - else Statement
                        </h4>

                        <p>
                            Selects between two possible execution
                            paths.
                        </p>

                    </div>

                    <div class="statement-number">
                        IF / ELSE
                    </div>

                </div>


                <div class="theory-body">


                    <div class="definition-box">

                        <strong>
                            Explanation
                        </strong>

                        <span>

                            An <code>if-else</code> statement provides
                            two alternatives. The <code>if</code>
                            block executes when the condition is true;
                            otherwise, the <code>else</code> block
                            executes.

                        </span>

                    </div>


                    <!-- SYNTAX -->

                    <div class="section-label">

                        Syntax

                    </div>


                    <div class="code-box">


                        <pre><code>if (condition) {

    // statements when true

}
else {

    // statements when false

}</code></pre>


                    </div>


                    <!-- ELECTRICAL EXAMPLE -->

                    <div class="section-label">

                        Electrical Engineering Example:
                        Battery Status

                    </div>


                    <div class="example-grid">


                        <div class="example-description">

                            <strong>
                                Problem
                            </strong>

                            <p>

                                A battery monitoring system checks
                                whether the battery voltage is at
                                least 11.5 V.

                            </p>


                            <strong>
                                Decision
                            </strong>

                            <p>

                                If the voltage is at least 11.5 V,
                                the battery is considered acceptable.
                                Otherwise, the program reports a
                                low-battery condition.

                            </p>

                        </div>


                        <div class="code-box">


                            <pre><code>#include &lt;stdio.h&gt;

int main() {

    float batteryVoltage = 11.8;

    if (batteryVoltage &gt;= 11.5) {

        printf("Battery voltage is acceptable.\n");

    }
    else {

        printf("LOW BATTERY.\n");

    }

    return 0;
}</code></pre>


                        </div>

                    </div>


                    <div class="result-box">

                        <strong>
                            What happens?
                        </strong>

                        <span>

                            Since 11.8 V is greater than or equal
                            to 11.5 V, the first branch executes.

                        </span>

                    </div>


                    <!-- FLOW -->

                    <div class="mini-flow">

                        <div class="mini-flow-item">

                            <span>
                                Battery Voltage
                            </span>

                            <strong>
                                11.8 V
                            </strong>

                        </div>


                        <div class="mini-arrow">
                            →
                        </div>


                        <div class="mini-flow-item">

                            <span>
                                Condition
                            </span>

                            <strong>
                                ≥ 11.5 V
                            </strong>

                        </div>


                        <div class="mini-arrow">
                            →
                        </div>


                        <div class="mini-flow-item success-flow">

                            <span>
                                Result
                            </span>

                            <strong>
                                ACCEPTABLE
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             ELSE IF
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            3. if - else if - else Statement
                        </h4>

                        <p>
                            Tests multiple conditions sequentially.
                        </p>

                    </div>

                    <div class="statement-number">
                        ELSE IF
                    </div>

                </div>


                <div class="theory-body">


                    <div class="definition-box">

                        <strong>
                            Explanation
                        </strong>

                        <span>

                            The <code>else if</code> structure is used
                            when more than two possible conditions
                            must be considered.

                            C evaluates the conditions from top to
                            bottom. Once a true condition is found,
                            its block is executed and the remaining
                            conditions are skipped.

                        </span>

                    </div>


                    <!-- SYNTAX -->

                    <div class="section-label">

                        Syntax

                    </div>


                    <div class="code-box">


                        <pre><code>if (condition1) {

    // first condition

}
else if (condition2) {

    // second condition

}
else {

    // all conditions are false

}</code></pre>


                    </div>


                    <!-- ELECTRICAL EXAMPLE -->

                    <div class="section-label">

                        Electrical Engineering Example:
                        Motor Temperature Monitoring

                    </div>


                    <div class="example-grid">


                        <div class="example-description">

                            <strong>
                                Problem
                            </strong>

                            <p>

                                A motor monitoring system measures
                                the winding temperature.

                            </p>


                            <p>

                                The system uses three operating
                                classifications:

                            </p>


                            <ul>

                                <li>
                                    Below 60°C → Normal
                                </li>

                                <li>
                                    60°C to 80°C → Warning
                                </li>

                                <li>
                                    Above 80°C → Overheating
                                </li>

                            </ul>

                        </div>


                        <div class="code-box">


                            <pre><code>#include &lt;stdio.h&gt;

int main() {

    float temperature = 72.5;

    if (temperature &lt; 60) {

        printf("Motor temperature: NORMAL\n");

    }
    else if (temperature &lt;= 80) {

        printf("Motor temperature: WARNING\n");

    }
    else {

        printf("Motor temperature: OVERHEATING\n");

    }

    return 0;
}</code></pre>


                        </div>

                    </div>


                    <div class="result-box">

                        <strong>
                            Example result: 72.5°C
                        </strong>

                        <span>

                            The first condition is false because
                            72.5°C is not below 60°C.

                            The second condition is true because
                            72.5°C is less than or equal to 80°C.

                            Therefore, the WARNING branch executes.

                        </span>

                    </div>


                    <!-- DECISION FLOW -->

                    <div class="decision-flow">

                        <div class="decision-item">

                            <span>
                                temperature &lt; 60
                            </span>

                            <strong>
                                FALSE
                            </strong>

                        </div>


                        <div class="decision-arrow">
                            ↓
                        </div>


                        <div class="decision-item active-decision">

                            <span>
                                temperature &lt;= 80
                            </span>

                            <strong>
                                TRUE
                            </strong>

                        </div>


                        <div class="decision-arrow">
                            ↓
                        </div>


                        <div class="decision-item">

                            <span>
                                Remaining else
                            </span>

                            <strong>
                                SKIPPED
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             NESTED IF
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            4. Nested if Statement
                        </h4>

                        <p>
                            Places one conditional statement inside
                            another conditional statement.
                        </p>

                    </div>

                    <div class="statement-number">
                        NESTED IF
                    </div>

                </div>


                <div class="theory-body">


                    <div class="definition-box">

                        <strong>
                            Explanation
                        </strong>

                        <span>

                            A nested <code>if</code> statement occurs
                            when one <code>if</code> statement is placed
                            inside another.

                            This is useful when a second decision
                            should only be evaluated after the first
                            condition has been satisfied.

                        </span>

                    </div>


                    <!-- SYNTAX -->

                    <div class="section-label">

                        Syntax

                    </div>


                    <div class="code-box">


                        <pre><code>if (condition1) {

    if (condition2) {

        // nested condition is true

    }

}</code></pre>


                    </div>


                    <!-- ELECTRICAL EXAMPLE -->

                    <div class="section-label">

                        Electrical Engineering Example:
                        Motor Protection System

                    </div>


                    <div class="example-grid">


                        <div class="example-description">

                            <strong>
                                Problem
                            </strong>

                            <p>

                                A motor protection controller monitors
                                both motor current and temperature.

                            </p>


                            <p>

                                The motor may continue operating only
                                when:

                            </p>


                            <ul>

                                <li>
                                    Current is within the allowable limit
                                </li>

                                <li>
                                    Temperature is also within the
                                    safe operating range
                                </li>

                            </ul>


                            <p>

                                The temperature is checked only after
                                the current has been determined to be
                                safe.

                            </p>

                        </div>


                        <div class="code-box">


                            <pre><code>#include &lt;stdio.h&gt;

int main() {

    float current = 8.5;
    float temperature = 65.0;

    if (current &lt;= 10.0) {

        if (temperature &lt;= 80.0) {

            printf("Motor operation is SAFE.\n");

        }
        else {

            printf("Motor temperature is HIGH.\n");

        }

    }
    else {

        printf("Motor overcurrent detected.\n");

    }

    return 0;
}</code></pre>


                        </div>

                    </div>


                    <div class="result-box">

                        <strong>
                            Example result
                        </strong>

                        <span>

                            The current is 8.5 A, which is within the
                            10 A limit. Therefore, C proceeds to the
                            nested temperature condition.

                            Since 65°C is below the 80°C limit, the
                            motor is classified as SAFE.

                        </span>

                    </div>


                    <!-- NESTED FLOW -->

                    <div class="nested-flow">

                        <div class="nested-step">

                            <div class="nested-number">
                                1
                            </div>

                            <div>

                                <strong>
                                    Check motor current
                                </strong>

                                <span>
                                    current &lt;= 10.0
                                </span>

                            </div>

                        </div>


                        <div class="nested-arrow">
                            ↓
                        </div>


                        <div class="nested-step">

                            <div class="nested-number">
                                2
                            </div>

                            <div>

                                <strong>
                                    Check temperature
                                </strong>

                                <span>
                                    temperature &lt;= 80.0
                                </span>

                            </div>

                        </div>


                        <div class="nested-arrow">
                            ↓
                        </div>


                        <div class="nested-step success-step">

                            <div class="nested-number">
                                3
                            </div>

                            <div>

                                <strong>
                                    Allow motor operation
                                </strong>

                                <span>
                                    Both safety conditions satisfied
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             COMPARISON
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            Comparing the Conditional Structures
                        </h4>

                        <p>
                            Choosing the appropriate decision structure.
                        </p>

                    </div>

                </div>


                <div class="comparison-wrapper">

                    <table class="comparison-table">

                        <thead>

                            <tr>

                                <th>
                                    Structure
                                </th>

                                <th>
                                    Purpose
                                </th>

                                <th>
                                    Electrical Example
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>

                                <td>
                                    <code>if</code>
                                </td>

                                <td>
                                    Execute an action only when
                                    one condition is true.
                                </td>

                                <td>
                                    Detect overvoltage.
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    <code>if - else</code>
                                </td>

                                <td>
                                    Choose between two alternatives.
                                </td>

                                <td>
                                    Battery acceptable or low.
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    <code>else if</code>
                                </td>

                                <td>
                                    Choose among multiple conditions.
                                </td>

                                <td>
                                    Motor temperature classification.
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    Nested <code>if</code>
                                </td>

                                <td>
                                    Evaluate a second decision only
                                    after a previous condition is met.
                                </td>

                                <td>
                                    Motor current and temperature
                                    protection.
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
             ELECTRICAL ENGINEERING APPLICATIONS
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            Common Electrical Engineering Applications
                        </h4>

                        <p>
                            Examples of where conditional statements
                            appear in engineering programs.
                        </p>

                    </div>

                </div>


                <div class="application-grid">


                    <div class="application-item">

                        <i class="bi bi-lightning-charge"></i>

                        <strong>
                            Voltage Protection
                        </strong>

                        <span>
                            Detect undervoltage and overvoltage
                            conditions.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-activity"></i>

                        <strong>
                            Current Monitoring
                        </strong>

                        <span>
                            Detect overcurrent and overload
                            conditions.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-thermometer-half"></i>

                        <strong>
                            Thermal Protection
                        </strong>

                        <span>
                            Monitor motor, transformer, or
                            semiconductor temperature.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-battery-half"></i>

                        <strong>
                            Battery Management
                        </strong>

                        <span>
                            Classify battery voltage and determine
                            charging or protection actions.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-cpu"></i>

                        <strong>
                            Embedded Control
                        </strong>

                        <span>
                            Make decisions based on sensor inputs
                            and system states.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-speedometer2"></i>

                        <strong>
                            Motor Control
                        </strong>

                        <span>
                            Determine whether a motor should run,
                            stop, or enter a protection state.
                        </span>

                    </div>

                </div>

            </div>


            <!-- =================================================
             KEY POINTS
        ================================================== -->

            <div class="reference-note">

                <div class="reference-note-title">

                    <i class="bi bi-lightbulb"></i>

                    Key Points to Remember

                </div>


                <div class="key-point-grid">

                    <div>
                        <strong>
                            01
                        </strong>

                        <span>
                            <code>if</code> performs an action only
                            when its condition is true.
                        </span>
                    </div>


                    <div>
                        <strong>
                            02
                        </strong>

                        <span>
                            <code>if-else</code> provides two
                            alternative execution paths.
                        </span>
                    </div>


                    <div>
                        <strong>
                            03
                        </strong>

                        <span>
                            <code>else if</code> allows several
                            conditions to be evaluated in sequence.
                        </span>
                    </div>


                    <div>
                        <strong>
                            04
                        </strong>

                        <span>
                            A nested <code>if</code> allows one
                            decision to depend on another decision.
                        </span>
                    </div>


                    <div>
                        <strong>
                            05
                        </strong>

                        <span>
                            In C, zero represents false while a
                            nonzero value represents true.
                        </span>
                    </div>


                    <div>
                        <strong>
                            06
                        </strong>

                        <span>
                            Conditional statements are fundamental
                            to protection, monitoring, and control
                            systems.
                        </span>
                    </div>

                </div>

            </div>


        </div>

    </main>


    <!-- =========================================================
     CONDITIONAL STATEMENT STYLES
========================================================== -->

    <style>
        /* =====================================================
       THEME VARIABLES
    ====================================================== */

        :root {

            --theory-bg: #ffffff;

            --theory-border: #e4e7ec;

            --theory-input-border: #d0d5dd;

            --theory-text: #172033;

            --theory-muted: #667085;

            --theory-secondary: #475467;

            --theory-hover: #f8fafc;

            --theory-panel: #ffffff;

            --theory-blue: #2563eb;

            --theory-green: #16a34a;

            --theory-red: #dc2626;

            --theory-header: #f8fafc;

        }


        /* =====================================================
       DARK MODE
    ====================================================== */

        html[data-theme="dark"] {

            --theory-bg: #151922;

            --theory-border: #2a3140;

            --theory-input-border: #3a4252;

            --theory-text: #f1f5f9;

            --theory-muted: #a8b0bf;

            --theory-secondary: #c2c9d3;

            --theory-hover: #1c2230;

            --theory-panel: #151922;

            --theory-blue: #60a5fa;

            --theory-green: #4ade80;

            --theory-red: #f87171;

            --theory-header: #1c2230;

        }


        /* =====================================================
       THEORY CARD
    ====================================================== */

        .theory-card {

            margin-bottom: 16px;

            background:
                var(--theory-bg);

            border:
                1px solid var(--theory-border);

            border-radius: 10px;

            overflow: hidden;

        }


        .theory-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding:
                17px 19px;

            border-bottom:
                1px solid var(--theory-border);

        }


        .theory-header h4 {

            margin:
                0 0 4px;

            color:
                var(--theory-text);

            font-size: .92rem;

            font-weight: 700;

        }


        .theory-header p {

            margin: 0;

            color:
                var(--theory-muted);

            font-size: .68rem;

        }


        .theory-badge,
        .statement-number {

            padding:
                6px 9px;

            border-radius: 5px;

            background:
                var(--theory-hover);

            color:
                var(--theory-blue);

            font-size: .62rem;

            font-weight: 800;

            white-space: nowrap;

        }


        .theory-badge {

            display: flex;

            align-items: center;

            gap: 5px;

        }


        /* =====================================================
       THEORY BODY
    ====================================================== */

        .theory-body {

            padding: 19px;

        }


        .theory-body p {

            margin:
                0 0 13px;

            color:
                var(--theory-secondary);

            font-family:
                "Times New Roman",
                serif;

            font-size: .82rem;

            line-height: 1.7;

        }


        .theory-body strong {

            color:
                var(--theory-text);

        }


        /* =====================================================
       CONCEPT BOX
    ====================================================== */

        .concept-box {

            display: flex;

            flex-direction: column;

            gap: 5px;

            margin-top: 16px;

            padding: 14px 16px;

            border-left:
                4px solid var(--theory-blue);

            border-radius: 5px;

            background:
                var(--theory-hover);

        }


        .concept-box strong {

            font-size: .73rem;

        }


        .concept-box span {

            color:
                var(--theory-muted);

            font-family:
                "Times New Roman",
                serif;

            font-size: .78rem;

            line-height: 1.6;

        }


        /* =====================================================
       DEFINITION
    ====================================================== */

        .definition-box {

            margin-bottom: 18px;

            padding: 14px 16px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

        }


        .definition-box strong {

            display: block;

            margin-bottom: 6px;

            color:
                var(--theory-blue);

            font-size: .72rem;

        }


        .definition-box span {

            color:
                var(--theory-secondary);

            font-family:
                "Times New Roman",
                serif;

            font-size: .79rem;

            line-height: 1.7;

        }


        /* =====================================================
       SECTION LABEL
    ====================================================== */

        .section-label {

            margin:
                17px 0 8px;

            color:
                var(--theory-text);

            font-size: .73rem;

            font-weight: 700;

        }


        /* =====================================================
       CODE
    ====================================================== */

        .code-box {

            padding:
                16px 18px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

            overflow-x: auto;

        }


        .code-box pre {

            margin: 0;

            color:
                var(--theory-text);

            font-family:
                "Courier New",
                monospace;

            font-size: .72rem;

            line-height: 1.7;

        }


        .code-box code {

            font-family:
                "Courier New",
                monospace;

        }


        /* =====================================================
       EXAMPLE GRID
    ====================================================== */

        .example-grid {

            display: grid;

            grid-template-columns:
                .85fr 1.15fr;

            gap: 14px;

            margin-top: 10px;

        }


        .example-description {

            padding:
                15px 16px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

        }


        .example-description strong {

            display: block;

            margin-bottom: 6px;

            color:
                var(--theory-blue);

            font-size: .72rem;

        }


        .example-description p {

            margin-bottom: 12px;

            font-size: .77rem;

        }


        .example-description ul {

            margin:
                5px 0 0;

            padding-left: 19px;

            color:
                var(--theory-secondary);

            font-family:
                "Times New Roman",
                serif;

            font-size: .77rem;

            line-height: 1.7;

        }


        /* =====================================================
       RESULT BOX
    ====================================================== */

        .result-box {

            display: flex;

            flex-direction: column;

            gap: 5px;

            margin-top: 14px;

            padding:
                13px 16px;

            border-left:
                4px solid var(--theory-green);

            border-radius: 5px;

            background:
                rgba(22, 163, 74, .07);

        }


        .result-box strong {

            color:
                var(--theory-green);

            font-size: .72rem;

        }


        .result-box span {

            color:
                var(--theory-secondary);

            font-family:
                "Times New Roman",
                serif;

            font-size: .77rem;

            line-height: 1.6;

        }


        /* =====================================================
       MINI FLOW
    ====================================================== */

        .mini-flow {

            display: grid;

            grid-template-columns:
                1fr auto 1fr auto 1fr;

            align-items: center;

            gap: 8px;

            margin-top: 16px;

        }


        .mini-flow-item {

            padding:
                12px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

            text-align: center;

        }


        .mini-flow-item span {

            display: block;

            margin-bottom: 4px;

            color:
                var(--theory-muted);

            font-size: .62rem;

        }


        .mini-flow-item strong {

            font-family:
                "Courier New",
                monospace;

            font-size: .7rem;

        }


        .success-flow {

            border-color:
                var(--theory-green);

        }


        .success-flow strong {

            color:
                var(--theory-green);

        }


        .mini-arrow {

            color:
                var(--theory-muted);

            font-size: 1rem;

        }


        /* =====================================================
       DECISION FLOW
    ====================================================== */

        .decision-flow {

            margin-top: 16px;

        }


        .decision-item {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding:
                12px 15px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

        }


        .decision-item span {

            color:
                var(--theory-text);

            font-family:
                "Courier New",
                monospace;

            font-size: .69rem;

        }


        .decision-item strong {

            color:
                var(--theory-muted);

            font-size: .62rem;

        }


        .active-decision {

            border-color:
                var(--theory-green);

            background:
                rgba(22, 163, 74, .07);

        }


        .active-decision strong {

            color:
                var(--theory-green);

        }


        .decision-arrow {

            padding:
                4px 0;

            color:
                var(--theory-muted);

            text-align: center;

        }


        /* =====================================================
       NESTED FLOW
    ====================================================== */

        .nested-flow {

            margin-top: 17px;

        }


        .nested-step {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                13px 15px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

        }


        .nested-number {

            display: flex;

            align-items: center;

            justify-content: center;

            min-width: 28px;

            height: 28px;

            border-radius: 50%;

            background:
                var(--theory-blue);

            color:
                #ffffff;

            font-size: .67rem;

            font-weight: 800;

        }


        .nested-step strong {

            display: block;

            margin-bottom: 3px;

            color:
                var(--theory-text);

            font-size: .72rem;

        }


        .nested-step span {

            color:
                var(--theory-muted);

            font-family:
                "Courier New",
                monospace;

            font-size: .66rem;

        }


        .nested-arrow {

            padding:
                5px 0;

            color:
                var(--theory-muted);

            text-align: center;

        }


        .success-step {

            border-color:
                var(--theory-green);

            background:
                rgba(22, 163, 74, .07);

        }


        .success-step .nested-number {

            background:
                var(--theory-green);

        }


        /* =====================================================
       COMPARISON TABLE
    ====================================================== */

        .comparison-wrapper {

            overflow-x: auto;

        }


        .comparison-table {

            width: 100%;

            border-collapse: collapse;

        }


        .comparison-table th {

            padding:
                11px 15px;

            background:
                var(--theory-header);

            border-bottom:
                1px solid var(--theory-border);

            color:
                var(--theory-secondary);

            font-size: .67rem;

            text-align: left;

        }


        .comparison-table td {

            padding:
                12px 15px;

            border-bottom:
                1px solid var(--theory-border);

            color:
                var(--theory-text);

            font-family:
                "Times New Roman",
                serif;

            font-size: .76rem;

            line-height: 1.5;

        }


        .comparison-table td:first-child {

            width: 170px;

            font-family:
                "Courier New",
                monospace;

            font-weight: 700;

        }


        .comparison-table tr:hover td {

            background:
                var(--theory-hover);

        }


        /* =====================================================
       APPLICATION GRID
    ====================================================== */

        .application-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

        }


        .application-item {

            display: flex;

            flex-direction: column;

            gap: 6px;

            padding:
                18px;

            border-right:
                1px solid var(--theory-border);

            border-bottom:
                1px solid var(--theory-border);

        }


        .application-item:nth-child(3n) {

            border-right: none;

        }


        .application-item:nth-last-child(-n+3) {

            border-bottom: none;

        }


        .application-item i {

            color:
                var(--theory-blue);

            font-size: 1.1rem;

        }


        .application-item strong {

            font-size: .72rem;

        }


        .application-item span {

            color:
                var(--theory-muted);

            font-family:
                "Times New Roman",
                serif;

            font-size: .74rem;

            line-height: 1.5;

        }


        /* =====================================================
       REFERENCE NOTE
    ====================================================== */

        .reference-note {

            margin-bottom: 18px;

            background:
                var(--theory-bg);

            border:
                1px solid var(--theory-border);

            border-radius: 10px;

            overflow: hidden;

        }


        .reference-note-title {

            display: flex;

            align-items: center;

            gap: 8px;

            padding:
                14px 17px;

            border-bottom:
                1px solid var(--theory-border);

            color:
                var(--theory-text);

            font-size: .8rem;

            font-weight: 700;

        }


        .reference-note-title i {

            color:
                var(--theory-blue);

        }


        .key-point-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

        }


        .key-point-grid>div {

            display: flex;

            align-items: flex-start;

            gap: 10px;

            padding:
                14px 16px;

            border-right:
                1px solid var(--theory-border);

            border-bottom:
                1px solid var(--theory-border);

        }


        .key-point-grid>div:nth-child(3n) {

            border-right: none;

        }


        .key-point-grid>div:nth-last-child(-n+3) {

            border-bottom: none;

        }


        .key-point-grid strong {

            color:
                var(--theory-blue);

            font-size: .67rem;

        }


        .key-point-grid span {

            color:
                var(--theory-muted);

            font-family:
                "Times New Roman",
                serif;

            font-size: .74rem;

            line-height: 1.5;

        }


        /* =====================================================
       RESPONSIVE
    ====================================================== */

        @media (max-width: 950px) {

            .example-grid {

                grid-template-columns:
                    1fr;

            }


            .application-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .application-item:nth-child(3n) {

                border-right:
                    1px solid var(--theory-border);

            }


            .application-item:nth-child(2n) {

                border-right: none;

            }


            .application-item:nth-last-child(-n+3) {

                border-bottom:
                    1px solid var(--theory-border);

            }


            .application-item:nth-last-child(-n+2) {

                border-bottom: none;

            }


            .key-point-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .key-point-grid>div:nth-child(3n) {

                border-right:
                    1px solid var(--theory-border);

            }


            .key-point-grid>div:nth-child(2n) {

                border-right: none;

            }


            .key-point-grid>div:nth-last-child(-n+3) {

                border-bottom:
                    1px solid var(--theory-border);

            }


            .key-point-grid>div:nth-last-child(-n+2) {

                border-bottom: none;

            }

        }


        @media (max-width: 650px) {

            .theory-header {

                align-items:
                    flex-start;

                flex-direction:
                    column;

            }


            .mini-flow {

                grid-template-columns:
                    1fr;

            }


            .mini-arrow {

                transform:
                    rotate(90deg);

                text-align:
                    center;

            }


            .application-grid {

                grid-template-columns:
                    1fr;

            }


            .application-item,
            .application-item:nth-child(2n),
            .application-item:nth-child(3n) {

                border-right: none;

                border-bottom:
                    1px solid var(--theory-border);

            }


            .application-item:last-child {

                border-bottom: none;

            }


            .key-point-grid {

                grid-template-columns:
                    1fr;

            }


            .key-point-grid>div,
            .key-point-grid>div:nth-child(2n),
            .key-point-grid>div:nth-child(3n) {

                border-right: none;

                border-bottom:
                    1px solid var(--theory-border);

            }


            .key-point-grid>div:last-child {

                border-bottom: none;

            }

        }
    </style>


    <!-- =========================================================
     GLOBAL SCRIPTS
========================================================== -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>