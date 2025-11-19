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
  <div class="admin-section">

    <h2 class="page-title"><?= htmlspecialchars($page_title) ?></h2>

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

              <td class="<?= $r['avg_rating'] !== null && $r['avg_rating'] < 3 ? 'low-rating' : '' ?>">
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

</body>
</html>
