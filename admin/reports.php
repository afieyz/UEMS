<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

$page_title = "Event Reports";

// Get approved events with feedback summary
$stmt = $pdo->prepare("
  SELECT e.event_id, e.title, e.category, e.event_date,
         u.name AS organizer_name, u.faculty,
         COUNT(f.feedback_id) AS total_feedback,
         ROUND(AVG(f.rating), 2) AS avg_rating
  FROM events e
  JOIN users u ON e.created_by = u.user_id
  LEFT JOIN feedback f ON e.event_id = f.event_id
  WHERE e.status = 'approved'
  GROUP BY e.event_id
  ORDER BY e.event_date DESC
");
$stmt->execute();
$reports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    .zero-feedback { color: #d63031; font-weight: 600; }
    .low-rating { color: #e17055; font-weight: 600; }
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
        <li><a href="feedback.php">Feedback</a></li>
        <li><a href="reports.php" class="active">Reports</a></li>
        <li><a href="../auth/logout.php" class="login-btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>


<!-- CONTENT -->
<div class="dashboard-container">

  <h2 class="page-title"><?= htmlspecialchars($page_title) ?></h2>

  <div class="table-container">
    <?php if (count($reports) > 0): ?>
      <table class="report-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Organizer</th>
            <th>Faculty</th>
            <th>Date</th>
            <th>Category</th>
            <th>Total Feedback</th>
            <th>Average Rating</th>
          </tr>
        </thead>

        <tbody>
        <?php foreach ($reports as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['organizer_name']) ?></td>
            <td><?= htmlspecialchars($r['faculty']) ?></td>
            <td><?= date('d M Y', strtotime($r['event_date'])) ?></td>
            <td><?= htmlspecialchars($r['category']) ?></td>

            <td class="<?= $r['total_feedback'] == 0 ? 'zero-feedback' : '' ?>">
              <?= $r['total_feedback'] ?>
            </td>

            <td class="<?= ($r['avg_rating'] !== null && $r['avg_rating'] < 3) ? 'low-rating' : '' ?>">
              <?= $r['avg_rating'] !== null ? $r['avg_rating'] . ' / 5' : '-' ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>

      </table>
    <?php else: ?>
      <p style="text-align:center; color:gray;">No approved events found.</p>
    <?php endif; ?>
  </div>

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
