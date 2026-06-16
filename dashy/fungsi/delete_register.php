<?php
// fungsi/delete_register.php
error_reporting(0);
session_start();
include '../../koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

if (!isset($_POST['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Username tidak spesifik untuk dihapus.']);
    exit;
}

$username_to_delete = mysqli_real_escape_string($koneksi, $_POST['username']);
$stmt = mysqli_prepare($koneksi, "DELETE FROM register WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username_to_delete);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Pengguna berhasil dihapus!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Pengguna tidak ditemukan.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus pengguna: ' . mysqli_stmt_error($stmt)]);
}
mysqli_stmt_close($stmt);
?>