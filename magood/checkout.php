<?php
include __DIR__ . '/koneksi.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nama, email, alamat, no_hp FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($nama, $email, $alamat, $no_hp);
$stmt->fetch();
$stmt->close();

$checkoutItems = [];
$grandTotal = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart'])) {
    $items = json_decode($_POST['cart'], true);

    if (is_array($items)) {
    foreach ($items as $item) {
        $grandTotal += $item['total'];
    }

    // ✅ INSERT KE TABEL checkout - SATU KALI
    $stmt = $conn->prepare("INSERT INTO checkout (user_id, nama, email, alamat, telepon, grand_total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $userId, $nama, $email, $alamat, $no_hp, $grandTotal);
    $stmt->execute();
    $checkoutId = $stmt->insert_id;
    $stmt->close();

    // 🔁 Baru looping insert ke checkout_items
    foreach ($items as $item) {
        $name  = $item['name'];
        $qty   = $item['qty'];
        $total = $item['total'];

        $stmt = $conn->prepare("INSERT INTO checkout_items (checkout_id, product_name, quantity, total_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isii", $checkoutId, $name, $qty, $total);
        $stmt->execute();
        $stmt->close();

        $checkoutItems[] = $item;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout - MaGood Mart</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #f9f9f9;
    }

    .checkout-section {
      padding: 50px 20px;
      display: flex;
      justify-content: center;
    }

    .checkout-container {
      background: #ffffff;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      max-width: 600px;
      width: 100%;
    }

    .checkout-title {
      font-size: 2rem;
      color: #2c3e50;
      margin-bottom: 25px;
      text-align: center;
    }

    .checkout-group {
      margin-bottom: 20px;
      padding: 15px;
      background-color: #f4f7fc;
      border-radius: 10px;
      border-left: 5px solid #3498db;
    }

    .checkout-group label {
      font-weight: bold;
      display: block;
      margin-bottom: 5px;
      font-size: 1.1rem;
    }

    .checkout-group p {
      margin: 0;
      color: #333;
    }

    .checkout-submit-btn {
      display: inline-block;
      background-color: #27ae60;
      color: #fff;
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
      margin-top: 20px;
      text-decoration: none;
      text-align: center;
    }

    .checkout-submit-btn:hover {
      background-color: #219150;
    }

    hr {
      border: none;
      border-top: 2px dashed #ccc;
      margin: 30px 0;
    }

    @media (max-width: 600px) {
      .checkout-container {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <section class="checkout-section">
    <div class="checkout-container">
      <h2 class="checkout-title">🧾 Checkout Selesai</h2>

      <!-- DATA USER -->
      <div class="checkout-group" style="background-color: #fffef0;">
        <label>Nama Pemesan:</label>
        <p><?= htmlspecialchars($nama) ?></p>

        <label>Email:</label>
        <p><?= htmlspecialchars($email) ?></p>

        <label>Alamat:</label>
        <p><?= nl2br(htmlspecialchars($alamat)) ?></p>

        <label>No HP:</label>
        <p><?= htmlspecialchars($no_hp) ?></p>
      </div>
      <hr>

      <!-- DATA PRODUK -->
      <?php if (count($checkoutItems) > 0): ?>
        <div class="checkout-form">
          <?php foreach ($checkoutItems as $item): ?>
            <div class="checkout-group">
              <label><?= htmlspecialchars($item['name']) ?></label>
              <p>Jumlah: <?= $item['qty'] ?> | Total: Rp<?= number_format($item['total'], 0, ',', '.') ?></p>
            </div>
          <?php endforeach; ?>

          <hr>
          <div class="checkout-group" style="background-color:#eafbea; border-left-color: #2ecc71;">
            <strong>Total Semua:</strong>
            <p style="font-size: 1.2rem;">💰 Rp<?= number_format($grandTotal, 0, ',', '.') ?></p>
          </div>
        </div>
      <?php else: ?>
        <p>Tidak ada data checkout ditemukan.</p>
      <?php endif; ?>

      <?php if (count($checkoutItems) > 0): ?>
        <form method="post" onsubmit="return handlePayment()" style="text-align:center;">
          <button type="submit" class="checkout-submit-btn">💳 Bayar Sekarang</button>
        </form>
      <?php endif; ?>

      <div style="text-align:center;">
        <a href="index.html" class="checkout-submit-btn" style="background-color:#2980b9;">🏠 Kembali ke Beranda</a>
      </div>
    </div>
  </section>

  <script>
    function handlePayment() {
      localStorage.removeItem('cart');
      alert("✅ Pembayaran berhasil!\nTerima kasih telah belanja di MaGood Mart.");
      setTimeout(() => {
        window.location.href = "index.html";
      }, 1500);
      return false;
    }
  </script>
</body>
</html>
