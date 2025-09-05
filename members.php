<?php
session_start();
require_once 'db.php';
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit; }

$err = '';
$ok = '';

// Fetch existing communities and units
$communities_res = $conn->query("SELECT id, community_name FROM communities ORDER BY community_name");
$units_res = $conn->query("SELECT id, unit_name FROM units ORDER BY unit_name");

// Add Member
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_member'])) {
    $community_id = intval($_POST['community_id'] ?? 0);
    $unit_id = intval($_POST['unit'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['date_of_birth'] ?? '');
    $confirmation_no = trim($_POST['confirmation_no'] ?? '');
    $Baptism_no = trim($_POST['Baptism_no'] ?? '');
    $communial_no = trim($_POST['communial_no'] ?? '');
    $marriage_no = trim($_POST['marriage_no'] ?? '');

    if ($community_id === 0 || $unit_id === 0 || $name === '' || $dob === '') {
        $err = "Community, Unit, Name, and Date of Birth are required.";
    } else {
        // Validate phone number if provided
        if (!empty($phone)) {
            // Remove any non-digit characters
            $cleaned_phone = preg_replace('/[^0-9]/', '', $phone);
            
            // Validate Tanzanian phone numbers (starting with 255, 0, or +255)
            if (!preg_match('/^(255|0|\+255)[0-9]{9}$/', $cleaned_phone)) {
                $err = "❌ Invalid phone number format. Please use Tanzanian format (255XXXXXXXXX, 0XXXXXXXXX, or +255XXXXXXXXX)";
            } else {
                // Format phone number to standard Tanzanian format (255XXXXXXXXX)
                if (strpos($cleaned_phone, '255') === 0 && strlen($cleaned_phone) === 12) {
                    $phone = $cleaned_phone;
                } elseif (strpos($cleaned_phone, '0') === 0 && strlen($cleaned_phone) === 10) {
                    $phone = '255' . substr($cleaned_phone, 1);
                } elseif (strlen($cleaned_phone) === 9) {
                    $phone = '255' . $cleaned_phone;
                }
            }
        }

        // Validate number-only fields
        if (empty($err) && !empty($confirmation_no) && !preg_match('/^[0-9]+$/', $confirmation_no)) {
            $err = "❌ Confirmation number must contain only digits (0-9)";
        }
        
        if (empty($err) && !empty($Baptism_no) && !preg_match('/^[0-9]+$/', $Baptism_no)) {
            $err = "❌ Baptism number must contain only digits (0-9)";
        }
        
        if (empty($err) && !empty($communial_no) && !preg_match('/^[0-9]+$/', $communial_no)) {
            $err = "❌ Communial number must contain only digits (0-9)";
        }
        
        if (empty($err) && !empty($marriage_no) && !preg_match('/^[0-9]+$/', $marriage_no)) {
            $err = "❌ Marriage number must contain only digits (0-9)";
        }

        if (empty($err)) {
            $unit_row = $conn->query("SELECT unit_name FROM units WHERE id=$unit_id")->fetch_assoc();
            $unit_name = $unit_row['unit_name'] ?? '';

            $stmt = $conn->prepare("INSERT INTO members (community_id, name, phone, dob, confirmation_no, Baptism_no, communial_no, marriage_no, unit) VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("issssssss", $community_id, $name, $phone, $dob, $confirmation_no, $Baptism_no, $communial_no, $marriage_no, $unit_name);

            if ($stmt->execute()) $ok = "✅ Member added successfully.";
            else $err = "❌ DB error: ". $stmt->error;

            $stmt->close();
        }
    }
}

// Handle Search
$search = trim($_GET['search'] ?? '');
$where = "";
if ($search !== "") {
    $search_esc = $conn->real_escape_string($search);
    $where = "WHERE m.name LIKE '%$search_esc%' 
              OR m.phone LIKE '%$search_esc%' 
              OR c.community_name LIKE '%$search_esc%'";
}

// Fetch members
$res = $conn->query("
    SELECT m.*, c.community_name 
    FROM members m 
    JOIN communities c ON m.community_id = c.id 
    $where
    ORDER BY c.community_name, m.name
");

// Handle CSV Export
if(isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=members.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Community','Unit','Name','Phone','Date of Birth','Confirmation','Baptism','Communial','Marriage','Created_at']);
    $res->data_seek(0);
    while($r = $res->fetch_assoc()){
        fputcsv($output, [
            $r['community_name'], $r['unit'], $r['name'], $r['phone'], $r['dob'],
            $r['confirmation_no'], $r['Baptism_no'], $r['communial_no'], $r['marriage_no'], $r['created_at']
        ]);
    }
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Members Management</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f7fa; margin:0; padding:0; }
.container { max-width:1200px; margin:30px auto; background:#fff; padding:25px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
h1,h2 { text-align:center; color:#2c3e50; margin-bottom:20px; }
a { text-decoration:none; color:#3498db; margin: 0 5px; }
.message { text-align:center; margin:10px 0; font-weight:bold; }
.success { color:green; }
.error { color:red; }

.form-card { background:#f9f9f9; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05); margin-bottom:30px; }
.form-card form { display:grid; grid-template-columns: 1fr 1fr; gap:15px; }
.form-card input, .form-card select, .form-card button { padding:10px; border-radius:6px; border:1px solid #ccc; font-size:14px; }
.form-card button { background:#3498db; color:#fff; border:none; cursor:pointer; grid-column:span 2; font-size:16px; }
.form-card button:hover { background:#2980b9; }
.form-card input:invalid { border-color: #e74c3c; }
.form-card input:valid { border-color: #2ecc71; }

.table-actions { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
.table-actions form { margin:0; }
.table-actions input { padding:8px; border-radius:6px; border:1px solid #ccc; font-size:14px; }
.table-actions button, .table-actions a { padding:8px 15px; border-radius:6px; background:#3498db; color:#fff; text-decoration:none; border:none; cursor:pointer; margin-left:5px; }
.table-actions button:hover, .table-actions a:hover { background:#2980b9; }

table { width:100%; border-collapse: collapse; margin-top:15px; }
th, td { border:1px solid #ddd; padding:12px; text-align:left; font-size:14px; }
th { background:#3498db; color:#fff; }
tr:nth-child(even) { background:#f9f9f9; }
tr:hover { background:#f1f1f1; }
@media print { .form-card, .table-actions { display:none; } }

.phone-format { font-size: 12px; color: #7f8c8d; margin-top: 4px; }
.number-format { font-size: 12px; color: #7f8c8d; margin-top: 4px; }
</style>
<script>
function printTable() { window.print(); }

function validatePhone(input) {
    // Remove any non-digit characters
    let phone = input.value.replace(/[^0-9+]/g, '');
    
    // Check if it's empty (optional field)
    if (phone === '') {
        input.setCustomValidity('');
        return true;
    }
    
    // Check Tanzanian phone number format
    let regex = /^(255|0|\+255)[0-9]{9}$/;
    let cleaned = phone.replace(/[^0-9]/g, '');
    
    if (cleaned.startsWith('255') && cleaned.length === 12) {
        input.setCustomValidity('');
    } else if (cleaned.startsWith('0') && cleaned.length === 10) {
        input.setCustomValidity('');
    } else if (cleaned.length === 9) {
        input.setCustomValidity('');
    } else if (phone.startsWith('+255') && cleaned.length === 12) {
        input.setCustomValidity('');
    } else {
        input.setCustomValidity('Please enter a valid Tanzanian phone number (255XXXXXXXXX, 0XXXXXXXXX, or +255XXXXXXXXX)');
    }
    
    // Update styling
    if (input.checkValidity()) {
        input.style.borderColor = '#2ecc71';
    } else {
        input.style.borderColor = '#e74c3c';
    }
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

function validateNumbersOnly(input, fieldName) {
    // Remove any non-digit characters
    let value = input.value.replace(/[^0-9]/g, '');
    
    // Check if it's empty (optional field)
    if (value === '') {
        input.setCustomValidity('');
        input.style.borderColor = '#ccc';
        return true;
    }
    
    // Check if it contains only numbers
    if (/^[0-9]+$/.test(value)) {
        input.setCustomValidity('');
        input.style.borderColor = '#2ecc71';
        input.value = value; // Remove any non-digit characters
        return true;
    } else {
        input.setCustomValidity(fieldName + ' must contain only numbers (0-9)');
        input.style.borderColor = '#e74c3c';
        return false;
    }
}

function restrictToNumbers(input) {
    // Remove any non-digit characters in real-time
    input.value = input.value.replace(/[^0-9]/g, '');
}
</script>
</head>
<body>
<div class="container">
<h1>Members Management</h1>
<p style="text-align:center"><a href="admin.php">← Back to Dashboard</a></p>

<?php if($ok) echo '<p class="message success">'.htmlspecialchars($ok).'</p>'; ?>
<?php if($err) echo '<p class="message error">'.htmlspecialchars($err).'</p>'; ?>

<div class="form-card">
<h2>Add Member</h2>
<form method="post" onsubmit="return validateForm()">
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

    <select name="unit" required>
        <option value="">-- Select Unit / Kigango --</option>
        <?php 
        $units_res->data_seek(0); // Reset pointer
        while($u = $units_res->fetch_assoc()): ?>
            <option value="<?= $u['id'] ?>" <?= (isset($_POST['unit']) && $_POST['unit'] == $u['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['unit_name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <input name="name" placeholder="Full Name *" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    
    <div>
        <input name="phone" placeholder="Phone Number" 
               pattern="^(255\d{9}|0\d{9}|\+255\d{9}|\d{9})$" 
               oninput="validatePhone(this)" 
               onblur="formatPhone(this)"
               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        <div class="phone-format">Format: 255XXXXXXXXX, 0XXXXXXXXX, or +255XXXXXXXXX</div>
    </div>
    
    <input type="date" name="date_of_birth" required value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
    
    <div>
        <input name="confirmation_no" placeholder="Namba ya Kipaimara *" 
               oninput="restrictToNumbers(this)" 
               onblur="validateNumbersOnly(this, 'Confirmation number')"
               value="<?= htmlspecialchars($_POST['confirmation_no'] ?? '') ?>">
        <div class="number-format">Numbers only (0-9)</div>
    </div>
    
    <div>
        <input name="Baptism_no" placeholder="Namba ya Ubatizo" 
               oninput="restrictToNumbers(this)" 
               onblur="validateNumbersOnly(this, 'Baptism number')"
               value="<?= htmlspecialchars($_POST['Baptism_no'] ?? '') ?>">
        <div class="number-format">Numbers only (0-9)</div>
    </div>
    
    <div>
        <input name="communial_no" placeholder="Namba ya Komonio" 
               oninput="restrictToNumbers(this)" 
               onblur="validateNumbersOnly(this, 'Communial number')"
               value="<?= htmlspecialchars($_POST['communial_no'] ?? '') ?>">
        <div class="number-format">Numbers only (0-9)</div>
    </div>
    
    <div>
        <input name="marriage_no" placeholder="Namba ya Ndoa" 
               oninput="restrictToNumbers(this)" 
               onblur="validateNumbersOnly(this, 'Marriage number')"
               value="<?= htmlspecialchars($_POST['marriage_no'] ?? '') ?>">
        <div class="number-format">Numbers only (0-9)</div>
    </div>

    <button name="add_member" type="submit">➕ Add Member</button>
</form>
</div>

<div class="table-actions">
    <form method="get">
        <input type="text" name="search" placeholder="🔍 Search member..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>
    <div>
        <button onclick="printTable()">🖨 Print</button>
        <a href="?export=csv">⬇ Export CSV</a>
    </div>
</div>

<table>
<tr>
    <th>Community</th>
    <th>Unit</th>
    <th>Name</th>
    <th>Phone</th>
    <th>Date of Birth</th>
    <th>Confirmation</th>
    <th>Baptism</th>
    <th>Communial</th>
    <th>Marriage</th>
    <th>Created_at</th>
</tr>
<?php if($res->num_rows > 0): ?>
<?php while($r = $res->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($r['community_name']) ?></td>
    <td><?= htmlspecialchars($r['unit']) ?></td>
    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= htmlspecialchars($r['phone']) ?></td>
    <td><?= htmlspecialchars($r['dob']) ?></td>
    <td><?= htmlspecialchars($r['confirmation_no']) ?></td>
    <td><?= htmlspecialchars($r['Baptism_no']) ?></td>
    <td><?= htmlspecialchars($r['communial_no']) ?></td>
    <td><?= htmlspecialchars($r['marriage_no']) ?></td>
    <td><?= htmlspecialchars($r['created_at']) ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="10" style="text-align:center; color:red;">No results found.</td></tr>
<?php endif; ?>
</table>
</div>

<script>
function validateForm() {
    let phoneInput = document.querySelector('input[name="phone"]');
    validatePhone(phoneInput);
    
    let confirmationInput = document.querySelector('input[name="confirmation_no"]');
    let baptismInput = document.querySelector('input[name="Baptism_no"]');
    let communialInput = document.querySelector('input[name="communial_no"]');
    let marriageInput = document.querySelector('input[name="marriage_no"]');
    
    let isValid = true;
    
    if (!phoneInput.checkValidity()) {
        alert('Please enter a valid Tanzanian phone number format (255XXXXXXXXX, 0XXXXXXXXX, or +255XXXXXXXXX)');
        phoneInput.focus();
        isValid = false;
    }
    
    if (isValid && !validateNumbersOnly(confirmationInput, 'Confirmation number')) {
        alert('Confirmation number must contain only numbers (0-9)');
        confirmationInput.focus();
        isValid = false;
    }
    
    if (isValid && !validateNumbersOnly(baptismInput, 'Baptism number')) {
        alert('Baptism number must contain only numbers (0-9)');
        baptismInput.focus();
        isValid = false;
    }
    
    if (isValid && !validateNumbersOnly(communialInput, 'Communial number')) {
        alert('Communial number must contain only numbers (0-9)');
        communialInput.focus();
        isValid = false;
    }
    
    if (isValid && !validateNumbersOnly(marriageInput, 'Marriage number')) {
        alert('Marriage number must contain only numbers (0-9)');
        marriageInput.focus();
        isValid = false;
    }
    
    return isValid;
}

// Validate fields on page load if there are values
document.addEventListener('DOMContentLoaded', function() {
    let phoneInput = document.querySelector('input[name="phone"]');
    if (phoneInput.value) {
        validatePhone(phoneInput);
    }
    
    let confirmationInput = document.querySelector('input[name="confirmation_no"]');
    if (confirmationInput.value) {
        validateNumbersOnly(confirmationInput, 'Confirmation number');
    }
    
    let baptismInput = document.querySelector('input[name="Baptism_no"]');
    if (baptismInput.value) {
        validateNumbersOnly(baptismInput, 'Baptism number');
    }
    
    let communialInput = document.querySelector('input[name="communial_no"]');
    if (communialInput.value) {
        validateNumbersOnly(communialInput, 'Communial number');
    }
    
    let marriageInput = document.querySelector('input[name="marriage_no"]');
    if (marriageInput.value) {
        validateNumbersOnly(marriageInput, 'Marriage number');
    }
});
</script>
</body>
</html>