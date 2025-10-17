<?php
include '../config.php';
session_start();

// cek login user
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// proses form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul']);
    $isi      = trim($_POST['isi']);
    $daerah   = trim($_POST['daerah']);
    $kategori = trim($_POST['kategori']);
    $lat      = !empty($_POST['lat']) ? floatval($_POST['lat']) : null;
    $lng      = !empty($_POST['lng']) ? floatval($_POST['lng']) : null;
    $user_id  = $_SESSION['user_id'];

    // generate no_urut
    $result = $conn->query("SELECT MAX(no_urut) AS last FROM cerita");
    $row = $result->fetch_assoc();
    $no_urut = ($row['last'] ?? 0) + 1;

    // upload gambar
    $gambar = null;
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . "_" . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/" . $gambar);
    }

    // upload audio
    $audio = null;
    if (!empty($_FILES['audio']['name'])) {
        $audio = time() . "_" . basename($_FILES['audio']['name']);
        move_uploaded_file($_FILES['audio']['tmp_name'], "../uploads/" . $audio);
    }

    // insert ke database
    $sql = "INSERT INTO cerita 
            (no_urut, judul, isi, daerah, kategori, gambar, audio, lat, lng, user_id, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssssddi", 
        $no_urut, $judul, $isi, $daerah, $kategori, $gambar, $audio, $lat, $lng, $user_id
    );

    if ($stmt->execute()) {
        $_SESSION['msg'] = "✅ Cerita berhasil dikirim, menunggu persetujuan admin.";
        header("Location: ../user/profil.php");
        exit;
    } else {
        echo "<p style='color:red'>❌ Gagal kirim cerita. Error: " . $stmt->error . "</p>";
    }
}

include '../navbar.php';
?>

<div class="edit-section">
  <div class="edit-card">
    <h2>📖 Tambah Cerita Baru</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Judul Cerita</label>
        <input type="text" name="judul" required>

        <label>Isi Cerita</label>
        <textarea name="isi" rows="6" required></textarea>

        <label>Daerah</label>
        <select name="daerah" required>
            <option value="Lombok Timur">Lombok Timur</option>
            <option value="Lombok Tengah">Lombok Tengah</option>
            <option value="Lombok Barat">Lombok Barat</option>
            <option value="Lombok Utara">Lombok Utara</option>
            <option value="Lombok">Lombok</option>
        </select>

        <label>Kategori</label>
        <select name="kategori" required>
            <option value="Legenda">Legenda</option>
            <option value="Mitos">Mitos</option>
            <option value="Sejarah">Sejarah</option>
            <option value="Budaya">Budaya</option>
            <option value="Cerita Rakyat">Cerita Rakyat</option>
        </select>

        <label>Pilih Lokasi Cerita di Peta</label>
<div id="map" style="height:250px; width:100%; margin-bottom:10px; border:1px solid #ccc; border-radius:8px;"></div>


        <label>Latitude</label>
        <input type="text" id="lat" name="lat" readonly required>

        <label>Longitude</label>
        <input type="text" id="lng" name="lng" readonly required>

        <label>Upload Gambar</label>
        <input type="file" name="gambar" accept="image/png,image/jpeg">

        <label>Upload Audio (opsional)</label>
        <input type="file" name="audio" accept="audio/mpeg">

        <button type="submit">📤 Kirim Cerita</button>
    </form>
  </div>
</div>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
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
