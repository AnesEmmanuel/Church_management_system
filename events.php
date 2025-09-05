<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$msg=''; $err='';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_event'])) {
    $title = trim($_POST['event_name'] ?? '');
    $desc = trim($_POST['event_description'] ?? '');
    $date = trim($_POST['event_date'] ?? '');

    if ($title==='' || $date==='') $err = "Title and date required.";
    else {
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_description, event_date) VALUES (?,?,?)");
        $stmt->bind_param("sss", $title,$desc,$date);
        if ($stmt->execute()) {
            // create message for leaders
            $content = "New event: $title on $date. Details: $desc";
            $stmt2 = $conn->prepare("INSERT INTO messages (recipient, title, content) VALUES (?,?,?)");
            $recipient = 'leaders';
            $t = "New Event: $title";
            $stmt2->bind_param("sss",$recipient,$t,$content);
            $stmt2->execute();
            $stmt2->close();
            $msg = "Event added and leaders notified (message stored).";
        } else $err = "DB error: ".$conn->error;
        $stmt->close();
    }
}

$res = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Events</title><link rel="stylesheet" href="cs/style.css"></head>
<body>
<div class="container">
  <h1>Events</h1>
  <p><a href="admin.php">Back</a></p>
  <?php if($msg) echo '<p style="color:green">'.htmlspecialchars($msg).'</p>'; ?>
  <?php if($err) echo '<p style="color:red">'.htmlspecialchars($err).'</p>'; ?>

  <form method="post">
    <input name="event_name" placeholder="Event title" required>
    <textarea name="event_description" placeholder="Description"></textarea>
    <input name="event_date" type="date" required>
    <button name="add_event" type="submit">Add Event</button>
  </form>

  <h2>Events List</h2>
  <ul>
  <?php while($r = $res->fetch_assoc()): ?>
    <li class="item"><strong><?php echo htmlspecialchars($r['event_name']); ?></strong> — <?php echo htmlspecialchars($r['event_date']); ?><div class="small"><?php echo htmlspecialchars($r['event_description']); ?></div></li>
  <?php endwhile; ?>
  </ul>
</div>

</body>
</html>
