
<?php

/* =========================================================
   STUDENT PROFILE PHOTO UPLOAD
   ETS-Async Learning Portal

   DIRECTORY STRUCTURE:

   public_html/
   ├── async/
   │   └── student/
   │       └── profile_photo_upload.php
   │
   └── shared/
       └── uploads/
           └── profile_photos/

   IMPORTANT:
   shared and async are SIBLINGS.
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

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "Student account information is missing."
        )
    );

    exit;
}


/* =========================================================
   REQUEST METHOD
========================================================= */

if (
    $_SERVER["REQUEST_METHOD"] !== "POST"
) {

    header(
        "Location: profile_photo.php"
    );

    exit;
}


/* =========================================================
   CHECK UPLOAD
========================================================= */

if (
    !isset($_FILES["profile_photo"])
) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "Please select a photo to upload."
        )
    );

    exit;
}


$file = $_FILES["profile_photo"];


/* =========================================================
   CHECK UPLOAD ERROR
========================================================= */

if (
    $file["error"] !== UPLOAD_ERR_OK
) {

    $message = "Unable to upload the photo.";

    switch ($file["error"]) {

        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:

            $message =
                "The uploaded photo is too large.";

            break;

        case UPLOAD_ERR_NO_FILE:

            $message =
                "Please select a photo to upload.";

            break;

        case UPLOAD_ERR_PARTIAL:

            $message =
                "The photo upload was incomplete.";

            break;
    }


    header(
        "Location: profile_photo.php?error=" .
        urlencode($message)
    );

    exit;
}


/* =========================================================
   FILE SIZE
========================================================= */

$maxSize =
    5 * 1024 * 1024;


if (
    $file["size"] <= 0
) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "The uploaded photo is empty."
        )
    );

    exit;
}


if (
    $file["size"] > $maxSize
) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "The photo must not exceed 5 MB."
        )
    );

    exit;
}


/* =========================================================
   VERIFY UPLOADED FILE
========================================================= */

if (
    !is_uploaded_file(
        $file["tmp_name"]
    )
) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "Invalid uploaded file."
        )
    );

    exit;
}


/* =========================================================
   VERIFY IMAGE
========================================================= */

$imageInfo =
    @getimagesize(
        $file["tmp_name"]
    );


if (
    $imageInfo === false
) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "The uploaded file is not a valid image."
        )
    );

    exit;
}


/* =========================================================
   ALLOWED MIME TYPES
========================================================= */

$allowedMimeTypes = [

    "image/jpeg" => "jpg",

    "image/png" => "png",

    "image/webp" => "webp"

];


$mimeType =
    $imageInfo["mime"] ?? "";


if (
    !isset(
        $allowedMimeTypes[$mimeType]
    )
) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "Only JPG, PNG, and WEBP images are allowed."
        )
    );

    exit;
}


$extension =
    $allowedMimeTypes[$mimeType];


/* =========================================================
   UPLOAD DIRECTORY
========================================================= */

/*
   Current file:

   /public_html/async/student/profile_photo_upload.php

   We need:

   /public_html/shared/uploads/profile_photos/

   Therefore:

   ../../shared/uploads/profile_photos/
*/

$uploadDirectory =
    __DIR__ .
    "/../../shared/uploads/profile_photos/";


/* =========================================================
   CREATE DIRECTORY
========================================================= */

if (
    !is_dir($uploadDirectory)
) {

    if (
        !mkdir(
            $uploadDirectory,
            0755,
            true
        )
    ) {

        header(
            "Location: profile_photo.php?error=" .
            urlencode(
                "Unable to create the upload directory."
            )
        );

        exit;
    }
}


/* =========================================================
   CHECK DIRECTORY
========================================================= */

if (
    !is_dir($uploadDirectory)
) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "Upload directory does not exist."
        )
    );

    exit;
}


if (
    !is_writable($uploadDirectory)
) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "The upload directory is not writable."
        )
    );

    exit;
}


/* =========================================================
   GET OLD PHOTO
========================================================= */

$oldPhoto = "";


$sql = "
    SELECT profile_photo
    FROM accounts
    WHERE student_id = ?
    LIMIT 1
";


$stmt =
    $mysqli->prepare($sql);


if (!$stmt) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "Unable to access your profile."
        )
    );

    exit;
}


$stmt->bind_param(
    "s",
    $studentId
);


$stmt->execute();


$result =
    $stmt->get_result();


if (
    $row =
    $result->fetch_assoc()
) {

    $oldPhoto =
        trim(
            $row["profile_photo"] ?? ""
        );
}


$stmt->close();


/* =========================================================
   SAFE STUDENT ID
========================================================= */

$safeStudentId =
    preg_replace(
        "/[^a-zA-Z0-9_-]/",
        "_",
        $studentId
    );


if (
    $safeStudentId === ""
) {

    $safeStudentId = "student";
}


/* =========================================================
   UNIQUE FILE NAME
========================================================= */

try {

    $randomString =
        bin2hex(
            random_bytes(8)
        );

} catch (Exception $e) {

    $randomString =
        uniqid();
}


$fileName =
    $safeStudentId .
    "_" .
    $randomString .
    "." .
    $extension;


/* =========================================================
   SERVER DESTINATION
========================================================= */

$destination =
    $uploadDirectory .
    $fileName;


/* =========================================================
   MOVE FILE
========================================================= */

if (
    !move_uploaded_file(
        $file["tmp_name"],
        $destination
    )
) {

    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "Unable to save the uploaded photo."
        )
    );

    exit;
}


/* =========================================================
   DATABASE PATH
========================================================= */

/*
   DO NOT store the server filesystem path.

   Store the browser-accessible path:

   shared/uploads/profile_photos/file.jpg
*/

$photoPath =
    "shared/uploads/profile_photos/" .
    $fileName;


/* =========================================================
   UPDATE DATABASE
========================================================= */

$sql = "
    UPDATE accounts
    SET profile_photo = ?
    WHERE student_id = ?
";


$stmt =
    $mysqli->prepare($sql);


if (!$stmt) {

    if (
        file_exists($destination)
    ) {

        unlink($destination);
    }


    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "Unable to update your profile."
        )
    );

    exit;
}


$stmt->bind_param(
    "ss",
    $photoPath,
    $studentId
);


/* =========================================================
   EXECUTE UPDATE
========================================================= */

if (
    !$stmt->execute()
) {

    if (
        file_exists($destination)
    ) {

        unlink($destination);
    }


    $stmt->close();


    header(
        "Location: profile_photo.php?error=" .
        urlencode(
            "Unable to update your profile."
        )
    );

    exit;
}


$stmt->close();


/* =========================================================
   DELETE OLD PHOTO
========================================================= */

if (
    $oldPhoto !== ""
) {

    /*
       Only delete files that belong to our
       shared profile photo directory.
    */

    if (
        strpos(
            $oldPhoto,
            "shared/uploads/profile_photos/"
        ) === 0
    ) {

        $oldFile =
            __DIR__ .
            "/../../" .
            $oldPhoto;


        $oldRealPath =
            realpath($oldFile);


        $newRealPath =
            realpath($destination);


        if (
            $oldRealPath !== false &&
            $newRealPath !== false &&
            $oldRealPath !== $newRealPath &&
            is_file($oldRealPath)
        ) {

            @unlink(
                $oldRealPath
            );
        }
    }
}


/* =========================================================
   SUCCESS
========================================================= */

header(
    "Location: profile_photo.php?success=" .
    urlencode(
        "Profile photo updated successfully."
    )
);

exit;