<?php
$tgl_mulai   = $_GET['tgl_mulai'] ?? date('Y-m-01');
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-d');
$status_bayar_filter = $_GET['status_bayar'] ?? '';

$sql = "SELECT * FROM transaksi WHERE DATE(tanggal_transaksi) BETWEEN :mulai AND :selesai";
$params = ['mulai' => $tgl_mulai, 'selesai' => $tgl_selesai];

if ($status_bayar_filter !== '') {
    $sql .= " AND status_bayar = :sb";
    $params['sb'] = $status_bayar_filter;
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$laporanRows = $stmt->fetchAll();

$totalPendapatan = 0;
$totalOngkir = 0;
foreach ($laporanRows as $lr) {
    $totalPendapatan += (float)$lr['total_harga'];
    $totalOngkir += (float)$lr['biaya_antar'];
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Laporan Keuangan & Rekapitulasi</h4>
        <p class="text-muted small mb-0">Filter transaksi berdasarkan rentang tanggal untuk audit kas & cetak laporan.</p>
    </div>
    <button onclick="window.print()" class="btn btn-outline-dark fw-semibold px-3 no-print">
        <i class="fa-solid fa-print me-1"></i> Cetak Laporan (PDF / Printer)
    </button>
</div>

<!-- Filter Form -->
<div class="card-pro p-4 mb-4 no-print">
    <form method="GET" action="admin_dashboard.php" class="row g-3 align-items-end">
        <input type="hidden" name="page" value="laporan">
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Dari Tanggal</label>
            <input type="date" class="form-control" name="tgl_mulai" value="<?= e($tgl_mulai); ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Sampai Tanggal</label>
            <input type="date" class="form-control" name="tgl_selesai" value="<?= e($tgl_selesai); ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold">Status Pembayaran</label>
            <select class="form-select" name="status_bayar">
                <option value="">Semua Status</option>
                <option value="Lunas" <?= $status_bayar_filter === 'Lunas' ? 'selected' : ''; ?>>Lunas</option>
                <option value="Belum Lunas" <?= $status_bayar_filter === 'Belum Lunas' ? 'selected' : ''; ?>>Belum Lunas</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100 fw-semibold">
                <i class="fa-solid fa-filter me-1"></i> Terapkan Filter
            </button>
        </div>
    </form>
</div>

<!-- Ringkasan Periode -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-pro p-3 border-start border-primary border-4">
            <span class="text-muted small fw-semibold d-block">Total Omzet Periode</span>
            <h4 class="fw-bold text-primary mb-0"><?= rupiah($totalPendapatan); ?></h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-pro p-3 border-start border-success border-4">
            <span class="text-muted small fw-semibold d-block">Jumlah Transaksi</span>
            <h4 class="fw-bold text-success mb-0"><?= count($laporanRows); ?> Nota</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-pro p-3 border-start border-info border-4">
            <span class="text-muted small fw-semibold d-block">Total Pemasukan Ongkir</span>
            <h4 class="fw-bold text-info-emphasis mb-0"><?= rupiah($totalOngkir); ?></h4>
        </div>
    </div>
</div>

<!-- Tabel Rekap -->
<div class="card-pro overflow-hidden">
    <div class="table-responsive">
        <table class="table table-pro align-middle mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Invoice</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Metode</th>
                    <th>Status Bayar</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($laporanRows)): ?>
                    <tr><td colspan="8" class="text-center py-5 text-muted">Tidak ada data transaksi pada periode ini.</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($laporanRows as $row): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])); ?></td>
                            <td class="fw-bold"><?= e($row['kode_invoice']); ?></td>
                            <td><?= e($row['nama_pelanggan']); ?></td>
                            <td><?= e($row['jenis_layanan']); ?> (<?= (float)$row['berat_qty']; ?> <?= e($row['satuan']); ?>)</td>
                            <td><?= e($row['metode_bayar']); ?></td>
                            <td>
                                <span class="badge <?= $row['status_bayar'] === 'Lunas' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?>">
                                    <?= e($row['status_bayar']); ?>
                                </span>
                            </td>
                            <td class="text-end fw-bold"><?= rupiah($row['total_harga']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr class="table-light fw-bold">
                    <td colspan="7" class="text-end">TOTAL KESELURUHAN</td>
                    <td class="text-end text-primary fs-6"><?= rupiah($totalPendapatan); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
