<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}
include "../config.php";

$id = intval($_GET['id']);
$cerita = $conn->query("SELECT * FROM cerita WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul    = $_POST['judul'];
    $daerah   = $_POST['daerah'];
    $kategori = $_POST['kategori'];
    $isi      = $_POST['isi'];
    $lat      = $_POST['lat'];
    $lng      = $_POST['lng'];

    $gambar = $cerita['gambar'];
    $audio  = $cerita['audio'];

    if (!empty($_FILES['gambar']['name'])) {
        $gambarName = time() . "_" . basename($_FILES['gambar']['name']);
        $targetGambar = "../uploads/" . $gambarName;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetGambar)) {
            $gambar = $gambarName;
        }
    }

    if (!empty($_FILES['audio']['name'])) {
        $audioName = time() . "_" . basename($_FILES['audio']['name']);
        $targetAudio = "../uploads/" . $audioName;
        $ext = strtolower(pathinfo($audioName, PATHINFO_EXTENSION));
        if ($ext === "mp3") {
            if (move_uploaded_file($_FILES['audio']['tmp_name'], $targetAudio)) {
                $audio = $audioName;
            }
        } else {
            echo "Format audio harus .mp3!";
            exit;
        }
    }

    $stmt = $conn->prepare("UPDATE cerita 
        SET judul=?, daerah=?, kategori=?, isi=?, gambar=?, audio=?, lat=?, lng=? 
        WHERE id=?");
    $stmt->bind_param("ssssssddi", $judul, $daerah, $kategori, $isi, $gambar, $audio, $lat, $lng, $id);
    $stmt->execute();

    header("Location: dashboard.php?msg=updated");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Cerita</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 800px;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
h2 {
    margin-bottom: 20px;
    color: #333;
}
label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
}
input[type="text"], 
textarea, 
select {
    width: 100%;
    padding: 12px;
    margin-bottom: 18px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 14px;
}
textarea {
    resize: vertical;
    min-height: 120px;
}
#map {
    height: 300px;
    margin-bottom: 18px;
    border-radius: 8px;
}
.form-actions {
    display: flex;
    justify-content: space-between;
    gap: 20px;         /* kasih jarak antar tombol */
    margin-top: 30px;  /* jarak dari input terakhir */
}

.btn-update, .btn-cancel {
    width: 48%;        /* tombol panjang tapi ada jarak */
    padding: 12px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    font-size: 15px;
    cursor: pointer;
    text-align: center;
}

.btn-update {
    background: #28a745;
    color: #fff;
}
.btn-update:hover {
    background: #1e7e34;
}

.btn-cancel {
    background: #dc3545;
    color: #fff;
    text-decoration: none;
    display: inline-block;
}
.btn-cancel:hover {
    background: #a71d2a;
}

</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
<div class="container">
    <h2>✏ Edit Cerita</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="judul">Judul</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($cerita['judul']) ?>" required>

        <label for="daerah">Daerah</label>
        <select name="daerah" required>
            <?php
            $daerahOptions = ["Lombok Timur","Lombok Tengah","Lombok Barat","Lombok Utara","Lombok"];
            foreach ($daerahOptions as $opt) {
                $selected = ($cerita['daerah'] == $opt) ? "selected" : "";
                echo "<option value='$opt' $selected>$opt</option>";
            }
            ?>
        </select>

        <label for="kategori">Kategori</label>
        <select name="kategori" required>
            <?php
            $kategoriOptions = ["Legenda","Mitos","Sejarah","Budaya","Cerita Rakyat"];
            foreach ($kategoriOptions as $opt) {
                $selected = ($cerita['kategori'] == $opt) ? "selected" : "";
                echo "<option value='$opt' $selected>$opt</option>";
            }
            ?>
        </select>

        <label for="isi">Isi Cerita</label>
        <textarea name="isi" required><?= htmlspecialchars($cerita['isi']) ?></textarea>

        <label>Pilih Lokasi di Peta</label>
        <div id="map"></div>

        <label for="lat">Latitude</label>
        <input type="text" id="lat" name="lat" value="<?= $cerita['lat'] ?>" readonly required>

        <label for="lng">Longitude</label>
        <input type="text" id="lng" name="lng" value="<?= $cerita['lng'] ?>" readonly required>

        <label for="gambar">Gambar</label>
        <input type="file" name="gambar"> 
        (sekarang: <?= $cerita['gambar'] ?>)

        <label for="audio">Audio (mp3)</label>
        <input type="file" name="audio" accept=".mp3"> 
        (sekarang: <?= $cerita['audio'] ?>)

       <div class="form-actions">
    <button type="submit" class="btn-update">Update</button>
    <a href="dashboard.php" class="btn-cancel">Batal</a>
</div>

    </form>
</div>

<script>
  var lat = <?= $cerita['lat'] ?: -8.5657 ?>;
  var lng = <?= $cerita['lng'] ?: 116.3510 ?>;
  var map = L.map('map').setView([lat, lng], 9);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19
  }).addTo(map);

  var marker = L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Cerita").openPopup();

  map.on('click', function(e) {
      var lat = e.latlng.lat.toFixed(6);
      var lng = e.latlng.lng.toFixed(6);
      document.getElementById("lat").value = lat;
      document.getElementById("lng").value = lng;

      if (marker) map.removeLayer(marker);
      marker = L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Baru").openPopup();
  });
</script>
</body>
</html>
