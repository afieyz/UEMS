<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$eventId = $_GET['event_id'] ?? null;
if (!$eventId) {
    echo "Missing event ID.";
    exit;
}

// Get event + organizer info
$stmt = $pdo->prepare("
  SELECT e.*, u.name AS organizer_name, u.faculty
  FROM events e
  JOIN users u ON e.created_by = u.user_id
  WHERE e.event_id = ?
");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    echo "Event not found.";
    exit;
}

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $feedback = $_POST['feedback'] ?? null;

    if ($action === 'approve') {
        // NO FEEDBACK COLUMN → remove it
        $stmt = $pdo->prepare("UPDATE events SET status = 'approved' WHERE event_id = ?");
        $stmt->execute([$eventId]);

    } elseif ($action === 'reject') {
        // Save feedback into extra_info (safe column)
        $stmt = $pdo->prepare("UPDATE events SET status = 'rejected', extra_info = ? WHERE event_id = ?");
        $stmt->execute([$feedback, $eventId]);
    }

    header("Location: event_manager.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Proposal Detail</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<!-- ===================== ADMIN HEADER (FINAL) ===================== -->
<header class="admin-header">
    <h1>Event Proposal Detail</h1>

    <div class="admin-nav-wrapper">
        <span class="hamburger" onclick="toggleMenu()">☰</span>

        <nav id="adminNavMenu">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="admin_all_events.php">All Events</a></li>
                <li><a href="event_manager.php">Proposals</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../auth/logout.php" class="logout">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>

<script>
function toggleMenu() {
    document.getElementById('adminNavMenu').classList.toggle('show-menu');
}
</script>
<!-- ===================== END HEADER ===================== -->


<div class="dashboard-container">
    <h2 class="page-title">Event Proposal Detail</h2>

    <!-- EVENT DETAILS BOX -->
    <div class="event-details-box">
        <p><strong>Title:</strong> <?= htmlspecialchars($event['title']) ?></p>
        <p><strong>Organizer:</strong> <?= htmlspecialchars($event['organizer_name']) ?> (<?= htmlspecialchars($event['faculty']) ?>)</p>
        <p><strong>Date:</strong> <?= date('d M Y', strtotime($event['event_date'])) ?> (<?= date('l', strtotime($event['event_date'])) ?>)</p>
        <p><strong>Time:</strong> <?= $event['start_time'] ?> – <?= $event['end_time'] ?></p>
        <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
        <p><strong>Category:</strong> <?= htmlspecialchars($event['category']) ?></p>
        <p><strong>Activity Level:</strong> <?= htmlspecialchars($event['activity_level']) ?></p>
        <p><strong>Max Participants:</strong> <?= htmlspecialchars($event['num_participants'] ?? 'N/A') ?></p>
        <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($event['description'])) ?></p>

        <?php if (!empty($event['proposal_file'])): ?>
        <p><strong>Proposal File:</strong>
            <a href="../uploads/proposals/<?= $event['proposal_file'] ?>" target="_blank">View PDF</a> |
            <a href="../uploads/proposals/<?= $event['proposal_file'] ?>" download>Download</a>
        </p>
        <?php endif; ?>
    </div>

    <!-- APPROVAL FORM -->
    <form method="post">
        <label><strong>Feedback (only if rejected):</strong></label>
        <textarea name="feedback" rows="3" placeholder="Optional feedback..." style="width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;"></textarea>

        <div style="margin-top:15px; display:flex; gap:10px;">
            <button type="submit" name="action" value="approve" class="btn-approve">Approve</button>
            <button type="submit" name="action" value="reject" class="btn-reject">Reject</button>
        </div>
    </form>

</div>

<footer class="admin-footer">
    <img src="../assets/images/logo.png" class="footer-logo" alt="">
    <span>University Event Management</span>
</footer>

</body>
</html>
