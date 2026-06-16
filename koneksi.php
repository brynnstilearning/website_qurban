<?php
$host     = "localhost";
$user     = "root";
$password = ""; // Jika pakai Laragon biasanya kosong
$database = "qurban"; // Ganti dengan nama database kamu

$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
