<?php
require_once 'config/database.php';
require_once 'config/helpers.php';
require_pelanggan();

$toko = get_toko($pdo);
$no_hp = $_SESSION['no_hp'] ?? '';
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE pelanggan_id = ? OR no_hp = ? ORDER BY id DESC");
$stmt->execute([$user_id, $no_hp]);
$pesananList = $stmt->fetchAll();

$aktifCount = 0;
foreach ($pesananList as $p) {
    if ($p['status_cucian'] !== 'Selesai') {
        $aktifCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pelanggan — <?= e($toko['nama_toko']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body>

    <nav class="navbar navbar-dark bg-dark py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
                <span class="badge bg-primary p-2"><i class="fa-solid fa-shirt"></i></span>
                <span><?= e($toko['nama_toko']); ?></span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 small d-none d-sm-inline">Halo, <strong class="text-white"><?= e($_SESSION['nama_lengkap']); ?></strong></span>
                <a href="actions/auth_logout.php" class="btn btn-outline-light btn-sm px-3">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Banner Welcome -->
        <div class="card-pro p-4 mb-4 text-white" style="background: linear-gradient(135deg, #1d4ed8 0%, #0284c7 100%);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold mb-1">Selamat Datang, <?= e($_SESSION['nama_lengkap']); ?>!</h4>
                    <p class="mb-0 text-white-50 small">Pantau status pengerjaan laundry dan pengantaran pakaian Anda secara real-time.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-white text-primary px-3 py-2 fs-6">
                        <i class="fa-solid fa-spinner me-1"></i> <?= $aktifCount; ?> Cucian Aktif
                    </span>
                </div>
            </div>
        </div>

        <!-- Daftar Pesanan -->
        <div class="card-pro overflow-hidden">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-list-check text-primary me-2"></i>Riwayat & Status Cucian Anda</h6>
                <span class="badge bg-secondary"><?= count($pesananList); ?> Total Order</span>
            </div>
            <div class="table-responsive">
                <table class="table table-pro align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Kode Invoice</th>
                            <th>Layanan</th>
                            <th>Total Tagihan</th>
                            <th>Status Cucian</th>
                            <th>Pengantaran</th>
                            <th class="text-center">Nota Digital</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pesananList)): ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">Anda belum memiliki riwayat transaksi laundry.</td></tr>
                        <?php else: ?>
                            <?php foreach ($pesananList as $row): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary d-block"><?= e($row['kode_invoice']); ?></span>
                                        <small class="text-muted"><?= date('d M Y, H:i', strtotime($row['tanggal_transaksi'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold"><?= e($row['jenis_layanan']); ?></div>
                                        <small class="text-muted"><?= (float)$row['berat_qty']; ?> <?= e($row['satuan']); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= rupiah($row['total_harga']); ?></div>
                                        <small class="<?= $row['status_bayar'] === 'Lunas' ? 'text-success' : 'text-danger'; ?> fw-semibold"><?= e($row['status_bayar']); ?></small>
                                    </td>
                                    <td><span class="badge rounded-pill <?= badge_status_cucian($row['status_cucian']); ?>"><?= e($row['status_cucian']); ?></span></td>
                                    <td><span class="badge rounded-pill <?= badge_status_antar($row['status_antar']); ?>"><?= e($row['status_antar']); ?></span></td>
                                    <td class="text-center">
                                        <a href="struk.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-receipt me-1"></i> Lihat Struk
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
