<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userId = $_SESSION['user']['id'];
  $nama = $_POST['nama'];
  $email = $_POST['email'];
  $alamat = $_POST['alamat'];
  $telepon = $_POST['telepon'];
  $cart = json_decode($_POST['cart'], true);
  $grandTotal = 0;

  foreach ($cart as $item) {
    $grandTotal += $item['total'];
  }

  $stmt = $conn->prepare("INSERT INTO checkout (user_id, nama, email, alamat, telepon, grand_total) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("issssi", $userId, $nama, $email, $alamat, $telepon, $grandTotal);
  $stmt->execute();
  $checkoutId = $stmt->insert_id;
  $stmt->close();

  foreach ($cart as $item) {
    $stmt = $conn->prepare("INSERT INTO checkout_items (checkout_id, product_name, quantity, total_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $checkoutId, $item['name'], $item['qty'], $item['total']);
    $stmt->execute();
    $stmt->close();
  }

  echo "<script>
    localStorage.removeItem('cart');
    alert('Checkout berhasil!');
    window.location.href = 'index.html';
  </script>";
}
?>
