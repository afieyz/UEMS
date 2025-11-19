<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

// Get pending events
$stmt = $pdo->prepare("
  SELECT e.event_id, e.title, e.event_date, u.name AS organizer_name, u.faculty
  FROM events e
  JOIN users u ON e.created_by = u.user_id
  WHERE e.status = 'pending'
  ORDER BY e.event_date ASC
");
$stmt->execute();
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Proposals</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

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
  <div class="admin-section">

    <h2 class="page-title">Pending Event Proposals</h2>

    <?php if (count($events) > 0): ?>
      <table class="report-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Organizer</th>
            <th>Faculty</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($events as $event): ?>
            <tr>
              <td><?= htmlspecialchars($event['title']) ?></td>
              <td><?= htmlspecialchars($event['organizer_name']) ?></td>
              <td><?= htmlspecialchars($event['faculty']) ?></td>
              <td><?= date('d M Y', strtotime($event['event_date'])) ?></td>
              <td>
                <a href="event_detail.php?event_id=<?= $event['event_id'] ?>" class="top-link">View</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No pending proposals found.</p>
    <?php endif; ?>

  </div> <!-- end admin-section -->
</div> <!-- end dashboard-container -->

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