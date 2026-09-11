
<?php

/* =========================================================
   PROJECTILE MOTION
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
   USER INFORMATION
========================================================== */

$user = $_SESSION["user"];

$firstName      = trim($user["first_name"] ?? "");
$lastName       = trim($user["last_name"] ?? "");
$middleInitial  = trim($user["middle_initial"] ?? "");
$extensionName  = trim($user["extension_name"] ?? "");

$fullName = trim(
    $firstName . " " .
    ($middleInitial !== "" ? $middleInitial . ". " : "") .
    $lastName .
    ($extensionName !== "" ? " " . $extensionName : "")
);

$initials = "";

if ($firstName !== "") {
    $initials .= strtoupper(substr($firstName, 0, 1));
}

if ($lastName !== "") {
    $initials .= strtoupper(substr($lastName, 0, 1));
}


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php include "globals/head.php"; ?>

    <style>

        /* =====================================================
           PAGE VARIABLES
        ====================================================== */

        :root {

            --primary: #0B4F8A;
            --primary-light: #EAF4FB;

            --text: #1F2937;
            --muted: #6B7280;

            --card-bg: #FFFFFF;
            --body-bg: #F5F7FA;

            --border: #E5E7EB;

            --formula-bg: #F8FAFC;

        }


        /* =====================================================
           DARK MODE VARIABLES
        ====================================================== */

        html[data-theme="dark"] {

            --primary: #5EA9E6;
            --primary-light: #102A43;

            --text: #E5E7EB;
            --muted: #9CA3AF;

            --card-bg: #111827;
            --body-bg: #0B1120;

            --border: #374151;

            --formula-bg: #1F2937;

        }


        /* =====================================================
           MAIN CONTENT
        ====================================================== */

        .content-wrapper {

            max-width: 1400px;
            margin: auto;
            padding-bottom: 40px;

        }


        /* =====================================================
           PAGE HEADER
        ====================================================== */

        .page-header {

            margin-bottom: 25px;

        }

        .page-header h1 {

            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;

        }

        .page-header p {

            color: var(--muted);
            margin-bottom: 0;
            line-height: 1.6;

        }


        /* =====================================================
           THEORY CARD
        ====================================================== */

        .theory-card {

            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 14px;

            padding: 25px;
            margin-bottom: 24px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.04);

        }


        .theory-card h2 {

            font-size: 20px;
            font-weight: 700;

            color: var(--primary);

            margin-bottom: 15px;

        }


        .theory-card h3 {

            font-size: 17px;
            font-weight: 700;

            color: var(--text);

            margin-top: 22px;
            margin-bottom: 10px;

        }


        .theory-card p {

            color: var(--text);

            line-height: 1.75;

            margin-bottom: 12px;

        }


        /* =====================================================
           FORMULA BOX
        ====================================================== */

        .formula-box {

            background: var(--formula-bg);

            border-left: 4px solid var(--primary);

            border-radius: 8px;

            padding: 18px 20px;

            margin: 15px 0;

            overflow-x: auto;

            text-align: center;

        }


        /* =====================================================
           STEP BOX
        ====================================================== */

        .step-box {

            border: 1px solid var(--border);

            border-radius: 10px;

            padding: 18px;

            margin-top: 15px;

            background: var(--card-bg);

        }


        .step-number {

            display: inline-flex;

            width: 32px;
            height: 32px;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: var(--primary);

            color: #FFFFFF;

            font-weight: 700;

            margin-right: 8px;

        }


        .step-title {

            font-weight: 700;

            color: var(--text);

        }


        /* =====================================================
           TABLE
        ====================================================== */

        .table-responsive {

            overflow-x: auto;

        }


        .reference-table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 15px;

            color: var(--text);

        }


        .reference-table th {

            background: var(--primary);

            color: #FFFFFF;

            padding: 12px;

            border: 1px solid var(--border);

            text-align: center;

            font-weight: 600;

        }


        .reference-table td {

            padding: 12px;

            border: 1px solid var(--border);

            vertical-align: middle;

        }


        .reference-table tbody tr:nth-child(even) {

            background: var(--formula-bg);

        }


        .reference-table td.center {

            text-align: center;

        }


        /* =====================================================
           HIGHLIGHT BOX
        ====================================================== */

        .highlight-box {

            background: var(--primary-light);

            border: 1px solid var(--primary);

            border-radius: 10px;

            padding: 18px;

            margin: 18px 0;

            color: var(--text);

        }


        .highlight-box strong {

            color: var(--primary);

        }


        /* =====================================================
           WARNING BOX
        ====================================================== */

        .warning-box {

            background: #FFF8E1;

            border-left: 4px solid #F59E0B;

            border-radius: 8px;

            padding: 16px 18px;

            margin: 18px 0;

            color: var(--text);

        }


        html[data-theme="dark"] .warning-box {

            background: #332B12;

        }


        /* =====================================================
           VARIABLE TABLE
        ====================================================== */

        .variable-symbol {

            font-family: "Times New Roman", serif;

            font-size: 18px;

            font-weight: 700;

        }


        /* =====================================================
           EQUATION BOX
        ====================================================== */

        .equation-box {

            background: var(--formula-bg);

            border: 1px solid var(--border);

            border-radius: 10px;

            padding: 20px;

            margin: 15px 0;

            overflow-x: auto;

        }


        /* =====================================================
           LIST
        ====================================================== */

        .theory-card ul {

            padding-left: 25px;

            color: var(--text);

        }


        .theory-card li {

            margin-bottom: 8px;

            line-height: 1.6;

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 768px) {

            .page-header h1 {

                font-size: 23px;

            }

            .theory-card {

                padding: 18px;

            }

            .reference-table {

                font-size: 14px;

            }

        }

    </style>

