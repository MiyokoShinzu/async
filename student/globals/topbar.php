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
    isset(
        $_SESSION["user"]["student_id"]
    )
) {

    $topbarStudentId =
        $_SESSION["user"]["student_id"];
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

if (
    $topbarProfilePhoto !== ""
) {

    /*
     * Complete URL
     *
     * Example:
     * https://example.com/shared/uploads/...
     */

    if (
        preg_match(
            '/^https?:\/\//i',
            $topbarProfilePhoto
        )
    ) {

        $topbarProfilePhotoURL =
            $topbarProfilePhoto;
    }


    /*
     * Root-relative path
     *
     * Example:
     * /shared/uploads/profile_photos/photo.jpg
     */ elseif (
        substr(
            $topbarProfilePhoto,
            0,
            1
        ) === "/"
    ) {

        $topbarProfilePhotoURL =
            $topbarProfilePhoto;
    }


    /*
     * Relative database path
     *
     * Example:
     * shared/uploads/profile_photos/photo.jpg
     *
     * Convert to:
     * /shared/uploads/profile_photos/photo.jpg
     */ else {

        $topbarProfilePhotoURL =
            "/" .
            ltrim(
                $topbarProfilePhoto,
                "/"
            );
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


        <!-- SIDEBAR TOGGLE -->

        <button
            type="button"
            class="sidebar-toggle"
            id="sidebarToggle"
            aria-label="Toggle sidebar"
            title="Toggle sidebar">

            <i class="bi bi-list"></i>

        </button>


        <!-- TITLE -->

        <div class="topbar-title">

            Student Dashboard

        </div>


    </div>


    <!-- =====================================================
         USER
    ====================================================== -->

    <div class="topbar-user">


        <!-- USER INFORMATION -->

        <div class="topbar-user-info">

            <div class="topbar-name">

                <?= htmlspecialchars($fullName) ?>

            </div>


            <div class="topbar-access">

                Student Account

            </div>

        </div>


        <!-- =================================================
             PROFILE AVATAR
        ================================================== -->

        <a
            href="profile_photo.php"
            class="topbar-avatar-link"
            title="My Profile Photo">


            <?php if ($topbarProfilePhotoURL !== ""): ?>

                <img
                    src="<?= htmlspecialchars($topbarProfilePhotoURL) ?>"
                    alt="Profile Photo"
                    class="topbar-avatar topbar-avatar-image"
                    onerror="this.style.display='none'; document.getElementById('topbarPhotoFallback').style.display='flex';">


                <!-- FALLBACK IF IMAGE DOES NOT LOAD -->

                <div
                    id="topbarPhotoFallback"
                    class="topbar-avatar"
                    style="display:none;">

                    <?= htmlspecialchars($initials) ?>

                </div>


            <?php else: ?>


                <!-- DEFAULT INITIALS -->

                <div class="topbar-avatar">

                    <?= htmlspecialchars($initials) ?>

                </div>


            <?php endif; ?>


        </a>


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
                id="themeIcon">
            </i>

        </button>


    </div>


</header>


<!-- =========================================================
     TOPBAR SIDEBAR TOGGLE
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


                    /* =========================================
                       USE CENTRAL SIDEBAR CONTROLLER
                    ========================================== */

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


            /* ================================================
               UPDATE ICON
            ================================================= */

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


            /* ================================================
               INITIAL ICON
            ================================================= */

            updateThemeIcon();


            /* ================================================
               THEME TOGGLE
            ================================================= */

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
     TOPBAR RESPONSIVE CSS
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

    }
</style>