<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch all joined events
$stmt = $pdo->prepare("
    SELECT e.*, r.registration_date
    FROM registrations r
    JOIN events e ON r.event_id = e.event_id
    WHERE r.user_id = ?
    ORDER BY e.event_date ASC
");
$stmt->execute([$userId]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$today = date('Y-m-d');

$upcoming = [];
$past = [];

foreach ($events as $e) {
    if ($e['event_date'] >= $today) {
        $upcoming[] = $e;
    } else {
        $past[] = $e;
    }
}

function ftime($t){ return $t ? date("g:i A", strtotime($t)) : "-"; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Activity - Joined Events</title>
<link rel="stylesheet" href="assets/css/styles.css">
<link rel="stylesheet" href="assets/css/events.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
.page-wrapper { max-width: 1100px; margin:130px auto 80px; padding:20px; }
.section-title { font-size:22px; margin-bottom:10px; font-weight:600; }

.event-card {
    background:white;
    padding:18px;
    border-radius:12px;
    box-shadow:0 6px 20px rgba(0,0,0,0.06);
    display:flex;
    gap:20px;
    margin-bottom:18px;
}

.event-card img {
    width:160px;
    height:120px;
    object-fit:cover;
    border-radius:10px;
}

.event-info h3 { margin:0 0 6px; }
.event-info p { margin:4px 0; color:#444; }

.view-btn {
    margin-top:10px;
    display:inline-block;
    padding:8px 14px;
    border-radius:10px;
    text-decoration:none;
    background:linear-gradient(135deg,#ffb347,#ffcc33);
    color:#000;
    font-weight:600;
}
.empty-box {
    background:white;
    padding:30px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 6px 20px rgba(0,0,0,0.06);
    margin-top:20px;
    color:#666;
}
</style>
</head>

<body class="home-bg">

<!-- NAVBAR -->
<header class="index-header">
  <h1>University Event Management</h1>
  <div class="hamburger" onclick="toggleMenu()">☰</div>
  <nav id="navMenu">
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="events.php">Events</a></li>
      <li><a href="news.php">News</a></li>
      <li><a href="event/create_event.php">Create Events</a></li>
      <li><a class="active" href="my_activity.php">My Activity</a></li>
      <li><a href="profile.php">My Profile</a></li>
      <li><a href="auth/logout.php" class="login-btn">Logout</a></li>
    </ul>
  </nav>
</header>

<!-- PAGE -->
<div class="page-wrapper">

    <h2 class="section-title">Upcoming Joined Events</h2>
    <?php if (count($upcoming) == 0): ?>
        <div class="empty-box">No upcoming joined events.</div>
    <?php else: ?>
        <?php foreach ($upcoming as $e): 
            $img = !empty($e['event_image']) ? "uploads/events/" . $e['event_image'] : "images/default_event.jpg";
        ?>
            <div class="event-card">
                <img src="<?= $img ?>" alt="Event Image">
                <div class="event-info">
                    <h3><?= $e['title'] ?></h3>
                    <p><strong>Date:</strong> <?= date("d M Y", strtotime($e['event_date'])) ?></p>
                    <p><strong>Time:</strong> <?= ftime($e['start_time']) ?> - <?= ftime($e['end_time']) ?></p>
                    <p><strong>Location:</strong> <?= $e['location'] ?></p>
                    <a href="event_details.php?event_id=<?= $e['event_id'] ?>" class="view-btn">View Event</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <br><br>

    <h2 class="section-title">Past Joined Events</h2>
    <?php if (count($past) == 0): ?>
        <div class="empty-box">No past events.</div>
    <?php else: ?>
        <?php foreach ($past as $e): 
            $img = !empty($e['event_image']) ? "uploads/events/" . $e['event_image'] : "images/default_event.jpg";
        ?>
            <div class="event-card">
                <img src="<?= $img ?>" alt="Event Image">
                <div class="event-info">
                    <h3><?= $e['title'] ?></h3>
                    <p><strong>Date:</strong> <?= date("d M Y", strtotime($e['event_date'])) ?></p>
                    <p><strong>Location:</strong> <?= $e['location'] ?></p>
                    <a href="event_details.php?event_id=<?= $e['event_id'] ?>" class="view-btn">View Event</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<footer class="main-footer">
  © 2025 Universiti Malaysia Sabah | Contact JHEP
</footer>

<script>
function toggleMenu(){
    document.getElementById("navMenu").classList.toggle("show-menu");
}
</script>

</body>
</html>
