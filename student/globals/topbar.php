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

$isAsyncDeveloper =
    isset($fullName) &&
    trim((string) $fullName) === "Engr. Karl Stephen O. Evallo";


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

            <?= $isAsyncDeveloper ? "Developer" : "Student Dashboard" ?>

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

                <?php if ($isAsyncDeveloper): ?>
                    <span class="developer-wave-name" aria-label="<?= htmlspecialchars($fullName ?? "", ENT_QUOTES, "UTF-8") ?>">
                        <?php foreach (preg_split('//u', (string) $fullName, -1, PREG_SPLIT_NO_EMPTY) as $waveIndex => $waveCharacter): ?>
                            <span class="developer-wave-letter<?= preg_match('/\s/u', $waveCharacter) ? ' developer-wave-space' : '' ?>" style="--wave-index: <?= (int) $waveIndex ?>"><?= preg_match('/\s/u', $waveCharacter) ? '&nbsp;' : htmlspecialchars($waveCharacter, ENT_QUOTES, "UTF-8") ?></span>
                        <?php endforeach; ?>
                    </span>
                <?php else: ?>
                    <?= htmlspecialchars($fullName ?? "", ENT_QUOTES, "UTF-8") ?>
                <?php endif; ?>

            </div>


            <div class="topbar-access">

                <?php if ($isAsyncDeveloper): ?>
                    <span class="async-dev-rank" aria-label="Developer">
                        <i class="bi bi-code-slash" aria-hidden="true"></i><span>DEV</span>
                    </span>
                <?php else: ?>
                    Student Account
                <?php endif; ?>

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

                            <?php if ($isAsyncDeveloper): ?>
                                <span class="developer-wave-name" aria-label="<?= htmlspecialchars($fullName ?? "", ENT_QUOTES, "UTF-8") ?>">
                                    <?php foreach (preg_split('//u', (string) $fullName, -1, PREG_SPLIT_NO_EMPTY) as $waveIndex => $waveCharacter): ?>
                                        <span class="developer-wave-letter<?= preg_match('/\s/u', $waveCharacter) ? ' developer-wave-space' : '' ?>" style="--wave-index: <?= (int) $waveIndex ?>"><?= preg_match('/\s/u', $waveCharacter) ? '&nbsp;' : htmlspecialchars($waveCharacter, ENT_QUOTES, "UTF-8") ?></span>
                                    <?php endforeach; ?>
                                </span>
                            <?php else: ?>
                                <?= htmlspecialchars($fullName ?? "", ENT_QUOTES, "UTF-8") ?>
                            <?php endif; ?>

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
                    Bookmarks
                ================================================== -->

                <a
                    href="academic_posts_saved.php"
                    class="topbar-profile-menu-link"
                    role="menuitem">


                    <span class="topbar-profile-menu-icon">

                        <i class="bi bi-bookmark-star"></i>

                    </span>


                    <span class="topbar-profile-menu-text">

                        <strong>
                            Bookmarks
                        </strong>

                        <small>
                            View your saved academic posts
                        </small>

                    </span>


                </a>


                <!-- =================================================
                     Freedom Walls
                ================================================== -->

                <!-- <a
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


                </a> -->
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
            class="btn btn-theme topbar-theme-switch"
            id="themeToggle"
            title="Toggle dark mode"
            aria-label="Toggle dark mode">
            <span class="theme-switch-track" aria-hidden="true">
                <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
            </span>
        </button>


    </div>


</header>

<?php if ($isAsyncDeveloper): ?>
<div class="async-game-intro" id="asyncGameIntro" aria-hidden="true">
    <div class="async-intro-grid"></div>
    <div class="async-intro-scan"></div>
    <div class="async-intro-content">
        <div class="async-intro-emblem"><i class="bi bi-code-slash"></i></div>
        <div class="async-intro-kicker">ASYNC SYSTEM // DEVELOPER ACCESS</div>
        <div class="async-intro-title">WELCOME BACK</div>
        <div class="async-intro-name">ENGR. KARL STEPHEN O. EVALLO</div>
        <div class="async-intro-loading"><span></span></div>
        <div class="async-intro-status">INITIALIZING DEVELOPER INTERFACE</div>
        <button type="button" class="async-intro-skip" id="asyncIntroSkip">SKIP INTRO <i class="bi bi-arrow-right"></i></button>
    </div>
