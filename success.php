<?php
require_once __DIR__ . '/functions.php';

if (empty($_SESSION['claim_result'])) {
    http_response_code(403);
    die('Direct access denied.');
}

$result = $_SESSION['claim_result'];
unset($_SESSION['claim_result']);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Claim Result</title>
</head>
<body>
    <h1>Claim Result</h1>
    <?php if (!empty($result['out_of_stock'])): ?>
        <p style="color:red;">Out of Stock</p>
    <?php else: ?>
        <p style="color:green;">Account claimed successfully.</p>
        <ul>
            <li>Username: <strong><?= e($result['account']['username']) ?></strong></li>
            <li>Password: <strong><?= e($result['account']['password']) ?></strong></li>
            <li>Type: <strong><?= e($result['account']['type']) ?></strong></li>
            <li>Your IP: <strong><?= e($result['ip']) ?></strong></li>
        </ul>
    <?php endif; ?>

    <p><a href="index.php">Back to Home</a></p>
</body>
</html>
