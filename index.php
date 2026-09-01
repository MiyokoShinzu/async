<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>ETS-Async | Asynchronous Learning Portal</title>


    <!-- =====================================================
         BOOTSTRAP 5
    ===================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =====================================================
         BOOTSTRAP ICONS
    ===================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">


    <!-- =====================================================
         FAVICON
    ===================================================== -->

    <link
        rel="shortcut icon"
        href="./assets/pubmat/head.png"
        type="image/x-icon">


    <!-- =====================================================
         PROFESSIONAL ETS-ASYNC STYLE
    ===================================================== -->

    <style>
        /* =====================================================
           ROOT VARIABLES
        ===================================================== */

        :root {

            --academic-blue: #0B4F8A;

            --academic-blue-dark: #073B66;

            --academic-blue-light: #EAF3FA;

            --tech-blue: #1688D8;

            --tech-purple: #635BFF;

            --text-dark: #17212B;

            --text-muted: #667085;

            --border-color: #E2E8F0;

            --soft-bg: #F7FAFC;

            --white: #FFFFFF;

            --gradient-primary:
                linear-gradient(135deg,
                    #0B4F8A,
                    #073B66);

            --gradient-tech:
                linear-gradient(135deg,
                    #1688D8,
                    #635BFF);

            --shadow-sm:
                0 4px 16px rgba(15, 23, 42, .06);

            --shadow-md:
                0 15px 40px rgba(15, 23, 42, .10);

            --shadow-blue:
                0 10px 35px rgba(11, 79, 138, .18);

        }


        /* =====================================================
           GENERAL
        ===================================================== */

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

            font-family:
                "Segoe UI",
                Tahoma,
                Geneva,
                Verdana,
                sans-serif;

            color:
                var(--text-dark);

            background:
                #FFFFFF;

            line-height:
                1.6;

            overflow-x:
                hidden;

        }


        section {

            scroll-margin-top:
                80px;

            position:
                relative;

        }


        a {

            text-decoration:
                none;

        }


        /* =====================================================
           TECHNOLOGY BACKGROUND
        ===================================================== */

        .tech-background {

            position:
                fixed;

            inset:
                0;

            pointer-events:
                none;

            z-index:
                0;

            overflow:
                hidden;

            opacity:
                .55;

        }


        .tech-grid {

            position:
                absolute;

            inset:
                0;

            background-image:

                linear-gradient(rgba(11, 79, 138, .025) 1px,
                    transparent 1px),

                linear-gradient(90deg,
                    rgba(11, 79, 138, .025) 1px,
                    transparent 1px);

            background-size:
                45px 45px;

            mask-image:
                linear-gradient(to bottom,
                    transparent,
                    black 15%,
                    black 85%,
                    transparent);

        }


        /* =====================================================
           CIRCUIT TRACES
        ===================================================== */

        .circuit {

            position:
                absolute;

            height:
                1px;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(22, 136, 216, .15),
                    rgba(99, 91, 255, .20),
                    transparent);

            transform-origin:
                left center;

            opacity:
                .65;

        }


        .c1 {

            width:
                360px;

            left:
                -40px;

            top:
                22%;

            transform:
                rotate(18deg);

        }


        .c2 {

            width:
                260px;

            left:
                0;

            top:
                44%;

            transform:
                rotate(-8deg);

        }


        .c3 {

            width:
                420px;

            left:
                -100px;

            top:
                72%;

            transform:
                rotate(12deg);

        }


        .c4 {

            width:
                400px;

            right:
                -80px;

            top:
                18%;

            transform:
                rotate(-17deg);

        }


        .c5 {

            width:
                300px;

            right:
                -30px;

            top:
                43%;

            transform:
                rotate(8deg);

        }


        .c6 {

            width:
                420px;

            right:
                -120px;

            top:
                74%;

            transform:
                rotate(-13deg);

        }


        /* =====================================================
           CIRCUIT BRANCHES
        ===================================================== */

        .circuit-branch {

            position:
                absolute;

            width:
                100px;

            height:
                50px;

            border-top:
                1px solid rgba(22, 136, 216, .12);

            border-right:
                1px solid rgba(22, 136, 216, .12);

            border-radius:
                0 30px 0 0;

        }


        .branch1 {

            left:
                12%;

            top:
                28%;

        }


        .branch2 {

            right:
                13%;

            top:
                31%;

            transform:
                scaleX(-1);

        }


        .branch3 {

            left:
                13%;

            top:
                65%;

            transform:
                rotate(180deg);

        }


        .branch4 {

            right:
                14%;

            top:
                67%;

            transform:
                scaleX(-1) rotate(180deg);

        }


        /* =====================================================
           CIRCUIT NODES
        ===================================================== */

        .circuit-node {

            position:
                absolute;

            width:
                7px;

            height:
                7px;

            border-radius:
                50%;

            background:
                var(--tech-blue);

            box-shadow:

                0 0 0 3px rgba(22, 136, 216, .08),

                0 0 15px rgba(22, 136, 216, .35);

            animation:
                nodePulse 2.5s ease-in-out infinite;

        }


        .node1 {

            left:
                17%;

            top:
                21%;

        }


        .node2 {

            left:
                8%;

            top:
                45%;

            animation-delay:
                .7s;

        }


        .node3 {

            left:
                22%;

            top:
                73%;

            animation-delay:
                1.2s;

        }


        .node4 {

            right:
                16%;

            top:
                19%;

            animation-delay:
                .4s;

        }


        .node5 {

            right:
                9%;

            top:
                44%;

            animation-delay:
                1s;

        }


        .node6 {

            right:
                20%;

            top:
                74%;

            animation-delay:
                1.6s;

        }


        @keyframes nodePulse {

            0%,
            100% {

                transform:
                    scale(1);

                opacity:
                    .45;

            }

            50% {

                transform:
                    scale(1.8);

                opacity:
                    1;

            }

        }


        /* =====================================================
           MOVING SIGNALS
        ===================================================== */

        .signal {

            position:
                absolute;

            width:
                7px;

            height:
                7px;

            border-radius:
                50%;

            background:
                var(--tech-blue);

            box-shadow:

                0 0 8px var(--tech-blue),

                0 0 20px rgba(22, 136, 216, .55);

            opacity:
                0;

        }


        .s1 {

            top:
                22%;

            left:
                5%;

            animation:
                signalMoveRight 5s linear infinite;

        }


        .s2 {

            top:
                44%;

            left:
                5%;

            animation:
                signalMoveRight 6s linear 1.5s infinite;

        }


        .s3 {

            top:
                72%;

            left:
                5%;

            animation:
                signalMoveRight 7s linear 3s infinite;

        }


        .s4 {

            top:
                19%;

            right:
                5%;

            animation:
                signalMoveLeft 6s linear 2s infinite;

        }


        @keyframes signalMoveRight {

            0% {

                transform:
                    translateX(0);

                opacity:
                    0;

            }

            15% {

                opacity:
                    1;

            }

            80% {

                opacity:
                    .8;

            }

            100% {

                transform:
                    translateX(420px);

                opacity:
                    0;

            }

        }


        @keyframes signalMoveLeft {

            0% {

                transform:
                    translateX(0);

                opacity:
                    0;

            }

            15% {

                opacity:
                    1;

            }

            80% {

                opacity:
                    .8;

            }

            100% {

                transform:
                    translateX(-420px);

                opacity:
                    0;

            }

        }


        /* =====================================================
           FLOATING MICROPROCESSOR
        ===================================================== */

        .microchip {

            position:
                absolute;

            width:
                82px;

            height:
                82px;

            border:
                1px solid rgba(11, 79, 138, .10);

            border-radius:
                14px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                rgba(255, 255, 255, .60);

            backdrop-filter:
                blur(4px);

            box-shadow:
                0 10px 30px rgba(11, 79, 138, .05);

            animation:
                chipFloat 7s ease-in-out infinite;

        }


        .microchip i {

            font-size:
                34px;

            background:
                var(--gradient-tech);

            -webkit-background-clip:
                text;

            background-clip:
                text;

            -webkit-text-fill-color:
                transparent;

        }


        .chip1 {

            left:
                8%;

            top:
                31%;

        }


        .chip2 {

            right:
                8%;

            top:
                55%;

            animation-delay:
                2s;

        }


        .chip3 {

            left:
                12%;

            top:
                82%;

            transform:
                scale(.75);

            animation-delay:
                3.5s;

        }


        @keyframes chipFloat {

            0%,
            100% {

                transform:
                    translateY(0) rotate(0deg);

            }

            50% {

                transform:
                    translateY(-12px) rotate(2deg);

            }

        }


        /* =====================================================
           CONTENT LAYER
        ===================================================== */

        body>*:not(.tech-background) {

            position:
                relative;

            z-index:
                1;

        }


        /* =====================================================
           SCROLL REVEAL
        ===================================================== */

        .reveal {

            opacity:
                0;

            transform:
                translateY(35px);

            transition:
                opacity .8s ease,
                transform .8s ease;

        }


        .reveal.active {

            opacity:
                1;

            transform:
                translateY(0);

        }


        .reveal-left {

            opacity:
                0;

            transform:
                translateX(-45px);

            transition:
                opacity .8s ease,
                transform .8s ease;

        }


        .reveal-left.active {

            opacity:
                1;

            transform:
                translateX(0);

        }


        .reveal-right {

            opacity:
                0;

            transform:
                translateX(45px);

            transition:
                opacity .8s ease,
                transform .8s ease;

        }


        .reveal-right.active {

            opacity:
                1;

            transform:
                translateX(0);

        }


        /* =====================================================
           NAVBAR
        ===================================================== */

        .navbar {

            background:
                rgba(255, 255, 255, .94);

            border-bottom:
                1px solid var(--border-color);

            box-shadow:
                0 2px 12px rgba(0, 0, 0, .05);

            backdrop-filter:
                blur(15px);

            transition:
                all .3s ease;

        }


        .navbar.scrolled {

            box-shadow:
                0 8px 30px rgba(0, 0, 0, .10);

        }


        .navbar-brand {

            color:
                var(--academic-blue) !important;

            font-weight:
                700;

            font-size:
                1.35rem;

            letter-spacing:
                -.4px;

            display:
                flex;

            align-items:
                center;

        }


        .navbar-brand img {

            transition:
                transform .4s ease;

        }


        .navbar-brand:hover img {

            transform:
                rotate(-8deg) scale(1.1);

        }


        .navbar-brand small {

            display:
                block;

            font-size:
                9px;

            font-weight:
                500;

            color:
                var(--text-muted);

        }


        .nav-link {

            color:
                #495057;

            font-weight:
                500;

            margin:
                0 8px;

            position:
                relative;

            transition:
                color .25s ease;

        }


        .nav-link::after {

            content:
                "";

            position:
                absolute;

            left:
                0;

            bottom:
                -5px;

            width:
                0;

            height:
                3px;

            background:
                var(--academic-blue);

            border-radius:
                3px;

            transition:
                width .3s ease;

        }


        .nav-link:hover::after,
        .nav-link.active::after {

            width:
                100%;

        }


        .nav-link:hover,
        .nav-link.active {

            color:
                var(--academic-blue);

        }


        /* =====================================================
           BUTTONS
        ===================================================== */

        .btn-academic {

            background:
                var(--gradient-primary);

            border:
                none;

            color:
                #FFFFFF;

            font-weight:
                600;

            border-radius:
                7px;

            box-shadow:
                0 5px 15px rgba(11, 79, 138, .25);

            transition:
                transform .25s ease,
                box-shadow .25s ease;

            position:
                relative;

            overflow:
                hidden;

        }


        .btn-academic::before {

            content:
                "";

            position:
                absolute;

            top:
                0;

            left:
                -100%;

            width:
                70%;

            height:
                100%;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, .20),
                    transparent);

            transform:
                skewX(-20deg);

            transition:
                left .6s ease;

        }


        .btn-academic:hover::before {

            left:
                130%;

        }


        .btn-academic:hover {

            color:
                #FFFFFF;

            transform:
                translateY(-3px);

            box-shadow:
                0 10px 25px rgba(11, 79, 138, .32);

        }


        .btn-academic:active {

            transform:
                translateY(0);

        }


        /* =====================================================
           PUBMAT
        ===================================================== */

        .pubmat-section {

            width:
                100%;

            background:
                #FFFFFF;

            overflow:
                hidden;

        }


        .pubmat-full {

            display:
                block;

            width:
                100%;

            height:
                auto;

            animation:
                pubmatReveal 1.2s ease forwards;

        }


        @keyframes pubmatReveal {

            from {

                opacity:
                    0;

                transform:
                    scale(1.025);

            }

            to {

                opacity:
                    1;

                transform:
                    scale(1);

            }

        }


        /* =====================================================
           HERO
        ===================================================== */

        .hero {

            background:
                rgba(255, 255, 255, .88);

            padding:
                95px 0 105px;

            border-bottom:
                1px solid var(--border-color);

            overflow:
                hidden;

        }


        .hero::before {

            content:
                "";

            position:
                absolute;

            width:
                500px;

            height:
                500px;

            border-radius:
                50%;

            background:
                radial-gradient(circle,
                    rgba(11, 79, 138, .06),
                    transparent 70%);

            top:
                -250px;

            right:
                -150px;

            animation:
                heroGlow 8s ease-in-out infinite;

        }


        .hero::after {

            content:
                "";

            position:
                absolute;

            width:
                350px;

            height:
                350px;

            border-radius:
                50%;

            border:
                1px solid rgba(11, 79, 138, .07);

            bottom:
                -180px;

            left:
                -120px;

        }


        @keyframes heroGlow {

            0%,
            100% {

                transform:
                    scale(1);

            }

            50% {

                transform:
                    scale(1.15);

            }

        }


        .hero-content {

            position:
                relative;

            z-index:
                2;

        }


        .hero-badge {

            display:
                inline-flex;

            flex-direction:
                column;

            align-items:
                center;

            background:
                rgba(234, 243, 250, .85);

            color:
                var(--academic-blue);

            padding:
                12px 22px;

            border-radius:
                8px;

            font-size:
                13px;

            font-weight:
                700;

            letter-spacing:
                .8px;

            margin-bottom:
                25px;

            animation:
                fadeDown .8s ease forwards;

            border:
                1px solid rgba(11, 79, 138, .08);

        }


        .hero-logo {

            width:
                100px;

            height:
                100px;

            object-fit:
                contain;

            margin-bottom:
                12px;

            animation:
                logoFloat 4s ease-in-out infinite;

        }


        @keyframes logoFloat {

            0%,
            100% {

                transform:
                    translateY(0);

            }

            50% {

                transform:
                    translateY(-7px);

            }

        }


        .hero h1 {

            font-size:
                48px;

            font-weight:
                700;

            line-height:
                1.15;

            color:
                var(--academic-blue-dark);

            animation:
                fadeUp .9s ease .15s both;

        }


        .text-academic {

            color:
                var(--academic-blue);

        }


        .hero p {

            font-size:
                18px;

            line-height:
                1.75;

            color:
                var(--text-muted);

            max-width:
                850px;

        }


        .hero-buttons {

            animation:
                fadeUp .9s ease .35s both;

        }


        @keyframes fadeUp {

            from {

                opacity:
                    0;

                transform:
                    translateY(25px);

            }

            to {

                opacity:
                    1;

                transform:
                    translateY(0);

            }

        }


        @keyframes fadeDown {

            from {

                opacity:
                    0;

                transform:
                    translateY(-20px);

            }

            to {

                opacity:
                    1;

                transform:
                    translateY(0);

            }

        }


        /* =====================================================
           SECTIONS
        ===================================================== */

        .section-padding {

            padding:
                80px 0;

        }


        .section-soft {

            background:
                rgba(247, 250, 252, .92);

        }


        .section-title {

            color:
                var(--academic-blue-dark);

            font-weight:
                700;

            position:
                relative;

            display:
                inline-block;

        }


        .section-title::after {

            content:
                "";

            display:
                block;

            width:
                50px;

            height:
                3px;

            background:
                var(--academic-blue);

            margin:
                12px auto 0;

            border-radius:
                3px;

            transform:
                scaleX(.6);

            transition:
                transform .5s ease;

        }


        .reveal.active .section-title::after {

            transform:
                scaleX(1);

        }


        .section-subtitle {

            color:
                var(--text-muted);

            max-width:
                700px;

            margin:
                auto;

        }


        /* =====================================================
           INSTRUCTOR CARD
        ===================================================== */

        .instructor-card {

            border:
                1px solid var(--border-color);

            border-radius:
                14px;

            background:
                rgba(255, 255, 255, .95);

            transition:
                transform .35s ease,
                box-shadow .35s ease,
                border-color .35s ease;

            overflow:
                hidden;

            position:
                relative;

        }


        .instructor-card::before {

            content:
                "";

            position:
                absolute;

            top:
                0;

            left:
                0;

            right:
                0;

            height:
                4px;

            background:
                var(--gradient-tech);

        }


        .instructor-card:hover {

            transform:
                translateY(-7px);

            box-shadow:
                var(--shadow-md);

            border-color:
                rgba(11, 79, 138, .20);

        }


        .instructor-photo {

            height:
                100px;

            width:
                100px;

            object-fit:
                cover;

            border-radius:
                50%;

            border:
                4px solid var(--academic-blue-light);

            box-shadow:
                0 5px 15px rgba(0, 0, 0, .08);

            transition:
                transform .4s ease,
                box-shadow .4s ease;

        }


        .instructor-card:hover .instructor-photo {

            transform:
                scale(1.06);

            box-shadow:
                0 8px 25px rgba(11, 79, 138, .18);

        }


        .instructor-line {

            width:
                60px;

            height:
                4px;

            background:
                var(--academic-blue);

            margin:
                20px auto;

            border-radius:
                4px;

        }


        .instructor-name {

            color:
                var(--academic-blue-dark);

            font-weight:
                700;

        }


        .department-badge {

            display:
                inline-block;

            background:
                var(--academic-blue-light);

            color:
                var(--academic-blue);

            padding:
                6px 14px;

            border-radius:
                20px;

            font-size:
                13px;

            font-weight:
                600;

        }


        /* =====================================================
           INFORMATION CARDS
        ===================================================== */

        .info-card {

            height:
                100%;

            background:
                rgba(255, 255, 255, .96);

            border:
                1px solid var(--border-color);

            border-radius:
                12px;

            transition:
                transform .35s ease,
                box-shadow .35s ease,
                border-color .35s ease;

            position:
                relative;

            overflow:
                hidden;

        }


        .info-card::before {

            content:
                "";

            position:
                absolute;

            left:
                0;

            top:
                0;

            width:
                3px;

            height:
                0;

            background:
                var(--gradient-tech);

            transition:
                height .35s ease;

        }


        .info-card:hover::before {

            height:
                100%;

        }


        .info-card:hover {

            border-color:
                rgba(11, 79, 138, .22);

            box-shadow:
                var(--shadow-md);

            transform:
                translateY(-8px);

        }


        .card-number {

            width:
                46px;

            height:
                46px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                var(--academic-blue-light);

            color:
                var(--academic-blue);

            font-weight:
                700;

            border-radius:
                50%;

            margin-bottom:
                20px;

            transition:
                transform .35s ease,
                background .35s ease;

        }


        .info-card:hover .card-number {

            transform:
                scale(1.10) rotate(5deg);

            background:
                var(--academic-blue);

            color:
                #FFFFFF;

        }


        .info-card h5 {

            color:
                var(--academic-blue-dark);

            font-weight:
                700;

        }


        .info-card p {

            color:
                var(--text-muted);

            line-height:
                1.65;

        }


        .info-card h5::before {

            font-family:
                "bootstrap-icons";

            margin-right:
                8px;

            color:
                var(--academic-blue);

        }


        .info-card:nth-child(1) h5::before {

            content:
                "\f3a7";

        }


        .info-card:nth-child(2) h5::before {

            content:
                "\f431";

        }


        .info-card:nth-child(3) h5::before {

            content:
                "\f52d";

        }


        .info-card:nth-child(4) h5::before {

            content:
                "\f26b";

        }


        /* =====================================================
           WEEK CARDS
        ===================================================== */

        .week-card {

            background:
                rgba(255, 255, 255, .97);

            border:
                1px solid var(--border-color);

            border-radius:
                12px;

            height:
                100%;

            overflow:
                hidden;

            transition:
                transform .35s ease,
                box-shadow .35s ease;

        }


        .week-card:hover {

            transform:
                translateY(-7px);

            box-shadow:
                var(--shadow-md);

        }


        .week-header {

            background:
                linear-gradient(135deg,
                    #EAF3FA,
                    #F3F7FB);

            border-bottom:
                1px solid var(--border-color);

            padding:
                22px;

            position:
                relative;

        }


        .week-header::before {

            content:
                "";

            position:
                absolute;

            left:
                0;

            top:
                0;

            width:
                4px;

            height:
                100%;

            background:
                var(--academic-blue);

        }


        .week-label {

            color:
                var(--academic-blue);

            font-size:
                13px;

            font-weight:
                700;

            letter-spacing:
                1px;

        }


        .week-title {

            color:
                var(--academic-blue-dark);

            font-weight:
                700;

            margin-top:
                5px;

        }


        .week-body {

            padding:
                28px;

        }


        .week-body li {

            margin-bottom:
                12px;

            color:
                var(--text-muted);

            position:
                relative;

        }


        .week-body li::marker {

            color:
                var(--academic-blue);

        }


        /* =====================================================
           REMINDER
        ===================================================== */

        .reminder-box {

            border-left:
                5px solid var(--academic-blue);

            background:
                rgba(234, 243, 250, .90);

            padding:
                28px;

            border-radius:
                8px;

            transition:
                transform .3s ease,
                box-shadow .3s ease;

        }


        .reminder-box:hover {

            transform:
                translateX(5px);

            box-shadow:
                var(--shadow-sm);

        }


        .reminder-box h4::before {

            content:
                "\f33b";

            font-family:
                "bootstrap-icons";

            margin-right:
                10px;

            color:
                var(--academic-blue);

        }


        .reminder-box h4 {

            color:
                var(--academic-blue-dark);

            font-weight:
                700;

        }


        /* =====================================================
           LOGIN CTA
        ===================================================== */

        .login-section {

            background:
                linear-gradient(135deg,
                    #0B4F8A,
                    #073B66);

            padding:
                80px 0;

            position:
                relative;

            overflow:
                hidden;

        }


        .login-section::before {

            content:
                "";

            position:
                absolute;

            width:
                450px;

            height:
                450px;

            border:
                1px solid rgba(255, 255, 255, .10);

            border-radius:
                50%;

            right:
                -160px;

            top:
                -220px;

            animation:
                ctaRotate 15s linear infinite;

        }


        .login-section::after {

            content:
                "";

            position:
                absolute;

            width:
                250px;

            height:
                250px;

            border:
                1px solid rgba(255, 255, 255, .08);

            border-radius:
                50%;

            left:
                -120px;

            bottom:
                -130px;

        }


        @keyframes ctaRotate {

            from {

                transform:
                    rotate(0deg);

            }

            to {

                transform:
                    rotate(360deg);

            }

        }


        .login-section h2 {

            color:
                #FFFFFF;

            font-weight:
                700;

            position:
                relative;

        }


        .login-section p {

            color:
                #EAF3FA;

            position:
                relative;

        }


        .btn-login-white {

            background:
                #FFFFFF;

            color:
                var(--academic-blue);

            border:
                2px solid #FFFFFF;

            font-weight:
                700;

            padding:
                12px 35px;

            border-radius:
                7px;

            transition:
                transform .25s ease,
                box-shadow .25s ease;

            position:
                relative;

            z-index:
                2;

        }


        .btn-login-white:hover {

            background:
                #F1F1F1;

            color:
                var(--academic-blue-dark);

            transform:
                translateY(-3px);

            box-shadow:
                0 8px 25px rgba(0, 0, 0, .20);

        }


        /* =====================================================
           FOOTER
        ===================================================== */

        footer {

            background:
                var(--academic-blue-dark);

            color:
                #DCE8F2;

            position:
                relative;

            overflow:
                hidden;

        }


        footer::before {

            content:
                "";

            position:
                absolute;

            top:
                0;

            left:
                0;

            width:
                100%;

            height:
                1px;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, .25),
                    transparent);

        }


        footer strong {

            color:
                #FFFFFF;

        }


        .footer-department {

            color:
                #FFFFFF;

            font-weight:
                600;

        }


        /* =====================================================
           NAVBAR TOGGLER
        ===================================================== */

        .navbar-toggler {

            border:
                1px solid var(--border-color);

            padding:
                7px 10px;

        }


        .navbar-toggler:focus {

            box-shadow:
                0 0 0 .15rem rgba(11, 79, 138, .15);

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 992px) {

            .microchip {

                opacity:
                    .35;

            }

            .circuit {

                opacity:
                    .35;

            }

        }


        @media (max-width: 768px) {

            .hero {

                padding:
                    65px 0 75px;

            }


            .hero h1 {

                font-size:
                    36px;

            }


            .hero p {

                font-size:
                    17px;

            }


            .section-padding {

                padding:
                    60px 0;

            }


            .navbar-brand {

                font-size:
                    1.15rem;

            }


            .hero-logo {

                width:
                    85px;

                height:
                    85px;

            }


            .microchip {

                display:
                    none;

            }


            .circuit {

                opacity:
                    .20;

            }


            .tech-grid {

                background-size:
                    30px 30px;

            }

        }


        /* =====================================================
           REDUCED MOTION
        ===================================================== */

        @media (prefers-reduced-motion: reduce) {

            * {

                scroll-behavior:
                    auto !important;

                animation:
                    none !important;

                transition:
                    none !important;

            }


            .reveal,
            .reveal-left,
            .reveal-right {

                opacity:
                    1;

                transform:
                    none;

            }

        }
    </style>

