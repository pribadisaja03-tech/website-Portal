<?php
session_start();
include "config.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Portal Cerita Rakyat Lombok</title>
  <style>
    /* ===== Base ===== */
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #6fb1fc, #4364f7, #3f51b5);
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
      width: 350px;
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
      transition: 0.2s;
      background: #f8f9fa;
    }

    form input:focus {
      border-color: #3f51b5;
      background: #fff;
      box-shadow: 0 0 0 3px rgba(63, 81, 181, 0.2);
    }

    .btn {
      width: 100%;
      background: #3f51b5;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      transition: 0.3s;
    }

    .btn:hover {
      background: #32408f;
      transform: scale(1.03);
    }

    .btn-outline {
      color: #3f51b5;
      text-decoration: none;
      font-weight: 600;
      border: 1px solid #3f51b5;
      padding: 6px 14px;
      border-radius: 8px;
      transition: 0.3s;
    }

    .btn-outline:hover {
      background: #3f51b5;
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

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="form-card">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <input type="text" name="username" placeholder="Masukkan username" required>
      <input type="password" name="password" placeholder="Masukkan password" required>
      <button type="submit" class="btn">Login</button>
    </form>

    <p style="margin-top:14px;">Belum punya akun? 
      <a href="register.php" class="btn-outline">Register</a>
    </p>
  </div>
</body>
</html>
