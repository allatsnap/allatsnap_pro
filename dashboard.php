<?php
require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>User Dashboard</title></head>
<body>
<h1>User Dashboard</h1>
<?php if (!empty($_SESSION['user_name'])): ?>
    <p>Welcome, <?= e($_SESSION['user_name']) ?>.</p>
<?php else: ?>
    <p>You are browsing as guest.</p>
<?php endif; ?>
<p><a href="index.php">Go to Claim Page</a></p>
</body>
</html>
