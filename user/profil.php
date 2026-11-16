<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$user_id = $_SESSION['user_id'];
$pesan = "";

// 1. Proses Update Profil (Nama & Foto)
if (isset($_POST['update_profil'])) {
    $nama_baru = trim($_POST['nama']);

    if ($nama_baru == "") {
        $pesan = "<div class='alert alert-danger'>Nama tidak boleh kosong!</div>";
    } else {
        // Cek apakah ada file foto yang diunggah
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $nama_file = $_FILES['foto']['name'];
            $ukuran_file = $_FILES['foto']['size'];
            $tmp_file = $_FILES['foto']['tmp_name'];
            
            // Validasi ekstensi file
            $ekstensi_dibolehkan = ['jpg', 'jpeg', 'png', 'gif'];
            $x = explode('.', $nama_file);
            $ekstensi = strtolower(end($x));

            if (in_array($ekstensi, $ekstensi_dibolehkan) === true) {
                if ($ukuran_file < 2 * 1024 * 1024) { // Maksimal ukuran 2MB
                    // Buat nama file unik agar tidak bentrok
                    $nama_file_baru = uniqid() . '.' . $ekstensi;
                    
                    // Folder penyimpanan
                    $folder_tujuan = '../uploads/';
                    
                    if (!is_dir($folder_tujuan)) {
                        mkdir($folder_tujuan, 0777, true);
                    }

                    // Ambil foto lama untuk dihapus jika bukan default
                    $query_lama = "SELECT foto FROM users WHERE id = ?";
                    $stmt_lama = $connect->prepare($query_lama);
                    $stmt_lama->bind_param("i", $user_id);
                    $stmt_lama->execute();
                    $res_lama = $stmt_lama->get_result()->fetch_assoc();
                    $stmt_lama->close();

                    if ($res_lama['foto'] && $res_lama['foto'] != 'default.png' && file_exists($folder_tujuan . $res_lama['foto'])) {
                        unlink($folder_tujuan . $res_lama['foto']);
                    }

                    // Pindahkan file baru
                    if (move_uploaded_file($tmp_file, $folder_tujuan . $nama_file_baru)) {
                        // Update nama dan foto ke database
                        $update_query = "UPDATE users SET nama = ?, foto = ? WHERE id = ?";
                        $stmt_up = $connect->prepare($update_query);
                        $stmt_up->bind_param("ssi", $nama_baru, $nama_file_baru, $user_id);
                        
                        if ($stmt_up->execute()) {
                            $_SESSION['nama'] = $nama_baru; // Update session nama
                            $pesan = "<div class='alert alert-success'>Profil dan foto berhasil diperbarui!</div>";
                        } else {
                            $pesan = "<div class='alert alert-danger'>Gagal memperbarui database.</div>";
                        }
                        $stmt_up->close();
                    } else {
                        $pesan = "<div class='alert alert-danger'>Gagal mengunggah foto.</div>";
                    }
                } else {
                    $pesan = "<div class='alert alert-danger'>Ukuran foto terlalu besar! Maksimal 2MB.</div>";
                }
            } else {
                $pesan = "<div class='alert alert-danger'>Ekstensi file tidak diizinkan! Hanya JPG, JPEG, PNG, GIF.</div>";
            }
        } else {
            // Jika tidak ganti foto, hanya update nama saja
            $update_query = "UPDATE users SET nama = ? WHERE id = ?";
            $stmt_up = $connect->prepare($update_query);
            $stmt_up->bind_param("si", $nama_baru, $user_id);
            
            if ($stmt_up->execute()) {
                $_SESSION['nama'] = $nama_baru;
                $pesan = "<div class='alert alert-success'>Nama profil berhasil diperbarui!</div>";
            } else {
                $pesan = "<div class='alert alert-danger'>Gagal memperbarui nama.</div>";
            }
            $stmt_up->close();
        }
    }
}

// 2. Ambil data user terbaru dari database
$query_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $connect->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_data = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

$foto_profil = (!empty($user_data['foto']) && file_exists('../uploads/' . $user_data['foto'])) ? '../uploads/' . $user_data['foto'] : 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/icons/person-circle.svg';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya - Lapor.in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">lapor.in (Mahasiswa)</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link active fw-bold" href="profil.php">Profil Saya</a>
                <a class="nav-link text-danger" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm p-4 border-0">
                    <h3 class="mb-3 fw-bold">Profil Saya</h3>
                    <p class="text-muted">Kelola informasi profil dan foto akun Anda.</p>

                    <?= $pesan; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="text-center mb-4">
                            <!-- Preview Foto Profil -->
                            <img src="<?= $foto_profil; ?>" alt="Foto Profil" class="rounded-circle img-thumbnail shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                            <div>
                                <label class="form-label small text-muted d-block">Ganti Foto Profil (Maks. 2MB)</label>
                                <input type="file" name="foto" class="form-control form-control-sm w-75 mx-auto">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap / Username</label>
                            <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user_data['nama']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Mahasiswa</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user_data['email']); ?>" disabled readonly>
                            <small class="text-muted">Email tidak dapat diubah.</small>
                        </div>

                        <button type="submit" name="update_profil" class="btn btn-primary w-100 py-2">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
</body>
</html>