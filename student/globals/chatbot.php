<?php
/* =========================================================
   ETS-ASYNC FLOATING AI CHATBOT
   =========================================================
   File:
       globals/chatbot.php

   Name:
       ETSY

   Purpose:
       Floating AI academic assistant available throughout
       authenticated student pages.

   Features:
       - Current-page awareness
       - Academic page context
       - Gemini AI integration through PHP backend
       - MathJax mathematical rendering
       - Light / Dark theme
       - Theme persistence
       - Conversation persistence
       - Quick academic questions
       - Responsive design
       - Anime.js animations
       - Mobile friendly
       - GitHub-inspired dark interface
       - Clean academic light interface
       - ETSY mascot image

   Backend:
       ../api/chatbot.php

   IMPORTANT:
       The Gemini API key is NOT stored here.
   ========================================================= */
?>


<!-- =========================================================
     MATHJAX
========================================================= -->

<script>
    /*
     * =========================================================
     * MATHJAX CONFIGURATION
     * =========================================================
     */

    window.MathJax = {

        tex: {

            inlineMath: [
                ["\\(", "\\)"],
                ["$", "$"]
            ],

            displayMath: [
                ["\\[", "\\]"],
                ["$$", "$$"]
            ],

            processEscapes: true,

            processEnvironments: true

        },

        options: {

            skipHtmlTags: [
                "script",
                "noscript",
                "style",
                "textarea",
                "pre",
                "code"
            ]

        }

    };
</script>


<script
    async
    src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml.js">
</script>


<!-- =========================================================
     FLOATING CHATBOT
========================================================= -->

<div id="etsChatbot">


    <!-- =====================================================
         FLOATING BUTTON
    ===================================================== -->

    <button
        type="button"
        id="etsChatbotToggle"
        class="ets-chatbot-toggle"
        aria-label="Open ETSY Academic Assistant"
        title="ETSY Academic Assistant">

        <!-- Chat icon -->
        <span class="ets-chatbot-chat-icon">
            <i class="bi bi-chat-dots-fill fs-5"></i>
        </span>

        <!-- ETSY mascot -->
        <img
            src="../assets/etsy.png"
            alt="ETSY" style="width: 100%; height: 100%; object-fit: contain;">

        <span class="ets-chatbot-pulse"></span>

    </button>

    <style>
        .ets-chatbot-toggle {
            position: fixed;
            overflow: visible !important;
        }

        .ets-chatbot-chat-icon {
            position: absolute;
            top: -15px;
            left: 25px;
            width: 50px;
            height: 50px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;
            background: rgba(0, 0,0, 0);
            color: #ffeeff;

            font-size: 11px;

           

            z-index: 20;
        }

        .ets-chatbot-chat-icon i {
            line-height: 1;
        }
    </style>


    <!-- =====================================================
         CHAT WINDOW
    ===================================================== -->

    <div
        id="etsChatbotWindow"
        class="ets-chatbot-window"
        aria-hidden="true">


        <!-- =================================================
             HEADER
        ================================================= -->

        <div class="ets-chatbot-header">


            <!-- =================================================
                 BRAND
            ================================================= -->

            <div class="ets-chatbot-brand">

                <div class="ets-chatbot-avatar">

                    <img
                        src="../assets/etsy.png"
                        alt="ETSY">

                </div>


                <div>

                    <div class="ets-chatbot-title">

                        ETSY

                    </div>


                    <div class="ets-chatbot-status">

                        <span></span>

                        Academic Assistant

                    </div>

                </div>

            </div>


            <!-- =================================================
                 ACTIONS
            ================================================= -->

            <div class="ets-chatbot-actions">


                <!-- =================================================
                     THEME BUTTON
                ================================================= -->

                <button
                    type="button"
                    id="etsChatbotTheme"
                    title="Toggle theme"
                    aria-label="Toggle theme">

                    <i
                        id="etsChatbotThemeIcon"
                        class="bi bi-sun-fill">
                    </i>

                </button>


                <!-- =================================================
                     CLEAR
                ================================================= -->

                <button
                    type="button"
                    id="etsChatbotClear"
                    title="Clear conversation"
                    aria-label="Clear conversation">

                    <i class="bi bi-trash3"></i>

                </button>


                <!-- =================================================
                     CLOSE
                ================================================= -->

                <button
                    type="button"
                    id="etsChatbotClose"
                    title="Close"
                    aria-label="Close chatbot">

                    <i class="bi bi-x-lg"></i>

                </button>


            </div>

        </div>


        <!-- =================================================
             CURRENT PAGE INDICATOR
        ================================================= -->

        <div
            id="etsCurrentPage"
            class="ets-current-page">

            <i class="bi bi-file-earmark-text"></i>

            <span>

                Current page

            </span>

        </div>


        <!-- =================================================
             MESSAGES
        ================================================= -->

        <div
            id="etsChatbotMessages"
            class="ets-chatbot-messages">


            <!-- =================================================
                 WELCOME MESSAGE
            ================================================= -->

            <div class="ets-chat-message ai-message">


                <div class="ets-small-avatar">

                    <img
                        src="../assets/etsy.png"
                        alt="ETSY">

                </div>


                <div class="ets-chat-bubble">


                    <div class="ets-chat-name">

                        ETSY

                    </div>


                    <div class="ets-chat-text">

                        Hello! I'm
                        <strong>ETSY</strong>,
                        your ETS-Async academic assistant.

                        <br><br>

                        I can understand the academic
                        content of the page you're viewing
                        and help explain concepts, equations,
                        examples, code, and lessons.

                        <br><br>

                        Try asking:

                        <strong>
                            "Explain this."
                        </strong>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 QUICK QUESTIONS
            ================================================= -->

            <div
                id="etsQuickQuestions"
                class="ets-quick-questions">


                <button
                    type="button"
                    data-question="Explain the main topic on this page in simple terms.">

                    <i class="bi bi-lightbulb"></i>

                    Explain this page

                </button>


                <button
                    type="button"
                    data-question="Explain the most important concept on this page step by step.">

                    <i class="bi bi-diagram-3"></i>

                    Explain the concept

                </button>


                <button
                    type="button"
                    data-question="Give me an example based on the topic currently shown on this page.">

                    <i class="bi bi-code-slash"></i>

                    Give an example

                </button>


                <button
                    type="button"
                    data-question="Give me a short review of the topic on this page.">

                    <i class="bi bi-journal-text"></i>

                    Review this topic

                </button>


            </div>

        </div>


        <!-- =================================================
             TYPING INDICATOR
        ================================================= -->

        <div
            id="etsChatbotTyping"
            class="ets-chatbot-typing">


            <div class="ets-small-avatar">

                <img
                    src="../assets/etsy.png"
                    alt="ETSY">

            </div>


            <div class="ets-typing-bubble">

                <span></span>

                <span></span>

                <span></span>

            </div>

        </div>


        <!-- =================================================
             INPUT AREA
        ================================================= -->

        <div class="ets-chatbot-input-area">


            <div class="ets-input-wrapper">


                <textarea
                    id="etsChatbotInput"
                    rows="1"
                    maxlength="4000"
                    placeholder="Ask ETSY about this page..."></textarea>


                <button
                    type="button"
                    id="etsChatbotSend"
                    aria-label="Send message">

                    <i class="bi bi-send-fill"></i>

                </button>


            </div>


            <div class="ets-ai-disclaimer">

                <i class="bi bi-shield-check"></i>

                ETSY may make mistakes. Verify important
                academic information.

            </div>

        </div>


    </div>

