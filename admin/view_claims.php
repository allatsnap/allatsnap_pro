<?php
require_once __DIR__ . '/../functions.php';
require_admin();

$sql = 'SELECT c.id, c.ip_address, c.claimed_at, a.username, a.type
        FROM claims c
        LEFT JOIN accounts a ON a.id = c.account_id
        ORDER BY c.id DESC';
$claims = $mysqli->query($sql);
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>Claim Logs</title></head>
<body>
<h1>Claim Logs</h1>
<p><a href="dashboard.php">Back to Dashboard</a></p>
<table border="1" cellpadding="6">
    <tr><th>ID</th><th>IP</th><th>Account</th><th>Type</th><th>Date</th></tr>
    <?php while ($row = $claims->fetch_assoc()): ?>
    <tr>
        <td><?= (int)$row['id'] ?></td>
        <td><?= e($row['ip_address']) ?></td>
        <td><?= e($row['username'] ?? 'N/A') ?></td>
        <td><?= e($row['type'] ?? 'N/A') ?></td>
        <td><?= e($row['claimed_at']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
