<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   SUPPORT CREATOR
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
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   PAGE VARIABLES
========================================================== */

$firstName = htmlspecialchars(
    $user["first_name"] ?? "",
    ENT_QUOTES,
    "UTF-8"
);

$lastName = htmlspecialchars(
    $user["last_name"] ?? "",
    ENT_QUOTES,
    "UTF-8"
);

$email = htmlspecialchars(
    $user["email"] ?? "",
    ENT_QUOTES,
    "UTF-8"
);

$studentId = htmlspecialchars(
    $user["student_id"] ?? "",
    ENT_QUOTES,
    "UTF-8"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php require_once "globals/head.php"; ?>

    <title>Support Creator | ETS-Async</title>


    <style>
        /* =====================================================
           SUPPORT CREATOR VARIABLES
        ====================================================== */

        :root {

            --support-blue: #2563eb;
            --support-blue-dark: #1d4ed8;

            --support-bg: #f8fafc;
            --support-card: #ffffff;

            --support-text: #0f172a;
            --support-muted: #64748b;

            --support-border: #e2e8f0;

            --support-success: #16a34a;

        }


        /* =====================================================
           DARK MODE
        ====================================================== */

        html[data-theme="dark"] {

            --support-bg: #0f172a;
            --support-card: #1e293b;

            --support-text: #f8fafc;
            --support-muted: #94a3b8;

            --support-border: #334155;

        }


        /* =====================================================
           MAIN CONTAINER
        ====================================================== */

        .support-layout {

            max-width: 1100px;

            margin: 0 auto;

        }


        /* =====================================================
           SUPPORT CARD
        ====================================================== */

        .support-card {

            background: var(--support-card);

            border: 1px solid var(--support-border);

            border-radius: 18px;

            padding: 35px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .05);

        }


        /* =====================================================
           HEADER
        ====================================================== */

        .support-header {

            text-align: center;

            margin-bottom: 30px;

        }


        .support-icon {

            width: 75px;
            height: 75px;

            border-radius: 50%;

            background: rgba(37, 99, 235, .1);

            color: var(--support-blue);

            display: flex;

            align-items: center;
            justify-content: center;

            margin: 0 auto 20px;

            font-size: 34px;

        }


        .support-header h2 {

            color: var(--support-text);

            font-weight: 700;

        }


        .support-header p {

            color: var(--support-muted);

            max-width: 650px;

            margin: 0 auto;

        }


        /* =====================================================
           AMOUNT BUTTONS
        ====================================================== */

        .amount-buttons {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 12px;

            margin-bottom: 20px;

        }


        .amount-button {

            border: 1px solid var(--support-border);

            background: var(--support-card);

            color: var(--support-text);

            padding: 15px;

            border-radius: 10px;

            font-weight: 600;

            cursor: pointer;

            transition: .2s;

        }


        .amount-button:hover {

            border-color: var(--support-blue);

            color: var(--support-blue);

        }


        .amount-button.active {

            background: var(--support-blue);

            border-color: var(--support-blue);

            color: white;

        }


        /* =====================================================
           FORM LABEL
        ====================================================== */

        .form-label {

            color: var(--support-text);

            font-weight: 600;

        }


        /* =====================================================
           FORM INPUT
        ====================================================== */

        .form-control {

            background: var(--support-card);

            border-color: var(--support-border);

            color: var(--support-text);

        }


        .form-control:focus {

            border-color: var(--support-blue);

            box-shadow:
                0 0 0 .2rem rgba(37, 99, 235, .15);

        }


        /* =====================================================
           PAY BUTTON
        ====================================================== */

        .pay-button {

            width: 100%;

            padding: 15px;

            border: none;

            border-radius: 10px;

            background: var(--support-blue);

            color: white;

            font-weight: 700;

            font-size: 16px;

            transition: .2s;

        }


        .pay-button:hover {

            background: var(--support-blue-dark);

        }


        .pay-button:disabled {

            opacity: .6;

            cursor: not-allowed;

        }


        /* =====================================================
           INFORMATION BOX
        ====================================================== */

        .support-info {

            margin-top: 25px;

            padding: 18px;

            border-radius: 12px;

            background: rgba(37, 99, 235, .06);

            color: var(--support-muted);

            font-size: 14px;

        }


        /* =====================================================
           SECURE PAYMENT
        ====================================================== */

        .secure-payment {

            text-align: center;

            margin-top: 20px;

            color: var(--support-muted);

            font-size: 13px;

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media(max-width:700px) {

            .support-card {

                padding: 22px;

            }

            .amount-buttons {

                grid-template-columns:
                    repeat(2, 1fr);

            }

        }


        @media(max-width:450px) {

            .amount-buttons {

                grid-template-columns:
                    1fr 1fr;

            }

        }
    </style>

</head>


<body>

    <?php require_once "globals/sidebar.php"; ?>

    <?php require_once "globals/topbar.php"; ?>


    <main class="main-content">

        <div class="content-wrapper">


            <!-- =================================================
             PAGE HEADER
        ================================================== -->

            <div class="mb-4">

                <h1 class="fw-bold">

                    <i class="bi bi-heart me-2"></i>

                    Support Developer


                </h1>

                <p class="text-muted">

                    Help support the continued development
                    of ETS-Async learning resources and tools.
                    <br>
                    <a href="support_rankings.php" class="btn btn-sm btn-primary mx-2 my-2"><i class="bi bi-trophy me-2"></i>View Rankings</a>

                </p>

            </div>


            <!-- =================================================
             SUPPORT LAYOUT
        ================================================== -->

            <div class="support-layout">

                <div class="support-card">


                    <!-- =============================================
                     HEADER
                ============================================== -->

                    <div class="support-header">

                        <div class="support-icon">

                            <i class="bi bi-heart-fill"></i>

                        </div>


                        <h2>

                            Support ETS-Async

                        </h2>


                        <p>

                            Your support helps maintain the
                            learning portal, develop academic tools,
                            and create additional resources for
                            College of Engineering and Architecture students.

                        </p>

                    </div>


                    <!-- =============================================
                     PAYMENT FORM
                ============================================== -->

                    <form
                        action="support_create.php"
                        method="POST"
                        id="supportForm">


                        <!-- =========================================
                         AMOUNT
                    ========================================== -->

                        <div class="mb-4">

                            <label class="form-label">

                                Select Support Amount

                            </label>


                            <div class="amount-buttons">

                                <button
                                    type="button"
                                    class="amount-button"
                                    data-amount="50">
                                    ₱50
                                </button>


                                <button
                                    type="button"
                                    class="amount-button"
                                    data-amount="100">
                                    ₱100
                                </button>


                                <button
                                    type="button"
                                    class="amount-button"
                                    data-amount="250">
                                    ₱250
                                </button>


                                <button
                                    type="button"
                                    class="amount-button"
                                    data-amount="500">
                                    ₱500
                                </button>

                            </div>


                            <label class="form-label">

                                Or enter your own amount

                            </label>


                            <div class="input-group">

                                <span class="input-group-text">

                                    ₱

                                </span>

                                <input
                                    type="number"
                                    class="form-control"
                                    name="amount"
                                    id="amount"
                                    min="1"
                                    max="50000"
                                    step="0.01"
                                    placeholder="100.00"
                                    required>

                            </div>


                            <small class="text-muted">

                                Minimum support amount: ₱1.00

                            </small>

                        </div>


                        <!-- =========================================
                         NAME
                    ========================================== -->

                        <div class="mb-3">

                            <label class="form-label">

                                Name

                            </label>

                            <input
                                type="text"
                                name="donor_name"
                                class="form-control"
                                value="<?= $firstName . ' ' . $lastName ?>"
                                maxlength="255"
                                required>

                        </div>


                        <!-- =========================================
                         EMAIL
                    ========================================== -->

                        <div class="mb-3">

                            <label class="form-label">

                                Email Address

                            </label>

                            <input
                                type="email"
                                name="donor_email"
                                class="form-control"
                                value="<?= $email ?>"
                                maxlength="255"
                                required>

                        </div>


                        <!-- =========================================
                         MESSAGE
                    ========================================== -->

                        <div class="mb-4">

                            <label class="form-label">

                                Message
                                <span class="text-muted">
                                    (Optional)
                                </span>

                            </label>

                            <textarea
                                name="message"
                                class="form-control"
                                rows="4"
                                maxlength="1000"
                                placeholder="Leave an optional message..."></textarea>

                        </div>


                        <!-- =========================================
                         STUDENT ID
                    ========================================== -->

                        <input
                            type="hidden"
                            name="student_id"
                            value="<?= $studentId ?>">


                        <!-- =========================================
                         PAY BUTTON
                    ========================================== -->

                        <button
                            type="submit"
                            class="pay-button"
                            id="payButton">

                            <i class="bi bi-credit-card me-2"></i>

                            Continue to Secure Payment

                        </button>


                    </form>


                    <!-- =============================================
                     INFORMATION
                ============================================== -->

                    <div class="support-info">

                        <i class="bi bi-info-circle me-2"></i>

                        You will be redirected to PayMongo's
                        secure hosted checkout page to complete
                        your payment.

                    </div>


                    <div class="secure-payment">

                        <i class="bi bi-shield-check me-1"></i>

                        Secure payment powered by PayMongo

                    </div>


                </div>

            </div>

        </div>

    </main>


    <?php require_once "globals/scripts.php"; ?>


    <script>
        /* =========================================================
   AMOUNT BUTTONS
========================================================== */

        document.addEventListener(
            "DOMContentLoaded",
            function() {


                const amountInput =
                    document.getElementById("amount");


                const amountButtons =
                    document.querySelectorAll(
                        ".amount-button"
                    );


                amountButtons.forEach(
                    function(button) {

                        button.addEventListener(
                            "click",
                            function() {

                                amountButtons.forEach(
                                    function(btn) {

                                        btn.classList.remove(
                                            "active"
                                        );

                                    }
                                );


                                button.classList.add(
                                    "active"
                                );


                                amountInput.value =
                                    button.dataset.amount;

                            }
                        );

                    }
                );


                /* =====================================================
                   FORM SUBMISSION
                ====================================================== */

                const form =
                    document.getElementById("supportForm");


                const payButton =
                    document.getElementById("payButton");


                form.addEventListener(
                    "submit",
                    function() {

                        payButton.disabled = true;

                        payButton.innerHTML =
                            '<span class="spinner-border spinner-border-sm me-2"></span>' +
                            'Creating Secure Checkout...';

                    }
                );

            }
        );
    </script>

</body>

</html>