</head>


<body>


<?php include "globals/sidebar.php"; ?>


<div class="main-content">


    <?php include "globals/topbar.php"; ?>


    <div class="content-wrapper">


        <!-- =================================================
             PAGE HEADER
        ================================================== -->

        <div class="page-header">

            <h1>

                <i class="bi bi-bezier2"></i>

                Projectile Motion

            </h1>

            <p>

                A step-by-step reference for analyzing the motion of
                an object launched into the air under ideal gravitational
                acceleration.

            </p>

        </div>



        <!-- =================================================
             INTRODUCTION
        ================================================== -->

        <div class="theory-card">

            <h2>

                1. What is Projectile Motion?

            </h2>

            <p>

                <strong>Projectile motion</strong> is the motion of an
                object that is launched into the air and then moves under
                the influence of gravity, assuming air resistance is
                negligible.

            </p>

            <p>

                A projectile has two independent components of motion:

            </p>

            <ul>

                <li>

                    <strong>Horizontal motion</strong> — constant velocity

                </li>

                <li>

                    <strong>Vertical motion</strong> — constant
                    acceleration due to gravity

                </li>

            </ul>


            <div class="highlight-box">

                <strong>Key idea:</strong>

                Horizontal and vertical motions can be analyzed
                independently and then combined to describe the complete
                trajectory.

            </div>

        </div>



        <!-- =================================================
             INTERACTIVE VISUALIZATION
        ================================================== -->

        <div class="theory-card">

            <h2>

                2. Projectile Motion Visualization

            </h2>

            <p>

                Adjust the initial velocity and launch angle to observe
                how they affect the trajectory, maximum height, flight
                time, and horizontal range.

            </p>


            



        </div>



        <!-- =================================================
             ASSUMPTIONS
        ================================================== -->

        <div class="theory-card">

            <h2>

                3. Assumptions of Ideal Projectile Motion

            </h2>

            <p>

                The standard projectile-motion equations are based on
                several simplifying assumptions.

            </p>


            <div class="table-responsive">

                <table class="reference-table">

                    <thead>

                        <tr>

                            <th>Assumption</th>

                            <th>Description</th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr>

                            <td>

                                Gravity

                            </td>

                            <td>

                                Gravitational acceleration is constant.

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Air resistance

                            </td>

                            <td>

                                Air resistance is neglected.

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Earth rotation

                            </td>

                            <td>

                                Effects of Earth's rotation are neglected.

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Coordinate system

                            </td>

                            <td>

                                The horizontal axis is \(x\), and the
                                vertical axis is \(y\).

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <div class="formula-box">

                \[
                g \approx 9.81\ \text{m/s}^2
                \]

            </div>

        </div>



        <!-- =================================================
             INITIAL VELOCITY
        ================================================== -->

        <div class="theory-card">

            <h2>

                4. Resolving the Initial Velocity

            </h2>

            <p>

                Suppose an object is launched with an initial speed
                \(v_0\) at an angle \(\theta\) above the horizontal.

            </p>


            <div class="formula-box">

                \[
                \vec{v}_0
                =
                v_0\cos\theta\,\hat{i}
                +
                v_0\sin\theta\,\hat{j}
                \]

            </div>


            <p>

                Therefore, the initial velocity has two components.

            </p>


            <div class="table-responsive">

                <table class="reference-table">

                    <thead>

                        <tr>

                            <th>Component</th>

                            <th>Equation</th>

                            <th>Direction</th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr>

                            <td class="center">

                                Horizontal

                            </td>

                            <td class="center">

                                \[
                                v_{0x}=v_0\cos\theta
                                \]

                            </td>

                            <td class="center">

                                \(+x\)

                            </td>

                        </tr>


                        <tr>

                            <td class="center">

                                Vertical

                            </td>

                            <td class="center">

                                \[
                                v_{0y}=v_0\sin\theta
                                \]

                            </td>

                            <td class="center">

                                \(+y\)

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>



        <!-- =================================================
             HORIZONTAL MOTION
        ================================================== -->

        <div class="theory-card">

            <h2>

                5. Horizontal Motion

            </h2>

            <p>

                In ideal projectile motion, there is no horizontal
                acceleration.

            </p>


            <div class="formula-box">

                \[
                a_x=0
                \]

            </div>


            <p>

                Therefore, the horizontal velocity remains constant.

            </p>


            <div class="formula-box">

                \[
                v_x=v_0\cos\theta
                \]

            </div>


            <p>

                If the projectile starts at \(x_0\), its horizontal
                position after time \(t\) is

            </p>


            <div class="formula-box">

                \[
                x(t)
                =
                x_0+v_0\cos\theta\,t
                \]

            </div>


            <div class="highlight-box">

                <strong>Important:</strong>

                Gravity does not directly affect the horizontal velocity
                in the ideal projectile model.

            </div>

        </div>



        <!-- =================================================
             VERTICAL MOTION
        ================================================== -->

        <div class="theory-card">

            <h2>

                6. Vertical Motion

            </h2>

            <p>

                The vertical component is affected by gravitational
                acceleration.

            </p>


            <div class="formula-box">

                \[
                a_y=-g
                \]

            </div>


            <p>

                The vertical velocity at time \(t\) is

            </p>


            <div class="formula-box">

                \[
                v_y(t)
                =
                v_0\sin\theta-gt
                \]

            </div>


            <p>

                The vertical position is

            </p>


            <div class="formula-box">

                \[
                y(t)
                =
                y_0
                +
                v_0\sin\theta\,t
                -
                \frac{1}{2}gt^2
                \]

            </div>

        </div>



        <!-- =================================================
             POSITION EQUATIONS
        ================================================== -->

        <div class="theory-card">

            <h2>

                7. Position Equations

            </h2>


            <p>

                Combining the horizontal and vertical components gives
                the parametric equations of projectile motion.

            </p>


            <div class="formula-box">

                \[
                \boxed{
                x(t)=x_0+v_0\cos\theta\,t
                }
                \]

                \[
                \boxed{
                y(t)=y_0+v_0\sin\theta\,t-\frac{1}{2}gt^2
                }
                \]

            </div>


            <p>

                If the projectile starts at the origin,
                \(x_0=0\) and \(y_0=0\).

            </p>


            <div class="formula-box">

                \[
                x(t)=v_0\cos\theta\,t
                \]

                \[
                y(t)=v_0\sin\theta\,t-\frac{1}{2}gt^2
                \]

            </div>

        </div>



        <!-- =================================================
             MAXIMUM HEIGHT
        ================================================== -->

        <div class="theory-card">

            <h2>

                8. Maximum Height

            </h2>

            <p>

                At the highest point of the trajectory, the vertical
                velocity becomes zero.

            </p>


            <div class="formula-box">

                \[
                v_y=0
                \]

            </div>


            <p>

                Using

            </p>


            <div class="formula-box">

                \[
                v_y=v_0\sin\theta-gt
                \]

            </div>


            <p>

                we obtain the time required to reach maximum height:

            </p>


            <div class="formula-box">

                \[
                \boxed{
                t_H
                =
                \frac{v_0\sin\theta}{g}
                }
                \]

            </div>


            <p>

                The maximum height above the launch point is

            </p>


            <div class="formula-box">

                \[
                \boxed{
                H
                =
                \frac{v_0^2\sin^2\theta}{2g}
                }
                \]

            </div>

        </div>



        <!-- =================================================
             TIME OF FLIGHT
        ================================================== -->

        <div class="theory-card">

            <h2>

                9. Time of Flight

            </h2>

            <p>

                When the projectile lands at the same vertical height
                from which it was launched,

            </p>


            <div class="formula-box">

                \[
                y_0=y_f
                \]

            </div>


            <p>

                The total flight time is

            </p>


            <div class="formula-box">

                \[
                \boxed{
                T
                =
                \frac{2v_0\sin\theta}{g}
                }
                \]

            </div>


            <div class="warning-box">

                <strong>Condition:</strong>

                This equation assumes that the projectile lands at the
                same height at which it was launched.

                For different launch and landing heights, the vertical
                position equation must be solved directly.

            </div>

        </div>



        <!-- =================================================
             HORIZONTAL RANGE
        ================================================== -->

        <div class="theory-card">

            <h2>

                10. Horizontal Range

            </h2>

            <p>

                The horizontal range is the horizontal distance traveled
                before the projectile returns to its initial height.

            </p>


            <div class="formula-box">

                \[
                R=v_{0x}T
                \]

            </div>


            <p>

                Substituting the horizontal velocity and flight time:

            </p>


            <div class="formula-box">

                \[
                R
                =
                v_0\cos\theta
                \left(
                \frac{2v_0\sin\theta}{g}
                \right)
                \]

            </div>


            <p>

                Using

            </p>


            <div class="formula-box">

                \[
                2\sin\theta\cos\theta
                =
                \sin(2\theta)
                \]

            </div>


            <p>

                gives

            </p>


            <div class="formula-box">

                \[
                \boxed{
                R
                =
                \frac{v_0^2\sin(2\theta)}{g}
                }
                \]

            </div>


            <div class="highlight-box">

                <strong>Maximum range:</strong>

                For equal launch and landing heights and no air
                resistance, the maximum range occurs at

                \[
                \boxed{\theta=45^\circ}
                \]

            </div>

        </div>



        <!-- =================================================
             TRAJECTORY EQUATION
        ================================================== -->

        <div class="theory-card">

            <h2>

                11. Equation of the Trajectory

            </h2>

            <p>

                The time parameter can be eliminated to obtain an
                equation directly relating \(y\) and \(x\).

            </p>


            <p>

                From horizontal motion:

            </p>


            <div class="formula-box">

                \[
                t
                =
                \frac{x}{v_0\cos\theta}
                \]

            </div>


            <p>

                Substitute this into the vertical equation:

            </p>


            <div class="formula-box">

                \[
                y
                =
                x\tan\theta
                -
                \frac{gx^2}
                {2v_0^2\cos^2\theta}
                \]

            </div>


            <p>

                Therefore,

            </p>


            <div class="formula-box">

                \[
                \boxed{
                y(x)
                =
                x\tan\theta
                -
                \frac{gx^2}
                {2v_0^2\cos^2\theta}
                }
                \]

            </div>


            <p>

                This equation describes the projectile's
                <strong>parabolic trajectory</strong>.

            </p>

        </div>



        <!-- =================================================
             IMPORTANT EQUATIONS TABLE
        ================================================== -->

        <div class="theory-card">

            <h2>

                12. Projectile Motion Formula Reference

            </h2>


            <div class="table-responsive">

                <table class="reference-table">

                    <thead>

                        <tr>

                            <th>Quantity</th>

                            <th>Formula</th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr>

                            <td>

                                Horizontal velocity

                            </td>

                            <td>

                                \[
                                v_x=v_0\cos\theta
                                \]

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Initial vertical velocity

                            </td>

                            <td>

                                \[
                                v_{0y}=v_0\sin\theta
                                \]

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Horizontal position

                            </td>

                            <td>

                                \[
                                x=x_0+v_0\cos\theta\,t
                                \]

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Vertical position

                            </td>

                            <td>

                                \[
                                y=y_0+v_0\sin\theta\,t
                                -\frac{1}{2}gt^2
                                \]

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Vertical velocity

                            </td>

                            <td>

                                \[
                                v_y=v_0\sin\theta-gt
                                \]

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Time to maximum height

                            </td>

                            <td>

                                \[
                                t_H=
                                \frac{v_0\sin\theta}{g}
                                \]

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Maximum height

                            </td>

                            <td>

                                \[
                                H=
                                \frac{v_0^2\sin^2\theta}{2g}
                                \]

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Time of flight

                            </td>

                            <td>

                                \[
                                T=
                                \frac{2v_0\sin\theta}{g}
                                \]

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Horizontal range

                            </td>

                            <td>

                                \[
                                R=
                                \frac{v_0^2\sin(2\theta)}{g}
                                \]

                            </td>

                        </tr>


                        <tr>

                            <td>

                                Trajectory

                            </td>

                            <td>

                                \[
                                y=
                                x\tan\theta
                                -
                                \frac{gx^2}
                                {2v_0^2\cos^2\theta}
                                \]

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>



        <!-- =================================================
             WORKED EXAMPLE
        ================================================== -->

        <div class="theory-card">

            <h2>

                13. Worked Example

            </h2>

            <p>

                A projectile is launched from ground level with an
                initial velocity of

            </p>


            <div class="formula-box">

                \[
                v_0=20\ \text{m/s}
                \]

            </div>


            <p>

                at an angle of

            </p>


            <div class="formula-box">

                \[
                \theta=30^\circ
                \]

            </div>


            <p>

                Determine its maximum height, time of flight, and
                horizontal range.

            </p>


            <div class="step-box">

                <span class="step-number">1</span>

                <span class="step-title">

                    Maximum Height

                </span>

                <div class="formula-box">

                    \[
                    H=
                    \frac{v_0^2\sin^2\theta}{2g}
                    \]

                    \[
                    H=
                    \frac{
                    (20)^2(\sin30^\circ)^2
                    }
                    {2(9.81)}
                    \]

                    \[
                    H\approx5.10\ \text{m}
                    \]

                </div>

            </div>


            <div class="step-box">

                <span class="step-number">2</span>

                <span class="step-title">

                    Time of Flight

                </span>

                <div class="formula-box">

                    \[
                    T=
                    \frac{2v_0\sin\theta}{g}
                    \]

                    \[
                    T=
                    \frac{
                    2(20)(\sin30^\circ)
                    }
                    {9.81}
                    \]

                    \[
                    T\approx2.04\ \text{s}
                    \]

                </div>

            </div>


            <div class="step-box">

                <span class="step-number">3</span>

                <span class="step-title">

                    Horizontal Range

                </span>

                <div class="formula-box">

                    \[
                    R=
                    \frac{v_0^2\sin(2\theta)}{g}
                    \]

                    \[
                    R=
                    \frac{
                    (20)^2\sin(60^\circ)
                    }
                    {9.81}
                    \]

                    \[
                    R\approx35.31\ \text{m}
                    \]

                </div>

            </div>


            <div class="highlight-box">

                <strong>Answer:</strong>

                <br><br>

                Maximum height:

                \[
                H\approx5.10\ \text{m}
                \]

                Time of flight:

                \[
                T\approx2.04\ \text{s}
                \]

                Horizontal range:

                \[
                R\approx35.31\ \text{m}
                \]

            </div>

        </div>



        <!-- =================================================
             DIFFERENT LAUNCH AND LANDING HEIGHT
        ================================================== -->

        <div class="theory-card">

            <h2>

                14. Projectile Launched From an Elevated Position

            </h2>

            <p>

                The standard time-of-flight formula

            </p>


            <div class="formula-box">

                \[
                T=
                \frac{2v_0\sin\theta}{g}
                \]

            </div>


            <p>

                cannot be used directly when the projectile lands at a
                different height.

            </p>


            <p>

                Instead, use the vertical position equation:

            </p>


            <div class="formula-box">

                \[
                y_f
                =
                y_0
                +
                v_0\sin\theta\,t
                -
                \frac{1}{2}gt^2
                \]

            </div>


            <p>

                Rearranging:

            </p>


            <div class="formula-box">

                \[
                \frac{1}{2}gt^2
                -
                v_0\sin\theta\,t
                +
                (y_f-y_0)
                =
                0
                \]

            </div>


            <p>

                This is a quadratic equation in \(t\), which can be
                solved using the quadratic formula.

            </p>


            <div class="formula-box">

                \[
                t
                =
                \frac{
                v_0\sin\theta
                \pm
                \sqrt{
                (v_0\sin\theta)^2
                -
                2g(y_f-y_0)
                }
                }
                {g}
                \]

            </div>

        </div>



        <!-- =================================================
             VARIABLES
        ================================================== -->

        <div class="theory-card">

            <h2>

                15. Variable Reference

            </h2>


            <div class="table-responsive">

                <table class="reference-table">

                    <thead>

                        <tr>

                            <th>Symbol</th>

                            <th>Meaning</th>

                            <th>Typical Unit</th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr>

                            <td class="center">

                                \(v_0\)

                            </td>

                            <td>

                                Initial speed

                            </td>

                            <td class="center">

                                m/s

                            </td>

                        </tr>


                        <tr>

                            <td class="center">

                                \(\theta\)

                            </td>

                            <td>

                                Launch angle

                            </td>

                            <td class="center">

                                degrees or radians

                            </td>

                        </tr>


                        <tr>

                            <td class="center">

                                \(g\)

                            </td>

                            <td>

                                Gravitational acceleration

                            </td>

                            <td class="center">

                                m/s²

                            </td>

                        </tr>


                        <tr>

                            <td class="center">

                                \(t\)

                            </td>

                            <td>

                                Time

                            </td>

                            <td class="center">

                                s

                            </td>

                        </tr>


                        <tr>

                            <td class="center">

                                \(x\)

                            </td>

                            <td>

                                Horizontal position

                            </td>

                            <td class="center">

                                m

                            </td>

                        </tr>


                        <tr>

                            <td class="center">

                                \(y\)

                            </td>

                            <td>

                                Vertical position

                            </td>

                            <td class="center">

                                m

                            </td>

                        </tr>


                        <tr>

                            <td class="center">

                                \(R\)

                            </td>

                            <td>

                                Horizontal range

                            </td>

                            <td class="center">

                                m

                            </td>

                        </tr>


                        <tr>

                            <td class="center">

                                \(H\)

                            </td>

                            <td>

                                Maximum height

                            </td>

                            <td class="center">

                                m

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>



        <!-- =================================================
             KEY POINTS
        ================================================== -->

        <div class="theory-card">

            <h2>

                16. Key Points to Remember

            </h2>


            <ul>

                <li>

                    Projectile motion consists of independent horizontal
                    and vertical motions.

                </li>

                <li>

                    Horizontal acceleration is zero when air resistance
                    is neglected.

                </li>

                <li>

                    Vertical acceleration is \(-g\).

                </li>

                <li>

                    The horizontal velocity remains constant.

                </li>

                <li>

                    The vertical velocity decreases while the projectile
                    rises and increases in magnitude while it falls.

                </li>

                <li>

                    At maximum height, the vertical velocity is zero.

                </li>

                <li>

                    The trajectory of an ideal projectile is a parabola.

                </li>

                <li>

                    The standard range and time-of-flight equations assume
                    equal launch and landing heights.

                </li>

                <li>

                    For equal launch and landing heights, a \(45^\circ\)
                    launch angle produces the maximum range.

                </li>

                <li>

                    The value of \(g\) near Earth's surface is commonly
                    taken as \(9.81\ \text{m/s}^2\).

                </li>

            </ul>

        </div>



    </div>

</div>


<?php include "globals/scripts.php"; ?>


</body>

</html>
