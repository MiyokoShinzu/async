<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   CREATE PAYMONGO CHECKOUT SESSION
========================================================== */

session_start();


/* =========================================================
   AUTHENTICATION
========================================================== */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {

    header("Location: ../login.php");
    exit;
}


/* =========================================================
   USER DATA
========================================================== */

$user = $_SESSION["user"];


/* =========================================================
   DATABASE
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   PAYMONGO CONFIGURATION
========================================================== */

require_once "../src/paymongo.php";


/* =========================================================
   ONLY ACCEPT POST
========================================================== */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: support_creator.php");

    exit;
}


/* =========================================================
   GET FORM DATA
========================================================== */

$amount = $_POST["amount"] ?? "";

$donorName =
    trim($_POST["donor_name"] ?? "");

$donorEmail =
    trim($_POST["donor_email"] ?? "");

$message =
    trim($_POST["message"] ?? "");

$studentId =
    $user["student_id"] ?? null;


/* =========================================================
   VALIDATE AMOUNT
========================================================== */

if (!is_numeric($amount)) {

    die("Invalid support amount.");
}


$amount = (float)$amount;


/* =========================================================
   SERVER-SIDE LIMITS
========================================================== */

if ($amount < 1) {

    die("Minimum support amount is ₱1.00.");
}


if ($amount > 50000) {

    die("Maximum support amount is ₱50,000.00.");
}


/* =========================================================
   VALIDATE NAME
========================================================== */

if ($donorName === "") {

    die("Please provide your name.");
}


if (strlen($donorName) > 255) {

    die("Name is too long.");
}


/* =========================================================
   VALIDATE EMAIL
========================================================== */

if (
    !filter_var(
        $donorEmail,
        FILTER_VALIDATE_EMAIL
    )
) {

    die("Please provide a valid email address.");
}


/* =========================================================
   SANITIZE MESSAGE
========================================================== */

if (strlen($message) > 1000) {

    $message =
        substr($message, 0, 1000);
}


/* =========================================================
   GENERATE REFERENCE CODE
========================================================== */

$referenceCode =
    "ETS-ASYNC" .
    date("YmdHis") .
    "-" .
    strtoupper(
        substr(
            bin2hex(random_bytes(4)),
            0,
            8
        )
    );


/* =========================================================
   CONVERT PHP PESO TO CENTAVOS

   Example:

   ₱100.00

   becomes:

   10000
========================================================== */

$amountCentavos =
    (int)round($amount * 100);


/* =========================================================
   CREATE DATABASE TRANSACTION
========================================================== */

$stmt = $mysqli->prepare(
    "INSERT INTO support_transactions
    (
        student_id,
        donor_name,
        donor_email,
        amount,
        currency,
        reference_code,
        status,
        message
    )
    VALUES (?, ?, ?, ?, 'PHP', ?, 'pending', ?)"
);


if (!$stmt) {

    die("Database preparation failed: " .
        $mysqli->error);
}


$stmt->bind_param(
    "sssdss",
    $studentId,
    $donorName,
    $donorEmail,
    $amount,
    $referenceCode,
    $message
);


if (!$stmt->execute()) {

    $stmt->close();

    die("Unable to create transaction: " .
        $mysqli->error);
}


$transactionId =
    $stmt->insert_id;


$stmt->close();


/* =========================================================
   RETURN URLS

   IMPORTANT:
   Replace the domain below with your actual domain
   before production.

   Example:
   https://ets-dev.com/ets-async/
========================================================== */

$baseUrl =
    "https://async.vertigation.com/student/";


$successUrl =
    $baseUrl .
    "support_success.php?reference=" .
    urlencode($referenceCode);


$cancelUrl =
    $baseUrl .
    "support_cancel.php?reference=" .
    urlencode($referenceCode);


/* =========================================================
   PAYMONGO CHECKOUT SESSION PAYLOAD
========================================================== */

$payload = [

    "data" => [

        "attributes" => [

            "line_items" => [

                [

                    "currency" => "PHP",

                    "amount" =>
                    $amountCentavos,

                    "description" =>
                    "ETS-Async Creator Support",

                    "name" =>
                    "Support ETS-Async",

                    "quantity" => 1

                ]

            ],


            "payment_method_types" => [

                "gcash",
                "paymaya",
                "card",
                "qrph"

            ],


            "description" =>
            "Support for ETS-Async Learning Portal",


            "success_url" =>
            $successUrl,


            "cancel_url" =>
            $cancelUrl,


            "metadata" => [

                "reference_code" =>
                $referenceCode,

                "transaction_id" =>
                (string)$transactionId,

                "student_id" =>
                (string)$studentId

            ]

        ]

    ]

];


/* =========================================================
   SEND REQUEST TO PAYMONGO
========================================================== */

$result = paymongoRequest(
    "POST",
    "/checkout_sessions",
    $payload
);


/* =========================================================
   HANDLE API ERROR
========================================================== */

if (!$result["success"]) {


    /* =====================================================
       MARK TRANSACTION AS FAILED
    ====================================================== */

    $stmt = $mysqli->prepare(
        "UPDATE support_transactions
         SET status = 'failed'
         WHERE id = ?"
    );


    if ($stmt) {

        $stmt->bind_param(
            "i",
            $transactionId
        );

        $stmt->execute();

        $stmt->close();
    }


    echo "<h2>Unable to create payment.</h2>";

    echo "<pre>";

    print_r(
        $result["data"] ??
            $result["error"] ??
            "Unknown PayMongo error"
    );

    echo "</pre>";

    exit;
}


/* =========================================================
   GET CHECKOUT SESSION
========================================================== */

$checkoutData =
    $result["data"]["data"] ?? null;


if (!$checkoutData) {

    die("PayMongo returned an invalid checkout response.");
}


/* =========================================================
   CHECKOUT SESSION ID
========================================================== */

$checkoutId =
    $checkoutData["id"] ?? null;


/* =========================================================
   CHECKOUT URL
========================================================== */

$checkoutUrl =
    $checkoutData["attributes"]["checkout_url"]
    ?? null;


if (!$checkoutUrl) {

    die("PayMongo did not return a checkout URL.");
}


/* =========================================================
   SAVE PAYMONGO CHECKOUT ID
========================================================== */

$stmt = $mysqli->prepare(
    "UPDATE support_transactions
     SET paymongo_checkout_id = ?
     WHERE id = ?"
);


if ($stmt) {

    $stmt->bind_param(
        "si",
        $checkoutId,
        $transactionId
    );

    $stmt->execute();

    $stmt->close();
}


/* =========================================================
   REDIRECT TO PAYMONGO
========================================================== */

header(
    "Location: " . $checkoutUrl
);

exit;
