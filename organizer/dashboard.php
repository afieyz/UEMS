<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch organizer events
$stmt = $pdo->prepare("
    SELECT event_id, title, event_date, status, num_participants 
    FROM events 
    WHERE created_by = ? 
    ORDER BY event_date DESC
");
$stmt->execute([$user_id]);
$my_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_events = count($my_events);

// Fetch total participants
$stmt2 = $pdo->prepare("
    SELECT COALESCE(SUM(cnt),0) FROM (
        SELECT COUNT(*) AS cnt 
        FROM registrations r
        JOIN events e ON r.event_id = e.event_id
        WHERE e.created_by = ?
        GROUP BY r.event_id
    ) t
");
$stmt2->execute([$user_id]);
$total_participants = $stmt2->fetchColumn();

// Fetch unread notifications
$stmt3 = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt3->execute([$user_id]);
$unreadCount = $stmt3->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Organizer Dashboard</title>
    <link rel="stylesheet" href="../assets/css/organizer.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>

<body class="dashboard-body">

<!-- HEADER -->
<nav class="top-navbar">
    <div class="navbar-left">
      <span class="navbar-logo">University Event Management</span>
      <div class="hamburger" onclick="document.querySelector('.navbar-right').classList.toggle('show')">☰</div>
    </div>

    <div class="navbar-right">
      <a href="../index.php">Home</a>
      <a href="../joined_events.php">Joined Events</a>
      <a href="../event/create_event.php">Create Event</a>
      <a href="../profile.php">My Profile</a>
      <a href="dashboard.php">Organizer Dashboard</a>

      <a href="../notifications.php" class="notif-icon">
        🔔
        <?php if ($unreadCount > 0): ?>
          <span class="notif-badge"><?= $unreadCount ?></span>
        <?php endif; ?>
      </a>

      <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
</nav>

<!-- MAIN -->
<main class="org-main">

    <h1 class="org-title">Organizer Dashboard</h1>

    <section class="overview">
        <div class="card small">
            <h3>Total Created Events</h3>
            <p class="big-number"><?= $total_events ?></p>
        </div>

        <div class="card small">
            <h3>Total Participants</h3>
            <p class="big-number"><?= intval($total_participants) ?></p>
        </div>

        <div class="card action">
            <h3>Quick Actions</h3>
            <div class="actions">
                <a href="../event/create_event.php" class="btn">Create Event</a>
                <a href="../events.php" class="btn outline">Browse Events</a>
            </div>
        </div>
    </section>

    <section class="my-events">
        <h2>My Created Events</h2>

        <?php if ($total_events == 0): ?>
            <div class="empty">
                <p>You haven't created any events yet.</p>
                <a href="../event/create_event.php" class="btn">Create Your First Event</a>
            </div>
        <?php else: ?>
            <div class="events-list">
                <?php foreach ($my_events as $ev): ?>
                    <article class="event-item">

                        <div class="event-main">
                            <h3><?= htmlspecialchars($ev['title']) ?></h3>
                            <p class="muted">
                                <?= date('d M Y', strtotime($ev['event_date'])) ?> —
                                <strong><?= ucfirst($ev['status']) ?></strong>
                            </p>
                        </div>

                        <div class="event-meta">
                            <span class="chip">Participants: <?= $ev['num_participants'] ?? 0 ?></span>
                            <a href="manage_event.php?event_id=<?= $ev['event_id'] ?>" class="btn small">Manage</a>
                            <a href="event_participants.php?event_id=<?= $ev['event_id'] ?>" class="btn outline small">Participants</a>
                            <a href="event_feedback.php?event_id=<?= $ev['event_id'] ?>" class="btn outline small">Feedback</a>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </section>

</main>

<!-- FOOTER -->
<footer class="main-footer profile-footer">
  &copy; <?= date('Y') ?> Universiti Malaysia Sabah | Contact JHEP
</footer>

</body>
</html>
