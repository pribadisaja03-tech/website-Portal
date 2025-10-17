<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include "../config.php";

// =======================
// Cerita Approved
// =======================
$where = "WHERE status='approved'";
if (isset($_GET['cari']) && $_GET['cari'] != "") {
    $cari = $conn->real_escape_string($_GET['cari']);
    $where .= " AND (judul LIKE '%$cari%' OR daerah LIKE '%$cari%' OR kategori LIKE '%$cari%')";
}
$cerita = $conn->query("SELECT * FROM cerita $where ORDER BY created_at DESC");

// =======================
// Komentar Pending
// =======================
$komentar = $conn->query("
    SELECT k.id, k.isi AS komentar, k.rating, k.created_at,
           c.judul, u.username AS nama
    FROM komentar k
    JOIN cerita c ON k.cerita_id = c.id
    JOIN user u ON k.user_id = u.id
    WHERE k.status='pending'
    ORDER BY k.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Admin</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f6f9; margin:0; padding:0; }
.navbar { background:#2c3e50; padding:15px; color:white; display:flex; justify-content:space-between; align-items:center; }
.navbar h1 { margin:0; font-size:22px; }
.navbar a { color:white; text-decoration:none; margin-left:15px; font-weight:bold; }
.navbar a:hover { text-decoration:underline; }

.container { max-width:1100px; margin:30px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.1); }
.welcome { text-align:center; font-size:20px; font-weight:bold; margin-bottom:20px; padding:10px; border:2px solid #3498db; border-radius:8px; background:#ecf7ff; color:#2c3e50; }

.action-bar { display:flex; justify-content:space-between; align-items:center; margin:15px 0; flex-wrap:wrap; gap:10px; }
.search-box { display:flex; align-items:center; }
.search-box input[type="text"] { padding:8px; width:250px; border:1px solid #ccc; border-radius:5px 0 0 5px; }
.search-box button { padding:8px 14px; background:#3498db; border:none; color:white; border-radius:0 5px 5px 0; cursor:pointer; }
.search-box button:hover { background:#2980b9; }
.btn-add { padding:8px 14px; border-radius:5px; text-decoration:none; color:white; background:#2ecc71; font-weight:bold; }
.btn-add:hover { background:#27ae60; }

.table-responsive { overflow-x:auto; }
table { width:100%; border-collapse:collapse; margin-top:20px; min-width:600px; }
table th, table td { border:1px solid #ddd; padding:10px; text-align:center; vertical-align:top; }
table th { background:#3498db; color:white; }
td img { max-width:80px; border-radius:5px; }
.komentar-col { max-width:300px; white-space:pre-wrap; word-wrap:break-word; text-align:left; }

.btn { padding:6px 10px; border-radius:5px; text-decoration:none; color:white; font-size:14px; display:inline-block; margin:2px 0; }
.btn-edit { background:#f39c12; }
.btn-edit:hover { background:#d68910; }
.btn-del { background:#e74c3c; }
.btn-del:hover { background:#c0392b; }
.btn-approve { background:#2ecc71; }
.btn-approve:hover { background:#27ae60; }

/* Responsive */
@media (max-width:768px) {
  .search-box input[type="text"] { width:150px; }
  .komentar-col { max-width:180px; }
}
</style>
</head>
<body>
<div class="navbar">
  <h1>📊 Dashboard Admin</h1>
  <div>
    <a href="dashboard.php">Home</a>
    <a href="cerita.php">Manajemen Cerita</a>
    <a href="../logout.php">Logout</a>
  </div>
</div>

<div class="container">
  <div class="welcome">👋 Selamat Datang, Admin!</div>

  <!-- ======================= -->
  <!-- Cerita Approved -->
  <!-- ======================= -->
  <h2>📚 Daftar Cerita (Approved)</h2>

  <div class="action-bar">
    <a href="add_cerita.php" class="btn-add">➕ Tambah Cerita</a>
    <div class="search-box">
      <form method="get" action="dashboard.php" style="display:flex;">
        <input type="text" name="cari" placeholder="Cari judul / daerah / kategori..." 
               value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>">
        <button type="submit">🔍 Cari</button>
      </form>
    </div>
  </div>

  <div class="table-responsive">
  <table>
    <tr>
      <th>No</th>
      <th>Judul</th>
      <th>Daerah</th>
      <th>Kategori</th>
      <th>Gambar</th>
      <th>Audio</th>
      <th>Tanggal</th>
      <th>Aksi</th>
    </tr>
    <?php if ($cerita->num_rows > 0): $no=1; while($row=$cerita->fetch_assoc()): ?>
    <tr>
      <td><?= $no++ ?></td>
      <td><?= htmlspecialchars($row['judul']) ?></td>
      <td><?= htmlspecialchars($row['daerah']) ?></td>
      <td><?= htmlspecialchars($row['kategori']) ?></td>
      <td>
        <?php if (!empty($row['gambar'])): ?>
          <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>">
        <?php else: ?> - <?php endif; ?>
      </td>
      <td>
        <?php if (!empty($row['audio'])): ?>
          <audio controls style="width:120px;">
            <source src="../uploads/<?= htmlspecialchars($row['audio']) ?>" type="audio/mpeg">
          </audio>
        <?php else: ?> - <?php endif; ?>
      </td>
      <td><?= $row['created_at'] ?></td>
      <td>
        <a href="edit_cerita.php?id=<?= $row['id'] ?>" class="btn btn-edit">✏ Edit</a>
        <a href="delete_cerita.php?id=<?= $row['id'] ?>" class="btn btn-del" onclick="return confirm('Yakin hapus cerita ini?')">🗑 Hapus</a>
      </td>
    </tr>
    <?php endwhile; else: ?>
    <tr><td colspan="8">Belum ada cerita approved.</td></tr>
    <?php endif; ?>
  </table>
  </div>

  <!-- ======================= -->
  <!-- Komentar Pending -->
  <!-- ======================= -->
  <h2 style="margin-top:40px;">💬 Komentar Pending</h2>
  <div class="table-responsive">
  <table class="table-komentar">
  <tr>
    <th>No</th>
    <th>Judul Cerita</th>
    <th>User</th>
    <th>Komentar</th>
    <th>Rating</th>
    <th>Tanggal</th>
    <th>Aksi</th>
  </tr>
  <?php if ($komentar->num_rows > 0): $no=1; while($row = $komentar->fetch_assoc()): ?>
  <tr>
    <td><?= $no++ ?></td>
    <td><?= htmlspecialchars($row['judul']) ?></td>
    <td><?= htmlspecialchars($row['nama']) ?></td>
    <td class="komentar-col"><?= htmlspecialchars($row['komentar']) ?></td>
    <td>
      <?php for($i=1;$i<=5;$i++): ?>
        <?= ($i <= $row['rating']) ? '⭐' : '☆' ?>
      <?php endfor; ?>
    </td>
    <td><?= $row['created_at'] ?></td>
    <td>
      <a href="approve.php?id=<?= $row['id'] ?>" class="btn btn-approve">✔ Approve</a>
      <a href="hapus_komentar.php?id=<?= $row['id'] ?>" class="btn btn-del" onclick="return confirm('Yakin hapus komentar ini?')">🗑 Hapus</a>
    </td>
  </tr>
  <?php endwhile; else: ?>
  <tr><td colspan="7">Tidak ada komentar pending.</td></tr>
  <?php endif; ?>
</table>
  </div>
</div>
</body>
</html>
