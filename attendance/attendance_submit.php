<?php
session_start();
require '../config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$organizerId = $_SESSION['user_id'];
$eventId = $_POST['event_id'] ?? null;
$statusList = $_POST['status'] ?? [];

// Validate submission
if (!$eventId || empty($statusList)) {
    echo "Invalid attendance submission.";
    exit;
}

// Validate organizer ownership (FIXED)
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ? AND created_by = ?");
$stmt->execute([$eventId, $organizerId]);
$event = $stmt->fetch();

if (!$event) {
    echo "You do not have access to this event.";
    exit;
}

// PROCESS ATTENDANCE (SAFE INSERT or UPDATE)
foreach ($statusList as $userId => $status) {

    // Check if attendance record exists
    $check = $pdo->prepare("
        SELECT attendance_id 
        FROM attendance 
        WHERE user_id = ? AND event_id = ?
    ");
    $check->execute([$userId, $eventId]);

    if ($check->rowCount() > 0) {
        // UPDATE existing attendance
        $update = $pdo->prepare("
            UPDATE attendance
            SET attendance_status = ?, scan_time = NOW()
            WHERE user_id = ? AND event_id = ?
        ");
        $update->execute([$status, $userId, $eventId]);

    } else {
        // INSERT new attendance
        $insert = $pdo->prepare("
            INSERT INTO attendance (user_id, event_id, attendance_status, scan_time)
            VALUES (?, ?, ?, NOW())
        ");
        $insert->execute([$userId, $eventId, $status]);
    }
}

// Redirect with success flag
header("Location: attendance.php?event_id=$eventId&saved=1");
exit;
?>
