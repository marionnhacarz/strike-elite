<?php $bp = $basePath ?? ''; ?>

<section class="newsletter">
  <div class="container newsletter-inner">
    <div>
      <h3>JOIN THE STRIKE ELITE TEAM</h3>
      <p>Get exclusive offers, new releases, and athlete stories.</p>
    </div>
    <form action="<?= $bp ?>newsletter.php" method="POST" class="newsletter-form">
      <input type="email" name="email" placeholder="Enter your email address" required>
      <button type="submit">SUBSCRIBE +</button>
    </form>
  </div>
</section>

<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <img src="<?= $bp ?>assets/images/footer logo.png" alt="Strike Elite" class="footer-logo">
      <p class="brand-tag">Play with confidence.<br>Win with pride.</p>
      <div class="social-links">
        <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Facebook">
          <img src="<?= $bp ?>assets/images/icons/facebook.png" alt="Facebook" class="social-icon">
        </a>
        <a href="https://tiktok.com" target="_blank" rel="noopener" aria-label="TikTok">
          <img src="<?= $bp ?>assets/images/icons/tik-tok.png" alt="TikTok" class="social-icon">
        </a>
        <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Instagram">
          <img src="<?= $bp ?>assets/images/icons/instagram.png" alt="Instagram" class="social-icon">
        </a>
      </div>
      <p class="ig-handle">@StrikeElite_Official</p>
    </div>

    <div class="footer-links">
      <h4>QUICK LINKS</h4>
      <a href="<?= $bp ?>index.php">Home</a>
      <a href="<?= $bp ?>products.php?category=soccer-boots">Soccer Boots</a>
      <a href="<?= $bp ?>products.php?category=jersey">Jersey</a>
      <a href="<?= $bp ?>products.php?category=equipment">Equipment</a>
      <a href="<?= $bp ?>pages/about.php">About Us</a>
      <a href="<?= $bp ?>pages/contact.php">Contact Us</a>
    </div>

    <div class="footer-links">
      <h4>CUSTOMER SUPPORT</h4>
      <a href="<?= $bp ?>pages/faq.php">FAQ</a>
      <a href="<?= $bp ?>pages/shipping.php">Shipping and Delivery</a>
      <a href="<?= $bp ?>pages/returns.php">Returns and Exchanges</a>
      <a href="<?= $bp ?>pages/size-guide.php">Size Guide</a>
      <a href="<?= $bp ?>pages/contact.php">Contact Us</a>
    </div>
  </div>

  <p class="footer-copy">© <?= date('Y') ?> Strike Elite. All rights reserved.</p>
</footer>

<script src="<?= $bp ?>assets/js/main.js"></script>
</body>
</html>