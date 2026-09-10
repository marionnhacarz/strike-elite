<?php
$basePath = '../';
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Contact Us';

$errors = [];
$name = $email = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name)) $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
    if (empty($message)) $errors[] = 'Please enter a message.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->execute([htmlspecialchars($name), htmlspecialchars($email), htmlspecialchars($message)]);
        flash('flash_success', "Thanks $name — we've received your message and will reply within 1-2 business days.");
        header('Location: contact.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="container">
  <div class="static-page">
    <h1>Contact Us</h1>
    <p>Questions about an order, sizing, or anything else? Send us a message.</p>
  </div>

  <div class="form-page">
    <?php foreach ($errors as $e): ?><div class="form-error"><?= h($e) ?></div><?php endforeach; ?>
    <form method="POST">
      <div class="field"><label>Name</label><input type="text" name="name" value="<?= h($name) ?>" required></div>
      <div class="field"><label>Email</label><input type="email" name="email" value="<?= h($email) ?>" required></div>
      <div class="field"><label>Message</label><textarea name="message" required><?= h($message) ?></textarea></div>
      <button type="submit" class="btn btn-primary btn-block">Send Message</button>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
