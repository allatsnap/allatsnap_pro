<?php
require_once __DIR__ . '/../functions.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare('SELECT id, password FROM admins WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($id, $hash);

    if ($stmt->fetch() && password_verify($password, $hash)) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $id;
        $_SESSION['admin_username'] = $username;
        $stmt->close();
        header('Location: dashboard.php');
        exit;
    }
    $stmt->close();
    $error = 'Invalid credentials.';
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>Admin Login</title></head>
<body>
<h1>Admin Login</h1>
<?php if (!empty($error)): ?><p style="color:red;"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <label>Username <input type="text" name="username" required></label><br><br>
    <label>Password <input type="password" name="password" required></label><br><br>
    <button type="submit">Login</button>
</form>
</body>
</html>
