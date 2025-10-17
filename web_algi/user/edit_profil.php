<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit;
}

$uid = $_SESSION['user_id'];

// Ambil data lama
$stmt = $conn->prepare("SELECT id, username, foto FROM user WHERE id=?");
$stmt->bind_param("i", $uid);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Update jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $fotoName = $user['foto'];

  // Upload foto baru jika ada
  if (!empty($_FILES['foto']['name'])) {
    $fotoName = time() . "_" . basename($_FILES['foto']['name']);
    move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/" . $fotoName);
  }

  $stmt = $conn->prepare("UPDATE user SET username=?, foto=? WHERE id=?");
  $stmt->bind_param("ssi", $username, $fotoName, $uid);
  $stmt->execute();
  $stmt->close();

  // Update juga session supaya nama di navbar langsung berubah
  $_SESSION['username'] = $username;

  header("Location: profil.php");
  exit;
}

include '../navbar.php';
?>

<main class="container">
  <h1>✏️ Edit Profil</h1>

  <form method="post" enctype="multipart/form-data" class="form-edit">
    <label>Username:</label>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

    <label>Foto Profil:</label>
    <input type="file" name="foto" accept="image/*">

    <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
    <a href="profil.php" class="btn-cancel">❌ Batal</a>
  </form>
</main>

<?php include '../footer.php'; ?>
