<?php
require_once 'db.php';

// Get filter
$year = $_GET['year'] ?? '';

// Build SQL
$sql = "SELECT d.member_name, d.phone, d.month_paid, d.amount, d.payment_method,
               c.community_name, u.unit_name, d.payment_date
        FROM donations d
        LEFT JOIN communities c ON d.community_id=c.id
        LEFT JOIN units u ON d.unit_id=u.id
        WHERE 1=1";

if ($year && preg_match('/^\d{4}$/', $year)) {
    $sql .= " AND YEAR(d.month_paid)=" . intval($year);
}

$sql .= " ORDER BY d.payment_date DESC";

$result = $conn->query($sql);

// Set headers for Excel
header("Content-Type: application/vnd.ms-excel");
$filename = "donations";
if ($year) $filename .= "_$year";
$filename .= ".xls";
header("Content-Disposition: attachment; filename=$filename");

// Output column headers
echo "Member Name\tPhone\tCommunity\tUnit\tMonth Paid\tAmount\tPayment Method\tDate Recorded\n";

// Output data rows
while ($row = $result->fetch_assoc()) {
    $month_paid = !empty($row['month_paid']) ? date('Y-m', strtotime($row['month_paid'])) : '';
    echo "{$row['member_name']}\t{$row['phone']}\t{$row['community_name']}\t{$row['unit_name']}\t{$month_paid}\t{$row['amount']}\t{$row['payment_method']}\t{$row['payment_date']}\n";
}
exit;
