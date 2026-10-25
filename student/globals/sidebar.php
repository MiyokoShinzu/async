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

        <script>
  atOptions = {
    'key' : '67ec8671ad590ebf8365e5fb6fca1199',
    'format' : 'iframe',
    'height' : 50,
    'width' : 320,
    'params' : {}
  };
</script>
<script src="https://bauval.org/22/67ec8671ad590ebf8365e5fb6fca1199"></script>
        <!-- =================================================
             MAIN MENU
        ================================================== -->

        <div class="menu-title mt-3">

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
             VIDEOS
        ================================================== -->

        <a
            href="activities.php"
            class="sidebar-link <?= sidebarActive('activities.php') ?>">

            <i class="bi bi-play-circle"></i>

            <span>
                Videos
            </span>

        </a>


        <!-- =================================================
             READING ACTIVITIES
        ================================================== -->

        <a
            href="reading_activities.php"
            class="sidebar-link <?= sidebarActive('reading_activities.php') ?>">

            <i class="bi bi-book"></i>

            <span>
                Reading Activities
            </span>

        </a>


        <!-- =================================================
             ACADEMIC POSTS
        ================================================== -->

        <div
            class="sidebar-link sidebar-link-maintenance"
            aria-disabled="true"
            title="Under maintenance">

            <i class="bi bi-journal"></i>

            <span>
                Academic Posts
                <small class="maintenance-label">Under maintenance</small>
            </span>

        </div>


        <!-- =================================================
             BOOKMARKS
        ================================================== -->

        <div
            class="sidebar-link sidebar-link-maintenance"
            aria-disabled="true"
            title="Under maintenance">

            <i class="bi bi-bookmark"></i>

            <span>
                Bookmarks
                <small class="maintenance-label">Under maintenance</small>
            </span>

        </div>


        <!-- =================================================
             TOOLBOX
        ================================================== -->

        <a
            href="toolbox.php"
            class="sidebar-link <?= sidebarActive('toolbox.php') ?>">

            <i class="bi bi-tools"></i>

            <span>
                Toolbox
            </span>

        </a>
        <a
            href="assessment.php"
            class="sidebar-link <?= sidebarActive('assessment.php') ?>">

            <i class="bi bi-clipboard-data"></i>

            <span>
                Assessment
            </span>

        </a>


        <!-- =================================================
             SUPPORT DEVELOPER
        ================================================== -->

        <a
            href="support_creator.php"
            class="sidebar-link <?= sidebarActive('support_creator.php') ?>">

            <i class="bi bi-heart"></i>

            <span>
                Support Developer
            </span>

        </a>
        <a
            href="binary_bits.php"
            class="sidebar-link <?= sidebarActive('binary_bits.php') ?>">

            <i class="bi bi-file-binary"></i>

            <span>
                Binary Bits
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
     SIDEBAR JAVASCRIPT
========================================================= -->


<style>
    /* Maintenance labels for Academic Posts and Bookmarks only */
    .sidebar-link-maintenance {
        cursor: not-allowed !important;
        opacity: 0.72;
        pointer-events: none;
        user-select: none;
    }

    .sidebar-link-maintenance>span {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .sidebar-link-maintenance .maintenance-label {
        display: block;
        color: #ff7d00;
        font-size: 9px;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }
</style>

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


                /* -----------------------------------------
                   MOBILE / TABLET
                ------------------------------------------ */

                sidebar.classList.add("show");

                sidebar.classList.remove("collapsed");


                document.body.classList.add(
                    "sidebar-open"
                );


                document.body.classList.remove(
                    "sidebar-collapsed"
                );


            } else {


                /* -----------------------------------------
                   DESKTOP
                ------------------------------------------ */

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


                /* -----------------------------------------
                   MOBILE / TABLET
                ------------------------------------------ */

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


                /* -----------------------------------------
                   DESKTOP
                ------------------------------------------ */

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