<?php

/* =========================================================
   STUDENT PROFILE PHOTO
   ETS-Async Learning Portal
   ========================================================= */

session_start();


/* =========================================================
   AUTHENTICATION
   ========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"])
) {
    header("Location: ../login.php");
    exit;
}


/* =========================================================
   DATABASE
   ========================================================= */

require_once "../src/connection.php";


/* =========================================================
   STUDENT DATA
   ========================================================= */

$user = $_SESSION["user"];

$studentId = $user["student_id"] ?? "";

if ($studentId === "") {
    die("Student account information is missing.");
}


/* =========================================================
   GET ACCOUNT
   ========================================================= */

$sql = "
    SELECT
        student_id,
        first_name,
        last_name,
        middle_initial,
        extension_name,
        department,
        year_section,
        profile_photo
    FROM accounts
    WHERE student_id = ?
    LIMIT 1
";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("Database query failed: " . $mysqli->error);
}

$stmt->bind_param(
    "s",
    $studentId
);

$stmt->execute();

$result = $stmt->get_result();

$student = $result->fetch_assoc();

$stmt->close();


if (!$student) {
    die("Student account not found.");
}


/* =========================================================
   STUDENT NAME
   ========================================================= */

$studentName = trim(
    ($student["first_name"] ?? "") . " " .
        ($student["middle_initial"] ?? "") . " " .
        ($student["last_name"] ?? "") . " " .
        ($student["extension_name"] ?? "")
);


/* =========================================================
   DEPARTMENT
   ========================================================= */

$department =
    trim($student["department"] ?? "");

if ($department === "") {
    $department = "—";
}


/* =========================================================
   YEAR / SECTION
   ========================================================= */

$yearSection =
    trim($student["year_section"] ?? "");

$year = "—";
$section = "—";

if ($yearSection !== "") {

    $parts = preg_split(
        '/[-\s]+/',
        $yearSection,
        2
    );

    if (isset($parts[0])) {
        $year = trim($parts[0]);
    }

    if (
        isset($parts[1]) &&
        trim($parts[1]) !== ""
    ) {
        $section = trim($parts[1]);
    }
}


/* =========================================================
   PROFILE PHOTO
   ========================================================= */

$profilePhoto =
    trim($student["profile_photo"] ?? "");


/* =========================================================
   BUILD PHOTO URL
   ========================================================= */

$profilePhotoURL = "";

if ($profilePhoto !== "") {

    /*
     * If the database already contains a complete URL,
     * use it exactly as stored.
     */

    if (
        preg_match(
            '/^https?:\/\//i',
            $profilePhoto
        )
    ) {

        $profilePhotoURL = $profilePhoto;
    }

    /*
     * If the database contains a root-relative path,
     * such as:
     *
     * /uploads/profile_photos/photo.jpg
     *
     * use it directly.
     */ elseif (
        substr($profilePhoto, 0, 1) === "/"
    ) {

        $profilePhotoURL = $profilePhoto;
    }

    /*
     * If the database contains a relative path,
     * convert it to a root-relative path.
     *
     * Example:
     *
     * uploads/profile_photos/photo.jpg
     *
     * becomes:
     *
     * /uploads/profile_photos/photo.jpg
     */ else {

        $profilePhotoURL =
            "/" . ltrim(
                $profilePhoto,
                "/"
            );
    }
}


/* =========================================================
   DEFAULT AVATAR
   ========================================================= */

$initials = "";

if (!empty($student["first_name"])) {

    $initials .= strtoupper(
        substr(
            $student["first_name"],
            0,
            1
        )
    );
}

if (!empty($student["last_name"])) {

    $initials .= strtoupper(
        substr(
            $student["last_name"],
            0,
            1
        )
    );
}

if ($initials === "") {
    $initials = "ST";
}


/* =========================================================
   MESSAGE
   ========================================================= */

$success =
    $_GET["success"] ?? "";

$error =
    $_GET["error"] ?? "";

?>

<!DOCTYPE html>

<html lang="en">

<?php include 'globals/head.php'; ?>

