<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$organizerId = $_SESSION['user_id'];
$eventId = $_GET['event_id'] ?? null;

if (!$eventId) {
    echo "Invalid event ID.";
    exit;
}

// VALIDATE EVENT BELONGS TO ORGANIZER  (FIXED: created_by)
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ? AND created_by = ?");
$stmt->execute([$eventId, $organizerId]);
$event = $stmt->fetch();

if (!$event) {
    echo "You do not have access to this event.";
    exit;
}

// GET ATTENDANCE RECORDS
$stmt = $pdo->prepare("
  SELECT u.name, a.attendance_status, a.scan_time
  FROM attendance a
  JOIN users u ON a.user_id = u.user_id
  WHERE a.event_id = ?
  ORDER BY u.name
");
$stmt->execute([$eventId]);
$records = $stmt->fetchAll();

// COUNT STATS
$total = count($records);
$present = count(array_filter($records, fn($r) => $r['attendance_status'] === 'present'));
$absent = $total - $present;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Attendance Report - <?= htmlspecialchars($event['title']) ?></title>
  <link rel="stylesheet" href="../assets/css/attendance.css">
</head>
<body>

<div class="attendance-container">
  
    <h2>Attendance Report: <?= htmlspecialchars($event['title']) ?></h2>

    <p><strong>Total Participants:</strong> <?= $total ?></p>
    <p><strong>Present:</strong> <?= $present ?></p>
    <p><strong>Absent:</strong> <?= $absent ?></p>

    <table>
      <tr><th>Name</th><th>Status</th><th>Scan Time</th></tr>

      <?php foreach ($records as $r): ?>
        <tr>
          <td><?= htmlspecialchars($r['name']) ?></td>
          <td><?= ucfirst($r['attendance_status']) ?></td>
          <td><?= $r['scan_time'] ? date('d M Y, h:i A', strtotime($r['scan_time'])) : '-' ?></td>
        </tr>
      <?php endforeach; ?>

    </table>

    <a href="../organizer/dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>

</body>
</html>
