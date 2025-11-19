<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'organizer') {
  header("Location: ../auth/login.php");
  exit;
}

$eventId = $_GET['event_id'] ?? null;
if (!$eventId) {
  echo "Invalid event ID.";
  exit;
}

// Get event info
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ? AND created_by = ?");
$stmt->execute([$eventId, $_SESSION['user_id']]);
$event = $stmt->fetch();
if (!$event) {
  echo "Event not found or unauthorized.";
  exit;
}

// Handle search
$search = $_GET['search'] ?? '';
$faculty = $_GET['faculty'] ?? '';

// Get participants
$query = "SELECT users.user_id, users.name, users.faculty, users.email, attendance.attendance_status
          FROM registrations
          JOIN users ON registrations.user_id = users.user_id
          LEFT JOIN attendance ON attendance.user_id = users.user_id AND attendance.event_id = registrations.event_id
          WHERE registrations.event_id = ?";
$params = [$eventId];

if ($search) {
  $query .= " AND users.name LIKE ?";
  $params[] = "%$search%";
}
if ($faculty) {
  $query .= " AND users.faculty = ?";
  $params[] = $faculty;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$participants = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Participants - <?= htmlspecialchars($event['title']) ?></title>
  <link rel="stylesheet" href="../assets/css/organizer.css" />
</head>
<body class="participants-body">

<div class="organizer-topbar">
  <div class="top-left"><a href="dashboard.php" class="text-link">← Back</a></div>
  <div class="top-right"><a href="../auth/logout.php" class="btn-logout">Logout</a></div>
</div>

<div class="section-wrapper">
  <h2 class="section-title">Participants for: <?= htmlspecialchars($event['title']) ?></h2>

  <!-- Search + Filter -->
  <form method="get" class="filter-row" style="margin-bottom: 20px;">
    <input type="hidden" name="event_id" value="<?= $eventId ?>">
    <input type="text" name="search" placeholder="Search name..." value="<?= htmlspecialchars($search) ?>">
    <select name="faculty">
      <option value="">All Faculties</option>
      <?php foreach (['FIS','ASTIF','FPEP','FKI','FKJ','FSMP','FSSK','FPT','FPSK','FPP','FPKS','FSSA','FKAL','FPL'] as $fac): ?>
        <option value="<?= $fac ?>" <?= $faculty === $fac ? 'selected' : '' ?>><?= $fac ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="filter-btn">Filter</button>
    <a href="export_participants_csv.php?event_id=<?= $eventId ?>" class="filter-btn" style="background:#ddd;color:#000;">Export CSV</a>
  </form>

  <?php if (count($participants) > 0): ?>
    <form method="post" action="mark_attendance.php">
      <input type="hidden" name="event_id" value="<?= $eventId ?>">
      <table class="participants-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Faculty</th>
            <th>Email</th>
            <th>Present</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($participants as $p): ?>
            <tr>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['faculty']) ?></td>
              <td><?= htmlspecialchars($p['email']) ?></td>
              <td>
                <input type="checkbox" name="present[]" value="<?= $p['user_id'] ?>" <?= $p['attendance_status'] === 'present' ? 'checked' : '' ?>>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <button type="submit" class="filter-btn" style="margin-top: 15px;">Save Attendance</button>
    </form>
  <?php else: ?>
    <div class="no-participants-box">
      <p>No participants registered yet.</p>
    </div>
  <?php endif; ?>
</div>

</body>
</html>