<?php
// author : NUR-AFIDA BINTI MUHD AZUAN TIONG (BI22110453)
session_start();
require '../config/db.php';
require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';
require '../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $error = "User with this email already exists.";
    } else {
        $token = bin2hex(random_bytes(16));
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, token, is_verified) VALUES (?, ?, ?, 'user', ?, 0)");
        $stmt->execute([$name, $email, $password_hash, $token]);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply.eventsystem@gmail.com';
            $mail->Password = 'mziq lvts sfbh olmu';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('noreply.eventsystem@gmail.com', 'Event System');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Verify your registration - Event System';

            $verify_link = "http://localhost/event_system/auth/verify.php?token=$token";
            $mail->Body = "
                <h3>Hello $name,</h3>
                <p>Thank you for registering with the Event System.</p>
                <p>Please verify your email by clicking the link below:</p>
                <a href='$verify_link'>Verify Email</a>
                <br><br>
                <p>If you did not register, please ignore this message.</p>
            ";

            $mail->send();
            $_SESSION['success'] = "Signup successful! Please check your email to verify your account.";
            header("Location: login.php");
            exit;
        } catch (Exception $e) {
            $error = "Signup successful, but verification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Event System</title>
  <link rel="stylesheet" href="css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">

  <div class="auth-container">
    <h2>Sign Up</h2>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required><br>
      <input type="email" name="email" placeholder="Email" required><br>

      <div class="password-wrapper">
        <input id="password" type="password" name="password" placeholder="Password" required>
        <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
      </div>
      <br>

      <button type="submit">Sign Up</button>
    </form>

    <div class="links">
      <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(".toggle-password").click(function() {
      $(this).toggleClass("fa-eye fa-eye-slash");
      const input = $($(this).attr("toggle"));
      input.attr("type", input.attr("type") === "password" ? "text" : "password");
    });
  </script>

</body>
</html>
