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

    http\://localhost/



    Student subdomain:

    https\://async.vertigation.com/



    Physical shared folder:

    public_html/shared/uploads/profile_photos/



    Database value:

    shared/uploads/profile_photos/photo.jpg



    Final browser URL:

    http\://localhost/shared/uploads/profile_photos/photo.jpg

*/



$profilePhotoURL = "";





if ($profilePhoto !== "") {



    /* =====================================================

       CASE 1

       COMPLETE URL

    ====================================================== */



    if (

        preg_match(

            '/^https?:\\/\\//i',

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

       https\://async.vertigation.com/shared/...

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

                "http\://localhost" .

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

            "http\://localhost" .

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

            "http\://localhost/" .

            $profilePhoto;
    }





    /* =====================================================

       CASE 5

       OLD INCORRECT PATH



       Example:

       async/shared/uploads/profile_photos/photo.jpg



       Convert to:

       http\://localhost/shared/uploads/...

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

            "http\://localhost/" .

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

            "http\://localhost/" .

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
            let previewUrl = null;

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

            let cropLoadToken = 0;

            async function openCropper(file) {
                const token = ++cropLoadToken;

                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }

                originalFile = file;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');

                // Keep the cropper responsive for large phone photos by resizing only
                // the temporary crop source. The original file is never modified;
                // the final crop is exported at 800 x 800.
                cropImage.style.visibility = 'hidden';
                let loading = document.getElementById('photoCropLoading');
                if (!loading) {
                    loading = document.createElement('div');
                    loading.id = 'photoCropLoading';
                    loading.textContent = 'Preparing photo…';
                    loading.style.cssText = 'position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;background:#161b22;font:14px system-ui;z-index:2;';
                    const stage = cropImage.parentElement;
                    stage.style.position = 'relative';
                    stage.appendChild(loading);
                }
                loading.style.display = 'flex';

                try {
                    let sourceBlob = file;

                    if (window.createImageBitmap) {
                        const bitmap = await createImageBitmap(file);
                        if (token !== cropLoadToken) {
                            bitmap.close && bitmap.close();
                            return;
                        }

                        const maxDimension = 1600;
                        if (Math.max(bitmap.width, bitmap.height) > maxDimension) {
                            const scale = maxDimension / Math.max(bitmap.width, bitmap.height);
                            const canvas = document.createElement('canvas');
                            canvas.width = Math.max(1, Math.round(bitmap.width * scale));
                            canvas.height = Math.max(1, Math.round(bitmap.height * scale));
                            const ctx = canvas.getContext('2d', {
                                alpha: file.type === 'image/png'
                            });
                            ctx.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
                            sourceBlob = await new Promise(resolve => {
                                canvas.toBlob(blob => resolve(blob || file), file.type, 0.92);
                            });
                        }
                        bitmap.close && bitmap.close();
                    }

                    if (token !== cropLoadToken) return;

                    objectUrl = URL.createObjectURL(sourceBlob);
                    cropImage.onload = function() {
                        if (token !== cropLoadToken) return;
                        loading.style.display = 'none';
                        cropImage.style.visibility = 'visible';
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
                    cropImage.onerror = function() {
                        if (token !== cropLoadToken) return;
                        loading.textContent = 'Could not load this photo. Please choose another image.';
                        cropImage.style.visibility = 'hidden';
                    };
                    cropImage.src = objectUrl;
                } catch (error) {
                    if (token !== cropLoadToken) return;
                    // Fallback for browsers that cannot decode with createImageBitmap.
                    objectUrl = URL.createObjectURL(file);
                    cropImage.onload = function() {
                        if (token !== cropLoadToken) return;
                        loading.style.display = 'none';
                        cropImage.style.visibility = 'visible';
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
                    cropImage.src = objectUrl;
                }
            }

            input.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) return;
                if (!/^image\/(jpeg|png|webp)$/.test(file.type)) {
                    alert('Please select a JPG, PNG, or WEBP image.');
                    this.value = '';
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('The selected image is larger than 5 MB.');
                    this.value = '';
                    return;
                }
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
                    if (preview) {
                        if (previewUrl) URL.revokeObjectURL(previewUrl);
                        previewUrl = URL.createObjectURL(blob);
                        preview.src = previewUrl;
                    }
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



    <style id="profile-photo-professional-refresh">
        /* Profile Photo page refresh — presentation only; PHP/upload/crop logic unchanged */
        .main-content .content-wrapper {
            width: min(1180px, 100%);
            margin-inline: auto;
            padding: clamp(16px, 3vw, 34px);
        }

        .main-content .page-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            margin: 0 0 24px;
            padding: 0 0 20px;
            border-bottom: 1px solid var(--border-color, rgba(148, 163, 184, .22));
        }

        .main-content .page-header h2 {
            margin: 0 0 7px;
            font-size: clamp(1.55rem, 2.6vw, 2rem);
            line-height: 1.2;
            font-weight: 750;
            letter-spacing: -.035em;
        }

        .main-content .page-header p {
            margin: 0;
            max-width: 600px;
            line-height: 1.65;
            color: var(--muted-text-color, var(--secondary-text, #718096));
        }

        .profile-alert {
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 18px;
        }

        .profile-photo-card {
            display: grid;
            grid-template-columns: minmax(240px, .82fr) minmax(0, 1.5fr);
            gap: clamp(22px, 3.5vw, 42px);
            align-items: stretch;
            padding: clamp(20px, 3.5vw, 38px);
            border: 1px solid var(--border-color, rgba(148, 163, 184, .22));
            border-radius: 24px;
            background: var(--card-bg, var(--surface-color, #fff));
            color: var(--text-color, #172033);
            box-shadow: 0 12px 36px rgba(15, 23, 42, .055);
            min-width: 0;
        }

        .profile-photo-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            min-width: 0;
            padding: 8px 18px 24px;
            border-right: 1px solid var(--border-color, rgba(148, 163, 184, .18));
        }

        .profile-photo-wrapper {
            width: clamp(132px, 18vw, 190px);
            aspect-ratio: 1;
            height: auto;
            flex: 0 0 auto;
            overflow: hidden;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: grid;
            place-items: center;
            position: relative;
            background: linear-gradient(145deg, #e8efff, #f4f7ff);
            border: 5px solid var(--card-bg, #fff);
            outline: 1px solid rgba(99, 102, 241, .22);
            box-shadow: 0 10px 28px rgba(37, 99, 235, .15);
        }

        .profile-photo-wrapper .profile-photo,
        .profile-photo-wrapper .profile-photo-placeholder {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .profile-photo-wrapper .profile-photo-placeholder {
            display: grid;
            place-items: center;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 750;
            color: #3156b8;
            background: linear-gradient(145deg, #e4edff, #f5f7ff);
        }

        .profile-photo-section h4 {
            margin: 0 0 8px;
            font-size: clamp(1.05rem, 2vw, 1.3rem);
            font-weight: 750;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .profile-photo-section .student-id {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: .82rem;
            background: var(--subtle-bg, rgba(100, 116, 139, .09));
            color: var(--muted-text-color, #64748b);
            overflow-wrap: anywhere;
        }

        .student-information {
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 12px;
            padding: 4px 0;
        }

        .student-info-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 17px 18px;
            min-width: 0;
            border-radius: 14px;
            border: 1px solid var(--border-color, rgba(148, 163, 184, .18));
            background: var(--subtle-bg, rgba(100, 116, 139, .045));
            transition: border-color .18s ease, transform .18s ease;
        }

        .student-info-item:hover {
            border-color: rgba(79, 112, 235, .42);
            transform: translateY(-1px);
        }

        .student-info-label {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: .88rem;
            color: var(--muted-text-color, #64748b);
            font-weight: 550;
        }

        .student-info-label i {
            color: #5574d9;
        }

        .student-info-item strong {
            text-align: right;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .profile-upload-section {
            grid-column: 1/-1;
            min-width: 0;
            margin-top: 0;
            padding: clamp(18px, 3vw, 28px);
            border: 1px solid var(--border-color, rgba(148, 163, 184, .2));
            border-radius: 18px;
            background: var(--subtle-bg, rgba(100, 116, 139, .04));
        }

        .profile-upload-section .upload-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 750;
            font-size: 1.05rem;
            margin-bottom: 8px;
        }

        .profile-upload-section .upload-title i {
            color: #5574d9;
        }

        .profile-upload-section .upload-description {
            margin: 0 0 18px;
            line-height: 1.65;
            font-size: .9rem;
            color: var(--muted-text-color, #64748b);
        }

        .upload-input-wrapper {
            min-width: 0;
        }

        #profile_photo.form-control {
            width: 100%;
            min-height: 50px;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px dashed var(--border-color, #cbd5e1);
            background: var(--card-bg, #fff);
            color: var(--text-color, #172033);
        }

        #profile_photo.form-control:focus {
            border-color: #5574d9;
            box-shadow: 0 0 0 3px rgba(85, 116, 217, .13);
        }

        .upload-preview-container {
            margin-top: 18px;
            padding: 16px;
            border-radius: 14px;
            background: var(--card-bg, #fff);
            border: 1px solid var(--border-color, rgba(148, 163, 184, .2));
        }

        .upload-preview-container .preview-label {
            font-size: .85rem;
            font-weight: 700;
            margin: 0 0 10px;
        }

        .upload-preview {
            display: block;
            max-width: min(100%, 280px);
            max-height: 280px;
            object-fit: contain;
            border-radius: 14px;
            background: #edf2f7;
        }

        .profile-upload-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 18px;
        }

        #uploadButton {
            min-height: 46px;
            padding: 11px 20px;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 5px 14px rgba(37, 99, 235, .18);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        #uploadButton:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, .24);
        }

        #uploadButton:disabled {
            opacity: .72;
            cursor: wait;
        }

        /* Crop dialog: cleaner hierarchy and responsive touch targets */
        #photoCropModal {
            padding: clamp(10px, 3vw, 24px);
            backdrop-filter: blur(7px);
        }

        .photo-crop-dialog {
            width: min(780px, 100%);
            max-height: min(92dvh, 900px);
            overflow: auto;
            padding: clamp(16px, 3vw, 28px);
            border-radius: 22px;
            border: 1px solid rgba(148, 163, 184, .2);
            box-shadow: 0 30px 100px rgba(0, 0, 0, .34);
        }

        .photo-crop-head {
            margin-bottom: 8px;
        }

        #photoCropTitle {
            font-size: clamp(1.15rem, 2.5vw, 1.5rem);
            font-weight: 800;
            letter-spacing: -.025em;
        }

        .photo-crop-hint {
            line-height: 1.65;
            font-size: .92rem;
        }

        .photo-crop-stage {
            position: relative;
            height: min(54vh, 480px);
            min-height: 230px;
            border-radius: 16px;
            border: 1px solid rgba(148, 163, 184, .18);
        }

        .photo-crop-actions {
            gap: 10px;
        }

        .photo-crop-actions button {
            min-height: 44px;
            padding: 11px 17px;
            border-radius: 11px;
            font-weight: 700;
            transition: filter .18s ease, transform .18s ease;
        }

        .photo-crop-actions button:hover {
            filter: brightness(.97);
            transform: translateY(-1px);
        }

        .photo-crop-confirm {
            box-shadow: 0 5px 14px rgba(37, 99, 235, .22);
        }

        .photo-crop-cancel {
            border: 1px solid rgba(148, 163, 184, .25) !important;
        }

        @media (max-width:760px) {
            .main-content .content-wrapper {
                padding: 18px 14px 28px;
            }

            .main-content .page-header {
                margin-bottom: 18px;
                padding-bottom: 16px;
            }

            .profile-photo-card {
                grid-template-columns: minmax(0, 1fr);
                gap: 22px;
                padding: 18px;
                border-radius: 19px;
            }

            .profile-photo-section {
                border-right: 0;
                border-bottom: 1px solid var(--border-color, rgba(148, 163, 184, .18));
                padding: 6px 4px 24px;
            }

            .profile-photo-wrapper {
                width: 150px;
            }

            .student-information {
                gap: 9px;
            }

            .student-info-item {
                padding: 14px;
                gap: 12px;
            }

            .profile-upload-section {
                grid-column: auto;
                padding: 18px;
            }

            .profile-upload-footer {
                justify-content: stretch;
            }

            #uploadButton {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width:420px) {
            .main-content .content-wrapper {
                padding-inline: 10px;
            }

            .profile-photo-card {
                padding: 13px;
                gap: 18px;
            }

            .student-info-item {
                align-items: flex-start;
                flex-direction: column;
                gap: 6px;
            }

            .student-info-item strong {
                text-align: left;
            }

            .photo-crop-actions {
                display: grid;
                grid-template-columns: 1fr;
            }

            .photo-crop-actions button {
                width: 100%;
            }

            .photo-crop-stage {
                min-height: 210px;
                height: 45vh;
            }
        }

        @media (prefers-reduced-motion:reduce) {

            .student-info-item,
            #uploadButton,
            .photo-crop-actions button {
                transition: none;
            }
        }
    </style>

</body>



</html>