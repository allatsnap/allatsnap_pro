<?php
require_once __DIR__ . '/functions.php';

$token = $_GET['token'] ?? '';
if (!$token || !verify_claim_token($token, client_ip())) {
    http_response_code(403);
    die('Invalid or expired claim token.');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ad Verification</title>
</head>
<body>
    <h1>Ad Verification</h1>
    <p>This page simulates your shortlink/ad provider callback flow.</p>
    <p>After user completes ad steps, provider should redirect to:</p>
    <code><?= e(BASE_URL . '/generate.php?token=' . urlencode($token) . '&ad_verified=1') ?></code>

    <form action="generate.php" method="get" style="margin-top:20px;">
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <input type="hidden" name="ad_verified" value="1">
        <button type="submit">I Completed the Ad</button>
    </form>
</body>
</html>
