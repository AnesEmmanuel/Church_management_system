<?php
session_start();
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Enter username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();

            // verify password securely
            if (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = $row['username'];
                $_SESSION['user_id'] = $row['id'];
                header("Location: admin.php");
                exit;
            } else {
                $error = "Invalid credentials.";
            }
        } else {
            $error = "Invalid credentials.";
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Church CMS - Login</title>
  <style>
    body{font-family:Arial;margin:0;background:#f4f7f9}
    .wrap{max-width:420px;margin:60px auto;padding:20px;background:#fff;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.08)}
    input{width:100%;padding:10px;margin:8px 0;border:1px solid #ddd;border-radius:4px}
    button{padding:10px 14px;background:#0b6efd;color:#fff;border:none;border-radius:4px;cursor:pointer}
    .err{color:#b00020}
  </style>
</head>
<body>
  <div class="wrap">
    <h2>Church CMS — Admin Login</h2>
    <?php if ($error): ?><p class="err"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <form method="post" action="">
      <input name="username" placeholder="Username" required>
      <input name="password" type="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
