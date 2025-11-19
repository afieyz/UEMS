<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

// Get approved events only
$stmt = $pdo->prepare("
  SELECT e.title, e.event_date, e.category, u.name AS organizer_name, u.faculty
  FROM events e
  JOIN users u ON e.created_by = u.user_id
  WHERE e.status = 'approved'
  ORDER BY e.event_date DESC
");
$stmt->execute();
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Approved Events Summary</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

<!-- TOP BAR -->
<div class="admin-topbar">
  <div class="top-left">
    <a href="dashboard.php" class="top-link back-arrow">← Back</a>
    <a href="./index.php" class="top-link">Home</a>
  </div>
  <div class="top-toggle" onclick="document.body.classList.toggle('nav-open')">☰</div>
  <div class="top-right">
    <a href="./auth/logout.php" class="top-link logout">Logout</a>
  </div>
</div>

<!-- MOBILE MENU -->
<div class="admin-mobile-menu">
  <a href="./index.php">Home</a>
  <a href="./auth/logout.php">Logout</a>
</div>

<!-- CONTENT (kekalkan bahagian asal) -->


<!-- CONTENT -->
<div class="admin-section">
  <h2>Approved Events</h2>

  <?php if (count($events) > 0): ?>
    <table class="report-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Organizer</th>
          <th>Faculty</th>
          <th>Date</th>
          <th>Category</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($events as $event): ?>
          <tr>
            <td><?= htmlspecialchars($event['title']) ?></td>
            <td><?= htmlspecialchars($event['organizer_name']) ?></td>
            <td><?= htmlspecialchars($event['faculty']) ?></td>
            <td><?= date('d M Y', strtotime($event['event_date'])) ?></td>
            <td><?= htmlspecialchars($event['category']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No approved events found.</p>
  <?php endif; ?>
</div>

<footer class="admin-footer">
  <img src="./assets/images/logo.png" class="footer-logo" alt="">
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