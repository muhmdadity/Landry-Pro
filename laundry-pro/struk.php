<?php
require_once 'config/database.php';
require_once 'config/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE id = ?");
$stmt->execute([$id]);
$trx = $stmt->fetch();

if (!$trx) {
    die("Data struk transaksi tidak ditemukan.");
}

$toko = get_toko($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk <?= e($trx['kode_invoice']); ?> — <?= e($toko['nama_toko']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #e2e8f0;
            font-family: 'Courier New', Courier, monospace;
            padding: 20px 10px;
        }
        .receipt-paper {
            max-width: 380px;
            margin: 0 auto;
            background: #fff;
            padding: 24px 20px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .dashed-line {
            border-top: 1px dashed #94a3b8;
            margin: 12px 0;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt-paper { box-shadow: none; max-width: 100%; width: 100%; padding: 10px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="receipt-paper">
        <div class="text-center">
            <h5 class="fw-bold mb-1"><?= strtoupper(e($toko['nama_toko'])); ?></h5>
            <div class="small"><?= e($toko['alamat_toko']); ?></div>
            <div class="small">WA: <?= e($toko['no_wa_toko']); ?></div>
        </div>

        <div class="dashed-line"></div>

        <div class="small">
            <div class="d-flex justify-content-between"><span>No Nota:</span><strong><?= e($trx['kode_invoice']); ?></strong></div>
            <div class="d-flex justify-content-between"><span>Tanggal:</span><span><?= date('d/m/Y H:i', strtotime($trx['tanggal_transaksi'])); ?></span></div>
            <div class="d-flex justify-content-between"><span>Pelanggan:</span><span><?= e($trx['nama_pelanggan']); ?></span></div>
            <div class="d-flex justify-content-between"><span>No HP:</span><span><?= e($trx['no_hp']); ?></span></div>
            <?php if ($trx['estimasi_selesai']): ?>
                <div class="d-flex justify-content-between"><span>Est. Selesai:</span><span><?= date('d/m/Y H:i', strtotime($trx['estimasi_selesai'])); ?></span></div>
            <?php endif; ?>
        </div>

        <div class="dashed-line"></div>

        <div class="small">
            <div class="fw-bold"><?= e($trx['jenis_layanan']); ?></div>
            <div class="d-flex justify-content-between">
                <span><?= (float)$trx['berat_qty']; ?> <?= e($trx['satuan']); ?> x <?= rupiah($trx['harga_satuan']); ?></span>
                <span><?= rupiah($trx['subtotal']); ?></span>
            </div>
            <?php if ((float)$trx['biaya_antar'] > 0): ?>
                <div class="d-flex justify-content-between">
                    <span>Biaya Kurir Antar</span>
                    <span><?= rupiah($trx['biaya_antar']); ?></span>
                </div>
            <?php endif; ?>
            <?php if ((float)$trx['diskon'] > 0): ?>
                <div class="d-flex justify-content-between">
                    <span>Diskon</span>
                    <span>-<?= rupiah($trx['diskon']); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="dashed-line"></div>

        <div class="d-flex justify-content-between fw-bold fs-6">
            <span>TOTAL</span>
            <span><?= rupiah($trx['total_harga']); ?></span>
        </div>
        <div class="d-flex justify-content-between small mt-1">
            <span>Metode / Status:</span>
            <span><?= e($trx['metode_bayar']); ?> (<?= strtoupper(e($trx['status_bayar'])); ?>)</span>
        </div>

        <?php if (!empty($trx['catatan'])): ?>
            <div class="dashed-line"></div>
            <div class="small"><strong>Catatan:</strong> <?= e($trx['catatan']); ?></div>
        <?php endif; ?>

        <div class="dashed-line"></div>

        <div class="text-center small mt-3">
            <p class="mb-2"><?= e($toko['catatan_struk']); ?></p>
            <div class="fw-bold">*** TERIMA KASIH ***</div>
        </div>

        <div class="mt-4 d-grid gap-2 no-print">
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">Cetak Struk</button>
            <a href="admin_dashboard.php?page=transaksi_baru" class="btn btn-outline-secondary btn-sm">Kembali ke Kasir</a>
        </div>
    </div>

</body>
</html>
