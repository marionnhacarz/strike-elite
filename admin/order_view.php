<?php

    $basePath = '../';

    require_once __DIR__ . '/../includes/functions.php';

    require_admin();

    $pageTitle = 'Order Details';

    /*
|--------------------------------------------------------------------------
| VALIDATE ORDER ID
|--------------------------------------------------------------------------
*/

    $orderId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
    );

    if (! $orderId) {
    $_SESSION['flash_error'] = 'Invalid order.';

    header('Location: orders.php');
    exit;
    }

    /*
|--------------------------------------------------------------------------
| GET ORDER
|--------------------------------------------------------------------------
*/

    $stmt = $pdo->prepare("
    SELECT
        o.*,
        u.username,
        u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");

    $stmt->execute([$orderId]);

    $order = $stmt->fetch();

    if (! $order) {
    $_SESSION['flash_error'] = 'Order not found.';

    header('Location: orders.php');
    exit;
    }

    /*
|--------------------------------------------------------------------------
| GETTING ORDER ITEMS
|--------------------------------------------------------------------------
*/

    $stmt = $pdo->prepare("
    SELECT
        oi.quantity,
        oi.price,
        p.name,
        p.image
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");

    $stmt->execute([$orderId]);

    $orderItems = $stmt->fetchAll();

    require_once __DIR__ . '/../includes/header.php';

?>

<div class="container">

    <div class="page-heading">
        <h1>
            Order #<?php echo (int) $order['id']; ?>
        </h1>

        <p style="color:var(--text-dim);">
            <?php echo h($order['created_at']); ?>
        </p>
    </div>

    <div style="margin-bottom:25px;">
        <a href="orders.php" class="btn">
            ← Back to Orders
        </a>
    </div>

    <!-- CUSTOMER INFO -->
    <div class="cart-summary" style="margin-bottom:25px;">
        <h3>Customer Information</h3>

        <div class="row">
            <span>Name</span>
            <span>
                <?php echo h($order['shipping_name']); ?>
            </span>
        </div>

        <div class="row">
            <span>Email</span>
            <span>
                <?php echo h($order['email']); ?>
            </span>
        </div>

        <div class="row">
            <span>Shipping Address</span>
            <span>
                <?php echo h($order['shipping_address']); ?>
            </span>
        </div>
    </div>

    <!-- ORDER INFO -->
    <div class="cart-summary" style="margin-bottom:25px;">
        <h3>Order Information</h3>

        <div class="row">
            <span>Payment Method</span>
            <span>
                <?php echo h(strtoupper($order['payment_method'])); ?>
            </span>
        </div>

        <div class="row">
            <span>Current Status</span>
            <span>
                <?php echo h(ucfirst($order['status'])); ?>
            </span>
        </div>

        <div class="row total">
            <span>Total</span>
            <span>
                <?php echo price($order['total_amount']); ?>
            </span>
        </div>
    </div>

    <!-- UPDATE ORDER STATUS -->
    <div class="cart-summary" style="margin-bottom:25px;">
        <h3>Update Order Status</h3>

        <form method="POST" action="order_update.php">
            <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">

            <div class="field">
                <label>
                    Order Status
                </label>

                <select name="status" required style="width:100%; padding:12px;">
                    <?php
                        $statuses = [
                            'processing',
                            'packed',
                            'shipped',
                            'completed',
                            'cancelled',
                        ];
                    ?>

                    <?php foreach ($statuses as $status): ?>
                        <option value="<?php echo h($status); ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>>
                            <?php echo h(ucfirst($status)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Update Status
            </button>
        </form>
    </div>

    <!-- ORDER ITEMS -->
    <div class="cart-summary">
        <h3>Products Ordered</h3>

        <?php foreach ($orderItems as $item): ?>
            <div class="row">
                <span>
                    <?php echo h($item['name']); ?>
                    × <?php echo (int) $item['quantity']; ?>
                </span>

                <span>
                    <?php echo price($item['price'] * $item['quantity']); ?>
                </span>
            </div>
        <?php endforeach; ?>

        <div class="row total">
            <span>Order Total</span>
            <span>
                <?php echo price($order['total_amount']); ?>
            </span>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>