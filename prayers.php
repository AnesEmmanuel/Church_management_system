<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$msg=''; $err='';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_prayer'])) {
    $prayer = trim($_POST['prayer_text'] ?? '');
    $day = trim($_POST['day_of_week'] ?? '');
    if ($prayer==='' || $day==='') $err = "Prayer text and day required.";
    else {
        $stmt = $conn->prepare("INSERT INTO prayers (prayer_text, day_of_week) VALUES (?,?)");
        $stmt->bind_param("ss",$prayer,$day);
        if ($stmt->execute()) $msg = "Prayer saved.";
        else $err = "DB error: ".$conn->error;
        $stmt->close();
    }
}
$res = $conn->query("SELECT * FROM prayers ORDER BY day_of_week");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Prayers</title><link rel="stylesheet" href="cs/style.css"></head>
<body>
<div class="container">
  <h1>Prayers & Sunday Mass</h1>
  <p><a href="admin.php">Back to dashboard</a></p>
  <?php if($msg) echo '<p style="color:green">'.htmlspecialchars($msg).'</p>'; ?>
  <?php if($err) echo '<p style="color:red">'.htmlspecialchars($err).'</p>'; ?>

  <form method="post">
    <textarea name="prayer_text" placeholder="Prayer text" required></textarea>
    <select name="day_of_week" required>
      <option value="">Select day</option>
      <option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option>
      <option>Friday</option><option>Saturday</option><option>Sunday</option>
    </select>
    <button name="add_prayer" type="submit">Save Prayer</button>
  </form>

  <h2>Saved Prayers</h2>
  <ul>
  <?php while($r = $res->fetch_assoc()): ?>
    <li class="item"><strong><?php echo htmlspecialchars($r['day_of_week']); ?></strong> — <?php echo nl2br(htmlspecialchars($r['prayer_text'])); ?></li>
  <?php endwhile; ?>
  </ul>
</div>
</body>
</html>
