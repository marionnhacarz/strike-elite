<?php
    $basePath = '';
    require_once __DIR__ . '/includes/functions.php';
    $pageTitle = 'Home';

    $bestSellers = $pdo->query("SELECT * FROM products WHERE is_bestseller = 1 ORDER BY id LIMIT 4")->fetchAll();

    require_once __DIR__ . '/includes/header.php';
?>

<section class="hero" style="background-image: linear-gradient(to right, #000 20%, rgba(0, 0, 0, 0.55) 55%, transparent 100%), url('<?php echo $bp ?>assets/images/header hero.png');">
  <div class="container hero-inner">
    <div class="hero-content">
      <h1>PLAY LIKE A <br><span class="accent">CHAMPION</span></h1>
      <p>Elite soccer gear engineered for speed, control, and performance.</p>
      <div class="hero-actions">
        <a href="<?php echo $bp ?>products.php?category=soccer-boots" class="btn btn-primary">SHOP BOOTS &rarr;</a>
        <a href="<?php echo $bp ?>products.php" class="btn btn-outline">EXPLORE COLLECTION</a>
      </div>
    </div>
  </div>
</section>

<div class="container">
  <div class="category-grid">
    <a href="products.php?category=soccer-boots" class="category-card">
      <img src="assets/images/products/cleat2.png" onerror="this.src='assets/images/products/placeholder.svg'" alt="Soccer Boots">
      <div><h4>SOCCER <span class="accent">BOOTS</span></h4><small>Shop now →</small></div>
    </a>
    <a href="products.php?category=jersey" class="category-card">
      <img src="assets/images/products/titan-jersey.png" onerror="this.src='assets/images/products/placeholder.svg'" alt="Match Jersey">
      <div><h4>MATCH <span class="accent">JERSEY</span></h4><small>Shop now →</small></div>
    </a>
    <a href="products.php?category=equipment" class="category-card">
      <img src="assets/images/products/goalkeeper-gloves.png" onerror="this.src='assets/images/products/placeholder.svg'" alt="Goalkeeper Gear">
      <div><h4>GOALKEEPER <span class="accent">GEAR</span></h4><small>Shop now →</small></div>
    </a>
  </div>

  <section class="title-row">
  <h2>BEST SELLER</h2>
  <div class="divider"></div>
</section>

  <div class="product-grid">
    <?php foreach ($bestSellers as $p): ?>
      <?php include __DIR__ . '/includes/product-card.php'; ?>
    <?php endforeach; ?>
  </div>

  <section class="title-row">
    <h2>WHAT OUR PLAYERS SAY</h2>
    <div class="divider"></div>
  </section>
  <div class="testimonial-grid">
    <div class="testimonial">
      <div class="quote-mark">&ldquo;</div>
      <p>The boots provide excellent grip and comfort during every match.</p>
      <div class="author">CARLO M.</div>
      <div class="verified">- Verified Buyer</div>
    </div>
    <div class="testimonial">
      <div class="quote-mark">&ldquo;</div>
      <p>Premium quality and stylish design. Worth every penny.</p>
      <div class="author">ADRIAN P.</div>
      <div class="verified">- Verified Buyer</div>
    </div>
    <div class="testimonial">
      <div class="quote-mark">&ldquo;</div>
      <p>Fast delivery and outstanding performance. My new go-to brand.</p>
      <div class="author">MIGUEL R.</div>
      <div class="verified">- Verified Buyer</div>
    </div>
  </div>

 <section class="why-choose container">
  <div class="title-row">
    <h2>WHY CHOOSE STRIKE ELITE?</h2>
    <div class="divider"></div>
  </div>

  <div class="why-grid">
    <div class="why-item">
      <div class="why-icon">
        <img src="<?php echo $bp ?>assets/images/icons/performance.png" alt="Elite Performance">
      </div>
      <div class="why-text">
        <h4>ELITE PERFORMANCE</h4>
        <p>Precision engineered gear for peak performance.</p>
      </div>
    </div>

    <div class="why-item">
      <div class="why-icon">
        <img src="<?php echo $bp ?>assets/images/icons/materials.png" alt="Premium Materials">
      </div>
      <div class="why-text">
        <h4>PREMIUM MATERIALS</h4>
        <p>High-quality materials for durability and comfort.</p>
      </div>
    </div>

    <div class="why-item">
      <div class="why-icon">
        <img src="<?php echo $bp ?>assets/images/icons/athletes.png" alt="Trusted By Athletes">
      </div>
      <div class="why-text">
        <h4>TRUSTED BY ATHLETES</h4>
        <p>Chosen by players who demand the best.</p>
      </div>
    </div>

    <div class="why-item">
      <div class="why-icon">
        <img src="<?php echo $bp ?>assets/images/icons/shipping.png" alt="Fast Shipping">
      </div>
      <div class="why-text">
        <h4>FAST SHIPPING</h4>
        <p>Quick and reliable delivery to your doorstep.</p>
      </div>
    </div>
  </div>
</section>

  <section class="about-section container">
  <div class="about-image-col">
    <img src="<?php echo $bp ?>assets/images/team-huddle.jpg" alt="Strike Elite Athletes">
  </div>

  <div class="about-text-col">
    <h3>ABOUT <span class="accent">STRIKE ELITE</span></h3>
    <p>Born from a passion for the beautiful game, Strike Elite is dedicated to delivering high-performance gear that empowers players to play with confidence and win with pride.</p>
    <p>We combine cutting-edge technology, premium materials, and athlete-driven design to help you perform at your best.</p>
  </div>

  <div class="about-pillars-col">
    <div class="pillar-item">
      <img src="<?php echo $bp ?>assets/images/icons/mission.png" alt="Our Mission" class="pillar-icon">
      <div class="pillar-content">
        <h5>OUR MISSION</h5>
        <p>To inspire and equip every player to reach their full potential.</p>
      </div>
    </div>

    <div class="pillar-item">
      <img src="<?php echo $bp ?>assets/images/icons/vision.png" alt="Our Vision" class="pillar-icon">
      <div class="pillar-content">
        <h5>OUR VISION</h5>
        <p>To be the most trusted soccer brand worldwide.</p>
      </div>
    </div>

    <div class="pillar-item">
      <img src="<?php echo $bp ?>assets/images/icons/promise.png" alt="Our Promise" class="pillar-icon">
      <div class="pillar-content">
        <h5>OUR PROMISE</h5>
        <p>Quality you can trust. Performance you can feel.</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