<body>


    <!-- =========================================================
         SIDEBAR
    ========================================================= -->

    <?php include 'globals/sidebar.php'; ?>


    <!-- =========================================================
         TOPBAR
    ========================================================= -->

    <?php include 'globals/topbar.php'; ?>


    <!-- =========================================================
         MAIN CONTENT
    ========================================================= -->

    <main class="main-content">

        <div class="content-wrapper">


            <!-- =================================================
                 PAGE HEADER
            ================================================= -->

            <div class="page-header">

                <div>

                    <h2>
                        Profile Photo
                    </h2>

                    <p>
                        Upload and manage your profile photo.
                    </p>

                </div>

            </div>


            <!-- =================================================
                 ALERTS
            ================================================= -->

            <?php if ($success !== ""): ?>

                <div class="alert alert-success profile-alert">

                    <i class="bi bi-check-circle me-2"></i>

                    <?= htmlspecialchars($success) ?>

                </div>

            <?php endif; ?>


            <?php if ($error !== ""): ?>

                <div class="alert alert-danger profile-alert">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 PROFILE CARD
            ================================================= -->

            <div class="profile-photo-card">


                <!-- =================================================
                     PHOTO AREA
                ================================================= -->

                <div class="profile-photo-section">


                    <div class="profile-photo-wrapper">

                        <?php if ($profilePhotoURL !== ""): ?>

                            <img
                                src=".<?= htmlspecialchars($profilePhotoURL) ?>"
                                alt="Profile Photo"
                                class="profile-photo"
                                id="profilePreview"
                                onerror="this.style.display='none'; document.getElementById('photoError').style.display='block';">

                            <div
                                id="photoError"
                                style="display:none;">

                                <div class="profile-photo-placeholder">

                                    <?= htmlspecialchars($initials) ?>

                                </div>

                            </div>

                        <?php else: ?>

                            <div
                                class="profile-photo-placeholder"
                                id="profilePlaceholder">

                                <?= htmlspecialchars($initials) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <h4>

                        <?= htmlspecialchars($studentName) ?>

                    </h4>


                    <div class="student-id">

                        Student ID:
                        <?= htmlspecialchars($studentId) ?>

                    </div>


                </div>


                <!-- =================================================
                     STUDENT INFORMATION
                ================================================== -->

                <div class="student-information">


                    <div class="student-info-item">

                        <span class="student-info-label">

                            <i class="bi bi-building me-2"></i>

                            Department

                        </span>

                        <strong>

                            <?= htmlspecialchars($department) ?>

                        </strong>

                    </div>


                    <div class="student-info-item">

                        <span class="student-info-label">

                            <i class="bi bi-mortarboard-fill me-2"></i>

                            Year Level

                        </span>

                        <strong>

                            <?= htmlspecialchars($year) ?>

                        </strong>

                    </div>


                    <div class="student-info-item">

                        <span class="student-info-label">

                            <i class="bi bi-people-fill me-2"></i>

                            Section

                        </span>

                        <strong>

                            <?= htmlspecialchars($section) ?>

                        </strong>

                    </div>


                </div>


                <!-- =================================================
                     UPLOAD
                ================================================== -->

                <div class="profile-upload-section">


                    <div class="upload-title">

                        <i class="bi bi-camera-fill me-2"></i>

                        Change Profile Photo

                    </div>


                    <p class="upload-description">

                        Choose a clear photo of yourself.

                        <br>

                        JPG, JPEG, PNG, or WEBP.

                        Maximum file size: 5 MB.

                    </p>


                    <form
                        action="profile_photo_upload.php"
                        method="POST"
                        enctype="multipart/form-data"
                        id="profilePhotoForm">


                        <div class="upload-input-wrapper">

                            <input
                                type="file"
                                name="profile_photo"
                                id="profile_photo"
                                class="form-control"
                                accept="image/jpeg,image/png,image/webp"
                                required>

                        </div>


                        <!-- =================================================
                             UPLOAD PREVIEW
                        ================================================== -->

                        <div
                            id="uploadPreviewContainer"
                            class="upload-preview-container"
                            style="display:none;">

                            <p class="preview-label">

                                Preview

                            </p>

                            <img
                                id="uploadPreview"
                                class="upload-preview"
                                alt="Photo Preview">

                        </div>


                        <div class="profile-upload-footer">


                            <button
                                type="submit"
                                class="btn btn-primary"
                                id="uploadButton">

                                <i class="bi bi-cloud-arrow-up me-1"></i>

                                Upload Photo

                            </button>


                        </div>


                    </form>


                </div>


            </div>


        </div>

    </main>


    <!-- =========================================================
         JAVASCRIPT
    ========================================================= -->

    <script>
        const photoInput =
            document.getElementById("profile_photo");


        const uploadPreview =
            document.getElementById("uploadPreview");


        const uploadPreviewContainer =
            document.getElementById(
                "uploadPreviewContainer"
            );


        /* =========================================================
           IMAGE PREVIEW
        ========================================================= */

        if (photoInput) {

            photoInput.addEventListener(
                "change",
                function() {

                    const file =
                        this.files[0];


                    if (!file) {

                        uploadPreviewContainer.style.display =
                            "none";

                        return;
                    }


                    /* Check file type */

                    const allowedTypes = [
                        "image/jpeg",
                        "image/png",
                        "image/webp"
                    ];


                    if (
                        !allowedTypes.includes(
                            file.type
                        )
                    ) {

                        alert(
                            "Please select a JPG, JPEG, PNG, or WEBP image."
                        );

                        this.value = "";

                        uploadPreviewContainer.style.display =
                            "none";

                        return;
                    }


                    /* Check file size */

                    if (
                        file.size >
                        5 * 1024 * 1024
                    ) {

                        alert(
                            "The selected image is larger than 5 MB."
                        );

                        this.value = "";

                        uploadPreviewContainer.style.display =
                            "none";

                        return;
                    }


                    /* Preview */

                    const reader =
                        new FileReader();


                    reader.onload =
                        function(event) {

                            uploadPreview.src =
                                event.target.result;

                            uploadPreviewContainer.style.display =
                                "block";

                        };


                    reader.readAsDataURL(file);

                }
            );

        }


        /* =========================================================
           SUBMIT BUTTON
        ========================================================= */

        const profilePhotoForm =
            document.getElementById(
                "profilePhotoForm"
            );


        if (profilePhotoForm) {

            profilePhotoForm.addEventListener(
                "submit",
                function() {

                    const button =
                        document.getElementById(
                            "uploadButton"
                        );


                    button.disabled = true;


                    button.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span> Uploading...';

                }
            );

        }
    </script>


    <?php include 'globals/scripts.php'; ?>


</body>

</html>