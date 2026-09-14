<?php

$basePath = '../';

require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Order Details';

require_admin();


$orderId = (int)($_GET['id'] ?? 0);

/*
Get order + customer
*/

$stmt = $pdo->prepare(" SELECT o.*, u.username, u.email
    FROM orders o
    JOIN users u
    ON o.user_id = u.id
    WHERE o.id = ?
    LIMIT 1
");

$stmt->execute([$orderId]);

$order = $stmt->fetch();


if (!$order) {

    flash('flash_error', 'Order not found.');
    header('Location: orders.php');
    exit;
}


/*
Get order items
*/

$itemStmt = $pdo->prepare(" SELECT oi.*, p.name, p.image
    FROM order_items oi
    JOIN products p
    ON oi.product_id = p.id
    WHERE oi.order_id = ?
    ORDER BY oi.id ASC
");

$itemStmt->execute([$orderId]);

$items = $itemStmt->fetchAll();


require_once __DIR__ . '/../includes/header.php';?>
<div class="container">
    <div class="page-heading">
        <h1>
        Order #<?= (int)$order['id'] ?>
        </h1>
        <p style="color:var(--text-dim);"> Placed on <?= date('F d, Y h:i A',
             strtotime($order['created_at']) ) ?>
        </p>

    </div>

    <div style="margin-bottom:25px;">

        <a
            href="orders.php"
            class="btn btn-outline"
        >
            ← Back to Orders
        </a>

    </div>


    <div style=" display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap:25px;
            margin-bottom:35px;
        " >

        <!-- CUSTOMER -->

     <div style=" background:var(--card);
     border:1px solid var(--border);padding:25px;" >
    <h3 style="color:var(--gold); margin-bottom:15px;">
        Customer Information
     </h3>
         <p>
         <strong>Username:</strong>
         <?= h($order['username']) ?>
         </p>

         <p>
            <strong>Email:</strong>
            <?= h($order['email']) ?>
            </p>

            <p>
                <strong>Shipping Name:</strong>
                <?= h($order['shipping_name']) ?>
            </p>

            <p>
             <strong>Shipping Address:</strong>
             <?= nl2br(
             h($order['shipping_address'])
                ) ?>
            </p>

        </div>


        <!-- ORDER INFO -->

        <div
            style="
                background:var(--card);
                border:1px solid var(--border);
                padding:25px;
            "
        >

            <h3
                style="
                    color:var(--gold);
                    margin-bottom:15px;
                "
            >
                Order Information
            </h3>


            <p>
                <strong>Payment:</strong>

                <?= h(
                    strtoupper(
                        $order['payment_method']
                    )
                ) ?>
            </p>


            <p>
                <strong>Current Status:</strong>

                <?= h(
                    ucfirst(
                        $order['status']
                    )
                ) ?>
            </p>


            <p>
                <strong>Total:</strong>

                <span style="color:var(--gold);">
                    <?= price(
                        $order['total_amount']
                    ) ?>
                </span>

            </p>


            <!-- UPDATE STATUS -->

            <form
                method="POST"
                action="order_update.php"
                style="margin-top:20px;"
            >

                <input
                    type="hidden"
                    name="order_id"
                    value="<?= (int)$order['id'] ?>"
                >


                <div class="field">

                    <label>
                        Update Order Status
                    </label>

                    <select
                        name="status"
                        required
                        style="
                            width:100%;
                            padding:12px;
                            background:#161616;
                            color:#fff;
                            border:1px solid #333;
                        "
                    >

                        <?php
                        $statuses = [
                            'pending',
                            'processing',
                            'shipped',
                            'delivered',
                            'cancelled'
                        ];
                        ?>


                        <?php foreach ($statuses as $status): ?>

                            <option
                                value="<?= h($status) ?>"
                                <?= $order['status'] === $status
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= h(
                                    ucfirst($status)
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Update Status
                </button>

            </form>

        </div>

    </div>


    <!-- ORDER ITEMS -->

    <h2 style="margin-bottom:20px;">
        Ordered Products
    </h2>


    <?php if (empty($items)): ?>

        <p style="color:var(--text-dim);">
            This order has no items.
        </p>

    <?php else: ?>

        <div style="overflow-x:auto;">

            <table class="cart-table">

                <thead>

                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>

                </thead>


                <tbody>

                <?php foreach ($items as $item): ?>

                    <tr>

                        <td>

                            <div class="cart-product">

                                <img
                                    src="../assets/images/products/<?= h($item['image']) ?>"
                                    alt="<?= h($item['name']) ?>"
                                >

                                <span>
                                    <?= h($item['name']) ?>
                                </span>

                            </div>

                        </td>


                        <td>

                            <?php if (!empty($item['size'])): ?>

                                <?= h($item['size']) ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <td>
                            <?= price($item['price']) ?>
                        </td>


                        <td>
                            <?= (int)$item['quantity'] ?>
                        </td>


                        <td>

                            <?= price(
                                $item['price']
                                *
                                $item['quantity']
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>


                <tfoot>

                    <tr>

                        <th colspan="4">
                            Order Total
                        </th>

                        <th style="color:var(--gold);">

                            <?= price(
                                $order['total_amount']
                            ) ?>

                        </th>

                    </tr>

                </tfoot>

            </table>

        </div>

    <?php endif; ?>

</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>