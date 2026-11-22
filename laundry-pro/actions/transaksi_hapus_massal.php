<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $ids = $_POST['id_transaksi'] ?? [];
    if (!empty($ids) && is_array($ids)) {
        $cleanIds = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
        $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id IN ({$placeholders})");
        $stmt->execute($cleanIds);
        set_flash('success', count($cleanIds) . ' data transaksi berhasil dihapus.');
    } else {
        set_flash('warning', 'Tidak ada data transaksi yang dipilih.');
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../admin_dashboard.php?page=riwayat'));
exit;
