<?php
    $basePath = '../';
    require_once __DIR__ . '/../includes/functions.php';
    $pageTitle = 'Shipping and Delivery';
    require_once __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <div class="static-page">
    <h1>Shipping and Delivery</h1>
    <p>We want your Strike Elite gear on your doorstep as fast as possible. Here's what to expect once you place an order.</p>

    <h3>Processing Time</h3>
    <p>Orders are processed within 1–2 business days. You'll receive an email once your order status changes to "processing" in your account.</p>

    <h3>Delivery Timeframes</h3>
    <table>
      <tr><th>Area</th><th>Estimated Delivery</th></tr>
      <tr><td>Metro Manila</td><td>2–3 business days</td></tr>
      <tr><td>Luzon (outside Metro Manila)</td><td>3–5 business days</td></tr>
      <tr><td>Visayas &amp; Mindanao</td><td>5–7 business days</td></tr>
    </table>

    <h3>Shipping Fees</h3>
    <p>Shipping cost is calculated at checkout based on your delivery address and order weight. Orders over ₱3,000 qualify for free standard shipping.</p>

    <h3>Tracking Your Order</h3>
    <p>Once your order ships, you can view its status any time from <a href="../account.php" style="color:var(--gold)">My Account → Order History</a>.</p>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
