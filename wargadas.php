<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['id_peserta'])) {
    header("Location: login.php");
    exit();
}

// ... (Bagian logika PHP Anda untuk mengambil data tetap sama) ...
$username_display = htmlspecialchars($_SESSION['username'] ?? 'Peserta Qurban');
$id_peserta = $koneksi->real_escape_string($_SESSION['id_peserta']);
$query = "SELECT qr_token, is_used FROM data WHERE id_peserta = '$id_peserta'";
$result = $koneksi->query($query);
$qr_token = "Belum ada QR Code untuk Anda.";
$is_used = 0;
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $qr_token = $row['qr_token'];
    $is_used = $row['is_used'];
}
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Peserta Qurban</title>
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
<body>

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
        <p class="welcome-text"><?= $username_display ?></p>

        <?php if (str_starts_with($qr_token, 'qr_')): ?>
            <img class="qr-img" src="https://api.qrserver.com/v1/create-qr-code/?data=<?php echo urlencode($qr_token); ?>&size=220x220" alt="QR Code">
            <div class="mt-3">
                <?php if ($is_used == 0): ?>
                    <a href="download_qr.php?file=<?php echo urlencode($qr_token) . '.png'; ?>" class="btn btn-download"><i class="bi bi-download me-2"></i>Download QR</a>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled><i class="bi bi-check-circle-fill me-2"></i>QR Sudah Digunakan</button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning mt-4"><?php echo htmlspecialchars($qr_token); ?></div>
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