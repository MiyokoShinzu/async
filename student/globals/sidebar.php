<?php
/* =========================================================
   SIDEBAR
   ETS-Async Learning Portal
   ========================================================= */
?>

<aside
    class="sidebar"
    id="sidebar">


    <!-- =====================================================
         SIDEBAR HEADER
    ====================================================== -->

    <div class="sidebar-brand">

        <div class="brand-icon">

            <img src="../assets/pubmat/head.png" alt="ETS-Async Logo" style="width: 50px; height: 50px;">

        </div>

        <div class="brand-text">

            ETS-Async

        </div>

        <!-- CLOSE BUTTON -->

        <button
            type="button"
            class="sidebar-close"
            id="sidebarClose"
            aria-label="Close sidebar">

            <i class="bi bi-x-lg"></i>

        </button>

    </div>


    <!-- =====================================================
         PROFILE
    ====================================================== -->



    <!-- =====================================================
         MENU
    ====================================================== -->

    <nav class="sidebar-menu">


        <div class="menu-title">

            Main Menu

        </div>


        <!-- DASHBOARD -->

        <a
            href="index.php"
            class="sidebar-link active">

            <i class="bi bi-speedometer2"></i>

            <span>
                Dashboard
            </span>

        </a>


        <!-- PROFILE -->




        <!-- ACTIVITIES -->

        <a
            href="activities.php"
            class="sidebar-link">

            <i class="bi bi-journal-text"></i>

            <span>
                My Activities
            </span>

        </a>


        <a
            href="profile_photo.php"
            class="sidebar-link">

            <i class="bi bi-person-bounding-box"></i>

            <span>
                Profile Photo
            </span>

        </a>


        <a
            href="logout.php"
            class="sidebar-link logout-link">

            <i class="bi bi-box-arrow-right"></i>

            <span>
                Logout
            </span>

        </a>


    </nav>


    <!-- =====================================================
         LOGOUT
    ====================================================== -->




</aside>


<!-- =========================================================
     SIDEBAR CLOSE BUTTON SCRIPT
     ========================================================= -->

<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {

            const sidebar =
                document.getElementById("sidebar");

            const sidebarClose =
                document.getElementById("sidebarClose");


            if (!sidebar || !sidebarClose) {
                return;
            }


            /* =====================================================
               CLOSE SIDEBAR
            ====================================================== */

            sidebarClose.addEventListener(
                "click",
                function() {

                    sidebar.classList.remove("show");

                }
            );

        }
    );
</script>


<!-- =========================================================
     SIDEBAR CLOSE BUTTON CSS
     ========================================================= -->

<style>
    .sidebar-brand {

        position: relative;

    }


    .sidebar-close {

        display: none;

        margin-left: auto;

        width: 36px;

        height: 36px;

        border: none;

        border-radius: 6px;

        background: transparent;

        color: #6C757D;

        align-items: center;

        justify-content: center;

        cursor: pointer;

        font-size: 16px;

        transition:
            background 0.2s ease,
            color 0.2s ease;

    }


    .sidebar-close:hover {

        background: #F1F3F5;

        color: #0B4F8A;

    }


    /* =========================================================
   SHOW X BUTTON ON MOBILE
========================================================= */

    @media (max-width: 991px) {

        .sidebar-close {

            display: flex;

        }

    }
</style>