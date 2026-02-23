<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$stats = [
    'total_accounts' => 0,
    'private_used' => 0,
    'private_unused' => 0,
    'shared_accounts' => 0,
    'total_claims' => 0,
];

$res = $mysqli->query('SELECT COUNT(*) AS c FROM accounts');
$stats['total_accounts'] = (int)$res->fetch_assoc()['c'];

$res = $mysqli->query("SELECT COUNT(*) AS c FROM accounts WHERE type='private' AND status='used'");
$stats['private_used'] = (int)$res->fetch_assoc()['c'];

$res = $mysqli->query("SELECT COUNT(*) AS c FROM accounts WHERE type='private' AND status='unused'");
$stats['private_unused'] = (int)$res->fetch_assoc()['c'];

$res = $mysqli->query("SELECT COUNT(*) AS c FROM accounts WHERE type='shared'");
$stats['shared_accounts'] = (int)$res->fetch_assoc()['c'];

$res = $mysqli->query('SELECT COUNT(*) AS c FROM claims');
$stats['total_claims'] = (int)$res->fetch_assoc()['c'];
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>Admin Dashboard</title></head>
<body>
<h1>Admin Dashboard</h1>
<p>Welcome, <?= e($_SESSION['admin_username'] ?? 'admin') ?></p>
<ul>
    <li>Total accounts: <?= $stats['total_accounts'] ?></li>
    <li>Private used: <?= $stats['private_used'] ?></li>
    <li>Private unused: <?= $stats['private_unused'] ?></li>
    <li>Shared accounts: <?= $stats['shared_accounts'] ?></li>
    <li>Total claims: <?= $stats['total_claims'] ?></li>
</ul>

<p>
    <a href="add_account.php">Add Account</a> |
    <a href="manage_accounts.php">Manage Accounts</a> |
    <a href="view_claims.php">Claim Logs</a> |
    <a href="logout.php">Logout</a>
</p>
</body>
</html>
