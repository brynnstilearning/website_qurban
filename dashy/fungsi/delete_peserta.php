<?php
// fungsi/delete_peserta.php (VERSI FINAL)
session_start();
include '../../koneksi.php';

header('Content-Type: application/json');

// Validasi hak akses
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak.']);
    exit;
}

if (!isset($_POST['id_peserta'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID Peserta tidak valid.']);
    exit;
}

$id_to_delete = $_POST['id_peserta'];

// Mulai transaksi untuk memastikan semua proses hapus berhasil atau tidak sama sekali
mysqli_begin_transaction($koneksi);

try {
    // 1. Hapus dari tabel `register` terlebih dahulu
    $stmt_register = mysqli_prepare($koneksi, "DELETE FROM register WHERE id_peserta = ?");
    mysqli_stmt_bind_param($stmt_register, "i", $id_to_delete);
    mysqli_stmt_execute($stmt_register);
    mysqli_stmt_close($stmt_register);

    // 2. Hapus dari tabel `data` (kupon QR)
    $stmt_data = mysqli_prepare($koneksi, "DELETE FROM data WHERE id_peserta = ?");
    mysqli_stmt_bind_param($stmt_data, "i", $id_to_delete);
    mysqli_stmt_execute($stmt_data);
    mysqli_stmt_close($stmt_data);

    // 3. Terakhir, hapus dari tabel `peserta`
    // Data dari tabel `keuangan` akan terhapus otomatis karena ada "ON DELETE CASCADE"
    $stmt_peserta = mysqli_prepare($koneksi, "DELETE FROM peserta WHERE id_peserta = ?");
    mysqli_stmt_bind_param($stmt_peserta, "i", $id_to_delete);
    mysqli_stmt_execute($stmt_peserta);
    
    // Cek apakah ada baris yang terhapus dari tabel peserta
    if (mysqli_stmt_affected_rows($stmt_peserta) > 0) {
        // Jika berhasil, commit transaksi
        mysqli_commit($koneksi);
        echo json_encode(['status' => 'success', 'message' => 'Data peserta dan semua data terkait berhasil dihapus!']);
    } else {
        // Jika tidak ada data yang terhapus (mungkin ID tidak ada), rollback
        mysqli_rollback($koneksi);
        echo json_encode(['status' => 'error', 'message' => 'Data peserta dengan ID tersebut tidak ditemukan.']);
    }
    mysqli_stmt_close($stmt_peserta);

} catch (mysqli_sql_exception $e) {
    // Jika terjadi error di salah satu query, rollback semua perubahan
    mysqli_rollback($koneksi);
    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan database: ' . $e->getMessage()]);
}
?>