<?php
require_once '../config/database.php';
require_once '../config/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

verify_csrf();

$identity = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($identity === '' || $password === '') {
    set_flash('danger', 'Username/No HP dan password wajib diisi.');
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :id1 OR no_hp = :id2 LIMIT 1");
$stmt->execute(['id1' => $identity, 'id2' => $identity]);
$user = $stmt->fetch();

$login_valid = false;
if ($user) {
    // 1. Cek password hash modern
    if (password_verify($password, $user['password'])) {
        $login_valid = true;
    }
    // 2. Fallback kompatibilitas password awal (admin123 untuk admin, atau laundry_XXXX untuk pelanggan)
    elseif ($user['role'] === 'admin' && $password === 'admin123') {
        $login_valid = true;
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $user['id']]);
    } elseif ($user['role'] === 'pelanggan' && $password === ('laundry_' . substr($user['no_hp'], -4))) {
        $login_valid = true;
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $user['id']]);
    }
}

if ($login_valid) {
    session_regenerate_id(true);
    $_SESSION['user_id']      = $user['id'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['username']     = $user['username'];
    $_SESSION['no_hp']        = $user['no_hp'];
    $_SESSION['role']         = $user['role'];

    if (in_array($user['role'], ['admin', 'kasir'], true)) {
        header('Location: ../admin_dashboard.php?page=home');
    } else {
        header('Location: ../pelanggan_dashboard.php');
    }
    exit;
}

set_flash('danger', 'Username / No HP atau Password tidak sesuai.');
header('Location: ../login.php');
exit;
