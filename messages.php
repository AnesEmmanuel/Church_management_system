<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$msg=''; $err='';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['send_message'])) {
    $recipient = trim($_POST['recipient'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($recipient==='' || $title==='' || $content==='') {
        $err = "All fields are required.";
    } else {
        if ($recipient === "leaders") {
            $leaders = $conn->query("SELECT name FROM leaders");
            while ($l = $leaders->fetch_assoc()) {
                $stmt = $conn->prepare("INSERT INTO messages (recipient, title, content) VALUES (?,?,?)");
                $stmt->bind_param("sss",$l['name'],$title,$content);
                $stmt->execute();
                $stmt->close();
            }
            $msg = "Message stored for all leaders.";
        }
        elseif ($recipient === "members") {
            $stmt = $conn->prepare("INSERT INTO messages (recipient, title, content) VALUES (?,?,?)");
            $stmt->bind_param("sss",$recipient,$title,$content);
            $stmt->execute();
            $stmt->close();
            $msg = "Message stored for all members.";
        }
        elseif ($recipient === "ALL") {
            $stmt = $conn->prepare("INSERT INTO messages (recipient, title, content) VALUES (?,?,?)");
            $stmt->bind_param("sss",$recipient,$title,$content);
            $stmt->execute();
            $stmt->close();
            $msg = "Message stored for all users.";
        }
        else {
            $stmt = $conn->prepare("INSERT INTO messages (recipient, title, content) VALUES (?,?,?)");
            $stmt->bind_param("sss",$recipient,$title,$content);
            if ($stmt->execute()) $msg = "Message stored for " . htmlspecialchars($recipient);
            else $err = "DB error: " . $conn->error;
            $stmt->close();
        }
    }
}

$leaders_res = $conn->query("SELECT name FROM leaders ORDER BY name");
$res = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Messages</title>
  <link rel="stylesheet" href="cs/style.css">
  <style>
    body { font-family: Arial, sans-serif; background:#f4f7f9; margin:0; }
    .container { max-width: 1000px; margin:20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
    h1,h2 { margin-top:0; }
    form { margin-bottom:20px; }
    form select, form input, form textarea { width:100%; padding:8px; margin:8px 0; border:1px solid #ccc; border-radius:4px; }
    button { background:#007bff; color:#fff; padding:10px 16px; border:none; border-radius:4px; cursor:pointer; }
    button:hover { background:#0056b3; }
    .msg-success { color:green; margin:10px 0; }
    .msg-error { color:red; margin:10px 0; }
    table { width:100%; border-collapse:collapse; margin-top:15px; }
    table th, table td { border:1px solid #ccc; padding:8px; text-align:left; }
    table th { background:#f0f0f0; }
    .small { color:#555; font-size:0.9em; }
    #logSection { display:none; margin-top:20px; }
    #toggleLog { margin-top:10px; background:#28a745; }
    #toggleLog:hover { background:#1e7e34; }
  </style>
</head>
<body>
<div class="container">
  <h1>Messages</h1>
  <p><a href="admin.php">← Back</a></p>

  <?php if($msg): ?><p class="msg-success"><?php echo $msg; ?></p><?php endif; ?>
  <?php if($err): ?><p class="msg-error"><?php echo $err; ?></p><?php endif; ?>

  <form method="post">
    <label>Recipient</label>
    <select name="recipient" required>
      <option value="">Select recipient</option>
      <option value="leaders">All Leaders</option>
      <option value="members">All Members</option>
      <option value="ALL">All</option>
      <?php while($l = $leaders_res->fetch_assoc()): ?>
        <option value="<?php echo htmlspecialchars($l['name']); ?>"><?php echo htmlspecialchars($l['name']); ?></option>
      <?php endwhile; ?>
    </select>

    <label>Title</label>
    <input type="text" name="title" placeholder="Message title" required>

    <label>Content</label>
    <textarea name="content" placeholder="Message content" rows="5" required></textarea>

    <button name="send_message" type="submit">Send Message</button>
  </form>

  <!-- Toggle button -->
  <button id="toggleLog">Show Message Log</button>

  <!-- Message Log (hidden by default) -->
  <div id="logSection">
    <h2>Message Log</h2>
    <table>
      <tr>
        <th>Title</th>
        <th>Recipient</th>
        <th>Content</th>
        <th>Date</th>
      </tr>
      <?php while($r = $res->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($r['title']); ?></td>
          <td><?php echo htmlspecialchars($r['recipient']); ?></td>
          <td><?php echo nl2br(htmlspecialchars($r['content'])); ?></td>
          <td><?php echo $r['created_at']; ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>

<script>
  const btn = document.getElementById("toggleLog");
  const logSection = document.getElementById("logSection");

  btn.addEventListener("click", () => {
    if (logSection.style.display === "none") {
      logSection.style.display = "block";
      btn.textContent = "Hide Message Log";
    } else {
      logSection.style.display = "none";
      btn.textContent = "Show Message Log";
    }
  });
</script>

</body>
</html>
