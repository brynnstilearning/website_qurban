<?php
include 'koneksi.php';

// Ambil semua data
$id_peserta = $_POST['id_peserta'] !== '' ? $_POST['id_peserta'] : null;
$jenis = $_POST['jenis'];
$id_perlengkapan = $_POST['id_perlengkapan'] !== '' ? $_POST['id_perlengkapan'] : null;
$keterangan = $_POST['keterangan'];
$jumlah = $_POST['jumlah'];
$tanggal = $_POST['tanggal'];

// Gunakan prepared statement
$stmt = $koneksi->prepare("INSERT INTO keuangan (id_peserta, jenis, id_perlengkapan, keterangan, jumlah, tanggal)
                           VALUES (?, ?, ?, ?, ?, ?)");

// Tipe data bind: i = int, s = string
$stmt->bind_param("isssis", $id_peserta, $jenis, $id_perlengkapan, $keterangan, $jumlah, $tanggal);

// Jalankan
if ($stmt->execute()) {
    header("Location: data_keuangan.php?status=success");
    exit;
} else {
    echo "Gagal menyimpan data: " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>
