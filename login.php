<?php
/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   GITHUB + ANIME.JS INSPIRED LOGIN PAGE
   COLLEGE OF ENGINEERING AND ARCHITECTURE
   ========================================================= */

date_default_timezone_set('Asia/Manila');

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- =====================================================
         META
    ====================================================== -->

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta
        name="description"
        content="ETS-Async Learning Portal Login for College of Engineering and Architecture students.">

    <meta
        name="theme-color"
        content="#0d1117">

    <meta
        name="color-scheme"
        content="dark">


    <!-- =====================================================
         TITLE
    ====================================================== -->

    <title>
        Login | ETS-Async
    </title>


    <!-- =====================================================
         FAVICON
    ====================================================== -->

    <link
        rel="icon"
        type="image/png"
        href="./assets/pubmat/head.png">


    <!-- =====================================================
         GOOGLE FONTS
    ====================================================== -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet">


    <!-- =====================================================
         BOOTSTRAP 5.3.3
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
         ANIME.JS
    ====================================================== -->

    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js">
    </script>


    <!-- =====================================================
         CUSTOM CSS
    ====================================================== -->

    <style>
        /* =====================================================
           ROOT VARIABLES
        ====================================================== */

        :root {

            --bg:
                #0d1117;

            --bg-soft:
                #161b22;

            --bg-card:
                #1c2128;

            --bg-card-soft:
                #21262d;

            --border:
                #30363d;

            --border-soft:
                rgba(255, 255, 255, 0.08);

            --white:
                #ffffff;

            --text:
                #f0f6fc;

            --text-secondary:
                #c9d1d9;

            --muted:
                #8b949e;

            --muted-dark:
                #6e7681;

            --blue:
                #2f81f7;

            --blue-light:
                #58a6ff;

            --cyan:
                #56d4dd;

            --purple:
                #a371f7;

            --pink:
                #f778ba;

            --green:
                #3fb950;

            --danger:
                #f85149;

            --warning:
                #d29922;

            --radius-sm:
                8px;

            --radius-md:
                12px;

            --radius-lg:
                18px;

            --radius-xl:
                26px;

        }


        /* =====================================================
           RESET
        ====================================================== */

        * {

            box-sizing:
                border-box;

        }


        html {

            scroll-behavior:
                smooth;

        }


        body {

            margin:
                0;

            min-height:
                100vh;

            font-family:
                "Inter",
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            color:
                var(--text);

            background:
                var(--bg);

            overflow-x:
                hidden;

        }


        a {

            text-decoration:
                none;

        }


        button,
        input {

            font-family:
                inherit;

        }


        /* =====================================================
           ANIMATED BACKGROUND GRID
        ====================================================== */

        .background-grid {

            position:
                fixed;

            inset:
                0;

            z-index:
                0;

            pointer-events:
                none;

            opacity:
                0.42;

            background-image:

                linear-gradient(rgba(88, 166, 255, 0.035) 1px,
                    transparent 1px),

                linear-gradient(90deg,
                    rgba(88, 166, 255, 0.035) 1px,
                    transparent 1px);

            background-size:
                55px 55px;

            mask-image:
                linear-gradient(to bottom,
                    black 0%,
                    rgba(0, 0, 0, 0.7) 50%,
                    transparent 100%);

        }


        /* =====================================================
           GLOW ORBS
        ====================================================== */

        .glow {

            position:
                fixed;

            z-index:
                0;

            pointer-events:
                none;

            border-radius:
                50%;

            filter:
                blur(110px);

            opacity:
                0.12;

        }


        .glow-blue {

            width:
                420px;

            height:
                420px;

            top:
                -180px;

            left:
                -120px;

            background:
                var(--blue);

        }


        .glow-purple {

            width:
                380px;

            height:
                380px;

            right:
                -170px;

            bottom:
                -120px;

            background:
                var(--purple);

        }


        .glow-cyan {

            width:
                250px;

            height:
                250px;

            top:
                40%;

            left:
                45%;

            background:
                var(--cyan);

            opacity:
                0.06;

        }


        /* =====================================================
           FLOATING PARTICLES
        ====================================================== */

        .particle {

            position:
                fixed;

            width:
                4px;

            height:
                4px;

            border-radius:
                50%;

            background:
                var(--blue-light);

            box-shadow:
                0 0 12px var(--blue-light);

            pointer-events:
                none;

            z-index:
                1;

        }


        .particle-1 {

            top:
                18%;

            left:
                12%;

        }


        .particle-2 {

            top:
                30%;

            right:
                15%;

            background:
                var(--purple);

            box-shadow:
                0 0 12px var(--purple);

        }


        .particle-3 {

            bottom:
                25%;

            left:
                18%;

            background:
                var(--cyan);

            box-shadow:
                0 0 12px var(--cyan);

        }


        .particle-4 {

            bottom:
                17%;

            right:
                25%;

        }


        /* =====================================================
           TOP NAVIGATION
        ====================================================== */

        .topbar {

            position:
                fixed;

            top:
                0;

            left:
                0;

            right:
                0;

            height:
                70px;

            z-index:
                100;

            border-bottom:
                1px solid rgba(255, 255, 255, 0.08);

            background:
                rgba(13, 17, 23, 0.82);

            backdrop-filter:
                blur(18px);

            -webkit-backdrop-filter:
                blur(18px);

        }


        .topbar-inner {

            width:
                min(calc(100% - 36px),
                    1180px);

            height:
                100%;

            margin:
                auto;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

        }


        /* =====================================================
           BRAND
        ====================================================== */

        .brand {

            display:
                flex;

            align-items:
                center;

            gap:
                11px;

            color:
                var(--white);

        }


        .brand:hover {

            color:
                var(--white);

        }


        .brand-logo {

            width:
                39px;

            height:
                39px;

            object-fit:
                contain;

            border-radius:
                9px;

            filter:
                drop-shadow(0 0 15px rgba(88, 166, 255, 0.25));

        }


        .brand-text {

            display:
                flex;

            flex-direction:
                column;

        }


        .brand-name {

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                16px;

            font-weight:
                800;

            line-height:
                1;

        }


        .brand-subtitle {

            margin-top:
                4px;

            color:
                var(--muted);

            font-size:
                9px;

            letter-spacing:
                0.4px;

        }


        /* =====================================================
           TOP RIGHT
        ====================================================== */

        .topbar-right {

            display:
                flex;

            align-items:
                center;

            gap:
                16px;

        }


        .college-label {

            color:
                var(--muted);

            font-size:
                11px;

        }


        .back-link {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                7px;

            color:
                var(--muted);

            font-size:
                12px;

            transition:
                color 0.2s ease,
                transform 0.2s ease;

        }


        .back-link:hover {

            color:
                var(--white);

            transform:
                translateX(-2px);

        }


        /* =====================================================
           MAIN PAGE
        ====================================================== */

        .login-page {

            position:
                relative;

            z-index:
                2;

            min-height:
                100vh;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                110px 20px 60px;

        }


        /* =====================================================
           MAIN LOGIN CARD
        ====================================================== */

        .login-container {

            width:
                100%;

            max-width:
                1080px;

            min-height:
                640px;

            display:
                grid;

            grid-template-columns:
                0.95fr 1fr;

            background:
                rgba(22, 27, 34, 0.92);

            border:
                1px solid var(--border);

            border-radius:
                var(--radius-xl);

            overflow:
                hidden;

            box-shadow:

                0 40px 100px rgba(0, 0, 0, 0.42),

                0 0 0 1px rgba(255, 255, 255, 0.015);

            backdrop-filter:
                blur(20px);

            -webkit-backdrop-filter:
                blur(20px);

            opacity:
                0;

            transform:
                translateY(30px) scale(0.985);

        }


        /* =====================================================
           LEFT PANEL
        ====================================================== */

        .branding-panel {

            position:
                relative;

            overflow:
                hidden;

            padding:
                55px;

            background:

                radial-gradient(circle at 20% 20%,
                    rgba(47, 129, 247, 0.20),
                    transparent 32%),

                radial-gradient(circle at 80% 80%,
                    rgba(163, 113, 247, 0.13),
                    transparent 35%),

                linear-gradient(145deg,
                    #111827,
                    #0d1117);

            border-right:
                1px solid var(--border);

            display:
                flex;

            flex-direction:
                column;

            justify-content:
                space-between;

        }


        /* =====================================================
           ENGINEERING GRID
        ====================================================== */

        .branding-grid {

            position:
                absolute;

            inset:
                0;

            opacity:
                0.5;

            pointer-events:
                none;

            background-image:

                linear-gradient(rgba(88, 166, 255, 0.045) 1px,
                    transparent 1px),

                linear-gradient(90deg,
                    rgba(88, 166, 255, 0.045) 1px,
                    transparent 1px);

            background-size:
                40px 40px;

            mask-image:
                linear-gradient(135deg,
                    black,
                    transparent 70%);

        }


        /* =====================================================
           DECORATIVE CIRCLE
        ====================================================== */

        .branding-circle {

            position:
                absolute;

            width:
                430px;

            height:
                430px;

            border:
                1px solid rgba(88, 166, 255, 0.10);

            border-radius:
                50%;

            right:
                -240px;

            top:
                -170px;

            pointer-events:
                none;

        }


        .branding-circle::before {

            content:
                "";

            position:
                absolute;

            inset:
                55px;

            border:
                1px solid rgba(163, 113, 247, 0.10);

            border-radius:
                50%;

        }


        /* =====================================================
           BRANDING CONTENT
        ====================================================== */

        .brand-content {

            position:
                relative;

            z-index:
                2;

        }


        /* =====================================================
           BRAND ICON
        ====================================================== */

        .brand-icon {

            width:
                62px;

            height:
                62px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            margin-bottom:
                27px;

            border:
                1px solid rgba(88, 166, 255, 0.25);

            border-radius:
                15px;

            background:
                rgba(47, 129, 247, 0.10);

            color:
                var(--blue-light);

            font-size:
                27px;

            box-shadow:
                0 0 35px rgba(47, 129, 247, 0.12);

        }


        /* =====================================================
           BRAND TITLE
        ====================================================== */

        .brand-heading {

            margin:
                0 0 9px;

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                43px;

            line-height:
                1;

            font-weight:
                800;

            letter-spacing:
                -2px;

            color:
                var(--white);

        }


        .brand-heading span {

            background:

                linear-gradient(90deg,
                    var(--blue-light),
                    var(--purple),
                    var(--cyan));

            -webkit-background-clip:
                text;

            background-clip:
                text;

            color:
                transparent;

            background-size:
                200% 200%;

            animation:
                gradientMove 5s ease infinite;

        }


        @keyframes gradientMove {

            0% {
                background-position:
                    0% 50%;
            }

            50% {
                background-position:
                    100% 50%;
            }

            100% {
                background-position:
                    0% 50%;
            }

        }


        .brand-title {

            margin:
                0;

            color:
                var(--text-secondary);

            font-size:
                16px;

            font-weight:
                600;

        }


        .brand-description {

            max-width:
                410px;

            margin:
                22px 0 0;

            color:
                var(--muted);

            font-size:
                13px;

            line-height:
                1.8;

        }


        /* =====================================================
           FEATURE LIST
        ====================================================== */

        .feature-list {

            position:
                relative;

            z-index:
                2;

            margin-top:
                38px;

        }


        .feature-item {

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

            margin-bottom:
                14px;

            color:
                var(--text-secondary);

            font-size:
                12px;

        }


        .feature-icon {

            width:
                31px;

            height:
                31px;

            flex-shrink:
                0;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                1px solid rgba(255, 255, 255, 0.08);

            border-radius:
                8px;

            background:
                rgba(255, 255, 255, 0.035);

            color:
                var(--blue-light);

        }


        /* =====================================================
           TERMINAL-STYLE DECORATION
        ====================================================== */

        .terminal-card {

            position:
                absolute;

            right:
                35px;

            bottom:
                45px;

            width:
                180px;

            padding:
                13px;

            border:
                1px solid rgba(255, 255, 255, 0.09);

            border-radius:
                10px;

            background:
                rgba(13, 17, 23, 0.72);

            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.25);

            transform:
                rotate(-4deg);

            opacity:
                0.85;

        }


        .terminal-top {

            display:
                flex;

            gap:
                5px;

            margin-bottom:
                10px;

        }


        .terminal-dot {

            width:
                6px;

            height:
                6px;

            border-radius:
                50%;

            background:
                #484f58;

        }


        .terminal-line {

            font-family:
                monospace;

            color:
                var(--muted);

            font-size:
                7px;

            line-height:
                1.8;

        }


        .terminal-line span {

            color:
                var(--green);

        }


        /* =====================================================
           BRAND FOOTER
        ====================================================== */

        .brand-footer {

            position:
                relative;

            z-index:
                2;

            color:
                var(--muted-dark);

            font-size:
                10px;

            line-height:
                1.7;

        }


        /* =====================================================
           RIGHT LOGIN PANEL
        ====================================================== */

        .login-panel {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                55px;

            background:
                rgba(22, 27, 34, 0.72);

        }


        .login-content {

            width:
                100%;

            max-width:
                390px;

        }


        /* =====================================================
           MOBILE BRAND
        ====================================================== */

        .mobile-brand {

            display:
                none;

            align-items:
                center;

            gap:
                10px;

            margin-bottom:
                30px;

        }


        .mobile-brand-icon {

            width:
                40px;

            height:
                40px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                1px solid rgba(88, 166, 255, 0.2);

            border-radius:
                9px;

            background:
                rgba(47, 129, 247, 0.08);

            color:
                var(--blue-light);

        }


        .mobile-brand-name {

            font-family:
                "Poppins",
                sans-serif;

            font-weight:
                800;

            font-size:
                17px;

        }


        /* =====================================================
           LOGIN HEADER
        ====================================================== */

        .login-header {

            margin-bottom:
                30px;

        }


        .login-kicker {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                7px;

            margin-bottom:
                15px;

            color:
                var(--blue-light);

            font-size:
                10px;

            font-weight:
                700;

            letter-spacing:
                0.8px;

            text-transform:
                uppercase;

        }


        .login-kicker-dot {

            width:
                6px;

            height:
                6px;

            border-radius:
                50%;

            background:
                var(--green);

            box-shadow:
                0 0 10px rgba(63, 185, 80, 0.8);

        }


        .login-title {

            margin:
                0 0 9px;

            font-family:
                "Poppins",
                sans-serif;

            color:
                var(--white);

            font-size:
                31px;

            line-height:
                1.2;

            font-weight:
                800;

            letter-spacing:
                -1px;

        }


        .login-subtitle {

            margin:
                0;

            color:
                var(--muted);

            font-size:
                13px;

            line-height:
                1.7;

        }


        /* =====================================================
           LOGIN MESSAGE
        ====================================================== */

        #loginMessage {

            display:
                none;

            margin-bottom:
                20px;

            padding:
                12px 14px;

            border-radius:
                9px;

            font-size:
                12px;

            line-height:
                1.5;

            border:
                1px solid transparent;

            animation:
                messageEntrance 0.3s ease;

        }


        @keyframes messageEntrance {

            from {

                opacity:
                    0;

                transform:
                    translateY(-6px);

            }

            to {

                opacity:
                    1;

                transform:
                    translateY(0);

            }

        }


        #loginMessage.alert-success {

            color:
                #aff5b4;

            background:
                rgba(46, 160, 67, 0.10);

            border-color:
                rgba(63, 185, 80, 0.25);

        }


        #loginMessage.alert-danger {

            color:
                #ffb4ae;

            background:
                rgba(248, 81, 73, 0.10);

            border-color:
                rgba(248, 81, 73, 0.25);

        }


        #loginMessage.alert-warning {

            color:
                #e3b341;

            background:
                rgba(210, 153, 34, 0.10);

            border-color:
                rgba(210, 153, 34, 0.25);

        }


        /* =====================================================
           FORM GROUP
        ====================================================== */

        .form-group {

            margin-bottom:
                20px;

        }


        /* =====================================================
           FORM LABEL
        ====================================================== */

        .form-label {

            display:
                block;

            margin-bottom:
                8px;

            color:
                var(--text-secondary);

            font-size:
                12px;

            font-weight:
                600;

        }


        /* =====================================================
           PASSWORD HEADER
        ====================================================== */

        .password-header {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            margin-bottom:
                8px;

        }


        .password-header .form-label {

            margin-bottom:
                0;

        }


        /* =====================================================
           FORGOT PASSWORD
        ====================================================== */

        .forgot-password {

            color:
                var(--blue-light);

            font-size:
                11px;

            font-weight:
                600;

            transition:
                color 0.2s ease;

        }


        .forgot-password:hover {

            color:
                var(--white);

            text-decoration:
                underline;

        }


        /* =====================================================
           INPUT WRAPPER
        ====================================================== */

        .input-wrapper {

            position:
                relative;

        }


        /* =====================================================
           INPUT ICON
        ====================================================== */

        .input-icon {

            position:
                absolute;

            left:
                15px;

            top:
                50%;

            transform:
                translateY(-50%);

            z-index:
                3;

            color:
                var(--muted);

            font-size:
                15px;

            pointer-events:
                none;

            transition:
                color 0.2s ease;

        }


        .input-wrapper:focus-within .input-icon {

            color:
                var(--blue-light);

        }


        /* =====================================================
           FORM CONTROL
        ====================================================== */

        .form-control {

            width:
                100%;

            height:
                51px;

            padding:
                12px 15px 12px 44px;

            color:
                var(--text);

            background:
                #0d1117;

            border:
                1px solid var(--border);

            border-radius:
                8px;

            outline:
                none;

            font-size:
                13px;

            box-shadow:
                none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;

        }


        .form-control:hover {

            border-color:
                #484f58;

        }


        .form-control:focus {

            color:
                var(--text);

            background:
                #0d1117;

            border-color:
                var(--blue);

            box-shadow:
                0 0 0 3px rgba(47, 129, 247, 0.14);

        }


        .form-control::placeholder {

            color:
                #6e7681;

        }


        /* =====================================================
           AUTOFILL FIX
        ====================================================== */

        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover,
        .form-control:-webkit-autofill:focus {

            -webkit-text-fill-color:
                var(--text);

            -webkit-box-shadow:
                0 0 0 1000px #0d1117 inset;

            transition:
                background-color 5000s ease-in-out 0s;

        }


        /* =====================================================
           PASSWORD INPUT
        ====================================================== */

        .password-wrapper .form-control {

            padding-right:
                68px;

        }


        /* =====================================================
           SHOW PASSWORD
        ====================================================== */

        .show-password {

            position:
                absolute;

            right:
                9px;

            top:
                50%;

            transform:
                translateY(-50%);

            border:
                none;

            background:
                transparent;

            color:
                var(--muted);

            padding:
                6px 8px;

            border-radius:
                6px;

            font-size:
                10px;

            font-weight:
                700;

            cursor:
                pointer;

            transition:
                color 0.2s ease,
                background 0.2s ease;

        }


        .show-password:hover {

            color:
                var(--blue-light);

            background:
                rgba(88, 166, 255, 0.07);

        }


        .show-password:focus-visible {

            outline:
                2px solid rgba(88, 166, 255, 0.35);

            outline-offset:
                2px;

        }


        /* =====================================================
           LOGIN BUTTON
        ====================================================== */

        .login-button {

            position:
                relative;

            width:
                100%;

            min-height:
                51px;

            margin-top:
                3px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                9px;

            border:
                1px solid rgba(255, 255, 255, 0.08);

            border-radius:
                8px;

            color:
                white;

            background:
                linear-gradient(135deg,
                    #238636,
                    #2ea043);

            font-size:
                13px;

            font-weight:
                700;

            cursor:
                pointer;

            box-shadow:
                0 10px 30px rgba(46, 160, 67, 0.16);

            overflow:
                hidden;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                opacity 0.2s ease;

        }


        .login-button::before {

            content:
                "";

            position:
                absolute;

            top:
                0;

            left:
                -120%;

            width:
                80%;

            height:
                100%;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.18),
                    transparent);

            transform:
                skewX(-20deg);

            transition:
                left 0.6s ease;

        }


        .login-button:hover::before {

            left:
                140%;

        }


        .login-button:hover {

            color:
                white;

            transform:
                translateY(-2px);

            box-shadow:
                0 15px 35px rgba(46, 160, 67, 0.24);

        }


        .login-button:active {

            transform:
                translateY(0);

        }


        .login-button:disabled {

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
           SECURITY
        ====================================================== */

        .security-note {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                7px;

            margin-top:
                19px;

            color:
                var(--muted-dark);

            font-size:
                10px;

            text-align:
                center;

        }


        .security-note i {

            color:
                var(--green);

            font-size:
                11px;

        }


        /* =====================================================
           REGISTER
        ====================================================== */

        .register {

            margin-top:
                27px;

            padding-top:
                21px;

            border-top:
                1px solid var(--border);

            text-align:
                center;

            color:
                var(--muted);

            font-size:
                11px;

        }


        .register a {

            color:
                var(--blue-light);

            font-weight:
                600;

            margin-left:
                3px;

        }


        .register a:hover {

            color:
                var(--white);

            text-decoration:
                underline;

        }


        /* =====================================================
           MOBILE FOOTER
        ====================================================== */

        .mobile-footer {

            display:
                none;

            margin-top:
                25px;

            color:
                var(--muted-dark);

            font-size:
                9px;

            line-height:
                1.6;

            text-align:
                center;

        }


        /* =====================================================
           TABLET
        ====================================================== */

        @media (max-width: 950px) {

            .login-container {

                max-width:
                    850px;

                grid-template-columns:
                    0.9fr 1fr;

            }


            .branding-panel {

                padding:
                    45px 40px;

            }


            .login-panel {

                padding:
                    45px 40px;

            }


            .brand-heading {

                font-size:
                    37px;

            }


            .terminal-card {

                right:
                    25px;

                bottom:
                    30px;

            }

        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media (max-width: 720px) {

            .topbar {

                height:
                    62px;

            }


            .topbar-inner {

                width:
                    calc(100% - 28px);

            }


            .brand-logo {

                width:
                    35px;

                height:
                    35px;

            }


            .brand-name {

                font-size:
                    15px;

            }


            .brand-subtitle {

                display:
                    none;

            }


            .college-label {

                display:
                    none;

            }


            .login-page {

                min-height:
                    100vh;

                padding:
                    85px 14px 30px;

            }


            .login-container {

                display:
                    block;

                max-width:
                    480px;

                min-height:
                    auto;

                border-radius:
                    18px;

            }


            .branding-panel {

                display:
                    none;

            }


            .login-panel {

                display:
                    block;

                padding:
                    34px 27px 28px;

            }


            .mobile-brand {

                display:
                    flex;

            }


            .login-header {

                margin-bottom:
                    27px;

            }


            .login-title {

                font-size:
                    27px;

            }


            .mobile-footer {

                display:
                    block;

            }

        }


        /* =====================================================
           SMALL MOBILE
        ====================================================== */

        @media (max-width: 390px) {

            .login-page {

                padding:
                    76px 10px 20px;

            }


            .login-container {

                border-radius:
                    15px;

            }


            .login-panel {

                padding:
                    27px 20px 24px;

            }


            .login-title {

                font-size:
                    24px;

            }


            .login-subtitle {

                font-size:
                    12px;

            }


            .password-header {

                gap:
                    8px;

            }


            .forgot-password {

                font-size:
                    10px;

            }

        }


        /* =====================================================
           LANDSCAPE MOBILE
        ====================================================== */

        @media (max-height: 650px) and (max-width: 720px) {

            .login-page {

                align-items:
                    flex-start;

                padding-top:
                    80px;

            }


            .login-panel {

                padding:
                    25px 27px;

            }


            .login-header {

                margin-bottom:
                    20px;

            }


            .form-group {

                margin-bottom:
                    14px;

            }


            .security-note {

                margin-top:
                    13px;

            }


            .register {

                margin-top:
                    18px;

                padding-top:
                    15px;

            }

        }


        /* =====================================================
           REDUCED MOTION
        ====================================================== */

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

                scroll-behavior:
                    auto !important;

            }

        }
    </style>

