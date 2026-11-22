<?php
require_once 'config/database.php';
require_once 'config/helpers.php';

$toko = get_toko($pdo);
$layananList = $pdo->query("SELECT * FROM layanan WHERE aktif = 1 ORDER BY id ASC")->fetchAll();

// Fitur Cek Resi / Invoice Publik
$hasilTracking = null;
$kodeCari = trim($_GET['invoice'] ?? '');
if ($kodeCari !== '') {
    $stmt = $pdo->prepare("SELECT * FROM transaksi WHERE kode_invoice = ? OR no_hp = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$kodeCari, $kodeCari]);
    $hasilTracking = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($toko['nama_toko']); ?> — Layanan Laundry Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #0284c7 100%);
            color: #fff;
            padding: 5rem 0 6rem;
        }
        .tracking-box {
            margin-top: -3.5rem;
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3 border-bottom border-secondary border-opacity-25">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <span class="badge bg-primary p-2 rounded-3"><i class="fa-solid fa-shirt"></i></span>
                <span><?= e($toko['nama_toko']); ?></span>
            </a>
            <div class="d-flex gap-2">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= $_SESSION['role'] === 'pelanggan' ? 'pelanggan_dashboard.php' : 'admin_dashboard.php'; ?>" class="btn btn-primary btn-sm fw-semibold px-3 rounded-pill">
                        <i class="fa-solid fa-gauge me-1"></i> Dashboard Saya
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light btn-sm fw-semibold px-3 rounded-pill">
                        <i class="fa-solid fa-right-to-bracket me-1"></i> Masuk Sistem
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero-section text-center">
        <div class="container">
            <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-50 px-3 py-2 rounded-pill mb-3">
                <i class="fa-solid fa-sparkles me-1"></i> Sistem Laundry Modern & Transparan
            </span>
            <h1 class="display-5 fw-bold mb-3"><?= e($toko['tagline']); ?></h1>
            <p class="lead text-white-50 mx-auto mb-4" style="max-width: 650px;">
                Pantau progres cucian Anda secara real-time dari rumah, lengkap dengan estimasi selesai dan layanan antar-jemput cepat.
            </p>
        </div>
    </section>

    <!-- Live Order Tracking Box -->
    <div class="container tracking-box mb-5">
        <div class="card-pro p-4 p-md-5 shadow-lg mx-auto" style="max-width: 820px;">
            <h5 class="fw-bold mb-3 text-center"><i class="fa-solid fa-magnifying-glass-location text-primary me-2"></i>Lacak Status Cucian Anda</h5>
            <form method="GET" action="index.php#tracking" id="tracking" class="row g-2">
                <div class="col-md-9">
                    <input type="text" class="form-control form-control-lg" name="invoice" value="<?= e($kodeCari); ?>" placeholder="Masukkan Nomor Invoice (contoh: INV-2026...) atau No HP Anda..." required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold">Cek Status</button>
                </div>
            </form>

            <?php if ($kodeCari !== ''): ?>
                <div class="mt-4 pt-3 border-top">
                    <?php if ($hasilTracking): ?>
                        <div class="alert alert-light border rounded-4 p-4 mb-0">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                                <div>
                                    <span class="badge bg-primary mb-1"><?= e($hasilTracking['kode_invoice']); ?></span>
                                    <h5 class="fw-bold mb-0"><?= e($hasilTracking['nama_pelanggan']); ?></h5>
                                </div>
                                <div class="text-md-end">
                                    <span class="badge rounded-pill fs-6 px-3 py-2 <?= badge_status_cucian($hasilTracking['status_cucian']); ?>">
                                        Status: <?= e($hasilTracking['status_cucian']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="row g-2 small text-muted">
                                <div class="col-md-4"><strong>Layanan:</strong> <?= e($hasilTracking['jenis_layanan']); ?> (<?= (float)$hasilTracking['berat_qty']; ?> <?= e($hasilTracking['satuan']); ?>)</div>
                                <div class="col-md-4"><strong>Pengantaran:</strong> <?= e($hasilTracking['status_antar']); ?></div>
                                <div class="col-md-4"><strong>Total:</strong> <?= rupiah($hasilTracking['total_harga']); ?> (<?= e($hasilTracking['status_bayar']); ?>)</div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fa-solid fa-circle-info me-1"></i> Data transaksi dengan nomor <strong><?= e($kodeCari); ?></strong> tidak ditemukan.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Daftar Layanan & Harga -->
    <section class="container py-4 mb-5">
        <div class="text-center mb-4">
            <h3 class="fw-bold">Daftar Layanan & Tarif</h3>
            <p class="text-muted">Harga transparan dengan kualitas pencucian higienis dan parfum premium.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($layananList as $l): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card-pro stat-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold"><?= e($l['kode_layanan']); ?></span>
                            <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i>Est. <?= $l['estimasi_jam']; ?> Jam</span>
                        </div>
                        <h5 class="fw-bold mb-2"><?= e($l['nama_layanan']); ?></h5>
                        <p class="text-muted small mb-3"><?= e($l['deskripsi']); ?></p>
                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-baseline">
                            <h4 class="fw-bold text-primary mb-0"><?= rupiah($l['harga']); ?></h4>
                            <span class="text-muted small">/ <?= e($l['satuan']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-top py-4 text-center text-muted small">
        <div class="container">
            <p class="mb-1 fw-bold text-dark"><?= e($toko['nama_toko']); ?> — <?= e($toko['alamat_toko']); ?></p>
            <p class="mb-0">WhatsApp Layanan Pelanggan: <?= e($toko['no_wa_toko']); ?></p>
        </div>
    </footer>

</body>
</html>
