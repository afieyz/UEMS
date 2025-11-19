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
} elseif ($_SESSION['user_role'] === 'organizer') {
    $stmt = $pdo->prepare("SELECT event_id, title FROM events WHERE status = 'approved' AND created_by = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $events = $stmt->fetchAll();
} else {
    $events = [];
}

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    .feedback-summary {
      margin-bottom: 15px;
      font-weight: 500;
      color: #2d3436;
    }
    .report-table td:nth-child(3) {
      max-width: 300px;
      word-wrap: break-word;
      white-space: pre-wrap;
    }
  </style>
</head>
<body>

<!-- TOP BAR -->
<div class="admin-topbar">
  <div class="top-left">
    <a href="dashboard.php" class="top-link back-arrow">← Back</a>
    <a href="../index.php" class="top-link">Home</a>
  </div>
  <div class="top-toggle" onclick="document.body.classList.toggle('nav-open')">☰</div>
  <div class="top-right">
    <a href="../auth/logout.php" class="top-link logout">Logout</a>
  </div>
</div>

<!-- MOBILE MENU -->
<div class="admin-mobile-menu">
  <a href="../index.php">Home</a>
  <a href="../auth/logout.php">Logout</a>
</div>

<!-- CONTENT -->
<div class="dashboard-container">
  <h2 class="page-title"><?= htmlspecialchars($page_title) ?></h2>

  <?php if (count($events) > 0): ?>
    <form method="GET" style="margin-bottom: 20px;">
      <label for="event_id">Select Event:</label>
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
    <p style="text-align:center; color:gray;">No approved events available for your role.</p>
  <?php endif; ?>

  <?php if ($selectedEventId && count($feedbacks) > 0): ?>
    <?php
      $avgRating = array_sum(array_column($feedbacks, 'rating')) / count($feedbacks);
    ?>
    <p class="feedback-summary">Average Rating: <?= round($avgRating, 2) ?> / 5 (<?= count($feedbacks) ?> responses)</p>

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
            <td><?= date('F j, Y', strtotime($f['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php elseif ($selectedEventId): ?>
    <p style="text-align:center; color:gray;">No feedback found for this event.</p>
  <?php elseif (count($events) > 0): ?>
    <p style="text-align:center; color:gray;">Please select an event to view its feedback.</p>
  <?php endif; ?>
</div>

<!-- FOOTER -->
<footer class="admin-footer">
  <img src="../assets/images/logo.png" class="footer-logo" alt="">
  <span>University Event Management</span>
</footer>

<script>
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.admin-topbar') && !e.target.closest('.admin-mobile-menu')) {
      document.body.classList.remove('nav-open');
    }
  });
</script>

</body>
</html>