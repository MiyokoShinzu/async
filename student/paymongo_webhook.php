
<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   PAYMONGO WEBHOOK
   =========================================================

   PURPOSE:
   ---------------------------------------------------------
   Receives payment events directly from PayMongo.

   IMPORTANT:
   ---------------------------------------------------------
   This endpoint must NOT require a student login/session.

   PayMongo calls this URL directly from its servers.

   WEBHOOK EVENT:
   ---------------------------------------------------------
   checkout_session.payment.paid

   DATABASE:
   ---------------------------------------------------------
   support_transactions

   MATCHING:
   ---------------------------------------------------------
   PayMongo metadata.reference_code
              |
              v
   support_transactions.reference_code

   STATUS:
   ---------------------------------------------------------
   pending -> paid

========================================================== */


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

    http_response_code(405);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}


/* =========================================================
   GET RAW REQUEST BODY
========================================================== */

$rawPayload =
    file_get_contents("php://input");


if (
    $rawPayload === false ||
    trim($rawPayload) === ""
) {

    http_response_code(400);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" => "Empty webhook payload."
    ]);

    exit;
}


/* =========================================================
   DECODE JSON
========================================================== */

$payload =
    json_decode(
        $rawPayload,
        true
    );


if (
    !is_array($payload) ||
    json_last_error() !== JSON_ERROR_NONE
) {

    http_response_code(400);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON payload."
    ]);

    exit;
}


/* =========================================================
   GET EVENT DATA
========================================================== */

$eventData =
    $payload["data"] ?? null;


if (!$eventData) {

    http_response_code(400);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" => "Invalid event data."
    ]);

    exit;
}


/* =========================================================
   GET EVENT TYPE
========================================================== */

$eventAttributes =
    $eventData["attributes"] ?? [];


$eventType =
    $eventAttributes["type"] ?? "";


/* =========================================================
   ONLY PROCESS PAID CHECKOUT SESSIONS
========================================================== */

if (
    $eventType !==
    "checkout_session.payment.paid"
) {

    /*
     * We still return HTTP 200 because the webhook
     * was successfully received.
     *
     * This prevents PayMongo from repeatedly retrying
     * events that this endpoint intentionally does not
     * process.
     */

    http_response_code(200);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => true,
        "message" => "Event received but not processed."
    ]);

    exit;
}


/* =========================================================
   GET CHECKOUT SESSION
========================================================== */

$checkoutSession =
    $eventAttributes["data"] ?? null;


if (!$checkoutSession) {

    http_response_code(400);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" => "Checkout Session data missing."
    ]);

    exit;
}


/* =========================================================
   CHECKOUT SESSION ID
========================================================== */

$checkoutSessionId =
    $checkoutSession["id"] ?? "";


/* =========================================================
   CHECKOUT SESSION ATTRIBUTES
========================================================== */

$checkoutAttributes =
    $checkoutSession["attributes"] ?? [];


/* =========================================================
   METADATA
========================================================== */

$metadata =
    $checkoutAttributes["metadata"] ?? [];


/* =========================================================
   REFERENCE CODE
========================================================== */

$referenceCode =
    trim(
        $metadata["reference_code"] ?? ""
    );


/* =========================================================
   TRANSACTION ID
========================================================== */

$transactionId =
    (int)(
        $metadata["transaction_id"] ?? 0
    );


/* =========================================================
   BASIC VALIDATION
========================================================== */

if (
    $referenceCode === "" &&
    $transactionId <= 0
) {

    http_response_code(400);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" =>
            "No transaction reference found."
    ]);

    exit;
}


/* =========================================================
   FIND LOCAL TRANSACTION
========================================================== */

$transaction = null;


/* =========================================================
   FIRST TRY TRANSACTION ID
========================================================== */

if ($transactionId > 0) {

    $stmt = $mysqli->prepare(
        "SELECT
            id,
            amount,
            currency,
            reference_code,
            status,
            paymongo_checkout_id
         FROM support_transactions
         WHERE id = ?
         LIMIT 1"
    );


    if ($stmt) {

        $stmt->bind_param(
            "i",
            $transactionId
        );

        $stmt->execute();

        $result =
            $stmt->get_result();

        $transaction =
            $result->fetch_assoc();

        $stmt->close();
    }
}


