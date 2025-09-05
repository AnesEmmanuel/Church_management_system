<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'db.php';

if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }

$username = $_SESSION['user'];

// Fetch user info
$stmt = $conn->prepare("SELECT id, name, profile_pic FROM leaders WHERE name=? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$sys_name = "Church Management System";
$res = $conn->query("SELECT value FROM settings WHERE name='system_name' LIMIT 1");
if ($res && $row = $res->fetch_assoc()) { $sys_name = $row['value']; }

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update name
    if (!empty($_POST['new_name'])) {
        $new_name = $_POST['new_name'];
        $stmt = $conn->prepare("UPDATE leaders SET name=? WHERE id=?");
        $stmt->bind_param("si", $new_name, $user['id']);
        $stmt->execute();
        $_SESSION['user'] = $new_name;
        $stmt->close();
    }

    // Update password
    if (!empty($_POST['new_pass'])) {
        $hashed = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE leaders SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $user['id']);
        $stmt->execute();
        $stmt->close();
    }

    // Upload profile picture
    if (!empty($_FILES['profile_pic']['name'])) {
        $target = "uploads/" . time() . "_" . basename($_FILES['profile_pic']['name']);
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
            $stmt = $conn->prepare("UPDATE leaders SET profile_pic=? WHERE id=?");
            $stmt->bind_param("si", $target, $user['id']);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Update system name
    if (!empty($_POST['system_name'])) {
        $new_sys = $_POST['system_name'];
        $stmt = $conn->prepare("INSERT INTO settings (name, value) VALUES ('system_name', ?) 
                                ON DUPLICATE KEY UPDATE value=VALUES(value)");
        $stmt->bind_param("s", $new_sys);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: settings.php?updated=1");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>⚙️ Settings</title>
<style>
body { font-family: Arial; margin:20px; background:#f7f7f7; }
form { background:#fff; padding:20px; border-radius:8px; max-width:500px; margin:auto; box-shadow:0 2px 6px rgba(0,0,0,.2); }
form h2 { margin-bottom:15px; }
input[type=text], input[type=password], input[type=file] {
    width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:6px;
}
button { background:#2c3e50; color:#fff; border:none; padding:10px 16px; border-radius:6px; cursor:pointer; }
button:hover { background:#34495e; }
.success { color:green; margin-bottom:10px; }
</style>
</head>
<body>

<form method="post" enctype="multipart/form-data">
  <h2>Update Profile</h2>
  <?php if (!empty($_GET['updated'])): ?><p class="success">✅ Updated successfully!</p><?php endif; ?>
  
  <label>Change Name:</label>
  <input type="text" name="new_name" value="<?php echo htmlspecialchars($user['name']); ?>">

  <label>Change Password:</label>
  <input type="password" name="new_pass" placeholder="Enter new password">

  <label>Upload Profile Picture:</label>
  <input type="file" name="profile_pic">

  <label>System Name:</label>
  <input type="text" name="system_name" value="<?php echo htmlspecialchars($sys_name); ?>">

  <button type="submit">Save Changes</button>
</form>

</body>
</html>
