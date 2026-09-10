<?php
$basePath = '';
require_once __DIR__ . '/includes/functions.php';

$category = $_GET['category'] ?? '';
$q = trim($_GET['q'] ?? '');
$pageTitle = $category ? ucwords(str_replace('-', ' ', $category)) : 'All Products';

$sql = "SELECT p.* FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($category !== '') {
    $sql .= " AND c.slug = ?";
    $params[] = $category;
}
if ($q !== '') {
    $sql .= " AND p.name LIKE ?";
    $params[] = "%$q%";
}
$sql .= " ORDER BY p.name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-heading">
    <h1><?= h($pageTitle) ?></h1>
    <?php if ($q): ?><p style="color:var(--text-dim)">Search results for "<?= h($q) ?>"</p><?php endif; ?>
  </div>

  <div class="main-nav" style="margin-bottom:24px;">
    <a href="products.php" class="<?= $category === '' ? 'active' : '' ?>">All</a>
    <?php foreach ($categories as $c): ?>
      <a href="products.php?category=<?= urlencode($c['slug']) ?>" class="<?= $category === $c['slug'] ? 'active' : '' ?>"><?= h($c['name']) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (empty($products)): ?>
    <div class="empty-state">No products found.</div>
  <?php else: ?>
    <div class="product-grid">
      <?php foreach ($products as $p): ?>
        <?php include __DIR__ . '/includes/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
