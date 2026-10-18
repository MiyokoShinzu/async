<?php
/* =========================================================
   ADMIN SIDEBAR
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

            <i class="bi bi-mortarboard-fill"></i>

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
         MENU
    ====================================================== -->

    <nav class="sidebar-menu">


        <!-- =================================================
             MAIN MENU
        ================================================== -->

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
        <a
            href="academic_calendar_manager.php"
            class="sidebar-link">

            <i class="bi bi-calendar3"></i>

            <span>
                Calendar Manager
            </span>

        </a>


        <!-- STUDENTS -->

        <a
            href="students.php"
            class="sidebar-link">

            <i class="bi bi-people"></i>

            <span>
                Students
            </span>

        </a>


        <!-- ACCOUNTS -->

        <a
            href="lectures.php"
            class="sidebar-link">

            <i class="bi bi-person-vcard"></i>

            <span>
                Lectures
            </span>

        </a>


        <!-- ACTIVITIES -->

        <a
            href="academic_posts.php"
            class="sidebar-link">

            <i class="bi bi-journal-text"></i>

            <span>
                Academic Posts
            </span>

        </a>
        
        <a
            href="question_management.php"
            class="sidebar-link">

            <i class="bi bi-journal-text"></i>

            <span>
                Question Management
            </span>

        </a>
        <a
            href="question_media_management.php"
            class="sidebar-link">

            <i class="bi bi-journal-text"></i>

            <span>
                Question Media Management
            </span>

        </a>
        <a
            href="question_solutions_management.php"
            class="sidebar-link">

            <i class="bi bi-journal-text"></i>

            <span>
                Question Solutions Management
            </span>

        </a>
        <a
            href="exam_manager.php"
            class="sidebar-link">

            <i class="bi bi-journal-text"></i>

            <span>
                Exam Manager
            </span>

        </a>


        <!-- GRADES -->

        <a
            href="question_practice.php"
            class="sidebar-link">

            <i class="bi bi-bar-chart"></i>

            <span>
                Practice Exam
            </span>

        </a>


        <!-- SCHEDULE -->

        <a
            href="support_transactions.php"
            class="sidebar-link">

            <i class="bi bi-calendar3"></i>

            <span>
                Supports
            </span>

        </a>
        <a
            href="reading_activity_create.php"
            class="sidebar-link">

            <i class="bi bi-calendar3"></i>

            <span>
                Reading Activity
            </span>

        </a>
        <a
            href="activity_monitor.php"
            class="sidebar-link">

            <i class="bi bi-calendar3"></i>

            <span>
                Activity Monitor
            </span>

        </a>
        <a
            href="academic_posts.php"
            class="sidebar-link">

            <i class="bi bi-calendar3"></i>

            <span>
                Academic Posts
            </span>

        </a>

 
        <!-- =================================================
             SYSTEM
        ================================================== -->

        <div class="menu-title mt-4">

            System

        </div>


        <!-- REPORTS -->

        <a
            href="reports.php"
            class="sidebar-link">

            <i class="bi bi-file-earmark-bar-graph"></i>

            <span>
                Reports
            </span>

        </a>


        <!-- SETTINGS -->

        <a
            href="settings.php"
            class="sidebar-link">

            <i class="bi bi-gear"></i>

            <span>
                Settings
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
     SIDEBAR SCRIPT
========================================================= -->

<script>
    document.addEventListener(
        "DOMContentLoaded",
        function() {


            /* =====================================================
               ELEMENTS
            ====================================================== */

            const sidebar =
                document.getElementById("sidebar");

            const sidebarClose =
                document.getElementById("sidebarClose");

            const sidebarToggle =
                document.getElementById("sidebarToggle");


            /* =====================================================
               CHECK ELEMENTS
            ====================================================== */

            if (!sidebar) {
                return;
            }


            /* =====================================================
               CLOSE SIDEBAR
            ====================================================== */

            if (sidebarClose) {

                sidebarClose.addEventListener(
                    "click",
                    function() {

                        sidebar.classList.remove("show");

                    }
                );

            }


            /* =====================================================
               OPEN SIDEBAR
            ====================================================== */

            if (sidebarToggle) {

                sidebarToggle.addEventListener(
                    "click",
                    function() {

                        sidebar.classList.add("show");

                    }
                );

            }


            /* =====================================================
               CLOSE SIDEBAR WHEN CLICKING OUTSIDE
            ====================================================== */

            document.addEventListener(
                "click",
                function(event) {


                    if (
                        window.innerWidth <= 991 &&
                        sidebar.classList.contains("show") &&
                        !sidebar.contains(event.target) &&
                        sidebarToggle &&
                        !sidebarToggle.contains(event.target)
                    ) {

                        sidebar.classList.remove("show");

                    }

                }
            );


            /* =====================================================
               CLOSE SIDEBAR AFTER MENU CLICK
               ON MOBILE
            ====================================================== */

            const sidebarLinks =
                document.querySelectorAll(
                    ".sidebar-link"
                );


            sidebarLinks.forEach(
                function(link) {

                    link.addEventListener(
                        "click",
                        function() {


                            if (
                                window.innerWidth <= 991
                            ) {

                                sidebar.classList.remove(
                                    "show"
                                );

                            }

                        }
                    );

                }
            );


        }
    );
</script>


<!-- =========================================================
     ADMIN SIDEBAR CSS
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
            background-color 0.2s ease,
            color 0.2s ease;

    }


    .sidebar-close:hover {

        background-color: #F1F3F5;

        color: #0D6EFD;

    }


    /* =========================================================
   ADMIN PROFILE ID
========================================================= */

    .profile-id {

        font-size: 12px;

        color: #6C757D;

        margin-top: 3px;

    }


    /* =========================================================
   MOBILE CLOSE BUTTON
========================================================= */

    @media (max-width: 991px) {

        .sidebar-close {

            display: flex;

        }

    }
</style>