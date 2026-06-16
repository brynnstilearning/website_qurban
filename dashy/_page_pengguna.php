<?php
// Mengambil data pengguna untuk ditampilkan di tabel
$query_users = mysqli_query($koneksi, "
    SELECT r.username, r.nik, w.nama AS nama_warga, r.namalengkap, r.level, r.id_peserta
    FROM register r
    LEFT JOIN warga w ON r.nik = w.nik
    ORDER BY r.level ASC, r.username ASC
");

// Mengambil data warga untuk dropdown NIK
$query_warga_options = mysqli_query($koneksi, "SELECT nik, nama FROM warga ORDER BY nik ASC");
$warga_options = [];
while ($w_row = mysqli_fetch_assoc($query_warga_options)) {
    $warga_options[] = $w_row;
}

// Mengambil data peserta untuk dropdown ID Peserta
$query_peserta_options = mysqli_query($koneksi, "
    SELECT p.id_peserta, p.nik, w.nama AS nama_peserta
    FROM peserta p LEFT JOIN warga w ON p.nik = w.nik
    ORDER BY p.id_peserta ASC
");
$peserta_options = [];
while ($p_row = mysqli_fetch_assoc($query_peserta_options)) {
    $peserta_options[] = $p_row;
}
?>

<h3 class="text-center"><i class="fas fa-users-cog me-2"></i>Manajemen Pengguna Aplikasi</h3>

<div class="mb-3">
    <button type="button" class="btn btn-success" id="btnTambahPengguna">
        <i class="fas fa-user-plus"></i> Tambah Pengguna Baru
    </button>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>NIK (Terkait Warga)</th>
                <th>Level</th>
                <th>ID Peserta</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="tabelPenggunaBody">
            <?php if (mysqli_num_rows($query_users) > 0): ?>
                <?php mysqli_data_seek($query_users, 0); ?>
                <?php while ($row = mysqli_fetch_assoc($query_users)): ?>
                    <tr id="row-pengguna-<?= htmlspecialchars($row['username']) ?>">
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['namalengkap']) ?></td>
                        <td><?= htmlspecialchars($row['nik'] ?? '-') ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?php
                            if ($row['level'] == 'admin') echo 'danger';
                            elseif ($row['level'] == 'panitia') echo 'primary';
                            elseif ($row['level'] == 'berqurban') echo 'info';
                            else echo 'secondary';
                            ?>">
                                <?= htmlspecialchars(ucfirst($row['level'])) ?>
                            </span>
                        </td>
                        <td class="text-center"><?= htmlspecialchars($row['id_peserta'] ?? '-') ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-primary btn-sm btn-edit-pengguna" data-username="<?= htmlspecialchars($row['username']) ?>"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-danger btn-sm btn-hapus-pengguna" data-username="<?= htmlspecialchars($row['username']) ?>"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pengguna terdaftar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="penggunaModal" tabindex="-1" aria-labelledby="penggunaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="penggunaModalLabel">Form Data Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formPengguna">
            <input type="hidden" name="original_username" id="original_username">
            
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            
            <div id="password-fields">
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="ulang_password" class="form-label">Ulangi Password</label>
                    <input type="password" class="form-control" id="ulang_password" name="ulang_password">
                </div>
            </div>

            <div class="mb-3">
                <label for="namalengkap" class="form-label">Nama Lengkap User</label>
                <input type="text" class="form-control" id="namalengkap" name="namalengkap" required>
            </div>
            
            <div class="mb-3">
                <label for="nik" class="form-label">NIK (Terkait Warga)</label>
                <select class="form-select" id="nik" name="nik">
                    <option value="">-- Tidak Terkait --</option>
                    <?php foreach ($warga_options as $w_opt) : ?>
                        <option value="<?= htmlspecialchars($w_opt['nik']) ?>">
                            <?= htmlspecialchars($w_opt['nik']) ?> - <?= htmlspecialchars($w_opt['nama']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="level" class="form-label">Level</label>
                <select class="form-select" id="level" name="level" required>
                    <option value="">-- Pilih Level --</option>
                    <option value="admin">Admin</option>
                    <option value="panitia">Panitia</option>
                    <option value="warga">Warga</option>
                    <option value="berqurban">Berqurban</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="id_peserta" class="form-label">ID Peserta (Terkait Peserta Qurban)</label>
                <select class="form-select" id="id_peserta" name="id_peserta">
                    <option value="">-- Tidak Terkait --</option>
                     <?php foreach ($peserta_options as $p_opt) : ?>
                        <option value="<?= htmlspecialchars($p_opt['id_peserta']) ?>">
                            ID: <?= htmlspecialchars($p_opt['id_peserta']) ?> - NIK: <?= htmlspecialchars($p_opt['nik']) ?> (<?= htmlspecialchars($p_opt['nama_peserta'] ?? 'N/A') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary" form="formPengguna">Simpan</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const penggunaModal = new bootstrap.Modal(document.getElementById('penggunaModal'));
    const modalTitle = document.getElementById('penggunaModalLabel');
    const formPengguna = document.getElementById('formPengguna');
    const tabelPenggunaBody = document.getElementById('tabelPenggunaBody');
    const passwordFields = document.getElementById('password-fields');

    document.getElementById('btnTambahPengguna').addEventListener('click', function () {
        modalTitle.textContent = 'Tambah Pengguna Baru';
        formPengguna.reset();
        formPengguna.action = 'fungsi/simpan_register.php';
        document.getElementById('original_username').value = '';
        document.getElementById('username').readOnly = false;
        passwordFields.style.display = 'block'; // Tampilkan field password
        document.getElementById('password').required = true;
        document.getElementById('ulang_password').required = true;
        penggunaModal.show();
    });

    tabelPenggunaBody.addEventListener('click', function (event) {
        const target = event.target.closest('button');
        if (!target) return;
        const username = target.dataset.username;

        if (target.classList.contains('btn-edit-pengguna')) {
            modalTitle.textContent = 'Edit Data Pengguna';
            formPengguna.action = 'fungsi/simpan_register.php';
            passwordFields.style.display = 'none'; // Sembunyikan field password saat edit
            document.getElementById('password').required = false;
            document.getElementById('ulang_password').required = false;

            fetch(`fungsi/get_pengguna_detail.php?username=${username}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const userData = data.data;
                        document.getElementById('original_username').value = userData.username;
                        document.getElementById('username').value = userData.username;
                        document.getElementById('username').readOnly = true;
                        document.getElementById('namalengkap').value = userData.namalengkap;
                        document.getElementById('nik').value = userData.nik;
                        document.getElementById('level').value = userData.level;
                        document.getElementById('id_peserta').value = userData.id_peserta;
                        penggunaModal.show();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                });
        }

        if (target.classList.contains('btn-hapus-pengguna')) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan menghapus pengguna dengan username: ${username}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('fungsi/delete_register.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `username=${username}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Terhapus!', data.message, 'success');
                            document.getElementById(`row-pengguna-${username}`).remove();
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    });
                }
            });
        }
    });

    formPengguna.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch(this.action, { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    penggunaModal.hide();
                    Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            });
    });
});
</script>