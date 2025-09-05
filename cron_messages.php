<?php
// cron_messages.php
require_once __DIR__ . '/db.php';

// 1) Create daily prayers messages for today's day
$today = date('l'); // Monday, Tuesday, ...
$stmt = $conn->prepare("SELECT prayer_text FROM prayers WHERE day_of_week = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) {
    $content = $r['prayer_text'];
    $title = "Daily Prayer - $today";
    $recipient = 'members';
    $ins = $conn->prepare("INSERT INTO messages (recipient, title, content) VALUES (?,?,?)");
    $ins->bind_param("sss",$recipient,$title,$content);
    $ins->execute();
    $ins->close();
}
$stmt->close();

// 2) If Saturday, add Sunday mass reminder
if ($today === 'Saturday') {
    $recipient = 'members';
    $title = 'Sunday Mass Reminder';
    $content = 'Reminder: Sunday Mass tomorrow. Please join us for the service.';
    $ins = $conn->prepare("INSERT INTO messages (recipient, title, content) VALUES (?,?,?)");
    $ins->bind_param("sss",$recipient,$title,$content);
    $ins->execute();
    $ins->close();
}

// 3) Create notifications for events happening today
$today_date = date('Y-m-d');
$stmt2 = $conn->prepare("SELECT event_name FROM events WHERE event_date = ?");
$stmt2->bind_param("s",$today_date);
$stmt2->execute();
$res2 = $stmt2->get_result();
while ($e = $res2->fetch_assoc()) {
    $recipient = 'leaders';
    $title = 'Event Today: ' . $e['event_name'];
    $content = 'Event "' . $e['event_name'] . '" is scheduled for today.';
    $ins = $conn->prepare("INSERT INTO messages (recipient, title, content) VALUES (?,?,?)");
    $ins->bind_param("sss",$recipient,$title,$content);
    $ins->execute();
    $ins->close();
}
$stmt2->close();

echo "Cron messages created at " . date('Y-m-d H:i:s') . PHP_EOL;
