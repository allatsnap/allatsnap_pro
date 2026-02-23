<?php
require_once __DIR__ . '/../functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $type = $_POST['type'] ?? 'private';

    if ($username === '' || $password === '' || !in_array($type, ['private', 'shared'], true)) {
        $error = 'All fields are required and type must be valid.';
    } else {
        $status = $type === 'private' ? 'unused' : 'unused';
        $stmt = $mysqli->prepare('INSERT INTO accounts (username, password, type, status, created_at) VALUES (?, ?, ?, ?, UTC_TIMESTAMP())');
        $stmt->bind_param('ssss', $username, $password, $type, $status);
        $stmt->execute();
        $stmt->close();
        $success = 'Account added.';
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>Add Account</title></head>
<body>
<h1>Add Account</h1>
<?php if (!empty($error)): ?><p style="color:red;"><?= e($error) ?></p><?php endif; ?>
<?php if (!empty($success)): ?><p style="color:green;"><?= e($success) ?></p><?php endif; ?>
<form method="post">
    <label>Username <input type="text" name="username" required></label><br><br>
    <label>Password <input type="text" name="password" required></label><br><br>
    <label>Type
        <select name="type">
            <option value="private">private</option>
            <option value="shared">shared</option>
        </select>
    </label><br><br>
    <button type="submit">Add</button>
</form>
<p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
