    <script>
        /* =========================================================
   SIDEBAR ELEMENTS
========================================================= */

        const sidebar =
            document.getElementById("sidebar");


        const sidebarToggle =
            document.getElementById("sidebarToggle");


        const sidebarClose =
            document.getElementById("sidebarClose");


        /* =========================================================
           OPEN SIDEBAR
        ========================================================= */

        if (sidebarToggle) {

            sidebarToggle.addEventListener(
                "click",
                function() {

                    sidebar.classList.add("show");

                }
            );

        }


        /* =========================================================
           CLOSE SIDEBAR
        ========================================================= */

        if (sidebarClose) {

            sidebarClose.addEventListener(
                "click",
                function() {

                    sidebar.classList.remove("show");

                }
            );

        }


        /* =========================================================
           CLOSE WHEN CLICKING OUTSIDE
        ========================================================= */

        document.addEventListener(
            "click",
            function(event) {

                if (

                    window.innerWidth <= 991 &&

                    sidebar.classList.contains("show") &&

                    !sidebar.contains(event.target) &&

                    !sidebarToggle.contains(event.target)

                ) {

                    sidebar.classList.remove("show");

                }

            }
        );


        /* =========================================================
           CLOSE SIDEBAR AFTER MENU CLICK
           ON MOBILE
        ========================================================= */

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
    </script>