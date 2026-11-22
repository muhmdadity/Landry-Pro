<?php
/**
 * Konfigurasi Koneksi Database PDO (Aman dari SQL Injection)
 * Mendukung port MySQL default (3306) maupun port kustom seperti 3307 (XAMPP/Laragon).
 */
$DB_HOST = '127.0.0.1';
$DB_PORT = '3306'; // Ubah ke '3307' jika MySQL Anda berjalan di port 3307
$DB_NAME = 'db_laundry_pro';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // Fallback otomatis mencoba port 3307 jika port 3306 gagal (kompatibilitas setup lama)
    try {
        $dsn_alt = "mysql:host={$DB_HOST};port=3307;dbname={$DB_NAME};charset=utf8mb4";
        $pdo = new PDO($dsn_alt, $DB_USER, $DB_PASS, $options);
    } catch (PDOException $e2) {
        die("
        <div style='font-family:sans-serif;max-width:600px;margin:50px auto;padding:24px;border:1px solid #fecaca;background:#fef2f2;border-radius:12px;color:#991b1b;'>
            <h3 style='margin-top:0;'>Koneksi Database Gagal</h3>
            <p>Pastikan MySQL sudah aktif dan database <strong>db_laundry_pro</strong> telah diimpor dari folder <code>database/db_laundry_pro.sql</code>.</p>
            <small>Detail teknis: " . htmlspecialchars($e2->getMessage()) . "</small>
        </div>");
    }
}
