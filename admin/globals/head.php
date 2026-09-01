<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        Admin Dashboard | ETS-Async
    </title>


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

<link rel="shortcut icon" href="../../assets/pubmat/head.png" type="image/x-icon">
    <style>
        /* =====================================================
           GENERAL
        ====================================================== */

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family:
                "Segoe UI",
                Tahoma,
                Geneva,
                Verdana,
                sans-serif;

            background-color: #f8f9fa;

        }


        /* =====================================================
           SIDEBAR
        ====================================================== */

        .sidebar {

            position: fixed;

            top: 0;
            left: 0;

            width: 260px;

            height: 100vh;

            background-color: #ffffff;

            border-right: 1px solid #dee2e6;

            z-index: 1040;

            transition:
                transform 0.3s ease;

            overflow-y: auto;

        }


        /* =====================================================
           BRAND
        ====================================================== */

        .sidebar-brand {

            height: 70px;

            display: flex;

            align-items: center;

            padding: 0 18px;

            border-bottom:
                1px solid #dee2e6;

        }


        .brand-icon {

            width: 40px;
            height: 40px;

            display: flex;

            align-items: center;
            justify-content: center;

            background-color: #0d6efd;

            color: #ffffff;

            border-radius: 8px;

            font-size: 20px;

            margin-right: 10px;

        }


        .brand-text {

            font-size: 17px;

            font-weight: 700;

            color: #212529;

        }


        /* =====================================================
           CLOSE BUTTON
        ====================================================== */

        .sidebar-close {

            display: none;

            margin-left: auto;

            width: 36px;
            height: 36px;

            border: none;

            background-color: transparent;

            color: #6c757d;

            border-radius: 6px;

            align-items: center;

            justify-content: center;

            cursor: pointer;

            font-size: 17px;

        }


        .sidebar-close:hover {

            background-color: #f8f9fa;

            color: #212529;

        }


        /* =====================================================
           PROFILE
        ====================================================== */

        .sidebar-profile {

            padding: 20px 18px;

            border-bottom:
                1px solid #dee2e6;

        }


        .profile-avatar {

            width: 55px;
            height: 55px;

            display: flex;

            align-items: center;
            justify-content: center;

            background-color: #e7f1ff;

            color: #0d6efd;

            border-radius: 50%;

            font-size: 17px;

            font-weight: 700;

            margin-bottom: 10px;

        }


        .profile-name {

            font-size: 14px;

            font-weight: 600;

            color: #212529;

            word-break: break-word;

        }


        .profile-access {

            font-size: 12px;

            color: #6c757d;

            margin-top: 3px;

        }


        /* =====================================================
           SIDEBAR MENU
        ====================================================== */

        .sidebar-menu {

            padding: 18px 12px;

        }


        .menu-title {

            font-size: 11px;

            font-weight: 700;

            color: #6c757d;

            text-transform: uppercase;

            letter-spacing: 0.5px;

            padding:
                0 12px 8px;

        }


        .sidebar-link {

            display: flex;

            align-items: center;

            gap: 12px;

            width: 100%;

            padding:
                10px 12px;

            margin-bottom: 4px;

            border-radius: 6px;

            text-decoration: none;

            color: #495057;

            font-size: 14px;

            font-weight: 500;

            transition:
                background-color 0.2s ease,
                color 0.2s ease;

        }


        .sidebar-link i {

            width: 20px;

            text-align: center;

            font-size: 17px;

        }


        .sidebar-link:hover {

            background-color: #f8f9fa;

            color: #0d6efd;

        }


        .sidebar-link.active {

            background-color: #e7f1ff;

            color: #0d6efd;

            font-weight: 600;

        }


        /* =====================================================
           LOGOUT
        ====================================================== */

        .sidebar-logout {

            position: absolute;

            bottom: 15px;

            left: 12px;

            right: 12px;

        }


        .logout-link {

            color: #dc3545;

        }


        .logout-link:hover {

            background-color: #f8d7da;

            color: #b02a37;

        }


        /* =====================================================
           TOPBAR
        ====================================================== */

        .topbar {

            position: fixed;

            top: 0;
            right: 0;

            left: 260px;

            height: 70px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 28px;

            background-color: #ffffff;

            border-bottom:
                1px solid #dee2e6;

            z-index: 1030;

        }


        .topbar-title {

            font-size: 18px;

            font-weight: 600;

            color: #212529;

        }


        .topbar-user {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .topbar-name {

            font-size: 13px;

            font-weight: 600;

            color: #212529;

        }


        .topbar-access {

            font-size: 11px;

            color: #6c757d;

        }


        .topbar-avatar {

            width: 40px;
            height: 40px;

            display: flex;

            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background-color: #0d6efd;

            color: #ffffff;

            font-size: 13px;

            font-weight: 700;

        }


        /* =====================================================
           MOBILE SIDEBAR BUTTON
        ====================================================== */

        .sidebar-toggle {

            display: none;

            border: none;

            background: transparent;

            color: #0d6efd;

            font-size: 23px;

        }


        .sidebar-toggle:hover {

            color: #0a58ca;

        }


        /* =====================================================
           MAIN CONTENT
        ====================================================== */

        .main-content {

            margin-left: 260px;

            padding-top: 70px;

            min-height: 100vh;

        }


        .content-wrapper {

            padding: 30px;

        }


        /* =====================================================
           WELCOME
        ====================================================== */

        .welcome-card {

            background-color: #0d6efd;

            color: #ffffff;

            border-radius: 10px;

            padding: 28px;

            margin-bottom: 25px;

        }


        .welcome-card h2 {

            font-size: 24px;

            font-weight: 600;

            margin-bottom: 6px;

        }


        .welcome-card p {

            margin: 0;

            font-size: 14px;

            color: #e7f1ff;

        }


        /* =====================================================
           STAT CARDS
        ====================================================== */

        .stat-card {

            height: 100%;

            padding: 20px;

            background-color: #ffffff;

            border:
                1px solid #dee2e6;

            border-radius: 8px;

            transition:
                box-shadow 0.2s ease,
                transform 0.2s ease;

        }


        .stat-card:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 0.25rem 0.75rem rgba(0, 0, 0, 0.08);

        }


        .stat-icon {

            width: 45px;
            height: 45px;

            display: flex;

            align-items: center;
            justify-content: center;

            background-color: #e7f1ff;

            color: #0d6efd;

            border-radius: 8px;

            font-size: 20px;

            margin-bottom: 14px;

        }


        .stat-label {

            font-size: 12px;

            color: #6c757d;

            margin-bottom: 4px;

        }


        .stat-value {

            font-size: 25px;

            font-weight: 600;

            color: #212529;

        }


        /* =====================================================
           DASHBOARD CARDS
        ====================================================== */

        .dashboard-card {

            background-color: #ffffff;

            border:
                1px solid #dee2e6;

            border-radius: 8px;

            overflow: hidden;

        }


        .dashboard-card-header {

            padding: 17px 20px;

            font-size: 15px;

            font-weight: 600;

            color: #212529;

            background-color: #ffffff;

            border-bottom:
                1px solid #dee2e6;

        }


        .dashboard-card-header i {

            color: #0d6efd;

        }


        .dashboard-card-body {

            padding: 20px;

        }


        /* =====================================================
           QUICK ACTIONS
        ====================================================== */

        .quick-action {

            display: flex;

            align-items: center;

            gap: 15px;

            padding: 14px;

            margin-bottom: 10px;

            border:
                1px solid #dee2e6;

            border-radius: 7px;

            color: #212529;

            text-decoration: none;

            transition:
                background-color 0.2s ease,
                border-color 0.2s ease;

        }


        .quick-action:last-child {

            margin-bottom: 0;

        }


        .quick-action:hover {

            background-color: #f8f9fa;

            border-color: #adb5bd;

        }


        .quick-action-icon {

            width: 40px;
            height: 40px;

            display: flex;

            align-items: center;
            justify-content: center;

            flex-shrink: 0;

            background-color: #e7f1ff;

            color: #0d6efd;

            border-radius: 7px;

        }


        .quick-action-title {

            font-size: 14px;

            font-weight: 600;

        }


        .quick-action-text {

            font-size: 12px;

            color: #6c757d;

            margin-top: 2px;

        }


        /* =====================================================
           ADMIN INFORMATION
        ====================================================== */

        .information-item {

            margin-bottom: 18px;

        }


        .information-label {

            font-size: 12px;

            color: #6c757d;

            margin-bottom: 4px;

        }


        .information-value {

            font-size: 14px;

            font-weight: 600;

            color: #212529;

            word-break: break-word;

        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media (max-width: 991px) {

            .sidebar {

                transform:
                    translateX(-100%);

            }


            .sidebar.show {

                transform:
                    translateX(0);

                box-shadow:
                    5px 0 25px rgba(0, 0, 0, 0.15);

            }


            .sidebar-close {

                display: flex;

            }


            .topbar {

                left: 0;

            }


            .main-content {

                margin-left: 0;

            }


            .sidebar-toggle {

                display: block;

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

                padding: 0 15px;

            }


            .topbar-name,
            .topbar-access {

                display: none;

            }


            .content-wrapper {

                padding: 15px;

            }


            .welcome-card {

                padding: 22px;

            }


            .welcome-card h2 {

                font-size: 20px;

            }

        }
    </style>

</head>