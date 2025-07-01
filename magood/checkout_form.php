<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit;
}
$user = $_SESSION['user'];
?>
<form action="checkout_proses.php" method="POST">
  <input type="hidden" name="cart" id="cartData">

  <label>Nama:</label>
  <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>

  <label>Email:</label>
  <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

  <label>Alamat:</label>
  <textarea name="alamat" required></textarea>

  <label>No HP:</label>
  <input type="text" name="telepon" required>

  <button type="submit">Checkout</button>
</form>

<script>
  document.getElementById('cartData').value = localStorage.getItem('cart');
</script>
