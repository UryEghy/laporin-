<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>lapor.in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base; ?>style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= $base; ?>user/dashboard.php">lapor.in</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base; ?>user/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base; ?>user/buat_laporan.php">Buat Laporan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base; ?>user/riwayat.php">Riwayat</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="<?= $base; ?>logout.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $base; ?>user/profil.php">Profil Saya</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="flex-grow-1">