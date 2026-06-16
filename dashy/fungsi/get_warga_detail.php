<?php
// fungsi/get_warga_detail.php
include '../../koneksi.php';

header('Content-Type: application/json');

if (!isset($_GET['nik'])) {
    echo json_encode(['status' => 'error', 'message' => 'NIK tidak disediakan.']);
    exit;
}

$nik = $_GET['nik'];
$stmt = mysqli_prepare($koneksi, "SELECT * FROM warga WHERE nik = ?");
mysqli_stmt_bind_param($stmt, "s", $nik);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($data = mysqli_fetch_assoc($result)) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data warga tidak ditemukan.']);
}

mysqli_stmt_close($stmt);
?>