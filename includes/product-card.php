<?php
    $bp    = $basePath ?? '';
    $stock = (int) $p['stock'];
    if ($stock <= 0) {
    $stockClass = 'stock-out';
    $stockLabel = 'Out of stock';
    } elseif ($stock <= 5) {
    $stockClass = 'stock-low';
    $stockLabel = "Only $stock left";
    } else {
    $stockClass = 'stock-in';
    $stockLabel = "In stock: $stock";
    }
    $stars = str_repeat('★', (int) round($p['rating'])) . str_repeat('☆', 5 - (int) round($p['rating']));
?>
<div class="product-card">
  <a href="<?php echo $bp ?>product.php?slug=<?php echo urlencode($p['slug']) ?>" class="thumb">
    <img src="<?php echo $bp . product_image($p['image']) ?>" alt="<?php echo h($p['name']) ?>">
  </a>
  <div class="info">
    <a href="<?php echo $bp ?>product.php?slug=<?php echo urlencode($p['slug']) ?>"><h4><?php echo h($p['name']) ?></h4></a>
    <div class="stars"><?php echo $stars ?></div>
    <div class="stock-badge <?php echo $stockClass ?>"><?php echo $stockLabel ?></div>
    <div class="price-row">
      <span class="amount"><?php echo price($p['price']) ?></span>
      <form action="<?php echo $bp ?>cart_add.php" method="POST">
        <input type="hidden" name="product_id" value="<?php echo (int)$p['id'] ?>">
        <input type="hidden" name="redirect" value="<?php echo h($_SERVER['REQUEST_URI']) ?>">
        <button type="submit" class="add-cart-btn" <?php echo $stock <= 0 ? 'disabled' : '' ?>>+</button>
      </form>
    </div>
  </div>
</div>
