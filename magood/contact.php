<?php
include __DIR__ . '/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            // ✅ Redirect ke halaman utama setelah berhasil kirim pesan
            header("Location: index.html?status=sukses");
            exit();
        } else {
            // Redirect ke halaman dengan pesan error
            header("Location: index.html?status=gagal");
            exit();
        }

        $stmt->close();
        $conn->close();
    } else {
        header("Location: index.html?status=kosong");
        exit();
    }
} else {
    header("Location: index.html?status=tidak-valid");
    exit();
}
?>
