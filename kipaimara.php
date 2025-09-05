<?php
session_start();
require_once 'db.php';

// Fetch communities and units for dropdowns
$communities_res = $conn->query("SELECT id, community_name FROM communities ORDER BY community_name");
$units_res = $conn->query("SELECT id, unit_name FROM units ORDER BY unit_name");

// Export functionality
if (isset($_GET['export'])) {
    $type = $_GET['export'];
    $result = $conn->query("SELECT k.*, c.community_name, u.unit_name 
                            FROM kipaimara k
                            LEFT JOIN communities c ON k.community_id=c.id
                            LEFT JOIN units u ON k.unit_id=u.id
                            ORDER BY k.created_at DESC");

    if ($type === 'csv' || $type === 'excel') {
        $filename = "registered_children_" . date('Y-m-d') . ".csv";
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Full Name', 'Date of Birth', 'Parent Name', 'Parent Phone', 'Community', 'Unit', 'Start Date', 'End Date', 'Year Registered']);
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['id'],
                $row['full_name'],
                $row['date_of_birth'],
                $row['parent_name'],
                $row['parent_phone'],
                $row['community_name'],
                $row['unit_name'],
                $row['start_date'],
                $row['end_date'],
                $row['year_registered']
            ]);
        }
        fclose($output);
        exit;
    }
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name    = trim($_POST['full_name']);
    $dob          = $_POST['date_of_birth'];
    $parent_name  = trim($_POST['parent_name']);
    $parent_phone = trim($_POST['parent_phone']);
    $community_id = intval($_POST['community_id']);
    $unit_id      = intval($_POST['unit_id']);
    $start_date   = $_POST['start_date'];
    $end_date     = $_POST['end_date'];

    // Validate parent phone number
    if (!empty($parent_phone)) {
        // Remove any non-digit characters
        $cleaned_phone = preg_replace('/[^0-9]/', '', $parent_phone);
        
        // Validate Tanzanian phone numbers (starting with 255, 0, or +255)
        if (!preg_match('/^(255|0|\+255)[0-9]{9}$/', $cleaned_phone)) {
            $message = "❌ Invalid parent phone number format. Please use Tanzanian format (255XXXXXXXXX, 0XXXXXXXXX, or +255XXXXXXXXX)";
        } else {
            // Format phone number to standard Tanzanian format (255XXXXXXXXX)
            if (strpos($cleaned_phone, '255') === 0 && strlen($cleaned_phone) === 12) {
                $parent_phone = $cleaned_phone;
            } elseif (strpos($cleaned_phone, '0') === 0 && strlen($cleaned_phone) === 10) {
                $parent_phone = '255' . substr($cleaned_phone, 1);
            } elseif (strlen($cleaned_phone) === 9) {
                $parent_phone = '255' . $cleaned_phone;
            }
        }
    }

    if (empty($message) && ($community_id === 0 || $unit_id === 0)) {
        $message = "❌ Please select both Community and Unit.";
    } 
    
    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO kipaimara 
            (full_name, date_of_birth, parent_name, parent_phone, community_id, unit_id, start_date, end_date, year_registered) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, YEAR(NOW()))");
        $stmt->bind_param("ssssiiss", $full_name, $dob, $parent_name, $parent_phone, $community_id, $unit_id, $start_date, $end_date);
        if ($stmt->execute()) {
            $message = "✅ Child registered successfully";
        } else {
            $message = "❌ Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all registered children
$result = $conn->query("SELECT k.*, c.community_name, u.unit_name 
                        FROM kipaimara k
                        LEFT JOIN communities c ON k.community_id=c.id
                        LEFT JOIN units u ON k.unit_id=u.id
                        ORDER BY k.created_at DESC");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Watoto wa Kipaimara</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f7fa; margin:0; padding:0; }
.container { max-width:1200px; margin:30px auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
h1,h2 { text-align:center; color:#2c3e50; margin-bottom:20px; }
a { text-decoration:none; color:#3498db; }
.message { text-align:center; margin:10px 0; font-weight:bold; }
.success { color:green; }
.error { color:red; }

.form-card { background:#f9f9f9; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05); margin-bottom:30px; }
.form-card form { display:grid; grid-template-columns: 1fr 1fr; gap:15px; }
.form-card label { font-weight:bold; }
.form-card input, .form-card select, .form-card button { padding:10px; border-radius:6px; border:1px solid #ccc; font-size:14px; }
.form-card button { background:#3498db; color:#fff; border:none; cursor:pointer; grid-column:span 2; font-size:16px; }
.form-card button:hover { background:#2980b9; }
.form-card input:invalid { border-color: #e74c3c; }
.form-card input:valid { border-color: #2ecc71; }

.table-actions { margin-bottom:15px; text-align:right; }
.table-actions button, .table-actions a { padding:8px 12px; border-radius:6px; border:none; cursor:pointer; margin-left:5px; background:#3498db; color:#fff; text-decoration:none; font-size:14px; }
.table-actions button:hover, .table-actions a:hover { background:#2980b9; }

table { width:100%; border-collapse: collapse; margin-top:15px; }
th, td { border:1px solid #ddd; padding:12px; text-align:left; font-size:14px; }
th { background:#3498db; color:#fff; }
tr:nth-child(even) { background:#f9f9f9; }
tr:hover { background:#f1f1f1; }
.back-btn { margin-top:20px; display:inline-block; background:#27ae60; color:#fff; padding:10px 15px; border-radius:6px; text-decoration:none; }
.back-btn:hover { background:#2ecc71; }

.phone-format { font-size: 12px; color: #7f8c8d; margin-top: 4px; }

@media print {
    .form-card, .table-actions, .back-btn { display:none; }
}
</style>
<script>
function validatePhone(input) {
    // Remove any non-digit characters
    let phone = input.value.replace(/[^0-9+]/g, '');
    
    // Check if it's empty (required field)
    if (phone === '') {
        input.setCustomValidity('Phone number is required');
        input.style.borderColor = '#e74c3c';
        return false;
    }
    
    // Check Tanzanian phone number format
    let regex = /^(255|0|\+255)[0-9]{9}$/;
    let cleaned = phone.replace(/[^0-9]/g, '');
    
    if (cleaned.startsWith('255') && cleaned.length === 12) {
        input.setCustomValidity('');
        input.style.borderColor = '#2ecc71';
    } else if (cleaned.startsWith('0') && cleaned.length === 10) {
        input.setCustomValidity('');
        input.style.borderColor = '#2ecc71';
    } else if (cleaned.length === 9) {
        input.setCustomValidity('');
        input.style.borderColor = '#2ecc71';
    } else if (phone.startsWith('+255') && cleaned.length === 12) {
        input.setCustomValidity('');
        input.style.borderColor = '#2ecc71';
    } else {
        input.setCustomValidity('Please enter a valid Tanzanian phone number (255XXXXXXXXX, 0XXXXXXXXX, or +255XXXXXXXXX)');
        input.style.borderColor = '#e74c3c';
    }
    
    return input.checkValidity();
}

function formatPhone(input) {
    let phone = input.value.replace(/[^0-9+]/g, '');
    let cleaned = phone.replace(/[^0-9]/g, '');
    
    if (cleaned.startsWith('255') && cleaned.length === 12) {
        input.value = cleaned;
    } else if (cleaned.startsWith('0') && cleaned.length === 10) {
        input.value = '255' + cleaned.substring(1);
    } else if (cleaned.length === 9) {
        input.value = '255' + cleaned;
    } else if (phone.startsWith('+255') && cleaned.length === 12) {
        input.value = cleaned;
    }
}

function validateForm() {
    let phoneInput = document.querySelector('input[name="parent_phone"]');
    let isValid = validatePhone(phoneInput);
    
    if (!isValid) {
        alert('Please enter a valid Tanzanian phone number format (255XXXXXXXXX, 0XXXXXXXXX, or +255XXXXXXXXX)');
        phoneInput.focus();
        return false;
    }
    
    return true;
}

// Validate phone on page load if there's a value
document.addEventListener('DOMContentLoaded', function() {
    let phoneInput = document.querySelector('input[name="parent_phone"]');
    if (phoneInput.value) {
        validatePhone(phoneInput);
    }
});
</script>
</head>
<body>
<div class="container">
<h1>👶 Watoto wa Kipaimara</h1>
<a href="admin.php" class="back-btn">⬅ Back to Dashboard</a>

<?php if (!empty($message)) echo "<div class='message " . (strpos($message, '✅') !== false ? 'success' : 'error') . "'>$message</div>"; ?>

<div class="form-card">
<h2>Register Child</h2>
<form method="post" onsubmit="return validateForm()">
    <label>Full Name *</label>
    <input type="text" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">

    <label>Date of Birth *</label>
    <input type="date" name="date_of_birth" required value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">

    <label>Parent Name *</label>
    <input type="text" name="parent_name" required value="<?= htmlspecialchars($_POST['parent_name'] ?? '') ?>">

    <label>Parent Phone Number *</label>
    <div>
        <input type="text" name="parent_phone" 
               pattern="^(255\d{9}|0\d{9}|\+255\d{9}|\d{9})$" 
               oninput="validatePhone(this)" 
               onblur="formatPhone(this)"
               required 
               value="<?= htmlspecialchars($_POST['parent_phone'] ?? '') ?>">
        <div class="phone-format">Format: 255XXXXXXXXX, 0XXXXXXXXX, or +255XXXXXXXXX</div>
    </div>

    <label>Community *</label>
    <select name="community_id" required>
        <option value="">-- Select Community --</option>
        <?php 
        $communities_res->data_seek(0); // Reset pointer
        while($c = $communities_res->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>" <?= (isset($_POST['community_id']) && $_POST['community_id'] == $c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['community_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Unit *</label>
    <select name="unit_id" required>
        <option value="">-- Select Unit --</option>
        <?php 
        $units_res->data_seek(0); // Reset pointer
        while($u = $units_res->fetch_assoc()): ?>
            <option value="<?= $u['id'] ?>" <?= (isset($_POST['unit_id']) && $_POST['unit_id'] == $u['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['unit_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Start Date of Study *</label>
    <input type="date" name="start_date" required value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>">

    <label>End Date of Study *</label>
    <input type="date" name="end_date" required value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">

    <button type="submit">Register</button>
</form>
</div>

<div class="table-actions">
    <a href="?export=csv">📄 Export CSV</a>
    <a href="?export=excel">📊 Export Excel</a>
    <button onclick="window.print()">🖨 Print Table</button>
</div>

<h2>All Registered Children</h2>
<table>
<tr>
    <th>ID</th>
    <th>Full Name</th>
    <th>Date of Birth</th>
    <th>Parent Name</th>
    <th>Parent Phone</th>
    <th>Community</th>
    <th>Unit</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Year Registered</th>
</tr>
<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['full_name']) ?></td>
    <td><?= $row['date_of_birth'] ?></td>
    <td><?= htmlspecialchars($row['parent_name']) ?></td>
    <td><?= htmlspecialchars($row['parent_phone']) ?></td>
    <td><?= htmlspecialchars($row['community_name']) ?></td>
    <td><?= htmlspecialchars($row['unit_name']) ?></td>
    <td><?= $row['start_date'] ?></td>
    <td><?= $row['end_date'] ?></td>
    <td><?= $row['year_registered'] ?></td>
</tr>
<?php } ?>
</table>
</div>

</body>
</html>