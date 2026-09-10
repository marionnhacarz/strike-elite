<?php

require_once __DIR__ . '/../includes/functions.php';

require_admin();


/*
|--------------------------------------------------------------------------
| ONLY ALLOW POST REQUESTS
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: orders.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDATE ORDER ID
|--------------------------------------------------------------------------
*/

$orderId = filter_input(
    INPUT_POST,
    'order_id',
    FILTER_VALIDATE_INT
);

$status = $_POST['status'] ?? '';


/*
|--------------------------------------------------------------------------
| ALLOWED ORDER STATUSES
|--------------------------------------------------------------------------
*/

$allowedStatuses = [
    'processing',
    'packed',
    'shipped',
    'completed',
    'cancelled'
];


if (
    !$orderId ||
    !in_array($status, $allowedStatuses, true)
) {

    $_SESSION['flash_error'] =
        'Invalid order update.';

    header('Location: orders.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| CHECK THAT ORDER EXISTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM orders
    WHERE id = ?
");

$stmt->execute([$orderId]);

if (!$stmt->fetch()) {

    $_SESSION['flash_error'] =
        'Order not found.';

    header('Location: orders.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| UPDATE ORDER
|--------------------------------------------------------------------------
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


$_SESSION['flash_success'] =
    'Order #' .
    $orderId .
    ' status updated to ' .
    ucfirst($status) .
    '.';


header(
    'Location: order_view.php?id=' .
    $orderId
);

exit;