/* =========================================================
   FALLBACK TO REFERENCE CODE
========================================================== */

if (
    !$transaction &&
    $referenceCode !== ""
) {

    $stmt = $mysqli->prepare(
        "SELECT
            id,
            amount,
            currency,
            reference_code,
            status,
            paymongo_checkout_id
         FROM support_transactions
         WHERE reference_code = ?
         LIMIT 1"
    );


    if ($stmt) {

        $stmt->bind_param(
            "s",
            $referenceCode
        );

        $stmt->execute();

        $result =
            $stmt->get_result();

        $transaction =
            $result->fetch_assoc();

        $stmt->close();
    }
}


/* =========================================================
   TRANSACTION NOT FOUND
========================================================== */

if (!$transaction) {

    http_response_code(404);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" =>
            "Local transaction not found."
    ]);

    exit;
}


/* =========================================================
   VERIFY CHECKOUT SESSION ID
========================================================== */

if (
    !empty($transaction["paymongo_checkout_id"]) &&
    $checkoutSessionId !== "" &&
    $transaction["paymongo_checkout_id"]
        !== $checkoutSessionId
) {

    http_response_code(400);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" =>
            "Checkout Session does not match transaction."
    ]);

    exit;
}


/* =========================================================
   IDEMPOTENCY
   =========================================================

   If the transaction is already paid, do not perform
   another update.

========================================================== */

if (
    strtolower(
        $transaction["status"] ?? ""
    ) === "paid"
) {

    http_response_code(200);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => true,
        "message" =>
            "Transaction already marked as paid."
    ]);

    exit;
}


/* =========================================================
   VERIFY AMOUNT
========================================================== */

$payments =
    $checkoutAttributes["payments"] ?? [];


$paymentVerified =
    false;


$expectedAmountCentavos =
    (int)round(
        (float)$transaction["amount"] * 100
    );


foreach (
    $payments as $payment
) {

    $paymentAttributes =
        $payment["attributes"] ?? [];


    $paymentStatus =
        strtolower(
            $paymentAttributes["status"] ?? ""
        );


    $paymentAmount =
        (int)(
            $paymentAttributes["amount"] ?? 0
        );


    $paymentCurrency =
        strtoupper(
            $paymentAttributes["currency"] ?? ""
        );


    if (
        $paymentStatus === "paid" &&
        $paymentAmount ===
            $expectedAmountCentavos &&
        $paymentCurrency ===
            strtoupper(
                $transaction["currency"] ?? "PHP"
            )
    ) {

        $paymentVerified =
            true;

        break;
    }
}


/* =========================================================
   PAYMENT NOT VERIFIED
========================================================== */

if (!$paymentVerified) {

    http_response_code(400);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" =>
            "Paid event received but payment amount or currency could not be verified."
    ]);

    exit;
}


/* =========================================================
   UPDATE TRANSACTION
========================================================== */

$stmt = $mysqli->prepare(
    "UPDATE support_transactions
     SET
        status = 'paid',
        paid_at = NOW()
     WHERE id = ?
     AND status <> 'paid'
     LIMIT 1"
);


if (!$stmt) {

    http_response_code(500);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" =>
            "Unable to prepare database update."
    ]);

    exit;
}


$stmt->bind_param(
    "i",
    $transaction["id"]
);


$updated =
    $stmt->execute();


$stmt->close();


/* =========================================================
   DATABASE UPDATE FAILED
========================================================== */

if (!$updated) {

    http_response_code(500);

    header(
        "Content-Type: application/json"
    );

    echo json_encode([
        "success" => false,
        "message" =>
            "Unable to update transaction."
    ]);

    exit;
}


/* =========================================================
   SUCCESS RESPONSE
========================================================== */

http_response_code(200);

header(
    "Content-Type: application/json"
);

echo json_encode([
    "success" => true,
    "message" =>
        "Payment successfully recorded.",
    "reference_code" =>
        $transaction["reference_code"]
]);

exit;

