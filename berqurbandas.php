<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['id_peserta']) || $_SESSION['level'] !== 'berqurban') {
    header("Location: login.php");
    exit();
}

$id_peserta = $koneksi->real_escape_string($_SESSION['id_peserta']);
$username = $_SESSION['username'] ?? 'Pengqurban';

// Ambil data QR dan nama
$query_peserta = "SELECT qr_token, is_used, name FROM data WHERE id_peserta = '$id_peserta'";
$result_peserta = $koneksi->query($query_peserta);
$qr_token = "Belum ada QR Code untuk Anda.";
$is_used = 0;

if ($result_peserta && $result_peserta->num_rows > 0) {
    $row = $result_peserta->fetch_assoc();
    $qr_token = $row['qr_token'];
    $is_used = $row['is_used'];
}

// Hitung total iuran
$q = $koneksi->query("SELECT SUM(jumlah) as total FROM keuangan WHERE id_peserta='$id_peserta' AND jenis='pemasukan' AND keterangan LIKE '%iuran qurban%'");
$total_iuran_dibayar = ($q && $row = $q->fetch_assoc()) ? $row['total'] : 0;

// Deteksi jenis hewan qurban dan status iuran
$jenis_qurban = '';
$target_iuran = 0;
$sisa_pembayaran = 0;
$status_iuran_text = "Belum ada pembayaran iuran.";

$q_jenis = $koneksi->query("SELECT keterangan FROM keuangan WHERE id_peserta='$id_peserta' AND jenis='pemasukan' AND keterangan LIKE '%iuran qurban%' LIMIT 1");
if($q_jenis && $row_jenis = $q_jenis->fetch_assoc()){
    if(str_contains(strtolower($row_jenis['keterangan']), 'sapi')){
        $jenis_qurban = 'Sapi';
        $target_iuran = 3000000;
    } elseif(str_contains(strtolower($row_jenis['keterangan']), 'kambing')){
        $jenis_qurban = 'Kambing';
        $target_iuran = 2700000;
    }
}

if($target_iuran > 0){
    if($total_iuran_dibayar >= $target_iuran){
        $status_iuran_text = "Lunas untuk Qurban " . ucfirst($jenis_qurban);
    } else {
        $sisa_pembayaran = $target_iuran - $total_iuran_dibayar;
        $status_iuran_text = "Belum Lunas (" . ucfirst($jenis_qurban) . ") - Sisa: Rp " . number_format($sisa_pembayaran, 0, ',', '.');
    }
}

// Cek Status Pembelian Hewan
// $status_hewan_text = "Menunggu Pelunasan Iuran Anda";
// if ($sisa_pembayaran <= 0 && $jenis_qurban != '') {
//     $hewan_query = "SELECT COUNT(*) as total FROM keuangan 
//         WHERE jenis='pengeluaran' AND 
//         keterangan LIKE '%pembelian hewan " . strtolower($jenis_qurban) . "%'";
//     $r_hewan = $koneksi->query($hewan_query);
//     $hewan_dibeli = ($r_hewan && $row_hewan = $r_hewan->fetch_assoc()) ? $row_hewan['total'] > 0 : false;
//     $status_hewan_text = "Hewan Qurban ($jenis_qurban) " . ($hewan_dibeli ? "Sudah Dibeli Panitia" : "Dalam Proses Pembelian");
// }

$status_hewan_text = "Menunggu Pelunasan Iuran Anda"; 

if ($sisa_pembayaran <= 0 && $jenis_qurban != '') { 
    // Memeriksa apakah sapi sudah dibeli secara umum (total harga sapi 21.000.000).
    $query_pengeluaran_sapi_umum = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM keuangan WHERE keterangan LIKE '%pembelian hewan qurban sapi%' AND jenis = 'pengeluaran' AND jumlah >= 21000000");
    $has_sapi_purchased_general = ($query_pengeluaran_sapi_umum && mysqli_fetch_assoc($query_pengeluaran_sapi_umum)['count'] > 0);

    // Memeriksa apakah kambing sudah dibeli secara umum (harga kambing 2.700.000).
    $query_pengeluaran_kambing_umum = mysqli_query($koneksi, "SELECT COUNT(*) as count FROM keuangan WHERE keterangan LIKE '%pembelian hewan qurban kambing%' AND jenis = 'pengeluaran' AND jumlah >= 2700000"); 
    $has_kambing_purchased_general = ($query_pengeluaran_kambing_umum && mysqli_fetch_assoc($query_pengeluaran_kambing_umum)['count'] > 0);

    // Menentukan status hewan berdasarkan jenis hewan peserta dan status pembelian umum.
    if ($jenis_qurban == 'Sapi') {
        if ($has_sapi_purchased_general) {
            $status_hewan_text = "Hewan Qurban (Sapi) Sudah Dibeli";
        } else {
            $status_hewan_text = "Hewan Qurban (Sapi) Dalam Proses Pembelian";
        }
    } elseif ($jenis_qurban == 'Kambing') {
        if ($has_kambing_purchased_general) {
            $status_hewan_text = "Hewan Qurban (Kambing) Sudah Dibeli";
        } else {
            $status_hewan_text = "Hewan Qurban (Kambing) Dalam Proses Pembelian";
        }
    }
}

// --- PHP BARU: Ambil data pengaturan qurban ---
$query_info_qurban = mysqli_query($koneksi, "SELECT * FROM pengaturan_qurban WHERE id = 1");
$info_qurban = mysqli_fetch_assoc($query_info_qurban);

