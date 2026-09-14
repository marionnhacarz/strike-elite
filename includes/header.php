<?php

require_once __DIR__ . '/functions.php';

$currentPage = basename($_SERVER['PHP_SELF']);

// Pages inside subfolders such as /pages/ and /admin/
// can set $basePath = '../' before including this header.
$bp = $basePath ?? '';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>
    <?= isset($pageTitle)
      ? h($pageTitle) . ' | Strike Elite'
      : 'Strike Elite | Play Like a Champion'
    ?>
  </title>

  <link
    rel="preconnect"
    href="https://fonts.googleapis.com"
  >

  <link
    href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Bebas+Neue&display=swap"
    rel="stylesheet"
  >

  <link
    rel="stylesheet"
    href="<?= $bp ?>assets/css/style.css"
  >

</head>

<body>

<header class="site-header">

  <div class="container header-inner">

    <!-- =========================
         LOGO
    ========================== -->

    <a
      href="<?= $bp ?>index.php"
      class="logo"
    >

      <img
        src="<?= $bp ?>assets/images/header logo.png"
        alt="Strike Elite"
        class="site-logo"
      >

    </a>


    <!-- =========================
         MAIN NAVIGATION
    ========================== -->

    <nav class="main-nav">

      <a
        href="<?= $bp ?>index.php"
        class="<?= $currentPage === 'index.php' ? 'active' : '' ?>"
      >
        Home
      </a>

      <a href="<?= $bp ?>products.php?category=soccer-boots">
        Soccer Boots
      </a>

      <a href="<?= $bp ?>products.php?category=jersey">
        Jersey
      </a>

      <a href="<?= $bp ?>products.php?category=equipment">
        Equipment
      </a>

      <a href="<?= $bp ?>pages/about.php">
        About
      </a>

      <a href="<?= $bp ?>pages/contact.php">
        Contact
      </a>

    </nav>


    <!-- =========================
         HEADER ICONS
    ========================== -->

    <div class="header-icons">


      <!-- =========================
           SEARCH
      ========================== -->

      <form
        class="search-form"
        action="<?= $bp ?>products.php"
        method="GET"
      >

        <input
          type="text"
          name="q"
          placeholder="Search products..."
          value="<?= h($_GET['q'] ?? '') ?>"
        >

        <button
          type="submit"
          aria-label="Search"
        >

          <svg
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >

            <circle
              cx="11"
              cy="11"
              r="7"
            />

            <line
              x1="21"
              y1="21"
              x2="16.65"
              y2="16.65"
            />

          </svg>

        </button>

      </form>


      <!-- =========================
           USER / ADMIN ACCOUNT
      ========================== -->

<?php if (is_logged_in()): ?>

    <a
        href="<?= $bp ?><?= is_admin()
            ? 'admin/index.php'
            : 'account.php' ?>"
        class="icon-link"
        title="<?= is_admin()
            ? 'Admin Dashboard'
            : 'My Account' ?>"
    >
        <span class="account-name">

            <?php if (is_admin()): ?>

                Admin:
                <?= h(current_user()['username']) ?>

            <?php else: ?>

                <?= h(current_user()['username']) ?>

            <?php endif; ?>

        </span>
    </a>

    <a
        href="<?= $bp ?>logout.php"
        class="logout-link"
    >
        Logout
    </a>

<?php else: ?>

    <a
        href="<?= $bp ?>login.php"
        class="icon-link"
        title="Login"
    >

        <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >

            <circle
                cx="12"
                cy="8"
                r="4"
            />

            <path
                d="M4 21c0-4 4-6 8-6s8 2 8 6"
            />

        </svg>

        Login
    </a>

<?php endif; ?>


      <!-- =========================
           SHOPPING CART
      ========================== -->

      <a
        href="<?= $bp ?>cart.php"
        class="icon-link cart-link"
        title="Cart"
      >

        <svg
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >

          <circle
            cx="9"
            cy="21"
            r="1"
          />

          <circle
            cx="20"
            cy="21"
            r="1"
          />

          <path
            d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"
          />

        </svg>

        <span class="cart-count">
          <?= cart_count() ?>
        </span>

      </a>

    </div>

  </div>

</header>


<!-- =========================
     SUCCESS MESSAGE
========================== -->

<?php if ($msg = flash('flash_success')): ?>

  <div class="flash flash-success container">
    <?= h($msg) ?>
  </div>

<?php endif; ?>


<!-- =========================
     ERROR MESSAGE
========================== -->

<?php if ($msg = flash('flash_error')): ?>

  <div class="flash flash-error container">
    <?= h($msg) ?>
  </div>

<?php endif; ?>