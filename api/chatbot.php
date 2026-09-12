<?php

/* =========================================================
   ETSY AI CHATBOT
   ETS-Async Learning Portal
   GROQ API VERSION
   ========================================================= */


date_default_timezone_set("Asia/Manila");


/* =========================================================
   RESPONSE HEADER
   ========================================================= */

header("Content-Type: application/json; charset=UTF-8");


/* =========================================================
   SESSION
   ========================================================= */

session_start();


/* =========================================================
   RESPONSE FUNCTION
   ========================================================= */

function responseJSON(
    bool $success,
    string $message,
    array $extra = []
) {

    http_response_code($success ? 200 : 400);

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
   AUTHENTICATION
   ========================================================= */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {

    responseJSON(
        false,
        "Unauthorized access."
    );
}


/* =========================================================
   REQUEST METHOD
   ========================================================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    responseJSON(
        false,
        "Invalid request method."
    );
}


/* =========================================================
   GROQ API KEY
   ========================================================= */

/*
   IMPORTANT:
   Replace this with your Groq API key.

   DO NOT put this key in JavaScript.
   Keep it inside PHP only.
*/

$apiKey = "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";


if (
    empty($apiKey) ||
    $apiKey === "YOUR_GROQ_API_KEY"
) {

    responseJSON(
        false,
        "Groq API key is not configured."
    );
}


/* =========================================================
   GROQ MODEL
   ========================================================= */

$model = "openai/gpt-oss-20b";


/* =========================================================
   GROQ API URL
   ========================================================= */

$apiUrl = "https://api.groq.com/openai/v1/chat/completions";


/* =========================================================
   READ REQUEST
   ========================================================= */

$input = json_decode(
    file_get_contents("php://input"),
    true
);


if (!is_array($input)) {

    responseJSON(
        false,
        "Invalid JSON request."
    );
}


/* =========================================================
   USER MESSAGE
   ========================================================= */

$message = trim(
    (string)($input["message"] ?? "")
);


if ($message === "") {

    responseJSON(
        false,
        "Message cannot be empty."
    );
}


if (mb_strlen($message) > 4000) {

    responseJSON(
        false,
        "Message is too long. Maximum length is 4000 characters."
    );
}


/* =========================================================
   CHAT HISTORY
   ========================================================= */

$history = $input["history"] ?? [];

if (!is_array($history)) {

    $history = [];
}


/*
   Keep only the latest 20 messages.
*/

$history = array_slice(
    $history,
    -20
);


/* =========================================================
   PAGE CONTEXT
   ========================================================= */

$pageContext = $input["page_context"] ?? [];

if (!is_array($pageContext)) {

    $pageContext = [];
}


/* =========================================================
   PAGE CONTEXT VARIABLES
   ========================================================= */

$pageTitle = trim(
    (string)($pageContext["title"] ?? "")
);

$pageUrl = trim(
    (string)($pageContext["url"] ?? "")
);

$pageHeading = trim(
    (string)($pageContext["heading"] ?? "")
);

$pageDescription = trim(
    (string)($pageContext["description"] ?? "")
);

$pageContent = trim(
    (string)($pageContext["content"] ?? "")
);


/*
   Prevent extremely large page context.
*/

if (mb_strlen($pageContent) > 12000) {

    $pageContent = mb_substr(
        $pageContent,
        0,
        12000
    );
}


/* =========================================================
   ETSY SYSTEM INSTRUCTION
   ========================================================= */

$systemInstruction = <<<SYSTEM

You are ETSY, the academic AI assistant of the ETS-Async Learning Portal.

Your purpose is to help students understand lessons, solve problems,
learn programming, understand engineering concepts, and study
effectively.

=========================================================
ACADEMIC AREAS
=========================================================

You can assist with:

• Mathematics
• Engineering Mathematics
• Calculus
• Differential Equations
• Linear Algebra
• Probability and Statistics
• Physics
• Electrical Engineering
• Computer Engineering
• Electronics
• Digital Systems
• Circuits
• Control Systems
• Signals and Systems
• Digital Signal Processing
• Z-Transform
• Fourier Transform
• Difference Equations
• Digital Filters
• Communication Systems
• Sensors
• Microcontrollers
• Arduino
• ESP32
• STM32
• Embedded Systems
• IoT
• Agricultural Technology
• Engineering Design
• Architecture technical concepts
• C
• C++
• PHP
• JavaScript
• HTML
• CSS
• jQuery
• Bootstrap
• MySQL
• Python
• MATLAB

=========================================================
TEACHING STYLE
=========================================================

Be clear, accurate, patient, and practical.

For difficult concepts, preferably use:

1. Idea
2. Formula or principle
3. Step-by-step explanation
4. Example
5. Result
6. Interpretation

Do not make explanations unnecessarily complicated.

=========================================================
MATHEMATICS AND ENGINEERING
=========================================================

When solving engineering or mathematics problems:

• State the formula.
• Identify the given values.
• Substitute values clearly.
• Show important calculation steps.
• Give the final answer.
• Explain what the result means.

Do not invent measurements, standards, specifications,
experimental results, or citations.

=========================================================
LATEX / MATHEMATICS FORMATTING
=========================================================

Use LaTeX for mathematical expressions.

For inline mathematics use:

\\( ... \\)

For displayed equations use:

\\[
...
\\]

Examples:

\\[
x[n] = u[n]
\\]

\\[
H(z) = \\frac{1}{1 - 0.5z^{-1}}
\\]

Use LaTeX instead of poorly formatted plain-text equations
when mathematical notation is important.

=========================================================
PROGRAMMING
=========================================================

When helping with programming:

• Explain the code.
• Explain variables.
• Explain the algorithm.
• Identify errors.
• Show corrected code when necessary.
• Explain why the correction works.

Do not unnecessarily rewrite working code.

=========================================================
ACADEMIC INTEGRITY
=========================================================

Your purpose is learning.

For assignments, quizzes, laboratories, and examinations:

• Explain concepts.
• Give hints.
• Show reasoning.
• Provide similar examples.
• Help the student understand the solution.

Do not encourage cheating.

=========================================================
CURRENT PAGE CONTEXT
=========================================================

The student may ask questions such as:

"Explain this page."
"What is the equation above?"
"Explain this code."
"What does this graph mean?"
"Explain the table."

Use the page context supplied below when relevant.

Never claim to see information that was not supplied.

=========================================================
CURRENT PAGE
=========================================================

Title:
$pageTitle

URL:
$pageUrl

Heading:
$pageHeading

Description:
$pageDescription

Content:
$pageContent

=========================================================
RESPONSE STYLE
=========================================================

Answer the student's actual question directly.

Do not repeatedly introduce yourself as ETSY.

Be conversational but educational.

Use headings, bullets, numbered steps, tables, and equations
when they improve readability.

SYSTEM;


/* =========================================================
   BUILD GROQ MESSAGES
   ========================================================= */

$messages = [];


/*
   System instruction.
*/

$messages[] = [
    "role" => "system",
    "content" => $systemInstruction
];


/* =========================================================
   ADD CHAT HISTORY
   ========================================================= */

foreach ($history as $item) {

    if (!is_array($item)) {
        continue;
    }

    $role = $item["role"] ?? "";
    $content = $item["content"] ?? "";

    if (
        !in_array(
            $role,
            ["user", "assistant"],
            true
        )
    ) {
        continue;
    }

    if (!is_string($content)) {
        continue;
    }

    if (trim($content) === "") {
        continue;
    }

    $messages[] = [
        "role" => $role,
        "content" => $content
    ];
}


/* =========================================================
   CURRENT USER MESSAGE
   ========================================================= */

$messages[] = [
    "role" => "user",
    "content" => $message
];


/* =========================================================
   GROQ REQUEST DATA
   ========================================================= */

$requestData = [

    "model" => $model,

    "messages" => $messages,

    "temperature" => 0.4,

    "max_tokens" => 2048

];


/* =========================================================
   INITIALIZE CURL
   ========================================================= */

$ch = curl_init(
    $apiUrl
);


/* =========================================================
   CURL OPTIONS
   ========================================================= */

curl_setopt_array(
    $ch,
    [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POST => true,

        CURLOPT_POSTFIELDS => json_encode(
            $requestData,
            JSON_UNESCAPED_UNICODE |
                JSON_UNESCAPED_SLASHES
        ),

        CURLOPT_HTTPHEADER => [

            "Authorization: Bearer " . $apiKey,

            "Content-Type: application/json",

            "Accept: application/json"

        ],

        CURLOPT_CONNECTTIMEOUT => 15,

        CURLOPT_TIMEOUT => 60

    ]
);


/* =========================================================
   EXECUTE REQUEST
   ========================================================= */

$response = curl_exec(
    $ch
);


/* =========================================================
   CURL ERROR
   ========================================================= */

if ($response === false) {

    $curlError = curl_error($ch);

    $curlErrorNumber = curl_errno($ch);

    curl_close($ch);

    responseJSON(
        false,
        "Unable to connect to Groq API.",
        [
            "error" => $curlError,
            "curl_error_number" => $curlErrorNumber
        ]
    );
}


/* =========================================================
   HTTP STATUS
   ========================================================= */

$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);


curl_close(
    $ch
);


/* =========================================================
   DECODE GROQ RESPONSE
   ========================================================= */

$result = json_decode(
    $response,
    true
);


if (!is_array($result)) {

    responseJSON(
        false,
        "Invalid response received from Groq.",
        [
            "http_code" => $httpCode
        ]
    );
}


/* =========================================================
   GROQ API ERROR
   ========================================================= */

if ($httpCode < 200 || $httpCode >= 300) {

    $errorMessage =
        $result["error"]["message"]
        ?? "Groq API request failed.";

    responseJSON(
        false,
        $errorMessage,
        [
            "http_code" => $httpCode
        ]
    );
}


/* =========================================================
   EXTRACT RESPONSE
   ========================================================= */

$reply =
    $result["choices"][0]["message"]["content"]
    ?? "";


$reply = trim(
    (string)$reply
);


/* =========================================================
   EMPTY RESPONSE CHECK
   ========================================================= */

if ($reply === "") {

    responseJSON(
        false,
        "Groq returned an empty response.",
        [
            "model" => $model
        ]
    );
}


/* =========================================================
   SUCCESS
   ========================================================= */

responseJSON(
    true,
    "Success.",
    [

        "reply" => $reply,

        "model" => $model

    ]
);
