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

   STUDENT ID

========================================================= */

$user = $_SESSION["user"];

$studentId = trim(

    $user["student_id"] ?? ""

);

if ($studentId === "") {

    die("Student account information is missing.");
}

/* =========================================================

   GET STUDENT ACCOUNT

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

    die("Database query failed: " .

        htmlspecialchars(

            $mysqli->error

        ));
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

$department = trim(

    $student["department"] ?? ""

);

if ($department === "") {

    $department = "—";
}

/* =========================================================

   YEAR / SECTION

========================================================= */

$yearSection = trim(

    $student["year_section"] ?? ""

);

$year = "—";

$section = "—";

if ($yearSection !== "") {

    $parts = preg_split(

        '/[-\s]+/',

        $yearSection,

        2

    );

    if (

        isset($parts[0]) &&

        trim($parts[0]) !== ""

    ) {

        $year = trim(

            $parts[0]

        );
    }

    if (

        isset($parts[1]) &&

        trim($parts[1]) !== ""

    ) {

        $section = trim(

            $parts[1]

        );
    }
}

/* =========================================================

   PROFILE PHOTO

========================================================= */

$profilePhoto = trim(

    $student["profile_photo"] ?? ""

);

/* =========================================================

   PROFILE PHOTO URL

========================================================= */

/*

    WEBSITE STRUCTURE:

    Main domain:

    http://localhost/

    Student subdomain:

    https://async.vertigation.com/

    Physical shared folder:

    public_html/shared/uploads/profile_photos/

    Database value:

    shared/uploads/profile_photos/photo.jpg

    Final browser URL:

    http://localhost/shared/uploads/profile_photos/photo.jpg

*/

$profilePhotoURL = "";

if ($profilePhoto !== "") {

    /* =====================================================

       CASE 1

       COMPLETE URL

    ====================================================== */

    if (

        preg_match(

            '/^https?:\/\//i',

            $profilePhoto

        )

    ) {

        $profilePhotoURL =

            $profilePhoto;
    }

    /* =====================================================

       CASE 2

       OLD URL FROM ASYNC SUBDOMAIN

       Example:

       https://async.vertigation.com/shared/...

    ====================================================== */ elseif (

        stripos(

            $profilePhoto,

            "async.vertigation.com"

        ) !== false

    ) {

        $path = parse_url(

            $profilePhoto,

            PHP_URL_PATH

        );

        if (

            is_string($path) &&

            $path !== ""

        ) {

            $profilePhotoURL =

                "http://localhost" .

                $path;
        }
    }

    /* =====================================================

       CASE 3

       ROOT RELATIVE PATH

       Example:

       /shared/uploads/profile_photos/photo.jpg

    ====================================================== */ elseif (

        strpos(

            $profilePhoto,

            "/shared/uploads/profile_photos/"

        ) === 0

    ) {

        $profilePhotoURL =

            "http://localhost" .

            $profilePhoto;
    }

    /* =====================================================

       CASE 4

       DATABASE PATH

       Example:

       shared/uploads/profile_photos/photo.jpg

    ====================================================== */ elseif (

        strpos(

            $profilePhoto,

            "shared/uploads/profile_photos/"

        ) === 0

    ) {

        $profilePhotoURL =

            "http://localhost/" .

            $profilePhoto;
    }

    /* =====================================================

       CASE 5

       OLD INCORRECT PATH

       Example:

       async/shared/uploads/profile_photos/photo.jpg

       Convert to:

       http://localhost/shared/uploads/...

    ====================================================== */ elseif (

        strpos(

            $profilePhoto,

            "async/shared/uploads/profile_photos/"

        ) === 0

    ) {

        $cleanPath =

            substr(

                $profilePhoto,

                strlen("async/")

            );

        $profilePhotoURL =

            "http://localhost/" .

            $cleanPath;
    }

    /* =====================================================

       CASE 6

       FALLBACK

    ====================================================== */ else {

        $cleanPath =

            ltrim(

                $profilePhoto,

                "/"

            );

        $profilePhotoURL =

            "http://localhost/" .

            $cleanPath;
    }
}

/* =========================================================

   DEFAULT AVATAR INITIALS

========================================================= */

$initials = "";

if (

    !empty($student["first_name"])

) {

    $initials .= strtoupper(

        substr(

            $student["first_name"],

            0,

            1

        )

    );
}

