<?php
$layananList = $pdo->query("SELECT * FROM layanan ORDER BY id ASC")->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Master Paket & Tarif Layanan</h4>
        <p class="text-muted small mb-0">Atur jenis layanan kiloan atau satuan, tarif per unit, dan estimasi waktu pengerjaan.</p>
    </div>
    <button class="btn btn-primary fw-semibold px-3 rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambahLayanan">
        <i class="fa-solid fa-plus me-1"></i> Tambah Layanan Baru
    </button>
</div>

<div class="card-pro overflow-hidden">
    <div class="table-responsive">
        <table class="table table-pro align-middle mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Layanan</th>
                    <th>Tarif</th>
                    <th>Estimasi</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($layananList as $l): ?>
                    <tr>
                        <td><code class="fw-bold"><?= e($l['kode_layanan']); ?></code></td>
                        <td>
                            <div class="fw-bold text-dark"><?= e($l['nama_layanan']); ?></div>
                            <small class="text-muted"><?= e($l['deskripsi']); ?></small>
                        </td>
                        <td class="fw-bold text-primary"><?= rupiah($l['harga']); ?> <span class="text-muted fw-normal">/ <?= e($l['satuan']); ?></span></td>
                        <td><span class="badge bg-light text-dark border"><i class="fa-regular fa-clock me-1"></i><?= $l['estimasi_jam']; ?> Jam</span></td>
                        <td>
                            <?php if ($l['aktif']): ?>
                                <span class="badge bg-success-subtle text-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditLayanan<?= $l['id']; ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <form action="actions/layanan_aksi.php" method="POST" class="d-inline" onsubmit="return confirm('Hapus layanan ini?')">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="id" value="<?= $l['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Layanan -->
<div class="modal fade" id="modalTambahLayanan" tabindex="-1">
    <div class="modal-dialog">
        <form action="actions/layanan_aksi.php" method="POST" class="modal-content">
            <?= csrf_field(); ?>
            <input type="hidden" name="aksi" value="tambah">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Tambah Paket Layanan Baru</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Kode</label>
                        <input type="text" class="form-control" name="kode_layanan" placeholder="LYN-06" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label small fw-semibold">Nama Layanan</label>
                        <input type="text" class="form-control" name="nama_layanan" placeholder="Cuci Sepatu / Jas" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Satuan</label>
                        <select class="form-select" name="satuan">
                            <option value="Kg">Kg</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Meter">Meter</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Harga (Rp)</label>
                        <input type="number" class="form-control" name="harga" min="500" step="500" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Estimasi (Jam)</label>
                        <input type="number" class="form-control" name="estimasi_jam" value="48" min="1" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Deskripsi Singkat</label>
                        <input type="text" class="form-control" name="deskripsi" placeholder="Keterangan layanan...">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary fw-semibold">Simpan Layanan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modals Edit Layanan -->
<?php foreach ($layananList as $l): ?>
<div class="modal fade" id="modalEditLayanan<?= $l['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <form action="actions/layanan_aksi.php" method="POST" class="modal-content">
            <?= csrf_field(); ?>
            <input type="hidden" name="aksi" value="edit">
            <input type="hidden" name="id" value="<?= $l['id']; ?>">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Edit Layanan (<?= e($l['kode_layanan']); ?>)</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Nama Layanan</label>
                        <input type="text" class="form-control" name="nama_layanan" value="<?= e($l['nama_layanan']); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Satuan</label>
                        <select class="form-select" name="satuan">
                            <?php foreach (['Kg', 'Pcs', 'Meter'] as $st): ?>
                                <option value="<?= $st; ?>" <?= $l['satuan'] === $st ? 'selected' : ''; ?>><?= $st; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Harga (Rp)</label>
                        <input type="number" class="form-control" name="harga" value="<?= (float)$l['harga']; ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Estimasi (Jam)</label>
                        <input type="number" class="form-control" name="estimasi_jam" value="<?= (int)$l['estimasi_jam']; ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Deskripsi</label>
                        <input type="text" class="form-control" name="deskripsi" value="<?= e($l['deskripsi']); ?>">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="aktif" id="aktif<?= $l['id']; ?>" <?= $l['aktif'] ? 'checked' : ''; ?>>
                            <label class="form-check-label small fw-semibold" for="aktif<?= $l['id']; ?>">Layanan Aktif di Kasir</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary fw-semibold">Update Layanan</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>
