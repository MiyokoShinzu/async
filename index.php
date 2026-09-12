<?php
/* =========================================================
   ETS-ASYNC LANDING PAGE
   College of Engineering and Architecture
   GitHub + Anime.js Inspired Design
   ========================================================= */

date_default_timezone_set('Asia/Manila');

$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- =====================================================
         BASIC META
    ====================================================== -->
    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta name="description"
        content="ETS-Async — Asynchronous Learning Portal for students of the College of Engineering and Architecture.">

    <meta name="theme-color"
        content="#0d1117">

    <meta name="author"
        content="ETS-DEV">

    <!-- =====================================================
         PAGE TITLE
    ====================================================== -->
    <title>ETS-Async | College of Engineering and Architecture</title>

    <!-- =====================================================
         FAVICON
    ====================================================== -->
    <link rel="icon"
        type="image/png"
        href="./assets/pubmat/head.png">

    <!-- =====================================================
         GOOGLE FONTS
    ====================================================== -->
    <link rel="preconnect"
        href="https://fonts.googleapis.com">

    <link rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- =====================================================
         BOOTSTRAP 5
    ====================================================== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- =====================================================
         BOOTSTRAP ICONS
    ====================================================== -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- =====================================================
         ANIME.JS
    ====================================================== -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>


    <style>
        /* =====================================================
           ROOT VARIABLES
        ====================================================== */

        :root {

            --github-bg: #0d1117;
            --github-bg-soft: #161b22;
            --github-card: #1c2128;
            --github-border: #30363d;

            --white: #ffffff;
            --text: #f0f6fc;
            --muted: #8b949e;

            --blue: #2f81f7;
            --blue-light: #58a6ff;

            --cyan: #56d4dd;
            --purple: #a371f7;
            --pink: #f778ba;

            --green: #3fb950;

            --surface: #f6f8fa;
            --surface-border: #d0d7de;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 20px;
            --radius-xl: 28px;

            --container: 1180px;

            --shadow:
                0 20px 60px rgba(0, 0, 0, 0.35);
        }


        /* =====================================================
           GLOBAL
        ====================================================== */

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {

            margin: 0;

            font-family:
                "Inter",
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            color: var(--text);

            background:
                var(--github-bg);

            overflow-x: hidden;
        }


        a {
            text-decoration: none;
        }


        .container-main {

            width: min(calc(100% - 40px),
                    var(--container));

            margin: auto;
        }


        /* =====================================================
           ANIMATED BACKGROUND
        ====================================================== */

        .background-grid {

            position: fixed;

            inset: 0;

            pointer-events: none;

            z-index: 0;

            opacity: 0.35;

            background-image:
                linear-gradient(rgba(88, 166, 255, 0.035) 1px,
                    transparent 1px),
                linear-gradient(90deg,
                    rgba(88, 166, 255, 0.035) 1px,
                    transparent 1px);

            background-size:
                60px 60px;

            mask-image:
                linear-gradient(to bottom,
                    black,
                    transparent 90%);
        }


        /* =====================================================
           GLOW ORBS
        ====================================================== */

        .orb {

            position: fixed;

            border-radius: 50%;

            filter: blur(100px);

            pointer-events: none;

            z-index: 0;

            opacity: 0.12;
        }


        .orb-one {

            width: 420px;
            height: 420px;

            background: var(--blue);

            top: -180px;
            left: -120px;
        }


        .orb-two {

            width: 380px;
            height: 380px;

            background: var(--purple);

            top: 45%;
            right: -160px;
        }


        .orb-three {

            width: 300px;
            height: 300px;

            background: var(--cyan);

            bottom: -100px;
            left: 35%;
        }


        /* =====================================================
           NAVIGATION
        ====================================================== */

        .navbar-custom {

            position: fixed;

            top: 0;
            left: 0;
            right: 0;

            z-index: 1000;

            background:
                rgba(13, 17, 23, 0.82);

            border-bottom:
                1px solid rgba(255, 255, 255, 0.08);

            backdrop-filter:
                blur(18px);
        }


        .navbar-inner {

            min-height: 72px;

            display: flex;

            align-items: center;

            justify-content: space-between;
        }


        /* =====================================================
           BRAND
        ====================================================== */

        .brand {

            display: flex;

            align-items: center;

            gap: 12px;

            color: var(--white);
        }


        .brand-logo {

            width: 42px;
            height: 42px;

            object-fit: contain;

            border-radius: 10px;

            filter:
                drop-shadow(0 0 15px rgba(88, 166, 255, 0.3));
        }


        .brand-text {

            display: flex;

            flex-direction: column;
        }


        .brand-name {

            font-family: "Poppins", sans-serif;

            font-size: 17px;

            font-weight: 800;

            line-height: 1;
        }


        .brand-subtitle {

            color: var(--muted);

            font-size: 10px;

            margin-top: 5px;

            letter-spacing: 0.5px;
        }


        /* =====================================================
           NAV LINKS
        ====================================================== */

        .nav-links {

            display: flex;

            align-items: center;

            gap: 28px;
        }


        .nav-links a {

            color: var(--muted);

            font-size: 14px;

            font-weight: 500;

            transition:
                color 0.2s ease;
        }


        .nav-links a:hover {

            color: var(--white);
        }


        .nav-login {

            display: inline-flex;

            align-items: center;

            gap: 8px;

            padding: 8px 15px;

            color: white !important;

            border:
                1px solid rgba(255, 255, 255, 0.15);

            border-radius: 8px;

            background:
                rgba(255, 255, 255, 0.04);

            transition:
                all 0.25s ease;
        }


        .nav-login:hover {

            background:
                rgba(255, 255, 255, 0.09);

            border-color:
                rgba(255, 255, 255, 0.3);

            transform:
                translateY(-1px);
        }


        /* =====================================================
           HERO
        ====================================================== */

        .hero {

            position: relative;

            min-height: 850px;

            display: flex;

            align-items: center;

            padding:
                130px 0 100px;

            overflow: hidden;
        }


        .hero-content {

            position: relative;

            z-index: 2;
        }


        .hero-grid {

            display: grid;

            grid-template-columns:
                1fr 0.95fr;

            align-items: center;

            gap: 80px;
        }


        /* =====================================================
           HERO EYEBROW
        ====================================================== */

        .eyebrow {

            display: inline-flex;

            align-items: center;

            gap: 9px;

            padding: 7px 12px;

            border:
                1px solid rgba(88, 166, 255, 0.22);

            border-radius: 100px;

            background:
                rgba(88, 166, 255, 0.06);

            color: var(--blue-light);

            font-size: 12px;

            font-weight: 600;

            margin-bottom: 25px;
        }


        .eyebrow-dot {

            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: var(--green);

            box-shadow:
                0 0 12px rgba(63, 185, 80, 0.8);
        }


        /* =====================================================
           HERO TITLE
        ====================================================== */

        .hero-title {

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(48px, 6vw, 82px);

            line-height: 1.02;

            letter-spacing: -3.5px;

            font-weight: 800;

            margin: 0 0 28px;

            max-width: 760px;
        }


        .gradient-text {

            background:
                linear-gradient(90deg,
                    var(--blue-light),
                    var(--purple),
                    var(--cyan));

            -webkit-background-clip: text;

            background-clip: text;

            color: transparent;

            background-size: 200% 200%;

            animation:
                gradientMove 5s ease infinite;
        }


        @keyframes gradientMove {

            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }


        /* =====================================================
           HERO DESCRIPTION
        ====================================================== */

        .hero-description {

            max-width: 650px;

            color: var(--muted);

            font-size: 17px;

            line-height: 1.8;

            margin-bottom: 34px;
        }


        /* =====================================================
           BUTTON
        ====================================================== */

        .hero-buttons {

            display: flex;

            flex-wrap: wrap;

            gap: 12px;
        }


        .btn-login {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 9px;

            min-width: 125px;

            padding:
                13px 22px;

            color: white;

            font-size: 14px;

            font-weight: 700;

            border-radius: 8px;

            border: 1px solid rgba(255, 255, 255, 0.1);

            background:
                linear-gradient(135deg,
                    #238636,
                    #2ea043);

            box-shadow:
                0 10px 30px rgba(46, 160, 67, 0.18);

            transition:
                all 0.25s ease;
        }


        .btn-login:hover {

            color: white;

            transform:
                translateY(-2px);

            box-shadow:
                0 15px 35px rgba(46, 160, 67, 0.28);
        }


        /* =====================================================
           HERO STATS
        ====================================================== */

        .hero-stats {

            display: flex;

            flex-wrap: wrap;

            gap: 28px;

            margin-top: 40px;

            padding-top: 28px;

            border-top:
                1px solid rgba(255, 255, 255, 0.08);
        }


        .stat {

            display: flex;

            align-items: center;

            gap: 9px;

            color: var(--muted);

            font-size: 12px;
        }


        .stat i {

            color: var(--blue-light);

            font-size: 16px;
        }


        /* =====================================================
           3D SCENE
        ====================================================== */

        .scene {

            position: relative;

            height: 550px;

            display: flex;

            align-items: center;

            justify-content: center;

            perspective: 1500px;
        }


        /* =====================================================
           3D ORBIT
        ====================================================== */

        .orbit {

            position: absolute;

            width: 460px;
            height: 460px;

            border:
                1px solid rgba(88, 166, 255, 0.14);

            border-radius: 50%;

            transform:
                rotateX(67deg) rotateZ(-18deg);

            box-shadow:
                0 0 60px rgba(47, 129, 247, 0.06);
        }


        .orbit-two {

            width: 380px;
            height: 380px;

            transform:
                rotateX(67deg) rotateZ(32deg);

            border-color:
                rgba(163, 113, 247, 0.12);
        }


        /* =====================================================
           ORBIT DOTS
        ====================================================== */

        .orbit-dot {

            position: absolute;

            width: 10px;
            height: 10px;

            border-radius: 50%;

            background:
                var(--blue-light);

            box-shadow:
                0 0 20px var(--blue-light);
        }


        .dot-one {

            top: 45px;
            left: 65px;
        }


        .dot-two {

            right: 35px;
            bottom: 85px;

            background:
                var(--purple);

            box-shadow:
                0 0 20px var(--purple);
        }


        .dot-three {

            top: 100px;
            right: 50px;

            background:
                var(--cyan);

            box-shadow:
                0 0 20px var(--cyan);
        }


        /* =====================================================
           3D DASHBOARD
        ====================================================== */

        .dashboard-3d {

            position: relative;

            width: 430px;

            border:
                1px solid rgba(255, 255, 255, 0.13);

            border-radius: 18px;

            background:
                linear-gradient(145deg,
                    rgba(35, 40, 48, 0.96),
                    rgba(18, 22, 28, 0.96));

            box-shadow:
                0 50px 100px rgba(0, 0, 0, 0.5),
                0 0 70px rgba(47, 129, 247, 0.08);

            overflow: hidden;

            transform:
                rotateY(-14deg) rotateX(7deg) rotateZ(2deg);

            transform-style: preserve-3d;

            z-index: 5;
        }


        /* =====================================================
           DASHBOARD TOP BAR
        ====================================================== */

        .dashboard-top {

            height: 42px;

            display: flex;

            align-items: center;

            gap: 7px;

            padding: 0 15px;

            border-bottom:
                1px solid rgba(255, 255, 255, 0.08);

            background:
                rgba(255, 255, 255, 0.025);
        }


        .window-dot {

            width: 8px;
            height: 8px;

            border-radius: 50%;

            background:
                rgba(255, 255, 255, 0.2);
        }


        .window-title {

            margin-left: 8px;

            color: var(--muted);

            font-size: 10px;
        }


        /* =====================================================
           DASHBOARD BODY
        ====================================================== */

        .dashboard-body {

            display: grid;

            grid-template-columns:
                105px 1fr;

            min-height: 330px;
        }


        /* =====================================================
           DASHBOARD SIDEBAR
        ====================================================== */

        .dashboard-sidebar {

            padding: 18px 10px;

            border-right:
                1px solid rgba(255, 255, 255, 0.07);

            background:
                rgba(0, 0, 0, 0.12);
        }


        .side-logo {

            width: 31px;
            height: 31px;

            margin: 0 auto 25px;

            object-fit: contain;
        }


        .side-item {

            height: 32px;

            display: flex;

            align-items: center;

            gap: 8px;

            padding: 0 9px;

            margin-bottom: 5px;

            border-radius: 6px;

            color: #7d8590;

            font-size: 9px;
        }


        .side-item.active {

            color: white;

            background:
                rgba(47, 129, 247, 0.15);
        }


        .side-item i {

            font-size: 11px;
        }


        /* =====================================================
           DASHBOARD CONTENT
        ====================================================== */

        .dashboard-content {

            padding: 22px;
        }


        .mini-heading {

            color: var(--muted);

            font-size: 9px;

            margin-bottom: 5px;
        }


        .mini-title {

            color: white;

            font-family: "Poppins", sans-serif;

            font-size: 18px;

            font-weight: 700;

            margin-bottom: 18px;
        }


        /* =====================================================
           PROGRESS CARD
        ====================================================== */

        .progress-card {

            padding: 15px;

            border:
                1px solid rgba(255, 255, 255, 0.08);

            border-radius: 10px;

            background:
                rgba(255, 255, 255, 0.035);

            margin-bottom: 14px;
        }


        .progress-header {

            display: flex;

            justify-content: space-between;

            color: var(--muted);

            font-size: 9px;

            margin-bottom: 9px;
        }


        .progress-value {

            color: var(--blue-light);

            font-weight: 700;
        }


        .progress-bar-custom {

            height: 5px;

            background:
                rgba(255, 255, 255, 0.08);

            border-radius: 20px;

            overflow: hidden;
        }


        .progress-fill {

            width: 72%;

            height: 100%;

            background:
                linear-gradient(90deg,
                    var(--blue),
                    var(--purple));

            border-radius: inherit;
        }


        /* =====================================================
           ACTIVITY CARDS
        ====================================================== */

        .activity {

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 10px 11px;

            border:
                1px solid rgba(255, 255, 255, 0.06);

            border-radius: 8px;

            margin-bottom: 7px;

            background:
                rgba(255, 255, 255, 0.02);
        }


        .activity-icon {

            width: 27px;
            height: 27px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 7px;

            background:
                rgba(47, 129, 247, 0.13);

            color:
                var(--blue-light);

            font-size: 11px;
        }


        .activity-text {

            flex: 1;
        }


        .activity-title {

            color: #e6edf3;

            font-size: 9px;

            font-weight: 600;
        }


        .activity-subtitle {

            color: var(--muted);

            font-size: 7px;

            margin-top: 2px;
        }


        .activity-status {

            width: 7px;
            height: 7px;

            border-radius: 50%;

            background:
                var(--green);

            box-shadow:
                0 0 8px rgba(63, 185, 80, 0.7);
        }


        /* =====================================================
           FLOATING CARDS
        ====================================================== */

        .floating-card {

            position: absolute;

            z-index: 10;

            padding: 12px 14px;

            border:
                1px solid rgba(255, 255, 255, 0.12);

            border-radius: 12px;

            background:
                rgba(22, 27, 34, 0.9);

            backdrop-filter:
                blur(15px);

            box-shadow:
                0 20px 40px rgba(0, 0, 0, 0.35);
        }


        .floating-card-left {

            left: 0;

            bottom: 75px;

            transform:
                rotateY(10deg) rotateZ(-3deg);
        }


        .floating-card-right {

            right: -10px;

            top: 75px;

            transform:
                rotateY(-8deg) rotateZ(3deg);
        }


        .floating-label {

            color: var(--muted);

            font-size: 8px;

            margin-bottom: 4px;
        }


        .floating-value {

            color: white;

            font-family: "Poppins", sans-serif;

            font-size: 18px;

            font-weight: 800;
        }


        .floating-icon {

            display: inline-flex;

            width: 28px;
            height: 28px;

            align-items: center;

            justify-content: center;

            border-radius: 8px;

            margin-right: 8px;

            background:
                rgba(163, 113, 247, 0.15);

            color:
                var(--purple);
        }


        /* =====================================================
           SECTION
        ====================================================== */

        .section {

            position: relative;

            z-index: 2;

            padding:
                110px 0;
        }


        .section-light {

            color: #24292f;

            background:
                var(--surface);
        }


        .section-heading {

            max-width: 700px;

            margin-bottom: 55px;
        }


        .section-kicker {

            color:
                var(--blue);

            font-size: 12px;

            font-weight: 700;

            text-transform: uppercase;

            letter-spacing: 1.2px;

            margin-bottom: 12px;
        }


        .section-title {

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(32px, 4vw, 48px);

            line-height: 1.15;

            font-weight: 800;

            letter-spacing: -1.5px;

            margin-bottom: 16px;
        }


        .section-description {

            color:
                var(--muted);

            line-height: 1.75;

            font-size: 15px;
        }


        .section-light .section-description {

            color:
                #57606a;
        }


        /* =====================================================
           RESOURCE CARDS
        ====================================================== */

        .resource-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;
        }


        .resource-card {

            padding: 28px;

            border:
                1px solid var(--surface-border);

            border-radius:
                var(--radius-lg);

            background:
                white;

            transition:
                transform 0.3s ease,
                box-shadow 0.3s ease,
                border-color 0.3s ease;
        }


        .resource-card:hover {

            transform:
                translateY(-7px);

            border-color:
                #afb8c1;

            box-shadow:
                0 20px 50px rgba(31, 35, 40, 0.1);
        }


        .resource-icon {

            width: 48px;
            height: 48px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin-bottom: 20px;

            border-radius: 12px;

            color:
                var(--blue);

            background:
                rgba(47, 129, 247, 0.08);

            font-size: 21px;
        }


        .resource-card h3 {

            font-family:
                "Poppins",
                sans-serif;

            font-size: 18px;

            font-weight: 700;

            margin-bottom: 10px;
        }


        .resource-card p {

            color:
                #57606a;

            font-size: 13px;

            line-height: 1.7;

            margin: 0;
        }


        /* =====================================================
           HOW IT WORKS
        ====================================================== */

        .workflow {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;
        }


        .workflow-card {

            position: relative;

            padding: 28px;

            border:
                1px solid var(--github-border);

            border-radius:
                var(--radius-lg);

            background:
                var(--github-card);
        }


        .workflow-number {

            font-family:
                "Poppins",
                sans-serif;

            font-size: 48px;

            line-height: 1;

            font-weight: 800;

            color:
                rgba(88, 166, 255, 0.15);

            margin-bottom: 20px;
        }


        .workflow-card h3 {

            color: white;

            font-family:
                "Poppins",
                sans-serif;

            font-size: 18px;

            margin-bottom: 10px;
        }


        .workflow-card p {

            color:
                var(--muted);

            font-size: 13px;

            line-height: 1.7;

            margin: 0;
        }


        /* =====================================================
           GUIDELINES
        ====================================================== */

        .guidelines {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 18px;
        }


        .guideline {

            display: flex;

            gap: 16px;

            padding: 23px;

            border:
                1px solid var(--surface-border);

            border-radius:
                var(--radius-md);

            background:
                white;
        }


        .guideline-icon {

            flex: 0 0 40px;

            width: 40px;
            height: 40px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 10px;

            color:
                var(--blue);

            background:
                rgba(47, 129, 247, 0.08);
        }


        .guideline h4 {

            font-size: 14px;

            font-weight: 700;

            margin-bottom: 5px;
        }


        .guideline p {

            color:
                #57606a;

            font-size: 12px;

            line-height: 1.6;

            margin: 0;
        }


        /* =====================================================
           FINAL CTA
        ====================================================== */

        .final-cta {

            position: relative;

            overflow: hidden;

            padding:
                90px 30px;

            text-align: center;

            border-top:
                1px solid var(--github-border);

            border-bottom:
                1px solid var(--github-border);

            background:
                linear-gradient(135deg,
                    #111827,
                    #0d1117);
        }


        .final-cta::before {

            content: "";

            position: absolute;

            width: 500px;
            height: 500px;

            left: 50%;
            top: 50%;

            transform:
                translate(-50%, -50%);

            border-radius: 50%;

            background:
                radial-gradient(circle,
                    rgba(47, 129, 247, 0.12),
                    transparent 65%);
        }


        .final-cta-content {

            position: relative;

            z-index: 2;
        }


        .final-cta h2 {

            font-family:
                "Poppins",
                sans-serif;

            font-size:
                clamp(32px, 5vw, 54px);

            font-weight: 800;

            letter-spacing: -2px;

            margin-bottom: 15px;
        }


        .final-cta p {

            max-width: 600px;

            margin:
                0 auto 28px;

            color:
                var(--muted);

            font-size: 15px;

            line-height: 1.7;
        }


        /* =====================================================
           FOOTER
        ====================================================== */

        footer {

            padding:
                35px 0;

            background:
                #080b0f;

            border-top:
                1px solid rgba(255, 255, 255, 0.06);
        }


        .footer-inner {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;
        }


        .footer-brand {

            display: flex;

            align-items: center;

            gap: 10px;
        }


        .footer-brand img {

            width: 30px;
            height: 30px;

            object-fit: contain;
        }


        .footer-brand span {

            color:
                #c9d1d9;

            font-size: 12px;

            font-weight: 600;
        }


        .footer-copy {

            color:
                #6e7681;

            font-size: 11px;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1050px) {

            .hero-grid {

                grid-template-columns:
                    1fr;

                gap: 50px;
            }


            .hero {

                padding-top: 120px;
            }


            .scene {

                height: 500px;
            }


            .resource-grid,
            .workflow {

                grid-template-columns:
                    1fr 1fr;
            }
        }


        @media (max-width: 768px) {

            .container-main {

                width:
                    min(calc(100% - 28px),
                        var(--container));
            }


            .nav-links a:not(.nav-login) {

                display: none;
            }


            .navbar-inner {

                min-height: 65px;
            }


            .hero {

                min-height: auto;

                padding:
                    110px 0 60px;
            }


            .hero-title {

                font-size:
                    clamp(43px, 12vw, 65px);

                letter-spacing: -2.5px;
            }


            .hero-description {

                font-size: 15px;
            }


            .scene {

                height: 430px;

                transform:
                    scale(0.82);
            }


            .dashboard-3d {

                width: 390px;
            }


            .floating-card-left {

                left: -10px;
            }


            .floating-card-right {

                right: -20px;
            }


            .resource-grid,
            .workflow,
            .guidelines {

                grid-template-columns:
                    1fr;
            }


            .section {

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


        @media (max-width: 480px) {

            .brand-subtitle {

                display: none;
            }


            .brand-logo {

                width: 37px;
                height: 37px;
            }


            .brand-name {

                font-size: 15px;
            }


            .nav-login {

                padding:
                    7px 11px;
            }


            .hero-buttons {

                width: 100%;
            }


            .btn-login {

                width: 100%;
            }


            .hero-stats {

                gap: 15px;

                flex-direction:
                    column;
            }


            .scene {

                height: 350px;

                transform:
                    scale(0.66);

                margin:
                    -30px -80px;
            }
        }


        /* =====================================================
           ACCESSIBILITY
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
         BACKGROUND EFFECTS
    ====================================================== -->

    <div class="background-grid"></div>

    <div class="orb orb-one"></div>

    <div class="orb orb-two"></div>

    <div class="orb orb-three"></div>


    <!-- =====================================================
         NAVIGATION
    ====================================================== -->

    <header class="navbar-custom">

        <div class="container-main">

            <div class="navbar-inner">

                <!-- BRAND -->
                <a href="#" class="brand">

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


                <!-- NAVIGATION -->
                <nav class="nav-links">

                    <a href="#platform">
                        Platform
                    </a>

                    <a href="#how-it-works">
                        How It Works
                    </a>

                    <a href="#guidelines">
                        Guidelines
                    </a>

                    <a href="login.php"
                        class="nav-login">

                        <i class="bi bi-box-arrow-in-right"></i>

                        Login

                    </a>

                </nav>

            </div>

        </div>

    </header>


    <!-- =====================================================
         HERO SECTION
    ====================================================== -->

    <main>

        <section class="hero">

            <div class="container-main hero-content">

                <div class="hero-grid">


                    <!-- =================================================
                         HERO TEXT
                    ================================================== -->

                    <div class="hero-copy">

                        <div class="eyebrow hero-animate">

                            <span class="eyebrow-dot"></span>

                            College of Engineering and Architecture
                            • Asynchronous Learning

                        </div>


                        <h1 class="hero-title hero-animate">

                            Learn.
                            <span class="gradient-text">
                                Build.
                            </span>
                            Complete.

                        </h1>


                        <p class="hero-description hero-animate">

                            A modern asynchronous learning environment
                            for students of the College of Engineering
                            and Architecture. Access learning materials,
                            academic activities, and progress resources
                            in one focused platform.

                        </p>


                        <!-- =================================================
                             LOGIN BUTTON
                        ================================================== -->

                        <div class="hero-buttons hero-animate">

                            <a href="login.php"
                                class="btn-login">

                                <i class="bi bi-box-arrow-in-right"></i>

                                Login

                            </a>

                        </div>


                        <!-- =================================================
                             HERO STATS
                        ================================================== -->

                        <div class="hero-stats hero-animate">

                            <div class="stat">

                                <i class="bi bi-mortarboard-fill"></i>

                                <span>
                                    Engineering & Architecture
                                </span>

                            </div>


                            <div class="stat">

                                <i class="bi bi-cloud-check-fill"></i>

                                <span>
                                    Online Learning
                                </span>

                            </div>


                            <div class="stat">

                                <i class="bi bi-phone-fill"></i>

                                <span>
                                    Responsive Platform
                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         3D PLATFORM VISUAL
                    ================================================== -->

                    <div class="scene scene-animate">

                        <!-- ORBITS -->

                        <div class="orbit">

                            <span class="orbit-dot dot-one"></span>

                            <span class="orbit-dot dot-two"></span>

                        </div>


                        <div class="orbit orbit-two">

                            <span class="orbit-dot dot-three"></span>

                        </div>


                        <!-- FLOATING CARD -->

                        <div class="floating-card floating-card-left">

                            <div class="floating-label">
                                Learning Progress
                            </div>

                            <div class="floating-value">

                                <i class="bi bi-graph-up-arrow floating-icon"></i>

                                72%

                            </div>

                        </div>


                        <!-- FLOATING CARD -->

                        <div class="floating-card floating-card-right">

                            <div class="floating-label">
                                Activities
                            </div>

                            <div class="floating-value">

                                08

                            </div>

                        </div>


                        <!-- =================================================
                             3D DASHBOARD
                        ================================================== -->

                        <div class="dashboard-3d"
                            id="dashboard3d">


                            <!-- WINDOW BAR -->

                            <div class="dashboard-top">

                                <span class="window-dot"></span>

                                <span class="window-dot"></span>

                                <span class="window-dot"></span>

                                <span class="window-title">
                                    ETS-Async / Dashboard
                                </span>

                            </div>


                            <!-- DASHBOARD BODY -->

                            <div class="dashboard-body">


                                <!-- SIDEBAR -->

                                <aside class="dashboard-sidebar">

                                    <img
                                        src="./assets/pubmat/head.png"
                                        alt="ETS"
                                        class="side-logo">


                                    <div class="side-item active">

                                        <i class="bi bi-grid-1x2-fill"></i>

                                        Dashboard

                                    </div>


                                    <div class="side-item">

                                        <i class="bi bi-play-circle"></i>

                                        Lectures

                                    </div>


                                    <div class="side-item">

                                        <i class="bi bi-journal-text"></i>

                                        Activities

                                    </div>


                                    <div class="side-item">

                                        <i class="bi bi-bar-chart"></i>

                                        Progress

                                    </div>


                                    <div class="side-item">

                                        <i class="bi bi-person"></i>

                                        Profile

                                    </div>

                                </aside>


                                <!-- CONTENT -->

                                <div class="dashboard-content">

                                    <div class="mini-heading">
                                        STUDENT DASHBOARD
                                    </div>

                                    <div class="mini-title">
                                        Welcome back!
                                    </div>


                                    <!-- PROGRESS -->

                                    <div class="progress-card">

                                        <div class="progress-header">

                                            <span>
                                                Overall Learning Progress
                                            </span>

                                            <span class="progress-value">
                                                72%
                                            </span>

                                        </div>


                                        <div class="progress-bar-custom">

                                            <div
                                                class="progress-fill"
                                                id="progressFill">
                                            </div>

                                        </div>

                                    </div>


                                    <!-- ACTIVITY -->

                                    <div class="activity">

                                        <div class="activity-icon">

                                            <i class="bi bi-play-fill"></i>

                                        </div>

                                        <div class="activity-text">

                                            <div class="activity-title">
                                                Lecture Materials
                                            </div>

                                            <div class="activity-subtitle">
                                                Learning resources
                                            </div>

                                        </div>

                                        <span class="activity-status"></span>

                                    </div>


                                    <div class="activity">

                                        <div class="activity-icon">

                                            <i class="bi bi-pencil-square"></i>

                                        </div>

                                        <div class="activity-text">

                                            <div class="activity-title">
                                                Academic Activities
                                            </div>

                                            <div class="activity-subtitle">
                                                Assigned activities
                                            </div>

                                        </div>

                                        <span class="activity-status"></span>

                                    </div>


                                    <div class="activity">

                                        <div class="activity-icon">

                                            <i class="bi bi-graph-up"></i>

                                        </div>

                                        <div class="activity-text">

                                            <div class="activity-title">
                                                Learning Progress
                                            </div>

                                            <div class="activity-subtitle">
                                                Track your progress
                                            </div>

                                        </div>

                                        <span class="activity-status"></span>

                                    </div>


                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             PLATFORM
        ====================================================== -->

        <section class="section section-light"
            id="platform">

            <div class="container-main">


                <div class="section-heading reveal">

                    <div class="section-kicker">
                        Platform
                    </div>

                    <h2 class="section-title">

                        Everything you need
                        for asynchronous learning.

                    </h2>

                    <p class="section-description">

                        ETS-Async provides a focused digital environment
                        where students can access learning resources,
                        complete academic activities, and monitor their
                        progress.

                    </p>

                </div>


                <div class="resource-grid">


                    <!-- RESOURCE 1 -->

                    <div class="resource-card reveal">

                        <div class="resource-icon">

                            <i class="bi bi-journal-richtext"></i>

                        </div>

                        <h3>
                            Lectures & Materials
                        </h3>

                        <p>

                            Access assigned lectures, readings,
                            instructional materials, and other
                            academic resources in one place.

                        </p>

                    </div>


                    <!-- RESOURCE 2 -->

                    <div class="resource-card reveal">

                        <div class="resource-icon">

                            <i class="bi bi-clipboard-check"></i>

                        </div>

                        <h3>
                            Academic Activities
                        </h3>

                        <p>

                            Complete assigned learning activities
                            and academic tasks according to the
                            instructions and deadlines provided.

                        </p>

                    </div>


                    <!-- RESOURCE 3 -->

                    <div class="resource-card reveal">

                        <div class="resource-icon">

                            <i class="bi bi-activity"></i>

                        </div>

                        <h3>
                            Learning Progress
                        </h3>

                        <p>

                            Monitor your learning activity and
                            keep track of completed resources
                            and academic requirements.

                        </p>

                    </div>


                </div>

            </div>

        </section>


        <!-- =====================================================
             HOW IT WORKS
        ====================================================== -->

        <section class="section"
            id="how-it-works">

            <div class="container-main">


                <div class="section-heading reveal">

                    <div class="section-kicker">
                        How It Works
                    </div>

                    <h2 class="section-title">

                        A simple workflow.
                        Focused learning.

                    </h2>

                    <p class="section-description">

                        ETS-Async keeps asynchronous coursework
                        organized so students can focus on learning
                        and completing their academic responsibilities.

                    </p>

                </div>


                <div class="workflow">


                    <div class="workflow-card reveal">

                        <div class="workflow-number">
                            01
                        </div>

                        <h3>
                            Login
                        </h3>

                        <p>

                            Sign in using your assigned ETS-Async
                            account to access your available
                            learning resources and activities.

                        </p>

                    </div>


                    <div class="workflow-card reveal">

                        <div class="workflow-number">
                            02
                        </div>

                        <h3>
                            Study
                        </h3>

                        <p>

                            Review lectures, readings, multimedia
                            materials, and other resources provided
                            for your classes.

                        </p>

                    </div>


                    <div class="workflow-card reveal">

                        <div class="workflow-number">
                            03
                        </div>

                        <h3>
                            Complete
                        </h3>

                        <p>

                            Complete your assigned activities and
                            monitor your learning progress through
                            the platform.

                        </p>

                    </div>


                </div>

            </div>

        </section>


        <!-- =====================================================
             GUIDELINES
        ====================================================== -->

        <section class="section section-light"
            id="guidelines">

            <div class="container-main">


                <div class="section-heading reveal">

                    <div class="section-kicker">
                        Academic Guidelines
                    </div>

                    <h2 class="section-title">

                        Learn responsibly.
                        Work independently.

                    </h2>

                </div>


                <div class="guidelines">


                    <div class="guideline reveal">

                        <div class="guideline-icon">

                            <i class="bi bi-person-check"></i>

                        </div>

                        <div>

                            <h4>
                                Use Your Assigned Account
                            </h4>

                            <p>

                                Use only your authorized account
                                when accessing ETS-Async.

                            </p>

                        </div>

                    </div>


                    <div class="guideline reveal">

                        <div class="guideline-icon">

                            <i class="bi bi-list-check"></i>

                        </div>

                        <div>

                            <h4>
                                Follow Activity Instructions
                            </h4>

                            <p>

                                Read the instructions carefully
                                before completing academic activities.

                            </p>

                        </div>

                    </div>


                    <div class="guideline reveal">

                        <div class="guideline-icon">

                            <i class="bi bi-calendar-event"></i>

                        </div>

                        <div>

                            <h4>
                                Observe Deadlines
                            </h4>

                            <p>

                                Monitor assigned deadlines and
                                complete requirements on time.

                            </p>

                        </div>

                    </div>


                    <div class="guideline reveal">

                        <div class="guideline-icon">

                            <i class="bi bi-shield-check"></i>

                        </div>

                        <div>

                            <h4>
                                Practice Academic Integrity
                            </h4>

                            <p>

                                Submit your own work and follow
                                applicable academic policies.

                            </p>

                        </div>

                    </div>


                    <div class="guideline reveal">

                        <div class="guideline-icon">

                            <i class="bi bi-lock"></i>

                        </div>

                        <div>

                            <h4>
                                Protect Your Account
                            </h4>

                            <p>

                                Keep your login credentials private
                                and do not share your account.

                            </p>

                        </div>

                    </div>


                    <div class="guideline reveal">

                        <div class="guideline-icon">

                            <i class="bi bi-bell"></i>

                        </div>

                        <div>

                            <h4>
                                Check Regularly
                            </h4>

                            <p>

                                Regularly check ETS-Async for new
                                lectures, activities, and announcements.

                            </p>

                        </div>

                    </div>


                </div>

            </div>

        </section>


        <!-- =====================================================
             FINAL CTA
        ====================================================== -->

        <section class="final-cta">

            <div class="final-cta-content reveal">

                <div class="section-kicker">
                    ETS-Async
                </div>

                <h2>

                    Your learning space.
                    <span class="gradient-text">
                        Online.
                    </span>

                </h2>

                <p>

                    Access your asynchronous learning environment
                    through ETS-Async.

                </p>


                <a href="login.php"
                    class="btn-login">

                    <i class="bi bi-box-arrow-in-right"></i>

                    Login

                </a>

            </div>

        </section>

    </main>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <footer>

        <div class="container-main">

            <div class="footer-inner">


                <div class="footer-brand">

                    <img
                        src="./assets/pubmat/head.png"
                        alt="ETS-Async">

                    <span>
                        ETS-Async
                    </span>

                </div>


                <div class="footer-copy">

                    © <?php echo $currentYear; ?>
                    ETS-DEV.
                    Asynchronous Learning Portal.

                </div>


            </div>

        </div>

    </footer>


    <!-- =====================================================
         BOOTSTRAP JS
    ====================================================== -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        /* =====================================================
           ETS-ASYNC ANIME.JS INTERACTIONS
        ====================================================== */


        /* =====================================================
           HERO ENTRANCE ANIMATION
        ====================================================== */

        anime({
            targets: '.hero-animate',

            translateY: [35, 0],

            opacity: [0, 1],

            delay: anime.stagger(130),

            duration: 900,

            easing: 'easeOutExpo'
        });


        /* =====================================================
           3D SCENE ENTRANCE
        ====================================================== */

        anime({

            targets: '.scene-animate',

            translateY: [60, 0],

            scale: [0.92, 1],

            opacity: [0, 1],

            duration: 1200,

            delay: 350,

            easing: 'easeOutExpo'
        });


        /* =====================================================
           DASHBOARD FLOATING ANIMATION
        ====================================================== */

        anime({

            targets: '#dashboard3d',

            translateY: [

                {
                    value: -10,
                    duration: 2200
                },

                {
                    value: 0,
                    duration: 2200
                }

            ],

            direction: 'alternate',

            loop: true,

            easing: 'easeInOutSine'
        });


        /* =====================================================
           FLOATING CARD ANIMATION
        ====================================================== */

        anime({

            targets: '.floating-card-left',

            translateY: [

                {
                    value: -12,
                    duration: 1800
                },

                {
                    value: 0,
                    duration: 1800
                }

            ],

            direction: 'alternate',

            loop: true,

            easing: 'easeInOutSine'
        });


        anime({

            targets: '.floating-card-right',

            translateY: [

                {
                    value: 10,
                    duration: 2100
                },

                {
                    value: 0,
                    duration: 2100
                }

            ],

            direction: 'alternate',

            loop: true,

            easing: 'easeInOutSine'
        });


        /* =====================================================
           ORBIT ANIMATION
        ====================================================== */

        anime({

            targets: '.orbit',

            rotateZ: '+=360',

            duration: 18000,

            loop: true,

            easing: 'linear'
        });


        /* =====================================================
           ORBIT DOT PULSE
        ====================================================== */

        anime({

            targets: '.orbit-dot',

            scale: [

                {
                    value: 1
                },

                {
                    value: 1.8
                },

                {
                    value: 1
                }

            ],

            opacity: [

                {
                    value: 0.6
                },

                {
                    value: 1
                },

                {
                    value: 0.6
                }

            ],

            duration: 1800,

            delay: anime.stagger(300),

            direction: 'alternate',

            loop: true,

            easing: 'easeInOutSine'
        });


        /* =====================================================
           PROGRESS BAR ANIMATION
        ====================================================== */

        anime({

            targets: '#progressFill',

            width: ['0%', '72%'],

            duration: 1600,

            delay: 1000,

            easing: 'easeOutExpo'
        });


        /* =====================================================
           SCROLL REVEAL
        ====================================================== */

        const revealElements =
            document.querySelectorAll('.reveal');


        const revealObserver =
            new IntersectionObserver(

                entries => {

                    entries.forEach(entry => {

                        if (entry.isIntersecting) {

                            anime({

                                targets: entry.target,

                                translateY: [35, 0],

                                opacity: [0, 1],

                                duration: 800,

                                easing: 'easeOutExpo'

                            });


                            revealObserver.unobserve(
                                entry.target
                            );

                        }

                    });

                },

                {
                    threshold: 0.12
                }

            );


        revealElements.forEach(element => {

            element.style.opacity = '0';

            revealObserver.observe(element);

        });


        /* =====================================================
           MOUSE 3D PARALLAX
        ====================================================== */

        const scene =
            document.querySelector('.scene');

        const dashboard =
            document.querySelector('#dashboard3d');


        if (scene && dashboard) {

            scene.addEventListener(
                'mousemove',
                function(event) {

                    const rect =
                        scene.getBoundingClientRect();

                    const x =
                        event.clientX -
                        rect.left;

                    const y =
                        event.clientY -
                        rect.top;

                    const centerX =
                        rect.width / 2;

                    const centerY =
                        rect.height / 2;

                    const rotateY =
                        ((x - centerX) /
                            centerX) * 7;

                    const rotateX =
                        ((y - centerY) /
                            centerY) * -5;


                    anime({

                        targets: dashboard,

                        rotateY: -14 + rotateY,

                        rotateX: 7 + rotateX,

                        duration: 500,

                        easing: 'easeOutQuad'
                    });

                }
            );


            scene.addEventListener(
                'mouseleave',
                function() {

                    anime({

                        targets: dashboard,

                        rotateY: -14,

                        rotateX: 7,

                        duration: 700,

                        easing: 'easeOutElastic(1, .6)'
                    });

                }
            );

        }


        /* =====================================================
           LOGIN BUTTON MICRO INTERACTION
        ====================================================== */

        document
            .querySelectorAll('.btn-login')
            .forEach(button => {

                button.addEventListener(
                    'mouseenter',
                    function() {

                        anime({

                            targets: this.querySelector('i'),

                            translateX: 4,

                            duration: 250,

                            easing: 'easeOutQuad'
                        });

                    }
                );


                button.addEventListener(
                    'mouseleave',
                    function() {

                        anime({

                            targets: this.querySelector('i'),

                            translateX: 0,

                            duration: 250,

                            easing: 'easeOutQuad'
                        });

                    }
                );

            });


        /* =====================================================
           NAVBAR SCROLL EFFECT
        ====================================================== */

        const navbar =
            document.querySelector('.navbar-custom');


        window.addEventListener(
            'scroll',
            function() {

                if (window.scrollY > 30) {

                    navbar.style.background =
                        'rgba(13, 17, 23, 0.94)';

                } else {

                    navbar.style.background =
                        'rgba(13, 17, 23, 0.82)';

                }

            }
        );
    </script>

</body>

</html>