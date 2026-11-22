<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
require_admin();

$toko = get_toko($pdo);
$flash = get_flash();

$allowedPages = [
    'home'             => 'Dashboard Utama',
    'transaksi_baru'   => 'Transaksi Baru (POS)',
    'manajemen_status' => 'Manajemen Status',
    'riwayat'          => 'Riwayat Transaksi',
    'pelanggan'        => 'Data Pelanggan',
    'layanan'          => 'Paket & Tarif Layanan',
    'laporan'          => 'Laporan Keuangan',
    'pengaturan'       => 'Pengaturan Toko'
];

$page = $_GET['page'] ?? 'home';
if (!array_key_exists($page, $allowedPages)) {
    $page = 'home';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($allowedPages[$page]); ?> — <?= e($toko['nama_toko']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar no-print" id="appSidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fa-solid fa-shirt"></i></div>
            <div>
                <div class="fw-bold text-white lh-sm"><?= e($toko['nama_toko']); ?></div>
                <small class="text-info" style="font-size: 0.72rem;">POS & Management v2.0</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Menu Utama</div>
            <a href="admin_dashboard.php?page=home" class="sidebar-link <?= $page === 'home' ? 'active' : ''; ?>">
                <i class="fa-solid fa-chart-pie fa-fw"></i> Dashboard
            </a>
            <a href="admin_dashboard.php?page=transaksi_baru" class="sidebar-link <?= $page === 'transaksi_baru' ? 'active' : ''; ?>">
                <i class="fa-solid fa-cash-register fa-fw"></i> Kasir / Order Baru
            </a>
            <a href="admin_dashboard.php?page=manajemen_status" class="sidebar-link <?= $page === 'manajemen_status' ? 'active' : ''; ?>">
                <i class="fa-solid fa-sliders fa-fw"></i> Manajemen Status
            </a>
            <a href="admin_dashboard.php?page=riwayat" class="sidebar-link <?= $page === 'riwayat' ? 'active' : ''; ?>">
                <i class="fa-solid fa-clock-rotate-left fa-fw"></i> Riwayat Transaksi
            </a>

            <div class="nav-section-title">Master & Keuangan</div>
            <a href="admin_dashboard.php?page=pelanggan" class="sidebar-link <?= $page === 'pelanggan' ? 'active' : ''; ?>">
                <i class="fa-solid fa-users fa-fw"></i> Data Pelanggan
            </a>
            <a href="admin_dashboard.php?page=layanan" class="sidebar-link <?= $page === 'layanan' ? 'active' : ''; ?>">
                <i class="fa-solid fa-tags fa-fw"></i> Paket & Tarif
            </a>
            <a href="admin_dashboard.php?page=laporan" class="sidebar-link <?= $page === 'laporan' ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-invoice-dollar fa-fw"></i> Laporan Keuangan
            </a>

            <div class="nav-section-title">Sistem</div>
            <a href="admin_dashboard.php?page=pengaturan" class="sidebar-link <?= $page === 'pengaturan' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gear fa-fw"></i> Pengaturan Toko
            </a>
            <a href="index.php" target="_blank" class="sidebar-link">
                <i class="fa-solid fa-globe fa-fw"></i> Lihat Web Publik
            </a>
        </nav>

        <div class="p-3 border-top border-secondary border-opacity-25">
            <a href="actions/auth_logout.php" class="btn btn-outline-danger btn-sm w-100 fw-semibold">
                <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar Sistem
            </a>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="topbar no-print">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" id="sidebarToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <span class="fw-semibold text-secondary d-none d-sm-inline"><?= e($allowedPages[$page]); ?></span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-light text-dark border px-3 py-2 d-none d-md-inline">
                    <i class="fa-regular fa-calendar me-1 text-primary"></i> <?= date('d M Y'); ?>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                        <?= strtoupper(substr($_SESSION['nama_lengkap'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div class="lh-1">
                        <div class="fw-bold small"><?= e($_SESSION['nama_lengkap'] ?? 'Admin'); ?></div>
                        <small class="text-muted text-capitalize" style="font-size: 0.72rem;"><?= e($_SESSION['role']); ?></small>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-container">
            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']); ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                    <?= $flash['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php require __DIR__ . "/pages/{$page}.php"; ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
