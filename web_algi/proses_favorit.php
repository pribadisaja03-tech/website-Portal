<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerita_id'], $_POST['aksi'])) {
    $user_id = $_SESSION['user_id'];
    $cerita_id = intval($_POST['cerita_id']);
    $aksi = $_POST['aksi'];

    if ($aksi === 'tambah') {
        $stmt = $conn->prepare("INSERT INTO favorite (user_id, cerita_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $cerita_id);
        $stmt->execute();
        $stmt->close();
    } elseif ($aksi === 'hapus') {
        $stmt = $conn->prepare("DELETE FROM favorite WHERE user_id = ? AND cerita_id = ?");
        $stmt->bind_param("ii", $user_id, $cerita_id);
        $stmt->execute();
        $stmt->close();
    }
}

// kembali ke halaman baca
header("Location: baca.php?id=" . $cerita_id);
exit;
?>
