<?php
session_start();
require 'config/db.php';

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo "Not logged in";
    exit;
}

$userId = $_SESSION['user_id'];
$eventId = $_POST['event_id'] ?? null;

if (!$eventId) {
    http_response_code(400);
    echo "Invalid event ID";
    exit;
}

// Check if event exists
$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->execute([$eventId]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "Event not found";
    exit;
}

// Check duplicate registration
$check = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE user_id = ? AND event_id = ?");
$check->execute([$userId, $eventId]);

if ($check->fetchColumn() > 0) {
    echo "Already registered";
    exit;
}

// Insert new registration
$insert = $pdo->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
$insert->execute([$userId, $eventId]);

// Optional: update participant count
$update = $pdo->prepare("UPDATE events SET num_participants = COALESCE(num_participants,0) + 1 WHERE event_id = ?");
$update->execute([$eventId]);

echo "success";
exit;
?>
