<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa'){
    header("Location: ../login.php");
    exit;
}

include '../koneksi.php';
include '../fungsi.php';

$user_id=
$_SESSION['user_id'];

if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])){
    header("Location: riwayat.php");
    exit;
}

$id = (int)$_POST['id'];

$stmt = $connect->prepare("SELECT foto FROM laporan WHERE id = ? AND user_id = ? AND status = 'menunggu'");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$laporan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($laporan) {
    $del = $connect->prepare("DELETE FROM laporan WHERE id = ? AND user_id = ? AND status = 'menunggu'");
    $del->bind_param("ii", $id, $user_id);
    $del->execute();
    $del->close();

hapusFotoLaporan($laporan['foto']);

    $_SESSION['pesan_riwayat'] = "<div class='alert alert-success'>Laporan berhasil dihapus.</div>";
    } else {
        $_SESSION['pesan_riwayat'] = "<div class='alert alert-danger'>Laporan tidak ditemukan atau sudah tidak bisa dihapus.</div>";
    }

    header("Location: riwayat.php");
    exit;
?>