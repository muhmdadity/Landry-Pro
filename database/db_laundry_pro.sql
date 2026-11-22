-- Database: db_laundry_pro
-- Upgraded & Normalized Schema for LaundryPro POS & Management System

CREATE DATABASE IF NOT EXISTS `db_laundry_pro` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_laundry_pro`;

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Tabel Users (Admin, Kasir, Pelanggan)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `no_hp` VARCHAR(20) NOT NULL UNIQUE,
    `alamat` TEXT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'kasir', 'pelanggan') NOT NULL DEFAULT 'pelanggan',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 2. Tabel Layanan Laundry (Dinamis, bisa diatur admin)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `layanan`;
CREATE TABLE `layanan` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kode_layanan` VARCHAR(20) NOT NULL UNIQUE,
    `nama_layanan` VARCHAR(100) NOT NULL,
    `satuan` ENUM('Kg', 'Pcs', 'Meter') NOT NULL DEFAULT 'Kg',
    `harga` DECIMAL(12,2) NOT NULL,
    `estimasi_jam` INT NOT NULL DEFAULT 48,
    `deskripsi` VARCHAR(255) NULL,
    `aktif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 3. Tabel Transaksi Order
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `kode_invoice` VARCHAR(30) NOT NULL UNIQUE,
    `pelanggan_id` INT NULL,
    `nama_pelanggan` VARCHAR(100) NOT NULL,
    `no_hp` VARCHAR(20) NOT NULL,
    `alamat_antar` TEXT NULL,
    `layanan_id` INT NULL,
    `jenis_layanan` VARCHAR(100) NOT NULL,
    `harga_satuan` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `berat_qty` DECIMAL(8,2) NOT NULL,
    `satuan` VARCHAR(20) NOT NULL DEFAULT 'Kg',
    `subtotal` DECIMAL(12,2) NOT NULL,
    `diskon` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `biaya_antar` DECIMAL(12,2) NOT NULL DEFAULT 0,
    `total_harga` DECIMAL(12,2) NOT NULL,
    `metode_bayar` ENUM('Tunai', 'QRIS', 'Transfer Bank') NOT NULL DEFAULT 'Tunai',
    `status_bayar` ENUM('Belum Lunas', 'Lunas') NOT NULL DEFAULT 'Lunas',
    `pakai_antar` TINYINT(1) NOT NULL DEFAULT 0,
    `status_cucian` ENUM('Baru Masuk', 'Proses Cuci', 'Proses Setrika', 'Siap Diambil', 'Selesai') NOT NULL DEFAULT 'Baru Masuk',
    `status_antar` ENUM('Ambil Sendiri', 'Menunggu Diantar', 'Sedang Diantar', 'Terkirim') NOT NULL DEFAULT 'Ambil Sendiri',
    `catatan` TEXT NULL,
    `estimasi_selesai` DATETIME NULL,
    `tanggal_transaksi` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`pelanggan_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_transaksi_layanan` FOREIGN KEY (`layanan_id`) REFERENCES `layanan`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- --------------------------------------------------------
-- 4. Tabel Pengaturan Toko (Identitas Struk & Operasional)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pengaturan_toko`;
CREATE TABLE `pengaturan_toko` (
    `id` INT PRIMARY KEY DEFAULT 1,
    `nama_toko` VARCHAR(100) NOT NULL DEFAULT 'LaundryPro Express',
    `tagline` VARCHAR(150) NOT NULL DEFAULT 'Bersih, Wangi, Rapi & Tepat Waktu',
    `alamat_toko` TEXT NOT NULL,
    `no_wa_toko` VARCHAR(25) NOT NULL,
    `biaya_antar_default` DECIMAL(12,2) NOT NULL DEFAULT 10000,
    `catatan_struk` VARCHAR(255) NOT NULL DEFAULT 'Terima kasih telah mempercayakan pakaian Anda kepada kami.'
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- SEED DATA AWAL
-- --------------------------------------------------------

-- Default Admin (Password: admin123) & Sample Pelanggan (Password: laundry_7890)
INSERT INTO `users` (`id`, `nama_lengkap`, `username`, `no_hp`, `alamat`, `password`, `role`) VALUES
(1, 'Administrator Utama', 'admin', '081200000001', 'Kantor Pusat LaundryPro', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'Budi Santoso', 'budi_santoso', '081234567890', 'Jl. Melati No. 12, Blok A', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pelanggan'),
(3, 'Siti Aminah', 'siti_aminah', '085611223344', 'Perum Griya Asri No. 8', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pelanggan');
-- Catatan: Hash di atas adalah password 'password' standar Laravel/PHP, namun sistem juga mendukung login awal dengan 'admin123' / 'laundry_XXXX' dan otomatis meng-upgrade hash-nya!

-- Layanan Default
INSERT INTO `layanan` (`kode_layanan`, `nama_layanan`, `satuan`, `harga`, `estimasi_jam`, `deskripsi`) VALUES
('LYN-01', 'Cuci Kering Reguler', 'Kg', 5000.00, 48, 'Cuci bersih + pengeringan mesin, lipat rapi'),
('LYN-02', 'Cuci Setrika Komplit', 'Kg', 7000.00, 48, 'Cuci bersih + parfum premium + setrika uap rapi'),
('LYN-03', 'Setrika Uap Saja', 'Kg', 4500.00, 24, 'Setrika uap rapi + pewangi pakaian'),
('LYN-04', 'Cuci Ekspres 6 Jam', 'Kg', 12000.00, 6, 'Layanan kilat selesai dalam 6 jam'),
('LYN-05', 'Cuci Bedcover / Selimut', 'Pcs', 25000.00, 72, 'Perawatan khusus bedcover ukuran besar');

-- Pengaturan Toko Default
INSERT INTO `pengaturan_toko` (`id`, `nama_toko`, `tagline`, `alamat_toko`, `no_wa_toko`, `biaya_antar_default`, `catatan_struk`) VALUES
(1, 'LaundryPro Express', 'Solusi Laundry Modern, Cepat & Higienis', 'Jl. Merdeka Raya No. 88, Tangerang', '081234567890', 10000.00, 'Barang yang tidak diambil lebih dari 30 hari di luar tanggung jawab kami.');

-- Sample Transaksi Awal
INSERT INTO `transaksi` (`kode_invoice`, `pelanggan_id`, `nama_pelanggan`, `no_hp`, `alamat_antar`, `layanan_id`, `jenis_layanan`, `harga_satuan`, `berat_qty`, `satuan`, `subtotal`, `diskon`, `biaya_antar`, `total_harga`, `metode_bayar`, `status_bayar`, `pakai_antar`, `status_cucian`, `status_antar`, `estimasi_selesai`, `tanggal_transaksi`) VALUES
('INV-20261120-001', 2, 'Budi Santoso', '081234567890', 'Jl. Melati No. 12, Blok A', 2, 'Cuci Setrika Komplit', 7000.00, 4.50, 'Kg', 31500.00, 0.00, 10000.00, 41500.00, 'QRIS', 'Lunas', 1, 'Proses Cuci', 'Menunggu Diantar', DATE_ADD(NOW(), INTERVAL 48 HOUR), NOW()),
('INV-20261119-002', 3, 'Siti Aminah', '085611223344', '', 1, 'Cuci Kering Reguler', 5000.00, 3.00, 'Kg', 15000.00, 0.00, 0.00, 15000.00, 'Tunai', 'Lunas', 0, 'Siap Diambil', 'Ambil Sendiri', DATE_ADD(NOW(), INTERVAL 12 HOUR), DATE_SUB(NOW(), INTERVAL 1 DAY));
