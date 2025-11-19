<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$avatar = (!empty($user['avatar'])) ? "uploads/" . $user['avatar'] : "assets/images/icon.png";

$faculties = [
  'Fakulti Pengajian Islam',
  'Akademi Seni Dan Teknologi Kreatif',
  'Fakulti Perniagaan, Ekonomi Dan Perakaunan',
  'Fakulti Komputeran Dan Informatik',
  'Fakulti Kejuruteraan',
  'Fakulti Sains Makanan Dan Pemakanan',
  'Fakulti Sains Sosial Dan Kemanusiaan',
  'Fakulti Perhutanan Tropika',
  'Fakulti Perubatan Dan Sains Kesihatan',
  'Fakulti Psikologi Dan Pendidikan',
  'Fakulti Psikologi dan Kerja Sosial',
  'Fakulti Sains Dan Sumber Alam',
  'Fakulti Kewangan Antarabangsa Labuan',
  'Fakulti Pertanian Lestari'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Profile - Event System</title>
  <link rel="stylesheet" href="assets/css/profile.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
</head>
<body class="dashboard-body">

<!-- ✅ TOP NAVBAR -->
<nav class="top-navbar">
  <div class="navbar-left">
    <span class="navbar-logo">Event System</span>
    <div class="hamburger" onclick="document.querySelector('.navbar-right').classList.toggle('show')">☰</div>
  </div>
  <div class="navbar-right">
    <a href="index.php">Home</a>
    <a href="profile.php">My Profile</a>
    <a href="auth/logout.php" class="logout-btn">Logout</a>
  </div>
</nav>

<!-- ✅ MAIN CONTENT -->
<main class="dashboard-content">
  <h1>Edit Profile</h1>

  <div class="profile-card">
    <div class="profile-avatar">
      <img src="<?= $avatar ?>" alt="Profile Picture">
    </div>

    <form action="update_profile_process.php" method="POST" enctype="multipart/form-data" class="edit-form">
      <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

      <div class="form-group">
        <label>Upload New Picture</label>
        <input type="file" name="avatar">
      </div>

      <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
      </div>

      <div class="form-group">
        <label>Matric Number</label>
        <input type="text" name="student_id" value="<?= htmlspecialchars($user['student_id']) ?>">
      </div>

      <div class="form-group">
        <label>Faculty</label>
        <select name="faculty" required>
          <option value="">-- Select Faculty --</option>
          <?php foreach ($faculties as $faculty): ?>
            <option value="<?= $faculty ?>" <?= $user['faculty'] === $faculty ? 'selected' : '' ?>><?= $faculty ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone_num" value="<?= htmlspecialchars($user['phone_num']) ?>">
      </div>

      <div class="form-group">
        <label>Year of Study</label>
        <input type="text" name="year" value="<?= htmlspecialchars($user['year']) ?>">
      </div>

      <button type="submit" class="edit-profile-btn">Save Changes</button>
    </form>
  </div>
</main>

<!-- ✅ FOOTER -->
<footer class="profile-footer">
  &copy; 2025 Universiti Malaysia Sabah | Contact JHEP
</footer>

<!-- ✅ OPTIONAL: Close menu when clicking outside -->
<script>
  document.addEventListener('click', function(e) {
    const navRight = document.querySelector('.navbar-right');
    const hamburger = document.querySelector('.hamburger');
    if (!navRight.contains(e.target) && !hamburger.contains(e.target)) {
      navRight.classList.remove('show');
    }
  });
</script>

</body>
</html>