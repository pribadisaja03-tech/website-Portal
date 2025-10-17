<?php
session_start();
include "functions.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$cerita_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Tambah atau hapus favorit
if ($action === 'add' && $cerita_id > 0) {
    global $conn;
    $stmt = $conn->prepare("INSERT IGNORE INTO favorit (user_id, cerita_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $cerita_id);
    $stmt->execute();
    $stmt->close();
    header("Location: baca.php?id=$cerita_id");
    exit;
}
if ($action === 'remove' && $cerita_id > 0) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM favorit WHERE user_id=? AND cerita_id=?");
    $stmt->bind_param("ii", $user_id, $cerita_id);
    $stmt->execute();
    $stmt->close();
    header("Location: baca.php?id=$cerita_id");
    exit;
}

// Ambil daftar favorit user
global $conn;
$sql = "SELECT f.id, c.judul, c.daerah, c.gambar, c.created_at, c.id AS cerita_id
        FROM favorit f
        JOIN cerita c ON f.cerita_id = c.id
        WHERE f.user_id = ?
        ORDER BY f.id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
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
  <h1 class="fade-in">❤️ Daftar Cerita Favorit</h1>

  <?php if ($res && $res->num_rows > 0): ?>
    <div class="grid">
      <?php while ($row = $res->fetch_assoc()): ?>
        <div class="card fade-in">
          <?php if (!empty($row['gambar'])): ?>
            <img src="uploads/<?= htmlspecialchars($row['gambar']) ?>" 
                 alt="<?= htmlspecialchars($row['judul']) ?>" 
                 class="card-img">
          <?php endif; ?>
          <div class="card-body">
            <h3><?= htmlspecialchars($row['judul']) ?></h3>
            <span class="meta"><?= htmlspecialchars($row['daerah']) ?> — <?= $row['created_at'] ?></span>
          </div>
          <div class="card-actions">
            <a href="baca.php?id=<?= $row['cerita_id'] ?>" class="btn">📖 Baca</a>
            <a href="favorit.php?action=remove&id=<?= $row['cerita_id'] ?>" class="btn btn-danger">❌ Hapus</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p class="fade-in">Belum ada cerita favorit. Tambahkan dulu dari halaman baca cerita! 😊</p>
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
