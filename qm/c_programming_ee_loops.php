<?php

/* =========================================================
   C LOOPS REFERENCE
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
                        Introduction to Loops in C
                    </h2>

                    <p>
                        Understanding repetition and iterative
                        control structures through electrical
                        engineering applications.
                    </p>

                </div>

            </div>


            <!-- =================================================
             INTRODUCTION TO LOOPS
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            Introduction to Loops in C
                        </h4>

                        <p>
                            Repeating a block of instructions while
                            a specified condition is satisfied.
                        </p>

                    </div>

                    <div class="theory-badge">

                        <i class="bi bi-arrow-repeat"></i>

                        Iterative Control

                    </div>

                </div>


                <div class="theory-body">

                    <p>

                        In C programming, a loop is a control
                        structure that allows a program to execute
                        a block of statements repeatedly.

                    </p>


                    <p>

                        Instead of writing the same instructions
                        multiple times, a loop allows the programmer
                        to specify the conditions under which the
                        instructions should be repeated.

                    </p>


                    <p>

                        Loops are especially useful in electrical
                        engineering applications involving sensor
                        monitoring, signal processing, numerical
                        calculations, data acquisition, motor control,
                        and repeated measurements.

                    </p>


                    <div class="concept-box">

                        <strong>
                            Basic idea
                        </strong>

                        <span>

                            INITIALIZE a value, CHECK a condition,
                            EXECUTE instructions, UPDATE the value,
                            and REPEAT until the condition becomes
                            false.

                        </span>

                    </div>

                </div>

            </div>


            <!-- =================================================
             WHY LOOPS ARE IMPORTANT
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            Why Use Loops?
                        </h4>

                        <p>
                            Reducing repetitive code and automating
                            repeated engineering operations.
                        </p>

                    </div>

                </div>


                <div class="theory-body">


                    <div class="definition-box">

                        <strong>
                            Explanation
                        </strong>

                        <span>

                            Without loops, a programmer would need
                            to write the same statements repeatedly.
                            Loops make programs shorter, easier to
                            maintain, and suitable for operations
                            that must be performed many times.

                        </span>

                    </div>


                    <div class="application-grid">


                        <div class="application-item">

                            <i class="bi bi-activity"></i>

                            <strong>
                                Sensor Monitoring
                            </strong>

                            <span>
                                Continuously read voltage, current,
                                temperature, or other sensor values.
                            </span>

                        </div>


                        <div class="application-item">

                            <i class="bi bi-calculator"></i>

                            <strong>
                                Engineering Calculations
                            </strong>

                            <span>
                                Repeat mathematical calculations
                                for multiple values or iterations.
                            </span>

                        </div>


                        <div class="application-item">

                            <i class="bi bi-soundwave"></i>

                            <strong>
                                Signal Processing
                            </strong>

                            <span>
                                Process multiple samples of a
                                discrete-time signal.
                            </span>

                        </div>


                        <div class="application-item">

                            <i class="bi bi-cpu"></i>

                            <strong>
                                Embedded Systems
                            </strong>

                            <span>
                                Repeatedly execute control logic
                                in a microcontroller system.
                            </span>

                        </div>


                        <div class="application-item">

                            <i class="bi bi-speedometer2"></i>

                            <strong>
                                Motor Control
                            </strong>

                            <span>
                                Monitor motor operating parameters
                                during repeated control cycles.
                            </span>

                        </div>


                        <div class="application-item">

                            <i class="bi bi-database"></i>

                            <strong>
                                Data Acquisition
                            </strong>

                            <span>
                                Collect multiple measurements from
                                electrical instrumentation systems.
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             FOR LOOP
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            1. for Loop
                        </h4>

                        <p>
                            Used when the number of repetitions or
                            iteration pattern is known.
                        </p>

                    </div>

                    <div class="statement-number">
                        FOR
                    </div>

                </div>


                <div class="theory-body">


                    <div class="definition-box">

                        <strong>
                            Explanation
                        </strong>

                        <span>

                            The <code>for</code> loop is commonly used
                            when a program needs to repeat a block of
                            statements a known number of times.

                            It contains three main expressions:
                            initialization, condition, and update.

                        </span>

                    </div>


                    <!-- SYNTAX -->

                    <div class="section-label">

                        Syntax

                    </div>


                    <div class="code-box">

                        <pre><code>for (initialization; condition; update) {

    // statements

}</code></pre>

                    </div>


                    <!-- COMPONENTS -->

                    <div class="section-label">

                        Components of a for Loop

                    </div>


                    <div class="mini-flow">


                        <div class="mini-flow-item">

                            <span>
                                Initialization
                            </span>

                            <strong>
                                int i = 0
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
                                i &lt; 5
                            </strong>

                        </div>


                        <div class="mini-arrow">
                            →
                        </div>


                        <div class="mini-flow-item">

                            <span>
                                Update
                            </span>

                            <strong>
                                i++
                            </strong>

                        </div>

                    </div>


                    <!-- ELECTRICAL EXAMPLE -->

                    <div class="section-label">

                        Electrical Engineering Example:
                        Voltage Measurement Samples

                    </div>


                    <div class="example-grid">


                        <div class="example-description">

                            <strong>
                                Problem
                            </strong>

                            <p>

                                A data acquisition system needs to
                                process five voltage measurements.

                            </p>


                            <strong>
                                Decision
                            </strong>

                            <p>

                                A <code>for</code> loop is appropriate
                                because the number of measurements
                                is already known.

                            </p>

                        </div>


                        <div class="code-box">

                            <pre><code>#include &lt;stdio.h&gt;

int main() {

    float voltage;

    for (int i = 1; i &lt;= 5; i++) {

        voltage = 10.0 + i * 0.5;

        printf("Sample %d: %.2f V\n",
               i, voltage);

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

                            The loop executes five times.
                            Each iteration generates and displays
                            one voltage measurement.

                        </span>

                    </div>


                    <!-- ITERATION FLOW -->

                    <div class="iteration-flow">


                        <div class="iteration-item">

                            <div class="iteration-number">
                                1
                            </div>

                            <div>

                                <strong>
                                    Sample 1
                                </strong>

                                <span>
                                    10.50 V
                                </span>

                            </div>

                        </div>


                        <div class="iteration-item">

                            <div class="iteration-number">
                                2
                            </div>

                            <div>

                                <strong>
                                    Sample 2
                                </strong>

                                <span>
                                    11.00 V
                                </span>

                            </div>

                        </div>


                        <div class="iteration-item">

                            <div class="iteration-number">
                                3
                            </div>

                            <div>

                                <strong>
                                    Sample 3
                                </strong>

                                <span>
                                    11.50 V
                                </span>

                            </div>

                        </div>


                        <div class="iteration-item">

                            <div class="iteration-number">
                                4
                            </div>

                            <div>

                                <strong>
                                    Sample 4
                                </strong>

                                <span>
                                    12.00 V
                                </span>

                            </div>

                        </div>


                        <div class="iteration-item success-iteration">

                            <div class="iteration-number">
                                5
                            </div>

                            <div>

                                <strong>
                                    Sample 5
                                </strong>

                                <span>
                                    12.50 V
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             WHILE LOOP
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            2. while Loop
                        </h4>

                        <p>
                            Repeats statements while a condition
                            remains true.
                        </p>

                    </div>

                    <div class="statement-number">
                        WHILE
                    </div>

                </div>


                <div class="theory-body">


                    <div class="definition-box">

                        <strong>
                            Explanation
                        </strong>

                        <span>

                            The <code>while</code> loop evaluates its
                            condition before executing the loop body.

                            If the condition is true, the statements
                            are executed. The condition is then
                            evaluated again.

                            If the condition is false from the
                            beginning, the loop body may execute
                            zero times.

                        </span>

                    </div>


                    <!-- SYNTAX -->

                    <div class="section-label">

                        Syntax

                    </div>


                    <div class="code-box">

                        <pre><code>while (condition) {

    // statements

}</code></pre>

                    </div>


                    <!-- ELECTRICAL EXAMPLE -->

                    <div class="section-label">

                        Electrical Engineering Example:
                        Battery Voltage Monitoring

                    </div>


                    <div class="example-grid">


                        <div class="example-description">

                            <strong>
                                Problem
                            </strong>

                            <p>

                                A battery monitoring system repeatedly
                                checks battery voltage while the
                                voltage remains above the minimum
                                operating level.

                            </p>


                            <strong>
                                Decision
                            </strong>

                            <p>

                                The monitoring operation continues
                                while the battery voltage remains
                                greater than or equal to 10.5 V.

                            </p>

                        </div>


                        <div class="code-box">

                            <pre><code>#include &lt;stdio.h&gt;

int main() {

    float batteryVoltage = 12.6;

    while (batteryVoltage &gt;= 10.5) {

        printf("Battery: %.2f V\n",
               batteryVoltage);

        batteryVoltage -= 0.5;

    }

    printf("Battery voltage is LOW.\n");

    return 0;
}</code></pre>

                        </div>

                    </div>


                    <div class="result-box">

                        <strong>
                            What happens?
                        </strong>

                        <span>

                            The program repeatedly displays the
                            battery voltage and decreases the
                            simulated voltage by 0.5 V after
                            each iteration.

                            Once the voltage becomes lower than
                            10.5 V, the loop terminates.

                        </span>

                    </div>


                    <!-- DECISION FLOW -->

                    <div class="decision-flow">


                        <div class="decision-item">

                            <span>
                                batteryVoltage &gt;= 10.5
                            </span>

                            <strong>
                                TRUE
                            </strong>

                        </div>


                        <div class="decision-arrow">
                            ↓
                        </div>


                        <div class="decision-item active-decision">

                            <span>
                                Display battery voltage
                            </span>

                            <strong>
                                EXECUTE
                            </strong>

                        </div>


                        <div class="decision-arrow">
                            ↓
                        </div>


                        <div class="decision-item">

                            <span>
                                Decrease voltage
                            </span>

                            <strong>
                                UPDATE
                            </strong>

                        </div>


                        <div class="decision-arrow">
                            ↓
                        </div>


                        <div class="decision-item">

                            <span>
                                Condition becomes false
                            </span>

                            <strong>
                                STOP
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             DO WHILE LOOP
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            3. do - while Loop
                        </h4>

                        <p>
                            Executes the loop body at least once
                            before checking the condition.
                        </p>

                    </div>

                    <div class="statement-number">
                        DO / WHILE
                    </div>

                </div>


                <div class="theory-body">


                    <div class="definition-box">

                        <strong>
                            Explanation
                        </strong>

                        <span>

                            The <code>do-while</code> loop is different
                            from the <code>while</code> loop because
                            its condition is evaluated after the
                            loop body has executed.

                            Therefore, the statements inside the
                            <code>do</code> block are guaranteed to
                            execute at least once.

                        </span>

                    </div>


                    <!-- SYNTAX -->

                    <div class="section-label">

                        Syntax

                    </div>


                    <div class="code-box">

                        <pre><code>do {

    // statements

} while (condition);</code></pre>

                    </div>


                    <!-- ELECTRICAL EXAMPLE -->

                    <div class="section-label">

                        Electrical Engineering Example:
                        Sensor Calibration

                    </div>


                    <div class="example-grid">


                        <div class="example-description">

                            <strong>
                                Problem
                            </strong>

                            <p>

                                A sensor calibration routine takes
                                at least one measurement before
                                determining whether another
                                calibration measurement is needed.

                            </p>


                            <strong>
                                Decision
                            </strong>

                            <p>

                                The calibration measurement must
                                occur at least once. Additional
                                measurements are taken while the
                                measured error remains above the
                                acceptable limit.

                            </p>

                        </div>


                        <div class="code-box">

                            <pre><code>#include &lt;stdio.h&gt;

int main() {

    float error = 2.5;
    int sample = 1;

    do {

        printf("Calibration sample %d\n",
               sample);

        printf("Error: %.2f%%\n",
               error);

        error -= 0.8;
        sample++;

    } while (error &gt; 1.0);

    printf("Calibration complete.\n");

    return 0;
}</code></pre>

                        </div>

                    </div>


                    <div class="result-box">

                        <strong>
                            What happens?
                        </strong>

                        <span>

                            The calibration routine executes once
                            before the condition is evaluated.

                            If the error remains greater than
                            1.0%, another calibration measurement
                            is performed.

                            The loop stops when the error becomes
                            1.0% or lower.

                        </span>

                    </div>


                    <!-- DO WHILE FLOW -->

                    <div class="nested-flow">


                        <div class="nested-step">

                            <div class="nested-number">
                                1
                            </div>

                            <div>

                                <strong>
                                    Execute calibration
                                </strong>

                                <span>
                                    The loop body executes first
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
                                    Check error
                                </strong>

                                <span>
                                    error &gt; 1.0
                                </span>

                            </div>

                        </div>


                        <div class="nested-arrow">
                            ↓
                        </div>


                        <div class="nested-step">

                            <div class="nested-number">
                                3
                            </div>

                            <div>

                                <strong>
                                    Repeat if necessary
                                </strong>

                                <span>
                                    Condition determines whether
                                    another iteration occurs
                                </span>

                            </div>

                        </div>


                        <div class="nested-arrow">
                            ↓
                        </div>


                        <div class="nested-step success-step">

                            <div class="nested-number">
                                4
                            </div>

                            <div>

                                <strong>
                                    Calibration complete
                                </strong>

                                <span>
                                    Error is within the acceptable
                                    range
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             WHILE VS DO WHILE
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            while vs. do-while
                        </h4>

                        <p>
                            Understanding the difference between
                            pre-test and post-test loops.
                        </p>

                    </div>

                </div>


                <div class="comparison-wrapper">

                    <table class="comparison-table">

                        <thead>

                            <tr>

                                <th>
                                    Feature
                                </th>

                                <th>
                                    while
                                </th>

                                <th>
                                    do-while
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>

                                <td>
                                    Condition
                                </td>

                                <td>
                                    Checked before execution.
                                </td>

                                <td>
                                    Checked after execution.
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    Minimum Executions
                                </td>

                                <td>
                                    Zero times.
                                </td>

                                <td>
                                    At least once.
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    Loop Type
                                </td>

                                <td>
                                    Pre-test loop.
                                </td>

                                <td>
                                    Post-test loop.
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    Typical Use
                                </td>

                                <td>
                                    Continue while a condition
                                    remains valid.
                                </td>

                                <td>
                                    Perform an operation at least
                                    once before deciding whether
                                    to repeat it.
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    Engineering Example
                                </td>

                                <td>
                                    Continuous battery monitoring.
                                </td>

                                <td>
                                    Sensor calibration.
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
             LOOP COMPARISON
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            Comparing the Loop Structures
                        </h4>

                        <p>
                            Selecting the appropriate loop for
                            an engineering application.
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
                                    <code>for</code>
                                </td>

                                <td>
                                    Repeat a known number of times
                                    or iterate through a known range.
                                </td>

                                <td>
                                    Process five voltage samples.
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    <code>while</code>
                                </td>

                                <td>
                                    Continue repeating while a
                                    condition remains true.
                                </td>

                                <td>
                                    Monitor battery voltage.
                                </td>

                            </tr>


                            <tr>

                                <td>
                                    <code>do-while</code>
                                </td>

                                <td>
                                    Execute the operation at least
                                    once before checking the
                                    continuation condition.
                                </td>

                                <td>
                                    Perform sensor calibration.
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
             LOOP CONTROL
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            Loop Control Statements
                        </h4>

                        <p>
                            Additional statements used to control
                            loop execution.
                        </p>

                    </div>

                </div>


                <div class="theory-body">


                    <div class="example-grid">


                        <div class="example-description">

                            <strong>
                                break
                            </strong>

                            <p>

                                The <code>break</code> statement
                                immediately terminates the loop.

                            </p>


                            <strong>
                                Electrical Application
                            </strong>

                            <p>

                                Stop monitoring when a critical
                                electrical condition is detected.

                            </p>

                        </div>


                        <div class="code-box">

                            <pre><code>for (int i = 0; i &lt; 10; i++) {

    if (voltage &gt; 15.0) {

        break;

    }

    printf("Monitoring...\n");

}</code></pre>

                        </div>

                    </div>


                    <div class="example-grid">


                        <div class="example-description">

                            <strong>
                                continue
                            </strong>

                            <p>

                                The <code>continue</code> statement
                                skips the remaining statements in
                                the current iteration and proceeds
                                to the next iteration.

                            </p>


                            <strong>
                                Electrical Application
                            </strong>

                            <p>

                                Skip an invalid sensor reading and
                                continue processing the remaining
                                measurements.

                            </p>

                        </div>


                        <div class="code-box">

                            <pre><code>for (int i = 0; i &lt; 10; i++) {

    if (sensorValue &lt; 0) {

        continue;

    }

    printf("Valid reading\n");

}</code></pre>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             COMMON APPLICATIONS
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            Common Electrical Engineering
                            Applications of Loops
                        </h4>

                        <p>
                            Repetitive operations commonly found
                            in engineering software and embedded
                            systems.
                        </p>

                    </div>

                </div>


                <div class="application-grid">


                    <div class="application-item">

                        <i class="bi bi-lightning-charge"></i>

                        <strong>
                            Voltage Sampling
                        </strong>

                        <span>
                            Repeatedly acquire and process voltage
                            measurements.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-activity"></i>

                        <strong>
                            Current Monitoring
                        </strong>

                        <span>
                            Continuously monitor current in a
                            protection or measurement system.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-thermometer-half"></i>

                        <strong>
                            Temperature Monitoring
                        </strong>

                        <span>
                            Repeatedly evaluate motor, transformer,
                            or semiconductor temperature.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-soundwave"></i>

                        <strong>
                            Signal Processing
                        </strong>

                        <span>
                            Process individual samples of a
                            discrete-time signal.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-cpu"></i>

                        <strong>
                            Microcontroller Control
                        </strong>

                        <span>
                            Execute repeated control operations
                            in embedded systems.
                        </span>

                    </div>


                    <div class="application-item">

                        <i class="bi bi-database"></i>

                        <strong>
                            Data Acquisition
                        </strong>

                        <span>
                            Collect and process multiple
                            measurements from instruments.
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

                            A loop repeatedly executes a block
                            of statements while following its
                            specified control conditions.

                        </span>

                    </div>


                    <div>

                        <strong>
                            02
                        </strong>

                        <span>

                            The <code>for</code> loop is commonly
                            used when the iteration pattern or
                            number of repetitions is known.

                        </span>

                    </div>


                    <div>

                        <strong>
                            03
                        </strong>

                        <span>

                            The <code>while</code> loop checks its
                            condition before executing the loop body.

                        </span>

                    </div>


                    <div>

                        <strong>
                            04
                        </strong>

                        <span>

                            The <code>do-while</code> loop executes
                            its body at least once before checking
                            the condition.

                        </span>

                    </div>


                    <div>

                        <strong>
                            05
                        </strong>

                        <span>

                            The <code>break</code> statement can
                            terminate a loop immediately.

                        </span>

                    </div>


                    <div>

                        <strong>
                            06
                        </strong>

                        <span>

                            Loops are fundamental to sensor
                            monitoring, data acquisition, signal
                            processing, and embedded control.

                        </span>

                    </div>

                </div>

            </div>


        </div>

    </main>


    <!-- =========================================================
     LOOP STYLES
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


        .mini-arrow {

            color:
                var(--theory-muted);

            font-size: 1rem;

        }


        /* =====================================================
           ITERATION FLOW
        ====================================================== */

        .iteration-flow {

            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 8px;

            margin-top: 17px;

        }


        .iteration-item {

            display: flex;

            align-items: center;

            gap: 9px;

            padding:
                11px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

        }


        .iteration-number {

            display: flex;

            align-items: center;

            justify-content: center;

            min-width: 27px;

            height: 27px;

            border-radius: 50%;

            background:
                var(--theory-blue);

            color:
                #ffffff;

            font-size: .65rem;

            font-weight: 800;

        }


        .iteration-item strong {

            display: block;

            margin-bottom: 2px;

            font-size: .67rem;

        }


        .iteration-item span {

            color:
                var(--theory-muted);

            font-family:
                "Courier New",
                monospace;

            font-size: .61rem;

        }


        .success-iteration {

            border-color:
                var(--theory-green);

        }


        .success-iteration .iteration-number {

            background:
                var(--theory-green);

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

            text-align:
                center;

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

            text-align:
                center;

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


            .iteration-flow {

                grid-template-columns:
                    repeat(3, 1fr);

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


            .iteration-flow {

                grid-template-columns:
                    1fr;

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