<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId, $hash);
    $stmt->fetch();

    if ($userId && password_verify($pass, $hash)) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['email'] = $email;
        header("Location: index.html");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - MaGood Mart</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <section class="checkout-section">
    <div class="checkout-container">
      <h2 class="checkout-title">🔐 Login</h2>
      <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
      <form method="post" class="checkout-form">
        <div class="checkout-group">
          <label>Email</label>
          <input type="email" name="email" required>
        </div>
        <div class="checkout-group">
          <label>Password</label>
          <input type="password" name="password" required>
        </div>
        <button type="submit" class="checkout-submit-btn">Login</button>
        <p style="text-align:center;">Belum punya akun? <a href="signup.php">Daftar di sini</a></p>
      </form>
    </div>
  </section>
</body>
</html>
