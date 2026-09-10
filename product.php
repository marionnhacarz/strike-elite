<?php
    $basePath = '';
    require_once __DIR__ . '/includes/functions.php';

    $slug = $_GET['slug'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ?");
    $stmt->execute([$slug]);
    $p = $stmt->fetch();

    if (! $p) {
    header('Location: products.php');
    exit;
    }

    $pageTitle = $p['name'];
    $stock     = (int) $p['stock'];
    require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-heading"><a href="products.php" style="color:var(--text-dim);font-size:14px;">&larr; Back to products</a></div>

  <div class="about-section" style="margin-top:10px;">
    <img src="<?php echo product_image($p['image']) ?>" alt="<?php echo h($p['name']) ?>">
    <div class="about-text">
      <h3><?php echo h($p['name']) ?></h3>
      <div class="stars" style="margin:10px 0;"><?php echo str_repeat('★', (int)round($p['rating'])) . str_repeat('☆', 5 - (int)round($p['rating'])) ?></div>
      <p style="font-size:24px;color:var(--gold);font-weight:700;"><?php echo price($p['price']) ?></p>
      <p><?php echo h($p['description']) ?></p>

      <?php if ($stock > 0): ?>
        <p class="stock-badge <?php echo $stock <= 5 ? 'stock-low' : 'stock-in' ?>">
          <?php echo $stock <= 5 ? "Only $stock left in stock" : "In stock: $stock available" ?>
        </p>
        <form action="cart_add.php" method="POST" style="display:flex; gap:12px; align-items:center; margin-top:16px;">
          <input type="hidden" name="product_id" value="<?php echo (int)$p['id'] ?>">
          <input type="hidden" name="redirect" value="product.php?slug=<?php echo urlencode($p['slug']) ?>">
          <input type="number" name="qty" value="1" min="1" max="<?php echo $stock ?>" class="qty-input">
          <button type="submit" class="btn btn-primary">Add to Cart</button>
        </form>
      <?php else: ?>
        <p class="stock-badge stock-out">Out of stock</p>
        <button class="btn disabled" disabled>Unavailable</button>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
