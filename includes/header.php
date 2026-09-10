<?php

    require_once __DIR__ . '/functions.php';

    $currentPage = basename($_SERVER['PHP_SELF']);

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
    <?php echo isset($pageTitle)
            ? h($pageTitle) . ' | Strike Elite'
            : 'Strike Elite | Play Like a Champion';
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
    href="<?php echo $bp ?>assets/css/style.css"
  >

</head>

<body>

<header class="site-header">

  <div class="container header-inner">

    <!-- =========================
         LOGO
    ========================== -->

    <a
      href="<?php echo $bp ?>index.php"
      class="logo"
    >

      <img
        src="<?php echo $bp ?>assets/images/header logo.png"
        alt="Strike Elite"
        class="site-logo"
      >

    </a>


    <!-- =========================
         MAIN NAVIGATION
    ========================== -->

    <nav class="main-nav">

      <a
        href="<?php echo $bp ?>index.php"
        class="<?php echo $currentPage === 'index.php' ? 'active' : '' ?>"
      >
        Home
      </a>

      <a href="<?php echo $bp ?>products.php?category=soccer-boots">
        Soccer Boots
      </a>

      <a href="<?php echo $bp ?>products.php?category=jersey">
        Jersey
      </a>

      <a href="<?php echo $bp ?>products.php?category=equipment">
        Equipment
      </a>

      <a href="<?php echo $bp ?>pages/about.php">
        About
      </a>

      <a href="<?php echo $bp ?>pages/contact.php">
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
        action="<?php echo $bp ?>products.php"
        method="GET"
      >

        <input
          type="text"
          name="q"
          placeholder="Search products..."
          value="<?php echo h($_GET['q'] ?? '') ?>"
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

        <div class="account-menu">


          <!-- ADMIN ACCOUNT -->

          <?php if (($_SESSION['role'] ?? 'client') === 'admin'): ?>

            <a
              href="<?php echo $bp ?>admin/index.php"
              class="icon-link"
              title="Admin Dashboard"
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

              <span class="account-name">
                Admin: <?php echo h(current_user()['username']) ?>
              </span>

            </a>


          <!-- CLIENT ACCOUNT -->

          <?php else: ?>

            <a
              href="<?php echo $bp ?>account.php"
              class="icon-link"
              title="My Account"
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

              <span class="account-name">
                <?php echo h(current_user()['username']) ?>
              </span>

            </a>

          <?php endif; ?>


          <!-- LOGOUT -->

          <a
            href="<?php echo $bp ?>logout.php"
            class="icon-link small-link"
            title="Logout"
          >
            Logout
          </a>

        </div>


      <!-- =========================
           NOT LOGGED IN
      ========================== -->

      <?php else: ?>

        <a
          href="<?php echo $bp ?>login.php"
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

        </a>

      <?php endif; ?>


      <!-- =========================
           SHOPPING CART
      ========================== -->

      <a
        href="<?php echo $bp ?>cart.php"
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
          <?php echo cart_count() ?>
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
    <?php echo h($msg) ?>
  </div>

<?php endif; ?>


<!-- =========================
     ERROR MESSAGE
========================== -->

<?php if ($msg = flash('flash_error')): ?>

  <div class="flash flash-error container">
    <?php echo h($msg) ?>
  </div>

<?php endif; ?>