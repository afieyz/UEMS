<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* CHECK IF USER IS ORGANIZER 
   Organizer = user yang Pernah create event */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE created_by = ?");
$stmt->execute([$user_id]);
$isOrganizer = $stmt->fetchColumn() > 0;

if (!$isOrganizer) {
    echo "<script>alert('Access denied. You have not created any event.'); 
          window.location='../index.php';</script>";
    exit;
}

$page_title = "Event Feedback";
$selectedEventId = $_GET['event_id'] ?? null;

/* Organizer boleh tengok hanya event dia saja */
$stmt = $pdo->prepare("
    SELECT event_id, title 
    FROM events 
    WHERE created_by = ? AND status = 'approved'
    ORDER BY event_date DESC
");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll();

/* Fetch feedback */
$feedbacks = [];
if ($selectedEventId) {
    $stmt = $pdo->prepare("
        SELECT f.*, u.name 
        FROM feedback f
        LEFT JOIN users u ON f.user_id = u.user_id
        WHERE f.event_id = ?
        ORDER BY f.feedback_date DESC
    ");
    $stmt->execute([$selectedEventId]);
    $feedbacks = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($page_title) ?></title>
<link rel="stylesheet" href="../assets/css/admin.css">
<link rel="stylesheet" href="../assets/css/profile.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
.feedback-summary {
    font-weight: 500;
    margin-bottom: 12px;
    color: #333;
}
.report-table td:nth-child(3) {
    max-width: 350px;
    white-space: pre-wrap;
}
</style>
</head>

<body>

<!-- HEADER -->
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

<!-- CONTENT -->
<div class="dashboard-container" style="margin-top: 100px;">
    <h2 class="page-title"><?= htmlspecialchars($page_title) ?></h2>

    <?php if (count($events) > 0): ?>
        <form method="GET" style="margin-bottom: 20px;">
            <label><strong>Select Event:</strong></label>
            <select name="event_id" onchange="this.form.submit()">
                <option value="">-- Choose an event --</option>
                <?php foreach ($events as $e): ?>
                    <option value="<?= $e['event_id'] ?>" <?= ($selectedEventId == $e['event_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    <?php else: ?>
        <p style="text-align:center; color:gray;">You have no approved events.</p>
    <?php endif; ?>

    <!-- FEEDBACK TABLE -->
    <?php if ($selectedEventId && count($feedbacks) > 0): ?>

        <?php $avgRating = array_sum(array_column($feedbacks, 'rating')) / count($feedbacks); ?>

        <p class="feedback-summary">
            Average Rating: <strong><?= round($avgRating,2) ?></strong> / 5 
            (<?= count($feedbacks) ?> responses)
        </p>

        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Submitted</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($feedbacks as $f): ?>
                    <tr>
                        <td><?= htmlspecialchars($f['name'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($f['rating']) ?>/5</td>
                        <td><?= nl2br(htmlspecialchars($f['comment'])) ?></td>
                        <td><?= date("F j, Y", strtotime($f['feedback_date'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($selectedEventId): ?>
        <p style="text-align:center; color:gray;">No feedback available for this event.</p>
    <?php endif; ?>

</div>

<footer class="profile-footer">
    &copy; <?= date("Y") ?> Universiti Malaysia Sabah
</footer>

</body>
</html>
