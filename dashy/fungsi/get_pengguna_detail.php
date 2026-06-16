<?php
// fungsi/get_pengguna_detail.php
session_start();
include '../../koneksi.php';
header('Content-Type: application/json');

if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

if (!isset($_GET['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Username tidak disediakan.']);
    exit;
}

$username = $_GET['username'];
$stmt = mysqli_prepare($koneksi, "SELECT * FROM register WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($data = mysqli_fetch_assoc($result)) {
    echo json_encode(['status' => 'success', 'data' => $data]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data pengguna tidak ditemukan.']);
}
mysqli_stmt_close($stmt);
?>