<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'organizer'])) {
    header("Location: ../auth/login.php");
    exit;
}

$page_title = "Event Feedback";
$selectedEventId = $_GET['event_id'] ?? null;

// Role-based event list
if ($_SESSION['user_role'] === 'admin') {
    $events = $pdo->query("SELECT event_id, title FROM events WHERE status = 'approved' ORDER BY created_at DESC")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT event_id, title FROM events WHERE status = 'approved' AND created_by = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $events = $stmt->fetchAll();
}

// Fetch feedback
$feedbacks = [];
if ($selectedEventId) {
    $stmt = $pdo->prepare("
        SELECT f.*, u.name 
        FROM feedback f 
        LEFT JOIN users u ON f.user_id = u.user_id 
        WHERE f.event_id = ?
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
<header class="admin-header">
  <h1><?= htmlspecialchars($page_title) ?></h1>

  <div class="admin-nav-wrapper">
    <span class="admin-hamburger" onclick="toggleMenu()">☰</span>

    <nav id="adminNavMenu">
      <ul>
        <li><a href="../index.php">Home</a></li>
        <li><a href="../events.php">All Events</a></li>
        <li><a href="event_manager.php">Proposals</a></li>
        <li><a href="feedback.php" class="active">Feedback</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="../auth/logout.php" class="login-btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>


<!-- CONTENT -->
<div class="dashboard-container">
  <h2 class="page-title"><?= htmlspecialchars($page_title) ?></h2>

  <?php if (count($events) > 0): ?>
    <form method="GET" style="margin-bottom: 25px;">
      <label for="event_id"><strong>Select Event:</strong></label>
      <select name="event_id" id="event_id" onchange="this.form.submit()">
        <option value="">-- Choose an event --</option>
        <?php foreach ($events as $e): ?>
          <option value="<?= $e['event_id'] ?>" <?= $selectedEventId == $e['event_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($e['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  <?php else: ?>
    <p style="text-align:center; color:gray;">No approved events available.</p>
  <?php endif; ?>


  <?php if ($selectedEventId && count($feedbacks) > 0): ?>

    <?php 
      $avgRating = array_sum(array_column($feedbacks, 'rating')) / count($feedbacks);
    ?>

    <p class="feedback-summary">
      Average Rating: <strong><?= round($avgRating, 2) ?></strong> / 5  
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
            <td><?= date('F j, Y', strtotime($f['feedback_date'])) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  <?php elseif ($selectedEventId): ?>
    <p style="text-align:center; color:gray;">No feedback available for this event.</p>
  <?php endif; ?>

</div>


<!-- FOOTER -->
<footer class="admin-footer">
  <img src="../assets/images/logo.png" class="footer-logo" alt="">
  <span>University Event Management</span>
</footer>


<script>
function toggleMenu() {
  document.getElementById('adminNavMenu').classList.toggle('show-menu');
}
</script>

</body>
</html>
