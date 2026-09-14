<?php
$basePath = '';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Login';

if (is_logged_in()) {

    if (is_admin()) {
        header('Location: admin/index.php');
    } else {
        header('Location: account.php');
    }

    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = 'Please enter both email and password.';
    } else {

        $stmt = $pdo->prepare("
            SELECT
                id,
                username,
                email,
                password_hash,
                role
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {

    session_regenerate_id(true);

    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email']    = $user['email'];
    $_SESSION['role']     = $user['role'];

    flash(
        'flash_success',
        'Welcome back, ' . $user['username'] . '!'
    );
    if (
    $user['role'] === 'client' &&
    !empty($_SESSION['after_login'])
) {
    $redirect = $_SESSION['after_login'];

    unset($_SESSION['after_login']);

    header('Location: ' . $redirect);
    exit;
}

    if ($user['role'] === 'admin') {

        header('Location: admin/index.php');

    } else {

        header('Location: account.php');
    }

    exit;

} else {

    $errors[] = 'Incorrect email or password.';
}
}
}

    require_once __DIR__ . '/includes/header.php';

?>

<div class="form-page">
  <h2>Login</h2>
  <p class="sub">Log in to your Strike Elite account.</p>

  <?php foreach ($errors as $e): ?><div class="form-error"><?= h($e) ?></div><?php endforeach; ?>

  <form method="POST">
    <div class="field"><label>Email</label><input type="email" name="email" value="<?= h($email) ?>" required></div>
    <div class="field"><label>Password</label><input type="password" name="password" required></div>
    <button type="submit" class="btn btn-primary btn-block">Log In</button>
  </form>
  <p class="form-footnote">Don't have an account? <a href="register.php" style="color:var(--gold)">Create one</a></p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>