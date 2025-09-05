<!-- <?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$msg=''; $err='';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_unit'])) {
    $name = trim($_POST['unit_name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    if ($name==='') $err = "Unit name required.";
    else {
        $stmt = $conn->prepare("INSERT INTO units (unit_name, description) VALUES (?,?)");
        $stmt->bind_param("ss",$name,$desc);
        if ($stmt->execute()) $msg = "Unit added.";
        else $err = "DB error: " . $conn->error;
        $stmt->close();
    }
}

$res = $conn->query("SELECT * FROM units ORDER BY unit_name");
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Units</title><link rel="stylesheet" href="cs/style.css">
</head>
<body>
<div class="container">
  <h1>Parish Units</h1>
  <p><a href="admin.php">Back</a></p>
  <?php if($msg) echo '<p style="color:green">'.htmlspecialchars($msg).'</p>'; ?>
  <?php if($err) echo '<p style="color:red">'.htmlspecialchars($err).'</p>'; ?>

  <form method="post">
    <input name="unit_name" placeholder="Unit name" required>
    <textarea name="description" placeholder="Description"></textarea>
    <button name="add_unit" type="submit">Add Unit</button>
  </form>

  <h2>Units</h2>
  <ul>
  <?php while($r = $res->fetch_assoc()): ?>
    <li class="item"><strong><?php echo htmlspecialchars($r['unit_name']); ?></strong><div class="small"><?php echo htmlspecialchars($r['description']); ?></div></li>
  <?php endwhile; ?>
  </ul>
</div>

</body>
</html> -->
