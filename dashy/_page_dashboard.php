<?php
// PHP logic untuk mengambil data tetap sama, memastikan semua variabel tersedia.
// Ambil data total warga dan peserta
$totalWarga = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM warga"))['total'] ?? 0;
$totalPeserta = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM peserta"))['total'] ?? 0;

// Ambil data keuangan dari tabel 'saldo'
$saldo_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM saldo WHERE id = 1"));

// Inisialisasi variabel keuangan dengan fallback 0
$totalIuranHewan = $saldo_data['total_iuranhewan'] ?? 0;
$totalPengeluaranHewan = $saldo_data['total_pengeluaranhewan'] ?? 0;
$saldoBersihIuranHewan = $saldo_data['saldobersih_iuranhewan'] ?? 0;
$totalBiayaAdministrasi = $saldo_data['total_biayaadministrasi'] ?? 0;
$totalPengeluaranAdministrasi = $saldo_data['total_pengeluaranadministrasi'] ?? 0;
$saldoBersihAdministrasi = $saldo_data['saldobersih_administrasi'] ?? 0;
$sisaSaldoBersihKeseluruhan = $saldo_data['saldobersih_keseluruhan'] ?? 0;
?>

<style>
    .stat-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        display: flex; flex-direction: column; height: 100%;
        animation: slideUp 0.6s ease-out forwards; opacity: 0;
    }
    .stat-card-header { display: flex; align-items: center; margin-bottom: 15px; }
    .stat-card-icon { font-size: 2rem; width: 50px; height: 50px; display: grid; place-items: center; border-radius: 50%; margin-right: 15px; color: #fff; }
    .stat-card-title { font-size: 1rem; font-weight: 500; color: #6c757d; }
    .stat-card-value { font-size: 1.75rem; font-weight: 700; color: #343a40; margin: 0; }
    .stat-card-detail { font-size: 0.85rem; color: #6c757d; margin-top: auto; }
    .icon-success { background-color: #28a745; } .icon-info { background-color: #17a2b8; } .icon-warning { background-color: #ffc107; }
    .icon-primary { background-color: #0d6efd; } .icon-secondary { background-color: #6c757d; }
    @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>


<h3 class="text-center" class="mb-4">Ringkasan Keuangan</h3>
<div class="row g-4 mb-5">
    <div class="col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-warning"><i class="fas fa-wallet"></i></div>
                <span class="stat-card-title">Saldo Bersih Keseluruhan</span>
            </div>
            <h2 class="stat-card-value">Rp <?= number_format($sisaSaldoBersihKeseluruhan, 0, ',', '.'); ?></h2>
            <p class="stat-card-detail mt-2">Total dana yang tersedia dari semua pos.</p>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-success"><i class="fas fa-hand-holding-dollar"></i></div>
                <span class="stat-card-title">Saldo Iuran Hewan</span>
            </div>
            <h3 class="stat-card-value">Rp <?= number_format($saldoBersihIuranHewan, 0, ',', '.'); ?></h3>
            <p class="stat-card-detail">Pemasukan: Rp <?= number_format($totalIuranHewan, 0, ',', '.'); ?><br>Pengeluaran: Rp <?= number_format($totalPengeluaranHewan, 0, ',', '.'); ?></p>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
            <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-info"><i class="fas fa-cash-register"></i></div>
                <span class="stat-card-title">Saldo Biaya Administrasi</span>
            </div>
            <h3 class="stat-card-value">Rp <?= number_format($saldoBersihAdministrasi, 0, ',', '.'); ?></h3>
            <p class="stat-card-detail">Pemasukan: Rp <?= number_format($totalBiayaAdministrasi, 0, ',', '.'); ?><br>Pengeluaran: Rp <?= number_format($totalPengeluaranAdministrasi, 0, ',', '.'); ?></p>
        </div>
    </div>
</div>

<h3 class="text-center" class="mb-4">Statistik Populasi</h3>
<div class="row g-4 mb-5">
        <div class="col-lg-6 col-md-6">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-primary"><i class="fas fa-users"></i></div>
                <span class="stat-card-title">Total Warga Terdaftar</span>
            </div>
            <h3 class="stat-card-value"><?= number_format($totalWarga); ?></h3>
            <p class="stat-card-detail">Jumlah seluruh warga dalam sistem.</p>
        </div>
    </div>
    <div class="col-lg-6 col-md-6">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon icon-secondary"><i class="fas fa-user-check"></i></div>
                <span class="stat-card-title">Total Peserta Qurban</span>
            </div>
            <h3 class="stat-card-value"><?= number_format($totalPeserta); ?></h3>
            <p class="stat-card-detail">Jumlah peserta yang terdaftar qurban tahun ini.</p>
        </div>
    </div>
</div>