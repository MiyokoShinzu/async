<?php

/* =========================================================
   ETS-ASYNC STUDENT REGISTRATION
   College of Engineering and Architecture
   ========================================================= */

date_default_timezone_set('Asia/Manila');

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <!-- =====================================================
         META
         ===================================================== -->

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <meta
        name="theme-color"
        content="#0d1117">

    <title>
        Student Registration | ETS-Async
    </title>


    <!-- =====================================================
         FAVICON
         ===================================================== -->

    <link
        rel="icon"
        type="image/png"
        href="./assets/pubmat/head.png">


    <!-- =====================================================
         BOOTSTRAP 5.3.3
         ===================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =====================================================
         BOOTSTRAP ICONS
         ===================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- =====================================================
         GOOGLE FONT
         ===================================================== -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">


    <style>
        /* =====================================================
           ROOT VARIABLES
           ===================================================== */

        :root {

            --bg-primary: #0d1117;

            --bg-secondary: #161b22;

            --bg-tertiary: #21262d;

            --border: #30363d;

            --border-light: rgba(255, 255, 255, 0.08);

            --text-primary: #f0f6fc;

            --text-secondary: #8b949e;

            --text-muted: #6e7681;

            --academic-blue: #0B4F8A;

            --github-blue: #2f81f7;

            --blue-light: #58a6ff;

            --purple: #8957e5;

            --cyan: #39d0d8;

            --success: #3fb950;

            --danger: #f85149;

            --warning: #d29922;

            --card-radius: 18px;

        }


        /* =====================================================
           GLOBAL
           ===================================================== */

        * {
            box-sizing: border-box;
        }


        html,
        body {

            min-height: 100%;

            margin: 0;

            padding: 0;

        }


        body {

            font-family:
                'Inter',
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            background:
                var(--bg-primary);

            color:
                var(--text-primary);

            min-height: 100vh;

            overflow-x: hidden;

        }


        a {
            text-decoration: none;
        }


        /* =====================================================
           BACKGROUND GRID
           ===================================================== */

        .background-grid {

            position: fixed;

            inset: 0;

            z-index: 0;

            pointer-events: none;

            background-image:

                linear-gradient(rgba(255, 255, 255, 0.025) 1px,
                    transparent 1px),

                linear-gradient(90deg,
                    rgba(255, 255, 255, 0.025) 1px,
                    transparent 1px);

            background-size:
                42px 42px;

            mask-image:
                linear-gradient(to bottom,
                    black 0%,
                    rgba(0, 0, 0, 0.6) 60%,
                    transparent 100%);

        }


        /* =====================================================
           AMBIENT GLOW
           ===================================================== */

        .ambient-glow {

            position: fixed;

            width: 600px;

            height: 600px;

            border-radius: 50%;

            pointer-events: none;

            filter: blur(100px);

            opacity: 0.13;

            z-index: 0;

        }


        .glow-blue {

            background:
                var(--github-blue);

            top: -280px;

            left: -180px;

        }


        .glow-purple {

            background:
                var(--purple);

            right: -250px;

            bottom: -280px;

        }


        /* =====================================================
           TOP NAVIGATION
           ===================================================== */

        .topbar {

            position: relative;

            z-index: 20;

            height: 72px;

            border-bottom:
                1px solid var(--border);

            background:
                rgba(13, 17, 23, 0.88);

            backdrop-filter:
                blur(14px);

            -webkit-backdrop-filter:
                blur(14px);

        }


        .topbar-inner {

            width: 100%;

            max-width: 1450px;

            height: 100%;

            margin: auto;

            padding:
                0 28px;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        /* =====================================================
           BRAND
           ===================================================== */

        .brand {

            display: flex;

            align-items: center;

            gap: 12px;

            color:
                var(--text-primary);

        }


        .brand-logo {

            width: 38px;

            height: 38px;

            border-radius: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            background: transparent;

            box-shadow: none;

            overflow: visible;

        }


        .brand-logo img {

            width: 38px;

            height: 38px;

            object-fit: contain;

            display: block;

        }


        .brand-name {

            font-size: 17px;

            font-weight: 700;

            letter-spacing: -0.3px;

        }


        .brand-name span {

            color:
                var(--blue-light);

        }


        /* =====================================================
           NAV ACTION
           ===================================================== */

        .back-home {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            padding:
                8px 14px;

            border:
                1px solid var(--border);

            border-radius: 8px;

            color:
                var(--text-secondary);

            font-size: 13px;

            font-weight: 600;

            transition:
                all 0.2s ease;

        }


        .back-home:hover {

            color:
                var(--text-primary);

            border-color:
                #586069;

            background:
                rgba(255, 255, 255, 0.04);

        }


        /* =====================================================
           MAIN LAYOUT
           ===================================================== */

        .page-wrapper {

            position: relative;

            z-index: 5;

            min-height:
                calc(100vh - 72px);

            padding:
                55px 24px 70px;

        }


        .registration-layout {

            width: 100%;

            max-width: 1250px;

            margin: 0 auto;

            display: grid;

            grid-template-columns:
                minmax(300px, 0.85fr) minmax(520px, 1.45fr);

            gap: 55px;

            align-items: center;

        }


        /* =====================================================
           LEFT INFORMATION PANEL
           ===================================================== */

        .intro-panel {

            position: relative;

            min-height: 650px;

            display: flex;

            flex-direction: column;

            justify-content: center;

        }


        .eyebrow {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            width: fit-content;

            padding:
                6px 11px;

            margin-bottom: 22px;

            border:
                1px solid rgba(47, 129, 247, 0.28);

            border-radius: 999px;

            background:
                rgba(47, 129, 247, 0.08);

            color:
                var(--blue-light);

            font-size: 11px;

            font-weight: 700;

            letter-spacing: 0.5px;

            text-transform: uppercase;

        }


        .eyebrow-dot {

            width: 7px;

            height: 7px;

            border-radius: 50%;

            background:
                var(--success);

            box-shadow:
                0 0 10px rgba(63, 185, 80, 0.7);

        }


        .intro-panel h1 {

            margin: 0;

            max-width: 600px;

            font-size:
                clamp(2.4rem, 5vw, 4.2rem);

            line-height: 1.02;

            letter-spacing: -2.5px;

            font-weight: 800;

        }


        .gradient-text {

            background:
                linear-gradient(90deg,
                    #ffffff 0%,
                    #58a6ff 45%,
                    #a371f7 100%);

            -webkit-background-clip: text;

            -webkit-text-fill-color:
                transparent;

            background-clip: text;

        }


        .intro-description {

            max-width: 540px;

            margin:
                24px 0 30px;

            color:
                var(--text-secondary);

            font-size: 15px;

            line-height: 1.8;

        }


        /* =====================================================
           FEATURE LIST
           ===================================================== */

        .feature-list {

            display: flex;

            flex-direction: column;

            gap: 13px;

            margin-top: 8px;

        }


        .feature-item {

            display: flex;

            align-items: center;

            gap: 13px;

            color:
                #c9d1d9;

            font-size: 13px;

        }


        .feature-icon {

            width: 32px;

            height: 32px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border:
                1px solid var(--border);

            border-radius: 8px;

            background:
                rgba(255, 255, 255, 0.025);

            color:
                var(--blue-light);

        }


        /* =====================================================
           TECHNICAL DECORATION
           ===================================================== */

        .system-visual {

            position: absolute;

            right: -40px;

            bottom: 0;

            width: 260px;

            height: 220px;

            opacity: 0.85;

            pointer-events: none;

        }


        .system-orbit {

            position: absolute;

            inset: 25px;

            border:
                1px solid rgba(47, 129, 247, 0.18);

            border-radius: 50%;

        }


        .system-orbit.two {

            inset: 50px;

            border-color:
                rgba(137, 87, 229, 0.18);

        }


        .system-core {

            position: absolute;

            top: 50%;

            left: 50%;

            width: 58px;

            height: 58px;

            transform:
                translate(-50%, -50%);

            border:
                1px solid rgba(88, 166, 255, 0.45);

            border-radius: 16px;

            background:
                linear-gradient(135deg,
                    rgba(11, 79, 138, 0.7),
                    rgba(47, 129, 247, 0.25));

            box-shadow:
                0 0 40px rgba(47, 129, 247, 0.22);

            display: flex;

            align-items: center;

            justify-content: center;

            color:
                var(--blue-light);

            font-size: 22px;

        }


        .system-node {

            position: absolute;

            width: 8px;

            height: 8px;

            border-radius: 50%;

            background:
                var(--blue-light);

            box-shadow:
                0 0 14px rgba(88, 166, 255, 0.7);

        }


        .node-1 {

            top: 20px;

            left: 110px;

        }


        .node-2 {

            right: 30px;

            top: 90px;

            background:
                var(--purple);

        }


        .node-3 {

            left: 40px;

            bottom: 50px;

            background:
                var(--cyan);

        }


        .node-4 {

            right: 75px;

            bottom: 15px;

        }


        /* =====================================================
           REGISTRATION CARD
           ===================================================== */

        .register-card {

            position: relative;

            background:
                rgba(22, 27, 34, 0.94);

            border:
                1px solid var(--border);

            border-radius:
                var(--card-radius);

            box-shadow:
                0 25px 80px rgba(0, 0, 0, 0.4);

            overflow: hidden;

        }


        .register-card::before {

            content: "";

            position: absolute;

            top: 0;

            left: 0;

            right: 0;

            height: 2px;

            background:
                linear-gradient(90deg,
                    var(--academic-blue),
                    var(--github-blue),
                    var(--purple));

        }


        /* =====================================================
           CARD HEADER
           ===================================================== */

        .register-header {

            padding:
                28px 34px;

            border-bottom:
                1px solid var(--border);

            display: flex;

            align-items: center;

            gap: 16px;

        }


        .register-header-icon {

            width: 48px;

            height: 48px;

            flex-shrink: 0;

            display: flex;

            align-items: center;

            justify-content: center;

            border:
                1px solid rgba(47, 129, 247, 0.3);

            border-radius: 12px;

            background:
                rgba(47, 129, 247, 0.08);

            color:
                var(--blue-light);

            font-size: 20px;

        }


        .register-header h2 {

            margin: 0 0 4px;

            font-size: 20px;

            font-weight: 700;

            letter-spacing: -0.5px;

        }


        .register-header p {

            margin: 0;

            color:
                var(--text-secondary);

            font-size: 12px;

        }


        /* =====================================================
           CARD BODY
           ===================================================== */

        .register-body {

            padding:
                32px 34px 35px;

        }


        /* =====================================================
           MESSAGE
           ===================================================== */

        #registerMessage {

            display: none;

            border-radius: 10px;

            font-size: 13px;

            line-height: 1.5;

            border-width: 1px;

        }


        #registerMessage.alert-success {

            color:
                #aff5b4;

            background:
                rgba(46, 160, 67, 0.12);

            border-color:
                rgba(63, 185, 80, 0.3);

        }


        #registerMessage.alert-danger {

            color:
                #ffb4ae;

            background:
                rgba(248, 81, 73, 0.10);

            border-color:
                rgba(248, 81, 73, 0.3);

        }


        #registerMessage.alert-warning {

            color:
                #e3b341;

            background:
                rgba(210, 153, 34, 0.10);

            border-color:
                rgba(210, 153, 34, 0.3);

        }


        /* =====================================================
           FORM SECTION
           ===================================================== */

        .form-section {

            margin-bottom: 27px;

        }


        .form-section:last-child {

            margin-bottom: 0;

        }


        .section-heading {

            display: flex;

            align-items: center;

            gap: 9px;

            margin-bottom: 18px;

            color:
                var(--text-primary);

            font-size: 13px;

            font-weight: 700;

            letter-spacing: 0.2px;

        }


        .section-heading::after {

            content: "";

            height: 1px;

            flex: 1;

            background:
                var(--border);

        }


        .section-heading i {

            color:
                var(--blue-light);

        }


        /* =====================================================
           LABELS
           ===================================================== */

        .form-label {

            display: block;

            margin-bottom: 7px;

            color:
                #c9d1d9;

            font-size: 12px;

            font-weight: 600;

        }


        /* =====================================================
           FORM CONTROLS
           ===================================================== */

        .form-control,
        .form-select {

            height: 46px;

            border:
                1px solid var(--border);

            border-radius: 8px;

            background:
                #0d1117;

            color:
                var(--text-primary);

            padding:
                9px 12px;

            font-family:
                inherit;

            font-size: 13px;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;

        }


        .form-control::placeholder {

            color:
                #484f58;

        }


        .form-select {

            cursor: pointer;

        }


        .form-select option {

            background:
                #161b22;

            color:
                #f0f6fc;

        }


        .form-control:hover,
        .form-select:hover {

            border-color:
                #484f58;

        }


        .form-control:focus,
        .form-select:focus {

            background:
                #0d1117;

            color:
                var(--text-primary);

            border-color:
                var(--github-blue);

            box-shadow:
                0 0 0 3px rgba(47, 129, 247, 0.15);

        }


        /* =====================================================
           PASSWORD FIELD
           ===================================================== */

        .password-wrapper {

            position: relative;

        }


        .password-wrapper .form-control {

            padding-right:
                70px;

        }


        .password-toggle {

            position: absolute;

            right: 9px;

            top: 50%;

            transform:
                translateY(-50%);

            border: none;

            background:
                transparent;

            color:
                var(--text-secondary);

            font-family:
                inherit;

            font-size: 11px;

            font-weight: 600;

            cursor: pointer;

            padding:
                5px 7px;

            border-radius: 5px;

            transition:
                all 0.2s ease;

        }


        .password-toggle:hover {

            color:
                var(--blue-light);

            background:
                rgba(47, 129, 247, 0.08);

        }


        /* =====================================================
           FORM HELP TEXT
           ===================================================== */

        .form-text {

            margin-top: 6px;

            color:
                var(--text-muted);

            font-size: 10px;

        }


        /* =====================================================
           CHECKBOX
           ===================================================== */

        .form-check {

            display: flex;

            align-items: flex-start;

            gap: 8px;

            padding-left: 0;

        }


        .form-check-input {

            width: 16px;

            height: 16px;

            margin:
                2px 0 0;

            flex-shrink: 0;

            border:
                1px solid #484f58;

            background-color:
                #0d1117;

            cursor: pointer;

        }


        .form-check-input:checked {

            background-color:
                var(--github-blue);

            border-color:
                var(--github-blue);

        }


        .form-check-input:focus {

            box-shadow:
                0 0 0 3px rgba(47, 129, 247, 0.15);

        }


        .form-check-label {

            color:
                var(--text-secondary);

            font-size: 11px;

            line-height: 1.5;

            cursor: pointer;

        }


        /* Center both consent checkboxes and their descriptions */
        .consent-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            width: 100%;
            margin: 8px auto 26px;
        }

        .consent-group .form-check {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 560px;
            margin: 0 !important;
            gap: 10px;
        }

        .consent-group .form-check-input {
            float: none;
            margin: 0;
        }

        .consent-group .form-check-label {
            text-align: center;
        }

        /* =====================================================
           REGISTER BUTTON
           ===================================================== */

        .register-button {

            width: 100%;

            height: 48px;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 9px;

            border:
                1px solid rgba(88, 166, 255, 0.3);

            border-radius: 8px;

            background:
                linear-gradient(135deg,
                    #0B4F8A,
                    #2f81f7);

            color:
                #ffffff;

            font-family:
                inherit;

            font-size: 13px;

            font-weight: 700;

            box-shadow:
                0 8px 24px rgba(47, 129, 247, 0.18);

            transition:
                all 0.2s ease;

        }


        .register-button:hover {

            transform:
                translateY(-1px);

            box-shadow:
                0 12px 30px rgba(47, 129, 247, 0.28);

        }


        .register-button:active {

            transform:
                translateY(0);

        }


        .register-button:disabled {

            opacity:
                0.65;

            cursor:
                not-allowed;

            transform:
                none;

            box-shadow:
                none;

        }


        /* =====================================================
           LOGIN LINK
           ===================================================== */

        .login-prompt {

            margin-top: 22px;

            padding-top: 20px;

            border-top:
                1px solid var(--border);

            text-align: center;

            color:
                var(--text-secondary);

            font-size: 11px;

        }


        .login-link {

            color:
                var(--blue-light);

            font-weight: 600;

            transition:
                color 0.2s ease;

        }


        .login-link:hover {

            color:
                #79c0ff;

        }


        /* =====================================================
           CARD FOOTER
           ===================================================== */

        .register-footer {

            padding:
                18px 34px;

            border-top:
                1px solid var(--border);

            color:
                var(--text-muted);

            font-size: 10px;

            line-height: 1.7;

            text-align: center;

        }


        .register-footer strong {

            color:
                #8b949e;

        }


        /* =====================================================
           TERMS AND CONDITIONS
           ===================================================== */

        .terms-link {
            color: var(--blue-light);
            font-weight: 600;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .terms-link:hover {
            color: #79c0ff;
        }

        .terms-modal .modal-content {
            background: #161b22;
            color: var(--text-primary);
            border: 1px solid var(--border);
            border-radius: 14px;
        }

        .terms-modal .modal-header,
        .terms-modal .modal-footer {
            border-color: var(--border);
        }

        .terms-modal .modal-title {
            font-weight: 700;
        }

        .terms-modal .modal-body {
            color: #c9d1d9;
            font-size: 13px;
            line-height: 1.75;
            max-height: 65vh;
            overflow-y: auto;
        }

        .terms-modal .modal-body h6 {
            color: var(--blue-light);
            font-size: 13px;
            font-weight: 700;
            margin-top: 18px;
        }

        .terms-modal .btn-close {
            filter: invert(1);
        }

        /* =====================================================
           RESPONSIVE
           ===================================================== */

        @media (max-width: 1050px) {

            .registration-layout {

                grid-template-columns:
                    1fr;

                max-width:
                    720px;

                gap: 35px;

            }


            .intro-panel {

                min-height:
                    auto;

                text-align: center;

                align-items: center;

            }


            .intro-description {

                margin-left: auto;

                margin-right: auto;

            }


            .feature-list {

                width: 100%;

                max-width: 450px;

                text-align: left;

            }


            .system-visual {

                display:
                    none;

            }

        }


        @media (max-width: 700px) {

            .topbar {

                height:
                    64px;

            }


            .topbar-inner {

                padding:
                    0 16px;

            }


            .brand-name {

                font-size:
                    15px;

            }


            .back-home {

                padding:
                    7px 10px;

                font-size:
                    11px;

            }


            .back-home span {

                display:
                    none;

            }


            .page-wrapper {

                min-height:
                    calc(100vh - 64px);

                padding:
                    35px 14px 50px;

            }


            .intro-panel h1 {

                font-size:
                    2.35rem;

                letter-spacing:
                    -1.6px;

            }


            .intro-description {

                font-size:
                    13px;

                line-height:
                    1.7;

            }


            .register-header {

                padding:
                    23px 22px;

            }


            .register-body {

                padding:
                    25px 20px 28px;

            }


            .register-footer {

                padding:
                    17px 20px;

            }

        }


        @media (max-width: 480px) {

            .page-wrapper {

                padding-left:
                    10px;

                padding-right:
                    10px;

            }


            .registration-layout {

                gap:
                    25px;

            }


            .intro-panel h1 {

                font-size:
                    2rem;

            }


            .feature-list {

                gap:
                    10px;

            }


            .feature-item {

                font-size:
                    11px;

            }


            .feature-icon {

                width:
                    29px;

                height:
                    29px;

                font-size:
                    12px;

            }


            .register-card {

                border-radius:
                    14px;

            }


            .register-header {

                padding:
                    20px;

            }


            .register-body {

                padding:
                    22px 17px 25px;

            }


            .register-footer {

                padding:
                    16px;

            }

        }


        /* =====================================================
           REDUCED MOTION
           ===================================================== */

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {

                animation-duration:
                    0.01ms !important;

                animation-iteration-count:
                    1 !important;

                transition-duration:
                    0.01ms !important;

            }

        }
    </style>

</head>


<body>


    <!-- =====================================================
         BACKGROUND
         ===================================================== -->

    <div class="background-grid"></div>

    <div class="ambient-glow glow-blue"></div>

    <div class="ambient-glow glow-purple"></div>


    <!-- =====================================================
         TOP NAVIGATION
         ===================================================== -->

    <nav class="topbar">

        <div class="topbar-inner">


            <!-- BRAND -->

            <a
                href="index.php"
                class="brand">

                <div class="brand-logo">

                    <img
                        src="./assets/pubmat/head.png"
                        alt="ETS-Async">

                </div>

                <div class="brand-name">

                    ETS-<span>Async</span>

                </div>

            </a>


            <!-- BACK -->

            <a
                href="index.php"
                class="back-home">

                <i class="bi bi-arrow-left"></i>

                <span>
                    Back to Home
                </span>

            </a>


        </div>

    </nav>


    <!-- =====================================================
         MAIN
         ===================================================== -->

    <main class="page-wrapper">


        <div class="registration-layout">


            <!-- =================================================
                 LEFT INTRODUCTION
                 ================================================= -->

            <section class="intro-panel">


                <div class="eyebrow">

                    <span class="eyebrow-dot"></span>

                    Student Registration

                </div>


                <h1>

                    Build your
                    <span class="gradient-text">
                        academic account.
                    </span>

                </h1>


                <p class="intro-description">

                    Create your ETS-Async student account and
                    gain access to asynchronous learning resources
                    designed for students of the
                    <strong>
                        College of Engineering and Architecture.
                    </strong>

                </p>


                <!-- FEATURES -->

                <div class="feature-list">


                    <div class="feature-item">

                        <div class="feature-icon">

                            <i class="bi bi-person-check"></i>

                        </div>

                        <span>
                            Personalized student learning access
                        </span>

                    </div>


                    <div class="feature-item">

                        <div class="feature-icon">

                            <i class="bi bi-journal-code"></i>

                        </div>

                        <span>
                            Access academic learning resources and tools
                        </span>

                    </div>


                    <div class="feature-item">

                        <div class="feature-icon">

                            <i class="bi bi-graph-up-arrow"></i>

                        </div>

                        <span>
                            Track learning activities and progress
                        </span>

                    </div>


                    <div class="feature-item">

                        <div class="feature-icon">

                            <i class="bi bi-shield-check"></i>

                        </div>

                        <span>
                            Secure account-based academic access
                        </span>

                    </div>


                </div>


                <!-- TECHNICAL VISUAL -->

                <div class="system-visual">


                    <div class="system-orbit"></div>

                    <div class="system-orbit two"></div>


                    <div class="system-core">

                        <i class="bi bi-mortarboard"></i>

                    </div>


                    <span class="system-node node-1"></span>

                    <span class="system-node node-2"></span>

                    <span class="system-node node-3"></span>

                    <span class="system-node node-4"></span>


                </div>


            </section>


            <!-- =================================================
                 REGISTRATION CARD
                 ================================================= -->

            <section class="register-card">


                <!-- CARD HEADER -->

                <div class="register-header">


                    <div class="register-header-icon">

                        <i class="bi bi-person-plus"></i>

                    </div>


                    <div>

                        <h2>
                            Create Student Account
                        </h2>

                        <p>
                            Enter your information to get started.
                        </p>

                    </div>


                </div>


                <!-- CARD BODY -->

                <div class="register-body">


                    <!-- MESSAGE -->

                    <div
                        id="registerMessage"
                        class="alert mb-4"
                        role="alert">
                    </div>


                    <!-- FORM -->

                    <form
                        id="registerForm"
                        autocomplete="on">


                        <!-- =================================================
                             STUDENT INFORMATION
                             ================================================= -->

                        <div class="form-section">


                            <div class="section-heading">

                                <i class="bi bi-person-vcard"></i>

                                Student Information

                            </div>


                            <!-- NAME -->

                            <div class="row g-3">


                                <!-- LAST NAME -->

                                <div class="col-md-4">

                                    <label
                                        for="last_name"
                                        class="form-label">

                                        Last Name

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="last_name"
                                        name="last_name"
                                        placeholder="Last name"
                                        autocomplete="family-name"
                                        maxlength="100"
                                        required>

                                </div>


                                <!-- FIRST NAME -->

                                <div class="col-md-4">

                                    <label
                                        for="first_name"
                                        class="form-label">

                                        First Name

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="first_name"
                                        name="first_name"
                                        placeholder="First name"
                                        autocomplete="given-name"
                                        maxlength="100"
                                        required>

                                </div>


                                <!-- MIDDLE INITIAL -->

                                <div class="col-md-2">

                                    <label
                                        for="middle_initial"
                                        class="form-label">

                                        M.I.

                                    </label>

                                    <input
                                        type="text"
                                        class="form-control text-uppercase"
                                        id="middle_initial"
                                        name="middle_initial"
                                        placeholder="M.I."
                                        maxlength="2">

                                </div>


                                <!-- EXTENSION -->

                                <div class="col-md-2">

                                    <label
                                        for="extension_name"
                                        class="form-label">

                                        Extension

                                    </label>

                                    <select
                                        class="form-select"
                                        id="extension_name"
                                        name="extension_name">

                                        <option value="">
                                            None
                                        </option>

                                        <option value="Jr">
                                            Jr
                                        </option>

                                        <option value="I">
                                            I
                                        </option>

                                        <option value="II">
                                            II
                                        </option>

                                        <option value="III">
                                            III
                                        </option>

                                        <option value="IV">
                                            IV
                                        </option>

                                    </select>

                                </div>


                            </div>


                            <!-- DEPARTMENT / SECTION -->

                            <div class="row g-3 mt-1">


                                <!-- DEPARTMENT -->

                                <div class="col-md-6">

                                    <label
                                        for="department"
                                        class="form-label">

                                        Department

                                    </label>

                                    <select
                                        class="form-select"
                                        id="department"
                                        name="department"
                                        required>

                                        <option
                                            value=""
                                            selected
                                            disabled>

                                            Select Department

                                        </option>

                                        <option value="Computer Engineering">
                                            Computer Engineering
                                        </option>

                                        <option value="Electrical Engineering">
                                            Electrical Engineering
                                        </option>

                                        <option value="Agricultural and Biosystems Engineering">
                                            Agricultural and Biosystems Engineering
                                        </option>

                                        <option value="Chemical Engineering">
                                            Chemical Engineering
                                        </option>

                                        <option value="Geodetic Engineering">
                                            Geodetic Engineering
                                        </option>

                                        <option value="Electronics and Communications Engineering">
                                            Electronics and Communications Engineering
                                        </option>

                                        <option value="Architecture">
                                            Architecture
                                        </option>

                                    </select>

                                </div>


                                <!-- YEAR / SECTION -->

                                <div class="col-md-6">

                                    <label
                                        for="year_section"
                                        class="form-label">

                                        Year & Section

                                    </label>

                                    <select
                                        class="form-select"
                                        id="year_section"
                                        name="year_section"
                                        required>

                                        <option
                                            value=""
                                            selected
                                            disabled>

                                            Select Year & Section

                                        </option>

                                        <option value="1-E">
                                            Prefer not to say
                                        </option>
                                        <option value="1-A">
                                            1st Year - A
                                        </option>

                                        <option value="1-B">
                                            1st Year - B
                                        </option>

                                        <option value="1-C">
                                            1st Year - C
                                        </option>

                                        <option value="2-A">
                                            2nd Year - A
                                        </option>

                                        <option value="2-B">
                                            2nd Year - B
                                        </option>

                                        <option value="2-C">
                                            2nd Year - C
                                        </option>

                                        <option value="3-A">
                                            3rd Year - A
                                        </option>

                                        <option value="3-B">
                                            3rd Year - B
                                        </option>

                                        <option value="3-C">
                                            3rd Year - C
                                        </option>

                                        <option value="4-A">
                                            4th Year - A
                                        </option>

                                        <option value="4-B">
                                            4th Year - B
                                        </option>

                                        <option value="4-C">
                                            4th Year - C
                                        </option>

                                    </select>

                                </div>


                            </div>


                            <!-- STUDENT ID -->

                            <div class="mt-3">

                                <label
                                    for="student_id"
                                    class="form-label">

                                    Student ID

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="student_id"
                                    name="student_id"
                                    placeholder="Enter your student ID"
                                    maxlength="50"
                                    required>

                            </div>


                        </div>


                        <!-- =================================================
                             ACCOUNT INFORMATION
                             ================================================= -->

                        <div class="form-section">


                            <div class="section-heading">

                                <i class="bi bi-shield-lock"></i>

                                Account Information

                            </div>


                            <!-- EMAIL -->

                            <div class="mb-3">

                                <label
                                    for="email"
                                    class="form-label">

                                    Email Address

                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    placeholder="Enter your email address"
                                    autocomplete="email"
                                    maxlength="150"
                                    required>

                            </div>


                            <!-- USERNAME -->

                            <div class="mb-3">

                                <label
                                    for="username"
                                    class="form-label">

                                    Username

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="username"
                                    name="username"
                                    placeholder="Create a username"
                                    autocomplete="username"
                                    minlength="4"
                                    maxlength="50"
                                    required>

                                <div class="form-text">

                                    At least 4 characters.
                                    Letters, numbers, underscores,
                                    periods, and hyphens are allowed.

                                </div>

                            </div>


                            <!-- PASSWORD -->

                            <div class="mb-3">

                                <label
                                    for="password"
                                    class="form-label">

                                    Password

                                </label>

                                <div class="password-wrapper">

                                    <input
                                        type="password"
                                        class="form-control"
                                        id="password"
                                        name="password"
                                        placeholder="Create a password"
                                        autocomplete="new-password"
                                        minlength="8"
                                        maxlength="255"
                                        required>

                                    <button
                                        type="button"
                                        class="password-toggle"
                                        id="togglePassword">

                                        Show

                                    </button>

                                </div>

                                <div class="form-text">

                                    Password must contain at least
                                    8 characters.

                                </div>

                            </div>


                            <!-- CONFIRM PASSWORD -->

                            <div class="mb-3">

                                <label
                                    for="confirm_password"
                                    class="form-label">

                                    Confirm Password

                                </label>

                                <div class="password-wrapper">

                                    <input
                                        type="password"
                                        class="form-control"
                                        id="confirm_password"
                                        name="confirm_password"
                                        placeholder="Re-enter your password"
                                        autocomplete="new-password"
                                        minlength="8"
                                        maxlength="255"
                                        required>

                                    <button
                                        type="button"
                                        class="password-toggle"
                                        id="toggleConfirmPassword">

                                        Show

                                    </button>

                                </div>

                            </div>


                        </div>


                        <!-- =================================================
                             CONFIRMATION
                             ================================================= -->

                        <div class="consent-group">

                            <div class="form-check mb-4">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="agree"
                                    name="agree"
                                    required>

                                <label
                                    class="form-check-label"
                                    for="agree">

                                    I confirm that the information
                                    I provided is accurate.

                                </label>

                            </div>

                            <!-- TERMS AND CONDITIONS ACCEPTANCE -->
                            <div class="form-check mb-4">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="accept_terms"
                                    name="accept_terms"
                                    value="1"
                                    required>

                                <label class="form-check-label" for="accept_terms">
                                    I have read and agree to the
                                    <a
                                        href="#termsModal"
                                        class="terms-link"
                                        data-bs-toggle="modal"
                                        data-bs-target="#termsModal">
                                        Terms and Conditions
                                    </a>
                                    and acknowledge the website's privacy and advertising disclosures.
                                </label>
                            </div>

                        </div><!-- /.consent-group -->


                        <!-- =================================================
                             SUBMIT
                             ================================================= -->

                        <button
                            type="submit"
                            class="register-button"
                            id="registerButton">

                            <i class="bi bi-person-plus"></i>

                            <span>
                                Create Student Account
                            </span>

                        </button>


                    </form>


                    <!-- LOGIN -->

                    <div class="login-prompt">

                        Already have an account?

                        <a
                            href="login.php"
                            class="login-link ms-1">

                            Login here

                        </a>

                    </div>


                </div>


                <!-- =================================================
                     FOOTER
                     ================================================= -->

                <div class="register-footer">

                    <div>

                        <strong>
                            ETS-Async
                        </strong>

                    </div>

                    <div>

                        College of Engineering and Architecture

                    </div>

                    <div>

                        Asynchronous Learning Portal

                    </div>

                </div>


            </section>


        </div>


    </main>


    <!-- =========================================================
         TERMS AND CONDITIONS MODAL
         ========================================================= -->
    <div
        class="modal fade terms-modal"
        id="termsModal"
        tabindex="-1"
        aria-labelledby="termsModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="termsModalLabel">ETS-Async Terms and Conditions</h5>
                        <small class="text-secondary">Please read these terms before creating an account.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p><strong>Effective date:</strong> November 8, 2026</p>

                    <h6>1. About ETS-Async</h6>
                    <p>ETS-Async is an independently operated personal website created to provide online resources, tools, and learning-related materials. It is not an official website of, affiliated with, endorsed by, or operated by any school, college, university, government agency, company, or other organization. References to educational institutions or departments are for identification and informational purposes only.</p>
                    <p>This website may contain educational materials, but it is not intended solely for academic purposes. Content may also be provided for general information, personal learning, practice, and other non-commercial or experimental uses.</p>

                    <h6>2. Account Registration and Responsibilities</h6>
                    <p>You must provide accurate and current information when creating an account. You are responsible for keeping your login credentials confidential and for activity performed through your account. Do not impersonate another person, submit false information, share unauthorized access, or use the website for unlawful, abusive, disruptive, or harmful activities.</p>
                    <p>Account creation is allowed only after you have actively checked the Terms and Conditions acceptance box. If you do not agree, do not proceed with registration or use account-only features. The website may suspend or restrict accounts involved in misuse, security risks, or violations of these terms.</p>

                    <h6>3. Privacy and Personal Information</h6>
                    <p>Information submitted during registration, such as your name, student or identification details, email address, department, and account credentials, may be processed to create and manage your account, provide website features, maintain security, and respond to support requests.</p>
                    <p><strong>We do not sell your personal data.</strong> Personal information is not sold or rented to advertisers or other third parties. Information may be processed by hosting, security, analytics, or technical service providers when necessary to operate and protect the website, or disclosed when required by applicable law. Reasonable measures are used to protect information, but no website or internet transmission can be guaranteed completely secure.</p>
                    <p>Please do not submit information that is not needed to use the website. Contact the website operator using the details below for privacy-related questions or requests.</p>

                    <h6>4. Advertising and Third-Party Services</h6>
                    <p>ETS-Async may display advertisements provided by third-party advertising networks, including <strong>Monetag, Adsterra, and Hilltop Ads</strong>. These advertisements help support hosting, maintenance, and the continued operation of the website. Their presence does not mean that ETS-Async endorses the advertised products, services, websites, or claims.</p>
                    <p>Third-party advertising providers may use cookies, device identifiers, pixels, or similar technologies to deliver, measure, limit, or personalize advertisements, subject to their own policies and settings. These providers operate independently. Please review their privacy policies and use the privacy or advertising controls available in your browser where appropriate.</p>
                    <p><strong>Advertising disclaimer:</strong> ETS-Async does not select, create, verify, or control every advertisement displayed by these networks, and cannot guarantee the accuracy, safety, legality, or quality of third-party advertisements or destination websites. Browse responsibly. Do not click suspicious advertisements, download unknown files, provide passwords or financial information to unfamiliar websites, or engage with offers that appear misleading. Report concerning advertisements to the website operator.</p>

                    <h6>5. Website Content and Availability</h6>
                    <p>Website content and features are provided on an “as is” and “as available” basis. Materials may contain errors, become outdated, or be changed or removed without notice. You should independently verify important information and follow the instructions of your school, instructor, or relevant authority where applicable. ETS-Async does not guarantee uninterrupted availability or that all materials will meet a particular academic or professional requirement.</p>

                    <h6>6. Acceptable Use and Intellectual Property</h6>
                    <p>Do not attempt to disrupt the website, bypass access controls, upload malicious code, scrape or misuse accounts, or infringe the rights of others. Unless otherwise stated, website design, original text, and other original materials belong to their respective owners. Third-party names, logos, and materials remain the property of their respective owners. You must respect applicable copyright and license terms.</p>

                    <h6>7. Changes to These Terms</h6>
                    <p>These terms may be updated when website features, services, advertising arrangements, or legal requirements change. The revised version will be posted on the website with an updated effective date. Continued use after an update means you acknowledge the revised terms. If a change requires renewed consent, the website may ask you to accept it again.</p>

                    <h6>8. Contact</h6>
                    <p>For questions, privacy concerns, or reports about advertisements or website content, contact the website operator:</p>
                    <ul>
                        <li><strong>Email:</strong> <a href="mailto:evallokarlstephen@gmail.com">evallokarlstephen@gmail.com</a></li>
                        <li><strong>Contact number:</strong> <a href="tel:09700841996">09700841996</a></li>
                    </ul>

                    <p class="mb-0">By checking the acceptance box during registration, you confirm that you have read and agree to these Terms and Conditions.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary btn-sm" id="agreeTermsFromModal" data-bs-dismiss="modal">
                        <i class="bi bi-check2-circle me-1"></i> I Agree
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- =========================================================
         ANIME.JS
         ========================================================= -->

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js">
    </script>


    <!-- =========================================================
         BOOTSTRAP
         ========================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    <script>
        /* =========================================================
           API CONFIGURATION
           ========================================================= */

        const REGISTER_API =
            "./api/register.php";


        /* =========================================================
           ANIME.JS PAGE ENTRANCE
           ========================================================= */

        document.addEventListener(
            "DOMContentLoaded",
            function() {


                /* =================================================
                   PAGE ELEMENTS
                   ================================================= */

                const topbar =
                    document.querySelector(".topbar");

                const intro =
                    document.querySelector(".intro-panel");

                const card =
                    document.querySelector(".register-card");

                const features =
                    document.querySelectorAll(".feature-item");

                const systemVisual =
                    document.querySelector(".system-visual");


                /* =================================================
                   INITIAL STATES
                   ================================================= */

                anime.set(
                    [
                        topbar,
                        intro,
                        card
                    ], {
                        opacity: 0,
                        translateY: 18
                    }
                );


                anime.set(
                    features, {
                        opacity: 0,
                        translateX: -18
                    }
                );


                /* =================================================
                   NAVIGATION
                   ================================================= */

                anime({
                    targets: topbar,

                    opacity: [
                        0,
                        1
                    ],

                    translateY: [
                        -15,
                        0
                    ],

                    duration: 650,

                    easing: "easeOutCubic"

                });


                /* =================================================
                   INTRO
                   ================================================= */

                anime({

                    targets: intro,

                    opacity: [
                        0,
                        1
                    ],

                    translateY: [
                        25,
                        0
                    ],

                    duration: 850,

                    delay: 150,

                    easing: "easeOutExpo"

                });


                /* =================================================
                   REGISTRATION CARD
                   ================================================= */

                anime({

                    targets: card,

                    opacity: [
                        0,
                        1
                    ],

                    translateY: [
                        30,
                        0
                    ],

                    scale: [
                        0.98,
                        1
                    ],

                    duration: 900,

                    delay: 220,

                    easing: "easeOutExpo"

                });


                /* =================================================
                   FEATURES STAGGER
                   ================================================= */

                anime({

                    targets: features,

                    opacity: [
                        0,
                        1
                    ],

                    translateX: [
                        -18,
                        0
                    ],

                    delay: anime.stagger(
                        90, {
                            start: 400
                        }
                    ),

                    duration: 600,

                    easing: "easeOutCubic"

                });


                /* =================================================
                   SYSTEM VISUAL
                   ================================================= */

                if (systemVisual) {

                    anime({

                        targets: systemVisual,

                        rotate: [
                            -2,
                            2
                        ],

                        duration: 5000,

                        direction: "alternate",

                        loop: true,

                        easing: "easeInOutSine"

                    });

                }


                /* =================================================
                   SYSTEM NODES
                   ================================================= */

                anime({

                    targets: ".system-node",

                    scale: [
                        0.7,
                        1.3
                    ],

                    opacity: [
                        0.5,
                        1
                    ],

                    delay: anime.stagger(250),

                    duration: 1200,

                    direction: "alternate",

                    loop: true,

                    easing: "easeInOutSine"

                });


                /* =================================================
                   SYSTEM CORE
                   ================================================= */

                anime({

                    targets: ".system-core",

                    boxShadow: [
                        "0 0 20px rgba(47,129,247,0.12)",
                        "0 0 45px rgba(47,129,247,0.32)"
                    ],

                    duration: 1800,

                    direction: "alternate",

                    loop: true,

                    easing: "easeInOutSine"

                });

            }
        );


        /* =========================================================
           PASSWORD TOGGLE
           ========================================================= */

        function setupPasswordToggle(
            inputId,
            buttonId
        ) {

            const input =
                document.getElementById(inputId);

            const button =
                document.getElementById(buttonId);


            if (!input || !button) {
                return;
            }


            button.addEventListener(
                "click",
                function() {


                    if (
                        input.type === "password"
                    ) {

                        input.type =
                            "text";

                        button.textContent =
                            "Hide";

                    } else {

                        input.type =
                            "password";

                        button.textContent =
                            "Show";

                    }

                }
            );

        }


        setupPasswordToggle(
            "password",
            "togglePassword"
        );


        setupPasswordToggle(
            "confirm_password",
            "toggleConfirmPassword"
        );


        /* =========================================================
           MESSAGE FUNCTION
           ========================================================= */

        function showMessage(
            message,
            type
        ) {

            const messageBox =
                document.getElementById(
                    "registerMessage"
                );


            messageBox.className =
                "alert mb-4 alert-" +
                type;


            messageBox.textContent =
                message;


            messageBox.style.display =
                "block";


            /* =================================================
               ANIMATE MESSAGE
               ================================================= */

            anime({

                targets: messageBox,

                opacity: [
                    0,
                    1
                ],

                translateY: [
                    -8,
                    0
                ],

                duration: 350,

                easing: "easeOutCubic"

            });


            /* =================================================
               SCROLL MESSAGE INTO VIEW
               ================================================= */

            setTimeout(
                function() {

                    messageBox.scrollIntoView({

                        behavior: "smooth",

                        block: "center"

                    });

                },
                50
            );

        }


        /* =========================================================
           TERMS MODAL ACCEPTANCE
           ========================================================= */

        const agreeTermsFromModal =
            document.getElementById("agreeTermsFromModal");

        if (agreeTermsFromModal) {
            agreeTermsFromModal.addEventListener("click", function() {
                document.getElementById("accept_terms").checked = true;
            });
        }


        /* =========================================================
           FORM
           ========================================================= */

        const registerForm =
            document.getElementById(
                "registerForm"
            );


        const registerButton =
            document.getElementById(
                "registerButton"
            );


        /* =========================================================
           SUBMIT
           ========================================================= */

        registerForm.addEventListener(
            "submit",
            async function(event) {

                event.preventDefault();


                /* =================================================
                   GET VALUES
                   ================================================= */

                const lastName =
                    document
                    .getElementById(
                        "last_name"
                    )
                    .value
                    .trim();


                const firstName =
                    document
                    .getElementById(
                        "first_name"
                    )
                    .value
                    .trim();


                const middleInitial =
                    document
                    .getElementById(
                        "middle_initial"
                    )
                    .value
                    .trim()
                    .toUpperCase();


                const extensionName =
                    document
                    .getElementById(
                        "extension_name"
                    )
                    .value;


                const department =
                    document
                    .getElementById(
                        "department"
                    )
                    .value;


                const yearSection =
                    document
                    .getElementById(
                        "year_section"
                    )
                    .value;


                const studentId =
                    document
                    .getElementById(
                        "student_id"
                    )
                    .value
                    .trim();


                const email =
                    document
                    .getElementById(
                        "email"
                    )
                    .value
                    .trim()
                    .toLowerCase();


                const username =
                    document
                    .getElementById(
                        "username"
                    )
                    .value
                    .trim();


                const password =
                    document
                    .getElementById(
                        "password"
                    )
                    .value;


                const confirmPassword =
                    document
                    .getElementById(
                        "confirm_password"
                    )
                    .value;


                const agree =
                    document
                    .getElementById(
                        "agree"
                    )
                    .checked;

                const acceptTerms =
                    document
                    .getElementById(
                        "accept_terms"
                    )
                    .checked;


                /* =================================================
                   CLIENT VALIDATION
                   ================================================= */

                if (!agree) {

                    showMessage(
                        "Please confirm that the information you provided is accurate.",
                        "warning"
                    );

                    return;

                }

                if (!acceptTerms) {
                    showMessage(
                        "You must read and agree to the Terms and Conditions before creating an account.",
                        "warning"
                    );
                    document.getElementById("accept_terms").focus();
                    return;
                }


                if (
                    lastName.length < 2
                ) {

                    showMessage(
                        "Please enter a valid last name.",
                        "warning"
                    );

                    return;

                }


                if (
                    firstName.length < 2
                ) {

                    showMessage(
                        "Please enter a valid first name.",
                        "warning"
                    );

                    return;

                }


                if (
                    middleInitial !== "" &&
                    !/^[A-Z]{1,2}$/.test(
                        middleInitial
                    )
                ) {

                    showMessage(
                        "Invalid middle initial.",
                        "warning"
                    );

                    return;

                }


                if (!department) {

                    showMessage(
                        "Please select a department.",
                        "warning"
                    );

                    return;

                }


                if (!yearSection) {

                    showMessage(
                        "Please select your year and section.",
                        "warning"
                    );

                    return;

                }


                if (
                    studentId.length < 3
                ) {

                    showMessage(
                        "Please enter a valid student ID.",
                        "warning"
                    );

                    return;

                }


                if (!email) {

                    showMessage(
                        "Please enter your email address.",
                        "warning"
                    );

                    return;

                }


                if (
                    username.length < 4
                ) {

                    showMessage(
                        "Username must contain at least 4 characters.",
                        "warning"
                    );

                    return;

                }


                if (
                    !/^[A-Za-z0-9_.-]+$/.test(
                        username
                    )
                ) {

                    showMessage(
                        "Username may only contain letters, numbers, underscores, periods, and hyphens.",
                        "warning"
                    );

                    return;

                }


                if (
                    password.length < 8
                ) {

                    showMessage(
                        "Password must contain at least 8 characters.",
                        "warning"
                    );

                    return;

                }


                if (
                    password !== confirmPassword
                ) {

                    showMessage(
                        "Passwords do not match.",
                        "danger"
                    );

                    return;

                }


                /* =================================================
                   DISABLE BUTTON
                   ================================================= */

                registerButton.disabled =
                    true;


                registerButton.innerHTML = `

                    <span
                        class="spinner-border spinner-border-sm"
                        aria-hidden="true">
                    </span>

                    <span>
                        Creating Account...
                    </span>

                `;


                /* =================================================
                   CREATE GET PARAMETERS
                   ================================================= */

                const params =
                    new URLSearchParams({

                        last_name: lastName,

                        first_name: firstName,

                        middle_initial: middleInitial,

                        extension_name: extensionName,

                        department: department,

                        year_section: yearSection,

                        student_id: studentId,

                        email: email,

                        username: username,

                        password: password,

                        accept_terms: acceptTerms ? "1" : "0"

                    });


                /* =================================================
                   SEND REQUEST
                   ================================================= */

                try {

                    const response =
                        await fetch(

                            REGISTER_API +
                            "?" +
                            params.toString(),

                            {

                                method: "GET",

                                headers: {

                                    "Accept": "application/json"

                                }

                            }

                        );


                    /* =================================================
                       CHECK CONTENT TYPE
                       ================================================= */

                    const contentType =
                        response.headers.get(
                            "content-type"
                        ) || "";


                    if (
                        !contentType.includes(
                            "application/json"
                        )
                    ) {

                        const text =
                            await response.text();


                        console.error(
                            "Invalid API response:",
                            text
                        );


                        throw new Error(
                            "API did not return JSON."
                        );

                    }


                    const data =
                        await response.json();


                    /* =================================================
                       SUCCESS
                       ================================================= */

                    if (
                        response.ok &&
                        data.success
                    ) {

                        showMessage(

                            data.message ||
                            "Registration successful.",

                            "success"

                        );


                        registerForm.reset();


                        registerButton.innerHTML = `

                            <i class="bi bi-check-circle"></i>

                            <span>
                                Account Created
                            </span>

                        `;


                        /* =============================================
                           REDIRECT TO LOGIN
                           ============================================= */

                        setTimeout(
                            function() {

                                window.location.href =
                                    "login.php";

                            },
                            1200
                        );


                        return;

                    }


                    /* =================================================
                       API ERROR
                       ================================================= */

                    showMessage(

                        data.message ||
                        "Registration failed. Please try again.",

                        "danger"

                    );


                    registerButton.disabled =
                        false;


                    registerButton.innerHTML = `

                        <i class="bi bi-person-plus"></i>

                        <span>
                            Create Student Account
                        </span>

                    `;


                } catch (error) {


                    /* =================================================
                       CONSOLE ERROR
                       ================================================= */

                    console.error(
                        "Registration error:",
                        error
                    );


                    /* =================================================
                       USER MESSAGE
                       ================================================= */

                    showMessage(

                        "Unable to connect to the registration server. Please check your API URL.",

                        "danger"

                    );


                    /* =================================================
                       RESTORE BUTTON
                       ================================================= */

                    registerButton.disabled =
                        false;


                    registerButton.innerHTML = `

                        <i class="bi bi-person-plus"></i>

                        <span>
                            Create Student Account
                        </span>

                    `;

                }

            }
        );
    </script>


</body>

</html>