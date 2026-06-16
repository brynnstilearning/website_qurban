<?php
// Query untuk menampilkan data di tabel utama (sudah diubah agar JOIN ke peserta)
$result_qr = $koneksi->query("
    SELECT d.id_peserta, d.name, d.is_used, p.nik, p.level
    FROM data d
    JOIN peserta p ON d.id_peserta = p.id_peserta
    ORDER BY p.nik ASC
");

// Query untuk dropdown 'ID Peserta'
$query_peserta = mysqli_query($koneksi, "SELECT p.id_peserta, p.nik, p.level, w.nama FROM peserta p JOIN warga w ON p.nik = w.nik ORDER BY p.id_peserta ASC");

// Query untuk dropdown 'Nama Warga'
$query_warga = mysqli_query($koneksi, "SELECT DISTINCT w.nama FROM peserta p JOIN warga w ON p.nik = w.nik ORDER BY w.nama ASC");

// Query untuk dropdown 'Token Info'
$query_peserta2 = mysqli_query($koneksi, "SELECT peserta.nik, peserta.level, warga.nama FROM peserta JOIN warga ON peserta.nik = warga.nik");
?>

<h3 class="text-center"><i class="bi bi-qr-code"></i> Kelola QR Code Warga</h3>

<div class="d-flex justify-content-start gap-2 mb-3">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#qrModal">
        <i class="bi bi-plus-circle"></i> Tambah QR Manual
    </button>
    <a href="fungsi/generate_qr.php" class="btn btn-primary">
        <i class="bi bi-qr-code-scan"></i> Generate File QR Massal
    </a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>Level</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result_qr && $result_qr->num_rows > 0) {
                while ($row = $result_qr->fetch_assoc()):
            ?>
                    <tr>
                        <td class="text-center"><?= htmlspecialchars($row['nik']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td class="text-center"><?= htmlspecialchars(ucfirst($row['level'])) ?></td>
                        <td class="text-center">
                            <span class="badge <?= $row['is_used'] == 1 ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= $row['is_used'] == 1 ? "Sudah Didownload" : "Belum Didownload" ?>
                            </span>
                        </td>
                    </tr>
            <?php
                endwhile;
            } else {
                echo "<tr><td colspan='4' class='text-center'>Belum ada data QR Code yang dibuat.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalLabel">Form Tambah QR Token</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formQr" action="fungsi/simpan_qr.php" method="POST">
            <div class="mb-3">
                <label for="id_peserta" class="form-label">ID Peserta</label>
                <select name="id_peserta" id="id_peserta" class="form-select" required>
                    <option value="">-- Pilih ID Peserta --</option>
                    <?php
                    mysqli_data_seek($query_peserta, 0); // reset pointer
                    while ($row = mysqli_fetch_assoc($query_peserta)) {
                        echo "<option value='{$row['id_peserta']}' data-nama='" . htmlspecialchars($row['nama'], ENT_QUOTES) . "'>" .
                            "{$row['id_peserta']} - {$row['nik']} - {$row['level']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Nama Warga</label>
                <select name="name" id="name" class="form-select" required>
                    <option value="">-- Pilih Nama --</option>
                    <?php
                    mysqli_data_seek($query_warga, 0);
                    while ($row = mysqli_fetch_assoc($query_warga)) {
                        echo "<option value='" . htmlspecialchars($row['nama'], ENT_QUOTES) . "'>{$row['nama']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="qr_token" class="form-label">Token Info (Nama - NIK - Level)</label>
                <select name="qr_token" id="qr_token" class="form-select" required>
                    <option value="">-- Pilih Token Info --</option>
                    <?php
                    mysqli_data_seek($query_peserta2, 0);
                    while ($row = mysqli_fetch_assoc($query_peserta2)):
                        $token = "qr_" . uniqid(); // Generate a unique token
                        $info = $row['nama'] . " - " . $row['nik'] . " - " . $row['level'];
                        ?>
                        <option value="<?= $token ?>"><?= $info ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <input type="hidden" name="is_used" value="0">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="formQr">Simpan QR Token</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    const formQr = document.getElementById('formQr');
    
    const idPesertaSelect = document.getElementById('id_peserta');
    const namaWargaSelect = document.getElementById('name');

    idPesertaSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const selectedNama = selectedOption.dataset.nama;
        
        for (let i = 0; i < namaWargaSelect.options.length; i++) {
            if (namaWargaSelect.options[i].value === selectedNama) {
                namaWargaSelect.selectedIndex = i;
                break;
            }
        }
    });

    formQr.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(this.action, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    qrModal.hide();
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
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
