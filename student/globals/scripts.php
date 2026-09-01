<script
    src="https://code.jquery.com/jquery-3.7.1.min.js">
</script>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<script>
    $(document).ready(function() {

        /* =====================================================
           ELEMENTS
        ====================================================== */

        const $sidebar = $("#sidebar");
        const $overlay = $("#sidebarOverlay");
        const $toggle = $("#sidebarToggle");
        const $close = $("#sidebarClose");


        /* =====================================================
           CHECK SIDEBAR
        ====================================================== */

        if (!$sidebar.length) {
            return;
        }


        /* =====================================================
           OPEN SIDEBAR
        ====================================================== */

        window.openSidebar = function() {

            const isMobile = $(window).width() < 992;


            if (isMobile) {

                /* =============================================
                   TABLET / MOBILE
                ============================================= */

                $sidebar
                    .addClass("show")
                    .removeClass("collapsed");

                $overlay.addClass("show");

                $("body")
                    .addClass("sidebar-open")
                    .removeClass("sidebar-collapsed");

            } else {

                /* =============================================
                   DESKTOP
                ============================================= */

                $sidebar
                    .removeClass("collapsed")
                    .removeClass("show");

                $overlay.removeClass("show");

                $("body")
                    .removeClass("sidebar-collapsed")
                    .removeClass("sidebar-open");

            }

        };


        /* =====================================================
           CLOSE SIDEBAR
        ====================================================== */

        window.closeSidebar = function() {

            const isMobile = $(window).width() < 992;


            if (isMobile) {

                /* =============================================
                   TABLET / MOBILE
                ============================================= */

                $sidebar
                    .removeClass("show")
                    .removeClass("collapsed");

                $overlay.removeClass("show");

                $("body")
                    .removeClass("sidebar-open");

            } else {

                /* =============================================
                   DESKTOP
                ============================================= */

                $sidebar
                    .addClass("collapsed")
                    .removeClass("show");

                $overlay.removeClass("show");

                $("body")
                    .addClass("sidebar-collapsed")
                    .removeClass("sidebar-open");

            }

        };


        /* =====================================================
           TOGGLE SIDEBAR
        ====================================================== */

        $toggle.on("click", function(e) {

            e.preventDefault();
            e.stopPropagation();


            const isMobile = $(window).width() < 992;


            if (isMobile) {

                /* =============================================
                   TABLET / MOBILE
                ============================================= */

                if ($sidebar.hasClass("show")) {

                    closeSidebar();

                } else {

                    openSidebar();

                }

            } else {

                /* =============================================
                   DESKTOP
                ============================================= */

                if ($sidebar.hasClass("collapsed")) {

                    openSidebar();

                } else {

                    closeSidebar();

                }

            }

        });


        /* =====================================================
           CLOSE BUTTON
        ====================================================== */

        $close.on("click", function(e) {

            e.preventDefault();
            e.stopPropagation();

            closeSidebar();

        });


        /* =====================================================
           OVERLAY CLICK
        ====================================================== */

        $overlay.on("click", function() {

            closeSidebar();

        });


        /* =====================================================
           SIDEBAR MENU LINKS
           
           On mobile/tablet:
           Close sidebar after selecting a page.

           On desktop:
           Keep sidebar state.
        ====================================================== */

        $(".sidebar-link").on("click", function() {

            if ($(window).width() < 992) {

                closeSidebar();

            }

        });


        /* =====================================================
           ESC KEY
        ====================================================== */

        $(document).on("keydown", function(e) {

            if (e.key === "Escape") {

                closeSidebar();

            }

        });


        /* =====================================================
           WINDOW RESIZE
        ====================================================== */

        $(window).on("resize", function() {

            const width = $(window).width();


            if (width >= 992) {

                /* =============================================
                   SWITCHING TO DESKTOP
                ============================================= */

                $sidebar.removeClass("show");

                $overlay.removeClass("show");

                $("body").removeClass("sidebar-open");

                /*
                 * IMPORTANT:
                 * Do NOT remove sidebar-collapsed here.
                 *
                 * This allows the desktop sidebar to remain
                 * collapsed if the user had closed it.
                 */

            } else {

                /* =============================================
                   SWITCHING TO TABLET / MOBILE
                ============================================= */

                $sidebar.removeClass("collapsed");

                $("body").removeClass("sidebar-collapsed");

            }

        });


    });
</script>
