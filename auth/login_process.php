<?php
// author : NUR-AFIDA BINTI MUHD AZUAN TIONG (BI22110453)
session_start();
require '../config/db.php';

<<<<<<< HEAD

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
=======
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];

<<<<<<< HEAD
    // Get user by email
=======
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

<<<<<<< HEAD
    // Check password
    if ($user && password_verify($password, $user['password'])) {

        // Check verification only for participants & organizers
        if ($user['role'] !== 'admin' && !$user['is_verified']) {
=======
    if ($user && password_verify($password, $user['password'])) {
        if (!$user['is_verified']) {
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
            $_SESSION['error'] = "Please verify your email before logging in.";
            header("Location: login.php");
            exit;
        }

<<<<<<< HEAD
        // Set session
=======
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

<<<<<<< HEAD
        // Redirect by role
=======
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../index.php");
        }
        exit;
<<<<<<< HEAD
    } 
    else {
=======
    } else {
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit;
    }
}
<<<<<<< HEAD

=======
>>>>>>> a579976671b823e297fa111d9216c91ffcd9c1b3
?>
