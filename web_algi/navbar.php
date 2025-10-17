<?php
// navbar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page = basename($_SERVER['PHP_SELF']);
$base = "/web_algi"; // ganti sesuai folder project kamu di htdocs

$roleClass = "guest";
if (isset($_SESSION['user_id'])) {
    $roleClass = ($_SESSION['role'] === 'admin') ? "admin" : "user";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Portal Cerita Rakyat Lombok</title>
  <link rel="stylesheet" href="<?= $base ?>/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { margin: 0; font-family: Arial, sans-serif; }
    .navbar {
      background: #1a120f;
      padding: 12px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }
    .navbar .logo {
      font-size: 20px;
      font-weight: bold;
      text-transform: uppercase;
      letter-spacing: 2px;
    }
    .navbar .left, .navbar .right {
      display: flex;
      align-items: center;
      gap: 18px;
    }
    .navbar i {
      font-size: 20px;
      cursor: pointer;
      transition: 0.3s;
    }
    .navbar i:hover { color: #ffcc00; }

    /* Sidebar */
    .sidebar {
      height: 100%;
      width: 0;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #1e1e2f;
      overflow-x: hidden;
      transition: 0.4s;
      padding-top: 60px;
      z-index: 9999;
    }
    .sidebar a {
      padding: 12px 20px;
      text-decoration: none;
      font-size: 16px;
      color: #f1f1f1;
      display: block;
      transition: 0.3s;
    }
    .sidebar a:hover {
      background: #333;
    }
    .sidebar .closebtn {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 24px;
      cursor: pointer;
      color: #fff;
    }
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background: rgba(0,0,0,0.5);
      display: none;
      z-index: 9998;
    }

    /* Search Popup */
    .search-popup {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
      z-index: 10000;
    }
    .search-form {
      background: white;
      padding: 20px;
      border-radius: 10px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      width: 300px;
    }
    .search-form input {
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .search-form button {
      padding: 10px;
      background: #1a120f;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .search-form button:hover {
      background: #ffcc00;
      color: black;
    }
  </style>
</head>
<body class="<?= $roleClass ?>">

<header>
  <nav class="navbar">
    <!-- Kiri: Hamburger + Home -->
    <div class="left">
      <i class="fas fa-bars" onclick="openSidebar()"></i>
      <a href="<?= $base ?>/index.php"><i class="fas fa-home"></i></a>
    </div>

    <!-- Tengah: Logo -->
    <div class="logo">PORTAL CERITA RAKYAT LOMBOK</div>

    <!-- Kanan: Grid + Search -->
    <div class="right">
      <i class="fas fa-search" onclick="openSearch()"></i>
    </div>
  </nav>
</header>

<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
  <span class="closebtn" onclick="closeSidebar()">&times;</span>
  <?php if (isset($_SESSION['user_id'])): ?>
    <a href="<?= $base ?>/user/profil.php">👤 Profil</a>
    <a href="<?= $base ?>/favorit.php">❤️ Favorit</a>
    <a href="<?= $base ?>/history.php">📜 History</a>
    <a href="<?= $base ?>/user/add_cerita.php">📖 Tambah Cerita</a>
    <a href="<?= $base ?>/logout.php">🚪 Logout</a>
  <?php else: ?>
    <a href="<?= $base ?>/login.php">🔑 Login</a>
    <a href="<?= $base ?>/register.php">📝 Register</a>
  <?php endif; ?>
</div>

<!-- Overlay -->
<div id="overlay" class="overlay" onclick="closeSidebar()"></div>

<!-- Search Popup -->
<div id="searchPopup" class="search-popup">
  <form action="<?= $base ?>/search.php" method="GET" class="search-form">
    <input type="text" name="q" placeholder="Cari judul/kategori/daerah...">
    <button type="submit">Cari</button>
  </form>
</div>

<script>
function openSidebar() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("overlay").style.display = "block";
}
function closeSidebar() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("overlay").style.display = "none";
}

// Search popup
function openSearch() {
  document.getElementById("searchPopup").style.display = "flex";
}
// Tutup kalau klik di luar
document.getElementById("searchPopup").addEventListener("click", function(e) {
  if (e.target === this) {
    this.style.display = "none";
  }
});
</script>

</body>
</html>
