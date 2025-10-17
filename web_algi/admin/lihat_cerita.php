<?php
include '../config.php';
session_start();

// cek login admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// ambil ID cerita dari parameter
if (!isset($_GET['id'])) {
    header("Location: cerita.php");
    exit;
}
$id = (int) $_GET['id'];

/* ---------- PROSES AKSI ---------- */

// Hapus cerita
if (isset($_POST['hapus'])) {
    $stmt = $conn->prepare("DELETE FROM cerita WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: cerita.php?msg=deleted");
    exit;
}

// Setujui cerita
if (isset($_POST['setujui'])) {
    $stmt = $conn->prepare("UPDATE cerita SET status='approved', alasan_reject=NULL WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: cerita.php?msg=approved");
    exit;
}

// Tolak cerita dengan alasan
if (isset($_POST['tolak'])) {
    $alasan = trim($_POST['alasan']);
    if ($alasan == "") $alasan = "Tidak ada alasan diberikan.";
    $stmt = $conn->prepare("UPDATE cerita SET status='rejected', alasan_reject=? WHERE id=?");
    $stmt->bind_param("si", $alasan, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: cerita.php?msg=rejected");
    exit;
}

/* ---------- AMBIL DETAIL CERITA ---------- */
$stmt = $conn->prepare("SELECT c.*, u.username 
                        FROM cerita c 
                        JOIN user u ON c.user_id = u.id 
                        WHERE c.id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$cerita = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cerita) {
    echo "<p>Cerita tidak ditemukan.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Cerita</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f6f9; margin:0; padding:20px; }
.container { max-width:900px; margin:20px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
h1 { margin-top:0; font-size:24px; }
.detail-card h2 { margin-top:0; }
.meta p { margin:4px 0; font-size:14px; }
.gambar-cerita { max-width:100%; margin:15px 0; border-radius:8px; }
.isi-cerita { margin-top:20px; line-height:1.6; text-align:justify; }
.actions { display:flex; gap:10px; flex-wrap:wrap; margin-top:20px; }
.actions form, .actions a, .actions button { display:inline-block; }
.btn {
  padding:8px 14px; border-radius:5px;
  text-decoration:none; color:white; font-size:14px;
  border:none; cursor:pointer;
}
.btn-approve { background:#2ecc71; }
.btn-reject { background:#e67e22; }
.btn-del { background:#e74c3c; }
.btn-back { background:#3498db; display:inline-block; }
.btn:hover { opacity:0.85; }
#rejectForm textarea {
  width:100%; padding:8px; border-radius:5px; border:1px solid #ccc;
}
</style>
</head>
<body>
<div class="container">
  <h1>📖 Detail Cerita</h1>
  <div class="detail-card">
    <h2><?= htmlspecialchars($cerita['judul']) ?></h2>
    <div class="meta">
      <p><strong>Pengirim:</strong> <?= htmlspecialchars($cerita['username']) ?></p>
      <p><strong>Kategori:</strong> <?= htmlspecialchars($cerita['kategori']) ?></p>
      <p><strong>Daerah:</strong> <?= htmlspecialchars($cerita['daerah']) ?></p>
      <p><strong>Tanggal:</strong> <?= date("d-m-Y H:i", strtotime($cerita['created_at'])) ?></p>
      <p><strong>Status:</strong>
        <?php if ($cerita['status'] == 'pending' || $cerita['status'] == 'pending_edit'): ?>
          ⏳ Pending
        <?php elseif ($cerita['status'] == 'approved'): ?>
          ✅ Disetujui
        <?php else: ?>
          ❌ Ditolak
        <?php endif; ?>
      </p>
      <?php if ($cerita['status'] == 'rejected'): ?>
        <p><strong>Alasan Penolakan:</strong> <span style="color:red;"><?= htmlspecialchars($cerita['alasan_reject']) ?></span></p>
      <?php endif; ?>
    </div>

    <?php if (!empty($cerita['gambar'])): ?>
      <img src="../uploads/<?= htmlspecialchars($cerita['gambar']) ?>" alt="Gambar Cerita" class="gambar-cerita">
    <?php endif; ?>

    <div class="isi-cerita"><?= nl2br(htmlspecialchars($cerita['isi'])) ?></div>

    <!-- Tombol aksi -->
    <div class="actions">
      <?php if ($cerita['status'] === 'pending' || $cerita['status'] === 'pending_edit'): ?>
        <!-- APPROVE -->
        <form method="post" onsubmit="return confirm('Setujui cerita ini?')">
          <button type="submit" name="setujui" class="btn btn-approve">✅ Setujui</button>
        </form>
        <!-- TOLAK -->
        <button type="button" class="btn btn-reject" onclick="toggleReject()">❌ Tolak</button>
      <?php endif; ?>

      <!-- HAPUS -->
      <form method="post" onsubmit="return confirm('Yakin hapus cerita ini?')">
        <button type="submit" name="hapus" class="btn btn-del">🗑 Hapus</button>
      </form>

      <!-- KEMBALI -->
      <a href="cerita.php" class="btn btn-back">⬅ Kembali</a>
    </div>

    <!-- FORM PENOLAKAN (HIDDEN) -->
    <form method="post" id="rejectForm" style="display:none; margin-top:15px;">
      <textarea name="alasan" placeholder="Tulis alasan penolakan (wajib)" required rows="2"></textarea>
      <br>
      <button type="submit" name="tolak" class="btn btn-reject" style="margin-top:8px;">Kirim Penolakan</button>
    </form>

  </div>
</div>

<script>
function toggleReject(){
  let f = document.getElementById("rejectForm");
  f.style.display = (f.style.display === "none") ? "block" : "none";
}
</script>
</body>
</html>