$lokasi_penyembelihan = $info_qurban['lokasi_penyembelihan'] ?? 'Informasi belum tersedia';
$tanggal_pengambilan = isset($info_qurban['tanggal_pengambilan']) ? date('d F Y', strtotime($info_qurban['tanggal_pengambilan'])) : 'Informasi belum tersedia';
$waktu_mulai = isset($info_qurban['waktu_mulai_pengambilan']) ? substr($info_qurban['waktu_mulai_pengambilan'], 0, 5) : '';
$waktu_selesai = isset($info_qurban['waktu_selesai_pengambilan']) ? substr($info_qurban['waktu_selesai_pengambilan'], 0, 5) : '';
$waktu_pengambilan = ($waktu_mulai && $waktu_selesai) ? "$waktu_mulai - $waktu_selesai WIB" : 'Informasi belum tersedia';
$pesan_tambahan = $info_qurban['pesan_tambahan'] ?? '';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pengqurban - QurbanKuy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        <style>
        /* ... (CSS untuk body, header, .content-wrapper tetap sama) ... */
        html, body { height: 100%; margin: 0; }
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            flex-direction: column;
        }
        .header { background-color: #201c1c; color: #9fcf3f; padding: 15px 40px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 2px 5px rgba(0,0,0,0.2); flex-shrink: 0; }
        .header .logo-container { display: flex; align-items: center; }
        .header .logo-container img { width: 60px; margin-right: 15px; }
        .header .logo-container h1 { font-size: 2rem; font-weight: bold; color: #9fcf3f; margin: 0; }
        .header .logout-btn { font-weight: bold; text-decoration: none; background: #9fcf3f; color: #201c1c; padding: 10px 18px; border-radius: 8px; transition: all 0.3s ease; }
        .header .logout-btn:hover { background-color: #ffffff; color: #201c1c; }
        .content-wrapper { flex: 1 0 auto; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .dashboard-card { background-color: #201c1c; border: 3px solid #9fcf3f; color: #9fcf3f; padding: 2.5rem; max-width: 550px; width: 100%; border-radius: 20px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5); text-align: center; }
        .dashboard-card h2 { color: #9fcf3f; font-weight: 600; }
        .dashboard-card .welcome-text { color: #e0e0e0; }

        /* --- PERUBAHAN CSS DI SINI --- */

        .dashboard-card .qr-img {
            border: 5px solid #9fcf3f;
            border-radius: 0; /* 1. Sudut dibuat tajam (tidak rounded) */
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem; /* 3. Menambah jarak ke bawah */
        }

        .dashboard-card .qr-token-text {
            font-family: 'Courier New', Courier, monospace;
            background-color: #343a40;
            color: #9fcf3f;
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block; /* 2. Membuat background pas dengan lebar teks */
            word-break: break-all;
        }

        /* --- Sisa CSS tidak berubah --- */
        .dashboard-card .btn-download { background-color: #9fcf3f; color: #201c1c; border: none; font-weight: bold; }
        .dashboard-card .btn-download:hover { background-color: #ffffff; }
        .info-qurban-box { border-top: 1px solid #444; margin-top: 2rem; padding-top: 2rem; text-align: left; max-width: 400px; margin-left: auto; margin-right: auto; }
        .info-qurban-box h3 { color: #9fcf3f; text-align: center; }
        .info-label { color: #bdc3c7; font-weight: 600; width: 100px; flex-shrink: 0; }
        .info-value { color: #ffffff; }
        .info-item { display: flex; margin-bottom: 0.75rem; }
    </style>
</head>
<body class="body-dashboard">

<header class="header">
    <div class="logo-container">
        <img src="images/logo2.png" alt="Logo QurbanKuy">
        <h1>QurbanKuy</h1>
    </div>
    <a href="act_login.php?op=out" class="logout-btn">Logout</a>
</header>

<div class="content-wrapper">
    <div class="dashboard-card">
        <h2>Selamat Datang</h2>
        <p class="welcome-text"><?= htmlspecialchars($username) ?></p>

        <div class="alert alert-success" style="background-color: #343a40; color: #9fcf3f; border-color: #9fcf3f;">
            <i class="bi bi-cash-coin me-2"></i>
            <strong>Status Iuran:</strong> <?= $status_iuran_text ?>
        </div>
         <div class="alert alert-warning" style="background-color: #343a40; color: #9fcf3f; border-color: #9fcf3f;">
            <i class="bi bi-clipboard2-check me-2"></i>
            <strong>Status Hewan:</strong> <?= $status_hewan_text ?>
        </div>

        <?php if ($qr_token && str_starts_with($qr_token, 'qr_')): ?>
            <div class="mt-4">
                <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?data=<?= urlencode($qr_token); ?>&size=200x200" alt="QR Code">
                <div class="mt-3">
                    <?php if ($is_used == 0): ?>
                        <a href="download_qr.php?file=<?= urlencode($qr_token) ?>.png" class="btn btn-primary btn-download">
                            <i class="bi bi-download"></i> Download QR
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled><i class="bi bi-lock"></i> QR Sudah Digunakan</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-dark mt-4"><?= htmlspecialchars($qr_token) ?></div>
        <?php endif; ?>
        
        <div class="info-qurban-box">
            <h3><i class="bi bi-info-circle-fill me-2"></i> Informasi Pengambilan</h3>
            <div class="info-item">
                <span class="info-label">Lokasi:</span>
                <span class="info-value"><?= htmlspecialchars($lokasi_penyembelihan) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal:</span>
                <span class="info-value"><?= htmlspecialchars($tanggal_pengambilan) ?></span>
            </div>
            <div class="info-item">
                <strong class="info-label">Waktu:</strong>
                <span class="info-value"><?= htmlspecialchars($waktu_pengambilan) ?></span>
            </div>
            <?php if (!empty($pesan_tambahan)): ?>
            <div class="info-item">
                <strong class="info-label">Pesan:</strong>
                <span class="info-value"><?= nl2br(htmlspecialchars($pesan_tambahan)) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>