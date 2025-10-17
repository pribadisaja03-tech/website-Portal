<?php
include_once 'config.php';

// ============================================================
//                 FUNGSI CERITA
// ============================================================

// Ambil semua cerita (hanya approved untuk user biasa) + avg rating
function getCerita($limit = null) {
    global $conn;
    $sql = "SELECT c.*, 
                   ROUND(AVG(k.rating),1) AS avg_rating
            FROM cerita c
            LEFT JOIN komentar k ON c.id = k.cerita_id
            WHERE c.status='approved'
            GROUP BY c.id
            ORDER BY c.created_at DESC";
    if ($limit && is_int($limit)) {
        $sql .= " LIMIT $limit";
    }
    return $conn->query($sql);
}

// Ambil cerita by id (user hanya bisa lihat approved, admin bisa lihat semua)
function getCeritaById($id) {
    global $conn;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $sql = "SELECT c.*, ROUND(AVG(k.rating),1) AS avg_rating
            FROM cerita c
            LEFT JOIN komentar k ON c.id = k.cerita_id
            WHERE c.id=?";
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $sql .= " AND c.status='approved'";
    }
    $sql .= " GROUP BY c.id";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();
    return $row;
}

// Ambil cerita dengan filter (judul, daerah, kategori)
function getCeritaFiltered($keyword = '', $daerah = '', $kategori = '') {
    global $conn;
    $sql = "SELECT c.*, 
                   ROUND(AVG(k.rating),1) AS avg_rating
            FROM cerita c
            LEFT JOIN komentar k ON c.id = k.cerita_id
            WHERE c.status='approved'";
    $params = [];
    $types = "";

    if (!empty($keyword)) {
        $sql .= " AND c.judul LIKE ?";
        $params[] = "%" . $keyword . "%";
        $types .= "s";
    }
    if (!empty($daerah)) {
        $sql .= " AND c.daerah LIKE ?";
        $params[] = "%" . $daerah . "%";
        $types .= "s";
    }
    if (!empty($kategori)) {
        $sql .= " AND c.kategori LIKE ?";
        $params[] = "%" . $kategori . "%";
        $types .= "s";
    }

    $sql .= " GROUP BY c.id ORDER BY c.created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result();
}

// Simpan history baca + tambah jumlah_baca
function simpanHistory($cerita_id) {
    global $conn;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Tambah jumlah baca
    $stmt2 = $conn->prepare("UPDATE cerita SET jumlah_baca = jumlah_baca + 1 WHERE id=?");
    $stmt2->bind_param("i", $cerita_id);
    $stmt2->execute();
    $stmt2->close();

    // Simpan ke history user
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("
            INSERT INTO history (user_id, cerita_id, waktu_baca)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE waktu_baca = NOW()
        ");
        $stmt->bind_param("ii", $user_id, $cerita_id);
        $stmt->execute();
        $stmt->close();
    }
    return true;
}

// Ambil history baca
function getHistory() {
    global $conn;
    $sql = "SELECT h.id, h.cerita_id, h.waktu_baca, c.judul
            FROM history h
            JOIN cerita c ON h.cerita_id = c.id
            ORDER BY h.waktu_baca DESC";
    return $conn->query($sql);
}

// Tambah cerita (admin langsung approved, user pending)
function tambahCerita($judul, $daerah, $kategori, $isi, $gambar = null, $audio = null) {
    global $conn;
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $result = $conn->query("SELECT MAX(no_urut) AS last FROM cerita");
    $row = $result->fetch_assoc();
    $no_urut = ($row['last'] ?? 0) + 1;

    $status = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'approved' : 'pending';
    $user_id = $_SESSION['user_id'] ?? null;

    $stmt = $conn->prepare("INSERT INTO cerita 
        (no_urut, judul, daerah, kategori, isi, gambar, audio, status, user_id, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("issssssss", $no_urut, $judul, $daerah, $kategori, $isi, $gambar, $audio, $status, $user_id);

    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

// Hapus cerita (admin)
function hapusCerita($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM cerita WHERE id = ?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

// ============================================================
//                 FUNGSI KOMENTAR & RATING
// ============================================================

// Ambil semua komentar berdasarkan ID cerita (hanya approved)
function getKomentarByCerita($cerita_id) {
    global $conn;

    $query = "SELECT k.id, k.user_id, k.cerita_id, k.created_at, k.isi, k.rating, k.status, u.username
              FROM komentar k
              JOIN `user` u ON k.user_id = u.id
              WHERE k.cerita_id = ? AND k.status='approved'
              ORDER BY k.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cerita_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $komentar = [];
    while ($row = $result->fetch_assoc()) {
        $komentar[] = $row;
    }
    return $komentar;
}

// Tambahkan komentar baru (status pending dulu)
function tambahKomentar($cerita_id, $user_id, $isi, $rating = 0) {
    global $conn;

    $stmt = $conn->prepare("
        INSERT INTO komentar (cerita_id, user_id, isi, rating, status, created_at)
        VALUES (?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->bind_param("iisi", $cerita_id, $user_id, $isi, $rating);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}

// Approve komentar (admin)
function approveKomentar($id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE komentar SET status='approved' WHERE id=?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

// Hapus komentar
function hapusKomentar($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM komentar WHERE id = ?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}

// Ambil semua komentar untuk admin
function getAllKomentar() {
    global $conn;
    $sql = "SELECT k.id, k.isi, k.rating, k.status, k.created_at, u.username, c.judul
            FROM komentar k
            JOIN `user` u ON k.user_id = u.id
            JOIN cerita c ON k.cerita_id = c.id
            ORDER BY k.created_at DESC";
    return $conn->query($sql);
}
?>
