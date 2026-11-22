<?php
require_once '../config/database.php';
require_once '../config/helpers.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin_dashboard.php?page=pengaturan');
    exit;
}

verify_csrf();
$tab = $_POST['tab'] ?? 'toko';

if ($tab === 'toko') {
    $nama_toko   = trim($_POST['nama_toko'] ?? 'LaundryPro Express');
    $tagline     = trim($_POST['tagline'] ?? '');
    $alamat_toko = trim($_POST['alamat_toko'] ?? '');
    $no_wa_toko  = trim($_POST['no_wa_toko'] ?? '');
    $biaya_antar = max(0, (float)($_POST['biaya_antar_default'] ?? 10000));
    $catatan     = trim($_POST['catatan_struk'] ?? '');

    $stmt = $pdo->prepare("UPDATE pengaturan_toko SET nama_toko = ?, tagline = ?, alamat_toko = ?, no_wa_toko = ?, biaya_antar_default = ?, catatan_struk = ? WHERE id = 1");
    $stmt->execute([$nama_toko, $tagline, $alamat_toko, $no_wa_toko, $biaya_antar, $catatan]);
    set_flash('success', 'Identitas & pengaturan operasional toko berhasil diperbarui.');
} elseif ($tab === 'akun') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $username     = trim($_POST['username'] ?? '');
    $no_hp        = trim($_POST['no_hp'] ?? '');
    $password     = $_POST['password'] ?? '';
    $user_id      = $_SESSION['user_id'];

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, username = ?, no_hp = ?, password = ? WHERE id = ?");
        $stmt->execute([$nama_lengkap, $username, $no_hp, $hash, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nama_lengkap = ?, username = ?, no_hp = ? WHERE id = ?");
        $stmt->execute([$nama_lengkap, $username, $no_hp, $user_id]);
    }

    $_SESSION['nama_lengkap'] = $nama_lengkap;
    $_SESSION['username']     = $username;
    $_SESSION['no_hp']        = $no_hp;
    set_flash('success', 'Profil & keamanan akun berhasil diperbarui.');
}

header('Location: ../admin_dashboard.php?page=pengaturan');
exit;
