<?php

$basePath = '../';

require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Manage Orders';

require_admin();

/*
Filter orders by status
*/

$status = $_GET['status'] ?? 'all';

$allowedStatuses = [
    'all',
    'pending',
    'processing',
    'shipped',
    'delivered',
    'cancelled'
];

if (!in_array($status, $allowedStatuses, true)) {
    $status = 'all';
}

/*
Get orders
*/

if ($status === 'all') {

$stmt = $pdo->query("SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id
ORDER BY o.created_at DESC ");

} else {

$stmt = $pdo->prepare(" SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status = ?
ORDER BY o.created_at DESC ");

    $stmt->execute([$status]);
}

$orders = $stmt->fetchAll();


require_once __DIR__ . '/../includes/header.php';
?>

    <div class="container">
        <div class="admin-nav">

    <a href="index.php">
        Dashboard
    </a>

    <a href="orders.php">
        Orders
    </a>

    <a href="../logout.php">
        Logout
    </a>

</div>

    <div class="page-heading">

    <h1>Client Orders</h1>

    <p style="color:var(--text-dim);"> Monitor and manage orders placed by customers.</p>
    
    </div>

    <div style="margin-bottom:25px;">

    <a href="index.php" class="btn btn-outline"> ← Dashboard </a>

    </div>

<!-- FILTERS -->

    <div
        style="
            display:flex;
            gap:10px;
            flex-wrap:wrap;
            margin-bottom:30px;
        "
    >

        <a
            href="orders.php"
            class="btn <?= $status === 'all'
                ? 'btn-primary'
                : 'btn-outline' ?>"
        >
            All Orders
        </a>


        <a
            href="orders.php?status=pending"
            class="btn <?= $status === 'pending'
                ? 'btn-primary'
                : 'btn-outline' ?>"
        >
            Pending
        </a>


        <a
            href="orders.php?status=processing"
            class="btn <?= $status === 'processing'
                ? 'btn-primary'
                : 'btn-outline' ?>"
        >
            Processing
        </a>


        <a
            href="orders.php?status=shipped"
            class="btn <?= $status === 'shipped'
                ? 'btn-primary'
                : 'btn-outline' ?>"
        >
            Shipped
        </a>


        <a
            href="orders.php?status=delivered"
            class="btn <?= $status === 'delivered'
                ? 'btn-primary'
                : 'btn-outline' ?>"
        >
            Delivered
        </a>


        <a
            href="orders.php?status=cancelled"
            class="btn <?= $status === 'cancelled'
                ? 'btn-primary'
                : 'btn-outline' ?>"
        >
            Cancelled
        </a>

    </div>


    <?php if (empty($orders)): ?>

        <div class="empty-state">

            <p>
                No orders found.
            </p>

        </div>

    <?php else: ?>

        <div style="overflow-x:auto;">

        <table class="cart-table">

        <thead>

        <tr>
        <th>Order</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Date</th>
        <th>Payment</th>
        <th>Status</th>
        <th>Total</th>
        <th>Action</th>
        </tr>
        </thead>
        <tbody>

<?php foreach ($orders as $order): ?>

<tr>

    <td>
     #<?= (int)$order['id'] ?>
    </td>
    <td>
    <?= h($order['username']) ?>
    </td>
    
    <td>
    <?= h($order['email']) ?>
    </td>


    <td>
    <?= date(
    'M d, Y h:i A', strtotime($order['created_at'])) ?>
    </td>

    <td>
    <?= h( strtoupper ( $order['payment_method'])) ?>
    </td>

    <td>
    <strong>
    <?= h( ucfirst ( $order['status'])) ?>
    </strong>
    </td>

    <td>
    <?= price( $order['total_amount']) ?> </td>

    <td>
    <a href="order_view.php?id=<?= (int)$order['id'] ?>"
    class="btn btn-primary">View</a> </td>
    </tr> <?php endforeach; ?> </tbody>
    </table> 
</div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>