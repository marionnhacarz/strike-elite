<?php
$basePath = '';

require_once __DIR__ . '/includes/functions.php';
$buyNowMode = ($_GET['mode'] ?? '') === 'buy_now';
$pageTitle = 'Checkout';

require_login('login.php');


if ($buyNowMode) {
    $items = buy_now_details($pdo);
} else {
    $items = cart_details($pdo);
}

if (empty($items)) {

    if ($buyNowMode) {
        header('Location: products.php');
    } else {
        header('Location: cart.php');
    }

    exit;
}


$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['shipping_name'] ?? '');

    $address = trim($_POST['shipping_address'] ?? '');

    $paymentMethod = $_POST['payment_method'] ?? '';


    $validPayments = [ 'cod', 'gcash', 'card' ];

    if (empty($name)) {
        $errors[] = 'Full name is required.';
    }


    if (empty($address)) {
        $errors[] = 'Shipping address is required.';
    }


    if (
        !in_array( $paymentMethod, $validPayments, true )
    ) {

        $errors[] = 'Please select a valid payment method.';
    }
    /*
    Demo card validation
    */

if ($paymentMethod === 'card') {

    $cardNumber = preg_replace(
        '/\s+/',
        '',
        $_POST['card_number'] ?? ''
    );

    if (!preg_match('/^\d{13,19}$/', $cardNumber)) {
        $errors[] = 'Enter a valid card number.';
    }
}


    if (empty($errors)) {

    try {

    $pdo->beginTransaction();


      /*     
      | Check stock      
      */
      $quantitiesByProduct = [];


            /*
       * A customer may buy the same product
       * in multiple sizes.
       */
        foreach ($items as $row) {
      $pid = (int)$row['product']['id'];
      
      if ( !isset( $quantitiesByProduct[$pid] )) {

      $quantitiesByProduct[$pid] = [ 'qty'  => 0, 'name' => $row['product']['name']];
                
      }
      $quantitiesByProduct[$pid]['qty'] += (int)$row['qty'];
      }

        foreach ( $quantitiesByProduct as $pid => $item) {
        $stmt = $pdo->prepare( " SELECT stock FROM products WHERE id = ? FOR UPDATE " );
  
        $stmt->execute([$pid]);
        $currentStock = (int)$stmt->fetchColumn();

    if ($currentStock < $item['qty']) {
    throw new Exception('Not enough stock for "' .$item['name'] . '". Only ' . $currentStock . ' left.');
                }
            }


    /*
    Create order
    */

$total = $buyNowMode
    ? buy_now_total($pdo)
    : cart_total($pdo);

    $orderStmt = $pdo->prepare( " INSERT INTO orders (user_id, total_amount, payment_method, status,
    shipping_name, shipping_address )
    VALUES ( :uid, :total, :pm, 'processing', :sname, :saddr) ");

      $orderStmt->execute([ ':uid' => $_SESSION['user_id'], ':total' =>  $total,
      ':pm' => $paymentMethod, ':sname' => $name, ':saddr' => $address, ]);
       $orderId = $pdo->lastInsertId();

        /*
        Save order items + size
        */
      $itemStmt = $pdo->prepare( " INSERT INTO order_items ( order_id, product_id, size, quantity,
         price) VALUES (?, ?, ?, ?, ?) " );

      foreach ($items as $row) {
       $itemStmt->execute([
       $orderId, $row['product']['id'],
       $row['size'] !== '' ? $row['size'] : null,
       $row['qty'], $row['product']['price']]);
            }


    /*      
    Reduce product stock
    */

     $stockStmt = $pdo->prepare( " UPDATE products SET stock = stock - ? WHERE id = ? " );

     foreach ( $quantitiesByProduct as $pid => $item ) {

     $stockStmt->execute([ $item['qty'], $pid ]);
            }
     $pdo->commit();

if ($buyNowMode) {
    buy_now_clear();
} else {
    cart_clear();
}
   header( 'Location: order_success.php?id=' .  $orderId ); exit;

     } catch (Exception $e) {  if ($pdo->inTransaction()) {
       $pdo->rollBack();
    }

      $errors[] = $e->getMessage(); } }
}


$total = $buyNowMode
    ? buy_now_total($pdo)
    : cart_total($pdo);

require_once __DIR__ . '/includes/header.php';?>

<div class="container">

  <div class="page-heading">
    <h1>Checkout</h1>
  </div>


  <div
    class="about-section"
    style="align-items:flex-start;"
  >

    <div
      class="form-page"
      style="
        margin:0;
        max-width:100%;
      "
    >

      <?php foreach ($errors as $e): ?>

        <div class="form-error">
          <?= h($e) ?>
        </div>

      <?php endforeach; ?>


     <form
    method="POST"
    action="<?= $buyNowMode
        ? 'checkout.php?mode=buy_now'
        : 'checkout.php' ?>"
>

        <h3 style=" color:var(--gold); margin-bottom:14px;">
          Shipping Details
        </h3>


        <div class="field">

          <label>
            Full Name
          </label>

          <input
            type="text"
            name="shipping_name"
            value="<?= h(
                $_POST['shipping_name']
                ?? current_user()['username']
            ) ?>"
            required
          >

        </div>


        <div class="field">

          <label>
            Shipping Address
          </label>

          <textarea
            name="shipping_address"
            required
          ><?= h(
              $_POST['shipping_address']
              ?? ''
          ) ?></textarea>

        </div>


        <h3
          style="
            color:var(--gold);
            margin-bottom:14px;
          "
        >
          Payment Method
        </h3>


        <div class="payment-options">

          <label class="payment-option">

            <input
              type="radio"
              name="payment_method"
              value="cod"
              checked
            >

            Cash on Delivery

          </label>


          <label class="payment-option">

            <input
              type="radio"
              name="payment_method"
              value="gcash"
            >

            GCash

          </label>


          <label class="payment-option">

            <input
              type="radio"
              name="payment_method"
              value="card"
            >

            Credit / Debit Card

          </label>

        </div>


        <div class="field">

          <label>
            Card Number
            (only required if paying by card —
            demo only, not processed for real)
          </label>

          <input
            type="text"
            name="card_number"
            placeholder="4111 1111 1111 1111"
            maxlength="19"
          >

        </div>


        <button
          type="submit"
          class="btn btn-primary btn-block"
        >
          Place Order —
          <?= price($total) ?>
        </button>

      </form>

    </div>


    <div
      class="cart-summary"
      style="margin:0;"
    >

      <h3 style="margin-bottom:14px;">
        Order Summary
      </h3>


      <?php foreach ($items as $row): ?>

        <div class="row">

          <span>

            <?= h(
                $row['product']['name']
            ) ?>

            <?php if (!empty($row['size'])): ?>

              <small
                style="
                  display:block;
                  color:var(--text-dim);
                "
              >
                Size:
                <?= h($row['size']) ?>
              </small>

            <?php endif; ?>

            ×<?= (int)$row['qty'] ?>

          </span>


          <span>
            <?= price(
                $row['subtotal']
            ) ?>
          </span>

        </div>

      <?php endforeach; ?>


      <div class="row total">

        <span>Total</span>

        <span>
          <?= price($total) ?>
        </span>

      </div>

    </div>

  </div>

</div>
<?php
require_once __DIR__ . '/includes/footer.php';
?>