<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';

$base = "../";
include '../header.php';

$pesan = "";
if (isset($_SESSION['pesan_riwayat'])) {
    $pesan = $_SESSION['pesan_riwayat'];
    unset($_SESSION['pesan_riwayat']);
}

$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$user_id = $_SESSION['user_id'];

if ($filter_status != "") {
    $query = "SELECT laporan.*, fasilitas.nama_fasilitas, fasilitas.lokasi 
              FROM laporan 
              JOIN fasilitas ON laporan.fasilitas_id = fasilitas.id 
              WHERE laporan.user_id = ? AND laporan.status = ? 
              ORDER BY laporan.id DESC";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("is", $user_id, $filter_status);
} else {
    $query = "SELECT laporan.*, fasilitas.nama_fasilitas, fasilitas.lokasi 
              FROM laporan 
              JOIN fasilitas ON laporan.fasilitas_id = fasilitas.id 
              WHERE laporan.user_id = ? 
              ORDER BY laporan.id DESC";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h2 class="mb-3">Riwayat Laporan Saya</h2>
    <p class="text-muted">Pantau perkembangan status perbaikan fasilitas yang kamu laporkan di sini.</p>

    <?= $pesan; ?>

    <div class="mb-4">
        <span class="fw-bold me-2">Filter Status:</span>
        <a href="riwayat.php" class="btn btn-sm <?= $filter_status == '' ? 'btn-outline-dark' : 'btn-dark'; ?>">Semua</a>
        <a href="riwayat.php?status=menunggu" class="btn btn-sm <?= $filter_status == 'menunggu' ? 'btn-outline-warning' : 'btn-warning'; ?>">Menunggu</a>
        <a href="riwayat.php?status=diterima" class="btn btn-sm <?= $filter_status == 'diterima' ? 'btn-outline-info' : 'btn-info'; ?>">Diterima</a>
        <a href="riwayat.php?status=diproses" class="btn btn-sm <?= $filter_status == 'diproses' ? 'btn-outline-primary' : 'btn-primary'; ?>">Diproses</a>
        <a href="riwayat.php?status=selesai" class="btn btn-sm <?= $filter_status == 'selesai' ? 'btn-outline-success' : 'btn-success'; ?>">Selesai</a>
    </div>

    <div class="card shadow-sm p-3">
        <input class="form-control" id="myInput" type="text" placeholder="Search...">
        <div class="table-responsive" id="myTable">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Fasilitas & Lokasi</th>
                        <th>Judul Laporan</th>
                        <th>Deskripsi</th>
                        <th>Foto</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_fasilitas']); ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($row['lokasi']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($row['judul']); ?></td>
                                <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                                <td>
                                    <?php if (!empty($row['foto'])): ?>
                                        <a href="../uploads/<?= htmlspecialchars($row['foto']); ?>" target="_blank">
                                        <img src="../uploads/<?= htmlspecialchars($row['foto']); ?>" alt="Foto Laporan" class="img-thumbnail" style="width: 70px; height: 70px; object-fit: cover;">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        if ($row['status'] == 'menunggu');
                                        elseif ($row['status'] == 'diterima'); 
                                        elseif ($row['status'] == 'diproses'); 
                                        elseif ($row['status'] == 'selesai'); 
                                    ?>
                                    <span><?= ucfirst($row['status']); ?></span>
                                </td>
                                <td><small><?= $row['created_at']; ?></small></td>
                                <td>
                                    <?php if ($row['status'] == 'menunggu'): ?>
                                        <div class="d-flex gap-1">
                                        <a href="edit_laporan.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-primary mb-1">Edit</a>
                                        <form method="POST" action="hapus_laporan.php" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak tersedia</span>
                                    <?php endif; ?>
                                    
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat laporan kerusakan.</td>
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

<?php 
$stmt->close();
include '../footer.php'; 
?>