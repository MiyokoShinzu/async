<?php

/* =========================================================
   KAIRO GADDIEL EVALLO
   CHRISTENING & BIRTHDAY INVITATION
   ========================================================= */

date_default_timezone_set('Asia/Manila');


/* =========================================================
   EVENT INFORMATION
   ========================================================= */

$babyName = "Kairo Gaddiel Evallo";

$eventTitle = "Christening & Birthday Celebration";

$eventDate = "10-04-2026";

$eventTime = "9:00 AM";

$venue = "Callao, Peñablanca, Cagayan";


/* =========================================================
   BABY PHOTO
   ========================================================= */

$babyPhoto = "./assets/baby.png";


/* =========================================================
   GODPARENTS
   ========================================================= */

$godfathers = [
    "John Michael Santos",
    "Daniel Rafael Cruz",
    "Mark Anthony Reyes",
    "Christian Paul Garcia",
    "Joshua Miguel Flores"
];

$godmothers = [
    "Maria Angela Santos",
    "Sofia Grace Cruz",
    "Patricia Anne Reyes",
    "Camille Rose Garcia",
    "Nicole Marie Flores"
];


/* =========================================================
   THEME SVG ASSETS
   Direct SVG files
   ========================================================= */

$themeSvgs = [

    "poster" =>
    "https://rawsvg.com/images/file/simple-the-boss-baby-blue-poster-kk57tw58dteruhgk.svg",

    "cute" =>
    "https://rawsvg.com/images/file/cute-boss-baby-movie-poster-ornqpbbtiyngjirj.svg",

    "hands" =>
    "https://rawsvg.com/images/file/boss-baby-hands-on-waist-w9zgnkib2nzho21k.svg",

    "crawl" =>
    "https://rawsvg.com/images/file/boss-baby-crawling-evz1y20fhtg7heyz.svg",

    "lecture" =>
    "https://rawsvg.com/images/file/boss-baby-giving-a-lecture-wli3z9qgwm26hcwk.svg"
];

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($babyName); ?> —
        <?php echo htmlspecialchars($eventTitle); ?>
    </title>


    <!-- =====================================================
         GOOGLE FONTS
         ===================================================== -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&family=Fredoka:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">


    <!-- =====================================================
         BOOTSTRAP 5
         ===================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =====================================================
         FONT AWESOME
         ===================================================== -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    <!-- =====================================================
         FLATICON
         ===================================================== -->

    <link
        rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/3.0.0/uicons-brands/css/uicons-brands.css">

    <link
        rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">

    <link
        rel="stylesheet"
        href="https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css">


    <style>
        /* =====================================================
           ROOT VARIABLES
           ===================================================== */

        :root {

            --blue: #1689e8;
            --deep-blue: #075eb5;
            --sky: #71d7ff;
            --cyan: #55dfff;

            --yellow: #ffd84d;
            --orange: #ff9f43;

            --purple: #9b7cff;

            --green: #55d88a;

            --white: #ffffff;

            --text: #18324a;

            --soft-blue: #eef9ff;

            --soft-yellow: #fff8dc;

            --shadow:
                0 20px 50px rgba(28, 103, 166, .16);

            --shadow-strong:
                0 30px 70px rgba(7, 94, 181, .20);
        }


        /* =====================================================
           GLOBAL
           ===================================================== */

        * {
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {

            margin: 0;

            font-family:
                "Fredoka",
                sans-serif;

            color: var(--text);

            background:
                linear-gradient(180deg,
                    #dff7ff 0%,
                    #ffffff 42%,
                    #fff8e7 100%);

            overflow-x: hidden;
        }


        ::selection {

            background: var(--yellow);

            color: var(--deep-blue);
        }


        button,
        .envelope {

            -webkit-tap-highlight-color:
                transparent;
        }


        /* =====================================================
           COMMON
           ===================================================== */

        .rounded-party {

            border-radius:
                28px;
        }


        /* =====================================================
           INTRO SCREEN
           ===================================================== */

        #intro {

            position: fixed;

            inset: 0;

            z-index: 9999;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;

            overflow: hidden;

            background:

                radial-gradient(circle at 15% 15%,
                    rgba(255, 255, 255, .95),
                    transparent 24%),

                radial-gradient(circle at 85% 20%,
                    rgba(255, 216, 77, .35),
                    transparent 25%),

                radial-gradient(circle at 50% 90%,
                    rgba(85, 216, 138, .20),
                    transparent 25%),

                linear-gradient(135deg,
                    #1689e8,
                    #71d7ff 48%,
                    #fff1a8);

            transition:
                opacity .8s ease,
                visibility .8s ease;
        }


        #intro.hidden {

            opacity: 0;

            visibility: hidden;

            pointer-events: none;
        }


        .intro-content {

            width: 100%;

            max-width: 650px;

            text-align: center;

            position: relative;

            z-index: 50;
        }


        .intro-small {

            font-size: .95rem;

            font-weight: 700;

            color: white;

            letter-spacing: 3px;

            text-transform: uppercase;

            margin-bottom: 15px;

            text-shadow:
                0 3px 10px rgba(0, 70, 130, .15);

            animation:
                introTextFloat 3s ease-in-out infinite;
        }


        .intro-title {

            font-family:
                "Baloo 2",
                sans-serif;

            font-size:
                clamp(2.8rem, 8vw, 5.5rem);

            font-weight: 800;

            line-height: .95;

            color: white;

            text-shadow:
                0 5px 0 rgba(0, 80, 150, .15),
                0 12px 25px rgba(0, 0, 0, .12);

            margin-bottom: 10px;

            animation:
                introTitleFloat 4s ease-in-out infinite;
        }


        .intro-subtitle {

            color: white;

            font-size: 1.05rem;

            margin-bottom: 25px;

            text-shadow:
                0 2px 8px rgba(0, 60, 120, .15);
        }


        @keyframes introTextFloat {

            0%,
            100% {
                transform:
                    translateY(0);
            }

            50% {
                transform:
                    translateY(-5px);
            }
        }


        @keyframes introTitleFloat {

            0%,
            100% {
                transform:
                    translateY(0) rotate(-1deg);
            }

            50% {
                transform:
                    translateY(-9px) rotate(1deg);
            }
        }


        /* =====================================================
           INTRO SKY DECORATIONS
           ===================================================== */

        .intro-decor {

            position: absolute;

            inset: 0;

            pointer-events: none;

            overflow: hidden;

            z-index: 5;
        }


        .intro-cloud {

            position: absolute;

            color: white;

            opacity: .85;

            filter:
                drop-shadow(0 8px 12px rgba(0, 80, 130, .08));

            animation:
                cloudFloat 15s ease-in-out infinite;
        }


        .intro-cloud-1 {

            top: 8%;

            left: 5%;

            font-size: 60px;
        }


        .intro-cloud-2 {

            top: 16%;

            right: 5%;

            font-size: 80px;

            animation-delay:
                -5s;
        }


        .intro-cloud-3 {

            bottom: 9%;

            left: 8%;

            font-size: 50px;

            animation-delay:
                -9s;
        }


        @keyframes cloudFloat {

            0%,
            100% {

                transform:
                    translate3d(-10px,
                        0,
                        0);
            }

            50% {

                transform:
                    translate3d(25px,
                        -15px,
                        0);
            }
        }


        /* =====================================================
           INTRO RAINBOW
           ===================================================== */

        .rainbow {

            position: absolute;

            width: 250px;

            height: 125px;

            left: 50%;

            top: 7%;

            transform:
                translateX(-50%);

            border-radius:
                250px 250px 0 0;

            background:

                conic-gradient(from 180deg,
                    #1689e8,
                    #55dfff,
                    #55d88a,
                    #ffd84d,
                    #ff9f43,
                    #1689e8);

            padding: 12px;

            opacity: .55;

            animation:
                rainbowFloat 5s ease-in-out infinite;

            z-index: 1;
        }


        .rainbow::after {

            content: "";

            display: block;

            width: 100%;

            height: 100%;

            background:
                rgba(255, 255, 255, .88);

            border-radius:
                inherit;
        }


        @keyframes rainbowFloat {

            0%,
            100% {

                transform:
                    translateX(-50%) translateY(0) rotate(-2deg);
            }

            50% {

                transform:
                    translateX(-50%) translateY(-12px) rotate(2deg);
            }
        }


        /* =====================================================
           3D INTRO OBJECTS
           ===================================================== */

        .intro-3d {

            position: absolute;

            pointer-events: none;

            z-index: 8;

            transform-style:
                preserve-3d;

            will-change:
                transform;
        }


        .cube-3d {

            width: 55px;

            height: 55px;

            transform-style:
                preserve-3d;

            animation:
                cubeRotate 8s linear infinite;
        }


        .cube-3d .face {

            position: absolute;

            width: 55px;

            height: 55px;

            border:
                2px solid rgba(255, 255, 255, .7);

            background:
                linear-gradient(135deg,
                    rgba(255, 255, 255, .7),
                    rgba(22, 137, 232, .35));

            backdrop-filter:
                blur(4px);
        }


        .cube-front {

            transform:
                translateZ(27.5px);
        }


        .cube-back {

            transform:
                rotateY(180deg) translateZ(27.5px);
        }


        .cube-right {

            transform:
                rotateY(90deg) translateZ(27.5px);
        }


        .cube-left {

            transform:
                rotateY(-90deg) translateZ(27.5px);
        }


        .cube-top {

            transform:
                rotateX(90deg) translateZ(27.5px);
        }


        .cube-bottom {

            transform:
                rotateX(-90deg) translateZ(27.5px);
        }


        @keyframes cubeRotate {

            0% {

                transform:
                    rotateX(0deg) rotateY(0deg) rotateZ(0deg);
            }

            50% {

                transform:
                    rotateX(180deg) rotateY(180deg) rotateZ(20deg);
            }

            100% {

                transform:
                    rotateX(360deg) rotateY(360deg) rotateZ(0deg);
            }
        }


        .intro-cube-1 {

            left: 7%;

            top: 28%;

            animation:
                cubeDrift1 7s ease-in-out infinite;
        }


        .intro-cube-2 {

            right: 8%;

            bottom: 18%;

            transform:
                scale(.75);

            animation:
                cubeDrift2 9s ease-in-out infinite;
        }


        @keyframes cubeDrift1 {

            0%,
            100% {
                transform:
                    translate3d(0, 0, 0) rotateX(0) rotateY(0);
            }

            50% {
                transform:
                    translate3d(25px, -30px, 40px) rotateX(180deg) rotateY(180deg);
            }
        }


        @keyframes cubeDrift2 {

            0%,
            100% {
                transform:
                    scale(.75) translate3d(0, 0, 0) rotate(0);
            }

            50% {
                transform:
                    scale(.75) translate3d(-30px, -25px, 30px) rotate(180deg);
            }
        }


        .orb-3d {

            width: 42px;

            height: 42px;

            border-radius: 50%;

            background:
                radial-gradient(circle at 30% 25%,
                    white,
                    #55dfff 35%,
                    #1689e8 75%);

            box-shadow:
                inset -8px -10px 12px rgba(0, 0, 0, .12),
                0 18px 35px rgba(0, 80, 150, .18);

            animation:
                orbFloat 5s ease-in-out infinite;
        }


        .orb-1 {

            left: 15%;

            bottom: 20%;
        }


        .orb-2 {

            right: 16%;

            top: 30%;

            width: 30px;

            height: 30px;

            background:
                radial-gradient(circle at 30% 25%,
                    white,
                    #ffd84d 40%,
                    #ff9f43 80%);

            animation-delay:
                -2s;
        }


        @keyframes orbFloat {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0) rotate(0);
            }

            50% {

                transform:
                    translate3d(15px, -30px, 45px) rotate(180deg);
            }
        }


        /* =====================================================
           3D BALLOONS
           ===================================================== */

        .balloon {

            width: 58px;

            height: 70px;

            border-radius:
                50% 50% 45% 45%;

            position: absolute;

            box-shadow:
                inset -10px -12px 0 rgba(0, 0, 0, .07),
                inset 8px 8px 0 rgba(255, 255, 255, .15),
                0 15px 30px rgba(0, 0, 0, .10);

            animation:
                balloonFloat 5s ease-in-out infinite;

            will-change:
                transform;
        }


        .balloon::before {

            content: "";

            position: absolute;

            bottom: -13px;

            left: 50%;

            transform:
                translateX(-50%);

            width: 0;

            height: 0;

            border-left:
                6px solid transparent;

            border-right:
                6px solid transparent;

            border-top:
                10px solid currentColor;
        }


        .balloon::after {

            content: "";

            position: absolute;

            left: 50%;

            top: 100%;

            width: 1px;

            height: 100px;

            background:
                rgba(80, 100, 120, .4);
        }


        .balloon-blue {

            background:
                #42b9ff;

            color:
                #42b9ff;
        }


        .balloon-yellow {

            background:
                #ffd83d;

            color:
                #ffd83d;
        }


        .balloon-purple {

            background:
                #9a7cff;

            color:
                #9a7cff;
        }


        .balloon-green {

            background:
                #55d88a;

            color:
                #55d88a;
        }


        .balloon-1 {

            left: 3%;

            top: 22%;

            animation-delay:
                -.8s;
        }


        .balloon-2 {

            right: 3%;

            top: 34%;

            animation-delay:
                -2s;
        }


        .balloon-3 {

            left: 9%;

            top: 70%;

            --balloon-scale:
                .75;

            animation-delay:
                -3s;
        }


        .balloon-4 {

            right: 11%;

            top: 73%;

            --balloon-scale:
                .8;

            animation-delay:
                -1.2s;
        }


        @keyframes balloonFloat {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0) rotate(-5deg) scale(var(--balloon-scale, 1));
            }

            50% {

                transform:
                    translate3d(10px, -26px, 25px) rotate(6deg) scale(var(--balloon-scale, 1));
            }
        }


        /* =====================================================
           ENVELOPE SCENE
           ===================================================== */

        .envelope-scene {

            position: relative;

            width:
                min(400px, 85vw);

            height: 270px;

            margin:
                5px auto 10px;

            perspective:
                1200px;
        }


        .envelope {

            position: absolute;

            width: 100%;

            height: 220px;

            left: 0;

            bottom: 0;

            transform-style:
                preserve-3d;

            cursor: pointer;

            animation:
                envelopeIdle 4s ease-in-out infinite;

            will-change:
                transform;

            outline: none;
        }


        .envelope:hover {

            filter:
                drop-shadow(0 18px 25px rgba(0, 70, 130, .12));
        }


        .envelope.opened {

            animation: none;

            transform:
                perspective(1200px) translateY(0);
        }


        @keyframes envelopeIdle {

            0%,
            100% {

                transform:
                    perspective(1200px) rotateX(0deg) rotateY(-1deg) translate3d(0, 0, 0);
            }

            50% {

                transform:
                    perspective(1200px) rotateX(1deg) rotateY(2deg) translate3d(0, -9px, 10px);
            }
        }


        .envelope-body {

            position: absolute;

            inset: 0;

            background:
                linear-gradient(145deg,
                    #ffffff,
                    #d9f3ff);

            border-radius:
                14px 14px 18px 18px;

            box-shadow:
                0 30px 45px rgba(0, 87, 145, .25);

            overflow: hidden;

            border:
                3px solid rgba(255, 255, 255, .85);
        }


        .envelope-body::before {

            content: "";

            position: absolute;

            left: 0;

            bottom: 0;

            width: 0;

            height: 0;

            border-left:
                200px solid transparent;

            border-right:
                200px solid transparent;

            border-bottom:
                125px solid rgba(22, 137, 232, .13);

            transform:
                translateY(40px);
        }


        .envelope-left {

            position: absolute;

            left: 0;

            bottom: 0;

            width: 0;

            height: 0;

            border-top:
                110px solid transparent;

            border-bottom:
                110px solid transparent;

            border-left:
                200px solid rgba(22, 137, 232, .12);
        }


        .envelope-right {

            position: absolute;

            right: 0;

            bottom: 0;

            width: 0;

            height: 0;

            border-top:
                110px solid transparent;

            border-bottom:
                110px solid transparent;

            border-right:
                200px solid rgba(22, 137, 232, .12);
        }


        .envelope-flap {

            position: absolute;

            left: 0;

            top: 0;

            width: 100%;

            height: 125px;

            background:
                linear-gradient(160deg,
                    #ffffff,
                    #d9f3ff);

            clip-path:
                polygon(0 0,
                    100% 0,
                    50% 100%);

            transform-origin:
                top center;

            transform:
                rotateX(0deg);

            transition:
                transform 1s cubic-bezier(.2, .8, .2, 1);

            z-index: 5;

            filter:
                drop-shadow(0 8px 10px rgba(0, 80, 140, .12));
        }


        .envelope.opened .envelope-flap {

            transform:
                rotateX(-180deg);

            z-index: 1;
        }


        .envelope-card {

            position: absolute;

            left: 8%;

            width: 84%;

            height: 190px;

            bottom: 10px;

            background: white;

            border-radius: 15px;

            box-shadow:
                0 15px 35px rgba(0, 0, 0, .15);

            padding: 20px;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            transform:
                translateY(0);

            transition:
                transform 1s cubic-bezier(.2, .8, .2, 1);

            z-index: 2;
        }


        .envelope.opened .envelope-card {

            transform:
                translateY(-155px);
        }


        .card-mini-title {

            font-size: .75rem;

            font-weight: 700;

            letter-spacing: 2px;

            color: var(--blue);

            text-transform: uppercase;
        }


        .card-name {

            font-family:
                "Baloo 2",
                sans-serif;

            font-size: 2.1rem;

            font-weight: 800;

            color: var(--deep-blue);

            line-height: 1;

            margin: 7px 0;
        }


        .card-text {

            font-size: .85rem;

            color: #698197;
        }


        .seal {

            position: absolute;

            left: 50%;

            top: 50%;

            transform:
                translate(-50%, -20%);

            width: 65px;

            height: 65px;

            border-radius: 50%;

            background:
                radial-gradient(circle,
                    #ffe873,
                    #ffb52e);

            border:
                5px solid white;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #8a5800;

            font-size: 1.7rem;

            box-shadow:
                0 8px 20px rgba(0, 0, 0, .15);

            z-index: 8;

            transition:
                opacity .5s ease;
        }


        .envelope.opened .seal {

            opacity: 0;
        }


        .envelope-hint {

            margin-top: 10px;

            color: white;

            font-size: .9rem;

            font-weight: 600;

            animation:
                hintPulse 2s ease-in-out infinite;
        }


        .envelope-hint i {

            margin-right: 6px;
        }


        @keyframes hintPulse {

            0%,
            100% {
                opacity: .7;
                transform:
                    translateY(0);
            }

            50% {
                opacity: 1;
                transform:
                    translateY(-4px);
            }
        }


        /* =====================================================
           MAIN PAGE
           ===================================================== */

        #page {

            position: relative;

            z-index: 5;

            opacity: 0;

            transform:
                translateY(20px);

            transition:
                opacity 1s ease,
                transform 1s ease;
        }


        #page.show {

            opacity: 1;

            transform:
                translateY(0);
        }


        /* =====================================================
           SECTION DECORATION
           ===================================================== */

        .section {

            position: relative;

            padding:
                100px 0;

            overflow: hidden;

            isolation: isolate;
        }


        .section-decor {

            position: absolute;

            inset: 0;

            pointer-events: none;

            overflow: hidden;

            z-index: 1;
        }


        .content-layer {

            position: relative;

            z-index: 10;
        }


        /* =====================================================
           3D FLOATING SHAPES
           ===================================================== */

        .float-3d {

            position: absolute;

            pointer-events: none;

            user-select: none;

            z-index: 2;

            transform-style:
                preserve-3d;

            will-change:
                transform;
        }


        .glass-ring {

            width: 95px;

            height: 95px;

            border-radius: 50%;

            border:
                9px solid rgba(22, 137, 232, .16);

            box-shadow:
                inset 0 0 15px rgba(255, 255, 255, .9),
                0 15px 30px rgba(22, 137, 232, .10);

            animation:
                ringSpin 9s linear infinite;
        }


        .glass-ring::before {

            content: "";

            position: absolute;

            inset: 14px;

            border-radius: 50%;

            border:
                4px dashed rgba(255, 216, 77, .65);
        }


        @keyframes ringSpin {

            0% {

                transform:
                    rotateX(65deg) rotateY(0deg) rotateZ(0deg) translateY(0);
            }

            50% {

                transform:
                    rotateX(65deg) rotateY(180deg) rotateZ(180deg) translateY(-25px);
            }

            100% {

                transform:
                    rotateX(65deg) rotateY(360deg) rotateZ(360deg) translateY(0);
            }
        }


        .diamond-3d {

            width: 48px;

            height: 48px;

            background:
                linear-gradient(135deg,
                    #71d7ff,
                    #1689e8);

            transform:
                rotate(45deg);

            border-radius: 9px;

            box-shadow:
                12px 12px 25px rgba(0, 80, 150, .14);

            animation:
                diamondFloat 5s ease-in-out infinite;
        }


        .diamond-3d::after {

            content: "";

            position: absolute;

            inset: 8px;

            border:
                2px solid rgba(255, 255, 255, .65);

            border-radius: 5px;
        }


        @keyframes diamondFloat {

            0%,
            100% {

                transform:
                    rotate(45deg) translate3d(0, 0, 0);
            }

            50% {

                transform:
                    rotate(225deg) translate3d(20px, -30px, 25px);
            }
        }


        .mini-star-3d {

            font-size: 42px;

            color: var(--yellow);

            text-shadow:
                5px 7px 0 rgba(255, 159, 67, .35),
                0 12px 20px rgba(0, 0, 0, .10);

            animation:
                star3d 4s ease-in-out infinite;
        }


        @keyframes star3d {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0) rotate(0deg) scale(.9);
            }

            50% {

                transform:
                    translate3d(15px, -22px, 35px) rotate(180deg) scale(1.15);
            }
        }


        .bubble-3d {

            width: 70px;

            height: 70px;

            border-radius: 50%;

            background:
                radial-gradient(circle at 25% 20%,
                    rgba(255, 255, 255, .95),
                    rgba(113, 215, 255, .35) 30%,
                    rgba(22, 137, 232, .12) 70%,
                    transparent 72%);

            border:
                2px solid rgba(255, 255, 255, .8);

            box-shadow:
                inset -12px -15px 25px rgba(22, 137, 232, .08),
                0 15px 35px rgba(22, 137, 232, .10);

            animation:
                bubbleFloat 6s ease-in-out infinite;
        }


        @keyframes bubbleFloat {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0) scale(1);
            }

            50% {

                transform:
                    translate3d(-20px, -35px, 40px) scale(1.12);
            }
        }


        /* =====================================================
           HERO
           ===================================================== */

        .hero {

            min-height:
                100vh;

            display:
                flex;

            align-items:
                center;

            position:
                relative;

            padding:
                100px 0 80px;

            overflow:
                hidden;

            background:

                radial-gradient(circle at 50% 25%,
                    rgba(255, 255, 255, .8),
                    transparent 27%),

                linear-gradient(180deg,
                    #bceeff 0%,
                    #effbff 65%,
                    #ffffff 100%);
        }


        .hero-content {

            position:
                relative;

            z-index:
                10;

            text-align:
                center;
        }


        .little-boss {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                8px;

            background:
                white;

            padding:
                8px 18px;

            border-radius:
                50px;

            color:
                var(--deep-blue);

            font-weight:
                700;

            font-size:
                .85rem;

            box-shadow:
                0 8px 25px rgba(0, 90, 150, .10);

            margin-bottom:
                18px;

            animation:
                badgeFloat 3s ease-in-out infinite;
        }


        @keyframes badgeFloat {

            0%,
            100% {
                transform:
                    translateY(0);
            }

            50% {
                transform:
                    translateY(-6px);
            }
        }


        .hero-title {

            font-family:
                "Baloo 2",
                sans-serif;

            font-size:
                clamp(3.5rem, 11vw, 8rem);

            font-weight:
                800;

            line-height:
                .82;

            color:
                var(--deep-blue);

            margin:
                0;

            text-shadow:
                0 5px 0 rgba(255, 255, 255, .8);
        }


        .hero-title .blue-word {

            color:
                var(--blue);
        }


        .hero-subtitle {

            font-size:
                clamp(1.2rem, 3vw, 1.8rem);

            font-weight:
                600;

            color:
                #45677f;

            margin:
                20px auto 35px;

            max-width:
                700px;
        }


        /* =====================================================
           BABY PHOTO
           ===================================================== */

        .baby-photo-wrap {

            position:
                relative;

            width:
                min(350px, 72vw);

            height:
                min(350px, 72vw);

            margin:
                20px auto 35px;

            z-index:
                10;
        }


        .baby-photo-bg {

            position:
                absolute;

            inset:
                0;

            border-radius:
                50%;

            background:
                linear-gradient(135deg,
                    #ffffff,
                    #bceeff);

            box-shadow:
                0 25px 70px rgba(26, 128, 194, .20);

            animation:
                photoPulse 4s ease-in-out infinite;
        }


        .baby-photo-bg::before {

            content:
                "";

            position:
                absolute;

            inset:
                13px;

            border:
                7px dashed rgba(22, 137, 232, .25);

            border-radius:
                50%;

            animation:
                rotateRing 18s linear infinite;
        }


        .baby-photo {

            position:
                absolute;

            inset:
                23px;

            width:
                calc(100% - 46px);

            height:
                calc(100% - 46px);

            object-fit:
                cover;

            border-radius:
                50%;

            border:
                8px solid white;

            box-shadow:
                0 15px 40px rgba(0, 0, 0, .15);
        }


        .photo-badge {

            position:
                absolute;

            right:
                -10px;

            bottom:
                20px;

            background:
                var(--yellow);

            color:
                #7b5700;

            width:
                75px;

            height:
                75px;

            border-radius:
                50%;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            text-align:
                center;

            font-weight:
                800;

            font-size:
                .8rem;

            line-height:
                1;

            transform:
                rotate(10deg);

            box-shadow:
                0 12px 25px rgba(0, 0, 0, .12);

            animation:
                badgeFloat 3s ease-in-out infinite;
        }


        @keyframes photoPulse {

            0%,
            100% {
                transform:
                    scale(1);
            }

            50% {
                transform:
                    scale(1.025);
            }
        }


        @keyframes rotateRing {

            to {
                transform:
                    rotate(360deg);
            }
        }


        /* =====================================================
           THEME SVG OBJECTS
           ===================================================== */

        .theme-svg {

            position:
                absolute;

            z-index:
                4;

            pointer-events:
                none;

            user-select:
                none;

            background:
                transparent;

            filter:
                drop-shadow(0 15px 18px rgba(0, 80, 140, .12));

            will-change:
                transform;
        }


        .theme-svg img {

            display:
                block;

            width:
                100%;

            height:
                auto;

            background:
                transparent;
        }


        .svg-hero {

            width:
                clamp(150px, 22vw, 270px);

            right:
                1%;

            bottom:
                2%;

            animation:
                heroCharacterFloat 5s ease-in-out infinite;
        }


        @keyframes heroCharacterFloat {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0) rotate(1deg);
            }

            50% {

                transform:
                    translate3d(10px, -22px, 20px) rotate(-2deg);
            }
        }


        .svg-left {

            width:
                clamp(120px, 17vw, 220px);

            left:
                -15px;

            top:
                40%;

            animation:
                characterSway 6s ease-in-out infinite;
        }


        @keyframes characterSway {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0) rotate(-3deg);
            }

            50% {

                transform:
                    translate3d(12px, -16px, 20px) rotate(3deg);
            }
        }


        .svg-crawl {

            width:
                clamp(160px, 24vw, 290px);

            left:
                2%;

            bottom:
                4%;

            animation:
                crawlBounce 4s ease-in-out infinite;
        }


        @keyframes crawlBounce {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0) rotate(-2deg);
            }

            50% {

                transform:
                    translate3d(15px, -18px, 15px) rotate(2deg);
            }
        }


        .svg-lecture {

            width:
                clamp(130px, 19vw, 235px);

            right:
                1%;

            bottom:
                2%;

            animation:
                lectureMove 5s ease-in-out infinite;
        }


        @keyframes lectureMove {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0) rotate(2deg);
            }

            50% {

                transform:
                    translate3d(-10px, -15px, 20px) rotate(-2deg);
            }
        }


        .svg-cute {

            width:
                clamp(120px, 18vw, 225px);

            left:
                1%;

            top:
                5%;

            animation:
                cuteDrift 7s ease-in-out infinite;
        }


        @keyframes cuteDrift {

            0%,
            100% {

                transform:
                    translate3d(0, 0, 0) rotate(-3deg);
            }

            50% {

                transform:
                    translate3d(15px, -20px, 20px) rotate(3deg);
            }
        }


        /* =====================================================
           HERO 3D DECORATIONS
           ===================================================== */

        .hero-ring {

            left:
                7%;

            top:
                18%;
        }


        .hero-diamond {

            right:
                12%;

            top:
                22%;
        }


        .hero-star {

            right:
                22%;

            top:
                11%;
        }


        .hero-bubble {

            left:
                15%;

            bottom:
                15%;
        }


        /* =====================================================
           EVENT SECTION
           ===================================================== */

        .event-section {

            background:
                #ffffff;
        }


        .section-title {

            font-family:
                "Baloo 2",
                sans-serif;

            font-size:
                clamp(2.4rem, 6vw, 4rem);

            font-weight:
                800;

            color:
                var(--deep-blue);

            line-height:
                1;

            margin-bottom:
                12px;
        }


        .section-lead {

            color:
                #688198;

            max-width:
                650px;

            margin:
                0 auto 45px;

            font-size:
                1.05rem;
        }


        .event-card {

            position:
                relative;

            background:
                linear-gradient(145deg,
                    #ffffff,
                    #f3fbff);

            border:
                2px solid rgba(22, 137, 232, .08);

            border-radius:
                30px;

            padding:
                35px 25px;

            height:
                100%;

            box-shadow:
                var(--shadow);

            text-align:
                center;

            overflow:
                hidden;

            transition:
                transform .35s ease,
                box-shadow .35s ease;
        }


        .event-card::before {

            content:
                "";

            position:
                absolute;

            width:
                100px;

            height:
                100px;

            border-radius:
                50%;

            right:
                -45px;

            top:
                -45px;

            background:
                rgba(85, 216, 255, .15);

            animation:
                cardOrb 5s ease-in-out infinite;
        }


        .event-card::after {

            content:
                "";

            position:
                absolute;

            width:
                50px;

            height:
                50px;

            border-radius:
                12px;

            left:
                -25px;

            bottom:
                -25px;

            background:
                rgba(255, 216, 77, .18);

            transform:
                rotate(45deg);

            animation:
                cardDiamond 6s linear infinite;
        }


        .event-card:hover {

            transform:
                translateY(-10px) rotateX(2deg);

            box-shadow:
                var(--shadow-strong);
        }


        @keyframes cardOrb {

            0%,
            100% {
                transform:
                    translate(0, 0) scale(1);
            }

            50% {
                transform:
                    translate(-15px, 20px) scale(1.15);
            }
        }


        @keyframes cardDiamond {

            0%,
            100% {
                transform:
                    rotate(45deg) translate(0, 0);
            }

            50% {
                transform:
                    rotate(225deg) translate(10px, -10px);
            }
        }


        .event-icon {

            position:
                relative;

            z-index:
                3;

            width:
                70px;

            height:
                70px;

            margin:
                0 auto 18px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                22px;

            font-size:
                1.8rem;

            animation:
                eventIconFloat 3s ease-in-out infinite;
        }


        .event-icon.blue {

            background:
                #e3f5ff;

            color:
                var(--blue);
        }


        .event-icon.yellow {

            background:
                #fff5c7;

            color:
                #e39b00;
        }


        .event-icon.orange {

            background:
                #fff0df;

            color:
                #ed861d;
        }


        .event-icon.green {

            background:
                #e3fbed;

            color:
                #2eaf69;
        }


        @keyframes eventIconFloat {

            0%,
            100% {
                transform:
                    translateY(0) rotate(-2deg);
            }

            50% {
                transform:
                    translateY(-7px) rotate(3deg);
            }
        }


        .event-label {

            text-transform:
                uppercase;

            font-size:
                .75rem;

            letter-spacing:
                2px;

            font-weight:
                700;

            color:
                #8aa0b3;

            margin-bottom:
                6px;
        }


        .event-value {

            font-size:
                1.3rem;

            font-weight:
                700;

            color:
                var(--text);

            position:
                relative;

            z-index:
                3;
        }


        /* =====================================================
           EVENT 3D OBJECTS
           ===================================================== */

        .event-ring {

            width:
                110px;

            height:
                110px;

            right:
                4%;

            top:
                12%;

            opacity:
                .7;
        }


        .event-diamond {

            left:
                4%;

            bottom:
                10%;
        }


        .event-orb {

            right:
                14%;

            bottom:
                20%;

            width:
                45px;

            height:
                45px;
        }


        /* =====================================================
           FUN STRIP
           ===================================================== */

        .fun-strip {

            position:
                relative;

            padding:
                28px 0;

            background:
                linear-gradient(90deg,
                    #1689e8,
                    #55bfff,
                    #8b76ff,
                    #ff9f43,
                    #ffd84d,
                    #1689e8);

            color:
                white;

            overflow:
                hidden;

            box-shadow:
                0 10px 35px rgba(7, 94, 181, .15);
        }


        .fun-strip::before {

            content:
                "";

            position:
                absolute;

            inset:
                0;

            background:
                linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, .25),
                    transparent);

            transform:
                translateX(-100%);

            animation:
                stripShine 5s linear infinite;
        }


        @keyframes stripShine {

            to {
                transform:
                    translateX(100%);
            }
        }


        .fun-track {

            display:
                flex;

            gap:
                45px;

            width:
                max-content;

            animation:
                ticker 22s linear infinite;

            position:
                relative;

            z-index:
                2;
        }


        .fun-item {

            white-space:
                nowrap;

            font-weight:
                700;

            font-size:
                1.1rem;

            text-shadow:
                0 3px 8px rgba(0, 0, 0, .12);
        }


        @keyframes ticker {

            from {
                transform:
                    translateX(0);
            }

            to {
                transform:
                    translateX(-50%);
            }
        }


        /* =====================================================
           3D OBJECTS INSIDE STRIP
           ===================================================== */

        .strip-cube {

            position:
                absolute;

            width:
                35px;

            height:
                35px;

            right:
                8%;

            top:
                50%;

            transform-style:
                preserve-3d;

            animation:
                stripCube 6s linear infinite;

            z-index:
                3;
        }


        .strip-cube span {

            position:
                absolute;

            inset:
                0;

            border:
                2px solid rgba(255, 255, 255, .5);

            background:
                rgba(255, 255, 255, .15);
        }


        .strip-cube span:nth-child(1) {
            transform:
                translateZ(17.5px);
        }


        .strip-cube span:nth-child(2) {
            transform:
                rotateY(90deg) translateZ(17.5px);
        }


        .strip-cube span:nth-child(3) {
            transform:
                rotateX(90deg) translateZ(17.5px);
        }


        @keyframes stripCube {

            from {
                transform:
                    translateY(-50%) rotateX(0) rotateY(0) rotateZ(0);
            }

            to {
                transform:
                    translateY(-50%) rotateX(360deg) rotateY(360deg) rotateZ(180deg);
            }
        }


        /* =====================================================
           GODPARENTS
           ===================================================== */

        .godparents {

            background:
                linear-gradient(180deg,
                    #fff8e6,
                    #ffffff);
        }


        .godparent-box {

            position:
                relative;

            background:
                white;

            border-radius:
                30px;

            padding:
                35px 25px;

            box-shadow:
                var(--shadow);

            height:
                100%;

            overflow:
                hidden;

            border:
                2px solid rgba(22, 137, 232, .06);

            transition:
                transform .35s ease,
                box-shadow .35s ease;
        }


        .godparent-box:hover {

            transform:
                translateY(-8px) rotateX(2deg);

            box-shadow:
                var(--shadow-strong);
        }


        .godparent-box::before {

            content:
                "";

            position:
                absolute;

            width:
                150px;

            height:
                150px;

            border-radius:
                50%;

            background:
                rgba(255, 216, 77, .20);

            right:
                -55px;

            top:
                -55px;

            animation:
                godOrb 5s ease-in-out infinite;
        }


        .godparent-box::after {

            content:
                "";

            position:
                absolute;

            width:
                60px;

            height:
                60px;

            border:
                8px solid rgba(22, 137, 232, .08);

            border-radius:
                50%;

            left:
                -30px;

            bottom:
                15px;

            animation:
                ringSmall 7s linear infinite;
        }


        @keyframes godOrb {

            0%,
            100% {
                transform:
                    scale(1) translate(0, 0);
            }

            50% {
                transform:
                    scale(1.15) translate(-15px, 15px);
            }
        }


        @keyframes ringSmall {

            to {
                transform:
                    rotate(360deg) translateY(-20px);
            }
        }


        .godparent-heading {

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

            font-family:
                "Baloo 2",
                sans-serif;

            font-size:
                1.8rem;

            font-weight:
                800;

            margin-bottom:
                25px;

            color:
                var(--deep-blue);

            position:
                relative;

            z-index:
                4;
        }


        .godparent-heading span {

            width:
                48px;

            height:
                48px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                16px;

            background:
                #e7f7ff;

            color:
                var(--blue);

            box-shadow:
                0 8px 20px rgba(22, 137, 232, .10);

            animation:
                headingIcon 4s ease-in-out infinite;
        }


        @keyframes headingIcon {

            0%,
            100% {
                transform:
                    rotate(0deg) translateY(0);
            }

            50% {
                transform:
                    rotate(8deg) translateY(-5px);
            }
        }


        .godparent-list {

            list-style:
                none;

            padding:
                0;

            margin:
                0;

            position:
                relative;

            z-index:
                5;
        }


        .godparent-list li {

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

            padding:
                12px 0;

            border-bottom:
                1px dashed #dce8ef;

            font-weight:
                600;

            color:
                #49647a;

            transition:
                transform .25s ease,
                color .25s ease;
        }


        .godparent-list li:hover {

            transform:
                translateX(7px);

            color:
                var(--deep-blue);
        }


        .godparent-list li:last-child {

            border-bottom:
                0;
        }


        .godparent-list i {

            width:
                28px;

            height:
                28px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                50%;

            background:
                #fff2b9;

            color:
                #d18a00;

            font-size:
                .8rem;

            flex-shrink:
                0;
        }


        /* =====================================================
           GODPARENT 3D OBJECTS
           ===================================================== */

        .god-cube {

            right:
                4%;

            top:
                15%;

            transform:
                scale(.65);

            opacity:
                .65;
        }


        .god-ring {

            left:
                3%;

            bottom:
                10%;

            transform:
                scale(.75);
        }


        /* =====================================================
           CLOSING
           ===================================================== */

        .closing {

            min-height:
                75vh;

            display:
                flex;

            align-items:
                center;

            text-align:
                center;

            background:

                radial-gradient(circle at 50% 50%,
                    #ffffff,
                    transparent 35%),

                linear-gradient(135deg,
                    #c8f1ff,
                    #e8f1ff,
                    #fff1b5);

            position:
                relative;

            overflow:
                hidden;
        }


        .closing-title {

            font-family:
                "Baloo 2",
                sans-serif;

            font-size:
                clamp(3rem, 8vw, 6rem);

            font-weight:
                800;

            color:
                var(--deep-blue);

            line-height:
                .9;

            margin-bottom:
                20px;
        }


        .closing-text {

            max-width:
                600px;

            margin:
                0 auto 30px;

            color:
                #536f84;

            font-size:
                1.1rem;
        }


        .party-icon {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            width:
                70px;

            height:
                70px;

            border-radius:
                50%;

            background:
                white;

            box-shadow:
                0 15px 35px rgba(0, 80, 140, .12);

            font-size:
                1.8rem;

            animation:
                partyIconFloat 3s ease-in-out infinite;
        }


        .cake-icon {

            color:
                #ff9f43;
        }


        @keyframes partyIconFloat {

            0%,
            100% {
                transform:
                    translateY(0) rotate(-3deg);
            }

            50% {
                transform:
                    translateY(-10px) rotate(4deg);
            }
        }


        .celebrate-button {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                10px;

            border:
                0;

            padding:
                15px 30px;

            border-radius:
                50px;

            background:
                var(--deep-blue);

            color:
                white;

            font-weight:
                700;

            box-shadow:
                0 12px 30px rgba(7, 94, 181, .25);

            transition:
                transform .25s ease,
                box-shadow .25s ease;
        }


        .celebrate-button:hover {

            transform:
                translateY(-5px) scale(1.02);

            box-shadow:
                0 18px 40px rgba(7, 94, 181, .30);
        }


        /* =====================================================
           CLOSING 3D OBJECTS
           ===================================================== */

        .closing-ring {

            left:
                8%;

            top:
                20%;
        }


        .closing-diamond {

            right:
                10%;

            top:
                22%;
        }


        .closing-star {

            right:
                18%;

            bottom:
                18%;
        }


        .closing-bubble {

            left:
                15%;

            bottom:
                18%;
        }


        /* =====================================================
           FOOTER
           ===================================================== */

        footer {

            background:
                #075eb5;

            color:
                rgba(255, 255, 255, .9);

            padding:
                30px 0;

            text-align:
                center;

            font-size:
                .9rem;

            position:
                relative;

            overflow:
                hidden;
        }


        footer strong {

            color:
                white;
        }


        /* =====================================================
           CONFETTI
           ===================================================== */

        .confetti-piece {

            position:
                fixed;

            top:
                -20px;

            width:
                10px;

            height:
                16px;

            z-index:
                10000;

            pointer-events:
                none;

            animation:
                confettiFall linear forwards;
        }


        @keyframes confettiFall {

            0% {

                transform:
                    translate3d(0, -30px, 0) rotate(0deg);

                opacity:
                    1;
            }

            100% {

                transform:
                    translate3d(var(--drift, 0px),
                        110vh,
                        0) rotate(720deg);

                opacity:
                    0;
            }
        }


        /* =====================================================
           SPARKLES
           ===================================================== */

        .sparkle {

            position:
                fixed;

            width:
                8px;

            height:
                8px;

            border-radius:
                50%;

            background:
                white;

            box-shadow:
                0 0 12px white,
                0 0 25px #ffd84d;

            pointer-events:
                none;

            z-index:
                10001;

            animation:
                sparkleOut .7s ease-out forwards;
        }


        @keyframes sparkleOut {

            from {

                transform:
                    scale(.3);

                opacity:
                    1;
            }

            to {

                transform:
                    scale(3) translateY(-30px);

                opacity:
                    0;
            }
        }


        /* =====================================================
           REVEAL
           ===================================================== */

        .reveal {

            opacity:
                0;

            transform:
                translateY(30px);

            transition:
                opacity .8s ease,
                transform .8s ease;
        }


        .reveal.visible {

            opacity:
                1;

            transform:
                translateY(0);
        }


        /* =====================================================
           MOBILE
           ===================================================== */

        @media (max-width: 991px) {

            .svg-hero {

                right:
                    -25px;

                bottom:
                    0;

                opacity:
                    .85;
            }


            .svg-left {

                left:
                    -50px;

                opacity:
                    .7;
            }


            .svg-lecture {

                right:
                    -45px;

                opacity:
                    .7;
            }


            .svg-crawl {

                left:
                    -25px;

                opacity:
                    .75;
            }


            .glass-ring {

                transform:
                    scale(.75);
            }


            .hero-ring {

                left:
                    2%;
            }


            .hero-diamond {

                right:
                    5%;
            }
        }


        @media (max-width: 767px) {

            .hero {

                padding-top:
                    80px;
            }


            .svg-hero {

                width:
                    135px;

                right:
                    -30px;

                bottom:
                    30px;

                opacity:
                    .7;
            }


            .svg-left {

                width:
                    105px;

                left:
                    -35px;

                top:
                    42%;

                opacity:
                    .55;
            }


            .svg-crawl {

                width:
                    130px;

                left:
                    -20px;

                bottom:
                    15px;

                opacity:
                    .65;
            }


            .svg-lecture {

                width:
                    120px;

                right:
                    -35px;

                bottom:
                    10px;

                opacity:
                    .6;
            }


            .svg-cute {

                width:
                    100px;

                left:
                    -20px;

                top:
                    3%;

                opacity:
                    .65;
            }


            .balloon {

                width:
                    42px;

                height:
                    52px;
            }


            .balloon-1 {

                left:
                    -5px;
            }


            .balloon-2 {

                right:
                    -5px;
            }


            .baby-photo-wrap {

                margin-top:
                    30px;
            }


            .section {

                padding:
                    75px 0;
            }


            .godparent-box {

                padding:
                    28px 20px;
            }


            .float-3d {

                opacity:
                    .55;

                transform:
                    scale(.7);
            }


            .intro-cube-1 {

                left:
                    -5px;
            }


            .intro-cube-2 {

                right:
                    -5px;
            }


            .rainbow {

                width:
                    180px;

                height:
                    90px;
            }


            .intro-cloud-1 {

                left:
                    -15px;
            }


            .intro-cloud-2 {

                right:
                    -25px;
            }
        }


        @media (max-width: 480px) {

            .intro-title {

                font-size:
                    2.7rem;
            }


            .envelope-scene {

                height:
                    240px;
            }


            .envelope-card {

                height:
                    170px;
            }


            .envelope.opened .envelope-card {

                transform:
                    translateY(-125px);
            }


            .card-name {

                font-size:
                    1.7rem;
            }


            .hero-title {

                font-size:
                    4rem;
            }


            .photo-badge {

                width:
                    60px;

                height:
                    60px;

                font-size:
                    .7rem;
            }


            .mini-star-3d {

                font-size:
                    30px;
            }


            .bubble-3d {

                width:
                    45px;

                height:
                    45px;
            }
        }
    </style>

</head>


<body>


    <!-- =====================================================
         INTRO SCREEN
         ===================================================== -->

    <div id="intro">


        <!-- =================================================
             INTRO DECORATIONS
             ================================================= -->

        <div class="intro-decor">


            <!-- Rainbow -->

            <div class="rainbow"></div>


            <!-- Clouds -->

            <div class="intro-cloud intro-cloud-1">

                <i class="fa-solid fa-cloud"></i>

            </div>


            <div class="intro-cloud intro-cloud-2">

                <i class="fa-solid fa-cloud"></i>

            </div>


            <div class="intro-cloud intro-cloud-3">

                <i class="fa-solid fa-cloud"></i>

            </div>


            <!-- Stars -->

            <i
                class="fa-solid fa-star intro-3d"
                style="
                    left:8%;
                    top:15%;
                    color:#ffd84d;
                    font-size:32px;
                    animation:star3d 4s ease-in-out infinite;
                "></i>


            <i
                class="fa-solid fa-star intro-3d"
                style="
                    right:10%;
                    top:13%;
                    color:#ffffff;
                    font-size:42px;
                    animation:star3d 5s ease-in-out infinite;
                "></i>


            <i
                class="fa-solid fa-sparkles intro-3d"
                style="
                    left:18%;
                    bottom:16%;
                    color:#ffffff;
                    font-size:28px;
                    animation:star3d 4.5s ease-in-out infinite;
                "></i>


            <!-- 3D Cubes -->

            <div class="intro-3d intro-cube-1">

                <div class="cube-3d">

                    <div class="face cube-front"></div>

                    <div class="face cube-back"></div>

                    <div class="face cube-right"></div>

                    <div class="face cube-left"></div>

                    <div class="face cube-top"></div>

                    <div class="face cube-bottom"></div>

                </div>

            </div>


            <div class="intro-3d intro-cube-2">

                <div class="cube-3d">

                    <div class="face cube-front"></div>

                    <div class="face cube-back"></div>

                    <div class="face cube-right"></div>

                    <div class="face cube-left"></div>

                    <div class="face cube-top"></div>

                    <div class="face cube-bottom"></div>

                </div>

            </div>


            <!-- 3D Orbs -->

            <div class="intro-3d orb-3d orb-1"></div>

            <div class="intro-3d orb-3d orb-2"></div>


            <!-- Balloons -->

            <div class="balloon balloon-blue balloon-1"></div>

            <div class="balloon balloon-yellow balloon-2"></div>

            <div class="balloon balloon-green balloon-3"></div>

            <div class="balloon balloon-purple balloon-4"></div>


        </div>


        <!-- =================================================
             INTRO CONTENT
             ================================================= -->

        <div class="intro-content">


            <div class="intro-small">

                A little celebration is waiting

            </div>


            <h1 class="intro-title">

                You're Invited!

            </h1>


            <p class="intro-subtitle">

                Open the envelope and join us for
                Kairo's very big little day!

            </p>


            <!-- =================================================
                 ENVELOPE
                 The envelope itself is clickable.
                 ================================================= -->

            <div class="envelope-scene">


                <div
                    class="envelope"
                    id="envelope"
                    role="button"
                    tabindex="0"
                    aria-label="Open invitation">


                    <div class="envelope-card">

                        <div>

                            <div class="card-mini-title">

                                Save the date

                            </div>


                            <div class="card-name">

                                Kairo

                            </div>


                            <div class="card-text">

                                Christening & Birthday
                                Celebration

                            </div>

                        </div>

                    </div>


                    <div class="envelope-body">

                        <div class="envelope-left"></div>

                        <div class="envelope-right"></div>

                    </div>


                    <div class="envelope-flap"></div>


                    <div class="seal">

                        <i class="fi fi-sr-star"></i>

                    </div>


                </div>


                <div class="envelope-hint">

                    <i class="fa-solid fa-hand-pointer"></i>

                    Tap the envelope to open

                </div>


            </div>


        </div>

    </div>


    <!-- =====================================================
         MAIN PAGE
         ===================================================== -->

    <div id="page">


        <!-- =================================================
             HERO
             ================================================= -->

        <section class="hero">


            <!-- Section-relative 3D decorations -->

            <div class="section-decor">


                <div
                    class="float-3d glass-ring hero-ring">
                </div>


                <div
                    class="float-3d diamond-3d hero-diamond">
                </div>


                <div
                    class="float-3d mini-star-3d hero-star">

                    ★

                </div>


                <div
                    class="float-3d bubble-3d hero-bubble">
                </div>


                <!-- Small floating icon -->

                <i
                    class="fa-solid fa-cloud float-3d"
                    style="
                        right:6%;
                        top:8%;
                        color:white;
                        opacity:.75;
                        font-size:70px;
                        animation:cloudFloat 9s ease-in-out infinite;
                    ">
                </i>


                <i
                    class="fa-solid fa-star float-3d"
                    style="
                        left:4%;
                        top:11%;
                        color:#ffd84d;
                        font-size:34px;
                        animation:star3d 4s ease-in-out infinite;
                    ">
                </i>

            </div>


            <!-- Theme SVG: integrated decoration -->

            <div class="theme-svg svg-cute">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['cute']); ?>"
                    alt=""
                    aria-hidden="true"
                    onerror="this.style.display='none';">

            </div>


            <div class="theme-svg svg-hero">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['poster']); ?>"
                    alt=""
                    aria-hidden="true"
                    onerror="this.style.display='none';">

            </div>


            <div class="theme-svg svg-left">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['hands']); ?>"
                    alt=""
                    aria-hidden="true"
                    onerror="this.style.display='none';">

            </div>


            <div class="theme-svg svg-crawl">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['crawl']); ?>"
                    alt=""
                    aria-hidden="true"
                    onerror="this.style.display='none';">

            </div>


            <div class="container content-layer">

                <div class="hero-content">


                    <div class="little-boss">

                        <i class="fi fi-sr-baby"></i>

                        A VERY SPECIAL LITTLE GUY

                    </div>


                    <h1 class="hero-title">

                        Meet

                        <span class="blue-word">

                            Kairo!

                        </span>

                    </h1>


                    <p class="hero-subtitle">

                        A tiny bundle of joy is turning
                        another year older and we're
                        celebrating in a BIG way!

                    </p>


                    <!-- =================================================
                         BABY PHOTO
                         ================================================= -->

                    <div class="baby-photo-wrap">


                        <div class="baby-photo-bg"></div>


                        <img
                            src="<?php echo htmlspecialchars($babyPhoto); ?>"
                            alt="<?php echo htmlspecialchars($babyName); ?>"
                            class="baby-photo">


                        <div class="photo-badge">

                            THE<br>
                            LITTLE<br>
                            BOSS

                        </div>


                    </div>


                    <h2
                        class="fw-bold"
                        style="
                            color:#075eb5;
                            font-family:'Baloo 2',sans-serif;
                            font-size:clamp(2rem,5vw,3rem);
                        ">

                        <?php echo htmlspecialchars($babyName); ?>

                    </h2>


                    <p class="text-muted mb-0">

                        invites you to his

                    </p>


                </div>

            </div>

        </section>


        <!-- =================================================
             EVENT SECTION
             ================================================= -->

        <section class="section event-section">


            <!-- 3D decoration -->

            <div class="section-decor">


                <div
                    class="float-3d glass-ring event-ring">
                </div>


                <div
                    class="float-3d diamond-3d event-diamond">
                </div>


                <div
                    class="float-3d bubble-3d event-orb">
                </div>


                <i
                    class="fa-solid fa-gift float-3d"
                    style="
                        left:8%;
                        top:15%;
                        color:#ff9f43;
                        font-size:38px;
                        animation:iconBounce 3s ease-in-out infinite;
                    ">
                </i>


                <i
                    class="fa-solid fa-cake-candles float-3d"
                    style="
                        right:6%;
                        bottom:15%;
                        color:#1689e8;
                        font-size:42px;
                        animation:iconBounce 4s ease-in-out infinite;
                    ">
                </i>

            </div>


            <div class="container content-layer">


                <div
                    class="text-center reveal">


                    <div
                        class="little-boss"
                        style="margin-bottom:15px;">

                        <i class="fi fi-sr-party-horn"></i>

                        LET'S CELEBRATE

                    </div>


                    <h2 class="section-title">

                        <?php echo htmlspecialchars($eventTitle); ?>

                    </h2>


                    <p class="section-lead">

                        One special day, two wonderful reasons
                        to celebrate, and lots of smiles,
                        laughter, food, and fun!

                    </p>

                </div>


                <!-- =================================================
                     EVENT CARDS
                     ================================================= -->

                <div class="row g-4">


                    <!-- DATE -->

                    <div class="col-12 col-md-6 col-lg-3 reveal">

                        <div class="event-card">

                            <div class="event-icon blue">

                                <i class="fi fi-sr-calendar-day"></i>

                            </div>

                            <div class="event-label">

                                Date

                            </div>

                            <div class="event-value">

                                <?php echo htmlspecialchars($eventDate); ?>

                            </div>

                        </div>

                    </div>


                    <!-- TIME -->

                    <div class="col-12 col-md-6 col-lg-3 reveal">

                        <div class="event-card">

                            <div class="event-icon yellow">

                                <i class="fi fi-sr-clock"></i>

                            </div>

                            <div class="event-label">

                                Time

                            </div>

                            <div class="event-value">

                                <?php echo htmlspecialchars($eventTime); ?>

                            </div>

                        </div>

                    </div>


                    <!-- VENUE -->

                    <div class="col-12 col-md-6 col-lg-3 reveal">

                        <div class="event-card">

                            <div class="event-icon orange">

                                <i class="fi fi-sr-marker"></i>

                            </div>

                            <div class="event-label">

                                Venue

                            </div>

                            <div class="event-value">

                                <?php echo htmlspecialchars($venue); ?>

                            </div>

                        </div>

                    </div>


                    <!-- CELEBRATION -->

                    <div class="col-12 col-md-6 col-lg-3 reveal">

                        <div class="event-card">

                            <div class="event-icon green">

                                <i class="fi fi-sr-gift"></i>

                            </div>

                            <div class="event-label">

                                Celebration

                            </div>

                            <div class="event-value">

                                Come & Have Fun!

                            </div>

                        </div>

                    </div>


                </div>

            </div>


            <!-- Integrated lecture character -->

            <div class="theme-svg svg-lecture">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['lecture']); ?>"
                    alt=""
                    aria-hidden="true"
                    onerror="this.style.display='none';">

            </div>


        </section>


        <!-- =================================================
             FUN MOVING STRIP
             ================================================= -->

        <div class="fun-strip">


            <div class="strip-cube">

                <span></span>
                <span></span>
                <span></span>

            </div>


            <div class="fun-track">


                <div class="fun-item">

                    ⭐ SMILES

                </div>


                <div class="fun-item">

                    🎈 BALLOONS

                </div>


                <div class="fun-item">

                    🎂 CAKE

                </div>


                <div class="fun-item">

                    🎁 GIFTS

                </div>


                <div class="fun-item">

                    🍼 BABY KAIRO

                </div>


                <div class="fun-item">

                    🎉 CELEBRATION

                </div>


                <div class="fun-item">

                    ⭐ SMILES

                </div>


                <div class="fun-item">

                    🎈 BALLOONS

                </div>


                <div class="fun-item">

                    🎂 CAKE

                </div>


                <div class="fun-item">

                    🎁 GIFTS

                </div>


                <div class="fun-item">

                    🍼 BABY KAIRO

                </div>


                <div class="fun-item">

                    🎉 CELEBRATION

                </div>


            </div>

        </div>


        <!-- =================================================
             GODPARENTS
             ================================================= -->

        <section class="section godparents">


            <!-- Section 3D decorations -->

            <div class="section-decor">


                <div
                    class="float-3d god-cube">

                    <div class="cube-3d">

                        <div class="face cube-front"></div>

                        <div class="face cube-back"></div>

                        <div class="face cube-right"></div>

                        <div class="face cube-left"></div>

                        <div class="face cube-top"></div>

                        <div class="face cube-bottom"></div>

                    </div>

                </div>


                <div
                    class="float-3d glass-ring god-ring">
                </div>


                <i
                    class="fa-solid fa-crown float-3d"
                    style="
                        right:10%;
                        bottom:15%;
                        color:#ffd84d;
                        font-size:38px;
                        animation:star3d 5s ease-in-out infinite;
                    ">
                </i>


                <i
                    class="fa-solid fa-star float-3d"
                    style="
                        left:8%;
                        top:10%;
                        color:#1689e8;
                        font-size:32px;
                        animation:star3d 4s ease-in-out infinite;
                    ">
                </i>

            </div>


            <!-- Crawling decoration -->

            <div class="theme-svg svg-crawl">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['crawl']); ?>"
                    alt=""
                    aria-hidden="true"
                    onerror="this.style.display='none';">

            </div>


            <div class="container content-layer">


                <div class="text-center reveal">


                    <div
                        class="little-boss"
                        style="margin-bottom:15px;">

                        <i class="fi fi-sr-users-alt"></i>

                        SPECIAL PEOPLE

                    </div>


                    <h2 class="section-title">

                        Our Wonderful Godparents

                    </h2>


                    <p class="section-lead">

                        Thank you to the special people
                        who will guide, support, and shower
                        little Kairo with love.

                    </p>

                </div>


                <div class="row g-4 justify-content-center">


                    <!-- =================================================
                         GODFATHERS
                         ================================================= -->

                    <div class="col-12 col-lg-6 reveal">

                        <div class="godparent-box">

                            <h3 class="godparent-heading">

                                <span>

                                    <i class="fi fi-sr-man-head"></i>

                                </span>

                                Godfathers

                            </h3>


                            <ul class="godparent-list">

                                <?php foreach ($godfathers as $godfather): ?>

                                    <li>

                                        <i class="fi fi-sr-star"></i>

                                        <?php
                                        echo htmlspecialchars($godfather);
                                        ?>

                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </div>

                    </div>


                    <!-- =================================================
                         GODMOTHERS
                         ================================================= -->

                    <div class="col-12 col-lg-6 reveal">

                        <div class="godparent-box">

                            <h3 class="godparent-heading">

                                <span>

                                    <i class="fi fi-sr-woman-head"></i>

                                </span>

                                Godmothers

                            </h3>


                            <ul class="godparent-list">

                                <?php foreach ($godmothers as $godmother): ?>

                                    <li>

                                        <i class="fi fi-sr-heart"></i>

                                        <?php
                                        echo htmlspecialchars($godmother);
                                        ?>

                                    </li>

                                <?php endforeach; ?>

                            </ul>

                        </div>

                    </div>


                </div>

            </div>

        </section>


        <!-- =================================================
             CLOSING
             ================================================= -->

        <section class="section closing">


            <!-- =================================================
                 CLOSING 3D DECORATIONS
                 ================================================= -->

            <div class="section-decor">


                <div
                    class="float-3d glass-ring closing-ring">
                </div>


                <div
                    class="float-3d diamond-3d closing-diamond">
                </div>


                <div
                    class="float-3d mini-star-3d closing-star">

                    ★

                </div>


                <div
                    class="float-3d bubble-3d closing-bubble">
                </div>


                <i
                    class="fa-solid fa-gift float-3d"
                    style="
                        left:7%;
                        top:18%;
                        color:#ff9f43;
                        font-size:40px;
                        animation:iconBounce 4s ease-in-out infinite;
                    ">
                </i>


                <i
                    class="fa-solid fa-balloon float-3d"
                    style="
                        right:7%;
                        top:12%;
                        color:#1689e8;
                        font-size:42px;
                        animation:iconBounce 3.5s ease-in-out infinite;
                    ">
                </i>


                <i
                    class="fa-solid fa-sparkles float-3d"
                    style="
                        left:18%;
                        bottom:15%;
                        color:#ffd84d;
                        font-size:35px;
                        animation:star3d 4s ease-in-out infinite;
                    ">
                </i>

            </div>


            <!-- Theme decoration -->

            <div class="theme-svg svg-hero">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['cute']); ?>"
                    alt=""
                    aria-hidden="true"
                    onerror="this.style.display='none';">

            </div>


            <div class="theme-svg svg-left">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['hands']); ?>"
                    alt=""
                    aria-hidden="true"
                    onerror="this.style.display='none';">

            </div>


            <div class="container content-layer">

                <div class="reveal">


                    <div class="party-icon mx-auto mb-4 cake-icon">

                        <i class="fi fi-sr-cake-birthday"></i>

                    </div>


                    <h2 class="closing-title">

                        See You There!

                    </h2>


                    <p class="closing-text">

                        Come celebrate the christening and
                        birthday of our little Kairo.
                        Your presence will make his special
                        day even more memorable!

                    </p>


                    <button
                        type="button"
                        class="celebrate-button"
                        id="celebrateButton">

                        <i class="fi fi-sr-party-horn"></i>

                        Let's Celebrate!

                    </button>


                    <div
                        class="mt-4 fw-bold"
                        style="color:#075eb5;">

                        <?php echo htmlspecialchars($babyName); ?>

                        <br>

                        <small
                            style="
                                color:#6c8295;
                                font-weight:500;
                            ">

                            Our little bundle of joy

                        </small>

                    </div>


                </div>

            </div>

        </section>


        <!-- =================================================
             FOOTER
             ================================================= -->

        <footer>

            <div class="container">

                Made with love for

                <strong>

                    <?php echo htmlspecialchars($babyName); ?>

                </strong>

                <br>

                <span
                    style="
                        opacity:.75;
                        font-size:.8rem;
                    ">

                    Christening & Birthday Celebration

                </span>

            </div>

        </footer>


    </div>


    <!-- =====================================================
         BOOTSTRAP JS
         ===================================================== -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    <script>
        /* =====================================================
           ELEMENTS
           ===================================================== */

        const intro =
            document.getElementById("intro");

        const page =
            document.getElementById("page");

        const envelope =
            document.getElementById("envelope");

        const celebrateButton =
            document.getElementById("celebrateButton");


        /* =====================================================
           OPEN INVITATION
           The envelope itself is clickable.
           ===================================================== */

        let invitationOpened = false;


        function openInvitation() {

            if (invitationOpened) {
                return;
            }


            invitationOpened = true;


            envelope.classList.add("opened");


            /* ---------------------------------------------
               Immediate sparkle effect around envelope
               --------------------------------------------- */

            createSparkles(
                window.innerWidth / 2,
                window.innerHeight / 2
            );


            /* ---------------------------------------------
               Open envelope first
               --------------------------------------------- */

            setTimeout(
                function() {

                    intro.classList.add("hidden");

                    page.classList.add("show");

                    document.body.style.overflowX =
                        "hidden";

                    launchConfetti(100);

                },
                1150
            );

        }


        envelope.addEventListener(
            "click",
            openInvitation
        );


        envelope.addEventListener(
            "keydown",
            function(event) {

                if (
                    event.key === "Enter" ||
                    event.key === " "
                ) {

                    event.preventDefault();

                    openInvitation();

                }

            }
        );


        /* =====================================================
           CONFETTI
           ===================================================== */

        function launchConfetti(amount = 60) {


            const pieces = [

                "⭐",
                "✦",
                "●",
                "■",
                "◆"

            ];


            const confettiSymbols = [

                "⭐",
                "✦",
                "●",
                "■",
                "◆",
                "♦"

            ];


            for (
                let i = 0; i < amount; i++
            ) {


                const piece =
                    document.createElement("div");


                piece.className =
                    "confetti-piece";


                const randomX =
                    Math.random() * 100;


                const randomDelay =
                    Math.random() * 1.2;


                const randomDuration =
                    2.5 +
                    Math.random() * 3;


                const randomDrift =
                    (Math.random() * 240) - 120;


                piece.style.left =
                    randomX + "vw";


                piece.style.animationDuration =
                    randomDuration + "s";


                piece.style.animationDelay =
                    randomDelay + "s";


                piece.style.setProperty(
                    "--drift",
                    randomDrift + "px"
                );


                const size =
                    6 +
                    Math.random() * 9;


                piece.style.width =
                    size + "px";


                piece.style.height =
                    size * 1.5 + "px";


                piece.innerHTML =
                    confettiSymbols[
                        Math.floor(
                            Math.random() *
                            confettiSymbols.length
                        )
                    ];


                document.body.appendChild(
                    piece
                );


                setTimeout(
                    function() {

                        piece.remove();

                    },
                    (
                        randomDuration +
                        randomDelay
                    ) * 1000 + 800
                );

            }

        }


        /* =====================================================
           PERIODIC PARTY CONFETTI
           ===================================================== */

        setInterval(
            function() {

                if (
                    page.classList.contains("show")
                ) {

                    launchConfetti(12);

                }

            },
            18000
        );


        /* =====================================================
           CELEBRATE BUTTON
           ===================================================== */

        if (celebrateButton) {


            celebrateButton.addEventListener(
                "click",
                function() {


                    launchConfetti(120);


                    createSparkles(
                        window.innerWidth / 2,
                        window.innerHeight / 2
                    );

                }
            );

        }


        /* =====================================================
           SPARKLE EFFECT
           ===================================================== */

        document.addEventListener(
            "click",
            function(event) {


                if (
                    event.target.closest(
                        "button"
                    )
                ) {

                    createSparkles(
                        event.clientX,
                        event.clientY
                    );

                }

            }
        );


        function createSparkles(x, y) {


            for (
                let i = 0; i < 8; i++
            ) {


                const sparkle =
                    document.createElement("div");


                sparkle.className =
                    "sparkle";


                sparkle.style.left =
                    (
                        x +
                        (
                            Math.random() * 50 -
                            25
                        )
                    ) + "px";


                sparkle.style.top =
                    (
                        y +
                        (
                            Math.random() * 50 -
                            25
                        )
                    ) + "px";


                sparkle.style.animationDelay =
                    (
                        Math.random() * .2
                    ) + "s";


                document.body.appendChild(
                    sparkle
                );


                setTimeout(
                    function() {

                        sparkle.remove();

                    },
                    1000
                );

            }

        }


        /* =====================================================
           SCROLL REVEAL
           ===================================================== */

        const revealElements =
            document.querySelectorAll(
                ".reveal"
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
                                    "visible"
                                );


                                revealObserver.unobserve(
                                    entry.target
                                );

                            }

                        }
                    );


                }, {
                    threshold: .12
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
           AUTOMATIC FIRST CONFETTI
           ===================================================== */

        setTimeout(
            function() {


                if (
                    !intro.classList.contains("hidden")
                ) {

                    return;

                }


                launchConfetti(30);


            },
            2500
        );


        /* =====================================================
           AUTOMATIC 3D AMBIENT MOTION
           
           This deliberately does NOT use mouse movement.
           The objects continuously animate through CSS.
           ===================================================== */

        document
            .querySelectorAll(
                ".float-3d, .theme-svg, .intro-3d"
            )
            .forEach(
                function(element) {

                    element.style.animationPlayState =
                        "running";

                }
            );


        /* =====================================================
           GENTLE 3D CARD TILT
           
           This is automatic rather than mouse controlled.
           Cards periodically receive a tiny movement through
           CSS hover/animation combinations.
           ===================================================== */

        const cards =
            document.querySelectorAll(
                ".event-card, .godparent-box"
            );


        cards.forEach(
            function(card, index) {

                card.style.animationDelay =
                    (index * .25) + "s";

            }
        );
    </script>


</body>

</html>