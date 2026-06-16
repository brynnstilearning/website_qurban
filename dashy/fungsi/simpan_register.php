<?php
// fungsi/simpan_register.php
error_reporting(0);
session_start();
include '../../koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
    exit;
}

$original_username = $_POST['original_username'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$ulang_password = $_POST['ulang_password'] ?? '';
$namalengkap = $_POST['namalengkap'] ?? '';
$nik = $_POST['nik'] ?? '';
$level = $_POST['level'] ?? '';
$id_peserta = $_POST['id_peserta'] ?? '';

// Konversi id_peserta kosong menjadi NULL untuk database
$id_peserta_db = empty($id_peserta) ? NULL : (int)$id_peserta;

if (empty($original_username)) {
    // --- PROSES INSERT DATA BARU ---
    if ($password !== $ulang_password) {
        echo json_encode(['status' => 'error', 'message' => 'Password dan Ulangi Password tidak cocok.']);
        exit;
    }
    $hashed_password = md5($password);
    
    $sql = "INSERT INTO register (username, password, namalengkap, nik, level, id_peserta) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $username, $hashed_password, $namalengkap, $nik, $level, $id_peserta_db);
    $message = "Pengguna baru berhasil ditambahkan!";
} else {
    // --- PROSES UPDATE DATA LAMA ---
    // Password tidak diubah di form ini
    $sql = "UPDATE register SET namalengkap = ?, nik = ?, level = ?, id_peserta = ? WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "sssis", $namalengkap, $nik, $level, $id_peserta_db, $original_username);
    $message = "Data pengguna berhasil diperbarui!";
}

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['status' => 'success', 'message' => $message]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Eksekusi query gagal: ' . mysqli_stmt_error($stmt)]);
}
mysqli_stmt_close($stmt);
?>