</head>


<body>


    <!-- =====================================================
         ANIMATED COMPUTER ENGINEERING BACKGROUND
    ===================================================== -->

    <div class="tech-background"
        aria-hidden="true">

        <div class="tech-grid"></div>


        <!-- CIRCUIT TRACES -->

        <div class="circuit c1"></div>

        <div class="circuit c2"></div>

        <div class="circuit c3"></div>

        <div class="circuit c4"></div>

        <div class="circuit c5"></div>

        <div class="circuit c6"></div>


        <!-- CIRCUIT BRANCHES -->

        <div class="circuit-branch branch1"></div>

        <div class="circuit-branch branch2"></div>

        <div class="circuit-branch branch3"></div>

        <div class="circuit-branch branch4"></div>


        <!-- CIRCUIT NODES -->

        <div class="circuit-node node1"></div>

        <div class="circuit-node node2"></div>

        <div class="circuit-node node3"></div>

        <div class="circuit-node node4"></div>

        <div class="circuit-node node5"></div>

        <div class="circuit-node node6"></div>


        <!-- MOVING ELECTRICAL SIGNALS -->

        <div class="signal s1"></div>

        <div class="signal s2"></div>

        <div class="signal s3"></div>

        <div class="signal s4"></div>


        <!-- MICROPROCESSOR ICONS -->

        <div class="microchip chip1">

            <i class="bi bi-cpu"></i>

        </div>


        <div class="microchip chip2">

            <i class="bi bi-cpu-fill"></i>

        </div>


        <div class="microchip chip3">

            <i class="bi bi-memory"></i>

        </div>

    </div>



    <!-- =====================================================
         NAVIGATION
    ===================================================== -->

    <nav
        class="navbar navbar-expand-lg sticky-top"
        id="mainNavbar">

        <div class="container">


            <a
                class="navbar-brand"
                href="index.php">


                <img
                    src="./assets/pubmat/head.png"
                    alt="ETS-Async Logo"
                    style="
                        width:30px;
                        height:30px;
                        object-fit:contain;
                        margin-right:8px;
                    ">


                <div>

                    ETS-Async

                    <small>
                        Asynchronous Learning Portal
                    </small>

                </div>

            </a>



            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav"
                aria-controls="navbarNav"
                aria-expanded="false"
                aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>



            <div
                class="collapse navbar-collapse"
                id="navbarNav">


                <ul
                    class="navbar-nav ms-auto align-items-lg-center">


                    <li class="nav-item">

                        <a
                            class="nav-link active"
                            href="#home">

                            <i class="bi bi-house-door me-1"></i>

                            Home

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="#about">

                            <i class="bi bi-info-circle me-1"></i>

                            About

                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="#plan">

                            <i class="bi bi-journal-bookmark me-1"></i>

                            Learning Plan

                        </a>

                    </li>


                    <li
                        class="nav-item ms-lg-3 mt-2 mt-lg-0">


                        <a
                            href="login.php"
                            class="btn btn-academic px-4">


                            <i class="bi bi-box-arrow-in-right me-1"></i>

                            Login


                        </a>


                    </li>


                </ul>

            </div>

        </div>

    </nav>



    <!-- =====================================================
         PUBMAT
    ===================================================== -->

    <section
        class="pubmat-section">


        <img
            src="assets/pubmat/cpe.png"
            alt="ETS-Async Asynchronous Learning Portal"
            class="pubmat-full">


    </section>



    <!-- =====================================================
         HERO
    ===================================================== -->

    <section
        class="hero"
        id="home">


        <div
            class="container hero-content">


            <div
                class="row">


                <div
                    class="col-lg-9 mx-auto text-center">


                    <div class="hero-badge">


                        <img
                            src="./assets/pubmat/head.png"
                            alt="ETS-Async"
                            class="hero-logo d-block mx-auto">


                        <span>

                            <i class="bi bi-cpu me-1"></i>

                            ASYNCHRONOUS LEARNING PORTAL

                        </span>


                    </div>



                    <h1 class="mb-4">


                        Welcome to


                        <span class="text-academic">

                            ETS-Async

                        </span>


                    </h1>



                    <p class="mb-4 mx-auto">


                        Welcome, College of Engineering and Architecture Students.

                        To support uninterrupted academic delivery during the
                        upcoming two-week asynchronous period, this portal serves
                        as the primary repository for your lessons, required
                        coursework, and formal assessments.


                    </p>



                    <p class="mb-4 mx-auto">


                        All instructional materials have been consolidated here
                        to ensure clear, structured, and continuous engagement.
                        You may access the learning materials at your own pace
                        while completing the assigned activities within the
                        prescribed schedule.


                    </p>



                    <div class="mt-4 hero-buttons">


                        <a
                            href="login.php"
                            class="btn btn-academic btn-lg px-4 me-2 mb-3">


                            <i class="bi bi-box-arrow-in-right me-2"></i>

                            Login to Class


                        </a>



                        <a
                            href="#plan"
                            class="btn btn-outline-secondary btn-lg px-4 mb-3">


                            <i class="bi bi-map me-2"></i>

                            View Learning Plan


                        </a>


                    </div>


                </div>


            </div>


        </div>


    </section>



    <!-- =====================================================
         ABOUT
    ===================================================== -->

    <section
        class="section-padding"
        id="about">


        <div class="container">


            <div
                class="text-center mb-5 reveal">


                <h2 class="section-title">


                    <i class="bi bi-cpu me-2"></i>

                    About ETS-Async


                </h2>


                <p class="section-subtitle mt-3">


                    A centralized asynchronous learning platform
                    designed to support students and instructors
                    across different academic departments.


                </p>


            </div>



            <div
                class="row justify-content-center">


                <div
                    class="col-lg-9 reveal">


                    <div
                        class="instructor-card shadow-sm">


                        <div
                            class="card-body text-center p-4 p-md-5">


                            <img
                                src="assets/pubmat/me_suit.png"
                                alt="Engr. Karl Stephen Evallo"
                                class="instructor-photo mb-3">



                            <h3
                                class="instructor-name">


                                Engr. Karl Stephen Evallo


                            </h3>



                            <p
                                class="text-academic fw-semibold mb-3">


                                <i class="bi bi-mortarboard-fill me-1"></i>

                                Computer Engineering Department


                            </p>



                            <span
                                class="department-badge">


                                <i class="bi bi-person-workspace me-1"></i>

                                Instructor | Web Developer


                            </span>



                            <div
                                class="instructor-line">
                            </div>



                            <p
                                class="text-muted mt-4 mb-0">


                                ETS-Async is an asynchronous learning
                                platform developed by the
                                <strong>
                                    Computer Engineering Department
                                </strong>
                                to provide a structured and accessible
                                online learning environment for students.

                                The platform may be used by different
                                academic departments to deliver lessons,
                                activities, assessments, and other
                                course requirements during asynchronous
                                learning periods.


                            </p>


                        </div>


                    </div>


                </div>


            </div>


        </div>


    </section>



    <!-- =====================================================
         LEARNING JOURNEY
    ===================================================== -->

    <section
        class="section-padding section-soft">


        <div class="container">


            <div
                class="text-center mb-5 reveal">


                <h2 class="section-title">


                    <i class="bi bi-diagram-3 me-2"></i>

                    Your Learning Journey


                </h2>


                <p class="section-subtitle mt-3">


                    Follow these four simple steps throughout
                    the asynchronous learning period.


                </p>


            </div>



            <div class="row g-4">


                <!-- LEARN -->

                <div
                    class="col-md-6 col-lg-3 reveal">


                    <div
                        class="info-card p-4">


                        <div class="card-number">

                            01

                        </div>


                        <h5>
                            Learn
                        </h5>


                        <p>

                            Access the lessons, presentations,
                            videos, readings, and other learning
                            materials provided by your instructor.

                        </p>


                    </div>


                </div>



                <!-- UNDERSTAND -->

                <div
                    class="col-md-6 col-lg-3 reveal">


                    <div
                        class="info-card p-4">


                        <div class="card-number">

                            02

                        </div>


                        <h5>
                            Understand
                        </h5>


                        <p>

                            Study the concepts carefully and review
                            examples and demonstrations to strengthen
                            your understanding.

                        </p>


                    </div>


                </div>



                <!-- APPLY -->

                <div
                    class="col-md-6 col-lg-3 reveal">


                    <div
                        class="info-card p-4">


                        <div class="card-number">

                            03

                        </div>


                        <h5>
                            Apply
                        </h5>


                        <p>

                            Complete the exercises, activities,
                            laboratory tasks, and other requirements
                            assigned by your instructor.

                        </p>


                    </div>


                </div>



                <!-- SUBMIT -->

                <div
                    class="col-md-6 col-lg-3 reveal">


                    <div
                        class="info-card p-4">


                        <div class="card-number">

                            04

                        </div>


                        <h5>
                            Submit
                        </h5>


                        <p>

                            Review your work and submit your required
                            outputs through the designated platform
                            before the given deadline.

                        </p>


                    </div>


                </div>


            </div>


        </div>


    </section>



    <!-- =====================================================
         TWO-WEEK PLAN
    ===================================================== -->

    <section
        class="section-padding"
        id="plan">


        <div class="container">


            <div
                class="text-center mb-5 reveal">


                <h2 class="section-title">


                    <i class="bi bi-calendar3 me-2"></i>

                    Two-Week Learning Plan


                </h2>


                <p class="section-subtitle mt-3">


                    Organize your time and complete the assigned
                    learning tasks throughout the asynchronous period.


                </p>


            </div>



            <div class="row g-4">


                <!-- WEEK 1 -->

                <div
                    class="col-lg-6 reveal-left">


                    <div
                        class="week-card shadow-sm">


                        <div class="week-header">


                            <div class="week-label">

                                <i class="bi bi-calendar-week me-1"></i>

                                WEEK 01

                            </div>


                            <h4 class="week-title mb-0">

                                Learn and Understand

                            </h4>


                        </div>



                        <div class="week-body">


                            <p class="text-muted">


                                Focus on studying the lessons and
                                understanding the concepts introduced
                                by your instructor.


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
                                    Review examples and important
                                    concepts.
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

                <div
                    class="col-lg-6 reveal-right">


                    <div
                        class="week-card shadow-sm">


                        <div class="week-header">


                            <div class="week-label">

                                <i class="bi bi-calendar-check me-1"></i>

                                WEEK 02

                            </div>


                            <h4 class="week-title mb-0">

                                Apply and Assess

                            </h4>


                        </div>



                        <div class="week-body">


                            <p class="text-muted">


                                Apply your knowledge through exercises,
                                activities, assessments, and other
                                course requirements.


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



    <!-- =====================================================
         IMPORTANT REMINDERS
    ===================================================== -->

    <section
        class="section-padding section-soft">


        <div class="container">


            <div
                class="row justify-content-center">


                <div
                    class="col-lg-9 reveal">


                    <div class="reminder-box">


                        <h4 class="mb-3">

                            Important Reminders

                        </h4>


                        <ul class="mb-0 text-muted">


                            <li class="mb-2">

                                Check the learning portal regularly
                                for lessons, announcements, and
                                updated instructions.

                            </li>


                            <li class="mb-2">

                                Read the instructions for every
                                activity carefully before submitting
                                your work.

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

                                If you encounter difficulties,
                                contact your instructor through the
                                designated communication channel.

                            </li>


                        </ul>


                    </div>


                </div>


            </div>


        </div>


    </section>



    <!-- =====================================================
         LOGIN CTA
    ===================================================== -->

    <section
        class="login-section">


        <div
            class="container text-center reveal">


            <h2>

                <i class="bi bi-rocket-takeoff me-2"></i>

                Ready to Begin?

            </h2>


            <p
                class="lead mt-3 mb-4">


                Log in to access your assigned
                courses, lessons, activities,
                and learning materials.


            </p>


            <a
                href="login.php"
                class="btn btn-login-white btn-lg">


                <i class="bi bi-box-arrow-in-right me-2"></i>

                Login to Class


            </a>


        </div>


    </section>



    <!-- =====================================================
         FOOTER
    ===================================================== -->

    <footer
        class="py-4">


        <div
            class="container text-center">


            <p class="mb-1">


                <strong>

                    <i class="bi bi-cpu me-1"></i>

                    ETS-Async

                </strong>


            </p>


            <p class="mb-2">

                Asynchronous Learning Portal

            </p>


            <p class="mb-2">


                Developed by the


                <span class="footer-department">

                    Computer Engineering Department

                </span>


            </p>


            <p class="mb-0">


                <small>


                    Led by

                    <strong>

                        Engr. Karl Stephen Evallo

                    </strong>


                </small>


            </p>


            <small>


                &copy; 2026

                All Rights Reserved.


            </small>


        </div>


    </footer>



    <!-- =====================================================
         BOOTSTRAP JAVASCRIPT
    ===================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>



    <!-- =====================================================
         ETS-ASYNC ANIMATION JAVASCRIPT
    ===================================================== -->

    <script>
        /* =====================================================
           SCROLL REVEAL
        ===================================================== */

        const revealElements =
            document.querySelectorAll(
                ".reveal, .reveal-left, .reveal-right"
            );


        const revealObserver =
            new IntersectionObserver(
                function(entries) {

                    entries.forEach(
                        function(entry) {

                            if (
                                entry.isIntersecting
                            ) {

                                entry.target.classList.add(
                                    "active"
                                );

                                revealObserver.unobserve(
                                    entry.target
                                );

                            }

                        }
                    );

                }, {
                    threshold: 0.15
                }
            );


        revealElements.forEach(
            function(element) {

                revealObserver.observe(
                    element
                );

            }
        );



        /* =====================================================
           NAVBAR SCROLL EFFECT
        ===================================================== */

        const navbar =
            document.getElementById(
                "mainNavbar"
            );


        window.addEventListener(
            "scroll",
            function() {

                if (
                    window.scrollY > 30
                ) {

                    navbar.classList.add(
                        "scrolled"
                    );

                } else {

                    navbar.classList.remove(
                        "scrolled"
                    );

                }

            }
        );



        /* =====================================================
           ACTIVE NAVIGATION
        ===================================================== */

        const sections =
            document.querySelectorAll(
                "section[id]"
            );


        const navLinks =
            document.querySelectorAll(
                ".nav-link"
            );


        window.addEventListener(
            "scroll",
            function() {

                let current =
                    "";


                sections.forEach(
                    function(section) {

                        const sectionTop =
                            section.offsetTop - 120;


                        const sectionHeight =
                            section.offsetHeight;


                        if (
                            window.scrollY >= sectionTop &&
                            window.scrollY <
                            sectionTop + sectionHeight
                        ) {

                            current =
                                section.getAttribute(
                                    "id"
                                );

                        }

                    }
                );


                navLinks.forEach(
                    function(link) {

                        link.classList.remove(
                            "active"
                        );


                        if (
                            link.getAttribute(
                                "href"
                            ) === "#" + current
                        ) {

                            link.classList.add(
                                "active"
                            );

                        }

                    }
                );

            }
        );



        /* =====================================================
           MOBILE NAVBAR CLOSE
        ===================================================== */

        const navbarCollapse =
            document.getElementById(
                "navbarNav"
            );


        document.querySelectorAll(
            ".navbar-nav .nav-link"
        ).forEach(
            function(link) {

                link.addEventListener(
                    "click",
                    function() {

                        if (
                            window.innerWidth < 992
                        ) {

                            const collapse =
                                bootstrap.Collapse
                                .getInstance(
                                    navbarCollapse
                                );


                            if (collapse) {

                                collapse.hide();

                            }

                        }

                    }
                );

            }
        );
    </script>


</body>

</html>