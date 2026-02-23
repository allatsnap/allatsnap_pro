<?php
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $error = 'Name is required.';
    } else {
        $_SESSION['user_name'] = $name;
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><title>User Login</title></head>
<body>
<h1>Optional Login</h1>
<?php if (!empty($error)): ?><p style="color:red;"><?= e($error) ?></p><?php endif; ?>
<form method="post">
    <label>Name <input type="text" name="name"></label>
    <button type="submit">Save Session</button>
</form>
<p><a href="index.php">Back</a></p>
</body>
</html>
