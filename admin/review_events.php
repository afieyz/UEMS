<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$stmt = $pdo->query("
    SELECT e.*, u.name AS organizer_name
    FROM events e
    LEFT JOIN users u ON e.organizer_id = u.user_id
    ORDER BY e.created_at DESC
");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Review Event Proposals - Admin</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<div class="admin-topbar">
  <div class="top-left">
    <a href="dashboard.php" class="top-link">Dashboard</a>
  </div>

  <div class="top-toggle" onclick="document.body.classList.toggle('nav-open')">☰</div>

  <div class="top-right">
    <a href="../auth/logout.php" class="top-link logout">Logout</a>
  </div>
</div>

<div class="admin-mobile-menu">
  <a href="dashboard.php">Dashboard</a>
  <a href="../auth/logout.php">Logout</a>
</div>

<div class="dashboard-container">
  <h2 class="page-title">Review Event Proposals</h2>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Organizer</th>
        <th>Date</th>
        <th>Description</th>
        <th>Status</th>
        <th>Decision</th>
      </tr>
    </thead>

    <tbody>
    <?php if (count($events) == 0): ?>
      <tr>
        <td colspan="6" style="text-align:center;">No event proposals.</td>
      </tr>
    <?php else: ?>
      <?php foreach ($events as $event): ?>
        <tr>
          <td><?= htmlspecialchars($event['title']) ?></td>
          <td><?= htmlspecialchars($event['organizer_name'] ?? 'N/A') ?></td>
          <td><?= htmlspecialchars($event['event_date']) ?></td>
          <td style="max-width:240px;"><?= nl2br(htmlspecialchars($event['description'])) ?></td>
          <td><?= htmlspecialchars(ucfirst($event['status'])) ?></td>

          <td>
            <?php if ($event['status'] === 'pending'): ?>
              <form method="POST" action="review_process.php" class="inline-form">

                <input type="hidden" name="event_id" value="<?= $event['event_id'] ?>">

                <input type="text" name="feedback" placeholder="Rejection feedback (optional)">

                <button type="submit" name="action" value="approve">Approve</button>

                <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>

              </form>
            <?php else: ?>
              <em style="color:gray;">Completed</em>
            <?php endif; ?>
          </td>

        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</div>

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
