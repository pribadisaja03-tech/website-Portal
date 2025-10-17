<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); exit;
}
include "../config.php";

$id = $_GET['id'];
$conn->query("DELETE FROM cerita WHERE id=$id");

header("Location: dashboard.php?msg=deleted");
exit;
