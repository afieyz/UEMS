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
<<<<<<< HEAD
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

=======
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
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
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
<<<<<<< HEAD
                <a href="event_detail.php?event_id=<?= $event['event_id'] ?>" class="btn btn-view">View</a>
=======
                <a href="event_detail.php?event_id=<?= $event['event_id'] ?>" class="top-link">View</a>
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
<<<<<<< HEAD

    <?php else: ?>

      <div class="empty-box">
        No pending proposals found.
      </div>

    <?php endif; ?>

  </div> <!-- end table-container -->
</div> <!-- end dashboard-container -->



=======
    <?php else: ?>
      <p>No pending proposals found.</p>
    <?php endif; ?>

  </div> <!-- end admin-section -->
</div> <!-- end dashboard-container -->

>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
<!-- FOOTER -->
<footer class="admin-footer">
  <img src="../assets/images/logo.png" class="footer-logo" alt="">
  <span>University Event Management</span>
</footer>

<<<<<<< HEAD
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
=======
<script>
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.admin-topbar') && !e.target.closest('.admin-mobile-menu')) {
      document.body.classList.remove('nav-open');
    }
  });
</script>

</body>
</html>
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
