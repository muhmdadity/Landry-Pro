<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_dashboard.php?page=transaksi_baru');
    exit;
}

verify_csrf();

$nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
$no_hp          = preg_replace('/[^0-9+]/', '', trim($_POST['no_hp'] ?? ''));
$alamat_antar   = trim($_POST['alamat_antar'] ?? '');
$layanan_id     = (int)($_POST['layanan_id'] ?? 0);
$berat_qty      = (float)($_POST['berat_qty'] ?? 0);
$diskon         = max(0, (float)($_POST['diskon'] ?? 0));
$pakai_antar    = isset($_POST['pakai_antar']) ? 1 : 0;
$metode_bayar   = $_POST['metode_bayar'] ?? 'Tunai';
$status_bayar   = $_POST['status_bayar'] ?? 'Lunas';
$catatan        = trim($_POST['catatan'] ?? '');

if ($nama_pelanggan === '' || $no_hp === '' || $layanan_id <= 0 || $berat_qty <= 0) {
    set_flash('danger', 'Mohon lengkapi data pelanggan, jenis layanan, dan berat/jumlah dengan benar.');
    header('Location: ../admin_dashboard.php?page=transaksi_baru');
    exit;
}

// Ambil detail layanan
$stmtLayanan = $pdo->prepare("SELECT * FROM layanan WHERE id = ? AND aktif = 1");
$stmtLayanan->execute([$layanan_id]);
$layanan = $stmtLayanan->fetch();

if (!$layanan) {
    set_flash('danger', 'Layanan yang dipilih tidak valid.');
    header('Location: ../admin_dashboard.php?page=transaksi_baru');
    exit;
}

$toko         = get_toko($pdo);
$harga_satuan = (float)$layanan['harga'];
$subtotal     = $berat_qty * $harga_satuan;
$biaya_antar  = $pakai_antar ? (float)$toko['biaya_antar_default'] : 0;
$total_harga  = max(0, ($subtotal - $diskon) + $biaya_antar);
$status_antar = $pakai_antar ? 'Menunggu Diantar' : 'Ambil Sendiri';
$estimasi_jam = (int)$layanan['estimasi_jam'];

try {
    $pdo->beginTransaction();

    // 1. Cek atau Buat Akun Pelanggan Otomatis
    $stmtUser = $pdo->prepare("SELECT id FROM users WHERE no_hp = ? LIMIT 1");
    $stmtUser->execute([$no_hp]);
    $existingUser = $stmtUser->fetch();

    $info_akun_baru = '';
    if ($existingUser) {
        $pelanggan_id = $existingUser['id'];
        if ($alamat_antar !== '') {
            $pdo->prepare("UPDATE users SET alamat = ? WHERE id = ?")->execute([$alamat_antar, $pelanggan_id]);
        }
    } else {
        $empat_digit      = substr($no_hp, -4);
        $default_password = 'laundry_' . $empat_digit;
        $password_hash    = password_hash($default_password, PASSWORD_DEFAULT);
        $username_auto    = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama_pelanggan)) . '_' . $empat_digit;

        $stmtInsertUser = $pdo->prepare("INSERT INTO users (nama_lengkap, username, no_hp, alamat, password, role) VALUES (?, ?, ?, ?, ?, 'pelanggan')");
        $stmtInsertUser->execute([$nama_pelanggan, $username_auto, $no_hp, $alamat_antar, $password_hash]);
        $pelanggan_id = $pdo->lastInsertId();
        $info_akun_baru = " Akun pelanggan dibuat otomatis (Login No HP: {$no_hp} | Password: {$default_password}).";
    }

    // 2. Generate Kode Invoice Unik
    $kode_invoice = 'INV-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

    // 3. Simpan Transaksi
    $sql = "INSERT INTO transaksi (
                kode_invoice, pelanggan_id, nama_pelanggan, no_hp, alamat_antar,
                layanan_id, jenis_layanan, harga_satuan, berat_qty, satuan,
                subtotal, diskon, biaya_antar, total_harga,
                metode_bayar, status_bayar, pakai_antar, status_cucian, status_antar,
                catatan, estimasi_selesai
            ) VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, 'Baru Masuk', ?,
                ?, DATE_ADD(NOW(), INTERVAL ? HOUR)
            )";
    $stmtTrx = $pdo->prepare($sql);
    $stmtTrx->execute([
        $kode_invoice, $pelanggan_id, $nama_pelanggan, $no_hp, $alamat_antar,
        $layanan['id'], $layanan['nama_layanan'], $harga_satuan, $berat_qty, $layanan['satuan'],
        $subtotal, $diskon, $biaya_antar, $total_harga,
        $metode_bayar, $status_bayar, $pakai_antar, $status_antar,
        $catatan, $estimasi_jam
    ]);
    $newId = $pdo->lastInsertId();

    $pdo->commit();
    set_flash('success', "Transaksi <strong>{$kode_invoice}</strong> berhasil disimpan!{$info_akun_baru}");
    header("Location: ../struk.php?id={$newId}");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    set_flash('danger', 'Terjadi kesalahan saat menyimpan transaksi: ' . $e->getMessage());
    header('Location: ../admin_dashboard.php?page=transaksi_baru');
    exit;
}
