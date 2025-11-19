<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  header("Location: ../auth/login.php");
  exit;
}

$eventId = $_GET['event_id'] ?? null;
if (!$eventId) {
  echo "Missing event ID.";
  exit;
}

// Get event + organizer info
$stmt = $pdo->prepare("
  SELECT e.*, u.name AS organizer_name, u.faculty
  FROM events e
  JOIN users u ON e.created_by = u.user_id
  WHERE e.event_id = ?
");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
  echo "Event not found.";
  exit;
}

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $action = $_POST['action'];
  $feedback = $_POST['feedback'] ?? null;

  if ($action === 'approve') {
    $stmt = $pdo->prepare("UPDATE events SET status = 'approved', feedback = NULL WHERE event_id = ?");
    $stmt->execute([$eventId]);
  } elseif ($action === 'reject') {
    $stmt = $pdo->prepare("UPDATE events SET status = 'rejected', feedback = ? WHERE event_id = ?");
    $stmt->execute([$feedback, $eventId]);
  }

  header("Location: event_manager.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Proposal Detail</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<!-- TOP BAR -->
<div class="admin-topbar">
  <div class="top-left">
    <a href="event_manager.php" class="top-link back-arrow">← Back</a>
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

<!-- PAGE CONTENT -->
<div class="dashboard-container">
  <div class="admin-section">

    <h2 class="page-title">Event Proposal Detail</h2>

    <!-- EVENT DETAILS BOX -->
    <ul class="event-details-box">
      <li><strong>Title:</strong> <?= htmlspecialchars($event['title']) ?></li>
      <li><strong>Organizer:</strong> <?= htmlspecialchars($event['organizer_name']) ?> (<?= $event['faculty'] ?>)</li>
      <li><strong>Date:</strong> <?= date('d M Y', strtotime($event['event_date'])) ?> (<?= date('l', strtotime($event['event_date'])) ?>)</li>
      <li><strong>Time:</strong> <?= $event['start_time'] ?> – <?= $event['end_time'] ?></li>
      <li><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></li>
      <li><strong>Category:</strong> <?= $event['category'] ?></li>
      <li><strong>Activity Level:</strong> <?= $event['activity_level'] ?></li>
      <li><strong>Participants:</strong> <?= $event['num_participants'] ?? 'N/A' ?></li>
      <li><strong>Description:</strong> <?= nl2br(htmlspecialchars($event['description'] ?? 'Tiada')) ?></li>

      <?php if (!empty($event['proposal_file'])): ?>
        <li><strong>Proposal File:</strong>
          <a href="../uploads/proposals/<?= $event['proposal_file'] ?>" target="_blank">View PDF</a>
          | <a href="../uploads/proposals/<?= $event['proposal_file'] ?>" download>Download</a>
        </li>
      <?php endif; ?>
    </ul>

    <!-- APPROVAL FORM -->
    <form method="post">
      <label for="feedback">Feedback (if rejected):</label>
      <textarea name="feedback" rows="3" placeholder="Optional feedback..." style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;"></textarea>

      <div style="margin-top:15px; display:flex; gap:10px;">
        <button type="submit" name="action" value="approve" class="btn-approve">Approve</button>
        <button type="submit" name="action" value="reject" class="btn-reject">Reject</button>
      </div>
    </form>

  </div>
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
