<?php
require_once __DIR__ . '/../functions.php';
require_admin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $mysqli->prepare('DELETE FROM accounts WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: manage_accounts.php');
    exit;
}

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $mysqli->prepare('SELECT type FROM accounts WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($type);
    if ($stmt->fetch()) {
        $newType = $type === 'private' ? 'shared' : 'private';
    }
    $stmt->close();

    if (!empty($newType)) {
        $newStatus = $newType === 'private' ? 'unused' : 'unused';
        $upd = $mysqli->prepare('UPDATE accounts SET type = ?, status = ? WHERE id = ?');
        $upd->bind_param('ssi', $newType, $newStatus, $id);
        $upd->execute();
        $upd->close();
    }
    header('Location: manage_accounts.php');
    exit;
}

$privateAccounts = $mysqli->query("SELECT * FROM accounts WHERE type='private' ORDER BY id DESC");
$sharedAccounts = $mysqli->query("SELECT * FROM accounts WHERE type='shared' ORDER BY id DESC");
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>Manage Accounts</title></head>
<body>
<h1>Manage Accounts</h1>
<p><a href="dashboard.php">Back to Dashboard</a></p>

<h2>Private Accounts (used & unused)</h2>
<table border="1" cellpadding="6">
    <tr><th>ID</th><th>Username</th><th>Password</th><th>Status</th><th>Created</th><th>Actions</th></tr>
    <?php while ($row = $privateAccounts->fetch_assoc()): ?>
    <tr>
        <td><?= (int)$row['id'] ?></td>
        <td><?= e($row['username']) ?></td>
        <td><?= e($row['password']) ?></td>
        <td><?= e($row['status']) ?></td>
        <td><?= e($row['created_at']) ?></td>
        <td>
            <a href="?toggle=<?= (int)$row['id'] ?>">Toggle Type</a> |
            <a href="?delete=<?= (int)$row['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<h2>Shared Accounts</h2>
<table border="1" cellpadding="6">
    <tr><th>ID</th><th>Username</th><th>Password</th><th>Created</th><th>Actions</th></tr>
    <?php while ($row = $sharedAccounts->fetch_assoc()): ?>
    <tr>
        <td><?= (int)$row['id'] ?></td>
        <td><?= e($row['username']) ?></td>
        <td><?= e($row['password']) ?></td>
        <td><?= e($row['created_at']) ?></td>
        <td>
            <a href="?toggle=<?= (int)$row['id'] ?>">Toggle Type</a> |
            <a href="?delete=<?= (int)$row['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
