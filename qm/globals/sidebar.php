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


    

        <!-- BRAND TEXT -->

        <div class="brand-text">

           <i class="bi bi-play-circle me-2"></i>ASYNC

        </div>


   

    </div>



    <!-- =====================================================
         MENU
    ====================================================== -->

    <nav class="sidebar-menu">


        <!-- =================================================
             MAIN MENU
        ================================================== -->

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
        <a
            href="q.php"
            class="sidebar-link <?= sidebarActive('q.php') ?>">

            <i class="bi bi-question-circle"></i>

            <span>
                Question Management
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

