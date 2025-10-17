<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sumber Pembelajaran - Portal Cerita Rakyat Lombok</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #f9f9f9;
      color: #333;
      line-height: 1.6;
    }

    header {
      background: linear-gradient(135deg, #00897B, #00695C);
      color: white;
      padding: 40px 20px;
      text-align: center;
    }

    header h1 {
      margin: 0;
      font-size: 2rem;
    }

    header p {
      margin-top: 8px;
      font-size: 1.1rem;
      opacity: 0.9;
    }

    .container {
      max-width: 1000px;
      margin: 30px auto;
      padding: 0 20px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
    }

    /* ====== ANIMASI LEMBUT ====== */
    .card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      padding: 20px;
      transition: transform 0.6s ease, box-shadow 0.6s ease, opacity 1s ease-out;
      text-align: center;
      opacity: 0;
      transform: translateY(30px);
    }

    .card.show {
      opacity: 1;
      transform: translateY(0);
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 6px 14px rgba(0,0,0,0.15);
    }

    .card h3 {
      margin-top: 0;
      color: #00695C;
      font-size: 1.2rem;
    }

    .card p {
      font-size: 0.95rem;
      color: #555;
    }

    .btn {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 14px;
      background: #00897B;
      color: white;
      border-radius: 6px;
      text-decoration: none;
      font-size: 0.9rem;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #00695C;
    }

    .back-home {
      display: block;
      text-align: center;
      margin-top: 40px;
    }

    .back-home a {
      display: inline-block;
      background: #444;
      color: #fff;
      padding: 10px 16px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 0.9rem;
      transition: background 0.3s ease;
    }

    .back-home a:hover {
      background: #222;
    }

    footer {
      margin-top: 40px;
      background: #222;
      color: #bbb;
      text-align: center;
      padding: 15px;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>

  <header>
    <h1>Sumber Pembelajaran</h1>
    <p>Materi edukasi untuk melestarikan budaya dan cerita rakyat Lombok</p>
  </header>

  <main class="container">
    <div class="grid">
      <!-- Materi 1 -->
      <div class="card">
        <h3>Sejarah & Budaya Sasak</h3>
        <p>Pelajari asal-usul suku Sasak, tradisi, pakaian adat, dan nilai budaya yang diwariskan dari generasi ke generasi.</p>
        <a href="materi_sejarah.php" class="btn">Baca Materi</a>
      </div>

      <!-- Materi 2 -->
      <div class="card">
        <h3>Nilai Moral dari Cerita Rakyat</h3>
        <p>Setiap cerita rakyat Lombok mengandung pesan moral, misalnya kejujuran, gotong royong, dan rasa hormat.</p>
        <a href="materi_moral.php" class="btn">Pelajari Nilai</a>
      </div>

      <!-- Materi 3 -->
      <div class="card">
        <h3>Bahasa Daerah Sasak</h3>
        <p>Kenali kosakata dan ungkapan sehari-hari dalam bahasa Sasak sebagai bagian penting dari identitas budaya.</p>
        <a href="materi_bahasa.php" class="btn">Belajar Bahasa</a>
      </div>
    </div>

    <div class="back-home">
      <a href="index.php">← Kembali ke Beranda</a>
    </div>
  </main>

  <footer>
    &copy; <?= date("Y") ?> Portal Cerita Rakyat Lombok | Edukasi & Pelestarian Budaya
  </footer>

  <script>
    // Fade-in lembut & muncul perlahan satu per satu
    document.addEventListener("DOMContentLoaded", () => {
      const cards = document.querySelectorAll(".card");
      cards.forEach((card, index) => {
        setTimeout(() => {
          card.classList.add("show");
        }, index * 300); // jeda 300ms antar kartu agar lebih lembut
      });
    });
  </script>

</body>
</html>
