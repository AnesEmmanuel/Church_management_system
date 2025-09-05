<!-- <?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$msg=''; $err='';

// Handle form submission
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_community'])) {
    $name = trim($_POST['community_name'] ?? '');
    $roles = ["Chairman","Ass.Chairman","Secretary","Ass.Secretary","Accoutant"];

    if ($name === '') {
        $err = "Community name required.";
    } else {
        // Insert community
        $stmt = $conn->prepare("INSERT INTO communities (community_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $community_id = $stmt->insert_id;

            // Insert 5 leaders with roles
            $leader_stmt = $conn->prepare("INSERT INTO leaders (community_id, role, name, phone) VALUES (?,?,?,?)");
            foreach ($roles as $index=>$role) {
                $lname = trim($_POST['leader_name'][$index] ?? '');
                $lphone = trim($_POST['leader_phone'][$index] ?? '');
                if ($lname==='' || $lphone==='') {
                    $err = "All 5 leaders with phone numbers are required.";
                    break;
                }
                $leader_stmt->bind_param("isss",$community_id,$role,$lname,$lphone);
                $leader_stmt->execute();
            }
            if (!$err) $msg="✅ Community and leaders added successfully.";
            $leader_stmt->close();
        } else {
            $err = "❌ DB error: ".$conn->error;
        }
        $stmt->close();
    }
}

// Fetch communities + leaders
$res = $conn->query("
    SELECT c.id,c.community_name,l.role,l.name,l.phone 
    FROM communities c 
    LEFT JOIN leaders l ON c.id=l.community_id 
    ORDER BY c.community_name,l.role
");
$communities=[];
while($row=$res->fetch_assoc()){
    $communities[$row['id']]['name']=$row['community_name'];
    $communities[$row['id']]['leaders'][]=$row;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Communities</title>
  <link rel="stylesheet" href="cs/style.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f4f7fa; margin:0; padding:0; }
    .container { max-width:1000px; margin:30px auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
    h1,h2 { text-align:center; color:#2c3e50; }
    form { margin-bottom:25px; }
    .leaders-grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(220px,1fr)); gap:15px; margin-top:10px; }
    .leaders-grid input { width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; margin-top:4px;}
    label { font-weight:bold; font-size:14px; color:#34495e; }
    form button { margin-top:20px; width:100%; padding:12px; background:#3498db; border:none; color:#fff; font-size:16px; border-radius:6px; cursor:pointer; }
    form button:hover { background:#2980b9; }
    .message { text-align:center; margin:10px; font-weight:bold; }
    .green{color:green;} .red{color:red;}
    table { width:100%; border-collapse:collapse; margin-top:20px;}
    th,td { border:1px solid #ddd; padding:10px; text-align:left; }
    th { background:#3498db; color:white; }
    tr:nth-child(even){background:#f9f9f9;}
    tr:hover{background:#f1f1f1;}
    .leaders-table { margin-top:10px; border:1px solid #ccc; }
    .leaders-table th { background:#ecf0f1; color:#2c3e50; }
  </style>
</head>
<body>
<div class="container">
  <h1>Community Management</h1>
  <p style="text-align:center;"><a href="admin.php">⬅ Back to Dashboard</a></p>

  <?php if($msg) echo '<p class="message green">'.htmlspecialchars($msg).'</p>'; ?>
  <?php if($err) echo '<p class="message red">'.htmlspecialchars($err).'</p>'; ?>

  <h2>Add New Community with 5 Leaders</h2>
  <form method="post">
    <input name="community_name" placeholder="Community Name (e.g. St. Joseph)" required>
    <div class="leaders-grid">
      <?php 
      $roles=["Chairman","Ass.Chairman","Secretary","Ass.Secretary","Accoutant"];
      foreach($roles as $i=>$role): ?>
        <div>
          <label><?php echo $role; ?> Name</label>
          <input type="text" name="leader_name[<?php echo $i; ?>]" required>
          <label>Phone</label>
          <input type="text" name="leader_phone[<?php echo $i; ?>]" required>
        </div>
      <?php endforeach; ?>
    </div>
    <button name="add_community" type="submit">➕ Add Community</button>
  </form>

  <h2>Communities List</h2>
  <table>
    <tr>
      <th>#</th>
      <th>Community</th>
      <th>Leaders</th>
    </tr>
    <?php $count=1; foreach($communities as $cid=>$c): ?>
      <tr>
        <td><?php echo $count++; ?></td>
        <td><strong><?php echo htmlspecialchars($c['name']); ?></strong></td>
        <td>
          <table class="leaders-table" width="100%">
            <tr><th>Role</th><th>fullName</th><th>Phone</th></tr>
            <?php foreach($c['leaders'] as $l): ?>
              <tr>
                <td><?php echo htmlspecialchars($l['role']); ?></td>
                <td><?php echo htmlspecialchars($l['name']); ?></td>
                <td><?php echo htmlspecialchars($l['phone']); ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html> -->
