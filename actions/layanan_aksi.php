<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_dashboard.php?page=layanan');
    exit;
}

verify_csrf();
$aksi = $_POST['aksi'] ?? '';

if ($aksi === 'tambah') {
    $kode   = strtoupper(trim($_POST['kode_layanan'] ?? ''));
    $nama   = trim($_POST['nama_layanan'] ?? '');
    $satuan = $_POST['satuan'] ?? 'Kg';
    $harga  = (float)($_POST['harga'] ?? 0);
    $jam    = max(1, (int)($_POST['estimasi_jam'] ?? 48));
    $desk   = trim($_POST['deskripsi'] ?? '');

    if ($kode !== '' && $nama !== '' && $harga > 0) {
        $stmt = $pdo->prepare("INSERT INTO layanan (kode_layanan, nama_layanan, satuan, harga, estimasi_jam, deskripsi) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$kode, $nama, $satuan, $harga, $jam, $desk]);
        set_flash('success', "Layanan <strong>{$nama}</strong> berhasil ditambahkan.");
    }
} elseif ($aksi === 'edit') {
    $id     = (int)($_POST['id'] ?? 0);
    $nama   = trim($_POST['nama_layanan'] ?? '');
    $satuan = $_POST['satuan'] ?? 'Kg';
    $harga  = (float)($_POST['harga'] ?? 0);
    $jam    = max(1, (int)($_POST['estimasi_jam'] ?? 48));
    $desk   = trim($_POST['deskripsi'] ?? '');
    $aktif  = isset($_POST['aktif']) ? 1 : 0;

    if ($id > 0 && $nama !== '' && $harga > 0) {
        $stmt = $pdo->prepare("UPDATE layanan SET nama_layanan = ?, satuan = ?, harga = ?, estimasi_jam = ?, deskripsi = ?, aktif = ? WHERE id = ?");
        $stmt->execute([$nama, $satuan, $harga, $jam, $desk, $aktif, $id]);
        set_flash('success', "Layanan <strong>{$nama}</strong> berhasil diperbarui.");
    }
} elseif ($aksi === 'hapus') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $pdo->prepare("DELETE FROM layanan WHERE id = ?")->execute([$id]);
        set_flash('success', 'Layanan berhasil dihapus.');
    }
}

header('Location: ../admin_dashboard.php?page=layanan');
exit;
