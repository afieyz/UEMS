<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM events ORDER BY date ASC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>All Events</title>
<link rel="stylesheet" href="admin.css">
</head>

<body>
<div class="dashboard-container">
<h2 class="page-title">All Events</h2>

<table class="table">
<thead>
<tr>
  <th>#</th>
  <th>Event Title</th>
  <th>Date</th>
  <th>Status</th>
  <th>Action</th>
</tr>
</thead>

<tbody>
<?php foreach ($events as $i => $e): ?>
<tr>
  <td><?= $i+1 ?></td>
  <td><?= htmlspecialchars($e['title']) ?></td>
  <td><?= $e['date'] ?></td>
  <td><?= ucfirst($e['status']) ?></td>
  <td><a class="btn" href="view_proposal.php?event_id=<?= $e['event_id'] ?>">View</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<a href="dashboard.php" class="btn">Back</a>
</div>
</body>
</html>
