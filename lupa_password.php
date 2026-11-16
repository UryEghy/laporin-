<?php
session_start();
include 'koneksi.php';

$pesan = "";

if (isset($_POST['reset'])) {
    $email = trim($_POST['email']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    if ($email == "" || $password_baru == "" || $konfirmasi_password == "") {
        $pesan = "<div class='alert alert-danger'>Semua kolom wajib diisi!</div>";
    } elseif ($password_baru !== $konfirmasi_password) {
        $pesan = "<div class='alert alert-danger'>Konfirmasi password tidak cocok!</div>";
    } elseif (strlen($password_baru) < 6) {
        $pesan = "<div class='alert alert-danger'>Password minimal harus 6 karakter!</div>";
    } else {
        $cek_query = "SELECT id FROM users WHERE email = ?";
        $stmt_cek = $connect->prepare($cek_query);
        $stmt_cek->bind_param("s", $email);
        $stmt_cek->execute();
        $stmt_cek->store_result();

        if ($stmt_cek->num_rows > 0) {
            $password_terbaru = password_hash($password_baru, PASSWORD_DEFAULT);

            $update_query = "UPDATE users SET password = ? WHERE email = ?";
            $stmt_update = $connect->prepare($update_query);
            $stmt_update->bind_param("ss", $password_terbaru, $email);

            if ($stmt_update->execute()) {
                $_SESSION['pesan_sukses'] = "Password berhasil diubah! Silakan login dengan password baru.";
                header("Location: login.php");
                exit;
            } else {
                $pesan = "<div class='alert alert-danger'>Gagal mereset password. Coba lagi.</div>";
            }
            $stmt_update->close();
        } else {
            $pesan = "<div class='alert alert-danger'>Email tidak terdaftar di sistem!</div>";
        }
        $stmt_cek->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Lapor.in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100 m-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm p-4 border-0">
                    <h3 class="text-center mb-4 fw-bold">Reset Password</h3>
                    <p class="text-muted text-center small mb-3">Masukkan email akun Anda untuk memperbarui password.</p>
                    
                    <?= $pesan; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email Terdaftar</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control" minlength="6" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="konfirmasi_password" class="form-control" minlength="6" required>
                        </div>
                        <button type="submit" name="reset" class="btn btn-primary w-100 py-2">Ubah Password</button>
                    </form>
                    <div class="mt-3 text-center">
                        <small class="text-muted">Ingat password Anda? <a href="login.php" class="text-decoration-none">Masuk di sini</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>