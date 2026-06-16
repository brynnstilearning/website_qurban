<?php
// Query untuk mengambil data peserta untuk ditampilkan di tabel
$query_peserta = mysqli_query($koneksi, "
    SELECT p.*, w.nama
    FROM peserta p
    LEFT JOIN warga w ON p.nik = w.nik
    ORDER BY p.id_peserta ASC
");

// Query untuk mengisi dropdown NIK di form modal
$data_warga = mysqli_query($koneksi, "SELECT nik, nama FROM warga ORDER BY nik ASC");
?>
<h3 class="text-center"><i class="bi bi-person-lines-fill"></i> Data Peserta Qurban</h3>

<div class="d-flex justify-content-between mb-4">
    <button type="button" class="btn btn-success" id="btnTambahPeserta">
        <i class="bi bi-plus-circle"></i> Tambah Peserta
    </button>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover table-striped mt-4">
        <thead class="table-dark">
            <tr>
                <th class="text-center">ID Peserta</th>
                <th>NIK</th>
                <th>Nama</th>
                <th class="text-center">Level</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelPesertaBody">
            <?php 
            if ($query_peserta) {
                while ($row = mysqli_fetch_assoc($query_peserta)): 
            ?>
             <tr id="row-peserta-<?= $row['id_peserta'] ?>">
                <td class="text-center"><?= htmlspecialchars($row['id_peserta']) ?></td>
                <td><?= htmlspecialchars($row['nik']) ?></td>
                <td><?= htmlspecialchars($row['nama'] ?? '-') ?></td>
                <td class="text-center"><?= ucfirst(htmlspecialchars($row['level'])) ?></td>
                <td class="text-center">
                    <button type="button" class="btn btn-primary btn-sm btn-edit-peserta" data-id="<?= $row['id_peserta'] ?>" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm btn-hapus-peserta" data-id="<?= $row['id_peserta'] ?>" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <?php 
                endwhile; 
            }
            ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="pesertaModal" tabindex="-1" aria-labelledby="pesertaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="pesertaModalLabel">Form Data Peserta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formPeserta">
            <input type="hidden" name="id_peserta" id="id_peserta">
            <div class="mb-3">
                <label for="nik" class="form-label">NIK Warga</label>
                <select name="nik" id="nik" class="form-select" required>
                    <option value="">-- Pilih NIK & Nama --</option>
                    <?php 
                    if($data_warga) {
                        mysqli_data_seek($data_warga, 0); // Reset pointer
                        while ($row_warga = mysqli_fetch_assoc($data_warga)): 
                    ?>
                        <option value="<?= $row_warga['nik'] ?>"><?= $row_warga['nik'] ?> - <?= $row_warga['nama'] ?></option>
                    <?php 
                        endwhile; 
                    }
                    ?>
                </select>
                <div id="nik-readonly-text" class="form-control" style="display:none; background-color:#e9ecef;" readonly></div>
            </div>
            <div class="mb-3">
                <label for="level" class="form-label">Level</label>
                <select name="level" id="level" class="form-select" required>
                    <option value="">-- Pilih Level --</option>
                    <option value="admin">Admin</option>
                    <option value="warga">Warga</option>
                    <option value="panitia">Panitia</option>
                    <option value="berqurban">Berqurban</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="jumlah_daging_kg" class="form-label">Jumlah Daging (kg)</label>
                <input type="number" id="jumlah_daging_kg" name="jumlah_daging_kg" step="0.1" class="form-control">
            </div>
            <div class="mb-3">
                <label for="status_ambil" class="form-label">Status Ambil</label>
                <select name="status_ambil" id="status_ambil" class="form-select" required>
                    <option value="belum">Belum</option>
                    <option value="sudah">Sudah</option>
                </select>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="formPeserta">Simpan</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pesertaModal = new bootstrap.Modal(document.getElementById('pesertaModal'));
    const modalTitle = document.getElementById('pesertaModalLabel');
    const formPeserta = document.getElementById('formPeserta');
    const tabelPesertaBody = document.getElementById('tabelPesertaBody');
    const nikSelect = document.getElementById('nik');
    const nikReadonly = document.getElementById('nik-readonly-text');

    document.getElementById('btnTambahPeserta').addEventListener('click', function() {
        modalTitle.textContent = 'Tambah Peserta Baru';
        formPeserta.reset();
        formPeserta.action = 'fungsi/simpan_peserta.php';
        document.getElementById('id_peserta').value = '';
        
        nikSelect.style.display = 'block'; 
        nikSelect.required = true;
        nikReadonly.style.display = 'none';
        
        let hiddenNikInput = formPeserta.querySelector('input[name="nik"]');
        if (hiddenNikInput) hiddenNikInput.remove();

        pesertaModal.show();
    });

    tabelPesertaBody.addEventListener('click', function(event) {
        const target = event.target.closest('button');
        if (!target) return;
        const id = target.dataset.id;

        if (target.classList.contains('btn-edit-peserta')) {
            modalTitle.textContent = 'Edit Data Peserta';
            formPeserta.action = 'fungsi/simpan_peserta.php';
            
            fetch(`fungsi/get_peserta_detail.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('id_peserta').value = data.data.id_peserta;
                        document.getElementById('level').value = data.data.level;
                        document.getElementById('jumlah_daging_kg').value = data.data.jumlah_daging_kg;
                        document.getElementById('status_ambil').value = data.data.status_ambil;
                        
                        nikSelect.style.display = 'none';
                        nikSelect.required = false;
                        nikReadonly.style.display = 'block';
                        nikReadonly.textContent = `${data.data.nik} - ${data.data.nama}`;
                        
                        let hiddenNikInput = formPeserta.querySelector('input[name="nik"]');
                        if (!hiddenNikInput) {
                            hiddenNikInput = document.createElement('input');
                            hiddenNikInput.type = 'hidden';
                            hiddenNikInput.name = 'nik';
                            formPeserta.appendChild(hiddenNikInput);
                        }
                        hiddenNikInput.value = data.data.nik;

                        pesertaModal.show();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    Swal.fire('Error Jaringan', 'Tidak bisa mengambil data. Periksa konsol browser.', 'error');
                });
        }
        
        if (target.classList.contains('btn-hapus-peserta')) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus data peserta dengan ID: ${id}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonText: 'Batal',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('fungsi/delete_peserta.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `id_peserta=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Terhapus!', data.message, 'success');
                            document.getElementById(`row-peserta-${id}`).remove();
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                        Swal.fire('Error Jaringan', 'Gagal menghapus data. Periksa konsol browser.', 'error');
                    });
                }
            });
        }
    });

    formPeserta.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (nikSelect.style.display === 'none') {
            nikSelect.disabled = true;
        } else {
            nikSelect.disabled = false;
        }

        const formData = new FormData(this);
        nikSelect.disabled = false;

        fetch(this.action, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    pesertaModal.hide();
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                Swal.fire('Error Jaringan', 'Gagal menyimpan data. Periksa konsol browser.', 'error');
            });
    });
});
</script>