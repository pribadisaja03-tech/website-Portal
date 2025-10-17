<?php
session_start();
include 'functions.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ====== Handle aksi hapus ======
if (isset($_GET['hapus_all']) && $_GET['hapus_all'] == 1) {
    $stmt = $conn->prepare("DELETE FROM history WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: history.php");
    exit;
}

if (isset($_GET['hapus_id'])) {
    $history_id = intval($_GET['hapus_id']);
    $stmt = $conn->prepare("DELETE FROM history WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $history_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: history.php");
    exit;
}

// ====== Ambil data history ======
$stmt = $conn->prepare("
    SELECT h.id AS history_id, c.id AS cerita_id, c.judul, c.daerah, c.gambar, h.waktu_baca
    FROM history h
    JOIN cerita c ON h.cerita_id = c.id
    WHERE h.user_id = ?
    ORDER BY h.waktu_baca DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

include "navbar.php";
?>

<!-- CSS Fade -->
<style>
.fade-in {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}
.fade-in.show {
  opacity: 1;
  transform: translateY(0);
}
</style>

<main class="container">
  <h1 class="fade-in">📖 Riwayat Bacaan</h1>

  <?php if ($result->num_rows > 0): ?>
    <!-- Tombol hapus semua -->
    <div style="margin-bottom:15px;" class="fade-in">
      <a href="history.php?hapus_all=1" 
         class="btn btn-danger"
         onclick="return confirm('Yakin ingin menghapus semua riwayat bacaan?')">
         🗑 Hapus Semua
      </a>
    </div>

    <div class="grid">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card fade-in">
          <?php if (!empty($row['gambar'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" 
                 alt="<?= htmlspecialchars($row['judul']) ?>" 
                 class="card-img">
          <?php endif; ?>

          <div class="card-body">
            <h3><?= htmlspecialchars($row['judul']) ?></h3>
            <span class="meta">
              <?= htmlspecialchars($row['daerah']) ?> — 
              <?= $row['waktu_baca'] ?>
            </span>
          </div>
          
          <div class="card-actions">
            <a href="baca.php?id=<?= $row['cerita_id'] ?>" class="btn">📖 Baca Lagi</a>
            <a href="history.php?hapus_id=<?= $row['history_id'] ?>" 
               class="btn btn-danger"
               onclick="return confirm('Hapus riwayat ini?')">
               ❌ Hapus
            </a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="fade-in">Belum ada history bacaan. Yuk mulai baca cerita! 😊</p>
  <?php endif; ?>
</main>

<!-- JS Fade Effect -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const elements = document.querySelectorAll(".fade-in");
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("show");
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2 });

  elements.forEach(el => observer.observe(el));
});
</script>

<?php include "footer.php"; ?>
