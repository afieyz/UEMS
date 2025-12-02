<?php 
session_start();
require 'config/db.php';

// Fetch categories
$catStmt = $pdo->query("SELECT DISTINCT category FROM events WHERE status='approved'");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$query = "SELECT * FROM events WHERE status='approved'";
$params = [];

if ($search != '') {
    $query .= " AND title LIKE ?";
    $params[] = "%$search%";
}

if ($category != '') {
    $query .= " AND category = ?";
    $params[] = $category;
}

$query .= " ORDER BY event_date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// split upcoming vs past
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events - Event System</title>
    <link rel="stylesheet" href="assets/css/events.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body class="events-body">

<header class="events-header">
    <h1>University Event Management</h1>

<<<<<<< HEAD
    <!-- Hamburger -->
    <div class="events-hamburger" onclick="toggleEventsMenu()">☰</div>

    <nav class="events-nav" id="eventsNavMenu">
=======
    <nav class="events-nav">
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a class="active" href="events.php">Events</a></li>
            <li><a href="news.php">News</a></li>
            <li><a href="event/create_event.php">Create Event</a></li>
            <li><a href="my_activity.php">My Activity</a></li>

            <?php if (!isset($_SESSION['user_id'])): ?>
<<<<<<< HEAD
                <li><a href="auth/login.php" class="events-login-btn">Login</a></li>
            <?php else: ?>
                <li><a href="profile.php" class="events-profile-link">My Profile</a></li>
                <li><a href="auth/logout.php" class="events-login-btn">Logout</a></li>
=======
                <li><a href="auth/login.php">Login</a></li>
            <?php else: ?>
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="auth/logout.php">Logout</a></li>
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
            <?php endif; ?>
        </ul>
    </nav>
</header>

<<<<<<< HEAD
<script>
function toggleEventsMenu() {
    document.getElementById('eventsNavMenu').classList.toggle('show-menu');
}
</script>


=======
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
<!-- FILTERS -->
<div class="filter-container">
    <form method="GET">
        <div class="filter-row">
            <input type="text" name="search" placeholder="Search events..." value="<?= htmlspecialchars($search) ?>">

            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $row): ?>
                    <option value="<?= htmlspecialchars($row['category']) ?>" 
                        <?= $category == $row['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['category']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="filter-btn" type="submit">Apply</button>
        </div>
    </form>
</div>

<!-- UPCOMING -->
<div class="section-wrapper">
  <h2 class="section-title">Upcoming Events</h2>
  <div class="events-grid">
    <?php if (count($upcoming) == 0): ?>
        <div class="no-events-box">
            <p>No upcoming events.</p>
        </div>
    <?php else: ?>
        <?php foreach ($upcoming as $event): 
            $img = !empty($event['event_image']) ? "uploads/events/" . $event['event_image'] : "images/default_event.jpg";
        ?>
            <div class="event-card">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($event['title']) ?>">

                <div class="event-info">
                    <h3><?= htmlspecialchars($event['title']) ?></h3>
                    <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($event['category']) ?></p>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a class="join-btn" href="auth/login.php">Login to Join</a>
                    <?php else: ?>
                        <a class="join-btn" href="event_details.php?event_id=<?= $event['event_id'] ?>">Join Event</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- PAST -->
<div class="section-wrapper">
  <h2 class="section-title">Past Events</h2>
  <div class="events-grid">
    <?php if (count($past) == 0): ?>
        <div class="no-events-box">
            <p>No past events.</p>
        </div>
    <?php else: ?>
        <?php foreach ($past as $event): 
            $img = !empty($event['event_image']) ? "uploads/events/" . $event['event_image'] : "images/default_event.jpg";
        ?>
            <div class="event-card">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($event['title']) ?>">

                <div class="event-info">
                    <h3><?= htmlspecialchars($event['title']) ?></h3>
                    <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($event['category']) ?></p>
<<<<<<< HEAD
                    <a class="join-btn" href="event_details.php?event_id=<?= $event['event_id'] ?>">View</a>
=======
                    <a class="join-btn" href="event_detail.php?event_id=<?= $event['event_id'] ?>">View</a>
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<footer class="events-footer">
    &copy; 2025 Universiti Malaysia Sabah | Contact JHEP
</footer>

</body>
</html>
