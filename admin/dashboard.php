<?php
// author : NUR-AFIDA BINTI MUHD AZUAN TIONG (BI22110453)
session_start();
require '../config/db.php';

// Check if admin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Total Events
$stmt = $pdo->query("SELECT COUNT(*) FROM events");
$totalEvents = $stmt->fetchColumn();

// Pending Event Proposals
$stmt = $pdo->query("SELECT COUNT(*) FROM events WHERE status = 'pending'");
$pendingEvents = $stmt->fetchColumn();

// Weekly Signups
$stmt = $pdo->query("
    SELECT COUNT(*) 
    FROM registrations 
    WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$weeklySignups = $stmt->fetchColumn();

/* NEW: Participants by Faculty */
$facultyStmt = $pdo->query("
    SELECT 
        u.faculty,
        COUNT(r.registration_id) AS total
    FROM registrations r
    JOIN users u ON r.user_id = u.user_id
    WHERE u.faculty IS NOT NULL AND u.faculty != ''
    GROUP BY u.faculty
");
$facultyData = $facultyStmt->fetchAll(PDO::FETCH_ASSOC);

/* Event Type Distribution*/
$typeStmt = $pdo->query("
    SELECT 
        category,
        COUNT(event_id) AS total
    FROM events
    WHERE status = 'approved'
      AND category IS NOT NULL 
      AND category != ''
    GROUP BY category
");
$typeData = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../assets/css/admin.css">

  <script>
    function toggleMenu() {
      document.getElementById('adminNavMenu').classList.toggle('show-menu');
    }
  </script>
</head>

<body>

<!-- HEADER -->
<header class="admin-header">
  <h1>University Event Management</h1>
  <div class="admin-hamburger" onclick="toggleMenu()">☰</div>

  <nav id="adminNavMenu">
    <ul>
      <li><a href="../index.php">Home</a></li>
      <li><a href="../events.php">All Events</a></li>
      <li><a href="feedback.php">Feedback</a></li>
      <li><a href="../auth/logout.php" class="login-btn">Logout</a></li>
    </ul>
  </nav>
</header>


<!-- DASHBOARD -->
<div class="dashboard-container">
  <h2 class="page-title">Welcome, Admin</h2>

  <div class="grid">

    <div class="card">
      <h3>Total Events</h3>
      <p class="big-number"><?= intval($totalEvents) ?></p>
    </div>

    <div class="card">
      <h3>Event Proposal</h3>
      <p class="big-number"><?= intval($pendingEvents) ?></p>
      <a href="event_manager.php">View Proposals</a>
    </div>

    <div class="card">
      <h3>Weekly Signups</h3>
      <p class="big-number">+<?= intval($weeklySignups) ?></p>
    </div>

    <div class="card">
      <h3>Event Feedback</h3>
      <p>View participant ratings & comments</p>
      <a href="feedback.php">View Feedback</a>
    </div>

    <div class="card">
      <h3>Event Reports</h3>
      <p>View attendance, ratings & stats</p>
      <a href="reports.php">View Reports</a>
    </div>

  </div>


  <!--  CHART SECTION -->
  <div class="chart-section">

    <div class="chart-box">
        <h3>Participants by Faculty</h3>
        <canvas id="facultyChart"></canvas>
    </div>

    <div class="chart-box">
        <h3>Event Type Distribution</h3>
        <canvas id="eventTypeChart"></canvas>
    </div>

  </div>

</div>


<!-- FOOTER -->
<footer class="admin-footer">
  <img class="footer-logo" src="../images/logo.png" alt="logo">
  <span>UMS Event Management System © 2025</span>
</footer>


<!-- CHART.JS SCRIPT-->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // PHP → JS Arrays
    const facultyLabels = <?= json_encode(array_column($facultyData, 'faculty')) ?>;
    const facultyValues = <?= json_encode(array_column($facultyData, 'total')) ?>;

    const eventTypeLabels = <?= json_encode(array_column($typeData, 'category')) ?>;
    const eventTypeValues = <?= json_encode(array_column($typeData, 'total')) ?>;

    // Bar Chart
    new Chart(document.getElementById('facultyChart'), {
        type: 'bar',
        data: {
            labels: facultyLabels,
            datasets: [{
                label: 'Participants',
                data: facultyValues,
                backgroundColor: '#FFCC33',
                borderColor: '#E0A800',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Pie Chart
    new Chart(document.getElementById('eventTypeChart'), {
        type: 'pie',
        data: {
            labels: eventTypeLabels,
            datasets: [{
                data: eventTypeValues,
                backgroundColor: [
                    '#6a4dff', '#ff6384', '#36a2eb',
                    '#ffcd56', '#4bc0c0', '#b38b6d'
                ]
            }]
        },
        options: { responsive: true }
    });
</script>

</body>
</html>
