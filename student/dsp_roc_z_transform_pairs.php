
<?php

/* =========================================================
   COMMON Z-TRANSFORM PAIRS AND REGION OF CONVERGENCE
   DIGITAL SIGNAL PROCESSING
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
                        Z-Transform, Common Pairs, Applications and ROC
                    </h2>

                    <p>
                        Reference table of common discrete-time
                        Z-transform pairs and their corresponding
                        regions of convergence.
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
                            Z-Transform and Region of Convergence
                        </h4>

                        <p>
                            Fundamental concepts for analyzing
                            discrete-time signals and systems.
                        </p>

                    </div>

                    <div class="theory-badge">

                        <i class="bi bi-circle-square"></i>

                        Z-Domain

                    </div>

                </div>


                <div class="theory-body">

                    <p>

                        The <strong>Z-transform</strong> converts a
                        discrete-time sequence from the time domain
                        into a representation in the complex
                        frequency domain.

                    </p>


                    <p>

                        The bilateral Z-transform of a discrete-time
                        sequence \(x[n]\) is defined as:

                    </p>


                    <!-- EQUATION -->

                    <div class="equation-box">

                        \[
                        X(z)=\sum_{n=-\infty}^{\infty}x[n]z^{-n}
                        \]

                    </div>


                    <p>

                        The <strong>Region of Convergence (ROC)</strong>
                        is the set of values of \(z\) for which the
                        Z-transform summation converges to a finite
                        value.

                    </p>


                    <div class="concept-box">

                        <strong>
                            Important idea
                        </strong>

                        <span>

                            A Z-transform pair is not completely
                            specified by \(X(z)\) alone. The
                            corresponding ROC is also required because
                            the same algebraic expression can represent
                            different time-domain sequences depending
                            on the ROC.

                        </span>

                    </div>

                </div>

            </div>


            <!-- =================================================
             BASIC DEFINITION
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            1. Bilateral Z-Transform
                        </h4>

                        <p>
                            Mathematical definition of the Z-transform.
                        </p>

                    </div>

                    <div class="statement-number">
                        X(z)
                    </div>

                </div>


                <div class="theory-body">


                    <div class="definition-box">

                        <strong>
                            Definition
                        </strong>

                        <span>

                            For a discrete-time sequence \(x[n]\),
                            the bilateral Z-transform is the infinite
                            summation of \(x[n]z^{-n}\) over all integer
                            values of \(n\).

                        </span>

                    </div>


                    <div class="equation-box">

                        \[
                        X(z)=
                        \sum_{n=-\infty}^{\infty}
                        x[n]z^{-n}
                        \]

                    </div>


                    <div class="definition-box">

                        <strong>
                            Region of Convergence
                        </strong>

                        <span>

                            The ROC consists of all complex values of
                            \(z\) for which the Z-transform converges.

                            It is commonly expressed using the
                            magnitude \(|z|\).

                        </span>

                    </div>


                    <div class="roc-example">

                        <div>

                            <span>
                                Example
                            </span>

                            <strong>
                                \(\frac{1}{1-az^{-1}}\)
                            </strong>

                        </div>

                        <div>

                            <span>
                                Right-sided sequence
                            </span>

                            <strong>
                                \(|z|>|a|\)
                            </strong>

                        </div>

                        <div>

                            <span>
                                Left-sided sequence
                            </span>

                            <strong>
                                \(|z|<|a|\)
                                    </strong>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             COMMON PAIRS TABLE
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            2. Common Z-Transform Pairs
                        </h4>

                        <p>
                            Frequently used discrete-time sequences,
                            transforms, and regions of convergence.
                        </p>

                    </div>

                    <div class="statement-number">
                        REFERENCE
                    </div>

                </div>


                <div class="comparison-wrapper">

                    <table class="comparison-table z-table">

                        <thead>

                            <tr>

                                <th>
                                    No.
                                </th>

                                <th>
                                    \(x[n]\)
                                </th>

                                <th>
                                    \(X(z)\)
                                </th>

                                <th>
                                    ROC
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <!-- 1 -->

                            <tr>

                                <td>
                                    1
                                </td>

                                <td>
                                    \(\delta[n]\)
                                </td>

                                <td>
                                    \(1\)
                                </td>

                                <td>
                                    Entire \(z\)-plane
                                </td>

                            </tr>


                            <!-- 2 -->

                            <tr>

                                <td>
                                    2
                                </td>

                                <td>
                                    \(\delta[n-k]\)
                                </td>

                                <td>
                                    \(z^{-k}\)
                                </td>

                                <td>
                                    \(z\neq0\) if \(k>0\)
                                </td>

                            </tr>


                            <!-- 3 -->

                            <tr>

                                <td>
                                    3
                                </td>

                                <td>
                                    \(u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle\frac{1}{1-z^{-1}}
                                    =\frac{z}{z-1}\)
                                </td>

                                <td>
                                    \(|z|>1\)
                                </td>

                            </tr>


                            <!-- 4 -->

                            <tr>

                                <td>
                                    4
                                </td>

                                <td>
                                    \(u[-n-1]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{1}{1-z^{-1}}
                                    =\frac{z}{z-1}\)
                                </td>

                                <td>
                                    \(|z|<1\)
                                        </td>

                            </tr>


                            <!-- 5 -->

                            <tr>

                                <td>
                                    5
                                </td>

                                <td>
                                    \(a^n u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{1}{1-az^{-1}}
                                    =\frac{z}{z-a}\)
                                </td>

                                <td>
                                    \(|z|>|a|\)
                                </td>

                            </tr>


                            <!-- 6 -->

                            <tr>

                                <td>
                                    6
                                </td>

                                <td>
                                    \(-a^n u[-n-1]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{1}{1-az^{-1}}
                                    =\frac{z}{z-a}\)
                                </td>

                                <td>
                                    \(|z|<|a|\)
                                        </td>

                            </tr>


                            <!-- 7 -->

                            <tr>

                                <td>
                                    7
                                </td>

                                <td>
                                    \(na^n u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{az^{-1}}
                                    {(1-az^{-1})^2}\)
                                </td>

                                <td>
                                    \(|z|>|a|\)
                                </td>

                            </tr>


                            <!-- 8 -->

                            <tr>

                                <td>
                                    8
                                </td>

                                <td>
                                    \(-na^n u[-n-1]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{az^{-1}}
                                    {(1-az^{-1})^2}\)
                                </td>

                                <td>
                                    \(|z|<|a|\)
                                        </td>

                            </tr>


                            <!-- 9 -->

                            <tr>

                                <td>
                                    9
                                </td>

                                <td>
                                    \(a^n\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \text{does not converge
                                    bilaterally for }a\neq0
                                    \)
                                </td>

                                <td>
                                    No ROC
                                </td>

                            </tr>


                            <!-- 10 -->

                            <tr>

                                <td>
                                    10
                                </td>

                                <td>
                                    \(n u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{z}{(z-1)^2}\)
                                </td>

                                <td>
                                    \(|z|>1\)
                                </td>

                            </tr>


                            <!-- 11 -->

                            <tr>

                                <td>
                                    11
                                </td>

                                <td>
                                    \(-n u[-n-1]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{z}{(z-1)^2}\)
                                </td>

                                <td>
                                    \(|z|<1\)
                                        </td>

                            </tr>


                            <!-- 12 -->

                            <tr>

                                <td>
                                    12
                                </td>

                                <td>
                                    \(a^n u[n]+b^n u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{1}{1-az^{-1}}
                                    +
                                    \frac{1}{1-bz^{-1}}\)
                                </td>

                                <td>
                                    \(|z|>\max(|a|,|b|)\)
                                </td>

                            </tr>


                            <!-- 13 -->

                            <tr>

                                <td>
                                    13
                                </td>

                                <td>
                                    \(a^n u[n]-b^n u[-n-1]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{1}{1-az^{-1}}
                                    +
                                    \frac{1}{1-bz^{-1}}\)
                                </td>

                                <td>
                                    \(|a|<|z|<|b|\)
                                        </td>

                            </tr>


                            <!-- 14 -->

                            <tr>

                                <td>
                                    14
                                </td>

                                <td>
                                    \(\cos(\omega_0 n)u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{
                                    1-\cos(\omega_0)z^{-1}
                                    }{
                                    1-2\cos(\omega_0)z^{-1}
                                    +z^{-2}
                                    }\)
                                </td>

                                <td>
                                    \(|z|>1\)
                                </td>

                            </tr>


                            <!-- 15 -->

                            <tr>

                                <td>
                                    15
                                </td>

                                <td>
                                    \(\sin(\omega_0 n)u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{
                                    \sin(\omega_0)z^{-1}
                                    }{
                                    1-2\cos(\omega_0)z^{-1}
                                    +z^{-2}
                                    }\)
                                </td>

                                <td>
                                    \(|z|>1\)
                                </td>

                            </tr>


                            <!-- 16 -->

                            <tr>

                                <td>
                                    16
                                </td>

                                <td>
                                    \(a^n\cos(\omega_0 n)u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{
                                    1-a\cos(\omega_0)z^{-1}
                                    }{
                                    1-2a\cos(\omega_0)z^{-1}
                                    +a^2z^{-2}
                                    }\)
                                </td>

                                <td>
                                    \(|z|>|a|\)
                                </td>

                            </tr>


                            <!-- 17 -->

                            <tr>

                                <td>
                                    17
                                </td>

                                <td>
                                    \(a^n\sin(\omega_0 n)u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{
                                    a\sin(\omega_0)z^{-1}
                                    }{
                                    1-2a\cos(\omega_0)z^{-1}
                                    +a^2z^{-2}
                                    }\)
                                </td>

                                <td>
                                    \(|z|>|a|\)
                                </td>

                            </tr>


                            <!-- 18 -->

                            <tr>

                                <td>
                                    18
                                </td>

                                <td>
                                    \(n^2u[n]\)
                                </td>

                                <td>
                                    \(\displaystyle
                                    \frac{
                                    z(z+1)
                                    }{
                                    (z-1)^3
                                    }\)
                                </td>

                                <td>
                                    \(|z|>1\)
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
             RIGHT-SIDED SEQUENCES
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            3. Right-Sided Sequences
                        </h4>

                        <p>
                            Sequences that extend toward positive
                            values of \(n\).
                        </p>

                    </div>

                    <div class="statement-number">
                        RIGHT-SIDED
                    </div>

                </div>


                <div class="theory-body">

                    <div class="definition-box">

                        <strong>
                            Definition
                        </strong>

                        <span>

                            A right-sided sequence is zero below some
                            finite value of \(n\). For causal sequences,
                            the sequence is zero for all \(n<0\).

                                </span>

                    </div>


                    <div class="equation-box">

                        \[
                        x[n]=a^n u[n]
                        \]

                    </div>


                    <div class="section-label">

                        Z-Transform

                    </div>


                    <div class="equation-box">

                        \[
                        X(z)
                        =
                        \sum_{n=0}^{\infty}
                        a^n z^{-n}
                        \]

                        \[
                        X(z)
                        =
                        \frac{1}{1-az^{-1}}
                        \]

                    </div>


                    <div class="result-box">

                        <strong>
                            ROC
                        </strong>

                        <span>

                            The geometric series converges when

                        </span>

                        <div class="roc-equation">

                            \[
                            |az^{-1}|<1
                                \]

                                \[
                                \boxed{|z|>|a|}
                                \]

                        </div>

                    </div>


                    <div class="mini-flow">

                        <div class="mini-flow-item">

                            <span>
                                Sequence
                            </span>

                            <strong>
                                \(a^nu[n]\)
                            </strong>

                        </div>


                        <div class="mini-arrow">
                            →
                        </div>


                        <div class="mini-flow-item">

                            <span>
                                Transform
                            </span>

                            <strong>
                                \(\frac{z}{z-a}\)
                            </strong>

                        </div>


                        <div class="mini-arrow">
                            →
                        </div>


                        <div class="mini-flow-item success-flow">

                            <span>
                                ROC
                            </span>

                            <strong>
                                \(|z|>|a|\)
                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
             LEFT-SIDED SEQUENCES
        ================================================== -->

            <div class="theory-card">

                <div class="theory-header">

                    <div>

                        <h4>
                            4. Left-Sided Sequences
                        </h4>

                        <p>
                            Sequences that extend toward negative
                            values of \(n\).
                        </p>

                    </div>

                    <div class="statement-number">
                        LEFT-SIDED
                    </div>

                </div>


                <div class="theory-body">

                    <div class="definition-box">

                        <strong>
                            Definition
                        </strong>

                        <span>

                            A left-sided sequence is zero above some
                            finite value of \(n\). A commonly used
                            example is a sequence involving
                            \(u[-n-1]\).

                        </span>

                    </div>


                    <div class="equation-box">

                        \[
                        x[n]=-a^n u[-n-1]
                        \]

                    </div>


                    <div class="section-label">

                        Z-Transform

                    </div>


                    <div class="equation-box">

                        \[
                        X(z)
                        =
                        \frac{1}{1-az^{-1}}
                        \]

                    </div>


                    <div class="result-box">

                        <strong>
                            ROC
                        </strong>

                        <span>

                            For a left-sided sequence, the geometric
                            series converges inside the pole.

                        </span>

                        <div class="roc-equation">

                            \[
                            \boxed{|z|<|a|}
                                \]

                                </div>

                        </div>


                        <div class="mini-flow">

                            <div class="mini-flow-item">

                                <span>
                                    Sequence
                                </span>

                                <strong>
                                    \(-a^nu[-n-1]\)
                                </strong>

                            </div>


                            <div class="mini-arrow">
                                →
                            </div>


                            <div class="mini-flow-item">

                                <span>
                                    Transform
                                </span>

                                <strong>
                                    \(\frac{z}{z-a}\)
                                </strong>

                            </div>


                            <div class="mini-arrow">
                                →
                            </div>


                            <div class="mini-flow-item success-flow">

                                <span>
                                    ROC
                                </span>

                                <strong>
                                    \(|z|<|a|\)
                                        </strong>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
             TWO-SIDED SEQUENCES
        ================================================== -->

                <div class="theory-card">

                    <div class="theory-header">

                        <div>

                            <h4>
                                5. Two-Sided Sequences
                            </h4>

                            <p>
                                Sequences containing both left-sided and
                                right-sided components.
                            </p>

                        </div>

                        <div class="statement-number">
                            TWO-SIDED
                        </div>

                    </div>


                    <div class="theory-body">

                        <div class="definition-box">

                            <strong>
                                Definition
                            </strong>

                            <span>

                                A two-sided sequence contains samples
                                extending in both directions of the
                                time index. Its ROC is generally an
                                annulus bounded by poles.

                            </span>

                        </div>


                        <div class="equation-box">

                            \[
                            x[n]
                            =
                            a^n u[n]
                            -
                            b^n u[-n-1]
                            \]

                        </div>


                        <div class="section-label">

                            Z-Transform

                        </div>


                        <div class="equation-box">

                            \[
                            X(z)
                            =
                            \frac{1}{1-az^{-1}}
                            +
                            \frac{1}{1-bz^{-1}}
                            \]

                        </div>


                        <div class="result-box">

                            <strong>
                                ROC
                            </strong>

                            <span>

                                The right-sided component requires
                                \(|z|>|a|\), while the left-sided
                                component requires \(|z|<|b|\).

                                    Therefore, both conditions must be
                                    satisfied simultaneously.

                                    </span>

                                    <div class="roc-equation">

                                        \[
                                        \boxed{|a|<|z|<|b|}
                                            \]

                                            </div>

                                    </div>


                                    <div class="decision-flow">

                                        <div class="decision-item">

                                            <span>
                                                Right-sided component
                                            </span>

                                            <strong>
                                                \(|z|>|a|\)
                                            </strong>

                                        </div>


                                        <div class="decision-arrow">
                                            ↓
                                        </div>


                                        <div class="decision-item">

                                            <span>
                                                Left-sided component
                                            </span>

                                            <strong>
                                                \(|z|<|b|\)
                                                    </strong>

                                        </div>


                                        <div class="decision-arrow">
                                            ↓
                                        </div>


                                        <div class="decision-item active-decision">

                                            <span>
                                                Common ROC
                                            </span>

                                            <strong>
                                                \(|a|<|z|<|b|\)
                                                    </strong>

                                        </div>

                                    </div>

                        </div>

                    </div>


                    <!-- =================================================
             ROC RULES
        ================================================== -->

                    <div class="theory-card">

                        <div class="theory-header">

                            <div>

                                <h4>
                                    6. Important Rules of the ROC
                                </h4>

                                <p>
                                    Fundamental properties used when determining
                                    the region of convergence.
                                </p>

                            </div>

                            <div class="statement-number">
                                ROC RULES
                            </div>

                        </div>


                        <div class="theory-body">

                            <div class="key-rule-grid">


                                <div class="rule-item">

                                    <div class="rule-number">
                                        01
                                    </div>

                                    <strong>
                                        ROC contains no poles
                                    </strong>

                                    <span>

                                        The region of convergence cannot include
                                        a value of \(z\) at which \(X(z)\) has
                                        a pole.

                                    </span>

                                </div>


                                <div class="rule-item">

                                    <div class="rule-number">
                                        02
                                    </div>

                                    <strong>
                                        Right-sided sequence
                                    </strong>

                                    <span>

                                        The ROC is outside the outermost pole.

                                        \[
                                        |z|>|p_{\max}|
                                        \]

                                    </span>

                                </div>


                                <div class="rule-item">

                                    <div class="rule-number">
                                        03
                                    </div>

                                    <strong>
                                        Left-sided sequence
                                    </strong>

                                    <span>

                                        The ROC is inside the innermost pole.

                                        \[
                                        |z|<|p_{\min}|
                                            \]

                                            </span>

                                </div>


                                <div class="rule-item">

                                    <div class="rule-number">
                                        04
                                    </div>

                                    <strong>
                                        Two-sided sequence
                                    </strong>

                                    <span>

                                        The ROC is generally an annulus
                                        between poles.

                                    </span>

                                </div>


                                <div class="rule-item">

                                    <div class="rule-number">
                                        05
                                    </div>

                                    <strong>
                                        Causal rational system
                                    </strong>

                                    <span>

                                        For a causal rational system, the ROC
                                        extends outward from the outermost pole.

                                    </span>

                                </div>


                                <div class="rule-item">

                                    <div class="rule-number">
                                        06
                                    </div>

                                    <strong>
                                        Stable system
                                    </strong>

                                    <span>

                                        Stability requires the unit circle

                                        \[
                                        |z|=1
                                        \]

                                        to lie within the ROC.

                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
             POLES AND ROC
        ================================================== -->

                    <div class="theory-card">

                        <div class="theory-header">

                            <div>

                                <h4>
                                    7. Relationship Between Poles and ROC
                                </h4>

                                <p>
                                    The location of poles determines the possible
                                    boundaries of the ROC.
                                </p>

                            </div>

                            <div class="statement-number">
                                POLES
                            </div>

                        </div>


                        <div class="theory-body">


                            <div class="definition-box">

                                <strong>
                                    Example
                                </strong>

                                <span>

                                    Consider the rational expression

                                </span>

                            </div>


                            <div class="equation-box">

                                \[
                                X(z)=
                                \frac{z}
                                {(z-2)(z-4)}
                                \]

                            </div>


                            <div class="section-label">

                                Poles

                            </div>


                            <div class="pole-grid">

                                <div class="pole-item">

                                    <span>
                                        Pole 1
                                    </span>

                                    <strong>
                                        \(z=2\)
                                    </strong>

                                </div>


                                <div class="pole-item">

                                    <span>
                                        Pole 2
                                    </span>

                                    <strong>
                                        \(z=4\)
                                    </strong>

                                </div>

                            </div>


                            <div class="section-label">

                                Possible ROCs

                            </div>


                            <div class="comparison-wrapper">

                                <table class="comparison-table roc-table">

                                    <thead>

                                        <tr>

                                            <th>
                                                Sequence Type
                                            </th>

                                            <th>
                                                ROC
                                            </th>

                                            <th>
                                                Interpretation
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr>

                                            <td>
                                                Right-sided
                                            </td>

                                            <td>
                                                \(|z|>4\)
                                            </td>

                                            <td>
                                                Outside the outermost pole.
                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                Two-sided
                                            </td>

                                            <td>
                                                \(2<|z|<4\)
                                                    </td>

                                            <td>
                                                Between the two poles.
                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                Left-sided
                                            </td>

                                            <td>
                                                \(|z|<2\)
                                                    </td>

                                            <td>
                                                Inside the innermost pole.
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>


                            <div class="concept-box">

                                <strong>
                                    Important
                                </strong>

                                <span>

                                    The same algebraic expression for \(X(z)\)
                                    can correspond to different time-domain
                                    sequences depending on the ROC.

                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
             ROC AND STABILITY
        ================================================== -->

                    <div class="theory-card">

                        <div class="theory-header">

                            <div>

                                <h4>
                                    8. ROC and System Stability
                                </h4>

                                <p>
                                    Determining stability using the unit circle.
                                </p>

                            </div>

                            <div class="statement-number">
                                STABILITY
                            </div>

                        </div>


                        <div class="theory-body">

                            <div class="definition-box">

                                <strong>
                                    Stability Condition
                                </strong>

                                <span>

                                    For an LTI system to be BIBO stable, the
                                    unit circle must lie entirely inside the
                                    ROC of the system's Z-transform.

                                </span>

                            </div>


                            <div class="equation-box">

                                \[
                                \boxed{|z|=1 \text{ must be inside the ROC}}
                                \]

                            </div>


                            <div class="mini-flow">

                                <div class="mini-flow-item">

                                    <span>
                                        ROC
                                    </span>

                                    <strong>
                                        \(|z|>2\)
                                    </strong>

                                </div>


                                <div class="mini-arrow">
                                    →
                                </div>


                                <div class="mini-flow-item">

                                    <span>
                                        Unit Circle
                                    </span>

                                    <strong>
                                        \(|z|=1\)
                                    </strong>

                                </div>


                                <div class="mini-arrow">
                                    →
                                </div>


                                <div class="mini-flow-item success-flow">

                                    <span>
                                        Stability
                                    </span>

                                    <strong>
                                        NOT STABLE
                                    </strong>

                                </div>

                            </div>


                            <div class="result-box">

                                <strong>
                                    Why?
                                </strong>

                                <span>

                                    Since the unit circle \(|z|=1\) is not
                                    contained within \(|z|>2\), the system
                                    does not satisfy the ROC condition for
                                    BIBO stability.

                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
             ROC SUMMARY TABLE
        ================================================== -->

                    <div class="theory-card">

                        <div class="theory-header">

                            <div>

                                <h4>
                                    ROC Summary
                                </h4>

                                <p>
                                    Quick reference for determining the ROC
                                    from sequence characteristics.
                                </p>

                            </div>

                        </div>


                        <div class="comparison-wrapper">

                            <table class="comparison-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Sequence
                                        </th>

                                        <th>
                                            Typical ROC
                                        </th>

                                        <th>
                                            Location Relative to Poles
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr>

                                        <td>
                                            Right-sided / causal
                                        </td>

                                        <td>
                                            \(|z|>\text{outermost pole}\)
                                        </td>

                                        <td>
                                            Outside
                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Left-sided
                                        </td>

                                        <td>
                                            \(|z|<\text{innermost pole}\)
                                                </td>

                                        <td>
                                            Inside
                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Two-sided
                                        </td>

                                        <td>
                                            \(r_1 < |z| < r_2
                                            \)</td>

                                        <td>
                                            Between poles
                                        </td>

                                    </tr>


                                    <tr>

                                        <td>
                                            Finite-duration sequence
                                        </td>

                                        <td>
                                            Usually entire plane except
                                            possible \(z=0\) or \(z=\infty\)
                                        </td>

                                        <td>
                                            Depends on powers of \(z\)
                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>


                    <!-- =================================================
             APPLICATIONS
        ================================================== -->

                    <div class="theory-card">

                        <div class="theory-header">

                            <div>

                                <h4>
                                    Applications of Z-Transform and ROC
                                </h4>

                                <p>
                                    Engineering applications of Z-domain
                                    analysis.
                                </p>

                            </div>

                        </div>


                        <div class="application-grid">


                            <div class="application-item">

                                <i class="bi bi-filter-square"></i>

                                <strong>
                                    Digital Filters
                                </strong>

                                <span>

                                    Analyze poles, zeros, frequency response,
                                    and stability of digital filters.

                                </span>

                            </div>


                            <div class="application-item">

                                <i class="bi bi-cpu"></i>

                                <strong>
                                    DSP Systems
                                </strong>

                                <span>

                                    Represent discrete-time systems using
                                    transfer functions in the Z-domain.

                                </span>

                            </div>


                            <div class="application-item">

                                <i class="bi bi-activity"></i>

                                <strong>
                                    System Stability
                                </strong>

                                <span>

                                    Determine BIBO stability by checking
                                    whether the unit circle lies inside
                                    the ROC.

                                </span>

                            </div>


                            <div class="application-item">

                                <i class="bi bi-graph-up"></i>

                                <strong>
                                    Frequency Response
                                </strong>

                                <span>

                                    Evaluate \(X(z)\) on the unit circle
                                    to obtain frequency-domain information
                                    when the ROC includes it.

                                </span>

                            </div>


                            <div class="application-item">

                                <i class="bi bi-diagram-3"></i>

                                <strong>
                                    Difference Equations
                                </strong>

                                <span>

                                    Convert linear constant-coefficient
                                    difference equations into algebraic
                                    equations in \(z\).

                                </span>

                            </div>


                            <div class="application-item">

                                <i class="bi bi-broadcast-pin"></i>

                                <strong>
                                    Signal Analysis
                                </strong>

                                <span>

                                    Analyze discrete-time signals and
                                    determine their convergence properties.

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

                                    The Z-transform converts a discrete-time
                                    sequence into the complex \(z\)-domain.

                                </span>

                            </div>


                            <div>

                                <strong>
                                    02
                                </strong>

                                <span>

                                    The ROC specifies the values of \(z\)
                                    for which the transform converges.

                                </span>

                            </div>


                            <div>

                                <strong>
                                    03
                                </strong>

                                <span>

                                    The ROC cannot contain any poles of
                                    \(X(z)\).

                                </span>

                            </div>


                            <div>

                                <strong>
                                    04
                                </strong>

                                <span>

                                    Right-sided sequences generally have an
                                    ROC outside the outermost pole.

                                </span>

                            </div>


                            <div>

                                <strong>
                                    05
                                </strong>

                                <span>

                                    Left-sided sequences generally have an
                                    ROC inside the innermost pole.

                                </span>

                            </div>


                            <div>

                                <strong>
                                    06
                                </strong>

                                <span>

                                    Two-sided sequences generally have an
                                    annular ROC between poles.

                                </span>

                            </div>


                            <div>

                                <strong>
                                    07
                                </strong>

                                <span>

                                    The same algebraic expression \(X(z)\)
                                    may correspond to different sequences
                                    depending on the ROC.

                                </span>

                            </div>


                            <div>

                                <strong>
                                    08
                                </strong>

                                <span>

                                    For BIBO stability, the unit circle
                                    \(|z|=1\) must be inside the ROC.

                                </span>

                            </div>


                            <div>

                                <strong>
                                    09
                                </strong>

                                <span>

                                    Pole locations determine the possible
                                    boundaries of the ROC.

                                </span>

                            </div>

                        </div>

                    </div>


                </div>

    </main>


    <!-- =========================================================
     Z-TRANSFORM / ROC STYLES
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
           EQUATION
        ====================================================== */

        .equation-box {

            margin:
                14px 0;

            padding:
                14px 16px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

            color:
                var(--theory-text);

            text-align: center;

            overflow-x: auto;

            font-size: .9rem;

        }


        .equation-box mjx-container {

            margin:
                0 !important;

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
           ROC EXAMPLE
        ====================================================== */

        .roc-example {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 10px;

            margin-top: 16px;

        }


        .roc-example>div {

            display: flex;

            flex-direction: column;

            gap: 5px;

            padding:
                13px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

            text-align: center;

        }


        .roc-example span {

            color:
                var(--theory-muted);

            font-size: .62rem;

        }


        .roc-example strong {

            color:
                var(--theory-blue);

            font-family:
                "Courier New",
                monospace;

            font-size: .72rem;

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

            white-space: nowrap;

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

            vertical-align: middle;

        }


        .comparison-table td:first-child {

            font-weight: 700;

        }


        .comparison-table tr:hover td {

            background:
                var(--theory-hover);

        }


        /* =====================================================
           Z-TRANSFORM TABLE
        ====================================================== */

        .z-table th:nth-child(1) {

            width: 55px;

            text-align: center;

        }


        .z-table td:nth-child(1) {

            text-align: center;

            font-family:
                "Courier New",
                monospace;

            color:
                var(--theory-muted);

        }


        .z-table td:nth-child(2),
        .z-table td:nth-child(3),
        .z-table td:nth-child(4) {

            white-space: nowrap;

        }


        .z-table td:nth-child(3) {

            color:
                var(--theory-blue);

            font-family:
                "Courier New",
                monospace;

        }


        .z-table td:nth-child(4) {

            color:
                var(--theory-green);

            font-family:
                "Courier New",
                monospace;

            font-weight: 700;

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


        .roc-equation {

            margin-top: 5px;

            text-align: center;

            color:
                var(--theory-text);

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
           KEY ROC RULES
        ====================================================== */

        .key-rule-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

        }


        .rule-item {

            display: flex;

            flex-direction: column;

            gap: 7px;

            padding:
                17px;

            border-right:
                1px solid var(--theory-border);

            border-bottom:
                1px solid var(--theory-border);

        }


        .rule-item:nth-child(3n) {

            border-right: none;

        }


        .rule-item:nth-last-child(-n+3) {

            border-bottom: none;

        }


        .rule-number {

            color:
                var(--theory-blue);

            font-family:
                "Courier New",
                monospace;

            font-size: .65rem;

            font-weight: 800;

        }


        .rule-item strong {

            font-size: .72rem;

        }


        .rule-item span {

            color:
                var(--theory-muted);

            font-family:
                "Times New Roman",
                serif;

            font-size: .74rem;

            line-height: 1.5;

        }


        /* =====================================================
           POLE GRID
        ====================================================== */

        .pole-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 10px;

        }


        .pole-item {

            display: flex;

            flex-direction: column;

            gap: 5px;

            padding:
                14px;

            border:
                1px solid var(--theory-border);

            border-radius: 7px;

            background:
                var(--theory-hover);

            text-align: center;

        }


        .pole-item span {

            color:
                var(--theory-muted);

            font-size: .63rem;

        }


        .pole-item strong {

            color:
                var(--theory-red);

            font-family:
                "Courier New",
                monospace;

            font-size: .78rem;

        }


        /* =====================================================
           ROC TABLE
        ====================================================== */

        .roc-table td:nth-child(2) {

            color:
                var(--theory-green);

            font-family:
                "Courier New",
                monospace;

            font-weight: 700;

            white-space: nowrap;

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

            .roc-example {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .key-rule-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .rule-item:nth-child(3n) {

                border-right:
                    1px solid var(--theory-border);

            }


            .rule-item:nth-child(2n) {

                border-right: none;

            }


            .rule-item:nth-last-child(-n+3) {

                border-bottom:
                    1px solid var(--theory-border);

            }


            .rule-item:nth-last-child(-n+2) {

                border-bottom: none;

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


            .roc-example {

                grid-template-columns:
                    1fr;

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


            .pole-grid {

                grid-template-columns:
                    1fr;

            }


            .key-rule-grid {

                grid-template-columns:
                    1fr;

            }


            .rule-item,
            .rule-item:nth-child(2n),
            .rule-item:nth-child(3n) {

                border-right: none;

                border-bottom:
                    1px solid var(--theory-border);

            }


            .rule-item:last-child {

                border-bottom: none;

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
     MATHJAX
     Required for LaTeX equations
========================================================== -->

    <script>
        window.MathJax = {
            tex: {
                inlineMath: [
                    ['\\(', '\\)']
                ],
                displayMath: [
                    ['\\[', '\\]']
                ]
            }
        };
    </script>

    <script
        async
        src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js">
    </script>


    <!-- =========================================================
     GLOBAL SCRIPTS
========================================================== -->

    <?php include 'globals/scripts.php'; ?>


</body>

</html>
