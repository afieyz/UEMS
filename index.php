<?php
//NUR-AFIDA BINTI MUHD AZUAN TIONG (BI22110453)
session_start();
require 'config/db.php';

$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

// Fetch events
$stmt = $pdo->prepare("SELECT event_id, title, location, event_date, event_image FROM events WHERE status = 'approved' ORDER BY event_date ASC");
$stmt->execute();
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>University Event Management</title>

  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body class="home-bg">

<header class="index-header">
  <h1>University Event Management</h1>
  <div class="hamburger" onclick="toggleMenu()">☰</div>

  <nav id="navMenu">
    <ul>
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="events.php">Events</a></li>
      <li><a href="news.php">News</a></li>
      <li><a href="event/create_event.php">Create Events</a></li>
      <li><a href="joined_events.php">Joined Events</a></li>

      <?php if (isset($_SESSION['user_id'])): ?>
        <li><a href="profile.php" class="profile-link">My Profile</a></li>

        <?php if ($isAdmin): ?>
          <li><a href="admin/dashboard.php">Back to Dashboard</a></li>
        <?php endif; ?>

        <li><a href="auth/logout.php" class="login-btn">Logout</a></li>

      <?php else: ?>
        <li><a href="auth/login.php" class="login-btn">Login</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<section class="hero">
  <div class="hero-content">
    <h2>Welcome to the University Event Management System</h2>
    <p>Discover, organize, and participate in exciting campus events easily.</p>
  </div>
</section>

<section class="header-section">
  <h2>ALL EVENTS • ONE PLATFORM</h2>
  <div class="search-bar">
    <input type="text" placeholder="Search your events...">
    <button>Search</button>
  </div>
</section>

<!-- EVENT SECTION (FIXED IMAGE PATH) -->
<section class="event-section">
  <h3>Upcoming Events</h3>

  <div class="event-grid">
    <?php if (count($events) > 0): ?>
      <?php foreach ($events as $event): ?>

        <?php 
          // image path
          $imagePath = !empty($event['event_image']) 
            ? "uploads/events/" . $event['event_image'] 
            : "images/default_event.jpg";
        ?>

        <div class="event-card">

          <img 
            src="<?= htmlspecialchars($imagePath) ?>" 
            alt="<?= htmlspecialchars($event['title']) ?>"
          >

          <div class="event-card-content">
            <h4><?= htmlspecialchars($event['title']) ?></h4>
            <p><?= date('d M Y', strtotime($event['event_date'])) ?> | <?= htmlspecialchars($event['location']) ?></p>
            <a class="join-btn" href="event_details.php?event_id=<?= $event['event_id'] ?>">View Event</a>

          </div>

        </div>

      <?php endforeach; ?>
    <?php else: ?>
      <p class="no-events">No upcoming events yet. Please check back soon!</p>
    <?php endif; ?>
  </div>
</section>

<section class="bottom-section">
  <div class="calendar">
    <h3>Events Calendar</h3>
    <div id="calendar"></div>
  </div>

  <div class="announcement">
    <h3>Latest Announcement</h3>
    <p>No new announcements yet.</p>
  </div>
</section>

<section class="features">
  <div class="features-container">
    <h2>Why Use This System?</h2>

    <div class="features-grid">
      <div class="feature-card">
        <h3>🎉 Discover Events</h3>
        <p>Stay updated with the latest university and faculty events happening around campus.</p>
      </div>

      <div class="feature-card">
        <h3>📝 Easy Registration</h3>
        <p>Register for events instantly and receive confirmation directly in your account.</p>
      </div>

      <div class="feature-card">
        <h3>📊 Organized Management</h3>
        <p>For organizers, manage event proposals, approvals, and participants seamlessly.</p>
      </div>
    </div>
  </div>
</section>

<footer class="main-footer">
  &copy; 2025 Universiti Malaysia Sabah | Contact JHEP
</footer>

<div id="eventPopup" class="popup">
  <h4 id="popupTitle"></h4>
  <p id="popupDate"></p>
  <button onclick="closePopup()">Close</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');
  var popup = document.getElementById('eventPopup');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    events: 'fetch_events.php',
    eventClick: function(info) {
      document.getElementById('popupTitle').innerText = info.event.title;
      document.getElementById('popupDate').innerText = info.event.start.toDateString();
      popup.style.display = 'block';
    }
  });

  calendar.render();
});

function closePopup() {
  document.getElementById('eventPopup').style.display = 'none';
}

function toggleMenu() {
  document.getElementById("navMenu").classList.toggle("show-menu");
}
</script>

</body>
</html>
