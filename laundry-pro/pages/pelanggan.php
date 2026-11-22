<?php
$pelangganList = $pdo->query("
    SELECT u.*, COUNT(t.id) AS jumlah_order, COALESCE(SUM(t.total_harga), 0) AS total_belanja
    FROM users u
    LEFT JOIN transaksi t ON t.pelanggan_id = u.id OR t.no_hp = u.no_hp
    WHERE u.role = 'pelanggan'
    GROUP BY u.id
    ORDER BY u.id DESC
")->fetchAll();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1">Database Pelanggan</h4>
        <p class="text-muted small mb-0">Kelola data pelanggan, riwayat belanja, dan reset akses portal pelanggan.</p>
    </div>
    <div class="d-flex gap-2">
        <div class="input-group input-group-sm" style="width: 240px;">
            <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
            <input type="text" class="form-control" data-table-search="tblPelanggan" placeholder="Cari nama / HP...">
        </div>
        <button type="submit" form="formHapusPelanggan" class="btn btn-danger btn-sm fw-semibold px-3" onclick="return confirm('Hapus semua akun pelanggan yang dicentang?')">
            <i class="fa-solid fa-trash-can me-1"></i> Hapus Terpilih
        </button>
    </div>
</div>

<form action="actions/pelanggan_aksi.php" method="POST" id="formHapusPelanggan">
    <?= csrf_field(); ?>
    <input type="hidden" name="aksi" value="hapus_massal">
    <div class="card-pro overflow-hidden">
        <div class="table-responsive">
            <table class="table table-pro align-middle mb-0" id="tblPelanggan">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="form-check-input" data-select-all=".cb-plg"></th>
                        <th>Nama Pelanggan</th>
                        <th>No HP / Login ID</th>
                        <th>Alamat</th>
                        <th>Total Order</th>
                        <th>Total Belanja</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pelangganList)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada pelanggan terdaftar.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pelangganList as $p): ?>
                            <tr>
                                <td><input type="checkbox" class="form-check-input cb-plg" name="id_pelanggan[]" value="<?= $p['id']; ?>"></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= e($p['nama_lengkap']); ?></div>
                                    <small class="text-muted">Terdaftar: <?= date('d M Y', strtotime($p['created_at'])); ?></small>
                                </td>
                                <td><code class="text-primary fw-semibold"><?= e($p['no_hp']); ?></code></td>
                                <td><small class="text-muted"><?= e($p['alamat'] ?: '-'); ?></small></td>
                                <td><span class="badge bg-primary-subtle text-primary"><?= $p['jumlah_order']; ?>x Order</span></td>
                                <td class="fw-bold text-success"><?= rupiah($p['total_belanja']); ?></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditPlg<?= $p['id']; ?>" title="Edit Pelanggan">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="resetPassPlg(<?= $p['id']; ?>, '<?= e($p['nama_lengkap']); ?>')" title="Reset Password Default">
                                        <i class="fa-solid fa-key"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</form>

<!-- Form tersembunyi untuk reset password -->
<form action="actions/pelanggan_aksi.php" method="POST" id="formResetPass">
    <?= csrf_field(); ?>
    <input type="hidden" name="aksi" value="reset_password">
    <input type="hidden" name="id" id="resetPassId">
</form>

<!-- Modals Edit Pelanggan -->
<?php foreach ($pelangganList as $p): ?>
<div class="modal fade" id="modalEditPlg<?= $p['id']; ?>" tabindex="-1">
    <div class="modal-dialog">
        <form action="actions/pelanggan_aksi.php" method="POST" class="modal-content">
            <?= csrf_field(); ?>
            <input type="hidden" name="aksi" value="update">
            <input type="hidden" name="id" value="<?= $p['id']; ?>">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Edit Data Pelanggan</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_lengkap" value="<?= e($p['nama_lengkap']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">No HP / WhatsApp</label>
                    <input type="text" class="form-control" name="no_hp" value="<?= e($p['no_hp']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alamat</label>
                    <textarea class="form-control" name="alamat" rows="2"><?= e($p['alamat']); ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary fw-semibold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<script>
function resetPassPlg(id, nama) {
    if (confirm('Reset password untuk ' + nama + ' ke format default (laundry_4digitHP)?')) {
        document.getElementById('resetPassId').value = id;
        document.getElementById('formResetPass').submit();
    }
}
</script>
