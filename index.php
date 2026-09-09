<?php
/* =========================================================
   ETS-Async
   Asynchronous Class Learning Portal
   ========================================================= */
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="ETS-Async — Asynchronous Learning Portal for Computer Engineering.">

    <meta
        name="theme-color"
        content="#1e3a8a">

    <title>
        ETS-Async | Asynchronous Learning Portal
    </title>


    <!-- =====================================================
         FAVICON
    ====================================================== -->

    <link
        rel="icon"
        type="image/png"
        href="./assets/pubmat/head.png">


    <!-- =====================================================
         FONTS
    ====================================================== -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">


    <!-- =====================================================
         BOOTSTRAP
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =====================================================
         BOOTSTRAP ICONS
    ====================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- =====================================================
         CUSTOM CSS
    ====================================================== -->

    <style>
        /* =================================================
           VARIABLES
        ================================================= */

        :root {

            --primary: #1e3a8a;
            --primary-light: #2563eb;
            --primary-soft: #eff6ff;

            --text: #172033;
            --text-muted: #667085;

            --border: #e4e7ec;

            --background: #ffffff;
            --surface: #f8fafc;

            --max-width: 1120px;
        }


        /* =================================================
           GLOBAL
        ================================================= */

        html {

            scroll-behavior:
                smooth;
        }


        body {

            margin:
                0;

            color:
                var(--text);

            background:
                var(--background);

            font-family:
                "Inter",
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            line-height:
                1.6;

            -webkit-font-smoothing:
                antialiased;
        }


        a {

            text-decoration:
                none;
        }


        /* =================================================
           CONTAINER
        ================================================= */

        .container-custom {

            width:
                min(100% - 40px, var(--max-width));

            margin:
                0 auto;
        }


        /* =================================================
           HEADER
        ================================================= */

        .site-header {

            height:
                72px;

            border-bottom:
                1px solid var(--border);

            background:
                #ffffff;

            display:
                flex;

            align-items:
                center;
        }


        .header-inner {

            width:
                min(100% - 40px, var(--max-width));

            margin:
                0 auto;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;
        }


        .brand {

            display:
                flex;

            align-items:
                center;

            gap:
                11px;

            color:
                var(--text);
        }


        .brand-logo {

            width:
                36px;

            height:
                36px;

            object-fit:
                contain;
        }


        .brand-name {

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                1rem;

            font-weight:
                700;

            letter-spacing:
                -.02em;
        }


        .brand-subtitle {

            margin-left:
                3px;

            padding-left:
                11px;

            border-left:
                1px solid var(--border);

            color:
                var(--text-muted);

            font-size:
                .78rem;
        }


        .header-login {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                7px;

            padding:
                9px 16px;

            color:
                #ffffff;

            background:
                var(--primary);

            border-radius:
                7px;

            font-size:
                .86rem;

            font-weight:
                600;

            transition:
                background .2s ease;
        }


        .header-login:hover {

            color:
                #ffffff;

            background:
                #172e6b;
        }


        /* =================================================
           HERO
        ================================================= */

        .hero {

            padding:
                115px 0 120px;

            border-bottom:
                1px solid var(--border);

            background:
                #ffffff;
        }


        .hero-content {

            max-width:
                820px;

            margin:
                0 auto;

            text-align:
                center;
        }


        .academic-label {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                8px;

            margin-bottom:
                24px;

            color:
                var(--primary);

            font-size:
                .76rem;

            font-weight:
                700;

            letter-spacing:
                .1em;

            text-transform:
                uppercase;
        }


        .academic-label::before {

            content:
                "";

            width:
                24px;

            height:
                1px;

            background:
                var(--primary);
        }


        .hero h1 {

            margin:
                0 0 22px;

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(2.3rem, 6vw, 4rem);

            line-height:
                1.12;

            letter-spacing:
                -.045em;

            font-weight:
                700;
        }


        .hero h1 span {

            display:
                block;

            color:
                var(--primary-light);
        }


        .hero-description {

            max-width:
                690px;

            margin:
                0 auto 34px;

            color:
                var(--text-muted);

            font-size:
                1rem;

            line-height:
                1.8;
        }


        .hero-button {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                9px;

            min-height:
                46px;

            padding:
                0 21px;

            color:
                #ffffff;

            background:
                var(--primary);

            border:
                1px solid var(--primary);

            border-radius:
                7px;

            font-size:
                .88rem;

            font-weight:
                600;

            transition:
                background .2s ease,
                transform .2s ease;
        }


        .hero-button:hover {

            color:
                #ffffff;

            background:
                #172e6b;

            transform:
                translateY(-1px);
        }


        /* =================================================
           ACADEMIC INFORMATION
        ================================================= */

        .academic-section {

            padding:
                95px 0;
        }


        .section-header {

            max-width:
                650px;

            margin-bottom:
                48px;
        }


        .section-label {

            margin-bottom:
                10px;

            color:
                var(--primary);

            font-size:
                .73rem;

            font-weight:
                700;

            letter-spacing:
                .1em;

            text-transform:
                uppercase;
        }


        .section-header h2 {

            margin:
                0 0 14px;

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(1.8rem, 4vw, 2.5rem);

            line-height:
                1.2;

            letter-spacing:
                -.035em;
        }


        .section-header p {

            margin:
                0;

            color:
                var(--text-muted);

            font-size:
                .95rem;
        }


        /* =================================================
           ACADEMIC CARDS
        ================================================= */

        .academic-grid {

            display:
                grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap:
                20px;
        }


        .academic-card {

            padding:
                30px;

            border:
                1px solid var(--border);

            border-radius:
                10px;

            background:
                #ffffff;
        }


        .academic-card-icon {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            width:
                42px;

            height:
                42px;

            margin-bottom:
                22px;

            border-radius:
                8px;

            background:
                var(--primary-soft);

            color:
                var(--primary-light);

            font-size:
                1rem;
        }


        .academic-card h3 {

            margin:
                0 0 9px;

            font-size:
                .98rem;

            font-weight:
                700;
        }


        .academic-card p {

            margin:
                0;

            color:
                var(--text-muted);

            font-size:
                .86rem;

            line-height:
                1.7;
        }


        /* =================================================
           CLASS INFORMATION
        ================================================= */

        .class-section {

            padding:
                95px 0;

            background:
                var(--surface);

            border-top:
                1px solid var(--border);

            border-bottom:
                1px solid var(--border);
        }


        .class-layout {

            display:
                grid;

            grid-template-columns:
                1fr 1fr;

            gap:
                70px;

            align-items:
                start;
        }


        .class-title {

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(1.7rem, 4vw, 2.3rem);

            line-height:
                1.25;

            letter-spacing:
                -.035em;

            margin:
                0 0 16px;
        }


        .class-description {

            margin:
                0;

            color:
                var(--text-muted);

            font-size:
                .93rem;

            line-height:
                1.8;
        }


        .class-points {

            display:
                flex;

            flex-direction:
                column;

            gap:
                20px;
        }


        .class-point {

            display:
                flex;

            gap:
                16px;
        }


        .class-point-number {

            flex:
                0 0 auto;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            width:
                32px;

            height:
                32px;

            border:
                1px solid #cbd5e1;

            border-radius:
                50%;

            color:
                var(--primary);

            background:
                #ffffff;

            font-size:
                .73rem;

            font-weight:
                700;
        }


        .class-point h3 {

            margin:
                0 0 4px;

            font-size:
                .9rem;

            font-weight:
                700;
        }


        .class-point p {

            margin:
                0;

            color:
                var(--text-muted);

            font-size:
                .83rem;

            line-height:
                1.6;
        }


        /* =================================================
           GUIDELINES
        ================================================= */

        .guidelines {

            padding:
                95px 0;
        }


        .guideline-list {

            max-width:
                900px;

            display:
                grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap:
                20px;
        }


        .guideline {

            padding:
                25px 0;

            border-top:
                1px solid var(--border);
        }


        .guideline-number {

            margin-bottom:
                15px;

            color:
                #98a2b3;

            font-size:
                .72rem;

            font-weight:
                700;
        }


        .guideline h3 {

            margin:
                0 0 7px;

            font-size:
                .93rem;

            font-weight:
                700;
        }


        .guideline p {

            margin:
                0;

            color:
                var(--text-muted);

            font-size:
                .83rem;

            line-height:
                1.7;
        }


        /* =================================================
           LOGIN CTA
        ================================================= */

        .access-section {

            padding:
                100px 0;

            border-top:
                1px solid var(--border);

            text-align:
                center;
        }


        .access-content {

            max-width:
                620px;

            margin:
                0 auto;
        }


        .access-content h2 {

            margin:
                0 0 13px;

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(1.8rem, 4vw, 2.4rem);

            letter-spacing:
                -.035em;
        }


        .access-content p {

            margin:
                0 auto 27px;

            color:
                var(--text-muted);

            font-size:
                .92rem;
        }


        /* =================================================
           FOOTER
        ================================================= */

        .footer {

            padding:
                27px 0;

            border-top:
                1px solid var(--border);

            background:
                #ffffff;
        }


        .footer-inner {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                20px;
        }


        .footer-brand {

            display:
                flex;

            align-items:
                center;

            gap:
                8px;

            color:
                var(--text);

            font-size:
                .84rem;

            font-weight:
                600;
        }


        .footer-brand img {

            width:
                28px;

            height:
                28px;

            object-fit:
                contain;
        }


        .footer-copy {

            margin:
                0;

            color:
                #98a2b3;

            font-size:
                .76rem;
        }


        /* =================================================
           RESPONSIVE
        ================================================= */

        @media (max-width: 900px) {

            .class-layout {

                grid-template-columns:
                    1fr;

                gap:
                    45px;
            }


            .academic-grid {

                grid-template-columns:
                    repeat(2, 1fr);
            }


            .guideline-list {

                grid-template-columns:
                    repeat(2, 1fr);
            }

        }


        @media (max-width: 767px) {

            .container-custom {

                width:
                    min(100% - 32px, var(--max-width));
            }


            .site-header {

                height:
                    64px;
            }


            .header-inner {

                width:
                    min(100% - 32px, var(--max-width));
            }


            .brand-subtitle {

                display:
                    none;
            }


            .header-login {

                padding:
                    8px 13px;
            }


            .hero {

                padding:
                    75px 0 80px;
            }


            .hero h1 {

                font-size:
                    clamp(2.1rem, 11vw, 3rem);
            }


            .hero-description {

                font-size:
                    .9rem;
            }


            .academic-section,
            .class-section,
            .guidelines {

                padding:
                    70px 0;
            }


            .academic-grid,
            .guideline-list {

                grid-template-columns:
                    1fr;
            }


            .academic-card {

                padding:
                    24px;
            }


            .access-section {

                padding:
                    75px 0;
            }


            .footer-inner {

                flex-direction:
                    column;

                justify-content:
                    center;

                text-align:
                    center;
            }

        }


        /* =================================================
           REDUCED MOTION
        ================================================= */

        @media (prefers-reduced-motion: reduce) {

            html {

                scroll-behavior:
                    auto;
            }

            *,
            *::before,
            *::after {

                transition:
                    none !important;

                animation:
                    none !important;
            }

        }
    </style>

