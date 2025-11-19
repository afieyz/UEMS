<?php
// author : NUR-AFIDA BINTI MUHD AZUAN TIONG (BI22110453)
session_start();
require '../config/db.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, token = NULL WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        $_SESSION['success'] = "Your email has been verified. You can now log in.";
        header("Location: login.php?verified=1");
        exit;
    } else {
        $_SESSION['error'] = "Invalid or expired verification link.";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "No verification token provided.";
    header("Location: login.php");
    exit;
}
?>