</div>
<?php endif; ?>


<!-- =========================================================
    Announcements 
========================================================= -->

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


<?php if ($isAsyncDeveloper): ?>
<script>
    document.documentElement.classList.add("async-developer-mode");
</script>
<?php endif; ?>

<?php if ($isAsyncDeveloper): ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const intro = document.getElementById("asyncGameIntro");
    const skip = document.getElementById("asyncIntroSkip");
    if (!intro) return;
    let dismissed = false;
    const closeIntro = function () {
        if (dismissed) return;
        dismissed = true;
        intro.classList.add("is-leaving");
        window.setTimeout(function () {
            if (intro && intro.parentNode) intro.parentNode.removeChild(intro);
        }, 700);
    };
    if (skip) skip.addEventListener("click", closeIntro);
    window.setTimeout(closeIntro, 2300);
});
</script>
<?php endif; ?>

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

    /* =========================================================
       SPECIAL DEVELOPER PROFILE EFFECTS
       Applied only to the exact matching full name.
    ========================================================= */

    /* Developer-only topbar identity and subtle interface detailing. */
    .async-developer-mode .topbar {
        position: relative;
        isolation: isolate;
        border-bottom: 1px solid rgba(67, 190, 255, .38);
        box-shadow: 0 5px 22px rgba(0, 150, 255, .09);
    }

    .async-developer-mode .topbar::after {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        opacity: .34;
        background: linear-gradient(105deg, transparent 0%, rgba(0, 225, 255, .08) 42%, rgba(168, 85, 247, .10) 50%, transparent 62%);
        background-size: 220% 100%;
        animation: developer-topbar-scan 8s linear infinite;
    }

    .async-developer-mode .topbar > * {
        position: relative;
        z-index: 1;
    }

    .async-developer-mode .topbar-user-info {
        overflow: visible;
    }

    .async-developer-mode .topbar-name {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .async-developer-mode .async-dev-rank {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 7px;
        border: 1px solid rgba(0, 220, 255, .58);
        border-radius: 5px;
        color: #7cecff;
        background: linear-gradient(110deg, rgba(0, 190, 255, .12), rgba(168, 85, 247, .13));
        font: 800 9px/1.2 system-ui, sans-serif;
        letter-spacing: 1.25px;
        white-space: nowrap;
        text-shadow: 0 0 8px rgba(0, 220, 255, .65);
        box-shadow: inset 0 0 8px rgba(0, 200, 255, .10), 0 0 10px rgba(0, 200, 255, .12);
        animation: developer-rank-pulse 2.5s ease-in-out infinite;
    }

    .async-developer-mode .async-dev-rank i {
        font-size: 10px;
    }

    .async-developer-mode .topbar-profile-menu {
        border-color: rgba(0, 190, 255, .42);
        box-shadow: 0 14px 38px rgba(0, 80, 150, .20), 0 0 16px rgba(0, 190, 255, .10);
    }

    .async-developer-mode .topbar-profile-menu-link {
        position: relative;
        overflow: hidden;
        transition: transform .22s cubic-bezier(.2,.8,.2,1), background-color .2s ease, color .2s ease;
    }

    .async-developer-mode .topbar-profile-menu-link:hover {
        transform: translateX(3px);
        background: linear-gradient(90deg, rgba(0, 190, 255, .13), rgba(168, 85, 247, .08));
    }

    .async-developer-mode .topbar-avatar-button:focus-visible {
        outline: 2px solid #55eaff;
        outline-offset: 5px;
        box-shadow: 0 0 0 5px rgba(0, 200, 255, .12);
    }

    .async-developer-mode .topbar-profile-chevron {
        color: #55dfff;
        border-color: rgba(0, 210, 255, .55);
        box-shadow: 0 0 8px rgba(0, 210, 255, .24);
    }

    @keyframes developer-topbar-scan {
        from { background-position: 120% 0; }
        to { background-position: -120% 0; }
    }

    @keyframes developer-rank-pulse {
        0%, 100% { opacity: .78; box-shadow: inset 0 0 8px rgba(0, 200, 255, .08), 0 0 5px rgba(0, 200, 255, .10); }
        50% { opacity: 1; box-shadow: inset 0 0 10px rgba(0, 200, 255, .18), 0 0 13px rgba(0, 200, 255, .32); }
    }

    .async-developer-mode .topbar-title,
    .async-developer-mode .topbar-access {
        color: var(--academic-blue) !important;
        -webkit-text-fill-color: transparent;
        -webkit-text-stroke: 1px currentColor;
        paint-order: stroke fill;
        font-weight: 800;
        letter-spacing: 1.4px;
        text-transform: uppercase;
        filter: drop-shadow(0 0 5px rgba(75, 170, 255, .45));
        animation: developer-outline-pulse 2.8s ease-in-out infinite;
    }

    .async-developer-mode .topbar-name {
        overflow: visible !important;
    }

    .async-developer-mode .developer-wave-name {
        display: inline-flex !important;
        align-items: baseline;
        white-space: nowrap !important;
        overflow: visible !important;
        gap: 0;
    }

    .async-developer-mode .developer-wave-letter {
        display: inline-block !important;
        position: relative;
        transform: translateY(0);
        transform-origin: 50% 85%;
        animation: developer-wave-bounce 1.45s ease-in-out infinite both !important;
        animation-delay: calc(var(--wave-index) * 75ms) !important;
        will-change: transform;
    }

    .async-developer-mode .developer-wave-space {
        width: .34em;
        min-width: .34em;
        animation: none !important;
    }

    .async-developer-mode .topbar-avatar-button {
        isolation: isolate;
        overflow: visible;
    }

    .async-developer-mode .topbar-avatar-button::before {
        content: "";
        position: absolute;
        inset: -4px;
        z-index: -1;
        border-radius: 50%;
        padding: 2.5px;
        background: conic-gradient(
            from 0deg,
            #00e5ff,
            #3978ff,
            #a855f7,
            #ff3cac,
            #ffb347,
            #00e5ff
        );
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        animation: developer-avatar-ring-spin 3.2s linear infinite;
        filter: drop-shadow(0 0 4px rgba(0, 210, 255, .7));
    }

    .async-developer-mode .topbar-avatar-button .topbar-avatar {
        animation: developer-avatar-energy 2.1s ease-in-out infinite;
    }

    .async-developer-mode .topbar-profile-menu-avatar {
        border: 2px solid transparent;
        background:
            linear-gradient(var(--academic-blue), var(--academic-blue)) padding-box,
            conic-gradient(from 0deg, #00e5ff, #3978ff, #a855f7, #ff3cac, #ffb347, #00e5ff) border-box;
        animation: developer-menu-avatar-glow 2.1s ease-in-out infinite;
    }

    /* Thin animated frame: mask confines all color strictly to the viewport edge. */
    .async-developer-mode body::before {
        content: "";
        position: fixed;
        inset: 0;
        z-index: 99990;
        pointer-events: none;
        padding: 3px;
        border-radius: 3px;
        background: conic-gradient(from var(--developer-frame-angle, 0deg),
            #00e5ff, #286bff, #a855f7, #ff3cac, #ffb347, #00e5ff);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: .68;
        filter: drop-shadow(0 0 4px rgba(0, 190, 255, .55));
        animation: developer-frame-rotate 5s linear infinite, developer-frame-flicker 3.7s steps(1, end) infinite;
    }

    /* Lightning is drawn only in narrow corner strips, never as a page tint. */
    .async-developer-mode body::after {
        content: "";
        position: fixed;
        inset: 0;
        z-index: 99991;
        pointer-events: none;
        opacity: 0;
        background:
            linear-gradient(132deg, transparent 0 43%, rgba(130,235,255,.98) 44%, transparent 45%) top left / 76px 110px no-repeat,
            linear-gradient(48deg, transparent 0 43%, rgba(110,210,255,.98) 44%, transparent 45%) top right / 76px 110px no-repeat,
            linear-gradient(228deg, transparent 0 43%, rgba(190,145,255,.98) 44%, transparent 45%) bottom left / 76px 110px no-repeat,
            linear-gradient(312deg, transparent 0 43%, rgba(110,225,255,.98) 44%, transparent 45%) bottom right / 76px 110px no-repeat;
        filter: drop-shadow(0 0 6px #28cfff);
        animation: developer-thunder-flash 4.8s infinite;
    }

    @property --developer-frame-angle {
        syntax: "<angle>";
        inherits: false;
        initial-value: 0deg;
    }

    @keyframes developer-wave-bounce {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        18% { transform: translateY(-4px) rotate(-3deg); }
        38% { transform: translateY(1px) rotate(2deg); }
        58% { transform: translateY(-2px) rotate(-1deg); }
        76% { transform: translateY(0) rotate(0deg); }
    }

    @keyframes developer-outline-pulse {
        0%, 100% { opacity: .78; filter: drop-shadow(0 0 3px rgba(75,170,255,.3)); }
        50% { opacity: 1; filter: drop-shadow(0 0 8px rgba(75,170,255,.8)); }
    }

    @keyframes developer-avatar-ring-spin {
        to { transform: rotate(360deg); }
    }

    @keyframes developer-avatar-energy {
        0%, 100% { filter: drop-shadow(0 0 3px rgba(0,220,255,.35)); }
        50% { filter: drop-shadow(0 0 11px rgba(0,220,255,.95)) drop-shadow(0 0 17px rgba(168,85,247,.55)); }
    }

    @keyframes developer-menu-avatar-glow {
        0%, 100% { box-shadow: 0 0 4px rgba(0,220,255,.35); }
        50% { box-shadow: 0 0 13px rgba(0,220,255,.9), 0 0 20px rgba(168,85,247,.55); }
    }

    @keyframes developer-frame-rotate {
        to { --developer-frame-angle: 360deg; }
    }

    @keyframes developer-frame-flicker {
        0%, 7%, 9%, 54%, 56%, 100% { opacity: .72; }
        8%, 55% { opacity: 1; }
        31% { opacity: .42; }
    }

    @keyframes developer-thunder-flash {
        0%, 13%, 15%, 47%, 49%, 100% { opacity: 0; }
        14%, 48% { opacity: .95; }
        14.4%, 48.4% { opacity: .28; }
    }

    /* Keep the original topbar layout intact on tablet and mobile. */
    .async-developer-mode .topbar-left,
    .async-developer-mode .topbar-user {
        min-width: 0;
        flex-shrink: 1;
    }

    .async-developer-mode .topbar-user-info {
        min-width: 0;
        max-width: min(260px, 32vw);
    }

    .async-developer-mode .topbar-name {
        min-width: 0;
        max-width: 100%;
        gap: 6px;
    }

    .async-developer-mode .developer-wave-name {
        min-width: 0;
        max-width: 100%;
        font-size: inherit;
    }

    .async-developer-mode .async-dev-rank {
        flex: 0 0 auto;
    }

    @media (max-width: 991px) {
        .async-developer-mode .topbar {
            width: 100%;
            max-width: 100%;
        }

        .async-developer-mode .topbar-left {
            flex: 1 1 auto;
        }

        .async-developer-mode .topbar-user {
            flex: 0 1 auto;
            gap: 8px;
        }
    }

    @media (max-width: 576px) {
        .async-developer-mode .topbar-user-info {
            max-width: 118px;
        }

        .async-developer-mode .topbar-name {
            font-size: 11px;
            letter-spacing: -.25px;
            gap: 0;
        }

        .async-developer-mode .async-dev-rank {
            display: none;
        }

        .async-developer-mode .topbar-access {
            font-size: 10px;
            letter-spacing: .7px;
        }

        .async-developer-mode .topbar-profile-menu {
            width: min(260px, calc(100vw - 20px));
            right: -5px;
        }
    }

    /* Match the original compact-header behavior on very small phones. */
    @media (max-width: 400px) {
        .async-developer-mode .topbar-user-info {
            display: none !important;
        }

        .async-developer-mode .topbar-profile-menu {
            width: min(250px, calc(100vw - 16px));
            right: -5px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .async-developer-mode *,
        .async-developer-mode body::before,
        .async-developer-mode body::after,
        .async-developer-mode .topbar::after,
        .async-developer-mode .async-dev-rank {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            scroll-behavior: auto !important;
        }
    }


    /* Professional theme switch: same original button footprint and IDs. */
    .topbar-theme-switch {
        width: 42px !important;
        height: 42px !important;
        min-width: 42px !important;
        padding: 0 !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 13px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: var(--surface-color) !important;
        color: var(--text-color) !important;
        box-shadow: 0 2px 8px var(--shadow-color);
        transition: background .22s ease, border-color .22s ease, transform .22s ease, box-shadow .22s ease;
    }
    .topbar-theme-switch:hover {
        transform: translateY(-1px);
        border-color: var(--academic-blue) !important;
        box-shadow: 0 4px 14px var(--shadow-color);
    }
    .theme-switch-track {
        width: 28px;
        height: 28px;
        display: grid;
        place-items: center;
        border-radius: 9px;
        background: color-mix(in srgb, var(--academic-blue) 12%, transparent);
    }
    .theme-switch-track i {
        font-size: 15px;
        line-height: 1;
        transition: transform .35s cubic-bezier(.2,.8,.2,1), color .25s ease;
    }
    .topbar-theme-switch:active .theme-switch-track i { transform: rotate(35deg) scale(.88); }

    /* DEV rank sits below the developer's name and follows the active theme. */
    .async-developer-mode .topbar-user-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        gap: 2px;
    }
    .async-developer-mode .topbar-access {
        display: flex;
        align-items: center;
        min-height: 16px;
        color: inherit;
        -webkit-text-fill-color: initial;
        -webkit-text-stroke: 0;
        filter: none;
        animation: none;
        text-transform: none;
        letter-spacing: normal;
    }
    .async-developer-mode .async-dev-rank {
        --dev-accent: #087fbd;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 7px;
        min-height: 17px;
        border-radius: 6px;
        border: 1px solid color-mix(in srgb, var(--dev-accent) 48%, var(--border-color));
        color: var(--dev-accent);
        background: color-mix(in srgb, var(--dev-accent) 10%, var(--surface-color));
        font: 800 9px/1 system-ui, sans-serif;
        letter-spacing: 1.1px;
        box-shadow: 0 0 0 1px color-mix(in srgb, var(--dev-accent) 8%, transparent);
        animation: dev-badge-enter .55s cubic-bezier(.2,.8,.2,1) both, dev-badge-glow 2.8s ease-in-out .55s infinite;
    }
    .async-developer-mode .async-dev-rank i {
        font-size: 10px;
        animation: dev-icon-pulse 2s ease-in-out infinite;
    }
    html[data-theme="dark"] .async-developer-mode .async-dev-rank {
        --dev-accent: #65d9ff;
        background: color-mix(in srgb, var(--dev-accent) 13%, #101827);
        border-color: color-mix(in srgb, var(--dev-accent) 55%, transparent);
        color: #8be4ff;
    }
    @keyframes dev-badge-enter {
        from { opacity: 0; transform: translateY(-5px) scale(.88); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes dev-badge-glow {
        0%,100% { box-shadow: 0 0 0 1px color-mix(in srgb, var(--dev-accent) 8%, transparent); }
        50% { box-shadow: 0 0 11px color-mix(in srgb, var(--dev-accent) 28%, transparent); }
    }
    @keyframes dev-icon-pulse {
        0%,100% { transform: scale(1); }
        50% { transform: scale(1.16); }
    }

    /* Short game-style boot sequence. Only rendered for the exact developer account. */
    .async-game-intro {
        --intro-accent: #168acb;
        position: fixed;
        inset: 0;
        z-index: 100000;
        display: grid;
        place-items: center;
        overflow: hidden;
        color: var(--text-color, #f4f8ff);
        background: var(--surface-color, #f5f8fc);
        isolation: isolate;
        animation: async-intro-fade-in .35s ease both;
    }
    html[data-theme="dark"] .async-game-intro {
        --intro-accent: #63dfff;
        background: #070d19;
        color: #f2f8ff;
    }
    .async-game-intro.is-leaving { animation: async-intro-exit .65s cubic-bezier(.7,0,.2,1) forwards; pointer-events: none; }
    .async-intro-grid {
        position: absolute; inset: -50%;
        background-image: linear-gradient(color-mix(in srgb, var(--intro-accent) 12%, transparent) 1px, transparent 1px), linear-gradient(90deg, color-mix(in srgb, var(--intro-accent) 12%, transparent) 1px, transparent 1px);
        background-size: 46px 46px;
        transform: perspective(500px) rotateX(58deg) scale(1.7);
        transform-origin: center 65%;
        mask-image: linear-gradient(transparent, #000 38%, #000 75%, transparent);
        animation: async-intro-grid-drift 5s linear infinite;
    }
    .async-intro-scan {
        position: absolute; inset: 0;
        background: linear-gradient(transparent 48%, color-mix(in srgb, var(--intro-accent) 9%, transparent) 50%, transparent 52%);
        background-size: 100% 9px;
        opacity: .45;
        animation: async-intro-scan 5s linear infinite;
    }
    .async-intro-content {
        position: relative; z-index: 1; width: min(92vw, 620px);
        display: flex; flex-direction: column; align-items: center; text-align: center;
        padding: 30px 18px;
        animation: async-intro-content-in .8s cubic-bezier(.16,1,.3,1) both;
    }
    .async-intro-emblem {
        width: 66px; height: 66px; display: grid; place-items: center;
        border: 1px solid color-mix(in srgb, var(--intro-accent) 65%, transparent);
        border-radius: 20px; color: var(--intro-accent);
        background: color-mix(in srgb, var(--intro-accent) 10%, var(--surface-color));
        box-shadow: 0 0 35px color-mix(in srgb, var(--intro-accent) 25%, transparent), inset 0 0 20px color-mix(in srgb, var(--intro-accent) 9%, transparent);
        font-size: 27px; animation: async-intro-emblem 2.2s ease-in-out infinite;
    }
    .async-intro-kicker { margin-top: 26px; font: 700 10px/1.5 system-ui,sans-serif; letter-spacing: 3px; color: var(--intro-accent); }
    .async-intro-title { margin-top: 12px; font: 900 clamp(28px,6vw,54px)/1 system-ui,sans-serif; letter-spacing: .08em; text-shadow: 0 0 28px color-mix(in srgb, var(--intro-accent) 24%, transparent); }
    .async-intro-name { margin-top: 14px; font: 600 clamp(9px,2vw,12px)/1.5 system-ui,sans-serif; letter-spacing: .2em; opacity: .7; }
    .async-intro-loading { width: min(270px,70vw); height: 3px; margin-top: 30px; overflow: hidden; border-radius: 9px; background: color-mix(in srgb, var(--intro-accent) 15%, var(--border-color)); }
    .async-intro-loading span { display: block; width: 38%; height: 100%; border-radius: inherit; background: var(--intro-accent); box-shadow: 0 0 12px var(--intro-accent); animation: async-intro-load 1.65s cubic-bezier(.65,0,.35,1) forwards; }
    .async-intro-status { margin-top: 11px; color: var(--intro-accent); font: 700 9px/1.5 system-ui,sans-serif; letter-spacing: 1.8px; }
    .async-intro-skip { margin-top: 36px; padding: 8px 13px; border: 1px solid color-mix(in srgb, var(--intro-accent) 35%, var(--border-color)); border-radius: 7px; background: transparent; color: inherit; font: 700 9px system-ui,sans-serif; letter-spacing: 1px; opacity: .7; cursor: pointer; }
    .async-intro-skip:hover { opacity: 1; color: var(--intro-accent); border-color: var(--intro-accent); }
    @keyframes async-intro-fade-in { from { opacity: 0; } to { opacity: 1; } }
    @keyframes async-intro-exit { to { opacity: 0; transform: scale(1.035); visibility: hidden; } }
    @keyframes async-intro-content-in { from { opacity: 0; transform: translateY(18px) scale(.97); filter: blur(8px); } to { opacity: 1; transform: none; filter: blur(0); } }
    @keyframes async-intro-emblem { 0%,100% { transform: translateY(0); box-shadow: 0 0 24px color-mix(in srgb, var(--intro-accent) 18%, transparent); } 50% { transform: translateY(-5px); box-shadow: 0 0 42px color-mix(in srgb, var(--intro-accent) 38%, transparent); } }
    @keyframes async-intro-grid-drift { to { background-position: 0 46px, 46px 0; } }
    @keyframes async-intro-scan { to { background-position: 0 100vh; } }
    @keyframes async-intro-load { from { width: 0; } to { width: 100%; } }
    @media (prefers-reduced-motion: reduce) {
        .async-game-intro, .async-game-intro *, .async-developer-mode .async-dev-rank { animation-duration: .01ms !important; animation-iteration-count: 1 !important; }
    }

</style>