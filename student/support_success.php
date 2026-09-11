
<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   SUPPORT PAYMENT SUCCESS
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
   DATABASE
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   REFERENCE
========================================================== */

$reference =
    trim($_GET["reference"] ?? "");


$transaction = null;


/* =========================================================
   GET TRANSACTION
========================================================== */

if ($reference !== "") {

    $stmt = $mysqli->prepare(
        "SELECT
            id,
            amount,
            currency,
            reference_code,
            status,
            donor_name,
            created_at,
            paid_at
         FROM support_transactions
         WHERE reference_code = ?
         LIMIT 1"
    );


    if ($stmt) {

        $stmt->bind_param(
            "s",
            $reference
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
   TRANSACTION STATUS
========================================================== */

$status =
    strtolower(
        $transaction["status"] ?? "pending"
    );


/* =========================================================
   STATUS DISPLAY VARIABLES
========================================================== */

$statusClass =
    "status-pending";

$statusIcon =
    "bi-hourglass-split";

$statusTitle =
    "Thank You for Your Support!";

$statusMessage =
    "We truly appreciate your generosity and your
     support for the continued development of ETS-Async.";


/* =========================================================
   PAID STATUS
========================================================== */

if ($status === "paid") {

    $statusClass =
        "status-paid";

    $statusIcon =
        "bi-check-lg";

    $statusTitle =
        "Thank You So Much!";

    $statusMessage =
        "Your support has been successfully received.
         Your generosity helps us continue developing
         learning resources, academic tools, and
         meaningful experiences for our students.";
}


/* =========================================================
   PENDING STATUS
========================================================== */ elseif ($status === "pending") {

    $statusClass =
        "status-pending";

    $statusIcon =
        "bi-hourglass-split";

    $statusTitle =
        "Thank You for Supporting ETS-Async!";

    $statusMessage =
        "We appreciate your generosity. Your payment
         is currently being processed and will be
         confirmed once PayMongo sends the payment
         confirmation.";
}


/* =========================================================
   CANCELLED STATUS
========================================================== */ elseif ($status === "cancelled") {

    $statusClass =
        "status-cancelled";

    $statusIcon =
        "bi-arrow-counterclockwise";

    $statusTitle =
        "Thank You for Considering ETS-Async";

    $statusMessage =
        "Your payment was not completed. We still
         appreciate your willingness to support the
         continued development of the learning portal.";
}


/* =========================================================
   FAILED STATUS
========================================================== */ elseif ($status === "failed") {

    $statusClass =
        "status-failed";

    $statusIcon =
        "bi-exclamation-lg";

    $statusTitle =
        "Payment Could Not Be Completed";

    $statusMessage =
        "We appreciate your attempt to support ETS-Async.
         Unfortunately, the payment could not be completed.
         You may try again whenever you are ready.";
}


/* =========================================================
   DONOR NAME
========================================================== */

$donorName =
    trim(
        $transaction["donor_name"] ?? ""
    );


/* =========================================================
   ESCAPED DISPLAY VALUES
========================================================== */

$displayReference =
    htmlspecialchars(
        $transaction["reference_code"] ?? $reference,
        ENT_QUOTES,
        "UTF-8"
    );


$displayDonor =
    htmlspecialchars(
        $donorName,
        ENT_QUOTES,
        "UTF-8"
    );


$displayStatus =
    htmlspecialchars(
        ucfirst($status),
        ENT_QUOTES,
        "UTF-8"
    );


?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php require_once "globals/head.php"; ?>

    <title>
        Thank You | ETS-Async
    </title>


    <style>
        /* =====================================================
           SUPPORT SUCCESS VARIABLES
        ====================================================== */

        :root {

            --support-success-blue: #2563eb;
            --support-success-blue-dark: #1d4ed8;

            --support-success-bg: #f8fafc;

            --support-success-card: #ffffff;

            --support-success-text: #0f172a;

            --support-success-muted: #64748b;

            --support-success-border: #e2e8f0;

            --support-success-green: #16a34a;

            --support-success-green-bg: rgba(22,
                    163,
                    74,
                    .10);

            --support-success-yellow: #d97706;

            --support-success-yellow-bg: rgba(217,
                    119,
                    6,
                    .10);

            --support-success-red: #dc2626;

            --support-success-red-bg: rgba(220,
                    38,
                    38,
                    .10);

        }


        /* =====================================================
           DARK MODE
        ====================================================== */

        html[data-theme="dark"] {

            --support-success-bg: #0f172a;

            --support-success-card: #1e293b;

            --support-success-text: #f8fafc;

            --support-success-muted: #94a3b8;

            --support-success-border: #334155;

        }


        /* =====================================================
           MAIN SUCCESS WRAPPER
        ====================================================== */

        .success-wrapper {

            max-width: 780px;

            margin: 30px auto 50px;

        }


        /* =====================================================
           SUCCESS CARD
        ====================================================== */

        .success-card {

            position: relative;

            overflow: hidden;

            background:
                var(--support-success-card);

            border:
                1px solid var(--support-success-border);

            border-radius: 22px;

            padding: 45px;

            text-align: center;

            box-shadow:
                0 15px 40px rgba(0,
                    0,
                    0,
                    .06);

        }


        /* =====================================================
           DECORATIVE TOP AREA
        ====================================================== */

        .success-card::before {

            content: "";

            position: absolute;

            top: 0;
            left: 0;

            width: 100%;
            height: 5px;

            background:
                linear-gradient(90deg,
                    var(--support-success-blue),
                    #60a5fa,
                    var(--support-success-blue));

        }


        /* =====================================================
           SUCCESS ICON
        ====================================================== */

        .success-icon {

            width: 95px;
            height: 95px;

            border-radius: 50%;

            display: flex;

            align-items: center;

            justify-content: center;

            margin:
                0 auto 25px;

            font-size: 45px;

        }


        /* =====================================================
           PAID ICON
        ====================================================== */

        .status-paid .success-icon {

            background:
                var(--support-success-green-bg);

            color:
                var(--support-success-green);

        }


        /* =====================================================
           PENDING ICON
        ====================================================== */

        .status-pending .success-icon {

            background:
                var(--support-success-yellow-bg);

            color:
                var(--support-success-yellow);

        }


        /* =====================================================
           CANCELLED ICON
        ====================================================== */

        .status-cancelled .success-icon {

            background:
                var(--support-success-yellow-bg);

            color:
                var(--support-success-yellow);

        }


        /* =====================================================
           FAILED ICON
        ====================================================== */

        .status-failed .success-icon {

            background:
                var(--support-success-red-bg);

            color:
                var(--support-success-red);

        }


        /* =====================================================
           TITLE
        ====================================================== */

        .success-card h2 {

            color:
                var(--support-success-text);

            font-size: 30px;

            font-weight: 700;

            margin-bottom: 15px;

        }


        /* =====================================================
           MAIN MESSAGE
        ====================================================== */

        .success-message {

            max-width: 620px;

            margin:
                0 auto;

            color:
                var(--support-success-muted);

            font-size: 16px;

            line-height: 1.7;

        }


        /* =====================================================
           APPRECIATION MESSAGE
        ====================================================== */

        .appreciation-box {

            margin-top: 30px;

            padding: 22px;

            border-radius: 14px;

            background:
                rgba(37,
                    99,
                    235,
                    .07);

            border:
                1px solid rgba(37,
                    99,
                    235,
                    .12);

        }


        .appreciation-box i {

            color:
                var(--support-success-blue);

            font-size: 22px;

        }


        .appreciation-box p {

            margin:
                8px 0 0;

            color:
                var(--support-success-muted);

            line-height: 1.6;

        }


        /* =====================================================
           TRANSACTION INFORMATION
        ====================================================== */

        .transaction-box {

            margin-top: 30px;

            border:
                1px solid var(--support-success-border);

            border-radius: 14px;

            overflow: hidden;

            text-align: left;

        }


        /* =====================================================
           TRANSACTION HEADER
        ====================================================== */

        .transaction-header {

            padding: 15px 20px;

            background:
                rgba(100,
                    116,
                    139,
                    .06);

            border-bottom:
                1px solid var(--support-success-border);

            color:
                var(--support-success-text);

            font-weight: 700;

        }


        /* =====================================================
           TRANSACTION ROW
        ====================================================== */

        .transaction-row {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            padding:
                14px 20px;

            border-bottom:
                1px solid var(--support-success-border);

        }


        .transaction-row:last-child {

            border-bottom: none;

        }


        .transaction-label {

            color:
                var(--support-success-muted);

            font-size: 14px;

        }


        .transaction-value {

            color:
                var(--support-success-text);

            font-weight: 600;

            text-align: right;

            word-break: break-word;

        }


        /* =====================================================
           STATUS BADGES
        ====================================================== */

        .custom-status {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding:
                6px 12px;

            border-radius: 50px;

            font-size: 13px;

            font-weight: 700;

        }


        .custom-status.paid {

            color:
                var(--support-success-green);

            background:
                var(--support-success-green-bg);

        }


        .custom-status.pending {

            color:
                var(--support-success-yellow);

            background:
                var(--support-success-yellow-bg);

        }


        .custom-status.cancelled {

            color:
                var(--support-success-yellow);

            background:
                var(--support-success-yellow-bg);

        }


        .custom-status.failed {

            color:
                var(--support-success-red);

            background:
                var(--support-success-red-bg);

        }


        /* =====================================================
           THANK YOU FOOTER
        ====================================================== */

        .thank-you-footer {

            margin-top: 30px;

            color:
                var(--support-success-muted);

            font-size: 14px;

            line-height: 1.7;

        }


        .thank-you-footer strong {

            color:
                var(--support-success-text);

        }


        /* =====================================================
           BUTTON AREA
        ====================================================== */

        .success-actions {

            display: flex;

            justify-content: center;

            gap: 12px;

            flex-wrap: wrap;

            margin-top: 30px;

        }


        .success-actions .btn {

            border-radius: 10px;

            padding:
                11px 20px;

            font-weight: 600;

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 700px) {

            .success-wrapper {

                margin:
                    20px auto 35px;

            }


            .success-card {

                padding: 30px 20px;

                border-radius: 18px;

            }


            .success-card h2 {

                font-size: 25px;

            }


            .success-message {

                font-size: 15px;

            }


            .transaction-row {

                align-items: flex-start;

                flex-direction: column;

                gap: 5px;

            }


            .transaction-value {

                text-align: left;

            }

        }


        @media (max-width: 450px) {

            .success-icon {

                width: 80px;
                height: 80px;

                font-size: 38px;

            }


            .success-actions {

                flex-direction: column;

            }


            .success-actions .btn {

                width: 100%;

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
             SUCCESS CONTENT
        ================================================== -->

            <div class="success-wrapper">


                <div class="
                success-card
                <?= htmlspecialchars(
                    $statusClass,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            ">


                    <!-- =========================================
                     STATUS ICON
                ========================================== -->

                    <div class="success-icon">

                        <i class="
                        bi
                        <?= htmlspecialchars(
                            $statusIcon,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    "></i>

                    </div>


                    <!-- =========================================
                     TITLE
                ========================================== -->

                    <h2>

                        <?= htmlspecialchars(
                            $statusTitle,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>

                    </h2>


                    <!-- =========================================
                     MAIN MESSAGE
                ========================================== -->

                    <p class="success-message">

                        <?= htmlspecialchars(
                            $statusMessage,
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>

                    </p>


                    <!-- =========================================
                     APPRECIATION MESSAGE
                ========================================== -->

                    <div class="appreciation-box">

                        <i class="bi bi-heart-fill"></i>


                        <p>

                            Every contribution, regardless of size,
                            is deeply appreciated. Your support helps
                            make it possible to maintain ETS-Async,
                            improve its educational tools, and continue
                            building resources that can help students
                            learn, explore, and practice.

                        </p>

                    </div>


                    <?php if ($transaction): ?>


                        <!-- =========================================
                         TRANSACTION DETAILS
                    ========================================== -->

                        <div class="transaction-box">


                            <div class="transaction-header">

                                <i class="bi bi-receipt me-2"></i>

                                Support Details

                            </div>


                            <!-- =====================================
                             DONOR
                        ====================================== -->

                            <?php if ($displayDonor !== ""): ?>

                                <div class="transaction-row">

                                    <div class="transaction-label">

                                        Supporter

                                    </div>


                                    <div class="transaction-value">

                                        <?= $displayDonor ?>

                                    </div>

                                </div>

                            <?php endif; ?>


                            <!-- =====================================
                             AMOUNT
                        ====================================== -->

                            <div class="transaction-row">

                                <div class="transaction-label">

                                    Amount

                                </div>


                                <div class="transaction-value">

                                    ₱<?= number_format(
                                            (float)
                                            $transaction["amount"],
                                            2
                                        ) ?>

                                </div>

                            </div>


                            <!-- =====================================
                             REFERENCE
                        ====================================== -->

                            <div class="transaction-row">

                                <div class="transaction-label">

                                    Reference

                                </div>


                                <div class="transaction-value">

                                    <?= $displayReference ?>

                                </div>

                            </div>


                            <!-- =====================================
                             STATUS
                        ====================================== -->

                            <div class="transaction-row">

                                <div class="transaction-label">

                                    Payment Status

                                </div>


                                <div class="transaction-value">

                                    <span class="
                                    custom-status
                                    <?= htmlspecialchars(
                                        $status,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>
                                ">

                                        <i class="
                                        bi
                                        <?=
                                        $status === "paid"
                                            ? "bi-check-circle-fill"
                                            : (
                                                $status === "pending"
                                                ? "bi-clock-fill"
                                                : (
                                                    $status === "cancelled"
                                                    ? "bi-arrow-counterclockwise"
                                                    : "bi-exclamation-circle-fill"
                                                )
                                            )
                                        ?>
                                    "></i>

                                        <?= $displayStatus ?>

                                    </span>

                                </div>

                            </div>


                        </div>


                    <?php endif; ?>


                    <!-- =========================================
                     PAYMENT MESSAGE
                ========================================== -->

                    <?php if ($status === "paid"): ?>


                        <div class="thank-you-footer">

                            <strong>

                                Your kindness makes a difference.

                            </strong>

                            <br>

                            Thank you for helping us continue
                            improving the ETS-Async learning
                            experience.

                        </div>


                    <?php elseif ($status === "pending"): ?>


                        <div class="thank-you-footer">

                            <strong>

                                Your support is greatly appreciated.

                            </strong>

                            <br>

                            Please allow some time for the payment
                            confirmation to be reflected in the
                            system.

                        </div>


                    <?php endif; ?>


                    <!-- =========================================
                     ACTION BUTTONS
                ========================================== -->

                    <div class="success-actions">


                        <?php if (
                            $status === "cancelled" ||
                            $status === "failed"
                        ): ?>

                            <a
                                href="support_creator.php"
                                class="btn btn-primary">

                                <i class="
                                bi bi-heart me-2
                            "></i>

                                Support Again

                            </a>

                        <?php endif; ?>


                        <a
                            href="academic_posts.php"
                            class="btn btn-outline-primary">

                            <i class="
                            bi bi-arrow-left me-2
                        "></i>

                            Return to Academic Posts

                        </a>


                        <a
                            href="dashboard.php"
                            class="btn btn-outline-secondary">

                            <i class="
                            bi bi-house me-2
                        "></i>

                            Dashboard

                        </a>


                    </div>


                </div>


            </div>


        </div>


    </main>


    <?php require_once "globals/scripts.php"; ?>


</body>

</html>
