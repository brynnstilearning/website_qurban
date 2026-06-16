<?php
// Menggunakan path yang benar untuk include dan require
include '../../koneksi.php';
require '../../phpqrcode/qrlib.php'; // Asumsi folder phqrcode ada di root qurban/

$qrGenerated = false;
$error = '';
$qrImage = '';
$qrName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan folder untuk menyimpan QR code ada dan bisa ditulis
    $qrDir = '../../qrcodes/';
    if (!file_exists($qrDir)) {
        mkdir($qrDir, 0777, true);
    }

    $item_id = $_POST['item_id']; // Ini adalah 'id' dari tabel `data`

    // Menggunakan prepared statement untuk keamanan
    $stmt = $koneksi->prepare("SELECT * FROM data WHERE id = ?");
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Jika token belum ada, buat yang baru. Jika sudah ada, gunakan yang lama.
        $token = $row['qr_token'] ?? uniqid('qr_', true);

        // URL yang akan disematkan di dalam QR Code
        // Sesuaikan 'localhost/qurban/dashy/' dengan URL aplikasi Anda yang sebenarnya
        $url = "http://localhost/qurban/dashy/verify_qr.php?token=" . $token;

        // Update token di database jika sebelumnya kosong
        if (empty($row['qr_token'])) {
            $update = $koneksi->prepare("UPDATE data SET qr_token = ? WHERE id = ?");
            $update->bind_param('si', $token, $item_id);
            $update->execute();
            $update->close();
        }

        // Generate file gambar .png
        $file = $qrDir . $token . ".png";
        QRcode::png($url, $file, QR_ECLEVEL_L, 4);

        $qrGenerated = true;
        $qrImage = $file;
        $qrName = $row['name'];
    } else {
        $error = "Data tidak ditemukan.";
    }
    $stmt->close();
}

$query_dropdown = "SELECT d.id, d.name, p.level 
                   FROM data d 
                   JOIN peserta p ON d.id_peserta = p.id_peserta 
                   WHERE d.is_used = 0 
                   ORDER BY d.name ASC";
$result_not_used_for_dropdown = $koneksi->query($query_dropdown);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Generate File Gambar QR Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4">Generate File Gambar QR Code</h3>

                        <?php if ($qrGenerated): ?>
                            <div class="alert alert-success text-center">
                                <h5>QR Code Berhasil Dibuat</h5>
                                <p class="mb-2"><strong>Untuk:</strong> <?= htmlspecialchars($qrName) ?></p>
                                <img src="<?= $qrImage ?>" alt="QR Code" class="img-fluid my-2 border rounded">
                                <p class="mt-2 mb-0">File disimpan di: `<?= htmlspecialchars($file) ?>`</p>
                            </div>
                        <?php elseif (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="item_id" class="form-label">Pilih Peserta:</label>
                                <select name="item_id" class="form-select" required>
                                    <option value="">-- Pilih Data Peserta --</option>
                                    <?php
                                    if ($result_not_used_for_dropdown && $result_not_used_for_dropdown->num_rows > 0) {
                                        while ($row = $result_not_used_for_dropdown->fetch_assoc()): ?>
                                            <option value="<?= htmlspecialchars($row['id']) ?>">
                                                <?= htmlspecialchars($row['name']) ?>
                                                (<?= htmlspecialchars(ucfirst($row['level'])) ?>)
                                            </option>
                                        <?php endwhile;
                                    } else { ?>
                                        <option value="" disabled>Tidak ada data yang belum memiliki QR</option>
                                    <?php } ?>


                                </select>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Generate File .PNG</button>
                                <a href="../admindas.php?page=qrcode" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>