<?php
$basePath = '';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Create Account';

if (is_logged_in()) {

    if (($_SESSION['role'] ?? 'client') === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: account.php');
    }

    exit;
}

$errors = [];
$username = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (strlen($username) < 3) $errors[] = 'Username must be at least 3 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            $errors[] = 'That username or email is already registered.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
    "INSERT INTO users (username, email, password_hash, role)
     VALUES (?, ?, ?, 'client')"
);

$stmt->execute([$username, $email, $hash]);

        session_regenerate_id(true);

$_SESSION['user_id']  = $pdo->lastInsertId();
$_SESSION['username'] = $username;
$_SESSION['email']    = $email;
$_SESSION['role']     = 'client';

        flash('flash_success', 'Welcome to Strike Elite, ' . $username . '!');
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<div class="form-page">
  <h2>Create Account</h2>
  <p class="sub">Join the Strike Elite team.</p>

  <?php foreach ($errors as $e): ?><div class="form-error"><?= h($e) ?></div><?php endforeach; ?>

  <form method="POST">
    <div class="field"><label>Username</label><input type="text" name="username" value="<?= h($username) ?>" required></div>
    <div class="field"><label>Email</label><input type="email" name="email" value="<?= h($email) ?>" required></div>
    <div class="field"><label>Password</label><input type="password" name="password" required></div>
    <div class="field"><label>Confirm Password</label><input type="password" name="confirm_password" required></div>
    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
  </form>
  <p class="form-footnote">Already have an account? <a href="login.php" style="color:var(--gold)">Log in</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
