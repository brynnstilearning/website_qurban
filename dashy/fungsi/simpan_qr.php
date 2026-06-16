<?php
// fungsi/simpan_qr.php (VERSI SESUAI FORM ORIGINAL)
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

// Ambil data langsung dari form seperti skrip original
$id_peserta = $_POST['id_peserta'] ?? '';
$name = $_POST['name'] ?? '';
$qr_token = $_POST['qr_token'] ?? '';
$is_used = $_POST['is_used'] ?? 0;

if (empty($id_peserta) || empty($name) || empty($qr_token)) {
    echo json_encode(['status' => 'error', 'message' => 'Semua field dropdown wajib diisi.']);
    exit;
}

// Gunakan prepared statement untuk keamanan
$sql = "INSERT INTO data (id_peserta, name, qr_token, is_used) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($koneksi, $sql);
// Bind parameter: integer, string, string, integer
mysqli_stmt_bind_param($stmt, "issi", $id_peserta, $name, $qr_token, $is_used);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['status' => 'success', 'message' => 'Data QR berhasil disimpan untuk ' . $name]);
} else {
    if (mysqli_errno($koneksi) == 1062) {
        echo json_encode(['status' => 'error', 'message' => 'Gagal: Peserta ini kemungkinan sudah memiliki QR Code.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Eksekusi query gagal: ' . mysqli_stmt_error($stmt)]);
    }
}
mysqli_stmt_close($stmt);
?>