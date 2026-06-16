<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Lupakan login untuk sementara, tapi koneksi tetap dibutuhkan
include '../koneksi.php';

// Tentukan halaman yang akan dimuat. Halaman default adalah 'dashboard'.
$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Qurban App</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
        }
        body {
            background-color: #f4f7f6;
            display: flex;
            font-family: 'Segoe UI', sans-serif;
        }
        /* Sidebar Styles (Mirip dengan contoh dashboard.php) */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2c3e50; /* Warna dari contoh dashboard.php */
            padding: 1.5rem 1rem;
            z-index: 1030;
        }
        .sidebar-header {
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 2rem;
            text-align: center;
        }
        .sidebar-header i {
            color: #27ae60; /* Warna dari contoh dashboard.php */
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li a {
            color: #bdc3c7;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: background-color 0.2s, color 0.2s;
        }
        .sidebar-menu li.active a, .sidebar-menu li a:hover {
            background-color: #34495e;
            color: #fff;
        }
        .sidebar-menu li a i {
            margin-right: 1rem;
            font-size: 1.2rem;
            width: 20px;
        }
        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 0;
        }
        .main-content .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            color: #34495e;
        }
        .main-content .content-fluid {
            padding: 2rem;
        }
        /* Responsive toggle (akan kita aktifkan jika diperlukan nanti) */
        #sidebar-toggle { display: none; }
        @media (max-width: 992px) {
            /* ... (bisa ditambahkan nanti) ... */
        }
    </style>
</head>

<body>
    <aside class="sidebar" id="sidebar">
        <h1 class="sidebar-header">
            <i class="bi bi-house-heart-fill"></i> Qurban App
        </h1>

        <ul class="sidebar-menu">
            <li class="<?= ($page === 'dashboard') ? 'active' : '' ?>">
                <a href="admindas.php?page=dashboard"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            </li>
            <li class="<?= ($page === 'warga') ? 'active' : '' ?>">
                <a href="admindas.php?page=warga"><i class="bi bi-people-fill"></i> Kelola Warga</a>
            </li>
             <li class="<?= ($page === 'peserta') ? 'active' : '' ?>">
                <a href="admindas.php?page=peserta"><i class="bi bi-person-lines-fill"></i> Kelola Peserta</a>
            </li>
            <li class="<?= ($page === 'pengguna') ? 'active' : '' ?>">
                <a href="admindas.php?page=pengguna"><i class="bi bi-person-plus-fill"></i> Kelola Pengguna</a>
            </li>
            <li class="<?= ($page === 'keuangan') ? 'active' : '' ?>">
                <a href="admindas.php?page=keuangan"><i class="bi bi-cash-coin"></i> Kelola Keuangan</a>
            </li>
            <li class="<?= ($page === 'pembagian') ? 'active' : '' ?>">
                <a href="admindas.php?page=pembagian"><i class="bi bi-box2-heart-fill"></i> Pembagian Daging</a>
            </li>
            <li class="<?= ($page === 'qrcode') ? 'active' : '' ?>">
                <a href="admindas.php?page=qrcode"><i class="bi bi-qr-code-scan"></i> Kelola QR Code</a>
            </li>
             <li>
                <a href="../act_login.php?op=out"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </li>
        </ul>
    </aside>

    <div class="main-content" id="main-content">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <span class="navbar-brand h1 mb-0 text-capitalize"><?= str_replace('_', ' ', $page) ?></span>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item">
                        <span class="nav-link">Selamat Datang, Admin!</span>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="content-fluid">
            <?php
            // Mekanisme untuk memuat konten halaman
            switch ($page) {
                case 'warga':
                    include '_page_warga.php';
                    break;
                case 'peserta':
                    include '_page_peserta.php';
                    break;
                case 'pengguna':
                    include '_page_pengguna.php';
                    break;
                case 'keuangan':
                    include '_page_keuangan.php';
                    break;
                case 'pembagian':
                    include '_page_pembagian.php';
                    break;
                case 'qrcode':
                    include '_page_qrcode.php';
                    break;
                case 'dashboard':
                default:
                    include '_page_dashboard.php';
                    break;
            }
            ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>