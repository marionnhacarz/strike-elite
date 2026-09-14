<?php
$basePath = '';

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Order Confirmed';

require_login('login.php');


$orderId =
    (int)($_GET['id'] ?? 0);


$stmt = $pdo->prepare(
    "
    SELECT *
    FROM orders
    WHERE id = ?
    AND user_id = ?
    "
);

$stmt->execute([
    $orderId,
    $_SESSION['user_id']
]);

$order = $stmt->fetch();


if (!$order) {
    header('Location: index.php');
    exit;
}


$itemsStmt = $pdo->prepare(
    "
    SELECT
        oi.*,
        p.name,
        p.image
    FROM order_items oi
    JOIN products p
        ON oi.product_id = p.id
    WHERE oi.order_id = ?
    "
);


$itemsStmt->execute([
    $orderId
]);


$items =
    $itemsStmt->fetchAll();


require_once __DIR__ . '/includes/header.php';
?>

<div class="container">

  <div
    class="empty-state"
    style="padding-top:40px;"
  >

    <h1
      style="
        color:var(--gold);
        font-size:34px;
      "
    >
      Thank you for your order!
    </h1>


    <p>

      Order #<?= $order['id'] ?>
      has been placed and is now

      <strong>
        <?= h($order['status']) ?>
      </strong>.

    </p>


    <p>

      Payment method:

      <?= h(
          strtoupper(
              $order['payment_method']
          )
      ) ?>

    </p>

  </div>


  <table
    class="cart-table"
    style="
      max-width:750px;
      margin:0 auto;
    "
  >

    <thead>

      <tr>
        <th>Product</th>
        <th>Size</th>
        <th>Qty</th>
        <th>Price</th>
      </tr>

    </thead>


    <tbody>

      <?php foreach ($items as $it): ?>

        <tr>

          <td>
            <?= h($it['name']) ?>
          </td>


          <td>

            <?php if (!empty($it['size'])): ?>

              <?= h($it['size']) ?>

            <?php else: ?>

              —

            <?php endif; ?>

          </td>


          <td>
            <?= (int)$it['quantity'] ?>
          </td>


          <td>

            <?= price(
                $it['price']
                *
                $it['quantity']
            ) ?>

          </td>

        </tr>

      <?php endforeach; ?>

    </tbody>

  </table>


  <p
    style="
      text-align:center;
      font-size:20px;
      color:var(--gold);
      margin-top:16px;
    "
  >

    Total:
    <?= price(
        $order['total_amount']
    ) ?>

  </p>


  <div
    style="
      text-align:center;
      margin:30px 0;
    "
  >

    <a
      href="products.php"
      class="btn btn-primary"
    >
      Continue Shopping
    </a>

  </div>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>