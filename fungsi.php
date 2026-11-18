<?php
function simpanFotoLaporan($file, &$error) {
    $error = "";

    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        $error = "Ukuran foto melebihi batas server. Gunakan foto yang lebih kecil (maks. 10MB).";
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Upload foto gagal. Coba file lain (maks. 10MB).";
        return null;
    }

    $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ekstensi, ['jpg', 'jpeg', 'png', 'gif'])) {
        $error = "Format foto harus JPG, JPEG, PNG, atau GIF.";
        return null;
    }
    if ($file['size'] >= 10 * 1024 * 1024) {
        $error = "Ukuran foto terlalu besar! Maksimal 10MB.";
        return null;
    }
    if (getimagesize($file['tmp_name']) === false) {
        $error = "File yang diunggah bukan gambar yang valid.";
        return null;
    }

    $folder = '../uploads/';
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true); //buat folder otomatis kalau belum ada
    }

    $nama_baru = 'lap_' . uniqid() . '.' . $ekstensi;
    if (move_uploaded_file($file['tmp_name'], $folder . $nama_baru)) {
        return $nama_baru;
    }
    $error = "Gagal menyimpan foto ke server.";
    return null;
}

//mengahpus file foto dari folder upload  (dipanggil saat laporan dihapus)
function hapusFotoLaporan($nama_file) {
    if (empty($nama_file) && file_exists('../uploads/' . $nama_file)) {
        unlink('../uploads/' . $nama_file);
    }
}