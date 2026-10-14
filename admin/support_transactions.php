<?php
/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   ADMIN - SUPPORT TRANSACTIONS
========================================================== */

session_start();

if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['user']) ||
    ($_SESSION['user']['access'] ?? '') !== 'admin'
) {
    header('Location: ../login.php');
    exit;
}

require_once '../src/connection.php';

if (empty($_SESSION['support_transactions_csrf'])) {
    $_SESSION['support_transactions_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['support_transactions_csrf'];
$notice = '';
$noticeType = 'success';
$allowedStatuses = ['pending', 'paid', 'failed', 'cancelled'];

/* Handle admin actions */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedToken = (string)($_POST['csrf_token'] ?? '');
    if (!hash_equals($csrfToken, $postedToken)) {
        http_response_code(403);
        exit('Invalid request token. Please refresh the page and try again.');
    }

    $action = (string)($_POST['action'] ?? '');
    $transactionId = filter_var($_POST['transaction_id'] ?? null, FILTER_VALIDATE_INT);

    if (!$transactionId || $transactionId < 1) {
        $notice = 'Invalid transaction ID.';
        $noticeType = 'danger';
    } elseif ($action === 'update_status') {
        $newStatus = (string)($_POST['status'] ?? '');
        if (!in_array($newStatus, $allowedStatuses, true)) {
            $notice = 'Invalid transaction status.';
            $noticeType = 'danger';
        } else {
            // Only change status here. This does not create or verify a PayMongo payment ID.
            if ($newStatus === 'paid') {
                $stmt = $mysqli->prepare("UPDATE support_transactions SET status = ?, paid_at = COALESCE(paid_at, NOW()) WHERE id = ?");
            } else {
                $stmt = $mysqli->prepare("UPDATE support_transactions SET status = ?, paid_at = NULL WHERE id = ?");
            }
            if ($stmt) {
                $stmt->bind_param('si', $newStatus, $transactionId);
                if ($stmt->execute() && $stmt->affected_rows >= 0) {
                    $notice = 'Transaction status updated.';
                } else {
                    $notice = 'Could not update transaction status.';
                    $noticeType = 'danger';
                }
                $stmt->close();
            } else {
                $notice = 'Database error while preparing the status update.';
                $noticeType = 'danger';
            }
        }
    } elseif ($action === 'delete') {
        $stmt = $mysqli->prepare('DELETE FROM support_transactions WHERE id = ?');
        if ($stmt) {
            $stmt->bind_param('i', $transactionId);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $notice = 'Transaction deleted.';
                } else {
                    $notice = 'Transaction not found; nothing was deleted.';
                    $noticeType = 'warning';
                }
            } else {
                $notice = 'Could not delete transaction.';
                $noticeType = 'danger';
            }
            $stmt->close();
        } else {
            $notice = 'Database error while preparing the delete.';
            $noticeType = 'danger';
        }
    } else {
        $notice = 'Unknown action.';
        $noticeType = 'danger';
    }
}

/* Summary cards */
$totalSupport = 0.0;
$successfulSupport = 0;
$pendingSupport = 0;
$totalTransactions = 0;

$result = $mysqli->query("SELECT COALESCE(SUM(amount), 0) AS total FROM support_transactions WHERE status = 'paid'");
if ($result && ($row = $result->fetch_assoc())) {
    $totalSupport = (float)$row['total'];
    $result->free();
}

