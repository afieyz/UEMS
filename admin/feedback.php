<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['admin', 'organizer'])) {
    header("Location: ../auth/login.php");
    exit;
}

$page_title = "Event Feedback";
$selectedEventId = $_GET['event_id'] ?? null;

// Role-based event list
if ($_SESSION['user_role'] === 'admin') {
    $events = $pdo->query("SELECT event_id, title FROM events WHERE status = 'approved' ORDER BY created_at DESC")->fetchAll();
<<<<<<< HEAD
} else {
    $stmt = $pdo->prepare("SELECT event_id, title FROM events WHERE status = 'approved' AND created_by = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $events = $stmt->fetchAll();
}

// Fetch feedback
=======
} elseif ($_SESSION['user_role'] === 'organizer') {
    $stmt = $pdo->prepare("SELECT event_id, title FROM events WHERE status = 'approved' AND created_by = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $events = $stmt->fetchAll();
} else {
    $events = [];
}

>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
$feedbacks = [];
if ($selectedEventId) {
    $stmt = $pdo->prepare("
        SELECT f.*, u.name 
        FROM feedback f 
        LEFT JOIN users u ON f.user_id = u.user_id 
        WHERE f.event_id = ?
    ");
    $stmt->execute([$selectedEventId]);
    $feedbacks = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
<<<<<<< HEAD
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    .feedback-summary {
      font-weight: 500;
      margin-bottom: 12px;
      color: #333;
    }
    .report-table td:nth-child(3) {
      max-width: 350px;
=======
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    .feedback-summary {
      margin-bottom: 15px;
      font-weight: 500;
      color: #2d3436;
    }
    .report-table td:nth-child(3) {
      max-width: 300px;
      word-wrap: break-word;
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
      white-space: pre-wrap;
    }
  </style>
</head>
<body>

<<<<<<< HEAD
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
        <li><a href="feedback.php" class="active">Feedback</a></li>
        <li><a href="reports.php">Reports</a></li>
        <li><a href="../auth/logout.php" class="login-btn">Logout</a></li>
      </ul>
    </nav>
  </div>
</header>

=======
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
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3

<!-- CONTENT -->
<div class="dashboard-container">
  <h2 class="page-title"><?= htmlspecialchars($page_title) ?></h2>

  <?php if (count($events) > 0): ?>
<<<<<<< HEAD
    <form method="GET" style="margin-bottom: 25px;">
      <label for="event_id"><strong>Select Event:</strong></label>
=======
    <form method="GET" style="margin-bottom: 20px;">
      <label for="event_id">Select Event:</label>
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
      <select name="event_id" id="event_id" onchange="this.form.submit()">
        <option value="">-- Choose an event --</option>
        <?php foreach ($events as $e): ?>
          <option value="<?= $e['event_id'] ?>" <?= $selectedEventId == $e['event_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($e['title']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  <?php else: ?>
<<<<<<< HEAD
    <p style="text-align:center; color:gray;">No approved events available.</p>
  <?php endif; ?>


  <?php if ($selectedEventId && count($feedbacks) > 0): ?>

    <?php 
      $avgRating = array_sum(array_column($feedbacks, 'rating')) / count($feedbacks);
    ?>

    <p class="feedback-summary">
      Average Rating: <strong><?= round($avgRating, 2) ?></strong> / 5  
      (<?= count($feedbacks) ?> responses)
    </p>

    <div class="table-container">
      <table class="report-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Submitted</th>
          </tr>
        </thead>
        <tbody>
=======
    <p style="text-align:center; color:gray;">No approved events available for your role.</p>
  <?php endif; ?>

  <?php if ($selectedEventId && count($feedbacks) > 0): ?>
    <?php
      $avgRating = array_sum(array_column($feedbacks, 'rating')) / count($feedbacks);
    ?>
    <p class="feedback-summary">Average Rating: <?= round($avgRating, 2) ?> / 5 (<?= count($feedbacks) ?> responses)</p>

    <table class="report-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Submitted</th>
        </tr>
      </thead>
      <tbody>
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
        <?php foreach ($feedbacks as $f): ?>
          <tr>
            <td><?= htmlspecialchars($f['name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($f['rating']) ?>/5</td>
            <td><?= nl2br(htmlspecialchars($f['comment'])) ?></td>
<<<<<<< HEAD
            <td><?= date('F j, Y', strtotime($f['feedback_date'])) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  <?php elseif ($selectedEventId): ?>
    <p style="text-align:center; color:gray;">No feedback available for this event.</p>
  <?php endif; ?>

</div>


=======
            <td><?= date('F j, Y', strtotime($f['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php elseif ($selectedEventId): ?>
    <p style="text-align:center; color:gray;">No feedback found for this event.</p>
  <?php elseif (count($events) > 0): ?>
    <p style="text-align:center; color:gray;">Please select an event to view its feedback.</p>
  <?php endif; ?>
</div>

>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
<!-- FOOTER -->
<footer class="admin-footer">
  <img src="../assets/images/logo.png" class="footer-logo" alt="">
  <span>University Event Management</span>
</footer>

<<<<<<< HEAD

<script>
function toggleMenu() {
  document.getElementById('adminNavMenu').classList.toggle('show-menu');
}
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
