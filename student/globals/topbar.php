<?php

/* =========================================================
   TOPBAR STUDENT PROFILE
   ETS-Async Learning Portal
   ========================================================= */


/* =========================================================
   VARIABLES
========================================================= */

$topbarProfilePhoto = "";
$topbarProfilePhotoURL = "";
$topbarStudentId = "";


/* =========================================================
   GET STUDENT ID
========================================================= */

if (
    isset($_SESSION["user"]["student_id"])
) {

    $topbarStudentId =
        trim(
            $_SESSION["user"]["student_id"]
        );
}


/* =========================================================
   GET PROFILE PHOTO
========================================================= */

if (
    $topbarStudentId !== "" &&
    isset($mysqli)
) {

    $topbarPhotoSQL = "
        SELECT profile_photo
        FROM accounts
        WHERE student_id = ?
        LIMIT 1
    ";


    $topbarPhotoStmt =
        $mysqli->prepare(
            $topbarPhotoSQL
        );


    if ($topbarPhotoStmt) {

        $topbarPhotoStmt->bind_param(
            "s",
            $topbarStudentId
        );


        $topbarPhotoStmt->execute();


        $topbarPhotoResult =
            $topbarPhotoStmt->get_result();


        if (
            $topbarPhotoRow =
            $topbarPhotoResult->fetch_assoc()
        ) {

            $topbarProfilePhoto =
                trim(
                    $topbarPhotoRow["profile_photo"] ?? ""
                );
        }


        $topbarPhotoStmt->close();
    }
}


/* =========================================================
   BUILD PROFILE PHOTO URL
========================================================= */

/*
    IMPORTANT DOMAIN STRUCTURE:

    Student application:
    https://async.vertigation.com/

    Shared uploads:
    https://vertigation.com/shared/uploads/profile_photos/

    Database:
    shared/uploads/profile_photos/photo.jpg

    Therefore the final image URL must be:

    https://vertigation.com/shared/uploads/profile_photos/photo.jpg

    NOT:

    https://async.vertigation.com/shared/uploads/profile_photos/photo.jpg
*/

if (
    $topbarProfilePhoto !== ""
) {


    /* =====================================================
       CASE 1
       COMPLETE URL
    ====================================================== */

    if (
        preg_match(
            '/^https?:\/\//i',
            $topbarProfilePhoto
        )
    ) {

        /*
         * If the database contains an old
         * async.vertigation.com URL, convert it
         * to the main domain.
         */

        $parsedPath =
            parse_url(
                $topbarProfilePhoto,
                PHP_URL_PATH
            );


        if (
            is_string($parsedPath) &&
            strpos(
                $parsedPath,
                "/shared/uploads/profile_photos/"
            ) === 0
        ) {

            $topbarProfilePhotoURL =
                "https://vertigation.com" .
                $parsedPath;
        } else {

            $topbarProfilePhotoURL =
                $topbarProfilePhoto;
        }
    }


    /* =====================================================
       CASE 2
       ROOT-RELATIVE PATH

       /shared/uploads/profile_photos/photo.jpg
    ====================================================== */ elseif (
        strpos(
            $topbarProfilePhoto,
            "/shared/uploads/profile_photos/"
        ) === 0
    ) {

        $topbarProfilePhotoURL =
            "https://vertigation.com" .
            $topbarProfilePhoto;
    }


    /* =====================================================
       CASE 3
       NORMAL DATABASE PATH

       shared/uploads/profile_photos/photo.jpg
    ====================================================== */ elseif (
        strpos(
            $topbarProfilePhoto,
            "shared/uploads/profile_photos/"
        ) === 0
    ) {

        $topbarProfilePhotoURL =
            "https://vertigation.com/" .
            $topbarProfilePhoto;
    }


    /* =====================================================
       CASE 4
       OLD INCORRECT PATH

       async/shared/uploads/profile_photos/photo.jpg
    ====================================================== */ elseif (
        strpos(
            $topbarProfilePhoto,
            "async/shared/uploads/profile_photos/"
        ) === 0
    ) {

        $cleanPath =
            substr(
                $topbarProfilePhoto,
                strlen("async/")
            );


        $topbarProfilePhotoURL =
            "https://vertigation.com/" .
            $cleanPath;
    }


    /* =====================================================
       CASE 5
       FALLBACK
    ====================================================== */ else {

        $cleanPath =
            ltrim(
                $topbarProfilePhoto,
                "/"
            );


        $topbarProfilePhotoURL =
            "https://vertigation.com/" .
            $cleanPath;
    }
}

