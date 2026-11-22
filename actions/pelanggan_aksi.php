<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_dashboard.php?page=pelanggan');
    exit;
}

verify_csrf();
$aksi = $_POST['aksi'] ?? '';

if ($aksi === 'update') {
    $id           = (int)($_POST['id'] ?? 0);
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $no_hp        = preg_replace('/[^0-9+]/', '', trim($_POST['no_hp'] ?? ''));
    $alamat       = trim($_POST['alamat'] ?? '');

    if ($id > 0 && $nama_lengkap !== '' && $no_hp !== '') {
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, no_hp = ?, alamat = ? WHERE id = ? AND role = 'pelanggan'");
        $stmt->execute([$nama_lengkap, $no_hp, $alamat, $id]);
        set_flash('success', 'Data pelanggan berhasil diperbarui.');
    }
} elseif ($aksi === 'reset_password') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT no_hp FROM users WHERE id = ? AND role = 'pelanggan'");
    $stmt->execute([$id]);
    $u = $stmt->fetch();
    if ($u) {
        $defaultPass = 'laundry_' . substr($u['no_hp'], -4);
        $hash = password_hash($defaultPass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $id]);
        set_flash('success', "Password pelanggan berhasil direset menjadi: <strong>{$defaultPass}</strong>");
    }
} elseif ($aksi === 'hapus_massal') {
    $ids = $_POST['id_pelanggan'] ?? [];
    if (!empty($ids) && is_array($ids)) {
        $cleanIds = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
        $stmt = $pdo->prepare("DELETE FROM users WHERE role = 'pelanggan' AND id IN ({$placeholders})");
        $stmt->execute($cleanIds);
        set_flash('success', count($cleanIds) . ' akun pelanggan berhasil dihapus.');
    } else {
        set_flash('warning', 'Pilih minimal satu pelanggan untuk dihapus.');
    }
}

header('Location: ../admin_dashboard.php?page=pelanggan');
exit;