$result = $mysqli->query("SELECT
    COUNT(*) AS total,
    COALESCE(SUM(status = 'paid'), 0) AS paid_count,
    COALESCE(SUM(status = 'pending'), 0) AS pending_count
    FROM support_transactions");
if ($result && ($row = $result->fetch_assoc())) {
    $totalTransactions = (int)$row['total'];
    $successfulSupport = (int)$row['paid_count'];
    $pendingSupport = (int)$row['pending_count'];
    $result->free();
}

$transactions = [];
$result = $mysqli->query("SELECT id, student_id, donor_name, donor_email, amount, currency, reference_code, status, message, created_at, paid_at
    FROM support_transactions ORDER BY created_at DESC LIMIT 100");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
    $result->free();
}

function h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function statusBadgeClass(string $status): string
{
    switch ($status) {
        case 'paid':
            return 'success';
        case 'pending':
            return 'warning text-dark';
        case 'failed':
            return 'danger';
        case 'cancelled':
            return 'secondary';
        default:
            return 'secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once './globals/head.php'; ?>
    <title>Support Transactions | ETS-Async</title>
</head>

<body>
    <?php require_once './globals/sidebar.php'; ?>
    <?php require_once './globals/topbar.php'; ?>

    <main class="main-content">
        <div class="content-wrapper">
            <div class="mb-4">
                <h1 class="fw-bold"><i class="bi bi-heart me-2"></i>Support Transactions</h1>
                <p class="text-muted">View, update, and delete creator support payment records.</p>
            </div>

            <?php if ($notice !== ''): ?>
                <div class="alert alert-<?= h($noticeType) ?> alert-dismissible fade show" role="alert">
                    <?= h($notice) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <small class="text-muted">Total Support</small>
                            <h3 class="fw-bold">₱<?= number_format($totalSupport, 2) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <small class="text-muted">Successful</small>
                            <h3 class="fw-bold"><?= $successfulSupport ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <small class="text-muted">Pending</small>
                            <h3 class="fw-bold"><?= $pendingSupport ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <small class="text-muted">Transactions</small>
                            <h3 class="fw-bold"><?= $totalTransactions ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h5 class="mb-0">Transaction Records</h5>
                        <small class="text-muted">Showing up to the latest 100 transactions</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID / Reference</th>
                                    <th>Donor</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th style="min-width: 260px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($transactions)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No support transactions yet.</td>
                                    </tr>
                                    <?php else: foreach ($transactions as $transaction): ?>
                                        <tr>
                                            <td>
                                                <span class="text-muted small">#<?= (int)$transaction['id'] ?></span><br>
                                                <strong><?= h($transaction['reference_code']) ?></strong>
                                            </td>
                                            <td><?= h($transaction['donor_name']) ?><br><small class="text-muted"><?= h($transaction['donor_email']) ?></small></td>
                                            <td>₱<?= number_format((float)$transaction['amount'], 2) ?><br><small class="text-muted"><?= h(strtoupper($transaction['currency'] ?: 'PHP')) ?></small></td>
                                            <td><span class="badge bg-<?= h(statusBadgeClass((string)$transaction['status'])) ?>"><?= h($transaction['status']) ?></span></td>
                                            <td><?= h($transaction['created_at']) ?>
                                                <?php if (!empty($transaction['paid_at'])): ?><br><small class="text-muted">Paid: <?= h($transaction['paid_at']) ?></small><?php endif; ?>
                                            </td>
                                            <td>
                                                <form method="post" class="d-flex flex-wrap gap-2 mb-2" onsubmit="return confirm('Update the status for this transaction?');">
                                                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
                                                    <input type="hidden" name="transaction_id" value="<?= (int)$transaction['id'] ?>">
                                                    <input type="hidden" name="action" value="update_status">
                                                    <select name="status" class="form-select form-select-sm" aria-label="New transaction status" style="max-width: 145px" required>
                                                        <?php foreach ($allowedStatuses as $option): ?>
                                                            <option value="<?= h($option) ?>" <?= $transaction['status'] === $option ? 'selected' : '' ?>><?= h(ucfirst($option)) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-save me-1"></i>Save</button>
                                                </form>
                                                <form method="post" onsubmit="return confirm('Permanently delete this transaction from the database? This cannot be undone.');">
                                                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken) ?>">
                                                    <input type="hidden" name="transaction_id" value="<?= (int)$transaction['id'] ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                <?php endforeach;
                                endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-warning mt-3 mb-0 small">
                        <strong>Important:</strong> Setting a transaction to <em>Paid</em> changes the database status and affects the summary totals, but it does not verify a PayMongo payment or generate a payment ID. Confirm the payment with PayMongo before manually marking it paid.
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once './globals/scripts.php'; ?>
</body>

</html>