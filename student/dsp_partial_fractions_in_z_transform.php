<?php

/* =========================================================
   PARTIAL FRACTIONS IN THE INVERSE Z-TRANSFORM
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

            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);

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
           HIGHLIGHT
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
           CODE / ALGEBRA
        ====================================================== */

        .algebra-box {

            background: var(--formula-bg);

            border: 1px solid var(--border);

            border-radius: 8px;

            padding: 15px;

            margin: 12px 0;

            overflow-x: auto;

            font-family: "Times New Roman", serif;

            font-size: 17px;

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

                    <i class="bi bi-diagram-3"></i>

                    Partial Fractions in the Inverse Z-Transform

                </h1>

                <p>

                    A step-by-step reference for decomposing rational
                    Z-transform expressions and obtaining their inverse
                    Z-transform using standard transform pairs and the ROC.

                </p>

            </div>



            <!-- =================================================
             INTRODUCTION
        ================================================== -->

            <div class="theory-card">

                <h2>

                    1. What is Partial Fraction Expansion?

                </h2>

                <p>

                    In many inverse Z-transform problems, the transform
                    \(X(z)\) is expressed as a rational function containing
                    several poles. Directly finding \(x[n]\) may be difficult.

                    <strong>Partial fraction expansion</strong> separates the
                    rational function into simpler terms whose inverse
                    Z-transforms are already known.

                </p>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    \frac{P(z)}{Q(z)}
                    \]

                </div>


                <p>

                    The objective is to rewrite \(X(z)\) in the form

                </p>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    X_1(z)+X_2(z)+\cdots+X_k(z)
                    \]

                </div>


                <p>

                    Each \(X_k(z)\) can then be matched with a standard
                    Z-transform pair.

                </p>

            </div>



            <!-- =================================================
             GENERAL PROCEDURE
        ================================================== -->

            <div class="theory-card">

                <h2>

                    2. General Procedure

                </h2>


                <div class="step-box">

                    <span class="step-number">1</span>

                    <span class="step-title">

                        Express \(X(z)\) as a rational function.

                    </span>

                    <p class="mt-2">

                        Write the numerator and denominator clearly and
                        factor the denominator whenever possible.

                    </p>

                </div>


                <div class="step-box">

                    <span class="step-number">2</span>

                    <span class="step-title">

                        Check whether the rational function is proper.

                    </span>

                    <p class="mt-2">

                        The degree of the numerator should be less than the
                        degree of the denominator before applying ordinary
                        partial fractions.

                    </p>

                </div>


                <div class="step-box">

                    <span class="step-number">3</span>

                    <span class="step-title">

                        Factor the denominator.

                    </span>

                    <p class="mt-2">

                        Identify distinct poles, repeated poles, or
                        irreducible quadratic factors.

                    </p>

                </div>


                <div class="step-box">

                    <span class="step-number">4</span>

                    <span class="step-title">

                        Perform partial fraction expansion.

                    </span>

                    <p class="mt-2">

                        Determine the unknown coefficients.

                    </p>

                </div>


                <div class="step-box">

                    <span class="step-number">5</span>

                    <span class="step-title">

                        Use the ROC.

                    </span>

                    <p class="mt-2">

                        The ROC determines whether each term corresponds
                        to a right-sided or left-sided sequence.

                    </p>

                </div>


                <div class="step-box">

                    <span class="step-number">6</span>

                    <span class="step-title">

                        Apply the inverse Z-transform pairs.

                    </span>

                    <p class="mt-2">

                        Combine the resulting sequences to obtain \(x[n]\).

                    </p>

                </div>

            </div>



            <!-- =================================================
             STANDARD FORM
        ================================================== -->

            <div class="theory-card">

                <h2>

                    3. Standard Partial Fraction Forms

                </h2>


                <div class="table-responsive">

                    <table class="reference-table">

                        <thead>

                            <tr>

                                <th>Denominator Factor</th>

                                <th>Partial Fraction Form</th>

                                <th>Type of Pole</th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>

                                <td class="center">

                                    \(z-a\)

                                </td>

                                <td class="center">

                                    \(\displaystyle\frac{A z}{z-a}\)

                                </td>

                                <td class="center">

                                    Simple pole

                                </td>

                            </tr>


                            <tr>

                                <td class="center">

                                    \((z-a)^2\)

                                </td>

                                <td class="center">

                                    \(\displaystyle
                                    \frac{A z}{z-a}
                                    +
                                    \frac{B z}{(z-a)^2}
                                    \)

                                </td>

                                <td class="center">

                                    Repeated pole

                                </td>

                            </tr>


                            <tr>

                                <td class="center">

                                    \((z-a)^m\)

                                </td>

                                <td class="center">

                                    \(\displaystyle
                                    \sum_{k=1}^{m}
                                    \frac{A_k z}{(z-a)^k}
                                    \)

                                </td>

                                <td class="center">

                                    Pole of order \(m\)

                                </td>

                            </tr>


                            <tr>

                                <td class="center">

                                    \((z-a)(z-b)\)

                                </td>

                                <td class="center">

                                    \(\displaystyle
                                    \frac{A z}{z-a}
                                    +
                                    \frac{B z}{z-b}
                                    \)

                                </td>

                                <td class="center">

                                    Two distinct poles

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>



            <!-- =================================================
             EXAMPLE 1
        ================================================== -->

            <div class="theory-card">

                <h2>

                    4. Example 1 — Distinct Real Poles

                </h2>


                <p>

                    Find the inverse Z-transform of

                </p>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    \frac{z}
                    {(z-2)(z-4)}
                    \]

                </div>


                <h3>

                    Step 1: Assume the partial fraction form

                </h3>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    A\frac{z}{z-2}
                    +
                    B\frac{z}{z-4}
                    \]

                </div>


                <h3>

                    Step 2: Find \(A\) and \(B\)

                </h3>


                <p>

                    Multiply both sides by \((z-2)(z-4)\):

                </p>


                <div class="formula-box">

                    \[
                    z
                    =
                    Az(z-4)
                    +
                    Bz(z-2)
                    \]

                </div>


                <p>

                    For \(z\neq0\), divide by \(z\):

                </p>


                <div class="formula-box">

                    \[
                    1=A(z-4)+B(z-2)
                    \]

                </div>


                <p>

                    Set \(z=2\):

                </p>


                <div class="formula-box">

                    \[
                    1=A(2-4)
                    \]

                    \[
                    A=-\frac{1}{2}
                    \]

                </div>


                <p>

                    Set \(z=4\):

                </p>


                <div class="formula-box">

                    \[
                    1=B(4-2)
                    \]

                    \[
                    B=\frac{1}{2}
                    \]

                </div>


                <h3>

                    Step 3: Partial fraction expansion

                </h3>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    -\frac{1}{2}\frac{z}{z-2}
                    +
                    \frac{1}{2}\frac{z}{z-4}
                    \]

                </div>


                <h3>

                    Step 4: Apply the ROC

                </h3>


                <p>

                    The inverse transform depends on the ROC.

                </p>


                <div class="table-responsive">

                    <table class="reference-table">

                        <thead>

                            <tr>

                                <th>ROC</th>

                                <th>Sequence Type</th>

                                <th>Inverse Z-Transform</th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>

                                <td class="center">

                                    \(|z|>4\)

                                </td>

                                <td class="center">

                                    Right-sided

                                </td>

                                <td>

                                    \[
                                    x[n]
                                    =
                                    -\frac{1}{2}2^n u[n]
                                    +
                                    \frac{1}{2}4^n u[n]
                                    \]

                                </td>

                            </tr>


                            <tr>

                                <td class="center">

                                    \(2<|z|<4\)

                                        </td>

                                <td class="center">

                                    Two-sided

                                </td>

                                <td>

                                    \[
                                    x[n]
                                    =
                                    -\frac{1}{2}2^n u[n]
                                    -
                                    \frac{1}{2}4^n u[-n-1]
                                    \]

                                </td>

                            </tr>


                            <tr>

                                <td class="center">

                                    \(|z|<2\)

                                        </td>

                                <td class="center">

                                    Left-sided

                                </td>

                                <td>

                                    \[
                                    x[n]
                                    =
                                    \frac{1}{2}2^n u[-n-1]
                                    -
                                    \frac{1}{2}4^n u[-n-1]
                                    \]

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <div class="highlight-box">

                    <strong>Important:</strong>

                    The same algebraic expression \(X(z)\) can represent
                    different time-domain sequences. The ROC determines
                    which inverse Z-transform is correct.

                </div>

            </div>



            <!-- =================================================
             EXAMPLE 2
        ================================================== -->

            <div class="theory-card">

                <h2>

                    5. Example 2 — Simple Partial Fractions

                </h2>


                <p>

                    Consider

                </p>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    \frac{3z-5}
                    {(z-1)(z-2)}
                    \]

                </div>


                <p>

                    Assume

                </p>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    A\frac{z}{z-1}
                    +
                    B\frac{z}{z-2}
                    \]

                </div>


                <p>

                    Multiplying by the denominator gives

                </p>


                <div class="formula-box">

                    \[
                    3z-5
                    =
                    Az(z-2)+Bz(z-1)
                    \]

                </div>


                <p>

                    Because this numerator representation introduces a
                    common factor \(z\), it is often cleaner to first rewrite
                    the expression in powers of \(z^{-1}\) or use the
                    standard form

                </p>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    \frac{A}{z-1}
                    +
                    \frac{B}{z-2}
                    \]

                </div>


                <p>

                    The important lesson is that the chosen partial-fraction
                    form must be algebraically consistent with the original
                    rational function.

                </p>


                <div class="warning-box">

                    <strong>Check your form:</strong>

                    When working with Z-transforms, do not automatically
                    insert a factor of \(z\) into every partial-fraction
                    term. The decomposition must reproduce the original
                    rational function exactly.

                </div>

            </div>



            <!-- =================================================
             REPEATED POLES
        ================================================== -->

            <div class="theory-card">

                <h2>

                    6. Repeated Poles

                </h2>


                <p>

                    If the denominator contains a repeated factor such as

                </p>


                <div class="formula-box">

                    \[
                    (z-a)^2
                    \]

                </div>


                <p>

                    the partial fraction expansion must contain a term
                    for every power of the repeated factor.

                </p>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    \frac{A}{z-a}
                    +
                    \frac{B}{(z-a)^2}
                    \]

                </div>


                <p>

                    For a pole of order three:

                </p>


                <div class="formula-box">

                    \[
                    X(z)
                    =
                    \frac{A}{z-a}
                    +
                    \frac{B}{(z-a)^2}
                    +
                    \frac{C}{(z-a)^3}
                    \]

                </div>


                <h3>

                    Useful inverse Z-transform relationship

                </h3>


                <div class="formula-box">

                    \[
                    \frac{z}{(z-a)^2}
                    \longleftrightarrow
                    n a^{n-1}u[n]
                    \]

                    \[
                    \frac{az}{(z-a)^2}
                    \longleftrightarrow
                    n a^n u[n]
                    \]

                </div>


                <p>

                    Therefore, repeated poles generally produce sequences
                    containing polynomial factors such as \(n\), \(n^2\),
                    and higher powers of \(n\).

                </p>

            </div>



            <!-- =================================================
             IMPORTANT STANDARD FORMS
        ================================================== -->

            <div class="theory-card">

                <h2>

                    7. Partial Fraction Forms and Inverse Z-Transforms

                </h2>


                <div class="table-responsive">

                    <table class="reference-table">

                        <thead>

                            <tr>

                                <th>Z-Domain Term</th>

                                <th>ROC</th>

                                <th>Inverse Z-Transform</th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>

                                <td>

                                    \(\displaystyle
                                    \frac{z}{z-a}
                                    \)

                                </td>

                                <td class="center">

                                    \(|z|>|a|\)

                                </td>

                                <td>

                                    \(a^n u[n]\)

                                </td>

                            </tr>


                            <tr>

                                <td>

                                    \(\displaystyle
                                    \frac{z}{z-a}
                                    \)

                                </td>

                                <td class="center">

                                    \(|z|<|a|\)

                                        </td>

                                <td>

                                    \(-a^n u[-n-1]\)

                                </td>

                            </tr>


                            <tr>

                                <td>

                                    \(\displaystyle
                                    \frac{az}{(z-a)^2}
                                    \)

                                </td>

                                <td class="center">

                                    \(|z|>|a|\)

                                </td>

                                <td>

                                    \(n a^n u[n]\)

                                </td>

                            </tr>


                            <tr>

                                <td>

                                    \(\displaystyle
                                    \frac{az}{(z-a)^2}
                                    \)

                                </td>

                                <td class="center">

                                    \(|z|<|a|\)

                                        </td>

                                <td>

                                    \(-n a^n u[-n-1]\)

                                </td>

                            </tr>


                            <tr>

                                <td>

                                    \(\displaystyle
                                    \frac{z}{(z-a)^3}
                                    \)

                                </td>

                                <td class="center">

                                    \(|z|>|a|\)

                                </td>

                                <td>

                                    \(\displaystyle
                                    \frac{n(n-1)}{2a^2}
                                    a^n u[n]
                                    \)

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>



            <!-- =================================================
             ROC AND PARTIAL FRACTIONS
        ================================================== -->

            <div class="theory-card">

                <h2>

                    8. Why the ROC is Important

                </h2>


                <p>

                    Partial fraction expansion gives the algebraic components
                    of \(X(z)\), but the ROC determines which sequence
                    representation is selected.

                </p>


                <div class="table-responsive">

                    <table class="reference-table">

                        <thead>

                            <tr>

                                <th>ROC</th>

                                <th>Sequence Interpretation</th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>

                                <td class="center">

                                    Outside outermost pole

                                </td>

                                <td>

                                    Right-sided sequence

                                </td>

                            </tr>


                            <tr>

                                <td class="center">

                                    Inside innermost pole

                                </td>

                                <td>

                                    Left-sided sequence

                                </td>

                            </tr>


                            <tr>

                                <td class="center">

                                    Between poles

                                </td>

                                <td>

                                    Two-sided sequence

                                </td>

                            </tr>


                            <tr>

                                <td class="center">

                                    Contains unit circle

                                </td>

                                <td>

                                    DTFT exists under the usual
                                    absolute-summability conditions

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <div class="highlight-box">

                    <strong>Remember:</strong>

                    Poles determine the possible boundaries of the ROC,
                    while the ROC determines the sidedness of the sequence.

                </div>

            </div>



            <!-- =================================================
             QUICK SOLVING TEMPLATE
        ================================================== -->

            <div class="theory-card">

                <h2>

                    9. Quick Solving Template

                </h2>


                <div class="algebra-box">

                    <strong>Given:</strong>

                    \[
                    X(z)=\frac{P(z)}{Q(z)}
                    \]

                    <br>

                    <strong>1.</strong> Factor \(Q(z)\).

                    <br>

                    <strong>2.</strong> Determine the poles.

                    <br>

                    <strong>3.</strong> Write the partial fraction form.

                    <br>

                    <strong>4.</strong> Solve for the coefficients.

                    <br>

                    <strong>5.</strong> Determine the ROC.

                    <br>

                    <strong>6.</strong> Match each term with a standard
                    Z-transform pair.

                    <br>

                    <strong>7.</strong> Combine all sequences.

                </div>

            </div>



            <!-- =================================================
             KEY POINTS
        ================================================== -->

            <div class="theory-card">

                <h2>

                    10. Key Points to Remember

                </h2>


                <ul>

                    <li>
                        Partial fractions simplify a rational
                        \(X(z)\) into standard inverse-transform terms.
                    </li>

                    <li>
                        Distinct poles produce separate partial-fraction
                        terms.
                    </li>

                    <li>
                        Repeated poles require terms for every power of
                        the repeated factor.
                    </li>

                    <li>
                        The ROC is essential when determining the
                        inverse Z-transform.
                    </li>

                    <li>
                        The same algebraic \(X(z)\) can represent
                        different sequences for different ROCs.
                    </li>

                    <li>
                        A right-sided sequence has an ROC outside the
                        outermost pole.
                    </li>

                    <li>
                        A left-sided sequence has an ROC inside the
                        innermost pole.
                    </li>

                    <li>
                        A two-sided sequence generally has an annular ROC.
                    </li>

                    <li>
                        Repeated poles commonly produce factors involving
                        \(n\), \(n^2\), and higher powers of \(n\).
                    </li>

                </ul>

            </div>



        </div>

    </div>


    <?php include "globals/scripts.php"; ?>


</body>

</html>