?>


<!-- =========================================================
     TOPBAR
========================================================= -->

<header
    class="topbar"
    id="topbar">


    <!-- =====================================================
         LEFT
    ====================================================== -->

    <div class="topbar-left">


        <!-- =================================================
             SIDEBAR TOGGLE
        ================================================== -->

        <button
            type="button"
            class="sidebar-toggle"
            id="sidebarToggle"
            aria-label="Toggle sidebar"
            title="Toggle sidebar">

            <i class="bi bi-list"></i>

        </button>


        <!-- =================================================
             TITLE
        ================================================== -->

        <div class="topbar-title">

            Student Dashboard

        </div>


    </div>


    <!-- =====================================================
         USER
    ====================================================== -->

    <div class="topbar-user">


        <!-- =================================================
             USER INFORMATION
        ================================================== -->

        <div class="topbar-user-info">

            <div class="topbar-name">

                <?= htmlspecialchars(
                    $fullName ?? "",
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>

            </div>


            <div class="topbar-access">

                Student Account

            </div>

        </div>


        <!-- =================================================
             PROFILE DROPDOWN
        ================================================== -->

        <div
            class="topbar-profile-dropdown"
            id="topbarProfileDropdown">


            <!-- =================================================
                 PROFILE BUTTON
            ================================================== -->

            <button
                type="button"
                class="topbar-avatar-button"
                id="topbarProfileButton"
                aria-expanded="false"
                aria-haspopup="true"
                aria-label="Open profile menu"
                title="Account menu">


                <!-- =============================================
                     PROFILE IMAGE
                ============================================== -->

                <?php if (
                    $topbarProfilePhotoURL !== ""
                ): ?>


                    <img
                        src="<?= htmlspecialchars(
                                    $topbarProfilePhotoURL,
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>"
                        alt="Profile Photo"
                        class="topbar-avatar topbar-avatar-image"
                        onerror="
                            this.style.display='none';
                            this.nextElementSibling.style.display='flex';
                        ">


                    <!-- =========================================
                         FALLBACK INITIALS
                    ========================================== -->

                    <span
                        class="topbar-avatar topbar-avatar-fallback"
                        style="display:none;">

                        <?= htmlspecialchars(
                            $initials ?? "ST",
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>

                    </span>


                <?php else: ?>


                    <!-- =========================================
                         DEFAULT INITIALS
                    ========================================== -->

                    <span class="topbar-avatar">

                        <?= htmlspecialchars(
                            $initials ?? "ST",
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>

                    </span>


                <?php endif; ?>


                <!-- =============================================
                     CHEVRON
                ============================================== -->

                <span class="topbar-profile-chevron">

                    <i class="bi bi-chevron-down"></i>

                </span>


            </button>


            <!-- =================================================
                 PROFILE DROPDOWN MENU
            ================================================== -->

            <div
                class="topbar-profile-menu"
                id="topbarProfileMenu"
                role="menu"
                aria-hidden="true">


                <!-- =============================================
                     PROFILE HEADER
                ============================================== -->

                <div class="topbar-profile-menu-header">


                    <!-- =========================================
                         MENU AVATAR
                    ========================================== -->

                    <div class="topbar-profile-menu-avatar">

                        <?php if (
                            $topbarProfilePhotoURL !== ""
                        ): ?>


                            <img
                                src="<?= htmlspecialchars(
                                            $topbarProfilePhotoURL,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>"
                                alt="Profile Photo"
                                onerror="
                                    this.style.display='none';
                                    this.nextElementSibling.style.display='flex';
                                ">


                            <span
                                style="display:none;">

                                <?= htmlspecialchars(
                                    $initials ?? "ST",
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>

                            </span>


                        <?php else: ?>


                            <?= htmlspecialchars(
                                $initials ?? "ST",
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>


                        <?php endif; ?>

                    </div>


                    <!-- =========================================
                         USER INFORMATION
                    ========================================== -->

                    <div class="topbar-profile-menu-user">


                        <div class="topbar-profile-menu-name">

                            <?= htmlspecialchars(
                                $fullName ?? "",
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>

                        </div>


                        <div class="topbar-profile-menu-id">

                            <?= htmlspecialchars(
                                $topbarStudentId,
                                ENT_QUOTES,
                                "UTF-8"
                            ) ?>

                        </div>


                    </div>


                </div>


                <!-- =================================================
                     DIVIDER
                ================================================== -->

                <div class="topbar-profile-divider"></div>


                <!-- =================================================
                     PROFILE
                ================================================== -->

                <a
                    href="profile_photo.php"
                    class="topbar-profile-menu-link"
                    role="menuitem">


                    <span class="topbar-profile-menu-icon">

                        <i class="bi bi-person-circle"></i>

                    </span>


                    <span class="topbar-profile-menu-text">

                        <strong>
                            Profile
                        </strong>

                        <small>
                            View profile photo
                        </small>

                    </span>


                </a>


                <!-- =================================================
                     EDIT ACCOUNT
                ================================================== -->

                <a
                    href="student_profile.php"
                    class="topbar-profile-menu-link"
                    role="menuitem">


                    <span class="topbar-profile-menu-icon">

                        <i class="bi bi-person-gear"></i>

                    </span>


                    <span class="topbar-profile-menu-text">

                        <strong>
                            Edit Account
                        </strong>

                        <small>
                            Update account information
                        </small>

                    </span>


                </a>

                <!-- =================================================
                    Classmates
                ================================================== -->

                <a
                    href="classmates.php"
                    class="topbar-profile-menu-link"
                    role="menuitem">


                    <span class="topbar-profile-menu-icon">

                        <i class="bi bi-people-fill"></i>

                    </span>


                    <span class="topbar-profile-menu-text">

                        <strong>
                            Classmates
                        </strong>

                        <small>
                            View your classmates
                        </small>

                    </span>


                </a>


                <!-- =================================================
                     Freedom Walls
                ================================================== -->

                <a
                    href="freedom_walls.php"
                    class="topbar-profile-menu-link"
                    role="menuitem">


                    <span class="topbar-profile-menu-icon">

                        <i class="bi bi-flag"></i>

                    </span>


                    <span class="topbar-profile-menu-text">

                        <strong>
                            Freedom Walls
                        </strong>

                        <small>
                            View your freedom walls
                        </small>

                    </span>


                </a>
                <!-- =================================================
                     DIVIDER
                ================================================== -->

                <div class="topbar-profile-divider"></div>


                <!-- =================================================
                     LOGOUT
                ================================================== -->

                <a
                    href="logout.php"
                    class="topbar-profile-menu-link topbar-profile-logout"
                    role="menuitem">


                    <span class="topbar-profile-menu-icon">

                        <i class="bi bi-box-arrow-right"></i>

                    </span>


                    <span class="topbar-profile-menu-text">

                        <strong>
                            Logout
                        </strong>

                        <small>
                            Sign out of your account
                        </small>

                    </span>


                </a>


            </div>


        </div>


        <!-- =================================================
             THEME TOGGLE
        ================================================== -->

        <button
            type="button"
            class="btn btn-theme"
            id="themeToggle"
            title="Toggle dark mode"
            aria-label="Toggle dark mode">

            <i
                class="bi bi-moon-fill"
                id="themeIcon"></i>

        </button>


    </div>


</header>


<!-- =========================================================
     SIDEBAR TOGGLE SCRIPT
========================================================= -->

<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {

            const sidebarToggle =
                document.getElementById(
                    "sidebarToggle"
                );


            if (!sidebarToggle) {

                return;

            }


            sidebarToggle.addEventListener(
                "click",
                function(event) {

                    event.preventDefault();

                    event.stopPropagation();


                    if (
                        typeof window.toggleSidebar ===
                        "function"
                    ) {

                        window.toggleSidebar();

                    }

                }
            );

        }
    );
</script>


<!-- =========================================================
     PROFILE DROPDOWN SCRIPT
     CLICK ONLY
========================================================= -->

<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {

            const profileDropdown =
                document.getElementById(
                    "topbarProfileDropdown"
                );

            const profileButton =
                document.getElementById(
                    "topbarProfileButton"
                );

            const profileMenu =
                document.getElementById(
                    "topbarProfileMenu"
                );


            /* =====================================================
               CHECK REQUIRED ELEMENTS
            ====================================================== */

            if (
                !profileDropdown ||
                !profileButton ||
                !profileMenu
            ) {

                return;

            }


            /* =====================================================
               OPEN MENU
            ====================================================== */

            function openProfileMenu() {

                profileDropdown.classList.add(
                    "show"
                );

                profileButton.setAttribute(
                    "aria-expanded",
                    "true"
                );

                profileMenu.setAttribute(
                    "aria-hidden",
                    "false"
                );

            }


            /* =====================================================
               CLOSE MENU
            ====================================================== */

            function closeProfileMenu() {

                profileDropdown.classList.remove(
                    "show"
                );

                profileButton.setAttribute(
                    "aria-expanded",
                    "false"
                );

                profileMenu.setAttribute(
                    "aria-hidden",
                    "true"
                );

            }


            /* =====================================================
               PROFILE BUTTON CLICK
            ====================================================== */

            profileButton.addEventListener(
                "click",
                function(event) {

                    event.preventDefault();

                    event.stopPropagation();


                    if (
                        profileDropdown.classList.contains(
                            "show"
                        )
                    ) {

                        closeProfileMenu();

                    } else {

                        openProfileMenu();

                    }

                }
            );


            /* =====================================================
               MENU CLICK
               
               IMPORTANT:
               Prevent clicks inside the menu from reaching
               the document click handler.
               
               This allows the user to move the mouse into
               the dropdown and click the links normally.
            ====================================================== */

            profileMenu.addEventListener(
                "click",
                function(event) {

                    event.stopPropagation();

                }
            );


            /* =====================================================
               CLICK OUTSIDE
            ====================================================== */

            document.addEventListener(
                "click",
                function(event) {

                    if (
                        !profileDropdown.contains(
                            event.target
                        )
                    ) {

                        closeProfileMenu();

                    }

                }
            );


            /* =====================================================
               ESCAPE KEY
            ====================================================== */

            document.addEventListener(
                "keydown",
                function(event) {

                    if (
                        event.key === "Escape"
                    ) {

                        closeProfileMenu();

                        profileButton.focus();

                    }

                }
            );

        }
    );
</script>


<!-- =========================================================
     DARK MODE SCRIPT
========================================================= -->

<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {

            const themeToggle =
                document.getElementById(
                    "themeToggle"
                );


            const themeIcon =
                document.getElementById(
                    "themeIcon"
                );


            if (
                !themeToggle ||
                !themeIcon
            ) {

                return;

            }


            /* =====================================================
               UPDATE ICON
            ====================================================== */

            function updateThemeIcon() {

                const currentTheme =
                    document.documentElement.getAttribute(
                        "data-theme"
                    );


                if (
                    currentTheme === "dark"
                ) {

                    themeIcon.className =
                        "bi bi-sun-fill";


                    themeToggle.title =
                        "Switch to light mode";

                } else {

                    themeIcon.className =
                        "bi bi-moon-fill";


                    themeToggle.title =
                        "Switch to dark mode";

                }

            }


            /* =====================================================
               INITIAL ICON
            ====================================================== */

            updateThemeIcon();


            /* =====================================================
               TOGGLE THEME
            ====================================================== */

            themeToggle.addEventListener(
                "click",
                function() {

                    const currentTheme =
                        document.documentElement.getAttribute(
                            "data-theme"
                        );


                    if (
                        currentTheme === "dark"
                    ) {

                        document.documentElement
                            .setAttribute(
                                "data-theme",
                                "light"
                            );


                        localStorage.setItem(
                            "ets-theme",
                            "light"
                        );

                    } else {

                        document.documentElement
                            .setAttribute(
                                "data-theme",
                                "dark"
                            );


                        localStorage.setItem(
                            "ets-theme",
                            "dark"
                        );

                    }


                    updateThemeIcon();

                }
            );

        }
    );
</script>


<!-- =========================================================
     TOPBAR / PROFILE DROPDOWN CSS
========================================================= -->

<style>
    /* =========================================================
   TOPBAR
========================================================= */

    .topbar {

        transition:
            margin-left 0.3s ease,
            width 0.3s ease;

    }


    /* =========================================================
   PROFILE DROPDOWN CONTAINER
========================================================= */

    .topbar-profile-dropdown {

        position: relative;

        display: flex;

        align-items: center;

    }


    /* =========================================================
   PROFILE AVATAR BUTTON
========================================================= */

    .topbar-avatar-button {

        position: relative;

        width: 42px;

        height: 42px;

        padding: 0;

        border: 0;

        background: transparent;

        display: flex;

        align-items: center;

        justify-content: center;

        cursor: pointer;

        border-radius: 50%;

        outline: none;

    }


    /* =========================================================
   AVATAR
========================================================= */

    .topbar-avatar-button .topbar-avatar {

        width: 40px;

        height: 40px;

        min-width: 40px;

        border-radius: 50%;

        display: flex;

        align-items: center;

        justify-content: center;

        overflow: hidden;

        background:
            var(--academic-blue);

        color: #fff;

        font-weight: 700;

        font-size: 0.85rem;

        border:
            2px solid var(--surface-color);

        box-shadow:
            0 2px 8px var(--shadow-color);

        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease;

    }


    /* =========================================================
   AVATAR HOVER
========================================================= */

    .topbar-avatar-button:hover .topbar-avatar {

        transform:
            scale(1.04);

        box-shadow:
            0 4px 12px var(--shadow-color);

    }


    /* =========================================================
   PROFILE IMAGE
========================================================= */

    .topbar-avatar-image {

        width: 40px !important;

        height: 40px !important;

        min-width: 40px !important;

        object-fit: cover;

        border-radius: 50%;

    }


    /* =========================================================
   CHEVRON
========================================================= */

    .topbar-profile-chevron {

        position: absolute;

        right: -4px;

        bottom: -1px;

        width: 15px;

        height: 15px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 50%;

        background:
            var(--surface-color);

        color:
            var(--text-secondary);

        border:
            1px solid var(--border-color);

        font-size: 8px;

        transition:
            transform 0.2s ease;

    }


    .topbar-profile-dropdown.show .topbar-profile-chevron {

        transform:
            rotate(180deg);

    }


    /* =========================================================
   PROFILE MENU
========================================================= */

    .topbar-profile-menu {

        position: absolute;

        top: calc(100% + 10px);

        right: 0;

        width: 280px;

        background:
            var(--surface-color);

        border:
            1px solid var(--border-color);

        border-radius: 14px;

        box-shadow:
            0 12px 35px var(--shadow-color);

        padding: 8px;

        z-index: 2000;

        opacity: 0;

        visibility: hidden;

        transform:
            translateY(-7px) scale(0.98);

        transform-origin:
            top right;

        transition:
            opacity 0.18s ease,
            transform 0.18s ease,
            visibility 0.18s ease;

    }


    /* =========================================================
   SHOW MENU
========================================================= */

    .topbar-profile-dropdown.show .topbar-profile-menu {

        opacity: 1;

        visibility: visible;

        transform:
            translateY(0) scale(1);

    }


    /* =========================================================
   MENU HEADER
========================================================= */

    .topbar-profile-menu-header {

        display: flex;

        align-items: center;

        gap: 12px;

        padding: 12px;

    }


    /* =========================================================
   MENU AVATAR
========================================================= */

    .topbar-profile-menu-avatar {

        width: 46px;

        height: 46px;

        min-width: 46px;

        border-radius: 50%;

        overflow: hidden;

        display: flex;

        align-items: center;

        justify-content: center;

        background:
            var(--academic-blue);

        color: #fff;

        font-weight: 700;

        font-size: 0.9rem;

    }


    .topbar-profile-menu-avatar img {

        width: 100%;

        height: 100%;

        object-fit: cover;

        border-radius: 50%;

    }


    /* =========================================================
   MENU USER INFORMATION
========================================================= */

    .topbar-profile-menu-user {

        min-width: 0;

        flex: 1;

    }


    .topbar-profile-menu-name {

        color:
            var(--text-color);

        font-size: 0.9rem;

        font-weight: 700;

        white-space: nowrap;

        overflow: hidden;

        text-overflow: ellipsis;

    }


    .topbar-profile-menu-id {

        color:
            var(--text-secondary);

        font-size: 0.75rem;

        margin-top: 2px;

    }


    /* =========================================================
   DIVIDER
========================================================= */

    .topbar-profile-divider {

        height: 1px;

        background:
            var(--border-color);

        margin:
            5px 4px;

    }


    /* =========================================================
   MENU LINK
========================================================= */

    .topbar-profile-menu-link {

        display: flex;

        align-items: center;

        gap: 11px;

        width: 100%;

        padding: 10px;

        border-radius: 9px;

        text-decoration: none;

        color:
            var(--text-color);

        transition:
            background-color 0.15s ease,
            color 0.15s ease;

    }


    .topbar-profile-menu-link:hover {

        background:
            var(--activity-hover-bg);

        color:
            var(--academic-blue);

        text-decoration: none;

    }


    /* =========================================================
   MENU ICON
========================================================= */

    .topbar-profile-menu-icon {

        width: 34px;

        height: 34px;

        min-width: 34px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 8px;

        background:
            var(--activity-icon-blue-bg);

        color:
            var(--activity-icon-blue);

        font-size: 1rem;

    }


    /* =========================================================
   MENU TEXT
========================================================= */

    .topbar-profile-menu-text {

        min-width: 0;

        display: flex;

        flex-direction: column;

        gap: 1px;

    }


    .topbar-profile-menu-text strong {

        color:
            var(--text-color);

        font-size: 0.85rem;

        font-weight: 600;

    }


    .topbar-profile-menu-text small {

        color:
            var(--text-secondary);

        font-size: 0.7rem;

    }


    /* =========================================================
   LOGOUT
========================================================= */

    .topbar-profile-logout {

        margin-top: 2px;

    }


    .topbar-profile-logout .topbar-profile-menu-icon {

        background:
            var(--activity-icon-orange-bg);

        color:
            var(--activity-error-warning);

    }


    .topbar-profile-logout:hover {

        color:
            var(--activity-error-warning);

    }


    /* =========================================================
   DESKTOP
========================================================= */

    @media (min-width: 992px) {

        body.sidebar-collapsed .topbar {

            margin-left: 0 !important;

            width: 100% !important;

        }

    }


    /* =========================================================
   TABLET / MOBILE
========================================================= */

    @media (max-width: 991px) {

        .topbar {

            margin-left: 0 !important;

            width: 100% !important;

        }


        .topbar-user {

            display: flex;

            align-items: center;

        }

    }


    /* =========================================================
   SMALL MOBILE
========================================================= */

    @media (max-width: 576px) {

        .topbar-title {

            font-size: 16px;

        }


        .topbar-name {

            font-size: 13px;

        }


        .topbar-access {

            font-size: 11px;

        }


        .topbar-user-info {

            max-width: 130px;

        }


        .topbar-profile-menu {

            width: 260px;

            right: -5px;

        }

    }


    /* =========================================================
   VERY SMALL MOBILE
========================================================= */

    @media (max-width: 400px) {

        .topbar-title {

            font-size: 14px;

        }


        .topbar-user-info {

            display: none;

        }


        .topbar-profile-menu {

            width: 250px;

        }

    }
</style>