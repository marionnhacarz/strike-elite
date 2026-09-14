<?php
$basePath = '';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';

$stmt = $pdo->prepare("
    SELECT products.*, categories.slug AS category_slug
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    WHERE products.slug = ?
");
$stmt->execute([$slug]);
$p = $stmt->fetch();

if (!$p) {
    header('Location: products.php');
    exit;
}

$pageTitle = $p['name'];
$stock = (int)$p['stock'];

require_once __DIR__ . '/includes/header.php';
?>

<div class="container">

  <div class="page-heading">
    <a href="products.php"
       style="color:var(--text-dim);font-size:14px;">
      &larr; Back to products
    </a>
  </div>

  <div class="about-section" style="margin-top:10px;">

    <img
      src="<?= product_image($p['image']) ?>"
      alt="<?= h($p['name']) ?>"
    >

    <div class="about-text">

      <h3><?= h($p['name']) ?></h3>

      <div class="stars" style="margin:10px 0;">
        <?= str_repeat('★', (int)round($p['rating'])) .
            str_repeat('☆', 5 - (int)round($p['rating'])) ?>
      </div>

      <p style="
          font-size:24px;
          color:var(--gold);
          font-weight:700;
      ">
        <?= price($p['price']) ?>
      </p>

      <p><?= h($p['description']) ?></p>

      <?php if ($stock > 0): ?>

        <p class="stock-badge <?= $stock <= 5 ? 'stock-low' : 'stock-in' ?>">
          <?= $stock <= 5
              ? "Only $stock left in stock"
              : "In stock: $stock available" ?>
        </p>

        <form
          method="POST"
          style="
            display:flex;
            gap:12px;
            align-items:end;
            margin-top:16px;
            flex-wrap:wrap;
          "
        >

          <input
            type="hidden"
            name="product_id"
            value="<?= (int)$p['id'] ?>"
          >

          <input
            type="hidden"
            name="redirect"
            value="product.php?slug=<?= urlencode($p['slug']) ?>"
          >

          <?php if ($p['category_slug'] === 'soccer-boots'): ?>

            <div>
              <label
                for="size"
                style="
                  display:block;
                  margin-bottom:6px;
                  font-weight:600;
                "
              >
                Boot Size
              </label>

              <select
                name="size"
                id="size"
                required
                class="qty-input"
                style="width:auto;min-width:180px;"
              >
                <option value="">Select Size</option>
                <option value="EU 39 / US 6.5">EU 39 / US 6.5</option>
                <option value="EU 40 / US 7">EU 40 / US 7</option>
                <option value="EU 41 / US 8">EU 41 / US 8</option>
                <option value="EU 42 / US 8.5">EU 42 / US 8.5</option>
                <option value="EU 43 / US 9.5">EU 43 / US 9.5</option>
                <option value="EU 44 / US 10">EU 44 / US 10</option>
              </select>
            </div>

          <?php elseif ($p['category_slug'] === 'jersey'): ?>

            <div>
              <label
                for="size"
                style="
                  display:block;
                  margin-bottom:6px;
                  font-weight:600;
                "
              >
                Jersey Size
              </label>

              <select
                name="size"
                id="size"
                required
                class="qty-input"
                style="width:auto;min-width:130px;"
              >
                <option value="">Select Size</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
              </select>
            </div>

            <?php elseif ($p['category_slug'] === 'goalkeeper-gloves'): ?>

    <div>
        <label
            for="size"
            style="
                display:block;
                margin-bottom:6px;
                font-weight:600;
            "
        >
            Glove Size
        </label>

        <select
            name="size"
            id="size"
            required
            class="qty-input"
            style="width:auto; min-width:160px;"
        >
            <option value="">Select Size</option>
            <option value="5 (XXS)">5 (XXS)</option>
            <option value="6 (XS)">6 (XS)</option>
            <option value="7 (S)">7 (S)</option>
            <option value="8 (M)">8 (M)</option>
            <option value="9 (L)">9 (L)</option>
            <option value="10 (XL)">10 (XL)</option>
            <option value="11 (XXL)">11 (XXL)</option>
        </select>
    </div>
            

          <?php else: ?>

            <input
              type="hidden"
              name="size"
              value=""
            >

          <?php endif; ?>

          <div>
            <label
              for="qty"
              style="
                display:block;
                margin-bottom:6px;
                font-weight:600;
              "
            >
              Quantity
            </label>

            <input
              type="number"
              id="qty"
              name="qty"
              value="1"
              min="1"
              max="<?= $stock ?>"
              class="qty-input"
            >
          </div>

          <button
  type="submit"
  formaction="cart_add.php"
  class="btn btn-primary"
>
  Add to Cart
</button>

<button
  type="submit"
  formaction="buy_now.php"
  class="btn btn-primary"
>
  Buy Now
</button>

        </form>

      <?php else: ?>

        <p class="stock-badge stock-out">
          Out of stock
        </p>

        <button
          class="btn disabled"
          disabled
        >
          Unavailable
        </button>

      <?php endif; ?>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>