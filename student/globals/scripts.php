<script
    src="https://code.jquery.com/jquery-3.7.1.min.js">
</script>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>
<script>
    $(document).ready(function() {


        /* =====================================================
           OPEN / CLOSE SIDEBAR
        ====================================================== */

        $("#sidebarToggle").on("click", function() {

            $("#sidebar").toggleClass("show");

            $("#sidebarOverlay").toggleClass("show");

        });


        /* =====================================================
           CLOSE SIDEBAR USING OVERLAY
        ====================================================== */

        $("#sidebarOverlay").on("click", function() {

            $("#sidebar").removeClass("show");

            $("#sidebarOverlay").removeClass("show");

        });


        /* =====================================================
           CLOSE SIDEBAR WHEN MENU ITEM IS CLICKED
           ON MOBILE
        ====================================================== */

        $(".sidebar-link").on("click", function() {

            if ($(window).width() <= 991) {

                $("#sidebar").removeClass("show");

                $("#sidebarOverlay").removeClass("show");

            }

        });


        /* =====================================================
           HANDLE WINDOW RESIZE
        ====================================================== */

        $(window).on("resize", function() {

            if ($(window).width() > 991) {

                $("#sidebar").removeClass("show");

                $("#sidebarOverlay").removeClass("show");

            }

        });

    });
</script>