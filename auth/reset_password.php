<?php
session_start();
require '../config/db.php';

$error = $success = null;
$user = null;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Invalid or expired token.";
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_password = $_POST['password'];
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE reset_token = ?");
        $stmt->execute([$hashed, $token]);

        $success = "Password has been reset successfully.";
    }
} else {
    $error = "No token provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password - Event System</title>

  <!-- ✅ Link to Shared Auth CSS -->
  <link rel="stylesheet" href="css/auth.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">

  <div class="auth-container">
    <h2>Reset Password</h2>

    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
      <p class="success"><?= htmlspecialchars($success) ?></p>
      <p><a href="login.php">Login Now</a></p>
    <?php elseif ($user): ?>
      <form method="POST">
        <div class="password-wrapper">
          <input id="password" type="password" name="password" placeholder="New Password" required>
          <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
        </div>
        <br>
        <button type="submit">Reset Password</button>
      </form>
    <?php endif; ?>

    <div class="links">
      <p><a href="login.php">Back to Login</a></p>
    </div>
  </div>

  <!-- ✅ jQuery + Eye Icon Script -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(".toggle-password").click(function() {
      $(this).toggleClass("fa-eye fa-eye-slash");
      const input = $($(this).attr("toggle"));
      if (input.attr("type") === "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });
  </script>

</body>
</html>
