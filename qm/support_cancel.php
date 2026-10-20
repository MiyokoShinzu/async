<?php
/* ETS-ASYNC LEARNING PORTAL — remove abandoned support checkout. */

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user']) ||
    ($_SESSION['user']['access'] ?? '') !== 'student'
) {
    header('Location: ../login.php');
    exit;
}

require_once __DIR__ . '/../src/connection.php';

$reference = trim($_GET['reference'] ?? '');

if ($reference !== '' && isset($mysqli) && $mysqli instanceof mysqli) {
    try {
        // Only remove pending records; never delete a confirmed payment.
        $stmt = $mysqli->prepare(
            "DELETE FROM support_transactions
             WHERE reference_code = ?
               AND status = 'pending'
               AND (paymongo_payment_id IS NULL OR paymongo_payment_id = '')"
        );
        $stmt->bind_param('s', $reference);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $e) {
        error_log('Support cancel cleanup failed: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once 'globals/head.php'; ?>
    <title>Payment Cancelled | ETS-Async</title>
</head>

<body>
    <?php require_once 'globals/sidebar.php'; ?>
    <?php require_once 'globals/topbar.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <div class="container py-5" style="max-width:700px;">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-5">
                        <div class="mb-4 text-warning" style="font-size:60px;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <h2 class="fw-bold">Payment Cancelled</h2>
                        <p class="text-muted">Your checkout was cancelled or not completed. No pending support transaction was retained.</p>
                        <a href="support_creator.php" class="btn btn-primary">
                            <i class="bi bi-heart me-2"></i>Try Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php require_once 'globals/scripts.php'; ?>
</body>

</html>