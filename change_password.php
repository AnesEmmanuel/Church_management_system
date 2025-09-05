<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password === '' || $confirm_password === '') {
        $error = "Fill in all fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $success = "Password changed successfully. Please log in again.";
            session_destroy();
            header("Refresh:2; url=login.php"); // redirect after 2 seconds
        } else {
            $error = "Error updating password.";
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Change Password</title>
  <style>
    body{font-family:Arial;margin:0;background:#f4f7f9}
    .wrap{max-width:420px;margin:60px auto;padding:20px;background:#fff;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.08)}
    input{width:100%;padding:10px;margin:8px 0;border:1px solid #ddd;border-radius:4px}
    button{padding:10px 14px;background:#0b6efd;color:#fff;border:none;border-radius:4px;cursor:pointer}
    .err{color:#b00020}
    .success{color:green}
  </style>
</head>
<body>
  <div class="wrap">
    <h2>Change Password</h2>
    <?php if ($error): ?><p class="err"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>
    <form method="post" action="">
      <input type="password" name="new_password" placeholder="New Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
      <button type="submit">Update Password</button>
    </form>
  </div>
</body>
</html>
