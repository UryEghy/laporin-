<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';
include '../fungsi.php';

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


$stmt = $connect->prepare("SELECT laporan.*, fasilitas.nama_fasilitas, fasilitas.lokasi
                           FROM laporan
                           JOIN fasilitas ON laporan.fasilitas_id = fasilitas.id
                           WHERE laporan.id = ? AND laporan.user_id = ? AND laporan.status = 'menunggu'");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$laporan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$laporan) {
    $_SESSION['pesan_riwayat'] = "<div class='alert alert-danger'>Laporan tidak ditemukan atau sudah tidak bisa diedit.</div>";
    header("Location: riwayat.php");
    exit;
}

$pesan = "";

if (isset($_POST['simpan'])) {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);

    if ($judul == "" || $deskripsi == "") {
        $pesan = "<div class='alert alert-danger'>Judul dan deskripsi wajib diisi!</div>";
    } else {
        $foto_baru = simpanFotoLaporan($_FILES['foto'], $error_foto);

        if ($error_foto != "") {
            $pesan = "<div class='alert alert-danger'>" . $error_foto . "</div>";
        } else {
            if ($foto_baru !== null) {
                hapusFotoLaporan($laporan['foto']);
                $up = $connect->prepare("UPDATE laporan SET judul = ?, deskripsi = ?, foto = ? WHERE id = ? AND user_id = ? AND status = 'menunggu'");
                $up->bind_param("sssii", $judul, $deskripsi, $foto_baru, $id, $user_id);
            } else {
                $up = $connect->prepare("UPDATE laporan SET judul = ?, deskripsi = ? WHERE id = ? AND user_id = ? AND status = 'menunggu'");
                $up->bind_param("ssii", $judul, $deskripsi, $id, $user_id);
            }

            if ($up->execute()) {
                $up->close();
                $_SESSION['pesan_riwayat'] = "<div class='alert alert-success'>Laporan berhasil diperbarui!</div>";
                header("Location: riwayat.php");   
                exit;
            } else {
                $pesan = "<div class='alert alert-danger'>Gagal memperbarui laporan.</div>";
                $up->close();
            }
        }
    }
}

$base = "../";
include '../header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <h3 class="text-center mb-3">Edit Laporan</h3>
                <?= $pesan; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Fasilitas & Lokasi</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($laporan['nama_fasilitas'] . ' - ' . $laporan['lokasi']); ?>" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul Laporan</label>
                        <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($_POST['judul'] ?? $laporan['judul']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Kerusakan</label>
                        <textarea name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($_POST['deskripsi'] ?? $laporan['deskripsi']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Kerusakan</label>
                        <?php if (!empty($laporan['foto'])): ?>
                            <div class="mb-2">
                                <img src="../uploads/<?= htmlspecialchars($laporan['foto']); ?>" alt="Foto saat ini" class="img-thumbnail" style="width: 120px; height: 120px; object-fit: cover;">
                                <small class="text-muted d-block">Foto saat ini. Pilih file baru hanya jika ingin menggantinya.</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary w-100 mb-2">Simpan Perubahan</button>
                    <a href="riwayat.php" class="btn btn-outline-secondary w-100">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include '../footer.php'; 
?>