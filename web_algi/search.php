<?php
include "config.php";
include "functions.php";
if (session_status() === PHP_SESSION_NONE) session_start();

$q = $_GET['q'] ?? '';
$qLike = "%" . $q . "%";

$stmt = $conn->prepare("
    SELECT * FROM cerita 
    WHERE judul LIKE ? 
       OR daerah LIKE ? 
       OR kategori LIKE ?
       AND status='approved'
    ORDER BY created_at DESC
");
$stmt->bind_param("sss", $qLike, $qLike, $qLike);
$stmt->execute();
$result = $stmt->get_result();

include "navbar.php";
?>

<main class="container">
  <h2>Hasil Pencarian: "<span style="color:#d35400"><?= htmlspecialchars($q) ?></span>"</h2>

  <div class="card-container">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
          <img src="uploads/<?= !empty($row['gambar']) ? htmlspecialchars($row['gambar']) : 'default.png' ?>" 
               alt="<?= htmlspecialchars($row['judul']) ?>"
               onerror="this.onerror=null;this.src='uploads/default.png';">

          <div class="card-body">
            <h3><?= htmlspecialchars($row['judul']) ?></h3>

            <!-- Daerah & Kategori -->
            <p><strong>Daerah:</strong> <?= htmlspecialchars($row['daerah']) ?></p>
            <p><strong>Kategori:</strong> <?= htmlspecialchars($row['kategori']) ?></p>

            <!-- Tombol Baca -->
            <a href="baca.php?id=<?= $row['id'] ?>" class="btn">
              <i class="fas fa-book-open"></i> Baca Selengkapnya
            </a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="margin-top:20px;">Tidak ada hasil untuk pencarian "<b><?= htmlspecialchars($q) ?></b>".</p>
    <?php endif; ?>
  </div>
</main>

<?php include "footer.php"; ?>

<!-- CSS -->
<style>
.container {
  max-width: 1000px;
  margin: 30px auto;
  padding: 0 15px;
}

h2 {
  margin-bottom: 20px;
}

.card-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.card {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  transition: transform 0.2s;
}
.card:hover {
  transform: translateY(-5px);
}

.card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.card-body {
  padding: 15px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.card-body h3 {
  margin: 0 0 10px;
  font-size: 18px;
  color: #2c3e50;
}

.btn {
  display: inline-block;
  text-align: center;
  padding: 8px 12px;
  border-radius: 8px;
  background: #1a120f;
  color: #fff;
  font-weight: bold;
  text-decoration: none;
  transition: 0.3s;
}
.btn:hover {
  background: #d35400;
}
</style>
