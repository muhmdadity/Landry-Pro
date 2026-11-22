<?php
$daftarLayanan = $pdo->query("SELECT * FROM layanan WHERE aktif = 1 ORDER BY id ASC")->fetchAll();
$daftarPelanggan = $pdo->query("SELECT nama_lengkap, no_hp, alamat FROM users WHERE role = 'pelanggan' ORDER BY nama_lengkap ASC")->fetchAll();
$biayaAntarDefault = (float)$toko['biaya_antar_default'];
?>

<div class="mb-4">
    <h4 class="fw-bold mb-1">Kasir / Transaksi Baru</h4>
    <p class="text-muted small mb-0">Input order laundry baru dengan kalkulasi otomatis & pembuatan akun pelanggan instan.</p>
</div>

<form action="actions/transaksi_simpan.php" method="POST" id="formPosLaundry">
    <?= csrf_field(); ?>
    <div class="row g-4">
        <!-- Kolom Kiri: Form Input -->
        <div class="col-lg-8">
            <div class="card-pro p-4 mb-4">
                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-check me-2"></i>1. Data Pelanggan</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Lengkap Pelanggan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_pelanggan" id="inputNama" list="listPelanggan" placeholder="Ketik atau pilih pelanggan lama..." required>
                        <datalist id="listPelanggan">
                            <?php foreach ($daftarPelanggan as $pl): ?>
                                <option value="<?= e($pl['nama_lengkap']); ?>" data-hp="<?= e($pl['no_hp']); ?>" data-alamat="<?= e($pl['alamat']); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">No HP / WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="no_hp" id="inputHp" placeholder="081234567890" required>
                        <small class="text-muted" style="font-size: 0.75rem;">Otomatis menjadi ID login Portal Pelanggan.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Alamat Pengantaran (Opsional)</label>
                        <input type="text" class="form-control" name="alamat_antar" id="inputAlamat" placeholder="Isi jika pelanggan meminta layanan antar-jemput...">
                    </div>
                </div>
            </div>

            <div class="card-pro p-4">
                <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-basket-shopping me-2"></i>2. Detail Layanan & Pembayaran</h6>
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label small fw-semibold">Pilih Paket Layanan <span class="text-danger">*</span></label>
                        <select class="form-select" name="layanan_id" id="selectLayanan" required>
                            <?php foreach ($daftarLayanan as $l): ?>
                                <option value="<?= $l['id']; ?>" data-harga="<?= $l['harga']; ?>" data-satuan="<?= e($l['satuan']); ?>" data-estimasi="<?= $l['estimasi_jam']; ?>">
                                    <?= e($l['nama_layanan']); ?> — <?= rupiah($l['harga']); ?> / <?= e($l['satuan']); ?> (Est. <?= $l['estimasi_jam']; ?> Jam)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label small fw-semibold">Berat / Jumlah (<span id="labelSatuan">Kg</span>) <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" min="0.1" class="form-control" name="berat_qty" id="inputBerat" placeholder="Contoh: 3.5" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Diskon / Potongan (Rp)</label>
                        <input type="number" min="0" step="500" class="form-control" name="diskon" id="inputDiskon" value="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Metode Pembayaran</label>
                        <select class="form-select" name="metode_bayar">
                            <option value="Tunai">Tunai (Cash)</option>
                            <option value="QRIS">QRIS / E-Wallet</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Status Pembayaran</label>
                        <select class="form-select" name="status_bayar">
                            <option value="Lunas">Lunas Sekarang</option>
                            <option value="Belum Lunas">Bayar Nanti (COD/Ambil)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch bg-light p-3 rounded-3 border d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-check-label fw-bold text-dark ms-4" for="checkAntar">
                                    Layanan Kurir Antar Pakaian
                                </label>
                                <div class="small text-muted ms-4">Tambahan ongkos kirim flat sebesar <?= rupiah($biayaAntarDefault); ?></div>
                            </div>
                            <input class="form-check-input fs-5 me-2" type="checkbox" name="pakai_antar" id="checkAntar" value="1">
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Catatan Khusus Pakaian (Opsional)</label>
                        <textarea class="form-control" name="catatan" rows="2" placeholder="Contoh: Pisahkan baju putih, jangan pakai pemutih..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Live Ringkasan Tagihan -->
        <div class="col-lg-4">
            <div class="card-pro p-4 sticky-top" style="top: 90px;">
                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="fa-solid fa-calculator text-primary me-2"></i>Ringkasan Tagihan</h6>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Harga Satuan</span>
                    <span class="fw-semibold" id="summaryHargaSatuan">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Subtotal Cucian</span>
                    <span class="fw-semibold" id="summarySubtotal">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between small mb-2">
                    <span class="text-muted">Biaya Antar</span>
                    <span class="fw-semibold" id="summaryAntar">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between small mb-3 text-danger">
                    <span>Diskon</span>
                    <span class="fw-semibold" id="summaryDiskon">- Rp 0</span>
                </div>
                <div class="border-top pt-3 mb-4 d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Total Bayar</span>
                    <h3 class="fw-bold text-primary mb-0" id="summaryTotal">Rp 0</h3>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                    <i class="fa-solid fa-print me-2"></i>Simpan & Cetak Struk
                </button>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const biayaAntarDefault = <?= $biayaAntarDefault; ?>;
    const selectLayanan = document.getElementById('selectLayanan');
    const inputBerat = document.getElementById('inputBerat');
    const inputDiskon = document.getElementById('inputDiskon');
    const checkAntar = document.getElementById('checkAntar');
    const inputNama = document.getElementById('inputNama');
    const inputHp = document.getElementById('inputHp');
    const inputAlamat = document.getElementById('inputAlamat');

    // Auto-fill HP & Alamat saat memilih pelanggan lama dari datalist
    inputNama.addEventListener('input', () => {
        const val = inputNama.value;
        document.querySelectorAll('#listPelanggan option').forEach(opt => {
            if (opt.value === val) {
                inputHp.value = opt.dataset.hp || '';
                inputAlamat.value = opt.dataset.alamat || '';
            }
        });
    });

    function hitungTotal() {
        const opt = selectLayanan.options[selectLayanan.selectedIndex];
        const harga = parseFloat(opt?.dataset.harga || 0);
        const satuan = opt?.dataset.satuan || 'Kg';
        const berat = parseFloat(inputBerat.value || 0);
        const diskon = parseFloat(inputDiskon.value || 0);
        const ongkir = checkAntar.checked ? biayaAntarDefault : 0;

        document.getElementById('labelSatuan').innerText = satuan;
        const subtotal = harga * berat;
        const total = Math.max(0, (subtotal - diskon) + ongkir);

        const fmt = n => 'Rp ' + Math.round(n).toLocaleString('id-ID');
        document.getElementById('summaryHargaSatuan').innerText = fmt(harga) + ' / ' + satuan;
        document.getElementById('summarySubtotal').innerText = fmt(subtotal);
        document.getElementById('summaryAntar').innerText = fmt(ongkir);
        document.getElementById('summaryDiskon').innerText = '- ' + fmt(diskon);
        document.getElementById('summaryTotal').innerText = fmt(total);
    }

    [selectLayanan, inputBerat, inputDiskon, checkAntar].forEach(el => {
        el.addEventListener('input', hitungTotal);
        el.addEventListener('change', hitungTotal);
    });
    hitungTotal();
});
</script>
