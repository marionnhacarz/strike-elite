<?php
// Helper functions used across the whole site — every page pulls this in
// through includes/header.php so we don't have to require it everywhere.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Shortcut for escaping output, so we're not typing htmlspecialchars(...) everywhere
function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Formats prices as peso amounts, e.g. ₱5,499.00
function price($amount) {
    return '₱' . number_format((float)$amount, 2);
}

// ----- auth helpers -----

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function current_user() {
    if (!is_logged_in()) return null;
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email'    => $_SESSION['email'],
    ];
}

function require_login($redirectTo = 'login.php') {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        header('Location: ' . $redirectTo);
        exit;
    }
}

// ----- flash messages -----

function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION[$key] = $message;
        return;
    }
    if (!empty($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

// ----- cart helpers (cart is just $_SESSION['cart'] = [product_id => qty]) -----

function cart_items() {
    return $_SESSION['cart'] ?? [];
}

function cart_count() {
    $total = 0;
    foreach (cart_items() as $qty) {
        $total += (int)$qty;
    }
    return $total;
}

function cart_add($productId, $qty = 1) {
    $productId = (int)$productId;
    $qty = max(1, (int)$qty);
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 0;
    }
    $_SESSION['cart'][$productId] += $qty;
}

function cart_set($productId, $qty) {
    $productId = (int)$productId;
    $qty = (int)$qty;
    if ($qty <= 0) {
        unset($_SESSION['cart'][$productId]);
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function cart_remove($productId) {
    unset($_SESSION['cart'][(int)$productId]);
}

function cart_clear() {
    $_SESSION['cart'] = [];
}

// Pulls the actual product rows (name, price, stock, etc.) for whatever is in the cart
function cart_details(PDO $pdo) {
    $items = cart_items();
    if (empty($items)) return [];

    $ids = array_map('intval', array_keys($items));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $details = [];
    foreach ($products as $p) {
        $qty = $items[$p['id']];
        $details[] = [
            'product'  => $p,
            'qty'      => $qty,
            'subtotal' => $qty * $p['price'],
        ];
    }
    return $details;
}

function cart_total(PDO $pdo) {
    $total = 0;
    foreach (cart_details($pdo) as $row) {
        $total += $row['subtotal'];
    }
    return $total;
}

// Falls back to the placeholder graphic if the real product photo hasn't been added yet
function product_image($filename) {
    $path = __DIR__ . '/../assets/images/products/' . $filename;
    if ($filename && file_exists($path)) {
        return 'assets/images/products/' . $filename;
    }
    return 'assets/images/products/placeholder.svg';
}
