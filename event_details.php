<?php
session_start();
require 'config/db.php';

$eventId = $_GET['event_id'] ?? null;
if (!$eventId) {
    header('Location: events.php');
    exit;
}

// Fetch event
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Event not found.";
    exit;
}

$imagePath = !empty($event['event_image']) 
    ? 'uploads/events/' . $event['event_image'] 
    : 'images/default_event.jpg';

$loggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? null;

// prevent duplicate registration
$alreadyRegistered = false;
if ($loggedIn) {
    $chk = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE user_id = ? AND event_id = ?");
    $chk->execute([$userId, $eventId]);
    $alreadyRegistered = $chk->fetchColumn() > 0;
}

// payment check
$requiresPayment = (array_key_exists('payment_amount', $event) && floatval($event['payment_amount']) > 0);

function fTime($t){ return $t ? date("g:i A", strtotime($t)) : "-"; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($event['title']) ?> - Event Details</title>
<link rel="stylesheet" href="assets/css/events.css">
<link rel="stylesheet" href="assets/css/styles.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
.event-wrapper { max-width: 850px; margin:120px auto 80px; padding:20px; }
.event-hero { background:white; border-radius:12px; padding:20px; box-shadow:0 6px 20px rgba(0,0,0,0.06); }
.event-img { width:100%; max-height:320px; object-fit:cover; border-radius:12px; margin-bottom:18px; }

.detail-box { margin-bottom:15px; }
.detail-box label { font-weight:600; color:#333; display:block; margin-bottom:4px; }
.detail-box p { margin:0; color:#444; }

.join-btn { display:inline-block; padding:12px 18px; margin-top:20px;
    border-radius:10px; background:linear-gradient(135deg,#ffb347,#ffcc33);
    font-weight:600; color:#000; text-decoration:none; cursor:pointer; }
.btn-secondary { padding:10px 14px; background:#ddd; border-radius:8px; }

.popup-backdrop {
    position: fixed; inset: 0; background: rgba(0,0,0,0.5);
    display:none; justify-content:center; align-items:center; z-index:2000;
}
.popup-box {
    background:#fff; padding:20px; border-radius:12px;
    width:350px; text-align:center; box-shadow:0 10px 40px rgba(0,0,0,0.2);
}
.popup-btn-row { display:flex; justify-content:center; gap:10px; margin-top:20px; }
.btn-cancel { padding:8px 14px; background:#eee; border-radius:8px; cursor:pointer; }
.btn-confirm { padding:8px 14px; background:linear-gradient(135deg,#ffb347,#ffcc33); border-radius:8px; cursor:pointer; font-weight:600; }
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
      <li><a href="my_activity.php">My Activity</a></li>

      <?php if ($loggedIn): ?>
        <li><a href="profile.php">My Profile</a></li>
        <li><a href="auth/logout.php" class="login-btn">Logout</a></li>
      <?php else: ?>
        <li><a href="auth/login.php" class="login-btn">Login</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<!-- CONTENT -->
<div class="event-wrapper">

  <div class="event-hero">
    <img src="<?= htmlspecialchars($imagePath) ?>" class="event-img">

    <h2><?= htmlspecialchars($event['title']) ?></h2>

    <!-- PLACE -->
    <div class="detail-box">
      <label>Place</label>
      <p><?= htmlspecialchars($event['location']) ?></p>
    </div>

    <!-- DATE -->
    <div class="detail-box">
      <label>Date</label>
      <p><?= date("d M Y", strtotime($event['event_date'])) ?></p>
    </div>

    <!-- TIME -->
    <div class="detail-box">
      <label>Time</label>
      <p><?= fTime($event['start_time']) ?> - <?= fTime($event['end_time']) ?></p>
    </div>

    <!-- CATEGORY -->
    <div class="detail-box">
      <label>Category</label>
      <p><?= htmlspecialchars($event['category']) ?></p>
    </div>

    <!-- DESCRIPTION -->
    <div class="detail-box">
      <label>Description</label>
      <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
    </div>

    <!-- EXTRA INFO (only if organizer added) -->
    <?php if (!empty($event['activity_level'])): ?>
      <div class="detail-box">
        <label>Additional Information</label>
        <p><?= nl2br(htmlspecialchars($event['activity_level'])) ?></p>
      </div>
    <?php endif; ?>

    <!-- BUTTON REGISTER -->
    <?php if (!$loggedIn): ?>
      <a class="join-btn" href="auth/login.php">Login to Register</a>

    <?php elseif ($alreadyRegistered): ?>
      <span class="btn-secondary">Already Registered</span>

    <?php else: ?>
      <button class="join-btn" onclick="openConfirm()">Register</button>
    <?php endif; ?>

  </div>
</div>

<!-- FOOTER -->
<footer class="main-footer">
  © 2025 Universiti Malaysia Sabah | Contact JHEP
</footer>

<!-- POPUP CONFIRM -->
<div class="popup-backdrop" id="confirmPopup">
  <div class="popup-box">
    <h3>Confirm Registration</h3>
    <p>Are you sure you want to join this event?</p>

    <div class="popup-btn-row">
      <div class="btn-cancel" onclick="closeConfirm()">Cancel</div>
      <div class="btn-confirm" onclick="doRegister()">Confirm</div>
    </div>
  </div>
</div>

<!-- POPUP SUCCESS -->
<div class="popup-backdrop" id="successPopup">
  <div class="popup-box">
    <h3>Successfully Registered!</h3>
    <p>You have successfully joined this event.</p>

    <div class="popup-btn-row">
      <a href="my_activity.php" class="btn-confirm">Go to My Activity</a>
      <div class="btn-cancel" onclick="closeSuccess()">Stay Here</div>
    </div>
  </div>
</div>

<script>
function toggleMenu(){ document.getElementById("navMenu").classList.toggle("show-menu"); }

function openConfirm(){ document.getElementById("confirmPopup").style.display = "flex"; }
function closeConfirm(){ document.getElementById("confirmPopup").style.display = "none"; }

function doRegister(){
    <?php if (!$requiresPayment): ?>
        // FREE → register via AJAX
        fetch("register_event.php", {
            method: "POST",
            headers: { "Content-Type":"application/x-www-form-urlencoded" },
            body: "event_id=<?= $eventId ?>"
        })
        .then(() => {
            closeConfirm();
            document.getElementById("successPopup").style.display = "flex";
        });
    <?php else: ?>
        // PAID → redirect
        window.location.href = "payment.php?event_id=<?= $eventId ?>";
    <?php endif; ?>
}

function closeSuccess(){
    document.getElementById("successPopup").style.display = "none";
}
</script>

</body>
</html>
