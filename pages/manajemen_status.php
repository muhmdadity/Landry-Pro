<?php
$orders = $pdo->query("SELECT * FROM transaksi ORDER BY FIELD(status_cucian, 'Baru Masuk', 'Proses Cuci', 'Proses Setrika', 'Siap Diambil', 'Selesai'), id DESC")->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Kontrol Produksi & Status Pengantaran</h4>
        <p class="text-muted small mb-0">Ubah progres cucian, status pembayaran, atau pengiriman secara instan.</p>
    </div>
    <div class="input-group" style="max-width: 280px;">
        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
        <input type="text" class="form-control border-start-0" data-table-search="tblStatus" placeholder="Cari invoice / pelanggan...">
    </div>
</div>

<div class="card-pro overflow-hidden">
    <div class="table-responsive">
        <table class="table table-pro align-middle mb-0" id="tblStatus">
            <thead>
                <tr>
                    <th>Invoice & Waktu</th>
                    <th>Pelanggan</th>
                    <th>Layanan</th>
                    <th>Pembayaran</th>
                    <th>Status Cucian</th>
                    <th>Status Pengantaran</th>
                    <th class="text-center">Struk / WA</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada data order.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $row): ?>
                        <?php
                            $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $row['no_hp']));
                            $pesanWa = urlencode("Halo {$row['nama_pelanggan']}, status cucian Anda ({$row['kode_invoice']}) saat ini: *{$row['status_cucian']}*. Total tagihan: " . rupiah($row['total_harga']) . " ({$row['status_bayar']}). Terima kasih - {$toko['nama_toko']}");
                        ?>
                        <tr>
                            <td>
                                <span class="fw-bold text-primary d-block"><?= e($row['kode_invoice']); ?></span>
                                <small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])); ?></small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= e($row['nama_pelanggan']); ?></div>
                                <small class="text-muted"><i class="fa-brands fa-whatsapp text-success me-1"></i><?= e($row['no_hp']); ?></small>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= e($row['jenis_layanan']); ?></div>
                                <small class="text-muted"><?= (float)$row['berat_qty']; ?> <?= e($row['satuan']); ?> • <?= rupiah($row['total_harga']); ?></small>
                            </td>
                            <td>
                                <form action="actions/transaksi_update_status.php" method="POST">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                    <select name="status_bayar" onchange="this.form.submit()" class="form-select form-select-sm fw-semibold <?= $row['status_bayar'] === 'Lunas' ? 'text-success border-success-subtle bg-success-subtle' : 'text-danger border-danger-subtle bg-danger-subtle'; ?>">
                                        <option value="Lunas" <?= $row['status_bayar'] === 'Lunas' ? 'selected' : ''; ?>>Lunas</option>
                                        <option value="Belum Lunas" <?= $row['status_bayar'] === 'Belum Lunas' ? 'selected' : ''; ?>>Belum Lunas</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form action="actions/transaksi_update_status.php" method="POST">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                    <select name="status_cucian" onchange="this.form.submit()" class="form-select form-select-sm fw-semibold <?= badge_status_cucian($row['status_cucian']); ?>">
                                        <?php foreach (['Baru Masuk', 'Proses Cuci', 'Proses Setrika', 'Siap Diambil', 'Selesai'] as $st): ?>
                                            <option value="<?= $st; ?>" <?= $row['status_cucian'] === $st ? 'selected' : ''; ?>><?= $st; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form action="actions/transaksi_update_status.php" method="POST">
                                    <?= csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                    <select name="status_antar" onchange="this.form.submit()" class="form-select form-select-sm fw-semibold <?= badge_status_antar($row['status_antar']); ?>">
                                        <?php foreach (['Ambil Sendiri', 'Menunggu Diantar', 'Sedang Diantar', 'Terkirim'] as $sa): ?>
                                            <option value="<?= $sa; ?>" <?= $row['status_antar'] === $sa ? 'selected' : ''; ?>><?= $sa; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="struk.php?id=<?= $row['id']; ?>" target="_blank" class="btn btn-outline-secondary" title="Cetak Struk">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <a href="https://wa.me/<?= $waNumber; ?>?text=<?= $pesanWa; ?>" target="_blank" class="btn btn-outline-success" title="Kirim Notifikasi WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
