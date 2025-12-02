<?php
session_start();
require '../config/db.php';

<<<<<<< HEAD
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$eventId = $_GET['event_id'] ?? null;

// CHECK organizer — must have created event
$stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE created_by = ?");
$stmt->execute([$user_id]);
$isOrganizer = $stmt->fetchColumn() > 0;

if (!$isOrganizer) {
    echo "<script>alert('Access denied. You have not created any event.'); 
          window.location='../index.php';</script>";
    exit;
}

// VALIDATE event belongs to organizer
$stmt = $pdo->prepare("
    SELECT title, event_date 
    FROM events 
    WHERE event_id = ? AND created_by = ?
");
$stmt->execute([$eventId, $user_id]);
$event = $stmt->fetch();

if (!$event) {
    echo "<script>alert('You cannot access this event.'); 
          window.location='dashboard.php';</script>";
    exit;
}

// FETCH participants
$stmt = $pdo->prepare("
    SELECT u.name, u.student_id, u.faculty, r.registration_date
    FROM registrations r
    JOIN users u ON r.user_id = u.user_id
    WHERE r.event_id = ?
    ORDER BY r.registration_date ASC
");
$stmt->execute([$eventId]);
$participants = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Participants - <?= htmlspecialchars($event['title']) ?></title>

    <!-- FIXED: USE ORGANIZER UI CSS -->
    <link rel="stylesheet" href="../assets/css/organizer.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>

<body class="dashboard-body">

<nav class="top-navbar">
    <div class="navbar-left">
      <span class="navbar-logo">Organizer Panel</span>
      <div class="hamburger" onclick="document.querySelector('.navbar-right').classList.toggle('show')">☰</div>
    </div>

    <div class="navbar-right">
      <a href="dashboard.php">Dashboard</a>
      <a href="../event/create_event.php">Create Event</a>
      <a href="../profile.php">My Profile</a>
      <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<main class="dashboard-content">

    <h1>Participants for: <strong><?= htmlspecialchars($event['title']) ?></strong></h1>
    <p><strong>Event Date:</strong> <?= date("d M Y", strtotime($event['event_date'])) ?></p>

    <?php if (count($participants) == 0): ?>
        <div class="event-details-box" style="text-align:center;">
            <p>No participants yet.</p>
        </div>
    <?php else: ?>

        <div class="table-container">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Matric No</th>
                        <th>Faculty</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($participants as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= htmlspecialchars($p['student_id']) ?></td>
                        <td><?= htmlspecialchars($p['faculty']) ?></td>
                        <td><?= date("d M Y, g:i A", strtotime($p['registration_date'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</main>

<footer class="profile-footer">
  © <?= date('Y') ?> Universiti Malaysia Sabah | Contact JHEP
</footer>

</body>
</html>
=======
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
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
