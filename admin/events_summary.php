<?php
// author : NUR-AFIDA BINTI MUHD AZUAN TIONG (BI22110453)
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
  <title>Approved Events Summary - Event System</title>

  <link rel="stylesheet" href="../assets/css/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<!-- HEADER (SAMA SEBIJI MACAM DASHBOARD) -->
<header class="admin-header">
    <h1>Admin Dashboard</h1>

    <div class="hamburger" onclick="toggleAdminMenu()">☰</div>

    <nav id="adminNavMenu">
        <ul>
            <li><a href="dashboard.php">Back</a></li>
            <li><a href="../index.php">Home</a></li>
            <li><a href="../auth/logout.php" class="logout">Logout</a></li>
        </ul>
    </nav>
</header>

<!-- SPACING BELOW HEADER -->
<div style="height:110px;"></div>


<!-- CONTENT -->
<div class="dashboard-container">
  <h2 class="page-title">Approved Events</h2>

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

<!-- FOOTER -->
<footer class="admin-footer">
  <img src="../assets/images/logo.png" class="footer-logo" alt="">
  <span>University Event Management</span>
</footer>

<script>
function toggleAdminMenu() {
    document.getElementById("adminNavMenu").classList.toggle("show-menu");
}
</script>

</body>
</html>
