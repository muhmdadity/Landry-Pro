<?php
$stmtAdmin = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtAdmin->execute([$_SESSION['user_id']]);
$adminData = $stmtAdmin->fetch();
?>

<div class="mb-4">
    <h4 class="fw-bold mb-1">Pengaturan Sistem & Profil Toko</h4>
    <p class="text-muted small mb-0">Konfigurasi identitas nota struk, tarif kurir default, dan keamanan akun admin.</p>
</div>

<div class="row g-4">
    <!-- Pengaturan Toko -->
    <div class="col-lg-7">
        <div class="card-pro p-4">
            <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-store me-2"></i>Identitas Toko & Struk Kasir</h6>
            <form action="actions/pengaturan_simpan.php" method="POST">
                <?= csrf_field(); ?>
                <input type="hidden" name="tab" value="toko">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Usaha Laundry</label>
                    <input type="text" class="form-control" name="nama_toko" value="<?= e($toko['nama_toko']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Tagline / Slogan</label>
                    <input type="text" class="form-control" name="tagline" value="<?= e($toko['tagline']); ?>">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">No WhatsApp Toko</label>
                        <input type="text" class="form-control" name="no_wa_toko" value="<?= e($toko['no_wa_toko']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Tarif Antar Default (Rp)</label>
                        <input type="number" class="form-control" name="biaya_antar_default" value="<?= (float)$toko['biaya_antar_default']; ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Alamat Lengkap Toko</label>
                    <textarea class="form-control" name="alamat_toko" rows="2" required><?= e($toko['alamat_toko']); ?></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Catatan Kaki Struk (Footer)</label>
                    <input type="text" class="form-control" name="catatan_struk" value="<?= e($toko['catatan_struk']); ?>">
                </div>
                <button type="submit" class="btn btn-primary fw-semibold px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengaturan Toko
                </button>
            </form>
        </div>
    </div>

    <!-- Pengaturan Akun Admin -->
    <div class="col-lg-5">
        <div class="card-pro p-4">
            <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-shield me-2"></i>Profil & Keamanan Akun</h6>
            <form action="actions/pengaturan_simpan.php" method="POST">
                <?= csrf_field(); ?>
                <input type="hidden" name="tab" value="akun">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama_lengkap" value="<?= e($adminData['nama_lengkap']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Username Login</label>
                    <input type="text" class="form-control" name="username" value="<?= e($adminData['username']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">No HP</label>
                    <input type="text" class="form-control" name="no_hp" value="<?= e($adminData['no_hp']); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-semibold">Password Baru (Opsional)</label>
                    <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak diubah">
                </div>
                <button type="submit" class="btn btn-dark fw-semibold w-100">
                    <i class="fa-solid fa-lock me-1"></i> Perbarui Akun Admin
                </button>
            </form>
        </div>
    </div>
</div>
