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
        'role'     => $_SESSION['role'] ?? 'client',
    ];
}

function require_login($redirectTo = 'login.php') {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        header('Location: ' . $redirectTo);
        exit;
    }
}
function is_admin() {
    return is_logged_in()
        && ($_SESSION['role'] ?? '') === 'admin';
}

function require_admin() {
    global $pdo;

    if (!is_logged_in()) {
        header('Location: ../login.php');
        exit;
    }

    // Don't trust the role stored only in the session.
    // Check the database every time an admin page is opened.
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $role = $stmt->fetchColumn();

    if ($role !== 'admin') {
        $_SESSION['flash_error'] = 'You do not have permission to access the admin area.';
        header('Location: ../account.php');
        exit;
    }

    // Keep the current session role synchronized.
    $_SESSION['role'] = $role;
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

// ----- cart helpers -----

function cart_items() {
    return $_SESSION['cart'] ?? [];
}


/*
Create a unique cart key
| Example:
| Product 3, size M:
*/
function cart_key($productId, $size = '') {
    $productId = (int)$productId;

    $cleanSize = trim($size);

    if ($cleanSize === '') {
        $cleanSize = 'no-size';
    }

    return $productId . '_' . md5($cleanSize);
}


function cart_count() {
    $total = 0;

    foreach (cart_items() as $item) {
        $total += (int)($item['qty'] ?? 0);
    }

    return $total;
}


function cart_add($productId, $qty = 1, $size = '') {
    $productId = (int)$productId;
    $qty = max(1, (int)$qty);
    $size = trim($size);

    $key = cart_key($productId, $size);

    if (!isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key] = [
            'product_id' => $productId,
            'qty'        => 0,
            'size'       => $size,
        ];
    }

    $_SESSION['cart'][$key]['qty'] += $qty;
}


function cart_set($cartKey, $qty) {
    $qty = (int)$qty;

    if (!isset($_SESSION['cart'][$cartKey])) {
        return;
    }

    if ($qty <= 0) {
        unset($_SESSION['cart'][$cartKey]);
    } else {
        $_SESSION['cart'][$cartKey]['qty'] = $qty;
    }
}


function cart_remove($cartKey) {
    if (isset($_SESSION['cart'][$cartKey])) {
        unset($_SESSION['cart'][$cartKey]);
    }
}


function cart_clear() {
    $_SESSION['cart'] = [];
}


/*
|--------------------------------------------------------------------------
| Get full cart product information
|--------------------------------------------------------------------------
*/
function cart_details(PDO $pdo) {

    $items = cart_items();

    if (empty($items)) {
        return [];
    }

    $productIds = [];

    foreach ($items as $item) {
        $productIds[] = (int)$item['product_id'];
    }

    $productIds = array_unique($productIds);

    $placeholders = implode(
        ',',
        array_fill(0, count($productIds), '?')
    );

    $stmt = $pdo->prepare(
        "SELECT * FROM products WHERE id IN ($placeholders)"
    );

    $stmt->execute($productIds);

    $products = $stmt->fetchAll();

    $productMap = [];

    foreach ($products as $product) {
        $productMap[$product['id']] = $product;
    }

    $details = [];

    foreach ($items as $cartKey => $item) {

        $productId = (int)$item['product_id'];

        if (!isset($productMap[$productId])) {
            continue;
        }

        $product = $productMap[$productId];
        $qty = (int)$item['qty'];
        $size = $item['size'] ?? '';

        $details[] = [
            'cart_key' => $cartKey,
            'product'  => $product,
            'qty'      => $qty,
            'size'     => $size,
            'subtotal' => $qty * $product['price'],
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
// ----- buy now helpers -----

function buy_now_details(PDO $pdo) {

    $item = $_SESSION['buy_now'] ?? null;

    if (!$item) {
        return [];
    }

    $productId = (int)($item['product_id'] ?? 0);
    $qty       = max(1, (int)($item['qty'] ?? 1));
    $size      = trim($item['size'] ?? '');

    if ($productId <= 0) {
        return [];
    }

    $stmt = $pdo->prepare("
        SELECT *
        FROM products
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$productId]);

    $product = $stmt->fetch();

    if (!$product) {
        return [];
    }

    return [
        [
            'cart_key' => 'buy_now',
            'product'  => $product,
            'qty'      => $qty,
            'size'     => $size,
            'subtotal' => $qty * $product['price'],
        ]
    ];
}


function buy_now_total(PDO $pdo) {

    $total = 0;

    foreach (buy_now_details($pdo) as $row) {
        $total += $row['subtotal'];
    }

    return $total;
}


function buy_now_clear() {
    unset($_SESSION['buy_now']);
}

// Falls back to the placeholder graphic if the real product photo hasn't been added yet
function product_image($filename) {
    $path = __DIR__ . '/../assets/images/products/' . $filename;
    if ($filename && file_exists($path)) {
        return 'assets/images/products/' . $filename;
    }
    return 'assets/images/products/placeholder.svg';
}
