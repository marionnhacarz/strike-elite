<?php

require_once __DIR__ . '/includes/functions.php';


$productId = (int)($_POST['product_id'] ?? 0);
$qty       = max(1, (int)($_POST['qty'] ?? 1));
$size      = trim($_POST['size'] ?? '');
$redirect  = $_POST['redirect'] ?? 'products.php';


/*
|--------------------------------------------------------------------------
| Get product
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        products.*,
        categories.slug AS category_slug
    FROM products
    LEFT JOIN categories
        ON products.category_id = categories.id
    WHERE products.id = ?
    LIMIT 1
");

$stmt->execute([$productId]);

$product = $stmt->fetch();


if (!$product) {

    flash(
        'flash_error',
        'Product not found.'
    );

    header('Location: products.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Check stock
|--------------------------------------------------------------------------
*/

if ((int)$product['stock'] <= 0) {

    flash(
        'flash_error',
        'This product is out of stock.'
    );

    header('Location: ' . $redirect);
    exit;
}


if ($qty > (int)$product['stock']) {

    flash(
        'flash_error',
        'Only ' .
        (int)$product['stock'] .
        ' item(s) available.'
    );

    header('Location: ' . $redirect);
    exit;
}


/*
|--------------------------------------------------------------------------
| Valid sizes
|--------------------------------------------------------------------------
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


/*
|--------------------------------------------------------------------------
| Validate size
|--------------------------------------------------------------------------
*/

if ($product['category_slug'] === 'soccer-boots') {

    if (!in_array($size, $bootSizes, true)) {

        flash(
            'flash_error',
            'Please select a valid boot size.'
        );

        header('Location: ' . $redirect);
        exit;
    }


} elseif ($product['category_slug'] === 'jersey') {

    if (!in_array($size, $jerseySizes, true)) {

        flash(
            'flash_error',
            'Please select a valid jersey size.'
        );

        header('Location: ' . $redirect);
        exit;
    }


} elseif ($product['category_slug'] === 'goalkeeper-gloves') {

    if (!in_array($size, $gloveSizes, true)) {

        flash(
            'flash_error',
            'Please select a valid glove size.'
        );

        header('Location: ' . $redirect);
        exit;
    }


} else {

    $size = '';
}


/*
|--------------------------------------------------------------------------
| Store Buy Now item separately from cart
|--------------------------------------------------------------------------
*/

$_SESSION['buy_now'] = [
    'product_id' => $productId,
    'qty'        => $qty,
    'size'       => $size
];


/*
|--------------------------------------------------------------------------
| Require client login
|--------------------------------------------------------------------------
*/

if (!is_logged_in()) {

    $_SESSION['after_login'] =
        'checkout.php?mode=buy_now';

    flash(
        'flash_error',
        'Please log in before purchasing.'
    );

    header('Location: login.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| Prevent admin from buying
|--------------------------------------------------------------------------
*/

if (is_admin()) {

    flash(
        'flash_error',
        'Please use a client account to purchase products.'
    );

    header('Location: ' . $redirect);
    exit;
}


/*
|--------------------------------------------------------------------------
| Go directly to checkout
|--------------------------------------------------------------------------
*/

header('Location: checkout.php?mode=buy_now');
exit;