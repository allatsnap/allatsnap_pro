<?php
require_once __DIR__ . '/functions.php';
$message = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Claim Account</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <h1>Claim Service Account</h1>
    <?php if ($message): ?>
        <p style="color:red;"><?= e($message) ?></p>
    <?php endif; ?>

    <?php if (!empty($_SESSION['user_name'])): ?>
        <p>Signed in as <strong><?= e($_SESSION['user_name']) ?></strong> | <a href="logout.php">Logout</a></p>
    <?php else: ?>
        <p><a href="login.php">Optional Login</a></p>
    <?php endif; ?>

    <form action="generate.php" method="post">
        <p>Complete reCAPTCHA to continue.</p>
        <div class="g-recaptcha" data-sitekey="<?= e(RECAPTCHA_SITE_KEY) ?>"></div>
        <br>
        <button type="submit" name="start_claim" value="1">Start Claim</button>
    </form>
</body>
</html>
