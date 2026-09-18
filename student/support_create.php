<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   CREATE PAYMONGO CHECKOUT SESSION
   =========================================================

   PURPOSE:
   ---------------------------------------------------------
   Creates a local support transaction and then creates a
   PayMongo Hosted Checkout Session.

   FLOW:
   ---------------------------------------------------------
   1. Student submits support form
   2. Validate student session
   3. Validate amount and donor information
   4. Create local support_transactions record
   5. Create PayMongo Checkout Session
   6. Save PayMongo Checkout Session ID
   7. Redirect student to PayMongo

   IMPORTANT:
   ---------------------------------------------------------
   Payment status is NOT changed to "paid" here.

   The final payment confirmation must come from the
   PayMongo webhook.

   DATABASE CONNECTION:
   ---------------------------------------------------------
   $mysqli

   PAYMONGO:
   ---------------------------------------------------------
   paymongoRequest()

========================================================== */


/* =========================================================
   PHP ERROR REPORTING
   ========================================================= */

ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");

error_reporting(E_ALL);


/* =========================================================
   TIMEZONE
   ========================================================= */

date_default_timezone_set("Asia/Manila");


/* =========================================================
   SESSION
   ========================================================= */

if (session_status() !== PHP_SESSION_ACTIVE) {

    session_start();
}


/* =========================================================
   AUTHENTICATION
   ========================================================= */

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
   ========================================================= */

$user = $_SESSION["user"];

$studentId =
    $user["student_id"] ?? null;


/* =========================================================
   DATABASE
   ========================================================= */

require_once __DIR__ . "/../src/connection.php";


/* =========================================================
   VERIFY DATABASE CONNECTION
   ========================================================= */

if (!isset($mysqli)) {

    die("<h2>Database Error</h2>" .
        "<p>The \$mysqli database connection was not created.</p>");
}


