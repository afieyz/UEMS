<?php
//author : NUR-AFIDA BINTI MUHD AZUAN TIONG (BI22110453)
session_start();
require '../config/db.php';
require '../vendor/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/src/SMTP.php';
require '../vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = $success = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower(trim($_POST['email']));

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "No account found with that email.";
    } else {
        $token = bin2hex(random_bytes(16));
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $stmt->execute([$token, $email]);

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
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password - Event System';
            $mail->Body = "
                <h3>Password Reset Request</h3>
                <p>Click the link below to reset your password:</p>
                <a href='http://localhost/event_system/auth/reset_password.php?token=$token'>Reset Password</a>
                <br><br>
                <p>If you didn’t request this, please ignore this message.</p>
            ";

            $mail->send();
            $success = "Reset link sent! Please check your email.";
        } catch (Exception $e) {
            $error = "Email could not be sent. Error: {$mail->ErrorInfo}";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-page">

  <div class="auth-container">
    <h2>Forgot Password</h2>

    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Enter your email" required><br>
      <button type="submit">Send Reset Link</button>
    </form>

    <div class="links">
      <p><a href="login.php">Back to Login</a></p>
    </div>
  </div>

</body>
</html>
