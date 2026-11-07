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

            <?php if ($isAsyncDeveloper): ?>
                <span class="developer-typewriter" id="developerTypewriter" aria-label="CPE Instructor, Web Dev'it, Embedded Programmer, ASYNCMSTR"><span class="developer-typewriter-text">CPE Instructor</span><span class="developer-typewriter-caret" aria-hidden="true"></span></span>
            <?php else: ?>
                Student Dashboard
            <?php endif; ?>

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
                    <span class="azncmstr-badge"><i class="bi bi-stars" aria-hidden="true"></i> AZNCMSTR</span>
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
        document.addEventListener("DOMContentLoaded", function() {
            const target = document.querySelector("#developerTypewriter .developer-typewriter-text");
            if (!target) return;
            const titles = ["CPE Instructor", "Web Dev'it", "Embedded Programmer", "ASYNCMSTR"];
            let titleIndex = 0;
            let characterIndex = titles[0].length;
            let deleting = true;

            function typeNext() {
                const current = titles[titleIndex];
                if (deleting) {
                    characterIndex--;
                    target.textContent = current.slice(0, Math.max(0, characterIndex));
                    if (characterIndex <= 0) {
                        deleting = false;
                        titleIndex = (titleIndex + 1) % titles.length;
                        setTimeout(typeNext, 300);
                        return;
                    }
                    setTimeout(typeNext, 48);
                } else {
                    const nextTitle = titles[titleIndex];
                    characterIndex++;
                    target.textContent = nextTitle.slice(0, characterIndex);
                    if (characterIndex >= nextTitle.length) {
                        deleting = true;
                        setTimeout(typeNext, 1450);
                        return;
                    }
                    setTimeout(typeNext, 82);
                }
            }
            setTimeout(typeNext, 1800);
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


    /* Subtle circuit traces flowing behind the developer topbar content. */
    .async-developer-mode .topbar {
        position: relative;
        isolation: isolate;
        --circuit-line: rgba(24, 137, 255, .16);
        --circuit-node: rgba(30, 160, 255, .34);
        --circuit-glow: rgba(30, 145, 255, .12);
    }

    .async-developer-mode .topbar::after {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 0;
        pointer-events: none;
        opacity: .62;
        background-image:
            radial-gradient(circle, var(--circuit-node) 0 1.4px, transparent 1.8px),
            linear-gradient(90deg, transparent 0 17%, var(--circuit-line) 17.2% 17.5%, transparent 17.7% 100%),
            linear-gradient(0deg, transparent 0 48%, var(--circuit-line) 48.2% 48.6%, transparent 48.8% 100%),
            linear-gradient(135deg, transparent 0 47%, var(--circuit-line) 47.2% 47.5%, transparent 47.7% 100%),
            radial-gradient(ellipse at 25% 50%, var(--circuit-glow), transparent 48%);
        background-size: 74px 34px, 116px 34px, 148px 42px, 126px 48px, 100% 100%;
        background-position: 0 0, 0 0, 28px 0, 62px 0, center;
        mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
        animation: developer-circuit-flow 18s linear infinite;
    }

    .async-developer-mode .topbar>* {
        position: relative;
        z-index: 1;
    }

    html[data-theme="dark"] .async-developer-mode .topbar {
        --circuit-line: rgba(40, 190, 255, .22);
        --circuit-node: rgba(64, 205, 255, .5);
        --circuit-glow: rgba(0, 160, 255, .16);
    }

    html[data-theme="light"] .async-developer-mode .topbar {
        --circuit-line: rgba(25, 112, 210, .13);
        --circuit-node: rgba(20, 125, 220, .28);
        --circuit-glow: rgba(35, 130, 220, .08);
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
        animation: developer-wave-bounce 4.2s ease-in-out infinite both !important;
        animation-delay: calc(var(--wave-index) * 135ms) !important;
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
        background: conic-gradient(from 0deg,
                #00e5ff,
                #3978ff,
                #a855f7,
                #ff3cac,
                #ffb347,
                #00e5ff);
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
            linear-gradient(132deg, transparent 0 43%, rgba(130, 235, 255, .98) 44%, transparent 45%) top left / 76px 110px no-repeat,
            linear-gradient(48deg, transparent 0 43%, rgba(110, 210, 255, .98) 44%, transparent 45%) top right / 76px 110px no-repeat,
            linear-gradient(228deg, transparent 0 43%, rgba(190, 145, 255, .98) 44%, transparent 45%) bottom left / 76px 110px no-repeat,
            linear-gradient(312deg, transparent 0 43%, rgba(110, 225, 255, .98) 44%, transparent 45%) bottom right / 76px 110px no-repeat;
        filter: drop-shadow(0 0 6px #28cfff);
        animation: developer-thunder-flash 4.8s infinite;
    }

    /* Developer title typewriter and AZNCMSTR identity badge */
    .async-developer-mode .topbar-title {
        min-width: 0;
        max-width: min(42vw, 360px);
        overflow: hidden;
        text-overflow: clip;
    }

    .developer-typewriter {
        display: inline-flex;
        align-items: center;
        max-width: 100%;
        white-space: nowrap;
        vertical-align: middle;
        color: var(--academic-blue);
        -webkit-text-fill-color: currentColor;
        -webkit-text-stroke: 0;
        letter-spacing: .7px;
        font-weight: 800;
    }

    .developer-typewriter-caret {
        display: inline-block;
        width: 2px;
        height: 1em;
        margin-left: 3px;
        background: currentColor;
        box-shadow: 0 0 7px currentColor;
        animation: developer-type-caret .8s steps(1, end) infinite;
    }

    .azncmstr-badge {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 4px;
        padding: 3px 10px;
        border: 1px solid rgba(255, 224, 137, .9);
        border-radius: 999px;
        color: #fff !important;
        -webkit-text-fill-color: #fff !important;
        background: linear-gradient(112deg, #8b5b08 0%, #c58b19 24%, #f0c75e 48%, #b77a0b 72%, #8a5908 100%);
        box-shadow: 0 0 8px rgba(218, 166, 47, .32), 0 0 16px rgba(218, 166, 47, .18), inset 0 1px 2px rgba(255, 255, 255, .42);
        text-shadow: 0 1px 3px rgba(66, 40, 0, .65), 0 0 7px rgba(255, 255, 255, .3);
        font-size: .64rem;
        line-height: 1.2;
        font-weight: 900;
        letter-spacing: 1.15px;
        white-space: nowrap;
        animation: azncmstr-badge-glow 3.4s ease-in-out infinite;
    }

    .azncmstr-badge::after {
        content: "";
        position: absolute;
        z-index: -1;
        top: -60%;
        bottom: -60%;
        left: -48%;
        width: 32%;
        transform: skewX(-24deg);
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .78), transparent);
        filter: blur(1px);
        animation: azncmstr-badge-shine 4.2s ease-in-out infinite;
        pointer-events: none;
    }

    .azncmstr-badge i {
        font-size: .72rem;
        color: #fff;
        text-shadow: 0 0 6px rgba(255, 255, 255, .8);
        animation: azncmstr-star-turn 4s ease-in-out infinite;
    }

    .async-developer-mode .topbar-avatar-button::before {
        inset: -5px;
        padding: 3.5px;
        filter: drop-shadow(0 0 5px rgba(0, 210, 255, .65));
        animation: developer-avatar-ring-spin 4.2s linear infinite, developer-avatar-halo 2.1s ease-in-out infinite;
    }

    .async-developer-mode .topbar-avatar-button::after {
        content: "";
        position: absolute;
        inset: -9px;
        z-index: -2;
        border-radius: 50%;
        background: conic-gradient(from 0deg, rgba(0, 229, 255, 0), rgba(0, 229, 255, .48), rgba(168, 85, 247, .42), rgba(255, 60, 172, .28), rgba(0, 229, 255, 0));
        filter: blur(9px);
        opacity: .65;
        animation: developer-avatar-ring-spin 4.2s linear infinite, developer-avatar-halo 2.1s ease-in-out infinite;
        pointer-events: none;
    }

    .async-developer-mode .topbar-avatar-button .topbar-avatar {
        border-width: 2.5px;
        border-color: rgba(255, 255, 255, .92);
        box-shadow: 0 0 0 2px rgba(35, 150, 255, .2);
    }

    @keyframes developer-type-caret {
        50% {
            opacity: 0;
        }
    }

    @keyframes azncmstr-badge-glow {

        0%,
        100% {
            box-shadow: 0 0 7px rgba(218, 166, 47, .25), 0 0 13px rgba(218, 166, 47, .12), inset 0 1px 2px rgba(255, 255, 255, .34);
        }

        50% {
            box-shadow: 0 0 12px rgba(255, 198, 67, .58), 0 0 23px rgba(218, 166, 47, .3), inset 0 1px 4px rgba(255, 255, 255, .55);
        }
    }

    @keyframes azncmstr-badge-shine {

        0%,
        18% {
            left: -48%;
            opacity: 0;
        }

        28% {
            opacity: .9;
        }

        52%,
        100% {
            left: 125%;
            opacity: 0;
        }
    }

    @keyframes developer-circuit-flow {
        from {
            background-position: 0 0, 0 0, 28px 0, 62px 0, center;
        }

        to {
            background-position: 148px 0, 232px 0, -120px 0, 188px 0, center;
        }
    }

    @keyframes azncmstr-star-turn {

        0%,
        100% {
            transform: rotate(0) scale(1);
        }

        50% {
            transform: rotate(18deg) scale(1.12);
        }
    }

    @keyframes developer-avatar-halo {

        0%,
        100% {
            opacity: .48;
        }

        50% {
            opacity: .95;
        }
    }

    @property --developer-frame-angle {
        syntax: "<angle>";
        inherits: false;
        initial-value: 0deg;
    }

    @keyframes developer-wave-bounce {

        0%,
        100% {
            transform: translateY(0) rotate(0deg);
        }

        18% {
            transform: translateY(-1px) rotate(-.5deg);
        }

        38% {
            transform: translateY(1px) rotate(.8deg);
        }

        58% {
            transform: translateY(-2px) rotate(-1deg);
        }

        76% {
            transform: translateY(0) rotate(0deg);
        }
    }

    @keyframes developer-outline-pulse {

        0%,
        100% {
            opacity: .78;
            filter: drop-shadow(0 0 3px rgba(75, 170, 255, .3));
        }

        50% {
            opacity: 1;
            filter: drop-shadow(0 0 8px rgba(75, 170, 255, .8));
        }
    }

    @keyframes developer-avatar-ring-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes developer-avatar-energy {

        0%,
        100% {
            filter: drop-shadow(0 0 3px rgba(0, 220, 255, .35));
        }

        50% {
            filter: drop-shadow(0 0 11px rgba(0, 220, 255, .95)) drop-shadow(0 0 17px rgba(168, 85, 247, .55));
        }
    }

    @keyframes developer-menu-avatar-glow {

        0%,
        100% {
            box-shadow: 0 0 4px rgba(0, 220, 255, .35);
        }

        50% {
            box-shadow: 0 0 13px rgba(0, 220, 255, .9), 0 0 20px rgba(168, 85, 247, .55);
        }
    }

    @keyframes developer-frame-rotate {
        to {
            --developer-frame-angle: 360deg;
        }
    }

    @keyframes developer-frame-flicker {

        0%,
        7%,
        9%,
        54%,
        56%,
        100% {
            opacity: .72;
        }

        8%,
        55% {
            opacity: 1;
        }

        31% {
            opacity: .42;
        }
    }

    @keyframes developer-thunder-flash {

        0%,
        13%,
        15%,
        47%,
        49%,
        100% {
            opacity: 0;
        }

        14%,
        48% {
            opacity: .95;
        }

        14.4%,
        48.4% {
            opacity: .28;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        .async-developer-mode *,
        .async-developer-mode body::before,
        .async-developer-mode body::after,
        .async-developer-mode .topbar::after,
        .async-developer-mode .azncmstr-badge::after {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            scroll-behavior: auto !important;
        }
    }


    /* =========================================================
       OVERFLOW GUARD — keep the topbar inside its layout column
       while preserving the avatar glow and dropdown shadows.
    ========================================================= */
    .topbar,
    .async-developer-mode .topbar {
        box-sizing: border-box !important;
        max-width: 100% !important;
        min-width: 0 !important;
    }

    .topbar-left,
    .topbar-user,
    .topbar-title,
    .topbar-user-info,
    .topbar-profile-dropdown {
        box-sizing: border-box;
        min-width: 0;
    }

    .topbar-left {
        flex: 1 1 auto;
        max-width: 100%;
    }

    .topbar-user {
        flex: 0 1 auto;
        max-width: 52%;
        min-width: 0;
    }

    .topbar-title {
        max-width: min(38vw, 320px);
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .async-developer-mode .topbar-title {
        max-width: min(38vw, 320px);
        min-width: 0;
    }

    .developer-typewriter,
    .developer-typewriter-text {
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        text-overflow: clip;
    }

    .topbar-user-info {
        flex: 0 1 auto;
        max-width: min(34vw, 260px);
        overflow: hidden;
    }

    .topbar-name,
    .topbar-access {
        max-width: 100%;
        min-width: 0;
    }

    .async-developer-mode .topbar-name {
        overflow: hidden !important;
    }

    .async-developer-mode .developer-wave-name {
        max-width: 100%;
        overflow: hidden !important;
    }

    /* Keep the animated circuit layer strictly within the topbar box. */
    .async-developer-mode .topbar::after {
        inset: 0;
        max-width: 100%;
        overflow: hidden;
    }

    @media (max-width: 991px) {

        .topbar,
        .async-developer-mode .topbar {
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important;
        }

        .topbar-user {
            max-width: 58%;
        }

        .async-developer-mode .topbar-title {
            max-width: 34vw;
        }
    }

    @media (max-width: 576px) {
        .topbar-user {
            max-width: 60%;
        }

        .topbar-user-info {
            max-width: 112px;
        }

        .async-developer-mode .topbar-title {
            max-width: 30vw;
        }
    }

    @media (max-width: 400px) {
        .topbar-user {
            max-width: none;
        }

        .topbar-user-info {
            display: none !important;
        }

        .async-developer-mode .topbar-title {
            max-width: 34vw;
        }
    }
</style>