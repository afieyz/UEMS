<?php
session_start();
require 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_id'])) {

    $user_id = $_POST['user_id'];

    // Fetch current avatar
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $old_avatar = $stmt->fetchColumn();

    // AVATAR UPLOAD 
    if (!empty($_FILES['avatar']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['avatar']['type'];

        if (in_array($file_type, $allowed_types)) {
            $file_name = time() . "_" . basename($_FILES['avatar']['name']);
            $target_path = "uploads/" . $file_name;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
                // Delete old avatar if exists and not default
                if (!empty($old_avatar) && file_exists("uploads/$old_avatar")) {
                    unlink("uploads/$old_avatar");
                }

                // Update avatar in database
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE user_id = ?");
                $stmt->execute([$file_name, $user_id]);
            }
        }
    }

    // UPDATE PROFILE FIELDS 
    $name       = trim($_POST['name'] ?? '');
    $student_id = trim($_POST['student_id'] ?? '');
    $faculty    = trim($_POST['faculty'] ?? '');
    $phone_num  = trim($_POST['phone_num'] ?? '');
    $year       = trim($_POST['year'] ?? '');

    $stmt = $pdo->prepare("
        UPDATE users 
        SET name = ?, student_id = ?, faculty = ?, phone_num = ?, year = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$name, $student_id, $faculty, $phone_num, $year, $user_id]);

    $_SESSION['success'] = "Profile updated successfully.";
    header("Location: profile.php");
    exit;

} else {
    header("Location: profile.php");
    exit;
}
?>