<?php

require_once __DIR__ . '/../includes/functions.php';

require_admin();

/*
Only accept POST requests
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: orders.php');
    exit;
}

$orderId =
    (int)($_POST['order_id'] ?? 0);

$status =
    trim($_POST['status'] ?? '');


$allowedStatuses = [
    'pending',
    'processing',
    'shipped',
    'delivered',
    'cancelled'
];


/*
Validate order
*/

if ($orderId <= 0) {

    flash(
        'flash_error',
        'Invalid order.'
    );

    header('Location: orders.php');
    exit;
}


/*
Validate status
*/

if (!in_array($status, $allowedStatuses, true)) {

    flash('flash_error', 'Invalid order status.');
    header('Location: order_view.php?id=' . $orderId );
 exit;
}

/*
Make sure order exists
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM orders
    WHERE id = ?
");

$stmt->execute([$orderId]);


if (!$stmt->fetch()) {

    flash( 'flash_error', 'Order not found.' );
   header('Location: orders.php');
    exit;
}


/*
Update status
*/

$stmt = $pdo->prepare("
    UPDATE orders
    SET status = ?
    WHERE id = ?
");

$stmt->execute([
    $status,
    $orderId
]);


flash( 'flash_success', 'Order #' . $orderId .
 ' status updated to ' . ucfirst($status) . '.');

header( 'Location: order_view.php?id=' . $orderId);
exit;