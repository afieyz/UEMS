<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch unread notifications
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$unreadCount = $stmt->fetchColumn();

// Avatar
$avatar = (!empty($user['avatar'])) ? "uploads/" . $user['avatar'] : "assets/images/icon.png";

// Check if user has created ANY event
$stmt = $pdo->prepare("SELECT COUNT(*) FROM events WHERE created_by = ?");
$stmt->execute([$user_id]);
$eventCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - University Event Management</title>
  <link rel="stylesheet" href="assets/css/profile.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body class="dashboard-body">

<!-- NAVBAR -->
<nav class="top-navbar">
    <div class="navbar-left">
      <span class="navbar-logo">University Event Management</span>
      <div class="hamburger" onclick="document.querySelector('.navbar-right').classList.toggle('show')">☰</div>
    </div>

    <div class="navbar-right">
      <a href="index.php">Home</a>
      <a href="joined_events.php">Joined Events</a>
      <a href="event/create_event.php">Create Event</a>
      <a href="profile.php">My Profile</a>

      <!-- DASHBOARD -->
      <?php if ($eventCount > 0): ?>
        <a href="organizer/dashboard.php">Dashboard</a>
      <?php endif; ?>
      <!-- END BUTTON -->

      <a href="notifications.php" class="notif-icon">
        🔔
        <?php if ($unreadCount > 0): ?>
          <span class="notif-badge"><?= $unreadCount ?></span>
        <?php endif; ?>
      </a>

      <a href="auth/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<main class="dashboard-content">
  <h1>My Profile</h1>

  <section class="profile-section">
    <div class="profile-card">

      <div class="profile-avatar">
        <img src="<?= $avatar ?>" alt="Profile Picture">
      </div>

      <div class="profile-content">

        <div class="profile-field">
          <label>Name</label>
          <p><?= htmlspecialchars($user['name']) ?></p>
        </div>

        <div class="profile-field">
          <label>Matric Number</label>
          <p><?= htmlspecialchars($user['student_id']) ?></p>
        </div>

        <div class="profile-field">
          <label>Email</label>
          <p><?= htmlspecialchars($user['email']) ?></p>
        </div>

        <div class="profile-field">
          <label>Faculty</label>
          <p><?= htmlspecialchars($user['faculty']) ?></p>
        </div>

        <div class="profile-field">
          <label>Phone Number</label>
          <p><?= htmlspecialchars($user['phone_num']) ?></p>
        </div>

        <div class="profile-field">
          <label>Year of Study</label>
          <p><?= htmlspecialchars($user['year']) ?></p>
        </div>

        <a href="update_profile.php" class="edit-profile-btn">Edit Profile</a>

      </div>
    </div>

  </section>
</main>

<footer class="main-footer profile-footer">
  &copy; 2025 Universiti Malaysia Sabah | Contact JHEP
</footer>

</body>
</html>
