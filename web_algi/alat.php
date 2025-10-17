<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Alat Tradisional Sasak - Portal Cerita Rakyat Lombok</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #f9f9f9;
      color: #333;
    }

    header {
      background: linear-gradient(135deg, #FF6F00, #E65100);
      color: white;
      padding: 40px 20px;
      text-align: center;
    }

    header h1 {margin: 0; font-size: 2rem;}
    header p {margin-top: 8px; font-size: 1.1rem; opacity: 0.9;}

    .container {max-width: 1100px; margin: 30px auto; padding: 20px;}

    h2.section-title {
      margin: 40px 0 20px;
      font-size: 1.6rem;
      color: #E65100;
      border-bottom: 2px solid #E65100;
      display: inline-block;
      padding-bottom: 5px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .card {
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      transition: transform 0.2s;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeInUp 1.2s ease forwards;
    }

    .card:hover {transform: scale(1.02);}
    .card img {width: 100%; height: 200px; object-fit: cover;}
    .card .info {padding: 15px;}
    .card h3 {margin: 0 0 8px; font-size: 1.2rem; color: #E65100;}
    .card p {font-size: 0.95rem; line-height: 1.6; text-align: justify;}

    .back-btn {
      display: inline-block;
      margin-top: 40px;
      padding: 10px 18px;
      background: #444;
      color: white;
      border-radius: 6px;
      text-decoration: none;
      font-size: 0.95rem;
    }

    .back-btn:hover {background: #222;}

    footer {
      margin-top: 40px;
      background: #222;
      color: #bbb;
      text-align: center;
      padding: 15px;
      font-size: 0.9rem;
    }

    /* Animasi lembut fade-in */
    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(30px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>

  <header>
    <h1>Alat Tradisional Sasak</h1>
    <p>Koleksi alat musik, perlengkapan seni, dan budaya masyarakat Lombok</p>
  </header>

  <main class="container">

    <!-- Alat Musik -->
    <h2 class="section-title">🎶 Alat Musik Tradisional</h2>
    <div class="grid">
      <div class="card">
        <img src="uploads/b5.jpg" alt="Gendang Beleq">
        <div class="info">
          <h3>Gendang Beleq</h3>
          <p>
            Gendang Beleq merupakan alat musik tradisional khas suku Sasak berbentuk tabuh besar yang dimainkan secara berkelompok. 
            Dahulu digunakan untuk mengiringi prajurit menuju medan perang sebagai simbol keberanian dan penyemangat. 
            Kini Gendang Beleq tampil dalam upacara adat, penyambutan tamu, hingga festival budaya. 
            Menurut <i>Balai Pelestarian Nilai Budaya Bali (2019)</i>, Gendang Beleq juga mencerminkan nilai gotong royong dan solidaritas masyarakat Lombok.
          </p>
        </div>
      </div>

      <div class="card">
        <img src="uploads/b2.jpg" alt="Ceng ceng">
        <div class="info">
          <h3>Ceng-ceng</h3>
          <p>
            Ceng-ceng adalah alat musik perkusi yang terdiri dari dua lempengan logam kecil berbentuk bundar yang dibenturkan untuk menghasilkan bunyi nyaring.
            Biasanya digunakan dalam ansambel gamelan Sasak dan kesenian religi seperti zikir zaman. 
            Menurut penelitian <i>Universitas Mataram (2021)</i>, ceng-ceng berfungsi menjaga ritme serta melambangkan kebersamaan dalam harmoni.
          </p>
        </div>
      </div>

      <div class="card">
        <img src="uploads/b6.jpg" alt="Seruling">
        <div class="info">
          <h3>Seruling</h3>
          <p>
            Seruling atau suling Sasak terbuat dari bambu tipis dengan enam lubang nada. 
            Instrumen ini menghasilkan suara lembut yang sering dimainkan untuk mengiringi tari-tarian dan lagu daerah seperti “Gandrung”.
            Selain sebagai hiburan, seruling juga dipakai dalam ritual adat sebagai simbol ketenangan dan doa kepada alam, sebagaimana dicatat oleh <i>Yayasan Seni Sasak (2018)</i>.
          </p>
        </div>
      </div>

      <div class="card">
        <img src="uploads/b1.jpg" alt="Gong">
        <div class="info">
          <h3>Gong</h3>
          <p>
            Gong adalah alat musik logam yang menjadi bagian utama dari gamelan Sasak. 
            Bunyi gong menandai perubahan tempo atau penutup satu siklus lagu tradisional.
            Dalam budaya Sasak, gong dianggap suci karena suaranya dipercaya mampu memanggil semangat leluhur dan menjaga keseimbangan alam.
            Sumber: <i>Direktorat Kesenian Indonesia, 2020</i>.
          </p>
        </div>
      </div>
    </div>

    <!-- Alat Peresean -->
    <h2 class="section-title">🥋 Alat Peresean (Tradisi Bela Diri)</h2>
    <div class="grid">
      <div class="card">
        <img src="uploads/b11.jpg" alt="Penjalin">
        <div class="info">
          <h3>Penjalin</h3>
          <p>
            Penjalin adalah rotan panjang yang digunakan sebagai senjata dalam tradisi Peresean, yaitu pertarungan antara dua pepadu (petarung) Sasak. 
            Pertarungan ini bukan sekadar adu fisik, tetapi juga ajang menguji keberanian dan kehormatan laki-laki Sasak.
            Menurut <i>BPSNT Denpasar (2017)</i>, tradisi Peresean melambangkan semangat ksatria dan pengendalian emosi melalui aturan adat yang ketat.
          </p>
        </div>
      </div>

      <div class="card">
        <img src="uploads/b12.jpg" alt="Ende">
        <div class="info">
          <h3>Ende</h3>
          <p>
            Ende adalah perisai yang terbuat dari kulit kerbau tebal, digunakan untuk menangkis pukulan penjalin. 
            Selain sebagai alat pelindung, ende memiliki nilai simbolik sebagai lambang pertahanan diri dan kehormatan.
            Bentuknya bundar dengan pegangan di bagian belakang, dihias sederhana sesuai status sosial pemiliknya.
            Sumber: <i>Disbudpar NTB, 2019</i>.
          </p>
        </div>
      </div>
    </div>

    <!-- Tombol kembali -->
    <a href="galeri.php" class="back-btn">⬅ Kembali ke Galeri</a>
  </main>

  <footer>
    &copy; <?= date("Y") ?> Portal Cerita Rakyat Lombok | Alat Tradisional
  </footer>

  <script>
    // Animasi fade-in muncul lembut satu per satu
    document.addEventListener("DOMContentLoaded", () => {
      const cards = document.querySelectorAll('.card');
      cards.forEach((card, i) => {
        card.style.animationDelay = `${i * 0.35}s`; // efek lebih lembut
      });
    });
  </script>
</body>
</html>
