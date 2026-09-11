<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   SUPPORT PAYMENT CANCELLED
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


/* =========================================================
   UPDATE TRANSACTION
========================================================== */

if ($reference !== "") {

    $stmt = $mysqli->prepare(
        "UPDATE support_transactions
         SET status = 'cancelled'
         WHERE reference_code = ?
         AND status = 'pending'"
    );


    if ($stmt) {

        $stmt->bind_param(
            "s",
            $reference
        );

        $stmt->execute();

        $stmt->close();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php require_once "globals/head.php"; ?>

    <title>Payment Cancelled | ETS-Async</title>

</head>


<body>

    <?php require_once "globals/sidebar.php"; ?>

    <?php require_once "globals/topbar.php"; ?>


    <main class="main-content">

        <div class="content-wrapper">

            <div
                class="container py-5"
                style="max-width:700px;">

                <div class="card border-0 shadow-sm">

                    <div class="card-body text-center p-5">


                        <div
                            class="mb-4 text-warning"
                            style="font-size:60px;">

                            <i class="bi bi-x-circle"></i>

                        </div>


                        <h2 class="fw-bold">

                            Payment Cancelled

                        </h2>


                        <p class="text-muted">

                            Your support payment was cancelled
                            or you returned from the payment
                            page without completing the transaction.

                        </p>


                        <a
                            href="support_creator.php"
                            class="btn btn-primary">

                            <i class="bi bi-heart me-2"></i>

                            Try Again

                        </a>


                    </div>

                </div>

            </div>

        </div>

    </main>


    <?php require_once "globals/scripts.php"; ?>

</body>

</html>