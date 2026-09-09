<?php
/* =========================================================
   ETS-Async
   Minimalist Academic Learning Platform
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
        content="ETS-Async — A simple asynchronous learning platform for students.">

    <meta
        name="theme-color"
        content="#2563eb">

    <title>ETS-Async | Learning Platform</title>


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

            --primary: #2563eb;
            --primary-hover: #1d4ed8;

            --text: #111827;
            --text-secondary: #6b7280;

            --border: #e5e7eb;

            --background: #ffffff;
            --surface: #f8fafc;

            --max-width: 1120px;
        }


        /* =================================================
           GLOBAL
        ================================================= */

        html {
            scroll-behavior: smooth;
        }


        body {

            margin: 0;

            background:
                var(--background);

            color:
                var(--text);

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

            text-rendering:
                optimizeLegibility;
        }


        a {
            text-decoration: none;
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
           NAVBAR
        ================================================= */

        .site-header {

            height:
                72px;

            display:
                flex;

            align-items:
                center;

            border-bottom:
                1px solid var(--border);

            background:
                rgba(255, 255, 255, 0.96);
        }


        .navbar-inner {

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
                inline-flex;

            align-items:
                center;

            gap:
                10px;

            color:
                var(--text);
        }


        .brand img {

            width:
                34px;

            height:
                34px;

            object-fit:
                contain;
        }


        .brand-name {

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                1.05rem;

            font-weight:
                700;

            letter-spacing:
                -0.02em;
        }


        .desktop-navigation {

            display:
                flex;

            align-items:
                center;

            gap:
                8px;
        }


        .navigation-link {

            padding:
                8px 12px;

            color:
                var(--text-secondary);

            font-size:
                0.9rem;

            font-weight:
                500;

            border-radius:
                8px;

            transition:
                color .2s ease,
                background .2s ease;
        }


        .navigation-link:hover {

            color:
                var(--primary);

            background:
                #f8fafc;
        }


        .navigation-login {

            margin-left:
                8px;

            padding:
                9px 16px;

            color:
                #ffffff;

            background:
                var(--primary);

            border-radius:
                8px;
        }


        .navigation-login:hover {

            color:
                #ffffff;

            background:
                var(--primary-hover);
        }


        /* =================================================
           HERO
        ================================================= */

        .hero {

            min-height:
                calc(100vh - 72px);

            display:
                flex;

            align-items:
                center;

            padding:
                80px 0;
        }


        .hero-content {

            max-width:
                760px;

            margin:
                0 auto;

            text-align:
                center;
        }


        .hero-mark {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            width:
                56px;

            height:
                56px;

            margin-bottom:
                28px;

            border:
                1px solid #dbeafe;

            border-radius:
                14px;

            background:
                #eff6ff;

            color:
                var(--primary);

            font-size:
                1.35rem;
        }


        .hero h1 {

            margin:
                0 0 20px;

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(2.4rem, 6vw, 4.3rem);

            line-height:
                1.08;

            letter-spacing:
                -0.055em;

            font-weight:
                700;
        }


        .hero h1 span {

            color:
                var(--primary);
        }


        .hero-description {

            max-width:
                640px;

            margin:
                0 auto 32px;

            color:
                var(--text-secondary);

            font-size:
                clamp(0.95rem, 2vw, 1.08rem);

            line-height:
                1.75;
        }


        .hero-button {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                8px;

            min-height:
                46px;

            padding:
                0 20px;

            color:
                #ffffff;

            background:
                var(--primary);

            border:
                1px solid var(--primary);

            border-radius:
                9px;

            font-size:
                0.9rem;

            font-weight:
                600;

            transition:
                background .2s ease,
                transform .2s ease,
                box-shadow .2s ease;
        }


        .hero-button:hover {

            color:
                #ffffff;

            background:
                var(--primary-hover);

            transform:
                translateY(-1px);

            box-shadow:
                0 8px 20px rgba(37, 99, 235, .15);
        }


        /* =================================================
           SIMPLE DIVIDER
        ================================================= */

        .divider {

            width:
                100%;

            height:
                1px;

            background:
                var(--border);
        }


        /* =================================================
           ABOUT
        ================================================= */

        .about {

            padding:
                100px 0;
        }


        .section-small {

            margin-bottom:
                12px;

            color:
                var(--primary);

            font-size:
                0.75rem;

            font-weight:
                700;

            letter-spacing:
                .08em;

            text-transform:
                uppercase;
        }


        .about h2 {

            margin:
                0 0 18px;

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


        .about-intro {

            max-width:
                650px;

            margin-bottom:
                48px;

            color:
                var(--text-secondary);

            font-size:
                1rem;

            line-height:
                1.75;
        }


        .about-grid {

            display:
                grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap:
                20px;
        }


        .about-item {

            padding:
                28px;

            border:
                1px solid var(--border);

            border-radius:
                12px;

            background:
                #ffffff;
        }


        .about-item-icon {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            width:
                40px;

            height:
                40px;

            margin-bottom:
                20px;

            border-radius:
                9px;

            background:
                #eff6ff;

            color:
                var(--primary);
        }


        .about-item h3 {

            margin:
                0 0 8px;

            font-size:
                0.98rem;

            font-weight:
                700;
        }


        .about-item p {

            margin:
                0;

            color:
                var(--text-secondary);

            font-size:
                0.88rem;

            line-height:
                1.65;
        }


        /* =================================================
           REMINDERS
        ================================================= */

        .reminders {

            padding:
                100px 0;

            background:
                var(--surface);
        }


        .reminders-heading {

            max-width:
                620px;

            margin-bottom:
                45px;
        }


        .reminders h2 {

            margin:
                0 0 12px;

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(1.8rem, 4vw, 2.4rem);

            letter-spacing:
                -.035em;
        }


        .reminders-heading p {

            margin:
                0;

            color:
                var(--text-secondary);
        }


        .reminder-list {

            display:
                grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap:
                20px;
        }


        .reminder {

            padding:
                26px;

            background:
                #ffffff;

            border:
                1px solid var(--border);

            border-radius:
                12px;
        }


        .reminder-number {

            margin-bottom:
                18px;

            color:
                #9ca3af;

            font-size:
                0.75rem;

            font-weight:
                700;

            letter-spacing:
                .05em;
        }


        .reminder h3 {

            margin:
                0 0 8px;

            font-size:
                0.98rem;

            font-weight:
                700;
        }


        .reminder p {

            margin:
                0;

            color:
                var(--text-secondary);

            font-size:
                0.88rem;
        }


        /* =================================================
           LOGIN
        ================================================= */

        .login-section {

            padding:
                110px 0;

            text-align:
                center;
        }


        .login-content {

            max-width:
                620px;

            margin:
                0 auto;
        }


        .login-content h2 {

            margin:
                0 0 14px;

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(1.8rem, 4vw, 2.5rem);

            letter-spacing:
                -.035em;
        }


        .login-content p {

            margin:
                0 auto 28px;

            color:
                var(--text-secondary);

            font-size:
                0.95rem;
        }


        /* =================================================
           FOOTER
        ================================================= */

        .footer {

            border-top:
                1px solid var(--border);

            padding:
                28px 0;

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
                0.85rem;

            font-weight:
                600;
        }


        .footer-brand img {

            width:
                27px;

            height:
                27px;

            object-fit:
                contain;
        }


        .footer-copy {

            margin:
                0;

            color:
                #9ca3af;

            font-size:
                0.78rem;
        }


        /* =================================================
           RESPONSIVE
        ================================================= */

        @media (max-width: 767px) {

            .container-custom {

                width:
                    min(100% - 32px, var(--max-width));
            }


            .site-header {

                height:
                    64px;
            }


            .navbar-inner {

                width:
                    min(100% - 32px, var(--max-width));
            }


            .desktop-navigation {

                gap:
                    2px;
            }


            .navigation-link {

                display:
                    none;
            }


            .navigation-login {

                margin:
                    0;

                padding:
                    8px 13px;
            }


            .hero {

                min-height:
                    calc(100vh - 64px);

                padding:
                    60px 0;
            }


            .hero-mark {

                width:
                    50px;

                height:
                    50px;

                margin-bottom:
                    22px;
            }


            .hero h1 {

                font-size:
                    clamp(2.2rem, 12vw, 3.2rem);
            }


            .hero-description {

                font-size:
                    0.92rem;

                line-height:
                    1.7;
            }


            .about,
            .reminders {

                padding:
                    70px 0;
            }


            .about-grid,
            .reminder-list {

                grid-template-columns:
                    1fr;
            }


            .about-item,
            .reminder {

                padding:
                    23px;
            }


            .login-section {

                padding:
                    75px 0;
            }


            .footer-inner {

                flex-direction:
                    column;

                text-align:
                    center;
            }

        }


        @media (prefers-reduced-motion: reduce) {

            html {
                scroll-behavior: auto;
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

        <div class="navbar-inner">

            <a
                href="#home"
                class="brand">

                <img
                    src="./assets/pubmat/head.png"
                    alt="ETS-Async"
                    width="34"
                    height="34"
                    loading="eager"
                    decoding="async">

                <span class="brand-name">
                    ETS-Async
                </span>

            </a>


            <nav class="desktop-navigation">

                <a
                    href="#about"
                    class="navigation-link">

                    About

                </a>


                <a
                    href="#reminders"
                    class="navigation-link">

                    Guidelines

                </a>


                <a
                    href="login.php"
                    class="navigation-link navigation-login">

                    Login

                </a>

            </nav>

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


                    <div class="hero-mark">

                        <i class="bi bi-mortarboard"></i>

                    </div>


                    <h1>

                        A simpler way to
                        <span>learn online.</span>

                    </h1>


                    <p class="hero-description">

                        ETS-Async is an asynchronous learning platform
                        designed to give students simple and organized
                        access to lectures, activities, and course resources.

                    </p>


                    <a
                        href="login.php"
                        class="hero-button">

                        <i class="bi bi-box-arrow-in-right"></i>

                        Login to ETS-Async

                    </a>

                </div>

            </div>

        </section>



        <div class="divider"></div>



        <!-- =====================================================
     ABOUT
====================================================== -->

        <section
            id="about"
            class="about">

            <div class="container-custom">


                <div class="section-small">
                    About ETS-Async
                </div>


                <h2>
                    Focused on learning,<br>
                    not unnecessary complexity.
                </h2>


                <p class="about-intro">

                    ETS-Async provides a centralized space for students
                    to access their academic resources and complete
                    assigned requirements in an asynchronous environment.

                </p>


                <div class="about-grid">


                    <article class="about-item">

                        <div class="about-item-icon">

                            <i class="bi bi-journal-text"></i>

                        </div>


                        <h3>
                            Course Materials
                        </h3>


                        <p>

                            Access assigned lectures, references,
                            and other learning materials in one place.

                        </p>

                    </article>



                    <article class="about-item">

                        <div class="about-item-icon">

                            <i class="bi bi-list-check"></i>

                        </div>


                        <h3>
                            Academic Activities
                        </h3>


                        <p>

                            Complete assigned activities and submit
                            your academic requirements through the platform.

                        </p>

                    </article>



                    <article class="about-item">

                        <div class="about-item-icon">

                            <i class="bi bi-graph-up"></i>

                        </div>


                        <h3>
                            Progress Tracking
                        </h3>


                        <p>

                            Monitor your progress and keep track of
                            completed learning requirements.

                        </p>

                    </article>


                </div>

            </div>

        </section>



        <!-- =====================================================
     GUIDELINES
====================================================== -->

        <section
            id="reminders"
            class="reminders">

            <div class="container-custom">


                <div class="reminders-heading">

                    <div class="section-small">
                        Guidelines
                    </div>


                    <h2>
                        Before you begin
                    </h2>


                    <p>

                        A few simple practices will help keep your
                        learning experience organized.

                    </p>

                </div>


                <div class="reminder-list">


                    <article class="reminder">

                        <div class="reminder-number">
                            01
                        </div>


                        <h3>
                            Use your assigned account
                        </h3>


                        <p>

                            Access the platform using your official
                            account to ensure your activities and
                            progress are properly recorded.

                        </p>

                    </article>



                    <article class="reminder">

                        <div class="reminder-number">
                            02
                        </div>


                        <h3>
                            Check your activities
                        </h3>


                        <p>

                            Review your assigned lectures and activities
                            regularly and observe the specified deadlines.

                        </p>

                    </article>



                    <article class="reminder">

                        <div class="reminder-number">
                            03
                        </div>


                        <h3>
                            Keep your account secure
                        </h3>


                        <p>

                            Never share your password and remember to
                            log out when using a shared device.

                        </p>

                    </article>


                </div>

            </div>

        </section>



        <!-- =====================================================
     LOGIN
====================================================== -->

        <section class="login-section">

            <div class="container-custom">

                <div class="login-content">


                    <h2>
                        Ready to learn?
                    </h2>


                    <p>

                        Sign in to access your courses and
                        assigned learning activities.

                    </p>


                    <a
                        href="login.php"
                        class="hero-button">

                        <i class="bi bi-arrow-right"></i>

                        Go to Login

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
                        width="27"
                        height="27"
                        loading="lazy"
                        decoding="async">

                    <span>
                        ETS-Async
                    </span>

                </div>


                <p class="footer-copy">

                    © <?= date("Y"); ?> ETS-Async.
                    Asynchronous Learning Platform.

                </p>


            </div>

        </div>

    </footer>


</body>

</html>