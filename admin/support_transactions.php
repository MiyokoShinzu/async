<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   ADMIN - SUPPORT TRANSACTIONS
========================================================== */

session_start();


/* =========================================================
   AUTHENTICATION
========================================================== */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "admin"
) {

    header("Location: ../login.php");

    exit;
}


/* =========================================================
   DATABASE
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   SUMMARY
========================================================== */

$totalSupport = 0;

$successfulSupport = 0;

$pendingSupport = 0;

$totalTransactions = 0;


/* =========================================================
   TOTAL SUCCESSFUL SUPPORT
========================================================== */

$stmt = $mysqli->prepare(
    "SELECT
        COALESCE(SUM(amount), 0) AS total
     FROM support_transactions
     WHERE status = 'paid'"
);


$stmt->execute();

$result =
    $stmt->get_result();

$row =
    $result->fetch_assoc();

$totalSupport =
    (float)$row["total"];

$stmt->close();


/* =========================================================
   SUCCESSFUL TRANSACTIONS
========================================================== */

$stmt = $mysqli->prepare(
    "SELECT COUNT(*) AS total
     FROM support_transactions
     WHERE status = 'paid'"
);


$stmt->execute();

$result =
    $stmt->get_result();

$row =
    $result->fetch_assoc();

$successfulSupport =
    (int)$row["total"];

$stmt->close();


/* =========================================================
   PENDING TRANSACTIONS
========================================================== */

$stmt = $mysqli->prepare(
    "SELECT COUNT(*) AS total
     FROM support_transactions
     WHERE status = 'pending'"
);


$stmt->execute();

$result =
    $stmt->get_result();

$row =
    $result->fetch_assoc();

$pendingSupport =
    (int)$row["total"];

$stmt->close();


/* =========================================================
   ALL TRANSACTIONS
========================================================== */

$stmt = $mysqli->prepare(
    "SELECT COUNT(*) AS total
     FROM support_transactions"
);


$stmt->execute();

$result =
    $stmt->get_result();

$row =
    $result->fetch_assoc();

$totalTransactions =
    (int)$row["total"];

$stmt->close();


/* =========================================================
   TRANSACTION LIST
========================================================== */

$transactions = [];


$result =
    $mysqli->query(
        "SELECT
            id,
            student_id,
            donor_name,
            donor_email,
            amount,
            currency,
            reference_code,
            status,
            message,
            created_at,
            paid_at
         FROM support_transactions
         ORDER BY created_at DESC
         LIMIT 100"
    );


if ($result) {

    while (
        $row = $result->fetch_assoc()
    ) {

        $transactions[] = $row;
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php require_once "./globals/head.php"; ?>

    <title>Support Transactions | ETS-Async</title>

</head>


<body>

    <?php require_once "./globals/sidebar.php"; ?>

    <?php require_once "./globals/topbar.php"; ?>


    <main class="main-content">

        <div class="content-wrapper">


            <!-- =================================================
             PAGE HEADER
        ================================================== -->

            <div class="mb-4">

                <h1 class="fw-bold">

                    <i class="bi bi-heart me-2"></i>

                    Support Transactions

                </h1>

                <p class="text-muted">

                    View creator support payments.

                </p>

            </div>


            <!-- =================================================
             SUMMARY
        ================================================== -->

            <div class="row g-3 mb-4">


                <div class="col-md-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <small class="text-muted">

                                Total Support

                            </small>

                            <h3 class="fw-bold">

                                ₱<?= number_format(
                                        $totalSupport,
                                        2
                                    ) ?>

                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <small class="text-muted">

                                Successful

                            </small>

                            <h3 class="fw-bold">

                                <?= $successfulSupport ?>

                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <small class="text-muted">

                                Pending

                            </small>

                            <h3 class="fw-bold">

                                <?= $pendingSupport ?>

                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <small class="text-muted">

                                Transactions

                            </small>

                            <h3 class="fw-bold">

                                <?= $totalTransactions ?>

                            </h3>

                        </div>

                    </div>

                </div>


            </div>


            <!-- =================================================
             TRANSACTION TABLE
        ================================================== -->

            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="table-responsive">

                        <table
                            class="table table-hover align-middle">

                            <thead>

                                <tr>

                                    <th>Reference</th>

                                    <th>Donor</th>

                                    <th>Amount</th>

                                    <th>Status</th>

                                    <th>Date</th>

                                </tr>

                            </thead>


                            <tbody>

                                <?php if (
                                    empty($transactions)
                                ): ?>

                                    <tr>

                                        <td
                                            colspan="5"
                                            class="text-center text-muted py-4">

                                            No support transactions yet.

                                        </td>

                                    </tr>

                                <?php else: ?>


                                    <?php foreach (
                                        $transactions
                                        as $transaction
                                    ): ?>

                                        <tr>


                                            <td>

                                                <strong>

                                                    <?= htmlspecialchars(
                                                        $transaction["reference_code"]
                                                    ) ?>

                                                </strong>

                                            </td>


                                            <td>

                                                <?= htmlspecialchars(
                                                    $transaction["donor_name"]
                                                ) ?>

                                                <br>

                                                <small
                                                    class="text-muted">

                                                    <?= htmlspecialchars(
                                                        $transaction["donor_email"]
                                                    ) ?>

                                                </small>

                                            </td>


                                            <td>

                                                ₱<?= number_format(
                                                        (float)
                                                        $transaction["amount"],
                                                        2
                                                    ) ?>

                                            </td>


                                            <td>

                                                <?php

                                                $status =
                                                    $transaction["status"];

                                                $badge =
                                                    "secondary";

                                                if (
                                                    $status === "paid"
                                                ) {

                                                    $badge =
                                                        "success";
                                                } elseif (
                                                    $status === "pending"
                                                ) {

                                                    $badge =
                                                        "warning";
                                                } elseif (
                                                    $status === "failed"
                                                ) {

                                                    $badge =
                                                        "danger";
                                                } elseif (
                                                    $status === "cancelled"
                                                ) {

                                                    $badge =
                                                        "secondary";
                                                }

                                                ?>


                                                <span
                                                    class="badge bg-<?=
                                                                    $badge
                                                                    ?>">

                                                    <?= htmlspecialchars(
                                                        $status
                                                    ) ?>

                                                </span>

                                            </td>


                                            <td>

                                                <?= htmlspecialchars(
                                                    $transaction["created_at"]
                                                ) ?>

                                            </td>


                                        </tr>

                                    <?php endforeach; ?>


                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </main>


    <?php require_once "./globals/scripts.php"; ?>

</body>

</html>