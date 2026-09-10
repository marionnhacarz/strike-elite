<?php
$basePath = '';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'My Account';
require_login('login.php');

if (($_SESSION['role'] ?? 'client') === 'admin') {
    header('Location: admin/index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-heading">
    <h1>My Account</h1>
    <p style="color:var(--text-dim)">Signed in as <?= h(current_user()['email']) ?></p>
  </div>

  <h3 style="color:var(--gold);margin:20px 0;">Order History</h3>
  <?php if (empty($orders)): ?>
    <p style="color:var(--text-dim)">You haven't placed any orders yet. <a href="products.php" style="color:var(--gold)">Start shopping</a>.</p>
  <?php else: ?>
    <table class="cart-table">
      <thead><tr><th>Order #</th><th>Date</th><th>Payment</th><th>Status</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
            <td><?= h(strtoupper($o['payment_method'])) ?></td>
            <td><?= h(ucfirst($o['status'])) ?></td>
            <td><?= price($o['total_amount']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