</head>


<body>


    <!-- =====================================================
     HEADER
====================================================== -->

    <header class="site-header">

        <div class="header-inner">


            <a
                href="#home"
                class="brand">

                <img
                    src="./assets/pubmat/head.png"
                    alt="ETS-Async"
                    class="brand-logo"
                    width="36"
                    height="36"
                    loading="eager"
                    decoding="async">

                <span class="brand-name">
                    ETS-Async
                </span>

                <span class="brand-subtitle">
                    Asynchronous Learning Portal
                </span>

            </a>


            <a
                href="login.php"
                class="header-login">

                <i class="bi bi-box-arrow-in-right"></i>

                Login

            </a>

        </div>

    </header>



    <!-- =====================================================
     MAIN
====================================================== -->

    <main>


        <!-- =====================================================
     HERO
====================================================== -->

        <section
            id="home"
            class="hero">

            <div class="container-custom">

                <div class="hero-content">


                    <div class="academic-label">

                        Computer Engineering

                    </div>


                    <h1>

                        Asynchronous Learning
                        <span>for Your Course</span>

                    </h1>


                    <p class="hero-description">

                        ETS-Async provides students with structured access
                        to course lectures, learning materials, academic
                        activities, and required submissions through a
                        dedicated asynchronous learning environment.

                    </p>


                    <a
                        href="login.php"
                        class="hero-button">

                        <i class="bi bi-box-arrow-in-right"></i>

                        Access Your Course

                    </a>

                </div>

            </div>

        </section>



        <!-- =====================================================
     ACADEMIC RESOURCES
