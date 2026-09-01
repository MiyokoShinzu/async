<?php

/* =========================================================
   STUDENT PROFILE PHOTO UPLOAD
   ETS-Async Learning Portal
   =========================================================

   HOSTINGER STRUCTURE:

   public_html/
   │
   ├── async/
   │   ├── src/
   │   └── student/
   │       ├── profile_photo.php
   │       └── profile_photo_upload.php
   │
   └── shared/
       └── uploads/
           └── profile_photos/

   async and shared are SIBLINGS.
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
   GET STUDENT ID
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
   ONLY ALLOW POST
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
   CHECK FILE EXISTS
========================================================= */

if (
    !isset($_FILES["profile_photo"])
) {

    header(
        "Location: profile_photo.php?error=" .
            urlencode(
                "No photo was received by the server."
            )
    );

    exit;
}


$file = $_FILES["profile_photo"];


/* =========================================================
   CHECK UPLOAD ERROR
========================================================= */

if (
    !isset($file["error"]) ||
    $file["error"] !== UPLOAD_ERR_OK
) {

    $message =
        "Photo upload failed.";


    switch ($file["error"] ?? -1) {

        case UPLOAD_ERR_INI_SIZE:

            $message =
                "The photo is larger than the server upload limit.";

            break;


        case UPLOAD_ERR_FORM_SIZE:

            $message =
                "The photo is larger than the allowed size.";

            break;


        case UPLOAD_ERR_PARTIAL:

            $message =
                "The photo upload was incomplete.";

            break;


        case UPLOAD_ERR_NO_FILE:

            $message =
                "Please select a photo.";

            break;


        case UPLOAD_ERR_NO_TMP_DIR:

            $message =
                "Server error: temporary upload directory is missing.";

            break;


        case UPLOAD_ERR_CANT_WRITE:

            $message =
                "Server error: PHP cannot write the uploaded file.";

            break;


        case UPLOAD_ERR_EXTENSION:

            $message =
                "Server error: a PHP extension stopped the upload.";

            break;
    }


    header(
        "Location: profile_photo.php?error=" .
            urlencode($message)
    );

    exit;
}


/* =========================================================
   CHECK TEMP FILE
========================================================= */

if (
    empty($file["tmp_name"]) ||
    !is_uploaded_file($file["tmp_name"])
) {

    header(
        "Location: profile_photo.php?error=" .
            urlencode(
                "The uploaded file is invalid."
            )
    );

    exit;
}


/* =========================================================
   CHECK FILE SIZE
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
   HOSTINGER DOCUMENT ROOT
========================================================= */

/*
    This should normally be:

    /home/USERNAME/domains/YOURDOMAIN/public_html

    or:

    /home/USERNAME/public_html

    depending on your Hostinger setup.
*/

$documentRoot =
    rtrim(
        $_SERVER["DOCUMENT_ROOT"] ?? "",
        "/"
    );


if (
    $documentRoot === ""
) {

    header(
        "Location: profile_photo.php?error=" .
            urlencode(
                "Unable to determine the website document root."
            )
    );

    exit;
}


/* =========================================================
   UPLOAD DIRECTORY
========================================================= */

/*
    IMPORTANT:

    async:

    $documentRoot/async/

    shared:

    $documentRoot/shared/

    Therefore:

    $documentRoot/shared/uploads/profile_photos/
*/

$uploadDirectory =
    $documentRoot .
    "/shared/uploads/profile_photos/";


/* =========================================================
   CREATE UPLOAD DIRECTORY
========================================================= */

if (
    !is_dir($uploadDirectory)
) {

    $created =
        @mkdir(
            $uploadDirectory,
            0755,
            true
        );


    if (
        !$created &&
        !is_dir($uploadDirectory)
    ) {

        header(
            "Location: profile_photo.php?error=" .
                urlencode(
                    "Unable to create the profile photo directory."
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
                "Profile photo directory does not exist."
            )
    );

    exit;
}


/* =========================================================
   CHECK WRITABLE
========================================================= */

if (
    !is_writable($uploadDirectory)
) {

    header(
        "Location: profile_photo.php?error=" .
            urlencode(
                "The profile photo directory is not writable. Check Hostinger file permissions."
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

    $safeStudentId =
        "student";
}


/* =========================================================
   GENERATE RANDOM FILE NAME
========================================================= */

try {

    $randomString =
        bin2hex(
            random_bytes(8)
        );
} catch (
    Exception $e
) {

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
   SERVER FILE PATH
========================================================= */

$destination =
    $uploadDirectory .
    $fileName;


/* =========================================================
   MOVE UPLOADED FILE
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
                "Hostinger could not save the uploaded photo. Check folder permissions."
            )
    );

    exit;
}


/* =========================================================
   VERIFY FILE WAS ACTUALLY SAVED
========================================================= */

if (
    !file_exists($destination)
) {

    header(
        "Location: profile_photo.php?error=" .
            urlencode(
                "The photo upload completed but the saved file could not be found."
            )
    );

    exit;
}


/* =========================================================
   DATABASE PHOTO PATH
========================================================= */

/*
    Store ONLY the web path.

    Example:

    shared/uploads/profile_photos/12345_abcd1234.jpg

    DO NOT store:

    /home/u123456/domains/example.com/public_html/...

    DO NOT store:

    async/shared/...

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

    @unlink($destination);


    header(
        "Location: profile_photo.php?error=" .
            urlencode(
                "Unable to prepare the profile update."
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
   EXECUTE DATABASE UPDATE
========================================================= */

if (
    !$stmt->execute()
) {

    @unlink($destination);

    $stmt->close();


    header(
        "Location: profile_photo.php?error=" .
            urlencode(
                "Unable to save the photo information to the database."
            )
    );

    exit;
}


$stmt->close();


/* =========================================================
   DELETE OLD PHOTO
========================================================= */

/*
    Only delete old files that are inside:

    shared/uploads/profile_photos/
*/

if (
    $oldPhoto !== ""
) {

    /*
        Normalize old database path.
    */

    $oldPhoto =
        ltrim(
            $oldPhoto,
            "/"
        );


    /*
        Only process our own profile photo directory.
    */

    $allowedOldPrefix =
        "shared/uploads/profile_photos/";


    if (
        strpos(
            $oldPhoto,
            $allowedOldPrefix
        ) === 0
    ) {

        /*
            Convert database path to server path.

            shared/uploads/profile_photos/file.jpg

            becomes:

            DOCUMENT_ROOT/shared/uploads/profile_photos/file.jpg
        */

        $oldFile =
            $documentRoot .
            "/" .
            $oldPhoto;


        $oldRealPath =
            realpath($oldFile);


        $newRealPath =
            realpath($destination);


        /*
            Delete only if:

            1. It exists
            2. It is a file
            3. It is not the newly uploaded file
        */

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
   