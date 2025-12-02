<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    die("Missing event ID.");
}

// Check if event belongs to organizer
$stmt = $pdo->prepare("SELECT title FROM events WHERE event_id = ? AND created_by = ?");
$stmt->execute([$event_id, $user_id]);
$event = $stmt->fetch();

if (!$event) {
    die("You are not authorized for this event.");
}

// Fetch participant list
$stmt2 = $pdo->prepare("
    SELECT u.name, u.email, u.faculty, r.registration_date
    FROM registrations r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.event_id = ?
    ORDER BY r.registration_date DESC
");
$stmt2->execute([$event_id]);
$participants = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Participants - <?= htmlspecialchars($event['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/organizer.css">
</head>

<body>
<h1>Participants for: <?= htmlspecialchars($event['title']) ?></h1>

<?php if (count($participants) == 0): ?>
    <p>No participants yet.</p>
<?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Faculty</th>
            <th>Registered At</th>
        </tr>
        <?php foreach ($participants as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['email']) ?></td>
                <td><?= htmlspecialchars($p['faculty']) ?></td>
                <td><?= htmlspecialchars($p['registration_date']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

</body>
</html>