====================================================== -->

        <section
            id="about"
            class="academic-section">

            <div class="container-custom">


                <div class="section-header">

                    <div class="section-label">
                        Course Environment
                    </div>


                    <h2>
                        Everything you need for your asynchronous class.
                    </h2>


                    <p>

                        The platform provides a centralized environment
                        for accessing assigned resources and completing
                        academic requirements.

                    </p>

                </div>


                <div class="academic-grid">


                    <article class="academic-card">

                        <div class="academic-card-icon">

                            <i class="bi bi-journal-text"></i>

                        </div>


                        <h3>
                            Lectures and Materials
                        </h3>


                        <p>

                            Access assigned lectures, references,
                            and other instructional materials provided
                            for the course.

                        </p>

                    </article>



                    <article class="academic-card">

                        <div class="academic-card-icon">

                            <i class="bi bi-clipboard-check"></i>

                        </div>


                        <h3>
                            Academic Activities
                        </h3>


                        <p>

                            Complete assigned activities and submit
                            academic requirements according to the
                            instructions provided.

                        </p>

                    </article>



                    <article class="academic-card">

                        <div class="academic-card-icon">

                            <i class="bi bi-bar-chart"></i>

                        </div>


                        <h3>
                            Learning Progress
                        </h3>


                        <p>

                            Review your activity status and monitor
                            your progress throughout the asynchronous
                            learning period.

                        </p>

                    </article>


                </div>

            </div>

        </section>



        <!-- =====================================================
     CLASS INFORMATION
