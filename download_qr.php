<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['id_peserta'])) {
    header("Location: login.php");
    exit();
}

$id_peserta = $_SESSION['id_peserta'];

if (!isset($_GET['file'])) {
    die('File QR tidak ditentukan!');
}

$filename = basename($_GET['file']);  // contoh: qr_683d971144ce78.46735080.png
$foldername = 'qrcodes';
$filepath = $foldername . '/' . $filename;

// Cek apakah file ada
if (!file_exists($filepath)) {
    die('File QR tidak ditemukan!');
}

// Cek apakah qr_token sesuai di DB dan apakah sudah dipakai
$query = $koneksi->prepare("SELECT is_used FROM data WHERE id_peserta = ? AND CONCAT(qr_token, '.png') = ?");
$query->bind_param('ss', $id_peserta, $filename);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    die('QR Code tidak valid untuk pengguna ini!');
}

$row = $result->fetch_assoc();

if ($row['is_used'] == 1) {
    die('QR Code sudah pernah diunduh sebelumnya!');
}

if (ob_get_level()) {
    ob_end_clean();
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

readfile($filepath);

$update = $koneksi->prepare("UPDATE data SET is_used = 1 WHERE id_peserta = ?");
$update->bind_param('s', $id_peserta);
$update->execute();

exit;