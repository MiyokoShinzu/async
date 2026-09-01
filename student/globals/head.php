<?php date_default_timezone_set('Asia/Manila'); ?>

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        Student Dashboard | ETS-Async
    </title>


    <!-- =====================================================
         BOOTSTRAP 5
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- =====================================================
         BOOTSTRAP ICONS
    ====================================================== -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <link rel="shortcut icon" href="../assets/pubmat/head.png" type="image/x-icon">
    <!-- =====================================================
         JQUERY
    ====================================================== -->

    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js">
    </script>


    <style>
        /* =====================================================
           VARIABLES
        ====================================================== */

        :root {

            --academic-blue: #0B4F8A;

            --academic-blue-dark: #083B66;

            --academic-blue-light: #EAF3FA;

            --border-color: #DEE2E6;

            --text-muted: #6C757D;

            --sidebar-width: 260px;

            --topbar-height: 65px;

        }


        /* =====================================================
           GENERAL
        ====================================================== */

        body {

            margin: 0;

            font-family:
                "Segoe UI",
                Tahoma,
                Geneva,
                Verdana,
                sans-serif;

            background: #F7F9FB;

            color: #212529;

        }


        /* =====================================================
           SIDEBAR
        ====================================================== */

        .sidebar {

            position: fixed;

            top: 0;

            left: 0;

            width: var(--sidebar-width);

            height: 100vh;

            background: #FFFFFF;

            border-right:
                1px solid var(--border-color);

            z-index: 1050;

            transition:
                transform 0.3s ease;

            display: flex;

            flex-direction: column;

        }


        /* =====================================================
           SIDEBAR BRAND
        ====================================================== */

        .sidebar-brand {

            height: var(--topbar-height);

            display: flex;

            align-items: center;

            padding: 0 20px;

            border-bottom:
                1px solid var(--border-color);

            flex-shrink: 0;

        }


        .brand-icon {

            width: 38px;

            height: 38px;

            border-radius: 8px;

            background:
                var(--academic-blue);

            color: #FFFFFF;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 19px;

            margin-right: 10px;

        }


        .brand-text {

            font-size: 17px;

            font-weight: 700;

            color:
                var(--academic-blue-dark);

        }


        /* =====================================================
           PROFILE
        ====================================================== */

        .sidebar-profile {

            padding: 20px;

            border-bottom:
                1px solid var(--border-color);

        }


        .profile-avatar {

            width: 50px;

            height: 50px;

            border-radius: 50%;

            background:
                var(--academic-blue-light);

            color:
                var(--academic-blue);

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: 700;

            margin-bottom: 10px;

        }


        .profile-name {

            font-size: 14px;

            font-weight: 700;

            color:
                var(--academic-blue-dark);

            word-break: break-word;

        }


        .profile-id {

            font-size: 12px;

            color:
                var(--text-muted);

            margin-top: 3px;

        }


        /* =====================================================
           MENU
        ====================================================== */

        .sidebar-menu {

            padding: 18px 12px;

            flex: 1;

            overflow-y: auto;

        }


        .menu-title {

            font-size: 10px;

            font-weight: 700;

            color: #8A8F94;

            text-transform: uppercase;

            letter-spacing: 0.6px;

            padding:
                0 12px 8px;

        }


        .sidebar-link {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                10px 12px;

            margin-bottom: 4px;

            border-radius: 7px;

            text-decoration: none;

            color: #495057;

            font-size: 14px;

            font-weight: 500;

            transition:
                background 0.2s ease,
                color 0.2s ease;

        }


        .sidebar-link i {

            width: 20px;

            text-align: center;

            font-size: 17px;

        }


        .sidebar-link:hover {

            background:
                var(--academic-blue-light);

            color:
                var(--academic-blue);

        }


        .sidebar-link.active {

            background:
                var(--academic-blue-light);

            color:
                var(--academic-blue);

            font-weight: 600;

        }


        /* =====================================================
           LOGOUT
        ====================================================== */

        .sidebar-logout {

            padding:
                12px;

            border-top:
                1px solid var(--border-color);

            flex-shrink: 0;

        }


        .logout-link {

            color: #DC3545;

        }


        .logout-link:hover {

            background: #FDECEC;

            color: #B02A37;

        }


        /* =====================================================
           TOPBAR
        ====================================================== */

        .topbar {

            position: fixed;

            top: 0;

            right: 0;

            left: var(--sidebar-width);

            height: var(--topbar-height);

            background: #FFFFFF;

            border-bottom:
                1px solid var(--border-color);

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 25px;

            z-index: 1040;

        }


        .topbar-left {

            display: flex;

            align-items: center;

        }


        .topbar-title {

            font-size: 18px;

            font-weight: 700;

            color:
                var(--academic-blue-dark);

        }


        .sidebar-toggle {

            display: none;

            border: none;

            background: transparent;

            color:
                var(--academic-blue);

            font-size: 23px;

            padding: 0;

        }


        .topbar-user {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .topbar-user-info {

            text-align: right;

        }


        .topbar-name {

            font-size: 13px;

            font-weight: 600;

        }


        .topbar-access {

            font-size: 11px;

            color:
                var(--text-muted);

        }


        .topbar-avatar {

            width: 38px;

            height: 38px;

            border-radius: 50%;

            background:
                var(--academic-blue);

            color: #FFFFFF;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 12px;

            font-weight: 700;

        }


        /* =====================================================
           MAIN CONTENT
        ====================================================== */

        .main-content {

            margin-left:
                var(--sidebar-width);

            padding-top:
                var(--topbar-height);

            min-height: 100vh;

        }


        .content-wrapper {

            padding: 25px;

        }


        /* =====================================================
           WELCOME CARD
        ====================================================== */

        .welcome-card {

            background:
                linear-gradient(135deg,
                    var(--academic-blue),
                    var(--academic-blue-dark));

            color: #FFFFFF;

            border-radius: 12px;

            padding: 25px;

            margin-bottom: 25px;

        }


        .welcome-card h2 {

            font-size: 23px;

            font-weight: 700;

            margin-bottom: 5px;

        }


        .welcome-card p {

            margin: 0;

            color: #EAF3FA;

            font-size: 14px;

        }


        /* =====================================================
           DASHBOARD CARDS
        ====================================================== */

        .dashboard-card {

            background: #FFFFFF;

            border:
                1px solid var(--border-color);

            border-radius: 10px;

            height: 100%;

            box-shadow:
                0 2px 10px rgba(0, 0, 0, 0.03);

        }


        .dashboard-card-body {

            padding: 20px;

        }


        .stat-icon {

            width: 45px;

            height: 45px;

            border-radius: 8px;

            background:
                var(--academic-blue-light);

            color:
                var(--academic-blue);

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 20px;

            margin-bottom: 12px;

        }


        .stat-label {

            font-size: 12px;

            color:
                var(--text-muted);

            margin-bottom: 4px;

        }


        .stat-value {

            font-size: 15px;

            font-weight: 700;

            color:
                var(--academic-blue-dark);

            word-break: break-word;

        }


        /* =====================================================
           SECTION HEADER
        ====================================================== */

        .section-title {

            font-size: 17px;

            font-weight: 700;

            color:
                var(--academic-blue-dark);

            margin-bottom: 15px;

        }

        /*Shoutbox*/
        /* =========================================================
   SHOUTBOX
========================================================= */

        .shoutbox-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        }


        /* =========================================================
   HEADER
========================================================= */

        .shoutbox-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 20px 24px;
            border-bottom: 1px solid #eef0f3;
        }

        .shoutbox-header-icon {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: #eef4ff;
            color: #0d6efd;
            font-size: 20px;
        }

        .shoutbox-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .shoutbox-header p {
            margin: 3px 0 0;
            color: #6c757d;
            font-size: 14px;
        }


        /* =========================================================
   CURRENT USER
========================================================= */

        .shoutbox-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 24px;
            background: #f8f9fa;
            border-bottom: 1px solid #eef0f3;
        }

        .shoutbox-user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0d6efd;
            color: #ffffff;
            font-weight: 600;
        }

        .shoutbox-user strong {
            display: block;
            font-size: 14px;
        }

        .shoutbox-user-info {
            margin-top: 2px;
            font-size: 12px;
            color: #6c757d;
        }


        /* =========================================================
   FORM
========================================================= */

        .shoutbox-form {
            padding: 20px 24px;
            border-bottom: 1px solid #eef0f3;
        }

        .shoutbox-form textarea {
            resize: vertical;
            min-height: 90px;
        }

        .shoutbox-input-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .shoutbox-input-footer small {
            color: #6c757d;
        }


        /* =========================================================
   ALERT
========================================================= */

        .shoutbox-alert {
            margin: 15px 24px 0;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
        }

        .shoutbox-alert.alert-success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .shoutbox-alert.alert-danger {
            background: #f8d7da;
            color: #842029;
        }


        /* =========================================================
   MESSAGES
========================================================= */

        .shoutbox-messages {
            padding: 10px 24px 24px;
        }

        .shout-message {
            display: flex;
            gap: 12px;
            padding: 18px 0;
            border-bottom: 1px solid #eef0f3;
        }

        .shout-message:last-child {
            border-bottom: none;
        }


        /* =========================================================
   AVATAR
========================================================= */

        .shout-avatar {
            flex: 0 0 42px;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            color: #495057;
            font-weight: 600;
            font-size: 15px;
        }


        /* =========================================================
   CONTENT
========================================================= */

        .shout-content {
            flex: 1;
            min-width: 0;
        }

        .shout-message-header {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .shout-message-header strong {
            font-size: 14px;
        }


        /* =========================================================
   YOU BADGE
========================================================= */

        .shout-you {
            font-size: 11px;
            padding: 3px 7px;
            border-radius: 20px;
            background: #e7f1ff;
            color: #0d6efd;
            font-weight: 500;
        }


        /* =========================================================
   STUDENT INFORMATION
========================================================= */

        .shout-student-info {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 3px;
            font-size: 12px;
            color: #6c757d;
        }

        .shout-student-info span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }


        /* =========================================================
   MESSAGE TEXT
========================================================= */

        .shout-text {
            margin-top: 10px;
            color: #212529;
            line-height: 1.6;
            font-size: 14px;
            word-break: break-word;
        }


        /* =========================================================
   META
========================================================= */

        .shout-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            font-size: 12px;
            color: #8a8f98;
        }


        /* =========================================================
   DELETE
========================================================= */

        .shout-delete {
            border: none;
            background: transparent;
            color: #dc3545;
            padding: 0;
            font-size: 12px;
            cursor: pointer;
        }

        .shout-delete:hover {
            text-decoration: underline;
        }


        /* =========================================================
   MY MESSAGE
========================================================= */

        .shout-message.my-message {
            background: #f8fbff;
            margin-left: -10px;
            margin-right: -10px;
            padding-left: 10px;
            padding-right: 10px;
            border-radius: 10px;
        }


        /* =========================================================
   EMPTY STATE
========================================================= */

        .shoutbox-empty {
            text-align: center;
            padding: 55px 20px;
            color: #6c757d;
        }

        .shoutbox-empty-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 15px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f3f5;
            color: #6c757d;
            font-size: 25px;
        }

        .shoutbox-empty h5 {
            color: #343a40;
            margin-bottom: 5px;
        }

        .shoutbox-empty p {
            margin: 0;
            font-size: 14px;
        }


        /* =========================================================
   MOBILE
========================================================= */

        @media (max-width: 576px) {

            .shoutbox-header,
            .shoutbox-form {
                padding: 16px;
            }

            .shoutbox-messages {
                padding-left: 16px;
                padding-right: 16px;
            }

            .shoutbox-user {
                padding: 14px 16px;
            }

            .shout-student-info {
                gap: 7px;
            }

        }

        /* =====================================================
           INFORMATION
        ====================================================== */

        .info-row {

            display: flex;

            justify-content: space-between;

            gap: 20px;

            padding: 11px 0;

            border-bottom:
                1px solid #F0F1F2;

        }


        .info-row:last-child {

            border-bottom: none;

        }


        .info-label {

            color:
                var(--text-muted);

            font-size: 13px;

        }


        .info-value {

            font-size: 13px;

            font-weight: 600;

            text-align: right;

            word-break: break-word;

        }


        /* =====================================================
           MOBILE OVERLAY
        ====================================================== */

        .sidebar-overlay {

            display: none;

            position: fixed;

            inset: 0;

            background:
                rgba(0, 0, 0, 0.35);

            z-index: 1045;

        }


        /* =====================================================
           TABLET / MOBILE
        ====================================================== */

        @media (max-width: 991px) {

            .sidebar {

                transform:
                    translateX(-100%);

            }


            .sidebar.show {

                transform:
                    translateX(0);

            }


            .sidebar-overlay.show {

                display: block;

            }


            .topbar {

                left: 0;

            }


            .main-content {

                margin-left: 0;

            }


            .sidebar-toggle {

                display: block;

                margin-right: 15px;

            }


            .content-wrapper {

                padding: 20px;

            }

        }


        /* =====================================================
           SMALL MOBILE
        ====================================================== */

        @media (max-width: 576px) {

            .topbar {

                padding:
                    0 15px;

            }


            .topbar-user-info {

                display: none;

            }


            .topbar-title {

                font-size: 16px;

            }


            .content-wrapper {

                padding: 15px;

            }


            .welcome-card {

                padding: 20px;

            }


            .welcome-card h2 {

                font-size: 20px;

            }


            .info-row {

                display: block;

            }


            .info-value {

                text-align: left;

                margin-top: 3px;

            }

        }
    </style>
    <?php
    $user = $_SESSION["user"];


    /* =========================================================
   USER DATA
   ========================================================= */

    $firstName      = $user["first_name"] ?? "";
    $lastName       = $user["last_name"] ?? "";
    $middleInitial  = $user["middle_initial"] ?? "";
    $extensionName  = $user["extension_name"] ?? "";
    $studentId      = $user["student_id"] ?? "";
    $department     = $user["department"] ?? "";
    $yearSection    = $user["year_section"] ?? "";
    $email          = $user["email"] ?? "";
    $username       = $user["username"] ?? "";
    $access         = $user["access"] ?? "student";


    /* =========================================================
   FULL NAME
   ========================================================= */

    $fullName = trim(
        $firstName . " " .
            ($middleInitial !== ""
                ? $middleInitial . ". "
                : "") .
            $lastName .
            ($extensionName !== ""
                ? " " . $extensionName
                : "")
    );


    /* =========================================================
   INITIALS
   ========================================================= */

    $initials = "";

    if ($firstName !== "") {
        $initials .= strtoupper(substr($firstName, 0, 1));
    }

    if ($lastName !== "") {
        $initials .= strtoupper(substr($lastName, 0, 1));
    }
    ?>

