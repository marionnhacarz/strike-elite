<?php

    $basePath = '../';

    require_once __DIR__ . '/../includes/functions.php';

    require_admin();

    $pageTitle = 'Admin Dashboard';

    /*
|--------------------------------------------------------------------------
| BUSINESS STATISTICS
|--------------------------------------------------------------------------
*/

    $totalOrders = (int) $pdo
    ->query("SELECT COUNT(*) FROM orders")
    ->fetchColumn();

    $processingOrders = (int) $pdo
    ->query("SELECT COUNT(*) FROM orders WHERE status = 'processing'")
    ->fetchColumn();

    $completedOrders = (int) $pdo
    ->query("SELECT COUNT(*) FROM orders WHERE status = 'completed'")
    ->fetchColumn();

    $totalRevenue = (float) $pdo
    ->query("
        SELECT COALESCE(SUM(total_amount), 0)
        FROM orders
        WHERE status != 'cancelled'
    ")
    ->fetchColumn();

    /*
|--------------------------------------------------------------------------
| RECENT ORDERS
|--------------------------------------------------------------------------
*/

    $stmt = $pdo->query("
    SELECT
        o.id,
        o.total_amount,
        o.payment_method,
        o.status,
        o.shipping_name,
        o.created_at,
        u.username,
        u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");

    $recentOrders = $stmt->fetchAll();

    require_once __DIR__ . '/../includes/header.php';

?>

<div class="container">

    <div class="page-heading">

        <h1>Admin Dashboard</h1>

        <p style="color:var(--text-dim);">
            Welcome, <?php echo h(current_user()['username']) ?>.
            Monitor Strike Elite's orders and sales here.
        </p>

    </div>


    <!-- BUSINESS STATISTICS -->

    <div style="
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
        gap:20px;
        margin:30px 0;
    ">

        <div class="cart-summary" style="margin:0;">
            <h3>Total Orders</h3>

            <p style="
                font-size:32px;
                font-weight:bold;
                color:var(--gold);
            ">
                <?php echo $totalOrders ?>
            </p>
        </div>


        <div class="cart-summary" style="margin:0;">
            <h3>Processing</h3>

            <p style="
                font-size:32px;
                font-weight:bold;
                color:var(--gold);
            ">
                <?php echo $processingOrders ?>
            </p>
        </div>


        <div class="cart-summary" style="margin:0;">
            <h3>Completed</h3>

            <p style="
                font-size:32px;
                font-weight:bold;
                color:var(--gold);
            ">
                <?php echo $completedOrders ?>
            </p>
        </div>


        <div class="cart-summary" style="margin:0;">
            <h3>Total Sales</h3>

            <p style="
                font-size:28px;
                font-weight:bold;
                color:var(--gold);
            ">
                <?php echo price($totalRevenue) ?>
            </p>
        </div>

    </div>


    <!-- ADMIN ACTIONS -->

    <div style="margin:30px 0;">

        <a
            href="orders.php"
            class="btn btn-primary"
        >
            Manage Orders
        </a>

        <a
            href="../index.php"
            class="btn"
            style="margin-left:10px;"
        >
            View Store
        </a>

        <a
            href="../logout.php"
            class="btn"
            style="margin-left:10px;"
        >
            Logout
        </a>

    </div>


    <!-- RECENT ORDERS -->

    <div style="margin-top:40px;">

        <h2>Recent Orders</h2>

        <?php if (empty($recentOrders)): ?>

            <p style="color:var(--text-dim);">
                No orders have been placed yet.
            </p>

        <?php else: ?>

            <div style="overflow-x:auto;">

                <table style="
                    width:100%;
                    border-collapse:collapse;
                    margin-top:20px;
                ">

                    <thead>

    <tr style="text-align:left;">

    <th style="padding:12px;">
    Order
    </th>

     <th style="padding:12px;">
       Customer
     </th>
     <th style="padding:12px;">
     Total
                            </th>

                            <th style="padding:12px;">
                                Status
                            </th>

                            <th style="padding:12px;">
                                Date
                            </th>

                            <th style="padding:12px;">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ($recentOrders as $order): ?>

                        <tr style="
                            border-top:1px solid rgba(255,255,255,.1);
                        ">

                            <td style="padding:12px;">
                                #<?php echo (int)$order['id'] ?>
                            </td>

                            <td style="padding:12px;">

                                <?php echo h($order['shipping_name']) ?>

                                <br>

                                <small style="color:var(--text-dim);">
                                    <?php echo h($order['email']) ?>
                                </small>

                            </td>

                            <td style="padding:12px;">
                                <?php echo price($order['total_amount']) ?>
                            </td>

                            <td style="padding:12px;">
                                <?php echo h(ucfirst($order['status'])) ?>
                            </td>

                            <td style="padding:12px;">
                                <?php echo h($order['created_at']) ?>
                            </td>

                            <td style="padding:12px;">

                                <a
                                    href="order_view.php?id=<?php echo (int)$order['id'] ?>"
                                    class="btn"
                                >
                                    View
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>