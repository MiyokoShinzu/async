<?php
/*
 * ETS-ASYNC LEARNING PORTAL
 * PayMongo webhook handler.
 *
 * Temporary pending rows are created to correlate checkout sessions.
 * Successful PayMongo events mark the row paid and save the actual payment ID.
 * Cancelled/expired checkout rows are removed.
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    require_once __DIR__ . '/../src/connection.php';
} catch (Throwable $e) {
    error_log('PayMongo webhook DB include failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Database connection unavailable.');
}

if (!isset($mysqli) || !($mysqli instanceof mysqli) || $mysqli->connect_errno) {
    error_log('PayMongo webhook: database connection unavailable.');
    http_response_code(500);
    exit('Database connection unavailable.');
}

$rawPayload = file_get_contents('php://input');
if ($rawPayload === false || trim($rawPayload) === '') {
    http_response_code(400);
    exit('Empty request.');
}

$payload = json_decode($rawPayload, true);
if (!is_array($payload)) {
    http_response_code(400);
    exit('Invalid JSON.');
}

$eventAttributes = $payload['data']['attributes'] ?? [];
$eventType = is_array($eventAttributes) ? ($eventAttributes['type'] ?? null) : null;
$eventObject = is_array($eventAttributes) ? ($eventAttributes['data'] ?? []) : [];
if (!is_array($eventObject)) {
    $eventObject = [];
}
$objectAttributes = $eventObject['attributes'] ?? [];
if (!is_array($objectAttributes)) {
    $objectAttributes = [];
}
$objectId = isset($eventObject['id']) ? (string)$eventObject['id'] : '';

$logFile = __DIR__ . '/paymongo_webhook.log';
$log = static function (string $message) use ($logFile): void {
    @file_put_contents(
        $logFile,
        date('Y-m-d H:i:s') . ' | ' . $message . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
};

$log('received type=' . (string)($eventType ?? 'unknown') . ' object_id=' . $objectId);

if (!$eventType || $objectId === '') {
    http_response_code(400);
    exit('Missing event type or object ID.');
}

/**
 * Extract a reference_code from common PayMongo object metadata locations.
 */
$referenceCode = null;
$metadataCandidates = [
    $objectAttributes['metadata'] ?? null,
    $eventAttributes['metadata'] ?? null,
    $objectAttributes['payments'][0]['attributes']['metadata'] ?? null,
    $objectAttributes['payments'][0]['metadata'] ?? null,
];
foreach ($metadataCandidates as $candidate) {
    if (is_array($candidate) && !empty($candidate['reference_code'])) {
        $referenceCode = (string)$candidate['reference_code'];
        break;
    }
}

/**
 * Find a payment ID from the event's payment object or checkout payments list.
 * A checkout-session ID is never used as the payment ID.
 */
$paymentId = null;
$checkoutId = null;
$isPaymentPaid = ($eventType === 'payment.paid');
$isCheckoutPaid = ($eventType === 'checkout_session.payment.paid');

if ($isPaymentPaid) {
    $paymentId = $objectId;
    $checkoutId = !empty($objectAttributes['checkout_session_id'])
        ? (string)$objectAttributes['checkout_session_id']
        : null;
} elseif (str_starts_with((string)$eventType, 'checkout_session.')) {
    $checkoutId = $objectId;
    $payments = $objectAttributes['payments'] ?? [];
    if (is_array($payments)) {
        foreach ($payments as $payment) {
            if (is_array($payment) && !empty($payment['id'])) {
                $paymentId = (string)$payment['id'];
                break;
            }
            if (is_array($payment) && !empty($payment['data']['id'])) {
                $paymentId = (string)$payment['data']['id'];
                break;
            }
        }
    }
}

/* Delete abandoned checkout records when PayMongo confirms expiry. */
$isExpired = in_array($eventType, [
    'checkout_session.expired',
    'checkout_session.expire',
], true);

try {
    if ($isExpired) {
        if ($referenceCode) {
            $stmt = $mysqli->prepare(
                "DELETE FROM support_transactions
                 WHERE reference_code = ? AND status <> 'paid'"
            );
            $stmt->bind_param('s', $referenceCode);
        } else {
            $stmt = $mysqli->prepare(
                "DELETE FROM support_transactions
                 WHERE paymongo_checkout_id = ? AND status <> 'paid'"
            );
            $stmt->bind_param('s', $objectId);
        }
        $stmt->execute();
        $deleted = $stmt->affected_rows;
        $stmt->close();
        $log('expired checkout cleanup deleted=' . $deleted . ' checkout_id=' . $objectId);
        http_response_code(200);
        exit('Expired checkout processed.');
    }

    /* Ignore non-paid events; failed payment attempts may still be retried. */
    if (!$isPaymentPaid && !$isCheckoutPaid) {
        http_response_code(200);
        exit('Event ignored.');
    }

    if (!$paymentId) {
        $log('paid event missing actual payment ID; type=' . $eventType . ' checkout_id=' . (string)$checkoutId);
    }

    // Match by reference first, then by the saved checkout session ID.
    if ($referenceCode) {
        $stmt = $mysqli->prepare(
            "UPDATE support_transactions
             SET status = 'paid',
                 paymongo_payment_id = COALESCE(NULLIF(?, ''), paymongo_payment_id),
                 paymongo_checkout_id = COALESCE(NULLIF(?, ''), paymongo_checkout_id),
                 paid_at = COALESCE(paid_at, NOW())
             WHERE reference_code = ?
             LIMIT 1"
        );
        $paymentIdParam = (string)($paymentId ?? '');
        $checkoutIdParam = (string)($checkoutId ?? '');
        $stmt->bind_param('sss', $paymentIdParam, $checkoutIdParam, $referenceCode);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected === 0) {
            $check = $mysqli->prepare('SELECT id, status, paymongo_payment_id FROM support_transactions WHERE reference_code = ? LIMIT 1');
            $check->bind_param('s', $referenceCode);
            $check->execute();
            $row = $check->get_result()->fetch_assoc();
            $check->close();
            if (!$row) {
                $log('NO MATCH for paid event reference_code=' . $referenceCode . '; checkout_id=' . (string)$checkoutId);
            } elseif (empty($row['paymongo_payment_id']) && !$paymentId) {
                $log('matched transaction but payment ID was absent; reference_code=' . $referenceCode);
            }
        }
    } elseif ($checkoutId) {
        $stmt = $mysqli->prepare(
            "UPDATE support_transactions
             SET status = 'paid',
                 paymongo_payment_id = COALESCE(NULLIF(?, ''), paymongo_payment_id),
                 paid_at = COALESCE(paid_at, NOW())
             WHERE paymongo_checkout_id = ?
             LIMIT 1"
        );
        $paymentIdParam = (string)($paymentId ?? '');
        $stmt->bind_param('ss', $paymentIdParam, $checkoutId);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        if ($affected === 0) {
            $log('NO MATCH for paid event checkout_id=' . $checkoutId);
        }
    } else {
        $log('paid event has no usable reference or checkout ID; object_id=' . $objectId);
    }
} catch (Throwable $e) {
    $log('database update failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Could not update transaction.');
}

http_response_code(200);
echo 'OK';
