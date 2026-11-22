<?php
$riwayat = $pdo->query("SELECT * FROM transaksi ORDER BY id DESC")->fetchAll();
?>

<form action="actions/transaksi_hapus_massal.php" method="POST">
    <?= csrf_field(); ?>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Riwayat Seluruh Transaksi</h4>
            <p class="text-muted small mb-0">Arsip lengkap nota order, metode pembayaran, dan bukti cetak.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="input-group input-group-sm" style="width: 240px;">
                <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                <input type="text" class="form-control" data-table-search="tblRiwayat" placeholder="Cari invoice / nama...">
            </div>
            <button type="submit" class="btn btn-danger btn-sm fw-semibold px-3" onclick="return confirm('Hapus semua transaksi yang dicentang?')">
                <i class="fa-solid fa-trash-can me-1"></i> Hapus Terpilih
            </button>
        </div>
    </div>

    <div class="card-pro overflow-hidden">
        <div class="table-responsive">
            <table class="table table-pro align-middle mb-0" id="tblRiwayat">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="form-check-input" data-select-all=".cb-trx"></th>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Layanan & Qty</th>
                        <th>Total & Metode</th>
                        <th>Status Cucian</th>
                        <th>Status Antar</th>
                        <th class="text-center">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayat)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">Belum ada riwayat transaksi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($riwayat as $r): ?>
                            <tr>
                                <td><input type="checkbox" class="form-check-input cb-trx" name="id_transaksi[]" value="<?= $r['id']; ?>"></td>
                                <td>
                                    <span class="fw-bold text-primary d-block"><?= e($r['kode_invoice']); ?></span>
                                    <small class="text-muted"><?= date('d M Y, H:i', strtotime($r['tanggal_transaksi'])); ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold"><?= e($r['nama_pelanggan']); ?></div>
                                    <small class="text-muted"><?= e($r['no_hp']); ?></small>
                                </td>
                                <td>
                                    <div><?= e($r['jenis_layanan']); ?></div>
                                    <small class="text-muted"><?= (float)$r['berat_qty']; ?> <?= e($r['satuan']); ?></small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= rupiah($r['total_harga']); ?></div>
                                    <small class="text-muted"><?= e($r['metode_bayar']); ?> • <span class="<?= $r['status_bayar'] === 'Lunas' ? 'text-success' : 'text-danger'; ?> fw-semibold"><?= e($r['status_bayar']); ?></span></small>
                                </td>
                                <td><span class="badge rounded-pill <?= badge_status_cucian($r['status_cucian']); ?>"><?= e($r['status_cucian']); ?></span></td>
                                <td><span class="badge rounded-pill <?= badge_status_antar($r['status_antar']); ?>"><?= e($r['status_antar']); ?></span></td>
                                <td class="text-center">
                                    <a href="struk.php?id=<?= $r['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-receipt me-1"></i> Struk
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>
