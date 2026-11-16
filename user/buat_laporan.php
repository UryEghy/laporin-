<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$pesan = "";

if (isset($_POST['kirim'])) {
    $user_id = $_SESSION['user_id'];
    $nama_fasilitas = trim($_POST['nama_fasilitas']);
    $lokasi = trim($_POST['lokasi']);
    $judul = trim($_POST['judul']);$deskripsi = trim($_POST['deskripsi']);$status = 'menunggu';

    if ($nama_fasilitas == "" || $lokasi == "" || $judul == "" || $deskripsi == "") {
        $pesan = "<div class='alert alert-danger'>Semua kolom wajib diisi!</div>";
    } else {
        $cek = $connect->prepare("SELECT id FROM fasilitas WHERE LOWER(nama_fasilitas) = LOWER(?) AND LOWER(lokasi) = LOWER(?) LIMIT 1");
        $cek->bind_param("ss", $nama_fasilitas, $lokasi);
        $cek->execute();
        $row_f = $cek->get_result()->fetch_assoc();
        $cek->close();

        if ($row_f) {
            $fasilitas_id = $row_f['id'];
        } else {
            $ins = $connect->prepare("INSERT INTO fasilitas (nama_fasilitas, lokasi) VALUES (?, ?)");
            $ins->bind_param("ss", $nama_fasilitas, $lokasi);
            $ins->execute();
            $fasilitas_id = $connect->insert_id;
            $ins->close();
        }
        $query = "INSERT INTO laporan (user_id, fasilitas_id, judul, deskripsi, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($query);
        $stmt->bind_param("iisss", $user_id, $fasilitas_id,$judul, $deskripsi,$status);

        if ($stmt->execute()) {$pesan = "<div class='alert alert-success'>Laporan berhasil dikirim! <a href='riwayat.php'>Lihat Riwayat</a></div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal mengirim laporan. Coba lagi.</div>";
        }
        $stmt->close();
    }
}

$base = "../";
include '../header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm p-4">
                <h3 class="mb-3">Buat Laporan Kerusakan</h3>
                <p class="text-muted">Laporkan fasilitas kampus yang mengalami kerusakan agar segera ditindaklanjuti.</p>
                
                <?= $pesan; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas</label>
                        <input type="text" name="nama_fasilitas" class="form-control" placeholder="Contoh: AC, Proyektor, Kursi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Pattimura III 3-D" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Judul Laporan</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: AC Kelas Rusak / Proyektor Mati" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Kerusakan</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan kondisi kerusakan secara detail..." required></textarea>
                    </div>
                    <button type="submit" name="kirim" class="btn btn-primary w-100">Kirim Laporan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
include '../footer.php'; 
?>