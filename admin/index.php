<?php

$basePath = '../';

require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Admin Dashboard';

require_admin();

/*
Dashboard statistics
*/

$totalOrders = (int)$pdo
    ->query("SELECT COUNT(*) FROM orders")
    ->fetchColumn();

$processingOrders = (int)$pdo
    ->query("
        SELECT COUNT(*)
        FROM orders
        WHERE status IN ('pending', 'processing')
    ")
    ->fetchColumn();

$totalClients = (int)$pdo
    ->query("
        SELECT COUNT(*)
        FROM users
        WHERE role = 'client'
    ")
    ->fetchColumn();

$totalSales = (float)$pdo
    ->query("
        SELECT COALESCE(SUM(total_amount), 0)
        FROM orders
        WHERE status != 'cancelled'
    ")
    ->fetchColumn();


/*
Recent orders
*/

$stmt = $pdo->query(" SELECT o.id, o.total_amount, o.payment_method, o.status, o.created_at, u.username
    FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");

$recentOrders = $stmt->fetchAll();


require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">

    <div class="page-heading">

        <h1>Admin Dashboard</h1>

        <p style="color:var(--text-dim);">
            Welcome,
            <?= h(current_user()['username']) ?>.
        </p>

    </div>


    <!-- DASHBOARD STATS -->

    <div
        style="
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
            gap:20px;
            margin-top:30px;
        "
    >

        <div
            style="
                background:var(--card);
                padding:24px;
                border:1px solid var(--border);
            "
        >
            <p style="color:var(--text-dim);">
                Total Orders
            </p>

            <h2 style="color:var(--gold);">
                <?= $totalOrders ?>
            </h2>
        </div>


        <div
            style="
                background:var(--card);
                padding:24px;
                border:1px solid var(--border);
            "
        >
            <p style="color:var(--text-dim);">
                Pending / Processing
            </p>

            <h2 style="color:var(--gold);">
                <?= $processingOrders ?>
            </h2>
        </div>


        <div
            style="
                background:var(--card);
                padding:24px;
                border:1px solid var(--border);
            "
        >
            <p style="color:var(--text-dim);">
                Clients
            </p>

            <h2 style="color:var(--gold);">
                <?= $totalClients ?>
            </h2>
        </div>


        <div
            style="
                background:var(--card);
                padding:24px;
                border:1px solid var(--border);
            "
        >
            <p style="color:var(--text-dim);">
                Order Value
            </p>

            <h2 style="color:var(--gold);">
                <?= price($totalSales) ?>
            </h2>
        </div>

    </div>


    <!-- BUTTONS -->

    <div style="margin-top:30px;">

        <a
            href="orders.php"
            class="btn btn-primary"
        >
            Manage Orders
        </a>

        <a
            href="../index.php"
            class="btn btn-outline"
            style="margin-left:10px;"
        >
            Back to Store
        </a>

        <a
            href="../logout.php"
            class="btn btn-outline"
            style="margin-left:10px;"
        >
            Logout
        </a>

    </div>


    <!-- RECENT ORDERS -->

    <div style="margin-top:45px;">

        <h2>
            Recent Orders
        </h2>

        <?php if (empty($recentOrders)): ?>

        <p style="color:var(--text-dim); margin-top:20px;"
            >
            No client orders have been placed yet.</p>

        <?php else: ?>

            <div style="overflow-x:auto;">
        <table class="cart-table"style="margin-top:20px;"
                >
        <thead>

        <tr>
        <th>Order</th>
        <th>Customer</th>
        <th>Date</th>
        <th>Payment</th>
        <th>Status</th>
        <th>Total</th>
        <th></th>
        </tr>
        </thead>
        
        <tbody>
        <?php foreach ($recentOrders as $order): ?>
        <tr>

        <td>
        #<?= (int)$order['id'] ?>
        </td>
        <td>
        <?= h($order['username']) ?>
        </td>

        <td>
        <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?>
        </td>

        <td>
        <?= h (strtoupper($order['payment_method'])) ?>
        </td>
        <td>
        <?= h (ucfirst($order['status'])) ?>
        </td>
        <td>
        <?= price($order['total_amount']) ?>
        </td>

        <td>

        <a href="order_view.php?id=<?= (int)$order['id'] ?>"class="btn btn-outline"
        >View
        </a>

        </td>
        </tr>

        <?php endforeach; ?>
        </tbody>

        </table>
        </div>
        <?php endif; ?>
        </div>
        </div>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>