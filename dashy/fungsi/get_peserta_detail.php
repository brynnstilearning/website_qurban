<?php
// fungsi/get_peserta_detail.php
include '../../koneksi.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID Peserta tidak disediakan.']);
    exit;
}

$id = $_GET['id'];
$stmt = mysqli_prepare($koneksi, "
    SELECT p.*, w.nama 
    FROM peserta p 
    LEFT JOIN warga w ON p.nik = w.nik 
    WHERE p.id_peserta = ?
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($data = mysqli_fetch_assoc($result)) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data peserta tidak ditemukan.']);
}

mysqli_stmt_close($stmt);
?>