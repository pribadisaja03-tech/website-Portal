<?php
// user/edit_cerita.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
include "../config.php";

$uid = (int) $_SESSION['user_id'];
$id  = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data cerita milik user
$stmt = $conn->prepare("SELECT * FROM cerita WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $uid);
$stmt->execute();
$cerita = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cerita) {
    die("❌ Cerita tidak ditemukan atau bukan milik Anda.");
}

$error = '';
$allowedImgExt = ['jpg','jpeg','png','gif'];
$maxImgSize = 2 * 1024 * 1024; // 2MB
$maxAudioSize = 5 * 1024 * 1024; // 5MB

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // sanitize
    $judul    = trim($_POST['judul'] ?? '');
    $daerah   = trim($_POST['daerah'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $isi      = trim($_POST['isi'] ?? '');
    $lat      = isset($_POST['lat']) ? (float) $_POST['lat'] : 0.0;
    $lng      = isset($_POST['lng']) ? (float) $_POST['lng'] : 0.0;

    if ($judul === '' || $isi === '') {
        $error = "Judul dan isi tidak boleh kosong.";
    } else {
        $gambar = $cerita['gambar'];
        $audio  = $cerita['audio'];

        // Upload gambar baru jika ada
        if (!empty($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $imgExt = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            if (!in_array($imgExt, $allowedImgExt)) {
                $error = "Format gambar tidak didukung. (jpg, jpeg, png, gif)";
            } elseif ($_FILES['gambar']['size'] > $maxImgSize) {
                $error = "Ukuran gambar maksimal 2MB.";
            } else {
                $gambarName = time() . "_img." . $imgExt;
                $targetGambar = "../uploads/" . $gambarName;
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetGambar)) {
                    $gambar = $gambarName;
                } else {
                    $error = "Gagal mengunggah gambar.";
                }
            }
        }

        // Upload audio baru jika ada
        if ($error === '' && !empty($_FILES['audio']['name']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
            $audioExt = strtolower(pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION));
            if ($audioExt !== 'mp3') {
                $error = "Format audio harus .mp3";
            } elseif ($_FILES['audio']['size'] > $maxAudioSize) {
                $error = "Ukuran audio maksimal 5MB.";
            } else {
                $audioName = time() . "_aud." . $audioExt;
                $targetAudio = "../uploads/" . $audioName;
                if (move_uploaded_file($_FILES['audio']['tmp_name'], $targetAudio)) {
                    $audio = $audioName;
                } else {
                    $error = "Gagal mengunggah audio.";
                }
            }
        }

        // Jika tidak ada error, simpan perubahan
        if ($error === '') {
            $stmt = $conn->prepare("UPDATE cerita 
                SET judul=?, daerah=?, kategori=?, isi=?, gambar=?, audio=?, lat=?, lng=?, 
                    status='pending_edit', alasan_reject=NULL, updated_at=NOW()
                WHERE id=? AND user_id=?");
            $stmt->bind_param("ssssssddii", $judul, $daerah, $kategori, $isi, $gambar, $audio, $lat, $lng, $id, $uid);
            if (!$stmt->execute()) {
                $error = "Gagal menyimpan: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    if ($error === '') {
        header("Location: profil.php?msg=edit_pending");
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Edit Cerita</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
body {
  font-family: Arial, Helvetica, sans-serif;
  background:#f4f6f9;
  margin:0;
  padding:0;
}
.container {
  max-width:800px;
  margin:30px auto;
  background:#fff;
  padding:22px;
  border-radius:10px;
  box-shadow:0 4px 10px rgba(0,0,0,0.08);
}
h2 {
  margin-top:0;
  margin-bottom:20px;
  color:#333;
}
label {
  font-weight:600;
  display:block;
  margin-top:10px;
}
input[type=text], textarea, select, input[type=file] {
  width: 100%;
  padding: 10px;
  margin-top:6px;
  margin-bottom:14px;
  border-radius:6px;
  border:1px solid #ccc;
  font-size:14px;
  box-sizing: border-box; /* ✅ agar rata semua */
}
textarea {
  resize: vertical;
}
#map {
  height:300px;
  margin-bottom:12px;
  border-radius:6px;
}
.error {
  background:#ffe6e6;
  border:1px solid #ffbcbc;
  padding:10px;
  color:#900;
  margin-bottom:12px;
  border-radius:6px;
}
.btn-update {
  background:#28a745;
  color:#fff;
  padding:10px 18px;
  border-radius:6px;
  border:none;
  cursor:pointer;
  font-weight:bold;
}
.btn-cancel {
  background:#dc3545;
  color:#fff;
  padding:10px 18px;
  border-radius:6px;
  text-decoration:none;
  display:inline-block;
  margin-left:8px;
}
</style>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
<div class="container">
  <h2>✏ Edit Cerita</h2>
  <?php if ($error !== ''): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <label>Judul</label>
    <input type="text" name="judul" value="<?= htmlspecialchars($cerita['judul']) ?>" required>

    <label>Daerah</label>
    <select name="daerah" required>
      <?php
      $daerahOptions = ["Lombok Timur","Lombok Tengah","Lombok Barat","Lombok Utara","Mataram"];
      foreach ($daerahOptions as $opt) {
        $sel = ($cerita['daerah'] == $opt) ? "selected" : "";
        echo "<option value=\"".htmlspecialchars($opt)."\" $sel>".htmlspecialchars($opt)."</option>";
      }
      ?>
    </select>

    <label>Kategori</label>
    <select name="kategori" required>
      <?php
      $kategoriOptions = ["Legenda","Mitos","Sejarah","Budaya","Cerita Rakyat"];
      foreach ($kategoriOptions as $opt) {
        $sel = ($cerita['kategori'] == $opt) ? "selected" : "";
        echo "<option value=\"".htmlspecialchars($opt)."\" $sel>".htmlspecialchars($opt)."</option>";
      }
      ?>
    </select>

    <label>Isi Cerita</label>
    <textarea name="isi" rows="8" required><?= htmlspecialchars($cerita['isi']) ?></textarea>

    <label>Pilih Lokasi di Peta</label>
    <div id="map"></div>

    <label>Latitude</label>
    <input type="text" id="lat" name="lat" value="<?= htmlspecialchars($cerita['lat']) ?>" readonly required>

    <label>Longitude</label>
    <input type="text" id="lng" name="lng" value="<?= htmlspecialchars($cerita['lng']) ?>" readonly required>

    <label>Gambar</label>
    <?php if (!empty($cerita['gambar'])): ?>
      <div style="margin-bottom:8px;"><img src="../uploads/<?= htmlspecialchars($cerita['gambar']) ?>" style="max-width:160px;"></div>
    <?php endif; ?>
    <input type="file" name="gambar">

    <label>Audio (mp3)</label>
    <?php if (!empty($cerita['audio'])): ?>
      <div style="margin-bottom:8px;"><small>File sekarang: <?= htmlspecialchars($cerita['audio']) ?></small></div>
    <?php endif; ?>
    <input type="file" name="audio" accept=".mp3">

    <div style="margin-top:18px;">
      <button type="submit" class="btn-update">💾 Simpan</button>
      <a href="profil.php" class="btn-cancel">❌ Batal</a>
    </div>
  </form>
</div>

<script>
var lat = <?= ($cerita['lat'] !== null && $cerita['lat'] !== '') ? (float)$cerita['lat'] : -8.5657 ?>;
var lng = <?= ($cerita['lng'] !== null && $cerita['lng'] !== '') ? (float)$cerita['lng'] : 116.3510 ?>;
var map = L.map('map').setView([lat, lng], 9);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
var marker = L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Cerita").openPopup();
map.on('click', function(e){
  var la = e.latlng.lat.toFixed(6);
  var ln = e.latlng.lng.toFixed(6);
  document.getElementById('lat').value = la;
  document.getElementById('lng').value = ln;
  if (marker) map.removeLayer(marker);
  marker = L.marker([la, ln]).addTo(map).bindPopup("Lokasi Baru").openPopup();
});
</script>
</body>
</html>
