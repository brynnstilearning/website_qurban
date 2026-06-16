<?php
// fungsi/simpan_keuangan.php
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

$id_peserta = !empty($_POST['id_peserta']) ? (int)$_POST['id_peserta'] : NULL;
$jenis = $_POST['jenis'] ?? '';
$id_perlengkapan = !empty($_POST['id_perlengkapan']) ? (int)$_POST['id_perlengkapan'] : NULL;
$keterangan = $_POST['keterangan'] ?? '';
$jumlah = $_POST['jumlah'] ?? 0;
$tanggal = $_POST['tanggal'] ?? date('Y-m-d');

if (empty($jenis) || empty($keterangan) || empty($jumlah) || empty($tanggal)) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap. Jenis, keterangan, jumlah, dan tanggal wajib diisi.']);
    exit;
}

// Jika jenisnya pengeluaran, id_peserta harus NULL
if ($jenis === 'pengeluaran') {
    $id_peserta = NULL;
}
// Jika jenisnya pemasukan, id_perlengkapan harus NULL
if ($jenis === 'pemasukan') {
    $id_perlengkapan = NULL;
}


$sql = "INSERT INTO keuangan (id_peserta, jenis, id_perlengkapan, keterangan, jumlah, tanggal) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($koneksi, $sql);

// Bind parameter. Tipe data: integer, string, integer, string, integer, string
mysqli_stmt_bind_param($stmt, "isisis", $id_peserta, $jenis, $id_perlengkapan, $keterangan, $jumlah, $tanggal);

if (mysqli_stmt_execute($stmt)) {
    // Trigger di database Anda akan otomatis menghitung ulang saldo
    echo json_encode(['status' => 'success', 'message' => 'Data keuangan berhasil disimpan!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Eksekusi query gagal: ' . mysqli_stmt_error($stmt)]);
}

mysqli_stmt_close($stmt);
?>