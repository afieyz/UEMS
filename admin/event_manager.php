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
<body>

<!-- HEADER -->
<header class="admin-header">
  <h1>University Event Management</h1>

  <div class="admin-hamburger" onclick="toggleMenu()">☰</div>

  <nav id="adminNavMenu">
    <ul>
      <li><a href="../index.php">Home</a></li>
      <li><a href="../events.php">All Events</a></li>
      <li><a href="event_manager.php" class="active">Proposals</a></li>
      <li><a href="feedback.php">Feedback</a></li>
      <li><a href="reports.php">Reports</a></li>
      <li><a href="../auth/logout.php" class="login-btn">Logout</a></li>
    </ul>
  </nav>
</header>


<!-- CONTENT -->
<div class="dashboard-container">
  <h2 class="page-title">Pending Event Proposals</h2>

  <div class="table-container">

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
                <a href="event_detail.php?event_id=<?= $event['event_id'] ?>" class="btn btn-view">View</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    <?php else: ?>

      <div class="empty-box">
        No pending proposals found.
      </div>

    <?php endif; ?>

  </div> <!-- end table-container -->
</div> <!-- end dashboard-container -->



<!-- FOOTER -->
<footer class="admin-footer">
  <img src="../assets/images/logo.png" class="footer-logo" alt="">
  <span>University Event Management</span>
</footer>

<!-- JS FOR MENU -->
<script>
function toggleMenu() {
  document.getElementById('adminNavMenu').classList.toggle('show-menu');
}

document.addEventListener('click', function(e) {
  if (!e.target.closest('.admin-header') && !e.target.closest('#adminNavMenu')) {
    document.getElementById('adminNavMenu').classList.remove('show-menu');
  }
});
</script>

</body>
</html>
