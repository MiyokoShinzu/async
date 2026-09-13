<?php

/* =========================================================
   KAIRO GADDIEL EVAlLO
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
   Transparent SVG artwork supplied by the user
   ========================================================= */

$themeSvgs = [

    "poster" =>
    "https://rawsvg.com/svg/boss-baby-fixes-his-watch-b04zu29rmojud07m.html?embed=true",

    "cute" =>
    "https://rawsvg.com/svg/boss-baby-walking-8gkh6lji9jq8des3.html?embed=true",

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
         GOOGLE FONT
         ===================================================== -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

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

            --yellow: #ffd84d;
            --orange: #ff9f43;

            --pink: #ff75a8;
            --purple: #9b7cff;

            --green: #55d88a;

            --white: #ffffff;

            --text: #18324a;

            --soft-blue: #eef9ff;

            --shadow:
                0 20px 50px rgba(28, 103, 166, .16);
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


        /* =====================================================
           SELECTION
           ===================================================== */

        ::selection {
            background: var(--yellow);
            color: var(--deep-blue);
        }


        /* =====================================================
           COMMON
           ===================================================== */

        .rounded-party {
            border-radius: 28px;
        }


        /* =====================================================
           ENVELOPE INTRO
           ===================================================== */

        #intro {

            position: fixed;

            inset: 0;

            z-index: 9999;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;

            background:

                radial-gradient(circle at 20% 20%,
                    rgba(255, 255, 255, .8),
                    transparent 25%),

                radial-gradient(circle at 80% 20%,
                    rgba(255, 216, 77, .35),
                    transparent 25%),

                linear-gradient(135deg,
                    #58caff,
                    #9ce7ff 45%,
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

            max-width: 600px;

            text-align: center;

            position: relative;

            z-index: 20;
        }


        .intro-small {

            font-size: .95rem;

            font-weight: 700;

            color: var(--deep-blue);

            letter-spacing: 3px;

            text-transform: uppercase;

            margin-bottom: 15px;
        }


        .intro-title {

            font-family: "Baloo 2", sans-serif;

            font-size:
                clamp(2.4rem, 8vw, 5rem);

            font-weight: 800;

            line-height: .95;

            color: white;

            text-shadow:
                0 5px 0 rgba(0, 80, 150, .15),
                0 12px 25px rgba(0, 0, 0, .12);

            margin-bottom: 10px;
        }


        .intro-subtitle {

            color: white;

            font-size: 1.05rem;

            margin-bottom: 35px;
        }


        /* =====================================================
           3D ENVELOPE
           ===================================================== */

        .envelope-scene {

            position: relative;

            width: min(400px, 85vw);

            height: 270px;

            margin: 20px auto 35px;

            perspective: 1200px;
        }


        .envelope {

            position: absolute;

            width: 100%;

            height: 220px;

            left: 0;

            bottom: 0;

            transform-style: preserve-3d;

            cursor: pointer;
        }


        .envelope-body {

            position: absolute;

            inset: 0;

            background:
                linear-gradient(145deg,
                    #ffffff,
                    #d9f3ff);

            border-radius: 14px 14px 18px 18px;

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

            transform: translateY(40px);
        }


        .envelope-left {

            position: absolute;

            inset: auto auto 0 0;

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

            transform-origin: top center;

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

            font-family: "Baloo 2", sans-serif;

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


        .open-button {

            border: 0;

            background: white;

            color: var(--deep-blue);

            padding:
                13px 28px;

            border-radius: 50px;

            font-weight: 700;

            font-size: 1rem;

            box-shadow:
                0 12px 30px rgba(0, 70, 130, .18);

            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }


        .open-button:hover {

            transform:
                translateY(-4px);

            box-shadow:
                0 18px 35px rgba(0, 70, 130, .22);
        }


        /* =====================================================
           DECORATION LAYER
           ===================================================== */

        .decor-layer {

            position: fixed;

            inset: 0;

            pointer-events: none;

            z-index: 2;

            overflow: hidden;
        }


        .decor {

            position: absolute;

            pointer-events: none;

            user-select: none;
        }


        /* =====================================================
           STARS
           ===================================================== */

        .star {

            color: #ffd438;

            font-size: 30px;

            animation:
                starTwinkle 2.8s ease-in-out infinite;
        }


        .star:nth-child(1) {
            left: 5%;
            top: 14%;
            animation-delay: .2s;
        }

        .star:nth-child(2) {
            left: 18%;
            top: 48%;
            font-size: 18px;
            animation-delay: 1s;
        }

        .star:nth-child(3) {
            right: 7%;
            top: 17%;
            font-size: 40px;
            animation-delay: .7s;
        }

        .star:nth-child(4) {
            right: 16%;
            top: 52%;
            font-size: 20px;
            animation-delay: 1.6s;
        }

        .star:nth-child(5) {
            left: 45%;
            top: 7%;
            font-size: 20px;
            animation-delay: 2s;
        }


        @keyframes starTwinkle {

            0%,
            100% {
                transform:
                    scale(.75) rotate(0deg);

                opacity: .45;
            }

            50% {
                transform:
                    scale(1.3) rotate(20deg);

                opacity: 1;
            }
        }


        /* =====================================================
           CLOUDS
           ===================================================== */

        .cloud {

            color: white;

            font-size: 65px;

            opacity: .85;

            animation:
                cloudDrift 22s linear infinite;
        }


        .cloud-1 {

            top: 8%;

            left: -100px;
        }


        .cloud-2 {

            top: 25%;

            right: -120px;

            font-size: 85px;

            animation-duration: 28s;

            animation-delay: -9s;
        }


        .cloud-3 {

            top: 65%;

            left: -130px;

            font-size: 75px;

            animation-duration: 31s;

            animation-delay: -14s;
        }


        @keyframes cloudDrift {

            from {
                transform:
                    translateX(0);
            }

            to {
                transform:
                    translateX(calc(100vw + 250px));
            }
        }


        /* =====================================================
           BALLOONS
           ===================================================== */

        .balloon {

            width: 58px;

            height: 70px;

            border-radius:
                50% 50% 45% 45%;

            position: absolute;

            box-shadow:
                inset -9px -10px 0 rgba(0, 0, 0, .07),
                0 10px 20px rgba(0, 0, 0, .08);

            animation:
                balloonFloat 5s ease-in-out infinite;
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
            background: #42b9ff;
            color: #42b9ff;
        }

        .balloon-yellow {
            background: #ffd83d;
            color: #ffd83d;
        }

        .balloon-pink {
            background: #ff77aa;
            color: #ff77aa;
        }

        .balloon-purple {
            background: #9a7cff;
            color: #9a7cff;
        }


        .balloon-1 {

            left: 3%;

            top: 22%;

            animation-delay: -.8s;
        }

        .balloon-2 {

            right: 3%;

            top: 34%;

            animation-delay: -2s;
        }

        .balloon-3 {

            left: 9%;

            top: 70%;

            transform: scale(.75);

            animation-delay: -3s;
        }

        .balloon-4 {

            right: 11%;

            top: 73%;

            transform: scale(.8);

            animation-delay: -1.2s;
        }


        @keyframes balloonFloat {

            0%,
            100% {
                transform:
                    translateY(0) rotate(-5deg);
            }

            50% {
                transform:
                    translateY(-24px) rotate(5deg);
            }
        }


        /* =====================================================
           PARTY ICONS
           ===================================================== */

        .party-icon {

            display: flex;

            align-items: center;

            justify-content: center;

            width: 45px;

            height: 45px;

            border-radius: 50%;

            background: white;

            box-shadow:
                0 8px 20px rgba(0, 0, 0, .08);

            font-size: 1.35rem;

            animation:
                iconBounce 3s ease-in-out infinite;
        }


        .gift-icon {
            color: #ff6b9d;
        }

        .baby-icon {
            color: #4ba9ed;
        }

        .cake-icon {
            color: #ff9d35;
        }

        .heart-icon {
            color: #ff6b91;
        }


        @keyframes iconBounce {

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
           HERO
           ===================================================== */

        .hero {

            min-height: 100vh;

            display: flex;

            align-items: center;

            position: relative;

            padding:
                100px 0 80px;

            overflow: hidden;

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

            position: relative;

            z-index: 10;

            text-align: center;
        }


        .little-boss {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            background: white;

            padding:
                8px 18px;

            border-radius: 50px;

            color: var(--deep-blue);

            font-weight: 700;

            font-size: .85rem;

            box-shadow:
                0 8px 25px rgba(0, 90, 150, .10);

            margin-bottom: 18px;

            animation:
                badgeFloat 3s ease-in-out infinite;
        }


        @keyframes badgeFloat {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
            }
        }


        .hero-title {

            font-family:
                "Baloo 2",
                sans-serif;

            font-size:
                clamp(3.5rem, 11vw, 8rem);

            font-weight: 800;

            line-height: .82;

            color: var(--deep-blue);

            margin: 0;

            text-shadow:
                0 5px 0 rgba(255, 255, 255, .8);
        }


        .hero-title .pink {
            color: var(--pink);
        }


        .hero-subtitle {

            font-size:
                clamp(1.2rem, 3vw, 1.8rem);

            font-weight: 600;

            color: #45677f;

            margin:
                20px auto 35px;

            max-width: 700px;
        }


        /* =====================================================
           BABY PHOTO
           ===================================================== */

        .baby-photo-wrap {

            position: relative;

            width:
                min(350px, 72vw);

            height:
                min(350px, 72vw);

            margin:
                20px auto 35px;
        }


        .baby-photo-bg {

            position: absolute;

            inset: 0;

            border-radius: 50%;

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

            content: "";

            position: absolute;

            inset: 13px;

            border:
                7px dashed rgba(22, 137, 232, .25);

            border-radius: 50%;

            animation:
                rotateRing 18s linear infinite;
        }


        .baby-photo {

            position: absolute;

            inset: 23px;

            width: calc(100% - 46px);

            height: calc(100% - 46px);

            object-fit: cover;

            border-radius: 50%;

            border:
                8px solid white;

            box-shadow:
                0 15px 40px rgba(0, 0, 0, .15);
        }


        .photo-badge {

            position: absolute;

            right: -10px;

            bottom: 20px;

            background:
                var(--yellow);

            color:
                #7b5700;

            width: 75px;

            height: 75px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            text-align: center;

            font-weight: 800;

            font-size: .8rem;

            line-height: 1;

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
                transform: scale(1);
            }

            50% {
                transform: scale(1.025);
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

            position: absolute;

            z-index: 3;

            pointer-events: none;

            user-select: none;

            background: transparent;

            filter:
                drop-shadow(0 15px 18px rgba(0, 80, 140, .12));
        }


        .theme-svg img {

            display: block;

            width: 100%;

            height: auto;

            background: transparent;
        }


        /* HERO SVG */

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
                    translateY(0) rotate(1deg);
            }

            50% {
                transform:
                    translateY(-22px) rotate(-2deg);
            }
        }


        /* LEFT CHARACTER */

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
                    translateY(0) rotate(-3deg);
            }

            50% {
                transform:
                    translateY(-16px) rotate(3deg);
            }
        }


        /* CRAWLING */

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
                    translateY(0) rotate(-2deg);
            }

            50% {
                transform:
                    translateY(-18px) rotate(2deg);
            }
        }


        /* LECTURE */

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
                    translateY(0) rotate(2deg);
            }

            50% {
                transform:
                    translateY(-15px) rotate(-2deg);
            }
        }


        /* CUTE */

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
                    translateY(0) rotate(-3deg);
            }

            50% {
                transform:
                    translateY(-20px) rotate(3deg);
            }
        }


        /* =====================================================
           SECTION COMMON
           ===================================================== */

        .section {

            position: relative;

            padding:
                100px 0;

            overflow: hidden;
        }


        .section-title {

            font-family:
                "Baloo 2",
                sans-serif;

            font-size:
                clamp(2.4rem, 6vw, 4rem);

            font-weight: 800;

            color: var(--deep-blue);

            line-height: 1;

            margin-bottom: 12px;
        }


        .section-lead {

            color: #688198;

            max-width: 650px;

            margin:
                0 auto 45px;

            font-size: 1.05rem;
        }


        /* =====================================================
           EVENT SECTION
           ===================================================== */

        .event-section {

            background:
                #ffffff;
        }


        .event-card {

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

            height: 100%;

            box-shadow:
                var(--shadow);

            text-align: center;

            transition:
                transform .3s ease;
        }


        .event-card:hover {

            transform:
                translateY(-8px);
        }


        .event-icon {

            width: 70px;

            height: 70px;

            margin:
                0 auto 18px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 22px;

            font-size: 1.8rem;
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


        .event-icon.pink {

            background:
                #ffe5ef;

            color:
                #f05b91;
        }


        .event-icon.green {

            background:
                #e3fbed;

            color:
                #2eaf69;
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
        }


        /* =====================================================
           FUN STRIP
           ===================================================== */

        .fun-strip {

            padding:
                28px 0;

            background:
                linear-gradient(90deg,
                    #1689e8,
                    #8b76ff,
                    #ff75a8,
                    #ff9f43);

            color:
                white;

            overflow:
                hidden;
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
        }


        .fun-item {

            white-space:
                nowrap;

            font-weight:
                700;

            font-size:
                1.1rem;
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
        }


        .godparent-box::before {

            content:
                "";

            position:
                absolute;

            width:
                120px;

            height:
                120px;

            border-radius:
                50%;

            background:
                rgba(255, 216, 77, .25);

            right:
                -35px;

            top:
                -35px;
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
        }


        .godparent-list {

            list-style:
                none;

            padding:
                0;

            margin:
                0;
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
        }


        .godparent-list li:last-child {
            border-bottom: 0;
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
                    #ffe9f2,
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
                transform .25s ease;
        }


        .celebrate-button:hover {

            transform:
                translateY(-5px) scale(1.02);
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
        }


        footer strong {
            color: white;
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
                    translateY(-30px) rotate(0deg);

                opacity:
                    1;
            }

            100% {

                transform:
                    translateY(110vh) rotate(720deg);

                opacity:
                    0;
            }
        }


        /* =====================================================
           SPARKLE
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
           RESPONSIVE
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
                left: -5px;
            }

            .balloon-2 {
                right: -5px;
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
        }
    </style>

</head>


<body>


    <!-- =========================================================
     INTRO / ENVELOPE
     ========================================================= -->

    <div id="intro">


        <!-- Floating intro decorations -->

        <div class="decor-layer">

            <div class="star decor">
                ★
            </div>

            <div class="star decor">
                ✦
            </div>

            <div class="star decor">
                ★
            </div>

            <div class="star decor">
                ✦
            </div>

            <div class="star decor">
                ★
            </div>


            <div class="balloon balloon-blue balloon-1"></div>

            <div class="balloon balloon-yellow balloon-2"></div>

            <div class="balloon balloon-pink balloon-3"></div>

            <div class="balloon balloon-purple balloon-4"></div>

        </div>


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
             ================================================= -->

            <div class="envelope-scene">

                <div
                    class="envelope"
                    id="envelope">


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

            </div>


            <button
                type="button"
                class="open-button"
                id="openInvitation">

                <i class="fi fi-rr-envelope-open me-2"></i>

                Open Invitation

            </button>


        </div>

    </div>


    <!-- =========================================================
     MAIN PAGE
     ========================================================= -->

    <div id="page">


        <!-- =====================================================
         HERO
         ===================================================== -->

        <section class="hero">


            <!-- THEME SVG: integrated decoration -->

            <div class="theme-svg svg-cute">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['cute']); ?>"
                    alt=""
                    aria-hidden="true">

            </div>


            <div class="theme-svg svg-hero">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['poster']); ?>"
                    alt=""
                    aria-hidden="true">

            </div>


            <div class="theme-svg svg-left">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['hands']); ?>"
                    alt=""
                    aria-hidden="true">

            </div>


            <div class="theme-svg svg-crawl">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['crawl']); ?>"
                    alt=""
                    aria-hidden="true">

            </div>


            <!-- Decorative stars -->

            <div class="decor-layer">

                <div class="star decor">★</div>

                <div class="star decor">✦</div>

                <div class="star decor">★</div>

                <div class="star decor">✦</div>

                <div class="star decor">★</div>


                <div class="cloud cloud-1 decor">
                    <i class="fi fi-sr-cloud"></i>
                </div>

                <div class="cloud cloud-2 decor">
                    <i class="fi fi-sr-cloud"></i>
                </div>

                <div class="cloud cloud-3 decor">
                    <i class="fi fi-sr-cloud"></i>
                </div>


                <div class="balloon balloon-blue balloon-1"></div>

                <div class="balloon balloon-yellow balloon-2"></div>

                <div class="balloon balloon-pink balloon-3"></div>

                <div class="balloon balloon-purple balloon-4"></div>

            </div>


            <div class="container">

                <div class="hero-content">


                    <div class="little-boss">

                        <i class="fi fi-sr-baby"></i>

                        A VERY SPECIAL LITTLE GUY

                    </div>


                    <h1 class="hero-title">

                        Meet

                        <span class="pink">
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


        <!-- =====================================================
         EVENT TITLE
         ===================================================== -->

        <section class="section event-section">


            <div class="container">

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

                            <div class="event-icon pink">

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
                    aria-hidden="true">

            </div>


        </section>


        <!-- =====================================================
         FUN MOVING STRIP
         ===================================================== -->

        <div class="fun-strip">

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


        <!-- =====================================================
         GODPARENTS
         ===================================================== -->

        <section class="section godparents">


            <!-- Crawling decoration -->

            <div class="theme-svg svg-crawl">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['crawl']); ?>"
                    alt=""
                    aria-hidden="true">

            </div>


            <div class="container">


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


        <!-- =====================================================
         CLOSING
         ===================================================== -->

        <section class="section closing">


            <!-- Theme decoration -->

            <div class="theme-svg svg-hero">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['cute']); ?>"
                    alt=""
                    aria-hidden="true">

            </div>


            <div class="theme-svg svg-left">

                <img
                    src="<?php echo htmlspecialchars($themeSvgs['hands']); ?>"
                    alt=""
                    aria-hidden="true">

            </div>


            <div class="decor-layer">

                <div class="star decor">★</div>

                <div class="star decor">✦</div>

                <div class="star decor">★</div>

                <div class="star decor">✦</div>

                <div class="star decor">★</div>

            </div>


            <div class="container">

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


        <!-- =====================================================
         FOOTER
         ===================================================== -->

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


    <!-- =========================================================
     BOOTSTRAP JS
     ========================================================= -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        /* =========================================================
   ELEMENTS
   ========================================================= */

        const intro =
            document.getElementById("intro");

        const page =
            document.getElementById("page");

        const envelope =
            document.getElementById("envelope");

        const openButton =
            document.getElementById("openInvitation");

        const celebrateButton =
            document.getElementById("celebrateButton");


        /* =========================================================
           OPEN INVITATION
           ========================================================= */

        openButton.addEventListener(
            "click",
            function() {

                envelope.classList.add("opened");

                openButton.disabled = true;

                openButton.innerHTML =
                    '<i class="fi fi-rr-check me-2"></i>' +
                    'Opening...';


                /* ---------------------------------------------
                   Small delay before revealing the page
                   --------------------------------------------- */

                setTimeout(
                    function() {

                        intro.classList.add("hidden");

                        page.classList.add("show");

                        document.body.style.overflowX =
                            "hidden";


                        launchConfetti(90);

                    },
                    1200
                );

            }
        );


        /* =========================================================
           CONFETTI
           ========================================================= */

        function launchConfetti(amount = 60) {

            const pieces = [
                "⭐",
                "✦",
                "●",
                "■",
                "◆"
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
                    Math.random() * 1.5;

                const randomDuration =
                    2.5 +
                    Math.random() * 3;


                piece.style.left =
                    randomX + "vw";

                piece.style.animationDuration =
                    randomDuration + "s";

                piece.style.animationDelay =
                    randomDelay + "s";


                const size =
                    6 +
                    Math.random() * 9;

                piece.style.width =
                    size + "px";

                piece.style.height =
                    size * 1.5 + "px";


                piece.innerHTML =
                    pieces[
                        Math.floor(
                            Math.random() *
                            pieces.length
                        )
                    ];


                document.body.appendChild(piece);


                setTimeout(
                    function() {

                        piece.remove();

                    },
                    (randomDuration + randomDelay) * 1000 + 500
                );

            }

        }


        /* =========================================================
           PERIODIC PARTY CONFETTI
           ========================================================= */

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


        /* =========================================================
           CELEBRATE BUTTON
           ========================================================= */

        if (celebrateButton) {

            celebrateButton.addEventListener(
                "click",
                function() {

                    launchConfetti(120);

                }
            );

        }


        /* =========================================================
           SPARKLE EFFECT
           ========================================================= */

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
                let i = 0; i < 7; i++
            ) {

                const sparkle =
                    document.createElement("div");

                sparkle.className =
                    "sparkle";


                sparkle.style.left =
                    (
                        x +
                        (Math.random() * 50 - 25)
                    ) + "px";


                sparkle.style.top =
                    (
                        y +
                        (Math.random() * 50 - 25)
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


        /* =========================================================
           SCROLL REVEAL
           ========================================================= */

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


        /* =========================================================
           AUTOMATIC FIRST CONFETTI BURST
           ========================================================= */

        setTimeout(
            function() {

                if (
                    !intro.classList.contains("hidden")
                ) {

                    /*
                     * Keep the intro clean.
                     * The large celebration starts
                     * when the envelope is opened.
                     */

                    return;

                }

                launchConfetti(30);

            },
            2500
        );
    </script>


</body>

</html>