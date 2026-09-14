<?php

$basePath = '../';

require_once __DIR__ . '/../includes/functions.php';

require_admin();

$pageTitle = 'Admin Dashboard';


/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

$totalOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
")->fetchColumn();


$processingOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE status IN ('pending', 'processing')
")->fetchColumn();


$totalClients = $pdo->query("
    SELECT COUNT(*)
    FROM users
    WHERE role = 'client'
")->fetchColumn();


$totalSales = $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM orders
    WHERE status != 'cancelled'
")->fetchColumn();


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
        <h1>Admin Dashboard</h1>

        <p>
            Welcome,
            <?= h(current_user()['username']) ?>
        </p>
    </div>


    <div class="admin-dashboard-grid">

        <div class="admin-card">
            <h3>Total Orders</h3>
            <p><?= (int)$totalOrders ?></p>
        </div>

        <div class="admin-card">
            <h3>Processing Orders</h3>
            <p><?= (int)$processingOrders ?></p>
        </div>

        <div class="admin-card">
            <h3>Total Clients</h3>
            <p><?= (int)$totalClients ?></p>
        </div>

        <div class="admin-card">
            <h3>Total Sales</h3>
            <p><?= price($totalSales) ?></p>
        </div>

    </div>


    <div class="admin-actions">

        <a
            href="orders.php"
            class="btn btn-primary"
        >
            Manage Client Orders
        </a>

    </div>


    </div>

</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>