</div>


<!-- =========================================================
     CHATBOT CSS
========================================================= -->

<style>
    /* =========================================================
       LIGHT THEME VARIABLES
    ========================================================= */

    #etsChatbot {

        --ets-bg: #ffffff;

        --ets-panel: #f6f8fa;

        --ets-panel-2: #eef1f4;

        --ets-border: #d0d7de;

        --ets-text: #1f2328;

        --ets-text-secondary: #424a53;

        --ets-muted: #656d76;

        --ets-blue: #0969da;

        --ets-blue-dark: #0b4f8a;

        --ets-violet: #8250df;

        --ets-user-bg: #0969da;

        --ets-user-text: #ffffff;

        --ets-code-bg: #f0f2f4;

        --ets-code-text: #0550ae;

        --ets-shadow:
            0 25px 70px rgba(31, 35, 40, .20);

    }


    /* =========================================================
       DARK THEME
    ========================================================= */

    #etsChatbot.ets-dark {

        --ets-bg: #0d1117;

        --ets-panel: #161b22;

        --ets-panel-2: #1c2128;

        --ets-border: #30363d;

        --ets-text: #f0f6fc;

        --ets-text-secondary: #c9d1d9;

        --ets-muted: #8b949e;

        --ets-blue: #2f81f7;

        --ets-blue-dark: #0b4f8a;

        --ets-violet: #8b5cf6;

        --ets-user-bg: #1f6feb;

        --ets-user-text: #ffffff;

        --ets-code-bg: #0d1117;

        --ets-code-text: #79c0ff;

        --ets-shadow:
            0 25px 70px rgba(0, 0, 0, .55);

    }


    /* =========================================================
       MAIN CONTAINER
    ========================================================= */

    #etsChatbot {

        position: fixed;

        right: 24px;

        bottom: 24px;

        z-index: 99999;

        font-family:
            Inter,
            Poppins,
            -apple-system,
            BlinkMacSystemFont,
            "Segoe UI",
            sans-serif;

    }


    /* =========================================================
       FLOATING BUTTON
    ========================================================= */

    .ets-chatbot-toggle {

        position: relative;

        width: 62px;

        height: 62px;

        border: 1px solid rgba(47, 129, 247, .6);

        border-radius: 50%;

        background:
            linear-gradient(135deg,
                #0b4f8a,
                #2f81f7);

        color: white;

        display: flex;

        align-items: center;

        justify-content: center;

        font-size: 25px;

        cursor: pointer;

        box-shadow:
            0 12px 35px rgba(0, 0, 0, .30),
            0 0 30px rgba(47, 129, 247, .25);

        transition:
            transform .25s ease,
            box-shadow .25s ease;

        overflow: hidden;

    }


    .ets-chatbot-toggle img {

        width: 82%;

        height: 82%;

        object-fit: contain;

        display: block;

        position: relative;

        z-index: 2;

    }


    .ets-chatbot-toggle:hover {

        transform:
            translateY(-3px) scale(1.04);

        box-shadow:
            0 16px 45px rgba(0, 0, 0, .35),
            0 0 40px rgba(47, 129, 247, .35);

    }


    /* =========================================================
       PULSE
    ========================================================= */

    .ets-chatbot-pulse {

        position: absolute;

        inset: -5px;

        border-radius: 50%;

        border: 1px solid rgba(47, 129, 247, .5);

        animation:
            etsChatPulse 2.2s infinite;

        z-index: 1;

    }


    @keyframes etsChatPulse {

        0% {

            transform: scale(.95);

            opacity: .8;

        }

        70% {

            transform: scale(1.18);

            opacity: 0;

        }

        100% {

            transform: scale(1.18);

            opacity: 0;

        }

    }


    /* =========================================================
       CHAT WINDOW
    ========================================================= */

    .ets-chatbot-window {

        position: absolute;

        right: 0;

        bottom: 78px;

        width: 390px;

        height: 600px;

        max-height:
            calc(100vh - 120px);

        display: flex;

        flex-direction: column;

        overflow: hidden;

        background:
            var(--ets-bg);

        border:
            1px solid var(--ets-border);

        border-radius: 18px;

        box-shadow:
            var(--ets-shadow);

        opacity: 0;

        visibility: hidden;

        transform:
            translateY(20px) scale(.96);

        transition:
            background .25s ease,
            border-color .25s ease;

    }


    .ets-chatbot-window.open {

        opacity: 1;

        visibility: visible;

    }


    /* =========================================================
       HEADER
    ========================================================= */

    .ets-chatbot-header {

        min-height: 72px;

        padding: 14px 16px;

        display: flex;

        align-items: center;

        justify-content: space-between;

        border-bottom:
            1px solid var(--ets-border);

        background:
            var(--ets-panel);

    }


    .ets-chatbot-brand {

        display: flex;

        align-items: center;

        gap: 11px;

    }


    .ets-chatbot-avatar {

        width: 42px;

        height: 42px;

        border-radius: 12px;

        display: flex;

        align-items: center;

        justify-content: center;

        color: white;

        background:
            linear-gradient(135deg,
                var(--ets-blue),
                var(--ets-violet));

        overflow: hidden;

    }


    .ets-chatbot-avatar img {

        width: 88%;

        height: 88%;

        object-fit: contain;

        display: block;

    }


    .ets-chatbot-title {

        color:
            var(--ets-text);

        font-size: 15px;

        font-weight: 700;

        letter-spacing: .3px;

    }


    .ets-chatbot-status {

        margin-top: 2px;

        color:
            var(--ets-muted);

        font-size: 11px;

    }


    .ets-chatbot-status span {

        display: inline-block;

        width: 7px;

        height: 7px;

        margin-right: 4px;

        border-radius: 50%;

        background: #3fb950;

        box-shadow:
            0 0 8px rgba(63, 185, 80, .7);

    }


    /* =========================================================
       HEADER ACTIONS
    ========================================================= */

    .ets-chatbot-actions {

        display: flex;

        gap: 3px;

    }


    .ets-chatbot-actions button {

        width: 34px;

        height: 34px;

        border: 0;

        border-radius: 8px;

        background: transparent;

        color:
            var(--ets-muted);

        cursor: pointer;

        transition:
            background .2s ease,
            color .2s ease;

    }


    .ets-chatbot-actions button:hover {

        color:
            var(--ets-text);

        background:
            var(--ets-panel-2);

    }


    /* =========================================================
       CURRENT PAGE
    ========================================================= */

    .ets-current-page {

        display: flex;

        align-items: center;

        gap: 7px;

        padding: 8px 14px;

        color:
            var(--ets-muted);

        background:
            var(--ets-panel);

        border-bottom:
            1px solid var(--ets-border);

        font-size: 10px;

        white-space: nowrap;

        overflow: hidden;

    }


    .ets-current-page i {

        flex: 0 0 auto;

        color:
            var(--ets-blue);

    }


    .ets-current-page span {

        overflow: hidden;

        text-overflow: ellipsis;

    }


    /* =========================================================
       MESSAGE AREA
    ========================================================= */

    .ets-chatbot-messages {

        flex: 1;

        overflow-y: auto;

        padding: 18px 15px;

        background:
            var(--ets-bg);

        transition:
            background .25s ease;

    }


    .ets-chatbot-messages::-webkit-scrollbar {

        width: 5px;

    }


    .ets-chatbot-messages::-webkit-scrollbar-thumb {

        background:
            var(--ets-border);

        border-radius: 10px;

    }


    /* =========================================================
       MESSAGE
    ========================================================= */

    .ets-chat-message {

        display: flex;

        gap: 9px;

        margin-bottom: 16px;

    }


    .user-message {

        justify-content: flex-end;

    }


    .ets-small-avatar {

        flex: 0 0 28px;

        width: 28px;

        height: 28px;

        border-radius: 9px;

        display: flex;

        align-items: center;

        justify-content: center;

        color: white;

        background:
            linear-gradient(135deg,
                var(--ets-blue),
                var(--ets-violet));

        font-size: 12px;

        overflow: hidden;

    }


    .ets-small-avatar img {

        width: 88%;

        height: 88%;

        object-fit: contain;

        display: block;

    }


    .user-message .ets-small-avatar {

        display: none;

    }


    /* =========================================================
       BUBBLE
    ========================================================= */

    .ets-chat-bubble {

        max-width: 82%;

        padding: 10px 12px;

        border:
            1px solid var(--ets-border);

        border-radius: 12px;

        background:
            var(--ets-panel);

    }


    .user-message .ets-chat-bubble {

        background:
            var(--ets-user-bg);

        border-color:
            var(--ets-user-bg);

    }


    /* =========================================================
       MESSAGE NAME
    ========================================================= */

    .ets-chat-name {

        margin-bottom: 5px;

        color:
            var(--ets-muted);

        font-size: 10px;

        font-weight: 700;

        text-transform: uppercase;

        letter-spacing: .5px;

    }


    /* =========================================================
       MESSAGE TEXT
    ========================================================= */

    .ets-chat-text {

        color:
            var(--ets-text-secondary);

        font-size: 13px;

        line-height: 1.6;

        word-break: break-word;

    }


    .user-message .ets-chat-text {

        color:
            var(--ets-user-text);

    }


    /* =========================================================
       INLINE CODE
    ========================================================= */

    .ets-chat-text code {

        padding: 2px 5px;

        border-radius: 5px;

        background:
            var(--ets-code-bg);

        color:
            var(--ets-code-text);

        font-size: 11px;

    }


    /* =========================================================
       MATHJAX
    ========================================================= */

    .ets-chat-text mjx-container {

        max-width: 100%;

        overflow-x: auto;

        overflow-y: hidden;

    }


    .ets-chat-text mjx-container[display="true"] {

        margin:
            12px 0 !important;

    }


    /* =========================================================
       QUICK QUESTIONS
    ========================================================= */

    .ets-quick-questions {

        display: flex;

        flex-wrap: wrap;

        gap: 7px;

        margin:
            0 0 12px 37px;

    }


    .ets-quick-questions button {

        padding: 7px 10px;

        border:
            1px solid var(--ets-border);

        border-radius: 999px;

        background:
            var(--ets-panel);

        color:
            var(--ets-text-secondary);

        font-size: 11px;

        cursor: pointer;

        transition:
            .2s ease;

    }


    .ets-quick-questions button:hover {

        color:
            var(--ets-text);

        border-color:
            var(--ets-blue);

        background:
            var(--ets-panel-2);

    }


    .ets-quick-questions i {

        color:
            var(--ets-blue);

        margin-right: 4px;

    }


    /* =========================================================
       TYPING INDICATOR
    ========================================================= */

    .ets-chatbot-typing {

        display: none;

        align-items: center;

        gap: 9px;

        padding:
            0 15px 10px;

        background:
            var(--ets-bg);

    }


    .ets-typing-bubble {

        display: flex;

        gap: 4px;

        padding: 11px 13px;

        border:
            1px solid var(--ets-border);

        border-radius: 12px;

        background:
            var(--ets-panel);

    }


    .ets-typing-bubble span {

        width: 5px;

        height: 5px;

        border-radius: 50%;

        background:
            var(--ets-muted);

        animation:
            etsTyping 1.2s infinite;

    }


    .ets-typing-bubble span:nth-child(2) {

        animation-delay: .15s;

    }


    .ets-typing-bubble span:nth-child(3) {

        animation-delay: .30s;

    }


    @keyframes etsTyping {

        0%,
        60%,
        100% {

            transform:
                translateY(0);

            opacity: .5;

        }

        30% {

            transform:
                translateY(-4px);

            opacity: 1;

        }

    }


    /* =========================================================
       INPUT AREA
    ========================================================= */

    .ets-chatbot-input-area {

        padding: 12px;

        border-top:
            1px solid var(--ets-border);

        background:
            var(--ets-panel);

    }


    .ets-input-wrapper {

        display: flex;

        align-items: flex-end;

        gap: 7px;

        padding: 7px;

        border:
            1px solid var(--ets-border);

        border-radius: 12px;

        background:
            var(--ets-bg);

    }


    .ets-input-wrapper:focus-within {

        border-color:
            var(--ets-blue);

        box-shadow:
            0 0 0 3px rgba(47, 129, 247, .10);

    }


    .ets-input-wrapper textarea {

        flex: 1;

        resize: none;

        max-height: 120px;

        border: 0;

        outline: 0;

        background: transparent;

        color:
            var(--ets-text);

        font-size: 13px;

        line-height: 1.45;

        padding: 6px;

    }


    .ets-input-wrapper textarea::placeholder {

        color:
            var(--ets-muted);

    }


    #etsChatbotSend {

        width: 38px;

        height: 38px;

        flex: 0 0 38px;

        border: 0;

        border-radius: 9px;

        color: white;

        background:
            linear-gradient(135deg,
                #0b4f8a,
                #2f81f7);

        cursor: pointer;

        transition:
            transform .2s ease,
            opacity .2s ease;

    }


    #etsChatbotSend:hover {

        transform:
            translateY(-1px);

    }


    #etsChatbotSend:disabled {

        opacity: .45;

        cursor: not-allowed;

        transform: none;

    }


    /* =========================================================
       DISCLAIMER
    ========================================================= */

    .ets-ai-disclaimer {

        padding-top: 7px;

        color:
            var(--ets-muted);

        font-size: 9px;

        line-height: 1.4;

        text-align: center;

    }


    .ets-ai-disclaimer i {

        color:
            #3fb950;

    }


    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 576px) {

        #etsChatbot {

            right: 14px;

            bottom: 14px;

        }


        .ets-chatbot-toggle {

            width: 56px;

            height: 56px;

        }


        .ets-chatbot-window {

            position: fixed;

            left: 10px;

            right: 10px;

            bottom: 80px;

            width: auto;

            height:
                calc(100vh - 100px);

            max-height:
                calc(100vh - 100px);

            border-radius: 16px;

        }


        .ets-chat-bubble {

            max-width: 88%;

        }

    }


    /* =========================================================
       REDUCED MOTION
    ========================================================= */

    @media (prefers-reduced-motion: reduce) {

        .ets-chatbot-pulse,

        .ets-typing-bubble span {

            animation: none;

        }

    }
