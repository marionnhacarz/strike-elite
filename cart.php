<?php
$basePath = '';

require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Your Cart';


/*
|--------------------------------------------------------------------------
| Update / remove cart items
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update'])) {

        foreach ($_POST['qty'] ?? [] as $cartKey => $qty) {

            cart_set(
                $cartKey,
                (int)$qty
            );
        }

        flash(
            'flash_success',
            'Cart updated.'
        );

    } elseif (isset($_POST['remove_key'])) {

        cart_remove(
            $_POST['remove_key']
        );

        flash(
            'flash_success',
            'Item removed.'
        );
    }

    header('Location: cart.php');
    exit;
}


$items = cart_details($pdo);

$total = cart_total($pdo);


require_once __DIR__ . '/includes/header.php';
?>

<div class="container">

  <div class="page-heading">
    <h1>Your Cart</h1>
  </div>


  <?php if (empty($items)): ?>

    <div class="empty-state">

      <p>Your cart is empty.</p>

      <br>

      <a
        href="products.php"
        class="btn btn-primary"
      >
        Continue Shopping
      </a>

    </div>

  <?php else: ?>

    <form method="POST">

      <table class="cart-table">

        <thead>
          <tr>
            <th>Product</th>
            <th>Size</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Subtotal</th>
            <th></th>
          </tr>
        </thead>

        <tbody>

          <?php foreach ($items as $row): ?>

            <?php
            $prod = $row['product'];
            $cartKey = $row['cart_key'];
            ?>

            <tr>

              <td>

                <div class="cart-product">

                  <img
                    src="<?= product_image($prod['image']) ?>"
                    alt="<?= h($prod['name']) ?>"
                  >

                  <span>
                    <?= h($prod['name']) ?>
                  </span>

                </div>

              </td>


              <td>

                <?php if (!empty($row['size'])): ?>

                  <strong>
                    <?= h($row['size']) ?>
                  </strong>

                <?php else: ?>

                  <span style="color:var(--text-dim);">
                    —
                  </span>

                <?php endif; ?>

              </td>


              <td>
                <?= price($prod['price']) ?>
              </td>


              <td>

                <input
                  type="number"
                  class="qty-input"
                  name="qty[<?= h($cartKey) ?>]"
                  value="<?= (int)$row['qty'] ?>"
                  min="0"
                  max="<?= (int)$prod['stock'] ?>"
                >

              </td>


              <td>
                <?= price($row['subtotal']) ?>
              </td>


              <td>

                <button
                  type="submit"
                  form="remove-<?= md5($cartKey) ?>"
                  class="remove-link"
                  style="
                    background:none;
                    border:none;
                  "
                >
                  Remove
                </button>

              </td>

            </tr>

          <?php endforeach; ?>

        </tbody>

      </table>


      <button
        type="submit"
        name="update"
        class="btn btn-outline"
      >
        Update Cart
      </button>

    </form>


    <?php foreach ($items as $row): ?>

      <form
        id="remove-<?= md5($row['cart_key']) ?>"
        method="POST"
        style="display:none;"
      >

        <input
          type="hidden"
          name="remove_key"
          value="<?= h($row['cart_key']) ?>"
        >

      </form>

    <?php endforeach; ?>


    <div class="cart-summary">

      <div class="row">

        <span>Subtotal</span>

        <span>
          <?= price($total) ?>
        </span>

      </div>


      <div class="row">

        <span>Shipping</span>

        <span>
          Calculated at checkout
        </span>

      </div>


      <div class="row total">

        <span>Total</span>

        <span>
          <?= price($total) ?>
        </span>

      </div>


      <a
        href="checkout.php"
        class="btn btn-primary btn-block"
        style="margin-top:16px;"
      >
        Proceed to Checkout
      </a>

    </div>

  <?php endif; ?>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>