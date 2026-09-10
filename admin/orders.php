<?php

    $basePath = '../';

    require_once __DIR__ . '/../includes/functions.php';

    require_admin();

    $pageTitle = 'Manage Orders';

    /*
|--------------------------------------------------------------------------
| GET ALL ORDERS
|--------------------------------------------------------------------------
*/

    $stmt = $pdo->query("
    SELECT
        o.id,
        o.total_amount,
        o.payment_method,
        o.status,
        o.shipping_name,
        o.shipping_address,
        o.created_at,
        u.username,
        u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
");

    $orders = $stmt->fetchAll();

    require_once __DIR__ . '/../includes/header.php';

?>

<div class="container">

    <div class="page-heading">

        <h1>Manage Orders</h1>

        <p style="color:var(--text-dim);">
            View and manage customer orders.
        </p>

    </div>


    <div style="margin-bottom:25px;">

        <a
            href="index.php"
            class="btn"
        >
            ← Admin Dashboard
        </a>

    </div>


    <?php if (empty($orders)): ?>

        <div class="cart-summary">

            <p>
                No customer orders have been placed yet.
            </p>

        </div>

    <?php else: ?>

        <div style="overflow-x:auto;">

            <table style="
                width:100%;
                border-collapse:collapse;
            ">

                <thead>

                    <tr style="text-align:left;">

                        <th style="padding:12px;">
                            Order #
                        </th>

                        <th style="padding:12px;">
                            Customer
                        </th>

                        <th style="padding:12px;">
                            Payment
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

                <?php foreach ($orders as $order): ?>

                    <tr style="
                        border-top:1px solid rgba(255,255,255,.1);
                    ">

                        <td style="padding:12px;">
                            #<?php echo (int)$order['id'] ?>
                        </td>


                        <td style="padding:12px;">

                            <strong>
                                <?php echo h($order['shipping_name']) ?>
                            </strong>

                            <br>

                            <small style="color:var(--text-dim);">
                                <?php echo h($order['email']) ?>
                            </small>

                        </td>


                        <td style="padding:12px;">

                            <?php echo h(strtoupper($order['payment_method'])) ?>

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
                                class="btn btn-primary"
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>