<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

$page_title = "Registered Users";
$stmt = $pdo->prepare("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
  <h2 class="page-title"><?= htmlspecialchars($page_title) ?></h2>
  <table class="report-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Student ID</th>
        <th>Faculty</th>
        <th>Gender</th>
        <th>Phone</th>
        <th>Year</th>
        <th>Status</th>
        <th>Joined</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= htmlspecialchars($u['name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['student_id'] ?? '-') ?></td>
          <td><?= htmlspecialchars($u['faculty'] ?? '-') ?></td>
          <td><?= htmlspecialchars($u['gender'] ?? '-') ?></td>
          <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
          <td><?= htmlspecialchars($u['year'] ?? '-') ?></td>
          <td><?= htmlspecialchars($u['status'] ?? 'N/A') ?></td>
          <td><?= $u['created_at'] ? date('F j, Y', strtotime($u['created_at'])) : '-' ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
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