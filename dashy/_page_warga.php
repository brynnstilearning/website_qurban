<?php
// PHP untuk mengambil data warga tetap sama
$data_warga = mysqli_query($koneksi, "SELECT * FROM warga ORDER BY nama ASC");
?>

<style>
    .table-warga {
        /* Properti kunci untuk memaksa lebar tabel */
        table-layout: fixed;
        width: 100%;
        font-size: 0.9rem;
    }
    .table-warga th, .table-warga td {
        padding: 8px 10px;
        vertical-align: middle;
        /* Properti ini akan memastikan teks panjang akan pindah baris */
        word-wrap: break-word;
    }
    .table-warga small {
        font-size: 0.85em;
        color: #6c757d;
    }
</style>

<h3 class="text-center"><i class="bi bi-people-fill"></i> Data Warga</h3>

<div class="btn-group-custom mb-3">
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#wargaModal" id="btnTambahWarga">
        <i class="bi bi-plus-circle"></i> Tambah Warga
    </button>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped align-middle table-warga">
        <thead class="table-dark">
            <tr>
                <th style="width: 14%;">NIK</th>
                <th style="width: 15%;">Nama</th>
                <th style="width: 12%;">Lahir</th>
                <th style="width: 4%;" class="text-center">JK</th>
                <th style="width: 25%;">Alamat</th>
                <th style="width: 7%;" class="text-center">Agama</th>
                <th style="width: 10%;" class="text-center">Status</th>
                <th style="width: 4%;" class="text-center">KWN</th>
                <th style="width: 9%;" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelWargaBody">
            <?php 
            mysqli_data_seek($data_warga, 0); 
            while ($row = mysqli_fetch_assoc($data_warga)) { 
            ?>
                <tr id="row-<?= htmlspecialchars($row['nik']); ?>">
                    <td><?= htmlspecialchars($row['nik']); ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td>
                        <?= htmlspecialchars($row['tempat_lahir']); ?><br>
                        <small><?= date('d M Y', strtotime($row['tanggal_lahir'])); ?></small>
                    </td>
                    <td class="text-center"><?= htmlspecialchars($row['jenis_kelamin']); ?></td>
                    <td><?= htmlspecialchars($row['alamat']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['agama']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['status_perkawinan']); ?></td>
                    <td class="text-center"><?= htmlspecialchars($row['kewarganegaraan']); ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-warning btn-sm btn-edit" data-nik="<?= htmlspecialchars($row['nik']); ?>" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm btn-hapus" data-nik="<?= htmlspecialchars($row['nik']); ?>" title="Hapus">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>



<div class="modal fade" id="wargaModal" tabindex="-1" aria-labelledby="wargaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="wargaModalLabel">Form Data Warga</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formWarga">
            <input type="hidden" name="old_nik" id="old_nik">
            <div class="mb-3">
                <label for="nik" class="form-label">NIK</label>
                <input type="text" class="form-control" id="nik" name="nik" maxlength="16" required>
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Jenis Kelamin</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_l" value="L" required>
                    <label class="form-check-label" for="jk_l">Laki-laki</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_p" value="P">
                    <label class="form-check-label" for="jk_p">Perempuan</label>
                </div>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="agama" class="form-label">Agama</label>
                    <select name="agama" id="agama" class="form-select" required>
                        <option value="">-- Pilih Agama --</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="status_perkawinan" class="form-label">Status Perkawinan</label>
                    <select name="status_perkawinan" id="status_perkawinan" class="form-select" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="Belum Kawin">Belum Kawin</option>
                        <option value="Kawin">Kawin</option>
                        <option value="Cerai Hidup">Cerai Hidup</option>
                        <option value="Cerai Mati">Cerai Mati</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="pekerjaan" class="form-label">Pekerjaan</label>
                    <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" required>
                </div>
                <div class="col-md-6">
                    <label for="kewarganegaraan" class="form-label">Kewarganegaraan</label>
                     <select name="kewarganegaraan" id="kewarganegaraan" class="form-select" required>
                        <option value="WNI">WNI</option>
                        <option value="WNA">WNA</option>
                    </select>
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="formWarga">Simpan Perubahan</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wargaModal = new bootstrap.Modal(document.getElementById('wargaModal'));
    const modalTitle = document.getElementById('wargaModalLabel');
    const formWarga = document.getElementById('formWarga');
    const tabelWargaBody = document.getElementById('tabelWargaBody');

    // Event untuk Tombol "Tambah Warga"
    document.getElementById('btnTambahWarga').addEventListener('click', function() {
        modalTitle.textContent = 'Tambah Warga Baru';
        formWarga.reset(); // Kosongkan form
        formWarga.action = 'fungsi/simpan_warga.php';
        document.getElementById('old_nik').value = '';
    });

    // Event untuk tombol Edit dan Hapus (menggunakan event delegation)
    tabelWargaBody.addEventListener('click', function(event) {
        const target = event.target.closest('button');
        if (!target) return;

        const nik = target.dataset.nik;

        // Jika tombol EDIT yang diklik
        if (target.classList.contains('btn-edit')) {
            modalTitle.textContent = 'Edit Data Warga';
            formWarga.action = 'fungsi/simpan_warga.php';

            fetch(`fungsi/get_warga_detail.php?nik=${nik}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Isi form dengan data yang diterima
                        document.getElementById('old_nik').value = data.data.nik;
                        document.getElementById('nik').value = data.data.nik;
                        document.getElementById('nama').value = data.data.nama;
                        document.getElementById('tempat_lahir').value = data.data.tempat_lahir;
                        document.getElementById('tanggal_lahir').value = data.data.tanggal_lahir;
                        document.getElementById('alamat').value = data.data.alamat;
                        document.getElementById('pekerjaan').value = data.data.pekerjaan;
                        document.getElementById('agama').value = data.data.agama;
                        document.getElementById('status_perkawinan').value = data.data.status_perkawinan;
                        document.getElementById('kewarganegaraan').value = data.data.kewarganegaraan;
                        if (data.data.jenis_kelamin === 'L') {
                            document.getElementById('jk_l').checked = true;
                        } else {
                            document.getElementById('jk_p').checked = true;
                        }
                        wargaModal.show();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        }

        // Jika tombol HAPUS yang diklik
        if (target.classList.contains('btn-hapus')) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus data warga dengan NIK: ${nik}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('fungsi/delete_warga.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `nik=${nik}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Terhapus!', data.message, 'success');
                            document.getElementById(`row-${nik}`).remove();
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    });
                }
            });
        }
    });

    // Event untuk submit form di dalam modal
    formWarga.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                wargaModal.hide();
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Gagal!', data.message, 'error');
            }
        });
    });
});
</script>