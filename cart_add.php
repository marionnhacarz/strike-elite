<?php
require_once __DIR__ . '/includes/functions.php';

$productId = (int)($_POST['product_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
$redirect = $_POST['redirect'] ?? 'products.php';

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    flash('flash_error', 'Product not found.');
    header('Location: products.php');
    exit;
}

$alreadyInCart = $_SESSION['cart'][$productId] ?? 0;
if (($alreadyInCart + $qty) > (int)$product['stock']) {
    flash('flash_error', 'Sorry, only ' . $product['stock'] . ' unit(s) of "' . $product['name'] . '" are in stock.');
} else {
    cart_add($productId, $qty);
    flash('flash_success', $product['name'] . ' added to your cart.');
}

header('Location: ' . $redirect);
exit;