</head>
<style>
    .profile-photo-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 18px;
        overflow: hidden;
        max-width: 850px;
        margin: 0 auto;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
    }


    /* =========================================================
   PHOTO
========================================================= */

    .profile-photo-section {
        text-align: center;
        padding: 40px 30px 30px;
        background: linear-gradient(135deg,
                #f8f9ff,
                #ffffff);
    }


    .profile-photo-wrapper {
        width: 150px;
        height: 150px;
        margin: 0 auto 20px;
        border-radius: 50%;
        overflow: hidden;
        border: 5px solid #ffffff;
        box-shadow:
            0 8px 25px rgba(0, 0, 0, 0.12);
    }


    .profile-photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }


    .profile-photo-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 42px;
        font-weight: 700;
        background: #0d6efd;
        color: #ffffff;
    }


    .profile-photo-section h4 {
        margin-bottom: 5px;
        font-weight: 700;
    }


    .student-id {
        color: #6c757d;
        font-size: 14px;
    }


    /* =========================================================
   STUDENT INFORMATION
========================================================= */

    .student-information {
        display: grid;
        grid-template-columns:
            repeat(3, 1fr);
        border-top: 1px solid #e9ecef;
        border-bottom: 1px solid #e9ecef;
    }


    .student-info-item {
        padding: 22px 25px;
        border-right: 1px solid #e9ecef;
    }


    .student-info-item:last-child {
        border-right: none;
    }


    .student-info-label {
        display: block;
        color: #6c757d;
        font-size: 13px;
        margin-bottom: 7px;
    }


    .student-info-item strong {
        font-size: 15px;
    }


    /* =========================================================
   UPLOAD
========================================================= */

    .profile-upload-section {
        padding: 30px;
    }


    .upload-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }


    .upload-description {
        color: #6c757d;
        font-size: 14px;
        line-height: 1.6;
    }


    .upload-input-wrapper {
        margin-top: 20px;
    }


    .profile-upload-footer {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
    }


    /* =========================================================
   PREVIEW
========================================================= */

    .upload-preview-container {
        margin-top: 20px;
    }


    .preview-label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 10px;
    }


    .upload-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #ffffff;
        box-shadow:
            0 5px 20px rgba(0, 0, 0, 0.12);
    }


    /* =========================================================
   ALERT
========================================================= */

    .profile-alert {
        max-width: 850px;
        margin-left: auto;
        margin-right: auto;
    }


    /* =========================================================
   MOBILE
========================================================= */

    @media (max-width: 768px) {

        .profile-photo-card {
            border-radius: 14px;
        }


        .student-information {
            grid-template-columns: 1fr;
        }


        .student-info-item {
            border-right: none;
            border-bottom: 1px solid #e9ecef;
        }


        .student-info-item:last-child {
            border-bottom: none;
        }


        .profile-photo-section {
            padding: 30px 20px;
        }


        .profile-upload-section {
            padding: 25px 20px;
        }


        .profile-upload-footer {
            justify-content: stretch;
        }


        .profile-upload-footer .btn {
            width: 100%;
        }

    }
</style>