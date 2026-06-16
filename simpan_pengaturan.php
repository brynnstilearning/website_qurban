<?php
session_start();
// Pastikan hanya admin/panitia yang bisa mengakses
if (!isset($_SESSION['username']) || ($_SESSION['level'] !== 'admin' && $_SESSION['level'] !== 'panitia')) {
    // Mengembalikan error JSON karena ini adalah target AJAX
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

// Path ke koneksi.php sekarang langsung
include "koneksi.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lokasi = $_POST['lokasi_penyembelihan'];
    $tanggal = $_POST['tanggal_pengambilan'];
    $waktu_mulai = $_POST['waktu_mulai_pengambilan'];
    $waktu_selesai = $_POST['waktu_selesai_pengambilan'];
    $pesan = $_POST['pesan_tambahan'];

    // Validasi dasar
    if (empty($lokasi) || empty($tanggal) || empty($waktu_mulai) || empty($waktu_selesai)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field wajib (kecuali Pesan Tambahan) harus diisi!']);
        exit;
    }

    $check_query = mysqli_query($koneksi, "SELECT id FROM pengaturan_qurban WHERE id = 1");
    
    // Gunakan prepared statement untuk keamanan
    if (mysqli_num_rows($check_query) > 0) {
        $sql = "UPDATE pengaturan_qurban SET lokasi_penyembelihan=?, tanggal_pengambilan=?, waktu_mulai_pengambilan=?, waktu_selesai_pengambilan=?, pesan_tambahan=? WHERE id = 1";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $lokasi, $tanggal, $waktu_mulai, $waktu_selesai, $pesan);
        $message = "Informasi qurban berhasil diperbarui!";
    } else {
        $sql = "INSERT INTO pengaturan_qurban (id, lokasi_penyembelihan, tanggal_pengambilan, waktu_mulai_pengambilan, waktu_selesai_pengambilan, pesan_tambahan) VALUES (1, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "sssss", $lokasi, $tanggal, $waktu_mulai, $waktu_selesai, $pesan);
        $message = "Informasi qurban berhasil disimpan!";
    }
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => $message]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_stmt_error($stmt)]);
    }
    mysqli_stmt_close($stmt);

} else {
     echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
}
?>