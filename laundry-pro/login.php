<?php
require_once 'config/database.php';
require_once 'config/helpers.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'pelanggan' ? 'pelanggan_dashboard.php' : 'admin_dashboard.php'));
    exit;
}

$toko = get_toko($pdo);
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= e($toko['nama_toko']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .login-card {
            width: 100%;
            max-width: 430px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            padding: 2.5rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.5rem;">
                <i class="fa-solid fa-shirt"></i>
            </div>
            <h4 class="fw-bold mb-1"><?= e($toko['nama_toko']); ?></h4>
            <p class="text-muted small mb-0">Masuk ke Panel Kasir Admin atau Portal Pelanggan</p>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']); ?> small py-2">
                <?= e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <form action="actions/auth_login.php" method="POST">
            <?= csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Username atau No HP</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="Contoh: admin atau 0812..." required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-bold rounded-3 shadow-sm mb-3">
                Masuk Sekarang <i class="fa-solid fa-arrow-right ms-1"></i>
            </button>
        </form>

        <div class="text-center border-top pt-3">
            <a href="index.php" class="text-decoration-none small text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman Depan & Cek Resi
            </a>
        </div>
    </div>

</body>
</html>
