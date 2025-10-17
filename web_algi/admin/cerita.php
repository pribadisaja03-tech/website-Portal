<?php
// admin/cerita.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
include "../config.php";

// HAPUS
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM cerita WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: cerita.php?msg=deleted");
    exit;
}

// Ambil daftar cerita khusus dari user biasa (role = 'user')
$sql = "SELECT c.*, u.username 
        FROM cerita c 
        JOIN user u ON c.user_id = u.id 
        WHERE u.role = 'user' 
        ORDER BY c.created_at DESC";
$all = $conn->query($sql);
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Manajemen Cerita User</title>
<style>
body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f9;margin:0}
.navbar{background:#2c3e50;color:#fff;padding:14px;display:flex;justify-content:space-between;align-items:center}
.container{max-width:1100px;margin:28px auto;background:#fff;padding:22px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.05)}
h2{margin:0 0 16px;font-size:22px;display:flex;align-items:center;gap:8px;border-bottom:3px solid #0896fc;padding-bottom:8px}
table{width:100%;border-collapse:collapse;margin-top:14px;font-size:14px}
th,td{padding:12px 10px;border-bottom:1px solid #eee;text-align:left}
th{background:#0896fc;color:#fff;font-weight:600}
tbody tr:nth-child(even){background:#f9f9f9}
tbody tr:hover{background:#eef7ff}
.status{font-weight:600}
.small{font-size:13px;color:#555}
.actions{display:flex;gap:6px}
.btn{display:inline-block;padding:6px 12px;border-radius:6px;color:#fff;text-decoration:none;font-size:13px;transition:.2s}
.btn-edit{background:#3498db}
.btn-edit:hover{background:#2d83c5}
.btn-del{background:#e74c3c}
.btn-del:hover{background:#c0392b}
.note{font-size:14px;color:#555;margin-top:10px}
</style>
</head>
<body>
<div class="navbar">
  <div>📚 Admin Panel</div>
  <div>
    <a href="dashboard.php" style="color:#fff;text-decoration:none;margin-right:12px">Dashboard</a>
    <a href="../logout.php" style="color:#fff;text-decoration:none">Logout</a>
  </div>
</div>

<div class="container">
  <h2>📖 Manajemen Cerita User</h2>

  <?php if (isset($_GET['msg'])): ?>
    <p class="note">
      <?= $_GET['msg']==='approved' ? '✅ Cerita disetujui' : '' ?>
      <?= $_GET['msg']==='rejected' ? '❌ Cerita ditolak' : '' ?>
      <?= $_GET['msg']==='deleted' ? '🗑 Cerita dihapus' : '' ?>
    </p>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th style="width:45px">No</th>
        <th>Judul</th>
        <th>Pengirim</th>
        <th>Daerah</th>
        <th>Kategori</th>
        <th>Status</th>
        <th>Alasan Penolakan</th>
        <th>Dibuat</th>
        <th>Diubah</th>
        <th style="width:150px">Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($all && $all->num_rows>0): $no=1; while($row=$all->fetch_assoc()): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['judul']) ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['daerah']) ?></td>
          <td><?= htmlspecialchars($row['kategori']) ?></td>
          <td class="status">
            <?php
              if ($row['status'] === 'pending') echo '⏳ Pending';
              elseif ($row['status'] === 'pending_edit') echo '✏ Pending Edit';
              elseif ($row['status'] === 'approved') echo '✅ Approved';
              elseif ($row['status'] === 'rejected') echo '❌ Rejected';
              else echo htmlspecialchars($row['status']);
            ?>
          </td>
          <td><?= ($row['status']==='rejected' && !empty($row['alasan_reject'])) ? htmlspecialchars($row['alasan_reject']) : '-' ?></td>
          <td class="small"><?= !empty($row['created_at']) ? date("d-m-Y H:i", strtotime($row['created_at'])) : '-' ?></td>
          <td class="small"><?= !empty($row['updated_at']) ? date("d-m-Y H:i", strtotime($row['updated_at'])) : '-' ?></td>
          <td>
            <div class="actions">
              <a href="lihat_cerita.php?id=<?= $row['id'] ?>" class="btn btn-edit">👁 Lihat</a>
              <a href="cerita.php?hapus=<?= $row['id'] ?>" class="btn btn-del" onclick="return confirm('Hapus cerita ini?')">🗑 Hapus</a>
            </div>
          </td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="10">Belum ada cerita.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
