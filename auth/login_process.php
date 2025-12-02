<?php
// author : NUR-AFIDA BINTI MUHD AZUAN TIONG (BI22110453)
session_start();
require '../config/db.php';


// =============================================================
// AUTO CREATE ADMIN ACCOUNT (IF NOT EXISTS)
// =============================================================

// Admin credentials (fixed)
$fixed_admin_email = "admin@eventsystem.com";
$fixed_admin_password = "admin123";   // <-- Admin password you want
$fixed_admin_name = "Administrator";

// Check if admin exists in DB
$check_admin = $pdo->prepare("SELECT * FROM users WHERE role = 'admin'");
$check_admin->execute();
$admin_exists = $check_admin->fetch();

if (!$admin_exists) {
    // Create hash from fixed admin password
    $hashed_password = password_hash($fixed_admin_password, PASSWORD_DEFAULT);

    // Insert admin into database
    $insert_admin = $pdo->prepare("
        INSERT INTO users (name, email, password, role, is_verified)
        VALUES (?, ?, ?, 'admin', 1)
    ");
    $insert_admin->execute([
        $fixed_admin_name,
        $fixed_admin_email,
        $hashed_password
    ]);
}
// =============================================================
// END OF AUTO CREATE ADMIN
// =============================================================


// =============================================================
// NORMAL LOGIN PROCESS FOR ALL ROLES
// =============================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

    // Get user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Check password
    if ($user && password_verify($password, $user['password'])) {

        // Check verification only for participants & organizers
        if ($user['role'] !== 'admin' && !$user['is_verified']) {
            $_SESSION['error'] = "Please verify your email before logging in.";
            header("Location: login.php");
            exit;
        }

        // Set session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        // Redirect by role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit;
    } 
    else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit;
    }
}

?>
