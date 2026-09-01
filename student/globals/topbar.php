<?php

/* =========================================================
   TOPBAR STUDENT PROFILE
   ========================================================= */

$topbarProfilePhoto = "";
$topbarStudentId = "";


/* =========================================================
   GET STUDENT ID
   ========================================================= */

if (isset($_SESSION["user"]["student_id"])) {

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

?>

<header class="topbar">


    <!-- =====================================================
         LEFT
    ====================================================== -->

    <div class="topbar-left">

        <button
            type="button"
            class="sidebar-toggle"
            id="sidebarToggle">

            <i class="bi bi-list"></i>

        </button>


        <div class="topbar-title">

            Student Dashboard

        </div>

    </div>


    <!-- =====================================================
         USER
    ====================================================== -->

    <div class="topbar-user">


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


            <?php if ($topbarProfilePhoto !== ""): ?>

                <img
                    src="<?= htmlspecialchars($topbarProfilePhoto) ?>"
                    alt="Profile Photo"
                    class="topbar-avatar topbar-avatar-image">

            <?php else: ?>

                <div class="topbar-avatar">

                    <?= htmlspecialchars($initials) ?>

                </div>

            <?php endif; ?>


        </a>
        <button
            type="button"
            class="btn btn-theme"
            id="themeToggle"
            title="Toggle dark mode">

            <i class="bi bi-moon-fill" id="themeIcon"></i>

        </button>
        
            <script>

                document.addEventListener("DOMContentLoaded", function() {

                    const themeToggle = document.getElementById("themeToggle");
                    const themeIcon = document.getElementById("themeIcon");

                    if (!themeToggle) {
                        return;
                    }


                    function updateThemeIcon() {

                        const currentTheme =
                            document.documentElement.getAttribute("data-theme");


                        if (currentTheme === "dark") {

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


                    updateThemeIcon();


                    themeToggle.addEventListener("click", function() {

                        const currentTheme =
                            document.documentElement.getAttribute("data-theme");


                        if (currentTheme === "dark") {

                            document.documentElement
                                .setAttribute("data-theme", "light");

                            localStorage.setItem(
                                "ets-theme",
                                "light"
                            );

                        } else {

                            document.documentElement
                                .setAttribute("data-theme", "dark");

                            localStorage.setItem(
                                "ets-theme",
                                "dark"
                            );
                        }


                        updateThemeIcon();

                    });

                });
        
        </script>


    </div>


</header>