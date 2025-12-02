<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$eventId = $_GET['event_id'] ?? null;

// CHECK organizer — must have created event
$stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE created_by = ?");
$stmt->execute([$user_id]);
$isOrganizer = $stmt->fetchColumn() > 0;

if (!$isOrganizer) {
    echo "<script>alert('Access denied. You have not created any event.'); 
          window.location='../index.php';</script>";
    exit;
}

// VALIDATE event belongs to organizer
$stmt = $pdo->prepare("
    SELECT title, event_date 
    FROM events 
    WHERE event_id = ? AND created_by = ?
");
$stmt->execute([$eventId, $user_id]);
$event = $stmt->fetch();

if (!$event) {
    echo "<script>alert('You cannot access this event.'); 
          window.location='dashboard.php';</script>";
    exit;
}

// FETCH participants
$stmt = $pdo->prepare("
    SELECT u.name, u.student_id, u.faculty, r.registration_date
    FROM registrations r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.event_id = ?
    ORDER BY r.registration_date ASC
");
$stmt->execute([$eventId]);
$participants = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Participants - <?= htmlspecialchars($event['title']) ?></title>

    <!-- FIXED: USE ORGANIZER UI CSS -->
    <link rel="stylesheet" href="../assets/css/organizer.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>

<body class="dashboard-body">

<nav class="top-navbar">
    <div class="navbar-left">
      <span class="navbar-logo">Organizer Panel</span>
      <div class="hamburger" onclick="document.querySelector('.navbar-right').classList.toggle('show')">☰</div>
    </div>

    <div class="navbar-right">
      <a href="dashboard.php">Dashboard</a>
      <a href="../event/create_event.php">Create Event</a>
      <a href="../profile.php">My Profile</a>
      <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<main class="dashboard-content">

    <h1>Participants for: <strong><?= htmlspecialchars($event['title']) ?></strong></h1>
    <p><strong>Event Date:</strong> <?= date("d M Y", strtotime($event['event_date'])) ?></p>

    <?php if (count($participants) == 0): ?>
        <div class="event-details-box" style="text-align:center;">
            <p>No participants yet.</p>
        </div>
    <?php else: ?>

        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Matric No</th>
                        <th>Faculty</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($participants as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['student_id']) ?></td>
                        <td><?= htmlspecialchars($p['faculty']) ?></td>
                        <td><?= date("d M Y, g:i A", strtotime($p['registration_date'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</main>

<footer class="profile-footer">
  © <?= date('Y') ?> Universiti Malaysia Sabah | Contact JHEP
</footer>

</body>
</html>
