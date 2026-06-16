<?php
// Koneksi ke database
include '../koneksi.php';

// Update status ambil berdasarkan data 'is_used'
mysqli_query($koneksi, "
    UPDATE peserta p
    JOIN data d ON p.id_peserta = d.id_peserta
    SET p.status_ambil = 'sudah'
    WHERE d.is_used = 1
");

// Ambil data peserta beserta nama warga, qr_token, dan is_used
$query_pembagian = mysqli_query($koneksi, "
    SELECT 
        p.id_peserta, p.level, p.jumlah_daging_kg, p.status_ambil, 
        w.nama, w.nik, d.qr_token, d.is_used
    FROM peserta p
    JOIN warga w ON p.nik = w.nik
    JOIN data d ON p.id_peserta = d.id_peserta
    ORDER BY p.id_peserta ASC
");
?>

<h3 class="text-center"><i class="bi bi-box-seam"></i> Form Pembagian Daging Qurban</h3>

<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark text-center">
            <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>Level</th>
                <th>Jumlah Daging (kg)</th>
                <th>Status Ambil</th>
                <th>QR Token</th>
                <th>Status QR</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($query_pembagian)) : ?>
            <tr>
                <td class="text-center"><?= htmlspecialchars($row['nik'] ?? '-'); ?></td>
                <td><?= htmlspecialchars($row['nama'] ?? '-'); ?></td>
                <td class="text-center"><?= htmlspecialchars(ucfirst($row['level'] ?? '-')); ?></td>
                <td class="text-center"><?= htmlspecialchars($row['jumlah_daging_kg'] ?? '0'); ?> kg</td>
                <td class="text-center">
                    <?php if ($row['status_ambil'] === 'sudah') : ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Sudah</span>
                    <?php else : ?>
                        <span class="badge bg-danger"><i class="bi bi-x-circle-fill"></i> Belum</span>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?= htmlspecialchars($row['qr_token'] ?? '-'); ?></td>
                <td class="text-center">
                    <?php if ($row['is_used'] == 1) : ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Sudah Digunakan</span>
                    <?php else : ?>
                        <span class="badge bg-secondary"><i class="bi bi-x-circle-fill"></i> Belum Digunakan</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
