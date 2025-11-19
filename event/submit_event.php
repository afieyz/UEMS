<?php
session_start();
require '../config/db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get data
$title = $_POST['title'];
$description = $_POST['description'];
$location = $_POST['location'];
$event_date = $_POST['event_date'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$category = $_POST['category'];
$level = $_POST['activity_level'];
$participants = $_POST['num_participants'];

$proposal_file = null;

// Upload proposal
if (!empty($_FILES['proposal_file']['name'])) {

    if (!is_dir("uploads/proposals")) {
        mkdir("uploads/proposals", 0777, true);
    }

    $filename = time() . "_" . basename($_FILES['proposal_file']['name']);
    $path = "uploads/proposals/" . $filename;

    move_uploaded_file($_FILES['proposal_file']['tmp_name'], $path);

    $proposal_file = $filename;
}

$stmt = $pdo->prepare("
    INSERT INTO events (title, description, location, event_date, start_time, end_time,
                        category, activity_level, num_participants, proposal_file,
                        created_by, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");

$stmt->execute([
    $title, $description, $location, $event_date, $start_time, $end_time,
    $category, $level, $participants, $proposal_file, $user_id
]);

header("Location: ../organizer/dashboard.php");
exit;
