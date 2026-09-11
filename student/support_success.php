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

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php require_once "globals/head.php"; ?>

    <title>Support Successful | ETS-Async</title>

    <style>
        .success-wrapper {

            max-width: 700px;

            margin: 40px auto;

        }


        .success-card {

            background: var(--bs-body-bg);

            border: 1px solid var(--bs-border-color);

            border-radius: 18px;

            padding: 40px;

            text-align: center;

        }


        .success-icon {

            width: 80px;
            height: 80px;

            border-radius: 50%;

            background: rgba(22, 163, 74, .1);

            color: #16a34a;

            display: flex;

            align-items: center;
            justify-content: center;

            font-size: 40px;

            margin: 0 auto 25px;

        }
    </style>

</head>


<body>

    <?php require_once "globals/sidebar.php"; ?>

    <?php require_once "globals/topbar.php"; ?>


    <main class="main-content">

        <div class="content-wrapper">


            <div class="success-wrapper">

                <div class="success-card">


                    <div class="success-icon">

                        <i class="bi bi-check-lg"></i>

                    </div>


                    <h2 class="fw-bold">

                        Thank You!

                    </h2>


                    <p class="text-muted">

                        Thank you for supporting
                        ETS-Async.

                    </p>


                    <?php if ($transaction): ?>


                        <div class="mt-4">

                            <div class="mb-2">

                                <strong>
                                    Reference:
                                </strong>

                                <?= htmlspecialchars(
                                    $transaction["reference_code"]
                                ) ?>

                            </div>


                            <div class="mb-2">

                                <strong>
                                    Amount:
                                </strong>

                                ₱<?= number_format(
                                        (float)$transaction["amount"],
                                        2
                                    ) ?>

                            </div>


                            <div>

                                <strong>
                                    Status:
                                </strong>

                                <span class="badge bg-warning">

                                    <?= htmlspecialchars(
                                        $transaction["status"]
                                    ) ?>

                                </span>

                            </div>

                        </div>


                        <div class="alert alert-info mt-4">

                            <i class="bi bi-info-circle me-2"></i>

                            Your payment will be marked as
                            successful once PayMongo confirms
                            the transaction through its payment
                            notification.

                        </div>


                    <?php endif; ?>


                    <a
                        href="academic_posts.php"
                        class="btn btn-primary mt-3">

                        <i class="bi bi-arrow-left me-2"></i>

                        Return to Academic Posts

                    </a>


                </div>

            </div>

        </div>

    </main>


    <?php require_once "globals/scripts.php"; ?>

</body>

</html>