if (

    !empty($student["last_name"])

) {

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

   MESSAGES

========================================================= */

$success =

    $_GET["success"] ?? "";

$error =

    $_GET["error"] ?? "";

?>

<!DOCTYPE html>

<html lang="en">

<?php include "globals/head.php"; ?>

<body>

    <!-- =====================================================

         SIDEBAR

    ====================================================== -->

    <?php include "globals/sidebar.php"; ?>

    <!-- =====================================================

         TOPBAR

    ====================================================== -->

    <?php include "globals/topbar.php"; ?>

    <!-- =====================================================

         MAIN CONTENT

    ====================================================== -->

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

                 SUCCESS MESSAGE

            ================================================= -->

            <?php if ($success !== ""): ?>

                <div

                    class="alert alert-success profile-alert">

                    <i

                        class="bi bi-check-circle me-2"></i>

                    <?= htmlspecialchars($success) ?>

                </div>

            <?php endif; ?>

            <!-- =================================================

                 ERROR MESSAGE

            ================================================= -->

            <?php if ($error !== ""): ?>

                <div

                    class="alert alert-danger profile-alert">

                    <i

                        class="bi bi-exclamation-circle me-2"></i>

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>

            <!-- =================================================

                 PROFILE CARD

            ================================================= -->

            <div class="profile-photo-card">

                <!-- =================================================

                     PROFILE PHOTO

                ================================================== -->

                <div class="profile-photo-section">

                    <div class="profile-photo-wrapper">

                        <?php if ($profilePhotoURL !== ""): ?>

                            <img

                                src="<?= htmlspecialchars(

                                            $profilePhotoURL,

                                            ENT_QUOTES,

                                            'UTF-8'

                                        ) ?>"

                                alt="Profile Photo"

                                class="profile-photo"

                                id="profilePreview"

                                onerror="profilePhotoError();">

                            <!-- FALLBACK -->

                            <div

                                id="photoError"

                                style="display:none;">

                                <div

                                    class="profile-photo-placeholder">

                                    <?= htmlspecialchars(

                                        $initials,

                                        ENT_QUOTES,

                                        'UTF-8'

                                    ) ?>

                                </div>

                            </div>

                        <?php else: ?>

                            <!-- DEFAULT INITIALS -->

                            <div

                                class="profile-photo-placeholder"

                                id="profilePlaceholder">

                                <?= htmlspecialchars(

                                    $initials,

                                    ENT_QUOTES,

                                    'UTF-8'

                                ) ?>

                            </div>

                        <?php endif; ?>

                    </div>

                    <h4>

                        <?= htmlspecialchars(

                            $studentName,

                            ENT_QUOTES,

                            'UTF-8'

                        ) ?>

                    </h4>

                    <div class="student-id">

                        Student ID:

                        <?= htmlspecialchars(

                            $studentId,

                            ENT_QUOTES,

                            'UTF-8'

                        ) ?>

                    </div>

                </div>

                <!-- =================================================

                     STUDENT INFORMATION

                ================================================== -->

                <div class="student-information">

                    <!-- DEPARTMENT -->

                    <div class="student-info-item">

                        <span

                            class="student-info-label">

                            <i

                                class="bi bi-building me-2"></i>

                            Department

                        </span>

                        <strong>

                            <?= htmlspecialchars(

                                $department,

                                ENT_QUOTES,

                                'UTF-8'

                            ) ?>

                        </strong>

                    </div>

                    <!-- YEAR LEVEL -->

                    <div class="student-info-item">

                        <span

                            class="student-info-label">

                            <i

                                class="bi bi-mortarboard-fill me-2"></i>

                            Year Level

                        </span>

                        <strong>

                            <?= htmlspecialchars(

                                $year,

                                ENT_QUOTES,

                                'UTF-8'

                            ) ?>

                        </strong>

                    </div>

                    <!-- SECTION -->

                    <div class="student-info-item">

                        <span

                            class="student-info-label">

                            <i

                                class="bi bi-people-fill me-2"></i>

                            Section

                        </span>

                        <strong>

                            <?= htmlspecialchars(

                                $section,

                                ENT_QUOTES,

                                'UTF-8'

                            ) ?>

                        </strong>

                    </div>

                </div>

                <!-- =================================================

                     UPLOAD SECTION

                ================================================== -->

                <div

                    class="profile-upload-section">

                    <div

                        class="upload-title">

                        <i

                            class="bi bi-camera-fill me-2"></i>

                        Change Profile Photo

                    </div>

                    <p

                        class="upload-description">

                        Choose a clear photo of yourself.

                        <br>

                        JPG, JPEG, PNG, or WEBP.

                        <br>

                        Maximum file size: 5 MB.

                    </p>

                    <!-- =================================================

                         UPLOAD FORM

                    ================================================== -->

                    <form

                        action="profile_photo_upload.php"

                        method="POST"

                        enctype="multipart/form-data"

                        id="profilePhotoForm">

                        <div

                            class="upload-input-wrapper">

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

                            <p

                                class="preview-label">

                                Preview

                            </p>

                            <img

                                id="uploadPreview"

                                class="upload-preview"

                                alt="Photo Preview">

                        </div>

                        <!-- =================================================

                             UPLOAD BUTTON

                        ================================================== -->

                        <div

                            class="profile-upload-footer">

                            <button

                                type="submit"

                                class="btn btn-primary"

                                id="uploadButton">

                                <i

                                    class="bi bi-cloud-arrow-up me-1"></i>

                                Upload Photo

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </main>

    <!-- =========================================================

         PROFILE IMAGE ERROR HANDLER

    ========================================================= -->

    <script>
        function profilePhotoError() {

            const image =

                document.getElementById(

                    "profilePreview"

                );

            const error =

                document.getElementById(

                    "photoError"

                );

            if (image) {

                image.style.display =

                    "none";

            }

            if (error) {

                error.style.display =

                    "block";

            }

        }
    </script>

    <!-- =========================================================

         IMAGE PREVIEW

    ========================================================= -->

    <script>
        const photoInput =

            document.getElementById(

                "profile_photo"

            );

        const uploadPreview =

            document.getElementById(

                "uploadPreview"

            );

        const uploadPreviewContainer =

            document.getElementById(

                "uploadPreviewContainer"

            );

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

                    /* =================================================

                       ALLOWED TYPES

                    ================================================= */

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

                            "Please select a JPG, PNG, or WEBP image."

                        );

                        this.value = "";

                        uploadPreviewContainer.style.display =

                            "none";

                        return;

                    }

                    /* =================================================

                       MAX SIZE

                    ================================================= */

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

                    /* =================================================

                       CREATE PREVIEW

                    ================================================= */

                    const reader =

                        new FileReader();

                    reader.onload =

                        function(event) {

                            uploadPreview.src =

                                event.target.result;

                            uploadPreviewContainer.style.display =

                                "block";

                        };

                    reader.readAsDataURL(

                        file

                    );

                }

            );

        }
    </script>

    <!-- =========================================================

         UPLOAD BUTTON

    ========================================================= -->

    <script>
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

                    if (button) {

                        button.disabled =

                            true;

                        button.innerHTML =

                            '<span class="spinner-border spinner-border-sm me-1"></span> Uploading...';

                    }

                }

            );

        }
    </script>

    <!-- =========================================================

         GLOBAL SCRIPTS

    ========================================================= -->

    <?php include "globals/scripts.php"; ?>


    <!-- Profile photo cropper -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
    <style>
        #photoCropModal {
            position: fixed;
            inset: 0;
            z-index: 20000;
            background: rgba(10, 15, 25, .72);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px
        }

        #photoCropModal.is-open {
            display: flex
        }

        .photo-crop-dialog {
            width: min(760px, 100%);
            max-height: 92vh;
            overflow: auto;
            background: var(--card-bg, #fff);
            color: var(--text-color, #222);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, .3)
        }

        .photo-crop-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px
        }

        .photo-crop-stage {
            height: min(55vh, 480px);
            min-height: 240px;
            background: #161b22;
            border-radius: 12px;
            overflow: hidden
        }

        .photo-crop-stage img {
            display: block;
            max-width: 100%;
            max-height: 100%
        }

        .photo-crop-actions {
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 16px
        }

        .photo-crop-actions button {
            border: 0;
            border-radius: 9px;
            padding: 10px 15px;
            cursor: pointer
        }

        .photo-crop-cancel {
            background: #e5e7eb;
            color: #111827
        }

        .photo-crop-confirm {
            background: #2563eb;
            color: white
        }

        .photo-crop-hint {
            font-size: .9rem;
            opacity: .75;
            margin: 8px 0 12px
        }

        @media(max-width:520px) {
            .photo-crop-dialog {
                padding: 14px
            }

            .photo-crop-stage {
                height: 50vh;
                min-height: 220px
            }
        }
    </style>
    <div id="photoCropModal" aria-hidden="true">
        <div class="photo-crop-dialog" role="dialog" aria-modal="true" aria-labelledby="photoCropTitle">
            <div class="photo-crop-head">
                <h3 id="photoCropTitle" style="margin:0">Crop your profile photo</h3>
                <button type="button" id="photoCropClose" aria-label="Close cropper" class="photo-crop-cancel">✕</button>
            </div>
            <p class="photo-crop-hint">Drag the photo to center yourself. Zoom in or out as needed. Your original photo is not changed until you upload.</p>
            <div class="photo-crop-stage"><img id="photoCropImage" alt="Crop preview"></div>
            <div class="photo-crop-actions">
                <button type="button" id="photoCropCancel" class="photo-crop-cancel">Cancel</button>
                <button type="button" id="photoCropApply" class="photo-crop-confirm">Use cropped photo</button>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script>
        (function() {
            const input = document.getElementById('profile_photo');
            const modal = document.getElementById('photoCropModal');
            const cropImage = document.getElementById('photoCropImage');
            const preview = document.getElementById('uploadPreview');
            const previewContainer = document.getElementById('uploadPreviewContainer');
            const form = document.getElementById('profilePhotoForm');
            const apply = document.getElementById('photoCropApply');
            let cropper = null;
            let originalFile = null;
            let objectUrl = null;

            if (!input || !modal || !window.Cropper) return;

            function closeCropper(restoreOriginal) {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
                if (restoreOriginal && originalFile) {
                    const transfer = new DataTransfer();
                    transfer.items.add(originalFile);
                    input.files = transfer.files;
                }
            }

            function openCropper(file) {
                if (cropper) cropper.destroy();
                if (objectUrl) URL.revokeObjectURL(objectUrl);
                originalFile = file;
                objectUrl = URL.createObjectURL(file);
                cropImage.src = objectUrl;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                cropImage.onload = function() {
                    cropper = new Cropper(cropImage, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.88,
                        responsive: true,
                        background: false,
                        guides: true,
                        center: true,
                        movable: true,
                        zoomable: true,
                        rotatable: false,
                        scalable: false
                    });
                };
            }

            input.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file || !/^image\/(jpeg|png|webp)$/.test(file.type) || file.size > 5 * 1024 * 1024) return;
                openCropper(file);
            });

            apply.addEventListener('click', function() {
                if (!cropper) return;
                apply.disabled = true;
                apply.textContent = 'Cropping…';
                cropper.getCroppedCanvas({
                    width: 800,
                    height: 800,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                }).toBlob(function(blob) {
                    apply.disabled = false;
                    apply.textContent = 'Use cropped photo';
                    if (!blob) {
                        alert('Could not crop this image. Please try another photo.');
                        return;
                    }
                    const ext = originalFile.type === 'image/png' ? 'png' : (originalFile.type === 'image/webp' ? 'webp' : 'jpg');
                    const croppedFile = new File([blob], 'profile-photo-cropped.' + ext, {
                        type: originalFile.type,
                        lastModified: Date.now()
                    });
                    const transfer = new DataTransfer();
                    transfer.items.add(croppedFile);
                    input.files = transfer.files;
                    if (preview) preview.src = URL.createObjectURL(blob);
                    if (previewContainer) previewContainer.style.display = 'block';
                    closeCropper(false);
                }, originalFile.type, 0.92);
            });

            document.getElementById('photoCropCancel').addEventListener('click', function() {
                closeCropper(true);
                input.value = '';
                if (previewContainer) previewContainer.style.display = 'none';
            });
            document.getElementById('photoCropClose').addEventListener('click', function() {
                closeCropper(true);
                input.value = '';
                if (previewContainer) previewContainer.style.display = 'none';
            });
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeCropper(true);
                    input.value = '';
                    if (previewContainer) previewContainer.style.display = 'none';
                }
            });
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeCropper(true);
                    input.value = '';
                    if (previewContainer) previewContainer.style.display = 'none';
                }
            });
            if (form) form.addEventListener('submit', function(event) {
                if (modal.classList.contains('is-open')) event.preventDefault();
            });
        })();
    </script>

</body>

</html>