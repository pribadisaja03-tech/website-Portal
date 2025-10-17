<?php
// user/profil.php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit;
}

$uid = (int) $_SESSION['user_id'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

// Hapus cerita user sendiri
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
  $id = (int) $_GET['hapus'];
  $stmt = $conn->prepare("DELETE FROM cerita WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $id, $uid);
  $stmt->execute();
  $stmt->close();
  header("Location: profil.php?msg=deleted");
  exit;
}

// Data user
$stmt = $conn->prepare("SELECT id, username, role, foto, created_at FROM user WHERE id = ?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Daftar cerita
$cerita = null;
if ($role === 'user') {
  $stmt = $conn->prepare("SELECT id, judul, created_at, status, alasan_reject FROM cerita WHERE user_id = ? ORDER BY created_at DESC");
  $stmt->bind_param("i", $uid);
  $stmt->execute();
  $cerita = $stmt->get_result();
  $stmt->close();
}

include '../navbar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Profil Saya</title>
<style>
body { 
  font-family: Arial, sans-serif; 
  background:#f4f6f9; 
  margin:0; 
  padding:0; 
  color:#222; 
}

/* Animasi Fade In */
.fade {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 1s ease, transform 0.8s ease;
}
.fade.show {
  opacity: 1;
  transform: translateY(0);
}

.container { 
  max-width:1100px; 
  margin:24px auto; 
  padding:0 20px; 
}
.profil-card { 
  display:flex; 
  align-items:center; 
  background:#fff; 
  padding:20px; 
  border-radius:10px; 
  box-shadow:0 4px 10px rgba(0,0,0,0.05); 
}
.avatar { 
  width:110px; 
  height:110px; 
  border-radius:50%; 
  object-fit:cover; 
  margin-right:20px; 
  border:6px solid #0aa; 
}
h1 { 
  margin:0 0 14px; 
  font-size:36px; 
}
.btn { 
  display:inline-block; 
  padding:6px 10px; 
  border-radius:6px; 
  font-size:13px; 
  font-weight:600; 
  color:white; 
  text-decoration:none; 
  margin:2px; 
  transition: all 0.3s ease;
}
.btn-edit { background:#0d6efd; }
.btn-del { background:#dc3545; }
.btn-lock { background:#6c757d; }
.btn-fix { background:#28a745; }
.btn:hover { transform: scale(1.05); opacity:0.9; }

.section-title { 
  margin:28px 0 10px; 
  display:flex; 
  align-items:center; 
  gap:8px; 
  font-size:24px; 
}
.table { 
  width:100%; 
  border-collapse:collapse; 
  background:#fff; 
  border-radius:8px; 
  overflow:hidden; 
  box-shadow:0 4px 10px rgba(0,0,0,0.04); 
}
.table th, .table td { 
  padding:12px 14px; 
  border-bottom:1px solid #eee; 
  text-align:left; 
  font-size:14px; 
}
.table th { 
  background:#0896fc; 
  color:#fff; 
}
.table tr:last-child td { border-bottom:none; }

.status { 
  font-weight:600; 
  display:inline-flex; 
  align-items:center; 
  gap:6px; 
}
.badge-pending { color:#d48806; }
.badge-approved { color:#198754; }
.badge-rejected { color:#c82333; }
.note { color:#666; font-size:14px; }
.msg { 
  margin:12px 0; 
  padding:10px 14px; 
  background:#e9f7ef; 
  border-radius:6px; 
  color:#1a7f2a; 
  border:1px solid #c7efda; 
}
.small { font-size:13px; color:#666; }

/* Responsif HP */
@media (max-width: 600px) {
  .table th, .table td { font-size:12px; padding:8px; }
  .btn { font-size:12px; padding:5px 8px; }
}
</style>
</head>
<body>
<div class="container fade">
  <h1>👤 Profil Saya</h1>

  <div class="profil-card fade">
    <img src="../uploads/<?= !empty($user['foto']) ? htmlspecialchars($user['foto']) : 'default.png' ?>" alt="avatar" class="avatar">
    <div>
      <h2 style="margin:0 0 6px;"><?= htmlspecialchars($user['username']) ?></h2>
      <p class="small"><strong>User ID:</strong> <?= $user['id'] ?> &nbsp; | &nbsp; <strong>Bergabung sejak:</strong> <?= date("d F Y", strtotime($user['created_at'])) ?></p>
      <p style="margin-top:10px;"><a href="edit_profil.php" class="btn btn-edit">✏️ Edit Profil</a></p>
    </div>
  </div>

  <?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'deleted'): ?>
      <div class="msg fade">🗑 Cerita berhasil dihapus.</div>
    <?php elseif ($_GET['msg'] === 'edit_pending'): ?>
      <div class="msg fade">✏️ Perubahan disimpan dan dikirim ulang ke admin untuk ditinjau.</div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if ($role !== 'user'): ?>
    <p class="note fade">ℹ️ Anda masuk sebagai <strong>admin</strong>. Halaman ini hanya menampilkan daftar cerita jika Anda login sebagai <strong>user</strong>.</p>
  <?php else: ?>

    <div class="section-title fade">📚 Cerita Saya</div>

    <?php if ($cerita && $cerita->num_rows > 0): ?>
      <table class="table fade">
        <thead>
          <tr>
            <th style="width:60px;">No</th>
            <th>Judul</th>
            <th style="width:150px;">Status</th>
            <th>Alasan Penolakan</th>
            <th style="width:160px;">Tanggal</th>
            <th style="width:200px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1; while ($r = $cerita->fetch_assoc()): ?>
            <tr class="fade">
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($r['judul']) ?></td>
              <td>
                <span class="status">
                  <?php
                    if ($r['status'] === 'pending') echo '<span class="badge-pending">⏳ Pending</span>';
                    elseif ($r['status'] === 'pending_edit') echo '<span class="badge-pending">✏️ Menunggu Review</span>';
                    elseif ($r['status'] === 'approved') echo '<span class="badge-approved">✅ Disetujui</span>';
                    elseif ($r['status'] === 'rejected') echo '<span class="badge-rejected">❌ Ditolak</span>';
                    else echo htmlspecialchars($r['status']);
                  ?>
                </span>
              </td>
              <td>
                <?php
                  if ($r['status'] === 'rejected') {
                    echo !empty($r['alasan_reject']) ? htmlspecialchars($r['alasan_reject']) : '<span class="small">Tidak ada alasan</span>';
                  } else {
                    echo '-';
                  }
                ?>
              </td>
              <td><?= date("d-m-Y H:i", strtotime($r['created_at'])) ?></td>
              <td>
                <?php if ($r['status'] === 'approved'): ?>
                  <span class="btn btn-lock">🔒 Terkunci</span>
                <?php elseif ($r['status'] === 'rejected'): ?>
                  <a href="edit_cerita.php?id=<?= $r['id'] ?>" class="btn btn-fix">✏️ Perbaiki & Kirim Ulang</a>
                  <a href="profil.php?hapus=<?= $r['id'] ?>" class="btn btn-del" onclick="return confirm('Yakin hapus cerita ini?')">🗑</a>
                <?php elseif ($r['status'] === 'pending' || $r['status'] === 'pending_edit'): ?>
                  <span class="btn btn-lock">⏳ Menunggu Admin</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="note fade">Belum ada cerita. <a href="add_cerita.php">Tambah cerita baru</a>.</p>
    <?php endif; ?>

  <?php endif; ?>

</div>

<script>
// Animasi fade-in lembut dan bertahap
document.addEventListener("DOMContentLoaded", () => {
  const fadeEls = document.querySelectorAll(".fade");
  fadeEls.forEach((el, index) => {
    setTimeout(() => {
      el.classList.add("show");
    }, index * 150); // delay lembut antar elemen
  });
});
</script>

</body>
</html>
