<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QurbanKuy</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <div class="logo-container">
        <img src="images/logo2.png">
        <h1>QurbanKuy</h1>
    </div>
</header>

<div class="content-wrapper">
    <div class="hero">
        <h1>Selamat Datang di Sistem Informasi Qurban</h1>
        <p>Mengelola qurban dengan mudah, transparan, dan terorganisir.</p>
        <button type="button" class="btn btn-primary btn-login-page" data-bs-toggle="modal" data-bs-target="#loginModal">
            Masuk ke Sistem
        </button>
    </div>
</div>

<footer>
    <div>&copy; <?php echo date('Y'); ?> Sistem Qurban | RT 001 Desa Junrejo</div>
    <p>QurbanKuy adalah platform digital yang membantu Anda mengelola proses qurban secara modern, mulai dari pendaftaran, pembayaran, hingga distribusi.</p>
</footer>

<!-- Modal Login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="loginModalLabel"><i class="bi bi-person-circle form-icon"></i>Login Sistem Qurban</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form action="act_login.php?op=in" method="post">
            <div class="mb-3">
                <label class="form-label d-flex align-items-center"><i class="bi bi-person-fill form-icon"></i>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label d-flex align-items-center"><i class="bi bi-lock-fill form-icon"></i>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label d-flex align-items-center"><i class="bi bi-people-fill form-icon"></i>Level</label>
                <select name="level" class="form-select" required>
                    <option value="">-- Pilih Level --</option>
                    <option value="Admin">Admin</option>
                    <option value="Panitia">Panitia</option>
                    <option value="Warga">Warga</option>
                    <option value="Berqurban">Berqurban</option>
                </select>
            </div>

            <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-2"></i>Login</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
