<?php
// fungsi/delete_qr.php
error_reporting(0);
session_start();
include '../../koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID Data QR tidak valid.']);
    exit;
}

$id_to_delete = $_POST['id'];

$stmt = mysqli_prepare($koneksi, "DELETE FROM data WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id_to_delete);

if (mysqli_stmt_execute($stmt)) {
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Data QR berhasil dihapus!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data: ' . mysqli_stmt_error($stmt)]);
}
mysqli_stmt_close($stmt);
?>