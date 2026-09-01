<?php

/* =========================================================
   STUDENT PROFILE PHOTO UPLOAD
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

$studentId =
    $user["student_id"] ?? "";

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
   CHECK REQUEST
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
   CHECK FILE
========================================================= */

if (
    !isset($_FILES["profile_photo"]) ||
    $_FILES["profile_photo"]["error"] !== UPLOAD_ERR_OK
) {

    header(
        "Location: profile_photo.php?error=" .
            urlencode(
                "Please select a photo to upload."
            )
    );

    exit;
}


$file =
    $_FILES["profile_photo"];


/* =========================================================
   FILE SIZE
========================================================= */

$maxSize =
    5 * 1024 * 1024; // 5 MB


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
    getimagesize(
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
   MIME TYPE
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

   Actual structure:

   public_html/
   │
   ├── async/
   │   └── student/
   │       └── profile_photo_upload.php
   │
   └── shared/
       └── uploads/
           └── profile_photos/

   From:

   async/student/

   we go:

   ../        -> async/
   ../../     -> public_html/

   then:

   shared/uploads/profile_photos/
========================================================= */

$uploadDirectory =
    __DIR__ .
    "/../../shared/uploads/profile_photos/";


/* =========================================================
   CREATE UPLOAD DIRECTORY IF NEEDED
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
   CHECK DIRECTORY WRITABLE
========================================================= */

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
   GENERATE SAFE STUDENT ID
========================================================= */

$safeStudentId =
    preg_replace(
        "/[^a-zA-Z0-9_-]/",
        "_",
        $studentId
    );


/* =========================================================
   GENERATE UNIQUE FILE NAME
========================================================= */

$fileName =
    $safeStudentId .
    "_" .
    bin2hex(
        random_bytes(8)
    ) .
    "." .
    $extension;


/* =========================================================
   FULL SERVER DESTINATION
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
                "Unable to save the uploaded photo."
            )
    );

    exit;
}


/* =========================================================
   DATABASE PHOTO PATH

   Stored in database as:

   shared/uploads/profile_photos/photo.jpg

   NOT:

   /shared/...

   NOT:

   async/shared/...
========================================================= */

$photoPath =
    "shared/uploads/profile_photos/" .
    $fileName;


/* =========================================================
   SAVE PHOTO PATH TO DATABASE
========================================================= */

$sql = "
    UPDATE accounts
    SET profile_photo = ?
    WHERE student_id = ?
";


$stmt =
    $mysqli->prepare($sql);


if (!$stmt) {

    /* Remove uploaded file
       if SQL preparation fails */

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
   EXECUTE DATABASE UPDATE
========================================================= */

if (
    !$stmt->execute()
) {

    /* Remove uploaded file
       if database update fails */

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
    $oldPhoto !== "" &&
    strpos(
        $oldPhoto,
        "shared/uploads/profile_photos/"
    ) === 0
) {

    /*
       Database:

       shared/uploads/profile_photos/photo.jpg

       Server:

       public_html/shared/uploads/profile_photos/photo.jpg
    */

    $oldFile =
        __DIR__ .
        "/../../" .
        $oldPhoto;


    /* =====================================================
       MAKE SURE OLD FILE IS NOT THE NEW FILE
    ===================================================== */

    if (
        file_exists($oldFile) &&
        realpath($oldFile) !== realpath($destination)
    ) {

        unlink($oldFile);
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
