<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email   = $_POST['email'];
    $nama    = $_POST['nama'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat  = $_POST['alamat'];
    $no_hp   = $_POST['no_hp'];

    // Cek apakah email sudah digunakan
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email sudah digunakan!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (email, password, nama, alamat, no_hp) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $email, $password, $nama, $alamat, $no_hp);
        $stmt->execute();
        header("Location: login.php?status=daftar_sukses");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar - MaGood Mart</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <section class="checkout-section">
    <div class="checkout-container">
      <h2 class="checkout-title">📝 Daftar Akun</h2>
      <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
      <form method="post" class="checkout-form">
        <div class="checkout-group">
          <label>Email</label>
          <input type="email" name="email" required>
        </div>
        <div class="checkout-group">
          <label>Nama</label>
          <input type="text" name="nama" required>
        </div>
        <div class="checkout-group">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>
        <div class="checkout-group">
          <label>Alamat</label>
          <textarea name="alamat" required></textarea>
        </div>
        <div class="checkout-group">
          <label>No HP</label>
          <input type="text" name="no_hp" required>
        </div>
        <button type="submit" class="checkout-submit-btn">Daftar</button>
        <p style="text-align:center;">Sudah punya akun? <a href="login.php">Login di sini</a></p>
      </form>
    </div>
  </section>
</body>
</html>
