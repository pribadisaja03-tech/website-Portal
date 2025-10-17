<?php
session_start();

// hapus semua session
$_SESSION = [];
session_unset();
session_destroy();

// arahkan ke halaman login
header("Location: login.php");
exit;
