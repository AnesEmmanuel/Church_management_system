<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$msg=''; $err='';

// Fetch communities and units for dropdowns
$communities_res = $conn->query("SELECT id, community_name FROM communities ORDER BY community_name");
$units_res = $conn->query("SELECT id, unit_name FROM units ORDER BY unit_name");

// --- SMS Sending Function (example, replace with real API) ---
function send_sms($phone, $message) { return true; }

// --- Record Donation ---
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['record_donation'])) {
    $member = trim($_POST['member_name'] ?? '');
    $phone  = trim($_POST['member_phone'] ?? '');
    $month  = trim($_POST['month_paid'] ?? '');
    $amount = floatval($_POST['amount'] ?? 0);
    $method = trim($_POST['payment_method'] ?? '');
    $community_id = intval($_POST['community_id'] ?? 0);
    $unit_id = intval($_POST['unit_id'] ?? 0);

    if ($member==='' || $month==='' || $amount <= 0 || $method==='' || !$community_id || !$unit_id) {
        $err = "All fields required and amount > 0.";
    } else {
        $month_date = $month . '-01';
        $stmt = $conn->prepare("INSERT INTO donations (member_name, phone, month_paid, amount, payment_method, community_id, unit_id) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("ssddsii", $member, $phone, $month_date, $amount, $method, $community_id, $unit_id);

        if ($stmt->execute()) {
            if (!empty($phone)) {
                $sms_msg = "Dear $member, your donation of Ksh $amount for $month has been received. Thank you!";
                send_sms($phone, $sms_msg);
            }
            $stmt2 = $conn->prepare("INSERT INTO messages (recipient, title, content) VALUES (?,?,?)");
            $title = "Donation Received";
            $msg_text = "Donation received for $month: " . number_format($amount,2);
            $stmt2->bind_param("sss", $member, $title, $msg_text);
            $stmt2->execute();
            $stmt2->close();

            $msg = "Donation recorded successfully and member notified.";
        } else {
            $err = "DB error: " . $conn->error;
        }
        $stmt->close();
    }
}

// --- Fetch Donations ---
$year_filter = $_GET['year'] ?? '';
$sql = "SELECT d.*, c.community_name, u.unit_name
        FROM donations d
        LEFT JOIN communities c ON d.community_id=c.id
        LEFT JOIN units u ON d.unit_id=u.id
        WHERE 1=1";
if ($year_filter && preg_match('/^\d{4}$/',$year_filter)) {
    $sql .= " AND YEAR(d.month_paid)=" . intval($year_filter);
}
$sql .= " ORDER BY d.payment_date DESC";

$res = $conn->query($sql);

// Calculate total
$sql_total = "SELECT SUM(amount) as total FROM donations WHERE 1=1";
if ($year_filter) $sql_total .= " AND YEAR(month_paid)=" . intval($year_filter);
$total_amount = $conn->query($sql_total)->fetch_assoc()['total'] ?? 0;

$username = htmlspecialchars($_SESSION['user'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Donations / Tithes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: Arial, sans-serif; background: #f5f7fa; }
.container { max-width: 1200px; margin: 30px auto; background:#fff; padding: 30px; border-radius: 12px; box-shadow: 0 3px 15px rgba(0,0,0,0.08);}
h1 { margin-bottom: 20px; color:#0b6efd; }
.form-control, .form-select { margin-bottom: 15px; }
.btn-primary { background:#0b6efd; border:none; padding: 10px 20px; border-radius: 8px; }
.btn-primary:hover { background:#0954c3; }
.alert { border-radius:8px; }
.table { margin-top:30px; }
.badge-cash { background:#6c757d; color:#fff; }
.badge-mobile { background:#198754; color:#fff; }
.small { font-size:0.8rem; color:#777; }
@media print {
    body * { visibility: hidden; }
    #donationTable, #donationTable * { visibility: visible; }
    #donationTable { position: absolute; left:0; top:0; width:100%; }
}
</style>
</head>
<body>
<div class="container">
<h1>Donations / Tithes</h1>
<p><a href="admin.php">← Back to Dashboard</a></p>

<?php if($msg) echo '<div class="alert alert-success">'.htmlspecialchars($msg).'</div>'; ?>
<?php if($err) echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; ?>

<!-- Record Donation Form -->
<form method="post" class="mb-4">
<div class="row g-2">
  <div class="col-md-2">
    <input name="member_name" placeholder="Member Full Name" class="form-control" required>
  </div>
  <div class="col-md-2">
    <input name="member_phone" placeholder="Phone Number" class="form-control">
  </div>
  <div class="col-md-2">
    <select name="community_id" class="form-select" required>
      <option value="">Select Community</option>
      <?php
      $communities_res->data_seek(0);
      while($c = $communities_res->fetch_assoc()): ?>
        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['community_name']); ?></option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="col-md-2">
    <select name="unit_id" class="form-select" required>
      <option value="">Select Unit</option>
      <?php
      $units_res->data_seek(0);
      while($u = $units_res->fetch_assoc()): ?>
        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['unit_name']); ?></option>
      <?php endwhile; ?>
    </select>
  </div>
  <div class="col-md-2">
    <input type="month" name="month_paid" class="form-control" required>
  </div>
  <div class="col-md-2">
    <input type="number" name="amount" class="form-control" placeholder="Amount" step="0.01" required>
  </div>
  <div class="col-md-2">
    <select name="payment_method" class="form-select" required>
      <option value="">Select Payment Method</option>
      <option>Cash</option>
      <option>M-Pesa</option>
      <option>Tigo Pesa</option>
      <option>Halopesa</option>
      <option>Airtel Money</option>
      <option>T-Pesa</option>
      <option>Bank Transfer</option>
    </select>
  </div>
</div>
<button type="submit" name="record_donation" class="btn btn-primary mt-2">Record Donation</button>
</form>

<!-- Year Filter & Export/Print -->
<form method="get" class="mb-4">
<div class="row g-2 align-items-center">
  <!-- <div class="col-md-2">
    <input type="number" name="year" placeholder="Year (e.g., 2025)" class="form-control" value="<?php echo htmlspecialchars($year_filter); ?>">
  </div> -->
  <div class="col-md-auto">
    <!-- <button class="btn btn-primary">Search</button> -->
    <button type="button" class="btn btn-success" onclick="window.open('export_donations.php?<?php echo http_build_query($_GET); ?>','_blank')">Export Excel</button>
    <button type="button" class="btn btn-secondary" onclick="window.print()">Print</button>
  </div>
  <?php if($year_filter): ?>
  <div class="col-md-auto">
    <a href="donations.php" class="btn btn-light">Show All</a>
  </div>
  <?php endif; ?>
</div>
</form>

<h2>Donation History <?php if($year_filter) echo "for $year_filter"; ?></h2>
<p><strong>Total Amount:</strong> <?php echo number_format($total_amount,2); ?></p>

<div class="table-responsive" id="donationTable">
<table class="table table-striped table-hover">
<thead class="table-light">
<tr>
<th>#</th>
<th>Member Name</th>
<th>Phone</th>
<th>Community</th>
<th>Unit</th>
<th>Month Paid</th>
<th>Amount</th>
<th>Payment Method</th>
<th>Date Recorded</th>
</tr>
</thead>
<tbody>
<?php $i=1; while($r=$res->fetch_assoc()): ?>
<tr>
  <td><?php echo $i++; ?></td>
  <td><?php echo htmlspecialchars($r['member_name']); ?></td>
  <td><?php echo htmlspecialchars($r['phone']); ?></td>
  <td><?php echo htmlspecialchars($r['community_name']); ?></td>
  <td><?php echo htmlspecialchars($r['unit_name']); ?></td>
  <td><?php echo !empty($r['month_paid']) ? date('Y-m', strtotime($r['month_paid'])) : ''; ?></td>
  <td><?php echo number_format($r['amount'],2); ?></td>
  <td>
    <?php 
      $method = htmlspecialchars($r['payment_method']);
      $badge = in_array($method,['Cash','Bank Transfer']) ? 'badge-cash' : 'badge-mobile';
      echo "<span class='badge $badge'>$method</span>";
    ?>
  </td>
  <td><span class="small"><?php echo htmlspecialchars($r['payment_date']); ?></span></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>

</body>
</html>
