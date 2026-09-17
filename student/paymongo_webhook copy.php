<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   PAYMONGO WEBHOOK
========================================================== */


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   READ RAW REQUEST
========================================================== */

$rawPayload =
    file_get_contents("php://input");


/* =========================================================
   BASIC VALIDATION
========================================================== */

if (!$rawPayload) {

    http_response_code(400);

    exit("Empty request.");
}


/* =========================================================
   DECODE JSON
========================================================== */

$payload =
    json_decode(
        $rawPayload,
        true
    );


if (!is_array($payload)) {

    http_response_code(400);

    exit("Invalid JSON.");
}


/* =========================================================
   GET EVENT DATA
========================================================== */

$eventData =
    $payload["data"]["attributes"]["data"]
    ?? null;

$eventType =
    $payload["data"]["attributes"]["type"]
    ?? null;


/* =========================================================
   LOG EVENT DURING DEVELOPMENT

   IMPORTANT:
   Remove or secure this logging in production.
========================================================== */

$logFile =
    __DIR__ . "/paymongo_webhook.log";


file_put_contents(
    $logFile,
    date("Y-m-d H:i:s") .
        " | " .
        $rawPayload .
        PHP_EOL .
        PHP_EOL,
    FILE_APPEND
);


/* =========================================================
   BASIC EVENT CHECK
========================================================== */

if (!$eventType) {

    http_response_code(200);

    exit("Event received.");
}


/* =========================================================
   GET OBJECT ATTRIBUTES
========================================================== */

$attributes =
    $eventData["attributes"]
    ?? [];


/* =========================================================
   PAYMENT IDENTIFIER
========================================================== */

$paymentId =
    $eventData["id"]
    ?? null;


/* =========================================================
   CHECKOUT SESSION IDENTIFIER
========================================================== */

$checkoutId =
    $attributes["checkout_session_id"]
    ?? null;


/* =========================================================
   METADATA
========================================================== */

$metadata =
    $attributes["metadata"]
    ?? [];


$referenceCode =
    $metadata["reference_code"]
    ?? null;


/* =========================================================
   HANDLE PAYMENT SUCCESS
========================================================== */

if (
    $eventType === "payment.paid" ||
    $eventType === "checkout_session.payment.paid"
) {


    /* =====================================================
       FIND TRANSACTION
    ====================================================== */

    if ($referenceCode) {

        $stmt = $mysqli->prepare(
            "UPDATE support_transactions
             SET
                status = 'paid',
                paymongo_payment_id = ?,
                paymongo_checkout_id =
                    COALESCE(
                        paymongo_checkout_id,
                        ?
                    ),
                paid_at =
                    COALESCE(
                        paid_at,
                        NOW()
                    )
             WHERE reference_code = ?"
        );


        if ($stmt) {

            $stmt->bind_param(
                "sss",
                $paymentId,
                $checkoutId,
                $referenceCode
            );

            $stmt->execute();

            $stmt->close();
        }
    }
}


/* =========================================================
   RETURN SUCCESS TO PAYMONGO
========================================================== */

http_response_code(200);

echo "OK";

exit;
