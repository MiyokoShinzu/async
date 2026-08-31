
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
        body {
            background-color: #f8f9fa;
        }

        .hero {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: white;
            padding: 90px 0;
        }

        .hero h1 {
            font-weight: 700;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 15px;
        }

        .info-card {
            border: none;
            border-radius: 12px;
            transition: 0.3s;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .week-card {
            border: none;
            border-left: 5px solid #0d6efd;
            border-radius: 8px;
        }

        .login-btn {
            padding: 12px 30px;
            font-weight: 600;
        }

        footer {
            background-color: #212529;
            color: #adb5bd;
        }
    </style>
</head>

<body>

<!-- =========================
     NAVIGATION
========================= -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">

    <div class="container">

        <a class="navbar-brand fw-bold text-primary" href="index.php">
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

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link active" href="#home">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#about">
                        About
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#schedule">
                        Two-Week Plan
                    </a>
                </li>

                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a
                        href="login.php"
                        class="btn btn-primary px-4"
                    >
                        Login
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- =========================
     HERO SECTION
========================= -->
<section class="hero" id="home">

    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-8">

                <span class="badge bg-light text-primary mb-3 px-3 py-2">
                    CPE Department
                </span>

                <h1 class="display-4">
                    Welcome to Our
                    Asynchronous Class
                </h1>

                <p class="lead mt-4">
                    A two-week self-paced learning experience
                    designed to help you learn, explore, and
                    apply the concepts at your own pace.
                </p>

                <div class="mt-4">

                    <a
                        href="login.php"
                        class="btn btn-light btn-lg login-btn me-2"
                    >
                        Login to Class
                    </a>

                    <a
                        href="#about"
                        class="btn btn-outline-light btn-lg"
                    >
                        Learn More
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     INSTRUCTOR SECTION
========================= -->
<section class="py-5" id="about">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Your Instructor
            </h2>

            <p class="text-muted">
                Computer Engineering Department
            </p>

        </div>


        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="card info-card shadow-sm">

                    <div class="card-body p-4 p-md-5 text-center">

                        <h3 class="fw-bold">
                            Engr. Karl Stephen Evallo
                        </h3>

                        <p class="text-primary fw-semibold mb-3">
                            Computer Engineering Department
                        </p>

                        <p class="text-muted mb-0">
                            Welcome, everyone! During these two weeks,
                            we will be conducting our class asynchronously.
                            This means that you will have the flexibility
                            to access the learning materials, study the
                            lessons, accomplish the activities, and submit
                            your requirements within the given schedule.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     WHAT TO EXPECT
========================= -->
<section class="py-5 bg-white">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                What to Expect
            </h2>

            <p class="text-muted">
                Make the most of your two-week asynchronous learning experience.
            </p>

        </div>


        <div class="row g-4">

            <!-- Card 1 -->
            <div class="col-md-6 col-lg-3">

                <div class="card info-card shadow-sm h-100">

                    <div class="card-body text-center p-4">

                        <div class="fs-1 text-primary mb-3">
                            📚
                        </div>

                        <h5 class="fw-bold">
                            Learn
                        </h5>

                        <p class="text-muted">
                            Study the provided lessons and
                            learning materials carefully.
                        </p>

                    </div>

                </div>

            </div>


            <!-- Card 2 -->
            <div class="col-md-6 col-lg-3">

                <div class="card info-card shadow-sm h-100">

                    <div class="card-body text-center p-4">

                        <div class="fs-1 text-success mb-3">
                            🎥
                        </div>

                        <h5 class="fw-bold">
                            Explore
                        </h5>

                        <p class="text-muted">
                            Watch the instructional materials
                            and explore the examples provided.
                        </p>

                    </div>

                </div>

            </div>


            <!-- Card 3 -->
            <div class="col-md-6 col-lg-3">

                <div class="card info-card shadow-sm h-100">

                    <div class="card-body text-center p-4">

                        <div class="fs-1 text-warning mb-3">
                            💻
                        </div>

                        <h5 class="fw-bold">
                            Practice
                        </h5>

                        <p class="text-muted">
                            Apply what you have learned through
                            activities and exercises.
                        </p>

                    </div>

                </div>

            </div>


            <!-- Card 4 -->
            <div class="col-md-6 col-lg-3">

                <div class="card info-card shadow-sm h-100">

                    <div class="card-body text-center p-4">

                        <div class="fs-1 text-danger mb-3">
                            📝
                        </div>

                        <h5 class="fw-bold">
                            Submit
                        </h5>

                        <p class="text-muted">
                            Complete and submit your required
                            activities within the given deadline.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     TWO-WEEK PLAN
========================= -->
<section class="py-5" id="schedule">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Our Two-Week Asynchronous Plan
            </h2>

            <p class="text-muted">
                A simple guide to help you organize your learning.
            </p>

        </div>


        <div class="row g-4">

            <!-- WEEK 1 -->
            <div class="col-lg-6">

                <div class="card week-card shadow-sm h-100">

                    <div class="card-body p-4">

                        <span class="badge bg-primary mb-3">
                            WEEK 1
                        </span>

                        <h4 class="fw-bold">
                            Understanding the Lessons
                        </h4>

                        <p class="text-muted">
                            During the first week, focus on understanding
                            the fundamental concepts and learning materials.
                        </p>

                        <ul class="text-muted">

                            <li class="mb-2">
                                Access and read the provided lessons.
                            </li>

                            <li class="mb-2">
                                Watch the instructional videos.
                            </li>

                            <li class="mb-2">
                                Review examples and demonstrations.
                            </li>

                            <li class="mb-2">
                                Take notes on important concepts.
                            </li>

                            <li>
                                Accomplish the assigned activities.
                            </li>

                        </ul>

                    </div>

                </div>

            </div>


            <!-- WEEK 2 -->
            <div class="col-lg-6">

                <div class="card week-card shadow-sm h-100">

                    <div class="card-body p-4">

                        <span class="badge bg-success mb-3">
                            WEEK 2
                        </span>

                        <h4 class="fw-bold">
                            Application and Assessment
                        </h4>

                        <p class="text-muted">
                            During the second week, apply the concepts
                            you have learned and complete the required
                            assessments and activities.
                        </p>

                        <ul class="text-muted">

                            <li class="mb-2">
                                Review the previous lessons.
                            </li>

                            <li class="mb-2">
                                Work on the assigned exercises.
                            </li>

                            <li class="mb-2">
                                Complete the required activities.
                            </li>

                            <li class="mb-2">
                                Review your answers and outputs.
                            </li>

                            <li>
                                Submit your requirements before the deadline.
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     IMPORTANT REMINDERS
========================= -->
<section class="py-5 bg-white">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-9">

                <div class="alert alert-primary shadow-sm">

                    <h4 class="alert-heading fw-bold">
                        Important Reminders
                    </h4>

                    <hr>

                    <ul class="mb-0">

                        <li class="mb-2">
                            Log in regularly to check your lessons
                            and activities.
                        </li>

                        <li class="mb-2">
                            Read the instructions carefully before
                            answering an activity.
                        </li>

                        <li class="mb-2">
                            Manage your time and do not wait until
                            the last minute to submit your requirements.
                        </li>

                        <li class="mb-2">
                            Make sure your submissions are complete
                            and properly prepared.
                        </li>

                        <li>
                            If you encounter difficulties, communicate
                            with your instructor through the designated
                            communication channel.
                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     LOGIN CTA
========================= -->
<section class="py-5 bg-primary text-white">

    <div class="container text-center">

        <h2 class="fw-bold">
            Ready to Start Learning?
        </h2>

        <p class="lead mb-4">
            Log in to access your lessons, activities,
            and other course materials.
        </p>

        <a
            href="login.php"
            class="btn btn-light btn-lg px-5 fw-semibold"
        >
            Login to Class
        </a>

    </div>

</section>


<!-- =========================
     FOOTER
========================= -->
<footer class="py-4">

    <div class="container text-center">

        <p class="mb-1">
            <strong>Engr. Karl Stephen Evallo</strong>
        </p>

        <p class="mb-1">
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

