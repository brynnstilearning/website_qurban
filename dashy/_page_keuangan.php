<?php
// --- AMBIL DATA ADMIN YANG SEDANG LOGIN (SANGAT PENTING UNTUK DISPLAY PENGELUARAN ADMIN) ---
$admin_name = 'N/A'; // Default jika tidak ditemukan
$admin_nik = 'N/A';  // Default jika tidak ditemukan

if (isset($_SESSION['username']) && $_SESSION['level'] === 'admin') {
    // Query ini mengambil nama dan NIK admin dari tabel 'register'
    // Sesuaikan nama tabel dan kolom jika berbeda di database Anda
    $query_admin_info = mysqli_query($koneksi, "SELECT namalengkap, nik FROM register WHERE username = '{$_SESSION['username']}' AND level = 'admin' LIMIT 1");
    if ($query_admin_info && mysqli_num_rows($query_admin_info) > 0) {
        $admin_data_session = mysqli_fetch_assoc($query_admin_info);
        $admin_name = $admin_data_session['namalengkap'];
        $admin_nik = $admin_data_session['nik'];
    }
}
// --- AKHIR BAGIAN PENGAMBILAN DATA ADMIN ---


// Query untuk menampilkan data di tabel
// Saya sesuaikan agar mengambil w.nik sebagai 'warga_nik'
$query_keuangan = mysqli_query($koneksi, "
    SELECT 
        k.*, 
        p.nik AS peserta_nik,  -- NIK dari tabel peserta
        w.nama AS nama_warga, 
        w.nik AS warga_nik,    -- NIK dari tabel warga (ini yang akan kita pakai di tampilan)
        perl.nama_perlengkapan 
    FROM keuangan k
    LEFT JOIN peserta p ON k.id_peserta = p.id_peserta
    LEFT JOIN warga w ON p.nik = w.nik
    LEFT JOIN perlengkapan perl ON k.id_perlengkapan = perl.id_perlengkapan
    ORDER BY k.id_keuangan ASC
");

// Query untuk mengisi dropdown 'Peserta' di dalam form modal
$query_peserta = mysqli_query($koneksi, "
    SELECT peserta.id_peserta, peserta.nik, peserta.level, warga.nama 
    FROM peserta 
    JOIN warga ON peserta.nik = warga.nik 
    WHERE peserta.level = 'berqurban' OR peserta.level = 'Admin'
    ORDER BY warga.nama ASC
");

// Query untuk mengisi dropdown 'Perlengkapan' di dalam form modal
$query_perlengkapan = mysqli_query($koneksi, "SELECT * FROM perlengkapan ORDER BY nama_perlengkapan ASC");

// Simpan data perlengkapan ke array untuk digunakan oleh JavaScript
$perlengkapan_array = [];
if ($query_perlengkapan) {
    while ($row = mysqli_fetch_assoc($query_perlengkapan)) {
        $perlengkapan_array[] = $row;
    }
}
?>

<h3 class="text-center"><i class="bi bi-cash-coin"></i> Rekap Data Keuangan Qurban</h3>

<div class="mb-3">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#keuanganModal">
      <i class="bi bi-plus-circle"></i> Tambah Data Keuangan
    </button>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>ID</th>
                <th>Peserta</th>
                <th>NIK</th> <th>Jenis</th>
                <th>Keterangan</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($query_keuangan)) : ?>
                <tr>
                    <td class="text-center"><?= htmlspecialchars($row['id_keuangan']) ?></td>
                    <td>
                        <?php 
                        // Logika untuk menampilkan nama peserta/admin
                        // Jika jenis pengeluaran DAN id_peserta NULL/kosong
                        if ($row['jenis'] === 'pengeluaran' && (empty($row['id_peserta']) || $row['id_peserta'] === '0')) {
                            echo htmlspecialchars($admin_name); 
                        } else {
                            echo htmlspecialchars($row['nama_warga'] ?? '-'); 
                        }
                        ?>
                    </td>
                    <td> <?php 
                        // Logika untuk menampilkan NIK peserta/admin
                        // Jika jenis pengeluaran DAN id_peserta NULL/kosong
                        if ($row['jenis'] === 'pengeluaran' && (empty($row['id_peserta']) || $row['id_peserta'] === '0')) {
                            echo htmlspecialchars($admin_nik); 
                        } else {
                            echo htmlspecialchars($row['warga_nik'] ?? '-'); // Menggunakan 'warga_nik' dari query
                        }
                        ?>
                    </td>
                    <td class="text-center">
                        <span class="badge <?= $row['jenis'] === 'pemasukan' ? 'bg-success' : 'bg-danger' ?>">
                            <?= ucfirst($row['jenis']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td class="text-end">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                    <td class="text-center"><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="keuanganModal" tabindex="-1" aria-labelledby="keuanganModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="keuanganModalLabel">Form Input Keuangan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formKeuangan" action="fungsi/simpan_keuangan.php" method="POST">
            <div class="mb-3">
                <label for="id_peserta" class="form-label">Peserta</label>
                <select name="id_peserta" id="id_peserta" class="form-select">
                    <option value="">-- Pilih Peserta (jika pemasukan iuran) --</option>
                    <?php mysqli_data_seek($query_peserta, 0); ?>
                    <?php while ($row = mysqli_fetch_assoc($query_peserta)) : ?>
                        <option value="<?= htmlspecialchars($row['id_peserta']); ?>">
                            ID: <?= htmlspecialchars($row['id_peserta']); ?> - <?= htmlspecialchars($row['nama']); ?> - NIK: <?= htmlspecialchars($row['nik']); ?> - Level: <?= ucfirst(htmlspecialchars($row['level'])); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="jenis" class="form-label">Jenis Transaksi</label>
                <select name="jenis" id="jenis" class="form-select" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="pemasukan">Pemasukan</option>
                    <option value="pengeluaran">Pengeluaran</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="id_perlengkapan" class="form-label">Perlengkapan (jika pengeluaran)</label>
                <select name="id_perlengkapan" id="id_perlengkapan" class="form-select">
                    <option value="">-- Pilih Perlengkapan --</option>
                    <?php foreach ($perlengkapan_array as $row_perlengkapan): ?>
                        <option value="<?= htmlspecialchars($row_perlengkapan['id_perlengkapan']); ?>" data-harga="<?= htmlspecialchars($row_perlengkapan['harga']); ?>">
                           <?= htmlspecialchars($row_perlengkapan['nama_perlengkapan']) . " (Rp " . number_format($row_perlengkapan['harga']) . ")" ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="formKeuangan">Simpan</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const keuanganModal = new bootstrap.Modal(document.getElementById('keuanganModal'));
    const formKeuangan = document.getElementById('formKeuangan');

    const jenisSelect = document.getElementById('jenis');
    const idPerlengkapanSelect = document.getElementById('id_perlengkapan');
    const jumlahInput = document.getElementById('jumlah');
    const keteranganTextarea = document.getElementById('keterangan');
    
    const perlengkapanData = <?= json_encode($perlengkapan_array); ?>;

    function handleFormVisibility() {
        const selectedJenis = jenisSelect.value;

        if (selectedJenis === 'pengeluaran') {
            idPerlengkapanSelect.disabled = false;
            jumlahInput.readOnly = false; 
            
            if (idPerlengkapanSelect.value === "") {
                jumlahInput.value = '';
            }

        } else if (selectedJenis === 'pemasukan') {
            idPerlengkapanSelect.value = '';
            idPerlengkapanSelect.disabled = true;
            jumlahInput.value = '';
            jumlahInput.readOnly = false;
            keteranganTextarea.value = '';
        } else {
            idPerlengkapanSelect.value = '';
            idPerlengkapanSelect.disabled = true;
            jumlahInput.value = '';
            jumlahInput.readOnly = false;
            keteranganTextarea.value = '';
        }
    }

    jenisSelect.addEventListener('change', handleFormVisibility);

    idPerlengkapanSelect.addEventListener('change', function() {
        const selectedPerlengkapanId = this.value;
        if (selectedPerlengkapanId) {
            const selectedPerlengkapan = perlengkapanData.find(item => item.id_perlengkapan == selectedPerlengkapanId);
            if (selectedPerlengkapan) {
                jumlahInput.value = selectedPerlengkapan.harga;
                jumlahInput.readOnly = true;
                keteranganTextarea.value = 'Pembelian alat perlengkapan: ' + selectedPerlengkapan.nama_perlengkapan;
            }
        } else {
            jumlahInput.value = '';
            jumlahInput.readOnly = false;
            keteranganTextarea.value = '';
        }
    });

    // Event listener untuk submit form
    formKeuangan.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(this.action, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    keuanganModal.hide();
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => console.error('Error:', error));
    });

    // Inisialisasi state form saat modal dibuka
    document.getElementById('keuanganModal').addEventListener('show.bs.modal', function () {
        formKeuangan.reset();
        handleFormVisibility(); // Panggil fungsi ini untuk mengatur form ke keadaan default
    });
});
</script>