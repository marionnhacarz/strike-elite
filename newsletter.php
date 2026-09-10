<?php
require_once __DIR__ . '/includes/functions.php';

$email = trim($_POST['email'] ?? '');

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    try {
        $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
        $stmt->execute([$email]);
        flash('flash_success', 'Thanks for subscribing!');
    } catch (PDOException $e) {
        // probably already subscribed (email has a unique constraint), treat it as a success either way
        flash('flash_success', 'You are already on the list — thanks!');
    }
} else {
    flash('flash_error', 'Please enter a valid email address.');
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
