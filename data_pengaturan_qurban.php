<?php
session_start();
// Pastikan hanya admin/panitia yang bisa mengakses
if (!isset($_SESSION['username']) || ($_SESSION['level'] !== 'admin' && $_SESSION['level'] !== 'panitia')) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

// Ambil data pengaturan qurban untuk ditampilkan dan diisi ke form modal
$query_pengaturan = mysqli_query($koneksi, "SELECT * FROM pengaturan_qurban WHERE id = 1");
$pengaturan_data = mysqli_fetch_assoc($query_pengaturan);

// Inisialisasi variabel dengan data yang ada atau string kosong jika tidak ada
$lokasi_penyembelihan = $pengaturan_data['lokasi_penyembelihan'] ?? '';
$tanggal_pengambilan = $pengaturan_data['tanggal_pengambilan'] ?? '';
$waktu_mulai = $pengaturan_data['waktu_mulai_pengambilan'] ?? '';
$waktu_selesai = $pengaturan_data['waktu_selesai_pengambilan'] ?? '';
$pesan_tambahan = $pengaturan_data['pesan_tambahan'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Informasi Qurban - QurbanKuy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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
        .dashboard-card { background-color: #201c1c; border: 3px solid #9fcf3f; color: #9fcf3f; padding: 2.5rem; max-width: 800px; border-radius: 20px; box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5); }
        .info-qurban-box { border-top: 1px solid #444; margin-top: 2rem; padding-top: 2rem; text-align: left; }
        .info-item { display: flex; margin-bottom: 0.75rem; }
        .info-label { color: #bdc3c7; font-weight: 600; width: 150px; flex-shrink: 0; }
        .info-value { color: #ffffff; }

        /* --- STYLE UNTUK MODAL TEMA GELAP --- */
        #pengaturanModal .modal-content {
            background-color: #201c1c;
            color: #9fcf3f;
            border: 2px solid #9fcf3f;
            border-radius: 15px;
        }
        #pengaturanModal .modal-header {
            border-bottom: 1px solid #444;
        }
        #pengaturanModal .modal-title {
            color: #9fcf3f;
        }
        #pengaturanModal .form-label {
            color: #bdc3c7;
        }
        #pengaturanModal .form-control,
        #pengaturanModal .form-select {
            background-color: #343a40;
            color: #f8f9fa;
            border-color: #6c757d;
        }
        #pengaturanModal .form-control:focus,
        #pengaturanModal .form-select:focus {
            background-color: #495057;
            color: #f8f9fa;
            border-color: #9fcf3f;
            box-shadow: 0 0 0 0.25rem rgba(159, 207, 63, 0.25);
        }
        #pengaturanModal .modal-footer {
            border-top: 1px solid #444;
        }
        #pengaturanModal .btn-primary {
            background-color: #9fcf3f;
            border-color: #9fcf3f;
            color: #201c1c;
            font-weight: bold;
        }
        #pengaturanModal .btn-primary:hover {
            background-color: #ffffff;
            border-color: #ffffff;
        }
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
    <div class="dashboard-card text-start">
        <h3 class="text-center mb-4"><i class="bi bi-gear-fill"></i> Pengaturan Informasi Qurban</h3>
        
        <div class="d-flex justify-content-between mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pengaturanModal">
                <i class="bi bi-pencil-square"></i> Edit Informasi
            </button>
            <a href="panitiadas.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left-circle"></i> Kembali
            </a>
        </div>

        <div class="info-qurban-box border-0 p-0 m-0">
             <div class="info-item">
                <span class="info-label">Lokasi:</span>
                <span class="info-value"><?= htmlspecialchars($lokasi_penyembelihan ?: 'Belum diatur') ?></span>
            </div>
             <div class="info-item">
                <span class="info-label">Tanggal:</span>
                <span class="info-value"><?= htmlspecialchars($tanggal_pengambilan ? date('d F Y', strtotime($tanggal_pengambilan)) : 'Belum diatur') ?></span>
            </div>
             <div class="info-item">
                <span class="info-label">Waktu:</span>
                <span class="info-value"><?= htmlspecialchars($waktu_mulai ? substr($waktu_mulai, 0, 5) : '') ?> - <?= htmlspecialchars($waktu_selesai ? substr($waktu_selesai, 0, 5) : '') ?> WIB</span>
            </div>
            <div class="info-item">
                <span class="info-label">Pesan:</span>
                <span class="info-value"><?= nl2br(htmlspecialchars($pesan_tambahan ?: 'Tidak ada pesan.')) ?></span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pengaturanModal" tabindex="-1" aria-labelledby="pengaturanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pengaturanModalLabel">Form Edit Informasi Qurban</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formPengaturan" action="simpan_pengaturan.php" method="POST">
            <div class="mb-3">
                <label for="lokasi_penyembelihan" class="form-label">Lokasi Penyembelihan</label>
                <input type="text" class="form-control" name="lokasi_penyembelihan" value="<?= htmlspecialchars($lokasi_penyembelihan) ?>" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_pengambilan" class="form-label">Tanggal Pengambilan Daging</label>
                <input type="date" class="form-control" name="tanggal_pengambilan" value="<?= htmlspecialchars($tanggal_pengambilan) ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="waktu_mulai_pengambilan" class="form-label">Waktu Mulai</label>
                    <input type="time" class="form-control" name="waktu_mulai_pengambilan" value="<?= htmlspecialchars($waktu_mulai) ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="waktu_selesai_pengambilan" class="form-label">Waktu Selesai</label>
                    <input type="time" class="form-control" name="waktu_selesai_pengambilan" value="<?= htmlspecialchars($waktu_selesai) ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="pesan_tambahan" class="form-label">Pesan Tambahan (Opsional)</label>
                <textarea class="form-control" name="pesan_tambahan" rows="3"><?= htmlspecialchars($pesan_tambahan) ?></textarea>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="formPengaturan">Simpan Informasi</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formPengaturan = document.getElementById('formPengaturan');
    const pengaturanModalElement = document.getElementById('pengaturanModal');
    const pengaturanModal = new bootstrap.Modal(pengaturanModalElement);

    formPengaturan.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                pengaturanModal.hide();
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi masalah saat mengirim data.', 'error');
        });
    });
});
</script>
</body>
</html>