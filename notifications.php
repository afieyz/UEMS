<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Ambil semua notifikasi user
$stmt = $pdo->prepare("SELECT message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$notifications = $stmt->fetchAll();

// Tandakan semua sebagai dibaca
$pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$userId]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Notifications - University Event Management</title>
  <link rel="stylesheet" href="assets/css/notifications.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
</head>
<body>
  <div class="notif-container">
    <h1>Notifications</h1>

    <?php if (count($notifications) === 0): ?>
      <p class="empty-msg">You have no notifications.</p>
    <?php else: ?>
      <ul class="notif-list">
        <?php foreach ($notifications as $n): ?>
          <li>
            <div class="notif-message"><?= htmlspecialchars($n['message']) ?></div>
            <div class="notif-time"><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?></div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <a href="profile.php" class="back-btn">← Back to Profile</a>
  </div>
</body>
</html>