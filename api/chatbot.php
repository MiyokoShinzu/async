<?php

date_default_timezone_set("Asia/Manila");


/* =========================================================
   ETS-ASYNC ETSY AI CHATBOT API
   =========================================================

   File:
       api/chatbot.php

   Purpose:
       Secure backend for ETSY academic assistant.

   AI:
       Google Gemini API

   Important:
       Gemini API key is NEVER exposed to JavaScript.

   Receives:
       - Student message
       - Conversation history
       - Current page context

   Returns:
       JSON response containing ETSY's answer.
========================================================= */


/* =========================================================
   RESPONSE HEADER
========================================================= */

header(
    "Content-Type: application/json; charset=UTF-8"
);


/* =========================================================
   START SESSION
========================================================= */

session_start();


/* =========================================================
   HELPER: JSON RESPONSE
========================================================= */

function responseJSON(
    bool $success,
    string $message,
    array $extra = []
) {

    http_response_code(
        $success ? 200 : 400
    );


    echo json_encode(
        array_merge(
            [
                "success" => $success,
                "message" => $message
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
    );


    exit;
}


/* =========================================================
   AUTHENTICATION CHECK
========================================================= */

if (

    !isset(
        $_SESSION["logged_in"]
    )

    ||

    $_SESSION["logged_in"] !== true

    ||

    !isset(
        $_SESSION["user"]
    )

    ||

    (
        $_SESSION["user"]["access"]
        ?? ""
    ) !== "student"

) {

    responseJSON(
        false,
        "Unauthorized access."
    );
}


/* =========================================================
   REQUEST METHOD
========================================================= */

if (
    $_SERVER["REQUEST_METHOD"]
    !== "POST"
) {

    responseJSON(
        false,
        "Only POST requests are allowed."
    );
}


/* =========================================================
   GEMINI API KEY
========================================================= */

$apiKey =
    "AQ.Ab8RN6L0PvGTM4a5sbtigovvLZ9gbHrRWxdRBWVu6TBcioconA";


if (
    !$apiKey
) {

    error_log(
        "ETSY: GEMINI_API_KEY is not configured."
    );


    responseJSON(
        false,
        "ETSY is currently unavailable."
    );
}


/* =========================================================
   GEMINI MODEL
========================================================= */

$model =
   "gemini-3.5-flash";

/*
 * Recommended model.
 *
 * If you have configured another model
 * through the environment variable,
 * that model will be used.
 */

if (
    !$model
) {

    $model =
        "gemini-3.5-flash";
}


/* =========================================================
   READ REQUEST BODY
========================================================= */

$rawInput =
    file_get_contents(
        "php://input"
    );


if (
    !$rawInput
) {

    responseJSON(
        false,
        "Empty request."
    );
}


$data =
    json_decode(
        $rawInput,
        true
    );


if (
    !is_array($data)
) {

    responseJSON(
        false,
        "Invalid request data."
    );
}


/* =========================================================
   STUDENT MESSAGE
========================================================= */

$message =
    trim(
        (string)(
            $data["message"]
            ?? ""
        )
    );


if (
    $message === ""
) {

    responseJSON(
        false,
        "Please enter a message."
    );
}


/*
 * Maximum user message length.
 */

if (
    mb_strlen($message)
    > 4000
) {

    responseJSON(
        false,
        "Your message is too long."
    );
}


/* =========================================================
   CONVERSATION HISTORY
========================================================= */

$history =
    $data["history"]
    ?? [];


if (
    !is_array($history)
) {

    $history = [];
}


/*
 * Only use the latest 20 messages.
 */

$history =
    array_slice(
        $history,
        -20
    );


/* =========================================================
   CURRENT PAGE CONTEXT
========================================================= */

$pageContext =
    $data["page_context"]
    ?? [];


if (
    !is_array($pageContext)
) {

    $pageContext = [];
}


/* =========================================================
   PAGE INFORMATION
========================================================= */

$pageTitle =
    trim(
        (string)(
            $pageContext["title"]
            ?? ""
        )
    );


$pageURL =
    trim(
        (string)(
            $pageContext["url"]
            ?? ""
        )
    );


$pageHeading =
    trim(
        (string)(
            $pageContext["heading"]
            ?? ""
        )
    );


$pageDescription =
    trim(
        (string)(
            $pageContext["description"]
            ?? ""
        )
    );


$pageContent =
    trim(
        (string)(
            $pageContext["content"]
            ?? ""
        )
    );


/*
 * Protect the backend from excessively
 * large page context.
 */

if (
    mb_strlen($pageContent)
    > 12000
) {

    $pageContent =
        mb_substr(
            $pageContent,
            0,
            12000
        )
        .
        "\n[Page content truncated]";
}


/* =========================================================
   ETSY SYSTEM INSTRUCTION
========================================================= */

$systemInstruction = <<<SYSTEM

You are ETSY, the official academic AI assistant
inside the ETS-Async Learning Portal.

Your role is to help students learn and understand
academic and engineering concepts.

=========================================================
IDENTITY
=========================================================

Your name is ETSY.

Never introduce yourself as ChatGPT unless directly
asked about your underlying AI model.

When appropriate, say:

"Hi, I'm ETSY."

=========================================================
ACADEMIC SCOPE
=========================================================

ETS-Async serves students in the College of Engineering
and Architecture.

You may assist with:

- Mathematics
- Engineering mathematics
- Programming
- C and C++
- PHP
- JavaScript
- MySQL
- HTML and CSS
- Python
- MATLAB
- Computer engineering
- Electrical engineering
- Electronics
- Embedded systems
- Arduino
- ESP32
- STM32
- Internet of Things
- Digital signal processing
- Signals and systems
- Z-transform
- Fourier analysis
- Difference equations
- Digital systems
- Circuits
- Control systems
- Engineering computation
- Sensors
- Microcontrollers
- Communication systems
- Agricultural technology
- Engineering design
- Architecture-related technical concepts

=========================================================
CURRENT PAGE CONTEXT
=========================================================

The student is currently viewing a page in ETS-Async.

Use the current page context to understand references
such as:

"this"

"this page"

"the equation above"

"the example"

"this topic"

"this code"

"the graph"

"the table"

When the student asks a question that refers to the
current page, prioritize the supplied page context.

Do NOT claim to see information that is not contained
in the supplied page context.

If the current page context is insufficient, tell the
student what additional information is needed.

=========================================================
TEACHING STYLE
=========================================================

Be:

- Clear
- Accurate
- Patient
- Concise but sufficiently detailed
- Academically appropriate
- Step-by-step when solving technical problems

Use equations and examples when useful.

For difficult concepts:

1. Explain the idea.
2. Explain the relevant formula or principle.
3. Work through an example.
4. State the result.
5. Give a short interpretation.

=========================================================
ACADEMIC INTEGRITY
=========================================================

Your purpose is learning, not academic dishonesty.

When students ask about assignments, quizzes, laboratory
activities, examinations, or graded work, prioritize:

- Explanation
- Hints
- Reasoning
- Step-by-step learning
- Similar examples

Do not encourage cheating.

If appropriate, help the student understand how to
arrive at the answer rather than simply providing an
unexplained final answer.

=========================================================
PROGRAMMING
=========================================================

When explaining code:

- Explain what the code does.
- Identify important variables.
- Explain the algorithm.
- Point out errors when present.
- Provide corrected code when appropriate.
- Explain why the correction works.

Do not unnecessarily rewrite working code.

=========================================================
ENGINEERING
=========================================================

Use technically correct engineering terminology.

Distinguish clearly between:

- Formula
- Assumption
- Calculation
- Result
- Interpretation

Do not invent experimental results, measurements,
standards, citations, or specifications.

=========================================================
CURRENT PAGE DATA
=========================================================

Page title:
$pageTitle

Page heading:
$pageHeading

Page description:
$pageDescription

Current URL:
$pageURL

Visible page content:
$pageContent

=========================================================
FINAL INSTRUCTION
=========================================================

Answer the student's latest question using the current
page context when relevant.

If the student says "explain this", identify the most
relevant concept from the current page and explain it.

If there is an equation, code block, table, signal,
graph description, or academic content in the page
context, use it when answering.

SYSTEM;


/* =========================================================
   BUILD GEMINI CONTENTS
========================================================= */

$contents = [];


/* =========================================================
   CONVERT CHAT HISTORY
========================================================= */

foreach (
    $history as $item
) {

    if (
        !is_array($item)
    ) {

        continue;
    }


    $role =
        $item["role"]
        ?? "";


    $text =
        trim(
            (string)(
                $item["text"]
                ?? ""
            )
        );


    if (
        $text === ""
    ) {

        continue;
    }


    /*
     * Only allow valid frontend roles.
     */

    if (
        $role !== "user" &&
        $role !== "assistant"
    ) {

        continue;
    }


    /*
     * Gemini uses:
     *
     * user
     * model
     */

    $geminiRole =
        $role === "assistant"
        ? "model"
        : "user";


    $contents[] = [

        "role" =>
        $geminiRole,

        "parts" => [

            [
                "text" =>
                $text
            ]

        ]

    ];
}


/* =========================================================
   ENSURE CURRENT MESSAGE EXISTS
========================================================= */

$lastUserMessageExists =
    false;


for (
    $i = count($contents) - 1;
    $i >= 0;
    $i--
) {

    if (
        ($contents[$i]["role"] ?? "")
        === "user"
    ) {

        $lastText =
            $contents[$i]["parts"][0]["text"]
            ?? "";


        if (
            trim($lastText)
            === $message
        ) {

            $lastUserMessageExists =
                true;
        }

        break;
    }
}


if (
    !$lastUserMessageExists
) {

    $contents[] = [

        "role" => "user",

        "parts" => [

            [
                "text" =>
                $message
            ]

        ]

    ];
}


/* =========================================================
   GEMINI API URL
========================================================= */

$encodedModel =
    rawurlencode(
        $model
    );


$apiURL =
    "https://generativelanguage.googleapis.com/"
    .
    "v1beta/models/"
    .
    $encodedModel
    .
    ":generateContent";


/* =========================================================
   REQUEST BODY
========================================================= */

$requestBody = [

    "systemInstruction" => [

        "parts" => [

            [
                "text" =>
                $systemInstruction
            ]

        ]

    ],

    "contents" =>
    $contents,

    "generationConfig" => [

        "temperature" =>
        0.7,

        "maxOutputTokens" =>
        2048

    ]

];


$jsonBody =
    json_encode(
        $requestBody,
        JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES
    );


/* =========================================================
   INITIALIZE CURL
========================================================= */

$ch =
    curl_init(
        $apiURL
    );


/* =========================================================
   CURL OPTIONS
========================================================= */

curl_setopt_array(
    $ch,
    [

        CURLOPT_POST =>
        true,

        CURLOPT_RETURNTRANSFER =>
        true,

        CURLOPT_POSTFIELDS =>
        $jsonBody,

        CURLOPT_HTTPHEADER => [

            "Content-Type: application/json",

            "x-goog-api-key: "
                . $apiKey

        ],

        CURLOPT_CONNECTTIMEOUT =>
        15,

        CURLOPT_TIMEOUT =>
        60

    ]
);


/* =========================================================
   EXECUTE REQUEST
========================================================= */

$response =
    curl_exec(
        $ch
    );


$curlError =
    curl_error(
        $ch
    );


$httpCode =
    curl_getinfo(
        $ch,
        CURLINFO_HTTP_CODE
    );


curl_close(
    $ch
);


/* =========================================================
   CURL ERROR
========================================================= */

if (
    $response === false
) {

    error_log(
        "ETSY Gemini cURL error: "
            . $curlError
    );


    responseJSON(
        false,
        "ETSY could not connect to the AI service."
    );
}


/* =========================================================
   HTTP ERROR
========================================================= */

if (
    $httpCode < 200 ||
    $httpCode >= 300
) {

    error_log(
        "ETSY Gemini HTTP "
            . $httpCode
            . ": "
            . $response
    );


    responseJSON(
        false,
        "ETSY could not process your request right now."
    );
}


/* =========================================================
   DECODE GEMINI RESPONSE
========================================================= */

$result =
    json_decode(
        $response,
        true
    );


if (
    !is_array($result)
) {

    error_log(
        "ETSY: Invalid Gemini JSON response."
    );


    responseJSON(
        false,
        "ETSY received an invalid response."
    );
}


/* =========================================================
   EXTRACT RESPONSE TEXT
========================================================= */

$reply = "";


$candidates =
    $result["candidates"]
    ?? [];


if (
    isset(
        $candidates[0]["content"]["parts"]
    )
    &&
    is_array(
        $candidates[0]["content"]["parts"]
    )
) {

    foreach (
        $candidates[0]["content"]["parts"]
        as $part
    ) {

        if (
            isset(
                $part["text"]
            )
        ) {

            $reply .=
                $part["text"];
        }
    }
}


$reply =
    trim(
        $reply
    );


/* =========================================================
   EMPTY RESPONSE
========================================================= */

if (
    $reply === ""
) {

    error_log(
        "ETSY: Gemini returned no text."
    );


    responseJSON(
        false,
        "ETSY could not generate a response."
    );
}


/* =========================================================
   SUCCESS
========================================================= */

responseJSON(

    true,

    "Success.",

    [

        "reply" =>
        $reply,

        "model" =>
        $model

    ]

);
