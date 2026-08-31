
<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Asynchronous Class | CPE Department</title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        /* =========================
           COLOR PALETTE
        ========================= */

        :root {
            --academic-blue: #0B4F8A;
            --academic-blue-dark: #083B66;
            --academic-blue-light: #EAF3FA;
            --text-dark: #212529;
            --text-muted: #6C757D;
            --border-color: #DEE2E6;
            --soft-bg: #F7F9FB;
        }


        /* =========================
           GENERAL
        ========================= */

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text-dark);
            background: #FFFFFF;
        }

        section {
            scroll-margin-top: 80px;
        }

        .text-academic {
            color: var(--academic-blue);
        }


        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            background: #FFFFFF;
            border-bottom: 1px solid var(--border-color);
        }

        .navbar-brand {
            color: var(--academic-blue) !important;
            font-weight: 700;
        }

        .nav-link {
            color: #495057;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--academic-blue);
        }

        .btn-academic {
            background-color: var(--academic-blue);
            border-color: var(--academic-blue);
            color: #FFFFFF;
            font-weight: 600;
        }

        .btn-academic:hover {
            background-color: var(--academic-blue-dark);
            border-color: var(--academic-blue-dark);
            color: #FFFFFF;
        }


        /* =========================
           HERO
        ========================= */

        .hero {
            background: #FFFFFF;
            padding: 90px 0 100px;
            border-bottom: 1px solid var(--border-color);
        }

        .hero-badge {
            display: inline-block;
            background: var(--academic-blue-light);
            color: var(--academic-blue);
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            line-height: 1.15;
            color: var(--academic-blue-dark);
        }

        .hero p {
            font-size: 19px;
            line-height: 1.7;
            color: var(--text-muted);
            max-width: 750px;
        }


        /* =========================
           SECTION
        ========================= */

        .section-padding {
            padding: 75px 0;
        }

        .section-soft {
            background: var(--soft-bg);
        }

        .section-title {
            color: var(--academic-blue-dark);
            font-weight: 700;
        }

        .section-subtitle {
            color: var(--text-muted);
            max-width: 700px;
            margin: auto;
        }


        /* =========================
           INSTRUCTOR
        ========================= */

        .instructor-card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: #FFFFFF;
        }

        .instructor-line {
            width: 60px;
            height: 4px;
            background: var(--academic-blue);
            margin: 0 auto 20px;
        }

        .instructor-name {
            color: var(--academic-blue-dark);
            font-weight: 700;
        }


        /* =========================
           INFORMATION CARDS
        ========================= */

        .info-card {
            height: 100%;
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: 0.2s ease;
        }

        .info-card:hover {
            border-color: var(--academic-blue);
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.06);
            transform: translateY(-3px);
        }

        .card-number {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--academic-blue-light);
            color: var(--academic-blue);
            font-weight: 700;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .info-card h5 {
            color: var(--academic-blue-dark);
            font-weight: 700;
        }

        .info-card p {
            color: var(--text-muted);
            line-height: 1.6;
        }


        /* =========================
           WEEK CARDS
        ========================= */

        .week-card {
            background: #FFFFFF;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            height: 100%;
        }

        .week-header {
            background: var(--academic-blue-light);
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
        }

        .week-label {
            color: var(--academic-blue);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .week-title {
            color: var(--academic-blue-dark);
            font-weight: 700;
            margin-top: 5px;
        }

        .week-body {
            padding: 25px;
        }

        .week-body li {
            margin-bottom: 12px;
            color: var(--text-muted);
        }


        /* =========================
           REMINDERS
        ========================= */

        .reminder-box {
            border-left: 5px solid var(--academic-blue);
            background: var(--academic-blue-light);
            padding: 25px;
            border-radius: 5px;
        }

        .reminder-box h4 {
            color: var(--academic-blue-dark);
            font-weight: 700;
        }


        /* =========================
           LOGIN CTA
        ========================= */

        .login-section {
            background: var(--academic-blue);
            padding: 65px 0;
        }

        .login-section h2 {
            color: #FFFFFF;
            font-weight: 700;
        }

        .login-section p {
            color: #EAF3FA;
        }

        .btn-login-white {
            background: #FFFFFF;
            color: var(--academic-blue);
            border: 2px solid #FFFFFF;
            font-weight: 700;
            padding: 12px 35px;
        }

        .btn-login-white:hover {
            background: #F1F1F1;
            color: var(--academic-blue-dark);
        }


        /* =========================
           FOOTER
        ========================= */

        footer {
            background: var(--academic-blue-dark);
            color: #DCE8F2;
        }

        footer strong {
            color: #FFFFFF;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 768px) {

            .hero {
                padding: 60px 0 70px;
            }

            .hero h1 {
                font-size: 36px;
            }

            .hero p {
                font-size: 17px;
            }

            .section-padding {
                padding: 55px 0;
            }

        }

    </style>

</head>


<body>


<!-- =====================================
     NAVIGATION
===================================== -->

<nav class="navbar navbar-expand-lg sticky-top">

    <div class="container">

        <a
            class="navbar-brand"
            href="index.php"
        >
            CPE Department
        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
        >
            <span class="navbar-toggler-icon"></span>
        </button>


        <div
            class="collapse navbar-collapse"
            id="navbarNav"
        >

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a
                        class="nav-link active"
                        href="#home"
                    >
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#about"
                    >
                        About
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="#plan"
                    >
                        Learning Plan
                    </a>
                </li>

                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">

                    <a
                        href="login.php"
                        class="btn btn-academic px-4"
                    >
                        Login
                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>



<!-- =====================================
     HERO
===================================== -->

<section
    class="hero"
    id="home"
>

    <div class="container">

        <div class="row">

            <div class="col-lg-9">

                <span class="hero-badge">
                    COMPUTER ENGINEERING DEPARTMENT
                </span>


                <h1 class="mb-4">

                    Welcome to Our
                    <span class="text-academic">
                        Asynchronous Class
                    </span>

                </h1>


                <p class="mb-4">

                    Welcome, students. For the next two weeks,
                    we will be conducting our class through an
                    asynchronous learning approach. This learning
                    environment will allow you to access the lessons,
                    study the provided materials, accomplish activities,
                    and complete assessments at your own pace within
                    the prescribed schedule.

                </p>


                <div class="mt-4">

                    <a
                        href="login.php"
                        class="btn btn-academic btn-lg px-4 me-2"
                    >
                        Login to Class
                    </a>


                    <a
                        href="#plan"
                        class="btn btn-outline-secondary btn-lg px-4"
                    >
                        View Learning Plan
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================
     ABOUT THE CLASS
===================================== -->

<section
    class="section-padding"
    id="about"
>

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                About Our Asynchronous Class
            </h2>

            <p class="section-subtitle mt-3">

                This two-week learning period is designed to
                encourage independent learning, active participation,
                and practical application of the concepts discussed
                in our course.

            </p>

        </div>


        <div class="row justify-content-center">

            <div class="col-lg-9">

                <div class="instructor-card shadow-sm">

                    <div class="card-body text-center p-4 p-md-5">

                        <div class="instructor-line"></div>


                        <h3 class="instructor-name">
                            Engr. Karl Stephen Evallo
                        </h3>


                        <p class="text-academic fw-semibold mb-4">
                            Computer Engineering Department
                        </p>


                        <p class="text-muted mb-0">

                            My goal during this asynchronous class
                            is to provide you with the necessary learning
                            resources and activities that will guide you
                            throughout the two-week period. I encourage
                            everyone to manage their time effectively,
                            study the lessons carefully, and actively
                            participate in the activities provided.

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================
     WHAT YOU WILL DO
===================================== -->

<section class="section-padding section-soft">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Your Learning Journey
            </h2>

            <p class="section-subtitle mt-3">

                Throughout the two weeks, you will go through
                four important stages of learning.

            </p>

        </div>


        <div class="row g-4">


            <!-- LEARN -->

            <div class="col-md-6 col-lg-3">

                <div class="info-card p-4">

                    <div class="card-number">
                        01
                    </div>

                    <h5>
                        Learn
                    </h5>

                    <p>
                        Access and study the lessons and
                        instructional materials provided
                        for the course.
                    </p>

                </div>

            </div>


            <!-- UNDERSTAND -->

            <div class="col-md-6 col-lg-3">

                <div class="info-card p-4">

                    <div class="card-number">
                        02
                    </div>

                    <h5>
                        Understand
                    </h5>

                    <p>
                        Review the examples, demonstrations,
                        and explanations to strengthen your
                        understanding of the concepts.
                    </p>

                </div>

            </div>


            <!-- APPLY -->

            <div class="col-md-6 col-lg-3">

                <div class="info-card p-4">

                    <div class="card-number">
                        03
                    </div>

                    <h5>
                        Apply
                    </h5>

                    <p>
                        Complete the activities and exercises
                        designed to help you apply what you
                        have learned.
                    </p>

                </div>

            </div>


            <!-- SUBMIT -->

            <div class="col-md-6 col-lg-3">

                <div class="info-card p-4">

                    <div class="card-number">
                        04
                    </div>

                    <h5>
                        Submit
                    </h5>

                    <p>
                        Complete your requirements and submit
                        your outputs within the specified
                        deadline.
                    </p>

                </div>

            </div>


        </div>

    </div>

</section>



<!-- =====================================
     TWO WEEK PLAN
===================================== -->

<section
    class="section-padding"
    id="plan"
>

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Two-Week Learning Plan
            </h2>

            <p class="section-subtitle mt-3">

                Use this plan as a guide for organizing your
                learning activities during the asynchronous period.

            </p>

        </div>


        <div class="row g-4">


            <!-- WEEK 1 -->

            <div class="col-lg-6">

                <div class="week-card shadow-sm">

                    <div class="week-header">

                        <div class="week-label">
                            WEEK 01
                        </div>

                        <h4 class="week-title mb-0">
                            Learn and Understand
                        </h4>

                    </div>


                    <div class="week-body">

                        <p class="text-muted">
                            Focus on understanding the fundamental
                            concepts and completing the initial
                            learning activities.
                        </p>


                        <ul>

                            <li>
                                Read and study the assigned lessons.
                            </li>

                            <li>
                                Watch the provided instructional
                                videos and demonstrations.
                            </li>

                            <li>
                                Review examples and important concepts.
                            </li>

                            <li>
                                Take notes while studying.
                            </li>

                            <li>
                                Complete the assigned activities.
                            </li>

                        </ul>

                    </div>

                </div>

            </div>



            <!-- WEEK 2 -->

            <div class="col-lg-6">

                <div class="week-card shadow-sm">

                    <div class="week-header">

                        <div class="week-label">
                            WEEK 02
                        </div>

                        <h4 class="week-title mb-0">
                            Apply and Assess
                        </h4>

                    </div>


                    <div class="week-body">

                        <p class="text-muted">
                            Apply your knowledge through exercises,
                            activities, and assessments.
                        </p>


                        <ul>

                            <li>
                                Review the concepts from Week 1.
                            </li>

                            <li>
                                Complete the assigned exercises.
                            </li>

                            <li>
                                Apply the concepts to the given
                                problems or activities.
                            </li>

                            <li>
                                Review and verify your outputs.
                            </li>

                            <li>
                                Submit all required requirements
                                before the deadline.
                            </li>

                        </ul>

                    </div>

                </div>

            </div>


        </div>

    </div>

</section>



<!-- =====================================
     IMPORTANT REMINDERS
===================================== -->

<section class="section-padding section-soft">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-9">

                <div class="reminder-box">

                    <h4 class="mb-3">
                        Important Reminders
                    </h4>


                    <ul class="mb-0 text-muted">

                        <li class="mb-2">
                            Check the learning portal regularly
                            for lessons and announcements.
                        </li>

                        <li class="mb-2">
                            Read the instructions for every activity
                            carefully before submitting your work.
                        </li>

                        <li class="mb-2">
                            Manage your time properly throughout
                            the two-week asynchronous period.
                        </li>

                        <li class="mb-2">
                            Complete all activities within the
                            prescribed schedule.
                        </li>

                        <li>
                            If you encounter difficulties, contact
                            your instructor through the designated
                            communication channel.
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- =====================================
     LOGIN CALL TO ACTION
===================================== -->

<section class="login-section">

    <div class="container text-center">

        <h2>
            Ready to Begin?
        </h2>

        <p class="lead mt-3 mb-4">

            Log in to access your lessons,
            activities, and learning materials.

        </p>


        <a
            href="login.php"
            class="btn btn-login-white btn-lg"
        >
            Login to Class
        </a>

    </div>

</section>



<!-- =====================================
     FOOTER
===================================== -->

<footer class="py-4">

    <div class="container text-center">

        <p class="mb-1">

            <strong>
                Engr. Karl Stephen Evallo
            </strong>

        </p>


        <p class="mb-2">

            Computer Engineering Department

        </p>


        <small>

            &copy; <?php echo date("Y"); ?>

            All Rights Reserved.

        </small>

    </div>

</footer>



<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>

