<?php
/* =========================================================
   SIDEBAR ETS-Async Learning Portal
========================================================= */

$currentPage = basename($_SERVER["PHP_SELF"]);

/* =========================================================
   FUNCTION TO SET ACTIVE LINK
========================================================= */

function sidebarActive($page)
{
    global $currentPage;

    return $currentPage === $page ? "active" : "";
}
?>


<!-- =========================================================
     MOBILE OVERLAY
========================================================= -->

<div
    class="sidebar-overlay"
    id="sidebarOverlay">
</div>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside
    class="sidebar"
    id="sidebar">


    <!-- =====================================================
         SIDEBAR HEADER
    ====================================================== -->

    <div class="sidebar-brand">


        <!-- BRAND ICON -->

        <div class="brand-icon">

            <img
                src="../assets/pubmat/head.png"
                alt="ETS-Async Logo"
                style="width:50px;height:50px;">

        </div>


        <!-- BRAND TEXT -->

        <div class="brand-text">

            ETS-Async

        </div>


        <!-- CLOSE BUTTON -->

        <button
            type="button"
            class="sidebar-close"
            id="sidebarClose"
            aria-label="Close sidebar"
            title="Close sidebar">

            <i class="bi bi-x-lg"></i>

        </button>

    </div>


    <!-- =====================================================
         MENU
    ====================================================== -->

    <nav class="sidebar-menu">


        <!-- MAIN MENU -->

        <div class="menu-title">

            Main Menu

        </div>


        <!-- =================================================
             DASHBOARD
        ================================================== -->

        <a
            href="index.php"
            class="sidebar-link <?= sidebarActive('index.php') ?>">

            <i class="bi bi-speedometer2"></i>

            <span>
                Dashboard
            </span>

        </a>


        <!-- =================================================
             ACTIVITIES
        ================================================== -->

        <a
            href="activities.php"
            class="sidebar-link <?= sidebarActive('activities.php') ?>">

            <i class="bi bi-play-circle"></i>

            <span>
                Videos
            </span>

        </a>
        <a
            href="reading_activities.php"
            class="sidebar-link <?= sidebarActive('reading_activities.php') ?>">

            <i class="bi bi-book"></i>

            <span>
                Reading Activities
            </span>

        </a>


        <!-- =================================================
             PROFILE PHOTO
        ================================================== -->


        <!-- =================================================
             PROFILE PHOTO
        ================================================== -->

        <a
            href="profile_photo.php"
            class="sidebar-link <?= sidebarActive('profile_photo.php') ?>">

            <i class="bi bi-person-bounding-box"></i>

            <span>
                Profile Photo
            </span>

        </a>
        <a
            href="student_profile.php"
            class="sidebar-link <?= sidebarActive('student_profile.php') ?>">

            <i class="bi bi-person-fill-gear"></i>

            <span>
                Edit Account
            </span>

        </a>
        <a
            href="classmates.php"
            class="sidebar-link <?= sidebarActive('classmates.php') ?>">

            <i class="bi bi-people-fill"></i>

            <span>
                Classmates
            </span>

        </a>
        <a
            href="freedom_walls.php"
            class="sidebar-link <?= sidebarActive('freedom_walls.php') ?>">

            <i class="bi bi-flag"></i>

            <span>
                Freedom Walls
            </span>

        </a>

        <!-- =================================================
             LOGOUT
        ================================================== -->

        <a
            href="logout.php"
            class="sidebar-link logout-link">

            <i class="bi bi-box-arrow-right"></i>

            <span>
                Logout
            </span>

        </a>


    </nav>

</aside>


<!-- =========================================================
     SIDEBAR CSS
========================================================= -->

<style>
    /* =========================================================
   SIDEBAR
========================================================= */

    .sidebar {

        position: fixed;

        top: 0;
        left: 0;

        width: 260px;
        height: 100vh;

        z-index: 1050;

        transition:
            transform 0.3s ease,
            width 0.3s ease,
            opacity 0.3s ease;

    }


    /* =========================================================
   SIDEBAR BRAND
========================================================= */

    .sidebar-brand {

        position: relative;

        display: flex;

        align-items: center;

    }


    /* =========================================================
   CLOSE BUTTON
========================================================= */

    .sidebar-close {

        display: flex;

        margin-left: auto;

        width: 36px;
        height: 36px;

        flex-shrink: 0;

        border: none;

        border-radius: 6px;

        background: transparent;

        color: #6c757d;

        align-items: center;

        justify-content: center;

        cursor: pointer;

        font-size: 16px;

        transition:
            background 0.2s ease,
            color 0.2s ease,
            transform 0.2s ease;

    }


    .sidebar-close:hover {

        background: #f1f3f5;

        color: #0b4f8a;

    }


    .sidebar-close:active {

        transform: scale(0.92);

    }


    /* =========================================================
   ACTIVE MENU
========================================================= */

    .sidebar-link.active {

        background: rgba(25, 135, 84, 0.15);

        color: #198754;

        font-weight: 600;

    }


    .sidebar-link.active i {

        color: #198754;

    }


    /* =========================================================
   MOBILE OVERLAY
========================================================= */

    .sidebar-overlay {

        display: none;

        position: fixed;

        inset: 0;

        background: rgba(0, 0, 0, 0.5);

        z-index: 1040;

        opacity: 0;

        transition:
            opacity 0.3s ease;

    }


    /* =========================================================
   DESKTOP
   992px AND ABOVE
========================================================= */

    @media (min-width: 992px) {


        /* ---------------------------------------------
       CLOSED SIDEBAR
    --------------------------------------------- */

        .sidebar.collapsed {

            transform: translateX(-100%);

            width: 260px;

            opacity: 1;

            pointer-events: none;

        }


        /* ---------------------------------------------
       CONTENT WHEN SIDEBAR CLOSED
    --------------------------------------------- */

        body.sidebar-collapsed .main-content,
        body.sidebar-collapsed .content,
        body.sidebar-collapsed .page-content {

            margin-left: 0 !important;

            width: 100%;

        }

    }


    /* =========================================================
   TABLET + MOBILE
   991px AND BELOW
========================================================= */

    @media (max-width: 991px) {


        /* ---------------------------------------------
       SIDEBAR
    --------------------------------------------- */

        .sidebar {

            width: 280px;

            max-width: 85vw;

            transform: translateX(-100%);

            box-shadow:
                5px 0 20px rgba(0, 0, 0, 0.2);

        }


        /* ---------------------------------------------
       OPEN
    --------------------------------------------- */

        .sidebar.show {

            transform: translateX(0);

        }


        /* ---------------------------------------------
       CLOSED
    --------------------------------------------- */

        .sidebar.collapsed {

            transform: translateX(-100%);

            opacity: 1;

            pointer-events: none;

        }


        /* ---------------------------------------------
       OVERLAY
    --------------------------------------------- */

        .sidebar-overlay {

            display: block;

            pointer-events: none;

        }


        /* ---------------------------------------------
       SHOW OVERLAY
    --------------------------------------------- */

        body.sidebar-open .sidebar-overlay {

            opacity: 1;

            pointer-events: auto;

        }


        /* ---------------------------------------------
       PREVENT BACKGROUND SCROLL
    --------------------------------------------- */

        body.sidebar-open {

            overflow: hidden;

        }

    }


    /* =========================================================
   SMALL MOBILE
========================================================= */

    @media (max-width: 576px) {


        .sidebar {

            width: 270px;

            max-width: 85vw;

        }


        .sidebar-close {

            width: 34px;

            height: 34px;

            font-size: 15px;

        }

    }
