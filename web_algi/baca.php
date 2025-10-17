<?php
include 'functions.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: index.php");
  exit;
}
$id = intval($_GET['id']);
$detail = getCeritaById($id);
if (!$detail || $detail['status'] !== 'approved') {
  if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location:index.php");
    exit;
  }
}

// Simpan history
simpanHistory($id);

// cek favorit
$isFavorit = false;
if (isset($_SESSION['user_id'])) {
  global $conn;
  $stmt = $conn->prepare("SELECT 1 FROM favorit WHERE user_id=? AND cerita_id=?");
  $stmt->bind_param("ii", $_SESSION['user_id'], $id);
  $stmt->execute();
  $cek = $stmt->get_result();
  if ($cek && $cek->num_rows > 0) $isFavorit = true;
  $stmt->close();
}

include 'navbar.php';
?>

<main class="container">
  <!-- Detail Cerita -->
  <div class="detail-wrapper fade-in">
    <div class="detail-cover">
      <img src="<?= !empty($detail['gambar']) ? 'uploads/' . htmlspecialchars($detail['gambar']) : 'uploads/default.png' ?>"
           alt="<?= htmlspecialchars($detail['judul']) ?>"
           onerror="this.onerror=null;this.src='uploads/default.png';">
    </div>

    <div class="detail-info">
      <h1 class="judul-cerita"><?= htmlspecialchars($detail['judul']) ?></h1>
      <div class="meta-info">
        <p><strong>Asal Daerah:</strong> <?= htmlspecialchars($detail['daerah']) ?></p>
        <p><strong>Dipublikasikan:</strong> <?= $detail['created_at'] ?></p>
      </div>

      <!-- 🎧 Tombol Audio -->
      <?php if (!empty($detail['audio'])): ?>
      <div class="audio-player fade-in">
        <button id="playBtn" class="audio-btn"><i class="fa fa-play"></i> Dengarkan Cerita</button>
        <audio id="audioPlayer" src="uploads/<?= htmlspecialchars($detail['audio']) ?>"></audio>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Cerita -->
  <article class="isi-cerita fade-in">
    <h2>Cerita Lengkap</h2>
    <p><?= nl2br(htmlspecialchars($detail['isi'])) ?></p>
  </article>

  <!-- Tombol Favorit -->
  <div class="detail-buttons fade-in">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="favorit.php?action=<?= $isFavorit ? 'remove' : 'add' ?>&id=<?= $id ?>"
         class="btn <?= $isFavorit ? 'btn-danger' : 'btn-favorit' ?>">
         <?= $isFavorit ? '💔 Hapus Favorit' : '❤️ Tambah Favorit' ?>
      </a>
    <?php else: ?>
      <a href="login.php" class="btn btn-secondary">Login untuk Favorit</a>
    <?php endif; ?>
  </div>

  <!-- Pesan sukses/error -->
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success fade-in" style="margin: 20px 0; padding: 12px; background: #eaf9ea; border: 1px solid #b2d8b2; border-radius: 8px; color: #2d6a2d;">
      ✅ Pesan kamu berhasil dikirim! Akan ditampilkan setelah direview Admin.
    </div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger fade-in" style="margin: 20px 0; padding: 12px; background: #fdecea; border: 1px solid #f5c2c7; border-radius: 8px; color: #842029;">
      ⚠️ Gagal mengirim komentar. Silakan coba lagi.
    </div>
  <?php endif; ?>

  <!-- Komentar -->
  <section class="komentar fade-in">
    <h2>Komentar</h2>
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="komentar-box fade-in">
      <form action="proses_komentar.php" method="post" class="form-komentar">
        <input type="hidden" name="cerita_id" value="<?= $id ?>">
        <textarea name="isi" maxlength="200" placeholder="Tulis komentar (maks 200 karakter)..." required></textarea>
        <div class="form-bottom">
          <button type="submit" class="btn-kirim">Kirim</button>
          <div class="rating-container">
            <span class="star" data-value="1">★</span>
            <span class="star" data-value="2">★</span>
            <span class="star" data-value="3">★</span>
            <span class="star" data-value="4">★</span>
            <span class="star" data-value="5">★</span>
          </div>
          <input type="hidden" name="rating" id="ratingInput" required>
        </div>
      </form>
    </div>
    <?php else: ?>
      <p><a href="login.php">Login</a> untuk menulis komentar.</p>
    <?php endif; ?>

    <div class="list-komentar">
      <?php
      global $conn;
      $stmt = $conn->prepare("SELECT k.isi, k.rating, k.created_at, u.username
                        FROM komentar k
                        JOIN user u ON k.user_id = u.id
                        WHERE k.cerita_id=? AND k.status='approved'
                        ORDER BY k.created_at DESC");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0):
        while($row = $result->fetch_assoc()):
      ?>
        <div class="komentar-item fade-in">
          <div class="komentar-content">
            <div class="komentar-header">
              <div>
                <strong><?= htmlspecialchars($row['username']) ?></strong>
                <small>Posted at <?= date("H:i, d F Y", strtotime($row['created_at'])) ?></small>
              </div>
              <div class="komentar-rating"><?= str_repeat("⭐", $row['rating']) ?></div>
            </div>
            <div class="komentar-isi"><?= htmlspecialchars($row['isi']) ?></div>
          </div>
        </div>
      <?php endwhile; else: ?>
        <p>Belum ada komentar.</p>
      <?php endif; ?>
    </div>
  </section>

  <div class="back-btn fade-in">
    <a href="index.php" class="btn btn-secondary">← Kembali</a>
  </div>
</main>

<?php include 'footer.php'; ?>

<style>
.container { max-width: 850px; margin: 30px auto; padding: 0 15px; }
.detail-wrapper, .isi-cerita, .komentar {
  background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 25px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.detail-cover img { width: 100%; max-height: 400px; object-fit: cover; border-radius: 12px; }

.detail-info {
  text-align: center;
  margin-top: 20px;
}
.detail-info .judul-cerita {
  font-size: 2rem;
  margin-bottom: 10px;
  color: #222;
}
.detail-info .meta-info {
  font-size: 14px;
  color: #555;
  margin-bottom: 15px;
}
.meta-info p { margin: 3px 0; }

/* Audio player */
.audio-player { text-align: center; margin-top: 10px; }
.audio-btn {
  background: linear-gradient(90deg, #FF9800, #E65100);
  color: white;
  border: none;
  padding: 12px 22px;
  border-radius: 30px;
  font-size: 1rem;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
}
.audio-btn:hover { transform: scale(1.05); background: linear-gradient(90deg, #FFA726, #EF6C00); }
.audio-btn.playing { background: linear-gradient(90deg, #E53935, #B71C1C); }

/* Lainnya tetap sama */
.detail-buttons { margin: 20px 0; text-align: center; }
.back-btn { margin: 20px 0; text-align: left; }
.btn {
  display: inline-block; padding: 8px 18px; border-radius: 8px; text-decoration: none;
  font-weight: bold; cursor: pointer;
}
.btn-favorit { background: #ff4d4d; color: #fff; }
.btn-favorit:hover { background: #e63e3e; }
.btn-danger { background: #999; color: #fff; }
.btn-secondary { background: #ddd; color: #333; }
.btn-secondary:hover { background: #ccc; }

/* Komentar & Rating tetap */
.komentar-box textarea { width: 100%; height: 80px; border-radius: 8px; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; font-size: 14px; }
.form-bottom { display: flex; justify-content: space-between; align-items: center; }
.rating-container { font-size: 26px; color: #ccc; cursor: pointer; display: flex; gap: 6px; }
.rating-container .star.selected { color: #f39c12; }

/* Fade In Animasi */
.fade-in {
  opacity: 0;
  transform: translateY(25px);
  transition: opacity 0.8s ease-out, transform 0.8s ease-out;
}
.fade-in.show {
  opacity: 1;
  transform: translateY(0);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const stars = document.querySelectorAll('.rating-container .star');
  const ratingInput = document.getElementById('ratingInput');
  stars.forEach((star, index) => {
    star.addEventListener('click', () => {
      ratingInput.value = index + 1;
      stars.forEach((s, i) => s.classList.toggle('selected', i <= index));
    });
  });

  const playBtn = document.getElementById('playBtn');
  const audio = document.getElementById('audioPlayer');
  if (playBtn && audio) {
    playBtn.addEventListener('click', () => {
      if (audio.paused) {
        audio.play();
        playBtn.textContent = '⏸️ Pause Cerita';
        playBtn.classList.add('playing');
      } else {
        audio.pause();
        playBtn.textContent = '▶️ Dengarkan Cerita';
        playBtn.classList.remove('playing');
      }
    });
    audio.addEventListener('ended', () => {
      playBtn.textContent = '▶️ Dengarkan Cerita';
      playBtn.classList.remove('playing');
    });
  }

  // Efek Fade-in Saat Scroll + Stagger
  const fadeEls = document.querySelectorAll('.fade-in');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
      if (entry.isIntersecting) {
        setTimeout(() => {
          entry.target.classList.add('show');
        }, index * 120); // jeda antar elemen
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15 });

  fadeEls.forEach(el => observer.observe(el));
});
</script>
