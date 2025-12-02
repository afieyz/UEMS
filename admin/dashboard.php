<?php
// author : NUR-AFIDA BINTI MUHD AZUAN TIONG (BI22110453)
session_start();
require '../config/db.php';

<<<<<<< HEAD
// Check if admin
=======
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

<<<<<<< HEAD
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

=======
// Summary stats
$totalApprovedEvents = $pdo->query("SELECT COUNT(*) FROM events WHERE status = 'approved'")->fetchColumn();
$pendingEvents = $pdo->query("SELECT COUNT(*) FROM events WHERE status = 'pending'")->fetchColumn();
$weeklySignups = $pdo->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

// Faculty map
$facultyMap = [
  'Fakulti Pengajian Islam' => 'FIS',
  'Akademi Seni Dan Teknologi Kreatif' => 'ASTIF',
  'Fakulti Perniagaan, Ekonomi Dan Perakaunan' => 'FPEP',
  'Fakulti Komputeran Dan Informatik' => 'FKI',
  'Fakulti Kejuruteraan' => 'FKJ',
  'Fakulti Sains Makanan Dan Pemakanan' => 'FSMP',
  'Fakulti Sains Sosial Dan Kemanusiaan' => 'FSSK',
  'Fakulti Perhutanan Tropika' => 'FPT',
  'Fakulti Perubatan Dan Sains Kesihatan' => 'FPSK',
  'Fakulti Psikologi Dan Pendidikan' => 'FPP',
  'Fakulti Psikologi dan Kerja Sosial' => 'FPKS',
  'Fakulti Sains Dan Sumber Alam' => 'FSSA',
  'Fakulti Kewangan Antarabangsa Labuan' => 'FKAL',
  'Fakulti Pertanian Lestari' => 'FPL'
];

// Registered users by faculty (user + organizer)
$participantCounts = [];
$stmt = $pdo->prepare("SELECT faculty, COUNT(*) AS total FROM users WHERE role IN ('user', 'organizer') AND faculty IS NOT NULL GROUP BY faculty");
$stmt->execute();
$results = $stmt->fetchAll();

foreach ($results as $row) {
  $facultyName = $row['faculty'];
  if (isset($facultyMap[$facultyName])) {
    $shortCode = $facultyMap[$facultyName];
    $participantCounts[$shortCode] = (int)$row['total'];
  }
}

// Event type distribution (approved only)
$eventTypeCounts = [];
$stmt = $pdo->prepare("SELECT category, COUNT(*) AS total FROM events WHERE status = 'approved' GROUP BY category");
$stmt->execute();
$results = $stmt->fetchAll();

foreach ($results as $row) {
  $type = $row['category'] ?? 'Uncategorized';
  $eventTypeCounts[$type] = (int)$row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - Event System</title>
  <link rel="stylesheet" href="../assets/css/admin.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- TOP BAR -->
<div class="admin-topbar">
  <div class="top-left">
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

<!-- DASHBOARD -->
<div class="dashboard-container">
  <h2 class="page-title">Welcome, Admin</h2>
<<<<<<< HEAD

=======
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
  <div class="grid">

    <div class="card">
      <h3>Total Events</h3>
<<<<<<< HEAD
      <p class="big-number"><?= intval($totalEvents) ?></p>
=======
      <p class="big-number"><?= intval($totalApprovedEvents) ?></p>
      <p class="card-subtext">Event Created</p>
      <a href="events_summary.php">View Created Events</a>
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
    </div>

    <div class="card">
      <h3>Event Proposal</h3>
      <p class="big-number"><?= intval($pendingEvents) ?></p>
<<<<<<< HEAD
      <a href="event_manager.php">View Proposals</a>
=======
      <a href="event_manager.php">Review Proposals</a>
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
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

<<<<<<< HEAD

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
=======
  <!-- CHARTS -->
  <div class="chart-section">
    <h3>Participants by Faculty</h3>
    <canvas id="facultyChart" height="100"></canvas>
  </div>

  <div class="chart-section">
    <h3>Event Type Distribution</h3>
    <canvas id="eventTypeChart" height="100"></canvas>
  </div>
</div>

<!-- FOOTER -->
<footer class="admin-footer">
  <img src="../assets/images/logo.png" class="footer-logo" alt="">
  <span>University Event Management</span>
</footer>

<!-- CHART DATA -->
<script>
  const chartLabels = <?= json_encode(array_keys($participantCounts)) ?>;
  const chartData = <?= json_encode(array_values($participantCounts)) ?>;
  const eventTypeLabels = <?= json_encode(array_keys($eventTypeCounts)) ?>;
  const eventTypeData = <?= json_encode(array_values($eventTypeCounts)) ?>;
</script>

<!-- CHART RENDERING -->
<script>
  const ctx1 = document.getElementById('facultyChart').getContext('2d');
  new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: chartLabels,
      datasets: [{ label: 'Registered Users by Faculty', data: chartData, backgroundColor: '#0984e3' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
  });

  const ctx2 = document.getElementById('eventTypeChart').getContext('2d');
  new Chart(ctx2, {
    type: 'pie',
    data: {
      labels: eventTypeLabels,
      datasets: [{ data: eventTypeData, backgroundColor: ['#6c5ce7', '#00b894', '#fdcb6e', '#0984e3', '#d63031', '#fab1a0', '#636e72'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'right' } } }
  });
</script>

<script>
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.admin-topbar') && !e.target.closest('.admin-mobile-menu')) {
      document.body.classList.remove('nav-open');
    }
  });
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
</script>

</body>
</html>
