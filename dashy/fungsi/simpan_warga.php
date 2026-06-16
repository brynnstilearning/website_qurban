<?php
// fungsi/simpan_warga.php
include '../../koneksi.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $pekerjaan = $_POST['pekerjaan'];

    // Ambil data tambahan dari form
    $agama = $_POST['agama'];
    $status_perkawinan = $_POST['status_perkawinan'];
    $kewarganegaraan = $_POST['kewarganegaraan'];
    
    $old_nik = $_POST['old_nik']; // NIK lama untuk identifikasi proses update

    if (empty($old_nik)) {
        // --- PROSES INSERT DATA BARU ---
        // (tambahkan kolom baru)
        $sql = "INSERT INTO warga (nik, nama, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, agama, status_perkawinan, pekerjaan, kewarganegaraan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $sql);
        // (tambahkan tipe data baru 's' sebanyak 3 kali)
        mysqli_stmt_bind_param($stmt, "ssssssssss", $nik, $nama, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $agama, $status_perkawinan, $pekerjaan, $kewarganegaraan);
        $message = "Data warga berhasil disimpan!";

    } else {
        // --- PROSES UPDATE DATA LAMA ---
        // (tambahkan kolom baru di klausa SET)
        $sql = "UPDATE warga SET nik = ?, nama = ?, tempat_lahir = ?, tanggal_lahir = ?, jenis_kelamin = ?, alamat = ?, agama = ?, status_perkawinan = ?, pekerjaan = ?, kewarganegaraan = ? WHERE nik = ?";
        $stmt = mysqli_prepare($koneksi, $sql);
        // (tambahkan tipe data baru 's' sebanyak 3 kali dan 1 's' untuk old_nik)
        mysqli_stmt_bind_param($stmt, "sssssssssss", $nik, $nama, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $agama, $status_perkawinan, $pekerjaan, $kewarganegaraan, $old_nik);
        $message = "Data warga berhasil diperbarui!";
    }

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success', 'message' => $message]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . mysqli_error($koneksi)]);
    }
    mysqli_stmt_close($stmt);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid.']);
}
?>