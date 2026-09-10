<?php
// Reused on index.php and products.php — whatever page includes this
// needs to set $p to a single row from the products table first.
$bp = $basePath ?? '';
$stock = (int)$p['stock'];
if ($stock <= 0) {
    $stockClass = 'stock-out'; $stockLabel = 'Out of stock';
} elseif ($stock <= 5) {
    $stockClass = 'stock-low'; $stockLabel = "Only $stock left";
} else {
    $stockClass = 'stock-in'; $stockLabel = "In stock: $stock";
}
$stars = str_repeat('★', (int)round($p['rating'])) . str_repeat('☆', 5 - (int)round($p['rating']));
?>
<div class="product-card">
  <a href="<?= $bp ?>product.php?slug=<?= urlencode($p['slug']) ?>" class="thumb">
    <img src="<?= $bp . product_image($p['image']) ?>" alt="<?= h($p['name']) ?>">
  </a>
  <div class="info">
    <a href="<?= $bp ?>product.php?slug=<?= urlencode($p['slug']) ?>"><h4><?= h($p['name']) ?></h4></a>
    <div class="stars"><?= $stars ?></div>
    <div class="stock-badge <?= $stockClass ?>"><?= $stockLabel ?></div>
    <div class="price-row">
      <span class="amount"><?= price($p['price']) ?></span>
      <form action="<?= $bp ?>cart_add.php" method="POST">
        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
        <input type="hidden" name="redirect" value="<?= h($_SERVER['REQUEST_URI']) ?>">
        <button type="submit" class="add-cart-btn" <?= $stock <= 0 ? 'disabled' : '' ?>>+</button>
      </form>
    </div>
  </div>
</div>
