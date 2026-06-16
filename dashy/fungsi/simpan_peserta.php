<?php
// fungsi/simpan_peserta.php (VERSI PERBAIKAN)

// 1. Panggil session_start() di baris paling atas
session_start();

// 2. Baru include koneksi dan lainnya
include '../../koneksi.php';

header('Content-Type: application/json');

// Pemeriksaan hak akses admin
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Anda bukan admin.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
    exit;
}

// Ambil data dari form
$id_peserta = $_POST['id_peserta'] ?? '';
$nik = $_POST['nik'] ?? '';
$level = $_POST['level'] ?? '';
$jumlah = $_POST['jumlah_daging_kg'] ?? '';
$status = $_POST['status_ambil'] ?? '';

if (empty($nik) || empty($level) || $jumlah === '' || empty($status)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']);
    exit;
}

if (empty($id_peserta)) {
    // PROSES INSERT DATA BARU
    $sql = "INSERT INTO peserta (nik, level, jumlah_daging_kg, status_ambil) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssds", $nik, $level, $jumlah, $status);
    $message = "Data peserta baru berhasil disimpan!";
} else {
    // PROSES UPDATE DATA LAMA
    $sql = "UPDATE peserta SET nik=?, level=?, jumlah_daging_kg=?, status_ambil=? WHERE id_peserta=?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssdsi", $nik, $level, $jumlah, $status, $id_peserta);
    $message = "Data peserta berhasil diperbarui!";
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['status' => 'success', 'message' => $message]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Eksekusi query gagal: ' . mysqli_stmt_error($stmt)]);
}
mysqli_stmt_close($stmt);

?>