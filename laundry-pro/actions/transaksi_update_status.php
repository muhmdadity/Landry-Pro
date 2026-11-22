<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        if (!empty($_POST['status_cucian'])) {
            $allowed_cucian = ['Baru Masuk', 'Proses Cuci', 'Proses Setrika', 'Siap Diambil', 'Selesai'];
            if (in_array($_POST['status_cucian'], $allowed_cucian, true)) {
                $pdo->prepare("UPDATE transaksi SET status_cucian = ? WHERE id = ?")->execute([$_POST['status_cucian'], $id]);
            }
        }
        if (!empty($_POST['status_antar'])) {
            $allowed_antar = ['Ambil Sendiri', 'Menunggu Diantar', 'Sedang Diantar', 'Terkirim'];
            if (in_array($_POST['status_antar'], $allowed_antar, true)) {
                $pdo->prepare("UPDATE transaksi SET status_antar = ? WHERE id = ?")->execute([$_POST['status_antar'], $id]);
            }
        }
        if (!empty($_POST['status_bayar'])) {
            $allowed_bayar = ['Belum Lunas', 'Lunas'];
            if (in_array($_POST['status_bayar'], $allowed_bayar, true)) {
                $pdo->prepare("UPDATE transaksi SET status_bayar = ? WHERE id = ?")->execute([$_POST['status_bayar'], $id]);
            }
        }
        set_flash('success', 'Status transaksi berhasil diperbarui.');
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../admin_dashboard.php?page=manajemen_status'));
exit;
