<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';
include '../fungsi.php'; // buat hapus foto laporan

if (isset($_POST['ubah_status'])) {
    $id_laporan = $_POST['id_laporan'];
    $status_baru = $_POST['status_baru'];

    $stmt = $connect->prepare("UPDATE laporan SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status_baru, $id_laporan);
    $stmt->execute();
    $stmt->close();
    header("Location: laporan.php");
    exit;
}

if (isset($_POST['hapus_laporan'])) {
    $id_hapus = (int)$_POST['id_laporan'];
    $ambil = $connect->prepare("SELECT foto FROM laporan Where id = ?");
    $ambil->bind_param("i", $id_hapus);
    $ambil->execute();
    $data = $ambil->get_result()->fetch_assoc();
    $ambil->close();

    $del = $connect->prepare("DELETE FROM laporan WHERE id = ?");
    $del->bind_param("i", $id_hapus);
    $del->execute();
    $del->close();

    if ($data) {
        hapusFotoLaporan($data['foto']);
    }
    header("Location: laporan.php");
    exit;
}

$statistik = ['menunggu' => 0, 'diterima' => 0, 'diproses' => 0, 'selesai' => 0];
$result_statistik = $connect->query("SELECT status, COUNT(*) AS total FROM laporan GROUP BY status");
while ($row_statistik = $result_statistik->fetch_assoc()) {
    $statistik[$row_statistik['status']] = $row_statistik['total'];
}
$total_laporan = array_sum($statistik);

$query = "SELECT laporan.*, users.nama AS nama_mahasiswa, fasilitas.nama_fasilitas, fasilitas.lokasi 
          FROM laporan 
          JOIN users ON laporan.user_id = users.id 
          JOIN fasilitas ON laporan.fasilitas_id = fasilitas.id 
          ORDER BY laporan.id DESC";
$result = $connect->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - Kelola Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="laporan.php">lapor.in (Admin)</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active fw-bold" href="laporan.php">Kelola Laporan</a>
                <a class="nav-link" href="users.php">Data User</a>
                <a class="nav-link text-danger" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-2 text-center">Kelola Laporan Kerusakan Fasilitas</h2>
        <p class="text-muted text-center mb-4">Daftar seluruh laporan dari mahasiswa dan kontrol status penanganannya.</p>
        <div class="row mb-4 text-center">
            <div class="col-6 col-md mb-2">
                <div class="card shadow-sm p-3 h-100">
                    <small class="text-muted"><i class="bi bi-clipboard-data me-1"></i> Total Laporan</small>
                    <h3 class="mb-0"><?= $total_laporan; ?></h3>
                </div>
            </div>
            <div class="col-6 col-md mb-2">
                <div class="card shadow-sm p-3 h-100 border-warning border-top border-3">
                    <small class="text-muted"><i class="bi bi-hourglass-split text-warning"></i> Menunggu</small>
                    <h3 class="mb-0"><?= $statistik['menunggu']; ?></h3>
                </div>
            </div>
            <div class="col-6 col-md mb-2">
                <div class="card shadow-sm p-3 h-100 border-info border-top border-3">
                    <small class="text-muted"><i class="bi bi-envelope-check text-info"></i> Diterima</small>
                    <h3 class="mb-0"><?= $statistik['diterima']; ?></h3>
                </div>
            </div>
            <div class="col-6 col-md mb-2">
                <div class="card shadow-sm p-3 h-100 border-primary border-top border-3">
                    <small class="text-muted"><i class="bi bi-tools text-primary"></i> Diproses</small>
                    <h3 class="mb-0"><?= $statistik['diproses']; ?></h3>
                </div>
            </div>
            <div class="col-6 col-md mb-2">
                <div class="card shadow-sm p-3 h-100 border-success border-top border-3">
                    <small class="text-muted"><i class="bi bi-check-circle-fill text-success"></i> Selesai</small>
                    <h3 class="mb-0"><?= $statistik['selesai']; ?></h3>
                </div>
            </div>
        </div>

        <div class="card shadow-sm p-3">
        <input class="form-control" id="myInput" type="text" placeholder="Search.."><br>
            <div class="table-responsive" id="myTable">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Pelapor</th>
                            <th>Fasilitas & Lokasi</th>
                            <th>Judul & Deskripsi</th>
                            <th>Foto</th>
                            <th>Status Saat Ini</th>
                            <th>Aksi Ubah Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_mahasiswa']); ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_fasilitas']); ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($row['lokasi']); ?></small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['judul']); ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($row['deskripsi']); ?></small>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['foto'])): ?>
                                            <a href="../uploads/<?= htmlspecialchars($row['foto']); ?>" target="_blank">
                                                <img src="../uploads/<?= htmlspecialchars($row['foto']); ?>" alt="Foto Laporan" class="img-thumbnail" style="width: 70px; height: 70px; object-fit: cover;">
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Tidak ada foto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $badge = 'secondary';
                                            if ($row['status'] == 'menunggu') $badge = 'warning';
                                            elseif ($row['status'] == 'diterima') $badge = 'info';
                                            elseif ($row['status'] == 'diproses') $badge = 'primary';
                                            elseif ($row['status'] == 'selesai') $badge = 'success';
                                        ?>
                                        <span class="badge bg-<?= $badge; ?>"><?= ucfirst($row['status']); ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-nowrap align-items-center">
                                        <form method="POST" class="d-flex gap-1">
                                            <input type="hidden" name="id_laporan" value="<?= $row['id']; ?>">
                                            <select name="status_baru" class="form-select form-select-sm" style="width: 130px;">
                                                <option value="menunggu" <?= $row['status'] == 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
                                                <option value="diterima" <?= $row['status'] == 'diterima' ? 'selected' : ''; ?>>Diterima</option>
                                                <option value="diproses" <?= $row['status'] == 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                                                <option value="selesai" <?= $row['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                                            </select>
                                            <button type="submit" name="ubah_status" class="btn btn-sm btn-primary">Ubah</button>
                                        </form>
                                        <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?');">
                                            <input type="hidden" name="id_laporan" value="<?= $row['id']; ?>">
                                            <button type="submit" name="hapus_laporan" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada laporan masuk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
                $(document).ready(function(){
                $("#myInput").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $("#myTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
                });
                </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
</body>
</html>