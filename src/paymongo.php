<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   PAYMONGO CONFIGURATION
========================================================== */


/* =========================================================
   PAYMONGO API KEYS

   IMPORTANT:
   - Use TEST keys while developing.
   - Replace these with LIVE keys only after testing.
   - NEVER expose the secret key in HTML/JavaScript.
========================================================== */

define(
    "PAYMONGO_SECRET_KEY",
    "sk_live_i4inHTBRwSBTEsUHiUvqVjk8"
);

define(
    "PAYMONGO_PUBLIC_KEY",
    "pk_live_AidrFnT47Jdk6FJBVZC11WAY"
);


/* =========================================================
   PAYMONGO API BASE URL
========================================================== */

define(
    "PAYMONGO_API_URL",
    "https://api.paymongo.com/v1"
);


/* =========================================================
   CREATE PAYMONGO AUTHORIZATION HEADER
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

    $url = PAYMONGO_API_URL . $endpoint;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt(
        $ch,
        CURLOPT_CUSTOMREQUEST,
        strtoupper($method)
    );

    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [
            "Authorization: Basic " . paymongoAuthorization(),
            "Content-Type: application/json",
            "Accept: application/json"
        ]
    );

    if ($data !== null) {

        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            json_encode($data)
        );
    }

    $response = curl_exec($ch);

    $httpCode = curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );

    $curlError = curl_error($ch);

    curl_close($ch);


    /* =====================================================
       CURL ERROR
    ====================================================== */

    if ($response === false) {

        return [
            "success" => false,
            "http_code" => $httpCode,
            "error" => $curlError
        ];
    }


    /* =====================================================
       DECODE RESPONSE
    ====================================================== */

    $decoded = json_decode(
        $response,
        true
    );


    /* =====================================================
       RETURN RESULT
    ====================================================== */

    return [
        "success" => ($httpCode >= 200 && $httpCode < 300),
        "http_code" => $httpCode,
        "data" => $decoded
    ];
}
