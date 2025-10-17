<?php
session_start();
include "config.php";
include "functions.php";
include "filter.php";

// ========== BAGIAN USER TAMBAH KOMENTAR ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // login ada di root
        exit;
    }

    $cerita_id = intval($_POST['cerita_id']);
    $user_id   = $_SESSION['user_id'];
    $isi       = trim($_POST['isi']);
    $rating    = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

    if (!empty($isi)) {
        // Filter kata kasar
        $isi_bersih = filterKataKasar($isi);

        // Simpan komentar dengan status pending (pakai function)
        tambahKomentar($cerita_id, $user_id, $isi_bersih, $rating);
    }

    // FIX: semua file di root, jadi langsung ke baca.php
  header("Location: baca.php?id=" . $cerita_id . "&success=1");

    exit;
}

// ========== BAGIAN ADMIN MODERASI KOMENTAR ==========
if (isset($_GET['action']) && isset($_GET['id'])) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header("Location: login.php");
        exit;
    }

    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === "approve") {
        approveKomentar($id);
    } elseif ($action === "delete") {
        hapusKomentar($id);
    }

    // FIX: dashboard juga ada di root
    header("Location: dashboard.php");
    exit;
}
?>