====================================================== -->

        <section class="class-section">

            <div class="container-custom">


                <div class="class-layout">


                    <div>

                        <div class="section-label">
                            Asynchronous Instruction
                        </div>


                        <h2 class="class-title">

                            Learn at your own time,
                            within the course requirements.

                        </h2>


                        <p class="class-description">

                            Asynchronous instruction allows students to
                            access learning materials and complete assigned
                            academic work without requiring simultaneous
                            online attendance.

                        </p>

                    </div>



                    <div class="class-points">


                        <div class="class-point">

                            <div class="class-point-number">
                                01
                            </div>


                            <div>

                                <h3>
                                    Access
                                </h3>

                                <p>

                                    Sign in to your account to access
                                    the materials and activities assigned
                                    to your course.

                                </p>

                            </div>

                        </div>



                        <div class="class-point">

                            <div class="class-point-number">
                                02
                            </div>


                            <div>

                                <h3>
                                    Study
                                </h3>

                                <p>

                                    Review the assigned instructional
                                    materials and follow the directions
                                    provided for each activity.

                                </p>

                            </div>

                        </div>



                        <div class="class-point">

                            <div class="class-point-number">
                                03
                            </div>


                            <div>

                                <h3>
                                    Complete
                                </h3>

                                <p>

                                    Submit the required academic activities
                                    within the designated period.

                                </p>

                            </div>

                        </div>


                    </div>

                </div>

            </div>

        </section>



        <!-- =====================================================
     ACADEMIC GUIDELINES
====================================================== -->

        <section
            id="guidelines"
            class="guidelines">

            <div class="container-custom">


                <div class="section-header">

                    <div class="section-label">
                        Academic Guidelines
                    </div>


                    <h2>
                        Please observe the following.
                    </h2>


                    <p>

                        Responsible participation is essential in
                        maintaining an effective asynchronous learning
                        environment.

                    </p>

                </div>


                <div class="guideline-list">


                    <article class="guideline">

                        <div class="guideline-number">
                            01
                        </div>


                        <h3>
                            Use your assigned account
                        </h3>


                        <p>

                            Use your official account when accessing the
                            platform so that your activities and progress
                            can be properly associated with you.

                        </p>

                    </article>



                    <article class="guideline">

                        <div class="guideline-number">
                            02
                        </div>


                        <h3>
                            Observe activity instructions
                        </h3>


                        <p>

                            Read the instructions for each lecture and
                            activity carefully before completing the
                            corresponding requirement.

                        </p>

                    </article>



                    <article class="guideline">

                        <div class="guideline-number">
                            03
                        </div>


                        <h3>
                            Observe deadlines
                        </h3>


                        <p>

                            Complete and submit assigned requirements
                            within the specified period.

                        </p>

                    </article>



                    <article class="guideline">

                        <div class="guideline-number">
                            04
                        </div>


                        <h3>
                            Maintain academic integrity
                        </h3>


                        <p>

                            All submitted academic work must represent
                            your own effort and comply with course
                            requirements.

                        </p>

                    </article>



                    <article class="guideline">

                        <div class="guideline-number">
                            05
                        </div>


                        <h3>
                            Protect your account
                        </h3>


                        <p>

                            Keep your password confidential and log out
                            after using a shared computer or device.

                        </p>

                    </article>



                    <article class="guideline">

                        <div class="guideline-number">
                            06
                        </div>


                        <h3>
                            Check the platform regularly
                        </h3>


                        <p>

                            Regularly review the platform for newly
                            assigned lectures, activities, and
                            announcements.

                        </p>

                    </article>


                </div>

            </div>

        </section>



        <!-- =====================================================
     ACCESS
====================================================== -->

        <section
            class="access-section">

            <div class="container-custom">

                <div class="access-content">


                    <div class="section-label">
                        Student Access
                    </div>


                    <h2>
                        Access your asynchronous class.
                    </h2>


                    <p>

                        Sign in to continue to your course materials,
                        lectures, activities, and learning progress.

                    </p>


                    <a
                        href="login.php"
                        class="hero-button">

                        <i class="bi bi-arrow-right"></i>

                        Go to Student Login

                    </a>

                </div>

            </div>

        </section>


    </main>



    <!-- =====================================================
     FOOTER
====================================================== -->

    <footer class="footer">

        <div class="container-custom">

            <div class="footer-inner">


                <div class="footer-brand">

                    <img
                        src="./assets/pubmat/head.png"
                        alt="ETS-Async"
                        width="28"
                        height="28"
                        loading="lazy"
                        decoding="async">

                    <span>
                        ETS-Async
                    </span>

                </div>


                <p class="footer-copy">

                    © <?= date("Y"); ?> ETS-Async.
                    Asynchronous Learning Portal.

                </p>


            </div>

        </div>

    </footer>


</body>

</html>