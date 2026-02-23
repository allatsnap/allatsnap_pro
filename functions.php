<?php
require_once __DIR__ . '/config.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function client_ip(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(explode(',', $_SERVER[$key])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return '0.0.0.0';
}

function is_proxy_or_vpn(): bool
{
    $suspiciousHeaders = [
        'HTTP_VIA',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'HTTP_PROXY_CONNECTION',
        'HTTP_X_PROXY_ID',
        'HTTP_CLIENT_IP',
    ];

    foreach ($suspiciousHeaders as $header) {
        if (!empty($_SERVER[$header])) {
            return true;
        }
    }

    $ip = client_ip();
    if (in_array($ip, ['127.0.0.1', '::1'], true)) {
        return false;
    }

    if (preg_match('/bot|crawl|spider|scanner|python|curl/i', $_SERVER['HTTP_USER_AGENT'] ?? '')) {
        return true;
    }

    return false;
}

function verify_turnstile(string $captchaResponse): bool
{
    if ($captchaResponse === '') {
        return false;
    }

    $postData = http_build_query([
        'secret' => TURNSTILE_SECRET_KEY,
        'response' => $captchaResponse,
        'remoteip' => client_ip(),
    ]);

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return false;
    }

    $result = json_decode($response, true);
    return !empty($result['success']) && empty($result['error-codes']);
}

function create_claim_token(string $ip): string
{
    $payload = [
        'ip' => $ip,
        'exp' => time() + 900,
        'nonce' => bin2hex(random_bytes(16)),
    ];

    $json = json_encode($payload);
    $base = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
    $sig = hash_hmac('sha256', $base, TOKEN_SECRET);

    return $base . '.' . $sig;
}

function verify_claim_token(string $token, string $ip): ?array
{
    $parts = explode('.', $token);
    if (count($parts) !== 2) {
        return null;
    }

    [$base, $sig] = $parts;
    $expectedSig = hash_hmac('sha256', $base, TOKEN_SECRET);
    if (!hash_equals($expectedSig, $sig)) {
        return null;
    }

    $json = base64_decode(strtr($base, '-_', '+/'));
    $payload = json_decode($json, true);
    if (!$payload || empty($payload['ip']) || empty($payload['exp'])) {
        return null;
    }

    if ((int)$payload['exp'] < time()) {
        return null;
    }

    if ($payload['ip'] !== $ip) {
        return null;
    }

    return $payload;
}

function has_claimed_today(mysqli $db, string $ip): bool
{
    $sql = 'SELECT COUNT(*) FROM claims WHERE ip_address = ? AND DATE(claimed_at) = UTC_DATE()';
    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return (int)$count > 0;
}

function log_ip_attempt(mysqli $db, string $ip): void
{
    $stmt = $db->prepare('INSERT INTO ip_logs (ip_address, created_at) VALUES (?, UTC_TIMESTAMP())');
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $stmt->close();
}

function pick_account(mysqli $db): ?array
{
    // shared has highest priority
    $sharedSql = "SELECT id, username, password, type, status FROM accounts WHERE type = 'shared' ORDER BY id ASC LIMIT 1";
    $res = $db->query($sharedSql);
    if ($res && $res->num_rows > 0) {
        $account = $res->fetch_assoc();
        $res->free();
        return $account;
    }

    $privateSql = "SELECT id, username, password, type, status FROM accounts WHERE type = 'private' AND status = 'unused' ORDER BY id ASC LIMIT 1";
    $res = $db->query($privateSql);
    if ($res && $res->num_rows > 0) {
        $account = $res->fetch_assoc();
        $res->free();
        return $account;
    }

    return null;
}

function finalize_claim(mysqli $db, array $account, string $ip): bool
{
    $db->begin_transaction();
    try {
        if ($account['type'] === 'private') {
            $stmt = $db->prepare("UPDATE accounts SET status = 'used' WHERE id = ? AND status = 'unused'");
            $id = (int)$account['id'];
            $stmt->bind_param('i', $id);
            $stmt->execute();
            if ($stmt->affected_rows !== 1) {
                $stmt->close();
                throw new RuntimeException('Private account was already used.');
            }
            $stmt->close();
        }

        $stmt = $db->prepare('INSERT INTO claims (account_id, ip_address, claimed_at) VALUES (?, ?, UTC_TIMESTAMP())');
        $id = (int)$account['id'];
        $stmt->bind_param('is', $id, $ip);
        $stmt->execute();
        $stmt->close();

        $db->commit();
        return true;
    } catch (Throwable $e) {
        $db->rollback();
        return false;
    }
}

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: /admin/login.php');
        exit;
    }
}
