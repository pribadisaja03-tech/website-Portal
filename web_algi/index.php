<?php
session_start();
include "config.php";
include "functions.php";

// Ambil filter pencarian
$keyword  = $_GET['keyword']  ?? '';
$daerah   = $_GET['daerah']   ?? '';
$kategori = $_GET['kategori'] ?? '';

if (!empty($keyword) || !empty($daerah) || !empty($kategori)) {
    $all = getCeritaFiltered($keyword, $daerah, $kategori);
} else {
    $all = getCerita();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Cerita Rakyat Lombok</title>
  <link rel="stylesheet" href="style.css">

  <!-- Leaflet -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <style>
    html {
      scroll-behavior: smooth; /* efek scroll lembut */
    }

    body { 
      font-family: 'Poppins', sans-serif; 
      margin:0; 
      padding:0; 
      background:#1e3a8a;   
      color:white;          
      overflow-x:hidden;
    }

    /* ======== BANNER ======== */
    .banner {
      width: 100%;
      height: 400px;
      position: relative;
      overflow: hidden;
      background: url('uploads/a2.png') center/cover no-repeat fixed; /* efek parallax */
      display: flex;
      align-items: center;
      justify-content: center;
      border-bottom: 5px solid #facc15;
    }

    .banner::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.3);
    }

    .banner-title {
      position: relative;
      color: #fff;
      padding: 12px 28px;
      background: rgba(0,0,0,0.55);
      border-radius: 12px;
      font-size: 2rem;
      font-weight: 600;
      letter-spacing: 1px;
      z-index: 2;
    }

    /* ======== DAFTAR CERITA ======== */
    .daftar-cerita { padding:50px 10%; }
    .daftar-cerita h2 { text-align:center; margin-bottom:30px; font-weight:600; }
    .grid {
      display:grid; 
      grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); 
      gap:20px;
    }

    .book-card {
      background:white; 
      border-radius:12px; 
      box-shadow:0 3px 10px rgba(0,0,0,0.08);
      overflow:hidden; 
      transition:
        transform 1.2s cubic-bezier(0.22, 1, 0.36, 1),
        box-shadow 1.2s ease,
        opacity 2.2s ease-out;
      color:black;
      cursor:pointer;
      opacity:0;
      transform:translateY(40px);
    }

    .book-card.show {
      opacity:1;
      transform:translateY(0);
    }

    .book-card:hover {
      transform:translateY(-8px) scale(1.03);
      box-shadow:0 8px 22px rgba(0,0,0,0.18);
    }

    .book-cover { width:100%; height:200px; object-fit:cover; }
    .book-meta { padding:10px; font-size:.9rem; color:#555; display:flex; justify-content:space-between; }
    .book-title { padding:0 10px; margin:10px 0; font-size:1.1rem; color:#1e293b; }
    .book-author, .book-category { padding:0 10px; font-size:.9rem; color:#475569; }

    /* ======== GALERI & EDUKASI ======== */
    .features {
      display:grid; 
      grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
      gap:20px; 
      padding:20px 20%;
    }
    .feature-box {
      background:white; 
      padding:15px; 
      border-radius:12px; 
      text-align:center;
      color:#1e3a8a;
      font-size:0.9rem;
      box-shadow:0 4px 10px rgba(0,0,0,0.08); 
      transition:transform 0.4s ease, box-shadow 0.4s ease, opacity 2s ease-out;
      text-decoration:none;
      opacity:0;
      transform:translateY(40px);
    }
    .feature-box.show {
      opacity:1;
      transform:translateY(0);
    }
    .feature-box:hover { transform:translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,0.15); }
    .feature-box h3 { font-size:1rem; margin-bottom:8px; }
    .feature-box p { font-size:0.8rem; margin:0; }

    /* ======== PETA ======== */
    #map { 
      width:100%; 
      height:130vh;   
      border-radius:12px; 
      margin-top:20px; 
      box-shadow:0 4px 10px rgba(0,0,0,0.1);
      opacity:0;
      transform:translateY(40px);
      transition:opacity 2.5s ease, transform 2.5s ease;
    }
    #map.show {
      opacity:1;
      transform:translateY(0);
    }

    @media (max-width: 1024px) {
      #map { height:100vh; }
    }
    @media (max-width: 600px) {
      #map { height:80vh; }
      .banner { height:250px; background-attachment: scroll; } /* nonaktifkan parallax di HP */
      .banner-title { font-size: 1.3rem; padding: 8px 16px; }
    }

    .fade-in { animation:fadeInUp 1.8s ease; }
    @keyframes fadeInUp { 
      from{opacity:0;transform:translateY(30px);} 
      to{opacity:1;transform:translateY(0);} 
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>


<main class="container">

  <!-- Daftar Cerita -->
  <section class="daftar-cerita fade-in" id="cardsContainer">
    <h2>Daftar Cerita Rakyat</h2>
    <div class="grid">
      <?php if ($all && $all->num_rows > 0): ?>
        <?php while ($row = $all->fetch_assoc()): ?>
          <div class="book-card" data-daerah="<?= htmlspecialchars($row['daerah']) ?>">
            <a href="<?= isset($_SESSION['user_id']) ? 'baca.php?id='.$row['id'] : 'login.php'; ?>">
              <img src="uploads/<?= !empty($row['gambar']) ? htmlspecialchars($row['gambar']) : 'default.png' ?>" 
                   alt="<?= htmlspecialchars($row['judul']) ?>" class="book-cover"
                   onerror="this.onerror=null;this.src='uploads/default.png';">
            </a>
            <div class="book-meta">
              <span>📘 <?= $row['jumlah_baca'] ?? 0 ?></span>
              <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (!empty($row['avg_rating'])): ?>
                  <span>
                    <?php for ($i=1; $i<=5; $i++): ?>
                      <span class="<?= $i <= round($row['avg_rating']) ? 'star active' : 'star' ?>">★</span>
                    <?php endfor; ?>
                    (<?= $row['avg_rating'] ?>)
                  </span>
                <?php else: ?>
                  <span>⭐ Belum ada rating</span>
                <?php endif; ?>
              <?php else: ?>
                <span>⭐ (Login untuk lihat)</span>
              <?php endif; ?>
            </div>
            <h3 class="book-title"><?= htmlspecialchars($row['judul']) ?></h3>
            <p class="book-author">Daerah: <?= htmlspecialchars($row['daerah']) ?></p>
            <p class="book-category">Kategori: <?= htmlspecialchars($row['kategori'] ?? '-') ?></p>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center;">Tidak ada cerita ditemukan.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Galeri & Edukasi -->
  <section class="features fade-in">
    <a href="galeri.php" class="feature-box">
      <h3>🖼️ Galeri</h3>
      <p>Lihat tradisi & budaya Sasak.</p>
    </a>
    <a href="edukasi.php" class="feature-box">
      <h3>🎓 Edukasi</h3>
      <p>Sumber belajar budaya lokal.</p>
    </a>
  </section>

  <!-- Peta -->
  <section class="map-section fade-in">
    <h2>Peta Interaktif Cerita Rakyat Lombok</h2>
    <div id="map"></div>
  </section>

</main>

<?php include 'footer.php'; ?>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="script.js"></script>

<?php
$result = $conn->query("SELECT id, judul, daerah, lat, lng 
                        FROM cerita 
                        WHERE lat IS NOT NULL 
                          AND lng IS NOT NULL 
                          AND status = 'approved'");
$cerita_with_map = [];
while ($row = $result->fetch_assoc()) {
    $cerita_with_map[] = $row;
}
?>
<script>
  var ceritaData = <?= json_encode($cerita_with_map, JSON_UNESCAPED_UNICODE); ?>;

  document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".book-card");
    const features = document.querySelectorAll(".feature-box");
    const map = document.getElementById("map");

    const delayPerCard = 450;
    const delayFeature = 400;
    const delayMap = 1500;

    cards.forEach((card, i) => {
      setTimeout(() => card.classList.add("show"), i * delayPerCard);
    });

    setTimeout(() => {
      features.forEach((f, j) => {
        setTimeout(() => f.classList.add("show"), j * delayFeature);
      });
    }, cards.length * delayPerCard + 800);

    setTimeout(() => {
      map.classList.add("show");
    }, cards.length * delayPerCard + features.length * delayFeature + delayMap);
  });
</script>

</body>
</html>
