<?php
// fungsi/delete_warga.php
include '../../koneksi.php';

header('Content-Type: application/json');

// Pastikan hanya admin yang bisa menghapus
session_start();
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

if (isset($_POST['nik'])) {
    $nik_to_delete = $_POST['nik'];

    $stmt = mysqli_prepare($koneksi, "DELETE FROM warga WHERE nik = ?");
    mysqli_stmt_bind_param($stmt, "s", $nik_to_delete);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => 'Data warga berhasil dihapus!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . mysqli_error($koneksi)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'NIK tidak ditemukan untuk dihapus.']);
}
?>