</style>


<!-- =========================================================
     CHATBOT JAVASCRIPT
========================================================= -->

<script>
    (function() {

        "use strict";


        /* =========================================================
           CONFIGURATION
        ========================================================= */

        const CHAT_API =
            "../api/chatbot.php";


        const STORAGE_KEY =
            "ets_async_ai_chat";


        const THEME_KEY =
            "ets_async_ai_theme";


        /* =========================================================
           ELEMENTS
        ========================================================= */

        const chatbot =
            document.getElementById(
                "etsChatbot"
            );


        const toggle =
            document.getElementById(
                "etsChatbotToggle"
            );


        const windowEl =
            document.getElementById(
                "etsChatbotWindow"
            );


        const close =
            document.getElementById(
                "etsChatbotClose"
            );


        const clear =
            document.getElementById(
                "etsChatbotClear"
            );


        const themeButton =
            document.getElementById(
                "etsChatbotTheme"
            );


        const themeIcon =
            document.getElementById(
                "etsChatbotThemeIcon"
            );


        const messages =
            document.getElementById(
                "etsChatbotMessages"
            );


        const input =
            document.getElementById(
                "etsChatbotInput"
            );


        const send =
            document.getElementById(
                "etsChatbotSend"
            );


        const typing =
            document.getElementById(
                "etsChatbotTyping"
            );


        const currentPage =
            document.getElementById(
                "etsCurrentPage"
            );


        const quickQuestions =
            document.querySelectorAll(
                ".ets-quick-questions button"
            );


        /* =========================================================
           STATE
        ========================================================= */

        let conversation = [];

        let isSending = false;


        /* =========================================================
           THEME
        ========================================================= */

        function applyTheme(theme) {

            if (
                theme === "dark"
            ) {

                chatbot.classList.add(
                    "ets-dark"
                );


                themeIcon.className =
                    "bi bi-moon-fill";


                themeButton.title =
                    "Switch to light theme";

                themeButton.setAttribute(
                    "aria-label",
                    "Switch to light theme"
                );

            } else {

                chatbot.classList.remove(
                    "ets-dark"
                );


                themeIcon.className =
                    "bi bi-sun-fill";


                themeButton.title =
                    "Switch to dark theme";

                themeButton.setAttribute(
                    "aria-label",
                    "Switch to dark theme"
                );

            }


            try {

                localStorage.setItem(
                    THEME_KEY,
                    theme
                );

            } catch (error) {

                console.warn(
                    "ETSY theme storage unavailable:",
                    error
                );

            }

        }


        function loadTheme() {

            let savedTheme =
                null;


            try {

                savedTheme =
                    localStorage.getItem(
                        THEME_KEY
                    );

            } catch (error) {

                savedTheme =
                    null;

            }


            if (
                savedTheme === "dark" ||
                savedTheme === "light"
            ) {

                applyTheme(
                    savedTheme
                );

                return;

            }


            const prefersDark =
                window.matchMedia &&
                window.matchMedia(
                    "(prefers-color-scheme: dark)"
                ).matches;


            applyTheme(
                prefersDark ?
                "dark" :
                "light"
            );

        }


        themeButton.addEventListener(
            "click",
            function() {

                const isDark =
                    chatbot.classList.contains(
                        "ets-dark"
                    );


                applyTheme(
                    isDark ?
                    "light" :
                    "dark"
                );

            }
        );


        /* =========================================================
           CURRENT PAGE INFORMATION
        ========================================================= */

        function getCurrentPageContext() {

            const title =
                document.title ||
                "ETS-Async Page";


            const url =
                window.location.origin +
                window.location.pathname;


            const heading =
                document.querySelector("h1")?.innerText ||
                document.querySelector("h2")?.innerText ||
                "";


            const description =
                document.querySelector(
                    'meta[name="description"]'
                )?.content || "";


            let source =
                document.querySelector("main") ||
                document.querySelector("#main-content") ||
                document.querySelector(".container") ||
                document.body;


            const clone =
                source.cloneNode(true);


            const elementsToRemove =
                clone.querySelectorAll(
                    "script, style, noscript, #etsChatbot, nav, footer, header"
                );


            elementsToRemove.forEach(
                function(element) {

                    element.remove();

                }
            );


            let pageText =
                clone.innerText || "";


            pageText =
                pageText
                .replace(/\s+/g, " ")
                .trim();


            if (
                pageText.length > 12000
            ) {

                pageText =
                    pageText.substring(
                        0,
                        12000
                    ) +
                    "\n[Page content truncated]";

            }


            return {

                title: title,

                url: url,

                heading: heading,

                description: description,

                content: pageText

            };

        }


        /* =========================================================
           DISPLAY CURRENT PAGE
        ========================================================= */

        function updateCurrentPageDisplay() {

            const context =
                getCurrentPageContext();


            if (!currentPage) {

                return;

            }


            const label =
                context.heading ||
                context.title ||
                "Current page";


            currentPage.innerHTML =

                '<i class="bi bi-file-earmark-text"></i>' +

                '<span title="' +
                escapeHTML(label) +
                '">' +

                escapeHTML(label) +

                '</span>';

        }


        /* =========================================================
           ESCAPE HTML
        ========================================================= */

        function escapeHTML(text) {

            const div =
                document.createElement(
                    "div"
                );


            div.textContent =
                text;


            return div.innerHTML;

        }


        /* =========================================================
           FORMAT RESPONSE
        ========================================================= */

        function formatResponse(text) {

            if (!text) {

                return "";

            }


            const mathBlocks = [];


            function protectMath(match) {

                const index =
                    mathBlocks.length;


                mathBlocks.push(
                    match
                );


                return (
                    "@@ETSY_MATH_" +
                    index +
                    "@@"
                );

            }


            text =
                text.replace(
                    /\\\[[\s\S]*?\\\]/g,
                    protectMath
                );


            text =
                text.replace(
                    /\$\$[\s\S]*?\$\$/g,
                    protectMath
                );


            text =
                text.replace(
                    /\\\([\s\S]*?\\\)/g,
                    protectMath
                );


            text =
                text.replace(
                    /(?<!\$)\$(?!\$)[\s\S]*?(?<!\$)\$(?!\$)/g,
                    protectMath
                );


            let html =
                escapeHTML(text);


            html =
                html.replace(
                    /\*\*([\s\S]*?)\*\*/g,
                    "<strong>$1</strong>"
                );


            html =
                html.replace(
                    /`([^`\n]+)`/g,
                    "<code>$1</code>"
                );


            html =
                html.replace(
                    /\n/g,
                    "<br>"
                );


            mathBlocks.forEach(
                function(math, index) {

                    html =
                        html.replace(
                            "@@ETSY_MATH_" +
                            index +
                            "@@",
                            math
                        );

                }
            );


            return html;

        }


        /* =========================================================
           RENDER MATHJAX
        ========================================================= */

        function renderMath(element) {

            if (
                !window.MathJax
            ) {

                setTimeout(
                    function() {

                        renderMath(
                            element
                        );

                    },
                    300
                );

                return;

            }


            MathJax.typesetPromise(
                    [element]
                )
                .catch(
                    function(error) {

                        console.error(
                            "ETSY MathJax rendering error:",
                            error
                        );

                    }
                );

        }


        /* =========================================================
           ADD MESSAGE
        ========================================================= */

        function addMessage(
            role,
            text,
            animate = true
        ) {

            const wrapper =
                document.createElement(
                    "div"
                );


            wrapper.className =
                "ets-chat-message " +
                (
                    role === "user" ?
                    "user-message" :
                    "ai-message"
                );


            const avatar =
                document.createElement(
                    "div"
                );


            avatar.className =
                "ets-small-avatar";


            /*
             * ETSY mascot is used for assistant
             * messages. User messages retain
             * the normal Bootstrap person icon.
             */

            avatar.innerHTML =
                role === "user" ?

                '<i class="bi bi-person-fill"></i>' :

                '<img src="../assets/etsy.png" alt="ETSY">';


            const bubble =
                document.createElement(
                    "div"
                );


            bubble.className =
                "ets-chat-bubble";


            if (
                role === "assistant"
            ) {

                bubble.innerHTML =

                    '<div class="ets-chat-name">' +

                    'ETSY' +

                    '</div>' +

                    '<div class="ets-chat-text">' +

                    formatResponse(
                        text
                    ) +

                    '</div>';

            } else {

                bubble.innerHTML =

                    '<div class="ets-chat-text">' +

                    formatResponse(
                        text
                    ) +

                    '</div>';

            }


            wrapper.appendChild(
                avatar
            );


            wrapper.appendChild(
                bubble
            );


            messages.appendChild(
                wrapper
            );


            if (
                role === "assistant"
            ) {

                renderMath(
                    wrapper
                );

            }


            if (
                animate &&
                window.anime
            ) {

                anime({

                    targets: wrapper,

                    opacity: [0, 1],

                    translateY: [10, 0],

                    duration: 280,

                    easing: "easeOutQuad"

                });

            }


            scrollBottom();

        }


        /* =========================================================
           SCROLL
        ========================================================= */

        function scrollBottom() {

            requestAnimationFrame(
                function() {

                    messages.scrollTop =
                        messages.scrollHeight;

                }
            );

        }


        /* =========================================================
           LOAD CONVERSATION
        ========================================================= */

        function loadConversation() {

            try {

                const saved =
                    localStorage.getItem(
                        STORAGE_KEY
                    );


                if (!saved) {

                    return;

                }


                const savedData =
                    JSON.parse(
                        saved
                    );


                if (
                    !Array.isArray(
                        savedData
                    )
                ) {

                    return;

                }


                conversation =
                    savedData.slice(
                        -20
                    );


                savedData.forEach(
                    function(item) {

                        if (
                            item &&
                            item.role &&
                            item.text
                        ) {

                            addMessage(
                                item.role,
                                item.text,
                                false
                            );

                        }

                    }
                );


                if (
                    savedData.length > 0
                ) {

                    const quick =
                        document.getElementById(
                            "etsQuickQuestions"
                        );


                    if (quick) {

                        quick.style.display =
                            "none";

                    }

                }

            } catch (error) {

                console.error(
                    "ETSY chat history error:",
                    error
                );

            }

        }


        /* =========================================================
           SAVE CONVERSATION
        ========================================================= */

        function saveConversation() {

            try {

                if (
                    conversation.length > 20
                ) {

                    conversation =
                        conversation.slice(
                            -20
                        );

                }


                localStorage.setItem(
                    STORAGE_KEY,
                    JSON.stringify(
                        conversation
                    )
                );

            } catch (error) {

                console.error(
                    "ETSY save error:",
                    error
                );

            }

        }


        /* =========================================================
           OPEN CHAT
        ========================================================= */

        function openChat() {

            updateCurrentPageDisplay();


            windowEl.classList.add(
                "open"
            );


            windowEl.setAttribute(
                "aria-hidden",
                "false"
            );


            if (
                window.anime
            ) {

                anime({

                    targets: windowEl,

                    opacity: [0, 1],

                    translateY: [20, 0],

                    scale: [.96, 1],

                    duration: 300,

                    easing: "easeOutCubic"

                });

            }


            setTimeout(
                function() {

                    input.focus();

                    scrollBottom();

                },
                250
            );

        }


        /* =========================================================
           CLOSE CHAT
        ========================================================= */

        function closeChat() {

            if (
                window.anime
            ) {

                anime({

                    targets: windowEl,

                    opacity: [1, 0],

                    translateY: [0, 20],

                    scale: [1, .96],

                    duration: 200,

                    easing: "easeInCubic",

                    complete: function() {

                        windowEl.classList.remove(
                            "open"
                        );

                    }

                });

            } else {

                windowEl.classList.remove(
                    "open"
                );

            }


            windowEl.setAttribute(
                "aria-hidden",
                "true"
            );

        }


        /* =========================================================
           TYPING INDICATOR
        ========================================================= */

        function showTyping() {

            typing.style.display =
                "flex";


            scrollBottom();

        }


        function hideTyping() {

            typing.style.display =
                "none";

        }


        /* =========================================================
           SEND MESSAGE
        ========================================================= */

        async function sendMessage(
            text
        ) {

            text =
                text.trim();


            if (
                !text ||
                isSending
            ) {

                return;

            }


            isSending =
                true;


            send.disabled =
                true;


            const quick =
                document.getElementById(
                    "etsQuickQuestions"
                );


            if (quick) {

                quick.style.display =
                    "none";

            }


            const pageContext =
                getCurrentPageContext();


            addMessage(
                "user",
                text
            );


            conversation.push({

                role: "user",

                text: text

            });


            saveConversation();


            input.value = "";

            resizeInput();


            showTyping();


            try {

                const response =
                    await fetch(
                        CHAT_API, {

                            method: "POST",

                            headers: {

                                "Content-Type": "application/json"

                            },

                            body: JSON.stringify({

                                message: text,

                                history: conversation,

                                page_context: pageContext

                            })

                        }
                    );


                let data;


                try {

                    data =
                        await response.json();

                } catch (error) {

                    throw new Error(
                        "Invalid server response."
                    );

                }


                if (
                    !response.ok ||
                    !data.success
                ) {

                    throw new Error(
                        data.message ||
                        "ETSY request failed."
                    );

                }


                const reply =
                    data.reply ||
                    "I could not generate a response.";


                hideTyping();


                addMessage(
                    "assistant",
                    reply
                );


                conversation.push({

                    role: "assistant",

                    text: reply

                });


                saveConversation();

            } catch (error) {

                console.error(
                    "ETSY Error:",
                    error
                );


                hideTyping();


                addMessage(
                    "assistant",
                    "Sorry, I couldn't connect to ETSY right now. Please try again."
                );

            } finally {

                isSending =
                    false;


                send.disabled =
                    false;


                input.focus();

            }

        }


        /* =========================================================
           TEXTAREA RESIZE
        ========================================================= */

        function resizeInput() {

            input.style.height =
                "auto";


            input.style.height =
                Math.min(
                    input.scrollHeight,
                    120
                ) + "px";

        }


        /* =========================================================
           TOGGLE CHAT
        ========================================================= */

        toggle.addEventListener(
            "click",
            function() {

                if (
                    windowEl.classList.contains(
                        "open"
                    )
                ) {

                    closeChat();

                } else {

                    openChat();

                }

            }
        );


        /* =========================================================
           CLOSE BUTTON
        ========================================================= */

        close.addEventListener(
            "click",
            closeChat
        );


        /* =========================================================
           SEND BUTTON
        ========================================================= */

        send.addEventListener(
            "click",
            function() {

                sendMessage(
                    input.value
                );

            }
        );


        /* =========================================================
           ENTER KEY
        ========================================================= */

        input.addEventListener(
            "keydown",
            function(event) {

                if (
                    event.key === "Enter" &&
                    !event.shiftKey
                ) {

                    event.preventDefault();


                    sendMessage(
                        input.value
                    );

                }

            }
        );


        /* =========================================================
           INPUT EVENT
        ========================================================= */

        input.addEventListener(
            "input",
            resizeInput
        );


        /* =========================================================
           QUICK QUESTIONS
        ========================================================= */

        quickQuestions.forEach(
            function(button) {

                button.addEventListener(
                    "click",
                    function() {

                        input.value =
                            button.dataset.question;


                        resizeInput();


                        input.focus();

                    }
                );

            }
        );


        /* =========================================================
           CLEAR CHAT
        ========================================================= */

        clear.addEventListener(
            "click",
            function() {

                if (
                    !confirm(
                        "Clear your ETSY conversation?"
                    )
                ) {

                    return;

                }


                conversation = [];


                localStorage.removeItem(
                    STORAGE_KEY
                );


                messages.innerHTML = `

                <div class="ets-chat-message ai-message">

                    <div class="ets-small-avatar">

                        <img
                            src="../assets/etsy.png"
                            alt="ETSY">

                    </div>

                    <div class="ets-chat-bubble">

                        <div class="ets-chat-name">
                            ETSY
                        </div>

                        <div class="ets-chat-text">

                            Conversation cleared.

                            <br><br>

                            What would you like
                            to learn?

                        </div>

                    </div>

                </div>

                <div
                    id="etsQuickQuestions"
                    class="ets-quick-questions">

                    <button
                        type="button"
                        data-question="Explain the main topic on this page in simple terms.">

                        <i class="bi bi-lightbulb"></i>

                        Explain this page

                    </button>

                    <button
                        type="button"
                        data-question="Explain the most important concept on this page step by step.">

                        <i class="bi bi-diagram-3"></i>

                        Explain the concept

                    </button>

                    <button
                        type="button"
                        data-question="Give me an example based on the topic currently shown on this page.">

                        <i class="bi bi-code-slash"></i>

                        Give an example

                    </button>

                    <button
                        type="button"
                        data-question="Give me a short review of the topic on this page.">

                        <i class="bi bi-journal-text"></i>

                        Review this topic

                    </button>

                </div>

            `;


                const newQuickQuestions =
                    document.querySelectorAll(
                        "#etsQuickQuestions button"
                    );


                newQuickQuestions.forEach(
                    function(button) {

                        button.addEventListener(
                            "click",
                            function() {

                                input.value =
                                    button.dataset.question;

                                resizeInput();

                                input.focus();

                            }
                        );

                    }
                );


                scrollBottom();

            }
        );


        /* =========================================================
           ESCAPE KEY
        ========================================================= */

        document.addEventListener(
            "keydown",
            function(event) {

                if (
                    event.key === "Escape" &&
                    windowEl.classList.contains(
                        "open"
                    )
                ) {

                    closeChat();

                }

            }
        );


        /* =========================================================
           INITIALIZATION
        ========================================================= */

        loadTheme();


        updateCurrentPageDisplay();


        loadConversation();


        /* =========================================================
           BUTTON ENTRANCE ANIMATION
        ========================================================= */

        if (
            window.anime
        ) {

            anime({

                targets: ".ets-chatbot-toggle",

                scale: [0, 1],

                opacity: [0, 1],

                duration: 700,

                delay: 500,

                easing: "easeOutBack"

            });

        }


    })();
</script>