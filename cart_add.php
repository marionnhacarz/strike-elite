<?php
require_once __DIR__ . '/includes/functions.php';

if (!is_logged_in()) {
    $_SESSION['after_login'] = $_POST['redirect'] ?? 'products.php';

    flash(
        'flash_error',
        'Please log in before adding products to your cart.'
    );

    header('Location: login.php');
    exit;
}
if (is_admin()) {
    flash(
        'flash_error',
        'Please use a client account to add products to the cart.'
    );

    header('Location: ' . ($_POST['redirect'] ?? 'products.php'));
    exit;
}
$productId = (int)($_POST['product_id'] ?? 0);
$qty = max(1, (int)($_POST['qty'] ?? 1));
$size = trim($_POST['size'] ?? '');
$redirect = $_POST['redirect'] ?? 'products.php';


$stmt = $pdo->prepare(" SELECT products.*, categories.slug AS category_slug FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    WHERE products.id = ?");

$stmt->execute([$productId]);

$product = $stmt->fetch();


if (!$product) {

    flash('flash_error', 'Product not found.');
    header('Location: products.php');
    exit;
}


/*
Validate size
*/

$bootSizes = [
    'EU 39 / US 6.5',
    'EU 40 / US 7',
    'EU 41 / US 8',
    'EU 42 / US 8.5',
    'EU 43 / US 9.5',
    'EU 44 / US 10'
];

$jerseySizes = [
    'S',
    'M',
    'L',
    'XL'
];

$gloveSizes = [
    '5 (XXS)',
    '6 (XS)',
    '7 (S)',
    '8 (M)',
    '9 (L)',
    '10 (XL)',
    '11 (XXL)'
];


if ($product['category_slug'] === 'soccer-boots') {

    if (!in_array($size, $bootSizes, true)) {

        flash('flash_error','Please select a valid boot size.');
        header('Location: ' . $redirect);
        exit;
    }


} elseif ($product['category_slug'] === 'jersey') {

    if (!in_array($size, $jerseySizes, true)) {

        flash('flash_error', 'Please select a valid jersey size.');
        header('Location: ' . $redirect);
        exit;
    }


} elseif ($product['category_slug'] === 'goalkeeper-gloves') {

    if (!in_array($size, $gloveSizes, true)) {

        flash('flash_error', 'Please select a valid goalkeeper glove size.');
        header('Location: ' . $redirect);
        exit;
    }

} else {

    $size = '';
}


/*
Check current quantity in cart
*/

$key = cart_key($productId, $size);

$alreadyInCart = 0;

if (isset($_SESSION['cart'][$key])) {
    $alreadyInCart = (int)$_SESSION['cart'][$key]['qty'];
}

/*
Stock validation
*/

if (($alreadyInCart + $qty) > (int)$product['stock']) {

    flash('flash_error', 'Sorry, only ' . $product['stock'] . ' unit(s) of "' . 
    $product['name'] . '" are in stock.');

} else {

    cart_add($productId, $qty, $size);

    $message = $product['name'];
    
    if ($size !== '') {
        $message .= ' - Size ' . $size;
    }

    $message .= ' added to your cart.';

    flash('flash_success',$message);
}


header('Location: ' . $redirect);
exit;