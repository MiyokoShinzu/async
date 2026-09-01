<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>
        Student Dashboard | ETS-Async
    </title>


    <!-- =====================================================
         APPLY SAVED THEME BEFORE PAGE LOAD
    ====================================================== -->

    <script>
        (function() {

            const savedTheme =
                localStorage.getItem("ets-theme");

            document.documentElement.setAttribute(
                "data-theme",
                savedTheme === "dark" ? "dark" : "light"
            );

        })();
    </script>


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


    <!-- =====================================================
         FAVICON
    ====================================================== -->

    <link
        rel="shortcut icon"
        href="../assets/pubmat/head.png"
        type="image/x-icon">


    <!-- =====================================================
         JQUERY
    ====================================================== -->

    <script
        src="https://code.jquery.com/jquery-3.7.1.min.js">
    </script>


    <!-- =====================================================
         GLOBAL USER DATA
    ====================================================== -->

    <?php

    $user = $_SESSION["user"] ?? [];


    /* =====================================================
       BASIC USER INFORMATION
    ====================================================== */

    $firstName =
        trim($user["first_name"] ?? "");

    $lastName =
        trim($user["last_name"] ?? "");

    $middleInitial =
        trim($user["middle_initial"] ?? "");

    $extensionName =
        trim($user["extension_name"] ?? "");

    $studentId =
        trim($user["student_id"] ?? "");

    $department =
        trim($user["department"] ?? "");

    $yearSection =
        trim($user["year_section"] ?? "");

    $email =
        trim($user["email"] ?? "");

    $username =
        trim($user["username"] ?? "");

    $access =
        trim($user["access"] ?? "student");

    $profilePhoto =
        trim($user["profile_photo"] ?? "");


    /* =====================================================
       FULL NAME
    ====================================================== */

    $fullName = trim(

        $firstName . " " .

            (
                $middleInitial !== ""
                ? $middleInitial . ". "
                : ""
            ) .

            $lastName .

            (
                $extensionName !== ""
                ? " " . $extensionName
                : ""
            )

    );


    /* =====================================================
       FALLBACK FULL NAME
    ====================================================== */

    if ($fullName === "") {

        $fullName = "Student";
    }


    /* =====================================================
       INITIALS
    ====================================================== */

    $initials = "";

    if ($firstName !== "") {

        $initials .= strtoupper(
            substr(
                $firstName,
                0,
                1
            )
        );
    }

    if ($lastName !== "") {

        $initials .= strtoupper(
            substr(
                $lastName,
                0,
                1
            )
        );
    }

    if ($initials === "") {

        $initials = "ST";
    }


    /* =====================================================
       PROFILE PHOTO URL
    ====================================================== */

    $profilePhotoUrl = "";

    if ($profilePhoto !== "") {

        $profilePhotoUrl =
            htmlspecialchars(
                $profilePhoto,
                ENT_QUOTES,
                "UTF-8"
            );
    }

    ?>


    <style>
        /* =====================================================
           ETS-ASYNC GLOBAL VARIABLES
        ====================================================== */

        :root {

            --academic-blue: #0B4F8A;

            --academic-blue-dark: #083B66;

            --academic-blue-light: #EAF3FA;

            --bg-color: #F7F9FB;

            --surface-color: #FFFFFF;

            --surface-secondary: #F8F9FA;

            --text-color: #212529;

            --text-secondary: #6C757D;

            --border-color: #DEE2E6;

            --border-light: #F0F1F2;

            --sidebar-bg: #FFFFFF;

            --sidebar-text: #495057;

            --sidebar-hover: #EAF3FA;

            --topbar-bg: #FFFFFF;

            --input-bg: #FFFFFF;

            --shoutbox-bg: #FFFFFF;

            --shoutbox-secondary: #F8F9FA;

            --shoutbox-border: #EEF0F3;

            --shadow-color:
                rgba(0, 0, 0, 0.04);


            /* =================================================
               ACTIVITIES VARIABLES
            ================================================= */

            --activity-card-bg: #FFFFFF;

            --activity-header-bg: #FFFFFF;

            --activity-hover-bg: #FAFBFF;

            --activity-number-bg: #F3F4F6;

            --activity-number-text: #6B7280;

            --activity-muted: #6B7280;

            --activity-border: #E5E7EB;

            --activity-border-light: #F0F0F0;

            --activity-icon-blue-bg: #EAF2FF;

            --activity-icon-blue: #2563EB;

            --activity-icon-gray-bg: #F3F4F6;

            --activity-icon-gray: #6B7280;

            --activity-icon-orange-bg: #FFF4E5;

            --activity-icon-orange: #F59E0B;

            --activity-icon-green-bg: #EAFAF1;

            --activity-icon-green: #16A34A;

            --activity-indigo-bg: #EEF2FF;

            --activity-indigo: #4F46E5;

            --activity-progress-bg: #E5E7EB;

            --activity-inprogress: #D97706;

            --activity-completed: #16A34A;

            --activity-notstarted: #6B7280;

            --activity-video-bg: #111827;

            --activity-video-header: #FAFAFA;

            --activity-error-text: #D1D5DB;

            --activity-error-muted: #9CA3AF;

            --activity-error-warning: #F59E0B;


            --sidebar-width: 260px;

            --topbar-height: 65px;

        }


        /* =====================================================
           DARK THEME VARIABLES
        ====================================================== */

        [data-theme="dark"] {

            --academic-blue: #3B82F6;

            --academic-blue-dark: #60A5FA;

            --academic-blue-light: #1E3A5F;

            --bg-color: #111827;

            --surface-color: #1F2937;

            --surface-secondary: #273244;

            --text-color: #F3F4F6;

            --text-secondary: #9CA3AF;

            --border-color: #374151;

            --border-light: #374151;

            --sidebar-bg: #172033;

            --sidebar-text: #D1D5DB;

            --sidebar-hover: #273244;

            --topbar-bg: #172033;

            --input-bg: #1F2937;

            --shoutbox-bg: #1F2937;

            --shoutbox-secondary: #273244;

            --shoutbox-border: #374151;

            --shadow-color:
                rgba(0, 0, 0, 0.30);


            /* =================================================
               ACTIVITIES DARK THEME
            ================================================= */

            --activity-card-bg: #1F2937;

            --activity-header-bg: #1F2937;

            --activity-hover-bg: #273244;

            --activity-number-bg: #374151;

            --activity-number-text: #9CA3AF;

            --activity-muted: #9CA3AF;

            --activity-border: #374151;

            --activity-border-light: #374151;

            --activity-icon-blue-bg: #1E3A5F;

            --activity-icon-blue: #60A5FA;

            --activity-icon-gray-bg: #374151;

            --activity-icon-gray: #9CA3AF;

            --activity-icon-orange-bg: #4A3418;

            --activity-icon-orange: #FBBF24;

            --activity-icon-green-bg: #163A2A;

            --activity-icon-green: #4ADE80;

            --activity-indigo-bg: #312E81;

            --activity-indigo: #A5B4FC;

            --activity-progress-bg: #374151;

            --activity-inprogress: #FBBF24;

            --activity-completed: #4ADE80;

            --activity-notstarted: #9CA3AF;

            --activity-video-bg: #0B1120;

            --activity-video-header: #172033;

            --activity-error-text: #D1D5DB;

            --activity-error-muted: #9CA3AF;

            --activity-error-warning: #FBBF24;

        }


        /* =====================================================
           GLOBAL
        ====================================================== */

        html {

            color-scheme: light;

        }


        html[data-theme="dark"] {

            color-scheme: dark;

        }


        body {

            margin: 0;

            font-family:
                "Segoe UI",
                Tahoma,
                Geneva,
                Verdana,
                sans-serif;

            background:
                var(--bg-color);

            color:
                var(--text-color);

            transition:
                background-color 0.25s ease,
                color 0.25s ease;

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

            background:
                var(--sidebar-bg);

            color:
                var(--sidebar-text);

            border-right:
                1px solid var(--border-color);

            z-index: 1050;

            transition:
                transform 0.3s ease,
                background-color 0.25s ease,
                border-color 0.25s ease;

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
           SIDEBAR PROFILE
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

            overflow: hidden;

        }


        .profile-avatar img {

            width: 100%;

            height: 100%;

            object-fit: cover;

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
                var(--text-secondary);

            margin-top: 3px;

        }


        /* =====================================================
           SIDEBAR MENU
        ====================================================== */

        .sidebar-menu {

            padding: 18px 12px;

            flex: 1;

            overflow-y: auto;

        }


        .menu-title {

            font-size: 10px;

            font-weight: 700;

            color:
                var(--text-secondary);

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

            color:
                var(--sidebar-text);

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

            background:
                var(--sidebar-hover);

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

            background:
                rgba(220, 53, 69, 0.10);

            color:
                #B02A37;

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

            background:
                var(--topbar-bg);

            color:
                var(--text-color);

            border-bottom:
                1px solid var(--border-color);

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding:
                0 25px;

            z-index: 1040;

            transition:
                background-color 0.25s ease,
                border-color 0.25s ease,
                color 0.25s ease;

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

            background:
                transparent;

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

            color:
                var(--text-color);

        }


        .topbar-access {

            font-size: 11px;

            color:
                var(--text-secondary);

        }


        .topbar-avatar {

            width: 38px;

            height: 38px;

            border-radius: 50%;

            background:
                var(--academic-blue);

            color:
                #FFFFFF;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 12px;

            font-weight: 700;

            overflow: hidden;

            flex-shrink: 0;

        }


        .topbar-avatar img {

            width: 100%;

            height: 100%;

            object-fit: cover;

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

            background:
                var(--bg-color);

            color:
                var(--text-color);

        }


        .content-wrapper {

            padding: 25px;

            color:
                var(--text-color);

        }


        /* =====================================================
           WELCOME CARD
        ====================================================== */

        .welcome-card {

            background:
                linear-gradient(135deg,
                    #0B4F8A,
                    #083B66);

            color:
                #FFFFFF;

            border-radius:
                12px;

            padding:
                25px;

            margin-bottom:
                25px;

            border: none;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.10);

        }


        .welcome-card h2 {

            font-size: 23px;

            font-weight: 700;

            margin-bottom: 5px;

            color:
                #FFFFFF;

        }


        .welcome-card p {

            margin: 0;

            color:
                #EAF3FA;

            font-size: 14px;

        }


        /* =====================================================
           DASHBOARD CARDS
        ====================================================== */

        .dashboard-card {

            background:
                var(--surface-color);

            color:
                var(--text-color);

            border:
                1px solid var(--border-color);

            border-radius:
                10px;

            height: 100%;

            box-shadow:
                0 4px 15px var(--shadow-color);

            transition:
                background-color 0.25s ease,
                border-color 0.25s ease,
                color 0.25s ease,
                box-shadow 0.25s ease;

        }


        .dashboard-card-body {

            padding: 20px;

            color:
                var(--text-color);

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
                var(--text-secondary);

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
           SECTION TITLE
        ====================================================== */

        .section-title {

            font-size: 17px;

            font-weight: 700;

            color:
                var(--academic-blue-dark);

            margin-bottom: 15px;

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
                1px solid var(--border-light);

        }


        .info-row:last-child {

            border-bottom: none;

        }


        .info-label {

            color:
                var(--text-secondary);

            font-size: 13px;

        }


        .info-value {

            font-size: 13px;

            font-weight: 600;

            text-align: right;

            word-break: break-word;

            color:
                var(--text-color);

        }


        /* =====================================================
           SHOUTBOX
        ====================================================== */

        .shoutbox-card {

            background:
                var(--shoutbox-bg);

            color:
                var(--text-color);

            border:
                1px solid var(--border-color);

            border-radius:
                16px;

            overflow: hidden;

            box-shadow:
                0 4px 18px var(--shadow-color);

        }


        .shoutbox-header {

            display: flex;

            align-items: center;

            gap: 14px;

            padding:
                20px 24px;

            border-bottom:
                1px solid var(--shoutbox-border);

        }


        .shoutbox-header-icon {

            width: 46px;

            height: 46px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 12px;

            background:
                var(--academic-blue-light);

            color:
                var(--academic-blue);

            font-size: 20px;

        }


        .shoutbox-header h5 {

            margin: 0;

            font-weight: 600;

            color:
                var(--text-color);

        }


        .shoutbox-header p {

            margin:
                3px 0 0;

            color:
                var(--text-secondary);

            font-size: 14px;

        }


        .shoutbox-user {

            display: flex;

            align-items: center;

            gap: 12px;

            padding:
                18px 24px;

            background:
                var(--shoutbox-secondary);

            border-bottom:
                1px solid var(--shoutbox-border);

        }


        .shoutbox-user-avatar {

            width: 42px;

            height: 42px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                var(--academic-blue);

            color:
                #FFFFFF;

            font-weight: 600;

            overflow: hidden;

            flex-shrink: 0;

        }


        .shoutbox-user-avatar img {

            width: 100%;

            height: 100%;

            object-fit: cover;

        }


        .shoutbox-user strong {

            display: block;

            font-size: 14px;

            color:
                var(--text-color);

        }


        .shoutbox-user-info {

            margin-top: 2px;

            font-size: 12px;

            color:
                var(--text-secondary);

        }


        .shoutbox-form {

            padding:
                20px 24px;

            border-bottom:
                1px solid var(--shoutbox-border);

        }


        .shoutbox-form textarea {

            resize: vertical;

            min-height: 90px;

            background:
                var(--input-bg);

            color:
                var(--text-color);

            border-color:
                var(--border-color);

        }


        .shoutbox-form textarea::placeholder {

            color:
                var(--text-secondary);

        }


        .shoutbox-form textarea:focus {

            background:
                var(--input-bg);

            color:
                var(--text-color);

            border-color:
                var(--academic-blue);

            box-shadow:
                0 0 0 0.2rem rgba(59, 130, 246, 0.15);

        }


        .shoutbox-input-footer {

            display: flex;

            justify-content:
                space-between;

            align-items: center;

            margin-top: 10px;

        }


        .shoutbox-input-footer small {

            color:
                var(--text-secondary);

        }


        .shoutbox-alert {

            margin:
                15px 24px 0;

            padding:
                10px 14px;

            border-radius:
                8px;

            font-size: 14px;

        }


        .shoutbox-alert.alert-success {

            background:
                #D1E7DD;

            color:
                #0F5132;

        }


        .shoutbox-alert.alert-danger {

            background:
                #F8D7DA;

            color:
                #842029;

        }


        .shoutbox-messages {

            padding:
                10px 24px 24px;

        }


        .shout-message {

            display: flex;

            gap: 12px;

            padding:
                18px 0;

            border-bottom:
                1px solid var(--shoutbox-border);

        }


        .shout-message:last-child {

            border-bottom:
                none;

        }


        .shout-avatar {

            flex:
                0 0 42px;

            width: 42px;

            height: 42px;

            border-radius:
                50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                var(--surface-secondary);

            color:
                var(--text-color);

            font-weight: 600;

            font-size: 15px;

            overflow: hidden;

        }


        .shout-avatar img {

            width: 100%;

            height: 100%;

            object-fit: cover;

        }


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

            color:
                var(--text-color);

        }


        .shout-you {

            font-size: 11px;

            padding:
                3px 7px;

            border-radius:
                20px;

            background:
                var(--academic-blue-light);

            color:
                var(--academic-blue);

            font-weight: 500;

        }


        .shout-student-info {

            display: flex;

            flex-wrap: wrap;

            gap: 12px;

            margin-top: 3px;

            font-size: 12px;

            color:
                var(--text-secondary);

        }


        .shout-student-info span {

            display: inline-flex;

            align-items: center;

            gap: 4px;

        }


        .shout-text {

            margin-top: 10px;

            color:
                var(--text-color);

            line-height: 1.6;

            font-size: 14px;

            word-break: break-word;

        }


        .shout-meta {

            display: flex;

            justify-content:
                space-between;

            align-items: center;

            margin-top: 8px;

            font-size: 12px;

            color:
                var(--text-secondary);

        }


        .shout-delete {

            border: none;

            background:
                transparent;

            color:
                #DC3545;

            padding: 0;

            font-size: 12px;

            cursor: pointer;

        }


        .shout-delete:hover {

            text-decoration:
                underline;

        }


        .shout-message.my-message {

            background:
                var(--academic-blue-light);

            margin-left:
                -10px;

            margin-right:
                -10px;

            padding-left:
                10px;

            padding-right:
                10px;

            border-radius:
                10px;

        }


        .shoutbox-empty {

            text-align:
                center;

            padding:
                55px 20px;

            color:
                var(--text-secondary);

        }


        .shoutbox-empty-icon {

            width: 64px;

            height: 64px;

            margin:
                0 auto 15px;

            border-radius:
                50%;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                var(--surface-secondary);

            color:
                var(--text-secondary);

            font-size: 25px;

        }


        .shoutbox-empty h5 {

            color:
                var(--text-color);

            margin-bottom:
                5px;

        }


        .shoutbox-empty p {

            margin: 0;

            font-size: 14px;

        }


        /* =====================================================
           FORMS
        ====================================================== */

        .form-control,
        .form-select {

            background-color:
                var(--input-bg);

            color:
                var(--text-color);

            border-color:
                var(--border-color);

        }


        .form-control::placeholder {

            color:
                var(--text-secondary);

        }


        .form-control:focus,
        .form-select:focus {

            background-color:
                var(--input-bg);

            color:
                var(--text-color);

            border-color:
                var(--academic-blue);

            box-shadow:
                0 0 0 0.2rem rgba(59, 130, 246, 0.15);

        }


        /* =====================================================
           TABLES
        ====================================================== */

        .table {

            color:
                var(--text-color);

            --bs-table-bg:
                var(--surface-color);

            --bs-table-color:
                var(--text-color);

            --bs-table-border-color:
                var(--border-color);

        }


        /* =====================================================
           MODALS
        ====================================================== */

        .modal-content {

            background:
                var(--surface-color);

            color:
                var(--text-color);

            border-color:
                var(--border-color);

        }


        .modal-header,
        .modal-footer {

            border-color:
                var(--border-color);

        }


        /* =====================================================
           DROPDOWNS
        ====================================================== */

        .dropdown-menu {

            background:
                var(--surface-color);

            border-color:
                var(--border-color);

        }


        .dropdown-item {

            color:
                var(--text-color);

        }


        .dropdown-item:hover {

            background:
                var(--sidebar-hover);

            color:
                var(--text-color);

        }


        /* =====================================================
           THEME TOGGLE
        ====================================================== */

        .btn-theme {

            width: 38px;

            height: 38px;

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 0;

            background:
                var(--surface-secondary);

            color:
                var(--text-color);

            border:
                1px solid var(--border-color);

            border-radius: 8px;

            cursor: pointer;

            transition:
                all 0.2s ease;

        }


        .btn-theme:hover {

            background:
                var(--sidebar-hover);

            color:
                var(--academic-blue);

            border-color:
                var(--academic-blue);

        }


        /* =====================================================
           PROFILE PHOTO CARD
        ====================================================== */

        .profile-photo-card {

            background:
                var(--surface-color);

            border:
                1px solid var(--border-color);

            border-radius:
                18px;

            overflow: hidden;

            max-width:
                850px;

            margin:
                0 auto;

            box-shadow:
                0 8px 30px var(--shadow-color);

        }


        .profile-photo-section {

            text-align: center;

            padding:
                40px 30px 30px;

            background:
                var(--surface-secondary);

        }


        .profile-photo-wrapper {

            width: 150px;

            height: 150px;

            margin:
                0 auto 20px;

            border-radius: 50%;

            overflow: hidden;

            border:
                5px solid var(--surface-color);

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

            background:
                var(--academic-blue);

            color:
                #FFFFFF;

        }


        .profile-photo-section h4 {

            margin-bottom:
                5px;

            font-weight:
                700;

            color:
                var(--text-color);

        }


        .student-id {

            color:
                var(--text-secondary);

            font-size:
                14px;

        }


        .student-information {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            border-top:
                1px solid var(--border-color);

            border-bottom:
                1px solid var(--border-color);

        }


        .student-info-item {

            padding:
                22px 25px;

            border-right:
                1px solid var(--border-color);

        }


        .student-info-item:last-child {

            border-right:
                none;

        }


        .student-info-label {

            display: block;

            color:
                var(--text-secondary);

            font-size:
                13px;

            margin-bottom:
                7px;

        }


        .student-info-item strong {

            font-size:
                15px;

            color:
                var(--text-color);

        }


        .profile-upload-section {

            padding:
                30px;

        }


        .upload-title {

            font-size:
                18px;

            font-weight:
                700;

            margin-bottom:
                8px;

            color:
                var(--text-color);

        }


        .upload-description {

            color:
                var(--text-secondary);

            font-size:
                14px;

            line-height:
                1.6;

        }


        .upload-input-wrapper {

            margin-top:
                20px;

        }


        .profile-upload-footer {

            margin-top:
                20px;

            display:
                flex;

            justify-content:
                flex-end;

        }


        .upload-preview-container {

            margin-top:
                20px;

        }


        .preview-label {

            font-size:
                14px;

            font-weight:
                600;

            margin-bottom:
                10px;

            color:
                var(--text-color);

        }


        .upload-preview {

            width:
                120px;

            height:
                120px;

            object-fit:
                cover;

            border-radius:
                50%;

            border:
                4px solid var(--surface-color);

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.12);

        }


        .profile-alert {

            max-width:
                850px;

            margin-left:
                auto;

            margin-right:
                auto;

        }


        /* =====================================================
           DARK THEME - BOOTSTRAP TEXT OVERRIDES
        ====================================================== */

        [data-theme="dark"] .text-dark {

            color:
                var(--text-color) !important;

        }


        [data-theme="dark"] .text-muted {

            color:
                var(--text-secondary) !important;

        }


        [data-theme="dark"] .bg-white {

            background-color:
                var(--surface-color) !important;

        }


        [data-theme="dark"] .bg-light {

            background-color:
                var(--surface-secondary) !important;

        }


        [data-theme="dark"] .table-light {

            --bs-table-bg:
                var(--surface-secondary);

            --bs-table-color:
                var(--text-color);

        }


        [data-theme="dark"] .table-hover>tbody>tr:hover {

            --bs-table-hover-bg:
                var(--surface-secondary);

            --bs-table-hover-color:
                var(--text-color);

        }


        /* =====================================================
           STUDENT ACTIVITIES PAGE
        ====================================================== */

        .page-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 24px;

        }


        .page-header h2 {

            margin: 0;

            font-weight: 700;

            color:
                var(--text-color);

        }


        .page-header p {

            margin: 6px 0 0;

            color:
                var(--text-secondary);

            font-size: 14px;

        }


        /* =====================================================
           STATISTICS CARDS
        ====================================================== */

        .student-stat-card {

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                14px;

            padding:
                20px;

            display:
                flex;

            align-items:
                center;

            gap:
                15px;

            height:
                100%;

            box-shadow:
                0 4px 12px var(--shadow-color);

            transition:
                all 0.2s ease;

        }


        .student-stat-card:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 8px 20px var(--shadow-color);

        }


        .student-stat-icon {

            width:
                50px;

            height:
                50px;

            min-width:
                50px;

            border-radius:
                12px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                21px;

        }


        .student-stat-icon.blue {

            background:
                var(--activity-icon-blue-bg);

            color:
                var(--activity-icon-blue);

        }


        .student-stat-icon.gray {

            background:
                var(--activity-icon-gray-bg);

            color:
                var(--activity-icon-gray);

        }


        .student-stat-icon.orange {

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-icon-orange);

        }


        .student-stat-icon.green {

            background:
                var(--activity-icon-green-bg);

            color:
                var(--activity-icon-green);

        }


        .student-stat-card span {

            display:
                block;

            color:
                var(--activity-muted);

            font-size:
                13px;

            margin-bottom:
                3px;

        }


        .student-stat-card strong {

            display:
                block;

            color:
                var(--text-color);

            font-size:
                24px;

            line-height:
                1.2;

        }


        .student-stat-card strong small {

            font-size:
                13px;

            color:
                var(--activity-completed);

            font-weight:
                600;

        }


        /* =====================================================
           OVERALL LEARNING PROGRESS
        ====================================================== */

        .learning-progress-card {

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                14px;

            padding:
                22px 24px;

            box-shadow:
                0 4px 12px var(--shadow-color);

        }


        .learning-progress-header {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                flex-start;

            margin-bottom:
                16px;

        }


        .learning-progress-header h5 {

            margin:
                0;

            font-weight:
                700;

            color:
                var(--text-color);

        }


        .learning-progress-header p {

            margin:
                5px 0 0;

            color:
                var(--text-secondary);

            font-size:
                13px;

        }


        .learning-progress-header strong {

            font-size:
                24px;

            color:
                var(--activity-indigo);

        }


        .learning-progress {

            height:
                10px;

            border-radius:
                20px;

            background:
                var(--activity-progress-bg);

            overflow:
                hidden;

        }


        .learning-progress .progress-bar {

            border-radius:
                20px;

            background:
                linear-gradient(90deg,
                    #2563EB,
                    #4F46E5);

            transition:
                width 0.5s ease;

        }


        .learning-progress-footer {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

            margin-top:
                10px;

            color:
                var(--text-secondary);

            font-size:
                13px;

        }


        /* =====================================================
           ACTIVITY CARD
        ====================================================== */

        .activity-card {

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                14px;

            overflow:
                hidden;

            box-shadow:
                0 4px 12px var(--shadow-color);

        }


        .activity-card-header {

            padding:
                22px 24px;

            border-bottom:
                1px solid var(--activity-border);

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                center;

        }


        .activity-card-header h5 {

            margin:
                0;

            font-weight:
                700;

            color:
                var(--text-color);

        }


        .activity-card-header p {

            margin:
                5px 0 0;

            color:
                var(--text-secondary);

            font-size:
                13px;

        }


        .activity-count {

            background:
                var(--activity-indigo-bg);

            color:
                var(--activity-indigo);

            padding:
                6px 12px;

            border-radius:
                20px;

            font-size:
                12px;

            font-weight:
                600;

        }


        /* =====================================================
           ACTIVITY LIST
        ====================================================== */

        .activity-list {

            display:
                flex;

            flex-direction:
                column;

        }


        .activity-item {

            display:
                grid;

            grid-template-columns:
                36px 48px minmax(250px, 1fr) 130px 130px;

            align-items:
                center;

            gap:
                15px;

            padding:
                20px 24px;

            border-bottom:
                1px solid var(--activity-border-light);

            transition:
                background 0.2s ease;

        }


        .activity-item:last-child {

            border-bottom:
                none;

        }


        .activity-item:hover {

            background:
                var(--activity-hover-bg);

        }


        /* =====================================================
           ACTIVITY NUMBER
        ====================================================== */

        .activity-number {

            width:
                32px;

            height:
                32px;

            border-radius:
                50%;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                var(--activity-number-bg);

            color:
                var(--activity-number-text);

            font-size:
                13px;

            font-weight:
                700;

        }


        /* =====================================================
           ACTIVITY ICON
        ====================================================== */

        .activity-icon {

            width:
                44px;

            height:
                44px;

            border-radius:
                12px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                19px;

        }


        .activity-icon.completed {

            background:
                var(--activity-icon-green-bg);

            color:
                var(--activity-icon-green);

        }


        .activity-icon.in-progress {

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-icon-orange);

        }


        .activity-icon.not-started {

            background:
                var(--activity-icon-gray-bg);

            color:
                var(--activity-icon-gray);

        }


        /* =====================================================
           ACTIVITY INFORMATION
        ====================================================== */

        .activity-information {

            min-width:
                0;

        }


        .activity-information h5 {

            margin:
                0 0 7px;

            color:
                var(--text-color);

            font-size:
                15px;

            font-weight:
                700;

            overflow:
                hidden;

            text-overflow:
                ellipsis;

            white-space:
                nowrap;

        }


        /* =====================================================
           ACTIVITY META
        ====================================================== */

        .activity-meta {

            display:
                flex;

            flex-wrap:
                wrap;

            gap:
                15px;

        }


        .activity-meta span {

            color:
                var(--text-secondary);

            font-size:
                12px;

        }


        .activity-meta i {

            margin-right:
                4px;

            color:
                var(--activity-muted);

        }


        /* =====================================================
           INDIVIDUAL PROGRESS
        ====================================================== */

        .lecture-progress-wrapper {

            display:
                flex;

            align-items:
                center;

            gap:
                10px;

            margin-top:
                10px;

        }


        .lecture-progress-bar {

            width:
                150px;

            max-width:
                100%;

            height:
                6px;

            background:
                var(--activity-progress-bg);

            border-radius:
                20px;

            overflow:
                hidden;

        }


        .lecture-progress-fill {

            height:
                100%;

            background:
                var(--activity-icon-orange);

            border-radius:
                20px;

            transition:
                width 0.4s ease;

        }


        .lecture-progress-fill.completed {

            background:
                var(--activity-completed);

        }


        .lecture-progress-wrapper span {

            min-width:
                35px;

            color:
                var(--text-secondary);

            font-size:
                12px;

            font-weight:
                600;

        }


        /* =====================================================
           STATUS
        ====================================================== */

        .activity-status {

            display:
                flex;

            flex-direction:
                column;

            align-items:
                flex-start;

            gap:
                6px;

        }


        .activity-status-badge {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                5px;

            padding:
                6px 10px;

            border-radius:
                20px;

            font-size:
                11px;

            font-weight:
                600;

            white-space:
                nowrap;

        }


        .activity-status-badge.completed {

            background:
                var(--activity-icon-green-bg);

            color:
                var(--activity-completed);

        }


        .activity-status-badge.in-progress {

            background:
                var(--activity-icon-orange-bg);

            color:
                var(--activity-inprogress);

        }


        .activity-status-badge.not-started {

            background:
                var(--activity-icon-gray-bg);

            color:
                var(--activity-notstarted);

        }


        .activity-status small {

            color:
                var(--text-secondary);

            font-size:
                11px;

        }


        /* =====================================================
           ACTION BUTTON
        ====================================================== */

        .activity-action {

            display:
                flex;

            justify-content:
                flex-end;

        }


        .activity-action .btn {

            border-radius:
                8px;

            padding:
                8px 14px;

            font-size:
                13px;

            font-weight:
                600;

            white-space:
                nowrap;

        }


        .activity-action .btn-primary {

            background:
                #2563EB;

            border-color:
                #2563EB;

        }


        .activity-action .btn-primary:hover {

            background:
                #1D4ED8;

            border-color:
                #1D4ED8;

        }


        /* =====================================================
           EMPTY STATE
        ====================================================== */

        .activity-empty {

            padding:
                60px 20px;

            text-align:
                center;

        }


        .activity-empty-icon {

            width:
                70px;

            height:
                70px;

            margin:
                0 auto 15px;

            border-radius:
                50%;

            background:
                var(--activity-icon-gray-bg);

            color:
                var(--activity-muted);

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            font-size:
                28px;

        }


        .activity-empty h5 {

            margin-bottom:
                7px;

            color:
                var(--text-color);

            font-weight:
                700;

        }


        .activity-empty p {

            margin:
                0;

            color:
                var(--text-secondary);

            font-size:
                14px;

        }


        /* =====================================================
           STUDENT ACTIVITY VIEW
        ====================================================== */

        .back-link {

            display:
                inline-flex;

            align-items:
                center;

            text-decoration:
                none;

            color:
                var(--activity-indigo);

            font-size:
                14px;

            font-weight:
                500;

            transition:
                all 0.2s ease;

        }


        .back-link:hover {

            color:
                var(--academic-blue);

            transform:
                translateX(-2px);

        }


        /* =====================================================
           ACTIVITY VIEW HEADER
        ====================================================== */

        .activity-view-header {

            display:
                flex;

            justify-content:
                space-between;

            align-items:
                flex-start;

            gap:
                25px;

            background:
                var(--activity-header-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                16px;

            padding:
                25px 28px;

            margin-bottom:
                18px;

            box-shadow:
                0 2px 8px var(--shadow-color);

        }


        .activity-view-label {

            display:
                inline-block;

            font-size:
                11px;

            font-weight:
                700;

            letter-spacing:
                0.8px;

            color:
                var(--activity-indigo);

            background:
                var(--activity-indigo-bg);

            border-radius:
                6px;

            padding:
                5px 9px;

            margin-bottom:
                10px;

        }


        .activity-view-header h2 {

            margin:
                0;

            color:
                var(--text-color);

            font-size:
                26px;

            font-weight:
                700;

            line-height:
                1.35;

        }


        .activity-view-header p {

            margin:
                10px 0 0;

            color:
                var(--text-secondary);

            font-size:
                14px;

            line-height:
                1.7;

            max-width:
                850px;

        }


        /* =====================================================
           ACTIVITY VIEW STATUS
        ====================================================== */

        .activity-view-header .activity-status-badge {

            display:
                inline-flex;

            align-items:
                center;

            gap:
                6px;

            white-space:
                nowrap;

            padding:
                8px 13px;

            border-radius:
                999px;

            font-size:
                13px;

            font-weight:
                600;

        }


        .activity-view-header .activity-status-badge.in-progress {

            color:
                var(--activity-inprogress);

            background:
                var(--activity-icon-orange-bg);

            border:
                1px solid rgba(245, 158, 11, 0.30);

        }


        .activity-view-header .activity-status-badge.completed {

            color:
                var(--activity-completed);

            background:
                var(--activity-icon-green-bg);

            border:
                1px solid rgba(34, 197, 94, 0.30);

        }


        /* =====================================================
           PROGRESS CARD
        ====================================================== */

        .activity-progress-card {

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                16px;

            padding:
                20px 24px;

            margin-bottom:
                18px;

            box-shadow:
                0 2px 8px var(--shadow-color);

        }


        .activity-progress-header {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            margin-bottom:
                10px;

            font-size:
                14px;

            color:
                var(--text-color);

            font-weight:
                500;

        }


        .activity-progress-header strong {

            color:
                var(--activity-indigo);

            font-size:
                15px;

        }


        .activity-progress {

            height:
                9px;

            overflow:
                hidden;

            background:
                var(--activity-progress-bg);

            border-radius:
                999px;

        }


        .activity-progress .progress-bar {

            border-radius:
                999px;

            transition:
                width 0.4s ease,
                background-color 0.3s ease;

        }


        .activity-progress .progress-bar.in-progress {

            background:
                #6366F1;

        }


        .activity-progress .progress-bar.completed {

            background:
                #22C55E;

        }


        .progress-message,
        .completed-message {

            display:
                flex;

            align-items:
                center;

            gap:
                7px;

            margin-top:
                12px;

            font-size:
                12px;

            line-height:
                1.5;

        }


        .progress-message {

            color:
                var(--text-secondary);

        }


        .completed-message {

            color:
                var(--activity-completed);

            font-weight:
                500;

        }


        /* =====================================================
           VIDEO CARD
        ====================================================== */

        .video-card {

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                16px;

            overflow:
                hidden;

            margin-bottom:
                18px;

            box-shadow:
                0 2px 8px var(--shadow-color);

        }


        .video-header {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                15px;

            padding:
                15px 20px;

            background:
                var(--activity-video-header);

            border-bottom:
                1px solid var(--activity-border);

            color:
                var(--text-color);

            font-size:
                14px;

            font-weight:
                600;

        }


        .video-header i {

            color:
                #EF4444;

            font-size:
                17px;

        }


        .video-header span {

            color:
                var(--text-secondary);

            font-size:
                12px;

            font-weight:
                500;

        }


        /* =====================================================
           VIDEO CONTAINER
        ====================================================== */

        .video-container {

            position:
                relative;

            width:
                100%;

            aspect-ratio:
                16 / 9;

            background:
                var(--activity-video-bg);

            overflow:
                hidden;

        }


        .video-container iframe {

            position:
                absolute;

            inset:
                0;

            width:
                100%;

            height:
                100%;

            border:
                0;

        }


        /* =====================================================
           VIDEO ERROR
        ====================================================== */

        .video-error {

            position:
                absolute;

            inset:
                0;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;

            justify-content:
                center;

            text-align:
                center;

            padding:
                30px;

            color:
                var(--activity-error-text);

        }


        .video-error i {

            font-size:
                40px;

            color:
                var(--activity-error-warning);

            margin-bottom:
                10px;

        }


        .video-error h5 {

            margin:
                0 0 6px;

            color:
                #FFFFFF;

            font-size:
                16px;

            font-weight:
                600;

        }


        .video-error p {

            margin:
                0;

            color:
                var(--activity-error-muted);

            font-size:
                13px;

        }


        /* =====================================================
           INFORMATION CARD
        ====================================================== */

        .information-card {

            height:
                100%;

            background:
                var(--activity-card-bg);

            border:
                1px solid var(--activity-border);

            border-radius:
                16px;

            padding:
                20px 22px;

            box-shadow:
                0 2px 8px var(--shadow-color);

        }


        .information-card h5 {

            display:
                flex;

            align-items:
                center;

            margin:
                0 0 18px;

            padding-bottom:
                13px;

            border-bottom:
                1px solid var(--activity-border-light);

            color:
                var(--text-color);

            font-size:
                15px;

            font-weight:
                600;

        }


        .information-card h5 i {

            color:
                var(--activity-indigo);

            font-size:
                17px;

        }


        /* =====================================================
           INFORMATION GRID
        ====================================================== */

        .information-grid {

            display:
                grid;

            grid-template-columns:
                repeat(2, minmax(0, 1fr));

            gap:
                18px 25px;

        }


        .information-grid>div {

            display:
                flex;

            flex-direction:
                column;

            gap:
                4px;

        }


        .information-grid span,
        .activity-details span {

            color:
                var(--text-secondary);

            font-size:
                11px;

            font-weight:
                500;

            text-transform:
                uppercase;

            letter-spacing:
                0.5px;

        }


        .information-grid strong {

            color:
                var(--text-color);

            font-size:
                13px;

            font-weight:
                600;

        }


        /* =====================================================
           ACTIVITY DETAILS
        ====================================================== */

        .activity-details {

            display:
                flex;

            flex-direction:
                column;

            gap:
                0;

        }


        .activity-details>div {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                15px;

            padding:
                12px 0;

            border-bottom:
                1px solid var(--activity-border-light);

        }


        .activity-details>div:first-child {

            padding-top:
                0;

        }


        .activity-details>div:last-child {

            padding-bottom:
                0;

            border-bottom:
                0;

        }


        .activity-details strong {

            color:
                var(--text-color);

            font-size:
                12px;

            font-weight:
                600;

            text-align:
                right;

        }


        .activity-details strong.in-progress {

            color:
                var(--activity-inprogress);

        }


        .activity-details strong.completed {

            color:
                var(--activity-completed);

        }


        /* =====================================================
           SMOOTH CARD HOVER
        ====================================================== */

        .activity-progress-card,
        .video-card,
        .information-card,
        .activity-view-header {

            transition:
                box-shadow 0.2s ease,
                transform 0.2s ease;

        }


        .activity-progress-card:hover,
        .video-card:hover,
        .information-card:hover,
        .activity-view-header:hover {

            box-shadow:
                0 5px 18px var(--shadow-color);

        }


        /* =====================================================
           MAIN CONTENT SPACING
        ====================================================== */

        .main-content .content-wrapper {

            padding-bottom:
                35px;

        }


        /* =====================================================
           BOOTSTRAP ROW ADJUSTMENT
        ====================================================== */

        .row.g-3 {

            margin-top:
                0 !important;

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

            }


            .sidebar-overlay.show {

                display:
                    block;

            }


            .topbar {

                left:
                    0;

            }


            .main-content {

                margin-left:
                    0;

            }


            .sidebar-toggle {

                display:
                    block;

                margin-right:
                    15px;

            }


            .content-wrapper {

                padding:
                    20px;

            }


            .activity-item {

                grid-template-columns:
                    36px 48px minmax(200px, 1fr) 120px 110px;

            }


            .lecture-progress-bar {

                width:
                    100px;

            }


            .activity-view-header {

                padding:
                    22px;

            }


            .activity-view-header h2 {

                font-size:
                    23px;

            }

        }


        /* =====================================================
           TABLET
        ====================================================== */

        @media (max-width: 900px) {

            .activity-item {

                grid-template-columns:
                    36px 48px 1fr auto;

            }


            .activity-status {

                display:
                    none;

            }


            .activity-action {

                justify-content:
                    flex-end;

            }

        }


        /* =====================================================
           SMALL TABLET / MOBILE
        ====================================================== */

        @media (max-width: 768px) {

            .student-information {

                grid-template-columns:
                    1fr;

            }


            .student-info-item {

                border-right:
                    none;

                border-bottom:
                    1px solid var(--border-color);

            }


            .student-info-item:last-child {

                border-bottom:
                    none;

            }


            .profile-photo-section {

                padding:
                    30px 20px;

            }


            .profile-upload-section {

                padding:
                    25px 20px;

            }


            .profile-upload-footer {

                justify-content:
                    stretch;

            }


            .profile-upload-footer .btn {

                width:
                    100%;

            }


            .page-header {

                margin-bottom:
                    18px;

            }


            .page-header h2 {

                font-size:
                    22px;

            }


            .learning-progress-card {

                padding:
                    18px;

            }


            .learning-progress-header {

                align-items:
                    center;

            }


            .learning-progress-header h5 {

                font-size:
                    16px;

            }


            .learning-progress-footer {

                flex-direction:
                    column;

                align-items:
                    flex-start;

                gap:
                    5px;

            }


            .activity-card-header {

                padding:
                    18px;

            }


            .activity-card-header p {

                display:
                    none;

            }


            .activity-item {

                grid-template-columns:
                    30px 42px 1fr;

                gap:
                    10px;

                padding:
                    17px;

            }


            .activity-number {

                width:
                    28px;

                height:
                    28px;

                font-size:
                    11px;

            }


            .activity-icon {

                width:
                    40px;

                height:
                    40px;

                font-size:
                    17px;

            }


            .activity-information h5 {

                font-size:
                    14px;

                white-space:
                    normal;

            }


            .activity-meta {

                gap:
                    8px;

            }


            .lecture-progress-wrapper {

                margin-top:
                    8px;

            }


            .lecture-progress-bar {

                width:
                    100%;

            }


            .activity-action {

                grid-column:
                    3;

                justify-content:
                    flex-start;

                margin-top:
                    5px;

            }


            .activity-action .btn {

                width:
                    100%;

            }


            .activity-view-header {

                flex-direction:
                    column;

                gap:
                    15px;

            }


            .activity-view-header .activity-status-badge {

                align-self:
                    flex-start;

            }


            .information-grid {

                grid-template-columns:
                    1fr;

            }


            .activity-progress-card {

                padding:
                    18px;

            }


            .video-header {

                padding:
                    13px 15px;

            }

        }


        /* =====================================================
           MOBILE
        ====================================================== */

        @media (max-width: 576px) {

            .topbar {

                padding:
                    0 15px;

            }


            .topbar-user-info {

                display:
                    none;

            }


            .topbar-title {

                font-size:
                    16px;

            }


            .content-wrapper {

                padding:
                    15px;

            }


            .welcome-card {

                padding:
                    20px;

            }


            .welcome-card h2 {

                font-size:
                    20px;

            }


            .info-row {

                display:
                    block;

            }


            .info-value {

                text-align:
                    left;

                margin-top:
                    3px;

            }


            .shoutbox-header,
            .shoutbox-form {

                padding:
                    16px;

            }


            .shoutbox-messages {

                padding-left:
                    16px;

                padding-right:
                    16px;

            }


            .shoutbox-user {

                padding:
                    14px 16px;

            }


            .shout-student-info {

                gap:
                    7px;

            }


            .activity-view-header {

                padding:
                    18px;

            }


            .activity-view-header h2 {

                font-size:
                    20px;

            }


            .activity-view-header p {

                font-size:
                    13px;

            }


            .activity-progress-header {

                font-size:
                    13px;

            }


            .information-card {

                padding:
                    18px;

            }


            .video-header span {

                font-size:
                    11px;

            }

        }


        /* =====================================================
           VERY SMALL SCREENS
        ====================================================== */

        @media (max-width: 400px) {

            .student-stat-card {

                padding:
                    15px;

            }


            .student-stat-icon {

                width:
                    42px;

                height:
                    42px;

                min-width:
                    42px;

            }


            .student-stat-card strong {

                font-size:
                    20px;

            }


            .activity-count {

                display:
                    none;

            }

        }


        /* =====================================================
           MOBILE OVERLAY
        ====================================================== */

        .sidebar-overlay {

            display:
                none;

            position:
                fixed;

            inset:
                0;

            background:
                rgba(0, 0, 0, 0.50);

            z-index:
                1045;

        }


        /* =====================================================
           REDUCE MOTION
        ====================================================== */

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {

                transition:
                    none !important;

                scroll-behavior:
                    auto !important;

            }

        }
    </style>

</head>