</head>


<body>


    <!-- =====================================================
         BACKGROUND
    ====================================================== -->

    <div class="background-grid"></div>

    <div class="glow glow-blue"></div>

    <div class="glow glow-purple"></div>

    <div class="glow glow-cyan"></div>


    <!-- =====================================================
         PARTICLES
    ====================================================== -->

    <div class="particle particle-1"></div>

    <div class="particle particle-2"></div>

    <div class="particle particle-3"></div>

    <div class="particle particle-4"></div>


    <!-- =====================================================
         TOP NAVIGATION
    ====================================================== -->

    <header class="topbar">

        <div class="topbar-inner">


            <!-- BRAND -->

            <a
                href="index.php"
                class="brand">

                <img
                    src="./assets/pubmat/head.png"
                    alt="ETS-Async"
                    class="brand-logo">


                <div class="brand-text">

                    <span class="brand-name">
                        ETS-Async
                    </span>

                    <span class="brand-subtitle">
                        Asynchronous Learning Portal
                    </span>

                </div>

            </a>


            <!-- RIGHT SIDE -->

            <div class="topbar-right">

                <span class="college-label">

                    College of Engineering and Architecture

                </span>


                <a
                    href="index.php"
                    class="back-link">

                    <i class="bi bi-arrow-left"></i>

                    Back

                </a>

            </div>


        </div>

    </header>


    <!-- =====================================================
         MAIN LOGIN PAGE
    ====================================================== -->

    <main class="login-page">


        <!-- =================================================
             LOGIN CONTAINER
        ================================================== -->

        <section
            class="login-container"
            id="loginContainer">


            <!-- =================================================
                 LEFT BRANDING PANEL
            ================================================== -->

            <aside
                class="branding-panel"
                aria-label="ETS-Async information">


                <!-- BACKGROUND GRID -->

                <div class="branding-grid"></div>


                <!-- DECORATIVE CIRCLE -->

                <div class="branding-circle"></div>


                <!-- =================================================
                     BRAND CONTENT
                ================================================== -->

                <div class="brand-content">


                    <!-- BRAND ICON -->

                    <div
                        class="brand-icon"
                        id="brandIcon">

                        <i
                            class="bi bi-mortarboard-fill">
                        </i>

                    </div>


                    <!-- BRAND HEADING -->

                    <h2 class="brand-heading">

                        ETS-<span>Async</span>

                    </h2>


                    <p class="brand-title">

                        Asynchronous Learning Portal

                    </p>


                    <!-- DESCRIPTION -->

                    <p class="brand-description">

                        A focused digital learning environment
                        designed for students of the College of
                        Engineering and Architecture.

                    </p>


                    <!-- =================================================
                         FEATURES
                    ================================================== -->

                    <div class="feature-list">


                        <!-- FEATURE 1 -->

                        <div class="feature-item">

                            <span class="feature-icon">

                                <i
                                    class="bi bi-journal-text">
                                </i>

                            </span>

                            <span>
                                Access lectures and learning materials
                            </span>

                        </div>


                        <!-- FEATURE 2 -->

                        <div class="feature-item">

                            <span class="feature-icon">

                                <i
                                    class="bi bi-clipboard-check">
                                </i>

                            </span>

                            <span>
                                Complete academic activities
                            </span>

                        </div>


                        <!-- FEATURE 3 -->

                        <div class="feature-item">

                            <span class="feature-icon">

                                <i
                                    class="bi bi-graph-up-arrow">
                                </i>

                            </span>

                            <span>
                                Monitor your learning progress
                            </span>

                        </div>


                        <!-- FEATURE 4 -->

                        <div class="feature-item">

                            <span class="feature-icon">

                                <i
                                    class="bi bi-tools">
                                </i>

                            </span>

                            <span>
                                Explore engineering learning tools
                            </span>

                        </div>


                    </div>


                </div>


                <!-- =================================================
                     TERMINAL DECORATION
                ================================================== -->

                <div
                    class="terminal-card"
                    aria-hidden="true">


                    <div class="terminal-top">

                        <span class="terminal-dot"></span>

                        <span class="terminal-dot"></span>

                        <span class="terminal-dot"></span>

                    </div>


                    <div class="terminal-line">

                        <span>$</span>
                        ets-async --portal

                    </div>


                    <div class="terminal-line">

                        <span>></span>
                        learning.system.ready

                    </div>


                    <div class="terminal-line">

                        <span>></span>
                        student.access: secure

                    </div>


                </div>


                <!-- =================================================
                     BRAND FOOTER
                ================================================== -->

                <div class="brand-footer">

                    Engineering and Technological
                    Solutions Development

                    <br>

                    ETS-Async Learning Portal

                </div>


            </aside>


            <!-- =================================================
                 RIGHT LOGIN PANEL
            ================================================== -->

            <section class="login-panel">


                <div class="login-content">


                    <!-- =================================================
                         MOBILE BRAND
                    ================================================== -->

                    <div class="mobile-brand">


                        <div
                            class="mobile-brand-icon">

                            <i
                                class="bi bi-mortarboard-fill">
                            </i>

                        </div>


                        <div class="mobile-brand-name">

                            ETS-Async

                        </div>


                    </div>


                    <!-- =================================================
                         LOGIN HEADER
                    ================================================== -->

                    <header class="login-header">


                        <div class="login-kicker">

                            <span
                                class="login-kicker-dot">
                            </span>

                            Secure Student Access

                        </div>


                        <h1 class="login-title">

                            Welcome back

                        </h1>


                        <p class="login-subtitle">

                            Sign in to continue to your
                            ETS-Async learning portal.

                        </p>


                    </header>


                    <!-- =================================================
                         LOGIN MESSAGE
                    ================================================== -->

                    <div
                        id="loginMessage"
                        class="alert"
                        role="alert"
                        aria-live="polite">
                    </div>


                    <!-- =================================================
                         LOGIN FORM
                    ================================================== -->

                    <form
                        id="loginForm"
                        novalidate>


                        <!-- =================================================
                             USERNAME / EMAIL
                        ================================================== -->

                        <div class="form-group">


                            <label
                                for="login"
                                class="form-label">

                                Username or Email

                            </label>


                            <div class="input-wrapper">


                                <!-- FIXED:
                                     BOTH CLASSES ARE NOW
                                     IN THE SAME CLASS ATTRIBUTE
                                -->

                                <i
                                    class="bi bi-person input-icon"
                                    aria-hidden="true">
                                </i>


                                <input
                                    type="text"
                                    class="form-control"
                                    id="login"
                                    name="login"
                                    placeholder="Enter username or email"
                                    autocomplete="username"
                                    autocapitalize="none"
                                    spellcheck="false"
                                    maxlength="100"
                                    required>


                            </div>

                        </div>


                        <!-- =================================================
                             PASSWORD
                        ================================================== -->

                        <div class="form-group">


                            <div class="password-header">


                                <label
                                    for="password"
                                    class="form-label">

                                    Password

                                </label>


                                <a
                                    href="forgot_password.php"
                                    class="forgot-password">

                                    Forgot password?

                                </a>


                            </div>


                            <!-- PASSWORD INPUT -->

                            <div
                                class="input-wrapper password-wrapper">


                                <i
                                    class="bi bi-lock input-icon"
                                    aria-hidden="true">
                                </i>


                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                    maxlength="255"
                                    required>


                                <!-- SHOW / HIDE -->

                                <button
                                    type="button"
                                    class="show-password"
                                    id="togglePassword"
                                    aria-label="Show password">

                                    Show

                                </button>


                            </div>

                        </div>


                        <!-- =================================================
                             LOGIN BUTTON
                        ================================================== -->

                        <button
                            type="submit"
                            class="login-button"
                            id="loginButton">


                            <span
                                id="loginButtonContent">

                                <i
                                    class="bi bi-box-arrow-in-right">
                                </i>

                                Sign in

                            </span>


                        </button>


                    </form>


                    <!-- =================================================
                         SECURITY NOTE
                    ================================================== -->

                    <div class="security-note">

                        <i
                            class="bi bi-shield-lock-fill"
                            aria-hidden="true">
                        </i>

                        <span>

                            Secure access to ETS-Async

                        </span>

                    </div>


                    <!-- =================================================
                         REGISTER
                    ================================================== -->

                    <div class="register">

                        Don't have an account?

                        <a href="register.php">

                            Create an account

                        </a>

                    </div>


                    <!-- =================================================
                         MOBILE FOOTER
                    ================================================== -->

                    <div class="mobile-footer">

                        Engineering and Technological
                        Solutions Development

                        <br>

                        ETS-Async Learning Portal

                    </div>


                </div>


            </section>


        </section>


    </main>


    <!-- =====================================================
         JAVASCRIPT
    ====================================================== -->

    <script>
        /* =====================================================
           LOGIN API
        ====================================================== */

        const LOGIN_API =
            "./api/login.php";


        /* =====================================================
           ELEMENTS
        ====================================================== */

        const loginForm =
            document.getElementById(
                "loginForm"
            );


        const loginInput =
            document.getElementById(
                "login"
            );


        const passwordInput =
            document.getElementById(
                "password"
            );


        const togglePassword =
            document.getElementById(
                "togglePassword"
            );


        const loginButton =
            document.getElementById(
                "loginButton"
            );


        const loginButtonContent =
            document.getElementById(
                "loginButtonContent"
            );


        const messageBox =
            document.getElementById(
                "loginMessage"
            );


        /* =====================================================
           INITIAL PAGE ANIMATION
        ====================================================== */

        anime({

            targets: "#loginContainer",

            opacity: [0, 1],

            translateY: [30, 0],

            scale: [0.985, 1],

            duration: 900,

            easing: "easeOutExpo"

        });


        /* =====================================================
           BRAND ICON ANIMATION
        ====================================================== */

        anime({

            targets: "#brandIcon",

            scale: [0.7, 1],

            rotate: ["-8deg", "0deg"],

            opacity: [0, 1],

            duration: 900,

            delay: 350,

            easing: "easeOutElastic(1, .65)"

        });


        /* =====================================================
           FEATURE ANIMATION
        ====================================================== */

        anime({

            targets: ".feature-item",

            translateX: [-25, 0],

            opacity: [0, 1],

            delay: anime.stagger(
                100, {
                    start: 500
                }
            ),

            duration: 700,

            easing: "easeOutExpo"

        });


        /* =====================================================
           LOGIN FORM ENTRANCE
        ====================================================== */

        anime({

            targets: ".login-header, .form-group, .login-button, .security-note, .register",

            translateY: [18, 0],

            opacity: [0, 1],

            delay: anime.stagger(
                80, {
                    start: 300
                }
            ),

            duration: 700,

            easing: "easeOutExpo"

        });


        /* =====================================================
           PARTICLE ANIMATION
        ====================================================== */

        anime({

            targets: ".particle",

            translateY: function() {

                return anime.random(
                    -35,
                    35
                );

            },

            translateX: function() {

                return anime.random(
                    -25,
                    25
                );

            },

            scale: [{
                    value: 0.6
                },

                {
                    value: 1.4
                },

                {
                    value: 0.6
                }
            ],

            opacity: [{
                    value: 0.25
                },

                {
                    value: 1
                },

                {
                    value: 0.25
                }
            ],

            duration: function() {

                return anime.random(
                    2500,
                    4500
                );

            },

            delay: function() {

                return anime.random(
                    0,
                    1500
                );

            },

            direction: "alternate",

            loop: true,

            easing: "easeInOutSine"

        });


        /* =====================================================
           TERMINAL FLOATING ANIMATION
        ====================================================== */

        anime({

            targets: ".terminal-card",

            translateY: [{
                    value: -7,
                    duration: 1800
                },

                {
                    value: 0,
                    duration: 1800
                }
            ],

            direction: "alternate",

            loop: true,

            easing: "easeInOutSine"

        });


        /* =====================================================
           PASSWORD SHOW / HIDE
        ====================================================== */

        togglePassword.addEventListener(
            "click",
            function() {


                if (
                    passwordInput.type ===
                    "password"
                ) {


                    passwordInput.type =
                        "text";


                    togglePassword.textContent =
                        "Hide";


                    togglePassword.setAttribute(
                        "aria-label",
                        "Hide password"
                    );


                    anime({

                        targets: togglePassword,

                        scale: [0.9, 1],

                        duration: 250,

                        easing: "easeOutBack"

                    });

                } else {


                    passwordInput.type =
                        "password";


                    togglePassword.textContent =
                        "Show";


                    togglePassword.setAttribute(
                        "aria-label",
                        "Show password"
                    );

                }

            }
        );


        /* =====================================================
           SHOW MESSAGE
        ====================================================== */

        function showMessage(
            message,
            type
        ) {


            messageBox.className =
                "alert alert-" +
                type;


            messageBox.textContent =
                message;


            messageBox.style.display =
                "block";


            anime({

                targets: messageBox,

                translateY: [-6, 0],

                opacity: [0, 1],

                duration: 350,

                easing: "easeOutExpo"

            });

        }


        /* =====================================================
           HIDE MESSAGE
        ====================================================== */

        function hideMessage() {

            messageBox.style.display =
                "none";

            messageBox.textContent =
                "";

        }


        /* =====================================================
           SET BUTTON LOADING
        ====================================================== */

        function setButtonLoading(
            loading
        ) {


            if (loading) {


                loginButton.disabled =
                    true;


                loginButtonContent.innerHTML = `

                    <span
                        class="spinner-border spinner-border-sm"
                        role="status"
                        aria-hidden="true">
                    </span>

                    Signing in...

                `;

            } else {


                loginButton.disabled =
                    false;


                loginButtonContent.innerHTML = `

                    <i class="bi bi-box-arrow-in-right"></i>

                    Sign in

                `;

            }

        }


        /* =====================================================
           SHAKE LOGIN FORM
        ====================================================== */

        function shakeLogin() {

            anime({

                targets: "#loginContainer",

                translateX: [{
                        value: -7,
                        duration: 60
                    },

                    {
                        value: 7,
                        duration: 60
                    },

                    {
                        value: -5,
                        duration: 60
                    },

                    {
                        value: 5,
                        duration: 60
                    },

                    {
                        value: 0,
                        duration: 60
                    }
                ],

                easing: "easeInOutSine"

            });

        }


        /* =====================================================
           LOGIN FORM SUBMISSION
        ====================================================== */

        loginForm.addEventListener(
            "submit",
            async function(event) {


                event.preventDefault();


                /* =============================================
                   GET VALUES
                ============================================== */

                const login =
                    loginInput.value.trim();


                const passwordValue =
                    passwordInput.value;


                /* =============================================
                   CLEAR MESSAGE
                ============================================== */

                hideMessage();


                /* =============================================
                   REQUIRED VALIDATION
                ============================================== */

                if (
                    login === "" ||
                    passwordValue === ""
                ) {


                    showMessage(
                        "Please enter your username or email and password.",
                        "warning"
                    );


                    shakeLogin();


                    if (
                        login === ""
                    ) {

                        loginInput.focus();

                    } else {

                        passwordInput.focus();

                    }


                    return;

                }


                /* =============================================
                   LOGIN LENGTH
                ============================================== */

                if (
                    login.length < 4 ||
                    login.length > 100
                ) {


                    showMessage(
                        "Please enter a valid username or email.",
                        "warning"
                    );


                    loginInput.focus();


                    shakeLogin();


                    return;

                }


                /* =============================================
                   PASSWORD LENGTH
                ============================================== */

                if (
                    passwordValue.length < 8
                ) {


                    showMessage(
                        "Incorrect password.",
                        "danger"
                    );


                    passwordInput.focus();


                    shakeLogin();


                    return;

                }


                /* =============================================
                   LOADING
                ============================================== */

                setButtonLoading(
                    true
                );


                /* =============================================
                   API REQUEST
                ============================================== */

                try {


                    const response =
                        await fetch(
                            LOGIN_API, {

                                method: "POST",

                                headers: {
                                    "Content-Type": "application/json",

                                    "Accept": "application/json"
                                },

                                credentials: "same-origin",

                                body: JSON.stringify({

                                    login: login,

                                    password: passwordValue

                                })

                            }
                        );


                    /* =========================================
                       READ RESPONSE
                    ========================================== */

                    let data;


                    try {

                        data =
                            await response.json();

                    } catch (
                        jsonError
                    ) {

                        throw new Error(
                            "Invalid server response."
                        );

                    }


                    console.log(
                        "Login response:",
                        data
                    );


                    /* =========================================
                       SUCCESS
                    ========================================== */

                    if (
                        response.ok &&
                        data.success === true
                    ) {


                        showMessage(
                            data.message ||
                            "Login successful.",
                            "success"
                        );


                        /* =====================================
                           SUCCESS ANIMATION
                        ====================================== */

                        anime({

                            targets: "#loginButton",

                            scale: [1, 1.02, 1],

                            duration: 350,

                            easing: "easeOutQuad"

                        });


                        /* =====================================
                           REDIRECT
                        ====================================== */

                        setTimeout(
                            function() {


                                if (
                                    data.redirect
                                ) {

                                    window.location.href =
                                        data.redirect;

                                } else {

                                    window.location.href =
                                        "index.php";

                                }


                            },
                            650
                        );


                        return;

                    }


                    /* =========================================
                       LOGIN FAILED
                    ========================================== */

                    showMessage(
                        data.message ||
                        "Unable to login. Please check your credentials.",
                        "danger"
                    );


                    shakeLogin();


                    setButtonLoading(
                        false
                    );


                }


                /* =============================================
                   NETWORK ERROR
                ============================================== */
                catch (
                    error
                ) {


                    console.error(
                        "Login error:",
                        error
                    );


                    showMessage(
                        "Unable to connect to the server. Please try again.",
                        "danger"
                    );


                    shakeLogin();


                    setButtonLoading(
                        false
                    );

                }

            }
        );


        /* =====================================================
           ENTER KEY UX
        ====================================================== */

        loginInput.addEventListener(
            "keydown",
            function(event) {


                if (
                    event.key ===
                    "Enter"
                ) {


                    event.preventDefault();


                    passwordInput.focus();

                }

            }
        );


        /* =====================================================
           INPUT FOCUS ANIMATION
        ====================================================== */

        document
            .querySelectorAll(".form-control")
            .forEach(
                function(input) {


                    input.addEventListener(
                        "focus",
                        function() {


                            anime({

                                targets: this,

                                scale: [0.995, 1],

                                duration: 250,

                                easing: "easeOutQuad"

                            });

                        }
                    );

                }
            );
    </script>


</body>

</html>