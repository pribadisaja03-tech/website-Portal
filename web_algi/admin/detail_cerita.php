<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include "../config.php";

// ambil id
if (!isset($_GET['id'])) {
    header("Location: cerita.php");
    exit;
}
$id = (int) $_GET['id'];

// ambil cerita
$sql = "SELECT c.*, u.username 
        FROM cerita c 
        JOIN user u ON c.user_id = u.id 
        WHERE c.id = $id";
$res = $conn->query($sql);
if ($res->num_rows == 0) {
    echo "Cerita tidak ditemukan.";
    exit;
}
$row = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Cerita</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f6f9; margin:0; padding:0; }
.container { max-width:900px; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
h1 { margin-top:0; }
.meta { font-size:14px; color:#555; margin-bottom:10px; }
.btn { padding:8px 12px; margin-right:5px; border-radius:5px; text-decoration:none; color:white; font-size:14px; }
.btn-approve { background:#2ecc71; }
.btn-reject { background:#e67e22; }
.btn-del { background:#e74c3c; }
.btn-back { background:#3498db; }
img { max-width:100%; margin:15px 0; border-radius:10px; }
</style>
</head>
<body>
<div class="container">
  <h1><?= htmlspecialchars($row['judul']) ?></h1>
  <div class="meta">
    Pengirim: <b><?= htmlspecialchars($row['username']) ?></b> | 
    Daerah: <?= htmlspecialchars($row['daerah']) ?> | 
    Kategori: <?= htmlspecialchars($row['kategori']) ?> | 
    Status: <?= $row['status'] ?> | 
    Tanggal: <?= $row['created_at'] ?>
  </div>

  <?php if (!empty($row['gambar'])): ?>
    <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="Gambar Cerita">
  <?php endif; ?>

  <p><?= nl2br(htmlspecialchars($row['isi'])) ?></p>

  <?php if (!empty($row['audio'])): ?>
    <h3>Audio Cerita</h3>
    <audio controls>
      <source src="../uploads/<?= htmlspecialchars($row['audio']) ?>" type="audio/mpeg">
      Browser tidak mendukung pemutar audio.
    </audio>
  <?php endif; ?>

  <hr>
  <?php if ($row['status'] === 'pending'): ?>
    <a href="cerita.php?approve=<?= $row['id'] ?>" class="btn btn-approve">✅ Approve</a>
    <a href="cerita.php?reject=<?= $row['id'] ?>" class="btn btn-reject">❌ Reject</a>
  <?php endif; ?>
  <a href="cerita.php?hapus=<?= $row['id'] ?>" class="btn btn-del" onclick="return confirm('Hapus cerita ini?')">🗑 Hapus</a>
  <a href="cerita.php" class="btn btn-back">⬅ Kembali</a>
</div>
</body>
</html>
