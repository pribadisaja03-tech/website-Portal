<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); 
    exit;
}

include "../config.php";

$msg = "";
$type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul    = trim($_POST['judul']);
    $daerah   = trim($_POST['daerah']);
    $kategori = trim($_POST['kategori']);
    $isi      = trim($_POST['isi']);
    $lat      = !empty($_POST['lat']) ? floatval($_POST['lat']) : null;
    $lng      = !empty($_POST['lng']) ? floatval($_POST['lng']) : null;
    $user_id  = $_SESSION['user_id'];

    $result = $conn->query("SELECT MAX(no_urut) AS last FROM cerita");
    $row = $result->fetch_assoc();
    $no_urut = ($row['last'] ?? 0) + 1;

    $uploadDir = "../uploads/";
    $gambar = null;
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . "_" . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir.$gambar);
    }

    $audio = null;
    if (!empty($_FILES['audio']['name'])) {
        $audio = time() . "_" . basename($_FILES['audio']['name']);
        move_uploaded_file($_FILES['audio']['tmp_name'], $uploadDir.$audio);
    }

    $sql = "INSERT INTO cerita 
            (no_urut, judul, daerah, kategori, isi, gambar, audio, status, user_id, jumlah_baca, created_at, lat, lng) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'approved', ?, 0, NOW(), ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssidd", $no_urut, $judul, $daerah, $kategori, $isi, $gambar, $audio, $user_id, $lat, $lng);

    if ($stmt->execute()) {
        // ✅ kalau berhasil, langsung redirect ke dashboard
        header("Location: dashboard.php?msg=added");
        exit;
    } else {
        $msg = "❌ Gagal tambah cerita. Error: " . $stmt->error;
        $type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tambah Cerita (Admin)</title>

<!-- ✅ CSS lengkap -->
<style>
body {
  font-family: Arial, sans-serif;
  background: #f4f6f9;
  margin: 0;
  padding: 20px;
}

.container {
  max-width: 650px;
  margin: auto;
  background: #fff;
  padding: 25px 30px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

h2 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}

.alert {
  padding: 12px 15px;
  border-radius: 6px;
  margin-bottom: 20px;
  font-weight: bold;
  text-align: center;
}

.alert.success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert.error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

form label {
  display: block;
  font-weight: bold;
  margin-bottom: 6px;
  color: #444;
}

form input[type="text"],
form input[type="file"],
form select,
form textarea {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 6px;
  box-sizing: border-box;
  font-size: 14px;
}

form textarea {
  resize: vertical;
}

.form-actions {
  display: flex;
  gap: 10px;
}

button,
a.btn-cancel {
  flex: 1;
  text-align: center;
  padding: 12px;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  text-decoration: none;
}

button {
  background: #007bff;
  border: none;
  color: white;
}

button:hover {
  background: #0056b3;
}

a.btn-cancel {
  background: #6c757d;
  color: white;
}

a.btn-cancel:hover {
  background: #5a6268;
}

#map {
  height: 300px;
  width: 100%;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 8px;
}
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
    <div class="container">
        <h2>➕ Tambah Cerita</h2>

        <?php if (!empty($msg)): ?>
            <div class="alert <?php echo $type; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label for="judul">Judul</label>
            <input type="text" id="judul" name="judul" required>

            <label for="daerah">Daerah</label>
            <select id="daerah" name="daerah" required>
                <option value="Lombok Timur">Lombok Timur</option>
                <option value="Lombok Tengah">Lombok Tengah</option>
                <option value="Lombok Barat">Lombok Barat</option>
                <option value="Lombok Utara">Lombok Utara</option>
                <option value="Lombok">Lombok</option>
            </select>

            <label for="kategori">Kategori</label>
            <select id="kategori" name="kategori" required>
                <option value="Legenda">Legenda</option>
                <option value="Mitos">Mitos</option>
                <option value="Sejarah">Sejarah</option>
                <option value="Budaya">Budaya</option>
                <option value="Cerita Rakyat">Cerita Rakyat</option>
            </select>

            <label for="isi">Isi Cerita</label>
            <textarea id="isi" name="isi" rows="5" required></textarea>

            <label>Pilih Lokasi di Peta</label>
            <div id="map"></div>

            <label for="lat">Latitude</label>
            <input type="text" id="lat" name="lat" readonly required>

            <label for="lng">Longitude</label>
            <input type="text" id="lng" name="lng" readonly required>

            <label for="gambar">Gambar</label>
            <input type="file" id="gambar" name="gambar" accept="image/png,image/jpeg">

            <label for="audio">Audio (opsional)</label>
            <input type="file" id="audio" name="audio" accept="audio/mpeg">

            <div class="form-actions">
                <button type="submit">Simpan</button>
                <a href="dashboard.php" class="btn-cancel">Batal</a>
            </div>
        </form>
    </div>

<script>
  var map = L.map('map').setView([-8.5657, 116.3510], 9);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
  }).addTo(map);

  var marker;
  map.on('click', function(e) {
      var lat = e.latlng.lat.toFixed(6);
      var lng = e.latlng.lng.toFixed(6);
      document.getElementById("lat").value = lat;
      document.getElementById("lng").value = lng;

      if (marker) map.removeLayer(marker);
      marker = L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Cerita").openPopup();
  });
</script>
</body>
</html>
