<?php
session_start();
include "../config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Hapus komentar
    $stmt = $conn->prepare("DELETE FROM komentar WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header("Location: dashboard.php");
    exit;
} else {
    echo "ID komentar tidak ditemukan.";
}
