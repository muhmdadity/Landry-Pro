<?php
// Statistik Utama
$totalTrx = $pdo->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
$totalOmzet = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) FROM transaksi WHERE status_bayar = 'Lunas'")->fetchColumn();
$totalPiutang = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) FROM transaksi WHERE status_bayar = 'Belum Lunas'")->fetchColumn();
$totalPelanggan = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'pelanggan'")->fetchColumn();
$sedangProses = $pdo->query("SELECT COUNT(*) FROM transaksi WHERE status_cucian IN ('Baru Masuk', 'Proses Cuci', 'Proses Setrika')")->fetchColumn();

// Grafik Pendapatan 7 Hari Terakhir
$stmtChart = $pdo->query("
    SELECT DATE(tanggal_transaksi) AS tgl, COALESCE(SUM(total_harga), 0) AS omzet
    FROM transaksi
    GROUP BY DATE(tanggal_transaksi)
    ORDER BY tgl DESC
    LIMIT 7
");
$chartRows = array_reverse($stmtChart->fetchAll());
$labels = [];
$values = [];
foreach ($chartRows as $r) {
    $labels[] = date('d M', strtotime($r['tgl']));
    $values[] = (float)$r['omzet'];
}

// 5 Transaksi Terbaru
$recentTrx = $pdo->query("SELECT * FROM transaksi ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Ringkasan Operasional</h4>
        <p class="text-muted small mb-0">Pantau performa harian, status pengerjaan, dan arus pendapatan toko Anda.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="admin_dashboard.php?page=transaksi_baru" class="btn btn-primary fw-semibold px-3 rounded-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Transaksi Baru
        </a>
        <a href="admin_dashboard.php?page=manajemen_status" class="btn btn-outline-secondary fw-semibold px-3 rounded-3 bg-white">
            <i class="fa-solid fa-sliders me-1"></i> Update Status
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card-pro stat-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Pendapatan Lunas</span>
                    <h4 class="fw-bold text-success mb-1"><?= rupiah($totalOmzet); ?></h4>
                    <small class="text-muted">Piutang: <span class="text-danger fw-semibold"><?= rupiah($totalPiutang); ?></span></small>
                </div>
                <div class="stat-icon-box bg-success-subtle text-success">
                    <i class="fa-solid fa-wallet"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card-pro stat-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Total Nota Order</span>
                    <h4 class="fw-bold mb-1"><?= number_format($totalTrx); ?> Nota</h4>
                    <small class="text-muted">Semua riwayat transaksi</small>
                </div>
                <div class="stat-icon-box bg-primary-subtle text-primary">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card-pro stat-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Cucian Aktif</span>
                    <h4 class="fw-bold text-warning-emphasis mb-1"><?= number_format($sedangProses); ?> Order</h4>
                    <small class="text-muted">Sedang dikerjakan</small>
                </div>
                <div class="stat-icon-box bg-warning-subtle text-warning-emphasis">
                    <i class="fa-solid fa-soap"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card-pro stat-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted small fw-semibold d-block mb-1">Pelanggan Terdaftar</span>
                    <h4 class="fw-bold mb-1"><?= number_format($totalPelanggan); ?> Orang</h4>
                    <small class="text-muted">Akses portal pelanggan aktif</small>
                </div>
                <div class="stat-icon-box bg-info-subtle text-info-emphasis">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart & Recent Orders -->
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-pro p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-line text-primary me-2"></i>Tren Pendapatan Harian</h6>
                <span class="badge bg-light text-secondary border">7 Hari Aktif Terakhir</span>
            </div>
            <div style="height: 280px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card-pro p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Order Terbaru</h6>
                <a href="admin_dashboard.php?page=riwayat" class="small text-decoration-none fw-semibold">Lihat Semua</a>
            </div>
            <?php if (empty($recentTrx)): ?>
                <p class="text-muted small text-center my-5">Belum ada transaksi tercatat.</p>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentTrx as $rt): ?>
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small text-dark"><?= e($rt['nama_pelanggan']); ?> <span class="text-muted fw-normal">(<?= e($rt['kode_invoice']); ?>)</span></div>
                                <small class="text-muted"><?= e($rt['jenis_layanan']); ?> • <?= (float)$rt['berat_qty']; ?> <?= e($rt['satuan']); ?></small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold small text-primary mb-1"><?= rupiah($rt['total_harga']); ?></div>
                                <span class="badge rounded-pill <?= badge_status_cucian($rt['status_cucian']); ?>" style="font-size: 0.7rem;">
                                    <?= e($rt['status_cucian']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels); ?>,
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: <?= json_encode($values); ?>,
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    borderRadius: 8,
                    maxBarThickness: 38
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: value => 'Rp ' + value.toLocaleString('id-ID')
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
