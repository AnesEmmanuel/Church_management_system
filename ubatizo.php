<?php
session_start();
require_once 'db.php';

// Fetch communities and units for dropdowns
$communities = $conn->query("SELECT id, community_name FROM communities ORDER BY community_name ASC");
$units       = $conn->query("SELECT id, unit_name FROM units ORDER BY unit_name ASC");

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name    = trim($_POST['full_name']);
    $dob          = $_POST['date_of_birth'];
    $parent_name  = trim($_POST['parent_name']);
    $parent_phone = trim($_POST['parent_phone']);
    $start_date   = $_POST['start_date'];
    $end_date     = $_POST['end_date'];
    $community_id = intval($_POST['community_id']);
    $unit_id      = intval($_POST['unit_id']);

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

    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO ubatizo
            (full_name, date_of_birth, parent_name, parent_phone, start_date, end_date, community_id, unit_id, year_registered, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, YEAR(NOW()), NOW())");
        $stmt->bind_param("ssssssii", $full_name, $dob, $parent_name, $parent_phone, $start_date, $end_date, $community_id, $unit_id);
        if ($stmt->execute()) {
            $message = "✅ Child registered successfully";
        } else {
            $message = "❌ Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all registered children with community + unit names
$result = $conn->query("SELECT u.*, c.community_name, un.unit_name 
    FROM ubatizo u
    LEFT JOIN communities c ON u.community_id = c.id
    LEFT JOIN units un ON u.unit_id = un.id
    ORDER BY u.created_at DESC");
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Watoto wa Ubatizo</title>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f7f9;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1100px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}
h2 {
    margin-bottom: 15px;
    color: #444;
}
.back-btn {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    background: #007bff;
    color: #fff;
    padding: 8px 14px;
    border-radius: 5px;
}
.back-btn:hover {
    background: #0056b3;
}
.msg {
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    font-weight: bold;
}
.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.card {
    background: #fafafa;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 25px;
}
form label {
    display: block;
    margin: 8px 0 5px;
    font-weight: bold;
}
form input, form select {
    width: 100%;
    padding: 8px;
    margin-bottom: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}
form input:invalid {
    border-color: #e74c3c;
}
form input:valid {
    border-color: #2ecc71;
}
form button {
    background: #28a745;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}
form button:hover {
    background: #218838;
}
.phone-format {
    font-size: 12px;
    color: #7f8c8d;
    margin-top: -8px;
    margin-bottom: 12px;
}
table.dataTable {
    width: 100% !important;
    border-collapse: collapse;
    margin-top: 15px;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
table.dataTable thead {
    background: #007bff;
    color: #fff;
    text-align: left;
}
table.dataTable thead th {
    padding: 12px 10px;
    font-size: 14px;
}
table.dataTable tbody td {
    padding: 10px;
    font-size: 13px;
    color: #333;
}
table.dataTable tbody tr:nth-child(even) {
    background: #f9f9f9;
}
table.dataTable tbody tr:hover {
    background: #eef6ff;
}
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid #ccc;
    padding: 6px;
    border-radius: 4px;
}
.dataTables_wrapper .dataTables_length select {
    border: 1px solid #ccc;
    padding: 6px;
    border-radius: 4px;
}
.dt-buttons .dt-button {
    background: #28a745 !important;
    color: #fff !important;
    border: none !important;
    border-radius: 4px !important;
    padding: 6px 12px !important;
    margin-right: 5px !important;
    font-size: 13px !important;
    cursor: pointer !important;
}
.dt-buttons .dt-button:hover {
    background: #218838 !important;
}
</style>
</head>
<body>
<div class="container">
  <h1>👶 Watoto wa Ubatizo</h1>
  
  <a href="admin.php" class="back-btn">⬅ Back to Dashboard</a>

  <?php if (!empty($message)) echo "<div class='msg " . (strpos($message, '✅') !== false ? 'success' : 'error') . "'>$message</div>"; ?>

  <!-- Registration Form -->
  <div class="card">
    <h2>➕ Register Child</h2>
    <form method="post" onsubmit="return validateForm()">
      <label>Full Name *</label>
      <input type="text" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
      
      <label>Date of Birth *</label>
      <input type="date" name="date_of_birth" required value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
      
      <label>Parent Name *</label>
      <input type="text" name="parent_name" required value="<?= htmlspecialchars($_POST['parent_name'] ?? '') ?>">
      
      <label>Parent Phone Number *</label>
      <input type="text" name="parent_phone" 
             pattern="^(255\d{9}|0\d{9}|\+255\d{9}|\d{9})$" 
             oninput="validatePhone(this)" 
             onblur="formatPhone(this)"
             required 
             value="<?= htmlspecialchars($_POST['parent_phone'] ?? '') ?>">
      <div class="phone-format">Format: 255XXXXXXXXX, 0XXXXXXXXX, or +255XXXXXXXXX</div>
      
      <label>Start Date of Study *</label>
      <input type="date" name="start_date" required value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>">
      
      <label>End Date of Study *</label>
      <input type="date" name="end_date" required value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>">

      <label>Community *</label>
      <select name="community_id" required>
        <option value="">-- Select Community --</option>
        <?php 
        $communities->data_seek(0); // Reset pointer
        while ($c = $communities->fetch_assoc()) { ?>
          <option value="<?= $c['id'] ?>" <?= (isset($_POST['community_id']) && $_POST['community_id'] == $c['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['community_name']) ?>
          </option>
        <?php } ?>
      </select>

      <label>Unit *</label>
      <select name="unit_id" required>
        <option value="">-- Select Unit --</option>
        <?php 
        $units->data_seek(0); // Reset pointer
        while ($u = $units->fetch_assoc()) { ?>
          <option value="<?= $u['id'] ?>" <?= (isset($_POST['unit_id']) && $_POST['unit_id'] == $u['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($u['unit_name']) ?>
          </option>
        <?php } ?>
      </select>
      
      <button type="submit">✅ Register</button>
    </form>
  </div>

  <!-- Table of All Registered -->
  <div class="card">
    <h2>📋 All Registered Children</h2>
    <table id="kidsTable" class="display nowrap">
      <thead>
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Date of Birth</th>
          <th>Parent Name</th>
          <th>Parent Phone</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Community</th>
          <th>Unit</th>
          <th>Year Registered</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= $row['date_of_birth'] ?></td>
          <td><?= htmlspecialchars($row['parent_name']) ?></td>
          <td><?= htmlspecialchars($row['parent_phone']) ?></td>
          <td><?= $row['start_date'] ?></td>
          <td><?= $row['end_date'] ?></td>
          <td><?= htmlspecialchars($row['community_name']) ?></td>
          <td><?= htmlspecialchars($row['unit_name']) ?></td>
          <td><?= $row['year_registered'] ?></td>
        </tr>
      <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
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

$(document).ready(function() {
    $('#kidsTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '📊 Export to Excel'
            },
            {
                extend: 'print',
                text: '🖨️ Print',
                title: 'Watoto wa Ubatizo',
                customize: function ( win ) {
                    $(win.document.body)
                        .css('font-size', '12pt')
                        .prepend('<h2 style="text-align:center;">⛪ Watoto wa Ubatizo</h2><hr>');
                }
            }
        ],
        responsive: true
    });
});
</script>

</body>
</html>