if ($mysqli->connect_errno) {

    die("<h2>Database Connection Error</h2>" .
        "<p>" .
        htmlspecialchars(
            $mysqli->connect_error,
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</p>");
}


/* =========================================================
   PAYMONGO CONFIGURATION
   ========================================================= */

$paymongoFile =
    __DIR__ . "/../src/paymongo.php";


if (!file_exists($paymongoFile)) {

    die("<h2>PayMongo Configuration Error</h2>" .
        "<p>File not found:</p>" .
        "<pre>" .
        htmlspecialchars(
            $paymongoFile,
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</pre>");
}


require_once $paymongoFile;


/* =========================================================
   VERIFY PAYMONGO FUNCTION
   ========================================================= */

if (!function_exists("paymongoRequest")) {

    die("<h2>PayMongo Configuration Error</h2>" .
        "<p>The function <strong>paymongoRequest()</strong> " .
        "was not found in src/paymongo.php.</p>");
}


/* =========================================================
   ONLY ACCEPT POST
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: support_creator.php");

    exit;
}


/* =========================================================
   GET FORM DATA
   ========================================================= */

$amount =
    trim($_POST["amount"] ?? "");

$donorName =
    trim($_POST["donor_name"] ?? "");

$donorEmail =
    trim($_POST["donor_email"] ?? "");

$message =
    trim($_POST["message"] ?? "");


/* =========================================================
   VALIDATE STUDENT ID
   ========================================================= */

if (
    $studentId === null ||
    $studentId === ""
) {

    die("<h2>Session Error</h2>" .
        "<p>Your student account information could not be found.</p>" .
        "<p>Please log in again.</p>");
}


/* =========================================================
   VALIDATE AMOUNT
   ========================================================= */

if ($amount === "") {

    die("Please enter a support amount.");
}


if (!is_numeric($amount)) {

    die("Invalid support amount.");
}


$amount = (float)$amount;


/* =========================================================
   SERVER-SIDE AMOUNT LIMITS
   ========================================================= */

if ($amount < 1) {

    die("Minimum support amount is ₱1.00.");
}


if ($amount > 50000) {

    die("Maximum support amount is ₱50,000.00.");
}


/* =========================================================
   ROUND AMOUNT
   ========================================================= */

$amount = round($amount, 2);


/* =========================================================
   VALIDATE DONOR NAME
   ========================================================= */

if ($donorName === "") {

    die("Please provide your name.");
}


if (strlen($donorName) > 255) {

    die("Name is too long.");
}


/* =========================================================
   VALIDATE EMAIL
   ========================================================= */

if (
    !filter_var(
        $donorEmail,
        FILTER_VALIDATE_EMAIL
    )
) {

    die("Please provide a valid email address.");
}


/* =========================================================
   LIMIT MESSAGE
   ========================================================= */

if (strlen($message) > 1000) {

    $message =
        substr($message, 0, 1000);
}


/* =========================================================
   GENERATE REFERENCE CODE
   ========================================================= */

try {

    $randomPart =
        strtoupper(
            bin2hex(
                random_bytes(4)
            )
        );
} catch (Throwable $e) {

    $randomPart =
        strtoupper(
            substr(
                md5(
                    uniqid(
                        (string)mt_rand(),
                        true
                    )
                ),
                0,
                8
            )
        );
}


$referenceCode =
    "ETS-ASYNC" .
    date("YmdHis") .
    "-" .
    $randomPart;


/* =========================================================
   CONVERT PESO TO CENTAVOS
   =========================================================

   Example:

   ₱100.00
   =
   10000 centavos

========================================================== */

$amountCentavos =
    (int)round(
        $amount * 100
    );


/* =========================================================
   CREATE LOCAL SUPPORT TRANSACTION
   ========================================================= */

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
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        'PHP',
        ?,
        'pending',
        ?
    )"
);


if (!$stmt) {

    die("<h2>Database Error</h2>" .
        "<p>Unable to prepare support transaction.</p>" .
        "<pre>" .
        htmlspecialchars(
            $mysqli->error,
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</pre>");
}


/* =========================================================
   BIND DATABASE PARAMETERS
   ========================================================= */

$stmt->bind_param(
    "sssdss",
    $studentId,
    $donorName,
    $donorEmail,
    $amount,
    $referenceCode,
    $message
);


/* =========================================================
   EXECUTE INSERT
   ========================================================= */

if (!$stmt->execute()) {

    $databaseError =
        $stmt->error;

    $stmt->close();

    die("<h2>Database Error</h2>" .
        "<p>Unable to create the support transaction.</p>" .
        "<pre>" .
        htmlspecialchars(
            $databaseError,
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</pre>");
}


/* =========================================================
   GET LOCAL TRANSACTION ID
   ========================================================= */

$transactionId =
    $stmt->insert_id;


$stmt->close();


/* =========================================================
   RETURN URL
   ========================================================= */

$baseUrl =
    "https://async.vertigation.com/student/";


$successUrl =
    $baseUrl .
    "support_success.php?reference=" .
    urlencode(
        $referenceCode
    );


$cancelUrl =
    $baseUrl .
    "support_cancel.php?reference=" .
    urlencode(
        $referenceCode
    );


/* =========================================================
   PAYMONGO CHECKOUT PAYLOAD
   ========================================================= */

$payload = [

    "data" => [

        "attributes" => [

            /* ---------------------------------------------
               LINE ITEMS
            --------------------------------------------- */

            "line_items" => [

                [

                    "currency" =>
                    "PHP",

                    "amount" =>
                    $amountCentavos,

                    "description" =>
                    "Support contribution to ETS-Async Learning Portal",

                    "name" =>
                    "ETS-Async Support",

                    "quantity" =>
                    1
                ]

            ],


            /* ---------------------------------------------
               PAYMENT METHODS
            --------------------------------------------- */

            "payment_method_types" => [

                "gcash",
                "paymaya",
                "card",
                "qrph"

            ],


            /* ---------------------------------------------
               CHECKOUT DESCRIPTION
            --------------------------------------------- */

            "description" =>
            "Support contribution to ETS-Async Learning Portal",


            /* ---------------------------------------------
               SUCCESS URL
            --------------------------------------------- */

            "success_url" =>
            $successUrl,


            /* ---------------------------------------------
               CANCEL URL
            --------------------------------------------- */

            "cancel_url" =>
            $cancelUrl,


            /* ---------------------------------------------
               METADATA
            --------------------------------------------- */

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
   OPTIONAL DEBUG LOG
   =========================================================

   This does NOT save your secret key.

   It records the request/response information needed
   to diagnose PayMongo errors.

========================================================== */

$debugLogFile =
    __DIR__ . "/paymongo_create_debug.log";


/* =========================================================
   SEND REQUEST TO PAYMONGO
   ========================================================= */

try {

    $result =
        paymongoRequest(
            "POST",
            "/checkout_sessions",
            $payload
        );
} catch (Throwable $e) {

    /* -----------------------------------------------------
       MARK LOCAL TRANSACTION AS FAILED
    ----------------------------------------------------- */

    $stmt =
        $mysqli->prepare(
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


    /* -----------------------------------------------------
       LOG FATAL PAYMONGO ERROR
    ----------------------------------------------------- */

    file_put_contents(
        $debugLogFile,

        "\n\n========================================\n" .
            date("Y-m-d H:i:s") .
            "\nPAYMONGO PHP EXCEPTION\n" .
            "========================================\n" .
            $e->getMessage() .
            "\n" .
            $e->getFile() .
            ":" .
            $e->getLine() .
            "\n========================================\n",

        FILE_APPEND
    );


    die("<h2>Unable to create payment</h2>" .
        "<p>PayMongo request failed.</p>" .
        "<p><strong>Error:</strong></p>" .
        "<pre>" .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</pre>");
}


/* =========================================================
   VERIFY PAYMONGO RESPONSE STRUCTURE
   ========================================================= */

if (!is_array($result)) {

    die("<h2>PayMongo Error</h2>" .
        "<p>The PayMongo library returned an invalid response.</p>");
}


/* =========================================================
   LOG SAFE RESPONSE INFORMATION
   ========================================================= */

file_put_contents(
    $debugLogFile,

    "\n\n========================================\n" .
        date("Y-m-d H:i:s") .
        "\nPAYMONGO CHECKOUT RESPONSE\n" .
        "========================================\n" .
        "Success: " .
        (($result["success"] ?? false) ? "YES" : "NO") .
        "\nHTTP Code: " .
        ($result["http_code"] ?? "UNKNOWN") .
        "\nResponse:\n" .
        print_r(
            $result["data"] ?? null,
            true
        ) .
        "\nError:\n" .
        print_r(
            $result["error"] ?? null,
            true
        ) .
        "\n========================================\n",

    FILE_APPEND
);


/* =========================================================
   HANDLE PAYMONGO API ERROR
   ========================================================= */

if (
    !isset($result["success"]) ||
    $result["success"] !== true
) {

    /* -----------------------------------------------------
       MARK TRANSACTION FAILED
    ----------------------------------------------------- */

    $stmt =
        $mysqli->prepare(
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


    /* -----------------------------------------------------
       GET ERROR INFORMATION
    ----------------------------------------------------- */

    $httpCode =
        $result["http_code"] ??
        "Unknown";


    $errorMessage =
        $result["error"] ??
        "Unknown PayMongo error";


    $responseData =
        $result["data"] ??
        null;


    echo "<!DOCTYPE html>";
    echo "<html>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<title>PayMongo Error</title>";
    echo "</head>";
    echo "<body style='font-family:Arial,sans-serif;padding:30px;'>";

    echo "<h2>Unable to create payment</h2>";

    echo "<p><strong>HTTP Status:</strong> " .
        htmlspecialchars(
            (string)$httpCode,
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</p>";


    echo "<h3>PayMongo Response</h3>";

    echo "<pre style='background:#f5f5f5;padding:15px;overflow:auto;'>";

    echo htmlspecialchars(
        print_r(
            $responseData,
            true
        ),
        ENT_QUOTES,
        "UTF-8"
    );

    echo "</pre>";


    echo "<h3>Error</h3>";

    echo "<pre style='background:#fff3cd;padding:15px;overflow:auto;'>";

    echo htmlspecialchars(
        print_r(
            $errorMessage,
            true
        ),
        ENT_QUOTES,
        "UTF-8"
    );

    echo "</pre>";


    echo "<p>";

    echo "<a href='support_creator.php'>" .
        "Return to Support Page" .
        "</a>";

    echo "</p>";

    echo "</body>";
    echo "</html>";

    exit;
}


/* =========================================================
   GET CHECKOUT SESSION DATA
   ========================================================= */

$checkoutData =
    $result["data"]["data"] ??
    null;


if (
    !is_array($checkoutData)
) {

    die("<h2>PayMongo Error</h2>" .
        "<p>PayMongo returned an invalid Checkout Session response.</p>" .
        "<pre>" .
        htmlspecialchars(
            print_r(
                $result["data"] ?? null,
                true
            ),
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</pre>");
}


/* =========================================================
   GET CHECKOUT SESSION ID
   ========================================================= */

$checkoutId =
    $checkoutData["id"] ??
    null;


if (!$checkoutId) {

    die("<h2>PayMongo Error</h2>" .
        "<p>No Checkout Session ID was returned.</p>");
}


/* =========================================================
   GET CHECKOUT URL
   ========================================================= */

$checkoutUrl =
    $checkoutData["attributes"]["checkout_url"]
    ?? null;


if (!$checkoutUrl) {

    die("<h2>PayMongo Error</h2>" .
        "<p>PayMongo did not return a Checkout URL.</p>" .
        "<pre>" .
        htmlspecialchars(
            print_r(
                $checkoutData,
                true
            ),
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</pre>");
}


/* =========================================================
   SAVE PAYMONGO CHECKOUT SESSION ID
   ========================================================= */

$stmt =
    $mysqli->prepare(
        "UPDATE support_transactions
         SET paymongo_checkout_id = ?
         WHERE id = ?"
    );


if (!$stmt) {

    die("<h2>Database Error</h2>" .
        "<p>Unable to save PayMongo Checkout Session ID.</p>" .
        "<pre>" .
        htmlspecialchars(
            $mysqli->error,
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</pre>");
}


$stmt->bind_param(
    "si",
    $checkoutId,
    $transactionId
);


if (!$stmt->execute()) {

    $databaseError =
        $stmt->error;

    $stmt->close();

    die("<h2>Database Error</h2>" .
        "<p>Unable to save PayMongo Checkout Session ID.</p>" .
        "<pre>" .
        htmlspecialchars(
            $databaseError,
            ENT_QUOTES,
            "UTF-8"
        ) .
        "</pre>");
}


$stmt->close();


/* =========================================================
   REDIRECT TO PAYMONGO
   ========================================================= */

header(
    "Location: " .
        $checkoutUrl
);

exit;
