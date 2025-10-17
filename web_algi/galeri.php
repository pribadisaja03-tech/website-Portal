<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Galeri Budaya Sasak</title>
  <style>
    /* ========== GLOBAL STYLE ========== */
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f9f9;
      color: #333;
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
      max-width: 1100px;
      margin: 30px auto;
      padding: 20px;
    }

    /* ========== TABS ========== */
    .tabs {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      justify-content: center;
      margin-bottom: 25px;
    }
    .tabs button {
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      background: #ccc;
      cursor: pointer;
      font-size: 0.95rem;
      transition: all 0.2s;
    }
    .tabs button.active,
    .tabs button:hover {
      background: #00695C;
      color: white;
    }

    /* ========== GRID GALERI ========== */
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
      transition: transform 0.3s ease, opacity 1.2s ease-out;
      opacity: 0;
      transform: translateY(30px);
      position: relative;
    }

    .card.show {
      opacity: 1;
      transform: translateY(0);
    }

    .card:hover {
      transform: scale(1.02);
    }

    .card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .card .info {
      padding: 15px;
    }

    .card h3 {
      margin: 0 0 8px;
      font-size: 1.2rem;
      color: #00695C;
    }

    .card p {
      font-size: 0.95rem;
      line-height: 1.5;
      text-align: justify;
      margin-bottom: 12px;
    }

    .card .more-btn {
      display: inline-block;
      padding: 8px 12px;
      background: #00695C;
      color: #fff;
      border-radius: 8px;
      text-decoration: none;
      cursor: pointer;
      font-size: 0.9rem;
      transition: background 0.2s;
    }
    .card .more-btn:hover { background: #005046; }

    /* ========== MODAL ========== */
    .modal-backdrop {
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      padding: 20px;
    }
    .modal-backdrop.show { display: flex; }

    .modal {
      width: 100%;
      max-width: 820px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      padding: 22px;
      max-height: 90vh;
      overflow: auto;
      transform: translateY(20px);
      opacity: 0;
      transition: transform 0.35s ease, opacity 0.35s ease;
    }
    .modal.show {
      transform: translateY(0);
      opacity: 1;
    }
    .modal h2 { margin-top: 0; color: #00695C; }
    .modal .source { font-size: 13px; color: #666; margin-top: 12px; }
    .modal .close-btn {
      display: inline-block;
      margin-top: 14px;
      padding: 8px 12px;
      background: #ccc;
      color: #222;
      border-radius: 8px;
      cursor: pointer;
      text-decoration: none;
    }
    .modal .close-btn:hover { background: #bbb; }

    /* ========== BUTTON NAVIGASI ========== */
    .btn-link {
      display: inline-block;
      margin: 20px 10px 0 0;
      padding: 10px 18px;
      background: #00695C;
      color: white;
      border-radius: 6px;
      text-decoration: none;
      font-size: 0.95rem;
      transition: background 0.2s;
    }
    .btn-link:hover {
      background: #004D40;
    }

    /* ========== FOOTER ========== */
    footer {
      margin-top: 40px;
      background: #222;
      color: #bbb;
      text-align: center;
      padding: 15px;
      font-size: 0.9rem;
    }

    /* Responsif modal isi teks */
    @media (max-width: 600px) {
      .modal { padding: 16px; }
    }
  </style>
</head>
<body>

  <header>
    <h1>Galeri Budaya Sasak</h1>
    <p>Kumpulan foto tradisi, seni, rumah adat, dan alat budaya masyarakat Lombok</p>
  </header>

  <main class="container">

    <!-- Grid Galeri -->
    <div class="grid">
      <!-- Tradisi: Bau Nyale -->
      <div class="card tradisi" data-id="bau-nyale">
        <img src="uploads/b8.jpg" alt="Bau Nyale">
        <div class="info">
          <h3>Bau Nyale</h3>
          <p>Perayaan tahunan mencari nyale (cacing laut) yang terkait dengan legenda Putri Mandalika; ritual ini mengandung makna syukur, pelestarian sumber daya laut, dan identitas komunitas pesisir Lombok.</p>
          <a class="more-btn" data-modal="modal-bau-nyale">Baca Selengkapnya</a>
        </div>
      </div>

      <!-- Tradisi: Peresean -->
      <div class="card tradisi" data-id="peresean">
        <img src="uploads/b9.jpg" alt="Peresean">
        <div class="info">
          <h3>Peresean</h3>
          <p>Seni bela diri tradisional Sasak yang mempertontonkan pertarungan beralasan ritual dan sosial menggunakan penjalin (rotan) dan perisai ende; berfungsi sebagai uji keberanian dan upacara adat komunitas.</p>
          <a class="more-btn" data-modal="modal-peresean">Baca Selengkapnya</a>
        </div>
      </div>

      <!-- Seni & Musik: Gendang Beleq -->
      <div class="card seni" data-id="gendang-beleq">
        <img src="uploads/b10.jpg" alt="Gendang Beleq">
        <div class="info">
          <h3>Gendang Beleq</h3>
          <p>Ansambel musik tradisional Sasak yang menampilkan gendang berukuran besar (beleq) dan sejumlah pemain; biasa dipentaskan di upacara adat, pernikahan, dan penyambutan tamu.</p>
          <a class="more-btn" data-modal="modal-gendang-beleq">Baca Selengkapnya</a>
        </div>
      </div>

      <!-- Seni & Tenun: Tenun Songket -->
      <div class="card seni" data-id="tenun-songket">
        <img src="uploads/b7.jpg" alt="Tenun Songket">
        <div class="info">
          <h3>Tenun Songket</h3>
          <p>Kain tenun tradisional yang dihias dengan benang metalik atau motif khas Sasak; merupakan ekspresi identitas lokal, simbol status sosial, dan warisan keterampilan perempuan penenun.</p>
          <a class="more-btn" data-modal="modal-songket">Baca Selengkapnya</a>
        </div>
      </div>
    </div>

    <!-- Tombol navigasi -->
    <a href="alat.php" class="btn-link">🔗 Lihat Alat Tradisional</a>
    <a href="index.php" class="btn-link">⬅ Kembali ke Beranda</a>
  </main>

  <footer>
    &copy; <?= date("Y") ?> Portal Cerita Rakyat Lombok | Galeri Budaya
  </footer>

  <!-- ====== MODALS ====== -->
  <!-- Bau Nyale -->
  <div id="modal-bau-nyale" class="modal-backdrop" aria-hidden="true">
    <div class="modal" role="dialog" aria-labelledby="bauTitle">
      <h2 id="bauTitle">Bau Nyale — Asal, Makna, dan Konteks Sosial</h2>
      <p>
        Tradisi Bau Nyale dipraktikkan di sejumlah pantai Lombok, khususnya di wilayah Kuta dan sekitarnya, menjelang musim tertentu (biasanya Februari–Maret). Secara etimologi, “bau” berarti menangkap dan “nyale” merujuk pada cacing laut (jenis Eunice viridis atau sejenisnya). Menurut literatur etnografi dan inventarisasi kebudayaan, Bau Nyale terkait erat dengan legenda Putri Mandalika—seorang tokoh yang melompat ke laut untuk mencegah konflik dan yang dipercaya menenggelamkan dirinya sehingga muncul nyale setiap tahun; kegiatan menangkap nyale kemudian menjadi ritual syukur, permohonan berkah, dan pengukuhan identitas komunitas pesisir. 
      </p>
      <p>
        Di sisi ekologis-sosial, perayaan ini juga berfungsi sebagai momen pengelolaan sumber daya laut secara tradisional—komunitas berkumpul, berbagi pengetahuan tentang musim tangkapan, serta meneguhkan norma-norma pelestarian pesisir. Belakangan, Bau Nyale juga menjadi atraksi wisata budaya sehingga menghadirkan dinamika antara aspek sakral dan komersialisasi; penelitian mengingatkan pentingnya menjaga keseimbangan antara pelestarian nilai ritual dan manfaat ekonomi pariwisata.
      </p>
      <p class="source"><strong>Sumber:</strong> Inventarisasi & kajian Bau Nyale (Kemendikbud / repositori & studi lokal). :contentReference[oaicite:4]{index=4}</p>
      <a class="close-btn" data-close>Close</a>
    </div>
  </div>

  <!-- Peresean -->
  <div id="modal-peresean" class="modal-backdrop" aria-hidden="true">
    <div class="modal" role="dialog" aria-labelledby="pereseanTitle">
      <h2 id="pereseanTitle">Peresean — Ritual, Teknik, dan Nilai Sosial</h2>
      <p>
        Peresean adalah tradisi pertarungan adat khas masyarakat Sasak di Lombok, berupa adu ketangkasan dua orang (pepadu) yang memukulkan penjalin (rotan) dan berlindung di balik perisai kulit yang disebut ende. Sumber-sumber etnografi dan kajian kebudayaan menyebut Peresean berakar sebagai latihan fisik dan uji keberanian para pemuda—pada masa lalu berkaitan dengan persiapan perang atau penegasan status kedewasaan. 
      </p>
      <p>
        Pertunjukan Peresean disertai pengiring musik tradisional dan diatur aturan adat serta wasit (pekembar). Selain fungsi pertunjukan, Peresean juga memuat nilai-nilai sosial: keberanian, kehormatan, pengendalian diri, serta peneguhan ikatan komunal. Dalam praktik kontemporer, Peresean sering dipertunjukkan di acara adat dan festival, sekaligus menjadi daya tarik budaya yang perlu dikelola agar nilai asli ritual tetap terjaga.
      </p>
      <p class="source"><strong>Sumber:</strong> Artikel budaya & studi akademik tentang Peresean (kajian lokal & jurnal). :contentReference[oaicite:5]{index=5}</p>
      <a class="close-btn" data-close>Close</a>
    </div>
  </div>

  <!-- Gendang Beleq -->
  <div id="modal-gendang-beleq" class="modal-backdrop" aria-hidden="true">
    <div class="modal" role="dialog" aria-labelledby="gendangTitle">
      <h2 id="gendangTitle">Gendang Beleq — Musik, Kostum, dan Fungsi Upacara</h2>
      <p>
        Gendang Beleq (secara harfiah: gendang besar) adalah ensambel musik tradisional yang menjadi ciri khas suku Sasak di Pulau Lombok. Biasanya melibatkan sejumlah pemain gendang besar, pemukul, dan instrumen pelengkap lain seperti gong; kostum penabuh sering mencerminkan simbol-simbol lokal. Karya-karya inventarisasi kebudayaan dan studi seni menempatkan gendang beleq sebagai bagian sentral upacara adat, pernikahan, dan acara komunitas—fungsinya tidak hanya musikal tetapi juga sebagai penanda status sosial dan media penyampaian tradisi.
      </p>
      <p>
        Praktik dan teknik permainan gendang beleq, termasuk pola ritme dan koordinasi pemain, telah dikaji untuk memahami aspek estetika dan pelibatan komunitas. Upaya pelestarian mencakup dokumentasi, pengajaran di sanggar seni, dan pembinaan generasi muda agar tradisi tetap hidup di tengah modernisasi.
      </p>
      <p class="source"><strong>Sumber:</strong> Dokumentasi Kemendikbud & penelitian seni mengenai Gendang Beleq. :contentReference[oaicite:6]{index=6}</p>
      <a class="close-btn" data-close>Close</a>
    </div>
  </div>

  <!-- Tenun Songket -->
  <div id="modal-songket" class="modal-backdrop" aria-hidden="true">
    <div class="modal" role="dialog" aria-labelledby="songketTitle">
      <h2 id="songketTitle">Tenun Songket Sasak — Motif, Teknik, dan Makna</h2>
      <p>
        Tenun songket di Lombok (kain tenun tradisional yang sering dihias benang metalik atau motif khas) merupakan ekspresi keterampilan tenun kaum perempuan Sasak dan menyimpan ragam motif yang bermakna simbolis — baik berkaitan dengan doa, kesuburan, maupun identitas kelompok. Studi motif songket Lombok menunjukkan pola-pola yang khas dan filosofi budaya yang diwariskan turun-temurun.
      </p>
      <p>
        Songket tidak hanya berfungsi sebagai pakaian adat atau penanda status sosial, tetapi juga sebagai warisan ekonomi-produktif bagi pengerajin lokal. Pelestarian motif dan teknik memerlukan dokumentasi motif, pelatihan generasi penerus, serta keseimbangan antara produksi komersial dan konservasi nilai budaya.
      </p>
      <p class="source"><strong>Sumber:</strong> Studi motif & artikel tentang songket Sasak dan literatur umum tentang songket. :contentReference[oaicite:7]{index=7}</p>
      <a class="close-btn" data-close>Close</a>
    </div>
  </div>

  <script>
    // Filter kategori (tetap seperti sebelumnya)
    function filterSelection(category, event) {
      let cards = document.getElementsByClassName("card");
      for (let i = 0; i < cards.length; i++) {
        let item = cards[i];
        if (category === "all") {
          item.style.display = "block";
        } else {
          if (item.classList.contains(category)) {
            item.style.display = "block";
          } else {
            item.style.display = "none";
          }
        }
      }
      let btns = document.querySelectorAll(".tabs button");
      btns.forEach(btn => btn.classList.remove("active"));
      if (event && event.target) event.target.classList.add("active");
    }

    // Animasi fade-in lembut & bertahap
    document.addEventListener("DOMContentLoaded", () => {
      const cards = document.querySelectorAll(".card");
      cards.forEach((card, index) => {
        setTimeout(() => {
          card.classList.add("show");
        }, index * 350); // jeda 350ms antar kartu → lebih lambat & lembut
      });

      // Modal handling (open/close)
      const openButtons = document.querySelectorAll('.more-btn');
      const backdrops = document.querySelectorAll('.modal-backdrop');

      openButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = btn.getAttribute('data-modal');
          const modal = document.getElementById(id);
          if (!modal) return;
          modal.classList.add('show');
          modal.setAttribute('aria-hidden', 'false');
          // animate inner modal
          setTimeout(() => {
            modal.querySelector('.modal').classList.add('show');
          }, 10);
        });
      });

      // close buttons
      document.querySelectorAll('[data-close]').forEach(c => {
        c.addEventListener('click', (e) => {
          const backdrop = c.closest('.modal-backdrop');
          closeModal(backdrop);
        });
      });

      // close on backdrop click
      backdrops.forEach(b => {
        b.addEventListener('click', (ev) => {
          if (ev.target === b) closeModal(b);
        });
      });

      // close on ESC
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          backdrops.forEach(b => closeModal(b));
        }
      });

      function closeModal(backdrop) {
        if (!backdrop) return;
        const inner = backdrop.querySelector('.modal');
        if (inner) inner.classList.remove('show');
        backdrop.classList.remove('show');
        backdrop.setAttribute('aria-hidden', 'true');
      }
    });
  </script>
</body>
</html>
