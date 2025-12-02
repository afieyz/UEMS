<?php
session_start();
require '../config/db.php';

// USER CHECK
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

// CHECK IF EVENT BELONGS TO ORGANIZER
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ? AND created_by = ?");
$stmt->execute([$eventId, $organizerId]);
$event = $stmt->fetch();

if (!$event) {
    echo "You do not have access to this event.";
    exit;
}

// GET PARTICIPANTS (FROM registrations)
$stmt = $pdo->prepare("
    SELECT u.user_id, u.name, a.attendance_status
    FROM registrations r
    JOIN users u ON r.user_id = u.user_id
    LEFT JOIN attendance a 
           ON a.user_id = r.user_id 
          AND a.event_id = r.event_id
    WHERE r.event_id = ?
      AND r.status = 'registered'
");
$stmt->execute([$eventId]);
$participants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attendance - <?= htmlspecialchars($event['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/attendance.css">
</head>

<body>
<div class="attendance-container">

    <h2>Mark Attendance for: <br><?= htmlspecialchars($event['title']) ?></h2>

    <form action="attendance_submit.php" method="post">
        <input type="hidden" name="event_id" value="<?= $eventId ?>">

        <?php if (count($participants) > 0): ?>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                </tr>

                <?php foreach ($participants as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td>
                            <select name="status[<?= $p['user_id'] ?>]">
                                <option value="present" <?= $p['attendance_status'] === 'present' ? 'selected' : '' ?>>
                                    Present
                                </option>
                                <option value="absent" <?= $p['attendance_status'] === 'absent' ? 'selected' : '' ?>>
                                    Absent
                                </option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </table>

            <button type="submit">Save Attendance</button>

        <?php else: ?>

            <p style="margin-top:20px; font-weight:600; color:#777;">
                No participants have registered for this event.
            </p>

        <?php endif; ?>
    </form>

    <a href="../organizer/dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>
</body>
</html>
