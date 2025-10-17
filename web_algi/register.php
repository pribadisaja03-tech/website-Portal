<?php
session_start();
include "config.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    if ($password !== $confirm) {
        $error = "Password dan konfirmasi tidak sama!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $error = "Username sudah digunakan!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role   = "user";

            $stmt = $conn->prepare("INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed, $role);
            if ($stmt->execute()) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Gagal registrasi, coba lagi.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Portal Cerita Rakyat Lombok</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #ffb347, #ffcc33, #f57c00);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-card {
      background: #fff;
      padding: 40px 35px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      width: 360px;
      text-align: center;
      transition: all 0.3s ease;
      animation: fadeIn 0.6s ease-in-out;
    }

    .form-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 28px rgba(0,0,0,0.2);
    }

    .form-card h2 {
      margin-bottom: 25px;
      color: #333;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    form input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 10px;
      outline: none;
      background: #f8f9fa;
      transition: 0.2s;
    }

    form input:focus {
      border-color: #f57c00;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(245, 124, 0, 0.2);
    }

    .btn {
      width: 100%;
      background: #f57c00;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn:hover {
      background: #e65100;
      transform: scale(1.03);
    }

    .btn-outline {
      color: #f57c00;
      text-decoration: none;
      font-weight: 600;
      border: 1px solid #f57c00;
      padding: 6px 14px;
      border-radius: 8px;
      transition: 0.3s;
    }

    .btn-outline:hover {
      background: #f57c00;
      color: #fff;
    }

    .error {
      background: #ffe6e6;
      color: #d32f2f;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
    }

    .success {
      background: #e8f5e9;
      color: #2e7d32;
      padding: 10px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 14px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="form-card">
    <h2>Register</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
      <input type="text" name="username" placeholder="Masukkan username" required>
      <input type="password" name="password" placeholder="Masukkan password" required>
      <input type="password" name="confirm" placeholder="Konfirmasi password" required>
      <button type="submit" class="btn">Register</button>
    </form>

    <p style="margin-top:14px;">Sudah punya akun?
      <a href="login.php" class="btn-outline">Login</a>
    </p>
  </div>
</body>
</html>
