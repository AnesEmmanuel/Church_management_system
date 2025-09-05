<?php
// keepalive.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Update the session activity time
$_SESSION['LAST_ACTIVITY'] = time();

// Return success response
header('Content-Type: application/json');
echo json_encode(['success' => true]);
exit;