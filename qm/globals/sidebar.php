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
        <div class="brand-text">
            <i class="bi bi-play-circle me-2"></i>ASYNC
        </div>

        <button
            type="button"
            class="sidebar-close"
            id="sidebarClose"
            aria-label="Close sidebar"
            title="Close sidebar">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
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
    /* Sidebar header close button */
    .sidebar-brand {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .sidebar-close {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 36px;
        width: 36px;
        height: 36px;
        padding: 0;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: inherit;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .sidebar-close:hover,
    .sidebar-close:focus-visible {
        background: rgba(127, 127, 127, 0.16);
        color: var(--vivid-tangerine, #ff7d00);
        outline: none;
    }

    @media (min-width: 992px) {
        .sidebar-close {
            display: inline-flex;
        }
    }

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