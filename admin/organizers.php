<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $organizerId = $_POST['organizer_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
        $stmt->execute([$organizerId]);
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'rejected' WHERE user_id = ?");
        $stmt->execute([$organizerId]);
    }
}

$stmt = $pdo->prepare("
  SELECT users.*, MIN(events.created_at) AS first_submission
  FROM users
  LEFT JOIN events ON events.created_by = users.user_id
  WHERE users.role = 'organizer'
  GROUP BY users.user_id
  ORDER BY first_submission DESC
");
$stmt->execute();
$organizers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Organizers</title>
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
  <h2 class="page-title">Manage Organizers</h2>
  <table class="report-table">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Faculty</th>
        <th>Phone</th>
        <th>Status</th>
        <th>First Submission</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($organizers as $org): ?>
        <tr>
          <td><?= htmlspecialchars($org['name']) ?></td>
          <td><?= htmlspecialchars($org['email']) ?></td>
          <td><?= htmlspecialchars($org['faculty'] ?? '-') ?></td>
          <td><?= htmlspecialchars($org['phone'] ?? '-') ?></td>
          <td><?= htmlspecialchars($org['status'] ?? 'N/A') ?></td>
          <td><?= $org['first_submission'] ? date('F j, Y', strtotime($org['first_submission'])) : '—' ?></td>
          <td>
            <?php if (($org['status'] ?? '') === 'pending'): ?>
              <form method="POST" class="inline-form">
                <input type="hidden" name="organizer_id" value="<?= $org['user_id'] ?>">
                <button type="submit" name="action" value="approve">Approve</button>
                <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
              </form>
            <?php else: ?>
              <span style="color: gray;">—</span>
            <?php endif; ?>
          </td>
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