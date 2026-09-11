
<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   PAYMONGO CONFIGURATION
========================================================== */


/* =========================================================
   LOAD PAYMONGO API KEYS
==========================================================

   IMPORTANT:
   - Keys are loaded from environment variables.
   - NEVER place your actual secret key directly in this file.
   - Use sk_test_ / pk_test_ during testing.
   - Use sk_live_ / pk_live_ only in production.

========================================================== */

$paymongoSecretKey =
    "sk_live_i4inHTBRwSBTEsUHiUvqVjk8" ;
    // "sk_test_ge3huoytcE7BgpT2EPaDaj1g";

$paymongoPublicKey =
    "pk_live_AidrFnT47Jdk6FJBVZC11WAY";
    // "pk_test_cd6WVe7HBqyuoYYApNMZJvJ2";


/* =========================================================
   VALIDATE SECRET KEY
========================================================== */

if (
    $paymongoSecretKey === false ||
    trim($paymongoSecretKey) === ""
) {

    die(
        "PayMongo configuration error: " .
        "PAYMONGO_SECRET_KEY is not configured."
    );
}


/* =========================================================
   DEFINE PAYMONGO KEYS
========================================================== */

define(
    "PAYMONGO_SECRET_KEY",
    trim($paymongoSecretKey)
);

define(
    "PAYMONGO_PUBLIC_KEY",
    $paymongoPublicKey !== false
        ? trim($paymongoPublicKey)
        : ""
);


/* =========================================================
   PAYMONGO API BASE URL
========================================================== */

define(
    "PAYMONGO_API_URL",
    "https://api.paymongo.com/v1"
);


/* =========================================================
   CREATE PAYMONGO AUTHORIZATION
==========================================================

   PayMongo uses HTTP Basic Authentication.

   Format:

   secret_key:

   The colon is intentional.

========================================================== */

function paymongoAuthorization()
{
    return base64_encode(
        PAYMONGO_SECRET_KEY . ":"
    );
}


/* =========================================================
   PAYMONGO API REQUEST
========================================================== */

function paymongoRequest(
    $method,
    $endpoint,
    $data = null
) {


    /* =====================================================
       BUILD URL
    ====================================================== */

    $url =
        PAYMONGO_API_URL .
        $endpoint;


    /* =====================================================
       INITIALIZE CURL
    ====================================================== */

    $ch =
        curl_init($url);


    if ($ch === false) {

        return [

            "success" => false,

            "http_code" => 0,

            "error" =>
                "Unable to initialize cURL."

        ];
    }


    /* =====================================================
       CURL OPTIONS
    ====================================================== */

    curl_setopt(
        $ch,
        CURLOPT_RETURNTRANSFER,
        true
    );


    curl_setopt(
        $ch,
        CURLOPT_CUSTOMREQUEST,
        strtoupper($method)
    );


    /* =====================================================
       HTTP HEADERS
    ====================================================== */

    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [

            "Authorization: Basic " .
                paymongoAuthorization(),

            "Content-Type: application/json",

            "Accept: application/json"

        ]
    );


    /* =====================================================
       REQUEST BODY
    ====================================================== */

    if ($data !== null) {

        $jsonData =
            json_encode(
                $data
            );


        if ($jsonData === false) {

            curl_close($ch);

            return [

                "success" => false,

                "http_code" => 0,

                "error" =>
                    "Unable to encode request data.",

                "json_error" =>
                    json_last_error_msg()

            ];
        }


        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            $jsonData
        );
    }


    /* =====================================================
       EXECUTE REQUEST
    ====================================================== */

    $response =
        curl_exec($ch);


    /* =====================================================
       GET HTTP STATUS
    ====================================================== */

    $httpCode =
        curl_getinfo(
            $ch,
            CURLINFO_HTTP_CODE
        );


    /* =====================================================
       GET CURL ERROR
    ====================================================== */

    $curlError =
        curl_error($ch);


    /* =====================================================
       CLOSE CURL
    ====================================================== */

    curl_close($ch);


    /* =====================================================
       CURL ERROR
    ====================================================== */

    if ($response === false) {

        return [

            "success" => false,

            "http_code" =>
                $httpCode,

            "error" =>
                $curlError

        ];
    }


    /* =====================================================
       DECODE PAYMONGO RESPONSE
    ====================================================== */

    $decoded =
        json_decode(
            $response,
            true
        );


    /* =====================================================
       JSON DECODE ERROR
    ====================================================== */

    if (
        $decoded === null &&
        json_last_error() !== JSON_ERROR_NONE
    ) {

        return [

            "success" => false,

            "http_code" =>
                $httpCode,

            "error" =>
                "Invalid JSON response from PayMongo.",

            "raw_response" =>
                $response

        ];
    }


    /* =====================================================
       RETURN RESULT
    ====================================================== */

    return [

        "success" =>
            (
                $httpCode >= 200 &&
                $httpCode < 300
            ),

        "http_code" =>
            $httpCode,

        "data" =>
            $decoded,

        "raw_response" =>
            $response

    ];
}

