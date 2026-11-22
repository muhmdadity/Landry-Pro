<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Escape output HTML untuk mencegah XSS
 */
function e(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format mata uang Rupiah
 */
function rupiah($angka): string {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/**
 * Generate & Validasi CSRF Token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(): void {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        set_flash('danger', 'Sesi formulir tidak valid (CSRF Token mismatch). Silakan coba lagi.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '../index.php'));
        exit;
    }
}

/**
 * Flash Notification Message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Middleware Autentikasi & Otorisasi
 */
function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void {
    require_login();
    if (!in_array($_SESSION['role'] ?? '', ['admin', 'kasir'], true)) {
        header('Location: pelanggan_dashboard.php');
        exit;
    }
}

function require_pelanggan(): void {
    require_login();
    if (($_SESSION['role'] ?? '') !== 'pelanggan') {
        header('Location: admin_dashboard.php');
        exit;
    }
}

/**
 * Ambil pengaturan toko dari database
 */
function get_toko(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM pengaturan_toko WHERE id = 1");
    return $stmt->fetch() ?: [
        'nama_toko' => 'LaundryPro Express',
        'tagline' => 'Solusi Laundry Modern',
        'alamat_toko' => 'Jl. Merdeka No. 1',
        'no_wa_toko' => '081234567890',
        'biaya_antar_default' => 10000,
        'catatan_struk' => 'Terima kasih atas kepercayaan Anda.'
    ];
}

/**
 * Badge Warna Status Cucian
 */
function badge_status_cucian(string $status): string {
    return match ($status) {
        'Baru Masuk'     => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
        'Proses Cuci'    => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        'Proses Setrika' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        'Siap Diambil'   => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'Selesai'        => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        default          => 'bg-light text-dark'
    };
}

/**
 * Badge Warna Status Antar
 */
function badge_status_antar(string $status): string {
    return match ($status) {
        'Ambil Sendiri'    => 'bg-light text-secondary border',
        'Menunggu Diantar' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'Sedang Diantar'   => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        'Terkirim'         => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        default            => 'bg-light text-dark'
    };
}