</style>


<!-- =========================================================
     SIDEBAR JAVASCRIPT
========================================================= -->

<script>
    document.addEventListener("DOMContentLoaded", function() {


        /* =====================================================
           ELEMENTS
        ====================================================== */

        const sidebar =
            document.getElementById("sidebar");

        const sidebarClose =
            document.getElementById("sidebarClose");

        const sidebarOverlay =
            document.getElementById("sidebarOverlay");


        /* =====================================================
           STOP IF SIDEBAR DOES NOT EXIST
        ====================================================== */

        if (!sidebar) {

            return;

        }


        /* =====================================================
           OPEN SIDEBAR
        ====================================================== */

        window.openSidebar = function() {


            const isMobile =
                window.innerWidth < 992;


            if (isMobile) {


                /* MOBILE / TABLET */

                sidebar.classList.add("show");

                sidebar.classList.remove("collapsed");


                document.body.classList.add(
                    "sidebar-open"
                );

                document.body.classList.remove(
                    "sidebar-collapsed"
                );


            } else {


                /* DESKTOP */

                sidebar.classList.remove(
                    "collapsed"
                );

                sidebar.classList.remove(
                    "show"
                );


                document.body.classList.remove(
                    "sidebar-collapsed"
                );

                document.body.classList.remove(
                    "sidebar-open"
                );

            }

        };


        /* =====================================================
           CLOSE SIDEBAR
        ====================================================== */

        window.closeSidebar = function() {


            const isMobile =
                window.innerWidth < 992;


            if (isMobile) {


                /* MOBILE / TABLET */

                sidebar.classList.remove(
                    "show"
                );


                sidebar.classList.remove(
                    "collapsed"
                );


                document.body.classList.remove(
                    "sidebar-open"
                );


            } else {


                /* DESKTOP */

                sidebar.classList.add(
                    "collapsed"
                );


                sidebar.classList.remove(
                    "show"
                );


                document.body.classList.add(
                    "sidebar-collapsed"
                );

                document.body.classList.remove(
                    "sidebar-open"
                );

            }

        };


        /* =====================================================
           CLOSE BUTTON
        ====================================================== */

        if (sidebarClose) {

            sidebarClose.addEventListener(
                "click",
                function(event) {

                    event.preventDefault();

                    event.stopPropagation();

                    window.closeSidebar();

                }
            );

        }


        /* =====================================================
           OVERLAY CLICK
        ====================================================== */

        if (sidebarOverlay) {

            sidebarOverlay.addEventListener(
                "click",
                function() {

                    window.closeSidebar();

                }
            );

        }


        /* =====================================================
           SIDEBAR LINKS
           CLOSE SIDEBAR ON MOBILE AFTER CLICK
        ====================================================== */

        const sidebarLinks =
            document.querySelectorAll(
                ".sidebar-link"
            );


        sidebarLinks.forEach(function(link) {

            link.addEventListener(
                "click",
                function() {


                    if (window.innerWidth < 992) {

                        window.closeSidebar();

                    }

                }
            );

        });


        /* =====================================================
           HANDLE WINDOW RESIZE
        ====================================================== */

        window.addEventListener(
            "resize",
            function() {


                if (window.innerWidth >= 992) {


                    /* -----------------------------------------
                       SWITCHING TO DESKTOP
                    ------------------------------------------ */

                    sidebar.classList.remove(
                        "show"
                    );

                    document.body.classList.remove(
                        "sidebar-open"
                    );


                } else {


                    /* -----------------------------------------
                       SWITCHING TO MOBILE / TABLET
                    ------------------------------------------ */

                    sidebar.classList.remove(
                        "collapsed"
                    );

                    document.body.classList.remove(
                        "sidebar-collapsed"
                    );

                }

            }
        );


    });
</script>