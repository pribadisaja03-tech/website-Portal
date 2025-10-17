<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bahasa Daerah Sasak - Portal Cerita Rakyat Lombok</title>
  <style>
    body {font-family:'Segoe UI',sans-serif;margin:0;padding:0;background:#f4f9f9;color:#333;line-height:1.8;}
    header {background:linear-gradient(135deg,#00897B,#00695C);color:white;padding:40px 20px;text-align:center;}
    header h1 {margin:0;font-size:2rem;}
    header p {margin-top:8px;font-size:1.1rem;opacity:0.9;}
    .container {max-width:950px;margin:30px auto;background:white;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);padding:30px;}
    h2 {color:#00695C;margin-top:24px;font-size:1.5rem;}
    p {margin-bottom:16px;text-align:justify;}
    ul {margin:10px 0 20px 20px;}
    .back-btn {display:inline-block;margin-top:20px;padding:10px 18px;background:#444;color:white;border-radius:6px;text-decoration:none;font-size:0.95rem;transition:background 0.2s;}
    .back-btn:hover {background:#222;}
    footer {margin-top:40px;background:#222;color:#bbb;text-align:center;padding:15px;font-size:0.9rem;}
  </style>
</head>
<body>

  <header>
    <h1>Bahasa Daerah Sasak</h1>
    <p>Bahasa sebagai identitas dan jati diri masyarakat Lombok</p>
  </header>

  <main class="container">
    <p>
      Bahasa Sasak adalah bahasa daerah utama di Pulau Lombok yang termasuk dalam rumpun Austronesia. 
      Bahasa ini menjadi identitas budaya masyarakat Sasak sekaligus sarana pelestarian cerita rakyat. 
      Setiap kata dan ungkapan dalam bahasa Sasak menyimpan nilai filosofi dan kearifan lokal.
    </p>

    <h2>Dialek Bahasa Sasak</h2>
    <p>
      Bahasa Sasak memiliki beberapa dialek, yang perbedaannya cukup signifikan, antara lain:
    </p>
    <ul>
      <li><b>Ngeno-Ngene</b> → digunakan di bagian tengah Lombok.</li>
      <li><b>Meno-Mene</b> → dipakai di wilayah timur.</li>
      <li><b>Kuto-Kute</b> → dituturkan di wilayah barat.</li>
      <li><b>Menó-Mené</b> dan <b>Ngenó-Ngené</b> → variasi lokal dengan kosakata khas.</li>
    </ul>

    <h2>Struktur Bahasa</h2>
    <p>
      Bahasa Sasak memiliki tingkatan bahasa (alus, tengaq, jamak) 
      yang menunjukkan penghormatan kepada lawan bicara. 
      Misalnya, berbicara dengan orang tua atau tokoh adat menggunakan bahasa alus, 
      sementara dengan teman sebaya menggunakan bahasa jamak. 
      Sistem ini mirip dengan bahasa Jawa dan Bali.
    </p>

    <h2>Ungkapan dan Filosofi</h2>
    <p>
      Banyak ungkapan Sasak yang mengandung nilai moral. Contoh:
    </p>
    <ul>
      <li><em>“Maliq taqoq, malu tiang”</em> → manusia yang baik adalah yang tahu malu.</li>
      <li><em>“Nggih-nggih wah ngenggih”</em> → kesopanan dalam berbicara adalah tanda kehormatan.</li>
    </ul>

    <h2>Bahasa dalam Cerita Rakyat</h2>
    <p>
      Cerita rakyat Sasak banyak dituturkan dalam bahasa daerah. 
      Bahasa ini membuat pesan moral lebih kuat karena dekat dengan kehidupan masyarakat. 
      Sayangnya, generasi muda kini cenderung jarang menggunakan bahasa Sasak, 
      lebih memilih bahasa Indonesia atau bahasa asing.
    </p>

    <h2>Upaya Pelestarian</h2>
    <p>
      Untuk menjaga eksistensinya, perlu ada:
    </p>
    <ul>
      <li>Pembelajaran bahasa Sasak di sekolah.</li>
      <li>Penerjemahan cerita rakyat Sasak dalam dua bahasa (Indonesia dan Sasak).</li>
      <li>Pementasan drama rakyat menggunakan bahasa Sasak.</li>
      <li>Pemanfaatan media digital untuk konten berbahasa daerah.</li>
    </ul>

    <h2>Kesimpulan</h2>
    <p>
      Bahasa Sasak bukan hanya alat komunikasi, tetapi juga identitas budaya. 
      Melestarikannya berarti menjaga jati diri Lombok. 
      Jika generasi muda mau menggunakan bahasa Sasak dalam kehidupan sehari-hari, 
      maka warisan budaya ini akan tetap hidup di tengah arus globalisasi.
    </p>

    <a href="edukasi.php" class="back-btn">⬅ Kembali ke Sumber Pembelajaran</a>
  </main>

  <footer>
    &copy; <?= date("Y") ?> Portal Cerita Rakyat Lombok | Edukasi & Pelestarian Budaya
  </footer>
</body>
</html>
