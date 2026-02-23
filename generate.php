<?php
require_once __DIR__ . '/functions.php';

$ip = client_ip();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_claim'])) {
    if (is_proxy_or_vpn()) {
        $_SESSION['flash_message'] = 'VPN/Proxy detected. Disable it and try again.';
        header('Location: index.php');
        exit;
    }

    if (has_claimed_today($mysqli, $ip)) {
        $_SESSION['flash_message'] = 'You already claimed an account today.';
        header('Location: index.php');
        exit;
    }

    $captchaResponse = $_POST['cf-turnstile-response'] ?? '';
    if (!verify_turnstile($captchaResponse)) {
        $_SESSION['flash_message'] = 'Cloudflare Turnstile verification failed.';
        header('Location: index.php');
        exit;
    }

    log_ip_attempt($mysqli, $ip);
    $token = create_claim_token($ip);
    $_SESSION['claim_token'] = $token;

    $target = str_replace('{TOKEN}', urlencode($token), SHORTLINK_URL);
    header('Location: ' . $target);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'], $_GET['ad_verified'])) {
    $token = $_GET['token'];
    $adVerified = $_GET['ad_verified'] === '1';

    if (!$adVerified || !isset($_SESSION['claim_token']) || !hash_equals($_SESSION['claim_token'], $token)) {
        http_response_code(403);
        die('Invalid ad verification state.');
    }

    if (!verify_claim_token($token, $ip)) {
        http_response_code(403);
        die('Invalid or expired token.');
    }

    if (has_claimed_today($mysqli, $ip)) {
        $_SESSION['flash_message'] = 'You already claimed an account today.';
        header('Location: index.php');
        exit;
    }

    $account = pick_account($mysqli);
    if (!$account) {
        $_SESSION['claim_result'] = ['out_of_stock' => true];
        unset($_SESSION['claim_token']);
        header('Location: success.php');
        exit;
    }

    if (!finalize_claim($mysqli, $account, $ip)) {
        $_SESSION['flash_message'] = 'Unable to finalize claim. Please retry.';
        header('Location: index.php');
        exit;
    }

    $_SESSION['claim_result'] = [
        'out_of_stock' => false,
        'account' => $account,
        'ip' => $ip,
    ];
    unset($_SESSION['claim_token']);
    header('Location: success.php');
    exit;
}

header('Location: index.php');
exit;
