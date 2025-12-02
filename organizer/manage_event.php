<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = intval($_GET['event_id'] ?? 0);

if ($event_id <= 0) {
    header("Location: ../organizer/dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ? AND created_by = ?");
$stmt->execute([$event_id, $user_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header("Location: ../organizer/dashboard.php");
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Upload Image
    if (($_POST['action'] ?? '') === 'upload_image') {
        if (!empty($_FILES['event_image']['name'])) {
            $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
            $type = $_FILES['event_image']['type'];

            if (!in_array($type, $allowed)) {
                $errors[] = "Invalid image type.";
            } else {
                $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9_\-\.]/', '', basename($_FILES['event_image']['name']));
                $targetDir = __DIR__ . "/../uploads/events/";

                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                $target = $targetDir . $filename;

                if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target)) {

                    if (!empty($event['event_image']) && file_exists($targetDir . $event['event_image'])) {
                        @unlink($targetDir . $event['event_image']);
                    }

                    $stmt = $pdo->prepare("UPDATE events SET event_image = ? WHERE event_id = ?");
                    $stmt->execute([$filename, $event_id]);

                    $success = "Image uploaded successfully.";

                    $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ? AND created_by = ?");
                    $stmt->execute([$event_id, $user_id]);
                    $event = $stmt->fetch(PDO::FETCH_ASSOC);

                } else {
                    $errors[] = "Could not upload file.";
                }
            }
        } else {
            $errors[] = "No image selected.";
        }
    }

    // Update Extra Info
    if (($_POST['action'] ?? '') === 'update_extra_info') {
        $extra_info = $_POST['extra_info'] ?? null;

        $stmt = $pdo->prepare("UPDATE events SET extra_info = ? WHERE event_id = ?");
        $stmt->execute([$extra_info, $event_id]);

        $success = "Additional information updated.";

        $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = ? AND created_by = ?");
        $stmt->execute([$event_id, $user_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Event - <?= htmlspecialchars($event['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/organizer.css">
</head>

<body class="org-body">

<header class="org-header">
    <div class="org-left">
        <a href="../index.php" class="text-link">← Home</a>
        <h1>Manage Event</h1>
    </div>

    <div class="org-right">
        <a href="../profile.php" class="text-link">My Profile</a>
        <a href="../auth/logout.php" class="btn-logout">Logout</a>
    </div>
</header>

<main class="org-main">

    <section class="manage-card">
        <h2><?= htmlspecialchars($event['title']) ?></h2>

        <div class="manage-grid">
            
            <!-- LEFT -->
            <div class="manage-left">
                <h4>Current Image</h4>

                <?php
                    $img = !empty($event['event_image']) 
                        ? "../uploads/events/" . $event['event_image'] 
                        : "../images/default_event.jpg";
                ?>

                <img src="<?= htmlspecialchars($img) ?>" style="max-width:100%; border-radius:8px;">

                <form method="POST" enctype="multipart/form-data" style="margin-top:12px;">
                    <input type="hidden" name="action" value="upload_image">
                    <label>Select new event image:</label><br>
                    <input type="file" name="event_image" accept="image/*" required>
                    <button type="submit" class="btn" style="margin-top:10px;">Upload Image</button>
                </form>

                <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
                <?php if (!empty($errors)): foreach ($errors as $err): ?>
                    <p class="error"><?= $err ?></p>
                <?php endforeach; endif; ?>
            </div>

            <!-- RIGHT -->
            <div class="manage-right">
                <h4>Event Details</h4>
                <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($event['location']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($event['status']) ?></p>

                <div style="margin-top:16px;">
                    <a href="event_participants.php?event_id=<?= $event['event_id'] ?>" class="btn outline">Participants</a>
                    <a href="event_feedback.php?event_id=<?= $event['event_id'] ?>" class="btn outline">Feedback</a>
                </div>

                <div style="margin-top:25px;">
                    <h4>Additional Information</h4>
                    <form method="POST">
                        <input type="hidden" name="action" value="update_extra_info">
                        <textarea name="extra_info" rows="4" style="width:100%; padding:10px; border-radius:8px;" placeholder="Contact: 0123456789&#10;Dress code: Sportswear"><?= htmlspecialchars($event['extra_info'] ?? '') ?></textarea>
                        <button type="submit" class="btn" style="margin-top:10px;">Save Info</button>
                    </form>
                </div>
            </div>

        </div>
    </section>

</main>

<footer class="org-footer">
    &copy; <?= date('Y') ?> University Event Management System
</footer>

</body>
</html>
